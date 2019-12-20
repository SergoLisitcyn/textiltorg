<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

$strReturn = '<div class="breadcrumbs">';//<span onclick="history.back(); return false;" class="head_breadcrumbs_back"></span>

if (IS_HOME)
{
	$strReturn .= '
		<nobr>
			<b></b>
		</nobr>';

}
else
{
	$strReturn .= '
		<nobr>
			<a href="/" title="Главная" class="head_breadcrumbs_back">Главная</a>
		</nobr>';
}

if (Helper::IsRealFilePath(array('/catalog/index.php', '/catalog/detail/index.php')))
{
    foreach ($arResult as $nItem => $arItem)
    {
        $arItem["LINK"] = Helper::RemoveOneLavelUrl($arItem["LINK"]);
        $arResult[$nItem] = $arItem;
    }
}
/*
if(!empty($arResult))
	$strReturn .= ' > ';*/

$itemSize = count($arResult);
for($index = 0; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);

	$nextRef = ($index < $itemSize-2 && $arResult[$index+1]["LINK"] <> ""? ' itemref="bx_breadcrumb_'.($index+1).'"' : '');
	$child = ($index > 0? ' itemprop="child"' : '');
	$arrow = ' > ';//($index > 0? ' > ' : '');

	if($arResult[$index]["LINK"])
	{
		$strReturn .= '
			<nobr>
				'.$arrow.'
				<a href="'.$arResult[$index]["LINK"].'" title="'.$title.'">
					'.$title.'
				</a>
			</nobr>';
	}
	else
	{
		$strReturn .= '
			<nobr>
				'.$arrow.'
				<span>'.$title.'</span>
			</nobr>';
	}
}

$strReturn .= '</div>';

return $strReturn;
