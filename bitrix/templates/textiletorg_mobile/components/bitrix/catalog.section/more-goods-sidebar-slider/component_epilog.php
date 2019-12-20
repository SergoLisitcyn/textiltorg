<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arCompare = array();

global $isCatalogCompareScript;

$isCatalogCompareScript = (empty($isCatalogCompareScript))? false: true;

foreach ($_SESSION["CATALOG_COMPARE_LIST"][$arResult["ID"]]["ITEMS"] as $arItem)
{
    $arCompare[] = $arItem["ID"];
}
?>

<?if (empty($isCatalogCompareScript)):?>
    <script type='text/javascript'>
        var compare_catalog=<?=json_encode($arCompare)?>;
    </script>
<?endif?>

<?$isCatalogCompareScript = true?>

<?$APPLICATION->IncludeComponent(
    "custom:buy.prototype",
    "mobile",
    array(
        "ACTION" => "BUY_ONE_CLICK",
        "SUCCESS_MESSAGE" => array(
            "FILE" => "bitrix/components/custom/buy.prototype/templates/.default/template-message.php"
        )
    ),
    false,
    array(
        "HIDE_ICONS" => "Y",
    )
);

// Заменим макрос #CITY# в title
$seoTitle = $arResult["IPROPERTY_VALUES"]["SECTION_META_TITLE"];
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

$seoTitle = str_replace("#CITY#", $regionName, $seoTitle);
$APPLICATION->SetPageProperty("title", $seoTitle);