<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$MODULE_ID = "bxmaker.smsnotice";

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
\Bitrix\Main\Loader::includeModule($MODULE_ID);


$sTableID = $MODULE_ID;
$sCurPage = $APPLICATION->GetCurPage();
$page_prefix = $MODULE_ID . '_service_';
$errors = null;

// �������� ���� �������
$PREMISION_DEFINE = $APPLICATION->GetGroupRight($MODULE_ID);

if ($PREMISION_DEFINE != 'W') {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
    die();
}




CUtil::InitJSCore('jquery');
$dir = str_replace($_SERVER['DOCUMENT_ROOT'], '', _normalizePath(dirname(__FILE__)));

$bUtf = \Bxmaker\SmsNotice\Manager::isUTF();

$oServiceTable = new \Bxmaker\SmsNotice\ServiceTable();
$oService = new \Bxmaker\SmsNotice\Service();


// ��������� �������
$arServiceList = array(
    'REFERENCE'    => array(GetMessage($MODULE_ID . '.FIELD.CODE_SELECT')),
    'REFERENCE_ID' => array('')
);
foreach ($oService->getArray() as $ar) {
    $arServiceList['REFERENCE'][] = $ar['NAME'];
    $arServiceList["REFERENCE_ID"][] = $ar['CODE'];
}



// �����
$arSites = array();
$arSite = array(
    'REFERENCE_ID' => array(''),
    'REFERENCE' => array(GetMessage($MODULE_ID . '.FIELD.SITE_ID_SELECT'))
);
$dbr = \CSite::GetList($by = 'sort', $order = 'asc');
while ($ar = $dbr->Fetch()) {
    $arSite['REFERENCE_ID'][] = $ar['ID'];
    $arSite['REFERENCE'][] =  '[' . $ar['ID'] . '] ' . $ar['NAME'];

    $arSites[$ar['ID']] = $ar;
}


$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();



if ($req->isPost() && check_bitrix_sessid('sessid') && $req->getPost('method') && $req->getPost('method') == 'getServiceParams') {
    $APPLICATION->RestartBuffer();

    $arJson = array(
        'error'    => array(),
        'response' => array()
    );

    do {


        if (!$req->getPost('service') || !in_array($req->getPost('service'), $arServiceList["REFERENCE_ID"])) {
            $arJson['error'] = array(
                'error_code' => 'invalid_service',
                'error_msg'  => GetMessage($MODULE_ID . '.AJAX.INVALID_SERVICE'),
                'error_more' => array()
            );
            break;
        }

        $oServiceCurrent = $oService->getInstance($req->getPost('service'));
        $arJson['response'] = array(
            'items' => $oServiceCurrent->getParams(),
            'description' => $oServiceCurrent->getDescription()
        );


    } while (false);

	$APPLICATION->RestartBuffer();
	header('Content-Type: application/json');

    if (!empty($arJson['error'])) {
        echo json_encode(array(
            'error'  =>  ($bUtf ? $arJson['error'] : $APPLICATION->ConvertCharsetArray($arJson['error'],  LANG_CHARSET, 'UTF-8')),
            'status' => 0
        ));
    } else {
        echo json_encode(array(
            'response' => ($bUtf ? $arJson['response'] : $APPLICATION->ConvertCharsetArray($arJson['response'],  LANG_CHARSET, 'UTF-8')),
            'status'   => 1
        ));

    }

    die();
}


// ��������� ��� ������
$oMenu = new CAdminContextMenu(array(
    array(
        "TEXT"  => GetMessage($MODULE_ID . '.NAV_BTN.RETURN'),
        "LINK"  => $page_prefix . 'list.php?lang=' . LANG,
        "TITLE" => GetMessage($MODULE_ID . '.NAV_BTN.RETURN'),
    ),
));


// �������������
$fname = 'bxmaker_smsnotice_service_edit_form';

