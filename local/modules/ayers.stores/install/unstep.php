<?
/**
 * Created by PhpStorm
 * User: Sergey Pokoev
 * www.pokoev.ru
 * @ Академия 1С-Битрикс - 2015
 * @ academy.1c-bitrix.ru
 */
use \Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid())
    return;

Loc::loadMessages(__FILE__);
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
<?=bitrix_sessid_post()?>
    <input type="hidden" name="lang" value="<?echo LANGUAGE_ID?>">
    <input type="hidden" name="id" value="ayers.stores">
    <input type="hidden" name="uninstall" value="Y">
    <input type="hidden" name="step" value="2">
    <p><input type="checkbox" name="savedata" id="savedata" value="Y" checked><label for="savedata">Сохранить данные в БД</label></p>
    <input type="submit" name="" value="<?echo Loc::getMessage("MOD_UNINST_DEL")?>">
</form>