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
<div class="menu_home">
    <div class="overlay_main_center"></div>
    <div>
        <div class="text-center" id="catalog-section-slider">
            <?
            foreach($arResult["SECTIONS"] as $arSection):
                $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
                $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
            ?>
                <?if ($arSection["DEPTH_LEVEL"] == 1):?>
                    <?if ($arSection["IS_PARENT"]):?>
                        <div class="main_center_menu_plit_big" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
                            <div class="mcm_text"><?=$arSection["NAME"]?></div>
							<div class="sub-menu" id="menu_sub_<?=$arSubSection["ID"]?>">
								<ul>
									<?foreach($arResult["SECTIONS"] as $arSubSection):?>
										<?if ($arSubSection["IBLOCK_SECTION_ID"] == $arSection["ID"]):?>
											<li>
												<a href="<?=$arSubSection["CODE"]?>/">
													<div class="img"><img src="<?=$arSubSection["PICTURE"]["SRC"]?>"></div>
													<div class="text">
														<div class="cell"><?=$arSubSection["NAME"]?></div>
													</div>
												</a>
											</li>
										<?endif?>
									<?endforeach?>
								</ul>
							</div>
                        </div>
                    <?else:?>
                        <div class="main_center_menu_plit_big" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
                            <a class="abs_link" href="<?=$arSection["SECTION_PAGE_URL"]?>" title="<?=$arSection["NAME"]?>"></a>
                            <div class="mcm_text"><?=$arSection["NAME"]?></div>
                        </div>
                    <?endif?>
                <?endif?>
            <?endforeach?>
        </div>

        
    </div>
</div>

<?=$arResult["CENTER_MENU_DATA"]?>