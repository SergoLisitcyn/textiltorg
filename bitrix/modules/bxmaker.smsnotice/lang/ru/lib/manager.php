<?
$module_id = 'bxmaker.smsnotice';


// manager.php

$MESS[$module_id . '.MANAGER.MODULE_DEMO'] = 'Модуль работает в демо-режиме. Через несколько тестовые период закончится. Вы можете купить версию без ограничений и использовать новые сервисы и их выгоды при обновлении.';
$MESS[$module_id . '.MANAGER.MODULE_DEMO_EXPIRED'] = 'Время тестового периода закончилось! Приобретите полную версию модуля, чтобы снять ограничения и продолжить работу, а также получать обновления  и возможность использовать новые сервисы и их выгоды.';

$MESS[$module_id . '.MANAGER.OK'] = 'Ок';
$MESS[$module_id . '.MANAGER.NOT_FOUNT_TEMPLATES'] = 'Не надены активные шаблоны такого типа';
$MESS[$module_id . '.MANAGER.SEND_SMS_DEBUG_MODE'] = 'Режим отладки';

$MESS[$module_id . '.MANAGER.ERROR_SERVICE_INITIALIZATION'] = 'Не удалось инициализировать сервис для отправки СМС. Вероятнее всего у вас нет ни одного активного сервиса, вам нужно <a href="/bitrix/admin/bxmaker.smsnotice_service_list.php?lang=ru" target="_blank" >добавить здесь</a>';
$MESS[$module_id . '.MANAGER.ERROR_INVALID_PHONE'] = 'Номер телефона введен с ошибками';

$MESS[$module_id . '.MANAGER.NOTICE.INVALID_PARAM_SERVICE_ID'] = 'Значение параметра serviceId не указано или отсутствует';
$MESS[$module_id . '.MANAGER.NOTICE.UNKNOWN_SERVICE_ID'] = 'Не найден сервис с таким идентификтаором';

$MESS[$module_id . '.MANAGER.EVENT_ONBEFORE_SEND_ERROR_EVENTRESULT'] = 'Один из обработчиков событий вернул статус - ERROR. Отправка не произошла.';
$MESS[$module_id . '.MANAGER.EVENT_ONBEFORE_SEND_EMPTY_PARAMS'] = 'После рабоыт обработчиков событий отсутствует 1 или несколько обязательны парамтеров дял отправки. Отправка не произошла.';

$MESS[$module_id . '.MANAGER.EVENT_ONBEFORE_SEND_TEMPLATE_ERROR_EVENTRESULT'] = 'Один из обработчиков событий вернул статус - ERROR. Отправка не произошла.';
$MESS[$module_id . '.MANAGER.EVENT_ONBEFORE_SEND_TEMPLATE_EMPTY_PARAMS'] = 'После рабоыт обработчиков событий отсутствует массив с данными, даже пустой - $arFields. Отправка не произошла.';


$MESS[$module_id . '.DEMO_NOTICE'] = 'В демо-режиме доступен полный функционал, ограничения распространяются только на количество дней использования.<br> После окончания демо-периода,  смс сообщения перестанут отправляться как в ручном, так и в автоматическом режиме.<br>После покупки модуля смс сообщения вновь начнут отправляться без ограничений. <br>Приятной работы!';
$MESS[$module_id . '.DEMO_EXPIRED_NOTICE'] = 'Демо-режим закончился. Для восстановдения работы модуля, приобретите платную версию - <a href="https://bxmaker.ru/wPpgd" target="_blank" >здесь.</a>';


?>