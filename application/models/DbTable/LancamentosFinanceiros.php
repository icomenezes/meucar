<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_LancamentosFinanceiros extends Zend_Db_Table_Abstract
{

	protected $_name = 'financeiro_lancamentos';
	
	public function getLastId(){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('e'=>'LancamentosFinanceiros'),
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
			array('lf'=>'financeiro_lancamentos'),
			array('*')
		);
		
		$row->joinInner(
			array('if'=>'financeiro_itens'),
			'lf.id_financeiro_item = if.id',
			array('item')
		);
		
		$row->joinInner(
			array('gf'=>'financeiro_grupos'),
			'if.id_financeiro_grupo = gf.id',
			array('descricao', 'idGrupo'=>'id')
		);
		
		$row->joinLeft(
			array('u2'=>'usuarios'),
			'u2.id = lf.id_usuario_alteracao',
			array('ultimoUsuario'=>'nome')
		);
		
		if(isset($arr['id_empresa'])) {
			
			$row->where("gf.id_empresa = '" . $arr['id_empresa'] . "' OR gf.id_empresa is NULL");
			$row->where("if.id_empresa = " . $arr['id_empresa']);
			
		}
		
		if(isset($arr['data_inicial']) || isset($arr['data_final'])){
			
			$row->where("data_lancamento BETWEEN '".$arr['data_inicial']."' AND '".$arr['data_final']."'");
			
		}
		
		if(isset($arr['baixado']) && $arr['baixado'] == 1){
			
			$row->where("lf.baixado = 1");
			
		}elseif(isset($arr['baixado']) && $arr['baixado'] == 2){
			
			$row->where("lf.baixado = 0");
			
		}
		
		if(isset($arr['item']) && $arr['item']){
			
			$row->where("lf.id_financeiro_item = ".$arr['item']);
			
		}
		
		if(isset($arr['id']) && $arr['id']){
			
			$row->where("lf.id = " . $arr['id']);
			
		}
		
		if(isset($arr['nao_baixado']) && $arr['nao_baixado'] == 1){
			
			$row->where("lf.baixado = 0");
			
		}
		
		//$row->order('gf.descricao');
		
		$row->order('lf.data_lancamento DESC');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function getlancamentoPorDataItem($arr){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('lf'=>'financeiro_lancamentos'),
		array('id')
		);
		
		if(isset($arr['item'])) {
			
			$row->where("lf.id_financeiro_item = ".$arr['item']);
			
		}
		
		if(isset($arr['data_inicial']) || isset($arr['data_final'])) {
			
			$row->where("lf.data_lancamento BETWEEN '".$arr['data_inicial']."' AND '".$arr['data_final']."'");
			
		}else{
		
			$row->where("lf.data_lancamento <= '".@date("Y-m-d", mktime(0, 0, 0, @date("m")+1, 0, @date("Y")))."'");
			$row->where("lf.data_lancamento >= '".@date("Y-m-d", mktime(0, 0, 0, @date("m"), 1, @date("Y")))."'");
		
		}
		
		$row->where("lf.baixado = 1");

		$row->order('id DESC');

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

}
