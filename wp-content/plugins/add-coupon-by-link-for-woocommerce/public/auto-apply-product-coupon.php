<?php 

class pisol_acblw_auto_apply_product_coupon{
    function __construct(){
        add_action('woocommerce_add_to_cart', [$this, 'add_coupon'], 10, 2);
    }

    function add_coupon($cart_item_key, $product_id){
        $coupon_code = $this->get_product_associated_coupons($product_id); 

        foreach($coupon_code as $coupon_code){
            //check coupon code exists

            $coupon_code_id = wc_get_coupon_id_by_code($coupon_code);

            if(empty($coupon_code_id)){
                pisol_acblw_error_log(sprintf('Product %s tried to auto apply a Coupon code %s which does not exist any more', $product_id, $coupon_code),  'error'); 
                continue;
            }

            if (!WC()->cart->has_discount($coupon_code)) {
                WC()->cart->apply_coupon($coupon_code);
            }
        }
    }

    function get_product_associated_coupons($product_id){
        $product_coupons = [];

        $coupons = get_post_meta($product_id, 'pisol_acblw_associated_coupons', true);

        if(!empty($coupons)){
            if(is_array($coupons)){
                $product_coupons = $coupons;
            }else{
                $product_coupons = explode(',', $coupons);
                $product_coupons = array_map('trim', $product_coupons);
            }
        }

        $product = wc_get_product($product_id);
        $product_categories = $product->get_category_ids();

        foreach($product_categories as $cat){
            $cat_coupons = get_term_meta($cat, 'pisol_acblw_cat_associated_coupons', true);
            if(!empty($cat_coupons)){
                if(is_array($cat_coupons)){
                    $product_coupons = array_merge($product_coupons, $cat_coupons);
                }else{
                    $cat_coupons = explode(',', $cat_coupons);
                    $cat_coupons = array_map('trim', $cat_coupons);
                    $product_coupons = array_merge($product_coupons, $cat_coupons);
                }
            }
        }


        return array_unique($product_coupons);
    }
}

new pisol_acblw_auto_apply_product_coupon();