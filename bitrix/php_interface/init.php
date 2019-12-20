<?

require_once('dumplog.php');

if(preg_match("/textiletorg.ru$/i",$_SERVER['SERVER_NAME'])){
		$Falag = true;
		if(
			$_SERVER['SERVER_NAME'] == "www.textiletorg.ru"
			||
			$_SERVER['SERVER_NAME'] == "spb.textiletorg.ru"
			||
			$_SERVER['SERVER_NAME'] == "www.spb.textiletorg.ru"
			||
			$_SERVER['SERVER_NAME'] == "tmp.textiletorg.ru"
			||
			$_SERVER['SERVER_NAME'] == "prom.textiletorg.ru"
			||
			$_SERVER['SERVER_NAME'] == "www.prom.textiletorg.ru"
            ||
			$_SERVER['SERVER_NAME'] == "dev.textiletorg.ru"
            ||
			$_SERVER['SERVER_NAME'] == "www.dev.textiletorg.ru"
		){
			$Falag = false;
		}
		if($Falag){
			header("HTTP/1.1 301 Moved Permanently");
			header('Location: https://www.textiletorg.ru'.$APPLICATION->GetCurPage(false));
			exit();
		}
	}
/*
if(
	$_SERVER['SERVER_NAME'] !== "www.textiletorg.ru"
	&& $_SERVER['SERVER_NAME'] !== "spb.textiletorg.ru"
    && $_SERVER['SERVER_NAME'] !== "tmp.textiletorg.ru"
	&& $_SERVER['SERVER_NAME'] !== "adm.textiletorg.by" 
) {
	//header("HTTP/1.1 301 Moved Permanently");
	//header('Location: https://www.textiletorg.ru'.$APPLICATION->GetCurPage(false));
	//exit();
}*/
define("IS_HOME", ($APPLICATION->GetCurPage(false) == SITE_DIR));
define("PAGE", $APPLICATION->GetCurPage(true));
define("PAGE_FOLDER", $APPLICATION->GetCurPage());

global $SLIDER_IBLOCK_ID;
global $REGION_HOUSE_REGIONS;
global $REGION_DEFAULT_REGION;
global $REGION_COUNTRY_NAME_ORIG;
global $CATALOG_IBLOCK_ID;
global $PRODUCT_PAGE;
if (strpos(PAGE, '.html') == false) {
    $PRODUCT_PAGE = false;
} else {
    $PRODUCT_PAGE = true;
}
if (SITE_ID === "s1")
{
	$SLIDER_IBLOCK_ID = 2;
	$REGION_HOUSE_REGIONS = array(
		"Москва", "Санкт-Петербург", "Екатеринбург",
		"Нижний Новгород", "Ростов-на-Дону", "Новосибирск"
	);
	$REGION_DEFAULT_REGION = "Москва";
	$REGION_COUNTRY_NAME_ORIG = "Russian Federation";
    $CATALOG_IBLOCK_ID = 8;
}
elseif(SITE_ID === "by")
{
	$SLIDER_IBLOCK_ID = 23;
	$REGION_HOUSE_REGIONS = array("Минск");
	$REGION_DEFAULT_REGION = "Минск";
	$REGION_COUNTRY_NAME_ORIG = "Belarus";
    $CATALOG_IBLOCK_ID = 8;
}
elseif(SITE_ID === "tp")
{
    $SLIDER_IBLOCK_ID = 34;
    $REGION_HOUSE_REGIONS = array(
        "Москва", "Санкт-Петербург", "Екатеринбург",
        "Нижний Новгород", "Ростов-на-Дону",
    );
    $REGION_DEFAULT_REGION = "Москва";
    $REGION_COUNTRY_NAME_ORIG = "Russian Federation";
    $CATALOG_IBLOCK_ID = 33;
}

//Подключение mobile detect
if (file_exists(__DIR__ . '/include/lib/Mobile_Detect.php'))
{
    require_once __DIR__ . '/include/lib/Mobile_Detect.php';
    $mobileDetect = new Mobile_Detect;

    if ($_SERVER['REMOTE_ADDR'] == "62.152.84.178") {
     //   echo "<pre>"; print_r($_GET); echo "</pre>";
      //  die();
    }

    if (isset($_REQUEST["show_version"])) {

        $result = setcookie("SHOW_VERSION", $_REQUEST["show_version"], time() + 86400, "/");
        $location = CHTTP::urlDeleteParams($APPLICATION->GetCurPageParam(), array("show_version", "SECTION_CODE_PATH", "ELEMENT_CODE"));
        if ($_SERVER['REMOTE_ADDR'] == "62.152.84.178") {
          //  echo "<pre>"; print_r($_REQUEST); echo "</pre>";
          //  echo "<pre>"; print_r($_GET); echo "</pre>";
            //header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
          //  header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
         //   header("HTTP/1.0 307 Moved Temporarily");
          //  header("Location: " . $location);
          //  exit();
        }


        LocalRedirect($location);




    }




	if ((($mobileDetect->isMobile() && !$mobileDetect->isTablet()) && !isset($_COOKIE["SHOW_VERSION"])) || (isset($_COOKIE["SHOW_VERSION"]) && $_COOKIE["SHOW_VERSION"] == "mobile"))
	{
		if(strpos($_SERVER['SERVER_NAME'], 'ttprom.ru') === false && strpos($_SERVER['SERVER_NAME'], 'prom.textiletorg.ru') === false)
		{
			define('IS_MOBILE', true);
		}
	}
}



// Добавляем поля в почтовый шаблон
/*AddEventHandler("sale", "OnOrderNewSendEmail", "bxModifySaleMails");
function bxModifySaleMails($orderID, &$eventName, &$arFields)
{
	$arOrder = CSaleOrder::GetByID($orderID);
	
	//-- получаем телефоны и адрес
	$order_props = CSaleOrderPropsValue::GetOrderProps($orderID);
	$phone="";
	$index = ""; 
	$country_name = "";
	$city_name = "";  
	$address = "";
	
	while ($arProps = $order_props->Fetch())
	{
		if ($arProps["CODE"] == "PHONE")
		{
			$phone = htmlspecialchars($arProps["VALUE"]);
		}
		if ($arProps["CODE"] == "LOCATION")
		{
			$arLocs = CSaleLocation::GetByID($arProps["VALUE"]);
			$country_name =  $arLocs["COUNTRY_NAME_ORIG"];
			$city_name = $arLocs["CITY_NAME_ORIG"];
		}

		if ($arProps["CODE"] == "INDEX")
		{
			$index = $arProps["VALUE"];
		}

		if ($arProps["CODE"] == "ADDRESS")
		{
			$address = $arProps["VALUE"];
		}
	}

	$full_address = $index.", ".$country_name."-".$city_name.", ".$address;

	//-- получаем название службы доставки
	$arDeliv = CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]);
	$delivery_name = "";
	if ($arDeliv)
	{
		$delivery_name = $arDeliv["NAME"];
	}

	//-- получаем название платежной системы   
	$arPaySystem = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"]);
	$pay_system_name = "";
	if ($arPaySystem)
	{
		$pay_system_name = $arPaySystem["NAME"];
	}

	//-- добавляем новые поля в массив результатов
	$arFields["ORDER_DESCRIPTION"] = $arOrder["USER_DESCRIPTION"]; 
	$arFields["PHONE"] =  $phone;
	$arFields["DELIVERY_NAME"] =  $delivery_name;
	$arFields["PAY_SYSTEM_NAME"] =  $pay_system_name;
	$arFields["FULL_ADDRESS"] = $full_address;
	
	if (CModule::IncludeModule("sale") && $arFields["ORDER_USER"] == "wpwp")
	{
		$rsUser = CUser::GetByLogin($arFields["ORDER_USER"]);
		$arUser = $rsUser->Fetch();
		$arFUser = CSaleUser::GetList(array('USER_ID' => $arUser["ID"]));
		//p($arFUser["ID"]);

		//-- добавляем таблицу товаров
		$dbBasketItems = CSaleBasket::GetList(
			array("NAME" => "ASC"),
			array(
				"ORDER_ID" => $orderID,
				//"FUSER_ID" => CSaleBasket::GetBasketUserID(),
				//"FUSER_ID" => $arFUser["ID"],
				//"LID" => SITE_ID,
				//"ORDER_ID" => "NULL"
			),
			false,
			false,
			array("PRODUCT_ID", "ID", "NAME", "QUANTITY", "PRICE", "CURRENCY")
		);
		$arProducts = array();
		while ($arItem = $dbBasketItems->Fetch())
		{
			$arProducts[$arItem["ID"]] = $arItem;
		}
		
		$arFields["ORDER_COUNT"] = count($arProducts);
		
		$tableProducts .= '
			<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;" width="100%">
				<thead>
					<tr>
						<td align="center">#</td>
						<td align="center">Наименование</td>
						<td align="center">Цена</td>
						<td align="center">Количество</td>
						<td align="center">Итого</td>
					</tr>
				</thead>
				<tbody>';
		$index = 0;
		foreach($arProducts as $arProduct)
		{
			$index++;
			$tableProducts .= '
				<tr>
					<td align="center">'.$index.'</td>
					<td align="left">'.$arProduct["NAME"].'</td>
					<td align="right">'.number_format($arProduct["PRICE"], 2, '.', '').'</td>
					<td align="center">'.$arProduct["QUANTITY"].'</td>
					<td align="right">'.number_format($arProduct["PRICE"] * $arProduct["QUANTITY"], 2, '.', '').'</td>
					
				</tr>
				';
		}
		$tableProducts .= '</tbody></table>';
		$arFields["ORDER_TABLE"] = $tableProducts;
	}
}
*/

