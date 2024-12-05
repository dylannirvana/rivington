<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Disable all plugins except woocommerce and woocomerce-bookings for ajax requests on the book now form
 */
/*add_filter( 'option_active_plugins', function( $enabled_plugins ) {
    if ( wp_doing_ajax() ) {
        $request = $_REQUEST;

        if ( isset($request['wc-ajax']) ) {
            //error_log( $request['wc-ajax']);

            if ( $request['wc-ajax'] == 'wc_bookings_find_booking_slots' || $request['wc-ajax'] == 'wc_bookings_find_booked_day_blocks' ) {
                foreach ( $enabled_plugins as $key => $value ) {
                    //if ( strpos($value, 'woocommerce') === false ) {
                    if ( $value !== 'woocommerce/woocommerce.php' && $value !== 'woocommerce-bookings/woocommerce-bookings.php' ) {
                        unset($enabled_plugins[$key]);
                    }
                }

                if ( is_array( $enabled_plugins ) || is_object( $enabled_plugins ) ) {
                    //error_log( print_r( $enabled_plugins, true ) );
                } else {
                    //error_log( $enabled_plugins );
                }
            }
        }
    }

    return $enabled_plugins;
});*/