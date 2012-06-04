<?php
// namespace 
namespace icore\views;

use icore\Views;

class BlockView extends Views
{
	public function __construct()
	{
		// block view has a name
		$this->name = '';

		// default attributes 
		$this->title = '';
		$this->content = '';
		$this->links = array();
		$this->msg = '';
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
