<?php
// namespace 
namespace icore;

// views 
abstract class Views extends SingletonClass
{
	// parent
	public $parent;

	// internal saved data 
	protected $_data;

	// class static methods
	protected static function _file_()
	{
		return __FILE__;
	}

	public function __construct()
	{
		// each view needs a name
		$this->name = '';
	}

	public function __get($key)
	{
		return isset($this->_data[$key]) ? $this->_data[$key] : null;
	}

	public function __set($key, $value)
	{
		$this->_data[$key] = $value;
	}

	public function setData(array $data = array())
	{
		$this->_data = array_merge($this->_data, $data);	
	}

	/**
	 * used by view != PageView
	 */
	protected function getTplByName($name)
	{
		$tpls = array(
			"$name/" . $this->name . '-' . $this->parent->region . '-' . $this->parent->module . '-' . $this->parent->action .
			'.' . $this->parent->entry,
			"$name/" . $this->name . '-' . $this->parent->region . '-' . $this->parent->module . '.' . $this->parent->entry,
			"$name/" . $this->name . '-' . $this->parent->region . '.' . $this->parent->entry,
			"$name/" . $this->name . '.' . $this->parent->entry,
			"$name." . $this->parent->entry,
		);

		// tpl
		$t = null; 
		foreach($tpls as $tpl)
		{
			if (file_exists(THEMES_DIR . '/' . $this->parent->theme . '/' . $tpl))
			{
				$t = THEMES_DIR . '/' . $this->parent->theme . '/' . $tpl;
				break;
			}
		}

		if (empty($t) && $this->parent->theme !== $this->parent->theme_sys)
		{
			foreach($tpls as $tpl)
			{
				if (file_exists(THEMES_DIR . '/' . $this->parent->theme_sys . '/' . $tpl))
				{
					$t = THEMES_DIR . '/' . $this->parent->theme_sys . '/' . $tpl;
					break;
				}
			}
		}

		if (empty($t) || !file_exists($t))
		{
			throw new \Exception("tpl empty or not found:[tpl={$name} name={$this->name} r={$this->parent->region} m={$this->parent->module} a={$this->parent->action} entry={$this->parent->entry}]$t");
		}

		return $t;
	}

	// get template
	abstract public function getTpl();
	// get contents as string
	abstract public function get_contents();
}
