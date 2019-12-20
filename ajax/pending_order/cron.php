<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("catalog");
CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");

//Проверяем есть ли в базе заказы которые нужно добавить
$rsPendingOrders = $DB->Query("SELECT * FROM pending_order");
while ($arRow = $rsPendingOrders->Fetch())
{
    if ($arRow["datatime"] < date("Y-m-d G:i:s"))
    {
        $phone = OrderRepeat::preparePhone($arRow["phone"]); // Если телефон валидный
        if ($phone) {
            OrderMake($arRow); //Добавляем в Битрикс
        }
        $DB->Query("DELETE FROM pending_order WHERE id = ".$arRow["id"]); //Удаляем из БД
    }
}

//Оформляем заказ
function OrderMake($arRow = array())
{
    //Пользователь
    if (!intval($arRow["user_id"]))
    {
        $obUser = new CUser;

        $isMobile = ($arRow["is_mobile"] == 1) ? " (мобильная версия) " : " (основная версия) ";

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
          "USER_DESCRIPTION"  => "Автоматический заказ через 5 мин. бездействия пользователя".$isMobile
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
        AddOrderProperty("COMMENTS", "Автоматический заказ через 5 мин. бездействия пользователя".$isMobile, $orderId);
        AddOrderProperty("MS_ID", "", $orderId);
    }

    echo "Order: " . $orderId . "\n";
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