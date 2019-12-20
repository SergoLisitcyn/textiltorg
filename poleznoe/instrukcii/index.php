<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Инструкции по эксплуатации швейных машин. На примере представлена инструкция по эксплуатации швейной машины.");
$APPLICATION->SetPageProperty("keywords", "инструкции по эксплуатации швейных машин, janome инструкция, швейная машина janome, швейная машинка janome");
$APPLICATION->SetPageProperty("title", "Инструкции по устройству и эксплуатации швейных машин | ТекстильТорг");
$APPLICATION->SetTitle("Инструкции по эксплуатации швейных машинок");
?>
<img alt="" src="/upload/images/Instrukcii.jpg" style="float: right; margin: 0 0px 5px 40px;">
<p>На этой странице представлен каталок с описанием и правилами эксплуатации швейных машин и другой швейной техники. Здесь вы можете скачать нужную вам инструкцию в формате PDF. В самой инструкции есть полное описание, характеристики, устройство и информация о настройке.</p>

<?$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "instructions",
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
        "FILTER_NAME" => "",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "IBLOCK_ID" => "4",
        "IBLOCK_TYPE" => "help",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "INCLUDE_SUBSECTIONS" => "Y",
        "MESSAGE_404" => "",
        "NEWS_COUNT" => "500",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => ".default",
        "PAGER_TITLE" => "Инструкции",
        "PARENT_SECTION" => "",
        "PARENT_SECTION_CODE" => "",
        "PREVIEW_TRUNCATE_LEN" => "",
        "PROPERTY_CODE" => array(
            0 => "FILE",
            1 => "",
        ),
        "SET_BROWSER_TITLE" => "Y",
        "SET_LAST_MODIFIED" => "N",
        "SET_META_DESCRIPTION" => "Y",
        "SET_META_KEYWORDS" => "Y",
        "SET_STATUS_404" => "Y",
        "SET_TITLE" => "N",
        "SHOW_404" => "N",
        "SORT_BY1" => "ID",
        "SORT_BY2" => "ID",
        "SORT_ORDER1" => "ASC",
        "SORT_ORDER2" => "ASC",
        "COMPONENT_TEMPLATE" => "articles"
    ),
    false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>