<?php
namespace Easy\PDOW;
class Connect
{
	private $typ = NULL;
	private $file = NULL;
	protected $db = NULL;
	private $username = NULL;
	private $password = NULL;
	private $server = NULL;
	private $database = NULL;
	public function setTyp($typ)
	{
		if($typ=="mysql")
		{
			$this->typ = "mysql";
		}
		if($typ=="sqlite")
		{
			$this->typ = "sqlite";
		}
	}
	public function setFile($file)
	{
		if($this->typ=="sqlite")
		{
			$this->file = $file;
		}
	}
	public function setUsername($user)
	{
		$this->username = $user;
	}
	public function setServer($server)
	{
		$this->server = $server;
	}
	public function setDatabase($db)
	{
		$this->database = $db;
	}
	public function setPassword($password)
	{
		$this->password = $password;
	}
	public function createConnection()
	{
		if($this->typ=="sqlite")
		{
			$this->db = new \PDO('sqlite:'.$this->file); 
		}
		if($this->typ=="mysql")
		{
			$this->db = new \PDO("mysql:host=".$this->server.";dbname=".$this->database."", $this->username, $this->password); 
		}
	}
}
?>