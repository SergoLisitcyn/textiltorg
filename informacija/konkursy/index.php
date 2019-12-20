<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Конкурсы. Конкурсы Мой ТекстильТорг 2013 Мой ТекстильТорг 2012 Весна в ТекстильТорг 2012 Тиссура Couture 2011. Москва. Финал. Тиссура Couture 2011. Санкт-Петербург. Тиссура Couture 2011. Екатеринбург. Мой ТекстильТорг 2011 Мой ТекстильТорг 2010");
$APPLICATION->SetPageProperty("keywords", "конкурсы текстильторг 2013, текстильторг 2012, тиссура couture 2011, санкт-петербург тиссура couture 2011, екатеринбург текстильторг 2011, текстильторг 2010");
$APPLICATION->SetPageProperty("title", "Конкурсы в нашем магазине 2010-2013 | ТекстильТорг");
$APPLICATION->SetTitle("Конкурсы");
?><img src="/upload/images/Konkursy_1.jpg" style="float:right; margin: 0 0px 5px 40px;" alt="">
<p>За время долгой и плодотворной работы у нас сложилась добрая традиция – проведение различных конкурсов. Мы проводим их для того, чтобы каждый из Вас смог проявить свои творческие способности, блеснуть своими умениями и, конечно же, получить за это ценные призы и подарки. </p>
<p>В рамках конкурсов мы даём возможность проявить свою творческую фантазию и креативность, создать «произведение искусства», которое затем увидят тысячи посетителей нашего сайта и, тем самым, даже немножко прославиться.</p>
<p>Вас ждут увлекательные задания, дружеская атмосфера, интересные встречи и достойные награды. Мы гарантируем справедливое судейство, открытое обсуждение всех работ и праздничное настроение каждому!</p>
<p>Мы заранее анонсируем конкурсы. Принять в них участие может любой наш покупатель. Главные призы получат только несколько человек, а вот поощрительные призы получают все участники.</p>

<p><b>Следите за нашими анонсами, подавайте заявки и активно участвуйте, проявляйте себя и получайте заслуженные награды и призы!</b></p>
<p class="text-center"><a class="button yellow fancybox m-hidden" href="#competition" title="Оставить заявку на участие в следующем конкурсе">Оставить заявку на участие в следующем конкурсе</a></p>
<?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"competitions",
	array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
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
		"FILTER_NAME" => "",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "7",
		"IBLOCK_TYPE" => "info",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"INCLUDE_SUBSECTIONS" => "Y",
		"MESSAGE_404" => "",
		"NEWS_COUNT" => "200",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Новости",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PROPERTY_CODE" => array(
			0 => "DATE_START",
			1 => "",
		),
		"SET_BROWSER_TITLE" => "Y",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "Y",
		"SET_META_KEYWORDS" => "Y",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "Y",
		"SHOW_404" => "N",
        "SORT_BY1" => "SORT",
        "SORT_BY2" => "SORT",
        "SORT_ORDER1" => "DESC,nulls",
        "SORT_ORDER2" => "DESC,nulls",
		"COMPONENT_TEMPLATE" => "competitions"
	),
	false
);?>

<div id="competition" class="fancybox_block fancy_block">
 <?$APPLICATION->IncludeComponent(
    "custom:form.result.new",
    "konkursy",
    Array(
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A",
        "CHAIN_ITEM_LINK" => "",
        "CHAIN_ITEM_TEXT" => "",
        "EDIT_URL" => "",
        "IGNORE_CUSTOM_TEMPLATE" => "N",
        "LIST_URL" => "",
        "SEF_MODE" => "N",
        "SUCCESS_URL" => "",
        "USE_EXTENDED_ERRORS" => "N",
        "VARIABLE_ALIASES" => Array(
                "RESULT_ID" => "RESULT_ID",
                "WEB_FORM_ID" => "WEB_FORM_ID"
        ),
        "WEB_FORM_ID" => 3,

        "AJAX_MODE" => "Y",  // режим AJAX
        "AJAX_OPTION_SHADOW" => "N", // затемнять область
        "AJAX_OPTION_JUMP" => "Y", // скроллить страницу до компонента
        "AJAX_OPTION_STYLE" => "Y", // подключать стили
        "AJAX_OPTION_HISTORY" => "N",
    )
);?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>