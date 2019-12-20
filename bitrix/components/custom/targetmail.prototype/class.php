<?php
use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use \Bitrix\Main\Context;

class TargetMail extends CBitrixComponent
{
    private $productid;
    private $pagetype;
    private $totalvalue;
    private $list;

    private $data;

    private function formatParams()
    {
        $this->arParams['TOTAL_VALUE'] = $this->formatFloat($this->arParams['TOTAL_VALUE']);
    }

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
            $this->pagetype = 'home';
        }

        elseif (!empty($this->arParams['PAGE_PRODUCT']) &&
            ($this->arParams['PAGE_PRODUCT'] == $server->get('REAL_FILE_PATH') ||
            preg_match($this->arParams['PAGE_PRODUCT'], $request->getRequestedPage())))
        {
            $this->pagetype = 'product';
        }

        elseif (!empty($this->arParams['PAGE_CATEGORY']) &&
            ($this->arParams['PAGE_CATEGORY'] == $server->get('REAL_FILE_PATH') ||
            preg_match($this->arParams['PAGE_CATEGORY'], $request->getRequestedPage())))
        {
            $this->pagetype = 'category';
        }

        elseif (!empty($this->arParams['PAGE_CART']) &&
            ($this->arParams['PAGE_CART'] == $server->get('REAL_FILE_PATH') ||
            preg_match($this->arParams['PAGE_CART'], $request->getRequestedPage())))
        {
            $this->pagetype = 'cart';
        }

        elseif (!empty($this->arParams['PAGE_PURCHASE']) &&
            ($this->arParams['PAGE_PURCHASE'] == $server->get('REAL_FILE_PATH') ||
            preg_match($this->arParams['PAGE_PURCHASE'], $request->getRequestedPage())))
        {
            $this->pagetype = 'purchase';
        }

        $this->productid = (!empty($this->arParams['PRODUCT_ID']))? $this->arParams['PRODUCT_ID']: '';
        $this->totalvalue = (!empty($this->arParams['TOTAL_VALUE']))? $this->arParams['TOTAL_VALUE']: '';
        $this->list = (!empty($this->arParams['LIST']))? $this->arParams['LIST']: '1';

        $this->data = (object) array(
            'type' => 'itemView',
            'productid' => (!empty($this->productid)) ? $this->productid : '',
            'pagetype' => (!empty($this->pagetype)) ? $this->pagetype : 'other',
            'totalvalue' => (!empty($this->totalvalue)) ? $this->totalvalue : '',
            'list' => (!empty($this->list)) ? $this->list : '',
        );
    }

    public function executeComponent()
    {
        if (defined('IS_SHOW_TARGET_MAIL'))
        {
            return;
        }

        $this->includeComponentLang('class.php');

        $this->formatParams();
        $this->getData();
        $this->arResult['ID'] = (!empty($this->arParams['ID']))? $this->arParams['ID'] : 0;
        $this->arResult['DATA'] = $this->data;
        $this->arResult['JSON'] = json_encode($this->data);

        define('IS_SHOW_TARGET_MAIL', true);
        $this->includeComponentTemplate();
    }
};