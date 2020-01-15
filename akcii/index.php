<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Акции от компании ТекстильТорг. Скидки на швейное оборудование");
$APPLICATION->SetPageProperty("keywords", "акции текстильторг, скидки текстильторг, действующие акции, следующие акции, дарим скидки, рассрочка, кредит");
$APPLICATION->SetPageProperty("description", "Акции. На данный момент у нас действуют следующие акции: Дарим 10% Рассрочка 0%. Узнайте подробности у наших менеджеров");
$APPLICATION->SetTitle("Уважаемые покупатели и гости нашего сайта");
?>
    <p style="font-size: 14px;">
        Помимо самых низких цен, высокого качества товаров и огромного ассортимента, который всегда есть в наличии,
        <br>мы предлагаем Вам очень выгодные и интересные акции и скидки!
    </p>
    <p>С ними Вы можете ознакомиться здесь и сейчас.</p>

    <div class="akcii-skidki">
        <a href="/akcii/novogodnee-bezumie/"><img src="/bitrix/templates/textiletorg/aks-img/akcii/bezumie.png"></a>
        <a href="/akcii/darim-10/"><img src="/bitrix/templates/textiletorg/aks-img/akcii/darim10.png"></a>
        <a href="/akcii/nashli-deshevle/"><img src="/bitrix/templates/textiletorg/aks-img/akcii/sale.png"></a>
        <a href="/akcii/ytilizacia/"><img src="/bitrix/templates/textiletorg/aks-img/akcii/util.png"></a>
        <a href="/akcii/rassrochka/"><img src="/bitrix/templates/textiletorg/aks-img/akcii/rassro4ka.png"></a>
        <a href="/akcii/prishla-s-podrugoy-poluchila-skidku/"><img src="/bitrix/templates/textiletorg/aks-img/akcii/beripodrug.png"></a>
        <a href="/akcii/nevazhno-kakaya-karta-skidki-budut/"><img src="/bitrix/templates/textiletorg/aks-img/akcii/kartaskidok.png"></a>
        <a href="/akcii/khochesh-tovar-v-tekstiltorge-kupit-spetspredlozhenie-uspey-poluchit/"><img src="/bitrix/templates/textiletorg/aks-img/akcii/magicterms.png"></a>

    </div>

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

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>