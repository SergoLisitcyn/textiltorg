$(function() {
    BX.addCustomEvent(window, 'OnCompareChange', function(){
        $.get(location.href, {'ajax_action_compare': 'Y'}, function(data) {
            var box = $('#box-compare-right');

            if (box.length)
                 box.html(data);
        });
    });
});