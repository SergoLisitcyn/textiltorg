<?php
use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use \Bitrix\Main\Diag\Debug;
use \Ayers\Delivery\CalcPrice;
use \Bitrix\Main\Config\Option;

class StoresMap extends CBitrixComponent
{
    private $arItems = array();

    protected function checkModules()
    {
        if (!Main\Loader::includeModule('ayers.delivery'))
            return false;
    }

    public static function getUserIP()
    {
        if ($_SERVER["HTTP_X_FORWARDED_FOR"])
        {
            $clientIP = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else
        {
            $clientIP = $_SERVER["HTTP_CLIENT_IP"];
        }

        $clientProxy = $_SERVER["REMOTE_ADDR"];
        if(strlen($clientIP) <= 0)
        {
            $clientIP = $clientProxy;
        }

        return $clientIP;
    }

    private function isShowInfo()
    {
        $ip = Option::get('ayers.delivery', 'IP_MANAGERS', '');
        $ips = explode(',', $ip);

        foreach ($ips as $ip)
        {
            if ($this->getUserIP() == trim($ip))
            {
                return true;
                break;
            }
        }

        return false;
    }

    private function getInfo()
    {
        $result = array();
        $company = CalcPrice::GetOptimalCompany4City($this->arParams['CITY']);
        $company = CalcPrice::FormatOptimalCompany($company);
        $company = (!empty($company))? $company: 'Нет данных';

        $result[] = array(
            'TITLE' => 'Расчет доставки от компании',
            'VALUE' => $company
        );

        return $result;
    }

    public function executeComponent()
    {
        if (Main\Loader::includeModule('ayers.delivery'))
        {
            $isInShops = CalcPrice::IsInShops();

            if (!$isInShops)
            {
                $this->includeComponentLang('class.php');

                $this->checkModules();

                $this->arResult['CITY'] = $this->arParams['CITY'];
                $this->arResult['IP'] = $this->getUserIP();

                if ($this->isShowInfo())
                {
                    $this->arResult['INFO'] = $this->getInfo();
                }

                $this->includeComponentTemplate();
            }
        }
    }
};