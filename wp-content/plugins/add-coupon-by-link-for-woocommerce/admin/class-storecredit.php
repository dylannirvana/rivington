<?php 

namespace PISOL\ACBLW\ADMIN;

class StoreCredit {

    static $instance = null;

    public static function get_instance(){
        if(self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        // Add new coupon type in backend
        add_filter('woocommerce_coupon_discount_types', array($this, 'add_store_credit_coupon_type'));

        /**
         * will set the calculation method for this discount coupon to be like fixed cart discount, so we dont have to handle the calculation
         */
        add_filter('woocommerce_coupon_get_discount_type', [ __CLASS__,'couponDiscountType' ], 10, 2 );

        /**
         * we modify the amount based on the amount that was used in past orders
         */
        add_filter('woocommerce_coupon_get_amount', [ __CLASS__,'getAmount' ], 10, 2 );


        /**
         * we store the order id in the coupon meta so we can track the usage of the coupon and remaining balance
         */
        add_action( 'woocommerce_update_order', [$this, 'storeCreditCount'] );


        /**
         * remaining balance is shown in the meta box of the coupon in the backend
         */
        add_action('add_meta_boxes', array($this, 'add_store_credit_meta_box'));

        add_filter('woocommerce_cart_totals_coupon_html', array($this, 'display_remaining_balance_next_to_coupon'), 10, 3);

        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 10 );

        add_action( 'wp_ajax_woo_store_credit_refund', array( $this, 'woo_store_credit_refund' ) );

        add_filter( 'woocommerce_email_classes', array( $this,'register_store_credit_email') );

        add_action( 'wp_ajax_send_store_credit_email', array( $this, 'manually_send_store_credit_email' ) );
    }

    public function add_store_credit_coupon_type($types) {
        $types['pisol_acblw_store_credit'] = __('Store Credit', 'add-coupon-by-link-woocommerce');
        return $types;
    }

    static function couponDiscountType( $discount_type, $coupon ){
        if( !self::isCreditCoupon( $coupon->get_id() ) ) return $discount_type;

        if(function_exists('get_current_screen')){
            $current_screen = get_current_screen();
            if( !empty($current_screen) && is_object($current_screen) &&  in_array($current_screen->id, ['edit-shop_coupon', 'shop_coupon']) ){
                return $discount_type;
            }
        }

        return 'fixed_cart';
    }

    static function isCreditCoupon( $id ){
        $type = get_post_meta($id, 'discount_type', true);

        if($type == 'pisol_acblw_store_credit') return true;

        return false;
    }

    static function getAmount( $amt, $coupon ){
        if( !self::isCreditCoupon( $coupon->get_id() ) ) return $amt;

        if(function_exists('get_current_screen')){
            $current_screen = get_current_screen();
            if( !empty($current_screen) && is_object($current_screen) &&  in_array($current_screen->id, ['edit-shop_coupon', 'shop_coupon']) ){
                return $amt;
            }
        }

        $amount_that_can_be_used = 0;

        if(isset($_POST['action']) && $_POST['action'] == 'woocommerce_add_coupon_discount'){
            $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';
            $order = wc_get_order($order_id);

            if(!is_object($order)) return 0;

            $amount_that_can_be_used = self::getAmountThatCanBeUsed($coupon, $order);

            return $amount_that_can_be_used;
        }

        if(!function_exists('WC') || !is_object(WC()->cart)) return 0;

        $cart_subtotal = WC()->cart->subtotal;
        
        $amount_that_can_be_used = self::getAmountThatCanBeUsed($coupon);

        return $amount_that_can_be_used;
    }

