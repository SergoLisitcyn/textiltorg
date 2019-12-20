<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<?
CModule::IncludeModule('iblock');

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arFilter = array(
    "IBLOCK_ID" => intval($arParams["IBLOCK_ID"])
);

if ($arParams["ELEMENT_CODE_VARIABLE"])
	$arFilter["CODE"] = $_REQUEST[$arParams["ELEMENT_CODE_VARIABLE"]];

if ($arParams["ELEMENT_ID"])
	$arFilter["ID"] = $arParams["ELEMENT_ID"];

if (empty($arFilter["CODE"]) && empty($arFilter["ID"]))
    return;

$rsElements = CIBlockElement::GetList(
    array(),
    $arFilter,
    false,
    false,
    array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM","PROPERTY_*", "IBLOCK_SECTION_ID")
);

$IDs = array();

while ($obElement = $rsElements->GetNextElement())
{
    $arFields = $obElement->GetFields();
    $arProps = $obElement->GetProperties();

    if (is_array($arProps[$arParams["PROPERTY_CODE"]]["VALUE"]))
    	$IDs = array_merge($IDs, $arProps[$arParams["PROPERTY_CODE"]]["VALUE"]);
}

$IDs = array_unique($IDs);

if ($IDs) {
    $arFilterMoreGoodsSidebar = array(
        "ID" => $IDs
    );
} else {
    $arDefaultGoods = array();
    if ($arParams["PROPERTY_CODE"] == "MORE_GOODS") {
        $arDefaultGoodsFull = array(
            19 => array(904,3438,3306,1071,3454,3457),
        );
        $nav = CIBlockSection::GetNavChain(false,$arFields["IBLOCK_SECTION_ID"], array("ID", "IBLOCK_SECTION_ID", "NAME"));
        while($arSectionPath = $nav->ExtractFields()){
            $sectionId = ($arSectionPath["ID"]) ? $arSectionPath["ID"] : $arSectionPath["IBLOCK_SECTION_ID"];
            if (array_key_exists($sectionId, $arDefaultGoodsFull)) {
                $arDefaultGoods = $arDefaultGoodsFull[$sectionId];
                break;
            }
        }
    }
    $arFilterMoreGoodsSidebar = array(
        "ID" => $arDefaultGoods
    );
}

$GLOBALS[$arParams["FILTER_NAME"]] = $arFilterMoreGoodsSidebar;

$this->IncludeComponentTemplate();
?>