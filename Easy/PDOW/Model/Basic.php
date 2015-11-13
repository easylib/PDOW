<?php
namespace Easy\PDOW\Model;

class Basic
{
	protected $db;
	public function __construct()
	{
		$this->db = new \Easy\PDOW\PDOW();
	}
	/**
	 * Set Database Connection
	 *
	 * Set a PDOW Class as Database Connection for the Class
	 *
	 * @param $db A Instance of \Easy\PDOW\PDOW
	 */
	public function setDB($db)
	{
		$this->db = $db;
	}
	/**
	 * Check if there is a Database Connection
	 *
	 * Check if there a Database Connection. Cann used in Classes with extends from Basic.
	 */
	public function checkDB()
	{
		if(!isset($this->db))
		{
			throw new \Exception("No Database Connection Instance set", 1);
		}
	}
	/**
	 * Get Database Connection for Static Classes
	 *
	 * To use the Database Connection in Static Fucntion this return the PDOW Class
	 *
	 * @return Instance of \Easy\PDOW\PDOW
	 */
	static public function getDB()
	{
		$db = new \Easy\PDOW\PDOW();
		return $db;
	}
}
