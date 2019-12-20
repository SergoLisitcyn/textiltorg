<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("sale");

if (!empty($_POST["ACTION"]))
{
    switch ($_POST["ACTION"]) {
        case 'GET_SUM':
            $rsCart = CSaleBasket::GetList(
                false,
                array(
                    "FUSER_ID" => $_SESSION["SALE_USER_ID"],
                    "LID" => SITE_ID,
                    "ORDER_ID" => "NULL",
                    "DELAY" => "N",
                    "CAN_BUY" => "Y"
                ),
                false,
                false,
                array("ID" , "PRICE", "QUANTITY")
            );

            while ($arCartItem = $rsCart->Fetch())
            {
                $arResult["BASKET_COUNT_PRODUCT"]++;
                $arResult["BASKET_SUM"] += $arCartItem["PRICE"] * $arCartItem["QUANTITY"];
            }

            if (SITE_ID == "by")
            {
                $arResult["BASKET_SUM_FORMAT"] = number_format($arResult["BASKET_SUM"], 2, ",", " ");
            }
            else
            {
                $arResult["BASKET_SUM_FORMAT"] = number_format($arResult["BASKET_SUM"], 0, ".", " ");
            }
?>
            <span class="field-wrap" data-sum="<?=$arResult["BASKET_SUM"]?>"><?=$arResult["BASKET_SUM_FORMAT"]?> руб.</span>
            <?if ($arResult["BASKET_SUM_RB_FORMAT"]):?>
                <div class="call-full-summ-rb"><?=$arResult["BASKET_SUM_RB_FORMAT"]?> <small>руб.</small></div>
            <?endif?>
<?
            break;
    }
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>