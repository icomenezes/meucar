<?php

header("Content-Type: text/html; charset=UTF-8",true);

class WhatsappController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Whatsapp";

		Zend_Session::start();
		
	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function whatsappAction(){

		$layout = $this->_helper->layout();
	  	$layout->setLayout('layout-whatsapp');
		
	}

}

?>
