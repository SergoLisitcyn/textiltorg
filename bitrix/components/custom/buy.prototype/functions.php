<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!function_exists('AddOrderProperty'))
{
    function AddOrderProperty($code, $value, $order)
    {
        if (!strlen($code))
        {
            return false;
        }

        if (CModule::IncludeModule("sale"))
        {
            if ($arProp = CSaleOrderProps::GetList(array(), array("CODE" => $code))->Fetch())
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