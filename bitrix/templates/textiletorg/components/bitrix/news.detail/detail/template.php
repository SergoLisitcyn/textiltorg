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
<?if ($arResult["PROPERTIES"]["REPORT_TEXT"]["VALUE"]["TEXT"]):?>
    <div id="report">
        <h2>Отчет о проведении конкурса</h2>
        <?=$arResult["PROPERTIES"]["REPORT_TEXT"]["VALUE"]["TEXT"]?>
    </div>
<?endif?>