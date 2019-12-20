<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<div class="bg info-menu">
    <ul>
    <? foreach($arResult as $arItem):
        if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
            continue;

        if($arItem["SELECTED"] && PAGE_FOLDER == $arItem["LINK"]):?>
            <li><span><?=$arItem["TEXT"]?></span></li>
        <?else:?>
            <li><a href="<?=$arItem["LINK"]?>" class="nav-menu__item"><?=$arItem["TEXT"]?></a></li>
        <?endif?>
    <?endforeach?>
    </ul>
</div>
<?endif?>