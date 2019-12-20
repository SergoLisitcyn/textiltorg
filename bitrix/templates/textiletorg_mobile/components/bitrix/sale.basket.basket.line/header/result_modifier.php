<?
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
