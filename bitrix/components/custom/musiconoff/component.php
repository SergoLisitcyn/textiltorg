<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (isset($_REQUEST["AJAX_VOLUME"]) && $_REQUEST["AJAX_VOLUME"] == "Y")
{
    if ($_REQUEST["DEACTIVATE"] == "Y") {
        setcookie("CUSTOM_MUSICONOFF_DEACTIVATE", "Y", time() + 60*60*24*365, "/");
    } else {
        setcookie("CUSTOM_MUSICONOFF_DEACTIVATE", "", time() - 3600, "/");
    }
    $APPLICATION->RestartBuffer();
    die();
} 
$arResult["OFF_VOLUME"] = false;
if (isset($_COOKIE["CUSTOM_MUSICONOFF_DEACTIVATE"]) && $_COOKIE["CUSTOM_MUSICONOFF_DEACTIVATE"] == "Y")
{
    $arResult["OFF_VOLUME"] = true;
}

$this->IncludeComponentTemplate();
?>
