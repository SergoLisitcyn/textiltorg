<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
//echo "<pre>";
//var_dump($arResult["SECTIONS"])
?>
<div class="menu_home">
    <div class="overlay_main_center"></div>
    <div style="position:relative; margin-left:-20px; margin-top: -16px; margin-bottom: -17px;">
        <div id="wm_main_center_menu">
            <div class="head"><span id="wm_main_center_menu_title"></span><a href="javascript://" class="popup__close" onclick="wm_close(); return false;">X</a></div>
            <div id="content_main_center_menu"></div>
        </div>

        <?foreach($arResult["SECTIONS"] as $arSection):?>

            <div class="main_center_menu_plit_big" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
                <a href="<?=$arSection["SECTION_PAGE_URL"]?>" class="abs_link"></a>
                <div class="mcm_img">
                    <img src="<?=$arSection["RESIZE_PICTURE"]["SRC"]?>" alt="<?=$arSection["NAME"]?>" width="<?=$arSection["RESIZE_PICTURE"]["WIDTH"]?>" height="<?=$arSection["RESIZE_PICTURE"]["HEIGHT"]?>">
                </div>
                <div class="mcm_text"><div class="cell"><?=$arSection["NAME"]?></div></div>
            </div>

        <?endforeach?>

    </div>
</div>