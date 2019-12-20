<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Error;

use Bitrix\Sender\Message;
use Bitrix\Sender\Recipient;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

Loc::loadMessages(__FILE__);

class SenderMessageTestComponent extends CBitrixComponent
{
	/** @var ErrorCollection $errors */
	protected $errors;

	protected function checkRequiredParams()
	{
		if (!$this->arParams['MESSAGE_CODE'])
		{
			$this->errors->setError(new Error('Message code is not set.'));
		}

		return $this->errors->count() == 0;
	}

	protected function initParams()
	{
		$this->arParams['MESSAGE_CODE'] = isset($this->arParams['MESSAGE_CODE']) ? $this->arParams['MESSAGE_CODE'] : null;
	}

	protected function prepareResult()
	{
		$this->arResult['ACTION_URI'] = $this->getPath() . '/ajax.php';

		// init message
		try
		{
			$message = Message\Adapter::getInstance($this->arParams['MESSAGE_CODE']);
		}
		catch (\Bitrix\Main\SystemException $exception)
		{
			$this->errors->setError(new Error(Loc::getMessage(
				'SENDER_MESSAGE_TESTER_ERROR_UNKNOWN_CODE',
				array('%code%' => $this->arParams['MESSAGE_CODE'])
			)));

			return false;
		}

		$this->arResult['MESSAGE_CODE'] = $message->getCode();
		$this->arResult['MESSAGE_ID'] = $message->getConfiguration()->getId();

		if (!$message->getTester()->isSupport())
		{
			$this->errors->setError(new Error('')); // keep empty message
		}

		$this->arResult['TYPE_ID'] = $message->getTester()->getRecipientType();
		$this->arResult['TYPE_CODE'] = Recipient\Type::getCode($this->arResult['TYPE_ID']);
		$this->arResult['TYPE_CODE'] = strtolower($this->arResult['TYPE_CODE']);

			// dict
		$this->arResult['TYPES'] = array(
			'mail' => Recipient\Type::EMAIL,
			'phone' => Recipient\Type::PHONE,
		);

		$this->prepareRecipients($message->getTester()->getLastCodes());


		return true;
	}

	protected function prepareRecipients(array $codes)
	{
		$this->arResult['DEFAULT_RECIPIENTS'] = array();
		$this->arResult['LAST_RECIPIENTS'] = array();

		foreach ($codes as $code)
		{
			if (count($this->arResult['DEFAULT_RECIPIENTS']) === 0)
			{
				$this->arResult['DEFAULT_RECIPIENTS'][] = array(
					'id' => $code,
					'name' => $code,
					'data' => array(),
				);
			}

			$this->arResult['LAST_RECIPIENTS'][] = $code;
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

		$this->includeComponentTemplate();
	}
}