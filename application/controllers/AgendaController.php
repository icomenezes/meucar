<?php

header("Content-Type: text/html; charset=UTF-8", true);

class AgendaController extends Zend_Controller_Action {

   public function init() {

      $this->view->titulo = "Agenda";

      Zend_Session::start();
   }

   public function validaAcesso($require) {

      if (!in_array($require, $_SESSION['sessionUser']['permissoes'])) {
         $this->_helper->redirector->gotoUrl(URL . "/index/bad-access");
      }
   }

   private function listDir($start) {

   	$arr = null;

      $handle = opendir($start);

      if (is_dir($start)) {

         while (false !== ($file = readdir($handle))) {

            if ($file != '.' && $file != '..') {

               $arr[] = $file;
            }
         }
      }

      return $arr;
   }


   	public function concessionarioAction(){

      	$this->validaAcesso('listar_agenda');



  	}

   
   public function agendaConcessionarioAction(){

      $this->validaAcesso('listar_agenda');
      $start = "propaganda";
      $nome = $this->listDir($start);
      $nomeImg = $nome[0];
      $strVendedores = "";

      if (isset($nomeImg)){

         $dbLog = new Application_Model_DbTable_Log();

         $arrL = $dbLog->_get(array('id_usuario' => $_SESSION['sessionUser']['id'], 'data' => @date('Y-m-d')));

         if (count($arrL) < 3) {

			list($w, $h) = getimagesize("propaganda/" . $nomeImg . "");

			echo $w;
			
            //echo "<script type=\"text/javascript\">window.open('" . URL . "banner/show','', 'height=" . $h . ", width=" . $w . ", scrollbars = no, status=no, location = no, toolbar = no, menubar = no')</script>";
         
		 }

         $idPerfil = $_SESSION['sessionUser']['id_perfil'];

         if (isset($idPerfil) && $idPerfil == VENDEDOR) {

            $display = "display:none";
         }

         //var_export(SECRETARIA);

         $this->view->display = $display;

         if (isset($idPerfil) && ($idPerfil == CONCESSIONARIO || $idPerfil == SECRETARIA)) {

            $dsplay = "display:block";
			
         } else {

            $dsplay = "display:none";
         }

         $this->view->dsplay = $dsplay;
      }

      $antes = @date('-m-d', mktime(1, 1, 1, @date('m'), @date('d') - 1));
      $hoje = @date('-m-d');
      $amanha = @date('-m-d', mktime(1, 1, 1, @date('m'), @date('d') + 1));
      $depois = @date('-m-d', mktime(1, 1, 1, @date('m'), @date('d') - 2));
      $ultimo = @date('-m-d', mktime(1, 1, 1, @date('m'), @date('d') - 3));

      $arrFiltro['antes'] = $antes;
      $arrFiltro['hoje'] = $hoje;
      $arrFiltro['amanha'] = $amanha;
      $arrFiltro['depois'] = $depois;
      $arrFiltro['ultimo'] = $ultimo;

      //var_export($_SESSION);exit;

      $dbC = new Application_Model_DbTable_Clientes();
      
      if(isset($_SESSION['sessionUser']['id_empresa'])){
      
         $this->view->aniversariantes = $dbC->aniversario($arrFiltro);

      }
      
      $dbL = new Application_Model_DbTable_LancamentosFinanceiros();
      $this->view->pagamentos = $dbL->_get(array('nao_baixado' => 1, "id_empresa" => $_SESSION['sessionUser']['id_empresa']));

		if(isset($_SESSION['sessionUser']['id_empresa'])) {
		
			$dbUsuarios = new Application_Model_DbTable_Usuarios();
			$dbMotivos = new Application_Model_DbTable_Motivos();
			$idVendedor = "";

			foreach($dbUsuarios->getVendedores() as  $vendedor){
				if($vendedor['id'] != $idVendedor){
					$strVendedores .= "<option value='".$vendedor['id']."'>".$vendedor['nome']."</option>";
				}
				$idVendedor = $vendedor['id'];
			}

			$arrMotivos = $dbMotivos->getMotivos();

			$this->view->arrMotivos = $arrMotivos;
			$this->view->strVendedores = $strVendedores;
			
		}
	
	}
   

   public function agendaAction(){

      $this->validaAcesso('listar_agenda');
      $start = "propaganda";
      $nome = $this->listDir($start);
      $nomeImg = $nome[0];

      if (isset($nomeImg)) {

         $dbLog = new Application_Model_DbTable_Log();

         $arrL = $dbLog->_get(array('id_usuario' => $_SESSION['sessionUser']['id'], 'data' => @date('Y-m-d')));

        if (isset($arrL) && count($arrL) < 2) {

            list($w, $h) = getimagesize("propaganda/" . $nomeImg . "");

            //echo "<script type=\"text/javascript\">window.open('" . URL . "/banner/show','', 'height=" . $h . ", width=" . $w . ", scrollbars = no, status=no, location = no, toolbar = no, menubar = no')</script>";
         }

         $idPerfil = $_SESSION['sessionUser']['id_perfil'];

         if (isset($idPerfil) && $idPerfil == VENDEDOR) {

            $display = "display:none";
         }

         //var_export(SECRETARIA);

         $this->view->display = $display;

         if (isset($idPerfil) && ($idPerfil == CONCESSIONARIO || $idPerfil == SECRETARIA)) {

            $dsplay = "display:block";
         } else {

            $dsplay = "display:none";
         }

         $this->view->dsplay = $dsplay;
      }

      $antes = @date('-m-d', mktime(1, 1, 1, @date('m'), @date('d') - 1));
      $hoje = @date('-m-d');
      $amanha = @date('-m-d', mktime(1, 1, 1, @date('m'), @date('d') + 1));
      $depois = @date('-m-d', mktime(1, 1, 1, @date('m'), @date('d') - 2));
      $ultimo = @date('-m-d', mktime(1, 1, 1, @date('m'), @date('d') - 3));

      $arrFiltro['antes'] = $antes;
      $arrFiltro['hoje'] = $hoje;
      $arrFiltro['amanha'] = $amanha;
      $arrFiltro['depois'] = $depois;
      $arrFiltro['ultimo'] = $ultimo;

      //var_export($_SESSION);exit;

      $dbC = new Application_Model_DbTable_Clientes();
      
      if(isset($_SESSION['sessionUser']['id_empresa'])) {
      
         $this->view->aniversariantes = $dbC->aniversario($arrFiltro);

      }
      
		$dbL = new Application_Model_DbTable_LancamentosFinanceiros();
		$this->view->pagamentos = $dbL->_get(array('nao_baixado' => 1, "id_empresa" => $_SESSION['sessionUser']['id_empresa']));

		$dbMotivos = new Application_Model_DbTable_Motivos();
		$arrMotivos = $dbMotivos->getMotivos();

		$this->view->arrMotivos = $arrMotivos;
   
   }

