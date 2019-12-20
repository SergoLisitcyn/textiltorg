<?
ini_set('max_execution_time', 1500);
use \Bitrix\Main;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use \Ayers\Stores;

// подключим все необходимые файлы:
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php'); // первый общий пролог

require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/ayers.stores/include.php'); // инициализация модуля
require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/ayers.stores/prolog.php'); // пролог модуля

Loader::includeModule('ayers.stores');
Loc::loadMessages(__FILE__);

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight('ayers.stores');
// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT == 'D')
    $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));

$APPLICATION->setTitle('Пункты самовывоза');
?>
<?
// здесь вся серверная обработка и подготовка данных

switch ($_REQUEST['action']) {

    case 'upload_stores':

        $data = array_merge(
            \Ayers\Stores\DataCDEK::GetData(),
            \Ayers\Stores\DataPecom::GetData()
        );

        $result = \Ayers\Stores\Data::Save($data);

        $APPLICATION->RestartBuffer();

        if ($result)
        {
            echo 'OK';
        }

        die;
        break;
    case 'delete':
        if (!empty($ID))
        {
            $deleteItems = is_array($ID)? $ID: array($ID);

            foreach ($deleteItems as $itemID)
            {
                \Ayers\Stores\StoresTable::delete($itemID);
            }
        }

        break;
}


$tableId = 'tbl_stores_list';
$sortObject = new CAdminSorting($tableId, 'CITY', 'ASC');
$listObject = new CAdminList($tableId, $sortObject);

if(!isset($by))
    $by = 'CITY';
if(!isset($order))
    $order = 'ASC';

$filterFields = array(
    'find_id',
    'find_type',
    'find_city',
    'find_address',
    'find_phone',
    'find_email'
);
$listObject->initFilter($filterFields);

$filter = array();
if($find_id)
    $filter['=ID'] = $find_id;
if($find_type)
    $filter['=TYPE'] = $find_type;
if($find_city)
    $filter['%CITY'] = $find_city;
if($find_address)
    $filter['%ADDRESS'] = $find_address;
if($find_phone)
    $filter['%PHONE'] = $find_phone;
if($find_email)
    $filter['%EMAIL'] = $find_email;

$headers = array();
$headers['ID'] = array('id' => 'ID', 'content' => 'ID', 'sort' => 'ID', 'default' => true, 'align' => 'center');
$headers['TYPE'] = array('id' => 'TYPE', 'content' => 'Транспортная компания', 'sort' => 'TYPE', 'default' => true);
$headers['SORT'] = array('id' => 'SORT','content' => 'Сортировка', 'sort' => 'SORT', 'default' => true);
$headers['CITY'] = array('id' => 'CITY','content' => 'Город', 'sort' => 'CITY', 'default' => true);
$headers['CODE'] = array('id' => 'CODE','content' => 'Код пункта выдачи заказа', 'sort' => 'CODE', 'default' => false);
$headers['POSTCODE'] = array('id' => 'POSTCODE','content' => 'Индекс', 'sort' => 'POSTCODE', 'default' => true);
$headers['ADDRESS'] = array('id' => 'ADDRESS','content' => 'Адрес', 'sort' => 'ADDRESS', 'default' => false);
$headers['SHORT_ADDRESS'] = array('id' => 'SHORT_ADDRESS','content' => 'Короткий адрес', 'sort' => 'SHORT_ADDRESS', 'default' => true);
$headers['PHONE'] = array('id' => 'PHONE','content' => 'Телефон', 'sort' => 'PHONE', 'default' => true);
$headers['EMAIL'] = array('id' => 'EMAIL','content' => 'Эл. почта', 'sort' => 'EMAIL', 'default' => false);
$headers['TIME'] = array('id' => 'TIME','content' => 'Режим работы', 'sort' => 'TIME', 'default' => true);

$listObject->addHeaders($headers);


$select = array();
$selectFields = array_keys($headers);
foreach($selectFields as $fieldName)
{
    $select[$fieldName] = $fieldName;
}

$nav = new Main\UI\AdminPageNavigation('pages-stores-list');
$queryObject = \Ayers\Stores\StoresTable::getList(array(
    'select' => $select,
    'filter' => $filter,
    'order' => array($by => $order),
    'count_total' => true,
    'offset' => $nav->getOffset(),
    'limit' => $nav->getLimit()
));

$nav->setRecordCount($queryObject->getCount());
$listObject->setNavigation($nav, 'Страница');

