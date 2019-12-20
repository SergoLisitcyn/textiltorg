<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (!empty($arParams["ACTION_GOODS_FILTER_NAME"]))
{
	//$arProducts = ($arResult["PROPERTIES"]["PRODUCTS"]["VALUE"])? $arResult["PROPERTIES"]["PRODUCTS"]["VALUE"] : 0;
	
	$obProp = CIBlockElement::GetProperty($arResult["IBLOCK_ID"], $arResult["ID"], array("sort" => "asc"), Array("CODE"=>"PRODUCTS"));
	$arProducts = array();
	while($arProp = $obProp->GetNext())
	{
		$arProducts[] = $arProp["VALUE"];
	}
	
	$GLOBALS[$arParams["ACTION_GOODS_FILTER_NAME"]] = array(
		"ID" => $arProducts
	);
}

?>