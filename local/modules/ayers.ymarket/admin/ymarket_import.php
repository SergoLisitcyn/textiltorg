<?
use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Ayers\YMarket\YApi;
use Ayers\YMarket\CategoriesTable;
use Ayers\YMarket\VendorCategoryTable;
use Ayers\YMarket\VendorTable;
use Ayers\YMarket\Helper;

// подключим все необходимые файлы:
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php'); // первый общий пролог

require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/ayers.ymarket/include.php'); // инициализация модуля
require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/ayers.ymarket/prolog.php'); // пролог модуля

Loader::includeModule('ayers.ymarket');
Loc::loadMessages(__FILE__);

CJSCore::Init(array("jquery"));

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight('ayers.ymarket');
// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT == 'D')
    $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));

$APPLICATION->setTitle('Импорт из Yandex.Market');
?>
<?
// здесь вся серверная обработка и подготовка данных
$yApi = new YApi();
$errors = array();

// Обработка входящих данных
switch ($_REQUEST['action'])
{
    // Синхронизация категорий
    case 'sincCatigories':
        $APPLICATION->RestartBuffer();

        Loader::includeModule('ayers.ymarket');

        $arCategories = $yApi->getAllCategories();

        //\Bitrix\Main\Diag\Debug::dumpToFile(count($arCategories));

        $arCategoriesIds = array();
        $rsCategories = CategoriesTable::getList();
        while ($arCategory = $rsCategories->fetch())
        {
            $arCategoriesIds[] = $arCategory['YMARKET_ID'];
        }

        foreach ($arCategories as $arCategory)
        {
            // если не присутствует в хеше, то добавляем в бд
            if (!in_array($arCategory['id'], $arCategoriesIds))
            {
                $rsCategories = CategoriesTable::add(array(
                    'NAME' => $arCategory['name'],
                    'UNIQ_NAME' => $arCategory['uniqName'],
                    'YMARKET_ID' => $arCategory['id'],
                    'PARENT_ID' => $arCategory['parentId'],
                    'IS_CHILDREN' => ($arCategory['childrenCount']) ? 1: 0
                ));

                if (!$rsCategories->isSuccess())
                {
                    $errors[] = 'Ошибка добавления элемента';
                }

            }
        }

        if ($yApi->errors)
        {
            $errors += $yApi->errors;
        }

        if ($errors)
        {
            $yApi->showErrors($errors);
        }
        else
        {
            $message = new \CAdminMessage(array(
                'TYPE' => 'OK',
                'HTML' => true,
                'MESSAGE' => 'Синхронизация категорий завершена',
                'DETAILS' => '<a href="/bitrix/admin/ayers_ymarket_import.php">Перезагрузите</a> страницу.'
            ));

            echo $message->Show();
        }

        die;
    break;
    // Синхронизация производителей
    case 'sincVendor':
        $APPLICATION->RestartBuffer();

        Loader::includeModule('ayers.ymarket');

        $arVendors = $yApi->getAllVendors();

        //\Bitrix\Main\Diag\Debug::dumpToFile(count($arCategories));

        $arVendorsIds = array();
        $rsVendors = VendorTable::getList();
        while ($arVendor = $rsVendors->fetch())
        {
            $arVendorsIds[] = $arVendor['YMARKET_ID'];
        }

        foreach ($arVendors as $arVendor)
        {
            // если не присутствует в хеше, то добавляем в бд
            if (!in_array($arVendor['YMARKET_ID'], $arVendorsIds))
            {
                if (!VendorTable::addVendor($arVendor)) {
                    $errors[] = 'Ошибка добавления производителя ['.$arVendor['YMARKET_ID'].'] '. $arVendor['NAME'];
                }
            }
        }

        if ($yApi->errors)
        {
            $errors += $yApi->errors;
        }

        if ($errors)
        {
            $yApi->showErrors($errors);
        }
        else
        {
            $message = new \CAdminMessage(array(
                'TYPE' => 'OK',
                'HTML' => true,
                'MESSAGE' => 'Синхронизация производителей завершена',
                'DETAILS' => '<a href="/bitrix/admin/ayers_ymarket_import.php">Перезагрузите</a> страницу.'
            ));

            echo $message->Show();
        }

        die;
    break;
    // Список производителей категории
    case 'findCategoryVendor':
        $APPLICATION->RestartBuffer();

        $vendors = VendorCategoryTable::getVendorByCategory($_REQUEST['category_id']);
        arsort($vendors);
        echo Helper::getVendorOptions($vendors);
        die;
        break;
}

