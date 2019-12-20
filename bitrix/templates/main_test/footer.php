                                    <?$APPLICATION->IncludeComponent(
                                        "bitrix:main.include",
                                        "",
                                        array(
                                            "AREA_FILE_SHOW" => "page",
                                            "AREA_FILE_SUFFIX" => "inner_bottom",
                                            "COMPONENT_TEMPLATE" => ".default",
                                            "EDIT_TEMPLATE" => ""
                                        )
                                    );?>
                                </div>
                            </div>
                        </div>

                        <div class="clear"></div>

                        <?$APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            "bottom-page",
                            array(
                                "AREA_FILE_SHOW" => "page",
                                "AREA_FILE_SUFFIX" => "bottom",
                                "COMPONENT_TEMPLATE" => ".default",
                                "EDIT_TEMPLATE" => ""
                            )
                        );?>

                        <div id="footer">
                            <div id="lay_f5">
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:menu",
                                    "bottom",
                                    array(
                                        "ROOT_MENU_TYPE" => "bottom",
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
                                );?>
                                <div class="fbig">
                                    <div class="fbig_block">
                                        <?$APPLICATION->IncludeComponent(
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
                                        );?>
                                    </div>

                                    <div class="fbig_block">
                                        <?$APPLICATION->IncludeComponent(
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
                                        );?>
                                    </div>


                                    <div class="fbig_block">
                                        <?$APPLICATION->IncludeComponent(
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
                                        );?>
                                    </div>
                                    <div class="fbig_block">
                                        <?$APPLICATION->IncludeComponent(
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
                                        );?>
                                    </div>
                                    <div class="fbig_contacts">
                                        <div class="fbig_contacts-head">ЕСТЬ ВОПРОСЫ? ЗВОНИТЕ!</div>
                                        <?$APPLICATION->IncludeComponent(
                                            "custom:region-select.prototype",
                                            "footer",
                                            array(
                                                "DEFAULT_REGION_ID" => "19",
                                                "DEFAULT_REGION_CITY_NAME" => "Москва"
                                            ),
                                            false,
                                            array(
                                                "HIDE_ICONS" => "Y"
                                            )
                                        );?>

                                        <?$APPLICATION->IncludeComponent(
                                            "custom:region-phone.prototype",
                                            "signature",
                                            array(
                                                "FILES_PATH" => "/include/region-phone/",
                                                "DEFAULT_FILE" => "default"
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
                                                "PATH" => "/include/footer-phone.php",
                                                "EDIT_TEMPLATE" => "text.php"
                                            )
                                        );?>

                                        <a class="callme button fancybox" href="#form_callback" title="Мы перезвоним Вам через 5 минут или раньше!" <?=Helper::GetYandexCounter("callMe")?>>Заказать звонок</a>
                                    </div>
									<div style="clear: both;"></div>
                                    <div class="fbig_under">
										<div class="info-oferta">
                                             <?$APPLICATION->IncludeComponent(
                                                "bitrix:main.include",
                                                "",
                                                array(
                                                    "AREA_FILE_SHOW" => "file",
                                                    "PATH" => "/include/info-oferta.php",
                                                    "EDIT_TEMPLATE" => "text.php"
                                                )
                                            );?>
                                        </div>
                                        <div class="fcopy">
                                            <?$APPLICATION->IncludeComponent(
                                                "bitrix:main.include",
                                                "",
                                                array(
                                                    "AREA_FILE_SHOW" => "file",
                                                    "PATH" => "/include/footer-copy.php",
                                                    "EDIT_TEMPLATE" => "text.php"
                                                )
                                            );?>
                                        </div>
                                    </div>
                                </div>
                                <div class="f_border">
                                    <div class="frek">
                                        <?$APPLICATION->IncludeComponent(
                                            "bitrix:main.include",
                                            "",
                                            array(
                                                "AREA_FILE_SHOW" => "file",
                                                "PATH" => "/include/footer-requisites.php",
                                                "EDIT_TEMPLATE" => "text.php"
                                            )
                                        );?>
                                    </div>
                                    <div class="fsoc">
                                        <p>Следуйте за нами</p>
                                        <div class="htb_soc">
                                            <?$APPLICATION->IncludeComponent(
                                                "bitrix:main.include",
                                                "",
                                                array(
                                                    "AREA_FILE_SHOW" => "file",
                                                    "PATH" => "/include/footer-social.php",
                                                    "EDIT_TEMPLATE" => "text.php"
                                                )
                                            );?>
                                        </div>
                                    </div>
                                </div>

                                <div class="f_rules">
                                    <?$APPLICATION->IncludeComponent(
                                        "bitrix:main.include",
                                        "",
                                        array(
                                            "AREA_FILE_SHOW" => "file",
                                            "PATH" => "/include/footer-rules.php",
                                            "EDIT_TEMPLATE" => "text.php"
                                        )
                                    );?>
                                </div>

                                <div class="counters">
                                    <div class="loader"></div>
                                    <a style="position: fixed; bottom: 25px; right: 1px; cursor:pointer; display:none;" href="#" id="Go_Top">
                                        <img src="<?=SITE_TEMPLATE_PATH?>/js/strelka.png" alt="Наверх" title="Наверх">
                                    </a>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                )
            ),
            false,
            array(
                "HIDE_ICONS" => "Y",
            )
        );?>


        <?$APPLICATION->IncludeComponent("bitrix:catalog.compare.list",
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
        );?>

        <div id="popup-cart" class="popup-cart">
            <div class="popup-cart-header">Товар, добавленный в корзину</div>

            <table class="popup-cart-goods" border="0" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <td colspan="2">Товар</td>
                        <td>Стоимость</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="100">
                            <div class="popup-cart-good-picture"></div>
                        </td>
                        <td>
                            <div class="popup-cart-product"></div>
                            <div></div>
                        </td>
                        <td class="popup-cart-good-color-red">
                            <big></big> руб.
                        </td>
                    </tr>
                </tbody>
            </table>

            <div id="popup-cart-more-goods">

            </div>

            <div class="popup-cart-bottom-button">
                <a href="#fancybox-close" class="silver_button">Продолжить покупки</a>
                <a href="/cart/" class="red_button">Оформить заказ</a>
            </div>
        </div>

        <audio id="audio"></audio>
		<div id="toTop">&nbsp;</div>

        <?$APPLICATION->IncludeComponent(
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
        );?>

        <?$APPLICATION->IncludeComponent(
            "custom:targetmail.prototype",
            "",
            array(
                "ID" => "3077731",
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
        );?>
        <?$APPLICATION->IncludeComponent(
            "custom:basket-fixed.prototype",
            "",
            array(),
            false,
            array(
                "HIDE_ICONS" => "Y"
            )
        );?>
    </body>
</html>