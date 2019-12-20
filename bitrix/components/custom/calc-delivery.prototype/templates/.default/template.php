<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="delivery-button-container">
    <a href="#form_delivery" class="button scale-decrease calc-open-fancybox" title="Расчет доставки">Расчет доставки</a>
</div>
<div id="form_delivery" class="fancybox_block">
    <span class="calk_descrip">Данный инструмент поможет Вам рассчитать стоимость доставки до Вашего города.</span>

    <div class="pv_block">
        <div class="pv_block_name">
            <label>Город получателя</label>
        </div>
        <div class="pv_block_value">
            <div class="select_fon">
                <select class="result_from" id="calc-regions" name="REGION"></select>
            </div>
        </div>
    </div>

    <div class="pv_block">
        <div class="pv_block_name">
            <label>Категория</label>
        </div>
        <div class="pv_block_value">
            <div class="select_fon">
                <select class="result_from" id="calc-sections" name="SECTIONS"></select>
            </div>
        </div>
    </div>

    <div class="pv_block pv_block_brend" id="brand-container">
        <div class="pv_block_name">
            <label>Бренд</label>
        </div>
        <div class="pv_block_value">
            <div class="select_fon">
                <select class="result_from" id="calc-brands" name="BRAND"></select>
            </div>
        </div>
    </div>

    <div class="pv_block pv_block_model" >
        <div class="pv_block_name">
            <input class="res_from" type="hidden" />
            <label>Модель</label>
        </div>
        <div class="pv_block_value">
            <div class="select_fon">
                <select class="result_from" id="calc-goods" name="MODEL"></select>
            </div>
        </div>
    </div>

    <div id="calc-weight">

    </div>

    <hr />

    <div class="content_confirm_main_buttons">
        <button id="calc-button" class="button" type="submit" value="Рассчитать">Рассчитать</button>
        <button data-url="<?=$arResult["URL_ADD_TO_CART"]?>" data-id="<?=$arResult["ELEMENT_ID"]?>" id="calc-button-order" class="red_button">Оформить заказ</button>
    </div>

    <div id="calc-result">

    </div>

    <script>
        CALC_JSON = <?=$arResult["JSON"]?>;
    </script>
</div>
