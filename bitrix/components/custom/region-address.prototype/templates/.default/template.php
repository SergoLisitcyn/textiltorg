<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="box_block">
    <?if (!$arResult["FILE_EXISTS"]):?>
        <?$APPLICATION->IncludeComponent(
            "bitrix:main.include",
            "",
            array(
                "AREA_FILE_SHOW" => "file",
                "PATH" => $arResult["FILES_PATH"].$arResult["DEFAULT_FILE"].".php",
                "EDIT_TEMPLATE" => "text.php"
            )
        );?>
    <?endif?>

    <?$APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        array(
            "AREA_FILE_SHOW" => "file",
            "PATH" => $arResult["FILES_PATH"].$arResult["FILE_NAME"].".php",
            "EDIT_TEMPLATE" => "text.php"
        )
    );?>
</div>