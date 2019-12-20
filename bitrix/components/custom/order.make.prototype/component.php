<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

require_once(realpath(dirname(__FILE__))."/functions.php");

global $USER;

if (!CModule::IncludeModule("catalog"))
{
    ShowError("Модуль catalog не подключен");
    return;
}

if (!CModule::IncludeModule("sale"))
{
    ShowError("Модуль sale не подключен");
    return;
}

if (isset($_REQUEST["skritoe_pole"]) && !empty($_REQUEST["skritoe_pole"]))
{
    ShowError("Ошибка!");
    return;
}

if(!isset($arParams["CACHE_TIME"]))
{
    $arParams["CACHE_TIME"] = 36000000;
}

$arResult["BASKET_COUNT_PRODUCT"] = CSaleBasket::GetList(
    false,
    array(
        "FUSER_ID" => $_SESSION["SALE_USER_ID"],
        "LID" => SITE_ID,
        "ORDER_ID" => "NULL"
    ),
    false,
    false,
    array("ID")
)->SelectedRowsCount();

if ($_REQUEST["FORM_ACTION"] == "ORDER" && check_bitrix_sessid())
{
    $arResult["PERSON_TYPE_ID"] = (!empty($_REQUEST["PERSON_TYPE_ID"]) && intval($_REQUEST["PERSON_TYPE_ID"]))? intval($_REQUEST["PERSON_TYPE_ID"]): false;

    if (!$arResult["PERSON_TYPE_ID"])
    {
        return false;
    }
    
    if (!$USER->IsAuthorized())
    {
        $arResult["LOGIN"] = "anonymous_".RandString(8);
        $arResult["PASSWORD"] = RandString(8);
        $arResult["EMAIL"] = $arResult["LOGIN"]."@example.ru";
    }
	if (!empty($_REQUEST["GIFT_BAS"])) {
		Add2BasketByProductID(intval($_REQUEST["GIFT_BAS"]), 1);
	}
	
    $arResult["NAME"] = (!empty($_REQUEST["NAME"])) ? $_REQUEST["NAME"] : "";
    $arResult["PHONE"] = (!empty($_REQUEST["PHONE"])) ? $_REQUEST["PHONE"] : "";
    $arResult["EMAIL"] = (!empty($_REQUEST["EMAIL"])) ? $_REQUEST["EMAIL"] : $arResult["EMAIL"];

    $arResult["CITY"] = (!empty($_REQUEST["CITY"])) ? $_REQUEST["CITY"] : "";
    $arResult["DELIVERY"] = (!empty($_REQUEST["DELIVERY"])) ? $_REQUEST["DELIVERY"] : "";

    $arResult["STORE"] = (!empty($_REQUEST["STORE"])) ? $_REQUEST["STORE"] : "";
    $arResult["PAYMENT"] = (!empty($_REQUEST["PAYMENT"])) ? $_REQUEST["PAYMENT"] : "";
    $arResult["ADDRESS"] = (!empty($_REQUEST["ADDRESS"])) ? $_REQUEST["ADDRESS"] : "";

    $arResult["COMMENT"] = (!empty($_REQUEST["COMMENT"])) ? $_REQUEST["COMMENT"] : "";
    $arResult["COMMENT"] .= ($_REQUEST["IS_MOBILE"] == 1) ? " (мобильная версия) " : " (основная версия сайта) ";

    $arResult["UR_NAME"] = (!empty($_REQUEST["UR_NAME"])) ? $_REQUEST["UR_NAME"] : "";
    $arResult["UR_PHONE"] = (!empty($_REQUEST["UR_PHONE"])) ? $_REQUEST["UR_PHONE"] : "";
    $arResult["UR_EMAIL"] = (!empty($_REQUEST["UR_EMAIL"])) ? $_REQUEST["UR_EMAIL"] : $arResult["EMAIL"];

    $arResult["UR_CITY"] = (!empty($_REQUEST["UR_CITY"])) ? $_REQUEST["UR_CITY"] : "";
    $arResult["UR_CONTACT_ADDRESS"] = (!empty($_REQUEST["UR_CONTACT_ADDRESS"])) ? $_REQUEST["UR_CONTACT_ADDRESS"] : "";

    $arResult["UR_ORG"] = (!empty($_REQUEST["UR_ORG"])) ? $_REQUEST["UR_ORG"] : "";
    $arResult["UR_INN"] = (!empty($_REQUEST["UR_INN"])) ? $_REQUEST["UR_INN"] : "";
    $arResult["UR_KPP"] = (!empty($_REQUEST["UR_KPP"])) ? $_REQUEST["UR_KPP"] : "";
    $arResult["UR_BIK"] = (!empty($_REQUEST["UR_BIK"])) ? $_REQUEST["UR_BIK"] : "";
    $arResult["UR_CITY"] = (!empty($_REQUEST["UR_CITY"])) ? $_REQUEST["UR_CITY"] : "";
    $arResult["UR_BANK"] = (!empty($_REQUEST["UR_BANK"])) ? $_REQUEST["UR_BANK"] : "";

    try
    {
        if (empty($arResult["BASKET_COUNT_PRODUCT"]))
        {
            throw new Exception("В корзине нет полей");
        }

        $userName = ($arResult["PERSON_TYPE_ID"] == 1)? $arResult["NAME"]: $arResult["UR_NAME"];
        $userPhone = ($arResult["PERSON_TYPE_ID"] == 1)? $arResult["PHONE"]: $arResult["UR_PHONE"];

        if (empty($userName) || empty($userPhone) ||
            empty($arResult["DELIVERY"]) || empty($arResult["PAYMENT"]))
        {
            throw new Exception("Не заполнены обязательные поля");
        }

        if (!$USER->IsAuthorized())
        {
            $obUser = new CUser;

            $arFields = array(
                "NAME"              => $userName,
                "EMAIL"             => $arResult["EMAIL"],
                "LOGIN"             => $arResult["LOGIN"],
                "LID"               => SITE_ID,
                "ACTIVE"            => "Y",
                "GROUP_ID"          => array(3, 4),
                "PASSWORD"          => $arResult["PASSWORD"],
                "CONFIRM_PASSWORD"  => $arResult["PASSWORD"],
                "PERSONAL_PHONE"    => $userPhone,
            );

            $userId = $obUser->Add($arFields);

            if (!intval($userId))
                throw new Exception($obUser->LAST_ERROR);

            $USER->Authorize($userId, true);
        }
        else
        {
            $userId = $USER->GetID();
        }

        $rsCartItems = CSaleBasket::GetList(
            array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(true),
                "LID" => SITE_ID,
                "DELAY" => "N",
                "CAN_BUY" => "Y",
                "ORDER_ID" => NULL
            ),
            false,
            false,
            array()
        );


        $rsLocation = CSaleLocation::GetList(
            array(
                "ID" => $arResult["CITY"]
            ),
            array(
                "ID" => $arResult["CITY"],
                "LID" => LANGUAGE_ID
            ),
            false,
            false,
            array()
        );

        $arLocation = $rsLocation->Fetch();

        $orderPrice = 0;
        $orderCurrency = 0;
        $orderWeight = 0;

        $arCartItemsId = array();
        $arCartItems = array();
        $arCartQuantity = array();
        while ($arCartItem = $rsCartItems->GetNext())
        {
            $orderPrice += $arCartItem["PRICE"] * $arCartItem["QUANTITY"];
            $orderCurrency = $arCartItem["CURRENCY"];
            $orderWeight += ($arCartItem["WEIGHT"] * $arCartItem["QUANTITY"]);

            $arCartQuantity[$arCartItem["PRODUCT_ID"]] = $arCartItem["QUANTITY"];
            $arCartItemsId[] = $arCartItem["PRODUCT_ID"];
            $arCartItems[] = $arCartItem;
        }
		
		$arOrder = array(
            "SITE_ID" => SITE_ID,
            "USER_ID" => $GLOBALS["USER"]->GetID(),
            "ORDER_PRICE" => $orderPrice,
            "ORDER_WEIGHT" => $orderWeight,
            "BASKET_ITEMS" => $arCartItems
        );

        $arOptions = array(
            "COUNT_DISCOUNT_4_ALL_QUANTITY" => "Y",
        );

        $arErrors = array();

        CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);

        $orderPrice = 0;
        foreach ($arOrder["BASKET_ITEMS"] as $arOneItem)
        {
            $orderPrice += ($arOneItem["PRICE"] * $arOneItem["QUANTITY"]);
        }

        // delivery price calc
        $calcSum = 0;
        $isDeliveryFree = false;
        if (($arResult["DELIVERY"] == 4 || $arResult["DELIVERY"] == 14) && !empty($arCartItemsId))
        {
            if (\Bitrix\Main\Loader::includeModule('ayers.delivery'))
            {
                $isInShops = \Ayers\Delivery\CalcPrice::IsInShops();

                if (!$isInShops && SITE_ID == 's1' && $arLocation["CITY_NAME"])
                {
                    $arItems = CIBlockElement::GetList(
                        array(
                            "SORT" => "ASC"
                        ),
                        array(
                            "ID" => $arCartItemsId
                        ),
                        false,
                        false,
                        array("ID", "NAME", "IBLOCK_ID", "IBLOCK_SECTION_ID", "CATALOG_WEIGHT", "CATALOG_WIDTH", "CATALOG_HEIGHT", "CATALOG_LENGTH", "PROPERTY_DELIVERY", "CATALOG_GROUP_1")
                    );

                    while ($arItem = $arItems->Fetch())
                    {
                        $optimalCompany = \Ayers\Delivery\CalcPrice::GetOptimalCompany4City($_SESSION['GEO_REGION_CITY_NAME']);
                        $arOptimalDelivery4Items = \Ayers\Delivery\CalcPrice::GetOptimalDelivery4Items(
                            $optimalCompany,
                            $arLocation['CITY_NAME'],
                            $arItem['CATALOG_PRICE_1'],
                            array($arItem),
                            true
                        );

                        if ($arOptimalDelivery4Items)
                        {
                            $calcSum += ($arResult["DELIVERY"] == 4) ?
                                $arOptimalDelivery4Items['DELIVERY']['STANDART']['DELIVER'] * $arCartQuantity[$arItem['ID']]:
                                $arOptimalDelivery4Items['DELIVERY']['EXPRESS']['DELIVER'] * $arCartQuantity[$arItem['ID']];
                        }

                        if ($arItem["PROPERTY_DELIVERY_VALUE"] == "Бесплатная по РФ" && $arResult["DELIVERY"] == 4)
                        {
                            $isDeliveryFree = true;
                        }
                    }
                }
            }
        }

        $calcSum = ($isDeliveryFree || $calcSum === 0)? 0.000001: $calcSum;
		
		// Disable order notification
		\Bitrix\Sale\Notify::setNotifyDisable(true);

        $isOrderRepeat = OrderRepeat::isOrderRepeat($userPhone);
		
        $orderId = CSaleOrder::Add(
            array(
                "LID" => SITE_ID,
                "PERSON_TYPE_ID" => $arResult["PERSON_TYPE_ID"],
                "PAYED" => "N",
                "CANCELED" => "N",
                "STATUS_ID" => ($isOrderRepeat) ? "D" : "N",
                "PRICE" => $orderPrice,
                "CURRENCY" => $orderCurrency,
                "USER_ID" => $userId,
                "PAY_SYSTEM_ID" => $arResult["PAYMENT"],
                "DELIVERY_ID" => $arResult["DELIVERY"],
                "ALLOW_DELIVERY" => "N",
                "PRICE_DELIVERY" => $calcSum,
                "TAX_VALUE" => 0.0,
            )
        );

        if (!intval($orderId))
        {
            throw new Exception("Ошибка. Заказ не добавлен.");
        }

        CSaleBasket::OrderBasket($orderId, $_SESSION["SALE_USER_ID"], SITE_ID);

        if (!empty($arParams["FIELDS"][$arResult["PERSON_TYPE_ID"]]))
        {

            foreach ($arParams["FIELDS"][$arResult["PERSON_TYPE_ID"]] as $fieldCode)
            {
                if (!empty($arResult[$fieldCode]))
                {
                    if (!AddOrderProperty($fieldCode, $arResult[$fieldCode], $orderId, $arResult["PERSON_TYPE_ID"]))
                    {
                        throw new Exception("Ошибка. Свойство ".$fieldCode." к заказу не добавлено.");
                    }
                }
            }
        }

        AddOrderProperty("MS_ID", "", $orderId, $arResult["PERSON_TYPE_ID"]);
        AddOrderProperty("COMMENTS", $arResult["COMMENT"], $orderId, $arResult["PERSON_TYPE_ID"]);
        AddOrderProperty("DELIVERY_SUM", (intval($calcSum) <= 0) ? "Бесплатно" : $optimalCompany.' '.$calcSum.' руб.', $orderId, $arResult["PERSON_TYPE_ID"]);

        if (!($arOrder = CSaleOrder::GetByID($orderId)))
        {
           throw new Exception("Ошибка. Заказ не найден.");
        }

        foreach (GetModuleEvents("main", "OnOrderComponentFormed", true) as $arEvent)
        {
            ExecuteModuleEventEx($arEvent, array($arOrder, $userPhone));
        }

        $arResult["ORDER"] = $arOrder;
		
		
		#region mail message
			$strOrderList = $tableProducts = "";
			$dbBasketItems = CSaleBasket::GetList(
					array("ID" => "ASC"),
					array("ORDER_ID" => $orderId),
					false,
					false,
					array("ID", "NAME", "QUANTITY")
				);
			while ($arBasketItems = $dbBasketItems->Fetch())
			{
				$strOrderList .= $arBasketItems["NAME"]." - ".$arBasketItems["QUANTITY"]." ".GetMessage("SALE_QUANTITY_UNIT");
				$strOrderList .= "\n";
			}
			
			$tableProducts .= '
				<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;" width="100%">
					<thead>
						<tr>
							<td align="center">#</td>
							<td align="center">Наименование</td>
							<td align="center">Цена</td>
							<td align="center">Количество</td>
							<td align="center">Итого</td>
						</tr>
					</thead>
					<tbody>';
			$index = 0;
			foreach($arCartItems as $arProduct)
			{
				$index++;
				$tableProducts .= '
					<tr>
						<td align="center">'.$index.'</td>
						<td align="left">'.$arProduct["NAME"].'</td>
						<td align="right">'.number_format($arProduct["PRICE"], 2, '.', '').'</td>
						<td align="center">'.$arProduct["QUANTITY"].'</td>
						<td align="right">'.number_format($arProduct["PRICE"] * $arProduct["QUANTITY"], 2, '.', '').'</td>
						
					</tr>
					';
			}
			$tableProducts .= '</tbody></table>';
			
			$arDeliv = CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]);
			$delivery_name = "";
			if ($arDeliv)
			{
				$delivery_name = $arDeliv["NAME"];
			}
			
			$arFields = Array(
				"ORDER_ID" => $orderId,
				"ORDER_DATE" => Date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT", SITE_ID))),
				"ORDER_USER" => $USER->GetFormattedName(false),
				"PRICE" => SaleFormatCurrency($orderPrice, CSaleLang::GetLangCurrency(SITE_ID)),
				"BCC" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
				"EMAIL" => $arResult["EMAIL"],
				"ORDER_LIST" => $strOrderList,
				"ORDER_TABLE" => $tableProducts,
				"ORDER_COUNT" => count($arCartItems),
				"DELIVERY_NAME" => $delivery_name,
				"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME)
			);
			
			$eventName = "SALE_NEW_ORDER";
			
			$bSend = true;
			foreach(GetModuleEvents("sale", "OnOrderNewSendEmail", true) as $arEvent)
				if (ExecuteModuleEventEx($arEvent, Array($arResult["ORDER_ID"], &$eventName, &$arFields))===false)
					$bSend = false;

			if($bSend)
			{
				$event = new CEvent;
				$event->Send($eventName, SITE_ID, $arFields, "N");
			}

			CSaleMobileOrderPush::send("ORDER_CREATED", array("ORDER_ID" => $arFields["ORDER_ID"]));
			
			// Enable order notification
			\Bitrix\Sale\Notify::setNotifyDisable(false);
		#endregion
		
		
        LocalRedirect($APPLICATION->GetCurPage()."?ORDER_ID=".$arOrder["ACCOUNT_NUMBER"]);
    }
    catch (Exception $e)
    {
        LocalRedirect($arParams["CART_PAGE"]."?ERROR=".$e->getMessage());
    }
    die(false);
}

