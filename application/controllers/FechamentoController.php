<?php

header("Content-Type: text/html; charset=UTF-8",true);

class FechamentoController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Fechamento";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function listaAction(){
	
			
	}
	
}

?>