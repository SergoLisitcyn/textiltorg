<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->IncludeComponent(
    "custom:price.yandex.protorype",
    "",
    Array(
        "IBLOCK_ID" => "8",
        "PATH_LOG" => "/log/ru/",
		"MRC_PRICE_ID" => "9",
        "REGIONS" => array(
            'msk' => array(
                'regid' => 213, 
                'price_id' => 1,
            ), 
            'spb' => array(
                'regid' => 2,             
                'price_id' => 2,
            ), 
            'ekb' => array(
                'regid' => 54,             
                'price_id' => 4,
            ), 
            'n_nov' => array(
                'regid' => 47,             
                'price_id' => 5,
            ),
            'rnd' => array(
                'regid' => 39,
                'price_id' => 6,
            ) 
        ),        
    )
);
