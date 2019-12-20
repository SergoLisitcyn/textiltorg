<?
ini_set('max_execution_time', 1500);
use \Bitrix\Main;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use \Ayers\Stores;

// подключим все необходимые файлы:
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//
require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/ayers.stores/include.php'); // инициализация модуля
require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/ayers.stores/prolog.php'); // пролог модуля

Loader::includeModule('ayers.stores');
Loc::loadMessages(__FILE__);

$data = array_merge(
    \Ayers\Stores\DataCDEK::GetData(),
    \Ayers\Stores\DataPecom::GetData()
);

$result = \Ayers\Stores\Data::Save($data);

if ($result)
{
    echo 'Successful';
    error_log(date("Y-m-d H:i:s") . " Successful. \n", 3, dirname(__FILE__) . "result.log");
}
else
{
    echo 'Error';
    error_log(date("Y-m-d H:i:s") . " Error. \n", 3, dirname(__FILE__) . "result.log");
}