// ��������������
if ($req->get('ID')) {
    $dbr = $oServiceTable->getList(array(
        'filter' => array(
            'ID' => intval($req->get('ID'))
        )
    ));
    if ($ar = $dbr->fetch()) {
        $arResult = $ar;
    }
}


// ����������. ����������
if (($apply || $save) && check_bitrix_sessid() && $req->isPost()) {

    do {

        $errors = array();
        $arFields = array();

        $arFields['CODE'] = ($req->getPost('CODE') && in_array($req->getPost('CODE'), $arServiceList["REFERENCE_ID"]) ? trim($req->getPost('CODE')) : '');
        $arFields['NAME'] = ($req->getPost('NAME') ? trim($req->getPost('NAME')) : '');
        $arFields['ACTIVE'] = ($req->getPost('ACTIVE') ? true : false);
        $arFields['PARAMS'] = array();
        $arFields['SITE_ID'] = ($req->getPost('SITE_ID') && in_array($req->getPost('SITE_ID'), $arSite["REFERENCE_ID"]) ? trim($req->getPost('SITE_ID')) : '');


        if($req->getPost('SERVICE_PARAMS_LIST'))
        {
            $arParamsName = explode(',', $req->getPost('SERVICE_PARAMS_LIST'));
            $arParamsValue = ($req->getPost('SERVICE_PARAMS') ? $req->getPost('SERVICE_PARAMS') : array());
            foreach($arParamsName as $name)
            {
                $arFields['PARAMS'][$name] = (isset($arParamsValue[$name]) ? trim($arParamsValue[$name]) : '');
            }
        }

        $arResult = $arFields;

        if (strlen(trim($arFields['NAME'])) <= 0) {
            $errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($MODULE_ID . '.FIELD_ERROR.EMPTY_NAME'));
            break;
        }
        if (strlen(trim($arFields['CODE'])) <= 0) {
            $errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($MODULE_ID . '.FIELD_ERROR.EMPTY_CODE'));
            break;
        }
        if (strlen(trim($arFields['SITE_ID'])) <= 0) {
            $errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($MODULE_ID . '.FIELD_ERROR.EMPTY_SITE_ID'));
            break;
        }


        if (empty($errors)) {
            if ($req->get('ID')) {
                // ����������
                $result = $oServiceTable->update(intval($req->get('ID')), $arFields);

                if ($result->isSuccess()) {
                    if ($apply) {
                        LocalRedirect($APPLICATION->GetCurPageParam());
                    } elseif ($save) {
                        LocalRedirect('/bitrix/admin/' . $page_prefix . 'list.php?lang=' . LANG);
                    }
                } else {
                    $errors = $result->getErrors();
                }
            } else {
                // ���������
                $result = $oServiceTable->add($arFields);
                if ($result->isSuccess()) {
                    if ($apply) {
                        LocalRedirect($APPLICATION->GetCurPageParam('ID=' . $result->getId(), array('ID')));
                    } elseif ($save) {
                        LocalRedirect('/bitrix/admin/' . $page_prefix . 'list.php?lang=' . LANG);
                    }
                } else {
                    $errors = $result->getErrors();
                }
            }
        }

    } while (false);

}


$tab = new CAdminTabControl('edit', array(
    array(
        'DIV'   => 'edit',
        'TAB'   => GetMessage($MODULE_ID . '.TAB.EDIT'),
        'ICON'  => '',
        'TITLE' => GetMessage($MODULE_ID . '.TAB.EDIT')),
));

$APPLICATION->SetTitle(GetMessage($MODULE_ID . '.PAGE_TITLE'));

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


\Bxmaker\SmsNotice\Manager::getInstance()->showDemoMessage();
\Bxmaker\SmsNotice\Manager::getInstance()->addAdminPageCssJs();



if ($errors && is_array($errors))
{
    $arStr = array();
    foreach($errors as $error)
    {
        $arStr[] = $error->getMessage();
    }
    \CAdminMessage::ShowMessage(implode('<br />',$arStr));
}


