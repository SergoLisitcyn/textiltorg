jQuery().ready(function(){
	$('a.callme').click(function(){
		yaCounter1021532.reachGoal('click_po_zakazat_zvonok');
		ga('send', 'pageview', '/click_po_zakazat_zvonok');
		console.log('click_po_zakazat_zvonok');
		$('.callback-form').submit(function(){
			setTimeout(function(){
				if ($('.callback-form input[name="form_text_1"]').hasClass('success') && $('.callback-form input[name="form_text_2"]').hasClass('success')) {
					yaCounter1021532.reachGoal('usp_zakaz_zvonka');
					ga('send', 'pageview', '/usp_zakaz_zvonka');
					console.log('usp_zakaz_zvonka');
				}else{
					console.log('Empty fields');
				}
			},400);
		});
	});

	/*$('.eshop-item-small__one-click').click(function(){
		yaCounter1021532.reachGoal('click_po_kupit_v_1_click');
		ga('send', 'pageview', '/click_po_kupit_v_1_click');
		console.log('click_po_kupit_v_1_click');
		$('.buy-one-click-form').submit(function(){
			setTimeout(function(){
				if ($('.buy-one-click-form input[name="NAME"]').hasClass('success') && $('.buy-one-click-form input[name="PHONE"]').hasClass('success')) {
					yaCounter1021532.reachGoal('usp_pokupka_v_1_click');
					ga('send', 'pageview', '/usp_pokupka_v_1_click');
					console.log('usp_pokupka_v_1_click');
				}else{
					console.log('Empty fields');
				}
			},400);
		});
	});*/

	$('.buy-one-click').click(function(){
		yaCounter1021532.reachGoal('click_po_kupit_v_1_click');
		ga('send', 'pageview', '/click_po_kupit_v_1_click');
        fbq('track', 'AddToCart'); // Facebook AddToCart
		console.log('click_po_kupit_v_1_click');
		$('.buy-one-click-form').submit(function(){
			setTimeout(function(){
				if ($('.buy-one-click-form input[name="NAME"]').hasClass('success') && $('.buy-one-click-form input[name="PHONE"]').hasClass('success')) {
					yaCounter1021532.reachGoal('usp_pokupka_v_1_click');
					ga('send', 'pageview', '/usp_pokupka_v_1_click');
					console.log('usp_pokupka_v_1_click');
				}else{
					console.log('Empty fields');
				}
			},400);
		});
	});

	$('.inyourcart.scale-decrease, .incart_input.scale-decrease').click(function(){
		yaCounter1021532.reachGoal('click_po_kupit');
		ga('send', 'pageview', '/click_po_kupit');
        fbq('track', 'AddToCart'); // Facebook AddToCart
		console.log('click_po_kupit');
	});

	$('#footer-callback-form').submit(function(){
		setTimeout(function(){
			if ($('#footer-callback-form input[name="form_text_2"]').hasClass('success')) {
				yaCounter1021532.reachGoal('click_po_zhdu_zvonka');
				ga('send', 'pageview', '/click_po_zhdu_zvonka');
				console.log('click_po_zhdu_zvonka');
			}else{
				console.log('Empty fields');
			}
		},400);
	});


	
});