(function ( $ ) {
    'use strict';

    $(document).ready(function() {
        $('input[data-autocomplete-source]').each(autoFunc)
        deleteFunc($(document));
    });

    var autoFunc = function() {
        var $field = $(this);
        var prototype = $('[data-prototype]', $field.parent()).html();

        $field.autocomplete({
            source: $field.attr('data-autocomplete-source'),
            minLength: 2,
            select: function (event, ui) {
                var item = ui.item;
                var proto = prototype;
                item['hash'] = '';
                for (var i in item) {
                    var reg = new RegExp('%' + i + '%', 'g');
                    proto = proto.replace(reg, item[i]);
                }
                if (0 == $('[data-autocomplete-item='+ item['value'] +']', $field.parent()).length) {
                    var $proto = $(proto.trim());
                    deleteFunc($proto);
                    $('[data-autocomplete-target]', $field.parent()).append($proto);
                }
            },
            close: function(event, ui) {
                $field.val('');
            }
        });

        $field.next().hide();
    };

    var deleteFunc = function(scope) {
        $('[data-autocomplete-delete]', scope).click(function (e) {
            e.preventDefault();
            var $item = $(this).parents('[data-autocomplete-item]');
            $item.remove();
        })
    }

})( jQuery );
