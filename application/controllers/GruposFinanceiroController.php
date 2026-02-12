<?php

header("Content-Type: text/html; charset=UTF-8",true);

class GruposFinanceirosController extends Zend_Controller_Action
{

	public function init(){
		
		$this->view->titulo = "Concession&aacute;rias";
		
		Zend_Session::start();
		
	}

	public function validaAcesso($require){

		return true;

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function addAction(){
		
		$dbGrupoFinanceiro = new Application_Model_DbTable_GruposFinanceiros();
		
		$this->validaAcesso('gruposFinanceiros');
		
		if($this->getRequest()->isPost()){	
			
			$dados = $_POST;
			
			$dados['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			
			if($dbGrupoFinanceiro->insert($dados)){
   
				$this->view->mensagem = "Grupo financeiro cadastrado com sucesso!";
			   
			}else{
			   
				$this->view->mensagem = "Erro ao cadastrar o grupo financeiro.";
			   
			}
			
		}
	
	}
	
	public function listaAction(){
	
		$this->validaAcesso('gruposFinanceiros');
		
		$dbGrupoFinanceiro = new Application_Model_DbTable_GruposFinanceiros();
		
		$arrGruposFinanceiros = $dbGrupoFinanceiro->fetchAll();
		
		$this->view->gruposFinanceiros = $arrGruposFinanceiros;
	
	}
	
		public function delAction(){

		$this->validaAcesso('gerenciar_gruposFinanceiros');
	
		$dbGrupoFinanceiro = new Application_Model_GruposFinanceiro();
		
		try{
		
		$dbGrupoFinanceiro->delete('id ='.$this->_getParam('id'));		
		$this->_helper->redirector->gotoUrl("grupos-financeiro/lista");
	
		}catch(Exception $e){
		
			$this->view->mensagem = "Erro ao deletar Grupo. Existem refer&ecirc;ncias a este registro.";
		
		}
	}
	
	public function edtAction(){
	
		$this->validaAcesso('gruposFinanceiros');

		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		
		$this->view->id = $this->_getParam('id');
		
		$dados = $dbGruposFinanceiros->fetchAll("id = " . $this->_getParam('id'));
		
		$this->view->grupoFinanceiro = $dados[0];

		if($this->getRequest()->isPost()){
		
			$dbGruposFinanceiros->update($_POST, "id = " . $this->_getParam('id'));
			
			$this->_helper->redirector->gotoUrl("gruposFinanceiros/lista");
		
		}
	
	}
	
	
	
	

}

?>
