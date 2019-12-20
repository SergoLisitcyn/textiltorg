<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (count($arResult["PHOTOGALLERY"]) > 0)
{ ?>
    <div class="textiletorg-contacts-prototype-default">
        <div class="item">
            <div class="clear"></div>
            <ul class="photogallery">
                <? foreach ($arResult["PHOTOGALLERY"] as $arPhoto): ?>
                    <li><a href="<?=$arPhoto["BIG"];?>" class="fancybox" rel="gallery"><img alt="" src="<?=$arPhoto["SMALL"];?>" /></a></li>
                <? endforeach; ?>
            </ul>
        </div>
    </div>
<? }?>
