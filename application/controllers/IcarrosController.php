<?php

header("Content-Type: text/html; charset=UTF-8",true);

class IcarrosController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Icarros";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function testeAction(){
		
		
		
	}
	
	public function edtIcarrosBackgroundAction(){
		
		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
		
	}

	public function ajaxAction(){
	
		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
	
		if($this->_getParam('fn') == 'get_qtd_anuncios_icarros'){
	
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
			
			//echo count($arrAnuncio);
			
			if($arrAnuncio){

				if(count($arrAnuncio) > 1){
			
					foreach($arrAnuncio as $keyV=>$anuncios){

						$arrDados = get_object_vars($anuncios);
					
						$strJson .= $arrDados['id'].",".$arrDados['nome'].",".$arrDados['ativos'].",".$arrDados['disponiveis'].",";

					}
				
				}else{
				
					$arrDados = get_object_vars($arrAnuncio);
				
					$strJson = $arrDados['id'].",".$arrDados['nome'].",".$arrDados['ativos'].",".$arrDados['disponiveis'].",";
				
				}
				
				//var_export($strJson);
				
				echo substr($strJson,0,-1);
			
			}else{
			
				echo "erro";
			
			}

		}elseif($this->_getParam('fn') == 'get_modelos_fipe'){
		
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
			$dbVersoesIcarros = new Application_Model_DbTable_VersoesIcarros();
		
			$arrVeiculo = $dbVeiculos->getVeiculoEstoque($this->_getParam('id_veiculo'));
			
			$arrVersaoIcarros = $dbVersoesIcarros->getVersoesIcarrosPorFipe($arrVeiculo[0]['cod_fipe']);

			//if(!$arrVersaoIcarros[0]['fipe_id']){
			
				$arrModelosVersoes = $dbVersoesIcarros->getModelosVersoesIcarros($arrVeiculo[0]['marca'], $arrVeiculo[0]['ano_modelo']);
				
				foreach($arrModelosVersoes as $key=>$modelosVersoes){
				
					if($arrModelosVersoes[$key]['id'] != $arrModelosVersoes[$key+1]['id']){
				
						$strOptions .= "<option value='".$modelosVersoes['id']."'>".$modelosVersoes['nome']." - ".$arrVeiculo[0]['ano_modelo']."</option>";
				
					}
				
				}
		
				echo $strOptions;
				
			//}

		}elseif($this->_getParam('fn') == 'altera_icarros'){
		
			//////////////////////////////LOGA ICARROS////////////////////////////////////////////////
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
		
			$arrAnu = get_object_vars($client->obterListaAnunciantes($token));
			$arrAnuci = get_object_vars($arrAnu['dados']);
			$idAnunciante = $arrAnuci['id'];
			/////////////////////////////////FIM LOGAR ICARROS/////////////////////////////////////

			if($this->_getParam('id_plano') != "" && $this->_getParam('id_veiculo') != "" && $this->_getParam('name') != ""){
				
				$status = $this->edtIcarros($token, $idAnunciante, $client, $this->_getParam('id_veiculo'), $this->_getParam('name'), $this->_getParam('id_plano'), $this->_getParam('versao_id'));
			
				if($status == "OK"){
					
					//////////////////////////////BUSCA PLANOS ATUALIZADO/////////////////////////////////
					$arrPlanosPhp = $this->getPlanoIcarros($token, $idAnunciante, $client);
			
					foreach($arrPlanosPhp as $key=>$arrPlanos){
		
						$strPhp .= $arrPlanos['id'].":". $arrPlanos['nome'].":".$arrPlanos['ativos'].":". $arrPlanos['disponiveis']."|";
		
					}
	
					echo $this->_getParam('name')."/".substr($strPhp,0,-1);		
					//////////////////////////////////FIM PLANOS ATUALIZADO////////////////////////////////////////////
				
				}else{
					
					echo "ERRO|".$status;
				
				}
			
			}elseif($this->_getParam('id_plano') != "" && $this->_getParam('id_veiculo') != "" && $this->_getParam('name') == ""){
				
				$arr = $this->addIcarros($token, $idAnunciante, $client, $this->_getParam('id_veiculo'), $this->_getParam('id_plano'), $this->_getParam('versao_id'));
				//var_export($arr);

				//var_export($arr);
				
				$arrTemp = get_object_vars($arr['dados']);

				
				
				if($arr['status'] == "OK"){
					////ADD////
					
					if($this->_getParam('id_veiculo')){
					
						$dbVeiculos = new Application_Model_DbTable_Veiculos();
						
						$dados['icarros'] = 1;
						
						if($dbVeiculos->edt($this->_getParam('id_veiculo'), $dados)){
							
							//////////////////////////////BUSCA PLANOS ATUALIZADO/////////////////////////////////
							$arrPlanosPhp = $this->getPlanoIcarros($token, $idAnunciante, $client);
					
							foreach($arrPlanosPhp as $key=>$arrPlanos){
				
								$strPhp .= $arrPlanos['id'].":". $arrPlanos['nome'].":".$arrPlanos['ativos'].":". $arrPlanos['disponiveis']."|";
				
							}
			
							echo $arrTemp['id']."/".substr($strPhp,0,-1);		
							//////////////////////////////////FIM PLANOS ATUALIZADO////////////////////////////////////////////

						}else{
						
							echo "ERRO|"."Erro Inesperado!";
						
						}
					
					}
				
				}else{
					
					echo "ERRO|".$arr['descricao'];
				
				}
		
			}elseif($this->_getParam('id_plano') == "" && $this->_getParam('id_veiculo') != "" && $this->_getParam('name') != ""){
				
				$arr = get_object_vars($client->excluirAnuncio($token,$this->_getParam('name')));

				if($arr['status'] == "OK"){
					
					if($this->_getParam('id_veiculo')){
					
						$dbVeiculos = new Application_Model_DbTable_Veiculos();
						
						$dados['icarros'] = 0;
						
						$dbVeiculos->edt($this->_getParam('id_veiculo'), $dados);
				
						//////////////////////////////BUSCA PLANOS ATUALIZADO/////////////////////////////////
						$arrPlanosPhp = $this->getPlanoIcarros($token, $idAnunciante, $client);
						
						foreach($arrPlanosPhp as $key=>$arrPlanos){
					
							$strPhp .= $arrPlanos['id'].":". $arrPlanos['nome'].":".$arrPlanos['ativos'].":". $arrPlanos['disponiveis']."|";
					
						}
				
						echo "EXCLUIR/".substr($strPhp,0,-1);		
						//////////////////////////////////FIM PLANOS ATUALIZADO////////////////////////////////////////////

					}
			
				}else{
					
					echo "ERRO|".$arr['descricao'];
				
				}
			
			}else{

			
				//////////////////////////////BUSCA PLANOS ATUALIZADO/////////////////////////////////
				$arrPlanosPhp = $this->getPlanoIcarros($token, $idAnunciante, $client);
			
				foreach($arrPlanosPhp as $key=>$arrPlanos){
		
					$strPhp .= $arrPlanos['id'].":". $arrPlanos['nome'].":".$arrPlanos['ativos'].":". $arrPlanos['disponiveis']."|";
		
				}
	
				echo "CERTO/".substr($strPhp,0,-1);		
				//////////////////////////////////FIM PLANOS ATUALIZADO////////////////////////////////////////////
			
			}

		}
	
	}
	
	
	private function addIcarros($token, $idAnunciante, $client, $idVeiculo, $idPlano, $idVersao){
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		
		$arrVeiculos = $dbVeiculos->getVeiculoEstoque($idVeiculo);
		$add = $arrVeiculos[0];
		

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

		}elseif(strtolower($add['cor']) == "dourada"){

			$cor = 13;

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
	
		if($add['km'] == ""){
		
			$add['km'] = 1;
		
		}
		
		if($add['km'] == 0){
		
			$add['km'] = 1;
		
		}
	
		//////////////////////////////////////////

		$arrAnuncio['versao_id'] = $idVersao;
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
		$arrAnuncio['tipoAnuncio_id'] = $idPlano;
		$arrAnuncio['status'] = 1;
		$arrAnuncio['anuncio0km'] = false;
		$arrAnuncio['spotlight'] = false;
		$arrAnuncio['anunciante_id'] = $idAnunciante;

		$arr = get_object_vars($client->inserirAnuncio($token, $arrAnuncio));

		if($arr['status'] == "OK"){
			
			$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
			
			$arrTemp = get_object_vars($arr['dados']);
			$arrFotos = $dbFotosVeiculos->getFotosVeiculoIcarros($idVeiculo);
				
			if($arrFotos){
				
				foreach($arrFotos as $fotos){
					
					$filename =  $fotos['path'];
					$extensao = end(explode(".",$filename));
					$handle = fopen ($filename, "rb");
					$arrFotosBinario = fread ($handle, filesize ($filename));
					fclose($handle);
			
					$arr2[] = $client->inserirFoto($token, $arrTemp['id'], $extensao, $arrFotosBinario);
			
				}
				
			}
			
			return $arr;
			
		}else{
			
			//return $arr['descricao'];
			return $arr;
		
		}
		
	}
	
	
	private function edtIcarros($token, $idAnunciante, $client, $idVeiculo, $idAnuncio, $idPlano, $idVersao){
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		
		$arrVeiculos = $dbVeiculos->getVeiculoEstoque($idVeiculo);
		$edt = $arrVeiculos[0];
		

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
		
		if($edt['km'] == ""){
		
			$edt['km'] = 1;
		
		}
		
		if($edt['km'] == 0){
		
			$edt['km'] = 1;
		
		}
	
		//////////////////////////////////////////
	
		$arrAnuncio['id'] = $idAnuncio;
		$arrAnuncio['versao_id'] = $idVersao;
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
		$arrAnuncio['tipoAnuncio_id'] = $idPlano;
		$arrAnuncio['status'] = 1;
		$arrAnuncio['anuncio0km'] = false;
		$arrAnuncio['spotlight'] = false;
		$arrAnuncio['anunciante_id'] = $idAnunciante;

		$arr = get_object_vars($client->alterarAnuncio($token, $arrAnuncio));

		if($arr['status'] == "OK"){

			return "OK";
			
		}else{
			
			return $arr['descricao'];
		
		}
		
	}
	
	public function integracaoIcarros1Action(){
	
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
	
	public function estoqueCompartilhadoAction(){
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arr['venda'] = true;

		//////////////////LOGA ICARROS/////////////////////////////////////////////////

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
		/////////////////////////////////FIM LOGAR ICARROS/////////////////////////////////////

		
		$arrPlanos = $this->getPlanoIcarros($token, $idAnunciante, $client);
	
		if($arrPlanos['status'] != "Erro"){
		
			foreach($arrPlanos as $planos){
				
				$arrSelectPlano[$planos['id']]['nome'] = $planos['nome'];
				$arrSelectPlano[$planos['id']]['id'] = $planos['id'];
			
			}
			
			//var_export($arrSelectPlano);
		
			$arrEstoqueIcarros = $this->getEstoqueIcarros($token, $idAnunciante, $client);
			
			if($arrEstoqueIcarros['status'] != "Erro"){
			
				//var_export($arrEstoqueIcarros);

				$arrVeiculos = $dbVeiculos->getVeiculosCompleto($arr);

				foreach($arrVeiculos as $key=>$veiculo){
					
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
					
					$arrVeiculoCompleto[$key]['id'] = $veiculo['id'];
					$arrVeiculoCompleto[$key]['opcionais'] = $stringOpcional;
					$arrVeiculoCompleto[$key]['marca'] = $veiculo['marca'];
					$arrVeiculoCompleto[$key]['modelo'] = $veiculo['modelo'];
					$arrVeiculoCompleto[$key]['placa'] = $veiculo['placa'];
					$arrVeiculoCompleto[$key]['cor'] = $veiculo['cor'];
					$arrVeiculoCompleto[$key]['ano_fabricacao'] = $veiculo['ano_fabricacao'];
					$arrVeiculoCompleto[$key]['ano_modelo'] = $veiculo['ano_modelo'];
					$arrVeiculoCompleto[$key]['km'] = $veiculo['km'];
					$arrVeiculoCompleto[$key]['valor_venda'] = $veiculo['valor_venda'];
					$arrVeiculoCompleto[$key]['valor_aquisicao'] = $veiculo['valor_aquisicao'];
					$arrVeiculoCompleto[$key]['ativo'] = $veiculo['ativo'];
					$arrVeiculoCompleto[$key]['nome'] = $veiculo['nome'];
					$arrVeiculoCompleto[$key]['hora_alteracao'] = $veiculo['hora_alteracao'];
					$arrVeiculoCompleto[$key]['fotos'] = $arrFotos[0]['total'];
					$arrVeiculoCompleto[$key]['de'] = round(($veiculo['vendido'] == 0) ? $diferenca/86400 : 0,0);
					$arrVeiculoCompleto[$key]['vendido'] = $veiculo['vendido'];
					$arrVeiculoCompleto[$key]['consignado'] = $veiculo['consignado'];
					$arrVeiculoCompleto[$key]['descricao_site'] = $veiculo['descricao_site'];
					$arrVeiculoCompleto[$key]['cod_fipe'] = $veiculo['cod_fipe'];
					
					
					
					foreach($arrEstoqueIcarros as $estoqueIcarros){
						
						if(strtolower($estoqueIcarros['placa']) == strtolower(str_replace("-","",$veiculo['placa']))){
							
							$arrVeiculoCompleto[$key]['id_plano_icarros'] = "<option selected='selected' value='".$arrSelectPlano[$estoqueIcarros['tipoAnuncio_id']]['id']."'>".$arrSelectPlano[$estoqueIcarros['tipoAnuncio_id']]['nome']."</option>";
							$arrVeiculoCompleto[$key]['id_icarros'] = $estoqueIcarros['id'];
							$arrVeiculoCompleto[$key]['versao_id'] = $estoqueIcarros['versao_id'];
							break;
						
						}
						
					}
					
				}
				
				$this->view->veiculos = $arrVeiculoCompleto;
				$this->view->planos = $arrPlanos;
				$this->view->stoqueIcarros = $arrEstoqueIcarros;
			
			}else{
			
				$this->view->mensagem = $arrEstoqueIcarros['descricao'];
			
			}
			
		}else{
			
			$this->view->mensagem = $arrPlanos['descricao'];
		
		}
	
	}
	
	public function webmotorsAction(){
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arr['venda'] = true;

		//////////////////LOGA ICARROS/////////////////////////////////////////////////

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
		/////////////////////////////////FIM LOGAR ICARROS/////////////////////////////////////

		if($arrEmpresa[0]['login_webmotors'] && $arrEmpresa[0]['senha_webmotors']){
		
			$arrEstoqueWeb = $this->getEstoqueWebmotors($arrEmpresa[0]['cnpj'], $arrEmpresa[0]['login_webmotors'], $arrEmpresa[0]['senha_webmotors']);

		}

		//var_export($arrEmpresa[0]['cnpj']);
		
		//var_export($idAnunciante);
	
		$arrPlanos = $this->getPlanoIcarros($token, $idAnunciante, $client);
	
		
	
		if($arrPlanos['status'] != "Erro"){
		
			foreach($arrPlanos as $planos){
				
				$arrSelectPlano[$planos['id']]['nome'] = $planos['nome'];
				$arrSelectPlano[$planos['id']]['id'] = $planos['id'];
			
			}
			
			//var_export($arrSelectPlano);
		
			$arrEstoqueIcarros = $this->getEstoqueIcarros($token, $idAnunciante, $client);
			
			
			
			if($arrEstoqueIcarros['status'] != "Erro"){
			
				//var_export($arrEstoqueIcarros);

				$arrVeiculos = $dbVeiculos->getVeiculosCompleto($arr);

				foreach($arrVeiculos as $key=>$veiculo){
					
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
					
					$arrVeiculoCompleto[$key]['id'] = $veiculo['id'];
					$arrVeiculoCompleto[$key]['opcionais'] = $stringOpcional;
					$arrVeiculoCompleto[$key]['marca'] = $veiculo['marca'];
					$arrVeiculoCompleto[$key]['modelo'] = $veiculo['modelo'];
					$arrVeiculoCompleto[$key]['placa'] = $veiculo['placa'];
					$arrVeiculoCompleto[$key]['cor'] = $veiculo['cor'];
					$arrVeiculoCompleto[$key]['ano_fabricacao'] = $veiculo['ano_fabricacao'];
					$arrVeiculoCompleto[$key]['ano_modelo'] = $veiculo['ano_modelo'];
					$arrVeiculoCompleto[$key]['km'] = $veiculo['km'];
					$arrVeiculoCompleto[$key]['valor_venda'] = $veiculo['valor_venda'];
					$arrVeiculoCompleto[$key]['valor_aquisicao'] = $veiculo['valor_aquisicao'];
					$arrVeiculoCompleto[$key]['ativo'] = $veiculo['ativo'];
					$arrVeiculoCompleto[$key]['nome'] = $veiculo['nome'];
					$arrVeiculoCompleto[$key]['hora_alteracao'] = $veiculo['hora_alteracao'];
					$arrVeiculoCompleto[$key]['fotos'] = $arrFotos[0]['total'];
					$arrVeiculoCompleto[$key]['de'] = round(($veiculo['vendido'] == 0) ? $diferenca/86400 : 0,0);
					$arrVeiculoCompleto[$key]['vendido'] = $veiculo['vendido'];
					$arrVeiculoCompleto[$key]['consignado'] = $veiculo['consignado'];
					$arrVeiculoCompleto[$key]['descricao_site'] = $veiculo['descricao_site'];
					$arrVeiculoCompleto[$key]['cod_fipe'] = $veiculo['cod_fipe'];
					
					
					foreach($arrEstoqueIcarros as $estoqueIcarros){
						
						if(strtolower($estoqueIcarros['placa']) == strtolower(str_replace("-","",$veiculo['placa']))){
							
							$arrVeiculoCompleto[$key]['id_plano_icarros'] = "<option selected='selected' value='".$arrSelectPlano[$estoqueIcarros['tipoAnuncio_id']]['id']."'>".$arrSelectPlano[$estoqueIcarros['tipoAnuncio_id']]['nome']."</option>";
							$arrVeiculoCompleto[$key]['id_icarros'] = $estoqueIcarros['id'];
							$arrVeiculoCompleto[$key]['versao_id'] = $estoqueIcarros['versao_id'];
							break;
						
						}
						
					}
					
					if($arrEmpresa[0]['login_webmotors'] && $arrEmpresa[0]['senha_webmotors'] && $arrEstoqueWeb){
					
						foreach($arrEstoqueWeb as $estoqueWeb){
							
							if(strtolower($estoqueWeb['placa']) == strtolower(str_replace("-","",$veiculo['placa']))){
								
								$arrVeiculoCompleto[$key]['id_modalidade_webmotors'] = "<option selected='selected' value='".$estoqueWeb['codigo_modalidade']."'>Aguarde...</option>";
								$arrVeiculoCompleto[$key]['id_anuncio_webmotors'] = $estoqueWeb['codigo_anuncio'];
								break;
							
							}
							
						}
					
					}
					
				}
				
				$this->view->veiculos = $arrVeiculoCompleto;
				$this->view->planos = $arrPlanos;
				$this->view->stoqueIcarros = $arrEstoqueIcarros;
			
			}else{
			
				$this->view->mensagem = $arrEstoqueIcarros['descricao'];
			
			}
			
		}else{
			
			$this->view->mensagem = $arrPlanos['descricao'];
		
		}
		
	}
	
	
	public function webmotors2Action(){
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arr['venda'] = true;

		//////////////////LOGA ICARROS/////////////////////////////////////////////////

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
		/////////////////////////////////FIM LOGAR ICARROS/////////////////////////////////////


		if($arrEmpresa[0]['login_webmotors'] && $arrEmpresa[0]['senha_webmotors']){
		
			$arrEstoqueWeb = $this->getEstoqueWebmotors($arrEmpresa[0]['cnpj'], $arrEmpresa[0]['login_webmotors'], $arrEmpresa[0]['senha_webmotors']);

		}
		
		$arrPlanos = $this->getPlanoIcarros($token, $idAnunciante, $client);
	
		if($arrPlanos['status'] != "Erro"){
		
			foreach($arrPlanos as $planos){
				
				$arrSelectPlano[$planos['id']]['nome'] = $planos['nome'];
				$arrSelectPlano[$planos['id']]['id'] = $planos['id'];
			
			}
			
			//var_export($arrSelectPlano);
		
			$arrEstoqueIcarros = $this->getEstoqueIcarros($token, $idAnunciante, $client);
			
			
			if($arrEstoqueIcarros['status'] != "Erro"){
			
				//var_export($arrEstoqueIcarros);

				$arrVeiculos = $dbVeiculos->getVeiculosCompleto($arr);

				foreach($arrVeiculos as $key=>$veiculo){
					
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
					
					$arrVeiculoCompleto[$key]['id'] = $veiculo['id'];
					$arrVeiculoCompleto[$key]['opcionais'] = $stringOpcional;
					$arrVeiculoCompleto[$key]['marca'] = $veiculo['marca'];
					$arrVeiculoCompleto[$key]['modelo'] = $veiculo['modelo'];
					$arrVeiculoCompleto[$key]['placa'] = $veiculo['placa'];
					$arrVeiculoCompleto[$key]['cor'] = $veiculo['cor'];
					$arrVeiculoCompleto[$key]['ano_fabricacao'] = $veiculo['ano_fabricacao'];
					$arrVeiculoCompleto[$key]['ano_modelo'] = $veiculo['ano_modelo'];
					$arrVeiculoCompleto[$key]['km'] = $veiculo['km'];
					$arrVeiculoCompleto[$key]['valor_venda'] = $veiculo['valor_venda'];
					$arrVeiculoCompleto[$key]['valor_aquisicao'] = $veiculo['valor_aquisicao'];
					$arrVeiculoCompleto[$key]['ativo'] = $veiculo['ativo'];
					$arrVeiculoCompleto[$key]['nome'] = $veiculo['nome'];
					$arrVeiculoCompleto[$key]['hora_alteracao'] = $veiculo['hora_alteracao'];
					$arrVeiculoCompleto[$key]['fotos'] = $arrFotos[0]['total'];
					$arrVeiculoCompleto[$key]['de'] = round(($veiculo['vendido'] == 0) ? $diferenca/86400 : 0,0);
					$arrVeiculoCompleto[$key]['vendido'] = $veiculo['vendido'];
					$arrVeiculoCompleto[$key]['consignado'] = $veiculo['consignado'];
					$arrVeiculoCompleto[$key]['descricao_site'] = $veiculo['descricao_site'];
					$arrVeiculoCompleto[$key]['cod_fipe'] = $veiculo['cod_fipe'];
					
					
					foreach($arrEstoqueIcarros as $estoqueIcarros){
						
						if(strtolower($estoqueIcarros['placa']) == strtolower(str_replace("-","",$veiculo['placa']))){
							
							$arrVeiculoCompleto[$key]['id_plano_icarros'] = "<option selected='selected' value='".$arrSelectPlano[$estoqueIcarros['tipoAnuncio_id']]['id']."'>".$arrSelectPlano[$estoqueIcarros['tipoAnuncio_id']]['nome']."</option>";
							$arrVeiculoCompleto[$key]['id_icarros'] = $estoqueIcarros['id'];
							$arrVeiculoCompleto[$key]['versao_id'] = $estoqueIcarros['versao_id'];
							break;
						
						}
						
					}
					
					if($arrEmpresa[0]['login_webmotors'] && $arrEmpresa[0]['senha_webmotors']){
					
						foreach($arrEstoqueWeb as $estoqueWeb){
							
							if(strtolower($estoqueWeb['placa']) == strtolower(str_replace("-","",$veiculo['placa']))){
								
								$arrVeiculoCompleto[$key]['id_modalidade_webmotors'] = "<option selected='selected' value='".$estoqueWeb['codigo_modalidade']."'>Aguarde...</option>";
								$arrVeiculoCompleto[$key]['id_anuncio_webmotors'] = $estoqueWeb['codigo_anuncio'];
								break;
							
							}
							
						}
					
					}
					
				}
				
				$this->view->veiculos = $arrVeiculoCompleto;
				$this->view->planos = $arrPlanos;
				$this->view->stoqueIcarros = $arrEstoqueIcarros;
			
			}else{
			
				$this->view->mensagem = $arrEstoqueIcarros['descricao'];
			
			}
			
		}else{
			
			$this->view->mensagem = $arrPlanos['descricao'];
		
		}
	
	}
	
	
	private function getEstoqueIcarros($token, $idAnunciante, $client){
		
		$arr = get_object_vars($client->obterEstoqueAnunciante($token, $idAnunciante));

		if($arr['status'] == "OK"){

			$arrAnuncio = $arr['anuncios'];
			
			foreach($arrAnuncio as $arAnuncio){
				
				$arrAnuncios[] = get_object_vars($arAnuncio);

			}
		
			return $arrAnuncios;
	
		}else{
			
			return $arr;
		
		}
	
	}
	
	private function getPlanoIcarros($token, $idAnunciante, $client){

		$arr = get_object_vars($client->obterTiposAnuncioAnunciante($token, $idAnunciante));

		if($arr['status'] == "OK"){

			$arrAnuncio = $arr['tiposAnuncios'];
		
			if(count($arrAnuncio) > 1){
			
				$arrAnuncio = $arr['tiposAnuncios'];
				
				foreach($arrAnuncio as $keyV=>$anuncios){

					$arrDados = get_object_vars($anuncios);

					$arrPlanos[] = $arrDados;
					//$arrPlanos[]['tipo_anuncio'] = $arrDados;
	

				}
		
				
	
			}else{
				
				$arrDados = get_object_vars($arrAnuncio);
				$arrPlanos[] = $arrDados;
			
			}
			
			return $arrPlanos;
			
		}else{

			return $arr;
			
		}
		
	
	}
	
	
	public function integracaoIcarrosAction(){
	
		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
   
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
		
		$arr = $client->obterEstoqueAnunciante($token, $idAnunciante);
		/*
		foreach($arrAnuci as $anu){
		
			var_export(get_object_vars($anu));
			echo "<br><br>";
		
		}
		*/
		var_export($arr);

	}
	
	private function getEstoqueWebmotors($cnpj, $login, $senha){
	
		try{
	
			$url = "http://www.webmotors.com.br/IntegracaoRevendedor/wsLoginSistemaRevendedor.asmx?wsdl";

			$clientWeb = new SoapClient($url,array('trace' => 1, 'soap_version'=> SOAP_1_2));

			$tokenWeb = $clientWeb->autenticar(array("cnpj"=>$cnpj, "email"=>$login, "senha"=>$senha));
			//$tokenWeb = $clientWeb->autenticar(array("cnpj"=>"12.303.094/0001-57", "email"=>"contato@selectveiculos.com.br", "senha"=>"mwzfwq"));

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
				$arrWebEstoque[0]['placa'] = $arWebEstoque['Placa'];
			
			
			}else{
	
				foreach($arrEstoqueWeb3 as $key=>$web){
				
					$arWebEstoque = get_object_vars($web);
					
					$arrWebEstoque[$key]['codigo_anuncio'] = $arWebEstoque['CodigoAnuncio'];
					$arrWebEstoque[$key]['codigo_modalidade'] = $arWebEstoque['CodigoModalidade'];
					$arrWebEstoque[$key]['placa'] = $arWebEstoque['Placa'];

				}
			
			}

			return $arrWebEstoque;

		}catch (SoapFault $exception){
	
			//var_export($senha);
			echo $exception->getMessage();
		
		}

	}

}

?>
