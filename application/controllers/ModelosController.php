
<?php

header("Content-Type: text/html; charset=UTF-8",true);

class ModelosController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Modelos";

		Zend_Session::start();
		
	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function ajaxAction(){

		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
		
		header("Access-Control-Allow-Origin: *");

		if($this->_getParam('fn') == 'getByModelo'){
			
			$dbModelo = new Application_Model_DbTable_Modelos();
			
			$arrM = $dbModelo->fetchAll("modelo LIKE '".$this->_getParam('f')."%'",array("modelo", "ano_modelo"));

			foreach($arrM as $m){
			
				$xml .= "<option value=\"".$m['id']."\">".$m['modelo']." - ".$m['ano_modelo']."</option>";
			
			}

			echo $xml;
			
		}elseif($this->_getParam('fn') == 'getById'){
			
			$dbModelo = new Application_Model_DbTable_Modelos();
			
			$arrM = $dbModelo->fetchAll("id = ".$this->_getParam('f'));

			foreach($arrM as $m){
			
				foreach($m as $k => $v){
			
					if($k == 'preco'){
					
						$v = number_format($v,2,',','.');
					
					}
			
					echo $k.":".$v."|";
				}
			
			}
			
		}elseif($this->_getParam('fn') == 'busca_fipe'){
			
			$dbModelo = new Application_Model_DbTable_Modelos();
			
			$arrM = $dbModelo->fetchAll("id = ".$this->_getParam('fipe'));
			
			$fipe = $arrM[0]['preco'];
			
			$fipe = number_format($fipe,2,',','.');
			//$fipe = money_format('%i',$fipe);

			echo $fipe;	

			
			
		}elseif($this->_getParam('fn') == 'busca_modelos_todos'){
		
			$dbModelo = new Application_Model_DbTable_Modelo();
			
			$arrModelos = $dbModelo->getModelosPorMarca($this->_getParam('marcaa'));
			
			$modelos = "<option value=''>Selecione</option>";
			
			foreach($arrModelos as $modelo){
			
				$modelos .= "<option value='".$modelo['modelo']."'>".$modelo['modelo']."</option>";
			
			}
			
			echo $modelos;
			
			
		}elseif($this->_getParam('fn') == 'ano_modelo'){
		
			$dbModelo = new Application_Model_DbTable_Modelo();
			
			$arrAnoModelo = $dbModelo->getAnoModelo($this->_getParam('id_modelo'));
			
			$anoModelos = "<option value=''>Selecione</option>";
			
			foreach($arrAnoModelo as $anoModelo){
			
				$anoModelos .= "<option value='".$anoModelo['id']."'>".$anoModelo['ano_modelo']."</option>";
			
			}
			
			echo $anoModelos;
			

		}elseif(isset($_POST['marca']) && $_POST['marca'] != ""){
		
			$dbModelos = new Application_Model_DbTable_Modelos();

			if($this->_getParam('tipo') == ""){
			
				$arrModelos = $dbModelos->getModelosPorMarca($_POST['marca']);
			
			}else{
			
				$arrModelos = $dbModelos->getModelosPorMarcaTipo($_POST['marca'], $this->_getParam('tipo'));
			
			}
			
			$modelos = "<option value=''>Selecione</option>";
			
			foreach($arrModelos as $modelo){
			
				//$modelos .= "<option value='".$modelo['id']."'>".$modelo['modelo']." - ".$modelo['ano_modelo']."</option>";
				$modelos .= "<option value='".$modelo['id']."'>". mb_convert_encoding($modelo['modelo'], 'UTF-8', 'ISO-8859-1')." - ".$modelo['ano_modelo']."</option>";
			
			}
		
			echo $modelos;
		
		}elseif($this->_getParam('fn') == 'busca_modelo'){

			$marca = $this->_getParam('marca');
				
			if(!$this->_getParam('segmento')){
				
				if($this->_getParam('marca') == "MAGR�O TRICICLOS"){
				
					$marca = "MAGRAO TRICICLOS";
				
				}
			
				$dbModelos = new Application_Model_DbTable_Modelos();
			
				if($this->_getParam('estado')){
				
					$arrModelos = $dbModelos->getModelosPorMarcaVeiculosNovoUsado($marca, $this->_getParam('estado'));
				
					$modelos = "<option value=''>Selecione</option>";
				
					foreach($arrModelos as $modelo){
				
						$modelos .= "<option value='".$modelo['id']."'>".$modelo['modelo']."</option>";
				
					}
				
					echo $modelos;
				
				}else{

					$arrModelos = $dbModelos->getModelosPorMarcaVeiculos($marca);
					
					$modelos = "<option value=''>Selecione</option>";
					
					foreach($arrModelos as $modelo){
					
						$modelos .= "<option value='".$modelo['id']."'>".$modelo['modelo']."</option>";
					
					}
					
					//echo $this->_getParam('marca');
					echo $modelos;
				
				}
			
			}else{
			
				$dbModelos = new Application_Model_DbTable_Modelos();
			
				$arr['marca'] = $this->_getParam('marca');
				$arr['estado'] = $this->_getParam('estado');
				$arr['segmento'] = $this->_getParam('segmento');
			
				if($this->_getParam('estado')){
				
					$arrModelos = $dbModelos->getModelosPorMarcaVeiculosNovoUsado($marca, $this->_getParam('estado'));
				
					$modelos = "<option value=''>Selecione</option>";
				
					foreach($arrModelos as $modelo){
				
						$modelos .= "<option value='".$modelo['id']."'>".$modelo['modelo']."</option>";
				
					}
				
					echo $modelos;
				
				}else{

					$arrModelos = $dbModelos->getModelosPorMarcaVeiculos($arr);
					
					$modelos = "<option value=''>Selecione</option>";
					
					foreach($arrModelos as $modelo){
					
						$modelos .= "<option value='".$modelo['id']."'>".$modelo['modelo']."</option>";
					
					}
					
					//echo $this->_getParam('marca');
					echo $modelos;
				
				}
			
			}
		
		
		}elseif($this->_getParam('fn') == 'busca_marca'){
		
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
			
			if($this->_getParam('segmento') == "moto"){
			
				$arrMarcas = $dbVeiculos->getMarcasDistintasPorTipo($this->_getParam('segmento'));
				
				$marcas = "<option value=''>Selecione</option>";
				$marcas .= "<option value='AGRALE'>AGRALE</option>";
				$marcas .= "<option value='BMW'>BMW</option>";
				$marcas .= "<option value='DAFRA'>DAFRA</option>";
				$marcas .= "<option value='DUCATI'>DUCATI</option>";
				$marcas .= "<option value='HONDA'>HONDA</option>";
				$marcas .= "<option value='SUZUKI'>SUZUKI</option>";
				$marcas .= "<option value='YAMAHA'>YAMAHA</option>";
				$marcas .= "<option value=''></option>";
			
			}else{
			
				$arrMarcas = $dbVeiculos->getVeiculosMarca();
				
				$marcas = "<option value=''>Selecione</option>";
				$marcas .= "<option value='Citroën'>CITROËN</option>";
				$marcas .= "<option value='Fiat'>FIAT</option>";
				$marcas .= "<option value='Ford'>FORD</option>";
				$marcas .= "<option value='GM - Chevrolet'>GM - CHEVROLET</option>";
				$marcas .= "<option value='Honda'>HONDA</option>";
				$marcas .= "<option value='Hyundai'>HYUNDAI</option>";
				$marcas .= "<option value='Mitsubishi'>MITSUBISHI</option>";
				$marcas .= "<option value='Peugeot'>PEUGEOT</option>";
				$marcas .= "<option value='Renault'>RENAULT</option>";
				$marcas .= "<option value='Toyota'>TOYOTA</option>";
				$marcas .= "<option value='VW - VolksWagen'>VW - VOLKSWAGEN</option>";
				$marcas .= "<option value=''></option>";

			}
			
			
			
			foreach($arrMarcas as $marca){
			
				$marcas .= "<option value='".$marca['marca']."'>".$marca['marca']."</option>";
			
			}
			
			echo $marcas;
		
		}elseif($this->_getParam('fn') == 'busca_marca_tipo'){
		
			$dbVeiculos = new Application_Model_DbTable_Veiculos();
			
			if($this->_getParam('tipo') == "moto"){
			
				$arrMarcas = $dbVeiculos->getMarcasDistintasPorTipo($this->_getParam('tipo'));
			
				$marcas = "<option value=''>Selecione</option>";
				$marcas .= "<option value='BMW'>BMW</option>";
				$marcas .= "<option value='DAFRA'>DAFRA</option>";
				$marcas .= "<option value='DUCATI'>DUCATI</option>";
				$marcas .= "<option value='HARLEY-DAVIDSON'>HARLEY-DAVIDSON</option>";
				$marcas .= "<option value='HONDA'>HONDA</option>";
				$marcas .= "<option value='KAWASAKI'>KAWASAKI</option>";
				$marcas .= "<option value='SUZUKI'>SUZUKI</option>";
				$marcas .= "<option value='YAMAHA'>YAMAHA</option>";
				$marcas .= "<option value=''></option>";
			
			}elseif($this->_getParam('tipo') == "carro"){
			
				$arrMarcas = $dbVeiculos->getMarcasDistintasPorTipo($this->_getParam('tipo'));
			
				$marcas = "<option value=''>Selecione</option>";
				$marcas .= "<option value='Citroën'>CITROËN</option>";
				$marcas .= "<option value='Fiat'>FIAT</option>";
				$marcas .= "<option value='Ford'>FORD</option>";
				$marcas .= "<option value='GM - Chevrolet'>GM - CHEVROLET</option>";
				$marcas .= "<option value='Honda'>HONDA</option>";
				$marcas .= "<option value='Hyundai'>HYUNDAI</option>";
				$marcas .= "<option value='Mitsubishi'>MITSUBISHI</option>";
				$marcas .= "<option value='Peugeot'>PEUGEOT</option>";
				$marcas .= "<option value='Renault'>RENAULT</option>";
				$marcas .= "<option value='Toyota'>TOYOTA</option>";
				$marcas .= "<option value='VW - VolksWagen'>VW - VOLKSWAGEN</option>";
				$marcas .= "<option value=''></option>";
			
			}else{
			
				$arrMarcas = $dbVeiculos->getMarcasDistintas();
				
				$marcas = "<option value=''>Selecione</option>";
				$marcas .= "<option value='Citroën'>CITROËN</option>";
				$marcas .= "<option value='Fiat'>FIAT</option>";
				$marcas .= "<option value='Ford'>FORD</option>";
				$marcas .= "<option value='GM - Chevrolet'>GM - CHEVROLET</option>";
				$marcas .= "<option value='Honda'>HONDA</option>";
				$marcas .= "<option value='Hyundai'>HYUNDAI</option>";
				$marcas .= "<option value='Mitsubishi'>MITSUBISHI</option>";
				$marcas .= "<option value='Peugeot'>PEUGEOT</option>";
				$marcas .= "<option value='Renault'>RENAULT</option>";
				$marcas .= "<option value='Toyota'>TOYOTA</option>";
				$marcas .= "<option value='VW - VolksWagen'>VW - VOLKSWAGEN</option>";
				$marcas .= "<option value=''></option>";
			
			}
			
			foreach($arrMarcas as $marca){
			
				$marcas .= "<option value='".$marca['marca']."'>".$marca['marca']."</option>";
			
			}
			
			echo $marcas;
			
			
			
		}elseif($this->_getParam('fn') == 'busca_modelos'){

			$dbModelos = new Application_Model_DbTable_Modelos();

			$arrModelos = $dbModelos->getModelosPorMarca($this->_getParam('marca'));
			
			echo "<option value=''>Selecione</option>";
			
			foreach($arrModelos as $key=>$modelo){
				
				$arrTemp = explode(" ",$modelo['modelo']);
				
				if(strtolower($arrTemp[0]) != strtolower(current(explode(" ",$arrModelos[$key+1]['modelo'])))){
				
					echo "<option value='".$arrTemp[0]."'>".$arrTemp[0]."</option>";
				
				}
			
			}


		}elseif($this->_getParam('fn') == 'fipe_marcas'){

			// $this->_getParam('marca')
		
			$arrMarcas = self::curl_post_json("http://gds.tecnologia.ws/atualiza-fipe/".$this->_getParam('tipo')."/get-marcas.php", []);

			$marcas =  '<option disabled="disabled" selected="selected" value="">Selecione</option>';

			if($this->_getParam('tipo') == "carros") {

				$marcas .= '<option value="13">CITROËN</option>';
				$marcas .= '<option value="21">FIAT</option>';
				$marcas .= '<option value="22">FORD</option>';
				$marcas .= '<option value="23">GM - CHEVROLET</option>';
				$marcas .= '<option value="25">HONDA</option>';
				$marcas .= '<option value="26">HYUNDAI</option>';
				$marcas .= '<option value="41">MITSUBISHI</option>';
				$marcas .= '<option value="44">PEUGEOT</option>';
				$marcas .= '<option value="48">RENAULT</option>';
				$marcas .= '<option value="56">TOYOTA</option>';
				$marcas .= '<option value="59">VW - VOLKSWAGEN</option>';

			}elseif($this->_getParam('tipo') == "motos") {

				$marcas .= "<option value='67'>BMW</option>";
				$marcas .= "<option value='145'>DAFRA</option>";
				$marcas .= "<option value='74'>DUCATI</option>";
				$marcas .= "<option value='77'>HARLEY-DAVIDSON</option>";
				$marcas .= "<option value='80'>HONDA</option>";
				$marcas .= "<option value='85'>KAWASAKI</option>";
				$marcas .= "<option value='99'>SUZUKI</option>";
				$marcas .= "<option value='101'>YAMAHA</option>";

			}

			$marcas .= '<option disabled="disabled" value=""></option>';

			foreach ($arrMarcas as $value) {
				$marcas .= '<option value="'.$value['id'].'">'.$value['nome'].'</option>';
			}

			echo $marcas;

		}elseif($this->_getParam('fn') == 'fipe_modelos'){


			// $this->_getParam('marca')

			$arrModelos = self::curl_post_json("http://gds.tecnologia.ws/atualiza-fipe/".$this->_getParam('tipo')."/get-modelos.php", ['marca_id' => $this->_getParam('marca_id')]);

			$modelos = '<option disabled="disabled" selected="selected" value="">Selecione</option>';

			foreach ($arrModelos as $value) {
				$modelos .= '<option value="'.$value['id'].'">'.$value['nome'].'</option>';
			}

			echo $modelos;

		}elseif($this->_getParam('fn') == 'fipe_anos'){

			$arrAnos = self::curl_post_json("http://gds.tecnologia.ws/atualiza-fipe/".$this->_getParam('tipo')."/get-anos.php", ['marca_id' => $this->_getParam('marca_id'), 'modelo_id' => $this->_getParam('modelo_id')]);

			$anos = '<option disabled="disabled" selected="selected" value="">Selecione</option>';

			foreach ($arrAnos as $value) {
				$anos .= '<option value="'.$value['codigo'].'">'.str_replace('32000', 'Zero', $value['nome']).'</option>';
			}

			echo $anos;

		}elseif($this->_getParam('fn') == 'fipe_precos'){

			$arrFipe = self::curl_post_json("http://gds.tecnologia.ws/atualiza-fipe/".$this->_getParam('tipo')."/get-precos.php", ['marca_id' => $this->_getParam('marca_id'), 'modelo_id' => $this->_getParam('modelo_id'), 'codigo_ano' => $this->_getParam('codigo_ano')]);

			//echo $arrFipe[0]['valor'];
			echo json_encode($arrFipe[0]);

		}elseif($this->_getParam('fn') == 'get_modelo'){

			$dbModelos = new Application_Model_DbTable_Modelos();

			if($this->getRequest()->isPost()){

				$arr['tipo'] = $_POST['tipo'];
				$arr['marca'] = $_POST['marca_fipe'];
				$arr['modelo'] = $_POST['modelo_fipe'];
				$arr['cod_fipe'] = $_POST['codigo_fipe'];
				$arr['codigo'] = $_POST['id_ano_modelo'];
				$arr['combustivel'] = mb_substr(trim($_POST['combustivel']), 1, null, 'UTF-8');
				$arr['ano_modelo_fipe'] = str_replace('32000', 'Zero', $_POST['ano_modelo_fipe']);
				$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

				$arr['valor'] = str_replace("R$", "", $_POST['valor']);
				$arr['valor'] = str_replace(".", "", $arr['valor']);
				$arr['valor'] = trim(str_replace(",", ".", $arr['valor']));

				$arrModelos = $dbModelos->_getModeloExato($arr);

			}

			if($arrModelos == false) {

				$dados = array(
					'id_empresa' => $arr['id_empresa'],
					'modelo' => $arr['modelo']." ".$_POST['combustivel'],
					'marca' => $arr['marca'],
					'ano_modelo' => $arr['ano_modelo_fipe'],
					'codigo' => $arr['codigo'],
					'preco' => $arr['valor'],
					'id_usuario_alteracao' => $_SESSION['sessionUser']['id'],
					'hora_alteracao' => @date("Y-m-d H:i:s"),
					'cod_fipe' => $arr['cod_fipe'],
					'segmento' => $arr['tipo']
				);

				$dbModelos->add($dados);

				echo $dbModelos->getLastModelosId()['id'];

			}else{

				$dados = array(
					// 'preco' => $arr['valor'],
					'codigo' => $arr['codigo'],
					'id_usuario_alteracao' => $_SESSION['sessionUser']['id'],
					'hora_alteracao' => @date("Y-m-d H:i:s"),
				);

				$dbModelos->edt($arrModelos['id'], $dados);
                echo json_encode([
                    'id' => $arrModelos['id'],
                    'preco' => 'R$ ' . number_format($arrModelos['preco'], 2, ',', '.')
                ]);

				//echo json_encode($arrModelos['id']);

			}

		}elseif($this->_getParam('fn') == 'get_valor_fipe'){

			$arrFipe = self::curl_post_json("http://gds.tecnologia.ws/atualiza-fipe/".$this->_getParam('tipo')."s/get-valor.php", ['cod_fipe' => $this->_getParam('cod_fipe'), 'codigo_ano' => $this->_getParam('codigo_ano')]);

			echo json_encode($arrFipe[0]);
           // $this->getValorFipeLocal();

		}

	}

    // ===== NOVO MÉTODO: BUSCA FIPE DA TABELA LOCAL =====
	private function getValorFipeLocal()
	{
		try {
			$db = Zend_Db_Table::getDefaultAdapter();
			
			// Parâmetros recebidos
			$codigo_ano = $this->_getParam('codigo_ano');
			$cod_fipe = $this->_getParam('cod_fipe');
			$tipo = $this->_getParam('tipo');
			
			// Busca na tabela modelos_11 usando os campos existentes
			$sql = "SELECT 
						m.id,
						m.modelo,
						m.marca,
						m.preco,
						m.cod_fipe,
						m.ano_modelo,
						m.codigo,
						m.segmento
					FROM modelos_11 m
					WHERE m.codigo = ?
					AND m.cod_fipe = ?
					AND m.segmento = ?
					LIMIT 1";
			
			$resultado = $db->fetchRow($sql, [$codigo_ano, $cod_fipe, $tipo]);

			if ($resultado) {
				// Formata o valor como moeda brasileira
				$valorFormatado = 'R$ ' . number_format($resultado['preco'], 2, ',', '.');
				
				$retorno = [
					'valor' => $valorFormatado,
					'codigo_fipe' => $resultado['cod_fipe'],
					'ano_modelo' => $resultado['ano_modelo'],
					'combustivel' => '',
					'id_modelo' => $resultado['id']
				];
				
				echo json_encode($retorno);
			} else {
				// Caso não encontre na tabela local
				echo json_encode([
					'valor' => 'Sem referência...',
					'erro' => 'Não encontrado na base local'
				]);
			}
			
		} catch (Exception $e) {
			echo json_encode([
				'valor' => 'Erro ao buscar',
				'erro' => $e->getMessage()
			]);
		}
	}

	public function addAction(){
		
		$dbModelo = new Application_Model_DbTable_Modelos();
		
		$this->validaAcesso('gerenciar_modelos_de_veiculos');
		
		$dbVeiculos = new Application_Model_DbTable_Veiculos();
		
		$this->view->marcas = $dbVeiculos->getMarcasDistintas();
		
		if($this->getRequest()->isPost()){	
			
			$dados = $_POST;
			$dados['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$dados['hora_alteracao'] = @date("Y-m-d H:i:s");
			
			if($dbModelo->insert($dados)){
   
				$this->view->mensagem = "Modelo cadastrado com sucesso!";
			   
			}else{
			   
				$this->view->mensagem = "Erro ao cadastrar o Modelo.";
			   
			}
			
		}
	
	}
	
	public function listaAction(){
	
		$this->validaAcesso('listar_modelos_de_veiculos');
		
		$dbModelos = new Application_Model_DbTable_Modelos();
		
		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		if($this->getRequest()->isPost()){
		
			$arr['parcial'] = true;
			$arr['marca'] = $this->_getParam('marca');
			$arr['modelo'] = $this->_getParam('modelo');
			$arr['ano_modelo'] = $this->_getParam('ano_modelo');
			
		}
		
		$arrModelos = $dbModelos->_get($arr);
		
		$this->view->modelos = $arrModelos;
	
	}
	
	public function delAction(){

		$this->validaAcesso('gerenciar_modelos_de_veiculos');
	
		$dbModelos = new Application_Model_DbTable_Modelos();
		
		$dbModelos->delete("id = " . $this->_getParam('id'));
		
		$this->_helper->redirector->gotoUrl("modelos/lista");
	
	}
	
	public function edtAction(){
	
		$this->validaAcesso('gerenciar_modelos_de_veiculos');

		$dbModelos = new Application_Model_DbTable_Modelos();
		
		$this->view->id = $this->_getParam('id');
		
		$dados = $dbModelos->fetchAll("id = " . $this->_getParam('id'));
		
		$this->view->modelo = $dados[0];
				
		if($this->getRequest()->isPost()){
		
			$_POST['id_usuario_alteracao'] = $_SESSION['sessionUser']['id'];
			$_POST['hora_alteracao'] = @date("Y-m-d H:i:s");
		
			$dbModelos->update($_POST, "id = " . $_POST['id']);
			
			$this->_helper->redirector->gotoUrl("modelos/lista");
		
		}
	
	}
	
	public function atualizaAction(){
	
		$dbModelos = new Application_Model_DbTable_Modelos();
		
		$dbModelos->atualizaModelos();
	
	}


	private function curl_post_json($url, $data)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		$res = curl_exec($ch);
		if (curl_errno($ch)) {
			echo "Erro cURL: " . curl_error($ch) . "\n";
			return null;
		}
		curl_close($ch);
		return json_decode($res, true);
	}


}

?>
