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

<div class="halfcirclebox">
	<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get">
		<?foreach($arResult["ITEMS"] as $arItem):
			if(array_key_exists("HIDDEN", $arItem)):
				echo $arItem["INPUT"];
			endif;
		endforeach;?>
        <table>
            <tr>
                <th>Тип устройства</th>
                <th>Брэнд</th>
                <th></th>
            </tr>
            <tr>
                <td><?=$arResult["ITEMS"]["SECTION_ID"]["INPUT"]?></td>
                <td><?=$arResult["ITEMS"]["PROPERTY_3"]["INPUT"]?></td>
                <td>
                    <button type="submit" value="Фильтровать" class="red_button">Искать</button>
                    <input type="hidden" name="set_filter" value="Y" />
                </td>
            </tr>
        </table>
	</form>
</div>