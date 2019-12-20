<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

    <form action="<?= $APPLICATION->GetCurPage()?>" method="post" class="callback-form">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="FORM_ACTION" value="<?=$arResult["FORM_ACTION"]?>">
        <input type="hidden" name="FORM_ID" value="<?=$arResult["FORM_ID"]?>">
        <input type="hidden" name="IS_MOBILE" value="1">
		<input type="text" name="form_text_1" value="Без имени" style="display:none"/>
        <input type="text" name="form_text_2" placeholder="<?=(SITE_ID == "s1") ? "+7 (000) 000-00-00" : "+375123456789" ?>" data-validate="phone"/>
		<script>var yaSendCatalogDetail=<?=json_encode(Helper::GetYandexCounter(strval($arResult["YANDEX_COUNER"])))?>;</script>
        <input type="submit" value="Жду звонка!" onclick="<?=Helper::GetYandexCounter("callMe_Send")?>">
    </form>