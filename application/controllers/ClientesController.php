<?php

header("Content-Type: text/html; charset=UTF-8",true);

class ClientesController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Clientes";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}

	public function lerXlsxAction(){

		if($this->getRequest()->isPost()){

			if($_SESSION['sessionUser']['id_empresa'] == 239) {

				$dbUsuarios = new Application_Model_DbTable_Usuarios();
				$arrVendedores = $dbUsuarios->getVendedores();
				$dbFluxo = new Application_Model_DbTable_FluxoClientes();

				$arrListVendedores = array();

				$hash = null;

				$erro = false;
				$msg = "";

				foreach ($arrVendedores as $key => $arrVal) {
					$arrListVendedores[strtolower($arrVal['nome'])] = $arrVal['id'];
				}


				require_once 'Classes/PHPExcel.php';
				$objPHcPExcel=PHPExcel_IOFactory::load($_FILES["xlsx"]["tmp_name"]);

				foreach ($objPHcPExcel->getWorksheetIterator() as $key => $worksheet) { 

					//var_export($key);

		        	//=====	Ttitulo da Celula do Excel
		        	$worksheetTitle[$key]     = $worksheet->getTitle();
		        	//=====	Quantidade de Linhas 
		        	$highestRow         = $worksheet->getHighestRow();
		        	//=====	Quantidade de Colunas
		        	$highestColumn      = $worksheet->getHighestColumn();
		        	
		        	//=====	iremos acessar a linha que tem informações, iniciar pela linha 2
		        	//=====	linha 1 contem o cabeçalho

		        	if($highestRow > 1000) {
		        		$highestRow = 1000;
		        	}


		        	for ($row=2; $row <= $highestRow; ++ $row){

		        		//===== Coluna A
		        		// $cell = $worksheet -> getCellByColumnAndRow (0, $row);
		        		// $colunaA = $cell ->getValue();
		        		// //===== Coluna B
		        		// $cell = $worksheet -> getCellByColumnAndRow (1, $row);
		        		// $colunaB = $cell ->getValue();
		        		// //===== Coluna C
		        		// $cell = $worksheet -> getCellByColumnAndRow (2, $row);
		        		// $colunaC = $cell ->getValue();

		        		$usuario = $worksheet -> getCellByColumnAndRow (3, $row)->getValue();
		        		$nome = $worksheet -> getCellByColumnAndRow (6, $row)->getValue();
		        		$origem = $worksheet -> getCellByColumnAndRow (0, $row)->getValue();
		        		$dateTime = $worksheet -> getCellByColumnAndRow (8, $row)->getValue();

		        		if($hash == null) {
		        			$hash = md5($usuario.$nome.$origem.$dateTime);
		        		}

		        		if((isset($usuario) && $usuario != "") &&
						   (isset($origem) && $origem != "") &&
						   (isset($dateTime) && $dateTime != "") &&
						   (array_key_exists(strtolower($usuario), $arrListVendedores))) {


			        		$arr[$row]['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			        		$arr[$row]['id_usuario'] = $arrListVendedores[strtolower($usuario)];
			        		if($nome == ""){
			        			$arr[$row]['nome'] = "Nome Desconhecido";
			        		}else{
			        			$arr[$row]['nome'] = $nome;
			        		}

			        		if($origem == "WHATSAPP" || $origem == "TELEGRAM") {
			        			$tel = $worksheet -> getCellByColumnAndRow (7, $row)->getValue();
			        			$arr[$row]['telefone'] = "(".substr($tel, 2, 2).") ".substr($tel, 4, 10);
			        		}

			        		$date = explode(" ", $dateTime)[0];
			        		$time = explode(" ", $dateTime)[1];

			        		$arr[$row]['data'] = implode("-", array_reverse(explode("/", $date)))." ".$time.":00";

			        	}else{
			        		if(!array_key_exists(strtolower($usuario), $arrListVendedores) && $usuario != "") {
			        			$msg = "ERRO: O Atendente ".$usuario." não foi encontrado no sistema!<br>O nome na planilha deve ser exatamente o mesmo nome cadastrado no sistema.";
			        			$erro = true;
			        			break;
			        		}
			        	}
		        		
		        	}

		        	if($erro) {
		        		break;
		        	}
		        	
		        }

		        if($erro === false) {

			        if(count($dbFluxo->getFluxoHash($hash)) == 0) {

			        	if(count($arr) > 0){
			        		
			        		foreach ($arr as $key => $dados) {
			        			$dados['hash'] = $hash;
			        			$dbFluxo->add($dados);
			        		}
			        		$this->view->mensagem = "Foram adicionados ".count($arr)." atendimentos com sucesso!";
			        	}

			        }else{
			        	$this->view->mensagem = "ERRO: O sistema identificou que essa planilha já foi adicionada!";
			        }

		        }else{
		        	$this->view->mensagem = $msg;
		        }

			}
			
		}

	}
	
	
	public function listaFluxoAction(){
		
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
	
	
	
	private function getEmailImap(){
		
		$dbFluxo = new Application_Model_DbTable_FluxoClientes();
		
		$arrFluxo['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arrFluxo['data_inicial'] = @date('Y-m-d', mktime(0,0,0, @date('m')-100, @date('d'), @date('Y')));
		$arrFluxo['data_final'] = @date('Y-m-d 23:59:59');
		$arrFluxo['limite'] = 5000;
		
		$arrClients = $dbFluxo->getOrigemClientes($arrFluxo);

		//$dbFluxoClientes->closeConnection();
		
		//var_export(count($arrClients));
		
		//$caixaEmail = $this->conectaEmail();

		$numTotalEmail = imap_num_msg($caixaEmail);
		
		$ultimoEmail = "";

		$numEmailsLidos = 200;
		
		$emailAnterior = "";
		$telefoneAnterior = "";

		for($i=$numTotalEmail-$numEmailsLidos; $i<=$numTotalEmail; $i++){
			
			$arrHead = imap_headerinfo($caixaEmail, $i);

			if($arrHead->Unseen == "U"){
				$idFluxo = false;
				
				$arrLead = array();
				
				$arrHead = get_object_vars($arrHead);

				$arrFrom = get_object_vars($arrHead['from'][0]);
				$from = $arrFrom['mailbox']."@".$arrFrom['host'];
			
				$add = false;

				/////////////////////////////////////////////////////////
				////Seta Email como não lido 						 ////
				////imap_clearflag_full($caixaEmail, $i, "\\Seen");  ////
				/////////////////////////////////////////////////////////

				if($from == "alertaproposta@webmotors.com.br"){
				 ////Proposta Normal com assunto: Proposta (WebMotors)/////
					if(strpos($arrHead['Subject'], 'Proposta') !== false){

						$structure = imap_fetchstructure($caixaEmail, $i);
						$coding = $structure->encoding;

						$str = imap_fetchbody($caixaEmail, $i, "1");
		
						if ($coding == 0) {
						   	$str = $str;
						} elseif ($coding == 1) {
						    $str= imap_utf8($str);
						} elseif ($coding == 2) {
						    $str = imap_binary($str);
						} elseif ($coding == 3) {
						    $str = imap_base64($str);
						} elseif ($coding == 4) {
						    $str = imap_qprint($str);
						} elseif ($coding == 5) {
						    $str = $str;
						}

						$arr = explode("</td>",$str);

						$arrLead['nome'] = trim(strip_tags(current(explode("<br>", $this->arrayVal(explode(":", $arr[10]), 1)))));
						$arrLead['email'] = trim(strip_tags(current(explode("<br>", $this->arrayVal(explode(":", $arr[10]), 2)))));
						$arrLead['telefone'] = str_replace(" ", "",str_replace(" ", "",trim(strip_tags(current(explode("<br>", $this->arrayVal(explode(":", $arr[10]), 3)))))));
						$arrLead['id_usuario'] = $this->getFilaVendedoresPorDia($arrLead['email']);
						$arrLead['data'] = @date('Y-m-d H:i:s');
						$arrLead['imap_origem'] = 1;
						$arrLead['atualizacao'] = 1;
						$arrLead['id_usuario'] = $this->getFilaVendedoresPorDia(strtolower($email));

						$veiculo = trim(strip_tags(end(explode(":", $this->arrayVal(explode("<br>", $arr[16]),0)))));
						$ano = trim(strip_tags(end(explode(":", $this->arrayVal(explode("<br>", $arr[16]),1)))));
						$cor = trim(strip_tags(end(explode(":", $this->arrayVal(explode("<br>", $arr[16]),2)))));
						$veiculo = "Veículo de interesse: ".$veiculo."\nAno: ".$ano."\nCor: ".$cor;

						$msg = trim(strip_tags($arr[9]));
						$arrLead['atividades'] = $veiculo."\n\n".$msg;

						$placa = trim(strip_tags(end(explode(":", $this->arrayVal(explode("<br>", $arr[16]),3)))));
						$placa = substr($placa, 0, 3)."-".substr($placa, -4);
						$dbVeiculos = new Application_Model_DbTable_Veiculos();
						$arrVeiculo = $dbVeiculos->getVeiculoPorPlaca($placa);

						if($arrVeiculo){
							$arrLead['id_empresa'] = $arrVeiculo[0]['id_empresa'];
						}else{
							$arrLead['id_empresa'] = 239;
						}

						$arrLead['valor'] = trim(str_replace("R$", "",strip_tags(end(explode(":", $this->arrayVal(explode("<br>", $arr[16]),4))))));

						if($arrLead['id_empresa'] == 3){
							$arrLead['origem'] = 13;
						}elseif($arrLead['id_empresa'] == 239){
							$arrLead['origem'] = 33;
						}

						$add = true;

						if($emailAnterior != $arrLead['email'] || $telefoneAnterior != $arrLead['telefone']){

							$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
							$idFluxo = $dbFluxoClientes->add($arrLead);
							
							$emailAnterior = $arrLead['email'];
							$telefoneAnterior = $arrLead['telefone'];

						}

						if($idFluxo){

							$arrVeiculoFluxo['id_fluxo_cliente'] = $idFluxo;
							$arrVeiculoFluxo['veiculo'] = current(explode(" ", $arrVeiculo[0]['modelo']));
							$arrVeiculoFluxo['ano_modelo'] = $arrVeiculo[0]['ano_modelo'];
							$arrVeiculoFluxo['estoque'] = 1;
							
							$dbVeiculosFluxo = new Application_Model_DbTable_VeiculosFluxo();
							$dbVeiculosFluxo->add($arrVeiculoFluxo);

						}

					}
					
				///////Contato Normal(OLX)///////
				}elseif($from == "noreply@olx.com.br"){

					header("Content-Type: text/html; charset=ISO-8859-1", true);

					if(strpos($arrHead['Subject'], 'FWD') !== false){

						/*////////////////OLX antes de 16/03/2020////////////////
						$arr = explode("div",imap_qprint(imap_fetchbody($caixaEmail, $i, 1)));
						$arrDados = explode(">",$arr[4]);
						$interessado = strip_tags($arrDados[16]);
						$msg = strip_tags($arrDados[9]);
						$email = strip_tags($arrDados[22]);
						$telefone = strip_tags($arrDados[28]);
						$arrDadosCarro = explode("<td",$arr[5]);
						$veiculo = strip_tags($this->arrayVal(explode(">",$arrDadosCarro[2]),2));
						$valor = str_replace("R$", "", end(explode(":", strip_tags($this->arrayVal(explode(">",$arrDadosCarro[3]),2)))));
						/////////////////OLX antes de 16/03/2020//////////////*/

						$arr = explode("div",imap_qprint(imap_fetchbody($caixaEmail, $i, 1)));
						$interessado = strip_tags(trim(end(explode(">",$this->arrayVal(explode("<",$arr[8]),3)))));
						$msg = strip_tags(trim(end(explode(">",$this->arrayVal(explode("<",$arr[6]),2)))));
						$email = trim(end(explode(":",end(explode(">",strip_tags($arr[10]))))));
						$telefone = trim(end(explode(":",end(explode(">",strip_tags($arr[12]))))));
						$veiculo = trim(current(explode("(",end(explode(">",strip_tags($arr[15]))))));
						$valor = trim(end(explode("R$",end(explode(">",strip_tags($arr[15]))))));
						var_export($i);

					}elseif(strpos($arrHead['Subject'], 'comprador') !== false && strpos($arrHead['Subject'], 'interessado') !== false){

						$arr = explode("div",imap_qprint(imap_fetchbody($caixaEmail, $i, 1)));
						$interessado = trim(end(explode(">",strip_tags($arr[20]))));
						$email = trim(end(explode(">",strip_tags($arr[24]))));
						$msg = strip_tags(trim(end(explode(">",strip_tags($arr[10])))));
						$veiculo = trim(end(explode(">",strip_tags($arr[14]))));
						$valor = trim(str_replace("R$", "", end(explode(">",strip_tags($arr[16])))));
						$cpf = trim(str_replace("R$", "", end(explode(">",strip_tags($arr[28])))));
						$msg .= "\nCPF do cliente: ".$cpf;

					}

					if((strpos($arrHead['Subject'], 'comprador') !== false && strpos($arrHead['Subject'], 'interessado') !== false) || (strpos($arrHead['Subject'], 'FWD') !== false)){

						$arrLead['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
						$arrLead['id_usuario'] = $this->getFilaVendedoresPorDia(strtolower($email));
						$arrLead['nome'] = $interessado;
						$arrLead['telefone'] = $telefone;
						$arrLead['email'] = strtolower($email);
						$arrLead['valor'] = str_replace(",", ".", str_replace(".", "",  $valor));
						
						if($_SESSION['sessionUser']['id_empresa'] == 3){
							$arrLead['origem'] = 16;
						}elseif($_SESSION['sessionUser']['id_empresa'] == 239){
							$arrLead['origem'] = 28;
						}
						
						$arrLead['data'] = @date('Y-m-d H:i:s');
						$arrLead['atividades'] = "Veículo de interesse: ".$veiculo."\n\n".$msg;
						$arrLead['imap_origem'] = 1;
						$arrLead['atualizacao'] = 1;

						//if(strripos($arrHead['reply_toaddress'], '@') && strripos($email, '@') && $arrHead['reply_toaddress'] == $email && ($emailAnterior != $arrLead['email'] || $telefoneAnterior != $arrLead['telefone'])){
						if($emailAnterior != $arrLead['email'] || $telefoneAnterior != $arrLead['telefone']){

							$add = true;
							
							$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
							$dbFluxoClientes->add($arrLead);


							$emailAnterior = $arrLead['email'];
							$telefoneAnterior = $arrLead['telefone'];
							
						}

					}

				}elseif($from == "carros@icarros.com.br"){

				 ////Proposta Normal com assunto: Proposta Recebida(Icarros)/////
					if(strpos($arrHead['Subject'], 'Recebida') !== false){

						$arr = explode("table", imap_qprint(imap_fetchbody($caixaEmail, $i, 1)));

						$arrLead['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
						$msg = trim(end(explode(">", strip_tags($arr[10]))));
						$arrLead['nome'] = trim(end(explode("Nome", strip_tags($arr[12]))));
						$arrLead['email'] = strtolower(trim(end(explode("E-mail", strip_tags($arr[14])))));

						$arrTele = array();
						$arrTele = explode("Telefone", strip_tags($arr[16]));
						if($arrTele[1]){
							$arrLead['telefone'] = trim(end($arrTele));
						}else{
							$arrTele = array();
							$arrTele = explode("Celular", strip_tags($arr[16]));
							if($arrTele[1]){
								$arrLead['telefone'] = trim(end($arrTele));
							}
						}

						$arrLead['id_usuario'] = $this->getFilaVendedoresPorDia($arrLead['email']);
						$arrLead['data'] = @date('Y-m-d H:i:s');
						$arrLead['imap_origem'] = 1;
						$arrLead['atualizacao'] = 1;

						if($_SESSION['sessionUser']['id_empresa'] == 3){
							$arrLead['origem'] = 15;
						}elseif($_SESSION['sessionUser']['id_empresa'] == 239){
							$arrLead['origem'] = 30;
						}

						$entrada = trim(end(explode(">", strip_tags($arr[18]))));

						$veiculo = "Veículo de interesse: ".trim(end(explode("Anúncio:", strip_tags($arr[7]))));
						$arrLead['atividades'] = $veiculo."\n\n".$msg;

						$arrAnuncio = explode(" ", $veiculo);

						$placa = str_replace(")", "", str_replace("(", "", end($arrAnuncio)));
						$placa = substr($placa, 0, 3)."-".substr($placa, -4);
						array_pop($arrAnuncio);

						$arrLead['valor'] = str_replace(".","", end($arrAnuncio));

						$dbVeiculos = new Application_Model_DbTable_Veiculos();
						
						$arrVeiculo = $dbVeiculos->getVeiculoPorPlaca($placa);

						$add = true;
						
						if($emailAnterior != $arrLead['email'] || $telefoneAnterior != $arrLead['telefone']){

							$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
							$idFluxo = $dbFluxoClientes->add($arrLead);
							
							$emailAnterior = $arrLead['email'];
							$telefoneAnterior = $arrLead['telefone'];

						}
						

						if($idFluxo){

							$arrVeiculoFluxo['id_fluxo_cliente'] = $idFluxo;
							$arrVeiculoFluxo['veiculo'] = current(explode(" ", $arrVeiculo[0]['modelo']));
							$arrVeiculoFluxo['ano_modelo'] = $arrVeiculo[0]['ano_modelo'];
							$arrVeiculoFluxo['estoque'] = 1;
							
							$dbVeiculosFluxo = new Application_Model_DbTable_VeiculosFluxo();
							$dbVeiculosFluxo->add($arrVeiculoFluxo);

						}


					///////Proposta com Pré-Análise(Icarros)//////	
					}elseif(strpos($arrHead['Subject'], 'Analisado') !== false){

						$arr = explode("table", imap_qprint(imap_fetchbody($caixaEmail, $i, 1)));

						//var_export($arr);

						$arrLead['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
						$msg = trim(end(explode(">", strip_tags($arr[10]))));
						$arrLead['nome'] = trim(end(explode("Nome", strip_tags($arr[14]))));
						$arrLead['email'] = strtolower(trim(end(explode("E-mail", strip_tags($arr[16])))));
						
						$arrTele = array();
						$arrTele = explode("Telefone", strip_tags($arr[20]));
						if($arrTele[1]){
							$arrLead['telefone'] = trim(end($arrTele));
						}

						$arrTele = array();
						$arrTele = explode("Celular", strip_tags($arr[18]));
						if($arrTele[1]){
							$arrLead['celular'] = trim(end($arrTele));
						}

						$arrLead['id_usuario'] = $this->getFilaVendedoresPorDia($arrLead['email']);
						$arrLead['data'] = @date('Y-m-d H:i:s');
						$arrLead['imap_origem'] = 1;
						$arrLead['atualizacao'] = 1;

						if($_SESSION['sessionUser']['id_empresa'] == 3){
							$arrLead['origem'] = 15;
						}elseif($_SESSION['sessionUser']['id_empresa'] == 239){
							$arrLead['origem'] = 30;
						}

						$entrada = trim(end(explode(">", strip_tags($arr[18]))));

						$veiculo = "Veículo de interesse: ".trim(end(explode("Anúncio:", strip_tags($arr[7]))));
						$arrLead['atividades'] = $veiculo."\n\n".$msg;

						$arrAnuncio = explode(" ", $veiculo);

						$placa = str_replace(")", "", str_replace("(", "", end($arrAnuncio)));
						$placa = substr($placa, 0, 3)."-".substr($placa, -4);
						array_pop($arrAnuncio);

						$arrLead['valor'] = str_replace(".","", end($arrAnuncio));

						$dbVeiculos = new Application_Model_DbTable_Veiculos();
						
						$arrVeiculo = $dbVeiculos->getVeiculoPorPlaca($placa);

						$add = true;
						
						if($emailAnterior != $arrLead['email'] || $telefoneAnterior != $arrLead['telefone']){

							$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
							$idFluxo = $dbFluxoClientes->add($arrLead);
							
							$emailAnterior = $arrLead['email'];
							$telefoneAnterior = $arrLead['telefone'];

						}
						

						if($idFluxo){

							$arrVeiculoFluxo['id_fluxo_cliente'] = $idFluxo;
							$arrVeiculoFluxo['veiculo'] = current(explode(" ", $arrVeiculo[0]['modelo']));
							$arrVeiculoFluxo['ano_modelo'] = $arrVeiculo[0]['ano_modelo'];
							$arrVeiculoFluxo['estoque'] = 1;
							
							$dbVeiculosFluxo = new Application_Model_DbTable_VeiculosFluxo();
							$dbVeiculosFluxo->add($arrVeiculoFluxo);

						}

					}

				 ///////Comprecar//////	
				//}elseif(strpos($arrHead['subject'], "nteresse") && $arrHead['fromaddress'] == "mailer@comprecar.com.br" && $arrHead['reply_to'][0]->mailbox."@".$arrHead['reply_to'][0]->host != $arrHead['from'][0]->mailbox."@".$arrHead['from'][0]->host){
				}elseif($arrHead['subject'] == "Contato de Interesse" && ($from == "no-replay@comprecar.com.br" || $from == "sendmail@comprecar.com.br")){

					$arr = explode("<td", imap_qprint(imap_fetchbody($caixaEmail, $i, 1)));

					$arrVeiculo = explode(">", $arr[11]);
					$arr = explode(">", $arr[9]);

					$modelo = trim(strip_tags($arrVeiculo[4]));
					$anoModelo = trim(strip_tags($arrVeiculo[10]));
					$valor = trim(strip_tags($arrVeiculo[7]));
					
					$arrLead['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
					$arrLead['nome'] = trim(strip_tags($arr[4]));
					$arrLead['email'] = strtolower(trim(strip_tags($arr[7])));
					$arrLead['telefone'] = $this->arrayVal(explode(" (", trim(strip_tags($arr[10]))), 0);

					$msgWats = "";
					if($arr[10]){
						$msgWats = " (".$this->arrayVal(explode(" (", trim(strip_tags($arr[10]))), 1);
					}
					$msg = trim(strip_tags($arr[15])).$msgWats;

					$arrLead['id_usuario'] = $this->getFilaVendedoresPorDia($arrLead['email']);
					$arrLead['origem'] = 1;
					$arrLead['valor'] = str_replace("R$ ", "", str_replace(",", ".", str_replace(".", "", $valor)));
					$arrLead['data'] = @date('Y-m-d H:i:s');
					$arrLead['imap_origem'] = 1;
					$arrLead['atualizacao'] = 1;
					
					$arrLead['atividades'] = "Veículo de interesse\nModelo: ".$modelo."\nAno: ".$anoModelo."\nValor: ".$valor."\n\n".$msg;

					if($emailAnterior != $arrLead['email'] || $telefoneAnterior != $arrLead['telefone']){
						$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
						$idFluxo = $dbFluxoClientes->add($arrLead);
						
						$emailAnterior = $arrLead['email'];
						$telefoneAnterior = $arrLead['telefone'];
					}
			
			
					$add = true;


				/////MEU CARRO NOVO///////
				}elseif(strpos($arrHead['subject'], "proposta") !== false && strpos($arrHead['subject'], "Chegou") !== false && $arrHead['from'][0]->personal == "MeuCarroNovo" && $arrHead['from'][0]->host != $arrHead['reply_to'][0]->host){

					$arr = explode("table", imap_qprint(imap_fetchbody($caixaEmail, $i, 1)));
					$modelo = trim(str_replace('br class="visible" />', "", $this->arrayVal(explode("<", $arr[27]), 5)));
					$ano = trim(str_replace('strong>', "", $this->arrayVal(explode("<", $arr[27]), 7)));
					$cor = trim(str_replace('strong>', "", $this->arrayVal(explode("<", $arr[27]), 13)));
					$portas = trim(str_replace('strong>', "", $this->arrayVal(explode("<", $arr[27]), 16)));
					$comb = trim(str_replace('strong>', "", $this->arrayVal(explode("<", $arr[27]), 19)));
					$cambio = trim(str_replace('strong>', "", $this->arrayVal(explode("<", $arr[27]), 22)));
					$placa = trim(str_replace('strong>', "", $this->arrayVal(explode("<", $arr[27]), 25)));
					$msg = trim(str_replace("p>", "", $this->arrayVal(explode("<", $arr[29]), 11)));
					$valor = trim(end(explode(">",$this->arrayVal(explode("<", $arr[25]),7))));
					
					
					$arrLead['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
					$arrLead['nome'] = trim(str_replace("strong>", "", $this->arrayVal(explode("<", $arr[29]), 7)));
					$arrLead['telefone'] = trim(str_replace("br />", "", $this->arrayVal(explode("<", $arr[29]), 8)));
					$arrLead['email'] = strtolower(trim(str_replace("br />", "", $this->arrayVal(explode("<", $arr[29]), 9))));
					$arrLead['id_usuario'] = $this->getFilaVendedoresPorDia($arrLead['email']);
					if($_SESSION['sessionUser']['id_empresa'] == 3){
						$arrLead['origem'] = 24;
					}elseif($_SESSION['sessionUser']['id_empresa'] == 239){
						$arrLead['origem'] = 27;
					}
					$arrLead['valor'] = trim(str_replace("R$ ", "", str_replace(",", ".", str_replace(".", "", $valor))));
					$arrLead['data'] = @date('Y-m-d H:i:s');
					$arrLead['imap_origem'] = 1;
					$arrLead['atualizacao'] = 1;
					$arrLead['atividades'] = "VEÍCULO DE INTERESSE\nModelo: ".$modelo."\nAno: ".$ano."\nCor: ".$cor."\nPortas: ".$portas."\nCombut&iacute;vel: ".$comb."\nC&acirc;mbio: ".$cambio."\nValor: ".$valor."\nPlaca: ".$placa."\n\n".$msg;
					
					$dbVeiculos = new Application_Model_DbTable_Veiculos();
			
					$arrVeiculo = $dbVeiculos->getVeiculoPorPlaca($placa);
			
					if($emailAnterior != $arrLead['email'] || $telefoneAnterior != $arrLead['telefone']){
						$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
						$idFluxo = $dbFluxoClientes->add($arrLead);
						
						$emailAnterior = $arrLead['email'];
						$telefoneAnterior = $arrLead['telefone'];
					}
			
					$add = true;
			
					if($idFluxo){
						$arrVeiculoFluxo['id_fluxo_cliente'] = $idFluxo;
						$arrVeiculoFluxo['veiculo'] = current(explode(" ", $arrVeiculo[0]['modelo']));
						$arrVeiculoFluxo['ano_modelo'] = $arrVeiculo[0]['ano_modelo'];
						$arrVeiculoFluxo['estoque'] = 1;
			
						$dbVeiculosFluxo = new Application_Model_DbTable_VeiculosFluxo();
						$dbVeiculosFluxo->add($arrVeiculoFluxo);
					}
				
				///////Mercado Livre//////				
				}elseif(strpos($arrHead['subject'], "Fizeram") !== false && strpos($arrHead['subject'], "anuncio") !== false && $arrHead['from'][0]->personal == "Mercado Livre" && $arrHead['reply_to'][0]->mailbox == "nao-responder"){

					$arr = explode("table", imap_qprint(imap_fetchbody($caixaEmail, $i, 1)));
					$modelo = trim(str_replace("</a", "", $this->arrayVal(explode(">", $arr[10]),7)));
					
					$arrLead['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
					$arrLead['nome'] = trim(str_replace("</span", "", $this->arrayVal(explode(">", $arr[18]),12)));
					$arrLead['email'] = strtolower(trim(str_replace("</a", "", $this->arrayVal(explode(">", $arr[18]),20))));
					$arrLead['id_usuario'] = $this->getFilaVendedoresPorDia($arrLead['email']);
					$arrLead['telefone'] = trim(str_replace("</span", "", $this->arrayVal(explode(">", $arr[18]),28)));
					$arrLead['atividades'] = "VEÍCULO DE INTERESSE: ".$modelo."\n\n".trim(str_replace("</span", "", $this->arrayVal(explode(">", $arr[14]),7)));;
					if($_SESSION['sessionUser']['id_empresa'] == 3){
						$arrLead['origem'] = 43;
					}elseif($_SESSION['sessionUser']['id_empresa'] == 239){
						$arrLead['origem'] = 26;
					}
					$arrLead['data'] = @date('Y-m-d H:i:s');
					$arrLead['imap_origem'] = 1;
					$arrLead['atualizacao'] = 1;
					
					$arrLead['telefone'] = str_replace(")", "", str_replace("(", "", str_replace(".", "", str_replace("-", "", $arrLead['telefone']))));
					
					if(substr($arrLead['telefone'],0,1) == "0"){
						$arrLead['telefone'] = substr($arrLead['telefone'],1);
					}
					
					if($arrLead['telefone'] && strlen($arrLead['telefone']) <= 9){
						$arrLead['telefone'] = "(00) ".$arrLead['telefone'];
					}

					if($emailAnterior != $arrLead['email'] || $telefoneAnterior != $arrLead['telefone']){
						$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
						$dbFluxoClientes->add($arrLead);
						
						$emailAnterior = $arrLead['email'];
						$telefoneAnterior = $arrLead['telefone'];
					}
					$add = true;

				///////Select Veículos//////
				}elseif(strpos($arrHead['subject'], "Interesse") !== false && strpos($arrHead['subject'], "enviado") !== false && $arrHead['from'][0]->mailbox == "webmaster" && $arrHead['from'][0]->host == "selectveiculos.com.br"){

					$arr = explode("table", imap_qprint(imap_fetchbody($caixaEmail, $i, 1)));
					$modelo = trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 20))));
					$placa = trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 28))));
					$ano = trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 36))));
					$cor = trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 44))));
					$valor = trim(str_replace("R$ ","", trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 52))))));
					$msg = trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 89))));
					
					if(trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 97)))) == "Sim"){
						$veiculoTroca = "O cliente deseja usar um veículo em troca.";
					}else{
						$veiculoTroca = "O cliente não deseja usar um veículo em troca.";
					}
					
					if(trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 105)))) == "Sim"){
						$financiar = "O cliente deseja financiar.";
					}else{
						$financiar = "O cliente não deseja financiar.";
					}
					
					
					$arrLead['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
					$arrLead['nome'] = trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 64))));
					$arrLead['email'] = strtolower(trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 72)))));
					$arrLead['id_usuario'] = $this->getFilaVendedoresPorDia($arrLead['email']);
					$arrLead['telefone'] = trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 81))));
					if($_SESSION['sessionUser']['id_empresa'] == 3){
						$arrLead['origem'] = 14;
					}elseif($_SESSION['sessionUser']['id_empresa'] == 239){
						$arrLead['origem'] = 25;
					}
					$arrLead['data'] = @date('Y-m-d H:i:s');
					$arrLead['imap_origem'] = 1;
					$arrLead['atualizacao'] = 1;
					$arrLead['atividades'] = "VEÍCULO DE INTERESSE: ".$modelo." - ".$ano." - ".$cor." - R$".$valor." - ".$placa."\n\n".$msg."\n\n".$veiculoTroca."\n".$financiar;
					$arrLead['valor'] = $valor;

					if($arrLead['telefone'] && strlen($arrLead['telefone']) <= 9){
						$arrLead['telefone'] = "(00) ".$arrLead['telefone'];
					}


					
					$dbVeiculos = new Application_Model_DbTable_Veiculos();
			
					$arrVeiculo = $dbVeiculos->getVeiculoPorPlaca($placa);
			
					if($ultimoEmail != $arrHead['reply_toaddress']){
						
						$ultimoEmail = $arrHead['reply_toaddress'];

						if($emailAnterior != $arrLead['email'] || $telefoneAnterior != $arrLead['telefone']){
							$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
							$idFluxo = $dbFluxoClientes->add($arrLead);
						
							$emailAnterior = $arrLead['email'];
							$telefoneAnterior = $arrLead['telefone'];
						}
				
						$add = true;


				
						if($idFluxo){
							$arrVeiculoFluxo['id_fluxo_cliente'] = $idFluxo;
							$arrVeiculoFluxo['veiculo'] = current(explode(" ", $arrVeiculo[0]['modelo']));
							$arrVeiculoFluxo['ano_modelo'] = $arrVeiculo[0]['ano_modelo'];
							$arrVeiculoFluxo['estoque'] = 1;
				
							$dbVeiculosFluxo = new Application_Model_DbTable_VeiculosFluxo();
							$dbVeiculosFluxo->add($arrVeiculoFluxo);
						}

					}

				///////Meu Car//////
				}elseif(strpos($arrHead['subject'], "Proposta") !== false && strpos($arrHead['subject'], "Enviado") !== false && $arrHead['from'][0]->mailbox == "proposta" && $arrHead['from'][0]->host == "sistemameucar.com.br"){
					
					$arr = explode("table", imap_qprint(imap_fetchbody($caixaEmail, $i, 1)));
					$modelo = trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 20))));
					$placa = trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 28))));
					$ano = trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 36))));
					$cor = trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 44))));
					$valor = trim(str_replace("R$ ","", trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 52))))));
					$msg = trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 89))));
					
					if(trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 97)))) == "Sim"){
						$veiculoTroca = "O cliente deseja usar um veículo em troca.";
					}else{
						$veiculoTroca = "O cliente não deseja usar um veículo em troca.";
					}
					
					if(trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 105)))) == "Sim"){
						$financiar = "O cliente deseja financiar.";
					}else{
						$financiar = "O cliente não deseja financiar.";
					}
					
					
					$arrLead['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
					$arrLead['nome'] = trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 64))));
					$arrLead['email'] = strtolower(trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 72)))));
					$arrLead['id_usuario'] = $this->getFilaVendedoresPorDia($arrLead['email']);
					$arrLead['telefone'] = trim(end(explode(">", $this->arrayVal(explode("<", $arr[1]), 81))));
					if($_SESSION['sessionUser']['id_empresa'] == 3){
						$arrLead['origem'] = 46;
					}elseif($_SESSION['sessionUser']['id_empresa'] == 239){
						$arrLead['origem'] = 70;
					}
					$arrLead['data'] = @date('Y-m-d H:i:s');
					$arrLead['imap_origem'] = 1;
					$arrLead['atualizacao'] = 1;
					$arrLead['atividades'] = "VEÍCULO DE INTERESSE: ".$modelo." - ".$ano." - ".$cor." - R$".$valor." - ".$placa."\n\n".$msg."\n\n".$veiculoTroca."\n".$financiar;
					$arrLead['valor'] = $valor;

					if($arrLead['telefone'] && strlen($arrLead['telefone']) <= 9){
						$arrLead['telefone'] = "(00) ".$arrLead['telefone'];
					}
					
					$dbVeiculos = new Application_Model_DbTable_Veiculos();
			
					$arrVeiculo = $dbVeiculos->getVeiculoPorPlaca($placa);
			
					if($ultimoEmail != $arrHead['reply_toaddress']){

						if($emailAnterior != $arrLead['email'] || $telefoneAnterior != $arrLead['telefone']){
							$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
							$idFluxo = $dbFluxoClientes->add($arrLead);
							
							$emailAnterior = $arrLead['email'];
							$telefoneAnterior = $arrLead['telefone'];
						}
				
						$add = true;
				
						if($idFluxo){
							$arrVeiculoFluxo['id_fluxo_cliente'] = $idFluxo;
							$arrVeiculoFluxo['veiculo'] = current(explode(" ", $arrVeiculo[0]['modelo']));
							$arrVeiculoFluxo['ano_modelo'] = $arrVeiculo[0]['ano_modelo'];
							$arrVeiculoFluxo['estoque'] = 1;
				
							$dbVeiculosFluxo = new Application_Model_DbTable_VeiculosFluxo();
							$dbVeiculosFluxo->add($arrVeiculoFluxo);
						}

					}

				///////Emails clientes avulsos//////
				//}elseif(false){
				}else{
					
					foreach($arrClients as $clients){
						
						if($clients['email'] == $from){

							$arrTemp = explode("table",imap_qprint(imap_fetchbody($caixaEmail, $i, 2)));
							$arr = explode("div ",$arrTemp[0]);
							$msg = substr(str_replace('dir="ltr">', "", $arr[1]), 0, -1);
							$msg =  str_replace('<br clear="all">', '', str_replace("<div>", "", str_replace("</div>", "", str_replace("</div><div>", "\n", str_replace("<br>", "\n", $msg)))));
							$msg =  str_replace('dir="auto">', '', $msg);

							$clients['atividades'] = $clients['atividades']."\n\n- - - - - - - - - - - - ".@date('d/m/Y H:i:s').", ".$clients['nome']." - - - - - - - - - - - - -\n\n".$msg;
							$clients['atualizacao'] = 1;
							$clients['resposta_cliente'] = 1;
						
							if($clients['id']){
								unset($clients['descricao']);
								unset($clients['nome_usuario']);
								$dbFluxoClientes->edt($clients['id'], $clients);
							}

						}
						
					}

				}
				
				if($add){
					//var_export($arrLead);
				}

			}

		}
		
		imap_close($caixaEmail);

	}
	
	private function getFilaVendedoresPorDia($email){
		
		$dbUsuarios = new Application_Model_DbTable_Usuarios();
		$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
		
		$dados['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$dados['imap_origem'] = true;
		$dados['email_cliente'] = $email;
		
		$arrFluxoCliente = $dbFluxoClientes->getClientesFluxo($dados);

		if($arrFluxoCliente){
			
			if($dbUsuarios->getVendedorAptoReceberEmails($arrFluxoCliente[0]['id_usuario'])){
				$arr['id'] = $arrFluxoCliente[0]['id_usuario'];
			}else{
				$arrFluxoCliente = $dbFluxoClientes->getUltimoVendedorClientesFluxo(@date('Y-m-d'));

				$arrVendedores = $dbUsuarios->getVendedoresFilaEmails();

				foreach($arrVendedores as $key=>$vendedor){
					
					if($arrVendedores[$key]['id'] == $arrFluxoCliente[0]['id_usuario']){
						if($arrVendedores[$key+1]['id']){
							$arr = $arrVendedores[$key+1];
						}
					}
					
				}
			}

		}else{

			$arrFluxoCliente = $dbFluxoClientes->getUltimoVendedorClientesFluxo(@date('Y-m-d'));

			$arrVendedores = $dbUsuarios->getVendedoresFilaEmails();

			foreach($arrVendedores as $key=>$vendedor){
				
				if($arrVendedores[$key]['id'] == $arrFluxoCliente[0]['id_usuario']){
					if($arrVendedores[$key+1]['id']){
						$arr = $arrVendedores[$key+1];
					}
				}
				
			}

		}
		
		if(!$arr){
			$arr = $arrVendedores[0];
		}

		return $arr['id'];

	}
	
	
	private function getFilaVendedores($email){
		
		$dbUsuarios = new Application_Model_DbTable_Usuarios();
		$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();

		$dados['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$dados['imap_origem'] = true;
		$dados['email_cliente'] = $email;
		
		$arrFluxoCliente = $dbFluxoClientes->getClientesFluxo($dados);
		
		if($arrFluxoCliente){
			
			if($dbUsuarios->getVendedorAptoReceberEmails($arrFluxoCliente[0]['id_usuario'])){
				$arr['id'] = $arrFluxoCliente[0]['id_usuario'];
			}else{
				$arrFluxoCliente = $dbFluxoClientes->getUltimoVendedorClientesFluxo(@date('Y-m-d'));

				$arrVendedores = $dbUsuarios->getVendedoresFilaEmails();

				foreach($arrVendedores as $key=>$vendedor){
					
					if($arrVendedores[$key]['id'] == $arrFluxoCliente[0]['id_usuario']){
						if($arrVendedores[$key+1]['id']){
							$arr = $arrVendedores[$key+1];
						}
					}
					
				}
			}
			
		}else{
			
			unset($dados['email_cliente']);
			
			$arrFluxoCliente = $dbFluxoClientes->getClientesFluxo($dados);
			
			$arrVendedores = $dbUsuarios->getVendedoresFilaEmails();
		
			foreach($arrVendedores as $key=>$vendedor){
				
				if($arrVendedores[$key]['id'] == $arrFluxoCliente[0]['id_usuario']){
					if($arrVendedores[$key+1]['id']){
						$arr = $arrVendedores[$key+1];
					}else{
						$arr = current($arrVendedores);
					}
				}
				
			}
		}

		if(!$arr){
			$arr = current($arrVendedores);
		}
		
		return $arr['id'];

	}
	
	
	public function fluxoContatoClientesAction(){

		$dbUsuarios = new Application_Model_DbTable_Usuarios();
		
		//if($_SESSION['sessionUser']['id_empresa'] == 3){
			//$this->getEmailImap();
		//}
		
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
	
	private function conectaEmail(){
		
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
		
		//return imap_open("{imap.superheros.provisorio.ws:143/novalidate-cert}", $arrEmpresa[0]['email_imap'], $arrEmpresa[0]['senha_email_imap']);

		return imap_open("{imap.gmail.com:993/imap/ssl}", $arrEmpresa[0]['email_imap'], $arrEmpresa[0]['senha_email_imap']);
		//return imap_open("{imap.gmail.com:993/imap/ssl}", "contato@selectveiculos.com.br", $arrEmpresa[0]['senha_email_imap']);

	}
	
	
	public function addClienteFluxoAutoSalvarAction(){

		$this->view->id_usuario = $_SESSION['sessionUser']['id'];
		$dbMotivos = new Application_Model_DbTable_Motivos();
		$dbUsuarios = new Application_Model_DbTable_Usuarios();
		
		$strVendedores = "";
		$idVendedor = "";

		if($this->_getParam('id')){
		
			$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
			$dbVeiculosFluxo = new Application_Model_DbTable_VeiculosFluxo();
		
			$dados = $dbFluxoClientes->getClienteFluxo($this->_getParam('id'));
			$dadosVeiculos = $dbVeiculosFluxo->getVeiculoFluxo($dados[0]['id']);
			
			foreach($dbUsuarios->getVendedores() as  $vendedor){
				if($vendedor['id'] != $idVendedor){
					if($dados[0]['id_usuario'] == $vendedor['id']){
						$strVendedores .= "<option selected='selected' value='".$vendedor['id']."'>".$vendedor['nome']."</option>";						
					}else{
						$strVendedores .= "<option value='".$vendedor['id']."'>".$vendedor['nome']."</option>";						
					}
				}
				$idVendedor = $vendedor['id'];
			}

			$this->view->dados = $dados[0];
			$this->view->dadosVeiculos = $dadosVeiculos;

		}else{

			foreach($dbUsuarios->getVendedores() as  $vendedor){
				if($vendedor['id'] != $idVendedor){
					if($_SESSION['sessionUser']['id'] == $vendedor['id']){
						$strVendedores .= "<option selected='selected' value='".$vendedor['id']."'>".$vendedor['nome']."</option>";
					}else{
						$strVendedores .= "<option value='".$vendedor['id']."'>".$vendedor['nome']."</option>";						
					}
				}
				$idVendedor = $vendedor['id'];
			}

		}

		$arrMotivos = $dbMotivos->getMotivos();
		$this->view->arrMotivos = $arrMotivos;
		$this->view->strVendedores = $strVendedores;

	}	
	
	
	public function listaClienteFluxoAction(){

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
	
	
	public function addClienteFluxoAction(){

		$this->view->id_usuario = $_SESSION['sessionUser']['id'];

		if($this->_getParam('id')){
		
			$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
			$dbVeiculosFluxo = new Application_Model_DbTable_VeiculosFluxo();
		
			$dados = $dbFluxoClientes->getClienteFluxo($this->_getParam('id'));
			$dadosVeiculos = $dbVeiculosFluxo->getVeiculoFluxo($dados[0]['id']);

			$this->view->dados = $dados[0];
			$this->view->dadosVeiculos = $dadosVeiculos;

		}

	}
	
	
	
	public function edtAction(){
		
		$this->view->id = $this->_getParam('id');
	
	}
	
	public function impressaoAction(){
		
		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
		/*
		$dbClientes = new Application_Model_DbTable_Clientes();
		
		if($this->getRequest()->isPost()){
		
			unset($_POST['pessoa_fisica']);
			$dados = $_POST;
			$dados['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dados['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			if($dados['data_expedicao'] != ""){
			
				$arrTmpData = explode("/",$dados['data_expedicao']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['data_expedicao'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['nascimento'] != ""){
			
				$arrTmpData = explode("/",$dados['nascimento']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['nascimento'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['data_admissao'] != ""){
			
				$arrTmpData = explode("/",$dados['data_admissao']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['data_admissao'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['data_admissao_anterior'] != ""){
			
				$arrTmpData = explode("/",$dados['data_admissao_anterior']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['data_admissao_anterior'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['data_demissao_anterior'] != ""){
			
				$arrTmpData = explode("/",$dados['data_demissao_anterior']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['data_demissao_anterior'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['nascimento_conjuge'] != ""){
			
				$arrTmpData = explode("/",$dados['nascimento_conjuge']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['nascimento_conjuge'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['data_admissao_conjuge'] != ""){
			
				$arrTmpData = explode("/",$dados['data_admissao_conjuge']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['data_admissao_conjuge'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['cliente_desde'] != ""){
			
				$arrTmpData = explode("/",$dados['cliente_desde']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['cliente_desde'] = implode("-",$arrTmpData);
			
			}
		
			$dbClientes->update($dados, "id = " . $this->_getParam('id'));
		
		}
		*/
		$this->view->arrDados = $_POST;
	
	}
	
	public function ajaxAction(){

		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');

		if($this->_getParam('fn') == 'getByCpfNome'){

			if(empty($_SESSION['sessionUser']['id_empresa'])) return;

			$dbCliente = new Application_Model_DbTable_Clientes();

			$arrC = $dbCliente->fetchAll("(cpf LIKE  '".$this->_getParam('f')."%' OR nome LIKE  '".$this->_getParam('f')."%') AND id_empresa = ".$_SESSION['sessionUser']['id_empresa']);

			$callback = $this->_getParam('callback') ? $this->_getParam('callback') : 'populaCamposCliente';

			foreach($arrC as $c){
				echo "<li> <a href=\"#\" onclick=\"".$callback."(".$c['id'].");esconde($(this).parent().parent().parent())\">".$c['nome']." - ".$c['cpf']."</a></li>";
			}
			
		}elseif($this->_getParam('fn') == 'getById'){
			
			$dbCliente = new Application_Model_DbTable_Clientes();
			
			$arrC = $dbCliente->fetchAll("id = " . $this->_getParam('f'));

			foreach($arrC as $c){
			
				foreach($c as $k => $v){

					echo $k.":".$v."|";
				
				}
			
			}

		}elseif($this->_getParam('fn') == 'busca_cep'){

			$reg = file_get_contents("https://viacep.com.br/ws/".$this->_getParam('cep')."/json/");
			$arr = json_decode($reg, true);

			$dados['sucesso']     = 1;
			$dados['rua']     = $arr['logradouro'];
			$dados['bairro']  = $arr['bairro'];
			$dados['cidade']  = $arr['localidade'];
			$dados['estado']  = $arr['uf'];

			
			echo json_encode($dados);


		}elseif($this->_getParam('fn') == 'autosave'){

			$dbCliente = new Application_Model_DbTable_Clientes();
		
			$duploDados = explode("|-|",$this->_getParam('dados_cliente'));
			
			foreach($duploDados as $dados){
				
				$arrDados = explode("|",$dados);
				
				$arrDadosClientes[$arrDados[0]] = $arrDados[1];
			
			}
			
			$arrDadosClientes['nome'] = trim($arrDadosClientes['nome']);
			
			$arrDadosClientes['hora_alteracao'] = @date("Y-m-d H:i:s");
			$arrDadosClientes['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
		
			if($arrDadosClientes['primeira_consulta']){
				
				
				unset($arrDadosClientes['buttom_nova_negociacao']);
				unset($arrDadosClientes['button_imp']);
				unset($arrDadosClientes['primeira_consulta']);
				unset($arrDadosClientes['id_cliente']);
				$id = $dbCliente->insert($arrDadosClientes);
				
				echo $id;
				
				

			}else{
				
				if($arrDadosClientes['id_cliente']){
				
					unset($arrDadosClientes['buttom_nova_negociacao']);
					unset($arrDadosClientes['button_imp']);
					unset($arrDadosClientes['primeira_consulta']);
					$idCliente = $arrDadosClientes['id_cliente'];
					unset($arrDadosClientes['id_cliente']);
					$dbCliente->update($arrDadosClientes, "id = ".$idCliente);
				
				}
			
			}

		}elseif($this->_getParam('fn') == 'verifica_cpf'){

			if(empty($_SESSION['sessionUser']['id_empresa'])) return;

			$dbCliente = new Application_Model_DbTable_Clientes();

			$status = $dbCliente->getCpfExistente($this->_getParam('cpf'), $_SESSION['sessionUser']['id_empresa']);

			if(isset($status[0]['id'])){
	
				echo $status[0]['id'];
				
			}

		}elseif($this->_getParam('fn') == 'busca_cliente'){

			$dbCliente = new Application_Model_DbTable_Clientes();
		
			$arrClientes = $dbCliente->_get(array("id"=>$this->_getParam('id_cliente')));
			
			$strCliente = "";

			if(count($arrClientes) > 0){

				$arrCliente = $arrClientes[0];
				
				foreach($arrCliente as $chave=>$cliente){
					
					$strCliente .= $chave."|".$cliente."|-|";
				
				}

			}
			
			echo $strCliente;

		}elseif($this->_getParam('fn') == 'busca_negociaçoes'){

			$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
		
			$arrNegoClientes = $dbNegociacoes->getNegociacoesCliente($this->_getParam('id_cliente'));
			
			if($arrNegoClientes){
			
				$strNegoCliente = "<table id='tableNego'>";
				
				$strNegoCliente .= "<tr><th colspan='3'>NEGOCIAÇÕES</th></tr>";
				$strNegoCliente .= "<tr><th>MODELO</th><th>VALOR VENDA</th><th>DATA</th></tr>";
				
				foreach($arrNegoClientes as $negoCliente){
				
					$arrData = explode(" ",$negoCliente['data_concretizacao']);
					
					if($arrData[0] == "0000-00-00"){
						
						$strData = "Não Concretizada";
					
					}else{
					
						$strData = implode("/",array_reverse(explode("-",$arrData[0])));
					
					}
				
					$url = "javascript:window.open('/negociacoes/edt/id/".$negoCliente['id']."')";
					
					$strNegoCliente .= "<tr><td onclick=$url>".$negoCliente['modelo']."</td><td onclick=$url>R$ ".money_format("%i",$negoCliente['valor_venda'])."</td><td onclick=$url>".$strData."</td></tr>";
				
				}
				
				echo $strNegoCliente."</table>";
			
			}else{
				
				
			
			}
			
		
		}elseif($this->_getParam('fn') == 'busca_negociaçoes_compra'){

			$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
		
			$arrNegoClientes = $dbNegociacoes->getNegociacoesClienteCompra($this->_getParam('id_cliente'));
			
			if($arrNegoClientes){
			
				$strNegoCliente = "<table id='tableNego'>";
				
				$strNegoCliente .= "<tr><th colspan='3'>NEGOCIAÇÕES COMPRA / CONSIGNAÇÃO</th></tr>";
				$strNegoCliente .= "<tr><th>MODELO</th><th>VALOR VENDA</th><th>DATA</th></tr>";
				
				foreach($arrNegoClientes as $negoCliente){
				
					$arrData = explode(" ",$negoCliente['data_abertura']);
					
					if($arrData[0] == "0000-00-00"){
						
						$strData = "Não Concretizada";
					
					}else{
					
						$strData = implode("/",array_reverse(explode("-",$arrData[0])));
					
					}
				
					$url = "javascript:window.open('/negociacoes/edt-compra/id/".$negoCliente['id']."')";
					
					$strNegoCliente .= "<tr><td onclick=$url>".$negoCliente['modelo']."</td><td onclick=$url>R$ ".money_format("%i",$negoCliente['valor_venda'])."</td><td onclick=$url>".$strData."</td></tr>";
				
				}
				
				echo $strNegoCliente."</table>";
			
			}else{
				
				
			
			}
			
			
		}elseif($this->_getParam('fn') == 'busca_origem'){
			
			$dbOrigemClientes = new Application_Model_DbTable_OrigemClientes();
			
			$arrOrigem = $dbOrigemClientes->_getOrigem(array("id_empresa"=>$_SESSION['sessionUser']['id_empresa'], "noDefault"=>true));
			
			echo "<option value=''>Todos</option>";
			
			foreach($arrOrigem as $origem){

				if($origem['exibir'] == 1){
				
					if($origem['id'] != 7 && $origem['id'] != 9 && $origem['id'] != 6 && $origem['id'] != 8){
					
						echo "<option value='".$origem['id']."'>".$origem['descricao']."</option>";
					
					}
					
				}
			
			}
		

		
		}elseif($this->_getParam('fn') == 'edita_origem'){
			
	
			$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
			
			$arrDados['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			//$arrDados['id_usuario'] = $_SESSION['sessionUser']['id'];
			$arrDados['nome'] = $this->_getParam('nome');
			$arrDados['origem'] = $this->_getParam('id_origem');
			$arrDados['resultado'] = $this->_getParam('resultado');
			$arrDados['telefone'] = $this->_getParam('tel1');
			$arrDados['telefone2'] = $this->_getParam('tel2');
			$arrDados['celular'] = $this->_getParam('cel');
			$arrDados['email'] = $this->_getParam('email');
			$idFluxo = $this->_getParam('id_fluxo');
			$arrDados['data'] = @date("Y-m-d");
			$arrDados['gerado_negociacao'] = 1;

			if($idFluxo && $idFluxo != 0){
			
				$dbFluxoClientes->edt($idFluxo,$arrDados);

				$dbVeiculosFluxo = new Application_Model_DbTable_VeiculosFluxo();

				$arrDadosVeiculo['veiculo'] = current(explode(" ",$this->_getParam('veiculo_interesse')));
				$arrDadosVeiculo['ano_modelo'] = $this->_getParam('ano_modelo');
				$arrDadosVeiculo['estoque'] = 1;

				if($dbVeiculosFluxo->edt($idFluxo, $arrDadosVeiculo)){
				
					echo "sucesso";
				
				}
			
			}

		}elseif($this->_getParam('fn') == 'salva_origem'){
			
			
			$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
			
			if(!$dbFluxoClientes->getClienteFluxoIdNegociacao($this->_getParam('id_negociacao'))){
				
				$arrDados['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
				
				if($this->_getParam('id_usuario')){
					$arrDados['id_usuario'] = $this->_getParam('id_usuario');
				}else{
					$arrDados['id_usuario'] = $_SESSION['sessionUser']['id'];
				}
				
				$arrDados['nome'] = $this->_getParam('nome');
				$arrDados['origem'] = $this->_getParam('id_origem');
				$arrDados['resultado'] = $this->_getParam('resultado');
				$arrDados['telefone'] = $this->_getParam('tel1');
				$arrDados['telefone2'] = $this->_getParam('tel2');
				$arrDados['celular'] = $this->_getParam('cel');
				$arrDados['email'] = $this->_getParam('email');
				$arrDados['gerado_negociacao'] = $this->_getParam('id_negociacao');
				if($this->_getParam('data_concretizacao')){
					$arrDados['data'] = implode("-",array_reverse(explode("/",current(explode(" ",$this->_getParam('data_concretizacao'))))))." ".end(explode(" ",$this->_getParam('data_concretizacao')));
				}else{
					$arrDados['data'] = @date("Y-m-d H:i:s");
				}

				$id = $dbFluxoClientes->add($arrDados);
			
				if($id){

					$dbVeiculosFluxo = new Application_Model_DbTable_VeiculosFluxo();
					
					$arrDadosVeiculo['id_fluxo_cliente'] = $id;
					$arrDadosVeiculo['veiculo'] = current(explode(" ",$this->_getParam('veiculo_interesse')));
					$arrDadosVeiculo['ano_modelo'] = $this->_getParam('ano_modelo');
					$arrDadosVeiculo['estoque'] = 1;
					
					
					
					if($dbVeiculosFluxo->add($arrDadosVeiculo)){
					
						echo "sucesso";
					
					}
				
				}
				
			}
			
			
		}elseif($this->_getParam('fn') == 'busca_origem_id'){

			$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
			$arrFluxo = $dbFluxoClientes->getFluxoNome($this->_getParam('nome'));
			
			if(!$arrFluxo[0]['origem']){
				$dbClientes = new Application_Model_DbTable_Clientes();
				$arrFluxo = $dbClientes->getClienteNome($this->_getParam('nome'));
				echo "0|".$arrFluxo[0]['origem'];
			}else{
				echo $arrFluxo[0]['id']."|".$arrFluxo[0]['origem'];
			}


		}elseif($this->_getParam('fn') == 'salva_cadastro'){

			$dados['nome'] = $this->_getParam('nome');
			$dados['id_usuario'] = $this->_getParam('id_usuario');
			$dados['email'] = $this->_getParam('email');
			$dados['profissao'] = $this->_getParam('profissao');
			$dados['resultado'] = $this->_getParam('resultado');
			$dados['motivo'] = $this->_getParam('motivo');
			$dados['motivo_pre'] = $this->_getParam('motivo_pre');
			
			if($this->_getParam('data')){
				$dados['data'] = implode("-",array_reverse(explode("/",current(explode(" ",$this->_getParam('data'))))))." ".end(explode(" ",$this->_getParam('data')));				
			}else{
				$dados['data'] = @date('Y-m-d H:i:s');
			}
			
			$dados['ultima_visualizacao'] = @date('Y-m-d');
			$dados['origem'] = $this->_getParam('origem');
			$dados['telefone'] = $this->_getParam('telefone');
			$dados['telefone2'] = $this->_getParam('telefone2');
			$dados['celular'] = $this->_getParam('celular');
			$dados['valor'] = str_replace(",",".",str_replace(".","",$this->_getParam('valor')));
			$dados['entrada'] = str_replace(",",".",str_replace(".","",$this->_getParam('entrada')));
			$dados['parcela'] = str_replace(",",".",str_replace(".","",$this->_getParam('parcela')));
			$dados['veiculo_troca'] = $this->_getParam('veiculo_troca');
			$dados['ano_modelo_troca'] = $this->_getParam('ano_modelo_troca');
			$dados['obs'] = $this->_getParam('obs');
			$dados['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			
			if($this->_getParam('id_vendedor')){
				$dados['id_usuario'] = $this->_getParam('id_vendedor');
			}else{
				$dados['id_usuario'] = $_SESSION['sessionUser']['id'];
			}
			
			if($_SESSION['sessionUser']['id_empresa']){

				$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
				$idFluxoCliente = $dbFluxoClientes->add($dados);
				
			}

			if($idFluxoCliente){
			
				$erro = false;
			
				$dbVeiculosFluxo = new Application_Model_DbTable_VeiculosFluxo();
			
				$arrInteresse = substr($this->_getParam('veiculos_interesse'), 0, -2);
				
				$arrTemp0 = explode("||", $arrInteresse);
				
				foreach($arrTemp0 as $temp0){
					
					if($temp0 != ""){
					
						$arrTemp1 = explode("|",$temp0);
						
						$arrVeiculoInteresse['id_fluxo_cliente'] = $idFluxoCliente;
						$arrVeiculoInteresse['veiculo'] = end(explode(":",$arrTemp1[0]));
						$arrVeiculoInteresse['ano_modelo'] = end(explode(":",$arrTemp1[1]));
						$arrVeiculoInteresse['estoque'] = end(explode(":",$arrTemp1[2]));
						
						
						
						if(!$dbVeiculosFluxo->add($arrVeiculoInteresse)){
							
							$erro = true;
						
						}
					
					}
				
				}
				
			}else{
			
				$erro = true;
			
			}
			
			if($erro){
				
				echo "erro";
			
			}else{
				
				echo $idFluxoCliente;
			
			}
			
			
		}elseif($this->_getParam('fn') == 'edita_cadastro'){

			$idFluxoCliente = $this->_getParam('id_cliente_fluxo');
			$dados['nome'] = $this->_getParam('nome');
			$dados['email'] = $this->_getParam('email');
			$dados['profissao'] = $this->_getParam('profissao');
			$dados['resultado'] = $this->_getParam('resultado');
			$dados['motivo'] = $this->_getParam('motivo');
			$dados['motivo_pre'] = $this->_getParam('motivo_pre');
			//$dados['data'] = implode("-",array_reverse(explode("/",$this->_getParam('data'))));
			$dados['ultima_visualizacao'] = @date('Y-m-d');
			$dados['origem'] = $this->_getParam('origem');
			$dados['telefone'] = $this->_getParam('telefone');
			$dados['telefone2'] = $this->_getParam('telefone2');
			$dados['celular'] = $this->_getParam('celular');
			$dados['valor'] = str_replace(",",".",str_replace(".","",$this->_getParam('valor')));
			$dados['entrada'] = str_replace(",",".",str_replace(".","",$this->_getParam('entrada')));
			$dados['parcela'] = str_replace(",",".",str_replace(".","",$this->_getParam('parcela')));
			$dados['veiculo_troca'] = $this->_getParam('veiculo_troca');
			$dados['ano_modelo_troca'] = $this->_getParam('ano_modelo_troca');
			$dados['obs'] = $this->_getParam('obs');
			//$dados['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			
			if($this->_getParam('id_vendedor')){
				$dados['id_usuario'] = $this->_getParam('id_vendedor');
			}
			
			
			$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
			
			$dbFluxoClientes->edt($idFluxoCliente, $dados);

			if($idFluxoCliente){

				$erro = false;
			
				$dbVeiculosFluxo = new Application_Model_DbTable_VeiculosFluxo();
				
				$dbVeiculosFluxo->delIdClienteFluxo($idFluxoCliente);
			
				$arrInteresse = substr($this->_getParam('veiculos_interesse'), 0, -2);

				$arrTemp0 = explode("||", $arrInteresse);
				
				foreach($arrTemp0 as $temp0){
					
					if($temp0 != ""){
					
						$arrTemp1 = explode("|",$temp0);
						
						$arrVeiculoInteresse['id_fluxo_cliente'] = $idFluxoCliente;
						$arrVeiculoInteresse['veiculo'] = end(explode(":",$arrTemp1[0]));
						$arrVeiculoInteresse['ano_modelo'] = end(explode(":",$arrTemp1[1]));
						$arrVeiculoInteresse['estoque'] = end(explode(":",$arrTemp1[2]));
						
						
						
						if(!$dbVeiculosFluxo->add($arrVeiculoInteresse)){
							
							$erro = true;
						
						}
					
					}
				
				}
				
			}else{
			
				$erro = true;
			
			}
			
			if($erro){
				
				echo "erro";
			
			}else{
				
				echo $idFluxoCliente;
			
			}

			
		}elseif($this->_getParam('fn') == 'log_whatsapp'){
			
			
			if($this->_getParam('id_cliente_fluxo')){

				$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();

				$arrFluxoCliente = $dbFluxoClientes->getClienteFluxo($this->_getParam('id_cliente_fluxo'));
			
				if($arrFluxoCliente){
					
					if(!$arrFluxoCliente[0]['data_tempo_resposta']){
						$dados['data_tempo_resposta'] = @date('Y-m-d H:i:s');
					}
					
					$dados['atividades'] = $arrFluxoCliente[0]['atividades']."\n\n- - - - - - ".$_SESSION['sessionUser']['nome']." entrou em contato via WhatsApp às: ".@date('H:i:s d/m/Y')." - - - - -\n\n".$this->_getParam('mensagem')."\n\n";
					
					$dbFluxoClientes->edt($arrFluxoCliente[0]['id'],$dados);
					
					echo $dados['atividades'];
					
				}
				
			}
		
		}elseif($this->_getParam('fn') == 'agenda_contato'){
			
			
			if($this->_getParam('id_cliente_fluxo')){
				
				$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();

				$arrFluxoCliente = $dbFluxoClientes->getClienteFluxo($this->_getParam('id_cliente_fluxo'));
			
				if($arrFluxoCliente){
					
					if($this->_getParam('data')){
						$dados['data_agendamento'] = implode("-",array_reverse(explode("/",$this->_getParam('data'))));
						$dados['atividades'] = $arrFluxoCliente[0]['atividades']."\n\n- - - - - - ".$_SESSION['sessionUser']['nome']." agendou contato para: ".$this->_getParam('data')." - - - - -\n\n";
					}else{
						$dados['data_agendamento'] = null;
						$dados['atividades'] = $arrFluxoCliente[0]['atividades']."\n\n- - - - - - ".$_SESSION['sessionUser']['nome']." cancelou o agendamento. - - - - -\n\n";
					}

					$dbFluxoClientes->edt($arrFluxoCliente[0]['id'],$dados);
					
					echo $dados['atividades'];
					
				}
				
			}
			

		}elseif($this->_getParam('fn') == 'lista_fluxo_cliente'){
			
			$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
			$dbVeiculosFluxo = new Application_Model_DbTable_VeiculosFluxo();
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
			
			if($_SESSION['sessionUser']['id_empresa'] == 3 || $_SESSION['sessionUser']['id_empresa'] == 239){
				//$this->getEmailImap();
			}

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

			if($this->_getParam('nome')){
				$dados['nome'] = $this->_getParam('nome');
			}

			if($this->_getParam('veiculo')){
				$dados['veiculo'] = $this->_getParam('veiculo');
			}

			$dados['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			//$dados['nome'] = $this->_getParam('nome');
			//$dados['veiculo'] = $this->_getParam('veiculo');
			$dados['data_inicial'] = implode("-",array_reverse(explode("/",$this->_getParam('data_inicial'))));
			$dados['data_final'] = implode("-",array_reverse(explode("/",$this->_getParam('data_final'))));


			$arrFluxo = $dbFluxoClientes->getClientesFluxo($dados);
			//var_export($arrFluxo);
			
			if(isset($dados['filtro']) && $dados['filtro'] == "Agendada"){
				$arrFluxoCliente = $arrFluxo;
			}else{
				$arrFluxoAgendada = $dbFluxoClientes->getClientesFluxoAgendado($dados);
				$arrFluxoCliente = array_merge($arrFluxoAgendada, $arrFluxo);
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
			$strFluxo .= "<table class='table' id='fluxo'><tr>
							<th colspan='2'>Nome</th>
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
	
				if($fluxoCliente['resultado'] != "Duplicado" && $fluxoCliente['resultado'] != "Desistiu" && $fluxoCliente['resultado'] != "Fechou" && $fluxoCliente['resultado'] != "Concorrente" && $fluxoCliente['resultado'] != "Agendado"){
					
					$arrDate = explode("-", $fluxoCliente['ultima_visualizacao']);
					if($fluxoCliente['ultima_visualizacao'] && mktime(0,0,0,$arrDate[1], $arrDate[2], $arrDate[0]) == mktime(0,0,0,@date('m'), @date('d')-4, @date('Y')) && $fluxoCliente['envia_email'] == 0){
						if($_SESSION['sessionUser']['id_empresa'] == 3 || $_SESSION['sessionUser']['id_empresa'] == 239){
							$color = "bgcolor='#FF7777'";
							$data = date('Y-m-d', mktime(0,0,0,@date('m'), @date('d')-4, @date('Y')));
							$temEmail = true;

							$dbFluxoClientes->edt($fluxoCliente['id'], array('envia_email'=>1));
							
						}
					}elseif($fluxoCliente['ultima_visualizacao'] && mktime(0,0,0,$arrDate[1], $arrDate[2], $arrDate[0]) <= mktime(0,0,0,@date('m'), @date('d')-2, @date('Y'))){
						$color = "bgcolor='#FF7777'";
					}elseif($fluxoCliente['ultima_visualizacao'] && mktime(0,0,0,$arrDate[1], $arrDate[2], $arrDate[0]) == mktime(0,0,0,@date('m'), @date('d')-1, @date('Y'))){
						$color = "bgcolor='#FFFF55'";
					}
					
					
				}
				
				if($fluxoCliente['atualizacao'] == 1){
					$color = "bgcolor='#33ff33'";
				}
				
				if($fluxoCliente['resposta_cliente'] == 1){
					$color = "bgcolor='#63B8FF'";
				}

				if($fluxoCliente['data_agendamento'] && strtotime($fluxoCliente['data_agendamento']) <= strtotime(@date('Y-m-d')) && strtotime($fluxoCliente['ultima_visualizacao']) < strtotime(@date('Y-m-d'))){
					$color = "bgcolor='#33ff33'";
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
							

							$arr = $dbVeiculos->getVeiculoEstoqueFluxo($arrFiltro);

							if(isset($arr[0]['id'])){
								
								$color2 = $color." style='cursor:pointer; text-decoration:underline;' onClick='javascript:window.open(\"/veiculos/edt/id/".$arr[0]['id']."\")'";
								
								
							}else{
								
								$color2 = "bgcolor='#008000'";
								$cor = "style='color:#fff; font-weight:bold;'";
							
							}
						
						}elseif($veiculo['estoque'] == 1 && $fluxoCliente['resultado'] == "Negociação"){
							
							$arrFiltro['valor_venda'] = $fluxoCliente['valor'];
							$arrFiltro['modelo'] = $veiculo['veiculo'];
							$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

							$arr = $dbVeiculos->getVeiculoEstoqueFluxo($arrFiltro);

							if($arr[0]['id']){
								$color2 = $color." style='cursor:pointer; text-decoration:underline;' onClick='javascript:window.open(\"/veiculos/edt/id/".$arr[0]['id']."\")'";
							}else{
								$color2 = "bgcolor='#008000'";
								$cor = "style='color:#fff; font-weight:bold;'";
							}
							
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
				
				$arrTempNome = explode(" ", $fluxoCliente['nome']);
				if(isset($arrTempNome[1])){
					$fluxoCliente['nome'] = $arrTempNome[0]." ".$arrTempNome[1];
				}else{
					$fluxoCliente['nome'] = $arrTempNome[0];
				}

				$dbUsuarios = new Application_Model_DbTable_Usuarios();
			
				$arrUsuario = $dbUsuarios->_get(array("id_empresa"=>$_SESSION['sessionUser']['id_empresa'], "id"=>$fluxoCliente['id_usuario']));

				
				$arrTempUsuario = explode(" ", $arrUsuario[0]['nome']);
				if(isset($arrTempUsuario[2])){
					$arrUsuario[0]['nome'] = $arrTempUsuario[0]." ".$arrTempUsuario[1]." ".$arrTempUsuario[2];
				}elseif(isset($arrTempUsuario[1])){
					$arrUsuario[0]['nome'] = $arrTempUsuario[0]." ".$arrTempUsuario[1];
				}elseif(isset($arrTempUsuario[0])){
					$arrUsuario[0]['nome'] = $arrTempUsuario[0];
				}
				
				
				$dataAgendamento = "";
				if($fluxoCliente['data_agendamento']){
					$dataAgendamento = "<div class='agend'>Agendado:&nbsp".implode("/", array_reverse(explode("-", $fluxoCliente['data_agendamento'])))."</div>";
				}
				
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

				$strFluxo .= "<tr $color style='cursor:pointer;' onClick='javascript:window.open(\"/clientes/add-cliente-fluxo-auto-salvar/id/".$fluxoCliente['id']."\")'>
								<td style='border-right:0px;' rowspan='".$contSpan."'>".$fluxoCliente['nome']."</td>
								<td rowspan='".$contSpan."'>".$dataAgendamento."</td>
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

			echo $strFluxo."</table>";
			
			if($temEmail && $data){
				$this->email($data);//Envia e-mail para o gerente informando.
			}
			
		}elseif($this->_getParam('fn') == 'data_ultima_visualizacao'){
			
			$idFluxoCliente = $this->_getParam('id');
			$dados['ultima_visualizacao'] = $this->_getParam('ultima_visualizacao');
			$dados['atualizacao'] = 0;
			$dados['resposta_cliente'] = 0;
			
			$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();

			if($this->_getParam('id')){
				$dado = $dbFluxoClientes->getClienteFluxo($this->_getParam('id'));
				$dados['atividades'] = $dado[0]['atividades']."\n\n- - - - - - ".$_SESSION['sessionUser']['nome']." acessou este contato às: ".@date('H:i:s d/m/Y')." - - - - - -\n\n";
				
				if(!$dado[0]['data_tempo_resposta']){
					$dados['data_tempo_resposta'] = @date('Y-m-d H:i:s');
				}
				
				$dbFluxoClientes->edt($idFluxoCliente, $dados);
			}

		}

	}
	
	private function email($data){
		
		$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
		
		if($data == ""){
			return;
		}

		$arrFluxoClientes = $dbFluxoClientes->getFluxoClienteEmail($data);
		
		$strEmail = "<html>
						<head>
							<style>
								td{
									border:solid 0px;
									padding:2px;
								}
								th{
									border:solid 0px;
									font-weight:bold;
									text-align:right;
									padding:2px;
								}
							</style>
						</head>
						<body>
							<table>
								<tr><td colspan='2' style='font-size:15px;'>Olá, você recebeu este e-mail, pois há contatos no fluxo de clientes
								que está em atraso.<br/>Segue abaixo os dados do(s) cliente(s).</td></tr>";
								
		$strEmail .= "<tr><td><br/></td></tr>";
		
		$cor = true;
		
		foreach($arrFluxoClientes as $fluxoClientes){
			
			if($cor){
				$style = "style='background-color:#eee;'";
				$cor = false;
			}else{
				$style = "style='background-color:#fff;'";
				$cor = true;
			}
			
			$strEmail .= "<tr ".$style."><th>Nome: </th><td> ".$fluxoClientes['nome']."</td></tr>";
			$strEmail .= "<tr ".$style."><th>Telefone: </th><td> ".$fluxoClientes['telefone']."  ".$fluxoClientes['telefone2']."  ".$fluxoClientes['celular']."</td></tr>";
			$strEmail .= "<tr ".$style."><th>Origem: </th><td> ".$fluxoClientes['descricao']."</td></tr>";
			$strEmail .= "<tr ".$style."><th>Veiculo de troca: </th><td> ".$fluxoClientes['veiculo_troca']." / ".$fluxoClientes['ano_modelo_troca']."</td></tr>";
			$strEmail .= "<tr ".$style."><th>Cadastro: </th><td> ".implode("/",array_reverse(explode("-",$fluxoClientes['data'])))."</td></tr>";
			$strEmail .= "<tr ".$style."><th>Última visualização: </th><td> ".implode("/",array_reverse(explode("-",$fluxoClientes['ultima_visualizacao'])))."</td></tr>";
			$strEmail .= "<tr ".$style."><th>Vendedor: </th><td> ".$fluxoClientes['nome_usuario']."</td></tr>";
			$strEmail .= "<tr ".$style."><th>Resultado: </th><td> ".$fluxoClientes['resultado']."</td></tr>";
			$strEmail .= "<tr ".$style."><th>Valor: </th><td> R$ ".money_format("%i",$fluxoClientes['valor'])."</td></tr>";
			$strEmail .= "<tr ".$style."><th>Entrada: </th><td> R$ ".money_format("%i",$fluxoClientes['entrada'])."</td></tr>";
			$strEmail .= "<tr ".$style."><th>Parcela: </th><td> R$ ".money_format("%i",$fluxoClientes['parcela'])."</td></tr>";
			$strEmail .= "<tr ".$style."><th>Observações: </th><td> ".$fluxoClientes['obs']."</td></tr>";
			$strEmail .= "<tr ".$style."><th><br/></th><td></td></tr>";
			
		}
		
		$strEmail .= "</table></body></html>";

		$assunto = "Fluxo de clientes";
		
		$dbUsuarios = new Application_Model_DbTable_Usuarios();
		
		$arr = $dbUsuarios->getUsuarioPorPerfil($_SESSION['sessionUser']['id_empresa'], 2);

		$this->enviaEmail($arr[0]['email'], $assunto, $strEmail, $attach = false);
		
	}
	
	public function addAction(){
		
		$dbCliente = new Application_Model_DbTable_Clientes();
		
		$this->validaAcesso('gerenciar_clientes');
		
		$this->view->id_empresa = $_SESSION['sessionUser']['id_empresa'];
		
		if($this->getRequest()->isPost()){	

			unset($_POST['pessoa_fisica']);
		
			$dados = $_POST;
			$dados['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dados['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			if($dados['data_expedicao'] != ""){
			
				$arrTmpData = explode("/",$dados['data_expedicao']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['data_expedicao'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['nascimento'] != ""){
			
				$arrTmpData = explode("/",$dados['nascimento']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['nascimento'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['data_admissao'] != ""){
			
				$arrTmpData = explode("/",$dados['data_admissao']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['data_admissao'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['data_admissao_anterior'] != ""){
			
				$arrTmpData = explode("/",$dados['data_admissao_anterior']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['data_admissao_anterior'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['data_demissao_anterior'] != ""){
			
				$arrTmpData = explode("/",$dados['data_demissao_anterior']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['data_demissao_anterior'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['nascimento_conjuge'] != ""){
			
				$arrTmpData = explode("/",$dados['nascimento_conjuge']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['nascimento_conjuge'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['data_admissao_conjuge'] != ""){
			
				$arrTmpData = explode("/",$dados['data_admissao_conjuge']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['data_admissao_conjuge'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['cliente_desde'] != ""){
			
				$arrTmpData = explode("/",$dados['cliente_desde']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['cliente_desde'] = implode("-",$arrTmpData);
			
			}

			$id = $dbCliente->insert($dados);
			
			if($id){
   
				$this->view->mensagem = "Cliente cadastrado com sucesso!";
				$this->_helper->redirector->gotoUrl("clientes/edt/id/".$id."");
				
			}else{
			   
				$this->view->mensagem = "Erro ao cadastrar o Cliente.";
			   
			}
		
		}
	
	}
	
	public function listaAction(){
	
		$this->validaAcesso('listar_clientes');
		
		$dbCliente = new Application_Model_DbTable_Clientes();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		if($this->getRequest()->isPost()){
		
			$arr['parcial'] = true;
			if($this->_getParam('nome') != ""){
				$arr['nome'] = $this->_getParam('nome');
			}
			if($this->_getParam('cpf') != ""){
				$arr['cpf'] = $this->_getParam('cpf');
			}
			if($this->_getParam('cidade') != ""){
				$arr['cidade'] = $this->_getParam('cidade');
			}
			
			if($this->_getParam('nascInicial') != ""){
				$dtTemp = explode("/",$this->_getParam('nascInicial'));
				$dtTemp = implode("-", array_reverse($dtTemp));
				$arr['nascInicial'] = $dtTemp;
			}
			if($this->_getParam('nascFinal') != ""){
				$dtTemp = explode("/",$this->_getParam('nascFinal'));
				$dtTemp = implode("-", array_reverse($dtTemp));
				$arr['nascFinal'] = $dtTemp;
			}
			
			if($this->_getParam('compraInicial') != ""){
				$dtTemp = explode("/",$this->_getParam('compraInicial'));
				$dtTemp = implode("-", array_reverse($dtTemp));
				$arr['compraInicial'] = $dtTemp;
			}
			
			if($this->_getParam('compraFinal') != ""){
				$dtTemp = explode("/",$this->_getParam('compraFinal'));
				$dtTemp = implode("-", array_reverse($dtTemp));
				$arr['compraFinal'] = $dtTemp;
			}

			if($this->_getParam('mes_niver') != ""){
				$arr['mes_niver'] = $this->_getParam('mes_niver');
			}

		}else{
		
			$arr['hora_alteracao_inicial'] = @date("Y")."-".@date("m")."-01";
			$data = @date("Y-m-d",mktime(0, 0, 0, @date("m")+1, 0, @date("Y")));
			$arr['hora_alteracao_final'] = $data;
			$arr['lista'] = true;
		
		}
		
		$arrClientes = $dbCliente->_get($arr);

		if($this->_getParam('somente_celular')){
			foreach ($arrClientes as $key => $value) {
				
				if($arrClientes[$key]['tel1'] && $this->telefoneCelular($arrClientes[$key]['tel1'])){
					$arrClientes[$key]['tel1'] = $arrClientes[$key]['tel1'];
				}elseif($arrClientes[$key]['tel2'] && $this->telefoneCelular($arrClientes[$key]['tel2'])){
					$arrClientes[$key]['tel1'] = $arrClientes[$key]['tel2'];
				}elseif($arrClientes[$key]['cel'] && $this->telefoneCelular($arrClientes[$key]['cel'])){
					$arrClientes[$key]['tel1'] = $arrClientes[$key]['cel'];
				}else{
					unset($arrClientes[$key]);
				}

			}
		}

		$this->view->clientes = $arrClientes;
	
	}

	/*
	Verifica se o número recebido de parâmetro, é um número de um celular
	nas regras do estado de São Paulo.
	O formato é obrigatório "(99) 999999999".
	A função retorna um booleano. */
	private function telefoneCelular($numero){
		
		$arrTemp = explode(" ", $numero);
		if($arrTemp[1][0] == "9"){
			return true;
		}else{
			return false;
		}

	}
	
	public function delAction(){

		$this->validaAcesso('gerenciar_clientes');
	
		$dbClientes = new Application_Model_DbTable_Clientes();
		
		$dbClientes->delete("id = " . $this->_getParam('id'));
		
		$this->_helper->redirector->gotoUrl("clientes/lista");
	
	}
	
	public function editarAction(){
	
		$this->validaAcesso('gerenciar_clientes');

		$dbClientes = new Application_Model_DbTable_Clientes();
		
		$dados = $dbClientes->fetchAll("id = " . $this->_getParam('id'));
		$dados = $dados[0];
		
		if($dados['data_expedicao'] != ""){
		
			$arrTmpData = explode("-",$dados['data_expedicao']);
			$arrTmpData = array_reverse($arrTmpData);
			$dados['data_expedicao'] = implode("/",$arrTmpData);
		
		}
		
		if($dados['nascimento'] != ""){
		
			$arrTmpData = explode("-",$dados['nascimento']);
			$arrTmpData = array_reverse($arrTmpData);
			$dados['nascimento'] = implode("/",$arrTmpData);
		
		}
		
		if($dados['data_admissao'] != ""){
		
			$arrTmpData = explode("-",$dados['data_admissao']);
			$arrTmpData = array_reverse($arrTmpData);
			$dados['data_admissao'] = implode("/",$arrTmpData);
		
		}
		
		if($dados['data_admissao_anterior'] != ""){
			
			$arrTmpData = explode("-",$dados['data_admissao_anterior']);
			$arrTmpData = array_reverse($arrTmpData);
			$dados['data_admissao_anterior'] = implode("/",$arrTmpData);
		
		}
		
		if($dados['data_demissao_anterior'] != ""){
		
			$arrTmpData = explode("-",$dados['data_demissao_anterior']);
			$arrTmpData = array_reverse($arrTmpData);
			$dados['data_demissao_anterior'] = implode("/",$arrTmpData);
		
		}
		
		if($dados['nascimento_conjuge'] != ""){
		
			$arrTmpData = explode("-",$dados['nascimento_conjuge']);
			$arrTmpData = array_reverse($arrTmpData);
			$dados['nascimento_conjuge'] = implode("/",$arrTmpData);
		
		}
		
		if($dados['data_admissao_conjuge'] != ""){
		
			$arrTmpData = explode("-",$dados['data_admissao_conjuge']);
			$arrTmpData = array_reverse($arrTmpData);
			$dados['data_admissao_conjuge'] = implode("/",$arrTmpData);
		
		}
		
		if($dados['cliente_desde'] != ""){
		
			$arrTmpData = explode("-",$dados['cliente_desde']);
			$arrTmpData = array_reverse($arrTmpData);
			$dados['cliente_desde'] = implode("/",$arrTmpData);
		
		}
		
		$this->view->cliente = $dados;
		
		$this->view->id = $this->_getParam('id');
		$this->view->id_empresa = $_SESSION['sessionUser']['id_empresa'];
				
		if($this->getRequest()->isPost()){
		
			unset($_POST['pessoa_fisica']);
			$dados = $_POST;
			$dados['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dados['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			if($dados['data_expedicao'] != ""){
			
				$arrTmpData = explode("/",$dados['data_expedicao']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['data_expedicao'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['nascimento'] != ""){
			
				$arrTmpData = explode("/",$dados['nascimento']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['nascimento'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['data_admissao'] != ""){
			
				$arrTmpData = explode("/",$dados['data_admissao']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['data_admissao'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['data_admissao_anterior'] != ""){
			
				$arrTmpData = explode("/",$dados['data_admissao_anterior']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['data_admissao_anterior'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['data_demissao_anterior'] != ""){
			
				$arrTmpData = explode("/",$dados['data_demissao_anterior']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['data_demissao_anterior'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['nascimento_conjuge'] != ""){
			
				$arrTmpData = explode("/",$dados['nascimento_conjuge']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['nascimento_conjuge'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['data_admissao_conjuge'] != ""){
			
				$arrTmpData = explode("/",$dados['data_admissao_conjuge']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['data_admissao_conjuge'] = implode("-",$arrTmpData);
			
			}
			
			if($dados['cliente_desde'] != ""){
			
				$arrTmpData = explode("/",$dados['cliente_desde']);
				$arrTmpData = array_reverse($arrTmpData);
				$dados['cliente_desde'] = implode("-",$arrTmpData);
			
			}
		
			$dbClientes->update($dados, "id = " . $this->_getParam('id'));
			
			$this->_helper->redirector->gotoUrl("clientes/lista");
		
		}
	
	}



	/*
	Esta função servirá para ler cliente da loja 1(id=3) do Guilherme e cadastrar na loja 2(id=239)
	Nao executadar se não souber o que está fazendo.
	
	public function recadastrarAction(){

		$dbClientes = new Application_Model_DbTable_Clientes();

		$arrClientes = $dbClientes->getClientePorEmpresa(3);

		$count = 0;

		foreach ($arrClientes as $key => $value) {

			if(!$dbClientes->getCpfExistente($value['cpf'], 239)){
				echo $value['cpf']."<br>";
				$count++;
				$value['id_empresa'] = 239;
				unset($value['id']);
				$dbClientes->insert($value);
			}

		}

		echo $count;
		//var_export($arrClientes);



	}
	*/

	private function enviaEmail($para, $assunto, $corpo, $attach = false){

		$config = array(
			'auth' => 'login',
			'username' => 'sistemameucar@sistemameucar.com.br',
			'password' => 'g010502g',
			'port' => '587'
		);

		$transport = new Zend_Mail_Transport_Smtp('smtp.sistemameucar.com.br', $config);

		$mail = new Zend_Mail('UTF-8');
		$mail->setBodyHtml($corpo);
		$mail->setFrom('sistemameucar@sistemameucar.com.br');
		$mail->addTo($para);
		//$mail->addBcc('icomenezes@hotmail.com');
		//$mail->addTo('icomenezes@hotmail.com');
		$mail->setSubject($assunto);
	   

		try{

			if($attach){

				$mail->createAttachment(file_get_contents($attach), 'text/plain', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $attach);
         
			}

			return $mail->send($transport);
			
		}catch(Exception $e){

			echo $e->getMessage();
			
		}
		
	}
	
	private function arrayVal($arr,$key){
		return $arr[$key];
	}

}

?>
