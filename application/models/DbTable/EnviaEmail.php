<?php
class Application_Model_DbTable_EnviaEmail{
 
	public function envio(array $request){
	
		// AS VARIÁVEIS ABAIXO RECEBEM O CONTEÚDO ENVIADO PELO USUÁRIO ATRAVÉS DO FORMULÁRIO
 
		$remetente = array (
			'remetente' => $request['txt_de']
		);
 
		$destinatario = array (
			'destinatario' => $request['txt_para']
		);
 
		$mensagem = array (
			'mensagem' => $request['txt_msg']
		);
 
		$servidor = array (
			'servidor' => $request['txt_smtp']
		);
 
		$senha = array (
			'senha' => $request['txt_pass']
		);
 
		$config = array (
			'auth' => 'login',
			'username' => $remetente['remetente'],
			'password' => $senha['senha'],
			'port' => '587'
		);
 
		$mailTransport = new Zend_Mail_Transport_Smtp($servidor['servidor'], $config);  // ATIVA O OBJETO QUE FAZ CONEXÃO COM O SERVIDOR DE E-MAIL, PASSANDO O NOME DO SERVIDOR E OS DADOS 
											        // DE AUTENTICAÇÃO ESPECIFICADOS NA VARIÁVEL $config
 
		$mail = new Zend_Mail(); 							// CRIA UM OBJETO BASEADO NA CLASSE ZEND_MAIL(); E ARMAZENA NA VARIÁVEL $mail
		$mail->setFrom($remetente['remetente']); 			                // ESPECIFICA O PARÂMETRO FROM - REMETENTE
		$mail->addTo($destinatario['destinatario']);		                        // ESPECIFICA O PARÂMETRO TO - DESTINATÁRIO
		$mail->setSubject('TESTE LOCAWEB');					        // ASSUNTO DA MENSAGEM DO E-MAIL
		$mail->setBodyText($mensagem['mensagem']);			                // CONTEÚDO DA MENSAGEM
		$mail->send($mailTransport);					                // FUNÇÃO SEND(); QUE ENVIA A MENSAGEM COM TODOS OS PARÂMETROS ESPECIFICADOS E ARMAZENADOS E $mailTransport
	}
	
}

?>