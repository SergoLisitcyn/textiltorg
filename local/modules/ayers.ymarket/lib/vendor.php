<?php
namespace Ayers\YMarket;
use Bitrix\Main\Entity;
use Bitrix\Main\Type;

class VendorTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'ayers_ymarket_vendor';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            new Entity\StringField('NAME', array(
                'required' => true,
            )),
            new Entity\StringField('UNIQ_NAME'),
            new Entity\IntegerField('YMARKET_ID', array(
                'required' => true,
            ))
        );
    }

    public static function addVendor($data) {
        $categories = $data['CATEGORIES'];
        unset($data['CATEGORIES']);
        $rez = self::add($data);
        if ($rez->isSuccess()) {
            if (!empty($categories)) {
                VendorCategoryTable::addVendorConnection($data['YMARKET_ID'], $categories);
            }
            return true;
        } else {
            return false;
        }
    }
}