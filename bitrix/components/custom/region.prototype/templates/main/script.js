$(document).ready(function() {
	var cityID = $('#hid_city_id').val();

	$('.region_city[data-city-id=' + cityID + ']').removeClass('region_city').addClass('aselect_city');

	$('body').on('click', '.list_of_all_citys .listalfabet a.alfabet', function() {
		var id = $(this).attr('data-letter');

		$('.list_of_all_citys .listalfabet a.alfabet').removeClass('acl_activ');
		$(this).addClass('acl_activ');

		$('.list_of_all_citys .listcity.show').removeClass('show');
		$('.list_of_all_citys .listcity#abc-'+id).addClass('show');

		return false;
	});

	$('body').on('click', '.listcity li span', function() {
		var href = $(this).attr('data-href');

		if (href.length && !$(this).hasClass('aselect_city'))
		{
			window.location.href = href;
		}

		return false;
	});

	$('.listalfabet li:first-child a').click();

	$('body').on('click', '.show_all_city', function(e) {
        $('.list_of_famous_citys').css('display', 'none');
		$('.list_of_all_citys').css('display', 'block');
		$( '.wrap_show_all_city' ).html('<span class="show_famous_city">Показать популярные города</span><div>');
		if (!$('.listalfabet li a').hasClass( "acl_activ" )) {
            $('.listalfabet li:first-child a').click();
		}

    });

	$('body').live('click', '.show_famous_city', function(e) {
        $('.list_of_famous_citys').css('display', 'block');
		$('.list_of_all_citys').css('display', 'none');
		$( '.wrap_show_all_city' ).html('<span class="show_all_city">Показать все города</span><div>');
	});

	$('body').on('click', '.close_win', function(){
		show_hide_geo(0);
	});

	var states = [];
	$('.select_city').typeahead({
		hint: true,
		highlight: true,
		minLength: 3
	},
	{
		name: 'states',
		displayKey: 'name_ru',
		source: function(query, process) {
			return $.post(location.href,
				{
					QUERY: query,
					AJAX_SEARCH_REGION: 'Y'
				},
				function(response) {
					matches = [];
					$.each(response, function(i, str) {
						matches.push({name_ru: str['city_name'], region_name: str['region_name']});
					});
					console.log(matches);
					return process(matches);
				},
				'json'
			);
		},
		templates: {
			suggestion: Handlebars.compile('{{name_ru}}<br /><div class="small_tip">{{region_name}}</div>')
		}
	});

	$('body').on('click', '#do_choose', function() {
		SetGeoCity();
		return false;
	});

	function SetGeoCity()
	{
		var curUrl = window.location.href;
		//curUrl = curUrl.replace("spb.","www.");
		if ($('#input_city').val().indexOf("Санкт")!='-1'){
			curUrl = curUrl.replace("www.","spb.");
		}else{
			curUrl = curUrl.replace("spb.","www.");
		}
		curUrl += curUrl.indexOf('?') === -1 ? '?': '&';
		$(location).attr("href", curUrl + 'SET_CITY=' + $('#input_city').val());
	}
});

