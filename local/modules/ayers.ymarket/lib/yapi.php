<?php
namespace Ayers\YMarket;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Type;

class YApi {
    public $errors = array();
    public $modelsCategory = false;

    const MODULE_ID = 'ayers.ymarket';
    const CACHE_TIME = 2592000;
    const REGION_ID = 1; // Москва

    public function getAllCategories($id = false, $usleep = 150000)
    {
        $result = array();

        if (!empty($usleep))
        {
            usleep($usleep);
        }

        if (empty($id))
        {
            $arCategories = $this->getCategories();
        }
        else
        {
            $arCategories = $this->getCategoriesChildren($id);
        }

        foreach ($arCategories as $arCategory)
        {
            $result[$arCategory['id']] = $arCategory;

            if ($arCategory['childrenCount'])
            {
                $result += $this->getAllCategories($arCategory['id'], $usleep);
            }
        }

        return $result;
    }

    public function getAllVendors($usleep = 150000)
    {
        $result = array();
        $page = 1;

        do{
            $rest = $this->rest('vendor', array(
                'page' => $page,
                'count' => 30
            ));

            foreach ($rest['data']['getVendorList']['vendor'] as $vendor) {
                $result[] = array(
                    'NAME' => $vendor['name'],
                    'YMARKET_ID' => $vendor['id'],
                    'CATEGORIES' => $this->getVendorCategoriesList($vendor['categories'])
                );
            }
            $currentNum = $page * 30;
            $allNum = (int)$rest['data']['getVendorList']['total'];

            $page++;


            if (!empty($usleep))
            {
                usleep($usleep);
            }
        } while ($currentNum < $allNum);
        //} while ($page < 100);

        return $result;

    }

    protected function getVendorCategoriesList($ar) {
        $result = array();
        foreach ($ar as $category) {
            $result[] = $category['id'];
            if (!empty($category['innerCategories'])) {
                $result = array_merge($result, $this->getVendorCategoriesList($category['innerCategories']));
            }
        }
        return $result;
    }

    public function getCategories()
    {
        $result = array();

        $rest = $this->rest('category', array(
            'geo_id' => self::REGION_ID,
            'count' => 30
        ));
        return $rest['data']['categories']['items'];
    }

    public function getRegions()
    {
        $result = array();

        $rest = $this->rest('georegion', array(
            'count' => 30
        ));

        return $rest['data']['categories']['items'];
    }

    public function getRegionsChlidren($id)
    {
        $result = array();

        $rest = $this->rest('georegion/'.$id.'/children', array(
            'count' => 30
        ));

        return $rest['data']['categories']['items'];
    }

    public function getCategoriesChildren($id)
    {
        if (empty($id))
        {
            throw new Exception('Не указан идентификатор');
        }

        $result = array();

        $rest = $this->rest('category/'.$id.'/children', array(
            'geo_id' => self::REGION_ID,
            'count' => 30
        ));

        return $rest['data']['categories']['items'];
    }

    public function rest($method, $params = array(), $version = 1)
    {
        if (empty($method))
        {
            throw new Exception('Не указан метод для запроса');
        }

        $isCache = false;

        $key = Option::get(self::MODULE_ID, 'YAMARKET_KEY');

        $hash = serialize(array($method,$params));
        //$сache = Cache::createInstance();
        $сache = new \CPHPCache;

        if ($сache->InitCache(self::CACHE_TIME, $hash, '/'.self::MODULE_ID.'/rest/'))
        {
            $vars = $сache->GetVars();
            $data = $vars['data'];
            //\Bitrix\Main\Diag\Debug::dumpToFile('cash');
        }
        elseif ($сache->StartDataCache())
        {
            if (!function_exists('curl_init'))
            {
                throw new Exception('Не установлен curl');
            }

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://api.content.market.yandex.ru/v'.$version.'/'.$method.'.json?'.http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: '.$key
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $data = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            $сache->endDataCache(array('data' => $data));

            $rsStatistics = StatisticsTable::add(array(
                'DATE' => new Type\Datetime(date('Y-m-d H:i:s'), 'Y-m-d H:i:s'),
                'METHOD' => $method
            ));

            if (!$rsStatistics->isSuccess())
            {
                throw new Exception(join('<br>', $rsStatistics->getErrorMessages()));
            }

            //\Bitrix\Main\Diag\Debug::dumpToFile('rest');
        }

        $result = array(
            'data' => json_decode($data, true),
            'type' => ($isCache) ? 'cache' : 'rest'
        );


        if ($result['data']['errors'])
        {
            $errors = $this->decodeErrors($httpcode);
            $this->errors = ($errors)? array($errors): $result['errors'];
        }

        //\Bitrix\Main\Diag\Debug::dumpToFile($result);

        if (empty($result['data']))
        {
            //throw new Exception('Нет данных от Yandex.Market');
        }

        return $result;
    }

    private function decodeErrors($httpcode = 200)
    {
        $errors = array(
            '400' => 'Невалидный запрос',
            '401' => array(
                'Ошибка авторизации, возможные причины:',
                '<ul><li>В <a href="settings.php?mid=ayers.ymarket&lang='.\LANG.'">настройках</a> не указан авторизационный ключ или он не валидный</li><li>IP-адрес, с которого был отправлен запрос, отсутствует в списке IP-адресов, указанных при получении авторизационного ключа</li></ul>'
            ),
            '403' => 'Превышено ограничение на доступ к ресурсам'
        );

        return $errors[$httpcode];
    }

