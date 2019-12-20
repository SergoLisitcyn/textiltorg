<?php
namespace Ayers\Stores;
use \Bitrix\Main\Type;
use \Bitrix\Main\Data\Cache;
use \Bitrix\Main\Diag\Debug;

class DataCDEK {

    static public function GetData()
    {
        $сache = \Bitrix\Main\Data\Cache::createInstance();

        if ($сache->initCache(36000, 'datacdek', 'ayers.stores/datacdek'))
        {
            //$data = $сache->getVars();

            $data = self::getStoresCDEK();
        }
        elseif ($сache->startDataCache())
        {
            $data = self::getStoresCDEK();
            $сache->endDataCache($data);
        }

        return $data;
    }

    static public function getStoresCDEK()
    {
        $xml = file_get_contents("https://integration.cdek.ru/pvzlist.php");
        $object = simplexml_load_string($xml);

        foreach($object->Pvz as $item) {

            if(trim((string) $item->attributes()->CountryName) == "Россия"){
                if(isset($item->WeightLimi)) {
                    $weight_limit = $item->WeightLimi->attributes()->WeightMax;
                }

                $data[] = array(
                    'TYPE' => 'СДЭК',
                    'CITY' => trim((string) $item->attributes()->City),
                    'CODE' => trim((string) $item->attributes()->Code),
                    'ADDRESS' => trim((string) $item->attributes()->Address),
                    'METRO' => str_replace(array("ст. м. ","Ст. м. ", "м. "), "", trim((string) $item->attributes()->MetroStation)),
                    'PHONE' => self::FormatPhone((string) $item->attributes()->Phone),
                    'EMAIL' => trim((string) $item->attributes()->Email),
                    'TIME' => self::FormatTime((string) $item->attributes()->WorkTime),
                    'POSTCODE' => intval((string) $item->attributes()->Code),
                    'POINTS' => trim((string) $item->attributes()->coordY) . "," . trim((string) $item->attributes()->coordX),
                    'WEIGHT_LIMIT' => (!empty($weight_limit)) ? $weight_limit : 0
                );

                //if (count($data) >= 2){break;}
            }

        }
        return $data;
    }

    static public function FormatPhone($value = '')
    {
        $value = preg_replace('/[^-\+\(\)\s\d\,\;]+/', '', $value);
        $value = preg_replace('/\s{2,}/', ' ', $value);
        $value = preg_replace('/,/', ';', $value);
        return trim($value);
    }

    static public function FormatTime($value = '')
    {
        $value = preg_replace('/\n/', '; ', $value);
        $value = preg_replace('/\s{1,}\;/', ';', $value);
        $value = preg_replace('/\–/', '-', $value);
        $value = preg_replace('/\—/', '-', $value);
        $value = preg_replace('/\s{2,}/', ' ', $value);
        $value = preg_replace('/выходной день/u', 'выходной', $value);

        return trim($value);
    }
}