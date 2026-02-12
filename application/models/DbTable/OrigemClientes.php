<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_OrigemClientes extends Zend_Db_Table_Abstract
{

	protected $_name = 'origem_clientes';
	
	
	public function add($dados){
		
		try{

			return $this->insert($dados);
		
		} catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	}
	
	
	public function getLastId(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('o'=>'OrigemClientes'),
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
		//var_export($arr);
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('oc'=>'origem_clientes'),
			array('*')
		);
		
		if($arr['noDefault']){
		
			if($arr['id_empresa']){
			
				$row->where('oc.id_empresa = ' . $arr['id_empresa'] . ' OR oc.id_empresa is NULL');
			
			}
		
		}else{
			
			$row->where('oc.id_empresa is NULL');
		
		}
		
		if($arr['fl'] == 1){
			
			$row->where('oc.id <= 5 OR oc.id >= 10');
		
		}elseif($arr['fl'] == 0){
		
			$row->where('oc.id > 5 AND oc.id < 10');
		
		}
		
		//echo $row->__toString();exit;
	
		$row->order('oc.descricao');
		
		return $row->query()->fetchAll();
	
	}
	
	public function _getOrigem($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('oc'=>'origem_clientes'),
			array('*')
		);
		
		if($arr['noDefault']){
		
			if($arr['id_empresa']){
			
				$row->where('oc.id_empresa = ' . $arr['id_empresa'] . ' OR oc.id_empresa is NULL OR oc.id_empresa = 0');
			
			}
		
		}else{
			
			$row->where('oc.id_empresa is NULL');
		
		}
		
		//echo $row->__toString();exit;

		$row->order('oc.descricao');
		
		return $row->query()->fetchAll();
	
	}

}
