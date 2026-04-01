<?php

header("Content-Type: text/html; charset=UTF-8",true);



class NovosRelatoriosController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Relat&oacute;rios";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	
	public function contatosAction(){
		
		$dbUsuarios = new Application_Model_DbTable_Usuarios();
		$dbMotivos = new Application_Model_DbTable_Motivos();
		$strVendedores = "";
		$idVendedor = "";
		
		foreach($dbUsuarios->getTodosUsuarios() as  $vendedor){
			if($vendedor['id'] != $idVendedor){
				$strVendedores .= "<option value='".$vendedor['id']."'>".$vendedor['nome']."</option>";
			}
			$idVendedor = $vendedor['id'];
		}

		$arrMotivos = $dbMotivos->getMotivos();

		$this->view->arrMotivos = $arrMotivos;
		$this->view->strVendedores = $strVendedores;
		
	}
	
	
	public function fluxoClientesAction(){
		
		$dbUsuarios = new Application_Model_DbTable_Usuarios();
		$dbMotivos = new Application_Model_DbTable_Motivos();
		$strVendedores = "";
		$idVendedor = "";
		
		foreach($dbUsuarios->getTodosUsuarios() as  $vendedor){
			if($vendedor['id'] != $idVendedor){
				$strVendedores .= "<option value='".$vendedor['id']."'>".$vendedor['nome']."</option>";
			}
			$idVendedor = $vendedor['id'];
		}

		$arrMotivos = $dbMotivos->getMotivos();

		$this->view->arrMotivos = $arrMotivos;
		$this->view->strVendedores = $strVendedores;
		
	}
	
	public function origemVendasAction(){

		$dbOrigemClientes = new Application_Model_DbTable_OrigemClientes();

		$arrOrigem = $dbOrigemClientes->_getOrigem(array('noDefault'=>true, 'id_empresa'=>$_SESSION['sessionUser']['id_empresa']));
		
		$this->view->arrOrigem = $arrOrigem;

	}
	
	
	public function pesquisaSatisfacaoAction(){

		$dbUsuarios = new Application_Model_DbTable_Usuarios();

		$arrUsuarios = $dbUsuarios->getUsuariosDuasLojas($_SESSION['sessionUser']['id_empresa'], 239);
		
		$this->view->arrUsuarios = $arrUsuarios;

	}
	

	public function corretorasAction(){
		
		$this->validaAcesso('relatorios');

	}
	
	public function preparacaoAction(){
		
		//$this->validaAcesso('relatorios');
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arr['vendido'] = 0;
	
		$arrVeiculos = $dbVeiculos->getVeiculoEstoquePreparacao($arr);
		
		$this->view->arrVeiculos = $arrVeiculos;

	}
	
	
	public function ajaxAction(){

		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		if(empty($_SESSION['sessionUser']['id_empresa'])){
			echo json_encode(array('erro' => 'Sessão expirada. Por favor, faça login novamente.'));
			return;
		}
	
		if($this->_getParam('fn') == 'relatorio_fluxo_clientes'){
			
			$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
			$dbVeiculosFluxo = new Application_Model_DbTable_VeiculosFluxo();
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
			
			$dados['relatorio'] = true;
			$arrTotalVendedores = array();
			$arrTotalOrigem = array();
			$arrTotalResultado = array();
			$arrTotalMotivos = array();
			$arrTotalContato = array();
			$arrTotalSites = array();
			$arrTotalVeiculo = array();
			$style = "";

			if($_SESSION['sessionUser']['id_perfil'] != 2 && $_SESSION['sessionUser']['id_perfil'] != 4 && $_SESSION['sessionUser']['id_perfil'] != 9){
				
				$dados['vendedor'] = $_SESSION['sessionUser']['id'];

			}
			
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
			if($this->_getParam('nome')){
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

			//var_export($dados);

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
							
							.fluxo tr td{
								border-color:#fff;
								//border-right: 0px;
							}

						 </style>";
			$strFluxo .= "<table class='table' id='fluxo' class='fluxo'>
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
					
					/*
					if($fluxoCliente['resultado'] == "Desistiu"){
						
						$style = "style='background-color: pink; color: #888;'";
					
					}elseif($fluxoCliente['resultado'] == "Fechou"){
						
						$style = "style='background-color: green; color: #fff;'";
					
					}elseif($fluxoCliente['resultado'] == "Concorrente"){
						
						$style = "style='background-color: red; color: #fff;'";
					
					}elseif($fluxoCliente['resultado'] == "Negociação"){
						
						$style = "style='background-color: yellow; color: #888;'";

					}elseif($fluxoCliente['resultado'] == "Vai fechar"){
						
						$style = "style='background-color: orange; font-weight:bold; color: #FFF;'";
						
					}elseif($fluxoCliente['resultado'] == "Só venda"){
						
						$style = "style='background-color: blue; font-weight:bold; color: #FFF;'";
						
					}elseif($fluxoCliente['resultado'] == "Agendado"){
						
						$style = "style='background-color: #eee; font-weight:bold; color: #555;'";
					
					}else{
						
						$style = "";
					
					}

					if($fluxoCliente['resultado'] != "Desistiu" && $fluxoCliente['resultado'] != "Fechou" && $fluxoCliente['resultado'] != "Concorrente" && $fluxoCliente['resultado'] != "Agendado"){
						
						$arrDate = explode("-", $fluxoCliente['ultima_visualizacao']);
						if($fluxoCliente['ultima_visualizacao'] && mktime(0,0,0,$arrDate[1], $arrDate[2], $arrDate[0]) == mktime(0,0,0,@date('m'), @date('d')-4, @date('Y')) && $fluxoCliente['envia_email'] == 0){
							if($_SESSION['sessionUser']['id_empresa'] == 3 || $_SESSION['sessionUser']['id_empresa'] == 239){
								$color = "bgcolor='#FF7777'";
								$data = date('Y-m-d', mktime(0,0,0,@date('m'), @date('d')-4, @date('Y')));
								$temEmail = true;

								$dbFluxoClientes->edt($fluxoCliente['id'], array('envia_email'=>1));
								
							}
						}elseif($fluxoCliente['ultima_visualizacao'] && mktime(0,0,0,$arrDate[1], $arrDate[2], $arrDate[0]) < mktime(0,0,0,@date('m'), @date('d')-1, @date('Y'))){
							$color = "bgcolor='#FF7777'";
						}elseif($fluxoCliente['ultima_visualizacao'] && mktime(0,0,0,$arrDate[1], $arrDate[2], $arrDate[0]) == mktime(0,0,0,@date('m'), @date('d')-1, @date('Y'))){
							$color = "bgcolor='#FFFF55'";
						}
						
						
					}
					
					if($fluxoCliente['atualizacao'] == 1){
						$color = "bgcolor='#33ff33'";
					}

					if($fluxoCliente['data_agendamento'] && strtotime($fluxoCliente['data_agendamento']) <= strtotime(@date('Y-m-d')) && strtotime($fluxoCliente['ultima_visualizacao']) < strtotime(@date('Y-m-d'))){
						$color = "bgcolor='#33ff33'";
					}
					
					*/
					
					if($arrVeiculos){
					
						foreach($arrVeiculos as $veiculo){
							
							$contSpan++;
							
							$cor = "";
							$color2 = $color;

							if($veiculo['estoque'] == 0 && $fluxoCliente['resultado'] == "Negociação"){
							
								$arrFiltro['valor_venda'] = $fluxoCliente['valor'];
								$arrFiltro['modelo'] = $veiculo['veiculo'];
								$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
								

								//$arr = $dbVeiculos->getVeiculoEstoqueFluxo($arrFiltro);

								//if($arr[0]['id']){
									
								//	$color2 = $color." style='cursor:pointer; text-decoration:underline;' onClick='javascript:window.open(\"http://sistemameucar.com.br/veiculos/edt/id/".$arr[0]['id']."\")'";
									
									
								//}else{
									
								//	$color2 = "bgcolor='#008000'";
								//	$cor = "style='color:#fff; font-weight:bold;'";
								
								//}
							
							}elseif($veiculo['estoque'] == 1 && $fluxoCliente['resultado'] == "Negociação"){
								
								$arrFiltro['valor_venda'] = $fluxoCliente['valor'];
								$arrFiltro['modelo'] = $veiculo['veiculo'];
								$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

								//$arr = $dbVeiculos->getVeiculoEstoqueFluxo($arrFiltro);

								//if($arr[0]['id']){
								//	$color2 = $color." style='cursor:pointer; text-decoration:underline;' onClick='javascript:window.open(\"http://sistemameucar.com.br/veiculos/edt/id/".$arr[0]['id']."\")'";
								//}else{
								//	$color2 = "bgcolor='#008000'";
								//	$cor = "style='color:#fff; font-weight:bold;'";
								//}
								
							}

							$strVeiculo .= "<tr ".$color2." ><td ".$cor.">".$veiculo['veiculo']."</td><td ".$cor.">".$veiculo['ano_modelo']."</td></tr>";
							
							if(!isset($arrTotalVeiculo[strtoupper($veiculo['veiculo'])." ".$veiculo['ano_modelo']])){
								$arrTotalVeiculo[strtoupper($veiculo['veiculo'])." ".$veiculo['ano_modelo']] = 0;
							}
							$arrTotalVeiculo[strtoupper($veiculo['veiculo'])." ".$veiculo['ano_modelo']] += 1;
							
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
					
					//$arrTempNome = explode(" ", $fluxoCliente['nome']);
					//$fluxoCliente['nome'] = $arrTempNome[0]." ".$arrTempNome[1];

					$dbUsuarios = new Application_Model_DbTable_Usuarios();
				
					$arrUsuario = $dbUsuarios->_get(array("id_empresa"=>$_SESSION['sessionUser']['id_empresa'], "id"=>$fluxoCliente['id_usuario']));

					$dataAgendamento = "";

					/*
					$arrTempUsuario = explode(" ", $arrUsuario[0]['nome']);
					$arrUsuario[0]['nome'] = $arrTempUsuario[0]." ".$arrTempUsuario[1]." ".$arrTempUsuario[2];
					
					
					$dataAgendamento = "";
					if($fluxoCliente['data_agendamento']){
						$dataAgendamento = "<div class='agend'>Agendado:&nbsp".implode("/", array_reverse(explode("-", $fluxoCliente['data_agendamento'])))."</div>";
					}
					*/
					
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
					
					if(!isset($arrTotalVendedores[$arrUsuario[0]['nome']])){
						$arrTotalVendedores[$arrUsuario[0]['nome']] = 0;
					}
					$arrTotalVendedores[$arrUsuario[0]['nome']] += 1;

					if(!isset($arrTotalOrigem[$fluxoCliente['origem']])){
						$arrTotalOrigem[$fluxoCliente['origem']] = 0;
					}
					$arrTotalOrigem[$fluxoCliente['origem']] += 1;
					
					
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

					$strFluxo .= "<tr $color style='cursor:pointer;' onClick='javascript:window.open(\"http://sistemameucar.com.br/clientes/add-cliente-fluxo-auto-salvar/id/".$fluxoCliente['id']."\")'>
									<td style='border-right:0px;' rowspan='".$contSpan."'>".$fluxoCliente['nome']."</td>
									<!--<td rowspan='".$contSpan."'>".$dataAgendamento."</td>--->
									<td rowspan='".$contSpan."'>".$fluxoCliente['origem']."</td>
									<td rowspan='".$contSpan."'>".$fluxoCliente['veiculo_troca']." / ".$fluxoCliente['ano_modelo_troca']."</td>
									<td rowspan='".$contSpan."'>".implode("/",array_reverse(explode("-",current(explode(" ",$fluxoCliente['data'])))))."</td>
									<td class='resultado_motivo' ".$style." rowspan='".$contSpan."' ".$titleMotivo.">".$fluxoCliente['resultado']."</td>
									<td rowspan='".$contSpan."'>".$arrUsuario[0]['nome']."</td>
									<td rowspan='".$contSpan."'>".implode("/",array_reverse(explode("-",$fluxoCliente['ultima_visualizacao'])))."</td>
									<td rowspan='".$contSpan."'>".$totalTempo."</td>
									<td rowspan='".$contSpan."'>R$ ".money_format("%i",$fluxoCliente['valor'])."</td>
									".$strVeiculo."
								  </tr>";
				}
				
			}

			$strFluxo = $strFluxo."</table>";
			
			if($temEmail && $data){
				$this->email($data);//Envia e-mail para o gerente informando.
			}
			
				
/////////////////////////////Total Vendedores////////////////////////////////////////////////			
			$tableVendedores = "";
			if($arrTotalVendedores && !$this->_getParam('vendedor')){
				
				$tableVendedores = "<table class='table'>
										<tr>
											<th style='background-color:#777;' colspan='2'>VENDEDORES</th>
										</tr>
										<tr>
											<th>Nome</th>
											<th>Total</th>
										</tr>
										";
				arsort($arrTotalVendedores);
										
				foreach($arrTotalVendedores as $key=>$total){
					if(!$key){
						$key = "Desconhecido";
					}
					$tableVendedores .= "<tr><td>".$key."</td><td>".$total."</td></tr>";
				}

				$tableVendedores .= "</table>";
			}
///////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////Total Origem////////////////////////////////////////////////
			$tableOrigem = "";
			if($arrTotalOrigem && !$this->_getParam('origem')){
				$tableOrigem = "<table class='table'>
										<tr>
											<th style='background-color:#777;' colspan='2'>ORIGEM</th>
										</tr>
										<tr>
											<th>Origem</th>
											<th>Total</th>
										</tr>";
										
				arsort($arrTotalOrigem);
										
				foreach($arrTotalOrigem as $key=>$total){
					if(!$key){
						$key = "Desconhecida";
					}
					$tableOrigem .= "<tr><td>".$key."</td><td>".$total."</td></tr>";
				}

				$tableOrigem .= "</table>";
			}
////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////Total Resultados////////////////////////////////////////////////			
			$tableResultado = "";
			if($arrTotalResultado && !$this->_getParam('resultado')){
				$tableResultado = "<table class='table'>
										<tr>
											<th style='background-color:#777;' colspan='2'>RESULTADO</th>
										</tr>
										<tr>
											<th>Resultado</th>
											<th>Total</th>
										</tr>
										";
										
				arsort($arrTotalResultado);
										
				foreach($arrTotalResultado as $key=>$total){
					if(!$key){
						$key = "Não selecionado";
					}
					$tableResultado .= "<tr><td>".$key."</td><td>".$total."</td></tr>";
				}

				$tableResultado .= "</table>";
			}
////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////Total Motivos////////////////////////////////////////////////			
			$tableMotivos = "";
			if($arrTotalMotivos && !$this->_getParam('motivo_pre')){
				$tableMotivos = "<table class='table'>
										<tr>
											<th style='background-color:#777;' colspan='2'>MOTIVO</th>
										</tr>
										<tr>
											<th>Motivo</th>
											<th>Total</th>
										</tr>
										";
										
				arsort($arrTotalMotivos);
										
				foreach($arrTotalMotivos as $key=>$total){
					if(!$key){
						$key = "Desconhecido";
					}
					$tableMotivos .= "<tr><td>".$key."</td><td>".$total."</td></tr>";
				}

				$tableMotivos .= "</table>";
			}
////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////Total Contato////////////////////////////////////////////////			
			$tableContatos = "";
			if($arrTotalContato){
				$tableContatos = "<table class='table'>
										<tr>
											<th style='background-color:#777;' colspan='2'>FORMA DE CONTATO</th>
										</tr>
										<tr>
											<th>Forma</th>
											<th>Total</th>
										</tr>
										";
										
				arsort($arrTotalContato);
										
				foreach($arrTotalContato as $key=>$total){
					if(!$key){
						$key = "Desconhecido";
					}
					$tableContatos .= "<tr><td>".$key."</td><td>".$total."</td></tr>";
				}

				$tableContatos .= "</table>";
			}
////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////Total Sites////////////////////////////////////////////////			
			$tableSites = "";
			if($arrTotalSites){
				$tableSites = "<table class='table'>
										<tr>
											<th style='background-color:#777;' colspan='2'>SITES DE ANÚCIOS</th>
										</tr>
										<tr>
											<th>Site</th>
											<th>Total</th>
										</tr>
										";
										
				arsort($arrTotalSites);
	
				foreach($arrTotalSites as $key=>$total){
					if(!$key){
						$key = "Desconhecido";
					}
					$tableSites .= "<tr><td>".$key."</td><td>".$total."</td></tr>";
				}

				$tableSites .= "</table>";
			}
////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////Total Veículo////////////////////////////////////////////////			
			/*
			$tableVeiculos = "";
			if(false){
			//if($arrTotalVeiculo){
				$tableVeiculos = "<table class='table'>
										<tr>
											<th style='background-color:#777;' colspan='2'>VEÍCULO DESEJADO</th>
										</tr>
										<tr>
											<th>Modelo</th>
											<th>Total</th>
										</tr>
										";
										
				arsort($arrTotalVeiculo);
										
				foreach($arrTotalVeiculo as $key=>$total){
					if($key == ""){
						$key = "Desconhecido";
					}
					$tableVeiculos .= "<tr><td>".$key."</td><td>".$total."</td></tr>";
				}

				$tableVeiculos .= "</table>";
			}
			*/
////////////////////////////////////////////////////////////////////////////////////////////

/////////////////////////////Total Veículo Horizontal////////////////////////////////////////////////			
			$tableVeiculos = "";
			$countTable = 0;
			if($arrTotalVeiculo){
				$tableVeiculos = "<table width=100%; class='table'>
										<tr>
											<th style='background-color:#777;' colspan='14'>VEÍCULO DESEJADO</th>
										</tr>";
										
				$tableVeiculos .= "<tr>";
				
				for($i=0;$i<7;$i++){

					$tableVeiculos .= "<th>Modelo</th><th>Total</th>";
				}
				
				$tableVeiculos .= "</tr>";

				arsort($arrTotalVeiculo);

				foreach($arrTotalVeiculo as $key=>$total){
					
					$countTable++;
					
					if($key == ""){
						$key = "Desconhecido";
					}
					
					if($countTable == 1){
						$tableVeiculos .= "<tr>";
					}
					
					$tableVeiculos .= "<td>".$key."</td><td>".$total."</td>";
					
					if($countTable == 7){
						$tableVeiculos .= "</tr>";
						$countTable = 0;
					}

				}

				$tableVeiculos .= "</table>";

			}
////////////////////////////////////////////////////////////////////////////////////////////
				
			echo "<div style='font-weight:bold; margin-left:5px;'>Total de clientes: ".$cont."</div><table width='100%'><tr><td>".$tableVendedores."</td><td>".$tableSites."</td><td>".$tableOrigem."</td><td>".$tableResultado."</td><td>".$tableMotivos."</td><td>".$tableContatos."</td></tr></table>".$tableVeiculos.$strFluxo;
		
		}elseif($this->_getParam('fn') == 'relatorio_contatos'){
			
			$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
			$dbVeiculosFluxo = new Application_Model_DbTable_VeiculosFluxo();
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
			$style = "";
			

			if($_SESSION['sessionUser']['id_perfil'] != 2 && $_SESSION['sessionUser']['id_perfil'] != 4 && $_SESSION['sessionUser']['id_perfil'] != 9){
				
				$dados['vendedor'] = $_SESSION['sessionUser']['id'];

			}
			
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

			$dados['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$dados['nome'] = $this->_getParam('nome');
			$dados['veiculo'] = $this->_getParam('veiculo');
			$dados['data_inicial'] = implode("-",array_reverse(explode("/",$this->_getParam('data_inicial'))));
			$dados['data_final'] = implode("-",array_reverse(explode("/",$this->_getParam('data_final'))));
			
			
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
							<th>Nome</th>
							<th>E-mail</th>
							<th>Telefone</th>
							<th>Origem</th>
							<th>Data</th>
							<th>Resultado</th>
							<th>Vendedor</th>
							<th>Valor</th>
							<th colspan='2'>Veículo</th>
						</tr>";
			
			$cont = 0;
			$temEmail = false;
			
			foreach($arrFluxoCliente as $fluxoCliente){
				
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

				$arrVendedor = explode(" ", $arrUsuario[0]['nome']);

				$arrUsuario[0]['nome'] = $arrVendedor[0];
				if(isset($arrVendedor[1])){
					$arrUsuario[0]['nome'] = $arrVendedor[0]." ".$arrVendedor[1];
				}
				if(isset($arrVendedor[2])){
					$arrUsuario[0]['nome'] = $arrVendedor[0]." ".$arrVendedor[1]." ".$arrVendedor[2];
				}
				
				
				$strFluxo .= "<tr ".$color.">
								<td style='border-right:0px;' rowspan='".$contSpan."'>".$fluxoCliente['nome']."</td>
								<td rowspan='".$contSpan."'>".$fluxoCliente['email']."</td>
								<td rowspan='".$contSpan."'>".$fluxoCliente['telefone']."</td>
								<td rowspan='".$contSpan."'>".$fluxoCliente['origem']."</td>
								<td rowspan='".$contSpan."'>".implode("/",array_reverse(explode("-",current(explode(" ",$fluxoCliente['data'])))))."</td>
								<td class='resultado_motivo' ".$style." rowspan='".$contSpan."' ".$titleMotivo.">".$fluxoCliente['resultado']."</td>
								<td rowspan='".$contSpan."'>".$arrUsuario[0]['nome']."</td>
								<td rowspan='".$contSpan."'>R$ ".money_format("%i",$fluxoCliente['valor'])."</td>
								".$strVeiculo."
							  </tr>";
			
			}
				
			echo $strFluxo;
			
		
		}elseif($this->_getParam('fn') == 'preparacao'){
			
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
		
			$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$arr['vendido'] = 0;
			
			if($this->_getParam('servico')){
				$arr['servico'] = $this->_getParam('servico');
			}else{
				$arr['servico'] = "";
			}
			
			if($this->_getParam('id_veiculo')){
				
				$arr['id_veiculo'] = $this->_getParam('id_veiculo');
				
			}
			
			$arrVeiculos = $dbVeiculos->getVeiculoEstoquePreparacao($arr);

			$strTable = "<table class='table'>
							<tr>
								<th>Veículo</th>
								<th>Placa</th>
								<th>Serviços</th>
								<th>Observações</th>
							</tr>";
			
			foreach($arrVeiculos as $veiculo){
				
				$count = 0;
				$strServico1 = "";
				$strServico = "";
				$arrServico1 = array();
				
				if($veiculo['descricao_site']){
					$veiculo['modelo'] = $veiculo['descricao_site'];
				}
				
				
				
				if(($veiculo['mecanico'] == 0 || $veiculo['mecanico'] == 3 || $veiculo['mecanico'] == 4) && ($arr['servico'] == "mecanico" || $arr['servico'] == "" )){
					$count++;
					
					if($veiculo['mecanico'] == 0){
						$style = "style='background-color:yellow; color:#777;'";
					}
					
					if($veiculo['mecanico'] == 3){
						$style = "style='background-color:red; color:#FFF;'";
					}
					
					if($veiculo['mecanico'] == 4){
						$style = "style='background-color:blue; color:#FFF;'";
					}
					
					if($count == 1){
						$arrServico1['modelo'] = $veiculo['modelo'];
						$arrServico1['placa'] = $veiculo['placa'];
						$arrServico1['servico'] = "<td ".$style.">Mecânico</td><td ".$style.">".$veiculo['mecanico_obs']."</td>";
					}else{
						$strServico .= "<tr><td ".$style.">Mecânico</td><td ".$style.">".$veiculo['mecanico_obs']."</td></tr>";
					}
				}
		
		
		
				if(($veiculo['funilaria'] == 0 || $veiculo['funilaria'] == 3 || $veiculo['funilaria'] == 4) && ($arr['servico'] == "funilaria" || $arr['servico'] == "" )){
					$count++;
					
					if($veiculo['funilaria'] == 0){
						$style = "style='background-color:yellow; color:#777;'";
					}
					
					if($veiculo['funilaria'] == 3){
						$style = "style='background-color:red; color:#FFF;'";
					}
					
					if($veiculo['funilaria'] == 4){
						$style = "style='background-color:blue; color:#FFF;'";
					}
					
					if($count == 1){
						$arrServico1['modelo'] = $veiculo['modelo'];
						$arrServico1['placa'] = $veiculo['placa'];
						$arrServico1['servico'] = "<td ".$style.">Funilaria</td><td ".$style.">".$veiculo['funilaria_obs']."</td>";
					}else{
						$strServico .= "<tr><td ".$style.">Funilaria</td><td ".$style.">".$veiculo['funilaria_obs']."</td></tr>";
					}
				}
				
				
				
				if(($veiculo['martelinho'] == 0 || $veiculo['martelinho'] == 3 || $veiculo['martelinho'] == 4) && ($arr['servico'] == "martelinho" || $arr['servico'] == "" )){
					$count++;
					
					if($veiculo['martelinho'] == 0){
						$style = "style='background-color:yellow; color:#777;'";
					}
					
					if($veiculo['martelinho'] == 3){
						$style = "style='background-color:red; color:#FFF;'";
					}
					
					if($veiculo['martelinho'] == 4){
						$style = "style='background-color:blue; color:#FFF;'";
					}
					
					if($count == 1){
						$arrServico1['modelo'] = $veiculo['modelo'];
						$arrServico1['placa'] = $veiculo['placa'];
						$arrServico1['servico'] = "<td ".$style.">Martelinho</td><td ".$style.">".$veiculo['martelinho_obs']."</td>";
					}else{
						$strServico .= "<tr><td ".$style.">Martelinho</td><td ".$style.">".$veiculo['martelinho_obs']."</td></tr>";
					}
				}
				
				
				if(($veiculo['eletrica'] == 0 || $veiculo['eletrica'] == 3 || $veiculo['eletrica'] == 4) && ($arr['servico'] == "eletrica" || $arr['servico'] == "" )){
					$count++;
					
					if($veiculo['eletrica'] == 0){
						$style = "style='background-color:yellow; color:#777;'";
					}
					
					if($veiculo['eletrica'] == 3){
						$style = "style='background-color:red; color:#FFF;'";
					}
					
					if($veiculo['eletrica'] == 4){
						$style = "style='background-color:blue; color:#FFF;'";
					}
					
					if($count == 1){
						$arrServico1['modelo'] = $veiculo['modelo'];
						$arrServico1['placa'] = $veiculo['placa'];
						$arrServico1['servico'] = "<td ".$style.">Elétrica</td><td ".$style.">".$veiculo['eletrica_obs']."</td>";
					}else{
						$strServico .= "<tr><td ".$style.">Elétrica</td><td ".$style.">".$veiculo['eletrica_obs']."</td></tr>";
					}
				}
				
				
				
				if(($veiculo['tapecaria'] == 0 || $veiculo['tapecaria'] == 3 || $veiculo['tapecaria'] == 4) && ($arr['servico'] == "tapecaria" || $arr['servico'] == "" )){
					$count++;
					
					if($veiculo['tapecaria'] == 0){
						$style = "style='background-color:yellow; color:#777;'";
					}
					
					if($veiculo['tapecaria'] == 3){
						$style = "style='background-color:red; color:#FFF;'";
					}
					
					if($veiculo['tapecaria'] == 4){
						$style = "style='background-color:blue; color:#FFF;'";
					}
					
					if($count == 1){
						$arrServico1['modelo'] = $veiculo['modelo'];
						$arrServico1['placa'] = $veiculo['placa'];
						$arrServico1['servico'] = "<td ".$style.">Tapeçaria</td><td ".$style.">".$veiculo['tapecaria_obs']."</td>";
					}else{
						$strServico .= "<tr><td ".$style.">Tapeçaria</td><td ".$style.">".$veiculo['tapecaria_obs']."</td></tr>";
					}
				}
				
				
				
				if(($veiculo['lavacar'] == 0 || $veiculo['lavacar'] == 3 || $veiculo['lavacar'] == 4) && ($arr['servico'] == "lavacar" || $arr['servico'] == "" )){
					$count++;
					
					if($veiculo['lavacar'] == 0){
						$style = "style='background-color:yellow; color:#777;'";
					}
					
					if($veiculo['lavacar'] == 3){
						$style = "style='background-color:red; color:#FFF;'";
					}
					
					if($veiculo['lavacar'] == 4){
						$style = "style='background-color:blue; color:#FFF;'";
					}
					
					if($count == 1){
						$arrServico1['modelo'] = $veiculo['modelo'];
						$arrServico1['placa'] = $veiculo['placa'];
						$arrServico1['servico'] = "<td ".$style.">Lavacar</td><td ".$style.">".$veiculo['lavacar_obs']."</td>";
					}else{
						$strServico .= "<tr><td ".$style.">Lavacar</td><td ".$style.">".$veiculo['lavacar_obs']."</td></tr>";
					}
				}
				
				
				if(($veiculo['volante'] == 0 || $veiculo['volante'] == 3 || $veiculo['volante'] == 4) && ($arr['servico'] == "volante" || $arr['servico'] == "" )){
					$count++;
					
					if($veiculo['volante'] == 0){
						$style = "style='background-color:yellow; color:#777;'";
					}
					
					if($veiculo['volante'] == 3){
						$style = "style='background-color:red; color:#FFF;'";
					}
					
					if($veiculo['volante'] == 4){
						$style = "style='background-color:blue; color:#FFF;'";
					}
					
					if($count == 1){
						$arrServico1['modelo'] = $veiculo['modelo'];
						$arrServico1['placa'] = $veiculo['placa'];
						$arrServico1['servico'] = "<td ".$style.">Volante</td><td ".$style.">".$veiculo['volante_obs']."</td>";
					}else{
						$strServico .= "<tr><td ".$style.">Volante</td><td ".$style.">".$veiculo['volante_obs']."</td></tr>";
					}
				}
				
				
				if(($veiculo['calotas'] == 0 || $veiculo['calotas'] == 3 || $veiculo['calotas'] == 4) && ($arr['servico'] == "calotas" || $arr['servico'] == "" )){
					$count++;
					
					if($veiculo['calotas'] == 0){
						$style = "style='background-color:yellow; color:#777;'";
					}
					
					if($veiculo['calotas'] == 3){
						$style = "style='background-color:red; color:#FFF;'";
					}
					
					if($veiculo['calotas'] == 4){
						$style = "style='background-color:red; color:#FFF;'";
					}
					
					if($count == 1){
						$arrServico1['modelo'] = $veiculo['modelo'];
						$arrServico1['placa'] = $veiculo['placa'];
						$arrServico1['servico'] = "<td ".$style.">Calotas</td><td ".$style.">".$veiculo['calotas_obs']."</td>";
					}else{
						$strServico .= "<tr><td ".$style.">Calotas</td><td ".$style.">".$veiculo['calotas_obs']."</td></tr>";
					}
				}
				
				
				
				if(($veiculo['pneus'] == 0 || $veiculo['pneus'] == 3 || $veiculo['pneus'] == 4) && ($arr['servico'] == "pneus" || $arr['servico'] == "" )){
					$count++;
					
					if($veiculo['pneus'] == 0){
						$style = "style='background-color:yellow; color:#777;'";
					}
					
					if($veiculo['pneus'] == 3){
						$style = "style='background-color:red; color:#FFF;'";
					}
					
					if($veiculo['pneus'] == 4){
						$style = "style='background-color:blue; color:#FFF;'";
					}
					
					if($count == 1){
						$arrServico1['modelo'] = $veiculo['modelo'];
						$arrServico1['placa'] = $veiculo['placa'];
						$arrServico1['servico'] = "<td ".$style.">Pneus</td><td ".$style.">".$veiculo['pneus_obs']."</td>";
					}else{
						$strServico .= "<tr><td ".$style.">Pneus</td><td ".$style.">".$veiculo['pneus_obs']."</td></tr>";
					}
				}
				
				
				
				if(($veiculo['chaveiro'] == 0 || $veiculo['chaveiro'] == 3 || $veiculo['chaveiro'] == 4) && ($arr['servico'] == "chaveiro" || $arr['servico'] == "" )){
					$count++;
					
					if($veiculo['chaveiro'] == 0){
						$style = "style='background-color:yellow; color:#777;'";
					}
					
					if($veiculo['chaveiro'] == 3){
						$style = "style='background-color:red; color:#FFF;'";
					}
					
					if($veiculo['chaveiro'] == 4){
						$style = "style='background-color:blue; color:#FFF;'";
					}
					
					if($count == 1){
						$arrServico1['modelo'] = $veiculo['modelo'];
						$arrServico1['placa'] = $veiculo['placa'];
						$arrServico1['servico'] = "<td ".$style.">Chaveiro</td><td ".$style.">".$veiculo['chaveiro_obs']."</td>";
					}else{
						$strServico .= "<tr><td ".$style.">Chaveiro</td><td ".$style.">".$veiculo['chaveiro_obs']."</td></tr>";
					}
				}
				
				
				
				if(($veiculo['pincelar'] == 0 || $veiculo['pincelar'] == 3 || $veiculo['pincelar'] == 4) && ($arr['servico'] == "pincelar" || $arr['servico'] == "" )){
					$count++;
					
					if($veiculo['pincelar'] == 0){
						$style = "style='background-color:yellow; color:#777;'";
					}
					
					if($veiculo['pincelar'] == 3){
						$style = "style='background-color:red; color:#FFF;'";
					}
					
					if($veiculo['pincelar'] == 4){
						$style = "style='background-color:blue; color:#FFF;'";
					}
					
					if($count == 1){
						$arrServico1['modelo'] = $veiculo['modelo'];
						$arrServico1['placa'] = $veiculo['placa'];
						$arrServico1['servico'] = "<td ".$style.">Pincelar</td><td ".$style.">".$veiculo['pincelar_obs']."</td>";
					}else{
						$strServico .= "<tr><td ".$style.">Pincelar</td><td ".$style.">".$veiculo['pincelar_obs']."</td></tr>";
					}
				}
				
				
				if(($veiculo['outros'] == 0 || $veiculo['outros'] == 3 || $veiculo['outros'] == 4) && ($arr['servico'] == "outros" || $arr['servico'] == "" )){
					$count++;
					
					if($veiculo['outros'] == 0){
						$style = "style='background-color:yellow; color:#777;'";
					}
					
					if($veiculo['outros'] == 3){
						$style = "style='background-color:red; color:#FFF;'";
					}
					
					if($veiculo['outros'] == 4){
						$style = "style='background-color:blue; color:#FFF;'";
					}
					
					if($count == 1){
						$arrServico1['modelo'] = $veiculo['modelo'];
						$arrServico1['placa'] = $veiculo['placa'];
						$arrServico1['servico'] = "<td ".$style.">Outros</td><td ".$style.">".$veiculo['outros_obs']."</td>";
					}else{
						$strServico .= "<tr><td ".$style.">Outros</td><td ".$style.">".$veiculo['outros_obs']."</td></tr>";
					}
				}
				
				if(!isset($arrServico1['modelo'])){
					$arrServico1['modelo'] = "";
				}
				if(!isset($arrServico1['placa'])){
					$arrServico1['placa'] = "";
				}
				if(!isset($arrServico1['servico'])){
					$arrServico1['servico'] = "";
				}

				$strServico1 = "<tr><td rowspan='".$count."'>".$arrServico1['modelo']."</td><td rowspan='".$count."'>".$arrServico1['placa']."</td>".$arrServico1['servico']."</tr>";

				if($count != 0){
				
					$strTable .= $strServico1.$strServico;
				
				}
				
			}
			
			echo $strTable."</table>";
			
		}elseif($this->_getParam('fn') == 'origem_vendas'){
			
			$arrFiltro['id_origem'] = $this->_getParam('id_origem');
			$arrFiltro['aprovado'] = $this->_getParam('aprovado');
			$arrFiltro['data_inicial'] = implode("-",array_reverse(explode("/",$this->_getParam('data_inicial'))));
			$arrFiltro['data_final'] = implode("-",array_reverse(explode("/",$this->_getParam('data_final'))));

			if($this->_getParam('aprovado') != -1){
				$arrFiltro['gerado_negociacao'] = true;
				$arrFiltro['resultado'] = "Fechou";
			}
			
			$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
			
			$arr = $dbNegociacoes->_getPorOrigem($arrFiltro);

			if($arr){

			$strTabela = "<table class='table'>
						  <tr>
							  <th>Origem</th>
							  <th>Cliente</th>
							  <th>Bairro</th>
							  <th>Cidade</th>
							  <th>Vendedor</th>
						  </tr>
						  ";
						  
			$countRow = 0;
			$qtd = 0;
			$strRelatorio = "";
			
			foreach($arr as $key=>$ar){

				
				$countRow++;
				
				if($ar['origem'] == 0){
					if($ar['origem2']){
						$ar['origem'] = $ar['origem2'];
					}else{
						$ar['origem'] = 0;
					}
				}
				
				if(!isset($arr[$key+1]['origem']) || ($arr[$key+1]['origem'] != $ar['origem'])){

					if($ar['descricao'] == ""){
						if($ar['descricao2']){
							$ar['descricao'] = $ar['descricao2'];
						}else{
							$ar['descricao'] = "Desconhecida";
						}
					}
	
					$strTabelaLinhaUm = "<tr><td rowspan='".$countRow."'>".$ar['descricao']."</td><td>".$ar['nome']."</td><td>".$ar['bairro']."</td><td>".$ar['cidade']."</td><td>".$ar['nome_vendedor']."</td></tr>";
					$strTabela .= $strTabelaLinhaUm.$strRelatorio;
					$strRelatorio = "";
					$countRow = 0;
					
				}else{
					
					$strRelatorio .= "<tr><td>".$ar['nome']."</td><td>".$ar['bairro']."</td><td>".$ar['cidade']."</td><td>".$ar['nome_vendedor']."</td></tr>";
					
				}
				
				$qtd++;
				$arrTotal[$ar['origem']]['descricao'] = $ar['descricao'];
				if(!isset($arrTotal[$ar['origem']]['qtd'])){
					$arrTotal[$ar['origem']]['qtd'] = 0;
				}
				$arrTotal[$ar['origem']]['qtd']++;
				
				$arrTotalVendedor[$ar['id_vendedor']]['nome_vendedor'] = $ar['nome_vendedor'];
				if(!isset($arrTotalVendedor[$ar['id_vendedor']]['qtd'])){
					$arrTotalVendedor[$ar['id_vendedor']]['qtd'] = 0;
				}
				$arrTotalVendedor[$ar['id_vendedor']]['qtd']++;
				
				$arrTotalBairro[$ar['bairro']]['nome_bairro'] = $ar['bairro'];
				if(!isset($arrTotalBairro[$ar['bairro']]['qtd'])){
					$arrTotalBairro[$ar['bairro']]['qtd'] = 0;
				}
				$arrTotalBairro[$ar['bairro']]['qtd']++;
				
				$arrTotalCidade[$ar['cidade']]['nome_cidade'] = $ar['cidade'];
				if(!isset($arrTotalCidade[$ar['cidade']]['qtd'])){
					$arrTotalCidade[$ar['cidade']]['qtd'] = 0;
				}
				$arrTotalCidade[$ar['cidade']]['qtd']++;

			}
			
			foreach($arrTotal as $key=>$total){
				
				$arrTotal[$key]['porcento'] = round(($total['qtd']/$qtd)*100,2);
				
			}
			
			foreach($arrTotalVendedor as $key=>$totalV){
				
				$arrTotalVendedor[$key]['porcento'] = round(($totalV['qtd']/$qtd)*100,2);
				
			}
			
			foreach($arrTotalBairro as $key=>$totalV){
				
				$arrTotalBairro[$key]['porcento'] = round(($totalV['qtd']/$qtd)*100,2);
				
			}
			
			foreach($arrTotalCidade as $key=>$totalV){
				
				$arrTotalCidade[$key]['porcento'] = round(($totalV['qtd']/$qtd)*100,2);
				
			}
			
			
			
			$strTabelaTotal = "<table class='table' style='width:35%;'>
							   <tr>
									<th>Origem de Vendas</th>
									<th>Qtd</th>
									<th>%</th>
							   </tr>";
							   
							   
			foreach($arrTotal as $arTotal){
				
				$strTabelaTotal .= "<tr><td>".$arTotal['descricao']."</td>
										<td>".$arTotal['qtd']."</td>
										<td>".$arTotal['porcento']."%</td></tr>";
				
			}
			
			$strTabelaTotal .= "<tr>
									<th>Vendedor</th>
									<th>Qtd</th>
									<th>%</th>
							   </tr>";
							   
							   
			foreach($arrTotalVendedor as $arTotal){
				
				$strTabelaTotal .= "<tr><td>".$arTotal['nome_vendedor']."</td>
										<td>".$arTotal['qtd']."</td>
										<td>".$arTotal['porcento']."%</td></tr>";
				
			}
			
			
			$strTabelaTotal .= "<tr>
									<th>Bairro</th>
									<th>Qtd</th>
									<th>%</th>
							   </tr>";
							   
							   
			foreach($arrTotalBairro as $arTotal){
				
				$strTabelaTotal .= "<tr><td>".$arTotal['nome_bairro']."</td>
										<td>".$arTotal['qtd']."</td>
										<td>".$arTotal['porcento']."%</td></tr>";
				
			}
			
			$strTabelaTotal .= "<tr>
									<th>Cidade</th>
									<th>Qtd</th>
									<th>%</th>
							   </tr>";
							   
							   
			foreach($arrTotalCidade as $arTotal){
				
				$strTabelaTotal .= "<tr><td>".$arTotal['nome_cidade']."</td>
										<td>".$arTotal['qtd']."</td>
										<td>".$arTotal['porcento']."%</td></tr>";
				
			}
			
			
			$strTabelaTotal .= "<tr>
									<th>Total de origem de vendas</th>
									<th colspan='2'>".$qtd."</th>
							   </tr>";
							   
			
			echo $strTabela."</table>";
			echo $strTabelaTotal."</table>";

		}else{
			echo "Não há dados para serem mostrados com as características buscadas.";
		}

		}elseif($this->_getParam('fn') == 'folha_pagamento'){
		
			$dbUsuarios = new Application_Model_DbTable_Usuarios();
			$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
			
			$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$arr['id_perfil'] = $this->_getParam('id_perfil');
			$arr['id_usuario'] = $this->_getParam('id_usuario');
			$arr['data_inicial'] = implode("-",array_reverse(explode("/",$this->_getParam('data_inicial'))));
			$arr['data_final'] = implode("-",array_reverse(explode("/",$this->_getParam('data_final'))));
			$arr['order_perfil'] = true;
			$arr['excluido'] = 0;


			$arrFiltroVendas['data_inicial_concretizacao'] = implode("-",array_reverse(explode("/",$this->_getParam('data_inicial'))));
			$arrFiltroVendas['data_final_concretizacao'] = implode("-",array_reverse(explode("/",$this->_getParam('data_final'))));
			$arrFiltroVendas['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

			$totalComissaoSobreVenda = 0;
			
			$arrUsuarios = $dbUsuarios->usuariosNegociacoes($arr);

			$colunaSupervisor = true;
			if(count($arrUsuarios) == 1){
				if($arrUsuarios[0]['id_perfil'] == 3){
					$colunaSupervisor = false;
				}
			}
					
			
			$tabelaCabeca = "<table class='funcionario'>";
			$tabelaCabeca .= "<tr>
									<th>Funcionários</th>
									<th>Cargo</th>
									<th>Fixo</th>
									<th colspan='2'>Veículo</th>
									<th>Placa</th>
									<th>Valor Venda</th>
									<th>Comissões Venda</th>
									<th>Retorno</th>
									<th>Comissões Retorno</th>
									".($colunaSupervisor ? "<th>Comissões Supervisor</th>" : "")."
									<th>Total</th>
								</tr>";

			$totalFixo = 0;
			$comissaoSobreVenda = 0;
			$totalVenda = 0;
			$totalComissao = 0;
			$totalRetorno = 0;
			$totalComissaoRetorno = 0;
			$cont = 0;
			
			foreach($arrUsuarios as $key=>$usuarios){

				if($usuarios['id_perfil'] == 3){
				
					$arrFiltroVendas['id_vendedor'] = $usuarios['id'];
				
				}elseif($usuarios['id_perfil'] == 9){

					$comissaoSobreVenda = 0;

					$arrFiltroVendas['id_supervisor'] = $usuarios['id'];

					$arrVendas = $dbNegociacoes->getVendasPorUsuario($arrFiltroVendas);

					unset($arrFiltroVendas['id_supervisor']);

					foreach ($arrVendas as $arVenda) {
						$comissaoSobreVenda += $arVenda['comissao_supervisor'];
					}


					$arrFiltroVendas['id_vendedor'] = $usuarios['id'];

				/// Foi comentado dia 11/08/2025, pois não estava trazendo as vendas na folha de pagamento do Rafael Martho Flumian da basson(979) ///
				/// Esse caso o id de Gerente estava em nome de outra pessoa Thais Romano(896) ///
				// }elseif($usuarios['id_perfil'] == 4){
				
				// 	$arrFiltroVendas['id_gerente'] = $usuarios['id'];
				
				}else{
					
					$arrFiltroVendas['id_vendedor'] = $usuarios['id'];
					
				}
				
				$contador = 1;
				
				$arrVendas = $dbNegociacoes->getVendasPorUsuario($arrFiltroVendas);
				
				unset($arrFiltroVendas['id_vendedor']);
				unset($arrFiltroVendas['id_supervisor']);
				unset($arrFiltroVendas['id_gerente']);
				
				$totalFixo += $usuarios['valor_fixo_mensal'];

				$totalComissaoSobreVenda += $comissaoSobreVenda;

                $primeiroTable = '';
				
				if($arrVendas){
					
					$subtotalVenda = 0;
					$subtotalComissao = 0;
					$subtotalRetorno = 0;
					$subtotalComissaoRetorno = 0;
					$totalSobreVendaSupervisor = 0;
					
					$rowspan = count($arrVendas)+1;
					
					if(count($arrUsuarios) == 1){
						
						$cont = -1;

					}
					
					foreach($arrVendas as $chave=>$vendas){
					
						$retorno = 0;
						$comissaoRetorno = 0;
						$comissaoVenda = 0;
						
						if($vendas['descricao_site']){
							
							$vendas['modelo'] = $vendas['descricao_site'];
						
						}
						
						$arrModelo = explode(" ",$vendas['modelo']);

						if(isset($arrModelo[1])){
							$vendas['modelo'] = $arrModelo[0]." ".$arrModelo[1];
						}else{
							$vendas['modelo'] = $arrModelo[0];
						}
						
						if($usuarios['id_perfil'] == 3){
				
							$comissaoVenda = $vendas['comissao_vendedor'];
						
						}elseif($usuarios['id_perfil'] == 9){
						
							$comissaoVenda = $vendas['comissao_supervisor'];
						
						}elseif($usuarios['id_perfil'] == 4){
						
							$comissaoVenda = $vendas['comissao_gerente'];
						
						}
                        else{
							
                            
							$comissaoVenda = 0;							
						}

                        if (isset($vendas['comissao_vendedor'])) 
                        {
                            $comissaoVenda = $vendas['comissao_vendedor'] ?? $usuarios['valor_fixo'];
                        }
                        
                        
                         if(!$comissaoVenda){
							
							$comissaoVenda = $usuarios['valor_fixo'];
						
						}
						
						if($vendas['valor_financiado'] != 0){
						
							$retorno = ((($vendas['valor_financiado']*1.2)*$vendas['retorno_financeira'])/100) - $vendas['imposto_financeira'];
							$comissaoRetorno = ((((($vendas['valor_financiado']*1.2)*$vendas['retorno_financeira'])/100) - $vendas['imposto_financeira'])*$usuarios['percentual_retorno_financeiro'])/100;

						}

						$subtotalVenda += $vendas['valor_venda'];
						$subtotalComissao += $comissaoVenda;
						$subtotalRetorno += $retorno;
						$subtotalComissaoRetorno += $comissaoRetorno;

						$totalVenda += $vendas['valor_venda'];
						$totalComissao += $comissaoVenda;
						$totalRetorno += $retorno;
						$totalComissaoRetorno += $comissaoRetorno;

                        

						if($chave == 0){
							$primeiroTable = "<tr>
										<td rowspan='".($rowspan+$cont)."'>".$usuarios['nome']."</td>
										<td rowspan='".($rowspan+$cont)."'>".$usuarios['perfil']."</td>
										<td rowspan='".($rowspan-1)."'>R$ ".money_format("%i",$usuarios['valor_fixo_mensal'])."</td>
										<td>".$contador++."</td>
										<td>".$vendas['modelo']."</td>
										<td>".$vendas['placa']."</td>
										<td>R$ ".money_format("%i",$vendas['valor_venda'])."</td>
										<td>R$ ".money_format("%i",$comissaoVenda)."</td>
										<td>R$ ".money_format("%i",$retorno)."</td>
										<td>R$ ".money_format("%i",$comissaoRetorno)."</td>
										".($colunaSupervisor ? "<td style='color:#ccc;'>".($vendas['comissao_supervisor'] ? "R$ ".money_format("%i", $vendas['comissao_supervisor'])." (".current(explode(" ", $vendas['nomeSupervisor'])).")" : "")."</td>" : "")."
										<td>R$ ".money_format("%i",$comissaoRetorno+$comissaoVenda)."</td>
									  </tr>";
						
						}else{
							$primeiroTable .= "<tr>
												<td>".$contador++."</td>
												<td>".$vendas['modelo']."</td>
												<td>".$vendas['placa']."</td>
												<td>R$ ".money_format("%i",$vendas['valor_venda'])."</td>
												<td>R$ ".money_format("%i",$comissaoVenda)."</td>
												<td>R$ ".money_format("%i",$retorno)."</td>
												<td>R$ ".money_format("%i",$comissaoRetorno)."</td>
												".($colunaSupervisor ? "<td style='color:#ccc;'>".($vendas['comissao_supervisor'] ? "R$ ".money_format("%i", $vendas['comissao_supervisor'])." (".current(explode(" ", $vendas['nomeSupervisor'])).")" : "")."</td>" : "")."
												<td>R$ ".money_format("%i",$comissaoRetorno+$comissaoVenda)."</td>
											 </tr>";
										 
						}
						
						$arrContVeiculos[$vendas['id_veiculo']] = 1;
						
					}
					
					if(count($arrUsuarios) > 1){

						$primeiroTable .= "<tr style='background-color:#ccc;'>
												<td colspan='4' style='text-align:right;'><b>Subtotal</b></td>
												<td>R$ ".money_format("%i",$subtotalVenda)."</td>
												<td>R$ ".money_format("%i",$subtotalComissao)."</td>
												<td>R$ ".money_format("%i",$subtotalRetorno)."</td>
												<td>R$ ".money_format("%i",$subtotalComissaoRetorno)."</td>
												".($colunaSupervisor ? "<td>".($comissaoSobreVenda ? 'R$ '.money_format("%i", $comissaoSobreVenda) : '')."</td>" : "")."
												<td>R$ ".money_format("%i",$comissaoSobreVenda+$subtotalComissaoRetorno+$subtotalComissao+$usuarios['valor_fixo_mensal'])."</td>
										   </tr>";
									   
					}
				
				}else{
				
					$primeiroTable = "<tr style='background-color:#ccc;'>
										<td>".$usuarios['nome']."</td>
										<td>".$usuarios['perfil']."</td>
										<td>R$ ".money_format("%i",$usuarios['valor_fixo_mensal'])."</td>
										<td colspan='3' style='text-align:right;'><b>Subtotal</b></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										".($colunaSupervisor ? "<td>".($comissaoSobreVenda ? 'R$ '.money_format("%i", $comissaoSobreVenda) : '')."</td>" : "")."
										<td>R$ ".money_format("%i", $usuarios['valor_fixo_mensal']+$comissaoSobreVenda)."</td>
									  </tr>";
					
				}
				
				$tabelaCabeca .= $primeiroTable;
				
			}
			
			$totalTable = "<tr style='background-color:#ccc;'>
											<td colspan='2' style='text-align:right;'><b>TOTAL</b></td>
											<td><b>R$ ".money_format("%i",$totalFixo)."</b></td>
											<td  colspan='3'><b>".count($arrContVeiculos)." Ve&iacute;culos Vendidos</b></td>
											<td><b>R$ ".money_format("%i",$totalVenda)."</b></td>
											<td><b>R$ ".money_format("%i",$totalComissao)."</b></td>
											<td><b>R$ ".money_format("%i",$totalRetorno)."</b></td>
											<td><b>R$ ".money_format("%i",$totalComissaoRetorno)."</b></td>
											".($colunaSupervisor ? "<td><b>R$ ".money_format("%i",$totalComissaoSobreVenda)."</b></td>" : "")."
											<td><b>R$ ".money_format("%i",$totalComissaoRetorno+$totalComissao+$totalFixo+$totalComissaoSobreVenda)."</b></td>
									   </tr>";
			
			echo $tabelaCabeca.$totalTable .= "</table>";
			
			//var_export($arrUsuarios);
			
		}elseif($this->_getParam('fn') == 'pesquisa_satisfacao'){
			
			$dbPesquisaSatisfacao = new Application_Model_DbTable_PesquisaSatisfacao();
			
			$arr['loja'] = $this->_getParam('loja');
			$arr['id_vendedor'] = $this->_getParam('id_usuario');
			$arr['dispositivo'] = $this->_getParam('dispositivo');
			
			$arr['data_inicial'] = implode("-", array_reverse(explode("/", $this->_getParam('data_inicial'))));
			$arr['data_final'] = implode("-", array_reverse(explode("/", $this->_getParam('data_final'))));

			$arrLoja = array();
			$arrPreparador = array();
			$arrVendedor = array();
			
			$arrPesquisaSatisfacao = $dbPesquisaSatisfacao->getPesquisas($arr);

			//var_export($arrPesquisaSatisfacao);
			
			$strTable = "<table class='table' style='width:100%;'>
							<tr>
								<th>Vendedor</th>
								<th>Loja</th>
								<th>Cliente</th>
								<th>Telefone</th>
								<th>Veículo</th>
								<th>Simpatia Vendedor</th>
								<th>Conhecimento do Vendedor do Veículo</th>
								<th>Limpeza do Veículo</th>
								<th>Conservação do Veículo</th>
								<th>Valor do Veículo</th>
								<th>Valor do Veículo de Entrada</th>
								<th>Limpeza da Loja</th>
								<th>Indicar Loja</th>
								<th>Dispositivo</th>
								<th>Data e Hora</th>
								<th>Comentário</th>
								<th>IP</th>
							</tr>";
							
			$strTableTotalVendedor = "<table class='table'>
								<tr>
									<th>Vendedor</th>
									<th>Nota</th>
								</tr>";
								
			$strTableTotalLoja = "<table class='table'>
								<tr>
									<th>Loja</th>
									<th>Nota</th>
								</tr>";
								
			$strTableTotalPreparador = "<table class='table'>
								<tr>
									<th>Preparador</th>
									<th>Nota</th>
								</tr>";
			
			$count = 0;
			
			foreach($arrPesquisaSatisfacao as $pesquisaSatisfacao){
				
				$cor = "";

				if($count%2 == 0){
					$cor = "style='background-color:#eee;'";
				}
				
				$strTable .= "<tr ".$cor.">
								<td>".$pesquisaSatisfacao['nome_vendedor']."</td>
								<td>".$pesquisaSatisfacao['loja']."</td>
								<td>".$pesquisaSatisfacao['nome']."</td>
								<td>".$pesquisaSatisfacao['telefone']."</td>
								<td>".$pesquisaSatisfacao['marca']."-".$pesquisaSatisfacao['modelo_descricao']."-".$pesquisaSatisfacao['ano_modelo']."</td>
								<td>".$pesquisaSatisfacao['nota_vendedor']."</td>
								<td>".$pesquisaSatisfacao['nota_vendedor_veiculo']."</td>
								<td>".$pesquisaSatisfacao['nota_limpeza_veiculo']."</td>
								<td>".$pesquisaSatisfacao['nota_conservacao_veiculo']."</td>
								<td>".$pesquisaSatisfacao['nota_valor_veiculo']."</td>
								<td>".$pesquisaSatisfacao['nota_valor_veiculo_entrada']."</td>
								<td>".$pesquisaSatisfacao['nota_limpeza_loja']."</td>
								<td>".$pesquisaSatisfacao['nota_indicar_loja']."</td>
								<td>".$pesquisaSatisfacao['dispositivo']."</td>
								<td>".implode('/',array_reverse(explode('-',current(explode(' ',$pesquisaSatisfacao['data_hora'])))))."&nbsp;".explode(' ',$pesquisaSatisfacao['data_hora'])[1]."</td>
								<td title='".$pesquisaSatisfacao['comentario']."'><img src='/images/info.png' width='25%'></td>
								<td>".$pesquisaSatisfacao['ip']."</td>
							</tr>";
				
				$count++;
							
				$arrVendedor[$pesquisaSatisfacao['id_vendedor']]['nome'] = $pesquisaSatisfacao['nome_vendedor'];
				
				if(!isset($arrVendedor[$pesquisaSatisfacao['id_vendedor']]['nota'])) {
					$arrVendedor[$pesquisaSatisfacao['id_vendedor']]['nota'] = 0;
				}
				if($pesquisaSatisfacao['nota_vendedor'] == "Ótimo"){
					$arrVendedor[$pesquisaSatisfacao['id_vendedor']]['nota'] += 3;
				}
				if($pesquisaSatisfacao['nota_vendedor'] == "Normal"){
					$arrVendedor[$pesquisaSatisfacao['id_vendedor']]['nota'] += 1;
				}
				// if($pesquisaSatisfacao['nota_vendedor'] == "Ruim"){
				// 	$arrVendedor[$pesquisaSatisfacao['id_vendedor']]['nota'] += 0;
				// }
				
				if($pesquisaSatisfacao['nota_vendedor_veiculo'] == "Ótimo"){
					$arrVendedor[$pesquisaSatisfacao['id_vendedor']]['nota'] += 3;
				}
				if($pesquisaSatisfacao['nota_vendedor_veiculo'] == "Normal"){
					$arrVendedor[$pesquisaSatisfacao['id_vendedor']]['nota'] += 1;
				}
				/*if($pesquisaSatisfacao['nota_vendedor_veiculo'] == "Ruim"){
					$arrVendedor[$pesquisaSatisfacao['id_vendedor']]['nota'] += 0;
				}*/
				
				
				if($pesquisaSatisfacao['loja'] != "Não"){
				
					$arrLoja[$pesquisaSatisfacao['loja']]['nome'] = $pesquisaSatisfacao['loja'];
					
					if(!isset($arrLoja[$pesquisaSatisfacao['loja']]['nota'])){
						$arrLoja[$pesquisaSatisfacao['loja']]['nota'] = 0;
					}
					if($pesquisaSatisfacao['nota_limpeza_loja'] == "Ótimo"){
						$arrLoja[$pesquisaSatisfacao['loja']]['nota'] += 3;
					}
					if($pesquisaSatisfacao['nota_limpeza_loja'] == "Normal"){
						$arrLoja[$pesquisaSatisfacao['loja']]['nota'] += 1;
					}
					if($pesquisaSatisfacao['nota_limpeza_loja'] == "Ruim"){
						$arrLoja[$pesquisaSatisfacao['loja']]['nota'] += 0;
					}
					

					if($pesquisaSatisfacao['nota_valor_veiculo'] == "Barato"){
						$arrLoja[$pesquisaSatisfacao['loja']]['nota'] += 3;
					}
					if($pesquisaSatisfacao['nota_valor_veiculo'] == "Justo"){
						$arrLoja[$pesquisaSatisfacao['loja']]['nota'] += 1;
					}
					/*if($pesquisaSatisfacao['nota_valor_veiculo'] == "Caro"){
						$arrLoja[$pesquisaSatisfacao['loja']]['nota'] += 0;
					}*/
					
					if($pesquisaSatisfacao['nota_valor_veiculo_entrada'] == "Boa"){
						$arrLoja[$pesquisaSatisfacao['loja']]['nota'] += 3;
					}
					if($pesquisaSatisfacao['nota_valor_veiculo_entrada'] == "Justo"){
						$arrLoja[$pesquisaSatisfacao['loja']]['nota'] += 1;
					}
					/*if($pesquisaSatisfacao['nota_valor_veiculo_entrada'] == "Baixa"){
						$arrLoja[$pesquisaSatisfacao['loja']]['nota'] += 0;
					}*/
					
					if($pesquisaSatisfacao['nota_indicar_loja'] == "Sim"){
						$arrLoja[$pesquisaSatisfacao['loja']]['nota'] += 3;
					}
					if($pesquisaSatisfacao['nota_indicar_loja'] == "Talvez"){
						$arrLoja[$pesquisaSatisfacao['loja']]['nota'] += 1;
					}
					/*if($pesquisaSatisfacao['nota_indicar_loja'] == "Não"){
						$arrLoja[$pesquisaSatisfacao['loja']]['nota'] += 0;
					}*/
				
				}
				
				
				if($pesquisaSatisfacao['loja'] != "Não"){
				
					$arrPreparador[$pesquisaSatisfacao['loja']]['nome'] = "Preparador ".$pesquisaSatisfacao['loja'];
				
					if(!isset($arrPreparador[$pesquisaSatisfacao['loja']]['nota'])){
						$arrPreparador[$pesquisaSatisfacao['loja']]['nota'] = 0;
					}
					if($pesquisaSatisfacao['nota_limpeza_veiculo'] == "Ótimo"){
						$arrPreparador[$pesquisaSatisfacao['loja']]['nota'] += 3;
					}
					if($pesquisaSatisfacao['nota_limpeza_veiculo'] == "Normal"){
						$arrPreparador[$pesquisaSatisfacao['loja']]['nota'] += 1;
					}
					/*if($pesquisaSatisfacao['nota_limpeza_veiculo'] == "Ruim"){
						$arrPreparador[$pesquisaSatisfacao['loja']]['nota'] += 0;
					}*/
					
					if($pesquisaSatisfacao['nota_conservacao_veiculo'] == "Ótimo"){
						$arrPreparador[$pesquisaSatisfacao['loja']]['nota'] += 3;
					}
					if($pesquisaSatisfacao['nota_conservacao_veiculo'] == "Normal"){
						$arrPreparador[$pesquisaSatisfacao['loja']]['nota'] += 1;
					}
					/*if($pesquisaSatisfacao['nota_conservacao_veiculo'] == "Ruim"){
						$arrPreparador[$pesquisaSatisfacao['loja']]['nota'] += 0;
					}*/
				
				}
				
			
			}
			
			if(count($arrVendedor) > 0){
				$count = 0;
				foreach($arrVendedor as $vendedor){
					
					$cor = "";
					
					if($count%2 == 0){
						$cor = "style='background-color:#eee;'";
					}
					
					$strTableTotalVendedor .= "<tr ".$cor.">
													<td>".$vendedor['nome']."</td>
													<td>".$vendedor['nota']."</td>
											   </tr>";	
							
					$count++;
							
				}
			
			}

			if(count($arrLoja) > 0){
				$count = 0;
				foreach($arrLoja as $loja){
					
					$cor = "";
					
					if($count%2 == 0){
						$cor = "style='background-color:#eee;'";
					}
					
					$strTableTotalLoja .= "<tr ".$cor.">
													<td>".$loja['nome']."</td>
													<td>".$loja['nota']."</td>
											   </tr>";	
					$count++;			  
				}
			
			}
			
			if(count($arrPreparador) > 0){
				$count = 0;
				foreach($arrPreparador as $preparador){
					
					$cor = "";
					
					if($count%2 == 0){
						$cor = "style='background-color:#eee;'";
					}
					
					$strTableTotalPreparador .= "<tr ".$cor.">
													<td>".$preparador['nome']."</td>
													<td>".$preparador['nota']."</td>
											   </tr>";	
					$count++;				  
				}
			
			}
			
			
			$strTable .= "</table>";
			
			$strTableTotalVendedor .= "</table>";
			
			$strTableTotalLoja .= "</table>";
			
			$strTableTotalPreparador .= "</table>";

			
			echo "<table width='100%'>
					<tr>
						<td>".$strTableTotalVendedor."</td>
						<td>".$strTableTotalLoja."</td>
						<td>".$strTableTotalPreparador."</td>
					</tr>
				  </table>";
			
			echo "<table class='table' style='width:150%;'>
					<tr>
						<th>Pesquisas detalhadas</th>
					</tr>
					<tr>
						<td>".$strTable."</td>
					</tr>
				</table>";
			
		}elseif($this->_getParam('fn') == 'busca_veiculos'){
		
			$dbBuscaVeiculos = new Application_Model_DbTable_BuscaVeiculos();

			$totalParcela = 0;
			$totalEntrada = 0;
			$totalAvista = 0;
			
			if($this->_getParam('data_inicial') != ""){
				$dataInicial = implode("-",array_reverse(explode("/",$this->_getParam('data_inicial'))));
			}else{
				$dataInicial = @date("Y-m")."-01";
			}
			if($this->_getParam('data_final') != ""){
				$dataFinal = implode("-",array_reverse(explode("/",$this->_getParam('data_final'))));
			}else{
				$dataFinal = @date("Y-m-d");
			}
			
			$arrBuscaVeiculos = $dbBuscaVeiculos->getBuscaVeiculos($_SESSION['sessionUser']['id_empresa'], $dataInicial, $dataFinal);
			
			
			if($arrBuscaVeiculos){

				$strRelatorio = "<table class='table'>
									<tr>
										<th>Dados Cliente</th>
										<th>Dados Veículo</th>
										<th width='25%'>Opcionais</th>
										<th>Parcela</th>
										<th>Entrada</th>
										<th>À vista</th>
									</tr>";
				
				foreach($arrBuscaVeiculos as $buscaVeiculos){
				
					$strRelatorio .= "<tr>
										<td><b>Nome: </b>".$buscaVeiculos['nome']."<br/>
										<b>Email: </b>".$buscaVeiculos['email']."<br/>
										<b>Telefone: </b>".$buscaVeiculos['telefone']."<br/>
										<b>Celular: </b>".$buscaVeiculos['celular']."<br/>
										<b>Bairro: </b>".$buscaVeiculos['bairro']."<br/>
										<b>Cidade: </b>".$buscaVeiculos['cidade']."<br/>
										<b>Estado: </b>".$buscaVeiculos['estado']."</td>
										<td><b>Marca: </b>".$buscaVeiculos['marca']."<br/>
										<b>Modelo: </b>".$buscaVeiculos['modelo']."<br/>
										<b>Versão: </b>".$buscaVeiculos['versao']."<br/>
										<b>Motorização: </b>".$buscaVeiculos['motorizacao']."<br/>
										<b>Cor: </b>".$buscaVeiculos['cor']."<br/>
										<b>Ano: </b>".$buscaVeiculos['ano_veiculo']."<br/>
										<b>Combustivel: </b>".$buscaVeiculos['combustivel']."<br/>
										<b>Portas: </b>".$buscaVeiculos['portas']."<br/>
										<b>KM: </b>".$buscaVeiculos['maximo_km']."</td>
										<td>".$buscaVeiculos['opcionais']."</td>
										<td>R$ ".money_format("%i",$buscaVeiculos['parcela'])."</td>
										<td>R$ ".money_format("%i",$buscaVeiculos['entrada'])."</td>
										<td>R$ ".money_format("%i",$buscaVeiculos['avista'])."</td>
									 </tr>";
									 
									 
					$totalParcela += $buscaVeiculos['parcela'];
					$totalEntrada += $buscaVeiculos['entrada'];
					$totalAvista += $buscaVeiculos['avista'];
					
					if(!isset($arrModelo[$buscaVeiculos['marca']." ".strtolower($buscaVeiculos['modelo'])])){
						$arrModelo[$buscaVeiculos['marca']." ".strtolower($buscaVeiculos['modelo'])] = 0;
					}
					$arrModelo[$buscaVeiculos['marca']." ".strtolower($buscaVeiculos['modelo'])] += 1;
				
				}
				
				$qtdModelo = 0;
				
				foreach($arrModelo as $key=>$valor){
					
					if($valor > $qtdModelo){
					
						$strModelo = $key;
						$qtdModelo = $valor;
					
					}
				
				}
				
				$strRelatorio .= "<tr bgcolor='#EEEEEE' height='40px'>
									<td style='text-align:right;'>MODELO MAIS BUSCADO</td>
									<td>".ucwords($strModelo)."</td>
									<td style='text-align:right;'>MÉDIAS</td>
									<td>R$ ".money_format("%i",$totalParcela/count($arrBuscaVeiculos))."</td>
									<td>R$ ".money_format("%i",$totalEntrada/count($arrBuscaVeiculos))."</td>
									<td>R$ ".money_format("%i",$totalAvista/count($arrBuscaVeiculos))."</td>
								  </tr>";
				
				$strRelatorio .= "</table>";
				
				echo $strRelatorio;
			
			}else{
				
				echo "<div>Não foram encontrados registros com as características buscadas!</div>";
			
			}
			
			//var_export($arrBuscaVeiculos);
			
			
			
		}elseif($this->_getParam('fn') == 'indicacoes_corretoras_relatorio'){
	
			$dbIndicacoesCorretoras = new Application_Model_DbTable_IndicacoesCorretoras();
		
			$arrIndicacoes = $dbIndicacoesCorretoras->getIndicacaoPorCorretoraDataLoja($_SESSION['sessionUser']['id_empresa'], $this->_getParam('data_inicial'), $this->_getParam('data_final'));
		
			if($_SESSION['sessionUser']['id_empresa'] == 0){
		
				$strIndicacoes = "<tr><th></th><th>CLIENTE</th><th>CPF</th><th>TELEFONE</th><th>VEÍCULO</th><th>LOJA</th><th>DATA VENDA</th><th style='width:70px;'>VALOR DO SEGURO</th><th style='width:100px;'>RESULTADO</th></tr>";

			}else{
				
				$strIndicacoes = "<tr><th></th><th>CLIENTE</th><th>CPF</th><th>TELEFONE</th><th>VEÍCULO</th><th>DATA VENDA</th><th style='width:70px;'>VALOR DO SEGURO</th><th style='width:100px;'>RESULTADO</th></tr>";
			
			}
				
			if($arrIndicacoes){
				
				$qtd = 0;
				$soma = 0;
		
				foreach($arrIndicacoes as $indicacoes){
				
					$qtd++;
				
					if($indicacoes['resultado'] == 0){
						$indicacoes['resultado'] = "Não orçado";
					}
					if($indicacoes['resultado'] == 1){
						$indicacoes['resultado'] = "Vendido";
					}
					if($indicacoes['resultado'] == 2){
						$indicacoes['resultado'] = "Concorrente";
					}
					if($indicacoes['resultado'] == 3){
						$indicacoes['resultado'] = "Não tem interesse";
					}
				
					if($_SESSION['sessionUser']['id_empresa'] == 0){
				
						$strIndicacoes .= "<tr onmouseout=\"$(this).css('background-color','#FFFFFF')\" onmouseover=\"$(this).css('background-color','#DDDDDD')\">
											<td>
												".$qtd."
											</td>
											<td>
												".$indicacoes['nome']."
											</td>
											<td>
												".$indicacoes['cpf']."
											</td>
											<td>
												".$indicacoes['tel1']."<br/>".$indicacoes['tel2']."
											</td>
											<td>
												".$indicacoes['modelo']."
											</td>
											<td>
												".$indicacoes['nome_fantasia']."
											</td>
											<td>
												".implode("/",array_reverse(explode("-",$indicacoes['data_venda'])))."
											</td>
											<td>
												R$ ".money_format("%i",$indicacoes['valor_seguro'])."
											</td>
											<td>
												".$indicacoes['resultado']."
											</td>
									  </tr>";
					
					}else{
					
						$strIndicacoes .= "<tr onmouseout=\"$(this).css('background-color','#FFFFFF')\" onmouseover=\"$(this).css('background-color','#DDDDDD')\">
											<td>
												".$qtd."
											</td>
											<td>
												".$indicacoes['nome']."
											</td>
											<td>
												".$indicacoes['cpf']."
											</td>
											<td>
												".$indicacoes['tel1']."<br/>".$indicacoes['tel2']."
											</td>
											<td>
												".$indicacoes['modelo']."
											</td>
											<td>
												".implode("/",array_reverse(explode("-",$indicacoes['data_venda'])))."
											</td>
											<td>
												R$ ".money_format("%i",$indicacoes['valor_seguro'])."
											</td>
											<td>
												".$indicacoes['resultado']."
											</td>
									  </tr>";
					
					}

					$soma += $indicacoes['valor_seguro'];
				
				}
				
				if($_SESSION['sessionUser']['id_empresa'] == 0){
				
					$strIndicacoes .= "<tr><td colspan='7' style='text-align:right; background-color:#DDDDDD;'>TOTAL</td><td colspan='2' style='background-color:#DDDDDD;'>R$ ".money_format("%i",$soma)."</td></tr>";
					$strIndicacoes .= "<tr><td colspan='7' style='text-align:right; background-color:#DDDDDD;'>MÉDIA</td><td colspan='2' style='background-color:#DDDDDD;'>R$ ".money_format("%i",$soma/$qtd)."</td></tr>";
			
				}
				
				echo $strIndicacoes;
			
			}else{
				
				echo "Não há indicações com as características buscadas.";
			
			}
			
			
		}elseif($this->_getParam('fn') == 'origem_clientes'){
			
			$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
			
			$count = "";
			
			$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			if($this->_getParam('origem') != "") {
				$arrFiltro['origem'] = $this->_getParam('origem');
			}
	
			if($this->_getParam('data_inicial') != ""){
				$arrFiltro['data_inicial'] = implode("-",array_reverse(explode("/",$this->_getParam('data_inicial'))));

			}
			if($this->_getParam('data_final') != ""){
				$arrFiltro['data_final'] = implode("-",array_reverse(explode("/",$this->_getParam('data_final'))));
			}

			$arrFluxoClientes = $dbFluxoClientes->getOrigemClientes($arrFiltro);

			if(count($arrFluxoClientes) > 0){

				$strRelatorio = "<table class='table'>
									<tr>
										<th>Nome</th>
										<th>Origem</th>
										<th>Resultado</th>
										<th>Data</th>
										<th>Vendedor</th>
									</tr>";
									
				
				foreach($arrFluxoClientes as $fluxoClientes){
					
					$strRelatorio .= "<tr><td>".$fluxoClientes['nome']."</td>
										  <td>".$fluxoClientes['descricao']."</td>
										  <td>".$fluxoClientes['resultado']."</td>
										  <td>".$fluxoClientes['data']."</td>
										  <td>".$fluxoClientes['nome_usuario']."</td>
									  </tr>";
									  
					if(!isset($arrOrigemVendedor[$fluxoClientes['nome_usuario']])){
						$arrOrigemVendedor[$fluxoClientes['nome_usuario']] = 0;
					}
					$arrOrigemVendedor[$fluxoClientes['nome_usuario']] += 1;

					if(!isset($arrOrigem[$fluxoClientes['descricao']])){
						$arrOrigem[$fluxoClientes['descricao']] = 0;
					}
					$arrOrigem[$fluxoClientes['descricao']] += 1;

					if(!isset($arrOrigemResultado[$fluxoClientes['resultado']])) {
						$arrOrigemResultado[$fluxoClientes['resultado']] = 0;
					}
					$arrOrigemResultado[$fluxoClientes['resultado']] += 1;
					$count += 1;
					
				}
				
				
				$strRelatorio .= "</table>";
				
				$tableTotal = "<table class='table' style='width:30%'>
								<tr>
									<th>Vendedor</th>
									<th>Total</th>
								</tr>";

				foreach($arrOrigemVendedor as $key=>$origem){
					
					$tableTotal .= "<tr><td>".$key."</td><td>".$origem."</td></tr>";
				
				}
				
				$tableTotal .= "<tr><th>Origem dos clientes</th><th>Total</th></tr>";
				
				foreach($arrOrigem as $key=>$origem){
					
					$tableTotal .= "<tr><td>".$key."</td><td>".$origem."</td></tr>";
				
				}
				
				$tableTotal .= "<tr><th>Resultado</th><th>Total</th></tr>";
				
				foreach($arrOrigemResultado as $key=>$origem){
					
					if($key == ""){
						
						$key = "Sem resultado";
					
					}
					
					$tableTotal .= "<tr><td>".$key."</td><td>".$origem."</td></tr>";
				
				}
				
				$tableTotal .= "<tr><th>Total de Origens</th><th>".$count."</th></tr>";
				
				$tableTotal .= "</table>";

				echo $strRelatorio."   ".$tableTotal;
			}else{
				echo "<span style='margin-left: 10px; font-size: 20px;'>Não há origem de clientes com as características informadas!</span>";
			}

		}
		
	}
	
	
	public function origemClientesAction(){
		
		$this->validaAcesso('relatorios');
		
	}
	
	
	public function folhaPagamentoAction(){
		
		$this->validaAcesso('relatorios');
		
		$dbUsuarios = new Application_Model_DbTable_Usuarios();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$this->view->arrUsuarios = $dbUsuarios->_getUsuariosNegociacoes($arr);
		
		$arr['ordem_perfil'] = true;
		
		$this->view->arrPerfis = $dbUsuarios->_getUsuariosNegociacoes($arr);
		

	}
	
	
	public function buscaVeiculosAction(){
		
		
	
	}
	
	public function fechamentoAction(){
	
		$this->validaAcesso('relatorios');
		
		$dbVendedores = new Application_Model_DbTable_Vendedores();
		$dbCoresRelatorios = new Application_Model_DbTable_CoresRelatorios();

		$totalFolhaPagamento = 0;
		$arrTabelas = array();
		$credito = false;
		
		$arrBuscaGarantia['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrCores = $dbCoresRelatorios->_get($arr);
			
		$this->view->arrCores = $arrCores[0];

		$this->view->vendedores = $dbVendedores->_get($arr);
		
		if($this->getRequest()->isPost()){

			$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
			$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
			$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
			$dbGarantias = new Application_Model_DbTable_Garantias();
			
			$arrBusca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$arrBusca['aprovada'] = 1;
			$arrBusca['order_usuarios'] = true;
			if($_POST['id_vendedor'] == 0){
				$arrBusca['id_vendedor'] = null;
			}else{
				$arrBusca['id_vendedor'] = $_POST['id_vendedor'];
			}
			$arrBusca['data_inicial_concretizacao'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
			$arrBusca['data_final_concretizacao'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));
			$arrBuscaGarantia['data_inicial'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
			$arrBuscaGarantia['data_final'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));

			$arrNegociacoes = $dbNegociacoes->_get($arrBusca);
			
			foreach($arrNegociacoes as $key=>$valor){
			
				$valorDespesas = $dbDespesasVeiculos->getSomaDespesas($arrNegociacoes[$key]['id_veiculo']);
			 
				$arrNegociacoes[$key]['valor_despesas'] = $valorDespesas[0]['valor_despesas'];
			
			}
			
			$this->view->id_vendedor = $_POST['id_vendedor'];
			$this->view->data_inicial = $_POST['data_inicial'];
			$this->view->data_final = $_POST['data_final'];
			$this->view->arrNegociacoes = $arrNegociacoes;
			$this->view->gruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupo($arrBusca);
			$this->view->garantia = $dbGarantias->getGarantias($arrBuscaGarantia);
			
			///////////////////////////INICIO TABELAS MESES///////////////////////////////////////

			
			foreach($arrNegociacoes as $negociacao){
			
				$datas = explode(" ",$negociacao['data_concretizacao']);
				$data = explode("-",$datas[0]);
				
				if(!isset($arrStr[$data[1]]['Lucro Bruto dos Carros-0'])){
					$arrStr[$data[1]]['Lucro Bruto dos Carros-0'] = 0;
				}
				$arrStr[$data[1]]['Lucro Bruto dos Carros-0'] += ($negociacao['valor_venda']-($negociacao['valor_despesas']+$negociacao['valor_aquisicao']));
				
				if(!isset($arrStr[$data[1]]['qtd_vendas-0'])){
					$arrStr[$data[1]]['qtd_vendas-0'] = 0;
				}
				$arrStr[$data[1]]['qtd_vendas-0'] += 1;
			
				if(isset($negociacao['id_financeira'])){
					if(!isset($arrStr[$data[1]]['F&I-0'])){
						$arrStr[$data[1]]['F&I-0'] = 0;
					}
					$arrStr[$data[1]]['F&I-0'] += ((($negociacao['retorno_financeira']*1.2)*$negociacao['valor_financiado'])/100)-$negociacao['imposto_financeira'];
				
				}
				
				if(!isset($arrStr[$data[1]]['Despachantes-0'])){
					$arrStr[$data[1]]['Despachantes-0'] = 0;
				}
				$arrStr[$data[1]]['Despachantes-0'] += $negociacao['imposto'];
			
			}
			
			$arrBusca['data_inicial'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
			$arrBusca['data_final'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));
			
			$arrBusca['credito'] = true;
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arrBusca);
			
			foreach($arrGruposFinanceiros as $gruposFinanceiro){
			
				$data = explode("-",$gruposFinanceiro['data_lancamento']);
			
				if(!isset($arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']])){
					$arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']] = 0;
				}
				$arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']] += $gruposFinanceiro['valor'];
			
			}
			
			$arrBusca['credito'] = false;
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arrBusca);
			
			foreach($arrGruposFinanceiros as $gruposFinanceiro){
			
				$data = explode("-",$gruposFinanceiro['data_lancamento']);
				
				if(!isset($arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']])){
					$arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']] = 0;
				}
				$arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']] += $gruposFinanceiro['valor'];
			
			}
			
			
			$arrGarantias = $dbGarantias->getGarantiasIndividual($arrBuscaGarantia);
			
			foreach($arrGarantias as $garantia){
			
				$data = explode("-",$garantia['data_saida']);
				
				if(!isset($arrStr[$data[1]]['Garantias-1'])){
					$arrStr[$data[1]]['Garantias-1'] = 0;
				}
				$arrStr[$data[1]]['Garantias-1'] += $garantia['custo'];
				
			}
			
			$arrDataInicial = explode("/",$_POST['data_inicial']);
			$arrDataFinal = explode("/",$_POST['data_final']);
			
			if(isset($arrDataInicial[1])){
				$mesInicial = $arrDataInicial[1];
			}else{
				$mesInicial = 0;
			}
			if(isset($arrDataFinal[1])){
				$mesFinal = $arrDataFinal[1];
			}else{
				$mesFinal = 0;
			}
			if(isset($arrDataInicial[2])){
				$anoInicial = $arrDataInicial[2];
			}else{
				$anoInicial =0;
			}
			if(isset($arrDataFinal[2])){
				$anoFinal = $arrDataFinal[2];
			}else{
				$anoFinal = 0;
			}



			$calcula = true;

			while($calcula){


				$arrMes = explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial, 1, $anoInicial)));
				
				if(!isset($arrStr[$arrMes[1]]['Folha de Pagamento-1'])){
					$arrStr[$arrMes[1]]['Folha de Pagamento-1'] = 0;
				}
				$arrStr[$arrMes[1]]['Folha de Pagamento-1'] += $this->calculaFolhaPagamento(implode("/",array_reverse(explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial, 1, $anoInicial))))), implode("/",array_reverse(explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial+1, 0, $anoInicial))))));
			
				$totalFolhaPagamento += $this->calculaFolhaPagamento(implode("/",array_reverse(explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial, 1, $anoInicial))))), implode("/",array_reverse(explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial+1, 0, $anoInicial))))));

				//var_export(implode("/",array_reverse(explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial, 1, $anoInicial)))))." , ".implode("/",array_reverse(explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial+1, 0, $anoInicial)))))."<br>");

				if($mesInicial == $mesFinal && $anoInicial == $anoFinal){
					$calcula = false;
				}

				$mesInicial++;

				if($mesInicial > 12){
					$mesInicial = 1;
					$anoInicial++;
				}

			}
			

			foreach($arrStr as $mes=>$str){
				
				$totalCreditos = 0;
				$totalDebitos = 0;
				
				if(!isset($arrTabelas[$mes]['tabela'])){
					$arrTabelas[$mes]['tabela'] = 0;
				}
				$arrTabelas[$mes]['tabela'] .= "<table class='table' style='width:70%;'>
												<thead>
													<tr>
														<td colspan='2'>
															<center><label style='font-size:15px;'><b>Resultado Final - ".$this->mesExtenso($mes)."<b></label></center>
														</td>
													</tr>
													<tr>
														<th>Item</th>
														<th>Valor</th>
													</tr>
												</thead>";
			
				foreach($str as $indice=>$valor){
			
					$cd = explode("-", $indice);
					
					if($cd[1] == 0){
					
						$corCreditoDebito = "class='verde'";
						
						if($cd[0] != "qtd_vendas-0"){
						
							$totalCreditos += $valor;
							
						}
						
						$credito = true;
						
					}elseif($cd[1] == 1){
					
						if($credito){
						
							$arrTabelas[$mes]['tabela'] .= "<tr>
														<td style='border-bottom:solid 1px; border-top:solid 1px; border-left:solid 1px;' ".$corCreditoDebito."><b>TOTAL CR&Eacute;DITOS</b></td>
														<td style='border-bottom:solid 1px; border-top:solid 1px; border-right:solid 1px;' ".$corCreditoDebito."><b>R$ ".money_format("%i",$totalCreditos)."</b></td>
													</tr>";
						
						}
					
						$totalDebitos += $valor;
						$corCreditoDebito = "class='vermelho'";
						$credito = false;
						
					}
			
					if($cd[0] == "Lucro Bruto dos Carros"){
			
						$vendasQtd = " (".$str['qtd_vendas-0']." veículos vendidos)";
					
					}else{
						
						$vendasQtd = "";
					
					}
					
					if($cd[0] != "qtd_vendas"){
			
						$arrTabelas[$mes]['tabela'] .= "<tr>
															<td ".$corCreditoDebito.">".$cd[0].$vendasQtd."</td>
															<td ".$corCreditoDebito.">R$ ".money_format("%i",$valor)."</td>
														</tr>";
					
					
					}
					
				}
				
				$arrTabelas[$mes]['tabela'] .= "<tr>
													<td class='vermelho' style='border-bottom:solid 1px; border-top:solid 1px; border-left:solid 1px;'><b>TOTAL D&Eacute;BITOS</b></td>
													<td class='vermelho' style='border-bottom:solid 1px; border-top:solid 1px; border-right:solid 1px;'><b>R$ ".money_format("%i",$totalDebitos)."</b></td>
												</tr>
												<tr>
													<td style='border-bottom:solid 2px; border-top:solid 2px; border-left:solid 2px;'><b>TOTAL</b></td>
													<td style='border-bottom:solid 2px; border-top:solid 2px; border-right:solid 2px;'><b>R$ ".money_format("%i",$totalCreditos-$totalDebitos)."</b></td>
												</tr>";
				
				$arrTabelas[$mes]['tabela'] .= "</table>";
			
			}
			
			$inicial = explode("/",$_POST['data_inicial']);
			$finals = explode("/",$_POST['data_final']);
			
			if(isset($inicial[1])){
				$this->view->inicial = $inicial[1];
			}
			if(isset($finals[1])){
				$this->view->finals = $finals[1];
			}
			$this->view->arrTabelas = $arrTabelas;
			
