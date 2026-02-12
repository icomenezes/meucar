<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_PropagandasSite extends Zend_Db_Table_Abstract
{

	protected $_name = 'propagandas_site';
	
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
		
			$row->where('u.id = ' . $arr['id']);
		
		}
		
		if(!$arr['parcial']){
		
			if($arr['razao_social']){
			
				$row->where("e.razao_social = '" . $arr['razao_social'] . "'");
			
			}
			
			if($arr['cnpj']){
			
				$row->where("e.cnpj = '" . $arr['cnpj'] . "'");
			
			}
			
			if($arr['cidade']){
			
				$row->where("e.cidade = '" . $arr['cidade'] . "'");
			
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
			
		}
	
		$row->order('e.razao_social');
		
		//echo $row->__toString();
		
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

	public function getImagensSlides($empresa_id = null){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('ps'=>'propagandas_site'),
		array('*')
		);
        if ($empresa_id) {
            $row->where('ps.id_empresa = ?', $empresa_id);
        }
		$row->where('ps.tipo_propaganda = 1');
		$row->order('ps.data_upload DESC');

		return $row->query()->fetchAll();
		
	}
	
	public function getImagens(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('ps'=>'propagandas_site'),
		array('*')
		);
		
		$row->where('ps.tipo_propaganda = 0');
		$row->order('ps.data_upload DESC');

		return $row->query()->fetchAll();
		
	}
	
	public function getImagensPaisagem(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('ps'=>'propagandas_site'),
		array('*')
		);
		
		$row->where('ps.tipo_imagem = 2');
		$row->where('ps.tipo_propaganda != 1');
		
		$row->order('ps.data_upload DESC');

		return $row->query()->fetchAll();
		
	}
	
	public function getImagensRetrato(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('ps'=>'propagandas_site'),
		array('*')
		);
		
		$row->where('ps.tipo_imagem = 1');
		
		$row->order('ps.data_upload DESC');

		return $row->query()->fetchAll();
		
	}
	
	public function getImagem($id){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('ps'=>'propagandas_site'),
		array('*')
		);
		
		$row->where('ps.id = ' .$id);
		
		$row->order('ps.data_upload DESC');

		return $row->query()->fetchAll();
		
	}
	
	public function edt($id, $dados){
		
		try{

			return $this->update($dados, 'id = ' . $id);

		}catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
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
