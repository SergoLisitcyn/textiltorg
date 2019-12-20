<?php
if(empty($_GET['code'])){
    $redirect = 'https://oauth.yandex.ru/authorize?response_type=code&client_id=e6204b4c55a349b2948dbc53c7b8bd12';
    header("Location: ".$redirect);
    exit();
}
$src = array(
    'grant_type' => 'authorization_code',
    'code' => $_GET['code'],
    'client_id' => 'e6204b4c55a349b2948dbc53c7b8bd12',
    'client_secret' => '8f52bb3006f5441aae55db73fff0769e'
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
