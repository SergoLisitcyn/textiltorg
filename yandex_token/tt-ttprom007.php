<?php
// token - AQAAAAAyt2K-AAWfHGt7JjHFJkkllDFGaXIWLwQ
if(empty($_GET['code'])){
    $redirect = 'https://oauth.yandex.ru/authorize?response_type=code&client_id=5a73c0e70eae4859ba4b98dc4f537374'; // Приложение от textiletorgshop007
    header("Location: ".$redirect);
    exit();
}
$src = array(
    'grant_type' => 'authorization_code',
    'code' => $_GET['code'],
    'client_id' => 'b661875023444a58a66cd70e941bbf29',
    'client_secret' => '0f292e79c8ac47c9a03f1b7caa690e71'
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
