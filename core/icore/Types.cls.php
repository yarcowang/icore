<?php
// namespace
namespace icore;

class Types extends SingletonClass
{
	protected $_types = array();
	protected $_validators = array();

	public function getTypes()
	{
		return $this->_types;
	}

	public function __construct()
	{
		$this->init();
	}

	protected function init()
	{
		// TODO: add more types here
		$types = & $this->_types;
		$types[] = 'enum';
		$types[] = 'file';
		$types[] = 'float';
		$types[] = 'image';
		$types[] = 'int';
		$types[] = 'mail';
		$types[] = 'name';
		$types[] = 'owner';
		$types[] = 'password';
		$types[] = 'ref';
		$types[] = 'string';
		$types[] = 'text';
		$types[] = 'time';
		$types[] = 'title';
		$types[] = 'tree';

		// validators
		$validators = & $this->_validators;
		$validators['enum'] = create_function('$v,$e=""', 'return strpos($e, $v) !== false;');
		$validators['file'] = create_function('$v,$e=""', 'return true;'); // TODO: file 
		$validators['float'] = create_function('$v, $e=""', 'return filter_var($v, FILTER_VALIDATE_FLOAT);');
		$validators['image'] = create_function('$v,$e=""', 'return true;'); // TODO: image
		$validators['int'] = create_function('$v, $e=""', 'return filter_var($v, FILTER_VALIDATE_INT);');
		$validators['mail'] = create_function('$v,$e=""', 'if (strpos($v, \'@\')===false) return false; if ($e===\'strict\') return filter_var($v, FILTER_VALIDATE_EMAIL); return true;');
		$validators['name'] = create_function('$v,$e=""', 'if (!empty($e) && strlen($v)>$e) return false; return preg_match(\'|[^a-zA-Z_]|\', $v) ? false : true;');
		$validators['owner'] = create_function('$v,$e=""', 'return true;'); // TODO: owner
		$validators['password'] = create_function('$v,$e=""', 'if (strlen($v)<5) return false; if ($e==\'strict\' && (!preg_match(\'/[a-zA-Z]/\', $v) || !preg_match(\'/[0-9]/\', $v))) return false; return true;');
		$validators['ref'] = create_function('$v,$e=""', 'return true;'); // TODO: ref
		$validators['string'] = create_function('$v,$e=""', 'if (!empty($e) && strlen($v)>$e) return false; return true;');
		$validators['text'] = create_function('$v,$e=""', 'return true;');
		$validators['time'] = create_function('$v,$e=""', 'return true;'); // TODO: time
		$validators['title'] = create_function('$v,$e=""', 'if (!empty($e) && strlen($v)>$e) return false; return true;');
		$validators['tree'] = create_function('$v,$e=""', 'return true;'); // TODO: tree
	}

	public function validate($type, $value, $extra = '')
	{
		if (!isset($this->_validators[$type]) || empty($this->_validators[$type]))
			return false;

		return call_user_func_array($this->_validators[$type], array($value, $extra));
	}
}



