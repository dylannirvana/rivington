<?php
/*
Plugin Name: Woocommerce Square Custom Payment Order Description
Description: Modifies the order description sent to Square to include the booking resource names associated with the order. This plugin needs both Woocommerce Bookings and the Woocommerce Square plugin in order to work properly.
Version: 1.0.1
Author: Santiago Bazan
Author URI: https://codeable.io/developers/santiago-bazan
*/

$is_woo_square_plugin_active   = is_plugin_active('woocommerce-square/woocommerce-square.php');
$is_woo_bookings_plugin_active = is_plugin_active('woocommerce-bookings/woocommerce-bookings.php');

if ($is_woo_square_plugin_active && $is_woo_bookings_plugin_active) {
    add_filter('wc_square_payment_order_note', function($order_description, $order) {
        $booking_ids = WC_Booking_Data_Store::get_booking_ids_from_order_id($order->get_id());
        
        if (empty($booking_ids)) {
            return $order_description;
        }
        
        $resource_names = [];
        
        foreach ($booking_ids as $booking_id) {
            $booking       = new WC_Booking( $booking_id );
            $booking_order = $booking->get_order();
    
            if (!$booking_order) {
                continue;
            }
    
            $resource_names[] = $booking->get_resource()->get_name();
        }
    
        // return description name including resource names, if more than one resource
        // name, they will be comma separated.
        return sprintf('Order #%s, %s', $order->get_id(), implode(', ', $resource_names));
    }, 10, 2);
}
