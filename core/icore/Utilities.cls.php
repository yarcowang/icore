<?php
// namespace
namespace icore;

// utilities class
class Utilities
{
	/**
	 * pager
	 *
	 * @param integer current page no
	 * @param integer width, should be an odd
	 * @return array with "begin, curr, end" 3 keys
	 */
	public static function pager($page, $width = 15)
	{
		$ret = array();
		$ret['begin'] = 1;
		$ret['end'] = $width;

		// $page to N
		$page = $page < 1 ? 1 : intval($page);
		$ret['curr'] = $page;

		if ($page > $width / 2 + 1)
		{
			$ret['begin'] = $page - ($width - 1) / 2;
			$ret['end'] = $page + ($width - 1) / 2;
		}

		return $ret;
	}

	/**
	 * string to array
	 *
	 * @param string string need to be seperated
	 * @param string seperators which used in preg_split
	 * @return array
	 */
	public static function string2array($string, $seperator = "\s\t\n")
	{
		if (empty($string)) return array();
		$trim = create_function('& $v, $k', '$v = trim($v);');
		$array = preg_split('/[' . $seperator . ']+/', $string, null, PREG_SPLIT_NO_EMPTY);
		array_walk($array, $trim);
		return $array;
	}

	/**
	 * date string to seconds
	 *
	 * @param string the date string which should be a number and postfix one of "h"(hour), "d"(day), "w"(week), "m"(month), "y"(year)
	 * @return integer seconds
	 */
	public static function string2seconds($string)
	{
		$string = strtolower(trim($string));
		
		$ret = 0;
		if (strpos($string, 'h'))
		{
			$ret = intval($string) * 3600;
		}
		else if (strpos($string, 'd'))
		{
			$ret = intval($string) * 86400;
		}
		else if (strpos($string, 'w'))
		{
			$ret = intval($string) * 86400 * 7;
		}
		else if (strpos($string, 'm'))
		{
			$ret = intval($string) * 86400 * 30;
		}
		else if (strpos($string, 'y'))
		{
			$ret = intval($string) * 86400 * 365;
		}
		else
		{
			$ret = intval($string);
		}

		return $ret;
	}

	/**
	 * size string to bytes
	 *
	 * @param string size string which should be a number and postfix one of "k"(Kib), "m"(Mib), "g"(Gib)
	 * @return integer bytes
	 */
	public static function string2bytes($string)
	{
		$string = strtolower(trim($string));

		$ret = 0;
		if (strpos($string, 'k'))
		{
			$ret = intval($string) * 1024;
		}
		else if (strpos($string, 'm'))
		{
			$ret = intval($string) * 1024 * 1024;
		}
		else if (strpos($string, 'g'))
		{
			$ret = intval($string) * 1024 * 1024 * 1024;
		}

		return $ret;
	}

	/**
	 * bytes to size string
	 *
	 * @param integer bytes
	 * @return string
	 */
	public static function bytes2string($bytes)
	{
		$bytes = intval(trim($bytes));

		// G: 1073741824
		// M: 1048576
		// K: 1024
		$ret = '';
		if ($bytes > 1073741824)
		{
			$ret = sprintf("%.1f GiB", $bytes / 1073741824.0);
		}
		else if ($bytes > 1048576)
		{
			$ret = sprintf("%.1f MiB", $bytes / 1048576.0);
		}
		else if ($bytes > 1024)
		{
			$ret = sprintf("%.1f KiB", $bytes / 1024.0);
		}
		else
		{
			$ret = sprintf("%d Bytes", $bytes);
		}
		
		return $ret;
	}

	/**
	 * a simple timer
	 */
	public static function timer()
	{
	  static $begin = null;
  	if (!$begin)
  	{
    	$begin = microtime(true);
  	}
  	return sprintf("%.3f", microtime(true) - $begin);
	}

}

