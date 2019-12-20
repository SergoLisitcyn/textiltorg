$(document).ready(function() {
    $('#button_geo_yes').click(function() {
        $('#popup_geo').addClass('hide');
        $.get(location.href, {IS_GEO_CONFIRM: 'Y'});
    });

    $('#button_geo_no').click(function() {
        show_hide_geo(1);
        $('#popup_geo').addClass('hide');
    });

});

function show_hide_geo(sh){
    $.fancybox('#win_choose_region', {
        maxWidth    : 1170,
        maxHeight   : 600,
        fitToView   : false,
        autoSize    : true,
        closeClick  : false,
        openEffect  : 'none',
        closeEffect : 'none',
    });
}