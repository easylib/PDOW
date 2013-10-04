<?php
namespace Easy\PDOW;
class PDOW
{
	private $db;
	public function __construct($config, $group = "DEFAULT")
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
	public function insert($statment, $data)
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
	public function query($statment, $data)
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
		//OLD
		/*
		$STH = $this->db->query($statment); 
		$STH->setFetchMode(PDO::FETCH_ASSOC);
		$re = array();
		$STH->fetch($data);
		while($row = $STH->fetch()) {
			$re[] = $row;   
		}    
		return $re;
		 * 
		 */
	}
}
?>