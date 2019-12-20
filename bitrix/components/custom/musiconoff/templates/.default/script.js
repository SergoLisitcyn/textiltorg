$(function() {
    $(".custom-musiconoff-default").click(function() {
        
        var deactivate  = "Y";
        if ($(".custom-musiconoff-default").hasClass("off"))
        {
            var deactivate  = "N";
            $(".custom-musiconoff-default").attr("title", "Выключить звук");
        } else {
            $(".custom-musiconoff-default").attr("title", "Включить звук");
        }
        $(".custom-musiconoff-default").toggleClass("off");
        $.get(
            "",
            {
                AJAX_VOLUME: "Y",
                DEACTIVATE: deactivate
            }
        );
    });
});