<?
use \Bitrix\Main;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use \Ayers\Stores;
use \Ayers\Stores\StoresTable;

// подключим все необходимые файлы:
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php'); // первый общий пролог

require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/ayers.stores/include.php'); // инициализация модуля
require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/ayers.stores/prolog.php'); // пролог модуля

Loader::includeModule('ayers.stores');
Loc::loadMessages(__FILE__);

$ID = (!empty($_REQUEST['ID'])) ? intval($_REQUEST['ID']): 0;
$store = StoresTable::getById($ID)->fetch();

$isDisabled = ($store['TYPE'] == 'СДЭК' || $store['TYPE'] == 'ПЭК');

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight('ayers.stores');
// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT == 'D')
    $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));

if ($ID)
{
    $APPLICATION->setTitle('Пункты самовывоза: Редактирование');
}
else
{
    $APPLICATION->setTitle('Пункты самовывоза: Добавление');
}

?>
<?
// здесь вся серверная обработка и подготовка данных
if ($REQUEST_METHOD == 'POST' && (strlen($save) > 0 || strlen($apply) > 0) && check_bitrix_sessid())
{
    $arFields = ($isDisabled) ?
        array(
            'SHORT_ADDRESS' => $_POST['SHORT_ADDRESS'],
            'METRO' => $_POST['METRO'],
        ):
        array(
            'TYPE' => 'ТекстильТорг',
            'CITY' => $_POST['CITY'],
            'ADDRESS' => $_POST['ADDRESS'],
            'SHORT_ADDRESS' => $_POST['SHORT_ADDRESS'],
            'PHONE' => $_POST['PHONE'],
            'EMAIL' => $_POST['EMAIL'],
            'TIME' => $_POST['TIME'],
            'SORT' => preg_replace('/[^\d]+/', '', $_POST['SORT']),
            'POSTCODE' => intval($_POST['POSTCODE']),
            'POINTS' => preg_replace('/[^\d\,\.]+/', '', $_POST['POINTS']),
            'METRO' => $_POST['METRO']
        );

    if ($ID > 0)
    {
        $result = StoresTable::update($ID, $arFields);
    }
    else
    {
        $result = StoresTable::add($arFields);
        $ID = $result->isSuccess() ? $result->getId() : 0;
    }

    if (!$result->isSuccess())
    {
        $message = new CAdminMessage(array(
            'MESSAGE' => 'Ошибка при сохранении пункта самовывоза',
            'DETAILS' => join('<br>', $result->getErrorMessages())
        ));
    }
    else
    {
        if (strlen($save) > 0)
            LocalRedirect('ayers_stores_list.php?lang='.LANG);
        else
            LocalRedirect($APPLICATION->getCurPage().'?lang='.LANG.'&ID='.$ID);
    }
}
?>
<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php'); // второй общий пролог

// здесь вывод страницы
$menu = array(
    array(
        'ICON' => 'btn_list',
        'TEXT' => 'Список пунктов самовывоза',
        'LINK' => 'ayers_stores_list.php?lang='.LANG
    )
);

$menu[] = array(
    'ICON' => 'btn_new',
    'TEXT' => 'Добавить',
    'LINK' => 'ayers_stores_edit.php?lang='.LANG
);

$menu[] = array(
    'ICON' => 'btn_delete',
    'TEXT' => 'Удалить',
    'LINK' => 'javascript:if(confirm("Вы действительно хотите удалить выбранный пункт самовывоза?")) window.location="ayers_stores_list.php?action=delete&ID='.$ID.'&lang='.LANG.'&'.bitrix_sessid_get().'";',
);

$context = new CAdminContextMenu($menu);
$context->Show();

$tabs = array();
$tabs[] = ($ID) ?
    array('DIV' => 'edit1', 'TAB' => 'Редактирование', 'TITLE' => 'Редактирование пункта самовывоза'):
    array('DIV' => 'edit1', 'TAB' => 'Добавление', 'TITLE' => 'Добавление пункта сомовывоза');


$control = new CAdminTabControl('control', $tabs, false);

if ($message)
{
    echo $message->Show();
}
?>
<form method="POST" action="<?=$APPLICATION->GetCurPage(); ?>?lang=<?=LANG; ?>&amp;ID=<?=$ID; ?>" name="form1" enctype="multipart/form-data">
    <?=bitrix_sessid_post();?>

    <?$control->Begin();?>
    <?$control->BeginNextTab();?>
        <tr>
            <td><label for="TYPE">Транспортная компания</label></td>
            <td><input type="text" id="TYPE" size="30" name="TYPE" disabled value="<?=($isDisabled)? $store['TYPE']: 'ТекстильТорг';?>"></td>
        </tr>
        <tr>
            <td><label for="CITY">Город</label></td>
            <td><input type="text" id="CITY" size="30" name="CITY" <?if($isDisabled):?>disabled<?endif?> value="<?=$store['CITY']?>"></td>
        </tr>
        <tr>
            <td><label for="POSTCODE">Индекс</label></td>
            <td><input type="text" id="POSTCODE" size="10" name="POSTCODE" <?if($isDisabled):?>disabled<?endif?> value="<?=$store['POSTCODE']?>"></td>
        </tr>
        <tr>
            <td><label for="ADDRESS">Адрес</label></td>
            <td><input type="text" id="ADDRESS" size="60" name="ADDRESS" <?if($isDisabled):?>disabled<?endif?> value="<?=$store['ADDRESS']?>"></td>
        </tr>
        <tr>
            <td><label for="SHORT_ADDRESS">Короткий адрес</label></td>
            <td><input type="text" id="SHORT_ADDRESS" size="60" name="SHORT_ADDRESS"  value="<?=$store['SHORT_ADDRESS']?>"></td>
        </tr>
        <tr>
            <td><label for="PHONE">Телефон</label></td>
            <td><input type="text" id="PHONE" size="60" name="PHONE" <?if($isDisabled):?>disabled<?endif?> value="<?=$store['PHONE']?>"></td>
        </tr>
        <tr>
            <td><label for="EMAIL">Электронная почта</label></td>
            <td><input type="text" id="EMAIL" size="30" name="EMAIL" <?if($isDisabled):?>disabled<?endif?> value="<?=$store['EMAIL']?>"></td>
        </tr>
        <tr>
            <td><label for="TIME">Режим работы</label></td>
            <td><input type="text" id="TIME" size="60" name="TIME" <?if($isDisabled):?>disabled<?endif?> value="<?=$store['TIME']?>"></td>
        </tr>
        <tr>
            <td><label for="POINTS">Координаты</label></td>
            <td><input type="text" id="POINTS" size="40" name="POINTS" <?if($isDisabled):?>disabled<?endif?> value="<?=$store['POINTS']?>"><br><a href="https://yandex.ru/map-constructor/location-tool/" target="_blank">Определение координат</a></td>
        </tr>
        <tr>
            <td><label for="METRO">Расстояние до метро</label></td>
            <td><input type="text" id="METRO" size="60" name="METRO" value="<?=$store['METRO']?>"></td>
        </tr>
        <tr>
            <td><label for="SORT">Сортировка</label></td>
            <td><input type="text" id="SORT" size="5" name="SORT" value="<?=(!empty($store['SORT']))? $store['SORT']: 100?>"></td>
        </tr>
    <?$control->EndTab();?>
    <?$control->Buttons(array(
        'back_url' => 'ayers_stores_list.php?lang='.LANG
    ));?>
    <? $control->End(); ?>
</form>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>