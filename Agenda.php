<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Agenda extends Zend_Db_Table_Abstract
{

	protected $_name = 'agenda';
	
	public function getLastId(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('e'=>'empresas'),
		array('id')
		);
		  
		$row->order('id DESC');
		$row->limit(1);
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
	
	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('a'=>'agenda'),
			array('*')
		);
		
		if($arr['id_usuario']){
			
			$row->where('a.id_usuario = ' . $arr['id_usuario']);
		
		}
		
		if($arr['data_inicial'] || $arr['data_final']){
			
			$row->where("a.data BETWEEN '".$arr['data_inical']."' AND '".$arr['data_final']."'");
			
		}
		
		$row->where('a.baixado = 0');
	
		$row->order('a.data');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}

}
