<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
if (isset($_REQUEST["AJAX_PAGEN"]) && $_REQUEST["AJAX_PAGEN"] == "Y")
{
    $APPLICATION->RestartBuffer();
    header('Content-Type: text/html; charset='.LANG_CHARSET);
}
?>
<div class="topsell-description"><?=$arResult["~UF_TEXT_FOR_TOPSELL"];?></div>
<?if ($arResult["ITEMS"]):?>
	<div class="grid-list">
		<div class="itemlist">
			<?foreach ($arResult["ITEMS"] as $arItem):?>
				<?
				$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => "Будет удалена вся информация, связанная с этой записью. Продолжить?"));
				?>
				
				<div class="item <?if ($arItem["PHOTOS"]):?>sub_img<?endif?> border-bottom" id="<?=$this->GetEditAreaId($arItem["ID"])?>">
					<div class="name name-catalog-category-<?=$arItem["IBLOCK_SECTION_ID"];?>">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
						<?if ($arItem["PROPERTIES"]["D3_GALLERY"]["VALUE"]):?>
							<a class="3d-link" href="#" onclick=" window.open('<?=$arItem["PROPERTIES"]["D3_GALLERY"]["VALUE"]?>', '', 'width=1200, height=850'); return false; "><img src="<?=SITE_TEMPLATE_PATH?>/images/3D_icon.png" alt=""></a>
						<?endif;?>
					</div>
					<div class="item_content">
						<div class="wrap_blok_i">
							
							<div class="left">
								<div class="inner">
									<a href="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" class="fancybox detail-image" data-fancybox-group="gallery-<?=$arItem["ID"]?>">
										<img class="eshop-item-small__img" src="<?=$arItem["RESIZE_PICTURE"]["SRC"]?>" title="<?=$arItem["NAME"]?>" alt="<?=$arItem["NAME"]?>" width="<?=$arItem["RESIZE_PICTURE"]["WIDTH"]?>" height="<?=$arItem["RESIZE_PICTURE"]["HEIGHT"]?>">
										<div class="stamp"><span class="stamp-text"><span class="stamp-number"><?=($arItem["PROPERTIES"]["SOLD"]["VALUE"] ? $arItem["PROPERTIES"]["SOLD"]["VALUE"] : 0)?></span>шт.<br/>продано</span></div>
									</a>
									<?if ($arItem["PHOTOS"]):?>
										<ul class="inner_sub">
											<?foreach ($arItem["PHOTOS"] as $nPhoto => $arPhoto):?>
												<?if ($nPhoto < 4):?>
													<li class="<?=($nPhoto == 0 ? "active" : "")?>">
														<a data-fancybox-group="gallery-<?=$arItem["ID"]?>"  class="fancybox" href="<?=$arPhoto["BIG"]["SRC"]?>" data-detail="<?=$arPhoto["DETAIL"]["SRC"]?>"><img src="<?=$arPhoto["PREVIEW"]["SRC"]?>" title="<?=$arItem["NAME"]?>" alt="<?=$arItem["NAME"]?>" width="<?=$arPhoto["PREVIEW"]["WIDTH"]?>" height="<?=$arPhoto["PREVIEW"]["HEIGHT"]?>" /></a>
													</li>
												<?endif?>
											<?endforeach?>
										</ul>
									<?endif?>
								</div>
							</div>
							
							<div class="center">
								<div class="desc">
									<ul class="item-params">
										<?foreach ($arItem["DISPLAY_PROPERTIES"] as $arProp):?>
											<?if ($arProp["DISPLAY_VALUE"]):?>
												<li>
													<span class="param-name"><b><?=$arProp["NAME"]?>:</b></span>
													<span class="param-value"><?=(is_array($arProp["DISPLAY_VALUE"]) ? implode(", ", $arProp["DISPLAY_VALUE"]) : $arProp["DISPLAY_VALUE"])?> <?=$arProp["HINT"]?></span>
												</li>
											<?endif?>
										<?endforeach?>
									</ul>
								</div>
							</div>
							
							<div class="right">
								<div class="right_button_block">
									<a class="buy-button" <?if(SITE_ID == "s1"):?> onclick="yaCounter1021532.reachGoal('zakaz_1_click');" <?endif;?> href="#" title="Оформление заказа в 1 клик" data-good-id="<?=$arItem["ID"]?>">Купить</a>
									<div class="tobottom">
										<?if ($arItem["PROPERTIES"]["LINK_VIDEO"]["VALUE"]):?>
											<a class="watch-video" data-fancybox-type="iframe" href="<?=$arItem["PROPERTIES"]["LINK_VIDEO"]["VALUE"][0]?>" >Смотреть видео</a>
										<?endif;?>
										<?/*if ($arItem["PROPERTIES"]["D3_GALLERY"]["VALUE"]):?>
											<a class="watch-3d" href="#" onclick=" window.open('<?=$arItem["PROPERTIES"]["D3_GALLERY"]["VALUE"]?>', '', 'width=1200, height=850'); return false; ">Смотреть в <span class="red">3D</span></a>
										<?endif;*/?>
										<?if ($arItem["PROPERTIES"]["TEST_DRIVE"]["VALUE"]):?>
											<a class="test-drive fancybox" href="#test-drive-content-<?=$arItem["ID"]?>">Тест-драйв</a>
											<div class="test-drive-content" style="display:none;" id="test-drive-content-<?=$arItem["ID"]?>">
												<?=$arItem["TEST_DRIVE_ITEM"]["DETAIL_TEXT"]?>
											</div>
										<?endif;?>
									</div>
								</div>
							</div>
						</div>
						<div class="clear"></div>
					</div>

					<?#if ($arItem["IS_ICONS"] || $arItem["GOOD_ACTIONS"]):?>
					<ul class="table_icons">
						<?#if ($arItem["PROPERTIES"]["PRODUCT_CERTIFIED"]["VALUE_XML_ID"] == "Y"):?>
						<li class="tooltip-message" data-tooltipe-text="Товар прошел сертификацию."><img alt="" class="rst_ic-min" src="/bitrix/images/sprites/icon-back-min.png"><span>товар сертифицирован</span></li>
						<?#endif?>
						<?#if ($arItem["PROPERTIES"]["EXPERT_ADVICE"]["VALUE_XML_ID"] == "Y"):?>
						<li class="tooltip-message" data-tooltipe-text="Товар рекомендован экспертом."><img alt="" class="recom_ic-min" src="/bitrix/images/sprites/icon-back-min.png"><span>рекомендация эксперта</span></li>
						<?#endif?>
					</ul>
					<?#endif?>

					<div class="bottom">
						<div class="pull-left">
							<?if (intval($arItem["COMMENTS_COUNT"]) > 1 || true):?>
								<div class="left">
									<span>Отзывы: </span> <a class="comment_link" href="<?=$arItem["DETAIL_PAGE_URL"]?>#comment"><?=$arItem["COMMENTS_COUNT"]?> <span>шт.</span></a>
								</div>
							<?endif;?>
							<?if (($arItem["RATING"] && intval($arItem["COMMENTS_COUNT"]) > 0 && $arItem["RATING"]["CLASS"] != 'zero')  || true ):?>
								<div class="center">
									Рейтинг:
									<span class="stars <?=$arItem["RATING"]["CLASS"]?>"></span>
									<div class="inline_block_right"></div>
								</div>
							<?endif?>
							<?if ($arItem["GUARANTEE"] || true):?>
								<div class="left">
									<span>Гарантия: </span> <span class="comment_link"><?=($arItem["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"]." ".Helper::DeclOfNum($arItem["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"], array("год", "года", "лет")));?><span></span></span>
								</div>
							<?endif;?>
							<div class="cat-detail-url">
								<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="catalog-detail-url btn btn-warning">Посмотреть в каталоге</a>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					
					
					<?if ($arItem["OFFERS"]):?>
						<div id="popup-offers-<?=$arItem["ID"]?>" class="popup-offers fancybox_block">
							<div class="gift_blocks">
								<?foreach ($arItem["OFFERS"] as $arOffer):?>
									<div class="gift_block">
										<div class="img"><img src="<?=$arOffer["RESIZE_PICTURE"]["SRC"]?>" alt="<?=$arOffer["NAME"]?>" /></div>
										<div class="name"><?=$arOffer["PROPERTIES"]["VENDOR_CODE"]["VALUE"]?></div>
										<div class="price"><?=$arOffer["REGION_PRICE"]["DISCOUNT_VALUE"]?> руб.<?if ($arOffer["CATALOG_MEASURE_NAME"] != "шт"):?> / <?=$arOffer["CATALOG_MEASURE_NAME"]?><?endif?></div>
										<div class="buy_button incart_input scale-decrease" data-id="<?=$arOffer["ID"]?>" data-path="<?=$arOffer["ADD_URL"]?>">Добавить</div>
									</div>
								<?endforeach?>
								<div class="footer_button_block">
									<a href="#close-fancybox" class="button">Продолжить покупки</a>
									<a href="/cart/" class="red_button">Оформить заказ</a>
								</div>
							</div>
						</div>
					<?endif;?>
					
				</div>
			 <?endforeach;?>
		</div>
	</div>

	<div class="topsell-description"><?=$arResult["~UF_TEXT_FOR_BOTTSELL"];?></div>

	<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<?=$arResult["NAV_STRING"]?>
	<?endif;?>
