<?php

class Address extends AddressCore {
	
	public $numend;
	public $compl;
	
	public  function __construct($id_address = NULL, $id_lang = NULL){
		
        self::$definition['fields']['numend'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false, 'size' => 20);
        self::$definition['fields']['compl'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 128);

		parent::__construct($id_address);
	}
	
	public function getFields(){
		
		$add_field = parent::getFields();
		$add_field['numend'] = pSQL($this->numend);
		$add_field['compl'] = pSQL($this->compl);

		return $add_field;
	}
	
}

?>
