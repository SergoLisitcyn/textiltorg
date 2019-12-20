<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\HttpApplication;

$module_id = 'ayers.ymarket'; //обязательно, иначе права доступа не работают!

Loc::loadMessages($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/main/options.php');
Loc::loadMessages(__FILE__);

if ($APPLICATION->GetGroupRight($module_id) < 'S')
{
    $APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

Loader::includeModule($module_id);


$request = HttpApplication::getInstance()->getContext()->getRequest();

#Описание опций

$aTabs = array(
    array(
        'DIV' => 'edit1',
        'TAB' => 'Настройки',
        'TITLE' => 'Основные настройки',
        'OPTIONS' => array(
            array('YAMARKET_KEY', 'Авторизационный ключ:', '', array('text', 45)),
            array('REGION_ID', 'Идентификатор региона:', '10001', array('text', 5)),
        )
    ),
);

#Сохранение

if ($request->isPost() && $request['Update'] && check_bitrix_sessid())
{

    foreach ($aTabs as $aTab)
    {
        //Или можно использовать __AdmSettingsSaveOptions($module_id, $arOptions);
        foreach ($aTab['OPTIONS'] as $arOption)
        {
            if (!is_array($arOption)) //Строка с подсветкой. Используется для разделения настроек в одной вкладке
                continue;

            if ($arOption['note']) //Уведомление с подсветкой
                continue;


            //Или __AdmSettingsSaveOption($module_id, $arOption);
            $optionName = $arOption[0];

            $optionValue = $request->getPost($optionName);

            Option::set($module_id, $optionName, is_array($optionValue) ? implode(',', $optionValue):$optionValue);
        }
    }
}

#Визуальный вывод

$tabControl = new CAdminTabControl('tabControl', $aTabs);
?>
<?$tabControl->Begin()?>
<form method='post' action='<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($request['mid'])?>&amp;lang=<?=$request['lang']?>' name='ayers_referal_settings'>

    <?foreach ($aTabs as $aTab):?>
        <?if($aTab['OPTIONS']):?>
            <? $tabControl->BeginNextTab(); ?>
            <? __AdmSettingsDrawList($module_id, $aTab['OPTIONS']); ?>
        <?endif?>
    <?endforeach?>

    <?
    $tabControl->BeginNextTab();
    $tabControl->Buttons();
    ?>

    <input type='submit' name='Update' value='<?echo GetMessage('MAIN_SAVE')?>'>
    <input type='reset' name='reset' value='<?echo GetMessage('MAIN_RESET')?>'>
    <?=bitrix_sessid_post();?>
</form>
<?$tabControl->End()?>