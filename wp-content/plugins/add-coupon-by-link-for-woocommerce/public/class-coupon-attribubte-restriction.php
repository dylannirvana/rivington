<?php 

namespace PISOL\ACBLW\FRONT;

class CouponAttributeRestriction{
    static $instance = null;

    public static function get_instance(){
        if(self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct(){
        add_filter('woocommerce_coupon_get_excluded_product_ids', [$this, 'excluded_product_by_attribute'], 10, 2);
        add_filter('woocommerce_coupon_get_product_ids', [$this, 'included_product_by_attribute'], 10, 2);
    }

    function excluded_product_by_attribute($product_ids, $coupon) {

		$coupon_code = strtolower($coupon->get_code());

        $excluded_attr = self::get_excluded_attr($coupon);

		//check if $coupon_code has initial string TGREE in it
		if(!empty($excluded_attr) && is_array($excluded_attr)){

			$product_with_excluded_attr = self::get_product_with_attr($excluded_attr);

            if(!empty($product_with_excluded_attr) && is_array($product_with_excluded_attr) && is_array($product_ids)){
                $product_ids = array_merge($product_ids, $product_with_excluded_attr);
            }
		}	

        return $product_ids;
    }

    function included_product_by_attribute($product_ids, $coupon) {

		$coupon_code = strtolower($coupon->get_code());

        $included_attr = self::get_included_attr($coupon);

		//check if $coupon_code has initial string TGREE in it
		if(!empty($included_attr) && is_array($included_attr)){

			$product_with_included_attr = self::get_product_with_attr($included_attr);

            if(empty($product_with_included_attr)){
                $product_with_included_attr = [PHP_INT_MAX];
            }

            if(!empty($product_with_included_attr) && is_array($product_with_included_attr) && is_array($product_ids)){
                $product_ids = array_merge($product_ids, $product_with_included_attr);
            }
		}	

        return $product_ids;
    }

    static function get_included_attr($coupon){
        return get_post_meta($coupon->get_id(), '_included_attributes', true);
    }

    static function get_excluded_attr($coupon){
        return get_post_meta($coupon->get_id(), '_excluded_attributes', true);
    }

    static function get_product_with_attr($excluded_attr){
        $product_ids = [];
		if(isset(WC()->cart)){
			foreach( WC()->cart->get_cart() as $cart_item ) {
				$product = $cart_item['data'];

                foreach($excluded_attr as $tax_attr){
                    $parts = explode(':', $tax_attr);
                    if (count($parts) !== 2) {
                        continue;
                    }
                    $taxonomy = $parts[0];
                    $attr_id = $parts[1];

                    $selected_attribute_value = $product->get_attribute($taxonomy);

                    // Get the term by name
                    $term = get_term_by('name', $selected_attribute_value, $taxonomy);
                    // Check if the product has the 'pa_update' attribute
                    if ($term && $term->term_id == $attr_id) {
                        // Add the product ID to the array
                        $product_ids[] = $product->get_id();
                    }
                }
			}
		}
		return $product_ids;
    }
}

CouponAttributeRestriction::get_instance();