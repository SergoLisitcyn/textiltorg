<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Всё от Juki");

?><p style="text-align: justify;">
 <span style="font-size: 18pt;"><b>Juki:&nbsp;промышленник и домосед.</b></span><br>
 <br>
</p>
<p style="text-align: justify;">
 <img width="205" src="/upload/medialibrary/433/4336c3a806b704c01c5985ff19182f48.png" height="87" align="right">Изначально компания занималась промышленными швейными машинами и только затем перешла на бытовые швейные машинки.&nbsp;Штаб-квартира Juki&nbsp;находится в Тамаши,&nbsp;районе Токио.&nbsp;входит в тройку лидеров мировых производителей швейных машин.&nbsp;Активно сотрудничает со 170 странами на 6 континентах. "Разум и технологии" -&nbsp;основной девиз компании с 1988 года.<br>
	 Достаточно часто можно встретить новости о совместных проектах с такими компаниями,&nbsp;как,&nbsp;например,&nbsp;HITACHI.<br>
	 Швейные машины и оверлоки Juki Вы можете посмотреть и приобрести на нашем сайте.
</p>
<p style="text-align: justify;">
</p>
<p>
</p>
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
		"FILTER_CATEGORY" => array(36,256),
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