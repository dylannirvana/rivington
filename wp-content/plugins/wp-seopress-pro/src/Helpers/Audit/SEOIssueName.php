<?php

namespace SEOPressPro\Helpers\Audit;

if ( ! defined('ABSPATH')) {
    exit;
}

abstract class SEOIssueName {
    public static function getIssueNameI18n($issueName) {
        $data = [
            'json_schemas_duplicated'=> __('Duplicated JSON schemas', 'wp-seopress-pro'),
            'json_schemas_not_found'=> __('No JSON schemas found', 'wp-seopress-pro'),
            'old_post'=> __('Post too old', 'wp-seopress-pro'),
            'keywords_permalink'=> __('Target keyword not found in permalink', 'wp-seopress-pro'),
            'headings_not_found'=> __('No headings found', 'wp-seopress-pro'),
            'headings_h1_duplicated'=> __('Duplicated H1', 'wp-seopress-pro'),
            'headings_h1_not_found'=> __('H1 not found', 'wp-seopress-pro'),
            'headings_h1_without_target_kw'=> __('H1 without target keyword', 'wp-seopress-pro'),
            'headings_h2_without_target_kw'=> __('H2 without target keyword', 'wp-seopress-pro'),
            'headings_h3_without_target_kw'=> __('H3 without target keyword', 'wp-seopress-pro'),
            'title_without_target_kw'=> __('Meta title without target keyword', 'wp-seopress-pro'),
            'title_too_long'=> __('Meta title too long', 'wp-seopress-pro'),
            'title_not_custom'=> __('Meta title not customized', 'wp-seopress-pro'),
            'description_without_target_kw'=> __('Meta desc without target keyword', 'wp-seopress-pro'),
            'description_too_long'=> __('Meta desc too long', 'wp-seopress-pro'),
            'description_not_custom'=> __('Meta desc not customized', 'wp-seopress-pro'),
            'og_title_duplicated'=> __('OG title duplicated', 'wp-seopress-pro'),
            'og_title_empty'=> __('OG title empty', 'wp-seopress-pro'),
            'og_title_missing'=> __('OG title missing', 'wp-seopress-pro'),
            'og_desc_duplicated'=> __('OG description duplicated', 'wp-seopress-pro'),
            'og_desc_empty'=> __('OG description empty', 'wp-seopress-pro'),
            'og_desc_missing'=> __('OG description missing', 'wp-seopress-pro'),
            'og_img_empty'=> __('OG image empty', 'wp-seopress-pro'),
            'og_img_missing'=> __('OG image missing', 'wp-seopress-pro'),
            'og_url_duplicated'=> __('OG URL duplicated', 'wp-seopress-pro'),
            'og_url_empty'=> __('OG URL empty', 'wp-seopress-pro'),
            'og_url_missing'=> __('OG URL missing', 'wp-seopress-pro'),
            'og_sitename_duplicated'=> __('OG sitename duplicated', 'wp-seopress-pro'),
            'og_sitename_empty'=> __('OG sitename empty', 'wp-seopress-pro'),
            'og_sitename_missing'=> __('OG sitename missing', 'wp-seopress-pro'),
            'x_title_duplicated'=> __('X title duplicated', 'wp-seopress-pro'),
            'x_title_empty'=> __('X title empty', 'wp-seopress-pro'),
            'x_title_missing'=> __('X title missing', 'wp-seopress-pro'),
            'x_desc_duplicated'=> __('X description duplicated', 'wp-seopress-pro'),
            'x_desc_empty'=> __('X description empty', 'wp-seopress-pro'),
            'x_desc_missing'=> __('X description missing', 'wp-seopress-pro'),
            'x_img_empty'=> __('X image empty', 'wp-seopress-pro'),
            'x_img_missing'=> __('X image missing', 'wp-seopress-pro'),
            'meta_robots_duplicated'=> __('Meta robots duplicated', 'wp-seopress-pro'),
            'meta_robots_noindex'=> __('noindex is ON', 'wp-seopress-pro'),
            'meta_robots_nofollow'=> __('nofollow is ON', 'wp-seopress-pro'),
            'meta_robots_noimageindex'=> __('noimageindex is ON', 'wp-seopress-pro'),
            'meta_robots_noarchive'=> __('noarchive is ON', 'wp-seopress-pro'),
            'meta_robots_nosnippet'=> __('nosnippet is ON', 'wp-seopress-pro'),
            'meta_robots_nositelinkssearchbox'=> __('nositelinkssearchbox is ON', 'wp-seopress-pro'),
            'img_alt_missing'=> __('Alt text missing', 'wp-seopress-pro'),
            'img_alt_no_media'=> __('No media found', 'wp-seopress-pro'),
            'nofollow_links_too_many'=> __('Too many nofollow links', 'wp-seopress-pro'),
            'outbound_links_missing'=> __('Outbound links missing', 'wp-seopress-pro'),
            'internal_links_missing'=> __('Internal links missing', 'wp-seopress-pro'),
            'canonical_duplicated'=> __('Duplicated canonical tag', 'wp-seopress-pro'),
            'canonical_missing'=> __('Canonical tag missing', 'wp-seopress-pro'),
        ];

        return $data[$issueName];
    }
}
