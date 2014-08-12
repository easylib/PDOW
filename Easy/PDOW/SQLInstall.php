<?php
namespace Easy\PDOW;
class SQLInstall extends PDOW
{
	private $libs = array();
	public function __construct()
	{
		parent::__construct();
		$this->addLib("\Easy\PDOW\SQLInstall");
	}
	public function addLib($name);
	{
		$this->libs[] = $name;
	}
	public function install()
	{
		$statusArray = array();
		foreach($this->libs as $lib)
		{
			$installClass = new $lib;
			if(!is_object($installClass))
			{
				$statusArray[] = array("status"=>False, "name"=>$lib, "reason"=>"No Object");
				continue;
			}
			if(!method_exists($installClass, "installQuery"))
			{
				$statusArray[] = array("status"=>False, "name"=>$lib, "reason"=>"No Function installQuery");
				continue;
			}
			list($version, $querys) = $installClass->installQuery();
			$this->beginTransaction();
			foreach($querys as $query)
			{
				$this->insert($query);
			}
			$this->commit();
			$this->insertVersion($lib, $version, false);

		}
	}
	private function insertVersion($name, $version, $checkUpdate = true)
	{
		$create = true;
		if($checkUpdate==true)
		{
			$create = false;
			$r = $this->fetchOne('SELECT * FROM `installQuery` WHERE `name``= ?', array($name));
			if($r===false)
			{
				$create = true;
			}
			else
			{
				$this->insert('UPDATE `installQuery` SET `version`=? WHERE `name` = ?', array($version, $name));
			}

		}
		if($create)
		{
			$this->insert('INSERT INTO `installQuery`(`id`, `name`, `version`) VALUES (NULL, ?, ?)', array($name, $version));
		}

	}
	//Own Install Class
	public function installQuery()
	{
		$querys = array();
		$querys[] = 'CREATE TABLE IF NOT EXISTS `installQuery` (`id` int(255) NOT NULL, `name` varchar(255) NOT NULL, `version` int(255) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
		$querys[] = 'ALTER TABLE `installQuery` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`id`);';
		$querys[] = 'ALTER TABLE `installQuery` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;'
		return array(1, $querys);
	}
}
?>