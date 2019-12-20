<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Всё от Aurora");

?><span style="font-size: 18pt;"><b>Aurora: крейсер в мире шитья.</b></span><br>
<p style="text-align: justify;">
 <img src="/upload/medialibrary/d99/d994674534db5ff60d1d1868d755c712.png" align="right">В 2005 году на рынке швейной техники появился новый бренд, который стал известен своими... ножницами. Да-да, компания "Aurora" начала свою деятельность с выпуска инструментов для раскроя ткани, чтобы затем более серьёзно заняться швейными машинами. В своё время ножницы под брендом "Aurora" были лучшими в своём классе. И сегодня они по-прежнему не уступают по качеству другим производителям.<br>
	 Шло время и наряду с инструментами для шитья компания "Aurora" стала выпускать швейные машины и оверлоки, которые стали использоваться как в быту, так и на производстве. Немаловажная деталь - как и многие другие, "Aurora" ценит своих клиентов, но помимо прочего, стремится к раскрытию творческого потенциала покупателей своей продукции. В этом смысле главное - дать возможность творить по собственным дизайнам.<br>
	 Помимо аксессуаров для шитья, швейных машинок и оверлоков, "Aurora" производит гладильные доски высокого качества. Ознакомиться с продукцией торговой марки "Aurora" можно в соответствующем разделе.
</p>
 <br>
 <?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	"brands",
	Array(
		"ADD_SECTIONS_CHAIN" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "Y",
		"COMPONENT_TEMPLATE" => "brands",
		"COUNT_ELEMENTS" => "Y",
		"FILTER_CATEGORY" => array(29,826,249),
		"IBLOCK_ID" => "8",
		"IBLOCK_TYPE" => "catalog",
		"SECTION_CODE" => "",
		"SECTION_FIELDS" => array(0=>"",1=>"",),
		"SECTION_ID" => "",
		"SECTION_URL" => "",
		"SECTION_USER_FIELDS" => array(0=>"",1=>"",),
		"SHOW_PARENT_NAME" => "Y",
		"TOP_DEPTH" => "5",
		"VIEW_MODE" => "LINE"
	)
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>