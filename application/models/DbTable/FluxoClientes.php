<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_FluxoClientes extends Zend_Db_Table_Abstract
{

	protected $_name = 'fluxo_clientes';

	public function getFluxoHash($hash){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fc'=>'fluxo_clientes'),
		array('*')
		);
		
		$row->where('fc.hash = "'.$hash.'"');
		
		$row->limit(1);

		return $row->query()->fetchAll();
		
	}
	
	
	public function getRelatorioFluxoClientes($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fc'=>'fluxo_clientes'),
		array('*')
		);
		
		$row->joinLeft(
			array('m'=>'motivos'),
			'fc.motivo_pre = m.id',
			array('id_motivo_pre'=>'m.id','descricao')
		);
		
		if($arr['veiculo']){ 
		
			$row->joinInner(
				array('vf'=>'veiculos_fluxo'),
				'fc.id = vf.id_fluxo_cliente',
				array('veiculo')
			);

			$row->where("vf.veiculo = '".$arr['veiculo']."'");
		
		}

		if($arr['id_empresa']){
		
			$row->where('fc.id_empresa = '.$_SESSION['sessionUser']['id_empresa']);
		
		}
		
		if($arr['nome']){ 
		
			$row->where("fc.nome LIKE '".$arr['nome']."%'");
		
		}
		
		if($arr['vendedor']){ 
		
			$row->where("fc.id_usuario = ".$arr['vendedor']);
		
		}
		
		
		if($arr['data_inicial']){ 
		
			$row->where("fc.data >= '".$arr['data_inicial']."'");
		
		}
		
		if($arr['data_final']){ 
		
			$row->where("fc.data <= '".$arr['data_final']."'");
		
		}
		
		if($arr['origem']){ 
		
			$row->where("fc.origem = '".$arr['origem']."'");
		
		}
		
		if($arr['resultado']){ 
		
			$row->where("fc.resultado = '".$arr['resultado']."'");
		
		}
		
		if($arr['motivo_pre']){ 
		
			$row->where("fc.motivo_pre = ".$arr['motivo_pre']);
		
		}
		
		if($arr['data_agendamento']){ 
		
			$row->where("fc.data_agendamento IS NOT NULL");
		
		}

		if($arr['imap_origem']){
			
			if($arr['email_cliente']){ 
				$row->where("fc.email = '".$arr['email_cliente']."'");
			}

			$row->where("fc.imap_origem = 1");
			$row->limit(1);
		
		}

		$row->order('atualizacao DESC');
		$row->order('data DESC');

		//echo $row->__toString();
		return $row->query()->fetchAll();
		
	}
	
	
	public function getClienteFluxoPorNome($nome){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fc'=>'fluxo_clientes'),
		array('*')
		);
		
		$row->where('fc.id_empresa = '.$_SESSION['sessionUser']['id_empresa']);
		
		//$row->where('fc.nome LIKE "'.substr(current(explode(" ",$nome)),0,1).'%"');
		
		$row->where("fc.resultado LIKE '".$arr['resultado']."%'");				
		$row->where("gerado_negociacao IS NOT NULL OR gerado_negociacao <> 0");				
		$row->where("gerado_negociacao <> 0");	
		
		$row->order('fc.id DESC');

		//echo $row->__toString();
		return $row->query()->fetchAll();
		
	}
	
	
	public function getUltimoVendedorClientesFluxo($data){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fc'=>'fluxo_clientes'),
		array('*')
		);
		
		$row->where('fc.id_empresa = '.$_SESSION['sessionUser']['id_empresa']);
		
		$row->where('fc.data < "'.$data.' 00:00:00"');
		
		$row->where("fc.imap_origem = 1");
		
		$row->order('fc.id DESC');
		
		$row->limit(1);
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
		
	}
	
	
	public function getFluxoVendedor($idVendedor, $data){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fc'=>'fluxo_clientes'),
		array('*')
		);
		
		$row->where('fc.id_usuario = '.$idVendedor);
		
		$row->where('fc.data <= "'.$data.' 23:59:59"');
		
		$row->where('fc.data >= "'.$data.' 00:00:00"');
		
		$row->limit(1);
		
		//echo $row->__toString();

		return $row->query()->fetchAll();
		
	}
	
	
	public function getFluxoClienteEmail($data){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fc'=>'fluxo_clientes'),
		array('*')
		);
		
		$row->joinInner(
			array('oc'=>'origem_clientes'),
			'oc.id = fc.origem',
			array('descricao')
		);
		
		$row->joinInner(
			array('u'=>'usuarios'),
			'u.id = fc.id_usuario',
			array('nome_usuario'=>'nome')
		);

		$row->where('fc.id_empresa = '.$_SESSION['sessionUser']['id_empresa']);
		
		$row->where('fc.ultima_visualizacao = "'.$data.'"');
		
		$row->where('fc.envia_email = 0');
		
		$row->where('fc.resultado != "Desistiu"');
		$row->where('fc.resultado != "Fechou"');
		$row->where('fc.resultado != "Concorrente"');
		
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
		
	}
	
	
	public function getOrigemClientes($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fc'=>'fluxo_clientes'),
		array('*')
		);

		$row->joinInner(
			array('oc'=>'origem_clientes'),
			'oc.id = fc.origem',
			array('descricao')
		);
		
		$row->joinInner(
			array('u'=>'usuarios'),
			'u.id = fc.id_usuario',
			array('nome_usuario'=>'nome')
		);

		if(isset($arr['id_empresa'])){ 
		
			$row->where('fc.id_empresa = '.$arr['id_empresa']);
		
		}

		if(isset($arr['id_usuario'])){ 
		
			$row->where('fc.id_usuario = '.$arr['id_usuario']);
		
		}
		
		if(isset($arr['origem'])){ 
		
			$row->where('fc.origem = '.$arr['origem']);
		
		}

		if(isset($arr['data_inicial'])){ 
		
			$row->where("fc.data >= '".$arr['data_inicial']." 00:00:00'");
		
		}
		
		if(isset($arr['data_final'])){

			$row->where("fc.data <= '".$arr['data_final']." 23:59:59'");
		
		}

		$row->order('fc.data DESC');
		
		if(isset($arr['limite'])){
			$row->limit($arr['limite']);
		}

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

	public function getClienteFluxoIdNegociacao($id){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fc'=>'fluxo_clientes'),
		array('*')
		);
		
		$row->joinLeft(
			array('vf'=>'veiculos_fluxo'),
			'fc.id = vf.id_fluxo_cliente',
			array('veiculo', 'ano_modelo')
		);
		 
		 
		$row->where("fc.id_empresa = ".$_SESSION['sessionUser']['id_empresa']);
		 
		$row->where('fc.gerado_negociacao = '.$id);
		$row->order('id DESC');
		$row->limit(1);

		return $row->query()->fetchAll();
		
	}
	
	public function getClienteFluxo($id){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fc'=>'fluxo_clientes'),
		array('*')
		);
		
		$row->joinLeft(
			array('vf'=>'veiculos_fluxo'),
			'fc.id = vf.id_fluxo_cliente',
			array('veiculo', 'ano_modelo')
		);
		 
		 
		$row->where('fc.id = '.$id);
		$row->order('id DESC');
		$row->limit(1);

		return $row->query()->fetchAll();
		
	}
	
	public function getFluxoNome($nome){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fc'=>'fluxo_clientes'),
		array('*')
		);
		
		$row->where('fc.nome = "'.$nome.'"');
		
		$row->limit(1);

		return $row->query()->fetchAll();
		
	}
	
	public function getFluxoDataResposta($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fc'=>'fluxo_clientes'),
		array('*')
		);
		
		$row->joinLeft(
			array('u'=>'usuarios'),
			'fc.id_usuario = u.id',
			array('nome_usuario'=>'u.nome')
		);
		
		$row->where("fc.id_empresa = ".$_SESSION['sessionUser']['id_empresa']);
		
		if(isset($arr['data_inicial']) && $arr['data_inicial'] != ""){
			$row->where("fc.data_tempo_resposta >= '".$arr['data_inicial']." 00:00:00'");
		}
		
		if(isset($arr['data_final']) && $arr['data_final'] != ""){
			$row->where("fc.data_tempo_resposta <= '".$arr['data_final']." 23:59:59'");
		}
		
		if(isset($arr['vendedor'])){ 
			$row->where("fc.id_usuario = ".$arr['vendedor']);
		}

		return $row->query()->fetchAll();
		
	}
	
	
	public function getFluxoDataUltimaVisu($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fc'=>'fluxo_clientes'),
		array('*')
		);
		
		$row->joinLeft(
			array('u'=>'usuarios'),
			'fc.id_usuario = u.id',
			array('nome_usuario'=>'u.nome')
		);
		
		$row->where("fc.id_empresa = ".$_SESSION['sessionUser']['id_empresa']);
		
		if(isset($arr['data_inicial']) && $arr['data_inicial'] != ""){
			$row->where("fc.ultima_visualizacao >= '".$arr['data_inicial']."'");
		}
		
		if(isset($arr['data_final']) && $arr['data_final'] != ""){
			$row->where("fc.ultima_visualizacao <= '".$arr['data_final']."'");
		}
		
		if(isset($arr['vendedor'])){ 
			$row->where("fc.id_usuario = ".$arr['vendedor']);
		}

		return $row->query()->fetchAll();
		
	}
	
	
	public function getClientesFluxo($arr){

		$data = true;
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fc'=>'fluxo_clientes'),
		array('*')
		);
		
		$row->joinLeft(
			array('m'=>'motivos'),
			'fc.motivo_pre = m.id',
			array('id_motivo_pre'=>'m.id','descricao')
		);
		
		$row->joinLeft(
			array('u'=>'usuarios'),
			'fc.id_usuario = u.id',
			array('nome_usuario'=>'u.nome')
		);
		
		if(isset($arr['veiculo']) && $arr['veiculo'] != "") { 
		
			$row->joinInner(
				array('vf'=>'veiculos_fluxo'),
				'fc.id = vf.id_fluxo_cliente',
				array('veiculo')
			);

			$row->where("vf.veiculo = '".$arr['veiculo']."'");
		
		}

		if(isset($arr['id_empresa']) && $arr['id_empresa'] != ""){
		
			$row->where('fc.id_empresa = '.$arr['id_empresa']);
		
		}
		
		if(isset($arr['nome']) && $arr['nome'] != ""){ 
		
			$row->where("fc.nome LIKE '".$arr['nome']."%'");
		
		}
		
		if(isset($arr['vendedor']) && $arr['vendedor'] != ""){ 
		
			$row->where("fc.id_usuario = ".$arr['vendedor']);
		
		}
		
		if(isset($arr['origem']) && $arr['origem'] != ""){ 
		
			$row->where("fc.origem = ".$arr['origem']);
		
		}
		
		if($_SESSION['sessionUser']['id_empresa'] == 3){
			if(isset($arr['origem_sites'])){
				if($arr['origem_sites'] == "Comprecar"){
					$row->where("fc.origem = 1 OR fc.origem = 34 OR fc.origem = 35 OR fc.origem = 36");
				}elseif($arr['origem_sites'] == "Facebook"){
					$row->where("fc.origem = 17 OR fc.origem = 37 OR fc.origem = 38 OR fc.origem = 39");
				}elseif($arr['origem_sites'] == "Icarros"){
					$row->where("fc.origem = 15 OR fc.origem = 40 OR fc.origem = 41 OR fc.origem = 42");
				}elseif($arr['origem_sites'] == "Mercado Livre"){
					$row->where("fc.origem = 23 OR fc.origem = 43 OR fc.origem = 44 OR fc.origem = 45");
				}elseif($arr['origem_sites'] == "Meu Car"){
					$row->where("fc.origem = 2 OR fc.origem = 46 OR fc.origem = 47 OR fc.origem = 48");
				}elseif($arr['origem_sites'] == "Meu Carro Novo"){
					$row->where("fc.origem = 24 OR fc.origem = 50 OR fc.origem = 51");
				}elseif($arr['origem_sites'] == "Olx"){
					$row->where("fc.origem = 16 OR fc.origem = 52 OR fc.origem = 53 OR fc.origem = 54");
				}elseif($arr['origem_sites'] == "Site Loja"){
					$row->where("fc.origem = 14 OR fc.origem = 55 OR fc.origem = 56 OR fc.origem = 57");
				}elseif($arr['origem_sites'] == "Webmotors"){
					$row->where("fc.origem = 13");
				}
			}
		}elseif($_SESSION['sessionUser']['id_empresa'] == 239){
			if(isset($arr['origem_sites'])){
				if($arr['origem_sites'] == "Comprecar"){
					$row->where("fc.origem = 1 OR fc.origem = 58 OR fc.origem = 59 OR fc.origem = 60");
				}elseif($arr['origem_sites'] == "Facebook"){
					$row->where("fc.origem = 32 OR fc.origem = 61 OR fc.origem = 62 OR fc.origem = 63");
				}elseif($arr['origem_sites'] == "Icarros"){
					$row->where("fc.origem = 30 OR fc.origem = 64 OR fc.origem = 65 OR fc.origem = 66");
				}elseif($arr['origem_sites'] == "Mercado Livre"){
					$row->where("fc.origem = 26 OR fc.origem = 67 OR fc.origem = 68 OR fc.origem = 69");
				}elseif($arr['origem_sites'] == "Meu Car"){
					$row->where("fc.origem = 2 OR fc.origem = 70 OR fc.origem = 71 OR fc.origem = 72");
				}elseif($arr['origem_sites'] == "Meu Carro Novo"){
					$row->where("fc.origem = 27 OR fc.origem = 73 OR fc.origem = 74 OR fc.origem = 75");
				}elseif($arr['origem_sites'] == "Olx"){
					$row->where("fc.origem = 28 OR fc.origem = 76 OR fc.origem = 77 OR fc.origem = 79");
				}elseif($arr['origem_sites'] == "Site Loja"){
					$row->where("fc.origem = 25 OR fc.origem = 80 OR fc.origem = 81 OR fc.origem = 82");
				}elseif($arr['origem_sites'] == "Webmotors"){
					$row->where("fc.origem = 33");
				}
			}
		}
		
		if(isset($arr['resultado']) && $arr['resultado'] != ""){ 
			if($arr['resultado'] == "Fechou"){
				$row->where("fc.resultado LIKE '".$arr['resultado']."%'");				
				$row->where("gerado_negociacao IS NOT NULL OR gerado_negociacao <> 0");				
				$row->where("gerado_negociacao <> 0");				
			}else{
				$row->where("fc.resultado LIKE '".$arr['resultado']."%'");				
			}
		}
		
		if(isset($arr['motivo_pre']) && $arr['motivo_pre'] != ""){ 
		
			$row->where("fc.motivo_pre = ".$arr['motivo_pre']);
		
		}
		
		if(isset($arr['filtro']) && $arr['filtro'] == "Hoje"){
		
			$row->where("fc.data <= '".@date('Y-m-d')." 23:59:59'");
			$row->where("fc.data >= '".@date('Y-m-d')." 00:00:00'");
			$data = false;
		
		}
		
		if(isset($arr['filtro']) && $arr['filtro'] == "Agendada"){ 
		
			$row->where("fc.data_agendamento IS NOT NULL");
			$data = false;
		
		}

		if(isset($arr['filtro']) && $arr['filtro'] == "Atrasada"){ 
		
			$row->where("fc.ultima_visualizacao <= '".@date('Y-m-d', mktime(0,0,0, @date('m'), @date('d')-2,@date('Y')))."'");
			$row->where("fc.resultado = 'Negociação' or fc.resultado = 'Vai fechar'");
			//$data = false;
			
		}

		if(isset($arr['data_inicial']) && $data){ 
		
			$row->where("fc.data >= '".$arr['data_inicial']." 00:00:00'");
		
		}
		
		if(isset($arr['data_final']) && $data){ 
		
			$row->where("fc.data <= '".$arr['data_final']." 23:59:59'");
		
		}

		if(isset($arr['imap_origem'])){
			
			if(isset($arr['email_cliente'])){ 
				$row->where("fc.email = '".$arr['email_cliente']."'");
			}

			$row->where("fc.imap_origem = 1");
			$row->limit(1);
		
		}

		$row->order('atualizacao DESC');
		$row->order('data DESC');

		//var_export($row->__toString());
		return $row->query()->fetchAll();
		
	}
	
	public function getClientesFluxoAgendado($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fc'=>'fluxo_clientes'),
		array('*')
		);
		
		$row->joinLeft(
			array('m'=>'motivos'),
			'fc.motivo_pre = m.id',
			array('id_motivo_pre'=>'m.id','descricao')
		);
		
		if(isset($arr['veiculo'])){ 
		
			$row->joinInner(
				array('vf'=>'veiculos_fluxo'),
				'fc.id = vf.id_fluxo_cliente',
				array('veiculo')
			);

			$row->where("vf.veiculo = '".$arr['veiculo']."'");
		
		}

		if(isset($arr['id_empresa'])){
		
			$row->where('fc.id_empresa = '.$arr['id_empresa']);
		
		}
		
		if(isset($arr['nome'])){ 
		
			$row->where("fc.nome LIKE '".$arr['nome']."%'");
		
		}
		
		if(isset($arr['vendedor'])){ 
		
			$row->where("fc.id_usuario = ".$arr['vendedor']);
		
		}
		
		if(isset($arr['origem'])){ 
		
			$row->where("fc.origem = ".$arr['origem']);
		
		}
		
		
		if($_SESSION['sessionUser']['id_empresa'] == 3){
			if(isset($arr['origem_sites'])){
				if($arr['origem_sites'] == "Comprecar"){
					$row->where("fc.origem = 1 OR fc.origem = 34 OR fc.origem = 35 OR fc.origem = 36");
				}elseif($arr['origem_sites'] == "Facebook"){
					$row->where("fc.origem = 17 OR fc.origem = 37 OR fc.origem = 38 OR fc.origem = 39");
				}elseif($arr['origem_sites'] == "Icarros"){
					$row->where("fc.origem = 15 OR fc.origem = 40 OR fc.origem = 41 OR fc.origem = 42");
				}elseif($arr['origem_sites'] == "Mercado Livre"){
					$row->where("fc.origem = 23 OR fc.origem = 43 OR fc.origem = 44 OR fc.origem = 45");
				}elseif($arr['origem_sites'] == "Meu Car"){
					$row->where("fc.origem = 2 OR fc.origem = 46 OR fc.origem = 47 OR fc.origem = 48");
				}elseif($arr['origem_sites'] == "Meu Carro Novo"){
					$row->where("fc.origem = 24 OR fc.origem = 50 OR fc.origem = 51");
				}elseif($arr['origem_sites'] == "Olx"){
					$row->where("fc.origem = 16 OR fc.origem = 52 OR fc.origem = 53 OR fc.origem = 54");
				}elseif($arr['origem_sites'] == "Site Loja"){
					$row->where("fc.origem = 14 OR fc.origem = 55 OR fc.origem = 56 OR fc.origem = 57");
				}elseif($arr['origem_sites'] == "Webmotors"){
					$row->where("fc.origem = 13");
				}
			}
		}elseif($_SESSION['sessionUser']['id_empresa'] == 239){
			if(isset($arr['origem_sites'])){
				if($arr['origem_sites'] == "Comprecar"){
					$row->where("fc.origem = 1 OR fc.origem = 58 OR fc.origem = 59 OR fc.origem = 60");
				}elseif($arr['origem_sites'] == "Facebook"){
					$row->where("fc.origem = 32 OR fc.origem = 61 OR fc.origem = 62 OR fc.origem = 63");
				}elseif($arr['origem_sites'] == "Icarros"){
					$row->where("fc.origem = 30 OR fc.origem = 64 OR fc.origem = 65 OR fc.origem = 66");
				}elseif($arr['origem_sites'] == "Mercado Livre"){
					$row->where("fc.origem = 26 OR fc.origem = 67 OR fc.origem = 68 OR fc.origem = 69");
				}elseif($arr['origem_sites'] == "Meu Car"){
					$row->where("fc.origem = 2 OR fc.origem = 70 OR fc.origem = 71 OR fc.origem = 72");
				}elseif($arr['origem_sites'] == "Meu Carro Novo"){
					$row->where("fc.origem = 27 OR fc.origem = 73 OR fc.origem = 74 OR fc.origem = 75");
				}elseif($arr['origem_sites'] == "Olx"){
					$row->where("fc.origem = 28 OR fc.origem = 76 OR fc.origem = 77 OR fc.origem = 79");
				}elseif($arr['origem_sites'] == "Site Loja"){
					$row->where("fc.origem = 25 OR fc.origem = 80 OR fc.origem = 81 OR fc.origem = 82");
				}elseif($arr['origem_sites'] == "Webmotors"){
					$row->where("fc.origem = 33");
				}
			}
		}
		
		
		if($_SESSION['sessionUser']['id_empresa'] == 3){
			if(isset($arr['origem_sites']) && $arr['origem_sites'] == "Comprecar"){
				$row->where("fc.origem = 1 OR fc.origem = 34 OR fc.origem = 35 OR fc.origem = 36");
			}
		}
		
		if(isset($arr['resultado'])){ 
		
			$row->where("fc.resultado LIKE '".$arr['resultado']."%'");
		
		}
		
		if(isset($arr['motivo_pre'])){ 
		
			$row->where("fc.motivo_pre = ".$arr['motivo_pre']);
		
		}
		
		if(isset($arr['filtro']) && $arr['filtro'] == "Hoje"){ 
		
			$row->where("fc.data_agendamento = '".@date('Y-m-d')."'");
		
		}else{
			
			$row->where("fc.data_agendamento <= '".@date('Y-m-d')."'");
			
		}

		if(isset($arr['imap_origem'])){
			
			if(isset($arr['email_cliente'])){ 
				$row->where("fc.email = '".$arr['email_cliente']."'");
			}

			$row->where("fc.imap_origem = 1");
			$row->limit(1);
		
		}

		$row->order('data_agendamento DESC');
		$row->order('ultima_visualizacao DESC');

		//echo $row->__toString();
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
