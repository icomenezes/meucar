<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_BuscaVeiculos extends Zend_Db_Table_Abstract
{

	protected $_name = 'busca_veiculos';
	
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

		$this->delete('id = ' . $id);

	}
	
	public function getPerfis(){
	
		return $this->fetchAll();	
	
	}
	
	public function getOpcional($id){
		
		return $this->fetchAll("id = $id");
	
	}
	
	public function getBuscaVeiculos($idEmpresa, $dataInicial, $dataFinal){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('bv'=>'busca_veiculos'),
		array('*')
		);

		$row->where('id_empresa = '.$idEmpresa);
		
		if($dataInicial){
		
			$row->where('data_hora >= "'.$dataInicial.' 00:00:00"');
		
		}
		
		if($dataFinal){
		
			$row->where('data_hora <= "'.$dataFinal.' 23:59:00"');
		
		}
		
		$row->limit(1000);

		$row->order('id DESC');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
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
