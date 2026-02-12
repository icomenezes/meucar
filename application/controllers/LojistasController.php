<?php

header("Content-Type: text/html; charset=UTF-8", false);
header("Access-Control-Allow-Origin: *");

class LojistasController extends Zend_Controller_Action {

	public function init() {

		$this->view->titulo = "";

		Zend_Session::start();
	}
   
   
	public function ajaxAction(){
	
		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');

		if($this->_getParam('fn') == "fotosVeiculosIndex"){
		
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
		
			$arrVeiculos = $dbVeiculos->getVeiculosUsadosIndexPorEmpresa($this->_getParam('idEmpresa'));
			
			for($i = 0; $i < 17; $i++){
			
				if ($arrVeiculos[$i]['descricao_site']) {

					$arrVeiculos[$i]['modelo'] = $arrVeiculos[$i]['descricao_site'];
				
				}

			   if ($arrVeiculos[$i]['exibir_valor_site'] == 1) {

				  $arrVeiculos[$i]['valor_venda'] = "R$ " . money_format("%i", $arrVeiculos[$i]['valor_venda']);
			   
			   } else {

				  $arrVeiculos[$i]['valor_venda'] = "Consulte-nos";
			   }
			
				if($arrVeiculos[$i]['id']){
			
					$htmlVeiculo .= $arrVeiculos[$i]['id']."|".$arrVeiculos[$i]['path']."|".$arrVeiculos[$i]['modelo']."|".$arrVeiculos[$i]['valor_venda']."|".$arrVeiculos[$i]['combustivel']."|".$arrVeiculos[$i]['ano_modelo']."|";
			
				}
			
			}
			
			echo $htmlVeiculo;
		 
		}elseif($this->_getParam('fn') == "fotosVeiculosIndexNovos"){
		
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
		
			$arrVeiculos = $dbVeiculos->getVeiculosNovosIndexPorEmpresa($this->_getParam('idEmpresa'));
			
			if($arrVeiculos){
			
				for($i = 0; $i < 12; $i++){
				
					if ($arrVeiculos[$i]['descricao_site']) {

						$arrVeiculos[$i]['modelo'] = $arrVeiculos[$i]['descricao_site'];
					
					}

				   if ($arrVeiculos[$i]['exibir_valor_site'] == 1) {

					  $arrVeiculos[$i]['valor_venda'] = "R$ " . money_format("%i", $arrVeiculos[$i]['valor_venda']);
				   
				   } else {

					  $arrVeiculos[$i]['valor_venda'] = "Consulte-nos";
				   }
				   
					if($arrVeiculos[$i]['id']){
			
						$htmlVeiculo .= $arrVeiculos[$i]['id']."|".$arrVeiculos[$i]['path']."|".$arrVeiculos[$i]['modelo']."|".$arrVeiculos[$i]['valor_venda']."|".$arrVeiculos[$i]['combustivel']."|".$arrVeiculos[$i]['ano_modelo']."|";
				
					}
				
				}

				echo $htmlVeiculo;
			
			}
		 
		}elseif($this->_getParam('fn') == "fotoLogo"){
		
			$dbEmpresas = new Application_Model_DbTable_Empresas();
			
			$arrEmpresa = $dbEmpresas->getEmpresa($this->_getParam('idEmpresa'));
			
			echo $arrEmpresa[0]['path']."|".$arrEmpresa[0]['path_frente_loja'];
			
			
		}elseif($this->_getParam('fn') == "buscaRapida"){
		
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
			$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
			
			$_POST['str_veiculo'] = $this->_getParam('strVeiculos');
			$_POST['id_empresa'] = $this->_getParam('idEmpresa');
			$_POST['modelo'] = $this->_getParam('modelo');
			$_POST['minimo_ano'] = $this->_getParam('minimo_ano');
			$_POST['minimo_preco'] = $this->_getParam('minimo_preco');
			$_POST['maximo_ano'] = $this->_getParam('maximo_ano');
			$_POST['maximo_preco'] = $this->_getParam('maximo_preco');
			$_POST['novo_usado'] = $this->_getParam('novo_usado');
			
			if($this->_getParam('marca')){
			
				$_POST['marca'] = str_replace("+"," ",$this->_getParam('marca'));
			
			}
			
			if($this->_getParam('cidade')){
			
				$_POST['cidade'] = str_replace("+"," ",$this->_getParam('cidade'));

			}
		
			$arrVeiculos = $dbVeiculos->_getSite($_POST);
			
			foreach($arrVeiculos as $veiculos){
			
				if ($veiculos['descricao_site']) {

					$veiculos['modelo'] = $veiculos['descricao_site'];
					
				}

				if ($veiculos['exibir_valor_site'] == 1) {

					$veiculos['valor_venda'] = "R$ " . money_format("%i", $veiculos['valor_venda']);
				   
				} else {

					$veiculos['valor_venda'] = "Consulte-nos";
				}
				
				$arrFotosVeiculos = $dbFotosVeiculos->getFotosVeiculoSelecionado($veiculos['id']);

				foreach ($arrFotosVeiculos as $fotos) {

					if($fotos['capa'] == 1){

						$veiculos['path'] = $fotos['path'];
            
					}elseif($veiculos['path'] == ""){

						$veiculos['path'] = $fotos['path'];
            
					}
					
				}
				   
				if($veiculos['id']){
			
					$htmlVeiculo .= $veiculos['id']."|".$veiculos['path']."|".$veiculos['modelo']."|".$veiculos['valor_venda']."|".$veiculos['combustivel']."|".$veiculos['ano_modelo']."|".$veiculos['cidade']."|".$veiculos['nome_fantasia']."|";
				
				}
			
			}
			
			echo $htmlVeiculo;
		 
		}elseif($this->_getParam('fn') == "buscaVeiculo"){
		
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
			$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
			$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
			
			$_POST['id'] = $this->_getParam('id');
			$_POST['id_empresa'] = $this->_getParam('idEmpresa');
		
			$arrVeiculos = $dbVeiculos->_getSite($_POST);
			$arrOpcionaisVeiculos = $dbOpcionaisVeiculos->getVeiculosOpcionais($_POST['id']);
			$arrFotosVeiculos = $dbFotosVeiculos->getFotosVeiculoSelecionado($_POST['id']);
			
			$cont = 0;
			
			foreach ($arrFotosVeiculos as $fotos){

				$arrVeiculos[0]['path_'.$cont] = $fotos['path'];
				$cont++;
	
			}
			
			if($arrOpcionaisVeiculos){
			
				foreach($arrOpcionaisVeiculos as $key=>$opcionais){
		  
					$opcional = explode(")",$opcionais['opcional']);
					
					if($arrOpcionaisVeiculos[$key+1]['opcional'] == ""){
					
						$stringOpcionais .= $opcional[1].".";
						
					}else{
					
						$stringOpcionais .= $opcional[1].", ";
					
					}
				
				}
			
			}
	
			if($arrVeiculos[0]['id']){
	
				$htmlVeiculo .= $arrVeiculos[0]['id']."|".$arrVeiculos[0]['marca']."|".$arrVeiculos[0]['descricao_site']."|".$arrVeiculos[0]['valor_venda']."|".$arrVeiculos[0]['combustivel']."|".$arrVeiculos[0]['ano_modelo']."|".$arrVeiculos[0]['cidade']."|".$arrVeiculos[0]['nome_fantasia']."|".$arrVeiculos[0]['path_0']."|".$arrVeiculos[0]['path_1']."|".$arrVeiculos[0]['path_2']."|".$arrVeiculos[0]['path_3']."|".$arrVeiculos[0]['path_4']."|".$arrVeiculos[0]['obs_site']."|".$arrVeiculos[0]['tel1']."|".$arrVeiculos[0]['tel2']."|".$arrVeiculos[0]['endereco']."|".$arrVeiculos[0]['bairro']."|".$arrVeiculos[0]['estado']."|".$arrVeiculos[0]['placa']."|".$arrVeiculos[0]['ano_fabricacao']."|".$arrVeiculos[0]['exibir_valor_site']."|".$stringOpcionais."|";
	
			}
			
			echo $htmlVeiculo;
		 
		}elseif ($this->_getParam('fn') == "envia_interesse"){

			$dados['id_empresa'] = $this->_getParam('id_empresa');
			$dados['id_veiculo'] = $this->_getParam('id_veiculo');
			$dados['nome_cliente'] = $this->_getParam('nome');
			$dados['email'] = $this->_getParam('email');
			$dados['telefone'] = $this->_getParam('telefone');
			$dados['mensagem'] = $this->_getParam('mensagem');
			$dados['veiculo_troca'] = $this->_getParam('veiculo_troca');
			$dados['financiar'] = $this->_getParam('financiar');
			$dados['ofertas_email'] = $this->_getParam('oferta_email');
			$dados['path'] = $this->_getParam('path');

			//$urlSite = explode("/",$dados['path']);
			
			$dbEmpresas = new Application_Model_DbTable_Empresas();
			$dbVeiculos = new Application_Model_DbTable_Veiculos();

			$arrEmpresa = $dbEmpresas->getEmpresa($dados['id_empresa']);

			$arr['id'] = $dados['id_veiculo'];

			$arrVeiculo = $dbVeiculos->getVeiculoPorId($arr);

			if ($arrVeiculo[0]['descricao_site']) {

				$arrVeiculo[0]['modelo'] = $arrVeiculo[0]['descricao_site'];
			}

			if ($dados['veiculo_troca']) {

				$dados['veiculo_troca'] = "Sim";
			
			} else {

				$dados['veiculo_troca'] = "N&atilde;o";
         
			}

			if ($dados['financiar']) {

				$dados['financiar'] = "Sim";
			
			} else {

				$dados['financiar'] = "N&atilde;o";
			
			}

			$destinatario = $arrEmpresa[0]['email'];
			
			$assunto = "Proposta - Enviado por " . $dados['nome_cliente'] . " pelo site: ".$dados['path'].".";

			$corpo = "
			<html>
				<head>
					<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
					<title>MeuCar</title>
					<style>
						td{
							vertical-align:middle;
						}
						
						a img{
							width:200px;
							float:right;
						}
					</style>
				</head>
				<body>
					<table style='background-color:#E8E8E8; border:1px solid #CCCCCC; color:#666666; font:14px Arial, Helvetica, sans-serif;'>
						<tr><td colspan='2' style='border-bottom:solid 1px #CCCCCC; vertical-align:bottom; font-size:20px; height:50px;'>Dados do An&uacute;ncio</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Ve&iacute;culo:</b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'><a href='".$dados['path']."/veiculo.html?id=".$dados['id_veiculo']."'>".$dados['path']."/veiculo?id=".$dados['id_veiculo']."</a></td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Modelo: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $arrVeiculo[0]['marca'] . " " . $arrVeiculo[0]['modelo'] . "</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Placa: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $arrVeiculo[0]['placa'] . "</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Ano: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $arrVeiculo[0]['ano_fabricacao'] . "/" . $arrVeiculo[0]['ano_modelo'] . "</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Cor: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $arrVeiculo[0]['cor'] . "</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Valor: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>R$ " . money_format("%i", $arrVeiculo[0]['valor_venda']) . "</td></tr>
						<tr><td colspan='2' style='border-bottom:solid 1px #CCCCCC; vertical-align:bottom; font-size:20px; height:50px;'>Dados do Interessado</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Nome: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $dados['nome_cliente'] . "</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>E-mail: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $dados['email'] . " <-Usar este e-mail para resposta ao cliente.</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Telefone: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $dados['telefone'] . "</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Mensagem: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $dados['mensagem'] . "</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Usar Ve&iacute;culo em Troca: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $dados['veiculo_troca'] . "</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Deseja Financiar: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $dados['financiar'] . "</td></tr>
						<tr><td colspan='2' style=''><br><center><img style='width:150px;' src='http://sistemameucar.com.br/".$arrEmpresa[0]['path']."'/><br><a href='http://".$dados['path']."'>".$dados['path']."</a></center><br></td></tr>
					</table>
				</body>
			</html>";

			if ($this->enviaEmailInteresse($destinatario, $assunto, $corpo)){
				
				echo "Sucesso";
				
			} else {

				echo "Erro";
			
			}
			
		}elseif ($this->_getParam('fn') == "envia_amigo"){

			$dados['id_empresa'] = $this->_getParam('id_empresa');
			$dados['id_veiculo'] = $this->_getParam('id_veiculo');
			$dados['nome_remetente'] = $this->_getParam('nome_remetente');
			$dados['email_remetente'] = $this->_getParam('email_remetente');
			$dados['nome_amigo'] = $this->_getParam('nome_amigo');
			$dados['email_amigo'] = $this->_getParam('email_amigo');
			$dados['comentario'] = $this->_getParam('comentario');
			$dados['path'] = $this->_getParam('path');
			
			$dbEmpresas = new Application_Model_DbTable_Empresas();
			$dbVeiculos = new Application_Model_DbTable_Veiculos();

			$arrEmpresa = $dbEmpresas->getEmpresa($dados['id_empresa']);

			$arr['id'] = $dados['id_veiculo'];
			$arrVeiculo = $dbVeiculos->getVeiculoPorId($arr);

			if ($arrVeiculo[0]['descricao_site']) {

				$arrVeiculo[0]['modelo'] = $arrVeiculo[0]['descricao_site'];
			
			}

			$destinatario = $dados['email_amigo'];
			$remetente = $dados['email_remetente'];
			$assunto = "Mensagem Site ".$dados['path']." - Enviado por " . $dados['nome_remetente'];
			$corpo = "
				<html>
					<head>
						<meta content='text/html; charset=utf-8' http-equiv='Content-Type'>
						<title>".$dados['path']."</title>
						<style>
							table tr td{
								#border:solid 1px;
								width:100%;
							}
							
							a img{
								width:200px;
								float:right;
							}
						</style>
					</head>
					<body>
						<table style='background-color:#E8E8E8; border:1px solid #CCCCCC; color:#666666; font:14px Arial, Helvetica, sans-serif;'>
							<tr><td style='font-size:20px;'><br>Ol&aacute; " . $dados['nome_amigo'] . ".</td><td></td></tr>
							<tr><td colspan='2' style='height:10px;'></td></tr>
							<tr><td colspan='2'>" . $dados['nome_remetente'] . " visitou o site '".$dados['path']."' e lhe enviou esta mensagem abaixo que &eacute; de seu interesse.<br><br></td></tr>
							<tr><td colspan='2'><div style='background-color: #CCCCCC;'>" . $dados['comentario'] . "</div><br></td></tr>
							<tr><td colspan='2'>Clique no link abaixo para ser direcionado ao an&uacute;cio enviado por " . $dados['nome_remetente'] . ".</td></tr>
							<tr><td colspan='2'><a href='http://".$dados['path']."/veiculo.html?id=".$dados['id_veiculo']."'>".$dados['path']."/veiculo?id=".$dados['id_veiculo']."</a><br><br></td></tr>
							<tr><td colspan='2' style='height:10px;'></td></tr>
							<tr><td colspan='2' style=''><br><center><img style='width:150px;' src='http://sistemameucar.com.br/".$arrEmpresa[0]['path']."'/><br><a href='http://".$dados['path']."'>".$dados['path']."</a></center><br></td></tr>
						</table>
					</body>
				</html>";

			if ($this->enviaEmailAmigo($destinatario, $remetente, $assunto, $corpo)) {

				echo "sucesso";
			
			} else {

				echo "erro";
			
			}

		}
		
	}	
		
