$(function() {
    $('.textiletorg-contacts-prototype-default .photogallery').slick({
        slidesToShow: 3,
        slidesToScroll: 3,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                }
            }
        ]
    });

    $(".textiletorg-contacts-prototype-default .show-container").click(function() {
        var open = $(this).attr("data-open");
        var container = $(this).closest(".item");
		var showhide = $(".container-showhide."+open, container);

		$(showhide).toggleClass("open");
		if ($(showhide).hasClass("open")) {
			$(".container-showhide."+open, container).slideUp();
		} else {
			$(".container-showhide."+open, container).slideDown();
		}
    });

});
