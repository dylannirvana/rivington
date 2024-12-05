<?php

class pisol_acblw_message{
    static function couponAddedToSession( $coupon_code ){

        $session_msg = self::couponCodeSessionMessage($coupon_code);

        if(empty($session_msg)){
            $session_msg = pisol_acblw_get_option('acblw_coupon_added_to_session', __('Coupon has ben added it will be applied once coupon conditions are satisfied','add-coupon-by-link-woocommerce'));
        }

        if(wc_has_notice($session_msg, 'acblw_success' )) return;

        wc_add_notice( $session_msg, 'acblw_success', array('code' => $coupon_code) );
    }

    static function beforeCouponApplied( $coupon_code ){

        $condition_msg = self::couponCodeConditionNotMetMessage($coupon_code);

        if(empty($condition_msg)){
            $condition_msg = pisol_acblw_get_option('acblw_before_coupon_applied', __('Coupon will be applied once coupon conditions are satisfied','add-coupon-by-link-woocommerce'));
        }

        if(wc_has_notice($condition_msg, 'acblw_notice' ) || self::presentCouponAddedToSession( $coupon_code )) return;

        wc_add_notice( $condition_msg, 'acblw_notice', array('code' => $coupon_code) );
    }

    static function presentCouponAddedToSession( $coupon_code ){
        $added_to_session = pisol_acblw_get_option('acblw_coupon_added_to_session', __('Coupon has ben added it will be applied once coupon conditions are satisfied','add-coupon-by-link-woocommerce'));
        if(wc_has_notice($added_to_session, 'acblw_success' )) return true;

        return false;
    }

    static function couponCodeSessionMessage($code){
        //get coupon id from code 
        $coupon_id = wc_get_coupon_id_by_code($code);
        if(!$coupon_id) return;

        $added_to_session = get_post_meta($coupon_id, '_acblw_coupon_added_to_session', true);

        return $added_to_session;
    }

    static function couponCodeConditionNotMetMessage($code){
        //get coupon id from code 
        $coupon_id = wc_get_coupon_id_by_code($code);
        if(!$coupon_id) return;

        $condition_msg = get_post_meta($coupon_id, '_acblw_before_coupon_applied', true);

        return $condition_msg;
    }
}