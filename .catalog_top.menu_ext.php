<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;
$aMenuLinksExt = $APPLICATION->IncludeComponent(
	"custom:menu.sections", "",
	array(
		"ID" => $_REQUEST["SECTION_ID"],
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "8",
		"ACTIVE" => "N",
		"SECTION_URL" => "",
		"DEPTH_LEVEL" => "2",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
	),
	false,
	array(
        "HIDE_ICONS" => "Y"
    )
);

foreach ($aMenuLinksExt as $key => $arItem)
{
	if (intval($arItem[3]["DEPTH_LEVEL"]) > 1)
	{
		$url = Helper::RemoveOneLavelUrl($arItem[1]);
		$arItem[1] = $url;
		$arItem[2] = array($url);
	}

	$aMenuLinksExt[$key] = $arItem;
}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>