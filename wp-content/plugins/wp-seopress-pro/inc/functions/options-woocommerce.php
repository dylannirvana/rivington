<?php
defined('ABSPATH') or die('Please don&rsquo;t call the plugin directly. Thanks :)');

//WooCommerce
//=================================================================================================
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if (is_plugin_active('woocommerce/woocommerce.php')) {


    //noindex WooCommerce page
    add_filter('seopress_titles_noindex_bypass', 'seopress_pro_titles_noindex_bypass');
    function seopress_pro_titles_noindex_bypass($noindex) {
        if ('1' == seopress_get_toggle_option('woocommerce') && function_exists('is_cart') && (is_cart() && '1' === seopress_pro_get_service('OptionPro')->getWCCartPageNoindexEnable())) { //IS WooCommerce Cart page
            $noindex = seopress_pro_get_service('OptionPro')->getWCCartPageNoindexEnable();
        } elseif ('1' == seopress_get_toggle_option('woocommerce') && function_exists('is_checkout') && (is_checkout() && '1' === seopress_pro_get_service('OptionPro')->getWCCheckoutPageNoindexEnable())) { //IS WooCommerce Checkout page
            $noindex = seopress_pro_get_service('OptionPro')->getWCCheckoutPageNoindexEnable();
        } elseif ('1' == seopress_get_toggle_option('woocommerce') && function_exists('is_account_page') && (is_account_page() && '1' === seopress_pro_get_service('OptionPro')->getWCCustomerAccountPageNoindexEnable())) { //IS WooCommerce Customer account pages
            $noindex = seopress_pro_get_service('OptionPro')->getWCCustomerAccountPageNoindexEnable();
        } elseif ('1' == seopress_get_toggle_option('woocommerce') && function_exists('is_wc_endpoint_url') && (is_wc_endpoint_url() && '1' === seopress_pro_get_service('OptionPro')->getWCCustomerAccountPageNoindexEnable())) { //IS WooCommerce Customer account pages
            $noindex = seopress_pro_get_service('OptionPro')->getWCCustomerAccountPageNoindexEnable();
        }

        return $noindex;
    }


    if (is_singular('product')) {
        function seopress_woocommerce_product_og_price_hook() {
            if (seopress_pro_get_service('OptionPro')->getWCOGPriceEnable() === '1') {
                $product = wc_get_product(get_the_id());

                /*sale price*/
                $get_sale_price = '';
                if (isset($product) && method_exists($product, 'get_sale_price')) {
                    $get_sale_price = $product->get_sale_price();
                }

                /*sale price with tax (regular price as fallback if not available)*/
                $get_sale_price_with_tax = '';
                if (isset($product) && method_exists($product, 'get_price') && function_exists('wc_get_price_including_tax')) {
                    $get_sale_price_with_tax = wc_get_price_including_tax($product, ['price' => $get_sale_price]);
                }

                $get_sale_price_with_tax = apply_filters( 'seopress_product_price_amount', $get_sale_price_with_tax );

                $og_price = '<meta property="product:price:amount" content="' . $get_sale_price_with_tax . '">';

                echo $og_price . "\n";
            }
        }
        add_action('wp_head', 'seopress_woocommerce_product_og_price_hook', 1);

        //OG Currency
        function seopress_woocommerce_product_og_currency_hook() {
            if (seopress_pro_get_service('OptionPro')->getWCOGCurrencyEnable() === '1') {

                $currency = get_woocommerce_currency();

                $currency = apply_filters( 'seopress_product_price_currency', $currency );

                $og_currency = '<meta property="product:price:currency" content="' . $currency . '">';

                echo $og_currency . "\n";
            }
        }
        add_action('wp_head', 'seopress_woocommerce_product_og_currency_hook', 1);
    }

    //WooCommerce Structured data
    function seopress_woocommerce_schema_output_hook() {
        if (seopress_pro_get_service('OptionPro')->getWCDisableSchemaOutput() == '1') {
            if (function_exists('WC')) {
                remove_action('wp_footer', [ WC()->structured_data, 'output_structured_data' ], 10);
                remove_action('woocommerce_email_order_details', [ WC()->structured_data, 'output_email_structured_data' ], 30);
            }
        }
    }
    add_action('wp_head', 'seopress_woocommerce_schema_output_hook');

    //WooCommerce Breadcrumbs Structured data
    if (seopress_pro_get_service('OptionPro')->getWCDisableSchemaBreadcrumbsOutput() === '1') {
        add_filter('woocommerce_structured_data_breadcrumblist', '__return_false');
    }

    //WooCommerce Meta tag generator
    if (seopress_pro_get_service('OptionPro')->getWCDisableMetaGenerator() === '1') {
        remove_action('get_the_generator_html', 'wc_generator_tag', 10, 2);
        remove_action('get_the_generator_xhtml', 'wc_generator_tag', 10, 2);
    }
}
