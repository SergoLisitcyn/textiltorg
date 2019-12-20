<?php

global $USER;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");
CModule::IncludeModule("iblock");

$date = date_create(date('Y-m-d G:i:s'));

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
$comment = strip_tags(trim($_POST['comment']));

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
    "name" => $sName,
    "email" => $sEmail,
    "phone" => $sPhone,
    "datatime" => $sDatatime,
    "sity" => $sSity,
    "sum" => $sSum,
    "session" => $session,
    "user_id" => $userId,
    "sale_user" => serialize($arCartItems),
    "site_id" => SITE_ID,
    "pay_system_id" => $payment,
    "delivery_id" => $delivery,
    "comment" => $comment
);

// Создание заказа
$orderId = OrderMake($arFields);
echo $orderId;

//Оформляем заказ
function OrderMake($arRow = array())
{
    //Пользователь
    if (!intval($arRow["user_id"]))
    {
        $obUser = new CUser;

        $userLogin = "anonymous_".RandString(8);
        $userPass = RandString(8);
        $userEmail = $userLogin."@example.ru";

        $arFields = array(
            "NAME"              => $arRow["name"],
            "EMAIL"             => $userEmail,
            "LOGIN"             => $userLogin,
            "LID"               => $arRow["site_id"],
            "ACTIVE"            => "Y",
            "GROUP_ID"          => array(3, 4),
            "PASSWORD"          => $userPass,
            "CONFIRM_PASSWORD"  => $userPass,
            "PERSONAL_PHONE"    => $arRow["phone"],
            "USER_DESCRIPTION"  => $arRow["comment"]
        );

        $userId = $obUser->Add($arFields);

        if (!intval($userId))
        {
            return false;
        }
    }
    else
    {
        $userId = $arRow["user_id"];
    }

    $isOrderRepeat = OrderRepeat::isOrderRepeat($arRow["phone"]);

    //Создаем заказ
    $orderId = CSaleOrder::Add(
        array(
            "LID" => $arRow["site_id"],
            "PERSON_TYPE_ID" => 1,
            "PAYED" => "N",
            "CANCELED" => "N",
            "STATUS_ID" => ($isOrderRepeat) ? "D" : "N",
            "PRICE" => $arRow["sum"],
            "CURRENCY" => ($arRow["site_id"] == "by")? "BYN" : "RUB",
            "USER_ID" => $userId,
            "PAY_SYSTEM_ID" => $arRow["pay_system_id"],
            "DELIVERY_ID" => $arRow["delivery_id"],
            "ALLOW_DELIVERY" => "N",
            "PRICE_DELIVERY" => 0.000001,
            "TAX_VALUE" => 0.0
        )
    );

    if ($orderId)
    {
        //Связываем товары и корзину
        $arCartItems = unserialize($arRow['sale_user']);

        foreach ($arCartItems as $nItem => $arItem)
        {
            $arItem['ORDER_ID'] = $orderId;
            CSaleBasket::Add($arItem);
        }

        CSaleBasket::OrderBasket($orderId, $userId, SITE_ID);

        //Заполняем свойства заказа (одни из N)
        AddOrderProperty("CITY", $arRow['sity'], $orderId);
        AddOrderProperty("PHONE", $arRow['phone'], $orderId);
        AddOrderProperty("NAME", $arRow['name'], $orderId);
        AddOrderProperty("EMAIL", $arRow['email'], $orderId);
        AddOrderProperty("COMMENTS", $arRow["comment"], $orderId);
        AddOrderProperty("MS_ID", "", $orderId);
    }

    return $orderId;
}

function AddOrderProperty($code, $value, $order)
{
    if (!strlen($code))
    {
        return false;
    }

    if (CModule::IncludeModule("sale"))
    {
        if ($arProp = CSaleOrderProps::GetList(array(), array("CODE" => $code))->Fetch())
        {
            return CSaleOrderPropsValue::Add(array(
                "NAME" => $arProp["NAME"],
                "CODE" => $arProp["CODE"],
                "ORDER_PROPS_ID" => $arProp["ID"],
                "ORDER_ID" => $order,
                "VALUE" => $value,
            ));
        }
    }
}