<?php
use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use \Bitrix\Main\Diag\Debug;
use \Ayers\Stores;
use \Ayers\Stores\StoresTable;

class StoresProduct extends CBitrixComponent
{
    private $arItems = array();

    protected function checkModules()
    {
        if (!Main\Loader::includeModule('ayers.stores'))
            throw new Main\LoaderException('Модуль ayers.stores не установлен');
    }

    function getListCityStores()
    {
        $filter = array('=CITY' => $this->arParams['CITY']);

        if ($this->arParams['IS_FILTER_TYPE'] == 'Y')
        {
            if (\Bitrix\Main\Loader::includeModule('ayers.delivery'))
            {
                $isInShops = \Ayers\Delivery\CalcPrice::IsInShops();

                if (!$isInShops)
                {
                    $optimalCompany = \Ayers\Delivery\CalcPrice::GetOptimalCompany4City($this->arParams['CITY']);
                    $filter['=TYPE'] = \Ayers\Delivery\CalcPrice::FormatOptimalCompany($optimalCompany);
                }
                else
                {
                    $filter[] = array(
                        'LOGIC' => 'OR',
                        array('=TYPE' => 'СДЭК'),
                        array('=TYPE' => 'ТекстильТорг')
                    );
                }
            }
        }

        $result = StoresTable::getList(array(
            'order' => array('SORT' => 'ASC', 'ADDRESS' => 'ASC'),
            'select' => array('*'),
            'filter' => $filter
        ));

        return $result;
    }

    function filterHintItems()
    {
        $result = array();

        $count = ($this->arParams['COUNT_IN_HINT'] <= count($this->arItems))?
            $this->arParams['COUNT_IN_HINT']:
            count($this->arItems);
        $offset = 0;
        for ($i = 0; $i < $count + $offset; $i++)
        {
            $item = $this->arItems[$i];

            if ($item['TYPE'] == 'ТекстильТорг')
            {
                $result['HOME'][] = $this->arItems[$i];
                $offset++;
            }
            else
            {
                $result['STORES'][] = $this->arItems[$i];
            }
        }

        return $result;
    }

    function sortItems()
    {
        $result = array();
        foreach ($this->arItems as $arItem)
        {
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

    function getGroupJson()
    {
        $arItems = $this->sortItems();

        if ($arItems['HOME'])
        {
            $items = array();
            foreach ($arItems['HOME'] as $arItem)
            {
                if (empty($arItem['POINTS']))
                {
                    continue;
                }

                $items[] = (object) array(
                    'center' => explode(',', $arItem['POINTS']),
                    'address' => preg_replace('/\'/', '"', $arItem['SHORT_ADDRESS']),
                    'time' => preg_replace('/\;/', '<br>', $arItem['TIME']),
                    'phone' => $arItem['PHONE'],
                    'hint' => 'Перед самовывозом необходимо забронировать товар.'
                );
            }

            $result[] = (object) array(
                'name' => 'Магазины',
                'preset' => (object) array(
                    'iconLayout' => 'default#image',
                    'iconImageHref' => '/bitrix/components/ayers/stores.product/templates/.default/images/mark.png',
                    'iconImageSize' => array(42, 42),
                    'iconImageOffset' => array(-21, -42)
                ),
                'style' => 'shop',
                'items' => $items
            );
        }

        if ($arItems['STORES'])
        {
            $items = array();
            foreach ($arItems['STORES'] as $arItem)
            {
                if (empty($arItem['POINTS']))
                {
                    continue;
                }

                $items[] = (object) array(
                    'center' => explode(',', $arItem['POINTS']),
                    'address' => preg_replace('/\'/', '"', $arItem['SHORT_ADDRESS']),
                    'time' => preg_replace('/\;/', '<br>', $arItem['TIME']),
                    'phone' => $arItem['PHONE'],
                    'hint' => '<span class="red">*</span> Уважаемые клиенты, обратите внимание на то, что демонстрация возможна только в магазинах, в пунктах выдачи демонстрация не производится.<br>Перед самовывозом необходимо забронировать товар.'
                );
            }

            $result[] = (object) array(
                'name' => 'Пункты выдачи <span class="red">*</span>',
                'preset' => 'islands#blueIcon',
                'style' => 'stores',
                'items' => $items
            );
        }

        return json_encode($result);
    }

    public function executeComponent()
    {
        $this->includeComponentLang('class.php');

        $this->checkModules();

        $this->arItems = $this->getListCityStores()->fetchAll();

        $this->arResult['CITY'] = $this->arParams['CITY'];
        $this->arResult['ITEMS'] = $this->sortItems();
        $this->arResult['ITEMS_HINT'] = $this->filterHintItems();
        $this->arResult['IS_BUTTON_POPUP'] = (count($this->arItems) > $this->arParams['COUNT_IN_HINT']);
        $this->arResult['MAP_GROUP'] = $this->getGroupJson();

        $this->includeComponentTemplate();
    }
};