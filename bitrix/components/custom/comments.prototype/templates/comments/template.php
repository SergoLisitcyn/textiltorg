<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div id="item_rev">
    <div class="comments">
        <h4 class="title-comments">Комментарии</h4>
        <?if ($arResult["ITEMS"]):?>
            <ul>
                <?foreach ($arResult["ITEMS"] as $arItem):?>
                    <li>
                        <div class="comm" itemprop="review" itemscope itemtype="//schema.org/Review">
                            <div class="name">
                                <span>
                                    <span id="bba3108" itemprop="author"><?=$arItem["NAME"]?></span>
                                </span>
                                <span class="time" itemprop="datePublished"><?=$arItem["DATE"]?></span>
                            </div>
                            <div class="txt">
                                <span itemprop="description"><?=$arItem["QUESTION"]?></span>
                            </div>
                        </div>
                    </li>
                <?endforeach?>
            </ul>
        <?else:?>
            <div class="panel">
                <div class="comm">
                    <p>Нет комментариев</p>
                </div>
            </div>
        <?endif?>
        <div class="leavefeedback"><a href="#addcomment" class="fancybox" title="Новое сообщение">Оставить комментарий</a></div>
    </div>
</div>

<div id="addcomment" class="fancybox_block">
    <form action="<?= $APPLICATION->GetCurPage()?>" method="post" id="comments-form">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="AJAX_COMMENTS" value="Y">
        <input type="hidden" name="ELEMENT_ID" value="<?=$arResult["ELEMENT_ID"]?>">
        <p>Имя*:</p>
        <input type="text" class="w170" name="NAME" data-validate="name">

        <p>Телефон:</p>
        <input type="text" class="w170" name="PHONE">

        <p>Комментарий*:</p>
        <textarea rows="10" cols="100" name="QUESTION" data-validate="question"></textarea>

        <div class="send">
            <input type="submit" name="send" value="Отправить" class="button">
        </div>
    </form>
</div>