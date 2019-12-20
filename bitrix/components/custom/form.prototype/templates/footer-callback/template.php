<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$isMobile = (defined("IS_MOBILE")) ? 1 : 0;
?>


<form action="<?= $APPLICATION->GetCurPage()?>" method="post" id="footer-callback-form">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="FORM_ACTION" value="<?=$arResult["FORM_ACTION"]?>">
	<input type="hidden" name="FORM_ID" value="<?=$arResult["FORM_ID"]?>">
    <input type="hidden" name="IS_MOBILE" value="<?=$isMobile?>">
	<input type="text" class="i_phone input_callback <?=(SITE_ID == "by") ? "phone-by" : "phone-ru"?>" name="form_text_2" value="" placeholder="<?=(SITE_ID == "by") ? "+375 000000000" : "+7 (000) 000-00-00"?>" data-validate="phone"/>

    <button type="submit" onclick="<?=Helper::GetYandexCounter(strval($arResult["YANDEX_COUNER"]))?>">Жду звонка!</button>
    <div class="info">
        Наш эксперт перезвонит Вам через <b>28 секунд</b> и ответит на все возникшие вопросы!
    </div>

	<div class="offer-block">
		Нажимая на кнопку
		&laquo;Жду звонка!&raquo;, вы принимаете
		условие <a href="/politika/ostalis-voprosy" target="_blank">Публичной оферты</a>
	</div>
</form>
