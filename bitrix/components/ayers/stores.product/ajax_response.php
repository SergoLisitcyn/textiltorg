<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
$isXHR = strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$callback = isset($_GET['callback']) ? preg_replace('/[^a-z0-9$_]/si', '', $_GET['callback']) : false;
header('Access-Control-Allow-Origin: *');
header('Content-type: application/' . ($isXHR ? 'json' : 'x-javascript'));

use \Bitrix\Main\Loader;

require_once(realpath(dirname(__FILE__))."/class.php");
if (Loader::includeModule('ayers.stores')) {
    $arParams = array(
        'city' => $_REQUEST['city'],
        'is_filter_type' => $_REQUEST['is_filter_type'],
        'count_in_hint' => $_REQUEST['count_in_hint'],
        'cache_time' => $_REQUEST['cache_time'],
        'cache_type' => $_REQUEST['cache_type'],
    );
    $o = new StoresProduct();
    $data = $o->getStoresData($arParams);
    $result = ($callback ? $callback . '(' : '') . json_encode($data, true) . ($callback ? ')' : '');
    echo $result;
}
