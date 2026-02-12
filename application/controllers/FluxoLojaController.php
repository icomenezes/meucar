<?php

header("Content-Type: text/html; charset=UTF-8",true);

class FluxoLojaController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Fluxo de Loja";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function fluxoLojaAction(){
		
		//$this->validaAcesso('fluxoLoja');
		
	}


	public function novoRelatorioFluxoLojaAction(){

		$dbVendedores = new Application_Model_DbTable_Vendedores();
		$dbFluxoLoja = new Application_Model_DbTable_FluxoLoja();

		$this->view->titulo = "RALE";
		
		$this->view->vendedores = $dbVendedores->_get(array('id_empresa'=>$_SESSION['sessionUser']['id_empresa']));

		if($_SESSION['sessionUser']['id_perfil'] != 2){
			$_POST['id_usuario'] = $_SESSION['sessionUser']['id'];
		}


		if($this->getRequest()->isPost()){

			$this->view->vendedor = $_POST['id_usuario'];
			$this->view->mes = $_POST['mes'];
			$this->view->dataInicial = $_POST['data_inicial'];
			$this->view->dataFinal = $_POST['data_final'];
			unset($_POST['mes']);
			$_POST['data_inicial'] = implode("-", array_reverse(explode("/", $_POST['data_inicial'])));
			$_POST['data_final'] = implode("-", array_reverse(explode("/", $_POST['data_final'])));

			$arrRelatorio = $dbFluxoLoja->getFluxoNovoRelatorio($_POST);

		}else{

			$_POST['data_inicial'] = @date('Y-m-01');
			$_POST['data_final'] = @date('Y-m-d');

			$this->view->dataInicial = implode("/", array_reverse(explode("-", $_POST['data_inicial'])));
			$this->view->dataFinal = implode("/", array_reverse(explode("-", $_POST['data_final'])));

			$arrRelatorio = $dbFluxoLoja->getFluxoNovoRelatorio($_POST);
		}

		$this->view->arrRelatorio = $arrRelatorio;

		// print "<br><br><br><br><br><br><br><br><br><br><pre>";
		// var_export($arrRelatorio);
		// print "</pre>";

		

	}



	public function addNovoRelatorioDiarioAction(){
		
		$this->validaAcesso('gerenciar_fluxo_de_loja');
	
		$this->view->titulo = "Relatório Diário";

		$dbFluxoLoja = new Application_Model_DbTable_FluxoLoja();

		$arrDados[] = null;


		if($this->getRequest()->isPost()){

			$redireciona = false;

			if(!$_POST['data']){
				$arr['data'] = @date('y-m-d');
			}else{
				$arr['data'] = $_POST['data'];
				$redireciona = true;
			}

			unset($_POST['data']);

			foreach ($_POST as $key => $value) {

				$arr['id_origem_cliente'] = $key;
				$arrFluxo = $dbFluxoLoja->getFluxoData($arr);

				if($arrFluxo[0]['id']){
					$arr['qtd'] = $value;
					$dbFluxoLoja->edt($arrFluxo[0]['id'], $arr);
				}else{
					$arr['qtd'] = $value;
					$arr['id_usuario'] = $_SESSION['sessionUser']['id'];
					$dbFluxoLoja->add($arr);
				}

			}

			if($redireciona){
				$this->_helper->redirector->gotoUrl("/agenda/agenda");
			}

		}

		if($_SESSION['sessionUser']['id_perfil'] == 3 || $_SESSION['sessionUser']['id_perfil'] == 9){

			foreach ($dbFluxoLoja->fluxoLojaDiario(array('id_usuario'=>$_SESSION['sessionUser']['id'])) as $value){
				if(!isset($arrDados[$value['data']][$value['id_origem_cliente']])){
					$arrDados[$value['data']][$value['id_origem_cliente']] = 0;
				}
				$arrDados[$value['data']][$value['id_origem_cliente']] += $value['qtd'];
			}

		}else{
			
			foreach ($dbFluxoLoja->fluxoLojaDiario(array()) as $value){
				if(!isset($arrDados[$value['data']][$value['id_origem_cliente']])){
					$arrDados[$value['data']][$value['id_origem_cliente']] = 0;
				}
				$arrDados[$value['data']][$value['id_origem_cliente']] += $value['qtd'];
			}

		}

		$this->view->fluxoLoja = $arrDados;

	}

	
	public function addAction(){
		
		$this->validaAcesso('gerenciar_fluxo_de_loja');
		
		$dbFluxoLoja = new Application_Model_DbTable_FluxoLoja();
		
		$dbOrigemClientes = new Application_Model_DbTable_OrigemClientes();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrFiltro['fl'] = 1; // a consulta irá trazer somente os tipos referentes ao fluxo de loja
		
		$arrFiltro['noDefault'] = 1;
		
		$arrOrigemClientes = $dbOrigemClientes->_get($arrFiltro);
		
		$this->view->origemClientes = $arrOrigemClientes;
		
		$arrOrigemClientes = $dbOrigemClientes->_get($arrFiltro);
		
		$arrFiltro['id_usuario'] = $_SESSION['sessionUser']['id'];
		
		$arrFiltro['dataHoje'] = @date('Y-m-d');
		
		$arrFluxoLoja = $dbFluxoLoja->_get($arrFiltro);
		
		$this->view->fluxoLoja = $arrFluxoLoja;
		
		if($this->getRequest()->isPost()){
			
			$dados = $_POST;
			
			/*$dados['id_usuario'] = $_SESSION['sessionUser']['id'];
			
			$arrT = explode("/",$dados['data']);
			
			$dados['data'] = implode("-",array_reverse($arrT));
			
			unset($dados['hora']);*/
			
			//var_export($dados);exit;
			
			foreach($dados as $k => $v){
			
				$arrT = explode("_", $k);
				
				$dado['id_origem_cliente'] = $arrT[1];
				$dado['qtd'] = $v;
				$dado['id_usuario'] = $_SESSION['sessionUser']['id'];
				$dado['data'] = @date('Y-m-d');
			
				if(!$dado['qtd']){
				
					$dado['qtd'] = 0;
				
				}
				
				//var_export($dado);
				//exit;
				
				if($dbFluxoLoja->replaceFluxo($dado));
				
				header('Location: /agenda/agenda');
				
			}
			
			//header('Location: /agenda/agenda');
			
			/*var_export($dados);exit;
			
			if($dbFluxoLoja->insert($dados)){
   
				$this->view->mensagem = "Informa&ccedil;&atilde;o cadastrada com sucesso!";
			   
			}else{
			   
				$this->view->mensagem = "Erro ao cadastrar Informa&ccedil;&atilde;o.";
			   
			}*/
			
		}
	
	}
	
	public function addRelatorioDiarioAction(){
		
		$this->validaAcesso('gerenciar_fluxo_de_loja');
		
		$this->view->titulo = "Relatório Diário";
		
		$dbFluxoLoja = new Application_Model_DbTable_FluxoLoja();
		
		$dbOrigemClientes = new Application_Model_DbTable_OrigemClientes();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		$arrFiltro['fl'] = 0; // a consulta irá trazer somente os tipos referentes ao fluxo de loja
		
		$arrOrigemClientes = $dbOrigemClientes->_get($arrFiltro);
		
		$this->view->origemClientes = $arrOrigemClientes;
		
		$arrOrigemClientes = $dbOrigemClientes->_get($arrFiltro);
		
		$arrFiltro['id_usuario'] = $_SESSION['sessionUser']['id'];
		
		$arrFiltro['dataHoje'] = @date('Y-m-d');
		
		$arrFluxoLoja = $dbFluxoLoja->_get($arrFiltro);

		$this->view->fluxoLoja = $arrFluxoLoja;
		
		if($this->getRequest()->isPost()){
			
			$dados = $_POST;
			
			/*$dados['id_usuario'] = $_SESSION['sessionUser']['id'];
			
			$arrT = explode("/",$dados['data']);
			
			$dados['data'] = implode("-",array_reverse($arrT));
			
			unset($dados['hora']);*/
			
			//var_export($dados);exit;
			
			foreach($dados as $k => $v){
			
				$arrT = explode("_", $k);
				
				$dado['id_origem_cliente'] = $arrT[1];
				$dado['qtd'] = $v;
				$dado['id_usuario'] = $_SESSION['sessionUser']['id'];
				$dado['data'] = @date('Y-m-d');
			
				if(!$dado['qtd']){
				
					$dado['qtd'] = 0;
				
				}
				
				//var_export($dado);
				//exit;
				
				$dbFluxoLoja->replaceFluxo($dado);
				
				header('Location: /fluxo-loja/add');
				
			}
			
			/*var_export($dados);exit;
			
			if($dbFluxoLoja->insert($dados)){
   
				$this->view->mensagem = "Informa&ccedil;&atilde;o cadastrada com sucesso!";
			   
			}else{
			   
				$this->view->mensagem = "Erro ao cadastrar Informa&ccedil;&atilde;o.";
			   
			}*/
			
		}
	
	}

	public function edtAction(){
		
		$dbFluxoLoja = new Application_Model_DbTable_FluxoLoja();
		
		$this->validaAcesso('gerenciar_fluxo_de_loja');
		
		if($this->getRequest()->isPost()){
			
			$dados = $_POST;
			$dados['id_usuario'] = $_SESSION['sessionUser']['id'];
			
			$arrT = explode("/",$dados['data']);
			
			$dados['data'] = implode("-",array_reverse($arrT));
			
			$dados['data'] .= " " . $dados['hora'];
			
			unset($dados['hora']);
			
			if(!$dados['baixado']){ $dados['baixado'] = 0; }
			
			if($dbFluxoLoja->update($dados, "id = " . $this->_getParam('id'))){
   
				$this->view->mensagem = "Compromisso editado com sucesso!";
			   
			}else{
			   
				$this->view->mensagem = "Erro ao editar o compromisso.";
			   
			}
			
		}
		
		$a = $dbFluxoLoja->fetchAll("id = " . $this->_getParam('id'));
		
		$this->view->a = $a[0];
	
	}
	
	public function relatorioFluxoLojaAction(){
	
		$dbVendedores = new Application_Model_DbTable_Vendedores();
		
		$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arrFiltro['relatorio'] = 1;
		//$arrFiltro['id_perfil'] = VENDEDOR;
		
		$this->view->vendedores = $dbVendedores->_get($arrFiltro);
		
		if($this->getRequest()->isPost()){
			
			$dbFluxoLoja = new Application_Model_DbTable_FluxoLoja();
			
			$_POST['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			
			if($_POST['id_usuario'] == 0){
			
				$nomeVendedor = "Todos";
			
			}else{
				
				$id = $_POST['id_usuario'];
				$arrNome['id'] = $id;
				$nomeVendedor = $dbVendedores->_get($arrNome);
				$nomeVendedor = $nomeVendedor[0]['nome'];
			
			}
			
			if($_POST['data_inicial']){
			
				$this->view->dataInicial = $_POST['data_inicial'];
				
				$_POST['data_inicial'] = implode("-",array_reverse(explode("/",$_POST['data_inicial'])));
			
			}
			
			if($_POST['data_final']){
			
				$this->view->dataFinal = $_POST['data_final'];
				
				$_POST['data_final'] = implode("-",array_reverse(explode("/",$_POST['data_final'])));
			
			}
			
			$_POST['rd'] = 1;
			
			$arrFluxoLoja = $dbFluxoLoja->relatorioFluxoLoja($_POST);
			
			$relatorios .= "<div style='margin-left:10px; margin-bottom:10px;'><b>Relatório Diário: ".$nomeVendedor."</b></div>";

			$relatorios .= "<table class='table'>
						   <tr>
							<th class='cabeca'>Descri&ccedil;&atilde;o</th>
							<th class='cabeca'>Quantidade</th>
						  </tr>";
			$count = 1;
			$subtotal="";

			foreach($arrFluxoLoja as $key=>$fluxoLoja){
				
				if($arrFluxoLoja[$key+1]['id_origem_cliente'] != $fluxoLoja['id_origem_cliente']){
	
					$rowspan = "<tr>
								<td class='tds' rowspan='".$count."'>".$fluxoLoja['descricao']."</td>
								<td class='tds'>".$fluxoLoja['soma']."</td>
							  </tr>";
					
					$somaQtd += $fluxoLoja['soma'];
					
					$relatorios .= $rowspan.$relatorio.$subtotal;
					
					$count = 1;
					$relatorio ="";
					$somaQtd = 0;
					
				}else{
					$count++;
					$relatorio .="<tr>
									<td class='tds' rowspan='".$count."'>".$fluxoLoja['descricao']."</td>
									<td class='tds'>".$fluxoLoja['soma']."</td>
								</tr>";
						
					$somaDespesasFornecedor += $despesa['valor'];
				}
	
				$somaQtd += $fluxoLoja['soma'];
			
			}
			
			$relatorios .= "</table>";
			
			$post2['id_usuario'] = $_POST['id_usuario'];
			$post2['data_inicial'] = $_POST['data_inicial'];
			$post2['data_final'] = $_POST['data_final'];
			$post2['tipo_relatorio'] = $_POST['tipo_relatorio'];
			$post2['id_empresa'] = $_POST['id_empresa'];
			$post2['fl'] = $_POST['rd'];
			
			$arrFluxoLoja = $dbFluxoLoja->relatorioFluxoLoja($post2);
			
			$relatorios .= "<div style='margin-left:10px; margin-bottom:10px;'><b>Relatório de Fluxo de Loja: ".$nomeVendedor."</b></div>";

			$relatorios .= "<table class='table'>
						   <tr>
							<th class='cabeca'>Descri&ccedil;&atilde;o</th>
							<th class='cabeca'>Quantidade</th>
						  </tr>";
			$count = 1;
			$subtotal="";

			foreach($arrFluxoLoja as $key=>$fluxoLoja){
				
				if($arrFluxoLoja[$key+1]['id_origem_cliente'] != $fluxoLoja['id_origem_cliente']){
	
					$rowspan = "<tr>
								<td class='tds' rowspan='".$count."'>".$fluxoLoja['descricao']."</td>
								<td class='tds'>".$fluxoLoja['soma']."</td>
							  </tr>";
					
					$somaQtd += $fluxoLoja['soma'];
		
					$relatorios .= $rowspan.$relatorio.$subtotal;
					
					$count = 1;
					$relatorio ="";
					$somaQtd = 0;
					
				}else{
					$count++;
					$relatorio .="<tr>
									<td class='tds' rowspan='".$count."'>".$fluxoLoja['descricao']."</td>
									<td class='tds'>".$fluxoLoja['soma']."</td>
								</tr>";
						
					$somaDespesasFornecedor += $despesa['valor'];
				}
	
				$somaQtd += $fluxoLoja['soma'];
			
			}
			
			$relatorios .= "</table>";
			
			$this->view->relatorio = $relatorios;
			$this->view->idFornecedor = $_POST['id_fornecedor'];
			$this->view->ramoAtividade = $_POST['ramo_atividade'];
			
		}
	
	}
	
	public function gerarXlsAction(){
	
		$this->view->relatorio = $_POST['relatorio'];
		$this->view->dataInicial = $_POST['data_inicial'];
		$this->view->dataFinal = $_POST['data_final'];
		$this->view->tipoRelatorio =  $_POST['tipo_relatorio'];
	
	}

}

?>
