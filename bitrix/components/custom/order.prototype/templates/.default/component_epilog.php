<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/vendor/jquery.validate.min.js");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/vendor/jquery.formatter.min.js");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/vendor/jquery.form.min.js");

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/vendor/chosen/chosen.jquery.min.js");
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/vendor/chosen/chosen.css");
?>