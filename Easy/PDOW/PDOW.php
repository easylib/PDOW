<?php
namespace Easy\PDOW;
class PDOW
{
	private $db;
	public function connectConfig($config, $group = "DEFAULT", $utf8 = false)
	{
		#var_dump($config->get("host", "DB"));exit();
		$this->db = new \PDO("mysql:host=".$config->get("host", $group).";dbname=".$config->get("db", $group)."", $config->get("user", $group), $config->get("pass", $group)); 
		$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		if($utf8)
		{
			$this->db->query("SET CHARACTER SET utf8;");
			$this->db->query("SET NAMES utf8;");
		}
	}
	public function connect($host, $user, $pw, $db, $utf8 = false)
	{
		#var_dump($config->get("host", "DB"));exit();
		$this->db = new \PDO("mysql:host=".$host.";dbname=".$db."", $user, $pw); 
		$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		if($utf8)
		{
			$this->db->query("SET CHARACTER SET utf8;");
			$this->db->query("SET NAMES utf8;");
		}
	}
	public function beginTransaction()
	{
		return $this->db->beginTransaction();
	}
	public function rollBack()
	{
		return $this->db->rollBack();
	}
	public function commit()
	{
		return $this->db->commit();
	}
	/*
	public function dbModel($db = NULL, $utf8 = true)
	{
		require '../config/mysql.config.php';
		if($db!=NULL)
		{
			$mysql['db']= $db;
		}
		//$this->db = new mysqli($mysql["host"], $mysql["user"], $mysql["pass"], $mysql["db"]);
		$this->db = new PDO("mysql:host=".$mysql['host'].";dbname=".$mysql['db']."", $mysql["user"], $mysql["pass"]); 
	   $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	   if($utf8)
	   {
	   $this->db->query("SET CHARACTER SET utf8;");
		$this->db->query("SET NAMES utf8;");
	   }
	}
	*/
	public function insert($statment, $data = array())
	{
		try { 
		$STH = $this->db->prepare($statment); 
		$STH->execute($data);
		$STH->CloseCursor();
		}
		catch(PDOException $e) {
			echo $e;  
			}  
	}
	public function insertID($statment, $data = array())
	{
		try {
			$STH = $this->db->prepare($statment);
			$STH->execute($data);
			$STH->CloseCursor();
			$id = $this->query("SELECT LAST_INSERT_ID();", array());
			return $id[0][0];
		}
		catch(PDOException $e) {
			echo "<pre>".$e."</pre>";
		}
	}
	public function query($statment, $data = array())
	{
				try { 
		$STH = $this->db->prepare($statment); 
		$STH->execute($data);
		$re = array();
		while($row = $STH->fetch()) {
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
