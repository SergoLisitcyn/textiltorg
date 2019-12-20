<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Loader;
use \Ayers\Delivery\CalcPrice;

$arFilter = array(
    "ACTIVE" => "Y",
    "IBLOCK_ID" => (SITE_ID == "tp") ? "39" : "38",
    "PROPERTY_ELEMENT" => $arResult["ID"]
);
$res = CIBlockElement::GetList(array(), $arFilter, false, false);
$arResult["COMMENTS_COUNT"] = $res->SelectedRowsCount();

// is export
$arResult["CITY_SHOPS"] = explode(",", $arParams["CITY_SHOPS"]);
if (in_array($arParams["GEO_REGION_CITY_NAME"], $arResult["CITY_SHOPS"]))
    $arResult["IS_EXPORT"] =  "Y";

//resize picture
$arPictures = array(
    $arResult["PREVIEW_PICTURE"]["ID"],
    $arResult["DETAIL_PICTURE"]["ID"]
);

$arResult["RESIZE_PICTURE"] = Helper::Resize($arPictures, 600, 600, true);
if ($arResult["RESIZE_PICTURE"] == NULL)
{
    $arResult["RESIZE_PICTURE"]["SRC"] = SITE_TEMPLATE_PATH."/img/no-img-500.png";
    $arResult["RESIZE_PICTURE"]["NO_PICTURE"] = "Y";
}

// more photos
if ($arResult["PROPERTIES"]["PHOTOS"]["VALUE"])
{
    $arResult["PHOTOS"] = array();
    array_unshift($arResult["PROPERTIES"]["PHOTOS"]["VALUE"], $arResult["DETAIL_PICTURE"]["ID"]);

    foreach ($arResult["PROPERTIES"]["PHOTOS"]["VALUE"] as $photo)
        $arResult["PHOTOS"][] = array(
            "PREVIEW" => Helper::Resize(array($photo), 60, 60),
            "DETAIL" => Helper::Resize(array($photo), 600, 600, true),
        );
}

//price
if ($arResult["OFFERS"])
{
    // offers
    foreach ($arResult["OFFERS"] as $nOffer => $arOffer)
    {
        //resize picture
        $arPictures = array(
            $arOffer["PREVIEW_PICTURE"]["ID"],
            $arOffer["DETAIL_PICTURE"]["ID"]
        );

        $arOffer["RESIZE_PICTURE"] = Helper::Resize($arPictures, 210, 210, true);
        if ($arOffer["RESIZE_PICTURE"] == NULL)
        {
            $arOffer["RESIZE_PICTURE"]["SRC"] = SITE_TEMPLATE_PATH."/img/no-img-500.png";
            $arOffer["RESIZE_PICTURE"]["NO_PICTURE"] = "Y";
        }

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

        $arResult["OFFERS"][$nOffer] = $arOffer;
    }

    $arResult["CATALOG_MEASURE_NAME"] = $arOffer["CATALOG_MEASURE_NAME"];
    $arResult["REGION_PRICE"] = $minPrice;
}
else
{
    $arResult["REGION_PRICE"] = ($arResult["PRICES"][$arParams["GEO_REGION_CITY_NAME"]]) ?
        $arResult["PRICES"][$arParams["GEO_REGION_CITY_NAME"]] :
        $arResult["PRICES"][$arParams["REGION_PRICE_CODE_DEFAULT"]];

    if (isset($arResult["REGION_PRICE"]))
    {
        $arResult["REGION_PRICE"]["DEFAULT_VALUE"] = $arResult["REGION_PRICE"]["DISCOUNT_VALUE"];
    }
}

