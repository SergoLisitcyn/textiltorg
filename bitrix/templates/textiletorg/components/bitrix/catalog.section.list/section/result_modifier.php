<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arResult["IS_HIDE_SECTION"] = false;

foreach ($arResult["SECTIONS"] as $nSection => $arSection)
{
    if ($arSection["PICTURE"]["SRC"])
        $arResult["IS_PICTURES"] = true;

    $index = $nSection + 1;
    if ($index % 5 == 0)
        $arSection["IS_CLEAR"] = true;

    $arSection["SECTION_PAGE_URL"] = Helper::RemoveOneLavelUrl($arSection["SECTION_PAGE_URL"]);

    if (!empty($arSection["IBLOCK_SECTION_ID"]) && !in_array($arParams["SECTION_ID"], $arParams["SHOW_IN_SECTION"])) {
        $arResult["IS_HIDE_SECTION"] = true;
    }

    $arResult["SECTIONS"][$nSection] = $arSection;
}