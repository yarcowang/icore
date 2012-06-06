<?php
// namespace
namespace icore\cache;

use icore\Cache;

class CssCache extends Cache
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
	}

	public function saveCache(& $string)
	{
		// TODO: more strict for minify
		// ??? https://github.com/mrclay/minify

		// simple version
		$t = str_replace("\r\n", "\n", $string);

		$t = str_replace("\n", '', $t); // remove \n

		return parent::saveCache($t);
	}
}

