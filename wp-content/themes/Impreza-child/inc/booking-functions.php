<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/*
 * Remove some ugly bookings styles
 */
add_action('wp_print_styles', function () {
    wp_dequeue_style('wc-bookings-styles');
    wp_dequeue_style('jquery-ui-style');
});

/*
 * Make bookable resources a public post type and viewable on front end
 */
add_filter('woocommerce_register_post_type_bookable_resource', function ($args) {
    $args['public'] = true;
    $args['publicly_queryable'] = true;
    $args['rewrite'] = ['slug' => 'studio'];
    $args['has_archive'] = 'studios';
    $args['supports'] = ['title', 'editor'];

    return $args;
});

/*
 * When an order with a booking fails payment, immediately run the action to change the booking from unpaid to cancelled
 */
add_filter('woocommerce_bookings_failed_order_expire_scheduled_time_stamp', function ($timestamp) {
    $timestamp = time();

    return $timestamp;
});

/*
 * Sort studios by menu_order
 */
add_action('pre_get_posts', function ($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_post_type_archive('bookable_resource')) {
        $query->set('orderby', 'menu_order');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', 100);
    }
});

/*
 * Change the order of the booking form fields.
 */
// add_filter('booking_form_fields', 'custom_order_booking_fields');
function custom_order_booking_fields($fields)
{
    $reorder = [];
    $reorder[] = $fields['wc_bookings_field_resource'];  // Resource
    $reorder[] = $fields['wc_bookings_field_start_date'];  // Calendar or Start Date
    $reorder[] = $fields['wc_bookings_field_duration'];  // Duration
    $reorder[] = $fields['wc_bookings_field_persons'];  // Persons

    return $reorder;
}

/*
 * Filter the start times for book a studio
 */
add_filter('woocommerce_bookings_filter_time_slots', 'filter_time_slots', 20, 3);

function filter_time_slots($available_slots, $bookable_product, $args)
{
    if ($bookable_product->get_id() == 519 && $args['resource_id']) {
        $resource_id = $args['resource_id'];
        $day_of_week = date('N', $args['from']);

        // Monday - Friday
        if ($day_of_week >= 1 && $day_of_week <= 5) {
            $schedule = get_field('weekday_schedule', $resource_id);
        }
        // Saturday - Sunday
        elseif ($day_of_week == 6 || $day_of_week == 7) {
            $schedule = get_field('weekend_schedule', $resource_id);
        }

        // write_log($available_slots);

        foreach ($available_slots as $timestamp => $slot_info) {
            $time_short = date('Hi', $timestamp);

            switch ($schedule) {
                    // Standard (1 Hour Slots from 12pm-6pm, 2 Hour Slots from 6pm-12am)
                case 'Standard':
                    // We do not allow start times at 7pm, 9pm, and 11pm
                    $exclude = [
                        1900,
                        2100,
                        2300,
                    ];
                    break;
                    // Expanded (1 Hour Slots from 12pm-12am)
                case 'Expanded':
                    // We allow all start times
                    $exclude = [];
                    break;
                    // Compact (3 Hour Slots from 12pm-12am)
                case 'Compact':
                    // We do not allow start times at 1pm, 2pm, 4pm, 5pm, 7pm, 8pm, 10pm, and 11pm
                    $exclude = [
                        1300,
                        1400,
                        1600,
                        1700,
                        1900,
                        2000,
                        2200,
                        2300,
                    ];
                    break;
                default:
                    $exclude = [];
            }

            if (in_array($time_short, $exclude)) {
                unset($available_slots[$timestamp]);
            }
        }
    }

    return $available_slots;
}
/* 
 * Disabled by AV, replaced by wc_bookings_get_end_time_html
 * Set the min and max duration on book a studio based on the start date and time and resource
 */
