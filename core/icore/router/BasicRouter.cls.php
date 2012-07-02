<?php
// namespace
namespace icore\router;

use icore\Router;

// basic router, deal with ?e=<entry>&l=<lang>&r=<region>&m=<module>&a=<action>
class BasicRouter extends Router
{
	public function parse()
	{
		// entry & lang
		$this->entry = isset($_GET['entry']) && !empty($_GET['entry']) ? strtolower(trim($_GET['entry'])) : DEFAULT_ENTRY;
		$this->lang = isset($_GET['lang']) && !empty($_GET['lang']) ? strtolower(trim($_GET['lang'])) : DEFAULT_LANG;

		// region & module & action
		$this->region = isset($_GET['r']) && !empty($_GET['r']) ? strtolower(trim($_GET['r'])) : DEFAULT_REGION;
		$this->module = isset($_GET['m']) && !empty($_GET['m']) ? strtolower(trim($_GET['m'])) : DEFAULT_MODULE;
		$this->action = isset($_GET['a']) && !empty($_GET['a']) ? strtolower(trim($_GET['a'])) : DEFAULT_ACTION;
	}	

	// args should be one dimension array
	public function makeUrl($region = null, $module = null, $action = null, array $args = array(), $entry = null, $lang = null)
	{
		// entry & lang
		if (is_null($entry)) $entry = $this->entry;
		$ret = $entry === DEFAULT_ENTRY ? '?' : sprintf("?entry=%s&", $entry);

		if (is_null($lang)) $lang = $this->lang;
		$ret .= $lang === DEFAULT_LANG ? '' : sprintf("lang=%s&", $lang);

		// region, module, action, args
		if (!empty($region))
		{
			$ret .= 'r=' . rawurlencode($region) . '&';
		}
		if (!empty($module))
		{
			$ret .= 'm=' . rawurlencode($module) .'&';
		}
		if (!empty($action))
		{
			$ret .= 'a=' . rawurlencode($action) . '&';
		}
		if (!empty($args))
		{
			foreach($args as $k => $v)
			{
				$ret .= $k . '=' . rawurlencode($v) . '&';
			}
		}

		// if driver name is not index.php
		return substr(DRIVER_URI, -9) === 'index.php' ? substr($ret, 0, -1) : DRIVER_URI . substr($ret, 0, -1);
	}
}

