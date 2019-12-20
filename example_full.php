<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

// Подключение модуля инфоблоки
// Документация: http://dev.1c-bitrix.ru/api_help/iblock/index.php
CModule::IncludeModule("iblock");

$arVendorCodes = array("100924");

$arGuarantee = GetAllGuarantee();

$rsElements = CIBlockElement::GetList(
    // SORT: Сортировка по идентификатору
    array(
        "ID" => "ASC"
    ),
    // FILTER: Фильтр по инфоблоку каталога товаров (8) и массиву с артикулами
    array(
        "IBLOCK_ID" => 8,
        "PROPERTY_VENDOR_CODE" => $arVendorCodes
    ),
    false,
    false,
    // SELECT: Выборка полей
    array(
        "ID",                     // идентификатор товара
        "IBLOCK_ID",              // инфоблок елемента
        "NAME",                   // название товара
        "IBLOCK_SECTION_ID",      // идентификатор раздела для товара
        "PROPERTY_VENDOR_CODE",   // свойство: артикул
        "PROPERTY_CATEGORY_TYPE", // свойство: тип категории
        "PROPERTY_BRAND",         // свойство: бренд (выбирается идентификатор для справочника)
        "PROPERTY_MODEL",         // свойство: модель
        "PROPERTY_MODEL_NAME",    // свойство: модель
        "PROPERTY_SHORT_NAME",    // свойство: короткое название,
        ///
        "PROPERTY_PAYMENT_INSTALLMENTS", // свойство: рассрочка,
        "PROPERTY_DELIVERY",      // свойство: доставка (платная, бесплатная, бесплатная по РФ),
        "PROPERTY_GUARANTEE",     // свойство: гарантия
        "DETAIL_PAGE_URL",        // детальная страница товара (можно заменить через preg_raplace, запросив код элемента (CODE) или через регулярное выражение)
        "CATALOG_GROUP_1",        // цена Москва
        "CATALOG_GROUP_2",        // цена Санкт-Петербург
        "CATALOG_GROUP_4",        // цена Екатеринбург
        "CATALOG_GROUP_5",        // цена Нижний Новгород
        "CATALOG_GROUP_6",        // цена Ростов-на-Дону
        "CATALOG_GROUP_7",        // цена Минск
        "CATALOG_GROUP_9",        // цена MRC (если цена есть, то true, если нет, то выставляем false)
    )
);

// Fetch заменили на GetNext (для вывода детальной страницы)
while ($arElement = $rsElements->GetNext())
{
    $arElement["PROPERTY_GUARANTEE_VALUE"] = GetGuarantee($arElement["PROPERTY_GUARANTEE_VALUE"], $arGuarantee);

    echo "<pre>";
    var_dump($arElement);
    echo "</pre>";
}

// получить все гарантии (гарантии можно покупать, поэтому они как товары. используется привязка)
function GetAllGuarantee()
{
    $rsElements = CIBlockElement::GetList(
        array(
            "ID" => "ASC"
        ),
        array(
            "IBLOCK_ID" => 9
        ),
        false,
        false,
        array(
            "ID",
            "NAME"
        )
    );

    $arElements = array();
    while ($arElement = $rsElements->GetNext())
    {
        $arElements[$arElement["ID"]] = $arElement;
    }

    return $arElements;
}

// Выборка из массива гарантий
function GetGuarantee($idGuarantee, $arGuarantee)
{
    return $arGuarantee[$idGuarantee];
}