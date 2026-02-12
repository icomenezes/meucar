<?php
header("Content-Type: text/html; charset=UTF-8",true);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

class AppsController extends Zend_Controller_Action{

	public function init(){

		$this->view->titulo = "Apps";

		Zend_Session::start();

	}



	public function downloadAvaliacarAction(){
	
		// $layout = $this->_helper->layout();
		// $layout->setLayout('no-layout');

		// $fullPath = "apps/Avaliacar_V_5.2.apk";

		// header('Content-Type: application/vnd.android.package-archive');
	 //    header('Content-Disposition: attachment; filename="'.$fullPath.'"');
	 //    readfile($fullPath);
	 //    //removemos o arquivo zip após download
	 //    //unlink($fullPath);

	}



/////////////////////////////////////////INICIO CÓDIGO PARA VISUALIZAR AVALIAÇÕES NO SISTEMA///////////////////////////////////////////



public function aprovaAvaliacaoSistemaAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		if($this->getRequest()->isPost()){

			if($this->_getParam('app_avaliacoes') == "true"){

				if($_POST['id'] && $_POST['situacao']){

					$arrDados['aprovada'] = $_POST['situacao'];
					$arrDados['observacoes_gerencia'] = $_POST['observacoes_gerencia'];

					$dbAvaliacoes = new Application_Model_DbTable_Avaliacoes();
					if(!$dbAvaliacoes->edt($_POST['id'], $arrDados)){
						echo "Erro";
					}else{
						echo "Sucesso";
					}

				}

			}

		}

}



public function buscaAvaliacaoAction(){
	
	$layout = $this->_helper->layout();
	$layout->setLayout('no-layout');

	if($this->getRequest()->isPost()){	

		if($this->_getParam('app_avaliacoes') == "true"){

			$dbAvaliacoes = new Application_Model_DbTable_Avaliacoes();
			$arr = $dbAvaliacoes->getAvaliacao($_SESSION['sessionUser']['id_empresa'], $_POST['id']);
			echo json_encode($arr[0]);

		}

	}

}




public function listaAvaliacoesAction(){
	
	$layout = $this->_helper->layout();
	$layout->setLayout('no-layout');

	if($this->getRequest()->isPost() == true){


		if($this->_getParam('app_avaliacoes') == "true"){

			$dbAvaliacoes = new Application_Model_DbTable_Avaliacoes();

			if ($_SESSION['sessionUser']['id_perfil'] == SUPERVISOR) {

				$arr = $dbAvaliacoes->getAvaliacoesSupervisor($_SESSION['sessionUser']['id_empresa']);
				
			}elseif ($_SESSION['sessionUser']['id_perfil'] == CONCESSIONARIO) {

				$arr = $dbAvaliacoes->getAvaliacoes($_SESSION['sessionUser']['id_empresa']);

			}elseif ($_SESSION['sessionUser']['id_perfil'] == VENDEDOR) {

				$arr = $dbAvaliacoes->getAvaliacoesVendedor($_SESSION['sessionUser']['id_empresa'], $_SESSION['sessionUser']['id_usuario']);

			}

			foreach ($arr as $key => $value){
				$arr[$key]['nome_modelo'] = mb_convert_encoding($value['nome_modelo'], 'UTF-8', 'ISO-8859-1');
			}

			//var_export($arr);

			echo json_encode($arr);

		}

	}

}











///////////////////////////////////////////FIM CÓDIGO PARA VISUALIZAR AVALIAÇÕES NO SISTEMA////////////////////////////////////////////




