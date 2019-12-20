<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("catalog"))
{
    ShowError("Модуль catalog не подключен");
    return;
}

if (!CModule::IncludeModule("sale"))
{
    ShowError("Модуль sale не подключен");
    return;
}

if(!isset($arParams["CACHE_TIME"]))
{
    $arParams["CACHE_TIME"] = false;
}

$arResult["ELEMENTS_CART_ID"] = array();
$arResult["ELEMENTS_CART_XML_ID"] = array();
$arResult["GDESLON_CODES"] = "";
$arResult["BASKET_COUNT_PRODUCT"] = 0;
$arResult["BASKET_SUM"] = 0;
$arResult["IS_KLADR"] = (SITE_ID == 's1')? true: false;


$fuserId = CSaleBasket::GetBasketUserID();

CSaleBasket::UpdateBasketPrices($fuserId, SITE_ID);

$rsCart = CSaleBasket::GetList(
    false,
    array(
        "FUSER_ID" => $fuserId,
        "LID" => SITE_ID,
        "ORDER_ID" => "NULL",
        "DELAY" => "N",
        "CAN_BUY" => "Y"
    ),
    false,
    false,
    array("ID", "NAME", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY",
                "PRICE", "WEIGHT", "DETAIL_PAGE_URL", "NOTES", "CURRENCY", "VAT_RATE", "CATALOG_XML_ID",
                "PRODUCT_XML_ID", "SUBSCRIBE", "DISCOUNT_PRICE", "PRODUCT_PROVIDER_CLASS", "TYPE", "SET_PARENT_ID")
);

$arResult["DELIVER"] = 0;
$arResult["DELIVER_PERIOD"] = 0;
$arResult["DELIVER_EXPRESS"] = 0;
$arResult["DELIVER_EXPRESS_PERIOD"] = 0;

$arCartItemsId = array();
$arCartItems = array();
$arCartQuantity = array();
while ($arCartItem = $rsCart->GetNext())
{
    $arResult["ELEMENTS_CART_ID"][] = $arCartItem["PRODUCT_ID"];

    $productXmlId = (!empty($arCartItem["PRODUCT_XML_ID"])) ? $arCartItem["PRODUCT_XML_ID"]: $arCartItem["PRODUCT_ID"];

    $arResult["ELEMENTS_CART_XML_ID"][] = $productXmlId;
    $arResult["BASKET_COUNT_PRODUCT"]++;
    $arResult["BASKET_SUM"] += $arCartItem["PRICE"] * $arCartItem["QUANTITY"];
    $arResult["BASKET_WEIGHT"] += ($arCartItem["WEIGHT"] * $arCartItem["QUANTITY"]);

    $arCartQuantity[$arCartItem["PRODUCT_ID"]] = $arCartItem["QUANTITY"];
    $arCartItems[] = $arCartItem;
    $arCartItemsId[] = $arCartItem["PRODUCT_ID"];

    $gdeslonCodes .= $productXmlId .':'. intval($arCartItem["PRICE"] ) . ',';
    for($i=1; $i<$arCartItem["QUANTITY"]; $i++) { // Add the same
        $gdeslonCodes .= $productXmlId .':'. intval($arCartItem["PRICE"] ) . ',';
    }
}

$arResult['MRC'] = Helper::isMrc($arCartItemsId);

$arResult["GDESLON_CODES"] = rtrim($gdeslonCodes,',');

// delivery price calc

