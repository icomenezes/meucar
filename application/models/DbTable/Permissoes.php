<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Permissoes extends Zend_Db_Table_Abstract
{

	protected $_name = 'permissoes';
	
	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('p'=>'permissoes'),
			array('*')
		);
		
		$row->joinLeft(
			array('per'=>'perfis'),
			'p.id_perfil = per.id',
			array('perfil')
		);
	
		$row->order('per.perfil');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function _getUsuario($arr = array()){
		
		//var_export($arr);exit;
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('p'=>'permissoes'),
			array('*')
		);
		
		$row->joinInner(
			array('per'=>'perfis'),
			'p.id_perfil = per.id',
			array('perfil')
		);
		
		$row->joinInner(
			array('u'=>'usuarios'),
			'per.id = u.id_perfil',
			array('idUsuario'=>'id','u_perfil'=>'id_perfil','id_empresa','nome','cargo')
		);
		
		 if($arr['id_perfil']){
			
			if($arr['id_perfil']!= ADMINISTRADOR && $arr['id_perfil'] != CONCESSIONARIO && $arr['id_perfil'] != ADMINISTRATIVO){
			
				$row->where('per.id = ' . $arr['id_perfil']);
				
			} 
		}
		
		if($arr['id_empresa']){
		
			if($arr['id_perfil']!= ADMINISTRADOR){
		
				$row->where('u.id_empresa = ' . $arr['id_empresa']);
			}
		}
		
		if($arr['id_usuario']){
			
			if($arr['id_perfil']!= ADMINISTRADOR && $arr['id_perfil'] != CONCESSIONARIO && $arr['id_perfil'] != ADMINISTRATIVO){
			
				$row->where('u.id= ' . $arr['id_usuario']);
			
			} 
		
		} 
	
		$row->order('u.nome');
				
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function getLastId(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('e'=>'permissoes'),
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
