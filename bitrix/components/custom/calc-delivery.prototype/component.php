<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<?
if (!empty($arParams["SITE_ID"]) && SITE_ID != $arParams["SITE_ID"])
{
    return;
}

CModule::IncludeModule("iblock");
CModule::IncludeModule("highloadblock");
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

$arResult = array();

$arParams["CACHE_TIME"] = (empty($arParams["CACHE_TIME"])) ? $arParams["CACHE_TIME"]: 30 * 60;

$curUrl = preg_replace('/\?(?:.)+$/', '', $_SERVER["REQUEST_URI"]);
$arResult["URL_ADD_TO_CART"] = $curUrl."?ACTION=CALC_TO_CART";
$arResult["ELEMENT_ID"] = $arParams["ELEMENT_ID"];


if ($_REQUEST["ACTION"] == "CALC_TO_CART")
{
    $id = (!empty($_REQUEST["ID"])) ? intval($_REQUEST["ID"]) : 0;

    if ($id)
    {
        Add2BasketByProductID($id, 1, false);
        LocalRedirect("/cart/");
        die;
    }
}

if ($this->startResultCache(false, $arrFilter))
{
    $arResult["SECTION"] = (!empty($arParams["SECTION_ID"])) ? Helper::GetSection2Level($arParams["SECTION_ID"]): false;

    // Список брендов из HL нифоблоков
    $arBrands = array();

    $arBlock = HL\HighloadBlockTable::getById(1)->fetch();
    $rsEntity = HL\HighloadBlockTable::compileEntity($arBlock);

    $rsEntityDataClass = $rsEntity->getDataClass();
    $eTableName = $arBlock["TABLE_NAME"];

    $idTable = 'tbl_'.$eTableName;
    $rsBrands = $rsEntityDataClass::getList(array(
            "select" => array(
                    "ID",
                    "UF_NAME",
                    "UF_XML_ID"
            ),
            "filter" => array(),
            "order" => array(
                    "ID" => "ASC"
            )
    ));

    $rsBrands = new CDBResult($rsBrands, $idTable);
    while ($arBrand = $rsBrands->Fetch())
    {
        $arBrands[$arBrand["UF_XML_ID"]] = array(
                "ID" => $arBrand["ID"],
                "UF_XML_ID" => $arBrand["UF_XML_ID"],
                "NAME" => $arBrand["UF_NAME"]
        );
    }

    //Города
    $arShopCities = array();
    if (\Bitrix\Main\Loader::includeModule('ayers.stores'))
    {
        $rsShopCities = \Ayers\Stores\StoresTable::getList(array(
            'select' => array('ID', 'CITY'),
            'filter' => array(
                '=TYPE' => 'ТекстильТорг'
            ),
        ));

        while ($arShopCity = $rsShopCities->fetch())
        {
            $arShopCities[] = $arShopCity['CITY'];
        }
    }

    $arShopCities = array_unique($arShopCities);

    $rsRegions = CSaleLocation::GetList(
        array(
            "SORT" => "ASC",
            "COUNTRY_NAME_LANG" => "ASC",
            "CITY_NAME_LANG" => "ASC"
        ),
        array(
            "LID" => LANGUAGE_ID,
            "!CITY_NAME" => "",
            "COUNTRY_NAME_ORIG" => $arParams["COUNTRY_NAME_ORIG"]
        ),
        false,
        false,
        array()
    );

    $arJsonRegions = array();
    while ($arRegion = $rsRegions->Fetch())
    {
        if (in_array($arRegion["CITY_NAME"], $arShopCities))
        {
            continue;
        }

        $arResult["JSON"]["regions"][] = array(
            "id" => $arRegion["ID"],
            "name" => $arRegion["CITY_NAME"],
        );

        if ($arRegion["CITY_NAME"] == $arParams["GEO_REGION_CITY_NAME"])
        {
            $arResult["JSON"]["selected"]["region"] = $arRegion["ID"];
        }
    }

    // Список всех разделов
    $rsSections = CIBlockSection::GetList(
        array("NAME" => "ASC"),
        array(
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "ACTIVE" => "Y",
            "DEPTH_LEVEL" => 2,
            "!=CODE" => $arParams["IGNORE_SECTIONS"]
        ),
        false,
        array("ID", "IBLOCK_ID", "NAME")
    );

    $isSectionSelected = false;
    while ($arSection = $rsSections->GetNext())
    {
        // Список всех элементов в секции
        // Запрос находится в цикле, но кешируется
        $rsElements = CIBlockElement::GetList(
            array(),
            array(
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "ACTIVE" => "Y",
                "SECTION_ID" => $arSection["ID"],
                "INCLUDE_SUBSECTIONS" => "Y"
            ),
            false,
            false,
            array("ID", "NAME", "IBLOCK_ID", "PROPERTY_BRAND", "CATALOG_WEIGHT", "PROPERTY_DELIVERY")
        );

        $arJsonItems = array();
        $arJsonBrands = array();
        while($arElement = $rsElements->GetNext())
        {
            $weight = $arElement["CATALOG_WEIGHT"];
            $weight = number_format(($weight / 1000), 1, ".", "");
            $weight = ($weight) ? $weight : 0;

            $arBrand = $arBrands[$arElement["PROPERTY_BRAND_VALUE"]];

            if ($arBrand && empty($arJsonBrands[$arBrand["ID"]]))
            {
                $arJsonBrands[$arBrand["ID"]] = array(
                    "id" => $arBrand["ID"],
                    "name" => $arBrand["NAME"]
                );
            }

            if ($arBrand && $arParams["ELEMENT_ID"] == $arElement["ID"])
            {
                $arResult["JSON"]["selected"]["brand"] = $arBrand["ID"];
            }

            $arJsonItems[] = array(
                "id" => $arElement["ID"],
                "name" => $arElement["NAME"],
                "brand" => $arBrand["ID"],
                "weight" => $weight,
                "width" => number_format(($arElement["CATALOG_WIDTH"] / 1000), 1, ".", ""),
                "height" => number_format(($arElement["CATALOG_HEIGHT"] / 1000), 1, ".", ""),
                "length" => number_format(($arElement["CATALOG_LENGTH"] / 1000), 1, ".", ""),
                "free" => ($arElement["PROPERTY_DELIVERY_VALUE"] == "Бесплатная по РФ") ? true: false
            );

            if ($arParams["ELEMENT_ID"] == $arElement["ID"])
            {
                $arResult["JSON"]["selected"]["good"] = $arElement["ID"];
            }
        }

        usort($arJsonBrands, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        $arResult["JSON"]["sections"][] = array(
            "id" => $arSection["ID"],
            "name" => $arSection["NAME"],
            "brands" => $arJsonBrands,
            "items" => $arJsonItems
        );

        if (!empty($arResult["SECTION"]) && $arResult["SECTION"]["ID"] == $arSection["ID"])
        {
            $arResult["JSON"]["selected"]["section"] = $arSection["ID"];
        }
        elseif (!$isSectionSelected)
        {
            $arResult["JSON"]["selected"]["section"] = $arSection["ID"];
            $isSectionSelected = true;
        }
    }
}

$arResult["JSON"] = json_encode($arResult["JSON"]);

$this->IncludeComponentTemplate();
?>
