<?
/*
Принимает url (urlrewrite.php)

/otzivу
/otziv
/otzive.php?page=***
/otziv.php
*/

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/custom/region.prototype/ipgeobase.php");

CModule::IncludeModule("blog");

$page = preg_replace('/([^a-z\.\/_\d]+)/i', '', $_GET['page']);

if (empty($page))
{
    $uIP = CBlogUser::GetUserIP();
    $arResult["USER_IP"] = is_array($uIP)? $uIP[0]: $uIP;

    $obGeoBase = new IPGeoBase();
    $arGeoBase = $obGeoBase->getRecord($arResult["USER_IP"]);

    $cityName = ($arGeoBase["city"]) ? $arGeoBase["city"] : "Москва";

    switch($cityName)
    {
        case 'Санкт-Петербург':
            $page = 'otzivspb';
            $loc = 'http://market.yandex.ru/shop/182435/reviews/add?retpath=http%3A%2F%2Fmarket.yandex.ru%2Fshop%2F182435%2Freviews';
        break;
        case 'Москва':
            $page = 'otzivmsk';
            $loc = 'http://market.yandex.ru/shop/23954/reviews/add?retpath=http%3A%2F%2Fmarket.yandex.ru%2Fshop%2F23954%2Freviews';
        break;
        case 'Екатеринбург':
            $page = 'otzivekb';
            $loc = 'http://market.yandex.ru/shop/245834/reviews/add?retpath=http%3A%2F%2Fmarket.yandex.ru%2Fshop%2F245834%2Freviews';
        break;
        case 'Нижний Новгород':
            $page = 'otzivnnov';
            $loc = 'http://market.yandex.ru/shop/283837/reviews/add?retpath=http%3A%2F%2Fmarket.yandex.ru%2Fshop%2F283837%2Freviews';
        break;
        case 'Ростов-на-Дону':
            $page = 'otzivrnd';
            $loc = 'http://market.yandex.ru/shop/320798/reviews/add?retpath=http%3A%2F%2Fmarket.yandex.ru%2Fshop%2F320798%2Freviews';
        break;
        default:
            $page = 'otzivmsk';
            $loc = 'http://market.yandex.ru/shop/23954/reviews/add?retpath=http%3A%2F%2Fmarket.yandex.ru%2Fshop%2F23954%2Freviews';
    }
}
else
{
    switch($page)
    {
        case '/otzivm.php':
        case '/otzivm_mail':
            $loc = 'http://market.yandex.ru/shop/23954/reviews/add?retpath=http%3A%2F%2Fmarket.yandex.ru%2Fshop%2F23954%2Freviews';
        break; // Москва из смс

        case '/otzivp.php':
        case '/otzivp_mail':
            $loc = 'http://market.yandex.ru/shop/182435/reviews/add?retpath=http%3A%2F%2Fmarket.yandex.ru%2Fshop%2F182435%2Freviews';
        break; // Питер из смс

        case '/otzive.php':
        case '/otzive_mail':
            $loc = 'http://market.yandex.ru/shop/245834/reviews/add?retpath=http%3A%2F%2Fmarket.yandex.ru%2Fshop%2F245834%2Freviews%2F';
        break; // Екатеринбург из смс

        case '/otzivn.php':
        case '/otzivn_mail':
            $loc = 'http://market.yandex.ru/shop/283837/reviews/add?retpath=http%3A%2F%2Fmarket.yandex.ru%2Fshop%2F283837%2Freviews%2F';
        break; // Нижний Новгород из смс

        case '/otzivrnd.php':
        case '/otzivrnd_mail':
            $loc = 'http://market.yandex.ru/shop/320798/reviews/add?retpath=http%3A%2F%2Fmarket.yandex.ru%2Fshop%2F320798%2Freviews%2F';
        break; // Rostov из смс

        default:
            $loc = 'http://market.yandex.ru/shop/23954/reviews/add?retpath=http%3A%2F%2Fmarket.yandex.ru%2Fshop%2F23954%2Freviews';
            $page = '/otzivm.php';
    }
}

LocalRedirect($loc, true, "301 Moved permanently");
?>