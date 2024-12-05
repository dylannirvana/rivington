<?php 

namespace PISOL\ACBLW\FRONT;

class PaymentMethod{
    static $instance = null;

    public static function get_instance(){
        if(self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct(){
        add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'paymentMethods' ), 10, 2 );
        add_action( 'woocommerce_coupon_options_save', array( $this, 'save_meta' ), 10, 2 );
        add_filter( 'woocommerce_coupon_is_valid', array( $this, 'validate' ), 11, 3 );

    }

    function paymentMethods( $coupon_id = 0, $coupon = null){

			$payment_methods = ( is_object( $coupon ) && method_exists( $coupon, 'get_meta'  ) ) ? $coupon->get_meta( 'pi_acblw_payment_methods' ) : get_post_meta( $coupon_id, 'pi_acblw_payment_methods', true );

			if ( ! is_array( $payment_methods ) || empty( $payment_methods ) ) {
				$payment_methods = array();
			}

            $payment_gateways = WC()->payment_gateways->get_available_payment_gateways();

            $options = array();
            foreach ($payment_gateways as $gateway_id => $gateway) {
                $options[$gateway_id] = $gateway->get_title();
            }

            woocommerce_wp_select(
                array(
                    'id'                => 'pi_acblw_payment_methods',
                    'name'              => 'pi_acblw_payment_methods[]',
                    'label'             => __('Payment methods', 'add-coupon-by-link-woocommerce'),
                    'description'       => __('If you want to restrict the coupon to specific payment methods, select them from the list.', 'add-coupon-by-link-woocommerce'),
                    'desc_tip'          => true,
                    'value'             => $payment_methods,
                    'options'           => $options,
                    'custom_attributes' => array(
                        'multiple' => 'multiple',
                    ),
                    'class'             => 'wc-enhanced-select',
                )
            );

    }

    public function save_meta( $post_id = 0, $coupon = null ) {

        if ( empty( $post_id ) ) return;

        $payment_methods = isset($_POST['pi_acblw_payment_methods']) && is_array($_POST['pi_acblw_payment_methods']) ? array_map('sanitize_text_field', $_POST['pi_acblw_payment_methods']) : array();

        update_post_meta($post_id, 'pi_acblw_payment_methods', $payment_methods);
    }

    public function validate( $valid = false, $coupon = object, $discounts = null ) {
        // If coupon is invalid already, no need for further checks.
        if ( false === $valid ) {
            return $valid;
        }

        $coupon_id = method_exists($coupon, 'get_id') ? $coupon->get_id() : $coupon->id;

        $payment_methods = ( is_object( $coupon ) && method_exists( $coupon, 'get_meta'  ) ) ? $coupon->get_meta( 'pi_acblw_payment_methods' ) : get_post_meta( $coupon_id, 'pi_acblw_payment_methods', true );

        if(empty($payment_methods) || !is_array($payment_methods)){
            return $valid;
        }

        // Get selected payment method during checkout
        $chosen_payment_method = WC()->session->get('chosen_payment_method');

        // Check if coupon is applied and payment method is restricted
        if (!in_array($chosen_payment_method, $payment_methods)) {
            $valid = false;
            $coupon_code = ( is_object( $coupon ) && method_exists(  $coupon, 'get_code' )  ) ? $coupon->get_code() : '';
            WC()->cart->remove_coupon($coupon_code);
            wc_add_notice(__('This coupon is not valid for the selected payment method.', 'add-coupon-by-link-woocommerce'), 'error');
        }

        return $valid;
    }
}

PaymentMethod::get_instance();