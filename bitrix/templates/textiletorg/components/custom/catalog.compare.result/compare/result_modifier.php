<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @global array $arParams */
use Bitrix\Main\Type\Collection;
use \Bitrix\Main\Loader;
use \Ayers\Delivery\CalcPrice;


$arParams['TEMPLATE_THEME'] = (string)($arParams['TEMPLATE_THEME']);
if ($arParams['TEMPLATE_THEME'] != '')
{
	$arParams['TEMPLATE_THEME'] = preg_replace('/[^a-zA-Z0-9_\-\(\)\!]/', '', $arParams['TEMPLATE_THEME']);
	if ($arParams['TEMPLATE_THEME'] == 'site')
	{
		$templateId = COption::GetOptionString("main", "wizard_template_id", "eshop_bootstrap", SITE_ID);
		$templateId = (preg_match("/^eshop_adapt/", $templateId)) ? "eshop_adapt" : $templateId;
		$arParams['TEMPLATE_THEME'] = COption::GetOptionString('main', 'wizard_'.$templateId.'_theme_id', 'blue', SITE_ID);
	}
	if ($arParams['TEMPLATE_THEME'] != '')
	{
		if (!is_file($_SERVER['DOCUMENT_ROOT'].$this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css'))
			$arParams['TEMPLATE_THEME'] = '';
	}
}
if ($arParams['TEMPLATE_THEME'] == '')
	$arParams['TEMPLATE_THEME'] = 'blue';

$arResult['ALL_FIELDS'] = array();
$existShow = !empty($arResult['SHOW_FIELDS']);
$existDelete = !empty($arResult['DELETED_FIELDS']);
if ($existShow || $existDelete)
{
	if ($existShow)
	{
		foreach ($arResult['SHOW_FIELDS'] as $propCode)
		{
			$arResult['SHOW_FIELDS'][$propCode] = array(
				'CODE' => $propCode,
				'IS_DELETED' => 'N',
				'ACTION_LINK' => str_replace('#CODE#', $propCode, $arResult['~DELETE_FEATURE_FIELD_TEMPLATE']),
				'SORT' => $arResult['FIELDS_SORT'][$propCode]
			);
		}
		unset($propCode);
		$arResult['ALL_FIELDS'] = $arResult['SHOW_FIELDS'];
	}
	if ($existDelete)
	{
		foreach ($arResult['DELETED_FIELDS'] as $propCode)
		{
			$arResult['ALL_FIELDS'][$propCode] = array(
				'CODE' => $propCode,
				'IS_DELETED' => 'Y',
				'ACTION_LINK' => str_replace('#CODE#', $propCode, $arResult['~ADD_FEATURE_FIELD_TEMPLATE']),
				'SORT' => $arResult['FIELDS_SORT'][$propCode]
			);
		}
		unset($propCode, $arResult['DELETED_FIELDS']);
	}
	Collection::sortByColumn($arResult['ALL_FIELDS'], array('SORT' => SORT_ASC));
}

$arResult['ALL_PROPERTIES'] = array();
$existShow = !empty($arResult['SHOW_PROPERTIES']);
$existDelete = !empty($arResult['DELETED_PROPERTIES']);
if ($existShow || $existDelete)
{
	if ($existShow)
	{
		foreach ($arResult['SHOW_PROPERTIES'] as $propCode => $arProp)
		{
			$arResult['SHOW_PROPERTIES'][$propCode]['IS_DELETED'] = 'N';
			$arResult['SHOW_PROPERTIES'][$propCode]['ACTION_LINK'] = str_replace('#CODE#', $propCode, $arResult['~DELETE_FEATURE_PROPERTY_TEMPLATE']);
		}
		$arResult['ALL_PROPERTIES'] = $arResult['SHOW_PROPERTIES'];
	}
	unset($arProp, $propCode);
	if ($existDelete)
	{
		foreach ($arResult['DELETED_PROPERTIES'] as $propCode => $arProp)
		{
			$arResult['DELETED_PROPERTIES'][$propCode]['IS_DELETED'] = 'Y';
			$arResult['DELETED_PROPERTIES'][$propCode]['ACTION_LINK'] = str_replace('#CODE#', $propCode, $arResult['~ADD_FEATURE_PROPERTY_TEMPLATE']);
			$arResult['ALL_PROPERTIES'][$propCode] = $arResult['DELETED_PROPERTIES'][$propCode];
		}
		unset($arProp, $propCode, $arResult['DELETED_PROPERTIES']);
	}
	Collection::sortByColumn($arResult["ALL_PROPERTIES"], array('SORT' => SORT_ASC, 'ID' => SORT_ASC));
}

$arResult["ALL_OFFER_FIELDS"] = array();
$existShow = !empty($arResult["SHOW_OFFER_FIELDS"]);
$existDelete = !empty($arResult["DELETED_OFFER_FIELDS"]);
if ($existShow || $existDelete)
{
	if ($existShow)
	{
		foreach ($arResult["SHOW_OFFER_FIELDS"] as $propCode)
		{
			$arResult["SHOW_OFFER_FIELDS"][$propCode] = array(
				"CODE" => $propCode,
				"IS_DELETED" => "N",
				"ACTION_LINK" => str_replace('#CODE#', $propCode, $arResult['~DELETE_FEATURE_OF_FIELD_TEMPLATE']),
				'SORT' => $arResult['FIELDS_SORT'][$propCode]
			);
		}
		unset($propCode);
		$arResult['ALL_OFFER_FIELDS'] = $arResult['SHOW_OFFER_FIELDS'];
	}
	if ($existDelete)
	{
		foreach ($arResult['DELETED_OFFER_FIELDS'] as $propCode)
		{
			$arResult['ALL_OFFER_FIELDS'][$propCode] = array(
				"CODE" => $propCode,
				"IS_DELETED" => "Y",
				"ACTION_LINK" => str_replace('#CODE#', $propCode, $arResult['~ADD_FEATURE_OF_FIELD_TEMPLATE']),
				'SORT' => $arResult['FIELDS_SORT'][$propCode]
			);
		}
		unset($propCode, $arResult['DELETED_OFFER_FIELDS']);
	}
	Collection::sortByColumn($arResult['ALL_OFFER_FIELDS'], array('SORT' => SORT_ASC));
}

$arResult['ALL_OFFER_PROPERTIES'] = array();
$existShow = !empty($arResult["SHOW_OFFER_PROPERTIES"]);
$existDelete = !empty($arResult["DELETED_OFFER_PROPERTIES"]);
if ($existShow || $existDelete)
{
	if ($existShow)
	{
		foreach ($arResult['SHOW_OFFER_PROPERTIES'] as $propCode => $arProp)
		{
			$arResult["SHOW_OFFER_PROPERTIES"][$propCode]["IS_DELETED"] = "N";
			$arResult["SHOW_OFFER_PROPERTIES"][$propCode]["ACTION_LINK"] = str_replace('#CODE#', $propCode, $arResult['~DELETE_FEATURE_OF_PROPERTY_TEMPLATE']);
		}
		unset($arProp, $propCode);
		$arResult['ALL_OFFER_PROPERTIES'] = $arResult['SHOW_OFFER_PROPERTIES'];
	}
	if ($existDelete)
	{
		foreach ($arResult['DELETED_OFFER_PROPERTIES'] as $propCode => $arProp)
		{
			$arResult["DELETED_OFFER_PROPERTIES"][$propCode]["IS_DELETED"] = "Y";
			$arResult["DELETED_OFFER_PROPERTIES"][$propCode]["ACTION_LINK"] = str_replace('#CODE#', $propCode, $arResult['~ADD_FEATURE_OF_PROPERTY_TEMPLATE']);
			$arResult['ALL_OFFER_PROPERTIES'][$propCode] = $arResult["DELETED_OFFER_PROPERTIES"][$propCode];
		}
		unset($arProp, $propCode, $arResult['DELETED_OFFER_PROPERTIES']);
	}
	Collection::sortByColumn($arResult['ALL_OFFER_PROPERTIES'], array('SORT' => SORT_ASC, 'ID' => SORT_ASC));
}


foreach ($arResult["ITEMS"] as $nItem => $arItem)
{
	$arItem["REGION_PRICE"] = ($arItem["PRICES"][$arParams["GEO_REGION_CITY_NAME"]]) ?
		$arItem["PRICES"][$arParams["GEO_REGION_CITY_NAME"]] :
		$arItem["PRICES"][$arParams["REGION_PRICE_CODE_DEFAULT"]];

	if (isset($arItem["REGION_PRICE"]))
	{
		$arItem["REGION_PRICE"]["DEFAULT_VALUE"] = $arItem["REGION_PRICE"]["DISCOUNT_VALUE"];
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

	// url
    if ($arItem["DETAIL_PAGE_URL"]) {
        $arItem["DETAIL_PAGE_URL"] = Helper::RemoveOneLavelUrl($arItem["DETAIL_PAGE_URL"]);
    }

	$arResult["ITEMS"][$nItem] = $arItem;
}

$allCompare = $_SESSION[$arParams['NAME']][$arParams['IBLOCK_ID']]['ITEMS'];

foreach ($arResult["ITEMS"] as $key => $item) {
    if ($item['ID'] !== $item['~ID']) {
        $item['NAME'] = $allCompare[$item['ID']]['PROPERTY_VENDOR_CODE_VALUE'];
        $item['PICTURE'] = $allCompare[$item['ID']]["RESIZE_PICTURE"]["SRC"];
        $offersByItem[$item['~ID']][] = $item;
        if (
            $itemPrise[$item['~ID']]['DISCOUNT_VALUE'] > $item['REGION_PRICE']['DISCOUNT_VALUE']
            || empty($itemPrise[$item['~ID']]['DISCOUNT_VALUE'])
        ) {
            $itemPrise[$item['~ID']] = $item['REGION_PRICE'];
        }
        unset($arResult["ITEMS"][$key]);
        unset($_SESSION[$arParams['NAME']][$arParams['IBLOCK_ID']]['ITEMS'][$item['ID']]);
    }
}

$arResult['itemPrise'] = $itemPrise;
$arResult['offersByItem'] = $offersByItem;





