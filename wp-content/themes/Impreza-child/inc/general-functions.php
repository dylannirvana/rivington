<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/***************************************************
 * 
 * NEVER EDIT THIS FILE
 * IT MAY OCCASIONALLY BE OVERWRITTEN
 * 
 ***************************************************/


/**
 * Debug helper function to write to debug.log
 */
if ( ! function_exists('write_log')) {
	function write_log ( $log )  {
	   if ( is_array( $log ) || is_object( $log ) ) {
		  error_log( print_r( $log, true ) );
	   } else {
		  error_log( $log );
	   }
	}
} 


/**
 * Debug helper function to print out things
 */
if ( ! function_exists('appnet_pr')) {
	function appnet_pr($var) {
		echo '<pre>' . print_r($var, true) . '</pre>';
	}
}


/**
 * Remove certain tags from being output
 */
remove_action( 'wp_head', 'wp_generator' ) ;
remove_action( 'wp_head', 'wlwmanifest_link' ) ;
remove_action( 'wp_head', 'rsd_link' ) ;
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );


/**
 * Remove certain admin bar links
 */
add_action( 'admin_bar_menu', function($wp_admin_bar) {
	$wp_admin_bar->remove_node('edit-home-page');
	$wp_admin_bar->remove_menu('edit_us_builder');
	$wp_admin_bar->remove_menu('us-builder');
	$wp_admin_bar->remove_menu('revslider');
	$wp_admin_bar->remove_menu('customize');
}, 999 );


/**
 * Remove rev slider meta tag output
 */
add_filter('revslider_meta_generator', function() {
	return '';
});


/**
 * Remove rev slider meta boxes
 */
add_action('do_meta_boxes', function() {
	if ( is_admin() ) {
        $post_types = get_post_types();
        foreach ( $post_types as $post_type ) {
            remove_meta_box( 'mymetabox_revslider_0', $post_type, 'normal' );
            remove_meta_box( 'slider_revolution_metabox', $post_type, 'side' );
        }
    }
});


/**
 * Remove page builder meta output
 */
add_action('init', function() {
	if (defined( 'WPB_VC_VERSION')) {
	    remove_action('wp_head', array(visual_composer(), 'addMetaData'));
    }
}, 100);


/**
 * Hide admin bar for non admins
 */
add_filter('show_admin_bar', function($show) {
	if ( ! current_user_can( 'administrator' ) ) {
		$show = false;
	}

    return $show;
}, 20, 1);


/**
 * Disable non admins from going to backend
 */
add_action('admin_init', function() {
	if ( ! defined('DOING_AJAX') && ! current_user_can('edit_posts') ) {
        wp_redirect( site_url() );
        exit;
    }
});


/**
 * Add custom field to menu items
 */
add_action('wp_nav_menu_item_custom_fields', function($item_id, $item) {
	$desktop_menu_only = get_post_meta($item_id, 'desktop_menu_only', true);
    $mobile_menu_only = get_post_meta($item_id, 'mobile_menu_only', true);
	?>
	
	<p class="desktop-menu-only" style="display:block;">
		<label for="desktop-menu-only-<?php echo $item_id; ?>" >
			<input type="checkbox" 
				id="desktop-menu-only-<?php echo $item_id; ?>" 
				name="desktop-menu-only[<?php echo $item_id; ?>]" 
				<?php checked($desktop_menu_only, true); ?> 
			/><?php _e('Desktop Menu Only', 'appnet'); ?>
		</label>
	</p>

    <p class="mobile-menu-only" style="display:block;">
		<label for="mobile-menu-only-<?php echo $item_id; ?>" >
			<input type="checkbox" 
				id="mobile-menu-only-<?php echo $item_id; ?>" 
				name="mobile-menu-only[<?php echo $item_id; ?>]" 
				<?php checked($mobile_menu_only, true); ?> 
			/><?php _e('Mobile Menu Only', 'appnet'); ?>
		</label>
	</p>

	<?php
}, 5, 2);


/**
 * Save custom field for menu items
 */
add_action('wp_update_nav_menu_item', function($menu_id, $menu_item_db_id) {
	$desktop_menu_only_value = (isset($_POST['desktop-menu-only'][$menu_item_db_id]) && $_POST['desktop-menu-only'][$menu_item_db_id] == 'on') ? true : false;
	update_post_meta($menu_item_db_id, 'desktop_menu_only', $desktop_menu_only_value);

    $mobile_menu_only_value = (isset($_POST['mobile-menu-only'][$menu_item_db_id]) && $_POST['mobile-menu-only'][$menu_item_db_id] == 'on') ? true : false;
	update_post_meta($menu_item_db_id, 'mobile_menu_only', $mobile_menu_only_value);
}, 10, 2);


/**
 * If menu item is desktop or mobile only add class
 */