AddEventHandler("main", "OnAfterUserAuthorize", Array("AyersHandlers", "OnAfterUserAuthorizeHandler"));

class AyersHandlers
{
    // создаем обработчик события "OnAfterUserAuthorize"
    function OnAfterUserAuthorizeHandler($arUser)
    {
        $arResult = array(
            "ID" => $arUser["user_fields"]["ID"],
            "LOGIN" => $arUser["user_fields"]["LOGIN"],
            "LAST_LOGIN" => $arUser["user_fields"]["LAST_LOGIN"],
            "LAST_ACTIVITY_DATE" => $arUser["user_fields"]["LAST_ACTIVITY_DATE"]
        );

        if (!preg_match('/commerce_xml.php/', $_SERVER["REQUEST_URI"]))
        {
            AddMessage2Log(date("Y-m-d H:i:s")."\n".$_SERVER["REQUEST_URI"]."\n".print_r($arResult, true));
        }
    }
}

AddEventHandler("iblock", "OnAfterIBlockElementAdd", "OnAfterIBlockElementAddHandler");
function OnAfterIBlockElementAddHandler(&$arFields)
{
    if ($arFields["ID"] > 0)
    {
        $el = new CIBlockElement;
        $rs = $el->Update($arFields["ID"], array('XML_ID' => $arFields["ID"]."000001"));
    }
}

// custom sms
AddEventHandler("main", "OnOrderComponentFormed", array("AsOrderSmsSend", "SendSms"));

class AsOrderSmsSend
{
    public function SendSms($arOrder, $sPhone)
    {
        if (CModule::IncludeModule("bxmaker.smsnotice"))
        {
            $oManager = \Bxmaker\SmsNotice\Manager::getInstance();

            $oManager->sendTemplate("ORDER_NEW", array(
                "PHONE" => self::FormatPhone($sPhone),
                "ORDER_ID" => $arOrder["ACCOUNT_NUMBER"],
                "PRICE" => self::FormatSumm($arOrder["PRICE"]),
            ));
        }
    }

    static public function FormatPhone($sPhone)
    {
        $sPhone = preg_replace("/[^0-9]/", "", $sPhone);
        return $sPhone;
    }

    static public function FormatSumm($iSum = 0)
    {
        $iSum = number_format($iSum, 0, ".", " ");
        return $iSum;
    }
}
AddEventHandler('main', 'OnEpilog', 'PagenToTitle');

function PagenToTitle()
{
    global $APPLICATION;
    if (isset($_GET['PAGEN_1']) && intval($_GET['PAGEN_1']) > 1)
    {
        $title = $APPLICATION->GetTitle();

        $APPLICATION->SetPageProperty('title', $title.' – страница '.intval($_GET['PAGEN_1']). ' | Текстильторг');

        if (Helper::IsRealFilePath('/catalog/index.php'))
        {
            $APPLICATION->SetPageProperty('description', 'Заказать '.$title.' на странице '.$_GET['PAGEN_1'].' в каталоге интернет-магазина Textiltorg.');
        }
    }
}


AddEventHandler("main", "OnProlog", "SetCurrentSectionCodeBySectionCodePath");

function SetCurrentSectionCodeBySectionCodePath()
{
    global $APPLICATION;

    if (!empty($_REQUEST["SECTION_CODE_PATH"]))
    {

        $arPath = explode("/", $_REQUEST["SECTION_CODE_PATH"]);
        $_REQUEST["SECTION_CODE"] = array_pop($arPath);
        $_REQUEST["SUB_SECTION_CODE"] = $arPath[count($arPath)-1];

        if (CModule::IncludeModule("iblock"))
        {
            $arFilter = array(
                "IBLOCK_ID" => $GLOBALS["CATALOG_IBLOCK_ID"],
                "CODE" => $_REQUEST["SECTION_CODE"]
            );

            if (!empty($_REQUEST["SUB_SECTION_CODE"]))
            {
                $sectionId = Helper::GetSectionId(array(
                    "IBLOCK_ID" => $GLOBALS["CATALOG_IBLOCK_ID"],
                    "CODE" => $_REQUEST["SUB_SECTION_CODE"]
                ));

                if (!empty($sectionId))
                {
                    $arFilter["SECTION_ID"] = $sectionId;

                    $_REQUEST["SECTION_ID"] = Helper::GetSectionId($arFilter);
                }
                else
                {
                    define("CATALOG_404", "Y");
                }
            }
            else
            {
                $_REQUEST["SECTION_ID"] = Helper::GetSectionId($arFilter);
				}

            if (empty($_REQUEST["SECTION_ID"]))
            {
                define("CATALOG_404", "Y");
            } else {
                if ($filterSectionId = getSectionBrandFoFilter()) {
                    $_REQUEST["FILTER_SECTION_ID"] = $filterSectionId;
                }

            }
        }
    }
}

function getSectionBrandFoFilter() {
    if (false !== strripos($_REQUEST['SMART_FILTER_PATH'], 'brand-is-')) {
        foreach (explode('/', $_REQUEST['SMART_FILTER_PATH']) as $fValue) {
            if (false !== strripos($fValue, 'brand-is-')) {
                $brandName = str_replace('brand-is-', '', $fValue);
                if (false !== strripos($fValue, '-or-')) {
                    return false;
                }
            }
        }
        if(!CModule::IncludeModule("iblock")) {
            return false;
        }

        // выборка только активных разделов из инфоблока $IBLOCK_ID, $ID - раздел-родителя
        $arFilter = Array('IBLOCK_ID' => 8, 'GLOBAL_ACTIVE' => 'Y', 'SECTION_ID' => $_REQUEST["SECTION_ID"], 'CODE' => $brandName);
        $db_list = CIBlockSection::GetList(Array($by => $order), $arFilter, array('nTopCount' => 1), array('ID', 'CODE', 'NAME'));
        if ($ar_result = $db_list->Fetch()) {
           return $ar_result['ID'];
        }
    }
    return false;
}

