<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!--noindex-->
<form class="sort" id="catalog-sort" method="get">
    <div class="sorter">
        <?foreach ($arResult["ITEMS"] as $arItem):?>
            <div class="sorter_block">
                <div class="sorter_name"><?=$arItem["TITLE"]?>:</div>

                <div id="<?=$arItem["CODE"]?>" class="sorter_value_fon">
                    <select class="sorter_value" name="<?=$arItem["CODE"]?>">
                        <?foreach ($arItem["OPTIONS"] as $arOption):?>
                            <?if ($arOption["SELECTED"] == "Y"):?>
                                <option value="<?=$arOption["VALUE"]?>" selected><?=$arOption["TITLE"]?></option>
                            <?else:?>
                                <option value="<?=$arOption["VALUE"]?>"><?=$arOption["TITLE"]?></option>
                            <?endif?>
                        <?endforeach?>
                    </select>
                </div>
            </div>
        <?endforeach?>

        <div class="gridlist">
            <span class="tt-icons grid-btn"></span>
            <span class="tt-icons list-btn activ"></span>
        </div>
    </div>
</form>
<!--/noindex-->
<div class="clear"></div>