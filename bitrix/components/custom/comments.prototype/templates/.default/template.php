<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div id="item_rev">
	<div class="comments">
		<?if ($arParams["ELEMENT_NAME"]):?>
			<div class="title-comments">Отзывы покупателей про <?=$arParams["ELEMENT_NAME"]?></div>
		<?endif?>
		<?if ($arResult["ITEMS"]):?>
			<ul>
				<?foreach ($arResult["ITEMS"] as $arItem):?>
					<li>
						<div class="comm" itemprop="review" itemscope itemtype="http://schema.org/Review">
							<div class="name">
								<span>
                                    <meta itemprop="itemReviewed" content="<?=$arParams["ELEMENT_NAME_CLEAN"]?>"/>
									<span itemprop="author"><?=$arItem["NAME"]?></span>
								</span>
								<meta itemprop="datePublished" content="<?=date("c", strtotime($arItem["DATE"]));?>" />
								<span class="time"><?=$arItem["DATE"]?></span>
							</div>
							<div class="txt">
								<p itemprop="description"><?=$arItem["QUESTION"]?></p>
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
		<div class="leavefeedback"><a href="#addcomment" class="fancybox" title="Новое сообщение">Оставить свой отзыв</a></div>
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

        <p>Вопрос*:</p>
        <textarea rows="10" cols="100" name="QUESTION" data-validate="question"></textarea>

        <div class="rating" id="viewRate">
            <label>Оцените товар:</label>
            <div class="ik_select select_black">
                <select style="position: absolute; margin: 0px; padding: 0px; left: -9999px; top: 0px;" class="select_noautowidth2" name="RATING" id="rateSelect">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5" selected="selected">5</option>
                </select>
            </div>
            <span>(5 - отлично, 1 - оч. плохо)</span>
            <div class="clear"></div>
        </div>
        <div class="send">
            <input type="submit" name="send" value="Отправить" class="button">
        </div>
    </form>
</div>