	private function enviaEmailAmigo($para, $de, $assunto, $body, $attach = false){

		$config = array(
          'auth' => 'login',
          'username' => 'sistemameucar@sistemameucar.com.br',
          'password' => 'g010502g',
          'port' => '587'
		);

		$transport = new Zend_Mail_Transport_Smtp('smtp.sistemameucar.com.br', $config);

		$mail = new Zend_Mail("UTF-8");
		$mail->setFrom('sistemameucar@sistemameucar.com.br');
		$mail->addTo($para);
		$mail->setBodyHtml($body);
		$mail->setSubject($assunto);


		try {

			if ($attach) {

				$mail->createAttachment(file_get_contents($attach), 'text/plain', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $attach);
			
			}

			return $mail->send($transport);
			
		} catch (Exception $e) {

         //echo $e->getMessage();
		
		}
   
	}
	
	private function enviaEmailInteresse($para, $assunto, $body, $attach = false) {

		$config = array(
			'auth' => 'login',
			'username' => 'proposta@sistemameucar.com.br',
			'password' => 'g010502g',
			'port' => '587'
		);

		$transport = new Zend_Mail_Transport_Smtp('smtp.sistemameucar.com.br', $config);

		$mail = new Zend_Mail("UTF-8");
		$mail->setFrom('proposta@sistemameucar.com.br');
		$mail->addTo($para);
		//$mail->addBcc('proposta@sistemameucar.com.br');
		$mail->setBodyHtml($body);
		$mail->setSubject($assunto);


		try{

			if ($attach){

				$mail->createAttachment(file_get_contents($attach), 'text/plain', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $attach);
			
			}

			return $mail->send($transport);
			
		}catch (Exception $e){

			//echo $e->getMessage();
		
		}
   
	}
   
}

?>