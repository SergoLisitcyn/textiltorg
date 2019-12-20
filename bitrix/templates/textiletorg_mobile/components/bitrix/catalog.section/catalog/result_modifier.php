<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Loader;
use \Ayers\Delivery\CalcPrice;

if(preg_match("/spb.textiletorg/i",$_SERVER["SERVER_NAME"])){
	
	if($arResult["UF_SEO_SPB_M_KEYWORD"] != ""){
		$arResult["IPROPERTY_VALUES"]["SECTION_META_KEYWORDS"] = $arResult["UF_SEO_SPB_M_KEYWORD"];
	}
	
	//$arResult["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"]
	if($arResult["UF_SEO_SPB_M_TITLE"] != ""){
		$arResult["IPROPERTY_VALUES"]["SECTION_META_TITLE"] = $arResult["UF_SEO_SPB_M_TITLE"];
	}
	
	if($arResult["UF_SEO_SPB_M_DESC"] != ""){
		$arResult["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"] = $arResult["UF_SEO_SPB_M_DESC"];
	}
	
	//$arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]
	
	$arResult["DESCRIPTION"] = $arResult["~UF_SPB_DESCRIPTION"];
}

unset($arResult["UF_SPB_DESCRIPTION"],$arResult["UF_SEO_SPB_M_KEYWORD"],$arResult["UF_SEO_SPB_M_TITLE"],$arResult["UF_SEO_SPB_M_DESC"]);

// count comments
CModule::IncludeModule("highloadblock");
use Bitrix\Highloadblock as HL;

$arIDs = array();
$arCommentsCount = array();
foreach ($arResult["ITEMS"] as $nItem => $arItem)
    $arIDs[] = $arItem["ID"];

$hlBlock = HL\HighloadBlockTable::getById(6)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlBlock);

$entityDataClass = $entity->getDataClass();
$entityTableName = $hlBlock["TABLE_NAME"];

$tableID = 'tbl_'.$entityTableName;
$rsData = $entityDataClass::getList(array(
    "select" => array(
        "UF_ELEMENT"
    ),
    "filter" => array(
        "UF_ELEMENT" => $arIDs,
        "UF_ACTIVE" => true
    ),
    "order" => array(
        "UF_DATE"=>"DESC",
        "ID"=>"DESC"
    )
));

$rsData = new CDBResult($rsData, $tableID);

while ($arItem = $rsData->Fetch())
    $arCommentsCount[$arItem["UF_ELEMENT"]] += 1;

// actions good
$rsElementsActions = CIBlockElement::GetList(
    array(
        "SORT" => "ASC"
    ),
    array(
        "IBLOCK_ID" => 1,
        "ACTIVE" => "Y",
        "PROPERTY_PRODUCTS" => $arIDs
    ),
    false,
    false,
    array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "PROPERTY_ICON_GOOD", "PROPERTY_PRODUCTS")
);

$arElementsActions = array();
while ($arElementActions = $rsElementsActions->GetNext())
{
    if ($arElementActions["PROPERTY_ICON_GOOD_VALUE"])
    {
        $arElementActions["ICON_GOOD"] = CFile::GetPath($arElementActions["PROPERTY_ICON_GOOD_VALUE"]);
    }

    $arElementsActions[] = $arElementActions;
}

// Распихаем акции по товарам
$rsActions = CIBlockElement::GetList(
    array("SORT" => "ASC"),
    array("IBLOCK_ID" => 1, "ACTIVE" => "Y", "PROPERTY_PRODUCTS" => $arIDs),
    false,
    false,
    array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "PROPERTY_ICON_GOOD", "PROPERTY_PRODUCTS")
);
$arAcrions = array();
while($ob = $rsActions->GetNextElement())
{
    $arFields = $ob->GetFields();
    $arProps = $ob->GetProperties();
    $arAcrions[$arFields["ID"]] = array_merge($arFields, $arProps);
}
foreach ($arResult["ITEMS"] as $key => $arItem) {
    $arItemActions = array();
    foreach ($arAcrions as $action) {
        if (in_array($arItem["ID"], $action["PRODUCTS"]["VALUE"])) {
            $arItemActions[] = array(
                "NAME" => $action["NAME"],
                "URL" => $action["DETAIL_PAGE_URL"]
            );
        }
    }
    $arResult["ITEMS"][$key]["ACTIONS"] = $arItemActions;
}

