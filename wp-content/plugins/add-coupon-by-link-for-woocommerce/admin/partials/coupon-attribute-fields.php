<div class="pisol-container">
<div class="pisol-label"><?php esc_html_e('Included Product Attribute', 'add-coupon-by-link-woocommerce'); ?></div>
<select class="pisol-attribute pisol-mt" type="text" multiple="multiple" name="_included_attributes[]" data-nonce="<?php echo esc_attr( wp_create_nonce('security') ); ?>">
    <?php self::showOptions('_included_attributes', $coupon_id); ?>
</select>

<div class="pisol-label"><?php esc_html_e('Exclude Product Attribute', 'add-coupon-by-link-woocommerce'); ?></div>
<select class="pisol-attribute pisol-mt" type="text"  multiple="multiple" name="_excluded_attributes[]"  data-nonce="<?php echo esc_attr( wp_create_nonce('security') ); ?>">
    <?php self::showOptions('_excluded_attributes', $coupon_id); ?>
</select>
</div>