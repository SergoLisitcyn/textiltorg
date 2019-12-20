<?php
namespace Ayers\YMarket;
use Bitrix\Main\Entity;
use Bitrix\Main\Type;

class CategoriesTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'ayers_ymarket_categories';
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
            )),
            new Entity\IntegerField('PARENT_ID', array(
                'required' => true,
            )),
            new Entity\IntegerField('IS_CHILDREN')
        );
    }

    public static function getListCategories($id, $isChildren = true)
    {
        $result = array();

        $rsCategories = self::getList(array(
            'filter' => array(
                'PARENT_ID' => $id
            )
        ));

        while ($arCategory = $rsCategories->fetch())
        {
            if ($arCategory['IS_CHILDREN'] && $isChildren)
            {
                $arCategory['CHILDRENS'] = self::getListCategories($arCategory['YMARKET_ID'], $isChildren);
            }

            $result[] = $arCategory;
        }

        return $result;
    }
}