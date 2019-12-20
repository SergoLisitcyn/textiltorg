<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
$arTmp = array();
$categoryFilter = array(
    array(17,19,20,21),         // Все для шитья
    array(18,23,24,69),         // Все для вышивания
    array(82,83,85,90),         // Все для вязания
    array(97,121,105,98),       // Все для глажения
    array(171,172,183,197),     // Все для уборки
    array(22,45,48,56,50),      // Аксессуары
);
$categoryFormat = array(
    22 => "Аксессуары и расходные материалы",
    45 => "Лапки",
    48 => "Иглы",
    56 => "Нити",
    50 => "Фурнитура",
);
foreach($arResult as $id => $item) {
    foreach($categoryFilter as $nItem => $arItem) {
        if (in_array($item["PARAMS"]["ID"], $arItem)) {
            if ($item["IS_PARENT"]) {
                $icon = trim(CUtil::translit($item["TEXT"], "ru", array("replace_space"=>"-", "replace_other"=>"-")), "-");
                $arTmp[$nItem]["parent"] = array(
                    "TEXT" => (array_key_exists($item["PARAMS"]["ID"], $categoryFormat)) ? $categoryFormat[$item["PARAMS"]["ID"]] : $item["TEXT"],
                    "LINK" => $item["LINK"],
                    "ICON" => $icon. "-icon"
                );
            } else {
                $arTmp[$nItem]["child"][] = array(
                    "TEXT" => (array_key_exists($item["PARAMS"]["ID"], $categoryFormat)) ? $categoryFormat[$item["PARAMS"]["ID"]] : $item["TEXT"],
                    "LINK" => $item["LINK"]
                );
            }
        }
    }
}
//var_dump($arResult);
ksort($arTmp);
$arResult = $arTmp;
