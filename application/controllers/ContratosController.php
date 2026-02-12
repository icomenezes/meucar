<?php

header("Content-Type: text/html; charset=UTF-8",true);

class ContratosController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Contratos";

		Zend_Session::start();
		
	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function ajaxAction(){

		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');

		if($this->_getParam('fn') == 'busca_contratos'){
		
			$dbContratosRecibos = new Application_Model_DbTable_ContratosRecibos();
		
			$dadoGet['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			
			$arrContratos = $dbContratosRecibos->_get($dadoGet);
			
			$strContrato = "<table id='table_contratos' class='table'><tr><th colspan='2'>Clique no contrato</th></tr>";
			
			foreach($arrContratos as $contrato){
			
				$strContrato .= "<tr><td>".$contrato['nome']."</td><td><input type='button' value='Gerar Contrato' onclick='geraContrato(".$contrato['id'].")'></td></tr>";
			
			}
			
			echo $strContrato."</table>";
		
		}
		
	}
	
	public function selecionaContratoAction(){
	
		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
		
		$dbContratosRecibos = new Application_Model_DbTable_ContratosRecibos();
		
		$dadoGet['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$dadoGet['id_root'] = 1;

		$this->view->arrContratos = $dbContratosRecibos->_get($dadoGet);
		$this->view->idCliente = $this->_getParam('id_cliente');
		$this->view->idVeiculo = $this->_getParam('id_veiculo');
		$this->view->idEmpresa = $this->_getParam('id_empresa');
		$this->view->idNegociacao = $this->_getParam('id_negociacao');
		
	
	}
	
	public function geraContratoAction(){

		$dbContratosRecibos = new Application_Model_DbTable_ContratosRecibos();
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$dbClientes = new Application_Model_DbTable_Clientes();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
		$dbRecebimentoNegociacoes = new Application_Model_DbTable_RecebimentosNegociacoes();
		
		$arrRecebimentos = $dbRecebimentoNegociacoes->getRecebimentos($this->_getParam('id_negociacao'));
		
		$arrNegociacoes = $dbNegociacoes->getNegociacoes($this->_getParam('id_negociacao'));
		
		$arr['id'] = $this->_getParam('id_veiculo');
		
		$arrVeiculos = $dbVeiculos->_get($arr);


		$arrVeiculoTroca = $dbVeiculos->getVeiculoTrocaNegociacao($_SESSION['sessionUser']['id_empresa'], $this->_getParam('id_negociacao'));
		
		$arrOpcionaisVeiculos = $dbOpcionaisVeiculos->getVeiculosOpcionais($arrVeiculos[0]['id']);

		if(isset($arrVeiculoTroca[0]['id'])){
			$arrOpcionaisVeiculoTroca = $dbOpcionaisVeiculos->getVeiculosOpcionais($arrVeiculoTroca[0]['id']);
		}
		
		$arrEmpresas = $dbEmpresas->getEmpresa($this->_getParam('id_empresa'));

		$opcion = "";
		$conteudo = "";
		$strRecebimentos = "";
		
		foreach($arrOpcionaisVeiculos as $key=>$opcionais){
		  
			$opcional = explode(")",$opcionais['opcional']);
		
			if(isset($this->arrOpcionais[$key+1]['opcional'])){
		
				$opcion .= $opcional[1].".";
		
			}else{
	
				$opcion .= $opcional[1].", ";
		
			}
	
		}

		if(isset($arrOpcionaisVeiculoTroca)){

			$opcionTroca = "";
			foreach($arrOpcionaisVeiculoTroca as $key=>$opcionais){
			  
				$opcionalTroca = explode(")",$opcionais['opcional']);
			
				if(isset($this->arrOpcionais[$key+1]['opcional'])){
			
					$opcionTroca .= $opcionalTroca[1].".";
			
				}else{
		
					$opcionTroca .= $opcionalTroca[1].", ";
			
				}
		
			}

		}

		$dadoGet['id'] = $this->_getParam('id_contrato');

		$arrContratos = $dbContratosRecibos->_get($dadoGet);
		
		$arrCliente = $dbClientes->fetchAll("id = " . $this->_getParam('id_cliente'));
		
		$texto =  $arrContratos[0]['conteudo'];
		
		$tag = false;
		
		for($i=0;$i<=strlen($texto);$i++){
			
			if(isset($texto{$i})){

				if($tag == false){
					
					$tags ="";
					
					if($texto{$i} == "{" && $texto{$i+1} == "["){
					
						$tag = true;
					
					}else{
					
						$conteudo .= $texto{$i};
					
					}
				
				}else{
				
					if($texto{$i} != "[" && $texto{$i} != "}" && $texto{$i} != "]"){
				
						$tags .= $texto{$i};
					
					}
				
					if($texto{$i} == "}" && $texto{$i-1} == "]"){
					
						$arrTag = explode("-",$tags);
						
						if(end($arrTag) == "veiculo"){
						
							if($arrTag[0] == "opcionais"){
							
								$conteudo .= $opcion;
								
							}elseif($arrTag[0] == "valor_venda"){
							
								$valorEscrito = $this->valorExtenso($arrNegociacoes[0]['valor_venda']);
							
								$conteudo .= "R$ ".money_format("%i",$arrNegociacoes[0]['valor_venda'])." (".$valorEscrito.") ";
							
							
							}elseif($arrTag[0] == "valor_aquisicao"){
							
								$valorEscrito = $this->valorExtenso($arrVeiculos[0]['valor_aquisicao']);
							
								$conteudo .= "R$ ".money_format("%i",$arrVeiculos[0]['valor_aquisicao'])." (".$valorEscrito.") ";

							}elseif($arrTag[0] == "forma_de_pagamento"){
							
								if($arrRecebimentos[0]['data'] == @date("Y-m-d") && $arrRecebimentos[0]['valor'] == $arrNegociacoes[0]['valor_venda']){
								
									$strRecebimentos .="À vista R$ ".money_format("%i",$arrRecebimentos[0]['valor'])." (".$this->valorExtenso($arrRecebimentos[0]['valor']).") em ".$this->formaDePagamento($arrRecebimentos[0]['forma']).", ";

							
									if($arrRecebimentos[0]['numero'] != "" && $arrRecebimentos[0]['numero'] != 0){
							
										$strRecebimentos .= "número ".$arrRecebimentos[0]['numero'].", ";
								
									}
							
									if($arrRecebimentos[0]['banco'] != ""){
								
										$strRecebimentos .= "banco ".$arrRecebimentos[0]['banco'].", ";
							
									}
								
									if($arrRecebimentos[0]['agencia'] != ""){
								
										$strRecebimentos .= "agência ".$arrRecebimentos[0]['agencia'].", ";
								
									}
							
									if($arrRecebimentos[0]['cc'] != ""){
									
										$strRecebimentos .= "conta corrente ".$arrRecebimentos[0]['cc'].", ";
									
									}
								
									if($arrRecebimentos[0]['data'] != "" && $arrRecebimentos[0]['forma'] == 3){
								
										$strRecebimentos .= "em ".implode("/",array_reverse(explode("-",$arrRecebimentos[0]['data']))).", ";
								
									}else{
									
										$strRecebimentos .= "com data para ".implode("/",array_reverse(explode("-",$arrRecebimentos[0]['data']))).", ";
									
									}

									// foreach($arrRecebimentos as $recebimento){
									// 	if($recebimento['forma'] == 12) {
									// 		$strRecebimentos .=" e R$ ".money_format("%i",$recebimento['valor'])." (".$this->valorExtenso($recebimento['valor']).") de ".$this->formaDePagamento($recebimento['forma']).", ";
									// 	}
									// }
									
									$strRecebimentos = substr($strRecebimentos,0,-2).".";
								
								}else{
								
									$entrada = false;
									
									$arrTroca = $dbNegociacoes->getTroca($this->_getParam('id_negociacao'));


									if(count($arrTroca) > 0){
										
										$contTroca = 0;
										
										foreach($arrTroca as $trocas){
											
											$contTroca++;

											$trocaMarca = "";
											$trocaKm = "";
											$trocaModelo = "";
											$ano = "";
											$cor = "";
											$placa = "";
											$combustivel = "";
											$chassi = "";
											$renavam = "";

											if(isset($trocas['marca'])){
												$trocaMarca = "Marca: ".$trocas['marca'];
											}
											if(isset($trocas['km'])){
												$trocaKm = ", km atual: ".$trocas['km'];
											}
											if(isset($trocas['modelo'])){
												$trocaModelo = ", modelo: ".$trocas['modelo'];
											}
											if(isset($trocas['ano_fabricacao'])){
												$ano = ", ano de fabricação/modelo: ".$trocas['ano_fabricacao']."/".$trocas['ano_modelo'];
											}
											if(isset($trocas['cor'])){
												$cor = ", cor: ".$trocas['cor'];
											}
											if(isset($trocas['placa'])){
												$placa = ", placa: ".$trocas['placa'];
											}
											if(isset($trocas['combustivel'])){
												$combustivel = ", combustível: ".$trocas['combustivel'];
											}
											if(isset($trocas['chassi'])){
												$chassi = ", chassi: ".$trocas['chassi'];
											}
											if(isset($trocas['renavam'])){
												$renavam = ", renavam: ".$trocas['renavam'];
											}
											
											if($contTroca == 1){
												$veiPlural = "<b>Veículo objeto da troca</b>";
												if(count($arrTroca) > 1){
													$veiPlural = "<b>Veículos objetos da troca</b>";
												}
												$strRecebimentos .= $veiPlural.": ".$trocaMarca.$trocaKm.$trocaModelo.$ano.$cor.$placa.$combustivel.$chassi.$renavam.", valor R$ ".money_format("%i",$trocas['valor_aquisicao'])." (".$this->valorExtenso($trocas['valor_aquisicao'])."), "; 
											
											}else{
											
												$strRecebimentos .= "e o veículo: ".$trocaMarca.$trocaKm.$trocaModelo.$ano.$cor.$placa.$combustivel.$chassi.$renavam.", valor R$ ".money_format("%i",$trocas['valor_aquisicao'])." (".$this->valorExtenso($trocas['valor_aquisicao'])."), "; 
											
											}
											
										}
									
										$entrada = true;
									
									}
								
									foreach($arrRecebimentos as $recebimento){
									
										if($recebimento['data'] == @date("Y-m-d") && $entrada == false){
										
											$strRecebimentos .= "Entrada de: R$ ".money_format("%i",$recebimento['valor'])." (".$this->valorExtenso($recebimento['valor']).") em ".$this->formaDePagamento($recebimento['forma']).", ";
											
											if($recebimento['numero'] != "" && $recebimento['numero'] != 0){
											
												$strRecebimentos .= "número ".$recebimento['numero'].", ";
											
											}
											
											if($recebimento['banco'] != ""){
											
												$strRecebimentos .= "banco ".$recebimento['banco'].", ";
											
											}
											
											if($recebimento['agencia'] != ""){
											
												$strRecebimentos .= "agência ".$recebimento['agencia'].", ";
											
											}
											
											if($recebimento['cc'] != ""){
											
												$strRecebimentos .= "conta corrente ".$recebimento['cc'].", ";
											
											}
											
											$entrada = true;
										
										}else{
										
											if($entrada){
												
												if($recebimento['forma'] == 12) {
													$strRecebimentos .= "menos ";
												}else{
													$strRecebimentos .= "mais ";
												}
												
											}

											if($recebimento['forma'] == 12) {
												$em = "de ";
											}else{
												$em = "em ";
											}
										
											$strRecebimentos .="R$ ".money_format("%i",$recebimento['valor'])." (".$this->valorExtenso($recebimento['valor']).") ".$em." ".$this->formaDePagamento($recebimento['forma']).", ";
											
											if($recebimento['numero'] != "" && $recebimento['numero'] != 0){
											
												$strRecebimentos .= "número ".$recebimento['numero'].", ";
											
											}
											
											if($recebimento['banco'] != ""){
											
												$strRecebimentos .= "banco ".$recebimento['banco'].", ";
											
											}
											
											if($recebimento['agencia'] != ""){
											
												$strRecebimentos .= "agência ".$recebimento['agencia'].", ";
											
											}
											
											if($recebimento['cc'] != ""){
											
												$strRecebimentos .= "conta corrente ".$recebimento['cc'].", ";
											
											}
											
											if($recebimento['data'] != ""){

												if($recebimento['forma'] != 12) {
											
													$strRecebimentos .= "com data para ".implode("/",array_reverse(explode("-",$recebimento['data']))).", ";

												}
											
											}
											
											$entrada = true;
										}
									
									}
									
									$strRecebimentos = substr($strRecebimentos,0,-2).".";
									
									if($arrNegociacoes[0]['valor_financiado'] != 0){
									
										$dbFinanceiras = new Application_Model_DbTable_Financeiras();
										
										$arr['id'] = $arrNegociacoes[0]['id_financeira'];
										
										$financeira = $dbFinanceiras->_get($arr);
						
										$strRecebimentos .= "<br>Financiamento de ".money_format("%i",$arrNegociacoes[0]['valor_financiado'])." (".$this->valorExtenso($arrNegociacoes[0]['valor_financiado']).") pagos em ".$arrNegociacoes[0]['numero_prestacoes']."xR$ ".money_format("%i",$arrNegociacoes[0]['valor_prestacoes']).",";
									
										$strRecebimentos .= "<b> ".$financeira[0]['nome']." ".$financeira[0]['tel'].".</b>";
										
									}
								
								}
							
								$conteudo .= $strRecebimentos;
								
							}elseif($arrTag[0] == "obs"){	
								
								$conteudo .= $arrNegociacoes[0]['obs'];
							
							}else{
							
								if($arrTag[0] == "modelo"){
								
									if($arrVeiculos[0]['descricao_site']){
									
										$conteudo .= $arrVeiculos[0]['descricao_site'];
									
									}else{
							
										$conteudo .= $arrVeiculos[0][$arrTag[0]];
									
									}
									
								}else{
								
									$conteudo .= $arrVeiculos[0][$arrTag[0]];
								
								}
								
							}
						
						}elseif(end($arrTag) == "troca"){


							if(isset($arrVeiculoTroca)){


								if($arrTag[0] == "opcionais"){
									$conteudo .= $opcionTroca;
								}


								if($arrTag[0] == "modelo"){
								
									if($arrVeiculoTroca[0]['descricao_site']){
									
										$conteudo .= $arrVeiculoTroca[0]['descricao_site'];
									
									}else{
							
										$conteudo .= $arrVeiculoTroca[0][$arrTag[0]];
									
									}
									
								}else{
								
									$conteudo .= $arrVeiculoTroca[0][$arrTag[0]];
								
								}

							}

						}elseif(end($arrTag) == "cliente"){
						
							if($arrTag[0] == "profissao"){
							
								$conteudo .= $arrCliente[0]['cargo'];
							
							}elseif($arrTag[0] == "telefone"){
								
								$conteudo .= $arrCliente[0]['tel1']." ".$arrCliente[0]['tel2']." ".$arrCliente[0]['cel'];
							
							}elseif($arrTag[0] == "estado_civil"){
							
								if($arrCliente[0][$arrTag[0]] == 0){
								
									$conteudo .= "Solteiro";
								
								}elseif($arrCliente[0][$arrTag[0]] == 1){
								
									$conteudo .= "Casado";
								
								}elseif($arrCliente[0][$arrTag[0]] == 2){
								
									$conteudo .= "Divorciado";
								
								}elseif($arrCliente[0][$arrTag[0]] == 3){
								
									$conteudo .= "Viuvo";
								
								}
							
							}elseif($arrTag[0] == "complemento_endereco"){

								$conteudo .= $arrCliente[0]['complemento'];

							}elseif($arrTag[0] == "data_nascimento"){
							
								$conteudo .= implode("/", array_reverse(explode("-", $arrCliente[0]['nascimento'])));
							
							}else{
								
								$conteudo .= $arrCliente[0][$arrTag[0]];
							
							}
							
						}elseif(end($arrTag) == "concessionaria"){
						
							if($arrTag[0] == "telefone"){
								
								$conteudo .= $arrEmpresas[0]['tel1']." ".$arrEmpresas[0]['tel2']." ".$arrEmpresas[0]['tel3'];
						
							}elseif($arrTag[0] == "ano_atual"){
							
								$conteudo .= @date("Y");
						
							}else{
							
								$conteudo .= $arrEmpresas[0][$arrTag[0]];
							
							}
						
						}

						$tag = false;
						
					}
				
				}
				
			}
		
		}
		
		$this->view->conteudo = $conteudo;

	}
	
	public function termoAction(){
	
		$dbContratosRecibos = new Application_Model_DbTable_ContratosRecibos();
		$dadoGet['id'] = 11;
		$arrContratos = $dbContratosRecibos->_get($dadoGet);
		
		$this->view->conteudo = $arrContratos[0]['conteudo'];
	
	}
	
	public function addAction(){
		
		if($this->getRequest()->isPost()){
		
			$dados['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$dados['nome'] = $_POST['nome_contrato'];
			$dados['conteudo'] = $_POST['conteudo'];
			$dados['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dados['hora_alteracao'] = @date("Y/m/d H:i:s");
			
			$dbContratosRecibos = new Application_Model_DbTable_ContratosRecibos();
			
			if($dbContratosRecibos->add($dados)){
			
				$this->view->mensagem = "Contrato cadastrado com sucesso!";
			
			}else{
			
				$this->view->mensagem = "Erro ao cadastrar contrato!";
			
			}

		}
		
	}
	
	public function listaAction(){
	
		$dbContratosRecibos = new Application_Model_DbTable_ContratosRecibos();
		
		$dadoGet['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$this->view->contratos = $dbContratosRecibos->_get($dadoGet);
	
	}
	
	public function delAction(){
	
		$dbContratosRecibos = new Application_Model_DbTable_ContratosRecibos();

		if($dbContratosRecibos->del($this->_getParam('id'))){
		
			$this->_helper->redirector->gotoUrl("contratos/lista");
		
		}else{
		
			$this->_helper->redirector->gotoUrl("contratos/lista");
		
		}
	
	}
	
	public function edtAction(){
	
		$this->validaAcesso('gerenciar_modelos_de_veiculos');
		
		$dadoGet['id'] = $this->_getParam('id');
		
		$dbContratosRecibos = new Application_Model_DbTable_ContratosRecibos();
		
		$dados = $dbContratosRecibos->_get($dadoGet);
		
		$this->view->contratos = $dados[0];

		if($this->getRequest()->isPost()){
		
			$idContrato = $_POST['id'];
			$dadosPost['nome'] = $_POST['nome_contrato'];
			$dadosPost['conteudo'] = $_POST['conteudo'];
			$dadosPost['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dadosPost['hora_alteracao'] = @date("Y/m/d H:i:s");
		
			if($dbContratosRecibos->edt($idContrato,$dadosPost)){
			
				$this->_helper->redirector->gotoUrl("contratos/lista");
				
			}else{

				$this->view->mensagem = "Erro ao editar contrato ou n&atilde;o houve altera&ccedil;&atilde;o dos dados!";
			
			}
		
		}
	
	}
	
	public function atualizaAction(){
	
		$dbModelos = new Application_Model_DbTable_Modelos();
		
		$dbModelos->atualizaModelos();
	
	}
	
	
	private function valorExtenso($valor=0, $maiusculas=false){

		$rt = "";
	
		$valor = str_replace("-","",$valor);
	
		// verifica se tem virgula decimal
		if (strpos($valor,",") > 0){
		  // retira o ponto de milhar, se tiver
		  $valor = str_replace(".","",$valor);
	 
		  // troca a virgula decimal por ponto decimal
		  $valor = str_replace(",",".",$valor);
		}
		$singular = array("centavo", "real", "mil", "milh&atilde;o", "bilh&atilde;o", "trilh&atilde;o", "quatrilh&atilde;o");
		$plural = array("centavos", "reais", "mil", "milh&otilde;es", "bilh&otilde;es", "trilh&otilde;es","quatrilh&otilde;es");
 
		$c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
		$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
		$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
		$u = array("", "um", "dois", "tr&ecirc;s", "quatro", "cinco", "seis","sete", "oito", "nove");
 
        $z=0;
 
        $valor = number_format($valor, 2, ".", ".");
        $inteiro = explode(".",$valor);
		
		$cont=count($inteiro);
		
		for($i=0;$i<$cont;$i++)
            for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
                $inteiro[$i] = "0".$inteiro[$i];
 
				$fim = $cont - ($inteiro[$cont-1] > 0 ? 1 : 2);
        for ($i=0;$i<$cont;$i++) {
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
 
            $r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd &&$ru) ? " e " : "").$ru;
            $t = $cont-1-$i;
            $r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            if ($valor == "000")$z++; elseif ($z > 0) $z--;
            if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t];
            if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) &&($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }
 
        if(!$maiusculas){
		 
			return($rt ? $rt : "zero");
         
		}elseif($maiusculas == "2"){
		
			return (strtoupper($rt) ? strtoupper($rt) : "Zero");
        
		}else{
		
			return (ucwords($rt) ? ucwords($rt) : "Zero");
       
		}
 
	}
	
	private function formaDePagamento($num){
	
		if($num == 1){
		
			return "cartão de crédito";
		
		}elseif($num == 2){
		
			return "cheque";
			
		}elseif($num == 3){
		
			return "dinheiro";
			
		}elseif($num == 4){
		
			return "promissória";
			
		}elseif($num == 5){
		
			return "TED";
			
		}elseif($num == 6){
		
			return "devolução ao cliente";
			
		}elseif($num == 7){
		
			return "débitos do veículo de troca";
			
		}elseif($num == 8){
		
			return "quitação de veículo de troca";
		
		}elseif($num == 9){
		
			return "cartão de débito";
		
		}elseif($num == 10){
		
			return "boleto bancário";
		
		}elseif($num == 11){
		
			return "Pix";
		
		}elseif($num == 12){
		
			return "Desconto";
		
		}
	
	}

}

?>
