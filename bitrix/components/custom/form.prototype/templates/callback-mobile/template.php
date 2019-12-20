<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="fancy_block form form_callback" id="form_callback">
    <form action="<?= $APPLICATION->GetCurPage()?>" method="post" class="callback-form-mb1">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="FORM_ACTION" value="<?=$arResult["FORM_ACTION"]?>">
        <input type="hidden" name="FORM_ID" value="<?=$arResult["FORM_ID"]?>">
        <input type="hidden" name="IS_MOBILE" value="1">
        <p>Контактное лицо</p>
        <input type="text" name="form_text_1" placeholder="Иван Иванов" data-validate="name"/>
        <p>Телефон</p>
        <input type="text" name="form_text_2" placeholder="<?=(SITE_ID == "s1") ? "+79261234567" : "+375123456789" ?>" data-validate="phone"/>
        <button type="submit" class="button yellow" onclick="<?=Helper::GetYandexCounter("callMe_Send")?>">Жду звонка!</button>
    </form>
    <div class="offer-block-callback">
        Нажимая на кнопку &laquo;Жду звонка!&raquo;, <br>
        вы принимаете условие <a href="/politika/zakazat-zvonok" target="_blank">Публичной оферты</a>
    </div>
</div>