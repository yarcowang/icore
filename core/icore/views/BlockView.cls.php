<?php
// namespace 
namespace icore\views;

use icore\Views;
use icore\Model;

class BlockView extends Views
{
	public function __construct()
	{
		parent::__construct();

		// default attributes 
		$this->title = '';
		$this->content = '';
		$this->links = array();
		$this->msg = '';
	}

	public function setModel(Model $model)
	{
		// TODO: set model for BlockView
	}

	public function getTpl()
	{
		// TODO: cache
		return $this->getTplByName('block');
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
