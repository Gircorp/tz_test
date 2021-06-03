<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;

class ProductsComponent extends CBitrixComponent
{
    /**
     * кешируемые ключи arResult
     * @var array()
     */
    protected $cacheKeys = array();

    /**
     * дополнительные параметры, от которых должен зависеть кеш
     * @var array
     */
    protected $cacheAddon = array();

    /**
     * параметры постраничной навигации
     * @var array
     */
    protected $navParams = array();

    /**
     * возвращаемые значения
     * @var mixed
     */
    protected $returned;

    /**
     * тегированный кеш
     * @var mixed
     */
    protected $tagCache;

    /**
     * подключает языковые файлы
     */
    public function onIncludeComponentLang()
    {
        $this->includeComponentLang(basename(__FILE__));
        Loc::loadMessages(__FILE__);
    }

    /**
     * подготавливает входные параметры
     * @param array $arParams
     * @return array
     */
    public function onPrepareComponentParams($params)
    {
        $result = [
            'IBLOCK_TYPE' => trim($params['IBLOCK_TYPE']),
            'IBLOCK_ID' => intval($params['IBLOCK_ID']),
            'IBLOCK_CODE' => trim($params['IBLOCK_CODE']),
            'REVIEWS_HBLOCK_ID' => intval($params['REVIEWS_HBLOCK_ID']),
            'SHOW_NAV' => ($params['SHOW_NAV'] == 'Y' ? 'Y' : 'N'),
            'COUNT' => intval($params['COUNT']),
            'SORT_FIELD1' => strlen($params['SORT_FIELD1']) ? $params['SORT_FIELD1'] : 'ID',
            'SORT_DIRECTION1' => $params['SORT_DIRECTION1'] == 'ASC' ? 'ASC' : 'DESC',
            'SORT_FIELD2' => strlen($params['SORT_FIELD2']) ? $params['SORT_FIELD2'] : 'ID',
            'SORT_DIRECTION2' => $params['SORT_DIRECTION2'] == 'ASC' ? 'ASC' : 'DESC',
            'CACHE_TIME' => intval($params['CACHE_TIME']) > 0 ? intval($params['CACHE_TIME']) : 3600,
            'AJAX' => $params['AJAX'] == 'N' ? 'N' : $_REQUEST['AJAX'] == 'Y' ? 'Y' : 'N',
            'FILTER' => is_array($params['FILTER']) && sizeof($params['FILTER']) ? $params['FILTER'] : array(),
            'CACHE_TAG_OFF' => $params['CACHE_TAG_OFF'] == 'Y'
        ];
        return $result;
    }

    /**
     * определяет читать данные из кеша или нет
     * @return bool
     */
    protected function readDataFromCache()
    {
        global $USER;
        $cache_tag = 'ib_1';
        if ($this->arParams['CACHE_TYPE'] == 'N'){
            return false;
        }
        if($_GET['clear_cache'] == 'Y'){
            BXClearCache(true, "/".$cache_tag."/");
            return false;
        }
        if (is_array($this->cacheAddon))
            $this->cacheAddon[] = $USER->GetUserGroupArray();
        else
            $this->cacheAddon = array($USER->GetUserGroupArray());

        return !($this->startResultCache(false, $this->cacheAddon, $cache_tag));
    }

    /**
     * кеширует ключи массива arResult
     */
    protected function putDataToCache()
    {
        if (is_array($this->cacheKeys) && sizeof($this->cacheKeys) > 0)
        {
            $this->SetResultCacheKeys($this->cacheKeys);
        }
    }

    /**
     * прерывает кеширование
     */
    protected function abortDataCache()
    {
        $this->AbortResultCache();
    }

    /**
     * завершает кеширование
     * @return bool
     */
    protected function endCache()
    {
        if ($this->arParams['CACHE_TYPE'] == 'N')
            return false;

        $this->endResultCache();
    }

    /**
     * проверяет подключение необходиимых модулей
     * @throws LoaderException
     */
    protected function checkModules()
    {
        if (!Main\Loader::includeModule('iblock') || !Main\Loader::includeModule("highloadblock"))
            throw new Main\LoaderException(Loc::getMessage('STANDARD_ELEMENTS_LIST_CLASS_IBLOCK_MODULE_NOT_INSTALLED'));
    }

    /**
     * проверяет заполнение обязательных параметров
     * @throws SystemException
     */
    protected function checkParams()
    {
        if ($this->arParams['IBLOCK_ID'] <= 0 && strlen($this->arParams['IBLOCK_CODE']) <= 0)
            throw new Main\ArgumentNullException('IBLOCK_ID');
    }

