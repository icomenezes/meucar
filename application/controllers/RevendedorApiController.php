
<?php

header("Access-Control-Allow-Origin: https://www.selectveiculos.com.br");

class RevendedorApiController extends Zend_Controller_Action {

	public function indexAction()
	{

		$arr['id'] = 'teste';

		$this->_helper->json($arr);
	}

	public function getAction()
	{
	  $this->getResponse()->setBody('Foo!');
	  $this->getResponse()->setHttpResponseCode(200);
	}

	public function postAction() {

		$dbRevendedores = new Application_Model_DbTable_Revendedores();

		$_POST['senha'] = sha1(substr($_POST['nome'], 0, 3).substr(str_replace(".", "", $_POST['cnpj']), 0, 5));

		if($dbRevendedores->add($_POST)) {
			$arrResponse = array('status' => 201, 'message' => 'Cadastrado com sucesso');
		}else{
			$arrResponse = array('status' => 999, 'message' => 'Erro: não foi possível cadastrar os dados');
		}

		$this->_helper->json($arrResponse);

	}

	public function postLoginAction() {

		$dbRevendedores = new Application_Model_DbTable_Revendedores();
		$arrResponse = $dbRevendedores->getLogin($_POST);
		if(isset($arrResponse[0]) && $arrResponse[0]['email'] == $_POST['login'] && $arrResponse[0]['senha'] == sha1($_POST['senha'])) {
			$arrResponse = array('status' => 200, 'logado' => true);
		}else{
			$arrResponse = array('status' => 999, 'message' => 'Erro: não foi possível logar');
		}

		$this->_helper->json($arrResponse);

	}

	public function putAction()
	{
	  $this->getResponse()->setBody('resource updated');
	  $this->getResponse()->setHttpResponseCode(200);
	}

	public function deleteAction()
	{
	  $this->getResponse()->setBody('resource deleted');
	  $this->getResponse()->setHttpResponseCode(200);
	}

}

?>