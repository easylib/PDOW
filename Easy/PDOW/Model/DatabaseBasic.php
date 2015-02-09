<?php
namespace Easy\PDOW\Model;

class DatabaseBasic extends \Easy\PDOW\Model\Basic
{
	private $checkStructur = true; //Enabled or Dissabled the structur Check
	public function __construct($name, $id = NULL)
	{
		$this->name = $name;
		parent::__construct();
		if($id!=NULL)
		{
			$this->checkDB();
			$res = $this->db->query("SELECT * FROM `".$name."` WHERE `id` = ?", array($id));
			if(count($res)==1)
			{
				$this->data = $res[0];
			}
		}
		$this->structurCheck = new \Easy\PDOW\Structur\Check($name, $this->db);
	}
	public function get($param, $data = false)
	{
		if($data == false && method_exists($this, "get_".$param))
		{
			$name = "get_".$param;
			return $this->$name();
		}
		return $this->data[$param];
	}
	public function set($param, $value)
	{
		if($this->checkStructur)
		{
			try
			{
				$check = $this->structurCheck->checkEntry($param, $value);
				$check = true;
			}
			catch(\Exception $e)
			{
				$check = false;
			}
		}
		else
		{
			$check = true;
		}
		if($check == true)
		{
			$this->data[$param] = $value;
			$sql = 'UPDATE `'.$this->name.'` SET `'.$param.'`=? WHERE id = ?';
			$res = $this->db->insert($sql, array($value, $this->get("id")));
		}
	}
	public function create()
	{
		if($this->checkStructur)
		{
			try
			{
				$this->structurCheck->checkObject($this->data);
				$check = true;
			}
			catch(\Exception $e)
			{
				$check = false;
			}
		}
		else
		{
			$check = true;
		}
		if($check)
		{
			$structur = $this->getStuctur();
			$sql = "INSERT INTO `".$this->name."` (";
			$first = true;
			foreach($structur as $name => $entry)
			{
				if(!$first)
				{
					$sql .= ', ';
				}
				$sql .= '`'.$name.'`';
				$first = false;
			}
			$sql.= ") VALUES (";
			$first = true;
			foreach($structur as $name => $entry)
			{
				if(!$first)
				{
					$sql .= ', ';
				}
				$sql .= '?';
				$first = false;
			}
			$sql.= ")";
			#Create Value Array
			$params = array();
			foreach($structur as $name => $entry)
			{
				$data = NULL;
				if(isset($this->data[$name]))
				{
					$data = $this->data[$name];
				}
				$params[] = $data;
			}
			$id = $this->db->insertID($sql, $params);
			$this->__construct($this->name, $id);
		}
	}
	private function getStuctur()
	{
		$sql = 'DESCRIBE '.$name;
		$res = $this->db->query($sql, array());
		$re = array();
		foreach($res as $r)
		{
			$re[$r["Field"]] = array("Type"=>$r["Type"], "Null">$r["Null"], "Key"=>$r["Key"], "Default"=>$r["Default"], "Extra"=>$r["Extra"]);

		}
		return $re;
	}
}