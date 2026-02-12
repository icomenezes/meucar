<?php

header("Content-Type: text/html; charset=UTF-8",true);

class FinanceirasController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Financeiras / Despachantes";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function ajaxAction(){

		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
		
		$dbFinanceiras = new Application_Model_DbTable_Financeiras();
		
		$arr['id'] = $this->_getParam('id_financeira');
	
		$arrFinanceiras = $dbFinanceiras->_get($arr);
		
		$strFinanceiras = $arrFinanceiras[0]['imposto'];
		
		echo $strFinanceiras;
		
		
	}
	
	public function addAction(){
		
		$dbFinanceira = new Application_Model_DbTable_Financeiras();
		
		$this->validaAcesso('gerenciar_financeiras_despachantes');
		
		$this->view->id_empresa = $_SESSION['sessionUser']['id_empresa'];
		
		if($this->getRequest()->isPost()){	

			$_POST['imposto'] = str_replace(",",".",$_POST['imposto']);
			$_POST['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$_POST['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			$dados = $_POST;
			
			if($dbFinanceira->insert($dados)){
   
				$this->view->mensagem = "Cadastrada com sucesso!";
			   
			}else{
			   
				$this->view->mensagem = "Erro ao cadastrar!.";
			   
			}
			
		}
	
	}
	
	public function listaAction(){
	
		$this->validaAcesso('listar_financeiras_despachantes');
		
		$dbFinanceira = new Application_Model_DbTable_Financeiras();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		if($this->getRequest()->isPost()){
		
			$arr['parcial'] = true;
			$arr['nome'] = $this->_getParam('nome');
			$arr['cnpj'] = $this->_getParam('cnpj');
			
		}
		
		$arrFinanceiras = $dbFinanceira->_getFD($arr);
		
		$this->view->financeiras = $arrFinanceiras;
	
	}
	
	public function delAction(){

		$this->validaAcesso('gerenciar_financeiras_despachantes');
	
		$dbFinanceiras = new Application_Model_DbTable_Financeiras();
		
		try{
		
			$dbFinanceiras->delete("id = " . $this->_getParam('id'));		
			$this->_helper->redirector->gotoUrl("financeiras/lista");
			
		}catch(Exception $e){
		
			$this->view->mensagem = "Erro ao deletar Grupo. Existem referências a este registro.";
		
		}
		
		
	
	}
	
	public function edtAction(){
	
		$this->validaAcesso('gerenciar_financeiras_despachantes');

		$dbFinanceiras = new Application_Model_DbTable_Financeiras();
		
		$dados = $dbFinanceiras->fetchAll("id = " . $this->_getParam('id'));
		
		$this->view->financeira = $dados[0];
		
		$this->view->id = $this->_getParam('id');
		$this->view->id_empresa = $_SESSION['sessionUser']['id_empresa'];
				
		if($this->getRequest()->isPost()){
		
			$_POST['imposto'] = str_replace(",",".",$_POST['imposto']);
			$_POST['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$_POST['hora_alteracao'] = @date("Y-m-d H:i:s");
		
			$dbFinanceiras->update($_POST, "id = " . $this->_getParam('id'));
			
			$this->_helper->redirector->gotoUrl("financeiras/lista");
		
		}
	
	}

}

?>
