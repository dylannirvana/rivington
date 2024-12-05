<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//EDD
//=================================================================================================
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) || is_plugin_active( 'easy-digital-downloads-pro/easy-digital-downloads.php' )) {
	if(is_singular('download')) {
		function seopress_edd_product_og_price_hook() {
			if (seopress_pro_get_service('OptionPro')->getEddOgPrice() ==='1') {
				if (function_exists('edd_get_download_price') && function_exists('edd_format_amount')) {
					$price = edd_format_amount(edd_get_download_price( get_the_id()));

                    $price = apply_filters( 'seopress_product_price_amount', $price );

					$seopress_social_og_price = '<meta property="product:price:amount" content="'.$price.'">';

					echo $seopress_social_og_price."\n";
				}
			}
		}
		add_action( 'wp_head', 'seopress_edd_product_og_price_hook', 1 );

		//OG Currency
		function seopress_edd_product_og_currency_hook() {
			if (seopress_pro_get_service('OptionPro')->getEddOgCurrency() ==='1') {
				if (function_exists('edd_get_currency') && edd_get_currency() !='') {

                    $currency = edd_get_currency();

                    $currency = apply_filters( 'seopress_product_price_currency', $currency );

					$seopress_social_og_currency = '<meta property="product:price:currency" content="'.$currency.'">';

					echo $seopress_social_og_currency."\n";
				}

			}
		}
		add_action( 'wp_head', 'seopress_edd_product_og_currency_hook', 1 );
	}
	//EDD Meta tag generator
	if (seopress_pro_get_service('OptionPro')->getEddMetaGenerator() ==='1') {
		remove_action('wp_head','edd_version_in_header');
	}
}
