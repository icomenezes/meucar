<?php

header("Content-Type: text/html; charset=UTF-8",true);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

class FipeController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Fipe";

		Zend_Session::start();
		
	}
	
	public function validaAcesso($require){
	
		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function importarCsvAction()
	{
		$this->validaAcesso('gerenciar_concessionarias');

		$layout = $this->_helper->layout();
		$layout->setLayout('layout');

		$this->view->mensagem = null;
		$this->view->erro     = null;

		if (!$this->getRequest()->isPost()) {
			return;
		}

		$upload = isset($_FILES['csv']) ? $_FILES['csv'] : null;
		if (!$upload || $upload['error'] !== UPLOAD_ERR_OK) {
			$this->view->erro = 'Nenhum arquivo enviado ou erro no upload.';
			return;
		}

		$ext = strtolower(pathinfo($upload['name'], PATHINFO_EXTENSION));
		if (!in_array($ext, array('csv', 'txt'))) {
			$this->view->erro = 'Arquivo inválido. Envie um arquivo .csv ou .txt';
			return;
		}

		$db = $this->getInvokeArg('bootstrap')->getResource('db');

		$segmentoMap = array('CAR' => 'carros', 'TRUCK' => 'caminhoes', 'MOTO' => 'motos');

		$handle = fopen($upload['tmp_name'], 'r');
		if (!$handle) {
			$this->view->erro = 'Não foi possível ler o arquivo.';
			return;
		}

		// Pula o cabeçalho
		fgetcsv($handle);

		$db->beginTransaction();
		try {
			$db->query('TRUNCATE TABLE fipe');

			$batch     = array();
			$total     = 0;
			$batchSize = 500;

			while (($row = fgetcsv($handle)) !== false) {
				if (count($row) < 12) continue;

				$type        = $row[0];
				$brandCode   = $row[1];
				$brandValue  = $row[2];
				$modelCode   = $row[3];
				$modelValue  = $row[4];
				$yearCode    = $row[5];
				$yearValue   = $row[6];
				$fipeCode    = $row[7];
				$fuelLetter  = $row[8];
				$fuelType    = $row[9];
				$price       = $row[10];
				$month       = $row[11];

				$segmentoKey = strtoupper(trim($type));
				$segmento    = isset($segmentoMap[$segmentoKey]) ? $segmentoMap[$segmentoKey] : 'carros';

				// Limpa preço: "R$ 10.139,00" → 10139.00
				$preco = trim($price);
				$preco = preg_replace('/[^0-9,]/', '', $preco);
				$preco = str_replace(',', '.', str_replace('.', '', $preco));
				$preco = is_numeric($preco) ? (float)$preco : null;

				// Extrai só o ano da label (ex: "1991 Gasolina" → "1991")
				$anoPartes = explode(' ', trim($yearValue));
				$anoModelo = trim($anoPartes[0]);

				$combustivelLetra = trim($fuelLetter);
				$combustivelTipo  = trim($fuelType);

				$batch[] = array(
					'segmento'          => $segmento,
					'marca_id'          => (int)trim($brandCode),
					'marca'             => trim($brandValue),
					'modelo_id'         => (int)trim($modelCode),
					'modelo'            => trim($modelValue),
					'codigo'            => trim($yearCode),
					'ano_label'         => trim($yearValue),
					'ano_modelo'        => $anoModelo,
					'cod_fipe'          => trim($fipeCode),
					'combustivel_letra' => $combustivelLetra !== '' ? $combustivelLetra : null,
					'combustivel'       => $combustivelTipo  !== '' ? $combustivelTipo  : null,
					'preco'             => $preco,
					'mes_referencia'    => trim($month),
				);

				if (count($batch) >= $batchSize) {
					$this->_insertBatch($db, 'fipe', $batch);
					$total += count($batch);
					$batch = array();
				}
			}

			if ($batch) {
				$this->_insertBatch($db, 'fipe', $batch);
				$total += count($batch);
			}

			$db->commit();
			fclose($handle);

			$this->view->mensagem = "Importação concluída! {$total} registros importados.";

		} catch (Exception $e) {
			$db->rollBack();
			fclose($handle);
			$this->view->erro = 'Erro na importação: ' . $e->getMessage();
		}
	}

	private function _insertBatch($db, $table, array $rows)
	{
		if (!$rows) return;
		$cols        = array_keys($rows[0]);
		$placeholders = '(' . implode(',', array_fill(0, count($cols), '?')) . ')';
		$allPlaceholders = implode(',', array_fill(0, count($rows), $placeholders));
		$sql         = 'INSERT INTO ' . $table . ' (' . implode(',', $cols) . ') VALUES ' . $allPlaceholders;
		$values      = array();
		foreach ($rows as $row) {
			foreach ($row as $v) { $values[] = $v; }
		}
		$db->query($sql, $values);
	}

	public function marcasFipeAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		if($this->getRequest()->isPost()){	
		
			if($_POST['app'] == true){
				
				$dbFipe = new Application_Model_DbTable_Fipe();
				echo json_encode($dbFipe->getMarcas());
				
			}
		
		}

	}

	
	public function modelosFipeAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		if($this->getRequest()->isPost()){	
		
			//if($_POST['app'] == true){
			if($this->_getParam('app') == true){
				
				$dbFipe = new Application_Model_DbTable_Fipe();
				
				$arrModelos = $dbFipe->getModelos();
				
				foreach($arrModelos as $key=>$arr){
					$arrModelos[$key]['nome'] = str_replace("'", " ", mb_convert_encoding($arr['nome'], 'UTF-8', 'ISO-8859-1'));
				}
				
				echo json_encode($arrModelos);
				
			}
		
		}

	}

	public function anosModelosFipeAction(){
	
		$layout = $this->_helper->layout();
		$layout->setLayout('no-layout');
		
		$dbFipe = new Application_Model_DbTable_Fipe();
		
		$arrM = $dbFipe->getAnosModelos();
		
		foreach($arrM as $key=>$arr){
			$arrM[$key]['nome'] = mb_convert_encoding($arr['nome'], 'UTF-8', 'ISO-8859-1');
		}
		
		echo json_encode($arrM);

	}
	
}

?>
