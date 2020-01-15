<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Доставка. Спасибо, что выбрали нас! Мы сделаем все возможное, чтобы Ваш заказ был доставлен оперативно и Вы остались довольны! Доставка  по г.Москва и Спб. Мы находимся в Гагаринском районе");
$APPLICATION->SetPageProperty("keywords", "доставка товаров текстильторг, доставка в гагаринском районе, ленинский район");
$APPLICATION->SetPageProperty("title", "Доставка товаров нашего магазина. Гагаринский район | ТекстильТорг");
$APPLICATION->SetTitle("Доставка: сроки, расстояния, цена");
?><img width="396" src="/upload/images/dostavka.png" height="264" class="container-image" alt="">
<div class="m-hidden">
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
	"custom:region-phone.prototype",
	"signature",
	Array(
		"DEFAULT_FILE" => "default",
		"FILES_PATH" => "/include/region-dostavka/"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>

<?//$APPLICATION->IncludeComponent(
//	"custom:calc-delivery.prototype",
//	"",
//	array(
//		"IBLOCK_ID" => 8,
//		"IGNORE_SECTIONS" => array(
//			"aksessuary-dlya-shitya",
//			"aksessuary-dlya-vyshivaniya",
//			"aksessuary-dlya-vyazaniya",
//			"aksessuary-dlya-glazheniya",
//			"aksessuary-dlya-uborki",
//			"podarochnye-karty",
//			"hague",
//			"prinadlezhnosti",
//			"tkani-dlya-poshiva-odezhdy",
//			"tkani-dlya-obivki-mebeli",
//			"tkani-dlya-poshiva-shtor-i-zanovesey",
//			"tkani-dlya-postelnogo-belya"
//		),
//		"COUNTRY_NAME_ORIG" => $GLOBALS["REGION_COUNTRY_NAME_ORIG"],
//        "GEO_REGION_CITY_NAME" => $GLOBALS["GEO_REGION_CITY_NAME"],
//        "SITE_ID" => "s1"
//	),
//	false
//);?>

<h4>Дорогие покупатели! При получении заказа обязательно:
</h4>
<p>
	Проверьте комплектацию всего заказа и каждого товара в отдельности, правильность заполнения сопутствующих документов.<br>
	 Убедитесь в отсутствии на товарах видимых механических повреждений.
</p>
<div class="attention-dostavka">
    <span></span><b>ОЧЕНЬ ВЫЖНО</b>
    <p>
        Дорогие покупатели! При получении заказа обязательно проверьте комплектацию всего заказа и каждого товара в отдельности, правильность заполнения сопутствующих документов.
        Убедитесь в отсутствии на товарах видимых механических повреждений.
    </p>
</div>

<div class="accordion-header">
	<b class="underline">1. Как заказать и оплатить товар?</b>
</div>
<div class="accordion-content">
	<p>
		Это просто и легко! Вы можете оформить заказ любым из перечисленных ниже способов:
	</p>
	<ul class="list-red">
		<li>через корзину на сайте</li>
		<li>по телефону 8 (800) 333-71-83 (звонок бесплатный, круглосуточно)</li>
		<li>через Онлайн-Консультанта на сайте</li>
		<li>по электронной почте <a href="mailto:info@textiletorg.ru">info@textiletorg.ru</a>, либо через форму обратной связи на сайте.</li>
	</ul>
	<p>
		Вы можете <a href="https://www.textiletorg.ru/oplata/sposoby-oplaty/">оплатить</a> покупку любым удобным для вас способом при помощи наличного или безналичного расчёта, в том числе банковскими картами и электронными деньгами (Яндекс.Деньги).
	</p>

</div>
<div class="accordion-header">
	<b class="underline">2. Как быстро мне доставят мой заказ?</b>
</div>
<div class="accordion-content">
	<p>
		Доставка вашего заказа будет произведена максимально быстро!
	</p>
	<p>
		В городах-представительствах магазинов "ТекстильТорг" доставка осуществляется на следующий день после оформления заказа, либо в тот же день в случае срочной доставки.
	</p>
	<p>
		Для других регионов РФ отгрузка с нашего ближайшего склада в транспортную компанию производится в день оплаты либо на следующий день после поступления оплаты. Процесс доставки в отдаленный регион занимает от 2 до 5 дней.
	</p>
</div>
<div class="accordion-header">
	 <b class="underline">3. Как происходит гарантийное обслуживание товаров, приобретенных в ТекстильТорге?</b>
</div>
<div class="accordion-content">
	<p>
		На все товары в нашей компании распространяются правила 2-х годового гарантийного облуживания вместо 1-го, потому что мы заботимся о вашем комфорте и удобстве!
	</p>
	<p>
		Все товары, за исключением аксессуаров, обеспечиваются официальным гарантийным талоном и сервисным обслуживанием. Для абсолютного большинства товаров, представленных в нашем магазине, действует расширенная официальная <a href="https://www.textiletorg.ru/garantiya/garantiya-na-tovar/">гарантия</a> – 24 месяца (предоставляется бесплатно на все швейные, вышивальные и вязальные машины, а также оверлоки).
	</p>
	<p>
		Для получения списков сервисных центров в Вашем регионе, либо по вопросам транспортировки оборудования к нам (при отсутствии сервисных центров) необходимо обратиться к нашим специалистам либо на официальные сайты производителей.
	</p>
</div>
<div class="accordion-header">
	<b class="underline">4. Кто отвечает, если товар утерян или испорчен?</b>
</div>
<div class="accordion-content">
	<p>
		Мы несем ответственность за доставку заказанного Вами товара на все 100%!
	</p>
	<p>
		Мы ответственны за доставку Вашего заказа в целости и сохранности из пункта отправки (г. Москва) до транспортной компании.
	</p>
	<p>
		Транспортная компания ответственна за доставку от пункта отправки до пункта получения.
	</p>
	<p>
		Однако мы контролируем заказ до момента передачи его в Ваши руки – всегда!
	</p>

</div>
<div class="accordion-header">
	<b class="underline">5. Как можно забрать заказ самостоятельно?</b>
</div>
<div class="accordion-content">
	<p>
	</p>
	<p>
		Вы можете получить товар в любом из <a href="/poluchenie/nashi-magaziny/" style="color: #ed1b23;">наших магазинов</a> или <a href="/samovyvoz/" style="color: #ed1b23;">пунктов выдачи</a>, на территории РФ. Обязательно предварительно забронируйте товар прежде, чем приезжать в магазин, чтобы получить его!
	</p>
</div>
<!--
<p>
	<b>Внимание!</b>
</p>
<p>
	Доставка иногородним покупателям осуществляется транспортной компанией, выбранной самим покупателем.
</p>
<p>
	Вы можете проконсультироваться со специалистом и попробовать в действии любую интересующую Вас модель на месте. Магазины "ТекстильТорг" работают для Вас ежедневно (без выходных): с 09:00 до 21:00
</p>
-->
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	array(
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "block_feedback",
		"COMPONENT_TEMPLATE" => ".default",
		"EDIT_TEMPLATE" => ""
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>