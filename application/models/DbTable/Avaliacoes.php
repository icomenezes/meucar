<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Avaliacoes extends Zend_Db_Table_Abstract
{

	protected $_name = 'avaliacoes';


	public function getAvaliacao($idEmpresa, $id){

		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('a'=>'avaliacoes'),
			array('*')
		);

		$row->joinLeft(
			array('mo'=>'modelo'),
			'a.modelo = mo.id',
			array('nome_modelo'=>'nome')
		);

		if($idEmpresa == 3 || $idEmpresa == 239){
			$row->where('a.id_empresa = 3 OR a.id_empresa = 239');
		}else{
			$row->where('a.id_empresa = '.$idEmpresa);
		}
		
		$row->where('a.id = '.$id);

		$row->order('a.id DESC');

		$row->limit(1);

		return $row->query()->fetchAll();

	}


	public function getAvaliacoesVendedor($idEmpresa, $idUsuario){

		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('a'=>'avaliacoes'),
			array('id', 'data', 'nome', 'aprovada', 'solicitar_liberacao','id_app', 'id_usuario','placa')
		);

		$row->joinLeft(
			array('mo'=>'modelo'),
			'a.modelo = mo.id',
			array('nome_modelo'=>'nome')
		);

		$row->joinLeft(
			array('u'=>'usuarios'),
			'u.id = a.id_usuario',
			array('avaliador'=>'u.nome')
		);

		$row->where('a.id_empresa = '.$idEmpresa);

		$row->where('a.id_usuario = '.$idUsuario);

		$row->order('a.id DESC');

		$row->limit(500);

		return $row->query()->fetchAll();

	}


	public function getAvaliacoesSupervisor($idEmpresa){

		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('a'=>'avaliacoes'),
			array('id', 'data', 'nome', 'aprovada', 'solicitar_liberacao','id_app','placa')
		);

		$row->joinLeft(
			array('mo'=>'modelo'),
			'a.modelo = mo.id',
			array('nome_modelo'=>'nome')
		);

		$row->joinLeft(
			array('u'=>'usuarios'),
			'u.id = a.id_usuario',
			array('avaliador'=>'u.nome')
		);

		$row->where('a.id_empresa = '.$idEmpresa);

		$row->order('a.id DESC');

		$row->limit(200);

		return $row->query()->fetchAll();

	}


	public function getAvaliacoes($idEmpresa){

		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('a'=>'avaliacoes'),
			array('id', 'data', 'nome', 'aprovada', 'solicitar_liberacao','id_app','placa')
		);

		$row->joinLeft(
			array('mo'=>'modelo'),
			'a.modelo = mo.id',
			array('nome_modelo'=>'nome')
		);

		$row->joinLeft(
			array('u'=>'usuarios'),
			'u.id = a.id_usuario',
			array('avaliador'=>'u.nome')
		);

		if($idEmpresa == 3 || $idEmpresa == 239){
			$row->where('a.id_empresa = 3 OR a.id_empresa = 239');
		}else{
			$row->where('a.id_empresa = '.$idEmpresa);
		}


		$row->order('a.id DESC');

		$row->limit(200);

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

			return $this->update($dados, 'id = '.$id);

		}catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	} 


	public function getAvaliacaoPlaca($idEmpresa, $placa){

		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('a'=>'avaliacoes'),
			array('*')
		);

		if($idEmpresa == 3 || $idEmpresa == 239){
			$row->where('a.id_empresa = 3 OR a.id_empresa = 239');
		}else{
			$row->where('a.id_empresa = '.$idEmpresa);
		}
		
		$row->where("a.placa = '".substr($placa, 0, 3)."-".substr($placa, -4)."' OR a.placa = '".str_replace("-", "", $placa)."'");

		$row->limit(1);

		//echo $row->__toString();

		return $row->query()->fetchAll();

	}

}

?>