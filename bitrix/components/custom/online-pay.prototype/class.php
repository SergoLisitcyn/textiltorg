<?php
use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use \Bitrix\Main\Context;

class OnlinePay extends CBitrixComponent
{
    public function executeComponent()
    {
        $this->arResult['URL'] = (!empty($this->arParams['URL']))? $this->arParams['URL'] : 'http://demo.paykeeper.ru/create/';

        $this->includeComponentTemplate();
    }
};