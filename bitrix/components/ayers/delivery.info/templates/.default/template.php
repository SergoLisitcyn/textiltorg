<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<?if ($arResult['INFO']):?>
<div id="as-delivery-info">
    <strong class="as-delivery-info-title">Дополнительная информация по г. <?=$arResult['CITY']?></strong>
    <ul>
        <?foreach ($arResult['INFO'] as $arInfo):?>
            <li><?=$arInfo['TITLE']?>: <strong><?=$arInfo['VALUE']?></strong></li>
        <?endforeach?>
    </ul>
    <small class="as-delivery-info-hint">Ваш IP: <?=$arResult['IP']?></small>
</div>
<?endif?>