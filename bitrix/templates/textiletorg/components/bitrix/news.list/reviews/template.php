<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div class="allarticles">

<?foreach($arResult["ITEMS"] as $arItem):?>
    <?
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => "Удалить"));
    ?>
    <div class="article" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <?if ($arItem["PREVIEW_PICTURE"]["SRC"]):?>
            <div class="image">
                <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
                    <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" title="<?=$arItem["NAME"]?>" alt="<?=$arItem["NAME"]?>">
                </a>
            </div>
        <?endif?>
        <div class="right" style="padding-left: 30px;padding-top: 25px;">
            <div class="name"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="title"><?=$arItem["NAME"]?></a></div>
                <div class="anno"><?=$arItem["PREVIEW_TEXT"]?></div>
                <p><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="more">Подробнее</a></p>
        </div>
        <div class="clear"></div>
    </div>
<?endforeach;?>
</div>
<div class="pagination">
    <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
        <?=$arResult["NAV_STRING"]?>
    <?endif;?>
</div>