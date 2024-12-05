<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

do_action( 'woocommerce_email_header', $email_heading, null ); ?>

<p><?php echo wp_kses_post( $content ); ?></p>

<h4 style="text-align: center; font-weight:bold"><?php echo wp_kses_post( $coupon_code ); ?></h4>

<?php if ( ! empty( $desc ) ) { ?>
    <p><?php echo wp_kses_post( $desc ); ?></p>
<?php } ?>

<?php do_action( 'woocommerce_email_footer', null ); ?>
