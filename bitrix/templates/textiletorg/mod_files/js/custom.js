var frontBaseHref = '//www.textiletorg.ru/';

$(function() {
    function FixedHeader()
    {
        var w = $(window),
            h = $('.h_top-block'),
            p = $('#bx-panel'),
            o = 0;

        if (p.length)
            o += p.innerHeight();

        if (w.scrollTop() > o) {
            h.addClass('fixed');
        } else {
            h.removeClass('fixed');
        }
    }

    $(window).scroll(function() {
        FixedHeader();
    });

    $(window).resize(function() {
        FixedHeader();
    });

    FixedHeader();

    SyntaxHighlighter.all();
    $("a.gallery, a.iframe").fancybox();

    $('a.box_consult').click(function() {
        $('.lt-online').click();
        return false;
    });
});
