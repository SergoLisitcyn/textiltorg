$(function(){
    var getTimer;

    function search() {
        clearTimeout(getTimer);

        getTimer = setTimeout(function(){
            var options = $('#search-form').serialize() + '&AJAX_SEARCH=Y';

            $.get(location.href, options, function(data) {
                $("#results").html(data);
				$("#results").show();
				$('body').click(function(e) {
					if (!$(e.target).closest('.seares').length){
						$("#results").hide();
					}
				});
            });
        }, 500);

        return false;
    }

    $('#search-form input[NAME="QUERY"]').change(search);
    $('#search-form input[NAME="QUERY"]').keyup(search);
});