if ($arResult["REGION_PRICE"]["CURRENCY"] == "BYN")
{
    $arResult["REGION_PRICE"]["DISCOUNT_VALUE"] = number_format($arResult["REGION_PRICE"]["DISCOUNT_VALUE"], 2, ',', ' ');
    $arResult["REGION_PRICE"]["VALUE"] = number_format($arResult["REGION_PRICE"]["VALUE"], 2, ',', ' ');
}
else
{
    $arResult["REGION_PRICE"]["DISCOUNT_VALUE"] = number_format($arResult["REGION_PRICE"]["DISCOUNT_VALUE"], 0, '.', ' ');
    $arResult["REGION_PRICE"]["VALUE"] = number_format($arResult["REGION_PRICE"]["VALUE"], 0, '.', ' ');

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
                $arResult["REGION_PRICE"]["DISCOUNT_VALUE"],
                array($arResult),
                true
            );

            if (!empty($arOptimalDelivery4Items) && $arOptimalDelivery4Items['PRICE']['FORMAT'] != $arResult["REGION_PRICE"]["DISCOUNT_VALUE"])
            {
                $arResult["REGION_PRICE"]["DISCOUNT_VALUE"] = $arOptimalDelivery4Items['PRICE']['FORMAT'];
            }
        }
    }
}

// properties icons
$arResult["IS_ICONS"] = false;
$arPropertiesIcons = array(
    "PRODUCT_CERTIFIED",
    "SUITABLE_ALL_TYPES_FABRIC",
    "GIFT_COUPON_25",
    "EXPERT_ADVICE",
    "PARTICIPANT_ACTION_DISPOSAL"
);

foreach ($arPropertiesIcons as $code)
    if ($arResult["PROPERTIES"][$code]["VALUE_XML_ID"] == "Y")
        $arResult["IS_ICONS"] = true;

// rating
$rating = (intval($arResult["PROPERTIES"]["RATING"]["VALUE"]))? intval($arResult["PROPERTIES"]["RATING"]["VALUE"]) : 0;

$arResult["RATING"] = array(
    "IMAGE" => SITE_TEMPLATE_PATH."/img/stars_0".$rating.".png",
    "COUNT" => $rating,
    "VOTES" => intval($arResult["PROPERTIES"]["VOTES"]["VALUE"])
);

//quantity
if ($arResult["OFFERS"])
{
    $quantity = 0;
    foreach ($arResult["OFFERS"] as $arOffer)
    {
        $quantity += $arOffer["CATALOG_QUANTITY"];
    }
    $arResult["CATALOG_QUANTITY"] = $quantity;
}
else
    $quantity = intval($arResult["CATALOG_QUANTITY"]);

if ($quantity != 0)
    $arResult["QUANTITY"] = array(
        "TEXT" => "Много",
        "CLASS" => "five"
    );

if ($arResult["OFFERS"])
{
    $reserved = 0;
    foreach ($arResult["OFFERS"] as $arOffer)
        $reserved += $arOffer["CATALOG_QUANTITY_RESERVED"];
}
else
    $reserved = intval($arResult["CATALOG_QUANTITY_RESERVED"]);

if ($reserved != 0)
    $arResult["RESERVED"]["TEXT"] = "Много";

// credit
if ($arResult["PROPERTIES"]["PAYMENT_INSTALLMENTS"]["VALUE_XML_ID"] != "Y" && $arResult["REGION_PRICE"]["DEFAULT_VALUE"] >= 3000)
{
    $arResult["CREDIT"] = round($arResult["REGION_PRICE"]["DEFAULT_VALUE"] * 0.048);
    $arResult["CREDIT"] = number_format($arResult["CREDIT"], 0, ".", " ");
}

// brands
CModule::IncludeModule("highloadblock");
use Bitrix\Highloadblock as HL;

$hlBlock = HL\HighloadBlockTable::getById(1)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlBlock);

$entityDataClass = $entity->getDataClass();
$entityTableName = $hlBlock["TABLE_NAME"];

$tableID = 'tbl_'.$entityTableName;
$rsData = $entityDataClass::getList(array(
    "select" => array("UF_NAME", "UF_XML_ID"),
    "filter" => array(
        "UF_XML_ID" => $arResult["PROPERTIES"]["BRAND"]["VALUE"]
    ),
    "order" => array(
        "UF_SORT"=>"ASC",
        "UF_NAME"=>"ASC"
    )
));

