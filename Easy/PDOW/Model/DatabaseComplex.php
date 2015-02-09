<?php
namespace Easy\PDOW\Model;

class DatabaseComplex extends \Easy\PDOW\Model\DatabaseBasic
{
	protected $_dbName = NULL;
	protected $_relations = NULL;

	public function __construct($name, $id = NULL)
	{
		parent::__construct($name, $id);
		$this->getDatbase();
		$this->getRelations();
	}
	static public function find($key, $value)
	{
		$cn = get_called_class();
		$db = self::getDB();
		$tmp = new $cn(); # Fix Problem with not Createt Varieables
		unset($tmp);      # Fix Problem with not Createt Varieables
		$sql = 'SELECT `id` FROM '.self::$_tableNameStatic.' WHERE `'.$key.'` = ?';
		$res = $db->query($sql, array($value));
		if(count($res)==1)
		{
			#var_dump($cn);
			#var_dump(array(self::$_tableNameStatic, $res[0]["id"]));
			$class =  new $cn($res[0]["id"]);
			#var_dump(get_class($class));
			return $class;
		}
		elseif(count($res)>1)
		{
			$re = array();
			foreach($res as $r)
			{
				$re[] = new $cn($r["id"]);
			}
			return $re;
		}
		else
		{
			return false;
		}
	}

	public function __set($property, $value)
	{
		return $this->set($property, $value);
	}
	public function __get($property)
	{
		if(isset($this->_relations[$property]))
		{
			$cn = ucfirst($this->_relations[$property]["REFERENCED_TABLE_NAME"]);
			if(class_exists($cn))
			{
				return $cn::find($this->_relations[$property]["REFERENCED_COLUMN_NAME"], $this->get($property, true));
			}
			else
			{
				return $this->get($property);
			}
		}	
		else
		{
			return $this->get($property);
		}
	}

	private function getDatbase()
	{
		$res = $this->db->query('SELECT DATABASE();');
		$this->_dbName = $res[0][0];
	}
	private function getRelations()
	{
		$sql = 'SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND REFERENCED_TABLE_SCHEMA IS NOT NULL';
		$res = $this->db->query($sql, array($this->_dbName, $this->_tableName));
		$re = array();
		foreach($res as $r)
		{
			$re[$r["COLUMN_NAME"]] = array("REFERENCED_TABLE_NAME"=>$r["REFERENCED_TABLE_NAME"], "REFERENCED_COLUMN_NAME"=>$r["REFERENCED_COLUMN_NAME"]);
		}
		$this->_relations= $re;
	}
}