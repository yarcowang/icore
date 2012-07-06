<?php
// namespace 
namespace icore\views;

use icore\Views;
use icore\Model;

class FormView extends Views
{
	public function __construct()
	{
		parent::__construct();

		// default attributes 
		$this->title = '';
		$this->links = array();
		$this->msg = '';

		// form related
		$this->action = '';
		$this->method = 'post';
		$this->enctype = 'application/x-www-form-urlencoded';
	}

	public function setModel(Model $model)
	{
		// TODO: set model for FormView

		parent::setModel($model);
	}

	public function getTpl()
	{
		// TODO: cache
		return $this->getTplByName('form');
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
