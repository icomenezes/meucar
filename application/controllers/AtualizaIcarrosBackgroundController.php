<?php

header("Content-Type: text/html; charset=UTF-8",true);

class AtualizaIcarrosBackground extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Icarros";

		Zend_Session::start();
		
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();

		$arrVeiculos = $dbVeiculos->getVeiculosIcarros2();

		$status = $this->edtIcarrosSistemaTodas($arrVeiculos);

		//echo count($arrVeiculos);

		if(count($arrVeiculos) == $status){
		
			echo "Sucesso! ".count($arrVeiculos)." veiculos atualizados.";
		
		}else{
		
			echo "Erro";
			//var_export($status);
		
		}

	}

	
	private function edtIcarrosSistemaTodas($arrEdt){
	
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
	
		$countAtualizados = 0;
	
		foreach($arrEdt as $chave=>$edt){

			if($edt['id_empresa'] != $arrEdt[$chave-1]['id_empresa']){
		
				try{
					
					$url = 'https://paginasegura.icarros.com.br/services/icarroswebservice?wsdl';

					$client = new SoapClient($url, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
				
					$client->__setLocation('http://www.icarros.com.br/services/icarroswebservice');
				
					$token = $client->autenticarAcesso($edt['login_icarros'], $edt['senha_icarros']);
					
				}catch (SoapFault $exception){
					
					echo $exception->getMessage();
			
				}
				
				$arrAnu = get_object_vars($client->obterListaAnunciantes($token));
				$arrAnuci = get_object_vars($arrAnu['dados']);
				$idAnunciante = $arrAnuci['id'];
			
				$arrIcarros = $this->getEstoqueIcarros($token, $idAnunciante, $client);
			
			}
			
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
			
			
					$arrAnuncio['id'] = $arrI['id'];
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
						
						if($edt['id']){

							$dados['icarros'] = 1;
							
							if($dbVeiculos->edt($edt['id'], $dados)){

								$countAtualizados++;
							
							}

						}
					
					}
					
					

				}
			
			}
		
		}
		
		return $countAtualizados;
		//return $arrStatus['descricao'];
		//return $token;
		
		
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

}

?>