<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Import / Exports settings page
///////////////////////////////////////////////////////////////////////////////////////////////////

//Import Redirections from CSV
function seopress_import_redirections_settings() {
    if (empty($_POST['seopress_action']) || 'import_redirections_settings' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_import_redirections_nonce'], 'seopress_import_redirections_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'import_settings'))) {
        return;
    }

    $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

    if ('csv' != $extension) {
        wp_die(__('Please upload a valid .csv file', 'wp-seopress-pro'));
    }
    $import_file = $_FILES['import_file']['tmp_name'];
    if (empty($import_file)) {
        wp_die(__('Please upload a file to import', 'wp-seopress-pro'));
    }

    if ( ! $_POST['import_sep']) {
        wp_die(__('Please choose a separator', 'wp-seopress-pro'));
    }

    $csv = array_map(function ($item) {
        if ('comma' == $_POST['import_sep']) {
            $sep = ',';
        } elseif ('semicolon' == $_POST['import_sep']) {
            $sep = ';';
        } else {
            wp_die(__('Invalid separator'));
        }

        return str_getcsv($item, $sep, '\"');
    }, file($import_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

    //Remove duplicates from CSV
    $csv = array_unique($csv, SORT_REGULAR);

    foreach ($csv as $key => $value) {
        $csv_line = $value;

        //Third column: redirections type
        if ('301' == $csv_line[2] || '302' == $csv_line[2] || '307' == $csv_line[2] || '410' == $csv_line[2] || '451' == $csv_line[2]) {
            $csv_type_redirects[2] = $csv_line[2];
        }

        //Fourth column: redirections enabled
        $csv_line[3] = strtolower($csv_line[3]);
        if ('yes' == $csv_line[3]) {
            $csv_type_redirects[3] = $csv_line[3];
        } else {
            $csv_type_redirects[3] = '';
        }

        //Fifth column: redirections query param
        if ( ! empty($csv_line[4])) {
            if ('exact_match' == $csv_line[4] || 'with_ignored_param' == $csv_line[4] || 'without_param' == $csv_line[4]) {
                $csv_type_redirects[4] = $csv_line[4];
            } else {
                $csv_type_redirects[4] = 'exact_match';
            }
        }

        //Seventh column: redirect categories
        if ( ! empty($csv_line[6])) {
            $cats = array_values(explode(',', $csv_line[6]));
            $cats = array_map('intval', $cats);
            $cats = array_unique($cats);
        }

        $regex_enable = '';
        //regex enabled
        $csv_line[7]= strtolower($csv_line[7]);
        if ('yes' === $csv_line[7]) {
            $regex_enable = 'yes';
        }


        //logged status
        $logged_status = 'both';
        $csv_line[8]= strtolower($csv_line[8]);
        if (!empty($csv_line[8])) {
            $logged_status = $csv_line[8];
        }


        if ( ! empty($csv_line[0])) {
            $count = null;
            if ( ! empty($csv_line[5])) {
                $count = $csv_line[5];
            }
            $id = wp_insert_post([
                    'post_title'  => rawurldecode($csv_line[0]),
                    'post_type'   => 'seopress_404',
                    'post_status' => 'publish',
                    'meta_input'  => [
                        '_seopress_redirections_value'      => rawurldecode($csv_line[1]),
                        '_seopress_redirections_type'       => $csv_type_redirects[2],
                        '_seopress_redirections_enabled'    => $csv_type_redirects[3],
                        '_seopress_redirections_enabled_regex'  => $regex_enable,
                        '_seopress_redirections_logged_status'  => $logged_status,
                        '_seopress_redirections_param'      => $csv_type_redirects[4],
                        'seopress_404_count'                => $count,
                    ],
                ]
            );

            //Assign terms
            if ( ! empty($csv_line[6])) {
                wp_set_object_terms($id, $cats, 'seopress_404_cat');
            }
        }
    }

    wp_safe_redirect(admin_url('edit.php?post_type=seopress_404'));
    exit;
}
add_action('admin_init', 'seopress_import_redirections_settings');

//Import Redirections from Yoast Premium (CSV)
function seopress_import_yoast_redirections() {
    if (empty($_POST['seopress_action']) || 'import_yoast_redirections' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_import_yoast_redirections_nonce'], 'seopress_import_yoast_redirections_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'import_settings'))) {
        return;
    }

    $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

    if ('csv' != $extension) {
        wp_die(__('Please upload a valid .csv file'));
    }
    $import_file = $_FILES['import_file']['tmp_name'];
    if (empty($import_file)) {
        wp_die(__('Please upload a file to import'));
    }

    $csv = array_map('str_getcsv', file($import_file));

    foreach (array_slice($csv, 1) as $_key => $_value) {
        $csv_line = $_value;

        //Third column: redirections type
        if ('301' == $csv_line[2] || '302' == $csv_line[2] || '307' == $csv_line[2] || '410' == $csv_line[2] || '451' == $csv_line[2]) {
            $csv_type_redirects[2] = $csv_line[2];
        }

        //Fourth column: redirections enabled
        $csv_type_redirects[3] = 'yes';

        //Fifth column: redirections query param
        $csv_type_redirects[4] = 'exact_match';


        if ( ! empty($csv_line[0])) {
            $csv_line[0] = substr($csv_line[0], 1);
            if ( ! empty($csv_line[1])) {
                if ('//' === $csv_line[1]) {
                    $csv_line[1] = '/';
                } else {
                    $csv_line[1] = home_url() . $csv_line[1];
                }
            }
            $id = wp_insert_post([
                'post_title'        => urldecode($csv_line[0]),
                'post_type'         => 'seopress_404',
                'post_status'       => 'publish',
                'meta_input'        => [
                    '_seopress_redirections_value'          => urldecode($csv_line[1]),
                    '_seopress_redirections_type'           => $csv_type_redirects[2],
                    '_seopress_redirections_enabled'        => $csv_type_redirects[3],
                    '_seopress_redirections_enabled_regex'  => '',
                    '_seopress_redirections_logged_status'  => 'both',
                    '_seopress_redirections_param'          => $csv_type_redirects[4],
                ],
            ]);
        }
    }
    wp_safe_redirect(admin_url('edit.php?post_type=seopress_404'));
    exit;
}
add_action('admin_init', 'seopress_import_yoast_redirections');

