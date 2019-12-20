<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="region-select-prototype-right-column">	<div class="ic"></div>
    <a href="/poluchenie/nashi-magaziny/">&nbsp;</a>
    <div><span class="b">Наши магазины</span></div>
    <?php $frame = $this->createFrame()->begin(); ?>
        в&nbsp;г.&nbsp;<span><?=$arResult["GEO_REGION_CITY_NAME"]?></span>
    <?php
        $frame->beginStub();
        echo "...";
        $frame->end();
    ?>
</div>