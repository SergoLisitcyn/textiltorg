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
    <script>
        var compare_catalog=<?=json_encode($arCompare)?>;
    </script>
<?endif?>

<?$isCatalogCompareScript = true?>

<?$APPLICATION->IncludeComponent(
    "custom:buy.prototype",
    "",
    array(
        "ACTION" => "BUY_ONE_CLICK",
		"YANDEX_COUNER" => "oformit_zakaz",
        "SUCCESS_MESSAGE" => array(
            "FILE" => "bitrix/components/custom/buy.prototype/templates/.default/template-message.php"
        )
    ),
    false,
    array(
        "HIDE_ICONS" => "Y",
    )
);

CModule::IncludeModule("iblock");
$arResult["PATH"] = array();

$rsPath = CIBlockSection::GetNavChain($arParams["IBLOCK_ID"], $arResult["ID"]);
$rsPath->SetUrlTemplates("", $arParams["SECTION_URL"]);

while($arPath = $rsPath->GetNext())
{
    $arResult["PATH"][] = $arPath;
}
/*
foreach($arResult["PATH"] as $arPath)
{
    $APPLICATION->AddChainItem($arPath["NAME"], $arPath["~SECTION_PAGE_URL"]);
}
*/
// Заменим макрос #CITY# в title
$seoTitle = $arResult["IPROPERTY_VALUES"]["SECTION_META_TITLE"];
$seoDescription = $arResult["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"];
if(preg_match("/spb.textiletorg/i",$_SERVER["SERVER_NAME"])){
    $brandNamesLine = getBrandNamesLine();

    $seoTitle = str_replace("#CITY#", "Санкт-Петербурге", $seoTitle);
    $seoTitle = str_replace("#FILTER_BRAND#", $brandNamesLine, $seoTitle);

	if($arParams["TAGS_PAGE"] != "Y")
		$APPLICATION->SetPageProperty("keywords", $arResult["IPROPERTY_VALUES"]["SECTION_META_KEYWORDS"]);
    if($arParams["TAGS_PAGE"] != "Y")
        $APPLICATION->SetPageProperty("title", $seoTitle);
    $seoDescription = str_replace("#FILTER_BRAND#", $brandNamesLine, $seoDescription);
    if($arParams["TAGS_PAGE"] != "Y")
        $APPLICATION->SetPageProperty("description", $seoDescription);
} else {
    $brandNamesLine = getBrandNamesLine();

    $seoTitle = str_replace("#CITY#", "Москве", $seoTitle);
    $seoTitle = str_replace("#FILTER_BRAND#", $brandNamesLine, $seoTitle);

    if($arParams["TAGS_PAGE"] != "Y")
        $APPLICATION->SetPageProperty("keywords", $arResult["IPROPERTY_VALUES"]["SECTION_META_KEYWORDS"]);
    if($arParams["TAGS_PAGE"] != "Y")
        $APPLICATION->SetPageProperty("title", $seoTitle);
    $seoDescription = str_replace("#FILTER_BRAND#", $brandNamesLine, $seoDescription);
    if($arParams["TAGS_PAGE"] != "Y")
        $APPLICATION->SetPageProperty("description", $seoDescription);
}

function getBrandNamesLine() {
    global $arSelectBrands;
    $brandNamesLine = "";
    if (count($arSelectBrands) > 0) {
        $brandNames = array();
        foreach ($arSelectBrands as $brand) {
            $brandNames[] = $brand["VALUE"];
        }
        $brandNamesLine .= implode(", ", $brandNames);
    }
    if ('' !== $brandNamesLine) {
        $brandNamesLine = $brandNamesLine.' ';
    }
    return $brandNamesLine;
}