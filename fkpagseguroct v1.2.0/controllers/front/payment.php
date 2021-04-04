<?php

include_once(_PS_MODULE_DIR_.'fkpagseguroct/models/FKpagseguroctInterfaceClass.php');

class FKpagseguroctPaymentModuleFrontController extends ModuleFrontController {

    public $ssl = true;
    private $fkcustomers = false;
    
    public function __construct() {
        
        parent::__construct();
        
        $this->display_column_left = false;
        $this->display_column_right = false;

        // Verifica se o FKcustomers esta instalado
        if (module::isInstalled('fkcustomers')) {
            $this->fkcustomers = true;
        }
    }

    public function initContent() {

        parent::initContent();
    
    	$cart = $this->context->cart;
    	if (!$this->module->checkCurrency($cart)) {
    	    Tools::redirect('index.php?controller=order');
    	}

        // CPF e CNPJ
        $cpf = '';
        $cnpj = '';

        if ($this->fkcustomers) {
            if (isset($this->context->customer->tipo) and isset($this->context->customer->cpf_cnpj)) {
                if ($this->context->customer->tipo == 'pf') {
                    $cpf = preg_replace('/[^0-9]/','', $this->context->customer->cpf_cnpj);
                }else {
                    $cnpj = preg_replace('/[^0-9]/','', $this->context->customer->cpf_cnpj);
                }
            }
        }

        // Endereco de Entrega
        $endereco_entrega = $this->procEndereco($this->context->cart->id_address_delivery);
        
        // Endereco de Cobranca
        $endereco_cobranca = $this->procEndereco($this->context->cart->id_address_invoice);

        $this->context->smarty->assign(array(
            'cartao'                => Configuration::get('FKPAGSEGUROCT_CARTAO'),
            'boleto'                => Configuration::get('FKPAGSEGUROCT_BOLETO'),
            'transf'                => Configuration::get('FKPAGSEGUROCT_TRANSF'),
            'bb'                    => Configuration::get('FKPAGSEGUROCT_BB'),
            'banrisul'              => Configuration::get('FKPAGSEGUROCT_BANRISUL'),
            'bradesco'              => Configuration::get('FKPAGSEGUROCT_BRADESCO'),
            'hsbc'                  => Configuration::get('FKPAGSEGUROCT_HSBC'),
            'itau'                  => Configuration::get('FKPAGSEGUROCT_ITAU'),
            'parcelas_sem_juros'    => Configuration::get('FKPAGSEGUROCT_PARCELAS_SEM_JUROS'),
            'nbProducts'            => $cart->nbProducts(),
            'total'                 => $cart->getOrderTotal(true, Cart::BOTH),
            'cpf'                   => $cpf,
            'cnpj'                  => $cnpj,
            'endereco_entrega'      => $endereco_entrega,
            'endereco_cobranca'     => $endereco_cobranca,
            'msg_boleto'            => html_entity_decode(Configuration::get('FKPAGSEGUROCT_MSG_3')),
            'url_img'               => $this->module->getPathUri().'img/',
            'url_funcoes'           => $this->module->getPathUri().'funcoes.php',
            'ddd_validos'           => Configuration::get('FKPAGSEGUROCT_DDD'),
            'bootstrap'             => Configuration::get('FKPAGSEGUROCT_BOOTSTRAP'),
            
        ));

        if (Configuration::get('FKPAGSEGUROCT_BOOTSTRAP') == 'on') {
            $this->setTemplate('payment_execution_bootstrap.tpl');
        }else {
            $this->setTemplate('payment_execution.tpl');
        }

    }
	
    public function setMedia() {
    
        parent::setMedia();
    
    	// JS
    	$this->addJS(_PS_JS_DIR_.'jquery/plugins/fancybox/jquery.fancybox.js');
    	
    	// Adiciona Fancybox caso QuickView esteja desativado
    	if (!Configuration::get('PS_QUICK_VIEW')) {
    	    $this->addjqueryPlugin('fancybox');
    	}
    	
    	$this->addJS(_PS_MODULE_DIR_.'fkpagseguroct/js/jquery.maskedinput.js');
    	
    	// URL Pagseguro - Producao ou SandBox
    	if (Configuration::get('FKPAGSEGUROCT_MODO') == '1') {
    	    $this->addJS(Configuration::get('FKPAGSEGUROCT_URL_JS_PRODUCAO'));
    	}else {
    	    $this->addJS(Configuration::get('FKPAGSEGUROCT_URL_JS_SANDBOX'));
    	}

        $this->addJS(_PS_MODULE_DIR_.'fkpagseguroct/js/fkpagseguroct_front.js');
       
    }
	
    private function procEndereco($id_endereco) {
	
    	// Endereco
        $sql = 'SELECT *
                FROM '._DB_PREFIX_.'address
                WHERE id_address = '.(int)$id_endereco;

    	$address = Db::getInstance()->getRow($sql);

        $numend = false;
        $compl = false;

        if ($this->fkcustomers) {
            if (isset($address['numend'])) {
                if (trim($address['numend']) != '') {
                    if (!Configuration::get('FKCUSTOMERS_MODO') or Configuration::get('FKCUSTOMERS_MODO') == '1') {
                        $numend = true;
                    }
                }
            }

            if (isset($address['compl'])) {
                $compl = true;
            }
        }

    	if ($numend and $compl) {
    	
    	    $endereco = $address['address1'];
    	    if (strlen($endereco) > 80) {
    		  $endereco = substr($endereco, 0, 80);
    	    }
    	
    	    $numero = $address['numend'];
    	    if (strlen($numero) > 20) {
    		  $numero = substr($numero, 0, 20);
    	    }
    	    
    	    $complemento = $address['compl'];
    	    if (strlen($complemento) > 40) {
    		  $complemento = substr($complemento, 0, 40);
    	    }
    	}else {
    	    // Instancia FKpagseguroctInterfaceClass
    	    $checkout = new FKpagseguroctInterfaceClass();
    	    $endereco_tmp = $checkout->trataEndereco($address['address1']);
    	
    	    $endereco = substr($endereco_tmp['endereco'], 0, 80);
    	    $numero = substr($endereco_tmp['numero'], 0, 20);;
    	    $complemento = substr($endereco_tmp['complemento'], 0, 40);
    	}
    	
    	return array(
    	    'endereco'      => $endereco,
    	    'numero'        => $numero,
    	    'complemento'   => $complemento,
            'bairro'        => substr($address['address2'], 0, 60),
    	);
	
    }

}
