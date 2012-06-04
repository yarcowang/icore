<?php
// namespace
namespace icore;

// command line interface help
class CliHelper
{
	public static $shortOpts = 'he::l::r::m::a::';
	public static $longOpts = array(
		'help',
		'get::',
		'post::',
	);

	public static function enable()
	{
		if (php_sapi_name() !== 'cli')
			return;

		$opts = getopt(self::$shortOpts, self::$longOpts);
		if (isset($opts['h']) || isset($opts['help']))
		{
			self::help();
			exit;
		}

		// set GET & POST
		$_GET = array();
		$_POST = array();

		// short
		if (isset($opts['e']) && !empty($opts['e'])) $_GET['entry'] = $opts['e'];
		if (isset($opts['l']) && !empty($opts['l'])) $_GET['lang'] = $opts['l'];
		if (isset($opts['r']) && !empty($opts['r'])) $_GET['r'] = $opts['r'];
		if (isset($opts['m']) && !empty($opts['m'])) $_GET['m'] = $opts['m'];
		if (isset($opts['a']) && !empty($opts['a'])) $_GET['a'] = $opts['a'];

		// get & post
		if (isset($opts['get']) && !empty($opts['get']))
		{
			parse_str($opts['get'], $tmp);
			$_GET = array_merge($tmp, $_GET);
		}
		if (isset($opts['post']) && !empty($opts['post'])) parse_str($opts['post'], $_POST);
	}

	public static function help()
	{
		$help =<<<EOF
iCore Framework(ICORE_VERSION) cli help (::=optional option)
  -h, --help ---- show this help
  -e:: ---- set entry (default: DEFAULT_ENTRY)
  -l:: ---- set lang (default: DEFAULT_LANG)
  -r:: ---- set region (default: DEFAULT_REGION)
  -m:: ---- set module (default: DEFAULT_MODULE)
  -a:: ---- set action (default: DEFAULT_ACTION)
  --get:: ---- set GET 
  --post:: ---- set POST 
examples (index.php is the driver):
  php index.php -h
  php index.php (equal to: entry=DEFAULT_ENTRY, lang=DEFAULT_LANG, region=DEFAULT_REGION, module=DEFAULT_MODULE, action=DEFAULT_ACTION)
  php index.php -ehtml -len_US -rguest -mhome -aindex --get="k=v&k=v" --post="k=v&k=v"\n
EOF;
		print str_replace(
				array('ICORE_VERSION', 'DEFAULT_ENTRY', 'DEFAULT_LANG', 'DEFAULT_REGION', 'DEFAULT_MODULE', 'DEFAULT_ACTION'),
				array(ICORE_VERSION, DEFAULT_ENTRY, DEFAULT_LANG, DEFAULT_REGION, DEFAULT_MODULE, DEFAULT_ACTION),
				$help
			);
	}
}