$rsData = new CDBResult($rsData, $tableID);

$arBrands = array();
while ($arRes = $rsData->Fetch())
    $arResult["PROPERTIES"]["BRAND"]["PRINT_VALUE"] = $arRes["UF_NAME"];

// features
$hlBlock = HL\HighloadBlockTable::getById(2)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlBlock);

$entityDataClass = $entity->getDataClass();
$entityTableName = $hlBlock["TABLE_NAME"];

$tableID = 'tbl_'.$entityTableName;
$rsData = $entityDataClass::getList(array(
    "select" => array("*"),
    "filter" => array(
        "UF_XML_ID" => $arResult["PROPERTIES"]["FEATURES"]["VALUE"]
    ),
    "order" => array(
        "UF_SORT"=>"ASC",
        "UF_NAME"=>"ASC"
    )
));

$rsData = new CDBResult($rsData, $tableID);

while ($arRes = $rsData->Fetch())
    $arResult["PROPERTIES"]["FEATURES"]["PRINT_VALUE"][] = $arRes;

// equipment
$hlBlock = HL\HighloadBlockTable::getById(3)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlBlock);

$entityDataClass = $entity->getDataClass();
$entityTableName = $hlBlock["TABLE_NAME"];

$tableID = 'tbl_'.$entityTableName;
$rsData = $entityDataClass::getList(array(
    "select" => array("*"),
    "filter" => array(
        "UF_XML_ID" => $arResult["PROPERTIES"]["EQUIPMENT"]["VALUE"]
    ),
    "order" => array(
        "UF_SORT"=>"ASC",
        "UF_NAME"=>"ASC"
    )
));

$rsData = new CDBResult($rsData, $tableID);

while ($arRes = $rsData->Fetch())
    $arResult["PROPERTIES"]["EQUIPMENT"]["PRINT_VALUE"][] = $arRes;

// price component
$arPrice = array_shift($arResult["PRICES"]);

// guarantee
if ($arResult["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"])
{
    $arGuarantee = array();
    $arGuarantee[] = $arResult["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"] + 1;
    $arGuarantee[] = $arResult["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"] + 2;

    $rsElements = CIBlockElement::GetList(
        array(),
        array(
            array(
                "LOGIC" => "OR",
                array(
                    "%NAME" => $arGuarantee[0],
                    "CATALOG_PRICE_".$arPrice["PRICE_ID"] => array(590),
                ),
                array(
                    "%NAME" => $arGuarantee[1],
                    "CATALOG_PRICE_".$arPrice["PRICE_ID"] => array(990),
                ),
            ),
            "ACTIVE" => "Y",
            "IBLOCK_ID" => 9
        ),
        false,
        array(),
        array("ID", "NAME", "CATALOG_GROUP_".$arPrice["PRICE_ID"])
    );

    $arResult["GUARANTEE"] = array(
        array(
            "PRINT_NAME" => "<span class=\"integer\">".$arResult["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"]."</span> ".Helper::DeclOfNum($arResult["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"], array("год", "года", "лет")),
            "PRINT_PRICE" => "Бесплатно"
        )
    );

    while($arElement = $rsElements->GetNext())
    {
        $price = $arElement["CATALOG_PRICE_".$arPrice["PRICE_ID"]];
        if ($arResult["REGION_PRICE"]["CURRENCY"] == "BYN")
        {
            $arElement["PRINT_PRICE"] = (intval($price) > 0)?
                number_format($price, 2, ",", " ") . " руб." :
                "Бесплатно";
        }
        else
        {
            $arElement["PRINT_PRICE"] = (intval($price) > 0)?
                number_format($price, 0, ".", " ") . " руб." :
                "Бесплатно";
        }

        $arElement["ADD_URL"] = $currentPath.$arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arElement["ID"];
        $arElement["PRINT_NAME"] = preg_replace("/\sгарантии/iu", "", $arElement["NAME"]);
		$arElement["PRINT_NAME"] = "<span class=\"integer\">".str_replace(" ", "</span>", $arElement["PRINT_NAME"]);
        $arElement["PRICE"] = $price;
        $arResult["GUARANTEE"][] = $arElement;
    }
}

