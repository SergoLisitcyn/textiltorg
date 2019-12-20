// ikSelect 0.9.2
// Copyright (c) 2012 Igor Kozlov
// i10k.ru
(function(e,t,n,r){function f(t,n){var i=this;i.element=t;i.options=e.extend({},s,n);i._defaults=s;if(i.element===r)return i;i.fakeSelect=e('<div class="ik_select">'+i.options.syntax+"</div>");i.select=e(i.element);i.link=e(".ik_select_link",i.fakeSelect);i.linkText=e(".ik_select_link_text",i.fakeSelect);i.block=e(".ik_select_block",i.fakeSelect);i.list=e(".ik_select_list",i.fakeSelect);i.listInner=e('<div class="ik_select_list_inner"/>');i.filter=e([]);i.listItemsOriginal=e([]);i.nothingFoundText=e('<div class="ik_nothing_found"/>').html(i.select.data("nothingfoundtext"));if(i.options.filter&&!e.browser.mobile){i.filterWrap=e(".ik_select_filter_wrap",i.fakeSelect);i.filterWrap.length||(i.filterWrap=e('<div class="ik_select_filter_wrap"/>'));i.filter=e('<input type="text" class="ik_select_filter">');i.filterWrap.append(i.filter)}i.active=e([]);i.hover=e([]);i.hoverIndex=-1;i.listItems=e([]);i.listOptgroupItems=e([]);i.init()}var i=e(t),s={syntax:'<div class="ik_select_link"><span class="ik_select_link_text"></span></div><div class="ik_select_block"><div class="ik_select_list"></div></div>',autoWidth:!0,ddFullWidth:!0,customClass:"",ddCustomClass:"",ddMaxHeight:200,filter:!1,onShow:function(){},onHide:function(){},onKeyUp:function(){},onKeyDown:function(){},onHoverMove:function(){}},o=e([]),u=!1,a=-1;e.browser.mobile=/iphone|ipad|ipod|android/i.test(navigator.userAgent.toLowerCase());e.browser.operamini=Object.prototype.toString.call(t.operamini)==="[object OperaMini]";e.extend(f.prototype,{init:function(){var t=this,n=t.fakeSelect,r=t.select,s=t.link,u=t.block,a=t.list,f=t.listInner,l=t.filter;a.append(f);n.addClass(t.options.customClass);u.addClass(t.options.ddCustomClass);t.reset_all();r.attr("disabled")&&t.disable_select();s.bind("click.ikSelect",function(){if(s.hasClass("ik_select_link_disabled"))return this;o.length&&o.data("plugin_ikSelect").hide_block();t.show_block();t.options.filter?l.focus():r.focus()});r.bind("focus.ikSelect",function(){if(s.hasClass("ik_select_link_disabled"))return this;s.addClass("ik_select_link_focus");(n.offset().top+n.height()>i.scrollTop()+i.height()||n.offset().top+n.height()<i.scrollTop())&&i.scrollTop(n.offset().top-i.height()/2)});r.bind("blur.ikSelect",function(){if(s.hasClass("ik_select_link_disabled"))return this;s.removeClass("ik_select_link_focus")});r.bind("change.ikSelect",function(){t._select_fake_option()});var c="";l.bind("keyup.ikSelect",function(){f.show();c===""&&!t.listItemsOriginal.length&&(t.listItemsOriginal=t.listItems);if(l.val()!==c){if(l.val()===""){t.listItems=t.listItemsOriginal.show();t.listOptgroupItems.show();t.nothingFoundText.remove()}else{t.listItems=t.listItemsOriginal.show();t.listOptgroupItems.show();t.listItems.each(function(){if(e(".ik_select_option",this).html().search(new RegExp(l.val(),"i"))===-1){t.listItems=t.listItems.not(this);e(this).hide()}});if(t.listItems.length){t.nothingFoundText.remove();t.listOptgroupItems.each(function(){var t=e(this);e("> ul > li:visible",t).length||t.hide()});!t.listItems.filter(t.hover).length&&t.listItems.length&&t._move_to(t.listItems.eq(0));t.hoverIndex=t.listItems.index(t.hover)}else{f.hide();a.append(t.nothingFoundText)}}c=l.val()}});r.add(l).bind("keydown.ikSelect keyup.ikSelect",function(n){var i=t.listItems;t.hoverIndex<0&&(t.hoverIndex=i.index(t.hover));var o=n.which,a=n.type;switch(o){case 40:if(a==="keydown"){n.preventDefault();var f;if(t.hoverIndex<i.length-1){f=i.eq(++t.hoverIndex);while(f&&f.hasClass("ik_select_option_disabled"))f=i.filter(":eq("+ ++t.hoverIndex+")")}f&&t._move_to(f)}break;case 38:if(a==="keydown"){n.preventDefault();var l;if(t.hoverIndex>0){l=i.eq(--t.hoverIndex);while(l&&l.hasClass("ik_select_option_disabled"))l=i.filter(":eq("+ --t.hoverIndex+")")}l&&t._move_to(l)}break;case 33:case 36:if(a==="keydown"){n.preventDefault();t._move_to(i.filter(".not(ik_select_option_disabled):first"))}break;case 34:case 35:if(a==="keydown"){n.preventDefault();t._move_to(i.filter(".not(ik_select_option_disabled):last"))}break;case 32:if(a==="keydown"&&e(this).is(r)){n.preventDefault();u.is(":visible")?t._select_real_option():s.click()}break;case 13:if(a==="keydown"&&u.is(":visible")){n.preventDefault();t._select_real_option()}break;case 27:if(a==="keydown"){n.preventDefault();t.hide_block()}break;case 9:a==="keydown"&&(e.browser.webkit&&u.is(":visible")?n.preventDefault():t.hide_block());break;default:a==="keyup"&&e(this).is(r)&&t._select_fake_option()}if(a==="keydown"){t.options.onKeyDown(t,o);r.trigger("ikkeydown",[t,o])}if(a==="keyup"){t.options.onKeyUp(t,o);r.trigger("ikkeyup",[t,o])}});r.after(n);t.options.filter&&!e.browser.mobile&&a.prepend(t.filterWrap);t.redraw();r.appendTo(n)},redraw:function(){var t=this,n=t.select,r=t.fakeSelect,i=t.block,s=t.list,o=t.listInner,u=t.options.autoWidth,f=t.options.ddFullWidth;if(u||f){o.width("auto");e("ul",o).width("auto");r.width("auto");i.show().width(9999);o.css("float","left");s.css("position","absolute");var l=s.outerWidth(!0),c=s.width();s.css("position","static");i.hide().css("width","100%");o.css("float","none");if(a===-1){var h=e('<div style="width:50px; height:50px; overflow:hidden; position:absolute; top:-200px; left:-200px;"><div style="height:100px;"></div>');e("body").append(h);var p=e("div",h).innerWidth();h.css("overflow","auto");var d=e("div",h).innerWidth();e(h).remove();a=p-d}var v=r.parent().width();if(f){i.width(l);o.width(c);e("ul",o).width(c)}l>v&&(l=v);u&&r.width(l)}t._fix_height();n.css({position:"absolute",margin:0,padding:0,left:-9999,top:0});e.browser.mobile&&n.css({opacity:0,left:0,height:r.height()})},reset_all:function(){var t=this,n=t.select,r=t.linkText,i=t.listInner;r.html(n.val());i.empty();var s="";s+="<ul>";n.children().each(function(){if(this.tagName==="OPTGROUP"){var t=e(this);s+='<li class="ik_select_optgroup'+(t.is(":disabled")?" ik_select_optgroup_disabled":"")+'">';s+='<div class="ik_select_optgroup_label">'+t.attr("label")+"</div>";s+="<ul>";e("option",t).each(function(){var t=e(this);s+="<li"+(t.is(":disabled")?' class="ik_select_option_disabled"':"")+'><span class="ik_select_option'+(t[0].getAttribute("value")?"":" ik_select_option_novalue")+'" title="'+t.val()+'">'+t.html()+"</span></li>"});s+="</ul>";s+="</li>"}else{var n=e(this);s+="<li"+(n.is(":disabled")?' class="ik_select_option_disabled"':"")+'><span class="ik_select_option'+(n[0].getAttribute("value")?"":" ik_select_option_novalue")+'" title="'+n.val()+'">'+n.html()+"</span></li>"}});s+="</ul>";i.append(s);t._select_fake_option();t.listOptgroupItems=e(".ik_select_optgroup",i);t.listItems=e("li:not(.ik_select_optgroup)",i);t._attach_list_events(t.listItems)},_attach_list_events:function(t){var n=this,r=n.select,i=n.link,s=n.linkText,o=t.not(".ik_select_option_disabled");o.bind("click.ikSelect",function(){var t=e(".ik_select_option",this);s.html(t.html());r.val(t.attr("title"));n.active.removeClass("ik_select_active");n.active=e(this).addClass("ik_select_active");n.hide_block();t.hasClass("ik_select_option_novalue")?i.addClass("ik_select_link_novalue"):i.removeClass("ik_select_link_novalue");r.change();r.focus()});o.bind("mouseover.ikSelect",function(){n.hoverIndex=-1;n.hover.removeClass("ik_select_hover");n.hover=e(this).addClass("ik_select_hover")});o.addClass("ik_select_has_events")},_detach_list_events:function(e){e.unbind(".ikSelect");e.removeClass("ik_select_has_events")},set_defaults:function(t){e.extend(this._defaults,t||{});return this},hide_block:function(){var t=this,n=t.fakeSelect,r=t.block,i=t.select;t.options.filter&&!e.browser.mobile&&t.filter.val("").keyup();if(t.listItemsOriginal.length){t.listOptgroupItems.show();t.listItems=t.listItemsOriginal.show()}r.hide().appendTo(n).css({left:"",top:""});i.removeClass(".ik_select_opened");o=e([]);i.focus();t.options.onHide(t);i.trigger("ikhide",[t])},show_block:function(){var t=this,n=t.select;if(o.is(t.select)||!t.listItems.length)return t;o.length&&o.data("plugin_ikSelect").hide_block();var r=t.fakeSelect,s=t.block,u=t.list,a=t.listInner,f=t.hover,l=t.active,c=t.listItems;s.show();n.addClass("ik_select_opened");var h=e("option",n).index(e("option:selected",n));f.removeClass("ik_select_hover");l.removeClass("ik_select_active");var p=c.eq(h);p.addClass("ik_select_hover ik_select_active");t.hover=p;t.active=p;t.hoverIndex=t.listItems.index(p);s.css("left","");t.options.ddFullWidth&&r.offset().left+s.outerWidth(!0)>i.width()&&s.css("left",(s.offset().left+s.outerWidth(!0)-i.width())*-1);s.css("top","");s.offset().top+s.outerHeight(!0)>i.scrollTop()+i.height()&&s.css("top",(s.offset().top+s.outerHeight(!0)-parseInt(s.css("top"),10)-(i.scrollTop()+i.height()))*-1);var d=s.offset().left;d<0&&(d=0);var v=s.offset().top;s.width(s.width());s.appendTo("body").css({left:d,top:v});var m=e(".ik_select_active",u).position().top-u.height()/2;u.data("ik_select_scrollTop",m);a.scrollTop(m);o=n;t.options.onShow(t);n.trigger("ikshow",[t])},add_options:function(t){var n=this,r=n.select,i=n.listInner,s="",o="";e.each(t,function(t,n){if(typeof n=="string"){s+='<li><span class="ik_select_option" title="'+t+'">'+n+"</span></li>";o+='<option value="'+t+'">'+n+"</option>"}else if(typeof n=="object"){var u=e("> ul > li.ik_select_optgroup:eq("+t+") > ul",i),a=e("optgroup:eq("+t+")",r),f=n;e.each(f,function(e,t){s+='<li><span class="ik_select_option" title="'+e+'">'+t+"</span></li>";o+='<option value="'+e+'">'+t+"</option>"});u.append(s);a.append(o);s="";o=""}});if(o!==""){e(":first",i).append(s);r.append(o)}n._fix_height();n.listItems=e("li:not(.ik_select_optgroup)",i);n._attach_list_events(n.listItems)},remove_options:function(t){var n=this,r=n.select,i=n.listItems,s=e([]);e.each(t,function(t,n){e("option",r).each(function(t){e(this).val()===n&&(s=s.add(e(this)).add(i.eq(t)))})});n.listItems=i.not(s);s.remove();n._select_fake_option();n._fix_height()},_select_real_option:function(){var e=this.hover,t=this.active;t.removeClass("ik_select_active");e.addClass("ik_select_active").click()},_select_fake_option:function(){var t=this,n=t.select,r=t.link,i=t.linkText,s=t.listItems,o=e(":selected",n),u=e("option",n).index(o);i.html(o.html());o.length&&o[0].getAttribute("value")?r.removeClass("ik_select_link_novalue"):r.addClass("ik_select_link_novalue");t.hover=s.removeClass("ik_select_hover ik_select_active").eq(u).addClass("ik_select_hover ik_select_active");t.active=t.hover},disable_select:function(){var e=this.select,t=this.link;e.attr("disabled","disabled");t.addClass("ik_select_link_disabled")},enable_select:function(){var e=this.select,t=this.link;e.removeAttr("disabled");t.removeClass("ik_select_link_disabled")},toggle_select:function(){var e=this,t=this.link;t.hasClass("ik_select_link_disabled")?e.enable_select():e.disable_select()},make_selection:function(e){var t=this,n=t.select;n.val(e);t._select_fake_option()},disable_optgroups:function(t){var n=this,r=n.select,i=n.list;e.each(t,function(t,s){var o=e("optgroup:eq("+s+")",r);o.attr("disabled","disabled");e(".ik_select_optgroup:eq("+s+")",i).addClass("ik_select_optgroup_disabled");n.disable_options(e("option",o))});n._select_fake_option()},enable_optgroups:function(t){var n=this,r=n.select,i=n.list;e.each(t,function(t,s){var o=e("optgroup:eq("+s+")",r);o.removeAttr("disabled");e(".ik_select_optgroup:eq("+s+")",i).removeClass("ik_select_optgroup_disabled");n.enable_options(e("option",o))});n._select_fake_option()},disable_options:function(t){var n=this,r=n.select,i=n.listItems,s=e("option",r);e.each(t,function(t,r){if(typeof r=="object"){e(this).attr("disabled","disabled");var o=s.index(this),u=i.eq(o).addClass("ik_select_option_disabled");n._detach_list_events(u)}else s.each(function(t){if(e(this).val()===r){e(this).attr("disabled","disabled");var s=i.eq(t).addClass("ik_select_option_disabled");n._detach_list_events(s);return this}})});n._select_fake_option()},enable_options:function(t){var n=this,r=n.select,i=n.listItems,s=e("option",r);e.each(t,function(t,r){if(typeof r=="object"){e(this).removeAttr("disabled");var o=s.index(this),u=i.eq(o).removeClass("ik_select_option_disabled");n._attach_list_events(u)}else s.each(function(t){if(e(this).val()===r){e(this).removeAttr("disabled");var s=i.eq(t).removeClass("ik_select_option_disabled");n._attach_list_events(s);return this}})});n._select_fake_option()},detach_plugin:function(){var e=this,t=e.select,n=e.fakeSelect;t.unbind(".ikSelect").css({width:"",height:"",left:"",top:"",position:"",margin:"",padding:""});n.before(t);n.remove()},_move_to:function(t){var n=this,r=n.select,i=n.linkText,s=n.block,o=n.list,u=n.listInner;if(!s.is(":visible")&&e.browser.webkit){n.show_block();return this}n.hover.removeClass("ik_select_hover");t.addClass("ik_select_hover");n.hover=t;if(!e.browser.webkit){n.active.removeClass("ik_select_active");t.addClass("ik_select_active");n.active=t}if(!s.is(":visible")||e.browser.mozilla){if(!e.browser.mozilla){r.val(e(".ik_select_option",t).attr("title"));r.change()}i.html(e(".ik_select_option",t).html())}var a=t.offset().top-o.offset().top-parseInt(o.css("paddingTop"),10),f=a+t.outerHeight();f>o.height()?u.scrollTop(u.scrollTop()+f-o.height()):a<0&&u.scrollTop(u.scrollTop()+a);n.options.onHoverMove(t,n);r.trigger("ikhovermove",[t,n])},_fix_height:function(){var t=this,n=t.block,r=t.listInner,i=t.options.ddMaxHeight,s=t.options.ddFullWidth;n.show();r.css("height","auto");if(r.height()>i){r.css({overflow:"auto",height:i,position:"relative"});if(!e.data(r,"ik_select_hasScrollbar")&&s){n.width(n.width()+a);r.width(r.width()+a)}e.data(r,"ik_select_hasScrollbar",!0)}else if(e.data(r,"ik_select_hasScrollbar")){r.css({overflow:"",height:"auto"});r.width(r.width()-a);n.width(n.width()-a)}n.hide()}});e.fn.ikSelect=function(t){if(e.browser.operamini)return this;var n=Array.prototype.slice.call(arguments);return this.each(function(){if(!e.data(this,"plugin_ikSelect"))e.data(this,"plugin_ikSelect",new f(this,t));else if(typeof t=="string"){var r=e.data(this,"plugin_ikSelect");switch(t){case"reset":r.reset_all();break;case"hide_dropdown":r.hide_block();break;case"show_dropdown":u=!0;r.select.focus();r.show_block();break;case"add_options":r.add_options(n[1]);break;case"remove_options":r.remove_options(n[1]);break;case"enable":r.enable_select();break;case"disable":r.disable_select();break;case"toggle":r.toggle_select();break;case"select":r.make_selection(n[1]);break;case"set_defaults":r.set_defaults(n[1]);break;case"redraw":r.redraw();break;case"disable_options":r.disable_options(n[1]);break;case"enable_options":r.enable_options(n[1]);break;case"disable_optgroups":r.disable_optgroups(n[1]);break;case"enable_optgroups":r.enable_optgroups(n[1]);break;case"detach":r.detach_plugin()}}})};e.ikSelect=new f;e(n).bind("click.ikSelect",function(t){if(!u&&o.length&&!e(t.target).closest(".ik_select").length&&!e(t.target).closest(".ik_select_block").length){o.ikSelect("hide_dropdown");o=e([])}u&&(u=!1)})})(jQuery,window,document);

