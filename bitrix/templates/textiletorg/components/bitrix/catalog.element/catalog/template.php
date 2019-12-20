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


<? $APPLICATION->IncludeComponent(
    "bitrix:breadcrumb",
    "breadcrumbs",
    array(
        "START_FROM" => (Helper::IsRealFilePath(array('/catalog/index.php', '/catalog/detail/index.php'))) ? "1" : "0",
        "PATH" => "",
        "SITE_ID" => "-"
    ),
    false
); ?>


<div class="catalog-element-catalog" itemscope itemtype="http://schema.org/Product">
    <div class="item_one">

      <meta itemprop="brand" content="<?=$arResult["SECTION"]["NAME"]?>">

	  <div id="item">

            <div class="left_block_cart">
                <div id="title" class="item_name">
                    <h1 itemprop="name"><?=$arResult["NAME"]?></h1>
                    <?if ($arResult["PROPERTIES"]["IS_NEW"]["VALUE_XML_ID"] == "Y"):?>
                        <img src="<?=SITE_TEMPLATE_PATH?>/img/novinka.gif" alt=""/>
                    <?endif?>
                    <?if ($arResult["PROPERTIES"]["VENDOR_CODE"]["VALUE"]):?>
                        <span>Артикул <span itemprop="sku"><?=$arResult["PROPERTIES"]["VENDOR_CODE"]["VALUE"]?></span></span>
                    <?endif?>
                </div>
                <div class="img">
                    <?if ($arResult["PROPERTIES"]["D3_GALLERY"]["VALUE"]):?>
                        <a class="3d-popup" data-fancybox-type="iframe" href="<?=$arResult["PROPERTIES"]["D3_GALLERY"]["VALUE"]?>">
                            <i class="tt-icons show3d-icon"></i>
                        </a>
                    <?endif?>
                    <?if ($arResult["PROPERTIES"]["IS_GIFT"]["VALUE_XML_ID"] == "Y"):?>
                        <div class="gift_buy">
                            <?if (!empty($arResult["PROPERTIES"]["GIFT_DESCRIPTION"]["VALUE"])):?>
                            <div class="tooltip-message" data-tooltipe-text="<ul><li><?= implode("</li><li>", $arResult["PROPERTIES"]["GIFT_DESCRIPTION"]["VALUE"])?></li></ul>"></div>
                            <?endif?>
                        </div>
                    <?endif?>
                    <div class="in">
                        <div class="spring_marafon_info">Товар участвует в акции "Марафон скидок".<br>При покупке данной модели мы дарим Вам премиальный сервисный пакет обслуживания</div>
                        <?if (empty($arResult["RESIZE_PICTURE"])):?>
                            <a class="gallery" id="main_img" href="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" >
                        <?endif?>
                            <div class="picture_gallery cont_main_img_cart <?if ($arResult["PHOTOS"]):?>fix-img-container<?endif?>">
                                <?if ($arResult["GOOD_ACTIONS"]):?>
                                    <div class="zoom red_tape"></div>
                                <?endif?>
                                <?if ($arResult["PROPERTIES"]["IS_PREMIUM"]["VALUE_XML_ID"] == "Y"):?>
                                    <div class="spring_marafon"></div>
                                <?endif?>
                                    <img src="<?=$arResult["RESIZE_PICTURE"]["SRC"]?>"
                                         itemprop="image"
                                         alt="Фото <?=$arResult["NAME"]?> | Швейный магазин Текстильторг"
                                         data-id="0"
                                         data-sgallery="group1"
                                         title="Фото <?=$arResult["NAME"]?> | Швейный магазин Текстильторг"
                                         data-full="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
                                         data-thumb="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
                                         data-zoom-image="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
                                    />
                            </div>
							<? $i = 0; ?>
                            <?foreach ($arResult["PHOTOS"] as $nPhoto => $arPhoto):
                                        if ($nPhoto <= 0) continue;
										$i++;
                                    ?>
                                        <img data-sgallery="group1"
                                             data-id="<?=$nPhoto?>"
                                             style="display: none"
                                             title="Фото <?=$i;?> <?=$arResult["NAME"]?> | Швейный магазин Текстильторг"
											 alt="Фото <?=$i;?> <?=$arResult["NAME"]?> | Швейный магазин Текстильторг"
                                             data-full="<?=$arPhoto["DETAIL"]["SRC"]?>"
                                             data-thumb="<?=$arPhoto["PREVIEW"]["SRC"]?>"
                                             src="<?=$arPhoto["PREVIEW"]["SRC"]?>"/>
                                    <?endforeach?>


                        <?if (empty($arResult["RESIZE_PICTURE"])):?>
                            </a>
                        <?endif?>
                    </div>
                </div>
                <div class="clear"></div>
                <?if ($arResult["PHOTOS"]):?>
                    <div class="leftb">
                        <div id="wrapper_sl">
                            <div class="multiple-items" id="multiple-items">
								<? $num = 1; ?>
                                <?foreach ($arResult["PHOTOS"] as $nPhoto => $arPhoto):?>
                                    <div>
                                        <a href="<?=$arPhoto["DETAIL"]["SRC"]?>" <?if ($nPhoto == 0):?> class="focus_img"<?endif?> data-image="<?=$arPhoto["DETAIL"]["SRC"]?>" data-zoom-image="<?=$arPhoto["DETAIL"]["SRC"]?>" itemprop="image" data-id="<?=$nPhoto?>">
                                            <img src="<?=$arPhoto["PREVIEW"]["SRC"]?>" alt="Фото <?=$num?> <?=$arResult["NAME"]?> | Текстильторг" title="Фото <?=$num?> <?=$arResult["NAME"]?> | Текстильторг" />
                                        </a>
                                    </div>
									<? $num++; ?>
                                <?endforeach?>
                            </div>
                        </div>
                    </div>
					<script>
						var $=jQuery.noConflict();
						$(document).ready(function(){
							$("#wrapper_sl .multiple-items a img").click(function(){
								$('.zoomContainer').css({
										'max-height':'495px'
									});	
								
								$('.zoomWindowContainer div').css({
										'height':'495px'
								});
							});
						});
					</script>
                    <div class="clear"></div>
                <?endif?>
            </div>

			
			
				<?
								$PRODUCT_IBLOCK_ID=8;
									$PRODUCT_ID=$arResult["PROPERTIES"]["IDIBLOCK_UTSEN"]["VALUE"];
								//198
								$db_res = CPrice::GetList(
										array(),
										array(
											"PRODUCT_ID" => $PRODUCT_ID,
											)
									);
										?>
										
            <div class="right_block_cart">


                <?
                    $frame = $this->createFrame()->begin("");
                ?>

                <div class="grey_block">
                    <div class="price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                        <meta itemprop="url" content="<?="https://".SITE_SERVER_NAME.$arResult["DETAIL_PAGE_URL"]?>">
						<?/*------ utsenennye-tovary ------*/
							if(intval($arResult["IBLOCK_SECTION_ID"]) === 848):?>
                        <?if (intval($arResult["REGION_PRICE"]["DISCOUNT_VALUE"])):?>
								
								<div class="old-price">
								<?if ($ar_res = $db_res->Fetch()){?>
								
								<span class="price-name">Цена:  </span><span class="cls-price"><?=' '.number_format((strval($ar_res["PRICE"])),0,'.',' ').' '?><?/*CurrencyFormat($ar_res["PRICE"], $ar_res["CURRENCY"])=$arResult["PROPERTIES"]["PRICE_DISC"]["VALUE"]*/?>руб.<?if ($arResult["CATALOG_MEASURE_NAME"] != "шт"):?> / <?=$arResult["CATALOG_MEASURE_NAME"]?><?endif?></span>
								
								<?}?>
								<meta itemprop="price" content="<?=number_format(str_replace(" ", "", $arResult["REGION_PRICE"]["DISCOUNT_VALUE"]), 2, '.', '')?>">
								
								<meta itemprop="priceCurrency" content="RUB" />
								</div>
									<div class="new_price"> <span style="font-size: 1.2em" class="price-price"><?=$arResult["REGION_PRICE"]["DISCOUNT_VALUE"].' '?></span><span class="price-cur">руб.<?if ($arResult["CATALOG_MEASURE_NAME"] != "шт"):?> / <?=$arResult["CATALOG_MEASURE_NAME"]?><?endif?></span></div>
								
								<?else:?>
										<span itemprop="price" class="price-price">На заказ</span>
								<?endif?>
					
					
								
							<?else:?>
								<?if (intval($arResult["REGION_PRICE"]["DISCOUNT_VALUE"])):?>
									 <span class="price-name">Цена:  </span><span class="price-price"><?='  '.$arResult["REGION_PRICE"]["DISCOUNT_VALUE"]?></span>
									<meta itemprop="price" content="<?=number_format(str_replace(" ", "", $arResult["REGION_PRICE"]["DISCOUNT_VALUE"]), 2, '.', '')?>">
									<span class="price-cur">руб.<?if ($arResult["CATALOG_MEASURE_NAME"] != "шт"):?> / <?=$arResult["CATALOG_MEASURE_NAME"]?><?endif?></span>
									<meta itemprop="priceCurrency" content="RUB" />
									
									
									
								<?else:?>
									<span itemprop="price" class="price-price">На заказ</span>
								<?endif?>
							<?endif?>

						
						
						
						
						
						
                        <?/*if ($arResult["REGION_PRICE"]["VALUE_NOVAT"] > $arResult["REGION_PRICE"]["DISCOUNT_VALUE_NOVAT"]):?>
                            <div class="old-price">
                                Старая цена: <big><?=$arResult["REGION_PRICE"]["VALUE"]?></big> <small>руб.</small>
                            </div>
                        <?elseif (!empty($arResult["PROPERTIES"]["PRICE_OLD"]["VALUE"]) && SITE_ID == "s1"):?>
                            <div class="old-price">
                                Старая цена: <big><?=$arResult["PROPERTIES"]["PRICE_OLD"]["VALUE"]?></big> <small>руб.</small>
                            </div>
                        <?endif*/?>
                        <div class="tooltip-message-click" data-tooltipe-text='Данная цена является самой низкой по "г. <?=$_SESSION["GEO_REGION_CITY_NAME"]?>". Если Вы совершите невозможное - обнаружите стоимость ниже указанной*, сообщите об этом нам и мы снизим для Вас цену и сделаем подарок!'>
                            <div class="price_sub" >Это лучшая цена! дешевле не бывает</div>
                        </div>
                    </div>

