$(function() {
	var prev_pagination = '';
    $(".show-more").click(function() {
        var url = $(this).attr("data-href");
        $(".ajax-pagination .loader").show();
        $(".grid-list .itemgrid.last-link").addClass("load");
        $.get(url,
            {
                AJAX_PAGEN: "Y"
            },
            function(data) {
                $(".grid-list .itemgrid").removeClass("last");

                var list = $(".itemlist", data).html();
                //console.log(list);
				//console.log($(list).attr("id"));
				prev_pagination = $(".pagination").html();
                var pagination = $(".pagination", data).html();
                var pagination_url = $(".show-more", data).attr("data-href");

                if (typeof pagination_url == "undefined")
                {
                    $(".ajax-pagination .show-more").hide();
					$(".ajax-pagination .show-more").attr("data-href", url);
                }
                else
                {
                    $(".grid-list .itemgrid").addClass("last-link");
                    $(".ajax-pagination .show-more").attr("data-href", pagination_url);
                }
				
				$(".grid-list .last_item").remove();
				
                $(".grid-list > div").append(list);
                $(".ajax-pagination .pagination").html(pagination);

                if ($(".gridlist .grid-btn").hasClass("activ"))
                {
                    $(".gridlist .grid-btn").click();
                }
                $(".ajax-pagination .loader").hide();
                $(".grid-list .itemgrid.last-link").removeClass("load");


                $('.grid-list .itemgrid .name').matchHeight();
                $('.grid-list .itemgrid .item_content').matchHeight();

                // $('.grid-list .item').css({
                //     'height':'auto'
                // });
            }
        )
    });

    /* При клике по переключателю вида определим, нужно ли показывать последний элемент в виде ссылки */
    $(".gridlist .grid-btn").click(function() {
        //if ($(".ajax-pagination .show-more").length > 0) {
            $(".grid-list .itemgrid").addClass("last-link");
        //} else {
            //$(".grid-list .itemgrid").removeClass("last");
        //}
    });

    $(".grid-list").on("click", ".grid-list .itemgrid.last-link .item:last", function() {
        if(!$(this).hasClass('lastpage'))
		{
			$(".ajax-pagination .show-more").click();
		}
    });
	
	$(".grid-list").on("click", ".grid-list .itemgrid.last-link .item.lastpage:last", function() {
        //$(".ajax-pagination .show-more").click();
		//alert('Click');
		if (prev_pagination != '')
		{
			$(".ajax-pagination .loader").show();
			$(".grid-list .itemgrid.last-link").addClass("load");
			$(".grid-list .lastpage").remove();
			$(".ajax-pagination .loader").hide();
			$(".grid-list .itemgrid.last-link").removeClass("load");
			$(".ajax-pagination .pagination").html(prev_pagination);
			$(".ajax-pagination .show-more").show();
		}
		else
		{
			document.location.href = $(".ajax-pagination .next_page_link").attr("href");
		}
    });
});