Loader::includeModule('iblock');

$rsElements = \CIBlockElement::GetList(
    array(),
    array(
        'IBLOCK_ID' => 8 // можно вынести в настройки
    ),
    false,
    false,
    array('ID', 'NAME')
);

$arElements = array();
while ($arElement = $rsElements->GetNext())
{
    $arElements[$arElement['ID']] = $arElement['NAME'];
}

//

$arCategories = CategoriesTable::getListCategories(90401);
$arVendors = VendorCategoryTable::getVendorByCategory($_REQUEST['find_category']);

if (!empty($arCategories))
{
    $table = 'list52';

    $sortObject = new CAdminSorting($table, 'NAME', 'ASC');
    $listObject = new CAdminList($table, $sortObject);

    $filterFields = array(
        'find_category',
        'find_vendor',
        'find_rating',
        'find_offers',
        'find_is_new',
        'find_is_avalibel'
    );
    $listObject->initFilter($filterFields);

    $headers = array();
    $headers['PICTURE'] = array('id' => 'PICTURE', 'content' => 'Изображение', 'default' => true, 'align' => 'center');
    $headers['NAME'] = array('id' => 'NAME', 'content' => 'Название', 'sort' => 'NAME', 'default' => true);
    $headers['VENDOR'] = array('id' => 'VENDOR', 'content' => 'Производитель', 'sort' => 'VENDOR', 'default' => true);
    $headers['RATING'] = array('id' => 'RATING', 'content' => 'Рейтинг', 'sort' => 'RATING', 'default' => true);
    $headers['OFFERS'] = array('id' => 'OFFERS', 'content' => 'Количество предложений', 'sort' => 'OFFERS', 'default' => true);
    $headers['PRICE_MIN'] = array('id' => 'PRICE_MIN', 'content' => 'Мин. цена', 'sort' => 'PRICE_MIN', 'default' => true);
    $headers['PRICE_MAX'] = array('id' => 'PRICE_MAX', 'content' => 'Макс. цена', 'sort' => 'PRICE_MAX', 'default' => true);

    $listObject->addHeaders($headers);

    if(!empty($set_filter))
    {
        $pager = $_REQUEST['page'] ?: 1;

        $rez = $yApi->getModels($find_category, $find_vendor, $pager);
        $arModels = $rez['item'];

        foreach ($arModels as $n => $arModel)
        {
            if (!empty($find_rating) && (float)$arModel['rating'] < (float)$find_rating)
            {
                unset($arModels[$n]);
            }

            if (!empty($find_offers) && (float)$arModel['offersCount'] < (float)$find_offers)
            {
                unset($arModels[$n]);
            }

            if (!empty($find_is_new) && empty($arModel['isNew']))
            {
                unset($arModels[$n]);
            }
            if (!empty($find_is_avalibel) && empty($arModel['prices']))
            {
                unset($arModels[$n]);
            }
        }

        if (!empty($arModels))
        {
            foreach ($arModels as $arModel)
            {
                $arItem = array(
                    'PICTURE' => $arModel['previewPhoto']['url'],
                    'NAME' => $arModel['name'],
                    'RATING' => $arModel['rating'],
                    'PRICE_MIN' => $arModel['prices']['min'],
                    'PRICE_MAX' => $arModel['prices']['max']
                );

                $icon = '';
                foreach ($arElements as $elementId => $elementName)
                {
                    if (preg_match('/'.$arModel['name'].'/', $elementName))
                    {
                        $icon = '<div class="icon-picture">Есть в БД</div>';
                        break;
                    }
                }

                $rowList[$arModel['ID']] = $row = &$listObject->addRow($arModel['id'], $arItem);
                $row->AddViewField("RATING", ($arModel['rating'] >=0 )? $arModel['rating']: 'Нет');
                $row->AddViewField("OFFERS", $arModel['offersCount']);
                $row->AddViewField("PICTURE", '<img src="'.$arModel['previewPhoto']['url'].'" width="'.$arModel['previewPhoto']['width'].'" height="'.$arModel['previewPhoto']['height'].'">'.$icon);
                $row->AddViewField("PRICE_MIN", number_format($arModel['prices']['min'], 0, '.', ' ').' '.$arModel['prices']['curName']);
                $row->AddViewField("PRICE_MAX", number_format($arModel['prices']['max'], 0, '.', ' ').' '.$arModel['prices']['curName']);



                $row->AddViewField("NAME", '<a href="ayers_ymarket_model.php?ID='.$arModel['id'].'" target="_blank">'.$arModel['name'].'</a>');
            }
            if ($rez['total'] > $rez['current']) {
                $row1 = $listObject->addRow();
                $row1->AddViewField("PICTURE", '<a href="#more-model" class="adm-btn" data-page="'.($pager+1).'">Загрузить еще</a>');
               $row1->AddViewField('NAME', "Загруженно и отфильтрованно ".$rez['current'] .' из '.$rez['total']);
            }
        }
    }

    $listObject->checkListMode();
}
else
{
    if (empty($arCategories)) {
        $errors[] = array('Категорий не найдено', 'Запустите синхронизацию категорий.');
    }
}

