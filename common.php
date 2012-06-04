<?php
// icore comm for driver
if (!defined('DRIVER_FILE'))
{
	die('cant be accessed directly');
}

// module related file extension(.m.php) and class file extension(.cls.php, normally used in framework classes)
define('ICORE_MODULE_EXT', '.m.php');
define('ICORE_CLASS_EXT', '.cls.php');

// related directories
define('ROOT_DIR', __DIR__);
define('ICORE_CORE', ROOT_DIR . '/core');
define('MODULES_DIR', ROOT_DIR . '/modules');
define('THEMES_DIR', dirname(DRIVER_FILE) . '/themes');

// autoload
set_include_path(MODULES_DIR . PATH_SEPARATOR . ICORE_CORE . PATH_SEPARATOR . get_include_path());
spl_autoload_extensions(ICORE_MODULE_EXT . ',' . ICORE_CLASS_EXT);
spl_autoload_register();

// var directories
define('VAR_DIR', ROOT_DIR . '/var');
define('VAR_MODELS_DIR', VAR_DIR . '/models');
define('VAR_CACHE_DIR', VAR_DIR . '/cache');
define('VAR_CONFIGS_DIR', VAR_DIR . '/configs');
// ...

// common functions
function url($region = null, $module = null, $action = null, $args = array(), $entry = null, $lang = null)
{
	return \icore\Application::get()->router->makeUrl($region, $module, $action, $args, $entry, $lang);
}
/*
function i(\icore\Views $obj, $key, $default = null)
{
	$args = func_get_args();
	$ret = \icore\MultiLang::get()->translate($obj, $obj->$key);
	if (count($args) > 2)
	{
		unset($args[0]);
		$args[1] = $ret;
		$ret = call_user_func_array('sprintf', $args);
	}

	if (empty($ret) && !empty($default))
	{
		$ret = $default;
	}

	print $ret;
}
*/
