<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->IncludeComponent(
    "custom:price.yandex.protorype",
    "",
    Array(
        "IBLOCK_ID" => "8",
        "PATH_LOG" => "/log/rb/",
		"MRC_PRICE_ID" => "10",
        "REGIONS" => array(
            'by' => array(
                'regid' => 157,
                'price_id' => 7,
            )
        ),        
    )
);
