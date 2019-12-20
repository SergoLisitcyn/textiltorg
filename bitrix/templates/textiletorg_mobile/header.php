<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "//www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?
    \Bitrix\Main\Data\StaticHtmlCache::getInstance()->markNonCacheable();

	global $SUBDOMAIN;
	
	$SUBDOMAIN["SPB"] = preg_match("/^spb/i",$_SERVER['SERVER_NAME']);	
?>
<html <?if (IS_HOME):?>class="home"<?endif?>>
<head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
         <?if ($SUBDOMAIN["SPB"]):?>
	    <meta name="yandex-verification" content="7cf72b030ce8eb78" />
	    <?else:?>
        <meta name="yandex-verification" content="7cf72b030ce8eb78" />
	    <meta name="yandex-verification" content="7cf72b030ce8eb78" />
        <?endif;?>
        <meta name="yandex-verification" content="ed830fbaed09bc71" />
	    <meta name="format-detection" content="telephone=no">
        <meta name="format-detection" content="address=no">
        <title><?$APPLICATION->ShowTitle()?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" rel="shortcut icon" type="image/x-icon" />
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity=	"sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">

    <?
        $APPLICATION->ShowHead();

        if (!$USER->IsAuthorized())
            CJSCore::Init(array('ajax', 'json', 'ls', 'session', 'jquery', 'popup', 'pull'));

        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/fonts/fonts.css");

		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-3.1.0.min.js");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/script.js");

		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/slick-1.6.0/slick/slick.min.js");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/slick-1.6.0/slick/slick.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/slick-1.6.0/slick/slick-theme.css");
		
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/vendor/kladr/jquery.kladr.min.js");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/vendor/kladr/jquery.kladr.min.css");

        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/jquery-ui.min.css");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-ui.min.js");

        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox.css");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox.pack.js");
        $APPLICATION->AddHeadScript("/pending_order/ajax/pending_order.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/vendor/jquery.validate.min.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/vendor/jquery.formatter.min.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/vendor/jquery.form.min.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.touchSwipe.min.js");

?>

		<?
			$currentPage = $APPLICATION->GetCurPage(true);
			if (preg_match('/filter\/clear/', $currentPage))
			{
				$path = substr($currentPage, 0 , strripos($currentPage, 'filter/clear/'));
				?>
				<link rel="canonical" href="<?=$path?>"/>
				<?
			}
		?>
    <script type="text/javascript">(window.Image ? (new Image()) : document.createElement('img')).src = 'https://vk.com/rtrg?p=VK-RTRG-132960-aBYmt';</script>

    <? if ($_SERVER['SERVER_NAME'] == 'www.textiletorg.ru'): ?>
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

    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','https://connect.facebook.net/en_US/fbevents.js');

        fbq('init', '309309606576872');
        fbq('track', "PageView");
    </script>
    <!-- End Facebook Pixel Code -->

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-P37LLBT');</script>
<!-- End Google Tag Manager -->
    <script>var siteid = '<?=SITE_ID?>';
        var siteDomen = '<?=$_SERVER["SERVER_NAME"]?>';</script>