    static function getAmountThatCanBeUsed($coupon, $order = null){
        $coupon_used_in_orders = self::getCouponUsedInOrders($coupon->get_id());

        $total_discount_given = 0;

        if(!empty($coupon_used_in_orders)){

            /**
             * this is needed else, if user has 100 and he has applied the coupon to cart and order is created but payment is not don't then 
             * the coupon will nto consider the amount that is already used in this order and will show the full amount as available
             */
            if(is_object($order)){
                $order_id = $order->get_id();
            }else{
                $order_id = null;
            }

            $total_discount_given = self::getTotalDiscountGiven($coupon_used_in_orders, $coupon->get_id(), $order_id);
        }

        $coupon_amount = get_post_meta($coupon->get_id(), 'coupon_amount', true);

        if(empty($coupon_amount)) return 0;

        if($total_discount_given >= $coupon_amount) return 0;

        $amount_that_can_be_used = $coupon_amount - $total_discount_given;

        return $amount_that_can_be_used;
    }

    static function getCouponUsedInOrders($coupon_id){
        $used_in_orders = get_post_meta($coupon_id, 'acblw_store_credit_user_in_orders', false);

        if(empty($used_in_orders) || !is_array($used_in_orders)) return [];

        return array_unique( $used_in_orders );
    }

    static function getTotalDiscountGiven( $orders_id, $coupon_id, $excluded_order_id = null){

        if(empty($orders_id)) return 0;

        if(!is_array($orders_id)) return 0;

        if(in_array($excluded_order_id, $orders_id)){
            $key = array_search($excluded_order_id, $orders_id);
            unset($orders_id[$key]);
        }

        $total_discount = 0;
        foreach( $orders_id as $order_id){
            $order = wc_get_order( $order_id );

            if(! $order ) continue;

            $status = $order->get_status();
            if(in_array($status, ['trash', 'refunded', 'cancelled', 'failed'])) continue;

            $coupons = $order->get_items( 'coupon' );

            if(empty( $coupons )) continue;

            foreach ( $coupons as $item_id => $item ) :
                $stored_coupon_data = $item->get_meta('coupon_data');
                
                if(!isset($stored_coupon_data['id'])){
                    $code = $item->get_code();
                    $applied_coupon_id = wc_get_coupon_id_by_code($code);

                    if(empty($applied_coupon_id)) continue;

                }else{
                    $applied_coupon_id = $stored_coupon_data['id'];
                }

                if($applied_coupon_id == $coupon_id){
                    $discount_given = $item->get_discount();
                    $total_discount += $discount_given;
                }
            endforeach;
        }
        return $total_discount;
    }

    function storeCreditCount( $order_id ){
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return;
        }

        $coupons = $order->get_coupon_codes();

