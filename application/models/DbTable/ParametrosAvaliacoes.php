<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_ParametrosAvaliacoes extends Zend_Db_Table_Abstract
{

	protected $_name = 'parametros_avaliacoes';


	public function getParametros($idEmpresa){

		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('pa'=>'parametros_avaliacoes'),
			array('*')
		);

		$row->where('pa.id_empresa = '.$idEmpresa);

		$row->limit(1);

		return $row->query()->fetchAll();

	}

	
	public function add($dados){
		
		try{

			return $this->insert($dados);
		
		} catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	}

	public function edtIdEmpresa($id, $dados){
		
		try{

			return $this->update($dados, 'id_empresa = ' . $id);

		}catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	} 

	public function del($id){

		$this->delete('id = ' . $id);

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
?>