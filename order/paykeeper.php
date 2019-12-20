<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?$APPLICATION->IncludeComponent(
    "bitrix:sale.order.payment.receive",
    "",
    array(
        "PAY_SYSTEM_ID_NEW" => "6"
    )
);?>