<?
$MODULE_ID = 'bxmaker.smsnotice';

$MESS[$MODULE_ID . '_MODULE_NAME'] = 'СМС Оповещения';
$MESS[$MODULE_ID . '_MODULE_DESCRIPTION'] = 'Модуль для смс оповещений пользователей о заказах, при регистрации и при других заданных условиях. Позволяет в любой момент сменить сервис для отправки СМС по наиболее выгодным тарифам.';
$MESS[$MODULE_ID .'_PARTNER_NAME'] = "BXmaker";
$MESS[$MODULE_ID .'_PARTNER_URI'] = "http://bxmaker.ru/";

// Типы шаблонов ===========================================================

$MESS[$MODULE_ID . '.BXMAKER_AUTHUSERPHONE_SENDCODE.TEMPLATE_TYPE.NAME'] = "Отправка временного кода";
$MESS[$MODULE_ID . '.BXMAKER_AUTHUSERPHONE_SENDCODE.TEMPLATE_TYPE.DESCR'] = "#PHONE# - телефон получателя
#CODE# - временный код";

//---
$MESS[$MODULE_ID . '.BXMAKER_AUTHUSERPHONE_SENDCODE.TEMPLATE.NAME.main'] = "Отправка временного кода";
$MESS[$MODULE_ID . '.BXMAKER_AUTHUSERPHONE_SENDCODE.TEMPLATE.PHONE.main'] = "#PHONE#";
$MESS[$MODULE_ID . '.BXMAKER_AUTHUSERPHONE_SENDCODE.TEMPLATE.TEXT.main'] = "Ваш временный пароль/код подтверждения - #CODE#";


// Типы шаблонов ===========================================================
$MESS[$MODULE_ID . '.BXMAKER_AUTHUSERPHONE_USERADD.TEMPLATE_TYPE.NAME'] = "Регистрация пользователя";
$MESS[$MODULE_ID . '.BXMAKER_AUTHUSERPHONE_USERADD.TEMPLATE_TYPE.DESCR'] = "#PHONE# - номер получателя
#PASSWORD# - новый пароль";

//---
$MESS[$MODULE_ID . '.BXMAKER_AUTHUSERPHONE_USERADD.TEMPLATE.NAME.main'] = "Регистрация пользователя";
$MESS[$MODULE_ID . '.BXMAKER_AUTHUSERPHONE_USERADD.TEMPLATE.PHONE.main'] = "#PHONE#";
$MESS[$MODULE_ID . '.BXMAKER_AUTHUSERPHONE_USERADD.TEMPLATE.TEXT.main'] = "Вы успешно зарегистрированны на сайте. Ваш пароль - #PASSWORD#";


// Типы шаблонов ===========================================================
$MESS[$MODULE_ID . '.BXMAKER_AUTHUSERPHONE_USERCHANGEPASSWORD.TEMPLATE_TYPE.NAME'] = "Изменение пароля пользователя";
$MESS[$MODULE_ID . '.BXMAKER_AUTHUSERPHONE_USERCHANGEPASSWORD.TEMPLATE_TYPE.DESCR'] = "#PHONE# - Номер получателя
#PASSWORD# - новый пароль";

//---
$MESS[$MODULE_ID . '.BXMAKER_AUTHUSERPHONE_USERCHANGEPASSWORD.TEMPLATE.NAME.main'] = "Изменение пароля пользователя";
$MESS[$MODULE_ID . '.BXMAKER_AUTHUSERPHONE_USERCHANGEPASSWORD.TEMPLATE.PHONE.main'] = "#PHONE#";
$MESS[$MODULE_ID . '.BXMAKER_AUTHUSERPHONE_USERCHANGEPASSWORD.TEMPLATE.TEXT.main'] = "Ваш новый пароль - #PASSWORD#";


// Типы шаблонов ===========================================================
// main Регистрация пользвоателя
$MESS[$MODULE_ID . '.MAIN_USER_ADD.TEMPLATE_TYPE.NAME'] = "Регистрация пользователя";
$MESS[$MODULE_ID . '.MAIN_USER_ADD.TEMPLATE_TYPE.DESCR'] = "#PHONE# - телефон получателя
#LOGIN# - логин пользователя
#PASSWORD# - пароль пользователя";

//---
$MESS[$MODULE_ID . '.MAIN_USER_ADD.TEMPLATE.NAME.main'] = "Регистрация пользователя";
$MESS[$MODULE_ID . '.MAIN_USER_ADD.TEMPLATE.PHONE.main'] = "#PHONE#";
$MESS[$MODULE_ID . '.MAIN_USER_ADD.TEMPLATE.TEXT.main'] = "Вы успешно зарегистрированы на сайте #SERVER_NAME#. Для входа используйте логин - #LOGIN# и пароль #PASSWORD#";

