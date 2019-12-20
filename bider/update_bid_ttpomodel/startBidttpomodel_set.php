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
require_once dirname(dirname(__FILE__)) . "/yandex5v.class.php";
require_once dirname(dirname(__FILE__)) . "/config5vPomodel.php";

$targetСampaigns = array(31900315,31908879,31908904,31920148,31920158,31926888);

$limit = 95;
$offset = 0;
if(isset($argv[1])){
    $offset = $argv[1];
} else {
    exit();
}

// Номер запуска по смешению
$markOffset = array(
    0   => 1,
    95  => 2
);

// Крайние смешение
$endOffset = 95;

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
        $message = '(TT UpdateBids tt.pomodel) ТТ скрипт ставок. Количество активных компаний увеличилось, в выборке остались еще объекты, ' .
            'необходимо проверить и при необходимости добавить, дополнительный запуск скрипта с новым смещением.';
        error_log($message . " порядковый номер последнего возвращенного объекта - " . $campaigns['result']['LimitedBy'], 1, $email);
    }

    $uniqid = uniqid();

    //error_log(date("Y-m-d H:i:s") . " START " . " Запуск № " . $markOffset[$offset] . $uniqid, 1, "i.bashko@textiletorg.ru");
    //var_dump(date("Y-m-d H:i:s") . " START " . " Запуск № " . $markOffset[$offset] . " " . $uniqid);

    // Создаем необходимый массив для запроса
    foreach ($campaigns["result"]["Campaigns"] as $num => $dat) {

        if (in_array($dat["Id"], $targetСampaigns)) {
            // Fixed 350 Rub.
            $arrBidsParam[] = array(
                'CampaignId' => $dat["Id"],
                "Bid" => 350000000 // (это 350 рублей)
            );
        }

        // Записываем названия компаний
        $arrBidsData[$dat["Id"]] = $dat["Name"];

    }

    // Разбиваем массив по 10, ограничение API Директа
    $arrBidsParamChank = array_chunk($arrBidsParam, 10);

    error_log(date("Y-m-d H:i:s") . " Запуск № " . $markOffset[$offset] . "\n", 3, dirname(__FILE__) . "/log/set/" . $markOffset[$offset] . "/" . date("d.m.Y") . ".log");
    error_log($campaignsInfo['info'][5], 3, dirname(__FILE__) . "/log/set/" . $markOffset[$offset] . "/" . date("d.m.Y") . ".log");

    foreach ($arrBidsParamChank as $item => $value) {

        $bidsInfo = $yandex_bids->connect('set', bids_param($arrBidsParamChank[$item]));
        $bids = $bidsInfo['data'];

        foreach ($value as $id => $data) {

            var_dump($data["CampaignId"] . ' - ' . $arrBidsData[$data["CampaignId"]]);

            // Выводим результат в лог
            if (isset($bids["error"]['error_code'])) {
                $log = $arrBidsData[$data["CampaignId"]] . ' (' . $data["CampaignId"] . ') => Error: ' . $bids["error"]['error_string'] . ' ' . $bids["error"]['error_detail'] . "\n";
                error_log($log . "\n", 3, dirname(__FILE__) . "/log/set/" . $markOffset[$offset] . "/" . date("d.m.Y") . ".log");
            } else {
                $log = $arrBidsData[$data["CampaignId"]] . ' (' . $data["CampaignId"] . ') => Ставки обновлены! ' . "\n";
                error_log($log . "\n", 3, dirname(__FILE__) . "/log/set/" . $markOffset[$offset] . "/" . date("d.m.Y") . ".log");
            }
        }

    }

    //error_log(date("Y-m-d H:i:s") . " END " . " Запуск № " . $markOffset[$offset] . $uniqid, 1, "i.bashko@textiletorg.ru");
    //var_dump(date("Y-m-d H:i:s") . " END " . " Запуск № " . $markOffset[$offset] . " " . $uniqid);
} else {
    $message = 'Не удалось получить список компаний!';
    error_log($message . "\n", 3, dirname(__FILE__) . "/log/set/" . $markOffset[$offset] . "/" . date("d.m.Y") . ".log");
    echo $message . "\n";
}
