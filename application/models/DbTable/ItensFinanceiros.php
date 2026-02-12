<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_ItensFinanceiros extends Zend_Db_Table_Abstract
{

	protected $_name = 'financeiro_itens';


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
		array('e'=>'financeiro_itens'),
		array('id')
		);
	
	
		$row->order('id DESC');
		$row->limit(1);
		return $row->query()->fetchAll();
		
	}
	
	public function getItensDistintos($idEmpresa){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('i'=>'financeiro_itens'),
		array('*')
		);
		
		$row->where("i.id_empresa = ".$idEmpresa);
	
		$row->order('item ASC');

		return $row->query()->fetchAll();
		
	}
	
	
	public function getItem($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('if'=>'financeiro_itens'),
		array('*')
		);
		
		if($arr['id_empresa']){
		
			$row->where("if.id_empresa = " . $arr['id_empresa']);
		
		}
		
		if($arr['id']){
		
			$row->where("if.id = ". $arr['id']);
		
		}
		
		$row->where("if.recorrente = 1");
	
		$row->order('item ASC');

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
			array('if'=>'financeiro_itens'),
			array('*')
		);
		
		$row->joinInner(
				array('gf'=>'financeiro_grupos'),
				'if.id_financeiro_grupo = gf.id',
				array('descricao')
		);
		
		$row->joinLeft(
			array('u2'=>'usuarios'),
			'u2.id = gf.id_usuario_alteracao',
			array('ultimoUsuario'=>'nome')
		);
		
		if(isset($arr['id_empresa'])){
		
			$row->where("if.id_empresa = " . $arr['id_empresa']);
		
		}
		
		if(isset($arr['id_financeiro_grupo'])){
		
			$row->where("if.id_financeiro_grupo = " . $arr['id_financeiro_grupo']);
		
		}
	
		$row->order('if.item');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function _getItensRecorentes($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('if'=>'financeiro_itens'),
			array('*')
		);
		
		$row->joinInner(
			array('gf'=>'financeiro_grupos'),
			'if.id_financeiro_grupo = gf.id',
			array('descricao')
		);
		
		if(isset($arr['id_empresa'])) {
		
			$row->where("if.id_empresa = " . $arr['id_empresa']);
		
		}
		
		if(isset($arr['id_financeiro_grupo'])) {
		
			$row->where("if.id_financeiro_grupo = " . $arr['id_financeiro_grupo']);
		
		}
		
		$row->where("if.recorrente = 1");

		if(isset($arr['limit'])){
			$row->limit($arr['limit']);
		}
	
		$row->order('substr(if.data_inicio,-2)');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}

}
