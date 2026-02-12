<?php

class GerenciadorEmailsController extends Zend_Controller_Action {

	public function init() {

		$this->view->titulo = "E-mails";
		Zend_Session::start();
		
		$layout = $this->_helper->layout();
		$layout->setLayout('layout-emails');
		
	}
   
	public function indexAction(){
		
		echo "Index";
		
	}
	
	
	public function addAction(){
		
		echo "Add";
		
	}

}
?>

