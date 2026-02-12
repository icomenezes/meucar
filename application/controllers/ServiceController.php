<?php
header("Content-Type: text/html; charset=UTF-8",true);

class ServiceController extends Zend_Controller_Action{

	public function init(){

		$this->view->titulo = "Web Service";

		Zend_Session::start();

	}
	
	
	public function addAppFotosExistenteAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();

		$arrVeiculo = $dbVeiculos->getVeiculoEstoque($_POST['id_veiculo']);
		
		if($arrVeiculo[0]['id'] != ""){

			if($_FILES['file']['tmp_name'] != ""){

				if(!file_exists(("fotos_veiculos/".$arrVeiculo[0]['id_empresa']))){
	
					$extensao = strtolower(end(explode(".",$_FILES['file']['name'])));
					mkdir("fotos_veiculos/".$arrVeiculo[0]['id_empresa']);
					chmod("fotos_veiculos/".$arrVeiculo[0]['id_empresa'], 0755);
					$novoNome = "fotos_veiculos/".$arrVeiculo[0]['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$arrVeiculo[0]['descricao_site']))."-".$arrVeiculo[0]['ano_fabricacao']."-".@date("his").".".$extensao;

				}else{

					$extensao = strtolower(end(explode(".",$_FILES['file']['name'])));
					$novoNome = "fotos_veiculos/".$arrVeiculo[0]['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$arrVeiculo[0]['descricao_site']))."-".$arrVeiculo[0]['ano_fabricacao']."-".@date("his").".".$extensao;

				}
				
				/////////////////REDIMENCIONA IMAGEM///////////////////////
				$input_image = $_FILES['file']['tmp_name'];
				$size = getimagesize( $input_image );
				$thumb_width = "780";
				$thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );
				$thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );
				$src_img = ImageCreateFromJPEG( $input_image );
				ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );
				$copied = ImageJPEG( $thumbnail, $novoNome);
				chmod($novoNome, 0755);
				ImageDestroy( $thumbnail );


			}