// Типы шаблонов ===========================================================
// main Изменение пароля
$MESS[$MODULE_ID . '.MAIN_USER_CHANGEPASSWORD.TEMPLATE_TYPE.NAME'] = "Изменение пароля";
$MESS[$MODULE_ID . '.MAIN_USER_CHANGEPASSWORD.TEMPLATE_TYPE.DESCR'] = "#PHONE# - телефон
#LOGIN# - логин
#PASSWORD# - пароль";

//---
$MESS[$MODULE_ID . '.MAIN_USER_CHANGEPASSWORD.TEMPLATE.NAME.main'] = "Изменение пароля пользователя";
$MESS[$MODULE_ID . '.MAIN_USER_CHANGEPASSWORD.TEMPLATE.PHONE.main'] = "#PHONE#";
$MESS[$MODULE_ID . '.MAIN_USER_CHANGEPASSWORD.TEMPLATE.TEXT.main'] = "Ваш пароль на сайте #SERVER_NAME# успешно изменен. Используйте для входа логин - #LOGIN# и пароль #PASSWORD#";


// Типы шаблонов ===========================================================
// sale Новый заказ
$MESS[$MODULE_ID . '.ORDER_NEW.TEMPLATE_TYPE.NAME'] = "Новый заказ";
$MESS[$MODULE_ID . '.ORDER_NEW.TEMPLATE_TYPE.DESCR'] = "#PHONE# - номер телефона
#ORDER_ID# - номер заказа
#ORDER_STATUS_NAME# - название статуса заказа
#PRICE# - общая стоимость заказа
#DELIVERY_NAME# - наименование способа доставки
#DELIVERY_ID# - идентификатор службы доставки
#PRICE_DELIVERY# - стоимость доставки заказа
#PAY_SYSTEM_ID# - платежная система, которой (будет) оплачен заказ
#PAY_SYSTEM_NAME# - наименование способа оплаты
#PROPERTY_VALUE_{CODE}# -  шаблон подстановки значения свойства заказа, например #PROPERTY_VALUE_ADRES#
#TRACKING_NUMBER# - идентификатор отправления, если их несколько, они будут через запятую";

//---
$MESS[$MODULE_ID . '.ORDER_NEW.TEMPLATE.NAME.main'] = "Новый заказ";
$MESS[$MODULE_ID . '.ORDER_NEW.TEMPLATE.PHONE.main'] = "#PHONE#";
$MESS[$MODULE_ID . '.ORDER_NEW.TEMPLATE.TEXT.main'] = "Ваш заказ №#ORDER_ID# на сумму - #PRICE# руб. принят. Отследить статус заказа можно в личном кабинете.";




// sale  TRACKING_NUMBER Идентификтаор отправления
$MESS[$MODULE_ID . '.TRACKING_NUMBER.TEMPLATE_TYPE.NAME'] = "Добавлен идентификатор отправления";
$MESS[$MODULE_ID . '.TRACKING_NUMBER.TEMPLATE_TYPE.DESCR'] = "#PHONE# - номер телефона
#ORDER_ID# - номер заказа
#ORDER_STATUS_NAME# - название статуса заказа
#PRICE# - общая стоимость заказа
#DELIVERY_NAME# - наименование способа доставки
#DELIVERY_ID# - идентификатор службы доставки
#PRICE_DELIVERY# - стоимость доставки заказа
#PAY_SYSTEM_ID# - платежная система, которой (будет) оплачен заказ
#PAY_SYSTEM_NAME# - наименование способа оплаты
#PROPERTY_VALUE_{CODE}# -  шаблон подстановки значения свойства заказа, например #PROPERTY_VALUE_ADRES#
#TRACKING_NUMBER# - идентификатор отправления, если их несколько, они будут через запятую";

//---
$MESS[$MODULE_ID . '.TRACKING_NUMBER.TEMPLATE.NAME.main'] = "Добавлен идентификатор отправления";
$MESS[$MODULE_ID . '.TRACKING_NUMBER.TEMPLATE.PHONE.main'] = "#PHONE#";
$MESS[$MODULE_ID . '.TRACKING_NUMBER.TEMPLATE.TEXT.main'] = "Добрый день!  Идентификатор отправления вашего заказа  №#ORDER_ID# -  #TRACKING_NUMBER# ";