////////////////////////////////////APROVA AVALIAÇÃO///////////////////////////////////////////////////////

	public function aprovaAvaliacaoAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		if($this->getRequest()->isPost()){

			if($this->_getParam('app_avaliacoes') == "true"){

				if($this->login($_POST['login'], $_POST['senha']) ===  true){

					if($_POST['id'] && $_POST['situacao']){

						$arrDados['aprovada'] = $_POST['situacao'];
						$arrDados['observacoes_gerencia'] = $_POST['observacoes_gerencia'];

						$dbAvaliacoes = new Application_Model_DbTable_Avaliacoes();
						if(!$dbAvaliacoes->edt($_POST['id'], $arrDados)){
							echo "Erro";
						}else{
							echo "Sucesso";
						}

					}

				}

			}

		}

	}

////////////////////////////////////FIM APROVA AVALIAÇÃO///////////////////////////////////////////////////////


////////////////////////////////////BUSCA AVALIAÇÔES///////////////////////////////////////////////////////

	public function getAvaliacaoAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		if($this->getRequest()->isPost()){	

			if($this->_getParam('app_avaliacoes') == "true"){

				if($this->login($_POST['login'], $_POST['senha']) ===  true){

					$dbAvaliacoes = new Application_Model_DbTable_Avaliacoes();
					$arr = $dbAvaliacoes->getAvaliacao($_POST['id_empresa'], $_POST['id']);
					echo json_encode($arr[0]);

				}

			}

		}

	}

////////////////////////////////////FIM BUSCA AVALIAÇÔES///////////////////////////////////////////////////////





////////////////////////////////////BUSCA AVALIAÇÔES VENDEDOR///////////////////////////////////////////////////////

	public function getAvaliacoesVendedorAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		if($this->getRequest()->isPost()){	

			if($this->_getParam('app_avaliacoes') == "true"){

				if($this->login($_POST['login'], $_POST['senha']) ===  true){

					$dbAvaliacoes = new Application_Model_DbTable_Avaliacoes();
					$arr = $dbAvaliacoes->getAvaliacoesVendedor($_POST['id_empresa'], $_POST['id_usuario']);
					
					foreach ($arr as $key => $value){
						$arr[$key]['nome_modelo'] = mb_convert_encoding($value['nome_modelo'], 'UTF-8', 'ISO-8859-1');
					}

					echo json_encode($arr);

				}

			}

		}

	}

////////////////////////////////////FIM BUSCA AVALIAÇÔES VENDEDOR///////////////////////////////////////////////////////




////////////////////////////////////BUSCA AVALIAÇÔES VENDEDOR///////////////////////////////////////////////////////

	public function getAvaliacoesSupervisorAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		if($this->getRequest()->isPost()){	

			if($this->_getParam('app_avaliacoes') == "true"){

				if($this->login($_POST['login'], $_POST['senha']) ===  true){

					$dbAvaliacoes = new Application_Model_DbTable_Avaliacoes();
					$arr = $dbAvaliacoes->getAvaliacoesSupervisor($_POST['id_empresa']);
					
					foreach ($arr as $key => $value){
						$arr[$key]['nome_modelo'] = mb_convert_encoding($value['nome_modelo'], 'UTF-8', 'ISO-8859-1');
					}

					echo json_encode($arr);

				}

			}

		}

		/*$dbAvaliacoes = new Application_Model_DbTable_Avaliacoes();
		$arr = $dbAvaliacoes->getAvaliacoesSupervisor($_POST['id_empresa']);
		echo "<pre>";
		var_export($arr);
		echo "</pre>";
		
		*/
		//echo json_encode($arr);

	}

////////////////////////////////////FIM BUSCA AVALIAÇÔES VENDEDOR///////////////////////////////////////////////////////




////////////////////////////////////BUSCA AVALIAÇÔES///////////////////////////////////////////////////////

	public function getAvaliacoesAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		if($this->getRequest()->isPost()){	

			if($this->_getParam('app_avaliacoes') == "true"){

				if($this->login($_POST['login'], $_POST['senha']) ===  true){

					$dbAvaliacoes = new Application_Model_DbTable_Avaliacoes();
					$arr = $dbAvaliacoes->getAvaliacoes($_POST['id_empresa']);

					foreach ($arr as $key => $value){
						$arr[$key]['nome_modelo'] = mb_convert_encoding($value['nome_modelo'], 'UTF-8', 'ISO-8859-1');
					}

					echo json_encode($arr);

				}

			}

		}

	}