// gift wraps

$rsElements = CIBlockElement::GetList(
    array(),
    array(
        "ACTIVE" => "Y",
        "IBLOCK_ID" => 10
    ),
    false,
    array(),
    array("ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PICTURE", "CATALOG_GROUP_".$arPrice["PRICE_ID"])
);

$arResult["GIFT_WRAPS"] = array();
while($arElement = $rsElements->GetNext())
{
    $price = $arElement["CATALOG_PRICE_".$arPrice["PRICE_ID"]];
    if ($arResult["REGION_PRICE"]["CURRENCY"] == "BYN")
    {
        $arElement["PRINT_PRICE"] = number_format($price, 2, ",", " ") . " руб.";
    }
    else
    {
        $arElement["PRINT_PRICE"] = number_format($price, 0, ".", " ") . " руб.";
    }

    $arElement["ADD_URL"] = $currentPath.$arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arElement["ID"];
    $arElement["PRICE"] = $price;

    $arElement["RESIZE_PICTURE"]["BIG"] = Helper::Resize(array($arElement["DETAIL_PICTURE"]), 210, 210, true);
    $arElement["RESIZE_PICTURE"]["SMALL"] = Helper::Resize(array($arElement["PREVIEW_PICTURE"]), 70, 20, true);

    $arResult["GIFT_WRAPS"][] = $arElement;
}

// count comments
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
        "UF_ELEMENT" => $arResult["ID"],
        "UF_ACTIVE" => true
    ),
    "order" => array(
        "UF_DATE"=>"DESC",
        "ID"=>"DESC"
    )
));

$rsData = new CDBResult($rsData, $tableID);

$arResult["COMMENTS_COUNT"] = $rsData->SelectedRowsCount();

// export
if ($arResult["IS_EXPORT"] ==  "Y")
{
    $arResult['EXPORT_TEXT'] = 'сегодня';
}
else
{
    if (!empty($arOptimalDelivery4Items['DELIVERY']['STANDART']['PERIODS']))
    {
        $min = preg_replace('/^([\d]+)(?:[-\d\s]+)?$/', '$1', $arOptimalDelivery4Items['DELIVERY']['STANDART']['PERIODS']);

        switch ($min)
        {
            case 1:
                $sign = 'завтра';
                break;

            case 2:
                $sign = 'послезавтра';
                break;

            default:
                $sign = ($min) ?
                    'через '.$min.' '.Helper::DeclOfNum($min, array('день', 'дня', 'дней')):
                    '';
                break;
        }
    }

    $arResult['EXPORT_TEXT'] = (!empty($sign))? 'бесплатно, '.$sign.'': '';
}

// delivery
if ($arResult["PROPERTIES"]["DELIVERY"]["VALUE_XML_ID"] == "YRF" && $arResult["REGION_PRICE"]["CURRENCY"] != "BYN")
{
    $arResult["DELIVERY_TEXT"] = "бесплатно по РФ";
}
elseif ($arResult["IS_EXPORT"] == "Y")
{
    if ($arResult["PROPERTIES"]["DELIVERY"]["VALUE_XML_ID"] == "Y")
    {
        $arResult["DELIVERY_TEXT"] = "бесплатно завтра курьером";
    }
    else
    {
        $arResult["DELIVERY_TEXT"] = "завтра, стоимость 300 руб.";
    }
}
else
{
    if (!empty($arOptimalDelivery4Items['DELIVERY']['STANDART']['PERIODS']))
    {
        $min = preg_replace('/^([\d]+)(?:[-\d\s]+)?$/', '$1', $arOptimalDelivery4Items['DELIVERY']['STANDART']['PERIODS']);

        switch ($min)
        {
            case 1:
                $sign = 'завтра';
                break;

            case 2:
                $sign = 'послезавтра';
                break;

            default:
                $sign = ($min) ?
                    $min.' '.Helper::DeclOfNum($min, array('день', 'дня', 'дней')):
                    '';
                break;
        }
    }

    $arResult["DELIVERY_TEXT"] = ($arOptimalDelivery4Items['DELIVERY']['STANDART']['DELIVER'])?
        "".$sign.", стоимость ".$arOptimalDelivery4Items['DELIVERY']['STANDART']['DELIVER']." руб. ":
        "";
}