//add_filter('woocommerce_product_get_min_duration', 'set_booking_product_min_max_duration');
//add_filter('woocommerce_product_get_max_duration', 'set_booking_product_min_max_duration');
function set_booking_product_min_max_duration($value)
{
    if (empty($_POST) || empty($_POST['action'])) {
        return $value;
    }

    if ($_POST['action'] == 'wc_bookings_get_end_time_html' && $_POST['product_id'] == 519) {
        $resource_id = $_POST['resource_id'];

        // Add the timezone offset here because PHP time conversions are hard
        $timestamp = strtotime($_POST['start_date_time']) + wc_booking_timezone_offset();

        $timezone = wc_booking_get_timezone_string();
        $server_time = new DateTime(date('Y-m-d\TH:i:s', $timestamp), new DateTimeZone($timezone));

        $day_of_week = $server_time->format('N');
        $start_time = $server_time->format('Hi');

        // Monday - Friday
        if ($day_of_week >= 1 && $day_of_week <= 5) {
            $schedule = get_field('weekday_schedule', $resource_id);
        }
        // Saturday - Sunday
        elseif ($day_of_week == 6 || $day_of_week == 7) {
            $schedule = get_field('weekend_schedule', $resource_id);
        }

        switch ($schedule) {
                // Standard (1 Hour Slots from 12pm-6pm, 2 Hour Slots from 6pm-12am)
            case 'Standard':
                if ($start_time >= 1200 && $start_time < 1800) {
                    $value = 1;
                } elseif ($start_time >= 1800) {
                    $value = 2;
                }
                break;
                // Expanded (1 Hour Slots from 12pm-12am)
            case 'Expanded':
                $value = 1;
                break;
                // Compact (3 Hour Slots from 12pm-12am)
            case 'Compact':
                $value = 3;
                break;
            default:
                $value = 1;
        }
    }

    return $value;
}



//Added by AV start.

/**
 * Get max booking duration as per weekdays or weekends based on selected start time
 *
 * @param [type] $resource_id
 * @param [type] $start_date_time
 * @return void
 */
function studio_get_end_time_interval_by_weektime($resource_id, $start_date_time){
    
   

    // Add the timezone offset here because PHP time conversions are hard
    $timestamp = strtotime($start_date_time) + wc_booking_timezone_offset();

    $timezone = wc_booking_get_timezone_string();
    $server_time = new DateTime(date('Y-m-d\TH:i:s', $timestamp), new DateTimeZone($timezone));

    $day_of_week = $server_time->format('N');
    $start_time = $server_time->format('Hi');
    $schedule = '';
    // Monday - Friday
    if ($day_of_week >= 1 && $day_of_week <= 5) {
        $schedule = get_field('weekday_schedule', $resource_id);
    }
    // Saturday - Sunday
    elseif ($day_of_week == 6 || $day_of_week == 7) {
        $schedule = get_field('weekend_schedule', $resource_id);
    }

    if ($schedule === 'Standard') {
        // Standard (1 Hour Slots from 12pm-6pm, 2 Hour Slots from 6pm-12am)
        if ($start_time >= 1200 && $start_time < 1800) {
            $value = 1;
        } elseif ($start_time >= 1800) {
            $value = 2;
        }
    } elseif ($schedule === 'Expanded') {
        // Expanded (1 Hour Slots from 12pm-12am)
        $value = 1;
    } elseif ($schedule === 'Compact') {
        // Compact (3 Hour Slots from 12pm-12am)
        $value = 3;
    } else {
        // Default case
        $value = 1;
    }
    return [$schedule, $value];;
}

add_filter('wc_bookings_get_end_time_html', 'studio_altered_end_time_html', 10, 4);
/**
 * Alter end times to display as per weekdays or weekend schedule 12-6,6-12
 *
 * @param [HTML] $block_html
 * @param [ARRAY] $data
 * @param [ARRAY] $blocks
 * @param [ARRAY] $product
 * @return HTML
 */
