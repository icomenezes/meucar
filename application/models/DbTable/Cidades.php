<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Cidades extends Zend_Db_Table_Abstract
{

	protected $_name = 'Cidade';
	
	
	public function getCidadeString(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('c'=>'Cidade'),
		array('*')
		);

		$row->order('id DESC');
		
		return $row->query()->fetchAll();
		
		//echo $row->__toString();
		
	}

	private function getConnDb(){

		$db = new Zend_Db_Adapter_PDO_MYSQL(array(
			'host'     => HOST,
			'username' => USER,
			'password' => PASS,
			'dbname'   => DB
		));

		return $db;

	}

}
