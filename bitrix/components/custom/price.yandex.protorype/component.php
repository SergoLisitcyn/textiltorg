<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
ini_set('max_execution_time',0); 
require_once($_SERVER["DOCUMENT_ROOT"].$componentPath."/functions.php");

CModule::IncludeModule('iblock');
CModule::IncludeModule('currency');

$auth_key = 'WHfPf4QCWdkufEWgsZ2LVxHgLqljtT';
$header = Array('Authorization: ' . $auth_key);
$our_magazine = 'ТЕКСТИЛЬТОРГ';

$path_log = $_SERVER['DOCUMENT_ROOT'] . $arParams["PATH_LOG"];

$path_log_info = $path_log . date('Y-m-d_H:i:s_H:i:s') . "_info.log";
$path_log_error = $path_log . date('Y-m-d_H:i:s') . "_error.log";
$path_log_ch_price = $path_log . date('Y-m-d_H:i:s') . "_change.log";
$path_log_outcart = $path_log . date('Y-m-d_H:i:s') . "_outcart.log";
$path_log_full = $path_log . date('Y-m-d_H:i:s') . "_full.log";

$id_price = intval($arParams["MRC_PRICE_ID"]);

// <editor-fold defaultstate="collapsed" desc="Получение товаров из инфоблока">

$arFilter = array(
    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
    ">PROPERTY_YM_ID" => "0", // Указан id товара на яндекс маркете
    "PROPERTY_YM_SYNC" => false,
);

// Для беларусии добавляем фильтр
if (isset($arParams["REGIONS"]["by"])) {
	$arFilter["PROPERTY_MODEL_ID_FOR_ONLINERBY"] = false;
}

$arSelect = array("ID", "IBLOCK_ID", "NAME", "CATALOG_GROUP_".$id_price, "PROPERTY_YM_ID", "PROPERTY_YM_STEP");

// Из параметров компонента добавим группы цен в выборку
foreach ($arParams["REGIONS"] as $key => $val) {
    $arSelect[] = "CATALOG_GROUP_".$val["price_id"];
}

$arResult["ITEMS"] = array();
$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
while($ob = $res->Fetch()){ 
    $arResult["ITEMS"][] = $ob;
}

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Запрос цен в яндекс маркете">

$clearCashe = false;

