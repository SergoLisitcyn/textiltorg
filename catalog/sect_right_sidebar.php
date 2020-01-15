<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// sort filter status + sku
CModule::includeModule('catalog');

$arrFilter = array();
$arSubQueryUp = array("IBLOCK_ID" => 25, ">CATALOG_QUANTITY" => 0);

if ($_GET["status"] == "in-stock")
    $arrFilter[] = array(
        "LOGIC" => "OR",
        array("ID" => CIBlockElement::SubQuery('PROPERTY_CML2_LINK', $arSubQueryUp)),
        array(">CATALOG_QUANTITY" => 0)
    );

if ($_GET["status"] == "to-order")
    $arrFilter[] = array(
        "LOGIC" => "AND",
        array("!ID" => CIBlockElement::SubQuery('PROPERTY_CML2_LINK', $arSubQueryUp)),
        array("CATALOG_QUANTITY" => 0)
    );

$GLOBALS["arrFilterCatalog"] = $arrFilter;

// ru
$GLOBALS["arrFilterCatalog"][] = array(
    "PROPERTY_VIEW_SITE_RU_VALUE" => "Да"
);
?>
<? if($_REQUEST["SECTION_CODE"] == 'aksessuary-dlya-vyshivaniya' || $_REQUEST["SECTION_CODE"] == 'podarochnye-karty' || $_REQUEST["SECTION_CODE"] == 'aksessuary-dlya-shitya' || $_REQUEST["SECTION_CODE"] == 'aksessuary-dlya-vyazaniya' || $_REQUEST["SECTION_CODE"] == 'aksessuary-dlya-glazheniya' ){

} else {?>
<div class="box">
    <div class="filter_both_window"></div>
    <?
        //if (PAGE_FOLDER != "/catalog/"):
         if (1 == 1):
    ?>
        <div class="box other filter">
            <? $curPage = $APPLICATION->GetCurPage(false);
            //Получаем из урла имя раздела и его родителя

            CModule::IncludeModule("iblock");
            $curPage = $APPLICATION->GetCurPage(false);

            $arStr = str_split(strval($curPage), 1);


            $first = '';
            $sec = '';


            for ($i = 0, $count = 0, $v = (count($arStr)); $i <= (count($arStr) - 1); $i++) {
                $v = $v - 1;
                if ($arStr[$v] == '/') {
                    $count = $count + 1;
                }

                if ($count == 2) {


                    for ($c = $v, $count = 0; $c < count($arStr); $c++) {
                        $sec[$count] = $arStr[$c];
                        $count++;
                    }
                    break;
                }
            }

            for ($i = 0, $count = 0, $v = (count($arStr)); $i <= (count($arStr) - 1); $i++) {
                $v = $v - 1;
                if ($arStr[$v] == '/') {
                    $count = $count + 1;
                }

                if ($count == 2) {


                    for ($c = 0; $c <= $v; $c++) {
                        $first[$c] = $arStr[$c];
                    }
                    break;
                }
            }
            for ($i = 0, $count = 0, $check = 0, $v = (count($arStr)); $i <= (count($arStr) - 1); $i++) {
                $v = $v - 1;
                if ($arStr[$v] == '/') {
                    $count = $count + 1;
                }
                if ($count == 2) {
                    if ($check != 1) {
                        $countsec = $v;
                        $check = 1;


                    }
                }
                if ($count == 3) {


                    for ($c = $v, $count = 0; $c <= $countsec; $c++) {

                        $firstSec[$count] = $arStr[$c];

                        $count++;
                    }

                    break;
                }
            }


            $secName = $sec;
            $secName[0] = '';
            $secName[count($secName) - 1] = '';

            $firstSec[0] = '';
            $firstSec[count($firstSec) - 1] = '';

            $firstSec = implode($firstSec);
            $first = implode($first);//родитель
            $sec = implode($sec);//раздел
            $secName = implode($secName);


            $SEC_ID = array();
            $res = CIBlockSection::GetList(
                Array("SORT" => "ASC"),
                array("IBLOCK_ID" => 8, "CODE" => $firstSec, 'GLOBAL_ACTIVE' => 'Y')
            );

            while ($ar_res = $res->GetNext()) {

                $SEC_ID[] = $ar_res['ID'];
            }

            //Получаем айди раздела
            $res = CIBlockSection::GetList(
                Array("SORT" => "ASC"),
                array("SECTION_ID" => $SEC_ID, "CODE" => $secName, 'GLOBAL_ACTIVE' => 'Y')
            );

            while ($ar_res = $res->GetNext()) {

                $IDS[] = $ar_res['ID'];
            }


            //проверяем, есть ли у него дочерние элементы
            $intCount = 0;
            $intCount = CIBlockSection::GetCount(array('IBLOCK_ID' => 8, 'SECTION_ID' => $IDS));

            $listURL = array(
                0 => 'aksessuary-dlya-vyshivaniya',
                1 => 'aksessuary-dlya-shitya',
                2 => 'aksessuary-dlya-vyazaniya',
                3 => 'aksessuary-dlya-glazheniya',
                4 => 'aksessuary-dlya-uborki'
            );
            $checkURL = 0;
            for ($i = 0; $i <= 4; $i++) {
                $pos = strripos($curPage, $listURL[$i]);
                if ($pos !== false) {

                    $checkURL = 1;
                }
            }
            ?>
            <? //Если нет, то




            if ($intCount == 0 and $checkURL != 1):?>

                <?
                $GlOBALS["var"] = strval($SEC_ID[0]);
                $_REQUEST["SECTION_CODE_PATH"] = $first;
                $_REQUEST["SECTION_CODE"] = $firstSec;
                //переменную val используем как айди раздела для фильтра
                ?>

                <? $APPLICATION->IncludeComponent(
                    "bitrix:catalog.smart.filter",
                    "catalog",
                    array(
                        "FIL_SEC_FIRST" => $first,
                        "FIL_SEC_SECOND" => $sec,
                        "SEC_CHECK" => "Y",
                        "SEC_ID" => $GlOBALS["var"],
                        "CACHE_GROUPS" => "Y",
                        "CACHE_TIME" => "36000000",
                        "CACHE_TYPE" => "A",
                        "CONVERT_CURRENCY" => "N",
                        "DISPLAY_ELEMENT_COUNT" => "Y",
                        "FILTER_NAME" => "arrFilterCatalog",
                        "FILTER_VIEW_MODE" => "vertical",
                        "HIDE_NOT_AVAILABLE" => "N",
                        "IBLOCK_ID" => "8",
                        "IBLOCK_TYPE" => "catalog",
                        "INSTANT_RELOAD" => "N",
                        "PAGER_PARAMS_NAME" => "arrPager",
                        "POPUP_POSITION" => "left",
                        "PRICE_CODE" => $GLOBALS["CITY_FILTER_PRICE_CODE"],
                        "SAVE_IN_SESSION" => "N",
                        "SECTION_CODE" => "",
                        "SECTION_CODE_PATH" => "",
                        "SECTION_DESCRIPTION" => "-",
                        "SECTION_ID" => $GlOBALS["var"],
                        "SECTION_TITLE" => "-",
                        "SEF_MODE" => "Y",
                        "SEF_RULE" => "",
                        "SMART_FILTER_PATH" => $_REQUEST["SMART_FILTER_PATH"],
                        "TEMPLATE_THEME" => "red",
                        "XML_EXPORT" => "N",
                        "COMPONENT_TEMPLATE" => "catalog",
                        "HELP_SECTION_ID" => $GlOBALS["var"]
                    ),
                    false,
                    array(
                        "HIDE_ICONS" => "Y"
                    )
                ); ?>
            <? else:?>

                <?

                $APPLICATION->IncludeComponent(
                    "bitrix:catalog.smart.filter",
                    "catalog",
                    array(

                        "CACHE_GROUPS" => "Y",
                        "CACHE_TIME" => "36000000",
                        "CACHE_TYPE" => "A",
                        "CONVERT_CURRENCY" => "N",
                        "DISPLAY_ELEMENT_COUNT" => "N",
                        "FILTER_NAME" => "arrFilterCatalog",
                        "FILTER_VIEW_MODE" => "vertical",
                        "HIDE_NOT_AVAILABLE" => "N",
                        "IBLOCK_ID" => "8",
                        "IBLOCK_TYPE" => "catalog",
                        "INSTANT_RELOAD" => "N",
                        "PAGER_PARAMS_NAME" => "arrPager",
                        "POPUP_POSITION" => "left",
                        "PRICE_CODE" => $GLOBALS["CITY_FILTER_PRICE_CODE"],
                        "SAVE_IN_SESSION" => "N",
                        "SECTION_CODE" => $_REQUEST["SECTION_CODE"],
                        "SECTION_CODE_PATH" => "",
                        "SECTION_DESCRIPTION" => "-",
                        "SECTION_TITLE" => "-",
                        "SEF_MODE" => "Y",
                        "SEF_RULE" => "",
                        "SMART_FILTER_PATH" => $_REQUEST["SMART_FILTER_PATH"],
                        "TEMPLATE_THEME" => "red",
                        "XML_EXPORT" => "N",
                        "COMPONENT_TEMPLATE" => "catalog",
                        "HELP_SECTION_ID" => $_REQUEST["SECTION_CODE"],
                    ),
                    false,
                    array(
                        "HIDE_ICONS" => "Y"
                    )
                ); ?>
            <? endif; ?>


        </div>
    <? endif; ?>
</div>
<? } ?>
