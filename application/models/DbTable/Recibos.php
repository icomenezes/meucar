<?php

class Application_Model_DbTable_Recibos extends Zend_Db_Table_Abstract
{

	protected $_name = 'recibos';
	
	private function getConnDb(){

		$db = new Zend_Db_Adapter_PDO_MYSQL(array(
			'host'     => HOST,
			'username' => USER,
			'password' => PASS,
			'dbname'   => DB
		));

		return $db;

	}

	
	public function _get($arr = array()){
	
		//var_export($arr);	 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('r'=>'recibos'),
			array('*')
		);
				
		$row->joinLeft(
			array('u'=>'usuarios'),
			'r.id_usuario_emitiu = u.id',
			array('id_perfil', 'emitiu'=>'nome')
		);
		
		$row->joinLeft(
			array('c'=>'clientes'),
			'r.id_cliente = c.id',
			array('cliente'=>'nome')
		);
		
		if($arr['data_inicial'] || $arr['data_final']){
			
			$row->where("r.data BETWEEN '".$arr['data_inicial']."' AND '".$arr['data_final']."'");
			
		}
		
		if($arr['id_cliente']){
		
			$row->where('r.id_cliente = ' . $arr['id_cliente']);
		
		}
		
		if($arr['id_concessionaria']){
		
			$row->where('r.id_concessionaria = ' . $arr['id_concessionaria']);
		
		}
		
		if($arr['id_empresa']){
		
			$row->where('r.id_empresa = ' . $arr['id_empresa']);
		
		}
		
		if($arr['valor']){
		
			$row->where('r.valor = ' . $arr['valor']);
		
		}
		
		if($arr['referente']){
		
			$row->where('r.referente = ' . $arr['referente']);
		
		}
		
		if($arr['data']){
		
			$row->where('r.data = ' . $arr['data']);
		
		}
		
		if($arr['obs']){
		
			$row->where('r.obs = ' . $arr['obs']);
		
		}
		
		if($arr['data_hora']){
		
			$row->where('r.data_hora = ' . $arr['data_hora']);
		
		}
		
		if($arr['id_usuario_emitiu']){
		
			$row->where('r.id_usuario_emitiu =' . $arr['id_usuario_emitiu']);
		
		}
		
		$row->order('r.id DESC');
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
	
	}
	
	

}

?>