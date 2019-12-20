<?php
namespace Ayers\Delivery;
use \Bitrix\Main\Type;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Diag\Debug;
use \Bitrix\Main\Data\Cache;
use \Ayers\Delivery\Pecom;
use \Ayers\Delivery\Cdek;
use \Ayers\Stores\StoresTable;
use \Bitrix\Catalog\ProductTable;

Loader::includeModule('ayers.stores');
Loader::includeModule('iblock');

class CalcPrice {

    private static $arRegionMsk = array("Домодедово","Железнодорожный","Бронницы","Жуковский","Дубна","Королев","Ивантеевка","Звенигород","Климовск","Реутов","Краснознаменск","Лобня","Лыткарино","Протвино","Пущино","Фрязино","Электросталь","Красноармейск","Рошаль","Орехово-Зуево","Юбилейный","Дзержинский","Коломна","Подольск","Долгопрудный","Химки","Серпухов","Балашиха","Городской округ Черноголовка","Электрогорск","Котельники","Лосино-Петровский","Волоколамск","Воскресенск","Яхрома","Дмитров","Егорьевск","Зарайск","Дедовск","Истра","Истра-1","Снегири","Кашира","Кашира-8","Ожерелье","Клин","Высоковск","Красногорск","Видное","Луховицы","Люберцы","Можайск","Мытищи","Наро-Фоминск","Верея","Апрелевка","Ногинск","Черноголовка","Старая Купавна","Электроугли","Кубинка","Голицыно","Одинцово","Дрезна","Ликино-Дулево","Куровское","Озеры","Павловский Посад","Пушкино","Раменское","Руза","Краснозаводск","Сергиев Посад","Хотьково","Пересвет","Сергиев Посад-7","Солнечногорск","Солнечногорск-7","Солнечногорск-30","Солнечногорск-25","Солнечногорск-2","Ступино","Талдом","Чехов-2","Чехов","Чехов-3","Чехов-8","Шатура","Щелково");
    private static $arRegionSpb = array("Волосово","Бокситогорск","Пикалево","Сосновый Бор","Волхов","Новая Ладога","Сясьстрой","Сертолово","Всеволожск","Выборг","Каменногорск","Высоцк","Приморск","Светогорск","Коммунар","Гатчина","Ивангород","Кингисепп","Кириши","Шлиссельбург","Кировск","Отрадное","Лодейное Поле","Луга","Подпорожье","Приозерск","Сланцы","Тихвин","Любань","Никольское","Тосно");
    private static $arRegionEkb = array("Березовский","Асбест","Верхняя Пышма","Карпинск","Заречный","Ивдель","Краснотурьинск","Кировград","Качканар","Красноуральск","Лесной","Кушва","Новоуральск","Нижняя Тура","Первоуральск","Североуральск","Полевской","Ревда","Нижний Тагил","Каменск-Уральский","Ирбит","Красноуфимск","Алапаевск","Серов","Камышлов","Нижняя Салда","Волчанск","Среднеуральск","Верхний Тагил","Дегтярск","Тавда","Верхняя Тура","Артемовский","Богданович","Верхняя Салда","Верхотурье","Невьянск","Нижние Серги-3","Нижние Серги","Михайловск","Новая Ляля","Реж","Сухой Лог","Арамиль","Сысерть","Талица","Туринск");
    private static $arRegionRnd = array("Волгодонск","Батайск","Гуково","Зверево","Каменск-Шахтинский","Донецк","Новочеркасск","Новошахтинск","Шахты","Таганрог","Азов","Аксай","Белая Калитва","Зерноград","Красный Сулин","Константиновск","Миллерово","Морозовск","Пролетарск","Сальск","Семикаракорск","Цимлянск");
    private static $arRegionNn = array("Дзержинск","Бор","Саров","Арзамас","Семенов","Выкса","Первомайск","Шахунья","Балахна","Богородск","Ветлуга","Володарск","Городец","Заволжье","Княгинино","Кстово","Лукоянов","Кулебаки","Лысково","Павлово","Ворсма","Горбатов","Навашино","Перевоз","Сергач","Урень","Чкаловск");

