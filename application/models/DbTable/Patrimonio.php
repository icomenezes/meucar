<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Patrimonio extends Zend_Db_Table_Abstract
{

	protected $_name = 'patrimonio';
	
	
	public function getPatrimonio($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('p'=>'patrimonio'),
		array('id')
		);
		
		if($arr['id_empresa'] && $arr['data']){
		
			$row->where("p.id_empresa = ".$arr['id_empresa']." AND p.data = '".$arr['data']."'");
		
			//echo $row->__toString();
			return $row->query()->fetchAll();
		
		}

	}
	
	public function getPatrimonios($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('p'=>'patrimonio'),
		array('*')
		);
		
		if(isset($arr['id_empresa'])){
		
			$row->where("p.id_empresa = ".$arr['id_empresa']);
		
		}
		
		if(isset($arr['data_inicial']) && $arr['data_inicial'] != ""){
		
			$row->where("p.data >= '".$arr['data_inicial']."'");
		
		}
		
		if(isset($arr['data_final']) && $arr['data_final'] != ""){
		
			$row->where("p.data <= '".$arr['data_final']."'");
		
		}
		
		$row->order("data ASC");
		
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

			return $this->update($dados, 'id= ' . $id);

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
	
	public function getPerfil($id){
		
		return $this->fetchAll("id_ = ".$id);
	
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
