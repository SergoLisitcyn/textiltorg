<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Всё от Jaguar");

?><span style="font-size: 18pt;"><b>Jaguar:&nbsp;надёжный японский лидер.</b></span><br>
<p style="text-align: justify;">
 <img width="167" src="/upload/medialibrary/4f5/4f52d34f0c96c0b731b44a42033ad000.png" height="99" align="right">Когда-то известный бренд Jaguar назывался по-другому.&nbsp;с 1949 по 1978 год компания носила имя Maruzen Mischin Company и начала свою деятельность с&nbsp;продажи швейных машин и запчастей к ним.&nbsp;В 1952 году в Японии,&nbsp;благодаря Maruzen Mischin Company,&nbsp;появляется первая домашняя швейная машина,&nbsp;которая делала строчку зигзаг.&nbsp;именно с этого момента начинается стремительный подъём компании,&nbsp;она начинает поставлять новый тип машинок на мировой рынок.&nbsp;Это привело к тому,&nbsp;что в 1961 году компания получает статус главного экспортёра швейного оборудования,&nbsp;да притом,&nbsp;на мировом уровне.&nbsp;Качество японских швейных машин высоко ценится за рубежом.&nbsp;Экспорт растёт,&nbsp;через 3 года компания получает первую награду&nbsp;"Символ качества".&nbsp;Эта награда в период с 1964 по 1977 год будет вручена японскому производителю шесть раз.<br>
	 В 1969 году Maruzen Mischin Company открывает офис в Брюсселе.&nbsp;начинается активное сотрудничество с европейскими странами.&nbsp;В 1977 году европейский офис перемещается в Дюссельдорф,&nbsp;а через 2 года открывается новый завод в Тайване,&nbsp;что несколько снижает стоимость продукции и расширяет круг потребителей.<br>
	 1977 год стал ключевым в истории развития компании.&nbsp;Она меняет название на&nbsp;"Jaguar"&nbsp;и в следующем году бьёт собственный рекорд&nbsp;-&nbsp;на экспорт ушло 7 миллионов машин за год.&nbsp;Предыдущий результат составлял 5 миллионов и был связан с активной деятельностью компании в Европе.&nbsp;Некоторые эксперты связывают возросший экспорт швейных машин ещё и с тем,&nbsp;что за 3 года существования европейского офиса компания смогла отлично зарекомендовать себя на Западе.<br>
	 К концу XX века территория продаж швейных машин Jaguar увеличивается до 5 континентов.&nbsp;Сегодня продукция корпорации высоко востребована,&nbsp;а Jaguar уверенно входит в TOP-3 крупнейших производителей швейных машин.<br>
	 Продукция компании Jaguar&nbsp;-&nbsp;это не только швейные машины,&nbsp;но и отличные по качеству оверлоки,&nbsp;посмотреть ассортимент Вы можете на нашем сайте.
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
		"FILTER_CATEGORY" => array(34,254),
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