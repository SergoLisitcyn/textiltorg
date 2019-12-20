<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ErrorCollection;

use Bitrix\Sender\Templates;
use Bitrix\Sender\Message;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

Loc::loadMessages(__FILE__);

class SenderTemplateSelectorComponent extends CBitrixComponent
{
	/** @var ErrorCollection $errors */
	protected $errors;

	protected function checkRequiredParams()
	{
		return true;
	}

	protected function initParams()
	{
		$this->arParams['CAN_CANCEL'] = isset($this->arParams['CAN_CANCEL']) ? (bool) $this->arParams['CAN_CANCEL'] : false;
		/*
		$this->arParams['ID'] = isset($this->arParams['ID']) ? intval($this->arParams['ID']) : null;
		$this->arParams['INPUT_NAME'] = isset($this->arParams['INPUT_NAME']) ? (string) $this->arParams['INPUT_NAME'] : 'AGREEMENT_ID';

		$this->arParams['PATH_TO_ADD'] = isset($this->arParams['PATH_TO_ADD']) ? $this->arParams['PATH_TO_ADD'] : '';
		$this->arParams['PATH_TO_EDIT'] = isset($this->arParams['PATH_TO_EDIT']) ? $this->arParams['PATH_TO_EDIT'] : '';
		$this->arParams['PATH_TO_CONSENT_LIST'] = isset($this->arParams['PATH_TO_CONSENT_LIST']) ? $this->arParams['PATH_TO_CONSENT_LIST'] : '';
		$this->arParams['ACTION_REQUEST_URL'] = isset($this->arParams['ACTION_REQUEST_URL']) ? $this->arParams['ACTION_REQUEST_URL'] : '';
		*/

		if (!isset($this->arParams['MESSAGE_CODE']))
		{
			$this->arParams['MESSAGE_CODE'] = Message\iBase::CODE_MAIL;
		}
	}

	protected function prepareResult()
	{
		$this->arResult['ACTION_URI'] = $this->getPath() . '/ajax.php';
		$this->arResult['GRID'] = array(
			'rows'=> array(),
			'items'=> array(),
			'type'=> $this->arParams['MESSAGE_CODE'],
		);

		$selector = Templates\Selector::create()
			->withMessageCode($this->arParams['MESSAGE_CODE'])
			->withVersion(2);

		$this->arResult['GRID']['rows'] = $selector->getCategories();
		$templateCounter = 0;
		foreach ($selector->getList() as $template)
		{
			$messageFields = array();
			foreach ($template['FIELDS'] as $field)
			{
				$onDemand = isset($field['ON_DEMAND']) && $field['ON_DEMAND'];
				$messageFields[] = array(
					'code' => $field['CODE'],
					'value' => $onDemand ? null : $field['VALUE'],
					'onDemand' => $onDemand,
				);
			}
			
			$this->arResult['GRID']['items'][] = array(
				'id' => $template['TYPE'] . '|' . $template['ID'] . '|' . (++$templateCounter),
				'name' => $template['NAME'],
				'description' => $template['DESC'],
				'image' => $template['ICON'],
				'hot' => $template['HOT'],
				'rowId' => $template['CATEGORY'],
				'data' => array(
					'templateId' => $template['ID'],
					'templateType' => $template['TYPE'],
					'messageFields' => $messageFields,
				)
			);
		}

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