while($store = $queryObject->fetch())
{
    $rowList[$subscribe['ID']] = $row = &$listObject->addRow($store['ID'], $store);

    $actions = array();
    $actionUrl = '&ID='.$store['ID'];
    $actions[] = array(
        'ICON' => 'edit',
        'TEXT' => 'Редактировать',
        'ACTION' => $listObject->ActionRedirect('ayers_stores_edit.php?'.$actionUrl)
    );

    $actions[] = array(
        'ICON' => 'delete',
        'TEXT' => 'Удалить',
        'ACTION' => 'if(confirm("Вы действительно хотите удалить отмеченные пункты самовывоза?")) '.
            $listObject->actionDoGroup($store['ID'], 'delete', $actionUrl)
    );

    $row->addActions($actions);
}

$footerArray = array(array('title' => 'title',
    'value' => $queryObject->getCount()));
$listObject->addFooter($footerArray);

$listObject->addGroupActionTable(array(
    'delete' => 'Удалить'
));

$contextListMenu[] = array(
    'TEXT' => 'Добавить',
    'ICON' => 'btn_new',
    'LINK' => 'ayers_stores_edit.php?lang='.LANGUAGE_ID,
    'TITLE' => 'Добавить пункт выдачи'
);

$contextListMenu[] = array(
    'HTML' => '<input type="button" class="adm-btn" id="button-upload-stores" onclick="StartUploadStores()" value="Загрузить обновления">',
);
$listObject->addAdminContextMenu($contextListMenu);

$listObject->checkListMode();

?>
<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php'); // второй общий пролог

// здесь вывод страницы
?>

    <form method="GET" name="find_subscribe_form" id="find_subscribe_form" action="<?=$APPLICATION->getCurPageParam()?>">
        <?
        $findFields = array(
            'ID',
            'Транспортная компания',
            'Город',
            'Адрес',
            'Телефон',
            'Эл. почта'
        );

        $filterUrl = $APPLICATION->getCurPageParam();
        $filterObject = new CAdminFilter($tableId.'_filter', $findFields, array('table_id' => $tableId, 'url' => $filterUrl));
        $filterObject->setDefaultRows(array('find_item_id', 'find_type', 'find_city'));
        $filterObject->begin(); ?>
        <tr>
            <td>ID</td>
            <td><input type="text" name="find_id" size="11" value="<?=htmlspecialcharsbx($find_id)?>"></td>
        </tr>
        <tr>
            <td>Транспортная компания</td>
            <td>
                <select name="find_type">
                    <option value="">Любая</option>
                    <option value="ТекстильТорг"<?if($type=="ТекстильТорг")echo " selected"?>>
                        ТекстильТорг
                    </option>
                    <option value="СДЭК"<?if($type=="СДЭК")echo " selected"?>>
                        СДЭК
                    </option>
                    <option value="ПЭК"<?if($find_active=="ПЭК")echo " selected"?>>
                        ПЭК
                    </option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Город</td>
            <td><input type="text" name="find_city" size="40" value="<?=htmlspecialcharsbx($find_city)?>"></td>
        </tr>
        <tr>
            <td>Адрес</td>
            <td><input type="text" name="find_address" size="40" value="<?=htmlspecialcharsbx($find_address)?>"></td>
        </tr>
        <tr>
            <td>Телефон</td>
            <td><input type="text" name="find_phone" size="40" value="<?=htmlspecialcharsbx($find_phone)?>"></td>
        </tr>
        <tr>
            <td>Эл. почта</td>
            <td><input type="text" name="find_email" size="40" value="<?=htmlspecialcharsbx($find_email)?>"></td>
        </tr>
        <?
        $filterObject->buttons(array('table_id' => $tableId,
            'url' => $APPLICATION->getCurPageParam('', array('ID')), 'form' => 'find_stores_form'));
        $filterObject->end();
        ?>
    </form>
<?
$listObject->displayList();
?>

    <script language="JavaScript">

        function StartUploadStores()
        {
            document.getElementById('button-upload-stores').disabled = true;

            ShowWaitWindow();

            BX.ajax.post(
                'ayers_stores_list.php',
                {action: 'upload_stores'},
                function(result) {
                    if (result == 'OK')
                    {
                        location.reload();
                    }
                    else
                    {
                        alert('Ошибка обновления данных!');
                    }

                    CloseWaitWindow();
                    document.getElementById('button-upload-stores').disabled = false;
                }
            );
        }
    </script>

<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>