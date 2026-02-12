<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Fornecedores extends Zend_Db_Table_Abstract
{

	protected $_name = 'fornecedores';
	
	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('f'=>'fornecedores'),
			array('*')
		);
		
		$row->joinLeft(
			array('u'=>'usuarios'),
			'u.id = f.id_usuario_alteracao',
			array('nome')
		);
		
		if(isset($arr['id'])){
		
			$row->where('f.id = ' . $arr['id']);
		
		}
		
		if(isset($arr['id_empresa'])){
			
			$row->where('f.id_empresa = ' . $arr['id_empresa']);
			
		}
		
		if(isset($arr['parcial']) && $arr['parcial'] == true){
		
			if(isset($arr['nome'])){
			
				$row->where("f.razao_social LIKE '" . $arr['nome'] . "%'");
			
			}
			
			if(isset($arr['cnpj'])){
			
				$row->where("f.cnpj LIKE '" . $arr['cnpj'] . "%'");
			
			}
			
			if(isset($arr['ramo_atividade'])){
			
				$row->where("f.ramo_atividade LIKE '" . $arr['ramo_atividade'] . "%'");
			
			}

		}else{
			
			if(isset($arr['nome'])){
			
				$row->where("f.razao_social = '" . $arr['nome'] . "'");
			
			}
			
			if(isset($arr['cnpj'])){
			
				$row->where("f.cnpj = '" . $arr['cnpj'] . "'");
			
			}
			
			if(isset($arr['ramo_atividade'])){
			
				$row->where("f.ramo_atividade = '" . $arr['ramo_atividade'] . "'");
			
			}
			
		}
		
		
	
		$row->order('f.razao_social');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function _getDois($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('f'=>'fornecedores'),
			array('*')
		);
		
		$row->joinLeft(
			array('u'=>'usuarios'),
			'u.id = f.id_usuario_alteracao',
			array('nome')
		);
		
		if(isset($arr['id'])){
		
			$row->where('f.id = ' . $arr['id']);
		
		}
		
		if(isset($arr['id_empresa'])) {
			if($arr['id_empresa'] == 3 || $arr['id_empresa'] == 239) {
				
				$row->where("f.id_empresa = 3 or f.id_empresa = 239");
			
			}else{
			
				$row->where("f.id_empresa = ".$arr['id_empresa']);
			
			}
		}
		
		//if($arr['id_empresa']){
			
		//	$row->where('f.id_empresa = ' . $arr['id_empresa']);
			
		//}
		
		if(isset($arr['parcial']) && $arr['parcial'] == true) {
		
			if(isset($arr['nome'])){
			
				$row->where("f.razao_social LIKE '" . $arr['nome'] . "%'");
			
			}
			
			if(isset($arr['cnpj'])){
			
				$row->where("f.cnpj LIKE '" . $arr['cnpj'] . "%'");
			
			}
			
			if(isset($arr['ramo_atividade'])){
			
				$row->where("f.ramo_atividade LIKE '" . $arr['ramo_atividade'] . "%'");
			
			}

		}else{
			
			if(isset($arr['nome'])){
			
				$row->where("f.razao_social = '" . $arr['nome'] . "'");
			
			}
			
			if(isset($arr['cnpj'])){
			
				$row->where("f.cnpj = '" . $arr['cnpj'] . "'");
			
			}
			
			if(isset($arr['ramo_atividade'])){
			
				$row->where("f.ramo_atividade = '" . $arr['ramo_atividade'] . "'");
			
			}
			
		}
		
		$row->order('f.id DESC');
		
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

		$this->delete('id = ' . $id);

	}
	
	public function getPerfis(){
	
		return $this->fetchAll();	
	
	}
	
	public function getFornecedor($id){
		
		return $this->fetchAll("id = $id");
	
	}
	
	public function getFornecedoresPorEmpresa($idEmpresa){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('f'=>'fornecedores'),
		array('*')
		);
		
		if($idEmpresa == 3 || $idEmpresa == 239){
			
			$row->where("f.id_empresa = 3 or f.id_empresa = 239");
		
		}else{
		
			$row->where("f.id_empresa = ".$idEmpresa);
		
		}
		  
		$row->order('id DESC');
		
		return $row->query()->fetchAll();
		
	}
	
	public function getRamoAtividade($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('f'=>'fornecedores'),
		array('distinct(ramo_atividade)')
		);
		
		if($arr['id_empresa']){
			
			$row->where('f.id_empresa = ' . $arr['id_empresa']);
			
		}
		  
		$row->order('ramo_atividade ASC');
		
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
	
	public function atualizaModelos(){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'marca'),
		array('marca'=>'nome')
		);
		
		$row->joinLeft(
			array('mo'=>'modelo'),
			'mo.marca = m.id',
			array('modelo'=>'nome')
		);
		
		$row->joinLeft(
			array('am'=>'ano_modelo'),
			'am.modelo = mo.id',
			array('ano_modelo'=>'nome','codigo'=>'am.id','valor')
		);
		
		$row->order('m.id ASC');
		
		$arrFipe = $row->query()->fetchAll();
		
		$db = $this->getConnDb();
		
		foreach($arrFipe as $fipe){
		
			$x++;
		
			$sql .= "REPLACE INTO 'modelos_11' (codigo, modelo, marca, ano_modelo, preco) VALUES ('".$fipe['codigo']."','".$fipe['modelo']. " " . substr($fipe['ano_modelo'],4) . "','".$fipe['marca']."','".substr($fipe['ano_modelo'],0,4)."','".$fipe['valor']."');";
			
			if($x%50 == 0){
			
				$db->query($sql);
				$sql = "";
				
			}
		
		}
		
		$db->query($sql);	
	
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
