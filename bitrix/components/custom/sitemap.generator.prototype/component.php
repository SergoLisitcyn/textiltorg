<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('CHK_EVENT', true);

@set_time_limit(0);
@ignore_user_abort(true);

CModule::IncludeModule("iblock");

// <editor-fold defaultstate="collapsed" desc="Проверка параметров">

if (is_array($arParams["IBLOCK_ID_FOR_SECTIONS"])) {
    foreach ($arParams["IBLOCK_ID_FOR_SECTIONS"] as $key => $val) {
        $arParams["IBLOCK_ID_FOR_SECTIONS"][$key] = intval($val);
    }
} else {
    $arParams["IBLOCK_ID_FOR_SECTIONS"] = array();
}

if (is_array($arParams["IBLOCK_ID_FOR_ELEMENTS"])) {
    foreach ($arParams["IBLOCK_ID_FOR_ELEMENTS"] as $key => $val) {
        $arParams["IBLOCK_ID_FOR_ELEMENTS"][$key] = intval($val);
    }
} else {
    $arParams["IBLOCK_ID_FOR_ELEMENTS"] = array();
}

if (strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])) {
    $arrFilter = array();
} else {
    $arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
    if(!is_array($arrFilter))
        $arrFilter = array();
}

$arParams["PREFIX"] = trim($arParams["PREFIX"]);
if(strlen($arParams["PREFIX"]) <= 0)
    $arParams["PREFIX"] = 'http://www.textiletorg.ru';

$arParams["FILENAME"] = trim($arParams["FILENAME"]);
if(strlen($arParams["FILENAME"]) <= 0)
    $arParams["FILENAME"] = "sitemap.xml";

$arParams["MODIFIER_PAGE_URL"] = (isset($arParams["MODIFIER_PAGE_URL"]) && $arParams["MODIFIER_PAGE_URL"] === 'N' ? 'N' : 'Y');

// </editor-fold>

$lastmod = date("c");

$arResult["ITEMS"] = array();

$arResult["ITEMS"][] = array(
    "loc" => $arParams["PREFIX"],
    //"lastmod" => $lastmod,
    "changefreq" => "weekly",
    "priority" => "1.0"
);

// <editor-fold defaultstate="collapsed" desc="Статические страницы">

if (count($arParams["PAGES"] > 0)) {
    foreach($arParams["PAGES"] as $item) {
        if (!empty($item)) {
            $arResult["ITEMS"][] = array(
                "loc" => $arParams["PREFIX"].$item,
                //"lastmod" => $lastmod,
                "changefreq" => "weekly",
                "priority" => "0.8"
            );
        }
    }
}

// </editor-fold>

