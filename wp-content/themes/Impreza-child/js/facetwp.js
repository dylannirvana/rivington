(function($) {
    var header_height = $('.l-header').height();
    if ( $('#wpadminbar').length ) {
        header_height = header_height + $('#wpadminbar').height();
    }

    $(document).ready(function() {
        FWP.hooks.addAction('facetwp_map/marker/mouseover', function( marker ) {
            google.maps.event.trigger(marker, 'click'); 
        });
    
        FWP.hooks.addAction('facetwp_map/marker/mouseout', function( marker ) {
            //FWP_MAP.infoWindow.close();
        });

        $(document).on('mouseover', '.local-location', function() {
            var markers = FWP_MAP.get_post_markers($(this).data('id'));
            google.maps.event.trigger(markers[0], 'spider_click'); 
            FWP_MAP.map.setCenter(markers[0].getPosition());
        });

        $(document).on('mouseout', '.local-location', function() {
            FWP_MAP.infoWindow.close();
        });
    });

    $(document).on('facetwp-refresh', function() {
        if ( FWP.loaded ) {
            $('.facetwp-template').html('<div style="width:2.8rem;"><div class="g-preloader type_1"><div></div></div></div>');
        }
    });

    $(document).on('click', '.facetwp-page, .woocommerce-pagination .page-numbers', function() {
        $('html, body').animate({
            scrollTop: $('.facetwp-template').offset().top - header_height - 15
        }, 500);
    });

    $(document).on('click', '.facetwp-reset', function() {
        FWP.reset();
    });
})(jQuery);
