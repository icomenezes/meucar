<?php

header("Content-Type: text/html; charset=UTF-8", true);

class CarrosUsadosController extends Zend_Controller_Action {

   public function init() {

      $this->view->titulo = "";

      Zend_Session::start();

      header("Location: " . URL . "index/adm/");
   }

   public function buscaAvancadaAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('site');
	  
	  $this->view->title = "Busca Detalhada";
	  
	  
      if ($this->getRequest()->isPost()) {
         
      }
   }
   
	public function jundiaiAction(){

		$layout = $this->_helper->layout();
		$layout->setLayout('site');

		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbPropagandasSite = new Application_Model_DbTable_PropagandasSite();

		$arrVeiculos = $dbVeiculos->getVeiculosUsadosIndex();
		$arrVeiculosNovos = $dbVeiculos->getVeiculosNovosIndex();
		$arrPropagandasR = $dbPropagandasSite->getImagensRetrato();

		$this->view->arrVeiculos = $arrVeiculos;
		$this->view->arrVeiculosNovos = $arrVeiculosNovos;
		$this->view->arrPropagandasR = $arrPropagandasR;
		  
	}

   public function simuladorAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('site');

	  $this->view->title = "Simulador de Financiamento";
	  
      if ($this->getRequest()->isPost()) {

         $dbSimulador = new Application_Model_DbTable_Simulador();
         $dbVeiculos = new Application_Model_DbTable_Veiculos();
         $dbPropagandasSite = new Application_Model_DbTable_PropagandasSite();
         $dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();

         if ($_POST['ano_modelo'] < @date("Y") - 7) {

            $dados['antes_depois'] = "0";
			
         } else {

            $dados['antes_depois'] = "1";
         }

         $arrDadosSimulador = $dbSimulador->getSimuladorAntesDepois($dados);

         $coeficiente = $arrDadosSimulador[0]['meses_' . $_POST['numero_parcelas']];
         $parcela = str_replace(",", ".", $_POST['parcelas']);
         $entrada = str_replace(",", ".", $_POST['entrada']);

         $resultado = ((($parcela / $coeficiente) - 800) + $entrada);

         $buscaVeiculos['valor'] = $resultado;

         $arrVeiculos = $dbVeiculos->_getSite($buscaVeiculos);
         $arrPropagandasP = $dbPropagandasSite->getImagensPaisagem();

         foreach ($arrVeiculos as $key => $valor) {

            $arrFotosVeiculos = $dbFotosVeiculos->getFotosVeiculoSelecionado($arrVeiculos[$key]['id']);

            foreach ($arrFotosVeiculos as $fotos) {

               if ($fotos['capa'] == 1) {

                  $arrVeiculos[$key]['path'] = $fotos['path'];
               } elseif ($arrVeiculos[$key]['path'] == "") {

                  $arrVeiculos[$key]['path'] = $fotos['path'];
               }
            }

            if ($arrVeiculos[$key]['descricao_site'] != "") {

               $arrVeiculos[$key]['modelo'] = $arrVeiculos[$key]['descricao_site'];
            }
         }

         if ($_POST['receber_email'] != "" && $_POST['email'] != "" && $_POST['nome'] != "") {

            $this->emailOfertas($_POST['nome'], $_POST['email']);
         }

         $this->view->veiculos = $arrVeiculos;
         $this->view->arrPropagandasP = $arrPropagandasP;

         $this->view->resultado = $resultado;
         $this->view->entrada = $entrada;
         $this->view->parcela = $parcela;
         $this->view->numeroParcelas = $_POST['numero_parcelas'];
         $this->view->resposta = 1;
      }
   }

   public function contatoAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('no-layout');

      if ($this->getRequest()->isPost()) {

         $assunto = "Contato feito pelo site - Enviado por " . $_POST['nome'];
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
						<tr><td colspan='2'><div style='background-color: #CCCCCC;'>" . $_POST['mensagem'] . "</div><br></td></tr>
						<tr><td colspan='2' style='height:10px;'></td></tr>
						<tr><td colspan='2' style='height:10px;'>Cliente: " . $_POST['nome'] . "</td></tr>
						<tr><td colspan='2' style='height:10px;'>E-mail: " . $_POST['email'] . "</td></tr>
					</table>
				</body>
			</html>";

         $transport = Internas_MailConfig::getTransport(Internas_MailConfig::CONTA_CONTATO);

         $mail = new Zend_Mail("UTF-8");
         $mail->setFrom(Internas_MailConfig::getFrom(Internas_MailConfig::CONTA_CONTATO));
         $mail->addTo(Internas_MailConfig::getFrom(Internas_MailConfig::CONTA_CONTATO));
         $mail->setBodyHtml($corpo);
         $mail->setSubject($assunto);


         try {

            if ($attach) {

               $mail->createAttachment(file_get_contents($attach), 'text/plain', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $attach);
            }

            if ($mail->send($transport)) {

               $this->view->enviado = "Sucesso";
            }
         } catch (Exception $e) {

            //echo $e->getMessage();
         }
      }
   }

   public function anuncioGratisAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('site');
   }

   public function termosUsoAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('no-layout');
   }

   public function avaliarCarroAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('site');

      $dbModelos = new Application_Model_DbTable_Modelos();
      $dbEmpresas = new Application_Model_DbTable_Empresas();

      $this->view->arrMarcas = $dbModelos->getMarcas();
      $this->view->arrCidades = $dbEmpresas->getCidadesEmpresasAnunciantes();
	  
	  $this->view->title = "Avalie seu Carro";

      if ($this->getRequest()->isPost()) {

         $dbModelo = new Application_Model_DbTable_Modelo();
         $dbVeiculos = new Application_Model_DbTable_Veiculos();

         $arrAnoModelo = $dbModelo->getAnoModeloValor($_POST['ano_modelo']);
         $arrVeiculos = $dbVeiculos->getQtdVeiculos($_POST['ano_modelo']);
         $arrVeiculosMenor = $dbVeiculos->getVeiculosMenorValor($_POST['ano_modelo']);
         $arrVeiculosMaior = $dbVeiculos->getVeiculosMaiorValor($_POST['ano_modelo']);

         //var_export($_POST);

         $valorMedio = "N&atilde;o h&aacute; ve&iacute;culos anunciados com esse modelo.";

         if ($arrVeiculosMaior[0]['valor_maximo'] != "" && $arrVeiculosMenor[0]['valor_minimo'] != "") {

            $valorMedio = "R$ " . money_format("%i", ($arrVeiculosMaior[0]['valor_maximo'] + $arrVeiculosMenor[0]['valor_minimo']) / 2);
         }

         if ($arrVeiculosMenor[0]['valor_minimo']) {

            $arrVeiculosMenor[0]['valor_minimo'] = "R$ " . money_format("%i", $arrVeiculosMenor[0]['valor_minimo']);
         } else {

            $arrVeiculosMenor[0]['valor_minimo'] = "N&atilde;o h&aacute; ve&iacute;culos anunciados com esse modelo.";
         }

         if ($arrVeiculosMaior[0]['valor_maximo']) {

            $arrVeiculosMaior[0]['valor_maximo'] = "R$ " . money_format("%i", $arrVeiculosMaior[0]['valor_maximo']);
         } else {

            $arrVeiculosMaior[0]['valor_maximo'] = "N&atilde;o h&aacute; ve&iacute;culos anunciados com esse modelo.";
         }

         if ($_POST['receber_email'] != "" && $_POST['email'] != "" && $_POST['nome'] != "") {

            $this->emailOfertas($_POST['nome'], $_POST['email']);
         }

         $this->view->valorMenor = $arrVeiculosMenor[0]['valor_minimo'];
         $this->view->valorMedio = $valorMedio;
         $this->view->valorMaior = $arrVeiculosMaior[0]['valor_maximo'];
         $this->view->qtdVeiculos = $arrVeiculos[0]['qtd_veiculos'];
         $this->view->precoFipe = $arrAnoModelo[0]['preco'];
         $this->view->descricaoMarca = $_POST['descricao_marca'];
         $this->view->descricaoModelo = $_POST['descricao_modelo'];
         $this->view->descricaoAnoModelo = $_POST['descricao_ano_modelo'];
         $this->view->resposta = 1;
      }
   }

   public function cadastroOfertasAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('site');

      if ($this->getRequest()->isPost()) {

         $this->emailOfertas($_POST['nome'], $_POST['email']);
      }
   }

   public function cadastroSiteAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('site');

      if ($this->getRequest()->isPost()) {

         $dbEmpresas = new Application_Model_DbTable_Empresas();

         $dadosEmpresa['razao_social'] = $_POST['nome'];
         $dadosEmpresa['nome_fantasia'] = $_POST['nome'];
         $dadosEmpresa['cnpj'] = $_POST['cpf'];
         $dadosEmpresa['endereco'] = $_POST['endereco'];
         $dadosEmpresa['cidade'] = $_POST['cidade'];
         $dadosEmpresa['estado'] = $_POST['estado'];
         $dadosEmpresa['cep'] = $_POST['cep'];
         $dadosEmpresa['bairro'] = $_POST['bairro'];
         $dadosEmpresa['tel1'] = $_POST['telefone'];
         $dadosEmpresa['tel2'] = $_POST['celular'];
         $dadosEmpresa['email'] = $_POST['email'];
         $dadosEmpresa['sistema_site'] = 1;
         $dadosEmpresa['ativo'] = 1;

         $idEmpresa = $dbEmpresas->insert($dadosEmpresa);

         if ($idEmpresa) {

            $dadosUsuario['id_perfil'] = 10;
            $dadosUsuario['id_empresa'] = $idEmpresa;
            $dadosUsuario['nome'] = $_POST['nome'];
            $dadosUsuario['login'] = $_POST['login'];
            $dadosUsuario['senha'] = md5($_POST['senha']);
            $dadosUsuario['ativo'] = 0;
            $dadosUsuario['cpf'] = $_POST['cpf'];
            $dadosUsuario['rg'] = $_POST['rg'];
            $dadosUsuario['endereco'] = $_POST['endereco'];
            $dadosUsuario['bairro'] = $_POST['bairro'];
            $dadosUsuario['cidade'] = $_POST['cidade'];
            $dadosUsuario['estado'] = $_POST['estado'];
            $dadosUsuario['telefone'] = $_POST['telefone'];
            $dadosUsuario['celular'] = $_POST['celular'];
            $dadosUsuario['email'] = $_POST['email'];
            $dadosUsuario['data_contratacao'] = @date("Y-m-d");
            $dadosUsuario['newslatter'] = $_POST['newslatter'];

            $dbUsuarios = new Application_Model_DbTable_Usuarios();

            if ($dbUsuarios->insert($dadosUsuario)) {

               $this->_helper->redirector->gotoUrl("/index/adm");
            }
         }

         //var_export($_POST);
      }
   }

   public function mensagemPagueSeguroAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('site');
   }

   public function ajaxAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('no-layout');

      if ($this->_getParam('fn') == "envia_interesse"){

         $dados['id_empresa'] = $this->_getParam('id_empresa');
         $dados['id_veiculo'] = $this->_getParam('id_veiculo');
         $dados['nome_cliente'] = $this->_getParam('nome');
         $dados['email'] = $this->_getParam('email');
         $dados['telefone'] = $this->_getParam('telefone');
         $dados['mensagem'] = $this->_getParam('mensagem');
         $dados['veiculo_troca'] = $this->_getParam('veiculo_troca');
         $dados['financiar'] = $this->_getParam('financiar');
         $dados['ofertas_email'] = $this->_getParam('oferta_email');

         $dbEmpresas = new Application_Model_DbTable_Empresas();
         $dbVeiculos = new Application_Model_DbTable_Veiculos();

         $arrEmpresa = $dbEmpresas->getEmpresa($dados['id_empresa']);

         $arr['id'] = $dados['id_veiculo'];

         $arrVeiculo = $dbVeiculos->getVeiculoPorId($arr);

         if ($arrVeiculo[0]['descricao_site']) {

            $arrVeiculo[0]['modelo'] = $arrVeiculo[0]['descricao_site'];
         }

         if ($dados['veiculo_troca']) {

            $dados['veiculo_troca'] = "Sim";
         
		 } else {

            $dados['veiculo_troca'] = "N&atilde;o";
         }

         if ($dados['financiar']) {

            $dados['financiar'] = "Sim";
         } else {

            $dados['financiar'] = "N&atilde;o";
         }
		 
		 $assunto = "Proposta - Enviado por " . $dados['nome_cliente'] . " pelo site Meu Car";

        $destinatario = $arrEmpresa[0]['email'];
        //$destinatario = "gesieldiniz@terra.com.br";
		 
		if(current(explode(".",end(explode("@",$destinatario)))) == "terra"){

			    $corpo = "\r DADOS DO ANÚNCIO"
						."\r Veículo: http://sistemameucar.com.br/carros-usados/veiculo/id/" . $dados['id_veiculo']
						."\r Modelo: ".$arrVeiculo[0]['marca']." ".$arrVeiculo[0]['modelo']
						."\r Placa: ".$arrVeiculo[0]['placa']
						."\r Ano: ".$arrVeiculo[0]['ano_fabricacao']."/".$arrVeiculo[0]['ano_modelo']
						."\r Cor: ".$arrVeiculo[0]['cor']
						."\r Valor: R$ ".money_format("%i", $arrVeiculo[0]['valor_venda'])
						."\r\r DADOS DO INTERESSADO"
						."\r Nome: ".$dados['nome_cliente']
						."\r E-mail: ".$dados['email']
						."\r Telefone: ".$dados['telefone']
						."\r Mensagem: ".$dados['mensagem']
						."\r Usar Veículo em Troca: ".$dados['veiculo_troca']
						."\r Deseja Financiar: ".$dados['financiar'];
		
		}else{

			$corpo = "
			<html>
				<head>
					<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
					<title>MeuCar</title>
					<style>
						td{
							vertical-align:middle;
						}
						
						a img{
							width:200px;
							float:right;
						}
					</style>
				</head>
				<body>
					<table style='background-color:#E8E8E8; border:1px solid #CCCCCC; color:#666666; font:14px Arial, Helvetica, sans-serif;'>
						<tr><td colspan='2' style='border-bottom:solid 1px #CCCCCC; vertical-align:bottom; font-size:20px; height:50px;'>Dados do An&uacute;ncio</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Ve&iacute;culo:</b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'><a href='http://sistemameucar.com.br/carros-usados/veiculo/id/" . $dados['id_veiculo'] . "'>http://sistemameucar.com.br/carros-usados/veiculo/id/" . $dados['id_veiculo'] . "</a></td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Modelo: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $arrVeiculo[0]['marca'] . " " . $arrVeiculo[0]['modelo'] . "</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Placa: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $arrVeiculo[0]['placa'] . "</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Ano: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $arrVeiculo[0]['ano_fabricacao'] . "/" . $arrVeiculo[0]['ano_modelo'] . "</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Cor: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $arrVeiculo[0]['cor'] . "</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Valor: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>R$ " . money_format("%i", $arrVeiculo[0]['valor_venda']) . "</td></tr>
						<tr><td colspan='2' style='border-bottom:solid 1px #CCCCCC; vertical-align:bottom; font-size:20px; height:50px;'>Dados do Interessado</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Nome: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $dados['nome_cliente'] . "</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>E-mail: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $dados['email'] . " <-Usar este e-mail para resposta ao cliente.</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Telefone: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $dados['telefone'] . "</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Mensagem: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $dados['mensagem'] . "</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Usar Ve&iacute;culo em Troca: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $dados['veiculo_troca'] . "</td></tr>
						<tr><td style='border-bottom:solid 1px #CCCCCC; height:35px;'><b>Deseja Financiar: </b></td><td style='border-bottom:solid 1px #CCCCCC; height:25px;'>" . $dados['financiar'] . "</td></tr>
						<tr><td colspan='2' style=''><br><center><img style='width:150px;' src='http://sistemameucar.com.br/arquivos_site/images/Logo_Car.png'/><br><a href='http://sistemameucar.com.br/carros-usados'>www.sistemameucar.com.br</a></center><br></td></tr>
					</table>
				</body>
			</html>";
		
		}
		
			//var_export($this->enviaEmailInteresse($dados['email'], $dados['nome_cliente'], $destinatario, $assunto, $corpo));
			
         if ($this->enviaEmailInteresse($dados['email'], $dados['nome_cliente'], $destinatario, $assunto, $corpo)) {

            if ($dados['ofertas_email']) {

               $this->emailOfertas($dados['nome_cliente'], $dados['email']);
			   
            }

            $dbVisualizacoesEmpresaEstoque = new Application_Model_DbTable_VisualizacoesEmpresaEstoque();

            $arrDados['id_empresa'] = $this->_getParam('id_empresa');
            $arrDados['id_veiculo'] = $this->_getParam('id_veiculo');
            $arrDados['data'] = @date("Y-m-d");

            $arrVisualizacao = $dbVisualizacoesEmpresaEstoque->_get($arrDados);

            if ($arrVisualizacao[0]['id'] != "") {

               $arrVisualizacao[0]['qtd_interesse'] += 1;

               $dbVisualizacoesEmpresaEstoque->edt($arrVisualizacao[0]['id'], $arrVisualizacao[0]);
            } else {

               $arrDados['qtd_interesse'] = 1;

               $dbVisualizacoesEmpresaEstoque->add($arrDados);
            }

            echo "Sucesso";
         } else {

            echo "Erro";
         }
      } elseif ($this->_getParam('fn') == "visualizacao_telefone") {

         $dbVisualizacoesEmpresaEstoque = new Application_Model_DbTable_VisualizacoesEmpresaEstoque();

         $arrDados['id_empresa'] = $this->_getParam('id_empresa');
         $arrDados['id_veiculo'] = $this->_getParam('id_veiculo');
         $arrDados['data'] = @date("Y-m-d");

         $arrVisualizacao = $dbVisualizacoesEmpresaEstoque->_get($arrDados);

         if ($arrVisualizacao[0]['id'] != "") {

            $arrVisualizacao[0]['qtd_visualizacoes_telefone'] += 1;

            $dbVisualizacoesEmpresaEstoque->edt($arrVisualizacao[0]['id'], $arrVisualizacao[0]);
         } else {

            $arrDados['qtd_visualizacoes_telefone'] = 1;

            $dbVisualizacoesEmpresaEstoque->add($arrDados);
         }
      } elseif ($this->_getParam('fn') == "visualizacao_endereco") {

         $dbVisualizacoesEmpresaEstoque = new Application_Model_DbTable_VisualizacoesEmpresaEstoque();

         $arrDados['id_empresa'] = $this->_getParam('id_empresa');
         $arrDados['id_veiculo'] = $this->_getParam('id_veiculo');
         $arrDados['data'] = @date("Y-m-d");

         $arrVisualizacao = $dbVisualizacoesEmpresaEstoque->_get($arrDados);

         if ($arrVisualizacao[0]['id'] != "") {

            $arrVisualizacao[0]['qtd_visualizacoes_endereco'] += 1;

            $dbVisualizacoesEmpresaEstoque->edt($arrVisualizacao[0]['id'], $arrVisualizacao[0]);
         } else {

            $arrDados['qtd_visualizacoes_endereco'] = 1;

            $dbVisualizacoesEmpresaEstoque->add($arrDados);
         }
      } elseif ($this->_getParam('fn') == "visualizacao_estoque") {

         $dbVisualizacoesEmpresaEstoque = new Application_Model_DbTable_VisualizacoesEmpresaEstoque();

         $arrDados['id_empresa'] = $this->_getParam('id_empresa');
         $arrDados['id_veiculo'] = $this->_getParam('id_veiculo');
         $arrDados['data'] = @date("Y-m-d");

         $arrVisualizacao = $dbVisualizacoesEmpresaEstoque->_get($arrDados);

         if ($arrVisualizacao[0]['id'] != "") {

            $arrVisualizacao[0]['qtd_visualizacoes_estoque'] += 1;

            $dbVisualizacoesEmpresaEstoque->edt($arrVisualizacao[0]['id'], $arrVisualizacao[0]);
         } else {

            $arrDados['qtd_visualizacoes_estoque'] = 1;

            $dbVisualizacoesEmpresaEstoque->add($arrDados);
         }
      } elseif ($this->_getParam('fn') == "visualizacao") {

         $dbVisualizacoesEmpresaEstoque = new Application_Model_DbTable_VisualizacoesEmpresaEstoque();

         $arrDados['id_empresa'] = $this->_getParam('id_empresa');
         $arrDados['id_veiculo'] = $this->_getParam('id_veiculo');
         $arrDados['data'] = @date("Y-m-d");

         $arrVisualizacao = $dbVisualizacoesEmpresaEstoque->_get($arrDados);

         if ($arrVisualizacao[0]['id'] != "") {

            $arrVisualizacao[0]['qtd_visitas'] += 1;

            $dbVisualizacoesEmpresaEstoque->edt($arrVisualizacao[0]['id'], $arrVisualizacao[0]);
         } else {

            $arrDados['qtd_visitas'] = 1;

            $dbVisualizacoesEmpresaEstoque->add($arrDados);
         }
      } elseif ($this->_getParam('fn') == "busca_propaganda") {

         $dbPropagandasSite = new Application_Model_DbTable_PropagandasSite();
         $arrPropagandasP = $dbPropagandasSite->getImagensPaisagem();

         $maximo = rand(0, count($arrPropagandasP) - 1);

         echo $arrPropagandasP[$maximo]['path'] . "|" . $arrPropagandasP[$maximo]['link'];
		 
		 
      } elseif ($this->_getParam('fn') == "busca_veiculos") {
			
			
			
      }
   }

   public function indexTesteAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('site');

      session_destroy();


      $dbVeiculos = new Application_Model_DbTable_Veiculos();
      $dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
      $dbPropagandasSite = new Application_Model_DbTable_PropagandasSite();

      $arrVeiculos = $dbVeiculos->getVeiculosUsadosIndex();
      $arrAnunciosTres = $dbVeiculos->getUltimosTresAnuncios();
      $arrVeiculosNovos = $dbVeiculos->getVeiculosNovosIndex();
	  $arrPropagandasR = $dbPropagandasSite->getImagensRetrato();
	

      $this->view->arrVeiculos = $arrVeiculos;
      $this->view->arrVeiculosNovos = $arrVeiculosNovos;
      $this->view->arrPropagandasR = $arrPropagandasR;
	  $this->view->qtdAnuncios = $dbVeiculos->getCountVeiculosAnuncios();
	  $this->view->arrAnunciosTres =  $arrAnunciosTres;
	
		//var_export($arrPropagandasR);
	  
   }
   
   
   public function indexAction(){

      $layout = $this->_helper->layout();
      $layout->setLayout('site');

      session_destroy();

      $dbVeiculos = new Application_Model_DbTable_Veiculos();
      $dbEmpresas = new Application_Model_DbTable_Empresas();
      $dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
      $dbPropagandasSite = new Application_Model_DbTable_PropagandasSite();

      $arrVeiculos = $dbVeiculos->getVeiculosUsadosIndex();
      $arrEmpresas = $dbEmpresas->getEmpresasPorLogo();
      $arrAnunciosTres = $dbVeiculos->getUltimosTresAnuncios();
      $arrVeiculosNovos = $dbVeiculos->getVeiculosNovosIndex();
	  $arrPropagandasR = $dbPropagandasSite->getImagensRetrato();
	

      $this->view->arrVeiculos = $arrVeiculos;
      $this->view->arrVeiculosNovos = $arrVeiculosNovos;
      $this->view->arrEmpresas = $arrEmpresas;
      $this->view->arrPropagandasR = $arrPropagandasR;
	  $this->view->qtdAnuncios = $dbVeiculos->getCountVeiculosAnuncios();
	  $this->view->arrAnunciosTres =  $arrAnunciosTres;
	
		//var_export($arrPropagandasR);
	  
   }
   
   

   public function pag2Action() {

      $layout = $this->_helper->layout();
      $layout->setLayout('site');
   }
   /*
    public function sitemapAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('no-layout');
  
  }
*/
   public function mapaAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('no-layout');
   }

   public function buscaLojasAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('site');

      $dbEmpresas = new Application_Model_DbTable_Empresas();
	  
	  $this->view->title = "Busca Lojas - Jundia&iacute; e Regi&atilde;o";
	  

      if ($this->getRequest()->isPost()) {

         $this->view->lojas = $dbEmpresas->getEmpresasPorLetraNome($_POST);
      } else {

         $this->view->lojas = $dbEmpresas->getEmpresas();
      }
   }

	public function buscaVeiculosAction() {

		$layout = $this->_helper->layout();
		$layout->setLayout('site');

		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
		$dbPropagandasSite = new Application_Model_DbTable_PropagandasSite();

		$_POST['minimo_valor'] = str_replace(".", "", $_POST['minimo_valor']);
		$_POST['minimo_valor'] = str_replace(",", ".", $_POST['minimo_valor']);

		$_POST['maximo_valor'] = str_replace(".", "", $_POST['maximo_valor']);
		$_POST['maximo_valor'] = str_replace(",", ".", $_POST['maximo_valor']);

		$_POST['id_empresa'] = $this->_getParam('id-empresa');
		
		 $this->view->title = "Ve&iacute;culos - Jundia&iacute; e Regi&atilde;o";

		if ($this->_getParam('id-empresa')) {

			$this->view->arrEmpresa = $dbEmpresas->getEmpresa($this->_getParam('id-empresa'));
		
		}

		//var_export($_POST);
		
		if ($_POST['tipo_busca'] == 1) {

			foreach ($_POST as $key => $dados) {

				$arrOpcional = explode("_", $key);

				if ($arrOpcional[0] == "opcional") {

					$arrOpcionais[$arrOpcional[1]] = $arrOpcional[1];
				
				}
			}

			$arrVeiculo = $dbVeiculos->_getSiteAvancado($_POST);

			// var_export($arrVeiculo);
		 
			if ($arrOpcionais) {

				foreach ($arrOpcionais as $opcional) {

					foreach ($arrVeiculo as $key => $veiculos) {

						if (!$dbOpcionaisVeiculos->getOpcionalIdVeiculo($veiculos['id'], $opcional)) {

							unset($arrVeiculo[$key]);
						
						}
					
					}
				
				}
			
			}

			foreach ($arrVeiculo as $key => $veiculos) {

				$arrVeiculos[$key] = $veiculos;
			
			}
			
		} else {
			
			$stringUrl = end(explode("/",$_SERVER ['REQUEST_URI']));

			$stringUrl = strtolower($stringUrl);
			

			if(!$_POST['marca']){
				
				if($stringUrl == "volkswagen"){
				
					$stringUrl = "VW - VolksWagen";
				
				}elseif($stringUrl == "chevrolet"){
				
					$stringUrl = "GM - Chevrolet";
				
				
				}
				
				$_POST['marca'] = $stringUrl;
				$arrVeiculos = $dbVeiculos->_getSite($_POST);
				unset($_POST['marca']);
			
			}
			
			if(!$arrVeiculos && !$_POST['str_veiculo']){

				$_POST['str_veiculo'] = $stringUrl;
				$arrVeiculos = $dbVeiculos->_getSite($_POST);
				unset($_POST['str_veiculo']);
			
			}
			
			if(!$arrVeiculos){

				$arrVeiculos = $dbVeiculos->_getSite($_POST);
			
			}

		}
		
		
		$arrPropagandasP = $dbPropagandasSite->getImagensPaisagem();

		if($arrVeiculos){
		
			foreach ($arrVeiculos as $key => $valor) {

				$arrFotosVeiculos = $dbFotosVeiculos->getFotosVeiculoSelecionado($arrVeiculos[$key]['id']);

				foreach ($arrFotosVeiculos as $fotos) {

					if ($fotos['capa'] == 1) {

						$arrVeiculos[$key]['path'] = $fotos['path'];
						
					} elseif ($arrVeiculos[$key]['path'] == "") {

						$arrVeiculos[$key]['path'] = $fotos['path'];
					
					}
				
				}

				if ($arrVeiculos[$key]['descricao_site'] != "") {

					$arrVeiculos[$key]['modelo'] = $arrVeiculos[$key]['descricao_site'];
				
				}
			
			}
		
		}

		$this->view->veiculos = $arrVeiculos;
		$this->view->arrPropagandasP = $arrPropagandasP;
	  
		// var_export($arrVeiculos);
	}
	
	
	public function vendidoAction() {

		$layout = $this->_helper->layout();
		$layout->setLayout('site');
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();

		$arrVeiculo = $dbVeiculos->getVeiculoEstoque(end(explode("/",$_SERVER ['REQUEST_URI'])));
		
		$arrVeiculos = $dbVeiculos->_getVeiculosParecidos(current(explode(" ",$arrVeiculo[0]['modelo'])));

		if($arrVeiculos){
		
			foreach ($arrVeiculos as $key => $valor) {

				$arrFotosVeiculos = $dbFotosVeiculos->getFotosVeiculoSelecionado($arrVeiculos[$key]['id']);

				foreach ($arrFotosVeiculos as $fotos) {

					if ($fotos['capa'] == 1) {

						$arrVeiculos[$key]['path'] = $fotos['path'];
						
					} elseif ($arrVeiculos[$key]['path'] == "") {

						$arrVeiculos[$key]['path'] = $fotos['path'];
					
					}
				
				}

				if ($arrVeiculos[$key]['descricao_site'] != "") {

					$arrVeiculos[$key]['modelo'] = $arrVeiculos[$key]['descricao_site'];
				
				}
			
			}
		
		}

		$this->view->veiculos = $arrVeiculos;
		$this->view->modelo = current(explode(" ",$arrVeiculo[0]['modelo']));

	}
	

   public function veiculoAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('site');

      $dbVeiculos = new Application_Model_DbTable_Veiculos();
      $dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
      $dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
      $dbPropagandasSite = new Application_Model_DbTable_PropagandasSite();

      $arr['id'] = ($this->_getParam('id')) ? $this->_getParam('id') : '1';

      if ($this->_getParam('sistema_site')) {

         $arr['sistema_site'] = $this->_getParam('sistema_site');
      } else {

         $arr['sistema_site'] = 1;
      }

      $arrVeiculos = $dbVeiculos->_getSite($arr);

      if(count($arrVeiculos) <= 0)
         return true;
      
      $arrFotosVeiculos = $dbFotosVeiculos->getFotosVeiculoSelecionado($arrVeiculos[0]['id']);
      $arrOpcionaisVeiculos = $dbOpcionaisVeiculos->getVeiculosOpcionais($arrVeiculos[0]['id']);

      $i = 0;

      if ($arrFotosVeiculos) {

         foreach ($arrFotosVeiculos as $fotos) {

            if ($fotos['capa'] == 1) {

               $arrVeiculos[0]['capa'] = $fotos['path'];
            } else {
               $i++;
               $arrVeiculos[0]['path_' . $i] = $fotos['path'];
            }
         }
      } else {

         $arrVeiculos[0]['capa'] = "arquivos_site/images/preparacao_final.jpg";

         for ($e = 1; $e < 5; $e++) {

            $arrVeiculos[0]['path_' . $e] = "arquivos_site/images/preparacao_final.jpg";
         }
      }

      $arrVeiculosParecidos = $dbVeiculos->getVeiculosPorValor($arrVeiculos[0]['valor_venda']);
      $arrPropagandasR = $dbPropagandasSite->getImagensRetrato();

      $this->view->veiculo = $arrVeiculos[0];
      $this->view->arrOpcionais = $arrOpcionaisVeiculos;
      $this->view->arrVeiculosParecidos = $arrVeiculosParecidos;
      $this->view->arrPropagandasR = $arrPropagandasR;

      //var_export($arrVeiculosParecidos);
   }
   
   

   public function enviarAmigoAction() {

      $layout = $this->_helper->layout();
      $layout->setLayout('no-layout');

      $this->view->idCarro = $this->_getParam('id_carro');

      if ($this->getRequest()->isPost()) {

         $dbEnviaEmail = new Application_Model_DbTable_EnviaEmail();

         $destinatario = $_POST['email_amigo'];
         $remetente = $_POST['email_remetente'];
         $assunto = "Mensagem MeuCar - Enviado por " . $_POST['nome_remetente'];
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
						<tr><td style='font-size:20px;'><br>Ol&aacute; " . $_POST['nome_amigo'] . ".</td><td><!--<a href='http://sistemameucar.com.br/carros-usados'><img src='http://sistemameucar.com.br/arquivos_site/images/Logo_Car.png'/></a>--></td></tr>
						<tr><td colspan='2' style='height:10px;'></td></tr>
						<tr><td colspan='2'>Seu amigo " . $_POST['nome_remetente'] . ", visitou o site 'MeuCar' e lhe enviou esta mensagem abaixo que &eacute; de seu interesse.<br><br></td></tr>
						<tr><td colspan='2'><div style='background-color: #CCCCCC;'>" . $_POST['comentario'] . "</div><br></td></tr>
						<tr><td colspan='2'>Clique no link abaixo para ser direcionado ao an&uacute;cio enviado pelo " . $_POST['nome_remetente'] . ".</td></tr>
						<tr><td colspan='2'><a href='" . $_POST['link'] . "'>" . $_POST['link'] . "</a><br><br></td></tr>
						<tr><td colspan='2' style='height:10px;'></td></tr>
						<tr><td colspan='2' style=''><center><img style='width:150px;' src='http://sistemameucar.com.br/arquivos_site/images/Logo_Car.png' /><br><a href='http://sistemameucar.com.br/carros-usados'>www.sistemameucar.com.br</a></center><br></td></tr>
					</table>
				</body>
			</html>";

         if ($this->enviaEmailAmigo($destinatario, $remetente, $assunto, $corpo)) {

            $this->view->corpo = "sucesso";
         } else {

            $this->view->corpo = "erro";
         }
      }
   }

   public function anuncioIrregularAction() {

      $dbVeiculos = new Application_Model_DbTable_Veiculos();

      $layout = $this->_helper->layout();
      $layout->setLayout('no-layout');

      $arr['id'] = $this->_getParam('id_carro');

      $arrVeiculos = $dbVeiculos->_getSite($arr);

      $this->view->veiculo = $arrVeiculos[0];

      if ($this->getRequest()->isPost()) {

         $dbEmpresas = new Application_Model_DbTable_Empresas();

         $arrEmpresa = $dbEmpresas->getEmpresa($_POST['id_empresa']);

         $destinatario = $arrEmpresa[0]['email'];

         $assunto = "Irregularidade no anúncio site Meu Car - Enviado por " . $_POST['nome'];
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
						<tr><td style='font-size:20px;'><br>Ol&aacute;.</td><td></td></tr>
						<tr><td colspan='2' style='height:10px;'></td></tr>
						<tr><td colspan='2'>O cliente " . $_POST['nome'] . ", visitou o site 'MeuCar' e est&aacute; nos informando uma irregularidade no an&uacute;ncio.<br><br></td></tr>
						<tr><td colspan='2'><div style='background-color: #CCCCCC;'>" . $_POST['mensagem'] . "</div><br></td></tr>
						<tr><td colspan='2'>Clique no link abaixo para ser direcionado ao an&uacute;ncio.</td></tr>
						<tr><td colspan='2'><a href='http://sistemameucar.com.br" . $_POST['path'] . "'>http://sistemameucar.com.br" . $_POST['path'] . "</a><br><br></td></tr>
						<tr><td colspan='2' style='height:10px;'></td></tr>
						<tr><td colspan='2' style='height:10px;'>Cliente: " . $_POST['nome'] . ".</td></tr>
						<tr><td colspan='2' style='height:10px;'>E-mail: " . $_POST['email'] . ".</td></tr>
						<tr><td colspan='2' style=''><center><img style='width:150px;' src='http://sistemameucar.com.br/arquivos_site/images/Logo_Car.png' /><br><a href='http://sistemameucar.com.br/carros-usados'>www.sistemameucar.com.br</a></center><br></td></tr>
					</table>
				</body>
			</html>";

         if ($this->enviaEmailIrregularidade($_POST['email'], $destinatario, $assunto, $corpo, $attach = false)) {

            $this->view->enviado = "Sucesso";
         }
      }
   }

   public function carrosUsadosAction() {

      $dbVeiculos = new Application_Model_DbTable_Veiculos();

      $layout = $this->_helper->layout();
      $layout->setLayout('site');
   }

   private function enviaEmailAmigo($para, $de, $assunto, $body, $attach = false) {

      $transport = Internas_MailConfig::getTransport(Internas_MailConfig::CONTA_SISTEMA);

      $mail = new Zend_Mail();
      $mail->setFrom(Internas_MailConfig::getFrom(Internas_MailConfig::CONTA_SISTEMA));
      $mail->addTo($para);
      $mail->setBodyHtml($body);
      $mail->setSubject($assunto);


      try {

         if ($attach) {

            $mail->createAttachment(file_get_contents($attach), 'text/plain', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $attach);
         }

         return $mail->send($transport);
      } catch (Exception $e) {

         //echo $e->getMessage();
      }
   }

	private function enviaEmailInteresse($emailCliente, $nomeCliente, $para, $assunto, $body, $attach = false){

      $transport = Internas_MailConfig::getTransport(Internas_MailConfig::CONTA_PROPOSTA);

      $mail = new Zend_Mail('UTF-8');
	  $mail->setBodyHtml($body);
      $mail->setFrom(Internas_MailConfig::getFrom(Internas_MailConfig::CONTA_PROPOSTA));
	  $mail->setReplyTo($emailCliente, $nomeCliente);
      $mail->addTo($para);
      $mail->addBcc(Internas_MailConfig::getFrom(Internas_MailConfig::CONTA_PROPOSTA));
      $mail->setSubject($assunto);
	   

      try {

         if ($attach) {

            $mail->createAttachment(file_get_contents($attach), 'text/plain', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $attach);
         
		 }

         return $mail->send($transport);
      } catch (Exception $e) {

       //echo $e->getMessage();
      }
	}

   private function enviaEmailIrregularidade($emailCliente, $para, $assunto, $body, $attach = false) {

      $transport = Internas_MailConfig::getTransport(Internas_MailConfig::CONTA_SUPORTE);

      $mail = new Zend_Mail("UTF-8");
      $mail->setFrom(Internas_MailConfig::getFrom(Internas_MailConfig::CONTA_SUPORTE));
      $mail->addTo($para);
	  $mail->setReplyTo($emailCliente);
      $mail->addCc(Internas_MailConfig::getFrom(Internas_MailConfig::CONTA_SUPORTE));
      $mail->setBodyHtml($body);
      $mail->setSubject($assunto);


      try {

         if ($attach) {

            $mail->createAttachment(file_get_contents($attach), 'text/plain', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $attach);
         }

         return $mail->send($transport);
      } catch (Exception $e) {

         //echo $e->getMessage();
      }
   }

   private function emailOfertas($nome, $email, $attach = false) {

      $body = "<html>
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
						<tr><td colspan='2' style='height:10px;'>O cliente " . $nome . ", visitou o site Meu Car e deseja receber ofertas.</td></tr>
						<tr><td colspan='2' style='height:10px;'></td></tr>
						<tr><td colspan='2' style='height:10px;'>Cliente: " . $nome . "</td></tr>
						<tr><td colspan='2' style='height:10px;'>E-mail: " . $email . "</td></tr>
					</table>
				</body>
			</html>";

      $config = array(
          'auth' => 'login',
          'username' => 'suporte@sistemameucar.com.br',
          'password' => 'g010502g',
          'port' => '587'
      );

      $transport = new Zend_Mail_Transport_Smtp('smtp.sistemameucar.com.br', $config);

      $mail = new Zend_Mail("UTF-8");
      $mail->setFrom('suporte@sistemameucar.com.br');
      $mail->addTo('ofertas@sistemameucar.com.br');
      $mail->setBodyHtml($body);
      $mail->setSubject('Cliente deseja receber ofertas');


      try {

         if ($attach) {

            $mail->createAttachment(file_get_contents($attach), 'text/plain', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $attach);
         }

         return $mail->send($transport);
      } catch (Exception $e) {

         echo $e->getMessage();
      }
   
   }

}

?>
