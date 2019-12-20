<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<?
if (!CModule::IncludeModule("blog"))
{
    ShowError("Модуль blog не подключен");
    return;
}
if (!CModule::IncludeModule("iblock"))
{
    ShowError("Модуль iblock не подключен");
    return;
}

if (!CModule::IncludeModule("sale"))
{
    ShowError("Модуль sale не подключен");
    return;
}
$UpdateBasketPrices = false;


if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 36000000;


$arResult["HOUSE_REGIONS"] = (!empty($arParams["HOUSE_REGIONS"]) && is_array($arParams["HOUSE_REGIONS"]))? $arParams["HOUSE_REGIONS"]: array();
$arResult["QUERY"] = (!empty($_REQUEST["QUERY"]))? htmlspecialchars($_REQUEST["QUERY"], ENT_QUOTES) : null;
$arResult["DEFAULT_REGION"] = trim($arParams["DEFAULT_REGION"]);
$arResult["CITIES_PRICE"] = (!empty($arParams["CITIES_PRICE"]) && is_array($arParams["CITIES_PRICE"]))? $arParams["CITIES_PRICE"]: array("Москва" => 1);

$uIP = CBlogUser::GetUserIP();
$arResult["USER_IP"] = $uIP;

// Переопределяем для колцентра
if($arResult["USER_IP"] == '78.36.197.254') {
    $arResult["USER_IP"] = '188.255.54.230';
}

require_once(realpath(dirname(__FILE__))."/ipgeobase.php");
$obGeoBase = new IPGeoBase();
$arGeoBase = $obGeoBase->getRecord($arResult["USER_IP"]);

$currentPath = CHTTP::urlDeleteParams(
    $APPLICATION->GetCurPageParam(),
    array("SET_CITY", "SECTION_CODE_PATH", "ELEMENT_CODE", ""),
    array("delete_system_params" => true)
);

$ctxParam = strtoupper($_REQUEST["ctx"]);
$forciblySetСity = (!empty($arParams["CTX_PARAM"][$ctxParam])) ? $arParams["CTX_PARAM"][$ctxParam] : false;

/*-----------------------------------------------------------------*/
/*
if (SITE_ID === "s1" && (empty($_SESSION["GEO_REGION_CITY_NAME"]) || !empty($_REQUEST["SET_CITY"]))) {
    $arSite = explode('.', $_SERVER['HTTP_HOST']);
    if ($arSite[0] === 'www') {
        $currentDomain = $arSite[1];
    } else {
        $currentDomain = $arSite[0];
    }
    if ('textiletorg' === $currentDomain) {
        $currentDomain = false;
    }

    $_SESSION['REGION_DOMAIN_CODE'] = $currentDomain;

    $filter = array('IBLOCK_CODE' => 'subdomainCity', 'ACTIVE' => 'Y');

    if (!empty($_REQUEST["SET_CITY"])) {
        $filter['PROPERTY_CITY_NAME'] = $_REQUEST["SET_CITY"];
    } elseif (false === $currentDomain) {
        $filter['PROPERTY_CITY_NAME'] = $_SESSION["GEO_REGION_CITY_NAME"] ?: $arGeoBase["city"];
    } else {
        $filter['PROPERTY_DOMAIN_CODE'] = $currentDomain;
    }

    $dbDomain = \CIBlockElement::GetList(
        array(),
        $filter,
        false,
        false,
        array('ID', 'IBLOCK_ID', 'PROPERTY_CITY_NAME', 'PROPERTY_DOMAIN_CODE')
    );

   if ($domen = $dbDomain->Fetch()) {
        if ($_SESSION["GEO_REGION_CITY_NAME"] !== $domen['PROPERTY_CITY_NAME_VALUE'] || empty($_REQUEST["SET_CITY"])) {
            unset($_SESSION["GEO_REGION_CITY_NAME"]);
            $arGeoBase["city"] = $domen['PROPERTY_CITY_NAME_VALUE'];
        }
        if ($currentDomain != $domen['PROPERTY_DOMAIN_CODE_VALUE']) {
            $redirect = 'https://' . $domen['PROPERTY_DOMAIN_CODE_VALUE'] . '.textiletorg.ru';
            $redirect .= $APPLICATION->GetCurPageParam();
            LocalRedirect($redirect);
        }
    } else {
        if ($currentDomain && strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE) {
            $redirect = 'https://www.textiletorg.ru';
            $redirect .= $APPLICATION->GetCurPageParam();
            LocalRedirect($redirect);
        }
    }
}
*/
/*-----------------------------------------------------------------*/

$IsSiteS1 = SITE_ID == "s1";
$IsSubDomain = false;
$SubDomainCity = "";

if($IsSiteS1 && !preg_match("/^www/",$_SERVER["SERVER_NAME"])){
	foreach($arParams["SUBDOMAIN"] as $key => $value){
		if(preg_match("/^".$value."/",$_SERVER["SERVER_NAME"])){
			$IsSubDomain = true;
			$SubDomainCity = $key;
		}
	}
}

