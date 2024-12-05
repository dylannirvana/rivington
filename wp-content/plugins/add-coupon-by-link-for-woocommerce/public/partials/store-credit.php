<table class="store-credit-table woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
		<thead>
			<tr>
                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?php esc_html_e('Store credit code','add-coupon-by-link-woocommerce'); ?></span></th>
                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?php esc_html_e('Credit given','add-coupon-by-link-woocommerce'); ?></span></th>
                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr"><?php esc_html_e('Credit remaining','add-coupon-by-link-woocommerce'); ?></span></th>
                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total"><span class="nobr"><?php esc_html_e('Expiry date','add-coupon-by-link-woocommerce'); ?></span></th>
            </tr>
		</thead>
		<tbody>
            <?php if(!empty($coupons)) { ?>
            <?php foreach($coupons as $coupon_id): 
                $coupon = new \WC_coupon( $coupon_id );
                $amount = get_post_meta($coupon->get_id(), 'coupon_amount', true);
                $code = $coupon->get_code();
                $available = PISOL\ACBLW\ADMIN\StoreCredit::getAmountThatCanBeUsed($coupon);
                $expiry_date = $this->getExpiryDate( $coupon->get_date_expires() );
                $description = $coupon->get_description();
                $expired = self::couponExpired( $coupon );
            ?>
			<tr class=" woocommerce-orders-table__row woocommerce-orders-table__row--status-processing order">
                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" style="padding:20px 20px;" data-title="<?php esc_attr_e('Store credit code','add-coupon-by-link-woocommerce'); ?>">
                    <strong  class="pi-selectable"><?php echo esc_html( $code ); ?></strong> 
                    <?php if(!empty($description)): ?>
                    <span class="pisol-tooltip" title="<?php echo esc_attr($description); ?>">?</span>
                    <?php endif; ?>
                </td>
                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="<?php esc_attr_e('Credit given','add-coupon-by-link-woocommerce'); ?>">
                    <?php echo wp_kses_post( wc_price(get_post_meta($coupon->get_id(), 'coupon_amount', true)) ); ?>
                </td>
                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"  data-title="<?php esc_attr_e('Credit remaining','add-coupon-by-link-woocommerce'); ?>">
                    <?php echo wp_kses_post( wc_price($available) ); ?>
                </td>
                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"  data-title="<?php esc_attr_e('Expiry date','add-coupon-by-link-woocommerce'); ?>">
                    <?php echo esc_html( $expiry_date ? $expiry_date : '-' ); ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php }else{ ?>
                <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-processing order">
                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" style="padding:20px 20px; text-align:center;" data-title="<?php esc_attr_e('You dont have any store credit','add-coupon-by-link-woocommerce'); ?>" colspan="4">
                    <?php esc_html_e('You dont have any store credit','add-coupon-by-link-woocommerce'); ?>
                </td>
                </tr>
            <?php } ?>
        </tbody>
	</table>
    <style>
    .pisol-tooltip {
        display: inline-block;
        vertical-align: middle;
        width: 30px;
        text-align: center;
        margin: 0 !important;
        background: #000;
        color: #fff;
        border-radius: 30px;
    }
</style>