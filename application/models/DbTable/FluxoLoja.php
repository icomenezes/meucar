<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_FluxoLoja extends Zend_Db_Table_Abstract
{

	protected $_name = 'fluxo_loja';


	public function getFluxoNovoRelatorio($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fl'=>'fluxo_loja'),
		array('*')
		);

		$row->joinInner(
			array('u'=>'usuarios'),
			'fl.id_usuario = u.id',
			array('id_perfil', 'nomeUsuario'=>'nome', 'id_empresa')
		);


		if(isset($arr['relatorio_projetado']) && $arr['relatorio_projetado']){
			$row->where("u.relatorio_projetado = 1");
		}

		if(isset($arr['duas_lojas']) && $arr['duas_lojas']){
			$row->where("u.id_empresa = 3 OR u.id_empresa = 239");
		}else{
			$row->where("u.id_empresa = ".$_SESSION['sessionUser']['id_empresa']);
		}


		
		$row->where("(fl.id_origem_cliente = 6 OR fl.id_origem_cliente = 7 OR fl.id_origem_cliente = 8 OR fl.id_origem_cliente = 9)");
		  

		if(isset($arr['id_usuario']) && $arr['id_usuario'] != "0"){

			$row->where("fl.id_usuario = ".$arr['id_usuario']);
			
		}

		if(isset($arr['data_inicial'])){

			$row->where("fl.data >= '".$arr['data_inicial']."'");

		}

		if(isset($arr['data_final'])){

			$row->where("fl.data <= '".$arr['data_final']."'");

		}

		if(isset($arr['data'])){

			$row->where("fl.data = '".$arr['data']."'");

		}

		$row->order('fl.id_origem_cliente ASC');
		$row->order('fl.data ASC');
		$row->order('u.nome ASC');

		//echo $row->__toString();exit;

		return $row->query()->fetchAll();
		
	}


	public function getFluxoData($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fl'=>'fluxo_loja'),
		array('*')
		);
		  
		$row->where("fl.id_origem_cliente = ".$arr['id_origem_cliente']);
		$row->where("fl.id_usuario = ".$_SESSION['sessionUser']['id']);
		$row->where("fl.data ='".$arr['data']."'");

		$row->limit(1);
		return $row->query()->fetchAll();
		
	}


	public function edt($id, $dados){
		
		try{

			return $this->update($dados, 'id = '.$id);

		}catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
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
		array('fl'=>'fluxo_loja'),
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
	
	public function replaceFluxo($arr = array()){
		
		$db = $this->getConnDb();
		
		$sql = "REPLACE INTO fluxo_loja(id_usuario, id_origem_cliente, qtd, data) VALUES(".$arr['id_usuario'].", ".$arr['id_origem_cliente'].", ".$arr['qtd'].", '".$arr['data']."')";
		
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
			array('fl'=>'fluxo_loja'),
			array('*')
		);
		
		$row->joinInner(
			array('u'=>'usuarios'),
			'fl.id_usuario = u.id',
			array('id_perfil', 'nomeUsuario'=>'nome', 'id_empresa')
		);
		
		$row->joinInner(
			array('oc'=>'origem_clientes'),
			'fl.id_origem_cliente = oc.id',
			array('descricao')
		);
		
		if($arr['id_perfil = 3']){
			
			$row->where('fl.id_usuario = ' . $arr['id_usuario']);
		
		}
		
		if($arr['id_empresa']){
		
			$row->where('u.id_empresa =' . $arr['id_empresa']);
		
		}
		
		if($arr['id_perfil']){
			
			if($arr['id_perfil'] == 2){
			
				$row->where('u.id_perfil = 2 OR u.id_perfil = 4 OR u.id_perfil = 3');
			
			}elseif($arr['id_perfil'] == 4){
			
				$row->where('u.id_perfil = 4 OR u.id_perfil = 3');
			
			}elseif($arr['id_perfil'] == 3){
			
				$row->where('u.id_perfil = 3');
			
			}
			
		}
		
		if($arr['fl']){
			
			$row->where('oc.id <= 5 OR oc.id >= 10');
		
		}
		
		if($arr['rd']){
		
			$row->where('oc.id > 5 OR oc.id <= 9');
		
		}
		
		if($arr['dataHoje']){
			
			$data = $arr['dataHoje'];
		
			$row->where('fl.data = ' . "'".$data."'");
			
		}
	
		$row->order('fl.data');
		
		//echo $row->__toString();exit;
		
		return $row->query()->fetchAll();
	
	}
	
	public function somaFluxoLoja59($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('fl'=>'fluxo_loja'),
			array('id_origem_cliente', 'qtd', 'geralSoma'=>'SUM(fl.qtd)')
		);
		
		$row->group('id_origem_cliente');
		
		$row->joinInner(
			array('oc'=>'origem_clientes'),
			'fl.id_origem_cliente = oc.id',
			array('descricao')
		);

			
		$row->joinInner(
			array('u'=>'usuarios'),
			'fl.id_usuario = u.id',
			array('id_perfil', 'nomeUsuario'=>'nome')
		);
		
		
		if(isset($arr['id_empresa'])){
		
			$row->where("u.id_empresa = ".$arr['id_empresa']);
		
		}
		
		if(isset($arr['id_usuario'])){
		
			$row->where("fl.id_usuario = ".$arr['id_usuario']);
		
		}
		
		if(isset($arr['data_inicial'])){
		
			$row->where("fl.data >= '".$arr['data_inicial']."'");
		
		}
		
		if(isset($arr['data_final'])){
		
			$row->where("fl.data <= '".$arr['data_final']."'");
		
		}
		
		$row->where("oc.id > 5 AND oc.id <=9");
		
		$row->order('id_origem_cliente');
		
		//echo $row->__toString();exit;
		
		return $row->query()->fetchAll();

	}
	
	public function somaFluxoLoja15Hoje($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('fl'=>'fluxo_loja'),
			array('id_origem_cliente', 'qtd', 'geralSoma'=>'SUM(fl.qtd)')
		);
		
		$row->group('id_origem_cliente');
		
		$row->joinInner(
			array('oc'=>'origem_clientes'),
			'fl.id_origem_cliente = oc.id',
			array('descricao')
		);

			
		$row->joinInner(
			array('u'=>'usuarios'),
			'fl.id_usuario = u.id',
			array('id_perfil', 'nomeUsuario'=>'nome')
		);
		
		
		if(isset($arr['id_empresa'])){
		
			$row->where("u.id_empresa = ".$arr['id_empresa']);
		
		}
		
		if(isset($arr['id_usuario'])){
		
			$row->where("fl.id_usuario = ".$arr['id_usuario']);
		
		}

		$row->where("fl.data = '".@date("Y-m-d")."'");

		
		$row->where("oc.id <=5");
		
		$row->order('id_origem_cliente');
		
		//echo $row->__toString();exit;
		
		return $row->query()->fetchAll();

	}
	
	public function somaFluxoLoja15($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('fl'=>'fluxo_loja'),
			array('id_origem_cliente', 'qtd', 'geralSoma'=>'SUM(fl.qtd)')
		);
		
		$row->group('id_origem_cliente');
		
		$row->joinInner(
			array('oc'=>'origem_clientes'),
			'fl.id_origem_cliente = oc.id',
			array('descricao')
		);

			
		$row->joinInner(
			array('u'=>'usuarios'),
			'fl.id_usuario = u.id',
			array('id_perfil', 'nomeUsuario'=>'nome')
		);
		
		
		if(isset($arr['id_empresa'])) {
		
			$row->where("u.id_empresa = ".$arr['id_empresa']);
		
		}
		
		if(isset($arr['id_usuario'])) {
		
			$row->where("fl.id_usuario = ".$arr['id_usuario']);
		
		}
		
		if(isset($arr['data_inicial'])) {
		
			$row->where("fl.data >= '".$arr['data_inicial']."'");
		
		}
		
		if(isset($arr['data_final'])) {
		
			$row->where("fl.data <= '".$arr['data_final']."'");
		
		}
		
		$row->where("oc.id <=5");
		
		$row->order('id_origem_cliente');
		
		//echo $row->__toString();exit;
		
		return $row->query()->fetchAll();

	}
	
	public function somaFluxoLoja59Hoje($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('fl'=>'fluxo_loja'),
			array('id_origem_cliente', 'qtd', 'geralSoma'=>'SUM(fl.qtd)')
		);
		
		$row->group('id_origem_cliente');
		
		$row->joinInner(
			array('oc'=>'origem_clientes'),
			'fl.id_origem_cliente = oc.id',
			array('descricao')
		);

			
		$row->joinInner(
			array('u'=>'usuarios'),
			'fl.id_usuario = u.id',
			array('id_perfil', 'nomeUsuario'=>'nome')
		);
		
		
		if(isset($arr['id_empresa'])){
		
			$row->where("u.id_empresa = ".$arr['id_empresa']);
		
		}
		
		if(isset($arr['id_usuario'])){
		
			$row->where("fl.id_usuario = ".$arr['id_usuario']);
		
		}

		$row->where("fl.data = '".@date("Y-m-d")."'");

		
		$row->where("oc.id > 5 AND oc.id <=9");
		
		$row->order('id_origem_cliente');
		
		//echo $row->__toString();exit;
		
		return $row->query()->fetchAll();

	}
	
	public function somaFluxoLoja($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('fl'=>'fluxo_loja'),
			array('id_origem_cliente', 'qtd', 'soma'=>'SUM(fl.qtd)')
		);
		$row->group('id_origem_cliente');
		
		/*$row->joinInner(
			array('fl2'=>'fluxo_loja'),
			'fl2.data > '."'". @date("Y-m-1")."' AND fl2.data <= "."'". @date("Y-m-d")."'",
			array('dataFl' => 'data', 'qtdFl' => 'qtd', 'somaFl' => 'SUM(fl2.qtd)')
		);
		$row->group(id_origem_cliente);*/
		
		$row->joinInner(
			array('oc'=>'origem_clientes'),
			'fl.id_origem_cliente = oc.id',
			array('descricao')
		);
		
		
		if(isset($arr['id_usuario'])){
			
			$row->joinInner(
				array('u'=>'usuarios'),
				'fl.id_usuario = u.id',
				array('id_perfil', 'nomeUsuario'=>'nome')
			);
			
			$row->where('fl.id_usuario = ' . $arr['id_usuario']);
			
			if(isset($arr['id_perfil'])){
			
				if($arr['id_perfil'] == 2){
				
					$row->where('u.id_perfil = 2 OR u.id_perfil = 4 OR u.id_perfil = 3');
				
				}elseif($arr['id_perfil'] == 4){
				
					$row->where('u.id_perfil = 4 OR u.id_perfil = 3');
				
				}elseif($arr['id_perfil'] == 3){
				
					$row->where('u.id_perfil = 3');
				
				}
				
			}
			
		}
		
		if(isset($arr['id_origem_cliente'])){
			
			$row->where("fl.id_origem_cliente = ".$arr['id_origem_cliente']);
		
		}
		
		if(isset($arr['data_inicial']) || isset($arr['data_final'])){
			
			$row->where("fl.data BETWEEN '".$arr['data_inicial']."' AND '".$arr['data_final']."'");
			
		}else{
		
			$row->where("fl.data = " . "'" . @date('Y-m-d') . "'");
		
		}
		
		if($arr['all']){
			
			//true;
			
		}else{
		
			$row->where("oc.id <= 5 OR oc.id >9");
		
		}
		
		if($arr['rd']){
		
			$row->where("oc.id > 5 <= 9");
		
		}
		
		$row->order('fl.data');
		
		return $row->query()->fetchAll();
	
	}
	
	public function somaFluxoLojaMes($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('fl'=>'fluxo_loja'),
			array('id_origem_cliente', 'qtd', 'mensal'=>'SUM(fl.qtd)')
		);
		$row->group(id_origem_cliente);
		
		$row->joinInner(
			array('oc'=>'origem_clientes'),
			'fl.id_origem_cliente = oc.id',
			array('descricao')
		);
		
		$row->joinInner(
				array('u'=>'usuarios'),
				'fl.id_usuario = u.id',
				array('id_perfil', 'nomeUsuario'=>'nome')
		);
		
		
		if($arr['id_usuario']){
			
			$row->where('fl.id_usuario = ' . $arr['id_usuario']);
			
		}
		
		
		if($arr['id_empresa']){
			
			$row->where('u.id_empresa = ' . $arr['id_empresa']);
			
		}
		
		$dataInicial = @date('Y-m-01');
		$dataFinal = @date('Y-m-d');
		
		if($arr['data_inicial'] || $arr['data_final']){
		
			$row->where("fl.data BETWEEN '".$arr['data_inicial']."' AND '".$arr['data_final']."'");
			
		}else{
		
			$row->where("fl.data BETWEEN '".$dataInicial."' AND '".$dataFinal."'");
			
		}
		
		
		
		$row->where("oc.id <=5");
		

		
		$row->order('fl.data');
		
		//echo $row->__toString();exit;
		
		return $row->query()->fetchAll();
	
	}
	
	public function somaFluxoLojaGeralMes($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('fl'=>'fluxo_loja'),
			array('id_origem_cliente', 'qtd', 'geralMensal'=>'SUM(fl.qtd)')
		);
		$row->group(id_origem_cliente);
		
		/*$row->joinInner(
			array('fl2'=>'fluxo_loja'),
			'fl2.data > '."'". @date("Y-m-1")."' AND fl2.data <= "."'". @date("Y-m-d")."'",
			array('dataFl' => 'data', 'qtdFl' => 'qtd', 'somaFl' => 'SUM(fl2.qtd)')
		);
		$row->group(id_origem_cliente);*/
		
		$row->joinInner(
			array('oc'=>'origem_clientes'),
			'fl.id_origem_cliente = oc.id',
			array('descricao')
		);
		
		
		if($arr['id_usuario']){
			
			$row->joinInner(
				array('u'=>'usuarios'),
				'fl.id_usuario = u.id',
				array('id_perfil', 'nomeUsuario'=>'nome')
			);
			
			$row->where('fl.id_usuario = ' . $arr['id_usuario']);
			
			if($arr['id_perfil']){
			
				if($arr['id_perfil'] == 2){
				
					$row->where('u.id_perfil = 2 OR u.id_perfil = 4 OR u.id_perfil = 3');
				
				}elseif($arr['id_perfil'] == 4){
				
					$row->where('u.id_perfil = 4 OR u.id_perfil = 3');
				
				}elseif($arr['id_perfil'] == 3){
				
					$row->where('u.id_perfil = 3');
				
				}
				
			}
			
		}
		$dataInicial = @date('Y-m-01');
		$dataFinal = @date('Y-m-d');
		
		if($arr['data_inicial'] || $arr['data_final']){
		
			$row->where("fl.data BETWEEN '".$arr['data_inicial']."' AND '".$arr['data_final']."'");
			
		}else{
		
			$row->where("fl.data BETWEEN '".$dataInicial."' AND '".$dataFinal."'");
			
		}
		
		
		
			$row->where("oc.id > 5 AND oc.id <=9");
		
		
		
		/* if($arr['rd']){
		
			$row->where("oc.id > 5");
		
		} */
		
		$row->order('fl.data');
		
		//echo $row->__toString();exit;
		
		return $row->query()->fetchAll();
	
	}
	
	
	
	public function somaFluxoLojaGeral($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('fl'=>'fluxo_loja'),
			array('id_origem_cliente', 'qtd', 'geralSoma'=>'SUM(fl.qtd)')
		);
		
		$row->group(id_origem_cliente);
		
		$row->joinInner(
			array('oc'=>'origem_clientes'),
			'fl.id_origem_cliente = oc.id',
			array('descricao')
		);
		
		
		if($arr['id_usuario']){
			
			$row->joinInner(
				array('u'=>'usuarios'),
				'fl.id_usuario = u.id',
				array('id_perfil', 'nomeUsuario'=>'nome')
			);
			
			$row->where('fl.id_usuario = ' . $arr['id_usuario']);
			
			if($arr['id_perfil']){
			
				if($arr['id_perfil'] == 2){
				
					$row->where('u.id_perfil = 2 OR u.id_perfil = 4 OR u.id_perfil = 3');
				
				}elseif($arr['id_perfil'] == 4){
				
					$row->where('u.id_perfil = 4 OR u.id_perfil = 3');
				
				}elseif($arr['id_perfil'] == 3){
				
					$row->where('u.id_perfil = 3');
				
				}
				
			}
			
		}
		
		if($arr['data_inicial'] || $arr['data_final']){
			
			$row->where("fl.data BETWEEN '".$arr['data_inicial']."' AND '".$arr['data_final']."'");
			
		}else{
		
			$row->where("fl.data = " . "'" . @date('Y-m-d') . "'");
		
		}
		
		$row->where("oc.id > 5 AND oc.id <=9");
		
		
		$row->order('fl.data');
		
		//echo $row->__toString();exit;
		
		return $row->query()->fetchAll();
	
	}

	public function fluxoLojaDiario($arr){

		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('fl'=>'fluxo_loja'),
			array('*')
		);

		$row->joinInner(
			array('u'=>'usuarios'),
			'fl.id_usuario = u.id',
			array('id_perfil', 'nomeUsuario'=>'nome')
		);

		$row->where("u.id_empresa = ".$_SESSION['sessionUser']['id_empresa']);

		$row->where("fl.data >= '".@date('Y-m-01')."' AND fl.data <='".@date('Y-m-d')."'");

		if(isset($arr['id_usuario'])) {

			$row->where("u.id = ".$arr['id_usuario']);

		}

		$row->order('fl.data DESC');
		$row->order('fl.id_origem_cliente ASC');

		return $row->query()->fetchAll();

	}

	public function fluxoLojaTotal($arr){

		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('fl'=>'fluxo_loja'),
			array('id_origem_cliente', 'soma'=>'SUM(fl.qtd)')
		);

		$row->joinInner(
			array('u'=>'usuarios'),
			'fl.id_usuario = u.id',
			array('id_perfil', 'nomeUsuario'=>'nome')
		);

		//$row->where("u.id_empresa = ".$_SESSION['sessionUser']['id_empresa']);

		if($arr['data_inicial'] || $arr['data_final']){
			
			$row->where("fl.data >= '".$arr['data_inicial']."' AND  fl.data <= '".$arr['data_final']."'");
			
		}else{
		
			$row->where("fl.data >= '".@date('Y-m-01')."' AND fl.data <='".@date('Y-m-d')."'");
		
		}

		

		if($arr['id_usuario']){

			$row->where("u.id = ".$arr['id_usuario']);

		}

		if($arr['id_origem_cliente']){

			$row->where("fl.id_origem_cliente = ".$arr['id_origem_cliente']);

		}

		$row->order('fl.data DESC');
		$row->order('fl.id_origem_cliente ASC');

		return $row->query()->fetchAll();

	}

	
	public function relatorioFluxoLoja($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('fl'=>'fluxo_loja'),
			array('id_origem_cliente', 'soma'=>'SUM(fl.qtd)')
		);

		$row->group(id_origem_cliente);
		
		$row->joinInner(
			array('oc'=>'origem_clientes'),
			'fl.id_origem_cliente = oc.id',
			array('idOrigemCliente' => 'id','descricao', 'id_empresa')
		);
		
		
		if($arr['id_usuario']){
			
			$row->where('fl.id_usuario = ' . $arr['id_usuario']);
			
		}else{
		
			$row->joinInner(
				array('u'=>'usuarios'),
				'fl.id_usuario = u.id',
				array('')
			);
			
			$row->where('u.id_empresa = '.$arr['id_empresa']);
		
		}
		
		if($arr['id_empresa']){
		
			$row->where('oc.id_empresa =' . $arr['id_empresa'] . ' OR oc.id_empresa is NULL');
		
		}
		
		if($arr['data_inicial'] || $arr['data_final']){
			
			$row->where("fl.data > '".$arr['data_inicial']."' AND  fl.data < '".$arr['data_final']."'");
			
		}else{
		
			$row->where("fl.data = " . "'" . @date('Y-m-d') . "'");
		
		}
		
		if($arr['fl']){
		
			$row->where("oc.id <= 5 OR oc.id > 9");
		
		}
		
		if($arr['rd']){
		
			$row->where("oc.id > 5 AND oc.id <= 9");
		
		}
		
		$row->order('oc.descricao');
		
		//echo $row->__toString();
		//exit;
		
		return $row->query()->fetchAll();
	
	}


}
