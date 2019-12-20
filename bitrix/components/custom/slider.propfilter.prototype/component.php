<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<?

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

if (intval($arParams["SECTION_ID"]))
{
	$arResult["SECTION_ID"] = intval($arParams["SECTION_ID"]);
}


if (!empty($arResult["SECTION_ID"]))
{
    $GLOBALS[$arParams["FILTER_NAME"]][] = array(
       "LOGIC" => "OR",
        array("PROPERTY_".$arParams["PROPERTY"] => $arResult["SECTION_ID"]),
        array("=PROPERTY_".$arParams["PROPERTY"] => false)
    );
}
else
{
    $GLOBALS[$arParams["FILTER_NAME"]] = array(
        "PROPERTY_".$arParams["PROPERTY"] => false
    );
}
?>