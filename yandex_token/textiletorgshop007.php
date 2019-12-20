<?php

$src = array(
    'grant_type' => 'authorization_code',
    'code' => '5863340',
    'client_id' => '5a73c0e70eae4859ba4b98dc4f537374',
    'client_secret' => '17f5c8a54930431da8e3fedd71b76119'
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
