<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div id="competition" class="fancybox_block fancy_block">
    <form action="<?= $APPLICATION->GetCurPage() ?>" method="post" id="form-competition" class="form-competition">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="FORM_ACTION" value="<?=$arResult["FORM_ACTION"]?>">
        <input type="hidden" name="FORM_ID" value="<?=$arResult["FORM_ID"]?>">
        <label for="fio">Ф.И.О <span class="red">*</span></label>
        <input type="text" value="" name="form_text_6" id="fio" data-validate="name">
        <div class="blocks-form-inner">
            <div>
                <label for="city">Индекс<span class="red">*</span></label>
                <input type="text" value="" name="form_text_7" id="index" required>
            </div>

            <div>
                <label for="city">Город<span class="red">*</span></label>
                <input type="text" value="" name="form_text_8" id="city" required>
            </div>

            <div>
                <label for="street">Улица<span class="red">*</span></label>
                <input type="text" value="" name="form_text_9" id="street" required>
            </div>

            <div>
                <label for="house">Дом<span class="red">*</span></label>
                <input type="text" value="" name="form_text_10" id="house" required>
            </div>

            <div>
                <label for="build">Корп</label>
                <input type="text" value="" name="form_text_11" id="build">
            </div>

            <div>
                <label for="flat">Кв</label>
                <input type="text" value="" name="form_text_12" id="flat">
            </div>

            <div>
                <label for="phone">Телефон<span class="red">*</span></label>
                <input type="text" value="" name="form_text_13" id="phone" placeholder="+79261234567" data-validate="phone">
            </div>

            <div>
                <label for="email">E-mail<span class="red">*</span></label>
                <input type="text" value="" name="form_text_14" id="email" data-validate="email">
            </div>
        </div>

        <label for="about" class="yandex_pre">О себе</label>
        <textarea name="form_textarea_15" id="about"></textarea>

        <input type="submit" class="red_button" value="Оставить заявку на участие в конкурсе">
    </form>
</div>