<?
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(Helper::RemoveTwoSlash($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/iblock/classes/general/prop_html.php'));
require Helper::RemoveTwoSlash($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/usertypeelement.php');

AddEventHandler("main", "OnUserTypeBuildList", array("UserDataList", "GetUserTypeDescription"));

class UserDataList extends CUserTypeIBlockElement
{
    function GetUserTypeDescription()
    {
        return array(
            "USER_TYPE_ID" => "UserDataList",
            "CLASS_NAME" => "UserDataList",
            "DESCRIPTION" => "Список дополнительных товаров",
            "BASE_TYPE" => "int",
        );
    }

    function GetEditFormHTMLMulty($arUserField, $arHtmlControl)
    {
        global $APPLICATION;

        $APPLICATION->AddHeadScript("https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js");
        $APPLICATION->SetAdditionalCSS("/bitrix/css/multiselect/style.css");
        $APPLICATION->AddHeadScript("/bitrix/js/multiselect/multiselect.js");

        if (($arUserField["ENTITY_VALUE_ID"] < 1) && strlen($arUserField["SETTINGS"]["DEFAULT_VALUE"]) > 0)
            $arHtmlControl["VALUE"] = array(intval($arUserField["SETTINGS"]["DEFAULT_VALUE"]));
        elseif (!is_array($arHtmlControl["VALUE"]))
            $arHtmlControl["VALUE"] = array();

        $rsEnum = call_user_func_array(
            array($arUserField["USER_TYPE"]["CLASS_NAME"], "getlist"),
            array(
                $arUserField,
            )
        );
        if(!$rsEnum)
            return '';

        $optionsTo = '';
        $optionsFrom = '';
        $arOptionsSelect = array();
        $controlName = $arHtmlControl["NAME"];

        while($arEnum = $rsEnum->GetNext())
        {
            $bSelected = (
                (in_array($arEnum["ID"], $arHtmlControl["VALUE"])) ||
                ($arUserField["ENTITY_VALUE_ID"] <= 0 && $arEnum["DEF"] == "Y")
            );

            if (!$bSelected)
            {
                $optionsFrom .= '<option value="'.$arEnum["ID"].'">'.htmlspecialchars($arEnum["VALUE"]).'</option>';
            }
            else
            {
                $arOptionsSelect[$arEnum["ID"]] = $arEnum;
            }
        }

        foreach ($arUserField["VALUE"] as $id)
        {
            $arEnum = $arOptionsSelect[$id];
            $optionsTo .= '<option value="'.$arEnum["ID"].'">'.htmlspecialchars($arEnum["VALUE"]).'</option>';
        }

        $result = <<<SSS
            <div class="row">
                <div class="col-sm-5">
                    <select name="from[]" id="multiselect" class="form-control" size="8" multiple="multiple">
                        {$optionsFrom}
                    </select>
                </div>

                <div class="col-sm-2">
                    <button type="button" id="multiselect_rightAll" class="adm-btn">»</button>
                    <button type="button" id="multiselect_rightSelected" class="adm-btn">›</button>
                    <button type="button" id="multiselect_leftSelected" class="adm-btn">‹</button>
                    <button type="button" id="multiselect_leftAll" class="adm-btn">«</button>
                </div>

                <div class="col-sm-5">
                    <select name="{$controlName}" id="multiselect_to" class="form-control" size="8" multiple="multiple">
                        {$optionsTo}
                    </select>

                    <div class="row">
                        <div class="col-sm-6">
                            <button type="button" id="multiselect_move_up" class="adm-btn">Поднять</button>
                        </div>
                        <div class="col-sm-6">
                            <button type="button" id="multiselect_move_down" class="adm-btn">Опустить</button>
                        </div>
                    </div>
                </div>
            </div>

            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('#multiselect').multiselect({
                        search: {
                            left: '<input type="text" name="q" class="form-control" placeholder="Поиск..." />'
                        },
                        fireSearch: function(value) {
                            return value.length > 3;
                        },
                        keepRenderingSort: true
                    });
                });
            </script>
SSS;
        return $result;
    }
}