function studio_altered_end_time_html($block_html, $data, $blocks, $product)
{


    if (!isset($_POST['start_date_time']) && !isset($_POST['resource_id'])) {
        return $block_html;
    }
    $start_date_time      = isset($_POST['start_date_time']) ? wc_clean(wp_unslash($_POST['start_date_time'])) : '';
    $resource_id = $_POST['resource_id'];

    $interval_info = studio_get_end_time_interval_by_weektime($resource_id, $start_date_time);
    
    $schedule = $interval_info[0];
    $interval = $interval_info[1];

    $new_options_html = '<option value="0">End time</option>';


    foreach ($data as $booking_data) {
        $display  = $booking_data['display'];
        $end_time = $booking_data['end_time'];
        $duration = $booking_data['duration'];
        
        $iv = ($duration % $interval);

        
        $time_short = date('Hi', $end_time);
        $exclude = [
            //1700,
            1900,
            2100,
            2300
        ];

        if($schedule == 'Standard' && in_array($time_short, $exclude)){
            continue;
        }
        elseif($schedule == 'Compact' && $interval == 3 && $iv > 0){ //user picked time between 6-12
            continue;
        }

       /*  if($schedule == 'Standard' && $interval == 1 && ($time_short > 1800 || $time_short == 0)){ //user picked time between 12-6
            continue;
        }
        elseif($schedule == 'Standard' && $interval == 2 && $iv == 1){ //user picked time between 6-12
            continue;
        }
        elseif($schedule == 'Compact' && $interval == 3 && $iv > 0){ //user picked time between 6-12
            continue;
        } */
        
        $resource_id = absint($_POST['resource_id'] ?? 0); // phpcs:ignore WordPress.Security.NonceVerification.Missing

        $availability = wc_bookings_get_total_available_bookings_for_range(
            $product,
            strtotime(substr($start_date_time, 0, 19)),
            $end_time,
            0
        );


        $free_slots = 0;
        if (is_array($availability)) {
            if (empty($resource_id)) {
                $free_slots = array_sum(array_values($availability));
            } else {
                $free_slots = $availability[$resource_id] ?? 0;
            }
        } elseif (is_int($availability)) {
            $free_slots = $availability;
        }

        $resource_ids         = $product->get_resource_ids();
        $resources_assignment = ! $product->has_resources() ? 'customer' : $product->get_resources_assignment();

        // Set default to display the left count to true.
        $display_free_slots = true;

        if ($product->has_resources()) {
            $resource     = new WC_Product_Booking_Resource($resource_id);
            $resource_qty = $resource->get_qty();

            // Do not display free slots when there is only one resource and it has only one quantity.
            if (1 === count($resource_ids)) {
                // Assure the resource quantity with exact resource ID as the resource ID is set to 0 when resource assignment is automatic.
                $resource_qty = $product->get_available_quantity($resource_ids[0]);
                if (1 === (int) $resource_qty) {
                    $display_free_slots = false;
                }
            } elseif ('customer' === $resources_assignment) {
                // Do not display free slots if there is more than one resource and the type is customer-selected.
                if (1 === (int) $resource_qty) {
                    $display_free_slots = false;
                }
            }
        } elseif (1 === $product->get_qty()) {
            // Do not display free slots when there is no resource(s) and the product has only one quantity.
            $display_free_slots = false;
        }
        
        $display    .= ! $display_free_slots ? '' : sprintf(' (%s %s)', $free_slots, esc_html__('left', 'woocommerce-bookings'));
        /* $new_options_html .= sprintf(
            '<option data-short-time="%s" data-interval="%s" data-duration-display="%s" data-value="%s" value="%s">%s</option>',
            intval($time_short),
            intval($iv),
            esc_attr($display),
            get_time_as_iso8601($end_time),
            esc_attr($duration),
            date_i18n(wc_bookings_time_format(), $end_time)
        ); */
        $new_options_html .= sprintf(
            '<option data-duration-display="%1$s" data-value="%2$s" value="%3$s">%4$s%1$s</option>',
            esc_attr( $display ),
            get_time_as_iso8601( $end_time ),
            esc_attr( $duration ),
            date_i18n( wc_bookings_time_format(), $end_time )
        );
    }

    $block_html = preg_replace(
        '/(<select[^>]*>)(.*?)(<\/select>)/s',
        "$1$new_options_html$3",
        $block_html
    );
    
    return $block_html;
}
//Added by AV ends.
/*add_filter( 'wc_bookings_get_time_slots_html', function( $block_html, $available_blocks, $blocks, $product ) {
    $posted = array();
    parse_str( wp_unslash( $_POST['form'] ), $posted );

    $resource_id = (int) $posted['wc_bookings_field_resource'];

    if ( !$resource_id ) {
        return $block_html;
    }

    foreach ( $available_blocks as $block => $quantity ) {
        if ( $quantity['available'] > 0 ) {
            $from = $block;
            $to = strtotime( 'midnight', $from + DAY_IN_SECONDS );
            $booking_form = new WC_Booking_Form( $product );
            $end_times = $booking_form->get_end_times( $blocks, get_time_as_iso8601( $block ), [], $resource_id, $from, $to, false );
            write_log($end_times);
        }
    }

    return $block_html;
}, 20, 4);*/

/*
 * Set the cost of book a studio based on the ACF price field of the selected resource
 */
