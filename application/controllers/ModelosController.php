
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

			$tipo    = $this->_getParam('tipo');
			$tipoFipe = $this->getTipoVeiculoFipe($tipo);
			$tipoParallelum = $tipo == 'motos' ? 'motorcycles' : ($tipo == 'caminhoes' ? 'trucks' : 'cars');
			$preferidasIds = $tipo == 'motos' ? [67,145,74,77,80,85,99,101] : [13,21,22,23,25,26,41,44,48,56,59];

			$tabelaRef = $this->getTabelaReferenciaFipe();
			$arrMarcasRaw = $this->fipeApiPost('https://veiculos.fipe.org.br/api/veiculos/ConsultarMarcas', [
				'codigoTabelaReferencia' => $tabelaRef,
				'codigoTipoVeiculo'      => $tipoFipe
			]);

			$fonte = 'fipe';
			$arrMarcas = [];

			// Valida que a resposta é um array indexado com estrutura correta (Label+Value)
			if (is_array($arrMarcasRaw) && !empty($arrMarcasRaw) && isset($arrMarcasRaw[0]['Label'])) {
				$arrMarcas = $arrMarcasRaw;
			}

			if (!$arrMarcas) {
				$fonte    = 'parallelum';
				$raw      = $this->parallelumApiGet("https://parallelum.com.br/fipe/api/v2/{$tipoParallelum}/brands");
				if (is_array($raw) && !empty($raw) && isset($raw[0]['code'])) {
					foreach ($raw as $v) { $arrMarcas[] = ['Value' => $v['code'], 'Label' => $v['name']]; }
				}
			}

			if (!$arrMarcas) {
				$fonte = 'local';
				$dbVeiculos = new Application_Model_DbTable_Veiculos();
				$segmento = $tipo == 'motos' ? 'moto' : 'carro';
				$preferidasNomes = $segmento == 'moto'
					? ['BMW','DAFRA','DUCATI','HARLEY-DAVIDSON','HONDA','KAWASAKI','SUZUKI','YAMAHA']
					: ['Citroën','Fiat','Ford','GM - Chevrolet','Honda','Hyundai','Mitsubishi','Peugeot','Renault','Toyota','VW - VolksWagen'];
				$dbMarcas = $dbVeiculos->getMarcasDistintasPorTipo($segmento);
				foreach ($preferidasNomes as $p) { $arrMarcas[] = ['Value' => $p, 'Label' => strtoupper($p), '_pref' => true]; }
				$arrMarcas[] = ['Value' => '', 'Label' => '', '_sep' => true];
				foreach ($dbMarcas as $m) {
					if (!in_array($m['marca'], $preferidasNomes)) {
						$arrMarcas[] = ['Value' => $m['marca'], 'Label' => $m['marca']];
					}
				}
			}

			$marcas = '<option disabled="disabled" selected="selected" value="" data-fonte="'.$fonte.'">Selecione</option>';

			$prefBuffer = '';
			$restBuffer = '';
			$sepAdded   = false;
			foreach ($arrMarcas as $value) {
				if (isset($value['_sep'])) { continue; }
				$v = htmlspecialchars($value['Value']);
				$l = htmlspecialchars($value['Label']);
				$opt = '<option value="'.$v.'">'.$l.'</option>';
				if (isset($value['_pref']) || in_array((int)$value['Value'], $preferidasIds)) {
					$prefBuffer .= $opt;
				} else {
					$restBuffer .= $opt;
				}
			}
			$marcas .= $prefBuffer;
			if ($prefBuffer && $restBuffer) $marcas .= '<option disabled="disabled" value=""></option>';
			$marcas .= $restBuffer;

			echo $marcas;

		}elseif($this->_getParam('fn') == 'fipe_modelos'){

			$tipo           = $this->_getParam('tipo');
			$marcaId        = $this->_getParam('marca_id');
			$marcaNome      = trim($this->_getParam('marca_nome'));
			$tipoFipe       = $this->getTipoVeiculoFipe($tipo);
			$tipoParallelum = $tipo == 'motos' ? 'motorcycles' : ($tipo == 'caminhoes' ? 'trucks' : 'cars');

			$tabelaRef = $this->getTabelaReferenciaFipe();
			$resultado = $this->fipeApiPost('https://veiculos.fipe.org.br/api/veiculos/ConsultarModelos', [
				'codigoTabelaReferencia' => $tabelaRef,
				'codigoTipoVeiculo'      => $tipoFipe,
				'codigoMarca'            => $marcaId
			]);

			$fonte = 'fipe';
			$lista = [];

			if ($resultado && isset($resultado['Modelos']) && is_array($resultado['Modelos']) && !empty($resultado['Modelos'])) {
				foreach ($resultado['Modelos'] as $v) { $lista[] = ['Value' => $v['Value'], 'Label' => $v['Label']]; }
			} else {
				$fonte = 'parallelum';
				$raw   = $this->parallelumApiGet("https://parallelum.com.br/fipe/api/v2/{$tipoParallelum}/brands/{$marcaId}/models");
				if (is_array($raw) && !empty($raw) && isset($raw[0]['code'])) {
					foreach ($raw as $v) { $lista[] = ['Value' => $v['code'], 'Label' => $v['name']]; }
				}
			}

			if (!$lista) {
				$fonte     = 'local';
				$segmento  = $tipo == 'motos' ? 'moto' : 'carro';
				$dbModelos = new Application_Model_DbTable_Modelos();
				// Usa o nome da marca (enviado pelo JS) para buscar na base local
				$buscaMarca = $marcaNome ?: $marcaId;
				$dbLista    = $dbModelos->getModelosPorMarcaTipo($buscaMarca, $segmento);
				foreach ($dbLista as $v) { $lista[] = ['Value' => $v['modelo'], 'Label' => mb_convert_encoding($v['modelo'], 'UTF-8', 'ISO-8859-1')]; }
			}

			$modelos = '<option disabled="disabled" selected="selected" value="" data-fonte="'.$fonte.'">Selecione</option>';
			foreach ($lista as $v) {
				$modelos .= '<option value="'.htmlspecialchars($v['Value']).'">'.htmlspecialchars($v['Label']).'</option>';
			}

			echo $modelos;

		}elseif($this->_getParam('fn') == 'fipe_anos'){

			$tipo           = $this->_getParam('tipo');
			$marcaId        = $this->_getParam('marca_id');
			$marcaNome      = trim($this->_getParam('marca_nome'));
			$modeloId       = $this->_getParam('modelo_id');
			$modeloNome     = trim($this->_getParam('modelo_nome'));
			$tipoFipe       = $this->getTipoVeiculoFipe($tipo);
			$tipoParallelum = $tipo == 'motos' ? 'motorcycles' : ($tipo == 'caminhoes' ? 'trucks' : 'cars');

			$tabelaRef = $this->getTabelaReferenciaFipe();
			$arrAnos   = $this->fipeApiPost('https://veiculos.fipe.org.br/api/veiculos/ConsultarAnoModelo', [
				'codigoTabelaReferencia' => $tabelaRef,
				'codigoTipoVeiculo'      => $tipoFipe,
				'codigoMarca'            => $marcaId,
				'codigoModelo'           => $modeloId
			]);

			$fonte = 'fipe';
			$lista = [];

			if (is_array($arrAnos) && !empty($arrAnos) && isset($arrAnos[0]['Value'])) {
				foreach ($arrAnos as $v) { $lista[] = ['Value' => $v['Value'], 'Label' => str_replace('32000','Zero',$v['Label'])]; }
			} else {
				$fonte = 'parallelum';
				$raw   = $this->parallelumApiGet("https://parallelum.com.br/fipe/api/v2/{$tipoParallelum}/brands/{$marcaId}/models/{$modeloId}/years");
				if (is_array($raw) && !empty($raw) && isset($raw[0]['code'])) {
					foreach ($raw as $v) { $lista[] = ['Value' => $v['code'], 'Label' => str_replace('32000','Zero',$v['name'])]; }
				}
			}

			if (!$lista) {
				$fonte     = 'local';
				$segmento  = $tipo == 'motos' ? 'moto' : 'carro';
				$dbModelos = new Application_Model_DbTable_Modelos();
				// Usa os nomes (enviados pelo JS) para buscar na base local, pois IDs da FIPE/Parallelum não batem com a tabela local
				$buscaMarca  = $marcaNome  ?: $marcaId;
				// Usa apenas as 2 primeiras palavras do modelo para busca parcial (nome completo da FIPE é longo demais)
				$nomeModeloPartes = explode(' ', trim($modeloNome ?: $modeloId));
				$buscaModelo = implode(' ', array_slice($nomeModeloPartes, 0, 2));
				$dbAnos      = $dbModelos->getAnosDistintosPorModeloMarca($buscaModelo, $buscaMarca, $segmento);
				foreach ($dbAnos as $v) {
					$lista[] = ['Value' => $v['ano_modelo'], 'Label' => $v['ano_modelo'] == '32000' ? 'Zero KM' : $v['ano_modelo']];
				}
			}

			$anos = '<option disabled="disabled" selected="selected" value="" data-fonte="'.$fonte.'">Selecione</option>';
			foreach ($lista as $v) {
				$anos .= '<option value="'.htmlspecialchars($v['Value']).'">'.htmlspecialchars($v['Label']).'</option>';
			}

			echo $anos;

		}elseif($this->_getParam('fn') == 'fipe_precos'){

			$tipo           = $this->_getParam('tipo');
			$marcaId        = $this->_getParam('marca_id');
			$modeloId       = $this->_getParam('modelo_id');
			$codigoAno      = $this->_getParam('codigo_ano');
			$tipoFipe       = $this->getTipoVeiculoFipe($tipo);
			$tipoParallelum = $tipo == 'motos' ? 'motorcycles' : ($tipo == 'caminhoes' ? 'trucks' : 'cars');

			$partes          = explode('-', $codigoAno);
			$anoModelo       = $partes[0];
			$tipoCombustivel = isset($partes[1]) ? $partes[1] : 1;

			$tabelaRef = $this->getTabelaReferenciaFipe();
			$arrFipe   = $this->fipeApiPost('https://veiculos.fipe.org.br/api/veiculos/ConsultarValorComTodosParametros', [
				'codigoTabelaReferencia' => $tabelaRef,
				'codigoTipoVeiculo'      => $tipoFipe,
				'codigoMarca'            => $marcaId,
				'codigoModelo'           => $modeloId,
				'anoModelo'              => $anoModelo,
				'codigoTipoCombustivel'  => $tipoCombustivel,
				'tipoConsulta'           => 'tradicional'
			]);

			if ($arrFipe && isset($arrFipe['Valor'])) {
				echo json_encode([
					'fonte'          => 'fipe',
					'valor'          => $arrFipe['Valor'],
					'codigo_fipe'    => $arrFipe['CodigoFipe'],
					'combustivel'    => $arrFipe['Combustivel'],
					'ano_modelo'     => $arrFipe['AnoModelo'],
					'mes_referencia' => isset($arrFipe['MesReferencia']) ? $arrFipe['MesReferencia'] : ''
				]);
			} else {
				$arrFipeP = $this->parallelumApiGet("https://parallelum.com.br/fipe/api/v2/{$tipoParallelum}/brands/{$marcaId}/models/{$modeloId}/years/{$codigoAno}");

				if ($arrFipeP && isset($arrFipeP['price'])) {
					echo json_encode([
						'fonte'          => 'parallelum',
						'valor'          => $arrFipeP['price'],
						'codigo_fipe'    => isset($arrFipeP['codeFipe'])      ? $arrFipeP['codeFipe']      : '',
						'combustivel'    => isset($arrFipeP['fuel'])           ? $arrFipeP['fuel']           : '',
						'ano_modelo'     => isset($arrFipeP['modelYear'])      ? $arrFipeP['modelYear']      : $codigoAno,
						'mes_referencia' => isset($arrFipeP['referenceMonth']) ? $arrFipeP['referenceMonth'] : ''
					]);
				} else {
					echo json_encode([
						'fonte'       => 'local',
						'valor'       => '',
						'codigo_fipe' => '',
						'combustivel' => '',
						'ano_modelo'  => $codigoAno,
						'id'          => 0
					]);
				}
			}

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

				$novoId = $dbModelos->getLastModelosId()['id'];
				echo json_encode(['id' => $novoId, 'preco' => 'R$ ' . number_format($arr['valor'], 2, ',', '.')]);

			}else{

				$dados = array(
					'preco' => $arr['valor'],
					'codigo' => $arr['codigo'],
					'id_usuario_alteracao' => $_SESSION['sessionUser']['id'],
					'hora_alteracao' => @date("Y-m-d H:i:s"),
				);

				$dbModelos->edt($arrModelos['id'], $dados);
				echo json_encode(['id' => $arrModelos['id'], 'preco' => 'R$ ' . number_format($arr['valor'], 2, ',', '.')]);

			}

		}elseif($this->_getParam('fn') == 'get_valor_fipe'){

			$this->getValorFipeAtualizado();

		}

	}

	private function getValorFipeAtualizado()
	{
		$cod_fipe = $this->_getParam('cod_fipe');
		$codigo_ano = $this->_getParam('codigo_ano');
		$tipo = $this->_getParam('tipo');

		if ($cod_fipe && $codigo_ano) {
			// Monta o tipo para a BrasilAPI: carros, motos, caminhoes
			$tipoApi = $tipo;
			if (!$tipoApi) $tipoApi = 'carros';

			// Extrai o ano do codigo_ano (formato "2024-1")
			$partes = explode('-', $codigo_ano);
			$anoModelo = $partes[0];

			// BrasilAPI aceita consulta direta por codigo FIPE
			$url = 'https://brasilapi.com.br/api/fipe/preco/v1/' . urlencode($cod_fipe);

			$ch = curl_init($url);
			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 10,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_HTTPHEADER => ['Accept: application/json']
			]);
			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if ($httpCode == 200 && $response) {
				$dados = json_decode($response, true);

				if (is_array($dados)) {
					// A API retorna array com todos os anos, filtrar pelo ano desejado
					foreach ($dados as $item) {
						if (isset($item['anoModelo']) && $item['anoModelo'] == $anoModelo) {
							echo json_encode([
								'valor' => $item['valor'],
								'codigo_fipe' => $item['codigoFipe'],
								'ano_modelo' => $item['anoModelo'],
								'combustivel' => isset($item['combustivel']) ? $item['combustivel'] : ''
							]);
							return;
						}
					}

					// Se não encontrou o ano exato, retorna o primeiro resultado
					if (!empty($dados[0])) {
						echo json_encode([
							'valor' => $dados[0]['valor'],
							'codigo_fipe' => $dados[0]['codigoFipe'],
							'ano_modelo' => $dados[0]['anoModelo'],
							'combustivel' => isset($dados[0]['combustivel']) ? $dados[0]['combustivel'] : ''
						]);
						return;
					}
				}
			}
		}

		// Fallback: busca na tabela local
		$this->getValorFipeLocal();
	}

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

	private function parallelumApiGet($url)
	{
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 10,
			CURLOPT_HTTPHEADER     => ['Accept: application/json'],
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_FOLLOWLOCATION => true
		]);
		$response = curl_exec($ch);
		if (curl_errno($ch)) { curl_close($ch); return null; }
		curl_close($ch);
		return json_decode($response, true);
	}

	private function fipeApiPost($url, $params)
	{
		$postData = http_build_query($params, '', '&');

		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $postData,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTPHEADER => [
				'Accept: application/json, text/javascript, */*; q=0.01',
				'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.6,en;q=0.4',
				'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
				'Host: veiculos.fipe.org.br',
				'Origin: https://veiculos.fipe.org.br',
				'Referer: https://veiculos.fipe.org.br/',
				'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
			],
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_FOLLOWLOCATION => true
		]);

		$response = curl_exec($ch);
		if (curl_errno($ch)) {
			curl_close($ch);
			return null;
		}
		curl_close($ch);
		return json_decode($response, true);
	}

	private function getTabelaReferenciaFipe()
	{
		if (isset($_SESSION['fipe_tabela_ref']) && isset($_SESSION['fipe_tabela_ref_time'])
			&& (time() - $_SESSION['fipe_tabela_ref_time']) < 3600) {
			return $_SESSION['fipe_tabela_ref'];
		}

		$tabelas = $this->fipeApiPost('https://veiculos.fipe.org.br/api/veiculos/ConsultarTabelaDeReferencia', []);

		if (!$tabelas || empty($tabelas)) {
			return null;
		}

		$_SESSION['fipe_tabela_ref'] = $tabelas[0]['Codigo'];
		$_SESSION['fipe_tabela_ref_time'] = time();

		return $tabelas[0]['Codigo'];
	}

	private function getTipoVeiculoFipe($tipo)
	{
		$map = ['carros' => 1, 'motos' => 2, 'caminhoes' => 3];
		return isset($map[$tipo]) ? $map[$tipo] : 1;
	}

}

?>
