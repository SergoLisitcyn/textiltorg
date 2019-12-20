<?php
namespace Ayers\Stores;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;

class StoresTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'ayers_stores_list';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            new Entity\StringField('TYPE', array(
                'required' => true,
            )),
            new Entity\StringField('CITY', array(
                'required' => true,
            )),
            new Entity\StringField('CODE'),
            new Entity\StringField('POSTCODE'),
            new Entity\StringField('ADDRESS', array(
                'required' => true,
            )),
            new Entity\StringField('SHORT_ADDRESS', array(
                'required' => true,
            )),
            new Entity\StringField('PHONE'),
            new Entity\StringField('EMAIL'),
            new Entity\StringField('TIME'),
            new Entity\StringField('POINTS'),
            new Entity\StringField('METRO'),
            new Entity\IntegerField('WEIGHT_LIMIT'),
            new Entity\IntegerField('SORT'),
            new Entity\DatetimeField('DATE_CREATE', array(
                'required' => true,
                'default_value' => new Type\DateTime
            )),
            new Entity\DatetimeField('DATE_UPDATE', array(
                'required' => true,
                'default_value' => new Type\DateTime
            )),
        );
    }
}