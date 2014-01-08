;(function ( $ ) {
    'use strict';

    $(document).ready(function() {

        $('.print-packing-slips.btn').click(function(event) {
            event.preventDefault();
            var $btn = $(this);
            var orderIds = [];

            $('input.line-identifier:checked').each(function() {
                orderIds.push($(this).data('value'));
                $(this).closest('tr').fadeTo(null,.5);
            });

            if(orderIds.length === 0) {
                alert('Please select an order first.')
            } else {
                window.open(
                    $btn.attr('href') + '?orders=' + orderIds.join(),
                    'slips',
                    'width=1000, height=600'
                );
            }
        });

    });
})( jQuery );
