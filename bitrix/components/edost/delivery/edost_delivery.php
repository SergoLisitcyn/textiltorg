<?
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('PUBLIC_AJAX_MODE', true);
//define('DisableEventsCheck', true);
//define('BX_SECURITY_SHOW_MESSAGE', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$mode = (isset($_POST['mode']) ? preg_replace("/[^a-z|_]/i", "", substr($_POST['mode'], 0, 20)) : '');
$id = (isset($_POST['id']) ? preg_replace("/[^0-9A,]/i", "", substr($_POST['id'], 0, 2000)) : 0);
$order_id = (isset($_POST['order_id']) ? preg_replace("/[^0-9]/i", "", substr($_POST['order_id'], 0, 20)) : 0);
$zip = (isset($_POST['id']) ? preg_replace("/[^0-9a-z.]/i", "", substr($_POST['zip'], 0, 10)) : 0);
$admin = (isset($_POST['admin']) && $_POST['admin'] == 'Y' ? true : false);
$flag = (isset($_POST['flag']) ? preg_replace("/[^a-z|_]/i", "", substr($_POST['flag'], 0, 20)) : '');
$office_id = (isset($_POST['office_id']) ? preg_replace("/[^0-9A]/i", "", substr($_POST['office_id'], 0, 20)) : false);
$profile = (isset($_POST['profile']) ? intval($_POST['profile']) : false);
$option = (isset($_POST['option']) && $_POST['option'] == 'Y' ? true : false);

$group_right = $GLOBALS['APPLICATION']->GetGroupRight('sale');

// ��������� ���������� �� ��������������� ������
if ($mode == 'detail') {
	$GLOBALS['APPLICATION']->IncludeComponent('edost:delivery', '', array('MODE' => 'detail', 'SHIPMENT_ID' => $id), null, array('HIDE_ICONS' => 'Y'));
}

// ���������� ���������� ����� � ��������� ������ ��� �������� �������������� ��������
if ($mode == 'order_edit' && $group_right != 'D') {
	if ($id == '') $id = 0;
	if ($office_id !== false) {
		if (!empty($profile)) $_SESSION['EDOST']['admin_order_edit_office'][$id] = array('id' => 'edost:'.$profile, 'profile' => $profile, 'office_id' => $office_id);
		echo 'OK';
	}
	else {
		if (!isset($_SESSION['EDOST']['admin_order_edit'][$id])) $id = 0;
		if (isset($_SESSION['EDOST']['admin_order_edit'][$id])) echo $_SESSION['EDOST']['admin_order_edit'][$id];
		else echo '{}';
	}
}


