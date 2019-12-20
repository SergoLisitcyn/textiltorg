<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

if(IS_HOME) {
	$strReturn = '<div class="head_breadcrumbs title_page"><ul><li>';
}else{
	$strReturn = '<div class="head_breadcrumbs"><ul><li>';
}

$strReturn .= ($arResult)?
	'<span onclick="history.back(); return false;" class="head_breadcrumbs_back">Назад</span>' :
	'Главная';
$strReturn .= '</li></ul></div>';

return $strReturn;
?>