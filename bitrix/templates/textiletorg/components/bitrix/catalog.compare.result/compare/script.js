BX.namespace("BX.Iblock.Catalog");

BX.Iblock.Catalog.CompareClass = (function()
{
	var CompareClass = function(wrapObjId)
	{
		this.wrapObjId = wrapObjId;
	};

	CompareClass.prototype.MakeAjaxAction = function(url)
	{
		BX.showWait(BX(this.wrapObjId));
		BX.ajax.post(
			url,
			{
				ajax_action: 'Y'
			},
			BX.proxy(function(result)
			{
				BX.closeWait();
				BX(this.wrapObjId).innerHTML = result;
				
				$('.table_compare').clone().appendTo('#labels');
				$("#tables-wrapper .table-inner").mCustomScrollbar({
					theme:"yellow-red",
					scrollButtons:{enable:true},
					axis:"x"
				});
				
			}, this)
		);
	};

	return CompareClass;
})();

$('.popup-offers .buy_button').click(function() {
    var button = $(this),
        id = button.attr('data-id'),
        path = button.attr('data-path');

    button.parents('.gift_block').addClass('add-overlay');

    $.get(path);

    return false;
});