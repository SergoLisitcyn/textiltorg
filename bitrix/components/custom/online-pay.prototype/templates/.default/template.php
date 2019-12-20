<div class="online-pay-sidebar">
    <a class="box-online-pay fancybox" href="#online-pay-form" title="Online-оплата"><div class="ic"></div>
        Online-оплата
    </a>
</div>

<div id="online-pay-form">
	<form method="POST" action="<?=$arResult['URL']?>" >
		<label for="sum">Введите сумму оплаты:</label>
		<input type="text" id="sum" name="sum" value="" data-validate="sum"/>
		<label for="client_phone">Введите номер телефона:</label>
		<input type="text" id="client_phone" name="client_phone" value="" data-validate="client_phone" placeholder="+79261234567" />
		<div style="text-align:center">
			<button type="submit" class="button">Перейти к оплате</button>
		</div>
	</form>
</div>