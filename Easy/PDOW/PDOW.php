<?php
namespace Easy\PDOW;
class PDOW extends Connect
{
	#äprivate $db;
	static private $dbs = NULL;
	static private $defaultTransaction = false;
	private $openTransaction = false;
	public function __construct()
	{
		if(self::$dbs!=NULL)
		{
			$this->db = self::$dbs;
		}
		$this->fetch = \PDO::FETCH_BOTH;
	}
	public function connectConfig($config, $group = "DEFAULT", $utf8 = false)
	{
		trigger_error("Deprecated: Function connectConfig() is deprecated.");
		#var_dump($config->get("host", "DB"));exit();
		$this->db = new \PDO("mysql:host=".$config->get("host", $group).";dbname=".$config->get("db", $group)."", $config->get("user", $group), $config->get("pass", $group));
		$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		if($utf8)
		{
			$this->db->query("SET CHARACTER SET utf8;");
			$this->db->query("SET NAMES utf8;");
		}
	}
	public function createStatic()
	{
		self::$dbs = $this->db;
	}
	public function setDefaultTransaction($value)
	{
		if(is_bool($value))
		{
			self::$defaultTransaction = $value;
		}
		else {
			throw new \Exception("DefaultTransaction value must be bool");
		}
	}
	public function beginTransaction()
	{
		$this->openTransaction = true;
		return $this->db->beginTransaction();
	}
	public function rollBack()
	{
		$this->openTransaction = false;
		$r = $this->db->rollBack();
		if(self::$defaultTransaction===true)
		{
			$this->beginTransaction();
		}
		return $r;
	}
	public function commit()
	{
		$this->openTransaction = false;
		$r = $this->db->commit();
		if(self::$defaultTransaction===true)
		{
			$this->beginTransaction();
		}
		return $r;
	}
	public function insert($statment, $data = array())
	{
		if(!is_array($data))
		{
			$data = array($data);
		}
		try {
		$STH = $this->db->prepare($statment);
		$STH->execute($data);
		$STH->CloseCursor();
		}
		catch(PDOException $e) {
			throw $e;
			#echo $e;
			}
	}
	public function insertID($statment, $data = array())
	{
		if(!is_array($data))
		{
			$data = array($data);
		}
		try {
			$STH = $this->db->prepare($statment);
			$STH->execute($data);
			$STH->CloseCursor();
			$id = $this->query("SELECT LAST_INSERT_ID();", array());
			if(isset($id[0]["LAST_INSERT_ID()"]))
			{
				return $id[0]["LAST_INSERT_ID()"];
			}
			return $id[0][0];
		}
		catch(PDOException $e) {
			throw $e;
		}
	}
	public function setFetch($f)
	{
		$this->fetch  = $f;
	}
	public function query($statment, $data = array())
	{
		if(!is_array($data))
		{
			$data = array($data);
		}
				try {
		$STH = $this->db->prepare($statment);
		$STH->execute($data);
		$re = array();
		while($row = $STH->fetch($this->fetch)) {
			$re[] = $row;
		}
		$STH->CloseCursor();
		return $re;
		}
		catch(PDOException $e) {
			echo $e;
			}
	}
	public function fetchOne($statment, $data = array())
	{
		$res = $this->query($statment, $data);
		if(count($res)==1)
		{
			return $res[0];
		}
		return false;
	}
	public function fetchOneColum($statment, $data = array(), $colum = 0)
	{
		$re = array();
		$res = $this->query($statment, $data);
		foreach($res as $r)
		{
			$re[] = $r[$colum];
		}
		return $re;
	}
	public function fetchOneEntry($statment, $data = array(), $colum = 0)
	{
		$re = false;
		$res = $this->query($statment, $data);
		if(isset($res[0][$colum]))
		{
			$re = $res[0][$colum];
		}
		return $re;
	}
}
?>
