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
$arResult["ELEMENT_ID"] = intval($arParams["ELEMENT_ID"]);
$arResult["SUCCESS_MESSAGE"] = $arParams["SUCCESS_MESSAGE"];
$arResult["~SUCCESS_MESSAGE"] = htmlspecialchars_decode($arParams["SUCCESS_MESSAGE"]);

$arResult["ITEMS"] = array();

$arFilter = Array("IBLOCK_ID" => $arResult["IBLOCK_ID"], "ACTIVE"=>"Y", "PROPERTY_ELEMENT" => $arResult["ELEMENT_ID"]);
$arSelect = Array("ID", "NAME", "PREVIEW_TEXT", "PROPERTY_NAME", "PROPERTY_DATE");

$res = CIBlockElement::GetList(array("PROPERTY_DATE" => "DESC", "ID" => "DESC"), $arFilter, false, false, $arSelect);
while($ob = $res->GetNext())
{
    $arResult["ITEMS"][] = array(
        "NAME" => $ob["PROPERTY_NAME_VALUE"],
        "DATE" => $ob["PROPERTY_DATE_VALUE"],
        "QUESTION" => $ob["PREVIEW_TEXT"]
    );
}

$arResult["COUNT"] = count($arResult["ITEMS"]);

$this->IncludeComponentTemplate();
?>