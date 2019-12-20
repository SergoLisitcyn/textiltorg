<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
function mail_utf8($keyRegion, $message, $adEmail = "")
{
    $subject = "Цены для " . $keyRegion;
    $from_email = "admin@textiletorg.ru";
    $to = "andrey@textiletorg.ru,i.bashko@textiletorg.ru" . $adEmail;
    $from_user = "Робот цен";
    $message = nl2br($message);
    $from_user = "=?UTF-8?B?".base64_encode($from_user)."?=";
    $subject = "=?UTF-8?B?".base64_encode($subject)."?=";

    $headers = "From: $from_user <$from_email>\r\n".
        "MIME-Version: 1.0" . "\r\n" .
        "Content-type: text/html; charset=UTF-8" . "\r\n";

    return mail($to, $subject, $message, $headers);
}

/*
 * Отправляем запрос
 */
function curlGet($url,$arHeaders,$return_header_in_result=false) {
    $handle = curl_init();

    /*curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($handle, CURLOPT_VERBOSE, 1);
    curl_setopt($handle, CURLOPT_HEADER, 1);*/

    curl_setopt($handle, CURLOPT_URL, $url);
    /* curl_setopt($handle, CURLOPT_HTTPHEADER, $headers); */
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    if(!empty($arHeaders))
        curl_setopt($handle, CURLOPT_HTTPHEADER, $arHeaders);
    if ($return_header_in_result) curl_setopt($handle, CURLOPT_HEADER, 1);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($handle);
    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    return array("code"=>$code,"response"=>$response);
}

/*
 * Возвращает массив с минимальной ценой, названием компании и флагом 
 */
function get_minimal_price($rsp) {
    $offers = array();
    $min_offer = array();
    $min_price = 0;
    $eq_price = true;
    global $ignore_magaz;	
	
	$ignore_magaz = array('МАДАМ ДОМА - Магазин-Эксперт в швейной технике');
	
	$our_magazine = 'ТЕКСТИЛЬТОРГ';
	
    foreach($rsp as $val){
        $name = $val['shopInfo']['name'];
        $price = round($val['price']['value']);
        $rest = $val['onStock'];
        if($name != $our_magazine && $rest && !in_array($name, $ignore_magaz)){
            $offers[] = array('company' => $name, 'price' => $price);
            if( $price > 0 && ($price <= $min_price || empty($min_price))){
                $min_price = $price;
                $min_offer = array('company' => $name, 'price' => $price);
            }
        }
    }
    
    if(!empty($min_offer)){
        $t_price = $offers[0]['price'];
        foreach($offers as $val){
            if($val['price']!=$t_price)
                $eq_price = false;
        }
        $result = $min_offer;
        $result['eq_price'] = $eq_price;
        return $result;
    } else {
        return array();
    }
}

/*
 * Определяем, есть ли наше предложение в карточке товара яндекса
 */
function is_our_offer($rsp){
    $our_offer = false;
    global $our_magazine;	
    foreach($rsp as $val){
        $name = $val['shopInfo']['name'];       
        if($name == $our_magazine){
            $our_offer = true;
            break;
        }
    }
    return $our_offer;
}

/*
 * Пишем лог
 */
function write_log($path_log, $path_log_full, $type_mes, $message){
    
    if (!($fp = fopen($path_log, 'a')) || !($fp_full = fopen($path_log_full, 'a'))){
        return false;
    }
    fwrite($fp, '['.date("H:i:s").'] '."[$type_mes]:  ".$message."\n");
    fwrite($fp_full, '['.date("H:i:s").'] '."[$type_mes]:  ".$message."\n");
    fclose($fp);
    fclose($fp_full);
}

function getYandexData($token, $url) {
    // Установка HTTP-заголовков запроса
    $headers = array(
        "Authorization: $token",                    // OAuth-токен. Использование слова Bearer обязательно
        "Accept-Language: ru",                             // Язык ответных сообщений
        "Content-Type: application/json; charset=utf-8"    // Тип данных и кодировка запроса
    );

    // Создание контекста потока: установка HTTP-заголовков и тела запроса
    $streamOptions = stream_context_create(array(
        'http' => array(
            'method' => 'GET',
            'ignore_errors' => true, // Извлечь содержимое ответа даже в случае, если HTTP-код ответа отличен от 200
            'header' => $headers,
        )
    ));

    // Выполнение запроса, получение результата
    $result = file_get_contents($url, 0, $streamOptions);

    return array("response" => $result, "info" => $http_response_header);
}

