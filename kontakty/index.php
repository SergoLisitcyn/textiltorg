<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Контакты. Спасибо Вам за оказанное доверие к нашему магазину. +7 (495) 669-777-9  Мы будем рады Вашему звонку");
$APPLICATION->SetPageProperty("keywords", "контакты текстильторг, проба бытовой техники, швейный мир");
$APPLICATION->SetPageProperty("title", "Контакты нашего интернет-магазина - Швейный мир | ТекстильТорг ");
$APPLICATION->SetTitle("Контакты магазина «ТекстильТорг»");
?><div class="m-hidden">
 <img src="/upload/images/Samovyvoz.jpg" style="float: right; margin: 0 0px 5px 40px;" alt="">
	<?$APPLICATION->IncludeComponent(
	"custom:region-select.prototype",
	"footer",
	Array(
		"DEFAULT_REGION_CITY_NAME" => "Москва",
		"DEFAULT_REGION_ID" => "19"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
</div>
 <?$APPLICATION->IncludeComponent(
	"ayers:stores.page",
	"",
	Array(
		"CACHE_TIME" => 36000,
		"CITY" => $GLOBALS["GEO_REGION_CITY_NAME"],
		"COUNT_SHOW" => 3
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?> <br>
<p>
	 В торговых точках «ТекстильТорга» у Вас всегда есть возможность ознакомиться с нашей продукцией максимально полно и всесторонне. Вы можете абсолютно бесплатно попробовать в деле любой понравившийся товар.
</p>
 <?$APPLICATION->IncludeComponent(
	"ayers:stores.map",
	"",
	Array(
		"CACHE_TIME" => 36000,
		"CITY" => $GLOBALS["GEO_REGION_CITY_NAME"]
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
<div class="clear">
</div>
<div class="adantages-samovyvoz">
	 Преимущества самовывоза
</div>
 <br>
<div class="container-samovyvoz">
	<div class="container-samovyvoz-ul">
		<div class="adantage-1">
		</div>
		<div>
			 Экономия времени
		</div>
	</div>
	<div class="container-samovyvoz-ul">
		<div class="adantage-2">
		</div>
		<div>
			 Экономия денег на доставку
		</div>
	</div>
	<div class="container-samovyvoz-ul">
		<div class="adantage-3">
		</div>
		<div>
			 Возможность попробовать перед покупкой
		</div>
	</div>
	<div class="container-samovyvoz-ul">
		<div class="adantage-4">
		</div>
		<div>
			 Возможность изменить выбор
		</div>
	</div>
</div>
<div style="clear: both;">
</div>
 <br>
<p>
	 В магазинах «ТекстильТорг» у Вас всегда есть возможность ознакомиться с нашей продукцией максимально полно и всесторонне. Вы можете абсолютно бесплатно попробовать в деле любой понравившийся товар. Наши эксперты-консультанты научат Вас заправлять швейную машинку, помогут подобрать нитки и иглы для шитья, расскажут как выбрать и настроить нужную программу и многое другое. Вы сами сможете отпарить одежду, ощутить возможности гладильной системы, проверить качество вышивки и безупречность стежка.
</p>
<p>
	 Наши продавцы не только ответят на все Ваши вопросы, но и покажут в деле любую приглянувшуюся Вам модель.
</p>
<p>
	 Стоимость товара, указанная в каталоге нашего магазина, является окончательной и неизменной на день покупки. В нее уже включены все налоги.
</p>
<p>
	 При самовывозе цена на товар не меняется, Ваша экономия заключается в отсутствии оплаты доставки!
</p>
<p>
	 Выбирайте и резервируйте товар на сайте. Забрать свой заказ Вы сможете в любой день недели — <a href="/poluchenie/nashi-magaziny/">пункты выдачи</a> работают ежедневно (без выходных) с 09:00 до 21:00.
</p>
 <br>
 <br>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "block_feedback",
		"COMPONENT_TEMPLATE" => ".default",
		"EDIT_TEMPLATE" => ""
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>