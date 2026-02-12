<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_ModelosEmails extends Zend_Db_Table_Abstract
{

	protected $_name = 'modelos_emails';
	
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

		$this->delete('id = ' . $id);

	}
	
	public function getPerfis(){
	
		return $this->fetchAll();	
	
	}
	
	public function getModeloEmail($id){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('me'=>'modelos_emails'),
		array('*')
		);
		
		$row->where("me.id = ".$id);
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
		
	}
	
	public function getModelosEmails(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('me'=>'modelos_emails'),
		array('*')
		);
		
		$row->where("(me.id_empresa is null AND me.id_usuario is null) OR (me.id_empresa = ".$_SESSION['sessionUser']['id_empresa']." AND me.id_usuario = ".$_SESSION['sessionUser']['id'].") OR (me.id_empresa = ".$_SESSION['sessionUser']['id_empresa']." AND me.id_usuario is null)");

		//echo $row->__toString();
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