if ($arOptimalDelivery4Items['DELIVERY']['EXPRESS']['DELIVER'])
{
    $min = preg_replace('/^([\d]+)(?:[-\d\s]+)?$/', '$1', $arOptimalDelivery4Items['DELIVERY']['EXPRESS']['PERIODS']);

    switch ($min)
    {
        case 1:
            $sign = 'завтра';
            break;

        case 2:
            $sign = 'послезавтра';
            break;

        default:
            $sign = ($min) ?
                $min.' '.Helper::DeclOfNum($min, array('день', 'дня', 'дней')):
                '';
            break;
    }
    $arResult["DELIVERY_TEXT_2"] = "Стандарт - ".$arResult["DELIVERY_TEXT"];
    $arResult["DELIVERY_TEXT_2"] .= "<br>Экспресс - ".$sign.", стоимость ".$arOptimalDelivery4Items['DELIVERY']['EXPRESS']['DELIVER']." руб. ";
    $arResult["DELIVERY_TEXT_2"] = '<span class="deliveries">'.$arResult["DELIVERY_TEXT_2"].'</span>';
	
	
    $arResult["DELIVERY_TEXT"] = $sign.", стоимость ".$arOptimalDelivery4Items['DELIVERY']['STANDART']['DELIVER']." руб.";
    //$arResult["DELIVERY_TEXT"] .= '<span class="deliveries">'.$arResult["DELIVERY_TEXT"].'</span>';
}

//props help
$SECTION_ID = intval($arResult["IBLOCK_SECTION_ID"]);

$hlBlock = HL\HighloadBlockTable::getById(7)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlBlock);

$entityDataClass = $entity->getDataClass();
$entityTableName = $hlBlock["TABLE_NAME"];

$tableID = 'tbl_'.$entityTableName;

$arFilter = array();

if ($SECTION_ID)
{
    $arFilter = array(
        "LOGIC" => "OR",
        array("UF_SECTION" => $SECTION_ID),
        array("UF_SECTION" => "")
    );
}

$rsData = $entityDataClass::getList(array(
    "select" => array("ID", "UF_CODE", "UF_SECTION"),
    "filter" => $arFilter,
    "order" => array(
        "UF_NAME" => "ASC"
    )
));

$rsData = new CDBResult($rsData, $tableID);

while ($arRes = $rsData->Fetch())
{
    foreach ($arResult["DISPLAY_PROPERTIES"] as $nProp => $arProp)
    {
        if ($arProp["CODE"] == $arRes["UF_CODE"])
        {
            $arProp["HELP_ID"] = $arRes["ID"];
            $arResult["DISPLAY_PROPERTIES"][$nProp] = $arProp;
        }
    }
}

// props array
foreach ($arResult["DISPLAY_PROPERTIES"] as $nProp => $arProp)
{
    if (is_array($arProp["DISPLAY_VALUE"]))
    {
        $arProp["DISPLAY_VALUE"] = implode(", ", $arProp["DISPLAY_VALUE"]);
        $arResult["DISPLAY_PROPERTIES"][$nProp] = $arProp;
    }
}

// actions good
$rsElementsActions = CIBlockElement::GetList(
    array(
        "SORT" => "ASC"
    ),
    array(
        "IBLOCK_ID" => 1,
        "ACTIVE" => "Y",
        "PROPERTY_PRODUCTS" => $arResult["ID"]
    ),
    false,
    false,
    array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "PROPERTY_ICON_GOOD")
);