if($IsSubDomain){
	if($SubDomainCity != $_SESSION["GEO_REGION_CITY_NAME"]){
		$Query = CSaleLocation::GetList(
			array(),
			array("LID" => LANGUAGE_ID,"CITY_NAME" => $SubDomainCity,"COUNTRY_NAME_ORIG" => $arParams["COUNTRY_NAME_ORIG"]),
			false,
			false,
			array("ID","CITY_NAME")
		);
	    if ($Answer = $Query->GetNext()){
			
			$GLOBALS["GEO_REGION_CITY_ID"] = $_SESSION["GEO_REGION_CITY_ID"] = $Answer["ID"];
	        $GLOBALS["GEO_REGION_CITY_NAME"] = $_SESSION["GEO_REGION_CITY_NAME"] = $Answer["CITY_NAME"];
	       	$GLOBALS["GEO_IS_CONFIRMED"] = $_SESSION["GEO_IS_CONFIRMED"] = true;
			
	        $fuserId = CSaleBasket::GetBasketUserID();
			CSaleBasket::UpdateBasketPrices($fuserId, SITE_ID);
	    	
	    }
	}
} elseif(empty($_SESSION["GEO_REGION_CITY_ID"]) || empty($_SESSION["GEO_REGION_CITY_NAME"]) || !empty($_REQUEST["SET_CITY"]) || ($forciblySetСity)){

    if ($forciblySetСity)
    {
        $cityName = $forciblySetСity;
    }
    elseif (!empty($_REQUEST["SET_CITY"]))
    {
        $cityName = $_REQUEST["SET_CITY"];
    }
    else
    {
        $cityName = ($arGeoBase["city"]) ? $arGeoBase["city"] : $arResult["DEFAULT_REGION"];
    }

    $rsRegions = CSaleLocation::GetList(
        array(
            "SORT" => "ASC",
            "COUNTRY_NAME_LANG" => "ASC",
            "CITY_NAME_LANG" => "ASC"
        ),
        array(
            "LID" => LANGUAGE_ID,
            "CITY_NAME" => trim($cityName),
            "COUNTRY_NAME_ORIG" => $arParams["COUNTRY_NAME_ORIG"]
        ),
        false,
        false,
        array()
    );

    if ($arRegion = $rsRegions->GetNext())
    {

        $_SESSION["GEO_REGION_CITY_ID"] = $arRegion["ID"];
        $_SESSION["GEO_REGION_CITY_NAME"] = $arRegion["CITY_NAME"];
    }
    else
    {
        $rsRegions = CSaleLocation::GetList(
            array(
                "SORT" => "ASC",
                "COUNTRY_NAME_LANG" => "ASC",
                "CITY_NAME_LANG" => "ASC"
            ),
            array(
                "LID" => LANGUAGE_ID,
                "CITY_NAME" => $arResult["DEFAULT_REGION"],
                "COUNTRY_NAME_ORIG" => $arParams["COUNTRY_NAME_ORIG"]
            ),
            false,
            false,
            array()
        );

        if ($arRegion = $rsRegions->GetNext())
        {
            $_SESSION["GEO_REGION_CITY_ID"] = $arRegion["ID"];
            $_SESSION["GEO_REGION_CITY_NAME"] = $arRegion["CITY_NAME"];
        }
    }

    if (!empty($_REQUEST["SET_CITY"]))
    {
        $_SESSION["GEO_IS_CONFIRMED"] = true;
    }
    $UpdateBasketPrices = true;

}


$arResult["GEO_REGION_CITY_ID"] = $_SESSION["GEO_REGION_CITY_ID"];
$arResult["GEO_REGION_CITY_NAME"] = $_SESSION["GEO_REGION_CITY_NAME"];

$GLOBALS["GEO_REGION_CITY_NAME"] = $arResult["GEO_REGION_CITY_NAME"];

$GLOBALS["CITY_FILTER_PRICE_CODE"] = array(
    $arResult["DEFAULT_REGION"]
);

$GLOBALS["CITY_PRICE_CODE"] = array(
    $arResult["DEFAULT_REGION"],
    $arResult["GEO_REGION_CITY_NAME"]
);

