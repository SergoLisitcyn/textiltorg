<!DOCTYPE html>
<?
global $SUBDOMAIN;
$SUBDOMAIN["SPB"] = preg_match("/^spb/i", $_SERVER['SERVER_NAME']);
?>
<html class="<? if (IS_HOME): ?>home<? endif ?><? if (SITE_ID != 's1') { ?> domen_by<? } ?>" lang="<?= LANGUAGE_ID; ?>">
<head>
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><? $APPLICATION->ShowTitle() ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="yandex-verification" content="863f36ef31e77943"/>
    <meta name="yandex-verification" content="6c61fd5d52143e29"/>
    <meta name="yandex-verification" content="1283d43f4b9d50d5"/>
    <meta name="yandex-verification" content="ed830fbaed09bc71"/>

    <? if ($_SERVER['SERVER_NAME'] == 'www.textiletorg.ru' || $_SERVER['SERVER_NAME'] == 'dev.textiletorg.ru' || $_SERVER['SERVER_NAME'] == 'textiletorg.ru') : ?>
        <meta name="google-site-verification" content="dbkd-KtNaNsUP6gtaxnbRSOqwZVh4u1FdN-Wb06GUZw"/>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-64860093-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'UA-64860093-1');
        </script>
    <? endif; ?>

    <? if ($_SERVER['SERVER_NAME'] == 'spb.textiletorg.ru'): ?>
        <meta name="google-site-verification" content="dbkd-KtNaNsUP6gtaxnbRSOqwZVh4u1FdN-Wb06GUZw"/>
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-106473040-2"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());

            gtag('config', 'UA-106473040-2');
        </script>
    <? endif; ?>

    <? if (SITE_ID == "s1"): ?>
        <link href="/favicon6.ico" rel="shortcut icon" style="border-radius: 100px;" type="image/x-icon"/>
    <? elseif (SITE_ID == "tp"): ?>
        <link href="/favicon-tp.ico" rel="shortcut icon" style="border-radius: 100px;" type="image/x-icon"/>
    <? endif; ?>
    <? if ($SUBDOMAIN["SPB"]): ?>
        <meta name="yandex-verification" content="7cf72b030ce8eb78"/>
    <? else: ?>
        <meta name="yandex-verification" content="7cf72b030ce8eb78"/>
        <meta name="yandex-verification" content="93aed81ce7b07511"/>
    <? endif; ?>

    <?
    $APPLICATION->ShowHead(true);

    if (!$USER->IsAuthorized()) {
        CJSCore::Init(array('ajax', 'json', 'ls', 'session', 'jquery', 'popup', 'pull'));
    }

    $APPLICATION->AddHeadScript("//yandex.st/jquery/1.7.2/jquery.min.js");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/mod_files/css/font/fonts.css");
    //$APPLICATION->AddHeadScript("//www.gstatic.com/swiffy/v7.4/runtime.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/slick-1.6.0/slick/slick.min.js");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/js/slick-1.6.0/slick/slick.css");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/js/slick-1.6.0/slick/slick-theme.css");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/vendor/kladr/jquery.kladr.min.js");
    //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/ctrlenter/mistakes.js");
    //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/preload.js");
    //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/vverh.js");
    //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/search.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/jquery.cookie.min.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/fancybox/jquery.fancybox.js");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/js/fancybox/jquery.fancybox.css");

    //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/flexslider/slider/js/modernizr.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/flexslider/jquery.flexslider.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/flexslider/slider/js/shCore.js");
    //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/flexslider/slider/js/shBrushXml.js");
    //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/flexslider/slider/js/shBrushJScript.js");
    //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/flexslider/slider/js/jquery.easing.js");
    //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/flexslider/slider/js/jquery.mousewheel.js");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/flexslider/flexslider.css");