add_filter('nav_menu_css_class', function($classes, $menu_item) {
	$desktop_menu_only = get_post_meta($menu_item->ID, 'desktop_menu_only', true);
	if ($desktop_menu_only) {
		$classes[] = 'desktop-menu-only';
	}

    $mobile_menu_only = get_post_meta($menu_item->ID, 'mobile_menu_only', true);
	if ($mobile_menu_only) {
		$classes[] = 'mobile-menu-only';
	}

	return $classes;
}, 10, 2);


/**
 * Formats the TinyMCE editor
 */
add_filter('tiny_mce_before_init', function($initArray ) {
	$initArray[ 'paste_as_text' ] = 'true';
    $initArray[ 'paste_preprocess' ] = 'function(pl, o) {
        o.content = o.content.replace(/<div(.*?)>/ig, "");
        o.content = o.content.replace(/<\/div>/ig, "");
        o.content = o.content.replace(/<font(.*?)>/ig, "");
        o.content = o.content.replace(/<\/font>/ig, "");
        o.content = o.content.replace(/<center(.*?)>/ig, "");
        o.content = o.content.replace(/<\/center>/ig, "");
        o.content = o.content.replace(/<span(.*?)>/ig, "");
        o.content = o.content.replace(/<\/span>/ig, "");
        o.content = o.content.replace(/<img(.*?)>/ig, "");
        o.content = o.content.replace(/<\/img>/ig, "");
        o.content = o.content.replace(/ class="(.*?)"/ig, "");
        o.content = o.content.replace(/ id="(.*?)"/ig, "");
        o.content = o.content.replace(/ style="(.*?)"/ig, "");
    }';

    //$initArray[ 'extended_valid_elements' ] = 'div[*],span[*]';

    return $initArray;
});


/**
 * Sort page blocks by title in admin
 */
add_action('pre_get_posts', function($query) {
	if (is_admin()) {
        $post_type = $query->get('post_type');
        if($post_type == 'us_page_block' || $post_type == 'us_content_template'){
            if($query->get('orderby') == ''){
                $query->set('orderby', 'title');
            }
            if($query->get('order') == ''){
                $query->set('order', 'ASC');
            }
        }
    }
});


/**
 * Remove custom appearance grid metabox
 */
add_filter('us_config_meta-boxes', function($configs) {
	foreach($configs as $key => $config) {
		if ($config['id'] == 'us_portfolio_settings') {
			unset($configs[$key]);
		}
	}
	return $configs;
});


/**
 * Disable auto updates
 */
add_filter('plugins_auto_update_enabled', '__return_false');
add_filter('themes_auto_update_enabled', '__return_false');
add_filter('auto_update_plugin', '__return_false');
add_filter('auto_update_theme', '__return_false');


/**
 * Don't log failed login attemps in simple history
 */
add_filter('simple_history/simple_logger/log_message_key', function( $doLog, $loggerSlug, $messageKey, $SimpleLoggerLogLevelsLevel, $context ) {
	// Don't log login attempts to non existing users
	if ( 'SimpleUserLogger' == $loggerSlug && 'user_unknown_login_failed' == $messageKey ) {
		$doLog = false;
	}
	// Don't log failed logins to existing users
	if ( 'SimpleUserLogger' == $loggerSlug && 'user_login_failed' == $messageKey ) {
		$doLog = false;
	}
	return $doLog;
}, 10, 5);


/**
 * Get the full IP address of people in simple history
 */
add_filter('simple_history/privacy/anonymize_ip_address', '__return_false');


/**
 * Rename files on upload to remove shutterstock in name
 */
add_filter('sanitize_file_name', function($filename) {
	$filename = str_replace('shutterstock_', '', $filename);
	return $filename;
}, 10 );


/**
 * Remove shutterstuck_ from title when uploading
 */
add_action('add_attachment', function($post_ID) {
	if ( wp_attachment_is_image( $post_ID ) ) {

		$my_image_title = get_post( $post_ID )->post_title;
		$my_image_title = str_replace('shutterstock_', '', $my_image_title);

		$my_image_meta = array(
			'ID'		=> $post_ID,
			'post_title'	=> $my_image_title
		);

		wp_update_post( $my_image_meta );
	} 
});


/**
 * Helper shortcodes
 */

// current year for footer
add_shortcode('current_year', function() {
	$year = date('Y');
    return $year;
});

// blog site name for footer
add_shortcode('site_name', function() {
    return get_bloginfo( 'name' );
});

// build sitemap link for footer
add_shortcode('sitemap_link', function() {
    return '<a href="'.site_url().'/sitemap/">Sitemap</a>';
});



/**
 * Add default featured image row background in theme options
 */
