<?php

class Customer extends CustomerCore {

    public $tipo;
    public $cpf_cnpj;
    public $rg_ie;

    public function __construct($id = null) {

        self::$definition['fields']['tipo'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false, 'size' => 2);
        self::$definition['fields']['cpf_cnpj'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false, 'size' => 20);
        self::$definition['fields']['rg_ie'] = array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false, 'size' => 20);

        parent::__construct($id);
    }
    
	public function getFields() {

        $add_field = parent::getFields();
        $add_field['tipo'] = pSQL($this->tipo);
        $add_field['cpf_cnpj'] = pSQL($this->cpf_cnpj);
        $add_field['rg_ie'] = pSQL($this->rg_ie);

        return $add_field;
    }

	public function add($autodate = false, $nullValues = true) {

        if (Context::getContext()->controller->controller_type == 'front') {
            if ($this->tipo == 'pj') {
                $this->id_default_group = Configuration::get('FKCUSTOMERS_GRUPO');
            }
        }

        return parent::add();
    }

	public function update($nullValues = false) {

        if (Context::getContext()->controller->controller_type == 'front') {

            if ($this->tipo == 'pj') {
                $this->id_default_group = Configuration::get('FKCUSTOMERS_GRUPO');
            }

            $this->cleanGroups();
            $this->addGroups(array($this->id_default_group));
        }

        return parent::update();
    }

}

?>