////////////////////////////////////FIM BUSCA AVALIAÇÔES///////////////////////////////////////////////////////



//////////////////////////////////LOGIN AVALIACAR////////////////////////////////////////////////////

	public function loginAvaliacarAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		if($this->getRequest()->isPost()){
			
			if($this->_getParam('app_avaliacoes') == "true"){

				$dbUsuarios = new Application_Model_DbTable_Usuarios();
				$arr = $dbUsuarios->loginAppAvaliacoes($_POST['login'], $_POST['senha']);

				if($arr){
					$arrUsuario = $dbUsuarios->getUsuarioPorEmpresaPerfil($arr[0]['id_empresa'], 2);
					$arr[0]['celular_superior'] = $arrUsuario[0]['celular'];
				}

				if($arr){
					$dbAvaliacoes = new Application_Model_DbTable_ParametrosAvaliacoes();
					$arrParametros = $dbAvaliacoes->getParametros($arr[0]['id_empresa']);
					$arr[0]['telefone_gerente'] = $arrParametros[0]['telefone_gerente'];
				}

				if($arr){
					echo json_encode($arr[0]);
				}else{
					echo -1;
				}

			}

		}

	}

//////////////////////////////////FIM LOGIN AVALIACAR////////////////////////////////////////////////

////////////////////////////////////ADD AVALIAÇÔES///////////////////////////////////////////////////////

	public function addAvaliacaoAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		if($this->getRequest()->isPost()){	
		
			if($_POST['app_avaliacoes'] == true){

				if($this->login($_POST['login'], $_POST['senha']) ===  true){

					$dbUsuarios = new Application_Model_DbTable_Usuarios();

					$arrUsuario = $dbUsuarios->_get(array('id' => $_POST['id_usuario'], 'id_empresa' => $_POST['id_empresa']));

					unset($_POST['login']);
					unset($_POST['senha']);
				
					if($_POST['id_upload'] == 0){

						unset($_POST['app_avaliacoes']);
						unset($_POST['id_upload']);

						$_POST['solicitar_liberacao'] = ($_POST['solicitar_liberacao'] == 'true' ? 1 : 0);
						$_POST['data_upload'] = @date('Y-m-d H:i:s');
						$_POST['telefone_avaliador'] = $arrUsuario[0]['celular'];

						$dbAvaliacoes = new Application_Model_DbTable_Avaliacoes();

						$id = $dbAvaliacoes->add($_POST);
						
						if($id){

							$idEmpresa = $_POST['id_empresa'];

							foreach($_FILES as $key=>$file){
							
								if($file['tmp_name'] != ""){

									if(!file_exists("fotos_avaliacoes/".$idEmpresa)){

										mkdir("fotos_avaliacoes/".$idEmpresa, 0755, true);
										//chmod("fotos_avaliacoes/".$idEmpresa, 0755);
										$novoNome = "fotos_avaliacoes/".$idEmpresa."/".$id.@date('his').$key.".jpg";

									}else{

										$novoNome = "fotos_avaliacoes/".$idEmpresa."/".$id.@date('his').$key.".jpg";

									}
									
									/////////////////REDIMENCIONA IMAGEM///////////////////////
									$input_image = $file['tmp_name'];
									$size = getimagesize( $input_image );
									$thumb_width = "1280";
									$thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );
									$thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );
									$src_img = ImageCreateFromJPEG( $input_image );
									ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );
									$copied = ImageJPEG( $thumbnail, $novoNome);
									chmod($novoNome, 0755);
									ImageDestroy( $thumbnail );
									
									if($copied){

										if($file['name'] == 'foto_0'){
											$dadosFotos['doc_carro'] = $novoNome;
										}else{
											$dadosFotos[$file['name']] = $novoNome;
										}

									}

								}
								
							}

							if($dadosFotos){
								$dbAvaliacoes->edt($id, $dadosFotos);
							}


							echo $id;

						}else{
							echo "Erro";
						}

					}else{

						$id = $_POST['id_upload'];

						unset($_POST['app_avaliacoes']);
						unset($_POST['data_upload']);
						unset($_POST['id_upload']);

						$_POST['solicitar_liberacao'] = ($_POST['solicitar_liberacao'] == 'true' ? 1 : 0);
						$_POST['telefone_avaliador'] = $arrUsuario[0]['celular'];

						$dbAvaliacoes = new Application_Model_DbTable_Avaliacoes();

						if(!$dbAvaliacoes->edt($id, $_POST)){
							echo "Erro";
						}else{

							if($id){

								$idEmpresa = $_POST['id_empresa'];

								foreach($_FILES as $key=>$file){
								
									if($file['tmp_name'] != ""){

										if(!file_exists("fotos_avaliacoes/".$idEmpresa)){

											mkdir("fotos_avaliacoes/".$idEmpresa, 0755, true);
											$novoNome = "fotos_avaliacoes/".$idEmpresa."/".$id.@date('his').$key.".jpg";

										}else{

											$novoNome = "fotos_avaliacoes/".$idEmpresa."/".$id.@date('his').$key.".jpg";

										}
										
										/////////////////REDIMENCIONA IMAGEM///////////////////////
										$input_image = $file['tmp_name'];
										$size = getimagesize( $input_image );
										$thumb_width = "1280";
										$thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );
										$thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );
										$src_img = ImageCreateFromJPEG( $input_image );
										ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );
										$copied = ImageJPEG( $thumbnail, $novoNome);
										chmod($novoNome, 0755);
										ImageDestroy( $thumbnail );
										
										if($copied){

											if($file['name'] == 'foto_0'){
												$dadosFotos['doc_carro'] = $novoNome;
											}else{
												$dadosFotos[$file['name']] = $novoNome;
											}

										}

									}
									
								}

								if($dadosFotos){
									$dbAvaliacoes->edt($id, $dadosFotos);
								}

								echo "Sucesso";

							}else{
								echo "Erro";
							}

						}

					}

				}else{
					echo "Erro";
				}

				//var_export($_POST);
				//var_export($_FILES);

			}

		}

	}	
