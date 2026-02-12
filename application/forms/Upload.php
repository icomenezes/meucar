<?php

class Application_Form_Upload extends Zend_Form {

    public function  init() {

		$this->setName('upload');
		$this->setAction('');
		$this->setAttrib('class','form');

		$planilha = new Zend_Form_Element_File('planilha');
		$planilha->addValidator( new Zend_Validate_File_Extension('csv') )
			->setLabel('Upload do arquivo (csv)');
		
		$submit = new Zend_Form_Element_Submit('Salvar');
		
		$this->addElements(array($planilha, $submit));

    }
 
}
?>
