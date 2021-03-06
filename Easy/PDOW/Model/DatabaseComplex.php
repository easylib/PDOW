<?php
namespace Easy\PDOW\Model;

class DatabaseComplex extends \Easy\PDOW\Model\DatabaseBasic
{
	protected $_dbName = NULL;
	protected $_relations = NULL;
	protected static $namespace = '\PDOW\\';

	public function __construct($name, $id = NULL, $data = null)
	{
		parent::__construct($name, $id, $data);
		$this->getDatbase();
		$this->getRelations();
		self::$_tableNameStatic = $name;
	}
	static public function find($key, $value)
	{
		$cn = get_called_class();
		#$cn = self::$namespace.$cn;
		$db = self::getDB();
		$tmp = new $cn(); # Fix Problem with not Createt Varieables
		unset($tmp);      # Fix Problem with not Createt Varieables
		$sql = 'SELECT `id` FROM `'.self::$_tableNameStatic.'` WHERE `'.$key.'` = ?';
		$res = $db->query($sql, array($value));
		if(count($res)>=1)
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
			return [];
		}
	}
	static function findAnd($filter, $returnArray = false)
	{
		$cn = get_called_class();
		#$cn = self::$namespace.$cn;
		$db = self::getDB();
		$tmp = new $cn(); # Fix Problem with not Createt Varieables
		unset($tmp);      # Fix Problem with not Createt Varieables
		$whereFilter = "";
		$whereData = [];
		$first = true;
		foreach($filter as $key => $value)
		{
			if(!$first)
			{
				$whereFilter .= " AND ";
			}
			$whereFilter .= "`".$key."` = ?";
			$whereData[] = $value;
		}
		$return = "`id`";
		if($returnArray){
			$return = "*";
		}
		$sql = 'SELECT '.$return.' FROM `'.self::$_tableNameStatic.'` WHERE '.$whereFilter;
		$res = $db->query($sql, $whereData);
		if($returnArray == true)
		{
			return $res;
		}
		$re = array();
		foreach($res as $r)
		{
			$re[] = new $cn($r["id"]);
		}
		return $re;
	}
	static public function all()
	{
		$db = self::getDB();
		$cn = get_called_class();
		$tmp = new $cn();
		unset($tmp);
		$sql = 'SELECT * FROM `'.self::$_tableNameStatic.'';
		$res = $db->query($sql);
		if(count($res)>1)
		{
			$re = array();
			foreach($res as $r)
			{
				$re[] = new $cn($r["id"], $r);
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
		if(isset($this->_relations["intern"][$property]))
		{
			$cn = ucfirst($this->_relations["intern"][$property]["REFERENCED_TABLE_NAME"]);
			$cn = self::$namespace.$cn;
			if(class_exists($cn))
			{
				return $cn::find($this->_relations["intern"][$property]["REFERENCED_COLUMN_NAME"], $this->get($property, true))[0];
			}
			else
			{
				throw new \Exception("Class ".$cn." not Found", 1);

				return $this->get($property);
			}
		}
		elseif(isset($this->_relations["extern"][$property]))
		{
			$cn = ucfirst($this->_relations["extern"][$property]["TABLE_NAME"]);
			$cn = self::$namespace.$cn;
			if(class_exists($cn))
			{
				return $cn::find($this->_relations["extern"][$property]["COLUMN_NAME"], $this->get($this->_relations["extern"][$property]["REFERENCED_COLUMN_NAME"], true))[0];
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
		$this->_relations["intern"]= $re;

		$sql = 'SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA = ? AND REFERENCED_TABLE_NAME = ?';
		$res = $this->db->query($sql, array($this->_dbName, $this->_tableName));
		$re = array();
		foreach($res as $r)
		{
			$re[$r["TABLE_NAME"]] = array("TABLE_NAME"=>$r["TABLE_NAME"], "COLUMN_NAME"=>$r["COLUMN_NAME"], "REFERENCED_COLUMN_NAME"=>$r["REFERENCED_COLUMN_NAME"]);
		}
		$this->_relations["extern"]=$re;
	}
}
