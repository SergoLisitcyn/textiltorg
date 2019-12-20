<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?

class YandexDirectApiJson {
	
	private $Url = 'https://api.direct.yandex.com/json/v5/';
	private $Token = 'AgAAAAAyt3SXAAXaF2YICMyVAEBaptyqGAoeq0o';
	private $Login = 'textiletorg-test007';
	
	
	public function AdImagesAdd($List = array()){
		$Result = array();
		$Params = array(
			"method" => "add",
			"params" => array(
				"AdImages" => array()
			)
		);
		
		foreach($List as $key => $value){
			if(file_exists($value["PATH"]) && $value["NAME"] != ""){
				$Params["params"]["AdImages"][] = array(
					"ImageData" => base64_encode(file_get_contents($value["PATH"])),
					"Name" => base64_encode($value["NAME"]),
				);
			}
		}
		
		if(!empty($Params["params"]["AdImages"])){
			$Result = $this->Send("adimages","POST",$Params);
			/*
			Response:
			[result] => Array(
	            [AddResults] => Array (
                    [0] => Array(
                            [AdImageHash] => ZepcEZdTqAM75KfUoM9ihQ
					)
				)
	        )
			
			*/
		}
		return $Result;
	}
	
	private function Send($Url = "",$Method = "",$Params = array()){
		$Result = array();
		$Url = $this->Url.$Url;
		$Headers = array(
		   "Authorization: Bearer ".$this->Token,                  
		   "Client-Login: ".$this->Login,                      
		   "Accept-Language: ru",                             
		   "Content-Type: application/json; charset=utf-8"
		);
		$Body = json_encode($Params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$StreamContext = stream_context_create(array(
		   'http' => array(
		      'method' => $Method,
		      'header' => $Headers,
		      'content' => $Body
		   )
		));
		$Result = json_decode(file_get_contents($Url, 0, $StreamContext),true);
		return $Result;
	}
}
/*
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

$Ib = 8;

//ROTHER COMFORT 40E"
$YandexId = 7764751329;

$Htef = "ttp://www.textiletorg.ru/vse-dlya-shitya/brother/shveynaya-mashina-brother-comfort-40e.html?utm_source=yandex_direct&utm_medium=CPC&utm_campaign=direct&ctx=n_nov&utm_content={keyword}/{phrase_id}";
$Name = "BROTHER COMFORT 40E";

$Codes = explode("/",str_replace(".html","",parse_url($Htef,PHP_URL_PATH)));
$CCodes = count($Codes);
$Code = $Codes[$CCodes-1];

$Query = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $Ib,"CODE" => $Code,"NAME" => "%".$Name."%"), false,array("nTopCount" => 1),array("ID","NAME","DETAIL_PICTURE"));

if($Answer = $Query->Fetch()){
	
	$db_res = CPrice::GetList(
        array(),
        array(
                "PRODUCT_ID" => $Answer["ID"],
                "CATALOG_GROUP_ID" => 5
            )
    );
	
	$Img = "";
	if($Answer["DETAIL_PICTURE"] > 0){
		$Answer["DETAIL_PICTURE"] = CFile::GetFileArray($Answer["DETAIL_PICTURE"]);
		
		if($Answer["DETAIL_PICTURE"]["WIDTH"] > $Answer["DETAIL_PICTURE"]["HEIGHT"]){
			$Answer["DETAIL_PICTURE"] = CFile::ResizeImageGet($Answer["DETAIL_PICTURE"], array('width'=>960, 'height'=>640), BX_RESIZE_IMAGE_PROPORTIONAL, true);
		} else {
			$Answer["DETAIL_PICTURE"] = CFile::ResizeImageGet($Answer["DETAIL_PICTURE"], array('width'=>640, 'height'=>960), BX_RESIZE_IMAGE_PROPORTIONAL, true);
		}
		//$Ydaj = new YandexDirectApiJson();
		//$a = $Ydaj->AdImagesAdd(array(array("PATH" => $_SERVER["DOCUMENT_ROOT"].$Answer["DETAIL_PICTURE"]["src"],"NAME" => $Answer["NAME"])));
		
	}
	
	echo  $_SERVER["DOCUMENT_ROOT"].$Answer["DETAIL_PICTURE"]["src"];
	
	die();
	if ($ar_res = $db_res->Fetch())
	{
	  	$ar_res["PRICE"] = intval($ar_res["PRICE"])*1000000;
	  	
	   //--- Входные данные ----------------------------------------------------//
		// Адрес сервиса Ads для отправки JSON-запросов (регистрозависимый)
		$url = 'https://api.direct.yandex.com/json/v5/ads';
		// OAuth-токен пользователя, от имени которого будут выполняться запросы
		$token = 'AgAAAAAyt3SXAAXaF2YICMyVAEBaptyqGAoeq0o';
		// Логин клиента рекламного агентства
		// Обязательный параметр, если запросы выполняются от имени рекламного агентства
		$clientLogin = 'textiletorg-test007';
		// Идентификатор группы объявлений, в которую будет добавлено новое объявление

		//--- Подготовка и выполнение запроса -----------------------------------//
		// Установка HTTP-заголовков запроса
		$headers = array(
		   "Authorization: Bearer $token",                    // OAuth-токен. Использование слова Bearer обязательно
		   "Client-Login: $clientLogin",                      // Логин клиента рекламного агентства
		   "Accept-Language: ru",                             // Язык ответных сообщений
		   "Content-Type: application/json; charset=utf-8"    // Тип данных и кодировка запроса
		);

		// Параметры запроса к серверу API Директа
		$params = array(
			'method' => 'update',                                 // Используемый метод
			'params' => array(
				"Ads" => array(
					array(
						"Id" => $YandexId,
						"TextAd" => array(
							"AdImageHash" => "ZepcEZdTqAM75KfUoM9ihQ",
							"PriceExtension" => array(
								"Price" =>  intval($ar_res["PRICE"]), 
        						"PriceCurrency" => $ar_res["CURRENCY"]
							)
						)
					)
				)
			)
		);



		// Преобразование входных параметров запроса в формат JSON
		$body = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		// Создание контекста потока: установка HTTP-заголовков и тела запроса
		$streamOptions = stream_context_create(array(
		   'http' => array(
		      'method' => 'POST',
		      'header' => $headers,
		      'content' => $body
		   ),
		   
		));


		// Выполнение запроса, получение результата
		$result = file_get_contents($url, 0, $streamOptions);

		$result = json_decode($result,true);

		echo "<pre>";
			print_r($result);
		echo "</pre>";
	}
}


*/

/*

44610218 - 783
44610254 - 889
44610302 - 780
44610340 - 786
44610385 - 151
44610391 - 202
44610406 - 202
44610419 - 202
44610436 - 201
44610448 - 202
44610465 - 794

*/

//--- Входные данные ----------------------------------------------------//
// Адрес сервиса Ads для отправки JSON-запросов (регистрозависимый)
$url = 'https://api.direct.yandex.com/json/v5/ads';
// OAuth-токен пользователя, от имени которого будут выполняться запросы
$token = 'AgAAAAAyt3SXAAXaF2YICMyVAEBaptyqGAoeq0o';
// Логин клиента рекламного агентства
// Обязательный параметр, если запросы выполняются от имени рекламного агентства
$clientLogin = 'textiletorg-test007';
// Идентификатор группы объявлений, в которую будет добавлено новое объявление

//--- Подготовка и выполнение запроса -----------------------------------//
// Установка HTTP-заголовков запроса
$headers = array(
   "Authorization: Bearer $token",                    // OAuth-токен. Использование слова Bearer обязательно
   "Client-Login: $clientLogin",                      // Логин клиента рекламного агентства
   "Accept-Language: ru",                             // Язык ответных сообщений
   "Content-Type: application/json; charset=utf-8"    // Тип данных и кодировка запроса
);

// Параметры запроса к серверу API Директа
$params = array(
	'method' => 'get',                                 // Используемый метод
	'params' => array(
		'SelectionCriteria' => array(
			//"Ids" => array("7764751329"),
			"CampaignIds" => array(44610465),
			"States" => array("ON"),
			"Statuses" => array("ACCEPTED")
		),
		 "FieldNames" => array("AdCategories", "AgeLabel", "AdGroupId", "CampaignId", "Id", "State", "Status", "StatusClarification", "Type", "Subtype"),
		 "TextAdFieldNames" => array("Title","Title2","Text","Href","AdImageHash"),
		 "TextAdPriceExtensionFieldNames" => array("Price","OldPrice","PriceCurrency")
	)
);



// Преобразование входных параметров запроса в формат JSON
$body = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Создание контекста потока: установка HTTP-заголовков и тела запроса
$streamOptions = stream_context_create(array(
   'http' => array(
      'method' => 'POST',
      'header' => $headers,
      'content' => $body
   ),
   
));


// Выполнение запроса, получение результата
$result = file_get_contents($url, 0, $streamOptions);

$result = json_decode($result,true);

echo "<pre>";
	print_r($result);
echo "</pre>";

?>