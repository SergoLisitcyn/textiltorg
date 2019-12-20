<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
$isXHR = strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
header('Access-Control-Allow-Origin: *');
header('Content-type: application/' . ($isXHR ? 'json' : 'x-javascript'));

use \Bitrix\Main\Loader;

require_once(realpath(dirname(__FILE__))."/class.php");
$city = htmlspecialchars($_POST["city"]);
if (Loader::includeModule('ayers.stores')) {
    $arParams = array(
        'city' => (isset($GLOBALS['GEO_REGION_CITY_NAME'])) ? $GLOBALS['GEO_REGION_CITY_NAME'] : $city,
        'is_filter_type' => 'Y',
        'count_in_hint' => 700,
        'cache_time' => '36000',
        'cache_type' => 'A',
    );
    $o = new StoresProduct();
    $result = $o->getStoresData($arParams);
    echo $result['MAP_GROUP'];
}
