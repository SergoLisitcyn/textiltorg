<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$sections = array();

foreach ($arResult["ITEMS"] as $arItem)
    $sections[] = $arItem["IBLOCK_SECTION_ID"];

$rsSectctions = CIBlockSection::GetList(
    array(
        "sort" => "asc",
        "NAME" => "desc",

    ),
    array(
        "ID" => array_unique($sections)
    )
);

while ($arSection = $rsSectctions->GetNext())
{
    foreach ($arResult["ITEMS"] as $arItem)
        if ($arItem["IBLOCK_SECTION_ID"] == $arSection["ID"]) {
            $arItem["FILE_PATH"] =  CFile::GetPath($arItem["PROPERTIES"]["FILE"]["VALUE"]);
            $arSection["ITEMS"][] = $arItem;
        }

    $arResult["SECTIONS"][] = $arSection;
}

// echo "<pre>";
// var_dump($arResult["SECTIONS"]);
?>