        if ( count( $coupons ) > 0 ) {
            foreach ( $coupons as $code ) {

                if ( ! $code ) {
                    continue;
                }

                $coupon  = new \WC_Coupon( $code );

                if(!is_object($coupon)) continue;

                if(!self::isCreditCoupon($coupon->get_id())) continue;

                
                add_post_meta($coupon->get_id(), 'acblw_store_credit_user_in_orders', $order->get_id(), false);
                
            }
        }
    }

    public function add_store_credit_meta_box() {
        add_meta_box(
            'store_credit_meta_box',
            __('Store Credit Balance', 'add-coupon-by-link-woocommerce'),
            array($this, 'store_credit_meta_box_callback'),
            'shop_coupon',
            'side',
            'default'
        );
    }

    public function store_credit_meta_box_callback($post) {
        $coupon = new \WC_Coupon($post->ID);
        if ($coupon->get_discount_type() === 'pisol_acblw_store_credit') {
            $amount = self::getAmountThatCanBeUsed($coupon);
            echo '<p>' . esc_html__('Store credit remaining:', 'add-coupon-by-link-woocommerce') . ' ' . wp_kses_post( wc_price($amount) ) . '</p>';
            echo '<a class="button" id="send_store_credit_email" href="'.esc_url(wp_nonce_url(admin_url('admin-ajax.php?action=send_store_credit_email&coupon_id='.$post->ID), 'send_store_credit_email')).'">' . esc_html__('Send store credit email', 'add-coupon-by-link-woocommerce') . '</a>';
            // You can add additional logic to display remaining balance if needed
        } else {
            echo '<p>' . esc_html__('This is not a store credit coupon.', 'add-coupon-by-link-woocommerce') . '</p>';
        }
    }

    public function display_remaining_balance_next_to_coupon($coupon_html, $coupon, $discount_amount_html) {
        // Check if the coupon is a store credit coupon
        if (self::isCreditCoupon( $coupon->get_id() )) {

            if(isset($_POST['action']) && $_POST['action'] == 'woocommerce_add_coupon_discount'){
                $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';
                $order = wc_get_order($order_id);
    
                if(!is_object($order)) return 0;
    
                $amount_that_can_be_used = self::getAmountThatCanBeUsed($coupon, $order);
            }else{
                $amount_that_can_be_used = self::getAmountThatCanBeUsed($coupon);
            }            

            $discount_given = WC()->cart->get_coupon_discount_amount( $coupon->get_code(), WC()->cart->display_cart_ex_tax );

            $remaining_balance = $amount_that_can_be_used - $discount_given;
            
            if ($amount_that_can_be_used > 0) {
                $balance_message = sprintf(__('Store credit left after this: %s', 'add-coupon-by-link-woocommerce'), wc_price($remaining_balance));
                $coupon_html .= '<br><span class="store-credit-balance-message">' . $balance_message . '</span>';
            }
        }
        return $coupon_html;
    }

    function admin_scripts(){
        global $wp_query, $post, $theorder;
        $screen    = get_current_screen();
        $screen_id = $screen ? $screen->id : '';
        if ( in_array( $screen_id, array( 'shop_order', 'woocommerce_page_wc-orders' ), true ) ) {
                $order_id = 0;
				if ( $theorder instanceof \WC_Order ) {
					$order_id = $theorder->get_id();
				} elseif ( is_a( $post, 'WP_Post' ) && 'shop_order' === get_post_type( $post ) ) {
					$order_id = $post->ID;
				}
				$order = wc_get_order( $order_id );
				if ( $order ) {
                    wp_enqueue_script( 'acblw-refund-as-store-credit', plugin_dir_url( __FILE__ ) . 'js/admin-order.js', array( 'jquery', 'wc-admin-order-meta-boxes' ), '1.0.0', true );
					$order_localizer = array(
						'order_id'       => $order_id,
						'payment_method' => $order->get_payment_method( 'edit' ),
						'default_price'  => wc_price( 0 ),
						'is_refundable'  => true,
						'i18n'           => array(
							'refund'     => __( 'Refund', 'add-coupon-by-link-woocommerce' ),
							'via_wallet' => __( 'as store credit', 'add-coupon-by-link-woocommerce' ),
						),
					);
					wp_localize_script( 'acblw-refund-as-store-credit', 'acblw_wallet_admin_order_param', $order_localizer );
				}
                
        }
    }

    function woo_store_credit_refund(){
            ob_start();
			check_ajax_referer( 'order-item', 'security' );
			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				wp_die( -1 );
			}
			$order_id               = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
			$refund_amount          = isset( $_POST['refund_amount'] ) ? wc_format_decimal( sanitize_text_field( wp_unslash( $_POST['refund_amount'] ) ), wc_get_price_decimals() ) : 0;
			$refunded_amount        = isset( $_POST['refunded_amount'] ) ? wc_format_decimal( sanitize_text_field( wp_unslash( $_POST['refunded_amount'] ) ), wc_get_price_decimals() ) : 0;
			$refund_reason          = isset( $_POST['refund_reason'] ) ? sanitize_text_field( wp_unslash( $_POST['refund_reason'] ) ) : '';
			$line_item_qtys         = isset( $_POST['line_item_qtys'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['line_item_qtys'] ) ), true ) : array();
			$line_item_totals       = isset( $_POST['line_item_totals'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['line_item_totals'] ) ), true ) : array();
			$line_item_tax_totals   = isset( $_POST['line_item_tax_totals'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['line_item_tax_totals'] ) ), true ) : array();
			$api_refund             = isset( $_POST['api_refund'] ) && 'true' === $_POST['api_refund'];
			$restock_refunded_items = isset( $_POST['restock_refunded_items'] ) && 'true' === $_POST['restock_refunded_items'];
			$refund                 = false;
			$response               = array();
			try {
				$order      = wc_get_order( $order_id );
				$max_refund = wc_format_decimal( $order->get_total() - $order->get_total_refunded(), wc_get_price_decimals() );

				if ( ( ! $refund_amount && ( wc_format_decimal( 0, wc_get_price_decimals() ) !== $refund_amount ) ) || $max_refund < $refund_amount || 0 > $refund_amount ) {
					throw new \Exception( __( 'Invalid refund amount', 'woocommerce' ) );
				}

				if ( wc_format_decimal( $order->get_total_refunded(), wc_get_price_decimals() ) !== $refunded_amount ) {
					throw new \Exception( __( 'Error processing refund. Please try again.', 'woocommerce' ) );
				}

				// Prepare line items which we are refunding.
				$line_items = array();
				$item_ids   = array_unique( array_merge( array_keys( $line_item_qtys ), array_keys( $line_item_totals ) ) );

				foreach ( $item_ids as $item_id ) {
					$line_items[ $item_id ] = array(
						'qty'          => 0,
						'refund_total' => 0,
						'refund_tax'   => array(),
					);
				}
				foreach ( $line_item_qtys as $item_id => $qty ) {
					$line_items[ $item_id ]['qty'] = max( $qty, 0 );
				}
				foreach ( $line_item_totals as $item_id => $total ) {
					$line_items[ $item_id ]['refund_total'] = wc_format_decimal( $total );
				}
				foreach ( $line_item_tax_totals as $item_id => $tax_totals ) {
					$line_items[ $item_id ]['refund_tax'] = array_filter( array_map( 'wc_format_decimal', $tax_totals ) );
				}
				$refund_reason = $refund_reason ? $refund_reason : __( 'Store credit coupon given as refund #', 'add-coupon-by-link-woocommerce' ) . $order->get_order_number();
				// Create the refund object.
				$refund = wc_create_refund(
					array(
						'amount'         => $refund_amount,
						'reason'         => $refund_reason,
						'order_id'       => $order_id,
						'line_items'     => $line_items,
						'refund_payment' => $api_refund,
						'restock_items'  => $restock_refunded_items,
					)
				);
				if ( ! is_wp_error( $refund ) ) {
					$coupon_code = 'refund-' . $order->get_id().'-'.wp_generate_password(6, false);
                    $emails = array();
                    $emails[] = $order->get_billing_email();
                    $desc = sprintf(__('Store credit coupon given as a refund for order %s', 'add-coupon-by-link-woocommerce'), $order->get_id());
					self::issueCreditCoupon($refund_amount, $coupon_code, $emails, $desc);
                    $order->add_order_note(sprintf(__('Store credit coupon %s of %s amount was for email id %s as a refund', 'add-coupon-by-link-woocommerce'), $coupon_code, wc_price($refund_amount), implode(', ', $emails)));
                    $email_desc = sprintf(__('This coupon was given to you as a refund for the order #%s', 'add-coupon-by-link-woocommerce'), $order->get_id());
                    do_action( 'woocommerce_store_credit_assigned', $coupon_code, $email_desc );
				}

				if ( is_wp_error( $refund ) ) {
					throw new \Exception( $refund->get_error_message() );
				}

				if ( did_action( 'woocommerce_order_fully_refunded' ) ) {
					$response['status'] = 'fully_refunded';
				}
			} catch ( \Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
			// wp_send_json_success must be outside the try block not to break phpunit tests.
			wp_send_json_success( $response );
    }

    static function issueCreditCoupon($refund_amount, $coupon_code, $emails, $desc){
        $coupon = new \WC_Coupon();
        $coupon->set_code($coupon_code);
        $coupon->set_discount_type('pisol_acblw_store_credit');
        $coupon->set_amount($refund_amount);
        $coupon->set_description($desc);
        //get billing email and set as the email for the coupon
        $coupon->set_email_restrictions($emails);
        $coupon->save();
    }

    function register_store_credit_email( $email_classes ) {
        include_once 'class-storecredit-email.php';
        $email_classes['acblw_store_credit'] = new \WC_Email_ACBLW_Store_Credit();
        return $email_classes;
    }

    public function init_form_fields() {
        $placeholder_text  = sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' );
        $this->form_fields = array(
            'enabled'            => array(
                'title'   => __( 'Enable/Disable', 'woocommerce' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable this email notification', 'woocommerce' ),
                'default' => 'yes',
            ),
            'subject'            => array(
                'title'       => __( 'Subject', 'woocommerce' ),
                'type'        => 'text',
                'desc_tip'    => true,
                'description' => $placeholder_text,
                'placeholder' => $this->get_default_subject(),
                'default'     => '',
            ),
            'heading'            => array(
                'title'       => __( 'Email heading', 'woocommerce' ),
                'type'        => 'text',
                'desc_tip'    => true,
                'description' => $placeholder_text,
                'placeholder' => $this->get_default_heading(),
                'default'     => '',
            ),
            'additional_content' => array(
                'title'       => __( 'Additional content', 'woocommerce' ),
                'description' => __( 'Text to appear below the main email content.', 'woocommerce' ) . ' ' . $placeholder_text,
                'css'         => 'width:400px; height: 75px;',
                'placeholder' => __( 'N/A', 'woocommerce' ),
                'type'        => 'textarea',
                'default'     => $this->get_default_additional_content(),
                'desc_tip'    => true,
            ),
            'email_type'         => array(
                'title'       => __( 'Email type', 'woocommerce' ),
                'type'        => 'select',
                'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
                'default'     => 'html',
                'class'       => 'email_type wc-enhanced-select',
                'options'     => $this->get_email_type_options(),
                'desc_tip'    => true,
            ),
        );
    }

    function manually_send_store_credit_email(){
        check_ajax_referer('send_store_credit_email');

        if ( ! current_user_can( 'edit_shop_orders' ) ) {
            wp_send_json_error(  __( 'You do not have permission to send store credit email', 'add-coupon-by-link-woocommerce' )  );
        }

        $coupon_id = isset($_GET['coupon_id']) ? absint($_GET['coupon_id']) : 0;

        if(empty($coupon_id)){
            wp_send_json_error(  __( 'Invalid coupon id', 'add-coupon-by-link-woocommerce' )  );
        }

        $coupon = new \WC_Coupon($coupon_id);

        if(empty($coupon->get_id())){
            wp_send_json_error(  __( 'Invalid coupon', 'add-coupon-by-link-woocommerce' )  );
        }

        $emails = $coupon->get_email_restrictions();

        if(empty($emails)){
            wp_send_json_error(  __( 'No email found for this coupon', 'add-coupon-by-link-woocommerce' )  );
        }

        $type = get_post_meta($coupon_id, 'discount_type', true);

        if($type != 'pisol_acblw_store_credit'){
            wp_send_json_error(  __( 'This is not a store credit coupon', 'add-coupon-by-link-woocommerce' ) );
        }

        $coupon_code = $coupon->get_code();

        //this is needed to initialize woocommerce mailer inside admin-ajax.php
        WC()->mailer();

        try {
            do_action( 'woocommerce_store_credit_assigned', $coupon_code);
        } catch (\Exception $e) {
            wp_send_json_error(  $e->getMessage()  );
        }
        
        wp_send_json_success(  __( 'Store credit email sent successfully', 'add-coupon-by-link-woocommerce' )  );

    }
}

StoreCredit::get_instance();
