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
<?/*$APPLICATION->IncludeComponent(
    "ayers:stores.product",
    "",
    array(
        "CITY" => $GLOBALS["GEO_REGION_CITY_NAME"],
        "IS_FILTER_TYPE" => "Y",
        "COUNT_IN_HINT" => 3,
        "CACHE_TIME" => 36000
    ),
    false,
    array(
        "HIDE_ICONS" => "Y"
    )
);*/?>
<div class="shop_item detail">
    <h1><?=$arResult["NAME"]?></h1>
	<div style="    display: flex;
    justify-content: space-between;
    align-items: center;position:relative;">
		<?if ($arResult["PROPERTIES"]["VENDOR_CODE"]["VALUE"]):?>
			<p class="article">Артикул <b><?=$arResult["PROPERTIES"]["VENDOR_CODE"]["VALUE"]?></b></p>
		<?endif?>
		<?if ($arResult["PROPERTIES"]["D3_GALLERY"]["VALUE"]):?>
			<a onclick="window.open('<?=$arResult["PROPERTIES"]["D3_GALLERY"]["VALUE"]?>', '', 'width=1200, height=850');" href="#" class="grad360"></a>
		<?endif?>
		<? if (!empty($arResult["GOOD_ACTIONS"])): ?>
			<div class="akciya">Акция</div>
			<div class="actions-block">
				<div class="header">Участвует в акции</div>
                <? foreach ($arResult["GOOD_ACTIONS"] as $arAction): ?>
                    <div><a href="<?=$arAction["DETAIL_PAGE_URL"]?>">«<?=$arAction["NAME"]?>»</a></div>
                <? endforeach; ?>
            </div>
		<?endif?>
	</div>

    <!-- <div class="sub_description">
    Товар участвует в акции "Марафон скидок"<br />
    При покупки данной модели мы дарим Вам премиальный сервисный пакет обслуживания
    </div> -->

    <div class="img_block">
		<div class="overlay"></div>
		<div style="display:flex;justify-content:space-between;align-items:center;position:relative;">
		<?if ($arResult["PHOTOS"]):?>
			<ul class="title_img_block_slider">
				<?$active = "active";?><?$i=0?>
                <?foreach ($arResult["PHOTOS"] as $nPhoto => $arPhoto):?>
				<?$i++?>
                    <li><a href="<?=$arPhoto["DETAIL"]["SRC"]?>" target="_blank" class="<?=$active?> open_slider"><img src="<?=$arPhoto["DETAIL"]["SRC"]?>" alt="<?=$arResult["NAME"]?>" class="title_img"></a></li>
					<?$active = "";?>
                <?endforeach?>
            </ul>
			<div class="popup_slider">			
				<div class="popup_slider_header"><div class="popup_slider_close"></div></div>
				<ul class="popup_slider_title_img_block_slider">
					<?$active = "active";?><?$i=0?>
					<?foreach ($arResult["PHOTOS"] as $nPhoto => $arPhoto):?>
					<?$i++?>
						<li><img src="<?=$arPhoto["DETAIL"]["SRC"]?>" alt="<?=$arResult["NAME"]?>" class="title_img"></li>
						<?$active = "";?>
					<?endforeach?>
				</ul>
			</div>
		<?else:?>
			<a class="title_img_block" href="<?=$arResult["RESIZE_PICTURE"]["SRC"]?>">
				<img class="title_img open_slider" src="<?=$arResult["RESIZE_PICTURE"]["SRC"]?>" />
			</a>
			<div class="popup_slider">			
				<div class="popup_slider_header"><div class="popup_slider_close"></div></div>
				<img class="title_img" src="<?=$arResult["RESIZE_PICTURE"]["SRC"]?>" />
			</div>
		<?endif;?>
			
		
			<div style="min-width:155px;position:relative;right:-3px">
				<div class="price_block">
					<?if ($arResult["REGION_PRICE"]["DISCOUNT_VALUE"]):?>
						<strong class="black-strong">Цена:</strong> <span class="sum element-price"><?=$arResult["REGION_PRICE"]["DISCOUNT_VALUE"]?></span> <span class="text-red">руб.</span>
					<?else:?>
						<strong class="black-strong">Цена:</strong> <span class="sum">На заказ</span>
					<?endif?>
				</div>
                <?if ($arResult["REGION_PRICE"]["VALUE_NOVAT"] > $arResult["REGION_PRICE"]["DISCOUNT_VALUE_NOVAT"]):?>
                    <div class="old-price">
                        Старая цена: <big><?=$arResult["REGION_PRICE"]["VALUE"]?></big> <small>руб.</small>
                    </div>
                <?elseif (!empty($arResult["PROPERTIES"]["PRICE_OLD"]["VALUE"]) && SITE_ID == "s1"):?>
                    <div class="old-price">
                        Старая цена: <big><?=$arResult["PROPERTIES"]["PRICE_OLD"]["VALUE"]?></big> <small>руб.</small>
                    </div>
                <?endif?>
				<div class="bay_block" style="margin-top:0">
