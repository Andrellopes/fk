<?php

class AdminAddressesController extends AdminAddressesControllerCore {

    public function __construct() {
        
        parent::__construct();

        $this->_select = 'cl.`name` as country, d.`name` as estado';
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'country_lang` cl
                            ON (cl.`id_country` = a.`id_country` AND cl.`id_lang` = '.(int)$this->context->language->id.')
                        LEFT JOIN `'._DB_PREFIX_.'customer` c
                            ON a.id_customer = c.id_customer
                        LEFT JOIN `'._DB_PREFIX_.'state` d
                            ON a.id_state = d.id_state';

        $this->_where = 'AND a.id_customer != 0 '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'c');

        $countries = Country::getCountries($this->context->language->id);
        foreach ($countries as $country)
            $this->countries_array[$country['id_country']] = $country['name'];

        $this->fields_list = array(
            'id_address' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'firstname' => array(
                'title' => $this->l('First Name'),
                'filter_key' => 'a!firstname'
            ),
            'lastname' => array(
                'title' => $this->l('Last Name'),
                'filter_key' => 'a!lastname'
            ),
            'postcode' => array(
                'title' => $this->l('Zip/Postal Code'),
                'align' => 'right'
            ),
            'address1' => array(
                'title' => $this->l('Address')
            ),

        );

        if (Configuration::get('FKCUSTOMERS_MODO') == '1') {

            $this->fields_list = array_merge($this->fields_list, array(
                'numend' => array(
                    'title' => $this->l('Número')
                ),
                'compl' => array(
                    'title' => $this->l('Complemento')
                ),
            ));
        }