</head>
<body><!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P37LLBT"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=309309606576872&ev=PageView&noscript=1"/></noscript>
<!--div class="swipe"></div-->
	<?if ($USER->IsAdmin() && !$_GET["no-panel"]):?>
		<?$APPLICATION->ShowPanel();?>
	<?endif?>
    <?$APPLICATION->IncludeComponent(
        "custom:region.prototype",
        "mobile",
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
                "YAR" => "Ярославль",
				"NSK" => "Новосибирск",
				"KZN" => "Казань",
                "PRM" => "Пермь"
            ),
            "SUBDOMAIN" => array(
	        	 "Санкт-Петербург" => "spb",
	        ),
            "CACHE_TIME" => 0,
            "CITIES_PRICE" => array(
                "Москва" => 1,
                "Санкт-Петербург" => 2,
                "Екатеринбург" => 4,
                "Нижний Новгород" => 5,
                "Ростов-на-Дону" => 6,
				"Новосибирск" => 12,
				"Казань" => 13,
                "Минск" => 11
            )
        ),
        false,
        array(
            "HIDE_ICONS" => "Y"
        )
    );?>
	<div class="main-container">
		<div id="header">
                <div class="head_top_button">
                    <div class="header-table">
                        <div class="header-top">
                            <div class="tdmenu">
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:menu",
                                    "catalog",
                                    array(
                                        "ROOT_MENU_TYPE" => "catalog_mobile",
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
                                        "COMPONENT_TEMPLATE" => "top"
                                    ),
                                    false,
                                    array("HIDE_ICONS" => "Y")
                                );?>
                            </div>
                            <div class="center_block">
                                <a href="/" class="logo"></a>
                            </div>
                            <div class="tdbasket">
								<div class="search"></div>
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:sale.basket.basket.line",
                                    "header",
                                    array(
                                        "HIDE_ON_BASKET_PAGES" => "Y",
                                        "PATH_TO_BASKET" => SITE_DIR."m/cart/",
                                        "PATH_TO_ORDER" => SITE_DIR."m/order/make/",
                                        "PATH_TO_PERSONAL" => SITE_DIR."m/",
                                        "PATH_TO_PROFILE" => SITE_DIR."m/",
                                        "PATH_TO_REGISTER" => SITE_DIR."m/login/",
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
                                    array("HIDE_ICONS" => "Y")
                                );?>
                            </div>
                        </div>
                        <div>
                            <div>
                                <div id="header-phone" class="header-center" style="    margin-top: -10px;">
									<div></div>
									<div>
										<?$APPLICATION->IncludeComponent(
											"custom:region-phone.prototype",
											"",
											array(
												"FILES_PATH" => "/include/region-phone/",
												"DEFAULT_FILE" => "default"
											),
											false,
											array(
												"HIDE_ICONS" => "Y"
											)
										);?>
									</div>
									<div>
										<?$APPLICATION->IncludeComponent(
											"custom:region-select.prototype",
											"mobile-header",
											array(),
											false,
											array(
												"HIDE_ICONS" => "Y"
											)
										);?>
									</div>
                                </div>
                                <div id="wrapper-header-search">
                                    <?/*$APPLICATION->IncludeComponent("bitrix:search.form", "mobile_top",
                                        Array(
                                            "PAGE" => "#SITE_DIR#/search/index.php",
                                            "USE_SUGGEST" => "Y",
                                        ),
                                        false,
                                        array("HIDE_ICONS" => "Y")
                                    );*/?>
									<?$APPLICATION->IncludeComponent(
                                        "custom:catalog.search.prototype",
                                        "mobile",
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
                                    );?>
                                </div>
                            </div>
                        </div>
                    </div>			
                </div>
		</div>

		<div id="wrapper">
            <?if ($APPLICATION->GetCurPage(false) != "/cart/"):?>
				<?$APPLICATION->IncludeComponent(
					"bitrix:menu",
					"mobile-top",
					array(
						"ALLOW_MULTI_SELECT" => "N",
						"CHILD_MENU_TYPE" => "top_child",
						"DELAY" => "N",
						"MAX_LEVEL" => "2",
						"MENU_CACHE_GET_VARS" => array(
							0 => "",
						),
						"MENU_CACHE_TIME" => "3600",
						"MENU_CACHE_TYPE" => "N",
						"MENU_CACHE_USE_GROUPS" => "Y",
						"ROOT_MENU_TYPE" => "top_mobile",
						"USE_EXT" => "N",
					),
					false
				);?>
            <?endif?>

			<?$APPLICATION->IncludeComponent(
	"bitrix:breadcrumb", 
	"breadcrumbs", 
	array(
		"START_FROM" => "{(Helper::IsRealFilePath(array(\"/catalog/index.php\",\"/catalog/detail/index.php\")))?\"1\":\"0\"}",
		"PATH" => "",
		"SITE_ID" => "s1",
		"COMPONENT_TEMPLATE" => "breadcrumbs"
	),
	false
);?>

			<div class="content_block">
				<?if ((!defined("HIDE_PAGE_TITLE") || HIDE_PAGE_TITLE != true) && !(IS_HOME)):?>
					<h1><?$APPLICATION->ShowTitle(true)?></h1>
				<?endif?>