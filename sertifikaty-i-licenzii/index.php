<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Сертификаты, награды и лицензии");
$APPLICATION->AddHeadScript("esdscript.js");

$arImages = array(
//    "/upload/iblock/70b/70be0970349207e11fb0d2fba9911ca9.jpeg",
//    "/upload/iblock/70b/70be0970349207e11fb0d2fba9911ca9.jpeg",
//    "/upload/iblock/70b/70be0970349207e11fb0d2fba9911ca9.jpeg",
//    "/upload/iblock/70b/70be0970349207e11fb0d2fba9911ca9.jpeg",
//    "/upload/iblock/70b/70be0970349207e11fb0d2fba9911ca9.jpeg",
);
?><div class="sertifikaty-i-licenzii">
 <br>
</div>
 <img src="/upload/medialibrary/c00/c0000b148110da1027f64fbbb7f5b154.png">&nbsp; &nbsp; &nbsp;&nbsp;<img src="/upload/medialibrary/d36/d36970ba76d7439004fd105c32e41246.png">&nbsp; &nbsp; &nbsp;&nbsp;<img src="/upload/medialibrary/993/9937cb68b8909b724898d3c0ac229704.png">&nbsp; &nbsp; &nbsp;&nbsp;<img src="/upload/medialibrary/f19/f199972e5b1bc8cd76cec193a86792e4.png">&nbsp; &nbsp; &nbsp;&nbsp;<img src="/upload/medialibrary/60e/60e3db8c4661483216007ce07455bbdb.png">&nbsp; &nbsp; &nbsp;&nbsp;<br>
<p style="text-align: justify;">
	 Компания "ТекстильТорг" с самого первого дня своей работы заботилась о том, чтобы все соответствующие лицензии и сертификаты были в полном порядке. Некоторое время спустя возникла необходимость стать полноценным представителем того или иного бренда в России. Первый сертификат&nbsp;официального дистрибьютора наша компания получила от торговой марки&nbsp;Brother. Очень скоро к ней присоединяются Bernina, Elna и Janome. Все эти швейные производители с удовольствием сотрудничают с нашей компанией, предлагая свои новинки, которые мы, в свою очередь, предлагаем Вам.&nbsp;<br>
	 Мы не первый год сотрудничаем с мировыми брендами швейных машин и всегда готовы предоставить самое лучшее по самым выгодным ценам. Секрет такого успеха в том, что мы работаем напрямую с каждым из наших производителей, каждый сертификат дилера той или иной фабрики - свидетельство хороших и гармоничных партнёрских отношений с ведущими мировыми брендами. Ведь если компания сотрудничает со своими поставщиками долгое время, это говорит о том, что такой организации можно смело доверять.&nbsp;<br>
	 Мы доверяем своим поставщикам, а они - нам. Именно поэтому большинство наших довольных Клиентов доверяют нам и качеству продукции наших поставщиков.<br>
 <span style="color: #ff0000;"><b>Присоединяйтесь и Вы!</b></span>
</p>
 <br>
 <?if (defined("IS_MOBILE")):?><?$APPLICATION->IncludeComponent(
	"custom:sertifikaty.prototype",
	"mobile",
	Array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "N",
		"COMPONENT_TEMPLATE" => "mobile",
		"IMAGES" => $arImages
	)
);?><?else:?> <?$APPLICATION->IncludeComponent(
	"custom:sertifikaty.prototype",
	".default",
	Array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "N",
		"COMPONENT_TEMPLATE" => ".default",
		"IMAGES" => $arImages
	)
);?> <?endif?> <br>
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>