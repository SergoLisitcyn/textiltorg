<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();?>
<?$APPLICATION->IncludeComponent(
    "custom:buy.prototype",
    "mobile",
    array(
        "ACTION" => "BUY_ONE_CLICK",
		"YANDEX_COUNER" => "zakaz_v_1click",
        "SUCCESS_MESSAGE" => array(
            "FILE" => "bitrix/components/custom/buy.prototype/templates/mobile/template-message.php"
        )
    ),
    false,
    array(
        "HIDE_ICONS" => "Y",
    )
);

// Заменим макрос #CITY# в description
$seoDescription = $arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"];
$regionName = $GLOBALS["GEO_REGION_CITY_NAME"];

if (!empty($seoDescription) && false) { ?>
    <div class="seo-description">
        <?=$seoDescription?>
    </div>    
<?
}

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
