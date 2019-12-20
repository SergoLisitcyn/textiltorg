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
<div class="slider">
	<div class="flexslider">
    	<ul class="slides">
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => "Удалить слайд"));
				?>

				<li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					<?if ($arItem["PROPERTIES"]["URL"]["VALUE"]):?>
	                    <a href="<?=$arItem["PROPERTIES"]["URL"]["VALUE"]?>">
	                        <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="Фото <?=$arItem["PREVIEW_PICTURE"]["DESCRIPTION"]?> | Швейный магазин Текстильторг" title="Фото <?=$arItem["PREVIEW_PICTURE"]["DESCRIPTION"]?> | Швейный магазин Текстильторг" />
	                    </a>
	                <?else:?>
						<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="Фото <?=$arItem["PREVIEW_PICTURE"]["DESCRIPTION"]?> | Швейный магазин Текстильторг" title="Фото <?=$arItem["PREVIEW_PICTURE"]["DESCRIPTION"]?> | Швейный магазин Текстильторг" />
	                <?endif;?>
                </li>

			<?endforeach;?>
		</ul>
	</div>
</div>