<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (count($arResult["ITEMS"]) > 1):?>
    <div class="description_block comments">
        <div class="button yellow acc">Отзывы <span class="counts"><?=$arResult["COUNT"]?></span></div>
        <div class="description_block_content rew">
            <?if ($arResult["ITEMS"]):?>
                <?foreach ($arResult["ITEMS"] as $arItem):?>
                    <div class="rew_item">
                        <p class="name"><?=$arItem["NAME"]?> <span class="date">(<?=$arItem["DATE"]?>)</span></p>
                        <p><?=$arItem["QUESTION"]?></p>
                    </div>
                <?endforeach?>
            <?else:?>
                <p>Нет комментариев</p>
            <?endif?>

            <div class="rew_sub">
                <a href="#rew-all" class="rew_all hide">Читать далее ></a>
                <a href="#recall" class="button yellow fancybox" title="Оставить отзыв">Оставить свой отзыв</a>
            </div>
        </div>
    </div>

    <div id="recall" class="fancy_block form">
        <form action="<?= $APPLICATION->GetCurPage()?>" method="post" id="comments-form">
            <?=bitrix_sessid_post()?>
            <input type="hidden" name="AJAX_COMMENTS" value="Y">
            <input type="hidden" name="ELEMENT_ID" value="<?=$arResult["ELEMENT_ID"]?>">
            <p>Контактное лицо <span class="red">*</span></p>
            <input type="text" name="NAME" placeholder="Иванов Иван" data-validate="name"/>
            <p>Телефон</p>
            <input type="text" name="PHONE" placeholder="+79261234567"/>
            <p>Комментарий <span class="red">*</span></p>
            <textarea name="comment" name="QUESTION" placeholder="Ваш вопрос" data-validate="question"></textarea>
            <div class="reiting_block">
                <p>Оценка</p>
                <select class="reiting" name="RATING">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5" selected="selected">5</option>
                </select>
                (5 - отлично, 1 - оч. плохо)
            </div>
            <input type="submit" value="Отправить" class="button red">
        </form>
    </div>
<?endif?>