// Типы шаблонов ===========================================================
// sale Заказ отменен
$MESS[$MODULE_ID . '.ORDER_CANCELED.TEMPLATE_TYPE.NAME'] = "Заказ отменен";
$MESS[$MODULE_ID . '.ORDER_CANCELED.TEMPLATE_TYPE.DESCR'] = "#PHONE# - номер телефона
#ORDER_ID# - номер заказа
#ORDER_STATUS_NAME# - название статуса заказа
#PRICE# - общая стоимость заказа
#DELIVERY_NAME# - наименование способа доставки
#DELIVERY_ID# - идентификатор службы доставки
#PRICE_DELIVERY# - стоимость доставки заказа
#PAY_SYSTEM_ID# - платежная система, которой (будет) оплачен заказ
#PAY_SYSTEM_NAME# - наименование способа оплаты
#PROPERTY_VALUE_{CODE}# -  шаблон подстановки значения свойства заказа, например #PROPERTY_VALUE_ADRES#
#TRACKING_NUMBER# - идентификатор отправления, если их несколько, они будут через запятую";

//---
$MESS[$MODULE_ID . '.ORDER_CANCELED.TEMPLATE.NAME.main'] = "Заказ отменен";
$MESS[$MODULE_ID . '.ORDER_CANCELED.TEMPLATE.PHONE.main'] = "#PHONE#";
$MESS[$MODULE_ID . '.ORDER_CANCELED.TEMPLATE.TEXT.main'] = "Ваш заказ №#ORDER_ID# отменен";



// Типы шаблонов ===========================================================
// sale Заказ оплачен
$MESS[$MODULE_ID . '.ORDER_PAYED.TEMPLATE_TYPE.NAME'] = "Заказ оплачен";
$MESS[$MODULE_ID . '.ORDER_PAYED.TEMPLATE_TYPE.DESCR'] = "#PHONE# - номер телефона
#ORDER_ID# - номер заказа
#ORDER_STATUS_NAME# - название статуса заказа
#PRICE# - общая стоимость заказа
#DELIVERY_NAME# - наименование способа доставки
#DELIVERY_ID# - идентификатор службы доставки
#PRICE_DELIVERY# - стоимость доставки заказа
#PAY_SYSTEM_ID# - платежная система, которой (будет) оплачен заказ
#PAY_SYSTEM_NAME# - наименование способа оплаты
#PROPERTY_VALUE_{CODE}# -  шаблон подстановки значения свойства заказа, например #PROPERTY_VALUE_ADRES#
#TRACKING_NUMBER# - идентификатор отправления, если их несколько, они будут через запятую";

//---
$MESS[$MODULE_ID . '.ORDER_PAYED.TEMPLATE.NAME.main'] = "Заказ оплачен";
$MESS[$MODULE_ID . '.ORDER_PAYED.TEMPLATE.PHONE.main'] = "#PHONE#";
$MESS[$MODULE_ID . '.ORDER_PAYED.TEMPLATE.TEXT.main'] = "Ваш заказ №#ORDER_ID# оплачен";



// Типы шаблонов ===========================================================
// sale Статус заказа
$MESS[$MODULE_ID . '.ORDER_STATUS.TEMPLATE_TYPE.NAME'] = "Заказ - смена статуса";
$MESS[$MODULE_ID . '.ORDER_STATUS.TEMPLATE_TYPE.DESCR'] = "#PHONE# - номер телефона
#ORDER_ID# - номер заказа
#ORDER_STATUS_NAME# - название статуса заказа
#PRICE# - общая стоимость заказа
#DELIVERY_NAME# - наименование способа доставки
#DELIVERY_ID# - идентификатор службы доставки
#PRICE_DELIVERY# - стоимость доставки заказа
#PAY_SYSTEM_ID# - платежная система, которой (будет) оплачен заказ
#PAY_SYSTEM_NAME# - наименование способа оплаты
#PROPERTY_VALUE_{CODE}# -  шаблон подстановки значения свойства заказа, например #PROPERTY_VALUE_ADRES#
#TRACKING_NUMBER# - идентификатор отправления, если их несколько, они будут через запятую
";

//---
$MESS[$MODULE_ID . '.ORDER_STATUS.TEMPLATE.NAME.main'] = "Смена статуса заказа";
$MESS[$MODULE_ID . '.ORDER_STATUS.TEMPLATE.PHONE.main'] = "#PHONE#";
$MESS[$MODULE_ID . '.ORDER_STATUS.TEMPLATE.TEXT.main'] = "Статус вашего заказа №#ORDER_ID# -  #ORDER_STATUS_NAME# ";



