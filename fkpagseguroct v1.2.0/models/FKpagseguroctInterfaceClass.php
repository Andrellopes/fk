<?php

include_once 'cUrlClass.php';
include_once 'XmlParserClass.php';

class FKpagseguroctInterfaceClass {

    private $url_sessao;
    private $url_transacao;
    private $url_notificacao;
    private $charset;
    private $httpVersion;
    private $params = array();
    private $credenciais;
    private $context;
    
    private $code_transacao;
    private $referencia;
    private $cod_status;
    private $desc_status;
    private $cod_pagto;
    private $desc_pagto;
    private $linkBoleto;
    private $linkTransf;
    private $msgErro = array();
    
    // Identificador da transacao
    public function getCodTransacao() {
        return $this->code_transacao;
    }
    
    // Recupera a referencia informado no processo de pagamento
    public function getReferencia() {
        return $this->referencia;
    }
    
    // Codigo do pagamento
    public function getCodStatus() {
        return $this->cod_status;
    }
    
    // Descricao do codigo do pagamento
    public function getDescStatus() {
        return $this->desc_status;
    }
    
    // Codigo da forma de pagamento
    public function getCodPagto() {
        return $this->cod_pagto;
    }
    
    // Descricao do codigo da forma de pagamento
    public function getDescPagto() {
        return $this->desc_pagto;
    }
    
    // Link Boleto
    public function getLinkBoleto() {
        return $this->linkBoleto;
    }
    
    // Link Transferencia
    public function getLinkTransf() {
        return $this->linkTransf;
    }
    
    public function getMsgErro() {
        return $this->msgErro;
    }

    public function __construct() {
        
        // URL Pagseguro - Producao ou SandBox
        if (Configuration::get('FKPAGSEGUROCT_MODO') == '1') {
            $this->url_sessao = Configuration::get('FKPAGSEGUROCT_URL_SESSION_PRODUCAO');
            $this->url_transacao = Configuration::get('FKPAGSEGUROCT_URL_TRANSACTION_PRODUCAO');
            $this->url_notificacao = Configuration::get('FKPAGSEGUROCT_URL_NOTIFICATIONS_PRODUCAO');
        }else {
            $this->url_sessao = Configuration::get('FKPAGSEGUROCT_URL_SESSION_SANDBOX');
            $this->url_transacao = Configuration::get('FKPAGSEGUROCT_URL_TRANSACTION_SANDBOX');
            $this->url_notificacao = Configuration::get('FKPAGSEGUROCT_URL_NOTIFICATIONS_SANDBOX');
        }
        
        // Recupera credenciais
        $this->credenciais = array(
            'email' => Configuration::get('FKPAGSEGUROCT_EMAIL'),
            'token' => Configuration::get('FKPAGSEGUROCT_TOKEN')
        );
        
        // Charset
        $this->charset = Configuration::get('FKPAGSEGUROCT_CHARSET');

        // Versao HTTP
        $this->httpVersion = Configuration::get('FKPAGSEGUROCT_HTTP_VERSION');
        
        // Context
        $this->context = Context::getContext();
        
    }
    
    public function obterSessionId() {

        // Instancia cUrlClass
        $curlClass = new cUrlClass();
        
        // Solicita SessionId ao Pagseguro
        if (!$curlClass->post($this->url_sessao, $this->credenciais, 30, $this->charset, $this->httpVersion)) {
            $this->msgErro[] = array(
                'codigo'    => '00',
                'descricao' => $curlClass->getMsgErro()
            );
        
            return false;
        }
        
        // Verifica retorno
        if ($curlClass->getStatus() === 200) {
            
            $retorno = $curlClass->getResposta();
            
            // Verificar xml de retorno
            $xmlClass = new XmlParserClass($retorno);
            $xml = $xmlClass->getResult('session');
            
            if ($xml) {
                $session_id = $xml['id']; 
                $retJSON = array('erro' => '0', 'descricao' => '', 'dados' => $session_id);
            }else {
                $retJSON = array('erro' => '1', 'descricao' => $xmlClass->getMsgErro(), 'dados' => '');
            }
        }else {
            $retJSON = array('erro' => '1', 'descricao' => 'Erro API: '.$curlClass->getStatus(), 'dados' => '');
        }
        
        return json_encode($retJSON);
    }
    
