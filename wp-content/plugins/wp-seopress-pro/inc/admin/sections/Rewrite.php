<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_rewrite()
{
    seopress_print_pro_section('rewrite');

    if (!is_plugin_active('permalink-manager-pro/permalink-manager.php')) {
        if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') && '1' !== seopress_get_service('ToggleOption')->getToggleWhiteLabel()) { ?>
            <p>
                <a href="https://www.seopress.org/go/permalink-manager-pro" target="_blank">
                    <?php esc_html_e('We recommend Permalink Manager PRO plugin to rewrite easily and efficiently your URLs for SEO. Starting from just €49.', 'wp-seopress-pro'); ?>
                </a>
                <span class="dashicons dashicons-external"></span>
            </p>
<?php
        }
    }
}
