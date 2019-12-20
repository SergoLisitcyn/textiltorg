<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}
?>
<div class="system-page-navigation-modern-ajax">
	<div class="more__info"> Весь товар абсолютно новый, цена включает<br> в себя все налоги и доставку в любую точку РФ. </div>    
    <div class="loader"></div>
    <?if ($arResult["NavPageCount"] > $arResult["NavPageNomer"]) { ?>
        <div class="show-more5" data-href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_1<?//=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>">
            <div class="plus"></div>
            Показать еще
        </div>
    <?}?>
</div>