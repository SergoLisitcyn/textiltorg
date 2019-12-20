<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="zakaz_fin">

    <div class="cent">
        Здравствуйте,<?if (defined("IS_MOBILE")):?><br><?endif;?> номер Вашего заказа: <?if (defined("IS_MOBILE")):?><br><?endif;?><span class="red b_font" style="font-size: 150%"><?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?></span><br>
        <b onclick="<?= Helper::GetYandexCounter("oformit_zakaz_korzina", false); ?>">Спасибо, что выбрали нас!</b><br><br>
    </div>

    <?php /* ?>
    <?if ($arResult["PAY_SYSTEM"]["PSA_HAVE_RESULT_RECEIVE"] == "Y"):?>
        <?if ($arResult["ORDER"]["PAYED"] == "N"):?>
            <div class="oplata-center">
                <?if ($arResult["IS_PAY_CALL"] == "Y"):?>
                    <span class="oplata-center-call">Ожидает подтверждения оплаты</span>
                <?else:?>
                    <form method="POST" action="https://pay.textiletorg.ru/create/" id="payment-form">
                        <input type="hidden" name="sum" value="<?=$arResult["ORDER"]["PRICE"]?>"/>
                        <input type="hidden" name="clientid" value="<?=$arResult["ORDER"]["USER_ID"]?>"/>
                        <input type="hidden" name="orderid" value="<?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?>"/>
                        <input type="submit" class="red_button button red" value="Оплатить">
                    </form>
                <?endif?>
            </div>
            <?if ($arResult["IS_AUTO_REDIRECT"] == "Y"):?>
                <script>
                    $('#payment-form').submit();
                </script>
            <?endif?>
        <?else:?>
            <div class="oplata-center">
                <span class="oplata-center-success">Оплачено</span>
            </div>
        <?endif?>
    <?endif?>
    <?php */ ?>

    <? if (!$arResult["MRC"] && $arResult["PAY_SYSTEM"]["ID"] == 6): ?>
        <p style="text-align:center;">Мы благодарим Вас за покупку! Вы выбрали способ оплаты "Банковской картой онлайн". Наш специалист свяжется с Вами в ближайшее время и предоставит защищённую ссылку для безопасной оплаты вашего заказа.</p>
    <? elseif (!$arResult["MRC"] && $arResult["PAY_SYSTEM"]["ID"] == 3): ?>
        <p style="text-align:center;">Мы благодарим Вас за покупку! Вы выбрали способ оплаты "В кредит или рассрочку". Наш специалист свяжется с Вами в ближайшее время и предоставит защищённую ссылку для оформления заявки на кредит.</p>
    <? else: ?>
        <p style="text-align:center;">Мы благодарим Вас за покупку! Один из наших специалистов свяжется с Вами в ближайшее время!</p>
    <? endif; ?>

    <p style="text-align:center;">Наши специалисты работают круглосуточно, время работы магазина с 9:00 до 21:00</p>
<table cellpadding="1" cellspacing="1" align="center" style="width: 500px;">
<tbody>
<tr>
	<td width="250" height="100">
		<p style="float: left; margin: 0 7px 7px 0;">
 <img width="39" alt="tel_icon.jpg" src="/upload/medialibrary/bd6/bd60bac63466ea37a5b4eea8dd70f665.jpg" height="32" align="left" hspace="10" vspace="8">
		</p>
		<p>
 <b>Телефон:</b><br>
			 8 (800) 777-23-68<br>
			 многоканальный 24/7
		</p>
	</td>
	<td width="250" height="100">
		<p style="float: left; margin: 0 7px 7px 0;">
 <img width="35" alt="time_icon.jpg" src="/upload/medialibrary/db0/db014ee810745084f00bfd8052071c5a.jpg" height="33" align="left" hspace="10" vspace="8">
		</p>
		<p>
 <b>График работы:</b><br>
			 Ежедневно с 9:00 до 21:00
		</p>
	</td>
</tr>
<tr>
	<td width="250" height="100">
		<p style="float: left; margin: 0 7px 7px 0;">
 <img width="44" alt="dostavka_icon.jpg" src="/upload/medialibrary/e54/e540968591ba31a18abfa2f48611a37d.jpg" height="45" align="left" hspace="10" vspace="8">
		</p>
		<p>
			Подробнее о <a href="/dostavka/">доставке</a>
		</p>
	</td>
	<td width="250" height="100">
		<p style="float: left; margin: 0 7px 7px 0;">
 <img width="45" alt="samovyvoz_icon.jpg" src="/upload/medialibrary/f09/f095402a49c46ba0297f10368ec5c937.jpg" height="43" align="left" hspace="10" vspace="8">
		</p>
		<p>
			 Подробнее о <a href="/samovyvoz/">самовывозе</a>
		</p>
	</td>
