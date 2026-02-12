<?php

header("Content-Type: text/html; charset=UTF-8",true);

class RelatoriosMesController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Relat&oacute;rios";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function patrimonioAction(){
	
		$this->validaAcesso('relatorios');
	
		$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbPatrimonio = new Application_Model_DbTable_Patrimonio();

		$receber = 0;
		$pagar = 0;
		$soma = 0;
		$somaPatrimonio = 0;
		$strAtual = "";
		$strAnterior = "";
		
		if($this->getRequest()->isPost()){
		
			$_POST['valor'] = str_replace(".","",$_POST['patrimonio']);
			$_POST['valor'] = str_replace(",",".",$_POST['valor']);
			
			$_POST['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			
			unset($_POST['patrimonio']);
		
			$idPatrimonio = $dbPatrimonio->getPatrimonio($_POST);
			
			if($idPatrimonio){
			
				if($dbPatrimonio->edt($idPatrimonio[0]['id'],$_POST)){
			
					$this->view->mensagem = "Patrimonio foi salvo com sucesso!";
			
				}
			
			}else{
		
				if($dbPatrimonio->add($_POST)){
				
					$this->view->mensagem = "Patrimonio foi salvo com sucesso!";
				
				}
			
			}
		
		}
		
		$condicao = true;
		$i = date("m");
		$cont = 0;
		
		while($condicao){
		
			$ano = date("Y", mktime(0, 0, 0, $i, 1, @date("Y") ));
			$mes = $this->mesExtenso(date("m", mktime(0, 0, 0, $i, 1, @date("Y"))));
			
			
			
			$data[$i]['data_extenso'] = $mes." / ".$ano;
			$data[$i]['data_america'] = $ano."-".@date("m", mktime(0, 0, 0, $i, 1, @date("Y")))."-01";

			$i--;
			
			/*if(@date("m",mktime(0, 0, 0, $i, 1, date("Y"))) == @date("m")+9){
			
				$condicao = false;
			
			}*/
			
			$cont--;
			
			if($cont == -3){
			
				$condicao = false;
			
			}
		
		}
		
		$arrRecebimentos = $dbNegociacoes->getRecebimentosNegociacoes($_SESSION['sessionUser']['id_empresa']);
		
		foreach($arrRecebimentos as $recebimento){
		
			if($recebimento['valor'] > 0){
		
				$receber += $recebimento['valor'];
			
			}else{
			
				$pagar += $recebimento['valor'];
			
			}
		
		}
		
		$receber = $receber+$pagar;
		
		$arrRecebimentosGruposFinanceiros = $dbGruposFinanceiros->getLancamentosReceber($_SESSION['sessionUser']['id_empresa']);
		
		$receber += $arrRecebimentosGruposFinanceiros[0]['valor_lancamento'];

		$arrSomaDespesas = $dbVeiculos->getSomaDespesas($_SESSION['sessionUser']['id_empresa']);
		$arrSomaVeiculos = $dbVeiculos->getSomaVeiculos($_SESSION['sessionUser']['id_empresa']);
		
		$custoEstoque = $arrSomaVeiculos[0]['soma_veiculos']+$arrSomaDespesas[0]['soma_despesas'];
		
		
		/////////////////////////INICIO ALGORITMO GRÁFICO//////////////////////
		/////////////////////////BUSCA GRAFICO ANO ATUAL//////////////////////
		$valorMaior = 0;
		$dividor = 0;
		$_busca['data_inicial'] = @date("Y")."-01-01";
		$_busca['data_final'] = @date("Y")."-12-31";
		$_busca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrPatrimonioAtual = $dbPatrimonio->getPatrimonios($_busca);
		
		for($i=1;$i<=12;$i++){
			
			$tem = false;
			
			foreach($arrPatrimonioAtual as $patrimonioAtual){
			
				$mes = explode("-",$patrimonioAtual['data']);
				
				if($mes[1] == $i){
				
					$strAtual .= money_format("%i",$patrimonioAtual['valor']) .",";
					$tem = true;
					
					$soma += $patrimonioAtual['valor'];
					$dividor++;
					
					if($valorMaior < $patrimonioAtual['valor']){
					
						$valorMaior  = $patrimonioAtual['valor'];
					
					}
				
				}
			
			}
			
			if(!$tem){
			
				$strAtual .= "0,";
			
			}

		}
		
		$strAtual = substr($strAtual,0,-1);
		$strAtual = "[".$strAtual."]";
		
		
		
		/////////////////////////BUSCA GRAFICO ANO ANTERIOR//////////////////////
		$_busca['data_inicial'] = (@date("Y")-1)."-01-01";
		$_busca['data_final'] = (@date("Y")-1)."-12-31";
		
		$arrPatrimonioAnterior = $dbPatrimonio->getPatrimonios($_busca);
		
		for($i=1;$i<=12;$i++){
			
			$tem = false;
			
			foreach($arrPatrimonioAnterior as $patrimonioAnterior){
			
				$mesAnterior = explode("-",$patrimonioAnterior['data']);
				
				if($mesAnterior[1] == $i){
				
					$strAnterior .= money_format("%i",$patrimonioAnterior['valor']) .",";
					$tem = true;
					
					$soma += $patrimonioAnterior['valor'];
					$dividor++;
					
					if($valorMaior < $patrimonioAnterior['valor']){
					
						$valorMaior  = $patrimonioAnterior['valor'];
					
					}
				
				}
			
			}
			
			if(!$tem){
			
				$strAnterior .= "0,";
			
			}

		}
		
		$strAnterior = substr($strAnterior,0,-1);
		$strAnterior = "[".$strAnterior."]";
		
		if($dividor != 0){
			$media = money_format("%i",$soma/$dividor);
		}
		/////////////////////////FIM ALGORITMO GRÁFICO//////////////////////
		
		$strTabela = "<table class='table' style='width:820px; margin-left:76px;'>
						<tr>
							<th>Data</th>
							<th>Valor</th>
						</tr>";
						
		$_buscaRelatorio['data_final'] = @date("Y")."-12-31";
		$arrPatrimonioGeral = $dbPatrimonio->getPatrimonios($_buscaRelatorio);
		
		$arrPatrimonioGeral = array_reverse($arrPatrimonioGeral);
		
		foreach($arrPatrimonioGeral as $patrimonioGeral){
		
			$datas = explode("-",$patrimonioGeral['data']);
			
			$somaPatrimonio += $patrimonioGeral['valor'];
			
			$strTabela .=  "<tr>
								<td>".$this->mesExtenso($datas[1])." / ".$datas[0]."</td>
								<td>R$ ".money_format("%i",$patrimonioGeral['valor'])."</td>
							</tr>";
		
		}
		
		$strTabela .=   "<tr>
							<td style='border-left: solid 2px; border-top: solid 2px; border-bottom: solid 2px; text-align:right;'><b>MÉDIA</b></td>
							<td style='border-right: solid 2px; border-top: solid 2px; border-bottom: solid 2px;'><b>R$ ".money_format("%i",$somaPatrimonio/count($arrPatrimonioGeral))."</b></td>
						</tr>";
		
		$strTabela .= "</tabela>";
		
		$this->view->relatorio = $strTabela;
		$this->view->graficoAnterior = $strAnterior;
		$this->view->graficoAtual = $strAtual;
		$this->view->valorMaior = $valorMaior*1.10;
		$this->view->media = "[".$media.",".$media.",".$media.",".$media.",".$media.",".$media.",".$media.",".$media.",".$media.",".$media.",".$media.",".$media."]";
		$this->view->aReceber = $receber;
		$this->view->custoEstoque = $custoEstoque;
		$this->view->data = $data;
	
	}
	
	
	public function despesasAction(){
	
		$this->validaAcesso('relatorios');
		
		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
		$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
		$dbFinanceiras = new Application_Model_DbTable_Financeiras();
		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arr['tipo_grupo'] = true;
		$arr['credito_debito'] = 1;
		
		$this->view->gruposFinanceiros = $dbGruposFinanceiros->_get($arr);
		
		$strTabela = "<table class='table'>
					 <tr>
						<th class='cabeca'>Grupo</th>
						<th class='cabeca'>Item</th>
						<th class='cabeca'>Data</th>
						<th class='cabeca'>Baixado</th>
						<th class='cabeca'>Valor</th>
					 </tr>";
		
		if($this->getRequest()->isPost()){

			$arrBusca['data_inicial'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
			$arrBusca['data_final'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));
			$arrBusca['credito'] = false;
			$arrBusca['id_grupo'] = $_POST['grupo'];
			$arrBusca['baixado'] = $_POST['baixado'];
			$arrBusca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
				
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arrBusca);
				
			$panGrupo = 0;
				
			foreach($arrGruposFinanceiros as $key=>$gruposFinanceiro){
			
				if($gruposFinanceiro['baixado'] == 1){
				
					$baixado = "Sim";
				
				}else{
				
					$baixado = "N&atilde;o";
				
				}
			
				$panGrupo++;
				$somaGrupo += $gruposFinanceiro['valor'];
						
				if($arrGruposFinanceiros[$key+1]['id_grupo'] != $gruposFinanceiro['id_grupo']){
							
					$rowspan = "<tr>
									<td class='tds' rowspan='".$panGrupo."'>".$gruposFinanceiro['descricao']."</td>
									<td class='tds'>".$gruposFinanceiro['item']."</td>
									<td class='tds'>".implode("/",array_reverse(explode("-",$gruposFinanceiro['data_lancamento'])))."</td>
									<td class='tds'>".$baixado."</td>
									<td class='tds'>R$ ".money_format("%i",$gruposFinanceiro['valor'])."</td>
								</tr>";
											
					if($_POST['grupo'] == 0){
							
						$subtotal = "<tr><th colspan='4' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$somaGrupo)."</th></tr>";
				
					}
		
					$strGrupos .= $rowspan.$strGrupo.$subtotal;
					
					$somaGrupos += $somaGrupo;
					$panGrupos += $panGrupo;
			
					$rowspan="";
					$strGrupo="";
					$subtotal="";
					$panGrupo = 0;
					$somaGrupo=0;
				
				}else{
							
					$strGrupo .= "<tr>
									<td class='tds'>".$gruposFinanceiro['item']."</td>
									<td class='tds'>".implode("/",array_reverse(explode("-",$gruposFinanceiro['data_lancamento'])))."</td>
									<td class='tds'>".$baixado."</td>
									<td class='tds'>R$ ".money_format("%i",$gruposFinanceiro['valor'])."</td>
								</tr>";

				}

			}
			
		}else{
			
			$arrBusca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$arrBusca['credito'] = false;
			$arrBusca['data_inicial'] = @date("Y")."-".@date("m")."-01";
			
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arrBusca);
			
			$panGrupo = 0;
			
			foreach($arrGruposFinanceiros as $key=>$gruposFinanceiro){
			
				if($gruposFinanceiro['baixado'] == 1){
				
					$baixado = "Sim";
				
				}else{
				
					$baixado = "N&atilde;o";
				
				}
		
				$panGrupo++;
				$somaGrupo += $gruposFinanceiro['valor'];
					
				if($arrGruposFinanceiros[$key+1]['id_grupo'] != $gruposFinanceiro['id_grupo']){
						
					$rowspan = "<tr>
									<td class='tds' rowspan='".$panGrupo."'>".$gruposFinanceiro['descricao']."</td>
									<td class='tds'>".$gruposFinanceiro['item']."</td>
									<td class='tds'>".implode("/",array_reverse(explode("-",$gruposFinanceiro['data_lancamento'])))."</td>
									<td class='tds'>".$baixado."</td>
									<td class='tds'>R$ ".money_format("%i",$gruposFinanceiro['valor'])."</td>
								</tr>";
										
					if($_POST['grupo'] == 0){
						
						$subtotal = "<tr><th colspan='4' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$somaGrupo)."</th></tr>";
							
					}
						
						
				
					$strGrupos .= $rowspan.$strGrupo.$subtotal;
						
					$somaGrupos += $somaGrupo;
					$panGrupos += $panGrupo;
						
					$rowspan="";
					$strGrupo="";
					$subtotal="";
					$panGrupo = 0;
					$somaGrupo=0;
			
				}else{
						
					$strGrupo .= "<tr>
									<td class='tds'>".$gruposFinanceiro['item']."</td>
									<td class='tds'>".implode("/",array_reverse(explode("-",$gruposFinanceiro['data_lancamento'])))."</td>
									<td class='tds'>".$baixado."</td>
									<td class='tds'>R$ ".money_format("%i",$gruposFinanceiro['valor'])."</td>
								</tr>";

				}

			}
	
		}
		
		$total = $somaGrupos;
		$totalCount = $panGrupos;

		if($totalCount != 0){
		
			$strTotal .= "<tr><td colspan='4' style='border:solid 2px; text-align:right;'>M&Eacute;DIA</td><td style='border:solid 2px;'>R$ ".money_format("%i",$total/$totalCount)."</td></tr>";
		
		}
		
		$strTotal .= "<tr><td colspan='4' style='border:solid 2px; text-align:right;'>TOTAL</td><td style='border:solid 2px;'>R$ ".money_format("%i",$total)."</td></tr>";

		
		$strTabela .= $strGrupos.$strTotal;
		
		$strTabela = "<div style='margin-left:10px; margin-bottom:10px;'><b>Quantidade de Despesas: ".$totalCount."</b></div>".$strTabela;
		
		$this->view->idGrupo = $_POST['grupo'];
		$this->view->idBaixado = $_POST['baixado'];
		$this->view->dataInicial = $_POST['data_inicial'];
		$this->view->dataFinal = $_POST['data_final'];
		$this->view->relatorio = $strTabela."</table>";
	
	}
	
	public function financiamentosAction(){
	
		$this->validaAcesso('relatorios');
	
		$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
		$dbVendedores = new Application_Model_DbTable_Vendedores();
		$dbFinanceiras = new Application_Model_DbTable_Financeiras();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arrFiltro['tipo'] = 0;
		
		$this->view->financeiras = $dbFinanceiras->_get($arrFiltro);
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		//$arr['id_perfil'] = VENDEDOR;

		$this->view->vendedores = $dbVendedores->_get($arr);
	
		$arrBusca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arrBusca['data_inicial_concretizacao'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
		$arrBusca['data_final_concretizacao'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));
		$arrBusca['id_vendedor'] = $_POST['id_vendedor'];
		$arrBusca['id_financeira'] = $_POST['id_financeira'];
		$arrBusca['aprovada'] = 1;
		$arrBusca['relatorio_fi'] = 1;
		
		
		$strTabela = "<table class='table'>
			 <tr>
				<th class='cabeca'>Financeira</th>
				<th class='cabeca'>Vendedor</th>
				<th class='cabeca'>Ve&iacute;culo</th>
				<th class='cabeca'>Placa</th>
				<th class='cabeca'>Data</th>
				<th class='cabeca'>Valor Venda</th>
				<th class='cabeca'>Valor Financiado</th>
				<th class='cabeca'>F&I </th>
			</tr>";
	
		if($this->getRequest()->isPost()){
	
			$arrNegociacoesFi = $dbNegociacoes->getFinanciamentos($arrBusca);
		
			$panFi = 0;

			foreach($arrNegociacoesFi as $key=>$negociacaoFi){
		
				$dataTemp = explode(" ",$negociacaoFi['data_concretizacao']);
				$negociacaoFi['data'] = $dataTemp[0];
				
				if($negociacaoFi['id_financeira']){
			
					$panFi++;
					$somaFi += (($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100;
					$somaValorFi += $negociacaoFi['valor_financiado'];
					$somaValorVenda += $negociacaoFi['valor_venda'];
			
					if($arrNegociacoesFi[$key+1]['id_financeira'] != $negociacaoFi['id_financeira']){
				
						$rowspan = "<tr>
										<td class='tds' rowspan='".$panFi."'>".$negociacaoFi['nome']."</td>
										<td class='tds'>".$negociacaoFi['nomeUsuario']."</td>
										<td class='tds'>".$negociacaoFi['modelo']."</td>
										<td class='tds'>".$negociacaoFi['placa']."</td>
										<td class='tds'>".implode("/",array_reverse(explode("-",$negociacaoFi['data'])))."</td>
										<td class='tds'>R$ ".money_format("%i",$negociacaoFi['valor_venda'])."</td>
										<td class='tds'>R$ ".money_format("%i",$negociacaoFi['valor_financiado'])."</td>
										<td class='tds'>R$ ".money_format("%i",(($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100)."</td>
									</tr>";
						
						if($_POST['id_financeira'] == 0){
					
							$subtotal = "<tr><th colspan='5' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$somaValorVenda)."</th><th class='cabeca'>R$ ".money_format("%i",$somaValorFi)."</th><th class='cabeca'>R$ ".money_format("%i",$somaFi)."</th></tr>";
						
						}
				
						$strfI .= $rowspan.$strf.$subtotal;
						
						$panFis += $panFi;
						$somaFis += $somaFi;
						$somaValorFis += $somaValorFi;
						$somaValorVendas += $somaValorVenda;
						
						$panFi = 0;
						$somaFi = 0;
						$somaValorFi = 0;
						$somaValorVenda = 0;
						$strf = "";
						
					
					}else{
			
						$strf .= "<tr>
										<td class='tds'>".$negociacaoFi['nomeUsuario']."</td>
										<td class='tds'>".$negociacaoFi['modelo']."</td>
										<td class='tds'>".$negociacaoFi['placa']."</td>
										<td class='tds'>".implode("/",array_reverse(explode("-",$negociacaoFi['data'])))."</td>
										<td class='tds'>R$ ".money_format("%i",$negociacaoFi['valor_venda'])."</td>
										<td class='tds'>R$ ".money_format("%i",$negociacaoFi['valor_financiado'])."</td>
										<td class='tds'>R$ ".money_format("%i",(($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100)."</td>
									</tr>";
					}
				}

			}
		
		}else{
			
			$arrBusca['data_inicial_concretizacao'] = @date("Y")."-".@date("m") ."-01";
			
			$arrNegociacoesFi = $dbNegociacoes->getFinanciamentos($arrBusca);
		
			$panFi = 0;

			foreach($arrNegociacoesFi as $key=>$negociacaoFi){
		
				$dataTemp = explode(" ",$negociacaoFi['data_concretizacao']);
				$negociacaoFi['data'] = $dataTemp[0];
				
				if($negociacaoFi['id_financeira']){
			
					$panFi++;
					$somaFi += (($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100;
					$somaValorFi += $negociacaoFi['valor_financiado'];
					$somaValorVenda += $negociacaoFi['valor_venda'];
			
					if($arrNegociacoesFi[$key+1]['id_financeira'] != $negociacaoFi['id_financeira']){
				
						$rowspan = "<tr>
										<td class='tds' rowspan='".$panFi."'>".$negociacaoFi['nome']."</td>
										<td class='tds'>".$negociacaoFi['nomeUsuario']."</td>
										<td class='tds'>".$negociacaoFi['modelo']."</td>
										<td class='tds'>".$negociacaoFi['placa']."</td>
										<td class='tds'>".implode("/",array_reverse(explode("-",$negociacaoFi['data'])))."</td>
										<td class='tds'>R$ ".money_format("%i",$negociacaoFi['valor_venda'])."</td>
										<td class='tds'>R$ ".money_format("%i",$negociacaoFi['valor_financiado'])."</td>
										<td class='tds'>R$ ".money_format("%i",(($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100)."</td>
									</tr>";
						
						if($_POST['id_financeira'] == 0){
					
							$subtotal = "<tr><th colspan='5' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$somaValorVenda)."</th><th class='cabeca'>R$ ".money_format("%i",$somaValorFi)."</th><th class='cabeca'>R$ ".money_format("%i",$somaFi)."</th></tr>";
						
						}
				
						$strfI .= $rowspan.$strf.$subtotal;
						
						$panFis += $panFi;
						$somaFis += $somaFi;
						$somaValorFis += $somaValorFi;
						$somaValorVendas += $somaValorVenda;
						
						$panFi = 0;
						$somaFi = 0;
						$somaValorFi = 0;
						$somaValorVenda = 0;
						$strf = "";
						
					
					}else{
			
						$strf .= "<tr>
										<td class='tds'>".$negociacaoFi['nomeUsuario']."</td>
										<td class='tds'>".$negociacaoFi['modelo']."</td>
										<td class='tds'>".$negociacaoFi['placa']."</td>
										<td class='tds'>".implode("/",array_reverse(explode("-",$negociacaoFi['data'])))."</td>
										<td class='tds'>R$ ".money_format("%i",$negociacaoFi['valor_venda'])."</td>
										<td class='tds'>R$ ".money_format("%i",$negociacaoFi['valor_financiado'])."</td>
										<td class='tds'>R$ ".money_format("%i",(($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100)."</td>
									</tr>";
					}
				}

			}
		
		}
		
		$strTabela .= $strfI;
		
		if($panFis != 0){
		
			$strTotal .= "<tr><td colspan='5' style='border:solid 2px; text-align:right;'>M&Eacute;DIA</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaValorVendas/$panFis)."</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaValorFis/$panFis)."</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaFis/$panFis)."</td></tr>";
		
		}
		
		$strTotal .= "<tr><td colspan='5' style='border:solid 2px; text-align:right;'>TOTAL</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaValorVendas)."</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaValorFis)."</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaFis)."</td></tr>";
	
		$strTabela = "<div style='margin-left:10px; margin-bottom:10px;'><b>Quantidade de Financiamentos: ".$panFis."</b></div>".$strTabela.$strTotal."</table>";
		
		$this->view->id_vendedor = $_POST['id_vendedor'];
		$this->view->id_financeira = $_POST['id_financeira'];
		$this->view->dataInicial = $_POST['data_inicial'];
		$this->view->dataFinal = $_POST['data_final'];
		$this->view->relatorio = $strTabela;
	
	}
	
	
	public function garantiasAction(){
	
		$this->validaAcesso('relatorios');
		
		$dbGarantias = new Application_Model_DbTable_Garantias();
		$dbFornecedores = new Application_Model_DbTable_Fornecedores();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$this->view->fornecedores = $dbFornecedores->_get($arrFiltro);
		
		$arrBusca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

		if(isset($_POST['data_inicial']) && $_POST['data_inicial'] != ""){
			$arrBusca['data_inicial'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
		}
		if(isset($_POST['data_final']) && $_POST['data_final'] != ""){
			$arrBusca['data_final'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));
		}
		if(isset($_POST['id_fornecedor']) && $_POST['id_fornecedor'] != "0"){
			$arrBusca['id_fornecedor'] = $_POST['id_fornecedor'];
		}

		$arrBusca['relatorio_garantias'] = 1;

		$strTabela = "<table class='table'>
			 <tr>
				<th class='cabeca' width='135px'>Fornecedor</th>
				<th class='cabeca'>Descri&ccedil;&atilde;o</th>
				<th class='cabeca' width='135px'>Ve&iacute;culo</th>
				<th class='cabeca' width='60px'>Placa</th>
				<th class='cabeca'>Data</th>
				<th class='cabeca'width='70px'>Valor</th>
			</tr>";
		
		$color = true;
		
		$rows = 0;
		$totalGarantias = 0;
		$totalGeralGarantias = 0;
		$rowsTotal = 0;
		$strTab = "";
		$strTotal = "";
		$subtotal = "";

		if($this->getRequest()->isPost()){

			$arrGarantias = $dbGarantias->_get($arrBusca);
		
			foreach($arrGarantias as $key=>$garantia){
			
				$totalGarantias += $garantia['custo'];
				$rows++;
			
				if(!isset($arrGarantias[$key+1]['id_fornecedor']) || ($arrGarantias[$key+1]['id_fornecedor'] != $garantia['id_fornecedor'])){
					
					$color = true;
					
					$rowspan = "<tr>
									<td class='tds' rowspan='".$rows."'>".$garantia['razao_social']."</td>
									<td class='tds'>".$garantia['descricao_defeito']."</td>
									<td class='tds'>".substr($garantia['modelo'],0,20)."</td>
									<td class='tds'>".$garantia['placa']."</td>
									<td class='tds'>".implode("/",array_reverse(explode("-",$garantia['data_saida'])))."</td>
									<td class='tds'>R$ ".money_format("%i",$garantia['custo'])."</td>
								</tr>";
						
					if(!$_POST['id_fornecedor']){
					
						$subtotal = "<tr><th colspan='5' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$totalGarantias)."</th></tr>";
						
					}
						
					$totalGeralGarantias += $totalGarantias;
					$rowsTotal += $rows;
					$strTabela .= $rowspan.$strTab.$subtotal;
					
					$strTab="";
					$rows = 0;
					$totalGarantias = 0;
					
				}else{
				
					if($color){
						
						$bgcolor = "style='background-color: #DDDDDD;'";
						$color = false;
					
					}else{
						
						$bgcolor = "style='background-color: #FFFFFF;'";
						$color = true;
					
					}
				
					$strTab .= "<tr ".$bgcolor.">
										<td class='tds'>".$garantia['descricao_defeito']."</td>
										<td class='tds'>".substr($garantia['modelo'],0,20)."</td>
										<td class='tds'>".$garantia['placa']."</td>
										<td class='tds'>".implode("/",array_reverse(explode("-",$garantia['data_saida'])))."</td>
										<td class='tds'>R$ ".money_format("%i",$garantia['custo'])."</td>
									</tr>";
				
				
				}
			
			}
		
		}else{
		
			$arrBusca['data_inicial'] = @date("Y")."-".@date("m")."-01";
		
			$arrGarantias = $dbGarantias->_get($arrBusca);
		
			foreach($arrGarantias as $key=>$garantia){
			
				$totalGarantias += $garantia['custo'];
				$rows++;
			
				if(!isset($arrGarantias[$key+1]['id_fornecedor']) || ($arrGarantias[$key+1]['id_fornecedor'] != $garantia['id_fornecedor'])){
			
					$rowspan = "<tr>
									<td class='tds' rowspan='".$rows."'>".$garantia['razao_social']."</td>
									<td class='tds'>".$garantia['descricao_defeito']."</td>
									<td class='tds'>".substr($garantia['modelo'],0,20)."</td>
									<td class='tds'>".$garantia['placa']."</td>
									<td class='tds'>".implode("/",array_reverse(explode("-",$garantia['data_saida'])))."</td>
									<td class='tds'>R$ ".money_format("%i",$garantia['custo'])."</td>
								</tr>";
						
					$color = true;
					
					
					$subtotal = "<tr><th colspan='5' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$totalGarantias)."</th></tr>";
					
					$totalGeralGarantias += $totalGarantias;
					$rowsTotal += $rows;
					$strTabela .= $rowspan.$strTab.$subtotal;
					
					$strTab="";
					$rows = 0;
					$totalGarantias = 0;
					
				}else{
				
					if($color){
						
						$bgcolor = "style='background-color: #DDDDDD;'";
						$color = false;
					
					}else{
						
						$bgcolor = "style='background-color: #FFFFFF;'";
						$color = true;
					
					}
				
					$strTab .= "<tr ".$bgcolor.">
										<td class='tds'>".$garantia['descricao_defeito']."</td>
										<td class='tds'>".substr($garantia['modelo'],0,20)."</td>
										<td class='tds'>".$garantia['placa']."</td>
										<td class='tds'>".implode("/",array_reverse(explode("-",$garantia['data_saida'])))."</td>
										<td class='tds'>R$ ".money_format("%i",$garantia['custo'])."</td>
									</tr>";
				
				
				}
			
			}
		
		}
		
		if(count($arrGarantias) != 0){
		
			$strTotal .= "<tr><td colspan='5' style='border:solid 2px; text-align:right;'>M&Eacute;DIA</td><td style='border:solid 2px;'>R$ ".money_format("%i",$totalGeralGarantias/$rowsTotal)."</td></tr>";
		
		}
		
		$strTotal .= "<tr><td colspan='5' style='border:solid 2px; text-align:right;'>TOTAL</td><td style='border:solid 2px;'>R$ ".money_format("%i",$totalGeralGarantias)."</td></tr>";
	
		$strTabela = "<div style='margin-left:10px; margin-bottom:10px;'><b>Quantidade de Garantias: ".count($arrGarantias)."</b></div>".$strTabela.$strTotal."</table>";
		
		$this->view->relatorio = $strTabela;
		if(isset($_POST['data_inicial'])){
			$this->view->dataInicial = $_POST['data_inicial'];
		}
		if(isset($_POST['data_final'])){
			$this->view->dataFinal = $_POST['data_final'];
		}
	
	}
	
	
	public function receitasAction(){
	
		$this->validaAcesso('relatorios');
		
		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
		$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
		$dbFinanceiras = new Application_Model_DbTable_Financeiras();
		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arr['tipo_grupo'] = true;
		$arr['credito_debito'] = 0;
		
		$this->view->gruposFinanceiros = $dbGruposFinanceiros->_get($arr);
		
		$strTabela = "<table class='table'>
					 <tr>
						<th class='cabeca'>Receita</th>
						<th class='cabeca'>Cliente / Despachante / Financeira / Item</th>
						<th class='cabeca'>Ve&iacute;culo</th>
						<th class='cabeca'>Placa</th>
						<th class='cabeca'>Data</th>
						<th class='cabeca'>Valor</th>
					 </tr>";
					 
		$color = true;
		$strComissoes = "";
		$strNegociacoes = "";
		$strfI = "";
		$strTotal = "";
		$strGrupos = "";
		$subtotal = "";
		$somaComissao = 0;
		$somaLucro = 0;
		$somaFi = 0;
		$somaGrupos = 0;
		$panGrupos = 0;
		$panFi = 0;
		$arrDespachantes = array();
		$arrNegociacoes = array();
		
		if($this->getRequest()->isPost()){

			$arrBusca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$arrBusca['aprovada'] = 1;
			$arrBusca['data_inicial_concretizacao'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
			$arrBusca['data_final_concretizacao'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));

			if($_POST['receita'] == -1 || $_POST['receita'] == 0){

				$arrBusca['tipo'] = 1;
				
				if($_POST['data_inicial']){
				
					$arrBusca['data_inicial'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
			
				}
			
				if($_POST['data_final']){
					
					$arrBusca['data_final'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));
				
				}
		
				$arrDespachantes = $dbFinanceiras->getFinanceirasDespachantes($arrBusca);
				
				unset($arrBusca['data_inicial']);
				unset($arrBusca['data_final']);
		
				foreach($arrDespachantes as $key=>$despachante){

					$dataTemp = explode(" ",$despachante['data']);
					
					$despachante['data'] = $dataTemp[0];
				
					if(!isset($arrDespachantes[$key+1]['id']) || ($arrDespachantes[$key+1]['id'] == "")){
			
						$rowspan = "<tr>
										<td class='tds' rowspan='".count($arrDespachantes)."'>Comiss&otilde;es Despachantes</td>
										<td class='tds'>".$despachante['nome']."</td>
										<td class='tds'>".$despachante['modelo']."</td>
										<td class='tds'>".$despachante['placa']."</td>
										<td class='tds'>".implode("/",array_reverse(explode("-",$despachante['data'])))."</td>
										<td class='tds'>R$ ".money_format("%i",$despachante['imposto'])."</td>
									</tr>";
									
						$color = true;
						
						$somaComissao += $despachante['imposto'];
							
						$subtotal = "<tr><th colspan='5' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$somaComissao)."</th></tr>";
		
						$strComissoes = $rowspan.$strComissoes.$subtotal;
						
					}else{
					
						if($color){
							
							$bgColor = "style='background-color:#DDDDDD;'";
							$color = false;
						
						}else{
							
							$bgColor = "style='background-color:#FFFFFF;'";
							$color = true;
						
						}
					
						$strComissoes .= "<tr ".$bgColor.">
											<td class='tds'>".$despachante['nome']."</td>
											<td class='tds'>".$despachante['modelo']."</td>
											<td class='tds'>".$despachante['placa']."</td>
											<td class='tds'>".implode("/",array_reverse(explode("-",$despachante['data'])))."</td>
											<td class='tds'>R$ ".money_format("%i",$despachante['imposto'])."</td>
										 </tr>";
						
						$somaComissao += $despachante['imposto'];
					
					}
					
				}
			
			}
			
			if($_POST['receita'] == -3 || $_POST['receita'] == 0){
			
				$arrNegociacoes = $dbNegociacoes->_get($arrBusca);
				
				foreach($arrNegociacoes as $key=>$negociacao){
				
					$dataTemp = explode(" ",$negociacao['data_concretizacao']);
					$negociacao['data_concretizacao'] = $dataTemp[0];
					
					$arrSomaDespesa = $dbDespesasVeiculos->getSomaDespesas($negociacao['id_veiculo']);
				
					$somaLucro += ($negociacao['valor_venda']-($arrSomaDespesa[0]['valor_despesas']+$negociacao['valor_aquisicao']));
				
					if(!isset($arrNegociacoes[$key+1]['id']) || $arrNegociacoes[$key+1]['id'] == ""){
					
						$rowspan = "<tr>
										<td class='tds' rowspan='".count($arrNegociacoes)."'>Negocia&ccedil;&otilde;es</td>
										<td class='tds'>".$negociacao['nome']."</td>
										<td class='tds'>".$negociacao['modelo']."</td>
										<td class='tds'>".$negociacao['placa']."</td>
										<td class='tds'>".implode("/",array_reverse(explode("-",$negociacao['data_concretizacao'])))."</td>
										<td class='tds'>R$ ".money_format("%i",$negociacao['valor_venda']-($arrSomaDespesa[0]['valor_despesas']+$negociacao['valor_aquisicao']))."</td>
									</tr>";
									
						$color = true;
						
						if($_POST['receita'] != -3){
					
							$subtotal = "<tr><th colspan='5' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$somaLucro)."</th></tr>";
						
						}
						
						$strNegociacoes = $rowspan.$strNegociacoes.$subtotal;
					
					}else{
					
						if($color){
							
							$bgColor = "style='background-color:#DDDDDD;'";
							$color = false;
						
						}else{
							
							$bgColor = "style='background-color:#FFFFFF;'";
							$color = true;
						
						}
				
						$strNegociacoes .= "<tr ".$bgColor.">
												<td class='tds'>".$negociacao['nome']."</td>
												<td class='tds'>".$negociacao['modelo']."</td>
												<td class='tds'>".$negociacao['placa']."</td>
												<td class='tds'>".implode("/",array_reverse(explode("-",$negociacao['data_concretizacao'])))."</td>
												<td class='tds'>R$ ".money_format("%i",$negociacao['valor_venda']-($arrSomaDespesa[0]['valor_despesas']+$negociacao['valor_aquisicao']))."</td>
											</tr>";
				
					}
				
				}
			
			}
			
			if($_POST['receita'] == -2 || $_POST['receita'] == 0){
			
				$arrNegociacoesFi = $dbNegociacoes->_get($arrBusca);
	
				$panFi = 0;
				
				foreach($arrNegociacoesFi as $key=>$negociacaoFi){
					
					$dataTemp = explode(" ",$negociacaoFi['data_concretizacao']);
					$negociacaoFi['data'] = $dataTemp[0];
			
					if($negociacaoFi['nome_financeira'] != ""){
					
						$panFi++;
						$somaFi += (($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100;
						
						if(!isset($arrNegociacoesFi[$key+1]['id']) || $arrNegociacoesFi[$key+1]['id'] == ""){
							
							$rowspan = "<tr>
											<td class='tds' rowspan='".$panFi."'>F&I </td>
											<td class='tds'>".$negociacaoFi['nome_financeira']." / ".$negociacaoFi['nome']."</td>
											<td class='tds'>".$negociacaoFi['modelo']."</td>
											<td class='tds'>".$negociacaoFi['placa']."</td>
											<td class='tds'>".implode("/",array_reverse(explode("-",$negociacaoFi['data'])))."</td>
											<td class='tds'>R$ ".money_format("%i",(($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100)."</td>
										</tr>";
											
							if($_POST['receita'] != -2){
							
								$subtotal = "<tr><th colspan='5' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$somaFi)."</th></tr>";
								
							}
								
							$strfI = $rowspan.$strfI.$subtotal;
				
						}else{
							
							if($color){
							
								$bgColor = "style='background-color:#DDDDDD;'";
								$color = false;
						
							}else{
								
								$bgColor = "style='background-color:#FFFFFF;'";
								$color = true;
							
							}
							
							$strfI .= "<tr ".$bgColor.">
											<td class='tds'>".$negociacaoFi['nome_financeira']." / ".$negociacaoFi['nome']."</td>
											<td class='tds'>".$negociacaoFi['modelo']."</td>
											<td class='tds'>".$negociacaoFi['placa']."</td>
											<td class='tds'>".implode("/",array_reverse(explode("-",$negociacaoFi['data'])))."</td>
											<td class='tds'>R$ ".money_format("%i",(($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100)."</td>
										</tr>";

						}
					}
					
				}
				
			}
			
			
			if($_POST['receita'] >= 0){
			
				$arrBusca['credito'] = true;
				$arrBusca['data_inicial'] = $arrBusca['data_inicial_concretizacao'];
				$arrBusca['data_final'] = $arrBusca['data_final_concretizacao'];
				$arrBusca['id_grupo'] = $_POST['receita'];
				
				$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arrBusca);
				
				$panGrupo = 0;
				
				foreach($arrGruposFinanceiros as $key=>$gruposFinanceiro){
			
					$panGrupo++;
					$somaGrupo += $gruposFinanceiro['valor'];
						
					if($arrGruposFinanceiros[$key+1]['id_grupo'] != $gruposFinanceiro['id_grupo']){
							
						$rowspan = "<tr>
										<td class='tds' rowspan='".$panGrupo."'>".$gruposFinanceiro['descricao']."</td>
										<td class='tds'>".$gruposFinanceiro['item']."</td>
										<td class='tds'>N/A</td>
										<td class='tds'>N/A</td>
										<td class='tds'>".implode("/",array_reverse(explode("-",$gruposFinanceiro['data_lancamento'])))."</td>
										<td class='tds'>R$ ".money_format("%i",$gruposFinanceiro['valor'])."</td>
									</tr>";
									
						$color = true;
											
						if($_POST['receita'] != -2){
							
							$subtotal = "<tr><th colspan='5' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$somaGrupo)."</th></tr>";
								
						}
							
							
					
						$strGrupos .= $rowspan.$strGrupo.$subtotal;
							
						$somaGrupos += $somaGrupo;
						$panGrupos += $panGrupo;
							
						$rowspan="";
						$strGrupo="";
						$subtotal="";
						$panGrupo = 0;
						$somaGrupo=0;
				
					}else{
						
						if($color){
							
							$bgColor = "style='background-color:#DDDDDD;'";
							$color = false;
						
						}else{
							
							$bgColor = "style='background-color:#FFFFFF;'";
							$color = true;
						
						}
						
						
						$strGrupo .= "<tr ".$bgColor.">
										<td class='tds'>".$gruposFinanceiro['item']."</td>
										<td class='tds'>N/A</td>
										<td class='tds'>N/A</td>
										<td class='tds'>".implode("/",array_reverse(explode("-",$gruposFinanceiro['data_lancamento'])))."</td>
										<td class='tds'>R$ ".money_format("%i",$gruposFinanceiro['valor'])."</td>
									</tr>";

					}

				}
			
			}
			
		}else{
			
			$arrBusca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$arrBusca['aprovada'] = 1;
			$arrBusca['data_inicial_concretizacao'] = @date("Y")."-".@date("m")."-01";
			$arrBusca['data_inicial'] = @date("Y")."-".@date("m")."-01";
			$arrBusca['tipo'] = 1;
		
			$arrDespachantes = $dbFinanceiras->getFinanceirasDespachantes($arrBusca);
		
			foreach($arrDespachantes as $key=>$despachante){
		
				$dataTemp = explode(" ",$despachante['data']);
					
				$despachante['data'] = $dataTemp[0];
				
				if(!isset($arrDespachantes[$key+1]['id']) || ($arrDespachantes[$key+1]['id'] == "")){
			
					$rowspan = "<tr>
									<td class='tds' rowspan='".count($arrDespachantes)."'>Comiss&otilde;es Despachantes</td>
									<td class='tds'>".$despachante['nome']."</td>
									<td class='tds'>".$despachante['modelo']."</td>
									<td class='tds'>".$despachante['placa']."</td>
									<td class='tds'>".implode("/",array_reverse(explode("-",$despachante['data'])))."</td>
									<td class='tds'>R$ ".money_format("%i",$despachante['imposto'])."</td>
								</tr>";
								
					$color = true;
						
					$somaComissao += $despachante['imposto'];
							
					$subtotal = "<tr><th colspan='5' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$somaComissao)."</th></tr>";
		
					$strComissoes = $rowspan.$strComissoes.$subtotal;
						
				}else{
					
					if($color){
							
						$bgColor = "style='background-color:#DDDDDD;'";
						$color = false;
						
					}else{
							
						$bgColor = "style='background-color:#FFFFFF;'";
						$color = true;
						
					}
					
					$strComissoes .= "<tr ".$bgColor.">
										<td class='tds'>".$despachante['nome']."</td>
										<td class='tds'>".$despachante['modelo']."</td>
										<td class='tds'>".$despachante['placa']."</td>
										<td class='tds'>".implode("/",array_reverse(explode("-",$despachante['data'])))."</td>
										<td class='tds'>R$ ".money_format("%i",$despachante['imposto'])."</td>
										</tr>";
						
					$somaComissao += $despachante['imposto'];
					
				}
					
			}

			$arrNegociacoes = $dbNegociacoes->_get($arrBusca);
			
			foreach($arrNegociacoes as $key=>$negociacao){
			
				$dataTemp = explode(" ",$negociacao['data_concretizacao']);
				$negociacao['data_concretizacao'] = $dataTemp[0];
				
				$arrSomaDespesa = $dbDespesasVeiculos->getSomaDespesas($negociacao['id_veiculo']);
			
				$somaLucro += ($negociacao['valor_venda']-($arrSomaDespesa[0]['valor_despesas']+$negociacao['valor_aquisicao']));
				
				if(!isset($arrNegociacoes[$key+1]['id']) || ($arrNegociacoes[$key+1]['id'] == "")){
					
					$rowspan = "<tr>
									<td class='tds' rowspan='".count($arrNegociacoes)."'>Negocia&ccedil;&otilde;es</td>
									<td class='tds'>".$negociacao['nome']."</td>
									<td class='tds'>".$negociacao['modelo']."</td>
									<td class='tds'>".$negociacao['placa']."</td>
									<td class='tds'>".implode("/",array_reverse(explode("-",$negociacao['data_concretizacao'])))."</td>
									<td class='tds'>R$ ".money_format("%i",$negociacao['valor_venda']-($arrSomaDespesa[0]['valor_despesas']+$negociacao['valor_aquisicao']))."</td>
								</tr>";
								
					$color = true;
					
					$subtotal = "<tr><th colspan='5' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$somaLucro)."</th></tr>";
			
					$strNegociacoes = $rowspan.$strNegociacoes.$subtotal;
				
				}else{
			
					if($color){
			
						$bgColor = "style='background-color:#DDDDDD;'";
						$color = false;
						
					}else{
			
						$bgColor = "style='background-color:#FFFFFF;'";
						$color = true;
						
					}
			
					$strNegociacoes .= "<tr ".$bgColor.">
											<td class='tds'>".$negociacao['nome']."</td>
											<td class='tds'>".$negociacao['modelo']."</td>
											<td class='tds'>".$negociacao['placa']."</td>
											<td class='tds'>".implode("/",array_reverse(explode("-",$negociacao['data_concretizacao'])))."</td>
											<td class='tds'>R$ ".money_format("%i",$negociacao['valor_venda']-($arrSomaDespesa[0]['valor_despesas']+$negociacao['valor_aquisicao']))."</td>
										</tr>";
			
				}
			
			}
			
			
			$arrNegociacoesFi = $dbNegociacoes->_get($arrBusca);
	
			$panFi = 0;
			
			foreach($arrNegociacoesFi as $key=>$negociacaoFi){
				
				$dataTemp = explode(" ",$negociacaoFi['data_concretizacao']);
				$negociacaoFi['data'] = $dataTemp[0];
		
				if($negociacaoFi['nome_financeira'] != ""){
					$panFi++;
					$somaFi += (($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100;
					
					if(!isset($arrNegociacoesFi[$key+1]['id']) || ($arrNegociacoesFi[$key+1]['id'] == "")){
						
						$rowspan = "<tr>
										<td class='tds' rowspan='".$panFi."'>F&I </td>
										<td class='tds'>".$negociacaoFi['nome_financeira']." / ".$negociacaoFi['nome']."</td>
										<td class='tds'>".$negociacaoFi['modelo']."</td>
										<td class='tds'>".$negociacaoFi['placa']."</td>
										<td class='tds'>".implode("/",array_reverse(explode("-",$negociacaoFi['data'])))."</td>
										<td class='tds'>R$ ".money_format("%i",(($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100)."</td>
									</tr>";
										
						if(!isset($_POST['receita']) || $_POST['receita'] != -2){
						
							$subtotal = "<tr><th colspan='5' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$somaFi)."</th></tr>";
							
						}
							
						$strfI = $rowspan.$strfI.$subtotal;
						
						$color = true;
			
					}else{
						
						if($color){
			
							$bgColor = "style='background-color:#DDDDDD;'";
							$color = false;
						
						}else{
				
							$bgColor = "style='background-color:#FFFFFF;'";
							$color = true;
							
						}
						
						$strfI .= "<tr ".$bgColor.">
										<td class='tds'>".$negociacaoFi['nome_financeira']." / ".$negociacaoFi['nome']."</td>
										<td class='tds'>".$negociacaoFi['modelo']."</td>
										<td class='tds'>".$negociacaoFi['placa']."</td>
										<td class='tds'>".implode("/",array_reverse(explode("-",$negociacaoFi['data'])))."</td>
										<td class='tds'>R$ ".money_format("%i",(($negociacaoFi['retorno_financeira']*1.2)*$negociacaoFi['valor_financiado'])/100)."</td>
									</tr>";

					}
				}
				
			}
			
			
			$arrBusca['credito'] = true;
			
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arrBusca);
			
			$panGrupo = 0;
			
			foreach($arrGruposFinanceiros as $key=>$gruposFinanceiro){
		
				$panGrupo++;
				$somaGrupo += $gruposFinanceiro['valor'];
					
				if($arrGruposFinanceiros[$key+1]['id_grupo'] != $gruposFinanceiro['id_grupo']){
						
					$rowspan = "<tr>
									<td class='tds' rowspan='".$panGrupo."'>".$gruposFinanceiro['descricao']."</td>
									<td class='tds'>".$gruposFinanceiro['item']."</td>
									<td class='tds'>N/A</td>
									<td class='tds'>N/A</td>
									<td class='tds'>".implode("/",array_reverse(explode("-",$gruposFinanceiro['data_lancamento'])))."</td>
									<td class='tds'>R$ ".money_format("%i",$gruposFinanceiro['valor'])."</td>
								</tr>";
										
					if($_POST['receita'] != -2){
						
						$subtotal = "<tr><th colspan='5' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$somaGrupo)."</th></tr>";
							
					}
						
					$color = true;
				
					$strGrupos .= $rowspan.$strGrupo.$subtotal;
						
					$somaGrupos += $somaGrupo;
					$panGrupos += $panGrupo;
						
					$rowspan="";
					$strGrupo="";
					$subtotal="";
					$panGrupo = 0;
					$somaGrupo=0;
			
				}else{
					
					if($color){
			
						$bgColor = "style='background-color:#DDDDDD;'";
						$color = false;
						
					}else{
				
						$bgColor = "style='background-color:#FFFFFF;'";
						$color = true;
							
					}
					
					$strGrupo .= "<tr ".$bgColor.">
									<td class='tds'>".$gruposFinanceiro['item']."</td>
									<td class='tds'>N/A</td>
									<td class='tds'>N/A</td>
									<td class='tds'>".implode("/",array_reverse(explode("-",$gruposFinanceiro['data_lancamento'])))."</td>
									<td class='tds'>R$ ".money_format("%i",$gruposFinanceiro['valor'])."</td>
								</tr>";

				}

			}
	
		}
		
		$total = $somaLucro+$somaComissao+$somaFi+$somaGrupos;
		$totalCount = count($arrNegociacoes)+count($arrDespachantes)+$panFi+$panGrupos;

		if($totalCount != 0){
		
			$strTotal .= "<tr><td colspan='5' style='border:solid 2px; text-align:right;'>M&Eacute;DIA</td><td style='border:solid 2px;'>R$ ".money_format("%i",$total/$totalCount)."</td></tr>";
		
		}
		
		$strTotal .= "<tr><td colspan='5' style='border:solid 2px; text-align:right;'>TOTAL</td><td style='border:solid 2px;'>R$ ".money_format("%i",$total)."</td></tr>";

		
		$strTabela .= $strNegociacoes.$strComissoes.$strfI.$strGrupos.$strTotal;
		
		$strTabela = "<div style='margin-left:10px; margin-bottom:10px;'><b>Quantidade de Receitas: ".$totalCount."</b></div>".$strTabela;
		
		if(isset($_POST['receita'])){
			$this->view->idGrupo = $_POST['receita'];
		}
		if(isset($_POST['data_inicial'])){
			$this->view->dataInicial = $_POST['data_inicial'];
		}
		if(isset($_POST['data_final'])){
			$this->view->dataFinal = $_POST['data_final'];
		}
		$this->view->relatorio = $strTabela."</table>";
	
	}
	
	
	public function vendasMesAction(){
		
		$this->validaAcesso('relatorios');
		
		$dbVendedores = new Application_Model_DbTable_Vendedores();
		$dbCoresRelatorios = new Application_Model_DbTable_CoresRelatorios();
		
		$arrBuscaGarantia['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		//$arr['id_perfil'] = VENDEDOR;
		
		$arrCores = $dbCoresRelatorios->_get($arr);
			
		$this->view->arrCores = $arrCores[0];

		$this->view->vendedores = $dbVendedores->_get($arr);
		
		if($this->getRequest()->isPost()){

			$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
			$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
			$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
			$dbGarantias = new Application_Model_DbTable_Garantias();
			
			$arrBusca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$arrBusca['aprovada'] = 1;
			$arrBusca['order_usuarios'] = true;
			$arrBusca['id_vendedor'] = $_POST['id_vendedor'];
			$arrBusca['data_inicial_concretizacao'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
			$arrBusca['data_final_concretizacao'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));
			$arrBuscaGarantia['data_inicial'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
			$arrBuscaGarantia['data_final'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));

			$arrNegociacoes = $dbNegociacoes->_get($arrBusca);
			
			foreach($arrNegociacoes as $key=>$valor){
			
				$valorDespesas = $dbDespesasVeiculos->getSomaDespesas($arrNegociacoes[$key]['id_veiculo']);
			 
				$arrNegociacoes[$key]['valor_despesas'] = $valorDespesas[0]['valor_despesas'];
			
			}
			
			$this->view->id_vendedor = $_POST['id_vendedor'];
			$this->view->data_inicial = $_POST['data_inicial'];
			$this->view->data_final = $_POST['data_final'];
			$this->view->arrNegociacoes = $arrNegociacoes;
			$this->view->gruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupo($arrBusca);
			$this->view->garantia = $dbGarantias->getGarantias($arrBuscaGarantia);
			
			///////////////////////////INICIO TABELAS MESES///////////////////////////////////////
			
			foreach($arrNegociacoes as $negociacao){
			
				$datas = explode(" ",$negociacao['data_concretizacao']);
				$data = explode("-",$datas[0]);
				
				$arrStr[$data[1]]['Lucro Bruto dos Carros-0'] += ($negociacao['valor_venda']-($negociacao['valor_despesas']+$negociacao['valor_aquisicao']));
			
				if($negociacao['id_financeira']){
				
					$arrStr[$data[1]]['F&I-0'] += ((($negociacao['retorno_financeira']*1.2)*$negociacao['valor_financiado'])/100)-$negociacao['imposto_financeira'];
				
				}
				
				$arrStr[$data[1]]['Despachantes-0'] += $negociacao['imposto'];
			
			}
			
			$arrBusca['data_inicial'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
			$arrBusca['data_final'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));
			
			$arrBusca['credito'] = true;
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arrBusca);
			
			foreach($arrGruposFinanceiros as $gruposFinanceiro){
			
				$data = explode("-",$gruposFinanceiro['data_lancamento']);
			
				$arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']] += $gruposFinanceiro['valor'];
			
			}
			
			$arrBusca['credito'] = false;
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arrBusca);
			
			foreach($arrGruposFinanceiros as $gruposFinanceiro){
			
				$data = explode("-",$gruposFinanceiro['data_lancamento']);
			
				$arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']] += $gruposFinanceiro['valor'];
			
			}
			
			
			$arrGarantias = $dbGarantias->getGarantiasIndividual($arrBuscaGarantia);
			
			foreach($arrGarantias as $garantia){
			
				$data = explode("-",$garantia['data_saida']);
				
				$arrStr[$data[1]]['Garantias-1'] += $garantia['custo'];
				
			}
			
			foreach($arrStr as $mes=>$str){
				
				$totalCreditos = 0;
				$totalDebitos = 0;
				
				$arrTabelas[$mes]['tabela'] .= "<table class='table' style='width:70%;'>
												<thead>
													<tr>
														<td colspan='2'>
															<center><label style='font-size:15px;'><b>Resultado Final - ".$this->mesExtenso($mes)."<b></label></center>
														</td>
													</tr>
													<tr>
														<th>Item</th>
														<th>Valor</th>
													</tr>
												</thead>";
			
				foreach($str as $indice=>$valor){
			
					$cd = explode("-", $indice);
					
					if($cd[1] == 0){
					
						$corCreditoDebito = "class='verde'";
						$totalCreditos += $valor;
						$credito = true;
						
					}elseif($cd[1] == 1){
					
						if($credito){
							$arrTabelas[$mes]['tabela'] .= "<tr>
														<td style='border-bottom:solid 1px; border-top:solid 1px; border-left:solid 1px;' ".$corCreditoDebito."><b>TOTAL CR&Eacute;DITOS</b></td>
														<td style='border-bottom:solid 1px; border-top:solid 1px; border-right:solid 1px;' ".$corCreditoDebito."><b>R$ ".money_format("%i",$totalCreditos)."</b></td>
													</tr>";
						
						}
					
						$totalDebitos += $valor;
						$corCreditoDebito = "class='vermelho'";
						$credito = false;
						
					}
			
					$arrTabelas[$mes]['tabela'] .= "<tr>
														<td ".$corCreditoDebito.">".$cd[0]."</td>
														<td ".$corCreditoDebito.">R$ ".money_format("%i",$valor)."</td>
													</tr>";
					
					
				}
				
				$arrTabelas[$mes]['tabela'] .= "<tr>
													<td class='vermelho' style='border-bottom:solid 1px; border-top:solid 1px; border-left:solid 1px;'><b>TOTAL D&Eacute;BITOS</b></td>
													<td class='vermelho' style='border-bottom:solid 1px; border-top:solid 1px; border-right:solid 1px;'><b>R$ ".money_format("%i",$totalDebitos)."</b></td>
												</tr>
												<tr>
													<td style='border-bottom:solid 2px; border-top:solid 2px; border-left:solid 2px;'><b>TOTAL</b></td>
													<td style='border-bottom:solid 2px; border-top:solid 2px; border-right:solid 2px;'><b>R$ ".money_format("%i",$totalCreditos-$totalDebitos)."</b></td>
												</tr>";
				
				$arrTabelas[$mes]['tabela'] .= "</table>";
			
			}
			
			$comeco = explode("/",$_POST['data_final']);
			
			$this->view->comeco = $comeco[1];
			$this->view->arrTabelas = $arrTabelas;
			
///////////////////////////FIM TABELAS MESES///////////////////////////////////////
			
			
		}else{
		
			$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
			$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
			$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
			$dbGarantias = new Application_Model_DbTable_Garantias();
			
			$arrBusca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$arrBusca['data_inicial_concretizacao'] = @date("Y")."-". @date("m")."-01";
			//$arrBusca['data_inicial_concretizacao'] = @date("Y")."-03-01";
			$arrBusca['aprovada'] = 1;
			$arrBusca['order_usuarios'] = true;
			$arrBuscaGarantia['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$arrBuscaGarantia['data_inicial'] = @date("Y")."-". @date("m")."-01";
			
			$arrNegociacoes = $dbNegociacoes->_get($arrBusca);
			
			foreach($arrNegociacoes as $key=>$valor){
			
				$valorDespesas = $dbDespesasVeiculos->getSomaDespesas($arrNegociacoes[$key]['id_veiculo']);
			 
				$arrNegociacoes[$key]['valor_despesas'] = $valorDespesas[0]['valor_despesas'];
			
			}
			
			
			$this->view->gruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupo($arrBusca);
			$this->view->arrNegociacoes = $arrNegociacoes;
			$this->view->garantia = $dbGarantias->getGarantias($arrBuscaGarantia);
			
///////////////////////////INICIO TABELAS MESES///////////////////////////////////////
			
			foreach($arrNegociacoes as $negociacao){
			
				$datas = explode(" ",$negociacao['data_concretizacao']);
				$data = explode("-",$datas[0]);
				
				$arrStr[$data[1]]['Lucro Bruto dos Carros-0'] += ($negociacao['valor_venda']-($negociacao['valor_despesas']+$negociacao['valor_aquisicao']));
			
				if($negociacao['id_financeira']){
				
					$arrStr[$data[1]]['F&I-0'] += ((($negociacao['retorno_financeira']*1.2)*$negociacao['valor_financiado'])/100)-$negociacao['imposto_financeira'];
				
				}
				
				$arrStr[$data[1]]['Despachantes-0'] += $negociacao['imposto'];
			
			}
			
			$arr['credito'] = true;
			$arr['data_inicial'] = @date("Y")."-". @date("m")."-01";
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arr);
			
			foreach($arrGruposFinanceiros as $gruposFinanceiro){
			
				$data = explode("-",$gruposFinanceiro['data_lancamento']);
			
				$arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']] += $gruposFinanceiro['valor'];
			
			}
			
			$arr['credito'] = false;
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arr);
			
			foreach($arrGruposFinanceiros as $gruposFinanceiro){
			
				$data = explode("-",$gruposFinanceiro['data_lancamento']);
			
				$arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']] += $gruposFinanceiro['valor'];
			
			}
			
			
			$arrGarantias = $dbGarantias->getGarantiasIndividual($arrBuscaGarantia);
			
			foreach($arrGarantias as $garantia){
			
				$data = explode("-",$garantia['data_saida']);
				
				$arrStr[$data[1]]['Garantias-1'] += $garantia['custo'];
				
			}
			
			if($arrStr){
			
				foreach($arrStr as $mes=>$str){
					
					$totalCreditos = 0;
					$totalDebitos = 0;
					
					$arrTabelas[$mes]['tabela'] .= "<table class='table' style='width:70%;'>
													<thead>
														<tr>
															<td colspan='2'>
																<center><label style='font-size:15px;'><b>Resultado Final - ".$this->mesExtenso($mes)."<b></label></center>
															</td>
														</tr>
														<tr>
															<th>Item</th>
															<th>Valor</th>
														</tr>
													</thead>";
				
					foreach($str as $indice=>$valor){
				
						$cd = explode("-", $indice);
						
						if($cd[1] == 0){
						
							$corCreditoDebito = "class='verde'";
							$totalCreditos += $valor;
							$credito = true;
							
						}elseif($cd[1] == 1){
						
							if($credito){
								$arrTabelas[$mes]['tabela'] .= "<tr>
															<td style='border-bottom:solid 1px; border-top:solid 1px; border-left:solid 1px;' ".$corCreditoDebito."><b>TOTAL CR&Eacute;DITOS</b></td>
															<td style='border-bottom:solid 1px; border-top:solid 1px; border-right:solid 1px;' ".$corCreditoDebito."><b>R$ ".money_format("%i",$totalCreditos)."</b></td>
														</tr>";
							
							}
						
							$totalDebitos += $valor;
							$corCreditoDebito = "class='vermelho'";
							$credito = false;
							
						}
				
						$arrTabelas[$mes]['tabela'] .= "<tr>
															<td ".$corCreditoDebito.">".$cd[0]."</td>
															<td ".$corCreditoDebito.">R$ ".money_format("%i",$valor)."</td>
														</tr>";
						
						
					}
					
					$arrTabelas[$mes]['tabela'] .= "<tr>
														<td class='vermelho' style='border-bottom:solid 1px; border-top:solid 1px; border-left:solid 1px;'><b>TOTAL D&Eacute;BITOS</b></td>
														<td class='vermelho' style='border-bottom:solid 1px; border-top:solid 1px; border-right:solid 1px;'><b>R$ ".money_format("%i",$totalDebitos)."</b></td>
													</tr>
													<tr>
														<td style='border-bottom:solid 2px; border-top:solid 2px; border-left:solid 2px;'><b>TOTAL</b></td>
														<td style='border-bottom:solid 2px; border-top:solid 2px; border-right:solid 2px;'><b>R$ ".money_format("%i",$totalCreditos-$totalDebitos)."</b></td>
													</tr>";
					
					$arrTabelas[$mes]['tabela'] .= "</table>";
				
				}
			
			}
			
			$this->view->arrTabelas = $arrTabelas;
			
///////////////////////////FIM TABELAS MESES///////////////////////////////////////
		
		}
	
	}
	
	public function addCoresRelatoriosAction(){
	
		$this->validaAcesso('relatorios');
	
		$dbCoresRelatorios = new Application_Model_DbTable_CoresRelatorios();
		
		$_POST['amarelo_lucro'] = str_replace(",",".",$_POST['amarelo_lucro']);
		$_POST['vermelho_lucro'] = str_replace(",",".",$_POST['vermelho_lucro']);
		
		if($this->getRequest()->isPost()){
		
			if($_POST['id_empresa'] == ""){
			
				$_POST['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

				if($dbCoresRelatorios->add($_POST)){
				
					$this->view->mensagem = "Cores cadastradas com sucesso!";
				
				}else{
				
					$this->view->mensagem = "Erro ao cadastrar cores!";
				
				}
			
			}else{
			
				if($dbCoresRelatorios->edt($_POST['id_empresa'],$_POST)){
				
					$this->view->mensagem = "Cores editadas com sucesso!";
				
				}else{
				
					$this->view->mensagem = "Erro ao editar cores ou não houve altera&ccedil;&atilde;o nos dados!";
				
				}
			
			}
		
		}
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrCores = $dbCoresRelatorios->_get($arr);
			
		$this->view->arrCores = $arrCores[0];
	
	}
	
	public function relatorioXlsAction(){
	
		$this->validaAcesso('relatorios');
		
		$dbVendedores = new Application_Model_DbTable_Vendedores();
		$dbCoresRelatorios = new Application_Model_DbTable_CoresRelatorios();
		$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
		$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
		$dbGruposFinanceiros = new Application_Model_DbTable_GruposFinanceiros();
		$dbGarantias = new Application_Model_DbTable_Garantias();

		$arrBusca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arrBuscaGarantia['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arrBusca['aprovada'] = 1;
		$arrBusca['order_usuarios'] = true;
		$arrBusca['id_vendedor'] = $this->_getParam('id_vendedor');
		
		if($this->_getParam('data_inicial') != "" || $this->_getParam('data_final') != ""){
		
			$arrBusca['data_inicial_concretizacao'] = $this->_getParam('data_inicial');
			$arrBusca['data_final_concretizacao'] = $this->_getParam('data_final');
			$arrBuscaGarantia['data_inicial'] = $this->_getParam('data_inicial');
			$arrBuscaGarantia['data_final'] = $this->_getParam('data_final');
			$arrBusca['data_inicial'] = $this->_getParam('data_inicial');
			$arrBusca['data_final'] = $this->_getParam('data_final');
			
		}else{
			
			$arrBusca['data_inicial_concretizacao'] = @date("Y")."-". @date("m")."-01";
			$arrBusca['data_inicial'] = @date("Y")."-". @date("m")."-01";
			$arrBuscaGarantia['data_inicial'] = @date("Y")."-". @date("m")."-01";
		
		}
		
		$arrNegociacoes = $dbNegociacoes->_get($arrBusca);
			
		foreach($arrNegociacoes as $key=>$valor){
			
			$valorDespesas = $dbDespesasVeiculos->getSomaDespesas($arrNegociacoes[$key]['id_veiculo']);
			 
			$arrNegociacoes[$key]['valor_despesas'] = $valorDespesas[0]['valor_despesas'];
			
		}
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrCores = $dbCoresRelatorios->_get($arr);
		
		$this->view->gruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupo($arrBusca);		
		$this->view->arrCores = $arrCores[0];	
		$this->view->arrNegociacoes = $arrNegociacoes;
		$this->view->nomeVendedor = $this->_getParam('nome_vendedor');
		$this->view->dataInicial = $this->_getParam('data_inicial');
		$this->view->dataFinal = $this->_getParam('data_final');
		$this->view->garantia = $dbGarantias->getGarantias($arrBuscaGarantia);
		
		///////////////////////////INICIO TABELAS MESES///////////////////////////////////////
			
			foreach($arrNegociacoes as $negociacao){
			
				$datas = explode(" ",$negociacao['data_concretizacao']);
				$data = explode("-",$datas[0]);
				
				$arrStr[$data[1]]['Lucro Bruto dos Carros-0'] += ($negociacao['valor_venda']-($negociacao['valor_despesas']+$negociacao['valor_aquisicao']));
			
				$arrStr[$data[1]]['qtd_vendas-0'] += 1;
			
				if($negociacao['id_financeira']){
				
					$arrStr[$data[1]]['F&I-0'] += (($negociacao['retorno_financeira']*1.2)*$negociacao['valor_financiado'])/100;
				
				}
				
				$arrStr[$data[1]]['Despachantes-0'] += $negociacao['imposto'];
			
			}
			
			$arrBusca['credito'] = true;
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arrBusca);
			
			foreach($arrGruposFinanceiros as $gruposFinanceiro){
			
				$data = explode("-",$gruposFinanceiro['data_lancamento']);
			
				$arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']] += $gruposFinanceiro['valor'];
			
			}
			
			$arrBusca['credito'] = false;
			$arrGruposFinanceiros = $dbGruposFinanceiros->getLancamentosPorGrupos($arrBusca);
			
			foreach($arrGruposFinanceiros as $gruposFinanceiro){
			
				$data = explode("-",$gruposFinanceiro['data_lancamento']);
			
				$arrStr[$data[1]][$gruposFinanceiro['descricao']."-".$gruposFinanceiro['credito_debito']] += $gruposFinanceiro['valor'];
			
			}
			
			
			$arrGarantias = $dbGarantias->getGarantiasIndividual($arrBuscaGarantia);
			
			foreach($arrGarantias as $garantia){
			
				$data = explode("-",$garantia['data_saida']);
				
				$arrStr[$data[1]]['Garantias-1'] += $garantia['custo'];
				
			}
			
			
			$arrDataInicial = explode("-",$this->_getParam('data_inicial'));
			$arrDataFinal = explode("-",$this->_getParam('data_final'));
			
			$mesInicial = $arrDataInicial[1];
			$mesFinal = $arrDataFinal[1];
			$ano = $arrDataInicial[0];

			if($mesFinal+1 == 13){
				
				$mesFinal = 0;
			
			}
		
			while($mesInicial != $mesFinal+1){

				$arrMes = explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial, 1, $ano)));
				
				$arrStr[$arrMes[1]]['Folha de Pagamento-1'] += $this->calculaFolhaPagamento(implode("/",array_reverse(explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial, 1, $ano))))), implode("/",array_reverse(explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial+1, 0, $ano))))));
			
				$totalFolhaPagamento += $this->calculaFolhaPagamento(implode("/",array_reverse(explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial, 1, $ano))))), implode("/",array_reverse(explode("-",@date("Y-m-d", mktime(0,0,0, $mesInicial+1, 0, $ano))))));
			
				$mesInicial++;
				
				if($mesInicial == 13){
					
					$mesInicial = 1;
					$ano++;
					
				}
			
			}
			
			
			foreach($arrStr as $mes=>$str){
				
				$totalCreditos = 0;
				$totalDebitos = 0;
				
				$arrTabelas[$mes]['tabela'] .= "<table class='table' style='width:70%;'>
												<thead>
													<tr>
														<td colspan='2'>
															<center><label style='font-size:15px;'><b>Resultado Final - ".$this->mesExtenso($mes)."<b></label></center>
														</td>
													</tr>
													<tr>
														<th class='cabeca'>Item</th>
														<th class='cabeca'>Valor</th>
													</tr>
												</thead>";
			
				foreach($str as $indice=>$valor){
			
					$cd = explode("-", $indice);
					
					if($cd[1] == 0){
					
						$corCreditoDebito = "class='verde'";
						
						if($cd[0] != "qtd_vendas-0"){
						
							$totalCreditos += $valor;
							
						}
	
						$credito = true;
						
					}elseif($cd[1] == 1){
					
						if($credito){
						
							$arrTabelas[$mes]['tabela'] .= "<tr>
														<td style='border-bottom:solid 1px; border-top:solid 1px; border-left:solid 1px;' ".$corCreditoDebito."><b>TOTAL CR&Eacute;DITOS</b></td>
														<td style='border-bottom:solid 1px; border-top:solid 1px; border-right:solid 1px;' ".$corCreditoDebito."><b>R$ ".money_format("%i",$totalCreditos)."</b></td>
													</tr>";
						
						}
					
						$totalDebitos += $valor;
						$corCreditoDebito = "class='vermelho'";
						$credito = false;
						
					}
			
					
					if($cd[0] == "Lucro Bruto dos Carros"){
			
						$vendasQtd = " (".$str['qtd_vendas-0']." veículos vendidos)";
					
					}else{
						
						$vendasQtd = "";
					
					}
					
					if($cd[0] != "qtd_vendas"){
			
						$arrTabelas[$mes]['tabela'] .= "<tr>
															<td ".$corCreditoDebito.">".$cd[0].$vendasQtd."</td>
															<td ".$corCreditoDebito.">R$ ".money_format("%i",$valor)."</td>
														</tr>";
					
					
					}
					
				}
				
				$arrTabelas[$mes]['tabela'] .= "<tr>
													<td class='vermelho' style='border-bottom:solid 1px; border-top:solid 1px; border-left:solid 1px;'><b>TOTAL D&Eacute;BITOS</b></td>
													<td class='vermelho' style='border-bottom:solid 1px; border-top:solid 1px; border-right:solid 1px;'><b>R$ ".money_format("%i",$totalDebitos)."</b></td>
												</tr>
												<tr>
													<td style='border-bottom:solid 2px; border-top:solid 2px; border-left:solid 2px;'><b>TOTAL</b></td>
													<td style='border-bottom:solid 2px; border-top:solid 2px; border-right:solid 2px;'><b>R$ ".money_format("%i",$totalCreditos-$totalDebitos)."</b></td>
												</tr>";
				
				$arrTabelas[$mes]['tabela'] .= "</table>";
			
			}
			
			
			$inicial = explode("-",$this->_getParam('data_inicial'));
			$finals = explode("-",$this->_getParam('data_final'));
			
			$this->view->inicial = $inicial[1];
			$this->view->finals = $finals[1];
			$this->view->arrTabelas = $arrTabelas;
			
