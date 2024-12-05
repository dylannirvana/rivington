<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Hide a bunch of stuff on user profiles in admin
 */
function hide_user_info_admin() { ?>
    <style>
        .user-url-wrap,
        .user-description-wrap,
        .application-passwords,
        .user-display-name-wrap,
        .user-profile-picture .description {
            display:none;
        }
    </style>

    <script>
        jQuery(document).ready(function($) {
            $('h2:contains("Personal Options")').hide().next('table').hide();
            $('h2:contains("About the user")').hide();
            $('h2:contains("About Yourself")').hide().next('table').hide();
            //$('h2:contains("Customer billing address")').hide().next('table').hide();
            $('h2:contains("Customer shipping address")').hide().next('table').hide();
        });
    </script>
    
<?php }
add_action('show_user_profile', 'hide_user_info_admin');
add_action('edit_user_profile', 'hide_user_info_admin');