<?
$yaCounter = "1021532";
if($_SERVER["SERVER_NAME"] == "spb.textiletorg.ru") {
	$yaCounter = "48343148";
}
?>				
                    <?if ($arResult["OFFERS"]):?>
                        <a href="#popup-offers" class="incart_input scale-decrease fancybox"  onclick="<?if(SITE_ID == "s1"):?> yaCounter<?=$yaCounter?>.reachGoal('Open_Cart');return true;<?endif;?>" title="Выбор цвета">
                            <span class="acs-bay-btn"><i class="fa fa-shopping-cart"></i> Купить</span>
                        </a>
					<?else:?>
						<a href="<?=$arResult["ADD_URL"]?>" class="button buy red cart-button buy-cart-button" data-id="<?=$arResult["ID"]?>" data-path="<?=$arResult["ADD_URL"]?>" onclick="yaCounter<?=$yaCounter?>.reachGoal('Open_Cart')" style="padding: 2px 30px!important;background:#ff0000;font-size:16px;box-sizing:border-box;">
                            <i class="fa fa-shopping-cart"></i> Купить
                        </a>
					<?endif;?>
					<br>
					<a href="#buy_one_click" class="button yellow buy-one-click" style="font-size:13px;width:88%;font-family:'AvenirNextCyrBold', Verdana, Tahoma, Arial;box-sizing:border-box;margin-top:5px;line-height:1;padding:8px 10px !important;font-weight:normal;color:#464646;" data-good-id="<?=$arResult["ID"]?>" data-price="<?=$arResult["REGION_PRICE"]["DISCOUNT_VALUE_NOVAT"]?>" data-currency="<?=$arResult["REGION_PRICE"]["CURRENCY"]?>" data-good-name="<?=$arResult["NAME"]?>" data-good-url="<?=$arResult["DETAIL_PAGE_URL"]?>" title="Оформление заказа в 1 клик" onclick="yaCounter<?=$yaCounter?>.reachGoal('zakaz_1_click')">Купить в 1 клик</a>
				</div>
			</div>
		</div>
        <?if ($arResult["PHOTOS"]):?>
            <ul class="img_list_block">
				<?$active = "active";?><?$i=0;?>
                <?foreach ($arResult["PHOTOS"] as $nPhoto => $arPhoto):?>
					<?$i++?>
                    <li><a href="<?=$arPhoto["DETAIL"]["SRC"]?>" target="_blank" class="<?=$active?>"><img src="<?=$arPhoto["PREVIEW"]["SRC"]?>" alt="<?=$arResult["NAME"]?>"></a></li>
					<?$active = "";?>
                <?endforeach?>
            </ul>
        <?endif?>
    </div>
    <?if ($arResult["GUARANTEE"]):?>
        <div class="guarantee"><strong class="black-strong">Гарантия:</strong>&nbsp;<?=$arResult["GUARANTEE"][0]["PRINT_NAME"]?></div>
		<div class="clear"></div>
    <?endif?>
    <?if ($arResult["QUANTITY"]):?>
        <div class="quantity">
            <strong class="black-strong" style="float: left;">В наличии:</strong>
            <div class="count_img inrest_img"><span class="<?=$arResult["QUANTITY"]["CLASS"]?>"></span></div> <span><?=$arResult["QUANTITY"]["TEXT"]?></span>
        </div>
    <?endif?>
	<?if ($arResult["DELIVERY_TEXT"]):?>
        <div class="guarantee open_popup"><strong class="black-strong">Доставка:</strong> <?=$arResult["DELIVERY_TEXT"]?><span class="popup message"><?=$arResult["DELIVERY_TEXT_2"] ? $arResult["DELIVERY_TEXT_2"] : $arResult["DELIVERY_TEXT"]?></span></div>
    <?endif?>
	<?if ($arResult['EXPORT_TEXT']):?>
        <div class="guarantee"><strong class="black-strong margin-delivery tooltip-message-stores">Самовывоз:</strong> <?=$arResult['EXPORT_TEXT']?></div>
    <?endif?>
	<div style="margin:5px 0 10px">
    <?if ($arResult["PROPERTIES"]["PRODUCT_CERTIFIED"]["VALUE_XML_ID"] == "Y"):?>
        <div class="icon-message open_popup">
            <img src="<?=SITE_TEMPLATE_PATH?>/img/rst.png" />
			<div class="icon-message-detail">товар сертифицирован</div>
			<span class="popup message">Товар прошел сертификацию.</span>
        </div>
    <?endif?>        
    <?if (!empty($arResult["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
        <?if (in_array(1083, $arResult["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
             <div class="icon-message open_popup">
				<img src="<?=SITE_TEMPLATE_PATH?>/img/cloth1.png">
				<div class="icon-message-detail">для всех типов ткани</div>
				<span class="popup message">Товар пригодне для обработки всех типов тканей.</span>
			</div>
        <?else:?>
            <?if (in_array(1084, $arResult["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                <div class="icon-message open_popup">
					<img src="<?=SITE_TEMPLATE_PATH?>/img/cloth2.png">
					<div class="icon-message-detail">для легких и сверхлегких тканей</div>
					<span class="popup message">Товар пригодне для обработки легких и сверхлегких тканей.</span>
				</div>
            <?endif?>
            <?if (in_array(1085, $arResult["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                <div class="icon-message open_popup">
					<img src="<?=SITE_TEMPLATE_PATH?>/img/cloth3.png">
					<div class="icon-message-detail">для легких и средних тканей</div>
					<span class="popup message">Товар пригодне для обработки легких и средних тканей.</span>
				</div>
            <?endif?>
            <?if (in_array(1086, $arResult["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
                <div class="icon-message open_popup">
					<img src="<?=SITE_TEMPLATE_PATH?>/img/cloth4.png">
					<div class="icon-message-detail">для тяжелых и сверхтяжелых тканей</div>
					<span class="popup message">Товар пригодне для обработки тяжелых и сверхтяжелых тканей.</span>
				</div>
            <?endif?>
        <?endif?>
    <?endif?>
	<?if ($arResult["PROPERTIES"]["GIFT_COUPON_25"]["VALUE_XML_ID"] == "Y"):?>
        <div class="icon-message open_popup">
			<img src="<?=SITE_TEMPLATE_PATH?>/img/25.png">
			<div class="icon-message-detail">в подарок купон 25%</div>
			<span class="popup message">При покупке текущего товара в подарок купон со скидкой 25%.</span>
		</div>
    <?endif?>
    <?if ($arResult["PROPERTIES"]["EXPERT_ADVICE"]["VALUE_XML_ID"] == "Y"):?>
        <div class="icon-message open_popup">
			<img src="<?=SITE_TEMPLATE_PATH?>/img/recom.png">
			<div class="icon-message-detail">рекомендация эксперта</div>
			<span class="popup message">Товар рекомендован экспертом.</span>
		</div>
    <?endif?>
    <? if ($arResult["MESSAGE_MASTERCLASS_SHOW"]): ?>
        <div class="icon-message open_popup">
			<img src="<?=SITE_TEMPLATE_PATH?>/mod_files/css/images/gift.png">
			<div class="icon-message-detail">Демонстрация функционала моделей производится в любом из наших магазинов абсолютно бесплатно</div>
			<span class="popup message"></span>
		</div>
    <?endif?>
	</div>
	<?if ($arResult["RATING"] && $arResult["COMMENTS_COUNT"] > 0):?>
		<div class="rating" <?=($arResult["RATING"]["COUNT"]) ? '' : 'style="display: none;"'?>>
			<strong class="black-strong" style="float: left;">Рейтинг:</strong>
			<div class="rating rating_s">
				<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
					<meta itemprop="bestRating" content="5">
					<meta itemprop="ratingValue" content="<?=$arResult["RATING"]["COUNT"]?>" />
					<meta itemprop="ratingCount" content="<?=$arResult["RATING"]["VOTES"]?>">
				</div>
				<img src="<?=$arResult["RATING"]["IMAGE"]?>" alt="Рейтинг: <?=$arResult["RATING"]["COUNT"]?>; Голосов: <?=$arResult["RATING"]["VOTES"]?>">
				<div class="clear"></div>
			</div>
		</div>
	<?endif?>
    <div style="display:flex;justify-content: space-between;
    align-items: center; position:relative;">
        <div class="credit" style="background-image:url('<?=SITE_TEMPLATE_PATH?>/components/bitrix/catalog.element/catalog/img/nal.png')"><span>наличный расчет<span class="message">наличный расчет</span></span></div>
        <div class="credit" style="background-image:url('<?=SITE_TEMPLATE_PATH?>/components/bitrix/catalog.element/catalog/img/beznal.png')"><span>безналичный расчет<span class="message">безналичный расчет</span></span></div>
        <div class="credit" style="background-image:url('<?=SITE_TEMPLATE_PATH?>/components/bitrix/catalog.element/catalog/img/online.png')"><span>онлайн оплата<span class="message">онлайн оплата</span></span></div>
		<?if (!empty($arResult["CREDIT"])):?>
			<div class="credit" style="background-image:url('<?=SITE_TEMPLATE_PATH?>/components/bitrix/catalog.element/catalog/img/credit.png')"><span>кредит<span class="message"><a href="/informacija/kredit/" target="_blank">В кредит: <?=$arResult["CREDIT"]?> руб./мес.</a></span></span></div>
		<?else:?>
			<div class="credit" style="background-image:url('<?=SITE_TEMPLATE_PATH?>/components/bitrix/catalog.element/catalog/img/rassrochka.png')"><span>рассрочка<span class="message" target="_blank"><a href="/akcii/rassrochka/">рассрочка 0%</a></span></span></div>		
		<?endif?>
	</div>
<div class="korpus">
	<input type="radio" name="odin" checked="checked" id="vkl1"/><label for="vkl1" style="<?=(!empty($arResult["PROPERTIES"]["LINK_VIDEO"]["VALUE"])) ? "width:33.2%" : "width:49.9%"?>">О товаре</label>
	<input type="radio" name="odin" id="vkl2"/><label for="vkl2" style="<?=(!empty($arResult["PROPERTIES"]["LINK_VIDEO"]["VALUE"])) ? "left:33.4%" : "width:49.9%;left:50.1%"?>">Характеристики</label>
	<?if ($arResult["PROPERTIES"]["LINK_VIDEO"]["VALUE"]):?>
		<input type="radio" name="odin" id="vkl3"/><label for="vkl3" style="left:66.8%">Видео</label>
	<?endif;?>
	<div><?=$arResult["DETAIL_TEXT"]?></div>
	<div>
		<?foreach ($arResult["DISPLAY_PROPERTIES"] as $arProp):?>
            <?if ($arProp["DISPLAY_VALUE"]):?>
                <p><strong class="text-red"><?=$arProp["NAME"]?>:</strong> <?=$arProp["DISPLAY_VALUE"]?> <?if ($arProp["HELP_ID"]):?><a href="/help/?block=7&id=<?=$arProp["HELP_ID"]?>" class="help-info" title="Характеристики">?</a><?endif?></p>
            <?endif?>
        <?endforeach?>
	</div>
	<?if (!empty($arResult["PROPERTIES"]["LINK_VIDEO"]["VALUE"])):?>
		<div>
			<?foreach ($arResult['PROPERTIES']["LINK_VIDEO"]["VALUE"] as $value):?>
				<iframe width="100%" height="360" src="<?=$value?>" frameborder="0" allowfullscreen></iframe>
			<?endforeach?>
		</div>
	<?endif;?>
</div><br>
    <div class="description">

        <?$APPLICATION->IncludeComponent(
            "custom:comments.prototype",
            "mobile",
            array(
                "ELEMENT_ID" => $arResult["ID"],
                "IBLOCK_ID" => (SITE_ID == "tp") ? "39" : "38",
                "SUCCESS_MESSAGE" => "Сообщение добавлено и ожидает одобрения администратора."
            ),
            false,
            array(
                "HIDE_ICONS" => "Y"
            )
        );?>
    </div>
</div><?if (!Helper::IsRealFilePath(array('/catalog/index.php', '/catalog/detail/index.php'))){?>
			<?} else {?>
				<div style="height:200px" id="form_callback_visible"></div>
				<div class="botton_call" id="form_callback">
					<div>Мы знаем про <b><?= (empty($arResult["NAME_MORF_1"])) ? "швейные машины" : $arResult["NAME_MORF_1"] ?></b><br>абсолютно все!<br>Перезвоним вам через <b>28 секунд</b>, проконсультируем, дадим самую лучшую цену в г. <?=$GLOBALS["GEO_REGION_CITY_NAME"]?>!</div>
					<?$APPLICATION->IncludeComponent(
						"custom:form.prototype",
						"callback-mobile-2",
						array(
							"FORM_ID" => 1,
							"FORM_ACTION" => "FORM_CALLBACK",
							"YANDEX_COUNER" => "ostalis_voprosy_element",
							"SUCCESS_MESSAGE" => "Спасибо, что выбрали нас! Мы перезвоним Вам через 5 минут, или раньше!",
                            "FIELDS" => array(
                                "form_text_1",
                                "form_text_2",
                                "IS_MOBILE",
                            ),
                            "ORDER" => array(
                                'form_text_1' => 'NAME',
                                'form_text_2' => 'PHONE',
                                'IS_MOBILE'   => 'IS_MOBILE',
                            )
						),
						false,
						array(
							"HIDE_ICONS" => "Y",
						)
					);?>
				</div>
			<?}?>

<div id="add_basket" class="fancy_block form">
    <div class="info_block">
        <div class="img">
            <img src="#" alt="#">
        </div>
        <div class="text"></div>
    </div>
    <a href="#close-fancybox" class="button yellow">Продолжить покупки</a>
    <a href="/cart/" class="button red">Оформить заказ</a>
</div>
<?if ($arResult["OFFERS"]):?>
    <div id="popup-offers" class="fancybox_block">
        <div class="gift_blocks">
            <?foreach ($arResult["OFFERS"] as $arOffer):?>
                <div class="gift_block" style="text-align:center;">
                    <div class="img"><img src="<?=$arOffer["RESIZE_PICTURE"]["SRC"]?>" alt="<?=$arOffer["NAME"]?>" /></div>
                    <div class="name" style="font-size:.8em;"><?=$arOffer["PROPERTIES"]["VENDOR_CODE"]["VALUE"]?></div>
                    <div class="price"><b><?=$arOffer["REGION_PRICE"]["DISCOUNT_VALUE"]?> руб.<?if ($arOffer["CATALOG_MEASURE_NAME"] != "шт"):?> / <?=$arOffer["CATALOG_MEASURE_NAME"]?><?endif?></b></div>
                    <div class="buy_button incart_input scale-decrease" data-id="<?=$arOffer["ID"]?>" data-path="<?=$arOffer["ADD_URL"]?>">
                        <span class="acs-bay-btn"><i class="fa fa-shopping-cart"></i> Купить</span>
                    </div>
                </div>
            <?endforeach?>
            <!--div class="footer_button_block">
                <a href="#close-fancybox" class="button">Продолжить покупки</a>
                <a href="/cart/" class="red_button">Оформить заказ</a>
            </div-->
        </div>
    </div>
<?endif?>
<script>$(function(){$("#form_callback_visible").height($("#form_callback").height()+15);$("#form_callback").css("margin-top","-"+($("#form_callback").height()+15)+"px")})</script>

<?$productId = (!empty($arResult["XML_ID"])) ? $arResult["XML_ID"] : $arResult["ID"];
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