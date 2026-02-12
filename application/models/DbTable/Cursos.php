<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Cursos extends Zend_Db_Table_Abstract
{

	protected $_name = 'cursos';
	
	public function add($dados){
		
		try{

			return $this->insert($dados);
		
		} catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	}

	public function edt($id, $dados){
		
		try{

			return $this->update($dados, 'id_veiculo = ' . $id);

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
	
	public function getFornecedor($id){
		
		return $this->fetchAll("id = $id");
	
	}
	
	public function getQtdCursosPorUsuario($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('c'=>'cursos'),
		array('*')
		);
		
		if($arr['data_inicial']){
			$row->where("c.data >= '".$arr['data_inicial']." 00:00:00'");
		}
		
		if($arr['data_final']){
			$row->where("c.data <='".$arr['data_final']." 23:59:59'");
        }
        
        if($arr['id_usuario']){
			$row->where("c.id_usuario = ".$arr['id_usuario']);
		}
		  
		$row->order('id DESC');
		
		return $row->query()->fetchAll();
		
	}

}
