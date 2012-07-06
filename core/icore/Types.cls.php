<?php
// namespace
namespace icore;

class Types extends SingletonClass
{
	protected $_types = array();
	protected $_validators = array();

	// form element
	protected $_html = array();
	// TODO: protected $_html5 = array();

	// class static methods
	protected static function & _instance_()
	{
		static $instance;
		return $instance;
	}

	public function getTypes()
	{
		return $this->_types;
	}

	public function __construct()
	{
		$this->init();
	}

	public function validate($type, $value, $extra = '')
	{
		if (!isset($this->_validators[$type]) || empty($this->_validators[$type]))
			return false;

		return call_user_func_array($this->_validators[$type], array($value, $extra));
	}

	public function html($name, $type, $extra = '', $default = '')
	{
		if (!isset($this->_html[$type]) || empty($this->_html[$type]))
			return '';

		return call_user_func_array($this->_html[$type], array($name, $extra, $default));
	}

	/*
	public function html5($name, $type, $extra = '', $default = '')
	{
		if (!isset($this->_html5[$type]) || empty($this->_html5[$type]))
			return '';

		return call_user_func_array($this->_html5[$type], array($name, $extra, $default));
	}
	*/

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

		// html
		$html = & $this->_html;
		$html['enum'] = create_function('$n,$e="",$d=""', '$e=explode(",", $e);
$opts = "";
foreach($e as $i)
{
	$opts .= sprintf("\t<option value=\"%s\"%s>%s</option>\n", htmlspecialchars($i), $d == $i ? " selected=\"selected\"" : "", htmlspecialchars($i));
}
return sprintf("<select name=\"%s\" id=\"%s\">\n%s\n</select>\n", $n, $n, $opts);');
		$html['file'] = create_function('$n,$e="",$d=""', ''); // TODO: file 
		$html['float'] = create_function('$n, $e="", $d=""', 'if (empty($e)) $e = 20;
return sprintf("<input type=\"text\" name=\"%s\" id=\"%s\" value=\"%s\" size=\"%d\" maxlength=\"%d\" />\n", $n, $n, $d, $e > 20 ? 20 : $e, $e);');
		$html['image'] = create_function('$n, $e="", $d=""', 'return true;'); // TODO: image
		$html['int'] = create_function('$n, $e="", $d=""', 'if (empty($e)) $e = 20;
return sprintf("<input type=\"text\" name=\"%s\" id=\"%s\" value=\"%s\" size=\"%d\" maxlength=\"%d\" />\n", $n, $n, $d, $e > 20 ? 20 : $e, $e);');
		$html['mail'] = create_function('$n, $e="", $d=""', 'return sprintf("<input type=\"text\" name=\"%s\" id=\"%s\" value=\"%s\" size=\"%d\" maxlength=\"40\" />\n", $n, $n, htmlspecialchars($d), $e > 20 ? 20 : $e);');
		$html['name'] = create_function('$n, $e="", $d=""', 'if (empty($e)) $e = 40;
return sprintf("<input type=\"text\" name=\"%s\" id=\"%s\" value=\"%s\" size=\"%d\" maxlength=\"%d\" />\n", $n, $n, $d, $e > 20 ? 20 : $e, $e);');
		$html['owner'] = create_function('$n, $e="", $d=""', 'return true;'); // TODO: owner
		$html['password'] = create_function('$n, $e="", $d=""', 'if (empty($e)) $e = 40;
return sprintf("<input type=\"password\" name=\"%s\" id=\"%s\" size=\"%d\" maxlength=\"%d\" />\n", $n, $n, $e > 20 ? 20 : $e, $e);');
		$html['ref'] = create_function('$n, $e="", $d=""', 'return true;'); // TODO: ref
		$html['string'] = create_function('$n, $e="", $d=""', 'if (empty($e)) $e = 200;
return sprintf("<input type=\"text\" name=\"%s\" id=\"%s\" value=\"%s\" size=\"%d\" maxlength=\"%d\" />\n", $n, $n, htmlspecialchars($d), $e > 80 ? 80 : $e, $e);');
		$html['text'] = create_function('$n, $e="", $d=""', 'return sprintf("<textarea name=\"%s\" id=\"%s\" cols=\"80\" rows=\"%d\">\n%s\n</textarea>\n", $n, $n, $e, htmlspecialchars($d));');
		$html['time'] = create_function('$n, $e="", $d=""', 'return true;'); // TODO: time
		$html['title'] = create_function('$n, $e="", $d=""', 'if (empty($e)) $e = 200;
return sprintf("<input type=\"text\" name=\"%s\" id=\"%s\" value=\"%s\" size=\"80\" maxlength=\"%d\" />\n", $n, $n, htmlspecialchars($d), $e);');
		$html['tree'] = create_function('$v,$e=""', 'return true;'); // TODO: tree

		/*
		$html5 = & $this->_html5;
		$html5['enum'] = create_function('$n,$e="",$d=""', '$e=explode(",");
$opts = "";
foreach($e as $i)
{
	$opts .= sprintf("\t<option value=\"%s\"%s>%s</option>\n", htmlspecialchars($i), $d == $i ? " selected=\"selected\"" : "", htmlspecialchars($i));
}
return sprintf(<<<EOF
<select name="%s" id="%s">
	%s
</select>\n
EOF, $n, $n, $opts);');
		$html5['file'] = create_function('$n,$e="",$d=""', ''); // TODO: file 
		$html5['float'] = create_function('$n, $e="", $d=""', 'if (empty($e)) $e = 20;
return sprintf(<<<EOF
<input type="text" name="%s" id="%s" value="%s" size="20" maxlength="%d" />\n
EOF, $n, $n, $d, %e);');
		$html5['image'] = create_function('$n, $e="", $d=""', 'return true;'); // TODO: image
		$html5['int'] = create_function('$n, $e="", $d=""', 'if (empty($e)) $e = 20;
return sprintf(<<<EOF
<input type="text" name="%s" id="%s" value="%s" size="20" maxlength="%d" />\n
EOF, $n, $n, $d, %e);');
		$html5['mail'] = create_function('$n, $e="", $d=""', 'return sprintf(<<<EOF
<input type="text" name="%s" id="%s" value="%s" size="40" maxlength="40" />\n
EOF, $n, $n, htmlspecialchars($d));');
		$html5['name'] = create_function('$n, $e="", $d=""', 'if (empty($e)) $e = 40;
return sprintf(<<<EOF
<input type="text" name="%s" id="%s" value="%s" size="40" maxlength="%d" />\n
EOF, $n, $n, $d, %e);');
		$html5['owner'] = create_function('$n, $e="", $d=""', 'return true;'); // TODO: owner
		$html5['password'] = create_function('$n, $e="", $d=""', 'if (empty($e)) $e = 40;
return sprintf(<<<EOF
<input type="password" name="%s" id="%s" size="40" maxlength="%d" />\n
EOF, $n, $n, %e);');
		$html5['ref'] = create_function('$n, $e="", $d=""', 'return true;'); // TODO: ref
		$html5['string'] = create_function('$n, $e="", $d=""', 'if (empty($e)) $e = 200;
return sprintf(<<<EOF
<input type="text" name="%s" id="%s" value="%s" size="80" maxlength="%d" />\n
EOF, $n, $n, htmlspecialchars($d), %e);');
		$html5['text'] = create_function('$n, $e="", $d=""', 'return sprintf(<<<EOF
<textarea name="%s" id="%s" cols="80" rows="%d">
$e
</textarea>\n
EOF, $n, $n, $e, htmlspecialchars($d));');
		$html5['time'] = create_function('$n, $e="", $d=""', 'return true;'); // TODO: time
		$html5['title'] = create_function('$n, $e="", $d=""', 'if (empty($e)) $e = 200;
return sprintf(<<<EOF
<input type="text" name="%s" id="%s" value="%s" size="80" maxlength="%d" />\n
EOF, $n, $n, htmlspecialchars($d), %e);');
		$html5['tree'] = create_function('$v,$e=""', 'return true;'); // TODO: tree
		*/
	}
}