///////////////////////////FIM TABELAS MESES///////////////////////////////////////
			
			
		}else{
	
			$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
			$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
			$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
			$dbGarantias = new Application_Model_DbTable_Garantias();
			
			$arrBusca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$arrBusca['data_inicial_concretizacao'] = @date("Y")."-".(@date("m"))."-01";
			//$arrBusca['data_inicial_concretizacao'] = @date("Y")."-11-01";
			$arrBusca['aprovada'] = 1;
			$arrBusca['order_usuarios'] = true;
			$arrBuscaGarantia['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$arrBuscaGarantia['data_inicial'] = @date("Y")."-".(@date("m"))."-01";
			
			//var_export($arrBusca['data_inicial_concretizacao']);
			
			$arrNegociacoes = $dbNegociacoes->_get($arrBusca);
			
			foreach($arrNegociacoes as $key=>$valor){
			
				$valorDespesas = $dbDespesasVeiculos->getSomaDespesas($arrNegociacoes[$key]['id_veiculo']);
			 
				$arrNegociacoes[$key]['valor_despesas'] = $valorDespesas[0]['valor_despesas'];
			
			}
			
			
			$this->view->gruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupo($arrBusca);
			$this->view->arrNegociacoes = $arrNegociacoes;
			$this->view->garantia = $dbGarantias->getGarantias($arrBuscaGarantia);
			
///////////////////////////INICIO TABELAS MESES///////////////////////////////////////
	
			foreach($arrNegociacoes as $negociacao){
			
				$datas = explode(" ",$negociacao['data_concretizacao']);
				$data = explode("-",$datas[0]);
				
				if(!isset($arrStr[$data[1]]['Lucro Bruto dos Carros-0'])){
					$arrStr[$data[1]]['Lucro Bruto dos Carros-0'] = 0;
				}
				$arrStr[$data[1]]['Lucro Bruto dos Carros-0'] += ($negociacao['valor_venda']-($negociacao['valor_despesas']+$negociacao['valor_aquisicao']));
			
				if($negociacao['id_financeira']){
				
					if(!isset($arrStr[$data[1]]['F&I-0'])){
						$arrStr[$data[1]]['F&I-0'] = 0;
					}
					$arrStr[$data[1]]['F&I-0'] += ((($negociacao['retorno_financeira']*1.2)*$negociacao['valor_financiado'])/100)-$negociacao['imposto_financeira'];
				
				}
				
				if(!isset($arrStr[$data[1]]['Despachantes-0'])){
					$arrStr[$data[1]]['Despachantes-0'] = 0;
				}
				$arrStr[$data[1]]['Despachantes-0'] += $negociacao['imposto'];
			
			}
		
			$arr['credito'] = true;
			$arr['data_inicial'] = @date("Y")."-". @date("m")."-01";
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arr);
		
			foreach($arrGruposFinanceiros as $gruposFinanceiro){
			
				$data = explode("-",$gruposFinanceiro['data_lancamento']);
			
				if(!isset($arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']])){
					$arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']] = 0;
				}
				$arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']] += $gruposFinanceiro['valor'];
			
			}
		
			$arr['credito'] = false;
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arr);
			
			foreach($arrGruposFinanceiros as $gruposFinanceiro){
			
				$data = explode("-",$gruposFinanceiro['data_lancamento']);

				if(!isset($arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']])){
					$arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']] = 0;
				}
				$arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']] += $gruposFinanceiro['valor'];
			
			}
			
			
			$arrGarantias = $dbGarantias->getGarantiasIndividual($arrBuscaGarantia);
			
			foreach($arrGarantias as $garantia){
			
				$data = explode("-",$garantia['data_saida']);
				
				if(!isset($arrStr[$data[1]]['Garantias-1'])){
					$arrStr[$data[1]]['Garantias-1'] = 0;
				}
				$arrStr[$data[1]]['Garantias-1'] += $garantia['custo'];
				
			}

			$mesInicial = @date("m");
			$mesFinal =  @date("m");
			$ano = @date("Y");
			
			if($mesFinal+1 == 13){
				
				$mesFinal = 0;
			
			}
			
			while($mesInicial != $mesFinal+1){
				
				//echo @date("Y-m-d", mktime(0,0,0, $mesInicial, 1, $ano))."  ".@date("Y-m-d", mktime(0,0,0, $mesInicial+1, 0, $ano))."<br/>";

				$arrMes = explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial, 1, $ano)));
				
				if(!isset($arrStr[$arrMes[1]]['Folha de Pagamento-1'])){
					$arrStr[$arrMes[1]]['Folha de Pagamento-1'] = 0;
				}
				$arrStr[$arrMes[1]]['Folha de Pagamento-1'] += $this->calculaFolhaPagamento(implode("/",array_reverse(explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial, 1, $ano))))), implode("/",array_reverse(explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial+1, 0, $ano))))));
			
				$totalFolhaPagamento += $this->calculaFolhaPagamento(implode("/",array_reverse(explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial, 1, $ano))))), implode("/",array_reverse(explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial+1, 0, $ano))))));
			
				$mesInicial++;
				
				if($mesInicial == 13){
					
					$mesInicial = 1;
					$ano++;
					
				}
			
			}

			if($arrStr){

				foreach($arrStr as $mes=>$str){
					
					$totalCreditos = 0;
					$totalDebitos = 0;
					
					if(!isset($arrTabelas[$mes]['tabela'])){
						$arrTabelas[$mes]['tabela'] = "";
					}
					$arrTabelas[$mes]['tabela'] .= "<table class='table' style='width:70%;'>
													<thead>
														<tr>
															<td colspan='2'>
																<center><label style='font-size:15px;'><b>Resultado Final - ".$this->mesExtenso($mes)."<b></label></center>
															</td>
														</tr>
														<tr>
															<th>Item</th>
															<th>Valor</th>
														</tr>
													</thead>";
			
					foreach($str as $indice=>$valor){
				
						$cd = explode("-", $indice);
						
						if($cd[1] == 0){
						
							$corCreditoDebito = "class='verde'";
							$totalCreditos += $valor;
							$credito = true;
							
						}elseif($cd[1] == 1){
						
							if($credito){
								$arrTabelas[$mes]['tabela'] .= "<tr>
															<td style='border-bottom:solid 1px; border-top:solid 1px; border-left:solid 1px;' ".$corCreditoDebito."><b>TOTAL CR&Eacute;DITOS</b></td>
															<td style='border-bottom:solid 1px; border-top:solid 1px; border-right:solid 1px;' ".$corCreditoDebito."><b>R$ ".money_format("%i",$totalCreditos)."</b></td>
														</tr>";
							
							}
						
							$totalDebitos += $valor;
							$corCreditoDebito = "class='vermelho'";
							$credito = false;
							
						}
				
						$arrTabelas[$mes]['tabela'] .= "<tr>
															<td ".$corCreditoDebito.">".$cd[0]."</td>
															<td ".$corCreditoDebito.">R$ ".money_format("%i",$valor)."</td>
														</tr>";
						
						
					}
				
					$arrTabelas[$mes]['tabela'] .= "<tr>
														<td class='vermelho' style='border-bottom:solid 1px; border-top:solid 1px; border-left:solid 1px;'><b>TOTAL D&Eacute;BITOS</b></td>
														<td class='vermelho' style='border-bottom:solid 1px; border-top:solid 1px; border-right:solid 1px;'><b>R$ ".money_format("%i",$totalDebitos)."</b></td>
													</tr>
													<tr>
														<td style='border-bottom:solid 2px; border-top:solid 2px; border-left:solid 2px;'><b>TOTAL</b></td>
														<td style='border-bottom:solid 2px; border-top:solid 2px; border-right:solid 2px;'><b>R$ ".money_format("%i",$totalCreditos-$totalDebitos)."</b></td>
													</tr>";
					
					$arrTabelas[$mes]['tabela'] .= "</table>";
				
				}
			
			}
			
			$this->view->arrTabelas = $arrTabelas;
			
