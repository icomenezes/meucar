<?php

header("Content-Type: text/html; charset=UTF-8",true);

class UsuariosController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Usuarios";

		Zend_Session::start();
		
	}
	
	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function ajaxAction(){
		
		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
		
		header('Content-Type: application/json; charset=utf-8');
		
		if($this->_getParam('fn') == 'getComissoes'){
		
			$dbVendedores = new Application_Model_DbTable_Vendedores();
			
			$arrComissoes = $dbVendedores->getComissoes($this->_getParam('id'));
			
			echo $arrComissoes[0]['valor_fixo']."_".$arrComissoes[0]['percentual_venda'];
			
		}elseif($this->_getParam('fn') == 'getEmpresas'){
		
			// Obtém todas as empresas do banco de dados
			$dbEmpresas = new Application_Model_DbTable_Empresas();
			$arrEmpresas = $dbEmpresas->fetchAll();
			
			$empresas = array();
			if($arrEmpresas){
				foreach($arrEmpresas as $empresa){
					$empresas[] = array(
						'id' => $empresa['id'],
						'nome_fantasia' => $empresa['razao_social']
					);
				}
			}
			
			$resp = array('sucesso' => true, 'empresas' => $empresas);
			echo json_encode($resp);
		
		}elseif($this->_getParam('fn') == 'trocar-empresa'){
		
			$idEmpresa = $this->_getParam('id_empresa');
			
			if(!$idEmpresa){
				$resp = array('sucesso' => false, 'mensagem' => 'Parâmetro id_empresa ausente');
			}else{
				if(!isset($_SESSION['sessionUser'])){
					$resp = array('sucesso' => false, 'mensagem' => 'Sessão inválida');
				}else{
					$_SESSION['sessionUser']['id_empresa'] = (int)$idEmpresa;
					$resp = array('sucesso' => true);
				}
			}
			
			echo json_encode($resp);
		
		}elseif($this->_getParam('fn') == 'verifica_logado'){
			
			if($_SESSION['sessionUser']['id_empresa']){
			
				echo $_SESSION['sessionUser']['id_empresa'];

			}else{
				
				echo "0";
			
			}
		
		}elseif($this->_getParam('fn') == 'desloga'){
			
			$dbEmpresas = new Application_Model_DbTable_Empresas();
			
			if($this->_getParam('id_empresa')){
			
				$dados['logado'] = "0";
			
				$dbEmpresas->edt($this->_getParam('id_empresa'), $dados);

			}
		
		}elseif($this->_getParam('fn') == 'loga'){
			
			$dbEmpresas = new Application_Model_DbTable_Empresas();
			
			if($this->_getParam('id_empresa')){
			
				$dados['logado'] = "1";
			
				$dbEmpresas->edt($this->_getParam('id_empresa'), $dados);

			}
			
		}elseif($this->_getParam('fn') == 'deleta_foto_usuario'){
		
		
			$dbUsuarios = new Application_Model_DbTable_Usuarios();
			
			$arrUsuarios = $dbUsuarios->_get(array('id'=>$this->_getParam('id')));

			$path = $arrUsuarios[0]['path_foto'];
			
			$dadosUp['path_foto'] = null;

			if($dbUsuarios->update($dadosUp,"id = ".$this->_getParam('id'))){
				
				unlink($path);
			
				echo "Sucesso";
			
			}else{
			
				echo "Erro ao deletar foto";
			
			}

		}
	
	}
	
	public function esqueciSenhaAction(){
	
		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
		
		if($this->getRequest()->isPost()){
		
			$dbUsuarios = new Application_Model_DbTable_Usuarios();
			
			$arrUsuario = $dbUsuarios->getEmail($_POST['email']);
			
			if($arrUsuario){
			
				$hash = md5($arrUsuario[0]['email']);
				
				$hash = substr($hash,0,6);
				
				$hashBanco = md5($hash);
				
				$body = "
				<html>
					<head>
						<title>MeuCar</title>
						<style>
							
							table tr td{
								#border:solid 1px;
								width:100%;
								font-family: Arial, Helvetica, sans-serif;
							}
							
							a img{
								width:200px;
								float:right;
							}
						</style>
					</head>
					<body>
						<table style='background-color:#E8E8E8; border:1px solid #CCCCCC; color:#666666; font:14px Arial, Helvetica, sans-serif;'>
							<tr><td style='font-size:20px;'><br>Ol&aacute; ".$arrUsuario[0]['nome'].".</td><td><!--<a href='http://meucar.b1t.com.br/site'><img src='http://meucar.b1t.com.br/arquivos_site/images/Logo_Car.png'/></a>--></td></tr>
							<tr><td colspan='2' style='height:10px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
							<tr><td colspan='2'>Segue abaixo os dados de acesso.<br><br></td></tr>
							<tr><td colspan='2'>LOGIN: ".$arrUsuario[0]['login'].".</td></tr>
							<tr><td colspan='2'>SENHA: ".$hash.".</td></tr>
							<tr><td colspan='2' style='height:10px;'></td></tr>
							<tr><td colspan='2' style=''><center><img style='width:150px;' src='http://meucar.b1t.com.br/arquivos_site/images/Logo_Car.png'/><br><a href='http://meucar.b1t.com.br/site'>www.sistemameucar.com.br</a></center><br></td></tr>
						</table>
					</body>
				</html>";

				if($this->enviaEmail($arrUsuario[0]['email'], "Teste", $body)){
				
					$this->view->mensagem = "Um email com a nova senha foi enviada para <b>".$arrUsuario[0]['email']."</b>.<br><br><a href='/index'>www.sistemameucar.com.br</a>";
					$this->view->status = 1;
					
					$dados['senha'] = $hashBanco;
					
					$dbUsuarios->update($dados, 'id = '.$arrUsuario[0]['id']);
				
				}else{
				
					$this->view->mensagem = "Erro no envio do e-mail, por favor tente novamente!<br><br><a href='/usuarios/esqueci-senha'/>Voltar</a>";
					$this->view->status = -1;
				
				}
			
			}else{
			
				$this->view->mensagem = "E-mail não encontrado, por favor tente novamente!<br><br><a href='/usuarios/esqueci-senha'/>Voltar</a>";
				$this->view->status = -1;
			
			}
		
		}
	
	}
	
	public function edtUsuarioSiteAction(){
	
		//$this->validaAcesso('gerenciar_usuarios');
	
		$dbPerfil = new Application_Model_DbTable_Perfis();
		$dbUsuario = new Application_Model_DbTable_Usuarios();
		$dbVendedor = new Application_Model_DbTable_Vendedores();
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$antigoVendedor = 0;
		$novoVendedor = 0;
		
		$arrFiltro['id'] = $this->_getParam('id');		
		$usuario = $dbUsuario->_get($arrFiltro);
		//var_export($usuario);exit;
		
		$datas = explode("-",$usuario[0]['data_contratacao']);
		$usuario[0]['data_contratacao'] = implode("/", array_reverse($datas));
		
		$datas = explode("-",$usuario[0]['data_nascimento']);
		$usuario[0]['data_nascimento'] = implode("/", array_reverse($datas));
			
		
		
		//var_export($usuario);exit;

		$this->view->usuario = $usuario[0];
		
		if($usuario[0]['id_perfil'] == VENDEDOR || $usuario[0]['id_perfil'] == GERENTE || $usuario[0]['id_perfil'] == SUPERVISOR){
			
			$arrFiltro['id'] = $this->_getParam('id');
		
			$vendedor = $dbVendedor->_get($arrFiltro);
			
			$this->view->vendedor = $vendedor[0];
			
			$antigoVendedor = 1;
			
		}
		
		if($this->getRequest()->isPost()){
			
			$dados = $_POST;
			
			$idUsuario = $dados['id'];
			
			/*if((int)$dados['id_empresa'] >= 1){
			
				//continue;
			
			}else{
			
				$dados['id_empresa'] = null;
			
			}*/
			
			$dadosVendedor['valor_fixo'] = str_replace(".","",$dados['valor_fixo']);
			$dadosVendedor['valor_fixo'] = str_replace(",",".",$dadosVendedor['valor_fixo']);
			$dadosVendedor['percentual_venda'] = $dados['percentual_venda'];
			$dadosVendedor['manual'] = $dados['manual'];
			$dadosVendedor['percentual_retorno_financeiro'] = $dados['percentual_retorno_financeiro'];
			$dadosVendedor['id_usuario'] = $idUsuario;
			
			unset($dados['valor_fixo']);
			unset($dados['percentual_venda']);
			unset($dados['manual']);
			unset($dados['percentual_retorno_financeiro']);
			
			/*if($dados['id_perfil'] == VENDEDOR || $dados['id_perfil'] == GERENTE){
			
				$dados['cargo'] = NULL;
			
			}*/
			
			if($dados['id_perfil'] == VENDEDOR || $dados['id_perfil'] == GERENTE || $dados['id_perfil'] == SUPERVISOR){
			
				$novoVendedor = 1;
			
			}
			
			$dados['valor_fixo_mensal'] = str_replace(".","",$dados['valor_fixo_mensal']);
			$dados['valor_fixo_mensal'] = str_replace(",",".",$dados['valor_fixo_mensal']);
			
			//var_export($dados);
			//exit;
			
			//var_export($novoVendedor);
			//exit;
			
			$dados['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dados['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			$datas = explode("/",$dados['data_nascimento']);
			$dados['data_nascimento'] = implode("-", array_reverse($datas));
			
			$datas = explode("/",$dados['data_contratacao']);
			$dados['data_contratacao'] = implode("-", array_reverse($datas));

			$dados['data_demissao'] = implode("-",array_reverse(explode("/",$dados['data_demissao'])));
			
			if($dbUsuario->update($dados, "id = " . $idUsuario)){

				if($antigoVendedor == 1 && $novoVendedor == 1){
					
						if($dbVendedor->update($dadosVendedor, "id_usuario = " . $idUsuario)){
						
							$this->_helper->redirector->gotoUrl("usuarios/edt-usuario-site");
						
						}else{
						
							//$this->view->mensagem = "Erro ao editar vendedor / gerente. Ou não houve altera&ccedil;&atilde;o de dados!.";
						
						}
					
					
				}elseif($antigoVendedor == 1 && $novoVendedor == 0){
					
					$dbVendedor->delete("id_usuario = " . $dados['id']);
					
					$this->_helper->redirector->gotoUrl("usuarios/edt-usuario-site");
					
				}elseif($antigoVendedor == 0 && $novoVendedor == 1){
					
					$dbVendedor->insert($dadosVendedor);
					
					$this->_helper->redirector->gotoUrl("usuarios/edt-usuario-site");
					
				}
		
			}else{
				
				//var_export("=(");
				//exit;
			
				$this->view->mensagem = "Erro ao editar usuario!.";
			
			}

		}

		
		$arrPerfil = $dbPerfil->fetchAll();
		
		$this->view->perfis = $arrPerfil;
		
		$arrFiltro['id'] = $this->_getParam('id');
		
		$usuario = $dbUsuario->_get($arrFiltro);

		$arrEmpresas = $dbEmpresas->fetchAll();
		$this->view->empresas = $arrEmpresas;
		
		if($usuario[0]['id_perfil'] == VENDEDOR || $usuario[0]['id_perfil'] == GERENTE || $usuario[0]['id_perfil'] == SUPERVISOR){
			
			$arrFiltro['id'] = $this->_getParam('id');
		
			$vendedor = $dbVendedor->_get($arrFiltro);
			
			$this->view->vendedor = $vendedor[0];
			
			$antigoVendedor = 1;
			
		}
		
		$this->view->usuario = $usuario[0];
	
	}
	
	public function addAction(){
	
		$this->validaAcesso('gerenciar_usuarios');
		
		$dbPerfil = new Application_Model_DbTable_Perfis();
		
		if($_SESSION['sessionUser']['id_perfil'] == 1){
		
			$arrPerfil = $dbPerfil->fetchAll();
		
		}else{
		
			$arrPerfil = $dbPerfil->fetchAll('id IN (' .VENDEDOR .',' .GERENTE . ',' .FUNCIONARIO . ','.ADMINISTRATIVO .','.SECRETARIA .','.AVALIADOR.','.SUPERVISOR.')');
		
		}
		
		$this->view->perfis = $arrPerfil;
		
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$arrEmpresas = $dbEmpresas->fetchAll();
		$this->view->empresas = $arrEmpresas;
	
		if($this->getRequest()->isPost()){
		
			$dbUsuario = new Application_Model_DbTable_Usuarios();
			
			$dados = $_POST;
			
			$senha = md5($dados['senha']);
			
			$dados['senha'] = $senha;
			
			$idEmpresa = $_POST['id_empresa'];
			
			if((int)$_POST['id_empresa'] == 0){
			
				unset($dados['id_empresa']);
			
			}
			
			$dadosVendedor['valor_fixo'] = str_replace(".","",$dados['valor_fixo']);
			$dadosVendedor['valor_fixo'] = str_replace(",",".",$dadosVendedor['valor_fixo']);
			$dadosVendedor['percentual_venda'] = $dados['percentual_venda'];
			$dadosVendedor['manual'] = $dados['manual'];
			$dadosVendedor['percentual_retorno_financeiro'] = $dados['percentual_retorno_financeiro'];
			
			unset($dados['valor_fixo']);
			unset($dados['percentual_venda']);
			unset($dados['manual']);
			unset($dados['percentual_retorno_financeiro']);
			
			$dados['valor_fixo_mensal'] = str_replace(".","",$dados['valor_fixo_mensal']);
			$dados['valor_fixo_mensal'] = str_replace(",",".",$dados['valor_fixo_mensal']);
			
			if($dados['id_perfil'] == FUNCIONARIO){
				
				$codex = $dados['nome'] . $dados['cargo'] . date("Y-m-d h:i:s");
				
				$codificacao = md5($codex);
				
				$dados['senha'] = $codificacao;
				$dados['login'] = $codificacao;
			
			}
			
			/*if($dados['id_perfil'] == VENDEDOR || $dados['id_perfil'] == GERENTE){
			
				$dados['cargo'] = null;
			
			}*/
			
			$dados['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dados['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			$datas = explode("/",$dados['data_nascimento']);
			$dados['data_nascimento'] = implode("-", array_reverse($datas));
			
			$datas = explode("/",$dados['data_contratacao']);
			$dados['data_contratacao'] = implode("-", array_reverse($datas));

			if(in_array($_FILES['foto']['type'], array("image/pjpeg", "image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp"))){
			//if(eregi("^image\/(pjpeg|jpeg|png|gif|bmp)$", $_FILES['foto']['type'])){
				
				
				if(file_exists("usuarios_empresas/".$idEmpresa)){
					
					$indice = count(explode(".",$_FILES['foto']['name']))-1;
					$extensao = strtolower(explode(".",$_FILES['foto']['name'])[$indice]);
					$novoNome = "usuarios_empresas/".$idEmpresa."/".str_replace(" ","-",$_POST['nome']).".".$extensao;
		
				}else{
					
					$indice = count(explode(".",$_FILES['foto']['name']))-1;
					$extensao = strtolower(explode(".",$_FILES['foto']['name'])[$indice]);
					mkdir("usuarios_empresas/".$idEmpresa, 0755, true);
					$novoNome = "usuarios_empresas/".$idEmpresa."/".str_replace(" ","-",$_POST['nome']).".".$extensao;
			
				}

	
				if($_FILES['foto']['tmp_name'] != ""){

					/////////////////REDIMENCIONA IMAGEM///////////////////////
					# Caminho da imagem a ser redimensionada: 
					$input_image = $_FILES['foto']['tmp_name'];
			
					// Pega o tamanho original da imagem e armazena em um Array:
					$size = getimagesize( $input_image );
				
					// Configura a nova largura da imagem:
					$thumb_width = "640";
			
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
	
				}

				
				if($copied){
					
					unset($dados['foto']);
					$dados['path_foto'] = $novoNome;
					
				}
				
			}
			
			if(isset($dados['ativo'])){
				$dados['ativo'] = 1;
			}else{
				$dados['ativo'] = 0;
			}
			
			if(isset($dados['receber_emails'])){
				$dados['receber_emails'] = 1;
			}else{
				$dados['receber_emails'] = 0;
			}
			
			if(isset($dados['ativo_pesquisa'])){
				$dados['ativo_pesquisa'] = 1;
			}else{
				$dados['ativo_pesquisa'] = 0;
			}
			
			if(isset($dados['relatorio_projetado'])){
				$dados['relatorio_projetado'] = 1;
			}else{
				$dados['relatorio_projetado'] = 0;
			}
			
			
			try {
				$idVendedor = $dbUsuario->insert($dados);
			} catch (Exception $e) {
				if (strpos($e->getMessage(), '1062') !== false) {
					$this->view->mensagem     = "O login informado já está em uso. Escolha um login diferente.";
					$this->view->tipoMensagem = "erro";
				} else {
					$this->view->mensagem     = "Erro ao cadastrar usuário. Tente novamente.";
					$this->view->tipoMensagem = "erro";
				}
				$idVendedor = false;
			}

			if($idVendedor){

				if($dados['id_perfil'] == VENDEDOR || $dados['id_perfil'] == GERENTE || $dados['id_perfil'] == SUPERVISOR || $dados['id_perfil'] == CONCESSIONARIO){

					$dbVendedor = new Application_Model_DbTable_Vendedores();

					$dadosVendedor['id_usuario'] = $idVendedor;

					if($dbVendedor->insert($dadosVendedor)){

						$this->view->mensagem     = "Usuário cadastrado com sucesso!";
						$this->view->tipoMensagem = "sucesso";

					}else{

						$this->view->mensagem     = "Erro ao cadastrar usuário. Tente novamente.";
						$this->view->tipoMensagem = "erro";

					}
				
				}else{
				
					if($dados['id_perfil'] == 10){

						$arrEmpresas = $dbEmpresas->getEmpresa($idEmpresa);
				
						if($arrEmpresas[0]['novo_lojista'] == 1){
				
							if($_POST['email']){
					
								$para = $_POST['email'];

							}else{

								$para = $arrEmpresas[0]['email'];
					
							}

							$assunto = "Sua loja está cadastrada GRATUITAMENTE no site Meu Car.";
					
							$body = "<table bgcolor='#EFEFEF'>
										<tr><td>Olá <b>".$arrEmpresas[0]['nome_fantasia']."</b>,</td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>Segue abaixo o usuário e senha para acesso de seu anúncio gratuito em nosso site de vendas de veículos Meu Car:</td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>Login: <b>".$para."</b></td></tr>
										<tr><td>Senha: <b>".current(explode("@",$para))."123</b></td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>O site Meu Car conta com as melhores lojas da região e tem sido reconhecida como uma excelente ferramenta de vendas, proporcionando considerável aumento no fluxo de loja, ligações e email´s.  Trabalhamos com anúncios regionais e em redes sociais proporcionando um público mais regionalizado.</td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>Aproveite! Cadastre todo seu estoque gratuitamente em nosso portal e venda mais!</td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>Acesse já: <a target='_blank' href='http://www.sistemameucar.com.br'>www.sistemameucar.com.br</a></td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>Para adicionar seus anúncios clique em \"Área do revendedor\"</td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td><b>O que é o sistema Meu Car?</b></td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>Essa é uma excelente ferramenta de gestão para lojas de veículos, que não se limita apenas aos anúncios, mas oferece ainda:</td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr>
											<td>
												<ul>
													<li>Site e email personalizado para sua loja;</li>
													<li>Integração de estoque para o site Icarros e Webmotors ( Ou seja, o que você anunciar conosco pode ser automaticamente enviado para o Icarros e Webmotors);</li>
													<li>Gestão de revisões, despesas da loja, folha de pagamento e etc;</li>
													<li>Treinamentos comercias para sua equipe;</li>
													<li>Banco de currículos de profissionais de sua região;</li>
													<li>Teste de aptidão para contratação de novos vendedores;</li>
												</ul>
											</td>
										</tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>Tudo isso com mensais a partir de R$ 149,00! Para saber mais acesse:</td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td><a target='_blank' href='http://www.vendedorouoferecedor.com.br/sistema-de-loja-de-carros-meu-car.html'>http://www.vendedorouoferecedor.com.br/sistema-de-loja-de-carros-meu-car.html</a></td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>Ou entre em contato,</td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>Obrigado,</td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>Até mais.</td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td><center><img src='http://sistemameucar.com.br/arquivos_site/images/logo-meu-car.png' /></center></td></tr>
										<tr><td><center><a target='_blank' href='http://www.sistemameucar.com.br'>www.sistemameucar.com.br</a></center></td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>&nbsp;</td></tr>
										<tr><td>* Caso não deseje mais receber esses informativos responda esse email com a mensagem \"sair da lista\".</td></tr>
									</table>";
				
							$this->enviaEmailNovoLojista($para, $assunto, $body, $attach = false);

						}
	
					}
					
					$this->view->mensagem     = "Usuário cadastrado com sucesso!";
					$this->view->tipoMensagem = "sucesso";

				}

			}else{

				if(empty($this->view->mensagem)){
					$this->view->mensagem     = "Erro ao cadastrar usuário. Tente novamente.";
					$this->view->tipoMensagem = "erro";
				}

			}
			
		}
	
	}
	
	public function edtAction(){
	
		$this->validaAcesso('gerenciar_usuarios');
	
		$dbPerfil = new Application_Model_DbTable_Perfis();
		$dbUsuario = new Application_Model_DbTable_Usuarios();
		$dbVendedor = new Application_Model_DbTable_Vendedores();
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$antigoVendedor = 0;
		$novoVendedor = 0;
		
		$arrFiltro['id'] = $this->_getParam('id');
		
		$usuario = $dbUsuario->_get($arrFiltro);
		
		$arrFiltro['id'] = $this->_getParam('id');
		
		if($usuario[0]['id_perfil'] == VENDEDOR || $usuario[0]['id_perfil'] == GERENTE || $usuario[0]['id_perfil'] == SUPERVISOR){
			
			$arrFiltro['id'] = $this->_getParam('id');
		
			$vendedor = $dbVendedor->_get($arrFiltro);
			
			if(isset($vendedor[0])){
				$this->view->vendedor = $vendedor[0];
			}
			
			$antigoVendedor = 1;
			
		}
		
		
		//echo $antigoVendedor;
		
		if($this->getRequest()->isPost()){
			
			$dados = $_POST;
			
			if($dados['senha'] == "*****"){
			
				unset($dados['senha']);
			
			}else{
			
				$senha = md5($dados['senha']);
				
				$dados['senha'] = $senha;
				
			}
			
			$idEmpresa = $dados['id_empresa'];
			
			$idUsuario = $dados['id'];
			
			if((int)$dados['id_empresa'] >= 1){
			
				//continue;
			
			}elseif((int)$dados['id_empresa'] == 0){
			
				unset($dados['id_empresa']);
			
			}else{
			
				$dados['id_empresa'] = null;
			
			}
			
			$dadosVendedor['valor_fixo'] = str_replace(".","",$dados['valor_fixo']);
			$dadosVendedor['valor_fixo'] = str_replace(",",".",$dadosVendedor['valor_fixo']);
			$dadosVendedor['percentual_venda'] = $dados['percentual_venda'];
			$dadosVendedor['manual'] = $dados['manual'];
			$dadosVendedor['percentual_retorno_financeiro'] = $dados['percentual_retorno_financeiro'];
			$dadosVendedor['id_usuario'] = $idUsuario;
			
			unset($dados['valor_fixo']);
			unset($dados['percentual_venda']);
			unset($dados['manual']);
			unset($dados['percentual_retorno_financeiro']);
			
			/*if($dados['id_perfil'] == VENDEDOR || $dados['id_perfil'] == GERENTE){
			
				$dados['cargo'] = NULL;
			
			}*/
			

			///Regra antiga, não entendí a necessidade/////////////
			//if($dados['id_perfil'] == VENDEDOR || $dados['id_perfil'] == GERENTE || $dados['id_perfil'] == SUPERVISOR  || $dados['id_perfil'] == CONCESSIONARIO){
			if($dados['id_perfil'] == VENDEDOR || $dados['id_perfil'] == GERENTE || $dados['id_perfil'] == SUPERVISOR){
			
				$novoVendedor = 1;
			
			}
			
			$dados['valor_fixo_mensal'] = str_replace(".","",$dados['valor_fixo_mensal']);
			$dados['valor_fixo_mensal'] = str_replace(",",".",$dados['valor_fixo_mensal']);
			
			//var_export($dados);
			//exit;
			
			//var_export($novoVendedor);
			//exit;
			
			$dados['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dados['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			$datas = explode("/",$dados['data_nascimento']);
			$dados['data_nascimento'] = implode("-", array_reverse($datas));
			
			$datas = explode("/",$dados['data_contratacao']);
			$dados['data_contratacao'] = implode("-", array_reverse($datas));
			
			$datas = explode("/",$dados['data_demissao']);
			$dados['data_demissao'] = implode("-", array_reverse($datas));
			

			if(in_array($_FILES['foto']['type'], array("image/pjpeg", "image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp"))){
			//if(eregi("^image\/(pjpeg|jpeg|png|gif|bmp)$", $_FILES['foto']['type'])){
				
				
				if(file_exists("usuarios_empresas/".$idEmpresa)){
					
					$indice = count(explode(".",$_FILES['foto']['name']))-1;
					$extensao = strtolower(explode(".",$_FILES['foto']['name'])[$indice]);
					$novoNome = "usuarios_empresas/".$idEmpresa."/".str_replace(" ","-",$_POST['nome']).".".$extensao;
		
				}else{
					
					$indice = count(explode(".",$_FILES['foto']['name']))-1;
					$extensao = strtolower(explode(".",$_FILES['foto']['name'])[$indice]);
					mkdir("usuarios_empresas/".$idEmpresa, 0755, true);
					$novoNome = "usuarios_empresas/".$idEmpresa."/".str_replace(" ","-",$_POST['nome']).".".$extensao;
			
				}

	
				if($_FILES['foto']['tmp_name'] != ""){

					/////////////////REDIMENCIONA IMAGEM///////////////////////
					# Caminho da imagem a ser redimensionada: 
					$input_image = $_FILES['foto']['tmp_name'];
			
					// Pega o tamanho original da imagem e armazena em um Array:
					$size = getimagesize( $input_image );
				
					// Configura a nova largura da imagem:
					$thumb_width = "640";
			
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
	
				}

				
				if($copied){
					
					unset($dados['foto']);
					$dados['path_foto'] = $novoNome;
					
				}
				
			}
			
			if(isset($dados['ativo'])){
				$dados['ativo'] = 1;
			}else{
				$dados['ativo'] = 0;
			}
			

			if(isset($dados['receber_emails'])){
				$dados['receber_emails'] = 1;
			}else{
				$dados['receber_emails'] = 0;
			}
			
			if(isset($dados['ativo_pesquisa'])){
				$dados['ativo_pesquisa'] = 1;
			}else{
				$dados['ativo_pesquisa'] = 0;
			}

			if(isset($dados['relatorio_projetado'])){
				$dados['relatorio_projetado'] = 1;
			}else{
				$dados['relatorio_projetado'] = 0;
			}
			
			if($dbUsuario->update($dados, "id = " . $idUsuario)){

				if($antigoVendedor == 1 && $novoVendedor == 1){
					
						if($dbVendedor->update($dadosVendedor, "id_usuario = " . $idUsuario)){
						
							$this->_helper->redirector->gotoUrl("usuarios/lista");
						
						}else{
						
							//$this->view->mensagem = "Erro ao editar vendedor / gerente. Ou não houve altera&ccedil;&atilde;o de dados!.";
						
						}
					
					
				}elseif($antigoVendedor == 1 && $novoVendedor == 0){
					
					$dbVendedor->delete("id_usuario = " . $dados['id']);
					
					$this->_helper->redirector->gotoUrl("usuarios/lista");
					
				}elseif($antigoVendedor == 0 && $novoVendedor == 1){
					
					$dbVendedor->insert($dadosVendedor);
					
					$this->_helper->redirector->gotoUrl("usuarios/lista");
					
				}
		
			}else{
				
				//var_export("=(");
				//exit;
			
				$this->view->mensagem = "Erro ao editar usuario!.";
			
			}

		}

		
		$arrPerfil = $dbPerfil->fetchAll();
		
		$this->view->perfis = $arrPerfil;
		
		$arrFiltro['id'] = $this->_getParam('id');
		
		$usuario = $dbUsuario->_get($arrFiltro);

		//$usuario = $dbUsuario->_get($arrFiltro);
		
		$datas = explode("-",$usuario[0]['data_contratacao']);
		$usuario[0]['data_contratacao'] = implode("/", array_reverse($datas));
		
		$datas = explode("-",$usuario[0]['data_nascimento']);
		$usuario[0]['data_nascimento'] = implode("/", array_reverse($datas));
		
		$this->view->usuario = $usuario[0];

		$arrEmpresas = $dbEmpresas->fetchAll();
		$this->view->empresas = $arrEmpresas;
		
		///Regra antiga, não entendí a necessidade/////////////
		//if($usuario[0]['id_perfil'] == VENDEDOR || $usuario[0]['id_perfil'] == GERENTE || $usuario[0]['id_perfil'] == SUPERVISOR || $dados['id_perfil'] == CONCESSIONARIO){
		if($usuario[0]['id_perfil'] == VENDEDOR || $usuario[0]['id_perfil'] == GERENTE || $usuario[0]['id_perfil'] == SUPERVISOR){
			
			$arrFiltro['id'] = $this->_getParam('id');
		
			$vendedor = $dbVendedor->_get($arrFiltro);
			
			if(isset($vendedor[0])){
				$this->view->vendedor = $vendedor[0];
			}
			
			$antigoVendedor = 1;
			
		}
	
	}
	
	public function listaAction(){
	
		$this->validaAcesso('listar_usuarios');
	
		$dbUsuarios = new Application_Model_DbTable_Usuarios();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		if($this->getRequest()->isPost()){
		
			$arrFiltro['parcial'] = true;
			$arrFiltro['login'] = $this->_getParam('login');
			$arrFiltro['empresa'] = $this->_getParam('concessionaria');
			$arrFiltro['perfil'] = $this->_getParam('perfil');
			
		}
		
		
		
		$arrUsuarios = $dbUsuarios->_get($arrFiltro);
			
		$this->view->usuarios = $arrUsuarios;
	
	}
	
	public function delAction(){
	
		$this->validaAcesso('gerenciar_usuarios');
	
		$dbUsuarios = new Application_Model_DbTable_Usuarios();
		
		$dados['excluido'] = 1;
		$dados['ativo'] = 0;
		
		$dbUsuarios->update($dados, "id = " .$this->_getParam('id'));
		
		$this->_helper->redirector->gotoUrl("usuarios/lista");
	
	}
	
	public function alterarSenhaAction(){
		
		$idUsuario = $_SESSION['sessionUser']['id'];
		
		$this->view->idUsuario = $idUsuario;
		
		if($this->getRequest()->isPost()){
			
			$senha = md5($_POST['senha']);
			
			$dados['senha'] = $senha;
			
			$idUsuario = $_POST['id'];
		
			$dbUsuarios = new Application_Model_DbTable_Usuarios();
			
			$dbUsuarios->update($dados, "id = " . $idUsuario);
			
			if($_SESSION['sessionUser']['id_perfil'] == 10){
			
				$this->_helper->redirector->gotoUrl("veiculos/lista");
				
			}else{
			
				$this->_helper->redirector->gotoUrl("agenda/agenda");
			
			}

		}
	
	}
	
	private function enviaEmail($para, $assunto, $body, $attach = false) {
		
		$config = array('auth' => 'login','username' => 'gesiel@b1t.com.br','password' => 'primeirodeagosto', 'ssl'=>'ssl','port'=>'465');
 
		$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
		 
		$mail = new Zend_Mail();
		$mail->setFrom('gesiel@b1t.com.br');
		$mail->addTo($para);
		$mail->setBodyHtml($body);
		$mail->setSubject($assunto);
		
		
		try {

			if ($attach) {

				$mail->createAttachment(file_get_contents($attach), 'text/plain', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $attach);
         
			}

			return $mail->send($transport);
      
		}catch (Exception $e){
		 
			//echo $e->getMessage();
			
		}
	}
	
	
	private function enviaEmailNovoLojista($para, $assunto, $body, $attach = false) {

		$transport = Internas_MailConfig::getTransport(Internas_MailConfig::CONTA_SISTEMA);

		$mail = new Zend_Mail('UTF-8');
		$mail->setBodyHtml($body);
		$mail->setFrom(Internas_MailConfig::getFrom(Internas_MailConfig::CONTA_SISTEMA));
		$mail->addTo($para);
		$mail->addBcc(Internas_MailConfig::getEmailBccLojista());
		$mail->setSubject($assunto);
		
		
		try {

			if ($attach) {

				$mail->createAttachment(file_get_contents($attach), 'text/plain', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $attach);
         
			}

			return $mail->send($transport);
      
		}catch (Exception $e){
		 
			//echo $e->getMessage();
			
		}
		
	}
	
}

?>
