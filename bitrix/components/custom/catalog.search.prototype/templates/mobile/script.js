$(function(){
    var getTimer;

    function search() {
        clearTimeout(getTimer);

        getTimer = setTimeout(function(){
            var options = $('#search-form').serialize() + '&AJAX_SEARCH=Y';

            $.get(location.href, options, function(data) {
                $("#results").html(data);
                $(".seares").show();
                $("#searchresults .word").text($('#search-form').val());
            });
        }, 500);

        return false;
    }

    $('#search-form input[NAME="QUERY"]').change(search);
    $('#search-form input[NAME="QUERY"]').keyup(search);
});