<?php

class pisol_acblw_category_options{

    public function __construct( ) {
		add_action('product_cat_add_form_fields', array($this, 'add_form'), 10, 1);
        add_action('product_cat_edit_form_fields', array($this, 'edit_form'), 10, 1);

        add_action('edited_product_cat', array($this, 'save'), 10, 1);
        add_action('saved_product_cat', array($this, 'save'), 10, 1);
    }

    function add_form(){
        ?>
            <div class="form-field">
                <label for="pisol_acblw_cat_associated_coupons"><?php esc_html_e('Coupon code to apply','add-coupon-by-link-woocommerce'); ?></label>
                <input type="text" name="pisol_acblw_cat_associated_coupons">
                <p><?php esc_html_e('Insert the coupon code to apply when this product is added to the cart. You can add multiple coupon codes by separating them with comma(,)', 'add-coupon-by-link-woocommerce'); ?></p>
            </div>
        <?php
    }

    function edit_form( $term ){
        $term_id = $term->term_id;

        // retrieve the existing value(s) for this meta field.
        $coupons = get_term_meta($term_id, 'pisol_acblw_cat_associated_coupons', true);
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="pisol_acblw_cat_associated_coupons"><?php esc_html_e('Coupon code to apply','add-coupon-by-link-woocommerce'); ?></label></th>
            <td>
            <input type="text" name="pisol_acblw_cat_associated_coupons" value="<?php echo esc_attr($coupons); ?>">
            <p><?php esc_html_e('Insert the coupon code to apply when this product is added to the cart. You can add multiple coupon codes by separating them with comma(,)', 'add-coupon-by-link-woocommerce'); ?></p>
            </td>
        </tr>
        <?php
    }

    function save($term_id){
        if(is_ajax() && isset($_POST['action']) && $_POST['action'] == 'inline-save-tax') return;
        
        $coupons = sanitize_text_field(filter_input(INPUT_POST, 'pisol_acblw_cat_associated_coupons'));

        update_term_meta($term_id, 'pisol_acblw_cat_associated_coupons', $coupons);
    }
    
}
new pisol_acblw_category_options();