    public function processarCartao($dados_form) {

        // Adiciona parametros a serem enviados ao Pagseguro
        $this->params['email']          = $this->credenciais['email'];
        $this->params['token']          = $this->credenciais['token'];
        $this->params['paymentMethod']  = 'creditCard';
        $this->procPadroes();
        $this->procProdutos();
        $this->procValoresExtras();
        $this->procDadosComprador($dados_form);
        $this->procDadosEntrega($dados_form);
        $this->procDadosCartao($dados_form);
        $this->procDadosCobranca($dados_form);

        // Instancia cUrlClass
        $curlClass = new cUrlClass();
        
        // Envia transacao de pagamento ao Pagseguro
        if (!$curlClass->post($this->url_transacao, $this->params, 30, $this->charset, $this->httpVersion)) {
            $this->msgErro[] = array(
                'codigo'    => '00',
                'descricao' => $curlClass->getMsgErro()
            );
            
            return false;
        }
        
        // Resposta
        $retorno = $curlClass->getResposta();
        
        // Verificar xml de retorno
        $xmlClass = new XmlParserClass($retorno);
        $status = $curlClass->getStatus();
        
        // Executa acao conforme status de retorno
        if ($status == 200) {

            // Processamento normal
            $xml = $xmlClass->getResult('transaction');

            if ($xml) {
                $this->code_transacao = $xml['code'];
                $this->referencia = $xml['reference'];
                $this->cod_status = $xml['status'];
                $this->desc_status = $this->descricaoStatus($xml['status']);
                $this->cod_pagto = $xml['paymentMethod']['type'];
                $this->desc_pagto = $this->descricaoPagto($xml['paymentMethod']['type']);
            }else {
                $this->code_transacao = 'Verifique em sua conta no Pagseguro';
                $this->referencia = $dados_form['cart_id'];
                $this->cod_status = '0';
                $this->desc_status = '';
                $this->cod_pagto = '0';
                $this->desc_pagto = '';
            }

            return true;
        }else {
            $this->processaRetornoErro($status, $xmlClass);
            return false;
        }

    }
    
    public function processarBoleto($dados_form) {
        
        // Adiciona parametros a serem enviados ao Pagseguro
        $this->params['email']          = $this->credenciais['email'];
        $this->params['token']          = $this->credenciais['token'];
        $this->params['paymentMethod']  = 'boleto';
        $this->procPadroes();
        $this->procProdutos();
        $this->procValoresExtras();
        $this->procDadosComprador($dados_form);
        $this->procDadosEntrega($dados_form);
        
        // Instancia cUrlClass
        $curlClass = new cUrlClass();
        
        // Envia transacao de pagamento ao Pagseguro
        if (!$curlClass->post($this->url_transacao, $this->params, 30, $this->charset, $this->httpVersion)) {
            $this->msgErro[] = array(
                'codigo'    => '00',
                'descricao' => $curlClass->getMsgErro()
            );
        
            return false;
        }
        
        // Resposta
        $retorno = $curlClass->getResposta();
        
        // Verificar xml de retorno
        $xmlClass = new XmlParserClass($retorno);
        $status = $curlClass->getStatus();
        
        // Executa acao conforme status de retorno
        if ($status == 200) {

            // Processamento normal
            $xml = $xmlClass->getResult('transaction');

            if ($xml) {
                $this->code_transacao = $xml['code'];
                $this->referencia = $xml['reference'];
                $this->cod_status = $xml['status'];
                $this->desc_status = $this->descricaoStatus($xml['status']);
                $this->cod_pagto = $xml['paymentMethod']['type'];
                $this->desc_pagto = $this->descricaoPagto($xml['paymentMethod']['type']);
                $this->linkBoleto = $xml['paymentLink'];
            }else {
                $this->code_transacao = 'Verifique em sua conta no Pagseguro';
                $this->referencia = $dados_form['cart_id'];
                $this->cod_status = '0';
                $this->desc_status = '';
                $this->cod_pagto = '0';
                $this->desc_pagto = '';
                $this->linkBoleto = '';
            }

            return true;
        }else {
            $this->processaRetornoErro($status, $xmlClass);
            return false;
        }

    }
    
