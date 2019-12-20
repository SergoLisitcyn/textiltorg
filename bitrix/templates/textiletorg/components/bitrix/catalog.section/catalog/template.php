<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if (isset($_REQUEST["AJAX_PAGEN"]) && $_REQUEST["AJAX_PAGEN"] == "Y")
{
    $APPLICATION->RestartBuffer();
    header('Content-Type: text/html; charset='.LANG_CHARSET);
}
?>

<div class = "n_c_seo_t" >
    Швейные машинки по супер ценам! в сети магазинов "Текстильторг"
</div>

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

<div class = "n_c_gid" id = "n_c_gid" >
    гид по выбору
</div>



<div id = "n_c_h_catalog_content" >

<div class = "n_c_prezent" id = "n_c_prezent" >
    <?if(isset($_REQUEST["SECTION_CODE"]))
    {
        $arUFNames = Array("UF_ACTION", "UF_H1_TITLE", "UF_ACTION_MORE", "UF_H1_TITLE_SPB");
        $res = CIBlockSection::GetList(Array(), Array("IBLOCK_ID" => 8, "CODE" => $_REQUEST["SECTION_CODE"]), false, $arUFNames);
        if($arResult1 = $res->GetNext())
        {
            if ($arResult1["UF_ACTION"] && empty($arResult1["UF_H1_TITLE"]) && empty($arResult1["UF_ACTION_MORE"]))
            {
                $arResult1["SECTION_ACTION"] = '<div class="eshop-cat-detailed"><div class="eshop-cat-detailed__announce"><div class="attention">#TEXT#</div></div></div>';
                $arResult1["SECTION_ACTION"] = str_replace("#TEXT#", $arResult1["~UF_ACTION"], $arResult1["SECTION_ACTION"]);

                echo $arResult1["SECTION_ACTION"];
            }

            if ($arResult1["UF_H1_TITLE"])
            {
                $arResult1["SECTION_H1_TITLE"] = '<h1 class="catalog-action-title'.(empty($arResult1["UF_ACTION_MORE"]) ? '-single' : '').'">'.$arResult1["UF_H1_TITLE"].'</h1>';
                $h1 = $arResult1["UF_H1_TITLE"];
                echo $arResult1["SECTION_H1_TITLE"];
            }

            if ($arResult1["UF_ACTION_MORE"])
            {
                $arResult1["SECTION_ACTION_MORE"] = '<div class="section-title-action-description">#TEXT#</div>';
                $arResult1["SECTION_ACTION_MORE"] = str_replace("#TEXT#", $arResult1["~UF_ACTION_MORE"], $arResult1["SECTION_ACTION_MORE"]);
                $arResult1["SECTION_ACTION_MORE"] = str_replace("#TEXT#", $arResult1["~UF_ACTION_MORE"], $arResult1["SECTION_ACTION_MORE"]);
                $arResult1["SECTION_ACTION_MORE"] = str_replace("30.09.2019", date("t.m.Y"), $arResult1["SECTION_ACTION_MORE"]);
                echo $arResult1["SECTION_ACTION_MORE"];
            }
        }
    }
    ?>
</div>

