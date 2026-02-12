<?php

header("Content-Type: text/html; charset=UTF-8",true);

class EstatisticasController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Estatísticas";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}

	public function projecaoAction(){
		
		if($this->getRequest()->isPost()){
			if($_POST['data_final']){
				$this->view->dataInicial = $_POST['data_inicial'];
				$this->view->dataFinal = $_POST['data_final'];
			}else{
				$this->view->dataInicial = @date('01/m/Y');
				$this->view->dataFinal = @date('d/m/Y');
			}
		}else{
			$this->view->dataInicial = @date('01/m/Y');
			$this->view->dataFinal = @date('d/m/Y');
		}

	}


	public function projecaoVendasAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

	}

	public function ajaxAction(){

		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');

	  	$erro = -1;

	  	if($this->_getParam('fn') == 'get_rale_total'){

	  		$dbFluxoLoja = new Application_Model_DbTable_FluxoLoja();

	  		if(isset($_POST['data_inicial']) || isset($_POST['data_final'])){
				$_POST['data_inicial'] = implode("-", array_reverse(explode("/", $_POST['data_inicial'])));
				$_POST['data_final'] = implode("-", array_reverse(explode("/", $_POST['data_final'])));
			}else{
				$_POST['data_inicial'] = @date("Y-m-01");
				$_POST['data_final'] = @date("Y-m-d");
			}

			$_POST['duas_lojas'] = true;
			$_POST['relatorio_projetado'] = true;

			$arrRelatorio = $dbFluxoLoja->getFluxoNovoRelatorio($_POST);

			
			if($arrRelatorio){

				$head = "<tr><th>RALE</th>";

				$linhaR = "<tr><td style='text-align: left;'><b><span style='font-size: 15px; color: red; text-align: left;'>R</span>etornos Realizados</b></td>";
				$r = 0;
				$rTotal = 0;

				$linhaA = "<tr><td style='text-align: left;'><b><span style='font-size: 15px; color: red;'>A</span>tendimentos Loja</b></td>";
				$a = 0;
				$aTotal = 0;

				$linhaL = "<tr><td style='text-align: left;'><b><span style='font-size: 15px; color: red;'>L</span>igações Recebidas</b></td>";
				$l = 0;
				$lTotal = 0;

				$linhaE = "<tr><td style='text-align: left;'><b><span style='font-size: 15px; color: red;'>E</span>ntregas Realizadas</b></td>";
				$e = 0;
				$eTotal = 0;


				foreach ($arrRelatorio as $key=>$relatorio){

					$arrUsuarios[$relatorio['id_usuario']][$key] = $arrRelatorio[$key];

					if($relatorio['id_origem_cliente'] == 6){

						$r += $relatorio['qtd'];
						$rTotal += $relatorio['qtd'];

						if($arrRelatorio[$key+1]['data'] != $relatorio['data']){
							$head .= "<th style='vertical-align:middle;'>".substr(implode("/",array_reverse(explode("-",$relatorio['data']))), 0, 2)."</th>";
							$linhaR .= "<td style='text-align: center;'><span>".$r."</span></td>";
							$r = 0;
						}

					}

					if($relatorio['id_origem_cliente'] == 7){

						$a += $relatorio['qtd'];
						$aTotal += $relatorio['qtd'];

						if($arrRelatorio[$key+1]['data'] != $relatorio['data']){
							$linhaA .= "<td style='text-align: center;'><span>".$a."</span></td>";
							$a = 0;
						}

					}

					if($relatorio['id_origem_cliente'] == 8){

						$l += $relatorio['qtd'];
						$lTotal += $relatorio['qtd'];

						if($arrRelatorio[$key+1]['data'] != $relatorio['data']){
							$linhaL .= "<td style='text-align: center;'><span>".$l."</span></td>";
							$l = 0;
						}

					}

					if($relatorio['id_origem_cliente'] == 9){

						$e += $relatorio['qtd'];
						$eTotal += $relatorio['qtd'];

						if(isset($arrRelatorio[$key+1]['data'])){
							if($arrRelatorio[$key+1]['data'] != $relatorio['data']){
								$linhaE .= "<td style='text-align: center;'><span>".$e."</span></td>";
								$e = 0;
							}
						}else{
							$linhaE .= "<td style='text-align: center;'><span>".$e."</span></td>";
							$e = 0;
						}

					}

				}

			}


				$head .= "<th>TOTAL</th></tr>";
				$linhaR .= "<td style='text-align: center;'>".$rTotal."</td></tr>";
				$linhaA .= "<td style='text-align: center;'>".$aTotal."</td></tr>";
				$linhaL .= "<td style='text-align: center;'>".$lTotal."</td></tr>";
				$linhaE .= "<td style='text-align: center;'>".$eTotal."</td></tr>";

				echo "<table class='table table-hover table-striped'>".$head.$linhaR.$linhaA.$linhaL.$linhaE."</table>";


	  	}elseif($this->_getParam('fn') == 'get_rale_lojas'){

	  		$dbFluxoLoja = new Application_Model_DbTable_FluxoLoja();

	  		if(isset($_POST['data_inicial']) || isset($_POST['data_final'])){
				$_POST['data_inicial'] = implode("-", array_reverse(explode("/", $_POST['data_inicial'])));
				$_POST['data_final'] = implode("-", array_reverse(explode("/", $_POST['data_final'])));
			}else{
				$_POST['data_inicial'] = @date("Y-m-01");
				$_POST['data_final'] = @date("Y-m-d");
			}

			$_POST['duas_lojas'] = true;
			$_POST['relatorio_projetado'] = true;

			$arrRelatorio = $dbFluxoLoja->getFluxoNovoRelatorio($_POST);

			if($arrRelatorio){

				$cont1 = 0;
				$cont2 = 0;

				foreach ($arrRelatorio as $key=>$relatorio){
					
					if($relatorio['id_empresa'] == 3){
						$arrUsuarios[$relatorio['id_empresa']][$cont1] = $arrRelatorio[$key];
						$cont1++;
					}elseif($relatorio['id_empresa'] == 239){
						$arrUsuarios[$relatorio['id_empresa']][$cont2] = $arrRelatorio[$key];
						$cont2++;
					}
				}

				$arrUsuarios = array_reverse($arrUsuarios);

				foreach ($arrUsuarios as $usuarios){

					if($usuarios[0]['id_empresa'] == 3){
						$strloja = "Loja 1";
					}else{
						$strloja = "Loja 2";
					}

					$head = "<tr><th>".$strloja."</th>";

					$linhaR = "<tr><td style='text-align: left;'><b><span style='font-size: 15px; color: red;'>R</span>etornos Realizados</b></td>";
					$r = 0;
					$rTotal = 0;

					$linhaA = "<tr><td style='text-align: left;'><b><span style='font-size: 15px; color: red;'>A</span>tendimentos Loja</b></td>";
					$a = 0;
					$aTotal = 0;

					$linhaL = "<tr><td style='text-align: left;'><b><span style='font-size: 15px; color: red;'>L</span>igações Recebidas</b></td>";
					$l = 0;
					$lTotal = 0;

					$linhaE = "<tr><td style='text-align: left;'><b><span style='font-size: 15px; color: red;'>E</span>ntregas Realizadas</b></td>";
					$e = 0;
					$eTotal = 0;

					$strTable = "<br><br><table class='table table-hover table-striped'>";
					

					foreach ($usuarios as $key => $usuario){
						
						if($usuario['id_origem_cliente'] == 6){

							$r += $usuario['qtd'];
							$rTotal += $usuario['qtd'];

							if($usuarios[$key+1]['data'] != $usuario['data']){
								$head .= "<th style='vertical-align:middle;'>".substr(implode("/",array_reverse(explode("-",$usuario['data']))), 0, 2)."</th>";
								$linhaR .= "<td style='text-align: center;'><span>".$r."</span></td>";
								$r = 0;
							}

						}

						if($usuario['id_origem_cliente'] == 7){

							$a += $usuario['qtd'];
							$aTotal += $usuario['qtd'];

							if($usuarios[$key+1]['data'] != $usuario['data']){
								$linhaA .= "<td style='text-align: center;'><span>".$a."</span></td>";
								$a = 0;
							}

						}

						if($usuario['id_origem_cliente'] == 8){

							$l += $usuario['qtd'];
							$lTotal += $usuario['qtd'];

							if($usuarios[$key+1]['data'] != $usuario['data']){
								$linhaL .= "<td style='text-align: center;'><span>".$l."</span></td>";
								$l = 0;
							}

						}

						if($usuario['id_origem_cliente'] == 9){

							$e += $usuario['qtd'];
							$eTotal += $usuario['qtd'];

							if(isset($usuarios[$key+1]['data'])){
								if($usuarios[$key+1]['data'] != $usuario['data']){
									$linhaE .= "<td style='text-align: center;'><span>".$e."</span></td>";
									$e = 0;
								}
							}else{
								$linhaE .= "<td style='text-align: center;'><span>".$e."</span></td>";
								$e = 0;
							}

						}

					}

					$head .= "<th>TOTAL</th></tr>";
					$linhaR .= "<td style='text-align: center;'>".$rTotal."</td></tr>";
					$linhaA .= "<td style='text-align: center;'>".$aTotal."</td></tr>";
					$linhaL .= "<td style='text-align: center;'>".$lTotal."</td></tr>";
					$linhaE .= "<td style='text-align: center;'>".$eTotal."</td></tr>";

					$strTable .= $head;
					$strTable .= $linhaR;
					$strTable .= $linhaA;
					$strTable .= $linhaL;
					$strTable .= $linhaE;

					$strTable .= "</table>";

					echo $strTable;
					
				}

			}

	  	}elseif($this->_getParam('fn') == 'get_rale_vendedores'){

	  		$dbFluxoLoja = new Application_Model_DbTable_FluxoLoja();

	  		if(isset($_POST['data_inicial']) || isset($_POST['data_final'])){
				$_POST['data_inicial'] = implode("-", array_reverse(explode("/", $_POST['data_inicial'])));
				$_POST['data_final'] = implode("-", array_reverse(explode("/", $_POST['data_final'])));
			}else{
				$_POST['data_inicial'] = @date("Y-m-01");
				$_POST['data_final'] = @date("Y-m-d");
			}

			$_POST['duas_lojas'] = true;
			$_POST['relatorio_projetado'] = true;

			$arrRelatorio = $dbFluxoLoja->getFluxoNovoRelatorio($_POST);
			
			if($arrRelatorio){

				foreach ($arrRelatorio as $key=>$relatorio){
					$arrUsuarios[$relatorio['id_usuario']][$key] = $arrRelatorio[$key];
				}

				foreach ($arrUsuarios as $usuarios){

					$arrNome = current($usuarios);
					$head = "<tr><th>".$arrNome['nomeUsuario']."</th>";

					$linhaR = "<tr><td style='text-align: left;'><b><span style='font-size: 15px; color: red;'>R</span>etornos Realizados</b></td>";
					$r = 0;
					$rTotal = 0;

					$linhaA = "<tr><td style='text-align: left;'><b><span style='font-size: 15px; color: red;'>A</span>tendimentos Loja</b></td>";
					$a = 0;
					$aTotal = 0;

					$linhaL = "<tr><td style='text-align: left;'><b><span style='font-size: 15px; color: red;'>L</span>igações Recebidas</b></td>";
					$l = 0;
					$lTotal = 0;

					$linhaE = "<tr><td style='text-align: left;'><b><span style='font-size: 15px; color: red;'>E</span>ntregas Realizadas</b></td>";
					$e = 0;
					$eTotal = 0;

					$strTable = "<br><br><table class='table table-hover table-striped'>";
					

					foreach ($usuarios as $key => $usuario){
						
						if($usuario['id_origem_cliente'] == 6){

							$r += $usuario['qtd'];
							$rTotal += $usuario['qtd'];
							if(isset($usuarios[$key+1]['data'])) {
								if($usuarios[$key+1]['data'] != $usuario['data']){
									$head .= "<th style='vertical-align:middle;'>".substr(implode("/",array_reverse(explode("-",$usuario['data']))), 0, 2)."</th>";
									$linhaR .= "<td style='text-align: center;'><span>".$r."</span></td>";
									$r = 0;
								}
							}else{
								$head .= "<th style='vertical-align:middle;'>".substr(implode("/",array_reverse(explode("-",$usuario['data']))), 0, 2)."</th>";
								$linhaR .= "<td style='text-align: center;'><span>".$r."</span></td>";
								$r = 0;
							}

						}

						if($usuario['id_origem_cliente'] == 7){

							$a += $usuario['qtd'];
							$aTotal += $usuario['qtd'];
							if(isset($usuarios[$key+1]['data'])){
								if($usuarios[$key+1]['data'] != $usuario['data']){
									$linhaA .= "<td style='text-align: center;'><span>".$a."</span></td>";
									$a = 0;
								}
							}else{
								$linhaA .= "<td style='text-align: center;'><span>".$a."</span></td>";
								$a = 0;
							}

						}

						if($usuario['id_origem_cliente'] == 8){

							$l += $usuario['qtd'];
							$lTotal += $usuario['qtd'];
							if(isset($usuarios[$key+1]['data'])){
								if($usuarios[$key+1]['data'] != $usuario['data']){
									$linhaL .= "<td style='text-align: center;'><span>".$l."</span></td>";
									$l = 0;
								}
							}else{
								$linhaL .= "<td style='text-align: center;'><span>".$l."</span></td>";
								$l = 0;
							}

						}

						if($usuario['id_origem_cliente'] == 9){

							$e += $usuario['qtd'];
							$eTotal += $usuario['qtd'];
							if(isset($usuarios[$key+1]['data'])){
								if($usuarios[$key+1]['data'] != $usuario['data']){
									$linhaE .= "<td style='text-align: center;'><span>".$e."</span></td>";
									$e = 0;
								}
							}else{
								$linhaE .= "<td style='text-align: center;'><span>".$e."</span></td>";
								$e = 0;
							}

						}

					}

					$head .= "<th>TOTAL</th></tr>";
					$linhaR .= "<td style='text-align: center;'>".$rTotal."</td></tr>";
					$linhaA .= "<td style='text-align: center;'>".$aTotal."</td></tr>";
					$linhaL .= "<td style='text-align: center;'>".$lTotal."</td></tr>";
					$linhaE .= "<td style='text-align: center;'>".$eTotal."</td></tr>";

					$strTable .= $head;
					$strTable .= $linhaR;
					$strTable .= $linhaA;
					$strTable .= $linhaL;
					$strTable .= $linhaE;

					$strTable .= "</table>";

					echo $strTable;
					
				}

			}

	  	}elseif($this->_getParam('fn') == 'get_cursos'){

	  		$dbUsuarios = new Application_Model_DbTable_Usuarios();
	  		$dbCursos = new Application_Model_DbTable_Cursos();

	  		$arrFiltro['cursos'] = true;

			$arrUsuarios = $dbUsuarios->getVendedoresProjetado($arrFiltro);

			if(isset($_POST['data_inicial']) || isset($_POST['data_final'])){
				$arr['data_inicial'] = implode("-", array_reverse(explode("/", $_POST['data_inicial'])));
				$arr['data_final'] = implode("-", array_reverse(explode("/", $_POST['data_final'])));
			}else{
				$arr['data_inicial'] = @date("Y-m-01");
				$arr['data_final'] = @date("Y-m-d");
			}

			foreach ($arrUsuarios as $key => $usuario) {
				$arr['id_usuario'] = $usuario['id'];
                // $arr['data_inicial'] = @date("Y") . "-" . @date("m") . "-01";
                // $arr['data_final'] = @date("Y-m-d", mktime(0, 0, 0, @date("m") + 1, 0, @date("Y")));

				$arrTemp = $dbCursos->getQtdCursosPorUsuario($arr);

				if(!$arrTemp){
					$arrTemp[0]['id_usuario'] = $usuario['id'];
					$arrTemp[0]['nome'] = $usuario['nome'];
				}

				$arrTemp[0]['id_empresa'] = $usuario['id_empresa'];

                $arrCursos[$usuario['id']] = $arrTemp;

            }

            echo json_encode($arrCursos);

	  	}elseif($this->_getParam('fn') == 'save_metas'){

            $dbUsuarios = new Application_Model_DbTable_Usuarios();
            
            $id = $_POST['id'];
            unset($_POST['id']);

            if($id){
                echo $dbUsuarios->edt($id, $_POST);
            }

		}elseif($this->_getParam('fn') == 'get_data_retornos'){

			$dbUsuarios = new Application_Model_DbTable_Usuarios();
            $dbFluxoLoja = new Application_Model_DbTable_FluxoLoja();
            $arrFiltro = array();
            if(isset($_POST['id'])){
            	$arrFiltro['id'] = $_POST['id'];
            }
            if(isset($_POST['id_empresa'])){
	            $arrFiltro['id_empresa'] = $_POST['id_empresa'];
	        }
			$arrUsuarios = $dbUsuarios->getVendedoresProjetado($arrFiltro);

			if(isset($_POST['data_inicial']) || isset($_POST['data_final'])){
				$arr['data_inicial'] = implode("-", array_reverse(explode("/", $_POST['data_inicial'])));
				$arr['data_final'] = implode("-", array_reverse(explode("/", $_POST['data_final'])));
			}else{
				$arr['data_inicial'] = @date("Y-m-01");
				$arr['data_final'] = @date("Y-m-d");
			}

			foreach ($arrUsuarios as $key => $usuario) {

                $arrData[$key]['qtd_meta'] = $usuario['meta_retornos'];
				$arrData[$key]['id_usuario'] = $usuario['id'];
				$arrData[$key]['nome_vendedor'] = $usuario['nome'];
				$arrData[$key]['id_empresa'] = $usuario['id_empresa'];

				if(isset($_POST['data_inicial']) || isset($_POST['data_final'])){

					$arrTemp = explode("-", $arr['data_final']);

					if(isset($arrTemp[1]) && $arrTemp[1] == @date('m')){
						$arrData[$key]['qtd_dias'] = date('d');
						$arrData[$key]['qtd_dias_mes'] = date('t');
					}else{
						$arrData[$key]['qtd_dias'] = explode("-", $arr['data_final'])[2];
						$arrData[$key]['qtd_dias_mes'] = explode("-", $arr['data_final'])[2];
					}

				}else{
					$arrData[$key]['qtd_dias'] = date('d');
					$arrData[$key]['qtd_dias_mes'] = date('t');
				}

				$arr['id_usuario'] = $usuario['id'];
				$arr['id_origem_cliente'] = 6;
				$arrFluxo = $dbFluxoLoja->fluxoLojaTotal($arr);

				if(!$arrFluxo[0]['soma']){
					$arrFluxo[0]['soma'] = 0;
				}

				$arrData[$key]['qtd'] = $arrFluxo[0]['soma'];

			}

			echo json_encode($arrData);


		}elseif($this->_getParam('fn') == 'get_data_atendimentos'){

			$dbUsuarios = new Application_Model_DbTable_Usuarios();
			$dbFluxoClientes = new Application_Model_DbTable_FluxoClientes();

            $arrFiltro = array();
            if(isset($_POST['id'])){
            	$arrFiltro['id'] = $_POST['id'];
            }
            if(isset($_POST['id_empresa'])){
	            $arrFiltro['id_empresa'] = $_POST['id_empresa'];
	        }
			$arrUsuarios = $dbUsuarios->getVendedoresProjetado($arrFiltro);

			if(isset($_POST['data_inicial']) || isset($_POST['data_final'])){
				$arr['data_inicial'] = implode("-", array_reverse(explode("/", $_POST['data_inicial'])));
				$arr['data_final'] = implode("-", array_reverse(explode("/", $_POST['data_final'])));
			}else{
				$arr['data_inicial'] = @date("Y-m-01");
				$arr['data_final'] = @date("Y-m-d");
			}

			foreach ($arrUsuarios as $key => $usuario) {

				$arrData[$key]['qtd_meta'] = $usuario['meta_atendimentos'];
				$arrData[$key]['id_usuario'] = $usuario['id'];
				$arrData[$key]['nome_vendedor'] = $usuario['nome'];
				$arrData[$key]['id_empresa'] = $usuario['id_empresa'];

				if(isset($_POST['data_inicial']) || isset($_POST['data_final'])){

					$arrTemp = explode("-", $arr['data_final']);

					if($arrTemp[1] == @date('m')){
						$arrData[$key]['qtd_dias'] = date('d');
						$arrData[$key]['qtd_dias_mes'] = date('t');
					}else{
						$arrData[$key]['qtd_dias'] = explode("-", $arr['data_final'])[2];
						$arrData[$key]['qtd_dias_mes'] = explode("-", $arr['data_final'])[2];
					}

				}else{
					$arrData[$key]['qtd_dias'] = date('d');
					$arrData[$key]['qtd_dias_mes'] = date('t');
				}

				$arr['id_usuario'] = $usuario['id'];
				$arrFluxo = $dbFluxoClientes->getOrigemClientes($arr);

				$arrData[$key]['qtd'] = count($arrFluxo);

	
			}

			echo json_encode($arrData);


		}elseif($this->_getParam('fn') == 'get_data_repasse'){

			$dbUsuarios = new Application_Model_DbTable_Usuarios();
			$dbNegociacoes = new Application_Model_DbTable_Negociacoes();

            $arrFiltro = array();
            if(isset($_POST['id'])){
            	$arrFiltro['id'] = $_POST['id'];
            }
            if(isset($_POST['id_empresa'])){
	            $arrFiltro['id_empresa'] = $_POST['id_empresa'];
	        }
			$arrUsuarios = $dbUsuarios->getVendedoresProjetado($arrFiltro);

			$arr['tipo_venda'] = true;
			$arr['repasse'] = true;
			$arr['get_vendedor'] = true;

			if(isset($_POST['data_inicial']) || isset($_POST['data_final'])){
				$arr['data_inicial'] = implode("-", array_reverse(explode("/", $_POST['data_inicial'])));
				$arr['data_final'] = implode("-", array_reverse(explode("/", $_POST['data_final'])));
			}else{
				$arr['data_inicial'] = @date("Y-m-01");
				$arr['data_final'] = @date("Y-m-d");
			}

			foreach ($arrUsuarios as $key => $usuario) {

				$arrData[$key]['qtd_meta'] = $usuario['meta_repasses'];
				$arrData[$key]['id_usuario'] = $usuario['id'];
				$arrData[$key]['nome_vendedor'] = $usuario['nome'];
				$arrData[$key]['id_empresa'] = $usuario['id_empresa'];

				if(isset($_POST['data_inicial']) || isset($_POST['data_final'])){

					$arrTemp = explode("-", $arr['data_final']);

					if($arrTemp[1] == @date('m')){
						$arrData[$key]['qtd_dias'] = date('d');
						$arrData[$key]['qtd_dias_mes'] = date('t');
					}else{
						$arrData[$key]['qtd_dias'] = explode("-", $arr['data_final'])[2];
						$arrData[$key]['qtd_dias_mes'] = explode("-", $arr['data_final'])[2];
					}

				}else{
					$arrData[$key]['qtd_dias'] = date('d');
					$arrData[$key]['qtd_dias_mes'] = date('t');
				}

				$arr['id_vendedor'] = $usuario['id'];
				$arrVendas = $dbNegociacoes->getQtdPorUsuario($arr);

				$arrData[$key]['qtd'] = $arrVendas[0]['qtd'];

				
			}

			echo json_encode($arrData);


		}elseif($this->_getParam('fn') == 'get_data_vendas'){

			$dbUsuarios = new Application_Model_DbTable_Usuarios();
			$dbNegociacoes = new Application_Model_DbTable_Negociacoes();

            $arrFiltro = array();
            if(isset($_POST['id'])){
            	$arrFiltro['id'] = $_POST['id'];
            }
            if(isset($_POST['id_empresa'])){
	            $arrFiltro['id_empresa'] = $_POST['id_empresa'];
	        }
			$arrUsuarios = $dbUsuarios->getVendedoresProjetado($arrFiltro);

			$arr['tipo_venda'] = true;
			$arr['repasse'] = false;
			$arr['get_vendedor'] = true;

			if(isset($_POST['data_inicial']) || isset($_POST['data_final'])){
				$arr['data_inicial'] = implode("-", array_reverse(explode("/", $_POST['data_inicial'])));
				$arr['data_final'] = implode("-", array_reverse(explode("/", $_POST['data_final'])));
			}else{
				$arr['data_inicial'] = @date("Y-m-01");
				$arr['data_final'] = @date("Y-m-d");
			}


			foreach ($arrUsuarios as $key => $usuario) {

				$arrData[$key]['qtd_meta'] = $usuario['meta_vendas'];
				$arrData[$key]['id_usuario'] = $usuario['id'];
				$arrData[$key]['id_empresa'] = $usuario['id_empresa'];

				if(isset($_POST['data_inicial']) || isset($_POST['data_final'])){

					$arrTemp = explode("-", $arr['data_final']);

					if($arrTemp[1] == @date('m')){
						$arrData[$key]['qtd_dias'] = date('d');
						$arrData[$key]['qtd_dias_mes'] = date('t');
					}else{
						$arrData[$key]['qtd_dias'] = explode("-", $arr['data_final'])[2];
						$arrData[$key]['qtd_dias_mes'] = explode("-", $arr['data_final'])[2];
					}

				}else{
					$arrData[$key]['qtd_dias'] = date('d');
					$arrData[$key]['qtd_dias_mes'] = date('t');
				}

				$loja = "<span>(Loja 1)</span>";
				if($usuario['id_empresa'] == 239){
					$loja = "<span>(Loja 2)</span>";
				}

				$arrData[$key]['nome_vendedor'] = current(explode(" ", $usuario['nome'])).$loja;

				$arr['id_vendedor'] = $usuario['id'];
				$arrVendas = $dbNegociacoes->getQtdPorUsuario($arr);

				$arrData[$key]['qtd'] = $arrVendas[0]['qtd'];
				
			}

			echo json_encode($arrData);

		}elseif($this->_getParam('fn') == 'get_metas'){

			$dbMetas = new Application_Model_DbTable_Metas();
			$arrMetas = $dbMetas->getMetasPerfil(array('id_perfil' => $this->_getParam('id_perfil'), 'id_empresa' => $_SESSION['sessionUser']['id_empresa']));

			echo json_encode($arrMetas[0]);

		}elseif($this->_getParam('fn') == 'get_perfis'){

			$dbPerfil = new Application_Model_DbTable_Perfis();
			$arrPerfis = $dbPerfil->getPerfis();

			echo json_encode($arrPerfis);

		}elseif($this->_getParam('fn') == 'get_data_cursos'){

           	//$this->getEmailImap();

            $dbUsuarios = new Application_Model_DbTable_Usuarios();
            $dbCursos = new Application_Model_DbTable_Cursos();
            
            $arrFiltro = array();
            if(isset($_POST['id'])){
            	$arrFiltro['id'] = $_POST['id'];
            }
            if(isset($_POST['id_empresa'])){
	            $arrFiltro['id_empresa'] = $_POST['id_empresa'];
	        }
			$arrUsuarios = $dbUsuarios->getVendedoresProjetado($arrFiltro);

			if(isset($_POST['data_inicial']) || isset($_POST['data_final'])){
				$arr['data_inicial'] = implode("-", array_reverse(explode("/", $_POST['data_inicial'])));
				$arr['data_final'] = implode("-", array_reverse(explode("/", $_POST['data_final'])));
			}else{
				$arr['data_inicial'] = @date("Y-m-01");
				$arr['data_final'] = @date("Y-m-d");
			}

            foreach ($arrUsuarios as $key => $usuario) {

				$arrData[$key]['qtd_meta'] = $usuario['meta_cursos'];
				$arrData[$key]['id_usuario'] = $usuario['id'];
				
				if(isset($_POST['data_inicial']) || isset($_POST['data_final'])){

					$arrTemp = explode("-", $arr['data_final']);

					if($arrTemp[1] == @date('m')){
						$arrData[$key]['qtd_dias'] = date('d');
						$arrData[$key]['qtd_dias_mes'] = date('t');
					}else{
						$arrData[$key]['qtd_dias'] = explode("-", $arr['data_final'])[2];
						$arrData[$key]['qtd_dias_mes'] = explode("-", $arr['data_final'])[2];
					}

				}else{
					$arrData[$key]['qtd_dias'] = date('d');
					$arrData[$key]['qtd_dias_mes'] = date('t');
				}

				$arrData[$key]['nome_vendedor'] = $usuario['nome'];
				$arrData[$key]['id_empresa'] = $usuario['id_empresa'];

				$arr['id_usuario'] = $usuario['id'];
               	// $arr['data_inicial'] = @date("Y") . "-" . @date("m") . "-01";
                // $arr['data_final'] = @date("Y-m-d", mktime(0, 0, 0, @date("m") + 1, 0, @date("Y")));
				$arrCursos = $dbCursos->getQtdCursosPorUsuario($arr);

                $arrData[$key]['qtd'] = count($arrCursos);

            }
            
            echo json_encode($arrData);

        }elseif($this->_getParam('fn') == 'get_preparacoes'){

			$dbUsuarios = new Application_Model_DbTable_Usuarios();
			$dbVeiculos = new Application_Model_DbTable_Veiculos();

			$arrFiltro = array();
			$arrUsuarios = $dbUsuarios->getPreparadoresProjetado($arrFiltro);

			if(isset($_POST['data_inicial'])){

				$arrDataTemp = explode("/", $_POST['data_inicial']);
				$arr['data_inicial'] = $arrDataTemp[2]."-".$arrDataTemp[1]."-".$arrDataTemp[0];
				$arr['data_final'] = $arrDataTemp[2]."-".$arrDataTemp[1]."-".cal_days_in_month(CAL_GREGORIAN, $arrDataTemp[1], $arrDataTemp[2]);

			}else{
				$arr['data_inicial'] = @date("Y-m-01");
				$arr['data_final'] = @date("Y")."-".@date("m")."-".cal_days_in_month(CAL_GREGORIAN, @date("m"), @date("Y"));
			}

			foreach ($arrUsuarios as $key => $arrUsuario) {

				$arr['id_empresa'] = $arrUsuario['id_empresa'];

				$arrVeiculos = $dbVeiculos->getVeiculosPorEmpresaMes($arr);

				$arrDados[$key]['id'] = $arrUsuario['id'];
				$arrDados[$key]['nome'] = $arrUsuario['nome'];
				$arrDados[$key]['id_empresa'] = $arrUsuario['id_empresa'];
				$arrDados[$key]['realizado'] = 0;
				$arrDados[$key]['atrasado'] = 0;

				foreach ($arrVeiculos as $arrVeiculo) {
					if(
						($arrVeiculo['data_inicio_preparacao']) && 
						($arrVeiculo['outros_date_concluido']) && 
						($arrVeiculo['concluido_date_concluido']) && 
						($arrVeiculo['concluido_date_concluido'] != "0000-00-00") && 
						($arrVeiculo['outros_date_concluido'] != "0000-00-00") && 
						($arrVeiculo['data_inicio_preparacao'] != "0000-00-00") && 
						($arrVeiculo['concluido'] == 1) && 
					   	(strtotime($arrVeiculo['concluido_date_concluido']) <= strtotime($arrVeiculo['outros_date_concluido']))
					){
						$arrDados[$key]['realizado'] += 1;
						//$arrDados[$key]['placa'] .= " R:".$arrVeiculo['placa'];
					}elseif(
						($arrVeiculo['data_inicio_preparacao']) && 
						($arrVeiculo['outros_date_concluido']) && 
						($arrVeiculo['concluido_date_concluido']) && 
						($arrVeiculo['concluido_date_concluido'] != "0000-00-00") && 
						($arrVeiculo['outros_date_concluido'] != "0000-00-00") && 
						($arrVeiculo['data_inicio_preparacao'] != "0000-00-00") && 
						($arrVeiculo['concluido'] == 1) && 
						(strtotime($arrVeiculo['concluido_date_concluido']) > strtotime($arrVeiculo['outros_date_concluido']))
					){
						$arrDados[$key]['atrasado'] += 1;
						//$arrDados[$key]['placa'] .= " A:".$arrVeiculo['placa'];
					}elseif(
						($arrVeiculo['data_inicio_preparacao']) && 
						($arrVeiculo['data_inicio_preparacao'] != "0000-00-00") && 
						(strtotime(@date("Y-m-d")) > strtotime($arrVeiculo['outros_date_concluido']))
					){
						$arrDados[$key]['atrasado'] += 1;
						//$arrDados[$key]['placa'] .= " A:".$arrVeiculo['placa'];
					}

				}

			}

			echo json_encode($arrDados);

		}

	}

	private function conectaEmail(){
		
        $dbEmpresas = new Application_Model_DbTable_Empresas();

		$arrEmpresa = $dbEmpresas->getEmpresa(3);
		
		//return imap_open("{imap.superheros.provisorio.ws:143/novalidate-cert}", $arrEmpresa[0]['email_imap'], $arrEmpresa[0]['senha_email_imap']);

		return imap_open("{imap.gmail.com:993/imap/ssl}", $arrEmpresa[0]['email_imap'], $arrEmpresa[0]['senha_email_imap']);
		//return imap_open("{imap.gmail.com:993/imap/ssl}", "contato@selectveiculos.com.br", $arrEmpresa[0]['senha_email_imap']);

	}

	private function getEmailImap(){
		
		$caixaEmail = $this->conectaEmail();

        $dbCursos = new Application_Model_DbTable_Cursos();
        $dbUsuarios = new Application_Model_DbTable_Usuarios();

        $arr['cursos'] = true;
        $arrUsuarios = $dbUsuarios->getVendedoresProjetado($arr);
		

		$numEmailsLidos = 100;
		$numTotalEmail = imap_num_msg($caixaEmail);

		for($i=$numTotalEmail-$numEmailsLidos; $i<=$numTotalEmail; $i++){
		//for($i=12410; $i<=12410; $i++){
			
			$arrHead = imap_headerinfo($caixaEmail, $i);

			if($arrHead->Unseen == "U"){

				$arrHead = get_object_vars($arrHead);
				$arrFrom = get_object_vars($arrHead['from'][0]);
				$from = $arrFrom['mailbox']."@".$arrFrom['host'];

				if($from == "contato@vendedorouoferecedor.com.br"){

					$arr = explode(":", imap_qprint(imap_fetchbody($caixaEmail, $i, 1)));
					$arrDados['titulo'] = trim(str_replace("user", "", str_replace("\"", "", strtolower($arr[7]))));
					$arrDados['nome'] = strtolower(current(explode(" ", trim($arr[8]))));
					$arrDados['data'] = date("Y-m-d H:i:s", strtotime($arrHead['date']));
					$arrDados['score'] = strtolower(current(explode(" ", trim($arr[9]))));
					$arrDados['max_score'] = trim(str_replace("passing", "", strtolower(current(explode(" ", trim($arr[10]))))));
					$arrDados['pass_score'] = strtolower(current(explode(" ", trim($arr[11]))));
					$arrDados['result'] = trim(str_replace("*", "", str_replace("question", "", strtolower(current(explode(" ", trim($arr[15])))))));
                    
                    $dbCursos->add($arrDados);

				}elseif($from == "noreply@quizresults.net"){

					$arr = explode(":", imap_qprint(imap_fetchbody($caixaEmail, $i, 1)));
					$arrDados['titulo'] = str_replace("  ", " ", str_replace("&quot;", "", trim(str_replace("user", "", strtolower(strip_tags($arr[1]))))));
					$arrDados['nome'] = strip_tags(strtolower(current(explode(" ", trim($arr[2])))));
					$arrDados['data'] = strip_tags(date("Y-m-d H:i:s", strtotime($arrHead['date'])));
					$arrDados['score'] = strip_tags(current(explode(" ", trim($arr[3]))));
					$arrDados['max_score'] = strip_tags(str_replace(" ", "", current(explode("/>", $arr[4]))));
					$arrDados['pass_score'] = strip_tags(current(explode(" ", trim($arr[5]))));
					$arrDados['result'] = strip_tags(trim(str_replace("*", "", str_replace("question", "", strtolower(current(explode(" ", trim($arr[9]))))))));

                    foreach($arrUsuarios as $usuario){
                        if(current(explode(" ", strtolower($usuario['nome']))) == $arrDados['nome']){
                            $arrDados['id_usuario'] = $usuario['id'];
                        }
                    }

                    /////////////////////////////////////////////////////////
                    ////Seta Email como não lido 						 ////
                    //imap_clearflag_full($caixaEmail, $i, "\\Seen");  ////
                    /////////////////////////////////////////////////////////

					// var_export("<pre>");
					// var_export($arrDados);
                    // var_export("</pre>");

                    $dbCursos->add($arrDados);

				}

			}

		}

	}

	
	public function visualizaEstatisticaAction(){
	
		$dbVisualizacoes = new Application_Model_DbTable_VisualizacoesEmpresaEstoque();
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		
		$arrBusca['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$this->view->arrVeiculos = $dbVeiculos->getVeiculosPorEmpresa2($_SESSION['sessionUser']['id_empresa']);
		
		$modelo = "Todos";
		
		if($this->getRequest()->isPost()){
		
			if($_POST['id_veiculo']){
				
				$arr['id'] = $_POST['id_veiculo'];
			
				$arrVeiculo = $dbVeiculos->getVeiculoPorId($arr);
				
				if($arrVeiculo[0]['descricao_site']){
				
					$modelo = $arrVeiculo[0]['descricao_site'];
				
				}else{
				
					$modelo = $arrVeiculo[0]['modelo'];
			
				}
			
			}
			
		}else{
			
			$_POST['dias'] = 15;
			
		}
	
		$arrBusca['data_final'] = @date("Y-m-d");
		
		$arrBusca['data_inicial'] = @date("Y-m-d", mktime(0,0,0,@date("m"),(@date("d")-$_POST['dias']),@date("Y")));
		
		$arrDados = $dbVisualizacoes->_get($arrBusca);
		
		$dias = $_POST['dias']-1;
		
		for($i = 0; $i < $_POST['dias'];$i++){
		
			$arrBusca['data'] = @date("Y-m-d", mktime(0,0,0,@date("m"),(@date("d")-$dias),@date("Y")));
			
			$arrData = explode("-",$arrBusca['data']);

			
			$dadosView[$arrData[2]]['dia'] = $arrData[2];
			$dadosView[$arrData[2]]['telefone'] = 0;
			$dadosView[$arrData[2]]['endereco'] = 0;
			$dadosView[$arrData[2]]['estoque'] = 0;
			$dadosView[$arrData[2]]['visitas'] = 0;
			$dadosView[$arrData[2]]['interesse'] = 0;

			foreach($arrDados as $dados){
			
				if($arrBusca['data'] == $dados['data']){
				
					$dadosView[$arrData[2]]['dia'] = $dados['data'];
					$dadosView[$arrData[2]]['telefone'] += $dados['qtd_visualizacoes_telefone'];
					$dadosView[$arrData[2]]['endereco'] += $dados['qtd_visualizacoes_endereco'];
					$dadosView[$arrData[2]]['estoque'] += $dados['qtd_visualizacoes_estoque'];
					
					
					$totalTelefone += $dados['qtd_visualizacoes_telefone'];
					$totalEndereco += $dados['qtd_visualizacoes_endereco'];
					$totalEstoque += $dados['qtd_visualizacoes_estoque'];
					
					
					if($_POST['id_veiculo'] == ""){
					
						$dadosView[$arrData[2]]['visitas'] += $dados['qtd_visitas'];
						$totalVisitas += $dados['qtd_visitas'];
						
						$dadosView[$arrData[2]]['interesse'] += $dados['qtd_interesse'];
						$totalInteresse += $dados['qtd_interesse'];
					
					}elseif($dados['id_veiculo'] == $_POST['id_veiculo']){
					
						$dadosView[$arrData[2]]['visitas'] += $dados['qtd_visitas'];
						$totalVisitas += $dados['qtd_visitas'];
						
						$dadosView[$arrData[2]]['interesse'] += $dados['qtd_interesse'];
						$totalInteresse += $dados['qtd_interesse'];
					
					}
				
				}
			
			}
			
			$dias = $dias - 1;

			$diasView .= "'".$this->retornaDiaSemana($arrBusca['data'])." ".$arrData[2]."', ";
			$telefoneView .= $dadosView[$arrData[2]]['telefone'].", ";
			$telefoneViewInfo .= "'<b>".$this->retornaDiaSemana($arrBusca['data'])." Dia ".implode("/",array_reverse(explode("-",$arrBusca['data'])))."</b><br/> ".$dadosView[$arrData[2]]['telefone']." Visualizações do Telefone', ";
			
			$enderecoView .= $dadosView[$arrData[2]]['endereco'].", ";
			$enderecoViewInfo .= "'<b>".$this->retornaDiaSemana($arrBusca['data'])." Dia ".implode("/",array_reverse(explode("-",$arrBusca['data'])))."</b><br/> ".$dadosView[$arrData[2]]['endereco']." Visualizações do Endereço', ";
			
			$estoqueView .= $dadosView[$arrData[2]]['estoque'].", ";
			$estoqueViewInfo .= "'<b>".$this->retornaDiaSemana($arrBusca['data'])." Dia ".implode("/",array_reverse(explode("-",$arrBusca['data'])))."</b><br/> ".$dadosView[$arrData[2]]['estoque']." Visualizações do Estoque', ";
			
			$visitasView .= $dadosView[$arrData[2]]['visitas'].", ";
			$visitasViewInfo .= "'<b>".$this->retornaDiaSemana($arrBusca['data'])." Dia ".implode("/",array_reverse(explode("-",$arrBusca['data'])))."</b><br/> ".$dadosView[$arrData[2]]['visitas']." Visitas<br/> Veículo: ".$modelo."', ";
			
			$interesseView .= $dadosView[$arrData[2]]['interesse'].", ";
			$interesseViewInfo .= "'<b>".$this->retornaDiaSemana($arrBusca['data'])." Dia ".implode("/",array_reverse(explode("-",$arrBusca['data'])))."</b><br/> ".$dadosView[$arrData[2]]['interesse']." Interesses<br/> Veículo: ".$modelo."', ";
			
		}
		
		
		
		$diasView = "[".substr($diasView,0,-2)."]";
		
		$telefoneView = "[".substr($telefoneView,0,-2)."]";
		$telefoneViewInfo = substr($telefoneViewInfo,0,-2);
		
		$enderecoView = "[".substr($enderecoView,0,-2)."]";
		$enderecoViewInfo = substr($enderecoViewInfo,0,-2);
		
		$estoqueView = "[".substr($estoqueView,0,-2)."]";
		$estoqueViewInfo = substr($estoqueViewInfo,0,-2);
		
		$visitasView = "[".substr($visitasView,0,-2)."]";
		$visitasViewInfo = substr($visitasViewInfo,0,-2);
		
		$interesseView = "[".substr($interesseView,0,-2)."]";
		$interesseViewInfo = substr($interesseViewInfo,0,-2);
		
		
		
		$this->view->qtdDias = count($dadosView);
		$this->view->dias = $diasView;
		$this->view->idVeiculo = $_POST['id_veiculo'];

		$this->view->telefone = $telefoneView;
		$this->view->telefoneViewInfo = $telefoneViewInfo;
		
		$this->view->endereco = $enderecoView;
		$this->view->enderecoViewInfo = $enderecoViewInfo;
		
		$this->view->estoque = $estoqueView;
		$this->view->estoqueViewInfo = $estoqueViewInfo;
		
		$this->view->visitas = $visitasView;
		$this->view->visitasViewInfo = $visitasViewInfo;
		
		$this->view->interesses = $interesseView;
		$this->view->interessesViewInfo = $interesseViewInfo;
		
		$this->view->totalTelefone = $totalTelefone;
		$this->view->totalEndereco = $totalEndereco;
		$this->view->totalEstoque = $totalEstoque;
		$this->view->totalVisitas = $totalVisitas;
		$this->view->totalInteresses = $totalInteresse;
		
		
		
	//var_export($dadosView);
		
		
		//$this->view->arrDados = $dbVisualizacoes->_get($arrBusca);
		
		//var_export($dbVisualizacoes->_get($arrBusca));
	
	}
	
	
	public function listaAction(){
	
		/*$this->validaAcesso('listar_financeiras_despachantes');
		
		$dbFinanceira = new Application_Model_DbTable_Financeiras();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		if($this->getRequest()->isPost()){
		
			$arr['parcial'] = true;
			$arr['nome'] = $this->_getParam('nome');
			$arr['cnpj'] = $this->_getParam('cnpj');
			
		}
		
		$arrFinanceiras = $dbFinanceira->_getFD($arr);
		
		$this->view->financeiras = $arrFinanceiras;*/
	
	}
	
	function retornaDiaSemana($data) {
	
		$arrData = explode("-",$data);
	
		$ano =  $arrData[0];
		$mes =  $arrData[1];
		$dia =  $arrData[2];

		$diasemana = date("w", mktime(0,0,0,$mes,$dia,$ano) );

		switch($diasemana) {
			case"0": $diasemana = "Dom"; break;
			case"1": $diasemana = "Seg"; break;
			case"2": $diasemana = "Ter"; break;
			case"3": $diasemana = "Qua"; break;
			case"4": $diasemana = "Qui"; break;
			case"5": $diasemana = "Sex"; break;
			case"6": $diasemana = "Sáb"; break;
		}

		return $diasemana;
		
	}


}

?>
