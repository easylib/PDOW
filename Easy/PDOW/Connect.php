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
	private $port = 3306;
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
		$this->file = $file;
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
	public function setPort($port)
	{
		$this->port = $port;
	}
	public function createConnection($utf8 = false)
	{
		if($this->typ=="sqlite")
		{
			$this->db = new \PDO('sqlite:'.$this->file);
		}
		if($this->typ=="mysql")
		{
			$this->db = new \PDO("mysql:host=".$this->server.";port=".$this->port.";dbname=".$this->database."", $this->username, $this->password);
			$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}
		if($utf8)
		{
			$this->connectionUTF8();
		}
	}
	public function connectionUTF8()
	{
			$this->db->query("SET CHARACTER SET utf8;");
			$this->db->query("SET NAMES utf8;");
	}
}
?>
