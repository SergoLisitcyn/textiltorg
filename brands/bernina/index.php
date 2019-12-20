<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Всё от Bernina");

?><span style="font-size: 20pt;"><b>Bernina: швейцарские первопроходцы.</b></span><br>
<p style="text-align: justify;">
 <img src="/upload/medialibrary/38c/38c33edbe72a8ed0a81e066e7d17d9c4.png" align="right">Когда история швейцарской компании Bernina только начиналась, мир швейного оборудования был ещё не так хорошо представлен в Европе. В 1893 году компания выпускает свою первую швейную машину с возможностью обрабатывать край изделия мережкой. На тот момент это становится настоящей сенсацией: новая модель способна выполнять до 100 стежков в минуту, что сделало Bernina достаточно продаваемым брендом.&nbsp;<br>
	 Начало XX века стало для компании Bernina успешным, продукция фабрики поставляется за рубеж. Многие ценят швейные машины Bernina за исключительное швейцарское качество. С 1932 года окончательно сформирован логотип Bernina, а ещё через 6 лет инженеры компании выпустили первую машину со строчкой зигзаг. Следующим улучшением стала рукавная платформа. которую начали внедрять в модели швейных машин с 1945 года.<br>
	 Bernina считается настоящим швейцарским первопроходцем в области новых технологий и внедрения их в швейный процесс. Первый коленоподъёмник в 1963-м, первая электрическая педаль в 1971-м. Первая компьютерная модель в 1986-м, которая полностью автоматизировала выполнение петли. В дальнейшем развитие швейной техники Bernina направлено на компьютеризацию, упрощение работы и наращиванию скорости шитья без потери качества.<br>
	 Бренд Bernina представлен швейными машинами, оверлоками, вышивальными и швейно-вышивальными машинами. Все модели можно посмотреть в соответствующих разделах.
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
		"FILTER_CATEGORY" => array(30,251,264,268),
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