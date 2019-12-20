<?php
namespace Ayers\Stores;
use Bitrix\Main\Type;
use Bitrix\Main\Data\Cache;
use \Bitrix\Main\Diag\Debug;

class DataPecom {
    const SITE_URL = 'http://pecom.ru';
    const LIST_URL = '/contacts/list-filials/';

    static public function GetData()
    {
        $сache = \Bitrix\Main\Data\Cache::createInstance();
        $data = array();

        if ($сache->initCache(36000, 'datapecom', 'ayers.stores/datapecom'))
        {
            //$data = $сache->getVars();

            $arCities = self::GetCities();
            foreach ($arCities as $arCity)
            {
                $data = array_merge($data, self::GetItems($arCity));

                //if (count($data) >= 5){break;}
            }

            $сache->endDataCache($data);
        }
        elseif ($сache->startDataCache())
        {
            $arCities = self::GetCities();
            foreach ($arCities as $arCity)
            {
                $data = array_merge($data, self::GetItems($arCity));

                //if (count($data) >= 2){break;}
            }

            $сache->endDataCache($data);
        }

        return $data;
    }

    static private function GetCities()
    {
        $html = file_get_contents(self::SITE_URL . self::LIST_URL);

        $document = \phpQuery::newDocument($html);
        $list = $document->find('._cities a');

        $arCities = array();
        foreach ($list as $item)
        {
            $item = pq($item);

            $arCities[] = array(
                'NAME' => $item->text(),
                'HREF' => $item->attr('href')
            );

            //if (count($data) >= 2){break;}
        }

        return $arCities;
    }

    static private function GetItems($arCity = array())
    {
        $arResult = array();

        $html = file_get_contents(self::SITE_URL . $arCity['HREF']);
        $document = \phpQuery::newDocument($html);
        $list = $document->find('.contact-item');
        foreach ($list as $item)
        {
            $item = pq($item);

            if (!self::PQIsActive($item))
            {
                continue;
            }

            $arItem = array();
            $arItem['CITY'] = $arCity['NAME'];

            //$item->find('.col p b.uppercase')->remove();

            $address = self::PQAddress($item);
            $points = self::GetPoints($item);

            $arItem['TYPE'] = 'ПЭК';
            $arItem['CODE'] = self::PQGetCode($points);
            $arItem['POSTCODE'] = self::PQPostCode($item);
            $arItem['ADDRESS'] = $address;
            $arItem['PHONE'] = trim($item->find('._info div:eq(4) div:eq(1)')->text());
            $arItem['EMAIL'] = trim($item->find('._info div:eq(7) div:eq(1) a')->text());
            $arItem['TIME'] = self::PQTime($item);
            $arItem['POINTS'] = $points;

            $arResult[] = $arItem;
        }

        return $arResult;
    }

    static private function GetPoints($item)
    {
        $coordinates = trim($item->find('._info div:eq(3)')->text());
        $baseUrl = "https://geocode-maps.yandex.ru/1.x/?apikey=6aa04225-c875-43fb-b941-90d272be7fd2&format=json&geocode=";
        $json = file_get_contents($baseUrl.$coordinates);
        $tmp = json_decode($json, true);
        $coordinates = $tmp['response']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['Point']['pos'];
        $arTmp = explode(" ", $coordinates);
        $coordinates = $arTmp[1] .", ". $arTmp[0];

        return $coordinates;
    }

    static private function PQGetCode($points)
    {
        preg_match_all('!\d+!', $points, $arMatch);
        $result = implode($arMatch[0]);

        return $result;
    }

    static private function PQPostCode($item)
    {
        $text = $item->find('._info div:eq(0) div')->text();

        if (is_numeric(substr($text, 0, 1))) {

            if (preg_match('/(\d+),(.+)/', $text, $arMatch)) {
                return intval($arMatch[1]);
            }
        }

        return 0;
    }

    static private function PQAddress($item)
    {
        $text = $item->find('._info div:eq(0) div')->text();

        if (is_numeric(substr($text, 0, 1))) {
            if (preg_match('/(\d+),(.+)/', $text, $arMatch))
            {
                return trim($arMatch[2]);
            }
        }

        return $text;
    }

    static private function PQClearText($text, $array = array())
    {
        foreach ($array as $simbol)
        {
            $text = preg_replace('/'.$simbol.'/', '', $text);
        }

        $text = preg_replace('/\s{2,}/', '', $text);
        $text = preg_replace('/\n/', '', $text);

        return trim($text);
    }

    static private function PQTime($item)
    {
        $arTimes = array();
        $cols = $item->find('._worktime div');
        foreach ($cols as $col)
        {
            $col = pq($col);

            $day = $col->attr('data-day');
            $time = $col->text();
            $arTime = explode('до', $time);

            $arTimes[] = array(
                'DAY' => self::PQClearText($day),
                'FROM' => self::PQClearText($arTime[0], array('c')),
                'TO' => self::PQClearText($arTime[1], array('до'))
            );
        }

        $sTimes = '';

        $sSaveIndex = 0;

        for ($curIndex = 1; $curIndex <= count($arTimes); $curIndex++)
        {
            $arSaveItem = @$arTimes[$sSaveIndex];
            $arPrevItem = @$arTimes[$curIndex - 1];

            if ($arSaveItem['FROM'] != $arTimes[$curIndex]['FROM'])
            {
                $day = ($arSaveItem['DAY'] ==  $arPrevItem['DAY']) ?  $arPrevItem['DAY']: $arSaveItem['DAY'] .'-'. $arPrevItem['DAY'];
                $time = ($arSaveItem['FROM'] && $arSaveItem['TO']) ? 'с '.$arSaveItem['FROM'].' до '.$arSaveItem['TO']: $arSaveItem['FROM'];
                $sTimes .= mb_convert_case($day, MB_CASE_TITLE, 'UTF-8').' - '.$time.'; ';
                $sSaveIndex = $curIndex;
            }
        }

        return rtrim(trim($sTimes), ';');
    }

    static private function PQIsActive($item)
    {
        return ($item->find('._shipping ._row .mark:eq(2):contains("✓")')->text()) ? true: false;
    }
}