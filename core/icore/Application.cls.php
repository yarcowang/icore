<?php
// namespace
namespace icore;

// version
define('ICORE_VERSION', '0.9.1');

// state 
define('ICORE_BOOT_PHRASE', 1);
define('ICORE_INSTALL_PHRASE', 2);
define('ICORE_SYSTEM_PHRASE', 3);
define('ICORE_USER_PHRASE', 4);

// main application class
class Application extends SingletonClass
{
	// runtime information
  public $state = ICORE_BOOT_PHRASE;
	public $vars = array();

	// components
  public $db;
  public $router;
  public $user;

	// class static methods
	protected static function & _instance_()
	{
		static $instance;
		return $instance;
	}

  public static function run()
  {
    $app = Application::get();
    $app->loadConfig();
    $app->execute();
  }

  // load config
  public function loadConfig()
  {
    if (file_exists(CONFIG_FILE))
    {
			/*
      include_once CONFIG_FILE;
      $this->vars = $config;

			// connect db & get some settings
			$this->db = DB::get($this->vars['driver'])->init();

			$model = new Model('system');
			$this->vars = array_merge($this->vars, $model->map());

			// remove database informations for security
			unset($this->vars['driver'], $this->vars['dsn'], $this->vars['host'], $this->vars['dbname'], $this->vars['username'], $this->vars['password']);

			$this->state = ICORE_SYSTEM_PHRASE;
			*/
    }
		else
		{
			$this->state = ICORE_INSTALL_PHRASE;
		}
  }
  
  public function execute()
  {
		CliHelper::enable();

		switch($this->state)
		{
			case ICORE_INSTALL_PHRASE:
				$this->router = Router::get('icore\\router\\BasicRouter');
				$this->router->parse();
				$this->router->region = 'guest';
				$this->router->module = 'install';
				$class = 'guest\\install';
				break;
			/*
			default:
				// session
				// TODO: session could be a factory
				Session::start();
				$this->user = Auth::get()->user();

				$this->router = Router::get($this->vars['router']);
				$this->router->parse();

				// check region
				if (!Access::checkRegion($this->router->region, $this->user['role']))
				{
					$this->router->region = 'guest';
					$this->router->module = 'home';
					$this->router->action = 'access_denied';
				}

				switch($this->router->region)
				{
					case 'developer':
					case 'admin':
						$class = $this->router->region . '\\' . $this->router->module;
						break;
					case 'install':
						// you can never access it again without removing CONFIG_FILE
						$this->router->region = '';
					default:
						$this->state = ICORE_USER_PHRASE;
						
						$file = MODULES_DIR . '/' . $this->router->region . '/' . $this->router->module . ICORE_MODULE_EXT;
						$class = $this->router->region . '\\' . $this->router->module;
						if (!file_exists($file))
						{
							$class = 'icore\\Module';
							$action = $this->router->action; // save action
							$this->router->action = '__destruct';
						}
				}
			*/
		}

		$method = $this->router->action;
		if (isset($action)) 
			$this->router->action = $action; // for __destruct, but need action name in headers

		// is cached?
		$isCached = PageCache::get()->isCached();				

		$obj = new $class;
		if (!$isCached && $method !== '__destruct' && method_exists($obj, $method))
		{
    	$obj->$method();
		}
  }
}

