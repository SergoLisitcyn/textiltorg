<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$MODULE_ID = "bxmaker.smsnotice";

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
\Bitrix\Main\Loader::includeModule($MODULE_ID);


$oTemplateType = new Bxmaker\SmsNotice\Template\TypeTable();
$oTemplate = new \Bxmaker\SmsNotice\TemplateTable();
$oTemplateSite = new \Bxmaker\SmsNotice\Template\SiteTable();
$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();

$PREMISION_DEFINE = $APPLICATION->GetGroupRight($MODULE_ID);

if ($PREMISION_DEFINE == "D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
if ($PREMISION_DEFINE == 'W') $bReadOnly = false;
else  $bReadOnly = true;

$sTableID = 'bxmaker_smsnotice';
$sCurPage = $APPLICATION->GetCurPage();
$sSAPEdit = $MODULE_ID . '_template_edit.php';


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

    switch ($req->get('action')) {
        case "deactivate":
            foreach ($arID as $id) {
                $res = $oTemplate->update($id, array('ACTIVE' => false));
            }
            break;
        case "active":
            foreach ($arID as $id) {
                $res = $oTemplate->update($id, array('ACTIVE' => true));
            }
            break;
    }

	switch ($req->getPost('action_button')) {
		case "delete":
			foreach ($arID as $id) {
				$res = $oTemplate->delete($id);
			}
			break;
	}
}


// Типы
$arTemplateType = array();
$dbr = $oTemplateType->getList();
while($ar = $dbr->fetch())
{
    $arTemplateType[$ar['ID']] = $ar['NAME'];
}


// сайты
$arSite = array();
$dbr = \CSite::GetList($by = 'sort', $order = 'asc');
while ($ar = $dbr->Fetch()) {
    $arSite[$ar['ID']] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
}



// Сортировка ------------------------------
$by = 'ID';
if (isset($_GET['by']) && in_array($_GET['by'], array('ID', 'NAME', 'TYPE_ID', 'ACTIVE'))) $by = $_GET['by'];
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
    'select' =>array(
        '*', 'TP_' => 'TYPE'
    ),
    'order'  => $arOrder
);
if ($usePageNavigation) {
    $arQuery['limit'] = $navyParams['SIZEN'];
    $arQuery['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
}

$dbResultList = $oTemplate->getList($arQuery);

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
        "id"      => 'TYPE_ID',
        "content" => GetMessage($MODULE_ID . '_HEAD.TYPE'),
        "sort"    => 'TYPE_ID',
        "default" => true
    ),
    array(
        "id"      => 'ACTIVE',
        "content" => GetMessage($MODULE_ID . '_HEAD.ACTIVE'),
        "sort"    => 'ACTIVE',
        "default" => true
    ),
    array(
        "id"      => 'SITE',
        "content" => GetMessage($MODULE_ID . '_HEAD.SITE'),
        "sort"    => '',
        "default" => true
    )
));


while ($sArActions = $dbResultList->NavNext(true, 's_')) {

    $arSID = array();
    $dbrSID = $oTemplateSite->getList(array(
        'filter' => array(
            'TID' => $s_ID
        )
    ));
    while($ar = $dbrSID->fetch())
    {
        $arSID[] = (isset($arSite[$ar['SID']]) ? $arSite[$ar['SID']] : $ar['SID']);
    }


    $row = &$sAdmin->AddRow($s_ID, $sArActions);
    $row->AddField('TYPE_ID', (isset($s_TP_NAME) ? $s_TP_NAME . '<br><small>'. $s_TP_CODE .'</small>' : $s_TYPE_ID ));
    $row->AddField('NAME', $s_NAME);
    $row->AddField('ACTIVE', GetMessage($MODULE_ID . '_HEAD.ACTIVE_' . $s_ACTIVE));
    $row->AddField('ID', $s_ID);

    $row->AddField('SITE', implode('<br />', $arSID));

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
            "active" => GetMessage($MODULE_ID . '_LIST_ACTIVE'),
            "deactivate" => GetMessage($MODULE_ID . '_LIST_DEACTIVATE'),
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