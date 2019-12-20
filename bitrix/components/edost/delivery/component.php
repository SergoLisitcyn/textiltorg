<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

//$develop = true; // режим разработки !!!!!

if (!isset($arParams['MODE']) || !in_array($arParams['MODE'], array('sale.personal.order.detail', 'sale.personal.order.list', 'user_order', 'order_edit', 'detail', 'script', 'list', 'register', 'print', 'setting'))) return;

$mode = $arParams['MODE'];
$param = (isset($arParams['~PARAM']) && is_array($arParams['~PARAM']) ? $arParams['~PARAM'] : array());
$result = (isset($arParams['~RESULT']) && is_array($arParams['~RESULT']) ? $arParams['~RESULT'] : false);
$order_id = (isset($arParams['ORDER_ID']) ? intval($arParams['ORDER_ID']) : 0);
$shipment_id = (isset($arParams['SHIPMENT_ID']) ? intval($arParams['SHIPMENT_ID']) : 0);
$admin = (isset($arParams['ADMIN']) && $arParams['ADMIN'] == 'Y' ? true : false);
$history = (!empty($param['history']) ? $param['history'] : false);

if (!class_exists('edost_class')) {
	$s = 'modules/edost.delivery/classes/general/delivery_edost.php';
	$s = $_SERVER['DOCUMENT_ROOT'].getLocalPath($s);
	IncludeModuleLangFile($s);
	require_once($s);
}

$sign = GetMessage('EDOST_DELIVERY_SIGN');
$control_sign = GetMessage('EDOST_DELIVERY_CONTROL');
$admin_sign = GetMessage('EDOST_ADMIN');
$date = date('dmY');
$protocol = CDeliveryEDOST::GetProtocol();
$payment_select = '';
$config = CDeliveryEDOST::GetEdostConfig('all');
$cookie = edost_class::GetCookie();
$register_option = edost_class::GetRegisterOption();
$register_profile = edost_class::GetRegisterProfile();
$register_tariff = edost_class::GetRegisterTariff();

$ico_path = '/bitrix/images/delivery_edost_img';
$doc_path = $protocol.'edostimg.ru/img/doc2';
$img_path = $protocol.'edostimg.ru/img/site';
$edost_path = 'http://edost.ru/shop_edit.php?p=5&s=';

// скрипты и иконки работают по настройкам первого активного модуля
$c = false;
$config_old = false;
foreach ($config as $v) {
	if ($c === false) $c = $v;
	if (empty($v['param']['module_id'])) $config_old = true;
}
$script = edost_class::GetScriptData($c);
$template_ico = (isset($c['template_ico']) ? $c['template_ico'] : 'C');

$delimiter = '<div class="edost_delimiter"></div>';
$delimiter_button = '&nbsp;&nbsp;|&nbsp;&nbsp;';

$arResult = array(
	'component_path' => $componentPath,
	'component_path_locations' => str_replace('delivery', 'locations', $componentPath),
	'loading' => (!empty($param['loading']) ? $param['loading'] : 'loading_small.gif'),
);
if ($admin) {
	$arResult['loading'] = 'loading_small_admin.gif';
	$edost_locations = (CModule::IncludeModule('edost.locations') && property_exists('CLocationsEDOST', 'admin') && CLocationsEDOST::$admin == 1 ? true : false);
}

$loading_small = '<img style="vertical-align: middle;" src="'.$ico_path.'/'.$arResult['loading'].'" width="20" height="20" border="0">';
$loading_small2 = '<img style="vertical-align: middle; padding-bottom: 10px" src="'.$ico_path.'/'.$arResult['loading'].'" width="20" height="20" border="0">';
$ajax_file = $arResult['component_path'].'/edost_delivery.php';


// загрузка свойств заказа
if (!empty($order_id)) {
	$props = edost_class::GetProps($order_id);
//	echo '<br><b>props ('.$order_id.'):</b> <pre style="font-size: 12px">'.print_r($props, true).'</pre>';

	if (!empty($props['paysystem_list'])) {
		$s = '';
		foreach ($props['paysystem_list'] as $v) $s .= '<option value="'.$v['ID'].'"'.(!empty($v['cod']) ? ' data-edost_cod="Y"' : '').(!empty($v['checked']) ? 'selected' : '').'>['.$v['ID'].'] '.$v['NAME'].'</option>';
		$payment_select = '<select id="edost_payment" name="edost_payment" class="adm-bus-select" onchange="edost_ChangeDelivery()">'.$s.'</select>';
	}
}


// определние ID платежных систем с обработчиком наложенного платежа eDost
if ($admin || $mode == 'list') $payment_cod = edost_class::GetCodPaySystem();


// стили и js
if (in_array($mode, array('script', 'order_edit', 'list', 'register', 'sale.personal.order.detail', 'sale.personal.order.list', 'setting')) && empty($param['set']) && !isset($param['save'])) {
	$GLOBALS['APPLICATION']->SetAdditionalCSS($script['office_css']);
	if (in_array($mode, array('register', 'setting', 'order_edit'))) $GLOBALS['APPLICATION']->AddHeadString($script['office_js']);
	if ($mode == 'register') $GLOBALS['APPLICATION']->AddHeadString($script['profile_location']); // список городов для профилей оформления
	if ($mode == 'order_edit' && $edost_locations) $GLOBALS['APPLICATION']->AddHeadString($script['location']);

	$order_create = false;
	if ($mode == 'order_edit') {
		echo '<input id="edost_office_data" type="hidden" value=\'\'>';

		$edost_id = CDeliveryEDOST::GetAutomatic(true);

		if ($edost_locations) {
			if ($shipment_id == 0) $order_create = true;

			$props = (!$order_create ? edost_class::GetProps($shipment_id, array('shipment', 'shipment_all', 'no_payment')) : false);
//			echo '<br><b>props:</b> <pre style="font-size: 12px">'.print_r($props, true).'</pre>';

			$id = (!empty($props['location_code']) ? CSaleLocation::getLocationIDbyCODE($props['location_code']) : 0);
			$zip = '';

			$prop_remove = $prop_location = array();
			if ($order_create) {
				$ar = CSaleOrderProps::GetList(array(), array('ACTIVE' => 'Y'), false, false, array('ID', 'NAME', 'CODE'));
				while ($v = $ar->Fetch()) if (in_array($v['CODE'], array('LOCATION', 'ZIP', 'ADDRESS', 'ZIP_AUTO', 'CITY', 'METRO', 'PASSPORT'))) {
					$prop_remove[] = $v['ID'];
					if ($v['CODE'] == 'LOCATION') $prop_location[] = $v['ID'];
					if ($v['CODE'] == 'ZIP' && isset($_REQUEST['PROPERTIES'][$v['ID']])) $zip = $_REQUEST['PROPERTIES'][$v['ID']];
				}
			}

			$delivery_id = (!empty($props['delivery_id']) ? $props['delivery_id'] : 0);
			$prop2 = (!empty($props['prop']) ? CLocationsEDOST::SetProp2($props['prop']) : array());

			if ($order_create) {
				$prop = CLocationsEDOST::GetProp(isset($_REQUEST['SITE_ID']) ? htmlspecialcharsbx($_REQUEST['SITE_ID']) : '');

				if (!empty($_REQUEST['edost_shop_LOCATION'])) $id = $_REQUEST['edost_shop_LOCATION'];
				$prop['ZIP']['value'] = $zip;

				$ar = GetMessage('EDOST_LOCATIONS_ADDRESS');
				foreach ($ar as $k => $v) if (isset($_REQUEST['edost_'.$k])) $prop2[$k] = $_REQUEST['edost_'.$k];

				echo '<input id="edost_shop_LOCATION_CODE" name="'.$prop['LOCATION']['html_name'].'" type="hidden" value="'.(!empty($id) ? CSaleLocation::getLocationCODEbyID($id) : '').'">';
				$prop['LOCATION']['html_name'] = 'edost_shop_LOCATION';
			}
			else $prop = array(
				'LOCATION' => array('html_name' => 'edost_shop_LOCATION'),
				'ZIP' => array('html_name' => 'edost_shop_ZIP', 'value' => (isset($props['prop']['ZIP']['value']) ? $props['prop']['ZIP']['value'] : '')),
				'ADDRESS' => array(),
				'PASSPORT' => array(),
			);

			$edost_locations_param = array('ID' => $id, 'DELIVERY_ID' => $delivery_id, 'PROP' => $prop, 'PROP2' => $prop2, 'PARAM' => array('admin' => true));
?>
<div id="edost_location_admin_city_div" style="display: none;">
<div class="adm-bus-table-caption-title"<?=(!$order_create ? ' style="background: #eef5f5;"' : '')?>><?=$sign['location_head']?></div>
<table class="adm-detail-content-table edit-table" cellspacing="0" cellpadding="0" border="0" width="100%"><tr>
<td class="adm-detail-content-cell-l fwb" width="40%"></td>
<td id="edost_location_admin_city_td" class="adm-detail-content-cell-r">
<? $GLOBALS['APPLICATION']->IncludeComponent('edost:locations', '', array('MODE' => 'city') + $edost_locations_param, null, array('HIDE_ICONS' => 'Y')); ?>
</td></tr></table></div>

<div id="edost_location_admin_address_div" style="display: none;">
<input name="edost_location_admin" type="hidden" value="Y">
<div class="adm-bus-table-caption-title" style="background: #eef5f5;"><?=$sign['address_head']?></div>
<table class="adm-detail-content-table edit-table" cellspacing="0" cellpadding="0" border="0" width="100%"><tr>
<td class="adm-detail-content-cell-l fwb" width="40%"></td>
<td class="adm-detail-content-cell-r">
<div id="edost_office_address"></div>
<? $GLOBALS['APPLICATION']->IncludeComponent('edost:locations', '', array('MODE' => 'address') + $edost_locations_param, null, array('HIDE_ICONS' => 'Y')); ?>
</td></tr></table></div>

<div id="edost_location_admin_passport_div" style="display: none;">
<div class="adm-bus-table-caption-title" style="background: #eef5f5;"><?=$sign['passport_head']?></div>
<table class="adm-detail-content-table edit-table" cellspacing="0" cellpadding="0" border="0" width="100%"><tr>
<td class="adm-detail-content-cell-l fwb" width="40%"></td>
<td class="adm-detail-content-cell-r">
<? $GLOBALS['APPLICATION']->IncludeComponent('edost:locations', '', array('MODE' => 'passport') + $edost_locations_param, null, array('HIDE_ICONS' => 'Y')); ?>
</td></tr></table></div>
<?
		}
	}
?>

<?	if ($admin) { ?>
<style>
	.popup-window { z-index: 10561 !important; }
</style>
<?	} ?>

<script type="text/javascript">
	function edost_ShowDetail(id, mode) {

		if (mode == 'address_show') {
			id.style.display = 'none';
			id = BX.findNextSibling(id);
			id.style.display = 'block';
			return;
		}

		if (mode == 'all') {
			id.style.display = 'none';
			var E = BX.findNextSibling(id);
			if (E) E.style.display = 'block';
			return;
		}

		if (mode == 'hide') {
			edost_HideDetail(id);
			return;
		}

		var E = BX('edost_control_' + id + '_string');
		var E2 = BX('edost_control_' + id + '_detail');
		if (!E || !E2) return;

		E.style.display = 'none';
		E2.style.opacity = 1;
		E2.style.height = 'auto';
		E2.style.overflow = 'visible';
		E2.style.display = 'block';

		if (E2.innerHTML != '') return;

		E2.innerHTML = '<?=$loading_small2?>';
		BX.ajax.post('<?=$ajax_file?>', 'mode=detail&id=' + id, function(r) { E2.innerHTML = r; });

	}

	function edost_HideDetail(id, index, speed, height, scroll, scroll2) {

		var E2 = BX('edost_control_' + id + '_detail');
		if (!E2) return;

		if (index == undefined) {
			index = 100;
			speed = 0;
			height = E2.offsetHeight;
			E2.style.overflow = 'hidden';

			var p = BX.pos(E2);
			var p2 = BX.GetWindowScrollPos();
			scroll = (p2.scrollTop > p.top ? p2.scrollTop - p.top + <?=($mode == 'order_edit' ? '75' : '45')?> : 0);
			scroll2 = 0;
		}
		else if (index < 0) {
			var E = BX('edost_control_' + id + '_string');
			if (!E) return;

			E.style.display = 'block';
			E2.style.display = 'none';

			return;
		}

		E2.style.height = Math.round(height*index/100) + 'px';
		E2.style.opacity = index/100;
		if (scroll != 0) {
			var y1 = scroll - Math.round(scroll*index/100);
			var y2 = y1 - scroll2;
			scroll2 += y2;
			window.scrollBy(0, -y2);
		}

		if (speed < 5) speed += 0.5;
		index -= speed;

		window.setTimeout('edost_HideDetail(' + id + ', ' + index + ', ' + speed + ', ' + height + ', ' + scroll + ', ' + scroll2 + ')', 25);

	}

<?	// прокрутка вверх при переходе по якорю
	if ($mode == 'sale.personal.order.detail') { $anchor_up = (!empty($param['anchor_up']) ? intval($param['anchor_up']) : 20); ?>
	BX.ready(function() {
		var s = window.location.hash;
		if (s.indexOf('#shipment_') === 0) {
			s = s.substr(1);
			var E = BX('edost_' + s);
			if (!E) return;
			var p = BX.pos(E);
			var p2 = BX.GetWindowScrollPos();
			if (Math.abs(p.top - p2.scrollTop) < <?=$anchor_up?>) window.scrollBy(0, -<?=$anchor_up?>);
		}
	});
<?	} ?>

<?	if ($admin) { ?>
	var edost_data = false;
	var edost_control = [];
	var edost_shipment = false;
	var edost_shipment_edit = (window.location.href.indexOf('sale_order_shipment_edit.php?') > 0 ? true : false);
	var edost_order_create = (window.location.href.indexOf('sale_order_create.php?') > 0 ? true : false);
	var edost_office_open = false;
	var edost_alarm = true;
	var edost_reload = false;
	var edost_delivery_shop = false;
	var edost_user = false;

	var edost_register_data = '';
	var edost_transfer_bar;
	var edost_register_transfer = 0;
	var edost_register_transfer_set = -1;
	var edost_register_transfer_history = false;
	var edost_register_timer = 0;
	var edost_register_time_start = 0;
	var edost_register_time_end = 0;
	var edost_register_update = 0;

	var edost_input_timer;
	var edost_input_E, edost_input_value, edost_input_active;


	// фильтрация строк
	function edost_Filter(s, param) {
		s = s.replace(/;/g, ',').replace(/ /g, ',').replace(/^,+|,+$/gm, '').replace(/,+/g, ',');
		if (param == 'array') s = s.split(',');
		return s;
	}

	// поиск по идентификатору отправления
	function edost_Search(id, param, event) {

		var E = BX(id);
		if (!E) return;

		var s = id.split('_');
		var type = s[0];
		s.splice(0, 1);
		var value = s.join('_');

		if (param == 'start') {
			if (edost_search_timer != undefined) window.clearInterval(edost_search_timer);
			edost_search_value = edost_Filter(E.value);
			edost_search_timer = window.setInterval("edost_Search('" + id + "')", 100);
			return;
		}

		if (param == 'hide') {
			if (edost_search_timer != undefined) window.clearInterval(edost_search_timer);
			return;
		}

		if (param == 'keydown') {
			if (event.keyCode == 38 || event.keyCode == 13) if (event.preventDefault) event.preventDefault(); else event.returnValue = false;
			if (event.keyCode == 13) if (E.value != '') edost_SetParam(type, value);
			return;
		}

		var v = edost_Filter(E.value, 'array'); // удаление лишних символов и формирование массива с фразами
		if (value == 'search') for (var i = 0; i < v.length; i++) if (v[i].length < 3) { v.splice(i, 1); i--; } // удаление фраз длиной менее 3 букв

		// проверка на изменение в поисковом запросе
		var v2 = edost_search_value.split(',');
		if (v.length == v2.length) {
			var a = false;
			for (var i = 0; i < v.length; i++) if (v[i] != v2[i]) a = true;
			if (!a) return;
		}

//		if (edost_search_value == E.value && (value != 'search' || E.value.length < 3)) return;
//		if (edost_search_value == E.value || E.value.length < 1) return;
//		if (E.value.length < 1 || value == 'search' && E.value.length < 3) return;

		if (v[0].length == 0) return;

		edost_search_value = v.join(',');
		edost_SetParam(type, value);

	}

	// интеграция кода в страницу редактирования отгрузки
	function edost_InsertShipmentEdit(update) {

		var E = BX('SHIPMENT_ID_1');

		if (edost_order_create) {
			var E_paysystem = BX('PAY_SYSTEM_ID_1');
			if (E_paysystem && !E_paysystem.onchange) E_paysystem.onchange = new Function('', 'edost_ChangeDelivery(true)');
			if (!E && window.edost_UpdateLocation) window.edost_UpdateLocation('address');
		}

		if (!E) return;

		var id = E.value;
		if (update == undefined) update = true;
		edost_office_open = false;

		// получение отформатированных тарифов edost
		BX.ajax.post('<?=$ajax_file?>', 'mode=order_edit&id=' + id, function(data) {
			var E_delivery = BX('DELIVERY_1');
			var E_profile = BX('PROFILE_1');
			var E_price = BX('PRICE_DELIVERY_1');

			if (!E_delivery || !E_profile) {
				if (window.edost_UpdateLocation) window.edost_UpdateLocation('address');
				return;
			}

			var module_id = E_delivery.value;
			var profile_id = E_profile.value;
			var price = (E_price ? E_price.value : 0);

			if (profile_id != 0) {
				var E = E_profile.options[E_profile.selectedIndex];
				var profile_name = E.text;
			}

			var E = BX('edost_payment');
			var payment_index = (E ? E.selectedIndex : false);

			E_profile = BX.findParent(E_profile);

			var E = BX('edost_office_data');
			if (E) E.value = data;

			edost_data = data = (window.JSON && window.JSON.parse ? JSON.parse(data) : eval('(' + data + ')'));

			var E = BX('edost_paysystem_tr');
			if (E) BX.remove(E);
			var E = BX('edost_shipment_tr');
			if (E) BX.remove(E);

			if (module_id == data.module_id) {
				edost_shipment = false;
				edost_InsertControl();
			}
			else {
				edost_DrawControl();
				if (window.edost_UpdateLocation) window.edost_UpdateLocation('address');
				return;
			}

			// формирование списка с тарифами
			var s = '';
			var optgroup = false;
			var checked = false;
			var tariff_price = -1;
			var tariff_pricecash = -1;
			if (data.format != undefined) for (var i = 0; i < data.format.length; i++) {
				var v = data.format[i];
				if (v.head !== undefined) {
					if (optgroup) s += '</optgroup>';
					optgroup = true;
					s += '<optgroup label="' + v.head + '">';
					continue;
				}

				if (v.id == profile_id) {
					checked = true;
					if (v.price != undefined) tariff_price = v.price;
					if (v.pricecash != undefined) tariff_pricecash = v.pricecash;
				}

				s += '<option value="' + (v.id != '' ? v.id : data.zero_tariff) + '" ' + (v.id == profile_id || v.checked ? ' selected="selected"' : '');
				s += ' data-edost_profile="' + v.profile + '"';
				if (v.company_id != undefined) s += ' data-edost_company_id="' + v.company_id + '"';
				if (v.day != undefined) s += ' data-edost_day="' + v.day + '"';
				if (v.office_address_full != undefined) s += ' data-edost_address="' + v.office_address_full + '"';
				if (v.office_id != undefined) s += ' data-edost_office_id="' + v.office_id + '"';
				if (v.office_detailed != undefined) s += ' data-edost_office_detailed="' + v.office_detailed + '"';
				if (v.pricetotal_formatted === '0') v.pricetotal_formatted = '<?=$sign['free']?>';
				if (v.pricecash_formatted === '0') v.pricecash_formatted = '<?=$sign['free']?>';
				if (v.price != undefined) s += ' data-edost_price="' + v.price + '"';
				if (v.pricecash != undefined) s += ' data-edost_pricecash="' + v.pricecash + '"';
				if (v.office_mode != undefined) s += ' data-edost_office_mode="' + v.office_mode + '"';
				if (v.transfer_formatted != undefined && v.transfer_formatted != 0) s += ' data-edost_transfer_formatted="' + v.transfer_formatted + '"';
				if (v.priceinfo_formatted != undefined) s += ' data-edost_priceinfo_formatted="' + v.priceinfo_formatted + '"';
				if (v.error != undefined) s += ' data-edost_error="' + v.error + '"';
				if (v.tracking_example != undefined) s += ' data-edost_tracking_example="' + v.tracking_example + '"';
				if (v.tracking_format != undefined) s += ' data-edost_tracking_format="' + v.tracking_format + '"';
				s += '>' + v.title + (v.pricetotal_formatted != undefined ? ' - ' + v.pricetotal_formatted : '') + (v.pricecash_formatted != undefined ? ' (' + v.pricecash_formatted + ')' : '');
				s += '</option>';
			}
			if (optgroup) s += '</optgroup>';
			E_profile.innerHTML = '<select id="PROFILE_1" class="adm-bus-select" name="SHIPMENT[1][PROFILE]" onchange="edost_ChangeDelivery()">' + s + '</select>';

			// отключение блока "Расчетная стоимость доставки" (цена с кнопкой "применить")
			var E = BX('shipment_container_1');
			if (E) {
				E = BX.findChild(E, {'tag': 'tr', 'class': 'row_set_new_delivery_price'}, true);
				if (E) E.style.display = 'none';
			}

			var E = BX('BLOCK_PROFILES_1');
			if (E) {
				var E = BX.findParent(E);
				var cod = false;

<?				if (!empty($payment_select)) { ?>
				// выбор способа оплаты
				var s = '<td class="adm-detail-content-cell-l fwb"><?=$control_sign['paysystem']?>:</td><td class="adm-detail-content-cell-r"><?=$payment_select?></td>';
				E.appendChild( BX.create('tr', {'props': {'id': 'edost_paysystem_tr', 'innerHTML': s}}) );
				var E2 = BX('edost_payment');
				if (E2) {
					if (payment_index !== false) E2.options[payment_index].selected = true;
					if (E2.selectedIndex != -1 && E2.options[E2.selectedIndex].getAttribute('data-edost_cod') == 'Y') cod = true;
				}
<?				} ?>

				// поле для адреса пункта выдачи + дополнительная информация (error, warning, наценки за наложку, отформатированный адрес пункта выдачи)
				var s = '<td><input id="edost_address" name="edost_address" type="hidden" value=""></td><td id="edost_shipment_td"><?=(!$edost_locations ? '<div id="edost_office_address"></div>' : '')?><div id="edost_delivery_info"></div>';
				if (!checked && profile_id != 0 && edost_alarm) {
					var no_profile = '<?=$sign['no_profile']?>';
					s += '<div id="edost_alarm" style="padding-top: 10px; font-weight: bold; color: #e60;">' + no_profile.replace(/%profile%/g, profile_name).replace(/%price%/g, price) + '</div>';
				}
				else if (checked && profile_id != 0 && edost_alarm && tariff_price != -1 && (!cod && price != tariff_price || cod && price != tariff_pricecash && tariff_pricecash != -1)) {
					edost_alarm = 'price_change';
					var price_change = '<?=$sign['price_change']?>';
					s += '<div id="edost_alarm" style="padding-top: 10px; font-weight: bold; color: #e60;">' + price_change.replace(/%profile%/g, profile_name).replace(/%price%/g, price).replace(/%price2%/g, cod ? tariff_pricecash : tariff_price) + '</div>';
				}
				s += '</td>';

				E.appendChild( BX.create('tr', {'props': {'id': 'edost_shipment_tr', 'innerHTML': s}}) );
			}

			// включение блока выбора местоположений и адреса доставки
			if (!edost_order_create) {
				var E = BX('edost_location_admin_city_div');
				if (E) E.style.display = 'block';
				var E = BX('edost_location_admin_address_div');
				if (E) E.style.display = 'block';
				var E = BX('edost_location_admin_passport_div');
				if (E) E.style.display = 'block';
			}

			edost_ChangeDelivery(update);
			if (window.edost_UpdateLocation) window.edost_UpdateLocation('address');
		});

	}

	// выбор тарифа в выпадающем списке
	function edost_ChangeDelivery(update) {

		var E_office = BX('edost_office_address');
		var E_info = BX('edost_delivery_info');
		var E_alarm = BX('edost_alarm');
		var E_admin_city = BX('edost_location_admin_city_div');
		var E_admin_address = BX('edost_location_admin_address_div');
		var E_admin_passport = BX('edost_location_admin_passport_div');
		var E_location_address = BX('edost_location_address_div');
		var E_delivery = BX('DELIVERY_1');

		// проверка на доставку битрикса
		if (edost_delivery_shop !== 'location_updated') edost_delivery_shop = true;
		var ar = [<?=implode(', ', $edost_id)?>];
		if (E_delivery) for (var i = 0; i < ar.length; i++) if (E_delivery.value == ar[i]) { edost_delivery_shop = false; break; }

		// замена названия доставки на 'eDost'
		if (update === 'head') {
			var ar2 = BX.findChildren(E_delivery, {'tag': 'option'}, true);
			for (var i = 0; i < ar2.length; i++) for (var i2 = 0; i2 < ar.length; i2++) if (ar2[i].value == ar[i2]) { ar2[i].innerHTML = '[' + ar2[i].value + '] eDost'; break; }
			return;
		}

		// обновление списка доставок
		if (update === 'service') {
<?			if ($edost_locations) { ?>
	        // перезагрузка местоположения при смене пользователя
	        if (edost_order_create) {
				var s = '';
	        	var ar = ['USER_ID', 'BUYER_PROFILE_ID', 'PERSON_TYPE_ID'];
				for (var i = 0; i < ar.length; i++) {
					var E = BX(ar[i]);
					s += (E ? E.value : '') + '_';
				}
				if (edost_user != s) {
					var s2 = edost_user;
					edost_user = s;
					if (s2 !== false) {
						BX('edost_location_city_div').innerHTML = '<?=$loading_small2?>';
						window.setTimeout('edost_InsertLocation(true)', 1000);
					}
					return;
				}
			}
<?			} ?>

			edost_alarm = false;
			edost_SetTracking('update', '', 1, edost_delivery_shop ? false : true);
			if (E_office) E_office.style.display = 'none';
			if (E_info) E_info.style.display = 'none';
			if (E_alarm) E_alarm.style.display = 'none';
			if (!edost_order_create) {
				if (E_admin_city) E_admin_city.style.display = 'none';
				if (E_admin_address) E_admin_address.style.display = 'none';
				if (E_admin_passport) E_admin_passport.style.display = 'none';
			}
			else if (window.edost_UpdateLocation) {
				if (E_location_address) E_location_address.style.display = 'block';
				if (edost_delivery_shop) window.edost_UpdateLocation('address', false, 0);
			}
			return;
		}
		else if (edost_alarm === 'price_change') edost_alarm = false;
		else if (update === undefined && !edost_alarm && E_alarm) E_alarm.style.display = 'none';

		var reload = false;
		if (update === 'reload') {
			update = true;
			reload = true;
		}

		var request = false;
		if (update == undefined) {
			update = true;
			request = true;
			edost_office_open = true;
		}

		var office_window = true;
		if (update == 'office_esc') {
			update = false;
			office_window = false;
		}

		if (edost_delivery_shop) return;

		var E = BX('PROFILE_1');
		if (!E) return;

		var E_element = E.options[E.selectedIndex];
		var E_address = BX('edost_address');
		if (!E_element || !E_address) return;

		var cod = false;
		if (edost_order_create) {
			var E = BX('PAY_SYSTEM_ID_1');
			if (E && E.value != 0) {
				var ar = [<?=implode(', ', $payment_cod)?>];
				for (var i = 0; i < ar.length; i++) if (ar[i] == E.value) cod = true;
			}
		}
		else {
			var E = BX('edost_payment');
			if (E) {
				E = E.options[E.selectedIndex];
				if (E && E.getAttribute('data-edost_cod') == 'Y') cod = true;
			}
		}

		var company_id = E_element.getAttribute('data-edost_company_id');
		var profile = E_element.getAttribute('data-edost_profile');
		var price = E_element.getAttribute('data-edost_price');
		var pricecash = E_element.getAttribute('data-edost_pricecash');
		var address = E_element.getAttribute('data-edost_address');
		var office_id = E_element.getAttribute('data-edost_office_id');
		var office_detailed = E_element.getAttribute('data-edost_office_detailed');

		var tariff_id = (profile ? Math.ceil(profile/2) : 0);
		if (cod) price = (pricecash != undefined ? pricecash : 0);

		if (update) {
//			var E = BX('BASE_PRICE_DELIVERY_1');

			var E = BX('PRICE_DELIVERY_1');
			if (E) E.value = price;

			var E = BX('CALCULATED_PRICE_1');
			if (E) E.value = price;

			var E = BX('CUSTOM_PRICE_DELIVERY_1');
			if (E) E.value = 'Y';
		}

		edost_SetIco(tariff_id, company_id);

		if (!edost_order_create && E_admin_passport) {
			var a = false;
			var ar = [<?=implode(',', CDeliveryEDOST::$passport_required)?>];
			for (var i = 0; i < ar.length; i++) if (tariff_id == ar[i]) { a = true; break; }
			E_admin_passport.style.display = (a ? 'block' : 'none');
		}

		if (edost_reload) {
			edost_reload = false;
			BX.Sale.Admin.OrderAjaxer.sendRequest(BX.Sale.Admin.OrderEditPage.ajaxRequests.refreshOrderData());
			return;
		}

		var v1 = E_element.getAttribute('data-edost_tracking_example');
		var v2 = E_element.getAttribute('data-edost_tracking_format');
		edost_SetTracking(v1, v2);

		// вывод error, warning, pricecash и priceinfo
		var s = '';
		var error = '';

		if (cod) {
			if (pricecash != undefined) {
				var v = E_element.getAttribute('data-edost_transfer_formatted');
				if (v) s += '<div style="padding-top: 5px; color: #F00;"><?=str_replace('%transfer%', "' + v + '", $sign['transfer'])?></div>';
			}
			else if (profile > 0) error += '<span style="padding: 2px 8px; background: #F00; color: #FFF;"><?=$sign['admin_no_cod']?></span>';
		}

		var v = E_element.getAttribute('data-edost_day');
		if (v) s += '<div style="padding-top: 5px;"><span style="color: #888;"><?=$sign['day_head']?>:</span> ' + v + '</div>';

		var v = E_element.getAttribute('data-edost_priceinfo_formatted');
		if (v) s += '<div style="padding-top: 5px;"><?=str_replace('%price_info%', "' + v + '", $sign['priceinfo_warning_bitrix'])?></div>';

		var v = E_element.getAttribute('data-edost_error');
		if (v) error += '<div style="padding-top: 5px;">' + v + '</div>';

		if (edost_data.warning) error += '<div style="padding-top: 5px;">' + edost_data.warning + '</div>';

		E_info.innerHTML = (error != '' ? '<div style="padding-top: 5px; color: #F00; font-weight: bold; font-size: 12px;">' + error + '</div>' : '') + s;

		if (!edost_order_create && update && window.edost_UpdateLocation) window.edost_UpdateLocation('address');

		// вывод адреса
		E_address.value = (address != undefined ? address : '');
		E_office.style.display = (address != undefined ? 'block' : 'none');

		if (E_location_address) E_location_address.style.display = (address != undefined && profile ? 'none' : 'block');

		if (address != undefined && profile) {
			if (address == 'new') s = '<?=$loading_small?>';
			else {
				var ar = address.split(', <?=$sign['code']?>: ');
				if (ar[1] == undefined)	s = '<b style="color: #F00;"><?=$sign['office_unchecked']?></b>';
				else {
					s = (office_id && office_detailed !== 'N' ? ' (<a class="edost_link" href="' + office_detailed + '" target="_blank"><?=$sign['map']?></a>)' : '');
					s = '<b style="color: #00A;">' + ar[0].replace(': ', '</b>' + s + '<br>').replace(', <?=$sign['tel']?>:', '<br>').replace(', <?=$sign['schedule']?>:', '<br>');
					var code = ar[1].split('/');
					if (code[0] != '' && code[0] != 'S' && code[0] != 'T') s += '<br><b><?=$sign['code']?>: ' + code[0] + '</b>';
				}
				var v = E_element.getAttribute('data-edost_office_mode');
				if (v) s += (s != '' ? '<br>' : '') + '<span style="cursor: pointer; color: #A00; font-size: 14px; font-weight: bold;" onclick="edost_office.window(\'' + v + '\');">' + (ar[1] == undefined ? '<?=$sign['change2']?>' : '<?=$sign['change']?>') + '</span>';
			}
			E_office.innerHTML = s;

			if (office_id) {
				if (address == 'new') {
					// отправка на сервер выбранного пункта выдачи
					var E = BX('SHIPMENT_ID_1');
					if (E) BX.ajax.post('<?=$ajax_file?>', 'mode=order_edit&id=' + E.value + '&office_id=' + office_id + '&profile=' + profile, function(r) {
						if (reload) edost_reload = true;
						if (edost_order_create) BX.Sale.Admin.OrderAjaxer.sendRequest(BX.Sale.Admin.OrderEditPage.ajaxRequests.refreshOrderData());
						else BX.Sale.Admin.OrderShipment.prototype.getDeliveryPrice();
					});
					return;
				}

				if (!(edost_order_create && request)) return;
			}
		}

		if (office_window && edost_office_open && (profile == 'shop' || profile == 'office' || profile == 'terminal')) {
			request = false;
			edost_office.window(profile, true);
		}
		edost_office_open = true;


		if (edost_order_create && request) BX.Sale.Admin.OrderAjaxer.sendRequest(BX.Sale.Admin.OrderEditPage.ajaxRequests.refreshOrderData());

	}

	// выбор пункта выдачи
	function edost_SetOffice(profile, id, cod, mode) {

		edost_alarm = false;

		if (id == undefined) {
			edost_ChangeDelivery('office_esc');
			return;
		}

		var E = BX('PROFILE_1');
		if (!E) return;

		var E_element = E.options[E.selectedIndex];

		var v = profile.split('_');
		if (v[1] == undefined) return;

		E.value = E_element.value = v[1];
		E_element.setAttribute('data-edost_profile', v[0]);
		E_element.setAttribute('data-edost_address', 'new');
		E_element.setAttribute('data-edost_office_id', id);

		edost_ChangeDelivery(edost_order_create ? 'reload' : true);

	}

	// вывод примера и формата накладной доставки
	function edost_SetTracking(example, format, index, visible) {

		if (index == undefined) index = 1;
		if (visible == undefined) visible = true;

		var E = BX('edost_tracking_example_' + index);
		if (example == 'update') {
			if (E) E.style.display = (visible ? 'block' : 'none');
			return;
		}
		if (E) BX.remove(E);

		var ar = document.getElementsByName('SHIPMENT[' + index + '][TRACKING_NUMBER]');
		if (ar) for (var i = 0; i < ar.length; i++) {
			var E = BX.findParent(ar[i]);
			var s = '';
			if (example) s += '<span style="color: #888;"><?=$control_sign['tracking_example']?>:</span> ' + example;
			if (format) s += (s != '' ? '<br>' : '') + '<span style="color: #888;"><?=$control_sign['tracking_format']?>:</span> ' + format;
			if (E) E.appendChild( BX.create('div', {'props': {'id': 'edost_tracking_example_' + index, 'style': 'padding-top: 5px;' + (!visible ? ' display: none;' : ''), 'innerHTML': s}}) );
		}

	}

	// установка иконки тарифа
	function edost_SetIco(id, company_id, shipment_id, index) {

		if (!edost_shipment_edit && !edost_order_create) return;

		if (!id) id = 0;
		if (index == undefined) index = 1;

		var E = BX('delivery_service_logo_' + index);
<?		if ($template_ico == 'C') { ?>
		if (id == 0) company_id = 0;
		if (id >= 31 && id <= 34) company_id = 'v' + (id - 30);
		if (E) E.style = 'background: url("<?=$ico_path?>/company/' + company_id + '.gif"); background-size: contain;';
<?		} else { ?>
		if (E) E.style.background = 'url("<?=$ico_path?>/big/' + id + '.gif")';
<?		} ?>

		if (!shipment_id) return;

		var E = BX('sale-admin-order-icon-shipment-' + shipment_id);
		if (E) E.style.background = 'url("<?=$ico_path?>/' + id + '.gif")';

	}

	// распарсивание параметра в адресе документа "...?name=1&..."
	function edost_UrlParam(name) {
		var s = window.location.search.split('&' + name + '=');
		return (s[1] != undefined ? s[1].split('&')[0] : '');
	}

	// установка параметров в админке + запись куки + обновление блока контроля
	function edost_SetParam(param, value, value2) {
//		alert(param + '|' + value);

		if (param == 'control' && value == 'register') {
			if (window.history && window.history.pushState) window.history.pushState(null, null, '/bitrix/admin/edost.php?lang=<?=LANGUAGE_ID?>&type=register');
			BX('edost_control_data_div').innerHTML = '<div class="edost_map_loading2"><img src="<?=$ico_path?>/loading.gif" border="0" width="64" height="64"></div>';
			window.location.reload();
			return;
		}

		var post = '';
		var get = '';
		var company = '';
		var type = param.split('_')[0];
		if (value === true) value = 'Y';
		if (value === false) value = 'N';

		var main = false;
		if (value != 'reload' && value != 'reload_full')
			if (param == 'control' || param == 'register') main = true;
			else {
				edost_UpdateCookie(param, value); // функция из admin/edost.php !!!

				// блок настроек на странице контроля заказов и оформления доставки
				if (param == 'control_setting' || param == 'register_setting') {
					var a = (value == 'Y' ? true : false);
					BX('control_setting_show').style.display = (!a ? 'inline' : 'none');
					BX('control_setting_hide').style.display = (a ? 'inline' : 'none');
					BX('control_setting').style.display = (a ? 'block' : 'none');
					return;
				}

				value = '';

				if (location.href.indexOf('type=register&control=search') > 0) {
					main = true;
					value = location.href.split('&control=')[1];
				}
			}

		if (main) {
			// поиск на странице контроля и оформления
			var s = value.split('_');
			if (s[0] == 'search') post = get = '&search=' + encodeURIComponent(edost_search_value);
			else if (s[0] == 'history') {
				var E = BX('edost_history');
				post = get = '&id=' + E.value;
			}


			if (param == 'register')
				if (s[0] == 'company') {
					company = s[1];
					value = (value2 != undefined ? value2 : '');
				}
				else company = edost_UrlParam('company');
		}

		var E = BX('edost_data_div');
		if (!E) return;

		var p = BX.pos(E);
		var p2 = BX.GetWindowScrollPos();
		var x = p.top - p2.scrollTop;
		if (x < 0) window.scrollBy(0, x);

		BX('edost_control_data_div').innerHTML = '<div class="edost_map_loading2"><img src="<?=$ico_path?>/loading.gif" border="0" width="64" height="64"></div>';

		if (!main) {
			if (value == 'reload_full') {
				value = '';
				post += '&clear_cache=Y';
				E2 = BX('edost_reload');
				if (E2) E2.style.display = 'none';
			}
			if (param == 'register' && company == '') company = edost_UrlParam('company');

			value = edost_UrlParam('control');
			if (value == 'history') value += '&id=' + edost_UrlParam('id');
			if (value == 'search_order' || value == 'search_shipment') value += '&search=' + edost_UrlParam('search');
		}
		else if (window.history && window.history.pushState) window.history.pushState(null, null, '/bitrix/admin/edost.php?lang=<?=LANGUAGE_ID?>&type=' + type + (company != '' ? '&company=' + company : '') + (value != '' ? '&control=' + value : '') + get);

		post = 'type=' + type + '&ajax=Y' + (value != '' ? '&control=' + value : '') + (company != '' ? '&company=' + company : '') + post;
		BX.ajax.post('/bitrix/admin/edost.php', post, function(r) {
			E.innerHTML = r;
			if (param.split('_')[0] == 'register') edost_Register('active_all_update');
		});

	}


	function edost_Digit(v) {
		if (v == 0) v = '';
		return v;
	}

	function edost_InputPosition(x, y) {
		if (x == 0 && y == 0) edost_input_position = {'x': 0, 'y': 0};
		if (y > 0) edost_input_position.x = 0;
		edost_input_position.x += x;
		edost_input_position.y += y;
		return edost_input_position.x + '_' + edost_input_position.y;
	}

	function edost_getCoords(elem) {
		var box = elem.getBoundingClientRect();
		return {top: box.top + pageYOffset, left: box.left + pageXOffset};
	}


	var edost_package_onkeydown_backup = 'free', edost_package_overflow_backup = 'free', edost_package_onresize_backup = 'free';
	var edost_ajax_id, edost_package_id, edost_package_tariff, edost_package_item, edost_package_box, edost_package_item_mode, edost_input_position, edost_scroll = [0, 0];
	var edost_mouse = {'move': false, 'checkbox': false};

	function edost_Setting(mode, param, value) {

		if (mode == 'profile_setting_new' || mode == 'profile_setting_change') mode = 'profile_setting';

		var post = 'mode=' + mode;
		if (param == 'get') post += '&id=' + value;
		if (param == 'save' && value != '') post += '&data=' + encodeURIComponent(value);
		if (param == 'post') post += '&' + value;

		edost_window.resize('loading');
		BX.ajax.post('<?=$ajax_file?>', post, function(r) { edost_window.resize('set', r); });

	}

	function edost_Package(param, value, value2) {
//		console.log('edost_Package: ' + param + ' | ' + value + ' | ' + value2);

		var input_function = 'type="input" onfocus="edost_Package(\'input_focus\', this)" onblur="edost_Package(\'input_blur\')" onkeydown="edost_Package(\'input_keydown\', this, event)"';

		// загрузка параметров при первом открытии
		if (param == 'show') {
			edost_package_id = value;

			var E = BX('edost_package_error_' + edost_package_id.split('_')[1]);
			edost_package_tariff = (E ? E.getAttribute('data-tariff') : false);

			var E = document.getElementById('edost_package_' + value + '_item_data');
			if (E) {
				edost_package_item = (window.JSON && window.JSON.parse ? JSON.parse(E.value) : eval('(' + E.value + ')'));
				for (var i = 0; i < edost_package_item.length; i++) {
					edost_package_item[i].hide = (edost_package_item[i].hide == 1 ? true : false);
					edost_package_item[i].value = [];
					edost_package_item[i].remain = edost_package_item[i].QUANTITY;
				}
//				edost_ShowData(edost_package_item, '', 20);
			}

			var E = document.getElementById('edost_package_' + value + '_box_data');
			if (E) {
				edost_package_box = [{"item": []}];

				ar = E.value.split(':');
				var n = ar.length;
				for (var i = 0; i < n; i++) {
					s = ar[i].split(',');
                    var v = {"shipment_id": s[0], "weight": s[1], "size": s[2].split('x'), "insurance": s[3], "cod": s[4], "item": []};

                    var item = s[5].split('/')
					for (var i2 = 0; i2 < item.length; i2++) {
						s = item[i2].split(';');
    	                v.item.push({"id": s[0], "quantity": s[1]});
					}

					if (n == 1) edost_package_box[0] = v;
					else edost_package_box.push(v);
				}

				for (var i = 0; i < edost_package_box.length; i++)
					for (var i2 = 0; i2 < edost_package_box[i].item.length; i2++) {
						var v = edost_package_box[i].item[i2];
						for (var i3 = 0; i3 < edost_package_item.length; i3++) if (edost_package_item[i3].ID == v.id) {
							edost_package_box[i].item[i2] = [i3, v.quantity, v.quantity];

							edost_package_item[i3].value.push([i, v.quantity]);
							edost_package_item[i3].remain -= v.quantity;
						}
					}

				for (var i = 0; i < edost_package_item.length; i++) {
					var v = edost_package_item[i];
					if (v.remain > 0) {
						var n = v.value.length - 1;
						if (v.remain == 1) {
							if (n > 0 && v.value[n][1] == 0) v.value[n][1] = 1;
							else v.value.push([0, 1]);
							v.remain = 0;
						}
						else if (n < 0 || v.value[n][1] != 0) v.value.push([0, 0]);
					}
					if (v.value.length == 0) v.value = [[0, v.QUANTITY == 1 ? 1 : 0]];
				}

				for (var i = 0; i < edost_package_item.length; i++)
					if (!edost_package_item[i].hide && edost_package_item[i].remain > 0) edost_package_box[0].item.push([i, edost_package_item[i].remain, edost_package_item[i].remain]);

				if (edost_package_item_mode === undefined) edost_package_item_mode = <?=($cookie['register_package_item'] == 'Y' ? 'true' : 'false')?>;

//				edost_Package('update_box', 'start');
//				edost_ShowData(edost_package_box, '', 20);
			}
		}

		// сохранение
		if (param == 'save') {
			if (value == undefined) {
				// места
				for (var i = 1; i < edost_package_box.length; i++) if (edost_package_box[i] && !edost_package_box[i].hide && edost_package_box[i].error) return;

				var option = false;
				var id = edost_package_id.split('_')[1];
				var order_id = edost_package_id.split('_')[0];

				var s = [];
				for (var i = 1; i < edost_package_box.length; i++) if (edost_package_box[i] && !edost_package_box[i].hide && edost_package_box[i].item.length > 0) {
					var s2 = [];
					for (var i2 = 0; i2 < edost_package_box[i].item.length; i2++) s2.push(edost_package_item[ edost_package_box[i].item[i2][0] ].ID + ';' + edost_package_box[i].item[i2][1]);
					s.push(id + ',' + edost_package_box[i].weight + ',' + edost_package_box[i].size.join('x') + ',,,' + s2.join('/'));
				}
	            var data = s.join(':');

				edost_ajax_id = document.getElementById('edost_package_' + id);
			}
			else {
				// опции
				var option = true;
	            var id = value2.id;
	            var order_id = value2.order_id;
	            var data = value;

				edost_ajax_id = document.getElementById('edost_option_' + id);
			}

			if (!edost_ajax_id) return;

			edost_ajax_id.style.height = edost_ajax_id.offsetHeight + 'px';
			edost_ajax_id.innerHTML = '<?=$loading_small?>';
			BX.ajax.post('<?=$ajax_file?>', 'mode=package&order_id=' + order_id + '&id=' + id + '&data=' + encodeURIComponent(data) + (option ? '&option=Y' : ''), function(r) {
				edost_ajax_id.style.height = 'auto';
				edost_ajax_id.innerHTML = r;
				edost_Register('active_all_update');
			});

			if (value != undefined) return;
		}

		// изменение режима распределения: по местам / по списку
		if (param == 'mode') {
			if (value == 'update') {
				var E = document.getElementById('edost_package_setting_item');
				E.className = (edost_package_item_mode ? 'edost_button_big_on' : 'edost_button_big_off');
				if (value2 == 'start') return;
				param = 'show';
			}
			else {
				edost_package_item_mode = (value == 'item' ? true : false);
				edost_UpdateCookie('register_package_item', value == 'item' ? 'Y' : 'N');
				edost_Package('mode', 'update');
				return;
			}
		}


		// адаптивность
		if (param == 'resize') {
			var browser_w = (document.documentElement.clientWidth == 0 ? document.body.clientWidth : document.documentElement.clientWidth);
			var browser_h = (document.documentElement.clientHeight == 0 ? document.body.clientHeight : document.documentElement.clientHeight);

			browser_width = browser_w;
			browser_height = browser_h;

			window_w = 1000;
			window_h = (browser_h > 1100 ? 1000 : 800);

			var fullscreen = false;
			if (window_w > browser_w-100 || window_h > browser_h-100) {
				fullscreen = true;
				window_w = browser_w;
				window_h = browser_h;
			}

			document.body.style.overflow = (fullscreen ? 'hidden' : edost_package_overflow_backup);

			var E = document.getElementById('edost_package_window');
			E.style.width = window_w + 'px';
			E.style.height = window_h + 'px';
			E.style.left = Math.round((browser_w - window_w)*0.5) + 'px';
			E.style.top = Math.round((browser_h - window_h)*0.5) + 'px';
			E.style.borderRadius = (fullscreen ? 0 : '8px');

			var E_item = document.getElementById('edost_package_window_item');
			E_item.style.display = (edost_package_item_mode ? 'block' : 'none');
			E_item.style.height = 'auto';

			var E2 = document.getElementById('edost_package_window_item_head');
			E2.style.display = (edost_package_item_mode ? 'block' : 'none');

			var h = 0;
			var h0, h1, h2;

			var E2 = document.getElementById('edost_package_window_box');
			if (edost_package_item_mode) h = Math.round(E.offsetHeight - E_item.getBoundingClientRect().top + E.getBoundingClientRect().top - 20);
			else h = Math.round(E.offsetHeight - E2.getBoundingClientRect().top + E.getBoundingClientRect().top - 20);

			var E2 = E.querySelector('div.edost_window_close');
			E2.style.margin = (fullscreen ? '8px 8px 0 1px' : '4px 4px 0 1px');
			E2.style.position = 'absolute';
			E2.style.left = window_w - (fullscreen ? 32 : 29) + 'px';

			var E_button = document.getElementById('edost_package_window_button');
			E_button.style.display = (edost_package_item_mode && E_button.innerHTML != '' ? 'block' : 'none');

			var E_box = document.getElementById('edost_package_box_div');
			E_box.style.width = (browser_w > 1050 && !fullscreen ? '50%' : '490px');
			E_box.style.height = 'auto';
			E_box.style.display = (edost_package_item_mode ? 'none' : 'inline-block');
			if (edost_scroll[0] > 0) E_box.scrollTop = edost_scroll[0];

			if (edost_package_item_mode) {
				h0 = E_item.offsetHeight;
				if (h0 > h*0.5) h0 = Math.round(h*0.5);
				h2 = h - h0 - E_button.offsetHeight - 40;
				E_item.style.height = h0 + 'px';
			}
			else {
				if (browser_w > 1050) h1 = h2 = h;
				else {
					h1 = E_box.offsetHeight;
					if (h1 > h/2) h1 = Math.round(h/2);
					h2 = h - h1;
				}
				E_box.style.height = h1 + 'px';
			}
			var E = document.getElementById('edost_package_box_div_in');
			E.style.marginRight = (E_box.scrollHeight == E_box.offsetHeight ? '17px' : '');
			E.style.marginLeft = '17px';

			var E_box2 = document.getElementById('edost_package_box_div2');
			var w2 = (browser_w > 1050 && !fullscreen ? '50%' : '490px');
			E_box2.style.width = (edost_package_item_mode ? '100%' : w2);
			E_box2.style.height = h2 + 'px';

			if (edost_scroll[1] > 0) E_box2.scrollTop = edost_scroll[1];

			var E = document.getElementById('edost_package_box_div2_in');
			E.style.marginRight = (E_box2.scrollHeight == E_box2.offsetHeight ? '17px' : '');
			E.style.marginLeft = '17px';

			var a = (browser_w > 600 ? true : false);
			var E = document.getElementById('edost_package_window_head_div_right');
			E.className = (a ? 'edost_package_window_head edost_package_window_head_delimiter' : 'edost_package_window_head2');

			var E = document.getElementById('edost_package_window_head_div_left');
			E.className = (a ? 'edost_package_window_head' : '');

			edost_scroll = [0, 0];

			return;
		}


		// сброс распределения
		if (param == 'clear') {
			for (var i = 0; i < edost_package_item.length; i++) if (edost_package_item[i] && (value === undefined || i == value)) {
				v = edost_package_item[i];
				v.value = [[0, 0]];
				v.remain = v.QUANTITY;
			}

			param = 'show';
		}

		// разбить товар по местам
		if (param == 'cut') {
			v2 = value.split('|');
			value = v2[0];
			value2 = v2[1]*1;

			v = edost_package_item[value];
            if (value2 > v.remain*1) value2 = v.remain;

			var n = Math.floor(v.remain / value2);
			for (var i = 0; i < v.value.length; i++) if (v.value[i][1] == 0) { v.value[i][1] = value2; n--; }
			if (n > 0) for (var i = 0; i < n; i++) v.value.push([0, value2]);

			var n = v.remain % value2;
			if (n != 0)	v.value.push([0, n]);

			v.remain = 0;

			param = 'show';
		}

		// установить вес товаров в поле для веса коробки
		if (param == 'set_weight') {
			v = edost_package_box[value];
			v.weight = v.item_weight;

			param = 'show';
		}

		// поместить оставшиеся товары в свободную ячейку
		if (param == 'remain') {
			if (edost_package_item[value] && edost_package_item[value].remain > 0) {
				v = edost_package_item[value];
				for (var i = 0; i < v.value.length; i++) if (v.value[i][1] == 0) {
					v.value[i][1] = v.remain;
					v.remain = 0;
					break;
				}
			}

			param = 'show';
		}

		// поместить оставшиеся товары в место
		if (param == 'put') {
			value = value.split('|')[1];
			for (var i = 0; i < edost_package_item.length; i++) {
				v = edost_package_item[i];
				if (!v.hide && v.remain > 0) {
					var a = false;
					for (var i2 = 0; i2 < v.value.length; i2++) if (v.value[i2][0] == 0 && v.value[i2][1] == 0) { a = i2; break; }
					if (a === false) a = v.value.length - 1;
					v.value[a] = [value, v.remain];
					v.remain = 0;
				}
			}

			param = 'show';
		}

		// поместить товары у которых задано количество в место
		if (param == 'put2') {
			value = value.split('|')[1];
			for (var i = 0; i < edost_package_item.length; i++) {
				var v = edost_package_item[i];
				if (!v.hide) for (var i2 = 0; i2 < v.value.length; i2++) if (v.value[i2][0] == 0 && v.value[i2][1] != 0) v.value[i2][0] = value;
			}
			param = 'show';
		}

		// разместить оставшиеся товары по указанным местам
		if (param == 'put3') {
			for (var i = 0; i < edost_package_item.length; i++) {
				var v = edost_package_item[i];
				if (!v.hide) for (var i2 = 0; i2 < v.value.length; i2++) if (v.value[i2][0] != 0 && v.value[i2][1] == 0) {
					v.value[i2][1] = v.remain;
					v.remain = 0;
					break;
				}
			}

			param = 'show';
		}

		// переложить из одного места в другое
		if (param == 'put4') {
			s = value.split('|');
			var f = s[0];
			var t = s[1];
			var item = (s[2] != undefined ? s[2]*1 : false);

			var v = edost_package_box[f];
			for (var i = 0; i < v.item.length; i++) if (item === false || i === item) {
				var E_active = document.getElementById('box_' + f + '_active_' + i);

				var checked = E_active.checked;
				if (item === false && !checked && t != f) continue;
				var count = (!checked && t == f ? 0 : v.item[i][1]*1);
				if (count > v.item[i][2]*1) count = v.item[i][2]*1;

				var v2 = edost_package_item[ v.item[i][0] ];
				var ar = [];
				var n = 0; // товаров в остальных местах
				var w = 0; // товаров в месте "куда"
				for (var i2 = 0; i2 < v2.value.length; i2++)
					if (v2.value[i2][0] == 0 || v2.value[i2][0] == f || v2.value[i2][0] == t) {
						if (t != 0 && v2.value[i2][0] == t) w += v2.value[i2][1]*1;
					}
					else {
						n += v2.value[i2][1]*1;
						ar.push(v2.value[i2]);
					}

				if (t == f) w = 0;
				if (t > 0 && count != 0) {
					ar.push([t, count + w]);
					n += w;
				}
				if (f > 0 && count < v.item[i][2]*1) {
					ar.push([t != f ? f : 0, v.item[i][2] - count]);
					n += v.item[i][2] - count;
				}

				s = n + w + (t != 0 ? count : 0);
					if (t != f && s < v2.QUANTITY*1) ar.push([0, 0]);

				v2.value = ar;
			}

			param = 'show';
		}

		// разнести по местам начиная с
		if (param == 'put5') {
			var s = value.split('|');
			var v = edost_package_item[s[0]];
			var n = s[1];
			for (var i = 0; i < v.value.length; i++) if (v.value[i][0] == 0) {
				v.value[i][0] = n;
				n++;
			}

			param = 'show';
		}

		// разделить/собрать комплект
		if (param == 'unpack' || param == 'pack') {
			for (var i = 0; i < edost_package_item.length; i++) {
				v = edost_package_item[i];
				var a = (param == 'pack' ? true : false);
				if (v.ID == value) v.hide = !a;
				if (v.SET_PARENT_ID == value) v.hide = a;
			}

			param = 'show';
		}


		// обновление кнопок и выделение ячеек зеленым/красным в списке товаров
		if (param == 'update_item_list') {
			for (var i = 0; i < edost_package_item.length; i++) if (!edost_package_item[i].hide && (value == undefined || value == i)) {
				var v = edost_package_item[i];

				var E = document.getElementById('edost_package_item_' + i + '_button');
				E.innerHTML = edost_Package('draw_button', i);

				var c = 'on', count_green = false;
				if (v.remain < 0) c = 'error'
				else if (v.remain == 0) {
					c = 'green';
					count_green = true;
				}
				for (var i2 = 0; i2 < v.value.length; i2++) {
					var E2 = document.getElementById('edost_package_input_' + i + '_count_' + i2);
					if (E2) E2.className = 'edost_package_' + (count_green && v.value[i2][1] == 0 ? 'error' : c);
				}

				var box = true;
				for (var i2 = 0; i2 < v.value.length; i2++) if (v.value[i2][0] == 0) box = false;
				var c = (box && v.remain == 0 ? 'green' : 'on');
				for (var i2 = 0; i2 < v.value.length; i2++) {
					var E2 = document.getElementById('edost_package_input_' + i + '_box_' + i2);
					if (E2) E2.className = 'edost_package_' + (v.value[i2][0] > 100 || count_green && v.value[i2][1] == 0 ? 'error' : c);
				}
			}

			return;
		}

		// добавление/удаление ячеек в списке товаров
		if (param == 'update_item') {
			var code = (value ? value.getAttribute('data-code').split('_') : false);
			var v = (code !== false ? edost_package_item[code[0]] : false);

			if (code[1] == 'count') {
				var count = v.value[code[2]][1];

				var n = v.QUANTITY;
				var a = true;
				for (var i = 0; i < v.value.length; i++) {
					if (v.value[i][1] == 0) a = false;
					n -= v.value[i][1];
				}
				v.remain = n;

				var E = edost_input_E.parentNode.parentNode.parentNode;

				var E2 = BX.findNextSibling(E);
				if (E2) {
					E2.innerHTML = edost_Digit(v.remain);
					E2.style.color = (v.remain < 0 ? '#F00' : '#000');
					E2.style.cursor = (v.remain == 0 ? 'default' : 'pointer');
				}

				if (count != 0 && n > 0 && a) {
					v.value.push([0, 0]);

					var E2 = document.createElement('DIV');
					E2.style = 'padding: 2px 0;';
					E2.innerHTML = edost_Package('draw_item_input', code[0] + '|' + (v.value.length-1));
					E.appendChild(E2);
				}
			}

			// удаление пустых ячеек
			var ar = [];
			for (var i = 0; i < v.value.length; i++) {
				if (v.value[i][0] == 0 && v.value[i][1] == 0 && (v.remain == 0 || i != 0 && v.value[i][0] == 0 && v.value[i][1] == 0 && v.value[i-1][0] == 0 && v.value[i-1][1] == 0)) {
					var E = BX('edost_package_input_' + code[0] + '_count_' + i);
					if (E) {
						if (E.id == document.activeElement.id) edost_Package('input_keydown', E, {'keyCode': 38});
						E.parentNode.parentNode.remove();
					}
				}
				else ar.push(v.value[i]);
			}
			v.value = ar;

			edost_Package('update_item_list');
			edost_Package('update_save');
			edost_Package('update_box');

			return;
		}

		// генерация мест по списку товаров
		if (param == 'update_box' || param == 'show') {
			// обновление остатков товаров
			for (var i = 0; i < edost_package_item.length; i++) {
				var v = edost_package_item[i];
				var n = 0;
				for (var i2 = 0; i2 < v.value.length; i2++) n += v.value[i2][1]*1;
				v.remain = v.QUANTITY - n;
			}

			if (edost_package_item_mode) edost_Package('draw_button3');

			for (var i = 0; i < edost_package_box.length; i++) if (edost_package_box[i]) {
				edost_package_box[i].item = [];
				edost_package_box[i].hide = true;
			}

			var ar = [];
			for (var i = 0; i < edost_package_item.length; i++) if (!edost_package_item[i].hide) for (var i2 = 0; i2 < edost_package_item[i].value.length; i2++) {
				var box = edost_package_item[i].value[i2][0];
				var count = edost_package_item[i].value[i2][1];

				if (box == 0) continue;
				if (box > 100) box = 100;

				if (!edost_package_box[box]) edost_package_box[box] = {"id": box, "hide": false, "weight": 0, "size": [0, 0, 0], "item": []};

				var a = false;
				for (var u = 0; u < edost_package_box[box].item.length; u++) if (edost_package_box[box].item[u][0] == i) {
					edost_package_box[box].item[u][1] = edost_package_box[box].item[u][1]*1 + count*1;
					a = u;
					break;
				}
				if (a === false) {
					edost_package_box[box].item.push([i, count, 0]);
					a = edost_package_box[box].item.length - 1;
				}

				edost_package_box[box].hide = false;
			}

			for (var i = 0; i < edost_package_box.length; i++) if (edost_package_box[i])
				for (var i2 = 0; i2 < edost_package_box[i].item.length; i2++) edost_package_box[i].item[i2][2] = edost_package_box[i].item[i2][1];

			edost_package_box[0] = {"id": 0, "hide": false, "item": []};
			for (var i = 0; i < edost_package_item.length; i++) if (!edost_package_item[i].hide) {
				var n = 0;
				for (var i2 = 0; i2 < edost_package_item[i].value.length; i2++) if (edost_package_item[i].value[i2][0] > 0) n += edost_package_item[i].value[i2][1]*1;
				n = edost_package_item[i].QUANTITY - n;
				if (n > 0) edost_package_box[0].item.push([i, n, n]);
			}

			// добавление пустого места для ручного распределения
			if (!edost_package_item_mode) {
				var n = 0;
				for (var i = edost_package_box.length-1; i >= 0; i--) if (edost_package_box[i] && !edost_package_box[i].hide) { n = i+1; break; }
				if (n <= 0) n = 1;
				edost_package_box[n] = {"id": n, "hide": false, "weight": 0, "size": [0, 0, 0], "item": []};
			}

			// удаление веса и габаритов у пустых мест
			for (var i2 = 0; i2 < edost_package_box.length; i2++) if (edost_package_box[i2] && edost_package_box[i2].item.length == 0) {
				edost_package_box[i2].weight = 0;
				edost_package_box[i2].size = [0, 0, 0];
			}

//			edost_ShowData(edost_package_box, '', 20);

			if (param != 'show') {
				edost_Package('draw_box', 'all');
				return;
			}
		}

		// вывод кнопки "сохранить"
		if (param == 'update_save') {
			var a = true, error = false;
			for (var i = 0; i < edost_package_item.length; i++) if (!edost_package_item[i].hide) {
				var v = edost_package_item[i];
				if (v.remain != 0) a = false;
				for (var i2 = 0; i2 < v.value.length; i2++) if (v.value[i2][0] == 0) a = false;
				if (!a) break;
			}
			if (a) for (var i = 1; i < edost_package_box.length; i++) if (edost_package_box[i]) {
				var v = edost_package_box[i];
				if (!v.hide && v.item.length > 0 && (v.error || v.weight == 0)) {
					a = false;
					if (v.error) error = true;
				}
			}
			var c = (a ? '#0A0' : '#AAA');
			if (error) c = '#FAA';
			var E2 = document.getElementById('button_package_window_save');
			if (E2) E2.style.background = c;

			return;
		}

		// проверка на вес и габариты
		if (param == 'check') {
			if (!edost_package_tariff) return;

			for (var i = 1; i < edost_package_box.length; i++) if (edost_package_box[i] && !edost_package_box[i].hide && (value === undefined || i == value)) {
				var v = edost_package_box[i];
				var s = edost_CheckPackage(edost_package_tariff, v.weight, v.size);
				v.error = (s === true ? false : true);
				var E = document.getElementById('edost_package_box_error_' + i);
				if (E) E.innerHTML = (s === true ? '' : '<div style="padding-top: 5px;">' + s[0] + '</div>');
			}

			return;
		}

		// обработка ячеек
		if (param == 'update_input') {
			if (!edost_input_E || edost_input_E.value == edost_input_value) return;

			edost_input_value = edost_input_E.value;
			var code = edost_input_E.getAttribute('data-code').split('_');
			var p = edost_input_value.replace(/,/g, '.').replace(/[^0-9.]/g, '');
			var p2 = p.split('.')[0]; // целое число
			p = Math.round(p*1000)/1000;
			if (isNaN(p)) p = 0;

			if (code[2] == 'weight' || code[2] == 'size') {
				code[1] = code[1]*1;
				code[3] = code[3]*1;

				var v = edost_package_box[ code[1] ];

				if (code[2] == 'weight') v.weight = p;
				else v.size[ code[3] ] = p;

				var a = (p == 0 ? true : false);
				var s = (edost_input_E.getAttribute('data-code').indexOf('_size_') > 0 ? '_error2' : '_error');
				edost_input_E.className = edost_input_E.className.replace(a ? '_on' : s, a ? s : '_on');

				edost_Package('check', code[1]);
				edost_Package('update_save');

				return;
			}

			if (code[0] == 'box') {
				if (code[2] == 'count') edost_Package('update_box_item', edost_input_E);
				return;
			}

			if (code[1] == 'box' || code[1] == 'count') {
				v = edost_package_item[code[0]];
				v.value[code[2]][code[1] == 'box' ? 0 : 1] = (code[1] == 'box' ? p2 : p)
				edost_Package('update_item', edost_input_E);
			}

			return;
		}
		if (param == 'input_focus') {
			edost_input_E = value;
			edost_input_value = value.value;
			if (edost_input_timer != undefined) window.clearInterval(edost_input_timer);
			edost_input_timer = window.setInterval('edost_Package(\'update_input\')', 200);

			edost_Package('update_box_item', edost_input_E, 'focus');

			return;
		}
		if (param == 'input_blur') {
			edost_input_timer = window.clearInterval(edost_input_timer);
			edost_Package('update_input');

			edost_input_active = [-1, -1, -1, -1];
			edost_Package('update_box_item', edost_input_E);

			return;
		}
		if (param == 'input_keydown') {
			var event = value2;

			if (event.keyCode == 38 || event.keyCode == 13)
				if (event.preventDefault) event.preventDefault(); else event.returnValue = false;

			var x = 0, y = 0, id, s = [];

			if (event.keyCode == 37 && edost_input_E.selectionStart == 0) x = -1;
			if (event.keyCode == 39 && edost_input_E.selectionStart == edost_input_E.value.length) x = 1;
			if (event.keyCode == 38) y = -1;
			if (event.keyCode == 40) y = 1;

			if (x != 0 || y != 0 || event.keyCode == 13) {
				id = edost_input_E.id.split('_');
				s = edost_input_E.getAttribute('data-code').split('_');
				v = edost_package_item[s[0] === 'box' ? s[1] : s[0]];
			}

			if (event.keyCode == 13) {
				if (s[1] == 'cut' || s[1] == 'put' || s[1] == 'put2' || s[2] == 'put4' || s[1] == 'put5') {
					var manual = edost_input_E.value*1;
					if (isNaN(manual) || manual <= 0) return;

					if (s[2] == 'put4') edost_Package('put4', s[1] + '|' + manual);
					else edost_Package(s[1], (s[1] == 'cut' || s[1] == 'put5' ? s[0] : '') + '|' + manual);

					return;
				}

				if (s[2] == 'weight' || s[2] == 'size' || s[2] == 'count') x = 1;
				if (s[1] == 'box') x = 2;
				if (s[1] == 'count') {
					y = 1;
					s[1] = 'box';
				}
			}

			if (s[0] !== 'box') {
				if (x != 0) {
					if (x > 0 && s[1] == 'box' && (edost_input_E.selectionStart == edost_input_E.value.length || x == 2)) s[1] = 'count';
					else if (x < 0 && s[1] == 'count' && edost_input_E.selectionStart == 0) s[1] = 'box';
					else return;
				}
				if (y != 0) {
					for (var i = 0; i < edost_package_item.length; i++) {
						s[2] = s[2]*1 + y;
						if (s[2] < 0) {
							s[0]--;
							if (s[0] < 0) return;
							s[2] = edost_package_item[s[0]].value.length-1;
						}
						else if (s[2] > edost_package_item[s[0]].value.length-1) {
							s[0]++;
							if (s[0] > edost_package_item.length-1) return;
							s[2] = 0;
						}
						if (!edost_package_item[s[0]].hide) break;
					}
				}
			}
			if (x != 0 || y != 0) {
				if (s[0] === 'box') {
					for (var i2 = 0; i2 < 2; i2++) {
						if (i2 == 0) {
							id[3] = id[3]*1 + x*1;
							id[4] = id[4]*1 + y*1;
						}
						else {
							id[3] = 1;
							id[4] = id[4]*1 + x*1;
						}

						if (edost_Package('focus', id.join('_'), event)) return;
					}
				}
				else edost_Package('focus', 'edost_package_input_' + s.join('_'), event);
			}

			return;
		}

		// фокусировка на указанном input
		if (param == 'focus') {
			var event = value2;
			var E = document.getElementById(value);
			if (E) {
				if (event.preventDefault) event.preventDefault(); else event.returnValue = false;
				E.focus();
				E.setSelectionRange(-1, -1, 'none');
				return true;
			}
			else return false;
		}

		// обновление места
		if (param == 'update_box_item') {
			var code = value.getAttribute('data-code').split('_');
			if (code[2] == 'weight' || code[2] == 'size') return;

			console.log('update_box_item: ' + code);

			var v = edost_package_box[code[1]];
			if (code[2] == 'count') var item = edost_package_item[ v.item[code[3]][0] ];

			if (value2 == 'focus') edost_input_active = code;

			if (code[2] == 'count') {
				var E = document.getElementById('box_' + code[1] + '_active_' + code[3]);
				v.item[code[3]][1] = value.value*1;
				E.checked = (value.value == 0 ? false : true);
			}

			if (!v) return;

			var a = false;
			for (var i = 0; i < v.item.length; i++) {
				var E_table = document.getElementById('box_' + code[1] + '_table_' + i);
				var E_active = document.getElementById('box_' + code[1] + '_active_' + i);
				var E_count = BX.findChild(E_table, {'tag': 'input', 'attribute': {'type': 'input'}}, true);

				var checked = E_active.checked;

				if (!checked || v.item[i][1]*1 != v.item[i][2]*1) a = true;

				var c = ['edost_package_box_item'];
				var error = (checked && v.item[i][1]*1 > v.item[i][2]*1 ? true : false);
				var active = (edost_input_active && code[1] == edost_input_active[1] && i == edost_input_active[3] ? true : false);
				if (active) c.push('edost_package_box_item_active');
				if (checked) { if (!error) c.push('edost_package_box_item_on'); }
				else if (!active) c.push('edost_package_box_item_off');
				E_count.className = (error ? 'edost_package_error' : 'edost_package_on');
				E_table.className = c.join(' ');
			}

			var E = document.getElementById('box_' + code[1] + '_save');
			if (E) E.style.display = (a ? 'inline' : 'none');

			return;
		}

		// вывод кнопок в списке товаров
		if (param == 'draw_button') {
			v = edost_package_item[value];
			var s = '';

            if (v.QUANTITY > 1) {
				if (v.remain > 1) s += edost_Package('draw_button2', {"head": "<?=$control_sign['package']['cut']?>", "start": 1, "end": 5, "if": v.remain, "param": "cut", "value": value});

				var a = true;
				var n = 1;
				for (var i = 0; i < v.value.length; i++)
					if (v.value[i][0] == 0) a = false;
					else if (v.value[i][0]*1 >= n) n = v.value[i][0]*1 + 1;

				if (v.remain == 0 && !a) s += edost_Package('draw_button2', {"head": "<?=$control_sign['package']['put5']?>", "start": n, "end": n+4, "param": "put5", "value": value});

				if (v.value.length > 1 || v.value[0][0] != 0 || v.value[0][1] != 0) s += '<span style="float: right;" class="edost_control_button edost_control_button_low" onclick="edost_Package(\'clear\', ' + value + ')"><?=$control_sign['button']['clear']?></span>';
			}

			return s;
		}

		// вывод блока с цифровыми кнопками и ячейкой для ручного ввода
		if (param == 'draw_button2') {
			var v = value;
			s = '<div class="edost_package_button"' + (v.style ? ' style="' + v.style + '"' : '') + '>';
			s += '<span class="edost_package_button_head">' + v.head + '</span> ';
			var n = 0;
			for (var i = v.start; i <= v.end; i++) {
				if ((!v.if || v.if > i) && (!v.if2 || i != v.value)) {
					s += '<span class="edost_package_button edost_control_button edost_control_button_white" onclick="edost_Package(\'' + v.param + '\', \'' + v.value + '|' + i + '\')">' + (i == 0 ? '&nbsp;&nbsp;' : i) + '</span>';
					n++;
				}
				if (v.if2 && n == v.end) break;
			}
			if (v.if2) edost_InputPosition(0, 1);
			if (!v.if || v.if > v.end) s += '<input' + (v.if2 ? ' id="edost_input_2_' + edost_InputPosition(1, 0) + '"' : '') + ' data-code="' + (v.if2 ? 'box_' : '') + v.value + '_' + v.param + '" value="" ' + input_function + '>';
			s += '</div>';

			return s;
		}

		// вывод общих кнопок для списка товаров
		if (param == 'draw_button3') {
			var E2 = document.getElementById('edost_package_window_button');
			if (!E2) return;

			var n = 0, n2 = 0, n3 = 0;
			for (var i = 0; i < edost_package_item.length; i++) if (!edost_package_item[i].hide) {
				var v = edost_package_item[i];
				n2 += v.remain*1;
				for (var i2 = 0; i2 < v.value.length; i2++) {
					if (v.value[i2][0] == 0 && v.value[i2][1] != 0) n3 += v.value[i2][1]*1;
					if (v.value[i2][0] != 0 && v.value[i2][1] == 0) n++;
				}
			}

			var s = '';
			if (n2 > 0) s += edost_Package('draw_button2', {"head": "<?=$control_sign['package']['put']?>", "start": 1, "end": 5, "param": "put", "value": ""});
			if (n3 > 0) s += edost_Package('draw_button2', {"head": "<?=$control_sign['package']['put2']?>", "start": 1, "end": 5, "param": "put2", "value": ""});
			if (n > 0) s += '<span style="margin: 0 40px;" class="edost_package_button edost_control_button edost_control_button_white" onclick="edost_Package(\'put3\', \'\')"><?=$control_sign['package']['put3']?></span>';
			if (s != '') s = '<?=$delimiter?><div style="padding: 20px; 0">' + s + '</div>';
			E2.innerHTML = s;
			E2.style.display = (edost_package_item_mode && s != '' ? 'block' : 'none');

			return;
		}


		// вывод блока с ячейками в списке с товарами
		if (param == 'draw_item_input') {
			var s = '';
			value = value.split('|');
			v = edost_package_item[value[0]];
			if (value[1] == 'all') {
				for (var i = 0; i < v.value.length; i++) s += '<div style="padding: 2px 0;">' + edost_Package('draw_item_input', value[0] + '|' + i) + '</div>';
			}
			else {
				var id = value[0] + '_box_' + value[1];
				s += '<div class="edost_package_item_value">';
				s += '<input id="edost_package_input_' + id + '" data-code="' + id + '" value="' + edost_Digit(v.value[value[1]][0]) + '" ' + input_function + '>';
				s += '</div>'

				var id = value[0] + '_count_' + value[1];
				s += '<div class="edost_package_item_value">';
				s += '<input id="edost_package_input_' + id + '" data-code="' + id + '" value="' + edost_Digit(v.value[value[1]][1]) + '" ' + input_function + '>';
				s += '</div>'
			}

			return s;
		}

		// вывод подписи "разделить/собрать комплект"
		if (param == 'draw_set') {
			var v = value.split('|');

			var s = '';
			if (v[1] === 'set') s += '<span class="edost_control_button edost_control_button_low" onclick="edost_Package(\'unpack\', ' + v[0] + ')"><?=$control_sign['button']['package_set_unpack']?></span>';
			else if (v[1]) s += '<span class="edost_control_button edost_control_button_low" onclick="edost_Package(\'pack\', ' + v[1] + ')"><?=$control_sign['button']['package_set_pack']?></span>';
			if (s != '') {
				if (v[2] == 'box') s = '<div style="padding-left: 18px;">' + s + '</div>';
				else s = '<br>' + s;
			}

			return s;
		}

		// вывод списка товаров
		if (param == 'draw_item') {
			var E = document.getElementById('edost_package_window_item');
			if (!E) return;

			if (value == 'all') {
				var E2 = document.getElementById('edost_package_window_item_head');
				if (!E2) return;

				s = '<table width="100%" border="0" cellpadding="4" cellspacing="0" style="text-align: center;">';
				s += '<tr style="font-size: 12px;">';
				s += '<td width="80"></td>';
				s += '<td width="250"></td>';
				s += '<td width="50"><?=$control_sign['package']['head_all']?></td>';
				s += '<td width="120" style="font-weight: bold;">';
					s += '<div class="edost_package_item_value"><?=$control_sign['package']['head']?></div>';
					s += '<div class="edost_package_item_value"><?=$control_sign['quantity']?></div>';
				s += '</td>';
				s += '<td width="50"><?=$control_sign['package']['head_remain']?></td>';
				s += '<td><span style="float: right;" class="edost_control_button edost_control_button_low" onclick="edost_Package(\'clear\')"><?=$control_sign['button']['clear_all']?></span></td>';
				s += '</tr>';
				s += '</table>';
				E2.innerHTML = s;

				E.innerHTML = '';
				for (var i = 0; i < edost_package_item.length; i++) edost_Package('draw_item', i);
			}
			else {
				v = edost_package_item[value];

				if (v.hide) return;

				var s = '';
				if (value != 0) s += '<?=$delimiter?>'
				s += '<table id="edost_package_item_' + value + '_table" width="100%" border="0" cellpadding="4" cellspacing="0" style="text-align: center;">';
				s += '<tr>';
				s += '<td width="330" style="text-align: left; padding-left: 10px;">' + v.NAME;
					s += '<br><div class="edost_package_item_weight">' + v.package_weight + ' <?=$control_sign['kg']?></div>';
					s += '<div class="edost_package_item_size">' + (v.package_size ? v.package_size + ' <?=$control_sign['cm']?>' : '') + '</div>';
					s += '<div class="edost_package_item_volume">' + (v.package_volume > 0 ? v.package_volume + ' <?=$control_sign['meter']?><sup>3</sup>' : '') + '</div>';
					s += edost_Package('draw_set', v.ID + '|' + v.SET_PARENT_ID);
				s += '</td>';
				s += '<td width="50">' + v.QUANTITY + '</td>';
				s += '<td width="120">' + edost_Package('draw_item_input', value + '|all') + '</td>';
				s += '<td id="edost_package_item_' + value + '_remain" width="50" style="font-weight: bold; cursor: ' + (v.remain == 0 ? 'default' : 'pointer') + ';" onclick="edost_Package(\'remain\', \'' + value + '\')">' + (v.remain > 0 ? v.remain : '') + '</td>';
				s += '<td id="edost_package_item_' + value + '_button" style="text-align: left;">' + edost_Package('draw_button', value) + '</td>';
				s += '</tr></table>';
				E.innerHTML += s;
			}

			return;
		}

		// вывод мест
		if (param == 'draw_box') {
			if (value == 'all') {
				var E = document.getElementById('edost_package_window_box');
				if (!E) return;

				edost_InputPosition(0, 0);

				var E2 = document.getElementById('edost_package_window');
				var h = E2.offsetHeight - document.getElementById('edost_package_window_box').getBoundingClientRect().top + E2.getBoundingClientRect().top - 20;

				var s = '';
				if (edost_package_item_mode) s = '<?=$delimiter?><div style="height: 20px;"></div>';
				s += '<div id="edost_package_box_div"><div id="edost_package_box_div_in">' + edost_Package('draw_box', 0) + '</div></div>';
				s += '<div id="edost_package_box_div2"><div id="edost_package_box_div2_in">';
				var n = 0;
				for (var i = 1; i < edost_package_box.length; i++) if (edost_package_box[i] && !edost_package_box[i].hide) {
					s += edost_Package('draw_box', i);
					if (edost_package_box[i].item.length > 0) n++;
				}
				if (n == 0) s += '<div class="edost_package_help">' + (edost_package_item_mode ? '<?=$control_sign['package']['help_item']?>' : '<?=$control_sign['package']['help_box']?>') + '</div>';
				s += '</div></div>';

				E.innerHTML = s;

				edost_Package('resize');
			}
			else {
				v = edost_package_box[value];

				if (v.hide || edost_package_item_mode && value == 0) return '';

				var s = '';

				var c = '';
				if (value == 0) c = 'edost_package_box_0';
				else if (v.item.length == 0) c = 'edost_package_box_empty';
				else c = 'edost_package_box_normal';

				v.item_weight = 0;
				var volume = 0;
				if (v.item.length > 0) for (var i = 0; i < v.item.length; i++) {
					var v2 = edost_package_item[v.item[i][0]];
					v.item_weight += v2.package_weight * v.item[i][1];
				}
				var n = 100;
				if (v.item_weight >= 10) n = 10;
				else if (v.item_weight >= 100) n = 1;
				v.item_weight = Math.round(v.item_weight*n)/n;

				s += '<div class="edost_package_box ' + c + '" data-code="' + value + '">';
				s += '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';

				s += '<td class="edost_package_box_head">' + (value == 0 ? '<?=$control_sign['package']['new']?> ' : '<div><?=$control_sign['package']['head']?></div>' + value) + '</td>';
				if (value == 0) s += '</tr><tr>';
				s += '<td style="padding: 8px;">';

				s += '<div style="text-align: center;">';
				if (value != 0 && v.item.length > 0) {
					if (v.item_weight > 0) s += '<span class="edost_package_box_weight" onclick="edost_Package(\'set_weight\', \'' + value + '\', this)">' + v.item_weight + ' <?=$control_sign['kg']?></span>';
					edost_InputPosition(0, 1);
					s += '<input id="edost_input_2_' + edost_InputPosition(1, 0) + '" class="' + (v.weight == 0 ? 'edost_package_error' : 'edost_package_on') + '" data-code="box_' + value + '_weight" style="height: 18px; width: 35px;" value="' + edost_Digit(v.weight) + '" autocomplete="off" ' + input_function + '> <?=$control_sign['kg']?>';
					for (var i2 = 0; i2 <= 2; i2++) s += '<input id="edost_input_2_' + edost_InputPosition(1, 0) + '" class="' + (v.size[i2] == 0 ? 'edost_package_error2' : 'edost_package_on') + '" data-code="box_' + value + '_size_' + i2 + '" style="height: 18px; width: 35px;' + (i2 == 0 ? ' margin-left: 10px;' : '') + '" value="' + edost_Digit(v.size[i2]) + '" ' + input_function + '> ' + (i2 != 2 ? ' x ' : '');
					s += ' <?=$control_sign['cm']?>';
				}
				s += '</div>';

				s += '<div id="edost_package_box_error_' + value + '" style="color: #F00;"></div>';

				s += '<div style="padding-top: 5px; text-align: center;">'
				if (v.item.length > 0) {
					s += '<div style="padding-top: 5px;">';
					for (var i = 0; i < v.item.length; i++) {
						v2 = edost_package_item[v.item[i][0]];
						if (i != 0) s += '<?=$delimiter?>';
						s += '<table id="box_' + value + '_table_' + i + '" class="edost_package_box_item" width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';
						s += '<td>';
						if (!edost_package_item_mode) s += '<div class="checkbox">';
						var code = 'box_' + value + '_active_' + i;
						if (!edost_package_item_mode) s += '<input id="' + code + '" data-code="' + code + '" class="edost_package_checkbox" type="checkbox" onclick="edost_Package(\'update_box_item\', this)"' + (v.item[i][1] > 0 ? ' checked=""' : '') + '> ';
						if (!edost_package_item_mode) s += '<label data-code="box_' + value + '_name_' + i + '" class="edost_package_move" for="' + code + '">';
						s += (!edost_package_item_mode ? '<b>' + v2.NAME + '</b>' : v2.NAME);
						if (!edost_package_item_mode) s += '</label>';
						if (!edost_package_item_mode) s += edost_Package('draw_set', v2.ID + '|' + v2.SET_PARENT_ID + '|' + 'box');
						if (!edost_package_item_mode) s += '</div>';
						s += '</td>';
						s += '<td width="40" align="right" class="edost_package_box_item_all" style="' + (!edost_package_item_mode ? ' color: #888;' : ' font-weight: bold;') + '">' + edost_Digit(v.item[i][2]) + '</td>';
						if (!edost_package_item_mode) {
							edost_InputPosition(0, 1);
							s += '<td class="edost_package_box_item_count"><input id="edost_input_2_' + edost_InputPosition(1, 0) + '" data-code="box_' + value + '_count_' + i + '" value="' + edost_Digit(v.item[i][1]) + '" ' + input_function + '></td>';
                    	}
						s += '</tr></table>';
					}
					s += '</div>';

					if (!edost_package_item_mode) {
						s += edost_Package('draw_button2', {"head": "<?=$control_sign['package']['put4']?>", "start": 0, "end": 3, "if2": true, "param": "put4", "value": value, "style": "padding-top: 10px; margin: 0px;"});
						if (value != 0) s += '<span id="box_' + value + '_save" style="padding-left: 10px; display: none;" class="edost_control_button edost_control_button_green" onclick="edost_Package(\'put4\', \'' + value  + '|' + value + '\')"><?=$control_sign['button']['save']?></span>';
					}
				}
				s += '</div>';

				s += '</td>';

				s += '</tr></table>';
				s += '</div>'

				return s;
			}

			return;
		}

		if (param != 'show') {
			document.onkeydown = edost_package_onkeydown_backup;
			document.body.style.overflow = edost_package_overflow_backup;
			window.onresize = edost_package_onresize_backup;
			edost_package_onkeydown_backup = 'free';
		}
		else if (edost_package_onkeydown_backup == 'free') {
			edost_package_onkeydown_backup = document.onkeydown;
			edost_package_overflow_backup = document.body.style.overflow;
			edost_package_onresize_backup = window.onresize;
			document.onkeydown = new Function('event', 'if (event.keyCode == 27) edost_Package("close");');
			window.onresize = new Function('', 'edost_Package("resize")');
		}

		// генерация окна
		var E = document.getElementById('edost_package_window');
		if (!E) {
			var E = document.body;

			var E2 = document.createElement('DIV');
			E2.className = 'edost_office_window_fon';
			E2.id = 'edost_package_window_fon';
			E2.style.display = 'none';
			E2.onclick = new Function('', 'edost_Package("close")');
			E.appendChild(E2);

			var E2 = document.createElement('DIV');
			E2.className = 'edost_office_window edost_device_pc';
			E2.id = 'edost_package_window';
			E2.style.display = 'none';
			E2.style.border = 'none';

			var s = '';
			s += edost_office.close.replace('%onclick%', "edost_Package('close')");
			s += '<div id="edost_office_window_head" class="edost_office_window_head" style="font-size: 20px; padding-top: 6px;">';
				s += '<div id="edost_package_window_head_div_left" style="text-align: center;">';
				s += '<div id="edost_package_setting_item">';
				s += '<?=$control_sign['package']['window_head']?><div style="height: 5px;"></div>';
				s += '<span class="edost_control_button edost_control_button_light edost_control_button_low edost_button_big_off" onclick="edost_Package(\'mode\', \'box\')"><?=$control_sign['package']['mode_box']?></span>';
				s += '<span style="margin-left: 20px;" class="edost_control_button edost_control_button_light edost_control_button_low edost_button_big_on" onclick="edost_Package(\'mode\', \'item\')"><?=$control_sign['package']['mode_item']?></span>';
				s += '</div>';
				s += '</div>';
				s += '<div id="edost_package_window_head_div_right" style="text-align: center;">';
				s += '<div id="button_package_window_save" class="edost_register_button" style="margin: 0 auto; line-height: 15px; cursor: pointer; background: #0A0; border-radius: 5px; width: 160px; height: 35px; color: #FFF;" onclick="edost_Package(\'save\')">';
				s += '<div style="position: absolute; margin: 0 auto; width: 160px; text-align: center; padding-top: 10px; font-size: 20px;"><?=$control_sign['button']['package_save']?></div>';
				s += '</div>';
				s += '</div>';
			s += '</div>';
			s += '<div id="edost_package_window_data" class="edost" style="padding: 12px 20px 20px 20px; -moz-user-select: none; -webkit-user-select: none; user-select: none;">';
				s += '<div id="edost_package_window_item_head" class="edost" style="background: #DAF3FF;"></div>';
				s += '<div id="edost_package_window_item" class="edost" style="padding: 0 0 10px 0; overflow: auto;"></div>';
				s += '<div id="edost_package_window_button" class="edost"></div>';
				s += '<div id="edost_package_window_box" class="edost" style="text-align: center;"></div>';
			s += '</div>';
			E2.innerHTML = s;

			E.appendChild(E2);

			edost_Package('mode', 'update', 'start');

			// перетаскивание товаров между местами + выделение галочек товаров одним движением
			var E = document.getElementById('edost_package_window_data');
			if (E) {
				E.onmousedown = function(event) {
					if (edost_mouse.move || edost_mouse.checkbox || event.which != 1) return;

					var ar = ['move', 'checkbox'];
					for (var i = 0; i < ar.length; i++) {
						var E = event.target.closest('.edost_package_' + ar[i]);
						if (!E) continue;
						var c = E.getAttribute('data-code').split('_');
						edost_mouse[ar[i]] = {"E": E, "x": event.pageX, "y": event.pageY, "box": c[1], "item": c[3]};
						if (E.type == 'checkbox') edost_mouse[ar[i]].active = E.checked;
						if (ar[i] == 'move') edost_mouse[ar[i]].table = document.getElementById(c.join('_').replace('_name_', '_table_'))
					}

					return;
				}

				E.onmousemove = function(event) {
					if (edost_mouse.checkbox)	{
						if (Math.abs(event.pageX - edost_mouse.checkbox.x) < 5 && Math.abs(event.pageY - edost_mouse.checkbox.y) < 5) return;

						if (edost_mouse.checkbox.E.checked == edost_mouse.checkbox.active) edost_mouse.checkbox.E.checked = !edost_mouse.checkbox.active;

						var E = event.target.closest('.edost_package_checkbox');
						var c = (E ? E.getAttribute('data-code').split('_') : false);
						if (c[1] == edost_mouse.checkbox.box && c[3] != edost_mouse.checkbox.item && E.checked == edost_mouse.checkbox.active) E.checked = !edost_mouse.checkbox.active;
					}

					if (!edost_mouse.move) return;

					if (!edost_mouse.move.E2) {
						if (Math.abs(event.pageX - edost_mouse.move.x) < 5 && Math.abs(event.pageY - edost_mouse.move.y) < 5) return;

						edost_mouse.move.table.className = 'edost_package_box_item edost_package_box_item_move';
						edost_mouse.move.E2 = BX.create('div', {'props': {'innerHTML': edost_mouse.move.E.innerHTML + ' (' + edost_package_box[edost_mouse.move.box].item[edost_mouse.move.item][1] + ' <?=$control_sign['quantity']?>)'}});
						edost_mouse.move.E2.className = 'edost_package_box_move_active';

						var E = document.getElementById('edost_package_window_data');
						E.appendChild(edost_mouse.move.E2);
						var coords = edost_mouse.move.E.getBoundingClientRect();

						edost_mouse.move.shiftX = edost_mouse.move.x - coords.left;
						edost_mouse.move.shiftY = edost_mouse.move.y - coords.top;
					}

					edost_mouse.move.E2.style.left = Math.round(event.pageX - edost_mouse.move.shiftX) + 'px';
					edost_mouse.move.E2.style.top = Math.round(event.pageY - edost_mouse.move.shiftY) + 'px';

					var E = event.target.closest('.edost_package_box');
					var c = (E ? E.getAttribute('data-code') : false);
					if (edost_mouse.move.input && edost_mouse.move.input !== c && edost_mouse.move.input !== false) {
						edost_mouse.move.inputE.classList.remove('edost_package_box_move');
						edost_mouse.move.input = false;
					}
					if (c !== false && c != edost_mouse.move.box) {
						edost_mouse.move.input = c;
						edost_mouse.move.inputE = E;
						edost_mouse.move.inputE.classList.add('edost_package_box_move');
					}

					return false;
				}

				document.onmouseup = function(event) {
					if (edost_mouse.move && edost_mouse.move.E2) edost_mouse.move.E2.remove();

					if (edost_mouse.move)
						if (edost_mouse.move.inputE && edost_mouse.move.input !== false) edost_Package('put4', edost_mouse.move.box + '|' + edost_mouse.move.input + '|' + edost_mouse.move.item);
						else if (edost_mouse.move.table) edost_mouse.move.table.className = 'edost_package_box_item';

					edost_mouse.move = false;
					edost_mouse.checkbox = false;
				}
			}
		}


		var display = (param != 'show' ? 'none' : 'block');

		var package_data = document.getElementById('edost_package_window_data');
		if (!package_data) return;

		var E = document.getElementById('edost_package_window');
		if (!E) return;
		E.style.display = display;

		var E2 = document.getElementById('edost_package_window_fon');
		if (E2) E2.style.display = display;

		if (param != 'show') return;

		var E_box = document.getElementById('edost_package_box_div');
		var E_box2 = document.getElementById('edost_package_box_div2');
		if (E_box && E_box2) edost_scroll = [E_box.scrollTop, E_box2.scrollTop];

		edost_Package('draw_item', 'all');
		edost_Package('update_item_list');
		edost_Package('draw_box', 'all');
		edost_Package('check');
		edost_Package('update_save');
		edost_Package('resize');

	}


	// оформление доставки
	function edost_Register(param, value, value2) {
//		alert(param, value);

		var set = '';
		var post = '';

		if (param == 'delete') {
			var s = value.split('|');
			var name = s[0];
			var id = s[1];
			var hide = (s[2] ? true : false);

			var E = BX('register_button');
			if (E) E.style.display = 'none';
			var E = BX('register_button2');
			if (E) E.style.display = 'block';

			if (name == 'order') {
				edost_Register('delete', 'register|' + id);
				edost_Register('delete', 'batch|' + id);

				var E = BX('edost_shipment_' + id);
				if (!E) return;
				var E = E.parentNode.parentNode;
				var ar = E.children[2].getElementsByTagName('IMG');
				for (var i = 0; i < ar.length; i++) if (ar[i].classList.contains('edost_register_on') || ar[i].classList.contains('edost_register_off')) {
					ar[i].className = 'edost_register_disabled3';
					ar[i].onclick = '';
				}
				var ar = BX.findChildren(E, {'tag': 'input', 'attribute': {'type': 'checkbox'}}, true);
				for (var i = 0; i < ar.length; i++) {
					var E2 = BX.findNextSibling(ar[i]);
					E2.remove();
					ar[i].remove();
				}

				var E = E.parentNode.parentNode.parentNode;
				edost_Register('active_all_update', E);

				return;
			}

			var E = BX('edost_register_button_' + id);
			if (E) E.style.display = 'none';

			var E = BX('edost_register_company');
			var company = (E ? E.value : '');

			var E = BX('edost_' + name + '_img_' + id);
			if (E) {
				var s = E.src.split('control_' + name + '_');
				E.src = s[0] + 'control_' + (company == 23 ? name : 'batch') + '_' + 'delete.png';
			}

			var E = BX('edost_' + name + '_delete_' + id);
			if (E) E.style.display = 'none';
		}
		else if (param == 'button_active') {
			var s = value;
			var name = s[0];
			var a = (s[1] ? true : false);

			if (name != 'all') var id = [name];
			else {
				var id = [];
				var ar = document.getElementsByClassName('edost_register_button');
				for (var i = 0; i < ar.length; i++) id.push(ar[i].id);
			}
			for (var i = 0; i < id.length; i++) {
				var E = BX(id[i]);
				if (E) E.className = 'edost_register_button ' + (!a ? 'edost_register_button_disabled' : '');
				var E = BX(id[i] + '_disabled');
				if (E) E.style.display = (!a ? 'block' : 'none');
			}
		}
		else if (param == 'local_button_active') {
			for (var i = 0; i < value.length; i++) {
				var E = BX('edost_register_button_' + value[i][0] + '_' + value[i][1]);
				if (!E) continue;
				E.style.display = (value[i][2] ? 'inline' : 'none');
				E = E.previousSibling;
				if (E && E.className == 'edost_register_button_delimiter') E.style.display = (!value[i-1][2] || !value[i][2] ? 'none' : 'inline');
			}
		}
		else if (param == 'transfer_status') {
			var color = (value[1] != undefined ? value[1] : '888');
			value = value[0];

			if (color == 'red') color = 'F00';
			if (color == 'green') color = '0A0';

			var s = value;
<?			foreach ($control_sign['register_status'] as $k => $v) echo "
			if (value == '".$k."') s = '".$v."';";?>

			var E = BX('edost_transfer_status');
			if (E) E.innerHTML = '<span style="font-size: 16px; color: #' + color + ';">' + s + '</span>';
		}
		else if (param == 'transfer_start') {
			edost_transfer_bar = BX('edost_transfer_bar');

			var s = new Date();
			edost_register_time_start = s.getTime();
			edost_register_update = 0;
			edost_register_transfer = 0;
			edost_register_transfer_set = -1;
			edost_register_transfer_history = false;
			edost_register_time_end = 0;

			if (edost_register_timer != undefined) window.clearInterval(edost_register_timer);
			edost_register_timer = window.setInterval('edost_Register("transfer")', 40);
		}
		else if (param == 'transfer_stop') {
			if (edost_register_timer != undefined) window.clearInterval(edost_register_timer);
			if (value != undefined && value != '') {
				var s = '<b>' + value + '</b> <br> <span style="font-size: 16px; font-weight: normal; display: inline-block; margin-top: 5px;" class="edost_control_button edost_control_button_low" onclick="edost_Register(\'transfer_stop\'); edost_SetParam(\'register\', \'reload\');"><?=$control_sign['close']?></span>';
				edost_Register('transfer_status', [s, 'red']);
			}
			else {
				var E = BX('edost_transfer_fon');
				if (E) E.style.display = 'none';
				var E = BX('edost_transfer');
				if (E) E.style.display = 'none';
			}
		}
		else if (param == 'transfer') {
			var s = new Date();
			var time = (s.getTime() - edost_register_time_start)/100;

			if (edost_register_time_end !== 0) {
				var time = (edost_register_time_end - edost_register_time_start)/100 + (s.getTime() - edost_register_time_end)/15;
			}

			var data_E = BX('edost_main_div');
			var fon_E = BX('edost_transfer_fon');
			var transfer_E = BX('edost_transfer');
			if (data_E && fon_E && transfer_E) {
				fon_E.style.width = data_E.offsetWidth + 'px';
				fon_E.style.height = data_E.offsetHeight + 'px';
				fon_E.style.display = 'block';
				transfer_E.style.width = (data_E.offsetWidth-122) + 'px';
				transfer_E.style.display = 'block';
				var browser_h = (document.documentElement.clientHeight == 0 ? document.body.clientHeight : document.documentElement.clientHeight);
				transfer_E.style.top = Math.round((browser_h - transfer_E.offsetHeight)*0.5) + 'px';
			}

			if (edost_register_transfer == 0 || edost_register_transfer_set == 1 && edost_register_transfer == 1 && time > 25 || edost_register_time_end === 0 && edost_register_transfer_set == 2 && edost_register_transfer == 2 && time > 75) {
				edost_register_transfer++;
				if (edost_register_transfer == 1) edost_Register('transfer_status', ['transfer']);
				if (edost_register_transfer > 1) edost_Register('button', 'update');
			}
			if (time > 100) {
				var s = '';
				if (edost_register_transfer_set == -1) s = '<?=$control_sign['error']['timeout']?>!'; // ответ не получен (превышен лимит ожидания)
				else if (!edost_register_transfer_history) s = '<?=$control_sign['error']['no_data']?>!'; // во время обработки данных произошел сбой
				edost_Register('transfer_stop', s);

				edost_register_transfer_set = -2;
				if (s == '') edost_SetParam('register', 'history');

				return;
			}

			edost_transfer_bar.innerHTML = '<div style="background: #0eb3ff; height: 10px; width: ' + time + '%;">&nbsp;</div>';
		}
		else if (param == 'help') {
			var s = '';
<?			foreach ($control_sign['button']['register_data'] as $k => $v) echo "
			if (value == 'button_".$k."') s = '".$v['help']."';";?>

			var E = BX('edost_help');
			if (E) E.innerHTML = s;
			var E = BX('edost_help_default');
			if (E) E.style.display = (s == '' ? 'inline' : 'none');
		}
		else if (param == 'input_start') {
			edost_input_E = BX(value);
			edost_input_value = edost_input_E.value;
		}
		else if (param == 'update_input') {
			if (edost_input_E && edost_input_E.value != edost_input_value) {
				edost_input_value = edost_input_E.value;

				if (edost_input_E.id.indexOf('_date') > 0) {
					var date = new Date();
					var v = edost_input_E.value.split('.');
					var a = true;
					if (v[0] <= 31 && v[1] && v[1] <= 12 && v[2] && !v[3]) {
						var u = new Date(v[2], v[1]-1, v[0]);
						var n = new Date(date.getFullYear(), date.getMonth(), date.getDate());
						if (u && u.valueOf() >= n.valueOf() && v[0] == u.getDate() && v[1] == u.getMonth()+1 && v[2] == u.getFullYear()) a = false;
					}
					if (edost_input_E.id.indexOf('window_') == 0) {
						if (edost_input_E.value == edost_window.param.batch_date) a = true;
						edost_window.resize('error', a);
					}
					var s = '_error';
				}
				else {
					var a = (edost_input_E.value == 0 ? true : false);
					var s = (edost_input_E.getAttribute('data-code').indexOf('_size_') > 0 ? '_error2' : '_error');
				}
				edost_input_E.className = edost_input_E.className.replace(a ? '_on' : s, a ? s : '_on');

				edost_Register('active_all_update');
			}
		}
		else if (param == 'input_focus') {
			edost_input_E = value;
			edost_input_value = value.value;
			if (edost_input_timer != undefined) window.clearInterval(edost_input_timer);
			edost_input_timer = window.setInterval('edost_Register(\'update_input\')', 200);
		}
		else if (param == 'input_blur') {
			edost_input_timer = window.clearInterval(edost_input_timer);
			edost_Register('update_input');
		}
		else if (param == 'active_change' || param == 'active_change_on' || param == 'active_change_off') {
			var a = true;
			if (param == 'active_change') a = (value.className.indexOf('_on') > 0 ? true : false);
			if (param == 'active_change_on') a = false;
			value.className = value.className.replace(a ? '_on' : '_off', a ? '_off' : '_on');
			return !a;
		}
		else if (param == 'check_batch_date') {
			var E = BX('edost_batch_div');
			if (E) {
				var E = BX('edost_batch');
				if (!E || E.value == 'new') {
					var E = BX('edost_batch_date');
					if (E && E.className.indexOf('_error') > 0) return false;
				}
			}
			return true;
		}
		else if (param == 'active_all_update') {
			// глобальное обновление

			// размер главной формы
			var E = BX('edost_main_div');
			if (E) E.className = 'edost_main_div_size' + (edost_UpdateCookie('register_item') == 'Y' ? '2' : '');

			// обновление глобальной галочки выделения
			if (value) {
				var a = false;
				var ar = BX.findChildren(value, {'tag': 'input', 'attribute': {'type': 'checkbox'}}, true);
				for (var i = 0; i < ar.length; i++) if (ar[i].type != 'hidden' && ar[i].checked) { a = true; break; }
				BX(value.id + '_active').checked = a;
			}

			// ссылки на печать
			if (1 == 2) {
			var E = BX('edost_print');
			if (E) {
				var print = '<div style="color: #888; padding-bottom: 5px;"><?=$control_sign['print']['head']?>:</div>';

				var doc = edost_Register('get_doc', 'all');
                print += edost_Register('get_print_link', [doc, '<?=$control_sign['print']['doc_all']?>', '<?=$control_sign['print']['label_all']?>']);

				E.parentNode.style.display = (doc.count != 0 ? 'block' : 'none');

                var order = [];
                var batch = [];
				var ar = document.getElementsByClassName('edost_shipment');
				if (ar.length > 1) for (var i = 0; i < ar.length; i++) {
					if (!ar[i].checked) continue;

					var order_code = ar[i].getAttribute('data-code');
					var id = ar[i].id.split('edost_shipment_')[1];
					var doc = edost_Register('get_doc', id);
                    if (doc.count == 0) continue;

					var s = edost_Register('get_print_link', [doc, order_code]);

					var E2 = BX('edost_batch_shipment_' + id);
					if (E2) {
						var batch_id = E2.name;
						var a = false;
						for (var i2 = 0; i2 < batch.length; i2++) if (batch[i2][0] == batch_id) {
							a = true;
							batch[i2][1].normal = batch[i2][1].normal.concat(doc.normal);
							batch[i2][1].label = batch[i2][1].label.concat(doc.label);
							batch[i2][5].normal = batch[i2][5].normal.concat(doc.normal);
							batch[i2][5].label = batch[i2][5].label.concat(doc.label);
							batch[i2][2].push(s);
							break;
						}
						if (!a) {
							var E2 = BX('edost_batch_name_' + E2.name.split('edost_batch_')[1]);
							if (E2) {
								if (E2.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.id != 'edost_shipment_register_complete_batch_full') doc2 = doc;
								else doc2 = {
									'normal': [].concat([id + '_103'], doc.normal),
									'label': doc.label,
									'count': doc.count + 1,
								};
								batch.push([batch_id, doc2, [s], E2.value, edost_Register('get_print_link', [[id + '_103'], '<?=$control_sign['print']['batch_general_103']?>']), doc]);
							}
						}
					}
					else order.push(s);
				}

				for (var i = 0; i < batch.length; i++) {
					print += '<div style="padding-top: ' + (i == 0 ? '10' : '5') + 'px">';
					print += batch[i][3] + ': ' + edost_Register('get_print_link', [batch[i][1], '<?=$control_sign['print']['order_doc2']?>']);
//					print += ''<br>'' + batch[i][4] + ', ' + edost_Register('get_print_link', [batch[i][5], '<?=$control_sign['print']['order']?>']) + '<br>';
//					if (batch[i][2].length != 1) print += '<?=$control_sign['print']['order_prefix']?> ' + batch[i][2].join(', ');
					print += '</div>';
				}

				if (order.length != 0) print += '<div style="padding-top: 10px"><?=$control_sign['print']['order_prefix']?> ' + order.join(', ') + '<div>';

				E.innerHTML = print;
			}
			}


			var warning = [];
			var warning_add = '';
			var batch_div = false;
			var batch_count = 0;
			var batch_date = edost_Register('check_batch_date');
			var order_count = 0;

			// проверка на флаги заполненности профилей и требования опций
			var call = false;
			var flag_error = false;
			var E_call = BX('edost_call');
			if (E_call) {
				call = (E_call.checked ? 'Y' : 'N');
				var E_shop = BX('edost_profile_shop');
                if (E_shop) var E_option = E_shop.options[E_shop.selectedIndex];

				var s = (E_shop && E_shop.value ? ['change', E_shop.value, '<?=$control_sign['button']['profile_flag']?>'] : ['new', 0, '<?=$control_sign['button']['profile_new']?>']);
				var profile_button = '(<span class="edost_link" onclick="edost_window.set(\'profile_setting_' + s[0] + '\', \'type=shop;local=1;id=' + s[1] + '\')">' + s[2] + '</span>)';

				// вызов курьера
				if (call == 'Y' && (!E_shop || !E_option.getAttribute('data-flag_call'))) warning.push('<?=$control_sign['warning']['flag_call']?> ' + profile_button);
			}

			// заказы готовые к оформлению
			var E = BX('edost_shipment_register_new');
			if (E) {
                // определение типа выбранной сдачи (негабарит)
				var oversize = false;
				var E2 = BX('edost_batch');
				if (E2) {
					var E2 = E2.options[E2.selectedIndex];
					var o = E2.getAttribute('data-oversize');
					if (o != undefined) oversize = o*1;
				}

				// распечатать без оформления
				var doc = edost_Register('get_doc', 'print_no_register');
				edost_Register('button_active', ['button_print_no_register', doc.count != 0 ? 1 : 0]);

				// оформить
				var zero_weight = 0;
				var warning_main = {};
				var control_count = [];
				var ar = E.getElementsByClassName('edost_register_active');
				for (var i = 0; i < ar.length; i++) {
					var c = ar[i].getAttribute('data-code');
					id = c.split('_')[0];

					var warning_local = {"string": []};
					var register_active = false;
					var register_active2 = false;
					var batch_active = false;
					var control_active = true;
					if (ar[i].className.indexOf('_on') > 0) {
						order_count++;
                        var E_main = ar[i].parentNode.parentNode.parentNode;

						var ar2 = E_main.getElementsByClassName('edost_batch_active');
						for (var i2 = 0; i2 < ar2.length; i2++) if (ar2[i2].className.indexOf('_on') > 0) { batch_count++; batch_active = true; }

						var weight = 0;
						var ar2 = E_main.getElementsByClassName('edost_package_weight');
						for (var i2 = 0; i2 < ar2.length; i2++) if (ar2[i2].className.indexOf('_on') > 0) { weight = ar2[i2].value; register_active = true; } else zero_weight++;

						var size = [];
						var ar2 = E_main.getElementsByClassName('edost_package_size');
						for (var i2 = 0; i2 < ar2.length; i2++) size.push(ar2[i2].value);

						s = E_main.querySelector('.edost_service_button');
						if (s) {
							var p = edost_window.get_param(s);

							var E_props = BX('edost_props_' + id);
							var props = edost_window.get_param(E_props);

							// реверс (СДЭК)
							if (E_call && p.company == 5 && edost_window.in_array(48, p.service) && (!E_shop || !E_option.getAttribute('data-flag_reverse'))) warning_local.reverse = true;

							// SMS (Почта)
							if (props.phone == '' && p.company == 23 && edost_window.in_array(1, p.service)) warning_local.sms = true;
						}

						var E2 = BX('edost_package_error_' + id);
						if (E2) {
   							var box = E2.getAttribute('data-box')*1;
							if (box > 1) {
	   							var s = E2.getAttribute('data-error');
	   							if (s != '') warning_local[s] = true;
	   							else {
   									register_active = true;
   									register_active2 = true;
								}
							}
							else {
								var s = true;
								var tariff = E2.getAttribute('data-tariff');
								if (tariff != undefined) s = edost_CheckPackage(tariff, weight, size, oversize);
								if (s === true) register_active2 = true; else warning_add = s[1];
								E2.innerHTML = (s === true ? '' : '<div style="padding-top: 5px;">' + s[0] + '</div>');
							}
						}

						var c = ar[i].getAttribute('data-control').split('_');
						var k = -1;
						for (var i2 = 0; i2 < control_count.length; i2++) if (control_count[i2][0] == c[0]) { k = i2; break; }
						if (k == -1) { control_count.push([c[0], c[1], 0]); k = 0; }
						control_count[k][2]++;
						if (control_count[k][1] < control_count[k][2]) control_active = false;
					}

					for (var k in warning_local) warning_main[k] = warning_local[k];
					if (warning_local.reverse) warning_local.string.push('<?=$control_sign['warning']['flag_reverse_small']?>');
					if (warning_local.sms) warning_local.string.push('<?=$control_sign['warning']['sms_small']?>');
					if (warning_local.package_pack) warning_local.string.push('<?=$control_sign['warning']['package_pack_small']?>');
					if (warning_local.package_weight) warning_local.string.push('<?=$control_sign['warning']['package_weight_small']?>');

					var E = BX('edost_register_warning_' + id);
					if (E) E.innerHTML = warning_local.string.join('<br>');

					edost_Register('local_button_active', [[id, 'register', control_active && register_active && register_active2 && (call && batch_date || !call && (!batch_active || batch_date)) && warning_local.string.length == 0]]);
				}

				var control_active = true;
				for (var i2 = 0; i2 < control_count.length; i2++) if (control_count[i2][1] < control_count[i2][2]) {
					control_active = false;
					warning.push('<?=$control_sign['warning']['control_buy']?> <a class="edost_link2" href="http://edost.ru/shop_edit.php?p=5&s=' + control_count[i2][0] + '" target="_blank">' + (control_count[i2][2] - control_count[i2][1]) + '</a>');
					break;
				}

				if (zero_weight > 0) warning.push('<?=$control_sign['warning']['zero_weight']?>');
				if (warning_main.package_pack) warning.push('<?=$control_sign['warning']['package_pack']?>');
				if (warning_main.package_weight) warning.push('<?=$control_sign['warning']['package_weight']?>');
				if (warning_add != '') warning.push(warning_add);
				if (warning_main.reverse) warning.push('<?=$control_sign['warning']['flag_reverse']?> ' + profile_button);
				if (warning_main.sms) warning.push('<?=$control_sign['warning']['sms']?>');

				edost_Register('button_active', ['button_register', order_count == 0 || !control_active || call && !batch_date || warning.length > 0 ? 0 : 1]);
				edost_Register('button_active', ['button_register_print', order_count == 0 ? 0 : 1]);
         	}

			// на сдачу
			var E = BX('edost_shipment_register_complete');
			if (E) {
				var button_batch_active = false;
				var ar = E.getElementsByClassName('edost_batch_active');
				for (var i = 0; i < ar.length; i++) {
					var c = ar[i].getAttribute('data-code');
					id = c.split('_')[0];

					var batch_active = false;
					if (ar[i].className.indexOf('_on') > 0) {
						order_count++;
						batch_count++;
						button_batch_active = batch_active = true;
					}

					var print_active = false;
					var ar2 = ar[i].parentNode.parentNode.parentNode.getElementsByClassName('edost_doc');
					for (var i2 = 0; i2 < ar2.length; i2++) if (ar2[i2].className.indexOf('_on') > 0) { print_active = true; break }

					edost_Register('local_button_active', [[id, 'print', print_active], [id, 'batch', batch_active && batch_date]]);
				}
				edost_Register('button_active', ['button_batch', button_batch_active && batch_date ? 1 : 0]);
         	}

			// регистрация в отделении
			var E = BX('edost_shipment_register_complete_batch');
			if (E) {
				var button_office_active = false;
				var ar = E.getElementsByClassName('edost_batch_office');
				for (var i = 0; i < ar.length; i++) if (ar[i].checked) button_office_active = true;
				edost_Register('button_active', ['button_office', button_office_active ? 1 : 0]);
         	}

			// попробовать оформить еще раз
			var E = BX('edost_shipment_warning_red');
			if (E) {
				var a = false;
				var ar = E.getElementsByClassName('edost_shipment');
				for (var i = 0; i < ar.length; i++) if (ar[i].checked) { a = true; break; }
				edost_Register('button_active', ['button_register_repeat', a ? 1 : 0]);
         	}

			if (!batch_date && (batch_count != 0 || call)) warning.push('<?=$control_sign['warning']['batch_date']?>');

			var E = BX('edost_warning');
			if (E) {
				E.style.display = (warning.length > 0 ? 'block' : 'none');
				if (warning.length > 0) {
					for (var i = 0; i < warning.length; i++) warning[i] = '<span style="color: #555;">' + (warning.length > 1 ? '<b>' + (i+1) + '.</b> ' : '') + warning[i] + '</span>';
					E.innerHTML = '<b><?=$control_sign['warning']['register_head']?>:</b><div style="height: 5px;"></div>' + warning.join('<div style="height: 5px;"></div>');
				}
			}

			// сдачи почты
			if (!call) {
				var E = BX('edost_batch_div');
				if (E) {
					var ar = document.getElementsByClassName('edost_batch_active');
					for (var i = 0; i < ar.length; i++) if (ar[i].className.indexOf('_on') > 0) { batch_div = true; break; }
					E.style.display = (batch_div ? 'block' : 'none');

					var batch_E = BX('edost_batch');
					if (batch_E) {
						var E = BX('edost_batch_reset_div');
						E.style.display = (!batch_div && batch_E.value != 'new' ? 'block' : 'none');
					}
				}
			}

			for (var u = 0; u < 2; u++) {
				var E = BX('edost_shipment_register_complete_batch' + (u == 1 ? '_full' : ''));
				if (!E) continue;
				// распечатать (локальная кнопка)
				var ar = E.getElementsByClassName('edost_shipment');
				for (var i = 0; i < ar.length; i++) {
					var id = ar[i].id.split('edost_shipment_')[1];

					var a = false;
					var ar2 = ar[i].parentNode.parentNode.getElementsByClassName('edost_doc');
					for (var i2 = 0; i2 < ar2.length; i2++) if (ar2[i2].className.indexOf('_on') > 0) { a = true; break }

					var E2 = BX('edost_register_button_' + id + '_print');
					if (E2) E2.style.display = (a ? 'block' : 'none');
				}
			}

			edost_Register('batch_update');
		}
		else if (param == 'batch_update') {
			// включение заказов в новую или существующую сдачу

			var batch_E = BX('edost_batch');
			if (!batch_E) return;
			var E = batch_E.options[batch_E.selectedIndex];
			if (!E) return;

			var v = E.value;
			var o = E.getAttribute('data-order');
			if (o != undefined) o = o.split(',');

			var E2 = BX('edost_batch_date_span');
			if (E2) E2.style.display = (v == 'new' ? 'inline' : 'none');

			var order = [];
			var order_active = [];
			var ar = document.getElementsByClassName('edost_batch_active');
			for (var i = 0; i < ar.length; i++) if (ar[i].className.indexOf('_on') > 0 || ar[i].className.indexOf('_off') > 0) {
				var c = ar[i].getAttribute('data-code').split('_')[0];
				order.push(c);
				if (ar[i].className.indexOf('_on') > 0) order_active.push(c);
			}

			for (var i = 0; i < batch_E.options.length; i++) {
				var s = batch_E.options[i].getAttribute('data-order');
				if (s == undefined) continue;
				s = s.split(',');
				var a = false;
				for (var i2 = 0; i2 < s.length; i2++) {
					for (var i3 = 0; i3 < order_active.length; i3++) if (order_active[i3] == s[i2]) { a = true; break; }
					if (a) break;
				}
				batch_E.options[i].style.display = (a ? 'block' : 'none');
			}

			for (var i = 0; i < order.length; i++) {
				var E2 = BX('edost_shipment_' + order[i]);
				var E3 = BX('edost_batch_disabled_' + order[i]);
				var a = (v == 'new' ? true : false);
				if (!a) for (var i2 = 0; i2 < o.length; i2++) if (o[i2] == order[i]) { a = true; break; }
				if (!a && E2.checked) {
					E2.checked = false;
					edost_Register('active_main', 'edost_shipment_' + order[i]);
				}
				if (value == 'set') if (a && !E2.checked) {
					E2.checked = true;
					edost_Register('active_main', 'edost_shipment_' + order[i]);
				}
				E3.style.display = (!a ? 'block' : 'none');
			}
		}
		else if (param == 'active_all') {
			var E = BX(value);
			var a2 = (E.checked ? true : false);
			var id = E.id.substr(0, E.id.length - 7);

			// глобальная галочки выделения
			var ar = BX.findChildren(BX(id), {'tag': 'input', 'attribute': {'type': 'checkbox'}}, true);
			for (var i = 0; i < ar.length; i++) {
				ar[i].checked = a2;
				edost_Register('active_main2', ar[i].id);
			}

			var E = BX(value.id.split('_active')[0]);
			if (E) edost_Register('active_all_update', E);
		}
		else if (param == 'active_batch_all') {
			var E = BX(value);
			var a2 = (E.checked ? true : false);

			// галочка сдачи
			var ar = document.getElementsByName(value);
			if (ar) for (var i = 0; i < ar.length; i++) {
				var E = BX('edost_shipment_' + ar[i].value);
				E.checked = a2;
				edost_Register('active_main2', E.id);
			}

			edost_Register('active_all_update');
		}
		else if (param == 'active_main' || param == 'active_main2') {
			// галочка заказа
			var E = BX(value);
			var a2 = (E.checked ? true : false);
			var id = E.id.split('_')[2];

			if (E.id.indexOf('edost_shipment') == 0) {
				var ar = E.parentNode.parentNode.children[2].getElementsByTagName('IMG');
				for (var i = 0; i < ar.length; i++) edost_Register('active_change_' + (a2 ? 'on' : 'off'), ar[i]);

				var ar = E.parentNode.parentNode.children[2].getElementsByClassName('edost_package');
				for (var i = 0; i < ar.length; i++) ar[i].readOnly = (!a2 ? true : false);
			}

			if (param == 'active_main') {
				var E = E.parentNode.parentNode.parentNode.parentNode.parentNode;
				edost_Register('active_all_update', E);
			}
		}
		else if (param == 'active') {
			// включение/выключение команд (печать бланков, отгрузка, оформление доставки)
			var active = edost_Register('active_change', value);

			var c = value.getAttribute('data-code');
			var s = c.split('_');
			var shipment = s[0];
			var name = s[1];
			var required = (BX('register_required_' + shipment) ? true : false)
			var id = 'edost_shipment_' + shipment;

			var a = true;
			var ar = value.parentNode.parentNode.getElementsByTagName('IMG');
			for (var i = 0; i < ar.length; i++) {
				var active2 = (ar[i].className.indexOf('_on') > 0 ? true : false);
				var s = ar[i].getAttribute('data-code');
				var name2 = (s ? s.split('_')[1] : '');

				if (name == 'register' && name2 == 'doc' && (active && !active2 || !active && active2) ||
					name == 'register' && name2 == 'batch' && (active && !active2 || !active && active2) ||
					name == 'doc' && required && name2 == 'register' && active && !active2 ||
					name == 'batch' && name2 == 'register' && active && !active2 ||
					name == 'batch' && name2 == 'doc' && (active && !active2)
					) active2 = edost_Register('active_change', ar[i]);

				if (active2) a = false;
			}

			var a2 = false;
			var E = BX(id);
			if (active) E.checked = true;
			else if (a) E.checked = false;

			var ar = E.parentNode.parentNode.children[2].getElementsByClassName('edost_package');
			for (var i = 0; i < ar.length; i++) ar[i].readOnly = (!E.checked ? true : false);

			var E = value.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
			edost_Register('active_all_update', E);
		}
		else if (param == 'get_doc') {
			var r = {'normal': [], 'label': [], 'count': 0, 'full': false};
			ar = [];
			if (value === 'all') {
				id = ['edost_shipment_register_complete', 'edost_shipment_register_complete_batch', 'edost_shipment_register_complete_batch_full'];
				for (var i = 0; i < id.length; i++) {
					var E = BX(id[i]);
					if (!E) continue;

					doc = E.getElementsByClassName('edost_doc');
					for (var i2 = 0; i2 < doc.length; i2++) ar.push(doc[i2]);

					if (id[i] == 'edost_shipment_register_complete_batch_full') {
						doc = E.getElementsByClassName('edost_batch');
						for (var i2 = 0; i2 < doc.length; i2++) if (doc[i2].checked) {
							r.normal.push(doc[i2].id.split('edost_batch_')[1] + '_103');
							r.count++;
						}
					}
				}
			}
			else if (value === 'print_no_register') {
				var E = BX('edost_shipment_register_new');
				if (E) ar = E.getElementsByClassName('edost_doc_print_no_register');
			}
			else {
				var E = BX('edost_shipment_' + value).parentNode.parentNode;
				ar = E.getElementsByClassName('edost_doc');
			}
			for (var i = 0; i < ar.length; i++) if (ar[i].className.indexOf('_on') > 0) {
				var c = ar[i].getAttribute('data-code').split('_');
				var m = ar[i].getAttribute('data-mode');
				if (c[2] == 'order' || c[2] == 'package') r.full = true;
				var id = c[0] + '_' + c[2];
				if (m == 'normal') r.normal.push(id); else r.label.push(id);
				r.count++;
			}
			return r;
		}
		else if (param == 'search') {
			edost_search_value = value;
			edost_SetParam('register', 'search_shipment');
		}
		else if (param == 'get_print_link') {
			var link = '<a class="edost_print_link" href="edost.php?type=print&mode=%mode%&doc=%doc%" target="_blank">%name%<sup style="font-size: 12px;">%count%</sup></a>';
			var s = [];
			if (value[0].normal == undefined) s.push(link.replace(/%doc%/g, value[0].join('|')).replace(/%mode%/g, 'normal').replace(/%name%/g, value[1]).replace(/%count%/g, value[0].length > 1 ? ' ' + value[0].length + '' : ''));
			else {
				if (value[0].normal.length != 0) s.push(link.replace(/%doc%/g, value[0].normal.join('|')).replace(/%mode%/g, 'normal').replace(/%name%/g, value[1]).replace(/%count%/g, value[0].normal.length > 1 ? ' ' + value[0].normal.length + '' : ''));
				if (value[0].label.length != 0) s.push(link.replace(/%doc%/g, value[0].label.join('|')).replace(/%mode%/g, 'label').replace(/%name%/g, value[2] != undefined ? value[2] : '<?=$control_sign['print']['label']?>').replace(/%count%/g, value[0].label.length > 1 ? ' ' + value[0].label.length + '' : ''));
			}
			if (s.length > 1) return '<div>' + s.join(' / ') + ' <span style="font-size: 15px; color: #AAA;">(<span style="font-size: 15px; font-weight: normal;" class="edost_control_button edost_control_button_low" onclick="edost_Register(\'print\', [this, \'link\'])"><?=$control_sign['print']['open']?></span>)</span></div>';
			else return s[0];
		}
		else if (param == 'print') {
			var id = (value[0] != undefined ? value[0] : false);
			var type = (value[1] != undefined ? value[1] : '');
			var name = (value[2] != undefined ? value[2] : '');
			var full = false;

			var s = [[], []];

			if (type == 'batch' || type == 'batch_general') s[0].push(id + '_' + (name ? name : 'batch'));
			if (type == 'batch_order') full = true;

			if (type == 'link') {
				var E = id.parentNode.parentNode;
				if (!E) return;

				var ar = E.getElementsByTagName('A');
				if (ar) for (var i = 0; i < ar.length; i++) ar[i].click();

				return;
			}

			if (type == '') {
				c = edost_Register('get_doc', id);
				s[0] = c.normal;
				s[1] = c.label;
				full = c.full;
			}
			else if (type == 'batch' || type == 'batch_order') {
				var ar = document.getElementsByName('edost_batch_' + id);
				if (ar) for (var i = 0; i < ar.length; i++) {
					var c = edost_Register('get_doc', ar[i].value);
					if (c.normal.length > 0) s[0] = (s[0].length == 0 ? c.normal : s[0].concat(c.normal));
					if (c.label.length > 0) s[1] = (s[1].length == 0 ? c.label : s[1].concat(c.label));
				}
			}

			if (s[0].length == 0 && s[1].length == 0) alert('<?=$control_sign['print']['no_doc']?>');
			else for (var i = 0; i < s.length; i++) if (s[i].length > 0) {
				window.open('edost.php?type=print&mode=' + (i == 0 ? 'normal' : 'label') + '&doc=' + s[i].join('|'), '_blank');
				if (id !== 'print_no_register' && !full) break; // отключение печати по типам !!!
			}
		}
		else if (param == 'button') {
			var s = value.split('|');
			var value = s[0];
			var id = (s[1] ? s[1] : false);

			if (value != undefined) {
				var s = value.split('button_');
				post += '&button=' + (s[1] != undefined ? s[1] : s[0]);
			}

			if (value == 'button_register' || value == 'button_batch') {
				var ar = ['batch', 'batch_date', 'call', 'profile_shop', 'profile_delivery'];
				for (var i = 0; i < ar.length; i++) {
					var E = BX('edost_' + ar[i]);
					if (E) {
						post += '&' + ar[i] + '=';
						if (E.type == 'checkbox') post += (E.checked ? 'Y' : 'N');
						else post += E.value;
					}
				}
			}

			if (value == 'update') {
				set = edost_register_data;
				post += '&count=' + (edost_register_transfer - 1);
			}
			else if (param == 'history_print') {
				var E = BX('edost_history');
				set = E.value;
			}
			else {
				var s = [];

				if (id) {
					var E = BX('edost_shipment_' + id);
					if (E) E = E.parentNode.parentNode;
				}
				else if (value == 'button_register_repeat') var E = BX('edost_shipment_warning_red');
				else if (value == 'button_batch') var E = BX('edost_shipment_register_complete');
				else if (value == 'button_office') var E = BX('edost_shipment_register_complete_batch');
				else E = BX('edost_shipment_register_new');

				if (value == 'button_date') {
					s.push(id + '_date');
					post += '&date=' + encodeURIComponent(value2);
				}
				else if (value == 'button_office') {
					if (id !== false) s.push(id + '_office');
					else {
						var ar = E.getElementsByClassName('edost_batch_office');
						for (var i = 0; i < ar.length; i++) if (ar[i].checked) s.push(ar[i].id.split('edost_batch_')[1] + '_office');
					}
				}
				else if (value == 'button_print_no_register') {
					var doc = edost_Register('get_doc', 'print_no_register');
					for (var i = 0; i < doc.normal.length; i++) s.push(doc.normal[i].replace('_', '_doc_'));
					for (var i = 0; i < doc.label.length; i++) s.push(doc.label[i].replace('_', '_doc_'));
				}
				else {
					if (value == 'button_register_repeat') {
						var ar = E.getElementsByClassName('edost_register_repeat');
						for (var i = 0; i < ar.length; i++) {
							var c = ar[i].getAttribute('data-code');
							var c2 = c.split('_');
							var E2 = BX('edost_shipment_' + c2[0]);
							if (E2 && E2.checked) s.push(c);
						}
					}
					else {
						var ar = E.getElementsByClassName('edost_register_on');
						for (var i = 0; i < ar.length; i++) {
							var c = ar[i].getAttribute('data-code');
							s.push(c);
						}
					}
					var ar = E.getElementsByClassName('edost_package_on');
					for (var i = 0; i < ar.length; i++) if (!ar[i].readOnly) {
						var c = ar[i].getAttribute('data-code');
						s.push(c + '_' + ar[i].value.replace(/,/g, '.').replace(/[^0-9.]/g, ''));
					}
				}
				edost_register_data = set = s.join('|');
			}
//			alert(set + ' | ' + post);
		}

		if (param != 'button') return;

		var date = new Date();
		var ar = [date.getDate(), date.getMonth() + 1, date.getFullYear(), date.getHours(), date.getMinutes()];
		for (var i = 0; i < ar.length; i++) if (ar[i] < 10) ar[i] = '0' + ar[i];
		post += '&time=' + encodeURIComponent(ar.slice(0, 3).join('.') + ' ' + ar.slice(3, 5).join(':'));

		var status = '';
		if (value == 'button_register' || value == 'button_register_repeat' || value == 'button_batch' || value == 'button_office' || value == 'button_date') {
			status = 'transfer_end';
			edost_Register('transfer_start');

			var E = BX('edost_batch_div');
			if (E) E.style.display = 'none';
		}

		if (value == 'button_print_no_register') edost_Register('button_active', ['button_print_no_register', 0]);

		BX.ajax.post('/bitrix/admin/edost.php', 'type=register&ajax=Y&set=' + set + post, function(res) {
			if (edost_register_transfer_set == -2) return;

			if (res == '') return;
			res = (window.JSON && window.JSON.parse ? JSON.parse(res) : eval('(' + res + ')'));

			if (res.history) {
				edost_register_transfer_history = true;
				BX('edost_history_select').innerHTML = res.history;
				BX('edost_history_div').style.display = 'block';
			}

			if (res.error) {
				edost_Register('transfer_stop', res.error);
				return;
			}

			if (value == 'button_print_no_register') {
				edost_Register('button_active', ['button_print_no_register', 1]);
				edost_Register('print', ['print_no_register']);
			}

			if (status == 'transfer_end') edost_Register('transfer_status', ['transfer_end']);
			if (value == 'update' && (res.register_full != undefined || edost_register_transfer >= 3)) edost_Register('transfer_status', ['receive']);

			if (res.register_full != undefined) {
				var s = new Date();
				edost_register_time_end = s.getTime();
			}

			edost_register_transfer_set = edost_register_transfer;
		});

	}


	// вывод строки контроля (с кнопками)
	function edost_DrawControl(id, flag, control, status_full, index, register, package) {

		if (index == undefined) index = 1;

		var E = BX('edost_shipment_' + index + '_tr');

		if (id == 'update') {
			// скрыть строку контроля, если нет номера накладной
			if (E) {
				var ar = document.getElementsByName('SHIPMENT[' + index + '][TRACKING_NUMBER]');
				if (ar) {
					var a = (ar[0].value != '' ? true : false);
					a = true;
					E.style.display = (a ? 'table-row' : 'none');
				}
			}

			return;
		}

		if (E) BX.remove(E);

		var E = false;
		if (!edost_shipment_edit && !edost_order_create) E = BX('TRACKING_NUMBER_' + index + '_EDIT');
		else {
			E = document.getElementsByName('SHIPMENT[' + index + '][TRACKING_NUMBER]');
			if (E) E = E[0];
		}
		if (!E) return;

		E = BX.findParent(E, {'tag': 'tr'});
		E = BX.findParent(E);

		var E_package = BX('edost_package_' + index + '_tr');
		if (!E_package && package) {
			var s = '';
			s += '<td class="adm-detail-content-cell-l"><?=$control_sign['head_package']?>:</td>';
			s += '<td class="adm-detail-content-cell-r tal">' + package.package_formatted + (package.option_formatted ? '<br>' + package.option_formatted : '') + '</td>';
			if (E) E.appendChild( BX.create('tr', {'props': {'id': 'edost_package_' + index + '_tr', 'innerHTML': s}}) );
		}

		if (id == undefined) return;

		var small = (BX.pos(E).width < 900 ? true : false);
		var flag_special = (flag == 3 || flag == 4 ? true : false);
		var flag_new = (flag == 2 || flag == 4 ? true : false);
		var register_active = (register != undefined && register != 0 ? true : false);

		var s = '';
		s += '<td class="adm-detail-content-cell-l"><div id="edost_control_head_' + id + '">';

		if (edost_shipment_edit || edost_order_create) s += '<input id="edost_shipment_' + id + '_flag" name="edost_shipment_flag" type="hidden" value="' + (flag != undefined ? flag : '0') + '">';

		if (!control) {
			if (edost_control[index].count > 0) s += '<span class="edost_control_button edost_control_button_add" onclick="edost_SetControl(' + id + ', \'add\'' + ')"><?=$control_sign['button']['add']?></span>&nbsp;<br><span style="font-size: 11px;"><?=$control_sign['control_count2']?>: <b>' + edost_control[index].count + '</b></span>&nbsp;';
			else s += '<a href="<?=$edost_path?>' + edost_control[index].shop_id + '" target="_blank"><?=$control_sign['control_buy']?></a>';
		}
		else {
			s += '<div class="edost_control_head"' + (flag_special ? ' style="font-size: 15px;"' : '') + '>';

			if (!register_active) {
				if (small) s += '<div style="height: 18px; background: #eef5f5; padding: 0 5px 0 5px; float: left;"><img class="edost_control_button_new' + (flag_new ? '_active' : '') + '" src="<?=$img_path?>/control_new.png" border="0" onclick="edost_SetControl(' + id + ', \'' + (flag_new ? 'old' : 'new') + '\'' + ')" title="' + (flag_new ? '<?=$control_sign['button']['old']?>' : '<?=$control_sign['button']['new']?>') + '">&nbsp;</div>';
				else if (flag_new) s += '<div style="color: #000; font-size: 12px; border-width: 2px 0 2px 0; border-color: #000; border-style: solid; background: #FB0; padding: 0 5px 0 5px; height: 14px; float: left;">' + (small ? '<?=$control_sign['head_new_small']?>' : '<?=$control_sign['head_new']?>') + '</div><div style="width: 5px; height: 18px; float: left; background: #eef5f5;">&nbsp;</div>';

				if (flag_special) s += '<div class="edost_control_special edost_control_special_' + (small ? 'small' : 'big') + ' edost_control_special_left"></div>';
			}

			if (register_active) s += '<?=$control_sign['head_register']?>';
			else if (flag_special) s += (small ? '<?=$control_sign['head_special_small']?>' : '<?=$control_sign['head_special']?>');
			else s += (small ? '<?=$control_sign['head_small']?>' : '<?=$control_sign['head']?>');

			if (flag_special) s += '<div class="edost_control_special edost_control_special_' + (small ? 'small' : 'big') + ' edost_control_special_right"></div>';

			s += '</div>';

			if (!register_active) {
				s += '&nbsp;';

				if (!small) s += '<span class="edost_control_button edost_control_button_low" style="float: left;" onclick="edost_SetControl(' + id + ', \'' + (flag_new ? 'old' : 'new') + '\'' + ')">' + (flag_new ? '<?=$control_sign['button']['old']?>' : '<?=$control_sign['button']['new']?>') + '</span>';

				var ar = [];
				if (!flag_special) ar.push('<span class="edost_control_button edost_control_button_low" onclick="edost_SetControl(' + id + ', \'special\')">' + (small ? '<?=$control_sign['button']['special_small']?>' : '<?=$control_sign['button']['special']?>') + '</span>');
				else ar.push('<span class="edost_control_button edost_control_button_low" onclick="edost_SetControl(' + id + ', \'normal\')">' + (small ? '<?=$control_sign['button']['normal_small']?>' : '<?=$control_sign['button']['normal']?>') + '</span>');
				ar.push('<span class="edost_control_button edost_control_button_low" onclick="edost_SetControl(' + id + ', \'delete\')">' + (small ? '<?=$control_sign['button']['delete_small']?>' : '<?=$control_sign['button']['delete']?>') + '</span>');
				s += ar.join('&nbsp;&nbsp;|&nbsp;&nbsp;') + '';
			}
		}

		s += '</div></td>';

		s += '<td class="adm-detail-content-cell-r tal">';
		if (register_active) s += '<a class="edost_print_link" href="edost.php?lang=<?=LANGUAGE_ID?>&type=register&control=search_shipment&search=' + id +'"><?=$control_sign['print']['open']?></a>';
		else s += (status_full != '' ? status_full : '<div id="edost_control_' + id + '"></div>');
		s += '</td>';

		if (E) E.appendChild( BX.create('tr', {'props': {'id': 'edost_shipment_' + index + '_tr', 'innerHTML': s}}) );

	}


	// поставновка на контроль, снятие с котроля и изменение флага + отчистка списка 'changed'
	function edost_SetControl(id, flag, E) {

		var update_shipment = true;
		var post = '';

		if (flag == 'changed_delete') {
			BX('control_changed_div').style.display = 'none';
		}
		else if (E != undefined) {
			if (flag == 'delete_register') edost_Register('delete', 'order|' + id);
			else if (flag == 'order_date') {
				edost_Register('delete', 'order|' + id);
				post += '&date=' + encodeURIComponent(E);
			}
			else if (flag == 'order_batch_delete') edost_Register('delete', 'batch|' + id);
			else if (flag == 'batch_delete') {
				E.style.display = 'none';

				E = BX.findNextSibling(E);
				if (E) E.style.display = 'inline';

				E = BX('edost_batch_button_' + id);
				if (E) E.style.display = 'none';

				E = BX('edost_batch_print_' + id);
				if (E) E.style.display = 'none';

				var E = BX('edost_batch_' + id);
				if (E) E.parentNode.remove();

				var ar = document.getElementsByName('edost_batch_' + id);
				if (ar) for (var i = 0; i < ar.length; i++) edost_Register('delete', 'order|' + ar[i].value);
			}
		}
		else if (flag == 'auto' || flag == 'auto_off') {
			update_shipment = false;
			var auto_off = (flag == 'auto_off' ? true : false);

			id = id.toString().split(',');
			var a = 0;
			var id_new = [];
			for (var i = 0; i < id.length; i++) {
				var E = BX('edost_shipment_' + id[i] + '_value');
				if (!E) return;

				if (auto_off && E.value == 1) id_new.push(id[i]);

				if (i == 0) {
					a = (E.value == 1 || flag == 'auto_off' ? 0 : 1);
					flag = (a ? 'new' : 'old');
				}
				E.value = a;

				var E = BX('edost_shipment_' + id[i] + '_img');
				if (E) E.className = 'edost_control_button_new' + (a ? '_active' : '');
			}

			id = (auto_off ? id_new.join(',') : id.join(','));
		}
		else {
			if (edost_shipment_edit || edost_order_create) {
				var E = BX('edost_shipment_' + id + '_flag');
				var f = E.value;

				if (flag == 'delete') edost_control[1].count++;
				else if (flag == 'add') edost_control[1].count--;

				if (flag == 'delete') f = 0;
				else if (flag == 'new') f = (f == 3 ? 4 : 2);
				else if (flag == 'old') f = (f == 4 ? 3 : 1);
				else if (flag == 'special') f = (f == 2 ? 4 : 3);
				else if (flag == 'normal') f = (f == 4 ? 2 : 1);
				else f = 1;

				edost_DrawControl(id, f, f == 0 ? false : true, '', 1);

				return;
			}

			var E = BX('edost_control_head_' + id);
			if (E) E.innerHTML = '';

			var E = BX('edost_control_' + id);
			if (E) E.innerHTML = '<?=$loading_small?>';
		}

		BX.ajax.post('<?=$ajax_file?>', 'mode=control&id=' + id + '&flag=' + flag + post, function(r) {
			if (r != 'OK') alert(r);
			if (!update_shipment) return;
			edost_shipment = false;
			edost_InsertControl();
		});

	}


	// интеграция данных отгрузки (проверка на возможность постановки на контроль)
	function edost_InsertControl() {

		var shipment = [];
		var shipment_id = [];

		// поиск блоков с отгрузками
		for (var i = 1; i < 100; i++) {
			var E = BX('SHIPMENT_ID_' + i);
			if (!E) break;
			if (E.value != '') {
                var id = E.value;
				var E = BX('STATUS_ALLOW_DELIVERY_' + i); // BX('STATUS_DEDUCTED_' + i),  BX('STATUS_SHIPMENT_' + i);
				var E2 = BX('TRACKING_NUMBER_' + i + '_EDIT');
				shipment.push({i: i, id: id, allow_delivery: E ? E.value : '', tracking_number: E2 ? edost_office.trim(E2.value) : ''});
				shipment_id.push(id);
			}
		}
//		edost_ShowData(shipment, '', 20);

		// проверка на изменение параметров
		var a = false;
		if (edost_shipment === false || shipment.length != edost_shipment.length) a = true;
		else for (var i = 0; i < shipment.length; i++)
			if (shipment[i].id != edost_shipment[i].id ||
				shipment[i].allow_delivery != edost_shipment[i].allow_delivery ||
				shipment[i].tracking_number != edost_shipment[i].tracking_number) { a = true; break; }
		if (!a) return;

		edost_shipment = shipment;
		if (shipment_id.length == 0) return;

		BX.ajax.post('<?=$ajax_file?>', 'mode=control&id=' + shipment_id.join(','), function(r) {
			r = (window.JSON && window.JSON.parse ? JSON.parse(r) : eval('(' + r + ')'));

			for (var i = 0; i < shipment_id.length; i++) {
				var v = false, p = false;
				if (r.data) for (var i2 = 0; i2 < r.data.length; i2++) if (r.data[i2].id == shipment_id[i]) { v = r.data[i2]; break; }
				if (r.package) for (var i2 = 0; i2 < r.package.length; i2++) if (r.package[i2].id == shipment_id[i]) { p = r.package[i2]; break; }

				if (p) {
					// скрыть оригинальное поле "PACKAGE"
			        var s = document.querySelectorAll('div[data-id="buyer"] td.adm-detail-content-cell-r div');
					if (s) for (var i2 = 0; i2 < s.length; i2++) if (s[i2].innerHTML == p.prop) { BX.findParent(s[i2], {'tag': 'tr'}).style.display = 'none'; break; }
				}

				var index = i + 1;

				// открыть блок с подробной информацией по отгрузке на странице просмотра заказа + переход на отгрузку по якорю со страницы контроля заказов
				var E = BX('SHIPMENT_SECTION_' + index);
				if (E && E.style.display == 'none') {
					var a = false;
					var s = window.location.href.split('&edost_link=shipment_');
					if (s[1] == undefined) a = true;
					else {
						s = s[1].split('#');
						if (s[0] == v.id) a = 'scroll';
					}
					if (a) {
						E.style.display = 'block';
						if (a === 'scroll') BX('shipment_container_' + index).scrollIntoView();
						BX.style(BX('SHIPMENT_SECTION_SHORT_' + index), 'display', 'none');
					}
				}

				if (edost_shipment_edit || edost_order_create) {
					var E = BX('shipment_container_' + index);
					var E2 = BX('edost_shipment_flag_start');
					if (E && !E2) E.appendChild( BX.create('input', {'props': {'type': 'hidden', 'name': 'edost_shipment_flag_start', 'value': v.flag != undefined ? v.flag : 0}}) );
				}

				edost_control[index] = {'count': v.control_count ? v.control_count : 0, 'shop_id': v.shop_id ? v.shop_id : 0};

				edost_DrawControl(v.id, v.flag, v.control, v.status_full, index, v.register, p);

				if (v === false) continue;

				if (!edost_shipment_edit && !edost_order_create) edost_SetTracking(v.tracking_example, v.tracking_format, index, false);
			}
		});

	}

	// проверка на заполнение полей при нажатии кнопки "Сохранить" и "Применить"
	function edost_CheckProp(name) {

		var r = false;

<?		if ($edost_locations) { $s = GetMessage('EDOST_LOCATIONS_ERROR'); ?>
		var E_zip = BX('edost_zip');
		var E_country = BX('edost_country');
		var E_location = BX('edost_shop_LOCATION');
		if (E_location && E_location.value && E_zip && E_zip.type != 'hidden' && E_country && E_country.value.split('_')[0] == 0 && E_zip.value.replace(/ /g, '') == '' && !edost_delivery_shop) r = "<?=$s['zip']?>\n<?=$sign['order_error']?>!";
<?		} ?>

		var E = BX('edost_address');
		if (E && E.value == '') {
			var E = BX('PROFILE_1');
			if (E && E.selectedIndex != -1) {
				var s = E.options[E.selectedIndex].getAttribute('data-edost_address');
				if (s != undefined && s == '') r = "<?=$sign['office_unchecked']?>!\n<?=$sign['order_error']?>!";
			}
		}

		if (r) {
			alert(r);

			window.setTimeout(function(name) {
				var E = BX.findChild(document, {attribute: {'name': '' + name + ''}}, true);
				if (E) {
					E.disabled = false;
					E.classList.remove('adm-btn-load');
				}
				var E = BX.findChild(document, {attribute: {'class': name == 'apply' ? 'adm-btn-load-img' : 'adm-btn-load-img-green'}}, true);
				if (E) E.remove();
			}, 500, name);

			return false;
		}

		return true;

	}


<? if ($edost_locations) { ?>
	function edost_UpdateDelivery() {

		edost_alarm = false;

		var E = BX('PROFILE_1');

		if (edost_order_create) BX.Sale.Admin.OrderAjaxer.sendRequest(BX.Sale.Admin.OrderEditPage.ajaxRequests.refreshOrderData());
		else BX.Sale.Admin.OrderShipment.prototype.getDeliveryPrice();

		var location = BX('edost_shop_LOCATION').value;
		var city2 = BX('edost_city2');

		BX.ajax.post('<?=$arResult['component_path_locations']?>/edost_location.php', 'type=html&mode=city&ajax=Y&edost_delivery=Y&id=' + location + '&edost_city2=' + (city2 ? encodeURIComponent(city2.value) : ''), function(r) {
			BX('edost_location_city_div').innerHTML = r;
			BX('edost_location_city_loading').innerHTML = '';
			BX('edost_location_address_loading').innerHTML = '';
			var E = BX('edost_location_zip_hint'); if (E) E.innerHTML = '';
			var E = BX('edost_zip'); if (E) E.blur();
		});

	}


	function edost_UpdateLocation(mode, reload, location) {

		if (edost_delivery_shop === 'location_updated') return;
		if (edost_delivery_shop === true && location === 0) edost_delivery_shop = 'location_updated';

		var post = [];
		var ar = [['site_id', 'SITE_ID'], ['person_type', 'PERSON_TYPE_ID'], ['user_id', 'USER_ID'], ['profile_id', 'BUYER_PROFILE_ID']];
		for (var i = 0; i < ar.length; i++) {
			var E = BX(ar[i][1]);
			if (E) post.push(ar[i][0] + '=' + encodeURIComponent(E.value));
		}
		post = post.join('&');

		if (mode == 'address') {
			var E = BX('PROFILE_1');
			var delivery_id = (E ? E.value : '');

			if (location == undefined) location = BX('edost_shop_LOCATION').value;

			var ar = BX.findChildren(BX('edost_location_address_div'), {'tag': 'input'}, true);
			var prop2 = [];
			for (var i = 0; i < ar.length; i++) prop2.push(ar[i].name + '=' + encodeURIComponent(ar[i].value));

			post += (post != '' ? '&' : '') + (!reload ? 'ajax=Y&' : '') + 'delivery_id=' + delivery_id + '&' + prop2.join('&');
		}

		if (location == undefined) location = 0;

		BX.ajax.post('<?=$arResult['component_path_locations']?>/edost_location.php', 'type=html&admin=Y&mode=' + mode + '&edost_delivery=Y&id=' + location + '&' + post, function(r) {
			BX(mode == 'city' ? 'edost_location_admin_city_td' : 'edost_location_address_div').innerHTML = r;
			if (edost_order_create && mode == 'city' && reload) BX.Sale.Admin.OrderAjaxer.sendRequest(BX.Sale.Admin.OrderEditPage.ajaxRequests.refreshOrderData());
		});

	}


	function edost_InsertLocation(reload) {

		edost_delivery_shop = false;

		if (!reload) {
			var E = BX('BLOCK_DELIVERY_SERVICE_1');
			if (E) E = BX.findParent(E, {'tag': 'div', 'class': 'adm-bus-pay-section-right'});

			if (!edost_order_create) {
				var E2 = BX('edost_location_admin_city_div');
				if (E2) {
					var s = E2.innerHTML;
					BX.remove(E2);
					E.insertBefore( BX.create('div', {'props': {'id': 'edost_location_admin_city_div', 'style': 'display: none;', 'className': 'adm-bus-table-container caption border', 'innerHTML': s}}), E.children[0]);
				}
			}

			var E2 = BX('edost_location_admin_passport_div');
			if (E2) {
				var s = E2.innerHTML;
				BX.remove(E2);
				E.insertBefore( BX.create('div', {'props': {'id': 'edost_location_admin_passport_div', 'style': !edost_order_create ? 'display: none;' : '', 'className': 'adm-bus-table-container caption border', 'innerHTML': s}}), E.children[2]);
			}

			var E2 = BX('edost_location_admin_address_div');
			if (E2) {
				var s = E2.innerHTML;
				BX.remove(E2);
				E.insertBefore( BX.create('div', {'props': {'id': 'edost_location_admin_address_div', 'style': !edost_order_create ? 'display: none;' : '', 'className': 'adm-bus-table-container caption border', 'innerHTML': s}}), E.children[2]);
			}

			if (!edost_order_create) return;
		}

		var prop_div = BX('order_properties_container');

		if (!reload) {
			var E_location = false;
			var ar = [<?=implode(', ', $prop_location)?>];
			for (var i = 0; i < ar.length; i++) {
				var E = BX.findChild(prop_div, {'attribute': {'name': 'PROPERTIES[' + ar[i] + ']'}}, true);
				if (E != undefined) { E_location = E; break; }
			}
			if (!E_location) {
				window.setTimeout('edost_InsertLocation(' + (reload ? 'true' : 'false') + ')', 100);
				return;
			}
		}

		var E = BX.findParent(prop_div);

		if (reload) {
			edost_UpdateLocation('address', true);
			edost_UpdateLocation('city', true);
		}
		else {
			var E2 = BX('edost_location_admin_city_div');
			var s = E2.innerHTML;
			BX.remove(E2);
			E.insertBefore( BX.create('div', {'props': {'id': 'edost_location_admin_city_div', 'className': 'adm-bus-table-container caption border', 'innerHTML': s}}), BX.findNextSibling(prop_div));
		}

		// удаление полей битрикса
		var ar = [<?=implode(', ', $prop_remove)?>];
		for (var i = 0; i < ar.length; i++) {
			var E = BX.findChild(prop_div, {'attribute': {'name': 'PROPERTIES[' + ar[i] + ']'}}, true);
			if (!E) continue;
			E = BX.findParent(E, {'tag': 'tr'});
			if (!E) continue;

			var E2 = BX.findParent(E);
			BX.remove(E);
			if (E2.children.length == 0) {
				var E = BX.findParent(E2);
				if (E) BX.remove(E);
			}
		}

	}
<? } ?>


	// поиск позиции и переменной в строке по цепочке соответствий
	function edost_StringPosVal(s, ar, position, before) {
		var v = '', p = 0, p2 = 0;

		for (var i = 0; i < ar.length; i++)
			if (ar[i] == 'VALUE') p2 = p;
			else {
				p = s.indexOf(ar[i], p);
				if (p == -1) break;
				if (p2 != 0) { v = s.substr(p2, p-p2).replace(/^\s+|\s+$/gm, ''); break; }
				if (!before) p += ar[i].length;
			}

		return (position != undefined ? p : [p, v]);
	}


	// замена в строке по цепочке соответствий (s - строка, s2 - вставить, ar - цепочка соответствий, ar2 - цепочка соответствий для переменной)
	function edost_InsertString(s, s2, ar, ar2, before) {
		if (ar2 != undefined && ar2 != false) {
			v = edost_StringPosVal(s, ar2);
			if (v[1] != '') s2 = s2.replace('%value%', v[1]);
		}

		var p = edost_StringPosVal(s, ar, true, before);
		if (p > 0) s = s.substr(0, p) + s2 + s.substr(p);

		return s;
	}


	BX.ready(function() {
		if (!BX.Sale) return;

		// пример и формат накладной доставки
		var s = BX.Sale.Admin.OrderShipment.prototype.initUpdateTrackingNumber.toString();
		if (s.indexOf("/* edost */") == -1) {
			s = edost_InsertString(s, ' var edost_index = this.index;    /* edost */ ', ['{']);
			s = edost_InsertString(s, ' edost_SetTracking("update", "", edost_index, true); ', ['BX.bind', 'click', '{']);
			s = edost_InsertString(s, ' edost_SetTracking("update", "", edost_index, false); ', ['blur', 'BX.proxy', '{']);
//			alert(s);
			BX.Sale.Admin.OrderShipment.prototype.initUpdateTrackingNumber = eval("(" + s + ")");
		}

		if (edost_shipment_edit || edost_order_create) {
			// редактирование отгрузки + новый заказ
			edost_InsertShipmentEdit(false);

			// кнопки "сохранить" и "применить"
			var ar = ['save', 'apply'];
			for (var i = 0; i < ar.length; i++) {
				var E = BX.findChild(document, {attribute: {'name': ar[i]}}, true);
				if (E) E.onclick = new Function('', 'return edost_CheckProp("' + ar[i] + '");');
			}

			var s = BX.Sale.Admin.OrderShipment.prototype.updateDeliveryList.toString();
			if (s.indexOf("/* edost */") == -1) {
				s = edost_InsertString(s, ' edost_ChangeDelivery("service");    /* edost */ ', ['{']);
				s = edost_InsertString(s, ' edost_ChangeDelivery("head"); ', ['for', '}', '}']);
//				alert(s);
				BX.Sale.Admin.OrderShipment.prototype.updateDeliveryList = eval("(" + s + ")");
			}
			var s = BX.Sale.Admin.OrderShipment.prototype.updateProfiles.toString();
			if (s.indexOf("/* edost */") == -1) {
				s = edost_InsertString(s, ' edost_InsertShipmentEdit();    /* edost */ ', ['BX.bind('], false, true);
//				alert(s);
				BX.Sale.Admin.OrderShipment.prototype.updateProfiles = eval("(" + s + ")");
			}
			var s = BX.Sale.Admin.OrderShipment.prototype.getDeliveryPrice.toString();
			if (s.indexOf("/* edost */") == -1) {
				s = edost_InsertString(s, ' edost_InsertShipmentEdit();    /* edost */ ', ['BX.proxy(function', '{']);
//				alert(s);
				BX.Sale.Admin.OrderShipment.prototype.getDeliveryPrice = eval("(" + s + ")");
			}
			var s = BX.Sale.Admin.OrderShipment.prototype.updateDeliveryLogotip.toString();
			if (s.indexOf("/* edost */") == -1) {
				s = edost_InsertString(s, ' /* edost */ if (BX("PROFILE_"+this.index)) ', ['if'], false, true);
//				alert(s);
				BX.Sale.Admin.OrderShipment.prototype.updateDeliveryLogotip = eval("(" + s + ")");

			}
			var s = BX.Sale.Admin.OrderEditPage.registerFieldsUpdaters.toString();
			if (s.indexOf("/* edost */") == -1) {
				s = edost_InsertString(s, ' if (%value% == "DELIVERY_ERROR") continue;     /* edost */ ', ['for', '{'], ['var ', 'VALUE', ' in ']);
//				alert(s);
				BX.Sale.Admin.OrderEditPage.registerFieldsUpdaters = eval("(" + s + ")");
			}

			edost_ChangeDelivery('head');
			window.setInterval('edost_DrawControl("update")', 1000);

<?			if ($edost_locations) { ?>
	        edost_InsertLocation();
<?			} ?>
		}
		else {
			// просмотр заказа
			var s = BX.Sale.Admin.OrderAjaxer.sendRequest.toString();
			if (s.indexOf("/* edost */") == -1) {
				s = edost_InsertString(s, ' edost_InsertControl();    /* edost */ ', ['BX.Sale.Admin.OrderAjaxer.refreshOrderData.callback', ';']);
//				alert(s);
				BX.Sale.Admin.OrderAjaxer.sendRequest = eval("(" + s + ")");
			}
		}
	});

<?	} ?>
</script>

<?
}




if ($mode == 'setting') {

	// сохранение привязок доставки к оплате
	if (!empty($param['save']) && $param['paysystem']) {
		$new = $param['save'];
//		echo '<br><b>new:</b> <pre style="font-size: 12px">'.print_r($new, true).'</pre>';

		$zero_tariff_paysystem = (!empty($_POST['zero_tariff_paysystem']) ? intval($_POST['zero_tariff_paysystem']) : 0);
		$zero_tariff = (!empty($_POST['zero_tariff']) ? $_POST['zero_tariff'] : array());
		foreach ($new as $n_key => $n) {
			if (isset($n['cod'])) {
				$s = explode(',', $n['cod']);
				foreach ($s as $k2 => $v2) if (in_array($v2, $zero_tariff)) unset($s[$k2]);
			}
			else {
				$s = (!empty($n['save']) ? explode(',', $n['save']) : array());
				if ($zero_tariff_paysystem == $n_key && !empty($zero_tariff)) $s = array_merge($s, $zero_tariff);
				foreach ($n['delivery'] as $k2 => $v2) {
					$a = ($zero_tariff_paysystem != 0 && in_array($k2, $zero_tariff) ? true : false);
					if (!$a && $n['active'] == 'Y' && ($n['list'] == 'N' || $n['list'] == 'Y' && $v2 == 'Y')) $s[] = $k2;
				}
			}

			\Bitrix\Sale\Internals\DeliveryPaySystemTable::setLinks($n_key, \Bitrix\Sale\Internals\DeliveryPaySystemTable::ENTITY_TYPE_PAYSYSTEM, $s);
		}

		die();
	}


	// способы оплаты магазина
	$paysystem = array();
	$filter = array('=ACTIVE' => 'Y');
	if (version_compare(SM_VERSION, '18.5.0') >= 0) $filter['=ENTITY_REGISTRY_TYPE'] = 'ORDER';
	$ar = \Bitrix\Sale\PaySystem\Manager::getList(array('select' => array('ID', 'NAME', 'ACTION_FILE'), 'filter' => $filter));
	while ($v = $ar->Fetch()) {
		$p = array('name' => str_replace(array('<', '>'), array('&lt;', '&gt;'), $v['NAME']));
	    if (substr($v['ACTION_FILE'], -11) == 'edostpaycod') $p['cod'] = true;
		$p['tariff_disabled'] = array();
		$paysystem[$v['ID']] = $p;
	}
//	echo '<br><b>pay_system:</b><pre style="font-size: 12px">'.print_r($paysystem, true).'</pre>';

	// сайты битрикса
	$bitrix_site = array('all' => array('ID' => 'all'));
	$ar = CSite::GetList($p1 = 'sort', $p2 = 'asc', array());
	while ($v = $ar->Fetch()) $bitrix_site[] = array('ID' => $v['ID'], 'name' => $v['NAME']);
//	echo '<br><b>bitrix_site:</b><pre style="font-size: 12px">'.print_r($bitrix_site, true).'</pre>';

	// привязка доставки к оплате
	$service_paysystem = array();
	$ar = \Bitrix\Sale\Internals\ServiceRestrictionTable::getList(array('filter' => array('=CLASS_NAME' => '\Bitrix\Sale\Delivery\Restrictions\ByPaySystem')));
	while ($v = $ar->fetch()) $service_paysystem[$v['SERVICE_ID']] = $v;
//	echo '<br><b>service_paysystem:</b> <pre style="font-size: 12px">'.print_r($service_paysystem, true).'</pre>';

	// ставки НДС
	$vat = array();
	if (version_compare(SM_VERSION, '18.0.0') >= 0) {
		\Bitrix\Main\Loader::includeModule('catalog');
		$ar = CCatalogVat::GetListEx(array(), array('ACTIVE' => 'Y'), false, false, array('ID', 'NAME', 'RATE'));
		while ($v = $ar->Fetch()) $vat[$v['ID']] = $v;
	}
//	echo '<br><b>vat:</b><pre style="font-size: 12px">'.print_r($vat, true).'</pre>';

	// загрузка модулей с учетом привязки к сайтам
	$ar = CDeliveryEDOST::GetModule();
	$restriction_site = $ar['restriction_site'];
	$module = $module_original = $ar['module'];
	$module_count = count($module);
	$module_new = false;
	$module_site = (count($bitrix_site) > 2 ? true : false);
	$module_key_start = false;

	// генерация нового тарифа
	if ($module_count == 0 || $param['module_id'] === 'new' || isset($param['save'][0])) {
		$v = array(
			'ID' => 0,
			'CODE' => 'edost',
			'PARENT_ID' => 0,
			'NAME' => $admin_sign['delivery'],
			'ACTIVE' => 'Y',
			'DESCRIPTION' => '',
			'SORT' => 100,
			'LOGOTIP' => '',
			'CLASS_NAME' => '\Bitrix\Sale\Delivery\Services\Automatic',
			'CURRENCY' => 'RUB',
			'TRACKING_PARAMS' => array(),
			'ALLOW_EDIT_SHIPMENT' => 'Y',
			'CONFIG' => array('MAIN' => array('SID' => 'edost', 'DESCRIPTION_INNER' => '', 'MARGIN_VALUE' => '', 'MARGIN_TYPE' => 'CURRENCY', 'OLD_SETTINGS' => '')),
			'PROFILES' => array(),
		);
		$module = array(0 => $v);
		$module_new = true;
		$param['module_id'] = false;
	}

	// сайты к которым привязан модуль
	$site_link = array();
	if ($module_site) foreach ($module as $m_key => $m) {
		$string = array();
		$list = array();
		foreach ($bitrix_site as $site_key => $site) {
			$a = (!empty($restriction_site[$m_key]['PARAMS']['SITE_ID']) ? $restriction_site[$m_key]['PARAMS']['SITE_ID'] : false);
			$a = (!$a && $site['ID'] === 'all' || $a && in_array($site['ID'], $a) ? true : false);
			$name = ($site_key === 'all' ? $admin_sign['module_config']['site_all'] : $site['name'].' ('.$site['ID'].')');
			$list[] = array(
				'id' => ($site_key === 'all' ? '' : $site['ID']),
				'code' => 'module['.$m_key.'][site]['.$site['ID'].']',
				'name' => $name,
				'active' => $a,
			);
			if ($a) {
				$string[] = $name;
				if ($module['ACTIVE'] == 'Y') {
					if ($site_key !== 'all') $site_link[$site['ID']][] = $m_key;
					else foreach ($bitrix_site as $k => $v) if ($k !== 'all') $site_link[$v['ID']][] = $m_key;
				}
			}
		}
		$module[$m_key]['site_string'] = $string;
		$module[$m_key]['site_list'] = $list;
	}

	// загрузка профилей
	$edost_delivery_id = array();
	foreach ($module as $m_key => $m) if (!empty($m['ID'])) {
		$edost_delivery_id[$m['ID']] = $m['ID'];
		$profile = array();
		$ar = \Bitrix\Sale\Delivery\Services\Table::GetList(array('filter' => array('=PARENT_ID' => $m['ID'])));
		while ($v = $ar->fetch()) {
			$id = $v['ID'];
			$v['profile_id'] = (isset($v['CONFIG']['MAIN']['PROFILE_ID']) ? $v['CONFIG']['MAIN']['PROFILE_ID'] : '');
			$v['tariff_id'] = ceil(intval($v['profile_id']) / 2);
			$v['key'] = $v['profile_id'].'_'.$id;
			$profile[$v['key']] = $v;
			$edost_delivery_id[$id] = $id;
		}
		$module[$m_key]['PROFILES'] = $profile;
		if (isset($module_original[$m_key])) $module_original[$m_key]['PROFILES'] = $profile;
	}

	// загрузка настроек модуля + удаление остальных модулей при индивидуальной настройке
	$key = false;
	foreach ($module as $m_key => $m) {
		$key = $m_key;
		if ($param['module_id'] !== false && $m_key != $param['module_id']) {
			unset($module[$m_key]);
			continue;
		}
		$c = Bitrix\Sale\Delivery\Services\Manager::createObject($m);
		$c = $c->getConfig();
		$c = $c['all']['ITEMS'];
		$module[$m_key]['CONFIG']['CONFIG'] = $c;
	}
	$module_list = (count($module) > 1 ? true : false);

	if (empty($module)) { echo 'ERROR: The "edost.delivery" module is not installed!'; die(); }

	// загрузка тарифов, включенных в личном кабинете eDost
	foreach ($module as $m_key => $m) {
		$data = edost_class::RequestData('', $m['CONFIG']['CONFIG']['id']['VALUE'], $m['CONFIG']['CONFIG']['ps']['VALUE'], 'active=Y', 'delivery');
		if (isset($data['error'])) $data['error'] = CDeliveryEDOST::GetEdostError($data['error']);
		$module[$m_key]['edost'] = $data;
	}
/*
	// загрузка названий тарифов с сервера edost
	if ($mode == 'setting_module' && !empty($_POST['title'])) {
		foreach ($module as $k => $v) foreach ($v['PROFILES'] as $k2 => $v2) if (!empty($v['edost']['data'][$v2['profile_id']])) {
			$p = $v['edost']['data'][$v2['profile_id']];
			$s = $p['company'];
			if ($p['name'] != '') $s .= ' ('.$p['name'].')';
			$module[$k]['PROFILES'][$k2]['NAME'] = $s;
		}
	}
*/
	// добавление новых тарифов + сортировка
	foreach ($module as $m_key => $m) {
		$n = 0;
		$e = (!empty($m['edost']['data']) ? $m['edost']['data'] : array());
		$p = $m['PROFILES'];

		$zero = false;
		foreach ($p as $k => $v) {
			$id = $v['profile_id'];
			if ($id === '0') {
				$zero = true;
				$p[$k]['company_id'] = 0;
			}
			else if (!empty($e[$id])) {
				$e[$id]['bitrix'] = $v['ID'];
				$e[$id]['bitrix_key'] = $k;
				$p[$k]['company_id'] = $e[$id]['company_id'];
			}
		}

		// добавление нулевого тарифа
		if (!$zero) {
			$s = GetMessage('EDOST_DELIVERY_ERROR');
			$p['0'] = array('NAME' => $s['zero_tariff'], 'DESCRIPTION' => '', 'company_id' => 0, 'profile_id' => '0', 'key' => '0');
		}

		$tariff_new = false;
		$tariff_update = false;
		foreach ($e as $v)
			if (isset($v['bitrix'])) {
				// ошибка "изменение тарифов в личном кабинете eDost, необходимо пересохранить настройки"
				if ($m['ACTIVE'] == 'Y' && $p[$v['bitrix_key']]['ACTIVE'] == 'N') $tariff_update = true;
			}
			else {
				// ошибка "включен новый тариф в личном кабинете eDost, необходимо обновить модуль"
				if ($v['profile'] > DELIVERY_EDOST_TARIFF_COUNT*2) {
					$tariff_new = true;
					continue;
				}
				// добавление новых тарифов
				if ($m['ACTIVE'] == 'Y') $tariff_update = true;
				$p[$v['profile']] = array(
					'NAME' => $v['company'].($v['name'] != '' ? ' ('.$v['name'].')' : ''),
					'DESCRIPTION' => '',
					'company_id' => $v['company_id'],
					'profile_id' => $v['profile'],
					'key' => $v['profile'],
				);
			}
		if ($tariff_update) $module[$m_key]['edost']['error'] = $admin_sign['warning']['tariff_update'];
		else if ($tariff_new) $module[$m_key]['edost']['error'] = str_replace('%href%', '/bitrix/admin/update_system_partner.php?lang='.LANGUAGE_ID, $admin_sign['warning']['tariff_new']);

		$ar = array();
		foreach ($p as $v)
			if (!isset($v['company_id'])) $ar[] = 0;
			else {
				$n++;
				$ar[] = (!empty($e[$v['profile_id']]['sort']) ? $e[$v['profile_id']]['sort'] : 0);
			}
		if ($n > 1) {
			array_multisort($ar, SORT_ASC, SORT_NUMERIC, $p);
			$ar = $p;
			$p = array();
			foreach ($ar as $v) $p[$v['key']] = $v;
		}

		foreach ($p as $k => $v) if (isset($v['company_id'])) {
			$p[$k]['company_ico'] = edost_class::GetCompanyIco($v['company_id'], $v['tariff_id']);
			$p[$k]['NAME2'] = $v['NAME'].($v['company_id'] == 0 ? ' ('.$admin_sign['zero_tariff2'].')' : '').' <span style="opacity: 0.5;">['.$v['ID'].']</span>';
		}

		$module[$m_key]['PROFILES'] = $p;
	}

	// ошибка "разные модули подключены к одному сайту"
	foreach ($site_link as $k => $v) if (count($v) > 1)
		foreach ($v as $v2) if (isset($module[$v2])) $module[$v2]['edost']['error'] = $admin_sign['warning']['site'];

	// доступность доставки (модуль включен и нет ошибок)
	$module_active = $module_error = false;
	foreach ($module as $m_key => $m) if ($m['ACTIVE'] == 'Y') {
		if (!empty($m['edost']['error'])) $module_error = true;
		foreach ($m['PROFILES'] as $p) if (!empty($p['company_id'])) $module_active = true;
	}

	// привязка доставки к оплате
	$zero_tariff_paysystem = false;
	foreach ($paysystem as $p_key => $p) {
		$link = \Bitrix\Sale\Internals\DeliveryPaySystemTable::getLinks($p_key, \Bitrix\Sale\Internals\DeliveryPaySystemTable::ENTITY_TYPE_PAYSYSTEM);
		foreach ($module as $m_key => $m) if (!empty($m['ID']) && $m['ACTIVE'] == 'Y')
			foreach ($m['PROFILES'] as $k => $v) if (isset($v['company_id']) && $v['company_id'] == 0 && !empty($v['ID'])) {
				$i = array_search($v['ID'], $link);
				if ($i !== false) {
					if ($zero_tariff_paysystem === false) $zero_tariff_paysystem = $p_key; else $zero_tariff_paysystem = 0;
					if (empty($p['cod'])) $module[$m_key]['PROFILES'][$k]['paysystem'][$p_key] = $p_key;
				}
			}
		$paysystem[$p_key]['link'] = $link;
	}
	foreach ($paysystem as $p_key => $p) {
		$p['delivery'] = array();
		$p['save'] = array();
		$p['edost'] = array();
		$radio = array();

		// добавление активных тарифов
		foreach ($module as $m_key => $m) if (!empty($m['ID']) && $m['ACTIVE'] == 'Y') {
			$n = 0;
			foreach ($m['PROFILES'] as $k => $v) if (isset($v['company_id']) && !empty($v['ID'])) {
				if (!empty($zero_tariff_paysystem) && $v['company_id'] == 0) continue;

				$n++;
				$p['delivery'][] = $v['ID'];
				$i = array_search($v['ID'], $p['link']);
				if ($i !== false) {
					$p['edost'][$m_key][$k] = $v['ID'];
					if (empty($p['cod'])) $module[$m_key]['PROFILES'][$k]['paysystem'][$p_key] = $p_key;
					unset($p['link'][$i]);
				}
			}
			if (empty($p['edost'][$m_key])) $s = 'none';
			else if (count($p['edost'][$m_key]) == $n) $s = 'all';
			else $s = 'list';
			$radio[$m_key] = $s;
		}

		// удаление отключенных тарифов
		foreach ($p['link'] as $k => $v) if (in_array($v, $edost_delivery_id)) unset($p['link'][$k]);

		// сторонние модули доставки
		$p['save'] = $p['link'];

		$n = count($radio);
		$s = array('all' => 0, 'list' => 0, 'none' => 0);
		foreach ($radio as $v) $s[$v]++;
		if ($s['all'] == $n) $p['radio'] = 'all';
		else if ($s['none'] == $n) $p['radio'] = 'none';
		else $p['radio'] = 'list';

		$paysystem[$p_key] = $p;
	}
	$no_paysystem = array();
	foreach ($module as $m_key => $m) if ($m['ACTIVE'] == 'Y')
		foreach ($m['PROFILES'] as $p) if (isset($p['company_id']) && empty($p['paysystem'])) $no_paysystem[] = $p['NAME2'];

//	echo '<br><b>pay_system ('.count($paysystem).'):</b> <pre style="font-size: 12px">'.print_r($paysystem, true).'</pre>';
//	echo '<br><b>module ('.count($module).'):</b> <pre style="font-size: 12px">'.print_r($module, true).'</pre>';
//	echo '<br><b>edost_delivery_id:</b> <pre style="font-size: 12px">'.print_r($edost_delivery_id, true).'</pre>';

	// сохранение настроек модуля
	if (!empty($param['save'])) {
		$r = array();
		$new = $param['save'];

	 	$ar = array('NAME', 'DESCRIPTION');
		foreach ($new as $m_key => $m) {
			foreach ($ar as $s) if (isset($m[$s])) $m[$s] = trim($GLOBALS['APPLICATION']->ConvertCharset($m[$s], 'utf-8', LANG_CHARSET));
			if (isset($m['VAT_ID'])) $m['VAT_ID'] = intval($m['VAT_ID']);
			foreach ($m['profile'] as $k => $v) foreach ($ar as $s) if (isset($v[$s])) $m['profile'][$k][$s] = trim($GLOBALS['APPLICATION']->ConvertCharset($v[$s], 'utf-8', LANG_CHARSET));
			foreach ($m['config'] as $k => $v) {
				if ($k == 'host' && $v != '') {
					$v = trim(strtolower($v));
					if (substr($v, 0, 7) == 'http://') $v = substr($v, 7);
					if (substr($v, 0, 4) == 'www.') $v = substr($v, 4);
				}
				$m['config'][$k] = str_replace(';', '', $v);
			}
			$new[$m_key] = $m;
		}

		$update = array();
		$profile_new = array();
		foreach ($new as $n_key => $n) {
			if (!isset($module[$n_key])) continue;

			$m = $module[$n_key];
			$modules = (!isset($n['NAME']) ? true : false); // страница со всеми модулями (сохраняется только активность + создаются новые профили)

			$ar = array('PROFILES', 'edost', 'site_string', 'site_list');
			foreach ($ar as $v) if (isset($m[$v])) unset($m[$v]);
			unset($m['CONFIG']['CONFIG']);

			$ar = array('ACTIVE', 'NAME', 'DESCRIPTION', 'SORT');
			if (isset($n['VAT_ID'])) $ar[] = 'VAT_ID';
			foreach ($ar as $v) $m[$v] = $n[$v];

			if (!empty($n['config'])) $m['CONFIG']['all'] = $n['config'];

			$service = Bitrix\Sale\Delivery\Services\Manager::createObject($m);
			if (!$service) {
				$r['error'] = $admin_sign['warning']['create'];
				continue;
			}

			$m = $service->prepareFieldsForSaving($m);

			$id = false;
			if (!empty($m['ID'])) {
				// обновление
				$id = $m['ID'];
				if ($modules) $m = array('ACTIVE' => $m['ACTIVE']); else unset($m['ID']);
				\Bitrix\Sale\Delivery\Services\Manager::update($id, $m);
			}
			else if (!$modules && $m['ACTIVE'] == 'Y') {
				// добавление в базу
				$service = Bitrix\Sale\Delivery\Services\Manager::add($m);
				if ($service->isSuccess()) $id = $service->getId();
			}
			if ($id === false) continue;

			if (!$modules) $r['id'] = $id;
			if ($modules && $m['ACTIVE'] == 'Y' && isset($module[$n_key]['edost']['error'])) { $r['reload'] = 'Y'; $r['id'] = ''; }

			if ($modules) {
				$p = $module[$n_key]['PROFILES'];
				$module_original[$n_key]['ACTIVE'] = $n['ACTIVE'];
			}
			else {
				$p = $n['profile'];
				$module_original[$id]['ACTIVE'] = $n['ACTIVE'];
				$module_original[$id]['CONFIG_NEW'] = $n['config'];
				$module_original[$id]['PROFILES'] = array();

				// ограничения по сайтам
				$site_new = (!empty($n['site']) ? $n['site'] : false);
				$restriction = (!empty($restriction_site[$id]) ? $restriction_site[$id] : false);
				if (!($restriction === false && ($site_new === false || isset($site_new['all']) && $site_new['all'] == 'Y'))) {
					$site = array();
					if ($site_new !== false) foreach ($site_new as $k2 => $v2) if ($k2 !== 'all' && $v2 == 'Y') $site[] = $k2;
					$ar = \Bitrix\Sale\Delivery\Services\Manager::getRestrictionObject('\Bitrix\Sale\Delivery\Restrictions\BySite');
					$fields = array('SERVICE_ID' => $id, 'SERVICE_TYPE' => 0, 'SORT' => 100, 'PARAMS' => array('SITE_ID' => $site));
					$restriction_site[$id]['PARAMS']['SITE_ID'] = $site;
					$ar->save($fields, $restriction === false ? 0 : $restriction['ID']);
				}
			}

			// обновление профилей
			if (!empty($p)) foreach ($p as $k => $v) {
				$v2 = explode('_', $k);
				$edost = $v2[0];
				$bitrix = (isset($v2[1]) ? $v2[1] : 0);

				$new_profile = (empty($bitrix) && $m['ACTIVE'] == 'Y' ? true : false);
				if ($modules && !$new_profile) continue;

				if ($new_profile || $n['reload_ico'] == 'Y') {
					$img = '';
					if ($modules && !empty($module[$n_key]['CONFIG']['CONFIG']['template_ico']) && $module[$n_key]['CONFIG']['CONFIG']['template_ico']['VALUE'] != 'C' || !$modules && $n['config']['template_ico'] != 'C') $img = $ico_path.'/big/'.ceil($edost/2).'.gif';
					else if (isset($module[$n_key]['PROFILES'][$k]['company_ico'])) $img = $ico_path.'/company/bitrix/'.$module[$n_key]['PROFILES'][$k]['company_ico'].'.gif';

					if (\Bitrix\Main\IO\File::isFileExists($_SERVER['DOCUMENT_ROOT'].$img)) {
						$v['LOGOTIP'] = CFile::MakeFileArray($img);
						$v['LOGOTIP']['MODULE_ID'] = 'sale';
						// 'del' => 'флажок, удалить ли существующий файл (непустая строка)'
						CFile::SaveForDB($v, 'LOGOTIP', 'sale/delivery/logotip');
					}
				}

				if ($new_profile) {
					// добавление новых профилей
					$s = array(
						'CODE' => 'edost:'.$edost,
						'ACTIVE' => $m['ACTIVE'],
						'PARENT_ID' => $id,
						'NAME' => $v['NAME'],
						'DESCRIPTION' => $v['DESCRIPTION'],
						'SORT' => 100,
						'LOGOTIP' => (!empty($v['LOGOTIP']) ? $v['LOGOTIP'] : 0),
						'CLASS_NAME' => '\Bitrix\Sale\Delivery\Services\AutomaticProfile',
					);
					if (isset($v['VAT_ID'])) $s['VAT_ID'] = $v['VAT_ID'];

					$s = $s + $m;
					$s['CONFIG']['MAIN'] = array('PROFILE_ID' => (empty($edost) ? '0' : intval($edost)));

					$service = \Bitrix\Sale\Delivery\Services\Manager::add($s);
					if ($service->isSuccess()) {
						$bitrix = $service->getId();
						$profile_new[$id2] = $bitrix;
					}
				}
				else {
					// обновление
					$ar = array('ACTIVE' => $m['ACTIVE'], 'NAME' => $v['NAME'], 'DESCRIPTION' => $v['DESCRIPTION']);
					if (!empty($v['LOGOTIP'])) $ar['LOGOTIP'] = $v['LOGOTIP'];

					if (isset($m['VAT_ID']) && $n['reload_vat'] == 'Y') $ar['VAT_ID'] = $m['VAT_ID'];
					else if (isset($v['VAT_ID'])) $ar['VAT_ID'] = $v['VAT_ID'];

					\Bitrix\Sale\Delivery\Services\Table::update($bitrix, $ar);
				}

				if (!$modules) $module_original[$id]['PROFILES'][$edost.'_'.$bitrix] = array('ID' => $bitrix, 'CODE' => 'edost:'.$edost);

				if (isset($module[$n_key]['PROFILES'][$k])) $module[$n_key]['PROFILES'][$k]['updated'] = true;
			}

			// изменение активнсти остальных профилей
			if (!empty($module[$n_key]['PROFILES'])) foreach ($module[$n_key]['PROFILES'] as $k => $v) if (empty($v['updated']) && !empty($v['ID'])) {
				\Bitrix\Sale\Delivery\Services\Table::update($v['ID'], array('ACTIVE' => !$modules || !isset($v['company_id']) ? 'N' : $m['ACTIVE']));
			}
		}


		// сохранение настроек в 'option'
		if (!isset($r['error'])) {
			$option = array();
			$company_tariff = edost_class::GetCompanyTariff();
			foreach ($module_original as $m_key => $m) if ($m['ACTIVE'] == 'Y') {
				$c = '';
				if (!empty($m['CONFIG_NEW'])) {
					$c = array();
					foreach (CDeliveryEDOST::$setting_key as $k => $v) $c[] = (isset($m['CONFIG_NEW'][$k]) ? $m['CONFIG_NEW'][$k] : '');
					$c = implode(';', $c);
				}
				else if (!empty($m['CONFIG']['MAIN']['OLD_SETTINGS'])) $c = unserialize($m['CONFIG']['MAIN']['OLD_SETTINGS']);
				$m['config_string'] = $c;

				$c = CDeliveryEDOST::$setting_param_key;
				foreach ($m['PROFILES'] as $v) if ($v['CODE'] == 'edost:0') $c['zero_tariff'] = $v['ID'];
				$c['module_id'] = $m_key;
				$m['param_string'] = implode(';', $c);

				if (isset($module[$m_key])) foreach ($module[$m_key]['PROFILES'] as $v) if (isset($v['company_id'])) $company_tariff[$v['company_id']][$v['tariff_id']] = '';

				if (empty($restriction_site[$m_key]['PARAMS']['SITE_ID'])) $option['all'] = $m;
				else foreach ($restriction_site[$m_key]['PARAMS']['SITE_ID'] as $s) $option[$s] = $m;
			}
			$s = array();
			foreach ($option as $k => $v) if ($v['ACTIVE'] == 'Y') $s[$k] = $v['config_string'].';param='.$v['param_string'];
			\Bitrix\Main\Config\Option::set('edost.delivery', 'module_setting', serialize($s));
			edost_class::GetCompanyTariff($company_tariff);
//			echo '<br><b>option:</b><pre style="font-size: 12px">'.print_r($s, true).'</pre>';
		}

		echo edost_class::GetJson($r, false, false, false);

		die();
	}

}




if ($mode == 'list' || $mode == 'register') {
	// выпадающие списки и подписи с днями
	$day_delay = ($param['control'] == 'delay' ? 1 : $cookie['control_day_delay']);
	$day_office = ($param['control'] == 'office' ? 1 : $cookie['control_day_office']);
	$day = array();
	$ar = array(
		'control' => array('delay', 'office', 'complete'),
		'register' => array('insert')
	);
	$ar2 = array(2, 3, 4, 5, 6, 7, 8, 9, 10, 15, 20, 25, 30, 45, 60);
	foreach ($ar as $f_key => $f) foreach ($f as $v) {
		$s = '';
		foreach ($ar2 as $v2) $s .= '<option value="'.$v2.'"'.($v2 == $cookie[$f_key.'_day_'.$v] ? ' selected' : '').'>'.edost_class::GetDay($v2).'</option>';
		$day[$v]['select'] = '<select style="height: 20px; padding: 1px; margin-top: -3px;" onchange="edost_SetParam(\''.$f_key.'_day_'.$v.'\', this.value)">'.$s.'</select>';
	}
	$day['delay']['string'] = ($day_delay > 1 ? ' '.$control_sign['from'].' '.edost_class::GetDay($day_delay).' '.$control_sign['more'] : '');
	$day['office']['string'] = ($day_office > 1 ? ' '.edost_class::GetDay($day_office).' '.$control_sign['more'] : '');
	$day['complete']['string'] = ($param['control'] == 'complete_paid2' && $cookie['control_day_complete'] > 1 ? ' '.edost_class::GetDay($cookie['control_day_complete']).' '.$control_sign['more'] : '');
//	echo '<br><b>day:</b><pre style="font-size: 12px">'.print_r($day, true).'</pre>';

	// блок с настройками (открыт / скрыт)
	$setting_show = ($cookie[$mode == 'list' ? 'control_setting' : 'register_setting'] == 'Y' ? true : false);
}




if ($mode == 'register' || $mode == 'list' || $mode == 'print') {
	if (isset($_REQUEST['clear_cache']) && $_REQUEST['clear_cache'] == 'Y') edost_class::Control('clear_cache_flag');
	$control = edost_class::Control('clear_cache');
	if ($mode == 'list' && !empty($control['data'])) foreach ($control['data'] as $k => $v) if (!empty($v['register'])) unset($control['data'][$k]); // удаление заказов на оформлении доставки со страницы контроля
//	echo '<br><b>control:</b><pre style="font-size: 12px">'.print_r($control, true).'</pre>';

	if (!empty($control['control'])) {
		$count = 0;
		$count_string = '';
		$buy = array();
		$n = count($control['control']);
		foreach ($control['control'] as $v) {
			$count += $v['count'];
			$count_string .= ($n != 1 ? $v['site'].': ' : '').'<a class="edost_link2" href="'.$edost_path.$v['id'].'" target="_blank">'.$v['count'].'</a><br>';
			$buy[] = '<a class="edost_link2" href="'.$edost_path.$v['id'].'" target="_blank">'.($n == 1 ? $control_sign['control_buy'] : $v['site']).'</a>';
		}
		if ($count_string != '') $count_string = $control_sign['control_count'].($n != 1 ? '<br>' : ': ').$count_string;
	}

	if (!empty($control['error'])) {
		echo '<div style="text-align: center; font-size: 16px; color: #A00;">'.CDeliveryEDOST::GetEdostError($control['error'], 'control').'</div>';
		return;
	}
}




// оформление доставки (в админке в меню 'eDost')
if ($mode == 'register' || $mode == 'print') {
	$shipment_id = false;
	$register_set = $print_doc = false;
	$register_search = (in_array($param['control'], array('search_order', 'search_shipment')) ? true : false);
	if (empty($param['control'])) $param['control'] = '';
	$count = $control['count_register'];
	$param2 = '';
	$company = (!empty($param['company']) ? $param['company'] : '');

	$history_batch = false; // вывод всех заказов входящих в сдачу
	$print_batch = false; // печать акта передачи
	$print_package = false; // печать этикеток
	$history_mode = '';
	$doc_data = $control_sign['doc'];
	$shop_field = CDeliveryEDOST::GetPrintField('shop');

	// статусы заказов и отгрузок
	$status = array();
	$ar = \Bitrix\Sale\Internals\StatusTable::getList(array(
		'select' => array('ID', 'NAME' => 'Bitrix\Sale\Internals\StatusLangTable:STATUS.NAME', 'TYPE'),
		'filter' => array('=Bitrix\Sale\Internals\StatusLangTable:STATUS.LID' => LANGUAGE_ID),
		'order'  => array('SORT'),
	));
	while ($v = $ar->fetch()) $status[$v['ID']] = array('name' => $v['NAME'], 'type' => $v['TYPE']);
//	echo '<br><b>status:</b><pre style="font-size: 12px">'.print_r($status, true).'</pre>';

	// загрузка из истории
    if ($param['control'] == 'history') {
    	$v = false;
		if (isset($param['history_id']) && isset($history['data'][$param['history_id']])) $v = $history['data'][$param['history_id']];
		else if (!empty($history['data'])) {
			end($history['data']);
			$v = current($history['data']);
		}
		$param['set'] = (!empty($v['set']) ? $v['set'] : '');
		if (!empty($v['set'])) {
			$history_mode = $v['mode'];
			if (in_array($v['mode'], array('office', 'date'))) $history_batch = true;
		}
	}

	if (isset($param['doc'])) {
		// страница печати: type=print & mode=normal & doc=95_e1v|104_107|104_112ep|108_107

		$print_doc = array();
		$param['doc'] = preg_replace("/[^0-9a-z|_]/i", "", $param['doc']);
		$s = explode('|', $param['doc']);
		foreach ($s as $v) {
			$v = explode('_', $v);
			if (!empty($v[0]) && !empty($v[1])) $print_doc[$v[0]][] = $v[1];
			if ($v[1] == 'batch') $print_batch = $v[0];
			if ($v[1] == 'package') $print_package = $v[0];
		}

		// подключение всех заказов входящих в сдачу (для печати акта передачи)
		if ($print_batch !== false && !empty($control['data'][$print_batch]['batch_code'])) {
			$v2 = $control['data'][$print_batch];
			foreach ($control['data'] as $k => $v) if ($k != $v2['id'] && !empty($v['batch_code']) && $v['batch_code'] == $v2['batch_code']) $print_doc[$k] = array('batch');
		}

		$shipment_id = array_keys($print_doc);

//		echo '<br><b>print_doc:</b><pre style="font-size: 12px">'.print_r($print_doc, true).'</pre>';
//		echo '<br><b>shipment_id:</b><pre style="font-size: 12px">'.print_r($shipment_id, true).'</pre>';
	}
	else if (isset($param['set'])) {
		// кнопки + загрузка из истории: set=110_doc_7p|110_doc_107|110_doc_112ep  |  110_register  |  110_package_0_weight_10 | 110_package_0_size_0_1|110_package_0_size_1_2|110_package_0_size_2_3

		$register_set = $shipment_id = array();
		$s = explode('|', $param['set']);
		foreach ($s as $v) {
			$v = explode('_', $v);
			if (count($v) > 3) {
				$v = array_reverse($v);
				foreach ($v as $k2 => $v2) if ($k2 == 0) $v3 = $v2; else $v3 = array($v2 => $v3);
				edost_class::array_merge_recursive2($register_set, $v3);
			}
			else if (!empty($v[0]) && !empty($v[1]))
				if (isset($v[2])) $register_set[$v[0]][$v[1]][] = $v[2];
				else $register_set[$v[0]][$v[1]] = true;
		}

		// подключение всех заказов входящих в сдачу (для истории и обновления с типом 'регистрация в отделении')
		$a = false;
		if (!empty($param['button']) && $param['button'] == 'update') foreach ($register_set as $v) if (!empty($v['office']) || !empty($v['date'])) $a = true;
		if ($history_batch || $a) {
			$ar = array();
			foreach ($register_set as $k => $v) if (!empty($control['data'][$k]['batch_code']))
				foreach ($control['data'] as $k2 => $v2) if ($k2 != $k && !empty($v2['batch_code']) && $v2['batch_code'] == $control['data'][$k]['batch_code']) $ar[$k2] = $v;
			if (!empty($ar)) $register_set += $ar;
		}

		foreach ($register_set as $k => $v) if ($k != 'manual')	$shipment_id[] = $k;
//		echo '<br><b>register_set:</b><pre style="font-size: 12px">'.print_r($register_set, true).'</pre>';
	}
	else if ($register_search) {
		// поиск

		$shipment_id = array();
		$s = str_replace(array(',', ';', '.'), ' ', $param['search']);
		$s = explode(' ', $s);
		foreach ($s as $v) {
			$v = intval(trim($v));
			if ($v != 0) $shipment_id[] = $v;
		}
	}
	else {
		// главная страница оформления

		if (!empty($param['control'])) {
			$s = explode('_', $param['control']);
			if (!empty($s[1]) && in_array($s[0], array('company', 'shipment'))) {
				$param['control'] = $s[0];
				$param2 = $s[1];
			}
		}
		if (empty($param['control'])) $param['control'] = 'main';
	}

	// поиск новых заказов готовых к оформлению
	if ($print_doc === false && ($register_set === false || $param['control'] == 'history')) {
		$data = edost_class::GetShipmentData(0, array('day' => $cookie['register_day_insert']));
		foreach ($data as $k => $v) {
			$a = false;
			if (!isset($register_tariff[$v['tariff']])) $a = true;
			else {
				$s = CDeliveryEDOST::GetEdostConfig($v['site_id'], $config, true);
				if ($s['register_status'] != '' && $v['order_status'] == $s['register_status']) $a = true;
			}
			if ($a) unset($data[$k]);
		}
	}
//	echo '<br><b>data:</b><pre style="font-size: 12px">'.print_r($data, true).'</pre>';

	// расчет количества новых заказов + удаление заказов на контроле + добавление заказов на оформлении
	$ar = array();
	$n = (!empty($data) ? count($data) : 0);
	if (!empty($control['data'])) foreach ($control['data'] as $v) {
		if (isset($data[$v['id']])) {
			$n--;
			if (empty($v['register'])) unset($data[$v['id']]);
		}
		else if (!empty($v['register'])) $ar[] = $v['id'];
	}
	$count['register_new'] = $n;
	$ar = edost_class::GetShipmentData($ar);
	if (!empty($ar)) {
		if (empty($data)) $data = array();
		$data += $ar;
	}
//	echo '<br><b>data:</b><pre style="font-size: 12px">'.print_r($data, true).'</pre>';
//	echo '<br><b>control:</b><pre style="font-size: 12px">'.print_r($control, true).'</pre>';

	// сдачи
	$date = edost_class::time(date('d.m.Y'));
	$batch = array(); // все сдачи
	$batch_add = array(); // сдачи в которые можно включить новые заказы
	foreach ($control['data'] as $k => $v) if (!empty($v['batch_code']) && !empty($v['complete'])) {
		$k2 = $v['batch_code'];
		$ar = $v['batch'] + array('tariff' => $v['tariff'], 'register' => $v['register'], 'order' => array(), 'order_add' => array(), 'oversize' => (($v['batch']['type'] & 32) >> 5));
		if (empty($batch[$k2])) $batch[$k2] = $ar;
		$batch[$k2]['order'][] = $k;
		if (edost_class::time($v['batch']['date']) >= $date) {
			if (empty($batch_add[$k2])) $batch_add[$k2] = $ar;
			$batch_add[$k2]['order'][] = $k;
		}
	}
//	echo '<br><b>batch:</b><pre style="font-size: 12px; text-align: left;">'.print_r($batch, true).'</pre>';
//	echo '<br><b>batch_add:</b><pre style="font-size: 12px; text-align: left;">'.print_r($batch_add, true).'</pre>';

	if ($shipment_id !== false) $company = false;

	// список с количеством заказов на оформлении и иконками компаний
	$control_head = edost_class::ControlHead($data, array('type' => 'register', 'control' => !empty($control['data']) ? $control['data'] : array(), 'active' => $param['control'], 'company' => $company, 'path' => $ico_path));
	$company = $control_head['company'];

	// загрузка заказов по id
	if ($shipment_id !== false) {
		$ar = array();
		if ($param['control'] == 'search_order') $ar['order'] = true;
		$data = edost_class::GetShipmentData($shipment_id, $ar);
		foreach ($data as $k => $v)
			if (!isset($register_tariff[$v['tariff']])) unset($data[$k]);
			else if ($company == '') $company = $v['company_id'];
	}
//	echo '<br><b>data:</b><pre style="font-size: 12px">'.print_r($data, true).'</pre>';

	if (empty($data)) $data = array();

	// фильтр по типу вывода
	if ($print_doc === false && $register_set === false) foreach ($data as $k => $v) {
		$o = (isset($control['data'][$v['id']]) ? $control['data'][$v['id']] : false);
		if ($param['control'] == 'total' && !$o ||
			$param['control'] == 'transfer' && !($o && $o['status_warning'] != 2 && in_array($o['register'], array(2, 9, 10))) ||
			$param['control'] == 'delete' && !($o && in_array($o['register'], array(6, 7, 8))) ||
			$param['control'] == 'register_new' && $o ||
			$v['company_id'] != $company ||
			$param['control'] == 'status_20' && !($o && $o['status'] == 20) ||
			$param['control'] == 'main' && !(!$o || $o['register'] == 2 && $o['status_warning'] == 2))
			unset($data[$k]);
	}

	edost_class::AddRegisterData($data);
//	echo '<br><b>data:</b><pre style="font-size: 12px">'.print_r($data, true).'</pre>';
//	echo '<br><b>data:</b><pre style="font-size: 12px">'.print_r($control, true).'</pre>';

	$props_field = array('name', 'phone', 'location_name', 'zip', 'address_formatted');
	foreach ($data as $k => $v) {
		$cod = (!empty($v['cod']) ? $v['payment'][$v['cod']] : false);
		if (!empty($v['part'])) $cod = false; // отключение наложенного платежа, если в заказе несколько отгрузок

		$insurance = (!empty($v['insurance']) ? true : false);
		if ($cod) $insurance = true;

		$v['order_code'] = $v['order_id'].(!empty($v['part']) ? '/'.$v['id'] : '');

		if (!empty($control['data'][$k])) $v += $control['data'][$k];
		else {
			$v['register'] = 0;
			$v['batch_code'] = '';
			$v['batch'] = '';
		}

		if (!in_array($v['tariff'], CDeliveryEDOST::$register_no_required)) $v['register_required'] = true;

		// проверка на возможность включения в сдачу
		if (empty($v['batch_code'])) {
			if ($v['cod']) $type = 5;
			else if ($v['insurance']) $type = 4;
			else $type = 2;

			$type = ($v['tariff'] << 8) | $type; // $batch_type = ($tariff << 8) | (негабарит << 5) | (осторожно << 4) | (курьер << 3) | $type;

			foreach ($batch_add as $k2 => $v2) if (($v2['type'] & 65503) == $type) {
				$batch_add[$k2]['order_add'][] = $k;
				$batch_add[$k2]['company'] = $v['company'];
			}
		}

		$v['control_active'] = (empty($v['register']) && !empty($control['data'][$k]) ? true : false);
//		$v['deducted_active'] = $v['deducted'];
		$v['register_active'] = (!empty($v['register']) || $v['control_active'] ? true : false);
		$v['batch_active'] = (!empty($v['batch_code']) ? true : false);

		$set = (!empty($register_set[$k]) ? $register_set[$k] : false);
//		$v['deducted_on'] = ($set !== false && empty($set['deducted']) ? false : true);
		$v['register_on'] = true;
		$v['batch_on'] = ($cookie['register_no_batch'] == 'Y' && $v['register'] == 0 ? false : true);

		if (!empty($v['batch']['date'])) {
			$s = explode('.', $v['batch']['date']);
			$v['batch_date_formatted'] = $control_sign['week'][date('N', edost_class::time($v['batch']['date']))].'<span style="font-weight: normal;">, '.intval($s[0]).' '.$control_sign['month'][$s[1]-1].'</span>';
		}

		// возможность редактирования параметров отправления (вес, габариты и опции)
		if (!$v['control_active'] && empty($v['complete']) && ($v['register'] == 0 || $v['status_warning'] == 2 && !in_array($v['register'], array(6, 7, 8, 9, 10))) && empty($v['field_error']) && !empty($v['props']['package'])) $v['package_active'] = true;

		$v['order_date_formatted'] = '<b>'.$v['order_date']->format("d.m.Y").'</b> <span class="low2">'.$v['order_date']->format("H:i").'</span>';
		$v['order_status_formatted'] = edost_class::limit_string($status[$v['order_status']]['name'], 22);
		$v['delivery_price_formatted'] = edost_class::GetPrice('formatted', $v['delivery_price'], $v['delivery_currency'], '', false);

		$ar = array();
		if ($v['order_price'] - $v['order_sum_paid'] > 0) $ar[] = edost_class::GetPrice('formatted', $v['order_price'] - $v['order_sum_paid'], $v['order_currency'], '', false);
		if ($v['order_sum_paid'] != 0) $ar[] = '<span style="color: #0A0">'.edost_class::GetPrice('formatted', $v['order_sum_paid'], $v['order_currency'], '', false).'</span>';
		$v['order_price_formatted'] = implode('<br>', $ar);

		$v['checkbox'] = array('register' => 'register');
		if (in_array($v['company_id'], array(23))) $v['checkbox']['batch'] = 'batch';

		if (!empty($print_doc[$k])) $v['doc'] = $print_doc[$k];

		$s = array();
		foreach ($props_field as $v2) $s[] = $v2.'='.(isset($v['props'][$v2]) ? str_replace(array('"', ';', '='), '', $v['props'][$v2]) : '');
		$v['props_param'] = implode(';', $s);

		// список с товарами (для таблицы)
		if ($cookie['register_item'] == 'Y') {
			$string_length = 22;
			$hint = false;
			$basket = $basket_hint = array();
			$i = 0;
			foreach ($v['basket'] as $k2 => $v2) {
				$i++;

				$s1 = $s2 = '';
				if (!empty($v2['set'])) foreach ($v2['set'] as $v3) {
					$ar = edost_class::limit_string($v3['NAME'], $string_length, true);
					$s = '<br>&nbsp;&nbsp;&nbsp; - ';
					$s1 .= $s . $ar[0];
					$s2 .= $s . $ar[1];
				}

				$ar = edost_class::limit_string($v2['NAME'], $string_length, true);
				$s = '<span style="color: #058;">'.$i.'</span>. %name%'.($v2['QUANTITY'] > 1 ? ' (<b>'.intval($v2['QUANTITY']).$control_sign['quantity'].'</b>)' : '').' - <span style="color: #058;">'.$v2['price_total_formatted'].'</span>';
				$basket_hint[$i] = str_replace('%name%', $ar[0], $s) . $s1;
				$basket[$i] = str_replace('%name%', $ar[1], $s) . $s2;
				if ($basket_hint[$i] != $basket[$i]) $hint = true;
			}

			$max = 5;
			$n = count($v['basket']);
			if ($hint || $n > $max) $v['basket_hint'] = implode('<br>', $basket_hint);
			$s = ($n > $max ? '<br>... '.$control_sign['total2'].' '.edost_class::draw_string('item2', $n) : '');
			if ($n > $max) array_splice($basket, $max-1);
			$v['basket_formatted'] = implode('<br>', $basket).$s;
		}

		$data[$k] = $v;
	}

	edost_class::AddControlCount($data, $control);

	// определение общих сдач на одно число
	$ar = array();
	foreach ($batch_add as $k => $v)
		if (empty($v['order_add'])) unset($batch_add[$k]);
		else {
			$key = $v['date'].'_auto';
			if (isset($ar[$key])) {
				$ar[$key]['auto'] = true;
				$ar[$key]['order_add'] = array_merge($ar[$key]['order_add'], $v['order_add']);
			}
			else $ar[$key] = $v;
		}
	foreach ($ar as $k => $v) if (empty($v['auto'])) unset($ar[$k]);
	if (!empty($ar)) $batch_add = array_merge($ar, $batch_add);

//	echo '<br><b>control:</b><pre style="font-size: 12px">'.print_r($control, true).'</pre>';
//	echo '<br><b>data:</b><pre style="font-size: 12px">'.print_r($data, true).'</pre>';
//	echo '<br><b>batch_data:</b><pre style="font-size: 12px; text-align: left;">'.print_r($batch_add, true).'</pre>';
}




// обработка кнопок на странице оформления доставки
if ($mode == 'register' && isset($param['set']) && !isset($param['history_id'])) {
//	echo '<br><b>register_set:</b><pre style="font-size: 12px">'.print_r($register_set, true).'</pre>';
//	echo '<br><b>data:</b><pre style="font-size: 12px">'.print_r($data, true).'</pre>';
//	echo '<br><b>param:</b><pre style="font-size: 12px">'.print_r($param, true).'</pre>';

	if (empty($data)) die();

	$error = $register = array();

	// проверка заказов после отправки запроса на сервер
	$register_full = false;
	if ($param['button'] == 'update') {
		$ar = array();
		foreach ($register_set as $k => $v) if ((!empty($v['office']) || !empty($v['date']) || !empty($v['register']) || !empty($v['batch'])) && isset($data[$k])) $ar[$k] = $data[$k];
		$control2 = edost_class::Control($ar, array('get' => true));
//		echo '<br><b>get:</b><pre style="font-size: 12px">'.print_r($ar, true).'</pre>';

		$n = 0;
		foreach ($ar as $k => $v) if (isset($control2['data'][$k])) {
			$o = $control2['data'][$k];
			if (in_array($o['register'], array(4, 5)) || $o['register'] == 2 && $o['status_warning'] == 2) $n++;
		}
		if ($n == count($ar)) $register_full = true;
	}

	if ($param['button'] != 'update') foreach ($data as $k => $v) if (!empty($register_set[$k])) {
		$set = $register_set[$k];
		$config = CDeliveryEDOST::GetEdostConfig($v['site_id']);

//		echo '<br><b>set:</b> <pre style="font-size: 12px">'.print_r($set, true).'</pre>';
//		echo '<br><b>set:</b> <pre style="font-size: 12px">'.print_r($v, true).'</pre>';

		// параметры упаковки
		if (!empty($set['package'][0])) {
			$p = $set['package'][0];
			if (isset($p['weight'])) $v['props']['package'][0]['weight'] = $p['weight'];
			if (isset($p['size'])) $v['props']['package'][0]['size'] = $p['size'];
		}

		// установка статуса заказа при печате без оформления
		if ($param['button'] == 'print_no_register' && $config['register_status'] != '' && $v['order_status'] != $config['register_status']) {
			$order = \Bitrix\Sale\Order::load($v['order_id']);
			$order->setField('STATUS_ID', $config['register_status']);
			$order->save();
		}

		if ($param['button'] == 'print_no_register') continue;

		// оформление доставки
		if ($param['button'] != 'register_repeat' && !empty($set['batch']))
			if (empty($param['batch'])) $v['batch'] = array('date' => $param['batch_date'], 'number' => '');
			else if ($param['batch'] === 'new') $v['batch'] = array('date' => $param['batch_date'], 'number' => '');
			else {
				$s = explode('_', $param['batch']);
				$v['batch'] = array('date' => $s[0], 'number' => !empty($s[1]) ? $s[1] : '');
			}
		if ($param['button'] != 'register_repeat' && !empty($param['call'])) {
			$v['batch'] = array(
				'date' => $param['batch_date'],
				'number' => '',
				'call' => ($param['call'] == 'Y' ? '1' : '0'),
				'profile_shop' => intval($param['profile_shop']),
				'profile_delivery' => intval($param['profile_delivery']),
			);
		}

		$a = (!empty($set['register']) && (empty($control['data'][$k]) || $control['data'][$k]['status_warning'] == 2) ? true : false); // оформление новой доставки или исправление ошибки

		if (!empty($set['office']) || !empty($set['date'])) $register[] = $k;
		else if ($a || !empty($set['batch']) && !empty($control['data'][$k])) {
			$register[$k] = $v;
			if ($a) edost_class::SavePackage($v['order_id'], $v['id'], $v['props']['package']);
		}
	}

	// отправка данных на сервер
	if (!empty($register)) {
//		echo '<br><b>register:</b><pre style="font-size: 12px">'.print_r($register, true).'</pre>';

		$ar = array('data' => $control);
		if ($param['button'] == 'office') $ar['flag'] = 'batch_office';
		if ($param['button'] == 'date') {
			$ar['flag'] = 'batch_date';
			$ar['date'] = (isset($_POST['date']) ? preg_replace("/[^0-9.]/i", "", substr($_POST['date'], 0, 20)) : '');
		}

		$s = edost_class::Control($register, $ar);

		$e = GetMessage('EDOST_DELIVERY_ERROR');
		if (isset($s['add_error'])) $error[] = $control_sign['error']['register_head'].(isset($e[$s['add_error']]) ? $e[$s['add_error']] : $control_sign['error']['code'].' '.$s['add_error']).'!';
	}

	// результат в json
	$r = array('"error": "'.implode('<br>', $error).'"');
	if ($param['button'] != 'update') {
		// сохранение в историю
		$code = array();
		foreach ($data as $k => $v) $code[] = (in_array($param['button'], array('office', 'date')) ? $v['batch']['number'] : $v['order_code']);
		if (!empty($code)) {
			$history = edost_class::History($history, array('mode' => $param['button'], 'code_data' => $code, 'time' => $param['time'], 'set' => $param['set']));
			$r[] = '"history": '.json_encode($GLOBALS['APPLICATION']->ConvertCharset($history['select'], LANG_CHARSET, 'utf-8'));
		}
	}
	else {
		if ($register_full) $r[] = '"register_full": "1"';
	}
	if (!empty($param['doc'])) $r[] = '"doc": "'.implode('|', $param['doc']).'"';
	echo '{'.implode(', ', $r).'}';

	die();
}




// настройки (в админке в меню 'eDost')
if ($mode == 'setting') {
	$show_bar = true;

	if ($param['paysystem']) {
		// привязка к оплате

		if (!$module_active || $module_error) {
			$show_bar = false;
			echo '<div style="text-align: center;">'.str_replace('%href%', '/bitrix/admin/edost.php?lang='.LANGUAGE_ID.'&type=setting', $admin_sign['module_no'.($module_error ? 2 : '')]).'</div>';
		}
		else if (empty($paysystem)) {
			$show_bar = false;
			echo '<div style="text-align: center;">'.str_replace('%href%', '/bitrix/admin/sale_pay_system.php?lang='.LANGUAGE_ID, $admin_sign['paysystem_no']).'</div>';
		} else {
			foreach ($module as $m_key => $m) if ($m['ACTIVE'] == 'Y')
				foreach ($m['PROFILES'] as $p) if (isset($p['company_id']) && $p['company_id'] == 0)
					echo '<input name="zero_tariff['.$m_key.']" type="hidden" value="'.$p['ID'].'">';

			// наложенный платеж eDost
			foreach ($paysystem as $k => $v) if (isset($v['cod'])) { ?>
			<input name="paysystem[<?=$k?>][cod]" type="hidden" value="<?=implode(',', $v['delivery'])?>">
			<div style="margin-bottom: 15px; border: 1px solid #CCC; padding: 5px; text-align: center;">
				<table width="100%" border="0" cellpadding="4" cellspacing="0"><tr>
					<td width="220">
						<div style="font-size: 22px; color: #888; line-height: 20px;"><?=$admin_sign['paysystem_cod']['name']?></div>
						<div style="font-size: 18px; color: #4A4;"><?=$admin_sign['paysystem_cod']['active']?></div>
					</td>
					<td width="400" style="color: #888; text-align: left;">
						<div style="color: #555; font-size: 16px;"><?=$v['name'].' <span style="font-size: 14px; opacity: 0.5; cursor: pointer;" onclick="window.open(\'/bitrix/admin/sale_pay_system_edit.php?ID='.$k.'&lang='.LANGUAGE_ID.'\', \'_blank\')">['.$k.']</span>'?></div>
						<div style="height: 5px;"></div> <?=$admin_sign['paysystem_cod']['info']?>
					</td>
				</tr></table>
			</div>
<?			} ?>
<?
			// непривязанные профили
			if (!empty($no_paysystem)) {
				$n = count($no_paysystem);
				if ($n < 10) $n = 0;
				else {
					$n -= 5;
					$no_paysystem2 = array_slice($no_paysystem, 5);
					$no_paysystem = array_slice($no_paysystem, 0, 5);
				} ?>
			<div id="paysystem_error" style="margin-bottom: 15px; padding: 10px; text-align: center;">
				<div style="font-size: 20px; color: #F00; padding-bottom: 5px;"><?=$admin_sign['paysystem_tariff_error']?></div>
				<?=implode('<br>', $no_paysystem)?>
<?				if ($n > 0) {
					echo '<div id="paysystem_error_tariff" style="display: none;">'.implode('<br>', $no_paysystem2).'</div>';
					echo '<span class="edost_link" style="display: block; width: 80px; padding-top: 5px; margin: 0 auto; color: #AAA;" onclick="this.style.display = \'none\'; BX(\'paysystem_error_tariff\').style.display = \'block\'">'.$admin_sign['button']['show2'].'</span>';
				} ?>
			</div>
<?			}

			/* нулевой тариф */ ?>
			<div style="margin-bottom: 15px; background: #e1ecf5; padding: 10px; text-align: center;">
				<span style="font-size: 16px;"><?=$admin_sign['zero_tariff_paysystem_head']?></span>
				<select name="zero_tariff_paysystem" data-start="<?=(!empty($zero_tariff_paysystem) ? $zero_tariff_paysystem : 0)?>" style="font-size: 16px; vertical-align: baseline; margin-left: 2px;">
					<option value="0"><?=$admin_sign['zero_tariff_paysystem_none']?></option>
<?					foreach ($paysystem as $k => $v) if (!isset($v['cod'])) { ?>
					<option value="<?=$k?>" <?=($k == $zero_tariff_paysystem ? 'selected=""' : '')?>><?=$v['name'].' ['.$k.']'?></option>
<?					} ?>
				</select>
				<div style="font-size: 13px; color: #888; padding-top: 2px;"><?=$admin_sign['zero_tariff_paysystem_hint']?></div>
			</div>

<?			$i = 0;
			foreach ($paysystem as $k => $v) if (!isset($v['cod'])) { $i++; ?>
			<input name="paysystem[<?=$k?>][save]" type="hidden" value="<?=implode(',', $v['save'])?>">
			<div id="paysystem_<?=$k?>" class="setting_paysystem <?=($v['radio'] == 'none' ? 'edost_module_off' : '')?>" style="margin-bottom: 15px; padding: 5px 5px 5px 10px; background: #F0F0F0;">
				<table width="100%" border="0" cellpadding="4" cellspacing="0"><tr>
					<td width="300" valign="top"><div class="head edost_module_light" style="margin-top: 0px;">
						<?=$v['name'].' <span style="font-size: 16px; opacity: 0.5; cursor: pointer;" onclick="window.open(\'/bitrix/admin/sale_pay_system_edit.php?ID='.$k.'&lang='.LANGUAGE_ID.'\', \'_blank\')">['.$k.']</span>'?>
                    	</div>
						<div class="checkbox" style="margin-top: 5px;">
							<input id="paysystem_<?=$k?>_active" name="paysystem[<?=$k?>][active]" style="margin: 0px;" type="checkbox" <?=($v['radio'] != 'none' ? 'checked=""' : '')?> onclick="edost_SetData('paysystem_active', this)">
							<label class="green" for="paysystem_<?=$k?>_active" style="font-size: 18px;"><?=$admin_sign['paysystem_active']?></label>
						</div>
					</td>
					<td valign="top"><div style="">
						<div class="checkbox" style="margin-top: 0px;">
							<span id="paysystem_<?=$k?>_list_span" style="<?=($v['radio'] == 'none' ? 'display: none;' : '')?>">
								<input id="paysystem_<?=$k?>_list" name="paysystem[<?=$k?>][list]" type="checkbox" <?=($v['radio'] == 'list' ? 'checked=""' : '')?> onclick="edost_SetData('paysystem_active', this)">
								<label for="paysystem_<?=$k?>_list" style="font-weight: bold;"><?=$admin_sign['paysystem_list']?></label>
							</span>
						</div>

						<div id="paysystem_tariff_<?=$k?>" class="edost_paysystem_tariff" style="margin-top: 10px; display: <?=($v['radio'] == 'list' ? 'block' : 'none')?>;">
<?							$module_i = 0;
							foreach ($module as $m_key => $m) if ($m['ACTIVE'] == 'Y') {
								if ($module_count > 1) {
									$active_head = $admin_sign['module_config']['active_head'].' <span style="font-size: 15px; color: #888;">['.$m_key.']</span>';
									echo '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-size: 16px; background: #DDD; padding: 5px 10px; margin: '.($module_i != 0 ? '20px' : '0').' 0 0px 0;"><tr>'.
										'<td style="vertical-align: middle;">'.$active_head.'</td>'.
										($module_site ? '<td align="right"><span class="edost_module_config_value">'.implode('<br>', $m['site_string']).'</span>' : '').'</div></td>'.
										'</td></table>';
								}
								echo '<div style="padding: 10px; background: #FFF;">';
								$module_i++;
								$company = array();
								$count = $count_active = 0;
								foreach ($m['PROFILES'] as $p) if (isset($p['company_id'])) {
									if (!empty($zero_tariff_paysystem) && $p['company_id'] == 0) continue;

									$count++;
									$c = $p['company_id'];
									if (!isset($company[$c])) $company[$c] = array('count' => 0, 'active' => 0, 'name' => $p['profile_id'] == 0 ? $admin_sign['zero_tariff'] : $m['edost']['data'][$p['profile_id']]['company']);
									$company[$c]['count']++;
									if (!empty($v['edost'][$m_key]) && in_array($p['ID'], $v['edost'][$m_key])) {
										$count_active++;
										$company[$c]['active']++;
									}
								}
								$company_string = array();
								foreach ($company as $c) if ($c['active'] != 0) $company_string[] = $c['name'];
								$list_id = 'paysystem_'.$k.'_'.$m_key;
								if (empty($company_string)) $company_string = array('<span style="color: #A00;">'.$admin_sign['no_tariff'].'</span>');
								else if ($count_active > 0 && $count == $count_active) $company_string = array('<span style="color: #0A0;">'.$admin_sign['all_tariff'].'</span>');
								if (!empty($company_string)) {
									echo '<div id="'.$list_id.'_string">'.implode(', ', $company_string).
										'<div style="text-align: right;"><span class="edost_link paysystem_list_show" style="color: #AAA;" data-id="'.$list_id.'" onclick="edost_SetData(\'paysystem_list_show\', this)">'.$admin_sign['button']['show'].'</span></div>'.
										'</div> <div id="'.$list_id.'_list" style="display: none;">';
								}
								$c = false;
								$shift = false;
								$top = 0;
								$p_i = 0;
								foreach ($m['PROFILES'] as $p) if (isset($p['company_id'])) {
									if (!empty($zero_tariff_paysystem) && $p['company_id'] == 0) continue;

									$a = (($c !== false || $p['company_id'] != 0) && $c != $p['company_id'] ? true : false);
									$c = $p['company_id'];
									if ($shift != $c) $shift = false;
									$id = 'paysystem_'.$k.'_'.$m_key.'_'.$p['key'];
									$id2 = 'paysystem_'.$k.'_'.$m_key.'_company_'.$c;
									$name = 'paysystem['.$k.'][delivery]['.$p['ID'].']';
									$p_i++;

									if ($a && $company[$c]['count'] > 1) { $shift = $c; ?>
									<div class="checkbox" style="margin-top: <?=($p_i > 1 ? 10 : 0)?>px;">
										<input id="<?=$id2?>" style="margin: 0px;" type="checkbox" <?=($company[$c]['active'] != 0 ? 'checked=""' : '')?> onclick="edost_SetData('paysystem_tariff_active_company', this)">
										<label for="<?=$id2?>"><?=$m['edost']['data'][$p['profile_id']]['company']?></label>
										&nbsp;&nbsp;<span class="edost_link" style="color: #AAA; vertical-align: middle; <?=($company[$c]['active'] != 0 && $company[$c]['active'] != $company[$c]['count'] ? 'display: none;' : '')?>" data-id="<?=$id2?>" onclick="edost_SetData('paysystem_tariff_show', this)"><?=$admin_sign['button']['show']?></span>
									</div>
<?									} ?>
<?									if ($p_i == 0 && $shift === false) $top = 0; else $top = ($a && $shift === false && $company[$c]['count'] == 1 ? 10 : 4); ?>
									<div class="checkbox <?=$id2?>" style="margin-top: <?=$top?>px; <?=($shift !== false && $shift == $c ? 'padding-left: 20px;' : '')?> <?=($shift !== false && ($company[$c]['active'] == 0 || $company[$c]['active'] == $company[$c]['count']) ? 'display: none;' : '')?>">
										<input id="<?=$id?>" name="<?=$name?>" style="margin: 0px;" type="checkbox"<?=(!empty($v['edost'][$m_key]) && in_array($p['ID'], $v['edost'][$m_key]) ? ' checked=""' : '')?>>
										<label for="<?=$id?>"><?=$p['NAME2']?></label>
									</div>
<?								} ?>
								<div style="padding-top: 15px;">
									<div class="edost_button_cancel" style="display: inline-block; font-size: 14px; padding: 2px 0; margin: 7px 14px 0 0; width: 80px; background: #888;" data-id="<?=$list_id?>" data-active="Y" onclick="edost_SetData('paysystem_tariff_active', this)"><?=$admin_sign['button']['check']['Y']?></div>
									<div class="edost_button_cancel" style="display: inline-block; font-size: 14px; padding: 2px 0; margin: 7px 14px 0 0; width: 80px;" data-id="<?=$list_id?>" data-active="N" onclick="edost_SetData('paysystem_tariff_active', this)"><?=$admin_sign['button']['check']['N']?></div>
								</div>
<?								if (!empty($company_string)) echo '</div>';
								echo '</div>';
							} ?>
						</div>
					</div></td>
				</tr></table>
			</div>
<?			}
		}
	} else {
		// настройки модулей

		// непривязанные профили
		if (!empty($no_paysystem) && !$module_error) { ?>
		<div id="paysystem_error" style="margin-bottom: 15px; padding: 0 10px 10px 10px; text-align: center;">
			<div style="font-size: 20px; color: #F00; padding-bottom: 5px;"><?=$admin_sign['paysystem_tariff_error']?></div>
			<div style="text-align: center; font-size: 15px; color: #888;"><?=str_replace('%href%', '/bitrix/admin/edost.php?lang='.LANGUAGE_ID.'&type=paysystem', $admin_sign['paysystem_tariff_error_info'])?></div>
		</div>
<?		}

		// старые настройки
		if ($config_old) { ?>
		<div id="edost_config_old" style="margin-bottom: 15px; padding: 0 10px 10px 10px; text-align: center;">
			<div style="font-size: 20px; color: #F00; padding-bottom: 5px;"><?=$admin_sign['warning']['config_old']?></div>
		</div>
<?		}

		$i = 0;
		foreach ($module as $m_key => $m) {
			$module_key_start = $m_key;
			$i++;

			$active_head = ($module_new ? '<span style="color: #F00;">'.$control_sign['new_module'].'</span>' : $admin_sign['module_config']['active_head'].' <span style="font-size: 18px; color: #888;">['.$m_key.']</span>');
			$active = '
			<div class="checkbox">
				<input id="module_active_'.$m_key.'" name="module['.$m_key.'][ACTIVE]" type="checkbox"'.($m['ACTIVE'] == 'Y' ? ' checked=""' : '').' onclick="edost_SetData(\'module_active\', \''.$m_key.'\')">
				<label for="module_active_'.$m_key.'" class="green" style="font-size: 18px;">'.$admin_sign['module_config']['active'].'</label>
			</div>';

			if ($module_list) { ?>
				<div id="module_active_<?=$m_key?>_main" class="<?=($m['ACTIVE'] == 'Y' ? '' : 'edost_module_off')?>" style="font-size: 20px; background: #F0F0F0; padding: 5px 10px; <?=($i > 1 ? 'margin-top: 15px;' : '')?>">
					<table width="100%" border="0" cellpadding="4" cellspacing="0"><tr>
						<td width="150">
							<span class="edost_module_config_head edost_module_light"><?=str_replace('18px', '14px', $active_head)?></span>
							<?=$active?>
						</td>
						<td width="160" class="edost_module_light">
<?						if (!empty($m['CONFIG']['CONFIG']['template'])) {
							$n = $m['CONFIG']['CONFIG']['template'];
							if (!empty($n['VALUE'])) echo '<span class="edost_module_config_head">'.$admin_sign['module_config']['template_head'].'</span> <span class="edost_module_config_value">'.$n['OPTIONS'][$n['VALUE']].'</span>';
						} ?>
						</td>
						<td class="edost_module_light">
<?						if ($module_site) { ?>
							<span class="edost_module_config_head"><?=$admin_sign['module_config']['site_head_small'.(count($m['site_string']) > 1 ? '2' : '')]?></span> <span class="edost_module_config_value"><?=implode('<br>', $m['site_string'])?></span>
<?						} ?>
						</td>
						<td width="120" class="edost_module_light" style="text-align: right;">
							<a class="edost_office_button edost_office_button_blue" onclick="edost_SetData('get', '<?=$m_key?>'); return false;" href="/bitrix/admin/edost.php?lang=<?=LANGUAGE_ID?>&type=setting&module=<?=$m_key?>"><?=$admin_sign['module_config']['setting']?></a>
						</td>
					</tr></table>
<?					if ($m['ACTIVE'] == 'Y' && isset($m['edost']['error'])) { ?>
					<div style="margin: 0 0 5px 5px; color: #F00; font-size: 16px;"><?=$m['edost']['error']?></div>
<?					} ?>
				</div>
<?			} else { ?>
<?				if ($module_count != 0) echo '<div style="font-size: 20px; background: #F0F0F0; padding: 5px 10px; margin-bottom: 20px;">'.$active_head.'</div>';
				echo $active; ?>

				<div id="module_<?=$m_key?>_div">
					<div>
<?						if (!$module_new && isset($m['edost']['error'])) { ?>
						<div id="module_error" style="margin: 8px 0 0 0; color: #F00; font-size: 20px; text-align: center;"><?=$m['edost']['error']?></div>
<?						}

						$delimiter2 = '<div style="padding: 20px 0;">'.$delimiter.'</div>';
						echo $delimiter2;

						if ($module_site) { ?>
						<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
							<td><div id="module_<?=$m_key?>_site">
								<div style="color: #888;"><?=$admin_sign['module_config']['site_head']?></div>
<?								foreach ($m['site_list'] as $k => $v) { ?>
								<div class="checkbox" style="padding: 2px 0;">
									<input id="<?=$v['code']?>" name="<?=$v['code']?>" onclick="edost_SetData('site', this)" data-site="<?=$v['id']?>" data-start="<?=($v['active'] ? 'Y' : 'N')?>" style="margin: 0px;" type="checkbox" <?=($v['active'] ? 'checked=""' : '')?>>
									<label for="<?=$v['code']?>"><b><?=$v['name']?></b></label>
								</div>
<?								} ?>
							</div></td>
							<td style="padding-left: 15px; opacity: 0.5;"><?=$admin_sign['site_warning']?></td>
						</tr></table>
						<?=$delimiter2?>
<?						}

						foreach ($m['CONFIG']['CONFIG'] as $k => $v) {
							$c = (isset($admin_sign['module_config'][$k]) ? $admin_sign['module_config'][$k] : false);
							$id = 'module_'.$m_key.'_'.$k;
							$name = 'module['.$m_key.'][config]['.$k.']';
							$top = ($k == 'id' ? 0 : 5);
							if (in_array($k, array('hide_error', 'control', 'template'))) {
								echo $delimiter2;
								$top = 0;
							}
							$style = array();
							if (in_array($k, array('host'))) $style[] = 'font-weight: normal;';
							$class = array();
							if (in_array($k, array('id', 'ps', 'host'))) $class[] = 'edost_setting_param_head';
							if ($k == 'template') $class[] = 'orange'; ?>
						<div id="<?=$id?>_div" class="checkbox edost_setting_param" style="margin-top: <?=$top?>px;">
<?							if ($v['TYPE'] == 'Y/N') { ?>
							<input id="<?=$id?>" name="<?=$name?>" <?=(!empty($c['update']) ? 'onclick="edost_SetData(\'module_update\', \''.$m_key.'\')"' : '')?> style="margin: 0px;" type="checkbox"<?=($v['VALUE'] == 'Y' ? ' checked=""' : '')?>>
							<label for="<?=$id?>" <?=($k == 'control' || $k == 'template' ? 'class="orange"' : '')?>><b><?=$v['NAME']?></b></label>
<?							} else { ?>
							<b <?=(!empty($class) ? 'class="'.implode(' ', $class).'"' : '')?> <?=(!empty($style) ? 'style="'.implode(' ', $style).'"' : '')?>><?=$v['NAME']?></b><?=($v['TYPE'] == 'STRING' ? ':' : '')?>
<?							} ?>

<?							if ($v['TYPE'] == 'STRING') { $length = (!empty($c['length']) ? $c['length'] : 40); ?>
							<input class="normal" id="<?=$id?>" data-start="<?=$v['VALUE']?>" name="<?=$name?>" value="<?=$v['VALUE']?>" type="text" style="padding: 0px 4px; width: <?=$length*7?>px;" maxlength="<?=$length?>">
<?							} ?>

<?							if ($v['TYPE'] == 'ENUM') { $ar = $v['OPTIONS']; ?>
							<select id="<?=$id?>" name="<?=$name?>" <?=(!empty($c['update']) ? 'onclick="edost_SetData(\'module_update\', \''.$m_key.'\')"' : '')?> style="vertical-align: baseline;">
<?								foreach ($ar as $k2 => $v2) { ?>
								<option value="<?=$k2?>" <?=($k2 == $v['VALUE'] ? 'selected=""' : '')?>><?=$v2?></option>
<?								} ?>
							</select>
<?							} ?>

							<?=(!empty($c['note']) ? '<span class="note">'.$c['note'].'</span>' : '')?>
							<?=(!empty($c['hint']) ? draw_hint($id, $c['hint'], !empty($c['warning']), 5, 2) : '')?>

<?							if ($k == 'template_ico') {
								$id = 'module_'.$m_key.'_reload_ico';
								$name = 'module['.$m_key.'][reload_ico]'; ?>
							<input id="<?=$id?>" name="<?=$name?>" style="margin-left: 40px;" type="checkbox"> <label for="<?=$id?>"><b><?=$admin_sign['module_config']['reload_ico']?></b></label>
<?							} ?>
						</div>
<?						} ?>

						<?=$delimiter2?>

						<div class="checkbox">
<?						$ar = array('NAME' => array('width' => '300px', 'max' => 100), 'DESCRIPTION' => array('width' => '300px', 'max' => 2000), 'SORT' => array('width' => '50px', 'max' => 4));
						if (!empty($vat)) $ar['VAT_ID'] = array();
						foreach ($ar as $k => $v) $key_end = $k;
						foreach ($ar as $k => $v) {
							$name = 'module['.$m_key.']['.$k.']'; ?>
							<b class="edost_setting_param_head" style="font-weight: normal;"><?=$admin_sign['module_'.$k]?></b>
<?							if ($k == 'VAT_ID') { ?>
								<select name="<?=$name?>" style="vertical-align: baseline; max-width: 140px;">
									<option value="0"><?=$admin_sign['module_config']['vat_zero']?></option>
<?									foreach ($vat as $k2 => $v2) { ?>
									<option value="<?=$k2?>" <?=(isset($m[$k]) && $k2 == $m[$k] ? 'selected=""' : '')?>><?=$v2['NAME']?></option>
<?									} ?>
								</select>
<?								$id = 'module_'.$m_key.'_reload_vat';
								$name = 'module['.$m_key.'][reload_vat]'; ?>
								<input id="<?=$id?>" name="<?=$name?>" style="margin-left: 15px;" type="checkbox"> <label for="<?=$id?>"><b><?=$admin_sign['module_config']['reload_vat']?></b></label>
<?							} else { ?>
								<input class="normal" name="<?=$name?>" value="<?=str_replace('"', '&quot;', $m[$k])?>" type="text" style="vertical-align: baseline; padding: 0px 4px; width: <?=$v['width']?>;" maxlength="<?=$v['max']?>">
								<?=(!empty($admin_sign['module_'.$k.'_hint']) ? ' &nbsp;'.draw_hint('module_'.$m_key.'_'.$k, $admin_sign['module_'.$k.'_hint'], false, 0, 2) : '')?>
<?							}
							if ($k != $key_end) echo '<div style="height: 5px;"></div>';
						} ?>
						</div>

						<?=$delimiter2?>

						<div id="setting_module_tariff_<?=$m_key?>" class="checkbox">
							<table width="100%" border="0" cellpadding="2" cellspacing="0">
<?							$c = false;
							$count = 0;
							foreach ($m['PROFILES'] as $p) if (isset($p['company_id'])) $count++;
							foreach ($m['PROFILES'] as $p) if (isset($p['company_id'])) {
								$name = 'module['.$m_key.'][profile]['.$p['key'].']';

								if (empty($p['LOGOTIP'])) $ico = '<div style="display: inline-block; width: 22px;"></div>';
								else {
									$img = CFile::GetFileArray($p['LOGOTIP']);
									$ico = '<img class="edost_ico" style="width: 20px; vertical-align: middle; padding-right: 2px; padding-bottom: 3px;" src="'.$img['SRC'].'" border="0">';
								}
								if (!empty($p['ID'])) $ico = '<a class="edost_link" href="/bitrix/admin/sale_delivery_service_edit.php?lang=ru&PARENT_ID='.$m_key.'&ID='.$p['ID'].'&tabControl_active_tab=edit_restriction" target="_blank">'.$ico.'</a>';

								if ($p['company_id'] == 0) { ?>
								<tr style="padding-top: 10px; font-size: 14px; color: #888;"><td></td><td><?=$admin_sign['zero_tariff']?> <?=draw_hint('zero_tariff_'.$m_key, $admin_sign['zero_tariff_hint'], false, 4, 1)?></td><tr>
<?								} ?>

<?								if ($c && $c != $p['company_id']) echo '<tr style="height: 8px;"><td></td><tr>'; ?>

								<tr style="margin-top: <?=($c && $c != $p['company_id'] ? 10 : 4)?>px;">
									<td width="22"><?=$ico?></td>
									<td width="40%"><input class="normal" name="<?=$name?>[NAME]" value="<?=str_replace('"', '&quot;', $p['NAME'])?>" type="text" style="width: 100%;" maxlength="100"></td>
									<td style="padding-left: 5px;"><input class="normal" name="<?=$name?>[DESCRIPTION]" value="<?=str_replace('"', '&quot;', $p['DESCRIPTION'])?>" type="text" style="margin-left: 10px; width: 100%;" maxlength="2000"></td>
<?									if (!empty($vat) && $p['company_id'] != 0) { ?>
									<td width="80" style="padding-left: 25px;"><select name="<?=$name?>[VAT_ID]" style="width: 100%; height: 23px; border-radius: 0; box-shadow: none; -webkit-box-shadow: none; padding: 0;">
										<option value="0"></option>
<?										foreach ($vat as $k2 => $v2) { ?>
										<option value="<?=$k2?>" <?=(isset($p['VAT_ID']) && $k2 == $p['VAT_ID'] ? 'selected=""' : '')?>><?=$v2['NAME']?></option>
<?										} ?>
									</select></td>
<?									} else { ?>
									<td width="10"></td>
<?									} ?>
								</tr>

<?								if ($c === false && $count > 1) { ?>
								<tr style="height: 30px; font-size: 14px; color: #888; vertical-align: bottom;">
									<td></td>
									<td width="40%"><?=$admin_sign['tariff_title']?> <?=draw_hint('setting_module_tariff_title_'.$m_key, $admin_sign['tariff_title_hint'], false, 4, 1)?></td>
									<td style="padding-left: 15px;"><?=$admin_sign['tariff_description']?></td>
<?									if (!empty($vat)) { ?>
									<td width="80" style="padding-left: 25px;"><?=$admin_sign['tariff_vat']?></td>
<?									} ?>
								</tr>
<?								} ?>

<?								$c = $p['company_id']; ?>
<?							} ?>
							</table>
						</div>
					</div>
				</div>
<?			}
		}
	}

	if ($show_bar) { ?>
	<div id="edost_bar_div">
		<div id="edost_bar">
			<div class="edost_button_save" style="display: inline-block;" onclick="edost_SetData('save')"><?=$control_sign['button']['save']?></div>
<?			if (!$param['paysystem'] && !$module_list && $module_count > 1) { ?>
			<div class="edost_button_close" style="display: inline-block; margin-left: 40px;" onclick="edost_SetData('get', '')"><?=$control_sign['button']['close']?></div>
<?			} ?>

<?			if (!$param['paysystem'] && !$module_new && $module_site && ($module_list || $module_count == 1)) { ?>
			<div class="edost_button_cancel" style="display: inline-block; float: right; font-size: 16px; padding: 3px 0; margin: 7px 14px 0 0; width: 160px;" onclick="edost_SetData('new')"><?=$control_sign['button']['new_module']?></div>
<?			} ?>
		</div>
	</div>
<?	} ?>

	<div id="setting_save_error" style="color: #800;"></div>

<script type="text/javascript">
	if (window.edost_resize) edost_resize.bar('timer');
<? if ($module_key_start !== false) { ?>
	edost_SetData('module_update', '<?=$module_key_start?>');
<? } ?>
</script>

<?
}




// оформление доставки (в админке в меню 'eDost')
if ($mode == 'register') {
//	echo '<br><b>data:</b><pre style="font-size: 12px">'.print_r($data, true).'</pre>';

	// список заказов с разделением по группам
	$button = array_fill_keys(array_keys($control_sign['button']['register_data']), false);
	$list = $list_active = array_fill_keys(array_keys($control_sign['list_head']), array());
	foreach ($data as $k => $v) {
		$key = '';
		if ($param['control'] == 'warning_red') {
			if ($v['status_warning'] == 2) $key = 'warning_red';
		}
		else if ($param['control'] == 'warning_orange') {
			if ($v['status_warning'] == 3) $key = 'warning_orange';
		}
		else if ($param['control'] == 'warning_pink') {
			if ($v['status_warning'] == 1) $key = 'warning_pink';
		}
		else if ($param['control'] == 'status_20') {
			if (!empty($v['batch_20'])) $key = 'batch_20';
			else if ($v['register'] == 5) $key = 'register_complete_batch_full';
			else if ($v['register'] == 4) $key = (empty($v['batch_code']) ? 'register_complete' : 'register_complete_batch');
		}
		else if ($param['control'] == 'batch_20') {
			if (!empty($v['batch_20'])) $key = 'batch_20';
		}
		else if ($param['control'] == 'delete') {
			if (in_array($v['register'], array(6, 7, 8))) $key = 'register_delete';
		}
		else if ($param['control'] == 'register_complete') {
			if ($v['register'] == 4 && empty($v['batch_code'])) $key = 'register_complete';
		}
		else if ($param['control'] == 'register_complete_batch') {
			if (empty($v['batch_20']) && $v['register'] == 4 && !empty($v['batch_code'])) $key = 'register_complete_batch';
		}
		else if ($param['control'] == 'register_complete_batch_full') {
			if (empty($v['batch_20']) && $v['register'] == 5 && !empty($v['batch_code'])) $key = 'register_complete_batch_full';
		}
        else {
			if ($v['control_active']) $key = 'control';
			else if (empty($v['register'])) $key = (empty($v['field_error']) ? 'register_new' : 'field_error');
			else if (in_array($param['control'], array('company', 'total')) && in_array($v['register'], array(6, 7, 8))) $key = 'register_delete';
			else if ($v['status_warning'] == 2) $key = 'warning_red';
			else if (in_array($v['register'], array(2, 9, 10))) $key = 'register_transfer';
			else if (!empty($v['batch_20'])) $key = 'batch_20';
			else if (!empty($v['complete'])) {
				if (empty($v['batch_code'])) $key = 'register_complete';
				else $key = ($v['register'] == 4 ? 'register_complete_batch' : 'register_complete_batch_full');
			}
		}

		if ($key == '') continue;

		if ($key == 'register_new') {
			$button['register'] = true;
			if (empty($v['register_required'])) $button['print_no_register'] = true;
		}
		if ($key == 'register_complete') $button['batch'] = true;
		if ($key == 'register_complete_batch' && $v['register'] == 4) $button['office'] = true;

		if (!in_array($key, array('batch_20', 'register_delete', 'warning_pink', 'register_total', 'register_transfer', 'control', 'field_error'))) $list_active[$key] = $v['active'] = true;

		// удаление бланков описи (107) у оформленных заказов
		if (!($v['register'] == 0 && empty($v['register_required'])))
			foreach ($v['doc'] as $k2 => $v2) if ($v2 == '107') unset($v['doc'][$k2]);

		$list[$key][$k] = $v;
	}

	if (!empty($list['warning_red']) && $list_active['warning_red']) $button['register_repeat'] = true;
	foreach ($button as $k => $v) if (!$v) unset($button[$k]);
//	echo '<br><b>list:</b><pre style="font-size: 12px">'.print_r($button, true).'</pre>';

	$batch_date = array();
	for ($i = 0; $i <= 1; $i++) {
		$s = ($i == 0 ? time() : $s) + $cookie['register_batch_date']*60*60*24;
		$n = intval(date('N', $s));
		if ($cookie['register_batch_date_skip_weekend'] == 'Y' && $n >= 6) $s += (8-$n)*60*60*24;
		$batch_date[$i] = date('d.m.Y', $s);
	}

	// сортировка заказов со сдачей
	$list_sort = array('batch_20', 'register_complete_batch', 'register_complete_batch_full');
	foreach ($list_sort as $list_key) if (!empty($list[$list_key])) {
		$ar2 = $list[$list_key];
		$ar = array('date' => array(), 'number' => array(), 'id' => array());
		foreach ($ar2 as $v) {
			$ar['date'][] = edost_class::time($v['batch']['date']);
			$ar['number'][] = $v['batch']['number'];
			$ar['id'][] = $v['id'];
		}
		array_multisort($ar['date'], SORT_DESC, SORT_NUMERIC, $ar['number'], SORT_ASC, SORT_NUMERIC, $ar['id'], SORT_ASC, SORT_NUMERIC, $ar2);
		$ar = array();
		foreach ($ar2 as $v) $ar[$v['id']] = $v;
		$list[$list_key] = $ar;
	}

	// стили для заголовков групп
	$head = array(
		'register_transfer' => array('background' => 'AAA'),
		'warning_red' => array('background' => 'E55'),
		'warning_orange' => array('background' => '91764f'),
		'warning_pink' => array('background' => 'A77'),
		'register_total' => array('background' => '888'),
		'register_delete' => array('background' => 'AAA'),

		'register_new' => array('background' => '08C'),
		'field_error' => array('background' => 'A77'),
		'register_complete' => array('background' => 'F80'),
		'register_complete_batch' => array('background' => '9c5e85'),
		'register_complete_batch_full' => array('background' => '484'),

		'search_order' => array('background' => '24C'),
		'search_shipment' => array('background' => '08C'),
		'history' => array('background' => '08A'),
		'default' => array('background' => '888'),
	);

?>
<div class="edost">
	<div id="edost_reload" class="edost_link" style="float: right;"><img style="position: absolute; opacity: 0.5; padding: 5px; margin: -18px 0px 0 -2px;" src="<?=$img_path?>/control_reload.png" border="0" onclick="edost_SetParam('register', 'reload_full');" title="<?=$control_sign['button']['reload']?>"></div>

<?	if ($control_head['ico_count'] > 0) { ?>
	<div id="edost_register_ico" style="padding: 0px 0 5px 0;"><?=$control_head['ico']?></div>
	<div class="edost_delimiter" style="margin: 10px 0;"></div>
<?	} ?>

	<table width="100%" border="0" cellpadding="4" cellspacing="0"><tr>
		<td width="280" style="vertical-align: top;"><?=$control_head['count_list']?></td>
		<td width="280" style="vertical-align: top;"><?=$control_head['count_list2']?></td>
		<td style="vertical-align: top; text-align: right;">
<?			if ($param['control'] != 'main') { ?>
			<div style="padding-bottom: 5px;"><span class="edost_link" style="font-size: 14px; font-weight: bold;" onclick="edost_SetParam('register', '')"><?=$control_sign['main2']?></span></div>
<?			} ?>
			<span style="font-size: 11px;"><?=$count_string?></span>
		</td>
	</tr></table>

	<div style="float: right; text-align: right;">
		<span id="control_setting_show" class="edost_link" style="<?=($setting_show ? 'display: none;' : '')?>" onclick="edost_SetParam('register_setting', 'Y')"><?=$control_sign['setting']['show']?></span>
		<span id="control_setting_hide" class="edost_link" style="<?=(!$setting_show ? 'display: none;' : '')?>" onclick="edost_SetParam('register_setting', 'N')"><?=$control_sign['setting']['hide']?></span>
	</div>
	<div id="control_setting" style="margin-top: 20px; padding: 10px; border: 1px solid #888;<?=(!$setting_show ? ' display: none;' : '')?>">
<?		$ar = array('insert');
		foreach ($ar as $k => $v) echo '<div style="padding-top: '.($k != 0 ? '5' : '0').'px;">'.str_replace('%data%', $day[$v]['select'], $control_sign['setting']['day_'.$v]).'</div>';

		$ar = array('batch_date', 'print_107', 'print_label_format');
		foreach ($ar as $k => $v) {
			$s = '';
			foreach ($control_sign['select'][$v] as $k2 => $v2) $s .= '<option value="'.$k2.'"'.($k2 == $cookie['register_'.$v] ? ' selected' : '').'>'.$v2.'</option>';
			$s = '<select style="height: 20px; padding: 1px; margin-top: -3px;" onchange="edost_SetParam(\'register_'.$v.'\', this.value)">'.$s.'</select>';
			echo '<div style="padding-top: 5px;">'.str_replace('%data%', $s, $control_sign['setting'][$v]).'</div>';
		}

		$ar = array('batch_date_skip_weekend', 'item', 'status', 'no_label', 'no_batch');
		foreach ($ar as $v) { ?>
		<div class="checkbox" style="padding-top: 5px; font-size: 13px;">
			<input id="register_<?=$v?>" style="margin: 0;" type="checkbox"<?=($cookie['register_'.$v] == 'Y' ? ' checked=""' : '')?> onclick="edost_SetParam('register_<?=$v?>', this.checked)">
			<label for="register_<?=$v?>"><b><?=$control_sign['setting'][$v]?></b></label>
		</div>
<?		} ?>

		<div class="edost_button_option_main" style="margin-top: 10px;" onclick="edost_window.set('profile_setting')"><?=$control_sign['setting']['profile']?></div>
		<div class="edost_button_option_main" style="margin-top: 10px;" onclick="edost_window.set('option_setting', 'head=<?=$control_sign['setting']['option']?>')"><?=$control_sign['setting']['option']?></div>

<?		/* отдельная кнопка настроек для каждой компании
		<div style="padding: 3px 5px; margin: 10px 0 4px 0; color: #555; font-size: 20px; text-align: center;"><?=$control_sign['setting']['option']?></div>
		<div style="text-align: center;">
<?		foreach ($register_option as $v) { ?>
			<div class="edost_button_option">
				<img class="edost_ico" src="<?=$ico_path?>/company/<?=$v['id']?>.gif" border="0">
				<span><?=$v['name']?></span>
			</div>
<?		} ?>
		</div>
*/ ?>

<?		/* редактирование опций */ ?>
		<div style="display: none;">
<?			foreach ($register_option as $k => $v) { ?>
			<div id="edost_option_<?=$k?>_div">
<?				$a = false;
				foreach ($v['service'] as $s) if ($s['value'] != 0) {
					$a = true;
					$c = array();
					foreach (edost_class::$depend as $key) if (!empty($s['depend_'.$key])) $c[] = 'edost_option_service_depend_'.$key; ?>
				<div class="edost_option_service <?=implode(' ', $c)?>">
					<label>
						<input data-id="<?=$s['id']?>" style="margin: 0px;" type="checkbox" value="<?=$k2?>">
						<span><?=$s['name']?></span>
					</label>
				</div>
<?				}
				if (!$a) { ?>
				<div style="text-align: center; color: #888;"><?=$control_sign['package']['option_disable']?></div>
<?				} ?>
			</div>
<?			} ?>
		</div>

<?		/* изменение даты сдачи */ ?>
		<div id="edost_batch_date_div" style="display: none;">
			<div class="adm-workarea" style="padding: 0; text-align: center;">
				<div class="adm-input-wrap adm-calendar-inp adm-calendar-first" style="display: inline-block; margin: 0 0 10px 0;">
					<input type="text" class="adm-input adm-calendar-from edost_package_on" style="width: 140px; font-size: 16px;" id="edost_register_date" size="15" value="<?=$batch_date[0]?>" data-date="<?=$batch_date[1]?>" style="font-size: 16px;" onfocus="edost_Register('input_focus', this)" onblur="edost_Register('input_blur')">
					<span class="adm-calendar-icon" onclick="BX.calendar({node:this, field:'edost_register_date', bTime: false, bHideTime: false, callback: function() { edost_Register('input_start', 'edost_register_date'); }, callback_after: function() { edost_Register('update_input'); }});"></span>
				</div>
				<div class="edost_button_base edost_button_date" style="background: #08C; font-size: 14px; padding: 4px 8px 5px 8px; line-height: 14px;" onclick="edost_window.submit('date')"><?=$control_sign['button']['batch_date2']?></div>
				<div class="edost_call_warning edost_warning" style="display: none; padding-top: 8px;"><?=$control_sign['call']['batch_date_warning']?></div>
			</div>
		</div>

<?		/* исключение из сдачи (отмена оформления и изменение даты заказа) */ ?>
		<div id="edost_register_delete_div" style="display: none;">
			<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
				<td width="50%" align="left" valign="center">
					<div class="edost_button_base" style="background: #F00; font-size: 14px; padding: 4px 8px 5px 8px; line-height: 14px;" onclick="edost_window.submit('delete')"><?=$control_sign['button']['delete_register']?></div>
				</td>
				<td class="adm-workarea" align="right" valign="center" style="padding: 0;">
					<div class="adm-input-wrap adm-calendar-inp adm-calendar-first" style="display: inline-block; margin: 0 0 10px 0;">
						<input type="text" class="adm-input adm-calendar-from edost_package_on" style="width: 140px; font-size: 16px;" id="edost_register_date" size="15" value="<?=$batch_date[0]?>" data-date="<?=$batch_date[1]?>" style="font-size: 16px;" onfocus="edost_Register('input_focus', this)" onblur="edost_Register('input_blur')">
						<span class="adm-calendar-icon" onclick="BX.calendar({node:this, field:'edost_register_date', bTime: false, bHideTime: false, callback: function() { edost_Register('input_start', 'edost_register_date'); }, callback_after: function() { edost_Register('update_input'); }});"></span>
					</div>
					<div class="edost_button_base edost_button_date" style="background: #08C; font-size: 14px; padding: 4px 8px 5px 8px; line-height: 14px;" onclick="edost_window.submit('date')"><?=$control_sign['button']['batch_date2']?></div>
				</td>
			</tr></table>
		</div>
	</div>

	<div style="height: 20px;"></div>

	<div id="edost_control_data_div">
<?		if (empty($data)) { ?>
		<div style="text-align: center; font-size: 16px;">
<?			if (!empty($param['search'])) { ?>
			<div style="text-align: center; font-size: 16px; color: #800;"><?=$control_sign['no_'.$param['control']]?></div>
<?			} else { ?>
			<span style="color: #800; font-size: 20px;"><?=$control_sign['register_no']?></span>
			<br><br><span style="color: #888; font-size: 14px;"><?=$control_sign['register_help']?></span>
			<br><span style="font-size: 12px;"><?=$control_sign['register_help_link']?></span>
<?			} ?>
		</div>
<?		} ?>

<?
	// определение максимального количества документов в группе
	$n = 0;
	foreach ($list as $f_key => $f)
		if (empty($f)) unset($list[$f_key]);
		else foreach ($f as $k => $v) $n = max($n, count($v['doc']));
	$doc_width = 7 + ($n != 0 ? $n  : 1)*68 + 8;

	// ключи блоков по которым добавлять кнопки и печать
	$button_key = $print_key = $reload_key = '';
	$print_active = false;
	foreach ($list as $f_key => $f)	{
		if (in_array($f_key, array('register_new', 'register_complete', 'warning_red', 'register_complete_batch'))) $button_key = $f_key;
		if (in_array($f_key, array('register_new', 'register_complete', 'register_complete_batch', 'register_complete_batch_full', 'warning_red'))) $print_key = $f_key;
		if (in_array($f_key, array('register_complete', 'register_complete_batch', 'register_complete_batch_full'))) $print_active = true;
		$reload_key = $f_key;
	}
	if (!$print_active) $print_key = '';
	if ($button_key != '') $reload_key = $button_key;

	$filter = '';
	if ($history_mode != '') $filter = 'filter_history';
	else if ($register_search) $filter = 'filter_search';

	$i = 0;
	foreach ($list as $f_key => $f) {
		$h = (isset($head[$f_key]) ? $head[$f_key] : $head['default']);

		if ($i != 0) echo '<div style="height: 30px;"></div>';
		$i++;

		if (isset($h[0])) echo $h[0];
		else echo '<div style="padding: 4px; color: #FFF; background: #'.$h['background'].'; text-align: center; font-size: 15px; font-weight: bold;">'.
				(!empty($list_active[$f_key]) ? '
				<div style="position: absolute; margin: 1px 0 0 2px;">
					<input class="adm-checkbox adm-designed-checkbox" id="edost_shipment_'.$f_key.'_active" type="checkbox" checked="" onclick="edost_Register(\'active_all\', this)">
					<label'.(in_array($f_key, array('register_complete_batch_full')) ? ' style="display: none;"' : '').' class="adm-designed-checkbox-label adm-checkbox" for="edost_shipment_'.$f_key.'_active"></label>
				</div>' : '').$control_sign['list_head'][$f_key == 'register_complete_batch_full' && $company == 5 ? 'register_complete' : $f_key].($filter != '' ? '<span style="display: inline-block; margin: 0 0 2px 10px; padding: 0px 4px; background: #FFF; color: #000; border-radius: 5px; font-size: 12px; opacity: 0.5; font-weight: normal; ">'.$control_sign[$filter].'</span>' : '').
			'</div>';
?>
		<div class="edost" id="edost_shipment_<?=$f_key?>" style="border: 1px solid #e3e8ea; padding: 10px; background: #eef5f5;">
<?
		if (!in_array($f_key, array('batch_20', 'register_complete_batch', 'register_complete_batch_full'))) echo $delimiter;
		$batch_code = $batch_id = '';
		foreach ($f as $k => $v) {
			$print_no_register = ($v['register'] == 0 && empty($v['register_required']) ? true : false);
			$batch_active = (in_array($f_key, array('batch_20', 'register_complete_batch', 'register_complete_batch_full', 'company')) && !empty($v['batch_code']) ? true : false);
			if ($batch_active && $v['batch_code'] != $batch_code) { $batch_id = $k; ?>
                <div style="margin: <?=($batch_code != '' ? '40' : '0')?>px 0 10px 0; padding: 2px 10px; background: #FFF; border-width: 1px 0 1px 0; border-style: solid; border-color: #CCC;">
					<table width="100%" border="0" bordercolor="#888" cellpadding="0" cellspacing="0"><tr>
						<td width="25" valign="center">
<?							if (!empty($v['active'])) { ?>
							<div>
								<input class="adm-checkbox adm-designed-checkbox edost_batch<?=($v['register'] != 5 ? ' edost_batch_office' : '')?>" id="edost_batch_<?=$k?>" type="checkbox" checked="" onclick="edost_Register('active_batch_all', this.id)">
								<label<?=(in_array($f_key, array('register_complete_batch_full')) ? ' style="display: none;"' : '')?> class="adm-designed-checkbox-label adm-checkbox" for="edost_batch_<?=$k?>"></label>
								<input id="edost_batch_name_<?=$k?>" type="hidden" value="<?=str_replace(array('%number%', '%date%'), array($v['batch']['number'], $v['batch']['date']), $control_sign['print']['batch_name']).($v['register'] != 5 ? ' (<span style=\'color: #F00;\'>'.$control_sign['batch_select']['no_office'].'</span>)' : '')?>">
							</div>
<?							} ?>
						</td>
						<td align="left">
							<label for="edost_batch_<?=$k?>">
							<div style="display: inline-block;">
								<span style="font-size: 14px; font-weight: bold; color: #<?=(!empty($v['batch_20']) ? 'F00' : '555')?>;"><?=$v['batch_date_formatted']?></span>
<?								if (!empty($v['batch']['number'])) { ?>
								<br><span style="font-size: 13px; color: #555;"><?=$control_sign['batch_prefix']?></span> <span style="font-size: 13px; color: #555;"><?=$control_sign['order_prefix']?></span> <span style="font-size: 14px; font-weight: bold; color: #555;"><?=$v['batch']['number']?></span>
<?								} ?>
								<div id="edost_call_<?=$k?>" style="<?=(empty($v['batch']['call']) ? 'display: none;' : '')?> font-weight: bold; color: #080;"><?=$control_sign['call']['head2']?></div>
							</div>
							</label>
						</td>
						<td align="right" valign="center">
							<div id="edost_batch_button_<?=$k?>" style="display: inline;">
								<? /* показать все заказы из сдачи */ ?>
<?								$n = 0;
								foreach ($f as $k2 => $v2) if ($v2['batch_code'] == $v['batch_code']) $n++;
								if (isset($batch[$v['batch_code']]) && count($batch[$v['batch_code']]['order']) != $n) { ?>
									<div id="edost_batch_all_button_<?=$k?>" style="display: inline-block;">
										<div style="display: inline-block;">
											<span class="edost_control_button edost_control_button_all" onclick="edost_Register('search', '<?=implode(',', $batch[$v['batch_code']]['order'])?>')"><?=$control_sign['button']['batch_order'].' ('.count($batch[$v['batch_code']]['order']).')'?></span>
										</div>
										<?=$delimiter_button?>
									</div>
<?								} ?>

<?								if (empty($v['batch_20']))
									if ($v['register'] != 5) { ?>
										<? /* зарегистрировать в отделении */ ?>
										<div style="display: inline-block;"><span class="edost_control_button edost_control_button_office" onclick="edost_Register('button', 'button_office|<?=$v['id']?>')"><?=$control_sign['button']['batch_office']?></span></div>
										<?=$delimiter_button?>
<?									} else { ?>
										<? /* печать */ ?>
										<div id="edost_batch_print_button_<?=$k?>" style="display: inline-block;">
											<div style="display: inline-block;">
<? /*											<span class="edost_control_button edost_control_button_print" onclick="edost_Register('print', [<?=$v['id']?>, 'batch'])"><?=$control_sign['button']['print']?></span> */ ?>
												<span class="edost_control_button edost_control_button_print" onclick="edost_Register('print', [<?=$v['id']?>, 'batch_general'<?=($v['company_id'] == 23 ? ", '103'" : '')?>])"><?=$control_sign['print']['batch_general'.($v['company_id'] == 23 ? '_103' : '')]?></span><?=($v['company_id'] != 23 ? ', ' : '')?>
<?												if ($v['company_id'] != 23) { ?>
												<span class="edost_control_button edost_control_button_print" onclick="edost_Register('print', [<?=$v['id']?>, 'batch_order'])"><?=$control_sign['print']['batch_order']?></span>
<?												} ?>
											</div>
											<?=$delimiter_button?>
										</div>

<?										if ($v['company_id'] == 5) { ?>
											<? /* вызвать курьера */ ?>
<?											if (empty($v['batch']['call'])) { ?>
											<div id="edost_call_button_<?=$k?>" style="display: inline-block;">
												<div style="display: inline-block;"><span class="edost_control_button edost_control_button_call" data-param="id=<?=$v['id']?>" onclick="edost_window.set('call<?=(empty($v['batch']['profile_shop']) || empty($register_profile[$v['batch']['profile_shop']]['flag_call']) ? '_profile' : '')?>', this)"><?=$control_sign['call']['head']?></span></div>
												<?=$delimiter_button?>
											</div>
<?											} ?>

											<? /* профили */ ?>
											<div style="display: inline-block;"><span class="edost_control_button edost_control_button_low2" data-param="id=<?=$v['id']?>" onclick="edost_window.set('profile', this)"><?=$control_sign['button']['profile']?></span></div>
											<?=$delimiter_button?>
<?										} ?>
<?									} ?>

								<? /* изменение даты */ ?>
								<div style="display: inline-block;">
									<span class="edost_control_button edost_control_button_low2" data-param="id=<?=$v['id'].';batch_date='.$v['batch']['date'].';call='.(!empty($v['batch']['call']) ? $v['batch']['call'] : '')?>" onclick="edost_window.set('batch_date', this)"><?=$control_sign['button']['batch_date']?></span>
								</div>
								<?=$delimiter_button?>
							</div>
							<div style="display: inline-block;"><span class="edost_control_button edost_control_button_add" data-param="id=<?=$v['id']?>" onclick="edost_window.set('batch_delete', this)"><?=$control_sign['button']['batch_delete']?></span><span style="display: none; font-weight: bold;"><?=$control_sign['button']['batch_delete_ok']?></span></div>
						</td>
					</tr></table>
                </div>
<?
				echo $delimiter;
				$batch_code = $v['batch_code'];
			} ?>
			<table width="100%" style="min-height: 80px;" border="0" bordercolor="#888" cellpadding="4" cellspacing="0"><tr>
				<td width="25">
<?					if (!empty($v['active'])) { ?>
					<input class="adm-checkbox adm-designed-checkbox edost_shipment" id="edost_shipment_<?=$k?>" data-code="<?=$v['order_code']?>" type="checkbox" checked="" onclick="edost_Register('active_main', this.id)">
					<label<?=(in_array($f_key, array('register_complete_batch', 'register_complete_batch_full')) ? ' style="display: none;"' : '')?> class="adm-designed-checkbox-label adm-checkbox" for="edost_shipment_<?=$k?>"></label>
<?					} ?>

<?					if (!empty($batch_id)) echo '<input id="edost_batch_shipment_'.$k.'" name="edost_batch_'.$batch_id.'" type="hidden" value="'.$k.'">'; ?>
				</td>
				<td width="60" style="font-size: 15px; text-align: center;" align="center">
					<? if ($v['order_canceled']) { ?>
					<div style="font-size: 13px; color: #F00; font-weight: bold;"><?=$control_sign['order_canceled']?></div>
					<? } ?>

					<div style="font-size: 11px; opacity: 0.6;"><?=$v['order_date_formatted']?></div>

					<a href="/bitrix/admin/sale_order_view.php?ID=<?=$v['order_id']?>&lang=<?=LANGUAGE_ID?>&edost_link=shipment_<?=$k?>#delivery"><b><span style="font-size: 10px;"><?=$control_sign['order_prefix']?></span><?=$v['order_id']?></b></a>
					<br><a href="/bitrix/admin/sale_order_shipment_edit.php?order_id=<?=$v['order_id']?>&shipment_id=<?=$v['id']?>&lang=<?=LANGUAGE_ID?>"><?=$v['id']?></a>
				</td>
				<td width="440" align="left" style="font-size: 13px;">
					<div style="height: 55px; border-right: 1px solid #DDD; position: absolute; margin: 0 0 0 <?=$doc_width?>px;"></div>
					<div style="display: inline-block; width: <?=$doc_width?>px;">
<?						if (empty($v['doc'])) echo '<div style="color: #AAA; text-align: center; position: absolute; margin: -40px 0 0 5px; width: 55px;">'.$control_sign['no_doc'].'</div>';
						else {
							// кнопка печати
							if ($f_key == 'register_complete' && in_array($v['tariff'], array(3)) || in_array($f_key, array('register_complete_batch', 'register_complete_batch_full'))) {
								echo '<div id="edost_register_button_'.$k.'_print" style="position: absolute; margin: 44px 0 0 '.((count($v['doc']) - 1)*35 - 10).'px;"><span class="edost_control_button edost_control_button_low" onclick="edost_Register(\'print\', ['.$v['id'].'])">'.$control_sign['button']['print'].'</span></div>';
							}

							foreach ($v['doc'] as $v2) {
								$a = ($history_mode != 'print_no_register' || in_array($v2, $register_set[$v['id']]['doc']) ? true : false);
								$a2 = (in_array($f_key, array('register_complete_batch', 'register_complete_batch_full') && !in_array($v['company_id'], array(5))) ? true : false);

								if (empty($v['field_error']) && ($v['register_required'] || $f_key != 'register_complete') && (!empty($v['complete']) || $print_no_register)) echo '<img class="edost_doc'.($print_no_register ? ' edost_doc_print_no_register' : '').' edost_register'.($a2 ? '2' : '').' edost_register_'.($a ? 'on' : 'off').'" style="margin-right: 8px;'.($a2 ? ' box-shadow: none;' : '').'" data-code="'.$k.'_doc_'.$v2.'" data-mode="'.($cookie['register_no_label'] == 'Y' ? 'normal' : $doc_data[$v2]['mode']).'"'.(!$a2 ? ' onclick="edost_Register(\'active\', this)"' : '').'src="'.$doc_path.'/'.$v2.'_s.gif">';
								else echo '<img class="edost_register_disabled3" style="margin-right: 8px;" src="'.$doc_path.'/'.$v2.'_s.gif">';
							}
						} ?>
					</div>
<?
					foreach ($v['checkbox'] as $k2 => $v2) { ?>
					<div style="padding: 5px 8px 0 20px; display: inline-block;">
<? 					if (!empty($v[$k2.'_required'])) echo '<input id="'.$k2.'_required_'.$k.'" type="hidden" value="1">';
						if ($v[$k2.'_active']) {
							if ($k2 == 'register' && $v['register'] == 2 && $v['status_warning'] == 2) $code = 'wait';
							else if ($k2 == 'register' && in_array($v['register'], array(6, 8)) || $k2 == 'batch' && $v['register'] == 7 || $v['register'] == 8) $code = 'delete';
							else if ($k2 == 'batch' && $v['register'] == 9) $code = 'date';
							else if ($k2 == 'batch' && $v['register'] == 10) $code = 'office';
							else if (in_array($k2, array('register', 'batch')) && $v['register'] == 2) $code = 'transfer';
							else if ($k2 == 'register' && $v['register'] == 6) $code = 'delete';
							else $code = ($k2 == 'batch' && $v['register'] == 4 ? 'active2' : 'active');

							$class = array();
							if ($v['register'] == 2 && $v['status_warning'] != 2) $class[] = 'edost_register_active_disabled';

							$s = 'class="edost_register_disabled'.($v['register'] == 2 ? '' : '2').($code == 'wait' ? ' edost_register_repeat' : '').'"';
							if ($code == 'wait') $s .= ' data-code="'.$k.'_'.$k2.'"';
							echo '<img'.(!empty($class) ? ' class="'.implode(' ', $class).'"' : '').' id="edost_'.$k2.'_img_'.$v['id'].'"'.' style="position: absolute; z-index: 2; margin: 5px 0 0 -10px;" src="'.$img_path.'/control_'.$v2.'_'.$code.'.png">';

							// кнопки отмены
							if (in_array($code, array('active', 'active2')) && !empty($v['register']) && $v['register'] != 6 || $code == 'wait') {
								if ($k2 == 'register')
									if ($v['company_id'] == 5) echo '<div id="edost_register_delete_'.$k.'" style="position: absolute; margin: 44px 0 0 -5px;"><span class="edost_control_button edost_control_button_low" data-param="id='.$v['id'].';batch_date='.(!empty($v['batch']['date']) ? $v['batch']['date'] : '').'" onclick="edost_window.set(\'register_delete\', this)">'.$control_sign['button']['order_batch_delete_small'].'</span></div>';
//									else echo '<div id="edost_register_delete_'.$k.'" style="position: absolute; margin: 44px 0 0 0;"><span class="edost_control_button edost_control_button_low" onclick="edost_SetControl(\''.$v['id'].'\', \'delete_register\', this)">'.$control_sign['button']['delete_register_small'].'</span></div>';
									else echo '<div id="edost_register_delete_'.$k.'" style="position: absolute; margin: 44px 0 0 0;"><span class="edost_control_button edost_control_button_low" data-param="id='.$v['id'].'" onclick="edost_window.set(\'register_delete2\', this)">'.$control_sign['button']['delete_register_small'].'</span></div>';
//								if ($k2 == 'batch' && $batch_active) echo '<div id="edost_batch_delete_'.$k.'" style="position: absolute; margin: 44px 0 0 -5px;"><span class="edost_control_button edost_control_button_low" onclick="edost_SetControl(\''.$v['id'].'\', \'order_batch_delete\', this)">'.$control_sign['button']['order_batch_delete_small'].'</span></div>';
								if ($k2 == 'batch' && $batch_active) echo '<div id="edost_batch_delete_'.$k.'" style="position: absolute; margin: 44px 0 0 -5px;"><span class="edost_control_button edost_control_button_low" data-param="id='.$v['id'].'" onclick="edost_window.set(\'order_batch_delete\', this)">'.$control_sign['button']['order_batch_delete_small'].'</span></div>';
							}
						}
						else {
							if ($k2 == 'batch') echo '<img id="edost_'.$k2.'_disabled_'.$v['id'].'"'.' style="display: none; position: absolute; z-index: 3; margin: 5px 0 0 -10px; opacity: 0.8;" src="'.$img_path.'/control_'.$v2.'_disabled.png">';
							if (($v['register'] == 0 || !empty($v['complete'])) && empty($v['field_error'])) $s = 'class="edost_'.$k2.'_active edost_register edost_register_'.($v[$k2.'_on'] ? 'on' : 'off').'" data-code="'.$k.'_'.$k2.'"'.($k2 == 'register' ? ' data-control="'.$v['shop_id'].'_'.$v['control_count'].'"' : '').' onclick="edost_Register(\'active\', this)"';
							else $s = 'class="edost_register_disabled3"';
						}
						echo '<img '.$s.' src="'.$img_path.'/control_'.$v2.($v[$k2.'_active'] ? '_complete' : '').'.png">'; ?>
					</div>
<?					} ?>

<?					$a = (!empty($v['package_active']) ? true : false);
					if ($a || !empty($v['option']['service_formatted'])) { ?>
					<div style="padding-right: 10px; padding-top: <?=(!$a || isset($v[status_warning]) && $v[status_warning] == 2 ? '20' : '5')?>px;">
						<?=edost_class::GetPackageString($v, $a ? 'full' : 'option', $a)?>
					</div>
<?					} ?>

<?					if (in_array($f_key, array('register_transfer', 'register_delete', 'warning_pink', 'control')) && !empty($v['batch_code'])) { ?>
					<div style="padding-top: 12px;">
						<?=str_replace(array('%number%', '%date%'), array($v['batch']['number'], $v['batch']['date']), $control_sign['print']['batch'])?>
					</div>
<?					} ?>

<?					if (!empty($v['status']) && $v['status'] != 1 && !($f_key == 'batch_20' && $v['status'] == 20) || !$v['allow_delivery']) {
						$s = '';
						if (!$v['allow_delivery']) $s = $control_sign['error']['no_allow_delivery'];
						else if (!empty($v['status_string'])) $s = $v['status_string'];
						else if (!empty($v['status']) && !empty($control_sign['status'][$v['status']])) $s = $control_sign['status'][$v['status']];
						if ($s != '') echo '<div style="padding-top: 12px; color: #'.($v['status'] == 22 || $v['control_active'] ? '888' : 'F00').'; font-weight: bold;">'.$s.($v['control_active'] ? '' : '!').'</div>';

						if ($v['status'] == 25) echo '<span style="color: #A00; font-weight: bold;">'.$control_sign['api_error'].'</span>';
					} ?>

<?					if (!empty($v['field_error'])) {
						$s = array();
						foreach ($v['field_error'] as $k2 => $v2) $s[] = $control_sign['error']['no_field'].' <b>'.$control_sign['error']['field'][$k2].'</b>';
						echo '<div style="padding-top: 12px; color: #F00;">'.implode('<br>', $s).'</div>';
					}
					echo '<input id="edost_props_'.$k.'" type="hidden" data-param="'.$v['props_param'].'">';
					echo '<div id="edost_register_warning_'.$k.'" style="padding-top: 5px; color: #F00;"></div>'; ?>
				</td>

<?				if (!empty($v['basket_formatted'])) { ?>
				<td width="250" valign="top" align="left" style="font-size: 13px; padding-top: 7px;">
					<div id="edost_shipment_item_<?=$k?>" style="font-size: 11px;">
						<?=$v['basket_formatted']?>
						<br><div style="padding-top: 5px; font-size: 13px;"><span style="color: #888;"><?=$control_sign['basket_total']?>: </span><span style="color: #058;"><b><?=$v['basket_price_formatted']?></b></span></div>
					</div>
<?					if (!empty($v['basket_hint'])) { ?>
					<script type="text/javascript">
						new top.BX.CHint({parent: top.BX('edost_shipment_item_<?=$k?>'), show_timeout: 10, hide_timeout: 100, dx: 2, preventHide: true, min_width: 350, hint: '<?=$v['basket_hint']?>'});
					</script>
<?					} ?>
				</td>
<?				} ?>

				<td align="left" valign="top" style="font-size: 13px; padding-top: 7px;">
					<div style="float: right; text-align: right;">
						<?=(empty($v['package_active']) && !empty($v['package_formatted']) ? '<div style="color: #01627B;">&nbsp;&nbsp;'.$v['package_formatted'].'</div>' : '')?>
						&nbsp;&nbsp;<b><?=$v['order_price_formatted']?></b>
					</div>

<?					if ($cookie['register_status'] == 'Y') { ?>
					<div style="padding-bottom: 4px;"><span id="edost_order_status_<?=$k?>" style="cursor: default; font-weight: bold; background: #888; color: #FFF; padding: 1px 8px;"><?=$v['order_status_formatted']?></span></div>
<?					} ?>

					<?=$v['props']['name'].($develop ? ' ('.$v['register'].')' : '')?><br>
					<span style="color: #888;"><?=$v['props']['location_name']?></span><br>
					<img class="edost_ico edost_ico_small" style="padding: 0 5px 0 0;" src="<?=$ico_path?>/small/<?=$v['tariff']?>.gif" border="0" title="<?=$v['title']?>">
<?
					$s = '';
					if (!empty($v['props']['office'])) $s = '<b style="color: #b600ff;">'.$control_sign['office'].'</b>';
					else if (!in_array($v['tariff'], array(1, 2, 61, 68, 69, 70, 71, 72, 73, 74))) $s = '<b style="color: #ff008b;">'.$control_sign['door'].'</b>';
                    echo $s;

					if ($v['delivery_price_formatted'] > 0) echo ($s != '' ? '&nbsp;&nbsp;' : '').'<span style="color: #555; font-weight: bold;">'.$v['delivery_price_formatted'].'</span>';

					if (!empty($v['cod'])) echo '<br><span style="color: #b59422; font-weight: bold;"> '.$control_sign['cod'].' '.$v['cod_formatted'].'</span>';
?>
				</td>
			</tr></table>
<?
			// локальные кнопки заказа
			$s = '';
			if ($v['register'] == 2 && $v['status_warning'] == 2) $s = 'register_repeat';
			else if ($f_key == 'register_new') $s = 'register';
			else if ($f_key == 'register_complete') $s = 'batch';
			if ($s != '') {
				echo '<div id="edost_register_button_'.$k.'" style="float: right; margin: -22px 0 0 0;"><div style="position: absolute; width: 200px; margin-left: -204px; text-align: right;">';
				if ($s != '') echo '<span id="edost_register_button_'.$k.'_'.$s.'" class="edost_control_button edost_control_button_'.$s.'" onclick="edost_Register(\'button\', \'button_'.$s.'|'.$v['id'].'\')">'.$control_sign['button'][$s].'</span>';
				echo '</div></div>';
			}
?>
			<?=$delimiter?>
<?		} ?>
		</div>
<?
		if (isset($h[1])) echo $h[1];
		else echo '<div style="height: 8px; background: #'.$h['background'].';"></div>';
?>
<?		if ($f_key == $button_key) { ?>
		<div id="register_button">
<?			if (!empty($button['register']) || !empty($button['batch'])) { ?>
			<div id="edost_batch_reset_div" style="display: none; text-align: center; padding: 20px 0 0 0; font-size: 16px;">
				<span class="edost_control_button edost_control_button_low" onclick="var E = BX('edost_batch'); if (E) { E.value = 'new'; E.onchange(); }"><?=$control_sign['batch_select']['reset']?></span>
			</div>
			<div id="edost_batch_div" style="text-align: center; padding: 20px 0 0 0; font-size: 16px;">
<?
				$date = '<div class="adm-input-wrap adm-calendar-inp adm-calendar-first" style="display: inline-block; margin: -4px 0 0 5px;">
					<input type="text" class="adm-input adm-calendar-from edost_package_on" id="edost_batch_date" size="15" value="'.($history['batch_date'] ? $history['batch_date'] : $batch_date[0]).'" style="font-size: 16px;" onfocus="edost_Register(\'input_focus\', this)" onblur="edost_Register(\'input_blur\')">
					<span class="adm-calendar-icon" onclick="BX.calendar({node:this, field:\'edost_batch_date\', bTime: false, bHideTime: false, callback: function() { edost_Register(\'input_start\', \'edost_batch_date\'); }, callback_after: function() { edost_Register(\'update_input\'); }});"></span>
				</div>';

				// включить в сдачу
				$s = '';
				if (in_array($company, array(23))) {
					if (!empty($batch_add)) {
						$s .= '<option value="new" selected>'.$control_sign['batch_select']['new'].'</option>';
						foreach ($batch_add as $k2 => $v2) {
							if (!empty($v2['auto'])) $name = str_replace('%date%', $v2['date'], $control_sign['batch_select']['old2']);
							else $name = str_replace(array('%number%', '%date%', '%company%'), array($v2['number'], $v2['date'], $v2['company'].(!empty($control_sign['batch_type'][$v2['type']]) ? ' '.$control_sign['batch_type'][$v2['type']] : '').(count($v2['order']) > 1 ? ', '.edost_class::draw_string('order', count($v2['order'])) : '').($v2['oversize'] ? ', '.$control_sign['batch_select']['oversize'] : '').($v2['register'] == 5 ? ', '.$control_sign['batch_select']['office'] : '')), $control_sign['batch_select']['old']);
							$s .= '<option value="'.$k2.'" data-order="'.implode(',', $v2['order_add']).'" data-oversize="'.($v2['oversize'] ? '1' : '0').'">'.$name.'</option>';
						}
						$s = '<select id="edost_batch" style="max-width: 300px; font-size: 16px; padding: 1px; margin: -3px 5px 0 5px;" onchange="edost_Register(\'batch_update\', \'set\')">'.$s.'</select>';
					}
					else {
						$s = $control_sign['batch_select']['new'];
					}
					$s = $control_sign['batch_select']['head'].' '.$s.' <span id="edost_batch_date_span">'.$control_sign['batch_select']['to'].' '.$date.'</span>';
				}
				else {
					$s = $control_sign['batch_select']['call'].' '.$date;
				}
				echo '<div style="font-size: 16px;">'.$s.'</div>';

				// вызвать курьера
				if (in_array($company, array(5))) { ?>
					<div class="checkbox" style="padding-top: 15px; font-size: 16px;">
						<input id="edost_call" style="margin: 0;" type="checkbox" onclick="edost_Register('active_all_update')">
						<label for="edost_call"><?=$control_sign['call']['head']?></label>
					</div>
					<div id="edost_call_div" style="padding-top: 15px;">
<?						edost_class::GetRegisterProfile(array('company' => $company, 'main' => true)); ?>
					</div>
<?				} ?>
			</div>
<?			} ?>

			<div id="edost_warning" style="display: none; text-align: center; color: #F00; font-size: 16px; padding: 5px; width: 500px; margin: 20px auto 0 auto; border: 1px solid #F55; background: #FFF0F0;"></div>

			<div style="margin: 20px auto 0 auto; text-align: center;">
<?			foreach ($button as $k2 => $v2) { $v2 = $control_sign['button']['register_data'][$k2]; ?>
				<div style="display: inline-block; margin: 0 10px;">
					<div id="button_<?=$k2?>_disabled" class="" style="position: absolute; background: #FFF; z-index: 2; width: 160px; height: 50px; opacity: 0.7; display: none;"></div>
					<div id="button_<?=$k2?>" class="edost_register_button" style="line-height: 15px; cursor: pointer; background: #<?=$v2['color']?>; width: 160px; height: 50px; color: #FFF;" onclick="edost_Register('button', this.id)" onmouseenter="edost_Register('help', this.id)" onmouseleave="edost_Register('help')">
						<div style="position: absolute; width: 160px; text-align: center; padding-top: <?=$v2['top']?>px; font-size: <?=$v2['size']?>px;"><?=$v2['name']?></div>
					</div>
				</div>
<?			} ?>
			</div>

<?			if (!empty($button)) { ?>
			<div style="text-align: center; color: #888; font-size: 14px; margin-top: 15px;">
				<span id="edost_help"></span>
				<span id="edost_help_default"><?=$control_sign['button']['register_help']?></span>
			</div>
<?			} ?>
		</div>
<?		} ?>

<?		if ($f_key == $reload_key) { ?>
			<div id="register_button2" style="display: none; margin: 20px auto 0 auto; text-align: center;">
				<div id="button_reload" class="edost_register_button" style="display: inline-block; line-height: 15px; cursor: pointer; background: #888; width: 160px; height: 50px; color: #FFF;" onclick="edost_SetParam('register', 'reload_full')">
					<div style="position: absolute; width: 160px; text-align: center; padding-top: 16px; font-size: 18px;"><?=$control_sign['button']['reload']?></div>
				</div>
			</div>
<?		} ?>

<?		if (1 == 2) if ($f_key == $print_key) { if ($f_key == $button_key) echo '<div class="edost_delimiter" style="margin-top: 20px;"></div>' ?>
		<div>
			<div id="edost_print" style="text-align: center; padding: 15px; margin: 15px 0px 0px 0px; font-size: 16px;"></div>
		</div>
<?		} ?>
<?	} ?>

	</div>
</div>

<script type="text/javascript">
	edost_Register('active_all_update');
</script>
<?
}




// страница с документами на печать
if ($mode == 'print') {
	$param['mode'] = preg_replace("/[^a-z]/i", "", $param['mode']);
	foreach ($data as $k => $v) if (empty($v['doc'])) unset($data[$k]);
    $error = (empty($data) || empty($param['mode']) ? 'no_param' : false);

	if ($error === false) {
		foreach ($data as $k => $v) $data[$k] = edost_class::PackRegisterData($v);
//		echo '<br><b>data:</b> <pre style="font-size: 12px">'.print_r($data, true).'</pre>';

		$ar = array();
		$name = 'print';
		$print_no_register = true;
		foreach ($data as $k => $v) {
			$ar[$v['site_id']][] = $v;
			if (!empty($control['data'][$k])) {
				$print_no_register = false;
				$name = (!empty($v['batch_code']) ? $v['batch_code'].'_' : '').(in_array('103', $v['doc']) ? 'f103' : 'order_N'.$v['order_id']);
			}
		}

		if ($print_no_register && $shop_field['name'] == '' && $shop_field['address'] == '' && $shop_field['zip'] == '') $error = 'no_shop_field';

		if ($error === false) {
			$doc = array();
			foreach ($ar as $site_id => $site_shipment) {
				$s = CDeliveryEDOST::GetEdostConfig($site_id);
			    $print_param = $s['browser'].'|'.$cookie['register_print_107'].'|'.$cookie['register_print_label_format'];
				$s['doc'] = edost_class::PackDataArray($site_shipment, array('id', 'doc'));
				$doc[] = $s;
			}

//			$print_param .= '&paper_size='.urlencode($cookie['register_print_label_format']);

			$post = 'type=print&mode='.$param['mode'].'&doc='.edost_class::PackData($doc, array('id', 'ps', 'doc')).'&param='.$print_param.'&data=';
			if ($print_no_register || !empty($develop) || $print_batch !== false) $post .= edost_class::PackData($data, edost_class::$control_key);

			if (!empty($develop)) require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/edost.delivery/admin/doc.php');
			$print = edost_class::RequestData('', '', '', $post, 'print');
//			echo '<br><b>print:</b> <pre style="font-size: 12px">'.print_r($print, true).'</pre>';

			if (is_array($print) && isset($print['error'])) $error = $print['error'];
			else if (strpos($print, 'error=') === 0) {
				$s = explode(';', $print);
				$s = explode('error=', $s[0]);
				$error = (isset($s[1]) ? $s[1] : 4);
			}
			else {
				if (strpos($print, '%PDF') === 0) {
					header('Content-type: application/pdf; name='.$name.'.pdf');
					header('content-disposition: inline; filename='.$name.'.pdf');
				}
				else header('Content-Type: text/html; charset=windows-1251');

				echo $print;
			}
		}
	}

	if ($error) {
		header('Content-Type: text/html; charset=windows-1251');
		echo '<!DOCTYPE html><html>
		<head>
			<meta http-equiv=Content-Type content="text/html; charset=windows-1251">
			<title langs="ru">eDost: '.$GLOBALS['APPLICATION']->ConvertCharset($control_sign['print']['html_head'], LANG_CHARSET, 'windows-1251').'</title>
		</head>
		<body style="margin: 0; padding: 0;">
			<div style="max-width: 1000px; padding: 40px 20px 20px 20px; margin: 0 auto; text-align: center; font-size: 25px; color: #A00; font-family: arial;">'.$GLOBALS['APPLICATION']->ConvertCharset(CDeliveryEDOST::GetEdostError($error, 'control'), LANG_CHARSET, 'windows-1251').'</div>
		</body>
		</html>';
	}

    die();
}




// список заказов на контроле (в админке в меню 'eDost')
if ($mode == 'list') {
	if (!empty($control['data'])) {
		$data = edost_class::GetShipmentData(array_keys($control['data']));
//		echo '<br><b>GetShipmentData:</b><pre style="font-size: 12px">'.print_r($data, true).'</pre>';
		foreach ($control['data'] as $k => $v) if (!empty($data[$k])) $control['data'][$k] += $data[$k]; else unset($control['data'][$k]);
	}

	if (empty($control['control'])) return;

	if (empty($control['data'])) {
		echo '<div style="text-align: center; font-size: 16px;">';
		if (empty($count)) echo (count($buy) > 1 ? $control_sign['control_buy'].'<br>' : '').implode(' | ', $buy);
		else echo '<span style="color: #800;">'.$control_sign['control_no'].'</span><br><br><span style="color: #888; font-size: 14px;">'.$control_sign['control_help'].'</span><br><span style="font-size: 12px;">'.$control_sign['control_help_link'].'</span><br><br>'.$count_string;
		echo '</div>';

		return;
	}

	$data = $control['data'];
	$count = $control['count'];
	$company = '';
	$new_id = array();

	if (empty($param['control'])) $param['control'] = 'main';
	else {
		$s = explode('_', $param['control']);
		if (!empty($s[1]) && in_array($s[0], array('company', 'shipment'))) {
			$param['control'] = $s[0];
			if ($s[0] == 'company') foreach ($data as $v) if ($v['tariff'] == $s[1]) { $company = $v['company']; break; }
			if ($s[0] == 'shipment') $shipment_id = $s[1];
		}
	}
	$main = (in_array($param['control'], array('main', 'company', 'shipment', 'search', 'changed')) ? true : false);

	edost_class::AddPaymentData($data);

	// расчет количества заказов ожидающих зачисления наложки
	$count += array('complete_paid' => 0, 'complete_paid2' => 0);
	foreach ($data as $k => $v) if ($v['status'] == 5 && !empty($v['cod']) && empty($v['cod_paid'])) {
		$count['complete_paid']++;
		$v['complete_paid'] = true;
		$s = ceil((time() - edost_class::time($v['status_date']))/60/60/24);
		$v['day_complete'] = ($s >= 1 ? $s : 1);
		if ($cookie['control_day_complete'] > 1 && $v['day_complete'] > $cookie['control_day_complete']) {
			$v['complete_paid2'] = true;
			$count['complete_paid2']++;
		}
		$data[$k] = $v;
	}
	if ($count['complete_paid2'] != 0 && $count['complete_paid2'] == $count['complete_paid']) unset($count['complete_paid']);

	// количество выполенных заказов + заказы на удаление (доставленные, возвращенные, утерянные, утилизированные)
	$count['complete'] = 0;
	foreach ($data as $k => $v) if ($v['status'] == 5 && empty($v['complete_paid'])) {
		$count['complete']++;
		$data[$k]['complete'] = true;
		$data[$k]['delete'] = true;
	}
	else if (in_array($v['status'], array(7, 8, 9))) $data[$k]['delete'] = true;

//	echo '<br><b>data:</b><pre style="font-size: 12px">'.print_r($data, true).'</pre>';

	// расчет количества заказов с задержкой в доставке и которые лежат в пункте выдачи (по куки из настроек)
	$count += array('delay2' => 0, 'office2' => 0);
	foreach ($data as $k => $v) if ($v['status'] != 5) {
		if ($cookie['control_day_delay'] > 1 && $v['day_delay'] > $cookie['control_day_delay']) $count['delay2']++;
		if ($cookie['control_day_office'] > 1 && $v['day_office'] > $cookie['control_day_office']) $count['office2']++;
	}
//	if ($count['delay2'] != 0 && $count['delay2'] == $count['delay']) unset($count['delay']);
//	if ($count['office2'] != 0 && $count['office2'] == $count['office']) unset($count['office']);

	// список с количеством заказов на контроле и иконками компаний
	$control_head = edost_class::ControlHead($data, array('type' => 'control', 'active' => $param['control'], 'count' => $count, 'company' => $company, 'path' => $ico_path));

	// список с заказами, у которых сегодня изменился статус
	$changed = '';
	if ($cookie['control_changed'] == 'Y') {
		$control = edost_class::ControlChanged();
		if (!empty($control)) {
			// статусы заказов и отгрузок
			$status = array();
			$ar = \Bitrix\Sale\Internals\StatusTable::getList(array(
				'select' => array('ID', 'NAME' => 'Bitrix\Sale\Internals\StatusLangTable:STATUS.NAME'),
				'filter' => array('=Bitrix\Sale\Internals\StatusLangTable:STATUS.LID' => LANGUAGE_ID),
				'order'  => array('SORT'),
			));
			while ($v = $ar->fetch()) $status[$v['ID']] = $v['NAME'];
//			echo '<br><b>status:</b><pre style="font-size: 12px">'.print_r($status, true).'</pre>';

			foreach ($control as $k => $s) if (isset($data[$k])) {
				$data[$k]['changed'] = true;
				$v = $data[$k];
				if (empty($status[$v['order_status']])) continue;
				if ($v['status'] != 5) $color = ''; else $color = (!empty($v['complete_paid']) ? 'purple' : 'green');
				$changed .= '<a style="vertical-align: middle;" href="/bitrix/admin/sale_order_view.php?ID='.$v['order_id'].'&lang='.LANGUAGE_ID.'&edost_link=shipment_'.$k.'#delivery"><b><span style="font-size: 10px;">'.$control_sign['order_prefix'].'</span>'.$v['order_id'].'</b></a> - '.
					'<div style="display: inline-block; padding: 0;" class="edost_control_link" onclick="edost_SetParam(\'control\', \'shipment_'.$k.'\')">'.
						'<img class="edost_ico edost_ico_small" src="'.$ico_path.'/small/'.$v['tariff'].'.gif" border="0" title="'.$v['title'].'">'.
					'</div>'.
					'<span style="vertical-align: middle;"'.($color != '' ? ' class="edost_control_color_'.$color.'"' : '').'>'.$status[$v['order_status']].'</span><br>';
			}
		}
	}

	// поиск по идентификаторам отправлений
	$search = array();
	if ($param['control'] == 'search' && !empty($param['search'])) {
		$s = str_replace(array(',', ';'), array(' ', ' '), $param['search']);
		$s = explode(' ', $s);
		foreach ($s as $v) {
			$v = trim($v);
			if (strlen($v) > 1) $search[] = ToLower($v);
		}
	}

	// список заказов с разделением по группам
	$order_count = 0;
	$list = array_fill_keys(array_keys($control_sign['list_head']), array());
	foreach ($data as $k => $v) {
        $key = '';
		if ($main) {
			if ($param['control'] == 'company' && $v['company'] != $company) continue;
			if ($param['control'] == 'shipment' && $v['id'] != $shipment_id) continue;
			if ($param['control'] == 'changed' && empty($v['changed'])) continue;
			if ($param['control'] == 'search') {
				if (empty($search) || empty($v['tracking_code'])) continue;
				$a = false;
				$s = ToLower($v['tracking_code']);
				foreach ($search as $v2) if (strpos($s, $v2) !== false) $a = true;
				if (!$a) continue;
			}

			if ($v['flag'] == 4) $key = 'new_special';
			else if ($v['flag'] == 2) $key = 'new';
			else if ($v['flag'] == 3) $key = 'special';
            else if (!empty($v['complete_paid'])) $key = 'complete_paid';
			else if ($v['status'] != 5)
				if ($v['status_warning'] == 2) $key = 'warning_red';
				else if ($v['status_warning'] == 3) $key = 'warning_orange';
				else if ($v['day_office'] > $day_office) $key = 'office';
				else if ($v['day_delay'] > $day_delay) $key = 'delay';
				else if ($v['status_warning'] == 1) $key = 'warning_pink';

			if ($key == '' && ($cookie['control_show_total'] == 'Y' || $param['control'] != 'main')) $key = ($v['status'] == 5 ? 'complete' : 'total');
		}
		else if ($param['control'] == 'new') {
			if ($v['flag'] == 4) $key = 'new_special';
			else if ($v['flag'] == 2) $key = 'new';
		}
		else if ($param['control'] == 'special') {
			if (!empty($v['special'])) $key = 'special';
		}
		else if ($param['control'] == 'delay') {
			if ($v['status'] != 5 && $v['day_delay'] >= 1) $key = 'delay';
		}
		else if ($param['control'] == 'delay2') {
			if ($v['status'] != 5 && $v['day_delay'] > $day_delay) $key = 'delay';
		}
		else if ($param['control'] == 'office') {
			if ($v['status'] != 5 && $v['day_office'] >= 1) $key = 'office';
		}
		else if ($param['control'] == 'office2') {
			if ($v['status'] != 5 && $v['day_office'] > $day_office) $key = 'office';
		}
		else if ($param['control'] == 'total') {
			$key = 'total';
		}
		else if ($param['control'] == 'warning_red') {
			if ($v['status_warning'] == 2) $key = 'warning_red';
		}
		else if ($param['control'] == 'warning_orange') {
			if ($v['status_warning'] == 3) $key = 'warning_orange';
		}
		else if ($param['control'] == 'warning_pink') {
			if ($v['status_warning'] == 1) $key = 'warning_pink';
		}
		else if ($param['control'] == 'add') {
			if ($v['status'] == 0) $key = 'add';
		}
		else if ($param['control'] == 'complete') {
			if (!empty($v['complete'])) $key = 'complete';
		}
		else if ($param['control'] == 'complete_paid') {
			if (!empty($v['complete_paid'])) $key = 'complete_paid';
		}
		else if ($param['control'] == 'complete_paid2') {
			if (!empty($v['complete_paid2'])) $key = 'complete_paid';
		}
		if ($key == '') continue;
		$order_count++;
		$list[$key][$k] = $v;
	}

	// объединение 'new_special' и 'new'
	if (!empty($list['new_special'])) {
		$list['new'] = array('special_0' => '') + $list['new_special'] + array('special_1' => !empty($list['new']) ? 'delimiter' : '') + $list['new'];
		unset($list['new_special']);
	}
//	echo '<br><b>list:</b><pre style="font-size: 12px">'.print_r($list, true).'</pre>';

	// стили для заголовков групп
	$head = array(
		'new' => array(
			'<div style="border-width: 2px 0; border-color: #30240d; border-style: solid; padding: 4px; color: #30240d; background: #ffba00; text-align: center; font-size: 15px; font-weight: bold;">'.$control_sign['list_head']['new'].'</div>',
			'background' => 'ffba00',
		),
		'special' => array(
			'<div class="edost_control_head" style="font-size: 15px;"><div class="edost_control_special edost_control_special_big edost_control_special_left"></div>'.$control_sign['head_special'].'<div class="edost_control_special edost_control_special_big edost_control_special_right"></div></div>',
			'<div class="edost_control_head" style="height: 8px;"></div>',
		),
		'warning_red' => array('background' => 'E55'),
		'warning_orange' => array('background' => '91764f'),
		'warning_pink' => array('background' => 'A77'),
		'add' => array('background' => 'bbb'),
		'complete_paid' => array('background' => '9c5e85'),
		'complete' => array('background' => '585'),
		'default' => array('background' => '888'),
	);

?>
<div class="edost">

<?	if ($changed != '') { ?>
	<div id="control_changed_div" style="padding: 0 0 10px 0;">
		<span class="edost_link" style="float: right; opacity: 0.5;" onclick="edost_SetControl(0, 'changed_delete')" style="display: none;"><?=$control_sign['changed_delete']?></span>
		<b><?=$control_sign['changed_head']?>:</b><br>
		<?=$changed?>
		<div style="float: right; bottom: 15px; position: relative;"><span class="edost_link" onclick="edost_SetParam('control', 'changed')"><?=$control_sign['changed_show']?></span></div>
		<div style="height: 15px;"></div>
		<?=$delimiter?>
	</div>
<?	} ?>

	<table width="100%" border="0" cellpadding="4" cellspacing="0"><tr>
		<td width="280" style="vertical-align: top;"><?=$control_head['count_list']?></td>
		<td width="280" style="vertical-align: top;"><?=$control_head['count_list2']?></td>
		<td style="vertical-align: top; text-align: right;">
<?			if ($param['control'] != 'main') { ?>
			<div style="padding-bottom: 5px;"><span class="edost_link" style="font-size: 14px; font-weight: bold;" onclick="edost_SetParam('control', '')"><?=$control_sign['main']?></span></div>
<?			} ?>
			<span style="font-size: 11px;"><?=$count_string?></span>
		</td>
	</tr></table>

<?	if ($control_head['ico'] != '') { ?>
	<div style="padding: 12px 0 5px 0;"><?=$control_head['ico']?></div>
<?	} ?>

	<div style="float: right; text-align: right;">
		<span id="control_setting_show" class="edost_link" style="<?=($setting_show ? 'display: none;' : '')?>" onclick="edost_SetParam('control_setting', 'Y')"><?=$control_sign['setting']['show']?></span>
		<span id="control_setting_hide" class="edost_link" style="<?=(!$setting_show ? 'display: none;' : '')?>" onclick="edost_SetParam('control_setting', 'N')"><?=$control_sign['setting']['hide']?></span>
	</div>
	<div id="control_setting" style="margin-top: 20px; padding: 10px; border: 1px solid #888;<?=(!$setting_show ? ' display: none;' : '')?>">
<?
		$ar = array('delay', 'office', 'complete');
		foreach ($ar as $k => $v) echo '<div style="padding-top: '.($k != 0 ? '5' : '0').'px;">'.str_replace('%data%', $day[$v]['select'], $control_sign['setting']['day_'.$v]).'</div>';

		$ar = array('show_total', 'delete', 'paid', 'changed', 'complete_delay');
		foreach ($ar as $v) { ?>
		<div class="checkbox" style="padding-top: 5px; font-size: 13px;">
			<input id="control_<?=$v?>" style="margin: 0px;" type="checkbox"<?=($cookie['control_'.$v] == 'Y' ? ' checked=""' : '')?> onclick="edost_SetParam('control_<?=$v?>', this.checked)">
			<label for="control_<?=$v?>"><b><?=$control_sign['setting'][$v]?></b></label>
		</div>
<?		} ?>
	</div>

	<div style="height: 20px;"></div>

	<div id="edost_control_data_div">
<?
	if ($param['control'] == 'search' && empty($order_count)) echo '<div style="text-align: center; font-size: 16px; color: #800;">'.$control_sign['no_search'].'</div>';

	$i = 0;
	foreach ($list as $f_key => $f) if (!empty($f)) {
		$h = (isset($head[$f_key]) ? $head[$f_key] : $head['default']);

		if ($i != 0) echo '<div style="height: 30px;"></div>';
		$i++;

		if (isset($h[0])) echo $h[0];
		else echo '<div style="padding: 4px; color: #FFF; background: #'.$h['background'].'; text-align: center; font-size: 15px; font-weight: bold;">'.
				($param['control'] == 'total' ? $control_sign['total'] : $control_sign['list_head'][$f_key]).
				($f_key == 'delay' ? $day['delay']['string'] : '').($f_key == 'office' ? $day['office']['string'] : '').($f_key == 'complete_paid' ? $day['complete']['string'] : '').
			'</div>';

?>
		<div class="edost" style="border: 1px solid #e3e8ea; padding: 10px; background: #eef5f5;">
<?
		$start = true;
		foreach ($f as $k => $v) {
			if ($k == 'special_0') {
				echo $head['special'][0];
				continue;
			}
			if ($k == 'special_1') {
				echo $head['special'][1];
				$start = true;
				if ($v == 'delimiter') echo '<div style="height: 10px;"></div>';
				continue;
			}

			if ($start) echo $delimiter;

			$start = false;
			$new_id[] = $v['id'];
			$props = edost_class::GetProps($v['order_id'], array('no_payment'));
?>
			<table width="100%" border="0" bordercolor="#888" cellpadding="4" cellspacing="0"><tr>
				<td width="60" style="font-size: 15px; text-align: center;" align="center">
					<a href="/bitrix/admin/sale_order_view.php?ID=<?=$v['order_id']?>&lang=<?=LANGUAGE_ID?>&edost_link=shipment_<?=$k?>#delivery"><b><span style="font-size: 10px;"><?=$control_sign['order_prefix']?></span><?=$v['order_id']?></b></a>
				</td>
				<td width="25">
					<input id="edost_shipment_<?=$k?>_value" type="hidden" value="<?=($v['new'] ? '1' : '0')?>">
					<img id="edost_shipment_<?=$k?>_img" class="edost_control_button_new<?=($v['new'] ? '_active' : '')?>" src="<?=$img_path?>/control_new.png" border="0" onclick="edost_SetControl(<?=$k?>, 'auto')">
				</td>
				<td width="450" align="left" style="font-size: 13px;">
<?
					if ($v['status'] == 25) echo '<span style="color: #F00; font-weight: bold;">'.$control_sign['api_error'].'</span>';

					if (!empty($v['special']) && !$main && !in_array($param['control'], array('new', 'special'))) echo $head['special'][0];

					if (!empty($v['day_complete'])) {
						if ($v['day_complete'] > 1) echo '<span style="'.($v['day_complete'] > $cookie['control_day_complete'] ? 'color: #F00;' : '').'">'.$control_sign['day_complete'].' <b>'.edost_class::GetDay($v['day_complete']-1).'</b></span><br>';
					}
					else if (empty($v['delete']) || $cookie['control_complete_delay'] == 'Y') {
						if (empty($v['delete']) && !empty($v['day_office']) && $v['day_office'] > 1) {
							if (in_array($v['tariff'], CDeliveryEDOST::$post_office)) $s = $control_sign['day_office3'];
							else if (!empty($props['office'])) $s = $control_sign['day_office'];
							else $s = $control_sign['day_office2'];
							echo '<span style="'.($v['day_office'] > $cookie['control_day_office'] ? 'color: #F00;' : '').'">'.$s.' <b>'.edost_class::GetDay($v['day_office']-1).'</b></span><br>';
						}
						else if ($v['day_delay'] > 1) echo '<span style="'.($v['day_delay'] > $cookie['control_day_delay'] ? 'color: #F00;' : '').' '.($v['status'] == 5 ? 'opacity: 0.4;' : '').'">'.$control_sign['day_delay'].' <b>'.edost_class::GetDay($v['day_delay']-1).'</b></span><br>';
						else if ($v['day_arrival'] > 0 && empty($v['delete']) && empty($v['day_office']) && empty($v['day_delay'])) echo '<span style="opacity: 0.4;">'.$control_sign['day_arrival'].' <b>'.edost_class::GetDay($v['day_arrival']).'</b></span><br>';
					}

					echo edost_class::GetControlString($v);

//					if (in_array($v['status'], array(13, 14))) echo '<span>'.$control_sign['tracking_head'].': <b>'.$v['tracking_code'].'</b></span>';
?>
				</td>
				<td align="left" style="font-size: 13px;">
					<?=(in_array($param['control'], array('search', 'complete_paid', 'complete_paid2')) || in_array($v['status'], array(13, 14)) ? '<div style="float: right; font-weight: bold;">'.$v['tracking_code'].'</div>' : '')?>
					<?=$props['name']?><br>
					<span style="color: #888;"><?=$props['location_name']?></span><br>
					<img class="edost_ico edost_ico_small" src="<?=$ico_path?>/small/<?=$v['tariff']?>.gif" border="0" title="<?=$v['title']?>">
<?
					if (!empty($props['office'])) echo '<b>'.$control_sign['office'].'</b> ';
					if (!empty($v['cod'])) echo '<span style="color: #b59422; font-weight: bold;">'.$control_sign['cod'].'</span>';

					if (!empty($v['cod']) && empty($v['cod_paid']) && $cookie['control_paid'] == 'Y') echo '<div class="'.($v['status'] == 5 ? 'edost_control_color_purple' : '').'" style="float: right; font-weight: bold; position: relative; top: 6px;">'.$v['payment'][$v['cod']]['sum_formatted'].'</div>';

					$a1 = ($cookie['control_delete'] == 'Y' && !empty($v['delete']) ? true : false);
					$a2 = ($cookie['control_paid'] == 'Y' && $v['status'] == 5 && !empty($v['cod']) ? true : false);
					if ($a1 || $a2) {
					echo '<div>';
					if ($a1) {
						echo '<span class="edost_control_button edost_control_button_add" onclick="edost_SetControl(\''.$v['id'].'\', \'delete\', this)">'.$control_sign['button']['delete'].'</span><span style="display: none; font-weight: bold;">'.$control_sign['button']['delete_ok'].'</span>';
					}
					if ($a2) {
						$a = (empty($v['cod_paid']) ? true : false);
						if ($a) echo '<span class="edost_control_button edost_control_button_paid" style="float: right;" onclick="edost_SetControl(\''.$v['id'].'\', \'paid\', this)">'.$control_sign['button']['paid'].'</span>';
						echo '<span style="'.($a ? 'display: none; ' : '').'float: right; font-weight: bold;">'.$control_sign['button']['paid_ok'].'</span>';
					}
					echo '</div>';
					}
?>
				</td>
			</tr></table>

			<?=$delimiter?>
<?		}

		if ($f_key == 'new' && !empty($new_id)) echo '<input class="adm-btn" type="button" style="height: 20px; margin-top: 10px;" onclick="edost_SetControl(\''.implode(',', $new_id).'\', \'auto_off\')" value="'.$control_sign['button']['old_all'].'">';
?>
		</div>
<?
		if (isset($h[1])) echo $h[1];
		else echo '<div style="height: 8px; background: #'.$h['background'].';"></div>';
	}
?>
	</div>
</div>
<?
}




// загрузка контролируемых заказов с учетом доступа
if (in_array($mode, array('detail', 'user_order', 'sale.personal.order.detail', 'sale.personal.order.list'))) {
	global $USER;
	if (!$USER->IsAuthorized()) return;

	if ($USER->IsAdmin() && $mode == 'detail') {
		$admin_field = true;
		$data = edost_class::Control();
	}
	else {
		$admin_field = false;
		if (empty($param['test'])) $data = edost_class::GetControlShipment($USER->GetID(), 'user');
		else {
			$data = array('data' => array(1 => array('id' => 1, 'flag' => 1, 'tariff' => 1, 'tracking_code' => '1234567890', 'status' => 4, 'status_warning' => 0, 'status_string' => '', 'status_info' => '', 'status_date' => '01.02.2016', 'status_time' => '', 'day_arrival' => 0, 'day_delay' => 0, 'day_office' => 0, 'new' => '', 'special' => '', 'profile' => 117, 'title' => 'EMS', 'insurance' => false, 'company' => 'EMS', 'name' => '', 'delivery_id' => 1, 'order_id' => intval($param['test']), 'order_number' => intval($param['test']), 'site_id' => 's1', 'order_status' => 'F', 'allow_delivery' => true, 'deducted' => false, 'canceled' => false, 'shop_id' => 1, 'control_count' => 25)));
			if (!empty($param['user_order_max'])) for ($i = 2; $i <= $param['user_order_max']; $i++) { $data['data'][$i] = $data['data'][1]; $data['data'][$i]['order_number']++; $data['data'][$i]['status'] = 5; }
		}
	}

	$data = (!empty($data['data']) ? $data['data'] : array());
	if (!empty($data)) foreach ($data as $k => $v) if ($v['status'] == 0) unset($data[$k]);
	if (empty($data) && !in_array($mode, array('sale.personal.order.detail', 'sale.personal.order.list'))) return;
}




// детальная информация по контролируемому заказу
if ($mode == 'detail') {
	if (empty($data[$shipment_id])) return;

	// добавление полей для администратора (номер отправления, телефон покупателя и адрес доставки)
	$field = array();
	if ($admin_field) {
		$props = edost_class::GetProps($data[$shipment_id]['id'], array('shipment', 'no_payment', 'office_link', 'field'));
		if (!empty($props['field'])) $field = $props['field'];
//		echo '<br><b>GetProps:</b><pre>'.print_r($props, true).'</pre><br>';
	}

	$data = edost_class::GetControlDetail($shipment_id);
//	echo '<br><b>GetControlDetail:</b><pre>'.print_r($data, true).'</pre><br>';

	$s = '';
	if (!empty($field)) $data['field'] = (!empty($data['field']) ? array_merge($field, $data['field']) : $field);
	if (!empty($data['field'])) {
		$s .= '<table class="edost" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 15px;">';
		foreach ($data['field'] as $v) {
			if (empty($v['admin'])) $v['name'] = str_replace(array(' '.$control_sign['meter'].'3', '('.$control_sign['meter'].'3'), array(' '.$control_sign['meter'].'<sup>3</sup>', '('.$control_sign['meter'].'<sup>3</sup>'), $v['name']);
			if (!empty($v['bold'])) $v['value'] = '<b>'.$v['value'].'</b>';
			$s .= '<tr><td class="edost_control_field_name" style="width: 200px; padding: 2px; color: #'.(!empty($v['admin']) ? '826e00' : '888').';">'.$v['name'].':</td>'.
				'<td class="edost_control_field_value" style="width: auto; padding: 2px;">'.$v['value'].'</td></tr>';
		}
		$s .= '</table>';
	}
	$s .= (!empty($data['data']) ? edost_class::GetControlString($data['data']) : '<div style="padding-bottom: 10px;">'.$control_sign['detail_no'].'</div>');
	if ($s != '') $s .= '<div style="text-align: right;"><span class="edost_link edost_control_detail" style="opacity: 0.4;" onclick="edost_ShowDetail('.$shipment_id.', \'hide\')">'.$control_sign['detail_hide'].'</span></div>';
	echo $s;

	return;
}




// список с заказами покупателя (в шапке сайта)
if ($mode == 'user_order') {
	foreach ($data as $k => $v) $data[$k]['status_short'] = edost_class::GetControlString($v, !empty($param['user_order_string_length']) ? $param['user_order_string_length'] : 40);
	$arResult['shipment'] = $data;
}




// обработка страницы "детальная информация по заказу" и "мои заказы"
if ($mode == 'sale.personal.order.detail' || $mode == 'sale.personal.order.list') {
//	echo '<br><b>result:</b><pre>'.print_r($result, true).'</pre><br>';

	$order_id = false;
	$ico = (empty($param['ico']) || $param['ico'] == 'Y' ? true : false);
	$anchor = (empty($param['anchor']) || $param['anchor'] == 'Y' ? true : false);
	$zip_required = true;
	$detail = ($mode == 'sale.personal.order.detail' ? true : false);
	$company_tariff = edost_class::GetCompanyTariff(false, true);

	$orders = array();
	if ($detail && !empty($result['SHIPMENT'])) $orders = array(array('SHIPMENT' => $result['SHIPMENT']));
	if (!$detail && !empty($result['ORDERS'])) $orders = $result['ORDERS'];

	// название службы доставки и данные контроля
	if (!empty($orders)) foreach ($orders as $o_key => $o) if (!empty($o['SHIPMENT'])) foreach ($o['SHIPMENT'] as $k => $v) if (!empty($v['DELIVERY_ID'])) {
		$profile = CDeliveryEDOST::GetEdostProfile($v['DELIVERY_ID'], false);
//		echo '<br><b>profile:</b><pre>'.print_r($profile, true).'</pre><br>';
		if ($profile === false) continue;

		$zip_required = (CDeliveryEDOST::GetPropRequired($v['DELIVERY_ID'], 'zip') === 'Y' ? true : false);

		$id = false;
		if (!empty($v['ID'])) $id = $v['ID'];
		else if (!empty($v['ACCOUNT_NUMBER'])) foreach ($data as $v2) if ($v2['account_number'] == $v['ACCOUNT_NUMBER']) { $id = $v2['id']; break; }

		if (!empty($v['ORDER_ID'])) $order_id = $v['ORDER_ID'];

		$site_id = false;
		if (!empty($result['LID'])) $site_id = $result['LID'];
		else if (!empty($result['SITE_ID'])) $site_id = $result['SITE_ID'];
		else if (!empty($o['ORDER']['LID'])) $site_id = $o['ORDER']['LID'];

		$c = CDeliveryEDOST::GetEdostConfig($site_id, $config, true);
		$template_ico = (!empty($c['template_ico']) ? $c['template_ico'] : 'T2');
		if ($template_ico == 'C') $company_ico = edost_class::GetCompanyIco(isset($company_tariff[$profile['tariff']]) ? $company_tariff[$profile['tariff']] : 0, $profile['tariff']);

		$s = '';
		if ($detail && $anchor) $s .= '<a id="edost_shipment_'.$id.'" name="shipment_'.$id.'"></a>';
		if (!$ico) $s .= $profile['title'];
		else {
			if ($template_ico == 'C') $s .= '<img class="edost_ico edost_ico_company_small2" src="'.$ico_path.'/company/'.$company_ico.'.gif" border="0">';
			else if ($template_ico == 'T') $s .= '<img class="edost_ico edost_ico_small" src="'.$ico_path.'/small/'.$profile['tariff'].'.gif" border="0">';
			$s .= '<span class="edost_name" style="color: #555;">'.$profile['title'].'</span>';
		}

		if ($detail) {
			$v['DELIVERY_NAME'] = $v['DELIVERY']['NAME'] = $s;
			if (!empty($result['DELIVERY']['NAME'])) $result['DELIVERY']['NAME'] = $s; // поддержка старого шаблона
		}
		else {
			$result['INFO']['DELIVERY'][$v['DELIVERY_ID']]['NAME'] = $s;
		}

		if ($template_ico == 'C') $v['DELIVERY']['SRC_LOGOTIP'] = $ico_path.'/company/'.$company_ico.'.gif';
		else if ($template_ico == 'T') $v['DELIVERY']['SRC_LOGOTIP'] = $ico_path.'/big/'.$profile['tariff'].'.gif';

		if ($id !== false && isset($data[$id])) {
			$s = edost_class::GetControlString($data[$id]);

			if ($s) {
				$v['TRACKING_STATUS'] = $s;
				$v['TRACKING_STATUS_HEAD'] = $control_sign['tracking_status_head'];
			}
			else if (isset($v['TRACKING_STATUS'])) unset($v['TRACKING_STATUS']);

			if (isset($v['TRACKING_DESCRIPTION'])) unset($v['TRACKING_DESCRIPTION']);
		}

		if ($detail) $result['SHIPMENT'][$k] = $v;
		else $result['ORDERS'][$o_key]['SHIPMENT'][$k] = $v;
	}

	// форматирование полей
	if ($detail && !empty($result['ORDER_PROPS'])) {
		$props = false;
		foreach ($result['ORDER_PROPS'] as $k => $v) {
			if (!empty($v['ORDER_ID'])) $order_id = $v['ORDER_ID'];
			else if (!empty($result['ID'])) $order_id = $result['ID'];
			if (!empty($order_id)) $props = edost_class::GetProps($order_id, array('no_payment'));
			break;
		}
		if (!empty($props)) {
			$office	= (!empty($props['office']) ? true : false);

			// форматирование адреса
			foreach ($result['ORDER_PROPS'] as $k => $v) if ($v['CODE'] == 'ADDRESS') {
				$s = $props['address_formatted'];
				if (!$office) $s .= ($s != '' ? '<br>' : '' ).$props['location_name'];
				$result['ORDER_PROPS'][$k]['VALUE'] = $s;
			}

			// удаление ненужных полей
			foreach ($result['ORDER_PROPS'] as $k => $v)
				if ($v['CODE'] == 'LOCATION' ||
					$v['CODE'] == 'CITY' ||
					$office && in_array($v['CODE'], array('ZIP', 'METRO')) ||
					$v['CODE'] == 'ZIP_AUTO' ||
					$v['CODE'] == 'ZIP' && (substr($v['VALUE'], -1) == '.' || !$zip_required) ||
					$v['CODE'] == 'METRO' && empty($v['VALUE'])) unset($result['ORDER_PROPS'][$k]);

			// поля битроника
			if (!empty($result['PROPS_BY_GROUP'])) foreach ($result['PROPS_BY_GROUP'] as $k => $v) foreach ($v as $k2 => $v2) {
				if ($v2['CODE'] == 'ADDRESS') $result['PROPS_BY_GROUP'][$k][$k2]['VALUE'] = $s;
				if ($v2['CODE'] == 'LOCATION' || in_array($v2['CODE'], array('ZIP', 'ZIP_AUTO')) && ($office || $v2['CODE'] == 'ZIP_AUTO' && $v2['VALUE'] != 'Y')) unset($result['PROPS_BY_GROUP'][$k][$k2]);
			}
		}
	}

	return $result;
}




$arResult += array(
	'mode' => $mode,
	'param' => $param,
);

$this->IncludeComponentTemplate();

?>