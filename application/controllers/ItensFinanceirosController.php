<?php

header("Content-Type: text/html; charset=UTF-8",true);

class ItensFinanceirosController extends Zend_Controller_Action
{

	public function init(){
		
		$this->view->titulo = "Itens Financeiros";
		
		Zend_Session::start();
		
	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function addAction(){
		
		$this->validaAcesso('gerenciar_financeiro_itens');
		
		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrGruposFinanceiros = $dbGruposFinanceiros->_get($arrFiltro);
		
		$this->view->grupos = $arrGruposFinanceiros;
		
		$dbItemFinanceiro = new Application_Model_DbTable_ItensFinanceiros();
		
		if($this->getRequest()->isPost()){	
			
			$dados = $_POST;
			$dados['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			
			$dataTmp = explode("/",$dados['data_inicio']);
			$dados['data_inicio'] = implode("-",array_reverse($dataTmp));
			
			$dados['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dados['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			$dados['valor_padrao'] = str_replace(",", ".", $dados['valor_padrao']);
			
			try {

				if($dbItemFinanceiro->insert($dados)){

					$this->view->mensagem = "Item Financeiro cadastrado com sucesso!";

				}else{

					$this->view->mensagem = "Erro ao cadastrar Item Financeiro.";

				}

			} catch (Exception $e) {

				if (strpos($e->getMessage(), '1062') !== false) {
					$this->view->mensagem = "Já existe um item financeiro com este nome para sua empresa.";
				} else {
					$this->view->mensagem = "Erro ao cadastrar Item Financeiro.";
				}

			}
			
		}
	
	}
	
	public function listaAction(){
	
		$this->validaAcesso('listar_financeiro_itens');
		
		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrGruposFinanceiros = $dbGruposFinanceiros->_get($arrFiltro);
		
		$this->view->grupos = $arrGruposFinanceiros;
		
		$dbItensFinanceiros = new Application_Model_DbTable_ItensFinanceiros();
		
		if($this->getRequest()->isPost()){
		
			$arrFiltro['id_financeiro_grupo'] = $_POST['id_financeiro_grupo'];
		
		}
		
		if($this->_getParam('erro')){
			
			$this->view->mensagem = "Erro ao deletar Item. Existem lan&ccedil;amentos pertencentes a este item.";
		
		}
		
		$arrItensFinanceiros = $dbItensFinanceiros->_get($arrFiltro);
		
		$this->view->itensFinanceiros = $arrItensFinanceiros;

	}
	
	public function delAction(){
		
		$this->validaAcesso('gerenciar_financeiro_itens');
		
		$dbItensFinanceiros = new Application_Model_DbTable_ItensFinanceiros();
		
		try{
		
			$dbItensFinanceiros->delete("id = " . $this->_getParam('id'));
			
			$this->_helper->redirector->gotoUrl("itens-financeiros/lista");
			
		}catch(Exception $e){
		
			$this->_helper->redirector->gotoUrl("itens-financeiros/lista/erro/erro");
		
		}
		
	}
	
	public function edtAction(){
	
		$this->validaAcesso('gerenciar_financeiro_itens');
		
		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrGruposFinanceiros = $dbGruposFinanceiros->_get($arrFiltro);
		
		$this->view->grupos = $arrGruposFinanceiros;

		$dbItensFinanceiros = new Application_Model_DbTable_ItensFinanceiros();
		
		$this->view->id = $this->_getParam('id');
		
		$dados = $dbItensFinanceiros->fetchAll("id = " . $this->_getParam('id'));
		
		$dataTmp = explode("-",$dados[0]['data_inicio']);
		$dados[0]['data_inicio'] = implode("/",array_reverse($dataTmp));
		
		$this->view->itemFinanceiro = $dados[0];

		if($this->getRequest()->isPost()){
			
			$dataTmp = explode("/",$_POST['data_inicio']);
			$_POST['data_inicio'] = implode("-",array_reverse($dataTmp));
			
			$_POST['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$_POST['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			$dbItensFinanceiros->update($_POST, "id = " . $this->_getParam('id'));
			
			$this->_helper->redirector->gotoUrl("itens-financeiros/lista");
		
		}
	
	}	

}

?>