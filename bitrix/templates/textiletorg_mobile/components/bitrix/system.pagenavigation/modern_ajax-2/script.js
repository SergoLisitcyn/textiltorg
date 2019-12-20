$(function() {
   $("body").on("click", ".system-page-navigation-modern-ajax .show-more5", function() {
        var url = $(this).attr("data-href");
		var contentParent = $(this).parents(".catalog__content");
        var catalogId = $(contentParent).data("catalog-id");
        $(".system-page-navigation-modern-ajax .loader").show();
        $.get(url,
            {
                AJAX_PAGEN: "Y",
                AJAX_CATALOG_ID: catalogId,
                test: 1
            }, 
            function(data) {
				console.log("test");
				console.log(data);
				$(contentParent).find(".system-page-navigation-modern-ajax").remove();	
				var list = $(contentParent).html();
				$(contentParent).html("");			
				$(contentParent).prepend(data);
				$(contentParent).prepend(list);
                var pagination = $(".pagination", data).html();
                var pagination_url = $(".show-more", data).attr("data-href");/*
                $(".system-page-navigation-modern-ajax .pagination").html(pagination);
                if (typeof pagination_url == "undefined") {
                    $(".system-page-navigation-modern-ajax .show-more").remove();
                } else {
                    $(".system-page-navigation-modern-ajax .show-more").attr("data-href", pagination_url);
                }*/
				$(".system-page-navigation-modern-ajax .loader").hide();
            }
        )
   });
});