<?php
/*
	Скрипт для получения токена разработчика для испольлзования api яндекса директа.
	Алгоритм получения:
	заходим на скрипт, он переадресует на яндекс(необходимо быть залогиненым в нужном аккаунте),
	подтверждаем права доступа.
	Яндекс переадресует обратно на наш скрипт с кодом, его скрипт оправляет яндексу и получает токен.
	ID: 06034f18df9749738bcec0a77267b22d
	Пароль: 98ddf92f42e7469a826cd4d0c7efad69
	Callback URL: http://adm.textiletorg.ru/yandex_direct_new/get_token/get_token.php

    token - AQAAAAAg8tyAAARwg0uzhDIf7UV7um0FAKF7n0w

    Приложение от ttprom007 id - e6204b4c55a349b2948dbc53c7b8bd12
    Токен tt.ttprom - AQAAAAAg8tyAAARwg0uzhDIf7UV7um0FAKF7n0w
*/
if(empty($_GET['code'])){
    $redirect = 'https://oauth.yandex.ru/authorize?response_type=code&client_id=e6204b4c55a349b2948dbc53c7b8bd12';
    header("Location: ".$redirect);
    exit();
}
$src = array(
    'grant_type' => 'authorization_code',
    'code' => $_GET['code'],
    'client_id' => 'bd383a0ca0fe41fcb39db14cf9d07f23',
    'client_secret' => 'b51761ba2a624f578a9897c77618ad68'
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
