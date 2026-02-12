<?php

header("Content-Type: text/html; charset=UTF-8",true);

class GraficosController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Clientes";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function ajaxAction(){

		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
	
		if($this->_getParam('fn') == 'contatos'){
			
			$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
			$dbVeiculosFluxo = new Application_Model_DbTable_VeiculosFluxo();
			$dbVeiculos = new Application_Model_DbTable_Veiculos();

			if($_SESSION['sessionUser']['id_perfil'] != 2 && $_SESSION['sessionUser']['id_perfil'] != 4 && $_SESSION['sessionUser']['id_perfil'] != 9){
				
				$dados['vendedor'] = $_SESSION['sessionUser']['id'];

			}
			
			$dados['relatorio'] = true;
			
			if($this->_getParam('vendedor')){
				$dados['vendedor'] = $this->_getParam('vendedor');
			}
			
			if($this->_getParam('origem') && $this->_getParam('origem') != "Todos"){
				$dados['origem'] = $this->_getParam('origem');
			}
			
			if($this->_getParam('resultado')){
				$dados['resultado'] = $this->_getParam('resultado');
			}
			
			if($this->_getParam('motivo_pre')){
				$dados['motivo_pre'] = $this->_getParam('motivo_pre');
			}
			
			if($this->_getParam('filtro')){
				$dados['filtro'] = $this->_getParam('filtro');
			}
			
			if($this->_getParam('origem_sites')){
				$dados['origem_sites'] = $this->_getParam('origem_sites');
			}

			$dados['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			if($this->_getParam('nome')){
				$dados['nome'] = $this->_getParam('nome');
			}
			if($this->_getParam('veiculo')){
				$dados['veiculo'] = $this->_getParam('veiculo');
			}
			if($this->_getParam('data_inicial')){
				$dados['data_inicial'] = implode("-",array_reverse(explode("/",$this->_getParam('data_inicial'))));
			}
			if($this->_getParam('data_final')){
				$dados['data_final'] = implode("-",array_reverse(explode("/",$this->_getParam('data_final'))));
			}

			$arrFluxo = $dbFluxoClientes->getClientesFluxo($dados);
			
			if(isset($dados['filtro']) && $dados['filtro'] == "Agendada"){
				$arrFluxoCliente = $arrFluxo;
			}else{
				$arrFluxoAgendada = $dbFluxoClientes->getClientesFluxoAgendado($dados);
				$arrFluxoCliente = array_merge($arrFluxo, $arrFluxoAgendada);
			}

			$strFluxo = "<style>
							.agend{
								font-weight:bold;
								font-size:10px;
								padding:3px;
								background-color:#008000;
								color:#FFF;
								-webkit-animation-name: example;
								-webkit-animation-duration: 4s; /* Safari 4.0 - 8.0 */
								-webkit-animation-iteration-count: 10;
							}
							
							@-webkit-keyframes example{
								0% {background-color:#33ff33; color:#fff;}
								50%{background-color:white; color:#777;}
								75%{background-color:#33ff33; color:#fff;}
								100% {background-color:#33ff33; color:#fff;}
							}
							
							#fluxo tr td{
								border-color:#fff;
								//border-right: 0px;
							}

						 </style>";
			$strFluxo .= "<table class='table' id='fluxo'>
						<tr>
							<th style='background-color:#777;' colspan='11'>FLUXO DE LOJA</th>
						</tr>
						<tr>
							<th>Nome</th>
							<th>Origem</th>
							<th>Veículo Troca</th>
							<th>Data</th>
							<th>Resultado</th>
							<th>Vendedor</th>
							<th>Última</th>
							<th>Resposta</th>
							<th>Valor</th>
							<th colspan='2'>Veículo</th> 
						</tr>";
			
			$cont = 0;
			$temEmail = false;
			
			$arrTotalVendedores = array();
			$arrTotalOrigem = array();
			$arrTotalResultado = array();
			$arrTotalContato = array();
			$arrTotalVeiculo = array();
			$arrTotalSites = array();
			$arrTotalMotivos = array();
			$arrTotalPorDia = array();

			foreach($arrFluxoCliente as $fluxoCliente){
				
				if($fluxoCliente['resultado'] != "Duplicado" && $fluxoCliente['motivo_pre'] != 8){
				
					$contSpan = 1;
					$cont++;
					$strVeiculo = "";
					
					$arrVeiculos = $dbVeiculosFluxo->getVeiculoFluxo($fluxoCliente['id']);
					
					if($cont%2==0){
						$color = "bgcolor='#DDDDDD'";
					}else{
						$color = "bgcolor='#FFFFFF'";
					}

					$dbOrigemClientes = new Application_Model_DbTable_OrigemClientes();
				
					$arrOrigem = $dbOrigemClientes->_getOrigem(array("id_empresa"=>$_SESSION['sessionUser']['id_empresa'], "noDefault"=>true));

					foreach($arrOrigem as $origem){
						
						if($fluxoCliente['origem'] == $origem['id']){
							
							$fluxoCliente['origem'] = $origem['descricao'];
						
						}
					
					}

					if($arrVeiculos){
					
						foreach($arrVeiculos as $veiculo){
							
							$contSpan++;
							
							$cor = "";
							$color2 = $color;

							if($veiculo['estoque'] == 0 && $fluxoCliente['resultado'] == "Negociação"){
							
								$arrFiltro['valor_venda'] = $fluxoCliente['valor'];
								$arrFiltro['modelo'] = $veiculo['veiculo'];
								$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

							}elseif($veiculo['estoque'] == 1 && $fluxoCliente['resultado'] == "Negociação"){
								
								$arrFiltro['valor_venda'] = $fluxoCliente['valor'];
								$arrFiltro['modelo'] = $veiculo['veiculo'];
								$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

							}

							$strVeiculo .= "<tr ".$color2." ><td ".$cor.">".$veiculo['veiculo']."</td><td ".$cor.">".$veiculo['ano_modelo']."</td></tr>";
							
							if(!isset($arrTotalVeiculo[strtoupper(str_replace("?", "e",utf8_decode($veiculo['veiculo'])))." ".$veiculo['ano_modelo']])){
								$arrTotalVeiculo[strtoupper(str_replace("?", "e",utf8_decode($veiculo['veiculo'])))." ".$veiculo['ano_modelo']] = 0;
							}
							$arrTotalVeiculo[strtoupper(str_replace("?", "e",utf8_decode($veiculo['veiculo'])))." ".$veiculo['ano_modelo']] += 1;
							
						}
					
					
					}else{
						$strVeiculo .= "<td></td><td></td>";					
					}
					
					
					
					$titleMotivo = "";
					
					if($fluxoCliente['descricao'] && $fluxoCliente['id_motivo_pre'] != 1){
						$titleMotivo = "title='".$fluxoCliente['descricao']."'";
					}elseif($fluxoCliente['motivo']){
						$titleMotivo = "title='".$fluxoCliente['motivo']."'";
					}

					$dbUsuarios = new Application_Model_DbTable_Usuarios();
				
					$arrUsuario = $dbUsuarios->_get(array("id_empresa"=>$_SESSION['sessionUser']['id_empresa'], "id"=>$fluxoCliente['id_usuario']));

					if($fluxoCliente['data'] && $fluxoCliente['data_tempo_resposta']){
						
						$entrada = new DateTime($fluxoCliente['data']);
						$saida   = new DateTime($fluxoCliente['data_tempo_resposta']);
						$diferenca = $saida->diff($entrada);

						$arrHorasResp = get_object_vars($diferenca);

						$totalTempo = 0;
						
						if($arrHorasResp['y'] != 0){
							$totalTempo += $arrHorasResp['y']*8760;
						}
						
						if($arrHorasResp['m'] != 0){
							$totalTempo += $arrHorasResp['m']*730.001;
						}
						
						if($arrHorasResp['d'] != 0){
							$totalTempo += $arrHorasResp['d']*24;
						}
						
						if($arrHorasResp['h'] != 0){
							$totalTempo += $arrHorasResp['h'];
							$totalTempo = round($totalTempo,0)."Hs.";
						}elseif($arrHorasResp['i'] != 0 && $totalTempo == 0){
							$totalTempo += $arrHorasResp['i'];
							$totalTempo = $totalTempo."min.";
						}else{
							$totalTempo = round($totalTempo,0)."Hs.";
						}
		
					}else{
						$totalTempo = "N/A";
					}
					
					
					if($arrUsuario[0]['nome'] == ""){
						$arrUsuario[0]['nome'] = "Desconhecido";
					}
					if(!isset($arrTotalVendedores[$arrUsuario[0]['nome']])){
						$arrTotalVendedores[$arrUsuario[0]['nome']] = 0;
					}
					$arrTotalVendedores[$arrUsuario[0]['nome']] += 1;
					
					if(!isset($arrTotalOrigem[$fluxoCliente['origem']])){
						$arrTotalOrigem[$fluxoCliente['origem']] = 0;
					}
					$arrTotalOrigem[$fluxoCliente['origem']] += 1;
					
					if($fluxoCliente['resultado'] == ""){
						$fluxoCliente['resultado'] = "Sem resultado";
					}
					
					//$arrTotalResultado[$fluxoCliente['resultado']] += 1;
					
					if(!isset($arrTotalResultado[$fluxoCliente['resultado']])){
						$arrTotalResultado[$fluxoCliente['resultado']] = 0;
					}
					if($fluxoCliente['resultado'] == "Fechou"){
						if($fluxoCliente['gerado_negociacao']){
							$arrTotalResultado[$fluxoCliente['resultado']] += 1;
						}
					}else{
						$arrTotalResultado[$fluxoCliente['resultado']] += 1;	
					}
					
					
					if($this->_getParam('resultado') == "Desistiu" || $this->_getParam('resultado') == "Concorrente"){
						if(!$fluxoCliente['descricao']){
							$fluxoCliente['descricao'] = "Outro motivo";
						}
						if(!isset($arrTotalMotivos[$fluxoCliente['descricao']])){
							$arrTotalMotivos[$fluxoCliente['descricao']] = 0;
						}
						$arrTotalMotivos[$fluxoCliente['descricao']] += 1;
					}
					
					if(strpos(strtolower($fluxoCliente['origem']), 'fone') !== false || strpos(strtolower($fluxoCliente['origem']), 'telefone') !== false){
						if(!isset($arrTotalContato['Telefone'])){
							$arrTotalContato['Telefone'] = 0;
						}
						$arrTotalContato['Telefone'] += 1;
					}elseif(strpos(strtolower($fluxoCliente['origem']), 'email') !== false || strpos(strtolower($fluxoCliente['origem']), 'e-mail') !== false){
						if(!isset($arrTotalContato['E-mail'])){
							$arrTotalContato['E-mail'] = 0;
						}
						$arrTotalContato['E-mail'] += 1;
					}elseif(strpos(strtolower($fluxoCliente['origem']), 'whats') !== false || strpos(strtolower($fluxoCliente['origem']), 'whatsapp') !== false){
						if(!isset($arrTotalContato['WhatsApp'])){
							$arrTotalContato['WhatsApp'] = 0;
						}
						$arrTotalContato['WhatsApp'] += 1;
					}elseif(strpos(strtolower($fluxoCliente['origem']), 'frente de loja') !== false){
						if(!isset($arrTotalContato['Frente de Loja'])){
							$arrTotalContato['Frente de Loja'] = 0;
						}
						$arrTotalContato['Frente de Loja'] += 1;
					}
					
					
					if(strpos(strtolower($fluxoCliente['origem']), 'comprecar') !== false){
						if(!isset($arrTotalSites['Comprecar'])){
							$arrTotalSites['Comprecar'] = 0;
						}
						$arrTotalSites['Comprecar'] += 1;
					}elseif(strpos(strtolower($fluxoCliente['origem']), 'facebook') !== false){
						if(!isset($arrTotalSites['Facebook'])){
							$arrTotalSites['Facebook'] = 0;
						}
						$arrTotalSites['Facebook'] += 1;
					}elseif(strpos(strtolower($fluxoCliente['origem']), 'icarros') !== false){
						if(!isset($arrTotalSites['Icarros'])){
							$arrTotalSites['Icarros'] = 0;
						}
						$arrTotalSites['Icarros'] += 1;
					}elseif(strpos(strtolower($fluxoCliente['origem']), 'mercado') !== false){
						if(!isset($arrTotalSites['Mercado Livre'])){
							$arrTotalSites['Mercado Livre'] = 0;
						}
						$arrTotalSites['Mercado Livre'] += 1;
					}elseif(strpos(strtolower($fluxoCliente['origem']), 'novo') !== false){
						if(!isset($arrTotalSites['Meu Carro Novo'])){
							$arrTotalSites['Meu Carro Novo'] = 0;
						}
						$arrTotalSites['Meu Carro Novo'] += 1;
					}elseif(strpos(strtolower($fluxoCliente['origem']), 'car') !== false){
						if(!isset($arrTotalSites['Meu Car'])){
							$arrTotalSites['Meu Car'] = 0;
						}
						$arrTotalSites['Meu Car'] += 1;
					}elseif(strpos(strtolower($fluxoCliente['origem']), 'olx') !== false){
						if(!isset($arrTotalSites['Olx'])){
							$arrTotalSites['Olx'] = 0;
						}
						$arrTotalSites['Olx'] += 1;
					}elseif(strpos(strtolower($fluxoCliente['origem']), 'site') !== false){
						if(!isset($arrTotalSites['Site Loja'])){
							$arrTotalSites['Site Loja'] = 0;
						}
						$arrTotalSites['Site Loja'] += 1;
					}elseif(strpos(strtolower($fluxoCliente['origem']), 'webmotors') !== false){
						if(!isset($arrTotalSites['Webmotors'])){
							$arrTotalSites['Webmotors'] = 0;
						}
						$arrTotalSites['Webmotors'] += 1;
					}
					
				}

			}//FIM FOREACH FLUXO
			
			
			arsort($arrTotalVendedores);
			arsort($arrTotalOrigem);
			arsort($arrTotalResultado);
			arsort($arrTotalContato);
			arsort($arrTotalVeiculo);
			arsort($arrTotalSites);
			
			if($arrTotalMotivos){
				arsort($arrTotalMotivos);
			}

			if($cont > 0){
				echo json_encode(array(0=>$arrTotalVendedores,1=>$arrTotalOrigem,2=>$arrTotalResultado,3=>$arrTotalContato,4=>$arrTotalVeiculo,5=>$arrTotalSites,6=>$arrTotalMotivos));
			}else{
				echo json_encode(array(0=>"erro"));
			}
			
			
		}elseif($this->_getParam('fn') == 'contatos_geral'){
			
			$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
			
		 /////Busca somente por periodo/////////////////////////////////////////////////////////////

			if($_SESSION['sessionUser']['id_perfil'] != 2 && $_SESSION['sessionUser']['id_perfil'] != 4 && $_SESSION['sessionUser']['id_perfil'] != 9){
				$dadosPeriodo['vendedor'] = $_SESSION['sessionUser']['id'];
			}
			
			if($this->_getParam('vendedor')){
				$dadosPeriodo['vendedor'] = $this->_getParam('vendedor');
			}
		
			$dadosPeriodo['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$dadosPeriodo['data_inicial'] = implode("-",array_reverse(explode("/",$this->_getParam('data_inicial'))));
			$dadosPeriodo['data_final'] = implode("-",array_reverse(explode("/",$this->_getParam('data_final'))));
			
			$arrFluxo = $dbFluxoClientes->getClientesFluxo($dadosPeriodo);
			$arrFluxoResposta = $dbFluxoClientes->getFluxoDataResposta($dadosPeriodo);
			$arrFluxoUltima = $dbFluxoClientes->getFluxoDataUltimaVisu($dadosPeriodo);
			
			
			
			foreach($arrFluxo as $fluxo){
				
				if($fluxo['resultado'] != "Duplicado" && $fluxo['motivo_pre'] != 8){

					if(!isset($arrPorDia[current(explode(" ",$fluxo['data']))])){
						$arrPorDia[current(explode(" ",$fluxo['data']))] = 0;
					}
					$arrPorDia[current(explode(" ",$fluxo['data']))] += 1;
					
					$arrVendedores[$fluxo['nome_usuario']] = $fluxo['nome_usuario'];

					if(!isset($arrVendedorDia[$fluxo['nome_usuario']])){
						$arrVendedorDia[$fluxo['nome_usuario']] = 0;
					}
					$arrVendedorDia[$fluxo['nome_usuario']] += 1;

					if($fluxo['resultado'] == "Fechou" && $fluxo['gerado_negociacao']){
						if(!isset($arrFechou[$fluxo['nome_usuario']])){
							$arrFechou[$fluxo['nome_usuario']] = 0;
						}
						$arrFechou[$fluxo['nome_usuario']] += 1;
					}
				
				}
				
			}
			
			foreach($arrFluxoResposta as $fluxoResposta){
				
				if($fluxoResposta['resultado'] != "Duplicado" && $fluxoResposta['motivo_pre'] != 8){
				
					if(!isset($arrPorDiaResposta[current(explode(" ",$fluxoResposta['data_tempo_resposta']))])){
						$arrPorDiaResposta[current(explode(" ",$fluxoResposta['data_tempo_resposta']))] = 0;
					}
					$arrPorDiaResposta[current(explode(" ",$fluxoResposta['data_tempo_resposta']))] += 1;
					
					$arrVendedores[$fluxoResposta['nome_usuario']] = $fluxoResposta['nome_usuario'];

					if(!isset($arrVendedorResposta[$fluxoResposta['nome_usuario']])){
						$arrVendedorResposta[$fluxoResposta['nome_usuario']] = 0;
					}
					$arrVendedorResposta[$fluxoResposta['nome_usuario']] += 1;
					
				}
				
			}
			
			foreach($arrFluxoUltima as $fluxoUltima){
				
				if($fluxoUltima['resultado'] != "Duplicado" && $fluxoUltima['motivo_pre'] != 8){
					
					if(!isset($arrPorDiaUltima[current(explode(" ",$fluxoUltima['ultima_visualizacao']))])){
						$arrPorDiaUltima[current(explode(" ",$fluxoUltima['ultima_visualizacao']))] = 0;
					}
					$arrPorDiaUltima[current(explode(" ",$fluxoUltima['ultima_visualizacao']))] += 1;
					
					$arrVendedores[$fluxoUltima['nome_usuario']] = $fluxoUltima['nome_usuario'];

					if(!isset($arrVendedorUltima[$fluxoUltima['nome_usuario']])){
						$arrVendedorUltima[$fluxoUltima['nome_usuario']] = 0;
					}
					$arrVendedorUltima[$fluxoUltima['nome_usuario']] += 1;
					
				}
				
			}

			$dataI = explode("-", $dadosPeriodo['data_inicial']);
			$data = $dadosPeriodo['data_inicial'];
			$strDados = "";
			
			while($data != $dadosPeriodo['data_final']){
				
				$data = @date('Y-m-d', mktime(0,0,0, $dataI[1],$dataI[2], $dataI[0]));
				
				if(!isset($arrPorDia[$data])){
					$arrPorDia[$data] = 0;
				}
				
				if(!isset($arrPorDiaResposta[$data])){
					$arrPorDiaResposta[$data] = 0;
				}
				
				if(!isset($arrPorDiaUltima[$data])){
					$arrPorDiaUltima[$data] = 0;
				}
				
				if($arrPorDiaUltima[$data] != 0 || $arrPorDiaResposta[$data] != 0 || $arrPorDia[$data] != 0){
					$strDados .= substr(implode("/",array_reverse(explode("-", $data))), 0,-5).",".$arrPorDia[$data].",".$arrPorDiaResposta[$data].",".$arrPorDiaUltima[$data].":";
				}

				$dataI[2]++;

			}
			
			if($dadosPeriodo['data_inicial'] == $dadosPeriodo['data_final']){
				if($arrPorDiaUltima[$data] != 0 || $arrPorDiaResposta[$data] != 0 || $arrPorDia[$data] != 0){
					$strDados .= substr(implode("/",array_reverse(explode("-", $dadosPeriodo['data_inicial']))), 0,-5).",".$arrPorDia[$data].",".$arrPorDiaResposta[$data].",".$arrPorDiaUltima[$data].":";
				}
			}
			
			$strDados = substr($strDados,0,-1);
			
			$strVendedores = "";
			$strFechou = "";
			
			foreach($arrVendedores as $vendedores){
				
				if(!isset($arrVendedorDia[$vendedores])){
					$arrVendedorDia[$vendedores] = 0;
				}
				if(!isset($arrVendedorResposta[$vendedores])){
					$arrVendedorResposta[$vendedores] = 0;
				}
				if(!isset($arrVendedorUltima[$vendedores])){
					$arrVendedorUltima[$vendedores] = 0;
				}

				$strVendedores .= $vendedores.",".$arrVendedorDia[$vendedores].",".$arrVendedorResposta[$vendedores].",".$arrVendedorUltima[$vendedores].":";
				
				$strFechou .= $vendedores.",".$arrVendedorDia[$vendedores].",".$arrFechou[$vendedores].":";
				
			}
			
			$strVendedores = substr($strVendedores,0,-1);
			$strFechou = substr($strFechou,0,-1);
			
			echo $strDados."|".$strVendedores."|".$strFechou;

		}
		
	}
	
	
	public function contatosAction(){
		
		$dbUsuarios = new Application_Model_DbTable_Usuarios();
		$dbMotivos = new Application_Model_DbTable_Motivos();
		$strVendedores = "";
		$idVendedor = "";
		
		foreach($dbUsuarios->getVendedores() as  $vendedor){
			if($vendedor['id'] != $idVendedor){
				$strVendedores .= "<option value='".$vendedor['id']."'>".$vendedor['nome']."</option>";
			}
			$idVendedor = $vendedor['id'];
		}

		$arrMotivos = $dbMotivos->getMotivos();

		$this->view->arrMotivos = $arrMotivos;
		$this->view->strVendedores = $strVendedores;
		
	}
	
	public function contatosGeralAction(){
		
		$dbUsuarios = new Application_Model_DbTable_Usuarios();
		$strVendedores = "";
		$idVendedor = "";
		
		foreach($dbUsuarios->getVendedores() as  $vendedor){
			if($vendedor['id'] != $idVendedor){
				$strVendedores .= "<option value='".$vendedor['id']."'>".$vendedor['nome']."</option>";
			}
			$idVendedor = $vendedor['id'];
		}

		$this->view->strVendedores = $strVendedores;
		
	}
	
}
	
?>