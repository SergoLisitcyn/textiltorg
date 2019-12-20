<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
ini_set('max_execution_time', 500);

CModule::IncludeModule('iblock');
CModule::IncludeModule('currency');

if (isset($_GET['ctx'])) {
    $keyRegion = $_GET['ctx'];
} else {
    $keyRegion = "msk";
}
var_dump($keyRegion);
$our_magazine = 'ТЕКСТИЛЬТОРГ';

$path_log = $_SERVER['DOCUMENT_ROOT'] . $arParams["PATH_LOG"];

global $path_log_info;
global $path_log_error;
global $path_log_ch_price;
global $path_log_outcart;
global $path_log_full;
//$path_log_info = $path_log . date('Y-m-d_H:i:s_H:i:s') . $keyRegion . "_info.log";
//$path_log_error = $path_log . date('Y-m-d_H:i:s') . $keyRegion . "_error.log";
//$path_log_outcart = $path_log . date('Y-m-d_H:i:s') . $keyRegion . "_outcart.log";
$path_log_ch_price = $path_log . date('Y-m-d_H:i:s') . $keyRegion . "_change.log";
$path_log_full = $path_log . date('Y-m-d_H:i:s') . $keyRegion . "_full.log";

require_once($_SERVER["DOCUMENT_ROOT"].$componentPath."/functions.php");

// <editor-fold defaultstate="collapsed" desc="Получение товаров из инфоблока">
$arFilter = array(
    "ACTIVE" => "Y",
    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
    ">PROPERTY_YM_ID" => "0", // Указан id товара на яндекс маркете
    "PROPERTY_YM_SYNC" => false,
    //"ID" => array(708)
);

$mrcPriceRuId = intval($arParams["MRC_PRICE_RU_ID"]); // Цена мрц
$mrcPriceRbId = intval($arParams["MRC_PRICE_RB_ID"]); // Цена мрц
$purchasingPriceRbId = intval($arParams["PURCHASING_PRICE_RB_ID"]); // Закупочная цена для РБ
$arSelect = array("ID", "XML_ID", "IBLOCK_ID", "NAME", "CATALOG_GROUP_".$mrcPriceRuId, "CATALOG_GROUP_".$mrcPriceRbId, "CATALOG_GROUP_".$purchasingPriceRbId, "PROPERTY_YM_ID", "PROPERTY_YM_STEP", "PROPERTY_MODEL_ID_FOR_ONLINERBY");

// Из параметров компонента добавим группы цен в выборку
foreach ($arParams["REGIONS"] as $key => $val) {
    $arSelect[] = "CATALOG_GROUP_".$val["price_id"];
}
// Поулучим товары
$arResult["ITEMS"] = array();
$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect); //Array("nPageSize"=>10)
//$res = CIBlockElement::GetList(array(), $arFilter, false, Array("nPageSize"=>1), $arSelect); //Array("nPageSize"=>10)
while ($ob = $res->Fetch())
{
    $arResult["ITEMS"][$ob["ID"]] = $ob;
}

var_dump(count($arResult["ITEMS"]));

// Получим минимальные цены на яндекс маркете
foreach ($arResult["ITEMS"] as $key => $item)
{

    $arProduct = array(
        "ID" => $item["ID"],
        "NAME" => $item["NAME"],
        "YM_ID" => $item["PROPERTY_YM_ID_VALUE"]
    );

    $arYandexPrice = get_price_from_yandex($reg, $arParams["REGIONS"][$keyRegion]["regid"], $arProduct);

    //var_dump($item["NAME"] . ' - ' . $arParams["REGIONS"][$keyRegion]["regid"]);
    //var_dump($arYandexPrice);

    if (!empty($arYandexPrice))
    {
        $arResult["ITEMS"][$key]["MIN_PRICE_YANDEX_".$arParams["REGIONS"][$keyRegion]['price_id']] = $arYandexPrice;
    }

}

