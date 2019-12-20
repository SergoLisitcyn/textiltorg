<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));

function either($a, $b)  {  if ($a != NULL) return $a; return $b;}

$TMG_PK_SERVER_ADDR = CSalePaySystemAction::GetParamValue("TMG_PK_SERVER_ADDR");

$user_id = (int)$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["USER_ID"];
$sum = number_format(floatval($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"]), 2, ".", "");
$orderid = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ACCOUNT_NUMBER"];

#$email = either($GLOBALS["SALE_INPUT_PARAMS"]["PROPERTY"]["EMAIL"], $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["USER_EMAIL"]);
#$phone = htmlspecialchars($GLOBALS['SALE_INPUT_PARAMS']['PROPERTY']['PHONE']);

$opts = array ("sum"=>$sum, "user_id"=>$user_id);
$payment_parameters = array("clientid"=>$user_id, "orderid"=>$orderid, "sum"=>$sum, "phone"=>$phone, "email"=>$email);
$query = http_build_query($payment_parameters);
$err_num = $err_text = NULL;

$url = parse_url($TMG_PK_SERVER_ADDR);

$port = 80;
$ssl = "";

if ($url['scheme']=='https')
{
  $port = 443;
  $ssl = "ssl://";
}

$form = QueryGetData($url['host'], $port, $url['path'], $query, $err_num, $err_text, "POST", $ssl);

if ($form  == "")
  $form = "<h3>Произошла ошибка при инциализации платежа</h3><p>$err_num: ".htmlspecialchars($err_text)."</p>";
  ?>
  <div id='tmg_pk_form_container'>
  <?=$form?>
  </div>