use Bitrix\Main;
Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleOrderSaved',
    'OnSaleOrderSavedSession'
);

function OnSaleOrderSavedSession(Main\Event $event)
{
    global $DB;
    $isNew = $event->getParameter("IS_NEW");

    if ($isNew)
    {
        $DB->Query("DELETE FROM pending_order WHERE session = '".bitrix_sessid()."'");
    }
}

Main\EventManager::getInstance()->addEventHandler(
    'catalog',
    'OnGetOptimalPrice',
    'OnGetOptimalPriceRegion'
);
function OnGetOptimalPriceRegion($productID, $quantity = 1, $arUserGroups = array(), $renewal = "N", $arPrices = array(), $siteID = false, $arDiscountCoupons = false)
{
    $arPrice = GetPriceregion($productID, $_SESSION['GEO_REGION_CITY_NAME']);
    if (empty($arPrice))
    {

        $arPrice = (SITE_ID == "by")?
            GetPriceregion($productID, "Минск"):
            GetPriceregion($productID, "Москва");
    }

    if (SITE_ID == 's1' || SITE_ID == 'tp')
    {
        if (\Bitrix\Main\Loader::includeModule('ayers.delivery'))
        {
            $isInShops = \Ayers\Delivery\CalcPrice::IsInShops();

            if (!$isInShops)
            {
                $arOptPrices = \Bitrix\Catalog\ProductTable::getById($productID)->fetch();
                $arItems = array(
                    array(
                        'ID' => $productID,
                        'CATALOG_WEIGHT' => $arOptPrices['WEIGHT'],
                        'CATALOG_WIDTH' => $arOptPrices['WIDTH'],
                        'CATALOG_LENGTH' => $arOptPrices['LENGTH'],
                        'CATALOG_HEIGHT' => $arOptPrices['HEIGHT']
                    )
                );

                $optimalCompany = \Ayers\Delivery\CalcPrice::GetOptimalCompany4City($_SESSION['GEO_REGION_CITY_NAME']);

                $arOptimalDelivery4Items = \Ayers\Delivery\CalcPrice::GetOptimalDelivery4Items(
                    $optimalCompany,
                    $_SESSION['GEO_REGION_CITY_NAME'],
                    $arPrice['PRICE'],
                    $arItems
                );

                if (!empty($arOptimalDelivery4Items['PRICE']['DEFAULT']))
                {
                    $arPrice['PRICE'] = $arOptimalDelivery4Items['PRICE']['DEFAULT'];
                }
            }
        }
    }


    return array(
        'PRICE' => array(
            'PRICE' => $arPrice['PRICE'],
            'CURRENCY' => $arPrice['CURRENCY']
        )
    );
}

function GetPriceregion($productID, $name)
{
    $rsPrices = CPrice::GetList(
        array(),
        array(
            "PRODUCT_ID" => $productID,
            "CATALOG_GROUP_NAME" => $name
        )
    );

    if ($arPrice = $rsPrices->Fetch())
    {
        return $arPrice;
    }

    return false;
}

class OrderRepeat {
    // Покупатели за последний час
    static function lastSalesUserId() {
        if (CModule::IncludeModule("sale")) {
            $arrResult = array();
            $arFilter = array(">=DATE_INSERT" => date('d.m.Y H:i:s',strtotime('-1 hour')));
            $dbSales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);
            while ($arSales = $dbSales->Fetch()) {
                $arrResult[] = $arSales["USER_ID"];
            }
            return $arrResult;
        }
    }
    // Получаем телефон покупателя
    static function getUserPropsPhone($userId) {
        $result = null;
        $dbSales = CUser::GetByID($userId);
        $arSales = $dbSales->Fetch();
        if ($arSales["PERSONAL_PHONE"]) {
            $result = self::preparePhone($arSales["PERSONAL_PHONE"]);
        }
        return $result;
    }
    static function preparePhone($phone) {
        $result = null;
        $phoneCods = 8;
        $phoneNumberCount = 11;
        $replace = array("+","-","(",")"," ");

        $phone = str_replace($replace, "", $phone);
        $count = strlen($phone);

        // Если номер из 11 цифр и код 8
        if ($count == $phoneNumberCount) {
            $phone = preg_replace("/^7/", $phoneCods, $phone);
            if ($phone[0] == $phoneCods) {
                $result = $phone;
            }
        } elseif ($count == 10) { // Если номер указан без кода
            $result = $phone;
        }
        return $result;
    }
    // Проверяем присутсвует ли указанный телфон в списке за последний час
    static function isOrderRepeat($phone) {
        $phone = self::preparePhone($phone);
        $result = false;
        $arPhone = array();
        $arrUserId = self::lastSalesUserId();
        foreach ($arrUserId as $id => $value) {
            if ($phoneTmp = self::getUserPropsPhone($value)) {
                $arPhone[] = $phoneTmp;
            }
        }
        if (in_array($phone, $arPhone)) {
            $result = true;
        }
        return $result;
    }
}

class Helper {
    static function RemoveTwoSlash($text)
    {
        return preg_replace('/\/{2,}/', '/', $text);
    }

	static function WordWrap($word, $text, $tag = 'span')
    {
        return preg_replace('/('.$word.')(?=[^>]*(<|$))/iu', '<'.$tag.'>$1</'.$tag.'>', $text);
    }

    static function GetRequestUrl()
    {
        $url = preg_replace('/\/filter\/.+/', '/', $_SERVER['REQUEST_URI']);
        return preg_replace('/\?.+/', '', $url);
    }

    static function GetRequestFilterUrl()
    {
        $url = preg_replace('/.+(\/filter\/.+)/', '$1', $_SERVER['REQUEST_URI']);
        return $url;
    }

    static function IsRealFilePath($path = array())
    {
        $server = \Bitrix\Main\Context::getCurrent()->getServer();
        $realFilePath = $server->get('REAL_FILE_PATH');
        $output = false;

        if (is_array($path))
        {
            foreach ($path as $value)
            {
                if ($value == $realFilePath)
                {
                    $output = true;
                    break;
                }
            }
        }

        if (is_string($path))
        {
            if ($path == $realFilePath)
            {
                $output = true;
            }
        }

        return $output;
    }

    static function RemoveOneLavelUrl($url = '')
    {
        return preg_replace("/^\/[-_a-z]+(\/.+)/i", "$1", $url);
    }
    /**
     * ArKeyUpper
     * Преобразование ключей массива в верхний регистр
     *
     * @param array
     * @return boolean
     */
    static function ArKeyUpper(&$array)
    {
        foreach($array as &$value)
        {
            if(is_array($value))
            {
                self::ArKeyUpper($value);
            }
        }
        $array = array_change_key_case($array, CASE_UPPER);

        return $array;
    }

    /**
     * ArKeyLower
     * Преобразование ключей массива в нижний регистр
     *
     * @param array
     * @return boolean
     */
    static function ArKeyLower(&$array)
    {
        foreach($array as &$value)
        {
            if(is_array($value))
            {
                self::ArKeyLower($value);
            }
        }
        $array = array_change_key_case($array, CASE_LOWER);

        return $array;
    }