foreach ($arResult["ITEMS"] as $item) {
    
    // Перебираем с регионами
    foreach ($arParams["REGIONS"] as $reg => $data) {
        
        $count_of = 0;
        $end = false;
        $page = 1;
        $c_connect = 0;
        $arYandexOffers = array();
        $startUpdate = true;
        
        // <editor-fold defaultstate="collapsed" desc="Получаем страницы яндекса с предложениями">
        
        while (!$end) {
            $url = "https://api.content.market.yandex.ru/v1/model/".$item['PROPERTY_YM_ID_VALUE']."/offers.json?count=30&geo_id=" . $data['regid'] . '&page=' . $page;
            $rsp = curlGet($url, $header);
            if ($rsp['code'] != '200') {
                write_log($path_log_error, $path_log_full, 'ERROR', '[' . $reg . ']  Товар с id ' . $item['ID'] . ' и именем "' . $item['NAME'] . '". Ошибка при запросе к яндексу, ответ ядекса: ' . $rsp['response'] . "\n");
                $end = true;
            } else {
                $response = json_decode($rsp['response'], true);
                if (!isset($response['offers']['count']) || !isset($response['offers']['items']) || !isset($response['offers']['page'])) {
                    // если пришел ответ от яндекса без сообщения об ошибке, но с неправельной структурой данных
                    // пишем в лог увеличиваем смещение и выходим
                    write_log($path_log_error, $path_log_full, 'ERROR', '[' . $reg . ']  Товар с id ' . $item['ID'] . ' и именем "' . $item['NAME'] . '". Не ясен ответ от яндекса "' . $rsp['response'] . '"' . "\n");
                    break;
                }
                // если мы получили все предложения от яндекса - выходим из цикла
                if ($response['offers']['count'] == 0)
                    break;
                // увеличиваем счетчик полученных предложений
                $count_of += $response['offers']['count'];

                $res_arr = $response['offers']['items'];
                if (isset($response['offers']['regionDelimiterPosition'])) {
                    // если мы получили предложения с доставкой из других регионов, то  вырезаем их
                    $end = true;
                    if (isset($response['offers']['items'][$response['offers']['regionDelimiterPosition']]))
                        $res_arr = array_slice($res_arr, 0, $response['offers']['regionDelimiterPosition']);
                }
                // копируем полученные предложения в наш массив
                $arYandexOffers = array_merge($arYandexOffers, $res_arr);
                if ($response['offers']['total'] <= $count_of)
                    // если получили заявленое яндексом кол-во предложений - выходим
                    // из цикла
                    $end = true;
                else
                    $page++;
            }
            $c_connect++;
            if ($c_connect >= 6) {
                // затычка на слишком большое количество запросов к маркету
                write_log($path_log_error, $path_log_full, 'ERROR', '[' . $reg . ']  Товар с id ' . $item['ID'] . ' и именем "' . $item['NAME'] . '". Слишком много запросов к яндексу: ' . $rsp['response'] . "\n");
                $end = true;
                $startUpdate = false;
            }

        }
        
        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc="Обновление цен">
        
        if (!empty($arYandexOffers) && $startUpdate) {

            // Получаем минимальную цену на маркете
            $min_offer = get_minimal_price($arYandexOffers);

            // Проверка есть ли наше предложение в карточке товара
            if (!is_our_offer($arYandexOffers)) {
                write_log($path_log_outcart, $path_log_full, 'ERROR', '[' . $reg . ']  Товар с id ' . $item['ID'] . ' и именем "' . $item['NAME'] . '". Наше предложение отсутствует в карточке товара.' . "\n");
            }
            
            if (!empty($min_offer)) {

                // Поучаем нашу цену
                $key_price = "CATALOG_PRICE_".$data["price_id"];
                $our_price = $item[$key_price];
                
                $new_price = 0;
				if ($reg == "by") {
                    $step_price = 8000;
                } else {
                    $step_price = !empty($item['PROPERTY_YM_STEP_VALUE']) ? $item['PROPERTY_YM_STEP_VALUE'] : 120; // если шаг не задан, то 120
                }
                $mrc_return_const = false;
                
                // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                // Если by, то цену умножаем на 10000, чтобы избежать ошибок связанных с деноменацией
                // if($reg == 'by') { 
                    //$min_offer['price'] = $min_offer['price'] * 10000;
                    //$step_price = $step_price * 10000;
                // }
                // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

                if ((int) $min_offer['price'] <= 0)
                {
                    // если мы получили неверную цену от яндекса, то вылетаем с ошибкой
                    write_log($path_log_error, $path_log_full, 'ERROR', '  Товар с id ' . $item['ID'] . ' и именем "' . $item['NAME'] . '". Получены не корректные цены от яндекса. Товар пропушен.' . "\n");
                    exit();
                }

                // Получаем код текущей валюты
                $key_currency = "CATALOG_CURRENCY_".$data["price_id"];
                $currency = $item[$key_currency];
                
                // Получаем цену МРЦ в текущей валюте
                $mrc_price = round(CCurrencyRates::ConvertCurrency($item["CATALOG_PRICE_".$id_price], $item["CATALOG_CURRENCY_".$id_price], $currency), 2);

                //echo "Регион - $reg, мин цена на маркете - $min_offer[price], мрц - $mrc_price<br>";
                
                if (!empty($mrc_price)) { // если указана МРЦ

                    if ($min_offer['price'] >= $mrc_price) {
                        $new_price = $mrc_price;
                        $mrc_return_const = true;
                    } else {
                        // иначе выставляем ещё меньше
                        $new_price = $min_offer['price'] - $step_price;
                    }
                } else { // если товар не мрц
                    $new_price = $min_offer['price'] - $step_price;
                }
                
                // Получим закупочную цену в текущей валюте, есщи её нет закупочная цена = МРЦ
                if (empty($item["CATALOG_PURCHASING_PRICE"])) {
                    $purchasing_price = $mrc_price;
                } else {
                    $purchasing_price = round(CCurrencyRates::ConvertCurrency($item["CATALOG_PURCHASING_PRICE"], $item["CATALOG_PURCHASING_CURRENCY"], $currency), 2);
                }

                // Если новая цена меньше закупочной, то устанавливаем закупочную цену
                if($new_price < $purchasing_price){
                    write_log($path_log_info, $path_log_full, 'NOTE', '[' . $reg . ']  Товар с id ' . $item['ID'] . ' и именем "' . $item['NAME'] . '". Сгенерированная цена('.$new_price.') меньше минимальной('.$purchasing_price.') для данного товара' . "\n");
                    $new_price = $purchasing_price;
                }

                if ($new_price > 0 && $new_price != $our_price) {
                    
                    // Обновляем цену
                    $arFields = array(
                        "PRODUCT_ID" => $item["ID"],
                        "CATALOG_GROUP_ID" => $data["price_id"],
                        "PRICE" => $new_price,
                        "CURRENCY" => $currency
                    );
                    
                    $res = CPrice::GetList(array(),array("PRODUCT_ID" => $item["ID"], "CATALOG_GROUP_ID" => $data["price_id"]));
                    if ($arr = $res->Fetch())
                    {
                        CPrice::Update($arr["ID"], $arFields);
						$clearCashe = true;
                    }

                    if ($mrc_return_const)
                        write_log($path_log_ch_price, $path_log_full, 'CHANGE', '[' . $reg . ']  ' . 'Товар с МРЦ. Товар с id ' . $item['ID'] . ' и именем "' . $item['NAME'] . '". Установлена цена  ' . $new_price . ' руб.. Старая цена: ' . $our_price . ' руб. Цена на яндексе на все позиции : ' . $min_offer['price'] . ' руб.' . "\n");
                    else
                        write_log($path_log_ch_price, $path_log_full, 'CHANGE', '[' . $reg . ']  ' . 'Товар с МРЦ. Товар с id ' . $item['ID'] . ' и именем "' . $item['NAME'] . '". Изменена цена на ' . $new_price . ' руб.. Старая цена: ' . $our_price . ' руб., Шаг цены: ' . $step_price . ' руб. Разные цены на данную позицию. Минимальная цена на яндексе: ' . $min_offer['price'] . ' руб. от магазина "' . $min_offer['company'] . '"' . "\n");

                } else {
                    if ($new_price <= 0)
                        write_log($path_log_error, $path_log_full, 'ERROR', '[' . $reg . ']  Товар с id ' . $item['ID'] . ' и именем "' . $item['NAME'] . '". Цена без изменений, т.к. новая цена будет меньше 0' . "\n");
                    elseif ($new_price == $our_price)
                        write_log($path_log_info, $path_log_full, 'NOTE', '[' . $reg . ']  Товар с id ' . $item['ID'] . ' и именем "' . $item['NAME'] . '". Нет необходимости менять цену.' . "\n");
                }
            } else {
                // если нет предложений с наличием товара
                write_log($path_log_info, $path_log_full, 'NOTE', '[' . $reg . ']  Товар с id ' . $item['ID'] . ' и именем "' . $item['NAME'] . '". Цена без изменений. Нет предложений с наличием товара.' . "\n");
            }
        } else {
            write_log($path_log_error, $path_log_full, 'ERROR', '[' . $reg . ']  Товар с id ' . $item['ID'] . ' и именем "' . $item['NAME'] . '". Не удалось получить данные от яндекса, либо нет нужных предложений. Вернулось: ' . $rsp['response'] . "\n");
        }
        
        // </editor-fold>
    }
}

if ($clearCashe) {
	global $CACHE_MANAGER;
	$CACHE_MANAGER->ClearByTag("iblock_id_".$arParams["IBLOCK_ID"]);
}
// </editor-fold>