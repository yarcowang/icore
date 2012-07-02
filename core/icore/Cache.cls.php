<?php
// namespace
namespace icore;

abstract class Cache extends SingletonClass
{
	// point to app
	protected $_app;

	// key
	protected $_key;

	// cache
	protected static function _file_()
	{
		return __FILE__;
	}

	public function __construct()
	{
		$this->_app = Application::get();
	}

	public function setKey($key)
	{
		$this->_key = $key;
	}

	// abstract functions
	public function isCached()
	{
		$time = isset($this->_app->vars['page_cache_time']) ? time() - Utilities::string2seconds($this->_app->vars['page_cache_time']) : time();
		return file_exists($this->_key) && filemtime($this->_key) > $time;
	}

	public function saveCache(& $string)
	{
		$old = umask(0);
		file_put_contents($this->_key, $string);
		umask($old);
	}

	public function get_contents()
	{
		return file_get_contents($this->_key);		
	}
}
