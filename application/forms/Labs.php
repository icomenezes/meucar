<?php

class Application_Form_Labs extends Zend_Form
{

	public function init(){

		$this->setName('clientes');
		$this->setAction('add');
		$this->setAttrib('class','form');

		$oculto = new Zend_Form_Element_Hidden('oculto');

		$select = new Zend_Form_Element_Select('select');
		$select->addMultiOptions(array('1'=>'um', '2' => 'dois'))
			->setAttrib("title","Mensagem ao colocar o mouse em cima.")
			->setAttrib("onChange","")
			->setLabel("Select");

		$texto = new Zend_Form_Element_Text('texto');
		$texto ->setLabel('Textarea')
			->setRequired(true)
			->setAttrib("title","Mensagem ao colocar o mouse em cima.")
			->addValidator('NotEmpty');

		$submit = new Zend_Form_Element_Submit('Submit');
		
		$this->addElements(array($select, $texto, $submit, $oculto));

	}


}