///////////////////////////FIM TABELAS MESES///////////////////////////////////////
		
		}
		
		

	 	$this->view->rsFolhaPagamento = $totalFolhaPagamento;
	
	}
	
	
	public function listaEstoqueGerencialAction(){

		$this->validaAcesso('relatorios');

		set_time_limit(150);

		$dbVeiculo = new Application_Model_DbTable_Veiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbCoresRelatorios = new Application_Model_DbTable_CoresRelatorios();

		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$id_empresa = $_SESSION['sessionUser']['id_empresa'];

		$arrCores = $dbCoresRelatorios->_get($arr);

		// Pré-carrega opcionais, despesas e fotos em uma query cada
		$arrOpcionaisTodos = $dbOpcionaisVeiculos->getOpcionaisPorEmpresa($id_empresa);
		$arrDespesasTodas  = $dbDespesasVeiculos->getSomaDespesasPorEmpresa($id_empresa);
		$arrFotosTodas     = $dbFotosVeiculos->getNFotosPorEmpresa($id_empresa);

		// Indexa por id_veiculo para acesso O(1)
		$mapOpcionais = array();
		foreach($arrOpcionaisTodos as $op){
			$mapOpcionais[$op['id_veiculo']][] = $op['opcional'];
		}
		$mapDespesas = array();
		foreach($arrDespesasTodas as $dep){
			$mapDespesas[$dep['id_veiculo']] = $dep['valor_despesas'];
		}
		$mapFotos = array();
		foreach($arrFotosTodas as $f){
			$mapFotos[$f['id_veiculo']] = $f['total'];
		}

		$arr['vend'] = 1;
		$arr['order'] = 'marca';
		$arr['exibir_estoque'] = true;
		
		if(isset($_POST['origem']) && $_POST['origem'] == "Outros"){
		
			$arr['sql'] = "v.origem != 'Compra' AND v.origem != 'Concessionária' AND v.origem != 'Indicação' AND v.origem != 'Leilão' AND v.origem != 'Troca'";
		
		}elseif(isset($_POST['origem']) && $_POST['origem'] != "") {

			$arr['origem'] = $_POST['origem'];
			
		}
		
		$arrVeiculo = $dbVeiculo->_get($arr);
		
		
		$strTabela = "<table id='table_dados' style='width:1500px;'>";
		
		$count = 0;
		$count2 = 0;
		$somaVenda = 0;
		$somaDiasEstoque = 0;
		$somaCusto = 0;
		$somaCompra = 0;
		$somaRevisao = 0;
		$somaLucro = 0;
		$somaLucroPorcento = 0;
		$somaFIPE = 0;
		
		if(count($arrVeiculo) > 0){

			foreach($arrVeiculo as $key=>$veiculo){
			
				$count++;
				$count2++;
			
				$opcionais = "";

			if(isset($mapOpcionais[$veiculo['id']])){
					foreach($mapOpcionais[$veiculo['id']] as $opcNome){
						$tempOpcional = explode(")", $opcNome);
						$opcionais .= str_replace("(", "", $tempOpcional[0])." ";
					}
				}
				
				if($count2%2 == 0){
				
					$bgCor = "bgColor='#DDDDDD'";
				
				}else{
				
					$bgCor = "bgColor='#FFFFFF'";
				
				}
				
				if($veiculo['vendido'] == 0){
		
					$dataTmp[0] = date("Y-m-d");

				}else{
				
					if($veiculo['data_concretizacao'] == "0000-00-00 00:00:00" || $veiculo['data_concretizacao'] == null){
					
						$dataTmp[0] = date("Y-m-d");
					
					}else{
				
						$dataTmp = explode(" ",$veiculo['data_concretizacao']);
					
					}

				}
				
				$dataTmp2 = explode("-",$dataTmp[0]);
				$dataIni = explode("-",$veiculo['data_aquisicao']);
				$timestamp1 = mktime(0,0,0,$dataTmp2[1],$dataTmp2[2],$dataTmp2[0]); 
				$timestamp2 = mktime(0,0,0,$dataIni[1],$dataIni[2],$dataIni[0]);
				$segundos_diferenca = $timestamp1 - $timestamp2;
				$dias_diferenca = ($segundos_diferenca /(60 * 60 * 24));
				
		
				$valorDespesa = isset($mapDespesas[$veiculo['id']]) ? $mapDespesas[$veiculo['id']] : 0;
				$lucroReal = $veiculo['valor_venda'] - ($valorDespesa + $veiculo['valor_aquisicao']);
				$lucroPorcento = ($valorDespesa + $veiculo['valor_aquisicao']) > 0 ? $lucroReal / ($valorDespesa + $veiculo['valor_aquisicao']) * 100 : 0;
				
				if($veiculo['data_termino_revisao'] == null || $veiculo['data_termino_revisao'] == "0000-00-00"){
				
					$dataTerminoRevisao = "Em Revis&atilde;o";
					$veiculo['data_termino_revisao'] = "0000-00-00";
				
				}else{
				
					$dataTerminoRevisao = implode("/",array_reverse(explode("-",$veiculo['data_termino_revisao'])));
				
				}
				
				$dataTmp2 = explode("-",$veiculo['data_termino_revisao']);
				$dataIni = explode("-",$veiculo['data_aquisicao']);
				$timestamp1 = mktime(0,0,0,$dataTmp2[1],$dataTmp2[2],$dataTmp2[0]); 
				$timestamp2 = mktime(0,0,0,$dataIni[1],$dataIni[2],$dataIni[0]);
				$segundos_diferencas = $timestamp1 - $timestamp2;
				
				if($segundos_diferencas < 0){
					
					$dias_diferenca_revisao = "?";
				
				}else{
				
					$dias_diferenca_revisao = round($segundos_diferencas /(60 * 60 * 24),0);
				
				}
				
				$qtdFotosTotal = isset($mapFotos[$veiculo['id']]) ? $mapFotos[$veiculo['id']] : 0;
				
				if($dias_diferenca <= $arrCores[0]['verde_estoque']){
				
					$statusEstoque = "class='verde'";
					
				}elseif($dias_diferenca <= $arrCores[0]['amarelo_estoque'] && $dias_diferenca > $arrCores[0]['verde_estoque']){
				
					$statusEstoque = "class='amarelo'";
					
				}elseif($dias_diferenca > $arrCores[0]['amarelo_estoque']){
				
					$statusEstoque = "class='vermelho'";
					
				}
				
				if($dias_diferenca_revisao <= $arrCores[0]['verde_revisao']){
				
					$statusRevisao = "class='verde'";
					
				}elseif($dias_diferenca_revisao <= $arrCores[0]['amarelo_revisao'] && $dias_diferenca_revisao > $arrCores[0]['verde_revisao']){
				
					$statusRevisao = "class='amarelo'";
					
				}elseif($dias_diferenca_revisao > $arrCores[0]['amarelo_revisao']){
				
					$statusRevisao = "class='vermelho'";
					
				}
				
				if($lucroReal <= $arrCores[0]['vermelho_lucro']){
				
					$statusLucro = "class='vermelho'";
					
				}elseif($lucroReal <= $arrCores[0]['amarelo_lucro'] && $lucroReal > $arrCores[0]['vermelho_lucro']){
				
					$statusLucro = "class='amarelo'";
					
				}elseif($lucroReal > $arrCores[0]['amarelo_lucro']){
				
					$statusLucro = "class='verde'";
					
				}
				
				if(!isset($arrVeiculo[$key-1]['marca']) || ($arrVeiculo[$key-1]['marca'] != $veiculo['marca'])){
				
					$strTabela .="<tr><td colspan='20' bgcolor='#BBBBBB' style='height:25px; text-align:left;'><b>".$veiculo['marca']."</b></td></tr>";

				}

				// if(strpos($veiculo['modelo'], "Gasolina") !== false) {
				// 	$veiculo['codigo'] = $veiculo['ano_modelo'].'-1';
				// }elseif(strpos($veiculo['modelo'], "lcool") !== false) {
				// 	$veiculo['codigo'] = $veiculo['ano_modelo'].'-2';
				// }elseif(strpos($veiculo['modelo'], "Diesel") !== false) {
				// 	$veiculo['codigo'] = $veiculo['ano_modelo'].'-3';
				// }else{
				// 	$veiculo['codigo'] = $veiculo['ano_modelo'].'-1';
				// }

                $veiculo['codigo'] = $this->gerarIdModeloAno($veiculo);
				
				if($veiculo['descricao_site']){
				
					$veiculo['modelo'] = $veiculo['descricao_site'];
				
				}
				
				if($veiculo['ano_fabricacao'] == "Zero" || $veiculo['ano_modelo'] == "Zero"){
				
					$stringAno = "Zero";
				
				}else{
				
					$stringAno = substr($veiculo['ano_fabricacao'],-2)."/".substr($veiculo['ano_modelo'],-2);
				
				}
				
				
				if($veiculo['consignado'] == 0){
				
					$veiculo['consignado'] = "E";
					
				}elseif($veiculo['consignado'] == 1){
					
					$veiculo['consignado'] = "C";
					
				}elseif($veiculo['consignado'] == 3){
					
					$veiculo['consignado'] = "CV";
					
				}elseif($veiculo['consignado'] == 2){
					
					$veiculo['consignado'] = "R";
				
				}else{
				
					$veiculo['consignado'] = "Erro!";
					
				}
			
				$strTabela .="<tr>
								<td id='modelo' ".$bgCor.">".substr($veiculo['modelo'],0,15)."</td>
								<td id='opcionais' ".$bgCor." width='250px'>".$opcionais."</td>
								<td id='consignado'".$bgCor." width='70px'>".$veiculo['consignado']."</td>
								<td id='comb'".$bgCor.">".substr($veiculo['combustivel'],0,4)."</td>
								<td id='cor' ".$bgCor.">".$veiculo['cor']."</td>
								<td id='ano' ".$bgCor.">".$stringAno."</td>
								<td id='placa' ".$bgCor.">".$veiculo['placa']."</td>
								<td id='td_origem' ".$bgCor.">".$veiculo['origem']."</td>
								<td id='km' ".$bgCor.">".$veiculo['km']."</td>
								<td id='venda' ".$bgCor.">R$ ".money_format("%i",$veiculo['valor_venda'])."</td>
								<td id='estoque' ".$statusEstoque.">".round($dias_diferenca,0)."</td>
								<td id='custo' ".$bgCor.">R$ ".money_format("%i",$valorDespesa+$veiculo['valor_aquisicao'])."</td>
								<td id='compra' ".$bgCor.">R$ ".money_format("%i",$veiculo['valor_aquisicao'])."</td>
								<td id='revisao' ".$bgCor.">R$ ".money_format("%i",$valorDespesa)."</td>
								<td id='lucro' ".$statusLucro.">R$ ".money_format("%i",$lucroReal)."</td>
								<td id='lucro_porcento' ".$statusLucro.">".round($lucroPorcento,2)."%</td>
								<td id='Dcompra' ".$bgCor.">".implode("/",array_reverse(explode("-",$veiculo['data_aquisicao'])))."</td>
								<td id='termino_revisao' ".$bgCor.">".$dataTerminoRevisao."</td>
								<td id='em_revisao' ".$statusRevisao.">".$dias_diferenca_revisao."</td>
								<td id='".$veiculo['cod_fipe']."_".$veiculo['codigo']."' class='fipe' ".$bgCor." style='background-color:#4876FF; color:#FFFFFF; width: 90px;'>Aguarde...</td>
								<td id='fotos' ".$bgCor.">".$qtdFotosTotal."</td>
								<td style='display: none;' class='cod_fipe'> ".$veiculo['cod_fipe']." </td>
								<td style='display: none;' class='codigo_ano'> ".$veiculo['codigo']." </td>
								<td style='display: none;' class='segmento'> ".$veiculo['segmento']." </td>
								
							</tr>";
							 
				$somaVenda += $veiculo['valor_venda'];
				$somaDiasEstoque += $dias_diferenca;
				$somaCusto += $valorDespesa+$veiculo['valor_aquisicao'];
				$somaCompra += $veiculo['valor_aquisicao'];
				$somaRevisao += $valorDespesa;
				$somaLucro += $lucroReal;
				$somaLucroPorcento += $lucroPorcento;
				$somaFIPE += $veiculo['fipe'];
				
			
			}
		
			$totalMedia = "<tr>
							<td colspan='9' style='text-align:right; border:solid 1px;'><b>M&Eacute;DIA</b></td>
							<td style='border:solid 1px;'>R$ ".money_format("%i",$somaVenda/$count)."</td>
							<td style='border:solid 1px;'>".round($somaDiasEstoque/$count,0)."</td>
							<td style='border:solid 1px;'>R$ ".money_format("%i",$somaCusto/$count)."</td>
							<td style='border:solid 1px;'>R$ ".money_format("%i",$somaCompra/$count)."</td>
							<td style='border:solid 1px;'>R$ ".money_format("%i",$somaRevisao/$count)."</td>
							<td style='border:solid 1px;'>R$ ".money_format("%i",$somaLucro/$count)."</td>
							<td style='border:solid 1px;'>".round($somaLucroPorcento/$count,2)."%</td>
							<td></td>
							<td></td>
							<td></td>
							<td style='border:solid 1px;'>R$ ".money_format("%i",$somaFIPE/$count)."</td>
							<td></td>
						   </tr>";
						   
						   
			$totalSoma = "<tr>
							<td colspan='9' style='text-align:right; border:solid 1px;'><b>TOTAL</b></td>
							<td style='border:solid 1px;'>R$ ".money_format("%i",$somaVenda)."</td>
							<td></td>
							<td style='border:solid 1px;'>R$ ".money_format("%i",$somaCusto)."</td>
							<td style='border:solid 1px;'>R$ ".money_format("%i",$somaCompra)."</td>
							<td style='border:solid 1px;'>R$ ".money_format("%i",$somaRevisao)."</td>
							<td style='border:solid 1px;'>R$ ".money_format("%i",$somaLucro)."</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						   </tr>";
			
			
			$strTabela .= $totalMedia.$totalSoma."</table>";
			$this->view->arrEstoque = $strTabela;
			$this->view->arrQtdVeiculos = count($arrVeiculo);

		}

	}
	

    public function gerarIdModeloAno(array $veiculo): string
    {
        // Normaliza o modelo para evitar problemas com acentos/maiúsculas
        $modelo = mb_strtolower($veiculo['modelo'], 'UTF-8');

        // Ajusta ano 32000 para veículo 0 km
        $ano = ($veiculo['ano_modelo'] == 32000) ? '0km' : $veiculo['ano_modelo'];

        // Mapeamento de combustível → código
        $combustiveis = [
            'gasolina'      => 1,
            'álcool'        => 2,
            'alcool'        => 2,   // variação sem acento
            'etanol'        => 2,
            'diesel'        => 3,
            'elétrico'      => 4,
            'eletrico'      => 4,   // sem acento
            'flex'          => 5,
            'híbrido'       => 6,
            'hibrido'       => 6,   // sem acento
            'gás-natural'   => 7,
            'gas-natural'   => 7,   // sem acento
            'gnv'           => 7,
        ];

        $codigoCombustivel = 1; // padrão caso não encontre

        foreach ($combustiveis as $palavra => $codigo) {
            if (strpos($modelo, $palavra) !== false) {
                $codigoCombustivel = $codigo;
                break;
            }
        }

        return $ano . '-' . $codigoCombustivel;
    }

	
	public function listaEstoqueVendedoresAction(){
	
		//$this->validaAcesso('relatorios');
	
		$dbVeiculo = new Application_Model_DbTable_Veiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbCoresRelatorios = new Application_Model_DbTable_CoresRelatorios();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		
		$arrCores = $dbCoresRelatorios->_get($arr);
		
		$arr['vend'] = 1;
		$arr['order'] = 'marca';
		$arr['exibir_estoque'] = true;
		
		$arrVeiculo = $dbVeiculo->_get($arr);
		
		
		$strTabela = "<table>";
		
		$strTabela .= "<tr>
						<th id='th_placa' height='20px' style='vertical-align:middle;'><center>PLACA</center></th>
						<th id='th_veiculo' height='20px' style='vertical-align:middle;'><center>VE&Iacute;CULO</center></th>
						<th height='20px' style='vertical-align:middle;'><center>ANO</center></th>
						<th height='20px' style='vertical-align:middle;'><center>COR</center></th>
						<th height='20px' style='vertical-align:middle;'><center>COMB</center></th>
						<th height='20px' style='vertical-align:middle;'><center>KM</center></th>
						<th height='20px' style='vertical-align:middle;'><center>CONSIGNADO</center></th>
						<th height='20px' style='vertical-align:middle;'><center>OPCIONAIS</center></th>
						<th id='th_preco' height='20px' style='vertical-align:middle;'><center>PRE&Ccedil;O(R$)</center></th>
					  </tr>";
		$count = 0;
		
		foreach($arrVeiculo as $key=>$veiculo){
		
			$count++;
		
			$opcionais = "";
		
			$arrOpcionais = $dbOpcionaisVeiculos->getVeiculosOpcionais($veiculo['id']);
			
			foreach($arrOpcionais as $opcional){
			
				$tempOpcional = explode(")",$opcional['opcional']);
	
				$opcionais .= str_replace("(","",$tempOpcional[0])." ";
			
			}
			
			$opcionais = substr($opcionais,0,-1);
			
			if(!isset($arrVeiculo[$key-1]['marca']) || $arrVeiculo[$key-1]['marca'] != $veiculo['marca']){
			
				$strTabela .="<tr><td colspan='9' height='15px' style='background-color:#BBBBBB; text-align:left; font-size:12px;'><b>&nbsp;".strtoupper($veiculo['marca'])."</b></td></tr>";
			
			}
			
			if($veiculo['descricao_site']){
			
				$veiculo['modelo'] = $veiculo['descricao_site'];
			
			}
		
			if($count%2 == 0){
			
				$corBg = " bgcolor='#DDDDDD' ";
				//$corBg = " bgcolor='silver' ";
			
			}else{
			
				$corBg = "";
			
			}
			
			if($veiculo['consignado'] == 0){
			
				$veiculo['consignado'] = "E";
				
			}elseif($veiculo['consignado'] == 1){
				
				$veiculo['consignado'] = "C";
				
			}elseif($veiculo['consignado'] == 3){
				
				$veiculo['consignado'] = "CV";
				
			}elseif($veiculo['consignado'] == 2){
				
				$veiculo['consignado'] = "R";
			
			}else{
			
				$veiculo['consignado'] = "Erro!";
				
			}
			
			
			if($veiculo['ano_fabricacao'] == "Zero" || $veiculo['ano_modelo'] == "Zero"){
			
				$stringAno = "Zero";
			
			}else{
			
				$stringAno = substr($veiculo['ano_fabricacao'],-2)."/".substr($veiculo['ano_modelo'],-2);
			
			}
		
			$strTabela .="<tr>
							<td ".$corBg.">".$veiculo['placa']."</td>
							<td ".$corBg.">".substr($veiculo['modelo'],0,20)."</td>
							<td ".$corBg.">".$stringAno."</td>
							<td ".$corBg.">".$veiculo['cor']."</td>
							<td ".$corBg.">".substr($veiculo['combustivel'],0,1)."</td>
							<td ".$corBg.">".$veiculo['km']."</td>
							<td ".$corBg.">".$veiculo['consignado']."</td>
							<td ".$corBg.">".$opcionais."</td>
							<td ".$corBg.">R$ ".money_format("%i",$veiculo['valor_venda'])."</td>
						 </tr>";
		
		}
		
		$this->view->arrEstoque = $strTabela."</table>";
		$this->view->qtdVeiculos = count($arrVeiculo);
	
	}
	
	
	public function despesasAction(){

		$this->validaAcesso('relatorios');
		
		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
		$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
		$dbFinanceiras = new Application_Model_DbTable_Financeiras();
		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		$dbItensFinanceiros = new Application_Model_DbTable_ItensFinanceiros();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arr['tipo_grupo'] = true;
		$arr['credito_debito'] = 1;
		$somaGrupo = 0;
		$strGrupo = "";
		$subtotal = "";
		$strGrupos = "";
		$somaGrupos = 0;
		$panGrupos = 0;
		$totalCount = 0;
		$strTotal = "";
		
		$this->view->gruposFinanceiros = $dbGruposFinanceiros->_get($arr);
		$this->view->itens = $dbItensFinanceiros->getItensDistintos($_SESSION['sessionUser']['id_empresa']);
		
		$strTabela = "<table>
					 <tr>
						<th class='cabeca' style='vertical-align:middle; height:20px; font-size:13px;'>GRUPO</th>
						<th class='cabeca' style='vertical-align:middle; height:20px; font-size:13px;'>ITEM</th>
						<th class='cabeca' style='vertical-align:middle; height:20px; font-size:13px;'>DATA</th>
						<th class='cabeca' style='vertical-align:middle; height:20px; font-size:13px;'>BAIXADO</th>
						<th class='cabeca' style='vertical-align:middle; height:20px; font-size:13px;'>VALOR</th>
					 </tr>";
		
		if($this->getRequest()->isPost()){

			$arrBusca['data_inicial'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
			$arrBusca['data_final'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));
			$arrBusca['credito'] = false;
			if(isset($_POST['grupo']) && $_POST['grupo'] != "0"){
				$arrBusca['id_grupo'] = $_POST['grupo'];

			}
			if(isset($_POST['item']) && $_POST['item'] != "0"){
				$arrBusca['item'] = $_POST['item'];
			}
			$arrBusca['baixado'] = $_POST['baixado'];
			$arrBusca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
				
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arrBusca);


			$panGrupo = 0;
			$countCor = 0;
				
			foreach($arrGruposFinanceiros as $key=>$gruposFinanceiro){
				
				if($countCor%2 == 0){
				
					$corBg = " bgcolor='silver' ";
				
				}else{
				
					$corBg = "";
				
				}
			
				if($gruposFinanceiro['baixado'] == 1){
				
					$baixado = "Sim";
				
				}else{
				
					$baixado = "N&atilde;o";
				
				}
			
				$panGrupo++;
				$somaGrupo += $gruposFinanceiro['valor'];
						
				if(!isset($arrGruposFinanceiros[$key+1]['id_grupo']) || $arrGruposFinanceiros[$key+1]['id_grupo'] != $gruposFinanceiro['id_grupo']){
							
					$rowspan = "<tr>
									<td rowspan='".$panGrupo."'>".$gruposFinanceiro['descricao']."</td>
									<td >".$gruposFinanceiro['item']."</td>
									<td >".implode("/",array_reverse(explode("-",$gruposFinanceiro['data_lancamento'])))."</td>
									<td >".$baixado."</td>
									<td >R$ ".money_format("%i",$gruposFinanceiro['valor'])."</td>
								</tr>";
											
					if(isset($_POST['grupo']) && $_POST['grupo'] == 0){
							
						$subtotal = "<tr><th colspan='4' class='cabeca' style='text-align:right; vertical-align:middle; height:20px; font-size:13px;'>SUBTOTAL</th><th style='text-align:left; vertical-align:middle; height:20px; font-size:13px;' class='cabeca'>R$ ".money_format("%i",$somaGrupo)."</th></tr>";
				
					}
		
					$strGrupos .= $rowspan.$strGrupo.$subtotal;
					
					$somaGrupos += $somaGrupo;
					$panGrupos += $panGrupo;
			
					$rowspan="";
					$strGrupo="";
					$subtotal="";
					$panGrupo = 0;
					$somaGrupo=0;
					$countCor = 1;
				
				}else{
							
					$strGrupo .= "<tr>
									<td ".$corBg." >".$gruposFinanceiro['item']."</td>
									<td ".$corBg." >".implode("/",array_reverse(explode("-",$gruposFinanceiro['data_lancamento'])))."</td>
									<td ".$corBg." >".$baixado."</td>
									<td ".$corBg." >R$ ".money_format("%i",$gruposFinanceiro['valor'])."</td>
								</tr>";

				}
				
				$countCor++;

			}
			
		}else{

			$_POST['grupo'] = 0;
			
			$arrBusca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$arrBusca['credito'] = false;
			$arrBusca['data_inicial'] = @date("Y")."-".@date("m")."-01";
			
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arrBusca);
			
			$panGrupo = 0;
			$countCor = 0;
			
			foreach($arrGruposFinanceiros as $key=>$gruposFinanceiro){
				
				if($countCor%2 == 0){
				
					$corBg = " bgcolor='silver' ";
					//$corBg = " style='background-color: #DDDDDD;'";
				
				}else{
				
					$corBg = "";
				
				}
			
				if($gruposFinanceiro['baixado'] == 1){
				
					$baixado = "Sim";
				
				}else{
				
					$baixado = "N&atilde;o";
				
				}
		
				$panGrupo++;
				$somaGrupo += $gruposFinanceiro['valor'];

				if(!isset($arrGruposFinanceiros[$key+1]['id_grupo']) || $arrGruposFinanceiros[$key+1]['id_grupo'] != $gruposFinanceiro['id_grupo']){
						
					$rowspan = "<tr>
									<td rowspan='".$panGrupo."'>".$gruposFinanceiro['descricao']."</td>
									<td >".$gruposFinanceiro['item']."</td>
									<td >".implode("/",array_reverse(explode("-",$gruposFinanceiro['data_lancamento'])))."</td>
									<td >".$baixado."</td>
									<td >R$ ".money_format("%i",$gruposFinanceiro['valor'])."</td>
								</tr>";
										



					//if(isset($_POST['grupo']) && $_POST['grupo'] == 0){
						
						$subtotal = "<tr><th colspan='4' class='cabeca' style='text-align:right; vertical-align:middle; height:20px; font-size:13px;'>SUBTOTAL</th><th style='text-align:left; vertical-align:middle; height:20px; font-size:13px;' class='cabeca'>R$ ".money_format("%i",$somaGrupo)."</th></tr>";
							
					//}
						
						
				
					$strGrupos .= $rowspan.$strGrupo.$subtotal;
						
					$somaGrupos += $somaGrupo;
					$panGrupos += $panGrupo;
						
					$rowspan="";
					$strGrupo="";
					$subtotal="";
					$panGrupo = 0;
					$somaGrupo=0;
					$countCor = 1;

			
				}else{
						
					$strGrupo .= "<tr>
									<td ".$corBg." >".$gruposFinanceiro['item']."</td>
									<td ".$corBg.">".implode("/",array_reverse(explode("-",$gruposFinanceiro['data_lancamento'])))."</td>
									<td ".$corBg.">".$baixado."</td>
									<td ".$corBg.">R$ ".money_format("%i",$gruposFinanceiro['valor'])."</td>
								</tr>";

				}

				$countCor++;

			}
	
		}
		
		$total = $somaGrupos;
		$totalCount = $panGrupos;

		if($totalCount != 0){
		
			$strTotal .= "<tr><td colspan='4' style='border:solid 2px; text-align:right;'>M&Eacute;DIA</td><td style='border:solid 2px;'>R$ ".money_format("%i",$total/$totalCount)."</td></tr>";
		
		}
		
		$strTotal .= "<tr><td colspan='4' style='border:solid 2px; text-align:right;'>TOTAL</td><td style='border:solid 2px;'>R$ ".money_format("%i",$total)."</td></tr>";

		
		$strTabela .= $strGrupos.$strTotal;
		
		$strTabela = "<div style='margin-left:10px; margin-bottom:10px; font-family: Arial, Helvetica, sans-serif;'><b>Quantidade de Despesas: ".$totalCount."</b></div>".$strTabela;
		
		$this->view->idGrupo = $_POST['grupo'];

		if(isset($_POST['item'])){
			$this->view->item = $_POST['item'];
		}

		if(isset($_POST['baixado'])){
			$this->view->idBaixado = $_POST['baixado'];
		}

		if(isset($_POST['baixado'])){
			$this->view->dataInicial = $_POST['data_inicial'];
		}

		if(isset($_POST['baixado'])){
			$this->view->dataFinal = $_POST['data_final'];
		}

		$this->view->relatorio = $strTabela."</table>";
	

	}
	
	public function financiamentosAction(){
	
		$this->validaAcesso('relatorios');
	
		$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
		$dbVendedores = new Application_Model_DbTable_Vendedores();
		$dbFinanceiras = new Application_Model_DbTable_Financeiras();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arrFiltro['tipo'] = 0;
		
		$this->view->financeiras = $dbFinanceiras->_get($arrFiltro);
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

		$this->view->vendedores = $dbVendedores->_get($arr);
	
		$arrBusca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		if(isset($_POST['data_inicial']) && $_POST['data_inicial'] != ""){
			$arrBusca['data_inicial_concretizacao'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
		}else{
			$arrBusca['data_inicial_concretizacao'] = @date("Y")."-".(@date("m")-1)."-01";
		}
		if(isset($_POST['data_final']) && $_POST['data_final'] != ""){
			$arrBusca['data_final_concretizacao'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));
		}else{
			$arrBusca['data_final_concretizacao'] = @date("Y-m")."-".cal_days_in_month(CAL_GREGORIAN, @date("m") , @date("Y"));

		}
		if(isset($_POST['id_vendedor']) && $_POST['id_vendedor'] != 0){
			$arrBusca['id_vendedor'] = $_POST['id_vendedor'];
		}
		if(isset($_POST['id_financeira']) && $_POST['id_financeira'] != 0){
			$arrBusca['id_financeira'] = $_POST['id_financeira'];
		}
		$arrBusca['aprovada'] = 1;
		$arrBusca['relatorio_fi'] = 1;
		
		
		$strTabela = "<table>
			 <tr>
				<th style='text-align:center; vertical-align:middle; height:30px; font-size:12px;' class='cabeca'>Financeira</th>
				<th style='text-align:center; vertical-align:middle; height:30px; font-size:12px;' class='cabeca'>Vendedor</th>
				<th style='text-align:center; vertical-align:middle; height:30px; font-size:12px;' class='cabeca'>Ve&iacute;culo</th>
				<th style='text-align:center; vertical-align:middle; height:30px; font-size:12px;' class='cabeca'>Placa</th>
				<th style='text-align:center; vertical-align:middle; height:30px; font-size:12px;' class='cabeca'>Data</th>
				<th style='text-align:center; vertical-align:middle; height:30px; font-size:12px;' class='cabeca'>Valor Venda</th>
				<th style='text-align:center; vertical-align:middle; height:30px; font-size:12px;' class='cabeca'>Valor Financiado</th>
				<th style='text-align:center; vertical-align:middle; height:30px; font-size:12px;' class='cabeca'>F&I </th>
			</tr>";
	
		$panFi = 0;
		$countCor = 0;
		$somaFi = 0;
		$somaValorFi = 0;
		$somaValorVenda = 0;
		$strf = "";
		$strfI = "";
		$panFis = 0;
		$somaFis = 0;
		$somaValorFis = 0;
		$somaValorVendas = 0;
		$subtotal = "";
		$strTotal = "";
		
		if($this->getRequest()->isPost()){
	
			$arrNegociacoesFi = $dbNegociacoes->getFinanciamentos($arrBusca);
		

			foreach($arrNegociacoesFi as $key=>$negociacaoFi){
		
				$dataTemp = explode(" ",$negociacaoFi['data_concretizacao']);
				$negociacaoFi['data'] = $dataTemp[0];
				
				if($negociacaoFi['descricao_site']){
				
					$negociacaoFi['modelo'] = $negociacaoFi['descricao_site'];
				
				}
				
				if($negociacaoFi['id_financeira']){
				
				
					if($countCor%2 == 0){
				
						$corBg = " bgcolor='#DDDDDD' ";
				
					}else{
				
						$corBg = "";
				
					}
			
					$panFi++;
					$somaFi += ((($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100)-$negociacaoFi['imposto_financeira'];
					$somaValorFi += $negociacaoFi['valor_financiado'];
					$somaValorVenda += $negociacaoFi['valor_venda'];
			
					if(!isset($arrNegociacoesFi[$key+1]['id_financeira']) || ($arrNegociacoesFi[$key+1]['id_financeira'] != $negociacaoFi['id_financeira'])){
				
						$rowspan = "<tr>
										<td class='tds' rowspan='".$panFi."'>".$negociacaoFi['nome']."</td>
										<td class='tds'>".substr($negociacaoFi['nomeUsuario'],0,20)."</td>
										<td class='tds'>".substr($negociacaoFi['modelo'],0,18)."</td>
										<td class='tds'>".$negociacaoFi['placa']."</td>
										<td class='tds'>".implode("/",array_reverse(explode("-",$negociacaoFi['data'])))."</td>
										<td class='tds'>R$ ".money_format("%i",$negociacaoFi['valor_venda'])."</td>
										<td class='tds'>R$ ".money_format("%i",$negociacaoFi['valor_financiado'])."</td>
										<td class='tds'>R$ ".money_format("%i",((($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100)-$negociacaoFi['imposto_financeira'])."</td>
									</tr>";
						
						if($_POST['id_financeira'] == 0){
					
							$subtotal = "<tr><th colspan='5' class='cabeca' style='text-align:right; vertical-align:middle; height:13px; font-size:11px;'>SUBTOTAL</th><th class='cabeca' style='text-align:right; vertical-align:middle; height:13px; font-size:11px;'>R$ ".money_format("%i",$somaValorVenda)."</th><th class='cabeca' style='text-align:right; vertical-align:middle; height:13px; font-size:11px;'>R$ ".money_format("%i",$somaValorFi)."</th><th class='cabeca' style='text-align:right; vertical-align:middle; height:13px; font-size:11px;'>R$ ".money_format("%i",$somaFi)."</th></tr>";
						
						}
				
						$strfI .= $rowspan.$strf.$subtotal;
						
						$panFis += $panFi;
						$somaFis += $somaFi;
						$somaValorFis += $somaValorFi;
						$somaValorVendas += $somaValorVenda;
						
						$panFi = 0;
						$somaFi = 0;
						$somaValorFi = 0;
						$somaValorVenda = 0;
						$strf = "";
						$countCor = 1;
						
					
					}else{
			
						$strf .= "<tr>
										<td ".$corBg." class='tds'>".substr($negociacaoFi['nomeUsuario'],0,20)."</td>
										<td ".$corBg." class='tds'>".substr($negociacaoFi['modelo'],0,18)."</td>
										<td ".$corBg." class='tds'>".$negociacaoFi['placa']."</td>
										<td ".$corBg." class='tds'>".implode("/",array_reverse(explode("-",$negociacaoFi['data'])))."</td>
										<td ".$corBg." class='tds'>R$ ".money_format("%i",$negociacaoFi['valor_venda'])."</td>
										<td ".$corBg." class='tds'>R$ ".money_format("%i",$negociacaoFi['valor_financiado'])."</td>
										<td ".$corBg." class='tds'>R$ ".money_format("%i",((($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100)-$negociacaoFi['imposto_financeira'])."</td>
									</tr>";
					}
					
					$countCor++ ;
					
				}

			}
		
		}else{
			
			$arrBusca['data_inicial_concretizacao'] = @date("Y")."-".(@date("m")-1)."-01";
			
			$arrNegociacoesFi = $dbNegociacoes->getFinanciamentos($arrBusca);
		
			$panFi = 0;
			$strf = "";
			$strfI = "";
			$panFis = 0;
			$somaFis = 0;
			$somaValorFis = 0;
			$somaValorVendas = 0;
			$subtotal = "";
			$strTotal = "";

			foreach($arrNegociacoesFi as $key=>$negociacaoFi){
			
				if(isset($negociacaoFi['descricao_site'])){
				
					$negociacaoFi['modelo'] = $negociacaoFi['descricao_site'];
				
				}
		
				$dataTemp = explode(" ",$negociacaoFi['data_concretizacao']);
				$negociacaoFi['data'] = $dataTemp[0];
				
				if(isset($negociacaoFi['id_financeira'])){
				
				
					if($countCor%2 == 0){
				
						$corBg = " bgcolor='#DDDDDD' ";
				
					}else{
				
						$corBg = "";
				
					}
			
					$panFi++;
					$somaFi += ((($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100)-$negociacaoFi['imposto_financeira'];
					$somaValorFi += $negociacaoFi['valor_financiado'];
					$somaValorVenda += $negociacaoFi['valor_venda'];
			
					if(!isset($arrNegociacoesFi[$key+1]['id_financeira']) || ($arrNegociacoesFi[$key+1]['id_financeira'] != $negociacaoFi['id_financeira'])){
				
						$rowspan = "<tr>
										<td id='tds' rowspan='".$panFi."'>".$negociacaoFi['nome']."</td>
										<td id='tds'>".$negociacaoFi['nomeUsuario']."</td>
										<td id='tds'>".$negociacaoFi['modelo']."</td>
										<td id='tds'>".$negociacaoFi['placa']."</td>
										<td id='tds'>".implode("/",array_reverse(explode("-",$negociacaoFi['data'])))."</td>
										<td id='tds'>R$ ".money_format("%i",$negociacaoFi['valor_venda'])."</td>
										<td id='tds'>R$ ".money_format("%i",$negociacaoFi['valor_financiado'])."</td>
										<td id='tds'>R$ ".money_format("%i",((($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100)-$negociacaoFi['imposto_financeira'])."</td>
									</tr>";
						
						if(isset($_POST['id_financeira']) && $_POST['id_financeira'] == 0){
					
							$subtotal = "<tr><th colspan='5' class='cabeca' style='text-align:right; vertical-align:middle; height:13px; font-size:11px;'>SUBTOTAL</th><th class='cabeca' style='text-align:right; vertical-align:middle; height:13px; font-size:11px;'>R$ ".money_format("%i",$somaValorVenda)."</th><th class='cabeca' style='text-align:right; vertical-align:middle; height:13px; font-size:11px;'>R$ ".money_format("%i",$somaValorFi)."</th><th class='cabeca' style='text-align:right; vertical-align:middle; height:13px; font-size:11px;'>R$ ".money_format("%i",$somaFi)."</th></tr>";
						
						}
				
						$strfI .= $rowspan.$strf.$subtotal;
						
						$panFis += $panFi;
						$somaFis += $somaFi;
						$somaValorFis += $somaValorFi;
						$somaValorVendas += $somaValorVenda;
						
						$panFi = 0;
						$somaFi = 0;
						$somaValorFi = 0;
						$somaValorVenda = 0;
						$strf = "";
						
					
					}else{
			
						$strf .= "<tr>
										<td ".$corBg." id='tds'>".$negociacaoFi['nomeUsuario']."</td>
										<td ".$corBg." id='tds'>".$negociacaoFi['modelo']."</td>
										<td ".$corBg." id='tds'>".$negociacaoFi['placa']."</td>
										<td ".$corBg." id='tds'>".implode("/",array_reverse(explode("-",$negociacaoFi['data'])))."</td>
										<td ".$corBg." id='tds'>R$ ".money_format("%i",$negociacaoFi['valor_venda'])."</td>
										<td ".$corBg." id='tds'>R$ ".money_format("%i",$negociacaoFi['valor_financiado'])."</td>
										<td ".$corBg." id='tds'>R$ ".money_format("%i",((($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100)-$negociacaoFi['imposto_financeira'])."</td>
									</tr>";
					}
				}

				$countCor++ ;

			}
		
		}
		
		$strTabela .= $strfI;
		
		if($panFis != 0){
		
			$strTotal .= "<tr><td colspan='5' style='border:solid 2px; text-align:right;'>M&Eacute;DIA</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaValorVendas/$panFis)."</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaValorFis/$panFis)."</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaFis/$panFis)."</td></tr>";
		
		}
		
		$strTotal .= "<tr><td colspan='5' style='border:solid 2px; text-align:right;'>TOTAL</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaValorVendas)."</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaValorFis)."</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaFis)."</td></tr>";
	
		$strTabela = "<div style='margin-left:10px; margin-bottom:10px; font-family: Arial, Helvetica, sans-serif;'><b>Quantidade de Financiamentos: ".$panFis."</b></div>".$strTabela.$strTotal."</table>";

		if(isset($_POST['id_vendedor'])){
			$this->view->id_vendedor = $_POST['id_vendedor'];
		}
		if(isset($_POST['id_financeira'])){
			$this->view->id_financeira = $_POST['id_financeira'];
		}
		if(isset($_POST['data_inicial'])){
			$this->view->dataInicial = $_POST['data_inicial'];
		}
		if(isset($_POST['data_final'])){
			$this->view->dataFinal = $_POST['data_final'];
		}
		$this->view->relatorio = $strTabela;
	
	}
	
	public function relatorioXlsAction(){
	
		$this->validaAcesso('relatorios');
		
		$dbVendedores = new Application_Model_DbTable_Vendedores();
		$dbCoresRelatorios = new Application_Model_DbTable_CoresRelatorios();
		$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
		$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		$dbGarantias = new Application_Model_DbTable_Garantias();

		$arrBusca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arrBuscaGarantia['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arrBusca['aprovada'] = 1;
		$arrBusca['order_usuarios'] = true;
		$arrBusca['id_vendedor'] = $this->_getParam('id_vendedor');
		
		if($this->_getParam('data_inicial') != "" || $this->_getParam('data_final') != ""){
		
			$arrBusca['data_inicial_concretizacao'] = $this->_getParam('data_inicial');
			$arrBusca['data_final_concretizacao'] = $this->_getParam('data_final');
			$arrBuscaGarantia['data_inicial'] = $this->_getParam('data_inicial');
			$arrBuscaGarantia['data_final'] = $this->_getParam('data_final');
			$arrBusca['data_inicial'] = $this->_getParam('data_inicial');
			$arrBusca['data_final'] = $this->_getParam('data_final');
			
		}else{
			
			$arrBusca['data_inicial_concretizacao'] = @date("Y")."-". @date("m")."-01";
			$arrBusca['data_inicial'] = @date("Y")."-". @date("m")."-01";
			$arrBuscaGarantia['data_inicial'] = @date("Y")."-". @date("m")."-01";
		
		}
		
		$arrNegociacoes = $dbNegociacoes->_get($arrBusca);
			
		foreach($arrNegociacoes as $key=>$valor){
			
			$valorDespesas = $dbDespesasVeiculos->getSomaDespesas($arrNegociacoes[$key]['id_veiculo']);
			 
			$arrNegociacoes[$key]['valor_despesas'] = $valorDespesas[0]['valor_despesas'];
			
		}
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrCores = $dbCoresRelatorios->_get($arr);
		
		$this->view->gruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupo($arrBusca);		
		$this->view->arrCores = $arrCores[0];	
		$this->view->arrNegociacoes = $arrNegociacoes;
		$this->view->nomeVendedor = $this->_getParam('nome_vendedor');
		$this->view->dataInicial = $this->_getParam('data_inicial');
		$this->view->dataFinal = $this->_getParam('data_final');
		$this->view->garantia = $dbGarantias->getGarantias($arrBuscaGarantia);
		
		///////////////////////////INICIO TABELAS MESES///////////////////////////////////////
			
			foreach($arrNegociacoes as $negociacao){
			
				$datas = explode(" ",$negociacao['data_concretizacao']);
				$data = explode("-",$datas[0]);
				
				$arrStr[$data[1]]['Lucro Bruto dos Carros-0'] += ($negociacao['valor_venda']-($negociacao['valor_despesas']+$negociacao['valor_aquisicao']));
			
				if($negociacao['id_financeira']){
				
					$arrStr[$data[1]]['F&I-0'] += (($negociacao['retorno_financeira']*1.2)*$negociacao['valor_financiado'])/100;
				
				}
				
				$arrStr[$data[1]]['Despachantes-0'] += $negociacao['imposto'];
			
			}
			
			$arrBusca['credito'] = true;
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arrBusca);
			
			foreach($arrGruposFinanceiros as $gruposFinanceiro){
			
				$data = explode("-",$gruposFinanceiro['data_lancamento']);
			
				$arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']] += $gruposFinanceiro['valor'];
			
			}
			
			$arrBusca['credito'] = false;
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arrBusca);
			
			foreach($arrGruposFinanceiros as $gruposFinanceiro){
			
				$data = explode("-",$gruposFinanceiro['data_lancamento']);
			
				$arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']] += $gruposFinanceiro['valor'];
			
			}
			
			
			$arrGarantias = $dbGarantias->getGarantiasIndividual($arrBuscaGarantia);
			
			foreach($arrGarantias as $garantia){
			
				$data = explode("-",$garantia['data_saida']);
				
				$arrStr[$data[1]]['Garantias-1'] += $garantia['custo'];
				
			}
			
			foreach($arrStr as $mes=>$str){
				
				$totalCreditos = 0;
				$totalDebitos = 0;
				
				$arrTabelas[$mes]['tabela'] .= "<table class='table' style='width:70%;'>
												<thead>
													<tr>
														<td colspan='2'>
															<center><label style='font-size:15px;'><b>Resultado Final - ".$this->mesExtenso($mes)."<b></label></center>
														</td>
													</tr>
													<tr>
														<th class='cabeca'>Item</th>
														<th class='cabeca'>Valor</th>
													</tr>
												</thead>";
			
				foreach($str as $indice=>$valor){
			
					$cd = explode("-", $indice);
					
					if($cd[1] == 0){
					
						$corCreditoDebito = "class='verde'";
						$totalCreditos += $valor;
						$credito = true;
						
					}elseif($cd[1] == 1){
					
						if($credito){
							$arrTabelas[$mes]['tabela'] .= "<tr>
														<td style='border-bottom:solid 1px; border-top:solid 1px; border-left:solid 1px;' ".$corCreditoDebito."><b>TOTAL CR&Eacute;DITOS</b></td>
														<td style='border-bottom:solid 1px; border-top:solid 1px; border-right:solid 1px;' ".$corCreditoDebito."><b>R$ ".money_format("%i",$totalCreditos)."</b></td>
													</tr>";
						
						}
					
						$totalDebitos += $valor;
						$corCreditoDebito = "class='vermelho'";
						$credito = false;
						
					}
			
					$arrTabelas[$mes]['tabela'] .= "<tr>
														<td ".$corCreditoDebito.">".$cd[0]."</td>
														<td ".$corCreditoDebito.">R$ ".money_format("%i",$valor)."</td>
													</tr>";
					
					
				}
				
				$arrTabelas[$mes]['tabela'] .= "<tr>
													<td class='vermelho' style='border-bottom:solid 1px; border-top:solid 1px; border-left:solid 1px;'><b>TOTAL D&Eacute;BITOS</b></td>
													<td class='vermelho' style='border-bottom:solid 1px; border-top:solid 1px; border-right:solid 1px;'><b>R$ ".money_format("%i",$totalDebitos)."</b></td>
												</tr>
												<tr>
													<td style='border-bottom:solid 2px; border-top:solid 2px; border-left:solid 2px;'><b>TOTAL</b></td>
													<td style='border-bottom:solid 2px; border-top:solid 2px; border-right:solid 2px;'><b>R$ ".money_format("%i",$totalCreditos-$totalDebitos)."</b></td>
												</tr>";
				
				$arrTabelas[$mes]['tabela'] .= "</table>";
			
			}
			
			$this->view->arrTabelas = $arrTabelas;
			
///////////////////////////FIM TABELAS MESES///////////////////////////////////////
	
	}
	
	public function gerarXlsOrigemAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
	
		//$this->validaAcesso('relatorios');
	
		$this->view->xls = $this->_getParam('xls');
		$this->view->dataInicial = "'".$this->_getParam('data_inicial')."'";
		$this->view->dataFinal = "'".$this->_getParam('data_final')."'";
		$this->view->idOrigem =  $this->_getParam('id_origem');
		
		//echo $this->view->dataInicial;
	
	}
	

	public function gerarXlsAction(){
	
		//$this->validaAcesso('relatorios');
	
		$this->view->relatorio = $_POST['relatorio'];
		$this->view->dataInicial = $_POST['data_inicial'];
		$this->view->dataFinal = $_POST['data_final'];
		$this->view->tipoRelatorio =  $_POST['tipo_relatorio'];
	
	}
	
	private function calculaFolhaPagamento($dataInicial, $dataFinal){

		$dbUsuarios = new Application_Model_DbTable_Usuarios();
		$dbNegociacoes = new Application_Model_DbTable_Negociacoes();

		$totalFixo = 0;
		$totalComissao = 0;
		$totalComissaoRetorno = 0;
			
			$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			
			$arrFiltroVendas['data_inicial_concretizacao'] = implode("-",array_reverse(explode("/",$dataInicial)));
			$arrFiltroVendas['data_final_concretizacao'] = implode("-",array_reverse(explode("/",$dataFinal)));
			
			$arr['data_inicial'] = implode("-",array_reverse(explode("/",$dataInicial)));
			$arr['data_final'] = implode("-",array_reverse(explode("/",$dataFinal)));

			//var_export($arr['data_inicial']." , ".$arr['data_final']."<br>");

			$arrUsuarios = $dbUsuarios->_getUsuariosNegociacoes($arr);
			
			foreach($arrUsuarios as $key=>$usuarios){

				if($usuarios['id_perfil'] == 3){
				
					$arrFiltroVendas['id_vendedor'] = $usuarios['id'];
				
				}elseif($usuarios['id_perfil'] == 9){
				
					$arrFiltroVendas['id_supervisor'] = $usuarios['id'];
				
				}elseif($usuarios['id_perfil'] == 4){
				
					$arrFiltroVendas['id_gerente'] = $usuarios['id'];
				
				}else{
					
					$arrFiltroVendas['id_vendedor'] = $usuarios['id'];
					
				}
				
				$arrVendas = $dbNegociacoes->getVendasPorUsuario($arrFiltroVendas);
				
				unset($arrFiltroVendas['id_vendedor']);
				unset($arrFiltroVendas['id_supervisor']);
				unset($arrFiltroVendas['id_gerente']);
				
				$totalFixo += $usuarios['valor_fixo_mensal'];
				
				if($arrVendas){
					
					$subtotalVenda = 0;
					$subtotalComissao = 0;
					$subtotalRetorno = 0;
					$subtotalComissaoRetorno = 0;
					
					$rowspan = count($arrVendas)+1;
					
					if(count($arrUsuarios) == 1){
						
						$cont = -1;
					
					}
					
					foreach($arrVendas as $chave=>$vendas){
						
						if($vendas['descricao_site']){
							
							$vendas['modelo'] = $vendas['descricao_site'];
						
						}
						
						$arrModelo = explode(" ",$vendas['modelo']);
						
						if(isset($arrModelo[1])){
							$vendas['modelo'] = $arrModelo[0]." ".$arrModelo[1];
						}elseif(isset($arrModelo[0])){
							$vendas['modelo'] = $arrModelo[0];
						}else{
							$vendas['modelo'] = "";
						}
						
						if($usuarios['id_perfil'] == 3){
				
							$comissaoVenda = $vendas['comissao_vendedor'];
						
						}elseif($usuarios['id_perfil'] == 9){
						
							$comissaoVenda = $vendas['comissao_supervisor'];
						
						}elseif($usuarios['id_perfil'] == 4){
						
							$comissaoVenda = $vendas['comissao_gerente'];
						
						}else{
							
							$comissaoVenda = 0;
							
						}
						
						if($usuarios['valor_fixo']){
							
							$comissaoVenda = $usuarios['valor_fixo'];
						
						}
						
						if($vendas['valor_financiado'] != 0){
							
							$comissaoRetorno = ((((($vendas['valor_financiado']*1.2)*$vendas['retorno_financeira'])/100) - $vendas['imposto_financeira'])*$usuarios['percentual_retorno_financeiro'])/100;

						}

						$totalComissao += $comissaoVenda;
						$totalComissaoRetorno += $comissaoRetorno;
						
						
						
					}
				
				}
				
			}
			
		return $totalComissaoRetorno+$totalComissao+$totalFixo;
	
	}


	private function mesExtenso($mes){
		
		switch($mes){
				
			case 1:
			return "Janeiro";
			break;
					
			case 2:
			return "Fevereiro";
			break;
					
			case 3:
			return "Mar&ccedil;o";
			break;
					
			case 4:
			return "Abril";
			break;
					
			case 5:
			return "Maio";
			break;
					
			case 6:
			return "Junho";
			break;
					
			case 7:
			return "Julho";
			break;
					
			case 8:
			return "Agosto";
			break;
					
			case 9:
			return "Setembro";
			break;
					
			case 10:
			return "Outubro";
			break;
					
			case 11:
			return "Novembro";
			break;
					
			case 12:
			return "Dezembro";
			break;
				
		}
			
	}

}

?>
