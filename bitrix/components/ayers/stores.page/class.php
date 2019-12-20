<?php
use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use \Bitrix\Main\Diag\Debug;
use \Ayers\Stores;
use \Ayers\Stores\StoresTable;

class StoresPage extends CBitrixComponent
{
    private $arItems = array();

    protected function checkModules()
    {
        if (!Main\Loader::includeModule('ayers.stores'))
            throw new Main\LoaderException('Модуль ayers.stores не установлен');
    }

    function getListCityStores()
    {
        $filter = array('=CITY' => $this->arParams['CITY']); // Show single city

        if (\Bitrix\Main\Loader::includeModule('ayers.delivery'))
        {
            $isInShops = \Ayers\Delivery\CalcPrice::IsInShops();

            if ($isInShops)
            {
                $filter[] = array(
                    'LOGIC' => 'OR',
                    array('=TYPE' => 'СДЭК'),
                    array('=TYPE' => 'ТекстильТорг')
                );
            }
        }

        $result = StoresTable::getList(array(
            'order' => array('SORT' => 'ASC', 'ADDRESS' => 'ASC'),
            'select' => array('*'),
            'filter' => $filter
        ));

        return $result;
    }

    function formatTime()
    {
        foreach ($this->arItems as $nItem => $arItem)
        {
            $arItem['TIME'] = explode(';', $arItem['TIME']);
            $this->arItems[$nItem] = $arItem;
        }
    }

    function sortItems()
    {
        $result = array();
        foreach ($this->arItems as $arItem)
        {
            //\Bitrix\Main\Diag\Debug::dumpToFile($arItem, "", "stores-log.log");
            if ($arItem['TYPE'] == 'ТекстильТорг')
            {
                $result['HOME'][] = $arItem;
            }
            else
            {
                $result['STORES'][] = $arItem;
            }
        }

        return $result;
    }

    public function executeComponent()
    {
        $this->includeComponentLang('class.php');

        $this->checkModules();

        $this->arItems = $this->getListCityStores()->fetchAll();

        $this->formatTime();

        $this->arResult['CITY'] = $this->arParams['CITY'];
        $this->arResult['COUNT_SHOW'] = (!empty($this->arParams['COUNT_SHOW']))? $this->arParams['COUNT_SHOW']: 3;
        $this->arResult['ITEMS'] = $this->sortItems();

        $this->includeComponentTemplate();
    }
};