    /**
     * Resize
     * Ресайз изображений
     *
     * @param array
     * @param integer
     * @param integer
     * @return array
     */
    static function Resize($pictures, $width, $height, $proportional = false)
    {
        foreach ($pictures as $picture)
        {
            if(count($picture) > 0 && $picture !== NULL && $picture !== false)
            {
                $type = ($proportional)? BX_RESIZE_IMAGE_PROPORTIONAL_ALT : BX_RESIZE_IMAGE_EXACT;
                $return = CFile::ResizeImageGet(
                    $picture,
                    array(
                        "width" => intval($width),
                        "height" => intval($height)
                    ),
                    $type,
                    true
                );

                break;
            }
        }

        return self::ArKeyUpper($return);
    }

    /**
     * Truncate
     * Обрезание текста для анонса по словам
     *
     * @param string
     * @param integer
     * @param string
     * @param string
     * @param boolean
     * @param boolean
     * @return string
     */
    static function Truncate($string, $length = 80, $etc = "...", $charset="UTF-8", $breakWords = false, $middle = false)
    {
        if ($length == 0)
            return "";

        if (strlen($string) > $length) {
            $length -= min($length, strlen($etc));
            if (!$breakWords && !$middle)
                $string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length+1, $charset));

            if(!$middle)
                return rtrim(mb_substr($string, 0, $length, $charset),".,?\s") . $etc;
            else
                return rtrim(mb_substr($string, 0, $length/2, $charset),".,?\s") . $etc . rtrim(mb_substr($string, -$length/2, $charset),".,?\s");
        } else
            return rtrim($string, ".,?\s");
    }

    /**
     * DeclOfNum
     * Функция склонения числительных в русском языке
     *
     * @param int      $number  Число которое нужно просклонять
     * @param array  $titles      Массив слов для склонения
     * @return string
     **/
    function DeclOfNum($number, $titles)
    {
        $cases = array (2, 0, 1, 1, 1, 2);
        return $titles[ ($number%100 > 4 && $number %100 < 20) ? 2 : $cases[min($number%10, 5)] ];
    }

    // Helper::Result();
    function Result($arResult = array(), $isArray = false, $buffer = false, $isDie = false)
    {
        global $APPLICATION, $USER;

        if ($USER->IsAdmin())
        {
            if ($buffer)
                $APPLICATION->RestartBuffer();

            echo "<pre class=\"result\">";
            if ($isArray)
                print_r($arResult);
            else
                var_dump($arResult);
            echo "</pre>";
        }

        if ($isDie)
            die;
    }

    // Helper::isMrc();
    static function isMrc($arCartItemsId) {
        $bernina = array(30,251,264,268);
        $isMrc = true;
        if (SITE_ID == 's1' && $arCartItemsId)
        {
            $arItems = CIBlockElement::GetList(
                array("SORT" => "ASC"),
                array("ID" => $arCartItemsId),
                false,
                false,
                array("ID","CATALOG_GROUP_9", "IBLOCK_SECTION_ID")
            );
            while ($arItem = $arItems->Fetch())
            {
                $mrc = intval($arItem["CATALOG_PRICE_9"]);
                // Если bernina, online оплата и кредит через менеджеров
                if (in_array($arItem["IBLOCK_SECTION_ID"], $bernina)) {
                    $isMrc = false;
                }
            }
        }
        return $isMrc;
    }

    // Helper::Console();q
    static function Console($text)
    {
        global $APPLICATION, $USER;

        if ($USER->IsAdmin())
        {
            $result = "<script>console.log(";

            if (is_array($text) || is_object($text))
                $result .= json_encode($text);
            else
                $result .= $text;

            $result .= ");</script>";

            echo $result;
        }
    }

    static function GetSectionByCode($iblock, $code, $section)
    {
        if (empty($code))
            return false;

        $arFilter = array(
            "IBLOCK_ID" => $iblock,
            "CODE" => $code
        );

        if (!empty($section))
            $arFilter["IBLOCK_SECTION_ID"] = self::GetSectionByCode($iblock, $section);

        $rsSection = CIBlockSection::GetList(
            array(),
            $arFilter,
            false,
            array("ID")
        );

        if ($arSection = $rsSection->GetNext())
            return $arSection["ID"];

        return false;
    }

    static function GetSectionId($arFilter = array())
    {
        if (CModule::IncludeModule("iblock") && !empty($arFilter))
        {
            $dbSections = CIBlockSection::GetList(
                array("SORT" => "­­ASC"),
                $arFilter,
                false,
                array("ID", "NAME" ,"SECTION_ID")
            );

            if ($arSection = $dbSections->GetNext())
                return $arSection["ID"];
        }

        return false;
    }

    static function GetYandexCounter($target = "", $onClick = true)
    {
        $result = "";
        if ($_SERVER['SERVER_NAME'] == 'spb.textiletorg.ru') {
            $counter = "48343148";
        } elseif (SITE_ID == "tp") {
            $counter = "46320975";
        } else {
            $counter = "1021532";
        }
        if ($onClick) {
            $result .= "yaCounter".$counter.".reachGoal('".$target."'); return true;";
        } else {
            $result .= "yaCounter".$counter.".reachGoal('".$target."');";
        }

        return $result;
    }

    static function GetSection2Level($id)
    {
        $arResult = array();

        if (empty($id))
        {
            return false;
        }

        CModule::IncludeModule("iblock");
        $rsSections = CIBlockSection::GetByID($id);

        if ($arSection = $rsSections->GetNext())
        {
            if ($arSection["DEPTH_LEVEL"] > 2)
            {
                $arResult = self::GetSection2Level($arSection["IBLOCK_SECTION_ID"]);
            }
            else
            {
                $arResult = $arSection;
            }
        }

        return $arResult;
    }
}

AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('ASIntervalProp', 'GetUserTypeDescription'));
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/iblock/classes/general/prop_datetime.php';

class ASIntervalProp
{
    /**
     * Возвращает описание типа свойства.
     * @return array
     */
    public static function GetUserTypeDescription()
    {
        return array(
            'DESCRIPTION' => 'Временной интервал',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'ASDateStartEnd',
            'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
            'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML'),
            'ConvertToDB' => array(__CLASS__, 'ConvertToDB'),
            'ConvertFromDB' => array(__CLASS__, 'ConvertFromDB'),
        );
    }

    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        global $APPLICATION;
        $APPLICATION->SetAdditionalCSS('/local/admin/properties/time_interval.css');
        $APPLICATION->AddHeadScript('https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js');
        $APPLICATION->AddHeadScript('/local/admin/properties/time_interval.js');

        $arValue = self::GetArrayValue($value['VALUE']);

        $html = '<input type="hidden" class="value" NAME="'.$strHTMLControlName['VALUE'].'" value="' . $value['VALUE'] . '">';
        $html .= '<div class="time-interval">'.
                    'с <input type="text" class="start-hour" value="' . $arValue['START_HOUR'] . '" size="1" maxlength="2"> : '.
                    '<input type="text" class="start-min" value="' . $arValue['START_MIN'] . '" size="1" maxlength="2">'.
                '</div>'.
                '<div class="time-interval margin">'.
                    'до <input type="text" class="end-hour" value="' . $arValue['END_HOUR'] . '" size="1" maxlength="2"> : '.
                    '<input type="text" class="end-min" value="' . $arValue['END_MIN'] . '" size="1" maxlength="2">'.
                '</div>';

        return $html;
    }

    public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        return $value;
    }

    public static function ConvertToDB($arProperty, $value)
    {
        if (!empty($value['VALUE']))
        {
            $arValue = self::GetArrayValue($value['VALUE']);

            if ($arValue['START_HOUR'] > 23)
            {
                $arValue['START_HOUR'] = 23;
            }

            if ($arValue['START_MIN'] > 59)
            {
                $arValue['START_MIN'] = 59;
            }

            if ($arValue['END_HOUR'] > 23)
            {
                $arValue['END_HOUR'] = 23;
            }

            if ($arValue['END_MIN'] > 59)
            {
                $arValue['END_MIN'] = 59;
            }

            if ($arValue['START_HOUR'] && $arValue['START_MIN'] && $arValue['END_HOUR'] && $arValue['END_MIN'])
            {
                $value['VALUE'] = $arValue['START_HOUR'].':'.$arValue['START_MIN'].'||'.$arValue['END_HOUR'].':'.$arValue['END_MIN'];
            }
        }

        return $value;
    }

    public static function ConvertFromDB($arProperty, $value)
    {
        return $value;
    }

    public static function GetArrayValue($value)
    {
        if (empty($value))
        {
            return array();
        }

        $arTmp = explode('||', $value);
        $arStartTmp = explode(':', $arTmp[0]);
        $arEndTmp = explode(':', $arTmp[1]);

        return array(
            'START_HOUR' => str_pad($arStartTmp[0], 2, '0', STR_PAD_LEFT),
            'START_MIN' => str_pad($arStartTmp[1], 2, '0', STR_PAD_LEFT),
            'END_HOUR' => str_pad($arEndTmp[0], 2, '0', STR_PAD_LEFT),
            'END_MIN' => str_pad($arEndTmp[1], 2, '0', STR_PAD_LEFT),
        );
    }
}