//Export Redirections to CSV file
function seopress_export_redirections_settings() {
    if (empty($_POST['seopress_action']) || 'export_redirections' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_export_redirections_nonce'], 'seopress_export_redirections_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'export_settings'))) {
        return;
    }

    //Init
    $redirects_html = '';

    $args = [
        'post_type'      => 'seopress_404',
        'posts_per_page' => '-1',
        'meta_query'     => [
            [
                'key'     => '_seopress_redirections_type',
                'value'   => ['301', '302', '307', '410', '451'],
                'compare' => 'IN',
            ],
        ],
    ];

    $args = apply_filters('seopress_export_redirections_query', $args);

    $seopress_redirects_query = new WP_Query($args);

    if ($seopress_redirects_query->have_posts()) {
        while ($seopress_redirects_query->have_posts()) {
            $seopress_redirects_query->the_post();
            $redirect_categories = get_the_terms(get_the_ID(), 'seopress_404_cat');

            if(!empty($redirect_categories)){
                $redirect_categories = join(', ', wp_list_pluck($redirect_categories, 'term_id'));
            }
            else{
                $redirect_categories = "";
            }

            $redirects_html .= html_entity_decode(urldecode(urlencode(esc_attr(wp_filter_nohtml_kses(get_the_title())))));
            $redirects_html .= ';';
            $redirects_html .= html_entity_decode(urldecode(urlencode(esc_attr(wp_filter_nohtml_kses(get_post_meta(get_the_ID(), '_seopress_redirections_value', true))))));
            $redirects_html .= ';';
            $redirects_html .= get_post_meta(get_the_ID(), '_seopress_redirections_type', true);
            $redirects_html .= ';';
            $redirects_html .= get_post_meta(get_the_ID(), '_seopress_redirections_enabled', true);
            $redirects_html .= ';';
            $redirects_html .= get_post_meta(get_the_ID(), '_seopress_redirections_param', true);
            $redirects_html .= ';';
            $redirects_html .= get_post_meta(get_the_ID(), 'seopress_404_count', true);
            $redirects_html .= ';';
            $redirects_html .= $redirect_categories;
            $redirects_html .= ';';
            $redirects_html .= get_post_meta(get_the_ID(), '_seopress_redirections_enabled_regex', true);
            $redirects_html .= ';';
            $redirects_html .= get_post_meta(get_the_ID(), '_seopress_redirections_logged_status', true);
            $redirects_html .= "\n";
        }
        wp_reset_postdata();
    }

    ignore_user_abort(true);
    nocache_headers();
    header('Content-Type: application/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=seopress-redirections-export-' . date('m-d-Y') . '.csv');
    header('Expires: 0');
    echo $redirects_html;
    exit;
}
add_action('admin_init', 'seopress_export_redirections_settings');

