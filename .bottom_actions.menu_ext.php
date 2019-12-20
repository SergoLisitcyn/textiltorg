<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$aMenuLinksExt = array();

if (CModule::IncludeModule("iblock"))
{
    $IBLOCK_ID = 1;

    $rsElements = CIBlockElement::GetList(
        array(
            "SORT" => "ASC",
            "NAME" => "ASC"
        ),
        array(
            "IBLOCK_ID" => $IBLOCK_ID,
            "ACTIVE"=>"Y",
        ),
        false,
        false,
        array("ID", "NAME", "IBLOCK_ID", "DETAIL_PAGE_URL")
    );

    while($obElement = $rsElements->GetNextElement())
    {
        $arFields = $obElement->GetFields();

        $aMenuLinksExt[] = array(
            $arFields["NAME"],
            $arFields["DETAIL_PAGE_URL"],
            array(),
            array(
                "nTopCount" => 6
            ),
            ""
        );
    }
}

$aMenuLinks = array_merge($aMenuLinksExt, $aMenuLinks);
?>