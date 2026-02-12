<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Vendedores extends Zend_Db_Table_Abstract
{

	protected $_name = 'vendedores';
	
	public function getLastId(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'vendedores'),
		array('id')
		);
		  
		$row->order('id DESC');
		$row->limit(1);
		return $row->query()->fetchAll();
		
	}
	
	public function getComissoes($id){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'vendedores'),
		array('*')
		);

		$row->where('v.id_usuario = '.$id);
		
		$row->limit(1);
		return $row->query()->fetchAll();
		
	}
	
	/*public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'vendedores'),
			array('*')
		);

		if($arr['id_perfil']){
		
			$row->joinInner(
				array('u'=>'usuarios'),
				'u.id = v.id_usuario',
				array('nome','login', 'id_empresa')
			);
		
		}
	
		if($arr['id']){
		
			$row->where('v.id_usuario = ' . $arr['id']);
			
			$row->joinInner(
				array('u'=>'usuarios'),
				'u.id = v.id_usuario',
				array('nome')
			);
		
		}
		
		if($arr['id_empresa']){
		
			$row->where('u.id_empresa = ' . $arr['id_empresa']);
		
		}
		
		if($arr['id_perfil']){
		
			$row->where('u.id_perfil = ' .$arr['id_perfil']);
		
		}
		
		return $row->query()->fetchAll();
	
	}*/
	
	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('*')
		);
		
		$row->joinLeft(
			array('v'=>'vendedores'),
			'u.id = v.id_usuario',
			array('valor_fixo', 'percentual_venda', 'percentual_retorno_financeiro', 'manual')
		);

		
		if(isset($arr['id_empresa'])) {
		
			$row->where('u.id_empresa = ' . $arr['id_empresa']);
		
		}
		
		if(isset($arr['id_perfil'])){
		
			$row->where('u.id_perfil = ' .$arr['id_perfil']);
		
		}
		
		if(isset($arr['id'])) {
		
			$row->where('u.id = ' . $arr['id']);
			
		}
		
		$row->where('u.ativo = 1');
		
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
