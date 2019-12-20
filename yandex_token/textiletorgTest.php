<?php
/*
	Скрипт для получения токена разработчика для испольлзования api яндекса директа.
	Алгоритм получения:
	заходим на скрипт, он переадресует на яндекс(необходимо быть залогиненым в нужном аккаунте),
	подтверждаем права доступа.
	Яндекс переадресует обратно на наш скрипт с кодом, его скрипт оправляет яндексу и получает токен.
	ID: 4fc6416559d24904b447d55ff3ed58e2
	Пароль: a5d38e23756d442ab70890c4ecec5a33
	Callback URL: http://adm.textiletorg.by/yandex_token/textiletorgPomodel.php

    token - AQAAAAAgOqQTAASIYAA9CWcAI0k-jxtvWZ0SZVU
*/
if(empty($_GET['code'])){
    $redirect = 'https://oauth.yandex.ru/authorize?response_type=code&client_id=4fc6416559d24904b447d55ff3ed58e2';
    header("Location: ".$redirect);
    exit();
}
$src = array(
    'grant_type' => 'authorization_code',
    'code' => $_GET['code'],
    'client_id' => '4db1aa6355574d5498fcd6d1c1f1ac85',
    'client_secret' => 'dacafc0d31434a9aadf22a48bbc1b7da'
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