<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$i = 1; ?>
<?if (!empty($arResult)):?>
<div class="menu">
    <div class="menu_items">
		<div class="close"></div>
		<?$previousLevel = 0;
		foreach($arResult as $arItem):?>
			<?if(!empty($arItem["TEXT"])) {?>
				<?if ($arItem["DEPTH_LEVEL"] == 1):?>
					<?if ($previousLevel):?>
						</ul>
					<?else:?>
						<?$previousLevel = 1?>
					<?endif?>
					<?if ($arItem["IS_PARENT"]):?>
											<h2 class="root<?=$i++?>"><?=$arItem["TEXT"]?></h2>
					<?endif?>
					<ul>
				<?endif?>
				<?if ($arItem["DEPTH_LEVEL"] == 2):?>
					<li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
				<?endif?>
			<?}?>
		<?endforeach?>
    </div>
</div>
<?endif?>