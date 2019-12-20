<? $APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    array(
        "AREA_FILE_SHOW" => "page",
        "AREA_FILE_SUFFIX" => "inner_bottom",
        "COMPONENT_TEMPLATE" => ".default",
        "EDIT_TEMPLATE" => ""
    )
); ?>
</div>
</div>
</div>

<div class="clear"></div>

<?// $APPLICATION->IncludeComponent(
//    "bitrix:main.include",
//    "footer-phone",
//    array(
//        "AREA_FILE_SHOW" => "page",
//        "AREA_FILE_SUFFIX" => "bottom",
//        "COMPONENT_TEMPLATE" => "footer-phone",
//        "EDIT_TEMPLATE" => "",
//        "COMPOSITE_FRAME_MODE" => "A",
//        "COMPOSITE_FRAME_TYPE" => "AUTO"
//    ),
//    false
//); ?>


</div>
</div>
</div>

<div id="footer">
    <div class="bottom-menu">
        <div class="info-menu">
            <? $APPLICATION->IncludeComponent(
                "bitrix:menu",
                "bottom-block",
                array(
                    "ROOT_MENU_TYPE" => "bottom_about",
                    "MENU_CACHE_TYPE" => "Y",
                    "MENU_CACHE_TIME" => "36000000",
                    "MENU_CACHE_USE_GROUPS" => "Y",
                    "MENU_CACHE_GET_VARS" => array(),
                    "MAX_LEVEL" => "1",
                    "CHILD_MENU_TYPE" => "",
                    "USE_EXT" => "N",
                    "DELAY" => "N",
                    "ALLOW_MULTI_SELECT" => "N",
                    "TITLE" => ""
                ),
                false
            ); ?>
        </div>
    </div>

    <div class="footer-blocks">
        <div class="wrapper footer_w">
            <div class="menu-block">
                <? $APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "bottom-block",
                    array(
                        "ROOT_MENU_TYPE" => "bottom_actions",
                        "MENU_CACHE_TYPE" => "Y",
                        "MENU_CACHE_TIME" => "36000000",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_CACHE_GET_VARS" => array(),
                        "MAX_LEVEL" => "1",
                        "CHILD_MENU_TYPE" => "",
                        "USE_EXT" => "Y",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "N",
                        "TITLE" => "Акции"
                    ),
                    false
                ); ?>
            </div>
            <div class="menu-block">
                <? $APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "bottom-block",
                    array(
                        "ROOT_MENU_TYPE" => "bottom_info",
                        "MENU_CACHE_TYPE" => "Y",
                        "MENU_CACHE_TIME" => "36000000",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_CACHE_GET_VARS" => array(),
                        "MAX_LEVEL" => "1",
                        "CHILD_MENU_TYPE" => "",
                        "USE_EXT" => "N",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "N",
                        "TITLE" => "Информация"
                    ),
                    false
                ); ?>
            </div>
            <div class="menu-block">
                <? $APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "bottom-block",
                    array(
                        "ROOT_MENU_TYPE" => "bottom_help",
                        "MENU_CACHE_TYPE" => "Y",
                        "MENU_CACHE_TIME" => "36000000",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_CACHE_GET_VARS" => array(),
                        "MAX_LEVEL" => "1",
                        "CHILD_MENU_TYPE" => "",
                        "USE_EXT" => "N",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "N",
                        "TITLE" => "Полезное"
                    ),
                    false
                ); ?>
            </div>

            <div class="info-block">
                <div class="info-oferta">
                    <? $APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => "/include/info-oferta.php",
                            "EDIT_TEMPLATE" => "text.php"
                        )
                    ); ?>
                </div>
                <div class="fcopy">
                    <? $APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => "/include/footer-copy.php",
                            "EDIT_TEMPLATE" => "text.php"
                        )
                    ); ?>
                </div>
            </div>
        </div>
        <div class="subfooter">
            <div class="wrapper footer_w">
                <div class="items">
                    <div class="logo">
                        <!--Логотип-->
                        <a href="/"><img class = "footer_logo" src="<?= SITE_TEMPLATE_PATH; ?>/images/footer-logo.png" alt="Текстильторг"/></a>
                    </div>
                    <div class= "n_req_footer">
                        <div class="frek">
                            <? $APPLICATION->IncludeComponent(
                                "bitrix:main.include",
                                "",
                                array(
                                    "AREA_FILE_SHOW" => "file",
                                    "PATH" => "/include/footer-requisites.php",
                                    "EDIT_TEMPLATE" => "text.php"
                                )
                            ); ?>
                        </div>
                    </div>
                    <div class="fsoc">
                        <div class="htb_soc">
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
                    </div>
                </div>
            </div>
        </div>
        <div class="rules">
            <div class="footer_w_r">
                <? $APPLICATION->IncludeComponent(
                    "bitrix:main.include",
                    "",
                    array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => "/include/footer-rules.php",
                        "EDIT_TEMPLATE" => "text.php"
                    )
                ); ?>
            </div>
        </div>
    </div>
