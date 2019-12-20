<?
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config as Conf;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Application;


Loc::loadMessages(__FILE__);

Class ayers_common extends CModule
{
	function __construct()
	{
		$arModuleVersion = array();
		include(__DIR__."/version.php");

        $this->MODULE_ID = 'ayers.common';
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = "Общий модуль";
		$this->MODULE_DESCRIPTION = "Общий модуль для проекта textiletorg.ru";

		$this->PARTNER_NAME = "Ayers";
		$this->PARTNER_URI = "ayers.ru";
	}

    //Определяем место размещения модуля
    public function GetPath($notDocumentRoot=false)
    {
        if($notDocumentRoot)
            return str_ireplace($_SERVER["DOCUMENT_ROOT"],'',dirname(__DIR__));
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
        
        if(!Application::getConnection(\Ayers\Common\YandexPricesTable::getConnectionName())->isTableExists(
            Base::getInstance('\Ayers\Common\YandexPricesTable')->getDBTableName()
            )
        )
        {
            Base::getInstance('\Ayers\Common\YandexPricesTable')->createDbTable();
        }
    }

    function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        Application::getConnection(\Ayers\Common\YandexPricesTable::getConnectionName())->
            queryExecute('drop table if exists '.Base::getInstance('\Ayers\Common\YandexPricesTable')->getDBTableName());

        Option::delete($this->MODULE_ID);
    }

        function InstallEvents()
	{
            return true;
	}

	function UnInstallEvents()
	{
            return true;
	}

	function InstallFiles()
	{
            return true;
	}

	function UnInstallFiles()
	{
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
                $APPLICATION->ThrowException("Ошибка версии платформы");
            }
	}

	function DoUninstall()
	{
            global $APPLICATION;
           
            $this->UnInstallEvents();
            $this->UnInstallFiles();
            $this->UnInstallDB();
            
            \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
	}
}
?>