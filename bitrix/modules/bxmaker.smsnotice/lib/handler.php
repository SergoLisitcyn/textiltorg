<?
	namespace Bxmaker\SmsNotice;


	use Bitrix\Main\Application;
	use \Bitrix\Main\Entity;
	use Bitrix\Main\Localization\Loc;
	use Bitrix\Main\Type\DateTime;
	use Bitrix\Main\Loader;


	class Handler
	{

		static private $module_id = 'bxmaker.smsnotice';

		/**
		 * @var \Bitrix\Main\Config\Option
		 */
		static private $oOption = null; // параметры модуля
		static private $siteID = null; // индификатор сайта текущего для использвоания парамтеров подключения и определения шаблона смс
		static private $arTmpData = array(); // временные данные для передачи между обработчиками

		static private function init()
		{
			if (is_null(self::$oOption)) self::$oOption = new \Bitrix\Main\Config\Option();
			if (is_null(self::$siteID)) self::$siteID = Manager::getInstance()->getCurrentSiteId();
		}

		/**
		 * Получение паарметра модуля
		 *
		 * @param        $name
		 * @param string $default_value
		 *
		 * @return string
		 * @throws \Bitrix\Main\ArgumentNullException
		 */
		static private function getParam($name, $default_value = '')
		{

			self::init();
			return self::$oOption->get(self::$module_id, $name, $default_value, self::$siteID);
		}


		//===========================================================================================
		// main
		//===========================================================================================

		public function main_OnBeforeUserAdd($arParams)
		{
			if (isset($arParams['LOGIN']) && isset($arParams['PASSWORD'])) {
				self::$arTmpData['main_user_add'][$arParams['LOGIN']] = $arParams['PASSWORD'];
			}
		}

		/**
		 * После добавления/регистрации пользвоателя, главный модуль битрикса
		 *
		 * @param $arFields
		 */
		public function main_OnAfterUserAdd($arFields)
		{
			if (isset($arFields['ID']) && intval($arFields['ID'])) {
				// проверяем указано ли поле в котором ранится номер телефона
				$field_phone = trim(self::getParam('HANDLER.USER_PHONE_FIELD', null));
				$template_type = trim(self::getParam('HANDLER.USER_ADD_TEMPLATE_TYPE', null));

				if ($field_phone && $template_type) {
					//получаем все необходимые поля
					$arTemplateFields = array();
					$oUser = new \CUser();
					$dbr = $oUser->GetList($by = 'ID', $order = 'asc', array('ID' => intval($arFields['ID'])), array('SELECT' => array('UF_*')));
					if ($arTemplateFields = $dbr->Fetch()) {
						// заменим поля на незашифрованные
						if (isset(self::$arTmpData['main_user_add'][$arFields['LOGIN']])) {
							$arTemplateFields['PASSWORD'] = self::$arTmpData['main_user_add'][$arFields['LOGIN']];
						}

						$arTemplateFields['PHONE'] = (array_key_exists($field_phone, $arTemplateFields) ? $arTemplateFields[$field_phone] : $field_phone);

						// отправка
						$result = Manager::getInstance()->sendTemplate($template_type, $arTemplateFields);
						if (!$result->isSuccess()) {
							//                        echo '<pre>';
							//                        print_r($result->getErrorMessages());
							//                        echo '</pre>';
						} else {
							$errors = (array)$result->getMore('errors');
							foreach ($errors as $error) {
								//                            echo '<pre>';
								//                            print_r($error);
								//                            echo '</pre>';
							}
						}
					}
				}
			}
		}


		public function main_OnBeforeUserUpdate($arParams)
		{
			if (isset($arParams['ID'])) {
				self::$arTmpData['main_user_update'][$arParams['ID']] = $arParams['PASSWORD'];
			}
		}

		/**
		 * Обновление пароля
		 *
		 * @param $arFields
		 */
		public function main_OnAfterUserUpdate($arFields)
		{
			if (isset($arFields['ID']) && isset($arFields['PASSWORD'])) {
				// проверяем указано ли поле в котором ранится номер телефона
				$field_phone = trim(self::getParam('HANDLER.USER_PHONE_FIELD', null));
				$template_type = trim(self::getParam('HANDLER.USER_UPDATE_TEMPLATE_TYPE', null));

				if ($field_phone && $template_type) {
					//получаем все необходимые поля
					$arTemplateFields = array();
					$oUser = new \CUser();
					$dbr = $oUser->GetList($by = 'ID', $order = 'asc', array('ID' => intval($arFields['ID'])), array('SELECT' => array('UF_*')));
					if ($arTemplateFields = $dbr->Fetch()) {

						// заменим поля на незашифрованные
						if (isset(self::$arTmpData['main_user_update'][$arFields['ID']])) {
							$arTemplateFields['PASSWORD'] = self::$arTmpData['main_user_update'][$arFields['ID']];
						}

						$arTemplateFields['PHONE'] = (array_key_exists($field_phone, $arTemplateFields) ? $arTemplateFields[$field_phone] : $field_phone);


						// отправка
						$result = Manager::getInstance()->sendTemplate($template_type, $arTemplateFields);
						if (!$result->isSuccess()) {
							//                        echo '<pre>';
							//                        print_r($result->getErrorMessages());
							//                        echo '</pre>';
						} else {
							$errors = (array)$result->getMore('errors');
							foreach ($errors as $error) {
								//                            echo '<pre>';
								//                            print_r($error);
								//                            echo '</pre>';
							}
						}

					}
				}
			}
		}


		//===========================================================================================
		// bxmaker.authuserphone
		//===========================================================================================
		public function bxmaker_authuserphone_onSendCode($arFields)
		{
			return Manager::getInstance()->sendTemplate('BXMAKER_AUTHUSERPHONE_SENDCODE', $arFields);
		}

		public function bxmaker_authuserphone_onUserAdd($arFields)
		{
			return Manager::getInstance()->sendTemplate('BXMAKER_AUTHUSERPHONE_USERADD', $arFields);
		}

		public function bxmaker_authuserphone_onUserChangePassword($arFields)
		{
			return Manager::getInstance()->sendTemplate('BXMAKER_AUTHUSERPHONE_USERCHANGEPASSWORD', $arFields);
		}

		//===========================================================================================
		// sale Интернет-магазин стандартный
		//===========================================================================================

		//Перед добавлением заказа
		public function sale_OnBeforeOrderAdd($arFields)
		{
			// во время добавления заказа многократно происходит обноление заказа
			// чтобы не пложить сотню смс,  подвязываемся к событию отправления письма о новом заказе
			self::$arTmpData['sale_order_add_start'] = true;
		}

		//Добавлени заказа
		public function sale_OnOrderAdd($ID, $arFields)
		{
			unset(self::$arTmpData['sale_order_add_start']);
		}

		//Добавлени заказа
		public function sale_OnSaleOrderSaved($order)
		{
			/** @var  \Bitrix\Sale\Order $order */

			//self::sale_onEventOrderAdd($order->getId());
		}

		private function sale_onEventOrderAdd($ORDER_ID)
		{
			if (strlen($ORDER_ID) > 0) {

				// не отправляем одно и тоже повторно
				if (isset(self::$arTmpData['sale_onEventOrderAdd_' . $ORDER_ID])) {
					return;
				}
				self::$arTmpData['sale_onEventOrderAdd_' . $ORDER_ID] = true;

				$arOrderData = self::getOrderData($ORDER_ID);

				$persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '');

				// проверяем указано ли поле в котором ранится номер телефона
				$field_phone = trim(self::getParam('HANDLER.PERSON_TYPE_' . $persone_type, null));
				$template_type = trim(self::getParam('HANDLER.ORDER_NEW_TEMPLATE_TYPE_' . $persone_type, null));

				if ($field_phone && $template_type) {

					if (isset($arOrderData['PROPERTY_VALUE_' . $field_phone])) {
						// подготовим поле
						$arOrderData['PHONE'] = $arOrderData['PROPERTY_VALUE_' . $field_phone];

						Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
					}
				}
			}
		}


		// перед обновлением заказа
		public function sale_OnBeforeOrderUpdate($ID, $arFields)
		{
			if (isset(self::$arTmpData['sale_order_add_start']) && self::$arTmpData['sale_order_add_start'] === true) {
				return;
			}
		}

		// Обновление заказ
		public function sale_OnOrderUpdate($ID, $arFields)
		{
			if (isset(self::$arTmpData['sale_order_add_start']) && self::$arTmpData['sale_order_add_start'] === true) {
				return;
			}
		}

		// заказ оплачен
		public function sale_OnSalePayOrder($ORDER_ID, $val)
		{
			if ($val == 'Y') {
				// не отправляем одно и тоже повторно
				if (isset(self::$arTmpData['sale_OnSalePayOrder' . $ORDER_ID])) {
					return;
				}
				self::$arTmpData['sale_OnSalePayOrder' . $ORDER_ID] = true;

				$arOrderData = self::getOrderData($ORDER_ID);

				$persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '');

				// проверяем указано ли поле в котором ранится номер телефона
				$field_phone = trim(self::getParam('HANDLER.PERSON_TYPE_' . $persone_type, null));
				$template_type = trim(self::getParam('HANDLER.ORDER_PAY_TEMPLATE_TYPE_' . $persone_type, null));

				if ($field_phone && $template_type) {

					if (isset($arOrderData['PROPERTY_VALUE_' . $field_phone])) {
						// подготовим поле
						$arOrderData['PHONE'] = $arOrderData['PROPERTY_VALUE_' . $field_phone];

						Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
					}
				}
			}
		}

		// заказ оплачен
		public function sale_OnSaleCancelOrder($ORDER_ID, $val)
		{

			if ($val == 'Y') {
				// не отправляем одно и тоже повторно
				if (isset(self::$arTmpData['sale_OnSaleCancelOrder' . $ORDER_ID])) {
					return;
				}
				self::$arTmpData['sale_OnSaleCancelOrder' . $ORDER_ID] = true;

				$arOrderData = self::getOrderData($ORDER_ID);

				$persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '');

				// проверяем указано ли поле в котором ранится номер телефона
				$field_phone = trim(self::getParam('HANDLER.PERSON_TYPE_' . $persone_type, null));
				$template_type = trim(self::getParam('HANDLER.ORDER_CANCELED_TEMPLATE_TYPE_' . $persone_type, null));


				if ($field_phone && $template_type) {

					if (isset($arOrderData['PROPERTY_VALUE_' . $field_phone])) {
						// подготовим поле
						$arOrderData['PHONE'] = $arOrderData['PROPERTY_VALUE_' . $field_phone];

						Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
					}
				}
			}
		}

		// заказ смена статуса
		public function sale_OnSaleStatusOrder($ORDER_ID, $STATUS_ID)
		{
			$STATUS_ID = (string)$STATUS_ID;

			// не отправляем одно и тоже повторно
			if (isset(self::$arTmpData['sale_OnSaleStatusOrder_' . $STATUS_ID . '_' . $ORDER_ID])) {
				return;
			}
			self::$arTmpData['sale_OnSaleStatusOrder_' . $STATUS_ID . '_' . $ORDER_ID] = true;

			$arOrderData = self::getOrderData($ORDER_ID);

			$persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '');

			// проверяем указано ли поле в котором ранится номер телефона
			$field_phone = trim(self::getParam('HANDLER.PERSON_TYPE_' . $persone_type, null));
			$template_type = trim(self::getParam('HANDLER.ORDER_STATUS_' . $STATUS_ID . '_TEMPLATE_TYPE_' . $persone_type, null));

			if ($field_phone && $template_type) {

				if (isset($arOrderData['PROPERTY_VALUE_' . $field_phone])) {
					// подготовим поле
					$arOrderData['PHONE'] = $arOrderData['PROPERTY_VALUE_' . $field_phone];

					Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
				}
			}

		}



		/**
		 * Возвращает массив данных для передачи в шаблон связанный с заказами стандартного модуля -  интернет-магазин
		 *
		 * @param $ORDER_ID
		 *
		 * @return array|bool|null
		 * @throws \Bitrix\Main\LoaderException
		 */
		private static function getOrderDataD7($ORDER_ID)
		{
			if (!Loader::includeModule('sale')) {
				return null;
			}

			// есо используются не реальные номера
			if(strlen(trim(\Bitrix\Main\Config\Option::get('sale', 'account_number_template', ''))) > 0)
			{
				if ($orderDat = \Bitrix\Sale\Order::getList(array(
					'filter' => array(
						'ACCOUNT_NUMBER' => $ORDER_ID
					)
				))->fetch())
				{
					$ORDER_ID = $orderDat['ID'];
				}
			}


			/** @var  \Bitrix\Sale\Order $order */
			$order = \Bitrix\Sale\Order::load($ORDER_ID);
			$arOrder = $order->getFieldValues();

			$arOrder['ORDER_ID'] = $arOrder['ACCOUNT_NUMBER'];

			// проверяем указано ли поле в котором ранится номер телефона
			$presonTypeId = (isset($arOrder['PERSON_TYPE_ID']) ? $arOrder['PERSON_TYPE_ID'] : '1');
			$phoneField = trim(self::getParam('HANDLER.PERSON_TYPE_' . $presonTypeId, 'PHONE'));
			$phoneDefault = null;
			$phone = null;


			//Свойства
			$collections = $order->getPropertyCollection();
			$arCollections = $collections->getArray();
			foreach ($arCollections['properties'] as $prop) {
				if ($prop['IS_PHONE'] == 'Y') {
					$phoneDefault = (isset($prop['VALUE'][0]) ? $prop['VALUE'][0] : null);
				}
				if ($prop['CODE'] == $phoneField) {
					$phone = (isset($prop['VALUE'][0]) ? $prop['VALUE'][0] : null);
				}

				$arOrder['PROPERTY_VALUE_' . $prop['CODE']] =  (isset($prop['VALUE'][0]) ? $prop['VALUE'][0] : null);
			}

			if (is_null($phone) || !Manager::getInstance()->isValidPhone($phone)) {
				if (!is_null($phoneDefault) && Manager::getInstance()->isValidPhone($phoneDefault)) {
					$phone = $phoneDefault;
				}
			}

			$arOrder['PHONE'] = Manager::getInstance()->getPreparePhone($phone);


			//доставка
			$arTrackNumber = array();
			$arOrder['DELIVERY_NAME'] = '';
			$shipmentCollection = $order->getShipmentCollection();
			foreach ($shipmentCollection as $shipment) {
				/** @var \Bitrix\Sale\Shipment $shipment */
				$arOrder['DELIVERY_NAME'] = $shipment->getDeliveryName();

				if (strlen(trim($track = $shipment->getField('TRACKING_NUMBER'))) > 0) {
					$arTrackNumber[] = $track;
				}
			}
			array_unique($arTrackNumber);
			$arOrder['TRACKING_NUMBER'] = implode(', ', $arTrackNumber);


			//оплата
			$arOrder['PAY_SYSTEM_NAME'] = '';
			$paymentCollection = $order->getPaymentCollection();
			/** @var \Bitrix\Sale\Payment $payment */
			foreach ($paymentCollection as $payment) {
				$arOrder['PAY_SYSTEM_NAME'] = $payment->getPaymentSystemName();
			}


			//Статусы заказов
			//        $orderStatus = \Bitrix\Sale\OrderStatus::getInitialStatus();
			//        $arStatus = \Bitrix\Sale\OrderStatus::getAllowedUserStatuses($arOrder['USER_ID'], $orderStatus);
			//        $arOrder['ORDER_STATUS_NAME'] = (isset($arStatus[$arOrder['STATUS_ID']]) ? $arStatus[$arOrder['STATUS_ID']] : $arOrder['STATUS_ID']);

			$orderStatus = \Bitrix\Sale\OrderStatus::getInitialStatus();
			$arStatus = \Bitrix\Sale\OrderStatus::getAllowedGroupStatuses(1,$orderStatus);
			$arOrder['ORDER_STATUS_NAME'] = (isset($arStatus[$arOrder['STATUS_ID']]) ? $arStatus[$arOrder['STATUS_ID']] : $arOrder['STATUS_ID']);


			// стоимость заказа

			$n = (intval(Manager::getInstance()->getParam('HANDLER.ORDER_PRICE_ROUND', '4')) >= 0 ? intval(Manager::getInstance()->getParam('HANDLER.ORDER_PRICE_ROUND', '4')) : 2);
			$arOrder['PRICE'] = number_format($arOrder['PRICE'], $n, '.', ' ');
			$arOrder['PRICE_DELIVERY'] = number_format($arOrder['PRICE_DELIVERY'], $n, '.', ' ');
			$arOrder['DISCOUNT_VALUE'] = number_format($arOrder['DISCOUNT_VALUE'], $n, '.', ' ');

			return $arOrder;
		}


		/**
		 * Возвращает массив данных для передачи в шаблон связанный с заказами стандартного модуля -  интернет-магазин
		 *
		 * @param $ORDER_ID
		 *
		 * @return array|bool|null
		 * @throws \Bitrix\Main\LoaderException
		 */
		private static function getOrderData($ORDER_ID)
		{
			if (!Loader::includeModule('sale')) {
				return null;
			}

			if(class_exists('\Bitrix\Sale\OrderStatus') && class_exists('\Bitrix\Sale\Order'))
			{
				return self::getOrderDataD7($ORDER_ID);
			}


			$oOrder = new \CSaleOrder();
			$oDelivery = new \CSaleDelivery();
			$oDeliveryHandler = new \CSaleDeliveryHandler();
			$oPaySystem = new \CSalePaySystem();
			$oOrderPropValue = new \CSaleOrderPropsValue();
			$oOrderStatus = new \CSaleStatus();



			// есо используются не реальные номера
			if (strlen(trim(\Bitrix\Main\Config\Option::get('sale', 'account_number_template', ''))) > 0) {
				if ($orderDat = $oOrder->GetList(array(), array(
					'ACCOUNT_NUMBER' => $ORDER_ID
				))->fetch()
				) {
					$ORDER_ID = $orderDat['ID'];
				}
			}


			if ($arOrder = $oOrder->GetByID($ORDER_ID)) {

				$arOrder['ORDER_ID'] = $arOrder['ID'];

				//-------------------------------------------
				// определяем службу доставки
				$arOrder['DELIVERY_NAME'] = '';
				if (count($arDeliveryTmp = explode(':', $arOrder['DELIVERY_ID'])) == 2) {
					// автоматизирвоанная
					$bDeliveryFindStop = false;
					$dbrDelivery = $oDeliveryHandler->GetList(
						array(
							'SORT' => 'ASC',
							'NAME' => 'ASC'
						),
						array(
							'SID' => $arDeliveryTmp[0]
						)
					);
					while (($arDelivery = $dbrDelivery->Fetch()) && !$bDeliveryFindStop) {
						foreach ($arDelivery['PROFILES'] as $deliveryProfileKey => $arDeliveryProfile) {
							if ($deliveryProfileKey == $arDeliveryTmp[1]) {
								$bDeliveryFindStop = true;
								$arOrder['DELIVERY_NAME'] = $arDelivery['NAME'];
							}
						}
					}
				} else {
					$arDelivery = $oDelivery->GetByID(intval($arOrder['DELIVERY_ID']));
					if (isset($arDelivery['NAME'])) {
						$arOrder['DELIVERY_NAME'] = $arDelivery["NAME"];
					}
				}

				// -----------------------------------------
				// Система оплаты
				$arPaySystem = $oPaySystem->GetByID(intval($arOrder['PAY_SYSTEM_ID']), $arOrder['PERSON_TYPE_ID']);
				$arOrder['PAY_SYSTEM_NAME'] = (isset($arPaySystem['PSA_NAME']) ? $arPaySystem['PSA_NAME'] : '');


				//-------------------------------------------
				// Свойства заказа
				$dbrOrderPropValue = $oOrderPropValue->GetList(array(), array('ORDER_ID' => $arOrder['ID']));
				while ($arOrderPropValue = $dbrOrderPropValue->Fetch()) {
					$arOrder['PROPERTY_VALUE_' . $arOrderPropValue['CODE']] = $arOrderPropValue['VALUE'];
				}

				// Статусы заказов
				$arOrderStatus = $oOrderStatus->GetByID($arOrder['STATUS_ID']);
				$arOrder['ORDER_STATUS_NAME'] = (isset($arOrderStatus['NAME']) ? $arOrderStatus['NAME'] : $arOrder['STATUS_ID']);

				// стоимость заказа
				Manager::getInstance()->getParam('HANDLER.ORDER_PRICE_ROUND', '4');


				$n                         = (intval(Manager::getInstance()->getParam('HANDLER.ORDER_PRICE_ROUND', '4')) >= 0 ? intval(Manager::getInstance()->getParam('HANDLER.ORDER_PRICE_ROUND', '4')) : 2);
				$arOrder['PRICE']          = number_format($arOrder['PRICE'], $n, '.', ' ');
				$arOrder['PRICE_DELIVERY'] = number_format($arOrder['PRICE_DELIVERY'], $n, '.', ' ');
				$arOrder['DISCOUNT_VALUE'] = number_format($arOrder['DISCOUNT_VALUE'], $n, '.', ' ');


				return $arOrder;
			}

			return null;
		}




		public function main_OnBeforeEventAdd($event, $lid, $arFields)
		{
			$site_id = $lid;

			switch ($event) {
				// Добавлен заказ
				case 'SALE_NEW_ORDER': {
					self::sale_onEventOrderAdd(isset($arFields['ORDER_ID']) ? $arFields['ORDER_ID'] : '');
					break;
				}
			}
		}


		/**
		 * Отправка трекера отправления
		 *
		 */
		public function sale_OnShipmentTrackingNumberChange($shipment)
		{
			\Bitrix\Main\Loader::includeModule('sale');

			$changedFields = $shipment->getFields()->getChangedValues();
			$orderId = $shipment->getFields()->get('ORDER_ID');
			$trackId = $changedFields['TRACKING_NUMBER'];

			$arOrder = Handler::getOrderDataD7($orderId);
			$arOrder['TRACKING_NUMBER'] = $trackId;

			$persone_type = (isset($arOrder['PERSON_TYPE_ID']) ? $arOrder['PERSON_TYPE_ID'] : '1');
			$template_type = trim(self::getParam('HANDLER.ORDER_TRACKING_NUMBER_TEMPLATE_TYPE_' . $persone_type, null));

			if (Manager::getInstance()->isValidPhone($arOrder['PHONE']) && $template_type) {

				// подготовим поле
				$arOrder['PHONE'] = $arOrder['PHONE'];
				$arOrder['ORDER_ID'] = $arOrder['ACCOUNT_NUMBER'];

				//отправка
				Manager::getInstance()->sendTemplate($template_type, $arOrder, $arOrder['LID']);

				//AddMessage2Log(' bxmaker.smsnotice  '. $template_type );
			} else {
				AddMessage2Log(' bxmaker.smsnotice ERROR  ' . __METHOD__ . ' valid phone - ' . (Manager::getInstance()->isValidPhone($arOrder['PHONE']) ? 'Y' : 'N') . ', template_type - ' . $template_type);
			}
		}

		/**
		 * Смена статуса
		 *
		 */
		public function sale_OnSaleStatusOrderChange($order, $value, $value_old)
		{
			\Bitrix\Main\Loader::includeModule('sale');


			// не отправляем одно и тоже повторно
			if (isset(self::$arTmpData['sale_OnSaleStatusOrder_' . $value . '_' . $order->getId()])) {
				return;
			}
			self::$arTmpData['sale_OnSaleStatusOrder_' . $value . '_' . $order->getId()] = true;


			/** @var \Bitrix\Sale\Order $order */

			if ($value != $value_old) {
				$arOrder = Handler::getOrderDataD7($order->getId());

				$persone_type = (isset($arOrder['PERSON_TYPE_ID']) ? $arOrder['PERSON_TYPE_ID'] : '1');
				$template_type = trim(self::getParam('HANDLER.ORDER_STATUS_' . $value . '_TEMPLATE_TYPE_' . $persone_type, null));

				if (Manager::getInstance()->isValidPhone($arOrder['PHONE']) && $template_type) {

					//отправка
					Manager::getInstance()->sendTemplate($template_type, $arOrder, $arOrder['LID']);
				} else {
					AddMessage2Log(' bxmaker.smsnotice ERROR  ' . __METHOD__ . ' valid phone - ' . (Manager::getInstance()->isValidPhone($arOrder['PHONE']) ? 'Y' : 'N') . ', template_type - ' . $template_type);
				}
			}
		}

		/**
		 * Отмена заказа
		 *
		 */
		public function sale_OnSaleOrderCanceled($order)
		{
			\Bitrix\Main\Loader::includeModule('sale');

			// не отправляем одно и тоже повторно
			if (isset(self::$arTmpData['sale_OnSaleCancelOrder' . $order->getId()])) {
				return;
			}
			self::$arTmpData['sale_OnSaleCancelOrder' . $order->getId()] = true;

			/** @var \Bitrix\Sale\Order $order */
			$arOrder = Handler::getOrderDataD7($order->getId());

			$persone_type = (isset($arOrder['PERSON_TYPE_ID']) ? $arOrder['PERSON_TYPE_ID'] : '1');
			$template_type = trim(self::getParam('HANDLER.ORDER_CANCELED_TEMPLATE_TYPE_' . $persone_type, null));

			if (Manager::getInstance()->isValidPhone($arOrder['PHONE']) && $template_type) {

				//отправка
				Manager::getInstance()->sendTemplate($template_type, $arOrder, $arOrder['LID']);
			} else {
				AddMessage2Log(' bxmaker.smsnotice ERROR  ' . __METHOD__ . ' valid phone - ' . (Manager::getInstance()->isValidPhone($arOrder['PHONE']) ? 'Y' : 'N') . ', template_type - ' . $template_type);
			}
		}

		/**
		 * Оплата заказа
		 *
		 */
		public function sale_OnSaleOrderPaid($order)
		{
			\Bitrix\Main\Loader::includeModule('sale');
			/** @var \Bitrix\Sale\Order $order */
			if($order->isPaid())
			{

				// не отправляем одно и тоже повторно
				if (isset(self::$arTmpData['sale_OnSalePayOrder' . $order->getId()])) {
					return;
				}
				self::$arTmpData['sale_OnSalePayOrder' . $order->getId()] = true;


				/** @var \Bitrix\Sale\Order $order */
				$arOrder = Handler::getOrderDataD7($order->getId());

				$persone_type = (isset($arOrder['PERSON_TYPE_ID']) ? $arOrder['PERSON_TYPE_ID'] : '1');
				$template_type = trim(self::getParam('HANDLER.ORDER_PAY_TEMPLATE_TYPE_' . $persone_type, null));

				if (Manager::getInstance()->isValidPhone($arOrder['PHONE']) && $template_type) {

					//отправка
					Manager::getInstance()->sendTemplate($template_type, $arOrder, $arOrder['LID']);
				} else {
					AddMessage2Log(' bxmaker.smsnotice ERROR  ' . __METHOD__ . ' valid phone - ' . (Manager::getInstance()->isValidPhone($arOrder['PHONE']) ? 'Y' : 'N') . ', template_type - ' . $template_type);
				}
			}


		}



	}