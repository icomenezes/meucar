<?php

header("Content-Type: text/html; charset=UTF-8",true);

class PerfisController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Perfil";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function addPerfilAction(){
		
		$dbPerfil = new Application_Model_DbTable_Perfis();
		
		$this->validaAcesso('perfis');
		
		if($this->getRequest()->isPost()){	
			
			$dados = $_POST;
			
			$arrPermissoes = $dados;
			
			unset ($dados['submit']);
			unset ($dados['empresas']);
			unset ($dados['hierarquias']);
			unset ($dados['tipos']);
			unset ($dados['perfis']);
			unset ($dados['usuarios']);
			unset ($dados['nfs']);
			unset ($dados['uploads']);
			//unset ($dados['cidades']);
			//unset ($dados['clientes']);
			//unset ($dados['complementos_hierarquias']);
			//unset ($dados['empresas']);
			//unset ($dados['estados']);
			//unset ($dados['itens_nfs']);
			//unset ($dados['usuarios']);
			//unset ($dados['nfs']);
			//unset ($dados['hierarquia']);
			//unset ($dados['paradas']);
			//unset ($dados['perfis_usuarios']);
			//unset ($dados['produtos']);
			//unset ($dados['roteiros']);
			//unset ($dados['hierarquias_usuarios']);
			//unset ($dados['vendedores']);
			//unset ($dados['visitas']);
			
			if($dbPerfil->add($dados)){
   
				$this->view->mensagem = "Perfil cadastrado com sucesso!";
			   
			}else{
			   
				$this->view->mensagem = "Erro ao cadastrar o Perfil. Perfil já existente";
			   
			}
			
			$idPerfil = $dbPerfil->getLastId();
			
			unset ($arrPermissoes['perfil']);
			
			$arrPermissoes['id_perfil'] = $idPerfil[0]['id'];
			
			$dbPermissoes = new Application_Model_DbTable_Permissoes();
			
			if($dbPermissoes->add($arrPermissoes)){
   
				$this->view->mensagem = "Perfil cadastrado com sucesso!";
			   
			}else{
			   
				$this->view->mensagem = "Erro ao cadastrar o Perfil. Perfil já existente";
			   
			}
			
		}
	
	}
	
	public function listaPerfisAction(){
	
		$this->validaAcesso('perfis');
		
		$dbPerfil = new Application_Model_DbTable_Perfis();
		
		$arrPerfis = $dbPerfil->getPerfis();
		
		$this->view->perfis = $arrPerfis;
	
	}
	
	public function deletePerfilAction(){

		$this->validaAcesso('perfis');
	
		$dbPerfis = new Application_Model_DbTable_Perfis();
		
		$dbPerfis->del($this->_getParam('id'));
		
		$this->_helper->redirector->gotoUrl("perfis/lista-perfis");
	
	}
	
	public function edtPerfilAction(){
	
		$this->validaAcesso('perfis');

		$dbPerfil = new Application_Model_DbTable_Perfis();
		
		$dados = $dbPerfil->getPerfil($this->_getParam('id'));
		
		$this->view->perfil = $dados[0];
		
		$dbPermissoes = new Application_Model_DbTable_Permissoes();
		
		$permissoes = $dbPermissoes->getPermissao($this->_getParam('id'));
		
		$this->view->permissoes = $permissoes[0];	
		
		if($this->getRequest()->isPost()){
		
			$param = $_POST;
			
			$permissoes = $param;
			
			unset ($dados['submit']);
			unset ($dados['empresas']);
			unset ($dados['hierarquias']);
			unset ($dados['tipos']);
			unset ($dados['perfis']);
			unset ($dados['usuarios']);
			unset ($dados['nfs']);
			
			$idPermissao = $permissoes['id_permissao'];
			
			unset ($permissoes['submit']);
			unset ($permissoes['perfil']);
			unset ($permissoes['id']);
			unset ($permissoes['id_permissao']);
			
			$zeraPermissoes = array('empresas'=>0,'hierarquias'=>0,'tipos'=>0,'perfis'=>0,'usuarios'=>0,'nfs'=>0);
			
			$dbPermissoes->edt($idPermissao,$zeraPermissoes);
			
			$dbPerfil->edt($param['id'],$param);
			
			$dbPermissoes->edt($idPermissao,$permissoes);
			
			$this->_helper->redirector->gotoUrl("perfis/lista-perfis");
		
		}
	
	}

}

?>
