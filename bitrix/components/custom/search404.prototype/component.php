<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); 

$arParams["PATH_LOG"] = isset($arParams["PATH_LOG"]) ? $arParams["PATH_LOG"] : "/log/";

$path_log = $_SERVER['DOCUMENT_ROOT'] . $arParams["PATH_LOG"];

$path_log = $path_log . date('Y-m-d') . ".log";

if ($fp = fopen($path_log, 'a')) {
    $message = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    if (!empty($_SERVER['HTTP_REFERER'])) {
        $message .= " переход со страницы ".$_SERVER['HTTP_REFERER'];
    }
    fwrite($fp, $message."\n");
    fclose($fp);
}