    public function processarTransf($dados_form) {
    
        // Adiciona parametros a serem enviados ao Pagseguro
        $this->params['email']          = $this->credenciais['email'];
        $this->params['token']          = $this->credenciais['token'];
        $this->params['paymentMethod']  = 'eft';
        $this->params['bankName']       = $dados_form['banco'];
        $this->procPadroes();
        $this->procProdutos();
        $this->procValoresExtras();
        $this->procDadosComprador($dados_form);
        $this->procDadosEntrega($dados_form);
    
        // Instancia cUrlClass
        $curlClass = new cUrlClass();
    
        // Envia transacao de pagamento ao Pagseguro
        if (!$curlClass->post($this->url_transacao, $this->params, 30, $this->charset, $this->httpVersion)) {
            $this->msgErro[] = array(
                'codigo'    => '00',
                'descricao' => $curlClass->getMsgErro()
            );
        
            return false;
        }
    
        // Resposta
        $retorno = $curlClass->getResposta();
    
        // Verificar xml de retorno
        $xmlClass = new XmlParserClass($retorno);
        $status = $curlClass->getStatus();
    
        // Executa acao conforme status de retorno
        if ($status == 200) {

            // Processamento normal
            $xml = $xmlClass->getResult('transaction');

            if ($xml) {
                $this->code_transacao = $xml['code'];
                $this->referencia = $xml['reference'];
                $this->cod_status = $xml['status'];
                $this->desc_status = $this->descricaoStatus($xml['status']);
                $this->cod_pagto = $xml['paymentMethod']['type'];
                $this->desc_pagto = $this->descricaoPagto($xml['paymentMethod']['type']);
                $this->linkTransf = $xml['paymentLink'];
            }else {
                $this->code_transacao = 'Verifique em sua conta no Pagseguro';
                $this->referencia = $dados_form['cart_id'];
                $this->cod_status = '0';
                $this->desc_status = '';
                $this->cod_pagto = '0';
                $this->desc_pagto = '';
                $this->linkTransf = '';
            }

            return true;
        }else {
            $this->processaRetornoErro($status, $xmlClass);
            return false;
        }

    }
    
    public function consultaNotificacao($id_code) {
        
        // Instancia cUrlClass
        $curlClass = new cUrlClass();
        
        // Envia consulta ao Pagseguro
        if (!$curlClass->get($this->url_notificacao.'/'.$id_code, $this->credenciais, 30, $this->charset)) {
            $this->msgErro[] = array(
                'codigo'    => '00',
                'descricao' => $curlClass->getMsgErro()
            );
        
            return false;
        }
        
        // Resposta
        $retorno = $curlClass->getResposta();
        
        // Verificar xml de retorno
        $xmlClass = new XmlParserClass($retorno);
        $xml = $xmlClass->getResult('transaction');
        
        if ($xml) {
            $this->referencia = $xml['reference'];
            $this->cod_status = $xml['status'];
            $this->desc_status = $this->descricaoStatus($xml['status']);
            $this->cod_pagto = $xml['paymentMethod']['type'];
            $this->desc_pagto = $this->descricaoPagto($xml['paymentMethod']['type']);
        }else {
            return false;
        }
        
        return true;
        
    }
    
