<?$APPLICATION->IncludeComponent(
	"custom:form.prototype",
	"footer-callback",
	array(
		"FORM_ID" => 1,
		"FORM_ACTION" => "FORM_CALLBACK",
		"SUCCESS_MESSAGE" => "Спасибо, что выбрали нас! Мы перезвоним Вам в ближайшее время!",
		"FIELDS" => array(
			"form_text_1",
			"form_text_2"
		)
	),
	false,
	array(
		"HIDE_ICONS" => "Y",
	)
);?>