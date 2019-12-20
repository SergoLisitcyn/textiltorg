<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\UI\Filter\Options as FilterOptions;
use Bitrix\Sender\MailingTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

Loc::loadMessages(__FILE__);

class SenderCampaignListComponent extends CBitrixComponent
{
	/** @var ErrorCollection $errors */
	protected $errors;

	protected function checkRequiredParams()
	{
		return true;
	}

	protected function initParams()
	{
		$this->arParams['PATH_TO_LIST'] = isset($this->arParams['PATH_TO_LIST']) ? $this->arParams['PATH_TO_LIST'] : '';
		$this->arParams['PATH_TO_USER_PROFILE'] = isset($this->arParams['PATH_TO_USER_PROFILE']) ? $this->arParams['PATH_TO_USER_PROFILE'] : '';
		$this->arParams['NAME_TEMPLATE'] = empty($this->arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $this->arParams["NAME_TEMPLATE"]);

		$this->arParams['GRID_ID'] = isset($this->arParams['GRID_ID']) ? $this->arParams['GRID_ID'] : 'SENDER_CAMPAIGN_GRID';
		$this->arParams['FILTER_ID'] = isset($this->arParams['FILTER_ID']) ? $this->arParams['FILTER_ID'] : $this->arParams['GRID_ID'] . '_FILTER';

		$this->arParams['RENDER_FILTER_INTO_VIEW'] = isset($this->arParams['RENDER_FILTER_INTO_VIEW']) ? $this->arParams['RENDER_FILTER_INTO_VIEW'] : '';
		$this->arParams['RENDER_FILTER_INTO_VIEW_SORT'] = isset($this->arParams['RENDER_FILTER_INTO_VIEW_SORT']) ? $this->arParams['RENDER_FILTER_INTO_VIEW_SORT'] : 10;

		$this->arParams['SET_TITLE'] = isset($this->arParams['SET_TITLE']) ? $this->arParams['SET_TITLE'] == 'Y' : true;
		$this->arParams['CAN_EDIT'] = isset($this->arParams['CAN_EDIT']) ? $this->arParams['CAN_EDIT'] : false;
	}

	protected function prepareResult()
	{
		$this->arResult['ERRORS'] = array();
		$this->arResult['ROWS'] = array();

		// set ui filter
		$this->setUiFilter();

		// set ui grid columns
		$this->setUiGridColumns();

		// create nav
		$nav = new PageNavigation("page");
		$nav->allowAllRecords(true)->setPageSize(10)->initFromUri();

		// get rows
		$list = MailingTable::getList(array(
			'select' => array(
				'ID', 'NAME', 'SORT', 'DATE_INSERT',
				'ACTIVE', 'IS_PUBLIC', 'SITE_ID'
			),
			'filter' => $this->getDataFilter(),
			'offset' => $nav->getOffset(),
			'limit' => $nav->getLimit(),
			'count_total' => true,
			'order' => array(
				'ID' => 'ASC'
			)
		));
		foreach ($list as $item)
		{
			// format user name
			$this->setRowColumnUser($item);

			$this->arResult['ROWS'][] = $item;
		}

		$this->arResult['TOTAL_ROWS_COUNT'] = $list->getCount();

		// set rec count to nav
		$nav->setRecordCount($list->getCount());
		$this->arResult['NAV_OBJECT'] = $nav;

		/* Set title */
		if ($this->arParams['SET_TITLE'])
		{
			/**@var CMain*/
			$GLOBALS['APPLICATION']->SetTitle(Loc::getMessage('SENDER_CAMPAIGN_COMP_TITLE'));
		}

		return true;
	}

	protected function getDataFilter()
	{
		$filterOptions = new FilterOptions($this->arParams['FILTER_ID']);
		$requestFilter = $filterOptions->getFilter($this->arResult['FILTERS']);

		$filter = array('IS_TRIGGER' => 'N');
		if (isset($requestFilter['NAME']) && $requestFilter['NAME'])
		{
			$filter['NAME'] = '%' . $requestFilter['NAME'] . '%';
		}
		if (isset($requestFilter['DATE_INSERT_from']) && $requestFilter['DATE_INSERT_from'])
		{
			$filter['>=DATE_INSERT'] = $requestFilter['DATE_INSERT_from'];
		}
		if (isset($requestFilter['DATE_INSERT_to']) && $requestFilter['DATE_INSERT_to'])
		{
			$filter['<=DATE_INSERT'] = $requestFilter['DATE_INSERT_to'];
		}

		return $filter;
	}

	protected function setUiGridColumns()
	{
		$this->arResult['COLUMNS'] = array(
			array(
				"id" => "ID",
				"name" => "ID",
				"default" => false
			),
			array(
				"id" => "DATE_INSERT",
				"name" => Loc::getMessage('SENDER_CAMPAIGN_COMP_UI_COLUMN_DATE_INSERT'),
				"default" => false
			),
			array(
				"id" => "NAME",
				"name" => Loc::getMessage('SENDER_CAMPAIGN_COMP_UI_COLUMN_NAME'),
				"default" => true
			),
			array(
				"id" => "CHAIN",
				"name" => Loc::getMessage('SENDER_CAMPAIGN_COMP_UI_COLUMN_CHAIN'),
				"default" => true
			),
			array(
				"id" => "RECIPIENT",
				"name" => Loc::getMessage('SENDER_CAMPAIGN_COMP_UI_COLUMN_RECIPIENT'),
				"default" => true
			),
			array(
				"id" => "STAT",
				"name" => Loc::getMessage('SENDER_CAMPAIGN_COMP_UI_COLUMN_STAT'),
				"default" => true
			),
		);
	}

	protected function setUiFilter()
	{
		$this->arResult['FILTERS'] = array(
			array(
				"id" => "NAME",
				"name" => Loc::getMessage('SENDER_CAMPAIGN_COMP_UI_COLUMN_NAME'),
				"default" => true
			),
			array(
				"id" => "DATE_INSERT",
				"name" => Loc::getMessage('SENDER_CAMPAIGN_COMP_UI_COLUMN_DATE_INSERT'),
				"type" => "date",
				"default" => true
			),
		);
	}

	protected function setRowColumnUser(array &$data)
	{
		$data['USER'] = '';
		$data['USER_PATH'] = '';
		if (!$data['USER_ID'])
		{
			return;
		}

		$data['USER_PATH'] = str_replace('#id#', $data['USER_ID'], $this->arParams['PATH_TO_USER_PROFILE']);
		$data['USER'] = \CUser::FormatName(
			$this->arParams['NAME_TEMPLATE'],
			array(
				'LOGIN' => $data['USER_LOGIN'],
				'NAME' => $data['USER_NAME'],
				'LAST_NAME' => $data['USER_LAST_NAME'],
				'SECOND_NAME' => $data['USER_SECOND_NAME']
			),
			true, false
		);
	}

	protected function printErrors()
	{
		foreach ($this->errors as $error)
		{
			ShowError($error);
		}
	}

	public function executeComponent()
	{
		$this->errors = new \Bitrix\Main\ErrorCollection();
		$this->initParams();
		if (!$this->checkRequiredParams())
		{
			$this->printErrors();
			return;
		}

		if (!$this->prepareResult())
		{
			$this->printErrors();
			return;
		}

		$this->includeComponentTemplate();
	}
}