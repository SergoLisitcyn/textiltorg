<?php
namespace Ayers\Stores;
use Bitrix\Main\Type;
use \Bitrix\Main\Diag\Debug;

//todo: в будущем можно добавить проерку данных
class Data {

    static public function Save($arData = array())
    {

        $rsStores = StoresTable::getList(array(
            'select' => array('*'),
            'filter' => array(
                '!=TYPE' => array('ТекстильТорг')
            )
        ));

        $arStores = array();
        while ($arStore = $rsStores->fetch())
        {
            $arStores[$arStore['CODE']] = $arStore;
            $arDeleteItems[$arStore['CODE']] = $arStore;
        }

        foreach ($arData as $arItem)
        {
            $arDataItem = array(
                'TYPE' => $arItem['TYPE'],
                'CODE' => $arItem['CODE'],
                'CITY' => $arItem['CITY'],
                'ADDRESS' => $arItem['ADDRESS'],
                'METRO' => $arItem['METRO'],
                'SHORT_ADDRESS' => self::FormatShortAddress($arItem['CITY'], $arItem['ADDRESS']),
                'PHONE' => $arItem['PHONE'],
                'EMAIL' => $arItem['EMAIL'],
                'TIME' => $arItem['TIME'],
                'SORT' => 500,
                'POSTCODE' => $arItem['POSTCODE'],
                'POINTS' => $arItem['POINTS'],
                'WEIGHT_LIMIT' => (intval($arItem['WEIGHT_LIMIT']))? $arItem['WEIGHT_LIMIT']: 0
            );

            if (array_key_exists($arItem['CODE'], $arStores))
            {
                if (!self::IsUpdate($arDataItem, $arStores[$arItem['CODE']]))
                {
                    unset($arDeleteItems[$arItem['CODE']]);
                    continue;
                }

                $arDataItem['DATE_UPDATE'] = new Type\DateTime;

                $result = StoresTable::update($arStores[$arItem['CODE']]['ID'], $arDataItem);
                unset($arDeleteItems[$arItem['CODE']]);

                if (!$result->isSuccess())
                {
                    throw new Exception($result->getErrorMessages());
                }
            }
            else
            {
                $result = StoresTable::add($arDataItem);

                if ($result->isSuccess())
                {
                    $arStores[$arItem['CODE']] = $arDataItem;
                    unset($arDeleteItems[$arItem['CODE']]);
                }
                else
                {
                    throw new Exception($result->getErrorMessages());
                }
            }
        }

        foreach ($arDeleteItems as $arItem)
        {
            StoresTable::delete($arItem['ID']);
        }

        return true;
    }

    static public function IsUpdate($arDataItem = array(), $arStore = array())
    {
        foreach ($arDataItem as $code => $arDataValue)
        {
            if ($arDataItem[$code] != $arStore[$code])
            {
                return true;
            }
        }

        return false;
    }

    static public function FormatShortAddress($city = '', $value = '')
    {
        $value = preg_replace('/(?:[г\s\.]+)?'.$city.'(?:[\s\,]+)/iu', '', $value);
        $value = preg_replace('/(?:[\,\s]+)?[а-я]+\s(область|обл|респ|край)(?:[\.\,\s]+)?/iu', '', $value);
        $value = preg_replace('/\,([^\s]+)/iu', ', $1', $value);
        $value = preg_replace('/\.([^\s]+)/iu', '. $1', $value);
        $value = preg_replace('/\.(?:[\s]{1,})?\,/iu', ',', $value);
        $value = rtrim($value, '.');
        $value = rtrim($value, ',');

        return trim($value);
    }
}