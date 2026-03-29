<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Negociacoes extends Zend_Db_Table_Abstract
{

	protected $_name = 'negociacoes';
	
	public function getQtdPorUsuario($arr){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('n'=>'negociacoes'),
			array('qtd'=>'count(*)')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'n.id_veiculo = v.id',
			array('placa','descricao_site','valor_venda')
		);

		if($arr['id_vendedor']){
			$row->where('n.id_vendedor = ' . $arr['id_vendedor']);
		}
		
		if($arr['data_inicial']){
			$row->where("n.data_abertura >= '".$arr['data_inicial']." 00:00:00'");
		}
		
		if($arr['data_final']){
			$row->where("n.data_abertura <='".$arr['data_final']." 23:59:59'");
		}
		
        $row->where("n.id_empresa = 3 OR n.id_empresa = 239");
		$row->where("n.compra = 0");

		if($arr['tipo_venda']){
			if($arr['repasse']){
				$row->where('v.consignado = 2');
			}else{
				$row->where('v.consignado <> 2');
			}
		}
		
		$row->where('n.aprovada <> -1');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	
	
	public function _getPorOrigem($arr){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('n'=>'negociacoes'),
			array('')
		);

		$row->joinInner(
			array('c'=>'clientes'),
			'n.id_cliente = c.id',
			array('nome', 'bairro', 'cidade')
		);
		
		$row->joinLeft(
			array('fc'=>'fluxo_clientes'),
			'fc.gerado_negociacao = n.id',
			array('origem')
		);
		
		$row->joinLeft(
			array('oc'=>'origem_clientes'),
			'oc.id = fc.origem',
			array('descricao')
		);
		
		$row->joinLeft(
			array('oc2'=>'origem_clientes'),
			'oc2.id = c.origem',
			array('descricao2'=>'descricao', 'origem2'=>'oc2.id')
		);
		
		$row->joinInner(
			array('u'=>'usuarios'),
			'n.id_vendedor = u.id',
			array('nome_vendedor'=>'nome', 'id_vendedor'=>'u.id')
		);
		
		
		$row->where('n.id_empresa = '.$_SESSION['sessionUser']['id_empresa']);
		
		
		if(isset($arr['data_inicial']) && $arr['data_inicial'] != ""){
			
			$row->where("n.data_abertura >= '".$arr['data_inicial']." 00:00:00'");
			
		}
		
		if(isset($arr['data_final']) && $arr['data_final'] != ""){
			
			$row->where("n.data_abertura <= '".$arr['data_final']." 23:59:59'");
			
		}
		
		
		if(isset($arr['aprovado']) && $arr['aprovado'] != ""){
			
			$row->where('n.aprovada = '.$arr['aprovado']);
			
		}

		$row->where('fc.gerado_negociacao IS NOT NULL');
			
		if(isset($arr['resultado'])){
			
			$row->where('fc.resultado = "'.$arr['resultado'].'"');
			
		}
		
		if($arr['id_origem']){
			
			$row->where('c.origem = '.$arr['id_origem']);
			
		}
		
		$row->order('fc.origem');

		//echo $row->__toString();
		return $row->query()->fetchAll();
		
	}
	
	
	public function getVendasPorUsuario($arr){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('n'=>'negociacoes'),
			array('*')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'n.id_veiculo = v.id',
			array('placa','descricao_site','valor_venda')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'v.id_modelo = m.id',
			array('marca','modelo')
		);

		$row->joinLeft(
			array('u'=>'usuarios'),
			'n.id_supervisor = u.id',
			array('nomeSupervisor'=>'u.nome')
		);

		if(isset($arr['get_vendedor'])){

			$row->joinLeft(
				array('u2'=>'usuarios'),
				'n.id_vendedor = u2.id',
				array('nome_vendedor'=>'u2.nome')
			);

		}
		
		if(isset($arr['id_vendedor'])){
		
			$row->where('n.id_vendedor = ' . $arr['id_vendedor']);
		
		}
		
		if(isset($arr['id_supervisor'])){
		
			$row->where('n.id_supervisor = ' . $arr['id_supervisor']);
		
		}
		
		if(isset($arr['id_gerente'])){
		
			$row->where('n.id_gerente = ' . $arr['id_gerente']);
		
		}
		
		if(isset($arr['data_inicial_concretizacao']) && $arr['data_inicial_concretizacao'] != ""){
			
			$row->where("n.data_concretizacao >= '".$arr['data_inicial_concretizacao']." 00:00:00'");
			
		}
		
		if(isset($arr['data_final_concretizacao']) && $arr['data_final_concretizacao'] != ""){
			
			$row->where("n.data_concretizacao <='".$arr['data_final_concretizacao']." 23:59:59'");
			
		}
		
		if(isset($arr['id_empresa'])){
		
			$row->where('n.id_empresa = ' . $arr['id_empresa']);
		
		}
		
		$row->where('n.aprovada = 1');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	
	
	public function _getPorIdOrigem($id){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('n'=>'negociacoes'),
			array('id_negociacao'=>'id','data_abertura')
		);
		
		$row->joinInner(
			array('c'=>'clientes'),
			'n.id_cliente = c.id',
			array('*')
		);
		
		$row->where('n.id = '.$id);
		
		return $row->query()->fetchAll();
		
	}
	
	
	public function getFinanciamentos($arr){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('n'=>'negociacoes'),
			array('*')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'n.id_empresa = e.id',
			array('nome_fantasia','idEmpresa'=>'e.id')
		);
		
		$row->joinInner(
			array('f'=>'financeiras_despachantes'),
			'n.id_financeira = f.id',
			array('nome')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'n.id_veiculo = v.id',
			array('placa','descricao_site')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'v.id_modelo = m.id',
			array('marca','modelo')
		);
		
		if(isset($arr['relatorio_fi']) && $arr['relatorio_fi'] == 1){
		
			$row->joinInner(
				array('u'=>'usuarios'),
				'n.id_usuario = u.id',
				array('nomeUsuario'=>'nome')
			);
		
		}
		
		if(isset($arr['id_empresa'])){
		
			$row->where('n.id_empresa = ' . $arr['id_empresa']);
		
		}
		
		if(isset($arr['data_inicial_concretizacao'])){
			
			$row->where("n.data_concretizacao >= '".$arr['data_inicial_concretizacao']." 00:00:00'");
			
		}
		
		if(isset($arr['data_final_concretizacao'])){
			
			$row->where("n.data_concretizacao <='".$arr['data_final_concretizacao']." 23:59:59'");
			
		}
		
		if(isset($arr['id_vendedor'])){
				
			$row->where("n.id_usuario = " . $arr['id_vendedor']);
		
		}
		
		if(isset($arr['id_financeira'])){
	
			$row->where("n.id_financeira = " .$arr['id_financeira']);
		
		}
		
		$row->where('n.aprovada = 1');
		
		if(isset($arr['relatorio_fi'])){
		
			$row->order('f.id');
		
		}else{
		
			$row->order('e.id');
		
		}
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	
	
	public function getRecebimentosNegociacoes($idEmpresa){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('n'=>'negociacoes'),
			array('id')
		);
		
		$row->joinInner(
			array('rn'=>'recebimentos_negociacoes'),
			'rn.id_negociacao = n.id',
			array('valor')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'v.id = n.id_veiculo',
			array()
		);
		
		$row->where('rn.baixado = 0');
		
		$row->where('n.id_empresa = '.$idEmpresa);
		
		$row->where('v.id_negociacao_compra is NULL');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	
	
	public function getTroca($idNegociacao){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('ano_modelo','modelo','marca','preco')
		);
		
		
		//$row->where('v.temp_troca = 1');
		
		$row->where('v.id_negociacao_troca = '.$idNegociacao.' OR v.id_negociacao_troca2 = '.$idNegociacao);
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	
	public function getNegociacoes($id){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('n'=>'negociacoes'),
			array('*')
		);
		
		$row->joinLeft(
			array('v'=>'veiculos'),
			'v.id_negociacao = n.id',
			array('id_veiculo_troca'=>'v.id','temp_troca','data_termino_revisao','valor_aquisicao')
		);
		
		$row->joinLeft(
			array('u'=>'usuarios'),
			'n.id_usuario = u.id',
			array('nome')
		);
		
		$row->joinLeft(
			array('ve'=>'vendedores'),
			've.id_usuario = u.id',
			array('valor_fixo','percentual_venda')
		);
		
		$row->where('n.id = ' .$id);
		
		//echo $row->__toString();
		
		$row->limit(1);
		
		return $row->query()->fetchAll();
		
	}
	
	public function getNegociacoesCliente($idCliente){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('n'=>'negociacoes'),
			array('*')
		);
		
		$row->joinLeft(
			array('v'=>'veiculos'),
			'v.id_negociacao = n.id',
			array('id_veiculo_troca'=>'v.id','temp_troca','data_termino_revisao','valor_aquisicao')
		);
		
		$row->joinLeft(
			array('u'=>'usuarios'),
			'n.id_usuario = u.id',
			array('nome')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('ano_modelo','modelo','marca','preco')
		);
		
		$row->where('n.id_cliente = ' .$idCliente);
		
		//echo $row->__toString();
		
		//$row->limit(1);
		
		return $row->query()->fetchAll();
		
	}
	
	
	public function getNegociacoesClienteCompra($idCliente){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('n'=>'negociacoes'),
			array('*')
		);
		
		$row->joinLeft(
			array('v'=>'veiculos'),
			'v.id_negociacao_compra = n.id',
			array('id_veiculo_troca'=>'v.id','temp_troca','data_termino_revisao','valor_aquisicao')
		);
		
		$row->joinLeft(
			array('u'=>'usuarios'),
			'n.id_usuario = u.id',
			array('nome')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('ano_modelo','modelo','marca','preco')
		);
		
		$row->where('n.id_cliente = ' .$idCliente);
		
		//echo $row->__toString();
		
		//$row->limit(1);
		
		return $row->query()->fetchAll();
		
	}
	
	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('n'=>'negociacoes'),
			array('*')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'n.id_veiculo = v.id',
			array('placa','km','valor_aquisicao','data_aquisicao','combustivel','origem','cor','data_termino_revisao','descricao_site','id_negociacao_compra','ano_fabricacao')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('ano_modelo','modelo','marca','preco')
		);
		
		$row->joinInner(
			array('c'=>'clientes'),
			'n.id_cliente = c.id',
			array('nome')
		);
		
		$row->joinInner(
			array('u'=>'usuarios'),
			'n.id_vendedor = u.id',
			array('id_perfil', 'nomeUsuario'=>'nome')
		);
		
		$row->joinLeft(
			array('fd'=>'financeiras_despachantes'),
			'n.id_despachante = fd.id',
			array('imposto')
		);
		
		$row->joinLeft(
			array('fd2'=>'financeiras_despachantes'),
			'n.id_financeira = fd2.id',
			array('nome_financeira'=>'fd2.nome')
		);
		
		if(isset($arr['agenda']) && $arr['agenda'] == true){
		
			$row->joinLeft(
				array('v3'=>'veiculos'),
				'v3.id_negociacao_troca = n.id',
				array('placa_troca_1'=>'placa','valor_compra_troca_1'=>'valor_aquisicao','descricao_site_troca_1'=>'descricao_site')
			);
			
			$row->joinLeft(
				array('m3'=>'modelos_11'),
				'v3.id_modelo = m3.id',
				array('modelo_troca_1'=>'modelo','ano_modelo_troca_1'=>'ano_modelo')
			);
			
		}
		
		if(isset($arr['buscaTroca']) && $arr['buscaTroca'] == true){
			
			$row->joinLeft(
				array('v2'=>'veiculos'),
				'v2.id_negociacao = n.id',
				array('id_troca'=>'id')
			);
		
		}
	
		if(isset($arr['id'])){
		
			$row->where('n.id = ' . $arr['id']);
		
		}
		
		if(isset($arr['id_empresa'])){
		
			$row->where('n.id_empresa = ' . $arr['id_empresa']);
		
		}
		
		if(isset($arr['id_negociacao_compra']) && $arr['id_negociacao_compra'] == 2){
		
			$row->where('n.compra = 0');
		
		}
		
		if(isset($arr['id_vendedor'])){
				
			$row->where("n.id_vendedor = " . $arr['id_vendedor']);
		
		}
		
		if(isset($arr['id_financeira'])){
	
			$row->where("n.id_financeira = " .$arr['id_financeira']);
		
		}
		
		if(isset($arr['str_modelo'])) {
	
			$row->where("m.modelo LIKE '".$arr['str_modelo']."%'");
		
		}
		
		if(isset($arr['id_perfil'])) {
			
			if($arr['id_perfil'] == 2){
			
				//$row->where('u.id_perfil = 2 OR u.id_perfil = 4 OR u.id_perfil = 3');
			
			}elseif($arr['id_perfil'] == 4){
			
				$row->where('u.id_perfil = 4 OR u.id_perfil = 3');
			
			}elseif($arr['id_perfil'] == 3){
			
				$row->where('u.id_perfil = 3');
			
			}
			
		}
		
		if(isset($arr['aprovada']) && $arr['aprovada'] != ""){
		
			$row->where('n.aprovada = ' . $arr['aprovada']);
		
		}
		
		/*if($arr['data_inicial'] || $arr['data_final']){
			
			$row->where("n.data_abertura BETWEEN '".$arr['data_inicial']." 00:00:00' AND '".$arr['data_final']."23:59:59'");
			
		}*/
		
		if(isset($arr['data_inicial_concretizacao']) && $arr['data_inicial_concretizacao'] != "") {
			
			$row->where("n.data_concretizacao >= '".$arr['data_inicial_concretizacao']." 00:00:00'");
			
		}
		
		if(isset($arr['data_final_concretizacao']) && $arr['data_final_concretizacao'] != "") {
			
			$row->where("n.data_concretizacao <='".$arr['data_final_concretizacao']." 23:59:59'");
			
		}
		
		if(isset($arr['data_inicial_abertura']) && $arr['data_inicial_abertura'] != "") {
			
			$row->where("n.data_abertura >= '".$arr['data_inicial_abertura']." 00:00:00'");
			
		}
		
		if(isset($arr['data_final_abertura']) && $arr['data_final_abertura'] != "") {
			
			$row->where("n.data_abertura <='".$arr['data_final_abertura']." 23:59:59'");
			
		}
		
		
		if(isset($arr['parcial']) && $arr['parcial'] == true){

			if(isset($arr['placa'])){

				$row->where("v.placa LIKE '" . $arr['placa'] . "%' OR v.renavam LIKE '".$arr['placa']."%'");

			}

			if(isset($arr['nome'])){

				$row->where("c.nome LIKE '".$arr['nome']."%' OR REPLACE(REPLACE(REPLACE(c.cpf, '.', ''), '-', ''), '/', '') LIKE '".$arr['nome']."%'");

			}
			
		}else{
			
			if(isset($arr['placa'])) {
				
				$row->where("v.placa = '" . $arr['placa'] . "' OR v.renavam = '".$arr['placa']."'");
			
			}
			
			if(isset($arr['nome'])){
			
				$row->where("c.nome = '".$arr['nome']."' OR c.cpf = '".$arr['nome']."' ");
			
			}
			
		}
		
		if(isset($arr['relatorio_fi']) && $arr['relatorio_fi']) {
		
			$row->order('n.id_financeira');
		
		}elseif(isset($arr['order_usuarios']) && $arr['order_usuarios']){
		
			$row->order('n.id_vendedor DESC');
			
		}elseif(isset($arr['lista']) && $arr['lista']){
		
			$row->order('n.data_abertura DESC');
		
		}else{
	
			$row->order('n.data_abertura');
			$row->order('n.data_concretizacao');
		
		}
		
		//echo $row->__toString();//exit;

		return $row->query()->fetchAll();
		
	}
	
	public function _getCompra($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('n'=>'negociacoes'),
			array('*')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'v.id_negociacao_compra = n.id',
			array('placa','km','valor_aquisicao','data_aquisicao','combustivel','finalidade'=>'origem','cor','data_termino_revisao','descricao_site','id_negociacao_compra')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('ano_modelo','modelo','marca','preco')
		);
		
		$row->joinInner(
			array('c'=>'clientes'),
			'n.id_cliente = c.id',
			array('nome', 'tel1', 'tel2', 'cargo', 'bairro', 'cidade')
		);
		
		$row->joinInner(
			array('u'=>'usuarios'),
			'n.id_vendedor = u.id',
			array('id_perfil', 'nomeUsuario'=>'nome')
		);
		
		$row->joinLeft(
			array('fd'=>'financeiras_despachantes'),
			'n.id_despachante = fd.id',
			array('imposto')
		);
		
		$row->joinLeft(
			array('fd2'=>'financeiras_despachantes'),
			'n.id_financeira = fd2.id',
			array('nome_financeira'=>'fd2.nome')
		);

		$row->joinLeft(
			array('oc'=>'origem_clientes'),
			'c.origem = oc.id',
			array('origem'=>'oc.descricao')
		);
		
		if(isset($arr['buscaTroca']) && $arr['buscaTroca'] == true){
			
			$row->joinLeft(
				array('v2'=>'veiculos'),
				'v2.id_negociacao = n.id',
				array('id_troca'=>'id')
			);
		
		}
	
		if(isset($arr['id']) && $arr['id'] != ""){
			$row->where('n.id = ' . $arr['id']);
		}
		
		if(isset($arr['id_empresa']) && $arr['id_empresa'] != ""){
			$row->where('n.id_empresa = ' . $arr['id_empresa']);
		}

		if(isset($arr['id_negociacao_compra']) && $arr['id_negociacao_compra'] == 1){
			$row->where('n.compra = 1');
		}
		
		if(isset($arr['id_vendedor']) && $arr['id_vendedor'] != ""){
			$row->where("n.id_vendedor = " . $arr['id_vendedor']);
		}

		if(isset($arr['id_financeira']) && $arr['id_financeira'] != ""){
			$row->where("n.id_financeira = " .$arr['id_financeira']);
		}
		
		if(isset($arr['id_perfil'])){
			
			if($arr['id_perfil'] == 2){
			
				//$row->where('u.id_perfil = 2 OR u.id_perfil = 4 OR u.id_perfil = 3');
			
			}elseif($arr['id_perfil'] == 4){
			
				$row->where('u.id_perfil = 4 OR u.id_perfil = 3');
			
			}elseif($arr['id_perfil'] == 3){
			
				$row->where('u.id_perfil = 3');
			
			}
			
		}
		
		if(isset($arr['aprovada']) && $arr['aprovada'] != ""){
		
			$row->where('n.aprovada = ' . $arr['aprovada']);
		
		}
		
		/*if($arr['data_inicial'] || $arr['data_final']){
			
			$row->where("n.data_abertura BETWEEN '".$arr['data_inicial']." 00:00:00' AND '".$arr['data_final']."23:59:59'");
			
		}*/
		
		if(isset($arr['data_inicial_concretizacao']) && $arr['data_inicial_concretizacao'] != ""){
			$row->where("n.data_concretizacao >= '".$arr['data_inicial_concretizacao']." 00:00:00'");
			
		}

		if(isset($arr['data_final_concretizacao']) && $arr['data_final_concretizacao'] != ""){
			$row->where("n.data_concretizacao <='".$arr['data_final_concretizacao']." 23:59:59'");
		}
		
		if(isset($arr['data_inicial_abertura']) && $arr['data_inicial_abertura'] != ""){
			$row->where("n.data_abertura >= '".$arr['data_inicial_abertura']." 00:00:00'");	
		}

		if(isset($arr['data_final_abertura']) && $arr['data_final_abertura'] != ""){
			$row->where("n.data_abertura <='".$arr['data_final_abertura']." 23:59:59'");
		}
		
		
		if(isset($arr['parcial']) && $arr['parcial'] == true){

			if(isset($arr['placa'])){

				$row->where("v.placa LIKE '" . $arr['placa'] . "%' OR v.renavam LIKE '".$arr['placa']."%'");

			}

			if(isset($arr['nome'])){

				$row->where("c.nome LIKE '".$arr['nome']."%' OR REPLACE(REPLACE(REPLACE(c.cpf, '.', ''), '-', ''), '/', '') LIKE '".$arr['nome']."%'");

			}

		}else{

			if(isset($arr['placa'])){

				$row->where("v.placa = '" . $arr['placa'] . "' OR v.renavam = '".$arr['placa']."'");

			}

			if(isset($arr['nome'])){

				$row->where("c.nome = '".$arr['nome']."' OR REPLACE(REPLACE(REPLACE(c.cpf, '.', ''), '-', ''), '/', '') = '".$arr['nome']."' ");
			
			}
			
		}
		
		if(isset($arr['relatorio_fi']) && $arr['relatorio_fi'] == 1){
			$row->order('n.id_financeira');
		}elseif(isset($arr['order_usuarios']) && $arr['order_usuarios'] == true){
			$row->order('n.id_vendedor DESC');
		}elseif(isset($arr['lista']) && $arr['lista'] == true){
			$row->order('n.data_abertura DESC');
		}else{
			$row->order('n.data_abertura');
			$row->order('n.data_concretizacao');
		}
		
		//echo $row->__toString();//exit;

		return $row->query()->fetchAll();
		
	}
	
	public function _getTroca($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('n'=>'negociacoes'),
			array('*')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'n.id = v.id_negociacao_troca',
			array('placa','km','valor_aquisicao','data_aquisicao','combustivel','origem','cor','data_termino_revisao','descricao_site')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('ano_modelo','modelo','marca','preco')
		);
		
		$row->joinInner(
			array('c'=>'clientes'),
			'n.id_cliente = c.id',
			array('nome')
		);
		
		$row->joinInner(
			array('u'=>'usuarios'),
			'n.id_vendedor = u.id',
			array('id_perfil', 'nomeUsuario'=>'nome')
		);
		
		$row->joinLeft(
			array('fd'=>'financeiras_despachantes'),
			'n.id_despachante = fd.id',
			array('imposto')
		);
		
		$row->joinLeft(
			array('fd2'=>'financeiras_despachantes'),
			'n.id_financeira = fd2.id',
			array('nome_financeira'=>'fd2.nome')
		);
		
		if(isset($arr['buscaTroca']) && $arr['buscaTroca'] == true){
			
			$row->joinLeft(
				array('v2'=>'veiculos'),
				'v2.id_negociacao = n.id',
				array('id_troca'=>'id')
			);
		
		}
	
		if(isset($arr['id'])){
		
			$row->where('n.id = ' . $arr['id']);
		
		}
		
		if(isset($arr['id_empresa'])){
		
			$row->where('n.id_empresa = ' . $arr['id_empresa']);
		
		}
		
		if(isset($arr['id_vendedor']) && $arr['id_vendedor'] != ""){
				
			$row->where("n.id_vendedor = " . $arr['id_vendedor']);
		
		}
		
		if(isset($arr['id_financeira']) && $arr['id_financeira'] != ""){
	
			$row->where("n.id_financeira = " .$arr['id_financeira']);
		
		}
		
		if(isset($arr['id_perfil'])){
			
			if($arr['id_perfil'] == 2){
			
				//$row->where('u.id_perfil = 2 OR u.id_perfil = 4 OR u.id_perfil = 3');
			
			}elseif($arr['id_perfil'] == 4){
			
				$row->where('u.id_perfil = 4 OR u.id_perfil = 3');
			
			}elseif($arr['id_perfil'] == 3){
			
				$row->where('u.id_perfil = 3');
			
			}
			
		}
		
		if(isset($arr['aprovada']) && $arr['aprovada'] != ""){
		
			$row->where('n.aprovada = ' . $arr['aprovada']);
		
		}
		
		/*if($arr['data_inicial'] || $arr['data_final']){
			
			$row->where("n.data_abertura BETWEEN '".$arr['data_inicial']." 00:00:00' AND '".$arr['data_final']."23:59:59'");
			
		}*/
		
		if(isset($arr['data_inicial_concretizacao']) && $arr['data_inicial_concretizacao'] != ""){
			
			$row->where("n.data_concretizacao >= '".$arr['data_inicial_concretizacao']." 00:00:00'");
			
		}

		if(isset($arr['data_final_concretizacao']) && $arr['data_final_concretizacao'] != ""){
			
			$row->where("n.data_concretizacao <='".$arr['data_final_concretizacao']." 23:59:59'");
			
		}
		
		if(isset($arr['data_inicial_abertura']) && $arr['data_inicial_abertura'] != ""){
			
			$row->where("n.data_abertura >= '".$arr['data_inicial_abertura']." 00:00:00'");
			
		}
		
		if(isset($arr['data_final_abertura']) && $arr['data_final_abertura'] != ""){
			
			$row->where("n.data_abertura <='".$arr['data_final_abertura']." 23:59:59'");
			
		}
		
		
		if(isset($arr['parcial']) && $arr['parcial'] == true){
			
			if(isset($arr['placa']) && $arr['placa'] != ""){
				
				$row->where("v.placa LIKE '" . $arr['placa'] . "%' OR v.renavam LIKE '".$arr['placa']."%'");
			
			}
			
			if(isset($arr['nome']) && $arr['nome'] != ""){

				$row->where("c.nome LIKE '".$arr['nome']."%' OR REPLACE(REPLACE(REPLACE(c.cpf, '.', ''), '-', ''), '/', '') LIKE '".$arr['nome']."%'");

			}

		}else{

			if(isset($arr['placa']) && $arr['placa'] != ""){

				$row->where("v.placa = '" . $arr['placa'] . "' OR v.renavam = '".$arr['placa']."'");

			}

			if(isset($arr['nome']) && $arr['nome'] != ""){

				$row->where("c.nome = '".$arr['nome']."' OR REPLACE(REPLACE(REPLACE(c.cpf, '.', ''), '-', ''), '/', '') = '".$arr['nome']."' ");
			
			}
			
		}
		
		if(isset($arr['relatorio_fi'])){
		
			$row->order('n.id_financeira');
		
		}elseif(isset($arr['order_usuarios'])){
		
			$row->order('n.id_vendedor DESC');
			
		}elseif(isset($arr['lista'])){
		
			$row->order('n.data_abertura DESC');
		
		}else{
	
			$row->order('n.data_abertura');
			$row->order('n.data_concretizacao');
		
		}
		
		//echo $row->__toString();//exit;

		return $row->query()->fetchAll();
		
	}
	
	public function getLastId(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('n'=>'negociacoes'),
		array('id')
		);
		  
		$row->order('id DESC');
		$row->limit(1);
		return $row->query()->fetchAll();
		
	}

	/**
	 * Retorna soma de valor_venda agrupada por vendedor e status (aprovada)
	 * Aceita filtros similares a _get(): id_empresa, data_inicial_abertura, data_final_abertura, parcial/nome/placa, id_negociacao_compra
	 */
	public function getResumoVendedores($arr = array()){

		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('n'=>'negociacoes'),
			array('id_vendedor'=>'n.id_vendedor','aprovada'=>'n.aprovada','qtd'=>'COUNT(n.id)','soma'=>'SUM(n.valor_venda)')
		);

		// juntar com usuarios (LEFT) para incluir negociações sem vendedor preenchido
		$row->joinLeft(
			array('u'=>'usuarios'),
			'n.id_vendedor = u.id',
			array('nomeUsuario'=>'u.nome')
		);

		// joins úteis para filtros (placa/nome quando parcial)
		$row->joinInner(
			array('v'=>'veiculos'),
			'n.id_veiculo = v.id',
			array()
		);

		$row->joinInner(
			array('c'=>'clientes'),
			'n.id_cliente = c.id',
			array()
		);

		if(isset($arr['id_empresa'])){
			$row->where('n.id_empresa = ' . $arr['id_empresa']);
		}

		if(isset($arr['id_negociacao_compra']) && $arr['id_negociacao_compra'] == 2){
			// lista principal de vendas (compra = 0)
			$row->where('n.compra = 0');
		}

		if(isset($arr['data_inicial_abertura']) && $arr['data_inicial_abertura'] != ""){
			$row->where("n.data_abertura >= '".$arr['data_inicial_abertura']." 00:00:00'");
		}

		if(isset($arr['data_final_abertura']) && $arr['data_final_abertura'] != ""){
			$row->where("n.data_abertura <= '".$arr['data_final_abertura']." 23:59:59'");
		}

		if(isset($arr['parcial']) && $arr['parcial'] == true){

			if(isset($arr['placa']) && $arr['placa'] != ""){
				$row->where("v.placa LIKE '" . $arr['placa'] . "%' OR v.renavam LIKE '".$arr['placa']."%'");
			}

			if(isset($arr['nome']) && $arr['nome'] != ""){
				$row->where("c.nome LIKE '".$arr['nome']."%' OR REPLACE(REPLACE(REPLACE(c.cpf, '.', ''), '-', ''), '/', '') LIKE '".$arr['nome']."%'");
			}

		}else{

			if(isset($arr['placa']) && $arr['placa'] != ""){
				$row->where("v.placa = '" . $arr['placa'] . "' OR v.renavam = '".$arr['placa']."'");
			}

			if(isset($arr['nome']) && $arr['nome'] != ""){
				$row->where("c.nome = '".$arr['nome']."' OR REPLACE(REPLACE(REPLACE(c.cpf, '.', ''), '-', ''), '/', '') = '".$arr['nome']."' ");
			}

		}

		$row->group(array('n.id_vendedor','n.aprovada'));
		$row->order('u.nome');

		// retorna linhas com id_vendedor, aprovada (-1,0,1), soma
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
			'dbname'   => DB,
			'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode=''")
		));

		return $db;

	}
	
	
	
	public function vendasRealizadas($arr = array()){
	
	
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('neg'=>'negociacoes'),
			array('id_usuario', 'data_concretizacao')//, 'soma'=>'SUM(fl.qtd)')
		);
		//$row->group(id_usuario);
		
		/* $row->joinInner(
			array(''=>'origem_clientes'),
			'fl.id_origem_cliente = oc.id',
			array('descricao')
		); */
		
		
		if($arr['id_usuario']){
			
			$row->joinInner(
				array('u'=>'usuarios'),
				'neg.id_usuario = u.id',
				array('id_perfil', 'nomeUsuario'=>'nome')
			);
			
			$row->where('neg.id_usuario = ' . $arr['id_usuario']);
			
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
		
		$row->where("neg.data_concretizacao = " . "'" . @date('Y-m-d') . "'");
		
		$row->order('neg.data_concretizacao');
		
		//echo $row->__toString();//exit;
		
		return $row->query()->fetchAll();
	
	}

	
	public function vendasRealizadasMes($arr = array()){
		
		$dataInicial = @date('Y-m-01');
		$dataFinal = @date('Y-m-d');
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('neg'=>'negociacoes'),
			array('id_usuario', 'data_concretizacao')
		);
		
		if($arr['id_perfil'] != 2 && $arr['id_perfil'] != 4){
			
			$row->joinInner(
				array('u'=>'usuarios'),
				'neg.id_usuario = u.id',
				array('id_perfil', 'nomeUsuario'=>'nome')
			);
			
			$row->where('neg.id_usuario = ' . $arr['id_usuario']);
			
		}else{
		
			$row->joinInner(
				array('u'=>'usuarios'),
				'neg.id_usuario = u.id',
				array('id_perfil', 'nomeUsuario'=>'nome')
			);
		
		}
		
		$row->where('neg.id_empresa = ' . $arr['id_empresa']);
		
		$row->where("neg.data_concretizacao >= '".$dataInicial." 00:00:00'");
		$row->where("neg.data_concretizacao <= '".$dataFinal." 23:59:59'");
		
		$row->order('neg.data_concretizacao');
		
		//echo $row->__toString();exit;
		
		return $row->query()->fetchAll();
	
	}
	
}
