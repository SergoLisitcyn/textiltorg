<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
{
	die();
}

/** @var CAllMain $APPLICATION */
/** @var array $arParams */
/** @var array $arResult */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
use Bitrix\Sender\Integration\Bitrix24;
use Bitrix\Sender\Internals\PrettyDate;
\Bitrix\Main\UI\Extension::load("ui.buttons");

$getMessageLocal = function($messageCode, $replace = []) use ($arParams)
{
	if (empty($arParams['~MESS'][$messageCode]))
	{
		return Loc::getMessage($messageCode, $replace);
	}

	return str_replace(
		array_keys($replace),
		array_values($replace),
		$arParams['~MESS'][$messageCode]
	);
};

Bitrix24\Service::initLicensePopup();

$containerId = 'sender-letter-time';
?>
<div id="<?=htmlspecialcharsbx($containerId)?>" class="sender-letter-time">
	<script type="text/javascript">
		BX.ready(function () {
			BX.Sender.Letter.Time.init(<?=Json::encode(array(
				'containerId' => $containerId,
				'actionUrl' => $arResult['ACTION_URL'],
				'isFrame' => $arParams['IFRAME'] == 'Y',
				'isSaved' => $arResult['IS_SAVED'],
				'isSupportReiterate' => $arResult['IS_SUPPORT_REITERATE'],
				'prettyDateFormat' => PrettyDate::getDateFormat(),
				'mess' => array(
					'atTime' => Loc::getMessage('SENDER_LETTER_TIME_TMPL_AT_TIME'),
					'time' => Loc::getMessage('SENDER_LETTER_TIME_TMPL_TIME'),
					'defered' => Loc::getMessage('SENDER_LETTER_TIME_TMPL_DEFERED'),
					'now' => Loc::getMessage('SENDER_LETTER_TIME_TMPL_NOW'),
					'schedule' => Loc::getMessage('SENDER_LETTER_TIME_TMPL_REITERATE'),
					'accept' => Loc::getMessage('SENDER_LETTER_TIME_TMPL_ACCEPT'),
					'cancel' => Loc::getMessage('SENDER_LETTER_TIME_TMPL_CANCEL'),
					'scheduleText' => Loc::getMessage('SENDER_LETTER_TIME_TMPL_SCHEDULE_TEXT'),
				)
			))?>);
		});
	</script>
	<form method="post" action="<?=htmlspecialcharsbx($arResult['SUBMIT_FORM_URL'])?>">
		<?=bitrix_sessid_post()?>

		<div class="sender-letter-time-title">
			<?
			echo $getMessageLocal(
				'SENDER_LETTER_TIME_TMPL_TITLE_' . (!$arResult['CAN_CHANGE'] ? 'EXISTS' : 'NEW'),
				array(
					'%name%' => '<div class="sender-letter-time-title-highlight">"'
								. htmlspecialcharsbx($arResult['TITLE'])
								. '"</div>'
				)
			);
			?>
		</div>
		<div class="sender-letter-time-icon"></div>
		<div class="sender-letter-time-button-container">
			<div class="sender-letter-time-button" style="<?=($arResult['CAN_CHANGE'] ? 'display: none;' : '')?>">
				<span class="sender-letter-time-button-name"><?=$getMessageLocal('SENDER_LETTER_TIME_TMPL_DATE_SEND')?>:</span>
				<a class="">
					<?=htmlspecialcharsbx($arResult['DATE_SEND'])?>
				</a>
			</div>
			<div class="sender-letter-time-button" style="<?=(!$arResult['CAN_CHANGE'] ? 'display: none;' : '')?>">
				<span class="sender-letter-time-button-name"><?=$getMessageLocal('SENDER_LETTER_TIME_TMPL_ACT_SEND')?>:</span>
				<a data-role="time-selector" class="sender-letter-time-link"></a>
				<input data-role="time-input"  type="hidden" name="LETTER_TIME" value="<?=htmlspecialcharsbx($arResult['LETTER_TIME'])?>">
			</div>
		</div>

		<input data-role="time-reiterate-days-of-week" type="hidden" name="DAYS_OF_WEEK" value="<?=htmlspecialcharsbx($arResult['DAYS_OF_WEEK'])?>">
		<input data-role="time-reiterate-times-of-day" type="hidden" name="TIMES_OF_DAY" value="<?=htmlspecialcharsbx($arResult['TIMES_OF_DAY'])?>">

		<?if (!empty($arResult['LIMITATION'])):?>
			<div class="sender-letter-info">
				<?=htmlspecialcharsbx($arResult['LIMITATION']['TEXT'])?>
				<a href="<?=htmlspecialcharsbx($arResult['LIMITATION']['SETUP_URI'])?>">
					<div class="sender-hint">
						<div class="sender-hint-icon"></div>
					</div>
				</a>
			</div>
		<?endif;?>


		<?
		$APPLICATION->IncludeComponent(
			"bitrix:sender.ui.button.panel",
			"",
			array(
				'SAVE' => array(),
				/*
				'CANCEL' => array(
					'URL' => $arParams['PATH_TO_LIST']
				),
				*/
			),
			false
		);
		?>

	</form>

	<div style="display: none;">
		<div data-role="time-reiterate" class="sender-letter-time-popup">
			<div class="sender-letter-time-popup-time-box">
				<div class="sender-letter-time-popup-time-name"><?=Loc::getMessage('SENDER_LETTER_TIME_TMPL_WEEK_DAYS_TIME')?>:</div>
				<select data-role="reiterate-times-of-day" class="sender-letter-time-popup-time-select">
					<?foreach ($arResult['TIME_LIST'] as $time):
						$time = htmlspecialcharsbx($time);
						?>
						<option value="<?=$time?>" <?=($time === '09:00' ? 'selected' : '')?>><?=$time?></option>
					<?endforeach?>
				</select>
			</div>

			<div class="sender-letter-time-popup-date-box">
				<div class="sender-letter-time-popup-date">
					<?
					$weekDays = [
						['id' => '1', 'name' => Loc::getMessage('SENDER_LETTER_TIME_TMPL_SCHEDULE_DAY_MON'), 'selected' => true],
						['id' => '2', 'name' => Loc::getMessage('SENDER_LETTER_TIME_TMPL_SCHEDULE_DAY_TUE'), 'selected' => true],
						['id' => '3', 'name' => Loc::getMessage('SENDER_LETTER_TIME_TMPL_SCHEDULE_DAY_WED'), 'selected' => true],
						['id' => '4', 'name' => Loc::getMessage('SENDER_LETTER_TIME_TMPL_SCHEDULE_DAY_THU'), 'selected' => true],
						['id' => '5', 'name' => Loc::getMessage('SENDER_LETTER_TIME_TMPL_SCHEDULE_DAY_FRI'), 'selected' => true],
						['id' => '6', 'name' => Loc::getMessage('SENDER_LETTER_TIME_TMPL_SCHEDULE_DAY_SAT'), 'selected' => true],
						['id' => '7', 'name' => Loc::getMessage('SENDER_LETTER_TIME_TMPL_SCHEDULE_DAY_SUN'), 'selected' => true],
					];
					foreach ($weekDays as $weekDay)
					{
						$dayNum = htmlspecialcharsbx($weekDay['id']);
						$dayName = htmlspecialcharsbx($weekDay['name']);
						$daySelected = (bool) $weekDay['selected'];
						?>
						<div class="sender-letter-time-popup-date-item sender-letter-time-popup-date-item-current"
							data-role="reiterate-days-of-week"
							data-value="<?=$dayNum?>"
						>
							<?=$dayName?>
						</div>
						<?
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>