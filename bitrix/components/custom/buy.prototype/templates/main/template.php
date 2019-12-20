<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="fancybox_block buy_one_click" id="buy-one-click-form">
    <div class="buy_one_click_block">
        <form action="<?=$APPLICATION->GetCurPage()?>" method="post" class="buy-one-click-form" >
            <?=bitrix_sessid_post()?>
            <input type="hidden" name="ACTION" value="<?=$arResult["ACTION"]?>">
            <input type="hidden" name="GOOD_ID" value="">
            <input type="hidden" name="GOOD_NAME" value="">
            <input type="hidden" name="GOOD_URL" value="">
            <input type="hidden" name="PRICE" value="">
            <input type="hidden" name="CURRENCY" value="">
            <input type="hidden" name="IS_MOBILE" value="0">

            <p>Контактное лицо:</p>
            <input type="text" class="i_phone input_callback" name="NAME" value="" placeholder="Иванов Иван" data-validate="name"/>
            <p>Телефон:</p>
            <input type="text" class="i_phone input_callback" name="PHONE" value="" placeholder="+79261234567" data-validate="phone"/>

            <div class="center">
                <input type="submit" onclick="<?=Helper::GetYandexCounter("oformit_zakaz")?>"  class="red_button"  value="Оформить заказ">
            </div>
        </form>
    </div>
    <div class="offer-block-callback">
        Нажимая на кнопку &laquo;Оформить заказ&raquo;, <br>
        вы принимаете условие <a href="/politika/zakaz-odin-klik" target="_blank">Публичной оферты</a>
    </div>
</div>