add_filter('us_config_theme-options', function( $options ) {
	$options['general']['fields']['default_featured_image'] = array(
		'title' => 'Default Featured Image',
		'title_pos' => 'side',
		'description' => 'Select a default fallback image for Page Builder rows if no featured image.',
		'type' => 'upload',
		'classes' => 'desc_3'
	);

    $link = add_query_arg('maintenance_mode_bypass_key', 'value', site_url());

    $maintenance_mode_bypass_key = array(
		'title' => 'Maintenance Mode Bypass Key',
		'title_pos' => 'side',
		'description' => 'Use the URL ' . $link . ' to bypass maintenance mode, replacing "value" with the key above.',
		'type' => 'text',
		'classes' => 'for_above',
        'show_if' => array( 'maintenance_mode', '=', TRUE ),
	);

    $options['general']['fields'] = array_slice($options['general']['fields'], 0, 3, true) +
    array('maintenance_mode_bypass_key' => $maintenance_mode_bypass_key) +
    array_slice($options['general']['fields'], 3, count($options['general']['fields']) - 3, true);

	return $options;
});


/**
 * Set the default featured image on row backgrounds if no feature image exists
 */
add_filter('shortcode_atts_vc_row', function($out, $pairs, $atts) {
	global $post;

    if (isset($atts['us_bg_image_source']) && $atts['us_bg_image_source'] == 'featured') {

        /**
         * Get the Impreza options
         */
        $option = get_option('usof_options_Impreza');
        $image_id = $option['default_featured_image'];
        if ( !$image_id ) {
            return $out;
        }

        /**
         * If post has no featured image or is an archive
         */
        if (!has_post_thumbnail($post->ID) || is_archive()) {
			if ($image_id && !empty($image_id)) {
				$out['us_bg_image_source'] = 'media';
				$out['us_bg_image'] = $image_id;
			}
		}
	    
	    /**
		 * Shop
		 */
		if (function_exists('is_shop')) {
			if (is_shop()) {
				$shop_id = get_option( 'woocommerce_shop_page_id' );
				$thumbnail_id = get_post_thumbnail_id( $shop_id );
				if ($thumbnail_id) {
					$out['us_bg_image_source'] = 'media';
					$out['us_bg_image'] = $thumbnail_id;
				}
			}
		}
		
		/**
		 * Product categories
		 */
		if (function_exists('is_product_category')) {
			if (is_product_category()) {
				$cat = get_queried_object();
				$thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
				if ($thumbnail_id) {
					$out['us_bg_image_source'] = 'media';
					$out['us_bg_image'] = $thumbnail_id;
				}
			}
		}
    }

    return $out;
}, 10, 3);


/**
 * Set the ability to bypass maintenance mode
 */
add_action('init', function() {
	$bypass_key = us_get_option( 'maintenance_mode_bypass_key' );

	if ( $bypass_key && !empty($bypass_key) ) {
		if ( isset($_GET['maintenance_mode_bypass_key']) && $_GET['maintenance_mode_bypass_key'] == $bypass_key ) {
			setcookie('maintenance_mode_bypass_key', $bypass_key, time() + (86400 * 30), '/');
		}
	}
});


add_filter('usof_get_option_maintenance_mode', function($value) {
	if ( $value ) {
		$bypass_key = us_get_option( 'maintenance_mode_bypass_key' );

		if ( $bypass_key && !empty($bypass_key) ) {
			if ( isset($_GET['maintenance_mode_bypass_key']) && $_GET['maintenance_mode_bypass_key'] == $bypass_key ) {
				return false;
			}

			if ( isset($_COOKIE['maintenance_mode_bypass_key']) && $_COOKIE['maintenance_mode_bypass_key'] == $bypass_key ) {
				return false;
			}
		}
	}
	
	return $value;
}, 20);
/**
 * End set the ability to bypass maintenance mode
 */


/**
 * Filter SEO Press post types
 */
add_filter('seopress_post_types', function($post_types) {
	foreach ( $post_types as $key => $post_type ) {
		if ( !is_post_type_viewable( $key ) ) {
			unset($post_types[$key]);
		}
	}

	return $post_types;
});


/**
 * Filter SEO Press taxonomies
 */
add_filter('seopress_get_taxonomies_args', function($args) {
	$args['publicly_queryable'] = true;
	return $args;
});


/**
 * Remove product attributes from SEO Press taxonomies
 */
add_filter('seopress_get_taxonomies_list', function($taxonomies) {
	foreach ( $taxonomies as $key => $value ) {
		if ( substr( $key, 0, 3 ) === 'pa_' ) {
			unset($taxonomies[$key]);
		}
	}

	return $taxonomies;
});


/**
 * Disable SEO Press universal metabox icon
 */
add_filter('seopress_can_enqueue_universal_metabox', '__return_false');


/**
 * Remove certain woocommerce nags
 */
