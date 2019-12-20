<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<?
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arResult["PRICE_ID"] = (!empty($arParams["PRICE_ID"]))? $arParams["PRICE_ID"]: "1";
$arResult["PRICE_CODE"] = "CATALOG_PRICE_".$arResult["PRICE_ID"];
$sort = (!empty($_GET["sort"])) ? $_GET["sort"] : $arParams["ITEMS"][0]["DEFAULT"];

switch ($sort)
{
	case "name":
		$ELEMENT_SORT_FIELD = "name";
		$ELEMENT_SORT_FIELD2 = "sort";
		$ELEMENT_SORT_ORDER = "asc";
		$ELEMENT_SORT_ORDER2 = "desc";
		break;

	case "price":
		$ELEMENT_SORT_FIELD = $arResult["PRICE_CODE"];
		$ELEMENT_SORT_FIELD2 = "name";
		$ELEMENT_SORT_ORDER = "asc,nulls";
		$ELEMENT_SORT_ORDER2 = "asc";
		break;

	case "price-asc":
		$ELEMENT_SORT_FIELD = $arResult["PRICE_CODE"];
		$ELEMENT_SORT_FIELD2 = "name";
		$ELEMENT_SORT_ORDER = "asc,nulls";
		$ELEMENT_SORT_ORDER2 = "asc";
		break;

	case "price-desc":
		$ELEMENT_SORT_FIELD = $arResult["PRICE_CODE"];
		$ELEMENT_SORT_FIELD2 = "name";
		$ELEMENT_SORT_ORDER = "desc,nulls";
		$ELEMENT_SORT_ORDER2 = "asc";
		break;

	case "availability":
		$ELEMENT_SORT_FIELD = "CATALOG_QUANTITY";
		$ELEMENT_SORT_FIELD2 ="name";
		$ELEMENT_SORT_ORDER = "desc,nulls";
		$ELEMENT_SORT_ORDER2 = "asc";
		break;

	case "rating":
		$ELEMENT_SORT_FIELD = "PROPERTY_VOTES";
		$ELEMENT_SORT_FIELD2 ="name";
		$ELEMENT_SORT_ORDER = "desc,nulls";
		$ELEMENT_SORT_ORDER2 = "asc";
		break;

	case "rating-price":
		$ELEMENT_SORT_FIELD = "PROPERTY_VOTES";
		$ELEMENT_SORT_FIELD2 = $arResult["PRICE_CODE"];
		$ELEMENT_SORT_ORDER = "desc,nulls";
		$ELEMENT_SORT_ORDER2 = "asc,nulls";
		break;

	case "search":
		$ELEMENT_SORT_FIELD = "IBLOCK_SECTION_ID";
		$ELEMENT_SORT_FIELD2 = $arResult["PRICE_CODE"];
		$ELEMENT_SORT_ORDER = "asc,nulls";
		$ELEMENT_SORT_ORDER2 = "desc,nulls";
		break;

	default:
		$ELEMENT_SORT_FIELD = $arResult["PRICE_CODE"];
		$ELEMENT_SORT_FIELD2 = "name";
		$ELEMENT_SORT_ORDER = "asc,nulls";
		$ELEMENT_SORT_ORDER2 = "desc";
		break;
}

$GLOBALS["ELEMENT_SORT_FIELD"] = $ELEMENT_SORT_FIELD;
$GLOBALS["ELEMENT_SORT_FIELD2"] = $ELEMENT_SORT_FIELD2;
$GLOBALS["ELEMENT_SORT_ORDER"] = $ELEMENT_SORT_ORDER;
$GLOBALS["ELEMENT_SORT_ORDER2"] = $ELEMENT_SORT_ORDER2;

$arResult["ITEMS"] = $arParams["ITEMS"];

foreach ($arResult["ITEMS"] as $nItem => $arItem)
{
	$arFormatOptions = array();

	foreach ($arItem["OPTIONS"] as $value => $title)
	{
		$arFormatOptions[] = array(
			"VALUE" => $value,
			"TITLE" => $title,
			"SELECTED" => (($_GET[$arItem["CODE"]] == $value) || (empty($_GET[$arItem["CODE"]]) && $arItem["DEFAULT"] == $value))? "Y" : "N"
		);
	}

	$arResult["ITEMS"][$nItem]["OPTIONS"] = $arFormatOptions;
}

$GLOBALS["PAGE_ELEMENT_COUNT"] = (intval($_GET["page-count"])) ? intval($_GET["page-count"]): "16";

$this->IncludeComponentTemplate();
?>