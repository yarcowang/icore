<?php
// namespace 
namespace icore\views;

use icore\Views;
use icore\Model;

class PageView extends Views
{
	public function __construct()
	{
		// get app
		$app = \icore\Application::get();	

		// router related
		$this->region = $app->router->region;
		$this->module = $app->router->module;
		$this->action = $app->router->action;
		$this->entry = $app->router->entry;
		$this->lang = $app->router->lang;

		// theme related
		$this->theme_app = isset($app->vars['theme_admin']) && !empty($app->vars['theme_admin']) ? $app->vars['theme_admin'] : 'default';
		$this->theme_sys = isset($app->vars['theme']) && !empty($app->vars['theme']) ? $app->vars['theme'] : 'default';
		$this->theme = empty($app->router) || $app->router->region === 'developer' || $app->router->region === 'admin' ? $this->theme_sys : $this->theme_app;

		// site info
		$this->sitename = isset($app->vars['sitename']) ? $app->vars['sitename'] : '';
		$this->description = isset($app->vars['description']) ? $app->vars['description'] : '';
		$this->keywords = isset($app->vars['keywords']) ? $app->vars['keywords'] : '';
		$this->logo = isset($app->vars['logo']) ? $app->vars['logo'] : '';
		$this->slogan = isset($app->vars['slogan']) ? $app->vars['slogan'] : '';
		$this->copyrights = isset($app->vars['copyrights']) ? $app->vars['copyrights'] : '';
		$this->message_method = isset($app->vars['message_method']) ? $app->vars['message_method'] : '';
		$this->message_frontpage = isset($app->vars['message_frontpage']) ? $app->vars['message_frontpage'] : '';
		$this->message_showup_time = isset($app->vars['message_showup_time']) ? $app->vars['message_showup_time'] : '';

		// regions
		$this->regions = array();
	}

	public function setModel(Model $model)
	{
		die('PageView no need Model');
	}

	public function getModel()
	{
		die('PageView no need Model');
	}

	public function getTpl()
	{
		// TODO: cache
		$tpls = array(
			'page/' . $this->region . '-' . $this->module . '-' . $this->action . '.' . $this->entry,
			'page/' . $this->region . '-' . $this->module . '.' . $this->entry,
			'page/' . $this->region . '.' . $this->entry,
			'page.' . $this->entry,
		);

		// tpl
		$t = '';
		foreach($tpls as $tpl)
		{
			if (file_exists(THEMES_DIR . '/' . $this->theme . '/' . $tpl))
			{
				$t = THEMES_DIR . '/' . $this->theme . '/' . $tpl;
				break;
			}
		}

		if (empty($t) && $this->theme !== $this->theme_sys)
		{
			foreach($tpls as $tpl)
			{
				if (file_exists(THEMES_DIR . '/' . $this->theme_sys . '/' . $tpl))
				{
					$t = THEMES_DIR . '/' . $this->theme_sys . '/' . $tpl;
					break;
				}

			}
		}

		if (empty($t) || !file_exists($t))
		{
			throw new \Exception("tpl empty or not found:[r={$this->region} m={$this->module} a={$this->action} entry={$this->entry}]$t");
		}

		return $t;
	}

	public function get_contents()
	{
		// regions
		$app = \icore\Application::get();	
		$max = isset($app->vars['max_region_num']) ? $app->vars['max_region_num'] : 10;

		// contents
		for($i = 0; $i < $max; $i++)
		{
			$views = & $this->_data['regions'][$i];
			if (empty($views)) continue;
			$t = '';
			foreach($views as $view)
			{
				$view->parent = $this;
				$t .= $view->get_contents();
			}
			$views = $t;
		}
		
		// get contents	
		ob_start();
		include $this->getTpl();
		$t = ob_get_contents();
		ob_end_clean();
		return $t;
	}
}
