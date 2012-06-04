<?php
// namespace 
namespace guest;

use icore\Module;

// install
class install extends Module
{
	public function __construct()
	{
		parent::__construct();

		// logo
		$view = new \icore\views\BlockView;
		$view->setData(array('name' => 'logo', 'title' => 'Install iCore')); // END HERE
		$this->addView(0, $view);

		// menu
		$view = new \icore\views\BlockView;
		$view->setData(array('name' => 'install-menu'));
		$this->addView(1, $view);

		// copyright
		$v = ICORE_VERSION;
		$view = new \icore\views\BlockView;
		$view->setData(array('name' => 'install',
			'content' => <<<EOF
			<br />
			<p>Copyright &copy;2012 Green Apple Tech Limited | Powered by iCore v{$v}. Supported by <span title="contact@green-apple.mobi">G.A.T.L.</span></p>
EOF
		));
		$this->addView(3, $view);
	}

	public function index()
	{
		$view = new \icore\views\BlockView;
		$view->setData(array('name' => 'install', 'title' => 'Introduction',
			'content' => <<<EOF
			<p>iCore is a php framework or CMS (short for content management system) for fast website building.</p>
			<p>It is something like drupal, but small and simple.</p>
			<br />
			<p><b>Since it uses file links, it may only works on *nix.</b></p>
			<br />
			<p>It begins from 2007 (maybe), a simple php framework called Coto. Coto is just a simple framework like CI, but never really used.</p>
			<p>After touch drupal, the idea of "simple drupal" comes up. But it is still a hard work.</p>
			<p>After finish a simple project of my friend, i merged all things together. That is this framework or you can call it "iCore CMS".</p>
			<p>The idea of the name "iCore" is original from apple's i-series. Apple is really a reputable company in my eyes.</p>
EOF
		, 'links' => array(array('href' => url(null, null, 'license'), 'class' => 'more', 'title' => 'license'))
		));
		$this->addView(2, $view);
	}

	public function license()
	{
		$view = new \icore\views\BlockView;
		$view->name = 'install';
		$view->title = 'License';
		$view->content = <<<EOF
			<p>iCore may include several libraries like jquery, tiny_mce etc under lib/. They are not a part of this framework.</p>
			<br />
			<p>This framework has Dual Licenses like Qt. One for personal(GPL), One for commercial.</p>
			<blockquote>THERE IS NO WARRANTY FOR THIS FRAMEWORK.</blockquote>
			<br />
			<p>If any suggestion, you could contact contact@green-apple.mobi.</p>
			<p>All rights reserved by GREEN APPLE TECH LIMITED (青澀蘋果有限公司) from 2011.</p>
EOF;
		$view->links = array(
			array('href' => './', 'class' => 'more', 'title' => 'prev'),
			array('href' => url(null, null, 'check'), 'class' => 'more', 'title' => 'checking')
		);
		$this->addView(2, $view);
	}

	public function check()
	{
		$options = array();
		$options['headers'] = array('title' => 'Title', 'result' => 'Result', 'required' => 'Required');
		$options['records'] = array();

		$item = & $options['records'][];
		$item['title'] = 'OS';
		$item['result'] = PHP_OS;
		$item['required'] = '*nix like';

		$item = & $options['records'][];
		$item['title'] = 'PHP Version';
		$item['result'] = PHP_VERSION;
		$item['required'] = '>=5.3';

		$item = & $options['records'][];
		$item['title'] = 'var/config';
		$item['result'] = is_writable(dirname(CONFIG_FILE)) ? 'writable' : 'not writable';
		$item['required'] = 'writable';

		foreach(array('pdo', 'pdo_mysql', 'json', 'gd') as $ext)
		{
			$item = & $options['records'][];
			$item['title'] = $ext;
			$item['result'] = extension_loaded($ext) ? 'Installed' : 'Not Installed';
			$item['required'] = 'required';
		}
		
		$view = new \icore\views\BlockView;
		$view->name = 'install-check';
		$view->title = 'Checking';
		$view->data = $options;
		$this->addView(2, $view);	
	}

	public function setting()
	{	
		$view = new \icore\views\BlockView;
		$view->name = 'install-settings';
		$this->addView(2, $view);	
	}

	public function result()
	{
		if (!empty($_POST))
		{
			// install process
			$db = & \icore\Application::get()->db;
			$db = \icore\DB::get($_POST['driver'])->init($_POST);

			// TODO: may use model

			// mysql
			$ret = $db->exec(file_get_contents(VAR_DIR . '/mysql.sql'));
			if ($ret !== false)
			{
				$out = sprintf("<?php\n\$config = %s;\n", var_export($_POST, true));	
				if (file_put_contents(CONFIG_FILE, $out))
				{
					// set current user as root
					\icore\Session::start();
					\icore\Auth::get()->user();				

					// redirect
					header('Location: ' . \icore\Router::get($_POST['router'])->makeUrl('developer'));
					exit;
				}
			}
		}
		
		$view = new \icore\views\BlockView;
		$view->name = 'install';
		$view->title = 'Install Error';
		$view->content = <<<EOF
			<p>Check the directory of var/config, it should be writable.</p>
			<p>Also check your database settings.</p>
EOF;
		$view->links = array(array('href' => url(null, null, 'setting'), 'class' => 'more', 'title' => 'prev'));
		$this->addView(2, $view);
	}
}