if ($yApi->errors)
{
    $errors += $yApi->errors;
}
?>
<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php'); // второй общий пролог

// здесь вывод страницы

$menu = array(
    array(
        'TEXT' => 'Синхронизация производителей',
        'LINK' => '#sinc-vendor'
    ),
    array(
        'TEXT' => 'Синхронизация категорий',
        'LINK' => '#sinc-categories'
    ),
    array(
        'TEXT' => 'Статистика запросов',
        'LINK' => 'ayers_ymarket_statistics.php?lang='.LANG
    )
);
$context = new CAdminContextMenu($menu);
$context->Show();
?>

<div id="result-messages">
    <?if (!empty($errors)):?>
        <?$yApi->showErrors($errors);?>
    <?endif?>
</div>

<?if (empty($errors)):?>
    <form method="GET" name="find-ymarket-goods" id="find-ymarket-goods" action="<?=$APPLICATION->getCurPageParam()?>">
        <?
        $arFindFields = array(
            'Категория на Яндекс.Маркет',
            'Производители на Яндекс.Маркет',
            'Новинки',
            'Доступно к покупке',
            'Мин. рейтинг',
            'Мин. количество предложений'
        );

        $filterUrl = $APPLICATION->getCurPageParam();
        $filterObject = new CAdminFilter($table.'_filter', $arFindFields, array('table_id' => $table, 'url' => $filterUrl));
        $filterObject->setDefaultRows(array('find_is_new'));
        $filterObject->begin();
        ?>
        <tr>
            <td>Категория на Яндекс.Маркет</td>
            <td id="select-category-container">
                <select name="find_category">
                    <?=Helper::getCategoriesOptions($arCategories, $find_category)?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Производители на Яндекс.Маркет</td>
            <td id="select-vendor-container">
                <select name="find_vendor">
                    <?arsort($arVendors)?>
                    <?=Helper::getVendorOptions($arVendors, $find_vendor)?>
                </select>
            </td>
        </tr>
        <tr >
            <td>Новинки</td>
            <td>
                <input type="checkbox" name="find_is_new" value="1" <?if($find_is_new):?>checked<?endif?>>
            </td>
        </tr>
        <tr >
            <td>Доступно к покупке</td>
            <td>
                <input type="checkbox" name="find_is_avalibel" value="1" <?if($find_is_avalibel):?>checked<?endif?>>
            </td>
        </tr>
        <tr>
            <td>Мин. рейтинг</td>
            <td>
                <?
                $ratings = array(1,2,3,3.5,4,4.5,5);
                ?>
                <select name="find_rating">
                    <option value="">Не учитывать</option>
                    <?foreach ($ratings as $rating):?>
                        <option value="<?=$rating?>" <?if($find_rating == $rating):?>selected<?endif?>><?=$rating?></option>
                    <?endforeach?>
                <select>
            </td>
        </tr>
        <tr>
            <td>Мин. количество предложений</td>
            <td>
                <?
                $offers = array(3,5,10,20,30,50,100);
                ?>
                <select name="find_offers">
                    <option value="">Не учитывать</option>
                    <?foreach ($offers as $offer):?>
                        <option value="<?=$offer?>" <?if($find_offers == $offer):?>selected<?endif?>><?=$offer?></option>
                    <?endforeach?>
                <select>
            </td>
        </tr>
        <?
        $filterObject->buttons(
            array(
                'table_id' => $table,
                'url' => $APPLICATION->getCurPageParam('', array('ID')),
                'form' => 'find-ymarket-goods'
            )
        );
        $filterObject->end();
        ?>
    </form>
