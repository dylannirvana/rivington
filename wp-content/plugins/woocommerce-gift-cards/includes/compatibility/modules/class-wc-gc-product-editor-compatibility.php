<?php
/**
 * WC_GC_Product_Editor_Compatibility class
 *
 * @package  Woo Gift Cards
 * @since    1.17.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Admin\Features\ProductBlockEditor\ProductTemplate;
use Automattic\WooCommerce\LayoutTemplates\LayoutTemplateRegistry;

/**
 * Product Editor Compatibility class.
 *
 * @version 1.17.0
 */
class WC_GC_Product_Editor_Compatibility {

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Includes.
		require_once WC_GC_ABSPATH . 'includes/admin/class-wc-gc-product-editor-template.php';
		require_once WC_GC_ABSPATH . 'includes/admin/class-wc-gc-product-editor-variation-template.php';

		// Assets.
		add_action( 'init', array( $this, 'register_custom_blocks_and_scripts' ) );

		// Templates.
		add_filter( 'woocommerce_product_editor_product_templates' , array( $this, 'add_product_template' ) );
		add_action( 'rest_api_init', array( $this, 'register_layout_templates' ) );
		add_filter( 'experimental_woocommerce_product_editor_product_template_id_for_product', array( $this, 'determine_product_template' ), 10, 2 );

		// Setup Guide for each user.
		add_filter( 'woocommerce_admin_get_user_data_fields', array( $this, 'add_gift_card_user_data_fields' ) );

		// Manage gift card during saving.
		add_filter( 'woocommerce_rest_pre_insert_product_object', array( $this, 'maybe_clear_gift_card_meta' ) );
		add_filter( 'woocommerce_rest_insert_product_object', array( $this, 'maybe_normalize_expiration_days_value' ) );
	}

	/**
	 * Maybe clear gift card meta when the template is changed.
	 *
	 * @param WC_Product $product Product object.
	 * @return WC_Product
	 */
	public function maybe_clear_gift_card_meta( $product ) {
		if ( WC_GC_Gift_Card_Product::is_gift_card( $product ) && $product->get_meta( '_product_template_id' ) !== 'gift-card-template' ) {
			$product->delete_meta_data( '_gift_card' );
			$product->delete_meta_data( '_gift_card_expiration_days' );
		}
		return $product;
	}

	/**
	 * Maybe normalize expiration days value.
	 *
	 * @param WC_Product $product Product object.
	 * @return WC_Product
	 */
	public function maybe_normalize_expiration_days_value( $product ) {
		if ( WC_GC_Gift_Card_Product::is_gift_card( $product ) ) {
			if ( $product->get_meta( '_gift_card_expiration_days') === '') {
				$product->update_meta_data( '_gift_card_expiration_days', '0' );
				$product->save_meta_data();
			}
		}
		return $product;
	}

	/**
	 * Determine product template.
	 *
	 * @param string $template_id Template ID.
	 * @param WC_Product $product Product object.
	 * @return string
	 */
	public function determine_product_template( $template_id, $product ) {
		if ( WC_GC_Gift_Card_Product::is_gift_card( $product ) ) {
			if ( $product->is_type('variation')) {
				return 'gift-card-variation-template';
			} else {
				return 'gift-card-template';
			}
		}
		return $template_id;
	}

	/**
	 * Add gift card user data fields.
	 *
	 * @param array $fields User data fields.
	 * @return array
	 */
	public function add_gift_card_user_data_fields( $fields ) {
		$fields[] = 'wc_gc_product_editor_tour_shown';
		return $fields;
	}

	/**
	 * Register custom blocks and scripts.
	 */
	public function register_custom_blocks_and_scripts() {
		if ( ! class_exists( 'Automattic\WooCommerce\Admin\PageController' ) || ! Automattic\WooCommerce\Admin\PageController::is_admin_page() || ! class_exists( 'Automattic\WooCommerce\Admin\Features\ProductBlockEditor\BlockRegistry' ) ) {
			return;
		}

		register_block_type( WC_GC_ABSPATH . 'assets/dist/gift-card-image/block.json' );
		register_block_type( WC_GC_ABSPATH . 'assets/dist/days-to-expire/block.json' );

		$asset_file = require WC_GC()->get_plugin_path() . '/assets/dist/gift-cards-welcome-guide.asset.php';
		wp_register_script( 'wc-gift-cards-welcome-guide', WC_GC()->get_plugin_url() . '/assets/dist/gift-cards-welcome-guide.js', $asset_file['dependencies'], WC_GC()->get_plugin_version(), true );
		wp_enqueue_script( 'wc-gift-cards-welcome-guide' );
		wp_register_style( 'wc-gc-welcome-guide-style', WC_GC()->get_plugin_url() . '/assets/dist/gift-cards-welcome-guide.css', false, WC_GC()->get_plugin_version() );
		wp_enqueue_style( 'wc-gc-welcome-guide-style' );
	}

	/**
	 * Register layout templates.
	 */
	public function register_layout_templates() {
		$layout_template_registry = wc_get_container()->get( LayoutTemplateRegistry::class );

		if ( ! $layout_template_registry->is_registered( 'gift-card' ) ) {
			$layout_template_registry->register(
				'gift-card',
				'product-form',
				WC_GC_GiftCardTemplate::class
			);
		}

		if ( ! $layout_template_registry->is_registered( 'gift-card-variation' ) ) {
			$layout_template_registry->register(
				'gift-card-variation',
				'product-form',
				WC_GC_GiftCardVariationTemplate::class
			);
		}
	}

	/**
	 * Add product template.
	 *
	 * @param array $templates Product templates.
	 * @return array
	 */
	public function add_product_template( $templates ) {
		$templates[] = new ProductTemplate(
			array(
				'id'                 => 'gift-card-template',
				'title'              => __( 'Gift card', 'woocommerce-gift-cards' ),
				'description'        => __( 'A digital voucher that a customer can redeem at your store.', 'woocommerce-gift-cards' ),
				'icon'               => 'gift',
				'layout_template_id' => 'gift-card',
				'product_data'       => array(
					'type' => 'simple',
					'meta_data' => array(
						array(
							'key'   => '_gift_card',
							'value' => 'yes',
						),
					),
				),
			)
		);
		$templates[] = new ProductTemplate(
			array(
				'id'                 => 'gift-card-variation-template',
				'title'              => __( 'Gift card variation', 'woocommerce-gift-cards' ),
				'description'        => '',
				'layout_template_id' => 'gift-card-variation',
				'is_selectable_by_user'         => false,
				'product_data' => array(
					'type' => 'variation',
				),
			)
		);
		return $templates;
	}
}

new WC_GC_Product_Editor_Compatibility();
