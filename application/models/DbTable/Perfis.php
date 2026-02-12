<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Perfis extends Zend_Db_Table_Abstract
{

	protected $_name = 'perfis';
	
	public function add($dados){
		
		try{

			return $this->insert($dados);
		
		} catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	}

	public function edt($id, $dados){
		
		try{

			return $this->update($dados, 'id = ' . $id);

		}catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	} 

	public function del($id){
	
		$dados = $this->getPerfil($id);

		$this->delete('id = ' . $id);

	}
	
	public function getPerfis(){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('p'=>'perfis'),
		array('*')
		);

		return $row->query()->fetchAll();
	
	}
	
	public function getPerfil($id){
		
		return $this->fetchAll("id = $id");
	
	}
	
	public function getLastId(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('p'=>'perfis'),
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

}
