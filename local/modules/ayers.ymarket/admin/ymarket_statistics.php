<?
use \Bitrix\Main;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use Ayers\YMarket\StatisticsTable;

// подключим все необходимые файлы:
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php'); // первый общий пролог

require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/ayers.ymarket/include.php'); // инициализация модуля
require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/ayers.ymarket/prolog.php'); // пролог модуля

Loader::includeModule('ayers.ymarket');
Loc::loadMessages(__FILE__);

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight('ayers.ymarket');
// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT == 'D')
    $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));

$APPLICATION->setTitle('Статистика запросов');
?>
<?
// здесь вся серверная обработка и подготовка данных

$tableId = 'tbl_ymarket_statistics';
$sortObject = new CAdminSorting($tableId, 'DATE', 'ASC');
$listObject = new CAdminList($tableId, $sortObject);

if(!isset($by))
    $by = 'DATE';
if(!isset($order))
    $order = 'ASC';

$filterFields = array(
    'find_date',
    'find_method'
);
$listObject->initFilter($filterFields);

$filter = array();
if($find_date)
    $filter['>=DATE'] = $find_date;
if($find_method)
    $filter['%METHOD'] = $find_method;

$headers = array();
$headers['ID'] = array('id' => 'ID', 'content' => 'ID', 'sort' => 'ID', 'default' => true, 'align' => 'center');
$headers['DATE'] = array('id' => 'DATE', 'content' => 'Дата', 'sort' => 'DATE', 'default' => true);
$headers['METHOD'] = array('id' => 'METHOD','content' => 'Метод', 'sort' => 'METHOD', 'default' => true);

$listObject->addHeaders($headers);

$select = array();
$selectFields = array_keys($headers);
foreach($selectFields as $fieldName)
{
    $select[$fieldName] = $fieldName;
}

$nav = new Main\UI\AdminPageNavigation('pages-ymarket-statistics');
$queryObject = StatisticsTable::getList(array(
    'select' => $select,
    'filter' => $filter,
    'order' => array($by => $order),
    'count_total' => true,
    'offset' => $nav->getOffset(),
    'limit' => $nav->getLimit()
));

$nav->setRecordCount($queryObject->getCount());
$listObject->setNavigation($nav, 'Страница');

while($rest = $queryObject->fetch())
{
    $rowList[$subscribe['ID']] = $row = &$listObject->addRow($store['ID'], $rest);
}

$footerArray = array(array('title' => 'title',
    'value' => $queryObject->getCount()));
$listObject->addFooter($footerArray);

$listObject->checkListMode();
?>
<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php'); // второй общий пролог

// здесь вывод страницы

$menu = array(
    array(
        'ICON' => 'btn_list',
        'TEXT' => 'Импорт товаров',
        'LINK' => 'ayers_ymarket_import.php?lang='.LANG
    )
);
$context = new CAdminContextMenu($menu);
$context->Show();
?>

<form method="GET" name="find_subscribe_form" id="find_subscribe_form" action="<?=$APPLICATION->getCurPageParam()?>">
    <?
    $findFields = array(
        'ID',
        'Дата',
        'Метод'
    );

    $filterUrl = $APPLICATION->getCurPageParam();
    $filterObject = new CAdminFilter($tableId.'_filter', $findFields, array('table_id' => $tableId, 'url' => $filterUrl));
    $filterObject->setDefaultRows(array('find_date', 'find_method'));
    $filterObject->begin(); ?>
    <tr>
        <td>ID</td>
        <td><input type="text" name="find_id" size="11" value="<?=htmlspecialcharsbx($find_id)?>"></td>
    </tr>
    <tr>
        <td>Дата</td>
        <td><input type="text" name="find_date" size="40" value="<?=htmlspecialcharsbx($find_date)?>"></td>
    </tr>
    <tr>
        <td>Метод</td>
        <td><input type="text" name="find_method" size="40" value="<?=htmlspecialcharsbx($find_method)?>"></td>
    </tr>
    <?
    $filterObject->buttons(array('table_id' => $tableId,
        'url' => $APPLICATION->getCurPageParam('', array('ID')), 'form' => 'find_statistics_form'));
    $filterObject->end();
    ?>
</form>
<?
$listObject->displayList();
?>

<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>