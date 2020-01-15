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
$this->setFrameMode(true);
?>

<div class="halfcirclebox" style="padding-top: 30px;">
	<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get">
		<?foreach($arResult["ITEMS"] as $arItem):
			if(array_key_exists("HIDDEN", $arItem)):
				echo $arItem["INPUT"];
			endif;
		endforeach;?>
        <table style="width: 100%;text-align: center;border-spacing: 0;border-collapse: collapse;">
            <tr style="background: #e7e7e7;height: 30px;">
                <th style="width: 300px;">Тип устройства</th>
                <th style="width: 300px;">Марка</th>
                <th></th>
            </tr>
            <tr>
                <td style="padding-top: 20px;padding-bottom: 20px;"><?=$arResult["ITEMS"]["SECTION_ID"]["INPUT"]?></td>
                <td style="padding-top: 20px;padding-bottom: 20px;"><?=$arResult["ITEMS"]["PROPERTY_3"]["INPUT"]?></td>
                <td style="text-align: left;padding-left: 30px;padding-top: 20px;padding-bottom: 20px;"><button type="submit" style="line-height: 5px;background: #e52324;border-radius: 5px 5px 15px 15px;border: none;color: #fff;padding: 10px 30px;font-weight: bold;" name="set_filter" value="Фильтровать">Найти</button>
                    <input type="hidden" name="set_filter" value="Y" /></td>
            </tr>
        </table>
<!--		Фильтровать по&nbsp;&nbsp;<b>категории</b>&nbsp;&nbsp;--><?//=$arResult["ITEMS"]["SECTION_ID"]["INPUT"]?><!--,-->
<!--		по&nbsp;&nbsp;<b>бренду</b>&nbsp;&nbsp;--><?//=$arResult["ITEMS"]["PROPERTY_3"]["INPUT"]?>
<!---->
<!--		<button type="submit" style="line-height: 5px; vertical-align: middle;float:right;" name="set_filter" value="Фильтровать">Фильтровать</button>-->
<!--		<input type="hidden" name="set_filter" value="Y" />-->
	</form>
</div>