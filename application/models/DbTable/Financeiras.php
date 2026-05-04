<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Financeiras extends Zend_Db_Table_Abstract
{

	protected $_name = 'financeiras_despachantes';
	
	
	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('fd'=>'financeiras_despachantes'),
			array('*')
		);
		
		$row->joinLeft(
			array('u'=>'usuarios'),
			'u.id = fd.id_usuario_alteracao',
			array('nome_alterou'=>'u.nome')
		);
		
		if(!empty($arr['id'])){

			$row->where('fd.id = ' . (int)$arr['id']);

		}
		
		if(isset($arr['tipo'])){
		
			$row->where('fd.tipo = ' . $arr['tipo']);
		
		}else{
		
			$row->where('fd.tipo = 0');
		
		}
		
		if(isset($arr['id_empresa'])){
			
			$row->where('fd.id_empresa = ' . $arr['id_empresa']);
			
		}
		
		if(isset($arr['parcial']) && $arr['parcial'] == true){
		
			if(isset($arr['nome'])){
			
				$row->where("fd.nome LIKE '" . $arr['nome'] . "%'");
			
			}
			
			if(isset($arr['cnpj'])){
			
				$row->where("fd.cnpj LIKE '" . $arr['cnpj'] . "%'");
			
			}

		}else{
			
			if(isset($arr['nome'])){
			
				$row->where("fd.nome = '" . $arr['nome'] . "'");
			
			}
			
			if(isset($arr['cnpj'])){
			
				$row->where("fd.cnpj = '" . $arr['cnpj'] . "'");
			
			}
			
		}
	
		$row->order('fd.nome');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function _getFD($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('fd'=>'financeiras_despachantes'),
			array('*')
		);
		
		$row->joinLeft(
			array('u'=>'usuarios'),
			'u.id = fd.id_usuario_alteracao',
			array('nome_alterou'=>'u.nome')
		);
		
		if(isset($arr['id'])) {
		
			$row->where('fd.id = ' . $arr['id']);
		
		}
		
		if(isset($arr['id_empresa'])){
			
			$row->where('fd.id_empresa = ' . $arr['id_empresa']);
			
		}
		
		if(isset($arr['parcial']) && $arr['parcial'] == true){
			
			if(isset($arr['nome'])){
			
				$row->where("fd.nome LIKE '" . $arr['nome'] . "%'");
			
			}
			
			if(isset($arr['cnpj'])){
			
				$row->where("fd.cnpj LIKE '" . $arr['cnpj'] . "%'");
			
			}

		}else{
		
			if(isset($arr['nome'])){
			
				$row->where("fd.nome = '" . $arr['nome'] . "'");
			
			}
			
			if(isset($arr['cnpj'])){
			
				$row->where("fd.cnpj = '" . $arr['cnpj'] . "'");
			
			}
		}
	
		$row->order('fd.nome');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function getFinanceirasDespachantes($arr = array()){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fd'=>'financeiras_despachantes'),
		array('*')
		);
		
		$row->joinLeft(
			array('n'=>'negociacoes'),
			'fd.id = n.id_despachante',
			array('valor_despachante', 'data'=>'data_concretizacao','forma_pagamento_despachante')
		);
		
		$row->joinLeft(
			array('v'=>'veiculos'),
			'v.id = n.id_veiculo',
			array('placa')
		);
		
		$row->joinLeft(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo')
		);
		
		$row->joinLeft(
			array('c'=>'clientes'),
			'c.id = n.id_cliente',
			array('nome_cliente'=>'c.nome')
		);
		
		if(isset($arr['id_empresa'])){
			
			$row->where('fd.id_empresa = ' . $arr['id_empresa']);
			
		}
		
		if(isset($arr['id'])){
			
			$row->where('fd.id = ' . $arr['id']);
			
		}
		
		if(isset($arr['tipo'])){
			
			$row->where('fd.tipo = ' . $arr['tipo']);
			
		}
		
		if(isset($arr['data_inicial'])){
		
			$row->where("n.data_concretizacao >= '".$arr['data_inicial']." 00:00:00'");
		
		}
		
		if(isset($arr['data_final'])){
		
			$row->where("n.data_concretizacao <= '". $arr['data_final']." 23:59:59'");
		
		}
		
		$row->where("n.aprovada = 1");
	
		$row->order('fd.nome ASC');

		//echo $row->__toString();

		return $row->query()->fetchAll();
		
	}
	
	public function getLastId(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('f'=>'financeiras'),
		array('id')
		);
		  
		$row->order('id DESC');
		$row->limit(1);
		return $row->query()->fetchAll();
		
	}

	public function add($dados){
		
		try{
			return $this->insert($dados);
		} catch (Exception $e){
			echo $e->getMessage();
		}
		
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
