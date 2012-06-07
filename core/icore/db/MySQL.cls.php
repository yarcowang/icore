<?php
// namespace
namespace icore\db;

use icore\DB;

// for mysql
class MySQL extends DB
{
	protected function initialize()
	{
		// dsn
		$this->_info['dsn'] = isset($this->_info['unix_socket']) && !empty($this->_info['unix_socket']) ?
			sprintf($this->_info['dsn'], $this->_info['unix_socket'], $this->_info['dbname']) :
			sprintf($this->_info['dsn'], $this->_info['host'], $this->_info['dbname'], isset($this->_info['port']) && !empty($this->_info['port']) ? $this->_info['port'] : 3306);

		$this->_pdo = new \PDO($this->_info['dsn'], $this->_info['username'], $this->_info['password'], array(
				\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
				\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8', // you must create your database in UTF8
			)
		);
	}

	// _buff
	public function select(array $columns = array())
	{
		$this->clean();
		$this->_buff['type'] = DB_SELECT;
		$this->_buff['columns'] = $columns;
		return $this;
	}

	public function from(array $table) // once
	{
		$this->_buff['primary'] = $table;
		return $this;
	}

	public function join(array $table, $on)
	{
		$this->_buff['tables'][] = array('type' => 'INNER JOIN', 'table' => $table, 'on' => $on);
		return $this;
	}

	public function leftJoin(array $table, $on)
	{
		$this->_buff['tables'][] = array('type' => 'LEFT JOIN', 'table' => $table, 'on' => $on);
		return $this;		
	}

	public function rightJoin(array $table, $on)
	{
		$this->_buff['tables'][] = array('type' => 'RIGHT JOIN', 'table' => $table, 'on' => $on);
		return $this;
	}

	public function innerJoin(array $table, $on)
	{
		return $this->join($table, $on);
	}

	public function where(array $wheres)
	{
		$this->_buff['wheres'] = array_merge($this->_buff['wheres'], $wheres);
		return $this;
	}

	// TODO: having ?

	public function order(array $orders)
	{
		$this->_buff['orders'] = array_merge($this->_buff['orders'], $orders);
		return $this;		
	}

	public function limit($size, $offset = 0)
	{
		$this->_buff['size'] = $size;
		$this->_buff['offset'] = $offset;
		return $this;
	}

	// other ops
	public function insertInto(array $table, array $data) // once
	{
		$this->clean();
		$this->_buff['type'] = DB_INSERT;
		$this->_buff['primary'] = $table;
		$this->_buff['columns'] = array_keys($data);
		$this->_buff['placeholders'] = count($data);
		$this->_buff['values'] = array_values($data);
		return $this;
	}

	public function updateFrom(array $table, array $data) // once
	{
		$this->clean();
		$this->_buff['type'] = DB_UPDATE;
		$this->_buff['primary'] = $table;
		$this->_buff['columns'] = array_keys($data);
		$this->_buff['values'] = array_values($data);
		return $this;
	}

	public function replaceInto(array $table, array $data) // once
	{
		$this->clean();
		$this->_buff['type'] = DB_REPLACE;
		$this->_buff['primary'] = $table;
		$this->_buff['columns'] = array_keys($data);
		$this->_buff['placeholders'] = count($data);
		$this->_buff['values'] = array_values($data);
		return $this;
	}

	public function deleteFrom(array $table) // once
	{
		$this->clean();
		$this->_buff['type'] = DB_DELETE;
		$this->_buff['primary'] = $table;
		return $this;
	}

	public function truncate(array $table) // once
	{
		$this->clean();
		$this->_buff['type'] = DB_TRUNCATE;
		$this->_buff['primary'] = $table;
		return $this;
	}