class ASCDEK
{
    static function CalcSum($regionName, $arItems)
    {
        $arDefault = array(
            "PERIOD" => "послезавтра",
            "PRICE" => 300
        );

        if (isset($arItems["CATALOG_WEIGHT"]))
        {
            $arItems = array(
                $arItems
            );
        }

        if ($region = self::getRegion($regionName))
        {
            $arGoods = self::GetDataGoods($arItems);

            if ($arGoods)
            {
                $data = self::GetData(array(
                    "version" => "1.0",
                    "dateExecute" => date("Y-m-d"),
                    "senderCityId" => "44",
                    "receiverCityId" => $region->id,
                    "tariffId" => "11",
                    "goods" => $arGoods
                ));

                if ($data->result)
                {
                    return self::GetResultPrint($data);
                }
                else
                    return $arDefault;
            }
        }
    }

    static function GetResultPrint($data)
    {
        return array(
            "PERIOD_PRINT" =>  ($data->result->deliveryPeriodMin == $data->result->deliveryPeriodMax)?
                $data->result->deliveryPeriodMax :
                $data->result->deliveryPeriodMin . "-" . $data->result->deliveryPeriodMax,
            "PERIOD_MAX" => $data->result->deliveryPeriodMax,
            "PERIOD_MIN" => $data->result->deliveryPeriodMin,
            "PRICE" => $data->result->price
        );
    }

    static function GetDataGoods($arItems = array())
    {
        $arGoods = array();

        foreach ($arItems as $arItem)
        {
            $weight = $arItem["CATALOG_WEIGHT"];
            $weight = number_format(($weight / 1000), 1, ".", "");
            $weight = ($weight > 0) ? $weight : "0.4";

            $width = number_format(($arItem["CATALOG_WIDTH"] / 1000), 1, ".", "");
            $height = number_format(($arItem["CATALOG_HEIGHT"] / 1000), 1, ".", "");
            $length = number_format(($arItem["CATALOG_LENGTH"] / 1000), 1, ".", "");

            if ($width > 0 && $height > 0 && $length > 0)
            {
                $arGoods[] = array(
                    "weight" => $weight,
                    "width" => $width,
                    "height" => $height,
                    "length" => $length,
                    "free" => ($arItem["PROPERTY_DELIVERY_VALUE"] == "Бесплатная по РФ") ? true: false
                );
            }
            else
            {
                $arGoods[] = array(
                    "weight" => $weight,
                    "volume" => 0.05,
                    "free" => ($arItem["PROPERTY_DELIVERY_VALUE"] == "Бесплатная по РФ") ? true: false
                );
            }
        }

        return $arGoods;
    }

    static function GetRegion($regionName)
    {
        $data = array(
            'q' => $regionName,
            'name_startsWith' => $regionName
        );

        $json = file_get_contents('http://api.cdek.ru/city/getListByTerm/jsonp.php?' . http_build_query($data));
        $region = json_decode($json);

        if (empty($region->geonames))
        {
            return false;
        }

        return $region->geonames[0];
    }

    static function GetData($arData = array())
    {
        $json = file_get_contents("http://api.cdek.ru/calculator/calculate_price_by_jsonp.php?callback=getdata&json=" . json_encode($arData));
        $json = preg_replace("/^getdata\((.+)\)$/", "$1", $json);
        $result = json_decode($json);

        return $result;
    }
}

AddEventHandler("iblock", "OnBeforeIBlockElementAdd", "OnBeforeIBlockElementAdd");
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "OnAfterIBlockElementAdd");
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "OnBeforeIBlockElementUpdate");

function OnBeforeIBlockElementAdd(&$arFields)
{
    if ($arFields["IBLOCK_ID"] == 8)
    {
        $idPropModelYearRelease = 371; // ID св-ва "Год выпуска модели"
        $idPropBaseYearRelease = 368; // ID св-ва "Год выпуска базы"

        // Значение св-в типа список
        $idPropBaseYearReleaseList1 = 1075;
        $idPropBaseYearReleaseList2 = 1076;
        $idPropBaseYearReleaseList3 = 1077;
        $idPropBaseYearReleaseList4 = 1078;

        if (!empty($arFields["PROPERTY_VALUES"][$idPropModelYearRelease])) {
            if (is_array($arFields["PROPERTY_VALUES"][$idPropModelYearRelease])) {
                foreach ($arFields["PROPERTY_VALUES"][$idPropModelYearRelease] as $val) {
                    $modelYear = $val["VALUE"];
                }
            } else {
                $modelYear = $arFields["PROPERTY_VALUES"][$idPropModelYearRelease];
            }

            $modelYearEef = intval($modelYear);

            if ($modelYear >= 1990) {

                if ($modelYear >= 1990 && $modelYear <= 1997) {
                    $arFields["PROPERTY_VALUES"][$idPropBaseYearRelease][0] = $idPropBaseYearReleaseList1;
                } elseif ($modelYear >= 1998 && $modelYear <= 2004) {
                    $arFields["PROPERTY_VALUES"][$idPropBaseYearRelease][0] = $idPropBaseYearReleaseList2;
                } elseif ($modelYear >= 2004 && $modelYear <= 2011) {
                    $arFields["PROPERTY_VALUES"][$idPropBaseYearRelease][0] = $idPropBaseYearReleaseList3;
                } else {
                    $arFields["PROPERTY_VALUES"][$idPropBaseYearRelease][0] = $idPropBaseYearReleaseList4;
                }

            }
        }

        // set vendor code
        $rsElements = CIBlockElement::GetList(
            array(
                "PROPERTY_VENDOR_CODE" => "DESC"
            ),
            array(
                "IBLOCK_ID" => 8,
                ">PROPERTY_VENDOR_CODE" => 0
            ),
            false,
            array("nTopCount" => 1),
            array("ID", "IBLOCK_ID", "NAME", "PROPERTY_VENDOR_CODE")
        );

        if ($arElement = $rsElements->GetNext())
        {
            $last = intval($arElement["PROPERTY_VENDOR_CODE_VALUE"]);
            if ($last)
            {
                $arFields["PROPERTY_VALUES"][16] = $last + 1;
            }
        }
    }
}

