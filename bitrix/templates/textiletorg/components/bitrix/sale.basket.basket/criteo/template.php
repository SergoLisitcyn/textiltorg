<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if($arResult["ITEMS"]["AnDelCanBuy"]):?>
    <script src="//static.criteo.net/js/ld/ld.js" async></script>
    <script>
        window.criteo_q = window.criteo_q || [];
        window.criteo_q.push(
            { event: "setAccount", account: 38714 },
            { event: "setEmail", email: "m.chernishov@textiletorg.ru" },
            { event: "setSiteType", type: "d" },
            { event: "viewBasket", item: [
                <? foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arItem): ?>
                { id: <?=(!empty($arItem["PRODUCT_XML_ID"])) ? $arItem["PRODUCT_XML_ID"] : $arItem["PRODUCT_ID"]?>, price: <?=$arItem["PRICE"]?>, quantity: <?=$arItem["QUANTITY"]?>},
                <? endforeach;?>
            ]}
        );
    </script>
<?endif?>
