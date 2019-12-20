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

<?foreach($arResult["SECTIONS"] as $arSection):?>
    <h3 style="color: #F97513; font-size: 14px"><?=$arSection["NAME"]?>:</h3>
    <p>
        <?foreach($arSection["ITEMS"] as $arItem):?>
            <a class="ins" href="<?=$arItem["FILE_PATH"]?>" target="_blank"><?=$arItem["NAME"]?></a><br>
        <?endforeach?>
        <br>
    </p>
<?endforeach?>