</div>

<?// $APPLICATION->IncludeComponent(
//    "custom:form.prototype",
//    "callback-footer",
//    array(
//        "FORM_ID" => 1,
//        "FORM_ACTION" => "FORM_CALLBACK",
//        "SUCCESS_MESSAGE" => "Спасибо, что выбрали нас! Мы перезвоним Вам в ближайшее время!",
//        "YANDEX_COUNER" => "callMe_Send",
//        "FIELDS" => array(
//            "form_text_1",
//            "form_text_2"
//        )
//    ),
//    false,
//    array(
//        "HIDE_ICONS" => "Y",
//    )
//); ?>

<? $APPLICATION->IncludeComponent(
    "bitrix:catalog.compare.list",
    "compare-block",
    array(
        "ACTION_VARIABLE" => "action",  // Название переменной, в которой передается действие
        "AJAX_MODE" => "N", // Включить режим AJAX
        "AJAX_OPTION_ADDITIONAL" => "", // Дополнительный идентификатор
        "AJAX_OPTION_HISTORY" => "N",   // Включить эмуляцию навигации браузера
        "AJAX_OPTION_JUMP" => "N",  // Включить прокрутку к началу компонента
        "AJAX_OPTION_STYLE" => "Y", // Включить подгрузку стилей
        "COMPARE_URL" => "/compare/",  // URL страницы с таблицей сравнения
        "DETAIL_URL" => "", // URL, ведущий на страницу с содержимым элемента раздела
        "IBLOCK_ID" => "8", // Инфоблок
        "IBLOCK_TYPE" => "catalog", // Тип инфоблока
        "NAME" => "CATALOG_COMPARE_LIST",   // Уникальное имя для списка сравнения
        "POSITION" => "top left",   // Положение на странице
        "POSITION_FIXED" => "Y",    // Отображать список сравнения поверх страницы
        "PRODUCT_ID_VARIABLE" => "id",  // Название переменной, в которой передается код товара для покупки
    ),
    false
); ?>

