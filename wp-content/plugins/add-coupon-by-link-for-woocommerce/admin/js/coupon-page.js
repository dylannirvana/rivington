(function ($) {
	'use strict';
    jQuery(document).ready(function($) {
        function dynamicFields() {
            jQuery(".pisol-attribute").selectWoo({
                width: '100%',
                closeOnSelect: false,
                placeholder: 'Select an attribute',
                cache: true,
                ajax: {
                    url: window.ajaxurl,
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            term: params.term,
                            action: "pisol_get_attribute",
                            _nonce: jQuery(this).data('nonce')
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };

                    },
                },
                minimumInputLength: 3
            });
        }
        dynamicFields();

        jQuery(document).on('click', '#send_store_credit_email', function(e) {
            e.preventDefault();
            var href = jQuery(this).attr('href');
            jQuery.ajax(
                {
                    url: href,
                    type: 'GET',
                    success: function (response) {

                        alert(response.data);
                    }
                }
            )
        });
    });



})(jQuery);