add_filter('woocommerce_bookings_calculated_booking_cost', function ($booking_cost, $product, $data) {
    if ($product->get_id() == 519) {
        if (!empty($data['_resource_id'])) {
            $resource_id = $data['_resource_id'];

            $day_of_week = date('N', strtotime($data['_date']));

            // Monday - Friday
            if ($day_of_week >= 1 && $day_of_week <= 5) {
                $price = get_field('base_price', $resource_id);
                $booking_cost = $price * $data['_duration'];
            }
            // Saturday - Sunday
            elseif ($day_of_week == 6 || $day_of_week == 7) {
                if (get_field('weekend_special_enabled', $resource_id)) {
                    $price = get_field('weekend_special_price', $resource_id);
                    $booking_cost = $price * ($data['_duration']/3);
                } else {
                    $price = get_field('base_price', $resource_id);
                    $booking_cost = $price * $data['_duration'];
                }
            }
        }
    }

    return $booking_cost;
}, 20, 3);




/*
 * When saving a bookable resource, set the featured image
 */
add_action('acf/save_post', 'acf_save_bookable_resource');
function acf_save_bookable_resource($post_id)
{
    if (get_post_type($post_id) != 'bookable_resource') {
        return;
    }

    $images = get_field('images', $post_id, false);

    if ($images) {
        set_post_thumbnail($post_id, $images[0]);
    } else {
        delete_post_thumbnail($post_id);
    }
}

/*
 * Change the archive title for studios
 */
add_filter('get_the_archive_title', function ($title) {
    if (is_post_type_archive('bookable_resource')) {
        $title = 'Studios';
    }

    return $title;
});

/*
 * Custom javascript on the book a studio form
 */
add_action('woocommerce_after_template_part', 'appnet_booking_form_javascript', 20, 4);
function appnet_booking_form_javascript($template_name, $template_path, $located, $args)
{
    if ($template_name != 'booking-form/datetime-picker.php') {
        return;
    } ?>

    <script>
        (function($) {
            $(document).ready(function() {
                //toggle_datepicker_visibility();

                <?php if (isset($_GET['studio'])) { ?>
                    $('#wc_bookings_field_resource').val(<?php echo $_GET['studio']; ?>).trigger('change');
                <?php } ?>
            });

            $('#wc-bookings-booking-form').on('change', '#wc_bookings_field_resource', function() {
                //toggle_datepicker_visibility();
            });

            function toggle_datepicker_visibility() {
                if ($('#wc_bookings_field_resource').val() == 'Select your studio') {
                    $('.picker').css('visibility', 'hidden');
                } else {
                    $('.picker').css('visibility', 'visible');
                }
            }

            let booking_selected_date = false;
            let booking_datepicker = $(".wc-bookings-date-picker").find(".picker:eq(0)");

            $("#wc-bookings-booking-form > fieldset").on('date-selected', function(event, date) {
                booking_selected_date = moment(date).toDate();
            });

            $('#wc-bookings-booking-form').on('change', '#wc_bookings_field_resource', function() {
                if (booking_selected_date !== false) {
                    booking_datepicker.datepicker('setDate', booking_selected_date);
                }
            });

            $(document).on('click', '.ui-datepicker-prev, .ui-datepicker-next', function() {
                booking_selected_date = false;
            });

            $(document).on('ajaxComplete', function(event, xhr, settings) {
                // When the datepicker loads
                if (typeof settings.url !== 'undefined') {
                    if (settings.url.includes('wc_bookings_find_booked_day_blocks')) {
                        if (booking_selected_date !== false) {
                            booking_datepicker.datepicker('setDate', booking_selected_date);
                            $('#wc-bookings-booking-form .ui-state-active').click();
                        }
                    }
                }

                if (typeof settings.data !== 'undefined') {
                    // When start times are loaded
                    // Disable start and end times if no studio selected
                    if (settings.data.includes('wc_bookings_get_blocks')) {
                        if ($('#wc_bookings_field_resource').val() == 'Select your studio') {
                            $('#wc-bookings-form-start-time, #wc-bookings-form-end-time').attr('disabled', 'disabled');
                        } else {
                            $('#wc-bookings-form-start-time, #wc-bookings-form-end-time').removeAttr('disabled');
                        }
                    }

                    // When end times load
                    // Automatically select the first option
                    if (settings.data.includes('wc_bookings_get_end_time_html')) {
                        var options_count = parseInt($('#wc-bookings-form-end-time option').length);

                        if (options_count > 1) {
                            $('#wc-bookings-form-end-time option[value="0"]').remove();
                            $('#wc-bookings-form-end-time').trigger('change');
                        }
                    }
                }
            });
        })(jQuery);
    </script>
<?php }

