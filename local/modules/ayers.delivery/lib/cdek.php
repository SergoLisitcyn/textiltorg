<?php
namespace Ayers\Delivery;
use \Bitrix\Main\Type;
use \Bitrix\Main\Diag\Debug;
use \Bitrix\Main\Data\Cache;

class Cdek {
    const DEFAULT_WEIGHT = 0.40;
    const DEFAULT_VOLUME = 0.05;

    static public function Calc($dataCity = 0, $dataPlaces = array(), $isDetail = false, $dataTariff = 10)
    {
        if (empty($dataCity) || empty($dataPlaces))
        {
            return false;
        }

        $сache = Cache::createInstance();

        // serialize не подходит, вылезают диффекты в некоторых городах
        $hashPlaces = '';
        foreach ($dataPlaces as $value) {
            $hashPlaces .= $value.';';
        }

        $hash = md5($dataCity.'||'.$hashPlaces.'||'.intval($isDetail).'||'.$dataTariff);

        if ($сache->initCache(36000, $hash, 'ayers.delivery/cdek/calc'))
        {
            $result = $сache->getVars();
        }
        elseif ($сache->startDataCache())
        {
            $calc = new \CalculatePriceDeliveryCdek();
            $calc->setSenderCityId(self::GetCodeCity('Москва'));
            $calc->setReceiverCityId($dataCity);
            $calc->setDateExecute(date('Y-m-d'));
            $calc->setTariffId($dataTariff); // Склад- Склад

            foreach ($dataPlaces as $place)
            {
                if (empty($place['volume']))
                {
                    $calc->addGoodsItemBySize(
                        $place['weight'],
                        $place['length'],
                        $place['width'],
                        $place['height']
                    );
                }
                else
                {
                    $calc->addGoodsItemByVolume(
                        $place['weight'],
                        $place['volume']
                    );
                }
            }

            if ($calc->calculate() === true)
            {
                $data = $calc->getResult();
                $result['TYPE'] = 'CDEK';
                $result['STANDART'] = array(
                    'PERIODS' => self::GetPeriods($data['result']['deliveryPeriodMin'], $data['result']['deliveryPeriodMax']),
                    'PRICE' => (float) $data['result']['price']
                );

                if ($isDetail)
                {
                    $delivery = self::Calc($dataCity, $dataPlaces, false, 11);
                    $result['STANDART']['DELIVER'] = $delivery['PRICE'] - $result['PRICE'];

                    if ($result['STANDART']['DELIVER'] < 300)
                    {
                        $result['STANDART']['DELIVER'] = 300;
                    }
                }

                $сache->endDataCache($result);
            }
        }

        return $result;
    }

    static private function GetPeriods($min, $max)
    {
        return ($min == $max)? $max: $min.' - '.$max;
    }

    static public function Items2Places($dataItems = array())
    {
        $places = array();

        foreach ($dataItems as $item)
        {
            $weight = $item['CATALOG_WEIGHT'] / 1000;
            $weight = ($weight > 0) ? self::NumberFormat($weight): self::DEFAULT_WEIGHT;

            $width = ($item['CATALOG_WIDTH'])? intval($item['CATALOG_WIDTH'] / 10): 0;
            $height = ($item['CATALOG_HEIGHT'])? intval($item['CATALOG_HEIGHT'] / 10): 0;
            $length = ($item['CATALOG_LENGTH'])? intval($item['CATALOG_LENGTH'] / 10): 0;

            if ($width > 0 && $height > 0 && $length > 0)
            {
                $places[] = array(
                    'weight' => (float) $weight,
                    'width' => (int) $width,
                    'height' => (int) $height,
                    'length' => (int) $length,
                );
            }
            else
            {
                $places[] = array(
                    'weight' => (float) $weight,
                    'volume' => self::DEFAULT_VOLUME
                );
            }
        }

        return $places;
    }

    static private function NumberFormat($number = 0)
    {
        return number_format($number, 2, '.', '');
    }

    static public function GetCodeCity($dataCityName = '')
    {
        $сache = Cache::createInstance();

        if ($сache->initCache(36000, 'GetCodeCity'.md5($dataCityName), 'ayers.delivery/cdek/city'))
        {
            $result = $сache->getVars();
        }
        elseif ($сache->startDataCache())
        {
            $query = array(
                'q' => $dataCityName,
                'name_startsWith' => $dataCityName
            );

            $json = file_get_contents('http://api.cdek.ru/city/getListByTerm/jsonp.php?' . http_build_query($query));
            $region = json_decode($json);

            if (empty($region->geonames))
            {
                return false;
            }

            $result = $region->geonames[0]->id;

            $сache->endDataCache($result);
        }

        return $result;
    }
}