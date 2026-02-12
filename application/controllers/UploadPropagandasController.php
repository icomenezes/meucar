<?php

header("Content-Type: text/html; charset=UTF-8",true);

class UploadPropagandasController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Propagandas";

		Zend_Session::start();
		
	}
	
	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}

	public function uploadSlideAction(){

		if($this->getRequest()->isPost()){
		
			$dbPropagandasSite = new Application_Model_DbTable_PropagandasSite();
			if(in_array($_FILES['imagem']['type'], array("image/pjpeg", "image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp"))){
			//if (eregi("^image\/(pjpeg|jpeg|jpg|png|gif|bmp)$", $_FILES['imagem']['type'])) {
				
				if(file_exists("propagandas_site/")){	
					$novoNome = "propagandas_site/".@date("his")."-".$_FILES['imagem']['name'];
				}else{
					mkdir("propagandas_site/");
					$novoNome = "propagandas_site/".@date("his")."-".$_FILES['imagem']['name'];
				}
					
				/*if($_FILES['imagem']['tmp_name'] != ""){
					$copied = copy($_FILES['imagem']['tmp_name'],$novoNome);
					chmod($novoNome, 0755);	
				}*/


				if($_FILES['imagem']['tmp_name'] != ""){

					/////////////////REDIMENCIONA IMAGEM///////////////////////
					# Caminho da imagem a ser redimensionada: 
					$input_image = $_FILES['imagem']['tmp_name'];
		
					// Pega o tamanho original da imagem e armazena em um Array:
					$size = getimagesize( $input_image );

					if($size[0] <= 1130 && $size[0] >= 1110 && $size[1] <= 290 && $size[1] >= 270){
			
						// Configura a nova largura da imagem:
						$thumb_width = 1120;
						$thumb_height = 280;

						// Cria a imagem com as cores reais originais na memória.
						$thumbnail = ImageCreateTrueColor($thumb_width, $thumb_height );

						// Criará uma nova imagem do arquivo.
						$src_img = ImageCreateFromJPEG($input_image );

						// Criará a imagem redimensionada:
						ImageCopyResampled($thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );

						// Informe aqui o novo nome da imagem e a localização:
						$copied = false;
						$copied = ImageJPEG( $thumbnail, $novoNome);
						chmod($novoNome, 0755);
						
						// Limpa da memoria a imagem criada temporáriamente: 
						ImageDestroy( $thumbnail );
		
						if($copied){
							
							$dadosUp['titulo'] = $_POST['titulo'];
							$dadosUp['path'] = $novoNome;
							$dadosUp['tipo_imagem'] = 2;
							$dadosUp['link'] = $_POST['link'];
							$dadosUp['data_upload'] = @date("Y-m-d");
							$dadosUp['tipo_propaganda'] = 1;
							$dadosUp['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
								
							if($dbPropagandasSite->insert($dadosUp)){
								$this->_helper->redirector->gotoUrl("upload-propagandas/lista-slides");
								$this->view->mensagem = "Imagem enviada com sucesso.";
							}else{
								$this->view->mensagem = "Erro ao enviar o imagem.";
							}
							
						}else{
							$this->view->mensagem = "Erro. N&atilde;o foi poss&iacute;vel realizar o upload da imagem.";
						}

					}else{
						$this->view->mensagem = "Erro. As dimensões da imagem, não está nas medidas obrigatória. (".$size[0]."x".$size[1].")";
					}

				}
				
			}
			
		}

	}

	public function delSlideAction(){
	
		$dbPropagandasSite = new Application_Model_DbTable_PropagandasSite();
		$arrImagem = $dbPropagandasSite->getImagem($this->_getParam('id'));
		
		if($dbPropagandasSite->delete("id = ".$this->_getParam('id'))){
		
			unlink($arrImagem[0]['path']);
		
			$this->_helper->redirector->gotoUrl("upload-propagandas/lista-slides");

		
		}else{
		
			$this->view->mensagem = "Erro. N&atilde;o foi poss&iacute;vel deletar a imagem. <a href='/upload-propagandas/lista-slides'>Voltar</a>";
		
		}
	
	}
	
	public function uploadAction(){
	
		
		if($this->getRequest()->isPost()){
		
			$dbPropagandasSite = new Application_Model_DbTable_PropagandasSite();
			
			if(in_array($_FILES['imagem']['type'], array("image/pjpeg", "image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp"))){
			//if (eregi("^image\/(pjpeg|jpeg|jpg|png|gif|bmp)$", $_FILES['imagem']['type'])) {
				
				if(file_exists("propagandas_site/")){
					
					$novoNome = "propagandas_site/".@date("his")."-".$_FILES['imagem']['name'];
					
				}else{
					
					mkdir("propagandas_site/");
					$novoNome = "propagandas_site/".@date("his")."-".$_FILES['imagem']['name'];
				
				}
					
				if($_FILES['imagem']['tmp_name'] != ""){
				
					$copied = copy($_FILES['imagem']['tmp_name'],$novoNome);
				
				}
					
				if($copied){
					
					$dadosUp['path'] = $novoNome;
					$dadosUp['tipo_imagem'] = $_POST['tipo_imagem'];
					$dadosUp['link'] = $_POST['link'];
					$dadosUp['data_upload'] = @date("Y-m-d");
						
					if($dbPropagandasSite->insert($dadosUp)){
					
						$this->view->mensagem = "Imagem enviada com sucesso.";
					
					}else{
					
						$this->view->mensagem = "Erro ao enviar o imagem.";
					
					}
					
				}else{
				
					$this->view->mensagem = "Erro. N&atilde;o foi poss&iacute;vel realizar o upload da imagem.";
				
				}
				
			}
			
		}
	
	}

	public function listaSlidesAction(){
	
		$dbPropagandasSite = new Application_Model_DbTable_PropagandasSite();
		
        $id_empresa = $_SESSION['sessionUser']['id_empresa'];

		$this->view->imagens = $dbPropagandasSite->getImagensSlides($id_empresa);
	
	}
	
	public function listaAction(){
	
		$dbPropagandasSite = new Application_Model_DbTable_PropagandasSite();

        
		
		
		$this->view->imagens = $dbPropagandasSite->getImagens();
	
	}
	
	public function delAction(){
	
		$dbPropagandasSite = new Application_Model_DbTable_PropagandasSite();
		$arrImagem = $dbPropagandasSite->getImagem($this->_getParam('id'));
		
		if($dbPropagandasSite->delete("id = ".$this->_getParam('id'))){
		
			unlink($arrImagem[0]['path']);
		
			$this->_helper->redirector->gotoUrl("upload-propagandas/lista");

		
		}else{
		
			$this->view->mensagem = "Erro. N&atilde;o foi poss&iacute;vel deletar a imagem. <a href='/upload-propagandas/lista'>Voltar</a>";
		
		}
	
	}

	public function edtAction(){
	
		$dbPropagandasSite = new Application_Model_DbTable_PropagandasSite();
		
		if($this->getRequest()->isPost()){
		
			if($dbPropagandasSite->edt($this->_getParam('id'), $_POST)){
			
				$this->view->mensagem = "Dados salvo com sucesso.";
			
			}else{
			
				$this->view->mensagem = "Erro ao realizar a edi&ccedil;&atilde;o de dados.";
			
			}
		
		}
		
		$this->view->imagem = $dbPropagandasSite->getImagem($this->_getParam('id'));

	}
	
	public function edtSlideAction(){
	
		$dbPropagandasSite = new Application_Model_DbTable_PropagandasSite();
		
		if($this->getRequest()->isPost()){
		
			if($dbPropagandasSite->edt($this->_getParam('id'), $_POST)){

				$this->view->imagem = $dbPropagandasSite->getImagem($this->_getParam('id'));
				$this->_helper->redirector->gotoUrl("upload-propagandas/lista-slides");
			
			}else{
			
				$this->view->mensagem = "Erro ao realizar a edi&ccedil;&atilde;o de dados.";
			
			}
		
		}
		
		$this->view->imagem = $dbPropagandasSite->getImagem($this->_getParam('id'));

	}
	
}

?>
