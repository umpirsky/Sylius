;(function ( $ ) {
    'use strict';

    $(document).ready(function() {
        $('[data-transform-target]').click(function() {
            var elems = $(this).attr('data-transform-target').split('|')
            var attr  = $(this).attr('data-transform-attr');

            for (var key in elems) {
                var elem = elems[key];

                if (attr) {
                    attr = attr.split('|');
                    for (var key2 in attr) {
                        var attrs = attr[key2].split('=');
                        if (attrs.length < 2) {
                            $(elem).attr(attrs[0], '');
                        } else {
                            $(elem).attr(attrs[0], attrs[1]);
                        }
                    }
                }

                var attr = $(this).attr('data-transform-submit');
                if (typeof attr !== 'undefined' && attr !== false) {
                    $(elem).parents('form').submit();
                }
            }
        }).change(function() {
            var elems = $(this).attr('data-transform-target').split('|')

            for (var key in elems) {
                var elem = elems[key];
                
                var checked = $(this).prop('checked');
                $(elem).each(function() {
                    $(this).attr('checked', checked);
                });
            }
        });
    });
})( jQuery );
