/* global acblw_wallet_admin_order_param, woocommerce_admin_meta_boxes, accounting, woocommerce_admin */

jQuery(function ($) {
    var woo_store_credits_items = {
        init: function () {
            if (acblw_wallet_admin_order_param.is_refundable) {
                $('.refund-actions .do-manual-refund').before('<button type="button" class="button button-primary do-store-credit-refund">' + acblw_wallet_admin_order_param.i18n.refund + ' <span class="wc-order-refund-amount">' + acblw_wallet_admin_order_param.default_price + '</span> ' + acblw_wallet_admin_order_param.i18n.via_wallet + '</button>');
                $('#woocommerce-order-items').on('click', '.refund-actions .do-store-credit-refund', this.do_store_credit_refund);
            }
        },
        
        do_store_credit_refund: function () {
            woo_store_credits_items.block();
            if (window.confirm(woocommerce_admin_meta_boxes.i18n_do_refund)) {
                var refund_amount = $('input#refund_amount').val();
                var refund_reason = $('input#refund_reason').val();
                var refunded_amount = $( 'input#refunded_amount' ).val();
                // Get line item refunds
                var line_item_qtys = {};
                var line_item_totals = {};
                var line_item_tax_totals = {};
                $('.refund input.refund_order_item_qty').each(function (index, item) {
                    if ($(item).closest('tr').data('order_item_id')) {
                        if (item.value) {
                            line_item_qtys[ $(item).closest('tr').data('order_item_id') ] = item.value;
                        }
                    }
                });

                $('.refund input.refund_line_total').each(function (index, item) {
                    if ($(item).closest('tr').data('order_item_id')) {
                        line_item_totals[ $(item).closest('tr').data('order_item_id') ] = accounting.unformat(item.value, woocommerce_admin.mon_decimal_point);
                    }
                });

                $('.refund input.refund_line_tax').each(function (index, item) {
                    if ($(item).closest('tr').data('order_item_id')) {
                        var tax_id = $(item).data('tax_id');

                        if (!line_item_tax_totals[ $(item).closest('tr').data('order_item_id') ]) {
                            line_item_tax_totals[ $(item).closest('tr').data('order_item_id') ] = {};
                        }

                        line_item_tax_totals[ $(item).closest('tr').data('order_item_id') ][ tax_id ] = accounting.unformat(item.value, woocommerce_admin.mon_decimal_point);
                    }
                });
                var data = {
                    action: 'woo_store_credit_refund',
                    order_id              : woocommerce_admin_meta_boxes.post_id,
                    refund_amount         : refund_amount,
                    refunded_amount       : refunded_amount,
                    refund_reason         : refund_reason,
                    line_item_qtys        : JSON.stringify(line_item_qtys, null, ''),
                    line_item_totals      : JSON.stringify(line_item_totals, null, ''),
                    line_item_tax_totals  : JSON.stringify(line_item_tax_totals, null, ''),
                    api_refund            : $(this).is('.do-api-refund'),
                    restock_refunded_items: $('#restock_refunded_items:checked').length ? 'true' : 'false',
                    security              : woocommerce_admin_meta_boxes.order_item_nonce
                };
                $.post(woocommerce_admin_meta_boxes.ajax_url, data, function (response) {
                    if (true === response.success) {
                        woo_store_credits_items.reload_items();
                        window.location.href = window.location.href;
                    } else {
                        window.alert(response.data.error);
                        woo_store_credits_items.unblock();
                    }
                });
            } else {
                woo_store_credits_items.unblock();
            }
        },
        block: function () {
            $('#woocommerce-order-items').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
        },
        unblock: function () {
            $('#woocommerce-order-items').unblock();
        },
        reload_items: function () {
            var data = {
                order_id: woocommerce_admin_meta_boxes.post_id,
                action: 'woocommerce_load_order_items',
                security: woocommerce_admin_meta_boxes.order_item_nonce
            };

            woo_store_credits_items.block();

            $.ajax({
                url: woocommerce_admin_meta_boxes.ajax_url,
                data: data,
                type: 'POST',
                success: function (response) {
                    $('#woocommerce-order-items').find('.inside').empty();
                    $('#woocommerce-order-items').find('.inside').append(response);
                    woo_store_credits_items.init_tiptip();
                    woo_store_credits_items.unblock();
                    woo_store_credits_items.init();
                }
            });
        },
        init_tiptip: function () {
            $('#tiptip_holder').removeAttr('style');
            $('#tiptip_arrow').removeAttr('style');
            $('.tips').tipTip({
                'attribute': 'data-tip',
                'fadeIn': 50,
                'fadeOut': 50,
                'delay': 200
            });
        }
    };
    woo_store_credits_items.init();
});