/////////////////////////////////////////////////////////////////////////////////////////////////////////	



////////////////////////////////////APP AVALIAÇÔES///////////////////////////////////////////////////////

	public function atualizaParametrosAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		if($this->getRequest()->isPost()){	
		
			if($this->_getParam('app_avaliacoes') == true){

				$dbAvaliacoes = new Application_Model_DbTable_ParametrosAvaliacoes();
				$arr = $dbAvaliacoes->getParametros($this->_getParam('id_empresa'));
				echo json_encode($arr[0]);

			}

		}

	}
	
	
	
/////////////////////////////////////////////////////////////////////////////////////////////////////////	
	public function appTesteAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		
		var_export($_POST);
		var_export("<br><br><br>");
		var_export($_FILES);
		
		
	}
	
	public function addFotosAppAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		if($this->getRequest()->isPost()){	
		
			if($_POST['app'] == true){
				
				$idEmpresa = $_POST['id_empresa'];
				$idVeiculo = $_POST['id_veiculo'];


				if($idVeiculo){
					
					foreach($_FILES as $key=>$file){
					
						if($file['tmp_name'] != ""){
							
							$dbVeiculos = new Application_Model_DbTable_Veiculos();
							
							$arr = $dbVeiculos->getVeiculoEstoqueApp($idVeiculo, $idEmpresa);

							if(!file_exists(("fotos_veiculos/".$idEmpresa))){

								mkdir("fotos_veiculos/".$idEmpresa);
								chmod("fotos_veiculos/".$idEmpresa, 0755);
								$novoNome = "fotos_veiculos/".$idEmpresa."/".str_replace(" ","-",str_replace("/","-",$arr[0]['descricao_site']))."-".$arr[0]['ano_fabricacao']."-".@date('his').$key.".jpg";

							}else{

								$novoNome = "fotos_veiculos/".$idEmpresa."/".str_replace(" ","-",str_replace("/","-",$arr[0]['descricao_site']))."-".$arr[0]['ano_fabricacao']."-".@date('his').$key.".jpg";

							}
							
							/////////////////REDIMENCIONA IMAGEM///////////////////////
							$input_image = $file['tmp_name'];
							$size = getimagesize( $input_image );
							$thumb_width = "1280";
							$thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );
							$thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );
							$src_img = ImageCreateFromJPEG( $input_image );
							ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );
							$copied = ImageJPEG( $thumbnail, $novoNome);
							chmod($novoNome, 0755);
							ImageDestroy( $thumbnail );
							
							if($copied){

								$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();

								$dadosFotos['id_veiculo'] = $idVeiculo;
								$dadosFotos['path'] = $novoNome;
								$dadosFotos['capa'] = 0;
								
								$dbFotosVeiculos->add($dadosFotos);

							}

						}
						
					}
					
					echo "sucesso";

				}

			}
			
		}
		
	}
	
	
	public function addVeiculoAppAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		if($this->getRequest()->isPost()){	
		
			if($_POST['app'] == true){
				
				$dbModelos = new Application_Model_DbTable_Modelos();
				
				$arr = explode(" ", $_POST['ano_modelos']);
				$anoModelo = trim(current($arr));
				$combustivel = trim(substr(end($arr), -4));

				$idModelo = $dbModelos->getModelosDados($_POST['modelos_11'], $anoModelo, $combustivel);
				
				if($idModelo[0]['id']){
					
					$arrDados['id_empresa'] = $_POST['id_empresa'];
					$arrDados['placa'] = $_POST['input_placa'];
					$arrDados['id_modelo'] = $idModelo[0]['id'];
					$arrDados['id_usuario_alteracao'] = $_POST['id_usuario'];
					$arrDados['descricao_site'] = $_POST['descricao_site'];
					
					$arrDados['consignado'] = $_POST['consignado'];

					if(!$_POST['novo_usado']){
						$arrDados['novo_usado'] = 1;
					}
					
					$arrDados['ano_fabricacao'] = $_POST['ano_fabricacao'];
					$arrDados['cor'] = $_POST['cor'];
					$arrDados['combustivel'] = $_POST['combustivel'];
					$arrDados['km'] = $_POST['km'];
					
					$arrDados['data_aquisicao'] = $_POST['data_aquisicao'];
					$arrDados['valor_aquisicao'] = $_POST['valor_aquisicao'];
					$arrDados['valor_venda'] = $_POST['valor_venda'];
					
					$arrDados['origem'] = "Compra";
					
					if($_POST['exibir_site_estoque']){
						$arrDados['exibir_site_estoque'] = 3;
					}
					
					if($_POST['exibir_km']){
						$arrDados['exibir_km'] = 1;						
					}
					
					if($_POST['exibir_valor_site']){
						$arrDados['exibir_valor_site'] = 1;						
					}
					
					$arrDados['ativo'] = 1;
					$arrDados['hora_alteracao'] = @date("Y-m-d H:i:s");
					
					$dbVeiculos = new Application_Model_DbTable_Veiculos();
					$idVeiculo = $dbVeiculos->add($arrDados);

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
							$arrOpcionais = explode("|", substr($_POST['opcionais'], 0, -1));
							
							foreach($arrOpcionais as $opcionais){
								$arrDadosOpc['id_veiculo'] = $idVeiculo;
								$arrDadosOpc['id_opcional'] = $opcionais;
							
								$dbOpcionaisVeiculos->add($arrDadosOpc);
							}
							
						}
						
						foreach($_FILES as $key=>$file){
						
							if($file['tmp_name'] != ""){

								if(!file_exists(("fotos_veiculos/".$arrDados['id_empresa']))){

									mkdir("fotos_veiculos/".$arrDados['id_empresa']);
									chmod("fotos_veiculos/".$arrDados['id_empresa'], 0755);
									$novoNome = "fotos_veiculos/".$arrDados['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$arrDados['descricao_site']))."-".$arrDados['ano_fabricacao']."-".$key.".jpg";

								}else{

									$novoNome = "fotos_veiculos/".$arrDados['id_empresa']."/".str_replace(" ","-",str_replace("/","-",$arrDados['descricao_site']))."-".$arrDados['ano_fabricacao']."-".$key.".jpg";

								}
								
								/////////////////REDIMENCIONA IMAGEM///////////////////////
								$input_image = $file['tmp_name'];
								$size = getimagesize( $input_image );
								$thumb_width = "1280";
								$thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );
								$thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );
								$src_img = ImageCreateFromJPEG( $input_image );
								ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );
								$copied = ImageJPEG( $thumbnail, $novoNome);
								chmod($novoNome, 0755);
								ImageDestroy( $thumbnail );
								
								if($copied){

									$dbFotosVeiculos = new Application_Model_DbTable_FotosVeiculos();

									$dadosFotos['id_veiculo'] = $idVeiculo;
									$dadosFotos['path'] = $novoNome;
									if($key == 0){
										$dadosFotos['capa'] = 1;
									}else{
										$dadosFotos['capa'] = 0;
									}

									$dbFotosVeiculos->add($dadosFotos);

								}else{

									echo "Não foi possivel salvar a foto de capa!";
									
								}

							}
							
						}
						
						echo "sucesso";

					}

				}else{
					echo "Erro: Não encontrado o ID do modelo(m01).";
				}

			}
			
		}
		
	}
	
	
	public function loginAppAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		if($this->getRequest()->isPost()){	
		
			if($_POST['app'] == true){
				
				$login = $_POST['login'];
				$senha = substr(substr($_POST['senha'], 2), 0 , -1); 
			
				$usuarios = new Application_Model_DbTable_Usuarios();
				$arrUsuario = $usuarios->getUsuarioByLoginSenha($login, $senha);
				
				$arr['id_usuario'] = $arrUsuario[0]['id'];
				$arr['id_empresa'] = $arrUsuario[0]['id_empresa'];
				$arr['nome'] = $arrUsuario[0]['nome'];
				
				echo json_encode($arr);
				
			}
			
		}
		
	}

	public function verificaPlacaAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');

		if($this->getRequest()->isPost()){	
		
			if($_POST['app'] == true){
				
				$idEmpresa = $_POST['id_empresa'];
				$placa = strtolower($_POST['placa']);
				
			
				$dbVeiculos = new Application_Model_DbTable_Veiculos();
				$arrVeiculo = $dbVeiculos->getVeiculoPorPlacaEmpresa($placa, $idEmpresa);
				
				$arr['id_veiculo'] = $arrVeiculo[0]['id'];
				
				
				if($arrVeiculo){	
					echo json_encode($arr);
				}else{
					$arr['id_veiculo'] = "erro";
					echo json_encode($arr);
				}
				
			}
			
		}
		
	}


	private function login($login, $senha){

		$dbUsuarios = new Application_Model_DbTable_Usuarios();
		$arr = $dbUsuarios->loginAppAvaliacoes($login, $senha);

		if($arr){
			return true;
		}else{
			return false;
		}

	}
	
	
	
	
	
	
	
	
	
	
	
	
	
///////////////////////////ANTIGO SOMENTE CONSULTA/////////////////////////////////

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
				$thumb_width = "1280";
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
				$thumb_width = "1280";
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
					$thumb_width = "1280";
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