<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$isAjax = (isset($_REQUEST["AJAX_SEARCH"]) && $_REQUEST["AJAX_SEARCH"] == "Y");
$idSearch = 'SEARCH'.$this->randString();
?>

<div class="search_block">
    <form method="get" name="search" id="search-form" action="<?=$arResult["ACTION_FORM"]?>">
        <input id="search_box" type="text" name="QUERY" value="<?=$arResult["QUERY"]?>" autocomplete="off" placeholder="Введите название товара" />
        <input type="submit" value="Поиск" />
    </form>
        <div class="seares">
			<div class="close"></div>
            <div id="searchresults">Результаты для <span class="word"></span></div>
            <div id="results">
                <?
                if ($isAjax)
                    $APPLICATION->RestartBuffer();

                $frame = $this->createFrame($idSearch)->begin('');
                ?>

                <?if ($arResult["ITEMS"]):?>
                    <?foreach ($arResult["ITEMS"] as $nItem => $arItem):?>
                        <table>
                            <tr>
                                <td valign='center' align='center' width='5%' style='color:#9F9F9F; text-align: center;'><?=$nItem + 1?> .</td>
                                <td valign='middle' align='center' width='25%'>
                                    <a href='<?=$arItem["DETAIL_PAGE_URL"]?>' title=''>
                                        <img alt="" src='<?=$arItem["RESIZE_PICTURE"]["SRC"]?>' style='max-width: 60px;'>
                                    </a>
                                </td>
                                <td valign='top' width='50%'>
                                    <a href='<?=$arItem["DETAIL_PAGE_URL"]?>' title='<?=$arItem["NAME"]?>'><?=$arItem["TITLE_FORMATED"]?></a><br>
                                    <span style='color: #FF6600;font-size: 10px;font-weight:normal;display: block;margin: 8px 0;'>Арт. <?=$arItem["PROPERTY_VENDOR_CODE_VALUE"]?></span><br>
                                    <?=$arItem["BODY_FORMATED"]?><br>
                                    <a href='<?=$arItem["DETAIL_PAGE_URL"]?>' style='text-decoration:none;font-size: 18px;padding: 15px 0 10px;display: block;'><small>подробнее...</small></a>
                                </td>
                                <td valign='middle' align='center' width='20%' style='text-align: center;min-width:100px'>
                                    <?if ($arItem["PRINT_PRICE"]):?>
                                        <span></span><span class='search-old-prece' style="color: #ff0000;font-weight: 600;"><?=$arItem["PRINT_PRICE"]?> р.</span>
                                    <?else:?>
                                        <span></span><span class='search-old-prece' style="color: #ff0000;font-weight: 600;">На заказ</span>
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
</div>