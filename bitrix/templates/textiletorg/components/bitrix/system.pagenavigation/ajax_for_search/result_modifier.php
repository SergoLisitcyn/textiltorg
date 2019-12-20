<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult["NavQueryString"] = str_replace("AJAX_PAGEN=Y&amp;", "", $arResult["NavQueryString"]);
$arResult["NavQueryString"] = str_replace("&amp;AJAX_PAGEN=Y", "", $arResult["NavQueryString"]);
$arResult["NavQueryString"] = str_replace("AJAX_PAGEN=Y", "", $arResult["NavQueryString"]);
$arResult["NavQueryString"] = preg_replace('/\SECTION_CODE_PATH=[^\&]+(&)?(amp;)?/', '', $arResult["NavQueryString"]);
$arResult["NavQueryString"] = preg_replace('/\SMART_FILTER_PATH=[^\&]+(&)?(amp;)?/', '', $arResult["NavQueryString"]);