//    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/js_flash_baner.js");
//    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/common2.js");
//    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/main_center_menu.js");
//    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/jquery.form.js");
//    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/aj_cart_data.js");
//    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/jquery.qtip-1.0.0-rc3.min.js");
//    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/lightbox/js/lightbox.js");
//    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/mod_files/js/jquery-ui-1.8.21.custom.min.js");
//    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/mod_files/js/jquery.formstyler.min.js");

    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/mod_files/js/typeahead.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/mod_files/js/handlebars_v2.js");
    //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/vendor/sumogallery/jquery.sumogallery.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/vendor/elevatezoom/jquery.elevateZoom-3.0.8.min.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/add-to-cart.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/script.js");
    //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/mod_files/js/custom.js");
    //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/vendor/jquery.validate.min.js");
    //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/vendor/jquery.formatter.min.js");
    //$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/vendor/jquery.form.min.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/jquery.matchHeight.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/vendor/malihu-custom-scrollbar-plugin-3.1.5/jquery.mCustomScrollbar.concat.min.js");










//    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/mod_files/css/sys_css.css");
//    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/mod_files/css/plugins.css");
    //$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/mod_files/css/common2.css");
//    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/mod_files/css/content.css");
//    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/mod_files/css/menu_style.css");
//    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/mod_files/css/new_style.css");
//    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/js/ctrlenter/mistakes.css");
//    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/js/lightbox/css/lightbox.css");
//    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/mod_files/css/jquery.formstyler.css");
//    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/mod_files/css/ref.css");
//    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/mod_files/css/jquery-ui-1.8.21.custom.css");
//    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/mod_files/css/custom_style.css");
//    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/mod_files/css/my.css");
//    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/mod_files/css/confirm_order.css");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/js/vendor/kladr/jquery.kladr.min.css");
    //$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/js/vendor/sumogallery/sumogallery.css");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/js/vendor/malihu-custom-scrollbar-plugin-3.1.5/jquery.mCustomScrollbar.css");

    ?>

    <?
    $currentPage = $APPLICATION->GetCurPage(true);
    if (preg_match('/filter\/clear/', $currentPage)) {
        $path = substr($currentPage, 0, strripos($currentPage, 'filter/clear/'));
        ?>
        <link rel="canonical" href="<?= $path ?>"/>
        <?
    }
    ?>

    <script>(window.Image ? (new Image()) : document.createElement('img')).src = 'https://vk.com/rtrg?p=VK-RTRG-132960-aBYmt';</script>

    <!-- Facebook Pixel Code -->
    <script>
        !function (f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function () {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window,
            document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

        fbq('init', '309309606576872');
        fbq('track', "PageView");
    </script>
    <!-- End Facebook Pixel Code -->

    <!-- Google Tag Manager -->
    <script>
        (function (w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-P37LLBT');
    </script>
    <!-- End Google Tag Manager -->
    <!-- Criteo -->
    <? $curPage = SITE_DIR . $APPLICATION->GetCurPage(true);
    $criteoHomePageTags = array(SITE_DIR . "/index.php",
        SITE_DIR . "/o-nas/index.php",
        SITE_DIR . "/novosti/index.php",
        SITE_DIR . "/akcii/index.php",
        SITE_DIR . "/informacija/index.php",
        SITE_DIR . "/informacija/konkursy/index.php",
        SITE_DIR . "/poleznoe/index.php",
        SITE_DIR . "/poleznoe/chastye-voprosy/index.php",
        SITE_DIR . "/poleznoe/stati/index.php",
        SITE_DIR . "/poleznoe/instrukcii/index.php",
        SITE_DIR . "/poleznoe/obzory/index.php",
        SITE_DIR . "/oplata/sposoby-oplaty/index.php",
        SITE_DIR . "/akcii/rassrochka/index.php",
        SITE_DIR . "/dostavka/index.php",
        SITE_DIR . "/samovyvoz/index.php",
        SITE_DIR . "/poluchenie/nashi-magaziny/index.php",
        SITE_DIR . "/garantiya/garantiya-na-tovar/index.php",
        SITE_DIR . "/informacija/garantii/index.php",
        SITE_DIR . "/kontakty/index.php",
        SITE_DIR . "/informacija/partneram/index.php"
    );
    ?>

    <? if (in_array($curPage, $criteoHomePageTags)): ?>
        <script>
            window.criteo_q = window.criteo_q || [];
            var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /webOS|Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
            window.criteo_q.push(
                {event: "setAccount", account: 38714},
                {event: "setEmail", email: "<?=$USER->GetEmail();?>"},
                {event: "setSiteType", type: deviceType},
                {event: "viewHome"});
        </script>
    <? endif ?>
    <script>
        var siteid = '<?=SITE_ID?>';
        var siteDomen = '<?=$_SERVER["SERVER_NAME"]?>';
        GEO_REGION_CITY_NAME = '<?=$GLOBALS['GEO_REGION_CITY_NAME']?>';
    </script>

    <meta property="og:type" content="website"/>
    <meta property="og:locale" content="ru_RU"/>
    <meta property='og:site_name' content='ТекстильТорг'/>
    <meta property="og:title" content="<?= $APPLICATION->ShowProperty("og:title"); ?>"/>
    <meta property="og:description" content="<?= $APPLICATION->ShowProperty("og:description"); ?>"/>
    <meta property="og:image" content="<?= $APPLICATION->ShowProperty("og:image"); ?>"/>
    <meta property="og:url" content="https://<?= $_SERVER['SERVER_NAME'] . $APPLICATION->GetCurPage(false); ?>"/>

    <link rel="canonical" href="https://<?= $_SERVER['SERVER_NAME'] . $APPLICATION->GetCurPage(false); ?>"/>
</head>
<?
$bodyClass = "";
switch (SITE_ID) {
    case "by":
        $bodyClass = "rb-style";
        break;
    case "tp":
        $bodyClass = "tp-style";
        break;
    default:
        break;
}
?>

<body class="<?= $bodyClass; ?>">
<div class="popup-overlay"></div>
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P37LLBT" height="0" width="0"
            style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<noscript>
    <img alt="" height="1" width="1" style="display:none"
         src="https://www.facebook.com/tr?id=309309606576872&ev=PageView&noscript=1"/>
</noscript>
<? if ($USER->IsAdmin()): ?>
    <? $APPLICATION->ShowPanel(); ?>
<? endif ?>

<? $APPLICATION->IncludeComponent(
    "custom:region.prototype",
    "main",
    array(
        "HOUSE_REGIONS" => $GLOBALS["REGION_HOUSE_REGIONS"],
        "DEFAULT_REGION" => $GLOBALS["REGION_DEFAULT_REGION"],
        "COUNTRY_NAME_ORIG" => $GLOBALS["REGION_COUNTRY_NAME_ORIG"],
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
            "YAR" => "Ярославль",
            "PRM" => "Пермь",
            "KZN" => "Казань",
            "KRD" => "Краснодар",
            "CHL" => "Челябинск",
            "TYM" => "Тюмень",
            "KRY" => "Красноярск",
            "UFA" => "Уфа",
            "OMS" => "Омск",
            "HNM" => "Ханты-мансийск",
            "KRG" => "Курган",
            "KIR" => "Киров",
            "VGG" => "Волгоград",
            "NSK" => "Новосибирск",
        ),
        "CITIES_PRICE" => array(
            "Москва" => 1,
            "Санкт-Петербург" => 2,
            "Екатеринбург" => 4,
            "Нижний Новгород" => 5,
            "Ростов-на-Дону" => 6,
            "Минск" => 11,
            "Новосибирск" => 12,
            "Казань" => 13,
        ),
        "SUBDOMAIN" => array(
            "Санкт-Петербург" => "spb",
        ),
        "CACHE_TIME" => 0,
    ),
    false,
    array(
        "HIDE_ICONS" => "Y"
    )
); ?>

<? $APPLICATION->IncludeComponent(
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
); ?>

<? $APPLICATION->IncludeComponent(
    "custom:form.prototype",
    "callback",
    array(
        "FORM_ID" => 1,
        "FORM_ACTION" => "FORM_CALLBACK",
        "SUCCESS_MESSAGE" => "Спасибо, что выбрали нас! Мы перезвоним Вам в ближайшее время!",
        "YANDEX_COUNER" => "callMe_Send",
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
); ?>

<!--<pre>--><?php //var_dump()?><!--</pre>-->

<div class="main-container">
    <div id="header">
        <div class="h-top-block clear">
            <div class="w-h-top">
                <div class="city">
                    <!--Блок гео-->
                    <? $APPLICATION->IncludeComponent(
                        "custom:region-select.prototype",
                        "main",
                        array(),
                        false,
                        array(
                            "HIDE_ICONS" => "Y"
                        )
                    ); ?>
                </div>
                <div class="logo">
                    <!--Логотип-->
                    <a href="/"><img src="<?= SITE_TEMPLATE_PATH; ?>/images/header-logo.png" alt="Текстильторг"/></a>
                </div>
                <div class="cart">
                    <div class="wrapper">
                        <!--Корзина-->
                        <? $APPLICATION->IncludeComponent(
                            "bitrix:sale.basket.basket.line",
                            "cart",
                            array(
                                "HIDE_ON_BASKET_PAGES" => "N",
                                "PATH_TO_BASKET" => SITE_DIR . "cart/",
                                "PATH_TO_ORDER" => SITE_DIR . "order/",
                                "PATH_TO_PERSONAL" => SITE_DIR,
                                "PATH_TO_PROFILE" => SITE_DIR,
                                "PATH_TO_REGISTER" => SITE_DIR . "login/",
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
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="w-h-search">
                <!--Блок поиска-->
                <? if (SITE_ID == "by") {
                    $GLOBALS["arrFilterHeaderSearch"] = array(
                        "CATALOG_CURRENCY_11" => "BYN",
                        "PROPERTY_VIEW_SITE_RB_VALUE" => "Да"
                    );
                }
                if (SITE_ID == "s1") {
                    $GLOBALS["arrFilterHeaderSearch"] = array(
                        "PROPERTY_VIEW_SITE_RU_VALUE" => "Да"
                    );
                }
                $APPLICATION->IncludeComponent(
                    "custom:catalog.search.prototype",
                    "main",
                    array(
                        "IBLOCK_ID" => $GLOBALS["CATALOG_IBLOCK_ID"],
                        "ACTION_FORM" => "/search/",
                        "FILTER_NAME" => "arrFilterHeaderSearch",
                        "PRICE_ID" => $GLOBALS["PRICE_ID"],
                        "GEO_REGION_CITY_NAME" => $GLOBALS["GEO_REGION_CITY_NAME"]
                    ),
                    false,
                    array(
                        "HIDE_ICONS" => "Y"
                    )
                ); ?>
            </div>

            <div class="w-h-menu">
                <!--Меню-->
                <? $APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "top",
                    array(
                        "ROOT_MENU_TYPE" => "top",
                        "MENU_CACHE_TYPE" => "Y",
                        "MENU_CACHE_TIME" => "36000000",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_CACHE_GET_VARS" => array(),
                        "MAX_LEVEL" => "1",
                        "CHILD_MENU_TYPE" => "",
                        "USE_EXT" => "N",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "N"
                    ),
                    false
                ); ?>
            </div>
        </div>
    </div>

    <div id="subheader">
        <div class="subheader-inner">
            <div class="menu-category">
                <? $APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "catalog-header",
                    array(
                        "ROOT_MENU_TYPE" => "catalog_header",
                        "MENU_CACHE_TYPE" => "N",
                        "MENU_CACHE_TIME" => "36000000",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_CACHE_GET_VARS" => array(
                            "ID"
                        ),
                        "DEPTH_LEVEL" => "3",
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
                ); ?>
            </div>
            <div class="contact-btn">
                <ul>
                    <li><div class="tt-icons message-icon"></div><span>мессенджеры</span></li>
                    <li class="livetex-btn"><div class="tt-icons consultant-icon"></div><span>онлайн консультант</span></li>
                    <li>
                        <a href="#form_callback" class="callme fancybox" title="Обратный звонок"><div class="tt-icons call-icon"></div><span>обратный звонок</span></a>
                    </li>
                </ul>
            </div>
            <div class="phone">
                <? $APPLICATION->IncludeComponent(
                    "custom:region-phone.prototype",
                    "main",
                    array(
                        "FILES_PATH" => "/include/region-phone/",
                        "DEFAULT_FILE" => "default"
                    ),
                    false,
                    array(
                        "HIDE_ICONS" => "Y"
                    )
                ); ?>
            </div>
        </div>

        <div class="right-social">
            <? $APPLICATION->IncludeComponent(
                "bitrix:main.include",
                "",
                array(
                    "AREA_FILE_SHOW" => "file",
                    "PATH" => "/include/footer-social.php",
                    "EDIT_TEMPLATE" => "text.php"
                )
            ); ?>
        </div>

        <div class="right-social-mobile">
            <? $APPLICATION->IncludeComponent(
                "bitrix:main.include",
                "",
                array(
                    "AREA_FILE_SHOW" => "file",
                    "PATH" => "/include/footer-social-mobile.php",
                    "EDIT_TEMPLATE" => "text.php"
                )
            ); ?>
        </div>

    </div>

    <div class="content">
        <div class="main-slider">
            <!--Slider-->
            <? $APPLICATION->IncludeComponent(
                "custom:slider.propfilter.prototype",
                "",
                array(
                    "SECTION_ID" => $_REQUEST["FILTER_SECTION_ID"] ?: $_REQUEST["SECTION_ID"],
                    "PROPERTY" => "SECTION",
                    "FILTER_NAME" => "arrFilterHeaderSlider"
                ),
                false,
                array(
                    "HIDE_ICONS" => "Y"
                )
            ); ?>

            <? if ($_SESSION["GEO_REGION_CITY_NAME"]) {
                $arrFilterHeaderSlider[] = ["LOGIC" => "OR", 0 => ["PROPERTY_REGION_VALUE" => $_SESSION["GEO_REGION_CITY_NAME"]], 1 => ["=PROPERTY_REGION" => false]];
            }
            if (!IS_HOME) {
                $arrFilterHeaderSlider[] = ["=PROPERTY_ONLY_MAIN" => false];
            } ?>

            <? global $SLIDER_IBLOCK_ID; ?>
            <? if (IS_HOME): ?>
                <? $APPLICATION->IncludeComponent(
                "bitrix:news.list",
                "main-slider",
                array(
                    "ACTIVE_DATE_FORMAT" => "d.m.Y",
                    "ADD_SECTIONS_CHAIN" => "N",
                    "AJAX_MODE" => "Y",
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
                    "FIELD_CODE" => array(
                        0 => "PREVIEW_PICTURE",
                        1 => "",
                    ),
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
                    "PROPERTY_CODE" => array(
                        0 => "",
                        1 => "URL",
                        2 => "",
                    ),
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
                    "SORT_ORDER2" => "ASC",
                    "COMPONENT_TEMPLATE" => "slider",
                    "STRICT_SECTION_CHECK" => "N",
                    "COMPOSITE_FRAME_MODE" => "A",
                    "COMPOSITE_FRAME_TYPE" => "AUTO"
                ),
                false
            ); ?>
            <? endif ?>
        </div>
       <? if (IS_HOME):  ?>
        <p class="hidden-xs hidden-sm main n_mobile_only">В нашем интернет-магазине Вы найдёте модели лучших швейных, вышивальных, вязальных машин, оверлоки, коверлоки, технику для глажения и уборки известных мировых производителей, а также огромный ассортимент аксессуаров к ним. Вас ждёт отличный сервис, бесплатный тест-драйв и быстрая доставка в любую точку России.</p>
        <? endif ?>
    </div>
    <div id="cont_sm">
        <div id="text">
            <!--Контент-->
            <div id="cont">

                <div id="cont_center">
                    <!--Центральный блок-->
                    <div class="centerblock <? if (PAGE_FOLDER == "/cart/"): ?>border-radius<? endif ?>">
                        <? //if (!IS_HOME):
                        if (1 == 2):
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
                        <? endif ?>
                        <?
                        $curent_class = '';

                        if (IS_HOME) {
                            $curent_class = 'title_page';
                        }

                        if ($APPLICATION->GetCurPageParam() == '/cart/') {
                            $curent_class = 'basket_page';
                        }
                        ?>

                        <div class="inner <?= $curent_class; ?>" id="lay_body">
                            <? if (IS_HOME): ?>
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:menu",
                                    "catalog-popular",
                                    array(
                                        "ROOT_MENU_TYPE" => "catalog_header",
                                        "MENU_CACHE_TYPE" => "N",
                                        "MENU_CACHE_TIME" => "36000000",
                                        "MENU_CACHE_USE_GROUPS" => "Y",
                                        "MENU_CACHE_GET_VARS" => array(
                                            "ID"
                                        ),
                                        "DEPTH_LEVEL" => "3",
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
                                ); ?>

                            <? else: ?>
                                <? if (!defined("HIDE_PAGE_TITLE")): ?>
                                    <h1><? $APPLICATION->ShowTitle(true) ?></h1>
                                <? endif ?>
                            <? endif ?>