if(empty($_REQUEST["ORDER_ID"]) && !intval($_REQUEST["ORDER_ID"]))
{
    LocalRedirect($arParams["CART_PAGE"]);
}
else
{
    $rsOrders = CSaleOrder::GetList(
        array(),
        array(
            //"USER_ID" => $USER->GetID(),
            "ACCOUNT_NUMBER" => intval($_REQUEST["ORDER_ID"])
        )
    );

    if ($arOrder = $rsOrders->Fetch())
    {
        $arResult["ORDER"] = $arOrder;
        $arResult["PAY_SYSTEM"] = CSalePaySystem::GetByID($arResult["ORDER"]["PAY_SYSTEM_ID"], $arResult["ORDER"]["PERSON_TYPE_ID"]);



        if ($arResult["PAY_SYSTEM"]["PSA_HAVE_RESULT_RECEIVE"] && $arResult["ORDER"]["PAYED"] == "N")
        {
            $arResult["IS_AUTO_REDIRECT"] = ($_COOKIE["PP_IS_REDIRECT_PAY_".$arOrder["ACCOUNT_NUMBER"]] != "Y")? "Y": "N";
            $arResult["IS_PAY_CALL"] = ($_COOKIE["PP_IS_PAY_CALL_".$arOrder["ACCOUNT_NUMBER"]] != "Y")? "N": "Y";

            setcookie("PP_ORDER_ID", $arOrder["ACCOUNT_NUMBER"], time() + 3600, "/");
            setcookie("PP_IS_REDIRECT_PAY_".$arOrder["ACCOUNT_NUMBER"], "Y", time() + 3600, "/");

            if ($arResult["IS_PAY_CALL"] != "Y")
            {
                setcookie("PP_IS_REDIRECT_ORDER_".$arOrder["ACCOUNT_NUMBER"], "N", time() + 3600, "/");
            }

            if ($_REQUEST["IS_AUTO_REDIRECT"] == "Y")
            {
                setcookie("PP_IS_REDIRECT_ORDER_".$_COOKIE["PP_ORDER_ID"], "Y", time() + 3600, "/");
                setcookie("PP_IS_PAY_CALL_".$_COOKIE["PP_ORDER_ID"], "Y", time() + 3600, "/");
                LocalRedirect("/order/?ORDER_ID=".$arResult["ORDER"]["ACCOUNT_NUMBER"], false);
            }
        }

/*        if ($arResult["PAY_SYSTEM"]["PSA_HAVE_RESULT_RECEIVE"] == "Y" && $arResult["ORDER"]["PAYED"] == "N")
        {
            $APPLICATION->SetTitle("Заказ оформлен, но не оплачен");
        }
        else
        {
            $APPLICATION->SetTitle("Заказ оформлен");
        }*/

        $APPLICATION->SetTitle("Заказ оформлен");

        $this->IncludeComponentTemplate();
    }
    else
    {
        ShowError("Ошибка! Заказ не найден.");

    }
}
?>
