<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

/* Add PRO features to SEO dashboard */
add_filter('seopress_features_list_before_tools', 'seopress_pro_features_list_before_tools');
function seopress_pro_features_list_before_tools($features)
{
    $docs = seopress_get_docs_links();
    $url = wp_parse_url(home_url());
    $host = isset($url['host']) ? $url['host'] : '';
    $port = isset($url['port']) ? ':' . $url['port'] : '';
    $current_site = $host . $port;

    $features['404'] = [
        'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-redirections.svg',
        'title' => __('Redirections', 'wp-seopress-pro'),
        'desc' => __('Monitor 404, create 301, 302 and 307 redirections.', 'wp-seopress-pro'),
        'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_404'),
        'help' => $docs['redirects']['enable'],
        'filter' => 'seopress_remove_feature_redirects',
    ];
    $features['rich-snippets'] = [
        'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-schemas.svg',
        'title' => __('Structured Data Types', 'wp-seopress-pro'),
        'desc' => __('Add data types to your content: articles, courses, recipes, videos, events, products and more.', 'wp-seopress-pro'),
        'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_404'),
        'help' => $docs['schemas']['add'],
        'filter' => 'seopress_remove_feature_schemas',
    ];
    if ( ! is_multisite() || (is_multisite() && defined('SUBDOMAIN_INSTALL') && true === constant('SUBDOMAIN_INSTALL'))//subdomains or single site
	|| (is_multisite() && defined('SUBDOMAIN_INSTALL') && false === constant('SUBDOMAIN_INSTALL') && defined('DOMAIN_CURRENT_SITE') && $current_site !== constant('DOMAIN_CURRENT_SITE')) //subdirectories with custom domains
	) {
        $features['robots'] = [
            'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-robots-txt.svg',
            'title' => __('robots.txt', 'wp-seopress-pro'),
            'desc' => __('Edit your robots.txt file.', 'wp-seopress-pro'),
            'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_robots'),
            'help' => $docs['robots']['file'],
            'filter' => 'seopress_remove_feature_robots',
        ];
    }
    if ( ! is_multisite()) {
        $features['htaccess'] = [
            'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-htaccess.svg',
            'title' => __('.htaccess', 'wp-seopress-pro'),
            'desc' => __('Edit your htaccess file.', 'wp-seopress-pro'),
            'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_htaccess'),
            'filter' => 'seopress_remove_feature_htaccess',
            'toggle' => false,
        ];
    }
    $features['local-business'] = [
        'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-local-business.svg',
        'title' => __('Local Business', 'wp-seopress-pro'),
        'desc' => __('Add Google Local Business data type.', 'wp-seopress-pro'),
        'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_local_business'),
        'help' => $docs['lb']['ebook'],
        'filter' => 'seopress_remove_feature_local_business',
    ];
    $features['ai'] = [
        'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-ai.svg',
        'title' => __('AI', 'wp-seopress-pro'),
        'desc' => __('Use the power of artificial intelligence to increase your productivity.', 'wp-seopress-pro'),
        'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_ai'),
        'help' => $docs['ai']['errors'],
        'filter' => 'seopress_remove_feature_ai',
    ];
    $features['breadcrumbs'] = [
        'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-breadcrumbs.svg',
        'title' => __('Breadcrumbs', 'wp-seopress-pro'),
        'desc' => __('Enable Breadcrumbs for your theme and improve your SEO in SERPs.', 'wp-seopress-pro'),
        'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_breadcrumbs'),
        'help' => $docs['breadcrumbs']['get_started'],
        'filter' => 'seopress_remove_feature_breadcrumbs',
    ];
    $features['woocommerce'] = [
        'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-woocommerce.svg',
        'title' => __('WooCommerce', 'wp-seopress-pro'),
        'desc' => __('Improve WooCommerce SEO.', 'wp-seopress-pro'),
        'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_woocommerce'),
        'help' => $docs['woocommerce']['ebook'],
        'filter' => 'seopress_remove_feature_woocommerce',
    ];
    $features['edd'] = [
        'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-edd.svg',
        'title' => __('Easy Digital Downloads', 'wp-seopress-pro'),
        'desc' => __('Improve Easy Digital Downloads SEO.', 'wp-seopress-pro'),
        'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_edd'),
        'filter' => 'seopress_remove_feature_edd',
    ];
    $features['page-speed'] = [
        'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-google-page-speed.svg',
        'title' => __('Google Page Speed', 'wp-seopress-pro'),
        'desc' => __('Track your website performance to improve SEO with Google Page Speed.', 'wp-seopress-pro'),
        'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_page_speed'),
        'filter' => 'seopress_remove_feature_page_speed',
        'help' => $docs['page_speed']['api'],
        'toggle' => false,
    ];
    $features['inspect-url'] = [
        'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-google-search-console.svg',
        'title' => __('Google Search Console', 'wp-seopress-pro'),
        'desc' => __('Get clicks, positions, CTR and impressions. Inspect your URL for details about crawling, indexing, mobile compatibility, schemas and more.', 'wp-seopress-pro'),
        'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_inspect_url'),
        'filter' => 'seopress_remove_feature_inspect_url',
        'help' => $docs['search_console_api']['metrics'],
        'toggle' => true,
    ];
    $features['news'] = [
        'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-google-news-sitemap.svg',
        'title' => __('Google News Sitemap', 'wp-seopress-pro'),
        'desc' => __('Optimize your site for Google News.', 'wp-seopress-pro'),
        'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_news'),
        'filter' => 'seopress_remove_feature_news',
        'help' => $docs['google_news']['get_started'],
    ];
    $features['bot'] = [
        'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-broken-links.svg',
        'title' => __('Audit', 'wp-seopress-pro'),
        'desc' => __('Scan your site to find SEO problems and broken links.', 'wp-seopress-pro'),
        'btn_primary' => admin_url('admin.php?page=seopress-bot-batch'),
        'filter' => 'seopress_remove_feature_bot',
        'help' => $docs['bot'],
    ];
    $features['alerts'] = [
        'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-alert.svg',
        'title' => __('SEO Alerts', 'wp-seopress-pro'),
        'desc' => __('Receive alerts by email/Slack about your SEO before it‘s too late.', 'wp-seopress-pro'),
        'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_alerts'),
        'filter' => 'seopress_remove_feature_alerts',
    ];
    $features['dublin-core'] = [
        'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-dublin-core.svg',
        'title' => __('Dublin Core', 'wp-seopress-pro'),
        'desc' => __('Add Dublin Core meta tags.', 'wp-seopress-pro'),
        'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_dublin_core'),
        'filter' => 'seopress_remove_feature_dublin_core',
    ];
    $features['rss'] = [
        'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-rss.svg',
        'title' => __('RSS', 'wp-seopress-pro'),
        'desc' => __('Configure default WordPress RSS.', 'wp-seopress-pro'),
        'btn_primary' => admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_rss'),
        'filter' => 'seopress_remove_feature_rss',
        'help' => $docs['rss']['get_started'],
        'toggle' => false,
    ];

    return $features;
}

/* Add PRO features to SEO dashboard (after Tools) */
add_filter('seopress_features_list_after_tools', 'seopress_pro_features_list_after_tools');
function seopress_pro_features_list_after_tools($features)
{
    $docs = seopress_get_docs_links();

    $features['license'] = [
        'svg' => SEOPRESS_PRO_ASSETS_DIR . '/img/ico-license.svg',
        'title' => __('License', 'wp-seopress-pro'),
        'desc' => __('Edit your license key.', 'wp-seopress-pro'),
        'btn_primary' => admin_url('admin.php?page=seopress-license'),
        'filter' => 'seopress_remove_feature_license',
        'help' => $docs['license']['license_errors'],
        'toggle' => false,
    ];

    return $features;
}
