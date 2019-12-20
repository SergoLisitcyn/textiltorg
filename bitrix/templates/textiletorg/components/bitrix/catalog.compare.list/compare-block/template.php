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
$isAjax = (isset($_REQUEST["ajax_action_compare_block"]) && $_REQUEST["ajax_action_compare_block"] == "Y");
$idCompareCountRight = 'compareListBlock'.$this->randString();
?>
<div id="box-compare-block">
	<?
    $count = count($arResult);
	if ($isAjax)
		$APPLICATION->RestartBuffer();

	$frame = $this->createFrame($idCompareCountRight)->begin('');
	?>
	<?if ($count):?>
		<div id="static-block" class="static-selector">
			<div class="inner-block">
				<ul class="static-hr">
					<li><a href="/compare/?DELETE_FROM_COMPARE_LIST_ALL=Y" id="clear-all-compare">Очистить</a></li>
					<li>Добавлены к сравнению: <span id="compare_count"><?=$count?></span> шт.</li>
                    <?if ($count > 1):?>
					<li><a href="/compare/" id="compare_link">Сравнить</a></li>
                    <?endif?>
				</ul>
			</div>
		</div>
	<?endif?>
	<?
	$frame->end();

	if ($isAjax)
		die();
	?>

</div>