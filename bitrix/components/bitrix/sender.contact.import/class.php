<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Uri;
use Bitrix\Main\Loader;
use Bitrix\Main\Error;

use Bitrix\Sender\Internals\QueryController;
use Bitrix\Sender\Internals\CommonAjax;
use Bitrix\Sender\Entity;
use Bitrix\Sender\Message;
use Bitrix\Sender\Security;


if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

Loc::loadMessages(__FILE__);

class SenderContactImportComponent extends CBitrixComponent
{
	/** @var ErrorCollection $errors */
	protected $errors;

	/** @var Entity\Template $entityTemplate */
	protected $entityTemplate;

	protected function checkRequiredParams()
	{
		if (!Loader::includeModule('sender'))
		{
			$this->errors->setError(new Error('Module `sender` is not installed.'));
			return false;
		}
		return $this->errors->count() == 0;
	}

	protected function initParams()
	{
		$request = Context::getCurrent()->getRequest();

		if (!isset($this->arParams['ID']))
		{
			$this->arParams['ID'] = intval($request->get('ID'));
		}

		$this->arParams['SET_TITLE'] = isset($this->arParams['SET_TITLE']) ? $this->arParams['SET_TITLE'] == 'Y' : true;
		$this->arParams['CAN_EDIT'] = isset($this->arParams['CAN_EDIT']) ? $this->arParams['CAN_EDIT'] : Security\User::current()->canEdit();
		$this->arParams['BLACKLIST'] = isset($this->arParams['BLACKLIST']) ? $this->arParams['BLACKLIST'] == 'Y' : true;

		if (isset($this->arParams['LIST_ID']))
		{
			$this->arParams['LIST_ID'] = (int) $this->arParams['LIST_ID'];
		}
		else
		{
			$this->arParams['LIST_ID'] = (int) $this->request->get('listId');
		}
	}

	protected function preparePost()
	{
		if ($this->errors->isEmpty())
		{
			$path = str_replace('#id#', $this->entityTemplate->getId(), $this->arParams['PATH_TO_IMPORT']);
			$uri = new Uri($path);
			if ($this->request->get('IFRAME') == 'Y')
			{
				$uri->addParams(array('IFRAME' => 'Y'));
			}
			$path = $uri->getLocator();

			LocalRedirect($path);
		}
	}

	protected function prepareResult()
	{
		/* Set title */
		if ($this->arParams['SET_TITLE'])
		{
			/**@var CAllMain*/
			$GLOBALS['APPLICATION']->SetTitle(
				$this->arParams['BLACKLIST'] ?
					Loc::getMessage('SENDER_CONTACT_IMPORT_BLACKLIST_TITLE')
					:
					Loc::getMessage('SENDER_CONTACT_IMPORT_TITLE')
			);
		}

		if (!Security\AccessChecker::checkViewAccess($this->errors))
		{
			return false;
		}

		$this->arResult['SUBMIT_FORM_URL'] = Context::getCurrent()->getRequest()->getRequestUri();
		$this->arResult['ACTION_URI'] = $this->getPath() . '/ajax.php';


		$this->entityTemplate = new Entity\Template($this->arParams['ID']);
		$this->arResult['ROW'] = $this->entityTemplate->getData();


		if (!$this->arResult['ROW']['TYPE'])
		{
			$this->arResult['ROW']['TYPE'] = Message\ConfigurationOption::TYPE_MAIL_EDITOR;
		}

		if ($this->request->isPost() && check_bitrix_sessid() && $this->arParams['CAN_EDIT'])
		{
			$this->preparePost();
		}

		$this->arResult['ROW']['CONTENT_URL'] = QueryController\Manager::getActionRequestingUri(
			CommonAjax\ActionGetTemplate::NAME,
			array(
				'template_type' => 'USER',
				'template_id' => $this->arResult['ROW']['ID']
			),
			$this->getPath() . '/ajax.php'
		);

		$this->arResult['USE_TEMPLATES'] = true;
		$this->arResult['SHOW_TEMPLATE_SELECTOR'] = empty($this->arResult['ROW']['CONTENT']);

		return true;
	}

	protected function printErrors()
	{
		foreach ($this->errors as $error)
		{
			ShowError($error);
		}
	}

	public function executeComponent()
	{
		$this->errors = new \Bitrix\Main\ErrorCollection();
		$this->initParams();
		if (!$this->checkRequiredParams())
		{
			$this->printErrors();
			return;
		}

		if (!$this->prepareResult())
		{
			$this->printErrors();
			return;
		}

		$this->includeComponentTemplate();
	}
}