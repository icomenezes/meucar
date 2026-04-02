<?php

header("Content-Type: text/html; charset=UTF-8", true);

class IndexController extends Zend_Controller_Action {

   public function init() {

      $this->initView();
      $layout = $this->_helper->layout();
      $layout->setLayout('login');
      $this->view->titulo = "Login";

      Zend_Session::start();

      $config = $this->getInvokeArg('bootstrap')->getOptions();
      $this->view->recaptchaEnabled = isset($config['recaptcha']['enabled']) ? (bool)$config['recaptcha']['enabled'] : true;
      $this->view->recaptchaSitekey = isset($config['recaptcha']['sitekey']) ? $config['recaptcha']['sitekey'] : '';
   }

   private function isRecaptchaEnabled() {
      $config = $this->getInvokeArg('bootstrap')->getOptions();
      return isset($config['recaptcha']['enabled']) ? (bool)$config['recaptcha']['enabled'] : true;
   }

   private function getRecaptchaSecretKey() {
      $config = $this->getInvokeArg('bootstrap')->getOptions();
      return isset($config['recaptcha']['secretkey']) ? $config['recaptcha']['secretkey'] : '';
   }

   private function verificaRecaptcha() {
      if (!$this->isRecaptchaEnabled()) {
         return true;
      }

      $captcha = $this->_getParam('g-recaptcha-response');
      if (!$captcha) {
         return false;
      }

      $secretKey = $this->getRecaptchaSecretKey();
      $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) . '&response=' . urlencode($captcha);
      $response = file_get_contents($url);
      $responseKeys = json_decode($response, true);

