<?php
namespace Easy\PDOW\Model;

class DatabaseBasic extends \Easy\PDOW\Model\Basic
{
	private $checkStructur = false; //Enabled or Dissabled the structur Check
	private $name = NULL;
	private $data = array();
	private $structurCheck;
	#private $db;
	public function __construct($name, $id = NULL)
	{
		$this->name = $name;
		parent::__construct();
		if($id!=NULL)
		{
			$this->getData($name, $id);
		}
		$this->structurCheck = new \Easy\PDOW\Structur\Check($name, $this->db);
	}
	public function setCheck($check = true)
	{
		$this->checkStructur = $check;
	}
	private function getData($name, $id)
	{
		$this->checkDB();
		$res = $this->db->query("SELECT * FROM `".$name."` WHERE `id` = ?", array($id));
		if(count($res)==1)
		{
			$this->data = $res[0];
		}
	}
	public function get($param, $data = false)
	{
		if($data == false && method_exists($this, "get_".$param))
		{
			$name = "get_".$param;
			return $this->$name();
		}
		if(isset($this->data[$param]))
		{
			return $this->data[$param];
		}
		else
		{
			throw new \Exception("Param ".$param." not found", 1);
			
		}
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
		if($check == true && isset($this->id))
		{
			$this->data[$param] = $value;
			$sql = 'UPDATE `'.$this->name.'` SET `'.$param.'`=? WHERE id = ?';
			$res = $this->db->insert($sql, array($value, $this->get("id")));
		}
		else
		{
			$this->data[$param] = $value;
		}
	}
	public function __set($property, $value)
	{
		#var_dump($property, $value);exit();
		return $this->set($property, $value);
	}
	public function __get($property)
	{
		return $this->get($property);
	}
	public function create()
	{

		if($this->checkStructur)
		{
			try
			{
				#var_dump($this->data);
				$this->structurCheck->checkObject($this->data);
				$check = true;
			}
			catch(\Exception $e)
			{
				throw $e;
				
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
			$this->getData($this->name, $id);
		}
		else
		{
			throw new \Exception("Check false", 1);
			
		}
	}
	private function getStuctur()
	{
		$sql = 'DESCRIBE '.$this->name;
		$res = $this->db->query($sql, array());
		$re = array();
		foreach($res as $r)
		{
			$re[$r["Field"]] = array("Type"=>$r["Type"], "Null">$r["Null"], "Key"=>$r["Key"], "Default"=>$r["Default"], "Extra"=>$r["Extra"]);

		}
		return $re;
	}
}