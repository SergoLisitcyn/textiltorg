<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
foreach ($arResult["ITEMS"] as $nItem => $arItem)
{
    if (!empty($arItem["PROPERTIES"]["TIME_SHOW"]["VALUE"]))
    {
        $arValue = ASIntervalProp::GetArrayValue($arItem["PROPERTIES"]["TIME_SHOW"]["VALUE"]);

        $isStart = (intval($arValue["START_HOUR"].$arValue["START_MIN"])<= date("Hi"));


        if (intval($arValue["END_HOUR"]) >= intval($arValue["START_HOUR"]))
        {
            $isEnd = (intval($arValue["END_HOUR"].$arValue["END_MIN"]) >= intval(date("Hi")));
        }
        else
        {
            $isEnd = (intval($arValue["END_HOUR"].$arValue["END_MIN"]) >= intval(date("Hi")));
        }

        if (!$isStart && !$isEnd)
        {
            unset($arResult["ITEMS"][$nItem]);
        }
    }
}
?>