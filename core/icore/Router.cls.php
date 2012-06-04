<?php
// namespace 
namespace icore;

// router
abstract class Router extends SingletonClass
{
	// basic information
	public $entry;
	public $lang;
	public $region;
	public $module;
	public $action;

	// class static methods
	protected static function _file_()
	{
		return __FILE__;
	}

	protected static function & _instance_()
	{
		static $instance;
		return $instance;
	}

	// abstract functions
	abstract public function parse();
	abstract public function makeUrl($region = null, $module = null, $action = null, array $args = array(), $entry = null, $lang = null);
}

