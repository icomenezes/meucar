<?php

header("Content-Type: text/html; charset=UTF-8",true);

class FornecedoresController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Fornecedores";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function ajaxAction(){

		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
		
		$dbFornecedores = new Application_Model_DbTable_Fornecedores();
	
		$arrFornecedores = $dbFornecedores->getFornecedoresPorEmpresa($_POST['idempresa']);
		
		$strFornecedor = "<option value=''>Selecione</option>";
		
		foreach($arrFornecedores as $fornecedor){
	
			$strFornecedor .= "<option value='".$fornecedor['id']."'>".$fornecedor['razao_social']."</option>";
		
		}
		
		echo $strFornecedor;
		
	}
	
	public function addAction(){
		
		$this->validaAcesso('gerenciar_fornecedores');
		
		if($this->getRequest()->isPost()){
		
			$dbFornecedores = new Application_Model_DbTable_Fornecedores();
			
			$dados = $_POST;
			$dados['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dados['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			if($dbFornecedores->add($dados)){
			
				$this->view->mensagem = "Fornecedor cadastrado com sucesso!";
			
			}else{
			
				$this->view->mensagem = "Erro ao cadastrar fornecedor!";
			
			}
			
		}
	
	}
	
	public function listaAction(){
	
		$this->validaAcesso('listar_fornecedores');
		
		$dbFornecedores = new Application_Model_DbTable_Fornecedores();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		if($this->getRequest()->isPost()){
		
			$arr['parcial'] = true;
			$arr['nome'] = $this->_getParam('razao_social');
			$arr['cnpj'] = $this->_getParam('cnpj');
			$arr['ramo_atividade'] = $this->_getParam('ramo_atividade');
			
		}
		
		$arrFornecedores = $dbFornecedores->_getDois($arr);
		
		$this->view->fornecedores = $arrFornecedores;
	
	}
	
	public function delAction(){

		$this->validaAcesso('gerenciar_fornecedores');
	
		$dbFornecedores = new Application_Model_DbTable_Fornecedores();
		
		try{
		
		$dbFornecedores->delete('id ='.$this->_getParam('id'));		
		$this->_helper->redirector->gotoUrl("fornecedores/lista");
	
		}catch(Exception $e){
		
			$this->view->mensagem = "Erro ao deletar Grupo. Existem refer&ecirc;ncias a este registro.";
		
		}
	}
	
	public function edtAction(){ 
	
		$this->validaAcesso('gerenciar_fornecedores');
	
		$dbFornecedores = new Application_Model_DbTable_Fornecedores();
		
		$arrFornecedores = $dbFornecedores->getFornecedor($this->_getParam('id'));
	
		$this->view->fornecedores = $arrFornecedores[0];
		
		if($this->getRequest()->isPost()){
		
			$dbFornecedores = new Application_Model_DbTable_Fornecedores();
			
			$_POST['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$_POST['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			if($_POST['ativo'] == ""){
			
				$_POST['ativo'] = 0;
			
			}
			
			if($dbFornecedores->edt($_POST['id'],$_POST)){
			
				$this->_helper->redirector->gotoUrl("fornecedores/lista");
			
			}else{
			
				$this->view->mensagem = "Erro ao editar fornecedor!";
			
			}

		}
		
	}

}

?>
