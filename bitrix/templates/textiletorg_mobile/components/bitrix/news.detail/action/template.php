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
    <div class="action-products-container">
        <p><strong>Данное предложение действует только на следующие товары:</strong></p>

        <div class="action-products">
            <?foreach ($arResult["PRODUCTS"] as $arProduct):?>
                <div class="action-products-item">
                    <img src="<?=$arProduct["RESIZE_PICTURE"]["SRC"]?>" width="<?=$arProduct["RESIZE_PICTURE"]["WIDTH"]?>" height="<?=$arProduct["RESIZE_PICTURE"]["HEIGHT"]?>">
                    <a href="<?=$arProduct["DETAIL_PAGE_URL"]?>" class="ar-black" target="_blank"><?=$arProduct["NAME"]?> - <span class="big_red"><?=$arProduct["PRINT_PRICE"]?></span><span class="red"> руб.</span></a>
                </div>
            <?endforeach?>
        </div>
    </div>
<?endif?>