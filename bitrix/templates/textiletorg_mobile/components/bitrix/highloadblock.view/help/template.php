<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<div class="help-ajax-container">
    <?if ($arResult["fields"]["UF_PICTURE"]["VALUE"]):?>
        <div class="img-container">
           <img src="<?=CFile::GetPath($arResult["fields"]["UF_PICTURE"]["VALUE"])?>">
        </div>
    <?elseif ($arResult["fields"]["UF_FILE"]["VALUE"]):?>
        <div class="img-container">
           <img src="<?=CFile::GetPath($arResult["fields"]["UF_FILE"]["VALUE"])?>">
        </div>
    <?endif?>

    <div class="text-container">
        <h2><?=$arResult["fields"]["UF_NAME"]["VALUE"]?></h2>
        <?if ($arResult["fields"]["UF_DESCRIPTION"]["VALUE"] || $arResult["fields"]["UF_DESC"]["VALUE"]):?>
            <?if ($arResult["fields"]["UF_DESCRIPTION"]["VALUE"]):?>
                <?=$arResult["fields"]["UF_DESCRIPTION"]["VALUE"]?>
            <?else:?>
                <?=$arResult["fields"]["UF_DESC"]["VALUE"]?>
            <?endif?>
        <?endif?>
    </div>

    <a href="#close-fancybox" class="button red">Закрыть</a>
</div>