      return !empty($responseKeys["success"]);
   }


   	public function excluiFotosAntigasAction() {

   		$layout = $this->_helper->layout();
      	$layout->setLayout('no-layout');

      	$dbFotosVeiculo = new Application_Model_DbTable_FotosVeiculos();
      	$dbVeiculos = new Application_Model_DbTable_Veiculos();
      	$arrVeiculos = $dbVeiculos->getVeiculoAntigos();
      	$count = 0;
		foreach ($arrVeiculos as $key => $veiculo) {

			if($veiculo['data_aquisicao'] <= (@date("Y")-3)."-".@date("m-d")){
				$dbFotosVeiculo->del($veiculo['id_foto']);
				unlink($veiculo['path']);
				$count++;
			}

		}

		echo $count." Fotos foram excluidas!";

   	}

   	public function indexAction() {

      	//Provisório
      	//header("Location: " . URL . "index/adm");


      	//Produção
      	header("Location: " . URL . "index/login-v2/");

     	exit;
   	}

   	public function addEmailAction() {

   		if(!$_SESSION){header("Location: ".URL);}

      	$layout = $this->_helper->layout();
      	$layout->setLayout('loginv2');

      	if ($this->getRequest()->isPost()) {

      		if(isset($_SESSION['sessionUser']['id'])){

      			$email = $this->_getParam('email');
      			$email2 = $this->_getParam('email2');

      			if($email === $email2){
      				$dados = array('email' => strtolower(trim($email)));
	      			$usuarios = new Application_Model_DbTable_Usuarios();

	      			if($usuarios->edt($_SESSION['sessionUser']['id'], $dados)){

	      				if($_SESSION['sessionUser']['id_empresa'] == 3 || $_SESSION['sessionUser']['id_empresa'] == 239){
					   	//if($_SESSION['sessionUser']['id'] == 402){

							if ($_SESSION['sessionUser']['id_perfil'] == 3 || $_SESSION['sessionUser']['id_perfil'] == 9){

								$dbFluxoLoja = new Application_Model_DbTable_FluxoLoja();

								if(@date("w", mktime(0, 0, 0,  @date("m"), @date("d")-1, @date("Y"))) == 0){
									$arrFiltro['data'] = @date("Y-m-d", mktime(0, 0, 0,  @date("m"), @date("d")-2, @date("Y")));
								}else{
									$arrFiltro['data'] = @date("Y-m-d", mktime(0, 0, 0,  @date("m"), @date("d")-1, @date("Y")));
								}

								$arrFiltro['id_usuario'] = $_SESSION['sessionUser']['id'];
								$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

								if(!$dbFluxoLoja->getFluxoNovoRelatorio($arrFiltro)){
					   				$this->_helper->redirector->gotoUrl("/fluxo-loja/add-novo-relatorio-diario/obrigatorio/1/data/".$arrFiltro['data']);
								}

					   		}

					   	}


					   	if ($usuario[0]['id_perfil'] == 10){

					   		$this->_helper->redirector->gotoUrl("/estatisticas/visualiza-estatistica");
					   		//$this->_helper->redirector->gotoUrl("/negociacoes/lista");

					   	}elseif($_SESSION['sessionUser']['id_perfil'] == 6){

					   		$this->_helper->redirector->gotoUrl("/negociacoes/corretoras");

					   	}elseif($_SESSION['sessionUser']['id_perfil'] == 2){

					   		$this->_helper->redirector->gotoUrl("/agenda/agenda-concessionario");
					   		//$this->_helper->redirector->gotoUrl("/negociacoes/lista");

					   	}elseif($_SESSION['sessionUser']['id_perfil'] == 1){

					   		$this->_helper->redirector->gotoUrl("/agenda/agenda-concessionario");
					   		//$this->_helper->redirector->gotoUrl("/negociacoes/lista");

					   	}elseif($_SESSION['sessionUser']['id_perfil'] == 11){

					   		$this->_helper->redirector->gotoUrl("/veiculos/preparacao");

					   	}else{

					   		$this->_helper->redirector->gotoUrl("/agenda/agenda");
					   		//$this->_helper->redirector->gotoUrl("/negociacoes/lista");

					   	}

	      			}else{
	      				$this->_helper->redirector->gotoUrl("/index/add-email?erro=true");
	      			}

      			}

      		}

      	}

   	}


   	public function loginV2Action() {

		if (stristr($_SERVER['HTTP_HOST'], 'www')) {
			$arrDomain = explode('www', $_SERVER['HTTP_HOST']);
			header("Location: http://" . substr($arrDomain[1], 1) . "/index/login-v2/");
		}

		if($_SERVER['HTTP_HOST'] == "meucar.com.br" || $_SERVER['HTTP_HOST'] == "www.meucar.com.br"){
			header("Location: " . URL . "index/login-v2/");
		}

     	session_destroy();
      	session_start();

      	$layout = $this->_helper->layout();
      	$layout->setLayout('loginv2');

      	//$this->view->form = new Application_Form_Login();

   	}

   	public function admAction() {

		// if (stristr($_SERVER['HTTP_HOST'], 'www')) {

		// 	$arrDomain = explode('www', $_SERVER['HTTP_HOST']);

		// 	//header("Location: http://" . substr($arrDomain[1], 1) . "/index/adm/");
		// 	header("Location: http://" . substr($arrDomain[1], 1) . "/index/login-v2/");
			
		// }else{
		// 	header("Location: ".URL."index/login-v2/");
		// }

		header("Location: " . URL . "index/login-v2/");

     	session_destroy();
      	session_start();

      	$layout = $this->_helper->layout();
      	$layout->setLayout('login');

      	$this->view->form = new Application_Form_Login();
   	}

   public function badAccessAction() {

      session_start();
      $layout = $this->_helper->layout();
      $layout->setLayout('layout');
      $this->view->form = new Application_Form_Login();

   }

   	public function logarV2Action() {

   		if ($this->getRequest()->isPost()) {

	        if($this->verificaRecaptcha()) {

			   	$layout = $this->_helper->layout();
			   	$layout->setLayout('layout');

			   	session_destroy();
			   	session_start();

			   	$login = $this->_getParam('user');
			   	$senha = $this->_getParam('senha');

			   	$usuarios = new Application_Model_DbTable_Usuarios();
			   	$usuario = $usuarios->getUsuarioByLoginSenha($login, $senha);

			   	if (!$usuario) {
			   		$this->_helper->redirector->gotoUrl("/index/login-v2?erro=true");
			   	}

			   	if ($usuario[0]['id_perfil'] != 1){

			   		if ($usuario[0]['empresa_ativa'] == 0){

			   			$this->_helper->redirector();

			   		}
			   	}

			   	$sessionUser = new Zend_Session_Namespace('sessionUser');

			   	$sessionUser->id = $usuario[0]['id'];
			   	$sessionUser->nome = $usuario[0]['nome'];
			   	$sessionUser->login = $usuario[0]['login'];
			   	$sessionUser->email = $usuario[0]['email'];
			   	$sessionUser->id_perfil = $usuario[0]['id_perfil'];
			   	$sessionUser->id_empresa = $usuario[0]['id_empresa'];
			   	$sessionUser->nome_fantasia = $usuario[0]['nome_fantasia'];
			   	$sessionUser->empresa_email = $usuario[0]['empresa_email'];
			   	$sessionUser->vend_nao_edit_valor = $usuario[0]['vend_nao_edit_valor'];

			   	$vendedores = new Application_Model_DbTable_Vendedores();

			   	if ($usuario[0]['id_perfil'] == VENDEDOR) {

			   		$vendedor = $vendedores->fetchAll("id_usuario = " . $usuario[0]['id']);

			   		$sessionUser->valor_fixo = $vendedor[0]['valor_fixo'];
			   		$sessionUser->percentual_venda = $vendedor[0]['percentual_venda'];
			   		$sessionUser->manual = $vendedor[0]['manual'];
			   		$sessionUser->percentual_retorno_financeiro = $vendedor[0]['percentual_retorno_financeiro'];
			   	}

			   	unset($arr);

			   	$arr['id_empresa'] = $usuario[0]['id_empresa'];
			   	$arr['id_perfil'] = GERENTE;

			   	$gerente = $vendedores->_get($arr);

			   	$sessionUser->nome_gerente = $gerente[0]['nome'];
			   	$sessionUser->valor_fixo_gerente = $gerente[0]['valor_fixo'];
			   	$sessionUser->percentual_venda_gerente = $gerente[0]['percentual_venda'];
			   	$sessionUser->manual_gerente = $gerente[0]['manual'];
			   	$sessionUser->percentual_retorno_financeiro_gerente = $gerente[0]['percentual_retorno_financeiro'];

			   	unset($arr);

			   	$arr['id_empresa'] = $usuario[0]['id_empresa'];
			   	$arr['id_perfil'] = SUPERVISOR;

			   	$supervisor = $vendedores->_get($arr);

			   	$sessionUser->nome_supervisor = $supervisor[0]['nome'];
			   	$sessionUser->valor_fixo_supervisor = $supervisor[0]['valor_fixo'];
			   	$sessionUser->percentual_venda_supervisor = $supervisor[0]['percentual_venda'];
			   	$sessionUser->manual_supervisor = $supervisor[0]['manual'];
			   	$sessionUser->percentual_retorno_financeiro_supervisor = $supervisor[0]['percentual_retorno_financeiro'];

			   	$permissoes = new Application_Model_DbTable_Permissoes();

			   	$arrPermissoes = $permissoes->fetchAll("id_perfil = " . $usuario[0]['id_perfil']);

			   	foreach ($arrPermissoes[0] as $k => $p) {

			   		if ($p > 0 && $k != 'id' && $k != 'id_perfil') {

			   			$arrPermFinal[] = $k;
			   		}
			   	}

			   	$sessionUser->permissoes = $arrPermFinal;



			   	$dbLog = new Application_Model_DbTable_Log();

			   	$dados['id_usuario'] = $_SESSION['sessionUser']['id'];

			   	$dados['acao'] = "Logou";

			   	$dados['data'] = @date('Y-m-d');

			   	$dbLog->insert($dados);


			   	$dbEmpresas = new Application_Model_DbTable_Empresas();

			   	if($_SESSION['sessionUser']['id_empresa']){

			   		$dadosLogadoChat['logado'] = "1";

			   		$dbEmpresas->edt($_SESSION['sessionUser']['id_empresa'], $dadosLogadoChat);

			   	}

			   	if($usuario[0]['id'] != 1){

			   		$this->enviaEmailNovaEmpresa();

			   	}


			   	if($_SESSION['sessionUser']['id_empresa'] == 3 || $_SESSION['sessionUser']['id_empresa'] == 239){

			   		$dbAcaoAutomatica = new Application_Model_DbTable_AcaoAutomatica();

			   		$arr = $dbAcaoAutomatica->_get(array('acao'=>'email_vendedor'));

			   		if($arr[0]['data'] != @date('Y-m-d')){

			   			$dbUsuarios = new Application_Model_DbTable_Usuarios();
			   			$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();

			   			$arrVendedores = $dbUsuarios->getVendedoresAptosEmails();

			   			foreach($arrVendedores as $vendedores){

			   				$data = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-1, date('Y')));

			   				if(!$dbFluxoClientes->getFluxoVendedor($vendedores['id'], $data)){

			   					$arrEmail[$vendedores['id']]['nome'] = $vendedores['nome'];

			   				}

			   			}

			   			if($arrEmail){

			   				$this->enviaEmailFluxo($arrEmail);

			   			}

			   			$dbAcaoAutomatica->edt($arr[0]['id'],array('data'=>@date('Y-m-d')));

			   		}

			   	}

			   	if((!isset($usuario[0]['email']) || $usuario[0]['email'] == "") && isset($_SESSION['sessionUser']['id'])){
			   		$this->_helper->redirector->gotoUrl("/index/add-email");
			   	}

			   	if($_SESSION['sessionUser']['id_empresa'] == 3 || $_SESSION['sessionUser']['id_empresa'] == 239){
			   	//if($_SESSION['sessionUser']['id'] == 402){

					if ($_SESSION['sessionUser']['id_perfil'] == 3 || $_SESSION['sessionUser']['id_perfil'] == 9){

						$dbFluxoLoja = new Application_Model_DbTable_FluxoLoja();

						if(@date("w", mktime(0, 0, 0,  @date("m"), @date("d")-1, @date("Y"))) == 0){
							$arrFiltro['data'] = @date("Y-m-d", mktime(0, 0, 0,  @date("m"), @date("d")-2, @date("Y")));
						}else{
							$arrFiltro['data'] = @date("Y-m-d", mktime(0, 0, 0,  @date("m"), @date("d")-1, @date("Y")));
						}

						$arrFiltro['id_usuario'] = $_SESSION['sessionUser']['id'];
						$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

						if(!$dbFluxoLoja->getFluxoNovoRelatorio($arrFiltro)){
			   				$this->_helper->redirector->gotoUrl("/fluxo-loja/add-novo-relatorio-diario/obrigatorio/1/data/".$arrFiltro['data']);
						}

			   		}

			   	}


			   	if ($usuario[0]['id_perfil'] == 10){

			   		$this->_helper->redirector->gotoUrl("/estatisticas/visualiza-estatistica");
			   		//$this->_helper->redirector->gotoUrl("/negociacoes/lista");

			   	}elseif($_SESSION['sessionUser']['id_perfil'] == 6){

			   		$this->_helper->redirector->gotoUrl("/negociacoes/corretoras");

			   	}elseif($_SESSION['sessionUser']['id_perfil'] == 2){

			   		$this->_helper->redirector->gotoUrl("/agenda/agenda-concessionario");
			   		//$this->_helper->redirector->gotoUrl("/negociacoes/lista");

			   	}elseif($_SESSION['sessionUser']['id_perfil'] == 1){

			   		$this->_helper->redirector->gotoUrl("/agenda/agenda-concessionario");
			   		//$this->_helper->redirector->gotoUrl("/negociacoes/lista");

			   	}elseif($_SESSION['sessionUser']['id_perfil'] == 11){

			   		$this->_helper->redirector->gotoUrl("/veiculos/preparacao");

			   	}else{

			   		$this->_helper->redirector->gotoUrl("/agenda/agenda");
			   		//$this->_helper->redirector->gotoUrl("/negociacoes/lista");

			   	}

			}else{
				echo "O sistema identificou que seu acesso não é humano. Por favor, entre em contato com o administrador do sistema.";
				exit;
			}

		}else{
			$this->_helper->redirector->gotoUrl("/index/login-v2?erro=true");
		}

	}

	public function logarAction() {

		header("Location: ".URL."index/login-v2/");

	   	$layout = $this->_helper->layout();
	   	$layout->setLayout('layout');

	   	session_destroy();
	   	session_start();

	   	$login = $this->_getParam('user');
	   	$senha = $this->_getParam('senha');

	   	$usuarios = new Application_Model_DbTable_Usuarios();

	   	$usuario = $usuarios->getUsuarioByLoginSenha($login, $senha);

	   	if (!$usuario) {

	   		$this->_helper->redirector->gotoUrl("/index/login-v2");


	   	}

	   	if ($usuario[0]['id_perfil'] != 1){

	   		if ($usuario[0]['empresa_ativa'] == 0){

	   			$this->_helper->redirector();

	   		}
	   	}

	   	$sessionUser = new Zend_Session_Namespace('sessionUser');

	   	$sessionUser->id = $usuario[0]['id'];
	   	$sessionUser->nome = $usuario[0]['nome'];
	   	$sessionUser->login = $usuario[0]['login'];
	   	$sessionUser->email = $usuario[0]['email'];
	   	$sessionUser->id_perfil = $usuario[0]['id_perfil'];
	   	$sessionUser->id_empresa = $usuario[0]['id_empresa'];
	   	$sessionUser->nome_fantasia = $usuario[0]['nome_fantasia'];

	   	$vendedores = new Application_Model_DbTable_Vendedores();

	   	if ($usuario[0]['id_perfil'] == VENDEDOR) {

	   		$vendedor = $vendedores->fetchAll("id_usuario = " . $usuario[0]['id']);

	   		$sessionUser->valor_fixo = $vendedor[0]['valor_fixo'];
	   		$sessionUser->percentual_venda = $vendedor[0]['percentual_venda'];
	   		$sessionUser->manual = $vendedor[0]['manual'];
	   		$sessionUser->percentual_retorno_financeiro = $vendedor[0]['percentual_retorno_financeiro'];
	   	}

	   	unset($arr);

	   	$arr['id_empresa'] = $usuario[0]['id_empresa'];
	   	$arr['id_perfil'] = GERENTE;

	   	$gerente = $vendedores->_get($arr);

	   	$sessionUser->nome_gerente = $gerente[0]['nome'];
	   	$sessionUser->valor_fixo_gerente = $gerente[0]['valor_fixo'];
	   	$sessionUser->percentual_venda_gerente = $gerente[0]['percentual_venda'];
	   	$sessionUser->manual_gerente = $gerente[0]['manual'];
	   	$sessionUser->percentual_retorno_financeiro_gerente = $gerente[0]['percentual_retorno_financeiro'];

	   	unset($arr);

	   	$arr['id_empresa'] = $usuario[0]['id_empresa'];
	   	$arr['id_perfil'] = SUPERVISOR;

	   	$supervisor = $vendedores->_get($arr);

	   	$sessionUser->nome_supervisor = $supervisor[0]['nome'];
	   	$sessionUser->valor_fixo_supervisor = $supervisor[0]['valor_fixo'];
	   	$sessionUser->percentual_venda_supervisor = $supervisor[0]['percentual_venda'];
	   	$sessionUser->manual_supervisor = $supervisor[0]['manual'];
	   	$sessionUser->percentual_retorno_financeiro_supervisor = $supervisor[0]['percentual_retorno_financeiro'];

	   	$permissoes = new Application_Model_DbTable_Permissoes();

	   	$arrPermissoes = $permissoes->fetchAll("id_perfil = " . $usuario[0]['id_perfil']);

	   	foreach ($arrPermissoes[0] as $k => $p) {

	   		if ($p > 0 && $k != 'id' && $k != 'id_perfil') {

	   			$arrPermFinal[] = $k;
	   		}
	   	}

	   	$sessionUser->permissoes = $arrPermFinal;



	   	$dbLog = new Application_Model_DbTable_Log();

	   	$dados['id_usuario'] = $_SESSION['sessionUser']['id'];

	   	$dados['acao'] = "Logou";

	   	$dados['data'] = @date('Y-m-d');

	   	$dbLog->insert($dados);


	   	$dbEmpresas = new Application_Model_DbTable_Empresas();

	   	if($_SESSION['sessionUser']['id_empresa']){

	   		$dadosLogadoChat['logado'] = "1";

	   		$dbEmpresas->edt($_SESSION['sessionUser']['id_empresa'], $dadosLogadoChat);

	   	}

	   	if($usuario[0]['id'] != 1){

	   		$this->enviaEmailNovaEmpresa();

	   	}


	   	if($_SESSION['sessionUser']['id_empresa'] == 3 || $_SESSION['sessionUser']['id_empresa'] == 239){

	   		$dbAcaoAutomatica = new Application_Model_DbTable_AcaoAutomatica();

	   		$arr = $dbAcaoAutomatica->_get(array('acao'=>'email_vendedor'));

	   		if($arr[0]['data'] != @date('Y-m-d')){

	   			$dbUsuarios = new Application_Model_DbTable_Usuarios();
	   			$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();

	   			$arrVendedores = $dbUsuarios->getVendedoresAptosEmails();

	   			foreach($arrVendedores as $vendedores){

	   				$data = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-1, date('Y')));

	   				if(!$dbFluxoClientes->getFluxoVendedor($vendedores['id'], $data)){

	   					$arrEmail[$vendedores['id']]['nome'] = $vendedores['nome'];

	   				}

	   			}

	   			if($arrEmail){

	   				$this->enviaEmailFluxo($arrEmail);

	   			}

	   			$dbAcaoAutomatica->edt($arr[0]['id'],array('data'=>@date('Y-m-d')));

	   		}

	   	}

	   	if($_SESSION['sessionUser']['id_empresa'] == 3 || $_SESSION['sessionUser']['id_empresa'] == 239){
	   	//if($_SESSION['sessionUser']['id'] == 402){

			if ($_SESSION['sessionUser']['id_perfil'] == 3 || $_SESSION['sessionUser']['id_perfil'] == 9){

				$dbFluxoLoja = new Application_Model_DbTable_FluxoLoja();

				if(@date("w", mktime(0, 0, 0,  @date("m"), @date("d")-1, @date("Y"))) == 0){
					$arrFiltro['data'] = @date("Y-m-d", mktime(0, 0, 0,  @date("m"), @date("d")-2, @date("Y")));
				}else{
					$arrFiltro['data'] = @date("Y-m-d", mktime(0, 0, 0,  @date("m"), @date("d")-1, @date("Y")));
				}

				$arrFiltro['id_usuario'] = $_SESSION['sessionUser']['id'];
				$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

				if(!$dbFluxoLoja->getFluxoNovoRelatorio($arrFiltro)){
	   				$this->_helper->redirector->gotoUrl("/fluxo-loja/add-novo-relatorio-diario/obrigatorio/1/data/".$arrFiltro['data']);
				}

	   		}

	   	}


	   	if ($usuario[0]['id_perfil'] == 10){

	   		$this->_helper->redirector->gotoUrl("/estatisticas/visualiza-estatistica");
	   		//$this->_helper->redirector->gotoUrl("/negociacoes/lista");

	   	}elseif($_SESSION['sessionUser']['id_perfil'] == 6){

	   		$this->_helper->redirector->gotoUrl("/negociacoes/corretoras");

	   	}elseif($_SESSION['sessionUser']['id_perfil'] == 2){

	   		$this->_helper->redirector->gotoUrl("/agenda/agenda-concessionario");
	   		//$this->_helper->redirector->gotoUrl("/negociacoes/lista");

	   	}elseif($_SESSION['sessionUser']['id_perfil'] == 1){

	   		$this->_helper->redirector->gotoUrl("/agenda/agenda-concessionario");
	   		//$this->_helper->redirector->gotoUrl("/negociacoes/lista");

	   	}elseif($_SESSION['sessionUser']['id_perfil'] == 11){

	   		$this->_helper->redirector->gotoUrl("/veiculos/preparacao");

	   	}else{

	   		$this->_helper->redirector->gotoUrl("/agenda/agenda");
	   		//$this->_helper->redirector->gotoUrl("/negociacoes/lista");

	   	}


	}

	/*public function loginFacebookAction() {

		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		require_once('FacebookApi/facebook.php');

		$config = array(
			'appId' => '566753576702752',
			'secret' => 'f874fa062d6c5656154fe5b045897661',
			'scope' => 'email'
		);

		$facebook = new Facebook($config);

		$user_id = $facebook->getUser();
		
		//echo "Login com facebook em manutenção.<br> <a href='http://sistemameucar.com.br'>Clique aqui.</a>";
		//exit;
		
		if($user_id){

		/*$facebook->api("/me/feed", "post", array(
             'message' => "O site Meu Car tem as melhores ofertas de carros em Jundiaí e região, nossos classificados são um verdadeiro Feirão de Carros! E você do facebook ainda pode anunciar um veículo gratuitamente! Curta e compartilhe!",
             'name' => "Meu Car",
             'link' => "http://sistemameucar.com.br",
         ));
		 
		
			//Envia para a página de permissão do facebook, nela voce irá dar permissão ao aplicativo
			//acessar dados da sua conta
		$url = $facebook->getLoginUrl(array('scope' => array('publish_stream','read_stream')));
			
		
         $user_profile = $facebook->api('/me');
		 
		if ($user_id) {
		  $logoutUrl = $facebook->getLogoutUrl();
		} else {
		  $loginUrl = $facebook->getLoginUrl(array("scope" => "publish_stream,read_friendlists,email"));
		}


         $id = $user_profile['id'];
         $nome = $user_profile['first_name'] . " " . $user_profile['last_name'];
         $pageFacebook = $user_profile['link'];
         $email = $user_profile['email'];
         $local = $user_profile['location']['name'];
         $arrLocal = explode(",", $local);
         $cidade = $arrLocal[0];
         $estado = $arrLocal[1];
		 
        if($user_profile){

            $dbUsuarios = new Application_Model_DbTable_Usuarios();

            $usuario = $dbUsuarios->getEmail($email);
			
			var_export($user_profile['email']);
			var_export($user_profile);
			exit;

            if($usuario){

               $sessionUser = new Zend_Session_Namespace('sessionUser');

               $sessionUser->id = $usuario[0]['id'];
               $sessionUser->nome = $usuario[0]['nome'];
               $sessionUser->login = $usuario[0]['login'];
               $sessionUser->id_perfil = $usuario[0]['id_perfil'];
               $sessionUser->id_empresa = $usuario[0]['id_empresa'];

               $permissoes = new Application_Model_DbTable_Permissoes();

               $arrPermissoes = $permissoes->fetchAll("id_perfil = " . $usuario[0]['id_perfil']);

               foreach ($arrPermissoes[0] as $k => $p) {

                  if ($p > 0 && $k != 'id' && $k != 'id_perfil') {

                     $arrPermFinal[] = $k;
                  }
               }

               $sessionUser->permissoes = $arrPermFinal;

               $dbLog = new Application_Model_DbTable_Log();

               $dados['id_usuario'] = $_SESSION['sessionUser']['id'];

               $dados['acao'] = "Logou com o facebook";

               $dados['data'] = @date('Y-m-d');

               $dbLog->insert($dados);

               if ($usuario[0]['id_perfil'] == 10) {

                  $this->_helper->redirector->gotoUrl("/estatisticas/visualiza-estatistica");
               } else {

                  $this->_helper->redirector->gotoUrl("/agenda/agenda");
               }
            } else {

               $dbEmpresas = new Application_Model_DbTable_Empresas();

               $dadosEmpresa['razao_social'] = $nome;
               $dadosEmpresa['nome_fantasia'] = $nome;
               $dadosEmpresa['cnpj'] = $id;
               $dadosEmpresa['cidade'] = $cidade;
               $dadosEmpresa['estado'] = $estado;
               $dadosEmpresa['email'] = $email;
               $dadosEmpresa['sistema_site'] = 1;
               $dadosEmpresa['ativo'] = 1;

               $idEmpresa = $dbEmpresas->insert($dadosEmpresa);

               if ($idEmpresa) {

                  $dadosUsuario['id_perfil'] = 10;
                  $dadosUsuario['id_empresa'] = $idEmpresa;
                  $dadosUsuario['nome'] = $nome;
                  $dadosUsuario['login'] = $email;
                  $dadosUsuario['senha'] = md5($email);
                  $dadosUsuario['ativo'] = 1;
                  $dadosUsuario['usuario_facebook'] = 1;
                  $dadosUsuario['cidade'] = $cidade;
                  $dadosUsuario['estado'] = $estado;
                  $dadosUsuario['email'] = $email;
                  $dadosUsuario['hora_alteracao'] = @date("Y-m-d H:i:s");
                  $dadosUsuario['data_contratacao'] = @date("Y-m-d");

                  if ($dbUsuarios->insert($dadosUsuario)) {

                     $usuario = $dbUsuarios->getEmail($email);

                     $sessionUser = new Zend_Session_Namespace('sessionUser');

                     $sessionUser->id = $usuario[0]['id'];
                     $sessionUser->nome = $usuario[0]['nome'];
                     $sessionUser->login = $usuario[0]['login'];
                     $sessionUser->id_perfil = $usuario[0]['id_perfil'];
                     $sessionUser->id_empresa = $usuario[0]['id_empresa'];

                     $permissoes = new Application_Model_DbTable_Permissoes();

                     $arrPermissoes = $permissoes->fetchAll("id_perfil = " . $usuario[0]['id_perfil']);

                     foreach ($arrPermissoes[0] as $k => $p) {

                        if ($p > 0 && $k != 'id' && $k != 'id_perfil') {

                           $arrPermFinal[] = $k;
                        }
                     }

                     $sessionUser->permissoes = $arrPermFinal;

                     //var_export($_SESSION);exit;

                     $dbLog = new Application_Model_DbTable_Log();

                     $dados['id_usuario'] = $_SESSION['sessionUser']['id'];

                     $dados['acao'] = "Se cadastrou com o Facebook";

                     $dados['data'] = @date('Y-m-d');

                     $dbLog->insert($dados);

                     if ($usuario[0]['id_perfil'] == 10) {

                        $this->_helper->redirector->gotoUrl("/veiculos/lista");
                     } else {

                        $this->_helper->redirector->gotoUrl("/agenda/agenda");
                     }
                  }
               }
            }
         }
      } else {

         $login_url = $facebook->getLoginUrl(array('scope' => 'publish_stream'));
         header("Location:" . $login_url);
      }
   }*/
   
   /*
   public function loginFacebookAction(){

		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		require_once('FacebookApi/facebook.php');

		$config = array(
			'appId' => '566753576702752',
			'secret' => 'f874fa062d6c5656154fe5b045897661'
		);

		$facebook = new Facebook($config);

		$user_id = $facebook->getUser();
		
		if($user_id == 0){
		
			$url = $facebook->getLoginUrl(array('scope' => array('publish_stream','email')));
			header("Location:".$url);
		
		}else{
		
			//Verificando se o comando de logout foi enviado
			if($_GET['action'] == 'finish' ){
				//Retirando a permissão do Aplicativo à sua conta no facebook
				session_destroy();
				header('Location: '.$facebook->getLogoutUrl());
				
			}else{
			
				$facebook->api("/me/feed", "post", array(
					'message' => "O site Meu Car tem as melhores ofertas de carros em Jundiaí e região, nossos classificados são um verdadeiro Feirão de Carros! E você do facebook ainda pode anunciar um veículo gratuitamente! Curta e compartilhe!",
					'name' => "Meu Car",
					'link' => "http://sistemameucar.com.br",
				));
					
				$user_profile = $facebook->api('/me');
				
				$id = $user_profile['id'];
				$nome = $user_profile['first_name'] . " " . $user_profile['last_name'];
				$pageFacebook = $user_profile['link'];
				$email = $user_profile['email'];
				$userName = $user_profile['username'];
				$local = $user_profile['location']['name'];
				$arrLocal = explode(",", $local);
				$cidade = $arrLocal[0];
				$estado = $arrLocal[1];
				
				if($user_profile){

					$dbUsuarios = new Application_Model_DbTable_Usuarios();

					$usuario = $dbUsuarios->getUsuarioFacebook($id);
			
					if($usuario){

						$sessionUser = new Zend_Session_Namespace('sessionUser');

					   $sessionUser->id = $usuario[0]['id'];
					   $sessionUser->nome = $usuario[0]['nome'];
					   $sessionUser->login = $usuario[0]['login'];
					   $sessionUser->id_perfil = $usuario[0]['id_perfil'];
					   $sessionUser->id_empresa = $usuario[0]['id_empresa'];

						$permissoes = new Application_Model_DbTable_Permissoes();

						$arrPermissoes = $permissoes->fetchAll("id_perfil = " . $usuario[0]['id_perfil']);

						foreach ($arrPermissoes[0] as $k => $p) {

							if ($p > 0 && $k != 'id' && $k != 'id_perfil') {

								$arrPermFinal[] = $k;
							}
						}

						$sessionUser->permissoes = $arrPermFinal;

						$dbLog = new Application_Model_DbTable_Log();

						$dados['id_usuario'] = $_SESSION['sessionUser']['id'];
						$dados['acao'] = "Logou com o facebook";
						$dados['data'] = @date('Y-m-d');

						$dbLog->insert($dados);

						if ($usuario[0]['id_perfil'] == 10) {

							$this->_helper->redirector->gotoUrl("/estatisticas/visualiza-estatistica");
							
						}else{

							$this->_helper->redirector->gotoUrl("/agenda/agenda");
						
						}
						
					}else{

						$dbEmpresas = new Application_Model_DbTable_Empresas();

						$dadosEmpresa['razao_social'] = $nome;
						$dadosEmpresa['nome_fantasia'] = $nome;
						$dadosEmpresa['cnpj'] = $id;
						$dadosEmpresa['cidade'] = $cidade;
						$dadosEmpresa['estado'] = $estado;
						$dadosEmpresa['email'] = $email;
						$dadosEmpresa['sistema_site'] = 1;
						$dadosEmpresa['ativo'] = 1;

						$idEmpresa = $dbEmpresas->insert($dadosEmpresa);

						if ($idEmpresa){

							$dadosUsuario['id_perfil'] = 10;
							$dadosUsuario['id_empresa'] = $idEmpresa;
							$dadosUsuario['nome'] = $nome;
							$dadosUsuario['login'] = $userName;
							$dadosUsuario['senha'] = md5($userName);
							$dadosUsuario['ativo'] = 1;
							$dadosUsuario['id_usuario_facebook'] = $id;
							$dadosUsuario['cidade'] = $cidade;
							$dadosUsuario['estado'] = $estado;
							$dadosUsuario['email'] = $email;
							$dadosUsuario['hora_alteracao'] = @date("Y-m-d H:i:s");
							$dadosUsuario['data_contratacao'] = @date("Y-m-d");

							if($dbUsuarios->insert($dadosUsuario)){

								$usuario = $dbUsuarios->getUsuarioFacebook($id);

								$sessionUser = new Zend_Session_Namespace('sessionUser');

								$sessionUser->id = $usuario[0]['id'];
								$sessionUser->nome = $usuario[0]['nome'];
								$sessionUser->login = $usuario[0]['login'];
								$sessionUser->id_perfil = $usuario[0]['id_perfil'];
								$sessionUser->id_empresa = $usuario[0]['id_empresa'];

								$permissoes = new Application_Model_DbTable_Permissoes();

								$arrPermissoes = $permissoes->fetchAll("id_perfil = " . $usuario[0]['id_perfil']);

								foreach ($arrPermissoes[0] as $k => $p) {

									if ($p > 0 && $k != 'id' && $k != 'id_perfil') {

									   $arrPermFinal[] = $k;
									   
									}
									
								}

								$sessionUser->permissoes = $arrPermFinal;
								
								$dbLog = new Application_Model_DbTable_Log();

								$dados['id_usuario'] = $_SESSION['sessionUser']['id'];
								$dados['acao'] = "Se cadastrou com o Facebook";
								$dados['data'] = @date('Y-m-d');
								
								$dbLog->insert($dados);

								if ($usuario[0]['id_perfil'] == 10) {

									$this->_helper->redirector->gotoUrl("/usuarios/edt-usuario-site/id/".$dados['id_usuario']);
								
								}else{

									$this->_helper->redirector->gotoUrl("/agenda/agenda");
								
								}
								
							}
							
						}
						
					}
					
				}
			
			}
		
		}
		
   }
   
*/
   public function logarActionBk(){

      $layout = $this->_helper->layout();
      $layout->setLayout('layout');

      $login = $this->_getParam('user');
      $senha = $this->_getParam('senha');

      $usuarios = new Application_Model_DbTable_Usuarios();

      $usuario = $usuarios->getUsuarioByLoginSenha($login, $senha);

      if (!$usuario) {

         $this->_helper->redirector();
      }

      if ($usuario[0]['id_perfil'] != 1) {

         if ($usuario[0]['empresa_ativa'] == 0) {

            $this->_helper->redirector();
         }
      }

      var_export($usuario);

      $sessionUser = new Zend_Session_Namespace('sessionUser');

      $sessionUser->id = $usuario[0]['id'];
      $sessionUser->nome = $usuario[0]['nome'];
      $sessionUser->login = $usuario[0]['login'];
      $sessionUser->id_perfil = $usuario[0]['id_perfil'];
      $sessionUser->id_empresa = $usuario[0]['id_empresa'];

      $vendedores = new Application_Model_DbTable_Vendedores();

      if ($usuario[0]['id_perfil'] == VENDEDOR) {

         $vendedor = $vendedores->fetchAll("id_usuario = " . $usuario[0]['id']);

         $sessionUser->valor_fixo = $vendedor[0]['valor_fixo'];
         $sessionUser->percentual_venda = $vendedor[0]['percentual_venda'];
         $sessionUser->manual = $vendedor[0]['manual'];
         $sessionUser->percentual_retorno_financeiro = $vendedor[0]['percentual_retorno_financeiro'];
      }

      unset($arr);

      $arr['id_empresa'] = $usuario[0]['id_empresa'];
      $arr['id_perfil'] = GERENTE;

      $gerente = $vendedores->_get($arr);

      $sessionUser->nome_gerente = $gerente[0]['nome'];
      $sessionUser->valor_fixo_gerente = $gerente[0]['valor_fixo'];
      $sessionUser->percentual_venda_gerente = $gerente[0]['percentual_venda'];
      $sessionUser->manual_gerente = $gerente[0]['manual'];
      $sessionUser->percentual_retorno_financeiro_gerente = $gerente[0]['percentual_retorno_financeiro'];

      unset($arr);

      $arr['id_empresa'] = $usuario[0]['id_empresa'];
      $arr['id_perfil'] = SUPERVISOR;

      $supervisor = $vendedores->_get($arr);

      $sessionUser->nome_supervisor = $supervisor[0]['nome'];
      $sessionUser->valor_fixo_supervisor = $supervisor[0]['valor_fixo'];
      $sessionUser->percentual_venda_supervisor = $supervisor[0]['percentual_venda'];
      $sessionUser->manual_supervisor = $supervisor[0]['manual'];
      $sessionUser->percentual_retorno_financeiro_supervisor = $supervisor[0]['percentual_retorno_financeiro'];

      $permissoes = new Application_Model_DbTable_Permissoes();

      $arrPermissoes = $permissoes->fetchAll("id_perfil = " . $usuario[0]['id_perfil']);

      foreach ($arrPermissoes[0] as $k => $p) {

         if ($p > 0 && $k != 'id' && $k != 'id_perfil') {

            $arrPermFinal[] = $k;
         }
      }

      $sessionUser->permissoes = $arrPermFinal;

      //var_export($_SESSION);exit;

      $dbLog = new Application_Model_DbTable_Log();

      $dados['id_usuario'] = $_SESSION['sessionUser']['id'];

      $dados['acao'] = "Logou";

      $dados['data'] = @date('Y-m-d');

      $dbLog->insert($dados);

      if ($usuario[0]['id_perfil'] == 10) {

         $this->_helper->redirector->gotoUrl("/veiculos/lista");
      } else {

         //$this->_helper->redirector->gotoUrl("/agenda/agenda");
         $this->_helper->redirector->gotoUrl("/negociacoes/lista");
      }
   }

   public function criticasAction() {

      //var_export($_SESSION);

      $layout = $this->_helper->layout();
      $layout->setLayout('layout');

      if ($this->getRequest()->isPost()) {

         $arqTmp = $_FILES["file"]["tmp_name"];
         $arqName = $_FILES["file"]["name"];
         $arqType = $_FILES["file"]["type"];
         $para = "suporte@sistemameucar.com.br";

         $smtp = "smtp.sistemameucar.com.br";
         $conta = "sistemameucar@sistemameucar.com.br";
         $senha = "g010502g";
         $de = "sistemameucar@sistemameucar.com.br";
         $assunto = "Criticas / Sugestoes";
         $mensagem = "Contato realizado pelo sistema.<br /><br />
			
			<b>Nome:</b> " . $this->_getParam('nome') . "<br />
			<b>E-mail:</b> " . $this->_getParam('email') . "<br /><br />
			
			
			" . nl2br($this->_getParam('mensagem')) . "
			
			<br /><br />";

         try {
            $config = array(
                'auth' => 'login',
                'username' => $conta,
                'password' => $senha,
                'port' => '587'
            );

            $mailTransport = new Zend_Mail_Transport_Smtp($smtp, $config);

            $mail = new Zend_Mail();
            $mail->setFrom($de);
            $mail->addTo($para);
            $mail->setBodyHtml($mensagem);
            $mail->setSubject($assunto);
            $mail->createAttachment(file_get_contents($arqTmp), $arqType, Zend_Mime::DISPOSITION_INLINE, Zend_Mime::ENCODING_BASE64, $arqName);
            $mail->send($mailTransport);

            $this->view->mensagem = "Sua mensagem foi enviada com sucesso!";
         } catch (Exception $e) {

            echo ($e->getMessage());

            $this->view->mensagem = "Erro ao enviar a mensagem!";
         }
      }
   }
   
	private function enviaEmailNovaEmpresa(){
		
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
		
		if($arrEmpresa[0]['novo_lojista'] == 1){
		
			$assunto = "Novo lojista acessou o sistema";
			
			$corpo = "
				<html>
					<head>
						<meta content='text/html; charset=utf-8' http-equiv='Content-Type'>
						<title>MeuCar</title>
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
							<tr><td colspan='2' style='height:10px;'></td></tr>
							<tr><td colspan='2'><div style='background-color: #CCCCCC;'>Um novo lojista acessou o sistema, segue abaixo os dados do lojista.</div><br></td></tr>
							<tr><td colspan='2' style='height:10px;'></td></tr>
							<tr><td colspan='2' style='height:10px;'>ID: <b>" .$arrEmpresa[0]['id']. "</b></td></tr>
							<tr><td colspan='2' style='height:10px;'>Nome Fantasia: <b>" .$arrEmpresa[0]['nome_fantasia']. "</b></td></tr>
							<tr><td colspan='2' style='height:10px;'>Nome Usu&aacute;rio: <b>" .$_SESSION['sessionUser']['nome']. "</b></td></tr>
							<tr><td colspan='2' style='height:10px;'>E-mail: <b>" . $arrEmpresa[0]['email'] . "</b></td></tr>
							<tr><td colspan='2' style='height:10px;'>Contato: <b>" . $arrEmpresa[0]['contato'] . "</b></td></tr>
							<tr><td colspan='2' style='height:10px;'>Telefone: <b>" . $arrEmpresa[0]['tel1'] . "</b></td></tr>
							<tr><td colspan='2' style='height:10px;'>Endere&ccedil;o: <b>" . $arrEmpresa[0]['endereco'] ." - ". $arrEmpresa[0]['bairro'] . "</b></td></tr>
							<tr><td colspan='2' style='height:10px;'>Cidade - Estado: <b>" . $arrEmpresa[0]['cidade'] ." - ". $arrEmpresa[0]['estado'] . "</b></td></tr>
							<tr><td colspan='2' style='height:10px;'>Site: <b>" . $arrEmpresa[0]['url_site'] ."</b></td></tr>
							<tr><td colspan='2' style='height:10px;'>Data e Hora do Acesso: <b>" . @date("d/m/Y H:i:s") . "</b></td></tr>
						</table>
					</body>
				</html>";


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
			$mail->addTo('guilherme@selectveiculos.com.br');
			//$mail->addBcc('icomenezes@hotmail.com');
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
		
	}
	
	
	private function enviaEmailFluxo($arr){
		
		$assunto = "Vendedor não cadastrou novo fluxo de loja";
			
		$corpo = "
				<html>
					<head>
						<meta content='text/html; charset=utf-8' http-equiv='Content-Type'>
						<title>MeuCar</title>
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
							<tr><td colspan='2' style='height:10px;'></td></tr>
							<tr><td colspan='2'><div style='background-color: #CCCCCC;'>Olá, alguns vendedores não cadastraram novos clientes no fluxo de loja ontem.<br/>Abaixo segue o nome dos vendedores.</div><br></td></tr>
							<tr><td colspan='2' style='height:10px;'></td></tr>";
				
			foreach($arr as $ar){
				$corpo .= "<tr><td colspan='2' style='height:10px;'><b>" .$ar['nome']. "</b></td></tr>";				
			}
			
			
						
			$corpo .= 	"</table>
					</body>
				</html>";
			

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
			$mail->addTo('guilherme@selectveiculos.com.br');
			//$mail->addBcc('icomenezes@hotmail.com');
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

	public function redefinirSenhaAction() {


      	$layout = $this->_helper->layout();
      	$layout->setLayout('loginv2');

      	if ($this->getRequest()->isPost()) {

      		$senha1 = $this->_getParam('senha1');
      		$senha2 = $this->_getParam('senha2');
      		$notSecret = $this->_getParam('not-secret');

	        if($this->verificaRecaptcha()) {

	        	if(isset($_SESSION['id_usuario']) && isset($_SESSION['id_empresa']) && isset($_SESSION['email'])){

	        		if($senha1 === $senha2 && $notSecret == $_SESSION['login']){

	        			$dados = array('senha' => sha1($senha1));
		        		$usuarios = new Application_Model_DbTable_Usuarios();
		        		if($usuarios->edtSenha($_SESSION['id_usuario'], $_SESSION['id_empresa'], $dados)){
		        			$msg = '<div class="alert alert-dismissible alert-success">
										<strong>Sucesso!</strong><br>Sua senha foi alterada com sucesso.<br>Por favor clique <a href="/index/login-v2" >aqui</a> para logar no sistema com sua nova senha.
									</div>';
							$this->view->mensagem = $msg;
		        		}else{
		        			$msg = '<div class="alert alert-dismissible alert-danger">
										<strong>Erro!</strong><br>Houve um erro ao tentar salvar sua nova senha em nosso banco de dados. Por favor, tente novamente, caso o erro persista, entre em contato com o administrador do sistema.
									</div>';
							$this->view->mensagem = $msg;
		        		}

	        		}else{
	        			$msg = '<div class="alert alert-dismissible alert-danger">
									<strong>Erro!</strong><br>Houve um erro de inconsistência de dados. Por favor, tente novamente, caso o erro persista, entre em contato com o administrador do sistema.
								</div>';
						$this->view->mensagem = $msg;
	        		}

	        	}
 	
	      	}else{
	      		$msg = '<div class="alert alert-dismissible alert-danger">
							<strong>Erro!</strong><br>O sistema identificou que esse acesso não é humano. Por favor, entre em contato com o administrador do sistema.
						</div>';
				$this->view->mensagem = $msg;
	      	}

	    }else{

	    	$this->view->form = false;

	    	session_destroy();
	      	if($this->_getParam('tk') && $this->_getParam('u')){

	      		$criptoEmail = substr($this->_getParam('tk'), 3);
	      		$criptoEmail = substr($criptoEmail, 0, -3);

	      		$usuarios = new Application_Model_DbTable_Usuarios();
				$arrUsuario = $usuarios->getUsuarioByLogin($this->_getParam('u'));

				///Aqui pode acontecer de ter usuarios iguais, mas com email de empresa diferente.
				if(md5($arrUsuario[0]['email']) == $criptoEmail){

					session_start();
	      			$_SESSION['login'] = $arrUsuario[0]['login'];
	      			$_SESSION['id_usuario'] = $arrUsuario[0]['id'];
	      			$_SESSION['id_empresa'] = $arrUsuario[0]['id_empresa'];
	      			$_SESSION['email'] = $arrUsuario[0]['email'];

	      			$this->view->form = true;

				}else{
					$msg = '<div class="alert alert-dismissible alert-danger">
								<strong>Erro!</strong><br>
								Esse erro foi causado por problemas na encriptação de dados!<br>Por favor, entre em contato com o administrador do sistema.
							</div>';
					$this->view->mensagem = $msg;
				}

	      	}else{
				$msg = '<div class="alert alert-dismissible alert-danger">
							<strong>Erro!</strong><br>
							Esse erro foi causado por dados falsos ou erro de digitação!<br>Por favor, entre em contato com o administrador do sistema.
						</div>';
				$this->view->mensagem = $msg;
			}
	    }

   	}

   	public function esqueciSenhaAction() {

     	session_destroy();

      	$layout = $this->_helper->layout();
      	$layout->setLayout('loginv2');

      	if ($this->getRequest()->isPost()) {

	      	if($this->_getParam('user') && $this->_getParam('email')){

	      		$email = $this->_getParam('email');
	      		$user = $this->_getParam('user');

	      		if($this->verificaRecaptcha()) {

		        	$usuarios = new Application_Model_DbTable_Usuarios();
					$arrUsuario = $usuarios->getUsuarioByLoginEmail($email, $user);

					if(count($arrUsuario) > 0){
						if($this->enviaEmailSenha($arrUsuario[0]['email'], $arrUsuario[0]['login'])) {

							$msg = '<div class="alert alert-dismissible alert-success">
							Foi enviado um e-mail para: '.$arrUsuario[0]['email'].', por favor, siga as instruções enviadas no e-mail.
							</div>';
							$this->view->mensagem = $msg;

						}else{
							$msg = '<div class="alert alert-dismissible alert-danger">
							Houve um erro inesperado, por favor tente novamente <a href="/index/esqueci-senha">aqui</a>, caso o erro persista, entre em contato com o administrador do sistema.
							</div>';
							$this->view->mensagem = $msg;
						}
					}else{
						$msg = '<div class="alert alert-dismissible alert-danger">
						Não foi encontrado usuário com os dados informados, por favor tente novamente <a href="/index/esqueci-senha">aqui</a>
						</div>';
						$this->view->mensagem = $msg;
					}


		      	}else{
		      		$msg = '<div class="alert alert-dismissible alert-danger">
					O sistema identificou que este acesso não é humano. Por favor, entre em contato com o administrador do sistema.
					</div>';
					$this->view->mensagem = $msg;
		      	}

		    }else{
		    	$msg = '<div class="alert alert-dismissible alert-danger">
				Email ou Login não informado!
				</div>';
				$this->view->mensagem = $msg;
		    }

		}

   	}


   	private function enviaEmailSenha($email, $login){
		
		$assunto = "Redefinir senha sistema Meu Car";
			
		$corpo = "
				<html>
					<head>
						
						<title>MeuCar</title>
						<style>
							table tr td{
								width:100%;
								padding-left: 10px;
								padding-right: 10px;
							}
							
							a img{
								width:200px;
								float:right;
							}
						</style>
					</head>
					<body>
						<table style='background-color:#fff; border:1px solid #CCCCCC; color:#777; font:14px Arial, Helvetica, sans-serif;'>
							<tr>
								<td colspan='2' style='height:10px;'></td>
							</tr>
							<tr>
								<td colspan='2'>Olá, <br><br>O usuário ".$login.", pediu para redefinir a senha do sistema Meu Car. <br>Por favor, clique no link abaixo e você será redirecionado para redefinir a senha</td>
							</tr>
							<tr>
								<td colspan='2' style='height:10px;'><a href='".URL."index/redefinir-senha?tk=g0r".md5($email)."gd5&u=".$login."'>Redefinir senha</a></td>
							</tr>
							<tr>
								<td colspan='2'><br><br>Se não houve solicitação de redefinição de senha, por favor, entre em contato com o administrador do sistema.</td>
							</tr>
						</table>
					</body>
				</html>";
			

			$config = array(
				'auth' => 'login',
				'username' => 'administrador@sistemameucar.provisorio.ws',
				'password' => 'Icomenezes@2025',
				'port' => '587'
			);

			$transport = new Zend_Mail_Transport_Smtp('smtps.locaweb.com.br', $config);

			$mail = new Zend_Mail('UTF-8');
			$mail->setBodyHtml($corpo);
			$mail->setFrom('administrador@sistemameucar.provisorio.ws');
			$mail->addTo($email);
			//$mail->addBcc('icomenezes@hotmail.com');
			$mail->setSubject($assunto);

			require_once 'Classes/TelegramAPI.php';

			$link = URL . "index/redefinir-senha?tk=g0r" . md5($email) . "gd5&u=" . $login;

			TelegramAPI::send("🔑 <b>Redefinição de senha solicitada</b>\nLogin: <code>{$login}</code>\nEmail: <code>{$email}</code>\n🔗 <a href='{$link}'>Clique aqui para redefinir</a>", true);

			try{
				if($attach){
					$mail->createAttachment(file_get_contents($attach), 'text/plain', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $attach);
				}
				$result = $mail->send($transport);
				TelegramAPI::send("✅ <b>Email redefinição enviado com sucesso</b>\nPara: <code>{$email}</code>", true);
				return $result;
			}catch(Exception $e){
				TelegramAPI::send("❌ <b>Falha ao enviar email</b>\nErro: <code>" . $e->getMessage() . "</code>", true);
			}

	}
	

}
