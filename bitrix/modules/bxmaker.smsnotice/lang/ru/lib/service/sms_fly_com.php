<?
$prefix = 'bxmaker.smsnotice.sms_fly_com.';

$MESS[$prefix .   'DESCRIPTION'] = '<p>Сервис <a  target="_blank" href="http://sms-fly.com/">SMS-Fly.com(откроется в новой вкладке)</a>,
зарегистрируйтесь <a  target="_blank" href="http://sms-fly.com/Registration/">здесь (откроется в новой вкладке)</a>.<br/>
Добавить имя отправителя (альфаимя) нужное вам, можно и нужно <a target="_blank" href="http://sms-fly.com/Client/Alfanames/">здесь (откроется в новой вкладке)</a>.
<br>Логин и пароль здесь это логин и пароль используемых для входа на сайт.</p>';



$MESS[$prefix . 'PARAMS.ID'] = 'Логин';
$MESS[$prefix . 'PARAMS.ID.HINT'] = '';

$MESS[$prefix . 'PARAMS.KEY'] = 'Пароль';
$MESS[$prefix . 'PARAMS.KEY.HINT'] = '';

$MESS[$prefix . 'PARAMS.SADR'] = 'Имя отправителя (альфаимя)';
$MESS[$prefix . 'PARAMS.SADR.HINT'] = '';


$MESS[$prefix . 'SERVICE.CURRENCY'] = ' грн.';

$MESS[$prefix . 'SEND_MESSAGE_DESCRIPTION'] = 'СМС оповещение';

// Отправка смс
$MESS[$prefix . 'ERROR_BALANCE_EMPTY'] = 'Не получен ответ от сервиса';
$MESS[$prefix . 'ERROR_STATUS_EMPTY'] = 'Не получен ответ от сервиса';
$MESS[$prefix . 'ERROR_SEND_EMPTY'] = 'Не получен ответ от сервиса';



$MESS[$prefix . 'XMLERROR'] = 'Некорректный XML ';
$MESS[$prefix . 'ERRPHONES'] = 'Неверно задан номер получателя';
$MESS[$prefix . 'ERRSTARTTIME'] = 'не корректное время начала отправки';
$MESS[$prefix . 'ERRENDTIME'] = 'не корректное время окончания рассылки';
$MESS[$prefix . 'ERRLIFETIME'] = 'не корректное время жизни сообщения';
$MESS[$prefix . 'ERRSPEED'] = 'не корректная скорость отправки сообщений';
$MESS[$prefix . 'ERRALFANAME'] = 'данное альфанумерическое имя использовать запрещено, либо ошибк';
$MESS[$prefix . 'ERRTEXT'] = 'некорректный текст сообщения';
$MESS[$prefix . 'INSUFFICIENTFUNDS'] = 'недостаточно средств. Проверяется только при получении запроса на отправку СМС сообщения одному абоненту';





$MESS[$prefix . 'MSG_STATUS_EXPIRED'] = 'истек срок доставки';
$MESS[$prefix . 'MSG_STATUS_UNDELIV'] = 'не доставлено';
$MESS[$prefix . 'MSG_STATUS_STOPED'] = 'остановлено системой (недостаточно средств)';
$MESS[$prefix . 'MSG_STATUS_ERROR'] = 'ошибка при отправке';
$MESS[$prefix . 'MSG_STATUS_USERSTOPED'] = 'остановлено пользователем';
$MESS[$prefix . 'MSG_STATUS_ALFANAMELIMITED'] = 'ограничено альфаименем';
$MESS[$prefix . 'MSG_STATUS_UNKNOWN_VALUE'] = 'Неизвестный статус';