function OnAfterIBlockElementAdd(&$arFields)
{
    //xml id
    if ($arFields["ID"] > 0)
    {
        $el = new CIBlockElement;
        $rs = $el->Update($arFields["ID"], array('XML_ID' => $arFields["ID"]."000001"));
    }
}

function OnBeforeIBlockElementUpdate(&$arFields)
{
    if ($arFields["IBLOCK_ID"] == 8)
    {
        $idPropModelYearRelease = 371; // ID св-ва "Год выпуска модели"
        $idPropBaseYearRelease = 368; // ID св-ва "Год выпуска базы"

        // Значение св-в типа список
        $idPropBaseYearReleaseList1 = 1075;
        $idPropBaseYearReleaseList2 = 1076;
        $idPropBaseYearReleaseList3 = 1077;
        $idPropBaseYearReleaseList4 = 1078;

        if (!empty($arFields["PROPERTY_VALUES"][$idPropModelYearRelease])) {
            if (is_array($arFields["PROPERTY_VALUES"][$idPropModelYearRelease])) {
                foreach ($arFields["PROPERTY_VALUES"][$idPropModelYearRelease] as $val) {
                    $modelYear = $val["VALUE"];
                }
            } else {
                $modelYear = $arFields["PROPERTY_VALUES"][$idPropModelYearRelease];
            }

            $modelYearEef = intval($modelYear);

            if ($modelYear >= 1990) {

                if ($modelYear >= 1990 && $modelYear <= 1997) {
                    $arFields["PROPERTY_VALUES"][$idPropBaseYearRelease][0] = $idPropBaseYearReleaseList1;
                } elseif ($modelYear >= 1998 && $modelYear <= 2004) {
                    $arFields["PROPERTY_VALUES"][$idPropBaseYearRelease][0] = $idPropBaseYearReleaseList2;
                } elseif ($modelYear >= 2004 && $modelYear <= 2011) {
                    $arFields["PROPERTY_VALUES"][$idPropBaseYearRelease][0] = $idPropBaseYearReleaseList3;
                } else {
                    $arFields["PROPERTY_VALUES"][$idPropBaseYearRelease][0] = $idPropBaseYearReleaseList4;
                }

            }
        }
    }
}

/*
 * Функция возвращает измененный падеж
 * $name - соходное слово
 * $padez - в какое падеж склонять ("ИМ", "РД", "ДТ", "ВН", "ТВ", "ПР")
 * $chislo - число
 */
function ChangeMorphology($name, $padez = 'ВН', $chislo = 'ЕД')
{
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/lib/phpmorphy/src/common.php");

    // Укажите путь к каталогу со словарями
    $dir = $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/lib/phpmorphy/dicts";

    $lang = 'ru_RU';

    // Укажите опции
    // Список поддерживаемых опций см. ниже
    $opts = array(
        'storage' => PHPMORPHY_STORAGE_FILE,
        'resolve_ancode' => phpMorphy::RESOLVE_ANCODES_AS_TEXT
    );

    try {
        $morphy = new phpMorphy($dir, $lang, $opts);
    } catch(phpMorphy_Exception $e) {
        die('Error occured while creating phpMorphy instance: ' . $e->getMessage());
    }

    $arException = array(
        "гладильные прессы" => array("ИМ" => "гладильные прессы", "РД" => "гладильного пресса", "ДТ" => "гладильному прессу", "ВН" => "гладильный пресс", "ТВ" => "гладильным прессом", "ПР" => "гладильном прессе"),
        "гладильная" => array("ИМ" => "гладильные", "РД" => "гладильную", "ДТ" => "гладильной", "ВН" => "гладильную", "ТВ" => "гладильной", "ПР" => "гладильном"),
        "оверлок" => array("ИМ" => "оверлок", "РД" => "оверлок", "ДТ" => "оверлокe", "ВН" => "оверлок", "ТВ" => "оверлоком", "ПР" => "оверлоке"),
        "коверлок" => array("ИМ" => "коверлок", "РД" => "коверлок", "ДТ" => "коверлоке", "ВН" => "коверлок", "ТВ" => "коверлоком", "ПР" => "коверлоке"),
        "утюг с парогенератором" => array("ИМ" => "утюг с парогенератором", "РД" => "утюга с парогенератором", "ДТ" => "утюгу с парогенератором", "ВН" => "утюг с парогенератором", "ТВ" => "утюг с парогенератором", "ПР" => "утюгу с парогенератором"),
        "гладильный паровой пресс" => array("ИМ" => "гладильный паровой пресс", "РД" => "гладильного парового пресса", "ДТ" => "гладильному паровому прессу", "ВН" => "гладильный паровой пресс", "ТВ" => "гладильный паровой пресс", "ПР" => "гладильному паровому прессу"),
        "гладильный пресс" => array("ИМ" => "гладильны пресс", "РД" => "гладильного пресса", "ДТ" => "гладильному прессу", "ВН" => "гладильный пресс", "ТВ" => "гладильный пресс", "ПР" => "гладильному прессу"),
        "с парогенератором" => array("ИМ" => "с парогенератором", "РД" => "с парогенератором", "ДТ" => "с парогенератором", "ВН" => "с парогенератором", "ТВ" => "с парогенератором", "ПР" => "с парогенератором"),
        "парогенератор с утюгом" => array("ИМ" => "парогенератор с утюгом", "РД" => "парогенератора с утюгом", "ДТ" => "парогенератору с утюгом", "ВН" => "парогенератор с утюгом", "ТВ" => "парогенератор с утюгом", "ПР" => "парогенератору с утюгом"),
        "пресс для брюк" => array("ИМ" => "пресс для брюк", "РД" => "пресса для брюк", "ДТ" => "прессу для брюк", "ВН" => "пресс для брюк", "ТВ" => "пресс для брюк", "ПР" => "прессу для брюк"),
        "пылесос компактный сухой и влажной уборки" => array("ИМ" => "пылесос компактный сухой и влажной уборки", "РД" => "пылесоса компактного сухой и влажной уборки", "ДТ" => "пылесосу компактному сухой и влажной уборки", "ВН" => "пылесос компактный сухой и влажной уборки", "ТВ" => "пылесос компактный сухой и влажной уборки", "ПР" => "пылесосу компактному сухой и влажной уборки"),
        "пылесос хозяйственный сухой и влажной уборки" => array("ИМ" => "пылесос хозяйственный сухой и влажной уборки", "РД" => "пылесоса хозяйственного сухой и влажной уборки", "ДТ" => "пылесосу хозяйственному сухой и влажной уборки", "ВН" => "пылесос хозяйственный сухой и влажной уборки", "ТВ" => "пылесос хозяйственный сухой и влажной уборки", "ПР" => "пылесосу хозяйственному сухой и влажной уборки"),
        "зарядная база" => array("ИМ" => "зарядная база", "РД" => "зарядной базы", "ДТ" => "зарядной базе", "ВН" => "зарядную базу", "ТВ" => "зарядную базу", "ПР" => "зарядной базе"),
        "с дозаливом" => array("ИМ" => "с дозаливом", "РД" => "с дозаливом", "ДТ" => "с дозаливом", "ВН" => "с дозаливом", "ТВ" => "с дозаливом", "ПР" => "с дозаливом"),
        "с вышивальным модулем" => array("ИМ" => "с вышивальным модулем", "РД" => "с вышивальным модулем", "ДТ" => "с вышивальным модулем", "ВН" => "с вышивальным модулем", "ТВ" => "с вышивальным модулем", "ПР" => "с вышивальным модулем"),
    );

    $nameLower = mb_strtolower($name);
    if (isset($arException[$nameLower][$padez])) {
        return $arException[$nameLower][$padez];
    }

    $arWord = explode(" ", $name);

    for ($i = 0; $i < count($arWord); $i++) {

        // Запоминаем была ил первая буква закгалвной
        $firstBig = (preg_match("/^[А-ЯA-Z]/u", $arWord[$i]));

        $word = mb_strtoupper($arWord[$i]);

        if (in_array($word, array("ПО", "ДЛЯ"))) break;

        // Пробуем получить род
        $ancore = $morphy->getAncode($word);
        foreach ($ancore as $item) {
            if (preg_match('/НО/', $item["common"])) {
                if (preg_match('/МР/', $item["all"][0])) {
                    $rod = "МР";
                } else if(preg_match('/ЖР/', $item["all"][0])) {
                    $rod = "ЖР";
                }
            } else {
                // Если не получилось получить род ищем по следующему слову
                $ancore = $morphy->getAncode(mb_strtoupper($arWord[$i + 1]));
                foreach ($ancore as $item) {
                    if (preg_match('/НО/', $item["common"])) {
                        if (preg_match('/МР/', $item["all"][0])) {
                            $rod = "МР";
                        } else if(preg_match('/ЖР/', $item["all"][0])) {
                            $rod = "ЖР";
                        }
                    }
                }
            }
        }
        $arMorphy = $morphy->castFormByGramInfo($word, null, array('НО', $padez, $chislo,$rod), true);
        $arWord[$i] = ($arMorphy) ? $arMorphy[0] : $word;

        $arWord[$i] = mb_strtolower($arWord[$i]);

        // Если первая буква была заглавной
        if ($firstBig) {
            //$arWord[$i] = ucfirst($arWord[$i]);
            $arWord[$i] = mb_convert_case($arWord[$i], MB_CASE_TITLE, 'UTF-8');
        }
    }
    //return strtolower(implode(" ", $arWord));
    return implode(" ", $arWord);
}