if (in_array($mode, array('package', 'option_setting', 'profile_setting', 'profile', 'batch', 'option', 'control')) && $group_right != 'D') {
	$s = 'modules/edost.delivery/classes/general/delivery_edost.php';
	require_once($_SERVER['DOCUMENT_ROOT'].(version_compare(SM_VERSION, '15.0.0') >= 0 ? getLocalPath($s) : '/bitrix/'.$s));

	if ($mode == 'batch' && !empty($_POST['save'])) {
		// ��������� �������� � ����� �������
		$batch = array();
		$ar = array('profile_shop', 'profile_delivery', 'call');
		foreach ($ar as $v) if (isset($_POST[$v])) $batch[$v] = intval($_POST[$v]);

        if (!empty($batch)) {
			$ar = edost_class::Control($id, array('batch' => $batch));
			edost_class::Control('clear_cache_flag');
			if (isset($ar['add_error'])) echo 'error:'.$ar['add_error'];
		}
	}
	if ($mode == 'profile') {
		// ����� ���������� ������� � ���������
		$control = edost_class::Control();
		if (!empty($control['data'][$id]['batch'])) {
			$v = $control['data'][$id];
			edost_class::GetRegisterProfile(array('company' => $v['company_id'], 'profile_shop' => $v['batch']['profile_shop'], 'profile_delivery' => $v['batch']['profile_delivery']));
		}
	}

	// ��������� ��������
	if ($mode == 'profile_setting') {
		edost_class::GetRegisterProfile(array('id' => $id, 'new' => (!empty($_POST['data']) ? $GLOBALS['APPLICATION']->ConvertCharset($_POST['data'], 'utf-8', LANG_CHARSET) : false), 'setting' => true));
	}
	// ��������� �����
	if ($mode == 'option_setting') {
		edost_class::GetRegisterOption($id, !empty($_POST['data']) ? $GLOBALS['APPLICATION']->ConvertCharset($_POST['data'], 'utf-8', LANG_CHARSET) : false, true);
	}

	// ��������� ���������� �������� � ����� ��������
	if ($mode == 'package') {
		$param = ($option ? 'option' : 'package');

		edost_class::SavePackage($order_id, $id, $option ? explode(',', $_POST['data']) : edost_class::UnPackDataArray($_POST['data'], 'package2'), $param);

		$data = edost_class::GetShipmentData($id);
		edost_class::AddRegisterData($data);
//		echo '<br><b>data:</b> <pre style="font-size: 12px">'.print_r($data, true).'</pre>';
		echo edost_class::GetPackageString(array_shift($data), $param);
	}

	// ��������� ������ �� �������������� ������� ��� �������� ��������� ������ � �������������� ��������
	if ($mode == 'control') {
		$id = explode(',', $id);
		foreach ($id as $k => $v) if (intval($v) != 0) $id[$k] = intval($v); else unset($id[$k]);

		if (!empty($flag)) {
			if ($flag === 'changed_delete') {
				edost_class::ControlChanged('delete');
			}
			else if ($flag === 'paid') {
				// ���������� ������� � ������� ������ � ������ 'control_status_completed'
				$ar = edost_class::GetControlShipment($id);
				if (!empty($ar['data'])) foreach ($ar['data'] as $k => $v) {
					$config = CDeliveryEDOST::GetEdostConfig($v['site_id']);
					$props = edost_class::GetProps($v['order_id']);
					if (empty($props['cod'])) continue;

					$a = false;
					$order = \Bitrix\Sale\Order::load($v['order_id']);
					if (empty($props['paid'])) {
						$a = true;
						$payment = $order->getPaymentCollection()->getItemById($props['payment_id']);
						$payment->setPaid('Y');
					}
					if ($config['control_status_completed'] != '' && $v['order_status'] != $config['control_status_completed']) {
						$a = true;
						$order->setField('STATUS_ID', $config['control_status_completed']);
					}
					if ($a) $order->save();
				}
			}
			else {
				// ��������� ����� ��������
				$ar = array('flag' => $flag);
				if ($flag == 'order_date') $ar['date'] = (isset($_POST['date']) ? preg_replace("/[^0-9.]/i", "", substr($_POST['date'], 0, 20)) : '');

				$ar = edost_class::Control($id, $ar);
				edost_class::Control('clear_cache_flag');
			}
//			echo '<br><b>control:</b><pre style="font-size: 12px">'.print_r($ar, true).'</pre>';

			if (!empty($ar['error'])) echo CDeliveryEDOST::GetEdostError($ar['error']);
			else echo 'OK';
		}
		else if (!empty($id)) {
			$r = array();

			// ��������� ������ �������� (���, �������� � �����)
			$ar = array();
			$data = edost_class::GetShipmentData($id);
			edost_class::AddRegisterData($data);
			foreach ($data as $k => $v) if (!empty($v['props']['prop']['PACKAGE']['value'])) $ar[] = array(
				'id' => $v['id'],
				'package_formatted' => (!empty($v['package_formatted']) ? $v['package_formatted'] : ''),
				'option_formatted' => edost_class::GetPackageString($v, 'option', false),
				'prop' => $v['props']['prop']['PACKAGE']['value'],
			);
//			echo '<br><b>package:</b> <pre style="font-size: 12px">'.print_r($ar, true).'</pre>';
			if (!empty($ar)) $r[] = '"package": '. edost_class::GetJson($ar, array('id', 'package_formatted', 'option_formatted', 'prop'), true, false);

			// ��������� ������ ��������
			$ar = edost_class::GetControlShipment($id);
//			echo '<br><b>GetControlShipment:</b><pre style="font-size: 12px">'.print_r($ar, true).'</pre>';
			if (!empty($ar['data'])) {
				$tracking = false;
				foreach ($ar['data'] as $v) { $tracking = edost_class::GetTracking($v['site_id']); break; }

				foreach ($ar['data'] as $k => $v) {
					$v['status_full'] = edost_class::GetControlString($v);
					if (!empty($tracking['data'])) foreach ($tracking['data'] as $v2) if (in_array($v['tariff'], $v2['tariff'])) {
						$v['tracking_example'] = $v2['example'];
						$v['tracking_format'] = $v2['format'];
					}
					$ar['data'][$k] = $v;
				}

				$r[] = '"data": '.edost_class::GetJson($ar['data'], array('id', 'flag', 'tariff', 'tracking_code', 'status', 'status_full', 'control', 'tracking_example', 'tracking_format', 'shop_id', 'control_count', 'register'), true, false);
			}

			echo '{'.implode(', ', $r).'}';
		}
		else echo 'ERROR';
	}
}


require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
?>