//Export Slug Changes to CSV file
function seopress_export_slug_changes() {
    if (empty($_POST['seopress_action']) || 'export_slug_changes' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_export_slug_changes_nonce'], 'seopress_export_slug_changes_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'export_settings'))) {
        return;
    }

    //Init
    $slug_changes_csv = '';

    $slug_changes = get_option('seopress_can_post_redirect') ?? null;

    if (!empty($slug_changes)) {
        foreach($slug_changes as $slug) {
            $slug_changes_csv .= html_entity_decode(urldecode(urlencode(esc_attr(wp_filter_nohtml_kses($slug['before_url'])))));
            $slug_changes_csv .= ';';
            $slug_changes_csv .= html_entity_decode(urldecode(urlencode(esc_attr(wp_filter_nohtml_kses($slug['new_url'])))));
            $slug_changes_csv .= ';';
            $slug_changes_csv .= esc_html($slug['type']);
            $slug_changes_csv .= "\n";
        }
    }

    ignore_user_abort(true);
    nocache_headers();
    header('Content-Type: application/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=seopress-slug-changes-export-' . date('m-d-Y') . '.csv');
    header('Expires: 0');
    echo $slug_changes_csv;
    exit;
}
add_action('admin_init', 'seopress_export_slug_changes');

//Export Redirections to txt file for .htaccess
function seopress_export_redirections_htaccess_settings() {
    if (empty($_POST['seopress_action']) || 'export_redirections_htaccess' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_export_redirections_htaccess_nonce'], 'seopress_export_redirections_htaccess_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'export_settings'))) {
        return;
    }

    //Init
    $redirects_html = '';

    $args = [
        'post_type'      => 'seopress_404',
        'posts_per_page' => '-1',
        'meta_query'     => [
            [
                'key'     => '_seopress_redirections_type',
                'value'   => ['301', '302', '307', '410', '451'],
                'compare' => 'IN',
            ],
            [
                'key'     => '_seopress_redirections_enabled',
                'value'   => 'yes',
            ],
        ],
    ];
    $seopress_redirects_query = new WP_Query($args);

    if ($seopress_redirects_query->have_posts()) {
        while ($seopress_redirects_query->have_posts()) {
            $seopress_redirects_query->the_post();

            switch (get_post_meta(get_the_ID(), '_seopress_redirections_type', true)) {
                case '301':
                    $type = 'redirect 301 ';
                    break;
                case '302':
                    $type = 'redirect 302 ';
                    break;
                case '307':
                    $type = 'redirect 307 ';
                    break;
                case '410':
                    $type = 'redirect 410 ';
                    break;
                case '451':
                    $type = 'redirect 451 ';
                    break;
            }

            $redirects_html .= $type . ' /' . untrailingslashit(urldecode(urlencode(esc_attr(wp_filter_nohtml_kses(get_the_title()))))) . ' ';
            $redirects_html .= urldecode(urlencode(esc_attr(wp_filter_nohtml_kses(get_post_meta(get_the_ID(), '_seopress_redirections_value', true)))));
            $redirects_html .= "\n";
        }
        wp_reset_postdata();
    }

    ignore_user_abort(true);
    echo $redirects_html;
    nocache_headers();
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename=seopress-redirections-htaccess-export-' . date('m-d-Y') . '.txt');
    header('Expires: 0');
    exit;
}
add_action('admin_init', 'seopress_export_redirections_htaccess_settings');

