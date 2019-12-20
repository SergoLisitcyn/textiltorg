<?
$module_code = "bxmaker.smsnotice";

$MESS[$module_code.'ACCESS_DENIED'] = 'Нет доступа';

$MESS[$module_code.'_MAIN_PAGE'] = 'Список типов';
$MESS[$module_code. '.NAV_BTN.RETURN'] = 'Вернуться к списку';


$MESS[$module_code.'.TAB.EDIT'] = 'Добавление/Редактирование  шаблона СМС';
$MESS[$module_code.'.PAGE_TITLE'] = 'Добавление/Редактирование шаблона СМС';


$MESS[$module_code. '.FIELD_LABEL.ID'] = 'ID';
$MESS[$module_code. '.FIELD_LABEL.SITE'] = 'Сайты';
$MESS[$module_code. '.FIELD_LABEL.NAME'] = 'Наименование';
$MESS[$module_code. '.FIELD_LABEL.NAME_HINT'] = 'Наименования для вас, чтобы отличить одно от другого';
$MESS[$module_code. '.FIELD_LABEL.ACTIVE'] = 'Активность';
$MESS[$module_code. '.FIELD_LABEL.PHONE'] = 'Номер получателя';
$MESS[$module_code. '.FIELD_LABEL.PHONE_HINT'] = 'Может быть как 79261234567, так и например #PHONE# - которое автоматически при отправке заменится на соответствующее значение';
$MESS[$module_code. '.FIELD_LABEL.TEXT'] = 'Сообщение';
$MESS[$module_code. '.FIELD_LABEL.TEXT_HINT'] = '2 и более подряд идущих пробела, при сохранении, автоматически будут заменены на 1 пробел. По краям текста пробелы лишние будут удалены.';
$MESS[$module_code. '.FIELD_LABEL.TEXT_PLACEHOLDER'] = 'Добрый день, #NAME#! Ваш заказ №#ORDER_ID# принят. Спасибо за покупку!';
$MESS[$module_code. '.FIELD_LABEL.TYPE'] = 'Тип шаблона';
$MESS[$module_code. '.FIELD.TYPE_SELECT'] = '- выберите тип -';


// Ошибки
$MESS[$module_code. '.FIELD_ERROR.EMPTY_NAME'] = 'Укажите название';
$MESS[$module_code. '.FIELD_ERROR.EMPTY_TYPE'] = 'Выдерите тип шаблона';
$MESS[$module_code. '.FIELD_ERROR.EMPTY_TYPE'] = 'Выдерите тип шаблона';
$MESS[$module_code. '.FIELD_ERROR.EMPTY_SITE'] = 'Укажите привязку хотябы к 1 сайту';


//AJAX
$MESS[$module_code.  '.AJAX.INVALID_TYPE'] = 'Выберите тип шаблона';
$MESS[$module_code.  '.AJAX.UNKNOW_TYPE'] = 'Не найден такой тип шаблона';
$MESS[$module_code.  '.AJAX.TEMPLATE_TYPE_FIELD_SITE_NAME'] = '#SITE_NAME# - Название веб-сайта, <small>Из настроек сайта, область - Параметры</small><br>';
$MESS[$module_code.  '.AJAX.TEMPLATE_TYPE_FIELD_SERVER_NAME'] = '#SERVER_NAME# - URL сервера (без http://), <small>Из настроек сайта, область - Параметры</small><br>';




