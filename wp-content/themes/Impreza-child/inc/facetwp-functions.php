<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Enqueue certain things when facet is on the page
 */
add_filter( 'facetwp_assets', function( $assets ) {
    $css_file_path = get_stylesheet_directory() . '/css/facetwp.css';
	$css_file_url = get_stylesheet_directory_uri() . '/css/facetwp.css';
    $assets['facetwp.css'] = $css_file_url . '?ver=' . filemtime($css_file_path);

    $js_file_path = get_stylesheet_directory() . '/js/facetwp.js';
	$js_file_url = get_stylesheet_directory_uri() . '/js/facetwp.js';
    $assets['facetwp.js'] = $js_file_url . '?ver=' . filemtime($js_file_path);

    return $assets;
});


/**
 * Add labels to facets
 */
add_filter( 'facetwp_facet_html2', function( $output, $params ) {
    /**
     * Don't put labels on these type of facets
     */
    $exclude = array(
        'pager',
        'sort',
        'search',
        'map',
        'reset'
        //'proximity'
    );

    if ( !in_array($params['facet']['type'], $exclude) ) {
        $output = '<span class="facet-label">' . $params['facet']['label']  .  '</span>' . $output;
    }

    return $output;
}, 10, 2 );


/**
 * Fix for random ordering and ajax, random caches every 5 minutes
 */
add_filter( 'posts_orderby', function($orderby) {
    $seed = floor( time() / 300 ); // randomize every 5 minutes
    $orderby = str_replace( 'RAND()', "RAND({$seed})", $orderby );
    return $orderby;
} );