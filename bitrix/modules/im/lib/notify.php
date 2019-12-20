<?php
namespace Bitrix\Im;

class Notify
{
	public static function getCounter($userId)
	{
		$userId = intval($userId);
		if (!$userId)
		{
			return false;
		}
		
		$query = "
			SELECT COUNT(1) CNT
			FROM b_im_message M
			INNER JOIN b_im_relation R ON R.CHAT_ID = M.CHAT_ID AND R.MESSAGE_TYPE = '".IM_MESSAGE_SYSTEM."'
			WHERE R.USER_ID = ".$userId." AND NOTIFY_READ <> 'Y'
		";
		$result = \Bitrix\Main\Application::getInstance()->getConnection()->query($query)->fetch();
		
		return intval($result['CNT']);
	}
	
	public static function getCounterByChatId($chatId)
	{
		$chatId = intval($chatId);
		if (!$chatId)
		{
			return false;
		}
		
		$query = "
			SELECT COUNT(1) CNT
			FROM b_im_message M 
			WHERE M.CHAT_ID = ".$chatId." AND M.NOTIFY_READ <> 'Y'
		";
		$result = \Bitrix\Main\Application::getInstance()->getConnection()->query($query)->fetch();
		
		return intval($result['CNT']);
	}
}