foreach ($arResult["ITEMS"] as $nItem => $arItem)
{
    //resize picture
    $arPictures = array(
        $arItem["PREVIEW_PICTURE"]["ID"],
        $arItem["DETAIL_PICTURE"]["ID"]
    );

    $arItem["RESIZE_PICTURE"] = Helper::Resize($arPictures, 220, 180, true);
    if ($arItem["RESIZE_PICTURE"] == NULL)
        $arItem["RESIZE_PICTURE"]["SRC"] = SITE_TEMPLATE_PATH."/img/noimage-220x180.jpg";


    if ($arItem["OFFERS"])
    {
        // offers
        foreach ($arItem["OFFERS"] as $nOffer => $arOffer)
        {
            //resize picture
            $arPictures = array(
                $arOffer["PREVIEW_PICTURE"]["ID"],
                $arOffer["DETAIL_PICTURE"]["ID"]
            );

            $arOffer["RESIZE_PICTURE"] = Helper::Resize($arPictures, 210, 210, true);
            if ($arOffer["RESIZE_PICTURE"] == NULL)
                $arOffer["RESIZE_PICTURE"]["SRC"] = SITE_TEMPLATE_PATH."/img/no-img-200.png";

            //price
            $arOffer["REGION_PRICE"] = ($arOffer["PRICES"][$arParams["GEO_REGION_CITY_NAME"]]) ?
                $arOffer["PRICES"][$arParams["GEO_REGION_CITY_NAME"]] :
                $arOffer["PRICES"][$arParams["REGION_PRICE_CODE_DEFAULT"]];

            if (isset($arResult["REGION_PRICE"]))
            {
                $arOffer["REGION_PRICE"]["DEFAULT_VALUE"] = $arOffer["REGION_PRICE"]["DISCOUNT_VALUE"];
            }

            // min price
            $minPrice = array();
            if (empty($minPrice) || $minPrice["DEFAULT_VALUE"] > $arOffer["REGION_PRICE"]["DEFAULT_VALUE"])
                $minPrice = $arOffer["REGION_PRICE"];

            $arItem["OFFERS"][$nOffer] = $arOffer;
        }

        $arItem["CATALOG_MEASURE_NAME"] = $arOffer["CATALOG_MEASURE_NAME"];
        $arItem["REGION_PRICE"] = $minPrice;
    }
    else
    {
        $arItem["REGION_PRICE"] = ($arItem["PRICES"][$arParams["GEO_REGION_CITY_NAME"]]) ?
        $arItem["PRICES"][$arParams["GEO_REGION_CITY_NAME"]] :
        $arItem["PRICES"][$arParams["REGION_PRICE_CODE_DEFAULT"]];

        if (isset($arItem["REGION_PRICE"]))
        {
            $arItem["REGION_PRICE"]["DEFAULT_VALUE"] = $arItem["REGION_PRICE"]["DISCOUNT_VALUE"];
        }
    }

    if ($arItem["REGION_PRICE"]["CURRENCY"] == "BYN")
    {
        $arItem["REGION_PRICE"]["DISCOUNT_VALUE"] = number_format($arItem["REGION_PRICE"]["DISCOUNT_VALUE"], 2, ',', ' ');
        $arItem["REGION_PRICE"]["VALUE"] = number_format($arItem["REGION_PRICE"]["VALUE"], 2, ',', ' ');
    }
    else
    {
        $arItem["REGION_PRICE"]["DISCOUNT_VALUE"] = number_format($arItem["REGION_PRICE"]["DISCOUNT_VALUE"], 0, '.', ' ');
        $arItem["REGION_PRICE"]["VALUE"] = number_format($arItem["REGION_PRICE"]["VALUE"], 0, '.', ' ');

        // delivery price calc
        if (Loader::includeModule('ayers.delivery'))
        {
            $isInShops = CalcPrice::IsInShops();

            if (!$isInShops && SITE_ID == 's1')
            {
                $optimalCompany = CalcPrice::GetOptimalCompany4City($arParams["GEO_REGION_CITY_NAME"]);

                $arOptimalDelivery4Items = CalcPrice::GetOptimalDelivery4Items(
                    $optimalCompany,
                    $arParams["GEO_REGION_CITY_NAME"],
                    $arItem["REGION_PRICE"]["DISCOUNT_VALUE"],
                    array($arItem),
                    true
                );

                if (!empty($arOptimalDelivery4Items) && $arOptimalDelivery4Items['PRICE']['FORMAT'] != $arItem["REGION_PRICE"]["DISCOUNT_VALUE"])
                {
                    $arItem["REGION_PRICE"]["DISCOUNT_VALUE"] = $arOptimalDelivery4Items['PRICE']['FORMAT'];
                }
            }
        }
    }

    // rating

    $rating = (intval($arItem["PROPERTIES"]["RATING"]["VALUE"]))? intval($arItem["PROPERTIES"]["RATING"]["VALUE"]) : 0;

    switch ($rating)
    {
        case "1":
            $class = "r1";
            break;
        case "2":
            $class = "r2";
            break;
        case "3":
            $class = "r3";
            break;
        case "4":
            $class = "r4";
            break;
        case "5":
            $class = "r5";
            break;

        default:
            $class = "r0";
            break;
    }

    $arItem["RATING"] = array(
        "CLASS" => $class,
        "COUNT" => $rating,
        "VOTES" => intval($arItem["PROPERTIES"]["VOTES"]["VALUE"])
    );

    // credit
    if ($arItem["PROPERTIES"]["PAYMENT_INSTALLMENTS"]["VALUE_XML_ID"] != "Y" && $arItem["REGION_PRICE"]["DEFAULT_VALUE"] >= 3000)
    {
        $arItem["CREDIT"] = round($arItem["REGION_PRICE"]["DEFAULT_VALUE"] * 0.048);
        $arItem["CREDIT"] = number_format($arItem["CREDIT"], 0, ".", " ");
    }

    // url
    $arItem["DETAIL_PAGE_URL"] = Helper::RemoveOneLavelUrl($arItem["DETAIL_PAGE_URL"]);

    //reviews
    $arItem["COMMENTS_COUNT"] = $arCommentsCount[$arItem["ID"]];

    $arResult["ITEMS"][$nItem] = $arItem;
}

$arResult["UF_YM_ID"] = 0;
$arResult["UF_TEXT_FOR_FEEDBACK"] = "";
$db_list = CIBlockSection::GetList(Array(), Array('IBLOCK_ID' => $arParams["IBLOCK_ID"], "ID" => $arParams["SECTION_ID"]), false, array("IBLOCK_ID", "ID", "UF_YM_ID", "UF_TEXT_FOR_FEEDBACK"));
while($ar_result = $db_list->GetNext())
{
    $arResult["UF_YM_ID"] = $ar_result["UF_YM_ID"];
    $arResult["UF_TEXT_FOR_FEEDBACK"] = $ar_result["UF_TEXT_FOR_FEEDBACK"];
}