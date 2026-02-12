<?php

header("Content-Type: text/html; charset=UTF-8",true);

class BannerController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Banner";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){
	
		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	private function listDir($start){

		$handle = opendir($start);
		
		if(is_dir($start)){
		
			while (false !== ($file = readdir($handle))) {
		 
				if($file != '.' && $file != '..'){
		 
					$arr[] = $file;
		 
				}
		 
			}
		 
		}

		return $arr;

    }
	
	public function addAction(){
	
		$this->validaAcesso('gerenciar_banner');
	
		$nome = $this->listDir($start);
		
		$start = "propaganda";
		
		$nome = $this->listDir($start);
		
		$nomeImg = $nome[0];
		
		list($w, $h) = getimagesize("propaganda/".$nomeImg."");
		
		$this->view->w = $w;
		$this->view->h = $h;
		
		$this->view->nomeImg = $nomeImg;
		
		if($this->getRequest()->isPost()){
			
			$dados = $this->getRequest()->getPost();
			
			$image = $_FILES['image']['name'];
			
			//var_export($image);exit;
			
			if($image){
		
				$filename = stripslashes($_FILES['image']['name']);
	
				$extension = explode(".",$filename);
				
				$extensao = $extension[1];
	
				$extansao = strtolower($extensao);
	
				$image_name = "bannerAtual".'.'.$extensao;
		
				if(file_exists("propaganda")){
				
					$newname="propaganda/".$image_name;
			
				}else{
					
					mkdir("propaganda");
					$newname="propaganda/".$image_name;
		
				}

				if($copied = copy($_FILES['image']['tmp_name'], $newname)){
				
					$this->view->mensagem = "Upload da imagem foi efetuado com sucesso";
				
				}else{
			
					$this->view->mensagem = "Erro ao efetuar o upload da imagem";
					
				}
			
			}else{
		
				if($deleta = unlink("propaganda/".$nomeImg."")){
				
					$this->view->mensagem = "Banner deletado com sucesso.";
				
				}else{
			
					$this->view->mensagem = "Erro ao deletar Banner.";
					
				}
			
			}
		
		}
		
	}
	
	public function showAction(){
	
		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
		
		$start = "propaganda";
		
		$nome = $this->listDir($start);
		
		$nomeImg = $nome[0];
		
		$this->view->nomeImg = $nomeImg;
	
	}
	
}

?>
