<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!function_exists('AddOrderProperty'))
{
    function AddOrderProperty($code, $value, $order, $person = 1)
    {
        if (!strlen($code))
        {
            return false;
        }

        if (CModule::IncludeModule("sale"))
        {
            if ($arProp = CSaleOrderProps::GetList(array(), array("CODE" => $code, "PERSON_TYPE_ID" => $person))->Fetch())
            {
                return CSaleOrderPropsValue::Add(array(
                    "NAME" => $arProp["NAME"],
                    "CODE" => $arProp["CODE"],
                    "ORDER_PROPS_ID" => $arProp["ID"],
                    "ORDER_ID" => $order,
                    "VALUE" => $value,
                ));
            }
        }
    }
}