/*!
 * jQuery Accordion widget
 * http://nefariousdesigns.co.uk/projects/widgets/accordion/
 *
 * Source code: http://github.com/nefarioustim/jquery-accordion/
 *
 * Copyright © 2010 Tim Huegdon
 * http://timhuegdon.com
 */

(function($) {
    var debugMode = false;

    function debug(msg) {
        if (!debugMode) { return; }
        if (window.console && window.console.log){
            window.console.log(msg);
        } else {
            alert(msg);
        }
    }

    $.fn.accordion = function(config) {
        var defaults = {
            "handle": "h4",
            "panel": ".panel",
            "speed": 200,
            "easing": "swing",
            "canOpenMultiple": false,
            "canToggle": false,
            "activeClassPanel": "open",
            "activeClassLi": "active",
            "lockedClass": "locked",
            "loadingClass": "loading"
        };

        if (config) {
            $.extend(defaults, config);
        }

        this.each(function() {
            var accordion   = $(this),
                reset       = {
                    height: 0,
                    marginTop: 0,
                    marginBottom: 0,
                    paddingTop: 0,
                    paddingBottom: 0
                },
                panels      = accordion.find(">li>" + defaults.panel)
                                .each(function() {
                                    var el = $(this);
                                    el
                                        .removeClass(defaults.loadingClass)
                                        .css("visibility", "hidden")
                                        .data("dimensions", {
                                            marginTop:      el.css("marginTop"),
                                            marginBottom:   el.css("marginBottom"),
                                            paddingTop:     el.css("paddingTop"),
                                            paddingBottom:  el.css("paddingBottom"),
                                            height:         this.offsetHeight - parseInt(el.css("paddingTop")) - parseInt(el.css("paddingBottom"))
                                        })
                                        .bind("panel-open.accordion", function(e, clickedLi) {
                                            var panel = $(this);
                                            clickedLi.addClass(defaults.activeClassLi);
                                            panel
                                                .css($.extend({overflow: "hidden"}, reset))
                                                .addClass(defaults.activeClassPanel)
                                                .show()
                                                .animate($.browser.msie && parseInt($.browser.version) < 8 ? panel.data("dimensions") : $.extend({opacity: 1}, panel.data("dimensions")), {
                                                    duration:   defaults.speed,
                                                    easing:     defaults.easing,
                                                    queue:      false,
                                                    complete:   function(e) {
                                                        if ($.browser.msie) {
                                                            this.style.removeAttribute('filter');
                                                        }
                                                        $(this).removeAttr("style");
                                                    }
                                                });
                                        })
                                        .bind("panel-close.accordion", function(e) {
                                            var panel = $(this);
                                            panel.closest("li").removeClass(defaults.activeClassLi);
                                            panel
                                                .removeClass(defaults.activeClassPanel)
                                                .css({
                                                    overflow: "hidden"
                                                })
                                                .animate($.browser.msie && parseInt($.browser.version) < 8 ? reset : $.extend({opacity: 0}, reset), {
                                                    duration:   defaults.speed,
                                                    easing:     defaults.easing,
                                                    queue:      false,
                                                    complete:   function(e) {
                                                        if ($.browser.msie) {
                                                            this.style.removeAttribute('filter');
                                                        }
                                                        panel.hide();
                                                    }
                                                });
                                        })
                                        .hide()
                                        .css("visibility", "visible");

                                    return el;
                                }),
                handles     = accordion.find(
                                " > li > "
                                + defaults.handle
                            )
                                .wrapInner('<a class="accordion-opener" href="#open-panel" />');

            accordion
                .find(
                    " > li."
                    + defaults.activeClassLi
                    + " > "
                    + defaults.panel
                    + ", > li."
                    + defaults.lockedClass
                    + " > "
                    + defaults.panel
                )
                .show()
                .addClass(defaults.activeClassPanel);

            var active = accordion.find(
                " > li."
                + defaults.activeClassLi
                + ", > li."
                + defaults.lockedClass
            );

            if (!defaults.canToggle && active.length < 1) {
                accordion
                    .find(" > li")
                    .first()
                    .addClass(defaults.activeClassLi)
                    .find(" > " + defaults.panel)
                    .addClass(defaults.activeClassPanel)
                    .show();
            }

            accordion.delegate(".accordion-opener", "click", function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                var clicked     = $(this),
                    clickedLi   = clicked.closest("li"),
                    panel       = clickedLi.find(">" + defaults.panel).first(),
                    open        = accordion.find(
                        " > li:not(."
                        + defaults.lockedClass
                        + ") > "
                        + defaults.panel
                        + "."
                        + defaults.activeClassPanel
                    );

                if (!clickedLi.hasClass(defaults.lockedClass)) {
                    if (panel.is(":visible")) {
                        if (defaults.canToggle) {
                            panel.trigger("panel-close");
                        }
                    } else {
                        panel.trigger("panel-open", [clickedLi]);
                        if (!defaults.canOpenMultiple) {
                            open.trigger("panel-close");
                        }
                    }
                }
            });
        });

        return this;
    };
})(jQuery);