<?
$yaCounter = "1021532";
if($_SERVER["SERVER_NAME"] == "spb.textiletorg.ru") {
	$yaCounter = "48343148";
}
?>
                    <div class="buybtn">
                        <?if ($arResult["OFFERS"]):?>
                            <a href="#popup-offers" class="incart_input scale-decrease fancybox"  onclick="<?if(SITE_ID == "s1"):?> yaCounter<?=$yaCounter?>.reachGoal('Open_Cart');return true;<?endif;?>" title="Выбор цвета">
                                <span class="acs-bay-btn"><i class="fa fa-shopping-cart"></i> Купить</span>
                            </a>
                        <?else:?>
                            <button onmousedown="try { rrApi.addToBasket(<?=$arResult["ID"]?>) } catch(e) {}" type="submit" class="incart_input scale-decrease" id="incart-button" data-id="<?=$arResult["ID"]?>"  onclick="<?if(SITE_ID == "s1"):?>yaCounter<?=$yaCounter?>.reachGoal('Open_Cart'); return true;<?endif;?>" data-path="<?=$arResult["ADD_URL"]?>" data-name="<?=$arResult["NAME"]?>" data-picture="<?=$arResult["RESIZE_PICTURE"]["SRC"]?>" data-vendor="<?=$arResult["PROPERTIES"]["VENDOR_CODE"]["VALUE"]?>" data-price="<?=$arResult["REGION_PRICE"]["DISCOUNT_VALUE"]?>">
                                <i class="fa fa-shopping-cart"></i> Купить
                            </button>
                        <?endif?>
                    </div>

                    <a class="one_click_input scale-decrease buy-one-click"  onclick="<?if(SITE_ID == "s1"):?>yaCounter<?=$yaCounter?>.reachGoal('zakaz_1_click'); return true;<?endif;?>" href="#buy_one_click_form" title="Оформление заказа в 1 клик" data-good-id="<?=$arResult["ID"]?>" data-price="<?=$arResult["REGION_PRICE"]["DISCOUNT_VALUE_NOVAT"]?>" data-currency="<?=$arResult["REGION_PRICE"]["CURRENCY"]?>" data-good-name="<?=$arResult["NAME"]?>" data-good-url="<?=$arResult["DETAIL_PAGE_URL"]?>">Купить в 1 клик</a>
                     <?if ($arResult["OFFERS"]):?>
                        <div class="politra" title="Выбор цвета" onclick="$(this).closest('.right_block_cart').find('.buybtn a').click()"><span>Палитра цветов</span></div>
                    <?endif?>

                    <div class="kredit-rasrochka">
                        <ul class="table_icon">
                            <?if(intval($arResult["IBLOCK_SECTION_ID"]) != 848):?>
                                <?if (!empty($arResult["CREDIT"])):?>
                                    <li class="big-red tooltip-message-click" data-tooltipe-text="Размер платежа является предварительным при условии первоначального взноса 10%, срок кредита 24 месяцев.<br><br>Точные даты и размеры ежемесячных платежей будут указаны в Графике платежей, являющемся неотъемлемой частью Кредитного договора.<br><br>Кредит предоставляется на общую сумму покупки от 3 000 руб.">
                                        <span class="credit_des">В кредит: <?=$arResult["CREDIT"]?> руб./мес.</span>
                                    </li>
                                <?endif?>
                                <?if ($arResult["PROPERTIES"]["PAYMENT_INSTALLMENTS"]["VALUE_XML_ID"] == "Y"):?>
                                    <li class="big-red tooltip-message-click" data-tooltipe-text="Вы можете приобрести данный товар в рассрочку - без процентов, в наших магазинах. Подробнее в разделе «<a href='/akcii/rassrochka/' target='_blank'>Рассрочка</a>»">
                                        <span class="credit_des">Рассрочка: 0%</span>
                                    </li>
                                <?endif?>
                            <?endif?>
                        </ul>
                    </div>
                </div>

                <?
                    $frame->end();
                ?>

                <?if ($arResult["IS_ICONS"] || $arResult["GOOD_ACTIONS"]):?>
                    <div class="grey_block grey_block_icons">
                        <ul class="table_icon">
                            <?if ($arResult["PROPERTIES"]["PRODUCT_CERTIFIED"]["VALUE_XML_ID"] == "Y"):?>
                                <li class="tooltip-message" data-tooltipe-text="Товар прошел сертификацию">
                                    <i class="tt-icons sertifikat-icon"></i>
                                    <span>товар сертифицирован</span>
                                </li>
                            <?endif?>

                            <?if (!empty($arResult["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                                <?if (in_array(1083, $arResult["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                                     <li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки всех типов тканей">
                                         <i class="tt-icons legkii-srednii-icon"></i>
                                         <span>для всех типов ткани</span>
                                     </li>
                                <?else:?>
                                    <?if (in_array(1084, $arResult["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                                        <li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки легких и сверхлегких типов тканей">
                                            <i class="tt-icons legkii-srednii-icon"></i>
                                            <span>для легких и сверхлегких тканей</span>
                                        </li>
                                    <?endif?>
                                    <?if (in_array(1085, $arResult["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                                        <li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки легких и средних типов тканей">
                                            <i class="tt-icons legkii-srednii-icon"></i>
                                            <span>для легких и средних тканей</span>
                                        </li>
                                    <?endif?>
                                    <?if (in_array(1086, $arResult["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                                        <li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки тяжелых и сверхтяжелых типов тканей">
                                            <i class="tt-icons geo-icon"></i>
                                            <span>для тяжелых и сверхтяжелых тканей</span>
                                        </li>
                                    <?endif?>
                                <?endif?>
                            <?endif?>

                            <?if ($arResult["PROPERTIES"]["GIFT_COUPON_25"]["VALUE_XML_ID"] == "Y"):?>
                                <?
                                    $giftCouponTooltipeText = (in_array(strtolower($arResult["PROPERTIES"]["BRAND"]["VALUE"]), array("pfaff", "husqvarna")))
                                        ? "При покупке данного товара в подарок купон со скидкой 25% на любые аксессуары и расходные материалы"
                                        : "При покупке текущего товара в подарок купон со скидкой 25%";
                                ?>
                                <li class="tooltip-message" data-tooltipe-text="<?=$giftCouponTooltipeText?>">
                                    <i class="tt-icons tkany-25-percent-icon"></i>
                                    <span class="tooltip_cloth_on">в подарок купон 25%</span>
                                </li>
                            <?endif?>
                            <?if ($arResult["PROPERTIES"]["EXPERT_ADVICE"]["VALUE_XML_ID"] == "Y"):?>
                                <li class="tooltip-message" data-tooltipe-text="Товар рекомендован экспертом">
                                    <i class="tt-icons tkany-expert-recommend-icon"></i>
                                    <span class="tooltip_cloth_on">рекомендация эксперта</span>
                                </li>
                            <?endif?>

                            <?foreach ($arResult["GOOD_ACTIONS"] as $arAction):?>
                                <li>
                                    <img class="acsia_ic" src="/bitrix/images/sprites/cloth1.png<?/*=$arAction["ICON_GOOD"]*/?>" alt="">
                                    <a href="<?=$arAction["DETAIL_PAGE_URL"]?>" target="_blank">участник акции «<?=$arAction["NAME"]?>»</a>
                                </li>
                            <?endforeach?>
                        </ul>
                    </div>
                <?endif?>
                <? if ($arResult["MESSAGE_GUARANTEE"] || $arResult["MESSAGE_MASTERCLASS_SHOW"] || $arResult["PROPERTIES"]["IS_PREMIUM"]["VALUE_XML_ID"] == "Y"): ?>
                    <div class="grey_block grey_block_icons only_here">
                        <div class="only_here_text <?=SITE_ID?>_none">Только у нас при покупке данной модели:</div>
                        <ul class="table_icon">
                            <? if ($arResult["MESSAGE_GUARANTEE"]): ?>
                            <li>
                                <i class="tt-icons garanci-<?=strval($arResult["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"])?>"></i>
                                <span class="tooltip_cloth_on"><?=$arResult["MESSAGE_GUARANTEE"]?></span>
                            </li>
                            <?endif?>
                            <? if ($arResult["MESSAGE_MASTERCLASS_SHOW"]): ?>
                                <li class="<?=SITE_ID?>_none">
                                    <i class="tt-icons masterklass-icon"></i>
                                    <span class="tooltip_cloth_on">Мастер класс и обучение в подарок!</span>
                                </li>
                            <?endif?>
                            <?if ($arResult["PROPERTIES"]["IS_PREMIUM"]["VALUE_XML_ID"] == "Y"):?>
                                <li>
                                    <i class="tt-icons geo-icon"></i>
                                    <span class="tooltip_cloth_on">Премиум сервис в подарок!</span>
                                </li>
                            <?endif?>
                        </ul>
                    </div>
                <?endif?>
            </div>

            <div class="clear"></div>

            <div class="textinfo">
                <div class="grey_block">
                    <div class="left_subblock_cart raiting">
                        <ul>
                            <?if ($arResult["RATING"] && $arResult["COMMENTS_COUNT"] > 0):?>
                                <li <?=($arResult["RATING"]["COUNT"]) ? '' : 'style="display: none;"'?>>
                                    <label>Рейтинг:</label>
                                    <div class="rating rating_s">
                                        <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                                        <meta itemprop="bestRating" content="5">
                                        <meta itemprop="ratingValue" content="<?=$arResult["RATING"]["COUNT"]?>" />
                                        <meta itemprop="ratingCount" content="<?=$arResult["RATING"]["VOTES"]?>">
                                        </div>
                                        <img class="rating_ic_<?=strval($arResult["RATING"]["COUNT"])?>" src="<?=$arResult["RATING"]["IMAGE"]?>" alt="Рейтинг: <?=$arResult["RATING"]["COUNT"]?>; Голосов: <?=$arResult["RATING"]["VOTES"]?>">
                                        <div class="clear"></div>
                                    </div>
                                </li>
                            <?endif?>
                            <?if ($arResult["QUANTITY"]):?>
                                <li>
                                    <label>В наличии:</label>
                                    <div class="count_img inrest_img"><span class="<?=$arResult["QUANTITY"]["CLASS"]?>"></span></div> <?=$arResult["QUANTITY"]["TEXT"]?>
                                </li>
                            <?endif?>
                            <?if ($arResult["GUARANTEE"]):?>
                                <li>
                                    <label>Гарантия:</label>
                                    <?if (count($arResult["GUARANTEE"]) > 1):?>
                                        <select id="guarantee">
                                            <?foreach ($arResult["GUARANTEE"] as $arGuarante):?>
                                                <option value="<?=$arGuarante["ID"]?>" data-price="<?=$arGuarante["PRICE"]?>" data-print-price="<?=$arGuarante["PRINT_PRICE"]?>" data-path="<?=$arGuarante["ADD_URL"]?>"><?=$arGuarante["PRINT_NAME"]?></option>
                                            <?endforeach?>
                                        </select>
                                        <span id="guarantee-price"> <?=$arResult["GUARANTEE"][0]["PRINT_PRICE"]?></span>
                                    <?else:?>
                                        <?=$arResult["GUARANTEE"][0]["PRINT_NAME"]?>
                                    <?endif?>
                                </li>
                            <?endif?>
                        </ul>
                    </div>

                    <div class="left_subblock_cart ex_cart">
                        <?php $frame = $this->createFrame()->begin(); ?>
                        <ul>
                            <?if ($arResult['EXPORT_TEXT']):?>
                                <?if (SITE_ID != "by"):?>
                                    <li id="samovivoz" class="samovivoz_info tooltip-message-stores">
                                        <label>Забрать самому:</label>
                                        <?=$arResult['EXPORT_TEXT']?> (<a href="#as-stores-popup-header" title="Адреса пунктов выдачи г. <?=$arResult["GEO_REGION_CITY_NAME"]?>" data-city="<?=$arResult["GEO_REGION_CITY_NAME"]?>" style="text-decoration: none"><span class="dotted"><b><?=$GLOBALS["ITEMS_STORE_COUNT"]?></b> <?=$GLOBALS["ITEMS_STORE_COUNT_TEXT"]?> в г. <?=$arResult["GEO_REGION_CITY_NAME"]?></span></a>)
                                    </li>
                                <?else:?>
                                    <li id="samovivoz" class="samovivoz_info tooltip-message-stores"><label>Самовывоз:</label> <?=$arResult['EXPORT_TEXT']?></li>
                                <?endif?>
                            <?endif?>
                            <?if ($arResult["DELIVERY_TEXT"]):?>
                                <li>
                                    <?if (!empty($arResult["DELIVERY_HELP"])):?>
                                        <span class="margin-delivery">
                                            <label class="deliveries-title">Доставка:</label> <?=$arResult["DELIVERY_TEXT"]?>
                                        </span>
                                    <?else:?>
                                        <span class="margin-delivery"><label class="deliveries-title">Доставка:</label> <?=$arResult["DELIVERY_TEXT"]?></span>
                                    <?endif?>
                                </li>
                            <?endif?>

                            <?if ($arResult["GIFT_WRAPS"] && empty($arResult["OFFERS"]) && SITE_ID != "s1"):?>
                                <li><label>Оплата:</label> Наличными / Банковской картой</li>
                            <?else:?>
                                <li><label>Оплата:</label> Наличными / Банковской картой / Яндекс.Деньги</li>
                            <?endif?>
                        </ul>
                        <?php
                        $frame->beginStub();
                        echo "...";
                        $frame->end();
                        ?>
                    </div>

                    <div class="clear"></div>
                </div>
            </div>

            <?if (!empty($arResult["SALE_CATION"])):?>
                <?=$arResult["SALE_CATION"]?>
            <?endif?>

            <?if ($arResult["SEO_TEXT"]):?>
                <div class="info_block_incart">
                    <div class="readmore">
                        <?=$arResult["SEO_TEXT"]?>
                    </div>
                </div>
            <?endif?>

            <div class="tabs">
                <!--<a name="comment"></a>-->
                <ul class="tabNavigation">
                    <li>
                        <a title="Нажмите, чтобы выбрать вкладку" class="otovare" href="#item_about">О товаре</a>
                    </li>
                    <?if ($arResult["DISPLAY_PROPERTIES"]):?>
                        <li>
                            <a title="Нажмите, чтобы выбрать вкладку" class="harakter" href="#item_har">Характеристики</a>
                        </li>
                    <?endif?>
                    <?if ($arResult["PROPERTIES"]["EQUIPMENT"]["PRINT_VALUE"] || $arResult["PROPERTIES"]["EQUIPMENT_HTML"]["~VALUE"]["TEXT"]):?>
                        <li>
                            <a title="Нажмите, чтобы выбрать вкладку" class="komplekt" href="#item_comp">Комплектация</a>
                        </li>
                    <?endif?>
                    <?if ($arResult["PROPERTIES"]["LINK_VIDEO"]["VALUE"]):?>
                        <li>
                            <a title="Нажмите, чтобы выбрать вкладку" class="vid" href="#item_video">Видео</a>
                        </li>
                    <?endif?>
                    <li <?=($arResult["COMMENTS_COUNT"] <= 1) ? 'style="display:none"' : '' ?>>
                        <a title="Нажмите, чтобы выбрать вкладку" class="otzuv" href="#item_rev">Отзывы<span class="counts"><?=$arResult["COMMENTS_COUNT"]?></span></a>
                    </li>
                </ul>

				<div id="item_about">
					<?if ($arResult["PROPERTIES"]["COUNTRY_PRODUCER"]["VALUE"]):?>
						<p><strong><span class="red">Страна производитель:</span></strong> <?=$arResult["PROPERTIES"]["COUNTRY_PRODUCER"]["VALUE"]?></p>
					<?endif?>
					<?if ($arResult["PROPERTIES"]["COUNTRY_COLLECTOR"]["VALUE"]):?>
						<p><strong><span class="red">Страна сборщик:</span></strong> <?=$arResult["PROPERTIES"]["COUNTRY_COLLECTOR"]["VALUE"]?></p>
					<?endif?>

					<?if ($arResult["DETAIL_TEXT"]):?>
						<div itemprop="description">
							<?=$arResult["DETAIL_TEXT"]?>
						</div>
					<?endif?>
				</div>

                <div id="item_har">
                    <?if ($arResult["DISPLAY_PROPERTIES"]):?>
                        <div class="display-properties">Технические характеристики <span><?=$arResult["NAME_MORF_2"]?>:</span></div>
                        <ul class="eshop-item-param">
                            <?foreach ($arResult["DISPLAY_PROPERTIES"] as $arProp):?>
                                <?if ($arProp["DISPLAY_VALUE"]):?>
                                    <li>
                                        <p class="eshop-item-param-name">
                                            <?if ($arProp["HELP_ID"]):?>
                                                <a title="Нажмите, для подробного описания данного параметра." class="eshop-item-small__field-details-link prop-popup" data-caption="<?=$arProp["NAME"]?>" data-fancybox-type="iframe" href='/help/?block=7&id=<?=$arProp["HELP_ID"]?>'><?=$arProp["NAME"]?><i class="inform_symb"></i></a>
                                            <?else:?>
                                                <?=$arProp["NAME"]?>:
                                            <?endif?>
                                        </p>
                                        <p class="eshop-item-param-value"><?=$arProp["DISPLAY_VALUE"]?> <?=$arProp["HINT"]?></p>
                                    </li>
                                <?endif?>
                            <?endforeach?>
                        </ul>
                    <?endif?>

                    <div class="items-2">
                        <?if ($arResult["PROPERTIES"]["FEATURES"]["PRINT_VALUE"]):?>
                            <div class="item">
                                <div class="eshop-item-detailed__custom-field-name">
                                    Отличительные особенности:
                                </div>

                                <?foreach ($arResult["PROPERTIES"]["FEATURES"]["PRINT_VALUE"] as $arFeature):?>
                                    <p>
                                        <?if ($arFeature["UF_FULL_DESCRIPTION"] || $arFeature["UF_PICTURE"]):?>
                                            <a title="Нажмите, для подробного описания данного параметра." class="eshop-item-small__field-details-link prop-popup" data-caption="<?=$arFeature["UF_NAME"]?>" data-fancybox-type="iframe" href='/help/?block=2&id=<?=$arFeature["ID"]?>'><?=$arFeature["UF_NAME"]?><i class="inform_symb"></i></a>
                                        <?else:?>
                                            <span><?=$arFeature["UF_NAME"]?></span>
                                        <?endif?>
                                    </p>
                                <?endforeach?>
                            </div>
                        <?endif?>
                    </div>
                </div>

                <div id="item_comp">
                    <?if ($arResult["PROPERTIES"]["EQUIPMENT"]["PRINT_VALUE"] || $arResult["PROPERTIES"]["EQUIPMENT_HTML"]["VALUE"]):?>
                        <div class="eshop-item-detailed__custom-field-name">
                            Комплектация
                        </div>
                        <?if ($arResult["PROPERTIES"]["EQUIPMENT"]["PRINT_VALUE"]):?>
                            <?foreach ($arResult["PROPERTIES"]["EQUIPMENT"]["PRINT_VALUE"] as $arEquipment):?>
                                <p>
                                    <?if ($arEquipment["UF_FULL_DESCRIPTION"] || $arEquipment["UF_FILE"]):?>
                                        <a title="Нажмите, для подробного описания данного параметра." class="eshop-item-small__field-details-link prop-popup" data-caption="<?=$arProp["NAME"]?>" data-fancybox-type="iframe" href='/help/?block=3&id=<?=$arEquipment["ID"]?>'><?=$arEquipment["NAME"]?><i class="inform_symb"></i></a>
                                    <?else:?>
                                        <span><?=$arEquipment["UF_NAME"]?></span>
                                    <?endif?>
                                </p>
                            <?endforeach?>
                        <?else:?>
                            <?=$arResult["PROPERTIES"]["EQUIPMENT_HTML"]["~VALUE"]["TEXT"]?>
                        <?endif?>
                    <?endif?>
                </div>

				<div id="item_video">
					<?if ($arResult["PROPERTIES"]["LINK_VIDEO"]["VALUE"]):?>
						<?foreach ($arResult['PROPERTIES']["LINK_VIDEO"]["VALUE"] as $value):?>
							<iframe width="480" height="360" src="<?=$value?>" frameborder="0" allowfullscreen></iframe>
						<?endforeach;?>
					<?endif?>
				</div>


				<?$APPLICATION->IncludeComponent(
					"custom:comments.prototype",
					"",
					array(
						"ELEMENT_ID" => $arResult["ID"],
                        "ELEMENT_NAME_CLEAN" => $arResult["NAME"],
						"ELEMENT_NAME" => $arResult["NAME_MORF_1"],
						"IBLOCK_ID" => (SITE_ID == "tp") ? "39" : "38",
					),
					false,
					array(
						"HIDE_ICONS" => "Y"
					)
				);?>
			</div>
		</div>
	</div>
</div>
<div class="clear"></div>

<?if (SITE_ID == "by" &&
    ($arResult["PROPERTIES"]["PRODUCER"]["VALUE"] ||
     $arResult["PROPERTIES"]["IMPORTER"]["VALUE"] ||
     $arResult["PROPERTIES"]["SERVICE_CENTERS"]["VALUE"])):?>
    <div class="textinfo red-line">
        <div class="grey_block">
            <ul class="info-list-by">
                <?if ($arResult["PROPERTIES"]["PRODUCER"]["VALUE"]):?>
                    <li class="icon-1">
                        <label>Производитель:</label>
                        <?=$arResult["PROPERTIES"]["PRODUCER"]["VALUE"]?>
                    </li>
                <?endif?>

                <?if ($arResult["PROPERTIES"]["IMPORTER"]["VALUE"]):?>
                    <li class="icon-2">
                        <label>Поставщик:</label>
                        <?=$arResult["PROPERTIES"]["IMPORTER"]["VALUE"]?>
                    </li>
                <?endif?>

                <?if ($arResult["PROPERTIES"]["SERVICE_CENTERS"]["VALUE"]):?>
                    <li class="icon-3">
                        <label>Сервисный центр:</label>
                        <?=$arResult["PROPERTIES"]["SERVICE_CENTERS"]["VALUE"]?>
                    </li>
                <?endif?>
            </ul>
        </div>
    </div>
<?endif?>

<div class="page_f item-footer point">
    <div class="zxt">
        <p class="title">Условия обмена и возврата товара:</p>
        <p>Если в течение 14 дней со дня получения товара Вы обнаружите в нем неисправность или брак деталей, немедленно обратитесь к нам по тел. 8 (800) 333-71-83. По Вашему желанию мы произведем замену товара на другой или примем неисправный прибор и вернем Вам деньги.</p>
        <div class="separator"></div>
        <p class="title">Внимание!</p>
        <p>Пожалуйста, обязательно сохраняйте гарантийный талон и кассовый чек в течение 2-х недель, потому что в случае брака или возврата мы сможем быстрее заменить товар или вернуть Вам ваши деньги.</p>
    </div>
    <div class="wrap-callback item">
	    <div class="custom-form-prototype-footer-callback">
        <div class="title">Остались вопросы?</div>
		<div class="callback-header-2">Мы знаем про <?= (empty($arResult["NAME_MORF_1"])) ? "швейные машины" : $arResult["NAME_MORF_1"] ?> абсолютно все!</div>
		<div class="callback-subheader-2">Перезвоним через <b>28 секунд</b>, проконсультируем, дадим самую низкую цену в г. <?=$arResult["GEO_REGION_CITY_NAME"]?>!</div>
		<div class="callback-subheader-3" style="margin-bottom:12px;display:none;">Наш эксперт перезвонит Вам через <b>28 секунд</b> и ответит на все возникшие вопросы!</div>
			<?$APPLICATION->IncludeComponent(
				"custom:form.prototype",
				"footer-callback",
				array(
					"FORM_ID" => 1,
					"FORM_ACTION" => "FORM_CALLBACK",
					"SUCCESS_MESSAGE" => "Спасибо, что выбрали нас! Мы перезвоним Вам в ближайшее время!",
					"YANDEX_COUNER" => "ostalis_voprosy_element",
					/*"YAN_SITE" => "s1",*/
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
    </div>
    <div class="clear"></div>
</div>

<?if ($arResult["GIFT_WRAPS"]):?>
    <div id="gift_wrap" class="fancybox_block">
    </div>
<?endif?>

<?if ($arResult["OFFERS"]):?>
    <div id="popup-offers" class="fancybox_block">
        <div class="gift_blocks">
            <?foreach ($arResult["OFFERS"] as $arOffer):?>
                <div class="gift_block">
                    <div class="img"><img src="<?=$arOffer["RESIZE_PICTURE"]["SRC"]?>" alt="<?=$arOffer["NAME"]?>" /></div>
                    <div class="name"><?=$arOffer["PROPERTIES"]["VENDOR_CODE"]["VALUE"]?></div>
                    <div class="price"><?=$arOffer["REGION_PRICE"]["DISCOUNT_VALUE"]?> руб.<?if ($arOffer["CATALOG_MEASURE_NAME"] != "шт"):?> / <?=$arOffer["CATALOG_MEASURE_NAME"]?><?endif?></div>
                    <div class="buy_button incart_input scale-decrease" data-id="<?=$arOffer["ID"]?>" data-path="<?=$arOffer["ADD_URL"]?>" style="background-size:auto;width:131px !important;padding:0;background-position:0;">
                        <span class="acs-bay-btn"><i class="fa fa-shopping-cart"></i> Купить</span>
                    </div>
                </div>
            <?endforeach?>
            <div class="footer_button_block">
                <a href="#close-fancybox" class="silver_button">Продолжить покупки</a>
                <a href="/cart/" class="red_button">Оформить заказ</a>
            </div>
        </div>
    </div>
<?endif?>

<script>
    (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
        try{ rrApi.view(<?=$arResult["ID"]?>); } catch(e) {}
    })
</script>

<!-- CRITEO -->

<script src="//static.criteo.net/js/ld/ld.js" async></script>
<script>
var deviceType = /iPad/.test(navigator.userAgent)?"t":/webOS|Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent)?"m":"d";
window.criteo_q = window.criteo_q || [];
window.criteo_q.push(
    { event: "setAccount", account: 38714 },
    { event: "setEmail", email: "<?=$USER->GetEmail();?>" },
    { event: "setSiteType", type: deviceType },
    { event: "viewItem", item: "<?=(!empty($arResult["XML_ID"])) ? $arResult["XML_ID"] : $arResult["ID"]?>" }
);
</script>
<!-- CRITEO -->

<?$APPLICATION->IncludeComponent(
    "custom:targetmail.prototype",
    "",
    array(
        "ID" => "2791918",
        "PAGE_CATEGORY" => "/catalog/index.php",
        "PAGE_PRODUCT" => "/catalog/detail/index.php",
        "PAGE_CART" => "/^\/cart/",
        "PAGE_PURCHASE" => "/^\/order/",
        "PRODUCT_ID" => (!empty($arResult["XML_ID"])) ? $arResult["XML_ID"] : $arResult["ID"],
        "TOTAL_VALUE" => $arResult["REGION_PRICE"]["DISCOUNT_VALUE"],
        "LIST" => "1"
    ),
    false,
    array(
        "HIDE_ICONS" => "Y"
    )
);?>

<?
$productId = (!empty($arResult["XML_ID"])) ? $arResult["XML_ID"] : $arResult["ID"];
$APPLICATION->IncludeComponent(
    "custom:gdeslon.prototype",
    "",
    array(
        "MID" => "88568",
        "PAGE_CATEGORY" => "/catalog/index.php",
        "PAGE_PRODUCT" => "/catalog/detail/index.php",
        "PAGE_CART" => "/^\/cart/",
        "PAGE_PURCHASE" => "/^\/order/",
        "PAGE_SEARCH" => "/^\/search/",
        "CODES" => $productId . ':' . intval($arResult["CATALOG_PRICE_1"]),
        "CAT_ID" => ""
    ),
    false,
    array(
        "HIDE_ICONS" => "Y"
    )
);?>