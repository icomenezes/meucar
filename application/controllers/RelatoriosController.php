<?php

header("Content-Type: text/html; charset=UTF-8",true);

class RelatoriosController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Relat&oacute;rios";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}


	public function ajaxAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
	
		if($this->_getParam('fn') == 'folha_pagamento'){

			$dbUsuarios = new Application_Model_DbTable_Usuarios();
			$arrFuncionarios = $dbUsuarios->usuariosNegociacoes($_POST);

			foreach($arrFuncionarios as $funcionarios){

				echo $funcionarios['nome']." ".$funcionarios['data_demissao'];
				echo "<br>";

			}


			echo "<pre>";
			var_export($arrFuncionarios);
			echo "</pre>";


		}elseif($this->_getParam('fn') == 'compras'){


			$_POST['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$_POST['id_negociacao_compra'] = 1;
			$_POST['data_inicial_abertura'] = implode("-", array_reverse(explode("/", $_POST['data_inicial_abertura'])));
			$_POST['data_final_abertura'] = implode("-", array_reverse(explode("/", $_POST['data_final_abertura'])));
			$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
			$arrCompras = $dbNegociacoes->_getCompra($_POST);

			$totalValor = 0;
			$totalFipe = 0;
			$qtd = count($arrCompras);

			$arrOrigem = array();

			$alter = true;


			$strTable = '<table class="table">
							<tr>
								<th></th>
								<th>Cliente</th>
								<th>Comprador</th>
								<th>Telefone</th>
								<th>Profissão</th>
								<th>Bairro</th>
								<th>Cidade</th>
								<th>Origem</th>
								<th>Veículo</th>
								<th>Ano</th>
								<th>Cor</th>
								<th>KM</th>
								<th>Finalidade</th>
								<th>FIPE</th>
								<th>Valor</th>
							</tr>';

			foreach ($arrCompras as $key => $compras) {

				$totalValor += $compras['valor_aquisicao'];
				$totalFipe += $compras['preco'];


				$color = "#fff";
				$alter = !$alter;
				if($alter){
					$color = "#eee";
				}

				$arrOrigem[$compras['origem']] += 1;

				
				$strTable .= '<tr style="background-color: '.$color.';">
								<td>'.($key+1).'</td>
								<td><a target="_blank" href="/clientes/edt/id/'.$compras['id_cliente'].'">'.$compras['nome'].'</a></td>
								<td>'.$compras['nomeUsuario'].'</td>
								<td>'.$compras['tel1'].'</td>
								<td>'.$compras['cargo'].'</td>
								<td>'.$compras['bairro'].'</td>
								<td>'.$compras['cidade'].'</td>
								<td>'.$compras['origem'].'</td>
								<td>'.$compras['modelo'].'</td>
								<td>'.$compras['ano_modelo'].'</td>
								<td>'.$compras['cor'].'</td>
								<td>'.$compras['km'].'</td>
								<td>'.$compras['finalidade'].'</td>
								<td>R$ '.number_format($compras['preco'], 2, ",", ".").'</td>
								<td>R$ '.number_format($compras['valor_aquisicao'], 2, ",", ".").'</td>
							  </tr>';


			}


			$strTable .= '<tr style="background-color: #ccc;"><td colspan="13" style="text-align: right;"><strong>MÉDIA</strong></td><td><strong>R$ '.number_format($totalFipe/$qtd, 2, ",", ".").'</strong></td><td><strong>R$ '.number_format($totalValor/$qtd, 2, ",", ".").'</strong></td></tr>';
			$strTable .= '<tr style="background-color: #bbb;"><td colspan="13" style="text-align: right;"><strong>TOTAL</strong></td><td><strong>R$ '.number_format($totalFipe, 2, ",", ".").'</strong></td><td><strong>R$ '.number_format($totalValor, 2, ",", ".").'</strong></td></tr>';


			$strTable .= '</table>';

			$strTOrigem = '<table class="table" style="width:30%;">
							<tr>
								<th>Origem</th>
								<th>Quantidade</th>
								<th>%</th>
							</tr>';


			$qtdOrigem = count($arrCompras);
			$alter = true;

			foreach ($arrOrigem as $key => $origem) {
				$color = "#fff";
				$alter = !$alter;
				if($alter){
					$color = "#eee";
				}
				$strTOrigem .= '<tr style="background-color: '.$color.';">
								<td>'.$key.'</td>
								<td>'.$arrOrigem[$key].'</td>
								<td>'.number_format((($arrOrigem[$key]/$qtdOrigem)*100), 2, ",", ".").'</td>
							  </tr>';
			}


			$strTOrigem .= '</table>';

			$strTable .= $strTOrigem;


			echo $strTable;

			// echo "<pre>";
			// var_export($arrCompras);
			// echo "</pre>";
		}
	
	}
	
	public function folhaPagamentoAction(){
	}

	public function comprasAction(){

		$this->view->data_inicial = @date("d/m/Y", mktime(0,0,0, @date("m"), 1, @date("Y")));
		$this->view->data_final = @date("d/m/Y", mktime(0,0,0, @date("m")+1, 0, @date("Y")));

	}

	public function gerarXlsAction(){
	
		//$this->validaAcesso('relatorios');
	
		$this->view->relatorio = $_POST['relatorio'];
		$this->view->dataInicial = $_POST['data_inicial'];
		$this->view->dataFinal = $_POST['data_final'];
		$this->view->tipoRelatorio =  $_POST['tipo_relatorio'];
	
	}

}