///////////////////////////FIM TABELAS MESES///////////////////////////////////////

		$this->view->rsFolhaPagamento = $totalFolhaPagamento;
	
	}
	
	public function simuladorResultadosAction(){
	
		$this->validaAcesso('simulador');
	
	}
	
	public function gerarXlsAction(){
	
		//$this->validaAcesso('relatorios');

		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
	
		$this->view->relatorio = $_POST['relatorio'];
		$this->view->dataInicial = $_POST['data_inicial'];
		$this->view->dataFinal = $_POST['data_final'];
		$this->view->tipoRelatorio =  $_POST['tipo_relatorio'];
	
	}
	
	public function permissoesAction(){
	
		$this->validaAcesso('relatorios');
		
		$dbPermissoes = new Application_Model_DbTable_Permissoes();
		
		$arrFiltro['id_usuario'] = $_SESSION['sessionUser']['id']; 	
		$arrFiltro['id_perfil'] = $_SESSION['sessionUser']['id_perfil'];
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

		$arrPermissoes = $dbPermissoes->_getUsuario($arrFiltro);
		
		foreach($arrPermissoes as $key=>$permissoes){
		
			if(substr($permissoes['nome'], 0, 3) == "adm"){
				
				unset($arrPermissoes[$key]);
				break;

			}
		
		}
		
		$this->view->permissoes = $arrPermissoes; 
		
	}

	public function despachantesAction(){
	
		$this->validaAcesso('relatorios');
		
		$dbFinanceiras = new Application_Model_DbTable_Financeiras();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arrFiltro['tipo'] = 1;
		
		$this->view->despachantes = $dbFinanceiras->_get($arrFiltro);

			$relatorios = "<table class='table'>
						   <tr>
							<th class='cabeca'>Despachante</th>
							<th class='cabeca'>Cliente</th>
							<th class='cabeca'>Data</th>
							<th class='cabeca'>Ve&iacute;culo</th>
							<th class='cabeca'>Placa</th>
							<th class='cabeca'>Forma de Pagamento</th>
							<th class='cabeca'>Valor</th>
							<th class='cabeca'>Comiss&atilde;o</th>
						  </tr>";
			$count = 1;
			$subtotal="";
		
		if($this->getRequest()->isPost()){

			$somaSubtotal = 0;
			$somaComissao = 0;
			$relatorio = "";
			$somaDespachantes = 0;
			$somaComissoes = 0;
		
			if($_POST['data_inicial']){
			
				$this->view->dataInicial = $_POST['data_inicial'];
				
				$arrFiltro['data_inicial'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
			
			}
			
			if($_POST['data_final']){
			
				$this->view->dataFinal = $_POST['data_final'];
				
				$arrFiltro['data_final'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));
			
			}
			
			if(isset($_POST['id_despachante']) && $_POST['id_despachante'] != "0"){
				$arrFiltro['id'] = $_POST['id_despachante'];
			}
		
			$arrDespachantes = $dbFinanceiras->getFinanceirasDespachantes($arrFiltro);
		
			foreach($arrDespachantes as $key=>$despachante){
		
				$dataTemp = explode(" ",$despachante['data']);
				
				if(isset($dataTemp[0])){
					$despachante['data'] = $dataTemp[0];
				}
			
				if(!isset($arrDespachantes[$key+1]['id']) || $arrDespachantes[$key+1]['id'] != $despachante['id']){
		
					$rowspan = "<tr>
									<td class='tds' rowspan='".$count."'>".$despachante['nome']."</td>
									<td class='tds'>".$despachante['nome_cliente']."</td>
									<td class='tds'>".implode("/",array_reverse(explode("-",$despachante['data'])))."</td>
									<td class='tds'>".$despachante['modelo']."</td>
									<td class='tds'>".$despachante['placa']."</td>
									<td class='tds'>".$despachante['forma_pagamento_despachante']."</td>
									<td class='tds'>R$ ".money_format("%i",$despachante['valor_despachante'])."</td>
									<td class='tds'>R$ ".money_format("%i",$despachante['imposto'])."</td>
								</tr>";
						
					$somaSubtotal += $despachante['valor_despachante'];
					$somaComissao += $despachante['imposto'];
						
					if(!isset($_POST['id_fornecedor'])){
						
						$subtotal = "<tr><th colspan='6' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$somaSubtotal)."</th><th class='cabeca'>R$ ".money_format("%i",$somaComissao)."</th></tr>";
						
					}
						
					$relatorios .= $rowspan.$relatorio.$subtotal;
						
					$count = 1;
					$relatorio ="";
					$somaSubtotal = 0;
					$somaComissao = 0;
			
				}else{
				
					$count++;
					$relatorio .= "<tr>
									<td class='tds'>".$despachante['nome_cliente']."</td>
									<td class='tds'>".implode("/",array_reverse(explode("-",$despachante['data'])))."</td>
									<td class='tds'>".$despachante['modelo']."</td>
									<td class='tds'>".$despachante['placa']."</td>
									<td class='tds'>".$despachante['forma_pagamento_despachante']."</td>
									<td class='tds'>R$ ".money_format("%i",$despachante['valor_despachante'])."</td>
									<td class='tds'>R$ ".money_format("%i",$despachante['imposto'])."</td>
								</tr>";
						
						
					$somaSubtotal += $despachante['valor_despachante'];
					$somaComissao += $despachante['imposto'];
					
				}
		
				$somaDespachantes += $despachante['valor_despachante'];
				$somaComissoes += $despachante['imposto'];
			
			}
		
		}else{

			$somaSubtotal = 0;
			$somaComissao = 0;
			$somaDespachantes = 0;
			$somaComissoes = 0;
			$relatorio = "";
			
			$arrFiltro['data_inicial'] = @date("Y")."-".@date("m") ."-01";
			
			$arrDespachantes = $dbFinanceiras->getFinanceirasDespachantes($arrFiltro);
		
			foreach($arrDespachantes as $key=>$despachante){
		
				$dataTemp = explode(" ",$despachante['data']);
				
				$despachante['data'] = $dataTemp[0];
			
				if(!isset($arrDespachantes[$key+1]['id']) || $arrDespachantes[$key+1]['id'] != $despachante['id']){
		
					$rowspan = "<tr>
									<td class='tds' rowspan='".$count."'>".$despachante['nome']."</td>
									<td class='tds'>".$despachante['nome_cliente']."</td>
									<td class='tds'>".implode("/",array_reverse(explode("-",$despachante['data'])))."</td>
									<td class='tds'>".$despachante['modelo']."</td>
									<td class='tds'>".$despachante['placa']."</td>
									<td class='tds'>".$despachante['forma_pagamento_despachante']."</td>
									<td class='tds'>R$ ".money_format("%i",$despachante['valor_despachante'])."</td>
									<td class='tds'>R$ ".money_format("%i",$despachante['imposto'])."</td>
								</tr>";
						
					$somaSubtotal += $despachante['valor_despachante'];
					$somaComissao += $despachante['imposto'];
						
					if(!isset($_POST['id_fornecedor'])){
						
						$subtotal = "<tr><th colspan='6' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$somaSubtotal)."</th><th class='cabeca'>R$ ".money_format("%i",$somaComissao)."</th></tr>";
						
					}
						
					$relatorios .= $rowspan.$relatorio.$subtotal;
						
					$count = 1;
					$relatorio ="";
					$somaSubtotal = 0;
					$somaComissao = 0;
		
				}else{
				
					$count++;
					$relatorio .= "<tr>
									<td class='tds'>".$despachante['nome_cliente']."</td>
									<td class='tds'>".implode("/",array_reverse(explode("-",$despachante['data'])))."</td>
									<td class='tds'>".$despachante['modelo']."</td>
									<td class='tds'>".$despachante['placa']."</td>
									<td class='tds'>".$despachante['forma_pagamento_despachante']."</td>
									<td class='tds'>R$ ".money_format("%i",$despachante['valor_despachante'])."</td>
									<td class='tds'>R$ ".money_format("%i",$despachante['imposto'])."</td>
								</tr>";
						
						
					$somaSubtotal += $despachante['valor_despachante'];
					$somaComissao += $despachante['imposto'];
					
				}
		
				$somaDespachantes += $despachante['valor_despachante'];
				$somaComissoes += $despachante['imposto'];
				
			}
		
		}
		
		if($arrDespachantes){
			
			$relatorios .= "<tr><td colspan='6' style='border:solid 2px; text-align:right;'>M&Eacute;DIA</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaDespachantes/count($arrDespachantes))."</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaComissoes/count($arrDespachantes))."</td></tr>";
			$relatorios .= "<tr><td colspan='6' style='border:solid 2px; text-align:right;'>TOTAL</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaDespachantes)."</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaComissoes)."</td></tr>";
			
		}
		
		$relatorios = "<div style='margin-left:10px; margin-bottom:10px;'><b>Quantidade de Servi&ccedil;os: ".count($arrDespachantes)."</b></div>".$relatorios;
		
		if(isset($_POST['id_despachante'])){
			$this->view->idDespachante = $_POST['id_despachante']; 
		}
		$this->view->relatorio = $relatorios."</table>"; 
		
	}

	public function fornecedoresAction(){
	
		$this->validaAcesso('relatorios');
	
		$dbFornecedores = new Application_Model_DbTable_Fornecedores();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		//$this->view->fornecedores = $dbFornecedores->_get($arrFiltro);
		$this->view->fornecedores = $dbFornecedores->_getDois($arrFiltro);
		$this->view->ramo_atividade = $dbFornecedores->getRamoAtividade($arrFiltro);

		$relatorios = "";
		$relatorio = "";
		$somaDespesasFornecedor = 0;
		$somaDespesas = 0;
		
		if($this->getRequest()->isPost()){
		
			$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
			
			$_POST['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			
			if($_POST['data_inicial']){
			
				$this->view->dataInicial = $_POST['data_inicial'];
				
				$_POST['data_inicial'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
			
			}
			
			if($_POST['data_final']){
			
				$this->view->dataFinal = $_POST['data_final'];
				
				$_POST['data_final'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));
			
			}
			
			$arrDespesas = $dbDespesasVeiculos->_get($_POST);
			
			$relatorios .= "<div style='margin-left:10px; margin-bottom:10px;'><b>Quantidade de Despesas: ".count($arrDespesas)."</b></div>";

			$relatorios .= "<table class='table'>
						   <tr>
							<th class='cabeca' width='180px'>Fornecedor</th>
							<th class='cabeca'>Despesa</th>
							<th class='cabeca'>Data</th>
							<th class='cabeca' width='130px'>Ve&iacute;culo</th>
							<th class='cabeca' width='60px'>Placa</th>
							<th class='cabeca' width='70px'>Valor</th>
						  </tr>";
			$count = 1;
			$subtotal="";
			$countColor = true;

			foreach($arrDespesas as $key=>$despesa){
				if(!isset($arrDespesas[$key+1]['id_fornecedor']) || ($arrDespesas[$key+1]['id_fornecedor'] != $despesa['id_fornecedor'])){
					
					$rowspan = "<tr>
								<td class='tds' rowspan='".$count."'>".$despesa['razao_social_fornecedor']."</td>
								<td class='tds'>".$despesa['despesa']."</td>
								<td class='tds'>".implode("/",array_reverse(explode("-",$despesa['data'])))."</td>
								<td class='tds'>".substr($despesa['modelo'],0 ,18)."</td>
								<td class='tds'>".$despesa['placa']."</td>
								<td class='tds'>R$ ".money_format("%i",$despesa['valor'])."</td>
							  </tr>";
					
					$somaDespesasFornecedor += $despesa['valor'];
					
					if(!isset($_POST['id_fornecedor']) || $_POST['id_fornecedor'] == 0){
					
						$subtotal = "<tr><th colspan='5' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$somaDespesasFornecedor)."</th></tr>";
						
						$countColor = true;
						
					}
					
					$relatorios .= $rowspan.$relatorio.$subtotal;
					
					$count = 1;
					$relatorio ="";
					$somaDespesasFornecedor = 0;
					
					
				}else{
					$count++;
					
					if($countColor){
						
						$color = "style='background-color:#DDDDDD;'";
						$countColor = false;
					
					}else{
						
						$color = "style='background-color:#FFF;'";
						$countColor = true;
					
					}

					// echo "<br>";
					// var_export($despesa['despesa']);
					// echo "<br>";
					
					$relatorio .= "<tr ".$color.">
								<td class='tds'>".str_replace('"', '', $despesa['despesa'])."</td>
								<td class='tds'>".implode("/",array_reverse(explode("-",$despesa['data'])))."</td>
								<td class='tds'>".substr($despesa['modelo'],0 ,18)."</td>
								<td class='tds'>".$despesa['placa']."</td>
								<td class='tds'>R$ ".money_format("%i",$despesa['valor'])."</td>
							  </tr>";
						
					$somaDespesasFornecedor += $despesa['valor'];
				}
	
				$somaDespesas += $despesa['valor'];
			
			}
			
			if($arrDespesas){
			
				$relatorios .= "<tr><td colspan='5' style='border:solid 2px; text-align:right;'>M&Eacute;DIA</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaDespesas/count($arrDespesas))."</td></tr>";
				$relatorios .= "<tr><td colspan='5' style='border:solid 2px; text-align:right;'>TOTAL</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaDespesas)."</td></tr>";
			
			}
			
			$relatorios .= "</table>";
			

			//var_export($relatorios);
			
			$this->view->relatorio = $relatorios;
			$this->view->idFornecedor = $_POST['id_fornecedor'];
			$this->view->ramoAtividade = $_POST['ramo_atividade'];
			
		}else{
		
			$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
			
			$_POST['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$_POST['data_inicial'] = @date("Y")."-".@date("m") ."-01";
			
			$arrDespesas = $dbDespesasVeiculos->_get($_POST);
			
			$relatorios .= "<div style='margin-left:10px; margin-bottom:10px;'><b>Quantidade de Despesas: ".count($arrDespesas)."</b></div>";

			$relatorios .= "<table class='table'>
						   <tr>
							<th class='cabeca'>Fornecedor</th>
							<th class='cabeca'>Despesa</th>
							<th class='cabeca'>Data</th>
							<th class='cabeca' width='130px'>Ve&iacute;culo</th>
							<th class='cabeca' width='60px'>Placa</th>
							<th class='cabeca' width='70px'>Valor</th>
						  </tr>";
			$count = 1;
			$subtotal="";
			$countColor = true;
			
			
			foreach($arrDespesas as $key=>$despesa){
				
				if(!isset($arrDespesas[$key+1]['id_fornecedor']) || ($arrDespesas[$key+1]['id_fornecedor'] != $despesa['id_fornecedor'])){
	
					$rowspan = "<tr>
								<td class='tds' rowspan='".$count."'>".$despesa['razao_social_fornecedor']."</td>
								<td class='tds'>".$despesa['despesa']."</td>
								<td class='tds'>".implode("/",array_reverse(explode("-",$despesa['data'])))."</td>
								<td class='tds'>".substr($despesa['modelo'],0 ,18)."</td>
								<td class='tds'>".$despesa['placa']."</td>
								<td class='tds'>R$ ".money_format("%i",$despesa['valor'])."</td>
							  </tr>";
					
					$somaDespesasFornecedor += $despesa['valor'];
					
					if(!isset($_POST['id_fornecedor']) || $_POST['id_fornecedor'] == 0){
					
						$subtotal = "<tr><th colspan='5' class='cabeca' style='text-align:right;'>SUBTOTAL</th><th class='cabeca'>R$ ".money_format("%i",$somaDespesasFornecedor)."</th></tr>";
						$countColor = true;
						
					}
					
					$relatorios .= $rowspan.$relatorio.$subtotal;
					
					$count = 1;
					$relatorio ="";
					$somaDespesasFornecedor = 0;
					
				}else{
				$count++;
				
					if($countColor){
						
						$color = "style='background-color:#DDDDDD;'";
						$countColor = false;
					
					}else{
						
						$color = "style='background-color:#FFF;'";
						$countColor = true;
					
					}
				
				$relatorio .= "<tr ".$color.">
								<td class='tds'>".$despesa['despesa']."</td>
								<td class='tds'>".implode("/",array_reverse(explode("-",$despesa['data'])))."</td>
								<td class='tds'>".substr($despesa['modelo'],0 ,18)."</td>
								<td class='tds'>".$despesa['placa']."</td>
								<td class='tds'>R$ ".money_format("%i",$despesa['valor'])."</td>
							  </tr>";
							  
				$somaDespesasFornecedor += $despesa['valor'];
							  
				}
			
				$somaDespesas += $despesa['valor'];
			
			}
			
			if($arrDespesas){
			
				$relatorios .= "<tr><td colspan='5' style='border:solid 2px; text-align:right;'>M&Eacute;DIA</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaDespesas/count($arrDespesas))."</td></tr>";
				$relatorios .= "<tr><td colspan='5' style='border:solid 2px; text-align:right;'>TOTAL</td><td style='border:solid 2px;'>R$ ".money_format("%i",$somaDespesas)."</td></tr>";
			
			}
			
			$relatorios .= "</table>";
			
			//var_export($relatorios);
			
			$this->view->relatorio = $relatorios;
			if(isset($_POST['id_fornecedor'])){
				$this->view->idFornecedor = $_POST['id_fornecedor'];
			}
			if(isset($_POST['ramo_atividade'])){
				$this->view->ramoAtividade = $_POST['ramo_atividade'];
			}
		}
	
	}
	
	
	public function veiculosAction(){
	
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$dbDespesasVeiculos = new Application_Model_DbTable_DespesasVeiculos();
		
		$arrBusca['id_empresa'] = $_POST['id_empresa'];
		
		if($_POST['data_inicial'] == "" && $_POST['data_final'] == ""){
		
			$arrBusca['data_inicial_abertura'] = @date("Y")."-".@date("m")."-01";
		
		}else{
		
			$arrBusca['data_inicial_abertura'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
			$arrBusca['data_final_abertura'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));
		
		}
		
		$this->view->arrEmpresas = $dbEmpresas->getEmpresas();
		
		$strRelatorios = "<table class='table'>
							<tr>
								<th class='cabeca'>Concession&aacute;ria</th>
								<th class='cabeca'>Ve&iacute;culo</th>
								<th class='cabeca'>Origem</th>
								<th class='cabeca'>Data Venda</th>
								<th class='cabeca'>Valor Venda</th>
								<th class='cabeca'>Valor Compra</th>
								<th class='cabeca'>Valor Despesa</th>
								<th class='cabeca' colspan='2'>Lucro</th>
							</tr>";
						  
		
		$count = 0;
		$qtdEmpresas = 0;
		
		$arrVeiculos = $dbVeiculos->getVeiculosVendidos($arrBusca);
		
		foreach($arrVeiculos as $key=>$veiculos){
			
			$count++;
	
			$valorDespesas = $dbDespesasVeiculos->getSomaDespesas($veiculos['id']);
			
			$data = explode(" ",$veiculos['data_concretizacao']);
			$veiculos['data_concretizacao'] = $data[0];
			
			$somaVenda += $veiculos['valor_venda'];
			$somaCompra += $veiculos['valor_aquisicao'];
			$somaDespesa += $valorDespesas[0]['valor_despesas'];
			$somaLucro += $veiculos['valor_venda']-($valorDespesas[0]['valor_despesas']+$veiculos['valor_aquisicao']);
			$somaLucroPorcento += (($veiculos['valor_venda']-($valorDespesas[0]['valor_despesas']+$veiculos['valor_aquisicao']))/($valorDespesas[0]['valor_despesas']+$veiculos['valor_aquisicao'])*100);
			
			$subVenda += $veiculos['valor_venda'];
			$subCompra += $veiculos['valor_aquisicao'];
			$subDespesa += $valorDespesas[0]['valor_despesas'];
			$subLucro += $veiculos['valor_venda']-($valorDespesas[0]['valor_despesas']+$veiculos['valor_aquisicao']);
			
			if($arrVeiculos[$key+1]['idEmpresa'] != $veiculos['idEmpresa']){

				$qtdEmpresas++;
				$unidade = "";

				if($veiculos['idEmpresa'] == 3){
					$unidade = "1";
				}elseif($veiculos['idEmpresa'] == 239){
					$unidade = "2";
				}
		
				$rowspan = "<tr>
								<td class='tds' rowspan='".($count)."'>".$veiculos['nome_fantasia']." ".$unidade."</td>
								<td class='tds'>".$veiculos['marca']." - ".$veiculos['modelo']."</td>
								<td class='tds'>".$veiculos['origem']."</td>
								<td class='tds'>".implode("/",array_reverse(explode("-",$veiculos['data_concretizacao'])))."</td>
								<td class='tds'>R$ ".money_format("%i",$veiculos['valor_venda'])."</td>
								<td class='tds'>R$ ".money_format("%i",$veiculos['valor_aquisicao'])."</td>
								<td class='tds'>R$ ".money_format("%i",$valorDespesas[0]['valor_despesas'])."</td>
								<td class='tds'>R$ ".money_format("%i",($veiculos['valor_venda']-($valorDespesas[0]['valor_despesas']+$veiculos['valor_aquisicao'])))."</td>
								<td class='tds'>".round((($veiculos['valor_venda']-($valorDespesas[0]['valor_despesas']+$veiculos['valor_aquisicao']))/($valorDespesas[0]['valor_despesas']+$veiculos['valor_aquisicao'])*100),2)."%</td>
							</tr>";
				
				if($_POST['id_empresa'] == 0){

					$strSubTotal = "<tr>
										<td style='background-color:#EEEEEE;' class='tds'>SUBTOTAL</td>
										<td style='background-color:#EEEEEE;' class='tds'>".$count." vendas</td>
										<td style='background-color:#EEEEEE;' class='tds'></td>
										<td style='background-color:#EEEEEE;' class='tds'></td>
										<td style='background-color:#EEEEEE;' class='tds'>R$ ".money_format("%i",$subVenda)."</td>
										<td style='background-color:#EEEEEE;' class='tds'>R$ ".money_format("%i",$subCompra)."</td>
										<td style='background-color:#EEEEEE;' class='tds'>R$ ".money_format("%i",$subDespesa)."</td>
										<td style='background-color:#EEEEEE;' class='tds'>R$ ".money_format("%i",$subLucro)."</td>
										<td style='background-color:#EEEEEE;' class='tds'></td>
									</tr>";
				
				}else{
				
					$strSubTotal = "";
				
				}
				
				$strRelatorios .= $rowspan.$relatorios.$strSubTotal;
				
				
				$relatorios = "";
				$count = 0;
				$subVenda = 0;
				$subCompra = 0;
				$subDespesa = 0;
				$subLucro = 0;
		
			}else{
			
				$relatorios .= "<tr>
									<td class='tds'>".$veiculos['marca']." - ".$veiculos['modelo']."</td>
									<td class='tds'>".$veiculos['origem']."</td>
									<td class='tds'>".implode("/",array_reverse(explode("-",$veiculos['data_concretizacao'])))."</td>
									<td class='tds'>R$ ".money_format("%i",$veiculos['valor_venda'])."</td>
									<td class='tds'>R$ ".money_format("%i",$veiculos['valor_aquisicao'])."</td>
									<td class='tds'>R$ ".money_format("%i",$valorDespesas[0]['valor_despesas'])."</td>
									<td class='tds'>R$ ".money_format("%i",($veiculos['valor_venda']-($valorDespesas[0]['valor_despesas']+$veiculos['valor_aquisicao'])))."</td>
									<td class='tds'>".round((($veiculos['valor_venda']-($valorDespesas[0]['valor_despesas']+$veiculos['valor_aquisicao']))/($valorDespesas[0]['valor_despesas']+$veiculos['valor_aquisicao'])*100),2)."%</td>
								</tr>";
			
			}
			
		}
		
		if($arrVeiculos){
		
			$strMedia = "<tr>
							<td style='border:solid 2px;'><b>M&Eacute;DIA</b></td>
							<td style='border:solid 2px;'><b>".count($arrVeiculos)/$qtdEmpresas." vendas</b></td>
							<td style='border:solid 2px;'></td>
							<td style='border:solid 2px;'></td>
							<td style='border:solid 2px;' class='tds'>R$ ".money_format("%i",$somaVenda/count($arrVeiculos))."</td>
							<td style='border:solid 2px;' class='tds'>R$ ".money_format("%i",$somaCompra/count($arrVeiculos))."</td>
							<td style='border:solid 2px;' class='tds'>R$ ".money_format("%i",$somaDespesa/count($arrVeiculos))."</td>
							<td style='border:solid 2px;' class='tds'>R$ ".money_format("%i",$somaLucro/count($arrVeiculos))."</td>
							<td style='border:solid 2px;' class='tds'>".round($somaLucroPorcento/count($arrVeiculos),2)."%</td>
						</tr>";
		
			$strTotal = "<tr>
							<td style='border:solid 2px;'><b>TOTAL</b></td>
							<td style='border:solid 2px;'><b>".count($arrVeiculos)." vendas</b></td>
							<td style='border:solid 2px;'></td>
							<td style='border:solid 2px;'></td>
							<td style='border:solid 2px;' class='tds'>R$ ".money_format("%i",$somaVenda)."</td>
							<td style='border:solid 2px;' class='tds'>R$ ".money_format("%i",$somaCompra)."</td>
							<td style='border:solid 2px;' class='tds'>R$ ".money_format("%i",$somaDespesa)."</td>
							<td style='border:solid 2px;' class='tds'>R$ ".money_format("%i",$somaLucro)."</td>
							<td style='border:solid 2px;'></td>
						</tr>";
				
		}
			
		$strRelatorios .= $strMedia;
		$strRelatorios .= $strTotal;
		$strRelatorios .= "</table>";


		$this->view->relatorio = $strRelatorios;
		$this->view->idEmpresa = $_POST['id_empresa'];
		$this->view->dataInicial = $_POST['data_inicial'];
		$this->view->dataFinal = $_POST['data_final'];
		
		//var_export($arrVeiculos);
	
	}
	
	public function financiamentosRootAction(){
	
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
		
		$arrBusca['id_empresa'] = $_POST['id_empresa'];
		
		if($_POST['data_inicial'] == "" && $_POST['data_final'] == ""){
		
			$arrBusca['data_inicial_concretizacao'] = @date("Y")."-".@date("m")."-01";
		
		}else{
		
			$arrBusca['data_inicial_concretizacao'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
			$arrBusca['data_final_concretizacao'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));
		
		}
		
		$this->view->arrEmpresas = $dbEmpresas->getEmpresas();
		
		$strRelatorios = "<table class='table'>
							<tr>
								<th class='cabeca'>Concession&aacute;ria</th>
								<th class='cabeca'>Financeira</th>
								<th class='cabeca'>Ve&iacute;culo</th>
								<th class='cabeca'>Data Venda</th>
								<th class='cabeca'>Valor Venda</th>
								<th class='cabeca'>Valor Financiado</th>
								<th class='cabeca'>N&uacute;mero Presta&ccedil;&otilde;es</th>
								<th class='cabeca'>F&I</th>
							</tr>";
							
		
		$arrFinanciamentos = $dbNegociacoes->getFinanciamentos($arrBusca);
		
		$count = 0;
		$qtdEmpresas = 0;
		
		foreach($arrFinanciamentos as $key=>$financiamento){
		
			$count++;
		
			$data = explode(" ",$financiamento['data_concretizacao']);
			$financiamento['data_concretizacao'] = $data[0];
			
			$somaVenda += $financiamento['valor_venda'];
			$somaFinanciado += $financiamento['valor_financiado'];
			$somaFI += ((($financiamento['retorno_financeira']*1.2)*$financiamento['valor_financiado'])/100);
			
			$subVenda += $financiamento['valor_venda'];
			$subFinanciado += $financiamento['valor_financiado'];
			$subFI += ((($financiamento['retorno_financeira']*1.2)*$financiamento['valor_financiado'])/100);
		
			if($arrFinanciamentos[$key+1]['idEmpresa'] != $financiamento['idEmpresa']){
			
				$qtdEmpresas++;
		
				$rowspan = "<tr>
								<td class='tds' rowspan='".$count."'>".$financiamento['nome_fantasia']."</td>
								<td class='tds'>".$financiamento['nome']."</td>
								<td class='tds'>".$financiamento['marca']." - ".$financiamento['modelo']."</td>
								<td class='tds'>".implode("/",array_reverse(explode("-",$financiamento['data_concretizacao'])))."</td>
								<td class='tds'>R$ ".money_format("%i",$financiamento['valor_venda'])."</td>
								<td class='tds'>R$ ".money_format("%i",$financiamento['valor_financiado'])."</td>
								<td class='tds'>".$financiamento['numero_prestacoes']."</td>
								<td class='tds'>R$ ".money_format("%i",((($financiamento['retorno_financeira']*1.2)*$financiamento['valor_financiado'])/100))."</td>
								
							</tr>";
				
				if($_POST['id_empresa'] == 0){

					$strSubTotal = "<tr>
										<td colspan='2' style='background-color:#EEEEEE;' class='tds'>SUBTOTAL</td>
										<td style='background-color:#EEEEEE;' class='tds'>".$count." financiamentos</td>
										<td style='background-color:#EEEEEE;' class='tds'></td>
										<td style='background-color:#EEEEEE;' class='tds'>R$ ".money_format("%i",$subVenda)."</td>
										<td style='background-color:#EEEEEE;' class='tds'>R$ ".money_format("%i",$subFinanciado)."</td>
										<td style='background-color:#EEEEEE;' class='tds'></td>
										<td style='background-color:#EEEEEE;' class='tds'>R$ ".money_format("%i",$subFI)."</td>
									</tr>";
				
				}else{
				
					$strSubTotal = "";
				
				}
				
				$strRelatorios .= $rowspan.$relatorios.$strSubTotal;
				
				
				$relatorios = "";
				$count = 0;
				$subVenda = 0;
				$subFinanciado = 0;
				$subFI = 0;
		
			}else{
			
				$relatorios .= "<tr>
									<td class='tds'>".$financiamento['nome']."</td>
									<td class='tds'>".$financiamento['marca']." - ".$financiamento['modelo']."</td>
									<td class='tds'>".implode("/",array_reverse(explode("-",$financiamento['data_concretizacao'])))."</td>
									<td class='tds'>R$ ".money_format("%i",$financiamento['valor_venda'])."</td>
									<td class='tds'>R$ ".money_format("%i",$financiamento['valor_financiado'])."</td>
									<td class='tds'>".$financiamento['numero_prestacoes']."</td>
									<td class='tds'>R$ ".money_format("%i",((($financiamento['retorno_financeira']*1.2)*$financiamento['valor_financiado'])/100))."</td>
								</tr>";
			
			}
			
		}
		
		
		
		if($arrFinanciamentos){
		
			$strMedia = "<tr>
							<td colspan='2' style='border:solid 2px;'><b>M&Eacute;DIA</b></td>
							<td style='border:solid 2px;'><b>".count($arrFinanciamentos)/$qtdEmpresas." vendas</b></td>
							<td style='border:solid 2px;'></td>
							<td style='border:solid 2px;' class='tds'>R$ ".money_format("%i",$somaVenda/count($arrFinanciamentos))."</td>
							<td style='border:solid 2px;' class='tds'>R$ ".money_format("%i",$somaFinanciado/count($arrFinanciamentos))."</td>
							<td style='border:solid 2px;'></td>
							<td style='border:solid 2px;' class='tds'>R$ ".money_format("%i",$somaFI/count($arrFinanciamentos))."</td>
						</tr>";
		
			$strTotal = "<tr>
							<td colspan='2' style='border:solid 2px;'><b>TOTAL</b></td>
							<td style='border:solid 2px;'><b>".count($arrFinanciamentos)." vendas</b></td>
							<td style='border:solid 2px;'></td>
							<td style='border:solid 2px;' class='tds'>R$ ".money_format("%i",$somaVenda)."</td>
							<td style='border:solid 2px;' class='tds'>R$ ".money_format("%i",$somaFinanciado)."</td>
							<td style='border:solid 2px;'></td>
							<td style='border:solid 2px;' class='tds'>R$ ".money_format("%i",$somaFI)."</td>
						</tr>";
				
		}
		
		$strRelatorios .= $strMedia;
		$strRelatorios .= $strTotal;
		$strRelatorios .= "</table>";

		
		$this->view->relatorio = $strRelatorios;
		$this->view->idEmpresa = $_POST['id_empresa'];
		$this->view->dataInicial = $_POST['data_inicial'];
		$this->view->dataFinal = $_POST['data_final'];
		
	}
	
	
	private function calculaFolhaPagamento($dataInicial, $dataFinal){
	
		$dbUsuarios = new Application_Model_DbTable_Usuarios();
		$dbNegociacoes = new Application_Model_DbTable_Negociacoes();
			
			$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			
			$arrFiltroVendas['data_inicial_concretizacao'] = implode("-",array_reverse(explode("/",$dataInicial)));
			$arrFiltroVendas['data_final_concretizacao'] = implode("-",array_reverse(explode("/",$dataFinal)));

			$arrUsuarios = $dbUsuarios->_getUsuariosNegociacoes($arr);
			
			foreach($arrUsuarios as $key=>$usuarios){

				if($usuarios['id_perfil'] == 3){
				
					$arrFiltroVendas['id_vendedor'] = $usuarios['id'];
				
				}elseif($usuarios['id_perfil'] == 9){
				
					$arrFiltroVendas['id_supervisor'] = $usuarios['id'];
				
				}elseif($usuarios['id_perfil'] == 4){
				
					$arrFiltroVendas['id_gerente'] = $usuarios['id'];
				
				}else{
					
					$arrFiltroVendas['id_vendedor'] = $usuarios['id'];
					
				}
				
				$arrVendas = $dbNegociacoes->getVendasPorUsuario($arrFiltroVendas);
				
				unset($arrFiltroVendas['id_vendedor']);
				unset($arrFiltroVendas['id_supervisor']);
				unset($arrFiltroVendas['id_gerente']);
				
				$totalFixo += $usuarios['valor_fixo_mensal'];
				
				if($arrVendas){
					
					$subtotalVenda = 0;
					$subtotalComissao = 0;
					$subtotalRetorno = 0;
					$subtotalComissaoRetorno = 0;
					
					$rowspan = count($arrVendas)+1;
					
					if(count($arrUsuarios) == 1){
						
						$cont = -1;
					
					}
					
					foreach($arrVendas as $chave=>$vendas){
						
						if($vendas['descricao_site']){
							
							$vendas['modelo'] = $vendas['descricao_site'];
						
						}
						
						$arrModelo = explode(" ",$vendas['modelo']);
						
						$vendas['modelo'] = $arrModelo[0]." ".$arrModelo[1];
						
						if($usuarios['id_perfil'] == 3){
				
							$comissaoVenda = $vendas['comissao_vendedor'];
						
						}elseif($usuarios['id_perfil'] == 9){
						
							$comissaoVenda = $vendas['comissao_supervisor'];
						
						}elseif($usuarios['id_perfil'] == 4){
						
							$comissaoVenda = $vendas['comissao_gerente'];
						
						}else{
							
							$comissaoVenda = 0;
							
						}
						
						if($usuarios['valor_fixo']){
							
							$comissaoVenda = $usuarios['valor_fixo'];
						
						}
						
						if($vendas['valor_financiado'] != 0){
							
							$comissaoRetorno = ((((($vendas['valor_financiado']*1.2)*$vendas['retorno_financeira'])/100) - $vendas['imposto_financeira'])*$usuarios['percentual_retorno_financeiro'])/100;

						}

						$totalComissao += $comissaoVenda;
						$totalComissaoRetorno += $comissaoRetorno;
						
						
						
					}
				
				}
				
			}
			
		return $totalComissaoRetorno+$totalComissao+$totalFixo;
	
	}
	
	
	private function mesExtenso($mes){
		
		switch($mes){
				
			case 1:
			return "Janeiro";
			break;
					
			case 2:
			return "Fevereiro";
			break;
					
			case 3:
			return "Mar&ccedil;o";
			break;
					
			case 4:
			return "Abril";
			break;
					
			case 5:
			return "Maio";
			break;
					
			case 6:
			return "Junho";
			break;
					
			case 7:
			return "Julho";
			break;
					
			case 8:
			return "Agosto";
			break;
					
			case 9:
			return "Setembro";
			break;
					
			case 10:
			return "Outubro";
			break;
					
			case 11:
			return "Novembro";
			break;
					
			case 12:
			return "Dezembro";
			break;
				
		}
			
	}

}

?>
