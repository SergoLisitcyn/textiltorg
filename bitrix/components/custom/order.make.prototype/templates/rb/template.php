<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->SetTitle("Заказ оформлен");
?>
<div class="zakaz_fin">
    <div class="cent">
    Здравствуйте, номер Вашего заказа: <span class="red b_font" style="font-size: 150%">Б<?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?></span><br>
        <b>Спасибо, что выбрали нас!</b><br><br>
    </div>

    <p style="text-align:center;">Мы благодарим Вас за покупку! Один из наших специалистов свяжется с Вами в ближайшее время!
<br>Наши специалисты работают круглосуточно, время работы магазина с 9:00 до 21:00</p>
<table cellpadding="1" cellspacing="1" align="center" style="width: 500px;">
<tbody>
<tr>
	<td width="250" height="100">

 <img width="35" alt="tel_icon.jpg" src="/upload/medialibrary/bd6/bd60bac63466ea37a5b4eea8dd70f665.jpg" height="33" style="float: left; margin: 0 7px 7px 0;">

		<p>
 <b>Телефон:</b><br>
			 +375 (17) 388-40-64<br>
			 многоканальный 24/7
		</p>
	</td>
	<td width="250" height="100">
 <img width="35" alt="time_icon.jpg" src="/upload/medialibrary/db0/db014ee810745084f00bfd8052071c5a.jpg" height="33" style="float: left; margin: 0 7px 7px 0;">
		<p>
 <b>График работы:</b><br>
			 Ежедневно с 9:00 до 21:00
		</p>
	</td>
</tr>
<tr>
	<td width="250" height="100">
 <img width="44" alt="dostavka_icon.jpg" src="/upload/medialibrary/e54/e540968591ba31a18abfa2f48611a37d.jpg" height="45" style="float: left; margin: 0 7px 7px 0;">

		<p>
			Подробнее о <a href="/dostavka/">доставке</a>
		</p>
	</td>
	<td width="250" height="100">
 <img width="44" alt="samovyvoz_icon.jpg" src="/upload/medialibrary/f09/f095402a49c46ba0297f10368ec5c937.jpg" height="43" style="float: left; margin: 0 7px 7px 0;">

		<p>
			 Подробнее о <a href="/samovyvoz/">самовывозе</a>
		</p>
	</td>
</tr>
</tbody>
</table>
<p style="text-align:center;"><b>Желаем Вам успехов во всем и отличного настроения!<br></b>
Ждем Вас снова, команда «ТекстильТорг»</p>
<p style="font-size:12px;">В целях повышения качества обслуживания и работы персонала компании, просим Вас сообщать все пожелания, претензии, комментарии и предложения по нашей работе, в службу проверки качества и контроля компании: support@textiletorg.by</p>
</div>

<?$APPLICATION->IncludeComponent(
    "custom:targetmail.prototype",
    "",
    array(
        "ID" => "3077731",
        "PAGE_CATEGORY" => "/catalog/index.php",
        "PAGE_PRODUCT" => "/catalog/detail/index.php",
        "PAGE_CART" => "/^\/cart/",
        "PAGE_PURCHASE" => "/^\/order/",
        "PRODUCT_ID" => $arResult["PRODUCRS_BASKET_ID"],
        "TOTAL_VALUE" => $arResult["PRODUCTS_SUMM"],
        "LIST" => "1"
    ),
    false,
    array(
        "HIDE_ICONS" => "Y"
    )
);?>
