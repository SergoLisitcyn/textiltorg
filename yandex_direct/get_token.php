<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
global $USER;

if(!$USER->IsAdmin()){
	die();
}

/*
ID: 84d4411f35dc4d198c51fcbda4bd27de
Пароль: 64ba9ffa61364204a2958573a93268eb
Callback URL: https://www.textiletorg.ru/yandex_direct/get_token.php

tocen = AgAAAAAyt3SXAAXaF2YICMyVAEBaptyqGAoeq0o

*/
// Идентификатор приложения
$client_id = 'b661875023444a58a66cd70e941bbf29';
// Пароль приложения
$client_secret = '0f292e79c8ac47c9a03f1b7caa690e71';

// Если скрипт был вызван с указанием параметра "code" в URL,
// то выполняется запрос на получение токена
if (isset($_GET['code']))
  {
    // Формирование параметров (тела) POST-запроса с указанием кода подтверждения
    $query = array(
      'grant_type' => 'authorization_code',
      'code' => $_GET['code'],
      'client_id' => $client_id,
      'client_secret' => $client_secret
    );
    $query = http_build_query($query);

    // Формирование заголовков POST-запроса
    $header = "Content-type: application/x-www-form-urlencoded";

    // Выполнение POST-запроса и вывод результата
    $opts = array('http' =>
      array(
      'method'  => 'POST',
      'header'  => $header,
      'content' => $query
      ) 
    );
    $context = stream_context_create($opts);
    $result = file_get_contents('https://oauth.yandex.ru/token', false, $context);
    $result = json_decode($result);

    dump(1);

    // Токен необходимо сохранить для использования в запросах к API Директа
    echo $result->access_token;
  }
// Если скрипт был вызван без указания параметра "code",
// пользователю отображается ссылка на страницу запроса доступа
  else 
    {
      echo '<a href="https://oauth.yandex.ru/authorize?response_type=code&client_id='.$client_id.'">Страница запроса доступа</a>';
    }
?>
<?
	
	/*
	$Fields = array(
		"method" => "get",
		"params" => array(
			"SelectionCriteria" => array(
				"CampaignId" => 44610218,
				"State" => "ON"
			)
		)
	);

	if($Ci = curl_init()){
		
		curl_setopt($Ci,CURLOPT_URL,"https://api.direct.yandex.ru/v4/json/");
		curl_setopt($Ci, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;
		curl_setopt($Ci, CURLOPT_USERPWD, "textiletorg-test007:Qw12345678910"); 
		curl_setopt($Ci, CURLOPT_POST, 1);
		curl_setopt($Ci, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($Ci, CURLOPT_POSTFIELDS, json_encode($Fields));
		
		$Data = curl_exec($Ci);
		
		curl_close($Ci);
	}
?>
<pre>
	<?print_r($Data);?>
</pre>
*/