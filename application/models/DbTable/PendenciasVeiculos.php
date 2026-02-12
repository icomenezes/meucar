<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_PendenciasVeiculos extends Zend_Db_Table_Abstract
{

	protected $_name = 'pendencias_veiculos';
	
	public function _get($arr = array()){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('pv'=>'pendencias_veiculos'),
			array('*')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'pv.id_veiculo = v.id',
			array('id_empresa', 'placa', 'id_modelo','descricao_site')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'v.id_modelo = m.id',
			array('modelo', 'ano_modelo')
		);
		
		if(isset($arr['empresa'])) {
			
			$row->where('v.id_empresa = ' . $arr['empresa']);
		
		}
		
		if(isset($arr['data_inicial']) || isset($arr['data_final'])) {
			
			$row->where("pv.data BETWEEN '".$arr['data_inicial']."' AND '".$arr['data_final']."'");
			
		}
		
		if(isset($arr['data_apartir'])) {
			
			$row->where("pv.data <= '".$arr['data_apartir']."'");
		
		}
		
		if(isset($arr['baixado']) && $arr['baixado']==1){
			
			$row->where('pv.baixada = ' . $arr['baixado']);
		
		}else{
		
			$row->where('pv.baixada = 0');
		}

		if(isset($arr['limit'])){
			$row->Limit($arr['limit']);
		}
		
		$row->order('pv.data DESC');
		
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

		return $this->delete('id = ' . $id);

	}
	
	public function getPerfis(){
	
		return $this->fetchAll();	
	
	}
	
	public function getFornecedor($id){
		
		return $this->fetchAll("id = $id");
	
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
	
	public function getPendencias($id){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('pv'=>'pendencias_veiculos'),
		array('*')
		);
		
		$row->where('id_veiculo = '.$id);

		$row->order('id ASC');
		
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
