<?php
namespace Ayers\Delivery;
use \Bitrix\Main\Type;
use \Bitrix\Main\Diag\Debug;
use \Bitrix\Main\Data\Cache;

class Pecom {
    const DEFAULT_WEIGHT = 0.40;
    const DEFAULT_VOLUME = 0.05;

    static public function Calc($dataCity = 0, $dataPlaces = array(), $isDetail = false)
    {
        if (empty($dataCity) || empty($dataPlaces))
        {
            return false;
        }

        $сache = Cache::createInstance();
        $hash = md5($dataCity.'||'.serialize($dataPlaces).'||'.intval($isDetail));

        if ($сache->initCache(36000, $hash, 'ayers.delivery/pecom/calc/'))
        {
            $result = $сache->getVars();

            if (empty($result)) {
                $result = self::GetPecomData($dataCity, $dataPlaces, $isDetail, $сache);
            }
        }
        elseif ($сache->startDataCache())
        {
            $result = self::GetPecomData($dataCity, $dataPlaces, $isDetail, $сache);
        }

        return $result;
    }

    static private function GetPecomData($dataCity, $dataPlaces, $isDetail, $сache)
    {
        $result = array();

        $data = array(
            'places' => $dataPlaces,
            'take' => array(
                'town' => self::GetCodeCity('Москва'), // ID города забора
                'tent' => 0, // требуется растентровка при заборе
                'gidro' => 0, // требуется гидролифт при заборе
                'manip' => 0, // требуется манипулятор при заборе
                'speed' => 0, // Срочный забор (только для Москвы)
                'moscow' => 0, // Без въезда, МОЖД, ТТК, Садовое.
            ),
            'deliver' => array(
                'town' => $dataCity, // ID города доставки
                'tent' => 0, // Требуется растентровка при доставке
                'gidro' => 0, // Требуется гидролифт при доставке
                'manip' => 0, // Требуется манипулятор при доставке
                'speed' => 0, // Срочная доставка (только для Москвы)
                'moscow' => 0, // Без въезда, МОЖД, ТТК, Садовое.
            ),
            'plombir' => 0, // Количество пломб
            'strah' => 0, // Величина страховки
            'ashan' => 0, // Доставка в Ашан
            'night' => 0, // Забор в ночное время
            'pal' => 0, // Требуется запаллечивание груза (0 - не требуется, значение больше нуля - количество паллет)
            'pallets' => 0 // Кол-во паллет для расчет услуги паллетной перевозки (только там, где эта услуга предоставляется)
        );

        $json = file_get_contents('http://calc.pecom.ru/bitrix/components/pecom/calc/ajax.php?' . http_build_query($data));

        $pecomResult = json_decode($json);

        if (!empty($pecomResult->auto) || !empty($pecomResult->avia))
        {
            $result['TYPE'] = 'PECOM';
            $deliver = $pecomResult->deliver[2];

            if (!empty($pecomResult->auto))
            {
                $result['STANDART'] = array(
                    'PERIODS' => (!empty($pecomResult->periods_days))? $pecomResult->periods_days: '1',
                    'PRICE' => $pecomResult->auto[2],
                );

                if ($isDetail)
                {
                    $result['STANDART']['DELIVER'] = ($deliver > 300)? $deliver : 300;
                }
            }

            if (!empty($pecomResult->avia))
            {
                $result['EXPRESS'] = array(
                    'PERIODS' => '1 - 2',
                    'PRICE' => $pecomResult->avia[2]
                );

                if ($isDetail)
                {
                    $diff = $pecomResult->avia[2] - $pecomResult->auto[2];
                    $diff = ($diff < 0)? 0: $diff;
                    $result['EXPRESS']['DELIVER'] = ($deliver + $diff > 300)? $deliver + $diff : 300;
                }
            }
        }

        $сache->endDataCache($result);

        return $result;
    }

    static public function Items2Places($dataItems = array())
    {
        $places = array();

        foreach ($dataItems as $item)
        {
            $weight = $item['CATALOG_WEIGHT'] / 1000;
            $weight = ($weight > 0) ? self::NumberFormat($weight): self::DEFAULT_WEIGHT;

            $width = ($item['CATALOG_WIDTH'])? self::NumberFormat($item['CATALOG_WIDTH'] / 1000): 0;
            $height = ($item['CATALOG_HEIGHT'])? self::NumberFormat($item['CATALOG_HEIGHT'] / 1000): 0;
            $length = ($item['CATALOG_LENGTH'])? self::NumberFormat($item['CATALOG_LENGTH'] / 1000): 0;

            $volume = $width * $height * $length;
            $volume = ($volume > 0)? self::NumberFormat($volume): self::DEFAULT_VOLUME;

            $places[] = array(
                (float) $width,
                (float) $length,
                (float) $height,
                (float) $volume,
                (float) $weight
            );
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

        if ($сache->initCache(36000, 'GetCodeCity'.md5($dataCityName), 'ayers.delivery/pecom/city'))
        {
            $regions = $сache->getVars();
        }
        elseif ($сache->startDataCache())
        {
            $json = file_get_contents('http://pecom.ru/ru/calc/towns.php');
            $regions = json_decode($json);

            $сache->endDataCache($regions);
        }

        foreach ($regions as $region)
        {
            foreach ($region as $code => $city)
            {
                if (self::CleanCityName($city) == self::CleanCityName($dataCityName))
                {
                    return $code;
                }
            }
        }

        return false;
    }

    static private function CleanCityName($dataName = '')
    {
        $dataName = preg_replace('/\([.]+\)/', '', $dataName);
        return trim($dataName);
    }
}