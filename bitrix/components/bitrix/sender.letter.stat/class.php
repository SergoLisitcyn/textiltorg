<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;

use Bitrix\Sender\Stat;
use Bitrix\Sender\Entity;
use Bitrix\Sender\Security;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

Loc::loadMessages(__FILE__);

class SenderLetterStatComponent extends CBitrixComponent
{
	/** @var ErrorCollection $errors */
	protected $errors;

	protected function checkRequiredParams()
	{
		if (!Loader::includeModule('sender'))
		{
			$this->errors->setError(new Error('Module `sender` is not installed.'));
			return false;
		}
		return true;
	}

	protected function initParams()
	{
		$request = Context::getCurrent()->getRequest();
		if (!isset($this->arParams['MAILING_ID']))
		{
			$this->arParams['MAILING_ID'] = intval($request->get('MAILING_ID'));
		}
		if (!$this->arParams['MAILING_ID'])
		{
			$this->arParams['MAILING_ID'] = intval($request->get('mailingId'));
		}

		if (!isset($this->arParams['CHAIN_ID']))
		{
			$this->arParams['CHAIN_ID'] = intval($request->get('ID'));
		}

		if (!$this->arParams['CHAIN_ID'])
		{
			$this->arParams['CHAIN_ID'] = intval($request->get('chainId'));
		}

		$this->arParams['SET_TITLE'] = isset($this->arParams['SET_TITLE']) ? $this->arParams['SET_TITLE'] === 'Y' : true;

		$this->arParams['POSTING_ID'] = null;
	}

	protected function prepareResult()
	{
		/* Set title */
		if ($this->arParams['SET_TITLE'])
		{
			/**@var CMain*/
			$GLOBALS['APPLICATION']->SetTitle(Loc::getMessage('SENDER_LETTER_STAT_COMP_TITLE'));
		}

		if (!Security\AccessChecker::checkViewAccess($this->errors))
		{
			return false;
		}

		$this->arResult['ACTION_URL'] = $this->getPath() . '/ajax.php';
		$this->arResult['MAILING_COUNTERS'] = array();

		$letter = new Entity\Letter($this->arParams['CHAIN_ID']);
		if (!$letter->getId())
		{
			$this->errors->setError(new Error(Loc::getMessage("SENDER_LETTER_STAT_COMP_NO_DATA")));
			return false;
		}
		$posting = $letter->getLastPostingData();
		/*
		if(empty($posting))
		{
			$this->errors->setError(new Error(Loc::getMessage("SENDER_LETTER_STAT_COMP_NO_DATA")));
			return false;
		}
		*/

		$posting['TITLE'] = $posting['TITLE'] ?: $posting['SUBJECT'];
		$this->arResult['POSTING'] = $posting;
		$this->arResult['IS_SUPPORT_HEAT_MAP'] = $letter->isSupportHeatMap();

		$mailingStat = Stat\Statistics::create()
			->filter('mailingId', $letter->getCampaignId())
			->filter('postingId', $posting['POSTING_ID'])
			->setCacheTtl(0);
		$this->arResult['CHAIN_LIST'] = $mailingStat->getChainList(7);
		$this->arResult['EFFICIENCY'] = $mailingStat->getEfficiency();
		$mailingCounters = $mailingStat->getCounters();
		foreach ($mailingCounters as $counter)
		{
			$this->arResult['MAILING_COUNTERS'][$counter['CODE']] = $counter;
		}

		$this->arResult['DATA'] = Stat\Posting::getData($letter->getId(), array(
			'USER_NAME_FORMAT' => $this->arParams['NAME_TEMPLATE'],
			'PATH_TO_USER_PROFILE' => $this->arParams['PATH_TO_USER_PROFILE'],
		));

		return $this->errors->isEmpty();
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