</tr>
</tbody>
</table>

<p style="text-align:center;"><b>Желаем Вам успехов во всем и отличного настроения!<br></b>
Ждем Вас снова, команда «ТекстильТорг»</p>
	<p style="font-size:12px;">В целях повышения качества обслуживания и работы персонала компании, просим Вас сообщать все пожелания, претензии, комментарии и предложения по нашей работе, в службу проверки качества и контроля компании: support@textiletorg.ru</p>
</div>

<!-- GoogleAnalytics target -->
<script> ga('send', 'event', 'orderDone', 'call'); </script>

<script type="text/javascript">
    $(window).load(function() {
        <?= Helper::GetYandexCounter("oformit_zakaz_korzina", false); ?>
    });
</script>

<? if (count($arResult["PRODUCRS_BASKET"]) > 0): ?>

    <!-- Facebook Purchase -->
    <script>
        fbq('track', 'Purchase', {
            content_type: 'product',
            contents: [
                <? foreach($arResult["PRODUCRS_BASKET"] as $arItem): ?>
                { id: <?=$arItem["ORIGIN_PRODUCT_ID"]?>, quantity: <?=$arItem["QUANTITY"]?>, item_price: <?=$arItem["PRICE"]?>},
                <? endforeach;?>
            ],
            value: <?=$arResult["PRODUCTS_SUMM"]?>,
            currency: 'RUB'
        });
    </script>

    <script>
        (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
            try {
                rrApi.order({
                    transaction: <?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?>,
                    items: [
                        <? foreach($arResult["PRODUCRS_BASKET"] as $arItem): ?>
                            { id: <?=$arItem["ORIGIN_PRODUCT_ID"]?>, qnt: <?=$arItem["QUANTITY"]?>, price: <?=$arItem["PRICE"]?>},
                        <? endforeach;?>
                    ]
                });
            } catch(e) {}
        })
    </script>
    
    <script>
        window.criteo_q = window.criteo_q || [];
        var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
		window.criteo_q.push(
			{ event: "setAccount", account: 38714 },
			{ event: "setHashedEmail", email: "<? echo $USER->GetEmail(); ?>" },
			{ event: "setSiteType", type: deviceType },
            { event: "trackTransaction", id: <?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?>, item: [
                <? foreach($arResult["PRODUCRS_BASKET"] as $arItem): ?>
                { id: <?=(!empty($arItem["PRODUCT_XML_ID"])) ? $arItem["PRODUCT_XML_ID"] : $arItem["PRODUCT_ID"]/*$arItem["ORIGIN_PRODUCT_ID"]*/?>, price: <?=$arItem["PRICE"]?>, quantity: <?=$arItem["QUANTITY"]?>},
                <? endforeach;?>
            ]}
        );
    </script>
    <!-- CRITEO -->
<? endif; ?>

<? if (!empty($arResult["ORDER"]["USER_EMAIL"])): ?>
    <script> (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() { rrApi.setEmail("<?=$arResult["ORDER"]["USER_EMAIL"]?>");});</script>
<? endif; ?>

<?$APPLICATION->IncludeComponent(
    "custom:targetmail.prototype",
    "",
    array(
        "ID" => "3077731",
        "PAGE_CATEGORY" => "/catalog/index.php",
        "PAGE_PRODUCT" => "/catalog/detail/index.php",
        "PAGE_CART" => "/^\/cart/",
        "PAGE_PURCHASE" => "/^\/order/",
        "PRODUCT_ID" => $arResult["PRODUCRS_BASKET_XML_ID"],
        "TOTAL_VALUE" => $arResult["PRODUCTS_SUMM"],
        "LIST" => "1"
    ),
    false,
    array(
        "HIDE_ICONS" => "Y"
    )
);?>

<script src="https://www.gdeslon.ru/thanks.js?codes=<?=$arResult["GDESLON_CODES"]?>&order_id=<?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?>&merchant_id=88568"></script>
<script src="https://www.gdeslon.ru/landing.js?mode=thanks?codes=<?=$arResult["GDESLON_CODES"]?>&order_id=<?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?>&mid=88568"></script>

