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
<div class="stat">
	<div class="allarticles">
		<?foreach($arResult["ITEMS"] as $arItem):?>
<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
?>
		<div id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="article">
			<div class="image">
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" border="0" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>" title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"></a>
			</div>
			<div class="right">
				<div class="name"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></div>
				<div class="anno"><?=$arItem["PREVIEW_TEXT"]?></div>
				<div class="date">
					<a class="more" href="<?=$arItem["DETAIL_PAGE_URL"]?>">Подробнее &gt;&gt;</a>
				</div>
			</div>
			<div class="clear"></div>
		</div>
<?
		endforeach;
?>
	</div>
</div>

