<?php
include_once (_PS_MODULE_DIR_.'fkpagseguroct/models/FKpagseguroctInterfaceClass.php');

class FKpagseguroctValidationModuleFrontController extends ModuleFrontController {

    private $erro_pagseguro = false;

    private $html_erro = '';

    public function __construct() {
        
        parent::__construct();
        
        $this->display_column_left = false;
        $this->display_column_right = false;
    }

    public function initContent() {
        
        parent::initContent();
        
        if ($this->erro_pagseguro) {
            
            $this->context->smarty->assign(array(
                'fkpagseguroct_msg_2'   => html_entity_decode(Configuration::get('FKPAGSEGUROCT_MSG_2')),
                'fkpagseguroct_erro'    => $this->html_erro,
                'fkpagseguroct_link'    => $this->context->link->getPageLink('order&step=1'),
                'bootstrap'             => Configuration::get('FKPAGSEGUROCT_BOOTSTRAP'),
            ));
            
            $this->setTemplate('erro.tpl');
        }
    }

    public function postProcess() {
        
        // Validacao
        $cart = $this->context->cart;
        
        if ($cart->id_customer == 0 or $cart->id_address_delivery == 0 or $cart->id_address_invoice == 0 or !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }
        
        // Verifica se esta opção de pagamento ainda está disponível no caso de o cliente mudar o endereço antes do fim do processo de compra
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            
            if ($module['name'] == 'fkpagseguroct') {
                $authorized = true;
                break;
            }
        }
        
        if (!$authorized) {
            $this->html_erro .= '<p>Este método de pagamento não está disponível para o local de destino.</p>';
            $this->erro_pagseguro = true;
            return;
        }
        
        // Instancia FKpagseguroctInterfaceClass
        $checkout = new FKpagseguroctInterfaceClass();
        
