<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult["TEMPLATE_PATH"] = dirname(__FILE__);

if (!empty($_REQUEST["ERROR"]))
{
    $arResult["ERROR"] = strip_tags($_REQUEST["ERROR"], '<br>');
}

$arResult['IS_ADDRESS_SHOW'] = 'N';

if (key($arResult["DELIVERYES"][$arResult["CITY_DELIVERY"]]) == '4' ||
    key($arResult["DELIVERYES"][$arResult["CITY_DELIVERY"]]) == '3')
{
    $arResult['IS_ADDRESS_SHOW'] = 'Y';
}

// Получим список подарков 
$arFilter = Array("IBLOCK_ID" => 40, "ACTIVE" => "Y");
$arSelect = Array("ID", "NAME", "IBLOCK_ID");
$arResult["GIFTS"] = array();
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while($ob = $res->GetNext())
{
    if (SITE_ID == "tp" && $ob["ID"] == 66593) {
        continue;
    }
    $arResult["GIFTS"][$ob["ID"]] = $ob["NAME"];
}

?>

