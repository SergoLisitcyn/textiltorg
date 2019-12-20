<?php
namespace Bitrix\Im;

class Chat
{
	public static function getRelation($chatId, $params = Array())
	{
		$chatId = intval($chatId);
		if ($chatId <= 0)
		{
			return false;
		}
		
		$connection = \Bitrix\Main\Application::getInstance()->getConnection();
		
		$selectFields = '';
		if (isset($params['SELECT']))
		{
			$map = \Bitrix\Im\Model\RelationTable::getMap();
			foreach ($params['SELECT'] as $key => $value)
			{
				if (is_int($key) && isset($map[$value]))
				{
					$selectFields .= "R.{$value}, ";
				}
				else if (!is_int($key) && isset($map[$key]))
				{
					$selectFields .= "R.{$key} {$connection->getSqlHelper()->forSql($value)}, ";
				}
			}
		}
		if (!$selectFields)
		{
			$selectFields = '*, ';
		}
		
		$whereFields = '';
		if (isset($params['FILTER']))
		{
			$map = \Bitrix\Im\Model\RelationTable::getMap();
			foreach ($params['FILTER'] as $key => $value)
			{
				if (!isset($map[$key]))
				{
					continue;
				}
				
				$whereFields .= " AND R.{$key} = {$connection->getSqlHelper()->forSql($value)}";
			}
		}
		
		if (isset($params['WITH_REAL_COUNTERS']) && $params['WITH_REAL_COUNTERS'] != 'N')
		{
			$lastId = "R.LAST_ID";
			if (is_array($params['WITH_REAL_COUNTERS']))
			{
				if (isset($params['WITH_REAL_COUNTERS']['LAST_ID']) && intval($params['WITH_REAL_COUNTERS']['LAST_ID']) > 0)
				{
					$lastId = intval($params['WITH_REAL_COUNTERS']['LAST_ID']);
				}
			}
			
			$sqlSelectCounter = "R.COUNTER PREVIOUS_COUNTER, (
				SELECT COUNT(1) FROM b_im_message M WHERE M.CHAT_ID = R.CHAT_ID AND M.ID > $lastId
			) COUNTER";
		}
		else
		{
			$sqlSelectCounter = 'R.COUNTER, R.COUNTER PREVIOUS_COUNTER';
		}
		
		$skipUnmodifiedRecords = false;
		if (isset($params['SKIP_RELATION_WITH_UNMODIFIED_COUNTERS']) && $params['SKIP_RELATION_WITH_UNMODIFIED_COUNTERS'] == 'Y')
		{
			$skipUnmodifiedRecords = true;
		}
		
		$query = "
			SELECT {$selectFields} {$sqlSelectCounter}
			FROM b_im_relation R
			WHERE R.CHAT_ID = {$chatId} {$whereFields}
			".($skipUnmodifiedRecords? ' HAVING COUNTER <> PREVIOUS_COUNTER': '')."
		";
		$relations = \Bitrix\Main\Application::getInstance()->getConnection()->query($query)->fetchAll();
		
		return $relations;
	}
}