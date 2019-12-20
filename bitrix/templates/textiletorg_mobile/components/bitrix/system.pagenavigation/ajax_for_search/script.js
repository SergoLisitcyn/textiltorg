$(function() {
    $(".show-more").click(function() {
        var url = $(this).attr("data-href");
        $("#ajax-pagination .loader").show();
        $("#grid-list .itemgrid.last-link").addClass("load");
        $.get(url,
            {
                AJAX_PAGEN: "Y"
            }, 
            function(data) {
                $("#grid-list .itemgrid").removeClass("last");
                
                var list = $(".itemlist", data).html();
                var pagination = $(".pagination", data).html();
                var pagination_url = $(".show-more", data).attr("data-href");
                
                if (typeof pagination_url == "undefined")
                {
                    $("#ajax-pagination .show-more").remove();
                }
                else
                {
                    $("#grid-list .itemgrid").addClass("last-link");
                    $("#ajax-pagination .show-more").attr("data-href", pagination_url);
                }
                
                $("#grid-list > div").append(list);
                $("#ajax-pagination .pagination").html(pagination);
               
                if ($(".gridlist .grid-btn").hasClass("activ"))
                {
                    $(".gridlist .grid-btn").click();
                }
                $("#ajax-pagination .loader").hide();
                $("#grid-list .itemgrid.last-link").removeClass("load");
				
				//выравнивание заголовков по высоте максимального
				$('#grid-list .name').matchHeight({ 
					// byRow: 0																  
				});
            }
        )
    });
    
    /* При клике по переключателю вида определим, нужно ли показывать последний элемент в виде ссылки */
    $(".gridlist .grid-btn").click(function() {
        if ($("#ajax-pagination .show-more").length > 0) {
            $("#grid-list .itemgrid").addClass("last-link");
        } else {
            $("#grid-list .itemgrid").removeClass("last");
        }
    });

    $("#grid-list").on("click", "#grid-list .itemgrid.last-link .item:last", function() {
        $("#ajax-pagination .show-more").click();
    });
});