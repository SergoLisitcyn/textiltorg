<?php

global $USER;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");
CModule::IncludeModule("iblock");
CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID());