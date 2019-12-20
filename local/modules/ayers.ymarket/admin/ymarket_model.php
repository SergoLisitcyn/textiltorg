<?
use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Ayers\YMarket\YApi;
use Ayers\YMarket\CategoriesTable;
use Ayers\YMarket\Helper;

// подключим все необходимые файлы:
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php'); // первый общий пролог

require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/ayers.ymarket/include.php'); // инициализация модуля
require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/ayers.ymarket/prolog.php'); // пролог модуля

Loader::includeModule('iblock');
Loader::includeModule('catalog');
Loader::includeModule('ayers.ymarket');
Loc::loadMessages(__FILE__);

$ID = (!empty($_REQUEST['ID'])) ? intval($_REQUEST['ID']): 0;

if (empty($ID))
{
    die;
}

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight('ayers.ymarket');
// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT == 'D')
    $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));

$APPLICATION->setTitle('Предосмотр модели');
?>
<?
// здесь вся серверная обработка и подготовка данных

$errors = false;

$yApi = new YApi();

$model = $yApi->getModel($ID);
$offers = $yApi->getModelOffers($ID);
$stores = $yApi->getStores($ID);
$opinion = $yApi->getOpinion($ID);

$description = '';

if (count($offers))
{
    // 10 первых описаний из предложений
    $count = (count($offers['items']) < 10)? count($offers['items']): 10;
    for ($i = 0; $i <= $count; $i++)
    {
        if ($offers['items'][$i]['description'])
        {
            $description .= $offers['items'][$i]['description'].'<br><br>';
        }
    }
}
else
{
    $description .= $model['description'];
}

// поиск элемента
$rsElements = \CIBlockElement::GetList(
    array(),
    array(
        'IBLOCK_ID' => 8, // можно вынести в настройки
        '%NAME' => $model['name']
    ),
    false,
    false,
    array('ID', 'NAME')
);

$element = false;
if ($arElement = $rsElements->GetNext())
{
    $element = $arElement['ID'];
}

if ($_GET['action'] == 'add' && check_bitrix_sessid())
{
    if (empty($element))
    {
        // добавление элемента
        $arFields = array(
            'NAME' => $model['name'],
            'ACTIVE' => 'N',
            'CODE' => \Cutil::translit(
                $model['name'],
                'ru',
                array(
                    'replace_space' => '-',
                    'replace_other' => '-'
                )
            ),
            'IBLOCK_ID' => 8, // todo: можно вынести в настройки
            'DETAIL_TEXT' => $description,
            'DETAIL_TEXT_TYPE' => 'html',
            'PREVIEW_TEXT' => Helper::Truncate($description, 250),
            'PREVIEW_TEXT_TYPE' => 'html',
            'DETAIL_PICTURE' => Helper::makeFileArray($model['mainPhoto']['url']),
            'PROPERTY_VALUES'=> array(
                'YM_ID' => $model['id'],
                'YM_LINK' => $model['link']
            ),
        );

        foreach ($model['photos']['photo'] as $photo)
        {
            if ($photo['url'] != $model['mainPhoto']['url'])
            {
                $arFields['PROPERTY_VALUES']['PHOTOS'][] = Helper::makeFileArray($photo['url']);
            }
        }

        $rsElement = new \CIBlockElement;

        if ($elementId = $rsElement->Add($arFields))
        {
            // добавляем коментарии в товар
            foreach ($opinion as $comment) {
                $time = (int)$comment['date'] / 1000;
                $date = date($DB->DateFormatToPHP(CSite::GetDateFormat()), $time);

                 $text = '';
                 if ($comment['pro'] != '') {
                     $text .= 'Достоинства: '. $comment['pro'].'<br>';
                 }
                 if ($comment['contra'] != '') {
                     $text .= 'Недостатки: '. $comment['contra'].'<br>';
                 }
                 $text .= $comment['text'];

                $prop = array(
                    472 => $elementId,
                    473 => $date,
                    474 => $comment['author'],
                    476 => $comment['grade'] + 3
                );
                $field = array(
                    'IBLOCK_SECTION_ID' => false,
                    'PROPERTY_VALUES' => $prop,
                    'NAME' => $model['name'],
                    'PREVIEW_TEXT_TYPE' => 'html',
                    'PREVIEW_TEXT' => $text,
                    'IBLOCK_ID' => 38,
                    'ACTIVE' => 'N'
                );
                if (!$rsElement->Add($field)) {
                    echo "Error: ".$rsElement->LAST_ERROR;
                    die();
                }
            }


            // добавление элемента в каталог
            $arFields = array(
                'ID' => $elementId,
                'QUANTITY' => '25',
                'QUANTITY_RESERVED' => '5'
            );

            \CCatalogProduct::Add($arFields);

            // добавление цены
            $arFields = array(
               'PRODUCT_ID' => $elementId,
               'CATALOG_GROUP_ID' => 1,
               'PRICE' => $model['prices']['min'],
               'CURRENCY' => 'RUB',
            );

            \CPrice::Add($arFields);

        }
        else
        {
            $errors[] = $rsElement->LAST_ERROR;
        }

        // echo '<pre>';
        // var_dump($arFields);
        // var_dump($model);
        // echo '</pre>';
        // die;
        if (empty($errors))
        {
            \LocalRedirect($APPLICATION->getCurPage().'?lang='.LANG.'&ID='.$ID);
        }
    }
    else
    {
        $errors[] = 'Элемент уже есть в БД';
    }
}
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

