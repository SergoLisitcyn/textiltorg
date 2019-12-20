<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if($_GET['authkey'] != 'dj19273gj9438fj1290d239ckmls38_AS2d23vn374') {header("HTTP/1.1 404 Not Found"); exit();}

$date = date('Y-m-d');

$region = '';
$result_ar = array();

switch ($_GET['ctx']) {
    case "msk":
        $region = 'msk';
        break;
    case "spb":
        $region = 'spb';
        break;
    case "ekb":
        $region = 'ekb';
        break;
    case "n_nov":
        $region = 'n_nov';
        break;
    case "rnd":
        $region = 'rnd';
        break;
    default:
        header("HTTP/1.1 404 Not Found"); exit();
        break;
}

global $DB;
$strSql = 'SELECT * FROM ayers_yandex_prices WHERE DATE="'.$date.'" AND REGION="'.$region.'" ORDER BY ID';
$res = $DB->Query($strSql, false, $err_mess.__LINE__);

while($row = $res->fetch()){
    $result_ar[$row['PRODUCT_ID']] = array(
        'name' => $row['NAME'],
        'id' => $row['PRODUCT_ID'],
        'txt_price' => $row['PRICE'],
        'ya_offers' => $row['YA_OFFERS']
    );
}
header('Content-type: application/json');
echo json_encode($result_ar);
