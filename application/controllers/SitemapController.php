<?php

header("Content-Type: text/html; charset=UTF-8",true);

class SitemapController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Sitemap";

		Zend_Session::start();
		

	}

	
	public function indexAction(){
		
		
	
	}
	
}
	

?>
