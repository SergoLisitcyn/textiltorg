$(function() {
	//KHAYR_MAIN_COMMENT_ShowMessage();
	$("body").on("click", "#KHAYR_MAIN_COMMENT_container .nav a", function() {
		BX.showWait();
		$.post($(this).attr("href"), {"ACTION": "nav"}, function(result) {
			$("#KHAYR_MAIN_COMMENT_container").html(result);
			BX.closeWait();
		});
		return false;
	});
	$(".khayr_main_comment .stock").each(function() {
		var elem = $(this);
		var collapse_text_height = elem.find('.collapse_text').height();
		var collapse_text_wrapper_height = elem.find('.collapse_text_wrapper').height();
		if (collapse_text_height > collapse_text_wrapper_height) {
			elem.find('.collapse_link.show_comment').show().on('click', function(e) {
				e.preventDefault();
				elem.find('.collapse_text_wrapper').css({'max-height': 'inherit'});
				$(this).hide();
				elem.find('.collapse_link.hide_comment').show().on('click', function(e) {
					e.preventDefault();
					elem.find('.collapse_text_wrapper').css({'max-height': collapse_text_wrapper_height});
					$(this).hide();
					elem.find('.collapse_link.show_comment').show()
				});
			});
		}
	});



	// $('.collapse_text').readmore({
	// 	moreLink: '<a href="#" class="collapse_link">Подробнее</a>',
	// 	collapsedHeight: 60,
	// 	lessLink: '<a href="#" class="collapse_link">Свернуть</a>'
	// });
});

function KHAYR_MAIN_COMMENT_getUrl(url, newParams)
{
	var link = document.createElement('a');
	link.href = url;
	//console.log(link.search);
	if (newParams)
	{
		if (link.search)
			link.search += '&'+newParams;
		else
			link.search = '?'+newParams;
	}
	var query = {};
	link.search.substring(1).split('&').forEach(function(value) {
		value = value.split('=');
		if (value[0] in query)
		{
			if (!(query[value[0]] instanceof Array))
				query[value[0]] = [query[value[0]]];
			query[value[0]].push(value[1]);
		}
		else
			query[value[0]] = value[1];
	});
	//console.log(query);
	var out = new Array();
	for (key in query)
		out.push(key + '=' + encodeURIComponent(query[key]));
	out = out.join('&');
	//console.log(query);
	if (out)
		link.search = "?"+out;
	else
		link.search = "";
	console.log(link.href);
	return link.href;
}

function KHAYR_MAIN_COMMENT_validate(_this, pagen)
{
	if (!pagen)
		pagen = '';
	else
		pagen = 'PAGEN_'+pagen;
	BX.showWait();
	$.ajax({
        url: KHAYR_MAIN_COMMENT_getUrl($(_this).attr("action"), pagen),
        type: 'POST',
		data: new FormData(_this),
		processData: false,
		contentType: false,
        success: function(result) {
			$("#KHAYR_MAIN_COMMENT_container").html(result);
			KHAYR_MAIN_COMMENT_ShowMessage();
		},
        error: function() {
		},
		complete: function() {
			BX.closeWait();
		}
    });
	return false;
}

function KHAYR_MAIN_COMMENT_delete(_this, id, message, pagen)
{
	if (!pagen)
		pagen = '';
	else
		pagen = 'PAGEN_'+pagen;
	if (!message)
		var message = "DELETE?";
	if (confirm(message))
	{
		BX.showWait();
		$(_this).parents(".stock:first").hide("slow");
		$.ajax({
			url: KHAYR_MAIN_COMMENT_getUrl(window.location.href, pagen),
			type: 'POST',
			data: {"ACTION": "delete", "COM_ID": id},
			success: function(result) {
				$("#KHAYR_MAIN_COMMENT_container").html(result);
				KHAYR_MAIN_COMMENT_ShowMessage();
			},
			error: function() {
			},
			complete: function() {
				BX.closeWait();
			}
		});
	}
	return false;
}
function KHAYR_MAIN_COMMENT_edit(_this, id)
{
	// $(".main_form").hide();
	// $(".form_for").hide();
	$("#edit_form_"+id).show();
}
function KHAYR_MAIN_COMMENT_add(_this, id)
{
	// $(".main_form").hide();
	// $(".form_for").hide();
	$("#add_form_"+id).show();
}
function KHAYR_MAIN_COMMENT_back()
{
	// $(".main_form").show();
	$(".form_for").hide();
}

var KHAYR_MAIN_COMMENT_action = false;
function KHAYR_MAIN_COMMENT_ShowMessage()
{
	$(".khayr_main_comment_suc_exp, .khayr_main_comment_err_exp").remove();
	var err = $(".err").text();
	var suc = $(".suc").text();
	clearTimeout(KHAYR_MAIN_COMMENT_action);
	if (err.length > 0)
	{
		ShowMessage('Ошибка', err);
	}
	else if (suc.length > 0)
	{
		ShowMessage('Комментарий оставлен', suc);
	}
}
function KHAYR_MAIN_COMMENT_exp_close()
{
	$(".khayr_main_comment_suc_exp, .khayr_main_comment_err_exp").fadeOut(1000);
	setTimeout(function() {
		$(".khayr_main_comment_suc_exp, .khayr_main_comment_err_exp").remove();
	}, 1000);
}
function ShowMessage(title, message) {
	$.fancybox( '<p>' + message + '</p>', {
		maxWidth	: 400,
		maxWidth	: 300,
		fitToView	: false,
		autoSize	: true,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none',
		beforeLoad: function() {
			this.title = title
		}
	});
}