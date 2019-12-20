$(document).ready(function() {

    $("#delect-region-search input").keyup(function() {
        var str = $(this).val();
        var no_result = false;
        if (str.length > 2) {
            no_result = true;
            $("#delect-region-search .city").each(function() {
                var city = $(this).text();
                var pattern = new RegExp(str, "i");
                var result = city.match(pattern);
                if (result != null) {
                    $(this).show();
                    city = city.replace(result[0], '<span class="red">'+result[0]+'</span>');
                    no_result = false;
                } else {
                    $(this).hide();
                }
                $(this).html(city);
            });
        } else {
            $("#delect-region-search .city").show();
        }
        
        if (no_result) {
            $("#delect-region-search .no-result").show();
        } else {
            $("#delect-region-search .no-result").hide();
        }

    });
    $(".sity.select-region-container .select-city").click(function() {
        $("#delect-region-search").toggleClass("open");
		$(".search").removeClass("open");
		$(".seares").hide();
		$("#wrapper-header-search").hide();
    });
    $("#delect-region-search .city").click(function() {
        document.location.href = $(this).attr("data-url");
    });
    $("#delect-region-search .close").click(function() {
        $("#delect-region-search").removeClass("open");
    });
});