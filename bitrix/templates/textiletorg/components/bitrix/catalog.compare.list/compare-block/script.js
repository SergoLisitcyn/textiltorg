$(function() {
    BX.addCustomEvent(window, 'OnCompareChange', function(){
        $.get(location.href, {'ajax_action_compare_block': 'Y'}, function(data) {
            var box = $('#box-compare-block');

            if (box.length)
                 box.html(data);
        });
    });

    $('body').on('click', '#clear-all-compare', function() {
        var href = $(this).attr('href');

        $(this).parents('#static-block').remove();

        $.get(href, function() {
            BX.onCustomEvent(window, 'OnCompareChange');
        });

        return false;
    });
});