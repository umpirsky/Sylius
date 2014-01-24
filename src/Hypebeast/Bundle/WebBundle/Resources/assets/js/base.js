var hypebeast = hypebeast ? hypebeast : {};

hypebeast.initSubNavbarDropdown = function($) {
    var $subnav = $('#site-subnavbar');
    var $dropdown = $('#site-subnavbar-dropdown');
    var timer = null;

    /**
     * Group menu item into columns
     */
    $dropdown.find('ul').each(function() {
        var $lis = $(this).find('> li');

        // unwrap <li> from the original <ul>
        $lis.appendTo($(this).parent());
        $(this).remove();

        // wrap every 4 <li>
        for(var i = 0; i < $lis.length; i+=5) {
            $lis.slice(i, i+5).wrapAll('<ul class="nav nav-list pull-left"></ul>');
        }
    });

    /**
     * Dropdown function
     */
    $subnav.find('> div > ul > li').bind({
        'mouseenter':function() {
            var $this = $(this);
            var target = $this.find('> a').data('target');

            clearTimeout(timer);

            if(!target) {
                timer = setTimeout(hideDropdown(), 200);
                return;
            }

            $dropdown.find('.container').hide();
            $dropdown.show();

            $dropdown.find(target).show();
        },
        'mouseleave': function() {
            timer = setTimeout(hideDropdown, 200);
        }
    });

    $dropdown.bind({
        'mouseenter': function() {
            clearTimeout(timer);
        },
        'mouseleave': function() {
            timer = setTimeout(hideDropdown, 200);
        }
    });

    function hideDropdown() {
        $dropdown.find('.container').hide();
    }
};

hypebeast.initSidebarFilter = function($) {
    var $filter = $('#sidebar-filter');
    var uri = new Uri(document.location);
    var params = {};

    try {
        params = $.parseJSON(decodeURIComponent(uri.getQueryParamValue('filter')));
    } catch (e) {

    }

    $filter.find('li').each(function() {
        var $li = $(this);
        var field = $li.closest('.section').data('field');
        var query = $li.data('query') || $li.text();
        var stringifiedQuery = JSON.stringify(query);

        // check selected item
        if(params[field]) {
            for(var i in params[field]) {
                if(stringifiedQuery === JSON.stringify(params[field][i])) {
                    $li.addClass('selected');
                }
            }
        }

        $li.click(function() {
            params[field] = params[field] ? $.grep(params[field], function(value) {
                return JSON.stringify(value) !== stringifiedQuery;
            }) : [];

            if($li.hasClass('selected') === false) {
                params[field].push($li.data('query')||$li.text());
            }

            $li.toggleClass('selected');

            $('#container').fadeTo(null,.5);
            document.location = uri.replaceQueryParam('filter', JSON.stringify(params)).toString();
        })
    });
};

$(function() {
    hypebeast.body = $("body");

    // start initial the page
    for(method in hypebeast) {
        // skip if not start with _init
        if(method.toString().substring(0,4) !== 'init') continue;

        // call method
        hypebeast[method.toString()](jQuery);
    }

    for(method in hypebeast.pageFunctions) {
        hypebeast.pageFunctions[method](jQuery);
    }
});