<?
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

	$MODULE_ID = "bxmaker.smsnotice";

	\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
	\Bitrix\Main\Loader::includeModule($MODULE_ID);

	CUtil::InitJSCore('jquery');

	$oManagerTable = new \Bxmaker\SmsNotice\ManagerTable();
	$oManager      = \Bxmaker\SmsNotice\Manager::getInstance();
	$oTemplate = new \Bxmaker\SmsNotice\TemplateTable();
	$app           = \Bitrix\Main\Application::getInstance();
	$req           = $app->getContext()->getRequest();
	$asset         = \Bitrix\Main\Page\Asset::getInstance();

	$dir  = str_replace($_SERVER['DOCUMENT_ROOT'], '', _normalizePath(dirname(__FILE__)));
	$bUtf = $oManager::isUTF();

	$PREMISION_DEFINE = $APPLICATION->GetGroupRight($MODULE_ID);

	if ($PREMISION_DEFINE == "D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	if ($PREMISION_DEFINE == 'W') {
		$bReadOnly = false;
	}
	else  $bReadOnly = true;

	$bAjax = false;
	if ($req->isAjaxRequest() && $req->getPost('method') && $req->getPost('method') == 'get_content') {
		$bAjax = true;
	}

	if ($req->isPost() && check_bitrix_sessid('sessid') && $req->getPost('method') && $req->getPost('method') == 'send_sms') {


		$arJson = array(
			'error'    => array(),
			'response' => array()
		);

		do {

			if (!$req->getPost('phone') || !$oManager->isValidPhone($req->getPost('phone'))) {
				$arJson['error'] = array(
					'error_code' => 'invalid_phone',
					'error_msg'  => GetMessage($MODULE_ID . '.AJAX.INVALID_PHONE'),
					'error_more' => array()
				);
				break;
			}

			$text = $req->getPost('text');
			$text = (!$bUtf ? $APPLICATION->ConvertCharset($text, 'UTF-8', "Windows-1251") : $text);

			if (!$text || strlen(trim($text)) <= 0) {
				$arJson['error'] = array(
					'error_code' => 'invalid_text',
					'error_msg'  => GetMessage($MODULE_ID . '.AJAX.INVALID_TEXT'),
					'error_more' => array()
				);
				break;
			}


			/**
			 * @var \Bxmaker\SmsNotice\Result $result
			 */
			$result = $oManager->send($req->getPost('phone'), $text);
			if ($result->isSuccess()) {


				switch ($result->getResult()) {
					case \Bxmaker\SmsNotice\SMS_STATUS_SENT: {
						$arJson['response'] = array(
							'msg' => GetMessage($MODULE_ID . '.AJAX.SMS_STATUS_SENT') . ' ' . date('H:i:s'),
						);
						break;
					}
					case \Bxmaker\SmsNotice\SMS_STATUS_DELIVERED: {
						$arJson['response'] = array(
							'msg' => GetMessage($MODULE_ID . '.AJAX.SMS_STATUS_DELIVERED') . ' ' . date('H:i:s'),
						);
						break;
					}
					case \Bxmaker\SmsNotice\SMS_STATUS_ERROR: {
						$arJson['error'] = array(
							'error_code' => '',
							'error_msg'  => $result->getMore('error_description')
						);
						break;
					}
				}
			}
			else {
				/**
				 * @var \Bxmaker\SmsNotice\Error $error
				 */
				$arErrors = $result->getErrors();
				foreach ($arErrors as $error) {
					$arJson['error'] = array(
						'error_code' => $error->getCode(),
						'error_msg'  => $error->getMessage(),
						'error_more' => $error->getMore()
					);
					break;
				}
			}
		} while (false);

		$APPLICATION->RestartBuffer();
		header('Content-Type: application/json');

		if (!empty($arJson['error'])) {
			echo json_encode(array(
				'error'  => ($bUtf ? $arJson['error'] : $APPLICATION->ConvertCharsetArray($arJson['error'], LANG_CHARSET, 'UTF-8')),
				'status' => 0
			));
		}
		else {
			echo json_encode(array(
				'response' => ($bUtf ? $arJson['response'] : $APPLICATION->ConvertCharsetArray($arJson['response'], LANG_CHARSET, 'UTF-8')),
				'status'   => 1
			));

		}
		die();
	}


	$sTableID = 'bxmaker_smsnotice_list_table';
	$sCurPage = $APPLICATION->GetCurPage();
	$sSAPEdit = $MODULE_ID . '_edit.php';


	$oSort  = new CAdminSorting($sTableID, "SORT", "ASC");
	$sAdmin = new CAdminList($sTableID, $oSort);

	// меню

	// Массовые операции удаления ---------------------------------
	if ($arID = $sAdmin->GroupAction()) {
		switch ($req->getPost('action_button')) {
			case "delete":
				foreach ($arID as $id) {
					$res = $oManagerTable->delete($id);
				}
				break;
		}
	}


	// сайты
	$arSite = array();
	$dbr    = \CSite::GetList($by = 'sort', $order = 'asc');
	while ($ar = $dbr->Fetch()) {
		$arSite[$ar['ID']] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
	}



	//шаблоны
	$arSiteData = $oManager->getSiteData();
	$arTemplate = array(
		'REFERENCE' => array(GetMessage($MODULE_ID . '_FORM.TEMPLATE_PLACEHOLDER')),
		'REFERENCE_ID' => array(0)
	);
	$arTemplateData = array();
	$dbrTemplate = $oTemplate->getList(array(
		'select' => array(
			'*', 'TYPE' => 'TYPE.*'
		),
		'filter' => array(
			'SITE.SID' => $oManager->getCurrentSiteId()
		)
	));
	while($ar = $dbrTemplate->fetch())
	{
		$arTemplate['REFERENCE'][] = '['.$ar['TYPECODE'].'] ' . $ar['NAME'];
		$arTemplate['REFERENCE_ID'][] = $ar['ID'];

		$ar['TYPEDESCR'] = GetMessage($MODULE_ID .'.AJAX.TEMPLATE_TYPE_FIELD_SITE_NAME') . "\n" . GetMessage($MODULE_ID .'.AJAX.TEMPLATE_TYPE_FIELD_SERVER_NAME') . "\n" . $ar['TYPEDESCR'];
		$ar['TYPEVALUE']['#SITE_NAME#'] = (isset($arSiteData['SITE_NAME']) ? $arSiteData['SITE_NAME'] : '');
		$ar['TYPEVALUE']['#SERVER_NAME#'] = (isset($arSiteData['SERVER_NAME']) ? $arSiteData['SERVER_NAME'] : '');

		$arTemplateData[$ar['ID']] = $ar;
	}

	// Сортировка ------------------------------
	$by = 'ID';
	if (isset($_GET['by']) && in_array($_GET['by'], array('ID', 'PHONE', 'STATUS', 'CREATED'))) $by = $_GET['by'];
	$arOrder = array($by => ($_GET['order'] == 'ASC' ? 'ASC' : 'DESC'));


	// Постраничная навигация ------------------
	$navyParams        = CDBResult::GetNavParams(CAdminResult::GetNavSize(
		$sTableID,
		array('nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage())
	));
	$usePageNavigation = true;
	if ($navyParams['SHOW_ALL']) {
		$usePageNavigation = false;
	}
	else {
		$navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
		$navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
	}


	// Запрос -----------------------------------
	$arQuery = array(
		'select' => array('*', 'TYPE_NAME' => 'TYPE.NAME'),
		'order'  => $arOrder
	);
	if ($usePageNavigation) {

		$totalCount = 0;
		$totalPages = 0;
		$dbrCount   = $oManagerTable->getList(array(
			'select' => array('CNT')
		));
		if ($ar = $dbrCount->fetch()) {
			$totalCount = $ar['CNT'];
		}

		if ($totalCount > 0) {
			$totalPages = ceil($totalCount / $navyParams['SIZEN']);
			if ($navyParams['PAGEN'] > $totalPages) {
				$navyParams['PAGEN'] = $totalPages;
			}
			$arQuery['limit']  = $navyParams['SIZEN'];
			$arQuery['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
		}
		else {
			$navyParams['PAGEN'] = 1;
			$arQuery['limit']    = $navyParams['SIZEN'];
			$arQuery['offset']   = 0;
		}
	}

	$dbResultList = new CAdminResult($oManagerTable->getList($arQuery), $sTableID);
	if ($usePageNavigation) {
		$dbResultList->NavStart($arQuery['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
		$dbResultList->NavRecordCount = $totalCount;
		$dbResultList->NavPageCount   = $totalPages;
		$dbResultList->NavPageNomer   = $navyParams['PAGEN'];
	}
	else {
		$dbResultList->NavStart();
	}


	$sAdmin->NavText($dbResultList->GetNavPrint(GetMessage($MODULE_ID . '_PAGE_LIST_TITLE_NAV_TEXT')));

	$sAdmin->AddHeaders(array(
		array(
			"id"      => 'ID',
			"content" => GetMessage($MODULE_ID . '_HEAD.ID'),
			"sort"    => 'ID',
			"default" => true
		),
		array(
			"id"      => 'PHONE',
			"content" => GetMessage($MODULE_ID . '_HEAD.PHONE'),
			"sort"    => 'PHONE',
			"default" => true
		),
		array(
			"id"      => 'STATUS',
			"content" => GetMessage($MODULE_ID . '_HEAD.STATUS'),
			"sort"    => 'STATUS',
			"default" => true
		),
		array(
			"id"      => 'TEXT',
			"content" => GetMessage($MODULE_ID . '_HEAD.TEXT'),
			"sort"    => 'TEXT',
			"default" => true
		),
		array(
			"id"      => 'SITE_ID',
			"content" => GetMessage($MODULE_ID . '_HEAD.SITE_ID'),
			"sort"    => 'SITE_ID',
			"default" => true
		),
		array(
			"id"      => 'CREATED',
			"content" => GetMessage($MODULE_ID . '_HEAD.CREATED'),
			"sort"    => 'CREATED',
			"default" => true
		),
		array(
			"id"      => 'COMMENT',
			"content" => GetMessage($MODULE_ID . '_HEAD.COMMENT'),
			"sort"    => 'COMMENT',
			"default" => true
		),


	));


	while ($sArActions = $dbResultList->NavNext(true, 's_')) {


		$row = &$sAdmin->AddRow($s_ID, $sArActions);
		$row->AddField('PHONE', $s_PHONE);

		$error_status = '';
		switch ($s_STATUS) {
			case \Bxmaker\SmsNotice\SMS_STATUS_ERROR: {

				if (isset($s_PARAMS['error_description'])) {
					$error_status .= '<br>' . $s_PARAMS['error_description'];
				}
				break;
			}
		}
		$row->AddField('STATUS', GetMessage($MODULE_ID . '_HEAD.STATUS_' . $s_STATUS) . $error_status);

		$row->AddField('TEXT', ($s_TYPE_NAME ? '<small><b>' . $s_TYPE_NAME . '</b></small><br>' : '') . $s_TEXT);
		$row->AddField('CREATED', $s_CREATED);
		$row->AddField('COMMENT', '<div class="sms_comment_box" > <span>' . TruncateText($s_COMMENT, 30) . '</span><div class="more">' . $s_COMMENT . '</div></div>');
		$row->AddField('SITE_ID', (isset($arSite[$s_SITE_ID]) ? $arSite[$s_SITE_ID] : ''));
		$row->AddField('ID', $s_ID);

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

	$arServiceParam = $oManager->getServiceParam();
	$balance        = $oManager->getBalance();
?>


	<div class="ap-smsnotice-send-box">
		<p><?= GetMessage($MODULE_ID . '_FORM.LABEL'); ?></p>

		<? if ($arServiceParam): ?>
			<p class="sms_service_info_box">
				<b><?= GetMessage($MODULE_ID . '_FORM.LABEL.SITE'); ?></b> <?= $arSite[$oManager->getCurrentSiteId()]; ?>
				<br/>
				<b><?= GetMessage($MODULE_ID . '_FORM.LABEL.SERVICE'); ?></b> [<?= $arServiceParam['ID']; ?>] <?= $arServiceParam['NAME']; ?>.
				<br/>
				<b><?= GetMessage($MODULE_ID . '_FORM.LABEL.BALANCE'); ?></b> <?= ($balance->isSuccess() ? $balance->getResult() : implode(', ', $balance->getErrorMessages())); ?>
			</p>

		<? endif; ?>


		<div class="msg_box">

		</div>

		<div class="row_item">
			<small><?= GetMessage($MODULE_ID . '_FORM.PHONE'); ?></small>
			<input type="text" name="phone" value="" placeholder="<?= GetMessage($MODULE_ID . '_FORM.PHONE_PLACEHOLDER'); ?>"/>
		</div>

		<div class="row_item">
			<small><?= GetMessage($MODULE_ID . '_FORM.TEMPLATE'); ?></small>
			<?=SelectBoxFromArray('template', $arTemplate);?>
		</div>

		<div class="row_item textarea_box" >
			<small><?= GetMessage($MODULE_ID . '_FORM.TEXT'); ?></small>
			<br/>
			<textarea name="text" rows="4" cols="10"></textarea>
		</div>
		<div class="info_box">
			<span class="text_size"><?= GetMessage($MODULE_ID . '_FORM.INFO'); ?></span>
			<span class="btn_clean"><?= GetMessage($MODULE_ID . '_FORM.BTN_CLEAN'); ?></span>
		</div>

		<div class="row_item translit_box">
			<input type="checkbox" name="translit" id="translit" value="Y"/>
			<label for="translit"><?= GetMessage($MODULE_ID . '_FORM.BTN_TRANSLIT'); ?></label>
		</div>

		<div class="fileds_box">

		</div>

		<div class="row_item">
			<input class="adm-btn btn_send " type="button" value="<?= GetMessage($MODULE_ID . '_FORM.SENT'); ?>"/>
		</div>

		<div class="result_msg_text">
			<div class="title_box"><?= GetMessage($MODULE_ID . '_FORM.RESULT_MSG'); ?></div>
			<div class="text_box"></div>
		</div>

	</div>

	<br/>

	<div class="smsnotice_error_description_box">
		<div class="close"><?= GetMessage($MODULE_ID . '_FORM.CLOSE_ERROR'); ?></div>
		<div class="descr">

		</div>
	</div>

	<h2><?= GetMessage($MODULE_ID . '_LIST_LABEL'); ?></h2>

	<div class="bxmaker_smsnotice_list_table_box">
		<?

			if ($bAjax) {
				$APPLICATION->RestartBuffer();
				ob_start();
			}

			$sAdmin->DisplayList();

			if ($bAjax) {


				echo ob_get_clean();

				die();
			}
		?>
	</div>

	<script type="text/javascript">
		BX.message({
			'bxmaker_smsnotice_template_type' : <?=json_encode( $APPLICATION->ConvertCharsetArray($arTemplateData, LANG_CHARSET, 'UTF-8'));?>,
			'bxmaker_smsnotice_translit' : <?=GetMessage($MODULE_ID . '.translit');?>
		});
	</script>

<?
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>