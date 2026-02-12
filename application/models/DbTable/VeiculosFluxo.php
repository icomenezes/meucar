<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_VeiculosFluxo extends Zend_Db_Table_Abstract
{

	protected $_name = 'veiculos_fluxo';
	
	public function add($dados){
		
		try{

			return $this->insert($dados);
		
		} catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	}

	public function edt($id, $dados){
		
		try{

			return $this->update($dados, 'id_fluxo_cliente = ' . $id);

		}catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	} 

	public function del($id){
	
		$dados = $this->getPerfil($id);

		$this->delete('id = ' . $id);

	}
	
	
	public function delIdClienteFluxo($id){

		$this->delete('id_fluxo_cliente = ' . $id);

	}
	
	
	public function getPerfis(){
	
		return $this->fetchAll();	
	
	}
	
	public function getPerfil($id){
		
		return $this->fetchAll("id = $id");
	
	}
	
	public function getVeiculoFluxo($id){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('vf'=>'veiculos_fluxo'),
		array('*')
		);

		$row->where('vf.id_fluxo_cliente = '.$id);
		
		$row->order('id ASC');

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
