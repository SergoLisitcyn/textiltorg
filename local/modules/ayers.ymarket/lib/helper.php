<?php
namespace Ayers\YMarket;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

class Helper {
    static public function getCategoriesOptions($arCategories, $current = 0, $level = 0)
    {
        $result = '';

        $prefix = '';

        $current = (empty($current))? 242704: $current; // todo: можно вынести в настройки

        for ($i=0; $i < $level; $i++)
        {
            $prefix .= ' &middot; &middot; &middot; ';
        }

        foreach ($arCategories as $arCategory)
        {
            $selected = ($current == $arCategory['YMARKET_ID'])? ' selected="selected"': '';
            $result .= '<option value="'.$arCategory['YMARKET_ID'].'"'.$selected.'>'.$prefix.$arCategory['NAME'].'</option>';

            if ($arCategory['CHILDRENS'])
            {
                $result .= self::getCategoriesOptions($arCategory['CHILDRENS'], $current, $level + 1);
            }
        }

        return $result;
    }

    public static function cmp($a, $b)
    {
        return strcmp($a['NAME'], $b['NAME']);
    }

    static public function getVendorOptions($arVendors, $current = 0)
    {
        usort($arVendors, array('\Ayers\YMarket\Helper','cmp'));

        $selected = (0 == $current)? ' selected="selected"': '';
        $result = '<option value="0"'.$selected.'>не учитывать</option>';

        foreach ($arVendors as $arVendor) {
            $selected = ($arVendor['VENDOR_YID'] == $current)? ' selected="selected"': '';
            $result .= '<option value="'.$arVendor['VENDOR_YID'].'"'.$selected.'>'.$arVendor['NAME'].'</option>';
        }

        return $result;
    }

    static public function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = false) {
        $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
        $str_end = "";
        if ($lower_str_end)
        {
            $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
        }
        else
        {
            $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
        }
        $str = $first_letter . $str_end;
        return $str;
    }

    static function searchElement($name)
    {
        if (empty($name))
        {
            throw new Exception('Не указана строка для поиска');
        }

        Loader::includeModule('iblock');

        $rsElements = \CIBlockElement::GetList(
            array(),
            array(
                'IBLOCK_ID' => 8 // можно вынести в настройки
            ),
            false,
            false,
            array('ID', 'NAME')
        );

        while ($arElement = $rsElements->GetNext())
        {
            if (preg_match('/'.$name.'/', $arElement['NAME']))
            {
                return $arElement['ID'];
            }
        }

        return false;
    }

    /**
     * Truncate
     * Обрезание текста для анонса по словам
     *
     * @param string
     * @param integer
     * @param string
     * @param string
     * @param boolean
     * @param boolean
     * @return string
     */
    static function Truncate($string, $length = 80, $etc = "...", $charset="UTF-8", $breakWords = false, $middle = false)
    {
        if ($length == 0)
            return "";

        if (strlen($string) > $length) {
            $length -= min($length, strlen($etc));
            if (!$breakWords && !$middle)
                $string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length+1, $charset));

            if(!$middle)
                return rtrim(mb_substr($string, 0, $length, $charset),".,?\s") . $etc;
            else
                return rtrim(mb_substr($string, 0, $length/2, $charset),".,?\s") . $etc . rtrim(mb_substr($string, -$length/2, $charset),".,?\s");
        } else
            return rtrim($string, ".,?\s");
    }

    static function makeFileArray($url)
    {
        $dir = $_SERVER["DOCUMENT_ROOT"].'/bitrix/tmp/ayers.ymarket/pictures/';
        $file = $dir.md5(rand(99999,99999999)).'.jpg';

        if (!file_exists($dir))
        {
            mkdir($dir, 0775, true);
        }

        file_put_contents($file, file_get_contents($url));

        $arFile = \CFile::MakeFileArray($file);
        $arFile['name'] = $arFile['name'].'.jpg';
        $arFile['tmp_name'] = $file;

        return $arFile;
    }
}

