<?php
// namespace
namespace icore;

abstract class SingletonClass 
{
	/**
	 * if it has subclasses, and you want to list the names of subclasses
	 *
	 * need:
	 * abstract protected static function _file_
	 */
	public static function getNames()
	{
		$file = static::_file_();
		$filename = basename($file);
		$path = dirname($file) . '/' . strtolower(substr($filename, 0, strlen($filename) - strlen(ICORE_CLASS_EXT)));

		$names = array();
		foreach(glob($path . '/*' . ICORE_CLASS_EXT) as $afile)
		{
			// class name
			$name = substr(basename($afile), 0,  -1 * strlen(ICORE_CLASS_EXT));
			$names[] = $name;
		}

		return $names;
	}

	/**
	 * if singleton
	 *
	 * need: 
	 * abstract protected static function & _instance_();
	 *
	 * @param string fullnamespace + classname
	 * @return instance
	 */
	public static function get($class = null)
	{
		$instance = & static::_instance_();
		if (!$instance)
		{
			$instance = empty($class) ? new static : new $class;
		}

		return $instance;	
	}
}
