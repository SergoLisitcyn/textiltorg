<?php
/*
 *
 * Документация
 * https://tech.yandex.ru/direct/doc/ref-v5/bids/setAuto-docpage/
 * https://tech.yandex.ru/direct/doc/ref-v5/campaigns/get-docpage/
 * */
require_once dirname(dirname(__FILE__)) . "/yandex5v.class.php";
require_once "dumplog.php";

// yandex direct account data
$account_data = array();
$account_data['login'] = 'textiletorg-test007';
//$account_data['token'] = 'AQAAAAAyt3SXAAWfHPLoLOli3ESlvDbn5zN6jTQ';

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

dumpLog($offset, '1', 'textiletorg-test007.log');

// Номер запуска по смешению
$markOffset = array(
    0 => 1
);

// Крайние смешение
$endOffset = 95;

$arrBidsParam = array();
$arrBidsData = array();

// Массив для Сampaigns запроса
$campaigns_param = array(
    'SelectionCriteria' => array('States' => array('ON')), // Кампания активна, объявления могут быть показаны.
    'FieldNames' => array('Id', 'Name'),
    //'Page' => array('Limit' => $limit, 'Offset' => $offset)
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

    // Создаем необходимый массив для запроса
    foreach ($campaigns["result"]["Campaigns"] as $num => $dat) {

        // Filter
        $arNameWords = explode(" ", $dat["Name"]);
        if (!in_array("РСЯ", $arNameWords) && !in_array("МКБ", $arNameWords)) {
			
			if (in_array("Д", $arNameWords)) {
				$arrBidsParam["auto"][] = array(
                    'Scope' => array('SEARCH'),
                    'CampaignId' => $dat["Id"],
                    "IncreasePercent" => 15, // (это проценты)
                    "CalculateBy" => "VALUE",
                    "Position" => "P11",
                    "MaxBid" => 330000000 // (это 330 рублей)
                );

                $arrBidsData[$dat["Id"]] = array(
                    "name" =>  $dat["Name"],
                    "type" =>  'auto, P11, 330 рублей',
                );
			}
	
			if (in_array("Д", $arNameWords) && in_array("Непрямые", $arNameWords)) {
				 $arrBidsParam["auto"][] = array(
                    'Scope' => array('SEARCH'),
                    'CampaignId' => $dat["Id"],
                    "IncreasePercent" => 30, // (это проценты)
                    "CalculateBy" => "VALUE",
                    "Position" => "P13",
                    "MaxBid" => 330000000 // (это 330 рублей)
                );

                $arrBidsData[$dat["Id"]] = array(
                    "name" =>  $dat["Name"],
                    "type" =>  'auto, P11, 330 рублей',
                );
			}
       
      
			if (in_array("НД", $arNameWords)) {
				  $arrBidsParam["auto"][] = array(
                    'Scope' => array('SEARCH'),
                    'CampaignId' => $dat["Id"],
                    "IncreasePercent" => 30, // (это проценты)
                    "CalculateBy" => "VALUE",
                    "Position" => "P13",
                    "MaxBid" => 330000000 // (это 330 рублей)
                );

                $arrBidsData[$dat["Id"]] = array(
                    "name" =>  $dat["Name"],
                    "type" =>  'auto, P11, 330 рублей',
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

} else {
    $message = 'Не удалось получить список компаний!';
    error_log($message . "\n", 3, dirname(__FILE__) . "/log/" . $markOffset[$offset] . "/" . date("d.m.Y") . ".log");
    echo $message . "\n";
}
