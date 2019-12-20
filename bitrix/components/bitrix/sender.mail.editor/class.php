<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\ErrorCollection;
use Bitrix\Sender\PostingRecipientTable;
use Bitrix\Sender\TemplateTable;
use Bitrix\Sender\Internals\QueryController as Controller;
use Bitrix\Sender\Internals\CommonAjax;
use Bitrix\Fileman\Block\EditorMail as BlockEditorMail;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

Loc::loadMessages(__FILE__);

class SenderMessageEditorMailComponent extends CBitrixComponent
{
	/** @var ErrorCollection $errors */
	protected $errors;

	protected function checkRequiredParams()
	{
		return true;
	}

	protected function initParams()
	{
		$this->arParams['INPUT_NAME'] = isset($this->arParams['INPUT_NAME']) ? $this->arParams['INPUT_NAME'] : 'MESSAGE';
		$this->arParams['VALUE'] = isset($this->arParams['~VALUE']) ? $this->arParams['~VALUE'] : '';
		if (!isset($this->arParams['~VALUE']))
		{
			$this->arParams['~VALUE'] = htmlspecialcharsback($this->arParams['VALUE']);
		}

		$this->arParams['HAS_USER_ACCESS'] = isset($this->arParams['HAS_USER_ACCESS']) ? (bool) $this->arParams['HAS_USER_ACCESS'] : false;
		$this->arParams['USE_LIGHT_TEXT_EDITOR'] = isset($this->arParams['USE_LIGHT_TEXT_EDITOR']) ? (bool) $this->arParams['USE_LIGHT_TEXT_EDITOR'] : false;
		$this->arParams['SITE'] = isset($this->arParams['SITE']) ? $this->arParams['SITE'] : $this->getSiteId();
		$this->arParams['CHARSET'] = isset($this->arParams['CHARSET']) ? $this->arParams['CHARSET'] : '';
		$this->arParams['CONTENT_URL'] = isset($this->arParams['CONTENT_URL']) ? $this->arParams['CONTENT_URL'] : '';

		$this->arParams['TEMPLATE_TYPE'] = isset($this->arParams['TEMPLATE_TYPE']) ? $this->arParams['TEMPLATE_TYPE'] : null;
		$this->arParams['TEMPLATE_ID'] = isset($this->arParams['TEMPLATE_ID']) ? $this->arParams['TEMPLATE_ID'] : null;

		$this->arParams['IS_TEMPLATE_MODE'] = isset($this->arParams['IS_TEMPLATE_MODE']) ? (bool) $this->arParams['IS_TEMPLATE_MODE'] : true;
		if (!isset($this->arParams['PERSONALIZE_LIST']) || !is_array($this->arParams['PERSONALIZE_LIST']))
		{
			$this->arParams['PERSONALIZE_LIST'] = array();
		}
	}

	protected function prepareResult()
	{
		Loader::includeModule('fileman');

		/*
		\CJSCore::RegisterExt("sender_editor", Array(
			"js" => array("/bitrix/js/sender/editor/htmleditor.js"),
			"rel" => array()
		));
		\CJSCore::Init(array("sender_editor"));
		*/


		// personalize tags
		if (!empty($this->arParams['PERSONALIZE_LIST']))
		{
			PostingRecipientTable::setPersonalizeList($this->arParams['PERSONALIZE_LIST']);
		}
		$this->arResult['PERSONALIZE_LIST'] = PostingRecipientTable::getPersonalizeList();

		// template use
		$this->arResult['TEMPLATE_USED'] = false;
		$this->arResult['DISPLAY_BLOCK_EDITOR'] = false;
		if (TemplateTable::isContentForBlockEditor($this->arParams['VALUE']))
		{
			$this->arResult['DISPLAY_BLOCK_EDITOR'] = true;
		}
		if ($this->arParams['TEMPLATE_TYPE'] && $this->arParams['TEMPLATE_ID'])
		{
			$this->arResult['DISPLAY_BLOCK_EDITOR'] = true;
			$this->arResult['TEMPLATE_USED'] = true;
		}

		$url = '';
		if($this->arResult['DISPLAY_BLOCK_EDITOR'])
		{
			if($this->arResult['TEMPLATE_USED'])
			{
				$url = CommonAjax\ActionGetTemplate::getRequestingUri(
					$this->getPath() . '/ajax.php',
					array(
						'template_type' => $this->arParams['TEMPLATE_TYPE'],
						'template_id' => $this->arParams['TEMPLATE_ID']
					)
				);
			}
			else
			{
				$url = $this->arParams['CONTENT_URL'];
			}
		}

		$controllerUri = $this->getPath() . '/ajax.php';
		$saveFileUrl = Controller\Manager::getActionRequestingUri('saveFile', array(), $controllerUri);
		$previewUrl = CommonAjax\ActionPreview::getRequestingUri($controllerUri);
		$this->arResult['INPUT_ID'] = 'bxed_' . $this->arParams['INPUT_NAME'];

		$this->arResult['~BLOCK_EDITOR'] = BlockEditorMail::show(array(
			'id' => $this->arParams['INPUT_NAME'],
			'charset' => $this->arParams['CHARSET'],
			'site' => $this->arParams['SITE'],
			'own_result_id' => $this->arResult['INPUT_ID'],
			'url' => $url,
			'previewUrl' => $previewUrl,
			'saveFileUrl' => $saveFileUrl,
			'templateType' => $this->arParams['TEMPLATE_TYPE'],
			'templateId' => $this->arParams['TEMPLATE_ID'],
			'isTemplateMode' => $this->arParams['IS_TEMPLATE_MODE'],
			'isUserHavePhpAccess' => $this->arParams['HAS_USER_ACCESS'],
			'useLightTextEditor' => $this->arParams['USE_LIGHT_TEXT_EDITOR'],
		));

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