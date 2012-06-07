<?php
// namespace
namespace icore;

class DBManager extends SingletonClass
{
	protected $_dbs = array(
		'rw' => array(),
		'ro' => array()
	);
	protected $_priority = array(
		'rw' => array(),
		'ro' => array()
	);

	// class static methods
	protected static function & _instance_()
	{
		static $instance;
		return $instance;
	}

	public function init(array $info)
	{
		if (!isset($info['db']) || empty($info['db']))
			return;

		foreach ($info['db'] as $flag => $dbs)
		{
			foreach ($dbs as $n => $db)
			{
				// priority & db
				$this->_priority[$flag][$n] = isset($db['priority']) && !empty($db['priority']) ? intval($db['priority']) : 1;
				$this->_dbs[$flag][$n] = DB::get($db['driver'])->init($db);
			}
		}
	}

	/**
	 * get a pdo object according to the flag
	 *
	 * @param string rw for read/write, ro for readonly
	 * @return PDO
	 */
	public function getDbByFlag($flag = 'rw')
	{
		// ref
		$dbs = & $this->_dbs[$flag];
		$priority = & $this->_priority[$flag];

		$which = Utilities::array4priority($priority);
		return $dbs[$which];
	}
}
