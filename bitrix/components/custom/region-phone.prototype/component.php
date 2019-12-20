<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<?
if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 36000000;

$arResult["DEFAULT_FILE"] = trim($arParams["DEFAULT_FILE"]);
$arResult["FILES_PATH"] = trim($arParams["FILES_PATH"]);
$transliteCity = Cutil::translit(
    $_SESSION["GEO_REGION_CITY_NAME"],
    "ru",
    array("replace_space" => "-", "replace_other" => "-")
);
//echo "<b style='display:none;'>{$transliteCity}</b>";
$arResult["FILE_NAME"] = trim($transliteCity, "-");
$arResult["FILE_EXISTS"] = file_exists($_SERVER["DOCUMENT_ROOT"].$arResult["FILES_PATH"].$arResult["FILE_NAME"].".php");

$this->IncludeComponentTemplate();
?>