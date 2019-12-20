<?php
// token - AQAAAAAyt3SXAAWfHPLoLOli3ESlvDbn5zN6jTQ
if(empty($_GET['code'])){
    $redirect = 'https://oauth.yandex.ru/authorize?response_type=code&client_id=5a73c0e70eae4859ba4b98dc4f537374'; // Приложение от textiletorgshop007
    header("Location: ".$redirect);
    exit();
}
$src = array(
    'grant_type' => 'authorization_code',
    'code' => $_GET['code'],
    'client_id' => 'a047371413f545839e68eaaa2ae0bd8c',
    'client_secret' => 'a26f12d5d040482d98f9b22a4c0d8957'
);
$query = http_build_query($src);
$res = PostConnect($query, "https://oauth.yandex.ru/token");

echo "<pre>";
print_r($res);
function PostConnect($src, $href) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CRLF, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $src);
    curl_setopt($ch, CURLOPT_URL, $href);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    return $result;
    curl_close($ch);
}
