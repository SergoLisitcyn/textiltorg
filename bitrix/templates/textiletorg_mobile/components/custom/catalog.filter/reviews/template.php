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

<div class="halfcirclebox">
	<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get">
		<?foreach($arResult["ITEMS"] as $arItem):
			if(array_key_exists("HIDDEN", $arItem)):
				echo $arItem["INPUT"];
			endif;
		endforeach;?>

		Фильтровать по&nbsp;&nbsp;<b>категории</b>&nbsp;&nbsp;<?=$arResult["ITEMS"]["SECTION_ID"]["INPUT"]?>,
		по&nbsp;&nbsp;<b>бренду</b>&nbsp;&nbsp;<?=$arResult["ITEMS"]["PROPERTY_3"]["INPUT"]?>

		<button type="submit" class="button" name="set_filter" value="Фильтровать" />Фильтровать</button>
		<input type="hidden" name="set_filter" value="Y" />
	</form>
</div>