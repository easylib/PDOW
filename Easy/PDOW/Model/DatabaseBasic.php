<?php
namespace Easy\PDOW\Model;

class DatabaseBasic extends \Easy\PDOW\Model\Basic
{
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
		$this->data[$param] = $value;
		$sql = 'UPDATE `'.$this->name.'` SET `'.$param.'`=? WHERE id = ?';
		#var_dump($sql);
		#var_dump(array($value, $this->get("id")));exit();
		$res = $this->db->insert($sql, array($value, $this->get("id")));
	}
}