<?php

class pisol_acblw_product_options{

    public function __construct( ) {
		add_action( 'woocommerce_product_data_tabs', array($this,'tab') );
		/** Adding order preparation days */
		add_action( 'woocommerce_product_data_panels', array($this,'setting') );
        add_action( 'woocommerce_process_product_meta', array($this,'setting_save') );
    }

    function tab($tabs){
        $tabs['pisol_acblw_tab'] = array(
            'label'    => __('Coupon to apply', 'add-coupon-by-link-woocommerce'),
            'target'   => 'pisol_acblw',
            'priority' => 21,
            'class' => array('hide_if_grouped')
        );
        return $tabs;
    }
    
    function setting() {
		echo '<div id="pisol_acblw" class="panel woocommerce_options_panel hidden">';
		woocommerce_wp_text_input( array(
            'label' => __("Coupon code to apply",'add-coupon-by-link-woocommerce'), 
            'id' => 'pisol_acblw_associated_coupons', 
            'name' => 'pisol_acblw_associated_coupons', 
            'description' => __("Insert the coupon code to apply when this product is added to the cart. You can add multiple coupon codes by separating them with comma(,).",'add-coupon-by-link-woocommerce')
          ) );

   
		echo '</div>';
    }
    
    function setting_save( $post_id ) {
        $product = wc_get_product( $post_id );

        $value = isset($_POST['pisol_acblw_associated_coupons']) ? $_POST['pisol_acblw_associated_coupons'] : '';
        $product->update_meta_data( 'pisol_acblw_associated_coupons', sanitize_text_field( $value ) );

        $product->save();
   }
    
}
if(is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX )){
    new pisol_acblw_product_options();
}