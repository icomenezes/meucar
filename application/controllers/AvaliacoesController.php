<?php

class AvaliacoesController extends Zend_Controller_Action {

	public function init() {

		$this->view->titulo = "Avaliações";
		Zend_Session::start();
		
		$layout = $this->_helper->layout();
		$layout->setLayout('layout');
		
	}



	public function buscaParametrosAction(){

		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		$dbParametros = new Application_Model_DbTable_ParametrosAvaliacoes();
		$arrParametros = $dbParametros->getParametros($_SESSION['sessionUser']['id_empresa']);

		echo json_encode($arrParametros[0]);

	}



	public function edtAction(){

		$layout = $this->_helper->layout();
		$layout->setLayout('layout');

	}

	public function fotoAction(){

		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		$this->view->path = $this->_getParam('path');

	}


	public function indexAction(){

		$layout = $this->_helper->layout();
		$layout->setLayout('layout');

	}

   
	public function addAction(){
			
		$dbAvaliacoes = new Application_Model_DbTable_ParametrosAvaliacoes();
		$dbUsuarios = new Application_Model_DbTable_Usuarios();

		$arrUsuarios = $dbUsuarios->getUsuariosPorEmpresaAvaliacoes();
		
		if($this->getRequest()->isPost()){

			$_POST["checkbox_valor_tabela_limite"] = ($_POST["checkbox_valor_tabela_limite"]) ? 1 : 0 ;
			$_POST["checkbox_margem_compra"] = ($_POST["checkbox_margem_compra"]) ? 1 : 0 ;
			$_POST["checkbox_margem_troca_estoque"] = ($_POST["checkbox_margem_troca_estoque"]) ? 1 : 0 ;
			$_POST["checkbox_margem_compra_acima_limite"] = ($_POST["checkbox_margem_compra_acima_limite"]) ? 1 : 0 ;
			$_POST["checkbox_margem_troca_estoque_acima_limite"] = ($_POST["checkbox_margem_troca_estoque_acima_limite"]) ? 1 : 0 ;
			$_POST["checkbox_valor_fipe_veiculo_repasse"] = ($_POST["checkbox_valor_fipe_veiculo_repasse"]) ? 1 : 0 ;
			$_POST["checkbox_margem_repasse"] = ($_POST["checkbox_margem_repasse"]) ? 1 : 0 ;
			$_POST["checkbox_limite_kilometragem"] = ($_POST["checkbox_limite_kilometragem"]) ? 1 : 0 ;
			$_POST["checkbox_kilometragem_ano"] = ($_POST["checkbox_kilometragem_ano"]) ? 1 : 0 ;
			$_POST["checkbox_martelinho"] = ($_POST["checkbox_martelinho"]) ? 1 : 0 ;
			$_POST["checkbox_pincelar"] = ($_POST["checkbox_pincelar"]) ? 1 : 0 ;
			$_POST["checkbox_pintar"] = ($_POST["checkbox_pintar"]) ? 1 : 0 ;
			$_POST["checkbox_funilaria"] = ($_POST["checkbox_funilaria"]) ? 1 : 0 ;
			$_POST["checkbox_trocar_pecas"] = ($_POST["checkbox_trocar_pecas"]) ? 1 : 0 ;
			$_POST["checkbox_farol"] = ($_POST["checkbox_farol"]) ? 1 : 0 ;
			$_POST["checkbox_lanterna"] = ($_POST["checkbox_lanterna"]) ? 1 : 0 ;
			$_POST["checkbox_vidro"] = ($_POST["checkbox_vidro"]) ? 1 : 0 ;
			$_POST["checkbox_retrovisor"] = ($_POST["checkbox_retrovisor"]) ? 1 : 0 ;
			$_POST["checkbox_roda"] = ($_POST["checkbox_roda"]) ? 1 : 0 ;
			$_POST["checkbox_vestigio_acidente"] = ($_POST["checkbox_vestigio_acidente"]) ? 1 : 0 ;
			$_POST["checkbox_motor"] = ($_POST["checkbox_motor"]) ? 1 : 0 ;
			$_POST["checkbox_caixa_direcao"] = ($_POST["checkbox_caixa_direcao"]) ? 1 : 0 ;
			$_POST["checkbox_cambio_manual"] = ($_POST["checkbox_cambio_manual"]) ? 1 : 0 ;
			$_POST["checkbox_cambio_automatico"] = ($_POST["checkbox_cambio_automatico"]) ? 1 : 0 ;
			$_POST["checkbox_suspensao"] = ($_POST["checkbox_suspensao"]) ? 1 : 0 ;
			$_POST["checkbox_embreagem"] = ($_POST["checkbox_embreagem"]) ? 1 : 0 ;
			$_POST["checkbox_freios"] = ($_POST["checkbox_freios"]) ? 1 : 0 ;
			$_POST["checkbox_escapamento"] = ($_POST["checkbox_escapamento"]) ? 1 : 0 ;
			$_POST["checkbox_eletrica"] = ($_POST["checkbox_eletrica"]) ? 1 : 0 ;
			$_POST["checkbox_ar_condicionado"] = ($_POST["checkbox_ar_condicionado"]) ? 1 : 0 ;
			$_POST["checkbox_luz_painel"] = ($_POST["checkbox_luz_painel"]) ? 1 : 0 ;
			$_POST["checkbox_aro_13"] = ($_POST["checkbox_aro_13"]) ? 1 : 0 ;
			$_POST["checkbox_aro_14"] = ($_POST["checkbox_aro_14"]) ? 1 : 0 ;
			$_POST["checkbox_aro_15"] = ($_POST["checkbox_aro_15"]) ? 1 : 0 ;
			$_POST["checkbox_aro_16"] = ($_POST["checkbox_aro_16"]) ? 1 : 0 ;
			$_POST["checkbox_aro_17"] = ($_POST["checkbox_aro_17"]) ? 1 : 0 ;
			$_POST["checkbox_aro_18"] = ($_POST["checkbox_aro_18"]) ? 1 : 0 ;
			$_POST["checkbox_off_road"] = ($_POST["checkbox_off_road"]) ? 1 : 0 ;
			$_POST["checkbox_faixa_couro"] = ($_POST["checkbox_faixa_couro"]) ? 1 : 0 ;
			$_POST["checkbox_faixa_tecido"] = ($_POST["checkbox_faixa_tecido"]) ? 1 : 0 ;
			$_POST["checkbox_manutencao"] = ($_POST["checkbox_manutencao"]) ? 1 : 0 ;
			$_POST["checkbox_granizo"] = ($_POST["checkbox_granizo"]) ? 1 : 0 ;
			$_POST["checkbox_mercado_normal"] = ($_POST["checkbox_mercado_normal"]) ? 1 : 0 ;
			$_POST["checkbox_mercado_ruim"] = ($_POST["checkbox_mercado_ruim"]) ? 1 : 0 ;
			$_POST["checkbox_mercado_pessimo"] = ($_POST["checkbox_mercado_pessimo"]) ? 1 : 0 ;
			$_POST["checkbox_mercado_nao_sei"] = ($_POST["checkbox_mercado_nao_sei"]) ? 1 : 0 ;
			
			$_POST["data_atualizacao"] = @date('Y-m-d H:i:s');

			///var_export($_POST);

			foreach ($_POST as $key => $value) {
				$_POST[$key] = str_replace(".", "", $_POST[$key]);
		        $_POST[$key] = str_replace(",", ".", $_POST[$key]);
			}

			$dbAvaliacoes->edtIdEmpresa($_SESSION['sessionUser']['id_empresa'], $_POST);

		}

		$arrParametros = $dbAvaliacoes->getParametros($_SESSION['sessionUser']['id_empresa']);

		$this->view->arrParametros = $arrParametros[0];
		$this->view->arrUsuarios = $arrUsuarios;

	}

}
?>