<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              piwebsolution.com
 * @since             1.1.62
 * @package           Add_Coupon_By_Link_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Add coupon by link / URL coupons for Woocommerce
 * Plugin URI:        https://www.piwebsolution.com
 * Description:       Adding coupons by url, so user can directly get coupon applied when they visit a link
 * Version:           1.1.62
 * Author:            PI Websolution
 * Author URI:        https://www.piwebsolution.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       add-coupon-by-link-woocommerce
 * Domain Path:       /languages
 * WC tested up to: 9.3.3
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if(!is_plugin_active( 'woocommerce/woocommerce.php')){
    function pi_acblw_woo_error_notice() {
        ?>
        <div class="error notice">
            <p><?php esc_html_e( 'Please install and activate WooCommerce plugin, without that this plugin cant work', 'add-coupon-by-link-woocommerce' ); ?></p>
        </div>
        <?php
    }
    add_action( 'admin_notices', 'pi_acblw_woo_error_notice' );
    return;
}

/**
 * Declare compatible with HPOS new order table 
 */
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

/**
 * Currently plugin version.
 * Start at version 1.1.62 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ADD_COUPON_BY_LINK_WOOCOMMERCE_VERSION', '1.1.62' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-add-coupon-by-link-woocommerce-activator.php
 */
function activate_add_coupon_by_link_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-add-coupon-by-link-woocommerce-activator.php';
	Add_Coupon_By_Link_Woocommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-add-coupon-by-link-woocommerce-deactivator.php
 */
function deactivate_add_coupon_by_link_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-add-coupon-by-link-woocommerce-deactivator.php';
	Add_Coupon_By_Link_Woocommerce_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_add_coupon_by_link_woocommerce' );
register_deactivation_hook( __FILE__, 'deactivate_add_coupon_by_link_woocommerce' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-add-coupon-by-link-woocommerce.php';

add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ),  'pisol_acblw_plugin_link' );

function pisol_acblw_plugin_link( $links ) {
    $links = array_merge( array(
        '<a href="' . esc_url( admin_url( '/admin.php?page=pi-acblw-coupon' ) ) . '" style="color:#f00; font-weight:bold;">' . __( 'Settings', 'cancel-order-request-woocommerce' ) . '</a>'
    ), $links );
    return $links;
}

if(!function_exists('pisol_acblw_error_log')){
    function pisol_acblw_error_log($message, $context = 'custom') {
        if (class_exists('WC_Logger')) {
            $logger = wc_get_logger();
            $logger->error(print_r($message, true), array('source' => 'Add coupon by link / URL coupons for Woocommerce', 'context' => $context));
        } else {
            error_log($message); // Fallback to the default PHP error log if WooCommerce logger is not available.
        }
    }
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.1.62
 */
function run_add_coupon_by_link_woocommerce() {

	$plugin = new Add_Coupon_By_Link_Woocommerce();
	$plugin->run();

}
run_add_coupon_by_link_woocommerce();