	public function fieldInfo2ColumnType(array $fieldInfo)
	{
	}

/*
public function createColumnFromType($name, $type, $extra, $required)
	{
		switch($type)
		{
			case 'int':
				return sprintf("%s INTEGER NOT NULL DEFAULT 0", $name);
			case 'float':
				return sprintf("%s DOUBLE NOT NULL DEFAULT 0.0", $name);
			case 'name':
				if (empty($extra)) $extra = 40;
				return sprintf("%s VARCHAR(%d) NOT NULL DEFAULT ''", $name, $extra);
			case 'title':
				if (empty($extra)) $extra = 120;
				return sprintf("%s VARCHAR(%d) NOT NULL DEFAULT ''", $name, $extra);
			case 'string':
				if (empty($extra)) $extra = 200;
				return sprintf("%s VARCHAR(%d) NOT NULL DEFAULT ''", $name, $extra);
			case 'text':
				return sprintf("%s TEXT", $name);
			case 'password':
				return sprintf("%s VARCHAR(40) NOT NULL DEFAULT ''", $name);
			case 'time':
				switch($extra)
				{
					case 'ctime':
						return sprintf("%s TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", $name);
					case 'mtime':
						return sprintf("%s TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", $name);
					case 'datetime':
					default:
						return sprintf("%s DATETIME NOT NULL DEFAULT 0", $name);
				}
			case 'mail':
				return sprintf("%s VARCHAR(40) NOT NULL DEFAULT ''", $name);
			case 'enum':
				$values = explode(',', $extra);
				$default = empty($values[0]) ? 'DEFAULT NULL' : sprintf("DEFAULT '%s'", $values[0]);
				if (empty($values[0]))
				{
					array_shift($values);
				}
				
				$enum = implode("','", $values);
				return sprintf("%s ENUM('%s') NOT NULL %s", $name, $enum, $default);
			default:
				return '';
		}
	}

	public function createColumnFromFile($name, $extra, $required)
	{
		return sprintf("%s INTEGER NOT NULL DEFAULT 0", $name);
	}

	public function createColumnFromImage($name, $extra, $required)
	{
		return sprintf("%s INTEGER NOT NULL DEFAULT 0", $name);	
	}

	public function createColumnFromMref($name, $extra, $required)
	{
		return sprintf("%s INTEGER NOT NULL FOREIGN KEY (%s) REFERENCES %s%s (id) ON DELETE CASCADE", $name, $name, $this->_info['tableprefix'], $extra);
	}

	public function createColumnFromRef($name, $extra, $required)
	{
		return sprintf("%s INTEGER NOT NULL, FOREIGN KEY (%s) REFERENCES %s%s (id) ON DELETE CASCADE", $name, $name, $this->_info['tableprefix'], $extra);
	}

	public function createColumnFromTree($name, $extra, $required)
	{
		return sprintf("%s_pid INTEGER NOT NULL DEFAULT 0,\n%s_path TEXT,\n%s_level INTEGER NOT NULL DEFAULT 0", $name, $name, $name);
	}

*/

	public function create(array $table, array $data)
	{
		$this->clean();
		$this->_buff['type'] = DB_TABLE_CREATE;
		$this->_buff['primary'] = $table;
		$this->_buff['columns'] = $data;	
		return $this;
	}

	public function drop(array $table)
	{
		$this->clean();
		$this->_buff['type'] = DB_TABLE_DROP;
		$this->_buff['primary'] = $table;
		return $this;
	}

