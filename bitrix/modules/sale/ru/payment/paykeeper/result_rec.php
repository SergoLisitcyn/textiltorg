<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die("No prolog included");?><?

$iPaymentId = (int)$_POST['id'];
$iUserId = (int)$_POST['clientid'];
$fSum = (float)$_POST['sum'];
$iOrderId = (int)$_POST['orderid'];
$strHash = $_POST['key'];

$APPLICATION->RestartBuffer();

if($iPaymentId == 0)
{
  echo "No payment specified!\n";
  AddMessage2Log("No payment specified");
  die();
}

$bCorrectPayment = True;

$arOrder = CSaleOrder::GetList(
    array(),
    array("ACCOUNT_NUMBER" => $iOrderId)
)->Fetch();

$rsOrderProps = CSaleOrderPropsValue::GetOrderProps($arOrder['ID']);
while ($arOrderProp = $rsOrderProps->Fetch())
{
    if ($arOrderProp["CODE"] == "MS_ID")
    {
        AddMessage2Log("msOrderId найден");
        $msOrderId = $arOrderProp["VALUE"];
        break;
    }
}

if (!($arOrder = CSaleOrder::GetByID($arOrder["ID"])))
{
    $bCorrectPayment = False;
    AddMessage2Log("Order not found");
    echo "Order not found\n";
}

if ($bCorrectPayment)
    CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"]);

$strSecretKey =  CSalePaySystemAction::GetParamValue("TMG_PK_SECRET_KEY");

$strCheck = md5($iPaymentId.number_format($fSum, 2, '.', '').$iUserId.$iOrderId.$strSecretKey);

if ($bCorrectPayment && strtoupper($strHash) != strtoupper($strCheck))
{
    $bCorrectPayment = False;
    AddMessage2Log("Hash mismatch");
    echo "Hash mismatch\n";
}

if($bCorrectPayment)
{
    $arFields = array(
        "PS_STATUS" => "Y",
        "PS_STATUS_CODE" => "Success",
        "PS_STATUS_DESCRIPTION" => "Payment accepted",
        "PS_STATUS_MESSAGE" => "Payment id: $iPaymentId",
        "PS_SUM" => $fSum,
        "PS_CURRENCY" => "",
        "PS_RESPONSE_DATE" => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
    );

    if ((float)$arOrder["PRICE"] == (float)($fSum))
    {
        AddMessage2Log("Сохранение заказа");
        CSaleOrder::PayOrder($arOrder["ID"], "Y");
        CSaleOrder::Update($arOrder["ID"], $arFields);


        define("BASEPATH",$_SERVER['DOCUMENT_ROOT']."moysklad");
        $classPath = $_SERVER['DOCUMENT_ROOT']."/moysklad/moysklad.php";

        if (file_exists($classPath) && $msOrderId)
        {
            require_once $classPath;

            $moysklad = new Moysklad(
                "robot@textiletorg",
                "V21_5!-aSt_7"
            );

            $moysklad->SetStatusPayKeeper($msOrderId, 'Оплата прошла успешно');
            AddMessage2Log("Импорт в МС прошел успешно");
        }
        else
        {
            AddMessage2Log("Не найден файл или msOrderId");
            AddMessage2Log($classPath);
            AddMessage2Log($msOrderId);
        }

        CEvent::Send("PAYKEEPER", "s1", array(
            "ORDER_ID" => $arOrder["ACCOUNT_NUMBER"],
            "SUMM" => $fSum,
            "PAY_DATE" => date("Y.m.d H:i:s"),
            "CLIENT" => trim($arOrder["USER_NAME"]." ".$arOrder["USER_LAST_NAME"])." ".$arOrder["USER_EMAIL"]
        ));

        echo "OK ".md5($iPaymentId.$strSecretKey);
        die();
    }
    else
    {
        AddMessage2Log("Некорректная сумма");
        print_r($arOrder);
        die("Incorrect sum");
    }
}
else
{
    AddMessage2Log("Incorrect payment");
    die("Incorrect payment");
}
?>

