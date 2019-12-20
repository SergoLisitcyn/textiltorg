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
<ul class="konkurs-list">
<?foreach($arResult["ITEMS_ACTIVE"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => "Удалить"));
	?>
	<li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <div class="konkurs-img"><img alt="" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"></div>
        <div class="konkurs-info">
            <div class="konkurs-name"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></div>
            <div class="konkurs-text">
                <?if ($arItem["DATES"]):?>
                    <p><strong><?=$arItem["DATES"]?></strong></p>
                <?endif?>
                <?=$arItem["PREVIEW_TEXT"]?>
            </div>
            <div class="konkurs-link">
                <?if ($arItem["PROPERTIES"]["REPORT_TEXT"]["VALUE"]):?>
                    <a class="button" href="<?=$arItem["DETAIL_PAGE_URL"]?>#report">Отчет о проведении</a>
                <?endif?>
                <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">Подробнее</a>
            </div>
        </div>
        <div class="clear"></div>
    </li>
<?endforeach;?>
</ul>

<?if ($arResult["ITEMS_ARCHIVE"]):?>
    <div class="pagi ajax ajax-pagination">
        <div class="loader"></div>
        <div class="show-more">
            <div class="plus"></div>
            Показать еще
        </div>
    </div>
<?endif?>

<ul class="konkurs-list archive">
<?foreach($arResult["ITEMS_ARCHIVE"] as $arItem):?>
    <?
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => "Удалить"));
    ?>
    <li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <div class="konkurs-img"><img alt="" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"></div>
        <div class="konkurs-info">
            <div class="konkurs-name"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></div>
            <div class="konkurs-text">
                <?if ($arItem["DATES"]):?>
                    <p><strong><?=$arItem["DATES"]?></strong></p>
                <?endif?>
                <?=$arItem["PREVIEW_TEXT"]?>
            </div>
            <div class="konkurs-link">
                <?if ($arItem["PROPERTIES"]["REPORT_TEXT"]["VALUE"]):?>
                    <a class="button" href="<?=$arItem["DETAIL_PAGE_URL"]?>#report">Отчет о проведении</a>
                <?endif?>
                <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">Подробнее</a>
            </div>
        </div>
        <div class="clear"></div>
    </li>
<?endforeach;?>
</ul>