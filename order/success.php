<?
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/pay.log");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($_COOKIE["PP_IS_REDIRECT_ORDER_".$_COOKIE["PP_ORDER_ID"]] == "N")
{
    LocalRedirect("/order/?ORDER_ID=".$_COOKIE["PP_ORDER_ID"]."&IS_AUTO_REDIRECT=Y", false);
    die;
}
else
{
    LocalRedirect('/');
}
?>