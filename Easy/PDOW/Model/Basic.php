<?php
namespace Easy\PDOW\Model;

class Basic
{
	public function __construct()
	{
		$this->db = new \Easy\PDOW\PDOW();
	}
	public function setDB()
	{
		$this->db = $db;
	}
	public function checkDB()
	{
		if(!isset($this->db))
		{
			throw new \Exception("No Database Connection Instance set", 1);
		}
	}
	static public function getDB()
	{
		$db = new \Easy\PDOW\PDOW();
		return $db;
	}
}