<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_PeriodoItensPreparacao extends Zend_Db_Table_Abstract
{

	protected $_name = 'periodo_itens_preparacao';
	
	public function get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('pip'=>'periodo_itens_preparacao'),
			array('*')
		);
		
		if($arr['id_empresa']){
			
			$row->where('pip.id_empresa = ' . $arr['id_empresa']);
			
		}

		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function add($dados){
		
		try{

			return $this->insert($dados);
		
		} catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	}

	public function edt($id, $dados){
		
		try{

			return $this->update($dados, 'id_empresa = ' . $id);

		}catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	} 

	public function del($id){

		$this->delete('id = ' . $id);

	}
	
	public function getPerfis(){
	
		return $this->fetchAll();	
	
	}
	
	public function getFornecedor($id){
		
		return $this->fetchAll("id = $id");
	
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
