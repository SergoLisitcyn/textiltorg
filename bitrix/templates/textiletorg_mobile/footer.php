
			<?$APPLICATION->IncludeComponent(
                "bitrix:main.include",
                "",
                array(
                    "AREA_FILE_SHOW" => "page",
                    "AREA_FILE_SUFFIX" => "bottom-mobile",
                    "COMPONENT_TEMPLATE" => ".default",
                    "EDIT_TEMPLATE" => ""
                )
            );?>
		</div>

		<div id="footer">
			<?if (!Helper::IsRealFilePath(array('/catalog/index.php', '/catalog/detail/index.php')) && $APPLICATION->GetCurPage(false) != "/search/"){?>
				<div class="text_block">
					<div class="content">
						<h2>Есть вопросы? звоните!</h2>


						<?$APPLICATION->IncludeComponent(
							"custom:region-select.prototype",
							"mobile-footer",
							array(),
							false,
							array(
								"HIDE_ICONS" => "Y"
							)
						);?>
						<div class="phone">
							<?$APPLICATION->IncludeComponent(
								"custom:region-phone.prototype",
								"signature",
								array(
									"FILES_PATH" => "/include/region-phone/",
									"DEFAULT_FILE" => "default"
								),
								false,
								array(
									"HIDE_ICONS" => "Y"
								)
							);?>
						</div>


						<?/*$APPLICATION->IncludeComponent(
							"bitrix:main.include",
							"",
							Array(
								"AREA_FILE_SHOW" => "file",
								"AREA_FILE_SUFFIX" => "inc",
								"EDIT_TEMPLATE" => "",
								"PATH" => "/include/mobile/footer-contacts.php"
							)
						);*/?>
					</div>
				</div>

				<div class="button_block">
					<a href="#form_callback" class="button yellow fancybox" title="Мы перезвоним Вам через 5 минут или раньше!" onclick="<?=Helper::GetYandexCounter("callMe_Send")?>" style="font-size:13.5px;">Заказать звонок</a>
				</div>
			<?} else {?>
			<?}?>

			<?$APPLICATION->IncludeComponent(
				"bitrix:main.include",
				"",
				Array(
					"AREA_FILE_SHOW" => "file",
					"AREA_FILE_SUFFIX" => "inc",
					"EDIT_TEMPLATE" => "",
					"PATH" => "/include/mobile/social-buttons.php",
				)
			);?>
			<div class="version_block"><a href="<?=$APPLICATION->GetCurPageParam("show_version=desctop", array("SECTION_CODE_PATH", "ELEMENT_CODE"));?>">Полная версия сайта</a></div>
			<?$APPLICATION->IncludeComponent(
				"bitrix:menu",
				"mobile-bottom",
				array(
					"ALLOW_MULTI_SELECT" => "N",
					"CHILD_MENU_TYPE" => "",
					"DELAY" => "N",
					"MAX_LEVEL" => "1",
					"MENU_CACHE_GET_VARS" => array(
						0 => "",
					),
					"MENU_CACHE_TIME" => "3600",
					"MENU_CACHE_TYPE" => "N",
					"MENU_CACHE_USE_GROUPS" => "Y",
					"ROOT_MENU_TYPE" => "bottom",
					"USE_EXT" => "N",
				),
				false
			);?>
		</div>
	</div>
</div>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "/include/footer-scripts.php",
			"EDIT_TEMPLATE" => "text.php"
		),
		false,
		array(
			"HIDE_ICONS" => "Y"
		)
);?>

<?$APPLICATION->IncludeComponent(
    "custom:form.prototype",
    "callback-mobile",
    array(
        "FORM_ID" => 1,
        "FORM_ACTION" => "FORM_CALLBACK",
        "SUCCESS_MESSAGE" => "Спасибо, что выбрали нас! Мы перезвоним Вам через 5 минут, или раньше!",
        "FIELDS" => array(
            "form_text_1",
            "form_text_2",
            "IS_MOBILE",
        ),
        "ORDER" => array(
            'form_text_1' => 'NAME',
            'form_text_2' => 'PHONE',
            'IS_MOBILE'   => 'IS_MOBILE',
        )
    ),
    false,
    array(
        "HIDE_ICONS" => "Y",
    )
);?>
		<?$APPLICATION->IncludeComponent(
			"custom:gdeslon.prototype",
			"",
			array(
				"MID" => "88568",
				"PAGE_CATEGORY" => "/catalog/index.php",
				"PAGE_PRODUCT" => "/catalog/detail/index.php",
				"PAGE_CART" => "/^\/cart/",
				"PAGE_PURCHASE" => "/^\/order/",
				"PAGE_SEARCH" => "/^\/search/",
				"CODES" => "",
				"CAT_ID" => ""
			),
			false,
			array(
				"HIDE_ICONS" => "Y"
			)
		);?>
	</body>
</html>