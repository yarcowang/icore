<?php
// namespace 
namespace icore\views;

use icore\Views;

class GridView extends Views
{
	public function __construct()
	{
		parent::__construct();

		// default attributes 
		$this->title = '';
		$this->headers = array();
		$this->records = array();
		$this->links = array();
		$this->msg = '';
	}

	public function getTpl()
	{
		// TODO: cache
		return $this->getTplByName('grid');
	}

	public function get_contents()
	{
		// get contents
		ob_start();
		include $this->getTpl();
		$t = ob_get_contents();
		ob_end_clean();
		return $t;
	}
}
