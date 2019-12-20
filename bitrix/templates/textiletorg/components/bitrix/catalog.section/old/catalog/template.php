<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
if (isset($_REQUEST["AJAX_PAGEN"]) && $_REQUEST["AJAX_PAGEN"] == "Y")
{
    $APPLICATION->RestartBuffer();
    header('Content-Type: text/html; charset='.LANG_CHARSET);
}
?>
<?if ($arResult["ITEMS"]):?>
    <div class="shop_items" id="catalog-items">
        <?if ($arParams["HIDE_FILTER"] != "Y"):?>
            <div class="filtre_block">
                <a href="#catalog-filter" class="button yellow" id="open-filter">Поиск по характеристикам</a>
            </div>
        <?endif?>
        <?foreach ($arResult["ITEMS"] as $arItem):?>
            <?
			$gdeslonCodes .= $arItem['XML_ID'] .':'. intval($arItem['CATALOG_PRICE_1']) . ',';
            $this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => "Будет удалена вся информация, связанная с этой записью. Продолжить?"));
            ?>
            <div class="shop_item" id="<?=$this->GetEditAreaId($arItem["ID"])?>">
                <div class="title_block">
                    <h2><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h2>
  
                </div>
                <div class="article_readmore" style="    display: flex;
    justify-content: space-between;
    align-items: center; margin-top:10px;margin-bottom:10px;position:relative;">
                    <?if ($arItem["PROPERTIES"]["VENDOR_CODE"]["VALUE"]):?>
                        <div class="article">Артикул <b><?=$arItem["PROPERTIES"]["VENDOR_CODE"]["VALUE"]?></b></div>
                    <?endif?>
					<?if ($arItem["PROPERTIES"]["D3_GALLERY"]["VALUE"]):?>
						<a onclick="window.open('<?=$arItem["PROPERTIES"]["D3_GALLERY"]["VALUE"]?>', '', 'width=1200, height=850');" href="#" class="grad360"></a>
					<?endif?>
					<? if (!empty($arItem["ACTIONS"])): ?>
						<div class="akciya">Акция</div>
						<div class="actions-block">
                            <div class="header">Участвует в акции</div>
                            <? foreach ($arItem["ACTIONS"] as $arAction): ?>
                                <div><a href="<?=$arAction["URL"]?>">«<?=$arAction["NAME"]?>»</a></div>
                            <? endforeach; ?>
                        </div>
					<?endif?>
                </div>

				<div style="display:flex;justify-content:space-between;align-items:center">
					<?if ($arItem["RESIZE_PICTURE"]["SRC"]):?>
							<div class="img_block">
									<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="title_img_block"><img src="<?=$arItem["RESIZE_PICTURE"]["SRC"]?>" class="title_img"></a>
							</div>
					<?endif?>
					<div style="min-width:155px;position:relative;right:-3px">
						<div class="price_block">
							<?if ($arItem["REGION_PRICE"]["DISCOUNT_VALUE"]):?>
								<strong class="black-strong">Цена:</strong> <span class="sum list-price"><?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?></span> <span class="text-red">руб.</span>
							<?else:?>
								<strong class="black-strong">Цена:</strong> <span class="sum">На заказ</span>
							<?endif?>
						</div>
                        <?if ($arItem["REGION_PRICE"]["VALUE_NOVAT"] > $arItem["REGION_PRICE"]["DISCOUNT_VALUE_NOVAT"]):?>
                            <div class="old-price">
                                Старая цена: <big><?=$arItem["REGION_PRICE"]["VALUE"]?></big> <small>руб.</small>
                            </div>
                        <?elseif (!empty($arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]) && SITE_ID == "s1"):?>
                            <div class="old-price">
                                Старая цена: <big><?=$arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]?></big> <small>руб.</small>
                            </div>
                        <?endif?>
						<?/*if ($arItem["RATING"] && $arItem["COMMENTS_COUNT"] > 0) {?>
							<div class="info_block">
								<div class="items_2">
									<div class="item">
										<div class="rating">
											<strong class="black-strong">Рейтинг:</strong> <span class="<?=$arItem["RATING"]["CLASS"]?>"></span>
										</div>
									</div>
								</div>
							</div>
						<? } */?><?
