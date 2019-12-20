<div class="kontakty" itemscope="" itemtype="http://schema.org/Organization">
    <p class="title">Ваш гид</p>
    <div class="item center">
        <p><b>от станции метро пл. Гарина-Михайловского</b></p>
        <iframe allowfullscreen="allowfullscreen" src="https://www.youtube.com/embed/ERgkh0qIOWU" width="1200" height="396"></iframe>
        <div class="left mg-right">
            <?$APPLICATION->IncludeComponent(
                "custom:region-select.prototype",
                "footer",
                Array(
                    "DEFAULT_REGION_CITY_NAME" => "Москва",
                    "DEFAULT_REGION_ID" => "19"
                ),
                false,
                Array(
                    'HIDE_ICONS' => 'Y'
                )
            );?>
            <ul>
                <li>Консультация и прием заказов:</li>
                <li><b itemprop="telephone">+7 (383) 209-64-54</b></li>
                <li>(круглосуточно, без выходных)</li>
            </ul>
        </div>

        <div class="left">
            <p class="title">Адреса наших магазинов:</p>
            <div itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress">
                <ul>
                    <li>Адрес нашего магазина:</li>
                    <li><b>г. <span itemprop="addressLocality">Новосибирск</span>, <span itemprop="streetAddress">улица Ленина д.55</span></b></li>
                    <li>График работы:</li>
                    <li><time itemprop="hoursAvailable" datetime="Mo,Su 09:00−21:00">с 09:00 до 21:00 (без выходных)</time></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "footer-phone",
    array(
        "AREA_FILE_SHOW" => "file",
        "PATH" => "/include/maps/novosibirsk.php"
    )
);?>