$(function() {
    $(".header_sub_menu_slider .root-item").click(function() {
        $(".header_sub_menu_slider .inner_menu").removeClass("open");
        $(".header_sub_menu_slider li.header_sub_menu_slider_item").removeClass("active");
        
        $(this).closest(".header_sub_menu_slider_item").find("ul").toggleClass("open");
        $(this).closest("li.header_sub_menu_slider_item").addClass("active");
        return false;
    });
});