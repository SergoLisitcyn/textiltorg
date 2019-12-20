<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);

if (isset($_REQUEST["AJAX_PAGEN"]) && $_REQUEST["AJAX_PAGEN"] == "Y")
{
    $APPLICATION->RestartBuffer();
    header('Content-Type: text/html; charset='.LANG_CHARSET);
}
else {
    $APPLICATION->SetAdditionalCSS("/bitrix/components/khayr/main.comment/libs/rateit.js/1.0.23/rateit.css");
    $APPLICATION->AddHeadScript("/bitrix/components/khayr/main.comment/libs/rateit.js/1.0.23/jquery.rateit.js");
}

#\Bitrix\Main\Diag\Debug::dumpToFile($arResult,"","otz.log"); 

function KHAYR_MAIN_COMMENT_ShowTree($arItem, $arParams, $arResult, $child)
{
	?>
	<div class="stock<?if(!$child):?> item<?endif;?>">
		<?if ($arItem["AUTHOR"]["AVATAR"]) {?>
			<div class="userImg">
				<?$picture = CFile::ResizeImageGet($arItem["AUTHOR"]["AVATAR"]["ID"], array("width" => 155, "height" => 900), BX_RESIZE_IMAGE_PROPORTIONAL);?>
				<a class="fancybox" href="<?=$arItem["AUTHOR"]["AVATAR"]["SRC"]?>"><img src="<?=$picture["src"];?>" alt=""></a>
			</div>
		<?}?>
		<div class="userInfo">
			<div class="userName">
				<?=$arItem["NAME"]?>
				<?if($arItem["PROPERTIES"]["RATING"]):?>
					<? $rating = ($arItem["PROPERTIES"]["RATING"]["VALUE"] && intval($arItem["PROPERTIES"]["RATING"]["VALUE"]) > 0) ? intval($arItem["PROPERTIES"]["RATING"]["VALUE"]) : 0; ?>
					<div class="rating rating-<?=$rating;?>"></div>
				<?endif;?>
			</div>
			<div class="userDate">
				<p><?=$arItem["PUBLISH_DATE"]?></p>
			</div>
			<div class="userText">
				<div class="collapse_text_wrapper">
					<div class="collapse_text">
						<?=$arItem["PUBLISH_TEXT"]?>
					</div>
				</div>
				<a href="#" class="collapse_link show_comment" style="display: none;">Подробнее</a>
				<a href="#" class="collapse_link hide_comment" style="display: none;">Скрыть</a>
				<div class='action'>
					<?
					/*global $USER;
					if ($arItem["CAN_COMMENT"]) {?>
						<a href="javascript:void();" onclick='KHAYR_MAIN_COMMENT_add(this, <?=$arItem["ID"]?>); return false;' title='<?=GetMessage("KHAYR_MAIN_COMMENT_COMMENT")?>'><?=GetMessage("KHAYR_MAIN_COMMENT_COMMENT")?></a>
					<?}?>
					<?if ($arItem["CAN_MODIFY"]) {?>
						<?if ($arItem["CAN_COMMENT"]) {?> | <?}?>
						<a href="javascript:void();" onclick='KHAYR_MAIN_COMMENT_edit(this, <?=$arItem["ID"]?>); return false;' title="<?=GetMessage("KHAYR_MAIN_COMMENT_EDIT")?>"><?=GetMessage("KHAYR_MAIN_COMMENT_EDIT")?></a>
					<?}?>
					<?if ($arItem["CAN_DELETE"]) {?>
						<?if ($arItem["CAN_COMMENT"] || $arItem["CAN_MODIFY"]) {?> | <?}?>
						<a href='javascript:void(0)' onclick='KHAYR_MAIN_COMMENT_delete(this, <?=$arItem["ID"]?>, "<?=GetMessage("KHAYR_MAIN_COMMENT_DEL_MESS")?>"); return false;' title='<?=GetMessage("KHAYR_MAIN_COMMENT_DELETE")?>'><?=GetMessage("KHAYR_MAIN_COMMENT_DELETE")?></a>
					<?}?>
					<?if ($arItem["SHOW_RATING"]) {?>
						<?if ($arItem["CAN_COMMENT"] || $arItem["CAN_MODIFY"] || $arItem["CAN_DELETE"]) {?> | <?}?>
						<?
						$arRatingParams = Array(
							"ENTITY_TYPE_ID" => "IBLOCK_ELEMENT",
							"ENTITY_ID" => $arItem["ID"],
							"OWNER_ID" => $arItem["PROPERTIES"]["USER"]["VALUE"],
							"PATH_TO_USER_PROFILE" => ""
						);
						if (!isset($arItem['RATING']))
							$arItem['RATING'] = Array(
								"USER_HAS_VOTED" => 'N',
								"TOTAL_VOTES" => 0,
								"TOTAL_POSITIVE_VOTES" => 0,
								"TOTAL_NEGATIVE_VOTES" => 0,
								"TOTAL_VALUE" => 0
							);
						$arRatingParams = array_merge($arRatingParams, $arItem['RATING']);
						$GLOBALS["APPLICATION"]->IncludeComponent(
							"bitrix:rating.vote",
							"standart",
							$arRatingParams,
							$component,
							Array("HIDE_ICONS" => "Y")
						);
						?>
					<?}*/?>
					<?if ($arItem["CAN_MODIFY"]) {?>
						<div class="form comment form_for" id='edit_form_<?=$arItem["ID"]?>'<?=($arResult["POST"]["COM_ID"] == $arItem["ID"] && !$arResult["SUCCESS"] ? " style='display: block;'" : "")?>>
							<form enctype="multipart/form-data" action="<?=$GLOBALS["APPLICATION"]->GetCurUri()?>" method='POST' onsubmit='return KHAYR_MAIN_COMMENT_validate(this);'>
								<textarea name="MESSAGE" rows="10" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_MESSAGE")?>'><?=$arItem["~PREVIEW_TEXT"]?></textarea>
								<input type='hidden' name='ACTION' value='update' />
								<input type='hidden' name='COM_ID' value='<?=$arItem["ID"]?>' />
								<input type="submit" class="red_button" value="<?=GetMessage("KHAYR_MAIN_COMMENT_SAVE")?>" />
								<div style="text-align: right;position: relative;top: -23px;">
									<a href="javascript:void(0)" onclick='KHAYR_MAIN_COMMENT_back(); return false;' style='margin-top: -25px; text-decoration: none;'><?=GetMessage("KHAYR_MAIN_COMMENT_BACK_BUTTON")?></a>
								</div>
							</form>
						</div>
					<?}?>
					<?if ($arItem["CAN_COMMENT"]) {?>
						<div class="form comment form_for" id='add_form_<?=$arItem["ID"]?>'<?=($arResult["POST"]["PARENT"] == $arItem["ID"] && !$arResult["SUCCESS"] ? " style='display: block;'" : "")?>>
							<form enctype="multipart/form-data" action="<?=$GLOBALS["APPLICATION"]->GetCurUri()?>" method='POST' onsubmit='return KHAYR_MAIN_COMMENT_validate(this);'>
								<input type="text" name='NONUSER' value='<?=$arResult["POST"]["NONUSER"]?>' <?=($arResult["USER"]["ID"] ? "readonly='readonly'" : "")?> placeholder="<?=GetMessage("KHAYR_MAIN_COMMENT_FNAME")?>" class="w-50" />
								<?if ($arResult["LOAD_AVATAR"]) {?>
									<input type="file" name='AVATAR' value='' placeholder="<?=GetMessage("KHAYR_MAIN_COMMENT_AVATAR")?>" class="w-50" />
								<?}?>
								<?if ($arResult["LOAD_EMAIL"]) {?>
									<input type="text" name='EMAIL' <?=($arResult["USER"]["ID"] ? "value='".$arResult["USER"]["EMAIL"]."' readonly='readonly'" : "value='".$arResult["POST"]["EMAIL"]."'")?> placeholder="<?=GetMessage("KHAYR_MAIN_COMMENT_EMAIL")?>" class="w-50" />
								<?}?>
								<?foreach ($arParams["ADDITIONAL"] as $additional) {?>
									<input type="text" name='<?=urlencode($additional)?>' value='<?=$arResult["POST"][$additional]?>' placeholder="<?=$additional?>" class="w-50" />
								<?}?>
								<div class="clear pt10"></div>
								<?if ($arParams["LOAD_MARK"]) {?>
									<?=GetMessage("KHAYR_MAIN_COMMENT_MARK")?>:
									<div class="rateit" data-rateit-backingfld="#rate_<?=$arItem["ID"]?>" data-rateit-resetable="false" data-rateit-min="0" data-rateit-max="5"></div>
									<input type="hidden" name="MARK" value="0" id="rate_<?=$arItem["ID"]?>" />
									<div class="rates" id="rate_<?=$arItem["ID"]?>_control"></div>
									<script>
										$(function() {
											$('#rate_<?=$arItem["ID"]?>_control').rateit({ min: 0, max: 5, step: 1, backingfld: '#rate_<?=$arItem["ID"]?>', resetable: false });
										});
									</script>
									<div class="clear pt10"></div>
								<?}?>
								<?if ($arParams["LOAD_DIGNITY"]) {?>
									<textarea name="DIGNITY" rows="3" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_DIGNITY")?>'><?=$arResult["POST"]["DIGNITY"]?></textarea>
								<?}?>
								<?if ($arParams["LOAD_FAULT"]) {?>
									<textarea name="FAULT" rows="3" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_FAULT")?>'><?=$arResult["POST"]["FAULT"]?></textarea>
								<?}?>
								<textarea name="MESSAGE" rows="10" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_MESSAGE")?>'></textarea>
								<input type='hidden' name='PARENT' value='<?=$arItem["ID"]?>' />
								<input type='hidden' name='ACTION' value='add' />
								<input type='hidden' name='DEPTH' value='<?=($arItem["PROPERTIES"]["DEPTH"]["VALUE"]+1)?>' />
								<?if ($arParams["USE_CAPTCHA"]) {?>
									<div>
										<div><?=GetMessage("KHAYR_MAIN_COMMENT_CAP_1")?></div>
										<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>" />
										<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA" />
										<div><?=GetMessage("KHAYR_MAIN_COMMENT_CAP_2")?></div>
										<input type="text" name="captcha_word" size="30" maxlength="50" value="" />
										<input type='hidden' name='clear_cache' value='Y' />
									</div>
								<?}?>
								<?if ($arParams["LEGAL"]) {?>
									<input type='checkbox' id="LEGAL_<?=$arItem["ID"]?>_form" name='LEGAL' value='Y' <?=($arResult["POST"]["LEGAL"] == "Y" ? "checked" : "")?> />
									<label for="LEGAL_<?=$arItem["ID"]?>_form"><?=$arParams["LEGAL_TEXT"]?></label>
									<div class="clear pt10"></div>
								<?}?>

								<input type="submit" class="red_button" value="Отправить" />
								<div style="text-align: right;position: relative;top: -23px;">
									<a href="javascript:void(0)" onclick='KHAYR_MAIN_COMMENT_back(); return false;' style='margin-top: -25px; text-decoration: none;'><?=GetMessage("KHAYR_MAIN_COMMENT_BACK_BUTTON")?></a>
								</div>
							</form>
						</div>
					<?}?>
				</div>
			</div>
			<?if (!empty($arItem["CHILDS"])) {?>
				<?
				$child = true;
				foreach ($arItem["CHILDS"] as $item) {?>
					<?=KHAYR_MAIN_COMMENT_ShowTree($item, $arParams, $arResult, $child)?>
				<?}?>
			<?}?>
		</div>
	</div>
<?}?>
<?if (!isset($_REQUEST["AJAX_PAGEN"]) || $_REQUEST["AJAX_PAGEN"] != "Y"){?>
<div class='khayr_main_comment' id='KHAYR_MAIN_COMMENT_container'>
	<?}?>
	<?if (strlen($_POST["ACTION"]) > 0) $GLOBALS["APPLICATION"]->RestartBuffer();?>
	<?if (!isset($_REQUEST["AJAX_PAGEN"]) || $_REQUEST["AJAX_PAGEN"] != "Y"){?>
		<p style='color: green; display: none;' class='suc'><?=$arResult["SUCCESS"]?></p>
		<p style='color: red; display: none;' class='err'><?=$arResult["ERROR_MESSAGE"]?></p>

		<div class="comments"><div class="leavefeedback"><a href="#" onClick="$('.khayr_main_comment .form.comment.main_form').show(); $(this).parent().hide(); return false;">Оставить отзыв</a></div></div>

		<div class="form comment main_form"<?=($arResult["POST"]["PARENT"] > 0 && !$arResult["SUCCESS"] ? " style='display: none;' " : "")?> style="display: none;">
			<?if ($arResult["CAN_COMMENT"]) {?>
				<form enctype="multipart/form-data" action="<?=$GLOBALS["APPLICATION"]->GetCurUri()?>" method='POST' onsubmit='return KHAYR_MAIN_COMMENT_validate(this);'>
					<input type="text" name='NONUSER' value='<?=$arResult["POST"]["NONUSER"]?>' <?=($arResult["USER"]["ID"] ? "readonly='readonly'" : "")?> placeholder="<?=GetMessage("KHAYR_MAIN_COMMENT_FNAME")?>" class="w-50" />
					<?if ($arResult["LOAD_AVATAR"]) {?>
						<input type="file" name='AVATAR' value='' placeholder="<?=GetMessage("KHAYR_MAIN_COMMENT_AVATAR")?>" class="w-50" />
					<?}?>
					<?if ($arResult["LOAD_EMAIL"]) {?>
						<input type="text" name='EMAIL' <?=($arResult["USER"]["ID"] ? "value='".$arResult["USER"]["EMAIL"]."' readonly='readonly'" : "value='".$arResult["POST"]["EMAIL"]."'")?> placeholder="<?=GetMessage("KHAYR_MAIN_COMMENT_EMAIL")?>" class="w-50" />
					<?}?>
					<?foreach ($arParams["ADDITIONAL"] as $additional) {?>
						<input type="text" name='<?=urlencode($additional)?>' value='<?=$arResult["POST"][$additional]?>' placeholder="<?=$additional?>" class="w-50" />
					<?}?>
					<div class="clear pt10"></div>
					<?if ($arParams["LOAD_MARK"]) {?>
						<?=GetMessage("KHAYR_MAIN_COMMENT_MARK")?>:
						<input type="hidden" name="MARK" value="0" id="rate_0" />
						<div class="rates" id="rate_0_control"></div>
						<script>
							$(function() {
								$('#rate_0_control').rateit({ min: 0, max: 5, step: 1, backingfld: '#rate_0', resetable: false });
							});
						</script>
						<div class="clear pt10"></div>
					<?}?>
					<?if ($arParams["LOAD_DIGNITY"]) {?>
						<textarea name="DIGNITY" rows="3" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_DIGNITY")?>'><?=$arResult["POST"]["DIGNITY"]?></textarea>
					<?}?>
					<?if ($arParams["LOAD_FAULT"]) {?>
						<textarea name="FAULT" rows="3" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_FAULT")?>'><?=$arResult["POST"]["FAULT"]?></textarea>
					<?}?>
					<textarea name="MESSAGE" rows="10" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_MESSAGE")?>'><?=$arResult["POST"]["MESSAGE"]?></textarea>
					<input type='hidden' name='PARENT' value='' />
					<input type='hidden' name='ACTION' value='add' />
					<input type='hidden' name='DEPTH' value='1' />
					<?if ($arParams["USE_CAPTCHA"]) {?>
						<div>
							<div><?=GetMessage("KHAYR_MAIN_COMMENT_CAP_1")?></div>
							<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>" />
							<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA" />
							<div><?=GetMessage("KHAYR_MAIN_COMMENT_CAP_2")?></div>
							<input type="text" name="captcha_word" size="30" maxlength="50" value="" />
							<input type='hidden' name='clear_cache' value='Y' />
						</div>
					<?}?>
					<?if ($arParams["LEGAL"]) {?>
						<input type='checkbox' id="LEGAL_main_form" name='LEGAL' value='Y' <?=($arResult["POST"]["LEGAL"] == "Y" ? "checked" : "")?> />
						<label for="LEGAL_main_form"><?=$arParams["LEGAL_TEXT"]?></label>
						<div class="clear pt10"></div>
					<?}?>
					<input type="submit" class="red_button" value="Отправить" />
				</form>
			<?} else {?>
				<div style='background: #FFFFFF;'></div>
			<?}?>
		</div>
	<?}?>
	<?if ($arResult["ITEMS"]) {?>
		<?if ($arParams["DISPLAY_TOP_PAGER"]) {?>
			<?=$arResult["NAV_STRING"]?>
		<?}?>
		<div class="grid-list">
			<div class="comments itemlist">
				<?foreach ($arResult["ITEMS"] as $k => $arItem){
					echo KHAYR_MAIN_COMMENT_ShowTree($arItem, $arParams, $arResult, false);
				}?>
			</div>
		</div>
	<?}?>
	<?if ($arParams["DISPLAY_BOTTOM_PAGER"] && $arResult["ITEMS"]) {?>
		<?=$arResult["NAV_STRING"]?>
	<?}?>
	<?if (isset($_REQUEST["AJAX_PAGEN"]) && $_REQUEST["AJAX_PAGEN"] == "Y"){
		die();
	}?>

	<?if (strlen($_POST["ACTION"]) > 0) die();?>
<?if (!isset($_REQUEST["AJAX_PAGEN"]) || $_REQUEST["AJAX_PAGEN"] != "Y"){?>
</div>
<?}?>