//Import Redirections from Redirections plugin JSON file
function seopress_import_redirections_plugin_settings() {
    if (empty($_POST['seopress_action']) || 'import_redirections_plugin_settings' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_import_redirections_plugin_nonce'], 'seopress_import_redirections_plugin_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'import_settings'))) {
        return;
    }

    $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

    if ('json' != $extension) {
        wp_die(__('Please upload a valid .json file'));
    }
    $import_file = $_FILES['import_file']['tmp_name'];
    if (empty($import_file)) {
        wp_die(__('Please upload a file to import'));
    }

    $settings = (array) json_decode(file_get_contents($import_file), true);

    foreach ($settings['redirects'] as $redirect_key => $redirect_value) {
        $type = '';
        if ( ! empty($redirect_value['action_code'])) {
            $type = $redirect_value['action_code'];
        } else {
            $type = '301';
        }

        $param = '';
        if ( ! empty($redirect_value['match_data']['source']['flag_query'])) {
            $flag_query = $redirect_value['match_data']['source']['flag_query'];
            if ('pass' == $flag_query) {
                $param = 'with_ignored_param';
            } elseif ('ignore' == $flag_query) {
                $param = 'without_param';
            } else {
                $param = 'exact_match';
            }
        }

        $enabled ='';
        if ( ! empty(true == $redirect_value['enabled'])) {
            $enabled ='yes';
        }
        $regex_enable ='';
        if ( ! empty($redirect_value['regex'])) {
            $regex_enable ='yes';
        }

        wp_insert_post([
            'post_title'  => ltrim(urldecode($redirect_value['url']), '/'),
            'post_type'   => 'seopress_404',
            'post_status' => 'publish',
            'meta_input'  => [
                '_seopress_redirections_value'   => urldecode($redirect_value['action_data']['url']),
                '_seopress_redirections_type'    => $type,
                '_seopress_redirections_enabled' => $enabled,
                '_seopress_redirections_enabled_regex' => $regex_enable,
                '_seopress_redirections_logged_status'  => 'both',
                '_seopress_redirections_param'   => $param,
            ],
        ]);
    }

    wp_safe_redirect(admin_url('edit.php?post_type=seopress_404'));
    exit;
}
add_action('admin_init', 'seopress_import_redirections_plugin_settings');

/**
 * Import Redirections from Rank Math plugin JSON file
 *
 * @since 3.8.2
 * @updated 6.3.0
 *
 */
function seopress_import_rk_redirections() {
    if (empty($_POST['seopress_action']) || 'import_rk_redirections' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_import_rk_redirections_nonce'], 'seopress_import_rk_redirections_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'import_settings'))) {
        return;
    }

    $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

    if ('json' != $extension) {
        wp_die(__('Please upload a valid .json file'));
    }
    $import_file = $_FILES['import_file']['tmp_name'];
    if (empty($import_file)) {
        wp_die(__('Please upload a file to import'));
    }

    $settings = (array) json_decode(file_get_contents($import_file), true);

    foreach ($settings['redirections'] as $redirect_key => $redirect_value) {
        $type = '';
        if ( ! empty($redirect_value['header_code'])) {
            $type = $redirect_value['header_code'];
        }

        $source = '';
        if ( ! empty($redirect_value['sources'])) {
            if (is_serialized($redirect_value['sources'])) {

                $source = @unserialize(sanitize_text_field($redirect_value['sources']), ['allowed_classes' => false]);

                if (is_array($source)) {
                    $source = ltrim(urldecode($source[0]['pattern']), '/');
                }
            }
        }

        $param = 'exact_match';

        $enabled = '';
        if ( ! empty('active' == $redirect_value['status'])) {
            $enabled ='yes';
        }

        $redirect = '';
        if ( ! empty($redirect_value['url_to'])) {
            $redirect = urldecode($redirect_value['url_to']);
        }

        $count = '';
        if ( ! empty($redirect_value['hits'])) {
            $count = (int)$redirect_value['hits'];
        }

        $regex = '';
        if ( ! empty($redirect_value['sources'])) {
            if (is_serialized($redirect_value['sources'])) {
                $sources = @unserialize(sanitize_text_field($redirect_value['sources']), ['allowed_classes' => false]);

                if (is_array($sources)) {
                    if(in_array("regex", array_column($sources, 'comparison'))) {
                        $regex = 'yes';
                    }
                }
            }
        }

        wp_insert_post(
            [
                'post_title'  => $source,
                'post_type'   => 'seopress_404',
                'post_status' => 'publish',
                'meta_input'  => [
                    '_seopress_redirections_value'   => $redirect,
                    '_seopress_redirections_type'    => $type,
                    '_seopress_redirections_enabled' => $enabled,
                    '_seopress_redirections_enabled_regex' => $regex,
                    '_seopress_redirections_logged_status'  => 'both',
                    'seopress_404_count'             => $count,
                    '_seopress_redirections_param'   => $param,
                ],
            ]
        );
    }

    wp_safe_redirect(admin_url('edit.php?post_type=seopress_404'));
    exit;
}
add_action('admin_init', 'seopress_import_rk_redirections');

