<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (isset($arParams["TEMPLATE_THEME"]) && !empty($arParams["TEMPLATE_THEME"]))
{
	$arAvailableThemes = array();
	$dir = trim(preg_replace("'[\\\\/]+'", "/", dirname(__FILE__)."/themes/"));
	if (is_dir($dir) && $directory = opendir($dir))
	{
		while (($file = readdir($directory)) !== false)
		{
			if ($file != "." && $file != ".." && is_dir($dir.$file))
				$arAvailableThemes[] = $file;
		}
		closedir($directory);
	}

	if ($arParams["TEMPLATE_THEME"] == "site")
	{
		$solution = COption::GetOptionString("main", "wizard_solution", "", SITE_ID);
		if ($solution == "eshop")
		{
			$templateId = COption::GetOptionString("main", "wizard_template_id", "eshop_bootstrap", SITE_ID);
			$templateId = (preg_match("/^eshop_adapt/", $templateId)) ? "eshop_adapt" : $templateId;
			$theme = COption::GetOptionString("main", "wizard_".$templateId."_theme_id", "blue", SITE_ID);
			$arParams["TEMPLATE_THEME"] = (in_array($theme, $arAvailableThemes)) ? $theme : "blue";
		}
	}
	else
	{
		$arParams["TEMPLATE_THEME"] = (in_array($arParams["TEMPLATE_THEME"], $arAvailableThemes)) ? $arParams["TEMPLATE_THEME"] : "blue";
	}
}
else
{
	$arParams["TEMPLATE_THEME"] = "blue";
}

$arParams["FILTER_VIEW_MODE"] = (isset($arParams["FILTER_VIEW_MODE"]) && toUpper($arParams["FILTER_VIEW_MODE"]) == "HORIZONTAL") ? "HORIZONTAL" : "VERTICAL";
$arParams["POPUP_POSITION"] = (isset($arParams["POPUP_POSITION"]) && in_array($arParams["POPUP_POSITION"], array("left", "right"))) ? $arParams["POPUP_POSITION"] : "left";

//props help
$SECTION_ID = intval($arParams["HELP_SECTION_ID"]);

CModule::IncludeModule("highloadblock");
use Bitrix\Highloadblock as HL;
$hlBlock = HL\HighloadBlockTable::getById(7)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlBlock);

$entityDataClass = $entity->getDataClass();
$entityTableName = $hlBlock["TABLE_NAME"];

$tableID = 'tbl_'.$entityTableName;

$arFilter = array();
if ($SECTION_ID)
{
    $arFilter = array(
        "LOGIC" => "OR",
        array("UF_SECTION" => $SECTION_ID),
        array("UF_SECTION" => "")
    );
}

$rsData = $entityDataClass::getList(array(
    "select" => array("ID", "UF_CODE", "UF_SECTION"),
    "filter" => $arFilter,
    "order" => array(
        "UF_NAME" => "ASC"
    )
));

$rsData = new CDBResult($rsData, $tableID);

while ($arRes = $rsData->Fetch())
{
    foreach ($arResult["ITEMS"] as $nProp => $arProp)
    {
        if ($arProp["CODE"] == $arRes["UF_CODE"])
        {
            $arProp["HELP_ID"] = $arRes["ID"];
            $arResult["ITEMS"][$nProp] = $arProp;
        }
    }
}

// Пересортируем выводимые поля в фильтре
// Массив сортировки (цена всвегда выводиться первой в списке
$arFilterSort = array(
    "Швейные машины" => array(
        "Бренд",
        "Год выпуска базы",
        "Прошиваемые материалы",
        "Рекомендация эксперта",
        "Тип машины",
        "Тип челнока",
        "Количество операций",
        "Потребляемая мощность",
        "Нитевдеватель",
        "Выполнение петель",
        "Цвет",
        "Габаритность",
		/*ttprom*/
		"Машина по типу пошиваемых материалов",
		"Тип платформы",
		"Максимальный подъем лапки",
		"Тип челночного механизма",
		"Система смазки",
		"Количество игл"
    ),
    "Оверлоки" => array(
        "Бренд",
        "Год выпуска базы",
        "Количество нитей",
        "Прошиваемые материалы",
        "Рекомендация эксперта",
        "Тип машины",
        "Количество швов",
        "Потребляемая мощность",
		/*"Количество игл",*/
        "Максимальная скорость шитья",
        "Дифференциальная подача материала",
        "Габаритность",
		/*---*/
		"Тип платформы",
		"Машина по типу пошиваемых материалов",
		"Высота подъема лапки",
		"Система смазки"
		
    ),
    "Вышивальные машины" => array(
        "Бренд",
        "Год выпуска базы",
        "Прошиваемые материалы",
        "Рекомендация эксперта",
        "Тип машины",
        "Количество игл",
        "Потребляемая мощность",
        "Тип дисплея",
        "Максимальный размер вышивки",
        "Количество встроенных дизайнов (в памяти машины)",
        "Максимальный размер вышивки",
        "Габаритность"
		/*---*/
    ),
    "Швейно-вышивальные машины" => array(
        "Бренд",
        "Год выпуска базы",
        "Прошиваемые материалы",
        "Рекомендация эксперта",
        "Тип машины",
        "Потребляемая мощность",
        "Тип челнока",
        "Нитевдеватель",
        "Виды швейных строчек",
        "Тип дисплея",
        "Максимальный размер вышивки",
        "Габаритность"
    ),
    "Вязальные машины" => array(
        "Бренд",
        "Рекомендация эксперта",
        "Корпус",
        "Количество фонтур",
        "Синхронизация с ПК",
        "Тип обрабатываемой пряжи",
        "Типы переплетений",
        "Габаритность"
    ),
    "Гладильные прессы" => array(
        "Бренд",
        "Рекомендация эксперта",
        "Потребляемая мощность",
        "Блок управления",
        "Время непрерывной работы",
        "Объем бойлера",
        "Режим NON-STOP",
        "Габаритность"
    ),
    "Гладильные системы" => array(
        "Бренд",
        "Рекомендация эксперта",
        "Потребляемая мощность",
        "Время непрерывной работы",
        "Режим NON-STOP",
        "Типы обрабатываемых тканей",
        "Размер рабочей поверхности",
        "Объем резервуара для воды",
        "Вертикальное отпаривание",
        "Габаритность"
    ),
    "Отпариватели" => array(
        "Бренд",
        "Рекомендация эксперта",
        "Потребляемая мощность",
        "Корпус",
        "Время непрерывной работы",
        "Режим NON-STOP",
        "Типы обрабатываемых тканей",
        "Количество режимов подачи пара"
    ),
    "Парогенераторы" => array(
        "Бренд",
        "Рекомендация эксперта",
        "Потребляемая мощность",
        "Корпус",
        "Время непрерывной работы",
        "Режим NON-STOP",
        "Типы обрабатываемых тканей",
        "Вертикальное отпаривание"
    ),
    "Пароочистители" => array(
        "Бренд",
        "Рекомендация эксперта",
        "Потребляемая мощность",
        "Время непрерывной работы",
        "Режим NON-STOP"
    ),
	
    "Пылесосы" => array(
        "Бренд",
        "Рекомендация эксперта",
        "Тип пылесоса",
        "Тип уборки",
        "Потребляемая мощность",
        "Мощность всасывания",
        "Тип пылесборника",
        "Вес"
    )
);
$arResult["SEPARATOR_FILTER"] = 0;
$arIdPropSort = array();

