<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_woocommerce()
{
    seopress_print_pro_section('woocommerce');
?>

    <img src="<?php echo esc_url(SEOPRESS_PRO_ASSETS_DIR . '/img/woocommerce-metabox.png'); ?>" alt='<?php esc_html_e('Screenshot of the WooCommerce metaboxe'); ?>' width='600' height='auto' />

    <?php
    if (!is_plugin_active('woocommerce/woocommerce.php')) { ?>

        <div class="seopress-notice is-warning">
            <p><?php echo wp_kses_post(__('You need to enable <strong>WooCommerce</strong> to apply these settings.', 'wp-seopress-pro')); ?></p>
        </div>

<?php
    }
}
