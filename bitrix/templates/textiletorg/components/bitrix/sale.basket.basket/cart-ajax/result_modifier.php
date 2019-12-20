<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
CModule::IncludeModule("iblock");
$arResult["IS_PRICE_RB"] = ($arParams["IS_PRICE_RB"] == "Y") ? "Y": "N";
$arResult["CUSTOM_ALL_COUNT"] = 0;

$arProdudtIDs = array();
foreach ($arResult["ITEMS"]["AnDelCanBuy"] as $nItem => $arItem)
{
    $arResult["CUSTOM_ALL_COUNT"] += $arItem["QUANTITY"];

    if ($arItem["CURRENCY"] == "BYN")
    {
        $arItem["RB_SUM"] = $arItem["SUM"];
        $arItem["SUM"] = number_format(($arItem["PRICE"] * $arItem["QUANTITY"]), 2, ",", " ")." руб.";
        $arItem["FULL_SUM"] = number_format(($arItem["FULL_PRICE"] * $arItem["QUANTITY"]), 2, ",", " ")." руб.";

        $arResult["IS_PRICE_RB"] = "Y";
    }
    else
    {
        $arItem["SUM"] = number_format($arItem["PRICE"] * $arItem["QUANTITY"], 0, ",", " ")." руб.";
        $arItem["FULL_SUM"] = number_format(($arItem["FULL_PRICE"] * $arItem["QUANTITY"]), 0, ",", " ")." руб.";
    }

    $arResult["ITEMS"]["AnDelCanBuy"][$nItem] = $arItem;

    $arProdudtIDs[] = $arItem["PRODUCT_ID"];
}

if ($arResult["IS_PRICE_RB"] != "Y")
{
    $arResult["CUSTOM_ALL_SUM"] = number_format($arResult["allSum"], 0, ".", " ");
}
else
{
    $arResult["CUSTOM_ALL_SUM_RB"] = number_format($arResult["allSum"], 0, ",", " ");
    $arResult["CUSTOM_ALL_SUM"] = number_format($arResult["allSum"] , 2, ",", " ");
}

// gift wraps
if ($arProdudtIDs)
{
    $rsElements = CIBlockElement::GetList(
        array(),
        array(
            "ACTIVE" => "Y",
            "ID" => $arProdudtIDs
        ),
        false,
        array(),
        array("ID", "IBLOCK_ID", "NAME", "PROPERTY_IS_GIFT_BOXES")
    );

    while($arElement = $rsElements->GetNext())
        $arResult["PRODUCTS"][$arElement["ID"]] = $arElement;

    // --
    foreach ($arResult["ITEMS"]["AnDelCanBuy"] as $nItem => $arItem)
    {
        $arProduct = $arResult["PRODUCTS"][$arItem["PRODUCT_ID"]];

        if ($arProduct["IBLOCK_ID"] == 8 && $arProduct["PROPERTY_IS_GIFT_BOXES_VALUE"] == "Да")
        {
            $arItem["IS_GIFT_WRAPS"] = true;
            $arResult["IS_GIFT_WRAPS"] = true;
        }

		$arItem["IBLOCK_ID"] = $arProduct["IBLOCK_ID"];

        //url
        $arItem["DETAIL_PAGE_URL"] = Helper::RemoveOneLavelUrl($arItem["DETAIL_PAGE_URL"]);

        $arResult["ITEMS"]["AnDelCanBuy"][$nItem] = $arItem;
    }
}

if (isset($_REQUEST["ajax_for_credit"]) && $_REQUEST["ajax_for_credit"] == "Y") {
    $APPLICATION->RestartBuffer();
    header("Content-Type: application/json");

    // Получим пути по разделам
    $arSectionsPath = array();

    $arFilter = array("IBLOCK_ID"=> 8, "ID" => $arProdudtIDs, "ACTIVE"=>"Y");
    $arSelect = array("ID", "IBLOCK_ID", "NAME", "IBLOCK_SECTION_ID");
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    while($ob = $res->Fetch())
    {
        $arSection = array();
        $nav = CIBlockSection::GetNavChain($ob["IBLOCK_ID"], $ob["IBLOCK_SECTION_ID"], array("NAME"));
        while($arSectionPath = $nav->Fetch()){
           $arSection[] = $arSectionPath["NAME"];
        }
        $ob["PATH"] = implode(" / ", $arSection);

        $arSectionsPath[$ob["ID"]] = $ob;
    }

    // Подготовим необходимый массив
    $arResult["ITEMS_ORDER"] = array("items" => array());

    foreach ($arResult["ITEMS"]["AnDelCanBuy"] as $item)
    {
        $arResult["ITEMS_ORDER"]["items"][] = array(
            "title" => $item["NAME"],
            "category" => $arSectionsPath[$item["PRODUCT_ID"]]["PATH"],
            "qty" => $item["QUANTITY"],
            "price" => $item["PRICE"]
        );
    }

    $arResult["ITEMS_ORDER"]["partnerId"] = "a06b000001s7bJ9AAI";
    $arResult["ITEMS_ORDER"]["partnerOrderId"] = "order_".uniqid();
    $arResult["ITEMS_ORDER"]["partnerName"] = "Текстильторг";

    $arResult["ITEMS_ORDER"]["details"] = array(
        'firstname' => $_REQUEST["firstname"],
        'cellphone' => $_REQUEST["cellphone"]
    );

    $json = json_encode($arResult["ITEMS_ORDER"]);
    $base64 = base64_encode($json);
    $secret = 'textiletorg-secret-s7bJ976d';

    // Формирование подписи
    function signMessage($message, $secretPhrase) {
        $message = $message.$secretPhrase;
        $result = md5($message).sha1($message);
        for ($i = 0; $i < 1102; $i++) {
            $result = md5($result);
        }
        return $result;
    }
    $sign = signMessage($base64, $secret);
    $credit = array("base64" => $base64, "sign" => $sign, "sum" => $arResult["ORDER_SUMM"]);

    echo(json_encode($credit));
    die;
}
?>