<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($_REQUEST["DELETE_FROM_COMPARE_LIST_ALL"] == "Y")
{
    $APPLICATION->RestartBuffer();
    unset($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]);
    die;
}