<?php

header("Content-Type: text/html; charset=UTF-8",true);

class TestesController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Testes";

		Zend_Session::start();
		
	}
	
	public function validaAcesso($require){
	
		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	
	
	public function testeAction(){
	
		/*
		echo "Operação em primeiro plano</br>";
		exec( "php segundoPlano.php > /tmp/segundoPlanoIndex.txt &");
		*/
		
		//fopen('../application/views/scripts/testes/txteste.txt','a');
		
		exec( "php ../application/views/scripts/testes/teste2.phtml > ../application/views/scripts/testes/txteste.txt &");
		
		
		/*
		$path = "../application/views/scripts/testes";
		$diretorio = dir($path);

		echo "Lista de Arquivos do diretório '<strong>".$path."</strong>':<br />";
		
		while($arquivo = $diretorio -> read()){
		
			echo "<a href='".$path.$arquivo."'>".$arquivo."</a><br />";

		}
		
		$diretorio -> close();
		
		*/
	
	}
	
	public function teste2Action(){

		echo "Controller";

	}
	
}

?>
