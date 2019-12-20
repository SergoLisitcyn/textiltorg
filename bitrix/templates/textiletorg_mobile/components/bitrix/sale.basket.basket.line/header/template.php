<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$isAjax = (isset($_REQUEST["ajax_action_cart"]) && $_REQUEST["ajax_action_cart"] == "Y");
$idHeaderCart = 'headerCart'.$this->randString();

?>
<a class="basket" id="box-header-cart" href="/cart/">
	<?
	if ($isAjax)
		$APPLICATION->RestartBuffer();

	$frame = $this->createFrame($idHeaderCart)->begin('');
	?>
	<?if ($arResult["NUM_PRODUCTS"]):?>
		<div class="basket_sum"><?=$arResult["NUM_PRODUCTS"]?></div>
	<?endif?>
	<?
	$frame->end();

	if ($isAjax)
		die();
	?>
</a>