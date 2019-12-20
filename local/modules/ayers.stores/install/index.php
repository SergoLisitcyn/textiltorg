<?
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config as Conf;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Application;

Loc::loadMessages(__FILE__);
Class ayers_stores extends CModule
{
	function __construct()
	{
		$arModuleVersion = array();
		include(__DIR__.'/version.php');

        $this->MODULE_ID = 'ayers.stores';
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME = Loc::getMessage('AYERS_STORES_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('AYERS_STORES_MODULE_DESC');

		$this->PARTNER_NAME = Loc::getMessage('AYERS_STORES_PARTNER_NAME');
		$this->PARTNER_URI = Loc::getMessage('AYERS_STORES_PARTNER_URI');

        $this->MODULE_SORT = 1;
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS='Y';
        $this->MODULE_GROUP_RIGHTS = 'Y';
	}

    //Определяем место размещения модуля
    public function GetPath($notDocumentRoot=false)
    {
        if($notDocumentRoot)
            return str_ireplace(Application::getDocumentRoot(),'',dirname(__DIR__));
        else
            return dirname(__DIR__);
    }

    //Проверяем что система поддерживает D7
    public function isVersionD7()
    {
        return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
    }

    function InstallDB()
    {
        Loader::includeModule($this->MODULE_ID);
        if(!Application::getConnection(\Ayers\Stores\StoresTable::getConnectionName())->isTableExists(
            Base::getInstance('\Ayers\Stores\StoresTable')->getDBTableName()
            )
        )
        {
            Base::getInstance('\Ayers\Stores\StoresTable')->createDbTable();
        }
    }

    function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        Application::getConnection(\Ayers\Stores\StoresTable::getConnectionName())->
            queryExecute('drop table if exists '.Base::getInstance('\Ayers\Stores\StoresTable')->getDBTableName());

        Option::delete($this->MODULE_ID);
    }

	function InstallEvents()
	{

	}

	function UnInstallEvents()
	{

	}

	function InstallFiles($arParams = array())
	{
        CopyDirFiles($this->GetPath() . '/install/themes/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/themes/', true, true);
        CopyDirFiles($this->GetPath() . '/install/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/');

        if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/ayers/'))
        {
            mkdir($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/ayers/');
        }

        symlink($this->GetPath() . '/install/components/ayers/stores.map', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/ayers/stores.map');
        symlink($this->GetPath() . '/install/components/ayers/stores.page', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/ayers/stores.page');
        symlink($this->GetPath() . '/install/components/ayers/stores.product', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/ayers/stores.product');

        return true;
	}

	function UnInstallFiles()
	{
        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . $this->GetPath() . '/install/themes/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/themes/');
        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . $this->GetPath() . '/install/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/');
        \Bitrix\Main\IO\Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/bitrix/cache/' . $this->MODULE_ID);
        \Bitrix\Main\IO\Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/' . $this->MODULE_ID);

        unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/ayers/stores.map');
        unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/ayers/stores.page');
        unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/ayers/stores.product');

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
        if($this->isVersionD7())
        {
            \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);

            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();
        }
        else
        {
            $APPLICATION->ThrowException(Loc::getMessage('AYERS_STORES_INSTALL_ERROR_VERSION'));
        }
	}

	function DoUninstall()
	{
        global $APPLICATION;

        global $APPLICATION;

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if($request['step'] < 2)
        {
            $APPLICATION->IncludeAdminFile('Удаление модуля "Пункты самовывоза"', $this->GetPath()."/install/unstep.php");
        }
        elseif($request['step'] == 2)
        {
            $this->UnInstallFiles();
    		$this->UnInstallEvents();

            if($request['savedata'] != 'Y')
            {
                $this->UnInstallDB();
            }

            \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
        }
	}

    function GetModuleRightList()
    {
        return array(
            'reference_id' => array('D','K','S','W'),
            'reference' => array(
                '[D] '.Loc::getMessage('AYERS_STORES_DENIED'),
                '[K] '.Loc::getMessage('AYERS_STORES_READ_COMPONENT'),
                '[S] '.Loc::getMessage('AYERS_STORES_WRITE_SETTINGS'),
                '[W] '.Loc::getMessage('AYERS_STORES_FULL'))
        );
    }
}
?>