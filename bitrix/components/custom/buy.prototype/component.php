<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<?
require_once(realpath(dirname(__FILE__))."/functions.php");

global $USER;

if (!CModule::IncludeModule("catalog"))
{
    ShowError("Модуль catalog не подключен");
    return;
}

if (!CModule::IncludeModule("iblock"))
{
    ShowError("Модуль iblock не подключен");
    return;
}

if (!CModule::IncludeModule("sale"))
{
    ShowError("Модуль sale не подключен");
    return;
}

if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 36000000;

$arResult["ACTION"] = $arParams["ACTION"];

if (is_array($arParams["SUCCESS_MESSAGE"]) && !empty($arParams["SUCCESS_MESSAGE"]["FILE"]))
{
    $tFile = $_SERVER["DOCUMENT_ROOT"]."/".$arParams["SUCCESS_MESSAGE"]["FILE"];
    $tFile = preg_replace("/\/{,2}/", "", $tFile);

    $tMessage = (file_exists($tFile)) ? file_get_contents($tFile) : "Файл шаблона для сообщения не найден";

    $arResult["SUCCESS_MESSAGE"] = $tMessage;
    $arResult["~SUCCESS_MESSAGE"] = htmlspecialchars_decode($tMessage);
}
else
{
    $arResult["SUCCESS_MESSAGE"] = $arParams["SUCCESS_MESSAGE"];
    $arResult["~SUCCESS_MESSAGE"] = htmlspecialchars_decode($arParams["SUCCESS_MESSAGE"]);
}

