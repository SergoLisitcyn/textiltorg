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
$isAjax = (isset($_REQUEST["ajax_action_compare"]) && $_REQUEST["ajax_action_compare"] == "Y");
$idCompareCountRight = 'compareListRigth'.$this->randString();
?>
<div id="box-compare-right">
	<?
	if ($isAjax)
		$APPLICATION->RestartBuffer();

	$frame = $this->createFrame($idCompareCountRight)->begin('');
	?>
	<?if (count($arResult)):?>
		<div class="box_block">
		    <div class="box_compare">
		        Сравнение <a href="/compare/"><?=count($arResult)?> шт.</a>
		    </div>
		</div>
	<?endif?>
	<?
	$frame->end();

	if ($isAjax)
		die();
	?>
</div>