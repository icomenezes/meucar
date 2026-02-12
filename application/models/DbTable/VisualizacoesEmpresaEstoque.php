<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_VisualizacoesEmpresaEstoque extends Zend_Db_Table_Abstract
{

	protected $_name = 'visualizacoes_empresa_estoque';
	
	
	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('vee'=>'visualizacoes_empresa_estoque'),
			array('*')
		);
		
		
		if($arr['id_empresa']){
			
			$row->where("vee.id_empresa = ". $arr['id_empresa']);
			
		}
		
		if($arr['id_veiculo']){
			
			$row->where("vee.id_veiculo = ". $arr['id_veiculo']);
			
		}
		
		if($arr['data']){
			
			$row->where("vee.data = '".$arr['data']."'");
			
		}
		
		if($arr['data_final']){
			
			$row->where("vee.data <= '".$arr['data_final']."'");
			
		}
		
		if($arr['data_inicial']){
			
			$row->where("vee.data >= '".$arr['data_inicial']."'");
			
		}
	
		$row->order('vee.data');
		
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
	
	public function getModelosPorEmpresa($idEmpresa){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelos_11'),
		array('*')
		);
		
		$row->where("m.id_empresa = ".$idEmpresa);
		  
		$row->order('id DESC');
		
		return $row->query()->fetchAll();
		
	}
	
	public function getModelosPorMarca($marca){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelos_11'),
		array('*')
		);
		
		$row->where("m.marca = '".$marca."'");
		  
		$row->order('modelo ASC');
		
		return $row->query()->fetchAll();
		
	}
	
	public function getModelosPorMarcaVeiculos($marca){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelos_11'),
		array('distinct(m.modelo)','id')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'v.id_modelo = m.id',
			array()
		);
		  
		
		$row->where("m.marca = '".$marca."'");
		  
		$row->order('modelo ASC');
		
		//echo $row->__toString();
		
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
