<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Fipe extends Zend_Db_Table_Abstract
{

	protected $_name = 'modelo';
	
	public function getMarcas(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'marca'),
		array('*')
		);

		//$row->limit(100);
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
		
	}
	
	public function getModelos(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelo'),
		array('*')
		);

		//$row->limit(6353);
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
		
	}
	
	public function getAnosModelos(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('am'=>'ano_modelo'),
		array('*')
		);

		//$row->where("tipo='moto'");
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
		
	}

}
