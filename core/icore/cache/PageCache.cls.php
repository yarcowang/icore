<?php
// namespace
namespace icore\cache;

use icore\Cache;

class PageCache extends Cache
{
	// class static methods
	protected static function & _instance_()
	{
		static $instance;
		return $instance;
	}

	public function __construct()
	{
		parent::__construct();

		// cache file
		$this->setKey(VAR_CACHE_DIR . '/pagecache_' . md5(CONFIG_FILE . serialize($_GET)));
	}

	public function isCached()
	{
		// page cache is not valid for dummy, json and cli
		if (in_array($this->_app->router->entry, array('dummy', 'json', 'cli')))
		{
			return false;
		}

		return parent::isCached();
	}
}