$oMenu->Show();

?>


    <script type="text/javascript">
        var bxmaker_smsnotice_service_edit_params_current = <?=json_encode(isset($arResult['PARAMS']) ? $arResult['PARAMS'] : array());?>;
    </script>

    <form action="<? $APPLICATION->GetCurPage() ?>" method="POST" name="<?= $fname ?>">
        <? echo bitrix_sessid_post(); ?>

        <? $tab->Begin(); ?>
        <? $tab->BeginNextTab(); ?>



        <? if ($req->get('ID')): ?>
            <tr style="display:none;">
                <td colspan="2">
                    <div class="service_notice_url">
                        <?

                        $protocol = 'http';
                        if(preg_match('#^HTTPS#', $_SERVER['SERVER_PROTOCOL'], $match))
                        {
                            $protocol = 'https';
                        }


                        if(isset($arSites[$arResult["SITE_ID"]]['DOMAIN']))
                        {
                            echo $protocol. '://' . $arSites[$arResult["SITE_ID"]]['DOMAIN'] . str_replace( \Bitrix\Main\Loader::getDocumentRoot(), '', \Bitrix\Main\Loader::getLocal('/modules/' . \Bxmaker\SmsNotice\Service::$moduleId))
                                . '/notice/index.php?serviceId=' . intval($req->get('ID'));
                        }
                        else
                        {
                            echo $protocol . '://' . $_SERVER['HTTP_HOST'] . str_replace(\Bitrix\Main\Loader::getDocumentRoot(), '', \Bitrix\Main\Loader::getLocal('/modules/' . \Bxmaker\SmsNotice\Service::$moduleId))
                                . '/notice/index.php?serviceId=' . intval($req->get('ID'));
                        }
                        ?>
                    </div>
                </td>
            </tr>

            <tr>
                <td><?= GetMessage($MODULE_ID . '.FIELD_LABEL.ID'); ?> </td>
                <td><?= $req->get('ID') ?></td>
            </tr>
        <? endif; ?>

        <tr>
            <td><?= GetMessage($MODULE_ID . '.FIELD_LABEL.ACTIVE'); ?> <span class="reg">*</span></td>
            <td><?= InputType('checkbox', 'ACTIVE', 'Y', ($arResult['ACTIVE'] ? 'Y' : 'N'), ''); ?></td>
        </tr>
        <tr>
            <td><?= GetMessage($MODULE_ID . '.FIELD_LABEL.NAME'); ?> <span class="reg">*</span></td>
            <td><?= InputType('text', 'NAME', $arResult['NAME'], ''); ?><?=ShowJSHint(GetMessage($MODULE_ID . '.FIELD_LABEL.NAME_HINT'));?> </td>
        </tr>
        <tr>
            <td><?= GetMessage($MODULE_ID . '.FIELD_LABEL.SITE_ID'); ?>  <span class="reg">*</span></td>
            <td><?= SelectBoxFromArray('SITE_ID', $arSite, $arResult["SITE_ID"]); ?></td>
        </tr>
        <tr>
            <td><?= GetMessage($MODULE_ID . '.FIELD_LABEL.CODE'); ?>  <span class="reg">*</span></td>
            <td><?= SelectBoxFromArray('CODE', $arServiceList, $arResult["CODE"]); ?></td>
        </tr>

        <tr class="content_type-html-row">
            <td style="text-align:center;" colspan="2"><?= GetMessage($MODULE_ID . '.FIELD_LABEL.PARAMS'); ?> </td>
        </tr>
        <tr class="content_type-html-row">
            <td colspan="2">
                <div class="service_params_box">

                </div>
            </td>
        </tr>


        <? $tab->EndTab(); ?>
        <? $tab->Buttons(array("disabled" => ($PREMISION_DEFINE != "W"),)); ?>
        <? $tab->End(); ?>
    </form>



<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>