if ($_REQUEST["ACTION"] == $arResult["ACTION"] && check_bitrix_sessid())
{
    $APPLICATION->RestartBuffer();
    header("Content-Type: application/json");

    try
    {
        $arResult["ORDER"]["GOOD"]["ID"] = IntVal($_REQUEST["GOOD_ID"]);
        $arResult["ORDER"]["NAME"] = trim($_REQUEST["NAME"]);
        $arResult["ORDER"]["PHONE"] = trim($_REQUEST["PHONE"]);
        $arResult["ORDER"]["CITY"] = $_SESSION["GEO_REGION_CITY_ID"];

        $isMobile = IntVal($_REQUEST["IS_MOBILE"]);
        $isMobile = ($isMobile == 1) ? " (мобильная версия) " : " (основная версия) ";

        $rsElements = CIBlockElement::GetList(
            array(),
            array(
                "ID" => $arResult["ORDER"]["GOOD"]["ID"]
            ),
            false,
            false,
            array("*", "XML_ID", "CATALOG_GROUP_".$_SESSION["PRICE_ID"])
        );

        if ($arElement = $rsElements->GetNext())
        {
            if(CCatalogSku::IsExistOffers($arElement["ID"]))
            {
                $arOffers = CIBlockPriceTools::GetOffersArray(
                    array(
                        'IBLOCK_ID' => $arElement['IBLOCK_ID'],
                        'HIDE_NOT_AVAILABLE' => 'Y',
                        'CHECK_PERMISSIONS' => 'Y'
                    ),
                    array($arElement['ID'])
                );

                $arOffersId = array();
                foreach($arOffers as $arOffer)
                {
                    $arOffersId[] = $arOffer['ID'];
                }

                $rsOfferElements = CIBlockElement::GetList(
                    array(),
                    array(
                        'ID' => $arOffersId
                    ),
                    false,
                    array(),
                    array("*", "CATALOG_GROUP_".$arResult["PRICE_ID"], "CATALOG_QUANTITY")
                );

                $minPrice = 0;
                while($arOfferElement = $rsOfferElements->GetNext())
                {
                    $price = $arOfferElement["CATALOG_PRICE_".$_SESSION["PRICE_ID"]];
                    if (empty($minPrice) || $price < $minPrice)
                    {
                        $minPrice = $price;
                    }
                }

                $price = $minPrice;
            }
            else
            {
                $price = $arElement["CATALOG_PRICE_".$_SESSION["PRICE_ID"]];
            }

            $currency = $arElement["CATALOG_CURRENCY_".$_SESSION["PRICE_ID"]];

            $arFieldsItem = array(
                "PRODUCT_ID" => $arElement["ID"],
                "PRICE" => $price,
                "CURRENCY" => $currency,
                "QUANTITY" => 1,
                "LID" => SITE_ID,
                "NAME" => $arElement["NAME"],
                "PRODUCT_XML_ID" => $arElement["XML_ID"],
                "DETAIL_PAGE_URL" => $arElement["DETAIL_PAGE_URL"]
            );
        }

        if (!$USER->IsAuthorized())
        {
            $arResult["LOGIN"] = "anonymous_".RandString(8);
            $arResult["PASSWORD"] = RandString(8);
            $arResult["EMAIL"] = $arResult["LOGIN"]."@example.ru";

            $obUser = new CUser;

            $arFields = array(
              "NAME"              => $arResult["ORDER"]["NAME"],
              "EMAIL"             => $arResult["EMAIL"],
              "LOGIN"             => $arResult["LOGIN"],
              "LID"               => SITE_ID,
              "ACTIVE"            => "Y",
              "GROUP_ID"          => array(3, 4),
              "PASSWORD"          => $arResult["PASSWORD"],
              "CONFIRM_PASSWORD"  => $arResult["PASSWORD"],
              "PERSONAL_PHONE"    => $arResult["ORDER"]["PHONE"],
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

        $isOrderRepeat = OrderRepeat::isOrderRepeat($arResult["ORDER"]["PHONE"]);

        // add order
        $arFields = array(
           "LID" => SITE_ID,
           "PERSON_TYPE_ID" => 1,
           "PAYED" => "N",
           "CANCELED" => "N",
           "STATUS_ID" => ($isOrderRepeat) ? "D" : "N",
           "PRICE" => $price,
           "CURRENCY" => $currency,
           "USER_ID" => $userId,
           "USER_DESCRIPTION" => "Покупка через форму «Купить в один клик»".$isMobile,
           "PRICE_DELIVERY" => 0.000001,
        );

        $orderId = CSaleOrder::Add($arFields);

        if ($orderId)
        {
            // add good
            $arFieldsItem["ORDER_ID"] = $orderId;

            CSaleBasket::Add($arFieldsItem);
            CSaleBasket::OrderBasket($orderId, $userId, SITE_ID);

            // add info
            AddOrderProperty("CITY", $arResult["ORDER"]["CITY"], $orderId);
            AddOrderProperty("NAME", $arResult["ORDER"]["NAME"], $orderId);
            AddOrderProperty("COMMENTS", "Покупка через форму «Купить в один клик»".$isMobile, $orderId);
            AddOrderProperty("PHONE", $arResult["ORDER"]["PHONE"], $orderId);
            AddOrderProperty("MS_ID", "", $orderId);

            $arOrder = CSaleOrder::GetByID($orderId);

            $arResult["SUCCESS_MESSAGE"] = str_replace("#RESULT_ID#", $arOrder["ACCOUNT_NUMBER"], $arResult["SUCCESS_MESSAGE"]);
            $arResult["~SUCCESS_MESSAGE"] = str_replace("#RESULT_ID#", $arOrder["ACCOUNT_NUMBER"], $arResult["~SUCCESS_MESSAGE"]);

            echo json_encode(array(
                "status" => "success",
                "message" => $arResult["~SUCCESS_MESSAGE"],
				"transaction" => $arOrder["ACCOUNT_NUMBER"],
                "id" => $arFieldsItem["PRODUCT_ID"],
                "qnt" => $arFieldsItem["QUANTITY"],
                "price" => $arFieldsItem["PRICE"]
            ));
        }
        else
        {
            global $strError;
            global $USER;
            if ($USER->IsAdmin()):
                throw new Exception("Ошибка. Не удалось создать заказ в один клик. ".$strError);
            else:
                throw new Exception("Ошибка. Не удалось создать заказ в один клик. ".$strError);
            endif;

        }
    }
    catch (Exception $e)
    {
        echo json_encode(array(
            "status" => "error",
            "message" => $e->getMessage()
        ));
    }
    die(false);
}

$this->IncludeComponentTemplate();
?>
