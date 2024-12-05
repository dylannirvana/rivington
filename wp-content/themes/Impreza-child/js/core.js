(function($) {
    const main = $(document).find('body'); 
    const spanElement = $('<span>', {
        id: 'partial-date-subtitle', // Set the ID
        text: '' // Optional text content
    });
    main.find('#wc-bookings-booking-form .wc-bookings-date-picker').append(spanElement);

    // Handle click and touchstart events
    main.on('click touchstart', '.wc-bookings-booking-form .ui-datepicker-calendar td a', function(e){
        console.log('Partial Booked Title');
        const a = $(this);
        const td = a.parent(); // Get parent <td>
        
        // Check if the <td> has 'partial_booked' class
        if(td.hasClass('partial_booked')){
            const title = td.attr('title'); // Get the title attribute
            spanElement.html(title).addClass('active');
            console.log(title);
        } else {
            spanElement.removeClass('active');
        }
    });

    // Handle hover (mouseenter) event
    main.on('mouseenter', '.wc-bookings-booking-form .ui-datepicker-calendar td a', function(e){
        console.log('Partial Booked Title');
        const a = $(this);
        const td = a.parent(); // Get parent <td>
        
        // Check if the <td> has 'partial_booked' class
        if(td.hasClass('partial_booked')){
            const title = td.attr('title'); // Get the title attribute
            spanElement.html(title).addClass('hover-active');
            console.log(title);
        } else {
            spanElement.removeClass('hover-active');
        }
    });

})(jQuery);