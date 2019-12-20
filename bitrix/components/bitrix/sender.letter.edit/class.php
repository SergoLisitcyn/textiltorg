<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Uri;

use Bitrix\Sender\Entity;
use Bitrix\Sender\Security;
use Bitrix\Sender\Message;
use Bitrix\Sender\Templates;

use Bitrix\Fileman\Block as FilemanBlock;
use Bitrix\Sender\Internals\PostFiles;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

Loc::loadMessages(__FILE__);

class SenderLetterEditComponent extends CBitrixComponent
{
	/** @var ErrorCollection $errors */
	protected $errors;

	/** @var Entity\Letter $letter Letter. */
	protected $letter;

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

		if (!isset($this->arParams['MESSAGE_CODE']))
		{
			$this->arParams['MESSAGE_CODE'] = $this->request->get('code');
		}
		if (!$this->arParams['MESSAGE_CODE'])
		{
			$this->arParams['MESSAGE_CODE'] = Message\iBase::CODE_MAIL;
		}

		if (!isset($this->arParams['IFRAME']))
		{
			$this->arParams['IFRAME'] = $this->request->get('IFRAME');
		}

		$this->arParams['SET_TITLE'] = isset($this->arParams['SET_TITLE']) ? $this->arParams['SET_TITLE'] == 'Y' : true;
		$this->arParams['SHOW_SEGMENT_COUNTERS'] = isset($this->arParams['SHOW_SEGMENT_COUNTERS']) ? $this->arParams['SHOW_SEGMENT_COUNTERS'] : true;
		$this->arParams['CAN_EDIT'] = isset($this->arParams['CAN_EDIT']) ? $this->arParams['CAN_EDIT'] : false;
	}

	protected function preparePostMessage()
	{
		$message = $this->letter->getMessage();
		$configuration = $message->getConfiguration();

		foreach ($configuration->getOptions() as $option)
		{
			$key = 'CONFIGURATION_' . $option->getCode();
			$value = $this->request->get($key);
			switch ($option->getType())
			{
				case Message\ConfigurationOption::TYPE_TEMPLATE_TYPE:
					$value = $this->letter->get('TEMPLATE_TYPE');
					break;
				case Message\ConfigurationOption::TYPE_TEMPLATE_ID:
					$value = $this->letter->get('TEMPLATE_ID');
					break;
				case Message\ConfigurationOption::TYPE_FILE:
					$value = $option->getValue();
					if (!is_array($value))
					{
						$value = array();
					}
					$value = PostFiles::getFromContext($key, $value);
					break;
				case Message\ConfigurationOption::TYPE_MAIL_EDITOR:
					$value = Security\Sanitizer::fixReplacedStyles($value);
					Loader::includeModule('fileman');
					$canEditPhp = Security\User::current()->canEditPhp();
					$canUseLpa = Security\User::current()->canUseLpa();
					$value = FilemanBlock\EditorMail::removePhpFromHtml($value, $option->getValue(), $canEditPhp, $canUseLpa);
					break;
			}
			$option->setValue($value);
		}

		$result = $configuration->checkOptions();
		if ($result->isSuccess())
		{
			$result = $message->saveConfiguration($configuration);
		}
		if ($result->isSuccess())
		{
			$this->letter->set('MESSAGE_ID', $configuration->getId());
		}

		$this->errors->add($result->getErrors());
	}

	protected function preparePostSegments($include = true)
	{
		$segments = $this->request->get('SEGMENT');
		if (!is_array($segments))
		{
			return array();
		}

		$key = $include ? 'INCLUDE' : 'EXCLUDE';
		if (!isset($segments[$key]) || !is_array($segments[$key]))
		{
			return array();
		}
		$segments = $segments[$key];

		$result = array();
		foreach ($segments as $segmentId)
		{
			$result[] = (int) $segmentId;
		}

		return $result;
	}

	protected function canSaveAsTemplate()
	{
		return $this->letter->getMessage()->getCode() === Message\iBase::CODE_MAIL;
	}

	protected function preparePostSaveAsTemplate()
	{
		if ($this->request->get('save_as_template') !== 'Y')
		{
			return;
		}

		if (!$this->canSaveAsTemplate())
		{
			return;
		}


		$templateType = $this->letter->get('TEMPLATE_TYPE');
		$templateId = $this->letter->get('TEMPLATE_ID');
		$message = $this->request->get('CONFIGURATION_MESSAGE');
		$name = $this->request->get('CONFIGURATION_SUBJECT') ?: $this->letter->get('TITLE');

		if (!$templateType || !$templateType || !$message)
		{
			return;
		}

		$template = Templates\Selector::create()
			->withMessageCode($this->letter->getMessage()->getCode())
			->withTypeId($templateType)
			->withId($templateId)
			->get();

		if (!$template || !$template['FIELDS']['MESSAGE']['VALUE'])
		{
			return;
		}

		$useBlockEditor = false;
		Loader::includeModule('fileman');

		$message = FilemanBlock\Content\Engine::fillHtmlTemplate($template['FIELDS']['MESSAGE']['VALUE'], $message);
		if ($message)
		{
			$useBlockEditor = true;
		}

		$addResult = \Bitrix\Sender\TemplateTable::add(array('NAME' => $name, 'CONTENT' => $message));
		if($useBlockEditor && $addResult->isSuccess())
		{
			$templateType = Templates\Type::getCode(Templates\Type::USER);
			$templateId = $addResult->getId();
			$this->letter->set('TEMPLATE_TYPE', $templateType);
			$this->letter->set('TEMPLATE_ID', $templateId);
			$this->letter->getMessage()->getConfiguration()->set('TEMPLATE_TYPE', $templateType);
			$this->letter->getMessage()->getConfiguration()->set('TEMPLATE_ID', $templateId);
		}
	}

	protected function preparePost()
	{
		// agreement accept check
		if(!Security\User::current()->isAgreementAccepted())
		{
			$this->errors->setError(new Error(Loc::getMessage('SENDER_COMP_LETTER_EDIT_ERROR_AGR')));
		}

		// prepare letter
		$data = array(
			'TITLE' => $this->request->get('TITLE'),
			'CAMPAIGN_ID' => $this->letter->get('CAMPAIGN_ID') ?: Entity\Campaign::getDefaultId(),
			'SEGMENTS_INCLUDE' => $this->preparePostSegments(true),
			'SEGMENTS_EXCLUDE' => $this->preparePostSegments(false),
			'TEMPLATE_TYPE' => $this->request->get('TEMPLATE_TYPE'),
			'TEMPLATE_ID' => $this->request->get('TEMPLATE_ID'),
		);
		if (!$this->letter->getId())
		{
			$data['CREATED_BY'] = Security\User::current()->getId();
		}
		$this->letter->mergeData($data);

		// copy template
		if ($this->errors->isEmpty())
		{
			$this->preparePostSaveAsTemplate();
		}

		// add message
		if ($this->errors->isEmpty())
		{
			$this->preparePostMessage();
		}

		// save letter
		if ($this->errors->isEmpty())
		{
			$this->letter->save();
		}
		if ($this->letter->hasErrors())
		{
			$this->errors->add($this->letter->getErrors());
			return;
		}

		// redirect
		if ($this->errors->isEmpty())
		{
			$url = str_replace('#id#', $this->letter->getId(), $this->arParams['PATH_TO_TIME']);
			$uri = new Uri($url);
			if ($this->arParams['IFRAME'] == 'Y')
			{
				$uri->addParams(array('IFRAME' => 'Y'));
			}
			LocalRedirect($uri->getLocator());
		}
	}

	protected function prepareResult()
	{
		if (!Security\AccessChecker::checkViewAccess($this->errors))
		{
			$GLOBALS['APPLICATION']->SetTitle(Loc::getMessage('SENDER_COMP_LETTER_EDIT_TITLE_ADD'));
			return false;
		}

		$this->arResult['SUBMIT_FORM_URL'] = Context::getCurrent()->getRequest()->getRequestUri();

		if ($this->arParams['IS_ADS'] === 'Y')
		{
			$this->letter = new Entity\Ad($this->arParams['ID']);
		}
		else
		{
			$this->letter = new Entity\Letter($this->arParams['ID']);
		}

		if (!$this->letter->getId())
		{
			$this->letter->set('MESSAGE_CODE', $this->arParams['MESSAGE_CODE']);
		}

		try
		{
			$message = $this->letter->getMessage();
			$this->arResult['MESSAGE_CODE'] = $message->getCode();
			$this->arResult['MESSAGE_ID'] = $message->getId();
			$this->arResult['MESSAGE_NAME'] = $message->getName();
			$this->arResult['MESSAGE'] = $message;
		}
		catch (SystemException $exception)
		{
			$this->errors->setError(new Error(Loc::getMessage('SENDER_COMP_LETTER_EDIT_ERROR_MSG_CODE', array('%type%' => $this->letter->get('MESSAGE_CODE')))));
			return false;
		}

		// Process POST
		if ($this->request->isPost() && check_bitrix_sessid() && $this->arParams['CAN_EDIT'])
		{
			$this->preparePost();
			$this->printErrors();
		}
		else if (!$this->letter->getId())
		{
			$this->letter->set('SEGMENTS_INCLUDE', Entity\Segment::getDefaultIds());
		}

		// get row
		$this->arResult['ROW'] = $this->letter->getData();

		// get campaign
		$this->arResult['CAMPAIGN_ID'] = $this->letter->getCampaignId();

		// get campaigns
		$this->arResult['CAMPAIGNS'] = array();
		$campaigns = Entity\Campaign::getList(array(
			'select' => array('ID', 'NAME', 'SITE_ID', 'SITE_NAME' => 'SITE.NAME'),
			'order' => array('ID' => 'DESC')
		));
		foreach ($campaigns as $campaign)
		{
			$campaign['SELECTED'] = $campaign['ID'] == $this->arResult['CAMPAIGN_ID'];
			$siteName = Loc::getMessage('SENDER_COMP_LETTER_EDIT_SITE') . " `{$campaign['SITE_NAME']}`";
			$this->arResult['CAMPAIGNS'][$siteName][] = $campaign;
		}


		// get options list
		$configuration = $this->letter->getMessage()->getConfiguration();
		$this->arResult['LIST'] = array(
			Message\ConfigurationOption::GROUP_DEFAULT => Message\Configuration::convertToArray(
				$configuration->getOptionsByGroup(Message\ConfigurationOption::GROUP_DEFAULT)
			),
			Message\ConfigurationOption::GROUP_ADDITIONAL => Message\Configuration::convertToArray(
				$configuration->getOptionsByGroup(Message\ConfigurationOption::GROUP_ADDITIONAL)
			),
		);

		$this->arResult['USE_TEMPLATES'] = Templates\Selector::create()
			->withMessageCode($this->arResult['MESSAGE_CODE'])
			->hasAny();

		$this->arResult['SHOW_TEMPLATE_SELECTOR'] = !$this->letter->getId() && !$this->request->isPost() && $this->arResult['USE_TEMPLATES'];
		$this->arResult['CAN_CHANGE_TEMPLATE'] = $this->letter->canChangeTemplate();


		$this->arResult['SEGMENTS'] = array(
			'INCLUDE' => $this->arResult['ROW']['SEGMENTS_INCLUDE'],
			'EXCLUDE' => $this->arResult['ROW']['SEGMENTS_EXCLUDE'],
			'RECIPIENT_COUNT' => $this->letter->getId() ?
				$this->letter->getCounter()->getAll()
				:
				null,
			'IS_RECIPIENT_COUNT_EXACT' => $this->letter->getId() > 0,
			'DURATION_FORMATTED' => !$this->letter->getState()->isFinished() ?
				$this->letter->getDuration()->getFormattedInterval()
				:
				null,
			'READONLY' => !$this->letter->canChangeSegments()
		);

		$this->arResult['CAN_SAVE_AS_TEMPLATE'] = $this->canSaveAsTemplate();

		if ($this->arParams['SET_TITLE'])
		{
			if ($this->arParams['IFRAME'] && $this->arResult['SHOW_TEMPLATE_SELECTOR'])
			{
				$GLOBALS['APPLICATION']->SetTitle(Loc::getMessage('SENDER_COMP_LETTER_EDIT_TITLE_TEMPLATES'));
			}
			else
			{
				$GLOBALS['APPLICATION']->SetTitle($this->letter->getId() ?
					Loc::getMessage('SENDER_COMP_LETTER_EDIT_TITLE_EDIT')
					:
					Loc::getMessage('SENDER_COMP_LETTER_EDIT_TITLE_ADD')
				);
			}
		}

		return true;
	}

	protected function initTemplates()
	{
		$configuration = $this->letter->getMessage()->getConfiguration();

		// get template input names and values
		$this->arResult['TEMPLATE_TYPE'] = null;
		$this->arResult['TEMPLATE_ID'] = null;
		$option = $configuration->getOptionByType(Message\ConfigurationOption::TYPE_TEMPLATE_TYPE);
		if ($option)
		{
			$this->arResult['TEMPLATE_TYPE_INPUT_NAME'] = $option->getCode();
			$this->arResult['TEMPLATE_TYPE'] = $option->getValue();
			$option = $configuration->getOptionByType(Message\ConfigurationOption::TYPE_TEMPLATE_ID);
			if ($option)
			{
				$this->arResult['TEMPLATE_ID_INPUT_NAME'] = $option->getCode();
				$this->arResult['TEMPLATE_ID'] = $option->getValue();
			}
		}

		$template = Templates\Selector::create()
			->withMessageCode($this->arResult['MESSAGE_CODE'])
			->withTypeId($this->arResult['TEMPLATE_TYPE'])
			->withId($this->arResult['TEMPLATE_ID'])
			->get();
		if ($template)
		{
			$this->arResult['TEMPLATE_NAME'] = $template['NAME'];
		}
		else
		{
			$this->arResult['TEMPLATE_TYPE'] = null;
			$this->arResult['TEMPLATE_ID'] = null;
		}
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

		$templateName = $this->request->get('showTime') == 'y' ? 'time' : '';
		$this->includeComponentTemplate($templateName);
	}
}