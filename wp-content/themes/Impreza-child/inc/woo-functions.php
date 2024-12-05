<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Begin add quantity buttons on single products
 */
add_action('woocommerce_before_quantity_input_field', function() {
    if ( is_product() ) {
        echo '<input type="button" value="-" class="minus">';
    }
});


add_action('woocommerce_after_quantity_input_field', function() {
    if ( is_product() ) {
        echo '<input type="button" value="+" class="plus">';
    }
});


add_action('wp_footer', function() {
    if ( is_product() ) {
        wc_enqueue_js("   
            $(document).on( 'click', 'input.plus, input.minus', function() {
                var qty = $( this ).parent( '.quantity' ).find( '.qty' );
                var val = parseFloat(qty.val());
                var max = parseFloat(qty.attr( 'max' ));
                var min = parseFloat(qty.attr( 'min' ));
                var step = parseFloat(qty.attr( 'step' ));
        
                if ( $( this ).is( '.plus' ) ) {
                    if ( max && ( max <= val ) ) {
                    qty.val( max ).change();
                    } else {
                    qty.val( val + step ).change();
                    }
                } else {
                    if ( min && ( min >= val ) ) {
                    qty.val( min ).change();
                    } else if ( val > 1 ) {
                    qty.val( val - step ).change();
                    }
                }
            });
        ");
    }
});
/**
 * End add quantity buttons on single products
 */


/**
 * Add a booking fee to all orders
 */
add_action('woocommerce_cart_calculate_fees', function($cart) {
	if (is_admin() && !defined('DOING_AJAX')) {
		return;
	}

    //if ( sizeof( $cart->get_applied_coupons() ) > 0 )
          //return;
    
	//WC()->cart->add_fee(Booking Fee', 2);
    $cart->add_fee('Booking Fee', 2);
});


/**
 * Hide order notes at checkout
 */
add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 9999 );


/**
 * Remove terms and conditions form opening inline at checkout
 */
add_action('wp_enqueue_scripts', function() {
	if ( is_checkout() ) {
		wp_add_inline_script( 'wc-checkout', "jQuery( document ).ready( function() { jQuery( document.body ).off( 'click', 'a.woocommerce-terms-and-conditions-link' ); } );" );
	}
}, 1000);


/**
 * Disable order again button
 */
remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );


/**
 * Make all orders completed if paid and virtual only
 */
add_filter('woocommerce_payment_complete_order_status', function($order_status, $order_id, $order) {
    $virtual_only = true;

    foreach ( $order->get_items() as $item_id => $item ) {
        $product = $item->get_product();

        if ( !$product->is_virtual() ) {
            $virtual_only = false;
            break;
        }
    }

    if ( $virtual_only ) {
        $order_status = 'completed';
    }
    
    return $order_status;
}, 20, 3);


/**
 * Check payment gateway order status for testing
 */
add_filter('woocommerce_cheque_process_payment_order_status', 'myplugin_change_order_to_agent_processing', 10, 2 );
function myplugin_change_order_to_agent_processing( $status, $order ){
    return 'completed';
}


/**
 * Reorder my account to put bookings second and add gift cards link
 */
add_filter( 'woocommerce_account_menu_items', 'bbloomer_add_link_my_account' );
function bbloomer_add_link_my_account( $items ) {
    if ( isset($items['bookings']) ) {
        unset($items['bookings']);

        $items = array_slice( $items, 0, 1, true ) 
        + array( 'bookings' => 'Bookings' )
        + array_slice( $items, 1, NULL, true );
    }

    /*$items = array_slice( $items, 0, 3, true ) 
	+ array( 'gift-cards' => 'Gift cards' )
	+ array_slice( $items, 3, NULL, true );*/
    
    return $items;
}


/**
 * Convert woo account menu into dropdown on mobile
 */
add_action('wp_footer', function() {
    if ( is_account_page() ) { ?>
        <script>
            (function($) {
                $(document).ready(function() {
                    const menu_list = $('.woocommerce-MyAccount-navigation > ul');
                    menu_list.addClass('hide_on_tablets hide_on_mobiles');

                    const menu_select = $('<select />');

                    menu_list.find('a').each(function() {
                        let menu_select_option = $('<option />');

                        menu_select_option.attr('value', $(this).attr('href')).html($(this).html());

                        if ( $(this).parent('li').hasClass('is-active') ) {
                            menu_select_option.attr('selected', 'selected');
                        }

                        menu_select.append(menu_select_option);
                    });

                    menu_select.insertAfter(menu_list).addClass('account-menu-mobile hide_on_default hide_on_laptops');
                });

                $(document).on('change', '.account-menu-mobile', function() {
                    window.location = $(this).val();
                });
            })(jQuery);
        </script>
    <?php }
});


/**
 * Remove some annoying metaboxes from spots
 */
add_action('add_meta_boxes', function() {
    remove_meta_box('postcustom', 'shop_order', 'normal');
    remove_meta_box('order_custom', 'woocommerce_page_wc-orders', 'normal');

    remove_meta_box('woocommerce-order-downloads', 'shop_order', 'normal');
    remove_meta_box('woocommerce-order-downloads', 'woocommerce_page_wc-orders', 'normal');
}, 90);


/**
 * Edit import data so we can import Woo gift cards
 */
add_filter( 'woocommerce_gc_giftcards_import_process_item_data', function($data) {
    $code = str_split($data['code'], 4);
    $data['code'] = implode('-', $code);
    
    if ( !isset($data['recipient']) || empty($data['recipient']) ) {
        $data['recipient'] = 'no-reply@rivingtonmusic.com';
    }

    if ( !isset($data['sender']) || empty($data['sender']) ) {
        $data['sender'] = 'Rivington Music Rehearsal Studios';
    }
    
    write_log($data);
    return $data;
}, 20);
