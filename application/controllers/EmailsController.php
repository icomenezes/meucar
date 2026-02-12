<?php

header("Content-Type: text/html; charset=UTF-8",true);

class EmailsController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "E-mails";

		Zend_Session::start();
		

	}
/*
	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
*/	

	public function ajaxAction(){
		
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		if($this->_getParam('fn') == "email_fluxo"){
			
			$dbEmpresas = new Application_Model_DbTable_Empresas();
			$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
			
			if($this->_getParam('id_cliente_fluxo')){
				$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();
				$arrFluxo = $dbFluxoClientes->getClienteFluxo($this->_getParam('id_cliente_fluxo'));
				
				if($arrFluxo[0]['veiculo']){
					$strVeiculo = " sobre o veículo: ".$arrFluxo[0]['veiculo']." - ".$arrFluxo[0]['ano_modelo'];
				}else{
					$strVeiculo = "";
				}
				
				
				$assunto = "De ".$arrEmpresa[0]['nome_fantasia']." para ".$arrFluxo[0]['nome'].$strVeiculo;
			}
			
			$corpo = $this->_getParam('mensagem');
			$email = $this->_getParam('email');
			
			
			

			if($corpo != "" && $email != ""){
				/*
				$config = array(
					'ssl' => 'tls',
					'port' => 587,
					'auth' => 'login',
					'username' => 'sistemameucar@sistemameucar.com.br',
					'password' => 'g010502g'
				);
				*/
				
				if($_SESSION['sessionUser']['id_empresa'] == 3){
					
					$setFrom = 'contato@selectveiculos.com.br';
					
					$config = array(
						'ssl' => 'tls',
						'port' => 587,
						'auth' => 'login',
						'username' => $setFrom,
						'password' => 'SV200315'
					);

				}elseif($_SESSION['sessionUser']['id_empresa'] == 239){
					
					$setFrom = 'contato2@selectveiculos.com.br';
					
					$config = array(
						'ssl' => 'tls',
						'port' => 587,
						'auth' => 'login',
						'username' => $setFrom,
						'password' => 'contato2123'
					);
					
				}
				
				$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);

				$mail = new Zend_Mail('UTF-8');
				$mail->setBodyHtml(str_replace("\n","<br/>",$corpo));
				$mail->setReplyTo($arrEmpresa[0]['email'], $arrEmpresa[0]['nome_fantasia']);
				$mail->setFrom($setFrom, $arrEmpresa[0]['nome_fantasia']);
				$mail->addTo($email);
				$mail->setSubject($assunto);
				
				$salva = false;
				
				try{
					if($attach){

						$mail->createAttachment(file_get_contents($attach), 'text/plain', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $attach);

					}

					if($mail->send($transport)){
						$salva = true;
					}else{
						echo "Erro";
					}
					
				}catch(Exception $e){

					//echo $e->getMessage();
					echo "Erro";
					
				}
				
				if($salva){
					
					if($arrFluxo[0]['id']){
							
						if(!$arrFluxo[0]['data_tempo_resposta']){
							$arrDado['data_tempo_resposta'] = @date('Y-m-d H:i:s');
						}
							
						$arrDado['atividades'] = $arrFluxo[0]['atividades']."\n\n- - - - - - ".$_SESSION['sessionUser']['nome']." enviou e-mail às: ".@date('H:i:s d/m/Y')." - - - - - -\n\n".$corpo;

						if($dbFluxoClientes->edt($arrFluxo[0]['id'], $arrDado)){
							echo $arrDado['atividades'];
						}

					}
					
				}
			
			}
			
		}elseif($this->_getParam('fn') == "modelo_email"){
			
			$dbModelosEmails = new Application_Model_DbTable_ModelosEmails();
			
			$dados['titulo'] = $this->_getParam('titulo');
			$dados['texto'] = $this->_getParam('texto');

			if($this->_getParam('id')){
				
				$arr = $dbModelosEmails->getModeloEmail($this->_getParam('id'));
				
				if($arr[0]['id_empresa'] && $arr[0]['id_usuario']){

					if($dbModelosEmails->edt($this->_getParam('id'), $dados)){
						echo "sucesso";
					}
					
				}else{
					
					echo "Não é possível editar este modelo de e-mail, pois é um modelo padrão do sistema.";
					
				}

			}else{
				
				$dados['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
				$dados['id_usuario'] = $_SESSION['sessionUser']['id'];
				
				if($dbModelosEmails->add($dados)){
					echo "sucesso";
				}
			}

		}elseif($this->_getParam('fn') == "get_modelos_emails"){
			
			$dbModelosEmails = new Application_Model_DbTable_ModelosEmails();
			
			$arrEmails = $dbModelosEmails->getModelosEmails();
			
			$str = "";
			
			foreach($arrEmails as $arr){
				$str .= $arr['id']."|".$arr['titulo']."|".$arr['texto']."||";
			}
			
			echo substr($str, 0, -2);
			
		}

	}

	public function emailNatalAnoNovoAction(){
	
		$dbClientes = new Application_Model_DbTable_Clientes();
		
		$arrClientes = $dbClientes->_getClientesMesAniversario('12');
		
		$cont = 0;
		
		foreach($arrClientes as $cliente){
		
			$cont++;
		
			if($cliente['sexo'] == 1){
		
				$nome = "Sra. ".$cliente['nome'];
				$telo = "t&ecirc;-la";
				
			}elseif($cliente['sexo'] == 0){
			
				$nome = "Sr. ".$cliente['nome'];
				$telo = "t&ecirc;-lo";
			
			}
		
			$corpo = "<html>
						<head>
						
						</head>
						<body>
							<div style='width:600px; height:380px; position: absolute; border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px;background-repeat:repeat; background-image:url(\"http://www.sistemameucar.com.br/images_comemorativas/fundo_natal.jpg\");'>
								<center>
									<table width='80%'>
										<tr>
											<td>
												<div style='font-family: Arial, Helvetica, sans-serif; float:top; color: #fff; font-weight: bold; font-size:14px; margin-top:60px; margin-left:20px;'>
													Olá,<br/><br/>
														".$nome.".<br/><br/>
														Nós da ".$cliente['nome_fantasia']." agradecemos sua confiança em relação a nossa empresa e desejamos a você e a seus familiares um feliz natal e um próspero ano novo!
														<br/><br/>
														<br/><br/>
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<center>
													<div style='font-family: Arial, Helvetica, sans-serif; float:bottom; position:relative; margin-left:20px;'>
														<img width='160px' height='80px' src=\"http://www.sistemameucar.com.br/".$cliente['path']."\" /><br/>
														<a target='_blanck' style='font-size:13px; color:#fff;' href='http://".$cliente['url_site']."'>".$cliente['url_site']."</a>
													</div>
												</center>
											</td>
										</tr>
									</table>
								</center>
							</div>
						</body>
					</html>";
				
				$assunto = $cliente['nome_fantasia'];
				
			//$this->enviaEmail($cliente['email'], $assunto, $corpo, $attach = false);
			
			
			
			//echo $corpo."<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>";
			
			//exit;
		
		}
		
		echo $cont;

	}
	
	
	public function emailSeisMesesAction(){
	
		$dbClientes = new Application_Model_DbTable_Clientes();
		
		$data = @date("Y-m-d", mktime(0, 0, 0, @date("m")-6, @date("d"), @date("Y")));
		
		$arrClientes = $dbClientes->clienteEmpresaNegociacao($data);
		
		foreach($arrClientes as $cliente){
		
			if($cliente['sexo'] == 1){
		
				$nome = "Sra. ".$cliente['nome'];
				$telo = "t&ecirc;-la";
				
			}elseif($cliente['sexo'] == 0){
			
				$nome = "Sr. ".$cliente['nome'];
				$telo = "t&ecirc;-lo";
			
			}
		
			$corpo = "<html>
						<head>
						
						</head>
						<body>
							<div style='width:600px; height:410px; position: absolute; border:solid 2px #19F; border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px;background-repeat:repeat; background-image:url(\"http://www.sistemameucar.com.br/images_comemorativas/logos_carros.jpg\");'>
								<center>
									<table width='90%'>
										<tr>
											<td>
												<div style='font-family: Arial, Helvetica, sans-serif; float:top; color: #19F; font-weight: bold; font-size:14px; margin-top:30px;'>
													Olá,<br/><br/>
														".$nome.".<br/><br/>
														Estamos enviando esse email para que você saiba que estamos a disposição caso tenha alguma dúvida ou sugestão sobre nossa empresa, gostariamos muito de ouvir sua opinião sobre nosso trabalho.<br/><br/> 
														E caso tenha alguma indicação teremos prazer em atender esse novo cliente.<br/><br/>
														Agradecemos sua atenção.<br/><br/>
														".$cliente['nome_fantasia']."<br/><br/>
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<center>
													<div style='font-family: Arial, Helvetica, sans-serif; float:bottom; position:relative; margin-left:20px;'>
														<img width='160px' height='80px' src=\"http://www.sistemameucar.com.br/".$cliente['path']."\" /><br/>
														<a target='_blanck' style='font-size:13px; color:#333;' href='http://".$cliente['url_site']."'>".$cliente['url_site']."</a>
													</div>
												</center>
											</td>
										</tr>
									</table>
								</center>
							</div>
						</body>
					</html>";
				
				$assunto = $cliente['nome_fantasia'];
				
			$this->enviaEmail($cliente['email'], $assunto, $corpo, $attach = false);
			
			//echo $corpo."<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>";
			
			//exit;
		
		}

	}
	
	
	public function emailAniversariosAction(){

		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
	
		$dbAcaoAutomatica = new Application_Model_DbTable_AcaoAutomatica();

		$arrFiltro['acao'] = 'email_aniversario';
		//$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

		$arr = $dbAcaoAutomatica->_get($arrFiltro);

		
		if($arr[0][data] != @date('Y-m-d')){

			if($arr[0][data]){
				$dbAcaoAutomatica->edt($arr[0][id], array('data'=>@date('Y-m-d')));
			}else{
				$dbAcaoAutomatica->add(array('data'=>@date('Y-m-d'),'acao'=>'email_aniversario'));
			}

			$dbClientes = new Application_Model_DbTable_Clientes();
			$arrClientes = $dbClientes->clienteEmpresaAniversario();

			// echo "<pre>";
			// var_export($arrClientes);
			// echo "</pre>";
			// exit;

			foreach($arrClientes as $cliente){

				$corpo = "<html>
						<head>
						</head>
						<body>
							<center>
							<div style='background-image:url(\"http://www.sistemameucar.com.br/images_comemorativas/background_aniversario_2.jpg\"); background-repeat:no-repeat; width:415px; height:626px; ' >
								<table>
									<tr>
										<td>
											<center>
											<div style='color: rgb(255, 255, 255); font-weight: bold; font-size:190%; margin-top:190px; text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.5);'>
												Feliz Aniversário!
											</div>
											</center>
										</td>
									</tr>
									<tr>
										<td>
											<div style='width:380px; float:top; color:rgb(255, 255, 255); font-weight: bold; font-size:140%; margin-top:30px;  position:relative; text-align: center; text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.5);'>
												".ucwords($cliente['nome']).", hoje é um dia especial e estamos muito felizes por ter você como cliente da nossa loja.<br/><br/>
												Desejamos muito sucesso, saúde e prosperidade em sua vida!
											</div>
											<br/>
										</td>
									</tr>
									<tr>
										<td>
											<center>
												<div style='float:bottom; position:relative; margin-left:20px; margin-top:30px;'>
													<img width='160px' height='80px' src=\"http://www.sistemameucar.com.br/".$cliente['path']."\" /><br/>
													<a target='_blanck' style='font-size:13px; color:#333;' href='http://".$cliente['url_site']."'>".$cliente['url_site']."</a>
												</div>
											</center>
										</td>
									</tr>
								</table>
							</div>
							</center>
						</body>
					</html>";


				$assunto = $cliente['nome_fantasia'];

				//echo $corpo;

				if($cliente['email'] && $cliente['empresa_email']){

					$empresaEmail = $cliente['empresa_email'];
					$nomeFantasia = $cliente['nome_fantasia'];

					$destinatario = $cliente['email'];

					if(self::enviaMailerCentralizado($destinatario, $assunto, $corpo, $empresaEmail, $nomeFantasia)){
						echo "Sucesso!<br>";
						//sleep(1);
					}else{
						echo "Erro!<br>";
						//sleep(1);
					}

				}

			}
		
		}
		
	
	}


	function enviaMailerCentralizado($destinatario, $assunto, $corpo, $emailResp, $nomeSite){

		//echo $destinatario;
	
		$url  = 'https://meucar.com.br/controllers/envia-email-lojistas.php';
		$data = [
			'hash' => md5(@date("Y-m-d H:i")."envia_centralizado"), 
			'destinatario' => $destinatario,
			'assunto' => $assunto,
			'corpo' => $corpo,
			'emailResp' => $emailResp,
			'nomeSite' => $nomeSite,
			 ];
	
		$ch   = curl_init();
	
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
		if(curl_exec($ch) == 0) {
			//$error_message = curl_strerror($errno);
			$result = false;
		}else{
			$result = true;
		}
	
		curl_close($ch);
	
		return $result;
	
	}
	
	
	// private function enviaEmail($para, $assunto, $corpo, $attach = false){

	// 	$config = array(
	// 		'auth' => 'login',
	// 		'username' => 'sistemameucar@sistemameucar.com.br',
	// 		'password' => 'g010502g',
	// 		'port' => '587'
	// 	);

	// 	$transport = new Zend_Mail_Transport_Smtp('smtp.sistemameucar.com.br', $config);

	// 	$mail = new Zend_Mail('UTF-8');
	// 	$mail->setBodyHtml($corpo);
	// 	$mail->setFrom('sistemameucar@sistemameucar.com.br');
	// 	$mail->addTo($para);
	// 	//$mail->addBcc('gesiel.diniz@gmail.com');
	// 	$mail->setSubject($assunto);
	   

	// 	try{

	// 		if($attach){

	// 			$mail->createAttachment(file_get_contents($attach), 'text/plain', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $attach);
         
	// 		}

	// 		return $mail->send($transport);
			
	// 	}catch(Exception $e){

	// 		//echo $e->getMessage();
			
	// 	}
		
	// }

}

?>
