<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_VersoesIcarros extends Zend_Db_Table_Abstract
{

	protected $_name = 'versoes_icarros';
	
	public function getVersoesIcarros(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('vi'=>'versoes_icarros'),
		array('*')
		);
	
		$row->order('id DESC');

		//$row->limit(1);
		
		return $row->query()->fetchAll();
		
	}
	
	public function getVersoesIcarrosPorFipe($fipe){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('vi'=>'versoes_icarros'),
		array('*')
		);
		
		$row->where("vi.fipe_id = '".$fipe."'");
	
		$row->order('id DESC');

		return $row->query()->fetchAll();
		
	}
	
	public function getModelosVersoesIcarros($marca, $anoModelo){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('vi'=>'versoes_icarros'),
		array('id', 'nome')
		);
		
		$row->joinInner(
			array('m'=>'modelos_11'),
			'vi.fipe_id = m.cod_fipe',
			array('')
		);
		
		$row->where("m.marca = '".$marca."'");
		
		$row->where("vi.ano_inicial <= '".$anoModelo."'");
		
		$row->where("vi.ano_final >= '".$anoModelo."'");
	
		$row->order('nome ASC');

		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	
	public function getVersoesIcarrosPorFipeAno($fipe, $anoModelo){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('vi'=>'versoes_icarros'),
		array('*')
		);
		
		$row->where("vi.fipe_id = '".$fipe."'");
		
		$row->where("vi.ano_inicial <= '".$anoModelo."'");
		
		$row->where("vi.ano_final >= '".$anoModelo."'");
	
		$row->order('id DESC');

		return $row->query()->fetchAll();
		
	}


}
