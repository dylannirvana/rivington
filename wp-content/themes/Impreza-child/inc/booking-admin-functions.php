<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * In the admin booking calendar view, remove bookings that end at 12am from showing on the next day and sort based on resource ID
 */
add_filter( 'woocommerce_bookings_in_date_range_query', function($booking_ids) {
    if ( isset($_GET['page']) && $_GET['page'] == 'booking_calendar' ) {
        if ( isset($_GET['calendar_day']) ) {
            $date = $_GET['calendar_day'];
        } else {
            $date = date('Y-m-d', time());
        }

        /**
         * Make this a global variable so we can access it later
         */
        global $booking_ids_array;

        $booking_ids_array = [];

        foreach ( $booking_ids as $key => $booking_id ) {
            $booking = get_wc_booking($booking_id);
            $start_timestamp = $booking->get_start();
            $start_date = date('Y-m-d', $start_timestamp); 
            
            /**
             * This is a booking that ended at midnight from the previous day
             */
            if ( $start_date !== $date) {
                unset($booking_ids[$key]);
                continue;
            }

            /**
             * Sort by resource menu_order
             */
            $resource = $booking->get_resource();

            if ( $resource ) {
                $booking_ids_array[$booking_id] = $resource->get_sort_order();
            } else {
                $booking_ids_array[$booking_id] = PHP_INT_MAX;
            }
        }

        asort($booking_ids_array);
        $booking_ids = array_keys($booking_ids_array);
    }
    
    return $booking_ids;
} );


/**
 * Color code stuff in the bookings calendar
 */
add_action('admin_footer', function() {
    if ( isset($_GET['page']) && $_GET['page'] == 'booking_calendar' ) {
        $posts = get_posts(array(
            'post_type' => 'bookable_resource',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids'
        ));

        $colors = array();

        foreach ($posts as $post_id) {
            $color = get_field('calendar_color', $post_id);
            if ( $color ) {
                $colors[get_the_title($post_id)] = $color;
            }
        } ?>

        <style>
            <?php foreach ( $colors as $key => $value ) { ?>
                .daily_view_booking[data-booking-resource="<?php echo $key; ?>"] {
                    background: <?php echo $value; ?> !important;
                }
            <?php } ?>
        </style>

        <script>
            (function($) {
                $('.daily_view_booking').each(function() {
                    var self = $(this);

                    if ( self.data('booking-resource') ) {
                        //var title = self.attr('data-booking-title');
                        //self.attr('data-booking-title', self.data('booking-resource') + ' - ' + title);
                        self.attr('data-booking-title',self.data('booking-resource'));
                    }
                });
            })(jQuery);
        </script>

    <?php }
});


/**
 * Add flagged user info to bookings in the calendar view
 */
add_action('admin_footer', function() {
    if ( isset($_GET['page']) && $_GET['page'] == 'booking_calendar' ) { ?>
        <script type="text/javascript">
            (function($) {

                <?php
                /**
                 * Global variable defined earlier
                 */
                global $booking_ids_array;

                if ( !empty($booking_ids_array) ) {
                    $booking_ids = array_keys($booking_ids_array);

                    foreach ( $booking_ids as $booking_id ) {
                        $booking = get_wc_booking($booking_id);
                        $user_id = $booking->get_customer_id();
    
                        if ( $user_id ) {
                            if ( get_field('flagged', 'user_' . $user_id) ) {
                                $flagged_reason = get_field('flagged_reason', 'user_' . $user_id);
            
                                if ( $flagged_reason ) { ?>
                                    $('.daily_view_booking[data-booking-id="<?php echo $booking_id; ?>"]').append('<span class="flagged-user-icon tips dashicons dashicons-info" data-tip="<?php echo htmlspecialchars($flagged_reason); ?>" style="position:absolute;left:5px;bottom:5px;"></span>');
                                <?php }
                            }
                        }
                    } ?>
    
                    jQuery(document).ready(function($) {
                        $('.flagged-user-icon').tipTip({
                            'attribute': 'data-tip',
                            'fadeIn':    50,
                            'fadeOut':   50,
                            'delay':     200
                        });
                    });

                <?php } ?>

            })(jQuery);
        </script>
    <?php }
});


/**
 * Edit the Admin Columns pro column on bookings to show a flagged user
 */
add_filter('ac/column/value', function ($value, $id, $column) {
    if ($column instanceof AC\Column\CustomField) {
        if ( $column->get_type() == 'column-meta' ) {
            if ( $column->get_meta_key() == '_booking_customer_id' ) {
                $user_id = $column->get_raw_value($id);

                if ( get_field('flagged', 'user_' . $user_id) ) {
                    //$value .= '<div class="flagged-user"></div>';

                    $flagged_reason = get_field('flagged_reason', 'user_' . $user_id);
                    if ( $flagged_reason ) {
                        $value .= '<span class="flagged-user-icon tips dashicons dashicons-info" data-tip="' . htmlspecialchars($flagged_reason) . '"></span>';
                    }
                }
            }
        }
    }

    if ($column instanceof ACA\ACF\Column) {
        if ( $column->get_meta_key() == 'flagged' ) {
            if ( $column->get_raw_value($id) ) {
                $value = '<span class="dashicons dashicons-info" style="color:#a00;"></span>';
            } else {
                $value = '';
            }
        }
    }
    
    return $value;
}, 10, 3);


/**
 * Add some styling and scripts to a flagged user in bookings list view
 */
add_action('admin_footer', function() { 
    global $pagenow;

    if ( $pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'wc_booking' ) { ?>
        <style>
            tr.type-wc_booking .flagged-user-icon {
                color:#a00;
                margin-top:-1px;
                margin-left:5px;
            }
        </style>

        <script>
            jQuery(document).ready(function($) {
                $('.flagged-user-icon').tipTip({
                    'attribute': 'data-tip',
                    'fadeIn':    50,
                    'fadeOut':   50,
                    'delay':     200
                });
            });
        </script>

    <?php }
});


/**
 * Add some meta in backend for bookings from old site info we imported and if the user is flagged
 */
add_action('woocommerce_admin_booking_data_after_booking_details', function($post_id) {
    $old_id = get_post_meta($post_id, 'old_id', true);
    $note = get_post_meta($post_id, 'note', true);
    $note_2 = get_post_meta($post_id, 'note_2', true);

    if ( $old_id || $note || $note_2 ) { ?>
        <p class="form-field form-field-wide">
            <label>Old Site Info:</label>
            <?php if ( $old_id ) {
                echo 'ID: ' . $old_id . '<br>';
            } ?>
            <?php if ( $note ) {
                echo 'Summary: ' . $note . '<br>';
            } ?>
            <?php if ( $note_2 ) {
                echo 'Description: ' . $note_2;
            } ?>
        </p>
    <?php }

    $user_id = get_post_meta($post_id, '_booking_customer_id', true);
    if ( $user_id ) {
        if ( get_field('flagged', 'user_' . $user_id) ) { ?>
            <p>User is Flagged <span class="dashicons dashicons-info" style="color:#a00;"></span></p>

            <?php $flagged_reason = get_field('flagged_reason', 'user_' . $user_id);
            if ( $flagged_reason ) {
                echo '<p>' . $flagged_reason . '</p>';
            }
        }
    }
});
