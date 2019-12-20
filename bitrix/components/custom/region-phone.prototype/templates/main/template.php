<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<span class="tt-icons phone-icon"></span>
<div class="geo-phone">
    <?if ($arResult["FILE_EXISTS"]){?>
        <?$APPLICATION->IncludeComponent(
            "bitrix:main.include",
            "",
            array(
                "AREA_FILE_SHOW" => "file",
                "PATH" => $arResult["FILES_PATH"].$arResult["FILE_NAME"].".php",
                "EDIT_TEMPLATE" => "text.php"
            )
        );?>
    <?}else{?>
        <?$APPLICATION->IncludeComponent(
            "bitrix:main.include",
            "",
            array(
                "AREA_FILE_SHOW" => "file",
                "PATH" => $arResult["FILES_PATH"].$arResult["DEFAULT_FILE"].".php",
                "EDIT_TEMPLATE" => "text.php"
            )
        );?>
    <?}?>
</div>
<a href="#form-callback" class="callme button fancybox" title="Мы перезвоним Вам через 5 минут или раньше!">
    (Заказ, консультация - круглосуточно)
</a>