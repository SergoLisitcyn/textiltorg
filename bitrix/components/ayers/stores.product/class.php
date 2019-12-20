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

    function getListCityStores($isInShops = NULL)
    {
        //$filter = array('=CITY' => $this->arParams['CITY']); // Show single city

        if ($this->arParams['IS_FILTER_TYPE'] == 'Y')
        {
            if (\Bitrix\Main\Loader::includeModule('ayers.delivery'))
            {
                if ($isInShops === NULL) {
                    $isInShops = \Ayers\Delivery\CalcPrice::IsInShops();
                }

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
        $tmp = array();
        $result = array();

        //$count = ($this->arParams['COUNT_IN_HINT'] <= count($this->arItems)) ? $this->arParams['COUNT_IN_HINT'] : count($this->arItems);
        $offset = 0;
        for ($i = 0; $i < count($this->arItems) + $offset; $i++)
        {
            $item = $this->arItems[$i];
            if ($item['TYPE'] == 'ТекстильТорг' && $item['CITY'] == $this->arParams['CITY']) {
                $result['HOME'][] = $this->arItems[$i];
                $offset++;
            }
            elseif ($item['CITY'] == $this->arParams['CITY']) {
                $tmp[] = $this->arItems[$i];
            }
        }
        $result['STORES'] = array_slice($tmp, 0, $this->arParams['COUNT_IN_HINT']);

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
        if(PAGE_FOLDER == "/cart/") {
            $btn = '<div class="red-btn2" onclick="chose_point();return false;">забрать тут</div><br><br>';
        } else {
            $btn = '';
        }

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

                $tmp = '<b class="tmpAddress" style="display: none">' . preg_replace('/\'/', '"', $arItem['SHORT_ADDRESS']) . '</b>';

                $items[] = (object) array(
                    'city' => $arItem['CITY'],
                    'center' => explode(',', $arItem['POINTS']),
                    'address' => preg_replace('/\'/', '"', $arItem['SHORT_ADDRESS']),
                    'metro' => ($arItem['METRO'] == null) ? '' : $arItem['METRO'],
                    'time' => preg_replace('/\;/', '<br>', $arItem['TIME']),
                    'phone' => $arItem['PHONE'],
                    'hint' => '<span class="red">*</span> Самовывоз из магазина – это лучший вариант. В нём мы проведём бесплатный мастер класс по демонстрации функционала модели, а квалифицированный персонал ответит на любые вопросы и при необходимости научат основам работы со швейной техникой. <br> Перед самовывозом необходимо забронировать товар.' . $btn . $tmp
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

                $tmp = '<b class="tmpAddress" style="display: none">' . preg_replace('/\'/', '"', $arItem['SHORT_ADDRESS']) . '</b>';

                $items[] = (object) array(
                    'city' => $arItem['CITY'],
                    'center' => explode(',', $arItem['POINTS']),
                    'address' => preg_replace('/\'/', '"', $arItem['SHORT_ADDRESS']),
                    'metro' => ($arItem['METRO'] == null) ? '' : $arItem['METRO'],
                    'time' => preg_replace('/\;/', '<br>', $arItem['TIME']),
                    'phone' => $arItem['PHONE'],
                    'hint' => '<span class="red">*</span> Перед самовывозом необходимо забронировать товар.' . $btn . $tmp
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

    function getStoresData($arParams)
    {
        $this->arParams['CITY'] = $arParams['city'];
        $this->arParams['IS_FILTER_TYPE'] = $arParams['is_filter_type'];
        $this->arParams['COUNT_IN_HINT'] = $arParams['count_in_hint'];
        $this->arParams['CACHE_TIME'] = $arParams['cache_time'];
        $this->arParams['CACHE_TYPE'] = $arParams['cache_type'];

        $this->arItems = $this->getListCityStores()->fetchAll();

        return array(
            'CITY' => $this->arParams['CITY'],
            'ITEMS' => $this->sortItems(),
            'ITEMS_HINT' => $this->filterHintItems(),
            'IS_BUTTON_POPUP' => (count($this->arItems) > $this->arParams['COUNT_IN_HINT']),
            'MAP_GROUP' => $this->getGroupJson()
        );
    }

    function getStoresCount()
    {
        foreach ($this->arItems as $key => $item) {
            if ($GLOBALS['GEO_REGION_CITY_NAME'] == $item["CITY"]) {
                $targetCities[] = $item["CITY"];
            }
        }
        return count($targetCities);
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

        $pointCount = $this->getStoresCount();
        if (substr($pointCount, -1) == 1) {
            $pointText = "точка продаж";
        } elseif (in_array(substr($pointCount, -1), array(2,3,4))) {
            $pointText = "точки продаж";
        } else {
            $pointText = "точек продаж";
        }

        $GLOBALS["ITEMS_STORE_COUNT_TEXT"] = $pointText;
        $GLOBALS["ITEMS_STORE_COUNT"] = $pointCount;

        $this->includeComponentTemplate();
    }
};