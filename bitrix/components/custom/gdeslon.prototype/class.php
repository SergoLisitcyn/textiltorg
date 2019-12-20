<?php
use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use \Bitrix\Main\Context;

class GdeSlon extends CBitrixComponent
{

    private $pagetype;
    private $data;

    private function formatFloat($price)
    {
        $price = preg_replace('/\,/', '.', $price);
        $price = preg_replace('/[^\d\.]+/', '', $price);
        return $price;
    }

    public function getData()
    {
        $request = Context::getCurrent()->getRequest();
        $server = Context::getCurrent()->getServer();

        if ($request->getRequestedPageDirectory() == '')
        {
            $this->pagetype = 'main';
        }

        elseif (!empty($this->arParams['PAGE_PRODUCT']) &&
            ($this->arParams['PAGE_PRODUCT'] == $server->get('REAL_FILE_PATH') ||
            preg_match($this->arParams['PAGE_PRODUCT'], $request->getRequestedPage())))
        {
            $this->pagetype = 'card';
        }

        elseif (!empty($this->arParams['PAGE_CATEGORY']) &&
            ($this->arParams['PAGE_CATEGORY'] == $server->get('REAL_FILE_PATH') ||
            preg_match($this->arParams['PAGE_CATEGORY'], $request->getRequestedPage())))
        {
            $this->pagetype = 'list';
        }

        elseif (!empty($this->arParams['PAGE_CART']) &&
            ($this->arParams['PAGE_CART'] == $server->get('REAL_FILE_PATH') ||
            preg_match($this->arParams['PAGE_CART'], $request->getRequestedPage())))
        {
            $this->pagetype = 'basket';
        }

        elseif (!empty($this->arParams['PAGE_SEARCH']) &&
            ($this->arParams['PAGE_SEARCH'] == $server->get('REAL_FILE_PATH') ||
            preg_match($this->arParams['PAGE_SEARCH'], $request->getRequestedPage())))
        {
            $this->pagetype = 'search';
        }

        elseif (!empty($this->arParams['PAGE_PURCHASE']) &&
            ($this->arParams['PAGE_PURCHASE'] == $server->get('REAL_FILE_PATH') ||
                preg_match($this->arParams['PAGE_PURCHASE'], $request->getRequestedPage())))
        {
            $this->pagetype = 'thanks';
        }

        $this->data = (object) array(
            'mid' => $this->arParams['MID'],
            'codes' => $this->arParams['CODES'],
            'cat_id' => $this->arParams['CAT_ID'],
            'pagetype' => (!empty($this->pagetype)) ? $this->pagetype : 'other',
        );
    }

    public function executeComponent()
    {
        if (defined('IS_SHOW_GDE_SLON'))
        {
            return;
        }

        $this->includeComponentLang('class.php');

        $this->getData();

        $this->arResult['MID'] = $this->data->mid;
        $this->arResult['CODES'] = $this->data->codes;
        $this->arResult['CAT_ID'] = $this->data->cat_id;
        $this->arResult['PAGE_TYPE'] = $this->data->pagetype;

        define('IS_SHOW_GDE_SLON', true);
        $this->includeComponentTemplate();
    }
};