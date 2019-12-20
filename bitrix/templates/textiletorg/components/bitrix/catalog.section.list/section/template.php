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
?>

<?if ($arResult["SECTIONS"] && !$arResult["IS_HIDE_SECTION"]):?>
    <div class="cat_menu2 <?if($arResult["IS_PICTURES"]):?>is-pictures<?endif?>">
        <?
        foreach($arResult["SECTIONS"] as $arSection):
            $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
            $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
        ?>
            <div class="tile_container" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
                <a href="<?=$arSection["SECTION_PAGE_URL"]?>">

                    <div class="boxsize_100 align_center">
                        <div class="boxsize_100 align_middle">
                            <?if ($arSection["PICTURE"]["SRC"]):?>
                                <img src="<?=$arSection["PICTURE"]["SRC"]?>" title="<?=$arSection["NAME"]?>" alt="<?=$arSection["NAME"]?>">
                            <?endif?>
                        </div>
                    </div>

                    <span><?=$arSection["NAME"]?></span>
                </a>
            </div>
        <?endforeach?>
    </div>
<?endif?>