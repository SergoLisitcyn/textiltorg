<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$arResult = array();

CModule::IncludeModule('iblock');
$IBLOCK_ID = intval($_REQUEST['IBLOCK_ID']);
$QUERY = trim($_REQUEST['q']);

if ($QUERY)
{

    $arSelect = array('ID', 'NAME', 'IBLOCK_ID', 'XML_ID');
    $arFilter = array('IBLOCK_ID' => 8, '%NAME' => $_REQUEST['q']);

    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while($ob = $res->GetNext())
    {
        $arResult[] = array('ID' => $ob['XML_ID'], 'NAME' => $ob['NAME']);
    }
}

header('Content-type: application/json');
echo json_encode($arResult);
