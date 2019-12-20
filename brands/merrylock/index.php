<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Всё от Merrylock");

?><span style="font-size: 18pt;"><b>Merrylock:&nbsp;короли оверлоков.</b></span><br>
<p style="text-align: justify;">
 <img width="209" src="/upload/medialibrary/511/51185f8376b26955700d0fed46445fcf.png" height="93" align="right">Достаточно известная в нашей стране,&nbsp;компания Merrylock была основана в 1993 году в Тайване молодым инженером Сенгом Чангом.&nbsp;Именно на Тайване в то время была максимальная концентрация производств,&nbsp;направленных на работу с высокими технологиями.&nbsp;инновационные разработки внедрялись в оборудование именно здесь.&nbsp;Этим и объясняется интерес Чанга к созданию Merrylock именно на Тайване. а дальше интерес нарастал, как снежный ком.&nbsp;В 2000 году Merrylock впервые шагнул со своей продукцией на европейскую землю.&nbsp;затем&nbsp;-&nbsp;в Америку и,&nbsp;наконец,&nbsp;в Азию.<br>
	 В чём же секрет компании Merrylock?&nbsp;Именно в их узкой специализации,&nbsp;которую они выбрали изначально. Дело в том,&nbsp;что подобный шаг позволяет полностью сконцентрироваться на каждой детали именно одного типа продукции.&nbsp;Это позволяет добиваться высоких результатов,&nbsp;достигать новых вершин и постоянно совершенствовать производство,&nbsp;не отвлекаясь на прочую продукцию.&nbsp;Каждая модель оверлока Merrylock&nbsp;-&nbsp;это как отдельный шедевр в Галерее современного искусства.<br>
	 Ознакомиться с оверлоками Merrylock можно на нашем сайте в соответствующем разделе.
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
		"FILTER_CATEGORY" => array(258),
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