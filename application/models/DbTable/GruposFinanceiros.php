<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_GruposFinanceiros extends Zend_Db_Table_Abstract
{

	protected $_name = 'financeiro_grupos';
	
	
	public function getLancamentosReceber($idEmpresa){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('fg'=>'financeiro_grupos'),
			array('id')
		);
		
		$row->joinLeft(
			array('fi'=>'financeiro_itens'),
			'fg.id = fi.id_financeiro_grupo',
			array()
		);
		
		$row->joinLeft(
			array('fl'=>'financeiro_lancamentos'),
			'fi.id = fl.id_financeiro_item',
			array('valor_lancamento'=>'SUM(valor)')
		);
		
		$row->where('fl.baixado = 0');
		
		$row->where('fg.credito_debito = 0');
		
		$row->where('fi.id_empresa = ' . $idEmpresa);
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
		
	}
	
	public function getLastId(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('e'=>'GruposFinanceiros'),
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
	
	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('gf'=>'financeiro_grupos'),
			array('*')
		);
		
		$row->joinLeft(
			array('u2'=>'usuarios'),
			'u2.id = gf.id_usuario_alteracao',
			array('ultimoUsuario'=>'nome')
		);
		
		if(isset($arr['noDefault'])) {
		
			if(isset($arr['id_empresa'])) {
			
				$row->where('gf.id_empresa = ' . $arr['id_empresa']);
			
			}
		
		}else{
		
			if(isset($arr['id_empresa'])){
			
				$row->where('gf.id_empresa = ' . $arr['id_empresa'] . ' OR gf.id_empresa is NULL');
			
			}
		
		}
		
		if(isset($arr['tipo_grupo']) && $arr['tipo_grupo'] == true){
		
			if(isset($arr['credito_debito']) && $arr['credito_debito'] == 1){
				
				$row->where('gf.credito_debito = 1');
				
			}else{
			
				$row->where('gf.credito_debito = 0');
			
			}
		
		}
	
		$row->order('gf.descricao');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function getLancamentosPorGrupo($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('fg'=>'financeiro_grupos'),
			array('*')
		);
		
		$row->joinInner(
			array('fi'=>'financeiro_itens'),
			'fg.id = fi.id_financeiro_grupo',
			array()
		);
		
		$row->joinInner(
			array('fl'=>'financeiro_lancamentos'),
			'fi.id = fl.id_financeiro_item',
			array('valor_lancamento'=>'SUM(valor)')
		);
		
		if(isset($arr['id_empresa'])){
			
			$row->where('fi.id_empresa = ' . $arr['id_empresa']);
			
		}
		
		if(isset($arr['data_inicial_concretizacao']) && $arr['data_inicial_concretizacao'] != ""){
			
			$row->where("fl.data_lancamento >= '".$arr['data_inicial_concretizacao']."'");
			
		}
		
		if(isset($arr['data_final_concretizacao']) && $arr['data_final_concretizacao'] != ""){
			
			$row->where("fl.data_lancamento <='".$arr['data_final_concretizacao']."'");
			
		}

		$row->group('fg.descricao');
		
		$row->order('fg.credito_debito');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function getLancamentosPorGrupos($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('fl'=>'financeiro_lancamentos'),
			array('valor','data_lancamento','baixado')
		);
		
		$row->joinInner(
			array('fi'=>'financeiro_itens'),
			'fi.id = fl.id_financeiro_item',
			array('item')
		);
		
		$row->joinInner(
			array('fg'=>'financeiro_grupos'),
			'fg.id = fi.id_financeiro_grupo',
			array('id_grupo'=>'id','descricao','credito_debito')
		);
		
		if(isset($arr['id_empresa'])){
			
			$row->where('fi.id_empresa = ' . $arr['id_empresa']);
			
		}
		
		if(isset($arr['id_grupo'])){
			
			$row->where('fg.id = '.$arr['id_grupo']);
			
		}
		
		if(isset($arr['item'])){
			
			$row->where('fi.item = "'.$arr['item'].'"');
			
		}
		
		if(isset($arr['credito'])){
			if($arr['credito'] == true){
				
				$row->where('fg.credito_debito = 0');
				
			}else{
			
				$row->where('fg.credito_debito = 1');
			
			}
		}
		
		if(isset($arr['baixado'])){
			if($arr['baixado'] == 1){
				
				$row->where('fl.baixado = 1');
				
			}elseif($arr['baixado'] == 2){
			
				$row->where('fl.baixado = 0');
			
			}
		}
		
		if(isset($arr['data_inicial'])){
			
			$row->where("fl.data_lancamento >= '".$arr['data_inicial']."'");
			
		}
		
		if(isset($arr['data_final'])){
			
			$row->where("fl.data_lancamento <='".$arr['data_final']."'");
			
		}
		
		$row->order('fg.id');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}

}
