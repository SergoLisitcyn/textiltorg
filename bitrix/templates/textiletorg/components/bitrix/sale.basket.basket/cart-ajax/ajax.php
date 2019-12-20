<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("sale");

if (IntVal($_POST["id"]) > 0) {
    CSaleBasket::Update($_POST["id"], array("QUANTITY" => $_POST["count"]));
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>