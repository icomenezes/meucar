<?php

header("Content-Type: text/html; charset=UTF-8",true);

class LancamentosFinanceirosController extends Zend_Controller_Action
{

	public function init(){
		
		$this->view->titulo = "Lançamentos Financeiros";
		
		Zend_Session::start();
		
	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	
	public function addRecorrenteAction(){
		
		$this->validaAcesso('gerenciar_financeiro_lancamentos');
		
		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		$dbLancamentosFinanceiros = new Application_Model_DbTable_LancamentosFinanceiros();
		$dbItensFinanceiros = new Application_Model_DbTable_ItensFinanceiros();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrItensFinanceiros = $dbItensFinanceiros->getItem($arrFiltro);
		
		$arrFiltro['id'] = $this->_getParam('id');
		
		$arrDadosItensFinanceiros = $dbItensFinanceiros->getItem($arrFiltro);
		

		if($this->getRequest()->isPost()){
			
			$dados = $_POST;
			
			unset($dados['id_financeiro_grupo']);
			
			$dataTmp = explode("/",$dados['data_lancamento']);
			$dados['data_lancamento'] = implode("-",array_reverse($dataTmp));
			
			$dados['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dados['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			$dados['valor'] = str_replace(",", ".", $dados['valor']);
			
			if($dbLancamentosFinanceiros->insert($dados)){
   
				$this->view->mensagem = "Item Financeiro cadastrado com sucesso!";
			   
			}else{
			   
				$this->view->mensagem = "Erro ao cadastrar Item Financeiro.";
			   
			}

		}
		
		$this->view->id = $this->_getParam('id');
		$this->view->itens = $arrItensFinanceiros;
		$this->view->dadosItens = $arrDadosItensFinanceiros[0];
	
	}
	
	
	public function addAction(){
		
		$this->validaAcesso('gerenciar_financeiro_lancamentos');
		
		$dbItensFinanceiros = new Application_Model_DbTable_ItensFinanceiros();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrItensFinanceiros = $dbItensFinanceiros->_get($arrFiltro);
		
		$this->view->arrItens = $arrItensFinanceiros;
		
		$dbLancamentoFinanceiro = new Application_Model_DbTable_LancamentosFinanceiros();
		
		if($this->getRequest()->isPost()){	
			
			$dados = $_POST;
			
			unset($dados['id_financeiro_grupo']);
			
			$dataTmp = explode("/",$dados['data_lancamento']);
			$dados['data_lancamento'] = implode("-",array_reverse($dataTmp));
			
			$dados['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dados['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			$dados['valor'] = str_replace(",", ".", $dados['valor']);
			
			if($dbLancamentoFinanceiro->insert($dados)){
   
				$this->view->mensagem = "Item Financeiro cadastrado com sucesso!";
			   
			}else{
			   
				$this->view->mensagem = "Erro ao cadastrar Item Financeiro.";
			   
			}
			
		}
	
	}
	
	public function listaAction(){
	
		$this->validaAcesso('listar_financeiro_lancamentos');
		
		$dbLancamentosFinanceiros = new Application_Model_DbTable_LancamentosFinanceiros();
		$dbItensFinanceiros = new Application_Model_DbTable_ItensFinanceiros();
		
		$this->view->itens = $dbItensFinanceiros->getItensDistintos($_SESSION['sessionUser']['id_empresa']);
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arrFiltro['data_inicial'] = @date("Y")."-".@date("m")."-01";
		$data = @date("Y-m-d",mktime(0, 0, 0, @date("m")+1, 0, @date("Y")));
		$arrFiltro['data_final'] = $data;
		
		if($this->getRequest()->isPost()){
			
			if($_POST['baixado']){
			
				$arrFiltro['baixado'] = $_POST['baixado'];
			
			}
			
			if($_POST['item']){
			
				$arrFiltro['item'] = $_POST['item'];
			
			}
			
			if($_POST['data_inicial'] && $_POST['data_final']){
				
				$dataTmp = explode("/",$_POST['data_inicial']);
				$_POST['data_inicial'] = implode("-",array_reverse($dataTmp));
				
				$dataTmp = explode("/",$_POST['data_final']);
				$_POST['data_final'] = implode("-",array_reverse($dataTmp));
				
				
				$arrFiltro['data_inicial'] = $_POST['data_inicial'];
				$arrFiltro['data_final'] = $_POST['data_final'];
			
			}elseif($_POST['data_inicial']){
			
				$dataTmp = explode("/",$_POST['data_inicial']);
				$_POST['data_inicial'] = implode("-",array_reverse($dataTmp));
			
				$arrFiltro['data_inicial'] = $_POST['data_inicial'];
				$arrFiltro['data_final'] = @date("Y-m-d");
			
			}elseif($_POST['data_final']){
				
				$dataTmp = explode("/",$_POST['data_final']);
				$_POST['data_final'] = implode("-",array_reverse($dataTmp));
				
				$arrFiltro['data_final'] = $_POST['data_final'];
				
			}
		
		}
		
		if($this->_getParam('erro')){
			
			$this->view->mensagem = "Erro ao deletar Grupo. Existem referências a este registro.";
		
		}
	
		$arrLancamentosFinanceiros = $dbLancamentosFinanceiros->_get($arrFiltro);
		
		$this->view->lancamentosFinanceiros = $arrLancamentosFinanceiros;

	}
	
	/*public function listaAction(){
	
		$this->validaAcesso('lancamentosFinanceiros');
		
		$dbLancamentosFinanceiros = new Application_Model_DbTable_LancamentosFinanceiros();
		
		if($this->getRequest()->isPost()){
		
			$arr['parcial'] = true;
			$arr['descricao'] = $this->_getParam('descricao');
			
			$arrLancamentosFinanceiros = $dbLancamentosFinanceiros->_get($arr);
			
		}else{
		
			$arrLancamentosFinanceiros = $dbLancamentosFinanceiros->fetchAll();
			
		}
		
		$this->view->lancamentosFinanceiros = $arrLancamentosFinanceiros;
	
	}*/
	
	public function delAction(){
		
		$this->validaAcesso('gerenciar_financeiro_lancamentos');
		
		$dbLancamentosFinanceiros = new Application_Model_DbTable_LancamentosFinanceiros();
		
		try{
		
			$dbLancamentosFinanceiros->delete("id = " . $this->_getParam('id'));
			$this->_helper->redirector->gotoUrl("lancamentos-financeiros/lista");
			
		}catch(Exception $e){
		
			//$this->view->mensagem = "Erro ao deletar Lançamento. Existem referências a este registro.";
		
		}
		
	}
	
	public function edtAction(){
	
		$this->validaAcesso('gerenciar_financeiro_lancamentos');
		
		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrGruposFinanceiros = $dbGruposFinanceiros->_get($arrFiltro);
		
		$this->view->grupos = $arrGruposFinanceiros;

		$dbLancamentosFinanceiros = new Application_Model_DbTable_LancamentosFinanceiros();
		
		$this->view->id = $this->_getParam('id');
		
		$arrFiltro['id'] = $this->_getParam('id');
		//var_export($arrFiltro);exit;
		$dados = $dbLancamentosFinanceiros->_get($arrFiltro);
		
		$dataTmp = explode("-",$dados[0]['data_lancamento']);
		$dados[0]['data_lancamento'] = implode("/",array_reverse($dataTmp));
		
		$dbItensFinanceiros = new Application_Model_DbTable_ItensFinanceiros();
			
		//$arrFiltro['id_financeiro_grupo'] = $dados[0]['idGrupo'];
		
		$arrItensFinanceiros = $dbItensFinanceiros->_get($arrFiltro);
		
		$this->view->itens = $arrItensFinanceiros;
		
		$this->view->lancamentoFinanceiro = $dados[0];
		
		if($this->getRequest()->isPost()){
			
			$dataTmp = explode("/",$_POST['data_lancamento']);
			$_POST['data_lancamento'] = implode("-",array_reverse($dataTmp));
			
			unset($_POST['id_financeiro_grupo']);
			
			$_POST['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$_POST['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			$_POST['valor'] = str_replace(".","",$_POST['valor']);
			$_POST['valor'] = str_replace(",",".",$_POST['valor']);
			
			$dbLancamentosFinanceiros->update($_POST, "id = " . $this->_getParam('id'));
			
			$this->_helper->redirector->gotoUrl("lancamentos-financeiros/lista");
		
		}
	
	}

	public function ajaxAction(){

		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');

		if($this->_getParam('fn') == 'getItens'){
			
			$idGrupo = $this->_getParam('id');
			
			$dbItensFinanceiros = new Application_Model_DbTable_ItensFinanceiros();
			
			$arrFiltro['id_financeiro_grupo'] = $idGrupo;
			
			$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
			$arrItensFinanceiros = $dbItensFinanceiros->_get($arrFiltro);
			
			echo "<option value=\"0\">SELECIONE</option>";
			
			foreach($arrItensFinanceiros as $itens){
			
				echo "<option value=\"" . $itens['id'] . "\" label = \"" . $itens['item'] ."\">  ". $itens['item'] ." </option>";
			
			}
			
		}
		
	}

}

?>