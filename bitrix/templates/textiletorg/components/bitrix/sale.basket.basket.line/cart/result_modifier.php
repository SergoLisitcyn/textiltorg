<?php
$arFilter = array(
	"FUSER_ID" => CSaleBasket::GetBasketUserID(),
	"LID" => SITE_ID,
	"ORDER_ID" => "NULL",
	"CAN_BUY" => "Y",
);
$arSelect = array(
	"QUANTITY",
);

$res = CSaleBasket::GetList(array(), $arFilter, false, false, $arSelect);
$quantityAll = 0;
while($ob = $res->GetNext())
{
	$quantityAll += $ob["QUANTITY"];
}

$arResult['NUM_PRODUCTS'] = $quantityAll;
$GLOBALS['NUM_PRODUCTS'] = $quantityAll;

if (SITE_ID == "by")
{
    $arResult['TOTAL_PRICE'] = preg_replace("/[^\d\.\,]+/", "", $arResult['TOTAL_PRICE']);
    $arResult['TOTAL_PRICE'] = trim($arResult['TOTAL_PRICE'], '.');
	if ($arResult['TOTAL_PRICE'] > 0)
	{
		$arResult['TOTAL_PRICE'] = number_format($arResult['TOTAL_PRICE'], 2, ',', ' ');
	}
}
else
{
    $arResult['TOTAL_PRICE'] = preg_replace("~[^\d\.\,]+~", "", $arResult['TOTAL_PRICE']);
    $arResult['TOTAL_PRICE'] = trim($arResult['TOTAL_PRICE'], '.');
    $arResult['TOTAL_PRICE'] = number_format($arResult['TOTAL_PRICE'], 0, ',', ' ');
}