function getHeaderParser($http_header) {
    if (isset($http_header)) {
        foreach ($http_header as $header) {
            if (preg_match('#HTTP/[0-9\.]+\s+([0-9]+)#', $header, $arr)) {
                $httpStr = $header; // Пояснение к HTTP-коду ответа
                $result["code"] = intval($arr[1]); // Числовое значение HTTP-кода
            }
            if (preg_match('/X-RateLimit-Daily-Remaining: (\d+)/', $header, $arr)) {
                $result["limit"] = $arr[1]; // (X-RateLimit-Daily-Remaining) оставшееся количество запросов для указанного в запросе авторизационного ключа в сутки до превышения суточного ограничения;
            }
        }
        return $result;
    }
    else {
        return 0;
    }
}

function get_price_from_yandex($region_code, $region_id, $arProduct) {
	global $path_log_info;
	global $path_log_error;
	global $path_log_ch_price;
	global $path_log_outcart;
	global $path_log_full;

	$auth_key = '896995a4-a647-4e61-9546-208b6a135ffe'; // old - WHfPf4QCWdkufEWgsZ2LVxHgLqljtT
	$header = Array('Authorization: ' . $auth_key);
	
	$count_of = 0;
	$end = false;
	$page = 1;
	$c_connect = 0;
	$arYandexOffers = array();
	$startUpdate = true;
	
	// <editor-fold defaultstate="collapsed" desc="Получаем страницы яндекса с предложениями">
	
	while (!$end) {
		$url = "https://api.content.market.yandex.ru/v1/model/".$arProduct['YM_ID']."/offers.json?count=30&geo_id=" . $region_id . '&page=' . $page;

		//var_dump($url);
		
		//$rsp = curlGet($url, $header); // Old function!
        $yandexData = getYandexData($auth_key, $url);
        $resultParser = getHeaderParser($yandexData["info"]);
        $rsp = $yandexData["response"];

		if ($resultParser['code'] != '200') {
			write_log($path_log_error, $path_log_full, 'ERROR', '[' . $region_code . ']  Товар с id ' . $arProduct['ID'] . ' и именем "' . $arProduct['NAME'] . '". Ошибка при запросе к яндексу, ответ ядекса: ' . $rsp . "\n" . json_encode($yandexData["info"]) . "\n");
			$end = true;
		} else {
            //write_log($path_log_error, $path_log_full, 'NOTE', $rsp . "\n" . json_encode($yandexData["info"]) . "\n");
			$response = json_decode($rsp, true);

			if (!isset($response['offers']['count']) || !isset($response['offers']['items']) || !isset($response['offers']['page'])) {
				// если пришел ответ от яндекса без сообщения об ошибке, но с неправельной структурой данных
				// пишем в лог увеличиваем смещение и выходим
				write_log($path_log_error, $path_log_full, 'ERROR', '[' . $region_code . ']  Товар с id ' . $arProduct['ID'] . ' и именем "' . $arProduct['NAME'] . '". Не ясен ответ от яндекса "' . $rsp . '"' . "\n");
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
			
			foreach($res_arr as $arItem)
			{
				$arYandexOffers[] = array(
					"shopInfo" => $arItem["shopInfo"],
					"price" => $arItem["price"],
					"onStock" => $arItem["onStock"],
					"modelId" => $arItem["modelId"]
				);
			}
			// копируем полученные предложения в наш массив
			//$arYandexOffers = array_merge($arYandexOffers, $res_arr);
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
			write_log($path_log_error, $path_log_full, 'ERROR', '[' . $region_code . ']  Товар с id ' . $arProduct['ID'] . ' и именем "' . $arProduct['NAME'] . '". Слишком много запросов к яндексу: ' . $rsp . "\n");
			$end = true;
			$startUpdate = false;
		}
	}
	
	if (!empty($arYandexOffers) && $startUpdate)
	{
		// Получаем минимальную цену на маркете
		$min_offer = get_minimal_price($arYandexOffers);
		
		// Проверка есть ли наше предложение в карточке товара
		if (!is_our_offer($arYandexOffers)) {
			write_log($path_log_outcart, $path_log_full, 'ERROR', '[' . $region_code . ']  Товар с id ' . $arProduct['ID'] . ' и именем "' . $arProduct['NAME'] . '". Наше предложение отсутствует в карточке товара.' . "\n");
		}
		
		if (!empty($min_offer))
		{
			if ((int) $min_offer['price'] <= 0)
			{
				// если мы получили неверную цену от яндекса, то вылетаем с ошибкой
				write_log($path_log_error, $path_log_full, 'ERROR', '  Товар с id ' . $arProduct['ID'] . ' и именем "' . $arProduct['NAME'] . '". Получены не корректные цены от яндекса. Товар пропушен.' . "\n");
				return array();
			}
			else 
			{
				return array("PRICE" => $min_offer['price'], "COMPANY" => $min_offer['company'], "YA_OFFERS" => $arYandexOffers);
			}
		}
	}
	else
	{
		write_log($path_log_error, $path_log_full, 'ERROR', '[' . $region_code . ']  Товар с id ' . $arProduct['ID'] . ' и именем "' . $arProduct['NAME'] . '". Не удалось получить данные от яндекса, либо нет нужных предложений. Вернулось: ' . $rsp . "\n");
	}
	
	return array();
}

/*
 * Функция авторизируется в личном кабинете onliner,
 * скачивает архив с csv файлом - цены конкурентов магазинов,
 * сохроняет архив в указанную папку.
 * */

function getCompetitorsPricesArchive($dir)
{
    // URL скрипта авторизации
    $login_url = 'http://b2b.onliner.by/login';

    // параметры для отправки запроса - логин и пароль
    $post_data = 'email=13993&password=Jcee0873';

    // создание объекта curl
    $ch = curl_init();

    // используем User Agent браузера
    $agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:18.0) Gecko/20100101 Firefox/18.0";
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);

    // задаем URL
    curl_setopt($ch, CURLOPT_URL, $login_url);

    // указываем что это POST запрос
    curl_setopt($ch, CURLOPT_POST, 1);

    // задаем параметры запроса
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

    // указываем, чтобы нам вернулось содержимое после запроса
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // в случае необходимости, следовать по перенаправлени¤м
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    // в случае если необходимо подробно увидить заголовки
    // curl_setopt($ch, CURLOPT_VERBOSE, 1);

    /*
        Задаем параметры сохранени¤ cookie
        как правило Cookie необходимы для дальнейшей работы с авторизацией
    */

    curl_setopt($ch, CURLOPT_COOKIEJAR, $_SERVER['DOCUMENT_ROOT'] . $dir . 'cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, $_SERVER['DOCUMENT_ROOT'] . $dir . 'cookie.txt');

    // выполняем запрос для авторизации
    curl_exec($ch);

    // Url для скачивания файла
    $host = "http://b2b.onliner.by/shop/competitors_prices";

    // Задаем имя файлу
    $output_filename = 'competitors_prices.csv.gz';

    // Путь хранения
    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . $dir . $output_filename, 'w');

    curl_setopt($ch, CURLOPT_URL, $host);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_FILE, $fp);

    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
}
/*
 * Фукция принимает путь к архиву .gz и распаковывает
*/
function unzipCompetitorsPrices($file_name)
{
	$file_name = $_SERVER['DOCUMENT_ROOT'] . $file_name;

    // Увеличение этого значения может повысить производительность
    $buffer_size = 4096; // read 4kb at a time
    $out_file_name = str_replace('.gz', '', $file_name);

    // Открываем файл (в двоичном режиме)
    $file = gzopen($file_name, 'rb');
    $out_file = fopen($out_file_name, 'wb');

    while (!gzeof($file)) {
        fwrite($out_file, gzread($file, $buffer_size));
    }

    fclose($out_file);
    gzclose($file);
}
/*
 * Функция возвращяет массив с ценами из onliner,
 * Массив ввиде onliner_id - цена
 * */
function getCompetitorsPrices($priceStep)
{
	$fileName = $_SERVER['DOCUMENT_ROOT']."/upload/onliner/competitors_prices.csv";
	
    $arrPriceData = array();

    if ($f = @fopen($fileName, "rt")) {
        // Первая строка представляет заголовки.
        // Предварительный вызов fgetcsv, позволит начать цикл со второй строки.
        fgetcsv($f,100000,';','"');
        for ($i=0; $data=fgetcsv($f, 100000,';','"'); $i++ ) {
            // Формируем массив onliner_id - цена
            $arrPriceData[$data[2]] = getPrice($data[8]);
        }
        fclose($f);
        return $arrPriceData;
    } else {
        return false;
    }
}
/*
 * Функция получает цену в денежных знаках образца 2009 года
 * Приводит string to float
 * Вычитает шаг цены
 * Возврашяет цену в денежных знаках образца 2000 года
 *
 * e.g. getPrice(374,70) return 3717000 (371.7)
 * */
function getPrice($price)
{
    if ($price) {
        $price = str_replace(",",".",$price);
        return $price * 10000;
    }
}

