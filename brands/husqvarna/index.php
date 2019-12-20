<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Всё от Husqvarna");

?><span style="font-size: 20pt;">Husqvarna: не только садовое оборудование.</span><br>
 <br>
<p style="text-align: justify;">
 <img width="205" src="/upload/medialibrary/ad2/ad22c5ceb09ad0d49e5e8b2541d04516.png" height="132" align="right">Шведский бренд и одна из старейших компаний в Европе, Husqvarna известна многим по садовому оборудованию, бензопилам и бензокосам, а также различному оборудованию для ландшафтного дизайна. Тем не менее, корпорация выпускает и швейные машинки, качество которых не подлежит сомнению.&nbsp;<br>
	 Компания Husqvarna изготавливает свои швейные машины так же качественно, как и в своё время оружие, мотоциклы или цепные пилы. Ознакомиться с ассортиментом швейных машин, оверлоков и швейно-вышивальных машин можно в этом разделе.&nbsp;
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
		"FILTER_CATEGORY" => array(33,298,732),
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
);?><br>
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>