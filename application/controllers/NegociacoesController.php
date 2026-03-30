<?php

header("Content-Type: text/html; charset=UTF-8", true);

class NegociacoesController extends Zend_Controller_Action {

	public function init() {

		$this->view->titulo = "Negocia&ccedil;&otilde;es";

		Zend_Session::start();
	
	}

	public function validaAcesso($require){

		if (!in_array($require, $_SESSION['sessionUser']['permissoes'])) {
			
			$this->_helper->redirector->gotoUrl(URL ."/index/bad-access");
		
		}
		
	}
   
   /*
	public function notaFiscalEletronicaAction(){

		//$this->validaAcesso('gerenciar_vendas');
		
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$arrEmpresa = $dbEmpresas->_get(array("id_empresa"=>$_SESSION['sessionUser']['id_empresa']));
		
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		$xml = fopen("nfe/notaFiscalEletronica01.xml","w+");

		fwrite($xml,"<?xml version='1.0' encoding='utf-8'?>\n");

		$strNotaFiscal = "<nfeProc xmlns='http://www.portalfiscal.inf.br/nfe' versao='3.10'>\n";
		$strNotaFiscal .= "<NFe xmlns='http://www.portalfiscal.inf.br/nfe'>\n";
		
		
		$chave = "NFe";
		$chave .= "35";
		$chave .= @date("y").@date("m");
		$chave .= str_replace("/","",str_replace("-","",str_replace(".","",$arrEmpresa[0]['cnpj'])));
		$chave .= "55";
		$chave .= "001";
		
		$
		
		
		$strNotaFiscal .= "<infNFe versao='3.10' Id='".$chave."'>\n";
		$strNotaFiscal .= "<ide>\n";
		$strNotaFiscal .= "<cUF>35</cUF>\n";
		$strNotaFiscal .= "<cNF>00104759</cNF>\n";

		$strNotaFiscal .= "</ide>\n";
		$strNotaFiscal .= "</infNFe>\n";
		$strNotaFiscal .= "</NFe>\n";
		$strNotaFiscal .= "</nfeProc>\n";
		
		fwrite($xml,$strNotaFiscal);

		fclose($xml);
	
	}
   */
   
	public function edtCompraAction(){

		$this->validaAcesso('gerenciar_vendas');

		$dbFinanceira = new Application_Model_DbTable_Financeiras();
		$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
		$dbTrocasNegociacoes = new Application_Model_DbTable_TrocasNegociacoes();
		$dbRecebimentosNegociacoes = new Application_Model_DbTable_RecebimentosNegociacoes();
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbUsuarios = new Application_Model_DbTable_Usuarios();
		

		if ($this->getRequest()->isPost()) {

			$idNegociacao = $this->_getParam('id');

			$arrDados['id_veiculo'] = $_POST['id_veiculo'];

			if ($_POST['id_gerente']) {

				$arrDados['id_gerente'] = $_POST['id_gerente'];
			
			} else {

            $arrDados['id_gerente'] = null;
         }

         if ($_POST['id_supervisor']) {

            $arrDados['id_supervisor'] = $_POST['id_supervisor'];
         } else {

            $arrDados['id_supervisor'] = null;
         }

         $arrDados['id_cliente'] = $_POST['id_cliente'];

         if ($_POST['id_financeira']) {

            $arrDados['id_financeira'] = $_POST['id_financeira'];
         }

         if ($_POST['data_abertura'] != '') $arrDados['data_abertura'] = $_POST['data_abertura'];
         if ($_POST['data_concretizacao'] != '') $arrDados['data_concretizacao'] = $_POST['data_concretizacao'];
         if ($_POST['data_cancelamento'] != '') $arrDados['data_cancelamento'] = $_POST['data_cancelamento'];
         if ($_POST['data_entrega_veiculo'] != '') $arrDados['data_entrega_veiculo'] = $_POST['data_entrega_veiculo'];
         $arrDados['km_entrega_veiculo'] = $_POST['km_entrega_veiculo'];
         if ($_POST['data_termino_garantia'] != '') $arrDados['data_termino_garantia'] = $_POST['data_termino_garantia'];
         if ($_POST['data_recebimento_veiculo'] != '') $arrDados['data_recebimento_veiculo'] = $_POST['data_recebimento_veiculo'];
         $arrDados['km_recebimento_veiculo'] = $_POST['km_recebimento_veiculo'];
         $arrDados['valor_base_calculo'] = $_POST['valor_base_calculo'];
         $arrDados['comissao_vendedor'] = $_POST['comissao_vendedor_real'];
         $arrDados['comissao_gerente'] = $_POST['comissao_gerente_real'];
         $arrDados['comissao_supervisor'] = $_POST['comissao_supervisor_real'];
         $arrDados['valor_financiado'] = $_POST['valor_financiado'];
         $arrDados['tac'] = $_POST['tac'];
         $arrDados['coeficiente_financeira'] = $_POST['coeficiente_financeira'];
         $arrDados['numero_prestacoes'] = $_POST['numero_prestacoes'];
         $arrDados['imposto_financeira'] = $_POST['imposto_financeira'];
         $arrDados['valor_prestacoes'] = $_POST['valor_prestacoes'];
         $arrDados['valor_despachante'] = $_POST['valor_despachante'];
         $arrDados['valor_venda'] = $_POST['valor_venda'];
         $arrDados['custos_transferencia'] = $_POST['custos_transferencia'];
         $arrDados['dias_garantia'] = $_POST['dias_garantia'];
         $arrDados['km_garantia'] = $_POST['km_garantia'];
         $arrDados['obs'] = $_POST['obs'];
         $arrDados['obs_interna'] = $_POST['obs_interna'];
         $arrDados['retorno_financeira'] = $_POST['retorno_financeira'];
         $arrDados['aprovada'] = $_POST['aprovada'];
         $arrDados['id_despachante'] = $_POST['id_despachante'];
         $arrDados['forma_pagamento_despachante'] = $_POST['forma_pagamento_despachante'];

        if ($_POST['aprovada'] == 0) {

            $arrDados['aprovada'] = $_POST['aprovada'];
         
		 }

         foreach (array('data_abertura', 'data_concretizacao', 'data_cancelamento', 'data_entrega_veiculo', 'data_termino_garantia', 'data_recebimento_veiculo') as $campoData) {
            if (!isset($arrDados[$campoData]) || $arrDados[$campoData] == '') {
               unset($arrDados[$campoData]);
               continue;
            }
            $dataTmp = explode(" ", $arrDados[$campoData]);
            $dataTmp2 = explode("/", $dataTmp[0]);
            if ($dataTmp2[0] == '00' || (isset($dataTmp2[2]) && $dataTmp2[2] == '0000')) {
               unset($arrDados[$campoData]);
            } else {
               $arrDados[$campoData] = implode("-", array_reverse($dataTmp2));
               $arrDados[$campoData] = $arrDados[$campoData] . " " . $dataTmp[1];
            }
         }

         foreach (array('tac', 'coeficiente_financeira', 'valor_despachante', 'valor_base_calculo', 'valor_financiado', 'retorno_financeira', 'numero_prestacoes', 'valor_prestacoes', 'comissao_vendedor', 'comissao_gerente', 'comissao_supervisor', 'imposto_financeira', 'custos_transferencia', 'valor_venda', 'km_entrega_veiculo', 'km_recebimento_veiculo', 'dias_garantia', 'km_garantia') as $campo) {
            if (isset($arrDados[$campo]) && $arrDados[$campo] === '') {
               $arrDados[$campo] = 0;
            }
         }

         if (isset($arrDados['id_despachante']) && $arrDados['id_despachante'] === '') {
            $arrDados['id_despachante'] = null;
         }

         $arrDados['valor_base_calculo'] = str_replace(".", "", $arrDados['valor_base_calculo']);
         $arrDados['valor_base_calculo'] = str_replace(",", ".", $arrDados['valor_base_calculo']);

         $arrDados['valor_financiado'] = str_replace(".", "", $arrDados['valor_financiado']);
         $arrDados['valor_financiado'] = str_replace(",", ".", $arrDados['valor_financiado']);

         $arrDados['tac'] = str_replace(".", "", $arrDados['tac']);
         $arrDados['tac'] = str_replace(",", ".", $arrDados['tac']);

         //$arrDados['coeficiente_financeira'] = str_replace(".","",$arrDados['coeficiente_financeira']);
         $arrDados['coeficiente_financeira'] = str_replace(",", ".", $arrDados['coeficiente_financeira']);

         $arrDados['imposto_financeira'] = str_replace(".", "", $arrDados['imposto_financeira']);
         $arrDados['imposto_financeira'] = str_replace(",", ".", $arrDados['imposto_financeira']);

         $arrDados['valor_prestacoes'] = str_replace(".", "", $arrDados['valor_prestacoes']);
         $arrDados['valor_prestacoes'] = str_replace(",", ".", $arrDados['valor_prestacoes']);

         $arrDados['valor_venda'] = str_replace(".", "", $arrDados['valor_venda']);
         $arrDados['valor_venda'] = str_replace(",", ".", $arrDados['valor_venda']);

         $arrDados['custos_transferencia'] = str_replace(".", "", $arrDados['custos_transferencia']);
         $arrDados['custos_transferencia'] = str_replace(",", ".", $arrDados['custos_transferencia']);

         $arrDados['comissao_vendedor'] = str_replace(".", "", $arrDados['comissao_vendedor']);
         $arrDados['comissao_vendedor'] = str_replace(",", ".", $arrDados['comissao_vendedor']);

         $arrDados['comissao_gerente'] = str_replace(".", "", $arrDados['comissao_gerente']);
         $arrDados['comissao_gerente'] = str_replace(",", ".", $arrDados['comissao_gerente']);

         $arrDados['comissao_supervisor'] = str_replace(".", "", $arrDados['comissao_supervisor']);
         $arrDados['comissao_supervisor'] = str_replace(",", ".", $arrDados['comissao_supervisor']);

         //$arrDados['valor_despachante'] = str_replace(".","",$arrDados['valor_despachante']);
         $arrDados['valor_despachante'] = str_replace(",", ".", $arrDados['valor_despachante']);


         unset($arrDados['origem']);

         $dbNegociacoes->update($arrDados, 'id = ' . $_POST['id']);

         if($arrDados['id_cliente'] && $_POST['origem']){
            $dbClientes = new Application_Model_DbTable_Clientes();
            $dbClientes->edt($arrDados['id_cliente'], array('origem'=>$_POST['origem'])); 
         }


         //$dbVeiculos->update(array('temp_troca' => 0, 'id_negociacao_troca' => null), "id_empresa = " . $_SESSION['sessionUser']['id_empresa'] . " AND id_negociacao_troca = " . $idNegociacao . " AND temp_troca = 1");
         $dbVeiculos->update(array('id_negociacao_compra' => null), "id_negociacao_compra = " . $idNegociacao);


         // if ($_POST['id_veiculo_troca'] != ""){

         //    if ($_POST['aprovada'] == "-1") {

         //       $arrDadosVeiculos['temp_troca'] = 0;
         //       $arrDadosVeiculos['id_negociacao_troca'] = null;

         //       $dbVeiculos->edt($_POST['id_veiculo_troca'], $arrDadosVeiculos);
			   
         //    } else {

         //       $arrDadosVeiculos['temp_troca'] = 1;
         //       $arrDadosVeiculos['id_negociacao_troca'] = $idNegociacao;

         //       $dbVeiculos->edt($_POST['id_veiculo_troca'], $arrDadosVeiculos);
         //    }
         // }

         if ($_POST['aprovada'] == "-1") {

            //$arrDadosVeiculos['vendido'] = 0;
            $arrDadosVeiculos['id_negociacao_compra'] = null;
            $dbVeiculos->edt($_POST['id_veiculo'], $arrDadosVeiculos);
        
		} else {

            $arrDadosVeiculos['id_negociacao_compra'] =  $idNegociacao;
            $dbVeiculos->update( $arrDadosVeiculos, "id = ".$_POST['id_veiculo']);

            //$dbVeiculos->update(array('vendido' => 1, 'id_negociacao' => $idNegociacao, 'data_termino_revisao' => $_POST['data_termino_revisao'], 'valor_venda' => $arrDados['valor_venda']), "id = " . $_POST['id_veiculo']);
         
		 }

         foreach ($_POST as $chave => $valor) {

            $pagamento = explode("_", $chave);

            if ($pagamento[1] <= 0) {

               if ($pagamento[0] == "Rforma") {

                  $arrPagamentos[$pagamento[1]]['forma'] = $valor;
                  $arrPagamentos[$pagamento[1]]['id_negociacao'] = $_POST['id'];
               }

               if ($pagamento[0] == "Rdata") {

                  $dataTmp2 = explode("/", $valor);
                  $valor = implode("-", array_reverse($dataTmp2));

                  $arrPagamentos[$pagamento[1]]['data'] = $valor;
               }

               if ($pagamento[0] == "Rnumero") {

                  $arrPagamentos[$pagamento[1]]['numero'] = $valor;
               }

               if ($pagamento[0] == "Rbanco") {

                  $arrPagamentos[$pagamento[1]]['banco'] = $valor;
               }

               if ($pagamento[0] == "Ragencia") {

                  $arrPagamentos[$pagamento[1]]['agencia'] = $valor;
               }

               if ($pagamento[0] == "Rcc") {

                  $arrPagamentos[$pagamento[1]]['cc'] = $valor;
               }

               if ($pagamento[0] == "Rvalor") {

                  $arrPagamentos[$pagamento[1]]['valor'] = $valor;
               }

               if ($pagamento[0] == "Rbaixado") {

                  $arrPagamentos[$pagamento[1]]['baixado'] = $valor;
               }
            } elseif ($pagamento[1] > 0) {

               if ($pagamento[0] == "Rforma") {

                  $arrPagamentosEdt[$pagamento[1]]['forma'] = $valor;
                  $arrPagamentosEdt[$pagamento[1]]['id'] = $pagamento[1];
               }

               if ($pagamento[0] == "Rdata") {

                  $dataTmp2 = explode("/", $valor);
                  $valor = implode("-", array_reverse($dataTmp2));

                  $arrPagamentosEdt[$pagamento[1]]['data'] = $valor;
               }

               if ($pagamento[0] == "Rnumero") {

                  $arrPagamentosEdt[$pagamento[1]]['numero'] = $valor;
               }

               if ($pagamento[0] == "Rbanco") {

                  $arrPagamentosEdt[$pagamento[1]]['banco'] = $valor;
               }

               if ($pagamento[0] == "Ragencia") {

                  $arrPagamentosEdt[$pagamento[1]]['agencia'] = $valor;
               }

               if ($pagamento[0] == "Rcc") {

                  $arrPagamentosEdt[$pagamento[1]]['cc'] = $valor;
               }

               if ($pagamento[0] == "Rvalor") {

                  $arrPagamentosEdt[$pagamento[1]]['valor'] = $valor;
               }

               if ($pagamento[0] == "Rbaixado") {

                  $arrPagamentosEdt[$pagamento[1]]['baixado'] = $valor;
               }
            }
         }

         if ($arrPagamentosEdt) {

            foreach ($arrPagamentosEdt as $pagamentoEdt) {

               $dbRecebimentosNegociacoes->update($pagamentoEdt, 'id = ' . $pagamentoEdt['id']);
            }
         }

         if ($arrPagamentos) {

            foreach ($arrPagamentos as $pagamento) {

               $dbRecebimentosNegociacoes->insert($pagamento);
            }
         }
      }


      $this->view->id_empresa = $_SESSION['sessionUser']['id_empresa'];

      $this->view->financeiras = $dbFinanceira->fetchAll("id_empresa = " . $_SESSION['sessionUser']['id_empresa'] . " AND tipo = 0");
      $this->view->despachante = $dbFinanceira->fetchAll("id_empresa = " . $_SESSION['sessionUser']['id_empresa'] . " AND tipo = 1");
      $this->view->arrGerentes = $dbUsuarios->getUsuarioComissao($_SESSION['sessionUser']['id_empresa'], 4);
      $this->view->arrSupervisor = $dbUsuarios->getUsuarioComissao($_SESSION['sessionUser']['id_empresa'], 9);



      $arrNegociacoes = $dbNegociacoes->getNegociacoes($this->_getParam('id'));

      //var_export($arrNegociacoes);

      $arrRecebimento = $dbRecebimentosNegociacoes->getRecebimentos($arrNegociacoes[0]['id']);

      $this->view->negociacao = $arrNegociacoes[0];
      $this->view->recebimentos = $arrRecebimento;
      $this->view->idNegociacao = $this->_getParam('id');
	  
   }
   
   
   public function listaComprasAction() {

      $this->validaAcesso('listar_vendas');

      $dbNegociacoes = new Application_Model_DbTable_Negociacoes();

      $arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
      $arr['data_inicial_abertura'] = @date("Y") . "-" . @date("m") . "-01";
      $data = @date("Y-m-d", mktime(0, 0, 0, @date("m") + 1, 0, @date("Y")));
      $arr['data_final_abertura'] = $data;
      $arr['lista'] = true;
	  $arr['id_negociacao_compra'] = 1;

      if ($_SESSION['sessionUser']['id_perfil'] == 3) {

         $arr['id_vendedor'] = $_SESSION['sessionUser']['id'];
      }

      if ($this->getRequest()->isPost()){

         $arr['parcial'] = true;
         $arr['nome'] = $this->_getParam('nome');
         $arr['placa'] = $this->_getParam('placa');
		 
		if($arr['placa']){
		
			$arrTemp = explode("-",$arr['placa']);
			
			if($arrTemp[1] == NULL && strlen($arr['placa']) < 8){
			
				$arr['placa'] = substr($arr['placa'],0,3)."-".substr($arr['placa'],3);
			
			}
		
		}
		
		 
         $arr['aprovada'] = $this->_getParam('aprovada');
         $arr['data_inicial_abertura'] = implode("-", array_reverse(explode("/", $this->_getParam('data_inicial'))));
         $arr['data_final_abertura'] = implode("-", array_reverse(explode("/", $this->_getParam('data_final'))));
      }

		$arrNegociacoes = $dbNegociacoes->_getCompra($arr);

		$this->view->negociacoes = $arrNegociacoes;

      // expose the search dates back to the view
      if(isset($arr['data_inicial_abertura']) && $arr['data_inicial_abertura']){
         $this->view->data_inicial = implode("/", array_reverse(explode("-", $arr['data_inicial_abertura'])));
      } else {
         $this->view->data_inicial = '';
      }
      if(isset($arr['data_final_abertura']) && $arr['data_final_abertura']){
         $this->view->data_final = implode("/", array_reverse(explode("-", $arr['data_final_abertura'])));
      } else {
         $this->view->data_final = '';
      }

      // passar resumo por vendedor/status para a view (inclui 'sem vendedor')
      $this->view->resumoVendedores = $dbNegociacoes->getResumoVendedores($arr);

   }
   
