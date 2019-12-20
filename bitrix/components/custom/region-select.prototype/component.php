<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<?
if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 36000000;

$arResult["GEO_REGION_CITY_ID"] = $_SESSION["GEO_REGION_CITY_ID"];
$arResult["GEO_REGION_CITY_NAME"] = $_SESSION["GEO_REGION_CITY_NAME"];
$arResult["IS_CONFIRMED"] = ($_SESSION["GEO_IS_CONFIRMED"])? $_SESSION["GEO_IS_CONFIRMED"] : false;

$this->IncludeComponentTemplate();
?>
