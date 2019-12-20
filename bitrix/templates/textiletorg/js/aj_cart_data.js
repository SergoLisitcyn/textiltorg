	function RefreshData()
	{
		var collect = $('.minus');
		var slot_total_price = $('#eshop_cart_total');
		var slot_total_count = $('#eshop_cart_count');
		var slot_vivod = $('#vivod');

		for( i = 0; i < collect.length; i++ )
		{
			RecalcPrices(collect[i]);
		}

		slot_vivod.html(window.grand_total + ' руб.');
		slot_total_price.html(window.grand_total + ' руб.');
		slot_total_count.html(window.total_count);
	}

	// Ajax-пересчет корзины
	function AjaxSubForm()
	{
		$('input[name="action"]').attr("value", "recalc");

		$('#entryFrm').ajaxSubmit(
                                    {
									success: function(data)
									{
										GetPrices(RefreshData);
									},
									beforeSubmit: function()
									{
									}
                                     }
                                     );

	}



	function alertObj(obj)
	{
		var str = "";
		for(k in obj)
		{
			str += k+": "+ obj[k]+"\r\n";
		}
		alert(str);
	}

	function GetPrices(cb_func)
	{
		AMI.$.ajax(
		{
			url: "/ami_service.php",
			data:
			{
				service: "eshop_order",
				action: "get_order_cost",
				json_data: JSON.stringify(window.aCartShippings)
			}
		}).done(function(content)
			{
				window.priceAj = JSON.parse(content);
				window.grand_total = priceAj.total.grand_total;
				window.total_count = 0;
				for( i = 0; i < priceAj.items.length; i++)
				{
					total_count += + priceAj.items[i].qty;
				}
				cb_func();
			});
	}

/*	AMI.$(document).ready(function()
	{
		window.timer_id = 0;
		AMI.$.ajax(
		{
					url: "http://test.textiletorg.ru/ami_service.php",
					data:
					{
							service: "eshop_cart",
							action: "get_available_shippings"
					}
		}).done(function(content)
			{
				window.aCartShippings = JSON.parse(content);
				GetPrices(function(){});
			});
	});*/

	function GetIdItem(nm)
	{
		var res = -1;
		for(i = 0; i < priceAj.items.length; i++)
		{
			if( nm == priceAj.items[i].name )
			{
				res = i;
				break;
			}
		}
		return res;
	}

	function RunTimer()
	{
		if( window.timer_id ) clearTimeout(window.timer_id);
		window.timer_id = setTimeout(ReloadData, 2500);
	}

	function ReloadData()
	{
		AjaxSubForm();
		if( window.timer_id ) clearTimeout(window.timer_id);
		var ins_img = '<img src="/img/load.gif" id="load_price">';
		$('.price').html(ins_img);
		$('#eshop_cart_total').html(ins_img);
		$('#eshop_cart_count').html(ins_img);
		$('#vivod').html(ins_img);

	}

	function RecalcPrices(obj)
	{
		var slot_price = $(obj).parent().parent().siblings('.price');
		var nm_item = $(obj).parent().parent().siblings('.name').children('.name2').children('a').html();
		var id_item = GetIdItem(nm_item);
		var total_price_item = window.priceAj.items[id_item].total_tax;
		slot_price.html(total_price_item + ' руб.');
	}


