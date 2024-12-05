<?php 

namespace PISOL\ACBLW\ADMIN;

class CouponMessage{
    static $instance = null;

    public static function get_instance(){
        if(self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct(){
        add_filter( 'woocommerce_coupon_data_tabs', [$this,'add_coupon_message_tab']);

        add_action( 'woocommerce_coupon_data_panels', [$this,'coupon_product_attribute_fields'],10 ,2 );

        add_action('woocommerce_coupon_options_save', [$this, 'save_coupon_attributes']);
    }

    function add_coupon_message_tab($tabs) {
        $tabs['pisol_acblw_coupon_message'] = array(
            'label'    => __( 'Message', 'add-coupon-by-link-woocommerce' ),
            'target'   => 'message_tab',
            'class'    => array(),
            'priority' => 30,
        );
        return $tabs;
    }

    function coupon_product_attribute_fields($coupon_id, $coupon) {
        ?>
        <div id="message_tab" class="panel woocommerce_options_panel">
            <?php include_once plugin_dir_path( dirname( __FILE__ ) ).'admin/partials/message.php'; ?>
        </div>
        <?php
    }
    
    function save_coupon_attributes($post_id) {
        // Check if we have posted data for our custom field
        if (isset($_POST['_acblw_coupon_added_to_session'])) {
            // Sanitize and decode JSON data
            $session_msg = sanitize_text_field($_POST['_acblw_coupon_added_to_session']);
    
            // Update or add custom meta field to the coupon
            update_post_meta($post_id, '_acblw_coupon_added_to_session', $session_msg);
        }

        if (isset($_POST['_acblw_before_coupon_applied'])) {
            // Sanitize and decode JSON data
            $before_msg = sanitize_text_field($_POST['_acblw_before_coupon_applied']);
    
            // Update or add custom meta field to the coupon
            update_post_meta($post_id, '_acblw_before_coupon_applied', $before_msg);
        }
    }

}

CouponMessage::get_instance();