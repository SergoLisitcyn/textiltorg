<?php
define('STOP_STATISTICS', true);
define('BX_SECURITY_SHOW_MESSAGE', true);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
use Bitrix\Sender\Internals\QueryController as Controller;
use Bitrix\Sender\Internals\CommonAjax;

if (!Loader::includeModule('sender'))
{
	return;
}

$actions = array();
$actions[] = CommonAjax\ActionGetTemplate::get();
$checker = CommonAjax\Checker::getReadPermissionChecker();

Controller\Listener::create()->addChecker($checker)->setActions($actions)->run();