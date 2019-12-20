<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
<?if ($arResult["ITEMS"]):?>
	<?$i = 0?>
	<?foreach ($arResult["ITEMS"] as $arItem):?>
		<?$i++?>
		<?
		$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => "Будет удалена вся информация, связанная с этой записью. Продолжить?"));
		?>						
		<?if($i == 1){?>
		  <div class="products">
		<?}?>
			<div class="col">
			  <div class="product" id="<?=$this->GetEditAreaId($arItem["ID"])?>">
				<div class="product__title"><?=$arItem["NAME"]?></div>
				<div class="product__article"> Артикул
				  <b><?=$arItem["PROPERTIES"]["VENDOR_CODE"]["VALUE"]?></b>
				</div>
				<div class="product__image">
				  <img src="<?=$arItem["RESIZE_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>"> </div>
				<div class="product__oldprice">
					<?if($arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]) {?> Цена:
					  <span class="product__num"><?=$arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]?>
						<span>руб.</span>
					  </span>
					<?}?>
				</div>
				<div class="product__price"> <?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"] ? "<span class=\"price_num\">".$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]."</span><span class=\"redprice\">руб.</span>" : "На заказ"?>
				</div>
				<div class="product__button">
				  <button href="<?=$arItem["ADD_URL"]?>" data-id="<?=$arItem["ID"]?>" data-path="<?=$arItem["ADD_URL"]?>" onclick="yaCounter1021532.reachGoal('Open_Cart')" type="button" name="button" class="button9 button--basket add-cart">В корзину</button>
				</div>
				<div class="product__profit">
					<?if($arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]) {?> Ваша выгода <?=!empty($arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]) ? ($arItem["REGION_PRICE"]["DISCOUNT_VALUE"] - $arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]) : ""?> руб. 
					<?}?>
				</div>				
				<div class="product__hurry"><?if($arItem["CATALOG_QUANTITY"]) {?> Торопитесь! Осталось <?=intval($arItem["CATALOG_QUANTITY"])?> шт.! <?}?></div>
				<div class="product__bonus">
					<?if($arItem["PROPERTIES"]["PAYMENT_INSTALLMENTS"]["VALUE_XML_ID"] == "Y"){?>
					  <div class="product__line product__procent">
						<span>Рассрочка 0%</span>
					  </div>
					<?}?>
					<?if(!empty($arItem["CREDIT"])){?>
					  <div class="product__line product__credit">
						<span>Можно в кредит</span>
					  </div>
					<?}?>
					  <div class="product__line product__car">
						<span>Бесплатная
						  <br>доставка</span>
					  </div>
				</div>
				<div class="product__more">
				  <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="product__link">Подробнее..</a>
				</div>
			  </div>
			</div>		
		<?if($i == 2){?>
		  </div>
		  <?$i = 0;?>
		<?}?>
    <?endforeach?>
    <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
        <?=$arResult["NAV_STRING"]?>
    <?endif;?>
<?endif;?>