<div id="popup-cart" class="popup-cart" style="display: none">
    <div class="popup-cart-header">Товар, добавленный в корзину</div>

    <table class="popup-cart-goods" style="border:0; padding:0; border-collapse: collapse;">
        <thead>
        <tr>
            <td colspan="2">Товар</td>
            <td>Стоимость</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="width:100px">
                <div class="popup-cart-good-picture"></div>
            </td>
            <td>
                <div class="popup-cart-product"></div>
                <div></div>
            </td>
            <td class="popup-cart-good-color-red">
                <span style="font-size: 1.2em"></span> руб.
            </td>
        </tr>
        </tbody>
    </table>

    <div id="popup-cart-more-goods">

    </div>

    <?$APPLICATION->IncludeComponent(
        "custom:order.prototype",
        "popup",
        array(
            "COUNTRY_NAME_ORIG" => $GLOBALS["REGION_COUNTRY_NAME_ORIG"],
            "GEO_REGION_CITY_NAME" => $GLOBALS["GEO_REGION_CITY_NAME"],
            "DELIVERYES" => array(
                "Регион" => array(
                    "4" => "Доставка",
                    "14" => "Экспресс доставка"
                ),
                "Москва" => array(
                    "3" => "Доставка курьером",
                ),
                "Санкт-Петербург" => array(
                    "3" => "Доставка курьером",
                ),
                "Екатеринбург" => array(
                    "3" => "Доставка курьером",
                ),
                "Нижний Новгород" => array(
                    "3" => "Доставка курьером",
                ),
                "Ростов-на-Дону" => array(
                    "3" => "Доставка курьером",
                ),
                "Минск" => array(
                    "3" => "Доставка курьером",
                ),
            ),
            "PAY_SYSTEMS" => array(
                "2" => "Наличными при получении",
                "3" => array(
                    "NAME" => "В кредит или рассрочку",
                    "KREDIT" => 0, // Обработчик в JS.
                    "HELP" => "Вы можете оформить покупку в онлайн кредит, для этого потребуется лишь Ваше желание!<br>Наш специалист свяжется с Вами  после оформления заказа и проведет все необходимые процедуры.<br>После одобрения заявки (ожидание от 30 до 90 мин.) банком партнером и поступления оплаты, Вы получите свой заказ!<br>Процент одобрения высокий, так как мы работам с несколькими банками партнерами!"
                ),
                "6" => "Банковской картой онлайн"
            ),
            "DELIVERY_STORE" => array(
                "2" => "Самовывоз"
            ),
            "FORM_ACTION" => "/order/"
        ),
        false,
        array(
            "HIDE_ICONS" => "Y"
        )
    );?>
</div>

<audio id="audio"></audio>
<div id="toTop" class="tt-icons top-btn-icon"></div>
<? if (IS_HOME) {
    $APPLICATION->IncludeComponent(
        "custom:buy.prototype",
        "main",
        array(
            "CACHE_FILTER" => "Y",
            "CACHE_GROUPS" => "Y",
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => "N",
            "ACTION" => "BUY_ONE_CLICK",
            "YANDEX_COUNER" => "oformit_zakaz",
            "SUCCESS_MESSAGE" => array(
                "FILE" => "bitrix/components/custom/buy.prototype/templates/main/template-message.php"
            )
        ),
        false,
        array(
            "HIDE_ICONS" => "Y",
        )
    );
} ?>
<? $APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    array(
        "AREA_FILE_SHOW" => "file",
        "PATH" => "/include/footer-scripts.php",
        "EDIT_TEMPLATE" => "text.php"
    ),
    false,
    array(
        "HIDE_ICONS" => "Y"
    )
); ?>

<? $APPLICATION->IncludeComponent(
    "custom:targetmail.prototype",
    "",
    array(
        "ID" => "2791918",
        "PAGE_CATEGORY" => "/catalog/index.php",
        "PAGE_PRODUCT" => "/catalog/detail/index.php",
        "PAGE_CART" => "/^\/cart/",
        "PAGE_PURCHASE" => "/^\/order/",
        "PRODUCT_ID" => $GLOBALS["TARGET_MAIL_PRODUCT_ID"],
        "TOTAL_VALUE" => $GLOBALS["TARGET_MAIL_TOTAL_VALUE"],
        "LIST" => "1"
    ),
    false,
    array(
        "HIDE_ICONS" => "Y"
    )
); ?>
<? $APPLICATION->IncludeComponent(
    "custom:basket-fixed.prototype",
    "main",
    array(),
    false,
    array(
        "HIDE_ICONS" => "Y"
    )
); ?>

<? $APPLICATION->IncludeComponent(
    "custom:gdeslon.prototype",
    "",
    array(
        "MID" => "88568",
        "PAGE_CATEGORY" => "/catalog/index.php",
        "PAGE_PRODUCT" => "/catalog/detail/index.php",
        "PAGE_CART" => "/^\/cart/",
        "PAGE_PURCHASE" => "/^\/order/",
        "PAGE_SEARCH" => "/^\/search/",
        "CODES" => "",
        "CAT_ID" => ""
    ),
    false,
    array(
        "HIDE_ICONS" => "Y"
    )
); ?>



</body>
</html>

