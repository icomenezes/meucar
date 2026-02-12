<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_RecebimentosNegociacoes extends Zend_Db_Table_Abstract
{

	protected $_name = 'recebimentos_negociacoes';
	
	
	public function _getAlarme($arr){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('rn'=>'recebimentos_negociacoes'),
			array('*')
		);
		
		$row->joinInner(
			array('n'=>'negociacoes'),
			'n.id = rn.id_negociacao',
			array('')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'v.id = n.id_veiculo',
			array('placa','ano_fabricacao','descricao_site')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo')
		);
		
		if($arr['id_empresa']){
			
			$row->where("n.id_empresa = " . $arr['id_empresa']);
			
		}
		
		if($arr['nao_baixado']){
			
			$row->where("rn.baixado = 0");
			
		}
		
		$row->where("rn.data <= '".@date('Y-m-d')."'");

		$row->where('n.compra = 0');
		
		$row->order('rn.data ASC');
		
		//echo $row->__toString();

		return $row->query()->fetchAll();
		
	}
	
	
	public function getRecebimentos($id){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('rn'=>'recebimentos_negociacoes'),
			array('*')
		);
		
		$row->where('rn.id_negociacao = ' .$id);
		
		//echo $row->__toString();
		$row->order('data');

		return $row->query()->fetchAll();
		
	}
	
	public function _get($arr){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('rn'=>'recebimentos_negociacoes'),
			array('*')
		);
		
		$row->joinInner(
			array('n'=>'negociacoes'),
			'n.id = rn.id_negociacao',
			array('')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'v.id = n.id_veiculo',
			array('placa','ano_fabricacao','descricao_site')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo')
		);
		
		if(isset($arr['id_empresa'])) {
			
			$row->where("n.id_empresa = " . $arr['id_empresa']);
			
		}
		
		if(isset($arr['nao_baixado'])){
			$row->where("rn.baixado = 0");
		}
		
		if(isset($arr['baixado'])) {
			
			$row->where("rn.baixado = " . $arr['baixado']);
			
		}
		
		//$row->where('v.id_negociacao_compra is NULL');
		$row->where('n.compra = 0');

		if(isset($arr['limit'])){
			$row->limit($arr['limit']);
		}
		
		$row->order('rn.data ASC');
		
		//echo $row->__toString();

		return $row->query()->fetchAll();
		
	}
	
	public function del($id){

		return $this->delete('id = ' . $id);

	}
	
	public function getLastId(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('r'=>'recebimentos_negociacoes'),
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
