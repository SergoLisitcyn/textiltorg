<?php

$aMenu = array(
    'parent_menu' => 'global_menu_store',
    'section' => 'catalog',
    'sort' => 5000,
    'text' => 'Пункты самовывоза',
    'title' => 'Пункты самовывоза СДЭК и ПЭК',
    'icon' => 'ayers_stores_menu_icon',
    'page_icon' => 'catalog_page_icon',
    'items_id' => 'ayers_stores_list',
    'url' => 'ayers_stores_list.php?lang='.LANGUAGE_ID,
    'more_url' => array('ayers_stores_edit.php')
);

return $aMenu;