   public function ajaxAction(){

      $layout = $this->_helper->layout();
      $layout->setLayout('no-layout');

      if(empty($_SESSION['sessionUser']['id_empresa'])) return;

      $idUsuario = $_SESSION['sessionUser']['id'];
      $idPerfil = $_SESSION['sessionUser']['id_perfil'];
      $idEmpresa = $_SESSION['sessionUser']['id_empresa'];
      $arrFiltro['lista'] = true;

	if ($this->_getParam('fn') == 'getNegociacoesConcessionario'){

		$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
		$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
		$dbRecebimentosNegociacoes = new Application_Model_DbTable_RecebimentosNegociacoes();
			
		$arrFiltro['agenda'] = true;

        if (isset($idPerfil) && $idPerfil != CONCESSIONARIO) {
            $arrFiltro['id_vendedor'] = $idUsuario;
        }

        $arrFiltro['id_empresa'] = $idEmpresa;
        $arrFiltro['id_perfil'] = $idPerfil;

        if($this->_getParam('agenda') == 1){
			$arrFiltro['buscaTroca'] = false;
		}else{
			$arrFiltro['buscaTroca'] = true;
		}
		
		$arrFiltro['placa'] = $this->_getParam('placa');
		$arrFiltro['str_modelo'] = $this->_getParam('str_modelo');

        $dataInicial = $this->_getParam('dataInicial');
        $dataFinal = $this->_getParam('dataFinal');

        if (isset($dataInicial) && isset($dataFinal)) {

            $dataTmp = explode("/", $dataInicial);
            $dataInicial = implode("-", array_reverse($dataTmp));

            $dataTmp = explode("/", $dataFinal);
            $dataFinal = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_inicial_abertura'] = $dataInicial;
            $arrFiltro['data_final_abertura'] = $dataFinal;
         
		}elseif(isset($dataInicial)){

            $dataTmp = explode("/", $dataInicial);
            $dataInicial = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_inicial_abertura'] = $dataInicial;
            $arrFiltro['data_final_abertura'] = @date("Y-m-d");
			
        }elseif (isset($dataFinal)) {

            $dataTmp = explode("/", $dataFinal);
            $dataFinal = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_inicial_abertura'] = "1900-01-01";
            $arrFiltro['data_final_abertura'] = $dataFinal;
			
        }else{
			
			$arrFiltro['data_inicial_abertura'] = $mesNum = @date("Y-m-d", mktime(0, 0, 0, @date("m")-1, 01, @date("Y")));
            //$arrFiltro['data_final_abertura'] = @date("Y-m-d");
		 
		}

         //echo $arrFiltro['data_final'];

         $arrNegociacoes = $dbNegociacoes->_get($arrFiltro);
		
		$options = "<option value='0'>Selecione</option>";
		
		
		for($i = @date("m"); $i > (@date("m")-25); $i--){
	
			$mesNum = @date("m", mktime(0, 0, 0, $i, 01, @date("Y")));
			$anoNum = @date("Y", mktime(0, 0, 0, $i, 01, @date("Y")));
			$ultimoDia = @date("d", mktime(0, 0, 0, $i+1, 0, @date("Y")));

			switch ($mesNum){
				case 1:
					$option =  "Janeiro - ".$anoNum;
					break;
							
				case 2:
					$option =  "Fevereiro - ".$anoNum;
					break;
						
				case 3:
					$option =  "Março - ".$anoNum;
					break;
							
				case 4:
					$option =  "Abril - ".$anoNum;
					break;
							
				case 5:
					$option =  "Maio - ".$anoNum;
					break;
							
				case 6:
					$option =  "Junho - ".$anoNum;
					break;
							
				case 7:
					$option =  "Julho - ".$anoNum;
					break;
							
				case 8:
					$option =  "Agosto - ".$anoNum;
					break;
							
				case 9:
					$option =  "Setembro - ".$anoNum;
					break;
						
				case 10:
					$option =  "Outubro - ".$anoNum;
					break;
							
				case 11:
					$option =  "Novembro - ".$anoNum;
					break;
							
				case 12:
					$option =  "Dezembro - ".$anoNum;
					break;
			
			}
					
			$options .= "<option value='".$ultimoDia."_".$mesNum."_".$anoNum."'>".$option."</option>";
				
		}
		
		
         echo "<form  class=\"form\" ><div class=\"inline\" style=\"width:98%; height:auto;\"><label style=\"font-size:20px;padding:0px;padding-left:0px;\">Negocia&ccedil;&otilde;es</label></div>
			
			<div class=\"inline\">
			<label class=\"label\">Mês</label>
			<select id=\"mes_negociacoes\" class=\"text\" onChange=\"enviaPeriodo(this.value, 'negociacoes');\">
			".$options."
			</select>
			</div>
			
			<div class=\"inline\">
			<label class=\"label\">Data Inicio</label>
			<input type=\"text\" id=\"data_inicial_negociacoes\" name=\"data_inicial_negociacoes\" class=\"text\"/>
			</div>			

			<div class=\"inline\">
			<label class=\"label\">Data Fim</label>
			<input type=\"text\" id=\"data_final_negociacoes\" name=\"data_final_negociacoes\" class=\"text\"/>
			</div>
			
			<div class=\"inline\">
			<label class=\"label\">Placa</label>
			<input type=\"text\" id=\"placa_negociacoes\" name=\"placa\" class=\"text\"/>
			</div>
			
			<div class=\"inline\">
			<label class=\"label\">Modelo</label>
			<input type=\"text\" id=\"str_modelo_negociacoes\" name=\"str_modelo\" class=\"text\" placeholder=\"Ex.:Celta\" />
            </div>
            
			
			<div class=\"inline\" style=\"margin-top:10px;\">
			<input type=\"button\" id=\"button\" name=\"button\" class=\"btn-small btn-find\" value=\"Pesquisar\" onclick=\"getNegociacoes()\"/>
			<input type=\"button\" id=\"button1\" name=\"button1\" class=\"btn-mini btn-include\" value=\"Add\" onclick=\"javascript:window.location.href='" . URL . "negociacoes/add'\" />
			</div>	
            </form>

			
			<div style='clear:both; width:100%;'>
			
			<style>
				#table_negociacao td{
					border-left:solid 1px #FFFFFF;
					border-bottom:solid 2px #FFFFFF;
					vertical-align:middle;
					padding:3px;
					font-size:11px;
				}
				
				#table_negociacao th{
					border-left:solid 1px;
					border-bottom:solid 1px;
					background-color:#AAAAAA;
					color:#FFFFFF;
					vertical-align:middle;
					text-align:center;
					height:25px;
					padding:3px;
				}
			</style>
			<table style='width:100%;' id='table_negociacao'>		
				<thead>
					<tr>
						<th>Nº</th>
						<th>Negocia&ccedil;&atilde;o</th>
						<th>Ve&iacute;culo Vendido</th>
						<th>Despesas</th>";
		if(true){
		
			echo "<th>Troca</th>";
			
		}
		
		echo "<th>Recebimento</th>
			  <th style='width:150px;'>Resumo</th>
			</tr>
			</thead>
			<tbody>";
			

         //var_export($arrNegociacoes);exit;

		// Batch: busca todas as despesas e recebimentos de uma vez (evita N+1 queries)
		$idsVeiculos = array();
		$idsNegociacoes = array();
		foreach($arrNegociacoes as $neg){
			if(isset($neg['id_veiculo'])) $idsVeiculos[] = $neg['id_veiculo'];
			if(isset($neg['id'])) $idsNegociacoes[] = $neg['id'];
		}

		$arrAllDespesas = $dbDespesasVeiculos->getDespesasByVeiculos($idsVeiculos);
		$despesasPorVeiculo = array();
		foreach($arrAllDespesas as $desp){
			$despesasPorVeiculo[$desp['id_veiculo']][] = $desp;
		}

		$arrAllRecebimentos = $dbRecebimentosNegociacoes->getRecebimentosByNegociacoes($idsNegociacoes);
		$recebimentosPorNegociacao = array();
		foreach($arrAllRecebimentos as $rec){
			$recebimentosPorNegociacao[$rec['id_negociacao']][] = $rec;
		}

		$linha = true;

        foreach ($arrNegociacoes as $negociacao){

            $tempo = explode(" ", $negociacao['data_abertura']);
            $data = explode("-", $tempo[0]);
            $negociacao['data_abertura'] = implode("/", array_reverse($data));
            $negociacao['data_abertura'] = $negociacao['data_abertura'] . " - " . $tempo[1];
			
			if(isset($negociacao['compra']) && $negociacao['compra'] == 1){
            
				$tipo = "Compra";
			
			}elseif(isset($negociacao['placa_troca_1'])) {

               $tipo = "Venda com Troca";
			   
            } else {

               $tipo = "Venda";
            }

            if (isset($negociacao['aprovada']) && $negociacao['aprovada'] == '1') {

				$cor = "#AAFFAA";
			   
            } elseif (isset($negociacao['aprovada']) && $negociacao['aprovada'] == '-1') {

               $cor = "#FFAAAA";
              
            } elseif(isset($negociacao['aprovada']) && $negociacao['aprovada'] == '0'){

               $cor = "#FFFFAA";
            
			}
			
			if(isset($negociacao['descricao_site'])){
			
				$negociacao['modelo'] = $negociacao['descricao_site'];
			
			}
			
			if(isset($negociacao['compra']) && $negociacao['compra'] == 0){

				$url = "javascript:window.location.href='/negociacoes/edt/id/" . $negociacao['id'] . "'";
			
			}else{
			
				$url = "javascript:window.location.href='/negociacoes/edt-compra/id/" . $negociacao['id'] . "'";
				$cor = "#66CCFF";
			}
			
			$arrDespesas = isset($despesasPorVeiculo[$negociacao['id_veiculo']]) ? $despesasPorVeiculo[$negociacao['id_veiculo']] : array();
			
			$strDespesas = "";
			$totalDespesas = 0;
			
			
			foreach($arrDespesas as $despesas){
			
				$strDespesas .= strtoupper($despesas['despesa'].": R$ ".money_format("%i",$despesas['valor']))."\n";
				$totalDespesas += $despesas['valor'];
			
			}
			
			if($strDespesas == ""){
				
				$strDespesas = "N&atilde;o há despesas";
			
			}else{
				
				$strDespesas .= "TOTAL: R$ ".money_format("%i",$totalDespesas);
			
			}
			
			$arrRecebimentos = isset($recebimentosPorNegociacao[$negociacao['id']]) ? $recebimentosPorNegociacao[$negociacao['id']] : array();
			
			$forma = "";
			
			if(isset($arrRecebimentos)) {
			
				foreach($arrRecebimentos as $recebimentos){

                // $formaRcto = $recebimentos['forma'];
                // $formaValor = $recebimentos['valor'];

                // echo 'a forma é :' . $forma .  ' e o valor é : ' . $formaValor;
                // echo '<br>';

                // print_r($recebimentos);
                // echo '<br><br>';

                
                
					if(isset($recebimentos['forma']) && $recebimentos['forma'] == 1){
						
						$forma .= "<br/><b>Cartão de Crédito</b>: ".money_format("%i",$recebimentos['valor']);
					
					}elseif(isset($recebimentos['forma']) && $recebimentos['forma'] == 2){
						
						$forma .= "<br/><b>Cheque</b>: ".money_format("%i",$recebimentos['valor']);
					
					}elseif(isset($recebimentos['forma']) && $recebimentos['forma'] == 3){
						
						$forma .= "<br/><b>Dinheiro</b>: ".money_format("%i",$recebimentos['valor']);
					
					}elseif(isset($recebimentos['forma']) && $recebimentos['forma'] == 4){
						
						$forma .= "<br/><b>Promissórias</b>: ".money_format("%i",$recebimentos['valor']);
					
					}elseif(isset($recebimentos['forma']) && $recebimentos['forma'] == 5){
						
						$forma .= "<br/><b>TED</b>: ".money_format("%i",$recebimentos['valor']);
					
					}elseif(isset($recebimentos['forma']) && $recebimentos['forma'] == 6){
						
						$forma .= "<br/><b>Devolução ao Cliente</b>: ".money_format("%i",$recebimentos['valor']);
					
					}elseif(isset($recebimentos['forma']) && $recebimentos['forma'] == 7){
						
						$forma .= "<br/><b>Débitos do  Veículo de Troca</b>: ".money_format("%i",$recebimentos['valor']);
					
					}elseif(isset($recebimentos['forma']) && $recebimentos['forma'] == 8){
						
						$forma .= "<br/><b>Quitação de  Veículo de Troca</b>: ".money_format("%i",$recebimentos['valor']);
					
					}elseif(isset($recebimentos['forma']) && $recebimentos['forma'] == 11){
						
						$forma .= "<br/><b>PIX</b>: ".money_format("%i",$recebimentos['valor']);
					
					}
				
				}
			
			}else{
		
				$forma = "";
				
			}

            echo "<tr style=\"background-color:$cor\">
					<td onclick=$url>
						" . $negociacao['id'] . "
					</td>
					<td onclick=$url>
						<b>Abertura</b>: " . $negociacao['data_abertura'] . "<br>
						<b>Tipo</b>: " . $tipo . "<br>
						<b>Cliente</b>: " . $negociacao['nome'] . "<br>
						<b>Vendedor</b>: " . $negociacao['nomeUsuario'] . "
					</td>
					
					<td onclick=$url>
						<b>Placa</b>: " . $negociacao['placa'] . "<br>
						<b>Modelo</b>: " . $negociacao['modelo']."<br>
						<b>Ano</b>: ".$negociacao['ano_modelo']."<br>
						<b>Valor</b>: R$ ".number_format($negociacao['valor_venda'],2,',','.')."<br>
						<b>Valor Aquisi&ccedil;&atilde;o</b>: R$ ".number_format($negociacao['valor_aquisicao'],2,',','.')."
					</td>
					
					<td onclick=$url>
						<center><img style='width:30px;' src='/images/info.png' title='".$strDespesas."' /></center>
					</td>";
					
		
		
				echo "<td onclick=$url>";
				
					if(isset($negociacao['modelo_troca_1'])){
				
						if(isset($negociacao['descricao_site_troca_1'])){
				
							$negociacao['modelo_troca_1'] = $negociacao['descricao_site_troca_1'];
						
						}
						
						echo "<b>Modelo</b>: ".$negociacao['modelo_troca_1']."<br/>
							  <b>Placa</b>: ".$negociacao['placa_troca_1']."<br/>
							  <b>Ano</b>: ".$negociacao['ano_modelo_troca_1']."<br/>
							  <b>Valor</b>: ".money_format("%i",$negociacao['valor_compra_troca_1'])."<br/>
							</td>";
					
					
					}else{
					
						echo "<center><b>N&atilde;o há ve&iacute;culo de troca<b/></center>";
					
					}
			

	
			echo "<td onclick=$url>";
				if($negociacao['valor_financiado'] != 0){
					
					echo "<b>Financiamento</b>: ".money_format("%i",$negociacao['valor_financiado'])."<br/>";
					echo "<b>Retorno</b>: ".money_format("%i",$negociacao['retorno_financeira']);
			
				}
				
			if(isset($negociacao['imposto_financeira']) && $negociacao['imposto_financeira'] != 0){
				
				$retorno = (((($negociacao['retorno_financeira'] * 1.2) * $negociacao['valor_financiado']) / 100) - $negociacao['imposto_financeira']);
			
			}elseif(isset($negociacao['imposto']) && $negociacao['imposto'] != 0){
				
				$retorno = (((($negociacao['retorno_financeira'] * 1.2) * $negociacao['valor_financiado']) / 100) - ((((($negociacao['retorno_financeira'] * 1.2) * $negociacao['valor_financiado']) / 100) * $negociacao['imposto']) / 100));
			
			}else{
			
				$retorno = (((($negociacao['retorno_financeira'] * 1.2) * $negociacao['valor_financiado']) / 100));
			
			}
			
				
			$aprovada = "";
			
			if(isset($negociacao['aprovada']) && $negociacao['aprovada'] == 1){
					
				$aprovada = "<input type='checkbox' id='aprovar_".$negociacao['id']."' checked='checked' onclick='reprova(".$negociacao['id'].",this.id, ".$negociacao['id_veiculo'].");' /><b>Aprovar</b>";
				
			}else{
					
				$aprovada = "<input type='checkbox' id='aprovar_".$negociacao['id']."' onclick='aprova(".$negociacao['id'].",this.id, ".$negociacao['id_veiculo'].")' /><b>Aprovar</b>";
				
			}
			
			if(isset($negociacao['compra']) && $negociacao['compra'] == 1){
				
				$aprovada = "";
				
			}

				
			echo $forma."
					
				</td>
				  
				<td>
					<div onclick=$url>
					<b>Venda</b>: ".money_format("%i",$negociacao['valor_venda'])."<br/>
					<b>Retorno</b>: ".money_format("%i",$retorno)."<br/>
					<b>Valor Aquisi&ccedil;&atilde;o</b>: ".money_format("%i",$negociacao['valor_aquisicao'])."<br/>
					<b>Despesas</b>: ".money_format("%i",$totalDespesas)."<br/>
					<b>Comissões</b>: ".money_format("%i",$negociacao['comissao_vendedor']+$negociacao['comissao_gerente']+$negociacao['comissao_supervisor'])."<br/>
					<b>Custo</b>: ".money_format("%i",$totalDespesas+$negociacao['valor_aquisicao'])."<br/>
					<b style='background-color:#008B45; color:#FFFFFF;'>Resultado: ".money_format("%i",($negociacao['valor_venda']+$retorno)-($totalDespesas+$negociacao['valor_aquisicao']+$negociacao['comissao_vendedor']+$negociacao['comissao_gerente']+$negociacao['comissao_supervisor']))."</b><br/>
					</div>
					".$aprovada."
				</td>
			</tr>";
			
        }
		