        $this->fields_list = array_merge($this->fields_list, array(
            'address2' => array(
                'title' => $this->l('Bairro')
            ),
            'city' => array(
                'title' => $this->l('City')
            ),
            'estado' => array(
                'title' => $this->l('Estado')
            ),
            'country' => array(
                'title' => $this->l('Country'),
                'type' => 'select', 'list' => $this->countries_array,
                'filter_key' => 'cl!id_country')
        ));

    }

    public function initContent() {
        // Inclui os cookies que serao utilizados pelo js
        include_once(_PS_MODULE_DIR_.'fkcustomers/includes/variaveis_cookie.php');

        parent::initContent();
    }
    
    public function renderForm() {

        // TODO: alterar quando mudar versão do Prestashop
        if (version_compare(_PS_VERSION_, '1.6.0.14', '<=')) {
            return $this->v1_6_0_5();
        }else {
            return $this->v1_6_1_0();
        }

    }

    public function processSave() {

        include_once(_PS_MODULE_DIR_.'fkcustomers/models/FKcustomersClass.php');

        // Instancia FKcustomersClass
        $fkcustomersClass = new FKcustomersClass();

        if ( Configuration::get('FKCUSTOMERS_MODO') == '1') {
            // Numero
            if (!Tools::getValue('numend')) {
                $this->errors[] = Tools::displayError('O campo Número é obrigatório.');
            }
        }

        // Telefone
        $telefone = Tools::getValue('phone');

        if ($telefone) {
            if (!$fkcustomersClass->validaDDD($telefone)) {
                $this->errors[] = Tools::displayError('DDD do Telefone é inválido.');
            }
        }

        // Celular
        $celular = Tools::getValue('phone_mobile');

        if ($celular) {
            if (!$fkcustomersClass->validaDDD($celular)) {
                $this->errors[] = Tools::displayError('DDD do Celular é inválido.');
            }
        }


        parent::processSave();
    }

    public function setMedia() {

        parent::setMedia();

        $this->addJS(_PS_JS_DIR_.'jquery/plugins/fancybox/jquery.fancybox.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/jquery.maskedinput.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_cookie.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_cpf.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_cnpj.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_cep.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_endereco.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_admin.js');
    }

    private function v1_6_0_5() {

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Addresses'),
                'icon' => 'icon-envelope-alt'
            ),
            'input' => array(
                array(
                    'type' => 'text_customer',
                    'label' => $this->l('Customer'),
                    'name' => 'id_customer',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Identification Number'),
                    'name' => 'dni',
                    'required' => false,
                    'col' => '4',
                    'hint' => $this->l('DNI / NIF / NIE')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Address alias'),
                    'name' => 'alias',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Home phone'),
                    'name' => 'phone',
                    'required' => false,
                    'col' => '4',
                    'hint' => Configuration::get('PS_ONE_PHONE_AT_LEAST') ? sprintf($this->l('You must register at least one phone number')) : ''
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Mobile phone'),
                    'name' => 'phone_mobile',
                    'required' => false,
                    'col' => '4',
                    'hint' => Configuration::get('PS_ONE_PHONE_AT_LEAST') ? sprintf($this->l('You must register at least one phone number')) : ''
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Other'),
                    'name' => 'other',
                    'required' => false,
                    'cols' => 15,
                    'rows' => 3,
                    'hint' => $this->l('Forbidden characters:').' &lt;&gt;;=#{}'
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );
        $id_customer = (int)Tools::getValue('id_customer');
        if (!$id_customer && Validate::isLoadedObject($this->object))
            $id_customer = $this->object->id_customer;
        if ($id_customer)
        {
            $customer = new Customer((int)$id_customer);
            $token_customer = Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)$this->context->employee->id);
        }

        $this->tpl_form_vars = array(
            'customer' => isset($customer) ? $customer : null,
            'tokenCustomer' => isset ($token_customer) ? $token_customer : null
        );

        // Order address fields depending on country format
        $addresses_fields = $this->processAddressFormat();
        // we use  delivery address
        $addresses_fields = $addresses_fields['dlv_all_fields'];

        $temp_fields = array();

        foreach ($addresses_fields as $addr_field_item)
        {
            if ($addr_field_item == 'company')
            {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Company'),
                    'name' => 'company',
                    'required' => false,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                );
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('VAT number'),
                    'col' => '2',
                    'name' => 'vat_number'
                );
            }
            else if ($addr_field_item == 'lastname')
            {
                if (isset($customer) &&
                    !Tools::isSubmit('submit'.strtoupper($this->table)) &&
                    Validate::isLoadedObject($customer) &&
                    !Validate::isLoadedObject($this->object))
                    $default_value = $customer->lastname;
                else
                    $default_value = '';

                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Last Name'),
                    'name' => 'lastname',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' 0-9!&amp;lt;&amp;gt;,;?=+()@#"�{}_$%:',
                    'default_value' => $default_value,
                );
            }
            else if ($addr_field_item == 'firstname')
            {
                if (isset($customer) &&
                    !Tools::isSubmit('submit'.strtoupper($this->table)) &&
                    Validate::isLoadedObject($customer) &&
                    !Validate::isLoadedObject($this->object))
                    $default_value = $customer->firstname;
                else
                    $default_value = '';

                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('First Name'),
                    'name' => 'firstname',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' 0-9!&amp;lt;&amp;gt;,;?=+()@#"�{}_$%:',
                    'default_value' => $default_value,
                );
            }
            else if ($addr_field_item == 'address1')
            {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Address'),
                    'name' => 'address1',
                    'col' => '6',
                    'required' => true,
                );
            }
            else if ($addr_field_item == 'numend' and Configuration::get('FKCUSTOMERS_MODO') == '1')
            {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Número'),
                    'name' => 'numend',
                    'col' => 6,
                    'required' => true,
                );
            }
            else if ($addr_field_item == 'compl' and Configuration::get('FKCUSTOMERS_MODO') == '1')
            {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Complemento'),
                    'name' => 'compl',
                    'col' => 6,
                    'required' => false,
                );
            }
            else if ($addr_field_item == 'address2')
            {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Address').' (2)',
                    'name' => 'address2',
                    'col' => '6',
                    'required' => false,
                );
            }
            elseif ($addr_field_item == 'postcode')
            {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Zip/Postal Code'),
                    'name' => 'postcode',
                    'col' => '2',
                    'required' => true,
                );
            }
            else if ($addr_field_item == 'city')
            {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('City'),
                    'name' => 'city',
                    'col' => '4',
                    'required' => true,
                );
            }
            else if ($addr_field_item == 'country' || $addr_field_item == 'Country:name')
            {
                $temp_fields[] = array(
                    'type' => 'select',
                    'label' => $this->l('State'),
                    'name' => 'id_state',
                    'required' => false,
                    'col' => '4',
                    'options' => array(
                        'query' => array(),
                        'id' => 'id_state',
                        'name' => 'name'
                    )
                );

                $temp_fields[] = array(
                    'type' => 'select',
                    'label' => $this->l('Country'),
                    'name' => 'id_country',
                    'required' => false,
                    'col' => '4',
                    'default_value' => (int)$this->context->country->id,
                    'options' => array(
                        'query' => Country::getCountries($this->context->language->id),
                        'id' => 'id_country',
                        'name' => 'name'
                    )
                );

            }
        }

        // merge address format with the rest of the form
        array_splice($this->fields_form['input'], 3, 0, $temp_fields);

        // Retorna para AdminController e não para AdminAddressesController
        return AdminControllerCore::renderForm();
    }

    private function v1_6_1_0() {

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Addresses'),
                'icon' => 'icon-envelope-alt'
            ),
            'input' => array(
                array(
                    'type' => 'text_customer',
                    'label' => $this->l('Customer'),
                    'name' => 'id_customer',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Identification Number'),
                    'name' => 'dni',
                    'required' => false,
                    'col' => '4',
                    'hint' => $this->l('DNI / NIF / NIE')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Address alias'),
                    'name' => 'alias',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Other'),
                    'name' => 'other',
                    'required' => false,
                    'cols' => 15,
                    'rows' => 3,
                    'hint' => $this->l('Forbidden characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'id_order'
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'address_type',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'back'
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        $this->fields_value['address_type'] = (int)Tools::getValue('address_type', 1);

        $id_customer = (int)Tools::getValue('id_customer');
        if (!$id_customer && Validate::isLoadedObject($this->object)) {
            $id_customer = $this->object->id_customer;
        }
        if ($id_customer) {
            $customer = new Customer((int)$id_customer);
            $token_customer = Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)$this->context->employee->id);
        }

        $this->tpl_form_vars = array(
            'customer' => isset($customer) ? $customer : null,
            'tokenCustomer' => isset($token_customer) ? $token_customer : null,
            'back_url' => urldecode(Tools::getValue('back'))
        );

        // Order address fields depending on country format
        $addresses_fields = $this->processAddressFormat();
        // we use  delivery address
        $addresses_fields = $addresses_fields['dlv_all_fields'];

        // get required field
        $required_fields = AddressFormat::getFieldsRequired();

        // Merge with field required
        $addresses_fields = array_unique(array_merge($addresses_fields, $required_fields));

        $temp_fields = array();

        foreach ($addresses_fields as $addr_field_item) {
            if ($addr_field_item == 'company') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Company'),
                    'name' => 'company',
                    'required' => in_array('company', $required_fields),
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                );
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('VAT number'),
                    'col' => '2',
                    'name' => 'vat_number',
                    'required' => in_array('vat_number', $required_fields)
                );
            } elseif ($addr_field_item == 'lastname') {
                if (isset($customer) &&
                    !Tools::isSubmit('submit'.strtoupper($this->table)) &&
                    Validate::isLoadedObject($customer) &&
                    !Validate::isLoadedObject($this->object)) {
                    $default_value = $customer->lastname;
                } else {
                    $default_value = '';
                }

                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Last Name'),
                    'name' => 'lastname',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' 0-9!&amp;lt;&amp;gt;,;?=+()@#"�{}_$%:',
                    'default_value' => $default_value,
                );
            } elseif ($addr_field_item == 'firstname') {
                if (isset($customer) &&
                    !Tools::isSubmit('submit'.strtoupper($this->table)) &&
                    Validate::isLoadedObject($customer) &&
                    !Validate::isLoadedObject($this->object)) {
                    $default_value = $customer->firstname;
                } else {
                    $default_value = '';
                }

                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('First Name'),
                    'name' => 'firstname',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' 0-9!&amp;lt;&amp;gt;,;?=+()@#"�{}_$%:',
                    'default_value' => $default_value,
                );
            } elseif ($addr_field_item == 'address1') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Address'),
                    'name' => 'address1',
                    'col' => '6',
                    'required' => true,
                );
            } elseif ($addr_field_item == 'numend' and Configuration::get('FKCUSTOMERS_MODO') == '1') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Número'),
                    'name' => 'numend',
                    'col' => 6,
                    'required' => true,
                );
            } elseif ($addr_field_item == 'compl' and Configuration::get('FKCUSTOMERS_MODO') == '1') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Complemento'),
                    'name' => 'compl',
                    'col' => 6,
                    'required' => false,
                );
            } elseif ($addr_field_item == 'address2') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Address').' (2)',
                    'name' => 'address2',
                    'col' => '6',
                    'required' => in_array('address2', $required_fields),
                );
            } elseif ($addr_field_item == 'postcode') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Zip/Postal Code'),
                    'name' => 'postcode',
                    'col' => '2',
                    'required' => true,
                );
            } elseif ($addr_field_item == 'city') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('City'),
                    'name' => 'city',
                    'col' => '4',
                    'required' => true,
                );
            } elseif ($addr_field_item == 'country' || $addr_field_item == 'Country:name') {
                $temp_fields[] = array(
                    'type' => 'select',
                    'label' => $this->l('Country'),
                    'name' => 'id_country',
                    'required' => in_array('Country:name', $required_fields) || in_array('country', $required_fields),
                    'col' => '4',
                    'default_value' => (int)$this->context->country->id,
                    'options' => array(
                        'query' => Country::getCountries($this->context->language->id),
                        'id' => 'id_country',
                        'name' => 'name'
                    )
                );
                $temp_fields[] = array(
                    'type' => 'select',
                    'label' => $this->l('State'),
                    'name' => 'id_state',
                    'required' => false,
                    'col' => '4',
                    'options' => array(
                        'query' => array(),
                        'id' => 'id_state',
                        'name' => 'name'
                    )
                );
            } elseif ($addr_field_item == 'phone') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Home phone'),
                    'name' => 'phone',
                    'required' => in_array('phone', $required_fields) || Configuration::get('PS_ONE_PHONE_AT_LEAST'),
                    'col' => '4',
                    'hint' => Configuration::get('PS_ONE_PHONE_AT_LEAST') ? sprintf($this->l('You must register at least one phone number.')) : ''
                );
            } elseif ($addr_field_item == 'phone_mobile') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Mobile phone'),
                    'name' => 'phone_mobile',
                    'required' =>  in_array('phone_mobile', $required_fields) || Configuration::get('PS_ONE_PHONE_AT_LEAST'),
                    'col' => '4',
                    'hint' => Configuration::get('PS_ONE_PHONE_AT_LEAST') ? sprintf($this->l('You must register at least one phone number.')) : ''
                );
            }
        }

        // merge address format with the rest of the form
        array_splice($this->fields_form['input'], 3, 0, $temp_fields);

        // Retorna para AdminController e não para AdminAddressesController
        return AdminControllerCore::renderForm();
    }

}

?>
