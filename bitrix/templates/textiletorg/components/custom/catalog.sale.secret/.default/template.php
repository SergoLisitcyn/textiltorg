<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
if (isset($_REQUEST["AJAX_PAGEN"]) && $_REQUEST["AJAX_PAGEN"] == "Y")
{
	$APPLICATION->RestartBuffer();
	header('Content-Type: text/html; charset='.LANG_CHARSET);
}
?>
<?/*
p($arResult["NAV_RESULT"]->NavPageCount);
p($arResult["NAV_RESULT"]->NavPageNomer);
p($arResult["NAV_RESULT"]->NavPageSize);
*/
//p($arProps);?>
<?if ($arResult["ITEMS"]):?>

	<div class="grid-list">
		<div class="itemgrid last-link">
			<?foreach ($arResult["ITEMS"] as $arItem):?>
				<?
					$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => "Будет удалена вся информация, связанная с этой записью. Продолжить?"));
				?>
				<div style="height: auto;" class="item <?if ($arItem["PHOTOS"]):?>sub_img<?endif?> shadow-box <?=($arResult["NAV_RESULT"]->NavPageCount == $arResult["NAV_RESULT"]->NavPageNomer ? "lastpage" : "")?>" id="<?=$this->GetEditAreaId($arItem["ID"])?>">
					<div class="name name-catalog-category-<?=$arItem["IBLOCK_SECTION_ID"];?>" style="height: 54px;">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
						<?if($arItem["PROPERTIES"]["IS_NEW"]["VALUE_XML_ID"] == "Y"):?>
							<img alt="" src="<?=SITE_TEMPLATE_PATH?>/img/novinka.gif" />
						<?endif;?>
						<?if(!empty($arItem["ACTIONS"])): ?>
							<div class="label-action">
								АКЦИЯ
								<div class="wrap-actions">
									<div class="actions-block">
										<div class="header">Участвует в акции</div>
										<? foreach ($arItem["ACTIONS"] as $arAction): ?>
											<div><a href="<?=$arAction["URL"]?>">«<?=$arAction["NAME"]?>»</a></div>
										<? endforeach; ?>
									</div>
								</div>
							</div>
						<?endif;?>
					</div>
					<?
						$PRODUCT_IBLOCK_ID=8;
						$PRODUCT_ID=$arItem["PROPERTIES"]["IDIBLOCK_UTSEN"]["VALUE"];//198
								$db_res = CPrice::GetList(
										array(),
										array(
											"PRODUCT_ID" => $PRODUCT_ID,
											)
									);
										?>
					<div class="item_content" style="height: 365px;">
						<div class="wrap_blok_i">
							
							<div class="left">
								<div class="inner">
									<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="eshop-item-img-link">
										<img class="eshop-item-small__img" src="<?=$arItem["RESIZE_PICTURE"]["SRC"]?>" title="<?=$arItem["DETAIL_PAGE_URL"]?>" alt="<?=$arItem["DETAIL_PAGE_URL"]?>" width="<?=$arItem["RESIZE_PICTURE"]["WIDTH"]?>" height="<?=$arItem["RESIZE_PICTURE"]["HEIGHT"]?>">
									</a>
									<? if ($arItem["PROPERTIES"]["IS_PREMIUM"]["VALUE_XML_ID"] == "Y") { ?>
										<div class="spring_marafon_min"></div>
									<? } ?>

									<?if ($arItem["GOOD_ACTIONS"]):?>
										<div class="red_tape_min"></div>
									<?endif?>

									<?if ($arItem["PHOTOS"]):?>
										<ul class="inner_sub">
											<?foreach ($arItem["PHOTOS"] as $nPhoto => $arPhoto):?>
												<?if ($nPhoto < 4):?>
													<li <?if ($nPhoto == 0):?> class="active"<?endif?>>
														<a href="<?=$arPhoto["DETAIL"]["SRC"]?>"><img src="<?=$arPhoto["PREVIEW"]["SRC"]?>" title="<?=$arItem["NAME"]?>" alt="<?=$arItem["NAME"]?>" width="<?=$arPhoto["PREVIEW"]["WIDTH"]?>" height="<?=$arPhoto["PREVIEW"]["HEIGHT"]?>" /></a>
													</li>
												<?endif?>
											<?endforeach?>
										</ul>
									<?endif?>
								</div>
							</div>
							
							<div class="center">
								<div class="desc">
									<?if((intval($arItem["IBLOCK_SECTION_ID"]) === 848) and (!empty($arItem["PROPERTIES"]["UTSEN_REASON"]["VALUE"]))):?>
										<div class="eshop-item-small__spec-announce"><p><b class="ucen_ic"><?=$arItem["PROPERTIES"]["UTSEN_REASON"]["NAME"].": "?></b><?=$arItem["PROPERTIES"]["UTSEN_REASON"]["VALUE"]?></p><!--noindex--><?=$arItem["~PREVIEW_TEXT"]?><!--/noindex--></div>
									<?else:?>
										<div class="eshop-item-small__spec-announce"><!--noindex--><?=$arItem["~PREVIEW_TEXT"]?><!--/noindex--></div>
									<?endif?>
								</div>
								
								<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">Подробнее...</a>
							</div>
							
							<?if ($arItem["IS_ICONS"] || $arItem["GOOD_ACTIONS"]):?>
								<ul class="table_icons">
									<?if ($arItem["PROPERTIES"]["PRODUCT_CERTIFIED"]["VALUE_XML_ID"] == "Y"):?>
										<li class="tooltip-message" data-tooltipe-text="Товар прошел сертификацию."><img alt="" class="rst_ic-min" src="/bitrix/images/sprites/icon-back-min.png"><span>товар сертифицирован</span></li>
									<?endif?>

									<?if (!empty($arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
										<?// Textiletorg props?>
										<?if (in_array(1083, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
											 <li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки всех типов тканей."><img alt="" class="cloth1_ic-min" src="/bitrix/images/sprites/icon-back-min.png"><span>для всех типов ткани</span></li>
										<?else:?>
											<?if (in_array(1084, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
												<li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки легких и сверхлегких типов тканей."><img alt="" class="cloth2_ic-min" src="/bitrix/images/sprites/icon-back-min.png"><span>для легких и сверхлегких тканей</span></li>
											<?endif?>
											<?if (in_array(1085, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
												<li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки легких и средних типов тканей."><img alt="" class="cloth3_ic-min" src="/bitrix/images/sprites/icon-back-min.png"><span>для легких и средних тканей</span></li>
											<?endif?>
											<?if (in_array(1086, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
												<li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки тяжелых и сверхтяжелых типов тканей."><img alt="" class="cloth4_ic-min" src="/bitrix/images/sprites/icon-back-min.png"><span>для тяжелых и сверхтяжелых тканей</span></li>
											<?endif?>
										<?endif?>
										<?// TTProm props?>
										<?if (in_array(1293, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
											 <li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки всех типов тканей."><img alt="" class="cloth1_ic-min" src="/bitrix/images/sprites/icon-back-min.png"><span>для всех типов ткани</span></li>
										<?else:?>
											<?if (in_array(1298, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
												<li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки легких и сверхлегких типов тканей."><img alt="" class="cloth2_ic-min" src="/bitrix/images/sprites/icon-back-min.png"><span>для легких и сверхлегких тканей</span></li>
											<?endif?>
											<?if (in_array(1294, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
												<li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки легких и средних типов тканей."><img alt="" class="cloth3_ic-min" src="/bitrix/images/sprites/icon-back-min.png"><span>для легких и средних тканей</span></li>
											<?endif?>
											<?if (in_array(1295, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
												<li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки тяжелых и сверхтяжелых типов тканей."><img alt="" class="cloth4_ic-min" src="/bitrix/images/sprites/icon-back-min.png"><span>для тяжелых и сверхтяжелых тканей</span></li>
											<?endif?>
										<?endif?>
									<?endif?>

									<?if ($arItem["PROPERTIES"]["GIFT_COUPON_25"]["VALUE_XML_ID"] == "Y"):?>
										<li class="tooltip-message" data-tooltipe-text="При покупке текущего товара в подарок купон со скидкой 25%."><img alt="" class="scid_ic-min" src="/bitrix/images/sprites/icon-back-min.png"><span>в подарок купон 25%</span></li>
									<?endif?>
									<?if ($arItem["PROPERTIES"]["EXPERT_ADVICE"]["VALUE_XML_ID"] == "Y"):?>
										<li class="tooltip-message" data-tooltipe-text="Товар рекомендован экспертом."><img alt="" class="recom_ic-min" src="/bitrix/images/sprites/icon-back-min.png"><span>рекомендация эксперта</span></li>
									<?endif?>

									<?foreach ($arItem["GOOD_ACTIONS"] as $arAction):?>
										<li>
											<img alt="" class="acsia_ic_min" src="/bitrix/images/sprites/icon-back-min.png<?/*=$arAction["ICON_GOOD"]*/?>">
											<a href="<?=$arAction["DETAIL_PAGE_URL"]?>" target="_blank">участник акции «<?=$arAction["NAME"]?>»</a>
										</li>
									<?endforeach?>
								</ul>
							<?endif?>
							
							<div class="virtuni">spb</div>
						</div>
						
					
						<div class="right">
							<div class="price sd">
								<?if(intval($arItem["IBLOCK_SECTION_ID"]) === 848):?>
									<?if (intval($arItem["REGION_PRICE"]["DISCOUNT_VALUE"])):?>
										<?if ($ar_res = $db_res->Fetch()):?>
											<div class="old-price"><span>Цена:  </span><div class="old-span"><?=number_format((strval($ar_res["PRICE"])),0,'.',' ').' '/*=strval($ar_res["PRICE"])=$arItem["PROPERTIES"]["PRICE_DISC"]["VALUE"]*/?><small>   руб.</small></div> </div>
										<?endif;?>
										<div class="new-price"><?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?> <small>руб.</small></div>
									<?else:?>
										<span>Цена:</span> на заказ
									<?endif;?>
								<?else:?>
									<?if (intval($arItem["REGION_PRICE"]["DISCOUNT_VALUE"])):?>
										<span>Цена: </span> <?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?> <small>руб.<?if ($arItem["CATALOG_MEASURE_NAME"] != "шт"):?> / <?=$arItem["CATALOG_MEASURE_NAME"]?><?endif?></small>
									<?else:?>
										<span>Цена: </span> на заказ
									<?endif?>
								<?endif;?>
							</div>
							
							<?if ($arItem["REGION_PRICE"]["VALUE_NOVAT"] > $arItem["REGION_PRICE"]["DISCOUNT_VALUE_NOVAT"]):?>
								<div class="old-price">
									Старая цена: <span style="font-size: 1.2em"><?=$arItem["REGION_PRICE"]["VALUE"]?></span> <small>руб.</small>
								</div>
							<?elseif (!empty($arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"])):?>
								<div class="old-price">
									Старая цена: <span style="font-size: 1.2em"><?=$arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]?></span> <small>руб.</small>
								</div>
							<?endif?>

							<div class="right_button_block">
								<div class="buybtn">
									<?if ($arItem["OFFERS"]):?>
										<a href="#popup-offers-<?=$arItem["ID"]?>" class="inyourcart scale-decrease fancybox" data-id="<?=$arItem["ID"]?>" <?if(SITE_ID =="s1"):?>onclick="yaCounter1021532.reachGoal('Open_Cart'); return true;"<?endif;?> data-path="<?=$arItem["ADD_URL"]?>" title="Выбор цвета"></a>
									<?else:?>
										<input onmousedown="try { rrApi.addToBasket(<?=$arItem["ID"]?>) } catch(e) {}" type="submit" class="inyourcart scale-decrease" data-id="<?=$arItem["ID"]?>" <?if(SITE_ID == "s1"):?>onclick="yaCounter1021532.reachGoal('Open_Cart'); return true;"<?endif;?> data-path="<?=$arItem["ADD_URL"]?>" data-name="<?=$arItem["NAME"]?>" data-picture="<?=$arItem["RESIZE_PICTURE"]["SRC"]?>" data-vendor="<?=$arItem["PROPERTIES"]["VENDOR_CODE"]["VALUE"]?>" data-price="<?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?>" data-price-rb="<?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?>" >
									<?endif?>
								</div>

								<div class="one-click-css clearfix">
									<a  class="eshop-item-small__one-click scale-decrease buy-one-click" onclick="yaCounter1021532.reachGoal('zakaz_1_click'); return true;" href="#buy-one-click-form" title="Оформление заказа в 1 клик" data-good-id="<?=$arItem["ID"]?>">Купить в 1 клик</a>
								</div>
							</div>
							
							<?if(intval($arItem["IBLOCK_SECTION_ID"]) != 848):?>
								<?if (!empty($arItem["CREDIT"])):?>
									<div class="creditprice_first_pay b-container-popup-credit b-container-popup-credit-credit">
										В кредит: <span class="credit_on tooltip-message-click" data-tooltipe-text="Размер платежа является предварительным при условии первоначального взноса 10%, срок кредита 24 месяцев.<br><br>Точные даты и размеры ежемесячных платежей будут указаны в Графике платежей, являющемся неотъемлемой частью Кредитного договора.<br><br>Кредит предоставляется на общую сумму покупки от 3 000 руб."><?=$arItem["CREDIT"]?> <small>руб.</small>/мес.</span>
									</div>
								<?endif?>

								<?if (!empty($arItem["CREDIT"]) || $arItem["PROPERTIES"]["PAYMENT_INSTALLMENTS"]["VALUE_XML_ID"] == "Y"):?>
									<div class="blue as-credit-info">
										<?if (!empty($arItem["CREDIT"])):?>
											<span class="credit_des inf_3000 tooltip-message-click" data-tooltipe-text="Кредит предоставляется на общую сумму покупки от 3 000 руб.<br><br>Вы можете приобрести данный товар в кредит, в наших магазинах. Подробнее в разделе «<a href='/informacija/kredit/' target='_blank'>Кредит</a>»">Можно в кредит: <span>Да</span> <i class="ico_ques"></i></span>
										<?endif?>
										<?if ($arItem["PROPERTIES"]["PAYMENT_INSTALLMENTS"]["VALUE_XML_ID"] == "Y"):?>
											<span class="credit_des inf_rassrochka tooltip-message-click" data-tooltipe-text="Вы можете приобрести данный товар в рассрочку - без процентов, в наших магазинах. Подробнее в разделе «<a href='/akcii/rassrochka/' target='_blank'>Рассрочка</a>»">Рассрочка: <span>0%</span> <i class="ico_ques"></i></span>
										<?endif?>
									</div>
								<?endif?>
							<?endif?>
							<div class="clear"></div>
							
							<?if ($arItem["QUANTITY_TEXT"]):?>
								<div class="instock ltl">наличие в магазинах: <?if(intval($arItem["IBLOCK_SECTION_ID"]) == 848):?><span><?=intval($arItem[CATALOG_QUANTITY])?><?else:?><span><?=$arItem["QUANTITY_TEXT"]?></span> <?endif?></div>
							<?endif?>
							<?if ($arItem["RESERVED_TEXT"]):?>
								<div class="instock ltl">наличие на доставку: <?if(intval($arItem["IBLOCK_SECTION_ID"]) == 848):?><span><?=intval($arItem[CATALOG_QUANTITY])?><?else:?><span><?=$arItem["RESERVED_TEXT"]?></span><?endif?> </div>
							<?endif?>
						</div>
						<div class="clear"></div>
					</div>

					<div class="bottom">
						<?if (intval($arItem["COMMENTS_COUNT"]) > 1):?>
							<div class="left">
								<span>Отзывы: </span> <a class="comment_link" href="<?=$arItem["DETAIL_PAGE_URL"]?>#comment"><?=$arItem["COMMENTS_COUNT"]?> <span>шт.</span></a>
							</div>
						<?endif;?>
						<?if ($arItem["OVERVIEWS"]):?>
							<div class="left">
								<span>Обзоры: </span> <span class="comment_link tooltip-message-click" data-tooltipe-text='<?=$arItem["OVERVIEWS"]["HELP"]?>'><?=$arItem["OVERVIEWS"]["COUNT"]?> <span>шт.</span></span>
							</div>
						<?endif;?>
						<?if ($arItem["RATING"] && intval($arItem["COMMENTS_COUNT"]) > 0 && $arItem["RATING"]["CLASS"] != 'zero'):?>
							<div class="center">
								Рейтинг:
								<span class="stars <?=$arItem["RATING"]["CLASS"]?>"></span>
								<div class="inline_block_right"></div>
							</div>
						<?endif?>
						<div class="right">
							<script>lbt='body_items'</script>
                            <div id="" class="qqq">
                                <!--                        <div class="qqq">-->
								<a href="<?=$arItem["ADD_COMPARE_URL"]?>" class="add-compare-button add-compare" data-add-compare-url="<?=$arItem["ADD_COMPARE_URL"]?>" data-delete-compare-url="<?=$arItem["DELETE_COMPARE_URL"]?>">Добавить к сравнению</a>
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
	<div class="clear"></div>
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
<?/* if (!$arResult["IS_HIDE_DESC"]): ?>
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

		<? if (!$arResult["FILE_SECTION_EXISTS"] && !$_SESSION['REGION_DOMAIN_CODE']): ?>
			<?=$arResult["DESCRIPTION"]?>
		<? endif; ?>
	</div>
<?endif*/?>
<?/*
<br>
<div class="clear"></div>
<div class="custom-form-prototype-footer-callback">
	<div class="callback-header">Остались вопросы?</div>
	<div class="callback-subheader"><b>Мы знаем про <?= (empty($arResult["UF_TEXT_FOR_FEEDBACK"])) ? "швейные машины" : $arResult["UF_TEXT_FOR_FEEDBACK"] ?> абсолютно все!</b></div>
	<div class="callback-subheader" style="margin-bottom:10px;">Наш эксперт перезвонит Вам через <b>28 секунд</b> и ответит на все возникшие вопросы!</div>

	<?$APPLICATION->IncludeComponent(
		"custom:form.prototype",
		"footer-callback",
		array(
			"FORM_ID" => 1,
			"FORM_ACTION" => "FORM_CALLBACK",
			"SUCCESS_MESSAGE" => "Спасибо, что выбрали нас! Мы перезвоним Вам в ближайшее время!",
			"YANDEX_COUNER" => "ostalis-voprosy_catalog",
			/*"YAN_SITE" => "s1",* /
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

<script>
var deviceType = /iPad/.test(navigator.userAgent)?"t":/webOS|Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent)?"m":"d";
window.criteo_q = window.criteo_q || [];
window.criteo_q.push(
    { event: "setAccount", account: 38714 },
    { event: "setEmail", email: "<?=$USER->GetEmail();?>" },
    { event: "setSiteType", type: deviceType },
    { event: "viewList", item:[
        <?php
        $count = 1;
                foreach ( $arResult["ITEMS"] as $arItem ) { if ($count <= 3) {?>
         "<?=(!empty($arItem["XML_ID"])) ? $arItem["XML_ID"] : $arItem["ID"]?>",
                <?php
        }
        $count++;
         } ?>
    ]}
);

</script>
<? if ($arParams["SECTION_ID"]):?>
    <script>
        (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
            try { rrApi.categoryView(<?=$arParams["SECTION_ID"]?>); } catch(e) {}
        })
    </script>
<?endif?>
?>