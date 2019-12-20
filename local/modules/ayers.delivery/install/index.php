<?
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config as Conf;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Application;
use \Bitrix\Main\EventManager;

Loc::loadMessages(__FILE__);
Class ayers_delivery extends CModule
{
	function __construct()
	{
		$arModuleVersion = array();
		include(__DIR__.'/version.php');

        $this->MODULE_ID = 'ayers.delivery';
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME = Loc::getMessage('AYERS_DELIVERY_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('AYERS_DELIVERY_MODULE_DESC');

		$this->PARTNER_NAME = Loc::getMessage('AYERS_DELIVERY_PARTNER_NAME');
		$this->PARTNER_URI = Loc::getMessage('AYERS_DELIVERY_PARTNER_URI');

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

    }

    function UnInstallDB()
    {

    }

	function InstallEvents()
	{

	}

	function UnInstallEvents()
	{

	}

	function InstallFiles($arParams = array())
	{
        if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/ayers/'))
        {
            mkdir($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/ayers/', 775);
        }

        symlink($this->GetPath() . '/install/components/ayers/delivery.info', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/ayers/delivery.info');

        return true;
	}

	function UnInstallFiles()
	{
        \Bitrix\Main\IO\Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/bitrix/cache/' . $this->MODULE_ID);
        \Bitrix\Main\IO\Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/' . $this->MODULE_ID);

        unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/ayers/delivery.info');

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
            $APPLICATION->ThrowException(Loc::getMessage('AYERS_DELIVERY_INSTALL_ERROR_VERSION'));
        }
	}

	function DoUninstall()
	{
        global $APPLICATION;

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        $this->UnInstallFiles();
		$this->UnInstallEvents();
        $this->UnInstallDB();

        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
	}

    function GetModuleRightList()
    {
        return array(
            'reference_id' => array('D','K','S','W'),
            'reference' => array(
                '[D] '.Loc::getMessage('AYERS_DELIVERY_DENIED'),
                '[K] '.Loc::getMessage('AYERS_DELIVERY_READ_COMPONENT'),
                '[S] '.Loc::getMessage('AYERS_DELIVERY_WRITE_SETTINGS'),
                '[W] '.Loc::getMessage('AYERS_DELIVERY_FULL'))
        );
    }
}
?>