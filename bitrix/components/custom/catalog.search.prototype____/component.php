<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

dumpLog($arParams);
?>

<?

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arResult["FILTER_NAME"] = (!empty($arParams["FILTER_NAME"]))? trim($arParams["FILTER_NAME"]): false;
$arResult["GEO_REGION_CITY_NAME"] = (!empty($arParams["GEO_REGION_CITY_NAME"]))? trim($arParams["GEO_REGION_CITY_NAME"]): "";
$arResult["PRICE_ID"] = (!empty($arParams["PRICE_ID"]))? $arParams["PRICE_ID"]: "1";

global $APPLICATION;

$arResult["ACTION_FORM"] = (!empty($arParams["ACTION_FORM"]))? $arParams["ACTION_FORM"] : $APPLICATION->GetCurPage();
$arResult["QUERY"] = (!empty($_REQUEST["QUERY"]))? htmlspecialchars($_REQUEST["QUERY"], ENT_QUOTES) : null;
$arResult["ITEMS"] = array();

if($_REQUEST["AJAX_SEARCH"] == "Y" && !empty($arResult["QUERY"]) && strlen($arResult["QUERY"]) >= 3)
{
    
	$arWord = explode(" ", trim($arResult["QUERY"]));
    $filterName = array("LOGIC" => "AND");
    foreach ($arWord as $wordName) {
        $filterName[] = array("%NAME" => trim($wordName));
    }
	
    if(SITE_ID === "tp"){
    	$arFilter = array(
    			"IBLOCK_ID" => intval($arParams["IBLOCK_ID"]),
    			"ACTIVE" => "Y",
    			"ACTIVE_DATE" => "Y",
    			$filterName
    	);
    } else {
    	$arFilter = array(
    			"IBLOCK_ID" => intval($arParams["IBLOCK_ID"]),
    			"ACTIVE" => "Y",
    			"ACTIVE_DATE" => "Y",
    			array(
    					"LOGIC" => "OR",
    					array("PROPERTY_VENDOR_CODE" => $_REQUEST["QUERY"]),
    					array($filterName)
    			)
    	);
    }

	if (SITE_ID == "s1") {
		$arFilter["PROPERTY_VIEW_SITE_RU_VALUE"] = "Да";
	} elseif(SITE_ID === "tp"){

	}else {
		$arFilter["CATALOG_CURRENCY_11"] = "BYN";
		$arFilter["PROPERTY_VIEW_SITE_RB_VALUE"] = "Да";
	}

	if ($arResult["FILTER_NAME"])
	{
		if (!empty($GLOBALS[$arResult["FILTER_NAME"]]) && is_array($GLOBALS[$arResult["FILTER_NAME"]]))
		{
			$arFilter = array_merge($arFilter, $GLOBALS[$arResult["FILTER_NAME"]]);
		}
	}

	$rsElements = CIBlockElement::GetList(
		array("CATALOG_PRICE_1" => "DESC"),
		$arFilter,
		false,
		array("nPageSize" => 10),
		array("*", "PROPERTY_VENDOR_CODE","CATALOG_PRICE_1", "CATALOG_GROUP_".$arResult["PRICE_ID"], "CATALOG_QUANTITY")
	);

	$number = 0;
	while($arElement = $rsElements->GetNext())
	{
		$arElement["NUMBER"] = $number + 1;
		$arElement["PREVIEW_TEXT"] = Helper::Truncate($arElement["PREVIEW_TEXT"], 150);


       	$arElement["TITLE_FORMATED"] = $arElement["NAME"];
        foreach ($arWord as $word) {
        	$arElement["TITLE_FORMATED"] = Helper::WordWrap($word, $arElement["TITLE_FORMATED"]);
        }

		$arElement["BODY_FORMATED"] = $arElement["PREVIEW_TEXT"];

		$arElement["DETAIL_PAGE_URL"] =  Helper::RemoveOneLavelUrl($arElement["DETAIL_PAGE_URL"]);

		//resize picture
		$arPictures = array(
			$arElement["PREVIEW_PICTURE"],
			$arElement["DETAIL_PICTURE"]
		);

		$arElement["RESIZE_PICTURE"] = Helper::Resize($arPictures, 65, 65, true);
		if ($arElement["RESIZE_PICTURE"] == NULL)
			$arElement["RESIZE_PICTURE"]["SRC"] = SITE_TEMPLATE_PATH."/img/noimage-65x65.jpg";

		if(CCatalogSku::IsExistOffers($arElement["ID"])   )
        {
            $arOffers = CIBlockPriceTools::GetOffersArray(
                array(
                    'IBLOCK_ID' => $arElement['IBLOCK_ID'],
                    'HIDE_NOT_AVAILABLE' => 'Y',
                    'CHECK_PERMISSIONS' => 'Y'
                ),
                array($arElement['ID'])
            );

            $arOffersId = array();
            foreach($arOffers as $arOffer)
            {
                $arOffersId[] = $arOffer['ID'];
            }

            $rsOfferElements = CIBlockElement::GetList(
                array(),
                array(
                    'ID' => $arOffersId
                ),
                false,
                array(),
                array("*", "CATALOG_GROUP_1", "CATALOG_GROUP_".$arResult["PRICE_ID"], "CATALOG_QUANTITY")
            );

            $minPrice = 0;
            while($arOfferElement = $rsOfferElements->GetNext())
            {
                $price = ($arOfferElement["CATALOG_PRICE_".$arResult["PRICE_ID"]])? $arOfferElement["CATALOG_PRICE_".$arResult["PRICE_ID"]]: $arOfferElement["CATALOG_PRICE_1"] ;
                if (empty($minPrice) || $price < $minPrice)
                {
                    $minPrice = $price;
                }
            }

            $price = number_format($minPrice, 0, ".", " ");
        }
        else
        {
        	$price = number_format($arElement["CATALOG_PRICE_".$arResult["PRICE_ID"]], 0, ".", " ");
        }

		if (\Bitrix\Main\Loader::includeModule('ayers.delivery'))
		{
			$isInShops = \Ayers\Delivery\CalcPrice::IsInShops();

			if (!$isInShops && SITE_ID == 's1')
		    {
		        $optimalCompany = \Ayers\Delivery\CalcPrice::GetOptimalCompany4City($arResult["GEO_REGION_CITY_NAME"]);

		        $arOptimalDelivery4Items = \Ayers\Delivery\CalcPrice::GetOptimalDelivery4Items(
		            $optimalCompany,
		            $arResult["GEO_REGION_CITY_NAME"],
		            $price,
		            array($arElement)
		        );

		        if (!empty($arOptimalDelivery4Items) && $arOptimalDelivery4Items['PRICE']['FORMAT'] != $arResult["REGION_PRICE"]["DISCOUNT_VALUE"])
		        {
		            $price = $arOptimalDelivery4Items['PRICE']['FORMAT'];
		        }
		    }
		}

		$arElement["PRINT_PRICE"] = $price;

		$arResult["ITEMS"][] = $arElement;
	}
}

// Отсортируем по цене
usort($arResult["ITEMS"], function($a, $b){
    $a = str_replace(" ", "", $a);
    $b = str_replace(" ", "", $b);

    if($a["PRINT_PRICE"]>$b["PRINT_PRICE"]) return 1;
    if($a["PRINT_PRICE"]==$b["PRINT_PRICE"]) return 0;
    if($a["PRINT_PRICE"]<$b["PRINT_PRICE"]) return -1;
});

$this->IncludeComponentTemplate();
?>