<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * WP User data
 */

global $us_grid_user_ID;

$user_ID = ( $us_elm_context == 'grid' ) ? $us_grid_user_ID : get_current_user_id();

// Do not output this element if no user ID
if ( empty( $user_ID ) ) {
	return;
}

$_atts['class'] = 'w-user-elm ' . $type;
$_atts['class'] .= $classes ?? '';
$_atts['class'] .= $color_link ? ' color_link_inherit' : '';

// Custom Field
if ( $type == 'custom' AND $custom_field ) {
	$_atts['class'] .= ' ' . $custom_field;
	$value = get_user_option( $custom_field, $user_ID );

	// Get translated name of the user role
} elseif ( $type == 'role' ) {

	if ( $userdata = get_userdata( $user_ID ) ) {
		$_atts['class'] .= ' ' . $userdata->roles[0];
		$value = translate_user_role( wp_roles()->roles[ $userdata->roles[0] ][ 'name' ] );
	} else {
		$value = '';
	}

	// Get the amount of user posts
} elseif ( $type == 'post_count' ) {
	$value = count_user_posts( $user_ID, explode( ',', $post_type ), TRUE );

	// Get the user data value
} else {
	$value = get_user_option( $type, $user_ID );
}

// Apply custom format to the registration date
if ( $type == 'user_registered' AND $value ) {
	$value = wp_date( $date_format, strtotime( $value ) );
}

// Do not show the element with empty or unsupported value
if (
	! usb_is_preview() 
	AND (
		! is_scalar( $value )
		OR $value === ''
	)
) {
	return;
}

// Text before value
if ( $text_before !== '' OR usb_is_preview() ) {
	$text_before = '<span class="w-post-elm-before">' . $text_before . '</span>';
}

// Text after value
if ( $text_after !== '' OR usb_is_preview() ) {
	$text_after = '<span class="w-post-elm-after">' . $text_after . '</span>';
}

// Link
if ( $type != 'description' ) {
	$link_atts = us_generate_link_atts( $link, /* additional data */array( 'label' => (string) $value ) );
}

// Output the element
$output = '<' . $tag . us_implode_atts( $_atts ) . '>';
$output .= $text_before;
$output .= empty( $link_atts['href'] ) ? '' : ( '<a' . us_implode_atts( $link_atts ) . '>' );

$output .= (string) $value;

$output .= empty( $link_atts['href'] ) ? '' : '</a>';
$output .= $text_after;
$output .= '</' . $tag . '>';

echo $output;