<?
set_time_limit(500);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->IncludeComponent(
    "custom:price-and-onliner.prototype",
    "",
    Array(
        "IBLOCK_ID" => "8",
        "PATH_LOG" => "/log/ym_amd_onliner/",
		"MRC_PRICE_RU_ID" => "9",
		"MRC_PRICE_RB_ID" => "10",
        "PURCHASING_PRICE_RB_ID" => "8",
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
            ),
            'nsk' => array(
                'regid' => 65,
                'price_id' => 12,
            ),
/*			'by' => array(
                'regid' => 157,
                'price_id' => 11,
            )*/
        ),
    )
);
