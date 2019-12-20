<?
	use \Bitrix\Main\Localization\Loc as Loc;

	Loc::loadMessages(__FILE__);


	class bxmaker_smsnotice extends CModule
	{

		var $MODULE_ID = "bxmaker.smsnotice";
		var $PARTNER_NAME = "BXmaker";
		var $PARTNER_URI = "http://bxmaker.ru/";

		var $MODULE_VERSION;
		var $MODULE_VERSION_DATE;
		var $MODULE_NAME;
		var $MODULE_DESCRIPTION;
		var $PARTNER_ID = "bxmaker";

		/**
		 * ������ ������������, ��� ��������� ������� ������ �������
		 * @var array
		 */
		private $arModuleDependences = array(

			// ������������
			//�����������
			array('main', 'OnBeforeUserAdd', '\Bxmaker\SmsNotice\Handler', 'main_OnBeforeUserAdd', 100000),
			array('main', 'OnAfterUserAdd', '\Bxmaker\SmsNotice\Handler', 'main_OnAfterUserAdd', 100000),

			//��������� ������
			array('main', 'OnBeforeUserUpdate', '\Bxmaker\SmsNotice\Handler', 'main_OnBeforeUserUpdate', 100000),
			array('main', 'OnAfterUserUpdate', '\Bxmaker\SmsNotice\Handler', 'main_OnAfterUserUpdate', 100000),

			// ������
			array('main', 'OnBeforeEventAdd', '\Bxmaker\SmsNotice\Handler', 'main_OnBeforeEventAdd', 100000),

			//D7
			array('sale', 'OnShipmentTrackingNumberChange', '\Bxmaker\SmsNotice\Handler', 'sale_OnShipmentTrackingNumberChange', 100000),
			array('sale', 'OnSaleStatusOrderChange', '\Bxmaker\SmsNotice\Handler', 'sale_OnSaleStatusOrderChange', 100000),
			array('sale', 'OnSaleOrderCanceled', '\Bxmaker\SmsNotice\Handler', 'sale_OnSaleOrderCanceled', 100000),
			array('sale', 'OnSaleOrderPaid', '\Bxmaker\SmsNotice\Handler', 'sale_OnSaleOrderPaid', 100000),
			array('sale', 'OnSaleOrderSaved', '\Bxmaker\SmsNotice\Handler', 'sale_OnSaleOrderSaved', 100000),


			//old
			array('sale', 'OnBeforeOrderAdd', '\Bxmaker\SmsNotice\Handler', 'sale_OnBeforeOrderAdd', 100000),
			array('sale', 'OnOrderAdd', '\Bxmaker\SmsNotice\Handler', 'sale_OnOrderAdd', 100000),
			array('sale', 'OnBeforeOrderUpdate', '\Bxmaker\SmsNotice\Handler', 'sale_OnBeforeOrderUpdate', 100000),
			array('sale', 'OnOrderUpdate', '\Bxmaker\SmsNotice\Handler', 'sale_OnOrderUpdate', 100000),
			array('sale', 'OnSalePayOrder', '\Bxmaker\SmsNotice\Handler', 'sale_OnSalePayOrder', 100000),
			array('sale', 'OnSaleCancelOrder', '\Bxmaker\SmsNotice\Handler', 'sale_OnSaleCancelOrder', 100000),
			array('sale', 'OnSaleStatusOrder', '\Bxmaker\SmsNotice\Handler', 'sale_OnSaleStatusOrder', 100000),

		);


		public function bxmaker_smsnotice()
		{
			include(__DIR__ . '/version.php');

			$this->MODULE_DIR = \Bitrix\Main\Loader::getLocal('modules/bxmaker.smsnotice');

			$this->isLocal = !!strpos($this->MODULE_DIR, '/local/modules/');

			$this->MODULE_NAME = Loc::getMessage($this->MODULE_ID . '_MODULE_NAME');
			$this->MODULE_DESCRIPTION = Loc::getMessage($this->MODULE_ID . '_MODULE_DESCRIPTION');
			$this->PARTNER_NAME = GetMessage('bxmaker.smsnotice_PARTNER_NAME');
			$this->PARTNER_URI = GetMessage('bxmaker.smsnotice_PARTNER_URI');
			$this->MODULE_VERSION = empty($arModuleVersion['VERSION']) ? '' : $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = empty($arModuleVersion['VERSION_DATE']) ? '' : $arModuleVersion['VERSION_DATE'];


			// ������� bxmaker.authuserphone
			$arMore = array(

				array('bxmaker.authuserphone', 'onSendCode', '\Bxmaker\SmsNotice\Handler', 'bxmaker_authuserphone_onSendCode'),
				array('bxmaker.authuserphone', 'onUserAdd', '\Bxmaker\SmsNotice\Handler', 'bxmaker_authuserphone_onUserAdd'),
				array('bxmaker.authuserphone', 'onUserChangePassword', '\Bxmaker\SmsNotice\Handler', 'bxmaker_authuserphone_onUserChangePassword')
			);
			if (IsModuleInstalled('bxmaker.authuserphone')) {
				$this->arModuleDependences = array_merge($this->arModuleDependences, $arMore);
			}


		}

		function DoInstall()
		{
			RegisterModule($this->MODULE_ID);
			$this->InstallDB();
			$this->InstallFiles();
			$this->InstallAgents();
			$this->InstallDependences();
			$this->InstallTemplates();

			return true;
		}

		function DoUninstall()
		{
			$this->UnInstallDB();
			$this->UnInstallFiles();
			$this->UnInstallAgents();
			$this->UnInstallDependences();
			$this->UnInstallTemplates();

			COption::RemoveOption($this->MODULE_ID);
			UnRegisterModule($this->MODULE_ID);

			return true;
		}

		/**
		 * ���������� � ���� ����������� ������ ��� ������ ������
		 * @return bool
		 */
		function InstallDB()
		{
			global $DB, $DBType, $APPLICATION;

			//         Database tables creation
			$DB->RunSQLBatch(dirname(__FILE__) . "/db/mysql/install.sql");

			return true;
		}


		/**
		 * �������� ������ ������
		 * @return bool|void
		 */
		function UnInstallDB()
		{
			global $DB, $DBType, $APPLICATION;

			$DB->RunSQLBatch(dirname(__FILE__) . "/db/mysql/uninstall.sql");

			return true;
		}


		/**
		 * ����������� ������
		 * @return bool|void
		 */
		function InstallFiles($arParams = array())
		{
			// �������� �����
			if ($this->isLocal) {
				CopyDirFiles($this->MODULE_DIR . "/install/components/", $_SERVER["DOCUMENT_ROOT"] . "/local/components/", true, true);
			} else {
				CopyDirFiles($this->MODULE_DIR . "/install/components/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/", true, true);
			}

			if (file_exists($path = $this->MODULE_DIR . '/admin')) {
				if ($dir = opendir($path)) {
					while (false !== $item = readdir($dir)) {
						if (in_array($item, array('.', '..', 'menu.php')))
							continue;

						if (!file_exists($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item))
							file_put_contents($file, '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/' . ($this->isLocal ? 'local' : 'bitrix') . '/modules/' . $this->MODULE_ID . '/admin/' . $item . '");?' . '>');
					}
				}
			}
			return true;
		}

		/**
		 * �������� ������
		 * @return bool|void
		 */
		function UnInstallFiles()
		{

			if (is_dir($this->MODULE_DIR . "/install/components/" . $this->PARTNER_ID . "/")) {
				$d = dir($this->MODULE_DIR . "/install/components/" . $this->PARTNER_ID . "/");
				while ($entry = $d->read()) {
					if ($entry == '.' || $entry == '..') continue;

					DeleteDirFilesEx('/local/components/' . $this->PARTNER_ID . '/' . $entry . '/');
					DeleteDirFilesEx('/local/components/' . $this->PARTNER_ID . '/' . $entry . '/');
				}
				$d->close();
			}

			if (file_exists($path = $this->MODULE_DIR . '/admin')) {
				if ($dir = opendir($path)) {
					while (false !== $item = readdir($dir)) {
						if (in_array($item, array('.', '..', 'menu.php')))
							continue;

						if (file_exists($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item))
							unlink($file);
					}
				}
			}
			return true;
		}

		/**
		 * ��������� �������
		 */
		public function InstallAgents()
		{
			$oAgent = new CAgent();
			$oAgent->AddAgent('\Bxmaker\SmsNotice\Agent::cleanSmsHistory();', $this->MODULE_ID, 'N', 3600);
			$oAgent->AddAgent('\Bxmaker\SmsNotice\Agent::checkSmsStatus();', $this->MODULE_ID, 'N', 50);
		}

		/**
		 * �������� �������
		 */
		public function UnInstallAgents()
		{
			$oAgent = new CAgent();
			$oAgent->RemoveModuleAgents($this->MODULE_ID);
		}


		/**
		 * ��������� ������������ �������
		 */
		public function InstallDependences()
		{
			foreach ($this->arModuleDependences as $item) {
				if (count($item) < 4) continue;

				RegisterModuleDependences($item[0], $item[1], $this->MODULE_ID, $item[2], $item[3], (isset($item[4]) ? intval($item[4]) : 100));
			}
		}

		/**
		 * �������� ������������ �������
		 */
		public function UnInstallDependences()
		{
			foreach ($this->arModuleDependences as $item) {
				if (count($item) < 4) continue;

				UnRegisterModuleDependences($item[0], $item[1], $this->MODULE_ID, $item[2], $item[3]);
			}
		}


		/**
		 * ���������� ����� �������� ��� � ����� ��������
		 */
		public function InstallTemplates()
		{
			\Bitrix\Main\Loader::includeModule($this->MODULE_ID);

			$oTemplateType = new \Bxmaker\SmsNotice\Template\TypeTable();
			$oTemplateSite = new \Bxmaker\SmsNotice\Template\SiteTable();
			$oTemplate = new \Bxmaker\SmsNotice\TemplateTable();


			$arSite = array();
			$dbr = \CSite::GetList($by = 'sort', $order = 'asc');
			while ($ar = $dbr->Fetch()) {
				$arSite[] = $ar['ID'];
			}


			$arTypes = array(

				// ������������
				'MAIN_USER_ADD'            => array('main'), // ����������� ������������
				'MAIN_USER_CHANGEPASSWORD' => array('main'), //��������� ������

				// ��������-�������
				'ORDER_NEW'                => array('main'), //����� ������
				'TRACKING_NUMBER'                => array('main'), //������������� �����������
				'ORDER_CANCELED'           => array('main'), //
				'ORDER_PAYED'              => array('main'), //
				'ORDER_STATUS'             => array('main'), //

			);

			if (IsModuleInstalled('bxmaker.authuserphone')) {
				$arTypes = array_merge($arTypes, array(
					// ������ bxmaker.authuserphone
					'BXMAKER_AUTHUSERPHONE_SENDCODE'           => array('main'), // main - ����������� �������� �������, ��� ������ �������� �������������
					'BXMAKER_AUTHUSERPHONE_USERADD'            => array('main'),
					'BXMAKER_AUTHUSERPHONE_USERCHANGEPASSWORD' => array('main'),
				));
			}

			foreach ($arTypes as $code => $arTemplates) {
				if (!is_array($arTemplates)) continue;

				// ����
				$res = $oTemplateType->add(array(
					'NAME'  => GetMessage($this->MODULE_ID . '.' . $code . '.TEMPLATE_TYPE.NAME'),
					'CODE'  => $code,
					'DESCR' => GetMessage($this->MODULE_ID . '.' . $code . '.TEMPLATE_TYPE.DESCR')
				));

				if (!$res->isSuccess()) {
					continue;
				}
				$templateTypeId = $res->getId();


				// �������
				foreach ($arTemplates as $template) {

					$resTemplate = $oTemplate->add(array(
						'TYPE_ID' => $templateTypeId,
						'NAME'    => GetMessage($this->MODULE_ID . '.' . $code . '.TEMPLATE.NAME.' . $template),
						'ACTIVE'  => true,
						'PHONE'   => GetMessage($this->MODULE_ID . '.' . $code . '.TEMPLATE.PHONE.' . $template),
						'TEXT'    => GetMessage($this->MODULE_ID . '.' . $code . '.TEMPLATE.TEXT.' . $template),
					));

					//���������
					foreach ($arSite as $sid) {
						$oTemplateSite->add(array(
							'TID' => intval($resTemplate->getId()),
							'SID' => $sid
						));
					}
				}
			}
		}

		/**
		 * ���������� ����� �������� ��� � ����� ��������
		 */
		public function UnInstallTemplates()
		{
			//        \Bitrix\Main\Loader::includeModule($this->MODULE_ID);
			//        $oTemplateType = new \Bxmaker\SmsNotice\Template\TypeTable();
			//        $oTemplate = new \Bxmaker\SmsNotice\TemplateTable();

			// ��� �� �����, ��� ��� ��� ������ �������� � ����, � �� ����� �������� ������� �� ���� ���������

		}

	}

