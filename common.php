<?php
// icore comm for driver
if (!defined('DRIVER_URI'))
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
define('THEMES_DIR', DRIVER_DIR . '/themes');

// autoload
set_include_path(MODULES_DIR . PATH_SEPARATOR . ICORE_CORE . PATH_SEPARATOR . get_include_path());
spl_autoload_extensions(ICORE_MODULE_EXT . ',' . ICORE_CLASS_EXT);
spl_autoload_register();

// var directories
define('VAR_DIR', ROOT_DIR . '/var');
define('VAR_MODELS_DIR', VAR_DIR . '/models');
define('VAR_SQLS_DIR', VAR_DIR . '/sqls');
define('VAR_CACHE_DIR', VAR_DIR . '/cache');
define('VAR_CONFIGS_DIR', VAR_DIR . '/configs');
// ...

// common functions
function url($region = null, $module = null, $action = null, $args = array(), $entry = null, $lang = null)
{
	return \icore\Application::get()->router->makeUrl($region, $module, $action, $args, $entry, $lang);
}

function js($url = null)
{
	static $buff;
	static $tpl1 = "<script type=\"text/javascript\" src=\"__SRC__\"></script>\n";
	static $tpl2 = '<script type="text/javascript">__JS__</script>';

	$compress_js = isset(\icore\Application::get()->vars['compress_js']) && !empty(\icore\Application::get()->vars['compress_js']) ? intval(\icore\Application::get()->vars['compress_js']) : 0;
	
	if (!empty($url))
	{
		if ($compress_js)
			$buff[] = $url;
		else
			return str_replace('__SRC__', $url, $tpl1);
	}
	else
	{
		if ($compress_js)
		{
			$key = VAR_CACHE_DIR . '/js_' . md5(CONFIG_FILE . serialize($buff));
			$obj = \icore\cache\JsCache::get();
			$obj->setKey($key);
			if (!$obj->isCached())
			{
				$string = '';
				foreach($buff as $url)
				{
					if (substr($url, 0, 4) === 'http')
					{
						$string .= file_get_contents($url);
					}
					else
					{
						$string .= file_get_contents(DRIVER_DIR . '/' . $url);
					}
				}
				$obj->saveCache($string);
			}
			return str_replace('__JS__', $obj->get_contents(), $tpl2);
		}
		else
		{
			return '';
		}
	}
}

function css($url = null, $media = 'all')
{
	static $buff;
	static $tpl1 = "<link rel=\"stylesheet\" type=\"text/css\" href=\"__HREF__\" media=\"__MEDIA__\" />\n";
	static $tpl2 = "<style type=\"text/css\" media=\"__MEDIA__\">__CSS__</style>";

	$compress_css = isset(\icore\Application::get()->vars['compress_css']) && !empty(\icore\Application::get()->vars['compress_css']) ? intval(\icore\Application::get()->vars['compress_css']) : 0;

	if (!empty($url))
	{
		if ($compress_css)
		{
			if (!isset($buff[$media]))
			{
				$buff[$media] = array();
			}
			$buff[$media][] = $url;
		}
		else
			return str_replace(array('__HREF__', '__MEDIA__'), array($url, $media), $tpl1);
	}
	else
	{
		if ($compress_css)
		{
			$t = '';
			foreach($buff as $media => $urls)
			{
				$key = VAR_CACHE_DIR . '/css_' . md5(CONFIG_FILE . serialize($urls));
				$obj = \icore\cache\CssCache::get();
				$obj->setKey($key);
				if (!$obj->isCached())
				{
					$string = '';
					foreach($urls as $url)
					{
						if (substr($url, 0, 4) === 'http')
						{
							$string .= file_get_contents($url);
						}
						else
						{
							$string .= file_get_contents(DRIVER_DIR . '/' . $url);
						}
					}
					$obj->saveCache($string);
				}
				$t .= str_replace(array('__MEDIA__', '__CSS__'), array($media, $obj->get_contents()), $tpl2);
			}
			return $t;
		}
		else
		{
			return '';
		}
	}
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
