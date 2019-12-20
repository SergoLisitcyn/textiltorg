<?

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

$MODULE_ID = 'bxmaker.smsnotice';
$MODULE_CODE = 'bxmaker_smsnotice';

$moduleSort = 10000;
$i=0;
$MOD_RIGHT = $APPLICATION->GetGroupRight($MODULE_ID);

if($MOD_RIGHT > "D") 
{
  $aMenu = array(
    "parent_menu" => "global_menu_settings", // поместим в раздел "Контент"
    "sort"        => $moduleSort, 
    "section"     => $MODULE_ID,             // вес пункта меню
    "url"         => '/bitrix/admin/' . $MODULE_ID.'_list.php?lang='.LANGUAGE_ID,
    "text"        => GetMessage($MODULE_CODE.'_MAIN_MENU_LINK_NAME'),       // текст пункта меню
    "title"       => GetMessage($MODULE_CODE.'_MAIN_MENU_LINK_DESCRIPTION'), // текст всплывающей подсказки
    "icon"        => $MODULE_CODE.'_icon', // малая иконка
    "page_icon"   => $MODULE_CODE.'_page_icon', // большая иконка
    "items_id"    => $MODULE_CODE.'_main_menu_items',  // идентификатор ветви
    "items"       => array()          
  );
  
        if (file_exists($path = dirname(__FILE__)))
		{
			if ($dir = opendir($path))
			{
				$arFiles = array();

				while(false !== $item = readdir($dir))
				{
                    if(is_dir($path . '/' . $item)) continue;
					if (in_array($item,array('.','..','menu.php',"main.php", 'edit.php', 'template_edit.php', 'template_type_edit.php', 'service_edit.php', 'user_date_bsm.php'))) continue;
                    $arFiles[] = $item;
				}

				sort($arFiles);
                $i++;
				foreach($arFiles as $item)
					$aMenu['items'][] = array(
						'url' => '/bitrix/admin/' . $MODULE_ID.'_'.$item.'?lang='.LANGUAGE_ID,
						'more_url' => array(
                            '/bitrix/admin/' . $MODULE_ID.'_'.str_replace('list.php', 'edit.php', $item).'?lang='.LANGUAGE_ID
                        ),
						'module_id' => $MODULE_ID,
						'text' => GetMessage($MODULE_CODE.'_'.substr($item,0,strpos($item,'.')).'_MENU_LINK_NAME'),
						"title" => GetMessage($MODULE_CODE.'_'.substr($item,0,strpos($item,'.')).'_MENU_LINK_DESCRIPTION'),
                        //"icon"        => $MODULE_CODE.'_'.$item.'_icon', // малая иконка
                       // "page_icon"   => $MODULE_CODE.'_'.$item.'_page_icon', // большая иконка
                        'sort' => $moduleSort+$i,
					);
			}
		}

    $aMenu['items'][] = array(
        'url'       => '/bitrix/admin/settings.php?lang='.LANGUAGE_ID.'&mid=' . $MODULE_ID . '&mid_menu=1',
        'more_url'  => array(),
        'module_id' => $MODULE_ID,
        'text'      => GetMessage($MODULE_CODE . '_OPTIONS_MENU_LINK_NAME'),
        "title"     =>GetMessage($MODULE_CODE . '_OPTIONS_MENU_LINK_NAME'),
        'sort'      => $moduleSort + $i,
    );

		$aModuleMenu[] = $aMenu;
  return $aModuleMenu;
}
return false;
