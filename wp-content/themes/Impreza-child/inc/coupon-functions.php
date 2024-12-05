<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


//add_filter('woocommerce_add_acblw_success', 'change_coupon_added_notice');
//add_filter('woocommerce_add_acblw_notice', 'change_coupon_added_notice');
function change_coupon_added_notice($message) {
    if ( isset($_GET['apply_coupon']) ) {
        $coupon_code = sanitize_text_field( $_GET['apply_coupon']);

        $new_message = get_field('added_message', wc_get_coupon_id_by_code($coupon_code));

        if ( $new_message ) {
            $message = $new_message;
        }
    }

    return $message;
}


add_filter('option_acblw_before_coupon_applied', 'change_coupon_added_message', 10, 2);
add_filter('option_acblw_coupon_added_to_session', 'change_coupon_added_message', 10, 2);
function change_coupon_added_message($value, $option) {
    if ( isset($_GET['apply_coupon']) ) {
        $coupon_code = sanitize_text_field( $_GET['apply_coupon']);

        $message = get_field('added_message', wc_get_coupon_id_by_code($coupon_code));

        if ( $message ) {
            $value = $message;
        }
    }

    return $value;
}



add_filter('woocommerce_gc_admin_edit_gift_cards_per_page', function($amount) {
    return 50;
});


/**
 * See if a coupon is valid for book a studio
 */
add_filter('woocommerce_coupon_is_valid_for_product', function($valid, $product, $coupon, $values ) {
    if ( !$valid ) {
        return $valid;
    }

    if ( $product->get_id() == 519 ) {        
        if ( isset($values['booking']) ) {
            $coupon_id = $coupon->get_id();

            $studios = get_field('studios', $coupon_id, false);
            if ( $studios ) {
                $resource_id = $values['booking']['_resource_id'];

                if ( !in_array($resource_id, $studios) ) {
                    write_log('Not the right resource');
                    return false;
                }
            }

            $days_of_week = get_field('days_of_week', $coupon_id, false);
            if ( $days_of_week ) {
                $date =  $values['booking']['_date'];
                $day_of_week = date('l', strtotime($date));

                if ( !in_array($day_of_week, $days_of_week) ) {
                    write_log('Not the right day of week.');
                    return false;
                }
            }

            $start_time = get_field('start_time', $coupon_id, false);
            $end_time = get_field('end_time', $coupon_id, false);
            if ( $end_time == '00:00:00' ) {
                $end_time = '23:59:59';
            }

            if ( $start_time ) {
                $time =  $values['booking']['_time'];
                $time = date('Hi', strtotime($time));

                $start_time = date('Hi', strtotime($start_time));
                $end_time = date('Hi', strtotime($end_time));

                if ( $time < $start_time ) {
                    write_log('Before start time.');
                    return false;
                }

                if ( $time >= $end_time ) {
                    write_log('After end time.');
                    return false;
                }
            }

            //write_log($values);
        }
    }

    return $valid;
}, 20, 4);


/**
 * Change the URL of redeem coupon if it has a product ID
 */
add_filter('wc_store_credit_redeem_url', function($url, $coupon) {
    $product_ids = $coupon->get_product_ids();

    if ( count($product_ids) == 1 ) {
        $url = get_permalink($product_ids[0]);
    }

    return $url;
}, 20, 2);


/**
 * Filter the shop page permalink so the store credit redeem url works properly
 */
add_filter('woocommerce_get_shop_page_permalink', function($url) {
    if ( !function_exists('wc_is_store_credit_coupon') ) {
        return $url;
    }

    if ( isset( $_GET['redeem_store_credit'] ) ) {
        $coupon_code = rawurldecode( wc_clean( wp_unslash( $_GET['redeem_store_credit'] ) ) );

        if ( $coupon_code && wc_is_store_credit_coupon( $coupon_code ) ) {
            global $wp;
            $url = site_url($wp->request);
        }
    }

    return $url;
});


/**
 * When an order is cancelled, don't restore the store credit if one was used
 */
add_action('woocommerce_order_status_changed', function($order_id, $old_status, $new_status, $order) {
    if ( $new_status == 'cancelled' ) {
        remove_filters_with_method_and_class_name('woocommerce_order_status_changed', 'WC_Store_Credit_Order', 'order_status_changed', 10);
    }
}, 9, 4);


/**
 * Only show actual coupons on coupon page in admin and hide store credits
 */
add_action('pre_get_posts', function($query) {
    if ( is_admin() && $query->is_main_query() && isset($_GET['post_type']) && $_GET['post_type'] == 'shop_coupon' ) {
        if ( !isset($_GET['coupon_type']) ) {
            $meta_query = (array) $query->get('meta_query');
            $meta_query[] = array(
                'key' => 'discount_type',
                'value' => 'store_credit',
                'compare' => '!='
            );

            $query->set('meta_query', $meta_query);
        }
    }
});


/**
 * Edit the subsubsub links in the admin for coupons
 */
add_filter('views_edit-shop_coupon', function($views) {
    if ( isset($_GET['coupon_type']) && $_GET['coupon_type'] == 'store_credit' ) {
        foreach ( $views as $key => $value ) {
            preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $value, $result);

            if (!empty($result)) {
                $views[$key] = str_replace($result['href'][0], $result['href'][0] . '&coupon_type=store_credit', $value);
            }
        }
    }
    
    return $views;
});


/**
 * Add some javascript to add/edit coupon page in admin
 */
add_action( 'woocommerce_coupon_data_panels', function($coupon_id, $coupon) { ?>
    <script>
        (function($) {
            $(document).on('change', '#discount_type', function() {
                var acf_fields = acf.getFields();

                if ( $(this).val() == 'store_credit' ) {
                    $('.free_shipping_field').addClass('hidden');

                    $.each(acf_fields, function(index, field) {
                        field.hide();
                    });

                    $('.acf-postbox').addClass('hidden');
                } else {
                    $('.free_shipping_field').removeClass('hidden');

                    $.each(acf_fields, function(index, field) {
                        field.show();
                    });

                    $('.acf-postbox').removeClass('hidden');
                }
            });
        })(jQuery);
    </script>
<?php }, 20, 2);