         echo "</tbody></table></div>";
		 
		 
		 
		 
    }elseif ($this->_getParam('fn') == 'getNegociacoes'){

         $dbNegociacoes = new Application_Model_DbTable_Negociacoes();

         if (isset($idPerfil) && $idPerfil != CONCESSIONARIO) {

            $arrFiltro['id_vendedor'] = $idUsuario;
         }

         $arrFiltro['id_empresa'] = $idEmpresa;
         $arrFiltro['id_perfil'] = $idPerfil;

         if ($this->_getParam('agenda') == 1) {

            $arrFiltro['buscaTroca'] = false;
         } else {

            $arrFiltro['buscaTroca'] = true;
         }

         $dataInicial = $this->_getParam('dataInicial');
         $dataFinal = $this->_getParam('dataFinal');

         if (isset($dataInicial) && isset($dataFinal)) {

            $dataTmp = explode("/", $dataInicial);
            $dataInicial = implode("-", array_reverse($dataTmp));

            $dataTmp = explode("/", $dataFinal);
            $dataFinal = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_inicial_abertura'] = $dataInicial;
            $arrFiltro['data_final_abertura'] = $dataFinal;
         } elseif (isset($dataInicial)) {

            $dataTmp = explode("/", $dataInicial);
            $dataInicial = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_inicial_abertura'] = $dataInicial;
            $arrFiltro['data_final_abertura'] = @date("Y-m-d");
         } elseif (isset($dataFinal)) {

            $dataTmp = explode("/", $dataFinal);
            $dataFinal = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_final_abertura'] = "1900-01-01";
            $arrFiltro['data_final_abertura'] = $dataFinal;
         }

         //echo $arrFiltro['data_final'];

         $arrNegociacoes = $dbNegociacoes->_get($arrFiltro);


         echo "<div class=\"inline\" style=\"width:98%;height:auto\"><label style=\"font-size:20px;padding:5px;padding-left:0px;\">Negocia&ccedil;&otilde;es</label></div>
			
			<div class=\"inline\">
			<label class=\"label\">Data Inicio</label>
			<input type=\"text\" id=\"data_inicial_negociacoes\" name=\"data_inicial_negociacoes\" class=\"text\"/>
			</div>			

			<div class=\"inline\">
			<label class=\"label\">Data Fim</label>
			<input type=\"text\" id=\"data_final_negociacoes\" name=\"data_final_negociacoes\" class=\"text\"/>
			</div>

			<div class=\"inline\" style=\"margin-top:10px;\">
			<input type=\"button\" id=\"button\" name=\"button\" class=\"btn-small btn-find\" value=\"Pesquisar\" onclick=\"getNegociacoes()\"/>
			</div>	

			<div class=\"inline\" style=\"margin-top:10px;\">
			<input type=\"button\" id=\"button1\" name=\"button1\" class=\"btn-mini btn-include\" value=\"Add\" onclick=\"javascript:window.location.href='" . URL . "negociacoes/add'\" />
			</div>

			<table class = 'table'>		

			<thead><tr><th style=\"widht:20px\">Número</th><th>Negociação</th></tr></thead>
			<tbody>";

         //var_export($arrNegociacoes);exit;	

         foreach ($arrNegociacoes as $negociacao) {

            $tempo = explode(" ", $negociacao['data_abertura']);
            $data = explode("-", $tempo[0]);
            $negociacao['data_abertura'] = implode("/", array_reverse($data));
            $negociacao['data_abertura'] = $negociacao['data_abertura'] . " - " . $tempo[1];
			
			if(isset($negociacao['compra']) && $negociacao['compra'] == 1){
            
				$tipo = "Compra";
			
			}elseif(isset($negociacao['id_troca'])){

               $tipo = "Venda com Troca";
			   
            } else {

               $tipo = "Venda";
            }

            if (isset($negociacao['aprovada']) && $negociacao['aprovada'] == '1') {

               $cor = "#AAFFAA";
			   
            } elseif (isset($negociacao['aprovada']) && $negociacao['aprovada'] == '-1') {

               $cor = "#FFAAAA";
              
            } elseif(isset($negociacao['aprovada']) && $negociacao['aprovada'] == '0'){

               $cor = "#FFFFAA";
            
			}
			
			if(isset($negociacao['compra']) && $negociacao['compra'] == 0){

				$url = "javascript:window.location.href='/negociacoes/edt/id/" . $negociacao['id'] . "'";
			
			}else{
			
				$url = "javascript:window.location.href='/negociacoes/edt-compra/id/" . $negociacao['id'] . "'";
				$cor = "#66CCFF";
			}

            echo "<tr onclick=$url style='background-color:".$cor."; cursor: pointer;'><td style=\"widht:20px\">" . $negociacao['id'] . "</td>
				
																		<td><b>Abertura</b>: " . $negociacao['data_abertura'] . "<br>

																			<b>Tipo</b>: " . $tipo . "<br>

																			<b>Placa</b>: " . $negociacao['placa'] . "<br>

																			<b>Modelo</b>: " . $negociacao['modelo'] . " / " . $negociacao['ano_modelo'] . "<br>

																			<b>Cliente</b>: " . $negociacao['nome'] . "<br>

																			<b>Vendedor</b>: " . $negociacao['nomeUsuario'] . "</td><tr>";
         }
         echo "</tbody></table>";
      } elseif ($this->_getParam('fn') == 'getAgenda') {

         $dbAgenda = new Application_Model_DbTable_Agenda();

         $arrFiltro['id_usuario'] = $idUsuario;

         $dataInicial = $this->_getParam('dataInicial');
         $dataFinal = $this->_getParam('dataFinal');
         $baixado = $this->_getParam('baixado');


         if (isset($dataInicial) && isset($dataFinal)) {

            $dataTmp = explode("/", $dataInicial);
            $dataInicial = implode("-", array_reverse($dataTmp));

            $dataTmp = explode("/", $dataFinal);
            $dataFinal = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_inicial'] = $dataInicial;
            $arrFiltro['data_final'] = $dataFinal;
         } elseif (isset($dataInicial)) {

            $dataTmp = explode("/", $dataInicial);
            $dataInicial = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_inicial'] = $dataInicial;
            $arrFiltro['data_final'] = @date("Y-m-d");
         } elseif (isset($dataFinal)) {

            $dataTmp = explode("/", $dataFinal);
            $dataFinal = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_final'] = "1900-01-01";
            $arrFiltro['data_final'] = $dataFinal;
         }

         if (isset($baixado) && $baixado == 1) {
            $arrFiltro['baixado'] = $baixado;
         } else {
            $arrFiltro['nao_baixado'] = 1;
         }

         $arrAgenda = $dbAgenda->_get($arrFiltro);
         //var_export($arrNegociacoes);exit;
		 
		 $options = "<option value='0'>Selecione</option>";
		
		
		for($i = @date("m"); $i > (@date("m")-25); $i--){
	
			$mesNum = @date("m", mktime(0, 0, 0, $i, 01, @date("Y")));
			$anoNum = @date("Y", mktime(0, 0, 0, $i, 01, @date("Y")));
			$ultimoDia = @date("d", mktime(0, 0, 0, $i+1, 0, @date("Y")));

			switch ($mesNum){
				case 1:
					$option =  "Janeiro - ".$anoNum;
					break;
							
				case 2:
					$option =  "Fevereiro - ".$anoNum;
					break;
						
				case 3:
					$option =  "Março - ".$anoNum;
					break;
							
				case 4:
					$option =  "Abril - ".$anoNum;
					break;
							
				case 5:
					$option =  "Maio - ".$anoNum;
					break;
							
				case 6:
					$option =  "Junho - ".$anoNum;
					break;
							
				case 7:
					$option =  "Julho - ".$anoNum;
					break;
							
				case 8:
					$option =  "Agosto - ".$anoNum;
					break;
							
				case 9:
					$option =  "Setembro - ".$anoNum;
					break;
						
				case 10:
					$option =  "Outubro - ".$anoNum;
					break;
							
				case 11:
					$option =  "Novembro - ".$anoNum;
					break;
							
				case 12:
					$option =  "Dezembro - ".$anoNum;
					break;
			
			}
					
			$options .= "<option value='".$ultimoDia."_".$mesNum."_".$anoNum."'>".$option."</option>";
				
		}
		 

         echo "<form class=\"form\"><div class=\"inline\" style=\"width:98%;height:auto\"><label style=\"font-size:20px;padding:5px;padding-left:0px;\">Agenda</label></div>

			<div class=\"inline\" style='width:35%'>
			<label class=\"label\">Mês</label>
			<select id=\"mes_negociacoes\" class=\"text\" onChange=\"enviaPeriodo(this.value, 'agenda');\">
			".$options."
			</select>
			</div>
		 
			<div class=\"inline\" style='width:17%'>
			<label class=\"label\">Data Inicio</label>
			<input type=\"text\" id=\"data_inicial_agenda\" name=\"data_inicial_agenda\" class=\"text\"/>
			</div>		

			<div class=\"inline\" style='width:17%'>
			<label class=\"label\">Data Fim</label>
			<input type=\"text\" id=\"data_final_agenda\" name=\"data_final_agenda\" class=\"text\"/>
			</div>		

			<div class=\"inline\" style='width:10%;'>	
			<label class=\"label\">Baixados</label>
			<input name=\"baixado_agenda\" id=\"baixado_agenda\" type=\"checkbox\" value=\"1\">	
			</div>		

			<div class=\"inline\" style=\"margin-top:1px; width:22%; clear:both;\">
			<input type=\"button\" id=\"button\" name=\"button\" class=\"btn-small btn-find\" value=\"Pesquisar\" onclick=\"getAgenda()\"/>
			</div>		
			
			<div class=\"inline\" style=\"margin-top:1px;\">
			<input type=\"button\" class=\"btn-mini btn-include\" value=\"Add\" onclick=\"javascript:window.location.href='add'\" />
            </div>
            </form>
			
			<table class = 'table'>
			<thead><tr><th>Data</th><th>Descri&ccedil;&atilde;o</th>";
         if (isset($baixado) && $baixado != 1) {
            echo "<th>Finalizar</th>";
         }
         echo "</tr></thead><tbody>";

         foreach ($arrAgenda as $agenda) {

            if (isset($agenda['data']) && $agenda['data'] > @date('Y-m-d hh:ii:ss')) {

               $cor = "#AAFFAA";
            } elseif (isset($negociacao['data']) && $negociacao['data'] < @date('Y-m-d hh:ii:ss')) {

               $cor = "#FFAAAA";
            } else {

               $cor = "#FFFFAA";
            }

            $tempo = explode(" ", $agenda['data']);
            $data = explode("-", $tempo[0]);
            $agenda['data'] = implode("/", array_reverse($data));
            $agenda['data'] = $agenda['data'] . " - " . $tempo[1];

            $url = "javascript:window.location.href='/agenda/edt/id/" . $agenda['id'] . "'";

            echo "<tr style=\"background-color:$cor\"><td onclick=\"$url\" >" . current(explode("-",$agenda['data'])) . "</td><td onclick=\"$url\">" . nl2br($agenda['descricao']) . "</td>";
            if (isset($baixado) && $baixado != 1) {
               echo "<td><center><input type=\"checkbox\" value=\"1\" onclick=\"baixaAgenda(" . $agenda['id'] . ", this)\" /></center></td>";
            }
            echo "<tr>";
         }

         echo "</tbody></table>";
		 
      } elseif ($this->_getParam('fn') == 'getVeiculos') {

         $dbV = new Application_Model_DbTable_Veiculos();

         $arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
         $arr['limit'] = 15;
         $arr['ordem_data'] = true;


         $arrV = $dbV->_get($arr);

         //var_export($arrNegociacoes);exit;

         echo "<div class=\"inline\" style=\"width:98%;height:auto\"><label style=\"font-size:20px;padding:5px;padding-left:0px;\">&Uacute;ltimos Ve&iacute;culos</label></div>

			<div class=\"inline\" style=\"margin-top:10px;\">
			<input type=\"button\" id=\"button1\" name=\"button1\" class=\"btn-mini btn-include\" value=\"Add\" onclick=\"javascript:window.location.href='" . URL . "veiculos/add'\" />
			</div>

			
			<table class = 'table'>			
			<thead><tr><th colspan='2'>Modelo</th><th>Ano</th><th>Cor</th><th>Pre&ccedil;o</th></tr></thead>
			<tbody>";

         foreach ($arrV as $v) {

			if(isset($v['app']) && $v['app'] == 1){
				
				$completo = "/images/icons/vetorvermelho.png";
				$alt = "Cadastro do veículo incompleto";
				
			}else{
				
				$completo = "/images/icons/vetorverde.png";
				$alt = "Cadastro do veículo completo";
			
			}
		 
            if (isset($v['descricao_site'])) {

               $v['modelo'] = $v['descricao_site'];
            }

            $url = "javascript:window.location.href='/veiculos/edt/id/" . $v['id'] . "'";
            echo "<tr onclick=\"$url\" style='cursor: pointer' >
					<td width='20px;'><img src='".$completo."' title='".$alt."' /></td>
					<td>" . $v['modelo'] . "</td>
					<td>" . $v['ano_modelo'] . "</td>
					<td>" . $v['cor'] . "</td>
					<td>" . number_format($v['valor_venda'], 2, ',', '.') . "</td>
				<tr>";
         }

         echo "</tbody></table>";
		 
      } elseif ($this->_getParam('fn') == 'getPendencias') {

         $dbPendencias = new Application_Model_DbTable_PendenciasVeiculos();

         $arrFiltro['empresa'] = $_SESSION['sessionUser']['id_empresa'];

         $dataInicial = $this->_getParam('dataInicial');
         $dataFinal = $this->_getParam('dataFinal');
         $baixado = "";
         $baixado = $this->_getParam('baixado');

         if (isset($dataInicial) && isset($dataFinal)) {

            $dataTmp = explode("/", $dataInicial);
            $dataInicial = implode("-", array_reverse($dataTmp));

            $dataTmp = explode("/", $dataFinal);
            $dataFinal = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_inicial'] = $dataInicial;
            $arrFiltro['data_final'] = $dataFinal;
         } elseif (isset($dataInicial)) {

            $dataTmp = explode("/", $dataInicial);
            $dataInicial = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_inicial'] = $dataInicial;
            $arrFiltro['data_final'] = @date("Y-m-d");
         } elseif (isset($dataFinal)) {

            $dataTmp = explode("/", $dataFinal);
            $dataFinal = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_final'] = "1900-01-01";
            $arrFiltro['data_final'] = $dataFinal;
         }

         if (isset($baixado) && $baixado == 1) {
            $arrFiltro['baixado'] = $baixado;
         }

        $arrFiltro['limit'] = 100;

         //var_export($arrFiltro);exit;

         $arrPendencias = $dbPendencias->_get($arrFiltro);
		 
		$options = "<option value='0'>Selecione</option>";
		
		
		for($i = @date("m"); $i > (@date("m")-25); $i--){
	
			$mesNum = @date("m", mktime(0, 0, 0, $i, 01, @date("Y")));
			$anoNum = @date("Y", mktime(0, 0, 0, $i, 01, @date("Y")));
			$ultimoDia = @date("d", mktime(0, 0, 0, $i+1, 0, @date("Y")));

			switch ($mesNum){
				case 1:
					$option =  "Janeiro - ".$anoNum;
					break;
							
				case 2:
					$option =  "Fevereiro - ".$anoNum;
					break;
						
				case 3:
					$option =  "Março - ".$anoNum;
					break;
							
				case 4:
					$option =  "Abril - ".$anoNum;
					break;
							
				case 5:
					$option =  "Maio - ".$anoNum;
					break;
							
				case 6:
					$option =  "Junho - ".$anoNum;
					break;
							
				case 7:
					$option =  "Julho - ".$anoNum;
					break;
							
				case 8:
					$option =  "Agosto - ".$anoNum;
					break;
							
				case 9:
					$option =  "Setembro - ".$anoNum;
					break;
						
				case 10:
					$option =  "Outubro - ".$anoNum;
					break;
							
				case 11:
					$option =  "Novembro - ".$anoNum;
					break;
							
				case 12:
					$option =  "Dezembro - ".$anoNum;
					break;
			
			}
					
			$options .= "<option value='".$ultimoDia."_".$mesNum."_".$anoNum."'>".$option."</option>";
				
		}

         echo "<form class=\"form\"><div class=\"inline\" style=\"width:98%;height:auto\"><label style=\"font-size:20px;padding:5px;padding-left:0px;\">Pend&ecirc;ncias</label></div>

			<div class=\"inline\" style='width:35%'>
			<label class=\"label\">Mês</label>
			<select id=\"mes_negociacoes\" class=\"text\" onChange=\"enviaPeriodo(this.value, 'pendencias');\">
			".$options."
			</select>
			</div>
		 
			<div class=\"inline\" style='width:17%'>
			<label class=\"label\">Data Inicio</label>
			<input type=\"text\" id=\"data_inicial_pendencias\" name=\"data_inicial_pendencias\" class=\"text\"/>
			</div>		

			<div class=\"inline\" style='width:17%'>
			<label class=\"label\">Data Fim</label>
			<input type=\"text\" id=\"data_final_pendencias\" name=\"data_final_pendencias\" class=\"text\"/>
			</div>		

			<div class=\"inline\" style='width:13%'>	
			<label class=\"label\">Baixados</label>
			<input name=\"baixado_pendencias\" id=\"baixado_pendencias\" type=\"checkbox\" value=\"1\">	
			</div>		
			
			<div class=\"inline\" style=\"margin-top:1px; clear:both;\">
			<input type=\"button\" id=\"button\" name=\"button\" class=\"btn-small btn-find\" value=\"Pesquisar\" onclick=\"getPendencias()\"/>
            </div>
            </form>	

			<table class = \"table\">
			<thead><tr><th>Data</th><th>Placa / Modelo</th><th>Descri&ccedil;&atilde;o</th>";
         if ($baixado != 1) {
            echo "<th>Baixar</th>";
         }
         echo "</tr></thead><tbody>";

         foreach ($arrPendencias as $pendencia) {


            if (isset($pendencia['descricao_site'])) {

               $pendencia['modelo'] = $pendencia['descricao_site'];
            }

            if (isset($pendencia['data']) && $pendencia['data'] > @date('Y-m-d hh:ii:ss')) {

               $cor = "#AAFFAA";
            } elseif (isset($pendencia['data']) && $pendencia['data'] < @date('Y-m-d hh:ii:ss')) {

               $cor = "#FFAAAA";
            } else {

               $cor = "#FFFFAA";
            }

            $tempo = explode(" ", $pendencia['data']);
            $data = explode("-", $tempo[0]);
            $pendencia['data'] = implode("/", array_reverse($data));

            $url = "javascript:window.location.href='/veiculos/edt/id/" . $pendencia['id_veiculo'] . "/p/true'";

            echo "<tr style=\"background-color:$cor\"><td onclick=\"$url\">" . $pendencia['data'] . "</td><td onclick=\"$url\">" . $pendencia['modelo'] . " / " . $pendencia['ano_modelo'] . "</td><td onclick=\"$url\">" . $pendencia['descricao'] . "</td>";
            if ($baixado != 1) {
               echo"<td><center><input type=\"checkbox\" value=\"1\" onclick=\"baixaPendencia(" . $pendencia['id'] . ", this)\" /></center></td>";
            }
            echo "<tr>";
         }

         echo "</tbody></table>";
      } elseif ($this->_getParam('fn') == 'getRecebimentosPendentes') {

         $dbRecebimentosPendentes = new Application_Model_DbTable_RecebimentosNegociacoes();

         $arrFiltro['id_usuario'] = $idUsuario;

         $dataInicial = $this->_getParam('dataInicial');
         $dataFinal = $this->_getParam('dataFinal');
         $baixado = "";
         $baixado = $this->_getParam('baixado');

         if (isset($dataInicial) && isset($dataFinal)) {

            $dataTmp = explode("/", $dataInicial);
            $dataInicial = implode("-", array_reverse($dataTmp));

            $dataTmp = explode("/", $dataFinal);
            $dataFinal = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_inicial'] = $dataInicial;
            $arrFiltro['data_final'] = $dataFinal;
         } elseif (isset($dataInicial)) {

            $dataTmp = explode("/", $dataInicial);
            $dataInicial = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_inicial'] = $dataInicial;
            $arrFiltro['data_final'] = @date("Y-m-d");
         } elseif (isset($dataFinal)) {

            $dataTmp = explode("/", $dataFinal);
            $dataFinal = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_final'] = "1900-01-01";
            $arrFiltro['data_final'] = $dataFinal;
         }

         if ($baixado == 1) {
            $arrFiltro['baixado'] = $baixado;
         } else {
            $arrFiltro['nao_baixado'] = 1;
         }

        $arrFiltro['limit'] = 200;
        $arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

        $arrRecebimentosPendentes = $dbRecebimentosPendentes->_get($arrFiltro);

		$options = "<option value='0'>Selecione</option>";

		for($i = @date("m"); $i > (@date("m")-25); $i--){
	
			$mesNum = @date("m", mktime(0, 0, 0, $i, 01, @date("Y")));
			$anoNum = @date("Y", mktime(0, 0, 0, $i, 01, @date("Y")));
			$ultimoDia = @date("d", mktime(0, 0, 0, $i+1, 0, @date("Y")));

			switch ($mesNum){
				case 1:
					$option =  "Janeiro - ".$anoNum;
					break;
							
				case 2:
					$option =  "Fevereiro - ".$anoNum;
					break;
						
				case 3:
					$option =  "Março - ".$anoNum;
					break;
							
				case 4:
					$option =  "Abril - ".$anoNum;
					break;
							
				case 5:
					$option =  "Maio - ".$anoNum;
					break;
							
				case 6:
					$option =  "Junho - ".$anoNum;
					break;
							
				case 7:
					$option =  "Julho - ".$anoNum;
					break;
							
				case 8:
					$option =  "Agosto - ".$anoNum;
					break;
							
				case 9:
					$option =  "Setembro - ".$anoNum;
					break;
						
				case 10:
					$option =  "Outubro - ".$anoNum;
					break;
							
				case 11:
					$option =  "Novembro - ".$anoNum;
					break;
							
				case 12:
					$option =  "Dezembro - ".$anoNum;
					break;
			
			}
					
			$options .= "<option value='".$ultimoDia."_".$mesNum."_".$anoNum."'>".$option."</option>";
				
		}
		 
		 

         echo "<form class=\"form\"><div class=\"inline\" style=\"width:98%;height:auto\"><label style=\"font-size:20px;padding:5px;padding-left:0px;\">Recebimentos Pendentes</label></div>";

         echo "
			
			<div class=\"inline\" style='width:35%'>
			<label class=\"label\">Mês</label>
			<select id=\"mes_negociacoes\" class=\"text\" onChange=\"enviaPeriodo(this.value, 'recebimentos_pendentes');\">
			".$options."
			</select>
			</div>
		 
			<div class=\"inline\" style='width:17%'>
			<label class=\"label\">Data Inicio</label>
			<input type=\"text\" id=\"data_inicial_recebimentos_pendentes\" name=\"data_inicial_recebimentos_pendentes\" class=\"text\"/>
			</div> 			

			<div class=\"inline\" style='width:17%'>
			<label class=\"label\">Data Fim</label>
			<input type=\"text\" id=\"data_final_recebimentos_pendentes\" name=\"data_final_recebimentos_pendentes\" class=\"text\"/>
			</div>
			
			<div class=\"inline\" style='width:13%'>	
			<label class=\"label\">Baixados</label>
			<input name=\"baixado_recebimentos_pendentes\" id=\"baixado_recebimentos_pendentes\" type=\"checkbox\" value=\"1\">		
			</div>			

			<div class=\"inline\" style=\"margin-top:0px; clear:both;\">
			<input type=\"button\" id=\"button\" name=\"button\" class=\"btn-small btn-find\" value=\"Pesquisar\" onclick=\"getRecebimentosPendentes()\"/>
            </div>
            </form>

			<table class = 'table'>
			<thead><tr><th>Veiculo</th><th>Valor</th><th>Data</th>";
         if ($baixado != 1) {
            echo "<th>Baixar</th>";
         }
         echo"</tr></thead><tbody>";

         foreach ($arrRecebimentosPendentes as $r) {

            //var_export($r);exit;

			if ($r['data'] > @date('Y-m-d hh:ii:ss')) {

               $cor = "#AAFFAA";
			   
            } elseif ($r['data'] < @date('Y-m-d hh:ii:ss')) {

               $cor = "#FFAAAA";
            } else {

               $cor = "#FFFFAA";
            }
			
            $tData = explode("-", $r['data']);
            $tData = implode("/", array_reverse($tData));
			
            $url = "javascript:window.location.href='/negociacoes/edt/id/" . $r['id_negociacao'] . "'";
			
			if(isset($r['descricao_site'])){
				
				$r['modelo'] = $r['descricao_site'];
			
			}
			
            echo "<tr style=\"background-color:$cor\" id=\"linha_pgto_p_" . $r['id'] . "\"><td onclick=\"$url\">" . substr($r['modelo'],0,31) . " - " . $r['ano_fabricacao'] . " - " . $r['placa'] . "</td><td onclick=\"$url\">R$ " . money_format("%i",$r['valor']) . "</td><td onclick=\"$url\">$tData</td>";
            if ($baixado != 1) {
               echo "<td><center><input type=\"checkbox\" value=\"1\" onclick=\"baixaPagamento(" . $r['id'] . ", this)\" /></center></td>";
            }
            echo "<tr>";
         }

         echo "</tbody></table>";
      } elseif ($this->_getParam('fn') == 'getPagamentosPendentes') {

         $dbPagamentosPendentes = new Application_Model_DbTable_LancamentosFinanceiros();
         $dbItensFinanceiros = new Application_Model_DbTable_ItensFinanceiros();

         $arrFiltro['id_usuario'] = $idUsuario;

         $dataInicial = $this->_getParam('dataInicial');
         $dataFinal = $this->_getParam('dataFinal');
         //$baixado = $this->_getParam('baixado');			
         $baixado = 0;

         if (isset($dataInicial) && isset($dataFinal)) {

            $dataTmp = explode("/", $dataInicial);
            $dataInicial = implode("-", array_reverse($dataTmp));

            $dataTmp = explode("/", $dataFinal);
            $dataFinal = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_inicial'] = $dataInicial;
            $arrFiltro['data_final'] = $dataFinal;
         } elseif (isset($dataInicial)) {

            $dataTmp = explode("/", $dataInicial);
            $dataInicial = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_inicial'] = $dataInicial;
            $arrFiltro['data_final'] = @date("Y-m-d");
         } elseif (isset($dataFinal)) {

            $dataTmp = explode("/", $dataFinal);
            $dataFinal = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_final'] = "1900-01-01";
            $arrFiltro['data_final'] = $dataFinal;
         }

         if ($baixado == 1) {
            $arrFiltro['baixado'] = $baixado;
         } else {
            $arrFiltro['nao_baixado'] = 1;
         }

         $arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

         $arrFiltro['limit'] = 100;
		 
		 $arrItensRecorrentes = $dbItensFinanceiros->_getItensRecorentes($arrFiltro);

         $arrPagamentosPendentes = $dbPagamentosPendentes->_get($arrFiltro);

		 $options = "<option value='0'>Selecione</option>";

		for($i = @date("m"); $i > (@date("m")-25); $i--){
	
			$mesNum = @date("m", mktime(0, 0, 0, $i, 01, @date("Y")));
			$anoNum = @date("Y", mktime(0, 0, 0, $i, 01, @date("Y")));
			$ultimoDia = @date("d", mktime(0, 0, 0, $i+1, 0, @date("Y")));

			switch ($mesNum){
				case 1:
					$option =  "Janeiro - ".$anoNum;
					break;
							
				case 2:
					$option =  "Fevereiro - ".$anoNum;
					break;
						
				case 3:
					$option =  "Março - ".$anoNum;
					break;
							
				case 4:
					$option =  "Abril - ".$anoNum;
					break;
							
				case 5:
					$option =  "Maio - ".$anoNum;
					break;
							
				case 6:
					$option =  "Junho - ".$anoNum;
					break;
							
				case 7:
					$option =  "Julho - ".$anoNum;
					break;
							
				case 8:
					$option =  "Agosto - ".$anoNum;
					break;
							
				case 9:
					$option =  "Setembro - ".$anoNum;
					break;
						
				case 10:
					$option =  "Outubro - ".$anoNum;
					break;
							
				case 11:
					$option =  "Novembro - ".$anoNum;
					break;
							
				case 12:
					$option =  "Dezembro - ".$anoNum;
					break;
			
			}
					
			$options .= "<option value='".$ultimoDia."_".$mesNum."_".$anoNum."'>".$option."</option>";
				
		}
		 
         echo "<form class=\"form\"><div class=\"inline\" style=\"width:98%;height:auto\"><label style=\"font-size:20px;padding:5px;padding-left:0px;\">Pagamentos Pendentes</label></div>
			
			<div class=\"inline\" style='width:35%'>
			<label class=\"label\">Mês</label>
			<select id=\"mes_negociacoes\" class=\"text\" onChange=\"enviaPeriodo(this.value, 'pagamentos_pendentes');\">
			".$options."
			</select>
			</div>
			
			<div class=\"inline\" style='width:17%'>
			<label class=\"label\">Data Inicio</label>
			<input type=\"text\" id=\"data_inicial_pagamentos_pendentes\" name=\"data_inicial_pagamentos_pendentes\" class=\"text\"/>
			</div> 			

			<div class=\"inline\" style='width:17%'>
			<label class=\"label\">Data Fim</label>
			<input type=\"text\" id=\"data_final_pagamentos_pendentes\" name=\"data_final_pagamentos_pendentes\" class=\"text\"/>
			</div>
			
			<div class=\"inline\" style='width:13%'>	
			<label class=\"label\">Baixados</label>
			<input name=\"baixado_pagamentos_pendentes\" id=\"baixado_pagamentos_pendentes\" type=\"checkbox\" value=\"1\">		
			</div>				

			<div class=\"inline\" style=\"margin-top:0px; width:22%; clear:both; \" >
			<input type=\"button\" id=\"button\" name=\"button\" class=\"btn-small btn-find\" value=\"Pesquisar\" onclick=\"getPagamentosPendentes()\"/>
			</div>	

			<div class=\"inline\" style=\"margin-top:0px; \">
			<input type=\"button\" id=\"button1\" name=\"button1\" class=\"btn-mini btn-include\" value=\"Add\" onclick=\"javascript:window.location.href='" . URL . "lancamentos-financeiros/add'\" />
			</div>
            </form>

			<table class = 'table' >

			<thead><tr><th>Vencimento</th><th>Grupo</th><th>Item</th><th>Valor</th>";
         if ($baixado != 1) {
            echo "<th>Baixar</th>";
         }
         echo"</tr></thead><tbody>";

         foreach ($arrPagamentosPendentes as $r) {

            //var_export($r);exit;

            if ($r['data_lancamento'] > @date('Y-m-d hh:ii:ss')) {

               $cor = "#AAFFAA";
			   
            } elseif ($r['data_lancamento'] < @date('Y-m-d hh:ii:ss')) {

               $cor = "#FFAAAA";
            } else {

               $cor = "#FFFFAA";
            }

            $tData = explode("-", $r['data_lancamento']);
            $tData = implode("/", array_reverse($tData));

            $url = "javascript:window.location.href='/lancamentos-financeiros/edt/id/" . $r['id'] . "'";

            echo "<tr style=\"background-color:$cor\" id=\"linha_p_" . $r['id'] . "\"><td onclick=\"$url\">".$tData."</td><td onclick=\"$url\">" . $r['descricao'] . "</td><td onclick=\"$url\">" . $r['item'] . "</td><td onclick=\"$url\">" . number_format($r['valor'], 2, ',', '.') . "</td>";

            if ($baixado != 1) {
               echo "<td><center><input type=\"checkbox\" value=\"1\" onclick=\"baixaLancamento(" . $r['id'] . ", this)\" /></center></td>";
            }
            echo "<tr>";
         }
		 
		foreach($arrItensRecorrentes as $itensRecorrentes){
		
			$arrFiltro['item'] = $itensRecorrentes['id'];
		
			if(!$dbPagamentosPendentes->getlancamentoPorDataItem($arrFiltro)) {

				$data = explode("-",$itensRecorrentes['data_inicio']);
			
				if($data[2] > @date('d')) {

					$cor = "#AAFFAA";
				   
				}elseif($data[2] < @date('d')){

					$cor = "#FFAAAA";
				   
				} else {

					$cor = "#FFFFAA";
				   
				}

				$tData = explode("-", $itensRecorrentes['data_inicio']);
				$tData = $tData[2]."/".@date("m/Y");

				$url = "javascript:window.location.href='/lancamentos-financeiros/add-recorrente/id/".$itensRecorrentes['id']."'";

				echo "<tr style=\"background-color:$cor\" id=\"linha_p_r".$itensRecorrentes['id']."\">
						<td onclick=\"$url\">".$tData."</td><td onclick=\"$url\">".$itensRecorrentes['descricao']."</td>
						<td onclick=\"$url\">".$itensRecorrentes['item']."</td>
						<td onclick=\"$url\">".number_format($itensRecorrentes['valor_padrao'], 2, ',', '.')."</td>";

				if ($baixado != 1) {
				
				   echo "<td><center><input type=\"checkbox\" value=\"1\" onclick=\"baixaLancamentoRecorrente(".$itensRecorrentes['id'].", this)\" /></center></td>";
				}
				
				echo "<tr>";
			
			}
		 
		}

        echo "</tbody></table>";
		 
		 
		}elseif ($this->_getParam('fn') == 'getFluxoLoja'){

			$dbFluxoLoja = new Application_Model_DbTable_FluxoLoja();

			$arrFiltro['id_perfil'] = $_SESSION['sessionUser']['id_perfil'];
			$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

			if ($arrFiltro['id_perfil'] != 4 && $arrFiltro['id_perfil'] != 2) {

				$arrFiltro['id_usuario'] = $_SESSION['sessionUser']['id'];
         
			}

			$dataInicial = $this->_getParam('dataInicial');
			$dataFinal = $this->_getParam('dataFinal');

			if (isset($dataInicial) && isset($dataFinal)) {

				$dataTmp = explode("/", $dataInicial);
				$dataInicial = implode("-", array_reverse($dataTmp));

				$dataTmp = explode("/", $dataFinal);
				$dataFinal = implode("-", array_reverse($dataTmp));

				$arrFiltro['data_inicial'] = $dataInicial;
				$arrFiltro['data_final'] = $dataFinal;
			
			}elseif (isset($dataInicial)){

				$dataTmp = explode("/", $dataInicial);
				$dataInicial = implode("-", array_reverse($dataTmp));

				$arrFiltro['data_inicial'] = $dataInicial;
				$arrFiltro['data_final'] = @date("Y-m-d");
			
			}elseif (isset($dataFinal)) {

				$dataTmp = explode("/", $dataFinal);
				$dataFinal = implode("-", array_reverse($dataTmp));

				$arrFiltro['data_final'] = "1900-01-01";
				$arrFiltro['data_final'] = $dataFinal;
			
			} else {

				$arrFiltro['data_inicial'] = @date("Y-m") . "-01";
				$arrFiltro['data_final'] = @date("Y-m-d");
			
			}

			 $options = "<option value='0'>Selecione</option>";

		for($i = @date("m"); $i > (@date("m")-25); $i--){
	
			$mesNum = @date("m", mktime(0, 0, 0, $i, 01, @date("Y")));
			$anoNum = @date("Y", mktime(0, 0, 0, $i, 01, @date("Y")));
			$ultimoDia = @date("d", mktime(0, 0, 0, $i+1, 0, @date("Y")));

			switch ($mesNum){
				case 1:
					$option =  "Janeiro - ".$anoNum;
					break;
							
				case 2:
					$option =  "Fevereiro - ".$anoNum;
					break;
						
				case 3:
					$option =  "Março - ".$anoNum;
					break;
							
				case 4:
					$option =  "Abril - ".$anoNum;
					break;
							
				case 5:
					$option =  "Maio - ".$anoNum;
					break;
							
				case 6:
					$option =  "Junho - ".$anoNum;
					break;
							
				case 7:
					$option =  "Julho - ".$anoNum;
					break;
							
				case 8:
					$option =  "Agosto - ".$anoNum;
					break;
							
				case 9:
					$option =  "Setembro - ".$anoNum;
					break;
						
				case 10:
					$option =  "Outubro - ".$anoNum;
					break;
							
				case 11:
					$option =  "Novembro - ".$anoNum;
					break;
							
				case 12:
					$option =  "Dezembro - ".$anoNum;
					break;
			
			}

			if($this->_getParam('dataFinal') && $this->_getParam('dataFinal') == $ultimoDia."/".$mesNum."/".$anoNum){
				
				$options .= "<option selected='selected' value='".$ultimoDia."_".$mesNum."_".$anoNum."'>".$option."</option>";

			}elseif(@date("m-Y") == $mesNum."-".$anoNum){

				$options .= "<option selected='selected' value='".$ultimoDia."_".$mesNum."_".$anoNum."'>".$option."</option>";

			}else{
			
				$options .= "<option value='".$ultimoDia."_".$mesNum."_".$anoNum."'>".$option."</option>";
				
			}
					
				
		}

			if(!isset($dataFinal)){
				$final =  @date("d", mktime(0, 0, 0,@date("m")+1, 0, @date("Y")))."/".@date("m")."/".@date("Y");
			}else{
				$final = implode("/", array_reverse(explode("-", $dataFinal)));
			}

			if(!isset($dataInicial)){
				$inicial =  "01/".@date("m")."/".@date("Y");
			}else{
				$inicial = implode("/", array_reverse(explode("-", $dataInicial)));
			}


			
			echo "<form class=\"form\"><div class=\"inline\" style=\"width:98%;height:auto\"><label style=\"font-size:20px;padding:5px;padding-left:0px;\">Fluxo de Loja</label></div>
			
			
			<div class=\"inline\" style='width:35%'>
			<label class=\"label\">Mês</label>
			<select id=\"mes_negociacoes\" class=\"text\" onChange=\"enviaPeriodo(this.value, 'fluxoLoja');\">
			".$options."
			</select>
			</div>
			
			<div class=\"inline\" style='width:17%'>
			<label class=\"label\">Data Inicio</label>
			<input type=\"text\" id=\"data_inicial_fluxoLoja\" name=\"data_inicial_fluxoLoja\" class=\"text\" value=\"".$inicial."\"/>
			</div>						

			<div class=\"inline\" style='width:17%'>
			<label class=\"label\">Data Fim</label>
			<input type=\"text\" id=\"data_final_fluxoLoja\" name=\"data_final_fluxoLoja\" class=\"text\" value=\"".$final."\"/>
			</div>
		
			<div class=\"inline\" style=\"margin-top:0px; clear:both;\">
			<input type=\"button\" id=\"button\" name=\"button\" class=\"btn-small btn-find\" value=\"Pesquisar\" onclick=\"getFluxoLoja()\"/>
			</div>		
			
			<div class=\"inline\" style=\"margin-top:0px;\">
			<input type=\"button\" id=\"button1\" name=\"button1\" class=\"btn-mini btn-include\" value=\"Add\" onclick=\"javascript:window.location.href='" . URL . "fluxo-loja/add-novo-relatorio-diario'\" />
            </div>
            </form>			
			<label style=\"font-size:14px;padding:3px;padding-left:0px; clear:both; display: none;\">Contatos Realizados</label>
			<table class = 'table' style='display:none;'>
			<thead><tr><th>Origem</th><th>Quantidade</th><th>Mensal</th><th>Média/Dia</th><th>Projetado</th></tr></thead>
			<tbody>";

			$arrContato = $dbFluxoLoja->somaFluxoLoja15($arrFiltro);
			$arrContatoHoje = $dbFluxoLoja->somaFluxoLoja15Hoje($arrFiltro);
			$totalContatosDia = "";
			$totalContatosMes = "";
			$totalContatosProjetado = "";

			foreach ($arrContato as $contato){

				if($arrContatoHoje){

					foreach ($arrContatoHoje as $contatoHoje) {

						$contato['qtd_dia'] = 0;

						if(isset($contatoHoje['id_origem_cliente']) == isset($contato['id_origem_cliente'])) {

							$contato['qtd_dia'] = $contatoHoje['geralSoma'];
							
							break;
					
						}
				
					}
					
				} else {

					$contato['qtd_dia'] = 0;
				
				}

				echo "<tr><td>" . $contato['descricao'] . "</td><td>" . $contato['qtd_dia'] . "</td><td>" . $contato['geralSoma'] . "</td><td>" . round($contato['geralSoma'] / @date("d"), 2) . "</td><td>" . round((($contato['geralSoma'] / @date("d")) * @date("t")), 2) . "</td><tr>";

				$totalContatosDia += $contato['qtd_dia'];
				$totalContatosMes += $contato['geralSoma'];
				//$totalRelatoriosMedia += $contato['geralSoma']/@date("d");
				$totalContatosProjetado += ($contato['geralSoma'] / @date("d")) * @date("t");
			
			}

			echo "<tr><td><b>Total</b></td><td><b>" . $totalContatosDia . "</b></td><td><b>" . $totalContatosMes . "</b></td><td></td><td><b>" . round($totalContatosProjetado, 2) . "</b></td><tr>";
			echo "</tbody></table><br>";

			echo"<label style=\"font-size:14px;padding:3px;padding-left:0px; clear:both;\">Relatório Diário Geral</label>";
			echo"<table class = 'table'>		

			<thead><tr><th>RALE</th><th>Mensal</th><th>Média/Dia</th><th>Projetado</th></tr></thead>
			<tbody>";

			$arrLoja = $dbFluxoLoja->somaFluxoLoja59($arrFiltro);
			$arrLojaHoje = $dbFluxoLoja->somaFluxoLoja59Hoje($arrFiltro);
			$totalRelatoriosDia = "";
			$totalRelatoriosMes = "";
			$totalRelatoriosProjetado = "";

			foreach ($arrLoja as $loja) {

				if ($arrLojaHoje) {

					foreach ($arrLojaHoje as $lojaHoje) {

						$loja['qtd_dia'] = 0;

						if (isset($lojaHoje['id_origem_cliente']) == isset($loja['id_origem_cliente'])) {

							$loja['qtd_dia'] = $lojaHoje['geralSoma'];
							
							break;
					
						}
					}
					
				} else {

					$loja['qtd_dia'] = 0;
				
				}


				echo "<tr><td>" . $loja['descricao'] . "</td><td>" . $loja['geralSoma'] . "</td><td>" . round($loja['geralSoma'] / @date("d"), 2) . "</td><td>" . round((($loja['geralSoma'] / @date("d")) * @date("t")), 2) . "</td><tr>";

				$totalRelatoriosDia += $loja['qtd_dia'];
				$totalRelatoriosMes += $loja['geralSoma'];
				//$totalRelatoriosMedia += $loja['geralSoma']/@date("d");
				$totalRelatoriosProjetado += ($loja['geralSoma'] / @date("d")) * @date("t");
			
			}

			//echo "<tr><td><b>Total</b></td><td><b>" . $totalRelatoriosDia . "</b></td><td><b>" . $totalRelatoriosMes . "</b></td><td></td><td><b>" . round($totalRelatoriosProjetado, 2) . "</b></td><tr>";
			echo "</tbody></table><br>";

			$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
			//$arrVendas = $dbNegociacoes->vendasRealizadas($arrFiltro);
         
			//if($_SESSION['sessionUser']['id_empresa'])
			
			$arrVendasMes = $dbNegociacoes->vendasRealizadasMes($arrFiltro);

			echo"<label style=\"font-size:14px;padding:3px;padding-left:0px;\">Vendas Realizadas</label>";

			echo"<table class = 'table'>	
			
			<thead><tr><th>Hoje</th><th>Mensal</th><th>Média/Dia</th><th>Projetado</th><th>Dias Restantes</th></thead>
			<tbody>";

			$vendasHoje = 0;
			$mensal = 0;
			$mediaMes = 0;

			$mediaDia = 1;

			foreach ($arrVendasMes as $vendasMes) {

				$dataTemp = explode(" ", $vendasMes['data_concretizacao']);
				$vendasMes['data_concretizacao'] = $dataTemp[0];

				if (isset($vendasMes['data_concretizacao']) && $vendasMes['data_concretizacao'] == @date('Y-m-d')) {

					$vendasHoje++;
				
				}

				$mensal++;
			}

			$mediaDia = $mensal / @date("d");
			$mediaDia = number_format($mediaDia, 2, '.', '');

			$mediaProjetado = $mediaDia * @date('t');
			$mediaProjetado = number_format($mediaProjetado, 2, '.', '');

			$mes = @date("t") - @date("d");

			echo "<tr><td>" . $vendasHoje . "</td><td>" . $mensal . "</td><td>" . $mediaDia . "</td><td>" . $mediaProjetado . "</td><td>" . $mes . "</td></tr>";

			echo"</tbody></table><br>";
			 
      } elseif ($this->_getParam('fn') == 'baixaAgenda') {

         $dbA = new Application_Model_DbTable_Agenda();
         $dbA->update(array('baixado' => 1), "id = " . $this->_getParam('id'));
      } elseif ($this->_getParam('fn') == 'baixaPendencia') {

         $dbP = new Application_Model_DbTable_PendenciasVeiculos();
         $dbP->update(array('baixada' => 1), "id = " . $this->_getParam('id'));
      } elseif ($this->_getParam('fn') == 'baixaPagamento') {

         $dbP = new Application_Model_DbTable_RecebimentosNegociacoes();
         $dbP->update(array('baixado' => 1), "id = " . $this->_getParam('id'));
      } elseif ($this->_getParam('fn') == 'baixaLancamento') {

         $dbP = new Application_Model_DbTable_LancamentosFinanceiros();
         $dbP->update(array('baixado' => 1), "id = " . $this->_getParam('id'));
		 
      }elseif ($this->_getParam('fn') == 'baixaPagamentoRecorrente') {

        $dbP = new Application_Model_DbTable_LancamentosFinanceiros();
        $dbItens = new Application_Model_DbTable_ItensFinanceiros();
		
			$arr['id'] = $this->_getParam('id');
			$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
			$arrItem = $dbItens->getItem($arr);
			
			$arrItemSalva['id_financeiro_item'] = $arrItem[0]['id'];
			$arrItemSalva['data_lancamento'] = @date("Y-m-d");
			$arrItemSalva['valor'] = $arrItem[0]['valor_padrao'];
			$arrItemSalva['baixado'] = 1;
			$arrItemSalva['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$arrItemSalva['hora_alteracao'] = @date('Y-m-d H:i:s');

			$dbP->add($arrItemSalva);
			
		
		}elseif ($this->_getParam('fn') == 'getAlarmePendencias'){
			
			$dbRecebimentosPendentes = new Application_Model_DbTable_RecebimentosNegociacoes();
			$dbPagamentosPendentes = new Application_Model_DbTable_LancamentosFinanceiros();
			$dbPendencias = new Application_Model_DbTable_PendenciasVeiculos();
			$dbItensFinanceiros = new Application_Model_DbTable_ItensFinanceiros();
			
			$temPendencia = false;

////////////////INICIO PENDENCIAS////////////////////////////////
			
			$strPendencia = "<table width='100%'>";
			
			$strPendencia .= "<tr>
								<th colspan='3' style='color:#fff; border: solid 1px; padding:3px; text-align:center;'>PENDÊNCIAS</th>
							  <tr>";
			
			$arr['data_apartir'] = @date("Y-m-d");
			$arr['baixado'] = 0;
			$arr['empresa'] = $_SESSION['sessionUser']['id_empresa'];

			foreach($dbPendencias->_get($arr) as $pendencia){
				
				$strPendencia .= "<tr style='cursor:pointer;' onClick='window.location.href=\"/veiculos/edt/id/".$pendencia['id_veiculo']."/p/true\"'>
									<td style='color:#fff; border: solid 1px; padding:3px;'>".strtoUpper(current(explode(" ",$pendencia['descricao_site'])))."</td>
									<td style='color:#fff; border: solid 1px; padding:3px;'>".substr($pendencia['descricao'], 0, 20)."</td>
									<td style='color:#fff; border: solid 1px; padding:3px;'>".implode("/",array_reverse(explode("-",$pendencia['data'])))."</td>
								 <tr>";
				
				$temPendencia = true;
			
			}
			
			$strPendencia .= "</table><br/>";

			
////////////////////////////////INICIO RECEBIMENTOS //////////////////////////////////////
			
			$strRecebimentos = "<table width='100%'>";
			
			$strRecebimentos .= "<tr>
									<th colspan='3' style='color:#fff; border: solid 1px; padding:3px; text-align:center;'>RECEBIMENTOS PENDENTES</th>
							     <tr>";
			
			foreach($dbRecebimentosPendentes->_getAlarme(array("id_empresa"=>$_SESSION['sessionUser']['id_empresa'], "nao_baixado"=>true)) as $recebimentos){
				
				$strRecebimentos .= "<tr style='cursor:pointer;' onClick='window.location.href=\"/negociacoes/edt/id/".$recebimentos['id_negociacao']."/p/true\"'>
									<td style='color:#fff; border: solid 1px; padding:3px;'>".strtoUpper(current(explode(" ",$recebimentos['modelo'])))."</td>
									<td style='color:#fff; border: solid 1px; padding:3px;'>R$ ".money_format("%i",$recebimentos['valor'])."</td>
									<td style='color:#fff; border: solid 1px; padding:3px;'>".implode("/",array_reverse(explode("-",$recebimentos['data'])))."</td>
								 <tr>";
								 
				$temPendencia = true;
			
			}

			$strRecebimentos .= "</table><br/>";
			
///////////////////INICIO PAGAMENTOS PENDENTES //////////////////////////////////////////

			$strPagamentos = "<table style='margin-right:50px;'>";
			
			$strPagamentos .= "<tr>
									<th colspan='4' style='color:#fff; border: solid 1px; padding:3px; text-align:center;'>PAGAMENTOS PENDENTES</th>
							     <tr>";

			foreach($dbItensFinanceiros->_getItensRecorentes(array("id_empresa"=>$_SESSION['sessionUser']['id_empresa'])) as $pagamentos){
			
				$data = explode("-",$pagamentos['data_inicio']);

				if(isset($data[2]) && $data[2] <= @date('d')){

					$arrFiltro['item'] = $pagamentos['id'];
		
					if(!$dbPagamentosPendentes->getlancamentoPorDataItem($arrFiltro)) {
				
						$tData = explode("-", $pagamentos['data_inicio']);
						$tData = $tData[2]."/".@date("m/Y");
					
						$strPagamentos .= "<tr style='cursor:pointer;' onClick='window.location.href=\"/lancamentos-financeiros/add-recorrente/id/".$pagamentos['id']."/p/true\"'>
											<td style='color:#fff; border: solid 1px; padding:3px;'>".$pagamentos['descricao']."</td>
											<td style='color:#fff; border: solid 1px; padding:3px;'>".$pagamentos['item']."</td>
											<td style='color:#fff; border: solid 1px; padding:3px;'>R$ ".money_format("%i",$pagamentos['valor_padrao'])."</td>
											<td style='color:#fff; border: solid 1px; padding:3px;'>".$tData."</td>
										 <tr>";
										 
						$temPendencia = true;
										 
					}
								 
				}
			
			}
			
			$strPagamentos .= "</table><br/>";

			if($temPendencia) {
			
				echo "<table width='95%'><tr><td colspan='3' style='text-align:center;'><label style='color:white; font-size:30px;'>PENDÊNCIAS</label></td></tr><tr><td> ".$strRecebimentos." </td><td> ".$strPendencia."</td><td> ".$strPagamentos."</td></tr></table>";
			
			}else{
				
				echo "";
			
			}
		 
		}
	  
   }

   public function addAction() {

      $dbAgenda = new Application_Model_DbTable_Agenda();

      $this->validaAcesso('gerenciar_agenda');

      if ($this->getRequest()->isPost()) {

         $dados = $_POST;
         $dados['id_usuario'] = $_SESSION['sessionUser']['id'];

         $arrT = explode("/", $dados['data']);
         $dados['data'] = implode("-", array_reverse($arrT));
         $dados['data'] .= " " . $dados['hora'];
         unset($dados['hora']);

         if ($dbAgenda->insert($dados)) {

            $this->view->mensagem = "Compromisso cadastrado com sucesso!";
         } else {

            $this->view->mensagem = "Erro ao cadastrar o compromisso.";
         }
      }
   }

   public function edtAction() {

      $dbAgenda = new Application_Model_DbTable_Agenda();

      $this->validaAcesso('gerenciar_agenda');

      if ($this->getRequest()->isPost()) {

         $dados = $_POST;

         $dados['id_usuario'] = $_SESSION['sessionUser']['id'];

         $arrT = explode("/", $dados['data']);
         $dados['data'] = implode("-", array_reverse($arrT));
         $dados['data'] .= " " . $dados['hora'];
         unset($dados['hora']);

         if (!isset($dados['baixado'])) {
            $dados['baixado'] = 0;
         }

         if ($dbAgenda->update($dados, "id = " . $this->_getParam('id'))) {

            $this->view->mensagem = "Compromisso editado com sucesso!";
         } else {

            $this->view->mensagem = "Erro ao editar o compromisso.";
         }
      }

      $a = $dbAgenda->fetchAll("id = " . $this->_getParam('id'));
      $this->view->a = $a[0];
   }

   public function listaAction() {

      //$this->validaAcesso('listar_financeiro_lancamentos');	

      $dbAgendaLancamentos = new Application_Model_DbTable_Agenda();

      $arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

      if ($this->getRequest()->isPost()) {

         if (isset($_POST['baixado'])) {

            $arrFiltro['baixado'] = $_POST['baixado'];
         }

         if (isset($_POST['data_inicial']) && isset($_POST['data_final'])) {

            $dataTmp = explode("/", $_POST['data_inicial']);
            $_POST['data_inicial'] = implode("-", array_reverse($dataTmp));

            $dataTmp = explode("/", $_POST['data_final']);
            $_POST['data_final'] = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_inicial'] = $_POST['data_inicial'];
            $arrFiltro['data_final'] = $_POST['data_final'];
         } elseif (isset($_POST['data_inicial'])) {

            $dataTmp = explode("/", $_POST['data_inicial']);
            $_POST['data_inicial'] = implode("-", array_reverse($dataTmp));

            $arrFiltro['data_inicial'] = $_POST['data_inicial'];
            $arrFiltro['data_final'] = @date("Y-m-d");
         } elseif (isset($_POST['data_final'])) {

            $dataTmp = explode("/", $_POST['data_final']);
            $_POST['data_final'] = implode("-", array_reverse($dataTmp));
            $arrFiltro['data_final'] = $_POST['data_final'];
         }
      } else {

         $arrFiltro['data_inicial'] = @date("Y") . "-" . @date("m") . "-01";
         $data = @date("Y-m-d", mktime(0, 0, 0, @date("m") + 1, 0, @date("Y")));
         $arrFiltro['data_final'] = $data;
      }

      $idUsuario = $_SESSION['sessionUser']['id'];

      if (isset($idUsuario) && $idUsuario != ADMINISTRATIVO) {

         $arrFiltro['id_usuario'] = $idUsuario;
      }

      $arrAgendaLancamentos = $dbAgendaLancamentos->listaAgenda($arrFiltro);
      $this->view->agenda = $arrAgendaLancamentos;
   }

}
?>

