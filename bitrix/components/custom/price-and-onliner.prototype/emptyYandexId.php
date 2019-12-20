<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once("functions.php");
// Подключение модуля инфоблоки
// Документация: http://dev.1c-bitrix.ru/api_help/iblock/index.php
CModule::IncludeModule("iblock");

$arFilter = array(
    "IBLOCK_ID" => 8,
    "ACTIVE" => "Y",
    "PROPERTY_YM_ID" => false, // Указан id товара на яндекс маркете
    "SECTION_ID" => array(19,20,23,24,69,83,85,90), // Швейная техника
    "INCLUDE_SUBSECTIONS" => "Y",
    // нет ID ЯМ
    "!ID" => array(91778,91418,91551,88397,88400,87712,3441,86131,229,247,248,314,1852,1853,1854,1855,1856,1857,1901,1902,1903,1904,1905,1906,3722,3723,66888,66890,70911,74298,75361,82518,82519,83056,83152,86171,1609,1610,1611,1612,1613,1614,1615,1621,1648,1657,1658,1659,3260,3270,3324,3338,3374,3442,3443,3458,3460,3591,3789,3792,3797,3802,11241,11375,11442,67144,68237,70798,70844,71892,72057,72080,72081,72868,72870,73481,78158,78159,78160,78161,78162,78165,78166,78167,78168,78177,78178,78195,78196,78197,78212,78213,83069,83932,85057,85058,85059,85060)
);
$arSelect = array("ID", "XML_ID", "IBLOCK_SECTION_ID", "NAME", "PROPERTY_YM_ID");

$rsElements = CIBlockElement::GetList(array("IBLOCK_SECTION_ID" => "ASC"), $arFilter, false, false, $arSelect);

// Выборка значений полей в массив
$arElements = array();
$i = 1;
$emailMessage = null;
while ($arElement = $rsElements->GetNext())
{
    $emailMessage .= $i . " - (ID" . $arElement["ID"] . ") " . $arElement["NAME"] . "\n";
    $i++;
}
if (!empty($emailMessage)) {
    mail_utf8('товаров не обновляются! Не указан "Идентификатор товара на Я.Маркете"', $emailMessage, ",i.bashko@textiletorg.ru");
}
