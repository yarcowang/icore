<?php
// namespace
namespace icore;

/**
 * read write model
 */
class ModelX extends Model
{
	// records related ops
	/**
	 * @return false when success, error string when failed
	 */
	public function add(array $data)
	{
		if (empty($this->_db)) return true;

		foreach($this->fields as $k)
		{
			if ($this->fieldRequired[$k] && !isset($data[$k]))
			{
				return 'field required';
			}

			$class = 'icore\\types\\' . $this->fieldType[$k];
			$obj = new $class;
			$obj->extra = $this->fieldExtra[$k];
			if (!$obj->validate($data[$k]))
			{
				return $this->fieldType[$k] . ' format error';
			}
		}

		// extra checking for type=app
		if ($this->type === 'app')
		{
			// you must set a title
			if (!isset($data['title']) || empty($data['title']))
			{
				return 'title required';
			}

			// you must set an lid
			if (!isset($data['lid']))
			{
				return 'language required';
			}

			// set owner
			$data['owner'] = Application::get()->user['id'];
			// set ctime
			$date['ctime'] = date('Y-m-d H:i:s');
		}

		try
		{
			$ret = $this->_db->insertInto(array($this->name), $data)->run();
			if ($ret) return false;
		}
		catch (\Exception $e)
		{
			return $e->getMessage();
		}
	}

	public function replace(array $data)
	{
		if (empty($this->_db)) return true;

		foreach($data as $k => $v)
		{
			if (!in_array($k, $this->fields))
			{
				continue;
			}

			if ($this->fieldRequired[$k] && !isset($data[$k]))
			{
				return 'field required';
			}

			$class = 'icore\\types\\' . $this->fieldType[$k];
			$obj = new $class;
			$obj->extra = $this->fieldExtra[$k];
			if (!$obj->validate($data[$k]))
			{
				return $this->fieldType[$k] . ' format error';
			}
		}		

		try
		{
			$ret = $this->_db->replaceInto(array($this->name), $data)->run();
			if ($ret) return false;
		}
		catch (\Exception $e)
		{
			return $e->getMessage();
		}
	}

	public function remove(array $wheres)
	{
		if (empty($this->_db)) return true;

		try
		{
			$ret = $this->_db->deleteFrom(array($this->name))->where($wheres)->run();
			if ($ret) return false;
		}
		catch (\Exception $e)
		{
			return $e->getMessage();
		}
	}

	// TODO: model remove etc.

/**
	public function saveModelToDB()
	{
		var_dump($this);	
	}

	public function saveModelToFile()
	{
	}
**/
}