function ChangeMorphologyText($text, $padez = 'ВН', $chislo = 'ЕД')
{
    $result = '';
    $text = stripcslashes(preg_replace('/\s{2,}/', ' ', $text));
    $text = str_replace(array('"',"'"), '“', $text);

    if (!preg_match('/([-а-я\s]+)(ника\s[-а-я0-9\s]+)/ius', $text, $words))
    {
        preg_match('/([-а-я\s]+)?(“[0-9а-я\s]+“)?(\([-0-9а-я\s]+\))?([-a-z0-9\*\%\s]+)?([а-я\s]+)?(\([-0-9а-я\s\*\%]+\))?([-a-z0-9\s\*\%]+)?([-а-я\s]+)?/isu', $text, $words);
    }

    for ($i=1; $i < count($words); $i++)
    {
        $word = trim($words[$i]);

        $result .= (preg_match('/[a-z0-9\(\)]+/', $word))? ' '.$word: ' '.ChangeMorphology($word, $padez, $chislo);

        if ($i == 1)
        {
            $result = mb_strtolower($result);
        }
    }

    $result = trim(preg_replace('/\s{2,}/', ' ', $result));

    return $result;
}

/*
 * Функция заменяет макросы с учетом падежа
 * $text - текст для замены
 * $arMacris - массив макросов
 */
function ReplaceMacrosInText($text, $arMascros)
{
    $arPadesh = array(
        "IM" => "ИМ",
        "RD" => "РД",
        "DT" => "ДТ",
        "VN" => "ВН",
        "TV" => "ТВ",
        "PR" => "ПР"
    );

    foreach ($arMascros as $macros => $macrosR) {
        foreach ($arPadesh as $padeshEn => $padeshRu) {
            $search = "#".$macros."_".$padeshEn."#";
            $replace = ChangeMorphology($macrosR, $padeshRu);
            /*
            // Если регион, то делаем город с большой буквы
            if ($macros == "REGION") {
                foreach (array(" ", "-") as $del) {

                    $arCity = explode($del, $replace);

                    foreach ($arCity as $key => $val) {
                        if (!in_array($val, array("на"))) {
                            $arCity[$key] = mb_convert_case($val, MB_CASE_TITLE, 'UTF-8');
                        }
                    }
                }
                $replace = implode($del, $arCity);
            }*/
            $text = str_replace($search, $replace, $text);
        }
    }
    return $text;
}


