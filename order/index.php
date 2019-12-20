<?
define("HIDE_BOTTOM_PANEL", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");
?><?if (defined("IS_MOBILE")):?><style>.zakaz_fin .cent{text-align:center}.zakaz_fin > table {max-width:320px;margin:0 auto;text-align:center;}.zakaz_fin > table img,.zakaz_fin > table p{float:none !important;}</style><?endif;?>
<?$APPLICATION->IncludeComponent(
    "custom:order.make.prototype",
    "",
    array(
        "FIELDS" => array(
            "1" => array(
                "NAME",
                "PHONE",
                "EMAIL",
                "CITY",
                "ADDRESS",
                "STORE"
            ),
            "2" => array(
                "UR_NAME",
                "UR_PHONE",
                "UR_EMAIL",
                "UR_CITY",
                "ADDRESS",
                "STORE",
                "UR_CONTACT_ADDRESS",
                "UR_ORG",
                "UR_INN",
                "UR_KPP",
                "UR_BIK",
                "UR_BANK"
            ),
        ),
        "CART_PAGE" => "/cart/",
        "PATH_TO_PAYMENT" => "/order/payment.php"
    ),
    false,
    array(
        "HIDE_ICONS" => "Y"
    )
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>