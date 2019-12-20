<?php

$aMenu = array(
    'parent_menu' => 'global_menu_store',
    'section' => 'catalog',
    'sort' => 5000,
    'text' => 'Импорт товаров из Yandex.Market',
    'title' => 'Инструмент для импорта товаров из Yandex.Market на сайт',
    'icon' => 'ayers_ymarket_menu_icon',
    'page_icon' => 'catalog_page_icon',
    'items_id' => 'ayers_stores_list',
    'url' => 'ayers_ymarket_import.php?lang='.LANGUAGE_ID,
    'more_url' => array('ayers_ymarket_statistics.php')
);

return $aMenu;