/**
 * Import Redirections from AIOSEO plugin JSON file
 *
 * @since 7.6.0
 */
function seopress_import_aioseo_redirections() {
    if (empty($_POST['seopress_action']) || 'import_aioseo_redirections' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_import_aioseo_redirections_nonce'], 'seopress_import_aioseo_redirections_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'import_settings'))) {
        return;
    }

    $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

    if ('json' != $extension) {
        wp_die(__('Please upload a valid .json file'));
    }
    $import_file = $_FILES['import_file']['tmp_name'];
    if (empty($import_file)) {
        wp_die(__('Please upload a file to import'));
    }

    $settings = (array) json_decode(file_get_contents($import_file), true);

    foreach ($settings as $redirect_key => $redirect_value) {
        $type = '';
        if ( ! empty($redirect_value['type'])) {

            switch ($redirect_value['type']) {
                case '301':
                    $type = '301';
                    break;
                case '302':
                    $type = '302';
                    break;
                case '307':
                    $type = '307';
                    break;
                case '410':
                    $type = '410';
                    break;
                case '451':
                    $type = '451';
                    break;
                default:
                    $param = '301';
            }
        }

        $source = '';
        if ( ! empty($redirect_value['source_url'])) {
            $source = sanitize_text_field($redirect_value['source_url']);
            $source = ltrim(urldecode($source), '/');
        }

        $param = 'exact_match';
        if ( !empty($redirect_value['query_param'])) {

            switch ($redirect_value['query_param']) {
                case 'exact':
                    $param = 'exact_match';
                    break;
                case 'ignore':
                    $param = 'without_param';
                    break;
                case 'pass':
                    $param = 'with_ignored_param';
                    break;
                case 'utm':
                    $param = 'with_ignored_param';
                    break;
                default:
                    $param = 'exact_match';
            }
        }

        $enabled = '';
        if ( ! empty('1' === $redirect_value['enabled'])) {
            $enabled ='yes';
        }

        $redirect = '';
        if ( ! empty($redirect_value['target_url'])) {
            $redirect = urldecode($redirect_value['target_url']);
        }

        $count = '';

        $regex = '';
        if ( ! empty('1' === $redirect_value['regex'])) {
            $regex = 'yes';
        }

        $logged_status = 'both';
        if ( ! empty($redirect_value['custom_rules'])) {
            $custom_rules = json_decode($redirect_value['custom_rules'], true);

            foreach($custom_rules as $rule_key => $rule_value) {
                if ($rule_value['type']==='login') {
                    switch ($rule_value['value']) {
                        case 'loggedin':
                            $logged_status = 'only_logged_in';
                            break;
                        case 'loggedout':
                            $logged_status = 'only_not_logged_in';
                            break;
                    }
                }
            }
        }

        wp_insert_post(
            [
                'post_title'  => $source,
                'post_type'   => 'seopress_404',
                'post_status' => 'publish',
                'meta_input'  => [
                    '_seopress_redirections_value'   => $redirect,
                    '_seopress_redirections_type'    => $type,
                    '_seopress_redirections_enabled' => $enabled,
                    '_seopress_redirections_enabled_regex' => $regex,
                    '_seopress_redirections_logged_status'  => $logged_status,
                    'seopress_404_count'             => $count,
                    '_seopress_redirections_param'   => $param,
                ],
            ]
        );
    }

    wp_safe_redirect(admin_url('edit.php?post_type=seopress_404'));
    exit;
}
add_action('admin_init', 'seopress_import_aioseo_redirections');

