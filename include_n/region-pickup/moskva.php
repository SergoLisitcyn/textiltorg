<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="addr_1_1" class="contacts addr_cont" style="margin-top: 10px;">
    <div>
        <b>ст. м. Марьина роща, ул. Сущёвский вал, д. 63</b> <i>(Ежедневно с 09:00 до 21:00)</i>
        <br><br>1 минута пешком от метро
        <span class="text-red text-small showContacts"><i>(подробная схема проезда)</i></span>
        <div class="cent blue contdown" id="Moscow_cart_v_2" style="display: none;">
            <?$APPLICATION->IncludeComponent(
                "bitrix:main.include",
                "",
                array(
                    "AREA_FILE_SHOW" => "file",
                    "PATH" => "/include/maps/moskva.php"
                )
            );?>
        </div>
    </div>
</div>