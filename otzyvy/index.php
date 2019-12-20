<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("tags", "Прочитать отзывы о сети магазинов Текстильторг");
$APPLICATION->SetPageProperty("keywords", "отзывы текстильторг сеть магазинов");
$APPLICATION->SetPageProperty("description", "Ознакомиться с отзывами о магазине Текстильторг");
$APPLICATION->SetTitle("Отзывы о компании Текстильторг");
?><?$APPLICATION->IncludeComponent(
	"khayr:main.comment",
	"reviews",
	Array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y H:i",
		"ADDITIONAL" => array(),
		"ALLOW_RATING" => "N",
		"AUTH_PATH" => "/auth/",
		"CAN_MODIFY" => "N",
		"COMPONENT_TEMPLATE" => "reviews",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"COUNT" => "20",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"JQUERY" => "N",
		"LEGAL" => "N",
		"LEGAL_TEXT" => "Я согласен с правилами размещения сообщений на сайте.",
		"LOAD_AVATAR" => "N",
		"LOAD_DIGNITY" => "N",
		"LOAD_FAULT" => "N",
		"LOAD_MARK" => "Y",
		"MAX_DEPTH" => "2",
		"MODERATE" => "Y",
		"NON_AUTHORIZED_USER_CAN_COMMENT" => "Y",
		"OBJECT_ID" => "1",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "modern_ajax",
		"PAGER_TITLE" => "",
		"REQUIRE_EMAIL" => "Y",
		"USE_CAPTCHA" => "N"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>