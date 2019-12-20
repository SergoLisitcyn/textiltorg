<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<?
if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 36000000;


$this->IncludeComponentTemplate();
?>