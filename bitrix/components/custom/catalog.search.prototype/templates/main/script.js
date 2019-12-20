$(function(){
    var getTimer;

    function search() {
        clearTimeout(getTimer);

        getTimer = setTimeout(function(){
            var options = $('#search-form').serialize() + '&AJAX_SEARCH=Y';

            $.get(location.href, options, function(data) {

                if (data.length < 200) {
                    data = '<ul> <li style = "text-align: center;"> Товаров не найдено </li> </ul>';
                }



                $("#results").html(data);
				$("#results").show();
				$(".popup-overlay").show();
				$('body').click(function(e) {
					if (!$(e.target).closest('.seares').length){
						$("#results").hide();
                        $(".popup-overlay").hide();
					}
				});
            });
        }, 500);

        return false;
    }

    //$('#search-form input[NAME="QUERY"]').change(search);
    $('#search-form input[NAME="QUERY"]').keyup(search);
});