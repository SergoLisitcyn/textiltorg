<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="fancy_block form buy_one_click" id="buy_one_click">
    <form action="<?= $APPLICATION->GetCurPage() ?>" method="post" class="buy-one-click-form">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="ACTION" value="<?=$arResult["ACTION"]?>">
        <input type="hidden" name="GOOD_ID" value="">
        <input type="hidden" name="GOOD_NAME" value="">
        <input type="hidden" name="GOOD_URL" value="">
        <input type="hidden" name="PRICE" value="">
        <input type="hidden" name="CURRENCY" value="">
        <input type="hidden" name="IS_MOBILE" value="1">
        <p>Контактное лицо</p>
        <input type="text" name="NAME" placeholder="Иванов Иван" data-validate="name"/>
        <p>Телефон</p>
        <input type="text" name="PHONE" placeholder="<?=(SITE_ID == "s1") ? "+79261234567" : "+375123456789" ?>" data-validate="phone"/>

        <input type="submit" class="button red" onclick="<?=Helper::GetYandexCounter("oformit_zakaz")?>" value="Оформить заказ">
    </form>
    <div class="offer-block-callback">
        Нажимая на кнопку &laquo;Оформить заказ&raquo;, <br>
        вы принимаете условие <a href="/politika/zakaz-odin-klik" target="_blank">Публичной оферты</a>
    </div>
</div>