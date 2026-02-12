<?php

header("Content-Type: text/html; charset=UTF-8",true);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

class FipeController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Fipe";

		Zend_Session::start();
		
	}
	
	public function validaAcesso($require){
	
		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function marcasFipeAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		if($this->getRequest()->isPost()){	
		
			if($_POST['app'] == true){
				
				$dbFipe = new Application_Model_DbTable_Fipe();
				echo json_encode($dbFipe->getMarcas());
				
			}
		
		}

	}

	
	public function modelosFipeAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		if($this->getRequest()->isPost()){	
		
			//if($_POST['app'] == true){
			if($this->_getParam('app') == true){
				
				$dbFipe = new Application_Model_DbTable_Fipe();
				
				$arrModelos = $dbFipe->getModelos();
				
				foreach($arrModelos as $key=>$arr){
					$arrModelos[$key]['nome'] = str_replace("'", " ", mb_convert_encoding($arr['nome'], 'UTF-8', 'ISO-8859-1'));
				}
				
				echo json_encode($arrModelos);
				
			}
		
		}

	}

	public function anosModelosFipeAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		$dbFipe = new Application_Model_DbTable_Fipe();
		
		$arrM = $dbFipe->getAnosModelos();
		
		foreach($arrM as $key=>$arr){
			$arrM[$key]['nome'] = mb_convert_encoding($arr['nome'], 'UTF-8', 'ISO-8859-1');
		}
		
		echo json_encode($arrM);

	}
	
}

?>
