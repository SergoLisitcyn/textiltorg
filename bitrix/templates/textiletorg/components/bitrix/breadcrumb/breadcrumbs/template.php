<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;


if (IS_HOME)
{
    $strReturn = '<div class="breadcrumbs">';
	$strReturn .= '
		<span class="nobr">
			<span class="b">Главная</span>
		</span>';

}
else
{
    $strReturn = '<div itemscope itemtype="http://schema.org/BreadcrumbList" class="breadcrumbs">';
	$strReturn .= '
		<span itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem" class="nobr">
			<a itemprop="item" href="/" title="Главная"><span itemprop="name">Главная</span></a>
			<meta itemprop="position" content="1">
		</span>';
}

if (Helper::IsRealFilePath(array('/catalog/index.php', '/catalog/detail/index.php')))
{
    foreach ($arResult as $nItem => $arItem)
    {
        $arItem["LINK"] = Helper::RemoveOneLavelUrl($arItem["LINK"]);
        $arResult[$nItem] = $arItem;
    }
}

if(!empty($arResult))
	$strReturn .= ' > ';

$itemSize = count($arResult);

for($index = 0; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
    $i = $index + 2;
	$nextRef = ($index < $itemSize-2 && $arResult[$index+1]["LINK"] <> ""? ' itemref="bx_breadcrumb_'.($index+1).'"' : '');
	$child = ($index > 0? ' itemprop="child"' : '');
	$arrow = ($index > 0? ' > ' : '');

	if($arResult[$index]["LINK"])
	{
		if(PAGE_FOLDER == $arResult[$index]["LINK"])
		{
			$strReturn .= "<span class='nobr last'>$arrow<span itemprop=\"name\">$title</span></span>";
		}
		else
		{
			if (!preg_match('#.*\/$#siU', $arResult[$index]["LINK"])) {
				$arResult[$index]["LINK"] = $arResult[$index]["LINK"] . '/';
			}
			$strReturn .= '
				<span itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem" class="nobr">
					'.$arrow.'
					<a itemprop="item" class="link" href="'.$arResult[$index]["LINK"].'" title="'.$title.'">
						<span itemprop="name">'.$title.'</span>
					</a>
					<meta itemprop="position" content="'.$i.'">
				</span>';
		}
	}
	else
	{
		$strReturn .= '
			<span class="nobr last">
				'.$arrow.'
				<span itemprop="name">'.$title.'</span>
			</span>';
	}
}

$strReturn .= '<div style="clear:both"></div></div>';

return $strReturn;
