<?php

header("Content-Type: text/html; charset=UTF-8",true);

class RevisoesController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Revis&otilde;es";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	
	public function addRevisoesAction(){
	
		$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
		$dbGarantias = new Application_Model_DbTable_Garantias();
	
		if($this->getRequest()->isPost()){
			
			$idFornecedor = $_POST['id_fornecedor'];
			
			unset($_POST['id_fornecedor']);
			
			foreach($_POST as $chave=>$valor){
				
				$dadosTemp = explode("_",$chave);
				
				if($dadosTemp[0] == "Ddata"){
				
					$dados[$dadosTemp[1]]['data'] = implode("-",array_reverse(explode("/",$valor)));
					$dadosGarantia[$dadosTemp[1]]['data_entrada'] = implode("-",array_reverse(explode("/",$valor)));
					$dadosGarantia[$dadosTemp[1]]['data_saida'] = implode("-",array_reverse(explode("/",$valor)));
					
				}
				
				if($dadosTemp[0] == "Ddescricao"){
				
					$dados[$dadosTemp[1]]['despesa'] = $valor;
					$dadosGarantia[$dadosTemp[1]]['descricao_defeito'] = $valor;
					$dadosGarantia[$dadosTemp[1]]['solucao'] = $valor;
					
				}
				
				if($dadosTemp[0] == "idveiculo"){
				
					$dados[$dadosTemp[1]]['id_veiculo'] = $valor;
					$dadosGarantia[$dadosTemp[1]]['id_veiculo'] = $valor;
					
				}
				
				if($dadosTemp[0] == "Dnf"){
				
					$dados[$dadosTemp[1]]['nf'] = $valor;
					
				}
				
				if($dadosTemp[0] == "Dgarantia"){
				
					$dados[$dadosTemp[1]]['dias_garantia'] = $valor;
					$dadosGarantia[$dadosTemp[1]]['dias_garantia'] = $valor;
					
				}
				
				if($dadosTemp[0] == "Dvalor"){
				
					$valor = str_replace(".", "", $valor);
					$valor = str_replace(",", ".", $valor);
				
					$dados[$dadosTemp[1]]['valor'] = $valor;
					$dadosGarantia[$dadosTemp[1]]['custo'] = $valor;
					
				}
				
				$dados[$dadosTemp[1]]['id_fornecedor'] = $idFornecedor;
				$dadosGarantia[$dadosTemp[1]]['id_fornecedor'] = $idFornecedor;
				
				if($dadosTemp[0] == "vendido"){
				
					$dados[$dadosTemp[1]]['vendido'] = $valor;
					$dadosGarantia[$dadosTemp[1]]['vendido'] = $valor;
					
				}
			
			}
			
			$qtdGarantias = 0;
			$qtdRevisoes = 0;

			foreach($dados as $arrDados){
			
				if($arrDados['vendido'] == 0){
				
					unset($arrDados['vendido']);
				
					if($dbDespesasVeiculos->add($arrDados)){
					
						$qtdRevisoes++;
					
					}
				
				}
			
			}
			
			foreach($dadosGarantia as $arrGarantia){
				
				if($arrGarantia['vendido'] == 1){
				
					unset($arrGarantia['vendido']);
				
					$arrGarantia['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
					$arrGarantia['hora_alteracao'] = @date("Y-m-d H:i:s");
				
					if($dbGarantias->add($arrGarantia)){
					
						$qtdGarantias++;
						
					}
				
				}
			
			}
			
			$this->view->mensagem = "Dados cadastrados com sucesso!<br>Revis&atilde;o: ".$qtdRevisoes."<br>Garantia: ".$qtdGarantias;
		
		}
	
	}

}
	
?>
