<?php
header("Content-Type: text/html; charset=UTF-8", true);

class AlteracoesController extends Zend_Controller_Action {

	public function init() {

		$this->view->titulo = "Ve&iacute;culos";

		Zend_Session::start();
		
	}

	public function validaAcesso($require) {

		if (!in_array($require, $_SESSION['sessionUser']['permissoes'])) {
			
			$this->_helper->redirector->gotoUrl(URL . "/index/bad-access");
		
		}
	
	}
   
   
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
			
			if(isset($_POST['valor_aquisicao'])) {
			
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
			$dadosVeiculos['data_termino_revisao'] = $_POST['data_termino_revisao'];
			$dadosVeiculos['data_termino_revisao'] = explode("/",$dadosVeiculos['data_termino_revisao']);
			$dadosVeiculos['data_termino_revisao'] = array_reverse($dadosVeiculos['data_termino_revisao']);
			$dadosVeiculos['data_termino_revisao'] = implode("-",$dadosVeiculos['data_termino_revisao']);
			
			if(!isset($_POST['ativo'])) {
			
				$dadosVeiculos['ativo'] = 0;
			
			}else{
			
				$dadosVeiculos['ativo'] = $_POST['ativo'];
				
			}
			
			if(isset($_POST['origem']) && $_POST['origem'] == "0"){
			
				$dadosVeiculos['origem'] = $_POST['origem_descricao'];
			
			}else{
			
				$dadosVeiculos['origem'] = $_POST['origem'];
			
			}
			
			if(isset($_POST['consignado'])) {
			
				$dadosVeiculos['consignado'] = 1;
			
			}else{
			
				$dadosVeiculos['consignado'] = 0;
			
			}

			$mensagem = "";
		
			if($dbVeiculos->edt($_POST['id'],$dadosVeiculos)){

				$idVeiculos = $_POST['id'];
				
				if(isset($_POST['capa'])) {
				
					$arrCapa = explode("\\",$_POST['capa']);
					
					if(isset($arrCapa[2])) {
						
						$strCapa = $arrCapa[2];
					
					}elseif(isset($arrCapa[0])){
						
						$strCapa = $arrCapa[0];
						$idCapa = $arrCapa[0];
					
					}
				
				}
			
				$cont = 1;
				
				$arrCheckList['id_veiculo'] =  $idVeiculos;
				$arrCheckList['quitado_leasing'] = $_POST['quitado_leasing'];
				$arrCheckList['pf_pj'] = $_POST['pf_pj'];
				$arrCheckList['gnv'] = $_POST['gnv'];
				
				if(isset($_POST['gnv']) && $_POST['gnv'] == 0){
				
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
									$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Ymdhis").$key.".".$extensao;

									
								}else{
									
									$extensao = strtolower(end(explode(".",$_FILES['foto']['name'][$key])));
									$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Ymdhis").$key.".".$extensao;
									
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
										 
									// Limpa da memoria a imagem criada temporáriamente: 
									ImageDestroy( $thumbnail );
									
									
									
									//$copied = copy($_FILES['foto']['tmp_name'][$key], $novoNome);
									
								}
									
								if($copied){
									
									$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
								
									$dadosFotos['id_veiculo'] = $idVeiculos;
									$dadosFotos['path'] = $novoNome;

									$idFoto = $dbFotosVeiculos->add($dadosFotos);
									
									if(isset($_POST['capa'])){
									
										if($_FILES['foto']['name'][$key] == $strCapa){
											
											$idCapa = $idFoto;
										
										}

									}
									
									if(isset($_POST['capa_multiplo'])) {
				
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
				$arrFotosVeiculo = $dbFotosVeiculos->getFotosVeiculoSelecionado($idVeiculos);
					
				foreach($arrFotosVeiculo as $fotosVeiculos){
						
					if(isset($fotosVeiculos['id']) && $idCapa == $fotosVeiculos['id']){
	
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

					if(isset($opcional[0]) && $opcional[0] == "opcional"){
				
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

					if(isset($anexos[0]) && $anexos[0] == "anexo"){
				
						if(isset($_FILES[$chave]['name'])) {
							
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
					
					if(isset($despesas[1]) && $despesas[1] <= 0){

						if(isset($despesas[0]) && $despesas[0] == "Ddata"){
						
							$dadosDespesas[$despesas[1]]['data'] = $valor;
						
						}
					
						if(isset($despesas[0]) && $despesas[0] == "Ddescricao"){
						
							$dadosDespesas[$despesas[1]]['despesa'] = $valor;
						
						}
						
						if(isset($despesas[0]) && $despesas[0] == "Dfornecedor"){
						
							$dadosDespesas[$despesas[1]]['id_fornecedor'] = $valor;
						
						}
						
						if(isset($despesas[0]) && $despesas[0] == "Dvalor"){
						
							$dadosDespesas[$despesas[1]]['valor'] = $valor;
						
						}
						
						if(isset($despesas[0]) && $despesas[0] == "Dgarantia"){
						
							$dadosDespesas[$despesas[1]]['dias_garantia'] = $valor;
						
						}
						
						if(isset($despesas[0]) && $despesas[0] == "Dnf"){
						
							$dadosDespesas[$despesas[1]]['nf'] = $valor;
						
						}
					
					}elseif(isset($despesas[1]) && $despesas[1] > 0){

						if(isset($despesas[0]) && $despesas[0] == "Ddata"){
						
							$dadosDespesasEdt[$despesas[1]]['data'] = $valor;
						
						}
					
						if(isset($despesas[0]) && $despesas[0] == "Ddescricao"){
						
							$dadosDespesasEdt[$despesas[1]]['despesa'] = $valor;
							$dadosDespesasEdt[$despesas[1]]['id'] = $despesas[1];
						
						}
						
						if(isset($despesas[0]) && $despesas[0] == "Dfornecedor"){
						
							$dadosDespesasEdt[$despesas[1]]['id_fornecedor'] = $valor;
							$dadosDespesasEdt[$despesas[1]]['id'] = $despesas[1];
						
						}
						
						if(isset($despesas[0]) && $despesas[0] == "Dvalor"){
						
							$dadosDespesasEdt[$despesas[1]]['valor'] = $valor;
						
						}
						
						if(isset($despesas[0]) && $despesas[0] == "Dgarantia"){
						
							$dadosDespesasEdt[$despesas[1]]['dias_garantia'] = $valor;
						
						}
						
						if(isset($despesas[0]) && $despesas[0] == "Dnf"){
						
							$dadosDespesasEdt[$despesas[1]]['nf'] = $valor;
						
						}
					
					}
					
				}
				
				if(isset($dadosDespesas)) {
				
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
				
				if(isset($dadosDespesasEdt)) {
				
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
					
					if(isset($pendencias[1]) && $pendencias[1] <= 0){

						if(isset($pendencias[0]) && $pendencias[0] == "Pdata"){
						
							$dadosPendencias[$pendencias[1]]['data'] = $valor;
						
						}
					
						if(isset($pendencias[0]) && $pendencias[0] == "Pdescricao"){
						
							$dadosPendencias[$pendencias[1]]['descricao'] = $valor;
						
						}
					
					}elseif(isset($pendencias[1]) && $pendencias[1] > 0){

						if(isset($pendencias[0]) && $pendencias[0] == "Pdata"){
						
							$dadosPendenciasEdt[$pendencias[1]]['data'] = $valor;
						
						}
					
						if(isset($pendencias[0]) && $pendencias[0] == "Pdescricao"){
						
							$dadosPendenciasEdt[$pendencias[1]]['descricao'] = $valor;
							$dadosPendenciasEdt[$pendencias[1]]['id'] = $pendencias[1];
						
						}
					
					}
					
				}
				
				if(isset($dadosPendencias)) {
				
					foreach($dadosPendencias as $pendencias){
					
						$data = explode("/",$pendencias['data']);
						
						$pendencias['data'] = $data[2]."-".$data[1]."-".$data[0];
						
						$pendencias['id_veiculo'] = $idVeiculos;
					
						if(!$dbPendencias->add($pendencias)){
							
							$mensagem .= "Erro ao adicionar pend&ecirc;ncia.<br>";
							
						}
					
					}
				
				}
				
				if(isset($dadosPendenciasEdt)) {
				
					foreach($dadosPendenciasEdt as $pendencias){
					
						$data = explode("/",$pendencias['data']);
						
						$pendencias['data'] = $data[2]."-".$data[1]."-".$data[0];
						
						$pendencias['id_veiculo'] = $idVeiculos;
						
						$dbPendencias->edt($pendencias['id'], $pendencias);
						
					}
				
				}
				
				$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
			
				if(isset($arrEmpresa[0]['login_icarros']) && isset($arrEmpresa[0]['senha_icarros'])) {
				
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
		$arrOpcionaisVeiculos = $dbOpcionaisVeiculos->getOpcionais();
		$arrOpcionaisVeiculo = $dbOpcionaisVeiculos->getOpcionaisVeiculoSelecionado($this->_getParam('id'));
		$arrAnexos = $dbAnexosVeiculos->getAnexo($this->_getParam('id'));
		$arrDespesasVeiculos = $dbDespesasVeiculos->getDespesas($this->_getParam('id'));
		$arrFornecedores = $dbFornecedores->getFornecedoresPorEmpresa($_SESSION['sessionUser']['id_empresa']);
		$arrPendencias = $dbPendencias->getPendencias($this->_getParam('id'));
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
			
			if(!isset($_POST['ativo'])) {
			
				$dadosVeiculos['ativo'] = 0;
			
			}else{
			
				$dadosVeiculos['ativo'] = $_POST['ativo'];
				
			}
			
			if(isset($_POST['origem']) && $_POST['origem'] == "0"){
			
				$dadosVeiculos['origem'] = $_POST['origem_descricao'];
			
			}else{
			
				$dadosVeiculos['origem'] = $_POST['origem'];
			
			}
			
			if(isset($_POST['consignado'])) {
			
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
				
				if(isset($_POST['gnv']) && $_POST['gnv'] == 0){
				
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
								$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Ymdhis").$key.".".$extensao;
								
							}else{
									
								$extensao = strtolower(end(explode(".",$_FILES['foto']['name'][$key])));
								$novoNome = "fotos_veiculos/".$dadosVeiculos['id_empresa']."/".@date("Ymdhis").$key.".".$extensao;
								
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
									 
								// Limpa da memoria a imagem criada temporáriamente: 
								ImageDestroy( $thumbnail );
								
								
								//$copied = copy($_FILES['foto']['tmp_name'][$key], $novoNome);
								
							}
								
							if($copied){
								
								$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
					
								$dadosFotos['id_veiculo'] = $idVeiculos[0]['id'];
								$dadosFotos['path'] = $novoNome;
						
								if(isset($capa[1]) && $conts == $capa[1]){
									
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

					if(isset($anexos[0]) && $anexos[0] == "anexo"){
			
						if(isset($_FILES[$chave]['name'])) {
							
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

					if(isset($despesas[0]) && $despesas[0] == "Ddata"){
					
						$dadosDespesas[$despesas[1]]['data'] = $valor;
					
					}
				
					if(isset($despesas[0]) && $despesas[0] == "Ddescricao"){
					
						$dadosDespesas[$despesas[1]]['despesa'] = $valor;
					
					}
					
					if(isset($despesas[0]) && $despesas[0] == "Dfornecedor"){
					
						$dadosDespesas[$despesas[1]]['id_fornecedor'] = $valor;
					
					}
					
					if(isset($despesas[0]) && $despesas[0] == "Dvalor"){
					
						$dadosDespesas[$despesas[1]]['valor'] = $valor;
					
					}
					
					if(isset($despesas[0]) && $despesas[0] == "Dgarantia"){
					
						$dadosDespesas[$despesas[1]]['dias_garantia'] = $valor;
					
					}
					
					if(isset($despesas[0]) && $despesas[0] == "Dnf"){
					
						$dadosDespesas[$despesas[1]]['nf'] = $valor;
					
					}
					
					
					//PEGA OPCIONAIS
					$opcional = explode("_",$chave);

					if(isset($opcional[0]) && $opcional[0] == "opcional"){
					
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
				
				//$this->_helper->redirector->gotoUrl("veiculos/edt/id/".$idVeiculos[0]['id']."/termino/1/msg/".$mensagem);
			
			}else{
			
				$this->view->mensagem = $mensagem;
			
			}
			
			
			

		}
	
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
		
			//echo $exception->getMessage();
			
			return "erro";
			
		}
		
		$arrAnu = get_object_vars($client->obterListaAnunciantes($token));
		$arrAnuci = get_object_vars($arrAnu['dados']);
		$idAnunciante = $arrAnuci['id'];

		$arr = get_object_vars($client->obterEstoqueAnunciante($token, $idAnunciante));
		
		$arrAnuncio = $arr['anuncios'];

		if($arrAnuncio){
		
			foreach($arrAnuncio as $keyV=>$anuncios){
		
				foreach($anuncios as $keyH=>$anuncio){
				
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
					
					}

				}
			
			}
		
		}

		return $arrStatus['status'];
		
	}

}
?>