$yaCounter = "1021532";
if($_SERVER["SERVER_NAME"] == "spb.textiletorg.ru") {
	$yaCounter = "48343148";
}
?>
						<div class="item">
							<?if ($arItem["COMMENTS_COUNT"] && false):?>
								<div class="rew_block">
									<strong>Отзывы:</strong> <span class="text-red"><?=$arItem["COMMENTS_COUNT"]?> шт.</span>
								</div>
							<?endif?>
							<div class="bay_block">
                                <?if ($arItem["OFFERS"]):?>
                                    <a href="#popup-offers-<?=$arItem["ID"]?>" class="button buy red inyourcart scale-decrease fancybox" data-id="<?=$arItem["ID"]?>" data-path="<?=$arItem["ADD_URL"]?>" onclick="yaCounter<?=$yaCounter?>.reachGoal('Open_Cart')" style="
                                        padding: 2px 30px!important;
                                        background: #ff0000;
                                        font-size: 16px;
                                        box-sizing: border-box;">
                                        <i class="fa fa-shopping-cart"></i> Купить
                                    </a>
                                <?else:?>
                                    <a href="<?=$arItem["ADD_URL"]?>" class="button buy red add-cart" data-id="<?=$arItem["ID"]?>" data-path="<?=$arItem["ADD_URL"]?>" onclick="yaCounter<?=$yaCounter?>.reachGoal('Open_Cart')" style="
                                        padding: 2px 30px!important;
                                        background: #ff0000;
                                        font-size: 16px;
                                        box-sizing: border-box;">
                                        <i class="fa fa-shopping-cart"></i> Купить
                                    </a>
                                <?endif?>
								<br>
								<a href="#buy_one_click" class="button yellow buy-one-click" title="Оформление заказа в 1 клик" data-good-id="<?=$arItem["ID"]?>" data-price="<?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE_NOVAT"]?>" data-currency="<?=$arItem["REGION_PRICE"]["CURRENCY"]?>" data-good-name="<?=$arItem["NAME"]?>" data-good-url="<?=$arItem["DETAIL_PAGE_URL"]?>" onclick="yaCounter<?=$yaCounter?>.reachGoal('zakaz_1_click')" style="font-size: 13px;
                                    width: 88%;
                                    box-sizing: border-box;
                                    font-family: 'AvenirNextCyrBold', Verdana, Tahoma, Arial;
                                    margin-top: 5px;
                                    line-height: 1;
                                    padding: 8px 8px!important;
                                    border: 1px solid red !important;
                                    color: #000;">Купить в 1 клик</a>
							</div>
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
                                </div>
                            </div>
                        <?endif;?>

					</div>
				</div>
                <div class="clear"></div>
	<?if ($arItem["PROPERTIES"]["PHOTOS"]["VALUE"]):?>
				<br><br>
<div class="img_block">
		<ul class="img_list_block" style="width:400px;margin:0 auto">
		<?$active = "active";?>
			<?foreach ($arItem["PROPERTIES"]["PHOTOS"]["VALUE"] as $nPhoto => $arPhoto):?>
				<?$file = CFile::GetPath($arPhoto)?>
				<li><a href="<?=$file?>" target="_blank" class="<?=$active;?>"><img src="<?=$file?>" alt="<?=$arItem["NAME"]?>"></a></li>
				<?$active = "";?>
			<?endforeach?>
		</ul>
</div>
	<?endif?>
