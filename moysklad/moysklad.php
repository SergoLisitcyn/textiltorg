<?php
defined("BASEPATH") OR die;
define("LOG_FILENAME", BASEPATH."/logs/".date("d-m-Y").".log");
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
CModule::IncludeModule("sale");

class Moysklad
{
    private $login = "";
    private $password = "";
    private $arOrganisations = array();
    const API_URL = "https://online.moysklad.ru/api/remap/1.1";

    function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;

        $this->arOrganisations = $this->Get("/entity/organization");
        $this->arStores = $this->Get("/entity/store");
    }

    public function Get($url, $params = array())
    {
        $options = array(
            "http" => array(
                "method" => "GET",
                "header" => "Authorization: Basic ".base64_encode($this->login.":".$this->password)."\r\n"
            )
        );

        $paramsUrl = '';
        foreach ($params as $param => $value)
        {
            $brid  = "=";
            if (preg_match('/\>$/', $param))
            {
                $param = explode('=', $param);
                $param = urlencode($param[0]).'='.urlencode($param[1]);
                $brid = "";
            }

            $paramsUrl .= (empty($paramsUrl))? "?" : "&";
            $paramsUrl .= $param.$brid.urlencode($value);
        }

        $context = stream_context_create($options);
        $result = file_get_contents(self::API_URL.$url.$paramsUrl, false, $context);

        return json_decode($result, true);
    }

    public function Put($url, $params = array())
    {
        $options = array(
            "http" => array(
                "method" => "PUT",
                "header" => "Authorization: Basic ".base64_encode($this->login.":".$this->password)."\r\n".
                            "Content-Type: application/json;charset=utf-8\r\n",
                "content" => json_encode($params)
            )
        );

        $context = stream_context_create($options);
        $result = file_get_contents(self::API_URL.$url, false, $context);

        return json_decode($result, true);
    }

    public function Sync($siteId, $arRegions)
    {
        AddMessage2Log("Синхронизация заказов".$this->_WrapText($siteId));

        $arBxOrders = $this->_GetBxOrders($siteId);

        // echo "<pre>";
        // var_dump($arBxOrders);

        if ($arBxOrders)
        {
            $arMsOrders = $this->Get("/entity/customerorder", array(
                "filter=moment>" => date('Y-m-d H:i:s', strtotime('-3 hours')).';applicable=true;version=0',
                "limit" => 100
            ));

            // echo "<pre>";
            // var_dump($arMsOrders);

            foreach ($arBxOrders as $arBxOrder)
            {
                $arLocation = CSaleLocation::GetByID($arBxOrder["PROPERTIES"]["CITY"]["VALUE"]);

                $isLocation = false;
                foreach ($arRegions as $nameRegion => $arRegion)
                {
                    if ($nameRegion == $arLocation["CITY_NAME"])
                    {
                        $isLocation = true;
                        break;
                    }
                }

                $region = ($isLocation)?
                    $arLocation["CITY_NAME"]:
                    "Регион";

                $organizationName = $arRegions[$region]["ORG"];
                $storeName = $arRegions[$region]["STORE"];

                foreach ($arMsOrders["rows"] as $arMsOrder)
                {
                    $attrNumberKey = 0;
                    foreach ($arMsOrder["attributes"] as $attrKey => $arAtribute)
                    {
                        if ($arAtribute["name"] == "Номер заявки")
                        {
                            $attrNumberKey = $attrKey;
                            break;
                        }
                    }

                    if (empty($attrNumberKey))
                    {
                        //AddMessage2Log("Ошибка, не найден ключ для номера заявки. Заказ создан вручную.");
                        continue;
                    }

                    if ($arBxOrder["ACCOUNT_NUMBER"] == $arMsOrder["attributes"][$attrNumberKey]["value"])
                    {
                        AddMessage2Log("Найден заказ в Моем складе".$this->_WrapText($arBxOrder["ACCOUNT_NUMBER"]));
                        $arOrderOrganisation = $this->_GetOrderOrganisation($organizationName);
                        $arOrderStore = $this->_GetOrderStore($storeName);

                        if ($arOrderOrganisation)
                        {
                            $arResult = $this->Put("/entity/customerorder/".$arMsOrder["id"], array(
                                "applicable" => false,
                                "organization" => array(
                                    "meta" => $arOrderOrganisation["meta"]
                                ),
                                "store" => array(
                                    "meta" => $arOrderStore["meta"]
                                )
                            ));

                            if ($arResult)
                            {
                                AddMessage2Log("Синхронизация заказа прошла успешно".$this->_WrapText($arBxOrder["ACCOUNT_NUMBER"]));
                                $this->_UpdateMsId($arBxOrder["ID"], $arMsOrder["id"], $arBxOrder["PERSON_TYPE_ID"]);
                            }
                            else
                            {
                                AddMessage2Log("Ошибка. Не удалось обновить заказ в Моем складе.".$this->_WrapText($arBxOrder["ACCOUNT_NUMBER"]));
                            }
                        }
                    }
                }
            }
        }
        else
            AddMessage2Log("Заказов не найдено".$this->_WrapText($siteId));
    }

    public function SetStatusPayKeeper($id, $status)
    {
        if (!empty($id))
        {
            AddMessage2Log("Установка статуса ".$status." для".$id);

            $arMsOrder = $this->Get("/entity/customerorder/".$id);

            if ($arMsOrder)
            {
                $arResult = $this->Put("/entity/customerorder/".$id, array(
                    "attributes" => array(
                        array(
                            "id" => "c287f03af-43f4-11e4-90a2-8eca002b1197",
                            "value" => $status
                        )
                    ),
                ));
            }
            else
            {
                AddMessage2Log("Ошибка. Не найден заказ ".$id);
            }
        }
        else
        {
            AddMessage2Log("Ошибка. Не указан идетификатор");
        }
    }

    private function _GetOrderOrganisation($organizationName)
    {
        $arOrderOrganisation = array();
        foreach ($this->arOrganisations["rows"] as $arOrganisation)
        {
            if ($arOrganisation["name"] == $organizationName)
            {
                $arOrderOrganisation = $arOrganisation;
                break;
            }
        }

        return $arOrderOrganisation;
    }

    private function _GetOrderStore($storeName)
    {
        $arOrderStore = array();
        foreach ($this->arStores["rows"] as $arStore)
        {
            if ($arStore["name"] == $storeName)
            {
                $arOrderStore = $arStore;
                break;
            }
        }

        return $arOrderStore;
    }

    private function _UpdateMsId($orderId, $value, $person = 1)
    {
        $rsProps = CSaleOrderPropsValue::GetList(
            array(),
            array(
                "ORDER_ID" => $orderId,
                "CODE" => "MS_ID",
                "PERSON_TYPE_ID" => $person
            )
        );

        if ($arProp = $rsProps -> Fetch())
        {
            CSaleOrderPropsValue::Update($arProp["ID"], array(
                "NAME" => $arProp["NAME"],
                "CODE" => $arProp["CODE"],
                "ORDER_PROPS_ID" => $arProp["ORDER_PROPS_ID"],
                "ORDER_ID" => $arProp["ORDER_ID"],
                "VALUE" => $value,
            ));
        }
    }

    private function _GetBxOrders($siteId)
    {
        $arBxOrders = array();
        $rsBxOrders = CSaleOrder::GetList(
            array(),
            array(
                "LID" => $siteId,
                "PROPERTY_VAL_BY_CODE_MS_ID" => "",
                ">=DATE_INSERT" => "01.09.2018"
            )
        );

        while ($arBxOrder = $rsBxOrders->Fetch())
        {

            $rsProps = CSaleOrderPropsValue::GetOrderProps($arBxOrder["ID"]);
            while ($arProp = $rsProps->Fetch())
            {
                $arBxOrder["PROPERTIES"][$arProp["CODE"]] = $arProp;
            }

            $arBxOrders[] = $arBxOrder;
        }

        return $arBxOrders;
    }

    private function _WrapText($text)
    {
        return " [".$text."]";
    }
}