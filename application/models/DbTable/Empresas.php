<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Empresas extends Zend_Db_Table_Abstract
{

	protected $_name = 'empresas';
	
	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('e'=>'empresas'),
			array('*')
		);
		
		$row->joinLeft(
			array('u'=>'usuarios'),
			'u.id = e.id_usuario_alteracao',
			array('nome')
		);
		
		if($arr['id']){
		
			$row->where('u.id = ' .$arr['id']);
		
		}
		
		if($arr['id_empresa']){
		
			$row->where('e.id = ' .$arr['id_empresa']);
		
		}
		
		if(!$arr['parcial']){
		
			if($arr['razao_social']){
			
				$row->where("e.razao_social = '" . $arr['razao_social'] . "'");
			
			}
			
			if($arr['cnpj']){
			
				$row->where("e.cnpj = '" . $arr['cnpj'] . "'");
			
			}
			
			if($arr['cidade']){
			
				$row->where("e.cidade = '".$arr['cidade']."'");
			
			}
			
			if($arr['tipo_empresa']){
			
				$row->where("e.tipo_empresa = 1");
			
			}

		}else{
			
			if($arr['razao_social']){
			
				$row->where("e.razao_social LIKE '" . $arr['razao_social'] . "%'");
			
			}
			
			if($arr['cnpj']){
			
				$row->where("e.cnpj LIKE '" . $arr['cnpj'] . "%'");
			
			}
			
			if($arr['cidade']){
			
				$row->where("e.cidade LIKE '" . $arr['cidade'] . "%'");
			
			}
			
			if($arr['tipo_empresa']){
			
				$row->where("e.tipo_empresa = 1");
			
			}
			
		}
		
		$row->where('e.excluido = 0');
	
		$row->order('e.nome_fantasia');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function edt($id, $dados){
		
		try{

			return $this->update($dados, 'id = ' . $id);

		}catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	} 
	
	public function getEmpresa($idEmpresa){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('e'=>'empresas'),
		array('*')
		);
		
		$row->where('e.id = ' .$idEmpresa);

		$row->limit(1);
		
		return $row->query()->fetchAll();
		
	}
	
	public function getEmpresas(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('e'=>'empresas'),
		array('*')
		);
		
		$row->where('e.sistema_site = 0');
		
		$row->where('e.ativo = 1');
		
		$row->order('e.nome_fantasia ASC');
		
		return $row->query()->fetchAll();
		
	}
	
	
	public function getEmpresasPorLogo(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('e'=>'empresas'),
		array('*')
		);
		
		$row->where('e.sistema_site = 0');
		
		$row->where('e.ativo = 1');
		
		$row->order('e.path DESC');
		
		return $row->query()->fetchAll();
		
	}
	
	public function getEmpresasPorLetraNome($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('e'=>'empresas'),
		array('*')
		);
		
		if($arr['letra']){
			
			$row->where("e.nome_fantasia LIKE '" . $arr['letra'] . "%'");
			
		}
		
		if($arr['nome_concessionaria']){
			
			$row->where("e.nome_fantasia LIKE '" . $arr['nome_concessionaria'] . "%'");
			
		}
		
		$row->where('e.sistema_site = 0');
		
		$row->where('e.ativo = 1');
		
		$row->order('e.nome_fantasia ASC');
		
		return $row->query()->fetchAll();
		
	}
	
	public function getCidadeEmpresaPorModelo($idModelo){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('e'=>'empresas'),
		array('distinct(cidade)')
		);
		
		$row->joinInner(
			array('v'=>'veiculos'),
			'e.id = v.id_empresa',
			array()
		);
		
		$row->order('e.cidade ASC');
		
		if($idModelo != "nada"){
		
			$row->where('v.id_modelo = ' .$idModelo);
		
		}

		return $row->query()->fetchAll();
		
	}
	
	public function getLastId(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('e'=>'empresas'),
		array('id')
		);
		  
		$row->order('id DESC');
		$row->limit(1);
		return $row->query()->fetchAll();
		
	}
	
	public function getCidadesEmpresasAnunciantes(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('e'=>'empresas'),
		array('distinct(cidade)')
		);
		  
		$row->order('cidade ASC');

		return $row->query()->fetchAll();
		
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
