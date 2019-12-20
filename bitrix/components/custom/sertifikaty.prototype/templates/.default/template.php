<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (count($arParams["IMAGES"]) > 0)
{ ?>
    <ul class="photogallery">
        <? foreach ($arParams["IMAGES"] as $arPhoto): ?>
            <li><a href="<?=$arPhoto;?>" class="fancybox" rel="gallery"><img alt="" src="<?=$arPhoto;?>" width="300" height="225" /></a></li>
        <? endforeach; ?>
    </ul>
<? }?>
