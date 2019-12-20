<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config as Conf;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);
Class ayers_ymarket extends CModule
{
	function __construct()
	{
		$arModuleVersion = array();
		include(__DIR__.'/version.php');

        $this->MODULE_ID = 'ayers.ymarket';
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME = Loc::getMessage('AYERS_YMARKET_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('AYERS_YMARKET_MODULE_DESC');

		$this->PARTNER_NAME = Loc::getMessage('AYERS_YMARKET_PARTNER_NAME');
		$this->PARTNER_URI = Loc::getMessage('AYERS_YMARKET_PARTNER_URI');

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
        return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
    }

    function InstallDB()
    {
        ModuleManager::registerModule($this->MODULE_ID);

        Loader::includeModule($this->MODULE_ID);

        if (!Application::getConnection(Ayers\YMarket\StatisticsTable::getConnectionName())->isTableExists(
            Base::getInstance('Ayers\YMarket\StatisticsTable')->getDBTableName()
            )
        ) {
            Base::getInstance('Ayers\YMarket\StatisticsTable')->createDbTable();
        }

        if (!Application::getConnection(Ayers\YMarket\CategoriesTable::getConnectionName())->isTableExists(
            Base::getInstance('Ayers\YMarket\CategoriesTable')->getDBTableName()
            )
        ) {
            Base::getInstance('Ayers\YMarket\CategoriesTable')->createDbTable();
        }

        if (!Application::getConnection(Ayers\YMarket\VendorTable::getConnectionName())->isTableExists(
            Base::getInstance('Ayers\YMarket\VendorTable')->getDBTableName()
            )
        ) {
            Base::getInstance('Ayers\YMarket\VendorTable')->createDbTable();
        }

        if (!Application::getConnection(Ayers\YMarket\VendorCategoryTable::getConnectionName())->isTableExists(
            Base::getInstance('Ayers\YMarket\VendorCategoryTable')->getDBTableName()
            )
        ) {
            Base::getInstance('Ayers\YMarket\VendorCategoryTable')->createDbTable();
        }
    }

    function UnInstallDB()
    {

        Loader::includeModule($this->MODULE_ID);

        Application::getConnection(Ayers\YMarket\StatisticsTable::getConnectionName())->
            queryExecute('drop table if exists '.Base::getInstance('Ayers\YMarket\StatisticsTable')->getDBTableName());

        Application::getConnection(Ayers\YMarket\CategoriesTable::getConnectionName())->
            queryExecute('drop table if exists '.Base::getInstance('Ayers\YMarket\CategoriesTable')->getDBTableName());

        Application::getConnection(Ayers\YMarket\CategoriesTable::getConnectionName())->
            queryExecute('drop table if exists '.Base::getInstance('Ayers\YMarket\VendorTable')->getDBTableName());

        Application::getConnection(Ayers\YMarket\VendorCategoryTable::getConnectionName())->
            queryExecute('drop table if exists '.Base::getInstance('Ayers\YMarket\VendorCategoryTable')->getDBTableName());

        //Option::delete($this->MODULE_ID);

        ModuleManager::unRegisterModule($this->MODULE_ID);
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

        return true;
	}

	function UnInstallFiles()
	{
        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . $this->GetPath() . '/install/themes/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/themes/');
        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . $this->GetPath() . '/install/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/');
        \Bitrix\Main\IO\Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/bitrix/cache/' . $this->MODULE_ID);
        \Bitrix\Main\IO\Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/' . $this->MODULE_ID);

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
        if($this->isVersionD7())
        {
            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();
        }
        else
        {
            $APPLICATION->ThrowException(Loc::getMessage('AYERS_YMARKET_INSTALL_ERROR_VERSION'));
        }
	}

	function DoUninstall()
	{
        global $APPLICATION;

        $this->UnInstallDB();
        $this->UnInstallFiles();
    	$this->UnInstallEvents();
	}

    function GetModuleRightList()
    {
        return array(
            'reference_id' => array('D','K','S','W'),
            'reference' => array(
                '[D] '.Loc::getMessage('AYERS_YMARKET_DENIED'),
                '[K] '.Loc::getMessage('AYERS_YMARKET_READ_COMPONENT'),
                '[S] '.Loc::getMessage('AYERS_YMARKET_WRITE_SETTINGS'),
                '[W] '.Loc::getMessage('AYERS_YMARKET_FULL'))
        );
    }
}
?>