<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Log extends Zend_Db_Table_Abstract
{

	protected $_name = 'log';
	
	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('l'=>'log'),
			array('*')
		);
		
		$row->joinLeft(
			array('u'=>'usuarios'),
			'u.id = l.id_usuario',
			array('nome')
		);
		
		if($arr['data']){
			
			$row->where("data='".$arr['data']."'");
			
		}
		
		if($arr['id_usuario']){
			
			$row->where("id_usuario=".$arr['id_usuario']);
			
		}
		
		$row->order('l.data');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}

	private function log($query, $descricao){

		$sql = "INSERT INTO log (id_usuario,query,descricao,hora) VALUES (".$_SESSION['sessionUser']['id'].",\"".var_export($query, 1)."\",'".$descricao."',NOW())";
		
		$db->query($sql);

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
