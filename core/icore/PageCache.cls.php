<?php
// namespace
namespace icore;

class PageCache extends SingletonClass
{
	protected $_app;

	// class static methods
	protected static function & _instance_()
	{
		static $instance;
		return $instance;
	}

	public function __construct()
	{
		$this->_app = Application::get();

		// cache file
		$this->_file = VAR_CACHE_DIR . '/pagecache_' . md5(CONFIG_FILE . serialize($_GET));
	}

	public function isCached()
	{
		// page cache is not valid for dummy, json and cli
		if (in_array($this->_app->router->entry, array('dummy', 'json', 'cli')))
		{
			return false;
		}

		$time = isset($this->_app->vars['page_cache_time']) ? time() - Utilities::string2seconds($this->_app->vars['page_cache_time']) : time();
		return file_exists($this->_file) && filemtime($this->_file) > $time;
	}

	public function saveCache(& $string)
	{
		$old = umask(0);
		file_put_contents($this->_file, $string);
		umask($old);
	}

	public function get_contents()
	{
		return file_get_contents($this->_file);		
	}
}