<?else:?>
	<?=$arParams["MESSAGE_NOT_FOUND"]?>
<?endif?>

<?
if (isset($_REQUEST["AJAX_PAGEN"]) && $_REQUEST["AJAX_PAGEN"] == "Y")
{
	die();
}?>
<? if (!$arResult["IS_HIDE_DESC"]): ?>
	<div class="catalog-section-description">
		<?$APPLICATION->IncludeComponent(
			"bitrix:main.include",
			"",
			array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => $arResult["FILE_SECTION_PATH"],
				"EDIT_TEMPLATE" => "text.php",
			)
		);?>
		<? if (!$arResult["FILE_SECTION_EXISTS"]/* && !$_SESSION['REGION_DOMAIN_CODE']*/): ?>
			<?=$arResult["DESCRIPTION"]?>
		<? endif; ?>
	</div>
<?endif?>

<br>
<div class="clear"></div>
<div class="custom-form-prototype-footer-callback">
	<div class="callback-header">Остались вопросы?</div>
	<div class="callback-subheader"><b>Мы знаем про <?= (empty($arResult["UF_TEXT_FOR_FEEDBACK"])) ? "швейные машины" : $arResult["UF_TEXT_FOR_FEEDBACK"] ?> абсолютно все!</b></div>
	<div class="callback-subheader" style="margin-bottom:17px;">Наш эксперт перезвонит Вам через <b>28 секунд</b> и ответит на все возникшие вопросы!</div>
	<?$APPLICATION->IncludeComponent(
		"custom:form.prototype",
		"footer-callback",
		array(
			"FORM_ID" => 1,
			"FORM_ACTION" => "FORM_CALLBACK",
			"SUCCESS_MESSAGE" => "Спасибо, что выбрали нас! Мы перезвоним Вам в ближайшее время!",
            "YANDEX_COUNER" => "ostalis-voprosy",
			"FIELDS" => array(
				"form_text_1",
				"form_text_2"
			)
		),
		$component,
		array(
			"HIDE_ICONS" => "Y",
		)
	);?>
</div>

<? if ($arParams["SECTION_ID"]):?>
	<script>
		(window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
			try { rrApi.categoryView(<?=$arParams["SECTION_ID"]?>); } catch(e) {}
		})
	</script>
<?endif?>