        // Processa Pagseguro
        if (Tools::getValue('fkpagseguroct_tipo') == 'cartao') {
            
            $dados_form = array(
                'cart_id'               => $cart->id,
                'titular_cartao'        => Tools::getValue('fkpagseguroct_cartao_titular'),
                'data_nasc'             => Tools::getValue('fkpagseguroct_cartao_nasc'),
                'telefone'              => Tools::getValue('fkpagseguroct_cartao_tel'),
                'doc'                   => (Tools::getValue('fkpagseguroct_cartao_cnpj') ? 'cnpj' : 'cpf'),
                'cpf'                   => Tools::getValue('fkpagseguroct_cartao_cpf'),
                'cnpj'                  => Tools::getValue('fkpagseguroct_cartao_cnpj'),
                'endereco_entrega'      => Tools::getValue('fkpagseguroct_cartao_endereco_entrega'),
                'numero_entrega'        => Tools::getValue('fkpagseguroct_cartao_numero_entrega'),
                'complemento_entrega'   => Tools::getValue('fkpagseguroct_cartao_complemento_entrega'),
                'bairro_entrega'        => Tools::getValue('fkpagseguroct_cartao_bairro_entrega'),
                'endereco_cobranca'     => Tools::getValue('fkpagseguroct_cartao_endereco_cobranca'),
                'numero_cobranca'       => Tools::getValue('fkpagseguroct_cartao_numero_cobranca'),
                'complemento_cobranca'  => Tools::getValue('fkpagseguroct_cartao_complemento_cobranca'),
                'bairro_cobranca'       => Tools::getValue('fkpagseguroct_cartao_bairro_cobranca'),
                'numero_cartao'         => Tools::getValue('fkpagseguroct_cartao_numero'),
                'mes_venc_cartao'       => Tools::getValue('fkpagseguroct_cartao_venc_mes'),
                'ano_venc_cartao'       => Tools::getValue('fkpagseguroct_cartao_venc_ano'),
                'cod_seg_cartao'        => Tools::getValue('fkpagseguroct_cartao_codigo'),
                'total_parcelas_cartao' => Tools::getValue('fkpagseguroct_cartao_parcelas'),
                'valor_parcelas_cartao' => Tools::getValue('fkpagseguroct_cartao_valor_parcela'),
                'token'                 => Tools::getValue('fkpagseguroct_cartao_token'),
                'hash'                  => Tools::getValue('fkpagseguroct_cartao_hash')
            );
            
            if (!$checkout->processarCartao($dados_form)) {
                $this->html_erro = '';
                
                foreach ($checkout->getMsgErro() as $erro) {
                    $this->html_erro .= '<p>'.$erro['codigo'].' - '.$erro['descricao'].'</p>';
                }
                
                $this->erro_pagseguro = true;
            }
        }elseif (Tools::getValue('fkpagseguroct_tipo') == 'boleto') {
            
            $dados_form = array(
                'cart_id'               => $cart->id,
                'telefone'              => Tools::getValue('fkpagseguroct_boleto_tel'),
                'doc'                   => Tools::getValue('fkpagseguroct_boleto_cpf_cnpj'),
                'cpf'                   => Tools::getValue('fkpagseguroct_boleto_cpf'),
                'cnpj'                  => Tools::getValue('fkpagseguroct_boleto_cnpj'),
                'endereco_entrega'      => Tools::getValue('fkpagseguroct_boleto_endereco_entrega'),
                'numero_entrega'        => Tools::getValue('fkpagseguroct_boleto_numero_entrega'),
                'complemento_entrega'   => Tools::getValue('fkpagseguroct_boleto_complemento_entrega'),
                'bairro_entrega'        => Tools::getValue('fkpagseguroct_boleto_bairro_entrega'),
                'hash'                  => Tools::getValue('fkpagseguroct_boleto_hash')
            );
            
            if (!$checkout->processarBoleto($dados_form)) {
                $this->html_erro = '';
                
                foreach ($checkout->getMsgErro() as $erro) {
                    $this->html_erro .= '<p>'.$erro['codigo'].' - '.$erro['descricao'].'</p>';
                }
                
                $this->erro_pagseguro = true;
            }
        }else {
            
            $dados_form = array(
                'cart_id'               => $cart->id,
                'banco'                 => Tools::getValue('fkpagseguroct_transf_banco'),
                'telefone'              => Tools::getValue('fkpagseguroct_transf_tel'),
                'doc'                   => Tools::getValue('fkpagseguroct_transf_cpf_cnpj'),
                'cpf'                   => Tools::getValue('fkpagseguroct_transf_cpf'),
                'cnpj'                  => Tools::getValue('fkpagseguroct_transf_cnpj'),
                'endereco_entrega'      => Tools::getValue('fkpagseguroct_transf_endereco_entrega'),
                'numero_entrega'        => Tools::getValue('fkpagseguroct_transf_numero_entrega'),
                'complemento_entrega'   => Tools::getValue('fkpagseguroct_transf_complemento_entrega'),
                'bairro_entrega'        => Tools::getValue('fkpagseguroct_transf_bairro_entrega'),
                'hash'                  => Tools::getValue('fkpagseguroct_transf_hash')
            );
            
            if (!$checkout->processarTransf($dados_form)) {
                $this->html_erro = '';
            
                foreach ($checkout->getMsgErro() as $erro) {
                    $this->html_erro .= '<p>'.$erro['codigo'].' - '.$erro['descricao'].'</p>';
                }
            
                $this->erro_pagseguro = true;
            }
            
        }
        
        // Retorna se houve erro na transacao
        if ($this->erro_pagseguro) {
            return;
        }
        
        // Grava tabela de controle
        $dados = array(
            'id_shop'       => $this->context->shop->id,
            'cod_cliente'   => $cart->id_customer,
            'id_cart'       => $cart->id,
            'cod_transacao' => $checkout->getCodTransacao(),
            'status'        => $checkout->getCodStatus(),
            'desc_status'   => $checkout->getDescStatus(),
            'pagto'         => $checkout->getCodPagto(),
            'desc_pagto'    => $checkout->getDescPagto(),
            'data_status'   => $date = date("Y/m/d h:i:s"),
            'data_pedido'   => $date = date("Y/m/d h:i:s")
        );
        
        Db::getInstance()->insert('fkpagseguroct', $dados);
        
        // Processa o pedido
        $customer = new Customer($cart->id_customer);
        
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }
        
        $currency = $this->context->currency;
        $total = (float) $cart->getOrderTotal(true, Cart::BOTH);
        
        $this->module->validateOrder($cart->id, Configuration::get('FKPAGSEGUROCT_STATE_ORDER'), $total, $this->module->displayName, NULL, array(), (int) $currency->id, false, $customer->secure_key);
        Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.
                        '&id_module='.$this->module->id.
                        '&id_order='.$this->module->currentOrder.
                        '&key='.$customer->secure_key.
                        '&cod_status='.$checkout->getCodStatus().
                        '&cod_transacao='.$checkout->getCodTransacao().
                        '&link_boleto='.$checkout->getLinkBoleto().
                        '&link_transf='.$checkout->getLinkTransf());
    }
}
