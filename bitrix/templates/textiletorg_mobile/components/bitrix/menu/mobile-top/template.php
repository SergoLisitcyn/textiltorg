<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<div class="head_sub_menu">
<ul class="header_sub_menu_slider">

<?
$previousLevel = 0;
foreach($arResult as $arItem):?>

	<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
		<?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
	<?endif?>

	<?if ($arItem["IS_PARENT"]):?>

		<?if ($arItem["DEPTH_LEVEL"] == 1):?>
			<li class="header_sub_menu_slider_item <?=$arItem["PARAMS"]["class"]?>" id="<?=$arItem["PARAMS"]["ID"]?>"><a href="<?=$arItem["LINK"]?>" class="<?if ($arItem["SELECTED"]):?>root-item selected<?else:?>root-item children<?endif?>"><?=$arItem["TEXT"]?></a>
                            <ul class="inner_menu">
		<?else:?>
			<li><a href="<?=$arItem["LINK"]?>" class="parent"><?=$arItem["TEXT"]?></a>
                            <ul class="inner_menu">
		<?endif?>

	<?else:?>


        <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                <li class="header_sub_menu_slider_item <?=$arItem["PARAMS"]["class"]?>" id="<?=$arItem["PARAMS"]["ID"]?>"><a href="<?=$arItem["LINK"]?>" class="<?if ($arItem["SELECTED"]):?>root-item selected<?else:?>root-item<?endif?>"><?=$arItem["TEXT"]?></a></li>
        <?else:?>
                <li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
        <?endif?>

	<?endif?>

	<?$previousLevel = $arItem["DEPTH_LEVEL"];?>

<?endforeach?>

<?if ($previousLevel > 1)://close last item tags?>
	<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
<?endif?>

</ul>

</div>
<?endif?>