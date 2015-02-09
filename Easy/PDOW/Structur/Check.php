<?php
namespace Easy\PDOW\Structur;
class Check extends \Easy\PDOW\Model\Basic
{
	function __construct($name, $db = NULL)
	{
		if($db != NULL)
		{
			$this->setDB($db);
		}
		$this->checkDB();
		$this->getStuctur($name);
	}
	private function getStuctur($name)
	{
		$sql = 'DESCRIBE '.$name;
		$res = $this->db->query($sql, array());
		$re = array();
		foreach($res as $r)
		{
			$re[$r["Field"]] = array("Type"=>$this->phareseDBTyp($r["Type"]), "Null">$r["Null"], "Key"=>$r["Key"], "Default"=>$r["Default"], "Extra"=>$r["Extra"]);

		}
		$this->data = $re;
	}
	private function phareseDBTyp($typ)
	{
		$orginal = $typ;
		$start = strpos($typ, "(");
		if($start>0)
		{
			$typ = substr($typ, 0, $start);
			$limit = substr($typ, $start+1);
			$ende = strpos($limit, ")");
			$limit = substr($limit, 0, $ende);
			return array("Typ"=>$typ, "Orginal"=>$orginal, "Limit"=>$limit);
		}
		else
		{
			return array("Typ"=>$typ, "Orginal"=>$orginal, "Limit"=>NULL);
		}


	}
	public function checkEntry($name, $value)
	{
		if(!isset($this->data[$name]))
		{
			throw new \Exception("Entry not Found", 1);
		}
		$context = $this->data[$name];
		switch ($this->data[$name]["Typ"]["Typ"]) {
			case 'int':
				if(!is_numeric($value))
				{
					throw new \Exception("Int musst be Numeric", 1);
				}
				break;
			
			default:
				# code...
				break;
		}
		if(isset($this->data[$name]["Typ"]["Limit"]))
		{
			if(strlen($value) > $this->data[$name]["Typ"]["Limit"])
			{
				throw new \Exception("Value is too long.", 1);
			}
		}
		return true;
	}
	public function checkObject($data)
	{
		foreach($this->data as $name => $entry)
		{
			if(isset($data[$name]))
			{
				$this->checkEntry($name, $data[$name]);
			}
			else
			{
				if($entry["Null"]==false && $entry["Default"] ==NULL && $entry["Extra"]!="auto_increment")
				{
					throw new \Exception("Entry ".$name." is not set", 1);
				}
			}
			unset($data[$name]);
		}
		if(count($data)>0)
		{
			throw new \Exception("Data set which are not in the Database", 1);
			
		}
	}
}