    /**
     * выполяет действия перед кешированием
     */
    protected function executeProlog()
    {
        if ($this->arParams['COUNT'] > 0)
        {
            $cnt = \Bitrix\Iblock\ElementTable::getCount(['IBLOCK_ID' => $this->arParams['IBLOCK_ID']]);
            $nav = new \Bitrix\Main\UI\ReversePageNavigation("nav", $cnt);

            if ($this->arParams['SHOW_NAV'] == 'Y')
            {
                $nav->allowAllRecords(false)
                    ->setPageSize($this->arParams['COUNT'])
                    ->initFromUri();
                $this->navParams = $nav;
                $this->cacheAddon = array($nav);
            }
            else
            {
                $this->navParams = $nav->setPageSize($this->arParams['COUNT']);
            }
        }
        else
            $this->navParams = false;
    }

    /**
     * получение результатов
     */
    protected function getResult()
    {
        // Получаем отзывы к товарам
        $reviews = array();
        $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($this->arParams['REVIEWS_HBLOCK_ID'])->fetch();
        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $iterator = $entity_data_class::getList([
            "select" => ["*"],
            "order" => ["ID" => "ASC"],
            "filter" => ["UF_ACTIVE" => '1']
        ]);

        while($element = $iterator->fetch()){
            $reviews['PRODUCT_'.$element['UF_PRODUCT']][] = [
                'ID' => $element['ID'],
                'NAME' => $element['UF_NAME'],
                'SURNAME' => $element['UF_SURNAME'],
                'TEXT' => $element['UF_TEXT'],
                'ACTIVE' => $element['UF_ACTIVE'],
            ];
        }

        // Получаем товары
        $filter = [
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
            'ACTIVE' => 'Y'
        ];
        $sort = [
            $this->arParams['SORT_FIELD2'] => (($this->arParams['SORT_FIELD2'] == $this->arParams['SORT_FIELD1']) ? $this->arParams['SORT_DIRECTION1'] : $this->arParams['SORT_DIRECTION2'] )
        ];

        $iterator = \Bitrix\Iblock\ElementTable::getList([
            "order" => $sort,
            'filter' => $filter,
            "count_total" => true,
            "offset" => $this->navParams->getOffset(),
            "limit" => $this->navParams->getLimit(),
            //'cache' => ['ttl', $this->arParams['CACHE_TIME']],
        ]);

        while ($element = $iterator->fetch())
        {
            $this->arResult['ITEMS'][] = [
                'ID' => $element['ID'],
                'NAME' => $element['NAME'],
                'ACTIVE_FROM' => $element['ACTIVE_FROM'],
                'DESCRIPTION' => $element['PREVIEW_TEXT'],
                'REVIEWS' => $reviews['PRODUCT_'.$element['ID']],
                'REVIEWS_COUNT' => count($reviews['PRODUCT_'.$element['ID']]),
            ];
        }

        if($this->arParams['SORT_FIELD1'] == 'REVIEWS' && $this->arParams['SORT_DIRECTION1'] == 'DESC'){
            // Сортировка по отзывам по убыванию
            usort($this->arResult['ITEMS'], function ($item1, $item2) {
                return $item2['REVIEWS_COUNT'] <=> $item1['REVIEWS_COUNT'];
            });
        }elseif($this->arParams['SORT_FIELD1'] == 'REVIEWS' && $this->arParams['SORT_DIRECTION1'] == 'ASC'){
            // Сортировка по отзывам по возрастанию
            usort($this->arResult['ITEMS'], function ($item1, $item2) {
                return $item1['REVIEWS_COUNT'] <=> $item2['REVIEWS_COUNT'];
            });
        }

        if ($this->arParams['SHOW_NAV'] == 'Y' && $this->arParams['COUNT'] > 0)
        {
            $this->arResult['NAV_OBJECT'] = $this->navParams;
        }
    }

    /**
     * выполняет действия после выполения компонента, например установка заголовков из кеша
     */
    protected function executeEpilog()
    {
        if ($this->arResult['IBLOCK_ID'] && $this->arParams['CACHE_TAG_OFF'])
            \CIBlock::enableTagCache($this->arResult['IBLOCK_ID']);
    }

    /**
     * выполняет логику работы компонента
     */
    public function executeComponent()
    {
        global $APPLICATION;
        try
        {
            $this->checkModules();
            $this->checkParams();
            $this->executeProlog();
            if ($this->arParams['AJAX'] == 'Y')
                $APPLICATION->RestartBuffer();

            if (!$this->readDataFromCache())
            {
                $this->getResult();
                $this->putDataToCache();
                $this->includeComponentTemplate();
            }
            $this->executeEpilog();

            if ($this->arParams['AJAX'] == 'Y')
                die();

            return $this->returned;
        }
        catch (Exception $e)
        {
            $this->abortDataCache();
            ShowError($e->getMessage());
        }
    }
}
?>