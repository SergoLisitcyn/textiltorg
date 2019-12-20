<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
$tmp = array();
$arParent = array();



//$arTarget = array(30, 251, 264, 268);
foreach ($arResult["SECTIONS"] as $nSection => $arSection)
{
    $arParent[$arSection["ID"]] = $arSection["NAME"];
}
foreach ($arResult["SECTIONS"] as $idSection => $itemSection)
{

    if (in_array($itemSection["ID"], $arParams["FILTER_CATEGORY"])) {
        $tmp[$idSection] = $itemSection;
        $tmp[$idSection]["SECTION_PAGE_URL"] = Helper::RemoveOneLavelUrl($itemSection["SECTION_PAGE_URL"]);
        $tmp[$idSection]["NAME"] = $arParent[$itemSection["IBLOCK_SECTION_ID"]] . " " . $itemSection["NAME"];
        $tmp[$idSection]["RESIZE_PICTURE"] = Helper::Resize(array($itemSection["PICTURE"]["ID"]), 290, 200, true);
    }
}
$arResult["SECTIONS"] = $tmp;
?>