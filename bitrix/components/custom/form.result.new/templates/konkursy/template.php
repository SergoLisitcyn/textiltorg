<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>

<?if ($arResult["isFormErrors"] == "Y"):?><?=$arResult["FORM_ERRORS_TEXT"];?><?endif;?>

<?if ($arResult["FINISH"]):?>
    <p>Ваша заявка на участие в конкурсе "Мастер интерьера 2018" принята.</p>
<?endif;?>
    
<?if ($arResult["isFormNote"] != "Y" && !$arResult["FINISH"])
{
?>
<?=$arResult["FORM_HEADER"]?>

<?
/***********************************************************************************
						form questions
***********************************************************************************/
?>
<div class="textiletorg-fotm-resilt-new-konkursy">
    <div>
    <?
    foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion)
    {
        if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden')
        {
            echo $arQuestion["HTML_CODE"];
        }
        else
        {
            switch ($FIELD_SID) {
                case "SIMPLE_QUESTION_189":
                    // Если поле ФИО
                    ?>
                    <label for="<?=$FIELD_SID?>"><?=$arQuestion["CAPTION"]?><?if ($arQuestion["REQUIRED"] == "Y"):?><span class="red">*</span><?endif;?></label>
        
                    <?=str_replace(array('<input',' size="0"'), array('<input id="'.$FIELD_SID.'"','') , $arQuestion["HTML_CODE"])?>
        
                    <div class="blocks-form-inner">
                    <?
                    break;
                case "SIMPLE_QUESTION_245":
                    // Если поле "О себе"
                    ?>
                    </div>
        
                    <label for="<?=$FIELD_SID?>" class="yandex_pre"><?=$arQuestion["CAPTION"]?><?if ($arQuestion["REQUIRED"] == "Y"):?><span class="red">*</span><?endif;?></label>
                    
                    <?=str_replace('<textarea', '<textarea id="'.$FIELD_SID.'"' , $arQuestion["HTML_CODE"])?>
                    <?
                    break;
                case "PHOTO":
                    ?>
                    <div>
                        <?=$arQuestion["CAPTION"]?>
                        <div>
	                        <?=str_replace(' size="0"', '' , $arQuestion["HTML_CODE"])?>
                        </div>
                    </div>
                    <?
                    break;
                default:
                    // Остальные поля
                    ?>
                    <div>
                        <label for="<?=$FIELD_SID?>"><?=$arQuestion["CAPTION"]?><?if ($arQuestion["REQUIRED"] == "Y"):?><span class="red">*</span><?endif;?></label>

	                     <?=str_replace(array('<input',' size="0"'), array('<input id="'.$FIELD_SID.'"','') , $arQuestion["HTML_CODE"])?>
                    </div>
                    <?
            }

            ?>
            <?
        }
    } //endwhile
    ?>
</div>
    <input class="red_button" <?=(intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : "");?> type="submit" name="web_form_submit" value="<?=htmlspecialcharsbx(strlen(trim($arResult["arForm"]["BUTTON"])) <= 0 ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]);?>" />
    
</div>

<?=$arResult["FORM_FOOTER"]?>
<?
} //endif (isFormNote)
?>
