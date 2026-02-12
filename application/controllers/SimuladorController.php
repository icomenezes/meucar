<?php

header("Content-Type: text/html; charset=UTF-8",true);

class SimuladorController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Simulador";

		Zend_Session::start();
		
	}
	
	public function validaAcesso($require){
	
		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	
	
	public function configuraSimuladorAction(){
	
		$this->validaAcesso('gerenciar_permissoes');
		
		$dbSimulador = new Application_Model_DbTable_Simulador();
		
		if($this->getRequest()->isPost()){
			
			foreach($_POST as $chave=>$post){
			
				$chaves = explode("-",$chave);
				$id = $chaves[1];
				$key = $chaves[0];
				
				$dados[$key] = $post;
				
				$dbSimulador->edt($id,$dados);
				
				//var_export($antes_depois."<br>");
			
			}
			
			$this->view->mensagem = "Valores alterados com sucesso!";
		
		}
		
		$this->view->dados = $dbSimulador->getDadosSimulador();
	
	}
	
}

?>
