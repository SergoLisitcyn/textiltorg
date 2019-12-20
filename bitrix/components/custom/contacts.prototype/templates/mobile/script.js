$(function() {
    $('.textiletorg-contacts-prototype-default2 .photogallery').slick({
        slidesToShow: 1,
        slidesToScroll: 1
    });

    $(".textiletorg-contacts-prototype-default2 .show-container").click(function() {
        var open = $(this).attr("data-open");
        var container = $(this).closest(".item");
		var showhide = $(".container-showhide."+open, container);

		if (open == "map") {
			$(".container-showhide.tour").removeClass("open").hide();
		} else {
			$(".container-showhide.map").removeClass("open").hide();
		}

		$(showhide).toggleClass("open");
		if ($(showhide).hasClass("open")) {
			$(".container-showhide."+open, container).slideDown();
		} else {
			$(".container-showhide."+open, container).slideUp();
		}
    });

});
