<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_DespesasVeiculos extends Zend_Db_Table_Abstract
{

	protected $_name = 'despesas_veiculos';
	
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

		return $this->delete('id = ' . $id);

	}
	
	public function delPorVeiculo($id){

		return $this->delete('id_veiculo = ' . $id);

	}
	
	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('dv'=>'despesas_veiculos'),
			array('*')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'v.id = dv.id_veiculo',
			array('placa')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'v.id_modelo = m.id',
			array('modelo')
		);
		
		$row->joinInner(
			array('f'=>'fornecedores'),
			'f.id = dv.id_fornecedor',
			array('razao_social_fornecedor' => 'razao_social')
		);
		
		if(isset($arr['id'])){
		
			$row->where('dv.id = ' . $arr['id']);
		
		}
		
		if(isset($arr['id_veiculo'])){
		
			$row->where('dv.id_veiculo = ' . $arr['id_veiculo']);
		
		}
		
		if(isset($arr['id_fornecedor']) && $arr['id_fornecedor'] != '0'){
		
			$row->where('dv.id_fornecedor = ' . $arr['id_fornecedor']);
		
		}
		
		if(isset($arr['id_empresa'])){
		
			$row->where('v.id_empresa = ' . $arr['id_empresa']);
		
		}
		
		if(isset($arr['ramo_atividade']) && $arr['ramo_atividade'] != '0'){

			$row->where("f.ramo_atividade = '".$arr['ramo_atividade']."'");
		
		}
		
		if(isset($arr['data_inicial']) && $arr['data_inicial'] != ""){
		
			$row->where("dv.data >= '".$arr['data_inicial']."'");
		
		}
		
		if(isset($arr['data_final']) && $arr['data_final'] != ""){
		
			$row->where("dv.data <= '". $arr['data_final']."'");
		
		}
	
		$row->order('f.razao_social ASC');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function getPerfis(){
	
		return $this->fetchAll();	
	
	}
	
	public function getDespesa($id){
		
		return $this->fetchAll("id_veiculo = $id");
	
	}
	
	public function getOpcionais(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('o'=>'opcionais'),
		array('*')
		);

		$row->order('id ASC');
		
		return $row->query()->fetchAll();
		
	}
	
	public function getDespesas($id_veiculo){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('dv'=>'despesas_veiculos'),
		array('*')
		);
		
		$row->joinLeft(
			array('f'=>'fornecedores'),
			'dv.id_fornecedor = f.id',
			array('razao_social')
		);
		
		$row->where('dv.id_veiculo = '.$id_veiculo);

		$row->order('id ASC');
		
		return $row->query()->fetchAll();
		
	}
	
	public function getSomaDespesas($id_veiculo){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('dv'=>'despesas_veiculos'),
		array("valor_despesas"=>"SUM(valor)")
		);
		
		$row->where('dv.id_veiculo = '.$id_veiculo);
		
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
