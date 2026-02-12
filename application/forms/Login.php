<?php

class Application_Form_Login extends Zend_Form {

    public function  init() {
	
		$this->setName('logon');
		$this->setAction('/index/logar');
		$this->setAttrib('class','form');

		$login = new Zend_Form_Element_Text('user');
		$login ->setLabel('Login')
			->setRequired(true)
			->setAttrib('class','text t20')
			->addValidator('NotEmpty');

		$senha = new Zend_Form_Element_Password('senha');
		$senha ->setLabel('Senha')
			->setRequired(true)
			->setAttrib('class','text t20')
			->addValidator('NotEmpty');
			
		$submit = new Zend_Form_Element_Submit('Logar');
		$submit->setAttrib('class','btn_login');
		
		$this->addElements(array($login, $senha, $submit, $esqueci = false));

    }
 
}
?>