// Получим IDшники св-в которые необходимо отсортировать
$db_list = CIBlockSection::GetList(array(), array("ID" => $arParams["SECTION_ID"]));
if ($ar_result = $db_list->Fetch())
{
	if ($ar_result['DEPTH_LEVEL'] > 2) {
        $nav = CIBlockSection::GetNavChain(false, $arParams["SECTION_ID"]);

        while($arSectionPath = $nav->GetNext()) {
            if ($arSectionPath["DEPTH_LEVEL"] == 2) {
                $sectionName = $arSectionPath["NAME"];
                break;
            }
        }
    } else {
        $sectionName = $ar_result['NAME'];
    }

    if (isset($arFilterSort[$sectionName]))
    {
        foreach($arFilterSort[$sectionName] as $sortName)
        {
            foreach ($arResult["ITEMS"] as $arItem)
            {
                if ($sortName == $arItem["NAME"])
                {
                    $arIdPropSort[] = $arItem["ID"];
                }
            }
        }
    }
}

if (count($arIdPropSort) > 0)
{
    $arResult["ITEMS_TEMP"] = array();

    foreach ($arIdPropSort as $IdProp)
    {
        if(empty($arResult["ITEMS"][$IdProp]["VALUES"]) || isset($arResult["ITEMS"][$IdProp]["PRICE"])) continue;
        if ($arResult["ITEMS"][$IdProp]["DISPLAY_TYPE"] == "A" && ($arResult["ITEMS"][$IdProp]["VALUES"]["MAX"]["VALUE"] - $arResult["ITEMS"][$IdProp]["VALUES"]["MIN"]["VALUE"] <= 0)) continue;

        $arResult["ITEMS_TEMP"][$IdProp] = $arResult["ITEMS"][$IdProp];
        unset($arResult["ITEMS"][$IdProp]);
        $arResult["SEPARATOR_FILTER"] = $IdProp;
    }

    // Фильтры которые необходимо перенести вверх из скрытого блока
    $arResult["ITEMS2"] = $arResult["ITEMS"];
    foreach ($arResult["ITEMS2"] as $arItem)
    {
        if(empty($arItem["VALUES"])|| isset($arItem["PRICE"])) continue;
        if ($arItem["DISPLAY_TYPE"] == "A" && ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0)) continue;

        $IdProp = 0;
        if ($arItem["PROPERTY_TYPE"] == "N") {
            foreach($arItem["VALUES"] as $arVal)
            {
                if (!empty($arVal["HTML_VALUE"]))
                {
                    $IdProp = $arItem["ID"];
                    break;
                }
            }
        } elseif ($arItem["PROPERTY_TYPE"] == "L") {
            foreach($arItem["VALUES"] as $arVal)
            {
                if ($arVal["CHECKED"])
                {
                    $IdProp = $arItem["ID"];
                    break;
                }
            }
        }

        if ($IdProp && !isset($arResult["ITEMS_TEMP"][$IdProp])) {
            $arResult["ITEMS_TEMP"][$IdProp] = $arResult["ITEMS"][$IdProp];
            unset($arResult["ITEMS"][$IdProp]);
            $arResult["SEPARATOR_FILTER"] = $IdProp;
        }

    }

    foreach ($arResult["ITEMS"] as $key => $val)
    {
        $arResult["ITEMS_TEMP"][$key] = $val;
    }

    $arResult["ITEMS"] = $arResult["ITEMS_TEMP"];
}

// Добавим хлебные крошки с брендами
global $arSelectBrands;
$arSelectBrands = array();
foreach ($arResult["ITEMS"][13]["VALUES"] as $arVal) {
    if ($arVal["CHECKED"]) {
        $arSelectBrands[] = $arVal;

    }
}