<?if ($arResult["ITEMS"]):?>
    <div class="grid-list">
        <div id = "n_grid_items" class="itemgrid">
            <?foreach ($arResult["ITEMS"] as $arItem):?>
				<?
                if (preg_match("/spb.textiletorg/i",$_SERVER["SERVER_NAME"])) {
                    if (!empty($arItem["PROPERTIES"]["SPB_PREVIEW_TEXT"]["VALUE"]["TEXT"])) {
                       $desc = $arItem["PROPERTIES"]["SPB_PREVIEW_TEXT"]["VALUE"]["TEXT"];
                     } else {
                       $desc = $arItem["~PREVIEW_TEXT"];
                    }
                } else {
                 if((intval($arItem["IBLOCK_SECTION_ID"]) === 848) and (!empty($arItem["PROPERTIES"]["UTSEN_REASON"]["VALUE"]))) {
                     $desc = $arItem["~PREVIEW_TEXT"];
                 } else {
                     $desc = $arItem["~PREVIEW_TEXT"];
                 }
               }
                $desc = substr($desc, 0, 200);
                $desc = substr($desc, 0, strrpos($desc, ' '));
                $desc = $desc.'...';


                $gdeslonCodes .= $arItem['XML_ID'] .':'. intval($arItem['CATALOG_PRICE_1']) . ',';
				$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => "Будет удалена вся информация, связанная с этой записью. Продолжить?"));
				?>
				<?if((intval($arItem[CATALOG_QUANTITY])== 0) and (intval($arItem["IBLOCK_SECTION_ID"]) === 848)):?>

				<?else:?>
				<div class="item <?if ($arItem["PHOTOS"]):?>sub_img<?endif?> shadow-box">


                    <div style = "width:81%;" class="n_grid n_catalog_name name name-catalog-category-<?=$arItem["IBLOCK_SECTION_ID"];?>">
                        <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>

                        <? if (!empty($arItem["ACTIONS"])): ?>
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
                        <? endif ?>
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
					<div class="item_content">
						<div class="wrap_blok_i">

							<div class="left">
								<div class="inner" style="position:relative;" >
									<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="eshop-item-img-link">
										<img class="eshop-item-small__img" src="<?=$arItem["RESIZE_PICTURE"]["SRC"]?>" title="Фото <?=$arItem["NAME"]?> | Швейный магазин Текстильторг" alt="Фото <?=$arItem["NAME"]?> | Швейный магазин Текстильторг" width="<?=$arItem["RESIZE_PICTURE"]["WIDTH"]?>" height="<?=$arItem["RESIZE_PICTURE"]["HEIGHT"]?>">
                                        <? if($arItem["PROPERTIES"]["IS_NEW"]["VALUE_XML_ID"] == "Y"): ?>
                                            <img class = "n_c_new" alt="" src="<?=SITE_TEMPLATE_PATH?>/n_img/new.png" />
                                        <? endif; ?>
                                    </a>
									<? if ($arItem["PROPERTIES"]["IS_PREMIUM"]["VALUE_XML_ID"] == "Y") { ?>
										<div class="spring_marafon_min"></div>
									<? } ?>

									<?if ($arItem["GOOD_ACTIONS"]):?>
										<div class="red_tape_min"></div>
									<?endif?>

									<?if ($arItem["PHOTOS"]):?>
										<ul class="inner_sub">
											<? $num = 1; ?>
											<?foreach ($arItem["PHOTOS"] as $nPhoto => $arPhoto):?>
												<?if ($nPhoto < 4):?>
													<li <?if ($nPhoto == 0):?> class="active"<?endif?>>
														<a href="<?=$arPhoto["DETAIL"]["SRC"]?>"><img src="<?=$arPhoto["PREVIEW"]["SRC"]?>" title="Фото <?=$num?> <?=$arItem["NAME"]?> | Текстильторг" alt="Фото <?=$num?> <?=$arItem["NAME"]?> | Текстильторг" width="<?=$arPhoto["PREVIEW"]["WIDTH"]?>" height="<?=$arPhoto["PREVIEW"]["HEIGHT"]?>" /></a>
													</li>
												<?endif?>
												<? $num++; ?>
											<?endforeach?>
										</ul>
									<?endif?>
								</div>
							</div>

							<div class="center">

                                <div class="n_list n_catalog_name name name-catalog-category-<?=$arItem["IBLOCK_SECTION_ID"];?>">
                                   <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
                                    <? if (!empty($arItem["ACTIONS"])): ?>
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
                                    <? endif ?>
                                </div>


								<div class="desc">
                                    <div class="eshop-item-small__spec-announce"><!--noindex--><?=$desc?><!--/noindex--></div>
								</div>


							</div>
							<?//p($arItem["IS_ICONS"])?>
							<?if ($arItem["IS_ICONS"] || $arItem["GOOD_ACTIONS"]):?>
								<ul class="table_icons">
									<?if ($arItem["PROPERTIES"]["PRODUCT_CERTIFIED"]["VALUE_XML_ID"] == "Y"):?>
										<li class="tooltip-message" data-tooltipe-text="Товар прошел сертификацию."><img alt="" style = "width: 35px;" class="rst_ic-min" src="/bitrix/templates/textiletorg/n_img/6933948b8a.jpg"><div>Товар сертифицирован</div></li>
									<?endif?>

									<?if (!empty($arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
										<?// Textiletorg props?>
										<?if (in_array(1083, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
											 <li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки всех типов тканей."><img style = "width: 35px;" alt="" class="cloth1_ic-min" src="/bitrix/templates/textiletorg/n_img/4104b508b3.jpg"><div>Для всех типов ткани</div></li>
										<?else:?>
											<?if (in_array(1084, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
												<li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки легких и сверхлегких типов тканей."><img style = "width: 35px;" alt=""  class="cloth2_ic-min" src="/bitrix/templates/textiletorg/n_img/4104b508b3_3.jpg"><div>Для легких и сверхлегких тканей</div></li>
											<?endif?>
											<?if (in_array(1085, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
												<li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки легких и средних типов тканей."><img style = "width: 35px;" alt="" class="cloth3_ic-min" src="/bitrix/templates/textiletorg/n_img/4104b508b3_2.jpg"><div>Для легких и средних тканей</div></li>
											<?endif?>
											<?if (in_array(1086, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
												<li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки тяжелых и сверхтяжелых типов тканей."><img style = "width: 35px;" alt="" class="cloth4_ic-min" src="/bitrix/templates/textiletorg/n_img/4104b508b3_1.jpg"><div>Для тяжелых и сверхтяжелых тканей</div></li>
											<?endif?>
										<?endif?>
										<?// TTProm props?>
										<?if (in_array(1293, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
											 <li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки всех типов тканей."><img style = "width: 35px;" alt="" class="cloth1_ic-min" src="/bitrix/templates/textiletorg/n_img/4104b508b3.jpg"><div>Для всех типов ткани</div></li>
										<?else:?>
											<?if (in_array(1298, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
												<li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки легких и сверхлегких типов тканей."><img style = "width: 35px;" alt="" class="cloth2_ic-min" src="/bitrix/templates/textiletorg/n_img/4104b508b3_3.jpg"><div>Для легких и сверхлегких тканей</div></li>
											<?endif?>
											<?if (in_array(1294, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
												<li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки легких и средних типов тканей."><img style = "width: 35px;" alt="" class="cloth3_ic-min" src="/bitrix/templates/textiletorg/n_img/4104b508b3_2.jpg"><div>Для легких и средних тканей</div></li>
											<?endif?>
											<?if (in_array(1295, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
												<li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки тяжелых и сверхтяжелых типов тканей."><img style = "width: 35px;" alt="" class="cloth4_ic-min" src="/bitrix/templates/textiletorg/n_img/4104b508b3_1.jpg"><div>Для тяжелых и сверхтяжелых тканей</div></li>
											<?endif?>
										<?endif?>
									<?endif?>

									<?if ($arItem["PROPERTIES"]["GIFT_COUPON_25"]["VALUE_XML_ID"] == "Y"):?>
										<li class="tooltip-message" data-tooltipe-text="При покупке текущего товара в подарок купон со скидкой 25%."><img alt="" style = "width: 35px;" class="scid_ic-min" src="/bitrix/templates/textiletorg/n_img/990efe3ee8.jpg"><div style = "padding-top: 10px;">В подарок купон 25%</div></li>
									<?endif?>
									<?if ($arItem["PROPERTIES"]["EXPERT_ADVICE"]["VALUE_XML_ID"] == "Y"):?>
										<li class="tooltip-message" data-tooltipe-text="Товар рекомендован экспертом."><img alt="" class="recom_ic-min" style = "width: 35px;" src="/bitrix/templates/textiletorg/n_img/ca1fde2ffd.jpg"><div style = "padding-top: 9px;">Рекомендация эксперта</div></li>
									<?endif?>

									<?foreach ($arItem["GOOD_ACTIONS"] as $arAction):?>
										<li>
											<img alt="" class="acsia_ic_min" src="/bitrix/images/sprites/icon-back-min.png<?/*=$arAction["ICON_GOOD"]*/?>">
											<a href="<?=$arAction["DETAIL_PAGE_URL"]?>" target="_blank">участник акции «<?=$arAction["NAME"]?>»</a>
										</li>
									<?endforeach?>
								</ul>
							<?endif?>
						</div>


						<div class="right">
                            <div class="price sd">
								<?/*------ utsenennye-tovary ------*/

							if(intval($arItem["IBLOCK_SECTION_ID"]) === 848):?>
                                <?if (intval($arItem["REGION_PRICE"]["DISCOUNT_VALUE"])):?>
									<?if ($ar_res = $db_res->Fetch()){?>
											<div class="old-price"><span>Цена:  </span><div class="old-span"><?=number_format((strval($ar_res["PRICE"])),0,'.',' ').' '/*=strval($ar_res["PRICE"])=$arItem["PROPERTIES"]["PRICE_DISC"]["VALUE"]*/?><small>   руб.</small></div> </div>
											<?}?>
											<div class="new-price"><?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?> <small>руб.</small></div>


								<?else:?>
                                    <span>Цена:</span> на заказ
                                <?endif?>
								   </div>
							<?else:?>


								    <?if (intval($arItem["REGION_PRICE"]["DISCOUNT_VALUE"])):?>

                                        <div class = "n_price_g">Цена: </div> <span class = "n_price_l">Цена: </span> <?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?> <small>руб.<?if ($arItem["CATALOG_MEASURE_NAME"] != "шт"):?> / <?=$arItem["CATALOG_MEASURE_NAME"]?><?endif?></small>


                                    <?else:?>


                                        <div class="n_price">
                                            Цена: <div class = "n_price_catalog"> <? echo number_format((strval($arItem['CATALOG_PRICE_1'])),0,'.',' '); ?> <span> руб. </span> </div>
                                        </div>

                                <?endif?>
								   </div>

							<?endif?>


                            <?if ($arItem["REGION_PRICE"]["VALUE_NOVAT"] > $arItem["REGION_PRICE"]["DISCOUNT_VALUE_NOVAT"]):?>
                                <div class="old-price">
                                    Старая цена: <big><?=$arItem["REGION_PRICE"]["VALUE"]?></big> <small>руб.</small>
                                </div>
                            <?elseif (!empty($arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"])):?>
                                <div class="old-price">
                                    Старая цена: <big><?=$arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]?></big> <small>руб.</small>
                                </div>
                            <?endif?>

<?
$yaCounter = "1021532";
if($_SERVER["SERVER_NAME"] == "spb.textiletorg.ru") {
	$yaCounter = "48343148";
}
?>
                            <div class="right_button_block">
                                <div class="buybtn">
                                    <?if ($arItem["OFFERS"]):?>
                                        <a href="#popup-offers-<?=$arItem["ID"]?>" class="inyourcart scale-decrease fancybox" data-id="<?=$arItem["ID"]?>" <?if(SITE_ID =="s1"):?>onclick="yaCounter<?=$yaCounter?>.reachGoal('Open_Cart'); return true;"<?endif;?> data-path="<?=$arItem["ADD_URL"]?>" title="Выбор цвета"></a>
                                    <?else:?>
                                        <button onmousedown="try { rrApi.addToBasket(<?=$arItem["ID"]?>) } catch(e) {}" type="submit" class="inyourcart scale-decrease" data-id="<?=$arItem["ID"]?>" <?if(SITE_ID == "s1"):?>onclick="yaCounter<?=$yaCounter?>.reachGoal('Open_Cart'); return true;"<?endif;?> data-path="<?=$arItem["ADD_URL"]?>" data-name="<?=$arItem["NAME"]?>" data-picture="<?=$arItem["RESIZE_PICTURE"]["SRC"]?>" data-vendor="<?=$arItem["PROPERTIES"]["VENDOR_CODE"]["VALUE"]?>" data-price="<?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?>" data-price-rb="<?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?>">
                                            КУПИТЬ
                                        </button>
                                    <?endif?>
                                </div>

                                <div class="one-click-css clearfix">
                                    <a  class="eshop-item-small__one-click scale-decrease buy-one-click" onclick="yaCounter<?=$yaCounter?>.reachGoal('zakaz_1_click'); return true;" href="#buy_one_click_form" title="Оформление заказа в 1 клик" data-good-id="<?=$arItem["ID"]?>">Купить в 1 клик</a>
                                </div>
                            </div>

                        <div class="n_c_child n_c_child_grid"> Покупая у нас Вы помогаете детям!
                            <div class = "n_help n_help_child" data-tool-text="Часть средств от продажи товаров, направляется в Детский дом (название уточним)" ></div>
                        </div>


								<?if(intval($arItem["IBLOCK_SECTION_ID"]) != 848):?>
                            <?if (!empty($arItem["CREDIT"]) && 1 == 2):?>
                                <div class="creditprice_first_pay b-container-popup-credit b-container-popup-credit-credit">
                                    В кредит: <span class="credit_on tooltip-message-click" data-tooltipe-text="Размер платежа является предварительным при условии первоначального взноса 10%, срок кредита 24 месяцев.<br><br>Точные даты и размеры ежемесячных платежей будут указаны в Графике платежей, являющемся неотъемлемой частью Кредитного договора.<br><br>Кредит предоставляется на общую сумму покупки от 3 000 руб."><?=$arItem["CREDIT"]?> <small>руб.</small>/мес.</span>
                                </div>
                            <?endif?>


                            <?if (!empty($arItem["CREDIT"]) || $arItem["PROPERTIES"]["PAYMENT_INSTALLMENTS"]["VALUE_XML_ID"] == "Y"):?>
                                <div class="blue as-credit-info">
                                    <?if (!empty($arItem["CREDIT"])):?>
                                        <span class="credit_des inf_3000 tooltip-message-click" data-tooltipe-text="Кредит предоставляется на общую сумму покупки от 3 000 руб.<br><br>Вы можете приобрести данный товар в кредит, в наших магазинах. Подробнее в разделе «<a href='/informacija/kredit/' target='_blank'>Кредит</a>»">Расрочка 0% и кредит <i class="ico_ques"></i></span>
                                    <?endif?>
                                    <?if ($arItem["PROPERTIES"]["PAYMENT_INSTALLMENTS"]["VALUE_XML_ID"] == "Y"):?>
                                        <span class="credit_des inf_rassrochka tooltip-message-click" data-tooltipe-text="Вы можете приобрести данный товар в рассрочку - без процентов, в наших магазинах. Подробнее в разделе «<a href='/akcii/rassrochka/' target='_blank'>Рассрочка</a>»">Рассрочка: <span>0%</span> <i class="ico_ques"></i></span>
                                    <?endif?>
                                </div>
                            <?endif?>
								<?endif?>
                            <div class="clear"></div>
                            <?if ($arItem["QUANTITY_TEXT"]):?>
                                <div class="instock ltl" style = "position: relative;">В наличии: <?if(intval($arItem["IBLOCK_SECTION_ID"]) == 848):?><span style = "text-transform: capitalize;"><?=intval($arItem[CATALOG_QUANTITY])?><?else:?><span style = "text-transform: capitalize; font-weight: bold; color: #222222d1;"><?=$arItem["QUANTITY_TEXT"]?></span>
                                <? if ($arItem["QUANTITY_TEXT"] == 'много'): ?>
                                            <img style = "width: 35px; position: absolute; top: -8px; right: 20px; " alt="" src="/bitrix/templates/textiletorg/n_img/10d7b857cd.jpg">
                                <?endif?>
                                <? if ($arItem["QUANTITY_TEXT"] == 'мало'): ?>
                                    <img style = "width: 35px; position: absolute; top: -8px; right: 20px; " alt="" src="/bitrix/templates/textiletorg/n_img/29ffdbf4c2.jpg">
                                <?endif?>
                                        <?endif?>
                                </div>
                            <?endif?>



                        <?if ($arItem["RESERVED_TEXT"] && 1 == 2):?>
                                <div class="instock ltl">В наличие на доставку: <?if(intval($arItem["IBLOCK_SECTION_ID"]) == 848):?><span><?=intval($arItem[CATALOG_QUANTITY])?><?else:?><span><?=$arItem["RESERVED_TEXT"]?></span><?endif?> </div>
                            <?endif?>


                        <?if ($arItem["IS_ICONS"] || $arItem["GOOD_ACTIONS"]):?>
                            <ul class="table_icons n_grid">
                                <?if ($arItem["PROPERTIES"]["PRODUCT_CERTIFIED"]["VALUE_XML_ID"] == "Y"):?>
                                    <li class="tooltip-message" data-tooltipe-text="Товар прошел сертификацию."><img alt="" style = "width: 30px;" class="rst_ic-min" src="/bitrix/templates/textiletorg/n_img/6933948b8a.jpg"><div>Товар сертифицирован</div></li>
                                <?endif?>

                                <?if (!empty($arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                                    <?// Textiletorg props?>
                                    <?if (in_array(1083, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                                        <li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки всех типов тканей."><img style = "width: 30px;" alt="" class="cloth1_ic-min" src="/bitrix/templates/textiletorg/n_img/4104b508b3.jpg"><div>Для всех типов ткани</div></li>
                                    <?else:?>
                                        <?if (in_array(1084, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                                            <li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки легких и сверхлегких типов тканей."><img style = "width: 30px;" alt=""  class="cloth2_ic-min" src="/bitrix/templates/textiletorg/n_img/4104b508b3_3.jpg"><div>Для легких и сверхлегких тканей</div></li>
                                        <?endif?>
                                        <?if (in_array(1085, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                                            <li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки легких и средних типов тканей."><img style = "width: 30px;" alt="" class="cloth3_ic-min" src="/bitrix/templates/textiletorg/n_img/4104b508b3_2.jpg"><div>Для легких и средних тканей</div></li>
                                        <?endif?>
                                        <?if (in_array(1086, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                                            <li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки тяжелых и сверхтяжелых типов тканей."><img style = "width: 30px;" alt="" class="cloth4_ic-min" src="/bitrix/templates/textiletorg/n_img/4104b508b3_1.jpg"><div>Для тяжелых и сверхтяжелых тканей</div></li>
                                        <?endif?>
                                    <?endif?>
                                    <?// TTProm props?>
                                    <?if (in_array(1293, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                                        <li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки всех типов тканей."><img style = "width: 30px;" alt="" class="cloth1_ic-min" src="/bitrix/templates/textiletorg/n_img/4104b508b3.jpg"><div>Для всех типов ткани</div></li>
                                    <?else:?>
                                        <?if (in_array(1298, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                                            <li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки легких и сверхлегких типов тканей."><img style = "width: 30px;" alt="" class="cloth2_ic-min" src="/bitrix/templates/textiletorg/n_img/4104b508b3_3.jpg"><div>Для легких и сверхлегких тканей</div></li>
                                        <?endif?>
                                        <?if (in_array(1294, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                                            <li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки легких и средних типов тканей."><img style = "width: 30px;" alt="" class="cloth3_ic-min" src="/bitrix/templates/textiletorg/n_img/4104b508b3_2.jpg"><div>Для легких и средних тканей</div></li>
                                        <?endif?>
                                        <?if (in_array(1295, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                                            <li class="tooltip-message" data-tooltipe-text="Товар пригоден для обработки тяжелых и сверхтяжелых типов тканей."><img style = "width: 30px;" alt="" class="cloth4_ic-min" src="/bitrix/templates/textiletorg/n_img/4104b508b3_1.jpg"><div>Для тяжелых и сверхтяжелых тканей</div></li>
                                        <?endif?>
                                    <?endif?>
                                <?endif?>

                                <?if ($arItem["PROPERTIES"]["GIFT_COUPON_25"]["VALUE_XML_ID"] == "Y"):?>
                                    <li class="tooltip-message" data-tooltipe-text="При покупке текущего товара в подарок купон со скидкой 25%."><img alt="" style = "width: 30px;" class="scid_ic-min" src="/bitrix/templates/textiletorg/n_img/990efe3ee8.jpg"><div style = "padding-top: 2px;">В подарок купон 25%</div></li>
                                <?endif?>
                                <?if ($arItem["PROPERTIES"]["EXPERT_ADVICE"]["VALUE_XML_ID"] == "Y"):?>
                                    <li class="tooltip-message" data-tooltipe-text="Товар рекомендован экспертом."><img alt="" class="recom_ic-min" style = "width: 30px;" src="/bitrix/templates/textiletorg/n_img/ca1fde2ffd.jpg"><div style = "padding-top: 9px;">Рекомендация эксперта</div></li>
                                <?endif?>

                                <?foreach ($arItem["GOOD_ACTIONS"] as $arAction):?>
                                    <li>
                                        <img alt="" class="acsia_ic_min" src="/bitrix/images/sprites/icon-back-min.png<?/*=$arAction["ICON_GOOD"]*/?>">
                                        <a href="<?=$arAction["DETAIL_PAGE_URL"]?>" target="_blank">участник акции «<?=$arAction["NAME"]?>»</a>
                                    </li>
                                <?endforeach?>
                            </ul>
                        <?endif?>

                        <div class="qqq">
                            <a href="<?=$arItem["ADD_COMPARE_URL"]?>" class="add-compare-button add-compare n_c_el_l" data-add-compare-url="<?=$arItem["ADD_COMPARE_URL"]?>" data-delete-compare-url="<?=$arItem["DELETE_COMPARE_URL"]?>">Добавить к сравнению</a>
                        </div>

                        <div class="n_catalog_iz n_grid">
                            <a href="<?=$arItem["ADD_COMPARE_URL"]?>" class="add-compare-button add-compare n_c_el" data-add-compare-url="<?=$arItem["ADD_COMPARE_URL"]?>" data-delete-compare-url="<?=$arItem["DELETE_COMPARE_URL"]?>">
                                <div class = "n_catalog_iz_img"></div>
                            </a>
                        </div>


                        <div class="n_catalog_cards"></div>



                        </div>
                        <div class="clear"></div>

                    <div class = "n_c_child_list_b" >
                        <div class="n_c_child n_c_child_list"> Покупая у нас Вы помогаете детям!
                            <div class = "n_help n_help_child" data-tool-text="Часть средств от продажи товаров, направляется в Детский дом (название уточним)" ></div>
                        </div>
                    </div>

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
                                        <div class="buy_button incart_input scale-decrease" data-id="<?=$arOffer["ID"]?>" data-path="<?=$arOffer["ADD_URL"]?>">
                                            <span class="acs-bay-btn"><i class="fa fa-shopping-cart"></i> Купить</span>
                                        </div>
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
				<?endif;?>
             <?endforeach;?>


			<div class="item last_item <?=($arResult["NAV_RESULT"]->NavPageCount == $arResult["NAV_RESULT"]->NavPageNomer ? "lastpage" : "")?>" style="height: auto;display: <?=($arResult["NAV_RESULT"]->NavPageCount <= 1 ? "none" : "block")?>">
				<div class="name"></div>
				<div class="item_content" style="height:auto;"></div>
			</div>

        </div>
    </div>

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

<script>
	$(function(){
        $("#text").append('<div class = "n_call_line"></div>');
	});
</script>

<br>
<div class="clear"></div>

<br>
<div class="clear"></div>
<div class="custom-form-prototype-footer-callback n_catalog_callback">
    <div class = "n_call_left"></div>
    <div class = "n_call_right"></div>
    <div class="callback-header">Остались вопросы?</div>
    <div class="callback-subheader">Мы знаем про <?= (empty($arResult["UF_TEXT_FOR_FEEDBACK"])) ? "швейные машины" : $arResult["UF_TEXT_FOR_FEEDBACK"] ?> абсолютно все!</div>
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

</div>

<div id = "n_c_h_vibor_content" class = "hide">

    <? $APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        array(
            "AREA_FILE_SHOW" => "file",
            "PATH" => "/include/n_c_h_vibor_content.php",
            "EDIT_TEMPLATE" => "text.php"
        )
    ); ?>

</div>

<div id = "n_c_h_proiz_content" class = "hide" >

    <? $APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        array(
            "AREA_FILE_SHOW" => "file",
            "PATH" => "/include/n_c_h_proiz_content.php",
            "EDIT_TEMPLATE" => "text.php"
        )
    ); ?>

</div>



<?$APPLICATION->IncludeComponent(
	"custom:gdeslon.prototype",
	"",
	array(
		"MID" => "88568",
		"PAGE_CATEGORY" => "/catalog/index.php",
		"PAGE_PRODUCT" => "/catalog/detail/index.php",
		"PAGE_CART" => "/^\/cart/",
		"PAGE_PURCHASE" => "/^\/order/",
		"PAGE_SEARCH" => "/^\/search/",
		"CODES" => rtrim($gdeslonCodes,','),
		"CAT_ID" => $arResult['UF_YM_ID']
	),
	false,
	array(
		"HIDE_ICONS" => "Y"
	)
);?>

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
