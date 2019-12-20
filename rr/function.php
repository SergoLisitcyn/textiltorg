<?
function FormatTextDesc($text)
{
        $text = ClearText($text);
        $text = TruncateText($text, 250);

        return $text;
}

function ClearText($text)
{
        $text = str_replace("\n", "", $text);
        $text = str_replace("\n\r", "", $text);
        $text = preg_replace("/\t+/", "", $text);
        $text = strip_tags($text);
        return $text;
}

function GetSaleNotes($isPayInst = false)
{
        $arSaleNotes = array(
                "DEFAULT" => "Обучение и Демонстрация Бесплатно!",
                "PAY_INST" => "Покажем и научим - Бесплатно! Рассрочка 0%"
        );

        return ($isPayInst)? $arSaleNotes["PAY_INST"] : $arSaleNotes["DEFAULT"];
}

function GetOfferGuarantee($years = array(), $printYaers = false)
{
    if (intval($years))
    {
        if ($printYaers)
        {
            return intval($years);
        }
        else
        {
            return "P".intval($years)."Y";
        }
    }

    return "true";
}