if (count($arParams["IBLOCK_ID_FOR_SECTIONS"]) > 0 && count($arParams["IBLOCK_ID_FOR_ELEMENTS"]) > 0) {

    // <editor-fold defaultstate="collapsed" desc="Получение разделов">

    $arFilter = array("IBLOCK_ID" => array_merge($arParams["IBLOCK_ID_FOR_SECTIONS"], $arParams["IBLOCK_ID_CATALOG"]), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
    $arSelect = array("ID", "IBLOCK_ID", "SECTION_PAGE_URL");

    $res = CIBlockSection::GetList(array("left_margin"=>"asc"), $arFilter, false, $arSelect);
    while($arItem = $res->GetNext()) {
		// Если отмеченно "Модифицировать URL"
        if ($arParams["MODIFIER_PAGE_URL"] && in_array($arItem["IBLOCK_ID"], $arParams["IBLOCK_ID_CATALOG"]))
        {
            $arItem["SECTION_PAGE_URL"] = Helper::RemoveOneLavelUrl($arItem["SECTION_PAGE_URL"]);
        }

        $arResult["ITEMS"][] = array(
			"loc" => $arParams["PREFIX"].$arItem["SECTION_PAGE_URL"],
			//"lastmod" => $lastmod,
            "changefreq" => "weekly",
			"priority" => "0.7"
        );
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Получение элементов каталога">

    $arFilter = array("IBLOCK_ID" => $arParams["IBLOCK_ID_CATALOG"], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
    $arSelect = array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL");

    $res = CIBlockElement::GetList(Array("NAME"=>"ASC"), array_merge($arFilter, $arrFilter), false, false, $arSelect);
    while($arItem = $res->GetNext()) {
        // Если отмеченно "Модифицировать URL"
        if ($arParams["MODIFIER_PAGE_URL"] && in_array($arItem["IBLOCK_ID"], $arParams["IBLOCK_ID_CATALOG"]))
        {
            $arItem["DETAIL_PAGE_URL"] = Helper::RemoveOneLavelUrl($arItem["DETAIL_PAGE_URL"]);
        }

        $arResult["ITEMS"][] = array(
			"loc" => $arParams["PREFIX"].$arItem["DETAIL_PAGE_URL"],
			//"lastmod" => $lastmod,
            "changefreq" => "weekly",
			"priority" => "0.6"
        );
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Получение элементов">

    $arFilter = array("IBLOCK_ID" => $arParams["IBLOCK_ID_FOR_ELEMENTS"], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
    $arSelect = array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL");

    $res = CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, false, false, $arSelect);
    while($arItem = $res->GetNext()) {
        // Если отмеченно "Модифицировать URL"
        if ($arParams["MODIFIER_PAGE_URL"] && in_array($arItem["IBLOCK_ID"], $arParams["IBLOCK_ID_CATALOG"]))
        {
            $arItem["DETAIL_PAGE_URL"] = Helper::RemoveOneLavelUrl($arItem["DETAIL_PAGE_URL"]);
        }

        $arResult["ITEMS"][] = array(
			"loc" => $arParams["PREFIX"].$arItem["DETAIL_PAGE_URL"],
			//"lastmod" => $lastmod,
            "changefreq" => "weekly",
			"priority" => "0.5"
        );
    }

    // </editor-fold>

}

// <editor-fold defaultstate="collapsed" desc="Сохранение в XML">

// Конвертация массива в XML
$arXML = array(
    "urlset" => array(
        "@xmlns:xsi" => "http://www.w3.org/2001/XMLSchema-instance",
        "@xmlns" => "http://www.sitemaps.org/schemas/sitemap/0.9",
        "@xsi:schemaLocation" => "http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd",
        "%" => array(
            "url" => $arResult["ITEMS"]
        )
    )
);
$converter = new ArrayToXML();
$xmlStr = $converter->buildXML($arXML, "");
/*---------------------27.06.17---------------------------*/
if ('Y' === $arParams['DYNAMIC']) {
    $phpStr = '<?php
$arSite = explode(".", $_SERVER["HTTP_HOST"]);
    if ($arSite[0] === "www") {
        $currentDomain = $arSite[1].".";
    } else {
        $currentDomain = $arSite[0].".";
    }
    if ("textiletorg." === $currentDomain) {
        $currentDomain = "www.";
    }
    header("Content-Type: text/xml");
';
    $search = array(
        '<?xml version="1.0" encoding="UTF-8"?>',
        $arParams["PREFIX"]
    );
    $replace = array(
        'echo \'<?xml version="1.0" encoding="UTF-8"?>\';?>',
        'https://<?= $currentDomain?>textiletorg.ru'
    );
    $xmlStr = str_ireplace(
        $search,
        $replace,
        $xmlStr
    );
    $phpStr .= $xmlStr;
    $xmlStr = $phpStr;
}
/*--------------------/27.06.17---------------------------*/
// Запись в файл
$f = fopen($_SERVER["DOCUMENT_ROOT"].'/'.$arParams["FILENAME"], 'w+');
fwrite($f, $xmlStr);
fclose($f);

// </editor-fold>
