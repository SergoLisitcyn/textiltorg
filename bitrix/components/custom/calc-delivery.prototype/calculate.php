<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$isXHR = strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$callback = isset($_GET['callback']) ? preg_replace('/[^a-z0-9$_]/si', '', $_GET['callback']) : false;

header('Access-Control-Allow-Origin: *');
header('Content-type: application/' . ($isXHR ? 'json' : 'x-javascript'));

use \Bitrix\Main\Loader;
use \Ayers\Delivery\CalcPrice;
use \Ayers\Delivery\Cdek;
use \Ayers\Delivery\Pecom;

if (Loader::includeModule('ayers.delivery'))
{
    $arJson = json_decode($_REQUEST['json'], true);

    if (!empty($arJson['goods']))
    {
        $arItems = array(
            array(
                'CATALOG_WEIGHT' => $arJson['goods'][0]['weight'] * 1000,
                'CATALOG_WIDTH' => $arJson['goods'][0]['width'] * 1000,
                'CATALOG_HEIGHT' => $arJson['goods'][0]['height'] * 1000,
                'CATALOG_LENGTH' => $arJson['goods'][0]['length'] * 1000
            )
        );

        $optimalCompany = CalcPrice::GetOptimalCompany4City($arJson['region']);

        if ($optimalCompany)
        {
            switch ($optimalCompany) {
                case 'CDEK':
                    $city = Cdek::GetCodeCity($arJson['region']);
                    $places = Cdek::Items2Places($arItems);
                    $result = Cdek::Calc($city, $places, true);
                    break;

                default:
                    $city = Pecom::GetCodeCity($arJson['region']);
                    $places = Pecom::Items2Places($arItems);
                    $result = Pecom::Calc($city, $places, true);
                    break;
            }

            if ($result['STANDART']['PRICE'])
            {
                $result['CALC_STANDART'] = (!empty($result['STANDART']['DELIVER']))? $result['STANDART']['DELIVER'] : $result['STANDART']['PRICE'];
                $result['CALC_STANDART'] = ($result['CALC_STANDART'] > 300)? $result['CALC_STANDART']: 300;
            }

            if ($result['EXPRESS']['PRICE'])
            {
                $result['CALC_EXPRESS'] = (!empty($result['EXPRESS']['DELIVER']))? $result['EXPRESS']['DELIVER'] : $result['EXPRESS']['PRICE'];
                $result['CALC_EXPRESS'] = ($result['CALC_EXPRESS'] > 300)? $result['CALC_EXPRESS']: 300;
            }

            \Bitrix\Main\Diag\Debug::dumpToFile($result);

            if ($result['CALC_STANDART'] > 0 || $result['CALC_EXPRESS'] > 0)
            {
                $data = array(
                    'result' => $result
                );
            }
            else
            {
                $data = array('error' => 'Для выбранного города уточняйте информацию у менеджера.');
            }
        }
        else
        {
            $data = array('error' => 'Для выбранного города нет возможности рассчитать доставку. Уточняйте информацию у менеджера.');
        }
    }
    else
    {
        $data = array('error' => 'Ошибка расчета доставки, не указаны товары');
    }
}
else
{
    $data = array('error' => 'Не подключен модуль ayers.delivery');
}

echo ($callback ? $callback . '(' : '') . json_encode($data) . ($callback ? ')' : '');
?>