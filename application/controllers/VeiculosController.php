<?php

header("Content-Type: text/html; charset=UTF-8",true);

class VeiculosController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Ve&iacute;culos";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}


	public function periodoPreparacaoAction(){
		
		$dbPeriodoPreparacao = new Application_Model_DbTable_PeriodoPreparacao();

		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

		if($this->getRequest()->isPost()){

			$arrDados = $dbPeriodoPreparacao->getPeriodoPreparacao($arr);

			if($arrDados){
				$dbPeriodoPreparacao->edt($arr['id_empresa'], $_POST);
			}else{
				$dbPeriodoPreparacao->add($_POST);
			}

		}

		$arrDados = $dbPeriodoPreparacao->getPeriodoPreparacao($arr);

		$this->view->arrDados = $arrDados[0];
		
	}
	
	
	public function preparacaoAction(){
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbPeriodoPreparacao = new Application_Model_DbTable_PeriodoPreparacao();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arr['vendido'] = 0;
		$arr['venda'] = true;

		$this->view->arrPeriodoPreparacao = current($dbPeriodoPreparacao->getPeriodoPreparacao());
		$this->view->arrVeiculos = $dbVeiculos->getVeiculosPreparacao($arr);
		
	}
	
	
	public function impressaoRevisaoAction(){

		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		
		$arrRevisoes = $dbVeiculos->getRevisoes($this->_getParam('id_veiculo'));
		
		$cont = 0;
		$totalValor = 0;
		$strRevisoes = strtoupper("<div style='margin-left:6px; font-size:18px; font-family: arial;'>".$this->_getParam('veiculo')."  -  ".$this->_getParam('placa')."</div><br/>");
		
		$strRevisoes .= "<table class='table_impressao'>
							<tr>
								<th colspan='2'>Descrição Revisão</th>
								<th>Fornecedor</th>
								<th>Data</th>
								<th>Valor</th>
							</tr>";
	
		foreach($arrRevisoes as $arrRevisao){
			
			$cont++;
			$strRevisoes .= "<tr>
								<td>".$cont."</td>
								<td>".$arrRevisao['despesa']."</td>
								<td>".$arrRevisao['razao_social']."</td>
								<td>".implode("/", array_reverse(explode("-", $arrRevisao['data'])))."</td>
								<td>R$".money_format("%i",$arrRevisao['valor'])."</td>
							</tr>";
							
							
			$totalValor += $arrRevisao['valor'];
		
		}
		
		$strRevisoes .= "<tr style='background-color:#CCC;'><td colspan='4' style='text-align:right; font-weight:bold;'>TOTAL</td><td style='font-weight:bold;'>R$".money_format("%i",$totalValor)."</td></tr>";
		$strRevisoes .= "</table>";

		$this->view->strRevisoes = $strRevisoes;
		
	}
	
	
	public function downloadAction(){

		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
	
		$this->view->path = str_replace("|","/",$this->_getParam('path'));

	}

	public function adicionarAction(){
	
		$this->validaAcesso('gerenciar_estoque');
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbAnexosVeiculos = new Application_Model_DbTable_AnexosVeiculos();
		
		$this->view->marcas = $dbVeiculos->getMarcasDistintas();
		
		if($this->_getParam('troca') == 1){
		
			$this->view->troca = 1;
		
		}else{
		
			$this->view->troca = 0;
		
		}

		if($this->getRequest()->isPost()){
		
			$dadosVeiculos['id_modelo'] = $_POST['id_modelo'];
			$dadosVeiculos['descricao_site'] = $_POST['descricao_site'];
			$dadosVeiculos['placa'] = $_POST['placa'];
			$dadosVeiculos['renavam'] = $_POST['renavam'];
			$dadosVeiculos['novo_usado'] = $_POST['novo_usado'];
			$dadosVeiculos['ano_fabricacao'] = $_POST['ano_fabricacao'];
			$dadosVeiculos['cor'] = $_POST['cor'];
			$dadosVeiculos['km'] = $_POST['km'];
			$dadosVeiculos['combustivel'] = $_POST['combustivel'];
			$dadosVeiculos['chassi'] = $_POST['chassi'];
			$dadosVeiculos['motor'] = $_POST['motor'];
			$dadosVeiculos['valor_aquisicao'] = $_POST['valor_aquisicao'];
			$dadosVeiculos['valor_aquisicao'] = str_replace(".","",$dadosVeiculos['valor_aquisicao']);
			$dadosVeiculos['valor_aquisicao'] = str_replace(",",".",$dadosVeiculos['valor_aquisicao']);		
			$dadosVeiculos['data_aquisicao'] = implode("-",array_reverse(explode("/",$_POST['data_aquisicao'])));
			$dadosVeiculos['valor_venda'] = $_POST['valor_venda'];
			$dadosVeiculos['valor_venda'] = str_replace(".","",$dadosVeiculos['valor_venda']);
			$dadosVeiculos['valor_venda'] = str_replace(",",".",$dadosVeiculos['valor_venda']);	
			$dadosVeiculos['id_empresa'] = $_POST['id_empresa'];
			//$dadosVeiculos['temp_troca'] = $_POST['temp_troca'];
			$dadosVeiculos['obs_interna'] = $_POST['obs_interna'];
			$dadosVeiculos['obs_site'] = $_POST['obs_site'];
			$dadosVeiculos['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dadosVeiculos['hora_alteracao'] = @date("Y-m-d H:i:s");
			$dadosVeiculos['exibir_valor_site'] = $_POST['exibir_valor_site'];
			$dadosVeiculos['exibir_km'] = $_POST['exibir_km_1']+$_POST['exibir_km_2'];
			$dadosVeiculos['exibir_site_estoque'] = $_POST['exibir_site_estoque_1']+$_POST['exibir_site_estoque_2'];
			
			if(!$_POST['ativo']){
			
				$dadosVeiculos['ativo'] = 0;
			
			}else{
			
				$dadosVeiculos['ativo'] = $_POST['ativo'];
				
			}
			
			if($_POST['origem'] == "0"){
			
				$dadosVeiculos['origem'] = $_POST['origem_descricao'];
			
			}else{
			
				$dadosVeiculos['origem'] = $_POST['origem'];
			
			}
			
			if($_POST['consignado'] == 0){
			
				$dadosVeiculos['consignado'] = 0;
			
			}elseif($_POST['consignado'] == 1){
			
				$dadosVeiculos['consignado'] = 1;
			
			}elseif($_POST['consignado'] == 2){
			
				$dadosVeiculos['consignado'] = 2;
			
			}elseif($_POST['consignado'] == 3){
			
				$dadosVeiculos['consignado'] = 3;
				
			}

			$mensagem = "";
			
			$cont = 1;
		
			if($dbVeiculos->add($dadosVeiculos)){

				$idVeiculos = $dbVeiculos->getLastId();
				
				$arrCheckList['id_veiculo'] =  $idVeiculos[0]['id'];
				$arrCheckList['quitado_leasing'] = $_POST['quitado_leasing'];
				$arrCheckList['pf_pj'] = $_POST['pf_pj'];
				$arrCheckList['gnv'] = $_POST['gnv'];
				
				if($_POST['gnv'] == 0){
				
					$arrCheckList['doc_gnv'] = 3;
				
				}else{
				
					$arrCheckList['doc_gnv'] = $_POST['doc_gnv'];
				
				}
				
				$dbCheckList = new Application_Model_DbTable_CheckList();
				
				if(!$dbCheckList->add($arrCheckList)){
				
					$mensagem .= "Erro ao cadastrar check-list.<br>";
				
				}
				
				$capa = explode("_", $_POST['capa']);
				
				$conts = 0;
				foreach($_FILES as $chave=>$valores){

					if($chave == "foto"){
				
						foreach($_FILES["foto"]["error"] as $key => $error){
							
							if(!file_exists(("fotos_veiculos/".$dadosVeiculos['id_empresa']))){
							
								$extensao = strtolower(end(explode(".",$_FILES['foto']['name'][$key])));
								mkdir("fotos_veiculos/".$dadosVeiculos['id_empresa']);
								chmod("fotos_veiculos/".$dadosVeiculos['id_empresa'], 0755);
								$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$dadosVeiculos['descricao_site']))."-".$dadosVeiculos['ano_fabricacao']."-".@date("his").$key.".".$extensao;
								
							}else{
						
								$extensao = strtolower(end(explode(".",$_FILES['foto']['name'][$key])));
								$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$dadosVeiculos['descricao_site']))."-".$dadosVeiculos['ano_fabricacao']."-".@date("his").$key.".".$extensao;
								
							}
								
							if($_FILES['foto']['tmp_name'][$key] != ""){
								
								
								/////////////////REDIMENCIONA IMAGEM///////////////////////
								# Caminho da imagem a ser redimensionada: 
								$input_image = $_FILES['foto']['tmp_name'][$key];
									 
								// Pega o tamanho original da imagem e armazena em um Array:
								$size = getimagesize( $input_image );
									 
								// Configura a nova largura da imagem:
								$thumb_width = "780";
									 
								// Calcula a altura da nova imagem para manter a proporção na tela: 
								$thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );
									 
								// Cria a imagem com as cores reais originais na memória.
								$thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );
									 
								// Criará uma nova imagem do arquivo.
								$src_img = ImageCreateFromJPEG( $input_image );
									 
								// Criará a imagem redimensionada:
								ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );
									 
								// Informe aqui o novo nome da imagem e a localização:
								$copied = ImageJPEG( $thumbnail, $novoNome);
								chmod($novoNome, 0755); 
								// Limpa da memoria a imagem criada temporáriamente: 
								ImageDestroy( $thumbnail );
								
								
								//$copied = copy($_FILES['foto']['tmp_name'][$key], $novoNome);
								
							}
								
							if($copied){
								
								$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
					
								$dadosFotos['id_veiculo'] = $idVeiculos[0]['id'];
								$dadosFotos['path'] = stripslashes($novoNome);
						
								if($conts == $capa[1]){
									
									$dadosFotos['capa'] = 1;
									$conts++;
									
								}else{
									
									$dadosFotos['capa'] = 0;
									$conts++;
									
								}
								
								if(!$dbFotosVeiculos->add($dadosFotos)){
									
									$mensagem .= "Erro ao realizar o upload de fotos.<br>";

								}
								
							}
								
							if($cont == 9){
					
								break;
								
							}
				
							$cont++;
						
						}
					
					}
				
				}
				
				//PEGA ANEXOS
				foreach($_FILES as $chave=>$valor){
				
					$anexos = explode("_",$chave);

					if($anexos[0] == "anexo"){
			
						if($_FILES[$chave]['name']){
							
							if(!file_exists(("anexos_veiculos/".$dadosVeiculos['id_empresa']))){
				
								mkdir("anexos_veiculos/".$dadosVeiculos['id_empresa']);
								chmod("anexos_veiculos/".$dadosVeiculos['id_empresa'], 0755); 
								$novoNome = "anexos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Y-m-d h:i:s")."_".$_FILES[$chave]['name'];
							
							}else{
							
								$novoNome = "anexos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Y-m-d h:i:s")."_".$_FILES[$chave]['name'];
							
							}
					
							$copied = copy($_FILES[$chave]['tmp_name'], $novoNome);
							
	
							if($copied){
								
								chmod($novoNome, 0755);
								
								$dbAnexosVeiculos = new Application_Model_DbTable_AnexosVeiculos();
								
								$dadosAnexos['id_veiculo'] = $idVeiculos[0]['id'];
								$dadosAnexos['path'] = $novoNome;
								$dadosAnexos['descricao'] = $_POST['descricaoanexo_'.$anexos[1]];
							
								if(!$dbAnexosVeiculos->add($dadosAnexos)){
								
									$mensagem .= "Erro ao realizar o upload de anexos.<br>";
									
								
								}
					
							}
						
						}
					
					}
				
				}
				
				foreach($_POST as $chave=>$valor){
				
					$despesas = explode("_",$chave);

					if($despesas[0] == "Ddata"){
					
						$dadosDespesas[$despesas[1]]['data'] = $valor;
					
					}
				
					if($despesas[0] == "Ddescricao"){
					
						$dadosDespesas[$despesas[1]]['despesa'] = $valor;
					
					}
					
					if($despesas[0] == "Dfornecedor"){
					
						$dadosDespesas[$despesas[1]]['id_fornecedor'] = $valor;
					
					}
					
					if($despesas[0] == "Dvalor"){
					
						$dadosDespesas[$despesas[1]]['valor'] = $valor;
					
					}
					
					if($despesas[0] == "Dgarantia"){
					
						$dadosDespesas[$despesas[1]]['dias_garantia'] = $valor;
					
					}
					
					if($despesas[0] == "Dnf"){
					
						$dadosDespesas[$despesas[1]]['nf'] = $valor;
					
					}
					
					
					//PEGA OPCIONAIS
					$opcional = explode("_",$chave);

					if($opcional[0] == "opcional"){
					
						$opcionais['id_veiculo'] = $idVeiculos[0]['id'];
						$opcionais['id_opcional'] = $opcional[1];
						
						if(!$dbOpcionaisVeiculos->add($opcionais)){
						
							$mensagem .= "Erro ao adicionar opcionais.<br>";
						
						}
					
					}
					
				}
				
				
				
				//PEGA DESPESAS
				foreach($dadosDespesas as $despesas){
				
					$data = explode("/",$despesas['data']);
					
					$despesas['data'] = $data[2]."-".$data[1]."-".$data[0];
					
					$despesas['id_veiculo'] = $idVeiculos[0]['id'];
				
					$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
					
					if($despesas['despesa'] != "" || $despesas['id_fornecedor'] != "" || $despesas['valor'] != ""){
					
						$despesas['valor'] = $despesas['valor'];
						$despesas['valor'] = str_replace(".","",$despesas['valor']);
						$despesas['valor'] = str_replace(",",".",$despesas['valor']);		
					
						if(!$dbDespesasVeiculos->add($despesas)){
						
							$mensagem .= "Erro ao adicionar despesas.<br>";
						
						}
						
					}
				
				}
				
				//PEGA PENDENCIAS
				foreach($_POST as $chave=>$valor){
				
					$pendencias = explode("_",$chave);

					if($pendencias[0] == "Pdata"){
					
						$dadosPendencias[$pendencias[1]]['data'] = $valor;
					
					}
					
					if($pendencias[0] == "Pdescricao"){
					
						$dadosPendencias[$pendencias[1]]['descricao'] = $valor;
					
					}
					
				}
				
				foreach($dadosPendencias as $pandencia){
				
					$data = explode("/",$pandencia['data']);
					
					$pandencia['data'] = $data[2]."-".$data[1]."-".$data[0];
					
					$pandencia['id_veiculo'] = $idVeiculos[0]['id'];
					$pandencia['baixada'] = 0;
				
					$dbPendenciasVeiculos = new Application_Model_DbTable_PendenciasVeiculos();
					
					if($pandencia['descricao'] != ""){
					
						if(!$dbPendenciasVeiculos->add($pandencia)){
						
							$mensagem .= "Erro ao adicionar pendências.<br>";
						
						}
						
					}
				
				}

			}
			
			$this->geraSitemap();
	
			if($mensagem == ""){
				
				$this->_helper->redirector->gotoUrl("veiculos/edt/id/".$idVeiculos[0]['id']."/msg/".$mensagem);
			
			}else{
			
				$this->view->mensagem = $mensagem;
			
			}
			
			
			

		}
	
	}
	
	public function lista3Action(){
	
		$this->validaAcesso('listar_estoque');
	
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbVersoesIcarros = new Application_Model_DbTable_VersoesIcarros();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		
		$arrEstoqueIcarros = $this->getEstoqueIcarros();
		
		//var_export($arrEstoqueIcarros);
		
		if($this->getRequest()->isPost()){
		
			$arr['parcial'] = true;
			$arr['modelo'] = $this->_getParam('modelo');
			$arr['placa'] = $this->_getParam('placa');
			$arr['ano_modelo'] = $this->_getParam('ano_modelo');
			
			if($this->_getParam('venda') == 1){
			
				$arr['vendido'] = $this->_getParam('venda');
			
			}
			
			if($this->_getParam('venda') == 2){
			
				$arr['vendido'] = "0";
			
			}

		}else{
		
			$arr['venda'] = true;
		
		}
		
		if($arr['placa']){
		
			$arrTemp = explode("-",$arr['placa']);
			
			if($arrTemp[1] == ""){
			
				$arr['placa'] = substr($arr['placa'],0,3)."-".substr($arr['placa'],3);
			
			}
		
		}

		$arrVeiculos = $dbVeiculos->getVeiculosCompleto($arr);
		$arrPlanos = $this->getPlanoIcarros();
		
		//var_export($arrPlanos);
		
		foreach($arrVeiculos as $veiculo){
			
			$stringOpcional = "";
			
			$arrOpcionais = $dbOpcionaisVeiculos->getVeiculosOpcionais($veiculo['id']);
			$arrFotos = $dbFotosVeiculos->getNFotos($veiculo['id']);
			
			foreach($arrOpcionais as $opcional){
			
				$opcionalTemp = explode(")",$opcional['opcional']);
			
				$stringOpcional .= substr($opcionalTemp[0],1)." / ";
			
			}
			
			list($anoInicio, $mesInicio, $diaInicio) = explode('-', $veiculo['data_aquisicao']);
			list($anoFim, $mesFim, $diaFim) = explode('-', @date('Y-m-d'));
			
			$dataInicio = mktime(0,0,0, $mesInicio, $diaInicio, $anoInicio);//data compra
			$dataFim    = mktime(0,0,0, $mesFim, $diaFim, $anoFim);//data de hoje
			
			$diferenca =  $dataFim -$dataInicio;
			
			if($arrEstoqueIcarros){
			
				foreach($arrEstoqueIcarros as $estoqueIcarros){
				
					$temFipe = false;
				
					$estoqueIcarros['placa'] = substr($estoqueIcarros['placa'],0,3)."-".substr($estoqueIcarros['placa'],-4);
				
					if(strtoupper($estoqueIcarros['placa']) == strtoupper($veiculo['placa'])){

						$arrVeiculoCompleto[$veiculo['id']]['icarros'] = 1;
						$arrVeiculoCompleto[$veiculo['id']]['tipo_anuncio'] = $arrPlanos[$estoqueIcarros['tipoAnuncio_id']];
						break;
					
					}else{
					
						$arrVeiculoCompleto[$veiculo['id']]['icarros'] = 0;
						$arrVeiculoCompleto[$veiculo['id']]['tipo_anuncio'] = "<option value=''>Selecione</option>";
					
					}
				
				}
			
			}
			
			$arrVeiculoCompleto[$veiculo['id']]['id'] = $veiculo['id'];
			$arrVeiculoCompleto[$veiculo['id']]['opcionais'] = $stringOpcional;
			$arrVeiculoCompleto[$veiculo['id']]['marca'] = $veiculo['marca'];
			$arrVeiculoCompleto[$veiculo['id']]['modelo'] = $veiculo['modelo'];
			$arrVeiculoCompleto[$veiculo['id']]['placa'] = $veiculo['placa'];
			$arrVeiculoCompleto[$veiculo['id']]['cor'] = $veiculo['cor'];
			$arrVeiculoCompleto[$veiculo['id']]['ano_fabricacao'] = $veiculo['ano_fabricacao'];
			$arrVeiculoCompleto[$veiculo['id']]['ano_modelo'] = $veiculo['ano_modelo'];
			$arrVeiculoCompleto[$veiculo['id']]['km'] = $veiculo['km'];
			$arrVeiculoCompleto[$veiculo['id']]['valor_venda'] = $veiculo['valor_venda'];
			$arrVeiculoCompleto[$veiculo['id']]['valor_aquisicao'] = $veiculo['valor_aquisicao'];
			$arrVeiculoCompleto[$veiculo['id']]['ativo'] = $veiculo['ativo'];
			$arrVeiculoCompleto[$veiculo['id']]['nome'] = $veiculo['nome'];
			$arrVeiculoCompleto[$veiculo['id']]['hora_alteracao'] = $veiculo['hora_alteracao'];
			$arrVeiculoCompleto[$veiculo['id']]['fotos'] = $arrFotos[0]['total'];
			$arrVeiculoCompleto[$veiculo['id']]['de'] = round(($veiculo['vendido'] == 0) ? $diferenca/86400 : 0,0);
			$arrVeiculoCompleto[$veiculo['id']]['vendido'] = $veiculo['vendido'];
			$arrVeiculoCompleto[$veiculo['id']]['consignado'] = $veiculo['consignado'];
			$arrVeiculoCompleto[$veiculo['id']]['descricao_site'] = $veiculo['descricao_site'];
			$arrVeiculoCompleto[$veiculo['id']]['cod_fipe'] = $veiculo['cod_fipe'];
			
		}
		
		$this->view->veiculos = $arrVeiculoCompleto;
	
	}
	
	
	public function lista2Action(){
	
		$this->validaAcesso('listar_estoque');
	
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbVersoesIcarros = new Application_Model_DbTable_VersoesIcarros();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

		if($this->getRequest()->isPost()){
		
			$arr['parcial'] = true;
			if($this->_getParam('modelo')){
				$arr['modelo'] = $this->_getParam('modelo');
			}
			if($this->_getParam('placa')){
				$arr['placa'] = $this->_getParam('placa');
			}
			if($this->_getParam('ano_modelo')){
				$arr['ano_modelo'] = $this->_getParam('ano_modelo');
			}
			
			if($this->_getParam('venda') == 1){
			
				$arr['vendido'] = $this->_getParam('venda');
			
			}
			
			if($this->_getParam('venda') == 2){
			
				$arr['vendido'] = "0";
			
			}
			
			//var_export($_POST);
			
		}else{
		
			$arr['venda'] = true;
		
		}
		
		if(isset($arr['placa'])){
		
			$arrTemp = explode("-",$arr['placa']);
			
			if(!isset($arrTemp[1])){
			
				$arr['placa'] = substr($arr['placa'],0,3)."-".substr($arr['placa'],3);
			
			}
		
		}

		$arrVeiculos = $dbVeiculos->getVeiculosCompleto($arr);
		
		foreach($arrVeiculos as $veiculo){
			
			$stringOpcional = "";
			
			$arrOpcionais = $dbOpcionaisVeiculos->getVeiculosOpcionais($veiculo['id']);
			$arrFotos = $dbFotosVeiculos->getNFotos($veiculo['id']);
			if(!isset($arrFotos[0]['total'])){
				$arrFotos[0]['total'] = 0;
			}
			
			foreach($arrOpcionais as $opcional){
			
				$opcionalTemp = explode(")",$opcional['opcional']);
			
				$stringOpcional .= substr($opcionalTemp[0],1)." / ";
			
			}
			
			list($anoInicio, $mesInicio, $diaInicio) = explode('-', $veiculo['data_aquisicao']);
			list($anoFim, $mesFim, $diaFim) = explode('-', @date('Y-m-d'));
			
			$dataInicio = mktime(0,0,0, $mesInicio, $diaInicio, $anoInicio);//data compra
			$dataFim    = mktime(0,0,0, $mesFim, $diaFim, $anoFim);//data de hoje
			
			$diferenca =  $dataFim -$dataInicio;
			
			$arrVeiculoCompleto[$veiculo['id']]['id'] = $veiculo['id'];
			$arrVeiculoCompleto[$veiculo['id']]['opcionais'] = $stringOpcional;
			$arrVeiculoCompleto[$veiculo['id']]['marca'] = $veiculo['marca'];
			$arrVeiculoCompleto[$veiculo['id']]['modelo'] = $veiculo['modelo'];
			$arrVeiculoCompleto[$veiculo['id']]['segmento'] = ucfirst($veiculo['segmento']);
			$arrVeiculoCompleto[$veiculo['id']]['placa'] = $veiculo['placa'];
			$arrVeiculoCompleto[$veiculo['id']]['cor'] = $veiculo['cor'];
			$arrVeiculoCompleto[$veiculo['id']]['ano_fabricacao'] = $veiculo['ano_fabricacao'];
			$arrVeiculoCompleto[$veiculo['id']]['ano_modelo'] = $veiculo['ano_modelo'];
			$arrVeiculoCompleto[$veiculo['id']]['km'] = $veiculo['km'];
			$arrVeiculoCompleto[$veiculo['id']]['valor_venda'] = $veiculo['valor_venda'];
			$arrVeiculoCompleto[$veiculo['id']]['valor_aquisicao'] = $veiculo['valor_aquisicao'];
			$arrVeiculoCompleto[$veiculo['id']]['ativo'] = $veiculo['ativo'];
			$arrVeiculoCompleto[$veiculo['id']]['nome'] = $veiculo['nome'];
			$arrVeiculoCompleto[$veiculo['id']]['hora_alteracao'] = $veiculo['hora_alteracao'];
			$arrVeiculoCompleto[$veiculo['id']]['fotos'] = $arrFotos[0]['total'];
			$arrVeiculoCompleto[$veiculo['id']]['de'] = round(($veiculo['vendido'] == 0) ? $diferenca/86400 : 0,0);
			$arrVeiculoCompleto[$veiculo['id']]['vendido'] = $veiculo['vendido'];
			$arrVeiculoCompleto[$veiculo['id']]['consignado'] = $veiculo['consignado'];
			$arrVeiculoCompleto[$veiculo['id']]['descricao_site'] = $veiculo['descricao_site'];
			$arrVeiculoCompleto[$veiculo['id']]['cod_fipe'] = $veiculo['cod_fipe'];
			$arrVeiculoCompleto[$veiculo['id']]['app'] = $veiculo['app'];
			
		}
		
		$this->view->veiculos = $arrVeiculoCompleto;
	
	}
	

	public function listaEstoqueGerencialAction(){
	
		$this->validaAcesso('relatorios');
	
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
		
		if($_POST['origem'] == "Outros"){
		
			$arr['sql'] = "v.origem != 'Compra' AND v.origem != 'Concessionária' AND v.origem != 'Indicação' AND v.origem != 'Leilão' AND v.origem != 'Troca'";
		
		}else{
		
			$arr['origem'] = $_POST['origem'];
			
		}
		
		$arrVeiculo = $dbVeiculo->_get($arr);
		
		$strTabela = "<table id='table_dados' style='width:1600px;'>";
		
		$count = 0;
		
		foreach($arrVeiculo as $key=>$veiculo){
		
			$count++;
		
			$opcionais = "";
		
			$arrOpcionais = $dbOpcionaisVeiculos->getVeiculosOpcionais($veiculo['id']);
			
			foreach($arrOpcionais as $opcional){
			
				$tempOpcional = explode(")",$opcional['opcional']);
	
				$opcionais .= str_replace("(","",$tempOpcional[0])." ";
			
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
			
	
			$revisao = $dbDespesasVeiculos->getSomaDespesas($veiculo['id']);
			$lucroReal = $veiculo['valor_venda']-($revisao[0]['valor_despesas']+$veiculo['valor_aquisicao']);
			$lucroPorcento = $lucroReal/($revisao[0]['valor_despesas']+$veiculo['valor_aquisicao'])*100;
			
			if($veiculo['data_termino_revisao'] == null || $veiculo['data_termino_revisao'] == "0000-00-00"){
			
				$dataTerminoRevisao = "Em Revis&atilde;o";
                if ($veiculo['data_termino_revisao'] == "0000-00-00"){ 
                    $veiculo['data_termino_revisao'] = NULL; 
                }  
			}else{
			
				$dataTerminoRevisao = implode("/",array_reverse(explode("-",$veiculo['data_termino_revisao'])));
			
			}
			
			$dataTmp2 = explode("-",$veiculo['data_termino_revisao']);
			$dataIni = explode("-",$veiculo['data_aquisicao']);
			$timestamp1 = mktime(0,0,0,$dataTmp2[1],$dataTmp2[2],$dataTmp2[0]); 
			$timestamp2 = mktime(0,0,0,$dataIni[1],$dataIni[2],$dataIni[0]);
			$segundos_diferencas = $timestamp1 - $timestamp2;
			
			if($segundos_diferencas < 0){
				
				$dias_diferenca_revisao = "Em Revis&atilde;o";
			
			}else{
			
				$dias_diferenca_revisao = round($segundos_diferencas /(60 * 60 * 24),0);
			
			}
			
			$qtdFotos = $dbFotosVeiculos->getNFotos($veiculo['id']);
			
			if(!$qtdFotos[0]['total']){
			
				$qtdFotos[0]['total'] = 0;
			
			}
			
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
			
			if($arrVeiculo[$key-1]['marca'] != $veiculo['marca']){
			
				$strTabela .="<tr><td colspan='21' style='background-color:#DDDDDD; text-align:left;'><b>".$veiculo['marca']."</b></td></tr>";
			
			}
			
			if($veiculo['descricao_site']){
			
				$veiculo['modelo'] = $veiculo['descricao_site'];
			
			}
		
			$strTabela .="<tr>
							<td class='tds' style='width:27px;'>".$count."</td>
							<td class='tds' style='width:101px;'>".$veiculo['modelo']."</td>
							<td class='tds' style='width:80px;'>".$opcionais."</td>
							<td class='tds' style='width:78px;'>".$veiculo['combustivel']."</td>
							<td class='tds' style='width:80px;'>".$veiculo['cor']."</td>
							<td class='tds' style='width:60px;'>".$veiculo['ano_fabricacao']."/".$veiculo['ano_modelo']."</td>
							<td class='tds' style='width:65px;'>".$veiculo['placa']."</td>
							<td class='tds' style='width:90px;'>".$veiculo['origem']."</td>
							<td class='tds' style='width:50px;'>".$veiculo['km']."</td>
							<td class='tds' style='width:80px;'>R$ ".money_format("%i",$veiculo['valor_venda'])."</td>
							<td ".$statusEstoque." style='width:60px;'>".round($dias_diferenca,0)." dias</td>
							<td class='tds' style='width:80px;'>R$ ".money_format("%i",$revisao[0]['valor_despesas']+$veiculo['valor_aquisicao'])."</td>
							<td class='tds' style='width:80px;'>R$ ".money_format("%i",$veiculo['valor_aquisicao'])."</td>
							<td class='tds' style='width:80px;'>R$ ".money_format("%i",$revisao[0]['valor_despesas'])."</td>
							<td ".$statusLucro." style='width:80px;'>R$ ".money_format("%i",$lucroReal)."</td>
							<td ".$statusLucro." style='width:78px;'>".round($lucroPorcento,2)."%</td>
							<td class='tds' style='width:81px;'>".implode("/",array_reverse(explode("-",$veiculo['data_aquisicao'])))."</td>
							<td class='tds' style='width:80px;'>".$dataTerminoRevisao."</td>
							<td ".$statusRevisao." style='width:80px;'>".$dias_diferenca_revisao."</td>
							<td class='tds'style='background-color:#4876FF; color:#FFFFFF; width:80px;'>R$ ".money_format("%i",$veiculo['fipe'])."</td>
							<td class='tds' style='width:27px;'>".$qtdFotos[0]['total']."</td>
						 </tr>";
						 
			$somaVenda += $veiculo['valor_venda'];
			$somaDiasEstoque += $dias_diferenca;
			$somaCusto += $revisao[0]['valor_despesas']+$veiculo['valor_aquisicao'];
			$somaCompra += $veiculo['valor_aquisicao'];
			$somaRevisao += $revisao[0]['valor_despesas'];
			$somaLucro += $lucroReal;
			$somaLucroPorcento += $lucroPorcento;
			$somaFIPE += $veiculo['fipe'];
			
		
		}
		
		$totalMedia = "<tr>
						<td colspan='9' style='text-align:right; border:solid 2px;'><b>M&Eacute;DIA</b></td>
						<td style='border:solid 2px;'>R$ ".money_format("%i",$somaVenda/$count)."</td>
						<td style='border:solid 2px;'>".round($somaDiasEstoque/$count,0)." dias</td>
						<td style='border:solid 2px;'>R$ ".money_format("%i",$somaCusto/$count)."</td>
						<td style='border:solid 2px;'>R$ ".money_format("%i",$somaCompra/$count)."</td>
						<td style='border:solid 2px;'>R$ ".money_format("%i",$somaRevisao/$count)."</td>
						<td style='border:solid 2px;'>R$ ".money_format("%i",$somaLucro/$count)."</td>
						<td style='border:solid 2px;'>".round($somaLucroPorcento/$count,2)."%</td>
						<td></td>
						<td></td>
						<td></td>
						<td style='border:solid 2px;'>R$ ".money_format("%i",$somaFIPE/$count)."</td>
						<td></td>
					   </tr>";
					   
					   
		$totalSoma = "<tr>
						<td colspan='9' style='text-align:right; border:solid 2px;'><b>TOTAL</b></td>
						<td style='border:solid 2px;'>R$ ".money_format("%i",$somaVenda)."</td>
						<td></td>
						<td style='border:solid 2px;'>R$ ".money_format("%i",$somaCusto)."</td>
						<td style='border:solid 2px;'>R$ ".money_format("%i",$somaCompra)."</td>
						<td style='border:solid 2px;'>R$ ".money_format("%i",$somaRevisao)."</td>
						<td style='border:solid 2px;'>R$ ".money_format("%i",$somaLucro)."</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					   </tr>";
		
		
		$strTabela .= $totalMedia.$totalSoma."</table>";
		
		$this->view->arrEstoque = $strTabela;
	
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
		
		
		$strTabela = "<table class='table'>";
		
		$strTabela .= "<tr>
						<th class='cabeca'>Nº</th>
						<th class='cabeca'>Modelo</th>
						<th class='cabeca'>Opcionais</th>
						<th class='cabeca'>Combustivel</th>
						<th class='cabeca'>Cor</th>
						<th class='cabeca'>Ano</th>
						<th class='cabeca'>Placa</th>
						<th class='cabeca'>KM</th>
						<th class='cabeca'>Venda</th>
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
			
			if($arrVeiculo[$key-1]['marca'] != $veiculo['marca']){
			
				$strTabela .="<tr><td colspan='9' style='background-color:#DDDDDD; text-align:left;'><b>".$veiculo['marca']."</b></td></tr>";
			
			}
			
			if($veiculo['descricao_site']){
			
				$veiculo['modelo'] = $veiculo['descricao_site'];
			
			}
		
			$strTabela .="<tr>
							<td class='tds'>".$count."</td>
							<td class='tds'>".$veiculo['modelo']."</td>
							<td class='tds'>".$opcionais."</td>
							<td class='tds'>".$veiculo['combustivel']."</td>
							<td class='tds'>".$veiculo['cor']."</td>
							<td class='tds'>".$veiculo['ano_fabricacao']."/".$veiculo['ano_modelo']."</td>
							<td class='tds'>".$veiculo['placa']."</td>
							<td class='tds'>".$veiculo['km']."</td>
							<td class='tds'>R$ ".money_format("%i",$veiculo['valor_venda'])."</td>
						 </tr>";
		
		}
		
		$this->view->arrEstoque = $strTabela."</table>";
	
	}
	
	public function imprimeInformativoAction(){
	
		$dbVeiculo = new Application_Model_DbTable_Veiculos();
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		
		$arr['id'] = $this->_getParam('id');
		
		
		$this->view->veiculo = $dbVeiculo->_get($arr);
		$this->view->empresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
		$this->view->opcionais = $dbOpcionaisVeiculos->getVeiculosOpcionais($this->_getParam('id'));

		//var_export($dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']));
		
	}
	
	public function ajaxAction(){

		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');

		if($this->_getParam('fn') == 'getByPlacaModelo'){
			
			$dbVeiculo = new Application_Model_DbTable_Veiculos();
			
			$arr['id_empresa']=$_SESSION['sessionUser']['id_empresa'];
			$arr['placa']=$this->_getParam('f');
			$arr['modelo']=$this->_getParam('f');
			$arr['parcial']=true;
			//($this->_getParam('vendido')) ? $arr['vendido'] = 1 : $arr['vendido'] = 0;
			
			if($this->_getParam('temp_troca')){
			
				$arr['vend'] = 1;
		
				$arrV = $dbVeiculo->_get($arr);

				foreach($arrV as $v){

					$exibe = true;
				
					if($v['descricao_site']){
					
						$v['modelo'] = $v['descricao_site'];
					
					}

					if($this->_getParam('fn_troca') == "true"){
						if($v['id_negociacao_troca'] != "" || $v['id_negociacao_troca2'] != ""){
							$exibe = false;
						}
					}

					if($exibe){
					
						if($this->_getParam('n') == 2){
					
							echo "<li> <a href=\"#\" onclick=\"populaCamposTroca2(".$v['id'].");esconde($(this).parent().parent().parent())\">".$v['modelo']." - ".$v['placa']."</a></li>";
						
						}else{
					
							echo "<li> <a href=\"#\" onclick=\"populaCamposTroca(".$v['id'].");esconde($(this).parent().parent().parent())\">".$v['modelo']." - ".$v['placa']."</a></li>";
						
						}

					}
					
				}
				
			}else{
				
				$arr['temp_troca'] = 0;
				
				if($this->_getParam('vendido') == "1"){
			
					$arr['vendidos'] = 1;
		
				}else{
				
					$arr['vend'] = 1;
				
				}
				
				$arrV = $dbVeiculo->_get($arr);

				foreach($arrV as $v){

					$exibe = true;
				
					if($v['descricao_site']){
					
						$v['modelo'] = $v['descricao_site'];
					
					}

					if($this->_getParam('fn_compra') == "true"){
						if($v['id_negociacao_compra'] != ""){
							$exibe = false;
						}
					}

					if($exibe){

						if($this->_getParam('n') == 2){
							echo "<li> <a href=\"#\" onclick=\"populaCamposVeiculo2(".$v['id'].");esconde($(this).parent().parent().parent())\">".$v['modelo']." - ".$v['placa']."</a></li>";
						}else{
							echo "<li> <a href=\"#\" onclick=\"populaCamposVeiculo(".$v['id'].");esconde($(this).parent().parent().parent())\">".$v['modelo']." - ".$v['placa']."</a></li>";
					
						}

					}
				
				}
				
			}

		}elseif($this->_getParam('fn') == 'getByPlacaModeloRevisao'){
		
			$dbVeiculo = new Application_Model_DbTable_Veiculos();
			
			$arr['id_empresa']=$_SESSION['sessionUser']['id_empresa'];

			$placa = $this->_getParam('f');
			
			if(strlen($placa) >= 3) {


				if(isset($placa{3}) && $placa{3} != "-") {
					if(isset($placa{6})){
						$arr['placa'] = $placa{0}.$placa{1}.$placa{2}."-".$placa{3}.$placa{4}.$placa{5}.$placa{6};
					}elseif(isset($placa{5})){
						$arr['placa'] = $placa{0}.$placa{1}.$placa{2}."-".$placa{3}.$placa{4}.$placa{5};
					}elseif(isset($placa{4})){
						$arr['placa'] = $placa{0}.$placa{1}.$placa{2}."-".$placa{3}.$placa{4};
					}elseif(isset($placa{3})){
						$arr['placa'] = $placa{0}.$placa{1}.$placa{2}."-".$placa{3};
					}
				}else{
					
					$arr['placa'] = $placa;
				
				}
			

				$arr['modelo'] = $this->_getParam('f');
				$arr['parcial'] = true;

				$arrV = $dbVeiculo->_get($arr);

				foreach($arrV as $v){
					
					if($v['descricao_site']){
						
						$v['modelo'] = $v['descricao_site'];
						
					}
					
					echo "<li> <a href=\"#\" onclick=\"populaCamposVeiculo(".$v['id'].",".$this->_getParam('d').");esconde($(this).parent().parent().parent())\">".$v['modelo']." - ".$v['placa']."</a></li>";
					
				}
				
			}

		}elseif($this->_getParam('fn') == 'getById'){
			
			$dbVeiculo = new Application_Model_DbTable_Veiculos();
			
			//$arr['id_empresa']=$_SESSION['sessionUser']['id_empresa'];
			$arr['id']=$this->_getParam('f');
			
			if($this->_getParam('n')){
			
				$arr['id_negociacao_troca'] = $this->_getParam('n');
				//$arr['temp_troca'] = 1;
			
			}
			
			if($this->_getParam('n2')){
			
				$arr['id_negociacao_troca2'] = $this->_getParam('n2');
				//$arr['temp_troca'] = 1;
			
			}
			
			$arrV = $dbVeiculo->_get($arr);
			
			//echo $arr['id_empresa'];

			foreach($arrV as $v){
				
				if($v['descricao_site']){
					
					$v['modelo'] = $v['descricao_site'];
					
				}
			
				foreach($v as $k => $val){
			
					/*if($k == 'valor_venda'){
					
						$val = number_format($val,2,',','.');
					
					}*/
			
					echo $k.":".$val."|";
				
				}
			
			}

		}elseif($this->_getParam('fn') == 'getTableDespesasByIdVeiculo'){
			
			$dbDV = new Application_Model_DbTable_DespesasVeiculos();
			$total = 0;
			
			$arr['id_veiculo'] = $this->_getParam('f');
			
			$arrDV = $dbDV->_get($arr);

			foreach($arrDV as $d){
			
				$d['data'] = explode("-",$d['data']);
				$d['data'] = array_reverse($d['data']);
				$d['data'] = implode("/",$d['data']);
				
				$total += $d['valor'];
				
				echo "<tr><td>".$d['data']."</td><td>".$d['despesa']."</td><td>".$d['razao_social_fornecedor']."</td><td>".$d['nf']."</td><td>".$d['dias_garantia']."</td><td>R$ ".number_format($d['valor'],2,',','.')."</td></tr>";
			
			}
			
			echo "<tr style='background-color:#ccc;'><td colspan='5' style='text-align: right; font-weight:bold;'>TOTAL GASTO</td><td id='total_despesas' style='font-weight:bold;'>R$ ".number_format($total,2,',','.')."</td></tr>";

			if($_SESSION['sessionUser']['id_empresa'] == 239 || $_SESSION['sessionUser']['id_empresa'] == 3){
				echo "<tr style='background-color:#bbb;'><td colspan='5' style='text-align: right; font-weight:bold;'><a id='link_avaliacao' target='_blank' href=''>TOTAL AVALIADO</a></td><td id='despesa_avaliada' style='font-weight:bold;'></td></tr>";
			}
		
		}elseif($this->_getParam('fn') == 'deleta_foto'){
		
			$dbFotosVeiculo = new Application_Model_DbTable_FotosVeiculos();
		
			$pathFoto = $dbFotosVeiculo->getPathFoto($this->_getParam('id_foto'));
		
			$qtdFotos = $dbFotosVeiculo->getQuantidadeFotos($this->_getParam('id_foto'));
			
			if($qtdFotos[0]['qtd'] > 2 ){
			
				$dbFotosVeiculo->del($this->_getParam('id_foto'));
				
				unlink($pathFoto[0]['path']);
			
				echo "Sucesso";
			
			}else{
			
				echo "Não é possivel deletar, cada veículo cadastrado deve ter no mínimo duas fotos";
			
			}
		
		}elseif($this->_getParam('fn') == 'deleta_anexo'){
			
			$dbAnexosVeiculos = new Application_Model_DbTable_AnexosVeiculos();
			
			$pathAnexo = $dbAnexosVeiculos->getPathAnexo($this->_getParam('id_anexo'));
			
			unlink($pathAnexo[0]['path']);
			
			if(!$dbAnexosVeiculos->del($this->_getParam('id_anexo'))){
		
				echo "Sucesso";
			
			}else{
			
				echo "Erro ao deletar anexo!";
			
			}
		
		}elseif($this->_getParam('fn') == 'deleta_despesa'){
			
			$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
		
			if($dbDespesasVeiculos->del($this->_getParam('id_despesa')) || $this->_getParam('id_despesa') <=0){
		
				echo "Sucesso";
			
			}else{
			
				echo "Erro ao deletar anexo!";
			
			}
		
		}elseif($this->_getParam('fn') == 'deleta_pendencia'){
			
			$dbPendenciasVeiculos = new Application_Model_DbTable_PendenciasVeiculos();
		
			if($dbPendenciasVeiculos->del($this->_getParam('id_pendencia')) || $this->_getParam('id_pendencia') <=0){
		
				echo "Sucesso";
			
			}else{
			
				echo "Erro ao deletar anexo!";
			
			}
		
		}elseif($this->_getParam('fn') == 'edita_valores'){
		
			$bolExcluir = true;
		
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
			$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		
			$arrJson = json_decode($this->_getParam('json_valores'),true);

			foreach($arrJson as $key=>$valor){
		
				$idVeiculo = explode("-",$key);

				$arrDados[$idVeiculo[0]] = $valor;

				if($idVeiculo[1] != ""){
				
					$dbVeiculos->edt($idVeiculo[1],$arrDados);
			
				}
			
			}
		
			$dbEmpresas = new Application_Model_DbTable_Empresas();
			$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
			
			/*
			if($arrJson){
			
				if($arrEmpresa[0]['senha_icarros'] != "" && $arrEmpresa[0]['login_icarros'] != ""){
		
					$arrIcarros = $this->getEstoqueIcarros();
					
					foreach($arrJson as $key=>$valor){
					
						$idVeiculo = explode("-",$key);
					
						$bolExcluir = true;
					
						$arrVeiculos = $dbVeiculos->getVeiculoEstoque($idVeiculo[1]);
						
						foreach($arrIcarros as $keyI=>$icarros){
						
							if(strtolower(implode("",explode("-",$arrVeiculos[0]['placa']))) == strtolower($icarros['placa'])){
							
								$arrVeiculosEdt[$keyI] = $arrVeiculos[0];
								
								unset($arrIcarros[$keyI]);
								
							
							}
						
						}
						
					}
					
					$sucesso = "";
			
					if($arrVeiculosEdt){
					
						if($this->edtIcarros($arrVeiculosEdt) == "OK"){
						
							$sucesso = "sucesso";
						
						}else{
					
							$sucesso = "Erro";
					
						}
					
					}else{
					
						$sucesso = "sucesso";
					
					}

				}else{
				
					$sucesso = "sucesso";
				
				}

				if($arrEmpresa[0]['login_webmotors'] && $arrEmpresa[0]['senha_webmotors']){
					
					$suces = true;
					
					$arrEstoqueWeb = $this->getEstoqueWebmotors($arrEmpresa[0]['cnpj'], $arrEmpresa[0]['login_webmotors'], $arrEmpresa[0]['senha_webmotors']);

					foreach($arrJson as $key=>$valor){
					
						$idVeiculo = explode("-",$key);

						$arrVeiculos = $dbVeiculos->getVeiculoEstoque($idVeiculo[1]);

						foreach($arrEstoqueWeb as $estoqueWeb){
							
							if(strtolower($estoqueWeb['placa']) == strtolower(str_replace("-","",$arrVeiculos[0]['placa']))){
								
								$arrVeiculos[0]['codigo_modalidade'] = $estoqueWeb['codigo_modalidade'];
								$arrVeiculos[0]['codigo_marca'] = $estoqueWeb['codigo_marca'];
								$arrVeiculos[0]['codigo_modelo'] = $estoqueWeb['codigo_modelo'];
								$arrVeiculos[0]['codigo_versao'] = $estoqueWeb['codigo_versao'];
								
								$arrVeiculoCompleto = $this->alteraValoresWebmotors($arrEmpresa[0]['cnpj'], $arrEmpresa[0]['login_webmotors'], $arrEmpresa[0]['senha_webmotors'], $estoqueWeb['codigo_anuncio'], $arrVeiculos);
								
								if($arrVeiculoCompleto != 500){
									
									$suces = false;
								
								}
								
								break;
					
							}
				
						}
					
					}
					
					if(!$suces){
					
						$sucesso = "Erro";
						
					}
					
				}
			
			}else{
				
				$sucesso = "sucesso";
			
			}
			*/
			echo $sucesso;
		
		}elseif($this->_getParam('fn') == 'edita_valores_icarros'){
		
			/*$bolExcluir = true;
		
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
			$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		
			$arrJson = json_decode($this->_getParam('json_valores'),true);
		
			foreach($arrJson as $key=>$valor){
		
				$idVeiculo = explode("-",$key);

				$arrDados[$idVeiculo[0]] = $valor;

				if($idVeiculo[1] != ""){
				
					$dbVeiculos->edt($idVeiculo[1],$arrDados);
			
				}
			
			}
			
			$arrCheckbox = json_decode($this->_getParam('json_checkbox'),true);
			
			foreach($arrCheckbox as $keyC=>$valorC){
			
				$idVeiculo = explode("_",$keyC);

				$arrDadosC[$idVeiculo[1]] = $valorC;
			
			}
			
			$arrSelect = json_decode($this->_getParam('json_select'),true);
			
			if($arrSelect != ""){
			
				foreach($arrSelect as $keyS=>$valorS){
				
					$idVeiculo = explode("_",$keyS);

					$arrDadosS[$idVeiculo[1]] = $valorS;
				
				}
			
			}

			
			$dbEmpresas = new Application_Model_DbTable_Empresas();
			$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
			
			if($arrEmpresa[0]['senha_icarros'] != "" && $arrEmpresa[0]['login_icarros'] != ""){

	
				$arrIcarros = $this->getEstoqueIcarros();
				
				foreach($arrDadosC as $key=>$versaoId){
				
					$bolExcluir = true;
				
					$arrVeiculos = $dbVeiculos->getVeiculoEstoque($key);

						foreach($arrIcarros as $keyI=>$icarros){
						
							if(strtolower(implode("",explode("-",$arrVeiculos[0]['placa']))) == strtolower($icarros['placa'])){
							
								$arrVeiculosEdt[$keyI] = $arrVeiculos[0];
								$arrVeiculosEdt[$keyI]['tipoAnuncio_id'] = $arrDadosS[$key];
								unset($arrIcarros[$keyI]);
								$bolExcluir = false;
							
							}
						
						}

					if($bolExcluir){
						
						$arrVeiculosAdd[$arrVeiculos[0]['id']] = $arrVeiculos[0];
						$arrVeiculosAdd[$arrVeiculos[0]['id']]['tipoAnuncio_id'] = $arrDadosS[$key];
						
						if($versaoId != 0){
						
							$arrVeiculosAdd[$arrVeiculos[0]['id']]['versao_id'] = $versaoId;
						
						}
					
					}
				
				}
				
				$sucesso = "";

				if($arrIcarros){
				
					if($this->delIcarros($arrIcarros) == "OK"){
					
						$sucesso .= "su";
					
					}
					
				}else{
				
					$sucesso .= "su";
				
				}
				
		
				if($arrVeiculosEdt){
				
					if($this->edtIcarros($arrVeiculosEdt) == "OK"){
					
						$sucesso .= "ce";
					
					}
					
				}else{
				
					$sucesso .= "ce";
				
				}
				
			
				if($arrVeiculosAdd){

					if($this->addIcarros($arrVeiculosAdd) == "OK"){
					
						$sucesso .= "sso";
			
					}

				}else{
				
					$sucesso .= "sso";
				
				}

				echo $sucesso;

			}else{
			
			
			}
			*/

			echo "sucesso";
		
		}elseif($this->_getParam('fn') == 'verifica_placa'){
		
			$dbVeiculo = new Application_Model_DbTable_Veiculos();
			
			$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$arr['placa'] = $this->_getParam('placa');
			
			$arrV = $dbVeiculo->_get($arr);
			
			if($arrV){
			
				echo "Esta placa pertence a um veículo já cadastrado em seu estoque!";
			
			}else{
				
				echo "no";
			
			}
			
		
		}elseif($this->_getParam('fn') == 'busca_estoque'){
			
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
			$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
			$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
			
			$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$arrFiltro['venda'] = true;
			$arrFiltro['order_valor_venda'] = false;
			
			$arrVeiculos = $dbVeiculos->getVeiculosCompleto($arrFiltro);
			$cont = 0;
			
			echo "<table class='table'>";
			
			foreach($arrVeiculos as $key=>$veiculos){
				
				$cont++;
				$stringOpcional = "";
			
				$arrOpcionais = $dbOpcionaisVeiculos->getVeiculosOpcionais($veiculos['id']);
				$arrFotos = $dbFotosVeiculos->getNFotos($veiculos['id']);
				
				foreach($arrOpcionais as $opcional){
				
					$opcionalTemp = explode(")",$opcional['opcional']);
				
					$stringOpcional .= substr($opcionalTemp[0],1)." / ";
				
				}
				
				if($veiculos['descricao_site']){
				
					$veiculos['modelo'] = $veiculos['descricao_site'];
				
				}
			
				if($arrVeiculos[$key-1]['marca'] != $arrVeiculos[$key]['marca']){
				
					echo "<tr>
							<td colspan='1' style='height: 25px; font-weight:bold; background-color: #CCCCCC;'>".mb_convert_encoding($veiculos['marca'], 'UTF-8', 'ISO-8859-1')."</td>
							<td style='height: 25px; font-weight:bold; text-align:center; background-color: #CCCCCC;'>COR</td>
							<td style='height: 25px; font-weight:bold; text-align:center; background-color: #CCCCCC;'>ANO</td>
							<td style='height: 25px; font-weight:bold; text-align:center; background-color: #CCCCCC;'>OPCS</td>
							<td style='height: 25px; font-weight:bold; text-align:center; background-color: #CCCCCC;'>PLACA</td>
							<td style='height: 25px; font-weight:bold; text-align:center; background-color: #CCCCCC;'>KM</td>
							<td style='height: 25px; font-weight:bold; text-align:center; background-color: #CCCCCC;'>COMB</td>
							<td style='height: 25px; font-weight:bold; text-align:center; background-color: #CCCCCC;'>PREÇO</td>
							<td style='height: 25px; font-weight:bold; text-align:center; background-color: #CCCCCC;'></td>
						  </tr>";
				
				}
				
				
				if($veiculos['ano_fabricacao'] == "Zero" || $veiculos['ano_modelo'] == "Zero"){
				
					$stringAno = "Zero";
				
				}else{
				
					$stringAno = substr($veiculos['ano_fabricacao'],-2)."/".substr($veiculos['ano_modelo'],-2);
				
				}
				
				
				echo "<tr>
						<td title='Modelo'>".substr($veiculos['modelo'],0,20)."</td>
						<td title='Cor'>".$veiculos['cor']."</td>
						<td title='Ano'>".$stringAno."</td>
						<td width='30%' title='Opcionais'>".$stringOpcional."</td>
						<td title='Placa'>".$veiculos['placa']."</td>
						<td title='Km'>".$veiculos['km']."</td>
						<td title='Combustivel'>".substr($veiculos['combustivel'],0,1)."</td>
						<td title='Valor venda'>R$ ".money_format("%i",$veiculos['valor_venda'])."</td>
						<td title='Adicionar'><input type='button' class='' value='Add' style='font-weight:bold; color: orange;' title='Mais um veículo' onClick='adicionaVeiculoCheio(\"".mb_convert_encoding($veiculos['marca'], 'UTF-8', 'ISO-8859-1')."\",\"".$veiculos['modelo']."\",\"".$veiculos['ano_modelo']."\");' /></td>
					</tr>";
			
			}
			
			echo "</table>";
			
		}elseif($this->_getParam('fn') == 'get_despesas_avaliacoes'){

			if(isset($_POST['placa'])){
				$dbParametros = new Application_Model_DbTable_ParametrosAvaliacoes();
				$dbAvaliacoes = new Application_Model_DbTable_Avaliacoes();

				$arrParam = $dbParametros->getParametros($_SESSION['sessionUser']['id_empresa'])[0];
				$arr = $dbAvaliacoes->getAvaliacaoPlaca($_SESSION['sessionUser']['id_empresa'], $_POST['placa'])[0];

				$total = 0;

				if($arr['motor'] == "Sim"){
					$total += $arrParam['motor'];
				}
				
				if($arr['caixa_direcao'] == "Sim"){
					$total += $arrParam['caixa_direcao'];
				}

				if($arr['cambio'] == "Sim"){
					if($arr['tipo_cambio'] == "manual"){
						$total += $arrParam['cambio_manual'];
					}
					if($arr['tipo_cambio'] == "automatico"){
						$total += $arrParam['cambio_automatico'];
					}
				}

				if($arr['suspensao'] == "Sim"){
					$total += $arrParam['suspensao'];
				}

				if($arr['embreagem'] == "Sim"){
					$total += $arrParam['embreagem'];
				}

				if($arr['freios'] == "Sim"){
					$total += $arrParam['freios'];
				}

				if($arr['escapamento'] == "Sim"){
					$total += $arrParam['escapamento'];
				}

				if($arr['eletrica'] == "Sim"){
					$total += $arrParam['eletrica'];
				}

				if($arr['luz_painel'] == "Sim"){
					$total += $arrParam['luz_painel'];
				}

				if($arr['ar_condicionado'] == "Sim"){
					$total += $arrParam['ar_condicionado'];
				}

				if($arr['qtd_pneus'] > 0){
					$total += $arr['qtd_pneus']*$arrParam[$arr['aro']];
				}

				if($arr['faixa'] > 0){
					$total += $arr['faixa']*$arrParam[$arr['tapecaria']];
				}

				// for ($i=1; $i <= 5; $i++) { 
				// 	if(isset($arr['despesa_valor_'.$i])){
				// 		$total += (float) str_replace(".", "", explode(",", $arr['despesa_valor_'.$i])[0]);
				// 	}
				// }


				$sevico[1] = "martelinho";
	            $sevico[2] = "pincelar";
	            $sevico[3] = "pintar";
	            $sevico[4] = "funilaria";
	            $sevico[5] = "trocar_pecas";

	            if($arr['data_lateral_dianteira_esquerda'] > 0){
	            	$total += $arrParam[$sevico[$arr['data_lateral_dianteira_esquerda']]];
	            }

	            if($arr['data_lateral_traseira_esquerda'] > 0){
	            	$total += $arrParam[$sevico[$arr['data_lateral_traseira_esquerda']]];
	            }

	            if($arr['data_porta_traseira_esquerda'] > 0){
	            	$total += $arrParam[$sevico[$arr['data_porta_traseira_esquerda']]];
	            }

	            if($arr['data_porta_dianteira_esquerda'] > 0){
	            	$total += $arrParam[$sevico[$arr['data_porta_dianteira_esquerda']]];
	            }

	            if($arr['data_vidro_dianteiro_esquerdo'] > 0){
	            	$total += $arrParam['vidro'];
	            }

	            if($arr['data_vidro_traseiro_esquerdo'] > 0){
	            	$total += $arrParam['vidro'];
	            }

	            if($arr['data_roda_traseira_esquerda'] > 0){
	            	$total += $arrParam['roda'];
	            }

	            if($arr['data_roda_dianteira_esquerda'] > 0){
	            	$total += $arrParam['roda'];
	            }
//////////////////////////////////////////////////////////////////////////////////////////////////////
	            if($arr['data_lateral_dianteira_direita'] > 0){
	            	$total += $arrParam[$sevico[$arr['data_lateral_dianteira_direita']]];
	            }

	            if($arr['data_lateral_traseira_direita'] > 0){
	            	$total += $arrParam[$sevico[$arr['data_lateral_traseira_direita']]];
	            }

	            if($arr['data_porta_traseira_direita'] > 0){
	            	$total += $arrParam[$sevico[$arr['data_porta_traseira_direita']]];
	            }

	            if($arr['data_porta_dianteira_direita'] > 0){
	            	$total += $arrParam[$sevico[$arr['data_porta_dianteira_direita']]];
	            }

	            if($arr['data_vidro_dianteiro_direito'] > 0){
	            	$total += $arrParam['vidro'];
	            }

	            if($arr['data_vidro_traseiro_direito'] > 0){
	            	$total += $arrParam['vidro'];
	            }

	            if($arr['data_roda_traseira_direita'] > 0){
	            	$total += $arrParam['roda'];
	            }

	            if($arr['data_roda_dianteira_direita'] > 0){
	            	$total += $arrParam['roda'];
	            }
//////////////////////////////////////////////////////////////////////////////////////////////////////
	            if($arr['data_vidro_dianteiro'] > 0){
	            	$total += $arrParam['vidro'];
	            }
//////////////////////////////////////////////////////////////////////////////////////////////////////
	            if($arr['data_capo'] > 0){
	            	$total += $arrParam[$sevico[$arr['data_capo']]];
	            }

	            if($arr['data_parachoque_dianteiro'] > 0){
	            	$total += $arrParam[$sevico[$arr['data_parachoque_dianteiro']]];
	            }
	           
	            if($arr['data_farol_direito'] > 0){
	            	$total += $arrParam['farol'];
	            }

	            if($arr['data_farol_esquerdo'] > 0){
	            	$total += $arrParam['farol'];
	            }

	            if($arr['data_retrovisor_direito'] > 0){
	            	$total += $arrParam['retrovisor'];
	            }

	            if($arr['data_retrovisor_esquerdo'] > 0){
	            	$total += $arrParam['retrovisor'];
	            }

	            if($arr['data_parachoque_traseiro'] > 0){
	            	$total += $arrParam[$sevico[$arr['data_parachoque_traseiro']]];
	            }

	            if($arr['data_tampa_porta'] > 0){
	            	$total += $arrParam[$sevico[$arr['data_tampa_porta']]];
	            }

	            if($arr['data_vidro_traseiro'] > 0){
	            	$total += $arrParam['vidro'];
	            }

	            if($arr['data_lanterna_esquerda_traseira'] > 0){
	            	$total += $arrParam['lanterna'];
	            }

	            if($arr['data_lanterna_direita_traseira'] > 0){
	            	$total += $arrParam['lanterna'];
	            }


				echo number_format($total,2,',','.')."_".$arr['id'];

			}


		}elseif($this->_getParam('fn') == 'preparacao'){



			echo $this->_getParam('dados');

			foreach (json_decode(json_encode($this->_getParam('dados'))) as $key => $value) {
				echo $idVeiculo = $value['id_veiculo'];
			}

			//var_export(json_decode($_POST['jSon']));


			/*$idVeiculo = $this->_getParam('id_veiculo');
			$servicoName = $this->_getParam('servico_nome');

				
			$arr[$servicoName] = $this->_getParam('id_servico');
			$arr[$servicoName."_obs"] = $this->_getParam('obs');
			
			if($this->_getParam('servico_nome') != "concluido"){
				$arr[$servicoName."_date_entrada"] = implode("-", array_reverse(explode("/", $this->_getParam('dataEntrada'))));
			}
			
			$arr[$servicoName."_date_concluido"] = implode("-", array_reverse(explode("/", $this->_getParam('dataConclusao'))));

			$dbVeiculos = new Application_Model_DbTable_Veiculos();

			if($arr[$servicoName] == 1){

				$arrVeiculo = $dbVeiculos->getVeiculoEstoque($idVeiculo);
				
				$arr2['mecanico'] = $arrVeiculo[0]['mecanico'];
				$arr2['funilaria'] = $arrVeiculo[0]['funilaria'];
				$arr2['martelinho'] = $arrVeiculo[0]['martelinho'];
				$arr2['eletrica'] = $arrVeiculo[0]['eletrica'];
				$arr2['tapecaria'] = $arrVeiculo[0]['tapecaria'];
				$arr2['lavacar'] = $arrVeiculo[0]['lavacar'];
				$arr2['volante'] = $arrVeiculo[0]['volante'];
				$arr2['calotas'] = $arrVeiculo[0]['calotas'];
				$arr2['pneus'] = $arrVeiculo[0]['pneus'];
				$arr2['chaveiro'] = $arrVeiculo[0]['chaveiro'];
				$arr2['pincelar'] = $arrVeiculo[0]['pincelar'];
				$arr2['outros'] = $arrVeiculo[0]['outros'];
				$arr2['concluido'] = $arrVeiculo[0]['concluido'];
				
				foreach($arr2 as $key=>$ar){
					if($key == $servicoName){
						break;
					}elseif($ar == 0){
						$arr[$key] = 3;
					}
	
				}

			}elseif($arr[$servicoName] == 0){
				
				$arrVeiculo = $dbVeiculos->getVeiculoEstoque($idVeiculo);
				
				$arr2['mecanico'] = $arrVeiculo[0]['mecanico'];
				$arr2['funilaria'] = $arrVeiculo[0]['funilaria'];
				$arr2['martelinho'] = $arrVeiculo[0]['martelinho'];
				$arr2['eletrica'] = $arrVeiculo[0]['eletrica'];
				$arr2['tapecaria'] = $arrVeiculo[0]['tapecaria'];
				$arr2['lavacar'] = $arrVeiculo[0]['lavacar'];
				$arr2['volante'] = $arrVeiculo[0]['volante'];
				$arr2['calotas'] = $arrVeiculo[0]['calotas'];
				$arr2['pneus'] = $arrVeiculo[0]['pneus'];
				$arr2['chaveiro'] = $arrVeiculo[0]['chaveiro'];
				$arr2['pincelar'] = $arrVeiculo[0]['pincelar'];
				$arr2['outros'] = $arrVeiculo[0]['outros'];
				$arr2['concluido'] = $arrVeiculo[0]['concluido'];
				
				$passou = false;
				foreach($arr2 as $key=>$ar){
					if($key == $servicoName){
						$passou = true;
					}
					if($passou && $ar == 1){
						$arr[$servicoName] = 3;
					}
				}
				
			}

			if($idVeiculo){
				if($this->_getParam('servico_nome') == "concluido" && $arr[$servicoName."_date_concluido"] && $arr[$servicoName."_date_concluido"] != "0000-00-00" && $arr[$servicoName] == 1){
					$arr['data_termino_revisao'] = $arr[$servicoName."_date_concluido"];
				}
				
				$arr['nome_usuario_preparacao'] = $_SESSION['sessionUser']['nome'];
				$arr['data_hora'] = @date('Y-m-d H:i:s');
				$arr['item_alterado'] = $servicoName;
				
				$dbVeiculos->edt($idVeiculo, $arr);
			}
*/
		}
		
	}


	public function novaPreparacaoAction(){

		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');

	  	$idVeiculo = $_POST['data'][0]['id_veiculo'];

		foreach($_POST['data'] as $arr){

			$arrDados[$arr['servico_nome']] = $arr['id_servico'];
			$arrDados[$arr['servico_nome']."_obs"] = $arr['obs'];

			if($arr['servico_nome'] == "concluido"){
				if($arr['id_servico'] == 1){
					$arrDados[$arr['servico_nome']."_date_concluido"] = implode("-", array_reverse(explode("/", $arr['dataConclusao'])));
					$arrDados['data_termino_revisao'] = @date('Y-m-d');
				}
			}else{
				$arrDados[$arr['servico_nome']."_date_entrada"] = implode("-", array_reverse(explode("/", $arr['dataEntrada'])));
				$arrDados[$arr['servico_nome']."_date_concluido"] = implode("-", array_reverse(explode("/", $arr['dataConclusao'])));
				$arrDados[$arr['servico_nome']."_dias_extras"] = $arr['dias_extras'];
			}

		}

		$dbVeiculos = new Application_Model_DbTable_Veiculos();

		

		if($_POST['data'][0]['id_servico'] == 1){

			$arrVeiculo = current($dbVeiculos->getVeiculoEstoque($idVeiculo));

			$arr2['mecanico'] = $arrVeiculo['mecanico'];
			$arr2['funilaria'] = $arrVeiculo['funilaria'];
			$arr2['martelinho'] = $arrVeiculo['martelinho'];
			$arr2['eletrica'] = $arrVeiculo['eletrica'];
			$arr2['tapecaria'] = $arrVeiculo['tapecaria'];
			$arr2['lavacar'] = $arrVeiculo['lavacar'];
			$arr2['volante'] = $arrVeiculo['volante'];
			$arr2['calotas'] = $arrVeiculo['calotas'];
			$arr2['pneus'] = $arrVeiculo['pneus'];
			$arr2['chaveiro'] = $arrVeiculo['chaveiro'];
			$arr2['pincelar'] = $arrVeiculo['pincelar'];
			$arr2['outros'] = $arrVeiculo['outros'];
			$arr2['concluido'] = $arrVeiculo['concluido'];
			
			foreach($arr2 as $key=>$ar){
				if($key == $_POST['data'][0]['servico_nome']){
					break;
				}elseif($ar == 0){
					$arrDados[$key] = 3;
				}

			}

		}elseif($_POST['data'][0]['id_servico'] == 0){

			$arrVeiculo = current($dbVeiculos->getVeiculoEstoque($idVeiculo));

			$arr2['mecanico'] = $arrVeiculo['mecanico'];
			$arr2['funilaria'] = $arrVeiculo['funilaria'];
			$arr2['martelinho'] = $arrVeiculo['martelinho'];
			$arr2['eletrica'] = $arrVeiculo['eletrica'];
			$arr2['tapecaria'] = $arrVeiculo['tapecaria'];
			$arr2['lavacar'] = $arrVeiculo['lavacar'];
			$arr2['volante'] = $arrVeiculo['volante'];
			$arr2['calotas'] = $arrVeiculo['calotas'];
			$arr2['pneus'] = $arrVeiculo['pneus'];
			$arr2['chaveiro'] = $arrVeiculo['chaveiro'];
			$arr2['pincelar'] = $arrVeiculo['pincelar'];
			$arr2['outros'] = $arrVeiculo['outros'];
			$arr2['concluido'] = $arrVeiculo['concluido'];
			
			$passou = false;
			foreach($arr2 as $key=>$ar){
				if($key == $_POST['data'][0]['servico_nome']){
					$passou = true;
				}
				if($passou && $ar == 1){
					$arrDados[$_POST['data'][0]['servico_nome']] = 3;
				}
			}
			
		}

		if($idVeiculo){
			$arrDados['nome_usuario_preparacao'] = $_SESSION['sessionUser']['nome'];
			$arrDados['data_hora'] = @date('Y-m-d H:i:s');
			$arrDados['item_alterado'] = $arr['servico_nome'];

			$dbVeiculos->edt($idVeiculo, $arrDados);

		}

		//var_export($arrDados);

	}

	
	public function envelopeAction(){
	
		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
	
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
		
		$arr['id'] = $this->_getParam('id');
		
		$arrVeiculo = $dbVeiculos->_get($arr);
		
		if($arrVeiculo[0]['descricao_site'] == null){
		
			$arrVeiculo[0]['descricao_site'] = $arrVeiculo[0]['marca']." - ".$arrVeiculo[0]['modelo'];
		
		}
		
		if($arrVeiculo[0]['id_negociacao_troca']){
		
			$arrNegociacao = $dbNegociacoes->_getPorIdOrigem($arrVeiculo[0]['id_negociacao_troca']);
			$origem = "Troca";
		
		}elseif($arrVeiculo[0]['id_negociacao_troca2']){
		
			$arrNegociacao = $dbNegociacoes->_getPorIdOrigem($arrVeiculo[0]['id_negociacao_troca2']);
			$origem = "Troca";
		
		}elseif($arrVeiculo[0]['id_negociacao_compra']){
		
			$arrNegociacao = $dbNegociacoes->_getPorIdOrigem($arrVeiculo[0]['id_negociacao_compra']);
			$origem = "Compra";
			
		}
		
		$this->view->veiculo = $arrVeiculo[0];
		$this->view->negociacao = $arrNegociacao[0];
		$this->view->origem = $origem;
	
	}
	
	
	/*
	public function addAction(){
	
		$this->validaAcesso('gerenciar_estoque');
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbAnexosVeiculos = new Application_Model_DbTable_AnexosVeiculos();
		
		$this->view->marcas = $dbVeiculos->getMarcasDistintas();
		
		if($this->_getParam('troca') == 1){
		
			$this->view->troca = 1;
		
		}else{
		
			$this->view->troca = 0;
		
		}
		
		//var_export($_POST);
	
		if($this->getRequest()->isPost()){
		
			$dadosVeiculos['id_modelo'] = $_POST['id_modelo'];
			$dadosVeiculos['descricao_site'] = $_POST['descricao_site'];
			$dadosVeiculos['placa'] = $_POST['placa'];
			$dadosVeiculos['renavam'] = $_POST['renavam'];
			$dadosVeiculos['novo_usado'] = $_POST['novo_usado'];
			$dadosVeiculos['ano_fabricacao'] = $_POST['ano_fabricacao'];
			$dadosVeiculos['cor'] = $_POST['cor'];
			$dadosVeiculos['km'] = $_POST['km'];
			$dadosVeiculos['combustivel'] = $_POST['combustivel'];
			$dadosVeiculos['chassi'] = $_POST['chassi'];
			$dadosVeiculos['motor'] = $_POST['motor'];
			$dadosVeiculos['valor_aquisicao'] = $_POST['valor_aquisicao'];
			$dadosVeiculos['valor_aquisicao'] = str_replace(".","",$dadosVeiculos['valor_aquisicao']);
			$dadosVeiculos['valor_aquisicao'] = str_replace(",",".",$dadosVeiculos['valor_aquisicao']);		
			$dadosVeiculos['data_aquisicao'] = implode("-",array_reverse(explode("/",$_POST['data_aquisicao'])));
			$dadosVeiculos['valor_venda'] = $_POST['valor_venda'];
			$dadosVeiculos['valor_venda'] = str_replace(".","",$dadosVeiculos['valor_venda']);
			$dadosVeiculos['valor_venda'] = str_replace(",",".",$dadosVeiculos['valor_venda']);	
			$dadosVeiculos['id_empresa'] = $_POST['id_empresa'];
			//$dadosVeiculos['temp_troca'] = $_POST['temp_troca'];
			$dadosVeiculos['obs_interna'] = $_POST['obs_interna'];
			$dadosVeiculos['obs_site'] = $_POST['obs_site'];
			$dadosVeiculos['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dadosVeiculos['hora_alteracao'] = @date("Y-m-d H:i:s");
			$dadosVeiculos['exibir_valor_site'] = $_POST['exibir_valor_site'];
			$dadosVeiculos['exibir_km'] = $_POST['exibir_km_1']+$_POST['exibir_km_2'];
			$dadosVeiculos['exibir_site_estoque'] = $_POST['exibir_site_estoque_1']+$_POST['exibir_site_estoque_2'];
			
			if(!$_POST['ativo']){
			
				$dadosVeiculos['ativo'] = 0;
			
			}else{
			
				$dadosVeiculos['ativo'] = $_POST['ativo'];
				
			}
			
			if($_POST['origem'] == "0"){
			
				$dadosVeiculos['origem'] = $_POST['origem_descricao'];
			
			}else{
			
				$dadosVeiculos['origem'] = $_POST['origem'];
			
			}
			
			if($_POST['consignado']){
			
				$dadosVeiculos['consignado'] = 1;
			
			}else{
			
				$dadosVeiculos['consignado'] = 0;
			
			}

			$mensagem = "";
			
			$cont = 1;
		

			if($dbVeiculos->add($dadosVeiculos)){

				$idVeiculos = $dbVeiculos->getLastId();
				
				$arrCheckList['id_veiculo'] =  $idVeiculos[0]['id'];
				$arrCheckList['quitado_leasing'] = $_POST['quitado_leasing'];
				$arrCheckList['pf_pj'] = $_POST['pf_pj'];
				$arrCheckList['gnv'] = $_POST['gnv'];
				
				if($_POST['gnv'] == 0){
				
					$arrCheckList['doc_gnv'] = 3;
				
				}else{
				
					$arrCheckList['doc_gnv'] = $_POST['doc_gnv'];
				
				}
				
				$dbCheckList = new Application_Model_DbTable_CheckList();
				
				if(!$dbCheckList->add($arrCheckList)){
				
					$mensagem .= "Erro ao cadastrar check-list.<br>";
				
				}
				
				$conts = 0;
				foreach($_FILES as $chave=>$valores){
				
					if($chave == "foto"){
				
						foreach($_FILES["foto"]["error"] as $key => $error){
						
							if($_FILES['foto']['size'][$key] <= 200000){
							
								if(!file_exists(("fotos_veiculos/".$dadosVeiculos['id_empresa']))){
							
									$extensao = strtolower(end(explode(".",$_FILES['foto']['name'][$key])));
									mkdir("fotos_veiculos/".$dadosVeiculos['id_empresa']);
									$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Ymdhis").$key.".".$extensao;
								
								}else{
									
									$extensao = strtolower(end(explode(".",$_FILES['foto']['name'][$key])));
									$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Ymdhis").$key.".".$extensao;
								
								}
								
								if($_FILES['foto']['tmp_name'][$key] != ""){
								
									$copied = copy($_FILES['foto']['tmp_name'][$key], $novoNome);
								
								}
								
								if($copied){
								
									$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
									
									$dadosFotos['id_veiculo'] = $idVeiculos[0]['id'];
									$dadosFotos['path'] = stripslashes($novoNome);
									
									if($conts == 0){
									
										$dadosFotos['capa'] = 1;
										$conts++;
									
									}else{
									
										$dadosFotos['capa'] = 0;
									
									}
								
									if(!$dbFotosVeiculos->add($dadosFotos)){
									
										$mensagem .= "Erro ao realizar o upload de fotos.<br>";

									}
								
								}
								
								if($cont == 5){
									
									break;
								
								}
								
								$cont++;
								
							}
						
						}
					
					}
				
				}
				
				//PEGA ANEXOS
				foreach($_FILES as $chave=>$valor){
				
					$anexos = explode("_",$chave);

					if($anexos[0] == "anexo"){
			
						if($_FILES[$chave]['name']){
							
							if(!file_exists(("anexos_veiculos/".$dadosVeiculos['id_empresa']))){
				
								mkdir("anexos_veiculos/".$dadosVeiculos['id_empresa']);
								$novoNome = "anexos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Y-m-d h:i:s")."_".$_FILES[$chave]['name'];
							
							}else{
							
								$novoNome = "anexos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Y-m-d h:i:s")."_".$_FILES[$chave]['name'];
							
							}
					
							$copied = copy($_FILES[$chave]['tmp_name'], $novoNome);
	
							if($copied){
					
								$dbAnexosVeiculos = new Application_Model_DbTable_AnexosVeiculos();
								
								$dadosAnexos['id_veiculo'] = $idVeiculos[0]['id'];
								$dadosAnexos['path'] = $novoNome;
								$dadosAnexos['descricao'] = $_POST['descricaoanexo_'.$anexos[1]];
							
								if(!$dbAnexosVeiculos->add($dadosAnexos)){
								
									$mensagem .= "Erro ao realizar o upload de anexos.<br>";
									
								
								}
					
							}
						
						}
					
					}
				
				}
				
				foreach($_POST as $chave=>$valor){
				
					$despesas = explode("_",$chave);

					if($despesas[0] == "Ddata"){
					
						$dadosDespesas[$despesas[1]]['data'] = $valor;
					
					}
				
					if($despesas[0] == "Ddescricao"){
					
						$dadosDespesas[$despesas[1]]['despesa'] = $valor;
					
					}
					
					if($despesas[0] == "Dfornecedor"){
					
						$dadosDespesas[$despesas[1]]['id_fornecedor'] = $valor;
					
					}
					
					if($despesas[0] == "Dvalor"){
					
						$dadosDespesas[$despesas[1]]['valor'] = $valor;
					
					}
					
					if($despesas[0] == "Dgarantia"){
					
						$dadosDespesas[$despesas[1]]['dias_garantia'] = $valor;
					
					}
					
					if($despesas[0] == "Dnf"){
					
						$dadosDespesas[$despesas[1]]['nf'] = $valor;
					
					}
					
					
					//PEGA OPCIONAIS
					$opcional = explode("_",$chave);

					if($opcional[0] == "opcional"){
					
						$opcionais['id_veiculo'] = $idVeiculos[0]['id'];
						$opcionais['id_opcional'] = $opcional[1];
						
						if(!$dbOpcionaisVeiculos->add($opcionais)){
						
							$mensagem .= "Erro ao adicionar opcionais.<br>";
						
						}
					
					}
					
				}
				
				
				
				//PEGA DESPESAS
				foreach($dadosDespesas as $despesas){
				
					$data = explode("/",$despesas['data']);
					
					$despesas['data'] = $data[2]."-".$data[1]."-".$data[0];
					
					$despesas['id_veiculo'] = $idVeiculos[0]['id'];
				
					$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
					
					if($despesas['despesa'] != "" || $despesas['id_fornecedor'] != "" || $despesas['valor'] != ""){
					
						$despesas['valor'] = $despesas['valor'];
						$despesas['valor'] = str_replace(".","",$despesas['valor']);
						$despesas['valor'] = str_replace(",",".",$despesas['valor']);		
					
						if(!$dbDespesasVeiculos->add($despesas)){
						
							$mensagem .= "Erro ao adicionar despesas.<br>";
						
						}
						
					}
				
				}
				
				//PEGA PENDENCIAS
				foreach($_POST as $chave=>$valor){
				
					$pendencias = explode("_",$chave);

					if($pendencias[0] == "Pdata"){
					
						$dadosPendencias[$pendencias[1]]['data'] = $valor;
					
					}
					
					if($pendencias[0] == "Pdescricao"){
					
						$dadosPendencias[$pendencias[1]]['descricao'] = $valor;
					
					}
					
				}
				
				foreach($dadosPendencias as $pandencia){
				
					$data = explode("/",$pandencia['data']);
					
					$pandencia['data'] = $data[2]."-".$data[1]."-".$data[0];
					
					$pandencia['id_veiculo'] = $idVeiculos[0]['id'];
					$pandencia['baixada'] = 0;
				
					$dbPendenciasVeiculos = new Application_Model_DbTable_PendenciasVeiculos();
					
					if($pandencia['descricao'] != ""){
					
						if(!$dbPendenciasVeiculos->add($pandencia)){
						
							$mensagem .= "Erro ao adicionar pendências.<br>";
						
						}
						
					}
				
				}

			}
	
			if($mensagem == ""){
				
				$this->_helper->redirector->gotoUrl("veiculos/edt/id/".$idVeiculos[0]['id']."/termino/1/´msg/".$mensagem);
			
			}else{
			
				$this->view->mensagem = $mensagem;
			
			}

		}
	
	}
	*/
	
	
	public function addBKP02Action(){

		// echo "<pre>";
		// var_export($_POST);
		// echo "</pre>";
		// exit;

		$this->validaAcesso('gerenciar_estoque');
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbAnexosVeiculos = new Application_Model_DbTable_AnexosVeiculos();
		$dbOrigemClientes = new Application_Model_DbTable_OrigemClientes();
		
		$this->view->marcas = $dbVeiculos->getMarcasDistintas();
		$this->view->origemClientes = $dbOrigemClientes->_getOrigem(array('noDefault' => true, 'id_empresa' => $_SESSION['sessionUser']['id_empresa']));
		
		if($this->_getParam('troca') == 1){
		
			$this->view->troca = 1;
		
		}else{
		
			$this->view->troca = 0;
		
		}

		if($this->getRequest()->isPost()){
		
			if(!$_POST['id_empresa']){
				$_POST['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			}
		
			$dadosVeiculos['id_modelo'] = $_POST['id_modelo'];
			$dadosVeiculos['descricao_site'] = $_POST['descricao_site'];
			$dadosVeiculos['placa'] = $_POST['placa'];
			$dadosVeiculos['renavam'] = $_POST['renavam'];
			$dadosVeiculos['novo_usado'] = $_POST['novo_usado'];
			$dadosVeiculos['ano_fabricacao'] = $_POST['ano_fabricacao'];
			$dadosVeiculos['cor'] = $_POST['cor'];
			$dadosVeiculos['km'] = $_POST['km'];
			$dadosVeiculos['combustivel'] = $_POST['combustivel'];
			$dadosVeiculos['chassi'] = $_POST['chassi'];
			$dadosVeiculos['motor'] = $_POST['motor'];
			$dadosVeiculos['valor_aquisicao'] = $_POST['valor_aquisicao'];
			$dadosVeiculos['valor_aquisicao'] = str_replace(".","",$dadosVeiculos['valor_aquisicao']);
			$dadosVeiculos['valor_aquisicao'] = str_replace(",",".",$dadosVeiculos['valor_aquisicao']);		
			$dadosVeiculos['data_aquisicao'] = implode("-",array_reverse(explode("/",$_POST['data_aquisicao'])));
			$dadosVeiculos['valor_venda'] = $_POST['valor_venda'];
			$dadosVeiculos['valor_venda'] = str_replace(".","",$dadosVeiculos['valor_venda']);
			$dadosVeiculos['valor_venda'] = str_replace(",",".",$dadosVeiculos['valor_venda']);	
			$dadosVeiculos['id_empresa'] = $_POST['id_empresa'];
			$dadosVeiculos['loja'] = $_POST['loja'];
			//$dadosVeiculos['temp_troca'] = $_POST['temp_troca'];
			$dadosVeiculos['obs_interna'] = $_POST['obs_interna'];
			$dadosVeiculos['video'] = $_POST['video'];
			$dadosVeiculos['obs_site'] = $_POST['obs_site'];
			$dadosVeiculos['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dadosVeiculos['hora_alteracao'] = @date("Y-m-d H:i:s");
			$dadosVeiculos['exibir_valor_site'] = $_POST['exibir_valor_site'];
			$dadosVeiculos['exibir_km'] = $_POST['exibir_km_1']+$_POST['exibir_km_2'];
			$dadosVeiculos['exibir_site_estoque'] = $_POST['exibir_site_estoque_1']+$_POST['exibir_site_estoque_2'];

			if($_POST['data_inicio_preparacao']){

				$dadosVeiculos['data_inicio_preparacao'] = implode("-",array_reverse(explode("/",$_POST['data_inicio_preparacao'])));

				$dbPeriodoPreparacao = new Application_Model_DbTable_PeriodoPreparacao();
				$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
				$arrPeriodos = current($dbPeriodoPreparacao->getPeriodoPreparacao($arr));

				$servico = "mecanico";
				$dadosVeiculos[$servico.'_date_entrada'] = $dadosVeiculos['data_inicio_preparacao'];
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "funilaria";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "martelinho";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "eletrica";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "tapecaria";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "lavacar";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "volante";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "calotas";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "pneus";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "chaveiro";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "pincelar";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "outros";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				//$servico = "concluido";
				//$dadosVeiculos[$servico.'_date_concluido'] = $dataFimTemp;

			}

			
			if(!$_POST['ativo']){
			
				$dadosVeiculos['ativo'] = 0;
			
			}else{
			
				$dadosVeiculos['ativo'] = $_POST['ativo'];
				
			}
			
			if($_POST['origem'] == "0"){
			
				$dadosVeiculos['origem'] = $_POST['origem_descricao'];
			
			}else{
			
				$dadosVeiculos['origem'] = $_POST['origem'];
			
			}
			
			if($_POST['consignado'] == 0){
			
				$dadosVeiculos['consignado'] = 0;
			
			}elseif($_POST['consignado'] == 1){
			
				$dadosVeiculos['consignado'] = 1;
			
			}elseif($_POST['consignado'] == 2){
			
				$dadosVeiculos['consignado'] = 2;
			
			}elseif($_POST['consignado'] == 3){
			
				$dadosVeiculos['consignado'] = 3;
			
			}

			$mensagem = "";
			
			$cont = 1;
		
			if($dbVeiculos->add($dadosVeiculos)){

				$idVeiculos = $dbVeiculos->getLastId();
				
				$arrCheckList['id_veiculo'] =  $idVeiculos[0]['id'];
				$arrCheckList['quitado_leasing'] = $_POST['quitado_leasing'];
				$arrCheckList['pf_pj'] = $_POST['pf_pj'];
				$arrCheckList['gnv'] = $_POST['gnv'];
				
				if($_POST['gnv'] == 0){
				
					$arrCheckList['doc_gnv'] = 3;
				
				}else{
				
					$arrCheckList['doc_gnv'] = $_POST['doc_gnv'];
				
				}
				
				$dbCheckList = new Application_Model_DbTable_CheckList();
				
				if(!$dbCheckList->add($arrCheckList)){
				
					$mensagem .= "Erro ao cadastrar check-list.<br>";
				
				}
				
				$capa = explode("_", $_POST['capa']);
				
				$conts = 0;
				foreach($_FILES as $chave=>$valores){

					if($chave == "foto"){
				
						foreach($_FILES["foto"]["error"] as $key => $error){
							
							if(!file_exists(("fotos_veiculos/".$dadosVeiculos['id_empresa']))){
							
								$extensao = strtolower(end(explode(".",$_FILES['foto']['name'][$key])));
								mkdir("fotos_veiculos/".$dadosVeiculos['id_empresa']);
								chmod("fotos_veiculos/".$dadosVeiculos['id_empresa'], 0755);
								$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$dadosVeiculos['descricao_site']))."-".$dadosVeiculos['ano_fabricacao']."-".@date("his").$key.".".$extensao;
								
							}else{
						
								$extensao = strtolower(end(explode(".",$_FILES['foto']['name'][$key])));
								$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$dadosVeiculos['descricao_site']))."-".$dadosVeiculos['ano_fabricacao']."-".@date("his").$key.".".$extensao;
								
							}
								
							if($_FILES['foto']['tmp_name'][$key] != ""){
								
								
								/////////////////REDIMENCIONA IMAGEM///////////////////////
								# Caminho da imagem a ser redimensionada: 
								$input_image = $_FILES['foto']['tmp_name'][$key];
									 
								// Pega o tamanho original da imagem e armazena em um Array:
								$size = getimagesize( $input_image );
									 
								// Configura a nova largura da imagem:
								$thumb_width = "780";
									 
								// Calcula a altura da nova imagem para manter a proporção na tela: 
								$thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );
									 
								// Cria a imagem com as cores reais originais na memória.
								$thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );
									 
								// Criará uma nova imagem do arquivo.
								$src_img = ImageCreateFromJPEG( $input_image );
									 
								// Criará a imagem redimensionada:
								ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );
									 
								// Informe aqui o novo nome da imagem e a localização:
								$copied = ImageJPEG( $thumbnail, $novoNome);
								chmod($novoNome, 0755); 
								// Limpa da memoria a imagem criada temporáriamente: 
								ImageDestroy( $thumbnail );
								
								
								//$copied = copy($_FILES['foto']['tmp_name'][$key], $novoNome);
								
							}
								
							if($copied){
								
								$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
					
								$dadosFotos['id_veiculo'] = $idVeiculos[0]['id'];
								$dadosFotos['path'] = stripslashes($novoNome);
						
								if($conts == $capa[1]){
									
									$dadosFotos['capa'] = 1;
									$conts++;
									
								}else{
									
									$dadosFotos['capa'] = 0;
									$conts++;
									
								}
								
								if(!$dbFotosVeiculos->add($dadosFotos)){
									
									$mensagem .= "Erro ao realizar o upload de fotos.<br>";

								}
								
							}
								
							if($cont == 9){
					
								break;
								
							}
				
							$cont++;
						
						}
					
					}
				
				}
				
				//PEGA ANEXOS
				foreach($_FILES as $chave=>$valor){
				
					$anexos = explode("_",$chave);

					if($anexos[0] == "anexo"){
			
						if($_FILES[$chave]['name']){
							
							if(!file_exists(("anexos_veiculos/".$dadosVeiculos['id_empresa']))){
				
								mkdir("anexos_veiculos/".$dadosVeiculos['id_empresa']);
								chmod("anexos_veiculos/".$dadosVeiculos['id_empresa'], 0755); 
								$novoNome = "anexos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Y-m-d h:i:s")."_".$_FILES[$chave]['name'];
							
							}else{
							
								$novoNome = "anexos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Y-m-d h:i:s")."_".$_FILES[$chave]['name'];
							
							}
					
							$copied = copy($_FILES[$chave]['tmp_name'], $novoNome);
							
	
							if($copied){
								
								chmod($novoNome, 0755);
								
								$dbAnexosVeiculos = new Application_Model_DbTable_AnexosVeiculos();
								
								$dadosAnexos['id_veiculo'] = $idVeiculos[0]['id'];
								$dadosAnexos['path'] = $novoNome;
								$dadosAnexos['descricao'] = $_POST['descricaoanexo_'.$anexos[1]];
							
								if(!$dbAnexosVeiculos->add($dadosAnexos)){
								
									$mensagem .= "Erro ao realizar o upload de anexos.<br>";
									
								
								}
					
							}
						
						}
					
					}
				
				}
				
				foreach($_POST as $chave=>$valor){
				
					$despesas = explode("_",$chave);

					if($despesas[0] == "Ddata"){
					
						$dadosDespesas[$despesas[1]]['data'] = $valor;
					
					}
				
					if($despesas[0] == "Ddescricao"){
					
						$dadosDespesas[$despesas[1]]['despesa'] = $valor;
					
					}
					
					if($despesas[0] == "Dfornecedor"){
					
						$dadosDespesas[$despesas[1]]['id_fornecedor'] = $valor;
					
					}
					
					if($despesas[0] == "Dvalor"){
					
						$dadosDespesas[$despesas[1]]['valor'] = $valor;
					
					}
					
					if($despesas[0] == "Dgarantia"){
					
						$dadosDespesas[$despesas[1]]['dias_garantia'] = $valor;
					
					}
					
					if($despesas[0] == "Dnf"){
					
						$dadosDespesas[$despesas[1]]['nf'] = $valor;
					
					}
					
					
					//PEGA OPCIONAIS
					$opcional = explode("_",$chave);

					if($opcional[0] == "opcional"){
					
						$opcionais['id_veiculo'] = $idVeiculos[0]['id'];
						$opcionais['id_opcional'] = $opcional[1];
						
						if(!$dbOpcionaisVeiculos->add($opcionais)){
						
							$mensagem .= "Erro ao adicionar opcionais.<br>";
						
						}
					
					}
					
				}
				
				
				
				//PEGA DESPESAS
				foreach($dadosDespesas as $despesas){
				
					$data = explode("/",$despesas['data']);
					
					$despesas['data'] = $data[2]."-".$data[1]."-".$data[0];
					
					$despesas['id_veiculo'] = $idVeiculos[0]['id'];
				
					$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
					
					if($despesas['despesa'] != "" || $despesas['id_fornecedor'] != "" || $despesas['valor'] != ""){
					
						$despesas['valor'] = $despesas['valor'];
						$despesas['valor'] = str_replace(".","",$despesas['valor']);
						$despesas['valor'] = str_replace(",",".",$despesas['valor']);		
					
						if(!$dbDespesasVeiculos->add($despesas)){
						
							$mensagem .= "Erro ao adicionar despesas.<br>";
						
						}
						
					}
				
				}
				
				//PEGA PENDENCIAS
				foreach($_POST as $chave=>$valor){
				
					$pendencias = explode("_",$chave);

					if($pendencias[0] == "Pdata"){
					
						$dadosPendencias[$pendencias[1]]['data'] = $valor;
					
					}
					
					if($pendencias[0] == "Pdescricao"){
					
						$dadosPendencias[$pendencias[1]]['descricao'] = $valor;
					
					}
					
				}
				
				foreach($dadosPendencias as $pandencia){
				
					$data = explode("/",$pandencia['data']);
					
					$pandencia['data'] = $data[2]."-".$data[1]."-".$data[0];
					
					$pandencia['id_veiculo'] = $idVeiculos[0]['id'];
					$pandencia['baixada'] = 0;
				
					$dbPendenciasVeiculos = new Application_Model_DbTable_PendenciasVeiculos();
					
					if($pandencia['descricao'] != ""){
					
						if(!$dbPendenciasVeiculos->add($pandencia)){
						
							$mensagem .= "Erro ao adicionar pendências.<br>";
						
						}
						
					}
				
				}

			}
			
			$this->geraSitemap();
			
			$this->geraXMLGlix();
			
			$this->geraXMLIbiubi();
			
			$this->geraXMLFisgo();
			
			$this->geraXMLTrovit();
			
			$this->geraXMLBuscaAcelerada();
			
			$this->geraXMLMitula();

			if($mensagem == ""){
				
				$this->_helper->redirector->gotoUrl("veiculos/edt/id/".$idVeiculos[0]['id']."/msg/".$mensagem);
			
			}else{
			
				$this->view->mensagem = $mensagem;
			
			}
			
			
			

		}
	
	}
	
	public function listaAction(){
	
		$this->validaAcesso('listar_estoque');
	
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		if($this->getRequest()->isPost()){
		
			$arr['parcial'] = true;
			$arr['modelo'] = $this->_getParam('modelo');
			$arr['placa'] = $this->_getParam('placa');
			$arr['ano_modelo'] = $this->_getParam('ano_modelo');
			
			if($this->_getParam('venda') == 1){
			
				$arr['vendido'] = $this->_getParam('venda');
			
			}
			
			if($this->_getParam('venda') == 2){
			
				$arr['vendido'] = "0";
			
			}
			
		}else{
		
			$arr['venda'] = true;
		
		}
		
		if($arr['placa']){
		
			$arrTemp = explode("-",$arr['placa']);
			
			if($arrTemp[1] == ""){
			
				$arr['placa'] = substr($arr['placa'],0,3)."-".substr($arr['placa'],3);
			
			}
		
		}

		$arrVeiculos = $dbVeiculos->getVeiculosCompleto($arr);
		
		//var_export($arrTemp[1]);
		//exit;
		
		foreach($arrVeiculos as $veiculo){
			
			$stringOpcional = "";
			
			$arrOpcionais = $dbOpcionaisVeiculos->getVeiculosOpcionais($veiculo['id']);
			$arrFotos = $dbFotosVeiculos->getNFotos($veiculo['id']);
			
			foreach($arrOpcionais as $opcional){
			
				$opcionalTemp = explode(")",$opcional['opcional']);
			
				$stringOpcional .= substr($opcionalTemp[0],1)." / ";
			
			}
			
			list($anoInicio, $mesInicio, $diaInicio) = explode('-', $veiculo['data_aquisicao']);
			list($anoFim, $mesFim, $diaFim) = explode('-', @date('Y-m-d'));
			
			$dataInicio = mktime(0,0,0, $mesInicio, $diaInicio, $anoInicio);//data compra
			$dataFim    = mktime(0,0,0, $mesFim, $diaFim, $anoFim);//data de hoje
			
			$diferenca =  $dataFim -$dataInicio;
			
			$arrVeiculoCompleto[$veiculo['id']]['id'] = $veiculo['id'];
			$arrVeiculoCompleto[$veiculo['id']]['opcionais'] = $stringOpcional;
			$arrVeiculoCompleto[$veiculo['id']]['marca'] = $veiculo['marca'];
			$arrVeiculoCompleto[$veiculo['id']]['modelo'] = $veiculo['modelo'];
			$arrVeiculoCompleto[$veiculo['id']]['placa'] = $veiculo['placa'];
			$arrVeiculoCompleto[$veiculo['id']]['cor'] = $veiculo['cor'];
			$arrVeiculoCompleto[$veiculo['id']]['ano_fabricacao'] = $veiculo['ano_fabricacao'];
			$arrVeiculoCompleto[$veiculo['id']]['ano_modelo'] = $veiculo['ano_modelo'];
			$arrVeiculoCompleto[$veiculo['id']]['km'] = $veiculo['km'];
			$arrVeiculoCompleto[$veiculo['id']]['valor_venda'] = $veiculo['valor_venda'];
			$arrVeiculoCompleto[$veiculo['id']]['ativo'] = $veiculo['ativo'];
			$arrVeiculoCompleto[$veiculo['id']]['nome'] = $veiculo['nome'];
			$arrVeiculoCompleto[$veiculo['id']]['hora_alteracao'] = $veiculo['hora_alteracao'];
			$arrVeiculoCompleto[$veiculo['id']]['fotos'] = $arrFotos[0]['total'];
			$arrVeiculoCompleto[$veiculo['id']]['de'] = round(($veiculo['vendido'] == 0) ? $diferenca/86400 : 0,0);
			$arrVeiculoCompleto[$veiculo['id']]['vendido'] = $veiculo['vendido'];
			$arrVeiculoCompleto[$veiculo['id']]['consignado'] = $veiculo['consignado'];
			$arrVeiculoCompleto[$veiculo['id']]['descricao_site'] = $veiculo['descricao_site'];
			
		}
		
		$this->view->veiculos = $arrVeiculoCompleto;
		
		//$this->geraSitemap();
		//$this->geraXMLGlix();
		//$this->geraXMLIbiubi();
		//$this->geraXMLFisgo();
		$this->geraXMLTrovit();
		//$this->geraXMLBuscaAcelerada();
		//$this->geraXMLMitula();
	
	}
	
	/*
	public function edtAction(){
	
		$this->validaAcesso('gerenciar_estoque');
	
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbAnexosVeiculos = new Application_Model_DbTable_AnexosVeiculos();
		$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
		$dbFornecedores = new Application_Model_DbTable_Fornecedores();
		$dbPendencias = new Application_Model_DbTable_PendenciasVeiculos();
		$dbModelo = new Application_Model_DbTable_Modelos();
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$this->view->ativaPendencia = $this->_getParam('p');

		if($this->getRequest()->isPost()){
		
			$dadosVeiculos['id_modelo'] = $_POST['id_modelo'];
			$dadosVeiculos['descricao_site'] = $_POST['descricao_site'];
			$dadosVeiculos['placa'] = $_POST['placa'];
			$dadosVeiculos['renavam'] = $_POST['renavam'];
			$dadosVeiculos['novo_usado'] = $_POST['novo_usado'];
			$dadosVeiculos['ano_fabricacao'] = $_POST['ano_fabricacao'];
			$dadosVeiculos['cor'] = $_POST['cor'];
			$dadosVeiculos['km'] = $_POST['km'];
			$dadosVeiculos['combustivel'] = $_POST['combustivel'];
			$dadosVeiculos['chassi'] = $_POST['chassi'];
			$dadosVeiculos['motor'] = $_POST['motor'];
			
			if($_POST['valor_aquisicao']){
			
				$dadosVeiculos['valor_aquisicao'] = $_POST['valor_aquisicao'];
				$dadosVeiculos['valor_aquisicao'] = str_replace(".","",$dadosVeiculos['valor_aquisicao']);
				$dadosVeiculos['valor_aquisicao'] = str_replace(",",".",$dadosVeiculos['valor_aquisicao']);

			}
			
			$dadosVeiculos['data_aquisicao'] = implode("-",array_reverse(explode("/",$_POST['data_aquisicao'])));
			$dadosVeiculos['valor_venda'] = $_POST['valor_venda'];
			$dadosVeiculos['valor_venda'] = str_replace(".","",$dadosVeiculos['valor_venda']);
			$dadosVeiculos['valor_venda'] = str_replace(",",".",$dadosVeiculos['valor_venda']);	
			$dadosVeiculos['id_empresa'] = $_POST['id_empresa'];
			//$dadosVeiculos['temp_troca'] = $_POST['temp_troca'];
			$dadosVeiculos['obs_interna'] = $_POST['obs_interna'];
			$dadosVeiculos['obs_site'] = $_POST['obs_site'];
			$dadosVeiculos['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
		$dadosVeiculos['hora_alteracao'] = @date("Y-m-d H:i:s");
		$dadosVeiculos['exibir_valor_site'] = $_POST['exibir_valor_site'];
		$dadosVeiculos['exibir_km'] = $_POST['exibir_km_1']+$_POST['exibir_km_2'];
		$dadosVeiculos['exibir_site_estoque'] = $_POST['exibir_site_estoque_1']+$_POST['exibir_site_estoque_2'];
		
		// Valida data_termino_revisao antes de processar
		if(!empty($_POST['data_termino_revisao']) && $_POST['data_termino_revisao'] != "0000-00-00"){
			$dadosVeiculos['data_termino_revisao'] = $_POST['data_termino_revisao'];
			$dadosVeiculos['data_termino_revisao'] = explode("/",$dadosVeiculos['data_termino_revisao']);
			$dadosVeiculos['data_termino_revisao'] = array_reverse($dadosVeiculos['data_termino_revisao']);
			$dadosVeiculos['data_termino_revisao'] = implode("-",$dadosVeiculos['data_termino_revisao']);
		}else{
			$dadosVeiculos['data_termino_revisao'] = NULL;
		}
		
		if(!$_POST['ativo']){
		
			$dadosVeiculos['ativo'] = 0;
		
		}else{
		
			$dadosVeiculos['ativo'] = $_POST['ativo'];			}
			
			if($_POST['origem'] == "0"){
			
				$dadosVeiculos['origem'] = $_POST['origem_descricao'];
			
			}else{
			
				$dadosVeiculos['origem'] = $_POST['origem'];
			
			}
			
			if($_POST['consignado']){
			
				$dadosVeiculos['consignado'] = 1;
			
			}else{
			
				$dadosVeiculos['consignado'] = 0;
			
			}

			$mensagem = "";
			
			$cont = 1;
		
			if($dbVeiculos->edt($_POST['id'],$dadosVeiculos)){

				$idVeiculos = $_POST['id'];
				
				$arrCheckList['id_veiculo'] =  $idVeiculos;
				$arrCheckList['quitado_leasing'] = $_POST['quitado_leasing'];
				$arrCheckList['pf_pj'] = $_POST['pf_pj'];
				$arrCheckList['gnv'] = $_POST['gnv'];
				
				if($_POST['gnv'] == 0){
				
					$arrCheckList['doc_gnv'] = 3;
				
				}else{
				
					$arrCheckList['doc_gnv'] = $_POST['doc_gnv'];
				
				}
				
				$dbCheckList = new Application_Model_DbTable_CheckList();
				
				$dbCheckList->edt($idVeiculos,$arrCheckList);
				
				foreach($_FILES as $chave=>$valores){
				
					if($chave == "foto"){
				
						foreach($_FILES["foto"]["error"] as $key => $error){
						
							if($_FILES['foto']['size'][$key] <= 200000){
							
								if($_FILES['foto']['name'][$key] != ""){
							
									if(!file_exists(("fotos_veiculos/".$dadosVeiculos['id_empresa']))){
								
										$extensao = strtolower(end(explode(".",$_FILES['foto']['name'][$key])));
										mkdir("fotos_veiculos/".$dadosVeiculos['id_empresa']);
										$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Ymdhis").$key.".".$extensao;
									
										
								
									
									
									}else{
									
										$extensao = strtolower(end(explode(".",$_FILES['foto']['name'][$key])));
										$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Ymdhis").$key.".".$extensao;
									
									}
									
									if($_FILES['foto']['tmp_name'][$key] != ""){
									
										$copied = copy($_FILES['foto']['tmp_name'][$key], $novoNome);
									
									}
									
									if($copied){
									
										$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
										
										$dadosFotos['id_veiculo'] = $idVeiculos;
										$dadosFotos['path'] = stripslashes($novoNome);
										
										if($chave == $capa){
										
											$dadosFotos['capa'] = 1;
										
										}else{
										
											$dadosFotos['capa'] = 0;
										
										}
									
										if(!$dbFotosVeiculos->add($dadosFotos)){
										
											$mensagem .= "Erro ao realizar o upload de fotos.<br>";

										}
									
									}
								
								}
								
								if($cont == 5){
									
									break;
								
								}
								
								$cont++;
							
							}
						
						}
					
					}
				
				}
				
				//CAPA
				$arrFotosVeiculo = $dbFotosVeiculos->getFotosVeiculoSelecionado($idVeiculos) ?: array();
						
				foreach($arrFotosVeiculo as $fotosVeiculos){
						
					if($_POST['capa'] == $fotosVeiculos['id']){
								
						$capa['capa'] = 1;
						$dbFotosVeiculos->edt($fotosVeiculos['id'],$capa);
							
					}else{
							
						$capa['capa'] = 0;
						$dbFotosVeiculos->edt($fotosVeiculos['id'],$capa);
							
					}
						
				}
			
				//PEGA OPCIONAIS
				$dbOpcionaisVeiculos->del($idVeiculos);
				
				foreach($_POST as $chave=>$post){
				
					$opcional = explode("_",$chave);

					if($opcional[0] == "opcional"){
				
						$opcionais['id_veiculo'] = $idVeiculos;
						$opcionais['id_opcional'] = $opcional[1];
					
						if(!$dbOpcionaisVeiculos->add($opcionais)){
					
							$mensagem .= "Erro ao adicionar opcionais.<br>";
						
						}
					
					}
				
				}
			
				//PEGA ANEXOS
				foreach($_FILES as $chave=>$valor){
					
					$anexos = explode("_",$chave);

					if($anexos[0] == "anexo"){
				
						if($_FILES[$chave]['name']){
							
							if(!file_exists(("anexos_veiculos/".$dadosVeiculos['id_empresa']))){
						
								mkdir("anexos_veiculos/".$dadosVeiculos['id_empresa']);
								$novoNome = "anexos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Y-m-d h:i:s")."_".$_FILES[$chave]['name'];
						
							}else{
						
								$novoNome = "anexos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Y-m-d h:i:s")."_".$_FILES[$chave]['name'];
						
							}
							
							$copied = copy($_FILES[$chave]['tmp_name'], $novoNome);
			
							if($copied){
							
								$dbAnexosVeiculos = new Application_Model_DbTable_AnexosVeiculos();
							
								$dadosAnexos['id_veiculo'] = $idVeiculos;
								$dadosAnexos['path'] = $novoNome;
								$dadosAnexos['descricao'] = $_POST['descricaoanexo_'.$anexos[1]];
							
								if(!$dbAnexosVeiculos->add($dadosAnexos)){
								
									$mensagem .= "Erro ao realizar o upload de anexos.<br>";
								
							
								}
							
							}
							
						}
						
					}
			
				}
				
				foreach($_POST as $chave=>$valor){
				
					$despesas = explode("_",$chave);
					
					if($despesas[1] <= 0){

						if($despesas[0] == "Ddata"){
						
							$dadosDespesas[$despesas[1]]['data'] = $valor;
						
						}
					
						if($despesas[0] == "Ddescricao"){
						
							$dadosDespesas[$despesas[1]]['despesa'] = $valor;
						
						}
						
						if($despesas[0] == "Dfornecedor"){
						
							$dadosDespesas[$despesas[1]]['id_fornecedor'] = $valor;
						
						}
						
						if($despesas[0] == "Dvalor"){
						
							$dadosDespesas[$despesas[1]]['valor'] = $valor;
						
						}
						
						if($despesas[0] == "Dgarantia"){
						
							$dadosDespesas[$despesas[1]]['dias_garantia'] = $valor;
						
						}
						
						if($despesas[0] == "Dnf"){
						
							$dadosDespesas[$despesas[1]]['nf'] = $valor;
						
						}
					
					}elseif($despesas[1] > 0){

						if($despesas[0] == "Ddata"){
						
							$dadosDespesasEdt[$despesas[1]]['data'] = $valor;
						
						}
					
						if($despesas[0] == "Ddescricao"){
						
							$dadosDespesasEdt[$despesas[1]]['despesa'] = $valor;
							$dadosDespesasEdt[$despesas[1]]['id'] = $despesas[1];
						
						}
						
						if($despesas[0] == "Dfornecedor"){
						
							$dadosDespesasEdt[$despesas[1]]['id_fornecedor'] = $valor;
							$dadosDespesasEdt[$despesas[1]]['id'] = $despesas[1];
						
						}
						
						if($despesas[0] == "Dvalor"){
						
							$dadosDespesasEdt[$despesas[1]]['valor'] = $valor;
						
						}
						
						if($despesas[0] == "Dgarantia"){
						
							$dadosDespesasEdt[$despesas[1]]['dias_garantia'] = $valor;
						
						}
						
						if($despesas[0] == "Dnf"){
						
							$dadosDespesasEdt[$despesas[1]]['nf'] = $valor;
						
						}
					
					}
					
				}
				
				if($dadosDespesas){
				
					foreach($dadosDespesas as $despesas){
					
						$data = explode("/",$despesas['data']);
						
						$despesas['data'] = $data[2]."-".$data[1]."-".$data[0];
						
						$despesas['id_veiculo'] = $idVeiculos;
						
						$despesas['valor'] = str_replace(".","",$despesas['valor']);
						$despesas['valor'] = str_replace(",",".",$despesas['valor']);	
					
						if(!$dbDespesasVeiculos->add($despesas)){
							
							$mensagem .= "Erro ao adicionar despesas.<br>";
							
						}
					
					}
				
				}
				
				if($dadosDespesasEdt){
				
					foreach($dadosDespesasEdt as $despesas){
					
						$data = explode("/",$despesas['data']);
						
						$despesas['data'] = $data[2]."-".$data[1]."-".$data[0];
						
						$despesas['id_veiculo'] = $idVeiculos;
						
						$despesas['valor'] = str_replace(".","",$despesas['valor']);
						$despesas['valor'] = str_replace(",",".",$despesas['valor']);
						
						$dbDespesasVeiculos->edt($despesas['id'], $despesas);
						
					}
				
				}
				
				//PEGA PENDENCIAS
				foreach($_POST as $chave=>$valor){
				
					$pendencias = explode("_",$chave);
					
					if($pendencias[1] <= 0){

						if($pendencias[0] == "Pdata"){
						
							$dadosPendencias[$pendencias[1]]['data'] = $valor;
						
						}
					
						if($pendencias[0] == "Pdescricao"){
						
							$dadosPendencias[$pendencias[1]]['descricao'] = $valor;
						
						}
					
					}elseif($pendencias[1] > 0){

						if($pendencias[0] == "Pdata"){
						
							$dadosPendenciasEdt[$pendencias[1]]['data'] = $valor;
						
						}
					
						if($pendencias[0] == "Pdescricao"){
						
							$dadosPendenciasEdt[$pendencias[1]]['descricao'] = $valor;
							$dadosPendenciasEdt[$pendencias[1]]['id'] = $pendencias[1];
						
						}
					
					}
					
				}
				
				if($dadosPendencias){
				
					foreach($dadosPendencias as $pendencias){
					
						$data = explode("/",$pendencias['data']);
						
						$pendencias['data'] = $data[2]."-".$data[1]."-".$data[0];
						
						$pendencias['id_veiculo'] = $idVeiculos;
					
						if(!$dbPendencias->add($pendencias)){
							
							$mensagem .= "Erro ao adicionar pend&ecirc;ncia.<br>";
							
						}
					
					}
				
				}
				
				if($dadosPendenciasEdt){
				
					foreach($dadosPendenciasEdt as $pendencias){
					
						$data = explode("/",$pendencias['data']);
						
						$pendencias['data'] = $data[2]."-".$data[1]."-".$data[0];
						
						$pendencias['id_veiculo'] = $idVeiculos;
						
						$dbPendencias->edt($pendencias['id'], $pendencias);
						
					}
				
				}
				
				$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
			
				if($arrEmpresa[0]['login_icarros'] && $arrEmpresa[0]['senha_icarros']){
				
					$arrVeiculos = $dbVeiculos->getVeiculoEstoque($idVeiculos);
				
					if($this->edtIcarrosSistema($arrVeiculos) != "OK"){
						
						$mensagem .= "Erro ao editar o ve&iacute;culo no Icarros, por favor tente novamente.<br>";
					
					}
				
				}
				
				if($mensagem == ""){
				
					$this->view->mensagem = "Altera&ccedil;&otilde;es efetuadas com sucesso!";
				
				}else{
				
					$this->view->mensagem = $mensagem;
				
				}
				
			}
		
		}
		
		$this->view->marcas = $dbVeiculos->getMarcasDistintas();
	
		$arrVeiculo = $dbVeiculos->getVeiculoSelecionadoCompleto($this->_getParam('id'));
		$id= $arrVeiculo[0]['id_modelo'];
		$arrFotosVeiculo = $dbFotosVeiculos->getFotosVeiculoSelecionado($this->_getParam('id'));
		$arrOpcionaisVeiculos = $dbOpcionaisVeiculos->getOpcionais() ?: array();
		$arrOpcionaisVeiculo = $dbOpcionaisVeiculos->getOpcionaisVeiculoSelecionado($this->_getParam('id'));
		$arrAnexos = $dbAnexosVeiculos->getAnexo($this->_getParam('id'));
		$arrDespesasVeiculos = $dbDespesasVeiculos->getDespesas($this->_getParam('id'));
		$arrFornecedores = $dbFornecedores->getFornecedoresPorEmpresa($_SESSION['sessionUser']['id_empresa']) ?: array();
		$arrPendencias = $dbPendencias->getPendencias($this->_getParam('id'));
		$arrModelo = $dbModelo->fetchAll("id = ".$id);
		
		
		$arrModelo[0]['preco'] = number_format($arrModelo[0]['preco'],2,',','.');
		$this->view->fipe = $arrModelo[0];
		
		// Valida data_termino_revisao antes de processar
		if(!empty($arrVeiculo[0]['data_termino_revisao']) && $arrVeiculo[0]['data_termino_revisao'] != "0000-00-00"){
			$arrVeiculo[0]['data_termino_revisao'] = explode("-",$arrVeiculo[0]['data_termino_revisao']);
			$arrVeiculo[0]['data_termino_revisao'] = array_reverse($arrVeiculo[0]['data_termino_revisao']);
			$arrVeiculo[0]['data_termino_revisao'] = implode("/",$arrVeiculo[0]['data_termino_revisao']);
		}else{
			$arrVeiculo[0]['data_termino_revisao'] = "";
		}
		
		$this->view->veiculo = $arrVeiculo[0];
		$this->view->opcionais = $arrOpcionaisVeiculos;
		$this->view->opcionaisVeiculo = $arrOpcionaisVeiculo;
		$this->view->fotos = $arrFotosVeiculo;
		$this->view->anexos = $arrAnexos;
		$this->view->despesas = $arrDespesasVeiculos;
		$this->view->fornecedores = $arrFornecedores;
		$this->view->pendencias = $arrPendencias;
		
		if($this->_getParam('termino') == 1){
		
			$this->view->termino = 1;
		
		}else{
		
			$this->view->termino = 0;
		
		}
	
	}
	
	
	*/
	



	public function edtAction(){
	
		$this->validaAcesso('gerenciar_estoque');
	
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbAnexosVeiculos = new Application_Model_DbTable_AnexosVeiculos();
		$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
		$dbFornecedores = new Application_Model_DbTable_Fornecedores();
		$dbPendencias = new Application_Model_DbTable_PendenciasVeiculos();
		$dbModelo = new Application_Model_DbTable_Modelos();
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$this->view->ativaPendencia = $this->_getParam('p');

		$idVeiculos = "";

		if($this->getRequest()->isPost()){
		
			if(!$_POST['id_empresa']){
				$_POST['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			}
		
			$dadosVeiculos['id_modelo'] = $_POST['id_modelo'];
			$dadosVeiculos['descricao_site'] = $_POST['descricao_site'];
			$dadosVeiculos['placa'] = $_POST['placa'];
			$dadosVeiculos['renavam'] = $_POST['renavam'];
			$dadosVeiculos['novo_usado'] = $_POST['novo_usado'];
			$dadosVeiculos['ano_fabricacao'] = $_POST['ano_fabricacao'];
			$dadosVeiculos['cor'] = $_POST['cor'];
			$dadosVeiculos['km'] = $_POST['km'];
			$dadosVeiculos['combustivel'] = $_POST['combustivel'];
			$dadosVeiculos['chassi'] = $_POST['chassi'];
			$dadosVeiculos['motor'] = $_POST['motor'];
			$dadosVeiculos['app'] = 0;
			$dadosVeiculos['loja'] = $_POST['loja'];
			
			if($_POST['valor_aquisicao']){
			
				$dadosVeiculos['valor_aquisicao'] = $_POST['valor_aquisicao'];
				$dadosVeiculos['valor_aquisicao'] = str_replace(".","",$dadosVeiculos['valor_aquisicao']);
				$dadosVeiculos['valor_aquisicao'] = str_replace(",",".",$dadosVeiculos['valor_aquisicao']);

			}
			
			$dadosVeiculos['data_aquisicao'] = implode("-",array_reverse(explode("/",$_POST['data_aquisicao'])));
			//$dadosVeiculos['data_inicio_preparacao'] = implode("-",array_reverse(explode("/",$_POST['data_inicio_preparacao'])));
			$dadosVeiculos['valor_venda'] = $_POST['valor_venda'];
			$dadosVeiculos['valor_venda'] = str_replace(".","",$dadosVeiculos['valor_venda']);
			$dadosVeiculos['valor_venda'] = str_replace(",",".",$dadosVeiculos['valor_venda']);	
			$dadosVeiculos['id_empresa'] = $_POST['id_empresa'];
			//$dadosVeiculos['temp_troca'] = $_POST['temp_troca'];
			$dadosVeiculos['obs_interna'] = $_POST['obs_interna'];
			$dadosVeiculos['video'] = $_POST['video'];
			$dadosVeiculos['obs_site'] = $_POST['obs_site'];
			$dadosVeiculos['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dadosVeiculos['hora_alteracao'] = @date("Y-m-d H:i:s");
			$dadosVeiculos['exibir_valor_site'] = $_POST['exibir_valor_site'];
			$dadosVeiculos['exibir_km'] = $_POST['exibir_km_1']+$_POST['exibir_km_2'];
			$dadosVeiculos['exibir_site_estoque'] = $_POST['exibir_site_estoque_1']+$_POST['exibir_site_estoque_2'];
			
			// Valida data_termino_revisao antes de processar
			if(!empty($_POST['data_termino_revisao']) && $_POST['data_termino_revisao'] != "0000-00-00"){
				$dadosVeiculos['data_termino_revisao'] = $_POST['data_termino_revisao'];
				$dadosVeiculos['data_termino_revisao'] = explode("/",$dadosVeiculos['data_termino_revisao']);
				$dadosVeiculos['data_termino_revisao'] = array_reverse($dadosVeiculos['data_termino_revisao']);
				$dadosVeiculos['data_termino_revisao'] = implode("-",$dadosVeiculos['data_termino_revisao']);
			}else{
				$dadosVeiculos['data_termino_revisao'] = NULL;
			}
            
            if ($dadosVeiculos['data_termino_revisao'] == "0000-00-00"){ 
                $dadosVeiculos['data_termino_revisao'] = NULL; 
            }

			if($_POST['data_inicio_preparacao']){

				$arrVeiculoTemp = current($dbVeiculos->getVeiculoSelecionadoCompleto($_POST['id']));

				$dadosVeiculos['data_inicio_preparacao'] = implode("-",array_reverse(explode("/",$_POST['data_inicio_preparacao'])));

				$dbPeriodoPreparacao = new Application_Model_DbTable_PeriodoPreparacao();
				$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
				$arrPeriodos = current($dbPeriodoPreparacao->getPeriodoPreparacao($arr));

				$servico = "mecanico";
				$dadosVeiculos[$servico.'_date_entrada'] = $dadosVeiculos['data_inicio_preparacao'];
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "funilaria";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "martelinho";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "eletrica";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "tapecaria";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "lavacar";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "volante";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "calotas";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "pneus";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "chaveiro";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "pincelar";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "outros";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				//$servico = "concluido";
				//$dadosVeiculos[$servico.'_date_concluido'] = $dataFimTemp;

			}
			
			if(!$_POST['ativo']){
			
				$dadosVeiculos['ativo'] = 0;
			
			}else{
			
				$dadosVeiculos['ativo'] = $_POST['ativo'];
				
			}
			
			if($_POST['origem'] == "0"){
			
				$dadosVeiculos['origem'] = $_POST['origem_descricao'];
			
			}else{
			
				$dadosVeiculos['origem'] = $_POST['origem'];
			
			}
			
			if($_POST['consignado'] == 0){
			
				$dadosVeiculos['consignado'] = 0;
			
			}elseif($_POST['consignado'] == 1){
			
				$dadosVeiculos['consignado'] = 1;
			
			}elseif($_POST['consignado'] == 2){
			
				$dadosVeiculos['consignado'] = 2;
			
			}elseif($_POST['consignado'] == 3){
			
				$dadosVeiculos['consignado'] = 3;
				
			}

			$mensagem = "";
		
			if($dbVeiculos->edt($_POST['id'],$dadosVeiculos)){

				$idVeiculos = $_POST['id'];
				
				if($_POST['capa']){
				
					$arrCapa = explode("\\",$_POST['capa']);
					
					if($arrCapa[2]){
						
						$strCapa = $arrCapa[2];
					
					}elseif($arrCapa[0]){
						
						$strCapa = $arrCapa[0];
						$idCapa = $arrCapa[0];
					
					}
				
				}
			
				$cont = 1;
				
				$arrCheckList['id_veiculo'] =  $idVeiculos;
				$arrCheckList['quitado_leasing'] = $_POST['quitado_leasing'];
				$arrCheckList['pf_pj'] = $_POST['pf_pj'];
				$arrCheckList['gnv'] = $_POST['gnv'];
				
				if($_POST['gnv'] == 0){
				
					$arrCheckList['doc_gnv'] = 3;
				
				}else{
				
					$arrCheckList['doc_gnv'] = $_POST['doc_gnv'];
				
				}
				
				$dbCheckList = new Application_Model_DbTable_CheckList();
				
				$dbCheckList->edt($idVeiculos,$arrCheckList);
				
				foreach($_FILES as $chave=>$valores){
				
					if($chave == "foto"){
				
						foreach($_FILES["foto"]["error"] as $key => $error){
							
							if($_FILES['foto']['name'][$key] != ""){

								if(!file_exists(("fotos_veiculos/".$dadosVeiculos['id_empresa']))){
								
									$extensao = strtolower(end(explode(".",$_FILES['foto']['name'][$key])));
									mkdir("fotos_veiculos/".$dadosVeiculos['id_empresa']);
									chmod("fotos_veiculos/".$dadosVeiculos['id_empresa'], 0755);
									$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$dadosVeiculos['descricao_site']))."-".$dadosVeiculos['ano_fabricacao']."-".@date("his").$key.".".$extensao;
									//$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Ymdhis").$key.".".$extensao;
									
									
								}else{
									
									$extensao = strtolower(end(explode(".",$_FILES['foto']['name'][$key])));
									$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$dadosVeiculos['descricao_site']))."-".$dadosVeiculos['ano_fabricacao']."-".@date("his").$key.".".$extensao;
									//$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Ymdhis").$key.".".$extensao;
									
								}
									
								if($_FILES['foto']['tmp_name'][$key] != ""){
									
									/////////////////REDIMENCIONA IMAGEM///////////////////////
									# Caminho da imagem a ser redimensionada: 
									$input_image = $_FILES['foto']['tmp_name'][$key];
										 
									// Pega o tamanho original da imagem e armazena em um Array:
									$size = getimagesize( $input_image );
										 
									// Configura a nova largura da imagem:
									$thumb_width = "780";
										 
									// Calcula a altura da nova imagem para manter a proporção na tela: 
									$thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );
										 
									// Cria a imagem com as cores reais originais na memória.
									$thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );
										 
									// Criará uma nova imagem do arquivo.
									$src_img = ImageCreateFromJPEG( $input_image );
										 
									// Criará a imagem redimensionada:
									ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );
										 
									// Informe aqui o novo nome da imagem e a localização:
									$copied = ImageJPEG( $thumbnail, $novoNome);
									
									chmod($novoNome, 0755);
									
									// Limpa da memoria a imagem criada temporáriamente: 
									ImageDestroy( $thumbnail );
									
									
									
									//$copied = copy($_FILES['foto']['tmp_name'][$key], $novoNome);
									
								}
									
								if($copied){
									
									
									
									$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
								
									$dadosFotos['id_veiculo'] = $idVeiculos;
									$novoNome = str_replace('\\', '', $novoNome);  // Modifica a variável original
									$dadosFotos['path'] = $novoNome;
									// var_export($novoNome); 
									
									$idFoto = $dbFotosVeiculos->add($dadosFotos);
									
									if($_POST['capa']){
									
										if($_FILES['foto']['name'][$key] == $strCapa){
											
											$idCapa = $idFoto;
										
										}

									}
									
									if($_POST['capa_multiplo']){
				
										if($cont == $_POST['capa_multiplo']){
										
											$idCapa = $idFoto;
										
										}
				
									}
									
								}
								
							}
					
							if($cont == 9){
							
								break;
								
							}
							
							$cont++;

						}
					
					}
				
				}
				
				//CAPA
				$arrFotosVeiculo = $dbFotosVeiculos->getFotosVeiculoSelecionado($idVeiculos) ?: array();
					
				foreach($arrFotosVeiculo as $fotosVeiculos){
						
					if($idCapa == $fotosVeiculos['id']){
	
						$capa['capa'] = 1;
						$dbFotosVeiculos->edt($fotosVeiculos['id'],$capa);
							
					}else{
							
						$capa['capa'] = 0;
						$dbFotosVeiculos->edt($fotosVeiculos['id'],$capa);
							
					}
						
				}
			
				//PEGA OPCIONAIS
				$dbOpcionaisVeiculos->del($idVeiculos);
				
				foreach($_POST as $chave=>$post){
				
					$opcional = explode("_",$chave);

					if($opcional[0] == "opcional"){
				
						$opcionais['id_veiculo'] = $idVeiculos;
						$opcionais['id_opcional'] = $opcional[1];
					
						if(!$dbOpcionaisVeiculos->add($opcionais)){
					
							$mensagem .= "Erro ao adicionar opcionais.<br>";
						
						}
					
					}
				
				}
			
				//PEGA ANEXOS
				foreach($_FILES as $chave=>$valor){
					
					$anexos = explode("_",$chave);

					if($anexos[0] == "anexo"){
				
						if($_FILES[$chave]['name']){
							
							if(!file_exists(("anexos_veiculos/".$dadosVeiculos['id_empresa']))){
						
								mkdir("anexos_veiculos/".$dadosVeiculos['id_empresa']);
								chmod("anexos_veiculos/".$dadosVeiculos['id_empresa'], 0755);
								$novoNome = "anexos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Y-m-d h:i:s")."_".$_FILES[$chave]['name'];

							}else{
						
								$novoNome = "anexos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Y-m-d h:i:s")."_".$_FILES[$chave]['name'];
						
							}
							
							$copied = copy($_FILES[$chave]['tmp_name'], $novoNome);
			
							if($copied){

								chmod($novoNome, 0755);
								
								$dbAnexosVeiculos = new Application_Model_DbTable_AnexosVeiculos();
							
								$dadosAnexos['id_veiculo'] = $idVeiculos;
								$dadosAnexos['path'] = $novoNome;
								$dadosAnexos['descricao'] = $_POST['descricaoanexo_'.$anexos[1]];
							
								if(!$dbAnexosVeiculos->add($dadosAnexos)){
								
									$mensagem .= "Erro ao realizar o upload de anexos.<br>";
								
							
								}
							
							}
							
						}
						
					}
			
				}
				
				foreach($_POST as $chave=>$valor){
				
					$despesas = explode("_",$chave);
					
					if($despesas[1] <= 0){

						if($despesas[0] == "Ddata"){
						
							$dadosDespesas[$despesas[1]]['data'] = $valor;
						
						}
					
						if($despesas[0] == "Ddescricao"){
						
							$dadosDespesas[$despesas[1]]['despesa'] = $valor;
						
						}
						
						if($despesas[0] == "Dfornecedor"){
						
							$dadosDespesas[$despesas[1]]['id_fornecedor'] = $valor;
						
						}
						
						if($despesas[0] == "Dvalor"){
						
							$dadosDespesas[$despesas[1]]['valor'] = $valor;
						
						}
						
						if($despesas[0] == "Dgarantia"){
						
							$dadosDespesas[$despesas[1]]['dias_garantia'] = $valor;
						
						}
						
						if($despesas[0] == "Dnf"){
						
							$dadosDespesas[$despesas[1]]['nf'] = $valor;
						
						}
					
					}elseif($despesas[1] > 0){

						if($despesas[0] == "Ddata"){
						
							$dadosDespesasEdt[$despesas[1]]['data'] = $valor;
						
						}
					
						if($despesas[0] == "Ddescricao"){
						
							$dadosDespesasEdt[$despesas[1]]['despesa'] = $valor;
							$dadosDespesasEdt[$despesas[1]]['id'] = $despesas[1];
						
						}
						
						if($despesas[0] == "Dfornecedor"){
						
							$dadosDespesasEdt[$despesas[1]]['id_fornecedor'] = $valor;
							$dadosDespesasEdt[$despesas[1]]['id'] = $despesas[1];
						
						}
						
						if($despesas[0] == "Dvalor"){
						
							$dadosDespesasEdt[$despesas[1]]['valor'] = $valor;
						
						}
						
						if($despesas[0] == "Dgarantia"){
						
							$dadosDespesasEdt[$despesas[1]]['dias_garantia'] = $valor;
						
						}
						
						if($despesas[0] == "Dnf"){
						
							$dadosDespesasEdt[$despesas[1]]['nf'] = $valor;
						
						}
					
					}
					
				}
				
				if($dadosDespesas){
				
					foreach($dadosDespesas as $despesas){
					
						$data = explode("/",$despesas['data']);
						
						$despesas['data'] = $data[2]."-".$data[1]."-".$data[0];
						
						$despesas['id_veiculo'] = $idVeiculos;
						
						$despesas['valor'] = str_replace(".","",$despesas['valor']);
						$despesas['valor'] = str_replace(",",".",$despesas['valor']);	
					
						if(!$dbDespesasVeiculos->add($despesas)){
							
							$mensagem .= "Erro ao adicionar despesas.<br>";
							
						}
					
					}
				
				}
				
				if($dadosDespesasEdt){
				
					foreach($dadosDespesasEdt as $despesas){
					
						$data = explode("/",$despesas['data']);
						
						$despesas['data'] = $data[2]."-".$data[1]."-".$data[0];
						
						$despesas['id_veiculo'] = $idVeiculos;
						
						$despesas['valor'] = str_replace(".","",$despesas['valor']);
						$despesas['valor'] = str_replace(",",".",$despesas['valor']);
						
						$dbDespesasVeiculos->edt($despesas['id'], $despesas);
						
					}
				
				}
				
				//PEGA PENDENCIAS
				foreach($_POST as $chave=>$valor){
				
					$pendencias = explode("_",$chave);
					
					if($pendencias[1] <= 0){

						if($pendencias[0] == "Pdata"){
						
							$dadosPendencias[$pendencias[1]]['data'] = $valor;
						
						}
					
						if($pendencias[0] == "Pdescricao"){
						
							$dadosPendencias[$pendencias[1]]['descricao'] = $valor;
						
						}
					
					}elseif($pendencias[1] > 0){

						if($pendencias[0] == "Pdata"){
						
							$dadosPendenciasEdt[$pendencias[1]]['data'] = $valor;
						
						}
					
						if($pendencias[0] == "Pdescricao"){
						
							$dadosPendenciasEdt[$pendencias[1]]['descricao'] = $valor;
							$dadosPendenciasEdt[$pendencias[1]]['id'] = $pendencias[1];
						
						}
					
					}
					
				}
				
				if($dadosPendencias){
				
					foreach($dadosPendencias as $pendencias){
					
						$data = explode("/",$pendencias['data']);
						
						$pendencias['data'] = $data[2]."-".$data[1]."-".$data[0];
						
						$pendencias['id_veiculo'] = $idVeiculos;
					
						if(!$dbPendencias->add($pendencias)){
							
							$mensagem .= "Erro ao adicionar pend&ecirc;ncia.<br>";
							
						}
					
					}
				
				}
				
				if($dadosPendenciasEdt){
				
					foreach($dadosPendenciasEdt as $pendencias){
					
						$data = explode("/",$pendencias['data']);
						
						$pendencias['data'] = $data[2]."-".$data[1]."-".$data[0];
						
						$pendencias['id_veiculo'] = $idVeiculos;
						
						$dbPendencias->edt($pendencias['id'], $pendencias);
						
					}
				
				}
				

				$arrVeiculos = $dbVeiculos->getVeiculoEstoque($idVeiculos);
			
				if($arrVeiculos[0]['icarros'] == 1 || $arrVeiculos[0]['icarros'] == 2){

					if($this->edtIcarrosSistema($arrVeiculos) != "OK"){
				
						if($arrVeiculos[0]['id']){
				
							$dados['icarros'] = 2;
						
							$dbVeiculos->edt($arrVeiculos[0]['id'], $dados);
						
						}
						
					}
				
				}
				/*
				exec("php ../application/views/scripts/icarros/edt-icarros-background.phtml > ../application/views/scripts/icarros/logIcarrosBackground.txt &");

				
				$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
				
				if($arrEmpresa[0]['login_webmotors'] && $arrEmpresa[0]['senha_webmotors']){
					
					$suces = true;
					
					$arrEstoqueWeb = $this->getEstoqueWebmotors($arrEmpresa[0]['cnpj'], $arrEmpresa[0]['login_webmotors'], $arrEmpresa[0]['senha_webmotors']);

					foreach($arrEstoqueWeb as $estoqueWeb){
				
						if(strtolower($estoqueWeb['placa']) == strtolower(str_replace("-","",$arrVeiculos[0]['placa']))){
					
							$arrVeiculos[0]['codigo_modalidade'] = $estoqueWeb['codigo_modalidade'];
							$arrVeiculos[0]['codigo_marca'] = $estoqueWeb['codigo_marca'];
							$arrVeiculos[0]['codigo_modelo'] = $estoqueWeb['codigo_modelo'];
							$arrVeiculos[0]['codigo_versao'] = $estoqueWeb['codigo_versao'];
	
							$arrVeiculoCompleto = $this->alteraValoresWebmotors($arrEmpresa[0]['cnpj'], $arrEmpresa[0]['login_webmotors'], $arrEmpresa[0]['senha_webmotors'], $estoqueWeb['codigo_anuncio'], $arrVeiculos);
				
							if($arrVeiculoCompleto != 500){

								$mensagem = "<br/>Houve um erro inesperado e o veículo não foi alterado na Webmotors.";
								//$mensagem = $arrVeiculoCompleto;
								
							}
					
						}
				
					}
					
				}
				*/
				
				if($mensagem == ""){
			
					$this->view->mensagem = "Altera&ccedil;&otilde;es efetuadas com sucesso!";
			
				}else{
			
					$this->view->mensagem = $mensagem;
		
				}
				
				
			}
		
		}
		
		$this->view->marcas = $dbVeiculos->getMarcasDistintas();
	
		if($idVeiculos == ""){
			
			$idVeiculos = $this->_getParam('id');
			
			
		
		}

		// Valida se ID do veículo foi fornecido
		if(empty($idVeiculos)){
			$this->view->erro = "ID do veículo não fornecido";
			return;
		}

		$arrVeiculo = $dbVeiculos->getVeiculoSelecionadoCompleto($idVeiculos);
		
		// Valida se veículo foi encontrado
		if(empty($arrVeiculo)){
			$this->view->erro = "Veículo não encontrado";
			return;
		}
		
		$id= $arrVeiculo[0]['id_modelo'];

	
		$arrFotosVeiculo = $dbFotosVeiculos->getFotosVeiculoSelecionado($idVeiculos) ?: array();
		$arrOpcionaisVeiculos = $dbOpcionaisVeiculos->getOpcionais() ?: array();
		$arrOpcionaisVeiculo = $dbOpcionaisVeiculos->getOpcionaisVeiculoSelecionado($idVeiculos) ?: array();
		$arrAnexos = $dbAnexosVeiculos->getAnexo($idVeiculos) ?: array();
		$arrDespesasVeiculos = $dbDespesasVeiculos->getDespesas($idVeiculos) ?: array();
		$arrFornecedores = $dbFornecedores->getFornecedoresPorEmpresa($_SESSION['sessionUser']['id_empresa']) ?: array();
		$arrPendencias = $dbPendencias->getPendencias($idVeiculos) ?: array();
		// $arrModelo = $dbModelo->fetchAll("id = ".$id);
        $arrModelo = $id ? $dbModelo->fetchAll("id = ".$id) : [];

		// if(strpos($arrModelo[0]['modelo'], "Gasolina") !== false) {
		// 	$arrModelo[0]['codigo'] = $arrModelo[0]['ano_modelo'].'-1';
		// }elseif(strpos($arrModelo[0]['modelo'], "lcool") !== false) {
		// 	$arrModelo[0]['codigo'] = $arrModelo[0]['ano_modelo'].'-2';
		// }elseif(strpos($arrModelo[0]['modelo'], "Diesel") !== false) {
		// 	$arrModelo[0]['codigo'] = $arrModelo[0]['ano_modelo'].'-3';
		// }else{
		// 	$arrModelo[0]['codigo'] = $arrModelo[0]['ano_modelo'].'-1';
		// }

		//var_export($arrModelo[0]);


		$arrModelo[0]['preco'] = number_format($arrModelo[0]['preco'],2,',','.');
		$this->view->fipe = $arrModelo[0];

		
		$arrVeiculo[0]['data_termino_revisao'] = explode("-",$arrVeiculo[0]['data_termino_revisao']);
		$arrVeiculo[0]['data_termino_revisao'] = array_reverse($arrVeiculo[0]['data_termino_revisao']);
		$arrVeiculo[0]['data_termino_revisao'] = implode("/",$arrVeiculo[0]['data_termino_revisao']);
		
		$this->view->veiculo = $arrVeiculo[0];
		$this->view->opcionais = $arrOpcionaisVeiculos;
		$this->view->opcionaisVeiculo = $arrOpcionaisVeiculo;
		$this->view->fotos = $arrFotosVeiculo;
		$this->view->anexos = $arrAnexos;
		$this->view->despesas = $arrDespesasVeiculos;
		$this->view->fornecedores = $arrFornecedores;
		$this->view->pendencias = $arrPendencias;
		
		if($this->_getParam('termino') == 1){
		
			$this->view->termino = 1;
		
		}else{
		
			$this->view->termino = 0;
		
		}
	
	}



	
	
	public function edtBKP02Action(){
	
		$this->validaAcesso('gerenciar_estoque');
	
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbAnexosVeiculos = new Application_Model_DbTable_AnexosVeiculos();
		$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
		$dbFornecedores = new Application_Model_DbTable_Fornecedores();
		$dbPendencias = new Application_Model_DbTable_PendenciasVeiculos();
		$dbModelo = new Application_Model_DbTable_Modelos();
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$this->view->ativaPendencia = $this->_getParam('p');

		$idVeiculos = "";

		if($this->getRequest()->isPost()){
		
			if(!$_POST['id_empresa']){
				$_POST['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			}
		
			$dadosVeiculos['id_modelo'] = $_POST['id_modelo'];
			$dadosVeiculos['descricao_site'] = $_POST['descricao_site'];
			$dadosVeiculos['placa'] = $_POST['placa'];
			$dadosVeiculos['renavam'] = $_POST['renavam'];
			$dadosVeiculos['novo_usado'] = $_POST['novo_usado'];
			$dadosVeiculos['ano_fabricacao'] = $_POST['ano_fabricacao'];
			$dadosVeiculos['cor'] = $_POST['cor'];
			$dadosVeiculos['km'] = $_POST['km'];
			$dadosVeiculos['combustivel'] = $_POST['combustivel'];
			$dadosVeiculos['chassi'] = $_POST['chassi'];
			$dadosVeiculos['motor'] = $_POST['motor'];
			$dadosVeiculos['app'] = 0;
			$dadosVeiculos['loja'] = $_POST['loja'];
			
			if($_POST['valor_aquisicao']){
			
				$dadosVeiculos['valor_aquisicao'] = $_POST['valor_aquisicao'];
				$dadosVeiculos['valor_aquisicao'] = str_replace(".","",$dadosVeiculos['valor_aquisicao']);
				$dadosVeiculos['valor_aquisicao'] = str_replace(",",".",$dadosVeiculos['valor_aquisicao']);

			}
			
			$dadosVeiculos['data_aquisicao'] = implode("-",array_reverse(explode("/",$_POST['data_aquisicao'])));
			//$dadosVeiculos['data_inicio_preparacao'] = implode("-",array_reverse(explode("/",$_POST['data_inicio_preparacao'])));
			$dadosVeiculos['valor_venda'] = $_POST['valor_venda'];
			$dadosVeiculos['valor_venda'] = str_replace(".","",$dadosVeiculos['valor_venda']);
			$dadosVeiculos['valor_venda'] = str_replace(",",".",$dadosVeiculos['valor_venda']);	
		$dadosVeiculos['id_empresa'] = $_POST['id_empresa'];
		//$dadosVeiculos['temp_troca'] = $_POST['temp_troca'];
		$dadosVeiculos['obs_interna'] = $_POST['obs_interna'];
		$dadosVeiculos['video'] = $_POST['video'];
		$dadosVeiculos['obs_site'] = $_POST['obs_site'];
		$dadosVeiculos['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
		$dadosVeiculos['hora_alteracao'] = @date("Y-m-d H:i:s");
		$dadosVeiculos['exibir_valor_site'] = $_POST['exibir_valor_site'];
		$dadosVeiculos['exibir_km'] = $_POST['exibir_km_1']+$_POST['exibir_km_2'];
		$dadosVeiculos['exibir_site_estoque'] = $_POST['exibir_site_estoque_1']+$_POST['exibir_site_estoque_2'];
		
		// Valida data_termino_revisao antes de processar
		if(!empty($_POST['data_termino_revisao'])){
			$dadosVeiculos['data_termino_revisao'] = $_POST['data_termino_revisao'];
			$dadosVeiculos['data_termino_revisao'] = explode("/",$dadosVeiculos['data_termino_revisao']);
			$dadosVeiculos['data_termino_revisao'] = array_reverse($dadosVeiculos['data_termino_revisao']);
			$dadosVeiculos['data_termino_revisao'] = implode("-",$dadosVeiculos['data_termino_revisao']);
		}else{
			$dadosVeiculos['data_termino_revisao'] = NULL;
		}
        if ($_POST['data_termino_revisao'] == "0000-00-00"){
            $dadosVeiculos['data_termino_revisao'] = NULL;
        }
        

		if($_POST['data_inicio_preparacao']){				
                $arrVeiculoTemp = current($dbVeiculos->getVeiculoSelecionadoCompleto($_POST['id']));

				$dadosVeiculos['data_inicio_preparacao'] = implode("-",array_reverse(explode("/",$_POST['data_inicio_preparacao'])));

				$dbPeriodoPreparacao = new Application_Model_DbTable_PeriodoPreparacao();
				$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
				$arrPeriodos = current($dbPeriodoPreparacao->getPeriodoPreparacao($arr));

				$servico = "mecanico";
				$dadosVeiculos[$servico.'_date_entrada'] = $dadosVeiculos['data_inicio_preparacao'];
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "funilaria";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "martelinho";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "eletrica";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "tapecaria";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "lavacar";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "volante";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "calotas";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "pneus";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "chaveiro";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "pincelar";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "outros";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]+$arrVeiculoTemp[$servico.'_dias_extras']), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				//$servico = "concluido";
				//$dadosVeiculos[$servico.'_date_concluido'] = $dataFimTemp;

			}
			
			if(!$_POST['ativo']){
			
				$dadosVeiculos['ativo'] = 0;
			
			}else{
			
				$dadosVeiculos['ativo'] = $_POST['ativo'];
				
			}
			
			if($_POST['origem'] == "0"){
			
				$dadosVeiculos['origem'] = $_POST['origem_descricao'];
			
			}else{
			
				$dadosVeiculos['origem'] = $_POST['origem'];
			
			}
			
			if($_POST['consignado'] == 0){
			
				$dadosVeiculos['consignado'] = 0;
			
			}elseif($_POST['consignado'] == 1){
			
				$dadosVeiculos['consignado'] = 1;
			
			}elseif($_POST['consignado'] == 2){
			
				$dadosVeiculos['consignado'] = 2;
			
			}elseif($_POST['consignado'] == 3){
			
				$dadosVeiculos['consignado'] = 3;
				
			}

			$mensagem = "";
		
			if($dbVeiculos->edt($_POST['id'],$dadosVeiculos)){

				$idVeiculos = $_POST['id'];
				
				if($_POST['capa']){
				
					$arrCapa = explode("\\",$_POST['capa']);
					
					if($arrCapa[2]){
						
						$strCapa = $arrCapa[2];
					
					}elseif($arrCapa[0]){
						
						$strCapa = $arrCapa[0];
						$idCapa = $arrCapa[0];
					
					}
				
				}
			
				$cont = 1;
				
				$arrCheckList['id_veiculo'] =  $idVeiculos;
				$arrCheckList['quitado_leasing'] = $_POST['quitado_leasing'];
				$arrCheckList['pf_pj'] = $_POST['pf_pj'];
				$arrCheckList['gnv'] = $_POST['gnv'];
				
				if($_POST['gnv'] == 0){
				
					$arrCheckList['doc_gnv'] = 3;
				
				}else{
				
					$arrCheckList['doc_gnv'] = $_POST['doc_gnv'];
				
				}
				
				$dbCheckList = new Application_Model_DbTable_CheckList();
				
				$dbCheckList->edt($idVeiculos,$arrCheckList);
				
				foreach($_FILES as $chave=>$valores){
				
					if($chave == "foto"){
				
						foreach($_FILES["foto"]["error"] as $key => $error){
							
							if($_FILES['foto']['name'][$key] != ""){

								if(!file_exists(("fotos_veiculos/".$dadosVeiculos['id_empresa']))){
								
									$extensao = strtolower(end(explode(".",$_FILES['foto']['name'][$key])));
									mkdir("fotos_veiculos/".$dadosVeiculos['id_empresa']);
									chmod("fotos_veiculos/".$dadosVeiculos['id_empresa'], 0755);
									$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$dadosVeiculos['descricao_site']))."-".$dadosVeiculos['ano_fabricacao']."-".@date("his").$key.".".$extensao;
									//$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Ymdhis").$key.".".$extensao;
									
									
								}else{
									
									$extensao = strtolower(end(explode(".",$_FILES['foto']['name'][$key])));
									$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$dadosVeiculos['descricao_site']))."-".$dadosVeiculos['ano_fabricacao']."-".@date("his").$key.".".$extensao;
									//$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Ymdhis").$key.".".$extensao;
									
								}
									
								if($_FILES['foto']['tmp_name'][$key] != ""){
									
									/////////////////REDIMENCIONA IMAGEM///////////////////////
									# Caminho da imagem a ser redimensionada: 
									$input_image = $_FILES['foto']['tmp_name'][$key];
										 
									// Pega o tamanho original da imagem e armazena em um Array:
									$size = getimagesize( $input_image );
										 
									// Configura a nova largura da imagem:
									$thumb_width = "780";
										 
									// Calcula a altura da nova imagem para manter a proporção na tela: 
									$thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );
										 
									// Cria a imagem com as cores reais originais na memória.
									$thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );
										 
									// Criará uma nova imagem do arquivo.
									$src_img = ImageCreateFromJPEG( $input_image );
										 
									// Criará a imagem redimensionada:
									ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );
										 
									// Informe aqui o novo nome da imagem e a localização:
									$copied = ImageJPEG( $thumbnail, $novoNome);
									
									chmod($novoNome, 0755);
									
									// Limpa da memoria a imagem criada temporáriamente: 
									ImageDestroy( $thumbnail );
									
									
									
									//$copied = copy($_FILES['foto']['tmp_name'][$key], $novoNome);
									
								}
									
								if($copied){
									
									
									
									$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
								
									$dadosFotos['id_veiculo'] = $idVeiculos;
									$dadosFotos['path'] = stripslashes($novoNome);
									
									//var_export($novoNome);
									
									$idFoto = $dbFotosVeiculos->add($dadosFotos);
									
									if($_POST['capa']){
									
										if($_FILES['foto']['name'][$key] == $strCapa){
											
											$idCapa = $idFoto;
										
										}

									}
									
									if($_POST['capa_multiplo']){
				
										if($cont == $_POST['capa_multiplo']){
										
											$idCapa = $idFoto;
										
										}
				
									}
									
								}
								
							}
					
							if($cont == 9){
							
								break;
								
							}
							
							$cont++;

						}
					
					}
				
				}
				
				//CAPA
				$arrFotosVeiculo = $dbFotosVeiculos->getFotosVeiculoSelecionado($idVeiculos) ?: array();
					
				foreach($arrFotosVeiculo as $fotosVeiculos){
						
					if($idCapa == $fotosVeiculos['id']){
	
						$capa['capa'] = 1;
						$dbFotosVeiculos->edt($fotosVeiculos['id'],$capa);
							
					}else{
							
						$capa['capa'] = 0;
						$dbFotosVeiculos->edt($fotosVeiculos['id'],$capa);
							
					}
						
				}
			
				//PEGA OPCIONAIS
				$dbOpcionaisVeiculos->del($idVeiculos);
				
				foreach($_POST as $chave=>$post){
				
					$opcional = explode("_",$chave);

					if($opcional[0] == "opcional"){
				
						$opcionais['id_veiculo'] = $idVeiculos;
						$opcionais['id_opcional'] = $opcional[1];
					
						if(!$dbOpcionaisVeiculos->add($opcionais)){
					
							$mensagem .= "Erro ao adicionar opcionais.<br>";
						
						}
					
					}
				
				}
			
				//PEGA ANEXOS
				foreach($_FILES as $chave=>$valor){
					
					$anexos = explode("_",$chave);

					if($anexos[0] == "anexo"){
				
						if($_FILES[$chave]['name']){
							
							if(!file_exists(("anexos_veiculos/".$dadosVeiculos['id_empresa']))){
						
								mkdir("anexos_veiculos/".$dadosVeiculos['id_empresa']);
								chmod("anexos_veiculos/".$dadosVeiculos['id_empresa'], 0755);
								$novoNome = "anexos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Y-m-d h:i:s")."_".$_FILES[$chave]['name'];

							}else{
						
								$novoNome = "anexos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Y-m-d h:i:s")."_".$_FILES[$chave]['name'];
						
							}
							
							$copied = copy($_FILES[$chave]['tmp_name'], $novoNome);
			
							if($copied){

								chmod($novoNome, 0755);
								
								$dbAnexosVeiculos = new Application_Model_DbTable_AnexosVeiculos();
							
								$dadosAnexos['id_veiculo'] = $idVeiculos;
								$dadosAnexos['path'] = $novoNome;
								$dadosAnexos['descricao'] = $_POST['descricaoanexo_'.$anexos[1]];
							
								if(!$dbAnexosVeiculos->add($dadosAnexos)){
								
									$mensagem .= "Erro ao realizar o upload de anexos.<br>";
								
							
								}
							
							}
							
						}
						
					}
			
				}
				
				foreach($_POST as $chave=>$valor){
				
					$despesas = explode("_",$chave);
					
					if($despesas[1] <= 0){

						if($despesas[0] == "Ddata"){
						
							$dadosDespesas[$despesas[1]]['data'] = $valor;
						
						}
					
						if($despesas[0] == "Ddescricao"){
						
							$dadosDespesas[$despesas[1]]['despesa'] = $valor;
						
						}
						
						if($despesas[0] == "Dfornecedor"){
						
							$dadosDespesas[$despesas[1]]['id_fornecedor'] = $valor;
						
						}
						
						if($despesas[0] == "Dvalor"){
						
							$dadosDespesas[$despesas[1]]['valor'] = $valor;
						
						}
						
						if($despesas[0] == "Dgarantia"){
						
							$dadosDespesas[$despesas[1]]['dias_garantia'] = $valor;
						
						}
						
						if($despesas[0] == "Dnf"){
						
							$dadosDespesas[$despesas[1]]['nf'] = $valor;
						
						}
					
					}elseif($despesas[1] > 0){

						if($despesas[0] == "Ddata"){
						
							$dadosDespesasEdt[$despesas[1]]['data'] = $valor;
						
						}
					
						if($despesas[0] == "Ddescricao"){
						
							$dadosDespesasEdt[$despesas[1]]['despesa'] = $valor;
							$dadosDespesasEdt[$despesas[1]]['id'] = $despesas[1];
						
						}
						
						if($despesas[0] == "Dfornecedor"){
						
							$dadosDespesasEdt[$despesas[1]]['id_fornecedor'] = $valor;
							$dadosDespesasEdt[$despesas[1]]['id'] = $despesas[1];
						
						}
						
						if($despesas[0] == "Dvalor"){
						
							$dadosDespesasEdt[$despesas[1]]['valor'] = $valor;
						
						}
						
						if($despesas[0] == "Dgarantia"){
						
							$dadosDespesasEdt[$despesas[1]]['dias_garantia'] = $valor;
						
						}
						
						if($despesas[0] == "Dnf"){
						
							$dadosDespesasEdt[$despesas[1]]['nf'] = $valor;
						
						}
					
					}
					
				}
				
				if($dadosDespesas){
				
					foreach($dadosDespesas as $despesas){
					
						$data = explode("/",$despesas['data']);
						
						$despesas['data'] = $data[2]."-".$data[1]."-".$data[0];
						
						$despesas['id_veiculo'] = $idVeiculos;
						
						$despesas['valor'] = str_replace(".","",$despesas['valor']);
						$despesas['valor'] = str_replace(",",".",$despesas['valor']);	
					
						if(!$dbDespesasVeiculos->add($despesas)){
							
							$mensagem .= "Erro ao adicionar despesas.<br>";
							
						}
					
					}
				
				}
				
				if($dadosDespesasEdt){
				
					foreach($dadosDespesasEdt as $despesas){
					
						$data = explode("/",$despesas['data']);
						
						$despesas['data'] = $data[2]."-".$data[1]."-".$data[0];
						
						$despesas['id_veiculo'] = $idVeiculos;
						
						$despesas['valor'] = str_replace(".","",$despesas['valor']);
						$despesas['valor'] = str_replace(",",".",$despesas['valor']);
						
						$dbDespesasVeiculos->edt($despesas['id'], $despesas);
						
					}
				
				}
				
				//PEGA PENDENCIAS
				foreach($_POST as $chave=>$valor){
				
					$pendencias = explode("_",$chave);
					
					if($pendencias[1] <= 0){

						if($pendencias[0] == "Pdata"){
						
							$dadosPendencias[$pendencias[1]]['data'] = $valor;
						
						}
					
						if($pendencias[0] == "Pdescricao"){
						
							$dadosPendencias[$pendencias[1]]['descricao'] = $valor;
						
						}
					
					}elseif($pendencias[1] > 0){

						if($pendencias[0] == "Pdata"){
						
							$dadosPendenciasEdt[$pendencias[1]]['data'] = $valor;
						
						}
					
						if($pendencias[0] == "Pdescricao"){
						
							$dadosPendenciasEdt[$pendencias[1]]['descricao'] = $valor;
							$dadosPendenciasEdt[$pendencias[1]]['id'] = $pendencias[1];
						
						}
					
					}
					
				}
				
				if($dadosPendencias){
				
					foreach($dadosPendencias as $pendencias){
					
						$data = explode("/",$pendencias['data']);
						
						$pendencias['data'] = $data[2]."-".$data[1]."-".$data[0];
						
						$pendencias['id_veiculo'] = $idVeiculos;
					
						if(!$dbPendencias->add($pendencias)){
							
							$mensagem .= "Erro ao adicionar pend&ecirc;ncia.<br>";
							
						}
					
					}
				
				}
				
				if($dadosPendenciasEdt){
				
					foreach($dadosPendenciasEdt as $pendencias){
					
						$data = explode("/",$pendencias['data']);
						
						$pendencias['data'] = $data[2]."-".$data[1]."-".$data[0];
						
						$pendencias['id_veiculo'] = $idVeiculos;
						
						$dbPendencias->edt($pendencias['id'], $pendencias);
						
					}
				
				}
				

				$arrVeiculos = $dbVeiculos->getVeiculoEstoque($idVeiculos);
			
				if($arrVeiculos[0]['icarros'] == 1 || $arrVeiculos[0]['icarros'] == 2){

					if($this->edtIcarrosSistema($arrVeiculos) != "OK"){
				
						if($arrVeiculos[0]['id']){
				
							$dados['icarros'] = 2;
						
							$dbVeiculos->edt($arrVeiculos[0]['id'], $dados);
						
						}
						
					}
				
				}
				/*
				exec("php ../application/views/scripts/icarros/edt-icarros-background.phtml > ../application/views/scripts/icarros/logIcarrosBackground.txt &");

				
				$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
				
				if($arrEmpresa[0]['login_webmotors'] && $arrEmpresa[0]['senha_webmotors']){
					
					$suces = true;
					
					$arrEstoqueWeb = $this->getEstoqueWebmotors($arrEmpresa[0]['cnpj'], $arrEmpresa[0]['login_webmotors'], $arrEmpresa[0]['senha_webmotors']);

					foreach($arrEstoqueWeb as $estoqueWeb){
				
						if(strtolower($estoqueWeb['placa']) == strtolower(str_replace("-","",$arrVeiculos[0]['placa']))){
					
							$arrVeiculos[0]['codigo_modalidade'] = $estoqueWeb['codigo_modalidade'];
							$arrVeiculos[0]['codigo_marca'] = $estoqueWeb['codigo_marca'];
							$arrVeiculos[0]['codigo_modelo'] = $estoqueWeb['codigo_modelo'];
							$arrVeiculos[0]['codigo_versao'] = $estoqueWeb['codigo_versao'];
	
							$arrVeiculoCompleto = $this->alteraValoresWebmotors($arrEmpresa[0]['cnpj'], $arrEmpresa[0]['login_webmotors'], $arrEmpresa[0]['senha_webmotors'], $estoqueWeb['codigo_anuncio'], $arrVeiculos);
				
							if($arrVeiculoCompleto != 500){

								$mensagem = "<br/>Houve um erro inesperado e o veículo não foi alterado na Webmotors.";
								//$mensagem = $arrVeiculoCompleto;
								
							}
					
						}
				
					}
					
				}
				*/
				
				if($mensagem == ""){
			
					$this->view->mensagem = "Altera&ccedil;&otilde;es efetuadas com sucesso!";
			
				}else{
			
					$this->view->mensagem = $mensagem;
		
				}
				
				
			}
		
		}
		
		$this->view->marcas = $dbVeiculos->getMarcasDistintas();
	
		if($idVeiculos == ""){
			
			$idVeiculos = $this->_getParam('id');
			
			
		
		}

		// Valida se ID do veículo foi fornecido
		if(empty($idVeiculos)){
			$this->view->erro = "ID do veículo não fornecido";
			return;
		}

		$arrVeiculo = $dbVeiculos->getVeiculoSelecionadoCompleto($idVeiculos);
		
		// Valida se veículo foi encontrado
		if(empty($arrVeiculo)){
			$this->view->erro = "Veículo não encontrado";
			return;
		}
		
		$id= $arrVeiculo[0]['id_modelo'];

	
		$arrFotosVeiculo = $dbFotosVeiculos->getFotosVeiculoSelecionado($idVeiculos) ?: array();
		$arrOpcionaisVeiculos = $dbOpcionaisVeiculos->getOpcionais() ?: array();
		$arrOpcionaisVeiculo = $dbOpcionaisVeiculos->getOpcionaisVeiculoSelecionado($idVeiculos) ?: array();
		$arrAnexos = $dbAnexosVeiculos->getAnexo($idVeiculos) ?: array();
		$arrDespesasVeiculos = $dbDespesasVeiculos->getDespesas($idVeiculos) ?: array();
		$arrFornecedores = $dbFornecedores->getFornecedoresPorEmpresa($_SESSION['sessionUser']['id_empresa']) ?: array();
		$arrPendencias = $dbPendencias->getPendencias($idVeiculos) ?: array();
		$arrModelo = $dbModelo->fetchAll("id = ".$id);
		
		
		$arrModelo[0]['preco'] = number_format($arrModelo[0]['preco'],2,',','.');
		$this->view->fipe = $arrModelo[0];

		
		$arrVeiculo[0]['data_termino_revisao'] = explode("-",$arrVeiculo[0]['data_termino_revisao']);
		$arrVeiculo[0]['data_termino_revisao'] = array_reverse($arrVeiculo[0]['data_termino_revisao']);
		$arrVeiculo[0]['data_termino_revisao'] = implode("/",$arrVeiculo[0]['data_termino_revisao']);
		
		$this->view->veiculo = $arrVeiculo[0];
		$this->view->opcionais = $arrOpcionaisVeiculos;
		$this->view->opcionaisVeiculo = $arrOpcionaisVeiculo;
		$this->view->fotos = $arrFotosVeiculo;
		$this->view->anexos = $arrAnexos;
		$this->view->despesas = $arrDespesasVeiculos;
		$this->view->fornecedores = $arrFornecedores;
		$this->view->pendencias = $arrPendencias;
		
		if($this->_getParam('termino') == 1){
		
			$this->view->termino = 1;
		
		}else{
		
			$this->view->termino = 0;
		
		}
	
	}

	public function delAction(){
	
		$this->validaAcesso('gerenciar_estoque');
	
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$dados['excluido'] = 1;
		
		if($dbVeiculos->edt($this->_getParam('id'), $dados)){
		
			$arrPathFotos = $dbFotosVeiculos->getFotosVeiculoSelecionado($this->_getParam('id'));
			
			foreach($arrPathFotos as $path){
			
				unlink($path['path']);
			
			}
			
			$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
			$arrVeiculos = $dbVeiculos->getVeiculoEstoque($this->_getParam('id'));
			
			if($arrEmpresa[0]['login_icarros'] && $arrEmpresa[0]['senha_icarros']){
			
				$arrIcarros = $this->getEstoqueIcarros();
				
				foreach($arrIcarros as $key=>$arrI){
				
					if(strtolower(substr($arrVeiculos[0]['placa'],0,3).substr($arrVeiculos[0]['placa'],4)) == strtolower($arrI['placa'])){
					
						try{
			
							$url = 'https://paginasegura.icarros.com.br/services/icarroswebservice?wsdl';

							$client = new SoapClient($url, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
		
							$client->__setLocation('http://www.icarros.com.br/services/icarroswebservice');
		
							$token = $client->autenticarAcesso($arrEmpresa[0]['login_icarros'], $arrEmpresa[0]['senha_icarros']);
			
						}catch (SoapFault $exception){
			
							echo $exception->getMessage();
	
						}
					
						$client->excluirAnuncio($token, $key);
						
					}
				
				}

			}
			
			if($arrEmpresa[0]['login_webmotors'] && $arrEmpresa[0]['senha_webmotors']){
				
				$this->delWebmotors(substr($arrVeiculos[0]['placa'],0,3).substr($arrVeiculos[0]['placa'],4));

			}
			
			
			$this->_helper->redirector->gotoUrl("veiculos/lista2");

		}
	
	}
	
	private function getPlanoIcarros(){
	
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);

		try{
			
			$url = 'https://paginasegura.icarros.com.br/services/icarroswebservice?wsdl';

			$client = new SoapClient($url, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
		
			$client->__setLocation('http://www.icarros.com.br/services/icarroswebservice');
		
			$token = $client->autenticarAcesso($arrEmpresa[0]['login_icarros'], $arrEmpresa[0]['senha_icarros']);
			
		}catch (SoapFault $exception) {
			
			echo $exception->getMessage();
	
		}
	
		$arrAnu = get_object_vars($client->obterListaAnunciantes($token));
		$arrAnuci = get_object_vars($arrAnu['dados']);
		$idAnunciante = $arrAnuci['id'];

		$arr = get_object_vars($client->obterTiposAnuncioAnunciante($token, $idAnunciante));
	
		$arrAnuncio = $arr['tiposAnuncios'];
		
		if($arrAnuncio){
		
			if(count($arrAnuncio) > 1){

				foreach($arrAnuncio as $keyV=>$anuncios){

					$arrDados = get_object_vars($anuncios);

					$arrPlanos[$arrDados['id']] = "<option value='".$arrDados['id']."' selected='selected'>".$arrDados['nome']."</option>";

					if($arrDados['ativos'] < $arrDados['disponiveis']){
					
						$strPlanos .= "<option value='".$arrDados['id']."'>".$arrDados['nome']."</option>";
					
					}
					
					
				}
			
				foreach($arrPlanos as $key=>$valor){
					
					$arrPlanos[$key] = $valor.$strPlanos;
				
				}
			
			}else{
			
				$arrDados = get_object_vars($arrAnuncio);
				$arrPlanos[$arrDados['id']] = "<option value='".$arrDados['id']."' selected='selected'>".$arrDados['nome']."</option>";
			
			}
		
		}
		
		//return $arrAnuncio;
		return $arrPlanos;
	
	}
	
	
	private function addIcarros($arrAdd){
	
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
		
			/////////////////ID ANUNCIANTE///////////////////////////////////////////////////////
			
			try{
				
				$url = 'https://paginasegura.icarros.com.br/services/icarroswebservice?wsdl';

				$client = new SoapClient($url, array('trace'=> 1, 'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
					
				$client->__setLocation('http://www.icarros.com.br/services/icarroswebservice');
					
				$token = $client->autenticarAcesso($arrEmpresa[0]['login_icarros'], $arrEmpresa[0]['senha_icarros']);
				
			}catch (SoapFault $exception) {
				
				//echo $exception->getMessage();
					
			}
				
			$arrAnu = get_object_vars($client->obterListaAnunciantes($token));
			$arrAnuci = get_object_vars($arrAnu['dados']);
			$idAnunciante = $arrAnuci['id'];
			
			///////////////////////////////////////////////////////////////////////////////////
		
		foreach($arrAdd as $add){
			
			
		
			////////QTD PORTAS////////////////
		
			if(stripos($add['modelo'], "5p")){
				$portas = 5;
			}elseif(stripos($add['modelo'], "4p")){
				$portas = 4;
			}elseif(stripos($add['modelo'], "3p")){
				$portas = 3;
			}else{
				$portas = 2;
			}
			//////////////////////////////////
			//////////OPCIONAIS///////////////////
			
			$arrOpcionais = $dbOpcionaisVeiculos->getVeiculosOpcionaisIcarros($add['id']);
			
			$arrOpcional = array();
			
			foreach($arrOpcionais as $opcionais){
			
				if($opcionais['id_opcionais_icarros'] != ""){
			
					$arrOpcional[] = $opcionais['id_opcionais_icarros'];
				
				}
			
			}
			////////////////////////////////////
			////////COMBUSTIVEL//////////////////
			
			if($add['combustivel'] == "Flex"){
			
				$combustivel = 5;
			
			}elseif($add['combustivel'] == "Gasolina"){
			
				$combustivel = 2;
			
			}elseif($add['combustivel'] == "Diesel"){
			
				$combustivel = 6;
			
			}elseif($add['combustivel'] == "Etanol"){
			
				$combustivel = 3;
			
			}elseif($add['combustivel'] == "GNV"){
			
				$combustivel = 4;
			
			}else{
			
				$combustivel = 0;
			
			}
			
			//////////////////////////////////////////
			
			////////////////CORES/////////////////////
			
			if(strtolower($add['cor']) == "amarelo"){
			
				$cor = 1;
			
			}elseif(strtolower($add['cor']) == "amarela"){
			
				$cor = 1;
			
			}elseif(strtolower($add['cor']) == "bege"){
			
				$cor = 4;
			
			}elseif(strtolower($add['cor']) == "branco"){
			
				$cor = 5;
			
			}elseif(strtolower($add['cor']) == "branca"){
			
				$cor = 5;
			
			}elseif(strtolower($add['cor']) == "prata"){
			
				$cor = 7;
			
			}elseif(strtolower($add['cor']) == "preto"){
			
				$cor = 8;
			
			}elseif(strtolower($add['cor']) == "preta"){
			
				$cor = 8;
			
			}elseif(strtolower($add['cor']) == "verde"){
			
				$cor = 9;
			
			}elseif(strtolower($add['cor']) == "vermelho"){
			
				$cor = 10;
			
			}elseif(strtolower($add['cor']) == "vermelha"){
			
				$cor = 10;
			
			}elseif(strtolower($add['cor']) == "azul"){
			
				$cor = 11;
			
			}elseif(strtolower($add['cor']) == "cinza"){
			
				$cor = 12;
			
			}elseif(strtolower($add['cor']) == "dourado"){
			
				$cor = 13;
			
			}elseif(strtolower($add['cor']) == "marrom"){
			
				$cor = 14;
			
			}elseif(strtolower($add['cor']) == "vinho"){
			
				$cor = 15;
			
			}elseif(strtolower($add['cor']) == "roxo"){
			
				$cor = 16;
			
			}elseif(strtolower($add['cor']) == "laranja"){
			
				$cor = 17;
			
			}elseif(strtolower($add['cor']) == "varias cores"){
			
				$cor = 23;
			
			}elseif(strtolower($add['cor']) == "rosa"){
			
				$cor = 25;
			
			}elseif(strtolower($add['cor']) == "bronze"){
			
				$cor = 26;
			
			}else{
			
				$cor = 24;
			
			}
			
			//////////////////////////////////////////

			$arrAnuncio['versao_id'] = $add['versao_id'];
			$arrAnuncio['anoFabricacao'] = $add['ano_fabricacao'];
			$arrAnuncio['anoModelo'] = $add['ano_modelo'];
			$arrAnuncio['portas'] = $portas;
			$arrAnuncio['listaOpcionais_ids'] = $arrOpcional;
			$arrAnuncio['km'] = $add['km'];
			$arrAnuncio['preco'] = $add['valor_venda'];
			$arrAnuncio['combustivel_id'] = $combustivel;
			$arrAnuncio['cor_id'] = $cor;
			$arrAnuncio['placa'] = substr($add['placa'],0,3).substr($add['placa'],4);
			$arrAnuncio['texto'] = $add['obs_site'];
			$arrAnuncio['tipoAnuncio_id'] = $add['tipoAnuncio_id'];
			$arrAnuncio['status'] = 1;
			$arrAnuncio['anuncio0km'] = false;
			$arrAnuncio['spotlight'] = false;
			$arrAnuncio['anunciante_id'] = $idAnunciante;

			$status = $client->inserirAnuncio($token,$arrAnuncio);

			$arrStatus = get_object_vars($status);
			
			if($arrStatus['status'] == "OK"){
			
				$arrDados = get_object_vars($arrStatus['dados']);
				$idVeiculoIcarros = $arrDados['id'];
				
				$arrFotos = $dbFotosVeiculos->getFotosVeiculoIcarros($add['id']);
				
				if($arrFotos){
				
					foreach($arrFotos as $fotos){
					
						$filename =  $fotos['path'];
						$extensao = end(explode(".",$filename));
						$handle = fopen ($filename, "rb");
						$arrFotosBinario = fread ($handle, filesize ($filename));
						fclose($handle);
						
						$client->inserirFoto($token, $idVeiculoIcarros, $extensao, $arrFotosBinario);
			
					}
				
				}
				
				
			
			}

		}
		
		//return $status;
		return $arrStatus['status'];
		
	}
	
	private function obterVersoes($segmento){
	
		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
   
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://selectveiculos.com.br/teste.php");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		$versoes = curl_exec($ch);
		curl_close($ch);

		$arrVersoes = explode("array",$versoes);
		
		foreach($arrVersoes as $versao){
		
			$versao = substr($versao,2);
			$versao = substr($versao,0,-2);
			$arrVersao = explode(",",$versao);
			
			$arrId = explode("=>",$arrVersao[0]);
			$arrFipe = explode("=>",$arrVersao[5]);
			
			$arrVersaoFinal[trim($arrId[1])][substr(substr($arrId[0],4),0,-2)] = trim($arrId[1]);
			$arrVersaoFinal[trim($arrId[1])][substr(substr($arrFipe[0],4),0,-2)] = substr(substr(trim($arrFipe[1]),1),0,-1);

		}
		
		return $arrVersaoFinal;
	
	}
/*	
	private function getOpcionais($opcionais){
	
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);

		try{
			
			$url = 'https://paginasegura.icarros.com.br/services/icarroswebservice?wsdl';

			$client = new SoapClient($url, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
		
			$client->__setLocation('http://www.icarros.com.br/services/icarroswebservice');
		
			$token = $client->autenticarAcesso($arrEmpresa[0]['login_icarros'], $arrEmpresa[0]['senha_icarros']);
			
		}catch (SoapFault $exception) {
			
			echo $exception->getMessage();
	
		}
	
		$arrAnu = get_object_vars($client->obterOpcionais($token));
		//$arrAnuci = get_object_vars($arrAnu['dados']);
		//$idAnunciante = $arrAnuci['id'];

		//$arr = get_object_vars($client->obterTiposAnuncioAnunciante($token, $idAnunciante));
	
		//$arrAnuncio = $arr['tiposAnuncios'];
		
		return $arrAnu;
		
	}
	
*/	
	private function delIcarros($arrDel){
	
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);

		try{
			
			$url = 'https://paginasegura.icarros.com.br/services/icarroswebservice?wsdl';

			$client = new SoapClient($url, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
		
			$client->__setLocation('http://www.icarros.com.br/services/icarroswebservice');
		
			$token = $client->autenticarAcesso($arrEmpresa[0]['login_icarros'], $arrEmpresa[0]['senha_icarros']);
			
		}catch (SoapFault $exception){
			
			echo $exception->getMessage();
	
		}
		
		foreach($arrDel as $key=>$del){
	
			$client->excluirAnuncio($token, $key);

		}

		return "OK";
		
	}
	
	private function edtIcarros($arrEdt){
	
	
		$arrIcarros = $this->getEstoqueIcarros();
		
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);

		try{
			
			$url = 'https://paginasegura.icarros.com.br/services/icarroswebservice?wsdl';

			$client = new SoapClient($url, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
		
			$client->__setLocation('http://www.icarros.com.br/services/icarroswebservice');
		
			$token = $client->autenticarAcesso($arrEmpresa[0]['login_icarros'], $arrEmpresa[0]['senha_icarros']);
			
		}catch (SoapFault $exception){
			
			echo $exception->getMessage();
	
		}
		
		$arrAnu = get_object_vars($client->obterListaAnunciantes($token));
		$arrAnuci = get_object_vars($arrAnu['dados']);
		$idAnunciante = $arrAnuci['id'];
		
		foreach($arrEdt as $edt){
			
			foreach($arrIcarros as $key=>$arrI){
			
				if(strtolower(substr($edt['placa'],0,3).substr($edt['placa'],4)) == strtolower($arrI['placa'])){
				
					if($edt['valor_venda'] != $arrI['preco']){

						////////QTD PORTAS////////////////
		
						if(stripos($edt['modelo'], "5p")){
							$portas = 5;
						}elseif(stripos($edt['modelo'], "4p")){
							$portas = 4;
						}elseif(stripos($edt['modelo'], "3p")){
							$portas = 3;
						}else{
							$portas = 2;
						}
						//////////////////////////////////
						//////////OPCIONAIS///////////////////
							
						$arrOpcionais = $dbOpcionaisVeiculos->getVeiculosOpcionaisIcarros($edt['id']);
							
						$arrOpcional = array();
							
						foreach($arrOpcionais as $opcionais){
							
							if($opcionais['id_opcionais_icarros'] != ""){
							
								$arrOpcional[] = $opcionais['id_opcionais_icarros'];
								
							}
							
						}
						////////////////////////////////////
						////////COMBUSTIVEL//////////////////
							
						if($edt['combustivel'] == "Flex"){
							
							$combustivel = 5;
							
						}elseif($edt['combustivel'] == "Gasolina"){
							
							$combustivel = 2;
							
						}elseif($edt['combustivel'] == "Diesel"){
							
							$combustivel = 6;
							
						}elseif($edt['combustivel'] == "Etanol"){
			
							$combustivel = 3;
			
						}elseif($edt['combustivel'] == "GNV"){
							
							$combustivel = 4;
							
						}else{
							
							$combustivel = 0;
							
						}
			
						//////////////////////////////////////////
							
						////////////////CORES/////////////////////
							
						if(strtolower($edt['cor']) == "amarelo"){
							
							$cor = 1;
							
						}elseif(strtolower($edt['cor']) == "amarela"){
							
							$cor = 1;
							
						}elseif(strtolower($edt['cor']) == "bege"){
							
							$cor = 4;
							
						}elseif(strtolower($edt['cor']) == "branco"){
							
							$cor = 5;
							
						}elseif(strtolower($edt['cor']) == "branca"){
							
							$cor = 5;
							
						}elseif(strtolower($edt['cor']) == "prata"){
							
							$cor = 7;
							
						}elseif(strtolower($edt['cor']) == "preto"){
							
							$cor = 8;
							
						}elseif(strtolower($edt['cor']) == "preta"){
							
							$cor = 8;
							
						}elseif(strtolower($edt['cor']) == "verde"){
							
							$cor = 9;
							
						}elseif(strtolower($edt['cor']) == "vermelho"){
							
							$cor = 10;
							
						}elseif(strtolower($edt['cor']) == "vermelha"){
							
							$cor = 10;
							
						}elseif(strtolower($edt['cor']) == "azul"){
							
							$cor = 11;
							
						}elseif(strtolower($edt['cor']) == "cinza"){
							
							$cor = 12;
							
						}elseif(strtolower($edt['cor']) == "dourada"){
							
							$cor = 13;
							
						}elseif(strtolower($edt['cor']) == "dourado"){
							
							$cor = 13;
							
						}elseif(strtolower($edt['cor']) == "marrom"){
							
							$cor = 14;
							
						}elseif(strtolower($edt['cor']) == "vinho"){
							
							$cor = 15;
							
						}elseif(strtolower($edt['cor']) == "roxo"){
							
							$cor = 16;
							
						}elseif(strtolower($edt['cor']) == "laranja"){
							
							$cor = 17;
							
						}elseif(strtolower($edt['cor']) == "varias cores"){
							
							$cor = 23;
							
						}elseif(strtolower($edt['cor']) == "rosa"){
							
							$cor = 25;
							
						}elseif(strtolower($edt['cor']) == "bronze"){
							
							$cor = 26;
							
						}else{
							
							$cor = 24;
							
						}
							
						//////////////////////////////////////////
			
			
						$arrAnuncio['id'] = $key;
						$arrAnuncio['versao_id'] = $arrI['versao_id'];
						$arrAnuncio['anoFabricacao'] = $edt['ano_fabricacao'];
						$arrAnuncio['anoModelo'] = $edt['ano_modelo'];
						$arrAnuncio['portas'] = $portas;
						$arrAnuncio['listaOpcionais_ids'] = $arrOpcional;
						$arrAnuncio['km'] = $edt['km'];
						$arrAnuncio['preco'] = $edt['valor_venda'];
						$arrAnuncio['combustivel_id'] = $combustivel;
						$arrAnuncio['cor_id'] = $cor;
						$arrAnuncio['placa'] = substr($edt['placa'],0,3).substr($edt['placa'],4);
						$arrAnuncio['texto'] = $edt['obs_site'];
						$arrAnuncio['tipoAnuncio_id'] = $edt['tipoAnuncio_id'];
						$arrAnuncio['status'] = 1;
						$arrAnuncio['anuncio0km'] = false;
						$arrAnuncio['spotlight'] = false;
						$arrAnuncio['anunciante_id'] = $idAnunciante;

						$client->alterarAnuncio($token, $arrAnuncio);

					}
					
					if($edt['tipoAnuncio_id'] != $arrI['tipoAnuncio_id']){
						
						$client->alterarTipoAnuncio($token, $key, $edt['tipoAnuncio_id']);
					
					}
			
				}
			
			}
		
		}

		return "OK";
		
	}

	private function getEstoqueIcarros(){
	
		$dbEmpresas = new Application_Model_DbTable_Empresas();
	
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);

		try{
		
			$url = 'https://paginasegura.icarros.com.br/services/icarroswebservice?wsdl';

			$client = new SoapClient($url, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
			
			$client->__setLocation('http://www.icarros.com.br/services/icarroswebservice');
			
			$token = $client->autenticarAcesso($arrEmpresa[0]['login_icarros'], $arrEmpresa[0]['senha_icarros']);
		
		}catch (SoapFault $exception) {
		
			echo $exception->getMessage();
			
			return "erro";
			
		}
		
		$arrAnu = get_object_vars($client->obterListaAnunciantes($token));
		$arrAnuci = get_object_vars($arrAnu['dados']);
		$idAnunciante = $arrAnuci['id'];

		$arr = get_object_vars($client->obterEstoqueAnunciante($token, $idAnunciante));
		
		$arrAnuncio = $arr['anuncios'];

		if($arrAnuncio){
			
			if(count($arrAnuncio) > 1){
		
				foreach($arrAnuncio as $keyV=>$anuncios){
			
					foreach($anuncios as $keyH=>$anuncio){
					
						$arrDados = get_object_vars($anuncios);

						$arrVeiculo[$arrDados['id']][$keyH] = $anuncio;
					
					}
				
				}
			
			}else{
			
				foreach($arrAnuncio as $keyH=>$anuncio){
					
					$arrDados = get_object_vars($anuncios);

					$arrVeiculo[$arrDados['id']][$keyH] = $anuncio;
					
				}
			
			}
		
		}
		
		return $arrVeiculo;
	
	}
	
	
	private function edtIcarrosSistema($arrEdt){
	
		$arrIcarros = $this->getEstoqueIcarros();
		
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);

		try{
			
			$url = 'https://paginasegura.icarros.com.br/services/icarroswebservice?wsdl';

			$client = new SoapClient($url, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
		
			$client->__setLocation('http://www.icarros.com.br/services/icarroswebservice');
		
			$token = $client->autenticarAcesso($arrEmpresa[0]['login_icarros'], $arrEmpresa[0]['senha_icarros']);
			
		}catch (SoapFault $exception){
			
			echo $exception->getMessage();
	
		}
		
		$arrAnu = get_object_vars($client->obterListaAnunciantes($token));
		$arrAnuci = get_object_vars($arrAnu['dados']);
		$idAnunciante = $arrAnuci['id'];
		
		foreach($arrEdt as $edt){
			
			foreach($arrIcarros as $key=>$arrI){
			
				if(strtolower(substr($edt['placa'],0,3).substr($edt['placa'],4)) == strtolower($arrI['placa'])){

					////////QTD PORTAS////////////////
		
					if(stripos($edt['modelo'], "5p")){
						$portas = 5;
					}elseif(stripos($edt['modelo'], "4p")){
						$portas = 4;
					}elseif(stripos($edt['modelo'], "3p")){
						$portas = 3;
					}else{
						$portas = 2;
					}
					//////////////////////////////////
					//////////OPCIONAIS///////////////////
		
					$arrOpcionais = $dbOpcionaisVeiculos->getVeiculosOpcionaisIcarros($edt['id']);
				
					$arrOpcional = array();
				
					foreach($arrOpcionais as $opcionais){
			
						if($opcionais['id_opcionais_icarros'] != ""){
					
							$arrOpcional[] = $opcionais['id_opcionais_icarros'];
					
						}
				
					}
					////////////////////////////////////
					////////COMBUSTIVEL//////////////////
			
					if($edt['combustivel'] == "Flex"){
				
						$combustivel = 5;
				
					}elseif($edt['combustivel'] == "Gasolina"){
				
						$combustivel = 2;
				
					}elseif($edt['combustivel'] == "Diesel"){
				
						$combustivel = 6;
					
					}elseif($edt['combustivel'] == "Etanol"){
			
						$combustivel = 3;
			
					}elseif($edt['combustivel'] == "GNV"){
					
						$combustivel = 4;
					
					}else{
				
						$combustivel = 0;
				
					}
			
					//////////////////////////////////////////
				
					////////////////CORES/////////////////////
				
					if(strtolower($edt['cor']) == "amarelo"){
				
						$cor = 1;
				
					}elseif(strtolower($edt['cor']) == "amarela"){
				
						$cor = 1;
				
					}elseif(strtolower($edt['cor']) == "bege"){
			
						$cor = 4;
				
					}elseif(strtolower($edt['cor']) == "branco"){
				
						$cor = 5;
				
					}elseif(strtolower($edt['cor']) == "branca"){
			
						$cor = 5;
				
					}elseif(strtolower($edt['cor']) == "prata"){
				
						$cor = 7;
				
					}elseif(strtolower($edt['cor']) == "preto"){
			
						$cor = 8;
				
					}elseif(strtolower($edt['cor']) == "preta"){
			
						$cor = 8;
			
					}elseif(strtolower($edt['cor']) == "verde"){
			
						$cor = 9;
			
					}elseif(strtolower($edt['cor']) == "vermelho"){
				
						$cor = 10;
				
					}elseif(strtolower($edt['cor']) == "vermelha"){
				
						$cor = 10;
				
					}elseif(strtolower($edt['cor']) == "azul"){
				
						$cor = 11;
				
					}elseif(strtolower($edt['cor']) == "cinza"){
				
						$cor = 12;
				
					}elseif(strtolower($edt['cor']) == "dourada"){
				
						$cor = 13;
					
					}elseif(strtolower($edt['cor']) == "dourado"){
				
						$cor = 13;
					
					}elseif(strtolower($edt['cor']) == "marrom"){
					
						$cor = 14;
					
					}elseif(strtolower($edt['cor']) == "vinho"){
					
						$cor = 15;
					
					}elseif(strtolower($edt['cor']) == "roxo"){
					
						$cor = 16;
				
					}elseif(strtolower($edt['cor']) == "laranja"){
				
						$cor = 17;
				
					}elseif(strtolower($edt['cor']) == "varias cores"){
				
						$cor = 23;
				
					}elseif(strtolower($edt['cor']) == "rosa"){
				
						$cor = 25;
				
					}elseif(strtolower($edt['cor']) == "bronze"){
					
						$cor = 26;
					
					}else{
					
						$cor = 24;
				
					}
			
					//////////////////////////////////////////
			
			
					$arrAnuncio['id'] = $key;
					$arrAnuncio['versao_id'] = $arrI['versao_id'];
					$arrAnuncio['anoFabricacao'] = $edt['ano_fabricacao'];
					$arrAnuncio['anoModelo'] = $edt['ano_modelo'];
					$arrAnuncio['portas'] = $portas;
					$arrAnuncio['listaOpcionais_ids'] = $arrOpcional;
					$arrAnuncio['km'] = $edt['km'];
					$arrAnuncio['preco'] = $edt['valor_venda'];
					$arrAnuncio['combustivel_id'] = $combustivel;
					$arrAnuncio['cor_id'] = $cor;
					$arrAnuncio['placa'] = substr($edt['placa'],0,3).substr($edt['placa'],4);
					$arrAnuncio['texto'] = $edt['obs_site'];
					$arrAnuncio['tipoAnuncio_id'] = $arrI['tipoAnuncio_id'];
					$arrAnuncio['status'] = 1;
					$arrAnuncio['anuncio0km'] = false;
					$arrAnuncio['spotlight'] = false;
					$arrAnuncio['anunciante_id'] = $idAnunciante;

					$status = $client->alterarAnuncio($token, $arrAnuncio);

					$arrStatus = get_object_vars($status);
					
					if($arrStatus['status'] == "OK"){
					
						if($arrI['fotos']){
						
							foreach($arrI['fotos'] as $fotoId){
								
								$client->excluirFoto($token, $key, $fotoId);
							
							}
						
						}
					
						$arrFotos = $dbFotosVeiculos->getFotosVeiculoIcarros($edt['id']);
					
						if($arrFotos){
						
							foreach($arrFotos as $fotos){
							
								$filename =  $fotos['path'];
								$extensao = end(explode(".",$filename));
								$handle = fopen ($filename, "rb");
								$arrFotosBinario = fread ($handle, filesize ($filename));
								fclose($handle);
								
								$client->inserirFoto($token, $key, $extensao, $arrFotosBinario);
					
							}
						
						}
						
						return $arrStatus['status'];
					
					}

				}
			
			}
		
		}
		
	}

	private function delWebmotors($placa){
		
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
		
		$url = "http://www.webmotors.com.br/IntegracaoRevendedor/wsLoginSistemaRevendedor.asmx?wsdl";
		$url2 = "http://www.webmotors.com.br/IntegracaoRevendedor/wsEstoqueRevendedorWebMotors.asmx?wsdl";
		
		$arrEstoqueWeb = $this->getEstoqueWebmotors($arrEmpresa[0]['cnpj'], $arrEmpresa[0]['login_webmotors'], $arrEmpresa[0]['senha_webmotors']);
		
		
		foreach($arrEstoqueWeb as $arEstoque){
		
			if(strtoupper($placa) == strtoupper($arEstoque['placa'])){
				
				$idAnuncio = $arEstoque['codigo_anuncio'];
				break;
			
			}
		
		}
		
		try{

			$client = new SoapClient($url,array('trace' => 1, 'soap_version'=> SOAP_1_2));

			$token = $client->autenticar(array("cnpj"=>$arrEmpresa[0]['cnpj'], "email"=>$arrEmpresa[0]['login_webmotors'], "senha"=>$arrEmpresa[0]['senha_webmotors']));

			$arr = get_object_vars($token);
			$arrHash = get_object_vars($arr['autenticarResult']);
			$hash = $arrHash['HashAutenticacao'];

			$client2 = new SoapClient($url2, array('trace' => 1));

			$params = array("pHashAutenticacao"=>$hash, "pCodigoAnuncio"=>$idAnuncio, "pMotivoExclusao"=>"3");
			
			$arrWeb = get_object_vars($client2->ExcluirCarro($params));


		}catch (SoapFault $exception){
	
			echo $exception->getMessage();
		
		}
		
		$retorno = get_object_vars($arrWeb['ExcluirCarroResult']);

		echo $retorno['CodigoRetorno'];
	
	}

	private function geraSitemap(){
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$arrVeiculos = $dbVeiculos->_getSite($arr);
		$arrEmpresas = $dbEmpresas->getEmpresas();

		//Defina a url do site
		$url = "http://www.sistemameucar.com.br";
		
		//Defina a frequencia de atualizacao
		$changefreq = "daily";
		
		// abre ou cria o arquivo xml
		$xml = fopen("sitemap.xml","w+");
		
		//Gravamos os dados iniciais do xml
		fwrite($xml,"<?xml version='1.0' encoding='UTF-8'?>\n<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\n");
		
		fwrite($xml,"\r<url>\n
						<loc>".$url."</loc>\n
						<lastmod>".@date('Y-m-d')."</lastmod>\n
						<changefreq>".$changefreq."</changefreq>\n
						<priority>1.0</priority>\n
					</url>\n");
					
		fwrite($xml,"\r<url>\n
						<loc>".$url."/carros-usados</loc>\n
						<lastmod>".@date('Y-m-d')."</lastmod>\n
						<changefreq>".$changefreq."</changefreq>\n
						<priority>1.0</priority>\n
					</url>\n");
					
		foreach($arrVeiculos as $arrVeiculo){
			
			if($arrVeiculo['descricao_site']){
			
				$arrVeiculo['modelo'] = $arrVeiculo['descricao_site'];

			}
			
			$arrVeiculo['modelo'] = str_replace(" ","-",$arrVeiculo['modelo']);
			$arrSoModelos[] = strtolower(current(explode("-",str_replace(" ","-", $arrVeiculo['modelo']))));
			$arrMarca[] = strtolower(end(explode("-", str_replace(" - ", "-", $arrVeiculo['marca']))));
			
			$strVeiculos .= "\r<url>\n
								<loc>".$url."/carros-usados/veiculo/".$arrVeiculo['cidade']."/".$arrVeiculo['modelo']."/id/".$arrVeiculo['id']."</loc>\n
								<lastmod>".@date('Y-m-d')."</lastmod>\n
								<changefreq>".$changefreq."</changefreq>\n
								<priority>0.8</priority>\n
							</url>\n";
		
		}
		
		fwrite($xml,$strVeiculos);
		
		foreach($arrEmpresas as $arrEmpresa){
			
			if($arrEmpresa['nome_fantasia']){
			
				$arrEmpresa['nome_fantasia'] = str_replace(" ","-",$arrEmpresa['nome_fantasia']);
				
				$strEmpresas .= "\r<url>\n
									<loc>".$url."/carros-usados/busca-veiculos/".$arrEmpresa['cidade']."/".$arrEmpresa['nome_fantasia']."/id_empresa/".$arrEmpresa['id']."</loc>\n
									<lastmod>".@date('Y-m-d')."</lastmod>\n
									<changefreq>".$changefreq."</changefreq>\n
									<priority>0.5</priority>\n
								</url>\n";
								
			}
		
		}
		
		fwrite($xml,$strEmpresas);
		
		$arrSoModelosUnico = array_unique($arrSoModelos);
		$arrMarcasUnico = array_unique($arrMarca);
		
		foreach($arrSoModelosUnico as $arrModelosUnico){
	
			$strModelosUnico .= "\r<url>\n
								<loc>".$url."/carros-usados/busca-veiculos/".$arrModelosUnico."</loc>\n
								<lastmod>".@date('Y-m-d')."</lastmod>\n
								<changefreq>".$changefreq."</changefreq>\n
								<priority>0.5</priority>\n
							</url>\n";
							
		}
		
		fwrite($xml,$strModelosUnico);
		
		
		foreach($arrMarcasUnico as $arrMarcaUnico){

			$strMarcaUnico .= "\r<url>\n
									<loc>".$url."/carros-usados/busca-veiculos/".mb_convert_encoding($arrMarcaUnico, 'UTF-8', 'ISO-8859-1')."</loc>\n
									<lastmod>".@date('Y-m-d')."</lastmod>\n
									<changefreq>".$changefreq."</changefreq>\n
									<priority>0.5</priority>\n
								</url>\n";

		}
		
		fwrite($xml,$strMarcaUnico);

		//Fechamos a estrutura do xml
		fwrite($xml,"\n</urlset>"); 
		
		//Fecha o arquivo aberto (para liberar memoria do servidor)
		fclose($xml);

		return $strMarcaUnico;
		
	}
	
	
	private function geraXMLGlix(){
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		//$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$arrVeiculos = $dbVeiculos->_getSite($arr);
		//$arrEmpresas = $dbEmpresas->getEmpresas();

		//Defina a url do site
		$url = "http://www.sistemameucar.com.br";
		
		// abre ou cria o arquivo xml
		$xml = fopen("glix_meucar.xml","w+");
		
		//Gravamos os dados iniciais do xml
		fwrite($xml,"<?xml version='1.0' encoding='utf-8'?>\n");
		
		fwrite($xml,"<glix>\n");
		
		foreach($arrVeiculos as $arrVeiculo){
			
			if($arrVeiculo['descricao_site']){
			
				$arrVeiculo['modelo'] = $arrVeiculo['descricao_site'];

			}
			
			$arrVeiculo['modelo'] = str_replace(" ","-",$arrVeiculo['modelo']);
			$arrSoModelos[] = strtolower(current(explode("-",str_replace(" ","-", $arrVeiculo['modelo']))));
			$arrMarca[] = strtolower(end(explode("-", str_replace(" - ", "-", $arrVeiculo['marca']))));
			
			$strVeiculos .= "\r<ad>\n
								<id>".$arrVeiculo['id']."</id>\n
								<url>".$url."/carros-usados/veiculo/".$arrVeiculo['cidade']."/".$arrVeiculo['modelo']."/id/".$arrVeiculo['id']."</url>\n
								<title>".$arrVeiculo['modelo']." - ".$arrVeiculo['ano_modelo']." - R$ ".$arrVeiculo['valor_venda']." - ".$arrVeiculo['cidade']." - MeuCar</title>\n
								<content>".$arrVeiculo['obs_site']."</content>\n
								<price>".$arrVeiculo['valor_venda']."</price>\n
								<car_type>".$arrVeiculo['segmento']."</car_type>\n
								<dealer>MeuCar</dealer>\n
								<city_area>".$arrVeiculo['bairro']."</city_area>\n
								<city>".$arrVeiculo['cidade']."</city>\n
								<postcode></postcode>\n
								<region>".$arrVeiculo['estado']."</region>\n
								<make>".mb_convert_encoding($arrVeiculo['marca'], 'UTF-8', 'ISO-8859-1')."</make>\n
								<model>".$arrVeiculo['modelo']."</model>\n
								<color>".$arrVeiculo['cor']."</color>\n
								<year>".$arrVeiculo['ano_modelo']."</year>\n
								<mileage></mileage>\n
								<doors></doors>\n
								<seats></seats>\n
								<fuel>".$arrVeiculo['combustivel']."</fuel>\n";
								
								
			$arrFotosVeiculos = $dbFotosVeiculos->getFotosVeiculoIcarros($arrVeiculo['id']);					
			
			$strVeiculos .= "\r<pictures>\n";
			
			foreach($arrFotosVeiculos as $fotosVeiculos){
				
				$strVeiculos .= "<picture>\n
								 <picture_url>".$url."/".$fotosVeiculos['path']."</picture_url>\n
								 <picture_title>".$arrVeiculo['modelo']."</picture_title>\n
								 </picture>\n";
			
			}
			
			$strVeiculos .= "</pictures>\n";
			
			$strVeiculos .= "<date>".@date("Y-m-d")."</date>
							<expiration_date>".@date("Y")."-".(@date("m")+1)."-".@date("d")."</expiration_date>
							<is_new></is_new>";
								
			$strVeiculos .= "</ad>\n";
							
		
		}
		
		fwrite($xml,$strVeiculos);
		
		//Fechamos a estrutura do xml
		fwrite($xml,"\n</glix>"); 
		
		//Fecha o arquivo aberto (para liberar memoria do servidor)
		fclose($xml);
		
	}

	
	private function getEstoqueWebmotors($cnpj, $login, $senha){
	
		try{
	
			$url = "http://www.webmotors.com.br/IntegracaoRevendedor/wsLoginSistemaRevendedor.asmx?wsdl";
				

			$clientWeb = new SoapClient($url,array('trace' => 1, 'soap_version'=> SOAP_1_2));

			$tokenWeb = $clientWeb->autenticar(array("cnpj"=>$cnpj, "email"=>$login, "senha"=>$senha));

			$arrWeb = get_object_vars($tokenWeb);
			$arrHash = get_object_vars($arrWeb['autenticarResult']);
			$hash = $arrHash['HashAutenticacao'];

			$url2 = "http://www.webmotors.com.br/IntegracaoRevendedor/wsEstoqueRevendedorWebMotors.asmx?wsdl";
			
			$clientWeb2 = new SoapClient($url2, array('trace' => 1));
			
			$params = array("pHashAutenticacao"=>$hash);
			
			$arrEstoqueWeb = get_object_vars($clientWeb2->ObterEstoqueAtual($params));
			$arrEstoqueWeb2 = get_object_vars($arrEstoqueWeb['ObterEstoqueAtualResult']);
			$arrEstoqueWeb3 = $arrEstoqueWeb2['Anuncio'];
	
	
			if(!is_array($arrEstoqueWeb3)){
				
				$arWebEstoque = get_object_vars($arrEstoqueWeb3);
			
				$arrWebEstoque[0]['codigo_anuncio'] = $arWebEstoque['CodigoAnuncio'];
				$arrWebEstoque[0]['codigo_modalidade'] = $arWebEstoque['CodigoModalidade'];
				$arrWebEstoque[0]['codigo_marca'] = $arWebEstoque['CodigoMarca'];
				$arrWebEstoque[0]['codigo_modelo'] = $arWebEstoque['CodigoModelo'];
				$arrWebEstoque[0]['codigo_versao'] = $arWebEstoque['CodigoVersao'];
				$arrWebEstoque[0]['placa'] = $arWebEstoque['Placa'];
			
			
			}else{
	
				foreach($arrEstoqueWeb3 as $key=>$web){
				
					$arWebEstoque = get_object_vars($web);
					
					$arrWebEstoque[$key]['codigo_anuncio'] = $arWebEstoque['CodigoAnuncio'];
					$arrWebEstoque[$key]['codigo_modalidade'] = $arWebEstoque['CodigoModalidade'];
					$arrWebEstoque[$key]['codigo_marca'] = $arWebEstoque['CodigoMarca'];
					$arrWebEstoque[$key]['codigo_modelo'] = $arWebEstoque['CodigoModelo'];
					$arrWebEstoque[$key]['codigo_versao'] = $arWebEstoque['CodigoVersao'];
					$arrWebEstoque[$key]['placa'] = $arWebEstoque['Placa'];

				}
			
			}

			return $arrWebEstoque;

		}catch (SoapFault $exception){
	
			echo $exception->getMessage();
		
		}

	}
	
	
	private function alteraValoresWebmotors($cnpj, $login, $senha, $codigAnuncio, $arrVeiculo){
		
		$url = "http://www.webmotors.com.br/IntegracaoRevendedor/wsLoginSistemaRevendedor.asmx?wsdl";
		$url2 = "http://www.webmotors.com.br/IntegracaoRevendedor/wsEstoqueRevendedorWebMotors.asmx?wsdl";


		$arrAnuncio['CodigoAnuncio'] = $codigAnuncio;
		$arrAnuncio['CodigoModalidade'] = $arrVeiculo[0]['codigo_modalidade'];

		if($arrVeiculo[0]['novo_usado'] == 1){
				
			$arrAnuncio['TipoAnuncio'] = "U";
			
		}else{
				
			$arrAnuncio['TipoAnuncio'] = "N";
			
		}
			
			
		$arrAnuncio['CodigoMarca'] = $arrVeiculo[0]['codigo_marca'];
		$arrAnuncio['CodigoModelo'] = $arrVeiculo[0]['codigo_marca'];
		$arrAnuncio['CodigoVersao'] = $arrVeiculo[0]['codigo_marca'];
		$arrAnuncio['AnoDoModelo'] = $arrVeiculo[0]['ano_modelo'];
		$arrAnuncio['AnoFabricacao'] = $arrVeiculo[0]['ano_fabricacao'];
		if($arrVeiculo[0]['km'] >= 1000){
			
			$arrAnuncio['Km'] = $arrVeiculo[0]['km'];
			
		}else{
			
			$arrAnuncio['Km'] = "";
			
		}
		
		$arrAnuncio['Placa'] = current(explode("-",$arrVeiculo[0]['placa'])).end(explode("-",$arrVeiculo[0]['placa']));
	
		
		if(stristr(strtoupper($arrVeiculo[0]['modelo']), "MANUAL")){

			$arrAnuncio['CodigoCambio'] = 23001;
			$arrAnuncio['DescricaoCambio'] = "Manual";

		}elseif(stristr(strtoupper($arrVeiculo[0]['modelo']), "Aut.")){
	
			$arrAnuncio['CodigoCambio'] = 23003;
			$arrAnuncio['DescricaoCambio'] = "Automático";
			
		}else{
			
			$arrAnuncio['CodigoCambio'] = 23001;
			$arrAnuncio['DescricaoCambio'] = "Manual";
		
		}
			
			
		if(stristr(strtoupper($arrVeiculo[0]['modelo']), "2P")){
			
			$arrAnuncio['NrPortas'] = 2;
			
		}elseif(stristr(strtoupper($arrVeiculo[0]['modelo']), "4P")){
			
			$arrAnuncio['NrPortas'] = 4;
				
		}elseif(stristr(strtoupper($arrVeiculo[0]['modelo']), "3P")){
			
			$arrAnuncio['NrPortas'] = 3;
			
		}elseif(stristr(strtoupper($arrVeiculo[0]['modelo']), "5P")){
			
			$arrAnuncio['NrPortas'] = 4;
			
		}else{
				
			$arrAnuncio['NrPortas'] = 0;
			
		}
		
			
		if(strtolower($arrVeiculo[0]['cor']) == "preta"){
			
			$arrVeiculo[0]['cor'] = "preto";
				
		}
			
		if(strtolower($arrVeiculo[0]['cor']) == "amarela"){
			
			$arrVeiculo[0]['cor'] = "amarelo";
				
		}


		if(strtolower($arrVeiculo[0]['cor']) == "branca"){
			
			$arrVeiculo[0]['cor'] = "branco";
				
		}

		if(strtolower($arrVeiculo[0]['cor']) == "dourada"){
			
			$arrVeiculo[0]['cor'] = "dourado";
				
		}

		if(strtolower($arrVeiculo[0]['cor']) == "roxa"){
			
			$arrVeiculo[0]['cor'] = "roxo";
				
		}

		if(strtolower($arrVeiculo[0]['cor']) == "vermelha"){
			
			$arrVeiculo[0]['cor'] = "vermelho";
				
		}

		foreach($this->getCoresWebmotors() as $key=>$cor){

			if(strtolower($arrVeiculo[0]['cor']) == strtolower($cor)){

				$arrAnuncio['CodigoCor'] = $key;
				$arrAnuncio['DescricaoCor'] = $cor;
				break;
				
			}
			
		}
		
			
		if(strtoupper($arrVeiculo[0]['combustivel']) == "ETANOL"){
				
			$arrAnuncio['CodigoCombustivel'] = 21202;
			$arrAnuncio['DescricaoCombustivel'] = "ÁLCOOL";
			
		}elseif(strtoupper($arrVeiculo[0]['combustivel']) == "GASOLINA"){
				
			$arrAnuncio['CodigoCombustivel'] = 21201;
			$arrAnuncio['DescricaoCombustivel'] = "GASOLINA";
	
		}elseif(strtoupper($arrVeiculo[0]['combustivel']) == "DIESEL"){
				
			$arrAnuncio['CodigoCombustivel'] = 21203;
			$arrAnuncio['DescricaoCombustivel'] = "DIESEL";
			
		}elseif(strtoupper($arrVeiculo[0]['combustivel']) == "GNV"){
				
			$arrAnuncio['CodigoCombustivel'] = 21204;
			$arrAnuncio['DescricaoCombustivel'] = "GNV";
			
		}elseif(strtoupper($arrVeiculo[0]['combustivel']) == "FLEX"){
				
			$arrAnuncio['CodigoCombustivel'] = 21205;
			$arrAnuncio['DescricaoCombustivel'] = "FLEX";
			
		}elseif(strtoupper($arrVeiculo[0]['combustivel']) == "ELETRICIDADE"){
				
			$arrAnuncio['CodigoCombustivel'] = 21210;
			$arrAnuncio['DescricaoCombustivel'] = "ELETRICIDADE";
			
		}
		
			
		$arrAnuncio['Blindado'] = "N";
		$arrAnuncio['AdaptadoDeficientesFisicos'] = "N";
		$arrAnuncio['UnicoDono'] = "N";
		$arrAnuncio['Alienado'] = "N";
		$arrAnuncio['IpvaPago'] = "N";
		$arrAnuncio['RevisadoOficinaAgendaDoCarro'] = "N";
		$arrAnuncio['RevisoesEmConcessionaria'] = "N";
		$arrAnuncio['GarantiaDeFabrica'] = "N";
		$arrAnuncio['Licenciado'] = "N";
		$arrAnuncio['PrecoVenda'] = $arrVeiculo[0]['valor_venda'];
		$arrAnuncio['Observacao'] = $arrVeiculo[0]['obs_site'];
		$arrAnuncio['DataUltimaAlteracao'] = @date("d/m/Y");
			
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();

		$arrOpcionaisVeiculos = $dbOpcionaisVeiculos->getVeiculosOpcionais($arrVeiculo[0]['id']);
		
		$countOp = 0;
		
		foreach($arrOpcionaisVeiculos as $keyOp=>$opcionaisVeiculos){
			
			if($opcionaisVeiculos['id_opcionais_webmotors']){
			
				$arrOpcionais[$countOp]['CodigoOpcional'] = $opcionaisVeiculos['id_opcionais_webmotors'];
				$arrOpcionais[$countOp]['Descricao'] = $opcionaisVeiculos['o.opcional'];
				$arrOpcionais[$countOp]['CodigoRetorno'] = "500";
				
				$countOp++;
				
			}
			
		}
			
		$arrAnuncio['Opcional'] = $arrOpcionais;
	
		foreach($arrAnuncio as $key=>$valor){
			
			$objeto->$key = $valor;
			
		}
	
			
		try{

			$client = new SoapClient($url,array('trace' => 1, 'soap_version'=> SOAP_1_2));

			$token = $client->autenticar(array("cnpj"=>$cnpj, "email"=>$login, "senha"=>$senha));

			$arr = get_object_vars($token);
			$arrHash = get_object_vars($arr['autenticarResult']);
			$hash = $arrHash['HashAutenticacao'];

			$client2 = new SoapClient($url2, array('trace' => 1));
			
			$params = array("pHashAutenticacao"=>$hash, "pAnuncio"=>$objeto);
			
			$arrWeb = get_object_vars($client2->AlterarCarro($params));
				

		}catch (SoapFault $exception){
	
			echo $exception->getMessage();
		
		}
	
		$retorno = get_object_vars($arrWeb['AlterarCarroResult']);

		return $retorno['CodigoRetorno'];

	}
	
	private function getCoresWebmotors(){
		
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
		
		$url = "http://www.webmotors.com.br/IntegracaoRevendedor/wsLoginSistemaRevendedor.asmx?wsdl";
		$url2 = "http://www.webmotors.com.br/IntegracaoRevendedor/wsEstoqueRevendedorWebMotors.asmx?wsdl";
	
		try{

			$client = new SoapClient($url,array('trace' => 1, 'soap_version'=> SOAP_1_2));

			$token = $client->autenticar(array("cnpj"=>$arrEmpresa[0]['cnpj'], "email"=>$arrEmpresa[0]['login_webmotors'], "senha"=>$arrEmpresa[0]['senha_webmotors']));

			$arr = get_object_vars($token);
			$arrHash = get_object_vars($arr['autenticarResult']);
			$hash = $arrHash['HashAutenticacao'];

			$client2 = new SoapClient($url2, array('trace' => 1));
		
			$params = array("pHashAutenticacao"=>$hash);

		
			$arrWeb = get_object_vars($client2->obterCores($params));
			$arrWeb2 = get_object_vars($arrWeb['ObterCoresResult']);
			$arrWeb3 = $arrWeb2['CorWM'];
	
			foreach($arrWeb3 as $key=>$web){
			
				$arWebCor = get_object_vars($web);
		
				//$arrWebMarcas[$key]['codigo_cor'] = $arWebMarcas['CodigoCor'];
				$arrWebCor[$arWebCor['CodigoCor']] = $arWebCor['Descricao'];

			}

			return $arrWebCor;

		}catch (SoapFault $exception){
	
			echo $exception->getMessage();
		
		}

	}
	
	
	private function geraXMLIbiubi(){
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		
		$arrVeiculos = $dbVeiculos->_getSite($arr);

		
		$url = "http://www.sistemameucar.com.br";
		
		// abre ou cria o arquivo xml
		$xml = fopen("ibiubi_meucar.xml","w+");
		
		//Gravamos os dados iniciais do xml
		fwrite($xml,"<?xml version='1.0' encoding='utf-8'?>\n");
		
		fwrite($xml,"<ibiubi>\n");
		
		foreach($arrVeiculos as $arrVeiculo){
		
			if($arrVeiculo['cidade'] != "" && $arrVeiculo['estado'] != "" && $arrVeiculo['valor_venda'] >= 1000){
			
				if($arrVeiculo['ano_modelo'] == "Zero"){
				
					$arrVeiculo['ano_modelo'] = @date("Y");
				
				}

				if(stripos($arrVeiculo['modelo'], "2p")){
				
					$portas = 2;
				
				}elseif(stripos($arrVeiculo['modelo'], "3p")){
				
					$portas = 3;
				
				}elseif(stripos($arrVeiculo['modelo'], "4p")){
				
					$portas = 4;
				
				}elseif(stripos($arrVeiculo['modelo'], "5p")){
				
					$portas = 5;
				
				}else{
					
					$portas = 2;
				
				}
				
				
				
				$arrVeiculo['modelo'] = mb_convert_encoding($arrVeiculo['modelo'], 'UTF-8', 'ISO-8859-1');
				
				$arrVeiculo['modelo'] = str_replace(" ","-",$arrVeiculo['modelo']);
				
				$arrOpcionaisVeiculos = $dbOpcionaisVeiculos->getVeiculosOpcionais($arrVeiculo['id']);
				
				$cambio = "Manual";
				$strOpcionais = "";
				
				foreach($arrOpcionaisVeiculos as $opcionaisVeiculos){
					
					if($opcionaisVeiculos['id'] == 5){
						
						$cambio = "Automático";
					
					}
					
					$arrOpcional = explode(") ", $opcionaisVeiculos['opcional']);
					
					if($arrOpcional[1] != "Farol de neblina" && $arrOpcional[1] != "Freio a disco" && $arrOpcional[1] != "Porta-malas elétrico" && $arrOpcional[1] != "Câmbio automático"){
						
						if($arrOpcional[1] == "CD Player"){
							
							$arrOpcional[1] = "Rádio e CD Player";
						
						}

						$strOpcionais .= "<item><![CDATA[".$arrOpcional[1]."]]></item>\n";

					}
					
					

				}
				
				if($arrVeiculo['combustivel'] == "Flex"){
				
					$arrVeiculo['combustivel'] = "Gasolina e &aacute;lcool";
				
				}
				
				if($arrVeiculo['combustivel'] == "Etanol"){
				
					$arrVeiculo['combustivel'] = "Álcool";
				
				}
				
				if($arrVeiculo['combustivel'] == "Eletricidade"){
				
					$arrVeiculo['combustivel'] = "Elétrico";
				
				}
				
				if($arrVeiculo['combustivel'] == "Gás-Natural"){
				
					$arrVeiculo['combustivel'] = "Gás natural";
				
				}

				
				$arrVeiculo['cor'] = current(explode(" ",$arrVeiculo['cor']));

				if(strtolower($arrVeiculo['cor']) == "preta"){
		
					$arrVeiculo['cor'] = "preto";
			
				}
				
				if(strtolower($arrVeiculo['cor']) == "amarela"){
					
					$arrVeiculo['cor'] = "amarelo";
			
				}


				if(strtolower($arrVeiculo['cor']) == "branca"){
					
					$arrVeiculo['cor'] = "branco";
				
				}

				if(strtolower($arrVeiculo['cor']) == "dourada"){
					
					$arrVeiculo['cor'] = "dourado";
				
				}

				if(strtolower($arrVeiculo['cor']) == "roxa"){
					
					$arrVeiculo['cor'] = "roxo";
			
				}

				if(strtolower($arrVeiculo['cor']) == "vermelha"){
					
					$arrVeiculo['cor'] = "vermelho";
				
				}
				

				
				if($arrVeiculo['marca'] == "VW - VolksWagen"){
					
					$arrVeiculo['marca'] = "VolksWagen";
				
				}
				
				if($arrVeiculo['marca'] == "GM - Chevrolet"){
					
					$arrVeiculo['marca'] = "Chevrolet";
				
				}
				
				$arrModelo = explode("-",$arrVeiculo['modelo']);
				
				if($arrModelo[0] == "Grand"){
					
					$arrModelo[0] = $arrModelo[1];
				
				}
				
				$arrVeiculo['cor'] = strtolower($arrVeiculo['cor']);
				
				$arrModeloUrl = explode("-",$arrVeiculo['modelo']);
				
				$strVeiculos .= "\r<anuncio_carros>\n
									<cod_anuncio><![CDATA[".$arrVeiculo['id']."]]></cod_anuncio>\n
									<url_anuncio><![CDATA[".$url."/carros-usados/veiculo/".$arrVeiculo['cidade']."/".str_replace("/","-", $arrModeloUrl[0]."-".$arrModeloUrl[1]."-".$arrModeloUrl[2])."/id/".$arrVeiculo['id']."]]></url_anuncio>\n
									<data_publicacao><![CDATA[".@date("d/m/Y")."]]></data_publicacao>
									<data_validade><![CDATA[".@date("d")."/".(@date("m")+1)."/".@date("Y")."]]></data_validade>
									<descricao><![CDATA[".$arrVeiculo['obs_site']."]]></descricao>\n
									<fabricante><![CDATA[".mb_convert_encoding($arrVeiculo['marca'], 'UTF-8', 'ISO-8859-1')."]]></fabricante>\n
									<modelo><![CDATA[".$arrModelo[0]."]]></modelo>\n
									<versao><![CDATA[".$arrModelo[1]." ".$arrModelo[2]."]]></versao>\n
									<bairro><![CDATA[]]></bairro>\n
									<cidade><![CDATA[".$arrVeiculo['cidade']."]]></cidade>\n
									<uf><![CDATA[".$arrVeiculo['estado']."]]></uf>\n
									<ano><![CDATA[".$arrVeiculo['ano_modelo']."]]></ano>\n
									<quilometragem><![CDATA[".$arrVeiculo['km']."]]></quilometragem>\n
									<combustivel><![CDATA[".$arrVeiculo['combustivel']."]]></combustivel>\n
									<cambio><![CDATA[".$cambio."]]></cambio>\n
									<portas><![CDATA[".$portas."]]></portas>\n
									<cor><![CDATA[".ucfirst($arrVeiculo['cor'])."]]></cor>\n
									<categoria><![CDATA[]]></categoria>\n
									<placa><![CDATA[]]></placa>\n";
				

							
				
				$strVeiculos .= "\r<opcionais>\n";
				
				$strVeiculos .= $strOpcionais;
				
				$strVeiculos .= "</opcionais>\n";

				
				
				$strVeiculos .= "<anunciante_nome><![CDATA[MeuCar]]></anunciante_nome>\n
								<url_logo_anunciante><![CDATA[".$url."/arquivos_site/images/logo-meu-car.png]]></url_logo_anunciante>\n";
									
									
									
				$arrFotosVeiculos = $dbFotosVeiculos->getFotosVeiculoIcarros($arrVeiculo['id']);					
				
				$strVeiculos .= "\r<fotos>\n";
				
				foreach($arrFotosVeiculos as $fotosVeiculos){
					
					$strVeiculos .= "<url><![CDATA[".$url."/".$fotosVeiculos['path']."]]></url>\n";
				
				}
				
				$strVeiculos .= "</fotos>\n";

				$strVeiculos .= "<url_video><![CDATA[]]></url_video>\n
								 <valor><![CDATA[".$arrVeiculo['valor_venda']."]]></valor>\n";
				
				
				$strVeiculos .= "</anuncio_carros>\n";
		
			}
		
		}
		
		fwrite($xml,$strVeiculos);
		
		//Fechamos a estrutura do xml
		fwrite($xml,"\n</ibiubi>"); 
		
		//Fecha o arquivo aberto (para liberar memoria do servidor)
		fclose($xml);
		
	}
	
	
	private function geraXMLFisgo(){
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		
		$arrVeiculos = $dbVeiculos->_getSite($arr);

		
		$url = "http://www.sistemameucar.com.br";
		
		// abre ou cria o arquivo xml
		$xml = fopen("fisgo_meucar.xml","w+");
		
		//Gravamos os dados iniciais do xml
		fwrite($xml,"<?xml version='1.0' encoding='utf-8'?>\n");
		
		fwrite($xml,"\n<fisgo>"); 
		
		foreach($arrVeiculos as $arrVeiculo){
			
			if(stripos($arrVeiculo['modelo'], "2p")){
			
				$portas = 2;
			
			}elseif(stripos($arrVeiculo['modelo'], "3p")){
			
				$portas = 3;
			
			}elseif(stripos($arrVeiculo['modelo'], "4p")){
			
				$portas = 4;
			
			}elseif(stripos($arrVeiculo['modelo'], "5p")){
			
				$portas = 5;
			
			}else{
				
				$portas = 2;
			
			}
			
			
			
			$arrVeiculo['modelo'] = mb_convert_encoding($arrVeiculo['modelo'], 'UTF-8', 'ISO-8859-1');
			
			$arrVeiculo['modelo'] = str_replace(" ","-",$arrVeiculo['modelo']);
			$arrSoModelos[] = strtolower(current(explode("-",str_replace(" ","-", $arrVeiculo['modelo']))));
			$arrMarca[] = strtolower(end(explode("-", str_replace(" - ", "-", $arrVeiculo['marca']))));
			
			
			$arrOpcionaisVeiculos = $dbOpcionaisVeiculos->getVeiculosOpcionais($arrVeiculo['id']);
			
			$cambio = "Manual";
			$strOpcionais = "";
			
			foreach($arrOpcionaisVeiculos as $opcionaisVeiculos){
				
				if($opcionaisVeiculos['id'] == 5){
					
					$cambio = "Automático";
				
				}
				
				$arrOpcional = explode(") ", $opcionaisVeiculos['opcional']);
				
				if($arrOpcional[1] != "Farol de neblina" && $arrOpcional[1] != "Freio a disco" && $arrOpcional[1] != "Porta-malas elétrico" && $arrOpcional[1] != "Câmbio automático"){
					
					if($arrOpcional[1] == "CD Player"){
						
						$arrOpcional[1] = "Rádio e CD Player";
					
					}

					$strOpcionais .= str_replace("&ocirc;","ô", str_replace("&acirc;","â", str_replace("&aacute;","á", str_replace("&atilde;","ã", str_replace("&ccedil;","ç", str_replace("&eacute;","é", $arrOpcional[1])))))).", ";

				}
				
				

			}
			
			if($arrVeiculo['combustivel'] == "Flex"){
			
				$arrVeiculo['combustivel'] = "Gasolina e álcool";
			
			}
			
			if($arrVeiculo['combustivel'] == "Etanol"){
			
				$arrVeiculo['combustivel'] = "Álcool";
			
			}
			
			if($arrVeiculo['combustivel'] == "Eletricidade"){
			
				$arrVeiculo['combustivel'] = "Elétrico";
			
			}
			
			if($arrVeiculo['combustivel'] == "Gás-Natural"){
			
				$arrVeiculo['combustivel'] = "Gás natural";
			
			}

			
			$arrVeiculo['cor'] = current(explode(" ",$arrVeiculo['cor']));

			if(strtolower($arrVeiculo['cor']) == "preta"){
	
				$arrVeiculo['cor'] = "preto";
		
			}
			
			if(strtolower($arrVeiculo['cor']) == "amarela"){
				
				$arrVeiculo['cor'] = "amarelo";
		
			}


			if(strtolower($arrVeiculo['cor']) == "branca"){
				
				$arrVeiculo['cor'] = "branco";
			
			}

			if(strtolower($arrVeiculo['cor']) == "dourada"){
				
				$arrVeiculo['cor'] = "dourado";
			
			}

			if(strtolower($arrVeiculo['cor']) == "roxa"){
				
				$arrVeiculo['cor'] = "roxo";
		
			}

			if(strtolower($arrVeiculo['cor']) == "vermelha"){
				
				$arrVeiculo['cor'] = "vermelho";
			
			}
			

			
			if($arrVeiculo['marca'] == "VW - VolksWagen"){
				
				$arrVeiculo['marca'] = "VolksWagen";
			
			}
			
			if($arrVeiculo['marca'] == "GM - Chevrolet"){
				
				$arrVeiculo['marca'] = "Chevrolet";
			
			}

			if($arrVeiculo['ano_modelo'] == "Zero"){
				
				$arrVeiculo['ano_modelo'] = @date("Y");
			
			}
			
			$arrVeiculo['cor'] = strtolower($arrVeiculo['cor']);
			
			$arrModeloUrl = explode("-",$arrVeiculo['modelo']);
			
			$strVeiculos .= "\r<veiculo>\n
								<codigo>".$arrVeiculo['id']."</codigo>\n
								<titulo>".mb_convert_encoding($arrVeiculo['marca'], 'UTF-8', 'ISO-8859-1')." ".$arrVeiculo['modelo']." | MeuCar</titulo>
								<marca>".mb_convert_encoding($arrVeiculo['marca'], 'UTF-8', 'ISO-8859-1')."</marca>\n
								<modelo>".$arrVeiculo['modelo']."</modelo>\n
								<fipe>".$arrVeiculo['cod_fipe']."</fipe>
								<ano_fabricacao>".$arrVeiculo['ano_fabricacao']."</ano_fabricacao>\n
								<ano_modelo>".$arrVeiculo['ano_modelo']."</ano_modelo>\n
								<portas>".$portas."</portas>\n
								<combustivel>".$arrVeiculo['combustivel']."</combustivel>\n
								<cor>".ucfirst($arrVeiculo['cor'])."</cor>\n
								<cambio>".$cambio."</cambio>\n
								<final_placa>".substr($arrVeiculo['placa'],-1)."</final_placa>\n
								<km>".$arrVeiculo['km']."</km>\n
								<descricao>".$arrVeiculo['obs_site']."</descricao>\n
								<preco>".$arrVeiculo['valor_venda']."</preco>\n
								<tipo></tipo>\n
								<bairro>".$arrVeiculo['bairro']."</bairro>\n
								<cidade>".$arrVeiculo['cidade']."</cidade>\n
								<uf>".$arrVeiculo['estado']."</uf>\n";

			$strVeiculos .= "<info_adicional>\n";
			
			$strVeiculos .= $strOpcionais;
			
			$strVeiculos .= "</info_adicional>\n";

			$strVeiculos .= "<url>".$url."/carros-usados/veiculo/".$arrVeiculo['cidade']."/".str_replace("/","-", $arrModeloUrl[0]."-".$arrModeloUrl[1]."-".$arrModeloUrl[2])."/id/".$arrVeiculo['id']."</url>\n";

			$arrFotosVeiculos = $dbFotosVeiculos->getFotosVeiculoIcarros($arrVeiculo['id']);					

			if($arrFotosVeiculos[0]['path'] != ""){
			
				$strVeiculos .= "<url_imagem>".$url."/".$arrFotosVeiculos[0]['path']."</url_imagem>\n";
			
			}else{
				
				$strVeiculos .= "<url_imagem>".$url."/arquivos_site/images/logo-meu-car.png</url_imagem>\n";
			
			}
			
			$strVeiculos .= "</veiculo>";
		
		}
		
		fwrite($xml,$strVeiculos);
		
		//Fechamos a estrutura do xml
		fwrite($xml,"\n</fisgo>"); 
		
		//Fecha o arquivo aberto (para liberar memoria do servidor)
		fclose($xml);
		
	}
	
	
	private function geraXMLTrovit(){
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		//$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$arr['segmento'] = "carro";
		
		$arrVeiculos = $dbVeiculos->_getSite($arr);
		//$arrEmpresas = $dbEmpresas->getEmpresas();

		//Defina a url do site
		$url = "http://www.sistemameucar.com.br";
		
		// abre ou cria o arquivo xml
		$xml = fopen("trovit_meucar.xml","w+");
		
		//Gravamos os dados iniciais do xml
		fwrite($xml,"<?xml version='1.0' encoding='utf-8'?>\n");
		
		fwrite($xml,"<trovit>\n");
		
		foreach($arrVeiculos as $arrVeiculo){
		
			if($arrVeiculo['valor_venda'] > 2000){
		
		
			if(stripos($arrVeiculo['modelo'], "2p")){
			
				$portas = 2;
			
			}elseif(stripos($arrVeiculo['modelo'], "3p")){
			
				$portas = 3;
			
			}elseif(stripos($arrVeiculo['modelo'], "4p")){
			
				$portas = 4;
			
			}elseif(stripos($arrVeiculo['modelo'], "5p")){
			
				$portas = 5;
			
			}else{
				
				$portas = 2;
			
			}
			
			if($arrVeiculo['descricao_site']){
			
				$arrVeiculo['modelo'] = $arrVeiculo['descricao_site'];

			}
			
			$arrVeiculo['modelo'] = str_replace(" ","-",$arrVeiculo['modelo']);
			$arrSoModelos[] = strtolower(current(explode("-",str_replace(" ","-", $arrVeiculo['modelo']))));
			$arrMarca[] = strtolower(end(explode("-", str_replace(" - ", "-", $arrVeiculo['marca']))));
			
			
			
			$arrOpcionaisVeiculos = $dbOpcionaisVeiculos->getVeiculosOpcionais($arrVeiculo['id']);
			
			$cambio = "Manual";
			$strOpcionais = "";
			
			foreach($arrOpcionaisVeiculos as $opcionaisVeiculos){
				
				if($opcionaisVeiculos['id'] == 5){
					
					$cambio = "Automático";
				
				}
				
				$arrOpcional = explode(") ", $opcionaisVeiculos['opcional']);
				
				if($arrOpcional[1] != "Farol de neblina" && $arrOpcional[1] != "Freio a disco" && $arrOpcional[1] != "Porta-malas elétrico" && $arrOpcional[1] != "Câmbio automático"){
					
					if($arrOpcional[1] == "CD Player"){
						
						$arrOpcional[1] = "Rádio e CD Player";
					
					}

					$strOpcionais .= mb_convert_encoding($arrOpcional[1], 'ISO-8859-1', 'UTF-8').", ";

				}
				
				

			}
			
			if($arrVeiculo['novo_usado'] == 1){
				
				$arrVeiculo['novo_usado'] = 0;
			
			}else{
				
				$arrVeiculo['novo_usado'] = 1;
			
			}
			
			$arrVeiculo['cor'] = current(explode(" ",$arrVeiculo['cor']));

			if(strtolower($arrVeiculo['cor']) == "preta"){
	
				$arrVeiculo['cor'] = "preto";
		
			}
			
			if(strtolower($arrVeiculo['cor']) == "amarela"){
				
				$arrVeiculo['cor'] = "amarelo";
		
			}


			if(strtolower($arrVeiculo['cor']) == "branca"){
				
				$arrVeiculo['cor'] = "branco";
			
			}

			if(strtolower($arrVeiculo['cor']) == "dourada"){
				
				$arrVeiculo['cor'] = "dourado";
			
			}

			if(strtolower($arrVeiculo['cor']) == "roxa"){
				
				$arrVeiculo['cor'] = "roxo";
		
			}

			if(strtolower($arrVeiculo['cor']) == "vermelha"){
				
				$arrVeiculo['cor'] = "vermelho";
			
			}
			

			
			if($arrVeiculo['marca'] == "VW - VolksWagen"){
				
				$arrVeiculo['marca'] = "VolksWagen";
			
			}
			
			if($arrVeiculo['marca'] == "GM - Chevrolet"){
				
				$arrVeiculo['marca'] = "Chevrolet";
			
			}
			
			if($arrVeiculo['ano_modelo'] == "Zero" || $arrVeiculo['ano_modelo'] ==  (@date("Y")+1)){
				
				$arrVeiculo['ano_modelo'] = @date("Y");
			
			}
			
			if($arrVeiculo['obs_site'] == ""){
				
				$arrVeiculo['obs_site'] = "Venha conferir mais ofertas no MeuCar.com.br";
			
			}elseif($strOpcionais == ""){
				
				$strOpcionais = "Venha conferir mais ofertas no MeuCar.com.br";
			
			}
			
			if($arrVeiculo['km'] != 0){
				
				$km = "<mileage><![CDATA[".$arrVeiculo['km']."]]></mileage>\n";
			
			}else{
			
				$km = "";
			
			}
			
			$strVeiculos .= "\r<ad>\n
								<id><![CDATA[".$arrVeiculo['id']."]]></id>\n
								<url><![CDATA[".$url.mb_convert_encoding("/carros-usados/veiculo/".$arrVeiculo['cidade']."/".$arrVeiculo['modelo']."/id/".$arrVeiculo['id'], 'ISO-8859-1', 'UTF-8')."]]></url>\n
								<title><![CDATA[".$arrVeiculo['modelo']." - ".$arrVeiculo['ano_modelo']." - R$ ".$arrVeiculo['valor_venda']." - ".mb_convert_encoding($arrVeiculo['cidade'], 'ISO-8859-1', 'UTF-8')." - MeuCar]]></title>\n
								<content><![CDATA[".$strOpcionais." ".$arrVeiculo['obs_site']."]]></content>\n
								<price><![CDATA[".$arrVeiculo['valor_venda']."]]></price>\n
								<make><![CDATA[".mb_convert_encoding($arrVeiculo['marca'], 'UTF-8', 'ISO-8859-1')."]]></make>\n
								<model><![CDATA[".$arrVeiculo['modelo']."]]></model>\n
								<color><![CDATA[".$arrVeiculo['cor']."]]></color>\n
								<year><![CDATA[".$arrVeiculo['ano_modelo']."]]></year>\n
								<fuel><![CDATA[".$arrVeiculo['combustivel']."]]></fuel>\n
								<doors><![CDATA[".$portas."]]></doors>\n
								<seats><![CDATA[]]></seats>\n
								<gears><![CDATA[]]></gears>\n
								<car_type><![CDATA[]]></car_type>\n
								".$km."
								<transmission><![CDATA[".$cambio."]]></transmission>\n
								<cylinders><![CDATA[]]></cylinders>\n
								<engine_size><![CDATA[]]></engine_size>\n
								<power><![CDATA[]]></power>\n
								<is_new><![CDATA[".$arrVeiculo['novo_usado']."]]></is_new>\n
								<warranty><![CDATA[]]></warranty>\n
								<dealer><![CDATA[MeuCar]]></dealer>\n
								<region><![CDATA[".$arrVeiculo['estado']."]]></region>\n
								<city><![CDATA[".mb_convert_encoding($arrVeiculo['cidade'], 'ISO-8859-1', 'UTF-8')."]]></city>\n
								<city_area><![CDATA[".mb_convert_encoding($arrVeiculo['bairro'], 'ISO-8859-1', 'UTF-8')."]]></city_area>\n
								<date><![CDATA[".@date("d/m/Y")."]]></date>\n
								<expiration_date><![CDATA[".@date("d")."/".(@date("m")+1)."/".@date("Y")."]]></expiration_date>\n
							
								";
								
								
			$arrFotosVeiculos = $dbFotosVeiculos->getFotosVeiculoIcarros($arrVeiculo['id']);					
			
			$strVeiculos .= "\r<pictures>\n";
			
			foreach($arrFotosVeiculos as $fotosVeiculos){
				
				$strVeiculos .= "<picture>\n
								 <picture_url><![CDATA[".$url."/".$fotosVeiculos['path']."]]></picture_url>\n
								 <picture_title><![CDATA[".$arrVeiculo['modelo']."]]></picture_title>\n
								 </picture>\n";
			
			}
			
			$strVeiculos .= "</pictures>\n";
			
			
								
			$strVeiculos .= "</ad>\n";
		}				
		
		}
		
		fwrite($xml,$strVeiculos);
		
		//Fechamos a estrutura do xml
		fwrite($xml,"\n</trovit>"); 
		
		//Fecha o arquivo aberto (para liberar memoria do servidor)
		fclose($xml);
		
	}
	
	
	private function geraXMLBuscaAcelerada(){
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		//$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$arrVeiculos = $dbVeiculos->_getSite($arr);
		//$arrEmpresas = $dbEmpresas->getEmpresas();

		//Defina a url do site
		$url = "http://www.sistemameucar.com.br";
		
		// abre ou cria o arquivo xml
		$xml = fopen("busca_acelerada_meucar.xml","w+");
		
		//Gravamos os dados iniciais do xml
		fwrite($xml,"<?xml version='1.0' encoding='utf-8'?>\n");
		
		fwrite($xml,"<buscaacelerada>\n");
		
		foreach($arrVeiculos as $arrVeiculo){
		
		
			if(stripos($arrVeiculo['modelo'], "2p")){
			
				$portas = 2;
			
			}elseif(stripos($arrVeiculo['modelo'], "3p")){
			
				$portas = 3;
			
			}elseif(stripos($arrVeiculo['modelo'], "4p")){
			
				$portas = 4;
			
			}elseif(stripos($arrVeiculo['modelo'], "5p")){
			
				$portas = 5;
			
			}else{
				
				$portas = 2;
			
			}
			
			if($arrVeiculo['descricao_site']){
			
				$arrVeiculo['modelo'] = $arrVeiculo['descricao_site'];

			}
			
			$arrVeiculo['modelo'] = str_replace("-"," ",$arrVeiculo['modelo']);
			$arrSoModelos[] = strtolower(current(explode("-",str_replace(" ","-", $arrVeiculo['modelo']))));
			$arrMarca[] = strtolower(end(explode("-", str_replace(" - ", "-", $arrVeiculo['marca']))));
			
			
			
			$arrOpcionaisVeiculos = $dbOpcionaisVeiculos->getVeiculosOpcionais($arrVeiculo['id']);
			
			$cambio = "Manual";
			$strOpcionais = "";
			
			foreach($arrOpcionaisVeiculos as $opcionaisVeiculos){
				
				if($opcionaisVeiculos['id'] == 5){
					
					$cambio = "Automático";
				
				}
				
				$arrOpcional = explode(") ", $opcionaisVeiculos['opcional']);
				
				if($arrOpcional[1] != "Farol de neblina" && $arrOpcional[1] != "Freio a disco" && $arrOpcional[1] != "Porta-malas elétrico" && $arrOpcional[1] != "Câmbio automático"){
					
					if($arrOpcional[1] == "CD Player"){
						
						$arrOpcional[1] = "Rádio e CD Player";
					
					}

					$strOpcionais .= $arrOpcional[1].", ";

				}
				
				

			}
			
			if($arrVeiculo['novo_usado'] == 1){
				
				$arrVeiculo['novo_usado'] = 0;
			
			}else{
				
				$arrVeiculo['novo_usado'] = 1;
			
			}
			
			$arrVeiculo['cor'] = current(explode(" ",$arrVeiculo['cor']));

			if(strtolower($arrVeiculo['cor']) == "preta"){
	
				$arrVeiculo['cor'] = "preto";
		
			}
			
			if(strtolower($arrVeiculo['cor']) == "amarela"){
				
				$arrVeiculo['cor'] = "amarelo";
		
			}


			if(strtolower($arrVeiculo['cor']) == "branca"){
				
				$arrVeiculo['cor'] = "branco";
			
			}

			if(strtolower($arrVeiculo['cor']) == "dourada"){
				
				$arrVeiculo['cor'] = "dourado";
			
			}

			if(strtolower($arrVeiculo['cor']) == "roxa"){
				
				$arrVeiculo['cor'] = "roxo";
		
			}

			if(strtolower($arrVeiculo['cor']) == "vermelha"){
				
				$arrVeiculo['cor'] = "vermelho";
			
			}
			

			
			if($arrVeiculo['marca'] == "VW - VolksWagen"){
				
				$arrVeiculo['marca'] = "VolksWagen";
			
			}
			
			if($arrVeiculo['marca'] == "GM - Chevrolet"){
				
				$arrVeiculo['marca'] = "Chevrolet";
			
			}
			
			if($arrVeiculo['ano_modelo'] == "Zero"){
				
				$arrVeiculo['ano_modelo'] = @date("Y");
			
			}
			
			if($arrVeiculo['obs_site'] == ""){
				
				$arrVeiculo['obs_site'] = "Venha conferir mais ofertas no MeuCar.com.br";
			
			}elseif($strOpcionais == ""){
				
				$strOpcionais = "Venha conferir mais ofertas no MeuCar.com.br";
			
			}
			
			if($arrVeiculo['segmento'] == "carro"){
				
				$tipo = 1;
			
			}elseif($arrVeiculo['segmento'] == "moto"){
				
				$tipo = 2;
			
			}else{
				
				$tipo = 1;
			
			}
	
			
			if($arrVeiculo['combustivel'] == "Etanol"){
			
				$arrVeiculo['combustivel'] = "Álcool";
			
			}
			
			if($arrVeiculo['combustivel'] == "Eletricidade"){
			
				$arrVeiculo['combustivel'] = "Elétrico";
			
			}
			
			if($arrVeiculo['combustivel'] == "Gás-Natural"){
			
				$arrVeiculo['combustivel'] = "GNV";
			
			}
			
			
			$strVeiculos .= "\r<ad>\n
								<type>".$tipo."</type>\n
								<url>".$url."/carros-usados/veiculo/".$arrVeiculo['cidade']."/".$arrVeiculo['modelo']."/id/".$arrVeiculo['id']."</url>\n
								<mobile_url></mobile_url>
								<title>".$arrVeiculo['modelo']." ".$arrVeiculo['ano_modelo']." ".$arrVeiculo['cidade']." MeuCar</title>\n
								<content>".$strOpcionais." ".$arrVeiculo['obs_site']."</content>\n
								<price>".$arrVeiculo['valor_venda']."</price>\n
								<make>".mb_convert_encoding($arrVeiculo['marca'], 'UTF-8', 'ISO-8859-1')."</make>\n
								<model>".$arrVeiculo['modelo']."</model>\n
								<color>".$arrVeiculo['cor']."</color>\n
								<year>".$arrVeiculo['ano_modelo']."</year>\n
								<fuel>".$arrVeiculo['combustivel']."</fuel>\n
								<doors>".$portas."</doors>\n
								<mileage>".$arrVeiculo['km']."</mileage>\n
								<transmission>".$cambio."</transmission>\n
								<region>".$arrVeiculo['estado']."</region>\n
								<city>".$arrVeiculo['cidade']."</city>\n";
								
								
			$arrFotosVeiculos = $dbFotosVeiculos->getFotosVeiculoIcarros($arrVeiculo['id']);					
			
			$strVeiculos .= "\r<pictures>\n";
			
			foreach($arrFotosVeiculos as $fotosVeiculos){
				
				$strVeiculos .= "<picture>\n
								 <picture_url>".$url."/".$fotosVeiculos['path']."</picture_url>\n
								 </picture>\n";
			
			}
			
			$strVeiculos .= "</pictures>\n";
			
			
								
			$strVeiculos .= "</ad>\n";
							
		
		}
		
		fwrite($xml,$strVeiculos);
		
		//Fechamos a estrutura do xml
		fwrite($xml,"\n</buscaacelerada>"); 
		
		//Fecha o arquivo aberto (para liberar memoria do servidor)
		fclose($xml);
		
	}
	
	
	
	private function geraXMLMitula(){
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		
		$arrVeiculos = $dbVeiculos->_getSite($arr);

		$url = "http://www.sistemameucar.com.br";
		
		// abre ou cria o arquivo xml
		$xml = fopen("mitula_meucar.xml","w+");
		
		//Gravamos os dados iniciais do xml
		fwrite($xml,"<?xml version='1.0' encoding='utf-8'?>\n");
		
		fwrite($xml,"<Mitula>\n");
		
		foreach($arrVeiculos as $arrVeiculo){
			
			if($arrVeiculo['cidade'] != "" && $arrVeiculo['estado'] != "" && $arrVeiculo['valor_venda'] >= 1000){

				if(stripos($arrVeiculo['modelo'], "2p")){
				
					$portas = 2;
				
				}elseif(stripos($arrVeiculo['modelo'], "3p")){
				
					$portas = 3;
				
				}elseif(stripos($arrVeiculo['modelo'], "4p")){
				
					$portas = 4;
				
				}elseif(stripos($arrVeiculo['modelo'], "5p")){
				
					$portas = 5;
				
				}else{
					
					$portas = 2;
				
				}
				
				if($arrVeiculo['descricao_site']){
				
					$arrVeiculo['modelo'] = $arrVeiculo['descricao_site'];

				}
				
				$arrVeiculo['modelo'] = str_replace(" ","-",$arrVeiculo['modelo']);
				$arrSoModelos[] = strtolower(current(explode("-",str_replace(" ","-", $arrVeiculo['modelo']))));
				$arrMarca[] = strtolower(end(explode("-", str_replace(" - ", "-", $arrVeiculo['marca']))));
				
				
				
				$arrOpcionaisVeiculos = $dbOpcionaisVeiculos->getVeiculosOpcionais($arrVeiculo['id']);
				
				$cambio = "Manual";
				$strOpcionais = "";
				
				foreach($arrOpcionaisVeiculos as $opcionaisVeiculos){
					
					if($opcionaisVeiculos['id'] == 5){
						
						$cambio = "Automático";
					
					}
					
					$arrOpcional = explode(") ", $opcionaisVeiculos['opcional']);
					
					if($arrOpcional[1] != "Farol de neblina" && $arrOpcional[1] != "Freio a disco" && $arrOpcional[1] != "Porta-malas elétrico" && $arrOpcional[1] != "Câmbio automático"){
						
						if($arrOpcional[1] == "CD Player"){
							
							$arrOpcional[1] = "Rádio e CD Player";
						
						}

						$strOpcionais .= $arrOpcional[1].", ";

					}
					
					

				}
				
				if($arrVeiculo['novo_usado'] == 1){
					
					$arrVeiculo['novo_usado'] = 0;
				
				}else{
					
					$arrVeiculo['novo_usado'] = 1;
				
				}
				
				$arrVeiculo['cor'] = current(explode(" ",$arrVeiculo['cor']));

				if(strtolower($arrVeiculo['cor']) == "preta"){
		
					$arrVeiculo['cor'] = "preto";
			
				}
				
				if(strtolower($arrVeiculo['cor']) == "amarela"){
					
					$arrVeiculo['cor'] = "amarelo";
			
				}


				if(strtolower($arrVeiculo['cor']) == "branca"){
					
					$arrVeiculo['cor'] = "branco";
				
				}

				if(strtolower($arrVeiculo['cor']) == "dourada"){
					
					$arrVeiculo['cor'] = "dourado";
				
				}

				if(strtolower($arrVeiculo['cor']) == "roxa"){
					
					$arrVeiculo['cor'] = "roxo";
			
				}

				if(strtolower($arrVeiculo['cor']) == "vermelha"){
					
					$arrVeiculo['cor'] = "vermelho";
				
				}
				

				
				if($arrVeiculo['marca'] == "VW - VolksWagen"){
					
					$arrVeiculo['marca'] = "VolksWagen";
				
				}
				
				if($arrVeiculo['marca'] == "GM - Chevrolet"){
					
					$arrVeiculo['marca'] = "Chevrolet";
				
				}
				
				if($arrVeiculo['ano_modelo'] == "Zero"){
					
					$arrVeiculo['ano_modelo'] = @date("Y");
				
				}
				
				if($arrVeiculo['obs_site'] == ""){
					
					$arrVeiculo['obs_site'] = "Venha conferir mais ofertas no MeuCar.com.br";
				
				}elseif($strOpcionais == ""){
					
					$strOpcionais = "Venha conferir mais ofertas no MeuCar.com.br";
				
				}
				
				$strVeiculos .= "\r<ad>\n
									<id><![CDATA[".$arrVeiculo['id']."]]></id>\n
									<url><![CDATA[".$url."/carros-usados/veiculo/".$arrVeiculo['cidade']."/".$arrVeiculo['modelo']."/id/".$arrVeiculo['id']."]]></url>\n
									<title><![CDATA[".$arrVeiculo['modelo']." - ".$arrVeiculo['ano_modelo']." - R$ ".$arrVeiculo['valor_venda']." - ".$arrVeiculo['cidade']." - MeuCar]]></title>\n
									<content><![CDATA[".$strOpcionais." ".$arrVeiculo['obs_site']."]]></content>\n
									<price><![CDATA[".$arrVeiculo['valor_venda']."]]></price>\n
									<make><![CDATA[".mb_convert_encoding($arrVeiculo['marca'], 'UTF-8', 'ISO-8859-1')."]]></make>\n
									<model><![CDATA[".$arrVeiculo['modelo']."]]></model>\n
									<color><![CDATA[".$arrVeiculo['cor']."]]></color>\n
									<year><![CDATA[".$arrVeiculo['ano_modelo']."]]></year>\n
									<city><![CDATA[".$arrVeiculo['cidade']."]]></city>\n
									<city_area><![CDATA[".$arrVeiculo['bairro']."]]></city_area>\n
									<postcode><![CDATA[".$arrVeiculo['cep']."]]></postcode>\n
									<region><![CDATA[".$arrVeiculo['estado']."]]></region>\n
									<mileage unit='kilometers'><![CDATA[".$arrVeiculo['km']."]]></mileage>\n
									<doors><![CDATA[".$portas."]]></doors>\n
									<fuel><![CDATA[".$arrVeiculo['combustivel']."]]></fuel>\n
									<transmission><![CDATA[".$cambio."]]></transmission>\n
									<engine_size><![CDATA[]]></engine_size>\n
									<power><![CDATA[]]></power>\n
									<seats><![CDATA[]]></seats>\n
									<gears><![CDATA[]]></gears>\n";
									
									
				$arrFotosVeiculos = $dbFotosVeiculos->getFotosVeiculoIcarros($arrVeiculo['id']);					
				
				$strVeiculos .= "\r<pictures>\n";
				
				foreach($arrFotosVeiculos as $fotosVeiculos){
					
					$strVeiculos .= "<picture>\n
									 <picture_url><![CDATA[".$url."/".$fotosVeiculos['path']."]]></picture_url>\n
									 <picture_title><![CDATA[".$arrVeiculo['modelo']."]]></picture_title>\n
									 </picture>\n";
				
				}
				
				$strVeiculos .= "</pictures>\n";
				
				
				$strVeiculos .= "<date><![CDATA[".@date("d/m/Y")."]]></date>\n
								 <time><![CDATA[".@date("H:i")."]]></time>\n
								 <is_new><![CDATA[".$arrVeiculo['novo_usado']."]]></is_new>\n
								 <car_type><![CDATA[]]></car_type>\n
								 <warranty><![CDATA[]]></warranty>\n
								 <expiration_date><![CDATA[".@date("d")."/".(@date("m")+1)."/".@date("Y")."]]></expiration_date>\n";

				$strVeiculos .= "</ad>\n";

			}
		
		}
		
		fwrite($xml,$strVeiculos);
		
		//Fechamos a estrutura do xml
		fwrite($xml,"\n</Mitula>"); 
		
		//Fecha o arquivo aberto (para liberar memoria do servidor)
		fclose($xml);
		
	}


	public function addAction(){

		// echo "<pre>";
		// var_export($_POST);
		// echo "</pre>";
		// exit;

		$this->validaAcesso('gerenciar_estoque');
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbAnexosVeiculos = new Application_Model_DbTable_AnexosVeiculos();
		$dbOrigemClientes = new Application_Model_DbTable_OrigemClientes();
		
		$this->view->marcas = $dbVeiculos->getMarcasDistintas();
		$this->view->origemClientes = $dbOrigemClientes->_getOrigem(array('noDefault' => true, 'id_empresa' => $_SESSION['sessionUser']['id_empresa']));
		
		if($this->_getParam('troca') == 1){
		
			$this->view->troca = 1;
		
		}else{
		
			$this->view->troca = 0;
		
		}

		if($this->getRequest()->isPost()){
		
			if(!$_POST['id_empresa']){
				$_POST['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			}

			$dadosVeiculos['id_modelo'] = (!empty($_POST['id_modelo']) && $_POST['id_modelo'] != '0') ? $_POST['id_modelo'] : 0;
			$dadosVeiculos['descricao_site'] = $_POST['descricao_site'];
			$dadosVeiculos['placa'] = $_POST['placa'];
			$dadosVeiculos['renavam'] = $_POST['renavam'];
			$dadosVeiculos['novo_usado'] = $_POST['novo_usado'];
			$dadosVeiculos['ano_fabricacao'] = $_POST['ano_fabricacao'];
			$dadosVeiculos['cor'] = $_POST['cor'];
			$dadosVeiculos['km'] = $_POST['km'];
			$dadosVeiculos['combustivel'] = $_POST['combustivel'];
			$dadosVeiculos['chassi'] = $_POST['chassi'];
			$dadosVeiculos['motor'] = $_POST['motor'];
			$dadosVeiculos['valor_aquisicao'] = $_POST['valor_aquisicao'];
			$dadosVeiculos['valor_aquisicao'] = str_replace(".","",$dadosVeiculos['valor_aquisicao']);
			$dadosVeiculos['valor_aquisicao'] = str_replace(",",".",$dadosVeiculos['valor_aquisicao']);		
			$dadosVeiculos['data_aquisicao'] = implode("-",array_reverse(explode("/",$_POST['data_aquisicao'])));
			$dadosVeiculos['valor_venda'] = $_POST['valor_venda'];
			$dadosVeiculos['valor_venda'] = str_replace(".","",$dadosVeiculos['valor_venda']);
			$dadosVeiculos['valor_venda'] = str_replace(",",".",$dadosVeiculos['valor_venda']);	
			$dadosVeiculos['id_empresa'] = $_POST['id_empresa'];
			$dadosVeiculos['loja'] = $_POST['loja'];
			//$dadosVeiculos['temp_troca'] = $_POST['temp_troca'];
			$dadosVeiculos['obs_interna'] = $_POST['obs_interna'];
			$dadosVeiculos['video'] = $_POST['video'];
			$dadosVeiculos['obs_site'] = $_POST['obs_site'];
			$dadosVeiculos['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dadosVeiculos['hora_alteracao'] = @date("Y-m-d H:i:s");
			$dadosVeiculos['exibir_valor_site'] = $_POST['exibir_valor_site'];
			$dadosVeiculos['exibir_km'] = $_POST['exibir_km_1']+$_POST['exibir_km_2'];
			$dadosVeiculos['exibir_site_estoque'] = $_POST['exibir_site_estoque_1']+$_POST['exibir_site_estoque_2'];

			if($_POST['data_inicio_preparacao']){

				$dadosVeiculos['data_inicio_preparacao'] = implode("-",array_reverse(explode("/",$_POST['data_inicio_preparacao'])));

				$dbPeriodoPreparacao = new Application_Model_DbTable_PeriodoPreparacao();
				$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
				$arrPeriodos = current($dbPeriodoPreparacao->getPeriodoPreparacao($arr));

				$servico = "mecanico";
				$dadosVeiculos[$servico.'_date_entrada'] = $dadosVeiculos['data_inicio_preparacao'];
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "funilaria";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "martelinho";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "eletrica";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "tapecaria";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "lavacar";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "volante";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "calotas";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "pneus";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "chaveiro";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "pincelar";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				$servico = "outros";
				$dadosVeiculos[$servico.'_date_entrada'] = $dataFimTemp;
				$arrTemp = explode("-", $dadosVeiculos[$servico.'_date_entrada']);
				$dadosVeiculos[$servico.'_date_concluido'] = @date("Y-m-d", mktime(0,0,0,  $arrTemp[1],  ($arrTemp[2]+$arrPeriodos[$servico]), $arrTemp[0]));
				$dataFimTemp = $dadosVeiculos[$servico.'_date_concluido'];

				//$servico = "concluido";
				//$dadosVeiculos[$servico.'_date_concluido'] = $dataFimTemp;

			}

			
			if(!$_POST['ativo']){
			
				$dadosVeiculos['ativo'] = 0;
			
			}else{
			
				$dadosVeiculos['ativo'] = $_POST['ativo'];
				
			}
			
			if($_POST['origem'] == "0"){
			
				$dadosVeiculos['origem'] = $_POST['origem_descricao'];
			
			}else{
			
				$dadosVeiculos['origem'] = $_POST['origem'];
			
			}
			
			if($_POST['consignado'] == 0){
			
				$dadosVeiculos['consignado'] = 0;
			
			}elseif($_POST['consignado'] == 1){
			
				$dadosVeiculos['consignado'] = 1;
			
			}elseif($_POST['consignado'] == 2){
			
				$dadosVeiculos['consignado'] = 2;
			
			}elseif($_POST['consignado'] == 3){
			
				$dadosVeiculos['consignado'] = 3;
			
			}

			$mensagem = "";
			
			$cont = 1;
		
			if($dbVeiculos->add($dadosVeiculos)){

				$idVeiculos = $dbVeiculos->getLastId();
				
				$arrCheckList['id_veiculo'] =  $idVeiculos[0]['id'];
				$arrCheckList['quitado_leasing'] = $_POST['quitado_leasing'];
				$arrCheckList['pf_pj'] = $_POST['pf_pj'];
				$arrCheckList['gnv'] = $_POST['gnv'];
				
				if($_POST['gnv'] == 0){
				
					$arrCheckList['doc_gnv'] = 3;
				
				}else{
				
					$arrCheckList['doc_gnv'] = $_POST['doc_gnv'];
				
				}
				
				$dbCheckList = new Application_Model_DbTable_CheckList();
				
				if(!$dbCheckList->add($arrCheckList)){
				
					$mensagem .= "Erro ao cadastrar check-list.<br>";
				
				}
				
				$capa = explode("_", $_POST['capa']);
				
				$conts = 0;
				foreach($_FILES as $chave=>$valores){

					if($chave == "foto"){
				
						foreach($_FILES["foto"]["error"] as $key => $error){
							
							if(!file_exists(("fotos_veiculos/".$dadosVeiculos['id_empresa']))){
							
								$extensao = strtolower(end(explode(".",$_FILES['foto']['name'][$key])));
								mkdir("fotos_veiculos/".$dadosVeiculos['id_empresa']);
								chmod("fotos_veiculos/".$dadosVeiculos['id_empresa'], 0755);
								$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$dadosVeiculos['descricao_site']))."-".$dadosVeiculos['ano_fabricacao']."-".@date("his").$key.".".$extensao;
								
							}else{
						
								$extensao = strtolower(end(explode(".",$_FILES['foto']['name'][$key])));
								$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$dadosVeiculos['descricao_site']))."-".$dadosVeiculos['ano_fabricacao']."-".@date("his").$key.".".$extensao;
								
							}
								
							if($_FILES['foto']['tmp_name'][$key] != ""){
								
								
								/////////////////REDIMENCIONA IMAGEM///////////////////////
								# Caminho da imagem a ser redimensionada: 
								$input_image = $_FILES['foto']['tmp_name'][$key];
									 
								// Pega o tamanho original da imagem e armazena em um Array:
								$size = getimagesize( $input_image );
									 
								// Configura a nova largura da imagem:
								$thumb_width = "780";
									 
								// Calcula a altura da nova imagem para manter a proporção na tela: 
								$thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );
									 
								// Cria a imagem com as cores reais originais na memória.
								$thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );
									 
								// Criará uma nova imagem do arquivo.
								$src_img = ImageCreateFromJPEG( $input_image );
									 
								// Criará a imagem redimensionada:
								ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );
									 
								// Informe aqui o novo nome da imagem e a localização:
								$copied = ImageJPEG( $thumbnail, $novoNome);
								chmod($novoNome, 0755); 
								// Limpa da memoria a imagem criada temporáriamente: 
								ImageDestroy( $thumbnail );
								
								
								//$copied = copy($_FILES['foto']['tmp_name'][$key], $novoNome);
								
							}
								
							if($copied){
								
								$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
					
								$dadosFotos['id_veiculo'] = $idVeiculos[0]['id'];
								$dadosFotos['path'] = stripslashes($novoNome);
						
								if($conts == $capa[1]){
									
									$dadosFotos['capa'] = 1;
									$conts++;
									
								}else{
									
									$dadosFotos['capa'] = 0;
									$conts++;
									
								}
								
								if(!$dbFotosVeiculos->add($dadosFotos)){
									
									$mensagem .= "Erro ao realizar o upload de fotos.<br>";

								}
								
							}
								
							if($cont == 9){
					
								break;
								
							}
				
							$cont++;
						
						}
					
					}
				
				}
				
				//PEGA ANEXOS
				foreach($_FILES as $chave=>$valor){
				
					$anexos = explode("_",$chave);

					if($anexos[0] == "anexo"){
			
						if($_FILES[$chave]['name']){
							
							if(!file_exists(("anexos_veiculos/".$dadosVeiculos['id_empresa']))){
				
								mkdir("anexos_veiculos/".$dadosVeiculos['id_empresa']);
								chmod("anexos_veiculos/".$dadosVeiculos['id_empresa'], 0755); 
								$novoNome = "anexos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Y-m-d h:i:s")."_".$_FILES[$chave]['name'];
							
							}else{
							
								$novoNome = "anexos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Y-m-d h:i:s")."_".$_FILES[$chave]['name'];
							
							}
					
							$copied = copy($_FILES[$chave]['tmp_name'], $novoNome);
							
	
							if($copied){
								
								chmod($novoNome, 0755);
								
								$dbAnexosVeiculos = new Application_Model_DbTable_AnexosVeiculos();
								
								$dadosAnexos['id_veiculo'] = $idVeiculos[0]['id'];
								$dadosAnexos['path'] = $novoNome;
								$dadosAnexos['descricao'] = $_POST['descricaoanexo_'.$anexos[1]];
							
								if(!$dbAnexosVeiculos->add($dadosAnexos)){
								
									$mensagem .= "Erro ao realizar o upload de anexos.<br>";
									
								
								}
					
							}
						
						}
					
					}
				
				}
				
				foreach($_POST as $chave=>$valor){
				
					$despesas = explode("_",$chave);

					if($despesas[0] == "Ddata"){
					
						$dadosDespesas[$despesas[1]]['data'] = $valor;
					
					}
				
					if($despesas[0] == "Ddescricao"){
					
						$dadosDespesas[$despesas[1]]['despesa'] = $valor;
					
					}
					
					if($despesas[0] == "Dfornecedor"){
					
						$dadosDespesas[$despesas[1]]['id_fornecedor'] = $valor;
					
					}
					
					if($despesas[0] == "Dvalor"){
					
						$dadosDespesas[$despesas[1]]['valor'] = $valor;
					
					}
					
					if($despesas[0] == "Dgarantia"){
					
						$dadosDespesas[$despesas[1]]['dias_garantia'] = $valor;
					
					}
					
					if($despesas[0] == "Dnf"){
					
						$dadosDespesas[$despesas[1]]['nf'] = $valor;
					
					}
					
					
					//PEGA OPCIONAIS
					$opcional = explode("_",$chave);

					if($opcional[0] == "opcional"){
					
						$opcionais['id_veiculo'] = $idVeiculos[0]['id'];
						$opcionais['id_opcional'] = $opcional[1];
						
						if(!$dbOpcionaisVeiculos->add($opcionais)){
						
							$mensagem .= "Erro ao adicionar opcionais.<br>";
						
						}
					
					}
					
				}
				
				
				
				//PEGA DESPESAS
				foreach($dadosDespesas as $despesas){
				
					$data = explode("/",$despesas['data']);
					
					$despesas['data'] = $data[2]."-".$data[1]."-".$data[0];
					
					$despesas['id_veiculo'] = $idVeiculos[0]['id'];
				
					$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
					
					if($despesas['despesa'] != "" || $despesas['id_fornecedor'] != "" || $despesas['valor'] != ""){
					
						$despesas['valor'] = $despesas['valor'];
						$despesas['valor'] = str_replace(".","",$despesas['valor']);
						$despesas['valor'] = str_replace(",",".",$despesas['valor']);		
					
						if(!$dbDespesasVeiculos->add($despesas)){
						
							$mensagem .= "Erro ao adicionar despesas.<br>";
						
						}
						
					}
				
				}
				
				//PEGA PENDENCIAS
				foreach($_POST as $chave=>$valor){
				
					$pendencias = explode("_",$chave);

					if($pendencias[0] == "Pdata"){
					
						$dadosPendencias[$pendencias[1]]['data'] = $valor;
					
					}
					
					if($pendencias[0] == "Pdescricao"){
					
						$dadosPendencias[$pendencias[1]]['descricao'] = $valor;
					
					}
					
				}
				
				foreach($dadosPendencias as $pandencia){
				
					$data = explode("/",$pandencia['data']);
					
					$pandencia['data'] = $data[2]."-".$data[1]."-".$data[0];
					
					$pandencia['id_veiculo'] = $idVeiculos[0]['id'];
					$pandencia['baixada'] = 0;
				
					$dbPendenciasVeiculos = new Application_Model_DbTable_PendenciasVeiculos();
					
					if($pandencia['descricao'] != ""){
					
						if(!$dbPendenciasVeiculos->add($pandencia)){
						
							$mensagem .= "Erro ao adicionar pendências.<br>";
						
						}
						
					}
				
				}

			}
			
			$this->geraSitemap();
			
			$this->geraXMLGlix();
			
			$this->geraXMLIbiubi();
			
			$this->geraXMLFisgo();
			
			$this->geraXMLTrovit();
			
			$this->geraXMLBuscaAcelerada();
			
			$this->geraXMLMitula();

			if($mensagem == ""){
				
				$this->_helper->redirector->gotoUrl("veiculos/edt/id/".$idVeiculos[0]['id']."/msg/".$mensagem);
			
			}else{
			
				$this->view->mensagem = $mensagem;
			
			}
			
			
			

		}
	
	}
	
	

}

?>
