$(document).ready(function() {
    'use strict';

    var typeheadSelector = 'input[name^="sylius_inventory_adjustment[adjustmentChanges]"][name$="[typehead]"]';
    var createTypehead = function(element) {
        element.typeahead({
            name: 'variants',
            remote: {
                url: inventory_update_variants_path+'?q=%QUERY',
                cache: false
            },
            template: '<span>{{sku}} - {{name}}</span> <span class="label label-success">{{onHand}}</span>',
            engine: Hogan,
            limit: 5
        }).bind('typeahead:selected', function(e, variant) {
            var container = element.closest('td');
            container.find('input[type="hidden"]').val(variant.id);
            container.next().html(variant.supplierCode);
            container.nextAll().eq(2).html(variant.onHand);
            container.nextAll().eq(1).find('input').trigger('change');
        });
    };
    createTypehead($(typeheadSelector));

    $(document).on('click', 'a[data-collection-button="add"]', function(e) {
        createTypehead($(typeheadSelector + ':last'));
    });

    $(document).on('change', 'input[name^="sylius_inventory_adjustment[adjustmentChanges]"][name$="[quantity]"]', function (e) {
        var available = $(this).parent().next();
        var after = available.next();
        after.html(parseInt($(this).val()) + parseInt(available.html()));
    });
});