function mb_lcfirst($text) {
    return mb_strtolower(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}
function p($a) {
    global $USER;
    if ($USER->IsAdmin()) {
        echo "<pre style=\"border:1px #F00 dashed;background:#FFF;\">";
        print_r($a);
        echo "</pre>";
    }
}

include 'include/UserDataList.php';

AddEventHandler("main", "OnEpilog", "SetSeoData");
function SetSeoData()
{    global $APPLICATION;
    $dir = $APPLICATION->GetCurDir();
    $uri = $APPLICATION->GetCurUri();
    // $title = CMain::GetTitle();
    $title = $APPLICATION->GetTitle();
    $m_title = $APPLICATION->GetProperty("title");
   // $aSEOData['title'] = $title.' | Cеть магазинов "ТекстильТорг"';
    $aSEOData['descr'] = '';
    $aSEOData['keywr'] = '';
    $aSEOData['h1'] = '';
	switch ($uri)
    {
    			/*
      case '':
        $aSEOData['title'] = '';
        $aSEOData['descr'] = '';
        $aSEOData['keywr'] = '';
		$aSEOData['h1'] = '';
      break;
*/
    }
    // Установка новых значений
    if(!empty($aSEOData['title'])) $APPLICATION->SetPageProperty('title', $aSEOData['title']);
    if(!empty($aSEOData['descr'])) $APPLICATION->SetPageProperty('description', $aSEOData['descr']);
    if(!empty($aSEOData['keywr'])) $APPLICATION->SetPageProperty('keywords', mb_strtolower($aSEOData['keywr']));
    if(!empty($aSEOData['h1']))    $APPLICATION->SetTitle($aSEOData['h1']);
}


/*textiletorg.ru - ОТО #4741443*/
/* Генерация title */
function SetTitle($h1){
$urltitlearray = array(
'/tkani-dlya-poshiva-odezhdy/podkladochnye/',
'/tkani-dlya-poshiva-odezhdy/dekorativnye/',
'/tkani-dlya-poshiva-odezhdy/platevye/',
'/tkani-dlya-poshiva-odezhdy/',
'/tkani-dlya-poshiva-odezhdy/bluzochnye/',
'/tkani-dlya-poshiva-odezhdy/teplyy-trikotazh/',
'/tkani-dlya-poshiva-odezhdy/paltovye/',
'/tkani-dlya-poshiva-odezhdy/kostyumnye/',
'/tkani-dlya-poshiva-odezhdy/sorochechnye/',
'/tkani-dlya-poshiva-odezhdy/naryadnye-tkani/',
'/tkani-dlya-obivki-mebeli/',
'/tkani-dlya-obivki-mebeli/kurtochno-plashchevye/',
'/tkani-dlya-obivki-mebeli/iskusstvennaya-kozha/',
'/tkani/',
'/tkani-dlya-poshiva-shtor-i-zanovesey/',
'/tkani-dlya-poshiva-shtor-i-zanovesey/trikotazh/',
'/tkani-dlya-postelnogo-belya/shelk/',
'/tkani-dlya-postelnogo-belya/',
'/aksessuary-dlya-vyazaniya/spitsy/spitsy-prym-alyuminievye-30sm-30mm-191452.html',
'/aksessuary-dlya-vyazaniya/spitsy/spitsy-prym-alyuminievye-30sm-35mm-191453.html',
'/aksessuary-dlya-vyazaniya/spitsy/spitsy-prym-alyuminievye-30sm-45mm-191455.html',
'/aksessuary-dlya-vyazaniya/spitsy/spitsy-prym-alyuminievye-30sm-40mm-191454.html',
'/aksessuary-dlya-vyazaniya/spitsy/spitsy-prym-alyuminievye-30sm-25mm-191451.html',
'/gladilnie-manekeni/eolo/gladilnyy-maneken-eolo-sa05-1.html',
'/gladilnie-manekeni/eolo/gladilnyy-maneken-eolo-sa04rp.html',
'/gladilnie-manekeni/eolo/gladilnyj-maneken-eolo-sa01-rubashka.html',
'/gladilnie-manekeni/eolo/gladilnyy-maneken-eolo-sa03.html',
'/gladilnie-manekeni/eolo/gladilnyy-maneken-dlya-rubashek-eolo-sa04r.html',
'/gladilnie-manekeni/eolo/gladilnyy-maneken-eolo-sa05-1-inox.html',
'/aksessuary-dlya-shitya/nozhnitsy/nozhnitsy-vyshivalnye-aurora-au-101-02.html',
'/aksessuary-dlya-shitya/nozhnitsy/nozhnitsy-vyshivalnye-aurora-au-405se.html',
'/aksessuary-dlya-shitya/igly/igly-dzhersi-130705h-suk-n-70-5-sht-schmetz.html',
'/aksessuary-dlya-shitya/igly/igly-dzhersi-130705h-suk-n-80-5-sht-schmetz.html',
'/aksessuary-dlya-shitya/igly/igly-dzhersi-130705h-suk-n-100-5-sht-schmetz.html',
'/prinadlezhnosti/silver-reed/intarsionnaya-karetka-silver-reed-ag-11.html',
'/prinadlezhnosti/silver-reed/intarsionnaya-karetka-silver-reed-ag-24.html',
'/aksessuary-dlya-shitya/spetsialnye-niti/nitki-dlya-potshva-monofil-n-60-1000m-belyy.html',
'/aksessuary-dlya-shitya/spetsialnye-niti/nitki-dlya-potshva-monofil-n-60-1000m-chernyy.html',
'/aksessuary-dlya-shitya/kvilting-i-pechvork/napolniteli/napolniteli-dlya-kviltov-poliester-freudenberg.html',
'/aksessuary-dlya-shitya/kvilting-i-pechvork/napolniteli/napolniteli-dlya-kviltov-poliester-53019-45dm-h-25yard-freudenberg.html',
'/aksessuary-dlya-shitya/kvilting-i-pechvork/napolniteli/napolniteli-dlya-kviltov-hlopok-49508-freudenberg.html',
'/gladilnie-manekeni/eolo/gladilnyj-maneken-eolo-sa10-shtany.html',
'/gladilnie-manekeni/eolo/gladilnyy-maneken-eolo-sa08-shtany.html',
'/aksessuary-dlya-shitya/stoliki/pristavnoy-stolik-brother-i-prisposoblenie-dlya-svobodnogo-peremescheniya-tfm3-html-5616.html',
'/aksessuary-dlya-shitya/stoliki/pristavnoy-stolik-brother-i-prisposoblenie-dlya-svobodnogo-peremescheniya-tfm.html',
'/aksessuary-dlya-shitya/nitki-dlya-shveynyh-mashin/nit-nizhnyaya-madiera-bobbinfil-n-70-500-m.html',
'/aksessuary-dlya-shitya/nitki-dlya-shveynyh-mashin/nit-nizhnyaya-madiera-bobbinfil-n-70-1500-m.html',
'/aksessuary-dlya-shitya/shkatulki/shkatulka-tkanevaya-au-262617-aurora.html',
'/aksessuary-dlya-shitya/shkatulki/shkatulka-tkanevaya-au-192814-aurora.html',
'/aksessuary-dlya-shitya/spetsialnye-niti/nitki-dlya-potshva-monofil-n-40-500m-chernyy.html',
'/aksessuary-dlya-shitya/spetsialnye-niti/nitki-dlya-potshva-monofil-n-40-1000-m-chernyy.html',
'/aksessuary-dlya-shitya/shkatulki/komplekt-shkatulok-tkanevyh-2pr-aurora.html',
'/aksessuary-dlya-shitya/shkatulki/komplekt-shkatulok-tkanevyh-2pr-au-303020-au-182311-aurora.html',
'/gladilnie-manekeni/eolo/gladilnyy-maneken-eolo-sa04-nastolnyy.html',
'/gladilnie-manekeni/eolo/gladilnyy-maneken-universalnyy-eolo-sa06-inox.html',
'/aksessuary-dlya-shitya/nitki-dlya-shveynyh-mashin/nabor-nitok-gutermann-sew-all-8-katushek-s-nozhnicami-salatovyj.html',
'/aksessuary-dlya-shitya/nitki-dlya-shveynyh-mashin/nabor-nitok-gutermann-sew-all-8-katushek-s-nozhnicami-goluboj.html',
'/aksessuary-dlya-shitya/mebel/vstavka-dlya-komfortnogo-shitya-dlya-stolov-serii-l-xl.html',
'/aksessuary-dlya-shitya/mebel/vstavka-dlya-komfortnogo-shitya.html',
'/aksessuary-dlya-uborki/prochee/',
'/aksessuary-dlya-uborki/',
'/shveynye-mashiny/bernina/shveyno-vyshivalnaya-mashina-bernina-b-560.html',
'/shvejno-vyshivalnye-mashiny/bernina/shvejno-vyshivalnaja-mashina-bernina-560.html',
'/parogeneratory/mie/parogenerator-s-utyugom-mie-stiro-1100-inox-black.html',
'/parogeneratory/mie/otparivatel-mie-magic-style.html',
'/pylesosy/kambrook/',
'/pylesosy/',
);	
	global $APPLICATION;
	if($h1==''){
		$h1 = $APPLICATION->GetTitle();
	}
	else{
		if(strpos($h1,'!')>0)
			$h1=substr($h1,0,strpos($h1,'!'));
	}
		$aSEOData['title'] = $h1.' | Cеть магазинов "ТекстильТорг"';
		if (in_array($_SERVER['REQUEST_URI'],$urltitlearray))
			{$APPLICATION->SetPageProperty('title', $aSEOData['title']);}
		/*else
			{$APPLICATION->SetPageProperty('title', $aSEOData['title']);}*/
}
/*textiletorg.ru - ОТО #4741443*/
/* Замена ссылок */
AddEventHandler("main", "OnEndBufferContent", "ChangeMyContent");
function ChangeMyContent(&$content)
{
    global $APPLICATION;
    $dir = $APPLICATION->GetCurDir();
    $uri = $_SERVER['REQUEST_URI'];
    if(empty($uri)) $uri = $APPLICATION->GetCurUri();
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/ReplaceLinks.php')) {
        include_once($_SERVER['DOCUMENT_ROOT'] . '/ReplaceLinks.php');
    }
    $content = str_replace("<head>", "<head><!-- Content rewriter enabled -->", $content);
    $content = str_replace('<head>', '<head><!--origUrl="' . $uri . '"-->' , $content);
    foreach ($replace_links as $old_url => $new_url){
        $content = str_replace($old_url.'"', $new_url.'"', $content);
    }
}

// Вырезатель type="text/javascript" :
AddEventHandler("main", "OnEndBufferContent", "removeType");

function removeType(&$content) {
	$content = replace_output($content);
}

function replace_output($d) {
	$text = str_replace(' type="text/javascript"', "", $d);
	$text = str_replace('async="true"', "async", $text);
	return $text;
}


