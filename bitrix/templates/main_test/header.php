<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "//www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html class="<?if (IS_HOME):?>home<?endif?><?if(SITE_ID != 's1'){?> domen_by<?}?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?$APPLICATION->ShowTitle()?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="/favicon6.ico" rel="shortcut icon"   style="border-radius: 100px;"      type="image/x-icon" />
        <?
        $APPLICATION->ShowHead(true);

        if (!$USER->IsAuthorized())
            CJSCore::Init(array('ajax', 'json', 'ls', 'session', 'jquery', 'popup', 'pull'));

        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/mod_files/css/sys_css.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/mod_files/css/plugins.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/mod_files/css/common2.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/mod_files/css/content.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/mod_files/css/menu_style.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/mod_files/css/new_style.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/flexslider/flexslider.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/ctrlenter/mistakes.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/lightbox/css/lightbox.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/mod_files/css/jquery.formstyler.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/mod_files/css/font/fonts.css");

        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/mod_files/css/jquery-ui-1.8.21.custom.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/mod_files/css/custom_style.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/mod_files/css/my.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/mod_files/css/confirm_order.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/slick-1.6.0/slick/slick.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/slick-1.6.0/slick/slick-theme.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox.css");

        $APPLICATION->SetAdditionalCSS("//dadata.ru/static/css/lib/suggestions-4.8.css");

        $APPLICATION->AddHeadScript("//yandex.st/jquery/1.7.2/jquery.min.js");
        $APPLICATION->AddHeadScript("//www.gstatic.com/swiffy/v7.4/runtime.js");
        $APPLICATION->AddHeadScript("//www.kupivkredit.ru/widget/vkredit.js");

        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/slick-1.6.0/slick/slick.min.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/vendor/kladr/jquery.kladr.min.js");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/vendor/kladr/jquery.kladr.min.css");

        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/ctrlenter/mistakes.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/preload.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/vverh.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/search.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.cookie.min.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox.pack.js");

        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/mod_files/js/uz_es_filter.js");

        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/flexslider/slider/js/modernizr.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/flexslider/jquery.flexslider.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/flexslider/slider/js/shCore.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/flexslider/slider/js/shBrushXml.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/flexslider/slider/js/shBrushJScript.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/flexslider/slider/js/jquery.easing.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/flexslider/slider/js/jquery.mousewheel.js");

        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/js_flash_baner.js");
        //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/swiffyobject.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/common2.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/main_center_menu.js");

        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.form.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/aj_cart_data.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.qtip-1.0.0-rc3.min.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/lightbox/js/lightbox.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/mod_files/js/jquery-ui-1.8.21.custom.min.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/mod_files/js/jquery.formstyler.min.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/mod_files/js/typeahead.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/mod_files/js/handlebars_v2.js");
        //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/mod_files/js/uz_scripts.js");

        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/vendor/sumogallery/sumogallery.css");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/vendor/sumogallery/jquery.sumogallery.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/vendor/elevatezoom/jquery.elevateZoom-3.0.8.min.js");



        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/add-to-cart.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/script.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/mod_files/js/custom.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/vendor/jquery.validate.min.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/vendor/jquery.formatter.min.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/vendor/jquery.form.min.js");
		
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.matchHeight.js");
        ?>

        <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/media.css?v=1.2">

						
		<?
			$curPage = $APPLICATION->GetCurPage(true);
			if (preg_match('/filter\/clear/', $curPage)) {			
				$haystack = $curPage;
				$needle   = 'filter/clear/';
				$pos      = strripos($haystack, $needle);				
				$cut=$pos;	
				$path = substr($haystack, 0 , $cut);    						
				?><link rel="canonical" href="<?=$path?>"/><?
			}
		?>
				
        <!--[if lt IE 9]>
            <link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/mod_files/css/ie.css" />
        <![endif]-->
        <script type="text/javascript">(window.Image ? (new Image()) : document.createElement('img')).src = 'https://vk.com/rtrg?p=VK-RTRG-132960-aBYmt';</script>
        <!-- Facebook Pixel Code -->
        <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
                n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
                document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '1970980093135896'); // Insert your pixel ID here.
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                       src="https://www.facebook.com/tr?id=1970980093135896&ev=PageView&noscript=1"
            /></noscript>
        <!-- DO NOT MODIFY -->
        <!-- End Facebook Pixel Code -->
    </head>
    <body <?if (SITE_ID == "by"):?>class="rb-style"<?endif?><?if (SITE_ID == "tp"):?>class="tp-style"<?endif?>>
        <?if ($USER->IsAdmin()):?>
            <?$APPLICATION->ShowPanel();?>
        <?endif?>
        <?
		$APPLICATION->IncludeComponent(
            "custom:region.prototype",
            "",
            array(
                "HOUSE_REGIONS" => $GLOBALS["REGION_HOUSE_REGIONS"],
                "DEFAULT_REGION" => $GLOBALS["REGION_DEFAULT_REGION"],
				"COUNTRY_NAME_ORIG"=> $GLOBALS["REGION_COUNTRY_NAME_ORIG"],
                "CTX_PARAM" => array(
                    "MSK" => "Москва",
                    "SPB" => "Санкт-Петербург",
                    "SMR" => "Самара",
                    "EKB" => "Екатеринбург",
                    "N_NOV" => "Нижний Новгород",
                    "RND" => "Ростов-на-Дону",
                    "BGD" => "Белгород",
                    "BNK" => "Брянск",
                    "VMR" => "Владимир",
                    "VRN" => "Воронеж",
                    "INV" => "Иваново",
                    "KLG" => "Калуга",
                    "KSM" => "Кострома",
                    "KRK" => "Курск",
                    "LPK" => "Липецк",
                    "ORL" => "Орёл",
                    "RZN" => "Рязань",
                    "SMK" => "Смоленск",
                    "TBK" => "Тамбов",
                    "TVR" => "Тверь",
                    "TUL" => "Тула",
                    "YAR" => "Ярославль"
                ),
                "CITIES_PRICE" => array(
                    "Москва" => 1,
                    "Санкт-Петербург" => 2,
                    "Екатеринбург" => 4,
                    "Нижний Новгород" => 5,
                    "Ростов-на-Дону" => 6,
                    "Минск" => 11
                ),
                "CACHE_TIME" => 0
            ),
            false,
            array(
                "HIDE_ICONS" => "Y"
            )
        );
		?>

        <?$APPLICATION->IncludeComponent(
            "ayers:delivery.info",
            "",
            array(
                "CITY" => $GLOBALS["GEO_REGION_CITY_NAME"],
                "CACHE_TIME" => 36000
            ),
            false,
            array(
                "HIDE_ICONS" => "Y"
            )
        );?>

        <?$APPLICATION->IncludeComponent(
            "custom:form.prototype",
            "callback",
            array(
                "FORM_ID" => 1,
                "FORM_ACTION" => "FORM_CALLBACK",
                "SUCCESS_MESSAGE" => "Спасибо, что выбрали нас! Мы перезвоним Вам в ближайшее время!",
                "FIELDS" => array(
                    "form_text_1",
                    "form_text_2"
                ),
                "ORDER" => array(
                    "form_text_1" => "NAME",
                    "form_text_2" => "PHONE"
                )
            ),
            false,
            array(
                "HIDE_ICONS" => "Y",
            )
        );?>

        <div class="ay-main-container">
            <div id="lay_f6">
                <div class="h_top-block clearfix">
                    <div class="wrap-block">
                        <?$APPLICATION->IncludeComponent(
                            "custom:musiconoff",
                            "",
                            array()
                        )?>
                        <?if(!IS_HOME):?>
                            <?if (PAGE_FOLDER == '/cart/'):?>
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:menu",
                                    "full",
                                    array(
                                        "ROOT_MENU_TYPE" => "catalog_top",
                                        "MENU_CACHE_TYPE" => "A",
                                        "MENU_CACHE_TIME" => "36000000",
                                        "MENU_CACHE_USE_GROUPS" => "Y",
                                        "MENU_CACHE_GET_VARS" => array(
                                            "ID"
                                        ),
                                        "MAX_LEVEL" => "2",
                                        "CHILD_MENU_TYPE" => "",
                                        "USE_EXT" => "Y",
                                        "DELAY" => "N",
                                        "ALLOW_MULTI_SELECT" => "N",
                                        "COMPONENT_TEMPLATE" => "top",
                                        "IGNORE_SECTIONS" => array("Прочее", "Подарочные карты"),
                                        "BIG_SECTIONS" => array("Все для глажения", "Оборудование специального назначения")
                                    ),
                                    false,
                                    array(
                                        "HIDE_ICONS" => "Y"
                                    )
                                );?>
                            <?else:?>
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:menu",
                                    "catalog-header",
                                    array(
                                        "ROOT_MENU_TYPE" => "catalog_header",
                                        "MENU_CACHE_TYPE" => "A",
                                        "MENU_CACHE_TIME" => "36000000",
                                        "MENU_CACHE_USE_GROUPS" => "Y",
                                        "MENU_CACHE_GET_VARS" => array(
                                            "ID"
                                        ),
                                        "MAX_LEVEL" => "5",
                                        "CHILD_MENU_TYPE" => "",
                                        "USE_EXT" => "Y",
                                        "DELAY" => "N",
                                        "ALLOW_MULTI_SELECT" => "N",
                                        "COMPONENT_TEMPLATE" => "top",
                                    ),
                                    false,
                                    array(
                                        "HIDE_ICONS" => "Y"
                                    )
                                );?>
                            <?endif?>
                        <?else:?>
                            <div class="htb_soc">
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    "",
                                    array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH" => "/include/header-social.php",
                                        "EDIT_TEMPLATE" => "text.php"
                                    )
                                );?>
                            </div>
                        <?endif?>

                        <div class="pln-logo">
                            <a href="/"><img src="/bitrix/templates/textiletorg/mod_files/ce_images/flogo.png" alt="Текстильторг"/></a>
                        </div>
                        <div class="htb_callme">
                            <a href="#form_callback" class="callme fancybox" title="Перезвоним Вам через 28 секунд!" <?=Helper::GetYandexCounter("callMe")?>>Перезвоним Вам через 28 секунд!</a>
                        </div>

                        <?$APPLICATION->IncludeComponent(
                            "bitrix:sale.basket.basket.line",
                            "cart",
                            array(
                                "HIDE_ON_BASKET_PAGES" => "Y",
                                "PATH_TO_BASKET" => SITE_DIR."cart/",
                                "PATH_TO_ORDER" => SITE_DIR."order/",
                                "PATH_TO_PERSONAL" => SITE_DIR,
                                "PATH_TO_PROFILE" => SITE_DIR,
                                "PATH_TO_REGISTER" => SITE_DIR."login/",
                                "POSITION_FIXED" => "N",
                                "SHOW_AUTHOR" => "N",
                                "SHOW_EMPTY_VALUES" => "Y",
                                "SHOW_NUM_PRODUCTS" => "Y",
                                "SHOW_PERSONAL_LINK" => "N",
                                "SHOW_PRODUCTS" => "N",
                                "SHOW_TOTAL_PRICE" => "Y",
                                "COMPONENT_TEMPLATE" => "cart"
                            ),
                            false,
                            array(
                                "HIDE_ICONS" => "Y"
                            )
                        );?>

                        <div class="htb_tel">
                            <?$APPLICATION->IncludeComponent(
                                "bitrix:main.include",
                                "",
                                array(
                                    "AREA_FILE_SHOW" => "file",
                                    "PATH" => "/include/header-phone.php",
                                    "EDIT_TEMPLATE" => "text.php"
                                )
                            );?>
                        </div>
                    </div>
                </div>
            </div>

            <div id="cont" >
                <div id="text">
                    <div id="header">
                        <div id="lay_f1">
                            <div id="incart_shw" style="display: none; background-color: black; filter:progid:DXImageTransform.Microsoft.Alpha(opacity=50);-moz-opacity: 0.5; -khtml-opacity: 0.5; opacity: 0.5; z-index: 1000; position: fixed; width: 100%; height: 20000; top: -20px; left: 0px; right: 0px;" onclick="Hide('shw');"></div>
                            <div class="logo">
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    "",
                                    array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH" => "/include/header-logo.php",
                                        "EDIT_TEMPLATE" => "text.php"
                                    )
                                );?>
                            </div>
                            <div class="center">
                                <!--Slider-->
                                <?$APPLICATION->IncludeComponent(
                                    "custom:slider.propfilter.prototype",
                                    "",
                                    array(
                                        "SECTION_ID" => $_REQUEST["FILTER_SECTION_ID"] ?:$_REQUEST["SECTION_ID"],
                                        "PROPERTY" => "SECTION",
                                        "FILTER_NAME" => "arrFilterHeaderSlider"
                                    ),
                                    false,
                                    array(
                                        "HIDE_ICONS" => "Y"
                                    )
                                );?>

								<?global $SLIDER_IBLOCK_ID;?>
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:news.list",
                                    "slider",
                                    array(
                                        "ACTIVE_DATE_FORMAT" => "d.m.Y",
                                        "ADD_SECTIONS_CHAIN" => "N",
                                        "AJAX_MODE" => "N",
                                        "AJAX_OPTION_ADDITIONAL" => "",
                                        "AJAX_OPTION_HISTORY" => "N",
                                        "AJAX_OPTION_JUMP" => "N",
                                        "AJAX_OPTION_STYLE" => "Y",
                                        "CACHE_FILTER" => "Y",
                                        "CACHE_GROUPS" => "Y",
                                        "CACHE_TIME" => "36000000",
                                        "CACHE_TYPE" => "A",
                                        "CHECK_DATES" => "Y",
                                        "DETAIL_URL" => "",
                                        "DISPLAY_BOTTOM_PAGER" => "N",
                                        "DISPLAY_TOP_PAGER" => "N",
                                        "FIELD_CODE" => array("PREVIEW_PICTURE",""),
                                        "FILTER_NAME" => "arrFilterHeaderSlider",
                                        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                                        "IBLOCK_ID" => $SLIDER_IBLOCK_ID,
                                        "IBLOCK_TYPE" => "slider",
                                        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                                        "INCLUDE_SUBSECTIONS" => "Y",
                                        "MESSAGE_404" => "",
                                        "NEWS_COUNT" => "20",
                                        "PAGER_BASE_LINK_ENABLE" => "N",
                                        "PAGER_DESC_NUMBERING" => "N",
                                        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                                        "PAGER_SHOW_ALL" => "N",
                                        "PAGER_SHOW_ALWAYS" => "N",
                                        "PAGER_TEMPLATE" => ".default",
                                        "PAGER_TITLE" => "Слайдер",
                                        "PARENT_SECTION" => "",
                                        "PARENT_SECTION_CODE" => "",
                                        "PREVIEW_TRUNCATE_LEN" => "",
                                        "PROPERTY_CODE" => array("URL"),
                                        "SET_BROWSER_TITLE" => "N",
                                        "SET_LAST_MODIFIED" => "N",
                                        "SET_META_DESCRIPTION" => "N",
                                        "SET_META_KEYWORDS" => "N",
                                        "SET_STATUS_404" => "N",
                                        "SET_TITLE" => "N",
                                        "SHOW_404" => "N",
                                        "SORT_BY1" => "SORT",
                                        "SORT_BY2" => "NAME",
                                        "SORT_ORDER1" => "ASC",
                                        "SORT_ORDER2" => "ASC"
                                    )
                                );?>
                            </div>

                            <div class="right" id="right_block_banner" paramname="wmode" value="transparent">
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    "",
                                    array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH" => "/include/right-banner.php",
                                        "EDIT_TEMPLATE" => "text.php"
                                    )
                                );?>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>

                    <!--Контент-->
                    <div id="cont">
                        <?if (PAGE_FOLDER != "/cart/"):?>
                            <div id="cont_top">
                                <?if (!IS_HOME):?>
                                    <!--Блок слева вверху-->
                                    <div class="leftblock leftblock_top" id="lay_f3">
                                        <div class="box top akcii">
                                            <div class="box_head">Акции</div>
                                            <?$APPLICATION->IncludeComponent(
                                                "bitrix:main.include",
                                                "",
                                                array(
                                                    "AREA_FILE_SHOW" => "file",
                                                    "PATH" => "/include/left-actions.php",
                                                    "EDIT_TEMPLATE" => "text.php"
                                                )
                                            );?>
                                        </div>
                                    </div>
                                <?endif?>

                                <!--Блок в центре вверху-->
                                <div class="centerblock" id="lay_f2">
                                    <!--Блок поиска-->
                                    <?
                                        if (SITE_ID == "by")
                                        {
                                            $GLOBALS["arrFilterHeaderSearch"] = array(
                                                "CATALOG_CURRENCY_11" => "BYN",
                                                "PROPERTY_VIEW_SITE_RB_VALUE" => "Да"
                                            );
                                        }

                                        if (SITE_ID == "s1")
                                        {
                                            $GLOBALS["arrFilterHeaderSearch"] = array(
                                                "PROPERTY_VIEW_SITE_RU_VALUE" => "Да"
                                            );
                                        }
                                    ?>
                                    <?$APPLICATION->IncludeComponent(
                                        "custom:catalog.search.prototype",
                                        "",
                                        array(
                                            "IBLOCK_ID" => 8,
                                            "ACTION_FORM" => "/search/",
                                            "FILTER_NAME" => "arrFilterHeaderSearch",
                                            "PRICE_ID" => $GLOBALS["PRICE_ID"],
                                            "GEO_REGION_CITY_NAME" => $GLOBALS["GEO_REGION_CITY_NAME"]
                                        ),
                                        false,
                                        array(
                                            "HIDE_ICONS" => "Y"
                                        )
                                    );?>
                                    <!--Меню-->

                                    <?$APPLICATION->IncludeComponent(
                                        "bitrix:menu",
                                        "top",
                                        array(
                                            "ROOT_MENU_TYPE" => "top",
                                            "MENU_CACHE_TYPE" => "A",
                                            "MENU_CACHE_TIME" => "36000000",
                                            "MENU_CACHE_USE_GROUPS" => "Y",
                                            "MENU_CACHE_GET_VARS" => array(
                                                "ID"
                                            ),
                                            "MAX_LEVEL" => "2",
                                            "CHILD_MENU_TYPE" => "top_child",
                                            "USE_EXT" => "Y",
                                            "DELAY" => "N",
                                            "ALLOW_MULTI_SELECT" => "N",
                                            "COMPONENT_TEMPLATE" => "top"
                                        ),
                                        false
                                    );?>
                                </div>

                                <!--Блок справа вверху-->
                                <div class="rightblock rightblock_top" id="lay_f4_top">
                                    <div class="box consult top">
                                        <div class="box_head">Заказ, консультация</div>
                                        <div class="box_block">
                                            <div class="tel tel_bl_u">
                                                <?$APPLICATION->IncludeComponent(
                                                    "custom:region-select.prototype",
                                                    "",
                                                    array(),
                                                    false,
                                                    array(
                                                        "HIDE_ICONS" => "Y"
                                                    )
                                                );?>

                                                <?$APPLICATION->IncludeComponent(
                                                    "custom:region-phone.prototype",
                                                    "",
                                                    array(
                                                        "FILES_PATH" => "/include/region-phone/",
                                                        "DEFAULT_FILE" => "all"
                                                    ),
                                                    false,
                                                    array(
                                                        "HIDE_ICONS" => "Y"
                                                    )
                                                );?>

                                                <?$APPLICATION->IncludeComponent(
                                                    "bitrix:main.include",
                                                    "",
                                                    array(
                                                        "AREA_FILE_SHOW" => "file",
                                                        "PATH" => "/include/sidebar-phones.php",
                                                        "EDIT_TEMPLATE" => "text.php"
                                                    )
                                                );?>

                                                <div style="line-height: 15px; padding-bottom: 2px; padding-top: 3px;">
                                                    <a href="#form_callback" class="callme button fancybox" title="Мы перезвоним Вам через 5 минут или раньше!" <?=Helper::GetYandexCounter("callMe")?>>Заказать звонок</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="clear:both;"></div>
                        <?endif?>

                        <div id="cont_center">
                            <?if (IS_HOME):?>
                                <!--Блок слева в контенте-->
                                <div class="leftblock">
                                    <?$APPLICATION->IncludeComponent(
                                        "bitrix:menu",
                                        "catalog-sidebar",
                                        array(
                                            "ROOT_MENU_TYPE" => "catalog",
                                            "MENU_CACHE_TYPE" => "A",
                                            "MENU_CACHE_TIME" => "36000000",
                                            "MENU_CACHE_USE_GROUPS" => "Y",
                                            "MENU_CACHE_GET_VARS" => array(
                                                "ID"
                                            ),
                                            "MAX_LEVEL" => "5",
                                            "CHILD_MENU_TYPE" => "",
                                            "USE_EXT" => "Y",
                                            "DELAY" => "N",
                                            "ALLOW_MULTI_SELECT" => "N",
                                            "COMPONENT_TEMPLATE" => "catalog-sidebar"
                                        ),
                                        false
                                    );?>

                                    <?$APPLICATION->IncludeComponent(
                                        "bitrix:main.include",
                                        "",
                                        array(
                                            "AREA_FILE_SHOW" => "file",
                                            "PATH" => "/include/left-sidebar-block.php",
                                            "EDIT_TEMPLATE" => "text.php"
                                        ),
                                        false,
                                        array(
                                            "HIDE_ICONS" => "Y"
                                        )
                                    );?>
                                    <div class="box end"></div>
                                </div>
                            <?endif?>

                            <!--Блок справа в контенте-->
                            <div class="rightblock" id="lay_f4">
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    "",
                                    array(
                                        "AREA_FILE_SHOW" => "sect",
                                        "AREA_FILE_SUFFIX" => "right_sidebar",
                                        "COMPONENT_TEMPLATE" => ".default",
                                        "EDIT_TEMPLATE" => ""
                                    ),
                                    false,
                                    array("HIDE_ICONS" => "Y")
                                );?>
                            </div>

                            <!--Центральный блок-->
                            <div class="centerblock <?if (PAGE_FOLDER == "/cart/"):?>border-radius<?endif?>">
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:breadcrumb",
                                    "breadcrumbs",
                                    array(
                                        "START_FROM" => (Helper::IsRealFilePath(array('/catalog/index.php', '/catalog/detail/index.php'))) ? "1" : "0",
                                        "PATH" => "",
                                        "SITE_ID" => "-"
                                    ),
                                false
                                );?>

                                <?
                                $curent_class = '';

                                if (IS_HOME)
                                {
                                    $curent_class = 'title_page';
                                }

                                if ($APPLICATION->GetCurPageParam() == '/cart/')
                                {
                                    $curent_class = 'basket_page';
                                }
                                ?>
                                <div class="inner <?=$curent_class?>" id="lay_body">
                                    <?if (!defined("HIDE_PAGE_TITLE")):?>
                                        <h1><?$APPLICATION->ShowTitle(true)?></h1>
                                    <?endif?>



