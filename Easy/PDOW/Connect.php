<?php
namespace Easy\PDOW;
class Connect
{
	private $typ = "mysql";
	private $file = NULL;
	protected $db = NULL;
	private $username = "root";
	private $password = NULL;
	private $server = "localhost";
	private $database = NULL;
	private $port = 3306;
	public function setTyp($typ) #Old To Remove
	{
		trigger_error("Remove setTyp!");
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
	public function createConnection($utf8 = true, $createStatic = true)
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
		if($createStatic)
		{
			$this->createStatic();
		}
	}
	public function connectionUTF8()
	{
			$this->db->query("SET CHARACTER SET utf8;");
			$this->db->query("SET NAMES utf8;");
	}
}
?>
