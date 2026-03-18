<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_ContratosRecibos extends Zend_Db_Table_Abstract
{

	protected $_name = 'contratos_recibos';
	
	
	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('cr'=>'contratos_recibos'),
			array('*')
		);
		
		$row->joinLeft(
			array('u'=>'usuarios'),
			'u.id = cr.id_usuario_alteracao',
			array('nome_alteracao'=>'nome')
		);
		
		if(isset($arr['id']) && $arr['id'] != ""){
			$row->where('cr.id = ' . $arr['id']);
		}

		if(isset($arr['id_root']) && $arr['id_root'] != "" && isset($arr['id_empresa']) && $arr['id_empresa'] != ""){

			$row->where("cr.id_empresa = ".$arr['id_empresa']." OR cr.id_empresa = ".$arr['id_root']);

		}elseif(isset($arr['id_empresa']) && $arr['id_empresa'] != ""){
			
			$row->where('cr.id_empresa = ' . $arr['id_empresa']);
			
		}
		
		
		
		if(isset($arr['parcial']) && $arr['parcial'] == true){
		
			if(isset($arr['marca']) && $arr['marca'] != ""){
			
				$row->where("cr.marca LIKE '" . $arr['marca'] . "%'");
			
			}
			
			if(isset($arr['modelo']) && $arr['modelo'] != ""){
			
				$row->where("cr.modelo LIKE '" . $arr['modelo'] . "%'");
			
			}
			
			if($arr['ano_modelo']){
			
				$row->where("cr.ano_modelo LIKE '" . $arr['ano_modelo'] . "%'");
			
			}

		}else{
			
			if(isset($arr['marca']) && $arr['marca'] != ""){
			
				$row->where("cr.marca = '" . $arr['marca'] . "'");
			
			}
			
			if(isset($arr['modelo']) && $arr['modelo'] != ""){
			
				$row->where("cr.modelo = '" . $arr['modelo'] . "'");
			
			}
			
			if(isset($arr['ano_modelo']) && $arr['ano_modelo'] != ""){
			
				$row->where("cr.ano_modelo = '" . $arr['ano_modelo'] . "'");
			
			}
			
		}
	
		$row->order('cr.id DESC');
		
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
	
		return $this->fetchAll();	
	
	}
	
	public function getPerfil($id){
		
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
