<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<?
if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 36000000;

$arResult["CURRENT_CITY"] = $_SESSION["GEO_REGION_CITY_NAME"];

$arPropCity = array();
$propertyEnums = CIBlockPropertyEnum::GetList(Array("SORT" => "ASC"), Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "CODE" => "CITY"));
while($enumFields = $propertyEnums->GetNext())
{
    $arPropCity[] = $enumFields["VALUE"];
}

$arResult["ITEMS"] = array();
$arFilter = array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE"=>"Y");
if (in_array($arResult["CURRENT_CITY"], $arPropCity))
{
    $arFilter["PROPERTY_CITY_VALUE"] = $arResult["CURRENT_CITY"];
}

$res = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter, false, false);
while($ob = $res->GetNextElement())
{
    $arFields = $ob->GetFields();
    $arProps = $ob->GetProperties();
    
    $arResult["ITEMS"][] = array_merge($arFields, $arProps);
}

// Ресайзим фотографии магазина
$arPhoto = array();
foreach($arResult["ITEMS"] as $key => $arItem)
{
    if (!empty($arItem["PHOTO"]["VALUE"])) {
        foreach ($arItem["PHOTO"]["VALUE"] as $idPhoto) {
            $file = CFile::ResizeImageGet($idPhoto, array('width'=>590, 'height'=> 450), BX_RESIZE_IMAGE_EXACT);
            $arPhoto[] = array(
                "SMALL" => $file['src'],
                "BIG" => CFile::GetPath($idPhoto)
            );
        }
    }
}
$arResult["PHOTOGALLERY"] = $arPhoto;

$this->IncludeComponentTemplate();
?>