			if($copied){

				$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();

				$arrVeiculo[0]['id'] = str_replace("#","",$arrVeiculo[0]['id']);
				
				$dadosFotos['id_veiculo'] = $arrVeiculo[0]['id'];
				$dadosFotos['path'] = $novoNome;
				$dadosFotos['capa'] = $_POST['capa'];

				$dbFotosVeiculos->add($dadosFotos);

			}else{

				echo "Não foi possivel salvar as demais fotos!";
				
			}

		}else{
			
			echo "Veículo não encontrado!";
		
		}

	}
	
	
	public function addAppFotosAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
			
		$arrVeiculo = $dbVeiculos->getLastIdEmpresa($_POST['id_empresa']);
		
		if($arrVeiculo[0]['id'] != ""){

			if($_FILES['file']['tmp_name'] != ""){

				if(!file_exists(("fotos_veiculos/".$_POST['id_empresa']))){
	
					$extensao = strtolower(end(explode(".",$_FILES['file']['name'])));
					mkdir("fotos_veiculos/".$_POST['id_empresa']);
					chmod("fotos_veiculos/".$_POST['id_empresa'], 0755);
					$novoNome = "fotos_veiculos/".$_POST['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$_POST['descricao_site']))."-".$_POST['ano_fabricacao']."-".@date("his").".".$extensao;

				}else{

					$extensao = strtolower(end(explode(".",$_FILES['file']['name'])));
					$novoNome = "fotos_veiculos/".$_POST['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$_POST['descricao_site']))."-".$_POST['ano_fabricacao']."-".@date("his").".".$extensao;

				}
				
				/////////////////REDIMENCIONA IMAGEM///////////////////////
				$input_image = $_FILES['file']['tmp_name'];
				$size = getimagesize( $input_image );
				$thumb_width = "780";
				$thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );
				$thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );
				$src_img = ImageCreateFromJPEG( $input_image );
				ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );
				$copied = ImageJPEG( $thumbnail, $novoNome);
				chmod($novoNome, 0755);
				ImageDestroy( $thumbnail );


			}

			if($copied){

				$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();

				$_POST['id_veiculo'] = str_replace("#","",$_POST['id_veiculo']);
				
				$dadosFotos['id_veiculo'] = $arrVeiculo[0]['id'];
				$dadosFotos['path'] = $novoNome;
				$dadosFotos['capa'] = 0;

				$dbFotosVeiculos->add($dadosFotos);
				
				$dbFotosVeiculos->closeConnection();
				
				var_export($dadosFotos);

			}else{

				echo "Não foi possivel salvar as demais fotos!";
				
			}

		}else{
			
			echo "Veículo não encontrado!";
		
		}

	}
	

	public function addAppAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		if($_POST['id_empresa'] != ""){
		
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
		
			$arrPost = $_POST;
			
			
			$_POST['placa'] = strtoupper(substr($_POST['placa'], 0, 3)."-". substr($_POST['placa'], -4));
			$_POST['valor_venda'] = str_replace(",",".",str_replace(".","",$_POST['valor_venda']));
			$_POST['valor_aquisicao'] = str_replace(",",".",str_replace(".","",$_POST['valor_compra']));
			
			unset($_POST['opcionais']);
			unset($_POST['str_modelo']);
			unset($_POST['valor_compra']);
			
			$_POST['ativo'] = 1;
			$_POST['data_aquisicao'] = @date("Y-m-d");
			$_POST['hora_alteracao'] = @date("Y-m-d H:i:s");
			$_POST['exibir_site_estoque'] = 3;
			$_POST['exibir_km'] = 0;
			$_POST['descricao_site'] = mb_convert_encoding($_POST['descricao_site'], 'UTF-8', 'ISO-8859-1');
			$_POST['origem'] = "Compra";
		
			if($_POST['valor_venda'] == "" || $_POST['valor_venda'] < 1000){
				
				$_POST['exibir_valor_site'] = 0;
			
			}else{
				
				$_POST['exibir_valor_site'] = 1;
			
			}

			$_POST['id_empresa'] = str_replace("#", "", $_POST['id_empresa']);
			
			$idVeiculo = $dbVeiculos->add($_POST);
			
			if($idVeiculo){

				$dbCheckList = new Application_Model_DbTable_CheckList();
				
				$ChDados['id_veiculo'] = $idVeiculo;
				$ChDados['quitado_leasing'] = 0;
				$ChDados['pf_pj'] = 0;
				$ChDados['gnv'] = 0;
				$ChDados['doc_gnv'] = 0;
				
				$dbCheckList->add($ChDados);
				
				if($_POST['opcionais']){

					$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();

					$arrOpcionais = explode(",", $_POST['opcionais']);
					
					foreach($arrOpcionais as $opcionais){
					
						$arrDadosOpc['id_veiculo'] = $idVeiculo;
						$arrDadosOpc['id_opcional'] = $opcionais;
					
						$dbOpcionaisVeiculos->add($arrDadosOpc);
					
					}
				
				}
		
				$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();
				
				$arr = explode("|",substr($arrPost['opcionais'], 0, -1));
				
				foreach($arr as $opcional){
					
					$dbOpcionaisVeiculos->add(array("id_veiculo"=>$idVeiculo, "id_opcional"=>end(explode("_",$opcional))));
				
				}
				
				
				
				if($_FILES['foto_capa']['tmp_name'] != ""){
					
					if(!file_exists(("fotos_veiculos/".$_POST['id_empresa']))){
	
						$extensao = strtolower(end(explode(".",$_FILES['foto_capa']['name'])));
						mkdir("fotos_veiculos/".$_POST['id_empresa']);
						chmod("fotos_veiculos/".$_POST['id_empresa'], 0755);
						$novoNome = "fotos_veiculos/".$_POST['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$_POST['descricao_site']))."-".$_POST['ano_fabricacao']."-".@date("his").".".$extensao;

					}else{
						
						$extensao = strtolower(end(explode(".",$_FILES['foto_capa']['name'])));
						$novoNome = "fotos_veiculos/".$_POST['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$_POST['descricao_site']))."-".$_POST['ano_fabricacao']."-".@date("his").".".$extensao;

					}
				
					/////////////////REDIMENCIONA IMAGEM///////////////////////
					$input_image = $_FILES['foto_capa']['tmp_name'];
					$size = getimagesize( $input_image );
					$thumb_width = "780";
					$thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );
					$thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );
					$src_img = ImageCreateFromJPEG( $input_image );
					ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );
					$copied = ImageJPEG( $thumbnail, $novoNome);
					chmod($novoNome, 0755);
					ImageDestroy( $thumbnail );


				}

				if($copied){
					
					$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
					
					$dadosFotos['id_veiculo'] = $idVeiculo;
					$dadosFotos['path'] = $novoNome;
					$dadosFotos['capa'] = 1;

					$dbFotosVeiculos->add($dadosFotos);

				}

				echo $idVeiculo;

			}
			
			$dbVeiculos->closeConnection();
			$dbFotosVeiculos->closeConnection();

		}

	}
	
	public function androidAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		$nomeFoto = basename( $_FILES['uploaded_file']['name']);
	
		if($nomeFoto){
		
			$idEmpresa = end(explode("_",current(explode(".",$nomeFoto))));
			
			$idVeiculoApp = current(explode("_",current(explode(".",$nomeFoto))));
			
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
			
			$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();
			
			if($idVeiculoApp == "meucar"){
			
				$arrVeiculo = $dbVeiculos->getLastIdEmpresa($idEmpresa);
				
			}else{
				
				$arrVeiculo = $dbVeiculos->getVeiculoEstoque($idVeiculoApp);
			
			}
		
			if($arrVeiculo[0]['descricao_site']){
			
				$arrVeiculo[0]['modelo'] = $arrVeiculo[0]['descricao_site'];
			
			}

			
			if(file_exists("fotos_veiculos/".$idEmpresa)){
			
				$extensao = strtolower(end(explode(".",$nomeFoto)));
				$novoNome = "fotos_veiculos/".$idEmpresa."/".str_replace(" ","-",str_replace("/","-",$arrVeiculo[0]['modelo']))."-".$arrVeiculo[0]['ano_fabricacao']."-".@date("his").".".$extensao;
				
			}else{

				$extensao = strtolower(end(explode(".",$nomeFoto)));
				mkdir("fotos_veiculos/".$idEmpresa);
				chmod("fotos_veiculos/".$idEmpresa, 0755);
				$novoNome = "fotos_veiculos/".$idEmpresa."/".str_replace(" ","-",str_replace("/","-",$arrVeiculo[0]['modelo']))."-".$arrVeiculo[0]['ano_fabricacao']."-".@date("his").".".$extensao;
		
			}
			
			if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $novoNome)){
		   
				$arrFotosVeiculos = $dbFotosVeiculos->getFotosVeiculoSelecionado($arrVeiculo[0]['id']);
		   
				if($arrFotosVeiculos[0]['id'] == ""){
					
					$dadosUp['capa'] = 1;
				
				}
		   
				$dadosUp['path'] = $novoNome;
				$dadosUp['id_veiculo'] = $arrVeiculo[0]['id'];
		
				$dbFotosVeiculos->add($dadosUp);

			}

		}

	}

	public function loginAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		

		$dbUsuarios = new Application_Model_DbTable_Usuarios();
	
		$arrDadosLogin = $dbUsuarios->getUsuarioByLoginSenha($_POST['login'], $_POST['senha']);

		if($arrDadosLogin[0]['id_empresa']){
			
			echo $arrDadosLogin[0]['id_empresa'].":".$arrDadosLogin[0]['id'];
		
		}else{
		
			$this->view->resultado = 0;
		
		}

	}
	
	public function dadosAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		$dbModelos = new Application_Model_DbTable_Modelos();

		$arrModelo = explode("--",$_POST['modelo']);
		
		$arrDadosModelo['marca'] = $_POST['marca'];
		$arrDadosModelo['modelo'] = $arrModelo[0];
		$arrDadosModelo['ano_modelo'] = $arrModelo[1];
		
		$arrIdModelo = $dbModelos->getIdModelo($arrDadosModelo);

		$arrDados['id_empresa'] = $_POST['id_empresa'];
		$arrDados['id_usuario_alteracao'] = $_POST['id_usuario'];
		$arrDados['id_modelo'] = $arrIdModelo[0]['id'];
		$arrDados['consignado'] = 0;
		$arrDados['app'] = 1;
		
		if($_POST['novo_usado'] == "Usado"){
			
			$arrDados['novo_usado'] = 1;
		
		}else{
			
			$arrDados['novo_usado'] = 0;
		
		}
		
		
		
		$arrDados['ano_fabricacao'] = $_POST['ano_fabricacao'];
		$arrDados['cor'] = $_POST['cor'];
		$arrDados['km'] = $arrDados['novo_usado'];
		$arrDados['combustivel'] = $_POST['combustivel'];
		$arrDados['valor_venda'] = $_POST['valor_venda'];
		
		if(strripos($_POST['valor_venda'], "-")){
			
			$arrDados['placa'] = $_POST['placa'];
		
		}else{
			
			$arrDados['placa'] = substr($_POST['placa'], 0, 3)."-". substr($_POST['placa'], -4);
		
		}
		
		$arrDados['valor_aquisicao'] = $_POST['valor_compra'];
		$arrDados['data_aquisicao'] = @date("Y-m-d");
		$arrDados['origem'] = "Compra";
		$arrDados['hora_alteracao'] = @date("Y-m-d H:i:s");
		
		if($_POST['valor_venda'] == "" || $_POST['valor_venda'] < 1000){
			
			$arrDados['exibir_valor_site'] = 0;
		
		}else{
			
			$arrDados['exibir_valor_site'] = 1;
		
		}
		
		$arrDados['exibir_site_estoque'] = 3;
		$arrDados['exibir_km'] = 0;
		$arrDados['ativo'] = 1;
		$arrDados['descricao_site'] = mb_convert_encoding($_POST['descricao_site'], 'UTF-8', 'ISO-8859-1');

		if($_POST['id_empresa'] && $arrDados['id_modelo']){
		
			$idVeiculo = $dbVeiculos->add($arrDados);
		
		}
		
		if($idVeiculo){
		
			$dbCheckList = new Application_Model_DbTable_CheckList();
			
			$ChDados['id_veiculo'] = $idVeiculo;
			$ChDados['quitado_leasing'] = 0;
			$ChDados['pf_pj'] = 0;
			$ChDados['gnv'] = 0;
			$ChDados['doc_gnv'] = 0;
			
			$dbCheckList->add($ChDados);
			
			if($_POST['opcionais']){

				$dbOpcionaisVeiculos = new Application_Model_DbTable_OpcionaisVeiculos();

				$arrOpcionais = explode(",", $_POST['opcionais']);
				
				foreach($arrOpcionais as $opcionais){
				
					$arrDadosOpc['id_veiculo'] = $idVeiculo;
					$arrDadosOpc['id_opcional'] = $opcionais;
				
					$dbOpcionaisVeiculos->add($arrDadosOpc);
				
				}
			
			}
			
			echo $idVeiculo;
		
		}else{
		
			echo "0";
		
		}

	}
	
	
	public function modelosAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		$dbModelos = new Application_Model_DbTable_Modelos();

		$arrModelos = $dbModelos->getModelosPorMarca($_POST['marca']);
		
		//echo "SELECIONE:";
		
		foreach($arrModelos as $modelos){
		
			echo mb_convert_encoding($modelos['modelo']."--".$modelos['ano_modelo'], 'UTF-8', 'ISO-8859-1').":";
		
		}
		
	}
	
	
	public function placaAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();

		//$_POST['id_empresa'] = 2;
		//$_POST['placa'] = "";
		
		$arrDados['id_empresa'] = $_POST['id_empresa'];
		
		if($_POST){
		
			if(strripos($_POST['placa'], "-")){
	
				$arrDados['placa'] = $_POST['placa'];
		
			}else{
			
				$arrDados['placa'] = substr($_POST['placa'], 0, 3)."-". substr($_POST['placa'], -4);
		
			}
		
		
			$arrVeiculos = $dbVeiculos->getPlacaEmpresa($arrDados);
		
		}

		if($arrVeiculos){
		
			echo $arrVeiculos[0]['id'];
			
		}else{
			
			echo 0;
		
		}
		
	}

}

?>