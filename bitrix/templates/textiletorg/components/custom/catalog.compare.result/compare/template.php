<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$isAjax = ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["ajax_action"]) && $_POST["ajax_action"] == "Y");

$templateData = array(
	'TEMPLATE_THEME' => $this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css',
	'TEMPLATE_CLASS' => 'bx_'.$arParams['TEMPLATE_THEME']
);

?><div class="bx_compare <? echo $templateData['TEMPLATE_CLASS']; ?>" id="bx_catalog_compare_block"><?
if ($isAjax)
{
	$APPLICATION->RestartBuffer();
}
?>
<div class="bx_sort_container">
	<div class="sorttext"><?=GetMessage("CATALOG_SHOWN_CHARACTERISTICS")?>:</div>
	<a class="sortbutton<? echo (!$arResult["DIFFERENT"] ? ' current' : ''); ?>" href="<? echo $arResult['COMPARE_URL_TEMPLATE'].'DIFFERENT=N'; ?>" rel="nofollow"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></a>
	<a class="sortbutton<? echo ($arResult["DIFFERENT"] ? ' current' : ''); ?>" href="<? echo $arResult['COMPARE_URL_TEMPLATE'].'DIFFERENT=Y'; ?>" rel="nofollow"><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></a>
</div>
<?
if (!empty($arResult["ALL_FIELDS"]) || !empty($arResult["ALL_PROPERTIES"]) || !empty($arResult["ALL_OFFER_FIELDS"]) || !empty($arResult["ALL_OFFER_PROPERTIES"]))
{
?>
<div class="bx_filtren_container">
	<div class="compare-params-label"><?=GetMessage("CATALOG_COMPARE_PARAMS")?></div>
	<ul><?
	if (!empty($arResult["ALL_FIELDS"]))
	{
		foreach ($arResult["ALL_FIELDS"] as $propCode => $arProp)
		{
			if (!isset($arResult['FIELDS_REQUIRED'][$propCode]))
			{
		?>
		<li><span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arProp["ACTION_LINK"])?>')">
			<span><input type="checkbox" id="PF_<?=$propCode?>"<? echo ($arProp["IS_DELETED"] == "N" ? ' checked="checked"' : ''); ?>></span>
			<label for="PF_<?=$propCode?>"><?=GetMessage("IBLOCK_FIELD_".$propCode)?></label>
		</span></li>
		<?
			}
		}
	}
	if (!empty($arResult["ALL_OFFER_FIELDS"]))
	{
		foreach($arResult["ALL_OFFER_FIELDS"] as $propCode => $arProp)
		{
			?>
			<li>
				<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arProp["ACTION_LINK"])?>')">
					<span><input type="checkbox" id="OF_<?=$propCode?>"<? echo ($arProp["IS_DELETED"] == "N" ? ' checked="checked"' : ''); ?>></span>
					<label for="OF_<?=$propCode?>"><?=GetMessage("IBLOCK_OFFER_FIELD_".$propCode)?></label>
				</span>
			</li>
		<?
		}
	}
	if (!empty($arResult["ALL_PROPERTIES"]))
	{
		foreach($arResult["ALL_PROPERTIES"] as $propCode => $arProp)
		{
	?>
		<li><span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arProp["ACTION_LINK"])?>')">
			<span><input type="checkbox" id="PP_<?=$propCode?>"<?echo ($arProp["IS_DELETED"] == "N" ? ' checked="checked"' : '');?>></span>
			<label for="PP_<?=$propCode?>"><?=$arProp["NAME"]?></label>
		</span></li>
	<?
		}
	}
	if (!empty($arResult["ALL_OFFER_PROPERTIES"]))
	{
		foreach($arResult["ALL_OFFER_PROPERTIES"] as $propCode => $arProp)
		{
	?>
		<li><span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arProp["ACTION_LINK"])?>')">
			<span><input type="checkbox" id="OP_<?=$propCode?>"<? echo ($arProp["IS_DELETED"] == "N" ? ' checked="checked"' : ''); ?>></span>
			<label for="OP_<?=$propCode?>"><?=$arProp["NAME"]?></label>
		</span></li>
	<?
		}
	}
	?>
	</ul>
</div>
<?
}
?>

		<script>
			(function($){
				$(window).on("load",function(){
					$('.table_compare').clone().appendTo('#labels');
					$("#tables-wrapper .table-inner").mCustomScrollbar({
						theme:"yellow-red",
						scrollButtons:{enable:true},
						axis:"x"
					});
					
				});
			})(jQuery);
		</script>

