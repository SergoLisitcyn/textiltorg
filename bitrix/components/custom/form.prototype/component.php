<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<?
$this->setFrameMode(true);
require_once(realpath(dirname(__FILE__))."/functions.php");

if (!CModule::IncludeModule("form"))
{
    ShowError("Модуль форм не подключен");
    return;
}

if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 36000000;

$arResult["FORM_ID"] = intval($arParams["FORM_ID"]);
$arResult["FORM_ACTION"] = trim($arParams["FORM_ACTION"]);
$arResult["START_RESULT_ID"] = intval($arParams["START_RESULT_ID"]);
$arResult["YANDEX_COUNER"] = (!empty($arParams["YANDEX_COUNER"]))? $arParams["YANDEX_COUNER"] : "callMe_Send";
$arResult["TAG_MANAGER"] = (!empty($arParams["TAG_MANAGER"]))? $arParams["TAG_MANAGER"] : "callback28seconds";

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

if ($_REQUEST["FORM_ACTION"] == $arResult["FORM_ACTION"] && check_bitrix_sessid())
{
    $APPLICATION->RestartBuffer();
    header("Content-Type: application/json");

    foreach ($arParams["FIELDS"] as $arField)
    {
        $arResult["FIELDS"][$arField] = $_REQUEST[$arField];
    }

    foreach ($arParams["ORDER"] as $code => $arField)
    {
        $arResult["FIELDS_ORDER"][$arField] = $_REQUEST[$code];
    }

    try
    {
        // add result form module
        if (is_array($arResult["FIELDS"]))
        {
            if ($RESULT_ID = CFormResult::Add($arResult["FORM_ID"], $arResult["FIELDS"]))
            {
                CFormCRM::onResultAdded($arParams["FORM_ID"], $RESULT_ID);
                CFormResult::SetEvent($RESULT_ID);
                CFormResult::Mail($RESULT_ID);

                if ($arResult["START_RESULT_ID"])
                    $RESULT_ID += $arResult["START_RESULT_ID"];

                $arResult["SUCCESS_MESSAGE"] = str_replace("#RESULT_ID#", $RESULT_ID, $arResult["SUCCESS_MESSAGE"]);
                $arResult["~SUCCESS_MESSAGE"] = str_replace("#RESULT_ID#", $RESULT_ID, $arResult["~SUCCESS_MESSAGE"]);
				$send_google = true;
                echo json_encode(array(
                    "status" => "success",
                    "message" => $arResult["~SUCCESS_MESSAGE"]
                ));
            }
            else
            {
                global $strError;
                throw new Exception($strError);
            }
        }
        else
            throw new Exception("При отправке данных возникла ошибка, просьба сообщить об этом по электронной почте, указанной в контактах. Не указаны поля для заполнения.");

        // add order
        if ($arResult["FIELDS_ORDER"])
        {
            if (SITE_ID == "tp") {
                /* Создаем заказ */
                if (!$USER->IsAuthorized())
                {
                    $arResult["LOGIN"] = "anonymous_".RandString(8);
                    $arResult["PASSWORD"] = RandString(8);
                    $arResult["EMAIL"] = $arResult["LOGIN"]."@example.ru";

                    $obUser = new CUser;

                    $arFields = array(
                        "NAME"              => $arResult["FIELDS_ORDER"]["NAME"],
                        "EMAIL"             => $arResult["EMAIL"],
                        "LOGIN"             => $arResult["LOGIN"],
                        "LID"               => SITE_ID,
                        "ACTIVE"            => "Y",
                        "GROUP_ID"          => array(3, 4),
                        "PASSWORD"          => $arResult["PASSWORD"],
                        "CONFIRM_PASSWORD"  => $arResult["PASSWORD"],
                        "PERSONAL_PHONE"    => $arResult["FIELDS_ORDER"]["PHONE"],
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

                $isMobile = IntVal($arResult["FIELDS_ORDER"]["IS_MOBILE"]);
                $isMobile = ($isMobile == 1) ? " (мобильная версия) " : " (основная версия) ";

                $isOrderRepeat = OrderRepeat::isOrderRepeat($arResult["FIELDS_ORDER"]["PHONE"]);

                // add order
                $arFields = array(
                    "LID" => SITE_ID,
                    "PERSON_TYPE_ID" => 1,
                    "PAYED" => "N",
                    "CANCELED" => "N",
                    "STATUS_ID" => ($isOrderRepeat) ? "D" : "N",
                    "PRICE" => 0,
                    "CURRENCY" => "RUB",
                    "USER_ID" => $userId,
                    "USER_DESCRIPTION" => "Обратный звонок".$isMobile,
                    "PRICE_DELIVERY" => 0.000001,
                );

                $orderId = CSaleOrder::Add($arFields);

                if ($orderId)
                {
                    // add info
                    AddOrderProperty("CITY", $_SESSION["GEO_REGION_CITY_ID"], $orderId);
                    AddOrderProperty("NAME", $arResult["FIELDS_ORDER"]["NAME"], $orderId);
                    AddOrderProperty("COMMENTS", "Обратный звонок".$isMobile, $orderId);
                    AddOrderProperty("PHONE", $arResult["FIELDS_ORDER"]["PHONE"], $orderId);
                    AddOrderProperty("MS_ID", "", $orderId);

                    $arResult["TAG_MANAGER"] = "order1click";
                }
                else
                {
                    global $strError;
                    throw new Exception("Ошибка. Не удалось создать заказ в один клик. ".$strError);
                }
            } else {
                saveLog($arResult);
                $phone = getCallBackPhone($arResult["FIELDS_ORDER"]["PHONE"]);
                if ($phone) {
                    sendCallBackPhone($phone);
                }
            }

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