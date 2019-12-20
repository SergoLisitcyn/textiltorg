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

<?if ($arResult["PRODUCTS"]):?>
    <div class="alert">
        В этой статье упоминались модели:<br>
        <?foreach ($arResult["PRODUCTS"] as $arProduct):?>
            <a style="color: white !important;text-decoration: none;" class="label label-info" href="<?=$arProduct["DETAIL_PAGE_URL"]?>" target="_blank"><?=$arProduct["NAME"]?></a><br>
        <?endforeach?>
    </div>
<?endif?>

<?//$APPLICATION->IncludeComponent(
//    "custom:comments.prototype",
//    "comments",
//    array(
//        "ELEMENT_ID" => $arResult["ID"],
//        "HBLOCK_ID" => 6,
//    ),
//    false,
//    array(
//        "HIDE_ICONS" => "Y"
//    )
//);?>