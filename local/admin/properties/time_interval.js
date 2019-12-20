jQuery.fn.ForceNumericOnly = function()
{
    return this.each(function()
    {
        $(this).keydown(function(e)
        {
            var key = e.charCode || e.keyCode || 0;
            // Разрешаем backspace, tab, delete, стрелки, обычные цифры и цифры на дополнительной клавиатуре
            return (
                key == 8 ||
                key == 9 ||
                key == 46 ||
                (key >= 37 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });
    });
};

$(function() {
    $('.time-interval input[type="text"]').ForceNumericOnly();

    $('.time-interval input[type="text"]').keyup(function(e) {
        var container = $(this).parents('td'),
            sh = container.find('input.start-hour').val(),
            sm = container.find('input.start-min').val(),
            eh = container.find('input.end-hour').val(),
            em = container.find('input.end-min').val();

        sh = (sh) ? parseInt(sh): 0;
        sm = (sm) ? parseInt(sm): 0;
        eh = (eh) ? parseInt(eh): 0;
        em = (em) ? parseInt(em): 0;

        container.find('input.value').val(sh + ':' + sm + '||' + eh + ':' + em);
    });
});