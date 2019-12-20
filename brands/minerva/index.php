<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Всё от Minerva");

?><span style="font-size: 18pt;"><b>Minerva: чешские энтузиасты покоряют Европу.</b></span><br>
<p style="text-align: justify;">
 <img width="179" src="/upload/medialibrary/1fe/1fe81cbab0b9b15a98dcbf981555bfbf.png" height="68" align="right">В начале 80-х годов XIX века никому тогда неизвестные Эмиль Резлер и Джосеф Комарек основывают в городе Вена компанию по швейной промышленности, которая затем станет носить имя Minerva. На долю компании выпадает очень много исторически значимых событий, начиная с распада Австро-Венгрии, оккупации, закрытия и повторного открытия, слияния и разъединения, а также полной остановки производства в 1968 году. Но, несмотря на это, компания Minerva с гордостью перенесла все невзгоды, в начале 90-х она покоряет европейский рынок и делает уверенные шаги в сторону России. Кстати, окончательное укрепление на российском рынке у компании Minerva произошло лишь несколько лет назад, в 2015 году.<br>
	 Мы с радостью предлагаем своим Клиентам ознакомиться с оверлоками, швейными и швейно-вышивальными машинами Minerva на нашем сайте.
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
		"FILTER_CATEGORY" => array(854,890,857),
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