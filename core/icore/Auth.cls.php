<?php
// namespace
namespace icore;

abstract class Auth extends SingletonClass
{
	protected static function _file_()
	{
		return __FILE__;
	}

	// auth
	abstract public function auth($id, $secret);

	// user
	public function user()
	{
		$app = Application::get();

		if (!isset($_SESSION['user']) || empty($_SESSION['user']))
		{
			$_SESSION['user'] = array('id' => 0, 'name' => 'anonymous', 'role' => 'guest');
		}

		if ($app->state === ICORE_INSTALL_PHRASE || (php_sapi_name() === 'cli' && $app->vars['sitemode'] === 'maintenance'))
		{
			$_SESSION['user'] = array('id' => 0, 'name' => 'root', 'role' => 'developer');
		}

		return $_SESSION['user'];
	}
}

