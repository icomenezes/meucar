
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

			$tipo          = $this->_getParam('tipo');
			$segmento      = $tipo === 'motos' ? 'motos' : ($tipo === 'caminhoes' ? 'caminhoes' : 'carros');
			$preferidasIds = $tipo === 'motos' ? [67,145,74,77,80,85,99,101] : [13,21,22,23,25,26,41,44,48,56,59];

			$db       = $this->getInvokeArg('bootstrap')->getResource('db');
			$rows     = $db->fetchAll(
				'SELECT DISTINCT marca_id, marca FROM fipe WHERE segmento = ? ORDER BY marca',
				[$segmento]
			);

			$fonte    = 'fipe_local';
			$arrMarcas = [];

			if ($rows) {
				foreach ($rows as $r) {
					$arrMarcas[] = ['Value' => $r['marca_id'], 'Label' => $r['marca']];
				}
			} else {
				// Fallback: APIs externas (tabela fipe ainda não importada)
				$tipoFipe       = $this->getTipoVeiculoFipe($tipo);
				$tipoParallelum = $tipo == 'motos' ? 'motorcycles' : ($tipo == 'caminhoes' ? 'trucks' : 'cars');
				$fonte          = 'fipe';
				$tabelaRef      = $this->getTabelaReferenciaFipe();
				$arrMarcasRaw   = $this->fipeApiPost('https://veiculos.fipe.org.br/api/veiculos/ConsultarMarcas', [
					'codigoTabelaReferencia' => $tabelaRef,
					'codigoTipoVeiculo'      => $tipoFipe
				]);
				if (is_array($arrMarcasRaw) && !empty($arrMarcasRaw) && isset($arrMarcasRaw[0]['Label'])) {
					$arrMarcas = $arrMarcasRaw;
				} else {
					$fonte = 'parallelum';
					$raw   = $this->parallelumApiGet("https://parallelum.com.br/fipe/api/v2/{$tipoParallelum}/brands");
					if (is_array($raw) && !empty($raw) && isset($raw[0]['code'])) {
						foreach ($raw as $v) { $arrMarcas[] = ['Value' => $v['code'], 'Label' => $v['name']]; }
					}
				}
			}

			$marcas     = '<option disabled="disabled" selected="selected" value="" data-fonte="'.$fonte.'">Selecione</option>';
			$prefBuffer = '';
			$restBuffer = '';
			foreach ($arrMarcas as $value) {
				$v   = htmlspecialchars($value['Value']);
				$l   = htmlspecialchars($value['Label']);
				$opt = '<option value="'.$v.'">'.$l.'</option>';
				if (in_array((int)$value['Value'], $preferidasIds)) {
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

			$tipo     = $this->_getParam('tipo');
			$marcaId  = (int)$this->_getParam('marca_id');
			$segmento = $tipo === 'motos' ? 'motos' : ($tipo === 'caminhoes' ? 'caminhoes' : 'carros');

			$db    = $this->getInvokeArg('bootstrap')->getResource('db');
			$rows  = $db->fetchAll(
				'SELECT DISTINCT modelo_id, modelo FROM fipe WHERE segmento = ? AND marca_id = ? ORDER BY modelo',
				[$segmento, $marcaId]
			);

			$fonte = 'fipe_local';
			$lista = [];

			if ($rows) {
				foreach ($rows as $r) {
					$lista[] = ['Value' => $r['modelo_id'], 'Label' => $r['modelo']];
				}
			} else {
				// Fallback: APIs externas
				$tipoFipe       = $this->getTipoVeiculoFipe($tipo);
				$tipoParallelum = $tipo == 'motos' ? 'motorcycles' : ($tipo == 'caminhoes' ? 'trucks' : 'cars');
				$fonte          = 'fipe';
				$tabelaRef      = $this->getTabelaReferenciaFipe();
				$resultado      = $this->fipeApiPost('https://veiculos.fipe.org.br/api/veiculos/ConsultarModelos', [
					'codigoTabelaReferencia' => $tabelaRef,
					'codigoTipoVeiculo'      => $tipoFipe,
					'codigoMarca'            => $marcaId
				]);
				if ($resultado && isset($resultado['Modelos']) && !empty($resultado['Modelos'])) {
					foreach ($resultado['Modelos'] as $v) { $lista[] = ['Value' => $v['Value'], 'Label' => $v['Label']]; }
				} else {
					$fonte = 'parallelum';
					$raw   = $this->parallelumApiGet("https://parallelum.com.br/fipe/api/v2/{$tipoParallelum}/brands/{$marcaId}/models");
					if (is_array($raw) && !empty($raw) && isset($raw[0]['code'])) {
						foreach ($raw as $v) { $lista[] = ['Value' => $v['code'], 'Label' => $v['name']]; }
					}
				}
			}

			$modelos = '<option disabled="disabled" selected="selected" value="" data-fonte="'.$fonte.'">Selecione</option>';
			foreach ($lista as $v) {
				$modelos .= '<option value="'.htmlspecialchars($v['Value']).'">'.htmlspecialchars($v['Label']).'</option>';
			}

			echo $modelos;

		}elseif($this->_getParam('fn') == 'fipe_anos'){

			$tipo      = $this->_getParam('tipo');
			$marcaId   = (int)$this->_getParam('marca_id');
			$modeloId  = (int)$this->_getParam('modelo_id');
			$segmento  = $tipo === 'motos' ? 'motos' : ($tipo === 'caminhoes' ? 'caminhoes' : 'carros');

			$db    = $this->getInvokeArg('bootstrap')->getResource('db');
			$rows  = $db->fetchAll(
				'SELECT DISTINCT codigo, ano_label FROM fipe WHERE segmento = ? AND marca_id = ? AND modelo_id = ? ORDER BY codigo DESC',
				[$segmento, $marcaId, $modeloId]
			);

			$fonte = 'fipe_local';
			$lista = [];

			if ($rows) {
				foreach ($rows as $r) {
					$lista[] = ['Value' => $r['codigo'], 'Label' => str_replace('32000', 'Zero', $r['ano_label'])];
				}
			} else {
				// Fallback: APIs externas
				$tipoFipe       = $this->getTipoVeiculoFipe($tipo);
				$tipoParallelum = $tipo == 'motos' ? 'motorcycles' : ($tipo == 'caminhoes' ? 'trucks' : 'cars');
				$fonte          = 'fipe';
				$tabelaRef      = $this->getTabelaReferenciaFipe();
				$arrAnos        = $this->fipeApiPost('https://veiculos.fipe.org.br/api/veiculos/ConsultarAnoModelo', [
					'codigoTabelaReferencia' => $tabelaRef,
					'codigoTipoVeiculo'      => $tipoFipe,
					'codigoMarca'            => $marcaId,
					'codigoModelo'           => $modeloId
				]);
				if (is_array($arrAnos) && !empty($arrAnos) && isset($arrAnos[0]['Value'])) {
					foreach ($arrAnos as $v) { $lista[] = ['Value' => $v['Value'], 'Label' => str_replace('32000','Zero',$v['Label'])]; }
				} else {
					$fonte = 'parallelum';
					$raw   = $this->parallelumApiGet("https://parallelum.com.br/fipe/api/v2/{$tipoParallelum}/brands/{$marcaId}/models/{$modeloId}/years");
					if (is_array($raw) && !empty($raw) && isset($raw[0]['code'])) {
						foreach ($raw as $v) { $lista[] = ['Value' => $v['code'], 'Label' => str_replace('32000','Zero',$v['name'])]; }
					}
				}
			}

			$anos = '<option disabled="disabled" selected="selected" value="" data-fonte="'.$fonte.'">Selecione</option>';
			foreach ($lista as $v) {
				$anos .= '<option value="'.htmlspecialchars($v['Value']).'">'.htmlspecialchars($v['Label']).'</option>';
			}

			echo $anos;

		}elseif($this->_getParam('fn') == 'fipe_precos'){

			$tipo      = $this->_getParam('tipo');
			$marcaId   = (int)$this->_getParam('marca_id');
			$modeloId  = (int)$this->_getParam('modelo_id');
			$codigoAno = $this->_getParam('codigo_ano');
			$segmento  = $tipo === 'motos' ? 'motos' : ($tipo === 'caminhoes' ? 'caminhoes' : 'carros');

			$db  = $this->getInvokeArg('bootstrap')->getResource('db');
			$row = $db->fetchRow(
				'SELECT preco, cod_fipe, combustivel, ano_label, mes_referencia FROM fipe WHERE segmento = ? AND marca_id = ? AND modelo_id = ? AND codigo = ? LIMIT 1',
				[$segmento, $marcaId, $modeloId, $codigoAno]
			);

			if ($row) {
				$valorFormatado = ($row['preco'] !== null)
					? 'R$ ' . number_format((float)$row['preco'], 2, ',', '.')
					: '';
				echo json_encode([
					'fonte'          => 'fipe_local',
					'valor'          => $valorFormatado,
					'codigo_fipe'    => $row['cod_fipe'],
					'combustivel'    => $row['combustivel'],
					'ano_modelo'     => $row['ano_modelo'],
					'mes_referencia' => $row['mes_referencia'],
				]);
			} else {
				// Fallback: APIs externas (modelo não está na tabela fipe local)
				$tipoFipe       = $this->getTipoVeiculoFipe($tipo);
				$tipoParallelum = $tipo == 'motos' ? 'motorcycles' : ($tipo == 'caminhoes' ? 'trucks' : 'cars');
				$partes          = explode('-', $codigoAno);
				$anoModelo       = $partes[0];
				$tipoCombustivel = isset($partes[1]) ? $partes[1] : 1;
				$tabelaRef       = $this->getTabelaReferenciaFipe();
				$arrFipe         = $this->fipeApiPost('https://veiculos.fipe.org.br/api/veiculos/ConsultarValorComTodosParametros', [
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
			}

		}elseif($this->_getParam('fn') == 'get_modelo'){

			$dbModelos = new Application_Model_DbTable_Modelos();

			if($this->getRequest()->isPost()){

				// Normaliza segmento para evitar encoding corrompido (ex: "caminh??o")
				$tipoRaw = strtolower(trim($_POST['tipo']));
				if (strpos($tipoRaw, 'caminhao') !== false || strpos($tipoRaw, 'caminhão') !== false || strpos($tipoRaw, 'caminh') !== false) {
					$arr['tipo'] = 'caminhao';
				} elseif (strpos($tipoRaw, 'moto') !== false) {
					$arr['tipo'] = 'moto';
				} else {
					$arr['tipo'] = 'carro';
				}
				// Normaliza marca para evitar double-encoding (ex: "CitroÃ«n" → "Citroën")
				$marcaRaw = $_POST['marca_fipe'];
				if (mb_detect_encoding($marcaRaw, ['UTF-8'], true) === false) {
					$marcaRaw = mb_convert_encoding($marcaRaw, 'UTF-8', 'ISO-8859-1');
				}
				$arr['marca'] = $marcaRaw;
				$arr['modelo'] = $_POST['modelo_fipe'];
				$arr['cod_fipe'] = $_POST['codigo_fipe'];
				$arr['codigo'] = $_POST['id_ano_modelo'];
				$arr['combustivel'] = mb_substr(trim($_POST['combustivel']), 1, null, 'UTF-8');
				// Normaliza ano_modelo: string 'null', vazio ou código bruto "2024-5" → só o ano
				$anoRaw = trim($_POST['ano_modelo_fipe']);
				if ($anoRaw === 'null' || $anoRaw === '') {
					// Deriva do codigo (ex: "2025-5" → "2025")
					$partes = explode('-', trim($_POST['id_ano_modelo']));
					$anoRaw = $partes[0];
				} elseif (strpos($anoRaw, '-') !== false) {
					$partes = explode('-', $anoRaw);
					$anoRaw = $partes[0];
				}
				$arr['ano_modelo_fipe'] = str_replace('32000', 'Zero', $anoRaw);
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
				$precoFormatado = is_numeric($arr['valor']) ? 'R$ ' . number_format((float)$arr['valor'], 2, ',', '.') : '';
				echo json_encode(['id' => $novoId, 'preco' => $precoFormatado]);

			}else{

				$dados = array(
					'preco' => $arr['valor'],
					'codigo' => $arr['codigo'],
					'ano_modelo' => $arr['ano_modelo_fipe'],
					'id_usuario_alteracao' => $_SESSION['sessionUser']['id'],
					'hora_alteracao' => @date("Y-m-d H:i:s"),
				);

				$dbModelos->edt($arrModelos['id'], $dados);
				$precoFormatado = is_numeric($arr['valor']) ? 'R$ ' . number_format((float)$arr['valor'], 2, ',', '.') : '';
				echo json_encode(['id' => $arrModelos['id'], 'preco' => $precoFormatado]);

			}

		}elseif($this->_getParam('fn') == 'get_valor_fipe'){

			$this->getValorFipeAtualizado();

		}

	}

	private function getValorFipeAtualizado()
	{
		$cod_fipe   = $this->_getParam('cod_fipe');
		$codigo_ano = $this->_getParam('codigo_ano');
		$tipo       = $this->_getParam('tipo') ?: 'carros';
		$segmento   = $tipo === 'motos' ? 'motos' : ($tipo === 'caminhoes' ? 'caminhoes' : 'carros');

		if ($cod_fipe && $codigo_ano) {
			$db  = Zend_Db_Table::getDefaultAdapter();
			$row = $db->fetchRow(
				'SELECT preco, cod_fipe, combustivel, ano_modelo, mes_referencia FROM fipe WHERE cod_fipe = ? AND codigo = ? AND segmento = ? LIMIT 1',
				[$cod_fipe, $codigo_ano, $segmento]
			);

			if ($row) {
				$valorFormatado = ($row['preco'] !== null)
					? 'R$ ' . number_format((float)$row['preco'], 2, ',', '.')
					: '';
				echo json_encode([
					'valor'          => $valorFormatado,
					'codigo_fipe'    => $row['cod_fipe'],
					'ano_modelo'     => $row['ano_modelo'],
					'combustivel'    => $row['combustivel'],
					'mes_referencia' => $row['mes_referencia'],
				]);
				return;
			}
		}

		echo json_encode([
			'valor'       => 'Sem referência...',
			'codigo_fipe' => $cod_fipe ?: '',
			'ano_modelo'  => $codigo_ano ?: '',
		]);
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
