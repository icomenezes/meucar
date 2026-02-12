<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Clientes extends Zend_Db_Table_Abstract
{

	protected $_name = 'clientes';
	
	
	public function getClienteNome($nome){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('c'=>'clientes'),
		array('*')
		);
		
		$row->where("c.nome = '".$nome."'");

		$row->limit(1);
		return $row->query()->fetchAll();
		
	}
	
	
	public function edt($id, $dados){
		
		try{

			return $this->update($dados, 'id = ' . $id);

		}catch (Exception $e){
		
			echo $e->getMessage();
		
		}
		
	} 
	
	
	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('c'=>'clientes'),
			array('*')
		);
		
		$row->joinLeft(
			array('u'=>'usuarios'),
			'u.id = c.id_usuario_alteracao',
			array('nome_alterou'=>'u.nome')
		);
		
		if(isset($arr['compraInicial']) || isset($arr['compraFinal'])){
		
			$row->joinInner(
				array('n'=>'negociacoes'),
				'c.id = n.id_cliente',
				array('n.data_abertura')
			);
		
		}
		
		if(isset($arr['id'])){
		
			$row->where('c.id = ' . $arr['id']);
		
		}
		
		if(isset($arr['parcial']) && $arr['parcial'] == true){

			if(isset($arr['nome'])){
			
				$row->where("c.nome LIKE '" . $arr['nome'] . "%'");
			
			}
			
			if(isset($arr['cpf'])){
			
				$row->where("c.cpf LIKE '" . $arr['cpf'] . "%'");
			
			}
			
			if(isset($arr['cidade'])){
			
				$row->where("c.cidade LIKE '" . $arr['cidade'] . "%'");
			
			}

			if(isset($arr['mes_niver'])){
				$row->where("MONTH(nascimento) = ".$arr['mes_niver']);
			}

		}else{
			
			if(isset($arr['nome'])){
			
				$row->where("c.nome = '" . $arr['nome'] . "'");
			
			}
			
			if(isset($arr['cpf'])){
			
				$row->where("c.cpf = '" . $arr['cnpj'] . "'");
			
			}
			
			if(isset($arr['cidade'])){
			
				$row->where("c.cidade = '" . $arr['cidade'] . "'");
			
			}
			
		}		
		
		if(isset($arr['nascInicial']) && isset($arr['nascFinal'])) {
			
			$row->where("c.nascimento BETWEEN '".$arr['nascInicial']."' AND '".$arr['nascFinal']."'");
		
		}
		
		// if($arr['compraInicial'] && $arr['compraFinal']){
			
		// 	$row->where("n.data_abertura BETWEEN '".$arr['compraInicial']." 00:00:01' AND '".$arr['compraFinal']." 23:59:59'");
		
		// }

		if(isset($arr['compraInicial'])){
			$row->where("n.data_abertura >= '".$arr['compraInicial']." 00:00:00'");
		}

		if(isset($arr['compraFinal'])){
			$row->where("n.data_abertura <= '".$arr['compraFinal']." 23:59:59'");
		}
		
		if(isset($arr['hora_alteracao_inicial']) && isset($arr['hora_alteracao_final'])) {
			
			$row->where("c.hora_alteracao BETWEEN '".$arr['hora_alteracao_inicial']." 00:00:01' AND '".$arr['hora_alteracao_final']." 23:59:59'");
		
		}
		
		if(isset($arr['aniversariante'])){
			
			$row->where("c.nascimento LIKE '%" . @date('-m-d') . "'");
		
		}
	
		if(!isset($arr['corretora'])){
	
			$row->where("c.id_empresa = " . $_SESSION['sessionUser']['id_empresa']);
		
		}
	
		if(isset($arr['lista']) && $arr['lista'] == true) {
		
			$row->order('c.hora_alteracao DESC');

		}else{
	
			$row->order('c.nome');
		
		}
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function getLastId(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('c'=>'clientes'),
		array('id')
		);
		  
		$row->order('id DESC');
		$row->limit(1);
		return $row->query()->fetchAll();
		
	}
	
	public function getClienteContrato($idCliente){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('c'=>'clientes'),
		array('*')
		);
		
		$row->where('c.id = ' .$idCliente);
		  
		$row->order('nome');
		
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
	
	public function buscaNome($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('c'=>'clientes'),
		array('nome')
		);
		
		$row->where('c.id = ' .$arr);
		  
		$row->order('nome');
		
		//echo $row->__toString();
		return $row->query()->fetchAll();
		
	}
	
	public function aniversario($arr){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('c'=>'clientes'),
			array('*')
		);
	
		if(isset($arr['aniversariante']) && $arr['aniversariante']){
			
			$row->where("c.nascimento LIKE '%" . @date('-m-d') . "'");
		
		}
		
		if($arr['antes']){
			
			$row->where("c.nascimento LIKE '%" . $arr['antes'] . "' OR c.nascimento LIKE '%" . $arr['hoje'] . "' OR c.nascimento LIKE '%" . $arr['amanha'] . "' OR c.nascimento LIKE '%" . $arr['depois'] . "' OR c.nascimento LIKE '%" . $arr['ultimo'] . "'");
		
		}
		
		$row->where("c.id_empresa = " . $_SESSION['sessionUser']['id_empresa']);
	
		$row->order('c.nome');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function clienteEmpresaAniversario() {

		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('c'=>'clientes'),
			array('*')
		);

		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = c.id_empresa',
			array('nome_fantasia','path','url_site', 'empresa_email'=>'email')
		);

		//$row->where("e.id = ".$_SESSION['sessionUser']['id_empresa']);
		//$row->where("e.id = 2");

		$row->where("e.ativo = 1");

		$row->where("c.nascimento LIKE '%" . @date('-m-d') . "'");

		$row->order('c.id DESC');

		//$row->limit(100);

		return $row->query()->fetchAll();

	}

	public function clienteEmpresaNegociacao($date){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('c'=>'clientes'),
			array('distinct(c.id)','nome', 'email','sexo')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = c.id_empresa',
			array('nome_fantasia','path','url_site')
		);
		
		$row->joinInner(
			array('n'=>'negociacoes'),
			'c.id = n.id_cliente',
			array('')
		);
			
		$row->where("data_concretizacao <= '".$date." 23:59:59'");
		$row->where("data_concretizacao >= '".$date." 00:00:00'");
		
		$row->where("e.ativo = 1");
		
		$row->where("c.email LIKE '%@%'");
	
		$row->order('c.nome');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function _getClientesMesAniversario($mes){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('c'=>'clientes'),
			array('distinct(c.id)','nome', 'email','sexo')
		);
		
		$row->joinInner(
			array('e'=>'empresas'),
			'e.id = c.id_empresa',
			array('nome_fantasia','path','url_site')
		);
		
		//var_export(substr('2013-12-12',5,-3));

		$row->where("MONTH(nascimento) = '".$mes."'");
		
		$row->where("c.email LIKE '%@%'");
		
		$row->where("e.ativo = 1");
	
		$row->order('c.nome');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}
	
	public function getCpfExistente($cpf, $idEmpresa){
	
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('c'=>'clientes'),
			array('*')
		);

		$row->where("id_empresa = ".$idEmpresa);
		
		$row->where("cpf = '".$cpf."'");
		
		//$row->limit(1);

		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}

	public function getClientePorEmpresa($idEmpresa){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('c'=>'clientes'),
		array('*')
		);
		
		$row->where('c.id_empresa = ' .$idEmpresa);
		  
		$row->order('nome');

		//$row->limit(1);
		
		return $row->query()->fetchAll();
		
	}

}
