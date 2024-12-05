<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class WC_Email_ACBLW_Store_Credit extends \WC_Email {

    public $coupon_code;

    public $amt;

    public $description;

    public function __construct() {
        $this->id          = 'store_credit';
        $this->title       = __('Store Credit', 'add-coupon-by-link-woocommerce');
        $this->description = __('This email is sent when a user receives store credit.', 'add-coupon-by-link-woocommerce');
        $this->customer_email = true;
        $this->placeholders   = array(
            '{amt}'   => '',
            '{coupon_code}' => '',
        );
        
        $this->template_base  = plugin_dir_path( __FILE__ ) . 'partials/';
        $this->template_html  = 'store-credit.php';
        $this->template_plain = 'store-credit-plain.php';

        // Call parent constructor to load any other defaults not explicity defined here
        parent::__construct();

        // Trigger email when store credit is assigned
        add_action( 'woocommerce_store_credit_assigned', array( $this, 'trigger' ), 10, 2 );
    }

    public function trigger( $coupon_code, $description = '' ) {
        $coupon = new \WC_Coupon( $coupon_code );

        $emails_array = $coupon->get_email_restrictions();

        $this->recipient = is_array( $emails_array ) ?  implode(  ',' ,$emails_array ) : $emails_array;

        $this->description = $description;

        if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
            return;
        }

        $this->placeholders['{coupon_code}'] = $coupon_code;
        $this->placeholders['{amt}'] = strip_tags(wc_price($coupon->get_amount()));

        $this->coupon_code = $coupon_code;

        $this->amt = $coupon->get_amount();

        if(!$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() )){
            throw new \Exception( esc_html__('Email could not be sent','add-coupon-by-link-woocommerce') );
        }
    }

    public function get_content_html() {
        $amt = strip_tags( wc_price($this->amt) );
        return wc_get_template_html( $this->template_html, array(
            'coupon_code' => $this->coupon_code,
            'email_heading' => $this->get_heading(),
            'amt' => $this->amt,
            'content' => $this->get_additional_content(),
            'desc' => $this->description,
        ), '', $this->template_base );
    }

    public function get_content_plain() {
        $amt = strip_tags( wc_price($this->amt) );
        return wc_get_template_html( $this->template_plain, array(
            'coupon_code' => $this->coupon_code,
            'email_heading' => $this->get_heading(),
            'amt' => $this->amt,
            'content' => $this->get_additional_content(),
            'desc' => $this->description,
        ), '', $this->template_base );
    }

    public function get_subject() {
        return apply_filters( 'woocommerce_email_subject_' . $this->id, $this->format_string($this->get_option( 'subject', $this->get_default_subject() )), $this->object );
    }

    public function get_default_subject() {
        return __( 'Store Credit Notification', 'add-coupon-by-link-woocommerce' );
    }

    public function get_heading() {
        return apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string($this->get_option( 'heading', $this->get_default_heading() )), $this->object );
    }

    public function get_default_heading() {
        return __( 'Store Credit of {amt}', 'add-coupon-by-link-woocommerce' );
    }

    public function get_additional_content() {
        return apply_filters( 'woocommerce_email_additional_content_' . $this->id, $this->format_string($this->get_option( 'additional_content', $this->get_default_additional_content() )), $this->object );
    }

    public function get_default_additional_content() {
        return __( 'Congratulations! You have received store credit of amount {amt}. Your coupon code is:', 'add-coupon-by-link-woocommerce' );
    }
}
