<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$GLOBALS["REGION_OPTIONS"] = "";
foreach ($arResult["LIST"] as $arItem){
        $GLOBALS["REGION_OPTIONS"] .= ($_SESSION["GEO_REGION_CITY_NAME"] == $arItem["CITY_NAME"]) ?
        '<div data-url="'.$arItem["SET_CITY_URL"].'" class="city selected">'.$arItem["CITY_NAME"].'</div>':
        '<div data-url="'.$arItem["SET_CITY_URL"].'" class="city">'.$arItem["CITY_NAME"].'</div>';
}
?>