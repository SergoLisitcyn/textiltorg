<?
class AsLocation
{
	static function OnSaleComponentOrderProperties(&$arUserResult, $request, &$arParams, &$arResult)
	{
		$locationCode = self::GetLocationCode();

		if ($locationCode)
		{
			$arProperty = self::GetLocationProperty($arUserResult["PERSON_TYPE_ID"]);

			if (!empty($arProperty["ID"]))
			{
				$arUserResult["ORDER_PROP"][$arProperty["ID"]] = $locationCode;
			}
		}

	}

	private static function GetLocationCode()
	{

		if (intval($_SESSION["GEO_REGION_CITY_ID"]))
		{
			$locationCode = CSaleLocation::getLocationCODEbyID($_SESSION["GEO_REGION_CITY_ID"]);

			if($locationCode)
			{
				return $locationCode;
			}
		}

		return false;
	}

	private static function GetLocationProperty($personTypeId)
	{
		$arResult = array();

		if (!empty($personTypeId))
		{
			if(\Bitrix\Main\Loader::includeModule("sale"))
			{
				$arFilter = array(
					"ACTIVE"         => "Y",
					"PERSON_TYPE_ID" => $personTypeId,
					"TYPE"           => "LOCATION",
					"IS_LOCATION"    => "Y",
				);

				$dbResult = \Bitrix\Sale\Internals\OrderPropsTable::getList(array(
					"select" => array("*"),
					"filter" => $arFilter,
					"order" => array("SORT" => "ASC")
				));

				if ($arRow = $dbResult->fetch())
				{
					$arResult = $arRow;
				}
			}
		}

		return $arResult;
	}
}