<?php 

namespace PISOL\ACBLW\FRONT;

use Automattic\WooCommerce\Utilities\DiscountsUtil;

class EmailRestriction{
    static $instance = null;

    public static function get_instance(){
        if(self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct(){
        add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'emailRestriction' ), 10, 2 );
        add_action( 'woocommerce_coupon_options_save', array( $this, 'save_meta' ), 10, 2 );
        add_filter( 'woocommerce_coupon_is_valid', array( $this, 'validate' ), 11, 3 );

    }

    function emailRestriction( $coupon_id = 0, $coupon = null){

			$excluded_emails = ( is_object( $coupon ) && method_exists( $coupon, 'get_meta'  ) ) ? $coupon->get_meta( 'pi_acblw_excluded_customer_email' ) : get_post_meta( $coupon_id, 'pi_acblw_excluded_customer_email', true );

			if ( ! is_array( $excluded_emails ) || empty( $excluded_emails ) ) {
				$excluded_emails = array();
			}

			woocommerce_wp_text_input(
				array(
					'id'                => 'pi_acblw_excluded_customer_email',
					'label'             => __( 'Excluded emails', 'add-coupon-by-link-woocommerce' ),
					'placeholder'       => __( 'No restrictions', 'add-coupon-by-link-woocommerce' ),
					'description'       => __( 'Specify the email addresses you want to exclude from order placement checks. Separate multiple addresses with commas. You can also use an asterisk (*) as a wildcard to match parts of an email address. For instance, "*@gmail.com" will exclude all Gmail addresses. E.g: abc@yahoo.com, *@gmail.com', 'add-coupon-by-link-woocommerce' ),
					'value'             => implode( ', ', $excluded_emails ),
					'desc_tip'          => true,
					'type'              => 'email',
					'class'             => '',
					'custom_attributes' => array(
						'multiple' => 'multiple',
					),
				)
			);

    }

    public function save_meta( $post_id = 0, $coupon = null ) {

        if ( empty( $post_id ) ) return;

        $coupon = new \WC_Coupon( $coupon );

        $excluded_emails = ( isset( $_POST['pi_acblw_excluded_customer_email'] ) ) ? wc_clean( wp_unslash( $_POST['pi_acblw_excluded_customer_email'] ) ) : '';

        $excluded_emails = explode( ',', $excluded_emails );
        $excluded_emails = array_map( 'trim', $excluded_emails );
        $excluded_emails = array_filter( $excluded_emails, 'is_email' );
        $excluded_emails = array_filter( $excluded_emails );

        if ( method_exists( $coupon, 'update_meta_data' ) && method_exists( $coupon, 'save' ) ) {
            $coupon->update_meta_data( 'pi_acblw_excluded_customer_email', $excluded_emails );
            $coupon->save();
        } else {
            update_post_meta( $post_id, 'pi_acblw_excluded_customer_email', $excluded_emails );
        }
    }

    public function validate( $valid = false, $coupon = object, $discounts = null ) {
        // If coupon is invalid already, no need for further checks.
        if ( false === $valid ) {
            return $valid;
        }

        $coupon_id = method_exists($coupon, 'get_id') ? $coupon->get_id() : $coupon->id;

        $exclude_customer_email = get_post_meta( $coupon_id, 'pi_acblw_excluded_customer_email', true );

        if(empty($exclude_customer_email)){
            return $valid;
        }

        $current_user              = ( is_user_logged_in() ) ? wp_get_current_user() : null;
		$user_email                = ( ! is_null( $current_user ) && ! empty( $current_user->user_email ) ) ? $current_user->user_email : '';

        $billing_email = isset( $_REQUEST['billing_email'] ) ? $_REQUEST['billing_email'] : '';

        $posted_data = array();
        $post_data = isset( $_POST['post_data'] ) && ! empty( $_POST['post_data'] ) ?  ( $_POST['post_data'] )  : ''; // phpcs:ignore
		wp_parse_str( $post_data, $posted_data );

        if(empty($billing_email) && isset($posted_data['billing_email'])){
            $billing_email = $posted_data['billing_email'];
        }

        $wc_customer               = WC()->customer;
		$wc_customer_email         = method_exists( $wc_customer, 'get_email' ) ? $wc_customer->get_email() : '';
		$wc_customer_billing_email = method_exists( $wc_customer, 'get_billing_email' ) ? $wc_customer->get_billing_email() : '';
        $check_emails  = array_unique(
            array_filter(
                array_map(
                    'strtolower',
                    array_map(
                        'sanitize_email',
                        array(
                            $billing_email,
                            $user_email,
                            $wc_customer_email,
                            $wc_customer_billing_email,
                        )
                    )
                )
            )
        );

        $cart = ( function_exists( 'WC' ) && isset( WC()->cart ) ) ? WC()->cart : null;

        try {

            if (class_exists('Automattic\WooCommerce\Utilities\DiscountsUtil') && method_exists('Automattic\WooCommerce\Utilities\DiscountsUtil', 'is_coupon_emails_allowed')) {
                // Use the new method if it exists
                $is_allowed = DiscountsUtil::is_coupon_emails_allowed($check_emails, $exclude_customer_email);
            } else {
                // Fall back to the old method if the new one doesn't exist
                $is_allowed = WC()->cart->is_coupon_emails_allowed($check_emails, $exclude_customer_email);
            }
        
            if ($is_allowed) {
                $valid = false;
                $coupon_code = ( is_object( $coupon ) && method_exists(  $coupon, 'get_code' )  ) ? $coupon->get_code() : '';
                WC()->cart->remove_coupon($coupon_code);
                wc_add_notice(__('This coupon is not valid for you.', 'add-coupon-by-link-woocommerce'), 'error');
            }
        } catch (\Exception $e) {
            pisol_acblw_error_log($e->getMessage(), 'error');
        }

        return $valid;
    }
}

EmailRestriction::get_instance();