<?
	$prefix = 'bxmaker.smsnotice.sms.';

	$MESS[$prefix . 'DESCRIPTION']
		= '<p>Сервис <a  target="_blank" href="https://bxmaker.ru/Pn6jX">SMS RU/(откроется в новой вкладке)</a>, зарегистрируйтесь <a  target="_blank" href="https://bxmaker.ru/S4mWp">здесь (откроется в новой вкладке)</a>. Сервис предоставляет 5 смс день на свой номер бесплтано для тестирования<br/>
Добавить имя отправителя нужное вам, можно <a target="_blank" href="https://bxmaker.ru/HENuA">здесь (откроется в новой вкладке)</a> либо оставьте соответствующее поле ниже пустым
<br>После этого введите свои логин и пароль от личного кабинета, указанные при регистрации.</p>';

	$MESS[$prefix . 'NOT_BALANCE'] = 'Данные не предоставляются';

	$MESS[$prefix . 'PARAMS.USER']      = 'Логин';
	$MESS[$prefix . 'PARAMS.USER.HINT'] = '';

	$MESS[$prefix . 'PARAMS.PWD']      = 'Пароль';
	$MESS[$prefix . 'PARAMS.PWD.HINT'] = '';

	$MESS[$prefix . 'PARAMS.SADR']      = 'Имя отправителя';
	$MESS[$prefix . 'PARAMS.SADR.HINT'] = '';

	$MESS[$prefix . 'PARAMS.TEST_MODE']      = 'Тестовый режим';
	$MESS[$prefix . 'PARAMS.TEST_MODE.HINT'] = '';

	$MESS[$prefix . 'SERVICE.CURRENCY'] = ' руб.';


	// ошибки различные
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_100']       = '100';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_100_VALUE'] = 'Запрос выполнен. На второй строчке вы найдете ваше текущее состояние баланса';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_200']       = '200';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_200_VALUE'] = 'Неправильный api_id';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_201']       = '201';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_201_VALUE'] = 'Не хватает средств на лицевом счету';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_202']       = '202';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_202_VALUE'] = 'Неправильно указан получатель';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_203']       = '203';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_203_VALUE'] = 'Нет текста сообщения';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_204']       = '204';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_204_VALUE'] = 'Имя отправителя не согласовано с администрацией';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_205']       = '205';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_205_VALUE'] = 'Сообщение слишком длинное (превышает 8 СМС)';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_206']       = '206';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_206_VALUE'] = 'Будет превышен или уже превышен дневной лимит на отправку сообщений';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_207']       = '207';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_207_VALUE'] = 'На этот номер (или один из номеров) нельзя отправлять сообщения, либо указано более 100 номеров в списке получателей';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_208']       = '208';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_208_VALUE'] = 'Параметр time указан неправильно';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_209']       = '209';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_209_VALUE'] = 'Вы добавили этот номер (или один из номеров) в стоп-лист';


	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_210']       = '210';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_210_VALUE'] = 'Используется GET, где необходимо использовать POST';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_211']       = '211';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_211_VALUE'] = 'Метод не найден';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_212']       = '212';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_212_VALUE'] = 'Текст сообщения необходимо передать в кодировке UTF-8 (вы передали в другой кодировке)';


	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_220']       = '220';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_220_VALUE'] = 'Сервис временно недоступен, попробуйте чуть позже';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_230']       = '230';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_230_VALUE'] = 'Превышен общий лимит количества сообщений на этот номер в день';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_231']       = '231';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_231_VALUE'] = 'Превышен лимит одинаковых сообщений на этот номер в минуту';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_232']       = '232';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_232_VALUE'] = 'Превышен лимит одинаковых сообщений на этот номер в день';


	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_300']       = '300';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_300_VALUE'] = 'Неправильный token (возможно истек срок действия, либо ваш IP изменился)';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_301']       = '301';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_301_VALUE'] = 'Неправильный пароль, либо пользователь не найден';

	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_302']       = '302';
	$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_302_VALUE'] = 'Пользователь авторизован, но аккаунт не подтвержден (пользователь не ввел код, присланный в регистрационной смс)';


	// Отправка смс
	$MESS[$prefix . 'MSG_SEND_100'] = '100';
	$MESS[$prefix . 'MSG_SEND_200'] = '200';
	$MESS[$prefix . 'MSG_SEND_201'] = '201';
	$MESS[$prefix . 'MSG_SEND_202'] = '202';
	$MESS[$prefix . 'MSG_SEND_203'] = '203';
	$MESS[$prefix . 'MSG_SEND_204'] = '204';
	$MESS[$prefix . 'MSG_SEND_205'] = '205';
	$MESS[$prefix . 'MSG_SEND_206'] = '206';
	$MESS[$prefix . 'MSG_SEND_207'] = '207';
	$MESS[$prefix . 'MSG_SEND_208'] = '208';
	$MESS[$prefix . 'MSG_SEND_209'] = '209';
	$MESS[$prefix . 'MSG_SEND_210'] = '210';
	$MESS[$prefix . 'MSG_SEND_211'] = '211';
	$MESS[$prefix . 'MSG_SEND_212'] = '212';
	$MESS[$prefix . 'MSG_SEND_220'] = '220';
	$MESS[$prefix . 'MSG_SEND_230'] = '230';
	$MESS[$prefix . 'MSG_SEND_300'] = '300';
	$MESS[$prefix . 'MSG_SEND_301'] = '301';
	$MESS[$prefix . 'MSG_SEND_302'] = '302';

	$MESS[$prefix . 'MSG_SEND_100_VALUE']= 'Сообщение принято к отправке.';
	$MESS[$prefix . 'MSG_SEND_200_VALUE'] = 'sms.ru: Неправильный api_id';
	$MESS[$prefix . 'MSG_SEND_201_VALUE'] = 'sms.ru: Не хватает средств на лицевом счету';
	$MESS[$prefix . 'MSG_SEND_202_VALUE'] = 'sms.ru: Неправильно указан получатель';
	$MESS[$prefix . 'MSG_SEND_203_VALUE'] = 'sms.ru: Нет текста сообщения';
	$MESS[$prefix . 'MSG_SEND_204_VALUE'] = 'sms.ru: Имя отправителя не согласовано с администрацией';
	$MESS[$prefix . 'MSG_SEND_205_VALUE'] = 'sms.ru: Сообщение слишком длинное (превышает 8 СМС)';
	$MESS[$prefix . 'MSG_SEND_206_VALUE'] = 'sms.ru: Будет превышен или уже превышен дневной лимит на отправку сообщений';
	$MESS[$prefix . 'MSG_SEND_207_VALUE'] = 'sms.ru: На этот номер (или один из номеров) нельзя отправлять сообщения, либо указано более 100 номеров в списке получателей';
	$MESS[$prefix . 'MSG_SEND_208_VALUE'] = 'sms.ru: Параметр time указан неправильно';
	$MESS[$prefix . 'MSG_SEND_209_VALUE'] = 'sms.ru: Вы добавили этот номер (или один из номеров) в стоп-лист';
	$MESS[$prefix . 'MSG_SEND_210_VALUE'] = 'sms.ru: Используется GET, где необходимо использовать POST';
	$MESS[$prefix . 'MSG_SEND_211_VALUE'] = 'sms.ru: Метод не найден';
	$MESS[$prefix . 'MSG_SEND_212_VALUE'] = 'sms.ru: Текст сообщения необходимо передать в кодировке UTF-8 (вы передали в другой кодировке)';
	$MESS[$prefix . 'MSG_SEND_220_VALUE'] = 'sms.ru: Сервис временно недоступен, попробуйте чуть позже';
	$MESS[$prefix . 'MSG_SEND_230_VALUE'] = 'sms.ru: Сообщение не принято к отправке, так как на один номер в день нельзя отправлять более 60 сообщений';
	$MESS[$prefix . 'MSG_SEND_300_VALUE'] = 'sms.ru: Неправильный token (возможно истек срок действия, либо ваш IP изменился)';
	$MESS[$prefix . 'MSG_SEND_301_VALUE'] = 'sms.ru: Неправильный пароль, либо пользователь не найден';
	$MESS[$prefix . 'MSG_SEND_302_VALUE'] = 'sms.ru: Пользователь авторизован, но аккаунт не подтвержден (пользователь не ввел код, присланный в регистрационной смс)';


	//статусы сообщений
	$MESS[$prefix . 'MSG_STATUS_M1']  = '-1';
	$MESS[$prefix . 'MSG_STATUS_100'] = '100';
	$MESS[$prefix . 'MSG_STATUS_101'] = '101';
	$MESS[$prefix . 'MSG_STATUS_102'] = '102';
	$MESS[$prefix . 'MSG_STATUS_103'] = '103';
	$MESS[$prefix . 'MSG_STATUS_104'] = '104';
	$MESS[$prefix . 'MSG_STATUS_105'] = '105';
	$MESS[$prefix . 'MSG_STATUS_106'] = '106';
	$MESS[$prefix . 'MSG_STATUS_107'] = '107';
	$MESS[$prefix . 'MSG_STATUS_108'] = '108';
	$MESS[$prefix . 'MSG_STATUS_200'] = '200';
	$MESS[$prefix . 'MSG_STATUS_210'] = '210';
	$MESS[$prefix . 'MSG_STATUS_211'] = '211';
	$MESS[$prefix . 'MSG_STATUS_220'] = '220';
	$MESS[$prefix . 'MSG_STATUS_300'] = '300';
	$MESS[$prefix . 'MSG_STATUS_301'] = '301';
	$MESS[$prefix . 'MSG_STATUS_302'] = '302';


	$MESS[$prefix . 'MSG_STATUS_M1_VALUE']  = 'Сообщение не найдено';
	$MESS[$prefix . 'MSG_STATUS_100_VALUE'] = 'Сообщение находится в нашей очереди';
	$MESS[$prefix . 'MSG_STATUS_101_VALUE'] = 'Сообщение передается оператору';
	$MESS[$prefix . 'MSG_STATUS_102_VALUE'] = 'Сообщение отправлено (в пути)';
	$MESS[$prefix . 'MSG_STATUS_103_VALUE'] = 'Сообщение доставлено';
	$MESS[$prefix . 'MSG_STATUS_104_VALUE'] = 'Не может быть доставлено: время жизни истекло';
	$MESS[$prefix . 'MSG_STATUS_105_VALUE'] = 'Не может быть доставлено: удалено оператором';
	$MESS[$prefix . 'MSG_STATUS_106_VALUE'] = 'Не может быть доставлено: сбой в телефоне';
	$MESS[$prefix . 'MSG_STATUS_107_VALUE'] = 'Не может быть доставлено: неизвестная причина';
	$MESS[$prefix . 'MSG_STATUS_108_VALUE'] = 'Не может быть доставлено: отклонено';
	$MESS[$prefix . 'MSG_STATUS_200_VALUE'] = 'Неправильный api_id';
	$MESS[$prefix . 'MSG_STATUS_201_VALUE'] = 'Не хватает средств на лицевом счету';
	$MESS[$prefix . 'MSG_STATUS_202_VALUE'] = 'Неправильно указан получатель';
	$MESS[$prefix . 'MSG_STATUS_203_VALUE'] = 'Нет текста сообщения';
	$MESS[$prefix . 'MSG_STATUS_204_VALUE'] = 'Имя отправителя не согласовано с администрацией';
	$MESS[$prefix . 'MSG_STATUS_205_VALUE'] = 'Сообщение слишком длинное (превышает 8 СМС)';
	$MESS[$prefix . 'MSG_STATUS_206_VALUE'] = 'Будет превышен или уже превышен дневной лимит на отправку сообщений';
	$MESS[$prefix . 'MSG_STATUS_207_VALUE'] = 'На этот номер (или один из номеров) нельзя отправлять сообщения, либо указано более 100 номеров в списке получателей';
	$MESS[$prefix . 'MSG_STATUS_208_VALUE'] = 'Параметр time указан неправильно';
	$MESS[$prefix . 'MSG_STATUS_209_VALUE'] = 'Вы добавили этот номер (или один из номеров) в стоп-лист';
	$MESS[$prefix . 'MSG_STATUS_210_VALUE'] = 'Используется GET, где необходимо использовать POST';
	$MESS[$prefix . 'MSG_STATUS_211_VALUE'] = 'Метод не найден';
	$MESS[$prefix . 'MSG_STATUS_212_VALUE'] = 'Текст сообщения необходимо передать в кодировке UTF-8 (вы передали в другой кодировке)';
	$MESS[$prefix . 'MSG_STATUS_220_VALUE'] = 'Сервис временно недоступен, попробуйте чуть позже';
	$MESS[$prefix . 'MSG_STATUS_230_VALUE'] = 'Превышен общий лимит количества сообщений на этот номер в день';
	$MESS[$prefix . 'MSG_STATUS_231_VALUE'] = 'Превышен лимит одинаковых сообщений на этот номер в минуту';
	$MESS[$prefix . 'MSG_STATUS_232_VALUE'] = 'Превышен лимит одинаковых сообщений на этот номер в день';
	$MESS[$prefix . 'MSG_STATUS_300_VALUE'] = 'Неправильный token (возможно истек срок действия, либо ваш IP изменился)';
	$MESS[$prefix . 'MSG_STATUS_301_VALUE'] = 'Неправильный пароль, либо пользователь не найден';
	$MESS[$prefix . 'MSG_STATUS_302_VALUE'] = 'Пользователь авторизован, но аккаунт не подтвержден (пользователь не ввел код, присланный в регистрационной смс)';


