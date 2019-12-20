<div class="clear"></div>
<div class="custom-form-prototype-footer-callback">
	<div class="callback-header-3">Остались вопросы?</div>
	<div class="callback-subheader-4">
		Позвоните нам по номеру <span>8 (800) 333-71-83</span>
	</div>
	<div class="callback-subheader-4">
		(звонок бесплатный , круглосуточно, без выходных)
	</div>
	<div class="we-will-call">Или мы перезвоним вам сами</div>
	<div class="small-paddings-offer-block">
		<?$APPLICATION->IncludeComponent(
			"custom:form.prototype",
			"footer-callback",
			array(
				"FORM_ID" => 1,
				"FORM_ACTION" => "FORM_CALLBACK",
				"SUCCESS_MESSAGE" => "Спасибо, что выбрали нас! Мы перезвоним Вам в ближайшее время!",
				"YANDEX_COUNER" => "ostalis_voprosy",
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
			$component,
			array(
				"HIDE_ICONS" => "Y",
			)
		);?>
	</div>
</div>
