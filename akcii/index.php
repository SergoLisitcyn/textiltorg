<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Акции от компании ТекстильТорг. Скидки на швейное оборудование");
$APPLICATION->SetPageProperty("keywords", "акции текстильторг, скидки текстильторг, действующие акции, следующие акции, дарим скидки, рассрочка, кредит");
$APPLICATION->SetPageProperty("description", "Акции. На данный момент у нас действуют следующие акции: Дарим 10% Рассрочка 0%. Узнайте подробности у наших менеджеров");
$APPLICATION->SetTitle("Уважаемые покупатели и гости нашего сайта");
?>
<img alt="" src="/upload/images/Akcii2.jpg" style="float: right; margin: 0 0px 5px 40px;">
<p style="font-size: 14px;">
    Помимо самых низких цен, высокого качества товаров и огромного ассортимента, который всегда есть в наличии, мы предлагаем Вам очень выгодные и интересные акции и скидки!
</p>
<p style="font-size: 14px;">С ними Вы можете ознакомиться здесь и сейчас.</p>

<?$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "actions",
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
        "DISPLAY_BOTTOM_PAGER" => "N",
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
        "IBLOCK_ID" => "1",
        "IBLOCK_TYPE" => "actions",
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
        "SORT_BY2" => "SORT",
        "SORT_ORDER1" => "ASC",
        "SORT_ORDER2" => "ASC",
        "COMPONENT_TEMPLATE" => "articles"
    ),
    false
);?>

<!--<div class="main_akcii_right">
    <div>
        <img src="/upload/images/itisgood.png">
    </div>
</div>-->

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>