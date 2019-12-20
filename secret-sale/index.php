<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Акции от компании ТекстильТорг. Скидки на швейное оборудование");
$APPLICATION->SetPageProperty("keywords", "акции текстильторг, скидки текстильторг, действующие акции, следующие акции, дарим скидки, рассрочка, кредит");
$APPLICATION->SetPageProperty("description", "Акции. На данный момент у нас действуют следующие акции: Дарим 10% Рассрочка 0%. Узнайте подробности у наших менеджеров");
$APPLICATION->SetTitle("");
if(isset($_REQUEST["AJAX_PAGEN"]) && $_REQUEST["AJAX_PAGEN"] == "Y") {
	$APPLICATION->RestartBuffer();
	header('Content-Type: text/html; charset='.LANG_CHARSET);
} else {
	if(defined("IS_MOBILE") || ($_GET["test"] == 1)) {?>
		  <div class="promo" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/images/promo.png');">
			<div class="promo__title"> Секретная
			  <br> Распродажа
			  <br>
			  <span class="promo__title--red">для избранных!</span>
			</div>
		  </div>
			<div class="catalog">
				<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				<?$k = 1;?>
	<?} else {?>
		<p><img alt="" src="<?=SITE_TEMPLATE_PATH?>/images/banner.png" style="max-width:100%; margin: 0 auto;"></p>
	<?
	}
}
				$arElements = array();
				$arFilters = array();
				global $arFilters;
				$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");
				$arFilter = Array("IBLOCK_ID"=>44, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
				if(isset($_REQUEST["AJAX_PAGEN"]) && $_REQUEST["AJAX_PAGEN"] == "Y")
					$arFilter["ID"] = $_REQUEST["AJAX_CATALOG_ID"];
				$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);
				while($ob = $res->GetNextElement()):
					$arFields = $ob->GetFields();
					$arProps = $ob->GetProperties();
					$arElements[$arFields["ID"]] = array("NAME" => $arFields["NAME"], "PRODUCTS" => $arProps["PRODUCTS"]["VALUE"]);
					$arrFilterCatalog = array("ID" => $arProps["PRODUCTS"]["VALUE"]);
					
					if(defined("IS_MOBILE") || ($_GET["test"] == 1) && (!isset($_REQUEST["AJAX_PAGEN"]) && $_REQUEST["AJAX_PAGEN"] != "Y")) {?>	
						<div class="panel">
							<div class="catalog__title" role="tab" id="heading<?=$arFields["ID"]?>">
							  <a role="button" class="catalog__link" data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$arFields["ID"]?>" aria-expanded="true" aria-controls="collapse<?=$arFields["ID"]?>">
								<span class="catalog__name"><?=$arFields["NAME"]?></span>
								<div class="catalog__bage">
								  <div class="catalog__guarantee"> Гарантия </div>
								  <span class="catalog__years">5 лет</span>
								</div>
							  </a>
							</div>
							<div id="collapse<?=$arFields["ID"]?>" class="catalog__content collapse in<?//=$k ? " in" : ""?>" role="tabpanel" aria-labelledby="heading<?=$arFields["ID"]?>" data-catalog-id="<?=$arFields["ID"]?>">
					<?}	elseif(!isset($_REQUEST["AJAX_PAGEN"]) && $_REQUEST["AJAX_PAGEN"] != "Y") {?>
						<div class="catalog__content" data-catalog-id="<?=$arFields["ID"]?>">
							<h2 style="text-align:center;margin:10px 0 20px 0;font-size:24px;background-color:rgb(245, 235, 39);color:rgb(34, 34, 34);padding:7px 0;font-weight:bold;line-height:1.5;"><?=$arFields["NAME"]?><img alt="" src="<?=SITE_TEMPLATE_PATH?>/images/garant.png" style="vertical-align:middle;padding-left:20px;"></h2>
					<?}				
									$APPLICATION->IncludeComponent(
										"bitrix:catalog.section",
										"secret-sale",
										Array(
												"ACTION_VARIABLE" => "action",
												"ADD_PICT_PROP" => "-",
												"ADD_PROPERTIES_TO_BASKET" => "Y",
												"ADD_SECTIONS_CHAIN" => "Y",
												"ADD_TO_BASKET_ACTION" => "ADD",
												"AJAX_MODE" => "N",
												"AJAX_OPTION_ADDITIONAL" => "",
												"AJAX_OPTION_HISTORY" => "N",
												"AJAX_OPTION_JUMP" => "N",
												"AJAX_OPTION_STYLE" => "Y",
												"BACKGROUND_IMAGE" => "-",
												"BASKET_URL" => "/cart/",
												"BROWSER_TITLE" => "-",
												"CACHE_FILTER" => "Y",
												"CACHE_GROUPS" => "Y",
												"CACHE_TIME" => "36000000",
												"CACHE_TYPE" => "A",
												"CITY_SHOPS" => "Москва,Санкт-Петербург,Нижний Новгород,Ростов-на-Дону,Екатеринбург,Новосибирск,Казань",
												"COMPONENT_TEMPLATE" => "catalog",
												"CONVERT_CURRENCY" => "N",
												"DETAIL_URL" => "",
												"DISABLE_INIT_JS_IN_COMPONENT" => "N",
												"DISPLAY_BOTTOM_PAGER" => "Y",
												"DISPLAY_TOP_PAGER" => "N",
												"ELEMENT_SORT_FIELD" => "sort",
												"ELEMENT_SORT_FIELD2" => "asc",
												"ELEMENT_SORT_ORDER" => "id",
												"ELEMENT_SORT_ORDER2" => "desc",
												"FILE_404" => "",
												"FILTER_NAME" => "arrFilterCatalog",
												"GEO_REGION_CITY_NAME" => $GLOBALS["GEO_REGION_CITY_NAME"],
												"HIDE_FILTER" => "N",
												"HIDE_NOT_AVAILABLE" => "N",
												"IBLOCK_ID" => "8",
												"IBLOCK_TYPE" => "catalog",
												"INCLUDE_SUBSECTIONS" => "A",
												"LABEL_PROP" => "-",
												"LINE_ELEMENT_COUNT" => "3",
												"MESSAGE_404" => "",
												"MESSAGE_NOT_FOUND" => "Товаров не найдено, попробуйте изменить параметры фильтра.",
												"MESS_BTN_ADD_TO_BASKET" => "В корзину",
												"MESS_BTN_BUY" => "Купить",
												"MESS_BTN_DETAIL" => "Подробнее",
												"MESS_BTN_SUBSCRIBE" => "Подписаться",
												"MESS_NOT_AVAILABLE" => "Нет в наличии",
												"META_DESCRIPTION" => "-",
												"META_KEYWORDS" => "-",
												"OFFERS_CART_PROPERTIES" => array(0=>"VENDOR_CODE",),
												"OFFERS_FIELD_CODE" => array(0=>"",1=>"",),
												"OFFERS_LIMIT" => "5",
												"OFFERS_PROPERTY_CODE" => array(0=>"VENDOR_CODE",1=>"",),
												"OFFERS_SORT_FIELD" => "sort",
												"OFFERS_SORT_FIELD2" => "id",
												"OFFERS_SORT_ORDER" => "asc",
												"OFFERS_SORT_ORDER2" => "desc",
												"PAGER_BASE_LINK_ENABLE" => "N",
												"PAGER_DESC_NUMBERING" => "N",
												"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
												"PAGER_SHOW_ALL" => "N",
												"PAGER_SHOW_ALWAYS" => "N",
												"PAGER_TEMPLATE" => ((defined("IS_MOBILE") || ($_GET["test"] == 1)) ? "modern_ajax-2" : "modern_ajax_secret_2"),
												"PAGER_TITLE" => "Товары",
												"PAGE_ELEMENT_COUNT" => ((defined("IS_MOBILE") || ($_GET["test"] == 1)) ? "6" : "3"),
												"PARENT_ITEM" => $arFields,
												"PARTIAL_PRODUCT_PROPERTIES" => "Y",
												"PRICE_CODE" => $GLOBALS["CITY_PRICE_CODE"],
												"PRICE_VAT_INCLUDE" => "Y",
												"PRODUCT_ID_VARIABLE" => "id",
												"PRODUCT_PROPERTIES" => array(),
												"PRODUCT_PROPS_VARIABLE" => "prop",
												"PRODUCT_QUANTITY_VARIABLE" => "",
												"PRODUCT_SUBSCRIPTION" => "N",
												"PROPERTY_CODE" => array(0=>"PHOTOS",1=>"",2=>"",),
												"REGION_PRICE_CODE_DEFAULT" => "Москва",
												"SECTION_CODE" => "",
												"SECTION_ID" => 0,
												"SECTION_ID_VARIABLE" => "SECTION_ID",
												"SECTION_URL" => "",
												"SECTION_USER_FIELDS" => array(0=>"UF_ACTION",1=>"UF_LINK",2=>"UF_H1_TITLE",3=>"UF_ACTION_MORE",4=>"",),
												"SEF_MODE" => "N",
												"SET_BROWSER_TITLE" => "N",
												"SET_LAST_MODIFIED" => "N",
												"SET_META_DESCRIPTION" => "N",
												"SET_META_KEYWORDS" => "Y",
												"SET_STATUS_404" => "Y",
												"SET_TITLE" => "Y",
												"SHOW_404" => "Y",
												"SHOW_ALL_WO_SECTION" => "Y",
												"SHOW_CLOSE_POPUP" => "N",
												"SHOW_DISCOUNT_PERCENT" => "N",
												"SHOW_OLD_PRICE" => "N",
												"SHOW_PRICE_COUNT" => "1",
												"TEMPLATE_THEME" => "blue",
												"USE_MAIN_ELEMENT_SECTION" => "N",
												"USE_PRICE_COUNT" => "N",
												"USE_PRODUCT_QUANTITY" => "N"
										)
									);
					if(defined("IS_MOBILE") || ($_GET["test"] == 1) && (!isset($_REQUEST["AJAX_PAGEN"]) && $_REQUEST["AJAX_PAGEN"] != "Y")) {?>
							</div>
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
							<script>
								$(function(){
									$(".form_callback_visible").height($(".form_callback").height()+15);
									$(".form_callback").css("margin-top","-"+($(".form_callback").height()+15)+"px");
								})
							</script>
						</div>
					<?} elseif(!isset($_REQUEST["AJAX_PAGEN"]) && $_REQUEST["AJAX_PAGEN"] != "Y") {?>
						</div>
					<?}
					$k = 0;
				endwhile;
				?>
<?if(isset($_REQUEST["AJAX_PAGEN"]) && $_REQUEST["AJAX_PAGEN"] == "Y") {
	die();
} else{?>				
	<?if(defined("IS_MOBILE") || ($_GET["test"] == 1)) {?>
				</div>
			</div>
		<script src="/js/bootstrap.js"></script>
		<script src="/js/owl.carousel.js"></script>
		<script src="/js/app.js"></script>
	<?}?>
<?}?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>