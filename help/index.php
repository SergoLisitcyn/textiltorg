<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Помощь");
$APPLICATION->RestartBuffer();
?>
<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/mod_files/css/common.css?_cv=5.14.4" type="text/css">
<?$APPLICATION->IncludeComponent(
    "bitrix:highloadblock.view",
    "help",
    array(
        "BLOCK_ID" => $_REQUEST["block"],
        "LIST_URL" => "",
        "ROW_ID" => $_REQUEST["id"],
        "COMPONENT_TEMPLATE" => "help"
    ),
    false
);?>
<?
die;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>