<?
namespace Bxmaker\SmsNotice;


use Bitrix\Main\Application;
use \Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Loader;


Loc::loadMessages(__FILE__);



Class ServiceTable extends Entity\DataManager
{

    public static
    function getFilePath()
    {
        return __FILE__;
    }

    public static
    function getTableName()
    {
        return 'bxmaker_smsnotice_service';
    }

    public static
    function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary'      => true,
                'autocomplete' => true
            )),
            new Entity\StringField('CODE', array(
                'required' => true
            )),
            new Entity\StringField('NAME', array(
                'required' => true
            )),
            new Entity\StringField('SITE_ID', array(
                'required'  => true,
                'validator' => function () {
                    return array(
                        new Entity\Validator\Range(2, 2)
                    );
                }
            )),
            new Entity\BooleanField('ACTIVE', array(
                'required' => true
            )),
            new Entity\StringField('PARAMS', array(
                'serialized' => true
            ))
        );
    }
}

class Service
{

    static private $arServiceList = array();
    static private $arInstances = array();

    static public $moduleId = 'bxmaker.smsnotice';



    public function getArray()
    {

        if (empty(self::$arServiceList)) {

            $arSort = array(
                'sms' => 100,
                'smsc' => 200,
                'bytehand' => 300,
                'mainsms' => 400,
                'sms48' => 500,
                'streamtelecom' => 600,
                'kolibrisms' => 500,
                'sms_fly_com' => 700
            );

            // из модуля
            $path = \Bitrix\Main\Loader::getLocal('modules/bxmaker.smsnotice/lib/service');
            if ($path && is_dir($path) && $d = opendir($path)) {
                while ($file = readdir($d)) {
                    if ($file == '.' || $file == '..' || preg_match('#^\.#', $file)) continue;

                    $code = preg_replace('/\.php$/', '', $file);

                    self::$arServiceList[$code] = array(
                        'CODE' => $code,
                        'FILE' => $path . '/' . $file,
                        'SORT' => (isset($arSort[$code]) ? $arSort[$code] : 5000),
                        'NAME'=> GetMessage(self::$moduleId . '.SERVICE.NAME_' . $code)
                    );

                }
                closedir($d);
            }

            // из php_interface
            $path = \Bitrix\Main\Loader::getLocal('modules/php_interface/bxamaker.smsnotice');
            if ($path && is_dir($path) && $d = opendir($path)) {
                while ($file = readdir($d)) {
                    if ($file == '.' || $file == '..') continue;

                    $code = preg_replace('/\.php$/', '', $file);

                    self::$arServiceList[$code] = array(
                        'CODE' => $code,
                        'FILE' => $path . '/' . $file,
                        'SORT' => (isset($arSort[$code]) ? $arSort[$code] : 5000),
                        'NAME' => $code
                    );

                }
                closedir($d);
            }

            // отсортируем
            uasort(self::$arServiceList, array($this, 'uasortServiceLsit'));
        }

        return self::$arServiceList;
    }

    private function uasortServiceLsit($a, $b){
        if($a['SORT'] == $b['SORT']) return 0;
        else return ($a['SORT'] > $b['SORT'] ? 1 : -1);
    }

    /**
     * @param      $service_code
     * @param null $arParams
     *
     * @return Service
     */
    static function getInstance($service_code, $arParams = null)
    {
        if (empty(self::$arServiceList)) {
            self::getList();
        }

        if (!isset(self::$arServiceList[$service_code])) {
            return array();
        }

        if (isset(self::$arInstances[$service_code])) {
            return self::$arInstances[$service_code];
        }

        require_once(self::$arServiceList[$service_code]['FILE']);

        $c = '\Bxmaker\SmsNotice\Service\\' . self::$arServiceList[$service_code]['CODE'];

        self::$arInstances[$service_code] = new $c($arParams);

        return self::$arInstances[$service_code];

    }

    /**
     * @param $service_code
     * return array();
     */
    public function getParams()
    {

        return array();

//        return array(
//            'SENDER' => array(
//                'NAME' => GetMessage('SMSSERVICE_SENDER'),
//                'TYPE' => 'STRING',
//                'DEFAULT_VALUE' => ''
//            ),
//            'TEST_MODE' => array(
//                'NAME' => '',
//                'TYPE' => 'CHECKBOX',
//                'DEFAULT_VALUE' => 'N' // OR Y
//            ),
//            'TMP_LIST' => array(
//                'NAME' => '',
//                'TYPE' => 'LIST',
//                'VALUES' => array(
//                    'VALUE' => GetMessage('VALUE_NAME'),
//                    'VALUE1' => GetMessage('VALUE1_NAME')
//                ),
//                'DEFAULT_VALUE' => 'VALUE1'
//            )
//        );

    }

    public function send($phone, $text, $arParams = array())
    {
        return new Result(new Error(GetMessage(self::$moduleId . '.SERVICE.SEND_UNKNOWN')));
    }

    public function getBalance()
    {
        return new Result(new Error(GetMessage(self::$moduleId . '.SERVICE.GET_BALANCE_UNKNOWN')));
    }

    /**
     * Метод должен быть переопределен в наследнике,  для обработки входящего опвещения о статусе сообщения
     * @return Result
     */
    public function notice()
    {
        return new Result(new Error('METHOD_NOT_DEFINED'));
    }

    public function agent($arSmsId = array())
    {
        return new Result(new Error('METHOD_NOT_DEFINED'));
    }

    public function getDescription()
    {
        return '';
    }

}
