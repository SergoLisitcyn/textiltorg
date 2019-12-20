<?
define('IS_SHOW_TARGET_MAIL', true);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
CModule::IncludeModule("iblock");
$arResult["PATH"] = array();

global $APPLICATION;

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/vendor/readmore.min.js");

$GLOBALS["CATALOG_ELEMENT_ID"] = $arResult["ID"];
$GLOBALS["CATALOG_ELEMENT_SECTION_ID"] = $arResult["IBLOCK_SECTION_ID"];

$rsPath = CIBlockSection::GetNavChain($arParams["IBLOCK_ID"], $arResult["IBLOCK_SECTION_ID"]);
$rsPath->SetUrlTemplates("", $arParams["SECTION_URL"]);

while($arPath = $rsPath->GetNext())
{
    $arResult["PATH"][] = $arPath;
}

/*foreach($arResult["PATH"] as $arPath)
{
    $APPLICATION->AddChainItem($arPath["NAME"], $arPath["~SECTION_PAGE_URL"]);
}
$APPLICATION->AddChainItem($arResult["NAME"]);*/
?>
<?$APPLICATION->IncludeComponent(
    "custom:buy.prototype",
    "",
    array(
        "ACTION" => "BUY_ONE_CLICK",
		"YANDEX_COUNER" => "zakaz_v_1click",
        "SUCCESS_MESSAGE" => array(
            "FILE" => "bitrix/components/custom/buy.prototype/templates/.default/template-message.php"
        )
    ),
    false,
    array(
        "HIDE_ICONS" => "Y",
    )
);

$APPLICATION->SetPageProperty("title", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]);
$APPLICATION->SetPageProperty("description", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]);
$APPLICATION->SetPageProperty("og:image", "https://".$_SERVER['SERVER_NAME'].$arResult["DETAIL_PICTURE"]["SRC"]);
$description = str_replace($arResult['NAME'] . " для дома.", "", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]);
$APPLICATION->SetPageProperty("og:description", $description);
if (in_array($GLOBALS["GEO_REGION_CITY_NAME"], array("Москва", "Санкт-Петербург"))) {
    $APPLICATION->SetPageProperty("og:title", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]);
} else {
    $APPLICATION->SetPageProperty("og:title", $arResult['NAME'] . " по Супер Цене!");
}

// Заменим макрос #CITY# в description
/*
$seoDescription = $arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"];
$regionName = $GLOBALS["GEO_REGION_CITY_NAME"];

$arCitysEnd = array(
    "Москва" => "Москве",
    "Санкт-Петербург" => "Санкт-Петербурге",
    "Екатеринбург" => "Екатеринбурге",
    "Нижний Новгород" => "Нижнем Новгороде",
    "Ростов-на-Дону" => "Ростове-на-Дону",
);
if (isset($arCitysEnd[$regionName])) {
    $regionName = $arCitysEnd[$regionName];
} else {
    $regionName = "городе " . $regionName;
}

$seoDescription = str_replace("#CITY#", $regionName, $seoDescription);
$APPLICATION->SetPageProperty("description", $seoDescription);


if (preg_match('/^[a-z]+$/i', $arResult["SECTION"]["NAME"])) {
    $trim = '/'.strtolower($arResult["SECTION"]["NAME"]).'/';
    $canonicalUrl = str_replace($trim, '/', $APPLICATION->GetCurPage(false));

    $APPLICATION->SetPageProperty("canonical", $canonicalUrl);
}*/