/*
 * Add price info to resources dropdown
 */
add_filter('woocommerce_bookings_resource_additional_cost_string', function ($additional_cost_string, $resource) {
    $base_price = get_field('base_price', $resource->get_id());
    if ($base_price) {
        $additional_cost_string .= ' | $' . $base_price . '/hr';
    }

    $max_occupancy = get_field('max_occupancy', $resource->get_id());
    if ($max_occupancy) {
        $additional_cost_string .= ' | Max Occupancy - ' . $max_occupancy;
    }

    return $additional_cost_string;
}, 20, 2);

/*
 * Change add to cart text for bookable products
 */
add_filter('woocommerce_booking_single_add_to_cart_text', function ($text) {
    $text = 'Add to Cart';

    return $text;
});

/*
 * Show end time in cart item meta for bookings
 */
add_filter('woocommerce_get_item_data', 'wp_kama_woocommerce_get_item_data_filter', 10, 2);
function wp_kama_woocommerce_get_item_data_filter($item_data, $cart_item)
{
    // appnet_pr($item_data);
    // appnet_pr($cart_item);

    if (isset($cart_item['booking'])) {
        $insert_at = 100;

        foreach ($item_data as $key => &$value) {
            if ($value['name'] == 'Booking Time') {
                $insert_at = $key + 1;
                $value['name'] = 'Start Time';
            }
        }

        $end_timestamp = $cart_item['booking']['_end_date'];

        $new_item_data = [
            'name' => 'End Time',
            'value' => date('g:i a', $end_timestamp),
            'display' => '',
        ];

        $item_data = array_slice($item_data, 0, $insert_at, true)
            + ['End Time' => $new_item_data]
            + array_slice($item_data, $insert_at, null, true);

        // appnet_pr($item_data);
    }

    return $item_data;
}

/*
 * Change the amount of time bookings are allowed to stay in the cart
 */
add_filter('woocommerce_bookings_remove_inactive_cart_time', function ($minutes) {
    return 30;
});

/*
 * Change the date display for bookings in order meta
 */
add_filter('wc_bookings_summary_list_date', function ($booking_date, $booking_start, $booking_end) {
    // appnet_pr($booking_start);
    // appnet_pr($booking_end);
    // $booking_date = date( 'F j, Y', $booking_start );
    $booking_date .= ' - ' . date('g:i a', $booking_end);

    return $booking_date;
}, 10, 3);

/*
 * When a user cancels a booking on the frontend, issue a store credit
 */
add_action('woocommerce_bookings_cancelled_booking', function ($booking_id) {
    if (!function_exists('wc_store_credit_send_credit_to_customer')) {
        return;
    }

    $booking = get_wc_booking($booking_id);
    $order_item_id = $booking->get_order_item_id();

    if (!$order_item_id) {
        return;
    }

    $amount = wc_get_order_item_meta($order_item_id, '_line_total', true);

    $customer = $booking->get_customer();
    // $user_name = $customer->name;
    $user_email = $customer->email;
    // $user_id = $customer->user_id;

    $product_id = $booking->get_product_id();
    if (!$product_id || empty($product_id)) {
        $product_id = 519;
    }

    $args = [
        'expiration' => [
            'number' => 6,
            'period' => 'months',
        ],
        'metas' => [
            'product_ids' => $product_id,
        ],
    ];

    $coupon = wc_store_credit_send_credit_to_customer($user_email, $amount, $args);

    if ($coupon && !is_wp_error($coupon)) {
        $note = 'Credit Issued for Cancelled Booking #' . $booking_id;

        $coupon = new WC_Coupon($coupon->get_id());
        if ($coupon) {
            $coupon->set_description($note);
            $coupon->save();
        }
    }
});

/**
 * Get all booking resources and do something with em, used with WP CLI.
 */
function get_all_booking_resources()
{
    $posts = get_posts([
        'post_type' => 'bookable_resource',
        'posts_per_page' => -1,
        'post_status' => 'any',
    ]);

    foreach ($posts as $post) {
        $post_id = $post->ID;

        // acf_save_bookable_resource($post_id);
    }
}