	public function addCompraAction(){

      $this->validaAcesso('gerenciar_vendas');

      $dbFinanceira = new Application_Model_DbTable_Financeiras();

      $this->view->id_empresa = $_SESSION['sessionUser']['id_empresa'];


		if ($this->getRequest()->isPost()) {

         $dbNegociacoes = new Application_Model_DbTable_Negociacoes();
         $dbRecebimentosNegociacoes = new Application_Model_DbTable_RecebimentosNegociacoes();
         $dbVeiculos = new Application_Model_DbTable_Veiculos();

         $_POST['id_usuario'] = $_SESSION['sessionUser']['id'];
         $_POST['id_vendedor'] = $_SESSION['sessionUser']['id'];

         
         unset($_POST['id_gerente']);
         unset($_POST['id_supervisor']);
         unset($_POST['find_cpf']);
         unset($_POST['find_placa']);
         unset($_POST['placa']);
         unset($_POST['numero_endereco']);
         unset($_POST['find_modelo_troca']);
         unset($_POST['valor_aquisicao']);
         unset($_POST['diferenca']);

         //ESCREVENDO NA TABELA NEGOCIACOES

         $dadosNegociacao = $_POST;
		
		 unset($dadosNegociacao['nome']);
         unset($dadosNegociacao['cpf']);
         unset($dadosNegociacao['rg']);
         unset($dadosNegociacao['nacionalidade']);
         unset($dadosNegociacao['endereco']);
         unset($dadosNegociacao['bairro']);
         unset($dadosNegociacao['complemento']);
         unset($dadosNegociacao['cidade']);
         unset($dadosNegociacao['estado']);
         unset($dadosNegociacao['cep']);
         unset($dadosNegociacao['tel1']);
         unset($dadosNegociacao['tel2']);
         unset($dadosNegociacao['cel']);
         unset($dadosNegociacao['email']);
         unset($dadosNegociacao['marca']);
         unset($dadosNegociacao['modelo']);
         unset($dadosNegociacao['ano_modelo']);
         unset($dadosNegociacao['cor']);
         unset($dadosNegociacao['id_veiculo_troca']);
         unset($dadosNegociacao['preco_troca']);
         unset($dadosNegociacao['km_troca']);
         unset($dadosNegociacao['renavam']);
         unset($dadosNegociacao['data_termino_revisao']);

        foreach ($dadosNegociacao as $k => $v) {

            $arrPartes = explode("_", $k);

            if ($arrPartes[0] == "Rforma" || $arrPartes[0] == "Rdata" || $arrPartes[0] == "Rnumero" || $arrPartes[0] == "Rbanco" || $arrPartes[0] == "Ragencia" || $arrPartes[0] == "Rcc" || $arrPartes[0] == "Rvalor" || $arrPartes[0] == "Rbaixado") {

               unset($dadosNegociacao[$k]);
            
			}
			
        }

         unset($dadosNegociacao['nome_vendedor']);
         unset($dadosNegociacao['comissao_vendedor_real']);
         unset($dadosNegociacao['nome_gerente']);
         unset($dadosNegociacao['comissao_gerente_real']);
         unset($dadosNegociacao['nome_supervisor']);
         unset($dadosNegociacao['comissao_supervisor_real']);
         unset($dadosNegociacao['multas']);

         foreach (array('data_concretizacao', 'data_cancelamento', 'data_entrega_veiculo', 'data_termino_garantia', 'data_recebimento_veiculo') as $campoData) {
            if (!isset($dadosNegociacao[$campoData]) || $dadosNegociacao[$campoData] == '') {
               $dadosNegociacao[$campoData] = '0000-00-00 00:00:00';
               continue;
            }
            $dataTmp = explode(" ", $dadosNegociacao[$campoData]);
            $dataTmp2 = explode("/", $dataTmp[0]);
            if ($dataTmp2[0] == '00' || (isset($dataTmp2[2]) && $dataTmp2[2] == '0000')) {
               $dadosNegociacao[$campoData] = '0000-00-00 00:00:00';
            } else {
               $dadosNegociacao[$campoData] = implode("-", array_reverse($dataTmp2));
               $dadosNegociacao[$campoData] = $dadosNegociacao[$campoData] . " " . $dataTmp[1];
            }
         }

         foreach (array('data_abertura') as $campoData) {
            if (!isset($dadosNegociacao[$campoData]) || $dadosNegociacao[$campoData] == '') {
               unset($dadosNegociacao[$campoData]);
               continue;
            }
            $dataTmp = explode(" ", $dadosNegociacao[$campoData]);
            $dataTmp2 = explode("/", $dataTmp[0]);
            if ($dataTmp2[0] == '00' || (isset($dataTmp2[2]) && $dataTmp2[2] == '0000')) {
               unset($dadosNegociacao[$campoData]);
            } else {
               $dadosNegociacao[$campoData] = implode("-", array_reverse($dataTmp2));
               $dadosNegociacao[$campoData] = $dadosNegociacao[$campoData] . " " . $dataTmp[1];
            }
         }
		 
		 $dadosNegociacao['compra'] = 1;

         foreach (array('tac', 'coeficiente_financeira', 'valor_despachante', 'valor_base_calculo', 'valor_financiado', 'retorno_financeira', 'numero_prestacoes', 'valor_prestacoes', 'comissao_vendedor', 'comissao_gerente', 'comissao_supervisor', 'imposto_financeira', 'custos_transferencia', 'valor_venda', 'km_entrega_veiculo', 'km_recebimento_veiculo', 'dias_garantia', 'km_garantia') as $campo) {
            if (!isset($dadosNegociacao[$campo]) || $dadosNegociacao[$campo] === '') {
               $dadosNegociacao[$campo] = 0;
            }
         }

         if (isset($dadosNegociacao['id_despachante']) && $dadosNegociacao['id_despachante'] === '') {
            $dadosNegociacao['id_despachante'] = null;
         }

      unset($dadosNegociacao['origem']);

      $idNegociacao = $dbNegociacoes->insert($dadosNegociacao);
       
       if($dadosNegociacao['id_cliente'] && $_POST['origem']){
         $dbClientes = new Application_Model_DbTable_Clientes();
         $dbClientes->edt($dadosNegociacao['id_cliente'], array('origem'=>$_POST['origem'])); 
       }




		 $arrVeiculo['id_negociacao_compra'] =  $idNegociacao;
		 
         $dbVeiculos->update( $arrVeiculo, "id = ".$_POST['id_veiculo']);

         //FIM ESCREVENDO NA TABELA NEGOCIACOES
         //ESCREVENDO NA TABELA RECEBIMENTOS NEGOCIACOES

         $dadosRecebimentos = $_POST;

        foreach ($dadosRecebimentos as $k => $v) {

            $arrPartes = explode("_", $k);

            if ($arrPartes[0] != "Rforma" && $arrPartes[0] != "Rdata" && $arrPartes[0] != "Rnumero" && $arrPartes[0] != "Rbanco" && $arrPartes[0] != "Ragencia" && $arrPartes[0] != "Rcc" && $arrPartes[0] != "Rvalor" && $arrPartes[0] != "Rbaixado") {

               unset($dadosRecebimentos[$k]);
			   
            } else {

               $arrFinalRecebimentos[$arrPartes[1]][substr($arrPartes[0], 1)] = $v;
            
			}
        }

        foreach ($arrFinalRecebimentos as $r){

            $r['id_negociacao'] = $idNegociacao;

            if ($r['data'] != "") {

               $dataTmp = explode(" ", $r['data']);
               $dataTmp2 = explode("/", $dataTmp[0]);
               $r['data'] = implode("-", array_reverse($dataTmp2));
               $r['data'] = $r['data'] . " " . $dataTmp[1];
            }

            $r['valor'] = str_replace(".", "", $r['valor']);
            $r['valor'] = str_replace(",", ".", $r['valor']);

            $dbRecebimentosNegociacoes->insert($r);
        }

        //FIM ESCREVENDO NA TABELA RECEBIMENTOS NEGOCIACOES


        $this->_helper->redirector->gotoUrl("negociacoes/edt-compra/id/".$idNegociacao);
		
		

	}
	  
}
   