<div class="article_readmore" style="margin-top:15px">				
	<?if ($arItem["PROPERTIES"]["PRODUCT_CERTIFIED"]["VALUE_XML_ID"] == "Y"):?>
		<div class="icon-message">
			<img src="<?=SITE_TEMPLATE_PATH?>/img/rst.png" />
			<div class="icon-message-detail">Товар прошел сертификацию</div>
		</div>
	<?endif?>
	<? if ($arItem["MESSAGE_MASTERCLASS_SHOW"]): ?>
		<div class="icon-message">
			<img src="<?=SITE_TEMPLATE_PATH?>/mod_files/css/images/gift.png">
			<div class="icon-message-detail">Демонстрация функционала моделей производится в любом из наших магазинов абсолютно бесплатно</div>
		</div>
	<?endif?>
	<?if (!empty($arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
		<?if (in_array(1083, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
			<div class="icon-message">
				<img src="<?=SITE_TEMPLATE_PATH?>/img/cloth1.png">
				<div class="icon-message-detail">для всех типов тканей</div>
			</div>
		<?else:?>
			<?if (in_array(1084, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
				<div class="icon-message">
					<img src="<?=SITE_TEMPLATE_PATH?>/img/cloth2.png">
					<div class="icon-message-detail">для легких и сверхлегких тканей/div>
				</div>
			<?endif?>
			<?if (in_array(1085, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
				<div class="icon-message">
					<img src="<?=SITE_TEMPLATE_PATH?>/img/cloth3.png">
					<div class="icon-message-detail">для легких и средних тканей</div>
				</div>
			<?endif?>
			<?if (in_array(1086, $arItem["PROPERTIES"]["CHAR_PROSHIVAEMYE_MATERIALY"]["VALUE_ENUM_ID"])):?>
				<div class="icon-message">
					<img src="<?=SITE_TEMPLATE_PATH?>/img/cloth4.png">
					<div class="icon-message-detail">для тяжелых и сверхтяжелых тканей</div>
				</div>
			<?endif?>
		<?endif?>
	<?endif?>					
	<?if ($arItem["PROPERTIES"]["GIFT_COUPON_25"]["VALUE_XML_ID"] == "Y"):?>
		<div class="icon-message">
			<img src="<?=SITE_TEMPLATE_PATH?>/img/25.png">
			<div class="icon-message-detail">При покупке текущего товара в подарок купон со скидкой 25%</div>
		</div>
	<?endif?>
	<?if ($arItem["PROPERTIES"]["EXPERT_ADVICE"]["VALUE_XML_ID"] == "Y"):?>
		<div class="icon-message">
			<img src="<?=SITE_TEMPLATE_PATH?>/img/recom.png">
			<div class="icon-message-detail">Товар рекомендован экспертом</div>
		</div>
	<?endif?>
	<div class="link-detail"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>">Подробнее..</a></div>
	<div class="clear"></div>
	<style>.icon-message{display:inline-block}.icon-message img{max-width:none}</style>
</div>

				
            </div>
        <?endforeach?>
    </div>
    <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
        <?=$arResult["NAV_STRING"]?>
    <?endif;?>

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
<?else:?>
    Товаров не найдено, попробуйте изменить параметры фильтра.
<?endif?>
<?
if (isset($_REQUEST["AJAX_PAGEN"]) && $_REQUEST["AJAX_PAGEN"] == "Y")
{
    die();
}?> 
<?if (!Helper::IsRealFilePath(array('/catalog/index.php', '/catalog/detail/index.php')) && $APPLICATION->GetCurPage(false) != "/search/"){?>
			<?} else {?>			
				<div style="height:200px" id="form_callback_visible"></div>
				<div class="botton_call" id="form_callback">
					<div>Мы знаем про <?= (empty($arResult["UF_TEXT_FOR_FEEDBACK"])) ? "швейные машины" : $arResult["UF_TEXT_FOR_FEEDBACK"] ?><br>абсолютно все!<br>Перезвоним вам через <b>28 секунд</b>, проконсультируем и ответим на все интересующие вас вопросы.</div>		
					<?$APPLICATION->IncludeComponent(
						"custom:form.prototype",
						"callback-mobile-2",
						array(
							"FORM_ID" => 1,
							"FORM_ACTION" => "FORM_CALLBACK",
							"YANDEX_COUNER" => "ostalis_voprosy_catalog",
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
<script>$(function(){$("#form_callback_visible").height($("#form_callback").height()+15);$("#form_callback").css("margin-top","-"+($("#form_callback").height()+15)+"px")})</script>

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