<?if ($_REQUEST['action'] == 'more') {
    $APPLICATION->RestartBuffer();
    }?>
    <?$listObject->displayList();?>
    <?if ($_REQUEST['action'] == 'more') {
        die();
    }?>
<?endif?>

<style type="text/css">
    #bx-admin-prefix .adm-btn-load.load-button-top-panel {
        box-sizing: border-box;
        padding: 7px 15px !important;
    }

    #bx-admin-prefix .adm-btn-load.load-button-top-panel .adm-btn-load-img {
        top: 50%;
        left: 50%;
        margin: -10px 0 0 -10px;
    }
</style>

<script>
    $(function() {
        $('a[href="#sinc-categories"]').click(function() {
            var button = $(this),
                load = $('<div/>');

            button.addClass('adm-btn-load load-button-top-panel');
            load.addClass('adm-btn-load-img').appendTo(button);

            $.get(
                'ayers_ymarket_import.php',
                {
                    action: 'sincCatigories'
                },
                function(data) {
                    $('#result-messages').html(data);

                    button.removeClass('adm-btn-load load-button-top-panel');
                    button.find('.adm-btn-load-img').remove();
                }
            );
            return false;
        });
        $('a[href="#sinc-vendor"]').click(function() {
            var button = $(this),
                load = $('<div/>');

            button.addClass('adm-btn-load load-button-top-panel');
            load.addClass('adm-btn-load-img').appendTo(button);

            $.get(
                'ayers_ymarket_import.php',
                {
                    action: 'sincVendor'
                },
                function(data) {
                    $('#result-messages').html(data);

                    button.removeClass('adm-btn-load load-button-top-panel');
                    button.find('.adm-btn-load-img').remove();
                }
            );
            return false;
        });
        $('body').on('click', 'a[href="#more-model"]', function() {
            var button = $(this),
                load = $('<div/>');
            var table = $('#list52 tbody');

            button.addClass('adm-btn-load load-button-top-panel');
            load.addClass('adm-btn-load-img').appendTo(button);

            var data = $('#find-ymarket-goods').serialize();
            data +='&set_filter=Найти&action=more&page=' + button.data('page');
            $.post(
                'ayers_ymarket_import.php',
                data,
                function(data) {
                    var list = $(data).find('.adm-list-table tbody tr');
                    button.parent().parent().remove();
                    table.append(list);
                }
            );
            return false;
        });
        $('#select-category-container select').change(function() {

            var list = $('#select-vendor-container select');

            $.get(
                'ayers_ymarket_import.php',
                {
                    action: 'findCategoryVendor',
                    category_id: $(this).val()
                },
                function(data) {
                    console.log(data);
                    $(list).html(data);

                }
            );
            return false;
        });
    });
</script>

<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>