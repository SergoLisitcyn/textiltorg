<?php
/*
 * Скрипт запускается 5 раз каждые 15 мин., при запуске передается смещение.
 *
 * На 20.12.2016 количество активных компаний 473.
 * На обновление ставок у 473-х компаний, уходит 1 ч. 10 мин. (суммарно 70 мин.).
 * Для запуска каждые 15 мин. высчитываем количество запусков скрипта и
 * количество компаний обновление ставок которых укладывается в 15 мин.
 *
 * 70 мин. / 15 мин. = 5 запусков
 * 473 комп. / 5 зап. = ~95 компаний
 *
 * В итоге, одновременно, каждые 15 минут запускается 5 одинаковых скриптов, разница только в смещение.
 *
 * Пример:
 *
 * Порядковый номер запуска, лимит, смещение.
 * 1, 95, 0
 * 2, 95, 95
 * 3, 95, 190
 * 4, 95, 285
 * 5, 95, 380
 * ...
 *
 * Замер времени выполнения - http://joxi.ru/KAx7VQNHgqvn28
 * Зеленым - Старт
 * Красным - Завершение
 * Черным - Старт очередного запуска
 *
 * С помощью такой схемы можно распределить потоки - если одним запросом обновить ставки всех кампаний,
 * то они ставятся в единую очередь. Но, если же обновлять ставки для групп кампаний независимо,
 * то шанс получить обновление быстрее увеличивается.
 *
 * Стратегия
 * Показы идут согласно настройки кампании, в которой выбрана стратегия показов 3е место спецразмещения,
 * однако ставки для ставок за исходную берем 1ую позицию спецразмещения и к ней прибавляем небольшой процент.
 * Если брать для ставок за основу 3 место - придется прибавлять большой процент.
 *
 * Документация
 * https://tech.yandex.ru/direct/doc/ref-v5/bids/setAuto-docpage/
 * https://tech.yandex.ru/direct/doc/ref-v5/campaigns/get-docpage/
 * */
/*
Запрос:
Программа запускается автоматически, каждые 15 минут, вызывается campaigns.get затем bids.setAuto.
Ответ каждого вызованного метода проверяется на наличие ошибок, если ошибки есть они логируются, а работа программы завершается.
Теоритически программа получает все активные компании, и обновляет для них ставки.
Переодичнасть в 15 минут обусловленна тем что программа успевает выполнится и результат на данный момент получается оптимальный.

Ответ:
Заявка одобрена. На данный момент Вы еще можете использовать сервис "Bids" для управления ставками,
при этом мы рекомендуем переходить на использование нового сервиса – "KeywordBids".
В будущем мы планируем отключить сервис "Bids", но каких-либо точных сроков сейчас назвать не можем.
Больше информации о сервисе "KeywordBids" Вы можете узнать на странице
https://tech.yandex.ru/direct/doc/ref-v5/keywordbids/keywordbids-docpage/

Дополнительно:
Одно приложение с одобренной заявкой можно использовать для работы с двумя различными аккаунтами.
Для того, чтобы начать пользоваться приложением в рамках аккаунта нужно получить OAuth-токен.
Токен получается на пару "логин пользователя" + "идентификатор приложения.
При использовании API Директа баллы списываются с аккаунта, владельца токена.
Так как у вас более одного аккаунта, то и токенов Вам нужно получать столько же, сколько аккаунтов Вы хотите подключить к приложению.
*/
//require_once dirname(dirname(__FILE__)) . "/yandex5v.class.php";
require_once "dumplog.php";

// yandex direct account data
$account_data = array();
$account_data['login'] = 'textiletorgshop007';
//$account_data['token'] = 'AQAAAAAyt2eSAAWfHBr33Omb70eYgGpTJT56H8M';
$account_data['token'] = 'AgAAAAAyt2eSAARwgxR5sCkVc0FurHlIWcML9Nw';


$account_data['locale'] = 'ru';
$account_data['campaigns'] = 'https://api.direct.yandex.com/json/v5/campaigns';
$account_data['bids'] = 'https://api.direct.yandex.com/json/v5/bids';

$limit = 95;
$offset = 0;
if(isset($argv[1])){
    $offset = $argv[1];
} else {
    exit();
}