    static public function GetOptimalDelivery4Items($dataCompany = 'PECOM', $dataCity = '', $dataPrice = 0, $dataItems = array(), $isDetail = false)
    {

        if (empty($dataCity) || empty($dataItems) || empty($dataPrice)) // || $dataCompany === NULL
        {
            return false;
        }

        // exceptions
        $isNotDPrice = false;
        foreach ($dataItems as $dataItem)
        {
            if ($dataItem['ID'])
            {
                if (isset($dataItem['PROPERTIES']['IS_NOT_D_PRICE']['VALUE']) && $dataItem['IBLOCK_SECTION_ID'] && $dataItem['IBLOCK_ID'])
                {
                    if ($dataItem['PROPERTIES']['DELIVERY']['VALUE'] == 'Бесплатная по РФ')
                    {
                        $isNotDPrice = true;
                    }

                    if (intval($dataItem['PROPERTIES']['IS_NOT_D_PRICE']['VALUE']))
                    {
                        $isNotDPrice = true;
                    }

                    if ($isNotDPriceSection = self::IsNotDPriceSection($dataItem['IBLOCK_ID'], $dataItem['IBLOCK_SECTION_ID']))
                    {
                        $isNotDPrice = true;
                    }
                }
                else
                {

                    $arElementProductID = \CCatalogSku::GetProductInfo($dataItem['ID']);
                    $elementProductID = (is_array($arElementProductID)) ? $arElementProductID['ID'] : $dataItem['ID'];

                    $rsElements = \CIBlockElement::GetList(
                        array(),
                        array(
                            'ID' => $elementProductID
                        ),
                        false,
                        false,
                        array('ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'PROPERTY_IS_NOT_D_PRICE', 'PROPERTY_DELIVERY')
                    );

                    if ($arElement = $rsElements->GetNext())
                    {
                        if ($arElement['PROPERTY_DELIVERY_VALUE'] == 'Бесплатная по РФ')
                        {
                            $isNotDPrice = true;
                        }

                        if (intval($arElement['PROPERTY_IS_NOT_D_PRICE_VALUE']))
                        {
                            $isNotDPrice = true;
                        }

                        if ($isNotDPriceSection = self::IsNotDPriceSection($arElement['IBLOCK_ID'], $arElement['IBLOCK_SECTION_ID']))
                        {
                            $isNotDPrice = true;
                        }
                    }
                }
            }
        }

        // calc
        switch ($dataCompany) {
            case 'CDEK':
                $city = Cdek::GetCodeCity($dataCity);
                $places = Cdek::Items2Places($dataItems);
                $delivery = Cdek::Calc($city, $places, $isDetail);
                break;

            case 'PECOM':
                $city = Pecom::GetCodeCity($dataCity);
                $places = Pecom::Items2Places($dataItems);
                $delivery = Pecom::Calc($city, $places, $isDetail);
                break;

            default:
                $city = Pecom::GetCodeCity($dataCity);
                $places = Pecom::Items2Places($dataItems);
                $delivery = Pecom::Calc($city, $places, $isDetail);
                break;

        }

        $price = (float) preg_replace('/[^\d\.]/', '', $dataPrice);

        if ($price > 0 && !$isNotDPrice)
        {
            $price += $delivery['STANDART']['PRICE'];
        }

        if ($mainCity = self::checkRegionCity($dataCity)) {
            $price = self::overrideRegionCityPrice($mainCity, $dataItem['ID']);
        }

        $result = array(
            'PRICE' => array(
                'DEFAULT' => $price,
                'FORMAT' => self::Format($price),
                'PRINT' => self::Format($price, 'руб.')
            ),
            'DELIVERY' => $delivery
        );

        return $result;
    }

    static function checkRegionCity($dataCity)
    {
        if (in_array($dataCity, static::$arRegionMsk)) {
            return 'Москва';
        } elseif (in_array($dataCity, static::$arRegionSpb)) {
            return 'Санкт-Петербург';
        } elseif (in_array($dataCity, static::$arRegionEkb)) {
            return 'Екатеринбург';
        } elseif (in_array($dataCity, static::$arRegionRnd)) {
            return 'Ростов-на-Дону';
        } elseif (in_array($dataCity, static::$arRegionNn)) {
            return 'Нижний Новгород';
        } else {
            return false;
        }
    }

    static function overrideRegionCityPrice($mainCity, $dataItemID)
    {
        $rsPrices = \CPrice::GetList(
            array(),
            array(
                "PRODUCT_ID" => $dataItemID,
                "CATALOG_GROUP_NAME" => $mainCity
            )
        );
        $arPrice = $rsPrices->Fetch();
        return $arPrice['PRICE'];
    }

    static function Format($dataPrice = 0, $dataCurreny = '')
    {
        return trim(number_format($dataPrice, 0, '.', ' ').' '.$dataCurreny);
    }

    static function GetOptimalCompany4City($dataCity = '')
    {
        $company = 'PECOM';

        if (empty($dataCity))
        {
            return $company;
        }

        $сache = Cache::createInstance();

        if ($сache->initCache(3600000, $dataCity, 'ayers.delivery/calc/company'))
        {
            $company = $сache->getVars();
        }
        elseif ($сache->startDataCache())
        {
            $store = StoresTable::getList(array(
                'select' => array('ID'),
                'filter' => array(
                    '=CITY' => $dataCity
                ),
                'limit' => 1
            ))->fetch();



            if (empty($store))
            {
                $company = NULL;
            }
            else
            {
                $cityCdek = Cdek::GetCodeCity($dataCity);
                $deliveryCdek = Cdek::Calc(
                    $cityCdek,
                    array(
                        array(
                            'weight' => Cdek::DEFAULT_WEIGHT,
                            'volume' => Cdek::DEFAULT_VOLUME
                        )
                    )
                );

                $cityPecom = Pecom::GetCodeCity($dataCity);
                $deliveryPecom = Pecom::Calc(
                    $cityPecom,
                    array(
                        array(
                            0,
                            0,
                            0,
                            Pecom::DEFAULT_VOLUME,
                            Pecom::DEFAULT_WEIGHT
                        )
                    )
                );

                if ($deliveryCdek['STANDART']['PRICE'] < $deliveryPecom['STANDART']['PRICE'])
                {
                    $company = 'CDEK';
                }

                $store = StoresTable::getList(array(
                    'select' => array('ID'),
                    'filter' => array(
                        '=CITY' => $dataCity,
                        '=TYPE' => ($company == 'CDEK')? 'СДЭК': 'ПЭК'
                    ),
                    'limit' => 1
                ))->fetch();

                if (empty($store))
                {
                    $company = ($company == 'CDEK')? 'PECOM': 'CDEK';
                }
            }

            $сache->endDataCache($company);
        }

        return $company;
    }

    static function FormatOptimalCompany($dataCompany = '')
    {
        switch ($dataCompany) {
            case 'PECOM':
                return 'ПЭК';
                break;

            case 'CDEK':
                return 'СДЭК';
                break;
        }

        return false;
    }

    static public function IsInShops()
    {
        $arShops = array(
            'Москва',
            'Санкт-Петербург',
            'Нижний Новгород',
            'Ростов-на-Дону',
            'Екатеринбург',
			'Новосибирск',
			'Казань'
        );

        return (in_array($_SESSION['GEO_REGION_CITY_NAME'], $arShops)); // // Включение калькуляции цен с сервисами ПЭК и СДЭК.
        //return 1; // Отключение калькуляции цен с сервисами ПЭК и СДЭК.
    }

    static public function IsNotDPriceSection($iblock, $id)
    {
        if (empty($iblock) || empty($id))
        {
            return false;
        }

        $сache = Cache::createInstance();

        if ($сache->initCache(36000, 'IsNotDPriceSection'.md5($iblock.'||'.$id), 'ayers.delivery/sections'))
        {
            $result = $сache->getVars();
        }
        elseif ($сache->startDataCache())
        {
            $result = false;

            $rsSection = \CIBlockSection::GetList(
                array(),
                array(
                    'ID' => $id,
                    'IBLOCK_ID' => $iblock
                ),
                false,
                array('ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'UF_IS_NOT_D_PRICE')
            );

            if ($arSection = $rsSection->GetNext())
            {
                if (intval($arSection['UF_IS_NOT_D_PRICE']))
                {
                    $result = true;
                }
                elseif ($arSection['IBLOCK_SECTION_ID'])
                {
                    $result = self::IsNotDPriceSection($iblock, $arSection['IBLOCK_SECTION_ID']);
                }
            }

            $сache->endDataCache($result);
        }

        return $result;
    }
}

?>