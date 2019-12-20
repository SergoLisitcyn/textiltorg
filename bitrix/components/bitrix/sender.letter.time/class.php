<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Web\Uri;
use Bitrix\Main\Context;
use Bitrix\Main\Type;
use Bitrix\Main\Loader;

use Bitrix\Sender\Dispatch;
use Bitrix\Sender\Entity;
use Bitrix\Sender\Security;
use Bitrix\Sender\Integration;
use Bitrix\Sender\Internals\PrettyDate;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

Loc::loadMessages(__FILE__);

class SenderLetterTimeComponent extends CBitrixComponent
{
	/** @var ErrorCollection $errors */
	protected $errors;

	/** @var Entity\Letter $letter */
	protected $letter;

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
		if (!isset($this->arParams['ID']))
		{
			$this->errors->setError(new Error(Loc::getMessage('SENDER_LETTER_TIME_COMP_ERR_NO_LETTER')));
		}

		$this->arParams['IS_ADS'] = isset($this->arParams['IS_ADS']) ? (bool) $this->arParams['IS_ADS'] : false;
		$this->arParams['SET_TITLE'] = isset($this->arParams['SET_TITLE']) ? $this->arParams['SET_TITLE'] == 'Y' : true;
		$this->arParams['CAN_EDIT'] = isset($this->arParams['CAN_EDIT']) ? $this->arParams['CAN_EDIT'] : Security\User::current()->canEdit();
	}

	protected function preparePost()
	{
		$dateTime = null;
		$code = $this->request->get('LETTER_TIME');
		if (Type\DateTime::isCorrect($code))
		{
			/** @var Type\DateTime $dateTime Time */
			$dateTime = new Type\DateTime($code);
			$diff = \CTimeZone::GetOffset();
			$dateTime->add(($diff > 0 ? "-" : "") . "PT" . abs($diff) . "S");
			$code = 'time';
		}

		$method = $this->letter->getMethod();
		if ($method->canChange())
		{
			switch ($code)
			{
				case 'now':
					$method->now();
					break;

				case Dispatch\Method::SCHEDULE:
					$scheduleTime = Dispatch\MethodSchedule::parseTimesOfDay($this->request->get('TIMES_OF_DAY'));
					$scheduleWeekDays = Dispatch\MethodSchedule::parseDaysOfWeek($this->request->get('DAYS_OF_WEEK'));
					if (empty($scheduleTime))
					{
						$this->errors->setError(new Error(Loc::getMessage('SENDER_LETTER_TIME_COMP_ERR_SCHEDULE_WRONG_TIME')));
					}
					if (empty($scheduleWeekDays))
					{
						$this->errors->setError(new Error(Loc::getMessage('SENDER_LETTER_TIME_COMP_ERR_SCHEDULE_WRONG_WEEK_DAYS')));
					}
					$method->set(
						(new Dispatch\MethodSchedule($this->letter))
							->setDaysOfWeek($scheduleWeekDays)
							->setTime($scheduleTime[0], $scheduleTime[1])
					);
					break;

				case Dispatch\Method::TIME:
					$method->time($dateTime);
					break;

				case Dispatch\Method::DEFERED:
				default:
					$method->defer();
					break;
			}

			if ($this->letter->hasErrors())
			{
				$this->errors->add($this->letter->getErrors());
				return;
			}

			$method->apply();
		}

		if ($this->errors->isEmpty())
		{
			$url = str_replace('#id#', $this->letter->getId(), $this->arParams['PATH_TO_TIME']);
			$uri = new Uri($url);
			if ($this->arParams['IFRAME'] == 'Y')
			{
				$uri->addParams(array('IFRAME' => 'Y'));
				$uri->addParams(array('IS_SAVED' => 'Y'));
			}

			LocalRedirect($uri->getLocator());
		}
	}

	protected function prepareResult()
	{
		/* Set title */
		if ($this->arParams['SET_TITLE'])
		{
			/**@var CAllMain*/
			$GLOBALS['APPLICATION']->SetTitle(Loc::getMessage('SENDER_LETTER_TIME_COMP_TITLE'));
		}

		if (!Security\AccessChecker::checkViewAccess($this->errors))
		{
			return false;
		}

		if ($this->arParams['IS_ADS'])
		{
			$this->letter = new Entity\Ad($this->arParams['ID']);
		}
		else
		{
			$this->letter = new Entity\Letter($this->arParams['ID']);
		}
		if (!$this->letter->getId())
		{
			$this->errors->setError(new Error(Loc::getMessage('SENDER_LETTER_TIME_COMP_ERR_NO_LETTER')));
			return false;
		}


		// Process POST
		if ($this->request->isPost() && check_bitrix_sessid() && $this->arParams['CAN_EDIT'])
		{
			$this->preparePost();
			$this->printErrors();
		}

		$this->arResult['SUBMIT_FORM_URL'] = Context::getCurrent()->getRequest()->getRequestUri();
		$this->arResult['ACTION_URL'] = $this->getPath() . '/ajax.php';
		$this->arResult['TITLE'] = $this->letter->get('TITLE');

		$method = $this->letter->getMethod();
		switch ($method->getCode())
		{
			case Dispatch\Method::TIME:
				/** @var Dispatch\MethodTime $methodInstance */
				$methodInstance = $method->get();
				$code = $methodInstance->getDateTime();
				$this->arResult['DATE_SEND'] = $this->letter->get('DATE_SEND') ?: $this->letter->get('AUTO_SEND_TIME');
				$this->arResult['DATE_SEND'] = PrettyDate::formatDateTime($this->arResult['DATE_SEND']);
				break;

			case Dispatch\Method::SCHEDULE:
				$code = Dispatch\Method::SCHEDULE;
				break;

			case Dispatch\Method::DEFERED:
			default:
				$code = Dispatch\Method::DEFERED;
				$this->arResult['DATE_SEND'] = '';
				break;
		}

		$this->arResult['LETTER_TIME'] = $code;
		$this->arResult['CAN_CHANGE'] = $method->canChange();
		$this->arResult['DAYS_OF_WEEK'] = $this->letter->get('DAYS_OF_WEEK');
		$this->arResult['TIMES_OF_DAY'] = $this->letter->get('TIMES_OF_DAY');
		$this->arResult['TIME_LIST'] = Dispatch\MethodSchedule::getTimeList();
		$this->arResult['IS_SAVED'] = $this->request->get('IS_SAVED') == 'Y';
		$this->arResult['IS_SUPPORT_REITERATE'] = $this->letter->isSupportReiterate();


		$this->arResult['LIMITATION'] = array();
		$transport = $this->letter->getMessage()->getTransport();
		if ($transport->getCode() === $transport::CODE_MAIL)
		{
			if (Integration\Bitrix24\Service::isCloud())
			{
				$limiter = Integration\Bitrix24\Limitation\Limiter::getMonthly();
				$this->arResult['LIMITATION']['TEXT'] = $limiter->getText();
				$this->arResult['LIMITATION']['SETUP_URI'] = $limiter->getParameter('setupUri');
				$this->arResult['LIMITATION']['SETUP_CAPTION'] = $limiter->getParameter('setupCaption');
			}
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