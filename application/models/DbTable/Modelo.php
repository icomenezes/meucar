<?php
//header("Content-Type: text/html; charset=ISO-8859-1",true);
header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Modelos extends Zend_Db_Table_Abstract
{

	protected $_name = 'modelos_11';
	
	
	/*Retorna id do modelo para cadastrar um veiculo via app.
	Parametros são: cod_fipe, combustivel e ano_modelo.*/
	public function getModelosDados($codFipe, $anoModelo, $combustivel){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelos_11'),
		array('id')
		);
		
		$row->where("m.cod_fipe = '".$codFipe."'");
		$row->where("m.ano_modelo = ".$anoModelo);
		$row->where("m.modelo LIKE '%".$combustivel."'");

		$row->order('id DESC');
		$row->limit(1);
		
		return $row->query()->fetchAll();
		
	}

	public function getLastModelosId(){

		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelos_11'),
		array('id')
		);

		$row->order('id DESC');
		$row->limit(1);
		return $row->query()->fetch();

	}

	public function _getModeloExato($arr = array()){

		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('m'=>'modelos_11'),
			array('*')
		);

		$row->where("m.modelo LIKE '%" . $arr['combustivel'] . "%'");
		$row->where("m.segmento = '".$arr['tipo']."'");
		$row->where("m.cod_fipe = '".$arr['cod_fipe']."'");
		$row->where("m.ano_modelo = '".$arr['ano_modelo_fipe']."'");
		$row->where("m.cod_fipe is not null");
		$row->limit(1);

		//echo $row->__toString();

		return $row->query()->fetch();

	}


	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('m'=>'modelos_11'),
			array('*')
		);
		
		$row->joinLeft(
			array('u'=>'usuarios'),
			'u.id = m.id_usuario_alteracao',
			array('nome')
		);
	
		if($arr['id']){
		
			$row->where('m.id = ' . $arr['id']);
		
		}
		
		if($arr['id_empresa']){
			
			$row->where('m.id_empresa = ' . $arr['id_empresa']);
			
		}
		
		if(!$arr['parcial']){
		
			if($arr['marca']){
			
				$row->where("m.marca = '" . $arr['marca'] . "'");
			
			}
			
			if($arr['modelo']){
			
				$row->where("m.modelo = '" . $arr['modelo'] . "'");
			
			}
			
			if($arr['ano_modelo']){
			
				$row->where("m.ano_modelo = '" . $arr['ano_modelo'] . "'");
			
			}

		}else{
			
			if($arr['marca']){
			
				$row->where("m.marca LIKE '" . $arr['marca'] . "%'");
			
			}
			
			if($arr['modelo']){
			
				$row->where("m.modelo LIKE '" . $arr['modelo'] . "%'");
			
			}
			
			if($arr['ano_modelo']){
			
				$row->where("m.ano_modelo LIKE '" . $arr['ano_modelo'] . "%'");
			
			}
			
		}
		
		$row->where("m.cod_fipe is not null");
	
		$row->order('m.marca');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function add($dados){
		
		try{

			return $this->insert($dados);
		
		} catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	}

	public function edt($id, $dados){
		
		try{

			return $this->update($dados, 'id = ' . $id);

		}catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	} 

	public function del($id){
	
		$dados = $this->getPerfil($id);

		$this->delete('id = ' . $id);

	}
	
	public function getPerfis(){
	
		return $this->fetchAll();	
	
	}
	
	public function getPerfil($id){
		
		return $this->fetchAll("id = $id");
	
	}
	
	public function getModelosPorEmpresa($idEmpresa){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelos_11'),
		array('*')
		);
		
		$row->where("m.id_empresa = ".$idEmpresa);
		  
		$row->order('id DESC');
		
		return $row->query()->fetchAll();
		
	}
	
	public function getModelosPorMarca($marca){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelos_11'),
		array('*')
		);
		
		if($marca == "Citroën"){
		
			$row->where("m.marca = 'Citroen'");
		
		}else{

			$row->where("m.marca = '".$marca."'");
			
		}
		
		$row->where("m.cod_fipe != 'null'");

		$row->order('modelo ASC');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	
	public function getModelosPorMarcaTipo($marca, $tipo){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelos_11'),
		array('*')
		);
		
		if($marca == "Citroën"){
		
			$row->where("m.marca = 'Citroen'");
		
		}else{

			$row->where("m.marca = '".$marca."'");
			
		}
		
		$row->where("m.segmento = '".$tipo."'");
		
		//$row->where("m.cod_fipe != 'null'");
		
		$row->where("m.cod_fipe is not null");

		$row->order('modelo ASC');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	
	public function getModeloPorMarca($marca){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelos_11'),
		array('*')
		);
		
		$row->where("m.marca = '".$marca."'");
		  
		$row->order('nome ASC');
		
		return $row->query()->fetchAll();
		
	}
	
	/*	
	public function getModelosPorMarcaVeiculos($marca){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelos_11'),
		array('distinct(m.modelo)','id')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'v.id_modelo = m.id',
			array()
		);
		  
		
		$row->where("m.marca = '".$marca."'");
		  
		$row->order('modelo ASC');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	*/
	
	///////////SOLUÇÂO CITROËN//////////////////////////////
	public function getModelosPorMarcaVeiculos($marca){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelos_11'),
		array('distinct(m.modelo)','id')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'v.id_modelo = m.id',
			array()
		);

		if(!is_array($marca)){
		
			if($marca == "Citroën"){
			
				$row->where("m.marca = 'Citroen' OR m.marca = 'Citro&#235;n'");
			
			}else{

				$row->where("m.marca = '".$marca."'");
				
			}
		
		}else{
		
			if($marca['marca']){
				
				$row->where("m.marca = '".$marca['marca']."'");
			
			}
			
			if($marca['segmento']){
				
				$row->where("m.segmento = '".$marca['segmento']."'");
			
			}
		
		}
		
		$row->where("v.vendido = 0");
		$row->where("v.ativo = 1");
		$row->where("v.excluido = 0");
		$row->where("(v.exibir_site_estoque = 2 OR v.exibir_site_estoque = 3)");

		$row->order('modelo ASC');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	/////////////////////////////////////////////////////////////////////
	
	
	public function getModelosPorMarcaVeiculosNovoUsado($marca, $estado){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelos_11'),
		array('distinct(m.modelo)','id')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'v.id_modelo = m.id',
			array()
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'v.id_empresa = e.id',
			array()
		);
		
		if($marca == "Citroën"){
		
			$row->where("m.marca = 'Citroen' OR m.marca = 'Citro&#235;n'");
		
		}else{

			$row->where("m.marca = '".$marca."'");
			
		}
		
		if($estado != 1){
		
			$row->where("v.novo_usado != 1");
		
		}else{
		
			$row->where("v.novo_usado = 1");
		
		}
		
		$row->where("v.vendido = 0");
		$row->where("v.ativo = 1");
		$row->where("e.ativo = 1");
		$row->where("v.excluido = 0");
		$row->where("(v.exibir_site_estoque = 2 OR v.exibir_site_estoque = 3)");
		  
		$row->order('modelo ASC');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}

	
	public function getLastId(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('p'=>'perfis'),
		array('id')
		);
		
		$row->order('id DESC');
		$row->limit(1);
		return $row->query()->fetchAll();
		
	}
	
	public function getMarcas(){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'marca'),
		array('*')
		);
		
		$row->order('nome ASC');
		
		return $row->query()->fetchAll();
		
	}
	
	
	public function getIdModelo($arr){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelos_11'),
		array('*')
		);

		$row->where("m.marca = '".$arr['marca']."'");

		$row->where("m.modelo = '".$arr['modelo']."'");
		
		$row->where("m.ano_modelo = '".$arr['ano_modelo']."'");
		
		$row->where("m.cod_fipe is not null");

		$row->order('id ASC');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	
	public function atualizaModelos(){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'marca'),
		array('marca'=>'nome')
		);
		
		$row->joinLeft(
			array('mo'=>'modelo'),
			'mo.marca = m.id',
			array('modelo'=>'nome')
		);
		
		$row->joinLeft(
			array('am'=>'ano_modelo'),
			'am.modelo = mo.id',
			array('ano_modelo'=>'nome','codigo'=>'am.id','valor')
		);
		
		$row->order('m.id ASC');
		
		$arrFipe = $row->query()->fetchAll();
		
		$db = $this->getConnDb();
		
		foreach($arrFipe as $fipe){
		
			$x++;
		
			//$sql .= "REPLACE INTO 'modelos_11' (codigo, modelo, marca, ano_modelo, preco) VALUES ('".$fipe['codigo']."','".$fipe['modelo']. " " . substr($fipe['ano_modelo'],4) . "','".$fipe['marca']."','".substr($fipe['ano_modelo'],0,4)."','".$fipe['valor']."');";
			$sql .= "UPDATE 'modelos_11' SET codigo = '".$fipe['codigo']."', modelo='".$fipe['modelo']. " " . substr($fipe['ano_modelo'],4) . "', marca='".$fipe['marca']."', ano_modelo='".substr($fipe['ano_modelo'],0,4)."', preco='".$fipe['valor']."' WHERE modelo = '".$fipe['modelo']. " " . substr($fipe['ano_modelo'],4) . "' AND marca = '".$fipe['marca']."' AND ano_modelo = '".substr($fipe['ano_modelo'],0,4)."';";
			if($x%50 == 0){
			echo str_replace(";", "<br>", $sql);
				//$db->query($sql);
				$sql = "";
				
			}
		
		}
		echo str_replace(";", "<br>", $sql);
                exit;
		//$db->query($sql);	
	
	}

	private function log($query, $descricao){

		$sql = "INSERT INTO log (id_usuario,query,descricao,hora) VALUES (".$_SESSION['sessionUser']['id'].",\"".var_export($query, 1)."\",'".$descricao."',NOW())";
		
		$db->query($sql);

	}

	private function getConnDb(){

		$db = new Zend_Db_Adapter_PDO_MYSQL(array(
			'host'     => HOST,
			'username' => USER,
			'password' => PASS,
			'dbname'   => DB
		));

		return $db;

	}

}
