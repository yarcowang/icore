<?php
// namespace
namespace icore;

/**
 * readonly model
 */
class Model implements \ArrayAccess
{
	// model info
	public $name;
	public $type;
	public $isVirtual;

	// fields
	public $fields;
	public $fieldType = array();
	public $fieldExtra = array();
	public $fieldRequired = array();

	// stored data
	public $data = array();
	public $dataType;

	// components 
	protected $_db;

  public function __construct($name)
  {
		// load data
		$this->name = $name;
		$this->loadModel();

		$this->_db = DBManager::get()->getDbByFlag('ro');
	}

	protected function loadModel()
	{
		$file = VAR_MODELS_DIR	. '/' . $this->name . '.ini';
		if (!file_exists($file))
		{
			throw new \Exception("model {$this->name} not exists under " . VAR_MODELS_DIR);
		}

		$fields = parse_ini_file($file, true);

		// global
		foreach($fields['global'] as $key => $value)
		{
			$this->$key = $value;
		}
		
		unset($fields['global']);
		foreach($fields as $field => $info)
		{
			$this->fields[] = $field;
			$this->fieldType[$field] = $info['type'];
			$this->fieldExtra[$field] = $info['extra'];
			$this->fieldRequired[$field] = $info['required'];
		}
	}

		// implements array access
	public function offsetExists($offset)
	{
		switch($this->dataType)
		{
			case 'item':
				if (is_string($offset))
				{
					return isset($this->data[$offset]);
				}
				break;
			case 'items':
				if (is_integer($offset))
				{
					return isset($this->data[$offset]);
				}
				break;
		}
		return false;
	}

	public function offsetGet($offset)
	{
		return ($this->dataType === 'item' || $this->dataType === 'items') && isset($this->data[$offset]) ? $this->data[$offset] : null;
	}

	// TODO: need active record?
	public function offsetSet($offset, $value)
	{
		if ($offset === 'id') return; // you can not change id

		if ($this->dataType === 'item' && is_string($offset))
		{
			$this->data[$offset] = $value;	
		}
	}

	public function offsetUnset($offset)
	{
		if ($offset === 'id') return; // you can not change id

		if ($this->dataType === 'item' && is_string($offset))
		{
			unset($this->data[$offset]);
		}	
	}

	/**
	 * field to form element
	 */
	public function ui($field, $default = '', $callback = 'html')
	{
		$types = Types::get();
		return call_user_func_array(array($types, $callback), array($field, $this->_fieldType[$field], $this->_fieldExtra[$field], $default));
	}
	
	/**
	 * fetch one record
	 */
	public function item(array $wheres = array(), $withRef = false)
	{
		if (empty($this->_db)) return array();

		// data type
		$this->dataType = 'item';

		// add id
		$fields = $this->fields;
		array_unshift($fields, 'id'); // all tables in icore require the id column

		// check tree fieldtype
		$count = count($fields);
		for($i = 1; $i < $count; $i++)	
		{
			if ($this->fieldType[$fields[$i]] === 'tree')
			{
				$field = $this->fieldExtra[$fields[$i]] === $this->name ? '' : $this->fieldExtra[$fields[$i]] . '_'; // if the name is the model name, set it as empty
				$fields = array_merge(array_slice($fields, 0, $i), array($field . 'pid', $field . 'level', $field . 'path'), array_slice($fields, $i + 1));
				break;
				// a model only has one column with the type "tree"
				$n += 2;
				$count += 2;
			}
		}

		// auto add app fields
		if ($this->type === 'app')
		{
			$fields[] = 'title';
			$fields[] = 'lid';
			$fields[] = 'owner';
			$fields[] = 'ptime';
			$fields[] = 'ctime';
		}

		if (!$withRef)
		{
			$this->_db->select($fields)->from(array($this->name))->where($wheres)->limit(1);
			$stmt = $this->_db->run();
			return $this->data = $stmt->fetch();
		}

		// TODO: with ref, join etc.
	}
	
	/**
	 * @param array where
	 * @param boolean with ref
	 * @param options array('order' => array('aaa' => 'ASC'), 'page' => 1, 'pagesize' => 10);
	 * @return array
	 */
	public function items(array $wheres = array(), $withRef = false, array $options = array())
	{
		if (empty($this->_db)) return array();

		// data type
		$this->data = array(); // clean prev fetch
		$this->dataType = 'items';

		$fields = $this->fields;
		array_unshift($fields, 'id'); // all tables in icore framework have the column named id

		// check tree fieldtype
		$count = count($fields);
		for($i = 1; $i < $count; $i++)	
		{
			if ($this->fieldType[$fields[$i]] === 'tree')
			{
				$field = $this->fieldExtra[$fields[$i]] === $this->name ? '' : $this->fieldExtra[$fields[$i]] . '_'; // if the name is the model name, set it as empty
				$fields = array_merge(array_slice($fields, 0, $i), array($field . 'pid', $field . 'level', $field . 'path'), array_slice($fields, $i + 1));
				break;
				// a model only has one column with the type "tree"
				$n += 2;
				$count += 2;
			}
		}

		// auto add app fields
		if ($this->type === 'app')
		{
			$fields[] = 'title';
			$fields[] = 'lid';
			$fields[] = 'owner';
			$fields[] = 'ptime';
			$fields[] = 'ctime';
		}

		// orders
		$orders = isset($options['order']) ? array($options['order']) : array();
		
		// sql 
		$this->_db->select($fields)->from(array($this->name))->where($wheres)->order($orders);
		if (!isset($options['page']) && isset($options['pagesize']))
		{
			$this->_db->limit($options['pagesize']);
		}
		if (isset($options['page']) && isset($options['pagesize']))
		{
			$offset = ($options['page'] - 1) * $options['pagesize'];
			$this->_db->limit($options['pagesize'], $offset);
		}

		if (!$withRef)
		{
			$stmt = $this->_db->run();
			while ($item = $stmt->fetch())
			{
				$this->data[] = $item;
			}
			return $this->data;
		}

		// TODO: with ref, join etc.
	}

	public function map($wheres = array(), $key = 'name', $value = 'value')
	{
		if (empty($this->_db)) return array();

		$stmt = $this->_db->select(array($key, $value))->from(array($this->name))->where($wheres)->run();
		$data = array();
		while ($row = $stmt->fetch())
		{
			$data[$row[$key]] = $row[$value];
		}
		return $data;
	}

	public function values($wheres = array(), $value = 'value')
	{
		if (empty($this->_db)) return array();
		
		$stmt = $this->_db->select(array($value))->from(array($this->name))->where($wheres)->run();
		return $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
	}
}
