<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_TabelaTemporalidades extends Zend_Db_Table_Abstract
{

	protected $_name = 'tabela_temporalidade';
	
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
	
	public function getTabelaTemporalidades(){
	
		return $this->fetchAll();	
	
	}
	
	public function getTabelaTemporalidade($id){
		
		return $this->fetchAll("id = $id");
	
	}
	
	/*public function getUsuarioCompleto(){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('*')
		);
		
		$row->joinInner(
			array('p'=>'perfis'),
			'p.id = u.id_perfil',
			array('perfil')
		);		
	
		$row->order('p.perfil');
		$row->order('u.login');
		
		return $row->query()->fetchAll();
	
	}*/

}