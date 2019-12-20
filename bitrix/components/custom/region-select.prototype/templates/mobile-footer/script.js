$(function() {
    $(".region-select-prototype-mobile-footer a").click(function() {
        $(".region-select-prototype-mobile-header .select-city").click();
        $("#delect-region-search input").focus();
        return false;
    });
});