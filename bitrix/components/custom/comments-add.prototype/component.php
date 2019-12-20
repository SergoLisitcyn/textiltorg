<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<?
if (!CModule::IncludeModule("iblock"))
{
    ShowError("Модуль iblock не подключен");
    return;
}

if (!CModule::IncludeModule("highloadblock"))
{
    ShowError("Модуль highloadblock не подключен");
    return;
}

use Bitrix\Highloadblock as HL;

if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 36000000;

$arResult["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
$arResult["SUCCESS_MESSAGE"] = $arParams["SUCCESS_MESSAGE"];
$arResult["~SUCCESS_MESSAGE"] = htmlspecialchars_decode($arParams["SUCCESS_MESSAGE"]);

if ($_REQUEST["AJAX_COMMENTS"] == "Y" && check_bitrix_sessid())
{
    $APPLICATION->RestartBuffer();
    header("Content-Type: application/json");

    $arResult["NAME"] = trim($_REQUEST["NAME"]);
    $arResult["PHONE"] = trim($_REQUEST["PHONE"]);
    $arResult["QUESTION"] = trim($_REQUEST["QUESTION"]);
    $arResult["RATING"] = $_REQUEST["RATING"];
    $arResult["ELEMENT_ID"] = intval($_REQUEST["ELEMENT_ID"]);

    try
    {
        // Получим название элемента
        $arFilter = Array("ID" => $arResult["ELEMENT_ID"]);
        $res = CIBlockElement::GetList(array(), $arFilter, false);
        if ($ob = $res->Fetch())
        {
            $name = $ob["NAME"];
        }
        
        $el = new CIBlockElement;
        $arProp = array();
        $arProp["ELEMENT"] = $arResult["ELEMENT_ID"];
        $arProp["DATE"] = ConvertTimeStamp();
        $arProp["NAME"] = $arResult["NAME"];
        $arProp["PHONE"] = $arResult["PHONE"];
        if ($arResult["RATING"])
        {
            $arProp["RATING"] = $arResult["RATING"];
        }
                
        $arLoadProductArray = Array(
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => $arResult["IBLOCK_ID"],
            "PROPERTY_VALUES" => $arProp,
            "NAME" => $name,
            "ACTIVE" => "N",
            "PREVIEW_TEXT" => $arResult["QUESTION"]
        );

        if($newId = $el->Add($arLoadProductArray))
        {
            $arResult["SUCCESS_MESSAGE"] = str_replace("#RESULT_ID#", $newId, $arResult["SUCCESS_MESSAGE"]);
            $arResult["~SUCCESS_MESSAGE"] = str_replace("#RESULT_ID#", $newId, $arResult["~SUCCESS_MESSAGE"]);

            echo json_encode(array(
                "status" => "success",
                "message" => $arResult["~SUCCESS_MESSAGE"]
            ));
        }
        else
        {
            throw new Exception('Ошибка добавления комментария');
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
?>