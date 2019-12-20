<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>

<?if ($arResult["fields"]["UF_DESCRIPTION"]["VALUE"] || $arResult["fields"]["UF_DESC"]["VALUE"]):?>
	<div class="tpl_div_body"> 
            <? if ($arResult["fields"]["UF_PICTURE"]["VALUE"]):?>
                <img alt="" src="<?=CFile::GetPath($arResult["fields"]["UF_PICTURE"]["VALUE"])?>" class="tpl_img_red"><br />
            <?elseif ($arResult["fields"]["UF_FILE"]["VALUE"]):?>
                <img alt="" src="<?=CFile::GetPath($arResult["fields"]["UF_FILE"]["VALUE"])?>" class="tpl_img_red"><br />
            <?endif?>
                
            <h1 class="tpl_h1" style="color: #000; padding: 15px 0 0 5px;"><?=$arResult["fields"]["UF_NAME"]["VALUE"]?></h1>
            
	    <description>
            <?if ($arResult["fields"]["UF_DESCRIPTION"]["VALUE"]):?>
                <?=$arResult["fields"]["UF_DESCRIPTION"]["VALUE"]?>
            <?else:?>
                <?=$arResult["fields"]["UF_DESC"]["VALUE"]?>
            <?endif?>
        </description>
	</div>
<?endif?>

<div style="text-align:center"><a href="javascript:" onclick="window.close();" class="tpl_aclose">Закрыть</a></div>
