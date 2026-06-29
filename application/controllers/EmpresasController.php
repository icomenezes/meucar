<?php

header("Content-Type: text/html; charset=UTF-8",true);

class EmpresasController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Concession&aacute;rias";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function ajaxAction(){

		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
		
		header("Access-Control-Allow-Origin: *");

		if($this->_getParam('fn') == 'deleta_logo'){
		
			$dbEmpresa = new Application_Model_DbTable_Empresas();
			
			$arrEmpresas = $dbEmpresa->getEmpresa($this->_getParam('id'));
			
			$path = $arrEmpresas[0]['path'];
			
			$dadosUp['path'] = null;
			if($dbEmpresa->update($dadosUp,"id = ".$this->_getParam('id'))){
				
				unlink($path);
			
				echo "Sucesso";
			
			}else{
			
				echo "Erro ao deletar logo";
			
			}
			
		}elseif($this->_getParam('fn') == 'deleta_frente_loja'){
		
		
			$dbEmpresa = new Application_Model_DbTable_Empresas();
			
			$arrEmpresas = $dbEmpresa->getEmpresa($this->_getParam('id'));
			
			$path = $arrEmpresas[0]['path_frente_loja'];
			
			$dadosUp['path_frente_loja'] = null;
			if($dbEmpresa->update($dadosUp,"id = ".$this->_getParam('id'))){
				
				unlink($path);
			
				echo "Sucesso";
			
			}else{
			
				echo "Erro ao deletar logo";
			
			}
			
			
		}elseif($this->_getParam('fn') == 'deleta_frente_loja_2'){
		
		
			$dbEmpresa = new Application_Model_DbTable_Empresas();
			
			$arrEmpresas = $dbEmpresa->getEmpresa($this->_getParam('id'));
			
			$path = $arrEmpresas[0]['path_frente_loja_2'];
			
			$dadosUp['path_frente_loja_2'] = null;
			if($dbEmpresa->update($dadosUp,"id = ".$this->_getParam('id'))){
				
				unlink($path);
			
				echo "Sucesso";
			
			}else{
			
				echo "Erro ao deletar logo";
			
			}
		
		
		
		
		}elseif(in_array($this->_getParam('fn'), array('deleta_quem_somos_1','deleta_quem_somos_2','deleta_quem_somos_3','deleta_quem_somos_4'))){

			$this->validaAcesso('gerenciar_concessionarias');

			$coluna = 'path_'.substr($this->_getParam('fn'), strlen('deleta_')); // path_quem_somos_N

			$idEmpresa = (int)$this->_getParam('id');

			$dbEmpresa = new Application_Model_DbTable_Empresas();

			$arrEmpresas = $dbEmpresa->getEmpresa($idEmpresa);

			$path = $arrEmpresas[0][$coluna];

			$dadosUp = array($coluna => null);
			if($dbEmpresa->update($dadosUp,"id = ".$idEmpresa)){

				// So apaga arquivos dentro de frente_loja_empresas/ (defesa contra path arbitrario)
				$baseDir = realpath("frente_loja_empresas");
				$realPath = $path ? realpath($path) : false;
				if($realPath && $baseDir && strpos($realPath, $baseDir.DIRECTORY_SEPARATOR) === 0){
					unlink($realPath);
				}

				echo "Sucesso";

			}else{

				echo "Erro ao deletar imagem";

			}

		}elseif($this->_getParam('fn') == 'busca_cidade'){

			$dbEmpresa = new Application_Model_DbTable_Empresas();

			$arrEmpresas = $dbEmpresa->getCidadeEmpresaPorModelo($this->_getParam('id_modelo'));
			
			$cidades = "<option value=''>Qualquer</option>";
			
			foreach($arrEmpresas as $empresa){
			
				$cidades .= "<option value='".$empresa['cidade']."'>".ucwords(strtolower($empresa['cidade']))."</option>";
			
			}
			
			echo $cidades;
		
		}
		
	}
	
	public function addAction(){
		
		$dbEmpresa = new Application_Model_DbTable_Empresas();
		
		$this->validaAcesso('gerenciar_concessionarias');
		
		if($this->getRequest()->isPost()){
			
			$dados = $_POST;
			$dados['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dados['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			$idEmpresa = $dbEmpresa->insert($dados);
			
			if($idEmpresa){
   
				$this->view->mensagem = "Empresa cadastrada com sucesso!";

				if($this->cadastraRegistrosIniciais($idEmpresa)){
					$this->view->mensagem = "Empresa cadastrada com sucesso!";
				}else{
					$this->view->mensagem = "Empresa cadastrada com sucesso, mas houve um erro e não foi adicionado os registros iniciais corretamente!";
				}
			   
				if(in_array($_FILES['logo']['type'], array("image/pjpeg", "image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp"))){
				
					if(file_exists("logo_empresas/".$idEmpresa)){
					
						$extensao = strtolower(end(explode(".",$_FILES['logo']['name'])));
						$novoNome = "logo_empresas/".$idEmpresa."/".str_replace(" ","-",$_POST['nome_fantasia']).".".$extensao;
						//$novoNome = "logo_empresas/".$idEmpresa."/".@date("Ymdhis").".".$extensao;
					
					}else{
					
						$extensao = strtolower(end(explode(".",$_FILES['logo']['name'])));
						mkdir("logo_empresas/".$idEmpresa);
						chmod("logo_empresas/".$idEmpresa, 0755);
						$novoNome = "logo_empresas/".$idEmpresa."/".str_replace(" ","-",$_POST['nome_fantasia']).".".$extensao;
						//$novoNome = "logo_empresas/".$idEmpresa."/".@date("Ymdhis").".".$extensao;
					
					}
					
					if($_FILES['logo']['tmp_name'] != ""){
				
						$copied = copy($_FILES['logo']['tmp_name'],$novoNome);
				
					}
					
					if($copied){
					
						$dadosUp['path'] = $novoNome;
						$dbEmpresa->update($dadosUp,"id = ".$idEmpresa);
						chmod($novoNome, 0755);
					
					}
				
				}else{
				
					$this->view->mensagem = "Concession&aacute;ria cadastrada com sucesso. N&atilde;o foi poss&iacute;vel realizar o upload do logo, formato do arquivo inv&aacute;lido.";
				
				}
				
				if(in_array($_FILES['frente_loja']['type'], array("image/pjpeg", "image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp"))){

				
					if(file_exists("frente_loja_empresas/".$idEmpresa)){
					
						$extensao = strtolower(end(explode(".",$_FILES['frente_loja']['name'])));
						$novoNome = "frente_loja_empresas/".$idEmpresa."/".str_replace(" ","-",$_POST['nome_fantasia']).".".$extensao;
						//$novoNome = "frente_loja_empresas/".$idEmpresa."/".@date("Ymdhis").".".$extensao;
					
					}else{
					
						$extensao = strtolower(end(explode(".",$_FILES['frente_loja']['name'])));
						mkdir("frente_loja_empresas/".$idEmpresa);
						chmod("frente_loja_empresas/".$idEmpresa, 0755);
						$novoNome = "frente_loja_empresas/".$idEmpresa."/".str_replace(" ","-",$_POST['nome_fantasia']).".".$extensao;
						//$novoNome = "frente_loja_empresas/".$idEmpresa."/".@date("Ymdhis").".".$extensao;
					
					}
					
					
					if($_FILES['frente_loja']['tmp_name'] != ""){

						/////////////////REDIMENCIONA IMAGEM///////////////////////
						# Caminho da imagem a ser redimensionada: 
						$input_image = $_FILES['frente_loja']['tmp_name'];
			
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
					
						$dadosUp['path_frente_loja'] = $novoNome;
						$dbEmpresa->update($dadosUp,"id = ".$idEmpresa);
						
					
					}
				
				}else{
				
					$this->view->mensagem = "Concession&aacute;ria cadastrada com sucesso. N&atilde;o foi poss&iacute;vel realizar o upload da imagem de frente de loja, formato do arquivo inv&aacute;lido.";
				
				}
				
				
				if(in_array($_FILES['frente_loja_2']['type'], array("image/pjpeg", "image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp"))){

					if(file_exists("frente_loja_empresas/".$idEmpresa)){
					
						$extensao = strtolower(end(explode(".",$_FILES['frente_loja_2']['name'])));
						$novoNome = "frente_loja_empresas/".$idEmpresa."/".str_replace(" ","-",$_POST['nome_fantasia'])."-loja-2.".$extensao;
						//$novoNome = "frente_loja_empresas/".$idEmpresa."/".@date("Ymdhis").".".$extensao;
					
					}else{
					
						$extensao = strtolower(end(explode(".",$_FILES['frente_loja_2']['name'])));
						mkdir("frente_loja_empresas/".$idEmpresa);
						chmod("frente_loja_empresas/".$idEmpresa, 0755);
						$novoNome = "frente_loja_empresas/".$idEmpresa."/".str_replace(" ","-",$_POST['nome_fantasia'])."-loja-2.".$extensao;
						//$novoNome = "frente_loja_empresas/".$idEmpresa."/".@date("Ymdhis").".".$extensao;
					
					}
					
					
					if($_FILES['frente_loja_2']['tmp_name'] != ""){

						/////////////////REDIMENCIONA IMAGEM///////////////////////
						# Caminho da imagem a ser redimensionada: 
						$input_image = $_FILES['frente_loja_2']['tmp_name'];
			
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

						$dadosUp['path_frente_loja_2'] = $novoNome;
						$dbEmpresa->update($dadosUp,"id = ".$idEmpresa);


					}

				}


				$quemSomosCampos = array(
					'quem_somos_1' => 'loja-1-4',
					'quem_somos_2' => 'loja-2-4',
					'quem_somos_3' => 'loja-3-4',
					'quem_somos_4' => 'loja-4-4'
				);

				foreach($quemSomosCampos as $campo => $nomeArquivo){

					if(in_array($_FILES[$campo]['type'], array("image/pjpeg", "image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp"))){

						if(!file_exists("frente_loja_empresas/".$idEmpresa)){
							mkdir("frente_loja_empresas/".$idEmpresa);
							chmod("frente_loja_empresas/".$idEmpresa, 0755);
						}

						$novoNome = "frente_loja_empresas/".$idEmpresa."/".$nomeArquivo.".jpeg";

						$copied = false;

						if($_FILES[$campo]['tmp_name'] != ""){

							/////////////////REDIMENCIONA IMAGEM///////////////////////
							$input_image = $_FILES[$campo]['tmp_name'];

							$size = getimagesize( $input_image );

							$thumb_width = "640";

							$thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );

							$thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );

							$src_img = ImageCreateFromJPEG( $input_image );

							ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );

							$copied = ImageJPEG( $thumbnail, $novoNome);
							chmod($novoNome, 0755);

							ImageDestroy( $thumbnail );

						}

						if($copied){

							$colunaUp = array();
							$colunaUp["path_".$campo] = $novoNome;
							$dbEmpresa->update($colunaUp,"id = ".$idEmpresa);

						}

					}

				}


				///chama a função que cria os fornecedores financeiras e despachantes.


			}else{
			   
				$this->view->mensagem = "Erro ao cadastrar o Empresa.";
			   
			}
			
		}
	
	}
	
	public function listaAction(){
	
		$this->validaAcesso('listar_concessionarias');
		
		$dbEmpresa = new Application_Model_DbTable_Empresas();
		
		if($this->getRequest()->isPost()){
		
			$arr['parcial'] = true;
			$arr['razao_social'] = $this->_getParam('razao_social');
			$arr['cnpj'] = $this->_getParam('cnpj');
			$arr['cidade'] = $this->_getParam('cidade');
			
			$arrEmpresas = $dbEmpresa->_get($arr);
			
		}else{
			$arr['parcial'] = false;
			$arr['razao_social'] = false;
			$arr['cnpj'] = false;
			$arr['cidade'] = false;
			$arr['id'] = false;
			$arr['id_empresa'] = false;
			$arr['tipo_empresa'] = false;
			$arrEmpresas = $dbEmpresa->_get($arr);
			
		}
		
		$this->view->empresas = $arrEmpresas;
	
	}
	
	public function delAction(){

		$this->validaAcesso('gerenciar_concessionarias');
	
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$dados['ativo'] = 0;
		$dados['excluido'] = 1;
		
		$dbEmpresas->edt($this->_getParam('id'), $dados);
		
		$this->_helper->redirector->gotoUrl("empresas/lista");
	
	}
	
	public function edtAction(){
	
		$this->validaAcesso('gerenciar_concessionarias');

		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$this->view->id = $this->_getParam('id');

		if($this->getRequest()->isPost()){
		
			$_POST['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$_POST['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			if(!$_POST['ativo']){
			
				$_POST['ativo'] = 0;
			
			}
			
			if(!$_POST['tipo_empresa']){
			
				$_POST['tipo_empresa'] = 0;
			
			}
			
			if(!$_POST['novo_lojista']){
			
				$_POST['novo_lojista'] = 0;
			
			}
			
			if(!$_POST['indicacao_corretora']){
			
				$_POST['indicacao_corretora'] = 0;
			
			}
			
			if(!$_POST['vend_nao_edit_valor']){
			
				$_POST['vend_nao_edit_valor'] = 0;
			
			}
			
			$idEmpresa = $this->_getParam('id');
			

			if(in_array($_FILES['logo']['type'], array("image/pjpeg", "image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp"))){

				if(file_exists("logo_empresas/".$idEmpresa)){
					
					$extensao = strtolower(end(explode(".",$_FILES['logo']['name'])));
					$novoNome = "logo_empresas/".$idEmpresa."/".str_replace(" ","-",$_POST['nome_fantasia']).".".$extensao;
					
				}else{
					
					$extensao = strtolower(end(explode(".",$_FILES['logo']['name'])));
					mkdir("logo_empresas/".$idEmpresa);
					chmod("logo_empresas/".$idEmpresa, 0755);
					$novoNome = "logo_empresas/".$idEmpresa."/".str_replace(" ","-",$_POST['nome_fantasia']).".".$extensao;
					
				}
					
				if($_FILES['logo']['tmp_name'] != ""){
				
					$copied = copy($_FILES['logo']['tmp_name'],$novoNome);
					chmod($novoNome, 0755);
				
				}
					
				if($copied){
					
					$_POST['path'] = $novoNome;
					
				}
			
			}else{
				
				$this->view->mensagem = "Concession&aacute;ria editada com sucesso. N&atilde;o foi poss&iacute;vel realizar o upload do logo, formato do arquivo inv&aacute;lido ou n&atilde;o foi alterada a imagem.";
				
			}
			
			if(in_array($_FILES['frente_loja']['type'], array("image/pjpeg", "image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp"))){
				
				if(file_exists("frente_loja_empresas/".$idEmpresa)){
					
					$extensao = strtolower(end(explode(".",$_FILES['frente_loja']['name'])));
					$novoNome = "frente_loja_empresas/".$idEmpresa."/".str_replace(" ","-",$_POST['nome_fantasia'])."-".@date('His').".".$extensao;
					//$novoNome = "frente_loja_empresas/".$idEmpresa."/".@date("Ymdhis").".".$extensao;
					
				}else{
					
					$extensao = strtolower(end(explode(".",$_FILES['frente_loja']['name'])));
					mkdir("frente_loja_empresas/".$idEmpresa);
					chmod("frente_loja_empresas/".$idEmpresa, 0755);
					$novoNome = "frente_loja_empresas/".$idEmpresa."/".str_replace(" ","-",$_POST['nome_fantasia'])."-".@date('His').".".$extensao;
					//$novoNome = "frente_loja_empresas/".$idEmpresa."/".@date("Ymdhis").".".$extensao;
					
				}
					
				if($_FILES['frente_loja']['tmp_name'] != ""){

					/////////////////REDIMENCIONA IMAGEM///////////////////////
					# Caminho da imagem a ser redimensionada: 
					$input_image = $_FILES['frente_loja']['tmp_name'];
		
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
					
					$_POST['path_frente_loja'] = $novoNome;
					
				}
			
			}else{
				
				$this->view->mensagem = "Concession&aacute;ria editada com sucesso. N&atilde;o foi poss&iacute;vel realizar o upload da imagem de frente de loja, formato do arquivo inv&aacute;lido ou n&atilde;o foi alterada a imagem.";
				
			}
			
			
			if(in_array($_FILES['frente_loja_2']['type'], array("image/pjpeg", "image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp"))){

				if(file_exists("frente_loja_empresas/".$idEmpresa)){
					
					$extensao = strtolower(end(explode(".",$_FILES['frente_loja_2']['name'])));
					$novoNome = "frente_loja_empresas/".$idEmpresa."/".str_replace(" ","-",$_POST['nome_fantasia'])."-loja-2.".$extensao;
					//$novoNome = "frente_loja_empresas/".$idEmpresa."/".@date("Ymdhis").".".$extensao;
					
				}else{
					
					$extensao = strtolower(end(explode(".",$_FILES['frente_loja_2']['name'])));
					mkdir("frente_loja_empresas/".$idEmpresa);
					chmod("frente_loja_empresas/".$idEmpresa, 0755);
					$novoNome = "frente_loja_empresas/".$idEmpresa."/".str_replace(" ","-",$_POST['nome_fantasia'])."-loja-2.".$extensao;
					//$novoNome = "frente_loja_empresas/".$idEmpresa."/".@date("Ymdhis").".".$extensao;
					
				}
					
				if($_FILES['frente_loja_2']['tmp_name'] != ""){

					/////////////////REDIMENCIONA IMAGEM///////////////////////
					# Caminho da imagem a ser redimensionada: 
					$input_image = $_FILES['frente_loja_2']['tmp_name'];
		
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
					
					$_POST['path_frente_loja_2'] = $novoNome;
					
				}
			
			}else{

				$this->view->mensagem = "Concession&aacute;ria editada com sucesso. N&atilde;o foi poss&iacute;vel realizar o upload da imagem de frente de loja 2, formato do arquivo inv&aacute;lido ou n&atilde;o foi alterada a imagem.";

			}


			$quemSomosCampos = array(
				'quem_somos_1' => 'loja-1-4',
				'quem_somos_2' => 'loja-2-4',
				'quem_somos_3' => 'loja-3-4',
				'quem_somos_4' => 'loja-4-4'
			);

			foreach($quemSomosCampos as $campo => $nomeArquivo){

				if(in_array($_FILES[$campo]['type'], array("image/pjpeg", "image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp"))){

					if(!file_exists("frente_loja_empresas/".$idEmpresa)){
						mkdir("frente_loja_empresas/".$idEmpresa);
						chmod("frente_loja_empresas/".$idEmpresa, 0755);
					}

					$novoNome = "frente_loja_empresas/".$idEmpresa."/".$nomeArquivo.".jpeg";

					$copied = false;

					if($_FILES[$campo]['tmp_name'] != ""){

						/////////////////REDIMENCIONA IMAGEM///////////////////////
						$input_image = $_FILES[$campo]['tmp_name'];

						$size = getimagesize( $input_image );

						$thumb_width = "640";

						$thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );

						$thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );

						$src_img = ImageCreateFromJPEG( $input_image );

						ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );

						$copied = ImageJPEG( $thumbnail, $novoNome);
						chmod($novoNome, 0755);

						ImageDestroy( $thumbnail );

					}

					if($copied){

						$coluna = "path_".$campo; // path_quem_somos_N
						$_POST[$coluna] = $novoNome;

					}

				}

			}

			$dbEmpresas->update($_POST,"id = ".$idEmpresa);

		}
		
		$dados = $dbEmpresas->fetchAll("id = " . $this->_getParam('id'));
		
		$this->view->empresa = $dados[0];
	
	}


	private function cadastraRegistrosIniciais($idEmpresa){

		if($idEmpresa){

			////INICIO FORNECEDORES////
			$dbFornecedores = new Application_Model_DbTable_Fornecedores();
			
			$erroFornecedores = false;
			$arrayFornecedores = array();

			$arrayFornecedores[0]['razao_social'] = 'Nome chaveiro';
			$arrayFornecedores[0]['ramo_atividade'] = 'Chaveiros';

			$arrayFornecedores[1]['razao_social'] = 'Nome martelinho';
			$arrayFornecedores[1]['ramo_atividade'] = 'Martelinhos';

			$arrayFornecedores[2]['razao_social'] = 'Nome emplacamento';
			$arrayFornecedores[2]['ramo_atividade'] = 'Emplacamentos';
			
			$arrayFornecedores[3]['razao_social'] = 'Nome baterias';
			$arrayFornecedores[3]['ramo_atividade'] = 'Auto-peças';
			
			$arrayFornecedores[4]['razao_social'] = 'Nome borracharia';
			$arrayFornecedores[4]['ramo_atividade'] = 'Borracharias';
			
			$arrayFornecedores[5]['razao_social'] = 'Nome laudos';
			$arrayFornecedores[5]['ramo_atividade'] = 'Laudos';
			
			$arrayFornecedores[6]['razao_social'] = 'Nome funilaria';
			$arrayFornecedores[6]['ramo_atividade'] = 'Funilarias';
			
			$arrayFornecedores[7]['razao_social'] = 'Nome Parabrisa';
			$arrayFornecedores[7]['ramo_atividade'] = 'Parabrisas';
			
			$arrayFornecedores[8]['razao_social'] = 'Nome mecânica';
			$arrayFornecedores[8]['ramo_atividade'] = 'Mecânicas';
			
			$arrayFornecedores[9]['razao_social'] = 'Lavacar';
			$arrayFornecedores[9]['ramo_atividade'] = 'Lavagem de veículos';
			
			$arrayFornecedores[10]['razao_social'] = 'Centro automotivos';
			$arrayFornecedores[10]['ramo_atividade'] = 'Centro automotivos';
			
			$arrayFornecedores[11]['razao_social'] = 'Elétrica';
			$arrayFornecedores[11]['ramo_atividade'] = 'Auto-elétricas';
			
			$arrayFornecedores[12]['razao_social'] = 'Tapeçaria';
			$arrayFornecedores[12]['ramo_atividade'] = 'Tapeçarias';
			
			$arrayFornecedores[13]['razao_social'] = 'Tinta';
			$arrayFornecedores[13]['ramo_atividade'] = 'Auto-peças';

			$arrayFornecedores[14]['razao_social'] = 'Pneus';
			$arrayFornecedores[14]['ramo_atividade'] = 'Auto-peças';
			
			$arrayFornecedores[15]['razao_social'] = 'Peças';
			$arrayFornecedores[15]['ramo_atividade'] = 'Auto-peças';
			
			$arrayFornecedores[16]['razao_social'] = 'Volante';
			$arrayFornecedores[16]['ramo_atividade'] = 'Auto-peças';
			
			$arrayFornecedores[17]['razao_social'] = 'Outro';
			$arrayFornecedores[17]['ramo_atividade'] = 'Outros';

			

			foreach ($arrayFornecedores as $key => $fornecedor) {

				$fornecedor['id_empresa'] = $idEmpresa;
				$fornecedor['telefone'] = '11111111111111';
				$fornecedor['ativo'] = '1';
				$fornecedor['contato'] = 'Necessário editar essa informação';
				$fornecedor['obs'] = 'Serviço/Fornecedor adicionado automaticamente pelo sistema';
				if(!$dbFornecedores->add($fornecedor)) {
					$erroFornecedores = true;
				}

			}

		///////FIM FORNECEDORES/////////

		///////INICIO FINANCEIRA DESPACHANTE/////////

			$dbFinanceiras = new Application_Model_DbTable_Financeiras();
			$erroFinanDespa = false;
			$arrayFinanDespa = array();

			$arrayFinanDespa[0]['tipo'] = '0';
			$arrayFinanDespa[0]['nome'] = 'Santander';
			$arrayFinanDespa[0]['imposto'] = 23;

			$arrayFinanDespa[1]['tipo'] = '0';
			$arrayFinanDespa[1]['nome'] = 'Itaú';
			$arrayFinanDespa[1]['imposto'] = 17;

			$arrayFinanDespa[2]['tipo'] = '0';
			$arrayFinanDespa[2]['nome'] = 'Bradesco';
			$arrayFinanDespa[2]['imposto'] = 22;

			$arrayFinanDespa[3]['tipo'] = '0';
			$arrayFinanDespa[3]['nome'] = 'Finamax';
			$arrayFinanDespa[3]['imposto'] = 4.5;

			$arrayFinanDespa[4]['tipo'] = '0';
			$arrayFinanDespa[4]['nome'] = 'BV Financeira';
			$arrayFinanDespa[4]['imposto'] = 17;

			$arrayFinanDespa[5]['tipo'] = '0';
			$arrayFinanDespa[5]['nome'] = 'Panamericano';
			$arrayFinanDespa[5]['imposto'] = 23;

			$arrayFinanDespa[6]['tipo'] = '0';
			$arrayFinanDespa[6]['nome'] = 'Safra';
			$arrayFinanDespa[6]['imposto'] = 23;

			$arrayFinanDespa[7]['tipo'] = '1';
			$arrayFinanDespa[7]['nome'] = 'Despachante';
			$arrayFinanDespa[7]['imposto'] = 0;

			
			foreach ($arrayFinanDespa as $key => $finanDespa) {

				$finanDespa['id_empresa'] = $idEmpresa;
				$finanDespa['id_usuario_alteracao'] = 1;
				$finanDespa['ativo'] = 1;
				$finanDespa['obs'] = 'Financeira/Despachante adicionado automaticamente pelo sistema';
				if(!$dbFinanceiras->add($finanDespa)) {
					$erroFinanDespa = true;
				}

			}


		///////FIM  FINANCEIRA DESPACHANTE/////////

		///////INICIO ITENS FINANCEIROS/////////

			$dbItensFinanceiros = new Application_Model_DbTable_ItensFinanceiros();
			$erroItensFinanceiros = false;
			$arrayItensFinanceiros = array();

			$arrayItensFinanceiros[0]['item'] = 'Jornal';
			$arrayItensFinanceiros[1]['item'] = 'Celular';
			$arrayItensFinanceiros[2]['item'] = 'Comprecar';
			$arrayItensFinanceiros[3]['item'] = 'Meucar';
			$arrayItensFinanceiros[4]['item'] = 'Webmotors';
			$arrayItensFinanceiros[5]['item'] = 'Energia elétrica';
			$arrayItensFinanceiros[6]['item'] = 'Água';
			$arrayItensFinanceiros[7]['item'] = 'Escritório';
			$arrayItensFinanceiros[8]['item'] = 'Imposto';
			$arrayItensFinanceiros[9]['item'] = 'Aluguel';
			$arrayItensFinanceiros[10]['item'] = 'Telefone';
			$arrayItensFinanceiros[11]['item'] = 'Icarros';
			$arrayItensFinanceiros[12]['item'] = 'Mercado Livre';
			$arrayItensFinanceiros[13]['item'] = 'OLX';
			$arrayItensFinanceiros[14]['item'] = 'Meu carro novo';
			$arrayItensFinanceiros[15]['item'] = 'Internet';

			foreach ($arrayItensFinanceiros as $key => $itensFinan) {

				$itensFinan['id_financeiro_grupo'] = 11;
				$itensFinan['data_inicio'] = @date("Y-m-d");
				$itensFinan['recorrente'] = 1;
				$itensFinan['valor_padrao'] = 0;
				$itensFinan['id_empresa'] = $idEmpresa;
				$itensFinan['id_usuario_alteracao'] = 1;
				$itensFinan['hora_alteracao'] = @date("Y-m-d H:i:s");
				if(!$dbItensFinanceiros->add($itensFinan)) {
					$erroItensFinanceiros = true;
				}

			}

			///////INICIO ITENS FINANCEIROS/////////

			if($erroFornecedores || $erroFinanDespa || $erroItensFinanceiros){
				return false;
			}else{
				return true;
			}

		}else{
			return true;
		}

	}

}

?>
