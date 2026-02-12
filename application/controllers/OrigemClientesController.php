<?php

header("Content-Type: text/html; charset=UTF-8",true);

class OrigemClientesController extends Zend_Controller_Action
{

	public function init(){
		
		$this->view->titulo = "Origem Clientes";
		
		Zend_Session::start();
		
	}

	public function validaAcesso($require){
		
		return true;
		
		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function addAction(){
		
		$dbOrigemCliente = new Application_Model_DbTable_OrigemClientes();
		
		//$this->validaAcesso('gerenciar_origem_clientes');
		
		if($this->getRequest()->isPost()){	

			if($_POST['exibir'] != 1){
				$_POST['exibir'] = 0;
			}
			
			$dados = $_POST;
			
			$dados['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			
			if($dbOrigemCliente->insert($dados)){
   
				$this->view->mensagem = "Origem Cliente cadastrado com sucesso!";
			   
			}else{
			   
				$this->view->mensagem = "Erro ao cadastrar o Origem Cliente.";
			   
			}
			
		}
	
	}
	
	public function listaAction(){
	
		$this->validaAcesso('listar_origem_clientes');
		
		$dbOrigemClientes = new Application_Model_DbTable_OrigemClientes();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrFiltro['noDefault'] = 1;
		
		$arrOrigemClientes = $dbOrigemClientes->_getOrigem($arrFiltro);

		//echo '<pre>';
		//var_export($arrOrigemClientes);
		//echo '</pre>';
		
		$this->view->origemClientes = $arrOrigemClientes;
		
		if($this->_getParam('erro')){
			
			$this->view->mensagem = "Erro ao deletar Grupo. Existem referências a este registro.";
		
		}
	
	}
	
	public function delAction(){

		$this->validaAcesso('gerenciar_origem_clientes');
	
		$dbOrigemClientes = new Application_Model_DbTable_OrigemClientes();
		
		
		
		try{
		
			$dbOrigemClientes->delete("id = " . $this->_getParam('id'));
			$this->_helper->redirector->gotoUrl("origem-clientes/lista");
			
		}catch(Exception $e){
		
			$this->view->mensagem = "Erro ao deletar Grupo. Existem referências a este registro.";
		
		}
		
		

	}
	
	public function edtAction(){
	
		$this->validaAcesso('gerenciar_origem_clientes');

		$dbOrigemClientes = new Application_Model_DbTable_OrigemClientes();
		
		$this->view->id = $this->_getParam('id');
		
		$dados = $dbOrigemClientes->fetchAll("id = " . $this->_getParam('id'));
		
		$this->view->origemCliente = $dados[0];

		if($this->getRequest()->isPost()){
			
			//$_POST['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			//$_POST['hora_alteracao'] = @date("Y-m-d H:i:s");

			if($_POST['exibir'] != 1){
				$_POST['exibir'] = 0;
			}
			
			$dbOrigemClientes->update($_POST, "id = " . $this->_getParam('id'));
			
			$this->_helper->redirector->gotoUrl("origem-clientes/lista");
		
		}
	
	}	

}

?>