dumpLog($offset, '1', 'textiletorgshop007.log');

// Номер запуска по смешению
$markOffset = array(
    0   => 1,
    95  => 2,
    190 => 3,
    285 => 4,
    380 => 5,
    475 => 6,
    570 => 7,
    665 => 8,
    760 => 9
);

// Крайние смешение
$endOffset = 760;

$arrBidsParam = array();
$arrBidsData = array();

// Массив для Сampaigns запроса
$campaigns_param = array(
    'SelectionCriteria' => array('States' => array('ON')), // Кампания активна, объявления могут быть показаны.
    'FieldNames' => array('Id', 'Name'),
    'Page' => array('Limit' => $limit, 'Offset' => $offset)
); // параметры запроса

// Функция возврощяет массив для Bids запроса
function bids_param($arrBidsParam)
{
    return array(
        'Bids' => $arrBidsParam
    ); // параметры запроса
}

// Класс для работы с яндексом
$yandex_campaigns = new ConnectYandex($account_data['login'], $account_data['token'], $account_data['locale'], $account_data['campaigns']);
$yandex_bids = new ConnectYandex($account_data['login'], $account_data['token'], $account_data['locale'], $account_data['bids']);

// Получение активных компаний
$campaignsInfo = $yandex_campaigns->connect('get', $campaigns_param);
$campaigns = $campaignsInfo['data'];

