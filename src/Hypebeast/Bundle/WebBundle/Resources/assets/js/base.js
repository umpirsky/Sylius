var hypebeast = hypebeast ? hypebeast : {};

hypebeast.initSubNavbarDropdown = function($) {
    var $subnav = $('#site-subnavbar');
    var $dropdown = $('#site-subnavbar-dropdown');
    var timer = null;

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

    $subnav.find('> div > ul > li').bind({
        'mouseenter':function() {
            var $this = $(this);
            var href = $this.find('> a').attr('href');

            if(!href || href.length <= 1 || href.substr(0, 1) !== '#') {
                timer = setTimeout(hideDropdown(), 200);
                return;
            }

            $dropdown.find('.container').hide();
            $dropdown.show();

            $dropdown.find('#'+href.substr(1)).show();
            clearTimeout(timer);
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
}

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