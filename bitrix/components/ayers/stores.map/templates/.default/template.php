<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<?if ($arResult['ITEMS']):?>
    <p class="as-show-load-map"><a href="#as-stores-map">подробная схема проезда</a></p>
    <div id="as-stores-map"></div>

    <script>
    AS_STORE_MAP_GROUP = <?=$arResult["MAP_GROUP"]?>;
    </script>
<?endif?>