/**
 * Import Redirections from SmartCrawl plugin JSON file
 *
 * @since 7.6.0
 */
function seopress_import_smartcrawl_redirections() {
    if (empty($_POST['seopress_action']) || 'import_smartcrawl_redirections' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_import_smartcrawl_redirections_nonce'], 'seopress_import_smartcrawl_redirections_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'import_settings'))) {
        return;
    }

    $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

    if ('json' != $extension) {
        wp_die(__('Please upload a valid .json file'));
    }
    $import_file = $_FILES['import_file']['tmp_name'];
    if (empty($import_file)) {
        wp_die(__('Please upload a file to import'));
    }

    $settings = (array) json_decode(file_get_contents($import_file), true);

    foreach ($settings as $redirect_key => $redirect_value) {
        $type = '';
        if ( ! empty($redirect_value['type'])) {

            switch ($redirect_value['type']) {
                case '301':
                    $type = '301';
                    break;
                case '302':
                    $type = '302';
                    break;
                case '307':
                    $type = '307';
                    break;
                case '410':
                    $type = '410';
                    break;
                case '451':
                    $type = '451';
                    break;
                default:
                    $param = '301';
            }
        }

        $source = '';
        if ( ! empty($redirect_value['source'])) {
            $source = sanitize_text_field($redirect_value['source']);
            $source = wp_parse_url($source);
            if (is_array($source) && isset($source['path'])) {
                $source = $source['path'];
            }

            $source = ltrim(rawurldecode($source), '/');
        }

        $param = 'exact_match';

        $enabled ='yes';

        $redirect = '';
        if ( ! empty($redirect_value['destination'])) {
            if (is_string($redirect_value['destination'])) {
                $redirect = rawurldecode($redirect_value['destination']);
            }
            if (is_array($redirect_value['destination']) && !empty($redirect_value['destination']['id'])) {
                $redirect = esc_url(get_permalink($redirect_value['destination']['id']));
            }
        }

        $count = '';

        $regex = '';
        if ( isset($redirect_value['options'][0]) && 'regex' === $redirect_value['options'][0]) {
            $regex = 'yes';
        }

        $logged_status = 'both';

        wp_insert_post(
            [
                'post_title'  => $source,
                'post_type'   => 'seopress_404',
                'post_status' => 'publish',
                'meta_input'  => [
                    '_seopress_redirections_value'   => $redirect,
                    '_seopress_redirections_type'    => $type,
                    '_seopress_redirections_enabled' => $enabled,
                    '_seopress_redirections_enabled_regex' => $regex,
                    '_seopress_redirections_logged_status'  => $logged_status,
                    'seopress_404_count'             => $count,
                    '_seopress_redirections_param'   => $param,
                ],
            ]
        );
    }

    wp_safe_redirect(admin_url('edit.php?post_type=seopress_404'));
    exit;
}
add_action('admin_init', 'seopress_import_smartcrawl_redirections');

//Export 404 errors to CSV file
function seopress_export_404_settings() {
    if (empty($_POST['seopress_action']) || 'export_404' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_export_404_nonce'], 'seopress_export_404_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'export_settings'))) {
        return;
    }

    //Init
    $errors_404_html = '';

    $args = [
        'post_type'      => 'seopress_404',
        'posts_per_page' => '-1',
        'meta_query'     => [
            [
                'key'     => '_seopress_redirections_type',
                'compare' => 'NOT EXISTS',
            ],
        ],
    ];

    $args = apply_filters('seopress_export_404_query', $args);

    $seopress_404_query = new WP_Query($args);

    if ($seopress_404_query->have_posts()) {
        while ($seopress_404_query->have_posts()) {
            $seopress_404_query->the_post();

            $errors_404_html .= html_entity_decode(urldecode(urlencode(esc_attr(wp_filter_nohtml_kses(get_the_title())))));
            $errors_404_html .= ';';
            $errors_404_html .= esc_html(get_post_meta(get_the_ID(), 'seopress_404_count', true));
            $errors_404_html .= ';';
            $errors_404_html .= html_entity_decode(urldecode(urlencode(esc_attr(wp_filter_nohtml_kses((get_post_meta(get_the_ID(), 'seopress_404_redirect_referer', true)))))));
            $errors_404_html .= "\n";
        }
        wp_reset_postdata();
    }

    ignore_user_abort(true);
    nocache_headers();
    header('Content-Type: application/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=seopress-404-export-' . date('m-d-Y') . '.csv');
    header('Expires: 0');
    echo $errors_404_html;
    exit;
}
add_action('admin_init', 'seopress_export_404_settings');