$arResult["GOOD_ACTIONS"] = array();
while ($arElementActions = $rsElementsActions->GetNext())
{
    if ($arElementActions["PROPERTY_ICON_GOOD_VALUE"])
        $arElementActions["ICON_GOOD"] = CFile::GetPath($arElementActions["PROPERTY_ICON_GOOD_VALUE"]);

    if ($arElementActions["ICON_GOOD"])
        $arResult["GOOD_ACTIONS"][] = $arElementActions;
}

// sale action
$rsSaleSections = CIBlockSection::GetList(
    array("LEFT_MARGIN" => "ASC"),
    array(
        "IBLOCK_ID" => 8,
        "!=UF_SALE_SECTION" => false
    ),
    false,
    array("ID", "IBLOCK_ID", "NAME" , "UF_SALE_SECTION")
);

while($arSaleSection = $rsSaleSections->Fetch())
{
    foreach ($arResult["SECTION"]["PATH"] as $arSection)
    {
        if ($arSection["ID"] == $arSaleSection["ID"])
        {
            $arResult["SALE_CATION"] = $arSaleSection["UF_SALE_SECTION"];
        }
    }
}

// seo text

if ($arResult["PROPERTIES"]["BRAND"]["PRINT_VALUE"])
{
    $rsSaleSections = CIBlockSection::GetList(
        array("LEFT_MARGIN" => "ASC"),
        array(
            "IBLOCK_ID" => 8,
            "!=UF_SEO_TEXT" => false
        ),
        false,
        array("ID", "IBLOCK_ID", "NAME" , "UF_SEO_TEXT")
    );

    while($arSaleSection = $rsSaleSections->Fetch())
    {
        foreach ($arResult["SECTION"]["PATH"] as $arSection)
        {
            if ($arSection["ID"] == $arSaleSection["ID"])
            {
                $arResult["SEO_TEXT"] = $arSaleSection["UF_SEO_TEXT"];
                $arResult["SEO_TEXT"] = preg_replace('/\[TITLE\]/', '<p class="info_block_incart_title">', $arResult["SEO_TEXT"]);
                $arResult["SEO_TEXT"] = preg_replace('/\[\/TITLE\]/', '</p>', $arResult["SEO_TEXT"]);
                $arResult["SEO_TEXT"] = preg_replace('/\[B\]/', '<strong>', $arResult["SEO_TEXT"]);
                $arResult["SEO_TEXT"] = preg_replace('/\[\/B\]/', '</strong>', $arResult["SEO_TEXT"]);
                $arResult["SEO_TEXT"] = preg_replace('/#BRAND#/', $arResult["PROPERTIES"]["BRAND"]["PRINT_VALUE"], $arResult["SEO_TEXT"]);

                break;
            }
        }
    }
}

if ($arResult["PROPERTIES"]["EQUIPMENT_HTML"]["~VALUE"]["TEXT"])
{
    $formatText = preg_replace("/<br>/", ",", $arResult["PROPERTIES"]["EQUIPMENT_HTML"]["~VALUE"]["TEXT"]);
    $formatText = preg_replace("/\n/", ",", $formatText);
    $formatText = preg_replace("/\n\r/", ",", $formatText);
    $formatText = preg_replace("/&amp;quot/", '"', $formatText);
    $formatText = strip_tags($formatText);
    $formatText = preg_replace("/,{2,}/iu", "", $formatText);
    $formatText = preg_replace("/\s{2,}/iu", "", $formatText);
    $formatText = preg_replace("/,\s{1,}/iu", ",", $formatText);
    $formatText = preg_replace("/\s{1,},/iu", ",", $formatText);
    $formatText = trim($formatText, ",");
    $formatText = preg_replace("/,/", "<br>", $formatText);

    $arResult["PROPERTIES"]["EQUIPMENT_HTML"]["~VALUE"]["TEXT_FORMAT"] = $formatText;
}

$arResult["NAME_MORF_1"] = mb_lcfirst(ChangeMorphologyText($arResult["NAME"]));
$arResult["NAME_MORF_2"] = mb_lcfirst(ChangeMorphologyText($arResult["NAME"], 'РД'));