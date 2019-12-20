$(function(){
	var width = $(window).width(),
		height = $(document).height();
	//Количество миниатюр картинок в слайдерах
	if (width < 380){
		//Широкая версия
		var footer_slick = 3,
			img_list_block = 5;
	}else{
		var footer_slick = 3,
			img_list_block = 5;
	}
	var title_img_block_slider = 1;
        if (width < 414){
            var header_sub_menu_slider = 4;
        } else {
            var header_sub_menu_slider = 4;
        }
	$('.header_sub_menu_slider').slick({
		infinite: true,
		slidesToShow: header_sub_menu_slider,
		slidesToScroll: header_sub_menu_slider,
	});
	$('.footer_slick').slick({
		infinite: true,
		slidesToShow: footer_slick,
		slidesToScroll: footer_slick,
	});
	//Галерея в карточке товара
	$('.img_list_block').slick({
		infinite: false,
		slidesToShow: img_list_block,
		slidesToScroll: img_list_block,
	});
	$('.img_list_block li').on('click',function(){
		$('.img_list_block li').find('a').removeClass('active');
		$(this).find('a').addClass('active');
		
		$('.title_img_block_slider li').removeClass('slick-current').removeClass('slick-active');
		$('.title_img_block_slider li').find('a').removeClass('active');
		$('.title_img_block_slider li').eq($(this).index()).addClass('slick-current').addClass('slick-active');
		$('.title_img_block_slider li').eq($(this).index()).find("a").addClass("active");
		$('.title_img_block_slider .slick-track').css("transform", "translate3d(-"+(150*$(this).index())+"px, 0px, 0px)");

		if($(this).parents(".shop_item").find('.title_img_block').length) {
			href = $(this).find('a').attr('href');
			src = $(this).find('img').attr('src');

			$(this).parents(".shop_item.detail").find('.title_img_block').attr({
				href: href
			});

			$(this).parents(".shop_item").find('.title_img').attr({
				src: href
			});
		}
		return false;
	});
	//Галерея деталки
	$('.title_img_block_slider').slick({
		infinite: false,
		slidesToShow: title_img_block_slider,
		slidesToScroll: title_img_block_slider,
	});
	$('.popup_slider_title_img_block_slider').slick({
		infinite: false,
		slidesToShow: title_img_block_slider,
		slidesToScroll: title_img_block_slider,
	});

	$('.menu').on('click', function(){
		$('.main-container').toggleClass('no_active');
		$(this).find('.menu_items').toggleClass('active');
		$(".search").removeClass("open");
		$(".seares").hide();
		$("#wrapper-header-search").hide();

		var h = $('#wrapper').height()-20;
		$('.menu_items').height(h);
	});
	
	
	$('.menu_items h2').on('click', function(){
		return false;
	});

	// Accordion
	$('.accordion').accordion({
		collapsible: true,
		active: false,
	});
	//Карточка товара
	$('.shop_item .description_block > .button').on('click', function () {
		if($(this).parents('.description_block').hasClass('active')){
			$('.shop_item .description_block').removeClass('active');
		}else{
			$('.shop_item .description_block').removeClass('active');
			$(this).parents('.description_block').addClass('active');
		}
	});

	//Окошки
	$('.fancybox').fancybox({
		maxWidth	: '100%',
		maxHeight	: 600,
		fitToView	: false,
		width		: '100%',
		height		: 'auto',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	});

	$('.abs_link').fancybox({
		maxWidth	: '100%',
		maxHeight	: 600,
		fitToView	: false,
		padding 	: 10,
		width		: '100%',
		height		: 'auto',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	});

	/*$('.title_img_block').fancybox({
		maxWidth	: '100%',
		maxHeight	: 600,
		fitToView	: false,
		padding 	: 10,
		width		: 'auto',
		height		: 'auto',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	});*/

	$('.fancybox[href="#competition"]').fancybox({
		maxWidth	: '100%',
		fitToView	: false,
		padding 	: 0,
		width		: 'auto',
		height		: 'auto',
		autoSize	: true,
		scrolling	: 'no',
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	});

	//Добавление в корзину
	$("a[href='#add_basket']").on('click', function(){
		$('#add_basket').find('.text').html($('h1').text() + ' <span class="text-red">' + $('.price_and_buy .price_block').find('.sum').text() + ' руб.</span>');
		$('#add_basket').find('.img').html($('.title_img'));
	});
	$('.showbottom').on('click', function(){
		$(this).next('div').toggle();
	});

	$('.tooltip-message-click').click(function(event) {
		var text = $(this).attr('data-tooltipe-text'),
			message = $('<div class="tooltip-message-container"/>');

		$('#tooltipe-message').remove();

		message
			.attr('id', 'tooltipe-message')
			.html(text);

		$(this).append(message);
		event.stopPropagation();
	});

	$('body').click(function(e) {
		var message = $('#tooltipe-message');
		if (message.length) {
			message.remove();
		}
	});
	$(".accordion-header").click(function() {
        $(this).next(".accordion-content").toggleClass("active");
    });
    $(document).mouseup(function (e) {
		var container = $(".inner_menu.open");
		if(container.has(e.target).length === 0) {
			container.removeClass("open");
			container.parents(".header_sub_menu_slider_item").removeClass("active");
		}
	});
    /* Открытие поиска */
    $("#header .header-table .search").click(function() {
        $(this).toggleClass("open");
        if ($(this).hasClass("open")) {
            $("#wrapper-header-phone").hide();
            $("#wrapper-header-search").fadeIn();
            $("#wrapper-header-search input[type=text]").focus();
			$('#delect-region-search').removeClass('open');
			$('#header .menu_items').removeClass('active');
			$('.main-container').removeClass('no_active');
        } else {
            $("#wrapper-header-phone").show();
            $("#wrapper-header-search").hide();
        }
    });
	
	$(".credit").click(function() {
		if($(this).find(".message").hasClass("active")) {
			$(this).find(".message").removeClass("active");
		} else {
			$(".message").removeClass("active");
			$(this).find(".message").addClass("active");
		}
	});
    $(document).mouseup(function (e) {
		var container = $(".credit .message");
		if(container.has(e.target).length === 0) {
			container.removeClass("active");
		}
	});
	$('.akciya').on('click', function(){
		if($(this).parents(".shop_item").find(".actions-block").hasClass("active")) {
			$(this).parents(".shop_item").find(".actions-block").removeClass("active");
		} else {
			$(this).parents(".shop_item").find(".actions-block").addClass("active");
		}
	});
    $(document).mouseup(function (e) {
		var container = $(".actions-block.active");
		if(container.has(e.target).length === 0) {
			container.removeClass("active");
		}
	});
	$(".icon-message").click(function() {
		if($(this).find(".icon-message-detail").hasClass("active")) {
			$(this).find(".icon-message-detail").removeClass("active");
		} else {
			$(".icon-message-detail").removeClass("active");
			$(this).find(".icon-message-detail").addClass("active");
		}
	});
    $(document).mouseup(function (e) {
		var container = $(".icon-message-detail.active");
		if(container.has(e.target).length === 0) {
			container.removeClass("active");
		}
	});
	$(".select_delivery").click(function() {
		if($(this).data("value") == "2") {
			$(".choose_store").show();
			$(".choose_address").hide();
		} else {
			$(".choose_address").show();
			$(".choose_store").hide();
		}
	});
	$(".open_popup").click(function() {
		if($(this).find(".popup").hasClass("active")) {
			$(this).find(".popup").removeClass("active");
		} else {
			$(".popup").removeClass("active");
			$(this).find(".popup").addClass("active");
		}
	});
    $(document).mouseup(function (e) {
		var container = $(".popup.active");
		if(container.has(e.target).length === 0) {
			container.removeClass("active");
		}
	});
	$(".select_options div").click(function() {
		if($(this).text() == "В кредит или рассрочку") {
			$(".question").show();
		} else {
			$(".question").hide();
		}
		$(".select_options").removeClass("active");
		$(this).parents(".custom_select").find("input[name='CITY']").val($(this).data("value"));
		$(this).parents(".custom_select").find(".select_view").text($(this).text());
		$(this).parents(".custom_select").find(".select_options div").removeClass("option-selected");
		$(this).addClass("option-selected");		
		
		$(this).parents(".custom_select").find(".select_view").addClass("select_view_choose");
	});
	$(".select_view").click(function() {
		$(".select_options").removeClass("active");
		$(this).parents(".custom_select").find(".select_options").addClass("active");
	});
    $(document).mouseup(function (e) {
		var container = $(".select_options.active");
		if(container.has(e.target).length === 0) {
			container.removeClass("active");
		}
	});
	$(".seares .close").click(function() {
		$(".search").removeClass("open");
		$(".seares").hide();
		$("#wrapper-header-search").hide();
	});
	$(".open_slider").click(function(e) {
		e.preventDefault();
		$(this).parents(".slick-slider").siblings(".popup_slider").show();
		$(".overlay").show();
		$(this).parents(".slick-slider").siblings(".popup_slider").find('.popup_slider_title_img_block_slider').get(0).slick.setPosition();
	});
	$(".popup_slider_close").click(function(e) {
		e.preventDefault();
		$(".popup_slider").hide();
		$(".overlay").hide();
	});
});