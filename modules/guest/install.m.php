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

		// page related
		$this->_title_more = 'iCore - install process';

		// logo
		$view = new \icore\views\BlockView;
		$view->setData(array('content' => 'iCore')); 
		$this->addView(0, $view);
		
		/* TODO: menu view
		// menu
		$view = new \icore\views\BlockView;
		$view->setData(array('name' => 'install-menu'));
		$this->addView(1, $view);
		*/

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
			<p>iCore is a php framework/CMS for fast website building and <b>for future (good for html5,php5.4 etc)</b>.</p>
			<p>It is something like drupal, but small and simple.</p>
			<br />
			<p>It begins from 2007 (maybe, i forget), a simple php framework called Coto. But it was never really used.</p>
			<p>When touch drupal, the idea of a "simple drupal" comes up. But it still remains a hard work.</p>
			<p>After finish a simple project, i merged all together. That is the framework you see, or you can call it "iCore CMS".</p>
EOF
		, 'links' => array(array('href' => url(null, null, 'license'), 'class' => 'more', 'title' => 'license'))
		));
		$this->addView(2, $view);
	}

	public function license()
	{
		$view = new \icore\views\BlockView;
		$view->setData(array('name' => 'install', 'title' => 'License',
			'content' => <<<EOF
			<p>iCore may include several libraries like jquery, tiny_mce etc under lib/. They are not a part of this framework.</p>
			<br />
			<p>This framework which under core/ has Dual Licenses like Qt. One for personal(GPL), One for commercial.</p>
			<blockquote>THERE IS NO WARRANTY FOR THIS FRAMEWORK.</blockquote>
			<br />
			<p>If any suggestion, you could contact contact@green-apple.mobi.</p>
			<p>All rights reserved by GREEN APPLE TECH LIMITED from 2011.</p>
EOF
		, 'links' => array(
			array('href' => './', 'class' => 'more', 'title' => 'prev'),
			array('href' => url(null, null, 'check'), 'class' => 'more', 'title' => 'checking')
			)
		));
		$this->addView(2, $view);
	}

	public function check()
	{
		/* TODO: grid view
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

		// short open tag before 5.4

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
		*/
	}

	public function setting()
	{	
		/* TODO: form view
		$view = new \icore\views\BlockView;
		$view->name = 'install-settings';
		$this->addView(2, $view);	
		*/
	}

	public function result()
	{// END HERE
		if (!empty($_POST))
		{
			// install process
			$db = & \icore\Application::get()->db;
			$db = \icore\DB::get($_POST['driver'])->init($_POST);

			// TODO: would use model in future

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

