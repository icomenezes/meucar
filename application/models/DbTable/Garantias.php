<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Garantias extends Zend_Db_Table_Abstract
{

	protected $_name = 'garantias';
	
	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('g'=>'garantias'),
			array('*')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'g.id_veiculo = v.id',
			array('id_modelo','placa','descricao_site')
		);
		
		$row->joinInner(
			array('u'=>'usuarios'),
			'g.id_usuario_alteracao = u.id',
			array('ultimoUsuario'=>'nome')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'v.id_modelo = m.id',
			array('modelo')
		);
		
		$row->joinLeft(
			array('n'=>'negociacoes'),
			'n.id_veiculo = v.id',
			array('idNegociacao'=>'id','data_concretizacao')
		);
		
		$row->joinLeft(
			array('f'=>'fornecedores'),
			'g.id_fornecedor = f.id',
			array('razao_social')
		);
		
		if(isset($arr['id_empresa'])){
		
			$row->where('v.id_empresa = ' . $arr['id_empresa']);
		
		}
		
		if(isset($arr['id_fornecedor'])){
		
			$row->where('g.id_fornecedor = ' . $arr['id_fornecedor']);
		
		}
		
		if(isset($arr['data_inicial'])){
		
			$row->where("g.data_saida >= '" .$arr['data_inicial']."'");
		
		}
		
		if(isset($arr['data_final'])){
		
			$row->where("g.data_saida <= '". $arr['data_final']."'");
		
		}
		
		if(isset($arr['relatorio_garantias'])){
		
			$row->order('g.id_fornecedor');
		
		}else{
	
			$row->order('g.data_entrada DESC');
			
		}
		
		if(isset($arr['id_veiculo'])){
		
			$row->where('g.id_veiculo = '.$arr['id_veiculo']);
		
		}

		$row->where('n.compra = 0');
		
		//Comentado em 23/09/2022, pois na listagem de garantias não estava aparecendo as garantias se a negociação não fosse aprovada.
		//$row->where('n.aprovada = 1');
		
		//echo $row->__toString();exit;
		
		return $row->query()->fetchAll();
	
	}
	
	public function getGarantias($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('g'=>'garantias'),
			array('valor'=>'SUM(custo)')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'g.id_veiculo = v.id',
			array()
		);
		
		if(isset($arr['id_empresa'])){
		
			$row->where('v.id_empresa = ' . $arr['id_empresa']);
		
		}
		
		if(isset($arr['data_inicial']) && $arr['data_inicial'] != ""){
		
			$row->where("g.data_saida >= '" .$arr['data_inicial']."'");
		
		}
		
		if(isset($arr['data_final']) && $arr['data_final'] != ""){
		
			$row->where("g.data_saida <= '". $arr['data_final']."'");
		
		}
	
		$row->order('g.data_entrada');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function getGarantiasIndividual($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('g'=>'garantias'),
			array('*')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'g.id_veiculo = v.id',
			array()
		);
		
		if(isset($arr['id_empresa'])){
		
			$row->where('v.id_empresa = ' . $arr['id_empresa']);
		
		}
		
		if(isset($arr['data_inicial']) && $arr['data_inicial'] != ""){
		
			$row->where("g.data_saida >= '" .$arr['data_inicial']."'");
		
		}
		
		if(isset($arr['data_final']) && $arr['data_final'] != ""){
		
			$row->where("g.data_saida <= '". $arr['data_final']."'");
		
		}
	
		$row->order('g.data_entrada');
		
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
	
	public function getLastId(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('g'=>'garantias'),
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
