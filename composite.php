<?
require_once __DIR__ . '/bitrix/php_interface/include/lib/Mobile_Detect.php';
$mobileDetect = new Mobile_Detect;
if($mobileDetect->isMobile()){
    $_GET['ncc'] = 1;
    if ($_COOKIE['SHOW_VERSION'] != 'desctop' || $_GET['show_version'] != 'desctop') {
        define('IS_MOBILE', true);
    }
}

if( $_COOKIE['SHOW_VERSION'] == 'mobile') {
    $_GET['ncc'] = 1;
}
