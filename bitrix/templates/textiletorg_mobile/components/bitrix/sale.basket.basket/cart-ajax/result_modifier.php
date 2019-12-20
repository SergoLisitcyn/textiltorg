<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
use Bitrix\Main;

$defaultParams = array(
	'TEMPLATE_THEME' => 'blue'
);
$arParams = array_merge($defaultParams, $arParams);
unset($defaultParams);

$arParams['TEMPLATE_THEME'] = (string)($arParams['TEMPLATE_THEME']);
if ('' != $arParams['TEMPLATE_THEME'])
{
	$arParams['TEMPLATE_THEME'] = preg_replace('/[^a-zA-Z0-9_\-\(\)\!]/', '', $arParams['TEMPLATE_THEME']);
	if ('site' == $arParams['TEMPLATE_THEME'])
	{
		$templateId = (string)Main\Config\Option::get('main', 'wizard_template_id', 'eshop_bootstrap', SITE_ID);
		$templateId = (preg_match("/^eshop_adapt/", $templateId)) ? 'eshop_adapt' : $templateId;
		$arParams['TEMPLATE_THEME'] = (string)Main\Config\Option::get('main', 'wizard_'.$templateId.'_theme_id', 'blue', SITE_ID);
	}
	if ('' != $arParams['TEMPLATE_THEME'])
	{
		if (!is_file($_SERVER['DOCUMENT_ROOT'].$this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css'))
			$arParams['TEMPLATE_THEME'] = '';
	}
}
if ('' == $arParams['TEMPLATE_THEME'])
	$arParams['TEMPLATE_THEME'] = 'blue';

foreach ($arResult["GRID"]["ROWS"] as $k => $arItem) {
	$arResult["GRID"]["ROWS"][$k]["DETAIL_PAGE_URL"] = Helper::RemoveOneLavelUrl($arItem["DETAIL_PAGE_URL"]);
}

$arResult["IS_PRICE_RB"] = ($arParams["IS_PRICE_RB"] == "Y") ? "Y": "N";
$arResult["CUSTOM_ALL_COUNT"] = 0;

foreach ($arResult["ITEMS"]["AnDelCanBuy"] as $nItem => $arItem)
{
    $arResult["CUSTOM_ALL_COUNT"] += $arItem["QUANTITY"];
}

foreach ($arResult["GRID"]["ROWS"] as $nItem => $arItem)
{
    if ($arItem["CURRENCY"] == "BYN")
    {
        $arItem["RB_SUM"] = str_replace("руб.", "", $arItem["SUM"]);
        $arItem["SUM"] = number_format(($arItem["PRICE"] * $arItem["QUANTITY"]), 2, ",", " ");
        $arItem["FULL_SUM"] = number_format(($arItem["FULL_PRICE"] * $arItem["QUANTITY"]), 2, ",", " ");

        $arResult["IS_PRICE_RB"] = "Y";
    }
    else
    {
        $arItem["SUM"] = number_format($arItem["PRICE"] * $arItem["QUANTITY"], 0, ",", " ");
        $arItem["FULL_SUM"] = number_format(($arItem["FULL_PRICE"] * $arItem["QUANTITY"]), 0, ",", " ");
    }

    $arResult["GRID"]["ROWS"][$nItem] = $arItem;
}



if ($arResult["IS_PRICE_RB"] != "Y")
{
    $arResult["CUSTOM_ALL_SUM"] = number_format($arResult["allSum"], 0, ".", " ");
}
else
{
    $arResult["CUSTOM_ALL_SUM"] = number_format($arResult["allSum"], 2, ",", " ");
}