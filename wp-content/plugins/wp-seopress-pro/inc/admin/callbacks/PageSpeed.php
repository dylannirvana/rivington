<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Page Speed Insights
function seopress_ps_url_callback() {
    $options = get_option('seopress_pro_option_name');

    if (isset($_GET['data_permalink'])) {
        $check   = $_GET['data_permalink'];
    } else {
        $check   = isset($options['seopress_ps_url']) ? $options['seopress_ps_url'] : get_home_url();
    }

    printf(
    '<input id="seopress_ps_url" type="text" name="seopress_pro_option_name[seopress_ps_url]" aria-label="' . esc_html__('Enter a URL to analyse with Page Speed Insights', 'wp-seopress-pro') . '" placeholder="' . esc_html__('Enter a URL to analyse with Page Speed Insights', 'wp-seopress-pro') . '" value="%s">',
    esc_html($check)
    ); ?>

    <p class="seopress-help description">
        <?php esc_html_e('Leave this field empty to analyse homepage', 'wp-seopress-pro'); ?>
    </p>

    <?php
}
function seopress_ps_api_key_callback() {
    $options = get_option('seopress_pro_option_name');
    $check   = isset($options['seopress_ps_api_key']) ? $options['seopress_ps_api_key'] : null;
    $docs = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';

    printf(
    '<input id="seopress_ps_api_key" type="text" name="seopress_pro_option_name[seopress_ps_api_key]" aria-label="' . esc_html__('Google Page Speed Insights API key', 'wp-seopress-pro') . '" placeholder="' . esc_html__('Enter your Page Speed Insights API key', 'wp-seopress-pro') . '" value="%s">',
    esc_html($check)
    );

    ?>
    <p class="seopress-help description">
        <span class="dashicons dashicons-external"></span>
        <a href="<?php echo esc_url($docs['page_speed']['api']); ?>" target="_blank">
            <?php esc_html_e('Learn how to create a free Google Page Speed API key.', 'wp-seopress-pro'); ?>
        </a>
    </p>
    <p class="seopress-help description">
        <span class="dashicons dashicons-external"></span>
        <a href="<?php echo esc_url($docs['page_speed']['google']); ?>" target="_blank">
            <?php
                esc_html_e('A Page Speed Insights key', 'wp-seopress-pro');
            ?>
        </a>
        <?php esc_html_e('is required to avoid quota errors.', 'wp-seopress-pro'); ?>
    </p>

    <?php
    include_once dirname(dirname(__FILE__)) . '/sections/PageSpeedReport.php';
}
