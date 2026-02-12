<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_IndicacoesCorretoras extends Zend_Db_Table_Abstract
{

	protected $_name = 'indicacoes_corretoras';
	
	public function add($dados){
		
		try{

			return $this->insert($dados);
		
		} catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	}

	public function edt($id, $dados){
		
		try{

			return $this->update($dados, 'id = '.$id);

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
	
	public function getIndicacaoPorVeiculoCliente($idVeiculo, $idCliente){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('ic'=>'indicacoes_corretoras'),
		array('*')
		);

		
		
		$row->where('id_veiculo = '.$idVeiculo);
		
		$row->where('id_cliente = '.$idCliente);

		$row->limit(1);
		
		return $row->query()->fetchAll();
		
	}
	
	public function getIndicacaoPorCorretora($idEmpresa){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('ic'=>'indicacoes_corretoras'),
		array('*')
		);
		
		$row->joinInner(
			array('c'=>'clientes'),
			'c.id = ic.id_cliente',
			array('nome')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'v.id = ic.id_veiculo',
			array('')
		);

		$row->joinInner(
			array('m'=>'modelos_11'),
			'v.id_modelo = m.id',
			array('modelo')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'ic.id_empresa = e.id',
			array('nome_fantasia')
		);
		
		$row->where('id_corretora = '.$idEmpresa);

		return $row->query()->fetchAll();
		
	}
	
	public function getIndicacaoPorCorretoraData($idEmpresa, $dataInicial, $dataFinal){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('ic'=>'indicacoes_corretoras'),
		array('*')
		);
		
		$row->joinInner(
			array('c'=>'clientes'),
			'c.id = ic.id_cliente',
			array('nome')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'v.id = ic.id_veiculo',
			array('')
		);

		$row->joinInner(
			array('m'=>'modelos_11'),
			'v.id_modelo = m.id',
			array('modelo')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'ic.id_empresa = e.id',
			array('nome_fantasia')
		);
		
		$row->where('id_corretora = '.$idEmpresa);
		
		if($dataInicial){
		
			$row->where("data_venda >= '".$dataInicial."'");
			
		}
		
		if($dataFinal){
		
			$row->where("data_venda <= '".$dataFinal."'");
		
		}

		return $row->query()->fetchAll();
		
	}
	
	
	public function getIndicacaoPorCorretoraDataLoja($idEmpresa, $dataInicial, $dataFinal){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('ic'=>'indicacoes_corretoras'),
		array('*')
		);
		
		$row->joinInner(
			array('c'=>'clientes'),
			'c.id = ic.id_cliente',
			array('nome')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'v.id = ic.id_veiculo',
			array('')
		);

		$row->joinInner(
			array('m'=>'modelos_11'),
			'v.id_modelo = m.id',
			array('modelo')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'ic.id_empresa = e.id',
			array('nome_fantasia')
		);
		
		if($idEmpresa != 0){
		
			$row->where('ic.id_empresa = '.$idEmpresa);
			
		}
		
		if($dataInicial){
		
			$row->where("data_venda >= '".$dataInicial."'");
			
		}
		
		if($dataFinal){
		
			$row->where("data_venda <= '".$dataFinal."'");
		
		}

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
