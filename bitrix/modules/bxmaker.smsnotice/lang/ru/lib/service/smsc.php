<?
$prefix = 'bxmaker.smsnotice.smsc.';

$MESS[$prefix .   'DESCRIPTION'] = '<p>(Самый быстрый, на наш взгляд). Сервис <a  target="_blank" href="https://bxmaker.ru/LdHGt">СМС Центр (откроется в новой вкладке)</a>, зарегистрируйтесь <a  target="_blank" href="https://bxmaker.ru/LJLRm">здесь (откроется в новой вкладке)</a>  и получить 15 рублей для тестирования<br/>
Добавить имя отправителя нужное вам, можно <a target="_blank" href="https://bxmaker.ru/aTDRK">здесь (откроется в новой вкладке)</a> либо оставьте поле пустым, будет использовано имя поумолчанию - имя сервиса.
<br>
После этого введите свои логин и пароль от личного кабинета, указанные при регистрации.</p>';

$MESS[$prefix . 'NOT_BALANCE'] = 'Данные не предоставляются';

$MESS[$prefix . 'PARAMS.USER'] = 'Логин';
$MESS[$prefix . 'PARAMS.USER.HINT'] = '';

$MESS[$prefix . 'PARAMS.PWD'] = 'Пароль';
$MESS[$prefix . 'PARAMS.PWD.HINT'] = '';

$MESS[$prefix . 'PARAMS.SENDER'] = 'Имя отправителя';
$MESS[$prefix . 'PARAMS.SENDER.HINT'] = '';

$MESS[$prefix . 'PARAMS.TEST_MODE'] = 'Тестовый режим';
$MESS[$prefix . 'PARAMS.TEST_MODE.HINT'] = '';

$MESS[$prefix . 'SERVICE.CURRENCY'] = ' руб.';

//статусы сообщений
$MESS[$prefix . 'MSG_STATUS_M3'] = '-3';
$MESS[$prefix . 'MSG_STATUS_M1'] = '-1';
$MESS[$prefix . 'MSG_STATUS_0'] = '0';
$MESS[$prefix . 'MSG_STATUS_1'] = '1';
$MESS[$prefix . 'MSG_STATUS_3'] = '3';
$MESS[$prefix . 'MSG_STATUS_20'] = '20';
$MESS[$prefix . 'MSG_STATUS_22'] = '22';
$MESS[$prefix . 'MSG_STATUS_23'] = '23';
$MESS[$prefix . 'MSG_STATUS_24'] = '24';
$MESS[$prefix . 'MSG_STATUS_25'] = '25';

$MESS[$prefix . 'MSG_STATUS_M3_VALUE'] = 'Сообщение не найдено';
$MESS[$prefix . 'MSG_STATUS_M1_VALUE'] = 'Ожидает отправки';
$MESS[$prefix . 'MSG_STATUS_0_VALUE'] = 'Передано оператору';
$MESS[$prefix . 'MSG_STATUS_1_VALUE'] = 'Доставлено';
$MESS[$prefix . 'MSG_STATUS_3_VALUE'] = 'Просрочено';
$MESS[$prefix . 'MSG_STATUS_20_VALUE'] = 'Невозможно доставить';
$MESS[$prefix . 'MSG_STATUS_22_VALUE'] = 'Неверный номер';
$MESS[$prefix . 'MSG_STATUS_23_VALUE'] = 'Запрещено';
$MESS[$prefix . 'MSG_STATUS_24_VALUE'] = 'Недостаточно средств';
$MESS[$prefix . 'MSG_STATUS_25_VALUE'] = 'Недоступный номер';



// ошибки различные
$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_1'] = '1';
$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_2'] = '2';
$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_4'] = '4';
$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_9'] = '9';

$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_1_VALUE'] = 'Ошибка в параметрах';
$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_2_VALUE'] = 'Неверный логин или пароль';
$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_4_VALUE'] = 'IP-адрес временно заблокирован';
$MESS[$prefix . 'ERROR_DESCRIPTION_BALANCE_9_VALUE'] = 'Попытка отправки более десяти запросов на получение баланса в течение минуты';


$MESS[$prefix . 'ERROR_DESCRIPTION_STATUS_1'] = '1';
$MESS[$prefix . 'ERROR_DESCRIPTION_STATUS_2'] = '2';
$MESS[$prefix . 'ERROR_DESCRIPTION_STATUS_4'] = '4';
$MESS[$prefix . 'ERROR_DESCRIPTION_STATUS_5'] = '5';
$MESS[$prefix . 'ERROR_DESCRIPTION_STATUS_9'] = '9';

$MESS[$prefix . 'ERROR_DESCRIPTION_STATUS_1_VALUE'] = 'Ошибка в параметрах';
$MESS[$prefix . 'ERROR_DESCRIPTION_STATUS_2_VALUE'] = 'Неверный логин или пароль';
$MESS[$prefix . 'ERROR_DESCRIPTION_STATUS_4_VALUE'] = 'IP-адрес временно заблокирован';
$MESS[$prefix . 'ERROR_DESCRIPTION_STATUS_5_VALUE'] = 'Ошибка удаления сообщения';
$MESS[$prefix . 'ERROR_DESCRIPTION_STATUS_9_VALUE'] = 'Попытка отправки более пяти запросов на получение статуса одного и того же сообщения в течение минуты';



$MESS[$prefix . 'ERROR_DESCRIPTION_1'] = '1';
$MESS[$prefix . 'ERROR_DESCRIPTION_2'] = '2';
$MESS[$prefix . 'ERROR_DESCRIPTION_3'] = '3';
$MESS[$prefix . 'ERROR_DESCRIPTION_4'] = '4';
$MESS[$prefix . 'ERROR_DESCRIPTION_5'] = '5';
$MESS[$prefix . 'ERROR_DESCRIPTION_6'] = '6';
$MESS[$prefix . 'ERROR_DESCRIPTION_7'] = '7';
$MESS[$prefix . 'ERROR_DESCRIPTION_8'] = '8';
$MESS[$prefix . 'ERROR_DESCRIPTION_9'] = '9';

$MESS[$prefix . 'ERROR_DESCRIPTION_1_VALUE'] = 'Ошибка в параметрах';
$MESS[$prefix . 'ERROR_DESCRIPTION_2_VALUE'] = 'Неверный логин или пароль';
$MESS[$prefix . 'ERROR_DESCRIPTION_3_VALUE'] = 'Недостаточно средств на счете Клиента';
$MESS[$prefix . 'ERROR_DESCRIPTION_4_VALUE'] = 'IP-адрес временно заблокирован из-за частых ошибок в запросах';
$MESS[$prefix . 'ERROR_DESCRIPTION_5_VALUE'] = 'Неверный формат даты';
$MESS[$prefix . 'ERROR_DESCRIPTION_6_VALUE'] = 'Сообщение запрещено (по тексту или по имени отправителя)';
$MESS[$prefix . 'ERROR_DESCRIPTION_7_VALUE'] = 'Неверный формат номера телефона';
$MESS[$prefix . 'ERROR_DESCRIPTION_8_VALUE'] = 'Сообщение на указанный номер не может быть доставлено';
$MESS[$prefix . 'ERROR_DESCRIPTION_9_VALUE'] = 'Отправка более одного одинакового запроса на передачу SMS-сообщения либо более пяти одинаковых запросов на получение стоимости сообщения в течение минуты';




