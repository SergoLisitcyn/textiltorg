AMI.Message.addListener("uz_on_inform_symb_click", function(oItem){
    //console.log("uz_on_inform_symb_click");
    var oLink = $(oItem).prev('a');
    //console.log(oLink);
    if(oLink.length){
        var href = $(oLink[0]).attr("href") + "";
        //console.log(href);
        if(href.substr(0, 11) == "javascript:"){
            href = href.substr(11);
            //console.log(href);
            if(href != ""){
                eval(href);
            }
        }
        //$(oLink).trigger("click");
    }
});


AMI.Message.addListener("uz_on_dop_items_over", function(oItem){
    //console.log("uz_on_dop_items_over");
    var id = $(oItem).attr("dop_items_item_id");
    if(typeof(id) != "undefined"){
        if(id != null && id != ""){
            id = id * 1;
            $(".dop_items_announce_"+id).show();
            $(".dop_items_announce_"+id).position({
                my: "left top",
                at: "right-20 top",
                of: "li[dop_items_item_id='"+id+"']"
            });
        }
    }

});

AMI.Message.addListener("uz_on_dop_items_out", function(oItem){
    //console.log("uz_on_dop_items_out");
    var id = $(oItem).attr("dop_items_item_id");
    //console.log("id final = "+id);
    if(typeof(id) != "undefined"){
        if(id != null && id != ""){
            id = id * 1;
            $(".dop_items_announce_"+id).hide();
        }
    }
});
