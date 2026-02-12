<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_PesquisaSatisfacao extends Zend_Db_Table_Abstract
{

	protected $_name = 'pesquisa_satisfacao';
	
	public function add($dados){
		
		try{
		
			return $this->insert($dados);
		
		} catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	}
	
	public function getPesquisas($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('ps'=>'pesquisa_satisfacao'),
		array('*')
		);
	
		$row-> joinInner(
			array('u'=>'usuarios'),
			'u.id = ps.id_vendedor',
			array('nome_vendedor'=>'u.nome')
		);

		if(isset($arr['id_vendedor']) && $arr['id_vendedor'] != 0){
			
			$row->where('ps.id_vendedor = '.$arr['id_vendedor']);
			
		}
		
		if(isset($arr['loja']) && $arr['loja'] != ""){
			if($arr['loja'] != "nao"){
				$row->where("ps.loja LIKE '%".$arr['loja']."'");
			}else{
				$row->where("ps.loja LIKE 'N%'");
			}
			
		}
		
		if(isset($arr['dispositivo'])){
			
			$row->where("ps.dispositivo LIKE '%".$arr['dispositivo']."'");
			
		}
		
		if(isset($arr['data_inicial']) && $arr['data_inicial'] != ""){
			
			$row->where('ps.data_hora >= "'.$arr['data_inicial'].' 00:00:00"');
			
		}
		
		if(isset($arr['data_final']) && $arr['data_final'] != ""){
			
			$row->where('ps.data_hora <= "'.$arr['data_final'].' 23:59:59"');
			
		}
		
	
		$row->order('id DESC');

		//echo $row->__toString();

		return $row->query()->fetchAll();
		
		
	}
	
	public function getLastId(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('ps'=>'pesquisa_satisfacao'),
		array('id')
		);
		  
		$row->order('id DESC');
		$row->limit(1);
		return $row->query()->fetchAll();
		
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