//Clean all 404
function seopress_clean_404_query_hook($args) {
    unset($args['date_query']);

    return $args;
}

function seopress_clean_404() {
    if (empty($_POST['seopress_action']) || 'clean_404' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_clean_404_nonce'], 'seopress_clean_404_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', '404'))) {
        return;
    }

    add_filter('seopress_404_cleaning_query', 'seopress_clean_404_query_hook');
    do_action('seopress_404_cron_cleaning', true);
    wp_safe_redirect(admin_url('edit.php?post_type=seopress_404'));
    exit;
}
add_action('admin_init', 'seopress_clean_404');

//Reset Count column
function seopress_clean_counters() {
    if (empty($_POST['seopress_action']) || 'clean_counters' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_clean_counters_nonce'], 'seopress_clean_counters_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', '404'))) {
        return;
    }

    global $wpdb;

    //SQL query
    $sql = 'DELETE  FROM `' . $wpdb->prefix . 'postmeta` WHERE `meta_key` = \'seopress_404_count\'';

    $sql = $wpdb->prepare($sql);

    $wpdb->query($sql);

    wp_safe_redirect(admin_url('edit.php?post_type=seopress_404'));
    exit;
}
add_action('admin_init', 'seopress_clean_counters');

//Clean all (redirects / 404 errors)
function seopress_clean_all() {
    if (empty($_POST['seopress_action']) || 'clean_all' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_clean_all_nonce'], 'seopress_clean_all_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', '404'))) {
        return;
    }

    global $wpdb;

    //SQL query
    $sql = 'DELETE `posts`, `pm`
		FROM `' . $wpdb->prefix . 'posts` AS `posts`
		LEFT JOIN `' . $wpdb->prefix . 'postmeta` AS `pm` ON `pm`.`post_id` = `posts`.`ID`
		WHERE `posts`.`post_type` = \'seopress_404\'';

    $sql = $wpdb->prepare($sql);

    $wpdb->query($sql);

    wp_safe_redirect(admin_url('edit.php?post_type=seopress_404'));
    exit;
}
add_action('admin_init', 'seopress_clean_all');

//Export SEOPress BOT Links to CSV
function seopress_bot_links_export_settings() {
    if (empty($_POST['seopress_action']) || 'export_csv_links_settings' != $_POST['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_POST['seopress_export_csv_links_nonce'], 'seopress_export_csv_links_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'export_settings'))) {
        return;
    }
    $args = [
        'post_type'      => 'seopress_bot',
        'posts_per_page' => 1000,
        'post_status'    => 'publish',
        'order'          => 'DESC',
        'orderby'        => 'date',
    ];
    $the_query = new WP_Query($args);

    $settings['URL']        = [];
    $settings['Source']     = [];
    $settings['Source_Url'] = [];
    $settings['Status']     = [];
    $settings['Type']       = [];

    $csv_fields   = [];
    $csv_fields[] = 'URL';
    $csv_fields[] = 'Source';
    $csv_fields[] = 'Source URL';
    $csv_fields[] = 'Status';
    $csv_fields[] = 'Type';

    $output_handle = @fopen('php://output', 'w');

    //Header
    ignore_user_abort(true);
    nocache_headers();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=seopress-links-export-' . date('m-d-Y') . '.csv');
    header('Expires: 0');
    header('Pragma: public');

    //Insert header row
    fputcsv($output_handle, $csv_fields);

    // The Loop
    if ($the_query->have_posts()) {
        while ($the_query->have_posts()) {
            $the_query->the_post();

            array_push($settings['URL'], get_the_title());

            array_push($settings['Source'], get_post_meta(get_the_ID(), 'seopress_bot_source_title', true));

            array_push($settings['Source_Url'], get_post_meta(get_the_ID(), 'seopress_bot_source_url', true));

            array_push($settings['Status'], get_post_meta(get_the_ID(), 'seopress_bot_status', true));

            array_push($settings['Type'], get_post_meta(get_the_ID(), 'seopress_bot_type', true));

            fputcsv($output_handle, array_merge($settings['URL'], $settings['Source'], $settings['Source_Url'], $settings['Status'], $settings['Type']));

            //Clean arrays
            $settings['URL']        = [];
            $settings['Source']     = [];
            $settings['Source_Url'] = [];
            $settings['Status']     = [];
            $settings['Type']       = [];
        }
        wp_reset_postdata();
    }

    // Close output file stream
    fclose($output_handle);

    exit;
}
add_action('admin_init', 'seopress_bot_links_export_settings');

