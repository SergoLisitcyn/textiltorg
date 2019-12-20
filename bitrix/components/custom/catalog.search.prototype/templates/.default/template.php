<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$isAjax = (isset($_REQUEST["AJAX_SEARCH"]) && $_REQUEST["AJAX_SEARCH"] == "Y");
$idSearch = 'SEARCH'.$this->randString();
?>

<div class="search catalog-search-prototype-default">
    <form class="form" method="get" name="search" id="search-form" action="<?=$arResult["ACTION_FORM"]?>">
        <div>
            <input class="search_field txt" id="search_box" type="text" name="QUERY" value="<?=$arResult["QUERY"]?>" autocomplete="off" placeholder="поиск товара" />
        </div>
        <div>
            <input class="search_button scale-decrease" type="submit" value="Поиск" />
        </div>
		<script>
			(function($){
				$(window).on("load",function(){
					$(".seares").mCustomScrollbar({
						theme:"yellow-red",
						scrollButtons:{enable:true},
					});
				});
			})(jQuery);
		</script>
        <div class="seares">
            <div id="searchresults">Результаты для <span class="word"></span></div>
            <div id="results">
                <?
                if ($isAjax)
                    $APPLICATION->RestartBuffer();

                $frame = $this->createFrame($idSearch)->begin('');
                ?>

                <?if ($arResult["ITEMS"]):?>
                    <?foreach ($arResult["ITEMS"] as $nItem => $arItem):

                        dumpLog($arItem);
                        ?>
                        <table>
                            <tr>
                                <td valign='center' align='center' width='25' style='color:#9F9F9F; text-align: center;'><?=$nItem + 1?> .</td>
                                <td valign='middle' align='center' width='70'>
                                    <a href='<?=$arItem["DETAIL_PAGE_URL"]?>' title=''>
                                        <img alt="" src='<?=$arItem["RESIZE_PICTURE"]["SRC"]?>' style='max-width: 60px;'>
                                    </a>
                                </td>
                                <td valign='top' width='340px'>
                                    <a href='<?=$arItem["DETAIL_PAGE_URL"]?>' title='<?=$arItem["NAME"]?>'><?=$arItem["TITLE_FORMATED"]?></a><br>
                                    <span style='color: #FF6600;font-size: 10px;font-weight:normal;'>Арт. <?=$arItem["PROPERTY_VENDOR_CODE_VALUE"]?></span><br>
                                    <?=$arItem["BODY_FORMATED"]?><br>
                                    <a href='<?=$arItem["DETAIL_PAGE_URL"]?>' style='text-decoration:none;'><small>подробнее...</small></a>
                                </td>
                                <td valign='middle' align='center' width='80px' style='text-align: center;'>
                                    <?if ($arItem["PRINT_PRICE"]):?>
                                        <span></span><span class='search-old-prece'><?=$arItem["PRINT_PRICE"]?> р.</span>
                                    <?elseif ($arItem["CATALOG_PRICE_1"]):?>
                                        <span></span><span class='search-old-prece'><?=$arItem["CATALOG_PRICE_1"]?> р.</span>

                                    <?else: ?>
                                        <span></span><span class='search-old-prece'>На заказ</span>

                                    <?endif?>
                                </td>
                            </tr>
                        </table>
                    <?endforeach?>
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