<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Швейные машины, оверлоки, вышивальные машины, товары для шитья в Москве по самым низким ценам - ТекстильТорг");
$APPLICATION->SetPageProperty("keywords", "купить швейные машины, швейные машинки, оверлоки");
$APPLICATION->SetPageProperty("description", "ТекстильТорг – сеть магазинов с огромным выбором швейных машинок, оверлоков, а так же товаров для шитья и рукоделия. Мы гарантируем СуперЦены и 100% наличие на все товары представленные в нашем каталоге.");
if (!defined("IS_MOBILE")) {
	$APPLICATION->SetTitle("Магазин швейной и мелкой бытовой техники ");

} else {
	$APPLICATION->SetTitle("ТекстильТорг — магазин швейной и гладильной техники");

} ?>
    <div id="userstyle_maincontent">


	<div id="seo-source">
		<?if (defined("IS_MOBILE")): ?>
			<div class="index-text-center">
						<h1 class="header-index"><?$APPLICATION->ShowTitle(true)?></h1>
				<p class="hidden-xs hidden-sm">В нашем интернет-магазине Вы найдёте модели лучших швейных, вышивальных, вязальных машин, оверлоки, коверлоки, технику для глажения и уборки известных мировых производителей, а также огромный ассортимент аксессуаров к ним. Вас ждёт отличный сервис, бесплатный тест-драйв и быстрая доставка в любую точку России.</p>
			</div>
		<? endif; ?>

		<div class="why-index" >
			<div style = "text-align: center;"><h2 class="hidden-xs hidden-sm header-index">Что мы можем Вам предложить?</h2></div>
			<ul>
				<li>
					<div class="wi_img"><i class="tt-icons we-have-all-icon"></i></div>
					<div class="wi_text">
						<div class="wi_text-head">У нас есть всё, что Вам нужно </div>
						Вы всегда найдёте то, что хотели, если посетите наш магазин.
					</div>
				</li>
                <li>
                    <div class="wi_img"><i class="tt-icons exclusive-product-icon"></i></div>
                    <div class="wi_text">
                        <div class="wi_text-head">Предложение<br>эксклюзивного товара</div>
                        Только в ТекстильТорге Вы сможете найти эксклюзивные модели, которых нет на других торговых площадках.
                    </div>
                </li>
                <li>
                    <div class="wi_img"><i class="tt-icons support-24-hour-icon"></i></div>
                    <div class="wi_text">
                        <div class="wi_text-head">Консультируем круглосуточно — ценим каждого покупателя!</div>
                        Если Вы никак не можете сделать выбор, наши менеджеры придут к Вам на помощь, проконсультируют по интересующим Вас вопросам и дадут подробную информацию по любой модели.
                    </div>
                </li>
				<li>
					<div class="wi_img"><i class="tt-icons new-assortment-icon"></i></div>
					<div class="wi_text">
						<div class="wi_text-head">Частое<br>обновление ассортимента </div>
						Мы ежеминутно отслеживаем информацию производителей о новинках и как можно быстрее представляем их Вам.
					</div>
				</li>
				<li>
					<div class="wi_img"><i class="tt-icons super-price-icon"></i></div>
					<div class="wi_text">
						<div class="wi_text-head">У нас антикризисные цены </div>
						Расценки на наши товары всегда были, есть и остаются ниже рыночных. Секрет в том, что мы работаем напрямую с производителями.
					</div>
				</li>
				<li>
					<div class="wi_img"><i class="tt-icons check-mark-index"></i></div>
					<div class="wi_text">
						<div class="wi_text-head">Тестируем машинки в Вашем присутствии </div>
						При личном посещении нашего магазина Вы сможете получить всю информацию об интересующем Вас товаре и проверить его в действии, заказав бесплатный тест-драйв.
					</div>
				</li>
			</ul>
		</div>


		<div class="accordion" id="accordion">
			<div data-target="technology">
				Как найти и купить лучшую технику?
                <span class="tt-icons arrow-bottom-icon"></span>
			</div>
			<div id="technology">
				<p>Специально для Вас мы подготовили на сайте <a href="https://www.textiletorg.ru/poleznoe/obzory/">раздел</a> с полезной информацией, которая поможет Вам разобраться в огромном мире технических характеристик и разнообразии функций представленных моделей. </p>
				<p>Наши обзорные статьи и бесплатные тест-драйвы о товарах расскажут о том:</p>
				<p>· как работают швейно-вышивальные машинки, <a href="https://www.textiletorg.ru/overloki/">оверлоки</a>, а также парогенераторы и <a href="https://www.textiletorg.ru/gladilnye-sistemy/">гладильные системы</a>;</p>
				<p>· какие дополнительные аксессуары могут пригодиться для улучшения функционала Вашей влажно-тепловой и швейно-вышивальной техники;</p>
				<p>· какие подарки и акции Вас ждут.</p>
				<p>Кроме того, мы составили подробное описание каждой модели и приложили инструкции по эксплуатации. Для всестороннего знакомства с товаром мы размещаем авторитетное мнение экспертов и отзывы тех, кто уже приобрёл <a href="https://www.textiletorg.ru/shveynye-mashiny/">швейные машинки</a> в нашем магазине.</p>
				<p>Определившись с выбором, Вы можете купить швейную машину и любую другую технику в нашем стационарном магазине <a href="https://www.textiletorg.ru/kontakty/">по адресу</a> или заказать с доставкой к Вам на дом.</p>
			</div>
			
			<div data-target="production">
				Как выбрать подходящий товар по выгодной цене?
                <span class="tt-icons arrow-bottom-icon"></span>
			</div>
			<div id="production">
				<p>За прошедшее время машинка претерпела качественные изменения, увеличился её функционал, она стала электронной и даже компьютеризированной. Более того, к швейным машинкам добавились простые и умелые <a href="https://www.textiletorg.ru/vyazalnye-mashiny/">вязальные машины</a><b>, </b>значительно облегчившие создание свитеров и шарфов. Но производители не останавливаются на достигнутом — они изо дня в день придумывают новые модификации и расширяют функционал созданных моделей такой нужной и полезной для дома техники.</p>
				<p>Часто случается так, что Вы заходите в магазин, смотрите на витрины, и глаза «разбегаются». Как сделать правильный выбор, если большинство моделей схожи внешне, а их цены разнятся? Если одна машинка стоит в несколько раз дешевле другой? Диапазон цен на парогенераторы, отпариватели, утюги и пылесосы тоже широк. </p>
				<p>Весь секрет в том, что цена на технику зависит от нескольких факторов:</p>
				<p>· набора функций, которыми обладает устройство;</p>
				<p>· технических параметров (количества выполняемых операций);</p>
				<p>· типа швейной или вязальной машины (электромеханическая, электронная, компьютеризированная);</p>
				<p>· страны-производителя.</p>
				<p>А выбор самого покупателя, как правило, зависит от его личных пожеланий и финансовых возможностей. Только сопоставив два этих важных аргумента, можно сделать оптимально правильный выбор.</p>
			</div>
			
			<div data-target="sewingmachines">
				Швейные машинки: какими бывают и как различаются?
                <span class="tt-icons arrow-bottom-icon"></span>
			</div>
			<div id="sewingmachines">
				<p><b><i>Швейные машинки с электроприводом (электрические)</i></b></p>
				<p>Это самый распространенный вариант. В такой технике вращение махового колеса обеспечивает электромотор. Как правило, в комплект входит и ножная педаль. Нажимая на неё, Вы приводите в движение мотор и маховое колесо. Силой нажатия не педаль регулируется и скорость шитья. Машины без электропривода относятся к механическим (ручным или ножным) и сейчас встречаются крайне редко потому как давно уже не производятся.</p>
				<p><b><i>Электромеханические машины</i></b></p>
				<p>В таких моделях управление осуществляется с помощью механических переключателей, которые располагаются на передней панели прибора. Вы вручную производите установку натяжения нити, типа строчки, длину и ширину стежка.</p>
				<p><b><i>Электронные машины</i></b></p>
				<p>Модели оборудованы электронными панелями, через которые и происходит управление машиной. Встроенный микропроцессор контролирует перемещение иглы относительно ткани. Есть автоматическая настройка некоторых параметров шитья. У таких машин нет ограничения по сложности шитья — они способны выполнять большое количество строчек.</p>
				<p><b><i>Компьютеризированные машины</i></b></p>
				<p>Это вершина эволюции швейных машин. Они на порядок проще в обращении и управлении и не имеют ограничений по всем направлениям. Легко справляются с различными видами строчек, полностью отвечают за управление и выполняют огромный список операций. Все, что требуется от Вас в работе с такой моделью — менять шпульки и наслаждаться её безупречной работой.</p>
			</div>
		</div>

        <div class = "ay-main-container" id = "n_index_clients">

            <div class = "n_index_clients_head" >
                <div class = "n_index_clients_head_text" > НАШИ КЛИЕНТЫ </div>
            </div>

            <div class = "n_index_clients_blocks">
                <div class = "n_index_clients_block">
                    <table>
                        <tr>
                            <td> <div class = "n_index_clients_img"></div></td>
                        </tr>
                        <tr>
                            <td> НАЗВАНИЕ ФИРМЫ </td>
                        </tr>
                    </table>
                </div>
                <div class = "n_index_clients_block">
                    <table>
                        <tr>
                            <td> <div class = "n_index_clients_img"></div></td>
                        </tr>
                        <tr>
                            <td> НАЗВАНИЕ ФИРМЫ </td>
                        </tr>
                    </table>
                </div>
                <div class = "n_index_clients_block">
                    <table>
                        <tr>
                            <td> <div class = "n_index_clients_img"></div></td>
                        </tr>
                        <tr>
                            <td> НАЗВАНИЕ ФИРМЫ </td>
                        </tr>
                    </table>
                </div>
                <div class = "n_index_clients_block">
                    <table>
                        <tr>
                            <td> <div class = "n_index_clients_img"></div></td>
                        </tr>
                        <tr>
                            <td> НАЗВАНИЕ ФИРМЫ </td>
                        </tr>
                    </table>
                </div>
                <div class = "n_index_clients_block">
                    <table>
                        <tr>
                            <td> <div class = "n_index_clients_img"></div></td>
                        </tr>
                        <tr>
                            <td> НАЗВАНИЕ ФИРМЫ </td>
                        </tr>
                    </table>
                </div>
                <div class = "n_index_clients_block">
                    <table>
                        <tr>
                            <td> <div class = "n_index_clients_img"></div></td>
                        </tr>
                        <tr>
                            <td> НАЗВАНИЕ ФИРМЫ </td>
                        </tr>
                    </table>
                </div>
            </div>


        </div>


	</div>

        <? $arrFilterProducts = array("ID" => array(1617,610,443,252,474)); ?>
        <?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section", 
	"slider", 
	array(
		"IBLOCK_ID" => "8",
		"IBLOCK_TYPE" => "catalog",
		"SHOW_ALL_WO_SECTION" => "Y",
		"INCLUDE_SUBSECTIONS" => "Y",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "Y",
		"FILTER_NAME" => "arrFilterProducts",
		"PRICE_CODE" => $GLOBALS["CITY_PRICE_CODE"],
		"GEO_REGION_CITY_NAME" => $GLOBALS["GEO_REGION_CITY_NAME"],
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"REGION_PRICE_CODE_DEFAULT" => "Москва",
		"COMPONENT_TITLE" => "Лидеры продаж",
		"COMPONENT_TITLE_COLOR" => "red",
		"COMPONENT_TEMPLATE" => "slider",
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_CODE" => "",
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"CUSTOM_FILTER" => "",
		"HIDE_NOT_AVAILABLE" => "N",
		"HIDE_NOT_AVAILABLE_OFFERS" => "N",
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER2" => "desc",
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER2" => "desc",
		"PAGE_ELEMENT_COUNT" => "18",
		"LINE_ELEMENT_COUNT" => "1",
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_LIMIT" => "5",
		"BACKGROUND_IMAGE" => "-",
		"SECTION_URL" => "",
		"DETAIL_URL" => "",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SEF_MODE" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"SET_TITLE" => "Y",
		"SET_BROWSER_TITLE" => "Y",
		"BROWSER_TITLE" => "-",
		"SET_META_KEYWORDS" => "Y",
		"META_KEYWORDS" => "-",
		"SET_META_DESCRIPTION" => "Y",
		"META_DESCRIPTION" => "-",
		"SET_LAST_MODIFIED" => "N",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"CONVERT_CURRENCY" => "N",
		"BASKET_URL" => "/personal/basket.php",
		"USE_PRODUCT_QUANTITY" => "N",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRODUCT_PROPERTIES" => array(
		),
		"OFFERS_CART_PROPERTIES" => array(
		),
		"DISPLAY_COMPARE" => "N",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Товары",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SET_STATUS_404" => "N",
		"SHOW_404" => "N",
		"MESSAGE_404" => "",
		"COMPATIBLE_MODE" => "Y",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N"
	),
	false
);?>

        <p class="hidden-xs hidden-sm main n_desc_only">В нашем интернет-магазине Вы найдёте модели лучших швейных, вышивальных, вязальных машин, оверлоки, коверлоки, технику для глажения и уборки известных мировых производителей, а также огромный ассортимент аксессуаров к ним. Вас ждёт отличный сервис, бесплатный тест-драйв и быстрая доставка в любую точку России.</p>

        <?$APPLICATION->IncludeComponent(
        "bitrix:catalog.section",
        "slider",
        array(
            "IBLOCK_ID" => "8",
            "IBLOCK_TYPE" => "catalog",
            "SHOW_ALL_WO_SECTION" => "Y",
            "INCLUDE_SUBSECTIONS" => "Y",
            "CACHE_FILTER" => "Y",
            "CACHE_GROUPS" => "N",
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => "Y",
            "FILTER_NAME" => "arrFilterProducts",
            "PRICE_CODE" => $GLOBALS["CITY_PRICE_CODE"],
            "GEO_REGION_CITY_NAME" => $GLOBALS["GEO_REGION_CITY_NAME"],
            "ADD_PROPERTIES_TO_BASKET" => "Y",
            "MESS_BTN_ADD_TO_BASKET" => "В корзину",
            "ADD_TO_BASKET_ACTION" => "ADD",
            "REGION_PRICE_CODE_DEFAULT" => "Москва",
            "COMPONENT_TITLE" => "Новинки",
            "COMPONENT_TITLE_COLOR" => "yellow",
        ),
        false
        );?>
        <?$APPLICATION->IncludeComponent(
        "bitrix:catalog.section",
        "slider",
        array(
            "IBLOCK_ID" => "8",
            "IBLOCK_TYPE" => "catalog",
            "SHOW_ALL_WO_SECTION" => "Y",
            "INCLUDE_SUBSECTIONS" => "Y",
            "CACHE_FILTER" => "Y",
            "CACHE_GROUPS" => "Y",
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => "Y",
            "FILTER_NAME" => "arrFilterProducts",
            "PRICE_CODE" => $GLOBALS["CITY_PRICE_CODE"],
            "GEO_REGION_CITY_NAME" => $GLOBALS["GEO_REGION_CITY_NAME"],
            "ADD_PROPERTIES_TO_BASKET" => "Y",
            "MESS_BTN_ADD_TO_BASKET" => "В корзину",
            "ADD_TO_BASKET_ACTION" => "ADD",
            "REGION_PRICE_CODE_DEFAULT" => "Москва",
            "COMPONENT_TITLE" => "Скидки и акции",
            "COMPONENT_TITLE_COLOR" => "yellow",
        ),
        false
        );?>


	<p class="hidden-xs hidden-sm"></p>
	
	<div id="seo-target"></div>
	<script>
		$(function(){
			var content = $("#seo-source").html();
			$("#seo-source").remove();
			$("#seo-target").append(content);
			$('div[data-target]', $("#accordion")).click(function(){
                $(this).toggleClass("active");
                $(this).find(".tt-icons").toggleClass('arrow-bottom-icon arrow-top-icon');
				//Overrided by jQury-UI plugin
				$('#' + $(this).data('target')).toggle();
			});
		});
	</script>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>