if ($arResult["GEO_REGION_CITY_NAME"] && array_key_exists($arResult["GEO_REGION_CITY_NAME"], $arResult["CITIES_PRICE"]))
{
    $GLOBALS["PRICE_ID"] = $arResult["CITIES_PRICE"][$arResult["GEO_REGION_CITY_NAME"]];
    $_SESSION["PRICE_ID"] = $arResult["CITIES_PRICE"][$arResult["GEO_REGION_CITY_NAME"]];
}
else
{
    $GLOBALS["PRICE_ID"] = $arResult["CITIES_PRICE"][$arResult["DEFAULT_REGION"]];
    $_SESSION["PRICE_ID"] = $arResult["CITIES_PRICE"][$arResult["DEFAULT_REGION"]];
}
if ($UpdateBasketPrices) {
    // перерасчет корзины для регинов
    $fuserId = CSaleBasket::GetBasketUserID();
    CSaleBasket::UpdateBasketPrices($fuserId, SITE_ID);
    if (!empty($_REQUEST["SET_CITY"])) {
        LocalRedirect($currentPath);
    }
//
}
require_once(realpath(dirname(__FILE__))."/location.php");
use Bitrix\Main;
Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleComponentOrderProperties',
    array("AsLocation", "OnSaleComponentOrderProperties")
);


if ($_REQUEST["AJAX_SEARCH_REGION"] == "Y" && !empty($arResult["QUERY"]) && strlen($arResult["QUERY"]) >= 3)
{
    $APPLICATION->RestartBuffer();
    header("Content-Type: application/json");

    try
    {
        $rsRegions = CSaleLocation::GetList(
            array(
                "SORT" => "ASC",
                "COUNTRY_NAME_LANG" => "ASC",
                "CITY_NAME_LANG" => "ASC"
            ),
            array(
                "LID" => LANGUAGE_ID,
                "%CITY_NAME" => $arResult["QUERY"],
                "COUNTRY_NAME_ORIG" => $arParams["COUNTRY_NAME_ORIG"],
            ),
            false,
            false,
            array()
        );

        $arResult["ITEMS"] = array();
        while ($arRegion = $rsRegions->Fetch())
        {
            $arResult["ITEMS"][] = array(
                "city_id" => $arRegion["ID"],
                "city_name" => $arRegion["CITY_NAME"],
                "region_id" => $arRegion["REGION_ID"],
                "region_name" => $arRegion["REGION_NAME"]
            );
        }

        echo json_encode($arResult["ITEMS"]);
    }
    catch (Exception $e)
    {
        echo json_encode(array(
            "status" => "error",
            "message" => $e->getMessage()
        ));
    }
    die(false);
}

if ($_REQUEST["IS_GEO_CONFIRM"] == "Y")
{
    $APPLICATION->RestartBuffer();
    header("Content-Type: application/json");

    $_SESSION["GEO_IS_CONFIRMED"] = true;

    echo json_encode(array(
        "status" => "success",
    ));

    die;
}

if ($this->StartResultCache())
{
    $rsRegions = CSaleLocation::GetList(
        array(
            "SORT" => "ASC",
            "COUNTRY_NAME_LANG" => "ASC",
            "CITY_NAME_LANG" => "ASC"
        ),
        array(
            "LID" => LANGUAGE_ID,
            "!CITY_NAME" => "",
            "COUNTRY_NAME_ORIG" => $arParams["COUNTRY_NAME_ORIG"],
        ),
        false,
        false,
        array()
    );

    $arResult["ABC"] = array();

    $currentPath .= (stripos($currentPath, '?') === false ? '?' : '&');

    while ($arRegion = $rsRegions->Fetch())
    {
        $arRegion["SET_CITY_URL"] = "";
        if($IsSiteS1){
			
			$Flag = isset($arParams["SUBDOMAIN"][$arRegion["CITY_NAME"]]);
			
			$arRegion["SET_CITY_URL"] .= "https://";
			$arRegion["SET_CITY_URL"] .= ($Flag ? $arParams["SUBDOMAIN"][$arRegion["CITY_NAME"]] : "www");
			$arRegion["SET_CITY_URL"] .= ".textiletorg.ru".$APPLICATION->GetCurPage(false);
			$arRegion["SET_CITY_URL"] .= ($Flag ? "" : "?SET_CITY=".$arRegion["CITY_NAME"]);
			
		} else {
			$arRegion["SET_CITY_URL"] .= "?SET_CITY=".$arRegion["CITY_NAME"];
		}
        
        if (in_array($arRegion["CITY_NAME"], $arResult["HOUSE_REGIONS"]))
            $arRegion["IS_HOUSE"] = "Y";

        if ($arRegion["LOC_DEFAULT"] == "Y")
            $arResult["ITEMS"]["DEFAULT"][] = $arRegion;

        $simbol = strtoupper(mb_substr($arRegion["CITY_NAME"], 0 , 1));
        $id = Cutil::translit($simbol, "ru", array("replace_space"=>"-", "replace_other"=>"-"));

        if (!in_array($simbol, $arResult["ABC"]))
        {
            $arResult["ABC"][$id] = "$simbol";
        }

        if ($arResult["GEO_REGION_CITY_ID"] == $arRegion["ID"])
            $arRegion["SELECTED"] = "Y";

        $arResult["LIST"][] = $arRegion;
        $arResult["ITEMS"]["ABC"][$id][] = $arRegion;
    }

    asort($arResult["ABC"]);

    $this->IncludeComponentTemplate();
}
?>