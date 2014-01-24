(function ($) {
    "use strict";

    var Price = function (options) {
        this.init('price', options, Price.defaults);
    };

    $.fn.editableutils.inherit(Price, $.fn.editabletypes.abstractinput);

    $.extend(Price.prototype, {
        render: function() {
           this.$input = this.$tpl.find('input');
        },

        value2html: function(value, element) {
            if(!value) {
                $(element).empty();
                return;
            }

            $.get($(element).data('price-url'), function(data) {
                $(element).html(data);
            })
        },

       value2input: function(value) {
           if(!value) {
             return;
           }

           this.$input.filter('[name="sylius_product_price[masterVariant][price]"]').val(value.masterVariant.price);
           this.$input.filter('[name="sylius_product_price[masterVariant][salePrice]"]').val(value.masterVariant.salePrice);
           this.$input.filter('[name="sylius_product_price[masterVariant][wholesalePrice]"]').val(value.masterVariant.wholesalePrice);
       },

       input2value: function() {
            return {
                masterVariant: {
                    price: this.$input.filter('[name="sylius_product_price[masterVariant][price]"]').val(),
                    salePrice: this.$input.filter('[name="sylius_product_price[masterVariant][salePrice]"]').val(),
                    wholesalePrice: this.$input.filter('[name="sylius_product_price[masterVariant][wholesalePrice]"]').val()
                }
           };
       },

       activate: function() {
            this.$input.filter('[name="sylius_product_price[masterVariant][price]"]').focus();
       },

       autosubmit: function() {
           this.$input.keydown(function (e) {
                if (e.which === 13) {
                    $(this).closest('form').submit();
                }
           });
       }
    });

    Price.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
        tpl: sylius_xeditable_price_tpl.trim(),
        inputclass: ''
    });

    $.fn.editabletypes.price = Price;
}(window.jQuery));
