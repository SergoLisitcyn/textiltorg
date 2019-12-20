<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$GLOBALS["arrFilterForMap"] = array(
	"PROPERTY_VIEW_SITE_RU_VALUE" => "Да"
);

$APPLICATION->IncludeComponent(
    "custom:sitemap.generator.prototype",
    "",
    Array(
        "FILTER_NAME" => "arrFilterForMap",
		"IBLOCK_ID_CATALOG" => array("8"),
        "IBLOCK_ID_FOR_SECTIONS" => array("1","3","6","7"),
        "IBLOCK_ID_FOR_ELEMENTS" => array("1","3","6","5","7"),
        "MODIFIER_PAGE_URL" => "Y",
        "PAGES" => array("/o-nas/","/akcii/","/poleznoe/","/poleznoe/stati/","/poleznoe/instrukcii/","/poleznoe/obzory/","/informacija/","/informacija/konkursy/","/informacija/partneram/","/informacija/kredit/","/informacija/garantii/","/dostavka/","/samovyvoz/","/kontakty/"),
        "PREFIX" => "https://www.textiletorg.ru",
        "FILENAME" => "sitemap.php",
        "DYNAMIC" => "Y"
    )
);