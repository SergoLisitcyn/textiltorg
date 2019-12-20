<?
	namespace Bxmaker\SmsNotice;


	const SMS_STATUS_SENT = 1; //отправлено
	const SMS_STATUS_DELIVERED = 2; //доставлено
	const SMS_STATUS_ERROR = 3; //ошибка

	const ERROR_SERVICE_INITIALIZATION = 1001; // не удалось проинициализировать, подключить, не найдне
	const ERROR_SERVICE_RESPONSE = 1002; // неизвестный ответ сревиса
	const ERROR_SERVICE_CUSTOM = 1003; // произвольная ошибка иная

	const ERROR_TEMPLATE_NOT_FOUND = 2002; //не наден шаблон
	const ERROR_INVALID_PHONE = 2003; // не верно указан номер телефона

	const ERROR_EVENT = 3001; // ошики связанные с событиями

	use Bitrix\Main\Application;
	use \Bitrix\Main\Entity;
	use Bitrix\Main\Localization\Loc;
	use Bitrix\Main\Type\DateTime;
	use Bitrix\Main\Loader;

	Loc::loadMessages(__FILE__);

	//include_once(dirname(__FILE__).'/../include.php');


	Class ManagerTable extends Entity\DataManager
	{

		public static
		function getFilePath()
		{
			return __FILE__;
		}

		public static
		function getTableName()
		{
			return 'bxmaker_smsnotice_list';
		}

		public static
		function getMap()
		{
			return array(
				new Entity\IntegerField('ID', array(
					'primary'      => true,
					'autocomplete' => true
				)),
				new Entity\StringField('PHONE', array(
					'required' => true
				)),
				new Entity\TextField('TEXT', array(
					'required' => true
				)),
				new Entity\TextField('COMMENT'), //  напрмиер режим отладки, содержимое ошибки
				new Entity\IntegerField('STATUS', array(
					'required'  => true,
					'validator' => function () {
						return array(
							new Entity\Validator\Range(0, 99)
						);
					}
				)),
				new Entity\DatetimeField('CREATED', array(
					'required' => true
				)),
				new Entity\IntegerField('TYPE_ID'),
				new Entity\IntegerField('SERVICE_ID', array(
					'required' => true
				)),
				new Entity\StringField('SITE_ID', array(
					'required'  => true,
					'validator' => function () {
						return array(
							new Entity\Validator\Range(2, 2)
						);
					}
				)),
				new Entity\TextField('PARAMS', array( // параметры для конкретных сервисов, чтобы хранить ккие то данные например для mainsms.ru - messageId
					'required'                => false,
					'save_data_modification'  => function () {
						return array(
							function ($value) {
								return serialize($value);
							}
						);
					},
					'fetch_data_modification' => function () {
						return array(
							function ($value) {
								return unserialize($value);
							}
						);
					}
				)),
				new Entity\ReferenceField('TYPE', '\Bxmaker\SmsNotice\Template\Type', array('=ref.ID' => 'this.TYPE_ID'), array('type_join' => 'left')),
				new Entity\ExpressionField('CNT', 'COUNT(ID)')
			);
		}
	}

	final Class Manager extends \Bxmaker_SmsNotice_Manager_Demo
	{

		static private $instance = null;

		protected $module_id = 'bxmaker.smsnotice';
		protected $bDebug = false; // режим отладки
		protected $arSmsTemplate = array(); // массивы шаблонов, чтобы не запрашивать повторно
		protected $arServices = array(); // массив проинициалиированных объектов сервисов
		protected $arServiceCurrent = array(); // данные по текущему подклчюенному сревису для отправки
		protected $siteID = null;
		protected $arSiteData = array(); // данные по текущему сайту


		protected $oOption = null; //параметры
		/**
		 * @var Service
		 */
		protected $oService = null; // объект активного проинициализированногос ервиса, через который ведется отправка
		protected $oManagerTable = null; // объект для работы с таблицей, в окторой хранится история отправленных смс
		protected $oTemplate = null; // объект для работы с шаблонами
		protected $bDemoExpired = false;


		private function __construct()
		{
			$this->oManagerTable = new ManagerTable();
			$this->oOption = new \Bitrix\Main\Config\Option();

			// отладка
			$this->bDebug = ($this->getParam('HANDLER.DEBUG', 'N') == 'Y');

		}

		private function __clone()
		{

		}

		/**
		 * @return Manager
		 */
		static public function getInstance()
		{
			if (is_null(self::$instance)) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}


		/*  Добавление скриптов и стилей на страницы модуля */
		public function addAdminPageCssJs()
		{
			$path = getLocalPath('modules/' . $this->module_id);
			if (file_exists($_SERVER["DOCUMENT_ROOT"] . $path . '/admin/css/style.css')) {
				echo '<style type="text/css" >' . file_get_contents($_SERVER["DOCUMENT_ROOT"] . $path . '/admin/css/style.css') . '</style>';
			}
			if (file_exists($_SERVER["DOCUMENT_ROOT"] . $path . '/admin/js/script.js')) {
				echo '<script type="text/javascript" >' . file_get_contents($_SERVER["DOCUMENT_ROOT"] . $path . '/admin/js/script.js') . '</script>';
			}
		}


		/**
		 * Если включен режим отладки
		 * @return bool
		 */
		public function isDebug()
		{
			return $this->bDebug;
		}

		/**
		 * Возвращает параметры используемого сервиса для отправки
		 *
		 * @param null $param
		 *
		 * @return array|null
		 */
		public function getServiceParam($param = null)
		{
			$resInitService = $this->initService();

			if (is_null($param)) return $this->arServiceCurrent;
			else return (isset($this->arServiceCurrent[$param]) ? $this->arServiceCurrent[$param] : null);
		}


		/**
		 * Инициализация конкретного сервиса
		 *
		 * @param null|true|int $id      ( true - инициализация активного сервиса, int - конкретного сервиса, null - использование последнего )
		 * @param null          $site_id идентификатор сайта
		 *
		 * @return Result
		 * @throws \Bitrix\Main\ArgumentException
		 */
		final protected function initService($id = null, $site_id = null)
		{
			if (is_null($site_id)) {
				$site_id = $this->getCurrentSiteId();
			}

			$result = $this->_getObjResult();

			if ($this->bDemoExpired) {
				$result->setError($this->_getObjError($this->getMsg('MANAGER.MODULE_DEMO_EXPIRED'), $this->_getConst('ERROR_SERVICE_INITIALIZATION')));
				return $result;
			}

			// если сервис был ранее проинициализирвоан
			if (isset($this->arServices[$site_id][$id])) {
				if ($this->arServices[$site_id][$id] === false) {
					$result->setError($this->_getObjError($this->getMsg('MANAGER.ERROR_SERVICE_INITIALIZATION'), $this->_getConst('ERROR_SERVICE_INITIALIZATION'), array(
						'SERVICE_ID' => $id,
						'SITE_ID'    => $site_id
					)));
				}
				$this->arServiceCurrent = $this->arServices[$site_id][$id]['DATA'];
				$this->oService = $this->arServices[$site_id][$id]['OBJ'];
				return $result;
			} else {
				// инициализируем
				$arFilter = array(
					'SITE_ID' => $site_id
				);
				if (is_null($id)) // подключение активногос сервиса
				{
					$arFilter['ACTIVE'] = true;
				} else {
					$arFilter['ID'] = intval($id);
				}

				$oServiceTable = new ServiceTable();
				$dbr = $oServiceTable->getList(array(
					'filter' => $arFilter,
					'limit'  => 1
				));
				if ($arServiceParams = $dbr->fetch()) {

					// парамтеры получены
					// подключаем файл
					$oService = new Service();
					$arService = $oService->getArray();
					if (isset($arService[$arServiceParams['CODE']]) && file_exists($arService[$arServiceParams['CODE']]['FILE'])) {
						include_once $arService[$arServiceParams['CODE']]['FILE'];
						$class_name = '\Bxmaker\SmsNotice\Service\\' . $arServiceParams['CODE'];
						if (class_exists($class_name)) {

							$this->arServiceCurrent = $arServiceParams;
							$this->oService = new $class_name($arServiceParams['PARAMS']);

							// сохраняем временно
							$this->arServices[$arServiceParams['SITE_ID']][$id]['DATA'] = $this->arServiceCurrent;
							$this->arServices[$arServiceParams['SITE_ID']][$id]['OBJ'] = $this->oService;


							return $result;
						}
					}
				}
			}

			//сброс
			$this->arServices[$site_id][$id] = false;
			$this->arServiceCurrent = array();

			$result->setError($this->_getObjError($this->getMsg('MANAGER.ERROR_SERVICE_INITIALIZATION'), $this->_getConst('ERROR_SERVICE_INITIALIZATION')));
			return $result;
		}


		public function send($phone, $text, $site_id = null)
		{
			// подклчюение сервиса активного
			$resInitService = $this->initService(null, $site_id);
			if (!$resInitService->isSuccess()) {
				return $resInitService;
			}

			if (is_null($site_id)) {
				$site_id = $this->getCurrentSiteId();
			}

			$arFields = array(
				'phone'   => $phone,
				'text'    => $text,
				'site_id' => $site_id
			);

			$event = new \Bitrix\Main\Event($this->module_id, "OnBeforeSend", array($arFields));
			$event->send();

			foreach ($event->getResults() as $eventResult) {
				$arParameters = $eventResult->getParameters();

				switch ($eventResult->getType()) {
					case \Bitrix\Main\EventResult::ERROR: {

						$msg = (isset($arParameters['error_msg']) ? $arParameters['error_msg'] : $this->getMsg('MANAGER.EVENT_ONBEFORE_SEND_ERROR_EVENTRESULT'));
						return $this->_getObjResult($this->_getObjError($msg, $this->_getConst('ERROR_EVENT'), array()));

						break;
					}
					case \Bitrix\Main\EventResult::SUCCESS: {
						// успешно
						break;
					}
					case \Bitrix\Main\EventResult::UNDEFINED: {
						/* обработчик вернул неизвестно что вместо объекта класса \Bitrix\Main\EventResult
					   его результат по прежнему доступен через getParameters
					   */
						break;
					}
				}
			}

			if (!isset($arParameters[0]['phone']) || !isset($arParameters[0]['text'])) {
				$this->_getObjResult($this->_getObjError($this->getMsg('MANAGER.EVENT_ONBEFORE_SEND_EMPTY_PARAMS'), $this->_getConst('ERROR_EVENT')));
			} else {
				$phone = $arParameters[0]['phone'];
				$text = $arParameters[0]['text'];
			}

			return $this->sendSms($phone, $text, $site_id);
		}


		public function sendTemplate($template, $arFields = array(), $site_id = null)
		{
			// подклчюение сервиса активного
			$resInitService = $this->initService(null, $site_id);
			if (!$resInitService->isSuccess()) {
				return $resInitService;
			}

			if (is_null($site_id)) {
				$site_id = $this->getCurrentSiteId();
			}

			// получаем шаблоны если не сделали эо ранее
			if (!isset($this->arSmsTemplate[$site_id][$template])) {
				if (is_null($this->oTemplate)) {
					$this->oTemplate = new TemplateTable();
				}

				$dbrTemplate = $this->oTemplate->getList(array(
					'filter' => array(
						'ACTIVE'    => true,
						'TYPE.CODE' => $template,
						'SITE.SID'  => $site_id
					)
				));
				while ($arTemplate = $dbrTemplate->fetch()) {
					$this->arSmsTemplate[$site_id][$template][] = $arTemplate;
				}
			}


			// проверка наличия шаблонов
			if (!isset($this->arSmsTemplate[$site_id][$template]) || empty($this->arSmsTemplate[$site_id][$template])) {
				return $this->_getObjResult($this->_getObjError($this->getMsg('MANAGER.NOT_FOUNT_TEMPLATES'), $this->_getConst('ERROR_TEMPLATE_NOT_FOUND')));
			}


			$event = new \Bitrix\Main\Event($this->module_id, "OnBeforeSendTemplate", array($template, $arFields));
			$event->send();

			foreach ($event->getResults() as $eventResult) {
				$arParameters = $eventResult->getParameters();

				if (!isset($arParameters[1])) {
					$this->_getObjResult($this->_getObjError($this->getMsg('MANAGER.EVENT_ONBEFORE_SEND_TEMPLATE_EMPTY_PARAMS'), $this->_getConst('ERROR_EVENT')));
				}

				$arFields = $arParameters[1];

				switch ($eventResult->getType()) {
					case \Bitrix\Main\EventResult::ERROR: {

						$msg = (isset($arParameters['error_msg']) ? $arParameters['error_msg'] : $this->getMsg('MANAGER.EVENT_ONBEFORE_SEND_TEMPLATE_ERROR_EVENTRESULT'));
						return $this->_getObjResult($this->_getObjError($msg, $this->_getConst('ERROR_EVENT'), array()));

						break;
					}
					case \Bitrix\Main\EventResult::SUCCESS: {
						// успешно
						break;
					}
					case \Bitrix\Main\EventResult::UNDEFINED: {
						/* обработчик вернул неизвестно что вместо объекта класса \Bitrix\Main\EventResult
					   его результат по прежнему доступен через getParameters
					   */
						break;
					}
				}
			}


			// обходим шаблоны и отправляем
			$arSentResult = array(
				'count'   => 0,
				'errors'  => null,
				'results' => array()
			);

			foreach ($this->arSmsTemplate[$site_id][$template] as $arTemplate) {
				$arSentResult['count']++;


				$this->prepareTemplate($arTemplate, $arFields, $site_id);

				$res = $this->sendSms($arTemplate['PHONE'], $arTemplate['TEXT'], $site_id, $arTemplate['TYPE_ID']);


				if ($res->isSuccess()) {
					$arSentResult['results'][] = $res;
				} else {
					$arSentResult['errors'] = (array)$arSentResult['errors'];
					$errors = $res->getErrors();
					if (isset($errors[0])) {
						$arSentResult['errors'][] = $errors[0];
					}
				}
			}

			$result = $this->_getObjResult();
			$result->setMore('count', $arSentResult['count']);
			$result->setMore('errors', $arSentResult['errors']);
			$result->setMore('results', $arSentResult['results']);

			return $result;
		}

		public function getBalance($site_id = null)
		{
			// подклчюение сервиса активного
			$resInitService = $this->initService(null, $site_id);
			if (!$resInitService->isSuccess()) {
				return $resInitService;
			}
			return $this->oService->getBalance();
		}


		/**
		 * Проверка, кодировка сайта UTF-8 или нет
		 * @return bool
		 */
		final static public function isUTF()
		{
			return (defined('BX_UTF') && BX_UTF === true);
			//        return (strtoupper(LANG_CHARSET) == 'UTF-8' || mb_strtoupper(LANG_CHARSET) == 'UTF-8');
		}

		static public function isWin()
		{
			return !self::isUTF();
		}




		/**
		 * Возвращает идентификатор текущего сайта
		 * @return null|string
		 */
		public function getCurrentSiteId()
		{
			// если админка то определяем сайт поумолчанию или  по текущему домену
			if (defined('ADMIN_SECTION')) {

				if (!$this->siteID) {
					$host = Application::getInstance()->getContext()->getRequest()->getHttpHost();
					$host = preg_replace('/(:[\d]+)/', '', $host);

					//ищем по домену
					$oSite = new \CSite();
					$dbr = $oSite->GetList($by = 'sort', $order = 'asc', array(
						'ACTIVE' => 'Y',
						'DOMAIN' => $host
					));
					if ($ar = $dbr->Fetch()) {
						$this->siteID = $ar['LID'];
					} else {
						// сайт поумолчанию
						$dbr = $oSite->GetList($by = 'sort', $order = 'asc', array(
							'DEFAULT' => 'Y'
						));
						if ($ar = $dbr->Fetch()) {
							$this->siteID = $ar['LID'];
						}
					}
				}
				return $this->siteID;
			}

			return SITE_ID;
		}

		/**
		 * Возвращает парамтеры сайта, для подставновки этих значений в шаблоны сообщений - SERVER_NAME напримре
		 *
		 * @param null $site_id
		 *
		 * @return mixed
		 */
		public function getSiteData($site_id = null)
		{
			if (is_null($site_id)) {
				$site_id = $this->getCurrentSiteId();
			}
			if (!isset($this->arSiteData[$site_id])) {
				$oSite = new \CSite();
				$dbr = $oSite->GetByID($site_id);
				if ($ar = $dbr->Fetch()) {
					$this->arSiteData[$site_id] = $ar;
				} else {
					$this->arSiteData[$site_id] = false;
				}
			}
			return $this->arSiteData[$site_id];
		}


		/**
		 * Используется для смены статуса извне системы,  некоторые сервисы сами дополнительно оповещают ресурсы о статусе доставки смс
		 * @return Result
		 * @throws \Bitrix\Main\ArgumentException
		 */
		public function notice($site_id = null)
		{

			$req = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
			$oManagerTable = new ManagerTable();
			$result = $this->_getObjResult();

			if (is_null($site_id)) {
				$site_id = $this->getCurrentSiteId();
			}

			$dbr = $oManagerTable->getList(array(
				'filter' => array(
					'ID' => (int)$req->getQuery('smsId')
				)
			));
			if ($ar = $dbr->fetch()) {

				// подклчюение сервиса активного
				$resInitService = $this->initService($ar['SERVICE_ID'], $site_id);
				if (!$resInitService->isSuccess()) {
					return $resInitService;
				}


				// получаем результат понятный для работы
				/**
				 * @var Result
				 */
				$resNoticeCheck = $this->oService->notice();

				// елси все успешно
				if ($resNoticeCheck->isSuccess()) {

					switch ($resNoticeCheck->getResult()) {
						case $this->_getConst('SMS_STATUS_DELIVERED'): {
							// обновляем статус у сообщения
							$oManagerTable->update($ar['ID'], array(
								'STATUS'  => $this->_getConst('SMS_STATUS_DELIVERED'),
								'COMMENT' => ''
							));
							break;
						}
						case $this->_getConst('SMS_STATUS_SENT'): {
							// обновляем статус у сообщения
							$oManagerTable->update($ar['ID'], array(
								'STATUS'  => $this->_getConst('SMS_STATUS_SENT'),
								'COMMENT' => ''
							));
						}
					}
					$result->setResult(true);

				} else {


					foreach ($resNoticeCheck->getErrors() as $error) {
						$result->setError($error);
					}

					$oManagerTable->update($ar['ID'], array(
						'STATUS'  => $this->_getConst('SMS_STATUS_ERROR'),
						'COMMENT' => $this->getCommentFromErrors($resNoticeCheck->getErrors())
					));
				}

			}

			return $result;
		}




		/**
		 * Получение значения одного из параметров модуля
		 *
		 * @param      $name
		 * @param null $default_value
		 *
		 * @return string
		 * @throws \Bitrix\Main\ArgumentNullException
		 */
		public function getParam($name, $default_value = null)
		{
			return $this->oOption->get($this->module_id, $name, $default_value, $this->getCurrentSiteId());
		}

		/**
		 * Возврат языкозависимое сообщение об ошибке или успешности
		 *
		 * @param      $name
		 * @param null $arReplace
		 *
		 * @return mixed|string
		 */
		protected function getMsg($name, $arReplace = null)
		{
			return GetMessage($this->module_id . '.' . $name, $arReplace);
		}

		protected function _getConst($name)
		{
			return (defined('Bxmaker\SmsNotice\\' . $name) ? constant('Bxmaker\SmsNotice\\' . $name) : null);
		}

		protected function _getNewObj($name)
		{
			return new $name();
		}

		protected function _getObj($name)
		{
			return (isset($this->$name) ? $this->$name : null);
		}

		protected function _getObjError($message, $code = 0)
		{
			return new Error($message, $code);
		}

		protected function _getObjResult($result = null){
			return new Result($result);
		}


	}