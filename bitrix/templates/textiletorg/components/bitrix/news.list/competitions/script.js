$(function() {
    $(".pagi.ajax .show-more").click(function() {
        $('.konkurs-list.archive').addClass('show');
        $(this).parents('.pagi').remove();
    });
});