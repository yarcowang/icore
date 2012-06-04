<?php
// namespace
namespace icore;

/**
 * basic module class
 */
class Module 
{
	// saved data
	protected $_data = array();

	// components
	protected $_app;

	// from router
	protected $_entry;
	protected $_lang;
	protected $_region;
	protected $_module;
	protected $_action;

	public function __construct()
	{
		$this->_app = Application::get();

		// router variables
		$this->_entry = & $this->_app->router->entry;
		$this->_lang = & $this->_app->router->lang;
		$this->_region = & $this->_app->router->region;
		$this->_module = & $this->_app->router->module;
		$this->_action = & $this->_app->router->action;
	}

	public function __destruct()
	{
		// extra header for debugging
		header('iCore-action: region=' . $this->_region . ' module=' . $this->_module . ' action=' . $this->_action);
		header('iCore-time-cost: ' . Utilities::timer());
		if ($this->_app->state != ICORE_INSTALL_PHRASE)
		{
			header('iCore-sqls-executed: ' . count($this->_app->db->sqls));
			if ($this->_app->vars['sitemode'] === 'devel')
			{
				foreach($this->_app->db->sqls as $k => $sql)
				{
					header('iCore-sql-executed' . $k . ': ' . str_replace("\n", ' ', $sql));
				}
			}
		}

		switch ($this->_app->router->entry)
		{
			case 'dummy':
				return;
			case 'json':
				print json_encode($this->_data);
				return;
			case 'cli':
				var_dump($this->_data);
				return;
			case 'xml':
				header('Content-Type: text/xml');
				break;
			case 'html':
			default:
				;
		}

		$pc = PageCache::get();
		if (!$pc->isCached())
		{
			// TODO: get views from database

			// page view
			$view = new \icore\views\PageView;
			$view->setData(array('regions' => $this->_data));
			$contents = $view->get_contents();
			$pc->saveCache($contents);
		}
		print $pc->get_contents();
	}

	/**
	 * add a view to a region
	 *
	 * @param integer region id
	 * @param object an object of icore\Views
	 * return object just added
	 */
	protected function addView($region, Views $view)
	{
		if (!isset($this->_data[$region]))
		{
			$this->_data[$region] = array();
		}

		$v = & $this->_data[$region][];
		$v = $view;
		return $v;
	}

	/**
	 * support gridedit
	 *
	 * you should create a method 'gridedit' to call this method
	 */
	protected function gridedit()
	{
		// clean data for json
		$this->_data = array();
		$this->_data['result'] = 1;
		$this->_data['data'] = '';
		$this->_data['message'] = 'unknown';

		// model
		$model = new Model($_POST['model_name']);

		switch($_POST['op'])
		{
			case 'del':
				$wheres = array();
				$wheres['id = ?'] = $_POST['id'];

				if (!$ret = $model->remove($wheres))
				{
					$this->_data['result'] = 0;
					$this->_data['message'] = '';
				}
				else
				{
					$this->_data['message'] = $ret;
				}
				break;	
			case 'edit':
				$data = array();
				$data['id'] = $_POST['id'];

				// check field name
				if (!in_array($_POST['name'], $model->fields))
				{
					$this->_data['message'] = 'error field name: ' . $_POST['name'];
					return;
				}

				$data[$_POST['name']] = $_POST['value'];
				if (!$ret = $model->replace($data))
				{
					$this->_data['result'] = 0;
					$this->_data['message'] = '';
				}
				else
				{
					$this->_data['message'] = $ret;
				}
				break;
			default:
				$this->_data['message'] = 'no such op';
		}
	}
}

