<?php

header("Content-Type: text/html; charset=UTF-8",true);

class PermissoesController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Permissoes";

		Zend_Session::start();
		
	}
	
	public function validaAcesso($require){
	
		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function addAction(){
	
		$this->validaAcesso('gerenciar_permissoes');
		
		$dbPerfil = new Application_Model_DbTable_Perfis();
		
		$arrPerfil = $dbPerfil->fetchAll();
		
		$this->view->perfis = $arrPerfil;
		
		$dbPermissoes = new Application_Model_DbTable_Permissoes();
		
		$colunasPermissoes = $dbPermissoes->fetchAll(null, null, 1, 0);
		
		$this->view->colunasPermissoes = $colunasPermissoes[0];
		
		if($this->getRequest()->isPost()){
			
			$dados = $_POST;
			
			if($dbPermissoes->insert($dados)){
		
				$this->view->mensagem = "Permissão cadastrado com sucesso!.";
	
			}else{
			
				$this->view->mensagem = "Erro ao cadastrar usuario!.";
			
			}
			
		}
	
	}
	
	public function edtAction(){
	
		$this->validaAcesso('gerenciar_permissoes');
		
		$dbPerfil = new Application_Model_DbTable_Perfis();
		
		$arrPerfil = $dbPerfil->fetchAll();
		
		$this->view->perfis = $arrPerfil;
		
		$dbPermissoes = new Application_Model_DbTable_Permissoes();
		
		$colunasPermissoes = $dbPermissoes->fetchAll(null, null, 1, 0);
		
		$this->view->colunasPermissoes = $colunasPermissoes[0];
		
		$this->view->id = $this->_getParam('id');
		
		$dados = $dbPermissoes->fetchAll("id = " . $this->_getParam('id'));
		
		$this->view->permissao = $dados[0];
		
		if($this->getRequest()->isPost()){
			
			$dados = $_POST;
			
			$cont = 0;
			
			foreach($colunasPermissoes[0] as $k => $v){
				
				if($k != "id" && $k != "id_perfil"){
					
					$arrNome = explode("_", $k);
					
					$nome = $arrNome[0] . " " . $arrNome[1];
					
					$nome = ucwords($nome);
					
					$arrZerado[$k] = 0;
					
				}
			
			}
			
			$dbPermissoes->update($arrZerado, "id = " . $this->_getParam('id'));
			
			$dbPermissoes->update($dados, "id = " . $this->_getParam('id'));
			
			$this->_helper->redirector->gotoUrl("permissoes/lista");
		
		}
	
	}
	
	public function listaAction(){
	
		$this->validaAcesso('listar_permissoes');
	
		$dbPermissoes = new Application_Model_DbTable_Permissoes();
	
		$arrFiltro['all'] = "1";
	
		$arrPermissoes = $dbPermissoes->_get($arrFiltro);
		
		//var_export($arrPermissoes[0]);
		//exit;

		$this->view->permissoes = $arrPermissoes;
	
	}
	
	public function delAction(){
	
		$this->validaAcesso('gerenciar_permissoes');
	
		$dbPermissoes = new Application_Model_DbTable_Permissoes();
		
		$dbPermissoes->delete("id = " . $this->_getParam('id'));
		
		$this->_helper->redirector->gotoUrl("permissoes/lista");
	
	}
	
	public function usuarioAction(){
	
		$this->validaAcesso('permissoes_usuario');
		
		$dbPermissoes = new Application_Model_DbTable_Permissoes();
		
		$arrFiltro['id_usuario'] = $_SESSION['sessionUser']['id']; 	
		$arrFiltro['id_perfil'] = $_SESSION['sessionUser']['id_perfil'];
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		//var_export($arrFiltro);exit;
		$arrPermissoes = $dbPermissoes->_getUsuario($arrFiltro);
		
		$this->view->permissoes = $arrPermissoes; 
	}
	
}

?>
