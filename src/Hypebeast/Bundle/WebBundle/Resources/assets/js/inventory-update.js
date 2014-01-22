var app = angular.module('hypebeast',[]);

app.config(function($interpolateProvider){
    $interpolateProvider.startSymbol('#{').endSymbol('}');
});

app.controller('StockAdjustment', ['$scope', function(scope) {
    scope.items = window.formData || [];

    scope.addItem = function(variant) {
        // Check for duplication
        for(var i in scope.items) {
            if (scope.items[i]['id'] == variant.id) {
                alert('Item already added.');
                return;
            }
        }

        variant.quantity = 1;
        scope.items.push(variant);
    };

    scope.removeItem = function(index) {
        scope.items.splice(index, 1);
    };
}]);

/**
 * Set quantity min and max according to adjustment reason
 */
app.directive('stockAdjustmentQuantity', function() {
    return function(scope, $el) {
        scope.$watch('reason', function(value) {
            switch (value) {
                case 'Other':
                    $el.removeAttr('min').removeAttr('max');
                    break;
                case 'Damaged':
                case 'Borrowed for Shooting':
                    $el.removeAttr('min').attr('max', -1);
                    break;
                default:
                    $el.removeAttr('max').attr('min', 1);
                    break;
            }
        })
    };
});

/**
 * Variant autocomplete
 */
app.directive('variantTypeahead', function() {
    return function(scope, $el) {
        $el.typeahead({
            name: 'variants',
            remote: {
                url: inventory_update_variants_path,
                cache: false
            },
            template: function(datum) {
                return '<span><code>'+datum.sku+'</code> '+datum.brand+' '+datum.name+'</span>' +
                    (datum.option?'('+datum.option+')':'')
                    ;
            },
            limit: 10
        });

        $el.bind('typeahead:selected typeahead:autocompleted', function(event, datum) {
            scope.$apply(function() {
                scope.addItem(datum);
            });

            // Empty the †ext field
            $el.typeahead('setQuery', '');
        });
    };
});

$(function() {
    /**
     * Disable mousewheel on input box.
     */
    $('body').on('mousewheel', 'input', function(event){
        event.preventDefault();
    });
});