//Export metadata
function seopress_download_batch_export() {
    if (empty($_GET['seopress_action']) || 'seopress_download_batch_export' != $_GET['seopress_action']) {
        return;
    }
    if ( ! wp_verify_nonce($_GET['nonce'], 'seopress_csv_batch_export_nonce')) {
        return;
    }
    if ( ! current_user_can(seopress_capability('manage_options', 'export_settings'))) {
        return;
    }
    if ('' != get_option('seopress_metadata_csv')) {
        $csv = get_option('seopress_metadata_csv');

        $csv_fields   = [];
        $csv_fields[] = 'id';
        $csv_fields[] = 'post_title';
        $csv_fields[] = 'url';
        $csv_fields[] = 'slug';
		$csv_fields[] = 'taxonomy';
        $csv_fields[] = 'post_type';
        $csv_fields[] = 'meta_title';
        $csv_fields[] = 'meta_desc';
        $csv_fields[] = 'fb_title';
        $csv_fields[] = 'fb_desc';
        $csv_fields[] = 'fb_img';
        $csv_fields[] = 'tw_title';
        $csv_fields[] = 'tw_desc';
        $csv_fields[] = 'tw_img';
        $csv_fields[] = 'noindex';
        $csv_fields[] = 'nofollow';
        $csv_fields[] = 'noimageindex';
        $csv_fields[] = 'noarchive';
        $csv_fields[] = 'nosnippet';
        $csv_fields[] = 'canonical_url';
        $csv_fields[] = 'primary_cat';
        $csv_fields[] = 'redirect_active';
        $csv_fields[] = 'redirect_status';
        $csv_fields[] = 'redirect_type';
        $csv_fields[] = 'redirect_url';
        $csv_fields[] = 'target_kw';
        ob_start();
        $output_handle = @fopen('php://output', 'w');

        //Insert header row
        fputcsv($output_handle, $csv_fields, ';');

        //Header
        ignore_user_abort(true);
        nocache_headers();
        header('Content-Type: application/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=seopress-metadata-export-' . date('m-d-Y') . '.csv');
        header('Expires: 0');
        header('Pragma: public');

        if ( ! empty($csv)) {
            foreach ($csv as $value) {
                fputcsv($output_handle, $value, ';');
            }
        }

        // Close output file stream
        fclose($output_handle);

        //Clean database
        delete_option('seopress_metadata_csv');
        exit;
    }
}
add_action('admin_init', 'seopress_download_batch_export');

// Delete all SEO Issues
function seopress_clean_audit_scans() {
    if (empty($_POST['seopress_action']) || 'clean_audit_scans' != $_POST['seopress_action']) {
        return;
    }
    if (!wp_verify_nonce($_POST['seopress_clean_audit_scans_nonce'], 'seopress_clean_audit_scans_nonce')) {
        return;
    }
    if (!current_user_can(seopress_capability('manage_options', 'cleaning'))) {
        return;
    }

    global $wpdb;

    // Clean custom table if it exists
    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}seopress_seo_issues'") === $wpdb->prefix . 'seopress_seo_issues') {
        $sql = 'DELETE FROM `' . $wpdb->prefix . 'seopress_seo_issues`';
        $sql = $wpdb->prepare($sql);
        $wpdb->query($sql);
    }

    wp_safe_redirect(admin_url('admin.php?page=seopress-import-export'));
    exit;
}
add_action('admin_init', 'seopress_clean_audit_scans');
