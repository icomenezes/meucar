<?php

header("Content-Type: text/html; charset=UTF-8",true);

class GruposFinanceirosController extends Zend_Controller_Action
{

	public function init(){
		
		$this->view->titulo = "Grupos Financeiros";
		
		Zend_Session::start();
		
	}

	public function validaAcesso($require){
	
		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function addAction(){
		
		$dbGrupoFinanceiro = new Application_Model_DbTable_GruposFinanceiros();
		
		$this->validaAcesso('gerenciar_financeiro_grupos');
		
		if($this->getRequest()->isPost()){	
			
			$dados = $_POST;
			
			$dados['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			
			$dados['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dados['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			if($dbGrupoFinanceiro->insert($dados)){
   
				$this->view->mensagem = "Grupo financeiro cadastrado com sucesso!";
			   
			}else{
			   
				$this->view->mensagem = "Erro ao cadastrar o grupo financeiro.";
			   
			}
			
		}
	
	}
	
	public function listaAction(){
	
		$this->validaAcesso('listar_financeiro_grupos');
		
		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrFiltro['noDefault'] = 1;
		
		$arrGruposFinanceiros = $dbGruposFinanceiros->_get($arrFiltro);
		
		$this->view->gruposFinanceiros = $arrGruposFinanceiros;
		
		if($this->_getParam('erro')){
			
			$this->view->mensagem = "Erro ao deletar Grupo. Existem referências a este registro.";
		
		}
	
	}
	
	public function delAction(){

		$this->validaAcesso('listar_financeiro_grupos');
		
		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		
		try{
		
		$dbGruposFinanceiros->delete('id ='.$this->_getParam('id'));		
		$this->_helper->redirector->gotoUrl("grupos-financeiros/lista");
	
		}catch(Exception $e){
		
			$this->view->mensagem = "Erro ao deletar Grupo. Existem refer&ecirc;ncias a este registro.";
		
		}
	}
	
	public function edtAction(){
	
		$this->validaAcesso('gerenciar_financeiro_grupos');

		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		
		$this->view->id = $this->_getParam('id');
		
		$dados = $dbGruposFinanceiros->fetchAll("id = " . $this->_getParam('id'));
		
		$this->view->grupoFinanceiro = $dados[0];

		if($this->getRequest()->isPost()){
			
			$_POST['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$_POST['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			$dbGruposFinanceiros->update($_POST, "id = " . $this->_getParam('id'));
			
			$this->_helper->redirector->gotoUrl("grupos-financeiros/lista");
		
		}
	
	}	

}

?>
