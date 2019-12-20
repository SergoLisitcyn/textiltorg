<?php
namespace Ayers\YMarket;
use Bitrix\Main\Entity;
use Bitrix\Main\Type;

class StatisticsTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'ayers_ymarket_statistics';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            new Entity\DatetimeField('DATE', array(
                'required' => true,
                'default_value' => new Type\DateTime
            )),
            new Entity\StringField('METHOD', array(
                'required' => true,
            )),
        );
    }
}