    public function showErrors($errors = array())
    {
        $errors = (!empty($errors))? $errors: $this->errors;

        foreach ($errors as $error)
        {
            $fields = array(
                'TYPE' => 'ERROR',
                'HTML' => true
            );

            if (is_array($error))
            {
                $fields['MESSAGE'] = $error[0];
                $fields['DETAILS'] = $error[1];
            }
            else
            {
                $fields['MESSAGE'] = $error;
            }

            $message = new \CAdminMessage($fields);

            echo $message->Show();
        }
    }

    public function getModels($modelsCategory = false, $modelsVendor = false, $pagePath = 1, $usleep = 150000)
    {

        $modelsCategory = (!empty($modelsCategory))? $modelsCategory : $this->modelsCategory;

        if (empty($modelsCategory))
        {
            throw new Exception('Не указана категория для моделей');
        }

        $result = array();
        if (empty($pagePath)) {
            $pagePath = 1;
        }

        $page = ($pagePath * 3) - 2;

        $fields = array(
            'geo_id' => self::REGION_ID,
            'count' => 30,
        );

        if ($modelsVendor) {
            $fields['vendor_id'] = $modelsVendor;
        }

        do {
            $fields['page'] = $page;
            $rest = $this->rest('category/'.$modelsCategory.'/models', $fields);

            $result = array_merge($result, $rest['data']['models']['items']);
            $curCount = 30 * $page;
            $allCount = $rest['data']['models']['total'];
            $page++;

        } while($page <= $pagePath * 3 && $allCount > $curCount);
        $result['item'] = $result;
        $result['total'] = $allCount;
        $result['current'] = $curCount;
        return $result;
    }

    public function getModel($id)
    {
        if (empty($id))
        {
            throw new Exception('Не указан идентификатор модели');
        }

        $rest = $this->rest('model/'.$id, array(
            'geo_id' => self::REGION_ID,
            'model_id' => $id
        ));

        return $rest['data']['model'];
    }
    public function getModelComment($id)
    {
        if (empty($id))
        {
            throw new Exception('Не указан идентификатор модели');
        }

        $rest = $this->rest('model/'.$id, array(
            'geo_id' => self::REGION_ID,
            'model_id' => $id
        ));

        return $rest['data']['model'];
    }

    public function getModelOffers($id)
    {
        if (empty($id))
        {
            throw new Exception('Не указан идентификатор модели');
        }

        $rest = $this->rest('model/'.$id.'/offers', array(
            'geo_id' => self::REGION_ID,
            'model_id' => $id,
            'fields' => 'FILTERS'
        ));

        return $rest['data']['offers'];
    }

    public function getOutlets($id, $page = 1, $usleep = 150000)
    {
        if (empty($id))
        {
            throw new Exception('Не указан идентификатор модели');
        }

        $result = array();

        $rest = $this->rest('model/'.$id.'/outlets', array(
            'geo_id' => self::REGION_ID,
            'count' => 30,
            'type' => 'store',
            'page' => $page
        ));

        if (!empty($usleep) && $rest['type'] != 'cache')
        {
            usleep($usleep);
        }

        foreach ($rest['data']['outlets']['outlet'] as $item)
        {
            $result[$item['offer']['id']] = $item;
        }

        if ($page == 1)
        {
            $pages = ceil($rest['data']['outlets']['total'] / 30);
            $pages = ($pages <= 5)? $pages: 5;

            if ($pages > 1)
            {
                for ($i = 2; $i <= $pages; $i++)
                {
                    //\Bitrix\Main\Diag\Debug::dumpToFile($i.'/'.$pages);
                    # цикл по запросам и страницам
                    $result += self::getOutlets($id, $i, $pages);
                }
            }
        }

        return $result;
    }

    public function getStores($id)
    {
        $outlets = $this->getOutlets($id);

        $storesHash = array();
        $stores = array();
        foreach ($outlets as $outlet)
        {
            $store = array(
                'name' => $outlet['offer']['name'],
                'shopName' => $outlet['offer']['shopInfo']['shopName'],
                'price' => $outlet['offer']['price']['value'],
                'currency' => $outlet['offer']['price']['currencyName']
            );

            $hash = md5($store['name'].$store['shopName'].$store['price'].$store['currency']);

            $url = $outlet['offer']['shopInfo']['url'];

            if ($url)
            {
                $store['url'] = $url;
            }

            if (!in_array($hash, $storesHash))
            {
                $stores[] = $store;
                $storesHash[] = $hash;
            }
        }

        return $stores;
    }

    function getOpinion($id)
    {
        if (empty($id))
        {
            throw new Exception('Не указан идентификатор модели');
        }
        $result = array();
        $page = 1;
        do {
            $rest = $this->rest('model/'.$id.'/opinion', array(
                'count' => 30,
                'page' => $page
            ));
            $result = array_merge($result, $rest['data']['modelOpinions']['opinion']);
            $curCoutn = 30 * $page;
            $allCount = $rest['data']['modelOpinions']['total'];
        } while($page <= 3 && $allCount > $curCoutn);

        return $result;
    }
}
