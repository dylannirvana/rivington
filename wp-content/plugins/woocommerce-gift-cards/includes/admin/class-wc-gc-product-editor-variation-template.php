<?php
/**
 * WC_GC_GiftCardVariationTemplate class
 *
 * @package  Woo Gift Cards
 * @since    1.17.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


use Automattic\WooCommerce\Internal\Features\ProductBlockEditor\ProductTemplates\ProductVariationTemplate;

/**
 * Gift Card Variation Template class.
 *
 * @version 1.17.0
 */
class WC_GC_GiftCardVariationTemplate extends ProductVariationTemplate {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_block_template_area_product-form_after_add_block_product-variation-details-section', array( $this, 'add_gift_card_fields' ) );
		parent::__construct();
		$shipping_group = $this->get_group_by_id( $this::GROUP_IDS['SHIPPING'] );
		if ( $shipping_group ) {
			$shipping_group->remove();
		}
	}

	/**
	 * Get the template ID.
	 *
	 * @return string
	 */
	public function get_id(): string {
		return 'gift-card-variation';
	}

	/**
	 * Get the template title.
	 *
	 * @return string
	 */
	public function get_title(): string { // phpcs:ignore
		return __( 'Gift Card Variation Template', 'woocommerce-gift-cards' );
	}

	/**
	 * Get the template description.
	 *
	 * @return string
	 */
	public function get_description(): string { // phpcs:ignore
		return __( 'Template for variations in the gift card form', 'woocommerce-gift-cards' );
	}

	/**
	 * Add gift card fields.
	 *
	 * @param BlockInterface $basic_details The basic details block.
	 */
	public function add_gift_card_fields( $basic_details ) {
		$parent   = $basic_details->get_parent();
		$section  = $parent->add_section( array(
			'id'         => 'gift-card-section',
			'order'      => 15,
			'attributes' => array(
				'title'       => __( 'Gift card', 'woocommerce-gift-cards' ),
				'description' => sprintf(
					/* translators: %1$s: Learn more link opening tag. %2$s: Learn more link closing tag.*/
						__( 'Create and sell digital gift cards that customers can use to pay for orders at your store. %1$sLearn more%2$s', 'woocommerce-gift-cards' ),
						'<a href="https://woo.com/document/gift-cards/store-owners-guide/" target="_blank" rel="noreferrer">',
						'</a>'
					),
			),
		));
		$columns  = $section->add_block( array(
			'id'        => 'gift-card-columns',
			'blockName' => 'core/columns',
		));
		$column_1 = $columns->add_block( array(
			'id'        => 'gift-card-column-1',
			'blockName' => 'core/column',
			'attributes' => array(
				'templateLock' => 'all',
			),
		));
		$column_1->add_block( array(
			'id'        => 'gift-card-recipient-email-image-field',
			'blockName' => 'woocommerce/product-select-field',
			'attributes' => array(
				'label' => __( 'Recipient email image', 'woocommerce-gift-cards' ),
				'help'  => __( 'This image is displayed in “Gift Card Received” emails sent to gift card recipients. Recommended ratio: 16:9', 'woocommerce-gift-cards' ),
				'property' => 'meta_data._gift_card_template_default_use_image',
				'options' => array(
					array(
						'value' => 'product',
						'label' => __( 'Use product cover image', 'woocommerce-gift-cards' ),
					),
					array(
						'value' => 'custom',
						'label' => __( 'Use custom image', 'woocommerce-gift-cards' ),
					),
					array(
						'value' => 'none',
						'label' => __( 'None', 'woocommerce-gift-cards' ),
					),
				),
			),
		));
		$columns->add_block( array(
			'id'        => 'gift-card-column-2',
			'blockName' => 'core/column',
			'attributes' => array(
				'templateLock' => 'all',
			),
		));
		$section->add_block(
			array(
				'id'        => 'gift-card-image-field',
				'blockName' => 'woocommerce-gift-cards/gift-card-image-field',
				'attributes' => array(),
			)
		);
	}
}
