<?php
/**
 * WC_GC_Stripe_Compatibility class
 *
 * @package  Woo Gift Cards
 * @since    1.1.5
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Stripe Gateway Compatibility.
 *
 * @version  1.16.9
 */
class WC_GC_Stripe_Compatibility {

	/**
	 * Initialize integration.
	 */
	public static function init() {
		add_filter( 'wc_stripe_hide_payment_request_on_product_page', array( __CLASS__, 'handle_express_checkout_buttons' ), 11, 2 );
	}

	/**
	 * Hide express checkout buttons on single Product page.
	 *
	 * @param  bool     $hide
	 * @param  WP_Post  $post (Optional)
	 * @return bool
	 */
	public static function handle_express_checkout_buttons( $hide, $post = null ) {

		if ( is_null( $post ) ) {
			global $post;
		}

		if ( ! is_object( $post ) || empty( $post->ID ) ) {
			return $hide;
		}

		$product = wc_get_product( $post->ID );
		if ( $product && is_a( $product, 'WC_Product' ) && WC_GC_Gift_Card_Product::is_gift_card( $product ) ) {
			$hide = true;
		}

		return $hide;
	}
}

WC_GC_Stripe_Compatibility::init();
