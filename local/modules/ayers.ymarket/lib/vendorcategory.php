<?php
namespace Ayers\YMarket;

use Bitrix\Main\Entity;

class VendorCategoryTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'ayers_ymarket_vendor_category';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            new Entity\IntegerField('VENDOR_YID', array(
                'required' => true,
            )),
            new Entity\ReferenceField(
                'VENDOR',
                'Ayers\YMarket\VendorTable',
                array('=this.VENDOR_YID' => 'ref.YMARKET_ID')
            ),
            new Entity\IntegerField('CATEGORY_YID', array(
                'required' => true,
            )),
            new Entity\ReferenceField(
                'CATEGORY',
                'Ayers\YMarket\CategoriesTable',
                array('=this.CATEGORY_YID' => 'ref.YMARKET_ID')
            )
        );
    }
    public static function addVendorConnection($vendorId, $categoryList) {
        if (!empty($vendorId)) {
            foreach ($categoryList as $categoriId) {
                self::add(array(
                    'VENDOR_YID' => $vendorId,
                    'CATEGORY_YID' => $categoriId
                ));
            }
        }
    }
    public static function getVendorByCategory($categoryId)
    {
        if (!$categoryId > 0) {
            $categoryId = 242704;
        }
        return self::getList(
            array(
                'select' => array('ID', 'VENDOR_YID', 'NAME' => 'VENDOR.NAME'),
                'filter' => array('CATEGORY_YID' => $categoryId)
            )
        )->fetchAll();
    }
}