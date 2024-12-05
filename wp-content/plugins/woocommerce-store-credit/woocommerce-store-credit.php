<?php
/**
 * Plugin Name: Store Credit for WooCommerce
 * Plugin URI: https://woocommerce.com/products/store-credit/
 * Description: Create "store credit" coupons for customers which are redeemable at checkout.
 * Version: 4.5.4
 * Author: Kestrel
 * Author URI: https://kestrelwp.com/
 * Requires PHP: 7.4
 * Requires at least: 5.4
 * Tested up to: 6.5
 * Text Domain: woocommerce-store-credit
 * Domain Path: /languages/
 *
 * Requires Plugins: woocommerce
 * WC requires at least: 4.0
 * WC tested up to: 8.9
 * Woo: 18609:c4bf3ecec4146cb69081e5b28b6cdac4
 *
 * Copyright: (c) 2012-2024 Kestrel [hey@kestrelwp.com]
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WC_Store_Credit
 * @since   2.1.11
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin requirements.
 */
if ( ! class_exists( 'WC_Store_Credit_Requirements', false ) ) {
	require_once __DIR__ . '/includes/class-wc-store-credit-requirements.php';
}

if ( ! WC_Store_Credit_Requirements::are_satisfied() ) {
	return;
}

// Define WC_STORE_CREDIT_FILE constant.
if ( ! defined( 'WC_STORE_CREDIT_FILE' ) ) {
	define( 'WC_STORE_CREDIT_FILE', __FILE__ );
}

// Include the main class of the plugin.
if ( ! class_exists( 'WC_Store_Credit' ) ) {
	include_once __DIR__ . '/includes/class-wc-store-credit.php';
}

/**
 * Main instance of the plugin.
 *
 * Returns the main instance of the plugin to prevent the need to use globals.
 *
 * @since 3.0.0
 *
 * @return WC_Store_Credit
 */
function wc_store_credit() {
	return WC_Store_Credit::instance();
}

wc_store_credit();
