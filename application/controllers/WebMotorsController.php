<?php

header("Content-Type: text/html; charset=UTF-8",true);

class WebMotorsController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "WebMotors";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}


	public function testeAction(){
		
		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
		
		try{
			
			$url = "http://www.webmotors.com.br/IntegracaoRevendedor/wsLoginSistemaRevendedor.asmx?wsdl";
		

			$client = new SoapClient($url,array('trace' => 1, 'soap_version'=> SOAP_1_2));

			$token = $client->autenticar(array("cnpj"=>"12303094000157", "email"=>"guilherme@selectveiculos.com.br", "senha"=>"gc1234"));

			$arr = get_object_vars($token);
			$arrHash = get_object_vars($arr['autenticarResult']);
			$hash = $arrHash['HashAutenticacao'];

			$url2 = "http://www.webmotors.com.br/IntegracaoRevendedor/wsEstoqueRevendedorWebMotors.asmx?wsdl";
			
			$client2 = new SoapClient($url2, array('trace' => 1));
			
			//$params = array("pHashAutenticacao"=>$hash, "pCodigoModelo"=>"2487", "pDataInicioAtualizacao"=>1900, "pDataFimAtualizacao"=>@date("Y"));
			$params = array("pHashAutenticacao"=>$hash);

			
			$arrModalidades = get_object_vars($client2->obterOpcionais($params));
			//$arrEstoqueWeb = get_object_vars($client2->ObterEstoqueAtual($params));
			//$arrEstoqueWeb2 = get_object_vars($arrEstoqueWeb['ObterEstoqueAtualResult']);
			//$arrEstoqueWeb3 = $arrEstoqueWeb2['Anuncio'];
			//$arrModalidade = get_object_vars($arrModalidades['ObterOpcionalResult']);
			//$arrModalidad = $arrModalidade['ModalidadeWM'];
			
			//foreach($arrModalidad as $modalidad){
				
				//$arModalidades = get_object_vars($modalidad);
				//var_export($arModalidades);
				//echo "<br/><br/>";
			
			//}
			
			//var_export($arrModalidades);
			
			
			$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();

			$arrOpcionaisVeiculos = $dbOpcionaisVeiculos->getVeiculosOpcionais(4278);
			
			
			
			foreach($arrOpcionaisVeiculos as $keyOp=>$opcionaisVeiculos){
			
				if($opcionaisVeiculos['id_opcionais_webmotors']){

					$arrOpcionais[$keyOp]['CodigoOpcional'] = $opcionaisVeiculos['id_opcionais_webmotors'];
					$arrOpcionais[$keyOp]['Descricao'] = substr(end(explode(")",$opcionaisVeiculos['opcional'])), 1);
					$arrOpcionais[$keyOp]['CodigoRetorno'] = "500";

				}
			
			}
			
		
			foreach($arrOpcionais as $key=>$teste){
			
				echo $key."=>".$teste['Descricao']."<br/><br/>";
			
			}
		
		}catch (SoapFault $exception) {
			
			echo $exception->getMessage();
	
		}

	}

	
	public function ajaxAction(){
	
		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
	
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
		
		$url = "http://www.webmotors.com.br/IntegracaoRevendedor/wsLoginSistemaRevendedor.asmx?wsdl";
		$url2 = "http://www.webmotors.com.br/IntegracaoRevendedor/wsEstoqueRevendedorWebMotors.asmx?wsdl";
	
		if($this->_getParam('fn') == 'alterar_webmotors'){
		
			try{


				$client = new SoapClient($url,array('trace' => 1, 'soap_version'=> SOAP_1_2));

				$token = $client->autenticar(array("cnpj"=>$arrEmpresa[0]['cnpj'], "email"=>$arrEmpresa[0]['login_webmotors'], "senha"=>$arrEmpresa[0]['senha_webmotors']));

				$arr = get_object_vars($token);
				$arrHash = get_object_vars($arr['autenticarResult']);
				$hash = $arrHash['HashAutenticacao'];

				$client2 = new SoapClient($url2, array('trace' => 1));
				
				$params = array("pHashAutenticacao"=>$hash);

				
				$arrModalidades = get_object_vars($client2->obterModalidade($params));
				$arrModalidade = get_object_vars($arrModalidades['ObterModalidadeResult']);
				$arrModalidad = $arrModalidade['ModalidadeWM'];
				
				
				//var_export($token);
				
				//if($arrTempModalidade['CodigoModalidade'] == 2673){
				if(!is_array($arrModalidad)){
					
					$arModalidades = get_object_vars($arrModalidad);
					
					$strAjax .= $arModalidades['CodigoModalidade']."|".$arModalidades['Descricao']."|".$arModalidades['QuantidadeAnunciosTotal']."|".$arModalidades['QuantidadeAnuncios']."|-|";
				
				}else{

					foreach($arrModalidad as $modalidad){
						
						$arModalidades = get_object_vars($modalidad);

						$strAjax .= $arModalidades['CodigoModalidade']."|".$arModalidades['Descricao']."|".$arModalidades['QuantidadeAnunciosTotal']."|".$arModalidades['QuantidadeAnuncios']."|-|";

					}

				}
				
				//var_export($arrModalidad);
				
				echo substr($strAjax,0,-3);

			}catch (SoapFault $exception){
				
				echo $exception->getMessage();
		
			}

		}elseif($this->_getParam('fn') == 'busca_modelos'){
		
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
			
			$arrVeiculo = $dbVeiculos->getVeiculoEstoque($this->_getParam('id_carro'));
		
			if($arrVeiculo[0]['segmento'] == "carro"){
		
				$arrMarcas = $this->getMarcasWebmotors();
				
				//var_export($arrMarcas);
				
				if(strtoupper(mb_convert_encoding($arrVeiculo[0]['marca'], 'UTF-8', 'ISO-8859-1')) == "CITROëN"){
					
					$arrVeiculo[0]['marca'] = "citroËn";
					
				}
				
				if(strtoupper(mb_convert_encoding($arrVeiculo[0]['marca'], 'UTF-8', 'ISO-8859-1')) == "KIA MOTORS"){
					
					$arrVeiculo[0]['marca'] = "KIA";
					
				}
				
				foreach($arrMarcas as $marcas){
			
					
			
					$tempMarca = explode(" - ",$arrVeiculo[0]['marca']);
					
					if($tempMarca[1]){
						
						$arrVeiculo[0]['marca'] = $tempMarca[1];
					
					}
			
					//$tr .= strtoupper($marcas['nome_marca'])."\n";
			
					if(strtoupper($arrVeiculo[0]['marca']) == strtoupper($marcas['nome_marca'])){
					
						$codigoMarca = $marcas['codigo_marca'];
						break;
						
						
					
					}

				}

				$arrModelos = $this->getModelosWebmotors($codigoMarca);
				
				//var_export($tr);
			
			}elseif($arrVeiculo[0]['segmento'] == "moto"){
			
				$arrMarcas = $this->getMarcasMotosWebmotors();
		
				foreach($arrMarcas as $marcas){

					if(stristr(strtoupper($arrVeiculo[0]['marca']), $marcas['nome_marca'])){
					
						$codigoMarca = $marcas['codigo_marca'];
						break;
					
					}

				}

				
				
				$arrModelos = $this->getModelosMotosWebmotors($codigoMarca, $arrVeiculo[0]['ano_modelo']);
		
			}

			
			foreach($arrModelos as $modelos){
				
				$codigoModelo .= "<option value='".$modelos['codigo_modelo']."'>".$modelos['nome_modelo']."</option>";

			}
			
			
			
			echo $codigoModelo."|-|".$codigoMarca."|-|".$arrVeiculo[0]['segmento'];

		}elseif($this->_getParam('fn') == 'busca_versoes'){
			
			$arrVersoes = $this->getVersoesWebmotors($this->_getParam('id_modelo'));
		
			foreach($arrVersoes as $versoes){
			
				$arrAno = $versoes['arrAnoVersao'];
			
				foreach($arrAno as $ano){
				
					if($this->_getParam('ano_modelo') == $ano){
				
						$strVersoes .= "<option value='".$versoes['CodigoVersao']."_".$ano."'>".$versoes['NomeVersao']."</option>";
					
					}
				}

			}
	
			echo $strVersoes;
			

		}elseif($this->_getParam('fn') == 'publica_veiculo'){
			
			$dbVeiculos = new Application_Model_DbTable_Veiculos();

			$arrVeiculo = $dbVeiculos->getVeiculoEstoque($this->_getParam('id_carros'));
			
			$arrAnuncio['CodigoAnuncio'] = 00000;
			$arrAnuncio['CodigoModalidade'] = end(explode("_", $this->_getParam('id_modalidade')));
			
			if($arrVeiculo[0]['novo_usado'] == 1){
				
				$arrAnuncio['TipoAnuncio'] = "U";
			
			}else{
				
				$arrAnuncio['TipoAnuncio'] = "N";
			
			}
			
			
			$arrAnuncio['CodigoMarca'] = $this->_getParam('id_marca');
			$arrAnuncio['CodigoModelo'] = $this->_getParam('id_modelo');
			$arrAnuncio['CodigoVersao'] = current(explode("_", $this->_getParam('id_versao_ano')));
			$arrAnuncio['AnoDoModelo'] = end(explode("_", $this->_getParam('id_versao_ano')));
			$arrAnuncio['AnoFabricacao'] = $arrVeiculo[0]['ano_fabricacao'];
			
			if($arrVeiculo[0]['km'] >= 1000){
			
				$arrAnuncio['Km'] = $arrVeiculo[0]['km'];
			
			}else{
			
				$arrAnuncio['Km'] = "";
			
			}
			
			$arrAnuncio['Placa'] = current(explode("-",$arrVeiculo[0]['placa'])).end(explode("-",$arrVeiculo[0]['placa']));
			
			
			if(stristr(strtoupper($this->_getParam('str_versao')), "MANUAL")){
				
				$arrAnuncio['CodigoCambio'] = 23001;
				$arrAnuncio['DescricaoCambio'] = "Manual";
				
			}else{
				
				$arrAnuncio['CodigoCambio'] = 23003;
				$arrAnuncio['DescricaoCambio'] = "Automático";
			
			}
			
			
			if(stristr(strtoupper($this->_getParam('str_versao')), "2P")){
			
				$arrAnuncio['NrPortas'] = 2;
			
			}elseif(stristr(strtoupper($this->_getParam('str_versao')), "4P")){
			
				$arrAnuncio['NrPortas'] = 4;
				
			}elseif(stristr(strtoupper($this->_getParam('str_versao')), "3P")){
			
				$arrAnuncio['NrPortas'] = 3;
			
			}elseif(stristr(strtoupper($this->_getParam('str_versao')), "5P")){
			
				$arrAnuncio['NrPortas'] = 5;
			
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
			$arrAnuncio['DataInclusao'] = @date("d/m/Y");
			$arrAnuncio['DataUltimaAlteracao'] = @date("d/m/Y");
			//$arrAnuncio['Opcional'] = "";
			
			$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();

			$arrOpcionaisVeiculos = $dbOpcionaisVeiculos->getVeiculosOpcionais($this->_getParam('id_carros'));
			
			$countOp = 0;
			
			foreach($arrOpcionaisVeiculos as $keyOp=>$opcionaisVeiculos){
			
				if($opcionaisVeiculos['id_opcionais_webmotors']){

					$arrOpcionais[$countOp]['CodigoOpcional'] = $opcionaisVeiculos['id_opcionais_webmotors'];
					$arrOpcionais[$countOp]['Descricao'] = substr(end(explode(")",$opcionaisVeiculos['opcional'])), 1);
					$arrOpcionais[$countOp]['CodigoRetorno'] = "500";

					$countOp++;
					
				}
			
			}


			
			
			$objeto = (object)$arrAnuncio;
			
			$objeto->Opcional = $arrOpcionais;

			try{

				$client = new SoapClient($url,array('trace' => 1, 'soap_version'=> SOAP_1_2));

				$token = $client->autenticar(array("cnpj"=>$arrEmpresa[0]['cnpj'], "email"=>$arrEmpresa[0]['login_webmotors'], "senha"=>$arrEmpresa[0]['senha_webmotors']));

				$arr = get_object_vars($token);
				$arrHash = get_object_vars($arr['autenticarResult']);
				$hash = $arrHash['HashAutenticacao'];

				$client2 = new SoapClient($url2, array('trace' => 1));
			
				$params = array("pHashAutenticacao"=>$hash, "pAnuncio"=>$objeto);
			
				$arrWeb = get_object_vars($client2->IncluirCarro($params));
				
				var_export($arrWeb);
				
			}catch (SoapFault $exception){
	
				echo $exception->getMessage();
		
			}
			
			$retorno = get_object_vars($arrWeb['IncluirCarroResult']);
			
			if($retorno['CodigoRetorno'] == 500){

				$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
				
				$arrFotos = $dbFotosVeiculos->getFotosVeiculoIcarros($this->_getParam('id_carros'));
				
				if($arrFotos){
					
					$conta = 0;
					
					foreach($arrFotos as $fotos){
						
						$filename =  $fotos['path'];
						$extensao = end(explode(".",$filename));
						$handle = fopen ($filename, "rb");
						$arrFotosBinario = fread ($handle, filesize ($filename));
						fclose($handle);

						$params = array("pHashAutenticacao"=>$hash, "pByteImage"=>$arrFotosBinario, "pCodigoAnuncio"=>$retorno['CodigoAnuncio']);
						
						$client2->IncluirFoto($params);
						
						$conta++;
						
						if($conta == 8){
							
							break;
						
						}
				
					}
					
				}
				
				echo $retorno['CodigoAnuncio'];
			
			}else{
			
				echo "Erro";
			
			}

		}elseif($this->_getParam('fn') == 'excluir_veiculo'){

			try{

				$client = new SoapClient($url,array('trace' => 1, 'soap_version'=> SOAP_1_2));

				$token = $client->autenticar(array("cnpj"=>$arrEmpresa[0]['cnpj'], "email"=>$arrEmpresa[0]['login_webmotors'], "senha"=>$arrEmpresa[0]['senha_webmotors']));

				$arr = get_object_vars($token);
				$arrHash = get_object_vars($arr['autenticarResult']);
				$hash = $arrHash['HashAutenticacao'];

				$client2 = new SoapClient($url2, array('trace' => 1));

				$params = array("pHashAutenticacao"=>$hash, "pCodigoAnuncio"=>end(explode("_", $this->_getParam('id_anuncio_veiculo'))), "pMotivoExclusao"=>"3");
			
				$arrWeb = get_object_vars($client2->ExcluirCarro($params));
				

			}catch (SoapFault $exception){
	
				echo $exception->getMessage();
		
			}
		
			$retorno = get_object_vars($arrWeb['ExcluirCarroResult']);
			
			echo $retorno['CodigoRetorno'];
			
			
		}elseif($this->_getParam('fn') == 'altera_modalidade'){

			try{

				$client = new SoapClient($url,array('trace' => 1, 'soap_version'=> SOAP_1_2));

				$token = $client->autenticar(array("cnpj"=>$arrEmpresa[0]['cnpj'], "email"=>$arrEmpresa[0]['login_webmotors'], "senha"=>$arrEmpresa[0]['senha_webmotors']));

				$arr = get_object_vars($token);
				$arrHash = get_object_vars($arr['autenticarResult']);
				$hash = $arrHash['HashAutenticacao'];

				$client2 = new SoapClient($url2, array('trace' => 1));

				$params = array("pHashAutenticacao"=>$hash, "pCodigoAnuncio"=>end(explode("_", $this->_getParam('id_anuncio'))), "pCodigoModalidade"=>$this->_getParam('id_modalidade'));
			
				$arrWeb = get_object_vars($client2->TrocarModalidadeCarro($params));
				
				$retorno = get_object_vars($arrWeb['TrocarModalidadeCarroResult']);
			
				echo $retorno['CodigoRetorno'];
				

			}catch (SoapFault $exception){
	
				echo $exception->getMessage();
		
			}
	
		}elseif($this->_getParam('fn') == 'publica_veiculo_moto'){
			
			$url2 = "http://www.webmotors.com.br/IntegracaoRevendedor/Motos/wsEstoqueRevendedorMotos.asmx?wsdl";
			
			$dbVeiculos = new Application_Model_DbTable_Veiculos();

			$arrVeiculo = $dbVeiculos->getVeiculoEstoque($this->_getParam('id_carros'));
			
			$arrAnuncio['CodigoAnuncio'] = 0000;
			$arrAnuncio['CodigoModalidade'] = end(explode("_", $this->_getParam('id_modalidade')));
			
			if($arrVeiculo[0]['novo_usado'] == 1){
				
				$arrAnuncio['TipoAnuncio'] = "U";
			
			}else{
				
				$arrAnuncio['TipoAnuncio'] = "N";
			
			}
			
			
			$arrAnuncio['CodigoMarca'] = $this->_getParam('id_marca');
			$arrAnuncio['CodigoModelo'] = $this->_getParam('id_modelo');
			//$arrAnuncio['CodigoVersao'] = current(explode("_", $this->_getParam('id_versao_ano')));
			$arrAnuncio['AnoDoModelo'] = $arrVeiculo[0]['ano_modelo'];
			$arrAnuncio['AnoFabricacao'] = $arrVeiculo[0]['ano_fabricacao'];
			$arrAnuncio['Km'] = $arrVeiculo[0]['km'];
			$arrAnuncio['Placa'] = current(explode("-",$arrVeiculo[0]['placa'])).end(explode("-",$arrVeiculo[0]['placa']));
			$arrAnuncio['TipoConsistencia'] = 0;
			
			
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

			foreach($this->getCoresMotosWebmotors() as $key=>$cor){

				if(strtolower($arrVeiculo[0]['cor']) == strtolower($cor)){

					$arrAnuncio['CodigoCorPredominante'] = $key;
					$arrAnuncio['CodigoCorSecundaria'] = "";
					$arrAnuncio['DescricaoCorPredominante'] = $cor;
					break;
				
				}
			
			}
			
			$arrAnuncio['NumeroCilindradas'] = "";
			$arrAnuncio['CodigoNumeroMarchas'] = 221;
			$arrAnuncio['CodigoTipoRefrigeracao'] = 212;
			$arrAnuncio['CodigoTipoAlimentacao'] = 201;
			$arrAnuncio['CodigoTipoMotor'] = 193;
			$arrAnuncio['CodigoTipoPartida'] = 233;
			$arrAnuncio['CodigoTipoFreio'] = 248;
			$arrAnuncio['UnicoDono'] = "N";
			$arrAnuncio['Alienado'] = "N";
			$arrAnuncio['IPVAPago'] = 'N';
			$arrAnuncio['GarantiaDeFabrica'] = "N";
			$arrAnuncio['Licenciado'] = "N";
			$arrAnuncio['PrecoVenda'] = $arrVeiculo[0]['valor_venda'];
			$arrAnuncio['CodigoSMS'] = 0;
			$arrAnuncio['PrecoRevenda'] = 0;
			$arrAnuncio['Observacao'] = $arrVeiculo[0]['obs_site'];
			$arrAnuncio['DataInclusao'] = @date("d/m/Y");
			$arrAnuncio['DataUltimaAlteracao'] = @date("d/m/Y");
			
			
			
			$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();

			$arrOpcionaisVeiculos = $dbOpcionaisVeiculos->getVeiculosOpcionais($this->_getParam('id_carros'));
			
			foreach($arrOpcionaisVeiculos as $keyOp=>$opcionaisVeiculos){
			
				if($opcionaisVeiculos['id_opcionais_webmotors']){
			
					$arrOpcionais[$keyOp]['CodigoOpcionalUsada'] = $opcionaisVeiculos['id_opcionais_webmotors'];
					$arrOpcionais[$keyOp]['Descricao'] = $opcionaisVeiculos['opcional'];
					$arrOpcionais[$keyOp]['CodigoRetorno'] = "500";
				
					
				
				}
			
			}
			
			$arrAnuncio['OpcionalUsado'] = $arrOpcionais;

			foreach($arrAnuncio as $key=>$valor){
			
				$objeto->$key = $valor;
			
			}
			
			
			try{

				$client = new SoapClient($url,array('trace' => 1, 'soap_version'=> SOAP_1_2));

				$token = $client->autenticar(array("cnpj"=>$arrEmpresa[0]['cnpj'], "email"=>$arrEmpresa[0]['login_webmotors'], "senha"=>$arrEmpresa[0]['senha_webmotors']));

				$arr = get_object_vars($token);
				$arrHash = get_object_vars($arr['autenticarResult']);
				$hash = $arrHash['HashAutenticacao'];

				$client2 = new SoapClient($url2, array('trace' => 1));
			
				$params = array("hashAutenticacao"=>$hash, "anuncioWM"=>$objeto);
			
				$arrWeb = get_object_vars($client2->incluirMoto($params));

			}catch (SoapFault $exception){
	
				echo $exception->getMessage();
		
			}
			
			var_export($arrAnuncio);
			
			//$retorno = get_object_vars($arrWeb['incluirMotoResult']);
			$retorno = $arrWeb;
			
			if($retorno['CodigoRetorno'] == 500){
				/*
				$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
				
				$arrFotos = $dbFotosVeiculos->getFotosVeiculoIcarros($this->_getParam('id_carros'));
				
				if($arrFotos){
					
					$conta = 0;
					
					foreach($arrFotos as $fotos){
						
						$filename =  $fotos['path'];
						$extensao = end(explode(".",$filename));
						$handle = fopen ($filename, "rb");
						$arrFotosBinario = fread ($handle, filesize ($filename));
						fclose($handle);

						$params = array("pHashAutenticacao"=>$hash, "pByteImage"=>$arrFotosBinario, "pCodigoAnuncio"=>$retorno['CodigoAnuncio']);
						
						$client2->IncluirFoto($params);
						
						$conta++;
						
						if($conta == 8){
							
							break;
						
						}
				
					}
					
				}
				
				echo $retorno['CodigoAnuncio'];
			*/
			}else{
			
				//echo "Erro";
				var_export($retorno);
				
			
			}

		}
	
	}
	
	
	private function getVersoesWebmotors($codigoModelo){
	
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

			$params = array("pHashAutenticacao"=>$hash, "pCodigoModelo"=>$codigoModelo, "pDataInicioAtualizacao"=>1900, "pDataFimAtualizacao"=>@date("Y"));
		
			$arrDados1 = get_object_vars($client2->obterVersao($params));
			$arrDados2 = get_object_vars($arrDados1['ObterVersaoResult']);
			$arrDados3 = $arrDados2['Versao'];
	
			foreach($arrDados3 as $arDados){
			
				$dados = get_object_vars($arDados);
				$arrVersao[$dados['CodigoVersao']]['CodigoVersao'] = $dados['CodigoVersao'];
				$arrVersao[$dados['CodigoVersao']]['NomeVersao'] = $dados['NomeVersao'];
				
				$arrAnoModelos = get_object_vars($dados['AnoModelo']);
				
				$arrObj = Array();
				
				$anoModelo = $arrAnoModelos['AnoModeloWM'];
			
				if (is_object($anoModelo)){
					
					$arrAno = get_object_vars($anoModelo);
					
					$arrObj[] = $arrAno['AnoModelo'];
					
					$arrVersao[$dados['CodigoVersao']]['arrAnoVersao'] = $arrObj;
				
				}else{
					
					foreach($anoModelo as $ano){
						
						$arrAnos = get_object_vars($ano);
						$arrObj[] = $arrAnos['AnoModelo'];
					
						$arrVersao[$dados['CodigoVersao']]['arrAnoVersao'] = $arrObj;
						
						
					
					}
				
				}
		
			}
			
			return $arrVersao;

		}catch (SoapFault $exception){
	
			echo $exception->getMessage();
		
		}

	}
	

	private function getModelosWebmotors($codigoMarca){
	
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
		
			$params = array("pHashAutenticacao"=>$hash, "pCodigoMarca"=>$codigoMarca);

		
			$arrWeb = get_object_vars($client2->obterModelo($params));
			$arrWeb2 = get_object_vars($arrWeb['ObterModeloResult']);
			$arrWeb3 = $arrWeb2['ModeloWM'];
	
			foreach($arrWeb3 as $key=>$web){
			
				$arWebModelo = get_object_vars($web);
		
				$arrWebModelos[$key]['codigo_modelo'] = $arWebModelo['CodigoModelo'];
				$arrWebModelos[$key]['nome_modelo'] = $arWebModelo['NomeModelo'];

			}

			return $arrWebModelos;
			

		}catch (SoapFault $exception){
	
			echo $exception->getMessage();
		
		}

	}
	
	
	private function getModelosMotosWebmotors($codigoMarca, $anoModelo){
	
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
		
		$url = "http://www.webmotors.com.br/IntegracaoRevendedor/wsLoginSistemaRevendedor.asmx?wsdl";
		$url2 = "http://www.webmotors.com.br/IntegracaoRevendedor/Motos/wsCodigosWebMotorsMotos.asmx?wsdl";
	
		try{

			$client = new SoapClient($url,array('trace' => 1, 'soap_version'=> SOAP_1_2));

			$token = $client->autenticar(array("cnpj"=>$arrEmpresa[0]['cnpj'], "email"=>$arrEmpresa[0]['login_webmotors'], "senha"=>$arrEmpresa[0]['senha_webmotors']));

			$arr = get_object_vars($token);
			$arrHash = get_object_vars($arr['autenticarResult']);
			$hash = $arrHash['HashAutenticacao'];

			$client2 = new SoapClient($url2, array('trace' => 1));
		
			$params = array("hashAutenticacao"=>$hash, "codigoMarca"=>$codigoMarca, "dataInicioAtualizacao"=>"1900-01-01", "dataFinalAtualizacao"=>(@date("Y")+1)."-12-31");

		
			$arrWeb = get_object_vars($client2->obterModeloMotos($params));
			$arrWeb2 = get_object_vars($arrWeb['obterModeloMotosResult']);
			$arrWeb3 = $arrWeb2['ModeloMotoWM'];
	
			foreach($arrWeb3 as $key=>$web){
			
				$arWebModelo = get_object_vars($web);
			/*
				$anoss = get_object_vars($arWebModelo['AnoMoto']);
				
				if(is_object($anoss['AnoMotoWM'])){
				
					$anosss =  get_object_vars($anoss['AnoMotoWM']);
					$ano = $anosss['AnoModelo'];
				
				}else{
					
					$ano = get_object_vars($anoss['AnoMotoWM']);
				
				}
				*/
				$arrWebModelos[$key]['codigo_modelo'] = $arWebModelo['CodigoModelo'];
				$arrWebModelos[$key]['nome_modelo'] = $arWebModelo['NomeModelo']." - ".$anossss['AnoModelo'];
			
			}

			return $arrWebModelos;
			//return $arWebModelo;
			

		}catch (SoapFault $exception){
	
			echo $exception->getMessage();
		
		}

	}
	
	
	private function getMarcasMotosWebmotors(){
	
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
		
		$url = "http://www.webmotors.com.br/IntegracaoRevendedor/wsLoginSistemaRevendedor.asmx?wsdl";
		$url2 = "http://www.webmotors.com.br/IntegracaoRevendedor/Motos/wsCodigosWebMotorsMotos.asmx?wsdl";
	
		try{

			$client = new SoapClient($url,array('trace' => 1, 'soap_version'=> SOAP_1_2));

			$token = $client->autenticar(array("cnpj"=>$arrEmpresa[0]['cnpj'], "email"=>$arrEmpresa[0]['login_webmotors'], "senha"=>$arrEmpresa[0]['senha_webmotors']));

			$arr = get_object_vars($token);
			$arrHash = get_object_vars($arr['autenticarResult']);
			$hash = $arrHash['HashAutenticacao'];

			$client2 = new SoapClient($url2, array('trace' => 1));
		
			$params = array("hashAutenticacao"=>$hash);

		
			$arrWeb = get_object_vars($client2->obterMarcaMotos($params));
			$arrWeb2 = get_object_vars($arrWeb['obterMarcaMotosResult']);
			$arrWeb3 = $arrWeb2['MarcaMotoWM'];
	
			foreach($arrWeb3 as $key=>$web){
			
				$arWebMarcas = get_object_vars($web);
		
				$arrWebMarcas[$key]['codigo_marca'] = $arWebMarcas['CodigoMarca'];
				$arrWebMarcas[$key]['nome_marca'] = $arWebMarcas['NomeMarca'];

			}

			return $arrWebMarcas;
		
			//return $arrWeb2;
			

		}catch (SoapFault $exception){
	
			echo $exception->getMessage();
		
		}

	}
	
	private function getMarcasWebmotors(){
	
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

		
			$arrWeb = get_object_vars($client2->obterMarca($params));
			$arrWeb2 = get_object_vars($arrWeb['ObterMarcaResult']);
			$arrWeb3 = $arrWeb2['MarcaWM'];
	
			foreach($arrWeb3 as $key=>$web){
			
				$arWebMarcas = get_object_vars($web);
		
				$arrWebMarcas[$key]['codigo_marca'] = $arWebMarcas['CodigoMarca'];
				$arrWebMarcas[$key]['nome_marca'] = $arWebMarcas['NomeMarca'];

			}

			//return $arrWeb;
			
			return $arrWebMarcas;
			

		}catch (SoapFault $exception){
	
			echo $exception->getMessage();
		
		}

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
	
	private function getCoresMotosWebmotors(){
		
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
		
		$url = "http://www.webmotors.com.br/IntegracaoRevendedor/wsLoginSistemaRevendedor.asmx?wsdl";
		$url2 = "http://www.webmotors.com.br/IntegracaoRevendedor/Motos/wsCodigosWebMotorsMotos.asmx?wsdl";
	
		try{

			$client = new SoapClient($url,array('trace' => 1, 'soap_version'=> SOAP_1_2));

			$token = $client->autenticar(array("cnpj"=>$arrEmpresa[0]['cnpj'], "email"=>$arrEmpresa[0]['login_webmotors'], "senha"=>$arrEmpresa[0]['senha_webmotors']));

			$arr = get_object_vars($token);
			$arrHash = get_object_vars($arr['autenticarResult']);
			$hash = $arrHash['HashAutenticacao'];

			$client2 = new SoapClient($url2, array('trace' => 1));
		
			$params = array("hashAutenticacao"=>$hash);

		
			$arrWeb = get_object_vars($client2->obterCoresMotosUsadas($params));
			$arrWeb2 = get_object_vars($arrWeb['obterCoresMotosUsadasResult']);
			$arrWeb3 = $arrWeb2['CorMotoUsadaWM'];
	
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

}

?>
