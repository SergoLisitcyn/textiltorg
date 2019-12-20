$(document).ready(function() {
    $('a[href="#as-stores-page-hide"]').click(function() {
        if (!$(this).hasClass('open'))
        {
            $('#as-stores-page-hide').removeClass('hide');
            $(this).text('Свернуть').addClass('open');
        }
        else
        {
            $('#as-stores-page-hide').addClass('hide');
            $(this).text('Подробнее').removeClass('open');
        }

        return false;
    });
});