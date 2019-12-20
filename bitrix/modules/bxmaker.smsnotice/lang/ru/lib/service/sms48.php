<?
$module_id = 'bxmaker.smsnotice';

$MESS[$module_id .  '.SERVICE.SMS48.DESCRIPTION'] = '<p>Сервис с дешевыми СМС, зарегистрироваться и получить доступ <a  target="_blank" href="https://bxmaker.ru/t46LA">можно здесь</a><br/>
После этого, нужно добавить дополнительный аккаунт SMS HTTP API <a href="https://bxmaker.ru/j2erw" target="_blank">здесь</a> и вписать заданные значения в поля API логин и API пароль.
 <br/> Имя отправителя - буквенное имя отправителя можно задать <a href="https://bxmaker.ru/uf7yB" target="_blank">здесь</a>.
  <br> Учтите, что вам надо сразу же заполнить свои данные юр. лица <a href="https://bxmaker.ru/S3cl6" target="_blank">здесь</a>, распечатать договора и гарантийные письма, заполнить, подписать, отсканировать и отправить им на почту сканы, оригиналы же отправить почтой на адрес укаазанный в контактах.
  <br/>После того как вы это сделали, вам сразу включат API, с помощью которого происходит отправка СМС сообщений через данный сервис.<br><br></p>';

$MESS[$module_id . '.SERVICE.SMS48.CURRENCY'] = ' руб.';

$MESS[$module_id . '.SERVICE.SMS48.PARAMS.LOGIN'] = 'Логин от аккаунта';
$MESS[$module_id . '.SERVICE.SMS48.PARAMS.PASS'] = 'Пароль от аккаунта';
$MESS[$module_id . '.SERVICE.SMS48.PARAMS.FROM'] = 'Имя отправителя';
$MESS[$module_id . '.SERVICE.SMS48.PARAMS.API_LOGIN'] = 'API логин';
$MESS[$module_id . '.SERVICE.SMS48.PARAMS.API_PASS'] = 'API пароль';
$MESS[$module_id . '.SERVICE.SMS48.PARAMS.DLR_URL'] = 'URL для оповещения о статусе отправки';

$MESS[$module_id . '.SERVICE.SMS48.PARAMS.LOGIN_HINT'] = 'Используется для отправки в случае, елси не указан API логин';
$MESS[$module_id . '.SERVICE.SMS48.PARAMS.PASS_HINT'] = 'Используется вместе c логином от аккаунта, елси не задан API логин';
$MESS[$module_id . '.SERVICE.SMS48.PARAMS.FROM_HINT'] = 'Имя, из 11 символов которое высвечивается у получаетеля в мобильном телефон в качесвте отправителя, вместо обычного цифрового номера.';
$MESS[$module_id . '.SERVICE.SMS48.PARAMS.API_LOGIN_HINT'] = 'Логин используемый для отправки СМС';
$MESS[$module_id . '.SERVICE.SMS48.PARAMS.API_PASS_HINT'] = 'Пароль используемый для отправки СМС';
$MESS[$module_id . '.SERVICE.SMS48.PARAMS.DLR_URL_HINT'] = 'URL на который приходит оповещение от сервиса со статусом, напримре доставлено или ошибка отправки и так далее.';


$MESS[$module_id . '.SERVICE.SMS48.NOTICE.NOT_FOUND_SMS_BY_ID'] = 'Не найдено сообщение с таким идентификатором';
$MESS[$module_id . '.SERVICE.SMS48.NOTICE.BXMAKER_SMSNOTICE_SERVICE_NOTICE_ERROR_UNKNOWN_SMS_STATUS'] = 'Неизвестный статус сообщения';


