<?php

header("Content-Type: text/html; charset=UTF-8",true);

class GarantiasController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Garantias";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function addAction(){
		
		$this->validaAcesso('gerenciar_garantias');
		
		$dbGarantia = new Application_Model_DbTable_Garantias();
		
		$dbFornecedores = new Application_Model_DbTable_Fornecedores();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrFornecedores = $dbFornecedores->_get($arr);
		
		$this->view->fornecedores = $arrFornecedores;
		
		if($this->getRequest()->isPost()){
			
			$dados = $_POST;
			$dados['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dados['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			unset($dados['find_placa']);
			unset($dados['placa']);
			unset($dados['marca']);
			unset($dados['ano_modelo']);
			unset($dados['cor']);
			unset($dados['valor_venda']);
			unset($dados['valor_aquisicao']);
			unset($dados['custos_transferencia']);
			unset($dados['dias_garantia']);
			unset($dados['km_garantia']);
			unset($dados['modelo']);
			
			//var_export($dados);exit;
			
			if($dados['data_entrada']){
			
				$dataTmp = explode("/",$dados['data_entrada']);
				$dados['data_entrada'] = implode("-",array_reverse($dataTmp));				
			
			}
			
			if($dados['data_saida']){

				$dataTmp = explode("/",$dados['data_saida']);
				$dados['data_saida'] = implode("-",array_reverse($dataTmp));
				
			}
			
			if($dados['data_cancelamento']){

				$dataTmp = explode("/",$dados['data_cancelamento']);
				$dados['data_cancelamento'] = implode("-",array_reverse($dataTmp));
				
			}
			
			//var_export($dados);exit;
			
			if($dbGarantia->insert($dados)){
   
				$this->view->mensagem = "Garantia cadastrada com sucesso!";
			   
			}else{
			   
				$this->view->mensagem = "Erro ao cadastrar o Garantia.";
			   
			}
			
		}
	
	}
	
	public function listaAction(){
	
		$this->validaAcesso('listar_garantias');
		
		$dbGarantia = new Application_Model_DbTable_Garantias();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arr['data_inicial'] = @date("Y")."-".@date("m")."-01";
		$data = @date("Y-m-d",mktime(0, 0, 0, @date("m")+1, 0, @date("Y")));
		$arr['data_final'] = $data;
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

		$arrGarantias = array();
		
		if($this->getRequest()->isPost()){
			
			if(isset($_POST['id_veiculo']) && $_POST['id_veiculo'] != ""){
				$arrFiltro['id_veiculo'] = $_POST['id_veiculo'];
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
				
				$arrFiltro['data_final'] = "1900-01-01";
				$arrFiltro['data_final'] = $_POST['data_final'];
				
			}
			
			$arrGarantias = $dbGarantia->_get($arrFiltro);

		}else{
			
			$arrGarantias = $dbGarantia->_get($arr);
			
		}
		
		if(count($arrGarantias) > 0){
			$this->view->garantias = $arrGarantias;
		}
	
	}
	
	public function delAction(){

		$this->validaAcesso('gerenciar_garantias');
	
		$dbGarantias = new Application_Model_DbTable_Garantias();
		
		$dbGarantias->delete("id = " . $this->_getParam('id'));
		
		$this->_helper->redirector->gotoUrl("garantias/lista");
	
	}
	
	public function edtAction(){
	
		$this->validaAcesso('gerenciar_garantias');

		$dbGarantias = new Application_Model_DbTable_Garantias();
		
		$this->view->id = $this->_getParam('id');
		
		$dados = $dbGarantias->fetchAll("id = " . $this->_getParam('id'));
		
		$this->view->garantia = $dados[0];
		
		$idVeiculo = $dados[0]['id_veiculo'];

		$arr['id'] = $idVeiculo;
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		
		$arrVeiculos = $dbVeiculos->_get($arr);
		
		$this->view->veiculo = $arrVeiculos[0];
		
		$dbFornecedores = new Application_Model_DbTable_Fornecedores();
		
		$arrF['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrFornecedores = $dbFornecedores->_get($arrF);
		
		$this->view->fornecedores = $arrFornecedores;
		
		//var_export($arrVeiculos);exit;
		
		if($this->getRequest()->isPost()){
		
			$_POST['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$_POST['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			if($_POST['data_entrada']){
			
				$dataTmp = explode("/",$_POST['data_entrada']);
				$_POST['data_entrada'] = implode("-",array_reverse($dataTmp));				
			
			}
			
			if($_POST['data_saida']){

				$dataTmp = explode("/",$_POST['data_saida']);
				$_POST['data_saida'] = implode("-",array_reverse($dataTmp));
				
			}
			
			if($_POST['data_cancelamento']){

				$dataTmp = explode("/",$_POST['data_cancelamento']);
				$_POST['data_cancelamento'] = implode("-",array_reverse($dataTmp));
				
			}
			
			unset($_POST['find_placa']);
			unset($_POST['placa']);
			unset($_POST['marca']);
			unset($_POST['ano_modelo']);
			unset($_POST['cor']);
			unset($_POST['valor_venda']);
			unset($_POST['valor_aquisicao']);
			unset($_POST['custos_transferencia']);
			unset($_POST['dias_garantia']);
			unset($_POST['km_garantia']);
			unset($_POST['modelo']);
		
			$dbGarantias->update($_POST, "id = " . $this->_getParam('id'));
			
			$this->_helper->redirector->gotoUrl("garantias/lista");
		
		}
	
	}

}

?>
