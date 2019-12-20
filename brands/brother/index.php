<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Всё от Brother");

?><span style="font-size: 20pt;">Brother: японское величие.<br>
 </span>
<p style="text-align: justify;">
 <img width="205" src="/upload/medialibrary/906/906390867e5d306bd8b6ebb5517d169a.png" height="74" align="right">Эта история полна интересных моментов. В&nbsp;начале XX века&nbsp;в&nbsp;небольшом&nbsp;японском&nbsp;городке&nbsp;Нагоя&nbsp;самым обычным&nbsp;горожанином Канэкити Ясуи была основана компания Brother.&nbsp;Шёл 1908&nbsp;год,&nbsp;в небольшой мастерской по ремонту швейных&nbsp;машинок вовсю кипела работа.&nbsp;Кто бы мог подумать, что спустя столетие, в 2008 году, корпорация Brother&nbsp;станет самой известной компанией по поставке печатающего оборудования по всему миру.&nbsp;<br>
	 Но пока что на дворе был 1908 год. Через 17&nbsp;лет&nbsp;управление компанией перейдёт братьям основателя,&nbsp;а&nbsp;к 1934&nbsp;году производство выросло в пять раз.&nbsp;<br>
	 Дальше больше.&nbsp;В 1959 году компания&nbsp;преодолевает символический&nbsp;рубеж в миллион швейных машин. К 2008 году в компании работают более 23&nbsp;тысяч сотрудников,&nbsp;оборот компании&nbsp;превысил&nbsp;3 миллиарда долларов, а доля продаж в России составляет&nbsp;5-10%, что довольно много.&nbsp;<br>
	 Brother&nbsp;по-прежнему производит швейные, вышивальные, швейно-вышивальные машины, оверлоки, а также принтеры по текстилю. Ознакомиться&nbsp;с продукцией Brother&nbsp;можно&nbsp;в&nbsp;соответствующем разделе.
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
		"FILTER_CATEGORY" => array(31,252,269,265,841,877),
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
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>