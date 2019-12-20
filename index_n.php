<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Швейные машины, оверлоки, вышивальные машины, товары для шитья в Москве по самым низким ценам - ТекстильТорг");
$APPLICATION->SetPageProperty("keywords", "купить швейные машины, швейные машинки, оверлоки");
$APPLICATION->SetPageProperty("description", "ТекстильТорг – сеть магазинов с огромным выбором швейных машинок, оверлоков, а так же товаров для шитья и рукоделия. Мы гарантируем СуперЦены и 100% наличие на все товары представленные в нашем каталоге.");
if (!defined("IS_MOBILE")) {
    $APPLICATION->SetTitle("Магазин швейной и мелкой бытовой техники ");
} else {
    $APPLICATION->SetTitle("ТекстильТорг — магазин швейной и гладильной техники");
}
//if($_GET["auth"] == "y") $USER->Authorize(1);
?>

<div style = "width:100%;min-height: 800px;">

    <div style = "width:1200px;margin: 0 auto;text-align: center; padding: 5px;">

        <div class="center">
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

            <?
            if ($_SESSION["GEO_REGION_CITY_NAME"]) {
                $arrFilterHeaderSlider[] = ["LOGIC" => "OR", 0 => ["PROPERTY_REGION_VALUE" => $_SESSION["GEO_REGION_CITY_NAME"]], 1 => ["=PROPERTY_REGION" => false]];
            }
            if (!IS_HOME) {
                $arrFilterHeaderSlider[] = ["=PROPERTY_ONLY_MAIN" => false];
            }
            ?>
            <? global $SLIDER_IBLOCK_ID; ?>
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
        </div>

        <div class = "ay-main-container" >

            <div class = "n_index_popular_head" >
                <table>
                    <tr>
                        <td>
                            <div class = "n_index_popular_head_text" > ПОПУЛЯРНЫЕ КАТЕГОРИИ ТОВАРОВ </div>
                        </td>
                        <td>
                            <div class = "n_index_popular_head_botton" > ВЕСЬ КАТАЛОГ </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class = "n_index_popular_blocks">
                <div class = "n_index_popular_block">
                    <div class = "n_index_popular_l_c" >
                        <div class = "n_index_pupular_img n_index_pupular_img_1"></div>
                    </div>
                        <div class = "n_index_pupular_block_h"> Все для вышивания </div>
                        <div class = "n_index_pupular_block_l">
                            <a href = ""> Вышивательные машины </a> <br>
                            <a href = ""> Оверлоки </a> <br>
                            <a href = ""> Манекены </a>
                        </div>
                </div>
                <div class = "n_index_popular_block">
                    <div class = "n_index_popular_l_c" >
                        <div class = "n_index_pupular_img n_index_pupular_img_2"></div>
                    </div>
                    <div class = "n_index_pupular_block_h"> Все для вышивания </div>
                    <div class = "n_index_pupular_block_l">
                        <a href = ""> Вышивательные машины </a> <br>
                        <a href = ""> Швейно-вышивательные машины </a> <br>
                        <a href = ""> Принтеры по текстилю </a>
                    </div>
                </div>
                <div class = "n_index_popular_block">
                    <div class = "n_index_popular_l_c" >
                        <div class = "n_index_pupular_img n_index_pupular_img_3"></div>
                    </div>
                    <div class = "n_index_pupular_block_h"> Все для вязания </div>
                    <div class = "n_index_pupular_block_l">
                        <a href = ""> Вязальные машины </a> <br>
                        <a href = ""> Кеттельные  машины </a> <br>
                        <a href = ""> Ткатские станки </a>
                    </div>
                </div>
            </div>
            <div class = "n_index_popular_blocks">
                <div class = "n_index_popular_block">
                    <div class = "n_index_popular_l_c" >
                        <div class = "n_index_pupular_img n_index_pupular_img_4"></div>
                    </div>
                    <div class = "n_index_pupular_block_h"> Все для глажения </div>
                    <div class = "n_index_pupular_block_l">
                        <a href = ""> Вышивательные машины </a> <br>
                        <a href = ""> Швейно-вышивательные машины </a> <br>
                        <a href = ""> Принтеры по текстилю </a>
                    </div>
                </div>

                <div class = "n_index_popular_block">
                    <div class = "n_index_popular_l_c" >
                        <div class = "n_index_pupular_img n_index_pupular_img_5"></div>
                    </div>
                    <div class = "n_index_pupular_block_h"> Все для уборки </div>
                    <div class = "n_index_pupular_block_l">
                        <a href = ""> Пылесосы </a> <br>
                        <a href = ""> Пароочистители </a> <br>
                        <a href = ""> Паровые швабры </a>
                    </div>
                </div>

                <div class = "n_index_popular_block">
                    <div class = "n_index_popular_l_c" >
                        <div class = "n_index_pupular_img n_index_pupular_img_6"></div>
                    </div>
                    <div class = "n_index_pupular_block_h"> Аксессуары и расходные материалы </div>
                    <div class = "n_index_pupular_block_l">
                        <a href = ""> Лапки, иглы, нити, фурнитура  </a>
                    </div>
                </div>
            </div>
        </div>

        <div class = "ay-main-container" style = "margin-top: 30px;" id = "userstyle_maincontent" >
		
                <?if (!defined("IS_MOBILE")):?>
                <? $arrFilterProducts = array("ID" => array(1617,610,443,252,474)); ?>
                <?$APPLICATION->IncludeComponent(
                "bitrix:catalog.section",
                "slider",
                array(
                    "IBLOCK_ID" => "8",
                    "IBLOCK_TYPE" => "catalog",
                    "SHOW_ALL_WO_SECTION" => "Y",
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "CACHE_FILTER" => "Y",
                    "CACHE_GROUPS" => "Y",
                    "CACHE_TIME" => "36000000",
                    "CACHE_TYPE" => "Y",
                    "FILTER_NAME" => "arrFilterProducts",
                    "PRICE_CODE" => $GLOBALS["CITY_PRICE_CODE"],
                    "GEO_REGION_CITY_NAME" => $GLOBALS["GEO_REGION_CITY_NAME"],
                    "ADD_PROPERTIES_TO_BASKET" => "Y",
                    "MESS_BTN_ADD_TO_BASKET" => "В корзину",
                    "ADD_TO_BASKET_ACTION" => "ADD",
                    "REGION_PRICE_CODE_DEFAULT" => "Москва",
                    "COMPONENT_TITLE" => "Лидеры продаж",
                    "COMPONENT_TITLE_COLOR" => "red",
                ),
                false
                );?>
                <?$APPLICATION->IncludeComponent(
                "bitrix:catalog.section",
                "slider",
                array(
                    "IBLOCK_ID" => "8",
                    "IBLOCK_TYPE" => "catalog",
                    "SHOW_ALL_WO_SECTION" => "Y",
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "CACHE_FILTER" => "Y",
                    "CACHE_GROUPS" => "N",
                    "CACHE_TIME" => "36000000",
                    "CACHE_TYPE" => "Y",
                    "FILTER_NAME" => "arrFilterProducts",
                    "PRICE_CODE" => $GLOBALS["CITY_PRICE_CODE"],
                    "GEO_REGION_CITY_NAME" => $GLOBALS["GEO_REGION_CITY_NAME"],
                    "ADD_PROPERTIES_TO_BASKET" => "Y",
                    "MESS_BTN_ADD_TO_BASKET" => "В корзину",
                    "ADD_TO_BASKET_ACTION" => "ADD",
                    "REGION_PRICE_CODE_DEFAULT" => "Москва",
                    "COMPONENT_TITLE" => "Новинки",
                    "COMPONENT_TITLE_COLOR" => "yellow",
                ),
                false
                );?>
                <?$APPLICATION->IncludeComponent(
                "bitrix:catalog.section",
                "slider",
                array(
                    "IBLOCK_ID" => "8",
                    "IBLOCK_TYPE" => "catalog",
                    "SHOW_ALL_WO_SECTION" => "Y",
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "CACHE_FILTER" => "Y",
                    "CACHE_GROUPS" => "Y",
                    "CACHE_TIME" => "36000000",
                    "CACHE_TYPE" => "Y",
                    "FILTER_NAME" => "arrFilterProducts",
                    "PRICE_CODE" => $GLOBALS["CITY_PRICE_CODE"],
                    "GEO_REGION_CITY_NAME" => $GLOBALS["GEO_REGION_CITY_NAME"],
                    "ADD_PROPERTIES_TO_BASKET" => "Y",
                    "MESS_BTN_ADD_TO_BASKET" => "В корзину",
                    "ADD_TO_BASKET_ACTION" => "ADD",
                    "REGION_PRICE_CODE_DEFAULT" => "Москва",
                    "COMPONENT_TITLE" => "Скидки и акции",
                    "COMPONENT_TITLE_COLOR" => "yellow",
                ),
                false
                );?>
            <?endif?>

        </div>

        <div class = "ay-main-container" >
            <div class = "n_index_pred_head" >
                <div class = "n_index_pred_head_text" > ЧТО МЫ МОЖЕМ ВАМ ПРЕДЛОЖИТЬ? </div>
            </div>

            <div class = "n_index_pred_blocks">
                <div class = "n_index_pred_block">
                    <div class = "n_index_pred_l_c" >
                        <div class = "n_index_pred_img n_index_pred_img_1"></div>
                    </div>
                    <div class = "n_index_pred_block_h"> У нас есть всё, что Вам нужно </div>
                    <div class = "n_index_pred_block_l">
                        Вы всегда найдёте то, что хотели, если посетите наш магазин.
                    </div>
                </div>
                <div class = "n_index_pred_block">
                    <div class = "n_index_pred_l_c" >
                        <div class = "n_index_pred_img n_index_pred_img_2"></div>
                    </div>
                    <div class = "n_index_pred_block_h"> Предложение эксклюзивного товара </div>
                    <div class = "n_index_pred_block_l">
                        Только в ТекстильТорге Вы сможете найти эксклюзивные модели, которых нет на других торговых площадках.
                    </div>
                </div>
                <div class = "n_index_pred_block">
                    <div class = "n_index_pred_l_c" >
                        <div class = "n_index_pred_img n_index_pred_img_3"></div>
                    </div>
                    <div class = "n_index_pred_block_h"> Консультируем круглосуточно — ценим каждого покупателя! </div>
                    <div class = "n_index_pred_block_l">
                        Если Вы никак не можете сделать выбор, наши менеджеры придут к Вам на помощь, проконсультируют по интересующим Вас вопросам и дадут подробную информацию по любой модели.
                    </div>
                </div>
            </div>
            <div class = "n_index_pred_blocks">
                <div class = "n_index_pred_block">
                    <div class = "n_index_pred_l_c" >
                        <div class = "n_index_pred_img n_index_pred_img_6"></div>
                    </div>
                    <div class = "n_index_pred_block_h">  Частое обновление ассортимента   </div>
                    <div class = "n_index_pred_block_l">
                        Мы ежеминутно отслеживаем информацию производителей о новинках и как можно быстрее представляем их Вам.
                    </div>
                </div>

                <div class = "n_index_pred_block">
                    <div class = "n_index_pred_l_c" >
                        <div class = "n_index_pred_img n_index_pred_img_5"></div>
                    </div>
                    <div class = "n_index_pred_block_h"> У нас антикризисные цены </div>
                    <div class = "n_index_pred_block_l">
                        Расценки на наши товары всегда были, есть и остаются ниже рыночных. Секрет в том, что мы работаем напрямую с производителями.
                    </div>
                </div>

                <div class = "n_index_pred_block">
                    <div class = "n_index_pred_l_c" >
                        <div class = "n_index_pred_img n_index_pred_img_4"></div>
                    </div>
                    <div class = "n_index_pred_block_h"> Тестируем машинки в Вашем присутствии </div>
                    <div class = "n_index_pred_block_l">
                        При личном посещении нашего магазина Вы сможете получить всю информацию об интересующем Вас товаре и проверить его в действии, заказав бесплатный тест-драйв.
                    </div>
                </div>
            </div>
        </div>

        <div class = "ay-main-container" >

            <div class="accordion" id="accordion">
                <div data-target="technology">
                    Как найти и купить лучшую технику?
                </div>
                <div id="technology">
                    <p>Специально для Вас мы подготовили на сайте <a href="https://www.textiletorg.ru/poleznoe/obzory/">раздел</a> с полезной информацией, которая поможет Вам разобраться в огромном мире технических характеристик и разнообразии функций представленных моделей. </p>
                    <p>Наши обзорные статьи и бесплатные тест-драйвы о товарах расскажут о том:</p>
                    <p>· как работают швейно-вышивальные машинки, <a href="https://www.textiletorg.ru/overloki/">оверлоки</a>, а также парогенераторы и <a href="https://www.textiletorg.ru/gladilnye-sistemy/">гладильные системы</a>;</p>
                    <p>· какие дополнительные аксессуары могут пригодиться для улучшения функционала Вашей влажно-тепловой и швейно-вышивальной техники;</p>
                    <p>· какие подарки и акции Вас ждут.</p>
                    <p>Кроме того, мы составили подробное описание каждой модели и приложили инструкции по эксплуатации. Для всестороннего знакомства с товаром мы размещаем авторитетное мнение экспертов и отзывы тех, кто уже приобрёл <a href="https://www.textiletorg.ru/shveynye-mashiny/">швейные машинки</a> в нашем магазине.</p>
                    <p>Определившись с выбором, Вы можете купить швейную машину и любую другую технику в нашем стационарном магазине <a href="https://www.textiletorg.ru/kontakty/">по адресу</a> или заказать с доставкой к Вам на дом.</p>
                </div>

                <div data-target="production">
                    Как выбрать подходящий товар по выгодной цене?
                </div>
                <div id="production">
                    <p>За прошедшее время машинка претерпела качественные изменения, увеличился её функционал, она стала электронной и даже компьютеризированной. Более того, к швейным машинкам добавились простые и умелые <a href="https://www.textiletorg.ru/vyazalnye-mashiny/">вязальные машины</a><b>, </b>значительно облегчившие создание свитеров и шарфов. Но производители не останавливаются на достигнутом — они изо дня в день придумывают новые модификации и расширяют функционал созданных моделей такой нужной и полезной для дома техники.</p>
                    <p>Часто случается так, что Вы заходите в магазин, смотрите на витрины, и глаза «разбегаются». Как сделать правильный выбор, если большинство моделей схожи внешне, а их цены разнятся? Если одна машинка стоит в несколько раз дешевле другой? Диапазон цен на парогенераторы, отпариватели, утюги и пылесосы тоже широк. </p>
                    <p>Весь секрет в том, что цена на технику зависит от нескольких факторов:</p>
                    <p>· набора функций, которыми обладает устройство;</p>
                    <p>· технических параметров (количества выполняемых операций);</p>
                    <p>· типа швейной или вязальной машины (электромеханическая, электронная, компьютеризированная);</p>
                    <p>· страны-производителя.</p>
                    <p>А выбор самого покупателя, как правило, зависит от его личных пожеланий и финансовых возможностей. Только сопоставив два этих важных аргумента, можно сделать оптимально правильный выбор.</p>
                </div>

                <div data-target="sewingmachines">
                    Швейные машинки: какими бывают и как различаются?
                </div>
                <div id="sewingmachines">
                    <p><b><i>Швейные машинки с электроприводом (электрические)</i></b></p>
                    <p>Это самый распространенный вариант. В такой технике вращение махового колеса обеспечивает электромотор. Как правило, в комплект входит и ножная педаль. Нажимая на неё, Вы приводите в движение мотор и маховое колесо. Силой нажатия не педаль регулируется и скорость шитья. Машины без электропривода относятся к механическим (ручным или ножным) и сейчас встречаются крайне редко потому как давно уже не производятся.</p>
                    <p><b><i>Электромеханические машины</i></b></p>
                    <p>В таких моделях управление осуществляется с помощью механических переключателей, которые располагаются на передней панели прибора. Вы вручную производите установку натяжения нити, типа строчки, длину и ширину стежка.</p>
                    <p><b><i>Электронные машины</i></b></p>
                    <p>Модели оборудованы электронными панелями, через которые и происходит управление машиной. Встроенный микропроцессор контролирует перемещение иглы относительно ткани. Есть автоматическая настройка некоторых параметров шитья. У таких машин нет ограничения по сложности шитья — они способны выполнять большое количество строчек.</p>
                    <p><b><i>Компьютеризированные машины</i></b></p>
                    <p>Это вершина эволюции швейных машин. Они на порядок проще в обращении и управлении и не имеют ограничений по всем направлениям. Легко справляются с различными видами строчек, полностью отвечают за управление и выполняют огромный список операций. Все, что требуется от Вас в работе с такой моделью — менять шпульки и наслаждаться её безупречной работой.</p>
                </div>
            </div>

        </div>


        <div class = "ay-main-container" >

            <div class = "n_index_clients_head" >
                <div class = "n_index_clients_head_text" > НАШИ КЛИЕНТЫ </div>
            </div>

            <div class = "n_index_clients_blocks">
                <div class = "n_index_clients_block">
                   <table>
                       <tr>
                           <td> <div class = "n_index_clients_img"></div></td>
                       </tr>
                       <tr>
                           <td> НАЗВАНИЕ ФИРМЫ </td>
                       </tr>
                   </table>
                </div>
                <div class = "n_index_clients_block">
                    <table>
                        <tr>
                            <td> <div class = "n_index_clients_img"></div></td>
                        </tr>
                        <tr>
                            <td> НАЗВАНИЕ ФИРМЫ </td>
                        </tr>
                    </table>
                </div>
                <div class = "n_index_clients_block">
                    <table>
                        <tr>
                            <td> <div class = "n_index_clients_img"></div></td>
                        </tr>
                        <tr>
                            <td> НАЗВАНИЕ ФИРМЫ </td>
                        </tr>
                    </table>
                </div>
                <div class = "n_index_clients_block">
                    <table>
                        <tr>
                            <td> <div class = "n_index_clients_img"></div></td>
                        </tr>
                        <tr>
                            <td> НАЗВАНИЕ ФИРМЫ </td>
                        </tr>
                    </table>
                </div>
                <div class = "n_index_clients_block">
                    <table>
                        <tr>
                            <td> <div class = "n_index_clients_img"></div></td>
                        </tr>
                        <tr>
                            <td> НАЗВАНИЕ ФИРМЫ </td>
                        </tr>
                    </table>
                </div>
                <div class = "n_index_clients_block">
                    <table>
                        <tr>
                            <td> <div class = "n_index_clients_img"></div></td>
                        </tr>
                        <tr>
                            <td> НАЗВАНИЕ ФИРМЫ </td>
                        </tr>
                    </table>
                </div>
            </div>


        </div>

    </div>
    </div>

</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
