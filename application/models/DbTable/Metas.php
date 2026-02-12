<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Metas extends Zend_Db_Table_Abstract
{

	protected $_name = 'metas';
	
	public function add($dados){
		
		try{

			return $this->insert($dados);
		
		} catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	}

	public function edtPeloPerfil($idPerfil, $dados){
		
		try{

			return $this->update($dados, 'id_perfil = '.$idPerfil.' AND id_empresa = ' . $_SESSION['sessionUser']['id_empresa']);

		}catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	} 

	public function del($id){

		$this->delete('id = ' . $id);

	}
	
	public function get(){
		
		$row->order('descricao ASC');
	
		return $this->fetchAll();	
	
	}
	
	public function getOpcional($id){
		
		return $this->fetchAll("id = $id");
	
	}
	
	public function getMetasPerfil($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('m'=>'metas'),
		array('*')
		);

		$row->where('m.id_perfil = ' .$arr['id_perfil']);
		$row->where('m.id_empresa = ' .$arr['id_empresa']);

		return $row->query()->fetchAll();
		
	}

}
