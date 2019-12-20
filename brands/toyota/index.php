<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Всё от Toyota");

?><p style="text-align: justify;">
 <span style="font-size: 18pt;"><b>Toyota:&nbsp;когда машинки бывают разными.</b></span><br>
 <br>
 <img width="182" src="/upload/medialibrary/91d/91de2f84375909c9e7200631b65079cd.png" height="62" align="right">Швейными машинами компания Кииджиро Тойоды стала заниматься с 1949 года. Тогда основателю TOYOTA Motor Corporation казалось,&nbsp;что швейная машина должна быть не только эффективной,&nbsp;но и красивой.&nbsp;Именно эстетическая сторона дела позволила первой модели швейной машины Toyota НА-1 сравняться с автомобилями той же марки.&nbsp;Пользователям очень понравилась эта модель,&nbsp;затем&nbsp;-&nbsp;остальные новинки компании и к 1952 году объём выпускаемой продукции у фабрики швейных машин достиг ста тысяч моделей в год.&nbsp;Через 10 лет производство выросло до 1.000.000 швейных машин,&nbsp;а ещё через 6 лет&nbsp;-&nbsp;до двух миллионов.&nbsp;Такие темпы роста выпуска продукции объясняются тем,&nbsp;что популярность швейных машин стала постепенно выходить за рамки Японии и постепенно выходить на европейский и американский рынок.<br>
	 В конце XX века Toyota выпускала 10 миллионов швейных машин в год.&nbsp;Это поистине значимое достижение компании.&nbsp;И сегодня Toyota не перестаёт радовать нас как хорошими автомобилями,&nbsp;так и качественными швейными машинами.&nbsp;Так что машинки порой бывают разными,&nbsp;но качество остаётся неизменно высоким.&nbsp;Познакомиться с ассортиментом швейных машин и оверлоков Toyota Вы можете на нашем сайте.
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
		"FILTER_CATEGORY" => array(27,261),
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