    public function atualizaStatusPedido($id_cart, $cod_status) {
        
        // Verifica se a constante está definida
        if (!defined('_PS_BASE_URL_')) {
            define('_PS_BASE_URL_', Tools::getShopDomain(true));
        }
        
        $sql = 'SELECT '._DB_PREFIX_.'order_history.id_order, '._DB_PREFIX_.'order_history.id_order_state
                FROM '._DB_PREFIX_.'order_history
                    INNER JOIN '._DB_PREFIX_.'orders
                        ON '._DB_PREFIX_.'order_history.id_order = '._DB_PREFIX_.'orders.id_order
                WHERE '._DB_PREFIX_.'orders.id_cart = '.(int)$id_cart.' '.'
                ORDER BY '._DB_PREFIX_.'order_history.id_order_history DESC';
        
        // Recupera o último id_order_state
        $statePedido = Db::getInstance()->getRow($sql);
        
        // Verifica:
        //***Se o status do PagSeguro é Pagamento Confirmado e o status atual do pedido é PagSeguro: aguardando pagamento
        //***Se o status do PagSeguro é Pagamento Confirmado e o status atual do pedido é Cancelado
        //***Se o status do PagSeguro é Pagamento Confirmado e o status atual do pedido é Sem Estoque
        //***Se o status do PagSeguro é Cancelado e o status atual do pedido é PagSeguro: aguardando pagamento
        //***Se o status do PagSeguro é Cancelado e o status atual do pedido é Sem Estoque
        if (Configuration::get('FKPAGSEGUROCT_STATUS_PAGO') == 'on' and $cod_status == '3' And $statePedido['id_order_state'] == Configuration::get('FKPAGSEGUROCT_STATE_ORDER') Or
            Configuration::get('FKPAGSEGUROCT_STATUS_PAGO') == 'on' and $cod_status == '3' And $statePedido['id_order_state'] == 6 Or
            Configuration::get('FKPAGSEGUROCT_STATUS_PAGO') == 'on' and $cod_status == '3' And $statePedido['id_order_state'] == 9 Or
            Configuration::get('FKPAGSEGUROCT_STATUS_CANC') == 'on' and $cod_status == '7' And $statePedido['id_order_state'] == Configuration::get('FKPAGSEGUROCT_STATE_ORDER') Or
            Configuration::get('FKPAGSEGUROCT_STATUS_CANC') == 'on' and $cod_status == '7' And $statePedido['id_order_state'] == 9) {
        
            // Instancia Order
            $order = new Order((int)$statePedido['id_order']);
    
            // Cria nova entrada em order_history
            $history = new OrderHistory();
            $history->id_order = $statePedido['id_order'];
            $history->id_employee = 0;  // FKpagseguroct
    
            $usarPagtoExistente = false;
            if (!$order->hasInvoice()) {
                $usarPagtoExistente = true;
            }
    
            if ($cod_status == '3') {
                $novoState = 2; // State de Pagamento Aceito
            }else {
                $novoState = 6; // State de Pagamento Cancelado
            }
    
            $history->changeIdOrderState((int)$novoState, $order, $usarPagtoExistente);
    
            $carrier = new Carrier($order->id_carrier, $order->id_lang);
            $templateVars = array();
            if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number) {
                $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));
            }
    
            // Salva as alterações
            if ($history->addWithemail(true, $templateVars)) {
    
                // Sincroniza quantidades, se necessário
                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
    
                    foreach ($order->getProducts() as $product) {
                        if (StockAvailable::dependsOnStock($product['product_id'])) {
                            StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
                        }
                    }
                }
            }
        }
        
    }
    
    private function procPadroes() {
        $this->params['receiverEmail']      = Configuration::get('FKPAGSEGUROCT_EMAIL');
        $this->params['notificationURL']    = Configuration::get('FKPAGSEGUROCT_URL_NOTIFICACAO');
        $this->params['currency']           = 'BRL';
        $this->params['paymentMode']        = 'default';
        $this->params['reference']          = $this->context->cart->id;
    }
    
    private function procProdutos() {
        
        $i = 0;
        $produtos = $this->context->cart->getProducts();
        
        foreach($produtos as $produto) {
            
            // Ignora produto se o valor não for maior que zero
            if ($produto['price_wt'] <= 0) {
                continue;
            }
            
            //Trata descricao do produto
            $descricao = $produto['name'];
            
            if (strlen($descricao) > 100) {
                $descricao = substr($descricao, 0, 100);
            }
            
            $i++;
            
            $this->params['itemId'.$i]          = $produto['id_product'];
            $this->params['itemDescription'.$i] = $descricao;
            $this->params['itemAmount'.$i]      = number_format($produto['price_wt'], 2, '.', '');
            $this->params['itemQuantity'.$i]    = $produto['cart_quantity'];
            
        }
    }
    
    private function procValoresExtras() {
        
        // Recupera acrescimo (Embalagem Presente)
        $acrescimo = $this->context->cart->getOrderTotal(true, Cart::ONLY_WRAPPING);
        
        // Recupera desconto
        $desconto = $this->context->cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
        
        if ($desconto > 0) {
            $desconto *= -1;
        }
        
        // Informa Valor Extra (Embalagem Presente - Desconto)
        $valor_extra = $acrescimo + $desconto;
        
        if ($valor_extra != 0) {
            $this->params['extraAmount'] = number_format($valor_extra, 2, '.', '');
        }
        
    }
    
    private function procDadosComprador($dados_form) {

        // Tipo de documento
        $doc = $dados_form['doc'];

        // CPF
        $cpf = $dados_form['cpf'];
        $cpf = preg_replace('/[^0-9]/','', $cpf);

        // CNPJ
        $cnpj = $dados_form['cnpj'];
        $cnpj = preg_replace('/[^0-9]/','', $cnpj);
        
        // Telefone
        $telefone = $this->trataTelefone($dados_form['telefone']);
        
        // Email
        $email = $this->context->customer->email;
        if (strlen($email) > 60) {
            $email = substr($email, 0, 60);
        }
        
        // Elimina espacos iniciais/finais e duplicados entre os nomes
        $nome = $this->context->customer->firstname.' '.$this->context->customer->lastname;
        $nome = trim($nome);
        $nome = preg_replace('/\s(?=\s)/', '', $nome);

        if (strlen($nome) > 50) {
            $nome = substr($nome, 0, 50);
        }
        
        $this->params['senderEmail']    = $email;
        $this->params['senderName']     = $nome;

        if ($doc == 'cpf') {
            $this->params['senderCPF']  = $cpf;
        }else {
            $this->params['senderCNPJ'] = $cnpj;
        }

        $this->params['senderAreaCode'] = $telefone['cod_area'];
        $this->params['senderPhone']    = $telefone['telefone'];
        $this->params['senderHash']     = $dados_form['hash'];
        
    }
    
    private function procDadosEntrega($dados_form) {
        
        // Endereco
        $address = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'address` WHERE `id_address` = '.(int)$this->context->cart->id_address_delivery);
        
        $cidade = $address['city'];
        if (strlen($cidade) > 60) {
            $cidade = substr($cidade, 0, 60);
        }
        
        $cep = $address['postcode'];
        $cep = preg_replace('/[^0-9]/','', $cep);
        
        // Estado
        $state = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'state` WHERE `id_state` = '.(int)$address['id_state']);
        $estado = $state['iso_code'];
        
        // Frete
        $frete = $this->context->cart->getOrderTotal(true, Cart::ONLY_SHIPPING);
        
        if ($frete > 0) {
            $this->params['shippingType']   = '3';
            $this->params['shippingCost']   = number_format($frete, 2, '.', '');
        }
        
        $this->params['shippingAddressCountry']     = 'BRA';
        $this->params['shippingAddressState']       = $estado;
        $this->params['shippingAddressCity']        = $cidade;
        $this->params['shippingAddressPostalCode']  = $cep;
        $this->params['shippingAddressDistrict']    = $dados_form['bairro_entrega'];
        $this->params['shippingAddressStreet']      = $dados_form['endereco_entrega'];
        $this->params['shippingAddressNumber']      = $dados_form['numero_entrega'];

        if (trim($dados_form['complemento_entrega']) != '') {
            $this->params['shippingAddressComplement']  = $dados_form['complemento_entrega'];
        }

    }
    
    private function procDadosCartao($dados_form) {
        
        // CPF
        $cpf = $dados_form['cpf'];
        $cpf = preg_replace('/[^0-9]/','', $cpf);

        // Elimina espacos iniciais/finais e duplicados entre os nomes
        $titularCartao = trim($dados_form['titular_cartao']);
        $titularCartao = preg_replace('/\s(?=\s)/', '', $titularCartao);

        // Telefone
        $telefone = $this->trataTelefone($dados_form['telefone']);
        
        $this->params['creditCardToken']                = $dados_form['token'];
        $this->params['installmentQuantity']            = $dados_form['total_parcelas_cartao'];
        $this->params['installmentValue']               = number_format($dados_form['valor_parcelas_cartao'], 2, '.', '');

        if (Configuration::get('FKPAGSEGUROCT_PARCELAS_SEM_JUROS') > 1) {
            $this->params['noInterestInstallmentQuantity']  = Configuration::get('FKPAGSEGUROCT_PARCELAS_SEM_JUROS');
        }

        $this->params['creditCardHolderName']           = $titularCartao;
        $this->params['creditCardHolderBirthDate']      = $dados_form['data_nasc'];
        $this->params['creditCardHolderCPF']            = $cpf;
        $this->params['creditCardHolderAreaCode']       = $telefone['cod_area'];
        $this->params['creditCardHolderPhone']          = $telefone['telefone'];
        
    }
    
    private function procDadosCobranca($dados_form) {
        
        // Endereco
        $address = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'address` WHERE `id_address` = '.(int)$this->context->cart->id_address_invoice);
        
        $cidade = $address['city'];
        if (strlen($cidade) > 60) {
            $cidade = substr($cidade, 0, 60);
        }
        
        $cep = $address['postcode'];
        $cep = preg_replace('/[^0-9]/','', $cep);
        
        // Estado
        $state = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'state` WHERE `id_state` = '.(int)$address['id_state']);
        $estado = $state['iso_code'];
        
        $this->params['billingAddressPostalCode']   = $cep;
        $this->params['billingAddressStreet']       = $dados_form['endereco_cobranca'];
        $this->params['billingAddressNumber']       = $dados_form['numero_cobranca'];

        if (trim($dados_form['complemento_cobranca']) != '') {
            $this->params['billingAddressComplement']   = $dados_form['complemento_cobranca'];
        }

        $this->params['billingAddressDistrict']     = $dados_form['bairro_cobranca'];
        $this->params['billingAddressCity']         = $cidade;
        $this->params['billingAddressState']        = $estado;
        $this->params['billingAddressCountry']      = 'BRA';
        
    }
    
    private function trataTelefone($telefone) {
    
        $codArea = '';
        $tel = '';
    
        $telefone = preg_replace('/[^0-9]/','', $telefone);
        $len = strlen($telefone);
    
        if ($len == 10) {
            $codArea = substr($telefone, 0, 2);
            $tel = substr($telefone, 2, 8);
        }else {
            if ($len == 11) {
                $codArea = substr($telefone, 0, 2);
                $tel = substr($telefone, 2, 9);
            }else {
                $codArea = '';
                $tel = '';
            }
        }
    
        return array('cod_area' => $codArea, 'telefone' => $tel);
    }
    
    public function trataEndereco($end_padrao) {
    
        $endereco = $end_padrao;
        $numero = '';
        $complemento = '';
        $bairro = '';
    
        $end_split = preg_split("/[-,\\n]/", $end_padrao);
    
        if (count($end_split) == 4) {
            list ($endereco, $numero, $complemento, $bairro) = $end_split;
        } elseif (count($end_split) == 3) {
            list ($endereco, $numero, $complemento) = $end_split;
        } elseif (count($end_split) == 2) {
            list ($endereco, $numero, $complemento) = self::ordenaDados($end_padrao);
        } else {
            $endereco = $end_padrao;
        }
    
        return array(
            'endereco'      => trim(substr($endereco, 0, 69)),
            'numero'        => trim($numero),
            'complemento'   => trim($complemento),
            'bairro'        => trim($bairro)
        );
    }
    
    private function ordenaDados($end_padrao) {
    
        $end_split = preg_split('/[-,\\n]/', $end_padrao);
    
        for ($i = 0; $i < strlen($end_split[0]); $i ++) {
            if (is_numeric(substr($end_split[0], $i, 1))) {
                return array(
                    substr($end_split[0], 0, $i),
                    substr($end_split[0], $i),
                    $end_split[1]
                );
            }
        }
    
        $end_padrao = preg_replace('/\s/', ' ', $end_padrao);
        $encontrar = substr($end_padrao, - strlen($end_padrao));
        for ($i = 0; $i < strlen($end_padrao); $i ++) {
            if (is_numeric(substr($encontrar, $i, 1))) {
                return array(
                    substr($end_padrao, 0, - strlen($end_padrao) + $i),
                    substr($end_padrao, - strlen($end_padrao) + $i),
                    ''
                );
            }
        }
    }

    private function processaRetornoErro($status, $xmlClass) {

        switch ($status) {

            case 400:

                $xml = $xmlClass->getResult('errors');

                if ($xml) {
                    // traduz e retorna mensagens de erros
                    foreach ($xml['error'] as $erro) {
                        $this->msgErro[] = array(
                            'codigo'    => $erro['code'],
                            'descricao' => $this->traduzirMensagem($erro['code'], $erro['message'])
                        );
                    }
                }

                break;

            case 401:

                $this->msgErro[] = array(
                    'codigo'    => '00000',
                    'descricao' => 'Credenciais inválidas'
                );

                break;

            case 405:

                $this->msgErro[] = array(
                    'codigo'    => '00000',
                    'descricao' => 'Método não permitido (somente permitido GET ou POST).'
                );

                break;

            case 415:

                $this->msgErro[] = array(
                    'codigo'    => '00000',
                    'descricao' => 'Não enviado Content-Type na chamada.'
                );

                break;

            default:
                // Erro nao previsto
                $this->msgErro[] = array(
                    'codigo'    => $status,
                    'descricao' => 'Erro não definido.'
                );

                break;

        }

    }


    private function traduzirMensagem($codigo, $msg) {
        
        $mensagem = array();
        $mensagem[10000] = 'bandeira do cartão de crédito inválida.';
        $mensagem[10001] = 'número do cartão de crédito com comprimento inválido.';
        $mensagem[10002] = 'formato de data inválido.';
        $mensagem[10003] = 'campo de segurança inválido.';
        $mensagem[10004] = 'cvv é obrigatório.';
        $mensagem[10006] = 'campo de segurança com comprimento inválido.';
        $mensagem[53004] = 'quantidade de itens inválida.';
        $mensagem[53005] = 'moeda corrente é necessária.';
        $mensagem[53006] = 'moeda corrente inválida.';
        $mensagem[53007] = 'referência com comprimento inválido.';
        $mensagem[53008] = 'URL de notificação com comprimento inválido.';
        $mensagem[53009] = 'URL de notificação com valor inválido.';
        $mensagem[53010] = 'remetente de e-mail é necessário.';
        $mensagem[53011] = 'remetente de e-mail com comprimento inválido.';
        $mensagem[53012] = 'remetente de e-mail com valor inválido.';
        $mensagem[53013] = 'nome do remetente é necessário.';
        $mensagem[53014] = 'nome do remetente com comprimento inválido.';
        $mensagem[53015] = 'nome do remetente com valor inválido.';
        $mensagem[53017] = 'cpf do remetente inválido.';
        $mensagem[53018] = 'código de área do remetente é necessário.';
        $mensagem[53019] = 'código de área do remetente inválido.';
        $mensagem[53020] = 'telefone do remetente é necessário.';
        $mensagem[53021] = 'telefone do remetente é inválido.';
        $mensagem[53022] = 'código postal do endereço de entrega é necessário.';
        $mensagem[53023] = 'código postal do endereço de entrega é inválido.';
        $mensagem[53024] = 'rua do endereço de entrega é necessário.';
        $mensagem[53025] = 'rua do endereço de entrega com comprimento inválido.';
        $mensagem[53026] = 'número do endereço de entrega é necessário.';
        $mensagem[53027] = 'número do endereço de entrega com comprimento inválido.';
        $mensagem[53028] = 'complemento do endereço de entrega com comprimento inválido.';
        $mensagem[53029] = 'bairro do endereço de entrega é necessário.';
        $mensagem[53030] = 'bairro do endereço de entrega com comprimento inválido.';
        $mensagem[53031] = 'cidade do endereço de entrega é necessário.';
        $mensagem[53032] = 'cidade do endereço de entrega com comprimento inválido.';
        $mensagem[53033] = 'estado do endereço de entrega é necessário.';
        $mensagem[53034] = 'estado do endereço de entrega é inválido.';
        $mensagem[53035] = 'país do endereço de entrega é necessário.';
        $mensagem[53036] = 'país do endereço de entrega com comprimento inválido.';
        $mensagem[53037] = 'token do cartão de crédito é necessário.';
        $mensagem[53038] = 'quantidade de parcelas é necessária.';
        $mensagem[53039] = 'quantidade de parcelas com valor inválido.';
        $mensagem[53040] = 'valor da parcela é necessário.';
        $mensagem[53041] = 'valor da parcela com valor inválido.';
        $mensagem[53042] = 'nome do titular do cartão de crédito é necessário.';
        $mensagem[53043] = 'nome do titular do cartão de crédito com comprimento inválido.';
        $mensagem[53044] = 'nome do titular do cartão de crédito com valor inválido.';
        $mensagem[53045] = 'cpf do titular do cartão de crédito é necessário.';
        $mensagem[53046] = 'cpf do titular do cartão de crédito com valor inválido.';
        $mensagem[53047] = 'data de nascimento do titular do cartão de crédito é necessária.';
        $mensagem[53048] = 'data de nascimento do titular do cartão de crédito com valor inválido.';
        $mensagem[53049] = 'código de área do titular do cartão de crédito é necessário.';
        $mensagem[53050] = 'código de área do titular do cartão de crédito com valor inválido.';
        $mensagem[53051] = 'telefone do titular do cartão de crédito é necessário.';
        $mensagem[53052] = 'telefone do titular do cartão de crédito com valor inválido.';
        $mensagem[53053] = 'código postal do endereço de cobrança é necessário.';
        $mensagem[53054] = 'código postal do endereço de cobrança com valor inválido.';
        $mensagem[53055] = 'rua do endereço de cobrança é necessária.';
        $mensagem[53056] = 'rua do endereço de cobrança com comprimento inválido.';
        $mensagem[53057] = 'número do endereço de cobrança é necessário.';
        $mensagem[53058] = 'número do endereço de cobrança com comprimento inválido.';
        $mensagem[53059] = 'complemento do endereço de cobrança com comprimento inválido.';
        $mensagem[53060] = 'bairro do endereço de cobrança é necessário.';
        $mensagem[53061] = 'bairro do endereço de cobrança com comprimento inválido.';
        $mensagem[53062] = 'cidade do endereço de cobrança é necessária.';
        $mensagem[53063] = 'cidade do endereço de cobrança com comprimento inválido.';
        $mensagem[53064] = 'estado do endereço de cobrança é necessário.';
        $mensagem[53065] = 'estado do endereço de cobrança com valor inválido.';
        $mensagem[53066] = 'país do endereço de cobrança é necessário.';
        $mensagem[53067] = 'país do endereço de cobrança com comprimento inválido.';
        $mensagem[53068] = 'email do destinatário com comprimento inválido.';
        $mensagem[53069] = 'email do destinatário com valor inválido.';
        $mensagem[53070] = 'id do item é necessário.';
        $mensagem[53071] = 'id do item com comprimento inválido.';
        $mensagem[53072] = 'descrição do item é necessária.';
        $mensagem[53073] = 'descrição do item com comprimento inválido.';
        $mensagem[53074] = 'quantidade do item é necessária.';
        $mensagem[53075] = 'quantidade do item fora da faixa.';
        $mensagem[53076] = 'quantidade do item com valor inválido.';
        $mensagem[53077] = 'montante do item é necessário.';
        $mensagem[53078] = 'montante do item com padrão inválido.';
        $mensagem[53079] = 'montante do item fora da faixa.';
        $mensagem[53081] = 'o remetente está relacionado com o destinatário.';
        $mensagem[53084] = 'destinatário inválido.';
        $mensagem[53085] = 'forma de pagamento indisponível.';
        $mensagem[53086] = 'montante total da compra fora da faixa.';
        $mensagem[53087] = 'cartão de crédito com data inválida.';
        $mensagem[53091] = 'hash de remetente inválido.';
        $mensagem[53092] = 'bandeira do cartão de crédito não é aceita.';
        $mensagem[53095] = 'tipo de transporte padrão inválido.';
        $mensagem[53096] = 'custo de transporte padrão inválido.';
        $mensagem[53097] = 'custo de transporte fora da faixa.';
        $mensagem[53098] = 'valor total da compra é negativo.';
        $mensagem[53099] = 'montante extra padrão inválido.';
        $mensagem[53101] = 'modo de pagamento valor inválido, os valores válidos são padrão e um gateway.';
        $mensagem[53102] = 'forma de pagamento valor inválido, os valores válidos são cartão de crédito, boleto e eft.';
        $mensagem[53104] = 'custo de transporte foi fornecido, endereço de envio deve estar completo.';
        $mensagem[53105] = 'informações sobre o remetente foram fornecidas, o e-mail deve ser fornecido também.';
        $mensagem[53106] = 'titular do cartão de crédito está incompleto.';
        $mensagem[53109] = 'informações sobre o endereço de envio foram fornecidas, o email do remetente deve ser fornecido também.';
        $mensagem[53110] = 'eft bancário é necessário.';
        $mensagem[53111] = 'eft bancário não foi aceito.';
        $mensagem[53115] = 'data de nascimento do remetente com valor inválido.';
        $mensagem[53117] = 'cnpj do remetente com valor inválido.';
        $mensagem[53118] = 'cpf é obrigatório.';
        $mensagem[53122] = 'o domínio do email do remetente é inválido.';
        $mensagem[53140] = 'quantidade de parcelas for a da faixa.';
        $mensagem[53141] = 'remetente está bloqueado.';
        $mensagem[53142] = 'token do cartão de crédito inválido.';

        return (isset($mensagem[$codigo]) ? $mensagem[$codigo].' (.'.$msg.')' : $msg);
        
    }
    
    private function descricaoStatus($cod_status) {
    
        $descricao = array(
            '1' => 'Aguardando pagamento',
            '2' => 'Em análise',
            '3' => 'Pagamento confirmado',
            '4' => 'Valor disponível',
            '5' => 'Em disputa',
            '6' => 'Valor pago devolvido ao comprador',
            '7' => 'Transação cancelada'
        );
    
        return (isset($descricao[$cod_status]) ? $descricao[$cod_status] : 'Não definido');
    }
    
    private  function descricaoPagto($cod_pagto) {
    
        $descricao = array(
            '1' => 'Cartão de crédito',
            '2' => 'Boleto',
            '3' => 'Débito online (TEF)',
            '4' => 'Saldo PagSeguro',
            '5' => 'Oi Paggo',
            '7' => 'Depósito em conta'
        );
    
        return (isset($descricao[$cod_pagto]) ? $descricao[$cod_pagto] : 'Não definido');
    }

}

?>