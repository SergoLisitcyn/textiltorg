<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Всё от Janome");

?><span style="font-size: 18pt;"><b>Janome: годзилла швейных машин.</b></span><br>
<p style="text-align: justify;">
 <img width="205" src="/upload/medialibrary/c84/c84ddad18a3eabafe8923ed06c6ef431.png" height="81" align="right">В 1921 году, в Японии открывается небольшой завод по производству швейных машин. Он назывался Pine и стал прародителем Janome. Такой, какой мы знаем её сейчас, компания стала спустя 14 лет после основания. Интересный факт - дочерняя компания Janome America владеет компанией Elna, поэтому машинки обоих брендов производят на одних и тех же заводах.<br>
	 Но вернёмся к истории. В 1867 году два предприимчивых американца наладили выпуск швейных машин New England и Home Shuttle. Позднее возникло и название - "New Home" В 20-х годах XX века предприниматели столкнулись с трудностями, поэтому были поглощены компанией The Free Sewing Machine Company. А она, в свою очередь, стала собственностью... компании Janome, которая, таким образом, получила полные права на торговую марку New Home.<br>
	 Отличительным достижением Janome стало и то, что они первыми предложили профессиональную вышивку в домашних условиях.<br>
	 Сегодня бренд Janome известен в более, чем в 100 странах мира, отчасти благодаря слаженной работе сотрудников каждого подразделения, отчасти - продуманной бизнес-стратегии. На нашем сайте Вы также можете ознакомиться с ассортиментом швейных машин, оверлоков, вышивальных и швейно-вышивальных машин.
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
		"FILTER_CATEGORY" => array(35,271,267,878,255),
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