<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
Dylan redirects login page to WC Bookings Calendar
*/
add_action('admin_init', 'wp_admin_redirect');
function wp_admin_redirect() {
// checks if plugin is active
if(is_plugin_active('woocommerce-bookings/woocommerce-bookings.php')) {
global $pagenow;
// redirect to WCB Calendar
if($pagenow == 'index.php' && !$GET['page']) {
wp_redirect(admin_url("edit.php?post_type=wc_booking&page=booking_calendar", "https"), 301);
exit;			
}	
}
}
/* END */

/**
 * Enqueue child theme styles and scripts
 */
add_action('wp_enqueue_scripts', function() {
	wp_dequeue_style( 'theme-style' );

	
	$css_file_path = get_stylesheet_directory() . '/css/core.css';
	$css_file_url = get_stylesheet_directory_uri() . '/css/core.css';
	wp_enqueue_style( 'child-style', $css_file_url, array(), filemtime($css_file_path) );

 	$js_file_path = get_stylesheet_directory() . '/js/core.js';
 	$js_file_url = get_stylesheet_directory_uri() . '/js/core.js';
 	wp_enqueue_script( 'child-core', $js_file_url, array('jquery'), filemtime($js_file_path), true );

	if ( class_exists( 'WooCommerce' ) ) {
		$css_file_path = get_stylesheet_directory() . '/css/woo.css';
		$css_file_url = get_stylesheet_directory_uri() . '/css/woo.css';
		wp_enqueue_style( 'child-woo', $css_file_url, array(), filemtime($css_file_path) );
	}
}, 19);


/**
 * Include certain files
 */
include 'inc/admin-functions.php';
include 'inc/booking-functions.php';
include 'inc/booking-admin-functions.php';
include 'inc/coupon-functions.php';
include 'inc/facetwp-functions.php';
include 'inc/general-functions.php';
include 'inc/shortcodes.php';
include 'inc/woo-functions.php';


/**
 * A function to remove an action from a plugin class
 */
function remove_filters_with_method_and_class_name( $hook_name, $class_name, $method_name, $priority = 0 ) {
    global $wp_filter;
    // Take only filters on right hook name and priority
    if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
        return false;
    }
    // Loop on filters registered
    foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {
        // Test if filter is an array ! (always for class/method)
        if ( isset( $filter_array['function'] ) && is_array( $filter_array['function']) ) {
            // Test if object is a class and method is equal to param !
            if ( is_object( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] )
                && get_class( $filter_array['function'][0] ) == $class_name && $filter_array['function'][1] == $method_name ) {
                // Test for WordPress >= 4.7 WP_Hook class (https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/)
                if ( is_a( $wp_filter[ $hook_name ], 'WP_Hook' ) ) {
                    unset( $wp_filter[ $hook_name ]->callbacks[ $priority ][ $unique_id ] );
                } else {
                    unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
                }
            }
        }
    }
    return false;
}


/**
 * Redirect the employee handbook page if not an admin or shop_manager
 */
add_action('template_redirect', function() {
	if ( get_the_ID() == 1138 ) {
		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
			$roles = (array) $user->roles;

			if ( !in_array('administrator', $roles) && !in_array('shop_manager', $roles) ) {
				wp_redirect( site_url() );
        		exit();
			}
		} else {
			$link = add_query_arg(
				'redirect_to',
				urlencode(get_permalink(get_the_ID())),
				get_permalink( wc_get_page_id( 'myaccount' ) )
			);

			wp_redirect($link);
			exit();
		}
	}
});

