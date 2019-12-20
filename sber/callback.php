<?php
/*
 * https://web.rbsdev.com/dokuwiki/doku.php/integration:api:callback:start
 *
 * [07/Feb/2018:08:49:06 +0300] "GET /sber/callback.php?checksum=1238F85DF6A28D0A5D41B7937717FD09E1A4005A7A5A67F1E08D64040E23889D&orderNumber=50967&mdOrder=ceea9fd0-e2bb-72b6-ceea-9fd00014d255&operation=deposited&status=0 HTTP/1.0" 200 - "-" "Apache-HttpClient/4.5.1 (Java/1.8.0_151)"
 * */

// Наш токен
$key = 'rdte9jhiau3b2uj5nevmcl25v0';

// Запоминаем ключ для дальнейшей проверки
$checksum = $_GET["checksum"];

// Собираем ответ в массив
$arSberData = array(
    "orderNumber" => $_GET["orderNumber"],
    "mdOrder" => $_GET["mdOrder"],
    "operation" => $_GET["operation"],
    "status" => $_GET["status"],
    "amount" => $_GET["amount"],
);

// Сортируем массив по ключу в алфавитном порядке
ksort($arSberData);

// Формируем строку из массива, (amount;123456;mdOrder;3ff6962a-7dcc-4283-ab50-a6d7dd3386fe;operation;deposited;orderNumber;10747;status;1;)
$res = array_map(function($k, $v) { return "$k;$v"; }, array_keys($arSberData), $arSberData);
$data = implode(";",$res) . ';';

// Вычисляем ключ из полученых данных
$hmac = hash_hmac ( 'sha256' , $data , $key);
$myChecksum = strtoupper($hmac);

// Тестовое логирование
$arSberData["checksum"] = $checksum;
$arSberData["hmac"] = $myChecksum;
$arSberData["str"] = $data;
error_log( print_r($arSberData, TRUE), 3, dirname(__FILE__) . '/logs/' . date('d-m-Y') . ".log");

// Если равны, даем знать
if ($checksum == $myChecksum) {
    header('HTTP/1.1 200 OK');

    // deposited - операция завершения, 1 - операция прошла успешно;
    if ($arSberData["operation"] == 'deposited' && $arSberData["status"] == 1) {
        $arSberData["orderNumber"];
        $arSberData["amount"];
    }
}
?>