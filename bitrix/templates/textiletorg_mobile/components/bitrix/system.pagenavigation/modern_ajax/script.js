$(function() {
   $(".system-page-navigation-modern-ajax .show-more").click(function() {
        var url = $(this).attr("data-href");
        $(".system-page-navigation-modern-ajax .loader").show();
        $.get(url,
            {
                AJAX_PAGEN: "Y",
                test: "1",
            }, 
            function(data) {
				console.log("test");
				console.log(data);
				var list = $(".content_block > #catalog-items").html();
				$(".content_block > #catalog-sort").remove();
				$(".content_block > .clear").remove();
				$(".content_block > #catalog-items").remove();
				$(".content_block > .system-page-navigation-modern-ajax").remove();				
				$(".content_block").prepend(data);
				$(".content_block > #catalog-items .filtre_block").remove();
				$(".content_block > #catalog-items").prepend(list);
                var pagination = $(".pagination", data).html();
                var pagination_url = $(".show-more", data).attr("data-href");
                
                $(".shop_item", data).each(function() {
                    $("#catalog-items").append('<div class="shop_item">'+$(this).html()+'</div>');
                });
                $(".system-page-navigation-modern-ajax .pagination").html(pagination);
                
                
                if (typeof pagination_url == "undefined")
                {
                    $(".system-page-navigation-modern-ajax .show-more").remove();
                }
                else
                {
                    $(".system-page-navigation-modern-ajax .show-more").attr("data-href", pagination_url);
                }

                $(".system-page-navigation-modern-ajax .loader").hide();
				
					$('.img_list_block').slick('unslick');
            }
        )
   });
});