<div id="labels"></div>
<div id="tables-wrapper">
<div class="table_compare">
	<div class="table-outer">
		<div class="table-inner">
			<table class="data-table">
			<?
			if (!empty($arResult["SHOW_FIELDS"]))
			{
				foreach ($arResult["SHOW_FIELDS"] as $code => $arProp)
				{
					$showRow = true;
					if (!isset($arResult['FIELDS_REQUIRED'][$code]) || $arResult['DIFFERENT'])
					{
						$arCompare = array();
						foreach($arResult["ITEMS"] as &$arElement)
						{
							$arPropertyValue = $arElement["FIELDS"][$code];
							if (is_array($arPropertyValue))
							{
								sort($arPropertyValue);
								$arPropertyValue = implode(" / ", $arPropertyValue);
							}
							$arCompare[] = $arPropertyValue;
						}
						unset($arElement);
						$showRow = (count(array_unique($arCompare)) > 1);
					}
					if ($showRow)
					{
						?><tr><td><?=GetMessage("IBLOCK_FIELD_".$code)?></td><?
						foreach($arResult["ITEMS"] as &$arElement)
						{
					?>
							<td valign="top">
					<?
							switch($code)
							{
								case "NAME":
									?><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement[$code]?></a>

									<?if($arElement["CAN_BUY"]):?>
									<!--noindex--><br /><a class="bx_bt_button bx_small" href="<?=$arElement["BUY_URL"]?>" rel="nofollow"><?=GetMessage("CATALOG_COMPARE_BUY"); ?></a><!--/noindex-->
									<?elseif (!empty($arResult['offersByItem'][$arElement['ID']])):?>
									<!--noindex--><br /><a href="#popup-offers-<?=$arElement["ID"]?>" class="bx_bt_button bx_small fancybox" title="Выбор цвета"><?=GetMessage("CATALOG_COMPARE_BUY"); ?></a><!--/noindex-->
									<?elseif(!empty($arResult["PRICES"]) || is_array($arElement["PRICE_MATRIX"])):?>
									<br /><?=GetMessage("CATALOG_NOT_AVAILABLE")?>
									<?endif;
									break;
								case "PREVIEW_PICTURE":
								case "DETAIL_PICTURE":
									if(is_array($arElement["FIELDS"][$code])):?>
										<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img
										src="<?=$arElement["FIELDS"][$code]["SRC"]?>"
										width="auto"
										height="150"
										alt="<?=$arElement["FIELDS"][$code]["ALT"]?>"
										title="<?=$arElement["FIELDS"][$code]["TITLE"]?>"
										/></a>
									<?endif;
									break;
								default:
									echo $arElement["FIELDS"][$code];
									break;
							}
						?>
							</td>
						<?
						}
						unset($arElement);
					}
				?>
				</tr>
				<?
				}
			}

			if (!empty($arResult["SHOW_OFFER_FIELDS"]))
			{
				foreach ($arResult["SHOW_OFFER_FIELDS"] as $code => $arProp)
				{
					$showRow = true;
					if ($arResult['DIFFERENT'])
					{
						$arCompare = array();
						foreach($arResult["ITEMS"] as &$arElement)
						{
							$Value = $arElement["OFFER_FIELDS"][$code];
							if(is_array($Value))
							{
								sort($Value);
								$Value = implode(" / ", $Value);
							}
							$arCompare[] = $Value;
						}
						unset($arElement);
						$showRow = (count(array_unique($arCompare)) > 1);
					}
					if ($showRow)
					{
					?>
					<tr>
						<td><?=GetMessage("IBLOCK_OFFER_FIELD_".$code)?></td>
						<?foreach($arResult["ITEMS"] as &$arElement)
						{
						?>
						<td>
							<?=(is_array($arElement["OFFER_FIELDS"][$code])? implode("/ ", $arElement["OFFER_FIELDS"][$code]): $arElement["OFFER_FIELDS"][$code])?>
						</td>
						<?
						}
						unset($arElement);
						?>
					</tr>
					<?
					}
				}
			}
			?>
			<tr>
				<td><?=GetMessage('CATALOG_COMPARE_PRICE');?></td>
				<?
				foreach ($arResult["ITEMS"] as &$arElement)
				{
					if ($arElement["REGION_PRICE"]["DISCOUNT_VALUE"])
					{
						?><td>
						<?=$arElement["REGION_PRICE"]["DISCOUNT_VALUE"]?> руб.
						</td><?
					} elseif ($arResult['itemPrise'][$arElement['ID']]) {
						?><td>
						<?=$arResult['itemPrise'][$arElement['ID']]["DISCOUNT_VALUE"]?> руб.
						</td><?
					} else {
						?><td>&nbsp;</td><?
					}
				}
				unset($arElement);
				?>
			</tr>
			<?
			if (!empty($arResult["SHOW_PROPERTIES"]))
			{
				foreach ($arResult["SHOW_PROPERTIES"] as $code => $arProperty)
				{
					$showRow = true;
					if ($arResult['DIFFERENT'])
					{
						$arCompare = array();
						foreach($arResult["ITEMS"] as &$arElement)
						{
							$arPropertyValue = $arElement["DISPLAY_PROPERTIES"][$code]["VALUE"];
							if (is_array($arPropertyValue))
							{
								sort($arPropertyValue);
								$arPropertyValue = implode(" / ", $arPropertyValue);
							}
							$arCompare[] = $arPropertyValue;
						}
						unset($arElement);
						$showRow = (count(array_unique($arCompare)) > 1);
					}

					if ($showRow)
					{
						?>
						<tr>
							<td><?=$arProperty["NAME"]?></td>
							<?foreach($arResult["ITEMS"] as &$arElement)
							{
								?>
								<td>
									<?=(is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
								</td>
							<?
							}
							unset($arElement);
							?>
						</tr>
					<?
					}
				}
			}

			if (!empty($arResult["SHOW_OFFER_PROPERTIES"]))
			{
				foreach($arResult["SHOW_OFFER_PROPERTIES"] as $code=>$arProperty)
				{
					$showRow = true;
					if ($arResult['DIFFERENT'])
					{
						$arCompare = array();
						foreach($arResult["ITEMS"] as &$arElement)
						{
							$arPropertyValue = $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["VALUE"];
							if(is_array($arPropertyValue))
							{
								sort($arPropertyValue);
								$arPropertyValue = implode(" / ", $arPropertyValue);
							}
							$arCompare[] = $arPropertyValue;
						}
						unset($arElement);
						$showRow = (count(array_unique($arCompare)) > 1);
					}
					if ($showRow)
					{
					?>
					<tr>
						<td><?=$arProperty["NAME"]?></td>
						<?foreach($arResult["ITEMS"] as &$arElement)
						{
						?>
						<td>
							<?=(is_array($arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
						</td>
						<?
						}
						unset($arElement);
						?>
					</tr>
					<?
					}
				}
			}
				?>
				<tr>
					<td>&nbsp;</td>
					<?foreach($arResult["ITEMS"] as &$arElement)
					{
					?>
					<td>
						<a onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arElement['~DELETE_URL'])?>'); BX.onCustomEvent(window, 'OnCompareChange');" href="javascript:void(0)"><?=GetMessage("CATALOG_REMOVE_PRODUCT")?></a>
					</td>
					<?
					}
					unset($arElement);
					?>
				</tr>
			</table>
			
		</div>
	</div>
</div>
</div>
    <?if (!empty($arResult['offersByItem'])) {?>
        <?foreach ($arResult['offersByItem'] as $key => $arItems) {?>
            <div id="popup-offers-<?=$key?>" class="popup-offers fancybox_block">
                <div class="gift_blocks">
                    <?foreach ($arItems as $arOffer):?>
                        <div class="gift_block">
                            <div class="img"><img src="<?=$arOffer["PICTURE"]?>" alt="<?=$arOffer["NAME"]?>" /></div>
                            <div class="name"><?=$arOffer["NAME"]?></div>
                            <div class="price"><?=$arOffer["REGION_PRICE"]["DISCOUNT_VALUE"]?> руб.</div>
                            <div class="buy_button incart_input scale-decrease" data-id="<?=$arOffer["ID"]?>" data-path="<?=$arOffer["BUY_URL"]?>">Добавить</div>
                        </div>
                    <?endforeach?>
                    <div class="footer_button_block">
                        <a href="#close-fancybox" class="button">Продолжить покупки</a>
                        <a href="/cart/" class="red_button">Оформить заказ</a>
                    </div>
                </div>
            </div>
        <? } ?>
    <? } ?>
<?
if ($isAjax)
{
	die();
}
?>
</div>
<script>
	var CatalogCompareObj = new BX.Iblock.Catalog.CompareClass("bx_catalog_compare_block");
</script>