<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Veiculos extends Zend_Db_Table_Abstract
{

	protected $_name = 'veiculos';

	public function getVeiculoAntigos(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('id','data_aquisicao')
		);

		$row->joinInner(
			array('fv'=>'fotos_veiculos'),
			'v.id = fv.id_veiculo',
			array('id_foto'=>'id','path')
		);
		
		//$row->where("v.id_empresa = 3");
		$row->where("v.data_aquisicao <= '".(@date("Y")-3)."-".@date("m-d")."'");
		$row->where("v.vendido = 1 OR v.excluido = 1");
		$row->order('v.id DESC');
		$row->limit(100);

		//echo $row->__toString();

		return $row->query()->fetchAll();
		
	}
	
	public function getVeiculoEstoqueApp($idVeiculo, $idEmpresa){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('*')
		);
		
		$row->where("v.id = ".$idVeiculo);
		$row->where("v.id_empresa = ".$idEmpresa);
		$row->order('v.id DESC');
		$row->limit(1);

		return $row->query()->fetchAll();
		
	}
	
	
	public function getVeiculoPorPlacaEmpresa($placa, $idEmpresa){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('*')
		);
	
		$row->where("v.placa = '".$placa."'");
		
		$row->where("v.id_empresa = ".$idEmpresa);
		
		$row->where("v.ativo = 1");
		$row->where("v.vendido = 0");
		$row->where("v.excluido = 0");

		$row->order('v.id DESC');
		$row->limit(1);
		
		return $row->query()->fetchAll();
		
	}
	
	
	public function getVeiculoPorPlaca($placa){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo','marca','ano_modelo','preco','cod_fipe')
		);
		
		$row->where("v.placa = '".$placa."'");

		$row->order('v.id DESC');
		
		return $row->query()->fetchAll();
		
	}
	
	public function getVeiculosPreparacao($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo','marca','ano_modelo','preco','cod_fipe')
		);
		
		$row->where("v.id_empresa = ".$arr['id_empresa']);
		$row->where("v.ativo = 1");
		$row->where("v.vendido = 0");
		$row->where("v.excluido = 0");

		
		/////////////UNION//////////////////////////////////
		$row2 = $this->select();
		$row2->setIntegrityCheck(false);
		$row2->from(
			array('v'=>'veiculos'),
			array('*')
		);
		
		$row2->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo','marca','ano_modelo','preco','cod_fipe')
		);
		
		$row2->joinLeft(
			array('n'=>'negociacoes'),
			'v.id = n.id_veiculo',
			array()
		);
		
		$row2->where("n.data_concretizacao >= '".@date('Y-m-d', mktime(0, 0, 0, @date('m')-1, @date('d'), @date('Y')))."' OR n.data_concretizacao = '0000-00-00 00:00:00'");
		
		$row2->where("n.data_abertura >= '".@date('Y-m-d', mktime(0, 0, 0, @date('m')-1, @date('d'), @date('Y')))."'");
		
		$row2->where("v.id_empresa = ".$arr['id_empresa']);
		$row2->where("v.ativo = 1");
		$row2->where("v.vendido = 1");
		$row2->where("v.excluido = 0");

		//$select = $this->select()->union(array($row, $row2))->order('marca ASC')->order('id DESC');
		$select = $this->select()->union(array($row, $row2))->order('id DESC');
		
		
		//echo $select->__toString();

		return $select->query()->fetchAll();
		
	}
	
	
	public function getVeiculoEstoquePreparacao($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo','marca','ano_modelo','cod_fipe','segmento')
		);
		
		$row->where("v.id_empresa = ".$arr['id_empresa']);
		$row->where("v.ativo = 1");
		$row->where("v.vendido = 0");
		$row->where("v.excluido = 0");
		
		if(isset($arr['servico']) && $arr['servico'] != ""){
			
			$row->where("(v.".$arr['servico']." = 0 OR v.".$arr['servico']." = 3)");
			
		}
		
		if(isset($arr['id_veiculo'])){
			
			$row->where("v.id = ".$arr['id_veiculo']);
			
		}
		
		$row->order('m.marca ASC');
		$row->order('v.id DESC');

		//echo $row->__toString();
		
		return $row->query()->fetchAll();

	}
	
	public function getVeiculoEstoque($idVeiculo){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo','marca','ano_modelo','cod_fipe','segmento')
		);
		
		$row->where("v.id = ".$idVeiculo);

		return $row->query()->fetchAll();
		
	}
	
	
	public function getRevisoes($idVeiculo){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('dv'=>'despesas_veiculos'),
		array('*')
		);
		
		
		$row->joinInner(
			array('f'=>'fornecedores'),
			'f.id = dv.id_fornecedor',
			array('razao_social')
		);
		
		
		$row->where("dv.id_veiculo = ".$idVeiculo);

		return $row->query()->fetchAll();
		
	}
	
	
	public function getVeiculoEstoqueFluxo($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('id')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array()
		);

		$row->where("m.modelo LIKE '".$arr['modelo']." %'");
		
		if($arr['valor_venda'] != 0){
		
			$row->where("v.valor_venda <= '".($arr['valor_venda']+3000)."'");
			
			$row->where("v.valor_venda >= '".($arr['valor_venda']-3000)."'");
		
		}

		$row->where("v.id_empresa = ".$arr['id_empresa']);
		$row->where("v.ativo = 1");
		$row->where("v.vendido = 0");
		$row->where("v.excluido = 0");
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	
	public function getPlacaEmpresa($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('*')
		);
		
		$row->where("v.placa = '".$arr['placa']."'");
		$row->where("v.id_empresa = ".$arr['id_empresa']);

		$row->order("v.id DESC");
		
		return $row->query()->fetchAll();
		
	}

	
	public function _getVeiculosParecidos($strModelo){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo' => 'upper(modelo)','ano_modelo' => 'upper(ano_modelo)','marca' => 'upper(marca)')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = v.id_empresa',
			array('cidade','nome_fantasia','logo_empresa'=>'path','tel1','tel2','endereco','bairro','estado','sistema_site','razao_social')
		);

		if($arr['sistema_site'] == 1){
		
			$row->joinLeft(
				array('u'=>'usuarios'),
				'e.id = u.id_empresa',
				array('telefone','celular','id_perfil','cidade_usuario'=>'cidade','bairro_usuario'=>'bairro','estado_usuario'=>'estado')
			);
		
		}
		
		$row->where("m.modelo LIKE '".$strModelo." %'");
		
		$row->where("v.vendido = 0");
		$row->where("v.ativo = 1");
		$row->where("e.ativo = 1");
		$row->where("v.excluido = 0");
		$row->where("(v.exibir_site_estoque = 2 OR v.exibir_site_estoque = 3)");
		
		$row->order('m.ano_modelo DESC');

		return $row->query()->fetchAll();
	
	}
	
	public function getVeiculosIcarros2(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('*')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'v.id_empresa = e.id',
			array('login_icarros','senha_icarros')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('ano_modelo')
		);
		
		$row->where("v.icarros = 2");
		
		$row->order("v.id_empresa");
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	
	
	public function getSomaDespesas($idEmpresa){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('id')
		);
		  
		$row->joinInner(
			array('dv'=>'despesas_veiculos'),
			'v.id = dv.id_veiculo',
			array('soma_despesas'=>'SUM(valor)')
		);
		
		$row->where("v.consignado = 0");
		
		$row->where("v.id_empresa = ".$idEmpresa);
		
		$row->where("v.vendido = 0");
		
		$row->where("v.excluido = 0");
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
		
	}
	
	public function getSomaVeiculos($idEmpresa){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('soma_veiculos'=>'SUM(valor_aquisicao)')
		);
		
		$row->where("v.consignado = 0");
		
		$row->where("v.id_empresa = ".$idEmpresa);
		
		$row->where("v.vendido = 0");
		
		$row->where("v.excluido = 0");
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
		
	}
	
	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo','marca','ano_modelo','fipe'=>'preco', 'cod_fipe', 'segmento')
		);
		
		$row->joinLeft(
			array('n'=>'negociacoes'),
			'n.id = v.id_negociacao',
			array('data_concretizacao')
		);
		
		if(isset($arr['id'])){
		
			$row->where('v.id = ' . $arr['id']);
		
		}
		
		if(isset($arr['id_empresa'])){
		
			$row->where('v.id_empresa = ' . $arr['id_empresa']);
		
		}
		
		
		//Esta parte alterei pois estava dando erro 19/12/2012.	
		
		if(isset($arr['vend']) && $arr['vend'] == 1){
		
			$row->where('v.vendido = 0');
		
		}
		///////////////////////////////////////
		
		
		if(isset($arr['vendidos']) && $arr['vendidos'] == 1){
		
			$row->where('v.vendido = 1');
		
		}
		
		if(isset($arr['temp_troca']) && $arr['temp_troca']){
		
			$row->where('v.temp_troca = 1');
		
		}
		
		if(isset($arr['id_negociacao'])) {
		
			$row->where('v.id_negociacao = '.$arr['id_negociacao']);
		
		}
		
		if(isset($arr['id_negociacao_troca'])) {
		
			$row->where('v.id_negociacao_troca = '.$arr['id_negociacao_troca']);
		
		}
		
		if(isset($arr['id_negociacao_troca2'])) {
		
			$row->where('v.id_negociacao_troca2 = '.$arr['id_negociacao_troca2']);
		
		}
		
		if(isset($arr['origem'])) {

			$row->where("v.origem = '".$arr['origem']."'");

		}
		
		if(isset($arr['exibir_estoque']) && $arr['exibir_estoque']){
		
			$row->where("v.exibir_site_estoque = 1 OR v.exibir_site_estoque = 3");
		
		}
		
		if(isset($arr['sql'])) {
		
			$row->where($arr['sql']);
		
		}
		
		
		if(isset($arr['parcial'])){
		
			if(isset($arr['placa'])){
		
				$row->where("v.placa LIKE '".$arr['placa']."%' OR m.modelo LIKE '".$arr['modelo']."%'");
		
			}
		
		}else{
		
			if(isset($arr['placa'])) {
		
				$row->where("v.placa = '".$arr['placa']."'");
		
			}
			
			if(isset($arr['modelo'])) {
		
				$row->where("m.modelo = '".$arr['modelo']."'");
		
			}			
		
		}
		
		$row->where("v.excluido = 0");
		
		if(isset($arr['order'])){
		
			$row->order("m.".$arr['order']);
			$row->order("m.modelo");
		
		}elseif(isset($arr['ordem_data'])){
		
			$row->order("v.id DESC");
		
		}else{
	
			$row->order('m.modelo');
		
		}
		
		if(isset($arr['limit'])) {
		
			$row->limit($arr['limit']);
	
		}
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function edt($id, $dados){
		
		try{

			return $this->update($dados, 'id = ' . $id);

		}catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	} 
	
	public function getVeiculosCompleto($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo','marca','ano_modelo','preco','cod_fipe', 'segmento')
		);
		
		$row->joinInner(
			array('u'=>'usuarios'),
			'u.id = v.id_usuario_alteracao',
			array('nome')
		);
		
		if(isset($arr['id']) && $arr['id'] != ""){
		
			$row->where('v.id = ' . $arr['id']);
		
		}
		
		if(isset($arr['id_empresa']) && $arr['id_empresa'] != ""){
		
			$row->where('v.id_empresa = ' . $arr['id_empresa']);
		
		}
		
		if(isset($arr['vendido']) && $arr['vendido'] != ""){
		
			$row->where('v.vendido = ' . $arr['vendido']);
		
		}
		
		if(isset($arr['venda']) && $arr['venda'] == true){
		
			$row->where('v.vendido = 0');
		
		}
		
		if(isset($arr['ativo'])){
		
			$row->where('v.ativo = 0');
		
		}
		
		if(isset($arr['parcial']) && $arr['parcial'] == true){
		
			if(isset($arr['placa'])){
				$row->where("v.placa LIKE '".$arr['placa']."%'");
			}
			
			if(isset($arr['modelo'])){
				$row->where("m.modelo LIKE '".$arr['modelo']."%'");
			}
			
			if(isset($arr['ano_modelo'])){
				$row->where("m.ano_modelo LIKE '".$arr['ano_modelo']."%'");
			}
		
		}else{
		
			if(isset($arr['placa'])){
		
				$row->where("v.placa = '".$arr['placa']."'");
		
			}
			
			if(isset($arr['modelo'])){
		
				$row->where("m.modelo = '".$arr['modelo']."'");
		
			}

			if(isset($arr['ano_modelo'])){
		
				$row->where("m.ano_modelo = '".$arr['ano_modelo']."'");
		
			}
		
		}
		
		$row->where("v.excluido = 0");
	
		if(isset($arr['order_valor_venda']) && $arr['order_valor_venda'] == true){
		
			$row->order('v.valor_venda ASC');
		
		}else{
	
			$row->order('m.marca ASC');
			$row->order('v.id DESC');
		
		}
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}


	public function getVeiculosPorEmpresaMes($arr){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo')
		);

		if($arr['id_empresa']){
			$row->where("v.id_empresa = ".$arr['id_empresa']);
		}

		if($arr['data_inicial']){
			$row->where("v.outros_date_concluido >= '".$arr['data_inicial']."'");
		}
		
		if($arr['data_final']){
			$row->where("v.outros_date_concluido <='".$arr['data_final']."'");
		}
		
		
		$row->order('m.modelo ASC');
		
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
	
	}
	
	public function getVeiculosPorEmpresa($idEmpresa){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo')
		);
		
		$row->where("v.id_empresa = ".$idEmpresa);
		
		$row->order('m.modelo ASC');
		
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
	
	}
	
	
	public function getVeiculosPorEmpresa2($idEmpresa){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo')
		);
		
		$row->where("v.ativo = 1");
		
		$row->where("v.vendido = 0");
		
		$row->where("v.excluido = 0");
		
		$row->where("v.id_empresa = ".$idEmpresa);
		
		$row->order('m.modelo ASC');
		
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
	
	}
	
	public function getVeiculosMarca(){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array()
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('distinct(marca)')
		);
		
		$row->order('m.marca ASC');
		
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
	
	}
	
	public function getQtdVeiculos($idModelo){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('qtd_veiculos'=>'count(*)')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = v.id_empresa',
			array()
		);
		
		$row->where("e.ativo = 1");
		
		$row->where("v.ativo = 1");
		
		$row->where("v.vendido = 0");
		
		$row->where("v.excluido = 0");
		
		$row->where("v.exibir_site_estoque = 2 OR v.exibir_site_estoque = 3");
		
		$row->where("v.id_modelo = ".$idModelo);
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function getVeiculosMenorValor($idModelo){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('valor_minimo'=>'MIN(valor_venda)')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = v.id_empresa',
			array()
		);
		
		$row->where("e.ativo = 1");
	
		$row->where("v.ativo = 1");
		
		$row->where("v.vendido = 0");
		
		$row->where("v.excluido = 0");
		
		$row->where("v.exibir_site_estoque = 2 OR v.exibir_site_estoque = 3");
		
		$row->where("v.id_modelo = ".$idModelo);
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function getVeiculosMaiorValor($idModelo){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('valor_maximo'=>'MAX(valor_venda)')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = v.id_empresa',
			array()
		);
		
		$row->where("e.ativo = 1");
	
		$row->where("v.ativo = 1");
		
		$row->where("v.vendido = 0");
		
		$row->where("v.excluido = 0");
		
		$row->where("v.exibir_site_estoque = 2 OR v.exibir_site_estoque = 3");
		
		$row->where("v.id_modelo = ".$idModelo);
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	
	public function getVeiculosUsadosIndex(){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('id','descricao_site','combustivel','ano_fabricacao','valor_venda','exibir_valor_site')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('marca','modelo','ano_modelo','segmento')
		);
		
		$row->joinInner(
			array('fv'=>'fotos_veiculos'),
			'v.id = fv.id_veiculo',
			array('path')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = v.id_empresa',
			array('cidade')
		);
		
		$row->where("e.ativo = 1");

		$row->where("fv.capa = 1");
		
		$row->where("v.ativo = 1");
		
		$row->where("v.novo_usado = 1");
		
		$row->where("v.vendido = 0");
		
		$row->where("v.excluido = 0");
		
		$row->where("v.exibir_site_estoque = 2 OR v.exibir_site_estoque = 3");
		
		$row->order('RAND()');
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
	
	}
	
	public function getVeiculosUsadosIndexPorEmpresa($idEmpresa){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('id','descricao_site','combustivel','ano_fabricacao','valor_venda','exibir_valor_site')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('marca','modelo','ano_modelo')
		);
		
		$row->joinInner(
			array('fv'=>'fotos_veiculos'),
			'v.id = fv.id_veiculo',
			array('path')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = v.id_empresa',
			array()
		);
		
		$row->where("v.id_empresa = ".$idEmpresa);
		
		$row->where("e.ativo = 1");

		$row->where("fv.capa = 1");
		
		$row->where("v.ativo = 1");
		
		$row->where("v.novo_usado = 1");
		
		$row->where("v.vendido = 0");
		
		$row->where("v.excluido = 0");
		
		$row->where("v.exibir_site_estoque = 2 OR v.exibir_site_estoque = 3");
		
		$row->order('RAND()');
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
	
	}
	
	public function getVeiculosNovosIndexPorEmpresa($idEmpresa){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('id','descricao_site','combustivel','ano_fabricacao','valor_venda','exibir_valor_site')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('marca','modelo')
		);
		
		$row->joinInner(
			array('fv'=>'fotos_veiculos'),
			'v.id = fv.id_veiculo',
			array('path')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = v.id_empresa',
			array('cidade')
		);
		
		$row->where("v.id_empresa = ".$idEmpresa);
		
		$row->where("e.ativo = 1");
		
		$row->where("fv.capa = 1");
		
		$row->where("v.ativo = 1");
		
		$row->where("v.novo_usado = 0");
		
		$row->where("v.vendido = 0");
		
		$row->where("v.excluido = 0");
		
		$row->where("v.exibir_site_estoque = 2 OR v.exibir_site_estoque = 3");
		
		$row->order('RAND()');
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
	
	}
	
	public function getVeiculosNovosIndex(){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('id','descricao_site','combustivel','ano_fabricacao','valor_venda','exibir_valor_site')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('marca','modelo','segmento','ano_modelo')
		);
		
		$row->joinInner(
			array('fv'=>'fotos_veiculos'),
			'v.id = fv.id_veiculo',
			array('path')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = v.id_empresa',
			array('cidade')
		);
		
		$row->where("e.ativo = 1");
		
		$row->where("fv.capa = 1");
		
		$row->where("v.ativo = 1");
		
		$row->where("v.novo_usado = 0");
		
		$row->where("v.vendido = 0");
		
		$row->where("v.excluido = 0");
		
		$row->where("v.exibir_site_estoque = 2 OR v.exibir_site_estoque = 3");
		
		$row->order('RAND()');
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
	
	}
	
	
	public function _getSite($arr){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo','ano_modelo','marca', 'segmento','cod_fipe')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = v.id_empresa',
			array('cidade','nome_fantasia','logo_empresa'=>'path','tel1','tel2','endereco','bairro','estado','sistema_site','razao_social','cep','email')
		);
		
		if($arr['sistema_site'] == 1){
		
			$row->joinLeft(
				array('u'=>'usuarios'),
				'e.id = u.id_empresa',
				array('telefone','celular','id_perfil','cidade_usuario'=>'cidade','bairro_usuario'=>'bairro','estado_usuario'=>'estado')
			);
		
		}
		
		if($arr['id']){
		
			$row->where("v.id = ".$arr['id']);
		
		}
		
		if($arr['modelo']){
		
			$row->where("m.id = " .$arr['modelo']);
		
		}elseif($arr['marca']){
		
			if($arr['marca'] == "Citroën"){
			
				$row->where("m.marca = 'Citroen' OR m.marca = 'Citro&#235;n'");
			
			}else{
		
				//$row->where("m.marca = '".$arr['marca']."'");
				$row->where("m.marca LIKE '".$arr['marca']."%'");
				
			}
		
			
		
		}

		if($arr['cidade']){
		
			$row->where("e.cidade LIKE '".$arr['cidade']."%'");
		
		}
		
		if($arr['segmento']){
		
			$row->where("m.segmento = '".$arr['segmento']."'");
		
		}
		
		if($arr['str_veiculo']){
		
			$row->where("v.descricao_site LIKE '".$arr['str_veiculo']."%' OR m.modelo LIKE '".$arr['str_veiculo']."%'");
		
		}
		
		if($arr['novo_usado'] == 2){
		
			$row->where("v.novo_usado = 0");
		
		}elseif($arr['novo_usado'] == 1){
		
			$row->where("v.novo_usado = 1");
		
		}
		
		$zero ="";
		if($arr['maximo_ano'] == @date('Y')){
		
			$zero = "OR m.ano_modelo = 'Zero'";
		
		}
		
		if($arr['minimo_ano'] == @date('Y')){
		
			$zero = "OR m.ano_modelo = 'Zero'";
		
		}
		
		if($arr['minimo_ano']){
		
			$row->where("m.ano_modelo >= '" .$arr['minimo_ano']."' ".$zero);
		
		}
		
		if($arr['maximo_ano']){
		
			$row->where("m.ano_modelo <= '" .$arr['maximo_ano']."' ".$zero);
		
		}
		
		if($arr['maximo_preco']){
		
			$row->where("v.valor_venda <= '".$arr['maximo_preco']."'");
		
		}
		
		if($arr['minimo_preco']){
		
			$row->where("v.valor_venda >= '".$arr['minimo_preco']."'");
		
		}
		
		if($arr['id_empresa']){
		
			$row->where("v.id_empresa = '".$arr['id_empresa']."'");
		
		}
		
		if($arr['valor']){
		
			$valorMaximo = $arr['valor']+2000;
			$valorMinimo = $arr['valor']-2000;
		
			$row->where("v.valor_venda <=  ".$valorMaximo." AND v.valor_venda >= ".$valorMinimo);
		
		}
		
		$row->where("v.vendido = 0");
		$row->where("v.ativo = 1");
		$row->where("e.ativo = 1");
		$row->where("v.excluido = 0");
		$row->where("(v.exibir_site_estoque = 2 OR v.exibir_site_estoque = 3)");
		
		$row->order('m.ano_modelo DESC');
		
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
	
	}
	
	
	public function _getSiteAvancado($arr){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo' => 'upper(modelo)','ano_modelo' => 'upper(ano_modelo)','marca' => 'upper(marca)')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = v.id_empresa',
			array('cidade','nome_fantasia','logo_empresa'=>'path','tel1','tel2','endereco','bairro','estado','sistema_site','razao_social')
		);

		if($arr['sistema_site'] == 1){
		
			$row->joinLeft(
				array('u'=>'usuarios'),
				'e.id = u.id_empresa',
				array('telefone','celular','id_perfil','cidade_usuario'=>'cidade','bairro_usuario'=>'bairro','estado_usuario'=>'estado')
			);
		
		}
		
		if($arr['modelo']){
		
			$row->where("m.id = ".$arr['modelo']);
		
		}elseif($arr['marca']){
		
			if($arr['marca'] == "Citroën"){
			
				$row->where("m.marca = 'Citroen' OR m.marca = 'Citro&#235;n'");
			
			}else{
		
				$row->where("m.marca = '".$arr['marca']."'");
				
			}
		
		}
		
		if($arr['segmento']){
		
			$row->where("m.segmento = '".$arr['segmento']."'");
		
		}

		if($arr['cidade']){
		
			$row->where("e.cidade = '".$arr['cidade']."'");
		
		}
		
		if($arr['cor']){
		
			$row->where("v.cor LIKE '%".$arr['cor']."%'");
		
		}
		
		$zero ="";
		if($arr['maximo_ano'] == @date('Y')){
		
			$zero = "OR m.ano_modelo = 'Zero'";
		
		}
		
		if($arr['minimo_ano'] == @date('Y')){
		
			$zero = "OR m.ano_modelo = 'Zero'";
		
		}
		
		if($arr['minimo_ano']){
		
			$row->where("m.ano_modelo >= '" .$arr['minimo_ano']."' ".$zero);
		
		}
		
		if($arr['maximo_ano']){
		
			$row->where("m.ano_modelo <= '" .$arr['maximo_ano']."' ".$zero);
		
		}
		
		if($arr['maximo_preco']){
		
			$row->where("v.valor_venda <= '".$arr['maximo_preco']."'");
		
		}
		
		if($arr['minimo_preco']){
		
			$row->where("v.valor_venda >= '".$arr['minimo_preco']."'");
		
		}
		
		if($arr['anunciante'] == 1){
		
			$row->where("e.sistema_site = 0");
		
		}elseif($arr['anunciante'] == 10){
			
			$row->where("e.sistema_site = 1");
		
		}
		
		if($arr['combustivel']){
		
			$row->where("v.combustivel = '".$arr['combustivel']."'");
		
		}
		
		if($arr['portas']){
		
			$row->where("m.modelo like '% ".$arr['portas']." %'");
		
		}
		
		if($arr['novo_usado'] == 1){
		
			$row->where("v.novo_usado = 1");
		
		}
		
		if($arr['novo_usado'] == 2){
		
			$row->where("v.novo_usado != 1");
		
		}
		
		
		$row->where("v.vendido = 0");
		$row->where("v.ativo = 1");
		$row->where("e.ativo = 1");
		$row->where("v.excluido = 0");
		$row->where("(v.exibir_site_estoque = 2 OR v.exibir_site_estoque = 3)");
		
		$row->order('m.ano_modelo DESC');
		
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
	
	}
	
	public function getVeiculoPorId($arr){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo','ano_modelo','marca')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = v.id_empresa',
			array('cidade','nome_fantasia','logo_empresa'=>'path','tel1','tel2','endereco','bairro')
		);
		
		if($arr['id']){
		
			$row->where("v.id = ".$arr['id']);
		
		}
		
		$row->order('m.ano_modelo DESC');
		
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
	
	}
	
	public function del($id){

		$this->delete('id = ' . $id);

	}
	
	public function add($dados){
		
		try{

			return $this->insert($dados);
		
		} catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	}
	
	public function getVeiculosPorValor($valor){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('*')
		);
		  
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo','ano_modelo','marca')
		);
		
		$row->joinInner(
			array('fv'=>'fotos_veiculos'),
			'v.id = fv.id_veiculo',
			array('path')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = v.id_empresa',
			array('cidade')
		);
		
		
		
		$valorAlto = $valor+2000.00;
		$valorBaixo = $valor-2000.00;
		
		$row->where("e.ativo = 1");
		$row->where("v.vendido = 0");
		$row->where('fv.capa = 1');
		$row->where("v.ativo = 1");
		$row->where("v.excluido = 0");
		$row->where("(v.exibir_site_estoque = 2 OR v.exibir_site_estoque = 3)");
		$row->where('v.valor_venda <= '.$valorAlto.' AND v.valor_venda >= '.$valorBaixo);
		
		$row->order('m.ano_modelo DESC');
		
		$row->limit(10);
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
		
	}
	
	public function getLastId(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('id')
		);
		  
		$row->order('id DESC');
		$row->limit(1);
		return $row->query()->fetchAll();
		
	}
	
	public function getLastIdEmpresa($idEmpresa){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo','ano_modelo','marca')
			
		);
		
		$row->where("v.id_empresa = ".$idEmpresa);

		$row->order('id DESC');
		$row->limit(1);
		return $row->query()->fetchAll();
		
	}
	
	public function getVeiculoTroca($idEmpresa, $idNegociacao){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('*')
		);

		$row->where("id_empresa = ".$idEmpresa." AND id_negociacao = ".$idNegociacao. " AND temp_troca = 1");

		//$row->order('id DESC');

		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}

	public function getVeiculoTrocaNegociacao($idEmpresa, $idNegociacao){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('*')
		);

		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo','marca','ano_modelo','fipe'=>'preco')
		);
		
		$row->where("v.id_empresa = ".$idEmpresa." AND v.id_negociacao_troca = ".$idNegociacao. " AND v.temp_troca = 1");

		//$row->order('id DESC');

		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	
	public function getVeiculoSelecionadoCompleto($idVeiculos){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('modelo','marca','ano_modelo','preco', 'segmento')
		);
		
		$row->joinInner(
			array('cl'=>'checklist_veiculos'),
			'cl.id_veiculo = v.id',
			array('quitado_leasing','pf_pj','gnv','doc_gnv')
		);
		
		$row->where('v.id = ' .$idVeiculos);
		  
		return $row->query()->fetchAll();
		
	}
	
	public function getMarcasDistintas(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelos_11'),
		array('distinct(marca)')
		);
		
		$row->where('m.cod_fipe is not null');

		$row->order('marca ASC');

		return $row->query()->fetchAll();
		
	}
	
	public function getCountVeiculosAnuncios(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('qtd_anuncios'=>'count(*)')
		);
		
		$row->where('v.excluido = 0');
		$row->where('v.vendido = 0');
		$row->where('v.ativo = 1');

		return $row->query()->fetchAll();
		
	}
	
	public function getUltimosTresAnuncios(){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('v'=>'veiculos'),
			array('id','descricao_site','combustivel','ano_fabricacao','valor_venda','exibir_valor_site')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'm.id = v.id_modelo',
			array('marca','modelo')
		);
		
		$row->joinInner(
			array('fv'=>'fotos_veiculos'),
			'v.id = fv.id_veiculo',
			array('path')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = v.id_empresa',
			array('cidade')
		);
		
		$row->where("e.ativo = 1");
		
		$row->where("fv.capa = 1");
		
		$row->where("v.ativo = 1");
		
		$row->where("v.vendido = 0");
		
		$row->where("v.excluido = 0");
		
		$row->where("v.exibir_site_estoque = 2 OR v.exibir_site_estoque = 3");
		
		$row->order('v.id DESC');
		
		$row->limit(3);
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
	
	}
	
	public function getMarcasDistintasPorTipo($tipo){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelos_11'),
		array('distinct(marca)')
		);
		
		$row->where('m.cod_fipe is not null');
		$row->where("segmento = '".$tipo."'");

		$row->order('marca ASC');

		return $row->query()->fetchAll();
		
	}
	
	public function getVeiculosVendidos($arr){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('v'=>'veiculos'),
		array('*')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'v.id_modelo = m.id',
			array('marca','modelo')
		);
		
		$row->joinInner(
			array('n'=>'negociacoes'),
			'v.id_negociacao = n.id',
			array('data_concretizacao')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'v.id_empresa = e.id',
			array('nome_fantasia','idEmpresa'=>'e.id')
		);
		
		
		//$row->where("v.ativo = 1");
		//$row->where("v.excluido = 0");
		$row->where("v.vendido = 1");
		
		if($arr['id_empresa']){
		
			$row->where("v.id_empresa = ".$arr['id_empresa']);
		
		}
		
		if($arr['data_inicial_concretizacao']){
			
			$row->where("n.data_concretizacao >= '".$arr['data_inicial_concretizacao']." 00:00:00'");
			
		}
		
		if($arr['data_final_concretizacao']){
			
			$row->where("n.data_concretizacao <='".$arr['data_final_concretizacao']." 23:59:59'");
			
		}

		if($arr['data_inicial_abertura']){
			
			$row->where("n.data_abertura >= '".$arr['data_inicial_abertura']." 00:00:00'");
			
		}
		
		if($arr['data_final_abertura']){
			
			$row->where("n.data_abertura <='".$arr['data_final_abertura']." 23:59:59'");
			
		}
		
		
		$row->order('e.nome_fantasia ASC');
		$row->order('e.id ASC');
		
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
