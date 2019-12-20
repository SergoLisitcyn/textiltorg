<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$isAjax = (isset($_REQUEST["ajax_action_line_cart"]) && $_REQUEST["ajax_action_line_cart"] == "Y");
$idLineCart = 'lineCart'.$this->randString();

?>
<a id="box-line-cart" class="bx-basket bx-opener htb-cart basket-header-block" href="/cart/">
    <span class="tt-icons cart-icon"></span>
<?
if ($isAjax)
{
    $APPLICATION->RestartBuffer();
}
//$frame = $this->createFrame($idLineCart)->begin('');
?>
<?if ($arParams['SHOW_NUM_PRODUCTS'] == 'Y' && ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y')):?>
	<div class="header-cart">
		<span class="header-cart-count">
            В КОРЗИНЕ
			<div id="eshop-cart-count-top"><?= ($arResult['NUM_PRODUCTS'] == 0) ? "" : $arResult['NUM_PRODUCTS']?>
                <?if ($arResult['NUM_PRODUCTS'] > 0):?>
                    <span class="text">шт.</span>
                <?else:?>
                    <span class="text">пусто</span>
                <?endif?>
            </div>
            <div class="header-cart-sum"><?=str_replace("руб.","",$arResult['TOTAL_PRICE']);?></div>
		</span>
	</div>
<?endif?>

<?
//$frame->end();

if ($isAjax)
    die();
?>
</a>