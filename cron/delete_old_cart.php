<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
function DeleteOld($nDays)
{
    CModule::IncludeModuleEx('sale');
    global $DB;

    $nDays = IntVal($nDays);
    $i = 0;
    $strSql =
        "SELECT ID ".
        "FROM b_sale_fuser ".
        "WHERE TO_DAYS(DATE_UPDATE)<(TO_DAYS(NOW())-".$nDays.") LIMIT 500";
    $db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
    while ($ar_res = $db_res->Fetch())
    {
        $i++;
        CSaleBasket::DeleteAll($ar_res["ID"], false);
        CSaleUser::Delete($ar_res["ID"]);
    }

    echo $i;
    return true;
}

DeleteOld(30);