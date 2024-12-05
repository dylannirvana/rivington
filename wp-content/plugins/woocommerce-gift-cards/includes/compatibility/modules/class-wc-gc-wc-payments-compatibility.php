<?php
/**
 * WC_GC_WC_Payments_Compatibility class
 *
 * @package  Woo Gift Cards
 * @since    1.10.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooPayments Compatibility.
 *
 * @version  1.16.11
 */
class WC_GC_WC_Payments_Compatibility {

	/**
	 * Initialize integration.
	 */
	public static function init() {
		// Hide express checkout buttons in gift card product pages.
		add_filter( 'wcpay_payment_request_is_product_supported', array( __CLASS__, 'handle_express_checkout_buttons' ), 10, 2 );
		add_filter( 'wcpay_woopay_button_is_product_supported', array( __CLASS__, 'handle_express_checkout_buttons' ), 10, 2 );
	}

	/**
	 * Hide express checkout buttons in gift card product pages.
	 *
	 * @param  bool       $is_supported
	 * @param  WC_Product $product
	 * @return bool
	 */
	public static function handle_express_checkout_buttons( $is_supported, $product ) {
		if ( false === $is_supported ) {
			return $is_supported;
		}

		if ( WC_GC_Gift_Card_Product::is_gift_card( $product ) && 'never' !== get_option( 'wc_gc_settings_send_as_gift_status', 'always' ) ) {
			$is_supported = false;
		}

		return $is_supported;
	}
}

WC_GC_WC_Payments_Compatibility::init();
