<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/market/config.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/market/lib/BitrixMarket.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/market/lib/ArrayToXML.php");
global $USER;

if (!empty($_REQUEST["REGION"]))
    define("REGION", strtoupper($_REQUEST["REGION"]));

if (!empty($_REQUEST["HANDLER"]))
    define("HANDLER", strtoupper($_REQUEST["HANDLER"]));

if (!empty($_REQUEST["IS_ROOT"]))
{

    $arHandler = $arConfig["ROOT_HANDLERS"][HANDLER];

    if (!empty($_REQUEST['ctx']))
    {
        define("REGION", strtoupper($_REQUEST['ctx']));
    }
    else
    {
        define("REGION", $arConfig["ROOT_HANDLERS"][HANDLER]["REGION"]);
    }
}
else
{
    $arHandler = $arConfig["HANDLERS"][REGION][HANDLER];
}

if (empty($arHandler))
{
    CHTTP::SetStatus('404 Not Found');
    die("ERROR 404. Page Not Found");
}

switch (HANDLER) {
    case 'PPODBOR':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/ppodbor.php");
        break;
    case 'TIU':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/tiu.php");
        break;
    case 'TMALL':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/tmall.php");
        break;
    case 'ALLITEMS':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/allitems.php");
        break;
    case 'WEBMASTER':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/webmaster.php");
        break;

    case 'ALLBIZ':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/allbiz.php");
        break;

    case 'IRR':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/irr.php");
        break;

    case 'GML':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/gml.php");
        break;

    case 'GOOGLE_MS':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/google_ms.php");
        break;

    case 'GOOGLE_MS2':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/google_ms2.php");
        break;

    case 'CRITEO':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/criteo.php");
        break;

    case 'GDESLON':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/yasearch.php");
        break;

    case 'MIXMARKET':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/mixmarket.php");
        break;

    case 'WIKIMART':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/wikimart.php");
        break;

    case 'WIKIMART_NEW':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/wikimart_new.php");
        break;

    case 'AVITO_CONTEXT':
        define("YML_NOT_PROPS", true);
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/yml.php");
        break;

    case 'ONLINERBY':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/onliner-by.php");
        break;

    case 'SHOPBY':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/shop-by.php");
        break;

    case 'TARGET_MY':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/target-my.php");
        break;

    case 'YASEARCH_BY':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/yasearch-by.php");
        break;

    case 'YASEARCH':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/yasearch.php");
        break;

	case 'AVITO':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/avito.php");
        break;

    case 'AVITO_SHVEJNYE_MASHINY':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/avito_shvejnye_mashiny.php");
        break;

    case 'GOODS':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/goods.php");
        break;
		
     case 'GOOGLE_SHVEYNYE_MASHINY':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/google_shveynye_mashiny.php");
        break;

    case 'DEMO':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/demo.php");
        break;

    case 'SHVEJNAYA_TEKHNIKA':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/shvejnaya_tekhnika.php");
        break;

    case 'GLADILNAYA_TEKHNIKA':
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/gladilnaya_tekhnika.php");
        break;
		
	default:
        require_once($_SERVER["DOCUMENT_ROOT"]."/market/handlers/yml.php");
        break;
}