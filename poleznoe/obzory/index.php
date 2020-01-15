<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Обзоры швейных машин. Их характеристики и сравнение. Также вы можете оставить свой отзыв по лучшей на ваш взгляд швейной машинке. В разделе можно просмотреть все имеющиеся у нас бренды");
$APPLICATION->SetPageProperty("keywords", "тест драйвы швейных машин, обзоры и сравнение швейных машинок, обзоры швейных машин, швейные машины отзывы, сравнение швейных машин, швейные машины характеристики");
$APPLICATION->SetPageProperty("title", "Тест драйвы и обзоры швейных, вышивальных, вязальных машин и другой техники | ТекстильТорг");
$APPLICATION->SetTitle("Тест драйвы и обзоры");
?>

<div class="halfcirclebox-text">
    <img alt="" src="/bitrix/templates/textiletorg/aks-img/o-nas/test-drive.png" style="float: right; margin: 0 0px 5px 40px;">
	<p>Вы хотите узнать о воможностях и функциях лучших моделей техники, представленной в нашем каталоге?</p>
<p>ТекстильТорг представляет вашему вниманию увлекательные и полезные обзоры - тест-драйвы.
<b>Тест-драйв</b> от ТекстильТорга – это показательные выступления вязальных, вышивальных и швейных машин, а также любой другой техники.</p>
<p>Мы самостоятельно тестируем технику, составляем подробный отчет о результатах тестирования и публикуем его на страницах нашего сайта. Демонстрация функционала моделей производится в любом из наших магазинов абсолютно <b>бесплатно</b>.</p>
<p>Вы всегда можете позвонить нам по бесплатному телефону 8 (800) 333-71-83 (круглосуточно и без выходных) и оставить заявку на проведение тест-драйва определенной модели.</p>
<p>Мы всегда рады помочь Вам сделать лучший выбор!</p>
</div>

<?$APPLICATION->IncludeComponent(
    "custom:catalog.filter",
    "reviews",
    Array(
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "36000000",
        "CACHE_TYPE" => "A",
        "FIELD_CODE" => array("SECTION_ID",""),
        "FILTER_NAME" => "arrFilterReviews",
        "IBLOCK_ID" => "5",
        "IBLOCK_TYPE" => "help",
        "LIST_HEIGHT" => "5",
        "NUMBER_WIDTH" => "5",
        "PAGER_PARAMS_NAME" => "arrPager",
        "PRICE_CODE" => array(),
        "PROPERTY_CODE" => array("BRAND",""),
        "SAVE_IN_SESSION" => "N",
        "TEXT_WIDTH" => "20"
    )
);?>


<?$APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"reviews", 
	array(
		"ACTIVE_DATE_FORMAT" => "",
		"ADD_SECTIONS_CHAIN" => "Y",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_NAME" => "arrFilterReviews",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "5",
		"IBLOCK_TYPE" => "help",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"INCLUDE_SUBSECTIONS" => "Y",
		"MESSAGE_404" => "",
		"NEWS_COUNT" => "20",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Статьи",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"SET_BROWSER_TITLE" => "Y",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "Y",
		"SET_META_KEYWORDS" => "Y",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SORT_BY1" => "SORT",
		"SORT_BY2" => "ID",
		"SORT_ORDER1" => "DESC",
		"SORT_ORDER2" => "ASC",
		"COMPONENT_TEMPLATE" => "reviews"
	),
	false
);?>
<br>
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