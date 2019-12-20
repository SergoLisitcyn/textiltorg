<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$MODULE_ID = "bxmaker.smsnotice";

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
\Bitrix\Main\Loader::includeModule($MODULE_ID);

$oTemplateType = new \Bxmaker\SmsNotice\Template\TypeTable();
$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();

$PREMISION_DEFINE = $APPLICATION->GetGroupRight($MODULE_ID);

if ($PREMISION_DEFINE == "D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
if ($PREMISION_DEFINE == 'W') $bReadOnly = false;
else  $bReadOnly = true;

$sTableID = 'bxmaker_smsnotice';
$sCurPage = $APPLICATION->GetCurPage();
$sSAPEdit = $MODULE_ID . '_template_type_edit.php';


$oSort = new CAdminSorting($sTableID, "SORT", "ASC");
$sAdmin = new CAdminList($sTableID, $oSort);

// меню
$sContent = array(
    array(
        "TEXT" => GetMessage($MODULE_ID . '_MENU_BTN_NEW_TITLE'),
        "LINK" => $sSAPEdit . "?lang=" . LANG,
        "TITLE" => GetMessage($MODULE_ID . '_MENU_BTN_NEW_TITLE'),
        "ICON" => "btn_new",
    ),
);
$sMenu = new CAdminContextMenu($sContent);


// Массовые операции удаления ---------------------------------
if ($arID = $sAdmin->GroupAction()) {

    switch ($req->get('action_button')) {
        case "delete":
            foreach ($arID as $id) {
                $res = $oTemplateType->delete($id);
            }
            break;
    }
}


// Сортировка ------------------------------
$by = 'ID';
if (isset($_GET['by']) && in_array($_GET['by'], array('ID', 'NAME', 'CODE'))) $by = $_GET['by'];
$arOrder = array($by => ($_GET['order'] == 'ASC' ? 'ASC' : 'DESC'));

// Постраничная навигация ------------------
$navyParams = CDBResult::GetNavParams(CAdminResult::GetNavSize(
    $sTableID,
    array('nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage())
));
$usePageNavigation = true;
if ($navyParams['SHOW_ALL']) {
    $usePageNavigation = false;
} else {
    $navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
    $navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
}

// Запрос -----------------------------------
$arQuery = array(
    'select' => array('*'),
    'order'  => $arOrder
);
if ($usePageNavigation) {
    $arQuery['limit'] = $navyParams['SIZEN'];
    $arQuery['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
}

$dbResultList = $oTemplateType->getList($arQuery);

$dbResultList = new CAdminResult($dbResultList, $sTableID);

$dbResultList->NavStart();


$sAdmin->NavText($dbResultList->GetNavPrint(GetMessage($MODULE_ID . '_PAGE_LIST_TITLE_NAV_TEXT')));

$sAdmin->AddHeaders(array(
    array(
        "id"      => 'ID',
        "content" => GetMessage($MODULE_ID . '_HEAD.ID'),
        "sort"    => 'ID',
        "default" => true
    ),
    array(
        "id"      => 'NAME',
        "content" => GetMessage($MODULE_ID . '_HEAD.NAME'),
        "sort"    => 'NAME',
        "default" => true
    ),
    array(
        "id"      => 'CODE',
        "content" => GetMessage($MODULE_ID . '_HEAD.CODE'),
        "sort"    => 'CODE',
        "default" => true
    ),
//    array(
//        "id"      => 'COUNT',
//        "content" => GetMessage($MODULE_ID . '_HEAD.COUNT'),
//        "sort"    => 'COUNT',
//        "default" => false
//    )
));


while ($sArActions = $dbResultList->NavNext(true, 's_')) {

    $row = &$sAdmin->AddRow($s_ID, $sArActions);
    $row->AddField('NAME', $s_NAME);
    $row->AddField('CODE', $s_CODE);
    $row->AddField('ID', $s_ID);

    $arActions = Array();
    $arActions[] = array(
        "ICON" => "edit",
        "TEXT" => GetMessage($MODULE_ID . '_MENU_EDIT'),
        "ACTION" => $sAdmin->ActionRedirect($sSAPEdit . "?ID=" . $s_ID . "&lang=" . LANG . ""),
        "DEFAULT" => true
    );

    $row->AddActions($arActions);

}


$sAdmin->AddFooter(
    array(
        array(
            "title" => GetMessage($MODULE_ID . '_LIST_SELECTED'),
            "value" => $dbResultList->SelectedRowsCount()
        ),
        array(
            "counter" => true,
            "title"   => GetMessage($MODULE_ID . '_LIST_CHECKED'),
            "value"   => "0"
        ),
    )
);

if (!$bReadOnly) {
    $sAdmin->AddGroupActionTable(
        array(
            "delete" => GetMessage($MODULE_ID . '_LIST_DELETE'),
        )
    );
}


$sAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage($MODULE_ID . '_PAGE_LIST_TITLE'));

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


\Bxmaker\SmsNotice\Manager::getInstance()->showDemoMessage();
\Bxmaker\SmsNotice\Manager::getInstance()->addAdminPageCssJs();


$sMenu->Show();
$sAdmin->DisplayList();

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>