$emailMessage = null;
// Обновляем цены
foreach ($arResult["ITEMS"] as $key => $item) {
    //var_dump($key);
		$regionPriceId = $arParams["REGIONS"][$keyRegion]["price_id"];
		$currency = $item["CATALOG_CURRENCY_".$regionPriceId];

		$ourPrice = $item["CATALOG_PRICE_".$regionPriceId]; // Наша цена
		$yandexPrice = ($keyRegion == "by") ? $item["MIN_PRICE_YANDEX_".$regionPriceId]["PRICE"] / 10000 : $item["MIN_PRICE_YANDEX_".$regionPriceId]["PRICE"]; // Минимальная цена яндекса
		$onlinerPrice = (isset($item["MIN_PRICE_ONLINER"])) ? $item["MIN_PRICE_ONLINER"] : 0; // Минимальная цена онлайнера

        //var_dump("Yandex min price: " . $yandexPrice);

		// Получаем цену МРЦ в текущей валюте
		$mrcPriceId = ($keyRegion == "by") ? $mrcPriceRbId : $mrcPriceRuId ;

		$mrcPrice = round(CCurrencyRates::ConvertCurrency($item["CATALOG_PRICE_".$mrcPriceId], $item["CATALOG_CURRENCY_".$mrcPriceId], $currency), 2);

		// Шаг цены
		if ($keyRegion == "by") {
			$stepPrice = 3;
		} else {

            // Товары до 10 тыс. руб - 250 руб, до 20 тыс. - 500 руб, выше 50 тыс. - 1500 руб
            if ($ourPrice > 5000 && $ourPrice < 10000) {
                $stepPrice = 250;
            } elseif ($ourPrice > 10000 && $ourPrice < 20000) {
                $stepPrice = 500;
            } elseif ($ourPrice > 50000) {
                $stepPrice = 1500;
            } else {
                $stepPrice = !empty($item['PROPERTY_YM_STEP_VALUE']) ? $item['PROPERTY_YM_STEP_VALUE'] : 120;
            }

        }

		// Если регион белорусия, то учитываем цену с оналйнера
		if ($keyRegion == "by") {
			if (empty($yandexPrice)) {
				$minPrice = $onlinerPrice;
			} elseif (empty($onlinerPrice)) {
				$minPrice = $yandexPrice;
			} else {
				$minPrice = ($yandexPrice < $onlinerPrice) ? $yandexPrice : $onlinerPrice;
			}
		} else {
			$minPrice = $yandexPrice;
		}

		$newPrice = 0;
		$mrcReturnConst = false;
        //var_dump("minPrice: " . $minPrice);
		if ($minPrice > 0) {
			if (!empty($mrcPrice)) { // если указана МРЦ
				if ($minPrice >= $mrcPrice) {
					$newPrice = $mrcPrice;
					$mrcReturnConst = true;
				} else {
					// иначе выставляем ещё меньше
					$newPrice = $minPrice - $stepPrice;
				}
			} else { // если товар не мрц
				$newPrice = $minPrice - $stepPrice;
			}

			// Получим закупочную цену для РБ в текущей валюте, есщи её нет закупочная цена = МРЦ
			if ($keyRegion == "by")
			{
				if (empty($item["CATALOG_PRICE_".$purchasingPriceRbId])) {
					$purchasingPrice = $mrcPrice;
				} else {
					$purchasingPrice = round(CCurrencyRates::ConvertCurrency($item["CATALOG_PRICE_".$purchasingPriceRbId], $item["CATALOG_CURRENCY_".$purchasingPriceRbId], $currency), 2);
				}
			}
			// Получим закупочную цену в текущей валюте, есщи её нет закупочная цена = МРЦ
			else
			{
				if (empty($item["CATALOG_PURCHASING_PRICE"])) {
					$purchasingPrice = $mrcPrice;
				} else {
					//$purchasingPrice = round(CCurrencyRates::ConvertCurrency($item["CATALOG_PURCHASING_PRICE"], $item["CATALOG_PURCHASING_CURRENCY"], $currency), 2);

					// курс цб +5%
					$factor = CCurrencyRates::GetConvertFactorEx($item["CATALOG_PURCHASING_CURRENCY"], $currency);
                    $factorTemp = ($factor/100)*5;
                    $factor = $factor + $factorTemp;
                    $purchasingPrice = round((float)$item["CATALOG_PURCHASING_PRICE"] * $factor, 2);

                    //var_dump($item["CATALOG_PURCHASING_PRICE"]);
                    //var_dump($item["CATALOG_PURCHASING_CURRENCY"]);
				}
			}
            //var_dump("newPrice: " . $newPrice);
            //var_dump("purchasingPrice: " . $purchasingPrice);
            //var_dump("mrcPrice: " . $mrcPrice);
			// Если новая цена меньше закупочной, то устанавливаем закупочную цену
			if($newPrice < $purchasingPrice){
				write_log($path_log_info, $path_log_full, 'NOTE', '[' . $keyRegion . ']  Товар с id ' . $item['ID'] . ' и именем "' . $item['NAME'] . '". Сгенерированная цена('.$newPrice.') меньше минимальной('.$purchasingPrice.') для данного товара' . "\n");
				$newPrice = $purchasingPrice;
			}
		}

        //var_dump("newPrice: " . $newPrice);

		// Обновляем цену
		$arFields = array(
			"PRODUCT_ID" => $item["ID"],
			"CATALOG_GROUP_ID" => $regionPriceId,
			"PRICE" => $newPrice,
			"CURRENCY" => $currency
		);

		// Обновляем цены в доп. таблице для других магазинов.
        if ($minPrice > 0) { // Без проверки с текущей ценой, для полного получения цен
            //if ($newPrice > 0 && $newPrice != $ourPrice) { // OLD!!!

            // Сохраняем цены яндекса в базу
            $arYandexOffer = $item["MIN_PRICE_YANDEX_" . $regionPriceId]["YA_OFFERS"];
            $arYandexOfferSave = array();
            if (!empty($arYandexOffer)) {
                usort($arYandexOffer, function ($a, $b) {
                    if ($a['price']['value'] > $b['price']['value']) return 1;
                    if ($a['price']['value'] == $b['price']['value']) return 0;
                    if ($a['price']['value'] < $b['price']['value']) return -1;
                });

                foreach ($arYandexOffer as $arItemYa) {
                    $arYandexOfferSave[] = array(
                        "magazine" => $arItemYa["shopInfo"]["name"],
                        "price" => $arItemYa["price"]["value"],
                        "rest" => $arItemYa["onStock"],
                    );
                }
            }

            if (count($arYandexOfferSave) > 0) {
                global $DB;
                $serialiseOffers = serialize($arYandexOfferSave);
                $idProductForSale = ($item["XML_ID"] > 0) ? $item["XML_ID"] : $item["ID"];
                $strSql = "INSERT INTO ayers_yandex_prices VALUES (0, NOW(), '$item[NAME]', '$keyRegion', '$idProductForSale', $newPrice, '$serialiseOffers');";
                $DB->Query($strSql, false, $err_mess . __LINE_);
            }

            if ($newPrice != $ourPrice) {
                $res = CPrice::GetList(array(), array("PRODUCT_ID" => $item["ID"], "CATALOG_GROUP_ID" => $regionPriceId));
                if ($arr = $res->Fetch()) {
                    CPrice::Update($arr["ID"], $arFields);
                    $clearCashe = true;
                } else {
                    if ($keyRegion == "by") {
                        $arFields["CURRENCY"] = "BYN";
                    } else {
                        $arFields["CURRENCY"] = "RUB";
                    }
                    CPrice::Add($arFields); // Если цена не установленна, устонавливаем.
                    $clearCashe = true;
                }

                if ($mrcReturnConst) {
                    $str = 'Товар с МРЦ. Товар с id ' . $item['ID'] . ' и именем <b>"' . $item['NAME'] . '"</b>. Установлена цена  ' . $newPrice . ' руб.. Старая цена: ' . $ourPrice . ' руб. Цена на яндексе на все позиции : ' . $minPrice . ' руб.' . "\n\n";
                    write_log($path_log_ch_price, $path_log_full, 'CHANGE', '[' . $keyRegion . ']  ' . $str);
                    $emailMessage .= $str;
                } else {
                    $str = 'Товар с id ' . $item['ID'] . ' и именем <b>"' . $item['NAME'] . '"</b>. Изменена цена на ' . $newPrice . ' руб.. Старая цена: ' . $ourPrice . ' руб., Шаг цены: ' . $stepPrice . ' руб. Минимальная цена на яндексе: ' . $minPrice . ' руб. от магазина "' . $item["MIN_PRICE_YANDEX_".$regionPriceId]["COMPANY"] . '"' . "\n\n";
                    write_log($path_log_ch_price, $path_log_full, 'CHANGE', '[' . $keyRegion . ']  ' . $str);
                    $emailMessage .= $str;
                }
            }
		} else {
			if ($newPrice <= 0)
				write_log($path_log_error, $path_log_full, 'ERROR', '[' . $keyRegion . ']  Товар с id ' . $item['ID'] . ' и именем "' . $item['NAME'] . '". Цена без изменений, т.к. новая цена будет меньше 0' . "\n");
			elseif ($newPrice == $ourPrice)
				write_log($path_log_info, $path_log_full, 'NOTE', '[' . $keyRegion . ']  Товар с id ' . $item['ID'] . ' и именем "' . $item['NAME'] . '". Нет необходимости менять цену.' . "\n");
		}
}

if (!empty($emailMessage)) {
    mail_utf8($keyRegion, $emailMessage);
}

if ($clearCashe) {
	global $CACHE_MANAGER;
	$CACHE_MANAGER->ClearByTag("iblock_id_".$arParams["IBLOCK_ID"]);
}
