<?php
/**
 * Created by PhpStorm.
 * User: Komp
 * Date: 29.06.2017
 * Time: 0:55
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

if (!$USER->IsAdmin()) {
    LocalRedirect('/');
}

\Bitrix\Main\Loader::includeModule('sale');
\Bitrix\Main\Loader::includeModule('iblock');


$res = \Bitrix\Sale\Location\LocationTable::getList(array(
    'filter' => array('=NAME.LANGUAGE_ID' => 'RU', '=TYPE.CODE' => 'CITY', '!CITY_ID' => false),
    'select' => array('*', 'NAME_RU' => 'NAME.NAME', 'TYPE_CODE' => 'TYPE.CODE')
));
$el = new CIBlockElement;
while ($city = $res->fetch()) {

        $PROP = array();
        $PROP[522] = $city['NAME_RU'];  // город
        $PROP[523] = CUtil::translit($city['NAME_RU'], 'ru',array(
            "replace_space" => '',
            "replace_other" => '',

        ));       // символьный домен

        $arLoadProductArray = Array(
            "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
            "IBLOCK_ID"      => 43,
            "PROPERTY_VALUES"=> $PROP,
            "NAME"           => $city['NAME_RU'],
            "ACTIVE"         => "Y",
        );

        if ($_REQUEST['go'] == 'go') {
            if (!$PRODUCT_ID = $el->Add($arLoadProductArray))
                echo "Error: " . $el->LAST_ERROR . '<br>';
        } else {
            p($arLoadProductArray);
        }
}