if (\Bitrix\Main\Loader::includeModule('ayers.delivery'))
{
    $isInShops = \Ayers\Delivery\CalcPrice::IsInShops();

    if (!$isInShops && SITE_ID == 's1' && $arCartItemsId)
    {
        $arItems = CIBlockElement::GetList(
            array(
                "SORT" => "ASC"
            ),
            array(
                "ID" => $arCartItemsId
            ),
            false,
            false,
            array("ID", "NAME", "IBLOCK_ID", "CATALOG_WEIGHT", "CATALOG_WIDTH", "CATALOG_HEIGHT", "CATALOG_LENGTH", "PROPERTY_DELIVERY", "CATALOG_GROUP_1")
        );

        $arResult['IS_DELIVER_FREE'] = false;
        while ($arItem = $arItems->Fetch())
        {
            $optimalCompany = \Ayers\Delivery\CalcPrice::GetOptimalCompany4City($_SESSION['GEO_REGION_CITY_NAME']);
            $arOptimalDelivery4Items = \Ayers\Delivery\CalcPrice::GetOptimalDelivery4Items(
                $optimalCompany,
                $_SESSION['GEO_REGION_CITY_NAME'],
                $arItem['CATALOG_PRICE_1'],
                array($arItem),
                true
            );




            if ($arOptimalDelivery4Items)
            {
                $arResult["DELIVER"] += $arOptimalDelivery4Items['DELIVERY']['STANDART']['DELIVER'] * $arCartQuantity[$arItem['ID']];
                $min = preg_replace('/^([\d]+)(?:[-\d\s]+)?$/', '$1', $arOptimalDelivery4Items['DELIVERY']['STANDART']['PERIODS']);

                if (empty($arResult["DELIVER_PERIOD"]) || $arResult["DELIVER_PERIOD"] < $min)
                {
                    $arResult["DELIVER_PERIOD"] = $min;
                }

                $arResult["DELIVER_EXPRESS"] += $arOptimalDelivery4Items['DELIVERY']['EXPRESS']['DELIVER'] * $arCartQuantity[$arItem['ID']];
                $min = preg_replace('/^([\d]+)(?:[-\d\s]+)?$/', '$1', $arOptimalDelivery4Items['DELIVERY']['EXPRESS']['PERIODS']);

                if (empty($arResult["DELIVER_EXPRESS_PERIOD"]) || $arResult["DELIVER_EXPRESS_PERIOD"] < $min)
                {
                    $arResult["DELIVER_EXPRESS_PERIOD"] = $min;
                }
            }

            if ($arItem["PROPERTY_DELIVERY_VALUE"] == "Бесплатная по РФ")
            {
                $arResult['IS_DELIVER_FREE'] = true;
            }
        }
    }
}

$arResult["DELIVER"] = (empty($arResult["DELIVER"]))? 0:
    number_format($arResult["DELIVER"], 0, ".", " ");

$arResult["DELIVER_EXPRESS"] = (empty($arResult["DELIVER_EXPRESS"]))? 0:
    number_format($arResult["DELIVER_EXPRESS"], 0, ".", " ");

$arResult["DELIVER_PERIOD_SIGN"] = (empty($arResult["DELIVER_PERIOD"]))?:
    Helper::DeclOfNum($arResult["DELIVER_PERIOD"], array('день', 'дня', 'дней'));

$arResult["DELIVER_EXPRESS_PERIOD_SIGN"] = (empty($arResult["DELIVER_EXPRESS_PERIOD"]))?:
    Helper::DeclOfNum($arResult["DELIVER_EXPRESS_PERIOD"], array('день', 'дня', 'дней'));

if (empty($arResult["DELIVER_EXPRESS"]))
{
    unset($arParams["DELIVERYES"]["Регион"][14]);
}

$arOrder = array(
    "SITE_ID" => SITE_ID,
    "USER_ID" => $GLOBALS["USER"]->GetID(),
    "ORDER_PRICE" => $arResult["BASKET_SUM"],
    "ORDER_WEIGHT" => $arResult["BASKET_WEIGHT"],
    "BASKET_ITEMS" => $arCartItems
);

$arOptions = array(
    "COUNT_DISCOUNT_4_ALL_QUANTITY" => "Y",
);

$arErrors = array();

CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);

$arResult["BASKET_SUM"] = 0;
foreach ($arOrder["BASKET_ITEMS"] as &$arOneItem)
{
    $arResult["BASKET_SUM"] += ($arOneItem["PRICE"] * $arOneItem["QUANTITY"]);
}

