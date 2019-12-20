<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Всё от Astralux");

?><span style="font-size: 18pt;"><b>Торговая марка AstraLux: достойный лидер в мире швейного оборудования.</b></span><br>
 <br>
<p style="text-align: justify;">
 <img src="/upload/medialibrary/349/349e9e1db68dd1b720614f8872388b8d.png" align="right">AstraLux как торговая марка увидела свет сравнительно недавно - в 2003 году. Но до&nbsp;появления этого бренда существовала другая&nbsp;компания, на базе которой и возник AstraLux - «Zeng Hsing Industrial Co., ltd». Этот промышленный гигант существует с 1968 года и в последнее время развивается настолько стремительно, что в 2016 году смог довести выпуск швейных машин и оверлоков до 3 с лишним миллионов экземпляров в год. При этом спрос на швейную технику этой компании не снижается.
</p>
<p style="text-align: justify;">
	 Продукция, которую выпускает AstraLux - швейные машины (механические, электромеханические, компьютеризированные), а также оверлоки. С 2013 года компания занимает почётное место в TOP-3 торговых марок. Основными конкурентами для AstraLux являются Brother и Janome.<br>
	 Купить швейные машины и оверлоки AstraLux Вы можете у нас на сайте, а ознакомиться с продукцией - в соответствующем разделе. Для вашего удобства мы собрали все самые интересные предложения в несколько основных разделов. Вам останется лишь выбрать нужный раздел и насладиться поиском необходимой Вам модели оверлока или швейной машины.
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
		"FILTER_CATEGORY" => array(28,248),
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