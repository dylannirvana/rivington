<div class="pisol-container">
    <?php
woocommerce_wp_textarea_input( array(
            'label' => __("Message shown when coupon is added in user session (If left blank global message will be used)",'add-coupon-by-link-woocommerce'), 
            'id' => '_acblw_coupon_added_to_session', 
            'name' => '_acblw_coupon_added_to_session', 
            'desc_tip'    => true,
            'description' => __("If coupon is conditional coupon it will apply when condition satisfied, till that time coupon is saved in session and user is shown a message that coupon is saved in the session",'add-coupon-by-link-woocommerce'),
            'placeholder' => __("Coupon saved in your session, it will be applied once coupon condition satisfied",'add-coupon-by-link-woocommerce')
          ) );
woocommerce_wp_textarea_input( array(
            'label' => __("Message shown when coupon is added in user session (If left blank global message will be used)",'add-coupon-by-link-woocommerce'), 
            'id' => '_acblw_before_coupon_applied', 
            'name' => '_acblw_before_coupon_applied', 
            'desc_tip'    => true,
            'description' => __("If coupon is conditional coupon it will apply when condition satisfied, till that time coupon is saved in session and user is shown a message that coupon is saved in the session",'add-coupon-by-link-woocommerce'),
            'placeholder' => __("Coupon will be applied once its conditions are satisfied",'add-coupon-by-link-woocommerce')
          ) );
    ?>
</div>