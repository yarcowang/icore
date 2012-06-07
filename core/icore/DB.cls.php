<?php
// namespace
namespace icore;

// icore envelop the following statements
define('DB_SELECT', 1);
define('DB_INSERT', 2);
define('DB_UPDATE', 3);
define('DB_REPLACE', 4);
define('DB_DELETE', 5);
define('DB_TRUNCATE', 6);
define('DB_TABLE_CREATE', 7);
define('DB_TABLE_DROP', 8);
// TODO: need alter table?

// some common constants 
// order
define('ASC', 'ASC');
define('DESC', 'DESC');

abstract class DB extends SingletonClass
{
	// sql executed
	public $sqls = array();

	// connection info
	protected $_info = array();

	// buff
	protected $_buff;
	protected $_batchMode = false;

	// components
	protected $_pdo;

	// class static methods
	protected static function _file_()
	{
		return __FILE__;
	}	

	protected static function & _instance_()
	{
		static $instance;
		return $instance;
	}

	protected function clean()
	{
		$this->_buff = array();
		$this->_buff['type'] = 0;
		$this->_buff['columns'] = array();
		$this->_buff['primary'] = array(); // primary table
		$this->_buff['tables'] = array(); // joined tables
		$this->_buff['wheres'] = array();
		// TODO: do i need having?
		$this->_buff['orders'] = array();
		$this->_buff['size'] = 0;
		$this->_buff['offset'] = 0;
		$this->_buff['placeholders'] = 0;
		$this->_buff['values'] = array();
	}

 	public function __construct()
	{
		$this->clean();
	}

	public function init(array $info)
	{
		$this->_info = $info;
		$this->initialize();
		return $this;
	}

	// YOU MUST INSTANCE A PDO OBJECT IN THIS METHOD IN SUBCLASS
	abstract protected function initialize(); 

	/**
	 * NOTICE: 
	 * when you use pdo internal function, take care of table prefix 
	 * use TABLEPREFIX_ (== $this->_info['tableprefix'])
	 * TODO: check args empty
	 */
	public function __call($method, $args)
	{
		// remember the sql
		if ($method == 'exec' || $method == 'query' || $method == 'prepare')
		{
			$this->sqls[] = $args[0];
		}

		// auto replace
		$args[0] = str_replace('TABLEPREFIX_', $this->_info['tableprefix'], $args[0]);
		return call_user_func_array(array($this->_pdo, $method), $args);
	}

	/**
	 * array('key' => 'table') => 'table AS key'
	 */
	protected function array2table(array $array)
	{
		list($key, $value) = each($array);
		return is_numeric($key) ? sprintf("%s%s", $this->_info['tableprefix'], $value) : sprintf("%s%s AS %s", $this->_info['tableprefix'], $value, $key);
	}

	/**
	 * array('id = ?' => 15, 'AND', 'name = ?' => 'yarco')
	 */
	protected function array2where()
	{
		$wheres = array();
		$values = array();
		foreach($this->_buff['wheres'] as $k => $v)
		{
			if (is_string($k))
			{
				$wheres[] = $k;
				$values[] = $v;
			}
			else
			{
				$wheres[] = $v;
			}
		}
		$this->_buff['values'] = array_merge($this->_buff['values'], $values);

		$sql = '';
		if (!empty($wheres))
		{
			$sql = 'WHERE ' . implode(' ', $wheres) . "\n";
		}
		return $sql;
	}

	/**
	 * internal columns to string
	 *
	 * array('a', 'b', 'c') => 'a=?, b=?, c=?'
	 */
	protected function column4update()
	{
		$columns = array();
		foreach($this->_buff['columns'] as $column)
		{
			$columns[] = $column . ' = ?';	
		}
		return implode(', ', $columns) . "\n";
	}

	// data select
	abstract public function select(array $columns = array()); // once
	abstract public function from(array $table); // once
	abstract public function join(array $table, $on);
	abstract public function leftJoin(array $table, $on);
	abstract public function rightJoin(array $table, $on);
	abstract public function innerJoin(array $table, $on);
	abstract public function where(array $wheres);
	// TODO: having ?
	abstract public function order(array $orders); // once
	abstract public function limit($size, $offset = 0); // once

	// data management
	abstract public function insertInto(array $table, array $data); // once
	abstract public function updateFrom(array $table, array $data); // once
	abstract public function replaceInto(array $table, array $data); // once
	abstract public function deleteFrom(array $table); // once
	abstract public function truncate(array $table); // once

	// TODO: table management
	abstract public function fieldInfo2ColumnType(array $fieldInfo);
	abstract public function create(array $table, array $data);
	abstract public function drop(array $table);

	// get sql
	abstract public function sql();

	/**
	 * @return bool or PDOStatement, throw Exception
	 */
	abstract public function run($batchMode = false);

	/* table manage 
	// icore type to database type
	abstract public function createColumnFromType($name, $type, $extra, $required);

	/**
	 * @return false or int
	public function createTable(Model $model)
	{
		$this->clean();
		$this->_buff['type'] = DB_TABLE_CREATE;
		$this->_buff['primary'] = array($model->info['name']);
		foreach($model->fields as $field)
		{
			// $this->_buff['columns'][] = $this->createColumnDefine($field, $model->fieldType[$field], $model->fieldTypeExtra[$field], $model->fieldRequired[$field]);
			$this->_buff['columns'][] = $this->createColumnFromType($field, $model->fieldType[$field], $model->fieldTypeExtra[$field], $model->fieldRequired[$field]);
		}
		$sql = & $this->sqls[];
		$sql = $this->sql();
		return $this->exec($sql);
	}

	/**
	 * @return false or int
	public function dropTable(Model $model)
	{
		$this->clean();
		$sql = & $this->sqls[];
		$sql = sprintf("DROP TABLE %s", $this->array2table(array($model->info['name'])));
		return $this->exec($sql);
	}

	/**
	 * @return bool or PDOStatement
	public function truncateTable(Model $model)
	{
		$this->clean();
		return $this->truncate(array($model->info['name']))->run();
	}
	*/
}

