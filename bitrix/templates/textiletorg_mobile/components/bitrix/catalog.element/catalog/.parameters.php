<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters["PRICE_CODE"] = array(
    "PARENT" => "PRICES",
    "NAME" => "Коды цен",
    "TYPE" => "STRING",
    "SORT" => 100
);

$arTemplateParameters["GEO_REGION_CITY_NAME"] = array(
    "PARENT" => "PRICES",
    "NAME" => "Регион для отображения цен",
    "TYPE" => "STRING",
    "SORT" => 800
);

$arTemplateParameters["REGION_PRICE_CODE_DEFAULT"] = array(
    "PARENT" => "PRICES",
    "NAME" => "Регион для отображения цен по умолчанию",
    "TYPE" => "STRING",
    "SORT" => 900
);

$arTemplateParameters["CITY_SHOPS"] = array(
    "PARENT" => "VISUAL",
    "NAME" => "Магазины в городах",
    "TYPE" => "STRING",
    "SORT" => 100
);

$arTemplateParameters['HELP_SECTION_CODE'] = array(
    'PARENT' => 'VISUAL',
    'NAME' => 'Код раздела для подсказок',
    'TYPE' => 'STRING',
    'DEFAULT' => '',
);
?>