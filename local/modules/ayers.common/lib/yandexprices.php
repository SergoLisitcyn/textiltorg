<?php

namespace Ayers\Common;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;

class YandexPricesTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'ayers_yandex_prices';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array('primary' => true, 'autocomplete' => true)),
			new Entity\DateField('DATE', array('default_value' => new Type\DateTime)),
            new Entity\StringField('NAME'),
            new Entity\StringField('REGION'),
            new Entity\StringField('PRODUCT_ID'),
            new Entity\FloatField('PRICE'),
            new Entity\TextField('YA_OFFERS')
        );
    }
}