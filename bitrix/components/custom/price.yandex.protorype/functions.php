<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

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
	
	$ignore_magaz = array();
	
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