add_filter('woocommerce_helper_suppress_admin_notices', '__return_true');
add_filter('woocommerce_allow_marketplace_suggestions', '__return_false');


/**
 * Hide Woocommerce extensions submenu
 */
/*add_action('admin_menu', function() {
	remove_submenu_page('woocommerce', 'wc-admin&path=/extensions');
}, 100);*/


/**
 * Redirect users after woocommerce login if redirect_to is set
 */
add_filter('woocommerce_login_redirect', function($redirect, $user) {
	if ( !empty($_REQUEST['redirect_to']) ) {
        $redirect = $_REQUEST['redirect_to'];
    }
    
    return $redirect;
}, 9999, 2);


/**
 * Function to wrap content in the Woocommerce email template
 */
function send_email_woocommerce_style( $heading, $message ) {
    if ( class_exists( 'WooCommerce' ) ) {
        $mailer = WC()->mailer();
        $wrapped_message = $mailer->wrap_message($heading, $message);
        $wc_email = new WC_Email;
        $html_message = $wc_email->style_inline($wrapped_message);
    } else {
        $html_message = $message;
    }
	
	return $html_message;
}


/**
 * Gravity forms enable some options by default on forms
 */
add_action('gform_after_save_form', function($form, $is_new) {
	if ( $is_new ) {
		$form['descriptionPlacement'] = 'above';
        $form['enableAnimation'] = true;
        $form['enableHoneypot'] = true;
        $form['is_active'] = '1';

		foreach ( $form['notifications'] as &$notification ) {
			$notification['from'] = '{from_email}';
		}

        GFAPI::update_form( $form );
    }
}, 10, 2);


/**
 * Gravity forms add custom from_email merge tag
 */
add_filter('gform_custom_merge_tags', function($merge_tags, $form_id, $fields, $element_id) {
	$merge_tags[] = array(
        'label' => 'From Email',
        'tag'   => '{from_email}',
    );
 
    return $merge_tags;
}, 20, 4 );


/**
 * Gravity forms filter merge tags output
 */
add_filter('gform_replace_merge_tags', function($text, $form, $entry) {
	if ( strpos($text, '{admin_email}') !== false ) {
		$company_email = get_field('company_email', 'option');

		if ( $company_email ) {
			$text = str_replace('{admin_email}', $company_email, $text);
		}
	} 
	
	if ( strpos($text, '{from_email}') !== false ) {
		$domain = parse_url(site_url());
		$text = str_replace('{from_email}', 'notification@' . $domain['host'], $text);
	}

	return $text;
}, 20, 3);


/**
 * Gravity forms enable field label visibility option
 */
add_filter('gform_enable_field_label_visibility_settings', '__return_true');


/**
 * Gravity forms readonly field
 */
add_filter('gform_field_content', function ( $field_content, $field ) {
    if ( strpos($field->cssClass, 'gf_readonly') !== false ) {
        return str_replace( 'type=', "readonly='readonly' type=", $field_content );
    }

    return $field_content;
}, 10, 2);


/**
 * Send all Gravity Form notification emails wrapped in the Woocommerce template
 */
add_filter('gform_notification', function($notification, $form, $entry) {
	if ( class_exists( 'WooCommerce' ) && function_exists('send_email_woocommerce_style') ) {
		$heading = $notification['subject'];
		$message = $notification['message'];

		$notification['disableAutoformat'] = true;
		$notification['message'] = send_email_woocommerce_style($heading, $message);
	}
	
    return $notification;
}, 10, 3);


/**
 * A Gravity Forms fix that was slowing down backend
 */
add_action('init', function() {
	if ( is_admin() ) {
		global $pagenow;

		if ( $pagenow == 'post.php' || $pagenow == 'post-new.php') {
			remove_action( 'init', array( 'GFForms', 'init_buffer' ) );
		}
	}
}, 9);


/**
 * Remove the live builder action from post row actions
 */
add_filter('post_row_actions', function($actions, $post) {
	unset( $actions['edit_us_builder'] );
	return $actions;
}, 601, 2);


/**
 * Don't generate cache file for logged in users
 */
add_filter('do_rocket_generate_caching_files', function($filter) {
	if ( is_user_logged_in() ) {
		return false;
	}

	return $filter;
});


/**
 * Remove account info/status from settings
 */
add_action('init', function() {
	define('WP_ROCKET_WHITE_LABEL_ACCOUNT', true);
});


/**
 * Remove WP Rocket metabox from unviewable post types
 */
add_filter('rocket_metabox_options_post_types', function($post_types) {
	foreach ( $post_types as $key => $post_type ) {
		$is_viewable = $post_type->publicly_queryable || ( $post_type->_builtin && $post_type->public );

		if ( !$is_viewable ) {
			unset($post_types[$key]);
		}
	}

	return $post_types;
});