if (SITE_ID == "by")
{
    $arResult["BASKET_SUM_FORMAT"] = number_format($arResult["BASKET_SUM"], 2, ",", " ");
}
else
{
    $arResult["BASKET_SUM_FORMAT"] = number_format($arResult["BASKET_SUM"], 0, ".", " ");
}

// Обработка способов оплаты
$arResult["PAY_SYSTEMS"] = (!empty($arParams["PAY_SYSTEMS"]) && is_array($arParams["PAY_SYSTEMS"]))? $arParams["PAY_SYSTEMS"]: array();

$arResult["DELIVERYES"] = (!empty($arParams["DELIVERYES"]) && is_array($arParams["DELIVERYES"])) ?
    $arParams["DELIVERYES"]:
    array();

// Пункты самовывоза
use \Ayers\Stores;
use \Bitrix\Main;
use \Ayers\Delivery;

if (Main\Loader::includeModule('ayers.stores') &&
    Main\Loader::includeModule('ayers.delivery'))
{
    if ($this->arParams['GEO_REGION_CITY_NAME'])
    {
        $arStoresFilter = array(
            '=CITY' => $this->arParams['GEO_REGION_CITY_NAME']
        );

        $isInShops = \Ayers\Delivery\CalcPrice::IsInShops();

        if (!$isInShops)
        {
            $arStoresFilter['=TYPE'] =  Delivery\CalcPrice::FormatOptimalCompany($optimalCompany);
        }
        else
        {
            $arStoresFilter[] = array(
                'LOGIC' => 'OR',
                array('=TYPE' => 'СДЭК'),
                array('=TYPE' => 'ТекстильТорг')
            );
        }

        $rsStores = \Ayers\Stores\StoresTable::getList(array(
            'order' => array('SORT' => 'ASC', 'ADDRESS' => 'ASC'),
            'select' => array('*'),
            'filter' => $arStoresFilter
        ));

        $arResult['STORES'] = array();
        while ($arStore = $rsStores->fetch())
        {
            if ($arStore['TYPE'] == 'ТекстильТорг')
            {
                $arResult['STORES']['HOME'][] = $arStore;
            }
            else
            {
                $arResult['STORES']['STORES'][] = $arStore;
            }
        }

        if (!empty($arResult["STORES"]) &&
            !empty($arParams["DELIVERY_STORE"]) && is_array($arParams["DELIVERY_STORE"]))
        {
            foreach ($arResult["DELIVERYES"] as $city => $arDelivery)
            {
                $arDelivery = $arParams["DELIVERY_STORE"] + $arDelivery;
                $arResult["DELIVERYES"][$city] =  $arDelivery;
            }
        }
    }
}

// --

foreach ($arResult["PAY_SYSTEMS"] as $nPaySystem => $arPaySystem)
{
    if (is_array($arPaySystem))
    {
        if ($arPaySystem["HELP"])
        {
            $arResult["HELP_PAY_SYSTEMS"][$nPaySystem] = $arPaySystem["HELP"];
        }

        if ($arResult["BASKET_SUM"] >= $arPaySystem["KREDIT"])
        {
            $arResult["PAY_SYSTEMS"][$nPaySystem] = $arPaySystem["NAME"];
        }
        else
        {
            unset($arResult["PAY_SYSTEMS"][$nPaySystem]);
        }
    }
}

$arResult["CITY_DELIVERY"] = (!empty($arParams["DELIVERYES"][$arParams["GEO_REGION_CITY_NAME"]])) ?
    $arParams["GEO_REGION_CITY_NAME"]:
    "Регион";

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

$arResult["LOCATIONS"] = array();
while ($arRegion = $rsRegions->Fetch())
{
    $arRegion["SELECTED"] = ($arRegion["CITY_NAME"] == $arParams["GEO_REGION_CITY_NAME"]) ? "Y": "N";
    $arResult["LOCATIONS"][] = $arRegion;
}

$this->IncludeComponentTemplate();
?>