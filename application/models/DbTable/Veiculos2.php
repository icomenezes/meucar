<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Veiculos extends Zend_Db_Table_Abstract
{

	protected $_name = 'veiculos';
	
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
			array('modelo','marca','ano_modelo','cod_fipe')
		);
		
		$row->where("v.id = ".$idVeiculo);
		
		
		
		//echo $row->__toString();
		
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
			array('modelo','marca','ano_modelo','fipe'=>'preco')
		);
		
		$row->joinLeft(
			array('n'=>'negociacoes'),
			'n.id = v.id_negociacao',
			array('data_concretizacao')
		);
		
		if($arr['id']){
		
			$row->where('v.id = ' . $arr['id']);
		
		}
		
		if($arr['id_empresa']){
		
			$row->where('v.id_empresa = ' . $arr['id_empresa']);
		
		}
		
		
		//Esta parte alterei pois estava dando erro 19/12/2012.	
		
		if($arr['vend'] == 1){
		
			$row->where('v.vendido = 0');
		
		}
		///////////////////////////////////////
		
		
		if($arr['vendidos'] == 1){
		
			$row->where('v.vendido = 1');
		
		}
		
		if($arr['temp_troca']){
		
			$row->where('v.temp_troca = 1');
		
		}
		
		if($arr['id_negociacao']){
		
			$row->where('v.id_negociacao = '.$arr['id_negociacao']);
		
		}
		
		if($arr['id_negociacao_troca']){
		
			$row->where('v.id_negociacao_troca = '.$arr['id_negociacao_troca']);
		
		}
		
		if($arr['id_negociacao_troca2']){
		
			$row->where('v.id_negociacao_troca2 = '.$arr['id_negociacao_troca2']);
		
		}
		
		if($arr['origem']){

			$row->where("v.origem = '".$arr['origem']."'");

		}
		
		if($arr['exibir_estoque']){
		
			$row->where("v.exibir_site_estoque = 1 OR v.exibir_site_estoque = 3");
		
		}
		
		if($arr['sql']){
		
			$row->where($arr['sql']);
		
		}
		
		
		if($arr['parcial']){
		
			if($arr['placa']){
		
				$row->where("v.placa LIKE '".$arr['placa']."%' OR m.modelo LIKE '".$arr['modelo']."%'");
		
			}
		
		}else{
		
			if($arr['placa']){
		
				$row->where("v.placa = '".$arr['placa']."'");
		
			}
			
			if($arr['modelo']){
		
				$row->where("m.modelo = '".$arr['modelo']."'");
		
			}			
		
		}
		
		$row->where("v.excluido = 0");
		
		if($arr['order']){
		
			$row->order("m.".$arr['order']);
			$row->order("m.modelo");
		
		}elseif($arr['ordem_data']){
		
			$row->order("v.id DESC");
		
		}else{
	
			$row->order('m.modelo');
		
		}
		
		if($arr['limit']){
		
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
			array('modelo','marca','ano_modelo','preco','cod_fipe')
		);
		
		$row->joinInner(
			array('u'=>'usuarios'),
			'u.id = v.id_usuario_alteracao',
			array('nome')
		);
		
		if($arr['id']){
		
			$row->where('v.id = ' . $arr['id']);
		
		}
		
		if($arr['id_empresa']){
		
			$row->where('v.id_empresa = ' . $arr['id_empresa']);
		
		}
		
		if($arr['vendido']){
		
			$row->where('v.vendido = ' . $arr['vendido']);
		
		}
		
		if($arr['venda']){
		
			$row->where('v.vendido = 0');
		
		}
		
		if($arr['parcial']){
		
			if($arr['placa']){
		
				$row->where("v.placa LIKE '".$arr['placa']."%'");
		
			}
			
			if($arr['modelo']){
		
				$row->where("m.modelo LIKE '".$arr['modelo']."%'");
		
			}
			
			if($arr['ano_modelo']){
		
				$row->where("m.ano_modelo LIKE '".$arr['ano_modelo']."%'");
		
			}
		
		}else{
		
			if($arr['placa']){
		
				$row->where("v.placa = '".$arr['placa']."'");
		
			}
			
			if($arr['modelo']){
		
				$row->where("m.modelo = '".$arr['modelo']."'");
		
			}

			if($arr['ano_modelo']){
		
				$row->where("m.ano_modelo = '".$arr['ano_modelo']."'");
		
			}
		
		}
		
		$row->where("v.excluido = 0");
	
		$row->order('m.marca ASC');
		$row->order('v.id DESC');
		
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
			array()
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
			array()
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
			array('modelo','ano_modelo','marca')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = v.id_empresa',
			array('cidade','nome_fantasia','logo_empresa'=>'path','tel1','tel2','endereco','bairro','estado','sistema_site')
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
		
		
		/*echo $row->__toString();*/
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
			array('cidade','nome_fantasia','logo_empresa'=>'path','tel1','tel2','endereco','bairro','estado','sistema_site')
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
			array()
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
			array('modelo','marca','ano_modelo','preco')
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
		
		
		$row->order('e.nome_fantasia ASC');
		
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
