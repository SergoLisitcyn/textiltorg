<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$isAjax = (isset($_REQUEST["AJAX_SEARCH"]) && $_REQUEST["AJAX_SEARCH"] == "Y");
$idSearch = 'SEARCH'.$this->randString();
?>
<div class="search catalog-search-prototype-default">
    <form class="form" method="get" name="search" id="search-form" action="<?=$arResult["ACTION_FORM"]?>">
        <div class="elements">
            <span style = "position:relative;">
                <input class="search-field txt" id="search_box" type="text" name="QUERY" value="<?=$arResult["QUERY"]?>" autocomplete="off" placeholder="поиск" />
                <i class="tt-icons search-icon"></i>
            </span>
            <input class="search-button scale-decrease" type="submit" value="НАЙТИ" />

        </div>
        <script>
            (function($){
                $(window).on("load",function(){
                    $(".seares").mCustomScrollbar({
                        scrollbarPosition: "outside",
                    });
                });
            })(jQuery);
        </script>
        <div class="seares">
            <div id="searchresults"><span class="word"></span></div>
            <div id="results">
                <?
                if ($isAjax)
                    $APPLICATION->RestartBuffer();

                $frame = $this->createFrame($idSearch)->begin('');
                ?>

                <?if ($arResult["ITEMS"]):?>
                    <ul>
                        <?foreach ($arResult["ITEMS"] as $nItem => $arItem):?>
                            <li>
                                <div class="image">
                                    <a href='<?=$arItem["DETAIL_PAGE_URL"]?>' title=''>
                                        <img alt="" src='<?=$arItem["RESIZE_PICTURE"]["SRC"]?>'>
                                    </a>
                                </div>
                                <div class="name">
                                    <a href='<?=$arItem["DETAIL_PAGE_URL"]?>' title='<?=$arItem["NAME"]?>' class="title"><?=$arItem["TITLE_FORMATED"]?></a>
                                    <div class="sku">Арт. <?=$arItem["PROPERTY_VENDOR_CODE_VALUE"]?></div>
                                    <div class="more"><a href='<?=$arItem["DETAIL_PAGE_URL"]?>'>подробнее...</a></div>
                                </div>
                                <div class="price">
                                    <?if ($arItem["PRINT_PRICE"]):?>
                                        <span class='search-old-prece'><?=$arItem["PRINT_PRICE"]?> р.</span>
                                    <?elseif ($arItem["CATALOG_PRICE_1"]):?>
                                        <span class='search-old-prece'><?=$arItem["CATALOG_PRICE_1"]?> р.</span>
                                    <?else:?>
                                        <span class='search-old-prece'>На заказ</span>
                                    <?endif?>
                                </div>
                            </li>
                        <?endforeach?>
                    </ul>
                <?endif?>
                <?
                $frame->end();

                if ($isAjax)
                    die();
                ?>
            </div>
        </div>
    </form>
</div>