	// get sql
	public function sql()
	{
		switch($this->_buff['type'])
		{
			case DB_SELECT:
				// check
				if (empty($this->_buff['columns']))
				{
					throw new \Exception('DB_SELECT must have at least one column');
				}
				if (empty($this->_buff['primary']))
				{
					throw new \Exception('DB_SELECT must have a primary table');
				}

				// select from
				$sql = "SELECT\n";
				$columns = $this->_buff['columns'];
				array_walk($columns, create_function('& $v, $k', 'if (is_string($k)) $v = $v . " AS " . $k;'));
				$sql .= implode(', ', $columns) . "\n";
				$sql .= 'FROM ' . $this->array2table($this->_buff['primary']) . "\n";

				// joined tables
				foreach($this->_buff['tables'] as $table)
				{
					$sql .= sprintf("%s %s ON %s", $table['type'], $this->array2table($table['table']), $table['on']) . "\n";
				}

				// where
				$sql .= $this->array2where();
				
				// TODO: having?

				// order
				if (!empty($this->_buff['orders']))
				{
					$orders = $this->_buff['orders'];
					array_walk($orders, create_function('& $v, $k', '$v = $k . " " . $v;'));
					$sql .= 'ORDER BY ' . implode(', ', $orders) . "\n";
				}

				// limit
				if (!empty($this->_buff['size']))
				{
					$sql .= empty($this->_buff['offset']) ? sprintf("LIMIT %d\n", $this->_buff['size']) : sprintf("LIMIT %d, %d\n", $this->_buff['offset'], $this->_buff['size']);
				}
				return $sql;

			case DB_INSERT:
				// check
				if (empty($this->_buff['columns']))
				{
					throw new \Exception('DB_INSERT must have at least one column');
				}
				if (empty($this->_buff['primary']))
				{
					throw new \Exception('DB_INSERT must have a primary table');
				}

				$sql = 'INSERT INTO ' . $this->array2table($this->_buff['primary']) . "\n";
				$sql .= '(' . implode(', ', $this->_buff['columns']) . ")\n";
				$sql .= "VALUES\n";
				$sql .= '(' . implode(', ', array_fill(0, $this->_buff['placeholders'], '?')) . ")\n";
				return $sql;

			case DB_UPDATE:
				// check
				if (empty($this->_buff['columns']))
				{
					throw new \Exception('DB_UPDATE must have at least one column');
				}
				if (empty($this->_buff['primary']))
				{
					throw new \Exception('DB_UPDATE must have a primary table');
				}	

				$sql = 'UPDATE ' . $this->array2table($this->_buff['primary']) . "\n";
				$sql .= "SET\n";
				$sql .= $this->column4update();
				$sql .= $this->array2where();
				return $sql;

			case DB_REPLACE:
				// check
				if (empty($this->_buff['columns']))
				{
					throw new \Exception('DB_REPLACE must have at least one column');
				}
				if (empty($this->_buff['primary']))
				{
					throw new \Exception('DB_REPLACE must have a primary table');
				}

				$sql = 'INSERT INTO ' . $this->array2table($this->_buff['primary']) . "\n";
				$sql .= '(' . implode(', ', $this->_buff['columns']) . ")\n";
				$sql .= "VALUES\n";
				$sql .= '(' . implode(', ', array_fill(0, $this->_buff['placeholders'], '?')) . ")\n";
				$sql .= "ON DUPLICATE KEY UPDATE\n";
				$sql .= $this->column4update();

				// duplicate values
				$this->_buff['values'] = array_merge($this->_buff['values'], $this->_buff['values']);
				return $sql;

			case DB_DELETE:
				// check
				if (empty($this->_buff['primary']))
				{
					throw new \Exception('DB_DELETE must have a primary table');
				}

				$sql = 'DELETE FROM ' . $this->array2table($this->_buff['primary']) . "\n";
				$sql .= $this->array2where();	
				return $sql;

			case DB_TRUNCATE:
				// check
				if (empty($this->_buff['primary']))
				{
					throw new \Exception('DB_TRUNCATE must have a primary table');
				}

				$sql = 'TRUNCATE TABLE ' . $this->array2table($this->_buff['primary']) . "\n";
				return $sql;

			// table management
			case DB_TABLE_CREATE:
				// check
				if (empty($this->_buff['columns']))
				{
					throw new \Exception('DB_TABLE_CREATE must have at least one column');
				}
				if (empty($this->_buff['primary']))
				{
					throw new \Exception('DB_TABLE_CREATE must have a primary table');
				}

				$sql = 'CREATE TABLE IF NOT EXISTS ' . $this->array2table($this->_buff['primary']) . "\n";
				$sql .= "(\n";
				foreach($this->_buff['columns'] as $column)
				{
					$sql .=	$this->fieldInfo2ColumnType($column) . "\n";
				}
				$sql .= "id INTEGER PRIMARY KEY AUTO_INCREMENT\n";
				$sql .= ");\n";
				return $sql;

			case DB_TABLE_DROP:
				// check
				if (empty($this->_buff['primary']))
				{
					throw new \Exception('DB_TABLE_DROP must have a primary table');
				}

				$sql = 'DROP TABLE IF EXISTS ' . $this->array2table($this->_buff['primary']) . "\n";	
				return $sql;
		}
	}

	/**
	 * @return bool or PDOStatement
	 */
	public function run($batchMode = false)
	{
		if ($batchMode)
		{
			if (!$this->_batchMode)
			{
				$this->_pdo->beginTransation();
				$this->_batchMode = true;
			}
		}

		$sql = $this->sql();
		$this->sqls[] = $sql;

		switch($this->_buff['type'])
		{
			case DB_TRUNCATE:
			case DB_TABLE_CREATE:
			case DB_TABLE_DROP:
				$ret = $this->_pdo->exec($sql);
				if ($ret === false)
				{
					$info = $this->_pdo->errorInfo();
					throw new \Exception($info[2]);
					return false;
				}

			default:
				$stmt = $this->_pdo->prepare($sql);
				if ($this->_buff['type'] === DB_SELECT && !(is_a($stmt, 'PDOStatement')))
				{
					$info = $this->_pdo->errorInfo();
					throw new \Exception($info[2]);
					return false;
				}

				$ret = $stmt->execute($this->_buff['values']);
				if (!$ret)
				{
					$info = $stmt->errorInfo();
					throw new \Exception($info[2]);
					return false;
				}
		}

		if (!$batchMode)
		{
			if ($this->_batchMode)
			{
				$this->_pdo->commit();
				$this->_batchMode = false;
			}
		}

		// return
		if ($this->_buff['type'] == DB_SELECT)
		{
			$stmt->setFetchMode(\PDO::FETCH_ASSOC);
			return $stmt;
		}
		else
		{
			return $ret;
		}
	}
}
