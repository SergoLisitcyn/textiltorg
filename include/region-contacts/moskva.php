<div class="kontakty" itemscope="" itemtype="http://schema.org/Organization">
    <p class="title">Ваш гид</p>
    <div class="item">
        <p><b>от станции метро МЦК</b></p>
        <iframe allowfullscreen="allowfullscreen" src="https://www.youtube.com/embed/WKinKeP8vTU" width="590" height="396"></iframe>
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
            <li><b itemprop="telephone">8 (800) 333-71-83</b></li>
            <li>(круглосуточно, без выходных)</li>
        </ul>
    </div>
    <div class="item right">
        <p><b>от станции метро Автозаводская</b></p>
        <iframe allowfullscreen="allowfullscreen" src="https://www.youtube.com/embed/ScHdPM3khxE" width="600" height="396"></iframe>
        <p class="title">Адреса наших магазинов:</p>
        <div itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress">
            <ul>
                <li>Адрес нашего магазина:</li>
                <li><b>г. <span itemprop="addressLocality">Москва</span>, <span itemprop="streetAddress">улица Трофимова, д.3</span></b> ( <span class="icon-metro"></span>ст. м. "Автозаводская")</li>
                <li>График работы:</li>
                <li><time itemprop="hoursAvailable" datetime="Mo,Su 09:00−21:00">с 09:00 до 21:00 (без выходных)</time></li>
            </ul>
        </div>
    </div>
</div>
<div class="clear"></div>
<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "footer-phone",
    Array(
        "AREA_FILE_SHOW" => "file",
        "PATH" => "/include/maps/moskva.php"
    )
);?>
