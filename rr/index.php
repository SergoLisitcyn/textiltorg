<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule('iblock');
CModule::IncludeModule('currency');

include "function.php";

$siteUrl = 'https://www.textiletorg.ru';
$iblockId = 8;
$priceIds = array(1, 2, 4, 5, 6);

$stringXml = '';

// Получаем идентификатор последнего обработанного товара
$file_handle = fopen("counter.txt", "r");
while (!feof($file_handle)) {
   $line = fgets($file_handle);
}
fclose($file_handle);

$lastId = intval($line);

$finish = false;

// Получаем секции
if ($lastId == 0) {

    $arResult["SECTIONS"] = array();
    $arFilter = Array("IBLOCK_ID" => $iblockId, "ACTIVE" => "Y");
    $db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter);
    while($arRes = $db_list->GetNext())
    {
        $arResult["SECTIONS"][] = $arRes;
    }

    // Записываем секции
    $stringXml = '<yml_catalog date="'.date("Y-m-d H:i").'">
        <shop>
            <name>ТЕКСТИЛЬТОРГ</name>
            <company>ТЕКСТИЛЬТОРГ - продажа бытового и промышленного оборудования</company>
            <url>www.textiletorg.ru</url>
            <currencies>
                <currency id="RUB" rate="1"/>
            </currencies>
            <categories>';

    foreach ($arResult["SECTIONS"] as $arSection) {

        if ($arSection["IBLOCK_SECTION_ID"])
        {
            $stringXml .= '<category id="'.$arSection["ID"].'" parentId="'.$arSection["IBLOCK_SECTION_ID"].'">'.$arSection["NAME"].'</category>';
        }
        else
        {
             $stringXml .= '<category id="'.$arSection["ID"].'">'.$arSection["NAME"].'</category>';
        }
    }

    $stringXml .= '</categories>
        <delivery-options>
            <option cost="300" days="1"/>
        </delivery-options>
        <offers>';
}

$time = time();

while (time() < $time + 30)
{
    $arFilter = array("IBLOCK_ID" => $iblockId, "ACTIVE" => "Y", ">ID" => $lastId, "!SECTION_ID" => false);
    $arSelect = array("ID", "NAME", "IBLOCK_ID", "PREVIEW_TEXT", "IBLOCK_SECTION_ID", "DETAIL_PICTURE", "DETAIL_PAGE_URL");
    foreach ($priceIds as $priceId)
    {
        $arSelect[] = "CATALOG_GROUP_".$priceId;
    }
    $res = CIBlockElement::GetList(array("ID" => "ASC"), $arFilter, false, array("nPageSize"=>50), $arSelect);
    if ($res->SelectedRowsCount())
    {
        while($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties();

            $available = (intval($arFields["CATALOG_QUANTITY"])) ? "true": "false";
            $arFields["DETAIL_PAGE_URL"] = Helper::RemoveOneLavelUrl($arFields["DETAIL_PAGE_URL"]);

            $stringXml .= '
            <offer id="'.$arFields["ID"].'" available="'.$available.'">
                <url>'.$siteUrl.$arFields["DETAIL_PAGE_URL"].'</url>
                <categoryId>'.$arFields["IBLOCK_SECTION_ID"].'</categoryId>
                <picture>'.$siteUrl.CFile::GetPath($arFields["DETAIL_PICTURE"]).'</picture>
                <delivery>true</delivery>
                <name>'.$arFields["NAME"].'</name>
                <vendor>'.$arProps["BRAND"]["VALUE"].'</vendor>
                <description>'.FormatTextDesc($arFields["PREVIEW_TEXT"]).'</description>
                <sales_notes>'.GetSaleNotes(!empty($arProps["PAYMENT_INSTALLMENTS"])).'</sales_notes>
                <manufacturer_warranty>'.GetOfferGuarantee($arProps["GUARANTEE_DEFAULT"]["VALUE"]).'</manufacturer_warranty>';

            // Запись параметров
            foreach ($arProps as $key => $val)
            {
                if(preg_match("/^CHAR_/", $key) && !empty($val["VALUE"]))
                {
                    if (is_array($val["VALUE"]))
                    {
                        $stringXml .= '<param name="'.$val["NAME"].'">'. implode(", ", $val["VALUE"]).'</param>';
                    } else {
                        $stringXml .= '<param name="'.$val["NAME"].'">'.$val["VALUE"].'</param>';
                    }
                }
            }

            // Запись цен
            foreach ($priceIds as $priceId)
            {
                if (!empty($arFields["CATALOG_PRICE_".$priceId]))
                {
                    $stockId = ($priceId > 3) ? $priceId - 1 : $priceId;
                    $stringXml .= '<stock id="'.$stockId.'">
                                        <available>true</available>
                                        <price>'.$arFields["CATALOG_PRICE_".$priceId].'</price>
                                        <oldPrice>'.$arFields["CATALOG_PRICE_".$priceId].'</oldPrice>
                                        <url>'.$siteUrl.$arFields["DETAIL_PAGE_URL"].'</url>
                                    </stock>';
                }
            }

            $stringXml .= '</offer>';

            $lastId = $arFields["ID"];
        }
    }
    else
    {
        $finish = true;
        $stringXml .= '</offers></shop></yml_catalog>';
        break;
    }
}

$stringXml = str_replace('&nbsp;', ' ', $stringXml);
$stringXml = str_replace('&', '&amp;', $stringXml);

$fp = fopen("retailrocket_temp.xml", "a");
fwrite($fp, $stringXml);
fclose($fp);

$fp = fopen("counter.txt", "w");
fwrite($fp, $lastId);
fclose($fp);

if ($finish) {
    // Скидываем счетчик
    $fp = fopen("counter.txt", "w");
    fwrite($fp, "0");
    fclose($fp);

    unlink("retailrocket.xml");
    rename("retailrocket_temp.xml", "retailrocket.xml");
    unlink("retailrocket_temp.xml");
}