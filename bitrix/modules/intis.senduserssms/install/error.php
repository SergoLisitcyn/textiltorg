<?if(!check_bitrix_sessid()) return;?>
<?
IncludeModuleLangFile(__FILE__);
echo CAdminMessage::ShowMessage(GetMessage("INTIS_SEND_USER_SMS_MOD_SALE_NOT_INSTALL"));
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
    <input type="hidden" name="lang" value="<?echo LANG?>">
    <input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
    <form>