	public function ajaxAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('no-layout');

		if ($this->_getParam('fn') == 'deleta_pagamento') {

			$dbRecebimentosNegociacoes = new Application_Model_DbTable_RecebimentosNegociacoes();

			if ($dbRecebimentosNegociacoes->del($this->_getParam('id_pagamento')) || $this->_getParam('id_pagamento') <= 0) {

				echo "Sucesso";
				
			} else {

				echo "Erro ao deletar pagamento!";
			}
		
		}elseif($this->_getParam('fn') == 'aprova_negociacao'){
		
			$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
			
			$arrDados['aprovada'] = 1;
			$arrDados['data_concretizacao'] = @date("Y-m-d H:i:s");
			$arrDados['data_cancelamento'] = "0000-00-00 00:00:00";
		
			if($this->_getParam('id_negociacao')){
		
				if($dbNegociacoes->update($arrDados, 'id = '.$this->_getParam('id_negociacao'))){
					
					$arrDadosVeiculo['vendido'] = 1;

               /*
               Foi retirado dia 09/11/2021/ pois a pedido do Guilherme.
               A data do termino da revisão será preenchida na tela de revisão em veículos/edt e na tela de preparação,
               quando o preparador clicar em salvar na última etapa(concuído).
					$arrDadosVeiculo['data_termino_revisao'] = @date("Y-m-d");
               */
					
					$dbVeiculos->update($arrDadosVeiculo, 'id = '.$this->_getParam('id_veiculo'));

					
					echo "sucesso";
				
				}else{
					
					echo "erro";
				
				}
			
			}
		
		}elseif($this->_getParam('fn') == 'reprova_negociacao'){
		
			$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
			
			$arrDados['aprovada'] = -1;
			$arrDados['data_cancelamento'] = @date("Y-m-d H:i:s");
			$arrDados['data_concretizacao'] = "0000-00-00 00:00:00";
		
			if($this->_getParam('id_negociacao')){
		
				if($dbNegociacoes->update($arrDados, 'id = '.$this->_getParam('id_negociacao'))){
				
					$arrDadosVeiculo['vendido'] = 0;
					
					$dbVeiculos->update($arrDadosVeiculo, 'id = '.$this->_getParam('id_veiculo'));
				
					echo "sucesso";
				
				}else{
					
					echo "erro";
				
				}

			}
			
		}elseif($this->_getParam('fn') == 'indicacoes_corretoras'){
	
			$dbIndicacoesCorretoras = new Application_Model_DbTable_IndicacoesCorretoras();
		
			$arrIndicacoes = $dbIndicacoesCorretoras->getIndicacaoPorCorretora($_SESSION['sessionUser']['id_empresa']);
		
			$strIndicacoes = "<tr><th>CLIENTE</th><th>CPF</th><th>TELEFONE</th><th>VEÍCULO</th><th>LOJA</th><th>DATA VENDA</th><th style='width:70px;'>VALOR DO SEGURO</th><th style='width:100px;'>RESULTADO</th></tr>";
		
			if($arrIndicacoes){
		
				foreach($arrIndicacoes as $indicacoes){
				
					$sel1 = "";
					$sel2 = "";
					$sel3 = "";
				
					if($indicacoes['resultado'] == 1){
						$sel1 = "selected = 'selected'";
					}
					if($indicacoes['resultado'] == 2){
						$sel2 = "selected = 'selected'";
					}
					if($indicacoes['resultado'] == 3){
						$sel3 = "selected = 'selected'";
					}
				
					$strIndicacoes .= "<tr onmouseout=\"$(this).css('background-color','#FFFFFF')\" onmouseover=\"$(this).css('background-color','#DDDDDD')\">
											<td style='cursor:pointer;' onclick=javascript:window.open('/negociacoes/impressao-cliente-corretora/id/".$indicacoes['id_cliente']."/id_veiculo/".$indicacoes['id_veiculo']."') >
												".$indicacoes['nome']."
											</td>
											
											<td style='cursor:pointer;' onclick=javascript:window.open('/negociacoes/impressao-cliente-corretora/id/".$indicacoes['id_cliente']."/id_veiculo/".$indicacoes['id_veiculo']."') >
												".$indicacoes['cpf']."
											</td>
											<td style='cursor:pointer;' onclick=javascript:window.open('/negociacoes/impressao-cliente-corretora/id/".$indicacoes['id_cliente']."/id_veiculo/".$indicacoes['id_veiculo']."') >
												".$indicacoes['tel1']."<br/>".$indicacoes['tel2']."
											</td>
											<td style='cursor:pointer;' onclick=javascript:window.open('/negociacoes/impressao-cliente-corretora/id/".$indicacoes['id_cliente']."/id_veiculo/".$indicacoes['id_veiculo']."') >
												".$indicacoes['modelo']."
											</td>
											<td style='cursor:pointer;' onclick=javascript:window.open('/negociacoes/impressao-cliente-corretora/id/".$indicacoes['id_cliente']."/id_veiculo/".$indicacoes['id_veiculo']."') >
												".$indicacoes['nome_fantasia']."
											</td>
											<td style='cursor:pointer;' onclick=javascript:window.open('/negociacoes/impressao-cliente-corretora/id/".$indicacoes['id_cliente']."/id_veiculo/".$indicacoes['id_veiculo']."') >
												".implode("/",array_reverse(explode("-",$indicacoes['data_venda'])))."
											</td>
											<td>
												<input type='text' value='".money_format("%i",$indicacoes['valor_seguro'])."' id='valor_".$indicacoes['id']."' style='width:70px;' onkeyUp='mudaCorSelect(); validaMoedaBR(this.id);' />
											</td>
											<td>
												<select id='select_".$indicacoes['id']."' style='width:100px;' onChange='mudaCorSelect();'>
													<option value='0'>Selecione</option>
													<option value='1' ".$sel1." style='background-color:green; color:#FFFFFF;'>Vendido</option>
													<option value='2' ".$sel2." style='background-color:red; color:#FFFFFF;'>Concorrente</option>
													<option value='3' ".$sel3."style='background-color:yellow; color:#000000;'>Não tem interesse</option>
												</select>
											</td>
									  </tr>";
				
				}
			
				echo $strIndicacoes;
			
			}else{
				
				echo "Não há vendas com as características buscadas.";
			
			}
			
		}elseif($this->_getParam('fn') == 'indicacoes_corretoras_caracteristicas'){
	
			$dbIndicacoesCorretoras = new Application_Model_DbTable_IndicacoesCorretoras();
		
			$arrIndicacoes = $dbIndicacoesCorretoras->getIndicacaoPorCorretoraData($_SESSION['sessionUser']['id_empresa'], $this->_getParam('data_inicial'), $this->_getParam('data_final'));
		
			$strIndicacoes = "<tr><th>CLIENTE</th><th>CPF</th><th>TELEFONE</th><th>VEÍCULO</th><th>LOJA</th><th>DATA VENDA</th><th style='width:70px;'>VALOR DO SEGURO</th><th style='width:100px;'>RESULTADO</th></tr>";
		
			if($arrIndicacoes){
		
				foreach($arrIndicacoes as $indicacoes){
				
					$sel1 = "";
					$sel2 = "";
					$sel3 = "";
				
					if($indicacoes['resultado'] == 1){
						$sel1 = "selected = 'selected'";
					}
					if($indicacoes['resultado'] == 2){
						$sel2 = "selected = 'selected'";
					}
					if($indicacoes['resultado'] == 3){
						$sel3 = "selected = 'selected'";
					}
				
					$strIndicacoes .= "<tr onmouseout=\"$(this).css('background-color','#FFFFFF')\" onmouseover=\"$(this).css('background-color','#DDDDDD')\">
											<td style='cursor:pointer;' onclick=javascript:window.open('/negociacoes/impressao-cliente-corretora/id/".$indicacoes['id_cliente']."/id_veiculo/".$indicacoes['id_veiculo']."') >
												".$indicacoes['nome']."
											</td>
											<td style='cursor:pointer;' onclick=javascript:window.open('/negociacoes/impressao-cliente-corretora/id/".$indicacoes['id_cliente']."/id_veiculo/".$indicacoes['id_veiculo']."') >
												".$indicacoes['cpf']."
											</td>
											<td style='cursor:pointer;' onclick=javascript:window.open('/negociacoes/impressao-cliente-corretora/id/".$indicacoes['id_cliente']."/id_veiculo/".$indicacoes['id_veiculo']."') >
												".$indicacoes['tel1']."<br/>".$indicacoes['tel2']."
											</td>
											<td style='cursor:pointer;' onclick=javascript:window.open('/negociacoes/impressao-cliente-corretora/id/".$indicacoes['id_cliente']."/id_veiculo/".$indicacoes['id_veiculo']."') >
												".$indicacoes['modelo']."
											</td>
											<td style='cursor:pointer;' onclick=javascript:window.open('/negociacoes/impressao-cliente-corretora/id/".$indicacoes['id_cliente']."/id_veiculo/".$indicacoes['id_veiculo']."') >
												".$indicacoes['nome_fantasia']."
											</td>
											<td style='cursor:pointer;' onclick=javascript:window.open('/negociacoes/impressao-cliente-corretora/id/".$indicacoes['id_cliente']."/id_veiculo/".$indicacoes['id_veiculo']."') >
												".implode("/",array_reverse(explode("-",$indicacoes['data_venda'])))."
											</td>
											<td>
												<input type='text' value='".money_format("%i",$indicacoes['valor_seguro'])."' id='valor_".$indicacoes['id']."' style='width:70px;' onkeyUp='mudaCorSelect(); validaMoedaBR(this.id);' />
											</td>
											<td>
												<select id='select_".$indicacoes['id']."' style='width:100px;' onChange='mudaCorSelect();'>
													<option value='0'>Selecione</option>
													<option value='1' ".$sel1." style='background-color:green; color:#FFFFFF;'>Vendido</option>
													<option value='2' ".$sel2." style='background-color:red; color:#FFFFFF;'>Concorrente</option>
													<option value='3' ".$sel3."style='background-color:yellow; color:#000000;'>Não tem interesse</option>
												</select>
											</td>
									  </tr>";
				
				}
			
				echo $strIndicacoes;
			
			}else{
				
				echo "Não há vendas com as características buscadas.";
			
			}
			
			
		}elseif($this->_getParam('fn') == 'salva_indicacoes'){
			
			$dbIndicacoesCorretoras = new Application_Model_DbTable_IndicacoesCorretoras();
			
			//if($this->_getParam('valor') != "0" && $this->_getParam('id') != "" && $this->_getParam('resultado') != ""){
			
				if($dbIndicacoesCorretoras->edt($this->_getParam('id'), array("valor_seguro"=>str_replace(",",".",$this->_getParam('valor')), "resultado"=>$this->_getParam('resultado')))){
					
					echo "sucesso";
					
				}else{
					
					echo "erro";
				
				}
				
			//}

		}
		
	}

   public function imprimirPromissoriaAction(){

      $layout = $this->_helper->layout();
      $layout->setLayout('no-layout');

      $dbEmpresas = new Application_Model_DbTable_Empresas();

      $arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);

      $dataVenc = $this->_getParam('data_promissoria');

      $dataVenc = explode("-", $dataVenc);

      $mes = $this->mesExtenso($dataVenc[1]);
      $ano = $this->valor_extenso($dataVenc[2]);
      $dia = $this->valor_extenso($dataVenc[0]);

      if ($dia != " um ") {

         $texto .= "Aos <b>" . $dia . " dias do m&ecirc;s de " . $mes . " de " . $ano . ".</b>";
      } else {

         $texto .= "A <b>" . $dia . " dia do m&ecirc;s de " . $mes . " de " . $ano . ".</b>";
      }

      $texto .= "<br>Pagarei por esta &uacute;nica via de NOTA PROMISS&Oacute;RIA a ";
      $texto .= "<b>" . $arrEmpresa[0]['razao_social'] . ",</b>";
      $texto .= " CNPJ <b>" . $arrEmpresa[0]['cnpj'] . "</b>";
      $texto .= " ou a sua ordem a quantia de <b>" . $this->valorExtensoDinheiro($this->_getParam('valor_promissoria')) . "</b> em moeda corrente deste pa&iacute;s.<br>";

      $emitente = "<br>Pag&aacute;vel em <b>" . $arrEmpresa[0]['cidade'] . " - " . $arrEmpresa[0]['estado'] . "</b><br>";
      $emitente .= "Emitente: <b>" . $this->_getParam('nome') . "</b><br>";
      $emitente .= "CPF/CNPJ: <b>" . $this->_getParam('cpf') . "</b><br>";
      $emitente .= "Endere&ccedil;o: <b>" . $this->_getParam('endereco') . " - " . $this->_getParam('cidade') . " - " . $this->_getParam('estado') . "</b><br>CEP:<b>" . $this->_getParam('cep') . "</b><br>";

      $assinatura = $arrEmpresa[0]['cidade'] . " " . @date("d") . " de " . $this->mesExtenso(@date("m")) . " de " . @date("Y") . "<br><br>";
      $assinatura .= "_____________________________________";

      $this->view->dataVenc = "Vencimento " . $dataVenc[0] . " de " . $mes . " de " . $dataVenc[2];
      $this->view->numProm = $this->_getParam('num_promissoria');
      $this->view->valorProm = $this->_getParam('valor_promissoria');
      $this->view->dataVencExtenso = $texto;
      $this->view->emitente = $emitente;
      $this->view->assinatura = $assinatura;

   }

   public function addAction(){

      $this->validaAcesso('gerenciar_vendas');

      $dbFinanceira = new Application_Model_DbTable_Financeiras();
      $dbUsuarios = new Application_Model_DbTable_Usuarios();
      $dbEmpresas = new Application_Model_DbTable_Empresas();

      $this->view->id_empresa = $_SESSION['sessionUser']['id_empresa'];
      $this->view->financeiras = $dbFinanceira->fetchAll("id_empresa = " . $_SESSION['sessionUser']['id_empresa'] . " AND tipo = 0");
      $this->view->despachante = $dbFinanceira->fetchAll("id_empresa = " . $_SESSION['sessionUser']['id_empresa'] . " AND tipo = 1");
	  $this->view->vendedores = $dbUsuarios->fetchAll("id_empresa = " . $_SESSION['sessionUser']['id_empresa'] . " AND (id_perfil = 3 or id_perfil = 2 or id_perfil = 9 or id_perfil = 4) AND ativo = 1 AND excluido = 0");
	  $this->view->supervisores = $dbUsuarios->fetchAll("id_empresa = " . $_SESSION['sessionUser']['id_empresa'] . " AND id_perfil = 9 AND ativo = 1 AND excluido = 0");
	  $this->view->gerentes = $dbUsuarios->fetchAll("id_empresa = " . $_SESSION['sessionUser']['id_empresa'] . " AND id_perfil = 4 AND ativo = 1 AND excluido = 0");



    if ($this->getRequest()->isPost()) {

         $dbNegociacoes = new Application_Model_DbTable_Negociacoes();
         $dbTrocasNegociacoes = new Application_Model_DbTable_TrocasNegociacoes();
         $dbRecebimentosNegociacoes = new Application_Model_DbTable_RecebimentosNegociacoes();
         $dbVeiculos = new Application_Model_DbTable_Veiculos();

        // $_POST['id_usuario'] = $_POST['id_vendedor'];
        $_POST['id_usuario'] = $_SESSION['sessionUser']['id'];

		 
		  $km = $_POST['km'];
		  
		 unset($_POST['km']);
		 
		if($_POST['id_gerente'] == ""){
		 
			unset($_POST['id_gerente']);
			
		}
		
		if($_POST['id_supervisor'] == ""){
		 
			unset($_POST['id_supervisor']);
			
		}


         unset($_POST['find_cpf']);
         unset($_POST['find_placa']);
         unset($_POST['numero_endereco']);
         unset($_POST['placa']);
         unset($_POST['find_modelo_troca']);
         unset($_POST['valor_aquisicao']);
         unset($_POST['diferenca']);

         if (!isset($_POST['aprovada'])) {

            $_POST['aprovada'] = -2;
			
         }

		 
         //ESCREVENDO NA TABELA NEGOCIACOES

         $dadosNegociacao = $_POST;

         unset($dadosNegociacao['id_veiculo_troca']);
         unset($dadosNegociacao['preco_troca']);
         unset($dadosNegociacao['km_troca']);
         unset($dadosNegociacao['renavam']);
		 unset($dadosNegociacao['id_veiculo_troca2']);
         unset($dadosNegociacao['preco_troca2']);
         unset($dadosNegociacao['km_troca2']);
         unset($dadosNegociacao['renavam2']);
         unset($dadosNegociacao['data_termino_revisao']);
		 unset($dadosNegociacao['km']);

         foreach ($dadosNegociacao as $k => $v) {

            $arrPartes = explode("_", $k);

            if ($arrPartes[0] == "Rforma" || $arrPartes[0] == "Rdata" || $arrPartes[0] == "Rnumero" || $arrPartes[0] == "Rbanco" || $arrPartes[0] == "Ragencia" || $arrPartes[0] == "Rcc" || $arrPartes[0] == "Rvalor" || $arrPartes[0] == "Rbaixado") {

               unset($dadosNegociacao[$k]);
            }
         }

         foreach (array('data_concretizacao', 'data_cancelamento', 'data_entrega_veiculo', 'data_termino_garantia', 'data_recebimento_veiculo') as $campoData) {
            if (!isset($dadosNegociacao[$campoData]) || $dadosNegociacao[$campoData] == '') {
               $dadosNegociacao[$campoData] = '0000-00-00 00:00:00';
               continue;
            }
            $dataTmp = explode(" ", $dadosNegociacao[$campoData]);
            $dataTmp2 = explode("/", $dataTmp[0]);
            if ($dataTmp2[0] == '00' || (isset($dataTmp2[2]) && $dataTmp2[2] == '0000')) {
               $dadosNegociacao[$campoData] = '0000-00-00 00:00:00';
            } else {
               $dadosNegociacao[$campoData] = implode("-", array_reverse($dataTmp2));
               $dadosNegociacao[$campoData] = $dadosNegociacao[$campoData] . " " . $dataTmp[1];
            }
         }

         foreach (array('data_abertura') as $campoData) {
            if (!isset($dadosNegociacao[$campoData]) || $dadosNegociacao[$campoData] == '') {
               unset($dadosNegociacao[$campoData]);
               continue;
            }
            $dataTmp = explode(" ", $dadosNegociacao[$campoData]);
            $dataTmp2 = explode("/", $dataTmp[0]);
            if ($dataTmp2[0] == '00' || (isset($dataTmp2[2]) && $dataTmp2[2] == '0000')) {
               unset($dadosNegociacao[$campoData]);
            } else {
               $dadosNegociacao[$campoData] = implode("-", array_reverse($dataTmp2));
               $dadosNegociacao[$campoData] = $dadosNegociacao[$campoData] . " " . $dataTmp[1];
            }
         }

		 $dadosNegociacao['comissao_vendedor_real'] = str_replace(".", "", $dadosNegociacao['comissao_vendedor_real']);
         $dadosNegociacao['comissao_vendedor'] = str_replace(",", ".", $dadosNegociacao['comissao_vendedor_real']);
		 
		 $dadosNegociacao['comissao_gerente_real'] = str_replace(".", "", $dadosNegociacao['comissao_gerente_real']);
         $dadosNegociacao['comissao_gerente'] = str_replace(",", ".", $dadosNegociacao['comissao_gerente_real']);
		 
		 $dadosNegociacao['comissao_supervisor_real'] = str_replace(".", "", $dadosNegociacao['comissao_supervisor_real']);
         $dadosNegociacao['comissao_supervisor'] = str_replace(",", ".", $dadosNegociacao['comissao_supervisor_real']);
		 
         $dadosNegociacao['valor_base_calculo'] = str_replace(".", "", $dadosNegociacao['valor_base_calculo']);
         $dadosNegociacao['valor_base_calculo'] = str_replace(",", ".", $dadosNegociacao['valor_base_calculo']);

         $dadosNegociacao['valor_financiado'] = str_replace(".", "", $dadosNegociacao['valor_financiado']);
         $dadosNegociacao['valor_financiado'] = str_replace(",", ".", $dadosNegociacao['valor_financiado']);

         $dadosNegociacao['tac'] = str_replace(".", "", $dadosNegociacao['tac']);
         $dadosNegociacao['tac'] = str_replace(",", ".", $dadosNegociacao['tac']);

         $dadosNegociacao['coeficiente_financeira'] = str_replace(".", "", $dadosNegociacao['coeficiente_financeira']);
         $dadosNegociacao['coeficiente_financeira'] = str_replace(",", ".", $dadosNegociacao['coeficiente_financeira']);

         $dadosNegociacao['valor_prestacoes'] = str_replace(".", "", $dadosNegociacao['valor_prestacoes']);
         $dadosNegociacao['valor_prestacoes'] = str_replace(",", ".", $dadosNegociacao['valor_prestacoes']);

         $dadosNegociacao['valor_venda'] = str_replace(".", "", $dadosNegociacao['valor_venda']);
         $dadosNegociacao['valor_venda'] = str_replace(",", ".", $dadosNegociacao['valor_venda']);

         $dadosNegociacao['custos_transferencia'] = str_replace(".", "", $dadosNegociacao['custos_transferencia']);
         $dadosNegociacao['custos_transferencia'] = str_replace(",", ".", $dadosNegociacao['custos_transferencia']);

         $dadosNegociacao['valor_despachante'] = str_replace(".","",$dadosNegociacao['valor_despachante']);
         $dadosNegociacao['valor_despachante'] = str_replace(",", ".", $dadosNegociacao['valor_despachante']);

		 $dadosNegociacao['imposto_financeira'] = str_replace(".", "", $dadosNegociacao['imposto_financeira']);
         $dadosNegociacao['imposto_financeira'] = str_replace(",", ".", $dadosNegociacao['imposto_financeira']);
		 
		 
        unset($dadosNegociacao['nome_vendedor']);
		unset($dadosNegociacao['comissao_vendedor_real']);
        unset($dadosNegociacao['nome_gerente']);
        unset($dadosNegociacao['comissao_gerente_real']);
        unset($dadosNegociacao['nome_supervisor']);
        unset($dadosNegociacao['comissao_supervisor_real']);
        unset($dadosNegociacao['multas']);

         if ($dadosNegociacao['id_financeira'] == "") {

            unset($dadosNegociacao['id_financeira']);

         }

         foreach (array('tac', 'coeficiente_financeira', 'valor_despachante', 'valor_base_calculo', 'valor_financiado', 'retorno_financeira', 'numero_prestacoes', 'valor_prestacoes', 'comissao_vendedor', 'comissao_gerente', 'comissao_supervisor', 'imposto_financeira', 'custos_transferencia', 'valor_venda', 'km_entrega_veiculo', 'km_recebimento_veiculo', 'dias_garantia', 'km_garantia') as $campo) {
            if (!isset($dadosNegociacao[$campo]) || $dadosNegociacao[$campo] === '') {
               $dadosNegociacao[$campo] = 0;
            }
         }

         if (isset($dadosNegociacao['id_despachante']) && $dadosNegociacao['id_despachante'] === '') {
            $dadosNegociacao['id_despachante'] = null;
         }

		 unset($dadosNegociacao['origem']);

         $idNegociacao = $dbNegociacoes->insert($dadosNegociacao);
		 
		 if($dadosNegociacao['id_cliente'] && $_POST['origem']){
			$dbClientes = new Application_Model_DbTable_Clientes();
			$dbClientes->edt($dadosNegociacao['id_cliente'], array('origem'=>$_POST['origem'])); 
		 }

         $dbVeiculos->update(array('vendido' => 1, 'id_negociacao' => $idNegociacao, 'data_termino_revisao' => $_POST['data_termino_revisao'], 'valor_venda' => $dadosNegociacao['valor_venda'], 'km'=>$km), "id = " . $_POST['id_veiculo']);

         //FIM ESCREVENDO NA TABELA NEGOCIACOES
         //ESCREVENDO NA TABELA TROCAS NEGOCIACOES

        if ($_POST['id_veiculo_troca']){

            $dbVeiculos->update(array('temp_troca' => 1, 'id_negociacao_troca' => $idNegociacao), "id = " . $_POST['id_veiculo_troca']);
         
		 }
		 
		if ($_POST['id_veiculo_troca2']){

            $dbVeiculos->update(array('temp_troca' => 1, 'id_negociacao_troca2' => $idNegociacao), "id = " . $_POST['id_veiculo_troca2']);
         
		}

         //FIM ESCREVENDO NA TABELA TROCAS NEGOCIACOES
         //ESCREVENDO NA TABELA RECEBIMENTOS NEGOCIACOES

         $dadosRecebimentos = $_POST;

         foreach ($dadosRecebimentos as $k => $v) {

            $arrPartes = explode("_", $k);

            if ($arrPartes[0] != "Rforma" && $arrPartes[0] != "Rdata" && $arrPartes[0] != "Rnumero" && $arrPartes[0] != "Rbanco" && $arrPartes[0] != "Ragencia" && $arrPartes[0] != "Rcc" && $arrPartes[0] != "Rvalor" && $arrPartes[0] != "Rbaixado") {

               unset($dadosRecebimentos[$k]);
            } else {

               $arrFinalRecebimentos[$arrPartes[1]][substr($arrPartes[0], 1)] = $v;
            }
         }



         foreach ($arrFinalRecebimentos as $r) {

            $r['id_negociacao'] = $idNegociacao;

            if ($r['data'] != "") {

               $dataTmp = explode(" ", $r['data']);
               $dataTmp2 = explode("/", $dataTmp[0]);
               $r['data'] = implode("-", array_reverse($dataTmp2));
               $r['data'] = $r['data'] . " " . $dataTmp[1];
            }

            $r['valor'] = str_replace(".", "", $r['valor']);
            $r['valor'] = str_replace(",", ".", $r['valor']);

            $dbRecebimentosNegociacoes->insert($r);
         }

         //FIM ESCREVENDO NA TABELA RECEBIMENTOS NEGOCIACOES

         $idNegociacao = $dbNegociacoes->getLastId();
		 
		$arrEmpresa = $dbEmpresas->_get(array("id_empresa"=>$_SESSION['sessionUser']['id_empresa']));
		 
		if($arrEmpresa[0]['indicacao_corretora'] == 1){
		 
			$this->enviaDadosCorretora($_SESSION['sessionUser']['id_empresa'], $dadosNegociacao['id_cliente'], $dadosNegociacao['id_veiculo'], $dadosNegociacao['id_usuario'], $dadosNegociacao['valor_venda']);
		
		}
		
        $this->_helper->redirector->gotoUrl("negociacoes/edt/id/" . $idNegociacao[0]['id']);
      
	  }
	  
   }

   public function edtAction(){

      $this->validaAcesso('gerenciar_vendas');

      $dbFinanceira = new Application_Model_DbTable_Financeiras();
      $dbNegociacoes = new Application_Model_DbTable_Negociacoes();
      $dbTrocasNegociacoes = new Application_Model_DbTable_TrocasNegociacoes();
      $dbRecebimentosNegociacoes = new Application_Model_DbTable_RecebimentosNegociacoes();
      $dbVeiculos = new Application_Model_DbTable_Veiculos();
      $dbUsuarios = new Application_Model_DbTable_Usuarios();
      $dbEmpresas = new Application_Model_DbTable_Empresas();

      $arrPagamentosEdt = array();
      $arrPagamentos = array();
	  
	  $this->view->vendedores = $dbUsuarios->fetchAll("id_empresa = " . $_SESSION['sessionUser']['id_empresa'] . " AND (id_perfil = 3 or id_perfil = 2 or id_perfil = 9 or id_perfil = 4) AND ativo = 1 AND excluido = 0");
	  $this->view->supervisores = $dbUsuarios->fetchAll("id_empresa = " . $_SESSION['sessionUser']['id_empresa'] . " AND id_perfil = 9 AND ativo = 1 AND excluido = 0");
	  $this->view->gerentes = $dbUsuarios->fetchAll("id_empresa = " . $_SESSION['sessionUser']['id_empresa'] . " AND id_perfil = 4 AND ativo = 1 AND excluido = 0");


		if ($this->getRequest()->isPost()) {

         $idNegociacao = $this->_getParam('id');

         $arrDados['id_veiculo'] = $_POST['id_veiculo'];

         if ($_POST['id_gerente']){

            $arrDados['id_gerente'] = $_POST['id_gerente'];
         
		 } else {

            $arrDados['id_gerente'] = null;
         }

         if ($_POST['id_supervisor']) {

            $arrDados['id_supervisor'] = $_POST['id_supervisor'];
         
		 } else {

            $arrDados['id_supervisor'] = null;
         
		 }

         $arrDados['id_cliente'] = $_POST['id_cliente'];

         if ($_POST['id_financeira']) {

            $arrDados['id_financeira'] = $_POST['id_financeira'];
         
		 }

         if(isset($_POST['id_vendedor']) && !empty($_POST['id_vendedor'])){
            $arrDados['id_vendedor'] = $_POST['id_vendedor'];
         }
         if(isset($_POST['data_abertura'])){
            $arrDados['data_abertura'] = $_POST['data_abertura'];
         }
         if(isset($_POST['data_concretizacao'])){
            $arrDados['data_concretizacao'] = $_POST['data_concretizacao'];
         }
         if(isset($_POST['data_cancelamento'])){
            $arrDados['data_cancelamento'] = $_POST['data_cancelamento'];
         }
         if(isset($_POST['data_entrega_veiculo'])){
            $arrDados['data_entrega_veiculo'] = $_POST['data_entrega_veiculo'];
         }
         if(isset($_POST['km_entrega_veiculo'])){
            $arrDados['km_entrega_veiculo'] = $_POST['km_entrega_veiculo'];
         }
         if(isset($_POST['data_termino_garantia'])){
            $arrDados['data_termino_garantia'] = $_POST['data_termino_garantia'];
         }
         if(isset($_POST['data_recebimento_veiculo'])){
            $arrDados['data_recebimento_veiculo'] = $_POST['data_recebimento_veiculo'];
         }
         if(isset($_POST['km_recebimento_veiculo'])){
            $arrDados['km_recebimento_veiculo'] = $_POST['km_recebimento_veiculo'];
         }
         if(isset($_POST['valor_base_calculo'])){
            $arrDados['valor_base_calculo'] = $_POST['valor_base_calculo'];
         }
         if(isset($_POST['comissao_vendedor_real'])){
            $arrDados['comissao_vendedor'] = $_POST['comissao_vendedor_real'];
         }
         if(isset($_POST['comissao_gerente_real'])){
            $arrDados['comissao_gerente'] = $_POST['comissao_gerente_real'];
         }
         if(isset($_POST['comissao_supervisor_real'])){
            $arrDados['comissao_supervisor'] = $_POST['comissao_supervisor_real'];
         }
         if(isset($_POST['valor_financiado'])){
            $arrDados['valor_financiado'] = $_POST['valor_financiado'];
         }
         if(isset($_POST['tac'])){
            $arrDados['tac'] = $_POST['tac'];
         }
         if(isset($_POST['coeficiente_financeira'])){
            $arrDados['coeficiente_financeira'] = $_POST['coeficiente_financeira'];
         }
         if(isset($_POST['numero_prestacoes'])){
            $arrDados['numero_prestacoes'] = $_POST['numero_prestacoes'];
         }
         if(isset($_POST['imposto_financeira'])){
            $arrDados['imposto_financeira'] = $_POST['imposto_financeira'];
         }
         if(isset($_POST['valor_prestacoes'])){
            $arrDados['valor_prestacoes'] = $_POST['valor_prestacoes'];
         }
         if(isset($_POST['valor_despachante'])){
            $arrDados['valor_despachante'] = $_POST['valor_despachante'];
         }
         if(isset($_POST['valor_venda'])){
            $arrDados['valor_venda'] = $_POST['valor_venda'];
         }
         if(isset($_POST['custos_transferencia'])){
            $arrDados['custos_transferencia'] = $_POST['custos_transferencia'];
         }
         if(isset($_POST['dias_garantia'])){
            $arrDados['dias_garantia'] = $_POST['dias_garantia'];
         }
         if(isset($_POST['km_garantia'])){
            $arrDados['km_garantia'] = $_POST['km_garantia'];
         }
         if(isset($_POST['obs'])){
            $arrDados['obs'] = $_POST['obs'];
         }
         if(isset($_POST['obs_interna'])){
            $arrDados['obs_interna'] = $_POST['obs_interna'];
         }
         if(isset($_POST['retorno_financeira'])){
            $arrDados['retorno_financeira'] = $_POST['retorno_financeira'];
         }
         if(isset($_POST['aprovada'])){
            $arrDados['aprovada'] = $_POST['aprovada'];
         }
         if(isset($_POST['id_despachante'])){
            $arrDados['id_despachante'] = $_POST['id_despachante'] !== '' ? $_POST['id_despachante'] : null;
         }
         if(isset($_POST['forma_pagamento_despachante'])){
            $arrDados['forma_pagamento_despachante'] = $_POST['forma_pagamento_despachante'];
         }
         if(isset($_POST['liberado_contrato'])){
            $arrDados['liberado_contrato'] = $_POST['liberado_contrato'];
         }


		 
		 $arrKM['km'] = $_POST['km'];
		 unset($_POST['km']);

         if (isset($_POST['aprovada']) && $_POST['aprovada'] == 0) {

            $arrDados['aprovada'] = $_POST['aprovada'];
         }

         if (isset($_POST['data_abertura']) && $arrDados['data_abertura'] != "") {

            $dataTmp = explode(" ", $arrDados['data_abertura']);
            $dataTmp2 = explode("/", $dataTmp[0]);
            if ($dataTmp2[0] == '00' || $dataTmp2[2] == '0000') {
               unset($arrDados['data_abertura']);
            } else {
               $arrDados['data_abertura'] = implode("-", array_reverse($dataTmp2));
               $arrDados['data_abertura'] = $arrDados['data_abertura'] . " " . $dataTmp[1];
            }
         }

         if (isset($_POST['data_concretizacao']) && $arrDados['data_concretizacao'] != "") {

            $dataTmp = explode(" ", $arrDados['data_concretizacao']);
            $dataTmp2 = explode("/", $dataTmp[0]);
            if ($dataTmp2[0] == '00' || $dataTmp2[2] == '0000') {
               unset($arrDados['data_concretizacao']);
            } else {
               $arrDados['data_concretizacao'] = implode("-", array_reverse($dataTmp2));
               $arrDados['data_concretizacao'] = $arrDados['data_concretizacao'] . " " . $dataTmp[1];
            }
         }

         if (isset($_POST['data_cancelamento']) && $arrDados['data_cancelamento'] != "") {

            $dataTmp = explode(" ", $arrDados['data_cancelamento']);
            $dataTmp2 = explode("/", $dataTmp[0]);
            if ($dataTmp2[0] == '00' || $dataTmp2[2] == '0000') {
               unset($arrDados['data_cancelamento']);
            } else {
               $arrDados['data_cancelamento'] = implode("-", array_reverse($dataTmp2));
               $arrDados['data_cancelamento'] = $arrDados['data_cancelamento'] . " " . $dataTmp[1];
            }
         }

         if (isset($_POST['data_entrega_veiculo']) && $arrDados['data_entrega_veiculo'] != "") {

            $dataTmp = explode(" ", $arrDados['data_entrega_veiculo']);
            $dataTmp2 = explode("/", $dataTmp[0]);
            if ($dataTmp2[0] == '00' || $dataTmp2[2] == '0000') {
               unset($arrDados['data_entrega_veiculo']);
            } else {
               $arrDados['data_entrega_veiculo'] = implode("-", array_reverse($dataTmp2));
               $arrDados['data_entrega_veiculo'] = $arrDados['data_entrega_veiculo'] . " " . $dataTmp[1];
            }
         }

         if (isset($_POST['data_termino_garantia']) && $arrDados['data_termino_garantia'] != "") {

            $dataTmp = explode(" ", $arrDados['data_termino_garantia']);
            $dataTmp2 = explode("/", $dataTmp[0]);
            if ($dataTmp2[0] == '00' || $dataTmp2[2] == '0000') {
               unset($arrDados['data_termino_garantia']);
            } else {
               $arrDados['data_termino_garantia'] = implode("-", array_reverse($dataTmp2));
               $arrDados['data_termino_garantia'] = $arrDados['data_termino_garantia'] . " " . $dataTmp[1];
            }
         }

         if (isset($_POST['data_recebimento_veiculo']) && $arrDados['data_recebimento_veiculo'] != "") {

            $dataTmp = explode(" ", $arrDados['data_recebimento_veiculo']);
            $dataTmp2 = explode("/", $dataTmp[0]);
            if ($dataTmp2[0] == '00' || $dataTmp2[2] == '0000') {
               unset($arrDados['data_recebimento_veiculo']);
            } else {
               $arrDados['data_recebimento_veiculo'] = implode("-", array_reverse($dataTmp2));
               $arrDados['data_recebimento_veiculo'] = $arrDados['data_recebimento_veiculo'] . " " . $dataTmp[1];
            }
         }

         if(isset($arrDados['valor_base_calculo'])){
            $arrDados['valor_base_calculo'] = str_replace(".", "", $arrDados['valor_base_calculo']);
            $arrDados['valor_base_calculo'] = str_replace(",", ".", $arrDados['valor_base_calculo']);
         }

         if(isset($arrDados['valor_financiado'])){
            $arrDados['valor_financiado'] = str_replace(".", "", $arrDados['valor_financiado']);
            $arrDados['valor_financiado'] = str_replace(",", ".", $arrDados['valor_financiado']);
         }

         if(isset($arrDados['tac'])){
            $arrDados['tac'] = str_replace(".", "", $arrDados['tac']);
            $arrDados['tac'] = str_replace(",", ".", $arrDados['tac']);
         }

         if(isset($arrDados['coeficiente_financeira'])){
            //$arrDados['coeficiente_financeira'] = str_replace(".","",$arrDados['coeficiente_financeira']);
            $arrDados['coeficiente_financeira'] = str_replace(",", ".", $arrDados['coeficiente_financeira']);
         }

         if(isset($arrDados['imposto_financeira'])){
            //$arrDados['imposto_financeira'] = str_replace(".", "", $arrDados['imposto_financeira']);
            $arrDados['imposto_financeira'] = str_replace(",", ".", $arrDados['imposto_financeira']);
         }

         if(isset($arrDados['valor_prestacoes'])){
            $arrDados['valor_prestacoes'] = str_replace(".", "", $arrDados['valor_prestacoes']);
            $arrDados['valor_prestacoes'] = str_replace(",", ".", $arrDados['valor_prestacoes']);
         }

         if(isset($arrDados['valor_venda'])){
            $arrDados['valor_venda'] = str_replace(".", "", $arrDados['valor_venda']);
            $arrDados['valor_venda'] = str_replace(",", ".", $arrDados['valor_venda']);
         }

         if(isset($arrDados['custos_transferencia'])){
            $arrDados['custos_transferencia'] = str_replace(".", "", $arrDados['custos_transferencia']);
            $arrDados['custos_transferencia'] = str_replace(",", ".", $arrDados['custos_transferencia']);
         }

         if(isset($arrDados['comissao_vendedor'])){
            $arrDados['comissao_vendedor'] = str_replace(".", "", $arrDados['comissao_vendedor']);
            $arrDados['comissao_vendedor'] = str_replace(",", ".", $arrDados['comissao_vendedor']);
         }

         if(isset($arrDados['comissao_gerente'])){
            $arrDados['comissao_gerente'] = str_replace(".", "", $arrDados['comissao_gerente']);
            $arrDados['comissao_gerente'] = str_replace(",", ".", $arrDados['comissao_gerente']);
         }

         if(isset($arrDados['comissao_supervisor'])){
            $arrDados['comissao_supervisor'] = str_replace(".", "", $arrDados['comissao_supervisor']);
            $arrDados['comissao_supervisor'] = str_replace(",", ".", $arrDados['comissao_supervisor']);
         }

         if(isset($arrDados['valor_despachante'])){
            //$arrDados['valor_despachante'] = str_replace(".","",$arrDados['valor_despachante']);
            $arrDados['valor_despachante'] = str_replace(",", ".", $arrDados['valor_despachante']);
         }

		 unset($arrDados['origem']);
		 
         $dbNegociacoes->update($arrDados, 'id = ' . $_POST['id']);

		 

		 if($arrDados['id_cliente'] && $_POST['origem']){
			$dbClientes = new Application_Model_DbTable_Clientes();
			$dbClientes->edt($arrDados['id_cliente'], array('origem'=>$_POST['origem'])); 
		 }

         $dbVeiculos->update(array('temp_troca' => 0, 'id_negociacao_troca' => null), "id_empresa = " . $_SESSION['sessionUser']['id_empresa'] . " AND id_negociacao_troca = " . $idNegociacao );
         $dbVeiculos->update(array('temp_troca' => 0, 'id_negociacao_troca2' => null), "id_empresa = " . $_SESSION['sessionUser']['id_empresa'] . " AND id_negociacao_troca2 = " . $idNegociacao);
         $dbVeiculos->update(array('vendido' => 0, 'id_negociacao' => null), "id_negociacao = " . $idNegociacao);

        if ($_POST['id_veiculo_troca'] != ""){

            if ($_POST['aprovada'] == "-1") {

               $arrDadosVeiculos['temp_troca'] = 0;
               $arrDadosVeiculos['id_negociacao_troca'] = null;
               $dbVeiculos->edt($_POST['id_veiculo_troca'], $arrDadosVeiculos);
			   
            } else {

               $arrDadosVeiculos['temp_troca'] = 1;
               $arrDadosVeiculos['id_negociacao_troca'] = $idNegociacao;
               $dbVeiculos->edt($_POST['id_veiculo_troca'], $arrDadosVeiculos);
			   
            }
        }
		
		if ($_POST['id_veiculo_troca2'] != ""){

            if ($_POST['aprovada'] == "-1") {

               $arrDadosVeiculos2['temp_troca'] = 0;
               $arrDadosVeiculos2['id_negociacao_troca2'] = null;
               $dbVeiculos->edt($_POST['id_veiculo_troca2'], $arrDadosVeiculos2);
			   
            } else {

               $arrDadosVeiculos2['temp_troca'] = 1;
               $arrDadosVeiculos2['id_negociacao_troca2'] = $idNegociacao;
               $dbVeiculos->edt($_POST['id_veiculo_troca2'], $arrDadosVeiculos2);
			   
            }
        }

        if ($_POST['aprovada'] == "-1") {

            $arrDadosVeiculo['vendido'] = 0;
            $arrDadosVeiculo['id_negociacao'] = null;

            $dbVeiculos->edt($_POST['id_veiculo'], $arrDadosVeiculo);
        
		}else{

            $dbVeiculos->update(array('vendido' => 1, 'id_negociacao' => $idNegociacao, 'data_termino_revisao' => $_POST['data_termino_revisao'], 'valor_venda' => $arrDados['valor_venda']), "id = " . $_POST['id_veiculo']);
         
			$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
			
			if($arrEmpresa[0]['login_icarros'] && $arrEmpresa[0]['senha_icarros']){
			
				$arrIcarros = $this->getEstoqueIcarros();
				$arrVeiculos = $dbVeiculos->getVeiculoEstoque($_POST['id_veiculo']);
				
				if($arrIcarros){
            foreach($arrIcarros as $key=>$arrI){
				
					if(strtolower(substr($arrVeiculos[0]['placa'],0,3).substr($arrVeiculos[0]['placa'],4)) == strtolower($arrI['placa'])){
					
						try{
			
							$url = 'https://paginasegura.icarros.com.br/services/icarroswebservice?wsdl';

							$client = new SoapClient($url, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
		
							$client->__setLocation('http://www.icarros.com.br/services/icarroswebservice');
		
							$token = $client->autenticarAcesso($arrEmpresa[0]['login_icarros'], $arrEmpresa[0]['senha_icarros']);
			
						}catch (SoapFault $exception){
			
							echo $exception->getMessage();
	
						}
					
						$client->excluirAnuncio($token, $key);
						
					}
				
				}

         }

			}
			
			if($arrEmpresa[0]['login_webmotors'] && $arrEmpresa[0]['senha_webmotors'] && $arrEstoqueWeb){
		
				$arrEstoqueWeb = $this->getEstoqueWebmotors($arrEmpresa[0]['cnpj'], $arrEmpresa[0]['login_webmotors'], $arrEmpresa[0]['senha_webmotors']);

				foreach($arrEstoqueWeb as $estoqueWeb){
			
					if(strtolower($estoqueWeb['placa']) == strtolower(str_replace("-","",$arrVeiculos[0]['placa']))){

						$this->excluiVeiculoWebmotors($arrEmpresa[0]['cnpj'], $arrEmpresa[0]['login_webmotors'], $arrEmpresa[0]['senha_webmotors'], $estoqueWeb['codigo_anuncio']);
						break;
				
					}
			
				}
			
			}
			
		}

         foreach ($_POST as $chave => $valor) {

            $pagamento = explode("_", $chave);

            if (isset($pagamento[1]) && $pagamento[1] <= 0) {

               if ($pagamento[0] == "Rforma") {

                  $arrPagamentos[$pagamento[1]]['forma'] = $valor;
                  $arrPagamentos[$pagamento[1]]['id_negociacao'] = $_POST['id'];
               }

               if ($pagamento[0] == "Rdata") {

                  $dataTmp2 = explode("/", $valor);
                  $valor = implode("-", array_reverse($dataTmp2));

                  $arrPagamentos[$pagamento[1]]['data'] = $valor;
               }

               if ($pagamento[0] == "Rnumero") {

                  $arrPagamentos[$pagamento[1]]['numero'] = $valor;
               }

               if ($pagamento[0] == "Rbanco") {

                  $arrPagamentos[$pagamento[1]]['banco'] = $valor;
               }

               if ($pagamento[0] == "Ragencia") {

                  $arrPagamentos[$pagamento[1]]['agencia'] = $valor;
               }

               if ($pagamento[0] == "Rcc") {

                  $arrPagamentos[$pagamento[1]]['cc'] = $valor;
               }

               if ($pagamento[0] == "Rvalor") {

                  $arrPagamentos[$pagamento[1]]['valor'] = $valor;
               }

               if ($pagamento[0] == "Rbaixado") {

                  $arrPagamentos[$pagamento[1]]['baixado'] = $valor;
               }
            } elseif (isset($pagamento[1]) && $pagamento[1] > 0) {

               if ($pagamento[0] == "Rforma") {

                  $arrPagamentosEdt[$pagamento[1]]['forma'] = $valor;
                  $arrPagamentosEdt[$pagamento[1]]['id'] = $pagamento[1];
               }

               if ($pagamento[0] == "Rdata") {

                  $dataTmp2 = explode("/", $valor);
                  $valor = implode("-", array_reverse($dataTmp2));

                  $arrPagamentosEdt[$pagamento[1]]['data'] = $valor;
               }

               if ($pagamento[0] == "Rnumero") {

                  $arrPagamentosEdt[$pagamento[1]]['numero'] = $valor;
               }

               if ($pagamento[0] == "Rbanco") {

                  $arrPagamentosEdt[$pagamento[1]]['banco'] = $valor;
               }

               if ($pagamento[0] == "Ragencia") {

                  $arrPagamentosEdt[$pagamento[1]]['agencia'] = $valor;
               }

               if ($pagamento[0] == "Rcc") {

                  $arrPagamentosEdt[$pagamento[1]]['cc'] = $valor;
               }

               if ($pagamento[0] == "Rvalor") {

                  $arrPagamentosEdt[$pagamento[1]]['valor'] = $valor;
               }

               if ($pagamento[0] == "Rbaixado") {

                  $arrPagamentosEdt[$pagamento[1]]['baixado'] = $valor;
               }
            }
         }

         if ($arrPagamentosEdt) {

            foreach ($arrPagamentosEdt as $pagamentoEdt) {

               $dbRecebimentosNegociacoes->update($pagamentoEdt, 'id = ' . $pagamentoEdt['id']);
            }
         }

         if ($arrPagamentos) {

            foreach ($arrPagamentos as $pagamento) {

               $dbRecebimentosNegociacoes->insert($pagamento);
            }
         }
		 
			$dbVeiculos->edt($_POST['id_veiculo'], $arrKM);
		 
		}


      $this->view->id_empresa = $_SESSION['sessionUser']['id_empresa'];

      $this->view->financeiras = $dbFinanceira->fetchAll("id_empresa = " . $_SESSION['sessionUser']['id_empresa'] . " AND tipo = 0");
      $this->view->despachante = $dbFinanceira->fetchAll("id_empresa = " . $_SESSION['sessionUser']['id_empresa'] . " AND tipo = 1");
      $this->view->arrGerentes = $dbUsuarios->getUsuarioComissao($_SESSION['sessionUser']['id_empresa'], 4);
      $this->view->arrSupervisor = $dbUsuarios->getUsuarioComissao($_SESSION['sessionUser']['id_empresa'], 9);



      $arrNegociacoes = $dbNegociacoes->getNegociacoes($this->_getParam('id'));

      //var_export($arrNegociacoes);

      $arrRecebimento = $dbRecebimentosNegociacoes->getRecebimentos($arrNegociacoes[0]['id']);

      $this->view->negociacao = $arrNegociacoes[0];
      $this->view->recebimentos = $arrRecebimento;
      $this->view->idNegociacao = $this->_getParam('id');
   }

   public function listaAction() {

      $this->validaAcesso('listar_vendas');

      $dbNegociacoes = new Application_Model_DbTable_Negociacoes();

      $arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

      $arr['data_inicial_abertura'] = @date("Y") . "-" . @date("m") . "-01";
      $data = @date("Y-m-d", mktime(0, 0, 0, @date("m") + 1, 0, @date("Y")));
      $arr['data_final_abertura'] = $data;
      $arr['lista'] = true;
	   $arr['id_negociacao_compra'] = 2;

      if ($_SESSION['sessionUser']['id_perfil'] == 3) {
         $arr['id_vendedor'] = $_SESSION['sessionUser']['id'];
      }

      if ($this->getRequest()->isPost()){

         $arr['parcial'] = true;
         if($this->_getParam('nome')){
            $arr['nome'] = preg_replace('/[^\w]/', '', $this->_getParam('nome'));
         }
          if($this->_getParam('placa')){
            $arr['placa'] = $this->_getParam('placa');
         }
         if($this->_getParam('str_modelo')){
            $arr['str_modelo'] = $this->_getParam('str_modelo');
         }

   		if(isset($arr['placa']) && $arr['placa'] != "") {
   		
   			$arrTemp = explode("-",$arr['placa']);
   			if(!isset($arrTemp[1]) && strlen($arr['placa']) < 8){
   			
   				$arr['placa'] = substr($arr['placa'],0,3)."-".substr($arr['placa'],3);
   			
   			}
   		
   		}
		 
         if($this->_getParam('aprovada')){
            $arr['aprovada'] = $this->_getParam('aprovada');
         }

         if($this->_getParam('data_inicial')){
            $arr['data_inicial_abertura'] = implode("-", array_reverse(explode("/", $this->_getParam('data_inicial'))));
         }else{
            $arr['data_inicial_abertura'] = null;
         }

         if($this->_getParam('data_final')){
            $arr['data_final_abertura'] = implode("-", array_reverse(explode("/", $this->_getParam('data_final'))));
         }else{
            $arr['data_final_abertura'] = null;
         }

      }

		$arrNegociacoes = $dbNegociacoes->_get($arr);
		
      $arrNegociacoesTroca = array();
		if(isset($arr['placa'])){
			$arrNegociacoesTroca = $dbNegociacoes->_getTroca($arr);
		}

      if(count($arrNegociacoes) > 0){
         $this->view->negociacoes = $arrNegociacoes;
      }
      if(count($arrNegociacoesTroca) > 0){
   		$this->view->negociacoesTroca = $arrNegociacoesTroca;
      }

      // expose the search dates back to the view (used by form inputs)
      if(isset($arr['data_inicial_abertura']) && $arr['data_inicial_abertura']){
         $this->view->data_inicial = implode("/", array_reverse(explode("-", $arr['data_inicial_abertura'])));
      } else {
         $this->view->data_inicial = '';
      }
      if(isset($arr['data_final_abertura']) && $arr['data_final_abertura']){
         $this->view->data_final = implode("/", array_reverse(explode("-", $arr['data_final_abertura'])));
      } else {
         $this->view->data_final = '';
      }

      // passar resumo por vendedor/status para a view
      $this->view->resumoVendedores = $dbNegociacoes->getResumoVendedores($arr);

	}

	
   public function delAction() {

      $dbN = new Application_Model_DbTable_Negociacoes();
      $dbV = new Application_Model_DbTable_Veiculos();

      $dbV->update(array('excluido' => 1, 'id_negociacao' => 'null'), "id_negociacao = " . $this->_getParam('id'));
      $dbV->update(array('vendido' => 0), "id_negociacao = " . $this->_getParam('id'));
      $dbN->delete("id = " . $this->_getParam('id'));

      $this->_helper->redirector->gotoUrl("negociacoes/lista");
	  
   }
   
    public function delCompraAction(){

      $dbN = new Application_Model_DbTable_Negociacoes();
      $dbV = new Application_Model_DbTable_Veiculos();

      $dbV->update(array('excluido' => 1, 'id_negociacao' => 'null'), "id_negociacao = " . $this->_getParam('id'));
      $dbV->update(array('vendido' => 0), "id_negociacao = " . $this->_getParam('id'));
      $dbN->delete("id = " . $this->_getParam('id'));

      $this->_helper->redirector->gotoUrl("negociacoes/lista-compras");
   }
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
	public function corretorasAction() {

		$this->validaAcesso('listar_vendas');

	}
	
	
	
	
	
	
	
	
	public function impressaoClienteCorretoraAction() {

		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
	
		$this->validaAcesso('listar_vendas');

		$dbClientes = new Application_Model_DbTable_Clientes();
		
		$arr['id'] = $this->_getParam('id');
		
		$arr['corretora'] = true;
		
		$arrClientes = $dbClientes->_get($arr);

		
		
		
		
		$this->view->arrCliente = $arrClientes[0];
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
	
		$arrVeiculo = $dbVeiculos->_get(array("id"=>$this->_getParam('id_veiculo')));

		$this->view->arrVeiculo = $arrVeiculo[0];

	}
   
   
   
   
   
   
   
   
	private function enviaDadosCorretora($idEmpresa, $idCliente, $idVeiculo, $idUsuarioVendedor, $valorVenda){
	
		$dbIndicacoesCorretoras = new Application_Model_DbTable_IndicacoesCorretoras();
		
		if(!$dbIndicacoesCorretoras->getIndicacaoPorVeiculoCliente($idVeiculo, $idCliente)){
	
			$dbClientes = new Application_Model_DbTable_Clientes();
			$dbEmpresas = new Application_Model_DbTable_Empresas();

			$arrCliente = $dbClientes->_get(array("id"=>$idCliente));
			
			
			
			$arr['cidade'] = "N40 T3m c1d4d3";
			$arr['tipo_empresa'] = 1;
			
			if($arrCliente[0]['cidade']){
			
				$arr['cidade'] = $arrCliente[0]['cidade'];

			}
			
			
			$arrCorretora = $dbEmpresas->_get($arr);
			
			
			if($arrCorretora[0]['id']){
				
				$idCorretora = $arrCorretora[0]['id'];
				$emailCorretora = $arrCorretora[0]['email'];
				$arrEmpresa = $dbEmpresas->_get(array("id"=>$idEmpresa));
			
			}else{
			
				$arr1['id_empresa'] = $idEmpresa;
			
				$arrEmpresa = $dbEmpresas->_get($arr1);
				$arrCorretora2 = $dbEmpresas->_get(array("cidade"=>$arrEmpresa[0]['cidade'], "tipo_empresa"=>1));
				$idCorretora = $arrCorretora2[0]['id'];
				$emailCorretora = $arrCorretora2[0]['email'];
			
			}
			
			
			
			if($idCorretora){

				$arrDados['id_empresa'] = $idEmpresa;
				$arrDados['id_corretora'] = $idCorretora;
				$arrDados['id_cliente'] = $idCliente;
				$arrDados['id_veiculo'] = $idVeiculo;
				$arrDados['id_usuario_vendedor'] = $idUsuarioVendedor;
				$arrDados['cpf'] = $arrCliente[0]['cpf'];
				$arrDados['tel1'] = $arrCliente[0]['tel1'];
				$arrDados['tel2'] = $arrCliente[0]['tel2'];
				//$arrDados['valor_venda'] = $valorVenda;
				$arrDados['data_venda'] = @date("Y-m-d");
				
				$dbIndicacoesCorretoras = new Application_Model_DbTable_IndicacoesCorretoras();
				
				if($dbIndicacoesCorretoras->add($arrDados)){
			
					$assunto = "Nova indicação do sistema Meu Car";
					
					$corpo = "
						<html>
							<head>
								<meta content='text/html; charset=utf-8' http-equiv='Content-Type'>
								<title>MeuCar</title>
								<style>
									table tr td{
										#border:solid 1px;
										width:100%;
									}
									
									a img{
										width:200px;
										float:right;
									}
								</style>
							</head>
							<body>
								<table style='background-color:#E8E8E8; border:1px solid #CCCCCC; color:#666666; font:14px Arial, Helvetica, sans-serif;'>
									<tr><td colspan='2' style='height:10px;'></td></tr>
									<tr><td colspan='2'><div style='background-color: #CCCCCC;'>Você tem uma nova indicação do sistema Meu Car, por favor acesse sistema para visualizar os dados.</div><br></td></tr>
									<tr><td colspan='2' style='height:10px;'></td></tr>
									<tr><td colspan='2' style=''><br><center><img style='width:150px;' src='http://sistemameucar.com.br//arquivos_site/images/logo-meu-car.png'/><br><a href='http://sistemameucar.com.br'>sistemameucar.com.br</a></center><br></td></tr>
								</table>
							</body>
						</html>";


					$config = array(
						'auth' => 'login',
						'username' => 'sistemameucar@sistemameucar.com.br',
						'password' => 'g010502g',
						'port' => '587'
					);

					$transport = new Zend_Mail_Transport_Smtp('smtp.sistemameucar.com.br', $config);

					$mail = new Zend_Mail('UTF-8');
					$mail->setBodyHtml($corpo);
					$mail->setFrom('sistemameucar@sistemameucar.com.br');
					$mail->addTo($emailCorretora);
					//$mail->addBcc('icomenezes@hotmail.com');
					$mail->setSubject($assunto);

					try{

						if($attach){

							$mail->createAttachment(file_get_contents($attach), 'text/plain', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $attach);
					 
						}

						return $mail->send($transport);
						
					}catch(Exception $e){

						echo $e->getMessage();
						
					}

				}
				
			}
		
		}
	
	}
   
   
   
   
   
   
   
   
   

	private function mesExtenso($num){
		
		if($num == "01"){
			
			return "Janeiro";
		
		}elseif($num == "02"){
			
			return "Fevereiro";
		
		}elseif($num == "03"){
			
			return "Mar&ccedil;o";
			
		}elseif($num == "04"){
			
			return "Abril";
			
		}elseif($num == "05"){
			
			return "Maio";
			
		}elseif($num == "06"){
		
			return "Junho";
		
		}elseif($num == "07"){
			
			return "Julho";
		
		}elseif($num == "08"){
			
			return "Agosto";
			
		}elseif($num == "09"){
		
			return "Setembro";
		
		}elseif($num == "10"){
		
			return "Outubro";
			
		}elseif($num == "11"){
		
			return "Novembro";
			
		}elseif($num == "12"){
		
			return "Dezembro";
		
		}
		
	}

   private function valor_extenso($valor = 0, $maiusculas = false) {

      // verifica se tem virgula decimal
      if (strpos($valor, ",") > 0) {
         // retira o ponto de milhar, se tiver
         $valor = str_replace(".", "", $valor);

         // troca a virgula decimal por ponto decimal
         $valor = str_replace(",", ".", $valor);
      }
      $singular = array("", "", "mil", "milh&atilde;o", "bilh&atilde;o", "trilh&atilde;o", "quatrilh&atilde;o");
      $plural = array("", "", "mil", "milh&otilde;es", "bilh&otilde;es", "trilh&otilde;es", "quatrilh&otilde;es");

      $c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
      $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
      $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
      $u = array("", "um", "dois", "tr&ecirc;s", "quatro", "cinco", "seis", "sete", "oito", "nove");

      $z = 0;

      $valor = number_format($valor, 2, ".", ".");
      $inteiro = explode(".", $valor);

      $cont = count($inteiro);

      for ($i = 0; $i < $cont; $i++)
         for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
            $inteiro[$i] = "0" . $inteiro[$i];

      $fim = $cont - ($inteiro[$cont - 1] > 0 ? 1 : 2);
      for ($i = 0; $i < $cont; $i++) {
         $valor = $inteiro[$i];
         $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
         $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
         $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

         $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
         $t = $cont - 1 - $i;
         $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
         if ($valor == "000")
            $z++; elseif ($z > 0)
            $z--;
         if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
            $r .= (($z > 1) ? " de " : "") . $plural[$t];
         if ($r)
            $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
      }

      if (!$maiusculas) {

         return($rt ? $rt : "zero");
      } elseif ($maiusculas == "2") {

         return (strtoupper($rt) ? strtoupper($rt) : "Zero");
      } else {

         return (ucwords($rt) ? ucwords($rt) : "Zero");
      }
   }

   private function valorExtensoDinheiro($valor = 0, $maiusculas = false) {

      // verifica se tem virgula decimal
      if (strpos($valor, ",") > 0) {
         // retira o ponto de milhar, se tiver
         $valor = str_replace(".", "", $valor);

         // troca a virgula decimal por ponto decimal
         $valor = str_replace(",", ".", $valor);
      }
      $singular = array("centavo", "real", "mil", "milh&atilde;o", "bilh&atilde;o", "trilh&atilde;o", "quatrilh&atilde;o");
      $plural = array("centavos", "reais", "mil", "milh&otilde;es", "bilh&otilde;es", "trilh&otilde;es", "quatrilh&otilde;es");

      $c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
      $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
      $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
      $u = array("", "um", "dois", "tr&ecirc;s", "quatro", "cinco", "seis", "sete", "oito", "nove");

      $z = 0;

      $valor = number_format($valor, 2, ".", ".");
      $inteiro = explode(".", $valor);

      $cont = count($inteiro);

      for ($i = 0; $i < $cont; $i++)
         for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
            $inteiro[$i] = "0" . $inteiro[$i];

      $fim = $cont - ($inteiro[$cont - 1] > 0 ? 1 : 2);
      for ($i = 0; $i < $cont; $i++) {
         $valor = $inteiro[$i];
         $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
         $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
         $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

         $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
         $t = $cont - 1 - $i;
         $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
         if ($valor == "000")
            $z++; elseif ($z > 0)
            $z--;
         if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
            $r .= (($z > 1) ? " de " : "") . $plural[$t];
         if ($r)
            $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
      }

      if (!$maiusculas) {

         return($rt ? $rt : "zero");
      } elseif ($maiusculas == "2") {

         return (strtoupper($rt) ? strtoupper($rt) : "Zero");
      } else {

         return (ucwords($rt) ? ucwords($rt) : "Zero");
      }
   }
   
	
	
	
	private function getEstoqueIcarros(){
	
		$dbEmpresas = new Application_Model_DbTable_Empresas();
	
		$arrEmpresa = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);

		try{
		
			$url = 'https://paginasegura.icarros.com.br/services/icarroswebservice?wsdl';

			$client = new SoapClient($url, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
			
			$client->__setLocation('http://www.icarros.com.br/services/icarroswebservice');
			
			$token = $client->autenticarAcesso($arrEmpresa[0]['login_icarros'], $arrEmpresa[0]['senha_icarros']);
		
		}catch (SoapFault $exception) {
		
			//echo $exception->getMessage();
			
			return "erro";
			
		}
		
		$arrAnu = get_object_vars($client->obterListaAnunciantes($token));

     // if($arrAnu){

   		$arrAnuci = get_object_vars($arrAnu['dados']);
   		$idAnunciante = $arrAnuci['id'];

   		$arr = get_object_vars($client->obterEstoqueAnunciante($token, $idAnunciante));
   		
   		$arrAnuncio = $arr['anuncios'];

   		if($arrAnuncio){
   		
   			foreach($arrAnuncio as $keyV=>$anuncios){
   		
   				foreach($anuncios as $keyH=>$anuncio){
   				
   					$arrDados = get_object_vars($anuncios);

   					$arrVeiculo[$arrDados['id']][$keyH] = $anuncio;
   				
   				}
   			
   			}
   		
   		}

		//}
		return $arrVeiculo;
	
	}
	
	
	private function getEstoqueWebmotors($cnpj, $login, $senha){
	
		try{
	
			$url = "http://www.webmotors.com.br/IntegracaoRevendedor/wsLoginSistemaRevendedor.asmx?wsdl";
				

			$clientWeb = new SoapClient($url,array('trace' => 1, 'soap_version'=> SOAP_1_2));

			$tokenWeb = $clientWeb->autenticar(array("cnpj"=>$cnpj, "email"=>$login, "senha"=>$senha));

			$arrWeb = get_object_vars($tokenWeb);
			$arrHash = get_object_vars($arrWeb['autenticarResult']);
			$hash = $arrHash['HashAutenticacao'];

			$url2 = "http://www.webmotors.com.br/IntegracaoRevendedor/wsEstoqueRevendedorWebMotors.asmx?wsdl";
			
			$clientWeb2 = new SoapClient($url2, array('trace' => 1));
			
			$params = array("pHashAutenticacao"=>$hash);
			
			$arrEstoqueWeb = get_object_vars($clientWeb2->ObterEstoqueAtual($params));
			$arrEstoqueWeb2 = get_object_vars($arrEstoqueWeb['ObterEstoqueAtualResult']);
			$arrEstoqueWeb3 = $arrEstoqueWeb2['Anuncio'];
	
	
			if(!is_array($arrEstoqueWeb3)){
				
				$arWebEstoque = get_object_vars($arrEstoqueWeb3);
			
				$arrWebEstoque[0]['codigo_anuncio'] = $arWebEstoque['CodigoAnuncio'];
				$arrWebEstoque[0]['codigo_modalidade'] = $arWebEstoque['CodigoModalidade'];
				$arrWebEstoque[0]['placa'] = $arWebEstoque['Placa'];
			
			
			}else{
	
				foreach($arrEstoqueWeb3 as $key=>$web){
				
					$arWebEstoque = get_object_vars($web);
					
					$arrWebEstoque[$key]['codigo_anuncio'] = $arWebEstoque['CodigoAnuncio'];
					$arrWebEstoque[$key]['codigo_modalidade'] = $arWebEstoque['CodigoModalidade'];
					$arrWebEstoque[$key]['placa'] = $arWebEstoque['Placa'];

				}
			
			}

			return $arrWebEstoque;

		}catch (SoapFault $exception){
	
			echo $exception->getMessage();
		
		}

	}
	
	private function excluiVeiculoWebmotors($cnpj, $login, $senha, $codigoAnuncio){
	
		try{
		
			$url = "http://www.webmotors.com.br/IntegracaoRevendedor/wsLoginSistemaRevendedor.asmx?wsdl";
			$url2 = "http://www.webmotors.com.br/IntegracaoRevendedor/wsEstoqueRevendedorWebMotors.asmx?wsdl";

			$client = new SoapClient($url,array('trace' => 1, 'soap_version'=> SOAP_1_2));

			$token = $client->autenticar(array("cnpj"=>$cnpj, "email"=>$login, "senha"=>$senha));

			$arr = get_object_vars($token);
			$arrHash = get_object_vars($arr['autenticarResult']);
			$hash = $arrHash['HashAutenticacao'];

			$client2 = new SoapClient($url2, array('trace' => 1));

			$params = array("pHashAutenticacao"=>$hash, "pCodigoAnuncio"=>$codigoAnuncio, "pMotivoExclusao"=>"3");
			
			$arrWeb = get_object_vars($client2->ExcluirCarro($params));
				

		}catch (SoapFault $exception){
	
			echo $exception->getMessage();
		
		}
		
		$retorno = get_object_vars($arrWeb['ExcluirCarroResult']);
			
		//echo $retorno['CodigoRetorno'];
	
	}
	
	
	public function geraXmlNfeClienteAction(){
		
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		$dbClientes = new Application_Model_DbTable_Clientes();
		$dbCidades = new Application_Model_DbTable_Cidades();
		
		$arrCliente = $dbClientes->getClienteContrato($this->_getParam('id_cliente'));
		
		$arrCodCidade = $dbCidades->getCidadeString();
		
		$strCodigoMunicipio = "";

		foreach($arrCodCidade as $codCidade){

			if(mb_convert_encoding($codCidade['Nome'], 'UTF-8', 'ISO-8859-1') == $arrCliente[0]['cidade']){

				
				$strCodigoMunicipio = trim($codCidade['CodigoMunicipio']);

			
			}
		
		}

		$strXml = '<sistema versao="1.02" xmlns="http://www.portalfiscal.inf.br/nfe">';
		
		$strXml .= '<dest>';
		
		if(strpos($arrCliente[0]['cpf'], "/") === true){
			
			$strXml .= '<CNPJ>'.trim(str_replace("/","", str_replace("-","", str_replace(".","", $arrCliente[0]['cpf'])))).'</CNPJ>';
		
		}else{
		
			$strXml .= '<CPF>'.trim(str_replace("/","", str_replace("-","", str_replace(".","", $arrCliente[0]['cpf'])))).'</CPF>';

		}
		
		$strXml .= '<xNome>'.trim($arrCliente[0]['nome']).'</xNome>';

		$strXml .= '<enderDest>';
		
		if($arrCliente[0]['endereco']){
		
			$strXml .= '<xLgr>'.trim($arrCliente[0]['endereco']).'</xLgr>';
		
		}
		
		if($arrCliente[0]['numero_endereco']){
		
			$strXml .= '<nro>'.trim($arrCliente[0]['numero_endereco']).'</nro>';
		
		}
		
		if($arrCliente[0]['complemento']){
		
			$strXml .= '<xCpl>'.trim($arrCliente[0]['complemento']).'</xCpl>';
		
		}
		
		if($arrCliente[0]['bairro']){
		
			$strXml .= '<xBairro>'.trim($arrCliente[0]['bairro']).'</xBairro>';
		
		}
		
		if($strCodigoMunicipio != ""){
		
			$strXml .= '<cMun>'.trim($strCodigoMunicipio).'</cMun>';
		
		}

		
		if($arrCliente[0]['cidade']){
		
			$strXml .= '<xMun>'.trim($arrCliente[0]['cidade']).'</xMun>';
		
		}
		
		if($arrCliente[0]['estado']){
		
			$strXml .= '<UF>'.trim($arrCliente[0]['estado']).'</UF>';
		
		}
		
		if($arrCliente[0]['cep']){
		
			$strXml .= '<CEP>'.trim(str_replace("-", "",$arrCliente[0]['cep'])).'</CEP>';
		
		}

		$strXml .= '<cPais>1058</cPais>';
		$strXml .= '<xPais>BRASIL</xPais>';
	
		if($arrCliente[0]['tel1']){
		
			$strXml .= '<fone>'.trim(str_replace(" ", "", str_replace(")", "",str_replace("(", "",str_replace("-", "",$arrCliente[0]['tel1']))))).'</fone>';
		
		}elseif($arrCliente[0]['tel2']){
			
			$strXml .= '<fone>'.trim(str_replace(" ", "", str_replace(")", "",str_replace("(", "",str_replace("-", "",$arrCliente[0]['tel2']))))).'</fone>';
		
		}

		$strXml .= '</enderDest>';
		$strXml .= '<email>'.trim($arrCliente[0]['email']).'</email>';
		$strXml .= '</dest>';
		$strXml .= '</sistema>';

		$this->view->nome = trim($arrCliente[0]['nome']);
		$this->view->strXml = $strXml;

	}
	
	
	
	public function geraXmlNfeProdutoAction(){
		
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$arrVeiculo = $dbVeiculos->getVeiculoEstoque($this->_getParam('id_veiculo'));
		
		if($arrVeiculo[0]['descricao_site']){
			
			$arrVeiculo[0]['modelo'] = $arrVeiculo[0]['descricao_site'];
		
		}
		
		$arrModelo = explode(" ", $arrVeiculo[0]['modelo']);
		
		$arrMarca = explode("-", $arrVeiculo[0]['marca']);
		
		if($arrMarca[1]){
			
			$arrVeiculo[0]['marca'] = trim($arrMarca[1]);
		
		}
		
		$strModelo = strtoupper(mb_convert_encoding($arrVeiculo[0]['marca'], 'UTF-8', 'ISO-8859-1')."-".substr($arrModelo[0]." ".$arrModelo[1],0,16)." ANO FAB:".$arrVeiculo[0]['ano_fabricacao']."/ANO MOD:".$arrVeiculo[0]['ano_modelo']." CHASSI:".$arrVeiculo[0]['chassi']." RENAVAM:".$arrVeiculo[0]['renavam']." PLACA:".str_replace("-", "", $arrVeiculo[0]['placa']));

		$strXml = '<sistema versao="1.02" xmlns="http://www.portalfiscal.inf.br/nfe">';
		
		$strXml .= '<det>';
		
		$strXml .= '<prod>';
		
		$strXml .= '<cProd>'.trim($arrVeiculo[0]['id']).'</cProd>';

		$strXml .= '<xProd>'.trim(substr($strModelo,0,120)).'</xProd>';
		
		$strXml .= '<qTrib>1</qTrib>';

		$strXml .= '</prod>';
		
		$strXml .= '</det>';
		
		$strXml .= '</sistema>';

		$this->view->modeloPlaca = trim($arrModelo[0]." ".$arrModelo[1]."-".str_replace("-", "",$arrVeiculo[0]['placa']));
		$this->view->strXml = $strXml;

	}
	
	
	public function geraXmlNfeTrocaAction(){
		
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$arrVeiculo = $dbVeiculos->getVeiculoEstoque($this->_getParam('id'));
		
		if($arrVeiculo[0]['descricao_site']){
			
			$arrVeiculo[0]['modelo'] = $arrVeiculo[0]['descricao_site'];
		
		}
		
		$arrModelo = explode(" ", $arrVeiculo[0]['modelo']);
		
		$arrMarca = explode("-", $arrVeiculo[0]['marca']);
		
		if($arrMarca[1]){
			
			$arrVeiculo[0]['marca'] = trim($arrMarca[1]);
		
		}
		
		$strModelo = strtoupper(mb_convert_encoding($arrVeiculo[0]['marca'], 'UTF-8', 'ISO-8859-1')."-".substr($arrModelo[0]." ".$arrModelo[1],0,16)." ANO FAB:".$arrVeiculo[0]['ano_fabricacao']."/ANO MOD:".$arrVeiculo[0]['ano_modelo']." CHASSI:".$arrVeiculo[0]['chassi']." RENAVAM:".$arrVeiculo[0]['renavam']." PLACA:".str_replace("-", "", $arrVeiculo[0]['placa']));

		$strXml = '<sistema versao="1.02" xmlns="http://www.portalfiscal.inf.br/nfe">';
		
		$strXml .= '<det>';
		
		$strXml .= '<prod>';
		
		$strXml .= '<cProd>'.trim($arrVeiculo[0]['id']).'</cProd>';

		$strXml .= '<xProd>'.trim(substr($strModelo,0,120)).'</xProd>';
		
		$strXml .= '<qTrib>1</qTrib>';

		$strXml .= '</prod>';
		
		$strXml .= '</det>';
		
		$strXml .= '</sistema>';

		$this->view->modeloPlaca = trim($arrModelo[0]." ".$arrModelo[1]."-".str_replace("-", "",$arrVeiculo[0]['placa']));
		$this->view->strXml = $strXml;

	}

}

?>