$menu[] = array(
    'TEXT' => 'Яндекс.Маркет',
    'LINK' => $model['link']
);

if (empty($element))
{
    $menu[] = array(
        'ICON' => 'btn_new',
        'TEXT' => 'Добавить',
        'LINK' => 'ayers_ymarket_model.php?ID='.$ID.'&action=add&lang='.LANG.'&'.bitrix_sessid_get(),
    );
}

$context = new CAdminContextMenu($menu);
$context->Show();

$tabs = array();
$tabs[] =  array('DIV' => 'tab1', 'TAB' => 'Общая информация', 'TITLE' => Helper::mb_ucfirst($model['kind']).' '.$model['name']);
$tabs[] =  array('DIV' => 'tab2', 'TAB' => 'Фотографии', 'TITLE' => 'Фотографии для '.$model['name']);
$tabs[] =  array('DIV' => 'tab3', 'TAB' => 'Коментарии', 'TITLE' => 'Коментарии '.$model['name']);
$tabs[] =  array('DIV' => 'tab4', 'TAB' => 'Магазины', 'TITLE' => 'Магазины продающие '.$model['name']);

$control = new CAdminTabControl('control', $tabs, false);
?>

<div id="result-messages">
    <?if (!empty($errors)):?>
        <?$yApi->showErrors($errors);?>
    <?endif?>
</div>

<form method="POST" action="<?=$APPLICATION->GetCurPage(); ?>?lang=<?=LANG; ?>&amp;ID=<?=$ID; ?>" name="form1" enctype="multipart/form-data">
    <?=bitrix_sessid_post();?>

    <?$control->Begin();?>
    <?$control->BeginNextTab();?>
        <tr>
            <td>Название:</td>
            <td><?=$model['name']?></td>
        </tr>
        <tr>
            <td>Категория:</td>
            <td><?=$model['category']['name']?></td>
        </tr>
        <tr>
            <td>Произовдитель:</td>
            <td><?=$model['vendor']?></td>
        </tr>
        <tr>
            <td>Цена:</td>
            <td>от <?=$model['prices']['min']?> до <?=$model['prices']['max']?> <?=$model['prices']['curName']?></td>
        </tr>
        <tr>
            <td>Описание:</td>
            <td>
                <?if (count($offers)):?>
                    <?
                    // 5 первых описаний из предложений
                    $count = (count($offers['items']) < 10)? count($offers['items']): 10;
                    for ($i = 0; $i <= $count; $i++):?>
                        <?if ($offers['items'][$i]['description']):?>
                            <?=$offers['items'][$i]['description']?><br><br>
                        <?endif?>
                    <?endfor?>
                <?else:?>
                    <?=$model['description']?>
                <?endif?>

            </td>
        </tr>
    <?$control->EndTab();?>
    <?$control->BeginNextTab();?>

        <tr>
            <td>
                <?foreach ($model['previewPhotos'] as $photo):?>
                    <img src="<?=$photo['url']?>">
                <?endforeach?>
            </td>
        </tr>

    <?$control->EndTab();?>
    <?$control->BeginNextTab();?>
    <?// max 90 коментариев?>
        <? foreach ($opinion as $comment) {?>
            <tr>
                <td>Имя:</td>
                <td><?=$comment['author']?></td>
            </tr>
            <tr>
                <td>Дата:</td>
                <? $time = (int)$comment['date'] / 100 ?>
                <? $date = new DateTime('@'.$time);?>
                <td><?= $date->format('d.m.Y H:i:s')?></td>
            </tr>
            <tr>
                <td>Достоинства:</td>
                <td><?= $comment['pro']?></td>
            </tr>
            <tr>
                <td>Недостатки:</td>
                <td><?= $comment['contra']?></td>
            </tr>
            <tr>
                <td>Коментарий:</td>
                <td><?= $comment['text']?></td>
            </tr>
            <tr>
                <td><hr><br><br></td>
                <td><hr><br><br></td>
            </tr>
        <?}?>

    <?$control->EndTab();?>
    <?$control->BeginNextTab();?>
        <?foreach ($stores as $store):?>
            <tr>
                <td><?=$store['name']?></td>
                <td>
                    <?if ($store['url']):?>
                        <a href="<?=$store['url']?>" target="_blank"><?=$store['shopName']?></a>
                    <?else:?>
                        <?=$store['shopName']?>
                    <?endif?>
                </td>
                <td><?=$store['price']?> <?=$store['currency']?></td>
            </tr>
        <?endforeach?>
    <?$control->EndTab();?>
    <? $control->End(); ?>
</form>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>