(function($,undefined){$.ui=$.ui||{};if($.ui.version){return}$.extend($.ui,{version:"1.8.13",keyCode:{ALT:18,BACKSPACE:8,CAPS_LOCK:20,COMMA:188,COMMAND:91,COMMAND_LEFT:91,COMMAND_RIGHT:93,CONTROL:17,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,INSERT:45,LEFT:37,MENU:93,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SHIFT:16,SPACE:32,TAB:9,UP:38,WINDOWS:91}});$.fn.extend({_focus:$.fn.focus,focus:function(delay,fn){return typeof delay==="number"?this.each(function(){var elem=this;setTimeout(function(){$(elem).focus();if(fn){fn.call(elem)}},delay)}):this._focus.apply(this,arguments)},scrollParent:function(){var scrollParent;if(($.browser.msie&&(/(static|relative)/).test(this.css('position')))||(/absolute/).test(this.css('position'))){scrollParent=this.parents().filter(function(){return(/(relative|absolute|fixed)/).test($.curCSS(this,'position',1))&&(/(auto|scroll)/).test($.curCSS(this,'overflow',1)+$.curCSS(this,'overflow-y',1)+$.curCSS(this,'overflow-x',1))}).eq(0)}else{scrollParent=this.parents().filter(function(){return(/(auto|scroll)/).test($.curCSS(this,'overflow',1)+$.curCSS(this,'overflow-y',1)+$.curCSS(this,'overflow-x',1))}).eq(0)}return(/fixed/).test(this.css('position'))||!scrollParent.length?$(document):scrollParent},zIndex:function(zIndex){if(zIndex!==undefined){return this.css("zIndex",zIndex)}if(this.length){var elem=$(this[0]),position,value;while(elem.length&&elem[0]!==document){position=elem.css("position");if(position==="absolute"||position==="relative"||position==="fixed"){value=parseInt(elem.css("zIndex"),10);if(!isNaN(value)&&value!==0){return value}}elem=elem.parent()}}return 0},disableSelection:function(){return this.bind(($.support.selectstart?"selectstart":"mousedown")+".ui-disableSelection",function(event){event.preventDefault()})},enableSelection:function(){return this.unbind(".ui-disableSelection")}});$.each(["Width","Height"],function(i,name){var side=name==="Width"?["Left","Right"]:["Top","Bottom"],type=name.toLowerCase(),orig={innerWidth:$.fn.innerWidth,innerHeight:$.fn.innerHeight,outerWidth:$.fn.outerWidth,outerHeight:$.fn.outerHeight};function reduce(elem,size,border,margin){$.each(side,function(){size-=parseFloat($.curCSS(elem,"padding"+this,true))||0;if(border){size-=parseFloat($.curCSS(elem,"border"+this+"Width",true))||0}if(margin){size-=parseFloat($.curCSS(elem,"margin"+this,true))||0}});return size}$.fn["inner"+name]=function(size){if(size===undefined){return orig["inner"+name].call(this)}return this.each(function(){$(this).css(type,reduce(this,size)+"px")})};$.fn["outer"+name]=function(size,margin){if(typeof size!=="number"){return orig["outer"+name].call(this,size)}return this.each(function(){$(this).css(type,reduce(this,size,true,margin)+"px")})}});function focusable(element,isTabIndexNotNaN){var nodeName=element.nodeName.toLowerCase();if("area"===nodeName){var map=element.parentNode,mapName=map.name,img;if(!element.href||!mapName||map.nodeName.toLowerCase()!=="map"){return false}img=$("img[usemap=#"+mapName+"]")[0];return!!img&&visible(img)}return(/input|select|textarea|button|object/.test(nodeName)?!element.disabled:"a"==nodeName?element.href||isTabIndexNotNaN:isTabIndexNotNaN)&&visible(element)}function visible(element){return!$(element).parents().andSelf().filter(function(){return $.curCSS(this,"visibility")==="hidden"||$.expr.filters.hidden(this)}).length}$.extend($.expr[":"],{data:function(elem,i,match){return!!$.data(elem,match[3])},focusable:function(element){return focusable(element,!isNaN($.attr(element,"tabindex")))},tabbable:function(element){var tabIndex=$.attr(element,"tabindex"),isTabIndexNaN=isNaN(tabIndex);return(isTabIndexNaN||tabIndex>=0)&&focusable(element,!isTabIndexNaN)}});$(function(){var body=document.body,div=body.appendChild(div=document.createElement("div"));$.extend(div.style,{minHeight:"100px",height:"auto",padding:0,borderWidth:0});$.support.minHeight=div.offsetHeight===100;$.support.selectstart="onselectstart"in div;body.removeChild(div).style.display="none"});$.extend($.ui,{plugin:{add:function(module,option,set){var proto=$.ui[module].prototype;for(var i in set){proto.plugins[i]=proto.plugins[i]||[];proto.plugins[i].push([option,set[i]])}},call:function(instance,name,args){var set=instance.plugins[name];if(!set||!instance.element[0].parentNode){return}for(var i=0;i<set.length;i++){if(instance.options[set[i][0]]){set[i][1].apply(instance.element,args)}}}},contains:function(a,b){return document.compareDocumentPosition?a.compareDocumentPosition(b)&16:a!==b&&a.contains(b)},hasScroll:function(el,a){if($(el).css("overflow")==="hidden"){return false}var scroll=(a&&a==="left")?"scrollLeft":"scrollTop",has=false;if(el[scroll]>0){return true}el[scroll]=1;has=(el[scroll]>0);el[scroll]=0;return has},isOverAxis:function(x,reference,size){return(x>reference)&&(x<(reference+size))},isOver:function(y,x,top,left,height,width){return $.ui.isOverAxis(y,top,height)&&$.ui.isOverAxis(x,left,width)}})})(jQuery);(function($,undefined){if($.cleanData){var _cleanData=$.cleanData;$.cleanData=function(elems){for(var i=0,elem;(elem=elems[i])!=null;i++){$(elem).triggerHandler("remove")}_cleanData(elems)}}else{var _remove=$.fn.remove;$.fn.remove=function(selector,keepData){return this.each(function(){if(!keepData){if(!selector||$.filter(selector,[this]).length){$("*",this).add([this]).each(function(){$(this).triggerHandler("remove")})}}return _remove.call($(this),selector,keepData)})}}$.widget=function(name,base,prototype){var namespace=name.split(".")[0],fullName;name=name.split(".")[1];fullName=namespace+"-"+name;if(!prototype){prototype=base;base=$.Widget}$.expr[":"][fullName]=function(elem){return!!$.data(elem,name)};$[namespace]=$[namespace]||{};$[namespace][name]=function(options,element){if(arguments.length){this._createWidget(options,element)}};var basePrototype=new base();basePrototype.options=$.extend(true,{},basePrototype.options);$[namespace][name].prototype=$.extend(true,basePrototype,{namespace:namespace,widgetName:name,widgetEventPrefix:$[namespace][name].prototype.widgetEventPrefix||name,widgetBaseClass:fullName},prototype);$.widget.bridge(name,$[namespace][name])};$.widget.bridge=function(name,object){$.fn[name]=function(options){var isMethodCall=typeof options==="string",args=Array.prototype.slice.call(arguments,1),returnValue=this;options=!isMethodCall&&args.length?$.extend.apply(null,[true,options].concat(args)):options;if(isMethodCall&&options.charAt(0)==="_"){return returnValue}if(isMethodCall){this.each(function(){var instance=$.data(this,name),methodValue=instance&&$.isFunction(instance[options])?instance[options].apply(instance,args):instance;if(methodValue!==instance&&methodValue!==undefined){returnValue=methodValue;return false}})}else{this.each(function(){var instance=$.data(this,name);if(instance){instance.option(options||{})._init()}else{$.data(this,name,new object(options,this))}})}return returnValue}};$.Widget=function(options,element){if(arguments.length){this._createWidget(options,element)}};$.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",options:{disabled:false},_createWidget:function(options,element){$.data(element,this.widgetName,this);this.element=$(element);this.options=$.extend(true,{},this.options,this._getCreateOptions(),options);var self=this;this.element.bind("remove."+this.widgetName,function(){self.destroy()});this._create();this._trigger("create");this._init()},_getCreateOptions:function(){return $.metadata&&$.metadata.get(this.element[0])[this.widgetName]},_create:function(){},_init:function(){},destroy:function(){this.element.unbind("."+this.widgetName).removeData(this.widgetName);this.widget().unbind("."+this.widgetName).removeAttr("aria-disabled").removeClass(this.widgetBaseClass+"-disabled "+"ui-state-disabled")},widget:function(){return this.element},option:function(key,value){var options=key;if(arguments.length===0){return $.extend({},this.options)}if(typeof key==="string"){if(value===undefined){return this.options[key]}options={};options[key]=value}this._setOptions(options);return this},_setOptions:function(options){var self=this;$.each(options,function(key,value){self._setOption(key,value)});return this},_setOption:function(key,value){this.options[key]=value;if(key==="disabled"){this.widget()[value?"addClass":"removeClass"](this.widgetBaseClass+"-disabled"+" "+"ui-state-disabled").attr("aria-disabled",value)}return this},enable:function(){return this._setOption("disabled",false)},disable:function(){return this._setOption("disabled",true)},_trigger:function(type,event,data){var callback=this.options[type];event=$.Event(event);event.type=(type===this.widgetEventPrefix?type:this.widgetEventPrefix+type).toLowerCase();data=data||{};if(event.originalEvent){for(var i=$.event.props.length,prop;i;){prop=$.event.props[--i];event[prop]=event.originalEvent[prop]}}this.element.trigger(event,data);return!($.isFunction(callback)&&callback.call(this.element[0],event,data)===false||event.isDefaultPrevented())}}})(jQuery);(function($,undefined){var mouseHandled=false;$(document).mousedown(function(e){mouseHandled=false});$.widget("ui.mouse",{options:{cancel:':input,option',distance:1,delay:0},_mouseInit:function(){var self=this;this.element.bind('mousedown.'+this.widgetName,function(event){return self._mouseDown(event)}).bind('click.'+this.widgetName,function(event){if(true===$.data(event.target,self.widgetName+'.preventClickEvent')){$.removeData(event.target,self.widgetName+'.preventClickEvent');event.stopImmediatePropagation();return false}});this.started=false},_mouseDestroy:function(){this.element.unbind('.'+this.widgetName)},_mouseDown:function(event){if(mouseHandled){return};(this._mouseStarted&&this._mouseUp(event));this._mouseDownEvent=event;var self=this,btnIsLeft=(event.which==1),elIsCancel=(typeof this.options.cancel=="string"?$(event.target).parents().add(event.target).filter(this.options.cancel).length:false);if(!btnIsLeft||elIsCancel||!this._mouseCapture(event)){return true}this.mouseDelayMet=!this.options.delay;if(!this.mouseDelayMet){this._mouseDelayTimer=setTimeout(function(){self.mouseDelayMet=true},this.options.delay)}if(this._mouseDistanceMet(event)&&this._mouseDelayMet(event)){this._mouseStarted=(this._mouseStart(event)!==false);if(!this._mouseStarted){event.preventDefault();return true}}if(true===$.data(event.target,this.widgetName+'.preventClickEvent')){$.removeData(event.target,this.widgetName+'.preventClickEvent')}this._mouseMoveDelegate=function(event){return self._mouseMove(event)};this._mouseUpDelegate=function(event){return self._mouseUp(event)};$(document).bind('mousemove.'+this.widgetName,this._mouseMoveDelegate).bind('mouseup.'+this.widgetName,this._mouseUpDelegate);event.preventDefault();mouseHandled=true;return true},_mouseMove:function(event){if($.browser.msie&&!(document.documentMode>=9)&&!event.button){return this._mouseUp(event)}if(this._mouseStarted){this._mouseDrag(event);return event.preventDefault()}if(this._mouseDistanceMet(event)&&this._mouseDelayMet(event)){this._mouseStarted=(this._mouseStart(this._mouseDownEvent,event)!==false);(this._mouseStarted?this._mouseDrag(event):this._mouseUp(event))}return!this._mouseStarted},_mouseUp:function(event){$(document).unbind('mousemove.'+this.widgetName,this._mouseMoveDelegate).unbind('mouseup.'+this.widgetName,this._mouseUpDelegate);if(this._mouseStarted){this._mouseStarted=false;if(event.target==this._mouseDownEvent.target){$.data(event.target,this.widgetName+'.preventClickEvent',true)}this._mouseStop(event)}return false},_mouseDistanceMet:function(event){return(Math.max(Math.abs(this._mouseDownEvent.pageX-event.pageX),Math.abs(this._mouseDownEvent.pageY-event.pageY))>=this.options.distance)},_mouseDelayMet:function(event){return this.mouseDelayMet},_mouseStart:function(event){},_mouseDrag:function(event){},_mouseStop:function(event){},_mouseCapture:function(event){return true}})})(jQuery);(function($,undefined){var numPages=5;$.widget("ui.slider",$.ui.mouse,{widgetEventPrefix:"slide",options:{animate:false,distance:0,max:100,min:0,orientation:"horizontal",range:false,step:1,value:0,values:null},_create:function(){var self=this,o=this.options,existingHandles=this.element.find(".ui-slider-handle").addClass("ui-state-default ui-corner-all"),handle="<a class='ui-slider-handle ui-state-default ui-corner-all' href='#'></a>",handleCount=(o.values&&o.values.length)||1,handles=[];this._keySliding=false;this._mouseSliding=false;this._animateOff=true;this._handleIndex=null;this._detectOrientation();this._mouseInit();this.element.addClass("ui-slider"+" ui-slider-"+this.orientation+" ui-widget"+" ui-widget-content"+" ui-corner-all"+(o.disabled?" ui-slider-disabled ui-disabled":""));this.range=$([]);if(o.range){if(o.range===true){if(!o.values){o.values=[this._valueMin(),this._valueMin()]}if(o.values.length&&o.values.length!==2){o.values=[o.values[0],o.values[0]]}}this.range=$("<div></div>").appendTo(this.element).addClass("ui-slider-range"+" ui-widget-header"+((o.range==="min"||o.range==="max")?" ui-slider-range-"+o.range:""))}for(var i=existingHandles.length;i<handleCount;i+=1){handles.push(handle)}this.handles=existingHandles.add($(handles.join("")).appendTo(self.element));this.handle=this.handles.eq(0);this.handles.add(this.range).filter("a").click(function(event){event.preventDefault()}).hover(function(){if(!o.disabled){$(this).addClass("ui-state-hover")}},function(){$(this).removeClass("ui-state-hover")}).focus(function(){if(!o.disabled){$(".ui-slider .ui-state-focus").removeClass("ui-state-focus");$(this).addClass("ui-state-focus")}else{$(this).blur()}}).blur(function(){$(this).removeClass("ui-state-focus")});this.handles.each(function(i){$(this).data("index.ui-slider-handle",i)});this.handles.keydown(function(event){var ret=true,index=$(this).data("index.ui-slider-handle"),allowed,curVal,newVal,step;if(self.options.disabled){return}switch(event.keyCode){case $.ui.keyCode.HOME:case $.ui.keyCode.END:case $.ui.keyCode.PAGE_UP:case $.ui.keyCode.PAGE_DOWN:case $.ui.keyCode.UP:case $.ui.keyCode.RIGHT:case $.ui.keyCode.DOWN:case $.ui.keyCode.LEFT:ret=false;if(!self._keySliding){self._keySliding=true;$(this).addClass("ui-state-active");allowed=self._start(event,index);if(allowed===false){return}}break}step=self.options.step;if(self.options.values&&self.options.values.length){curVal=newVal=self.values(index)}else{curVal=newVal=self.value()}switch(event.keyCode){case $.ui.keyCode.HOME:newVal=self._valueMin();break;case $.ui.keyCode.END:newVal=self._valueMax();break;case $.ui.keyCode.PAGE_UP:newVal=self._trimAlignValue(curVal+((self._valueMax()-self._valueMin())/numPages));break;case $.ui.keyCode.PAGE_DOWN:newVal=self._trimAlignValue(curVal-((self._valueMax()-self._valueMin())/numPages));break;case $.ui.keyCode.UP:case $.ui.keyCode.RIGHT:if(curVal===self._valueMax()){return}newVal=self._trimAlignValue(curVal+step);break;case $.ui.keyCode.DOWN:case $.ui.keyCode.LEFT:if(curVal===self._valueMin()){return}newVal=self._trimAlignValue(curVal-step);break}self._slide(event,index,newVal);return ret}).keyup(function(event){var index=$(this).data("index.ui-slider-handle");if(self._keySliding){self._keySliding=false;self._stop(event,index);self._change(event,index);$(this).removeClass("ui-state-active")}});this._refreshValue();this._animateOff=false},destroy:function(){this.handles.remove();this.range.remove();this.element.removeClass("ui-slider"+" ui-slider-horizontal"+" ui-slider-vertical"+" ui-slider-disabled"+" ui-widget"+" ui-widget-content"+" ui-corner-all").removeData("slider").unbind(".slider");this._mouseDestroy();return this},_mouseCapture:function(event){var o=this.options,position,normValue,distance,closestHandle,self,index,allowed,offset,mouseOverHandle;if(o.disabled){return false}this.elementSize={width:this.element.outerWidth(),height:this.element.outerHeight()};this.elementOffset=this.element.offset();position={x:event.pageX,y:event.pageY};normValue=this._normValueFromMouse(position);distance=this._valueMax()-this._valueMin()+1;self=this;this.handles.each(function(i){var thisDistance=Math.abs(normValue-self.values(i));if(distance>thisDistance){distance=thisDistance;closestHandle=$(this);index=i}});if(o.range===true&&this.values(1)===o.min){index+=1;closestHandle=$(this.handles[index])}allowed=this._start(event,index);if(allowed===false){return false}this._mouseSliding=true;self._handleIndex=index;closestHandle.addClass("ui-state-active").focus();offset=closestHandle.offset();mouseOverHandle=!$(event.target).parents().andSelf().is(".ui-slider-handle");this._clickOffset=mouseOverHandle?{left:0,top:0}:{left:event.pageX-offset.left-(closestHandle.width()/2),top:event.pageY-offset.top-(closestHandle.height()/2)-(parseInt(closestHandle.css("borderTopWidth"),10)||0)-(parseInt(closestHandle.css("borderBottomWidth"),10)||0)+(parseInt(closestHandle.css("marginTop"),10)||0)};if(!this.handles.hasClass("ui-state-hover")){this._slide(event,index,normValue)}this._animateOff=true;return true},_mouseStart:function(event){return true},_mouseDrag:function(event){var position={x:event.pageX,y:event.pageY},normValue=this._normValueFromMouse(position);this._slide(event,this._handleIndex,normValue);return false},_mouseStop:function(event){this.handles.removeClass("ui-state-active");this._mouseSliding=false;this._stop(event,this._handleIndex);this._change(event,this._handleIndex);this._handleIndex=null;this._clickOffset=null;this._animateOff=false;return false},_detectOrientation:function(){this.orientation=(this.options.orientation==="vertical")?"vertical":"horizontal"},_normValueFromMouse:function(position){var pixelTotal,pixelMouse,percentMouse,valueTotal,valueMouse;if(this.orientation==="horizontal"){pixelTotal=this.elementSize.width;pixelMouse=position.x-this.elementOffset.left-(this._clickOffset?this._clickOffset.left:0)}else{pixelTotal=this.elementSize.height;pixelMouse=position.y-this.elementOffset.top-(this._clickOffset?this._clickOffset.top:0)}percentMouse=(pixelMouse/pixelTotal);if(percentMouse>1){percentMouse=1}if(percentMouse<0){percentMouse=0}if(this.orientation==="vertical"){percentMouse=1-percentMouse}valueTotal=this._valueMax()-this._valueMin();valueMouse=this._valueMin()+percentMouse*valueTotal;return this._trimAlignValue(valueMouse)},_start:function(event,index){var uiHash={handle:this.handles[index],value:this.value()};if(this.options.values&&this.options.values.length){uiHash.value=this.values(index);uiHash.values=this.values()}return this._trigger("start",event,uiHash)},_slide:function(event,index,newVal){var otherVal,newValues,allowed;if(this.options.values&&this.options.values.length){otherVal=this.values(index?0:1);if((this.options.values.length===2&&this.options.range===true)&&((index===0&&newVal>otherVal)||(index===1&&newVal<otherVal))){newVal=otherVal}if(newVal!==this.values(index)){newValues=this.values();newValues[index]=newVal;allowed=this._trigger("slide",event,{handle:this.handles[index],value:newVal,values:newValues});otherVal=this.values(index?0:1);if(allowed!==false){this.values(index,newVal,true)}}}else{if(newVal!==this.value()){allowed=this._trigger("slide",event,{handle:this.handles[index],value:newVal});if(allowed!==false){this.value(newVal)}}}},_stop:function(event,index){var uiHash={handle:this.handles[index],value:this.value()};if(this.options.values&&this.options.values.length){uiHash.value=this.values(index);uiHash.values=this.values()}this._trigger("stop",event,uiHash)},_change:function(event,index){if(!this._keySliding&&!this._mouseSliding){var uiHash={handle:this.handles[index],value:this.value()};if(this.options.values&&this.options.values.length){uiHash.value=this.values(index);uiHash.values=this.values()}this._trigger("change",event,uiHash)}},value:function(newValue){if(arguments.length){this.options.value=this._trimAlignValue(newValue);this._refreshValue();this._change(null,0);return}return this._value()},values:function(index,newValue){var vals,newValues,i;if(arguments.length>1){this.options.values[index]=this._trimAlignValue(newValue);this._refreshValue();this._change(null,index);return}if(arguments.length){if($.isArray(arguments[0])){vals=this.options.values;newValues=arguments[0];for(i=0;i<vals.length;i+=1){vals[i]=this._trimAlignValue(newValues[i]);this._change(null,i)}this._refreshValue()}else{if(this.options.values&&this.options.values.length){return this._values(index)}else{return this.value()}}}else{return this._values()}},_setOption:function(key,value){var i,valsLength=0;if($.isArray(this.options.values)){valsLength=this.options.values.length}$.Widget.prototype._setOption.apply(this,arguments);switch(key){case"disabled":if(value){this.handles.filter(".ui-state-focus").blur();this.handles.removeClass("ui-state-hover");this.handles.attr("disabled","disabled");this.element.addClass("ui-disabled")}else{this.handles.removeAttr("disabled");this.element.removeClass("ui-disabled")}break;case"orientation":this._detectOrientation();this.element.removeClass("ui-slider-horizontal ui-slider-vertical").addClass("ui-slider-"+this.orientation);this._refreshValue();break;case"value":this._animateOff=true;this._refreshValue();this._change(null,0);this._animateOff=false;break;case"values":this._animateOff=true;this._refreshValue();for(i=0;i<valsLength;i+=1){this._change(null,i)}this._animateOff=false;break}},_value:function(){var val=this.options.value;val=this._trimAlignValue(val);return val},_values:function(index){var val,vals,i;if(arguments.length){val=this.options.values[index];val=this._trimAlignValue(val);return val}else{vals=this.options.values.slice();for(i=0;i<vals.length;i+=1){vals[i]=this._trimAlignValue(vals[i])}return vals}},_trimAlignValue:function(val){if(val<=this._valueMin()){return this._valueMin()}if(val>=this._valueMax()){return this._valueMax()}var step=(this.options.step>0)?this.options.step:1,valModStep=(val-this._valueMin())%step;alignValue=val-valModStep;if(Math.abs(valModStep)*2>=step){alignValue+=(valModStep>0)?step:(-step)}return parseFloat(alignValue.toFixed(5))},_valueMin:function(){return this.options.min},_valueMax:function(){return this.options.max},_refreshValue:function(){var oRange=this.options.range,o=this.options,self=this,animate=(!this._animateOff)?o.animate:false,valPercent,_set={},lastValPercent,value,valueMin,valueMax;if(this.options.values&&this.options.values.length){this.handles.each(function(i,j){valPercent=(self.values(i)-self._valueMin())/(self._valueMax()-self._valueMin())*100;_set[self.orientation==="horizontal"?"left":"bottom"]=valPercent+"%";$(this).stop(1,1)[animate?"animate":"css"](_set,o.animate);if(self.options.range===true){if(self.orientation==="horizontal"){if(i===0){self.range.stop(1,1)[animate?"animate":"css"]({left:valPercent+"%"},o.animate)}if(i===1){self.range[animate?"animate":"css"]({width:(valPercent-lastValPercent)+"%"},{queue:false,duration:o.animate})}}else{if(i===0){self.range.stop(1,1)[animate?"animate":"css"]({bottom:(valPercent)+"%"},o.animate)}if(i===1){self.range[animate?"animate":"css"]({height:(valPercent-lastValPercent)+"%"},{queue:false,duration:o.animate})}}}lastValPercent=valPercent})}else{value=this.value();valueMin=this._valueMin();valueMax=this._valueMax();valPercent=(valueMax!==valueMin)?(value-valueMin)/(valueMax-valueMin)*100:0;_set[self.orientation==="horizontal"?"left":"bottom"]=valPercent+"%";this.handle.stop(1,1)[animate?"animate":"css"](_set,o.animate);if(oRange==="min"&&this.orientation==="horizontal"){this.range.stop(1,1)[animate?"animate":"css"]({width:valPercent+"%"},o.animate)}if(oRange==="max"&&this.orientation==="horizontal"){this.range[animate?"animate":"css"]({width:(100-valPercent)+"%"},{queue:false,duration:o.animate})}if(oRange==="min"&&this.orientation==="vertical"){this.range.stop(1,1)[animate?"animate":"css"]({height:valPercent+"%"},o.animate)}if(oRange==="max"&&this.orientation==="vertical"){this.range[animate?"animate":"css"]({height:(100-valPercent)+"%"},{queue:false,duration:o.animate})}}}});$.extend($.ui.slider,{version:"1.8.13"})}(jQuery));

//<![CDATA[
    $(document).ready( function() {

	$(".read").click(function(){
		$(".hideAnnounce").fadeToggle("slow", "linear");
		$(".read").hide();
		$(".closeText").show();
	});

	$(".closeText").click(function(){
		$(".hideAnnounce").fadeToggle("slow", "linear");
		$("html, body").animate({ scrollTop: 0 }, "slow");
		$(".closeText").hide();
		$(".read").show();
	});

	$(".showbottom").click( function() {
		if ($(this).next('div').css('display') == 'none') {
			$(this).next('div').slideDown();
		} else {
			$(this).next('div').slideUp();
		}

		if ($(this).parent().next('div').css('display') == 'none') {
			$(this).parent().next('div').slideDown();
		} else {
			$(this).parent().next('div').slideUp();
		}

		return false;
	});


	$(".appendregion").click( function() {
		$.cookie('showwelcome', $(".ik_select_link_text").html());

		$.fancybox.close('#welcome');
		return false;
	});


	/*$('.questions').qtip({
		content: 'Подсказка для вас!',
		show: 'mouseover',
		position: {
		  corner: {
			 target: 'topMiddle',
			 tooltip: 'bottomLeft'
		  }
		},
	   style: {
		  name: 'cream' // Inherit from preset style
	   }
	});		*/

	$(".questions").click( function() {
		return false;
	});

	$(".ur_address").click( function() {
		if (this.checked) {
			$(".ur_address_block").slideUp();
		} else {
			$(".ur_address_block").slideDown();
		}
	});




        // Выбор всех
        //При клике на ссылку "Все", активируем checkbox
        $("a[href='#select_all']").click( function() {
           $("#" + $(this).attr('rel') + " input:checkbox:enabled").attr('checked', true);
            return false;
        });
		$(".welcome").fancybox({
				padding : 0,
				scrolling : 'no'
			});

		var checkInfo = $.cookie('showwelcome');
		if (checkInfo == '' || !checkInfo) {
			//$.fancybox.open("#welcome");// auto open #welcome on load page
		} else {
			if (checkInfo != 'Москва' && checkInfo != 'Балашиха' && checkInfo != 'Зеленоград' && checkInfo != 'Одинцово') {
				$(".useraddress").css('display','block');
			}
			//useraddress
		}

		$(".fancyboximg").fancybox({
				padding : 0,
				scrolling : 'no'
			});

		/*$(".inyourcart").fancybox({
				padding : 0,
				scrolling : 'no',
				wrapCSS : 'incart'
			});	*/
		$("#prev").fancybox({
				openEffect	: 'elastic',
				closeEffect	: 'elastic',
				wrapCSS : 'prev',
				helpers : {
					title : {
						type : 'inside'
					}
				}
			});


		$('#closeFancyBox').click(function() {
			$.fancybox.close('inyourcart');
			return false;
		});


		$(".amountchange").keydown(function(e){

			var thisammount = parseInt($(this).val());

			if (e.keyCode == 38) {
				var changeAmm = thisammount + 1;

				if (changeAmm != 100) {
					$(this).val(changeAmm);
				}
			}

			if (e.keyCode == 40) {
				var changeAmm = thisammount - 1;

				if (changeAmm != 0) {
					$(this).val(changeAmm);
				}
			}
		});


		$('#closeFancyBoxId').click(function() {
			var id = $(this).attr('closeid');
			$.fancybox.close('addYouCart_'+id);
			return false;
		});

		$('.fancybox').click(function() {
            if ($(this).attr('hf')){
			 $.fancybox.open($(this).attr('hf'));
             console.log('common2');
             return false;
            }

		});

		/*
		$(".fancybox4").fancybox({
			scrolling : 'auto',
			preload   : true,
			maxWidth	: 600,
			maxHeight	: 250,
			padding :  [35, 0, 0, 0],
			scrolling : 'no',
		});
		*/

		$('.fancybox4').click(function() {
			$.fancybox.open($(this).attr('hf'));
			return false;
		});

		if ($(".category-path").length > 0){
			$(".breadcrumbs").html($(".category-path").html());
		}

		$('.mail').click(function() {
			$.fancybox.open('#sendMail',{
				padding : 0,
				scrolling : 'no',
			});
			return false;
		});

		$('.sendMailButton').click(function() {
			if ($('#youname').val() == '') { alert('Укажите имя'); return false; }
			if ($('#youmail').val() == '') { alert('Укажите вашу почту'); return false; }
			if ($('#youmessage').val() == '') { alert('Напишите что-нибудь.'); return false; }
			if ($('#friendmail').val() == '') { alert('Укажите почту друга'); return false; }

			$.ajax({
			  type: "POST",
			  url: '/sendMail.php',
			  data: {
			  	action: "send",
				youname: $('#youname').val(),
				youmail: $('#youmail').val(),
				youmessage: $('#youmessage').val(),
				friendmail: $('#friendmail').val(),
				thisPage: $(location).attr('href')
			  },
			  success: function(data) {
				$('#sendMail div.inner').html(data);
			  }
			});
			return false;
		});




		$('.countbox a').click(function() {
			var action = $(this).attr('class');
			var block = $(this).closest('td.count').find('input#amount');

				 if (action == 'plus')
				 {

					//$(this).parent().parent().siblings('.price').html('a');
					//--
					/*var slot_price = $(this).parent().parent().siblings('.price');
					var int_price = + slot_price.html().replace(/\D+/g,"");

					slot_price.html(((int_price/+ block.val())*(+ block.val()+1))+' руб.');
					var pr = $(this).parent().parent().siblings('.name');
					var nm_item = $(pr).children('.name2').children('a').html();
					var id_item = GetIdItem(nm_item);
					slot_price.html(window.priceAj.items[id_item].total_tax);
					*/

					//RecalcPrice(this, +block.val(), +1);
					RunTimer();
					block.val(+ block.val() + 1);

				 }
				 else if (action == 'minus' && block.val() > 1)
				 {

					//$(this).parent().parent().siblings('.price').html('a');
					/*var slot_price = $(this).parent().parent().siblings('.price');
					var int_price = + slot_price.html().replace(/\D+/g,"");
					slot_price.html(((int_price/+ block.val())*(+ block.val()-1))+' руб.');
					$($(this).parent().parent().siblings('.name') > 'a').html('111');*/
					//RecalcPrice(this, +block.val(), -1);
					RunTimer();
					block.val(+ block.val() - 1);

				 }
      });



    });

$(function(){

//            $('#leftmenu_accordion').accordion({
//                canToggle: true,
//                canOpenMultiple: true
//            });

            $('#search_acco').accordion({
                canToggle: true,
                canOpenMultiple: true
            });
            $('.search_acco').accordion({
                canToggle: true,
            });
			$('#cabinet').accordion({
                canToggle: true,
                canOpenMultiple: true
            });
            $('#tovar_acco').accordion({
                canToggle: true,
                canOpenMultiple: true
            });
            $('#page_acco').accordion({
                canToggle: true,
                canOpenMultiple: true
            });
            $(".loading").removeClass("loading");
			// no autowidth
			$(".select_noautowidth").ikSelect({
				autoWidth:false,
				customClass: "select_black",
				ddCustomClass: "select_black_block"
			});
			// no autowidth
			$(".select_noautowidth2").ikSelect({
				autoWidth: false,
				ddFullWidth: false,
				customClass: "select_black",
				ddCustomClass: "select_black_block"
			});
			$(".select_noautowidth3").ikSelect({
				autoWidth: false,
				ddFullWidth: false,
				customClass: "select_black",
				ddCustomClass: "select_black_block",
				ddMaxHeight: 300
			});
		var tabContainers = $('div.tabs > div'); // получаем массив контейнеров
		tabContainers.hide().filter(':first').show(); // прячем все, кроме первого
		if( location.hash == '#comment' ){
			tabContainers.hide();
			$('#item_rev').show();
		}
		// далее обрабатывается клик по вкладке
		$('div.tabs ul.tabNavigation a').click(function () {
			tabContainers.hide(); // прячем все табы
			tabContainers.filter(this.hash).show(); // показываем содержимое текущего
			$('div.tabs ul.tabNavigation a').removeClass('selected'); // у всех убираем класс 'selected'
			$(this).addClass('selected'); // текушей вкладке добавляем класс 'selected'
			return false;
		}).filter(':first').click();
		if(location.hash == '#comment'){
			$('div.tabs ul.tabNavigation a').filter('.otzuv').click();
		}

		if ($("input#minCost").val() == "") {
			$("input#minCost").val(0);
		}

		if ($("input#maxCost").val() == "") {
			$("input#maxCost").val(20000);
		}

		var start = parseInt($("input#minCost").val());
		var rstop = parseInt($("input#maxCost").val());

		if (!start) { var start = 0; }
		if (!rstop) { var rstop = 20000; }

		$("#slider").slider({
			min: 0,
			max: 20000,
			values: [start,rstop],
			range: true,
			stop: function(event, ui) {
				$("input#minCost").val($("#slider").slider("values",0));
				$("input#maxCost").val($("#slider").slider("values",1));
			},
			slide: function(event, ui){
				$("input#minCost").val($("#slider").slider("values",0));
				$("input#maxCost").val($("#slider").slider("values",1));
			}
		});

		var start = parseInt($("input#minCost2").val());
		var rstop = parseInt($("input#maxCost2").val());

		if (!start) { var start = 0; }
		if (!rstop) { var rstop = 20000; }


		$("#slider2").slider({
			min: 0,
			max: 20000,
			values: [start,rstop],
			range: true,
			stop: function(event, ui) {
				$("input#minCost2").val($("#slider2").slider("values",0));
				$("input#maxCost2").val($("#slider2").slider("values",1));
			},
			slide: function(event, ui){
				$("input#minCost2").val($("#slider2").slider("values",0));
				$("input#maxCost2").val($("#slider2").slider("values",1));
			}
		});

		$('li.eshop-cat-tree__item').each(function(index) {
            var value = $(this).attr('hidename');
            if (typeof value != 'undefined') {
    			var checkInfo = $.cookie($(this).attr('hidename'));

    			if (checkInfo == $(this).attr('hidename') && $(this).attr('hidename')[0] != "") {
    				$(this).addClass('active');
    				$(this).attr('fade','true');

    				$('#named_' + $(this).attr('hidename')).removeAttr("style");
    				//console.log('yes');
    			}
    			//if ($(this).attr('fade') == "true") {
    				//$(this).addClass('active');
    			//}
    			//alert(index + ': ' + $(this).text());
            }
		});

		$(".eshop-cat-tree__item").click(function () {
			   if ($(this).hasClass('active')) {
				   $.cookie($(this).attr('hidename'), null);
				   $(this).removeAttr('fade','true');
			   } else {
				   $(this).attr('fade','true');
				   $.cookie($(this).attr('hidename'), $(this).attr('hidename'));
			   }
		});

		$(".newwindow").click(function () {
			 window.open($(".newwindow").attr('href'), "joe", "config = toolbar=0, scrollbars=1, width=600, height=500");
			 return false;
		});


		$(".cart_img").click(function () {
			 location.href='/cart/';
		});

		$(".tred").click(function () {
			 //picture_gallery
			 $('.picture_gallery').html('<object width="200" height="152" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0"><param name="SRC" value="' + $(".tred").attr('href') + '"><embed src="' + $(".tred").attr('href') + '" width="200" height="152"></embed></object>');
			 //location.href=$(".tred").attr('href');
			 return false;
		});


		$('.eshop-item-small__compare-link').each(function(index) {
			var paramvalue = getParam($(this).attr('compareprod'));

			if (paramvalue == "yes") {
				$(this).html('<input type="checkbox" title="Добавить к сравнению (для добавления товаров в сравнение воспользуйтесь ссылкой внизу списка)" checked>Добавлено к сравнению');
			}else $(this).html('Добавить к сравнению');
			//$(this).attr('compareprod')
		});


		$(".eshop-item-small__compare-link").click(function () {
			   setParam($(this).attr('compareprod'),'yes');
		});

		$("#sravnenieCount").click(function () {
			   azzrael_delete_cookie();
		});


		$("#bookmark").click(function(e){
		  // Отменяем действие браузера по умолчанию.
		  // Теперь при нажатии на ссылку вы не перейдете на другую страницу.
		  e.preventDefault();

		  var bookmarkUrl = this.href;
		  var bookmarkTitle = this.title;

		  if (window.sidebar) // For Mozilla Firefox Bookmark
			window.sidebar.addPanel(bookmarkTitle, bookmarkUrl,"");
		  else if( window.external || document.all) // For IE Favorite
			window.external.AddFavorite( bookmarkUrl, bookmarkTitle);
		  else if(window.opera) { // For Opera Browsers
			$("a.jQueryBookmark").attr("href", bookmarkUrl);
			$("a.jQueryBookmark").attr("title", bookmarkTitle);
			$("a.jQueryBookmark").attr("rel", "sidebar");
		  }
		  else { // Для других браузеров
			alert('Нажмите Ctrl+D, чтобы добавить страницу в закладки.');
			return false;
		  }

		  return false;
		});
});

var _cn = 'compare_cel';

function azzrael_delete_cookie(){
     $.cookie(_cn, null);
}

function azzrael_getArrayCookie(){
     var cstr = $.cookie(_cn);
     var kv = [];
     var arr_c = {};
     if(cstr != null){
         var arr = cstr.split(';');
         for(var i = 0; i < arr.length-1; i++ ){
             kv = arr[i].split('=');
             arr_c[kv[0]] = kv[1];
         }
     }
     return  arr_c;
}

function getParam(name){
     var arr_c = azzrael_getArrayCookie();
     return (arr_c[name] == null )? 0 : arr_c[name];
}

function setParam(name, value){
     var arr_c = azzrael_getArrayCookie();
     arr_c[name] = value;
     var cstr = '';
     for (var key in arr_c) {
         cstr += key + '=' + arr_c[key]+ ';';
     }
     $.cookie(_cn,cstr);
}
/*// Геотаргетинг
function linklist(what){
var selectedopt=what.options[what.selectedIndex];
//document.cookie = "geoname=" + selectedopt.getAttribute('target') + "; path=/";
$.cookie('geo', null, {path: "/"});
$.post(
		'/json/json.php',
		{geo:  selectedopt.getAttribute('target')},
		function(data){
			dataGeo = JSON.parse(data);
			$.cookie('geo', data, {path: "/"});//запись в куки
			window.location.reload();
			}
	);
}
*/
function Scroll_menu(obj)
{
	$(obj).parent().parent().parent().siblings('.etree_lev2').slideToggle('normal');
	return true;
}

function Scroll_menu_select(obj)
{
	//$(obj).siblings('.etree_lev2').slideToggle('normal');slideToggle
	//$(obj).parent().parent().parent().children('.etree_lev2').slideToggle('normal');
	$(obj).parent().parent().children('.panel').children('.etree_lev2').slideToggle('normal');
	return true;
}
function show_detaile(script) {
  var w_width = 550;
  var w_height = 450;
console.log(script);
  if (w_height > window.screen.availHeight)
    w_height = window.screen.availHeight;
  if (w_width > window.screen.availWidth)
    w_width = window.screen.availWidth;

  window.open(script, "pic", "resizable=yes, status=yes, scrollbars=yes, width=" + w_width + ", height=" + w_height);
  //return false;
}
function valid_contact()
{
	var name = $('input[name="visible_firstname"]').attr('value');
	var phone = $('input[name="contact"]').attr('value');
	var message = '';
	message += 'Ошибка заполнения данных!<br>';
	var error = false;

	if (name.trim().length == '') {
		error = true;
		message += ' Введите Ваше имя и фамилию.';
	}
/*	if (phone.trim().length == '') {
		error = true;
		message += ' Введите Ваш номер телефона.';
	}*/

	if (error) {
		alert(message);
		return false;
	} else {
		return true;
	}
}
function valid_phone(cform)
{
	var errmsg = '<b>Некорректный мобильный номер.</b><br />Необходимо корректно ввести номер в международном формате, например +7(921)000-00-00.';
        var     flag = true;
         		var s = $('input[name="phone"]').attr('value');//cform.phone.value;//$('input[name="phone"]').attr('value');
				if(!s)return true;
				s = s.replace(/[\(\)-]/gi, '');
				if(s.length > 10 && s.length < 13)
				{

					if( !isNaN(s) )
					{
						if(s.indexOf('+7') == 0){
						s = s.substr(2,s.length-1);
						}else
						if( s[0]=='8' )
						{
							s = s.substr(1,s.length-1);
						}

						if( s.indexOf('+') != -1 || s.length>10)
						{
							flag = false;
						}
					}else
                    {
                        flag = false;
                    }
				}else
				if(s.length != 10 || isNaN(s)) flag = false;
				if(!flag){alert(errmsg); return false;}
    			else
				{
				$('input[name="phone"]').attr('value', s);
				}
				}

				function valid_log(cform)
				{

				var errmsg = '<b>Некорректный мобильный номер.</b><br />Необходимо корректно ввести номер в международном формате, например +7(921)000-00-00.';
        var     flag = true;
				// проверка логина на содержание телефона
				if(typeof(cform.username)== "undefined") return true;
				var s = cform.username.value;//$('input[name="username"]').attr('value');//cform.phone.value;//$('input[name="phone"]').attr('value');
				s = s.replace(/[\(\)-]/gi, '');
				if(s.length > 10 && s.length < 13)
				{

					if( !isNaN(s) )
					{
						if(s.indexOf('+7') == 0){
						s = s.substr(2,s.length-1);
						}else
						if( s[0]=='8' )
						{
							s = s.substr(1,s.length-1);
						}

						if( s.indexOf('+') != -1 || s.length>10)
						{
							flag = false;
						}
					}
				}
				if(!flag){alert(errmsg); return false;}
    			else
				{
				cform.username.value = s;//$('input[name="username"]').attr('value', s);
				return true;
				}

	}

function showdetail(script) {
  if (  (script.substring(0,7).toLowerCase()) != "http://"  &&  (script.substring(0,8).toLowerCase() != "https://" ) ){
    script = frontBaseHref + script;
  }

  var w_width = '450px';
  var w_height = '500px';

  if (w_height > window.screen.availHeight)
    w_height = window.screen.availHeight;
  if (w_width > window.screen.availWidth)
    w_width = window.screen.availWidth;

  window.open(script, "pic", "resizable=yes, status=yes, scrollbars=yes, width=" + w_width + ", height=" + w_height);
  //return false;
}





function sh_fieldship(id){
arr = [1, 2, 3];
	if( !(id in arr) ){
		$('input[data-ami-method-id="11"]').parent().hide();
		$('input[data-ami-method-id="12"]').parent().hide();
		$('input[data-ami-method-id="17"]').parent().show();
		$('input[data-ami-method-id="78"]').parent().show();
	}else{
		$('input[data-ami-method-id="17"]').parent().hide();
		$('input[data-ami-method-id="78"]').parent().hide();
		$('input[data-ami-method-id="11"]').parent().show();
		$('input[data-ami-method-id="12"]').parent().show();
	}

	if(id == 1){
	$('#sam1_d').show();
	$('#sam1').attr('checked', 'checked');
	$('#sam2').removeAttr('checked');
	$('#sam2_d').hide();
	}else{
	$('#sam2_d').show();
	$('#sam2').attr('checked', 'checked');
	$('#sam1').removeAttr('checked');
	$('#sam1_d').hide();
	}
	for(i=0; i < 20; i++){
		selec = (i < 10)?'#custom_shipping_0'+i:'#custom_shipping_'+i;
		$(selec).hide();
	}
	$('.eshop-ordering__radio').removeAttr('checked');

	$('#metro_piter').hide();
	$('#metro_moscow').hide();
	if(id == 1){
	$('#metro_moscow').show();
	$('#metro_piter').hide();
	$('#metro_piter').attr('name', 'station_custom_del');
	$('#metro_moscow').attr('name', 'station_custom');
	}else
	if( id == 2 ){
	$('#metro_piter').show();
	$('#metro_moscow').hide();
	$('#metro_moscow').attr('name', 'station_custom_del');
	$('#metro_piter').attr('name', 'station_custom');
	}
}

function onchange_select(obj){
 id = $(obj).val();
 sh_fieldship(id);
}

function sh_field_flt(obj, per){
	tmp = $(obj);
	if (per == void 0){
		per = 2;
	}
	if(per==2)
	tmp.parent().parent().children('div[sh="bl"]').slideToggle('normal');
	else
	tmp.parent().parent().parent().children('div[sh="bl"]').slideToggle('normal');
	tmp = tmp.parent();
	tmp.toggleClass('slp_hd');
	tmp.toggleClass('slp_sh');
}

function advResortSubmits(ccol,cdim,ccolname,cdimname){
var sform = document.forms[_cms_document_form];
//var link = _cms_script_link;
//sform.elements['action'].value = 'rsrtme';
sform.elements[ccolname].value = ccol;
sform.elements[cdimname].value = cdim;
AMI.Form.Filter.submit(document.forms[_cms_document_form]);
return false;
}

function sh_geo(data){
	if(data)
	{
		if( $(".geo_phone").length ){
			$(".geo_phone").html(data['phone']);
		}
		if( $(".geo_name").length ){
			$(".geo_name").html(data['text']);
		}
		if( $(".geo_banner").length ){
			if(typeof(data['link'])!='undefined' && typeof(data['link'].url_img)!='undefined'){
				$(".geo_banner").html("<div class='mailme' id='geo_banner'></div>");
				elem = document.getElementById('geo_banner');
				inner_flash(data['link'].url_flash, data['link'].url_img, data['link'].alt, data['link'].reference, data['link'].width, data['link'].height);
			}
		}//<a href='/informacija/kredit/'><img src='/mod_files/ce_images/credit.gif'></a>
		/*if(data['id']==1 || data['id']==2){
				$(".akcii_img").html("<br /><script language='JavaScript' charset='utf-8' src='/img/js_akc_baner.js'></script>");
		}else
		if(data['id']!=1 && data['id']!=2){
			$(".akcii_img").html("<script language='JavaScript' charset='utf-8' src='/img/js_akc_baner.js'></script>");
		}*/
	}
}

function unserialize(oString){
        var oData = null;
        if(oString.charAt(0) == 'a' || oString.charAt(0) == 'o'){
            var isArray = oString.charAt(0) == 'a';
            if(isArray){
                oData = [];
            }else{
                oData = new Object();
            }
            var dataLength = '';
            var isShouldBeKey = isArray ? false : true;
            var keyValue = '';
            for(var i = 1; i < oString.length; i++){
                if(oString.charAt(i).match(/\d/)){
                    dataLength += oString.charAt(i);
                }else if(oString.charAt(i) == '.'){
                    dataLength = parseInt(dataLength);
                    if(isShouldBeKey){
                        keyValue = oString.substr(i + 1, dataLength);
                        isShouldBeKey = false;
                    }else{
                        if(isArray){
                            oData[oData.length] = decodeURIComponent(oString.substr(i + 1, dataLength));
                        }else{
                            oData[keyValue] = decodeURIComponent(oString.substr(i + 1, dataLength));
                            isShouldBeKey = true;
                        }
                        keyValue = '';
                    }
                    i = i + dataLength;
                    dataLength = '';
                }
            }
        }
        return oData;
    }
//----------------------------------------------------------------------------------------------------------CONFIRM_CART


function cut_plus(str){
	if(typeof str == 'string')
	return str.replace(new RegExp("\\+",'g'),' '); else return '';
}

// Геотаргетинг ПЕРЕОПРЕДЕЛЕНА!(УДАЛИТЬ старую)
// function linklist(what){
// var selectedopt=what.options[what.selectedIndex];
// $.cookie('ctx', null, {path: "/"});
// $.post(
// 		'/geoip/resp_geo.php',
// 		{geo:  selectedopt.getAttribute('target')},
// 		function(data){
// 				if(data=='200'){
// 					if(rdr_cart) window.location.href='/cart'; else {
// 						var query = window.location.search;
// 						query = query.replace(/(ctx=[^&$]{1,3}(&|\s|$))/gim, '');
// 						window.location.search = query;
// 					}
// 					//window.location.reload();
// 				} else {
// 					$.fancybox.close();
// 					alert('Произошла ошибка! Не получилось поменять Ваш регион. Пожалуйста, обновите страницу и попробуйте ещё раз.');
// 				}
// 			}
// 	);
// }

// function change_region_cart(what){
// var selectedopt = what.options[what.selectedIndex];
// $.cookie('ctx', null, {path: "/"});
// $.post(
// 		'/geoip/resp_geo_new.php',
// 		{region_id:  selectedopt.getAttribute('target')},
// 		function(data){
// 				if(data=='200'){
// 					get_cart_info();
// 				} else {
// 					$.fancybox.close();
// 					alert('Произошла ошибка! Не получилось поменять Ваш регион. Пожалуйста, обновите страницу и попробуйте ещё раз.');
// 				}
// 			}
// 	);
// }


// function get_compare_items() {
// 		var value = $.cookie('comparison');
// 		if (value != null) {
// 			var str = $.trim(value,';');
// 			var ArrayData = value.split(';');
// 			for(var i = 0; i < ArrayData.length; i++)
// 			ArrayData[i] = parseInt(ArrayData[i], 10);
// 			ArrayData = $.grep(ArrayData,function(n){ return(n) });
// 			return ArrayData;
// 		} else {
// 			return false;
// 		}
// }

// function render_compare_popup(){
// 	// get_compare_items - функция возвращает массив id товаров для сравнения
// 	var items = get_compare_items();
// 	if(!Array.isArray(items)){
// 		// если получена ерунда
// 		items = new Array();
// 	}
// 	var url = '/compare/index.php?ids='+items.join(';');
// 	if(items.length){
// 		$('.compare_popup').show();
// 	} else {
// 		$('.compare_popup').hide();
// 	}
// 	$('#compare_count').html(items.length);
// 	$('#compare_link').attr('href', url);
// 	$('#clear_all_compare').on('click', function(){compare_interface(0,'clear_all'); return false;});
// }

// function compare_interface(id, action){
// $.post('/compare/interface.php',
// 		{
// 			item_id: id,
// 			action: action
// 		}, function(data){
// 			switch(data){
// 				case '21': alert('Произошла ошибка при удалении товара. Пожалуйста, попробуйте перезагрузить страницу и попробовать вновь.'); break;
// 				case 'ok': render_compare_popup();render_compare_button();break;
// 				case '32': if(confirm('В списке сравнения присутствуют товары из разных категорий. Хотите очистить список?'))
// 								compare_interface(id, 'important'); break;
// 			}
// 		}

// 		);
// }

// function on_add_compare(obj){
// 		var id = $(obj).attr('data-id');
// 		compare_interface(id, 'add');
// 		return false;

// }

// function on_del_compare(obj){
// 		var id = $(obj).attr('data-id');
// 		compare_interface(id, 'remove');
// 		return false;
// 	}

// function render_compare_button(){
// 	var items = get_compare_items();
// 	if(!Array.isArray(items)){
// 		// если получена ерунда
// 		items = new Array();
// 	}
// 	$('.compare_check').html('Добавить к сравнению').removeClass('del_compare').addClass('add_compare').attr('onclick', 'on_add_compare(this)');
// 	for(var i in items){
// 		$('#compare_'+items[i]).html('<input type="checkbox" checked /> Добавлено к сравнению').removeClass('add_compare').addClass('del_compare').attr('onclick', 'on_del_compare(this)');
// 	}

// }
/* Start delivery */
function getLocalDeliveryPrice(u_id, u_id_city, u_lenght, u_region_id){
	$("#dostavka").prepend('<span class="delivery_loader"><label>Доставка: </label><img src="/img/load.gif" /></span>');
	$.ajax({
		url: "/delivery/type.php",
		type: "post",
		data: { id :  u_id, id_city :  u_id_city, lenght : u_lenght, region_id : u_region_id },
		success: function(respons){
			$(".delivery_loader").remove();
			$("#dostavka").prepend(respons);
		}
	});
}

// function getPecom(data) {
// 	$("#dostavka_").prepend('<span class="delivery_loader_"><label>Доставка: </label><img src="/img/load.gif" /></span>');
// 	$.ajax({
// 		url: "/delivery/pecom.php",
// 		type: "post",
// 		data: data,
// 		success: function(respons){
// 			$(".delivery_loader_").remove();
// 			$("#dostavka_").append(respons);
// 			if ($(".rem_str_one #dostavka").html()) {
// 				$(".rem_str_one + .rem_str_two #dostavka_ label").text("Доставка "); // или
// 			}
// 		}
// 	});
// }

function getJde(data) {
	$.ajax({
		url: "/delivery/jde.php",
		type: "post",
		data: data,
		success: function(respons){
			//console.log(respons);
			//$(".t_delivery tbody").append(respons);
		}
	});
}
function getRegion(id){
	switch(id){
		case '524925': return 'Москва'; break;
		case '524894': return 'Москва'; break;
		case '536203': return 'Санкт-Петербург'; break;
		case '536199': return 'Санкт-Петербург'; break;
		case '1490542': return 'Екатеринбург'; break;
		case '559838': return 'Нижний Новгород'; break;
		case '501165': return 'Ростов-на-Дону'; break;
		case '625143': return 'Минск'; break;
		case '620134': return 'Минск'; break;
		case '625073': return 'Минск'; break;
		case '625142': return 'Минск'; break;
		case '628035': return 'Минск'; break;
		case '628281': return 'Минск'; break;
		case '629631': return 'Минск'; break;
		default: return 'Москва';
	}
}

$( ".show_delivery_rf_free" ).live( "mouseover", function(event) {
    $('<div class="mytooltip">"Доставка данной модели будет осуществлена абсолютно бесплатно, в не зависимости от того, в каком регионе вы находитесь"</div>').appendTo('body');
    positionTooltip(event);
});
$( ".show_delivery_rf_free" ).live( "mouseout", function(event) {
    $( "div.mytooltip" ).remove();
});
$( ".show_delivery_msk_free" ).live( "mouseover", function(event) {
    $('<div class="mytooltip">"Стоимость доставки по г. Москва – бесплатно (в пределах МКАД), при дальнейшей оплате 30 руб/км"</div>').appendTo('body');
    positionTooltip(event);
});
$( ".show_delivery_msk_free" ).live( "mouseout", function(event) {
    $( "div.mytooltip" ).remove();
});
$( ".show_delivery_spb_free" ).live( "mouseover", function(event) {
    $('<div class="mytooltip">"Стоимость доставки по г. Санкт-Петербург – бесплатно (в пределах КАД), при дальнейшей оплате 30 руб/км"</div>').appendTo('body');
    positionTooltip(event);
});
$( ".show_delivery_spb_free" ).live( "mouseout", function(event) {
    $( "div.mytooltip" ).remove();
});
$( ".show_delivery_ekb_free" ).live( "mouseover", function(event) {
    $('<div class="mytooltip">"Стоимость доставки по г. Екатеринбург  – бесплатно (в пределах ЕКАД), при дальнейшей оплате 30 руб/км"</div>').appendTo('body');
    positionTooltip(event);
});
$( ".show_delivery_ekb_free" ).live( "mouseout", function(event) {
    $( "div.mytooltip" ).remove();
});
$( ".show_delivery_nnov_free" ).live( "mouseover", function(event) {
	$('<div class="mytooltip">"Стоимость доставки по г. Нижний Новгород  – бесплатно (в пределах HКАД), при дальнейшей оплате 30 руб/км"</div>').appendTo('body');
	positionTooltip(event);
});
$( ".show_delivery_nnov_free" ).live( "mouseout", function(event) {
	$( "div.mytooltip" ).remove();
});
$( ".show_delivery_rnd_free" ).live( "mouseover", function(event) {
	$('<div class="mytooltip">"Стоимость доставки по г. Ростов-на-Дону  – бесплатно (в пределах города), при дальнейшей оплате 30 руб/км"</div>').appendTo('body');
	positionTooltip(event);
});
$( ".show_delivery_rnd_free" ).live( "mouseout", function(event) {
	$( "div.mytooltip" ).remove();
});

$( ".show_delivery_msk" ).live( "mouseover", function(event) {
    $('<div class="mytooltip">"Стоимость доставки по г. Москва – 300 руб. (в пределах МКАД), при дальнейшей оплате 30 руб/км"</div>').appendTo('body');
    positionTooltip(event);
});
$( ".show_delivery_msk" ).live( "mouseout", function(event) {
    $( "div.mytooltip" ).remove();
});
$( ".show_delivery_spb" ).live( "mouseover", function(event) {
    $('<div class="mytooltip">"Стоимость доставки по г. Санкт-Петербург – 300 руб. (в пределах КАД), при дальнейшей оплате 30 руб/км"</div>').appendTo('body');
    positionTooltip(event);
});
$( ".show_delivery_spb" ).live( "mouseout", function(event) {
    $( "div.mytooltip" ).remove();
});
$( ".show_delivery_ekb" ).live( "mouseover", function(event) {
    $('<div class="mytooltip">"Стоимость доставки по г. Екатеринбург  – 300 руб. (в пределах ЕКАД), при дальнейшей оплате 30 руб/км"</div>').appendTo('body');
    positionTooltip(event);
});
$( ".show_delivery_ekb" ).live( "mouseout", function(event) {
    $( "div.mytooltip" ).remove();
});
$( ".show_delivery_nnov" ).live( "mouseover", function(event) {
	$('<div class="mytooltip">"Стоимость доставки по г. Нижний Новгород  – 300 руб. (в пределах HКАД), при дальнейшей оплате 30 руб/км"</div>').appendTo('body');
	positionTooltip(event);
});
$( ".show_delivery_nnov" ).live( "mouseout", function(event) {
	$( "div.mytooltip" ).remove();
});
$( ".show_delivery_rnd" ).live( "mouseover", function(event) {
	$('<div class="mytooltip">"Стоимость доставки по г. Ростов-на-Дону  – 300 руб. (в пределах города), при дальнейшей оплате 30 руб/км"</div>').appendTo('body');
	positionTooltip(event);
});
$( ".show_delivery_rnd" ).live( "mouseout", function(event) {
	$( "div.mytooltip" ).remove();
});

$( ".delivery_company_free" ).live( "mouseover", function(event) {
    $('<div class="mytooltip">"Стоимость доставки до терминала транспортной компании "ПЭК" г. Москва – бесплатно + cтоимость доставки транспортной компанией "ПЭК" в Ваш город"</div>').appendTo('body');
    positionTooltip(event);
});
$( ".delivery_company_free" ).live( "mouseout", function(event) {
    $( "div.mytooltip" ).remove();
});
$( ".delivery_company" ).live( "mouseover", function(event) {
    $('<div class="mytooltip">"Стоимость доставки до терминала транспортной компании "ПЭК" г. Москва – 300 руб. + cтоимость доставки транспортной компанией "ПЭК" в Ваш город"</div>').appendTo('body');
    positionTooltip(event);
});
$( ".delivery_company" ).live( "mouseout", function(event) {
    $( "div.mytooltip" ).remove();
});

function positionTooltip(event){
    var tPosX = event.pageX - -8;
    var tPosY = event.pageY - 100;
    $('div.mytooltip').css({'color': '#A27D35', 'border-radius': '8px', 'border': '2px dashed #FFA544', 'padding': '8px', 'background': '#FBF7AA', 'width': '220px', 'position': 'absolute', 'top': tPosY, 'left': tPosX});
};
/* End delivery */

/* КАЛЬКУЛЯТОР */
$(document).ready(function(){

// function getPecomCalk(data) {
// 	$(".calk_result").prepend('<span class="calk_loader_"><img src="/img/load.gif" /></span>');
// 	$.ajax({
// 		url: "/calk/pecom.php",
// 		type: "post",
// 		data: data,
// 		success: function(respons){
// 			$(".calk_loader_").remove();
// 			$( ".calk_result" ).html(respons);
// 			//console.log(respons);
// 		}
// 	});
// }

$( ".add_calk_item" ).click(function() {
	var asdd = $(".all_unather").children();

	var main_cat_show = $('.main_cat_show').html();
	var count = $(".all_unather").children().length;
	var result = "."+asdd[count-1]["className"];

	var matches = result.match(/[0-9]+/g);
	num = parseInt(matches[0])+1;

	$( result ).after('<div class="unather'+num+'"><div class="main_cat_show">'+main_cat_show+'</div><br><div class="sub_main_cat_show"></div><br><div class="sub_item_cat_show"></div><br><div class="calk_input"></div></div>');
	return false;
});

$( ".del_calk_item" ).live( "click", function() {
	var asdd = $(this).parents();
	var result = "."+asdd[1]["className"];
	$(result).remove();
});

$(".main_cat").live("change", function(e) {
	var asdd = $(this).parents();
	var result = "."+asdd[5]["className"];
	var main_cat = $(this).val();
	var data = {
		"main_cat": main_cat
	};
	$.ajax({
		url: "/_local/front_functions.php",
		type: "post",
		data: data,
		success: function(respons){
			$( result+" .sub_main_cat_show" ).html('<table><tr><td class="pad"><label>Выберете бренд</label></td><td><select class="sub_main_cat">'+respons+'</select></td><td class="calk_info">Например: Brother </td></tr></table>');
		}
	});
});
$(".sub_main_cat").live("change", function(e) {
	var asdd = $(this).parents();
	var result = "."+asdd[5]["className"];
	var cat_item = $(this).val();
	var data = {
		"cat_item": cat_item
	};
	$.ajax({
		url: "/_local/front_functions.php",
		type: "post",
		data: data,
		success: function(respons){
			$( result+" .sub_item_cat_show" ).html('<table><tr><td class="pad"><label>Выберете модель</label></td><td><select class="sub_item_cat">'+respons+'</select></td><td class="calk_info">Например: Brother SM 360e </td></tr></table>');
		}
	});
});
$(".sub_item_cat").live("change", function(e) {
	var asdd = $(this).parents();
	var result = "."+asdd[5]["className"];
	var item_data = $(this).val();
	var data = {
		"item_data": item_data
	};
	$.ajax({
		url: "/_local/front_functions.php",
		type: "post",
		data: data,
		success: function(respons){
			$( result+" .calk_input" ).html(respons);
			//console.log(respons);
		}
	});
});

$('.calk_popup .calk_close_window, .calk_overlay').click(function (){
$('.calk_popup, .calk_overlay').css('opacity','0');
$('.calk_popup, .calk_overlay').css('visibility','hidden');
});

$('a.open_window').click(function (e){
$('.calk_popup, .calk_overlay').css('opacity','1');
$('.calk_popup, .calk_overlay').css('visibility','visible');
e.preventDefault();
});

$('a.open_window_').click(function (e){

var fwef = $('.pv_cont').offset().top;
$('.pv_cont').css('top', fwef+'px');

$('#layer_bg.pv_dark').css('display','block');
$('.pv_cont').css('display','block');
e.preventDefault();
});

$('.pv_cont .calk_close_window, .calk_overlay').click(function (){
$('#layer_bg.pv_dark').css('display','none');
$('.pv_cont').css('display','none');
});

$( ".add_to_cart" ).click(function() {
	var itemid = 0;
	//var eshop_cart_count = parseInt($("#eshop_cart_count").text());
	$( ".sub_item_cat option:selected" ).each(function() {
		itemid = $( this ).val();
		if(itemid){
			var defef = buy_dop_item(itemid, 0, 1, 1);
			$('.calk .popup_confirm_add_item').css('display','block');
		} else {

		}
	});
	return false;
});

// $( ".calk_send" ).click(function() {
// 	var from = $(".res_from").text();
// 	var to = $(".res_to").text();

// 	var summ_weight = 0;
// 	var summ_volume = 0;
// 	$('.in_weight').each(function(index, element) {
// 		summ_weight += parseFloat($(element).text().replace(",", "."));

// 	});
// 	$('.in_volume').each(function(index, element) {
// 		summ_volume += parseFloat($(element).text().replace(",", "."));

// 	});

// 	var data = {
// 		"from": from,
// 		"weights": summ_weight,
// 		"volumes": summ_volume,
// 		"to": to
// 	};

// 	$( ".calk_result" ).html('');
// 	if(from != '' && summ_weight != '' && summ_volume != '' && to != ''){
// 		//if(weight.match(/^[\d]+$/) && volume.match(/^[\d|\.?]+$/)){
// 		getPecomCalk(data);

// 	} else {
// 		$(".calk_result").prepend('<span class="red">Все поля должны быть заполнены!</span>');
// 	}
// });

$("input#search_to").live("keyup", function(e) {
    // Set Search String
    var search_string = $(this).val();
	if(search_string){
		$("select.result_to").css("display", "block");
	} else{
		$("select.result_to").css("display", "none");
	}
    // Do Search
    if(search_string !== ''){
        $.ajax({
            type: "POST",
            url: "/calk/search.php",
            data: { query: search_string },
            cache: false,
            success: function(result){
				$("select.result_to").html(result);
            }
        });
    }return false;
});

$( "select.result_from" ).change( from );
$( "select.result_to" ).change( to );

function from() {
	var val = $( ".result_from" ).val() || [];
	var text = $('.result_from').find(":selected").text();
	$( ".res_from" ).html(val);
	$( "#search_from" ).val(text);
}

function to() {
	var val = $( ".result_to" ).val() || [];
	var text = $('.result_to').find(":selected").text();
	$( ".res_to" ).html(val);
	$( "#search_to" ).val(text);
	$("select.result_to").css("display", "none");
}

$( "select.result_city" ).change( maincity );
function maincity() {
	var val = $( ".result_city" ).val() || [];
	var text = $('.result_city').find(":selected").text();
	$( ".res_to" ).html(val);
	$( "#search_to" ).val(text);
}

});


/* Calk End */

/* Begin Show Cotton Info */
function showCottonInfo(item) {

	var data_array = [];
	data_array["Полиэстр"] = '/mod_files/cotton_prop_page/poliester.html';
	data_array["Спандекс"] = '/mod_files/cotton_prop_page/spandex.html';
	data_array["Вискоза"] = '/mod_files/cotton_prop_page/viskoza.html';
	data_array["Хлопчатобумажная"] = '/mod_files/cotton_prop_page/xlopchatobuma.html';
	data_array["Лен"] = '/mod_files/cotton_prop_page/len.html';

    var myWindow = window.open(data_array[item], "", "width=600, height=600");
    //myWindow.document.write("<p>"+data_array[item]+"</p>");
	//console.log('"'+item+'"');
}
/* End Show Cotton Info */


$(document).ready(function(){
	// Редирект на Спб поддомен.
	if ("www.textiletorg.ru" == location.host && $('#input_city').val().indexOf("Санкт")!='-1') {
		var curentUrl = window.location.href;
		curentUrl = curentUrl.replace("www.","spb.");
		window.location.replace(curentUrl);
	}
	// if ($('#input_city').val().indexOf("Санкт")!='-1'){
	// 	window.location.replace("https://spb.textiletorg.ru/");
	// }
    /*
	if(frontBaseHref == '//textiletorg.by/') {
		$("#oneclickphone").attr("placeholder", "+375173884064").val("").focus().blur();
	} else {
		$("#oneclickphone").attr("placeholder", "+79161234567").val("").focus().blur();
	}
    */

	/* Open call me in page credit. */

	/* BEGIN Category heder info text. */
	$( ".showalltext" ).click(function() {
		$(".showalltext").hide();
		$(".textinfo").show();
		return false;
	});
	$( ".closealltext" ).click(function() {
		$(".textinfo").hide();
		$(".showalltext").show();
		return false;
	});
	/* BEGIN Category heder info text. */

	$(".spring_marafon").hover(function(){
		$( ".spring_marafon_info" ).css( "display", "block" );
	}, function(){
		$( ".spring_marafon_info" ).css( "display", "none" );
	});

/* ALL_CITY_LIST */


// $( ".alfabet" ).click(function() {

// 	$('.listalfabet li a').each(function() {
// 		$(this).removeClass('acl_activ');
//     });

// 	$(this).addClass("acl_activ");
// 	var item = $(this).attr('data-letter');
// 	//alert(item);
// 	var data = {
// 		"acl_item": item
// 	};
// 	$.ajax({
// 		url: "/_local/front_functions.php",
// 		type: "post",
// 		data: data,
// 		success: function(respons){
// 			$( ".allcity" ).html(respons);
// 			//console.log(respons);
// 		}
// 	});
// 	return false;
// });


/* ALL_CITY_LIST. */

/* New Slider Start */
var str = $( ".breadcrumbs .category-path__current" ).text();

/* New Slider End */

	/* Begin Akciy Brother */
	$( ".img-popup" ).click(function() {
		$(".popup_video").css("display", "block");
		return false;
	});
	$( ".popup_img_close" ).click(function() {
		$(".popup_video").css("display", "none");
		return false;
	});
	/* End Akciy Brother */

// render_compare_popup();render_compare_button();

	/* Begin new template */
	$( ".btn-up-one" ).click(function() {
		$( ".dropdown-list-one-text" ).toggle();
	});
	$( ".btn-up-two" ).click(function() {
		$( ".dropdown-list-two-text" ).toggle();
	});
	$( ".btn-up-three" ).click(function() {
		$( ".dropdown-list-three-text" ).toggle();
	});
	/* End new template */




	/* Переключатель сетка/список */
	$('.grid-btn').on('click', function (e) {
		$('.grid-list div:first').removeClass('itemlist').addClass('itemgrid');
		$( ".list-btn" ).removeClass( "activ" );
		$( ".grid-btn" ).addClass( "activ" );

        var max_height = 0;

        $('.itemgrid .item').css('height', 'auto');

        $('.itemgrid .item').each(function() {
            var height = $(this).height();
            if (height > max_height)
                max_height = height;
        });

        $('.itemgrid .item').css('height', max_height);
        $('.itemgrid .item .item_content').css('height', Number(max_height-47));
	});
	$('.list-btn').on('click', function (e) {
		$('.grid-list div:first').removeClass('itemgrid').addClass('itemlist');
		$( ".grid-btn" ).removeClass( "activ" );
		$( ".list-btn" ).addClass( "activ" );
        $('.itemlist .item').css('height', 'inherit');
        $('.itemlist .item .item_content').css('height', 'inherit');
	});

	/* retailrocket (email) */
	$('#get-email-btn').on('click', function (e) {
		var email = $('#email-fild').val();
		rrApiOnReady.push(function() { rrApi.setEmail(email);	});
	});

	/* Baner right block */
	var $imgs = $("#banner-akciy").find("img"),
		i = 0;

	function changeImage(){
		var next = (++i % $imgs.length);
		$($imgs.get(next - 1)).fadeOut(500);
		$($imgs.get(next)).fadeIn(500);
	}
	var interval = setInterval(changeImage, 10000);

});