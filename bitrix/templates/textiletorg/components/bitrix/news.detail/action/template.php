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
<?=$arResult["DETAIL_TEXT"];?>
<?
if (!empty($arResult["PROPERTIES"]["SHOW_FEEDBACK"]["VALUE"])) {
	$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		array(
			"AREA_FILE_SHOW" => "sect",
			"AREA_FILE_SUFFIX" => "block_feedback",
			"COMPONENT_TEMPLATE" => ".default",
			"EDIT_TEMPLATE" => ""
		),
		$component
	);
}
?>