if (!isset($campaigns["error"]['error_code'])) {

    if (array_key_exists('LimitedBy', $campaigns['result']) && $campaigns['result']['LimitedBy'] > $endOffset) {
        $email = "i.bashko@textiletorg.ru";
        $message = $account_data['login'] . ' ТТ скрипт ставок. Количество активных компаний увеличилось, в выборке остались еще объекты, ' .
            'необходимо проверить и при необходимости добавить, дополнительный запуск скрипта с новым смещением.';
        error_log($message . " порядковый номер последнего возвращенного объекта - " . $campaigns['result']['LimitedBy'], 1, $email);
    }

    $uniqid = uniqid();

    //error_log(date("Y-m-d H:i:s") . " START " . " Запуск № " . $markOffset[$offset] . $uniqid, 1, "i.bashko@textiletorg.ru");
    error_log(date("Y-m-d H:i:s") . " START Запуск № " . $markOffset[$offset] . "\n", 3, dirname(__FILE__) . "/log/" . $markOffset[$offset] . "/" . date("d.m.Y") . ".log");

    // Создаем необходимый массив для запроса
    foreach ($campaigns["result"]["Campaigns"] as $num => $dat) {

        /*
         * 3 место + 25% , не более 330 руб
         * Исключение - РСЯ, эти компании полностью игнорируются
      
        $target = array(
            "Position" => "P13",
            "MaxBid" => 330000000,
            "MaxBidRub" => 330,
            "IncreasePercent" => 25
        );
        $rest = array(
            "Position" => "P13",
            "MaxBid" => 330000000,
            "MaxBidRub" => 330,
            "IncreasePercent" => 25
        );
	 */
		  	
        $arNameWords = explode(" ", $dat["Name"]);
	
		
        if (!in_array("РСЯ", $arNameWords) && !in_array("МКБ", $arNameWords)) {
         
		 	if (in_array("Д", $arNameWords)) {
				 $arrBidsParam["auto"][] = array(
                    'Scope' => array('SEARCH'),
                    'CampaignId' => $dat["Id"],
                    "IncreasePercent" => '15', // (это проценты)
                    "CalculateBy" => "VALUE",
                    "Position" => 'P11',
                    "MaxBid" => '330000000'
                );

                $arrBidsData[$dat["Id"]] = array(
                    "name" =>  $dat["Name"],
                    "type" =>  'auto '.$target["Position"].' + '.$target["IncreasePercent"].'%, '.$target["MaxBidRub"].' рублей',
                );
	
			}
	
			if (in_array("Д", $arNameWords) && in_array("Непрямые", $arNameWords)) {
				 $arrBidsParam["auto"][] = array(
                    'Scope' => array('SEARCH'),
                    'CampaignId' => $dat["Id"],
//                    "IncreasePercent" => '30', // (это проценты)
                    "IncreasePercent" => '15', // (это проценты)
                    "CalculateBy" => "VALUE",
//                    "Position" => 'P13',
                    "Position" => 'P11',
                    "MaxBid" => '330000000'
                );

                $arrBidsData[$dat["Id"]] = array(
                    "name" =>  $dat["Name"],
                    "type" =>  'auto '.$target["Position"].' + '.$target["IncreasePercent"].'%, '.$target["MaxBidRub"].' рублей',
                );
	
			}
       
      
			if (in_array("НД", $arNameWords)) {
				 $arrBidsParam["auto"][] = array(
                    'Scope' => array('SEARCH'),
                    'CampaignId' => $dat["Id"],
//                    "IncreasePercent" => '30', // (это проценты)
                    "IncreasePercent" => '15', // (это проценты)
                    "CalculateBy" => "VALUE",
//                    "Position" => 'P11',
                    "Position" => 'P13',
                    "MaxBid" => '330000000'
                );

                $arrBidsData[$dat["Id"]] = array(
                    "name" =>  $dat["Name"],
                    "type" =>  'auto '.$target["Position"].' + '.$target["IncreasePercent"].'%, '.$target["MaxBidRub"].' рублей',
                );
		
			}

		 
		 
        } 
		
    }

    foreach ($arrBidsParam as $type => $val) {

        // Разбиваем массив по 10, ограничение API Директа
        $arrBidsParamChank = array_chunk($val, 10);

        //error_log(date("Y-m-d H:i:s") . " Запуск № " . $markOffset[$offset] . "\n", 3, dirname(__FILE__) . "/log/" . $markOffset[$offset] . "/" . date("d.m.Y") . ".log");

        foreach ($arrBidsParamChank as $item => $value) {

            if ($type == 'fix') {
                $bidsInfo = $yandex_bids->connect('set', bids_param($arrBidsParamChank[$item]));
            } elseif ($type == 'auto') {
                $bidsInfo = $yandex_bids->connect('setAuto', bids_param($arrBidsParamChank[$item]));
            }

            $bids = $bidsInfo['data'];

            foreach ($value as $id => $data) {

                var_dump($data["CampaignId"] . ' - ' . $arrBidsData[$data["CampaignId"]]["name"] . ' - TYPE: ' . $arrBidsData[$data["CampaignId"]]["type"]);

                // Выводим результат в лог
                if (isset($bids["error"]['error_code'])) {
                    $log = 'TYPE: ' . $arrBidsData[$data["CampaignId"]]["type"] . ' ' . $arrBidsData[$data["CampaignId"]]["name"] . ' (' . $data["CampaignId"] . ') => Error: ' . $bids["error"]['error_string'] . ' ' . $bids["error"]['error_detail'] . "\n";
                    error_log($log . "\n", 3, dirname(__FILE__) . "/log/" . $markOffset[$offset] . "/" . date("d.m.Y") . ".log");
                } else {
                    $log = 'TYPE: ' . $arrBidsData[$data["CampaignId"]]["type"] . ' ' . $arrBidsData[$data["CampaignId"]]["name"] . ' (' . $data["CampaignId"] . ') => Ставки обновлены! ' . "\n";
                    error_log($log . "\n", 3, dirname(__FILE__) . "/log/" . $markOffset[$offset] . "/" . date("d.m.Y") . ".log");
                    if ($data["Errors"]) {
                        error_log(print_r($bidsInfo, TRUE) . "\n", 3, dirname(__FILE__) . "/log/" . $markOffset[$offset] . "/error-" . date("d.m.Y") . ".log");
                    }
                }
            }

        }

    }

    error_log(date("Y-m-d H:i:s") . " END Запуск № " . $markOffset[$offset] . "\n", 3, dirname(__FILE__) . "/log/" . $markOffset[$offset] . "/" . date("d.m.Y") . ".log");
    //error_log(date("Y-m-d H:i:s") . " END " . " Запуск № " . $markOffset[$offset] . $uniqid, 1, "i.bashko@textiletorg.ru");

} else {
    $message = 'Не удалось получить список компаний!';
    error_log($message . "\n", 3, dirname(__FILE__) . "/log/" . $markOffset[$offset] . "/" . date("d.m.Y") . ".log");
    echo $message . "\n";
}
