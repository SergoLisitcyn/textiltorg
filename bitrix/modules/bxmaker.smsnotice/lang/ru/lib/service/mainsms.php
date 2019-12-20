<?
$module_id = 'bxmaker.smsnotice';

$MESS[$module_id .  '.SERVICE.MAINSMS.DESCRIPTION'] = '<p>Сервис с дешевыми СМС - <a  target="_blank" href="https://bxmaker.ru/yHYWi">MainSMS (откроется в новой вкладке)</a>, зарегистрироваться  и получить 10 рублей для тестирования <a href="https://bxmaker.ru/BVPDN"   target="_blank"> можно здесь (откроется в новой вкладке)</a><br/>
После этого, нужно добавить API Проект <a href="https://bxmaker.ru/sGuFk" target="_blank">здесь</a> и заполнить поля - Название проекта, это же название затем укажите здесь в настрйоках ниже.
 <br/> Имя отправителя - буквенное имя отправителя можно задать <a href="https://bxmaker.ru/SlOlY" target="_blank">здесь (откроется в новой вкладке)</a>.
  <br> После модерации, вам надо заключить договора, составить гарантийные письма согласно образцам, поробнее на сайте, чтобы польлзователи получали СМС, у которых бы в качестве отправителя высвечивалось  заданное вами имя отправителя.
  <br/>Описание проекта можете не заполнять. URL обновлений статусов будет указан здесь #SERVICE_NOTICE_URL#, после нажатия кнопки применить или сохранить. Затем нажимаете кнопку создать проект.<br><br></p>';

$MESS[$module_id . '.SERVICE.MAINSMS.CURRENCY'] = ' руб.';

$MESS[$module_id . '.SERVICE.MAINSMS.PARAMS.PROJECT'] = 'Название проекта (project_name)';
$MESS[$module_id . '.SERVICE.MAINSMS.PARAMS.KEY'] = 'Ключ (api_key)';
$MESS[$module_id . '.SERVICE.MAINSMS.PARAMS.FROM'] = 'Имя отправителя ';
$MESS[$module_id . '.SERVICE.MAINSMS.PARAMS.USE_SSL'] = 'Защищенное соединение';
$MESS[$module_id . '.SERVICE.MAINSMS.PARAMS.TEST_MODE'] = 'Тестовый режим';

$MESS[$module_id . '.SERVICE.MAINSMS.PARAMS.PROJECT_HINT'] = '';
$MESS[$module_id . '.SERVICE.MAINSMS.PARAMS.KEY_HINT'] = 'В списке API проектов нажмите в столбике ключ на кнопку показать и скопировав, введите здесь эту строку.';
$MESS[$module_id . '.SERVICE.MAINSMS.PARAMS.FROM_HINT'] = 'Имя отправителя должно быть таким же как в настройках API проекта';
$MESS[$module_id . '.SERVICE.MAINSMS.PARAMS.USE_SSL_HINT'] = 'Использование защищенного соединения https';
$MESS[$module_id . '.SERVICE.MAINSMS.PARAMS.TEST_MODE_HINT'] = 'Все отправленные СМС будут отправлены, но не будут доставлены и не будет списана их стоимость с баланса, подходит для тестирования работы сервиса.';

$MESS[$module_id . '.SERVICE.MAINSMS.PARAMS.DLR_URL_HINT'] = 'URL на который приходит оповещение от сервиса со статусом, например  доставлено или ошибка отправки и так далее.';


$MESS[$module_id . '.SERVICE.MAINSMS.NOTICE.NOT_FOUND_SMS_BY_ID'] = 'Не найдено сообщение с таким идентификатором';
$MESS[$module_id . '.SERVICE.MAINSMS.NOTICE.BXMAKER_SMSNOTICE_SERVICE_NOTICE_ERROR_UNKNOWN_SMS_STATUS'] = 'Неизвестный статус сообщения';


$MESS[$module_id . '.SERVICE.MAINSMS.ERROR_UNKNOWN'] = 'Неизвестная ошибка';
$MESS[$module_id . '.SERVICE.MAINSMS.ERROR_1'] = 'Параметр project пуст';
$MESS[$module_id . '.SERVICE.MAINSMS.ERROR_2'] = 'Не верная подпись запроса(параметр sign)';
$MESS[$module_id . '.SERVICE.MAINSMS.ERROR_3'] = 'Параметр message пуст';
$MESS[$module_id . '.SERVICE.MAINSMS.ERROR_4'] = 'Параметр recipients пуст';
$MESS[$module_id . '.SERVICE.MAINSMS.ERROR_5'] = 'Проект с таким именем не найден';
$MESS[$module_id . '.SERVICE.MAINSMS.ERROR_6'] = 'Параметр recipients не содержит ни одного получателя';
$MESS[$module_id . '.SERVICE.MAINSMS.ERROR_7'] = 'Не достаточно денег на счету';
$MESS[$module_id . '.SERVICE.MAINSMS.ERROR_8'] = 'Параметр сендер пуст, содержит недопустимые символы или недопустимой длинны.';
$MESS[$module_id . '.SERVICE.MAINSMS.ERROR_9'] = 'Имя отправителя не проверено';
$MESS[$module_id . '.SERVICE.MAINSMS.ERROR_10'] = 'Проект выключен';


