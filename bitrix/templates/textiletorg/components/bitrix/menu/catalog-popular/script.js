$(function(){
	$("#header-drop-down-menu").hover(function() {
		$( ".popup-overlay" ).toggle();
	});
	  $("#open_menu").hover(
        function() {
            $('.store-address').show();
        }, function() {
            $('.store-address').hide();
        }
    );
});


$(function(){
	$("#header-drop-down-menu").hover(function() {
		$( ".popup-overlay" ).toggle();
	});
});


$(window).load(function(){
	$('.flexslider').flexslider({
		animation: "none"
	});
});
