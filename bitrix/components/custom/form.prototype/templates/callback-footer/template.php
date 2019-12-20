<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="fancybox_block form_callback" id="form_callback_footer">
    <div class="form_callback_block">
        <form action="<?= $APPLICATION->GetCurPage()?>" method="post" class="callback-form-footer" id="<?=$arResult["TAG_MANAGER"]?>">
            <?=bitrix_sessid_post()?>
            <input type="hidden" name="FORM_ACTION" value="<?=$arResult["FORM_ACTION"]?>">
            <input type="hidden" name="FORM_ID" value="<?=$arResult["FORM_ID"]?>">
            <p>Контактное лицо:</p>
            <input type="text" class="i_phone input_callback" name="form_text_1" value="" placeholder="Иванов Иван" data-validate="name"/>
            <p>Телефон:</p>
            <input type="text" class="i_phone input_callback" name="form_text_2" value="" placeholder="<?=(SITE_ID == "s1") ? "+79261234567" : "+79261234567" ?>" data-validate="phone"/>
            <div style="text-align:center">
                <button type="submit" class="button" onclick="<?=Helper::GetYandexCounter("callMe_Send")?>">Жду звонка!</button>
            </div>
        </form>
    </div>
    <div class="offer-block-callback">
        Нажимая на кнопку &laquo;Жду звонка!&raquo;, <br>
        вы принимаете условие <a href="/politika/zakazat-zvonok" target="_blank">Публичной оферты</a>
    </div>
</div>