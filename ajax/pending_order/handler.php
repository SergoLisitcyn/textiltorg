<?
if (isset($_POST['phone']))
{
	global $USER;
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	CModule::IncludeModule("sale");
	CModule::IncludeModule("catalog");
	CModule::IncludeModule("iblock");

	$date = date_create(date('Y-m-d G:i:s'));
	date_add($date, date_interval_create_from_date_string('5 min'));

	$sName = strip_tags(trim($_POST['name']));
	$sEmail = strip_tags(trim($_POST['email']));
	$sPhone = strip_tags(trim($_POST['phone']));
	$sDatatime = date_format($date, 'Y-m-d G:i:s'); //0000-00-00 00:00:00
	$sSity = strip_tags(trim($_POST['sity']));
	$sSum = strip_tags(trim($_POST['sum']));
	$sItem = strip_tags(trim($_POST['item']));
	$session = bitrix_sessid();
	$userId = ($USER->IsAuthorized()) ? $USER->GetID() : "";
	$delivery = strip_tags(trim($_POST['delivery']));
	$payment = strip_tags(trim($_POST['payment']));
    $isMobile = strip_tags(trim($_POST['isMobile']));

	$rsCartItems = CSaleBasket::GetList(
		array(
			"NAME" => "ASC",
			"ID" => "ASC"
		),
		array(
			"FUSER_ID" => CSaleBasket::GetBasketUserID(),
			"LID" => SITE_ID,
			"ORDER_ID" => "NULL",
			"CAN_BUY" => "Y"
		),
		false,
		false,
		array()
	);

	$arCartItems = array();
	while ($arCartItem = $rsCartItems->GetNext())
	{
		$arCartItems[] = array(
			"PRODUCT_ID" => $arCartItem["PRODUCT_ID"],
			"PRODUCT_PRICE_ID" => $arCartItem["PRODUCT_PRICE_ID"],
            "PRICE" => $arCartItem["PRICE"],
            "WEIGHT" => $arCartItem["WEIGHT"],
            "CURRENCY" => $arCartItem["CURRENCY"],
            "QUANTITY" => $arCartItem["QUANTITY"],
            "LID" => $arCartItem["LID"],
            "DELAY" => $arCartItem["DELAY"],
            "CAN_BUY" => $arCartItem["CAN_BUY"],
            "NAME" => $arCartItem["NAME"],
            "PRODUCT_XML_ID" => $arCartItem["PRODUCT_XML_ID"],
            "CATALOG_XML_ID" => $arCartItem["CATALOG_XML_ID"],
            "MODULE" => $arCartItem["MODULE"],
            "NOTES" => $arCartItem["NOTES"],
            "DETAIL_PAGE_URL" => $arCartItem["DETAIL_PAGE_URL"],
            "DISCOUNT_PRICE" => $arCartItem["DISCOUNT_PRICE"],
            "DISCOUNT_NAME" => $arCartItem["DISCOUNT_NAME"],
            "DISCOUNT_VALUE" => $arCartItem["DISCOUNT_VALUE"],
            "DISCOUNT_COUPON" => $arCartItem["DISCOUNT_COUPON"],
		);
	}

	$arFields = array(
		"name" => "'".$sName."'",
		"email" => "'".$sEmail."'",
		"phone" => "'".$sPhone."'",
		"datatime" => "'".$sDatatime."'",
		"sity" => "'".$sSity."'",
		"sum" => "'".$sSum."'",
		"session" => "'".$session."'",
		"user_id" => "'".$userId."'",
		"sale_user" => "'".serialize($arCartItems)."'",
		"site_id" => "'".SITE_ID."'",
		"pay_system_id" => "'".$payment."'",
		"delivery_id" => "'".$delivery."'",
		"is_mobile" => "'".$isMobile."'"
	);

	$status = false;
	$results = $DB->Query("SELECT * FROM pending_order WHERE session = '".$session."'");
	while ($row = $results->Fetch() && !$status)
	{
		$status = true;
	}

	$DB->PrepareFields("pending_order");
	$DB->StartTransaction();
	if($status)
	{
		$DB->Update("pending_order ", $arFields, " WHERE session = '".$session."'", $err_mess.__LINE__);
	}
	else
	{
		$DB->Insert("pending_order", $arFields, $err_mess.__LINE__);
	}

	if (strlen($strError)<=0)
	{
		$DB->Commit();
	}
	else
    {
        $DB->Rollback();
    }
}