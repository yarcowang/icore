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
		$this->_title_more = 'iCore CMS - install process';

		// logo
		$view = new \icore\views\BlockView;
		$view->setData(array('name' => 'logo', 'content' => 'iCore')); 
		$this->addView(0, $view);
		
		// menu
		$view = new \icore\views\TreeView;
		$view->setData(array('name' => 'menu'));
		$view->records = array(
			array('href' => 'javascript:void(0)', 'title' => 'About',  'level' => 1),
			array('href' => 'javascript:void(0)', 'title' => 'License', 'level' => 1),
			array('href' => 'javascript:void(0)', 'title' => 'Checking', 'level' => 1),
			array('href' => 'javascript:void(0)', 'title' => 'Settings', 'level' => 1),
			array('href' => 'javascript:void(0)', 'title' => 'Result', 'level' => 1),
		);
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
		$view->setData(array('name' => 'install', 'title' => 'About',
			'content' => <<<EOF
			<p>iCore is a loose php framework/CMS for fast website building (<b>considering html5,php5.4 etc)</b>).</p>
			<p>It is something like drupal, but small and simple.</p>
			<br />
			<p>It begins from 2007 (i forgot), a simple php framework called Coto. But never published.</p>
			<p>After touch drupal, the idea of "simple drupal" comes out. But it still remains a lot of things to do.</p>
			
			<p>And now i merged everythings together. That is the framework you see, or you can call it "iCore CMS".</p>
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
		$view = new \icore\views\GridView;	
		
		$view->name = 'install';
		$view->title = 'Checking';
		$view->headers = array('title' => 'Title', 'result' => 'Result', 'required' => 'Required');
		$view->records = array();

		$item = & $view->records[];
		$item['title'] = 'PHP Version';
		$item['result'] = PHP_VERSION;
		$item['required'] = '>=5.3';

		// short open tag before 5.4
		if (!version_compare(PHP_VERSION, '5.4', '>='))
		{
			$item = & $view->records[];
			$item['title'] = 'ShortOpenTag';
			$item['result'] = ini_get('short_open_tag') ? 'on' : 'off';
			$item['required'] = 'on';
		}

		$item = & $view->records[];
		$item['title'] = 'var/config';
		$item['result'] = is_writable(dirname(CONFIG_FILE)) ? 'writable' : 'not writable';
		$item['required'] = 'writable';

		foreach(array('pdo', 'json', 'gd') as $ext)
		{
			$item = & $view->records[];
			$item['title'] = $ext;
			$item['result'] = extension_loaded($ext) ? 'installed' : 'not installed';
			$item['required'] = 'required';
		}

		$view->links = array(
			array('href' => url(null, null, 'license'), 'class' => 'more', 'title' => 'prev'),	
			array('href' => url(null, null, 'check'), 'class' => 'more', 'title' => 'reload'),
			array('href' => url(null, null, 'setting'), 'class' => 'more', 'title' => 'settings')			
		);

		$this->addView(2, $view);
	}

	public function setting()
	{	
		$view = new \icore\views\FormView;

		$view->name = 'install';
		$view->title = 'Settings';

		$model = new \icore\Model('setting');
		$view->setModel($model);

		/* TODO: form view
		$view = new \icore\views\BlockView;
		$view->name = 'install-settings';
		*/
		$view->links = array(
			array('href' => url(null, null, 'check'), 'class' => 'more', 'title' => 'checking'),	
			array('href' => 'javascript:$(\'.submit\').closest(\'form\').submit();', 'class' => 'more submit', 'title' => 'submit')			
		);
		$this->addView(2, $view);	
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

