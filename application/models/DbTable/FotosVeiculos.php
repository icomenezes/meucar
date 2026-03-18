<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_FotosVeiculos extends Zend_Db_Table_Abstract
{

	protected $_name = 'fotos_veiculos';
	
	public function add($dados){
		
		try{
            if(isset($dados['path'])){
                $dados['path'] = str_replace('\\', '', $dados['path']);
            }
			
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

		$this->delete('id = ' . $id);

	}
	
	public function getPerfis(){
	
		return $this->fetchAll();	
	
	}
	
	public function getPathFoto($id){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fv'=>'fotos_veiculos'),
		array('*')
		);
		
		$row->where("fv.id = ".$id);
		
		return $row->query()->fetchAll();
		
	}
	
	public function getFotosVeiculoSelecionado($id){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fv'=>'fotos_veiculos'),
		array('*')
		);
		
		$row->where("fv.id_veiculo = ".$id);
		  
		$row->order('id DESC');
		
		return $row->query()->fetchAll();
		
	}
	
	public function getFotosVeiculoIcarros($id){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fv'=>'fotos_veiculos'),
		array('*')
		);
		
		$row->where("fv.id_veiculo = ".$id);
		  
		$row->order('capa DESC');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	
	public function getNFotos($id = false){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fv'=>'fotos_veiculos'),
		array('total' => 'COUNT(*)')
		);
		
		if($id){
		
			$row->where("fv.id_veiculo = ".$id);
			
		}
		  
		$row->group('id_veiculo');
		
		return $row->query()->fetchAll();
		
	}
	
	public function getNFotosPorEmpresa($id_empresa){

		$db = $this->getAdapter();
		$sql = "SELECT fv.id_veiculo, COUNT(*) as total
				FROM fotos_veiculos fv
				INNER JOIN veiculos v ON v.id = fv.id_veiculo
				WHERE v.id_empresa = ? AND v.excluido = 0
				GROUP BY fv.id_veiculo";
		return $db->fetchAll($sql, array($id_empresa));

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
	
	public function getQuantidadeFotos($id){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('fv'=>'fotos_veiculos'),
		array('qtd'=>'count(*)')
		);
		
		$row->joinInner(
			array('fv2'=>'fotos_veiculos'),
			'fv.id_veiculo = fv2.id_veiculo',
			array('')
		);
		
		$row->where("fv.id = ".$id);
		
		return $row->query()->fetchAll();
		
	}
	
	public function getModelosPorMarca($marca){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'modelos_11'),
		array('*')
		);
		
		$row->where("m.marca = '".$marca."'");
		  
		$row->order('id ASC');
		
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
		
			$sql .= "REPLACE INTO 'modelos_11' (codigo, modelo, marca, ano_modelo, preco) VALUES ('".$fipe['codigo']."','".$fipe['modelo']. " " . substr($fipe['ano_modelo'],4) . "','".$fipe['marca']."','".substr($fipe['ano_modelo'],0,4)."','".$fipe['valor']."');";
			
			if($x%50 == 0){
			
				$db->query($sql);
				$sql = "";
				
			}
		
		}
		
		$db->query($sql);	
	
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
