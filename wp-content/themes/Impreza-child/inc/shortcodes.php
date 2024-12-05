<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


add_shortcode('studio_booking_link', function() {
    ob_start();

    $link = add_query_arg(
        'studio',
        get_the_ID(),
        get_permalink(519)
    );

    echo '<a href="' . $link . '" class="w-btn us-btn-style_1"><span>Book Now</span></a>';

    return ob_get_clean();
});


/**
 * Woocommerce mini cart shortcode (NOT USED)
 */
add_shortcode('woocommerce_mini_cart', function() {
	if ( !is_product() ) {
		return;
	}

	ob_start(); ?>

	<div class="widget woocommerce widget_shopping_cart">
		<div class="widget_shopping_cart_content">
			<?php woocommerce_mini_cart(); ?>
		</div>
	</div>

	<?php
	//wp_enqueue_script( 'pw-gift-cards' );
	//wc_get_template( 'cart/apply-gift-card-after-cart-contents.php', array(), '', PWGC_PLUGIN_ROOT . 'templates/woocommerce/' );
	?>

	<?php return ob_get_clean();
});


add_shortcode('location_string', function() {
	ob_start();

	$location = get_field('location_address');
	if ( $location ) {
		$location_name = $location["name"];

		if(!$location_name){
			$location_name = explode(",",$location["address"])[0];
		}
		$street_address = $location_name. "<br />". $location["city"].", ".$location["state_short"].", ".$location["post_code"];

		/* 
		 * Sample return value from the location field.
			(
				[ADDRESS] => 666 PLAINSBORO RD SUITE 645, PLAINSBORO, NJ 08536, USA
				[LAT] => 40.3264734
				[LNG] => -74.5771756
				[ZOOM] => 14
				[PLACE_ID] => EJ82NJYGUGXHAW5ZYM9YBYBSZCBZDWL0ZSA2NDUSIFBSYWLUC2JVCM8GVG93BNNOAXASIE5KIDA4NTM2LCBVU0EIJROJCHYKFAOSCFP9KIRJ3COJEQM27X9M10UVEGLZDWL0ZSA2NDU
				[NAME] => 666 PLAINSBORO RD SUITE 645
				[STREET_NUMBER] => 666
				[STREET_NAME] => PLAINSBORO ROAD
				[STREET_NAME_SHORT] => PLAINSBORO RD
				[CITY] => PLAINSBORO TOWNSHIP
				[STATE] => NEW JERSEY
				[STATE_SHORT] => NJ
				[POST_CODE] => 08536
				[COUNTRY] => UNITED STATES
				[COUNTRY_SHORT] => US
			) 
		 */

		echo '	<div class="w-post-elm post_custom_field usg_post_custom_field_3 type_text location retrieve_lat_lng" data-lat='.$location['lat'].' data-lng='.$location['lng'].'>
					<span class="w-post-elm-value">'
						. $street_address . 
					'</span>
				</div>';
	}

	return ob_get_clean();
});


add_shortcode('location_city_only', function() {
	ob_start();

	$location = get_field('location_address');
	if ( $location ) {
		echo $location["city"];
	}

	return ob_get_clean();
});