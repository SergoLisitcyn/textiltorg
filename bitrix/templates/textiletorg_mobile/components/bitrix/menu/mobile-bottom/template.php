<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
    <div class="menu_block">
        <ul class="footer_slick">
            <?
            foreach($arResult as $arItem):
                if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
                    continue;
            ?>
                <?if($arItem["SELECTED"]):?>
                    <li class="footer_slick_item">
                        <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
                    </li>
                <?else:?>
                    <li class="footer_slick_item selected">
                        <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
                    </li>
                <?endif?>
            <?endforeach?>
        </ul>
    </div>
<?endif?>