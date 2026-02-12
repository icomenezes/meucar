<?php

header("Content-Type: text/html; charset=UTF-8",true);

class OpcionaisController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Opcionais";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		return true;

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function ajaxAction(){

		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
		
		$dbOpcionais = new Application_Model_DbTable_Opcionais();
	
		$arrOpcionais = $dbOpcionais->getOpcionais();
		/*
		$strOpcionais ="<input type='checkbox' id='seleciona_tudo' value='1'style='margin-left:21px;' onclick='selecionaTudo(this.value);'/><span style='font-size:18px;'>Selecionar todos</span>
						<table class='table'><tr>";
		*/
		
	
		$strOpcionais ="<table class='table'><tr><td colspan='3'>
							<input type='checkbox' id='seleciona_tudo' value='1'style='margin-left:4px;' onclick='selecionaTudo(this.value);'/><span style='font-size:18px;'>Selecionar todos</span></td></tr><tr>";
	
		
		$cont=1;
		
		foreach($arrOpcionais as $opcionais){
		
			if($opcionais['id'] == 1 || $opcionais['id'] == 2 || $opcionais['id'] == 42 || $opcionais['id'] == 4 || $opcionais['id'] == 50 || $opcionais['id'] == 34 || $opcionais['id'] == 20 || $opcionais['id'] == 9 || $opcionais['id'] == 52 || $opcionais['id'] == 57 || $opcionais['id'] == 16 || $opcionais['id'] == 47 || $opcionais['id'] == 48 || $opcionais['id'] == 49){
				
				$strOpcionais .= "<td width='32%'><input type='checkbox' id='opcional_".$opcionais['id']."'name='opcional_".$opcionais['id']."'/><b style='color:#333333;'>".$opcionais['opcional']."</b></td>";

			}else{
			
				$strOpcionais .= "<td width='32%'><input type='checkbox' id='opcional_".$opcionais['id']."'name='opcional_".$opcionais['id']."'/>".$opcionais['opcional']."</td>";
			
			}
			
			if($cont == 3){
				$strOpcionais .= "</tr>";
				$strOpcionais .= "<tr>";
				$cont = 0;
			}
			
			$cont++;
		
		}
		
		echo $strOpcionais.="</tr></table>";
		
	}
	
	public function addAction(){
		
		$dbEmpresa = new Application_Model_DbTable_Empresas();
		
		$this->validaAcesso('empresas');
		
		if($this->getRequest()->isPost()){	
			
			$dados = $_POST;
			
			if($dbEmpresa->insert($dados)){
   
				$this->view->mensagem = "Empresa cadastrada com sucesso!";
			   
			}else{
			   
				$this->view->mensagem = "Erro ao cadastrar o Empresa.";
			   
			}
			
		}
	
	}
	
	public function listaAction(){
	
		$this->validaAcesso('empresas');
		
		$dbEmpresa = new Application_Model_DbTable_Empresas();
		
		$arrEmpresas = $dbEmpresa->fetchAll();
		
		$this->view->empresas = $arrEmpresas;
	
	}
	
	public function delAction(){

		$this->validaAcesso('empresas');
	
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$dbEmpresas->delete("id = " . $this->_getParam('id'));
		
		$this->_helper->redirector->gotoUrl("empresas/lista");
	
	}
	
	public function edtAction(){
	
		$this->validaAcesso('empresas');

		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$this->view->id = $this->_getParam('id');
		
		$dados = $dbEmpresas->fetchAll("id = " . $this->_getParam('id'));
		
		$this->view->empresa = $dados[0];
				
		if($this->getRequest()->isPost()){
		
			$dbEmpresas->update($_POST, "id = " . $this->_getParam('id'));
			
			$this->_helper->redirector->gotoUrl("empresas/lista");
		
		}
	
	}

}

?>
