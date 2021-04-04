<?php

$context = Context::getContext();

// Recupera se e Pessoa Fisica ou Juridica
$tipo_pessoa = 'pf';

if (isset($context->customer->id)) {
    
    $dados = Db::getInstance()->getRow('SELECT tipo FROM '._DB_PREFIX_.'customer WHERE id_customer = '.$context->customer->id);
    
    if ($dados) {
        $tipo_pessoa = $dados['tipo'];
    }
    
}

// Variaveis smarty
$this->context->smarty->assign(array(
    'TipoPessoa'        => $tipo_pessoa,
    'UriPath'           => _PS_MODULE_DIR_.'fkcustomers',
    'UrlJs'      	    => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/fkcustomers/js/'
));


