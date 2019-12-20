<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arTmp = array();
$categoryFilter = array(
    array(19,20,21,23,24,69,83,85,90),                  // Швейная и вышивальная техника
    array(121,105,98,159,132,141,154,158,183,172,197),  // Гладильная и уборочная техника
    array(22,26,93,162,203,206,855,247,245),            // Аксессуары и прочее
);
$categoryTitle = array(
    "Швейная и вышивальная техника",
    "Гладильная и уборочная техника",
    "Аксессуары и прочее"
);
$categoryFormat = array(
    22 => "Все для швейных машин",
    26 => "Все для глажения",
    93 => "Все для вышивальных машин",
    162 => "Все для вязальных машин",
    203 => "Все для уборки",
    206 => "Ткани для шитья",
);
foreach($arResult as $id => $item) {
    foreach($categoryFilter as $nItem => $arItem) {
        if (in_array($item["PARAMS"]["ID"], $arItem)) {
            $icon = trim(CUtil::translit($item["TEXT"], "ru", array("replace_space"=>"-", "replace_other"=>"-")), "-");
            $items[$nItem][] = array(
                "TEXT" => (array_key_exists($item["PARAMS"]["ID"], $categoryFormat)) ? $categoryFormat[$item["PARAMS"]["ID"]] : $item["TEXT"],
                "LINK" => $item["LINK"],
                "ICON" => $icon
            );
        }
        $arTmp[$nItem] = array(
            "title" => $categoryTitle[$nItem],
            "items" => $items[$nItem]
        );
    }
}
//ksort($arTmp);
$arResult = $arTmp;
