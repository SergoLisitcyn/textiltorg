<?php
$frame = $this->createFrame()->begin();
$arHouseCities = array(
    "Москва" => "+7 (495) 662-97-87",
    "Санкт-Петербург" => "+7 (812) 389-33-30",
    "Екатеринбург" => "+7 (343) 357-90-87",
    "Нижний Новгород" => "+7 (831) 262-14-45",
    "Ростов-на-Дону" => "+7 (863) 303-43-33",
    "Новосибирск" => "+7 (383) 209-64-54"
); ?>
<div itemscope itemtype="http://schema.org/Organization">
<?php
if (array_key_exists($_SESSION["GEO_REGION_CITY_NAME"], $arHouseCities)) { ?>
    <span itemprop="telephone"><p class="callibri_phone_5"><?= $arHouseCities[$_SESSION["GEO_REGION_CITY_NAME"]]; ?></p></span>
    <span style="text-align: center;">Консультация и заказ 24/7</span>
<?php } else { ?>
    <span itemprop="telephone"><p class="callibri_phone_5">8 (800) 333-71-83</p></span>
    <span>Бесплатный звонок по РФ / 24 часа</span>
<?php }

$frame->beginStub();
echo "...";
$frame->end();?>
</div>
<!--
<div itemscope itemtype="http://schema.org/Organization"><span itemprop="telephone"><p class="callibri_phone_5">8 (800) 333-71-83</p></span>
<span>Бесплатный звонок по РФ / 24 часа</span></div>
-->
