<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arCenterMenuData = array();
foreach ($arResult["SECTIONS"] as $nSection => $arSection)
{
	$arResult["SECTIONS"][$nSection]["SECTION_PAGE_URL"] = preg_replace("/^(\/[-_a-z]+)\/[-_a-z0-9]+(\/[-_a-z0-9]+)(.+)/i", "$1$2$3", $arSection["SECTION_PAGE_URL"]);
}
foreach ($arResult["SECTIONS"] as $nSection => $arSection)
{
    if ($arSection["IBLOCK_SECTION_ID"] && $arSection["PICTURE"]["SRC"])
    {
        $arCenterMenuData[$arSection["IBLOCK_SECTION_ID"]][] = array(
            "url" => $arSection["SECTION_PAGE_URL"],
            "img" => $arSection["PICTURE"]["SRC"],
            "caption" => $arSection["NAME"]
        );
    }

    $arResult["SECTIONS"][$nSection] = $arSection;
}

foreach ($arCenterMenuData as $idSection => $arCenterMenuDataItem)
{
    foreach ($arResult["SECTIONS"] as $nSection => $arSection)
    {
        if ($arSection["ID"] == $idSection)
        {
            $arSection["IS_PARENT"] = true;
            $arResult["SECTIONS"][$nSection] = $arSection;
            break;
        }
    }
}

$arResult["CENTER_MENU_DATA"] = '<script type="text/javascript">var center_menu_data = '.json_encode($arCenterMenuData).';</script>';
?>