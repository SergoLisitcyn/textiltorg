<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!function_exists('AddOrderProperty'))
{
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
}

function getCallBackPhone($phone) {
    $result = null;
    $phoneCods = 8;
    $phoneNumberCount = 11;
    $replace = array("+","-","(",")"," ");

    $phone = str_replace($replace, "", $phone);
    $count = strlen($phone);

    // Если номер из 11 цифр и код 8
    if ($count == $phoneNumberCount) {
        $phone = preg_replace("/^7/", $phoneCods, $phone);
        if ($phone[0] == $phoneCods) {
            $result = $phone;
        }
    }
    return $result;
}

function sendCallBackPhone($phone){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://voip.textiletorg.ru/backcall.php?tel=".$phone);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
}

function saveLog($arResult) {
    $isMobile = IntVal($arResult["FIELDS_ORDER"]["IS_MOBILE"]);
    $isMobile = ($isMobile == 1) ? " (мобильная версия) " : " (основная версия) ";

    $message = $arResult["FIELDS_ORDER"]["PHONE"] ." | ". date("H:i:s") ." | ". $arResult["FIELDS_ORDER"]["NAME"] . $isMobile . SITE_ID;
    error_log($message . "\n", 3, dirname(__FILE__)."/log/callback/" . date("d-m-Y") . ".log");
}