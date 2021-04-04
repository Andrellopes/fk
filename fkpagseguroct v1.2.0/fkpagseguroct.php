<?php

include_once _PS_MODULE_DIR_.'fkpagseguroct/models/FKpagseguroctInterfaceClass.php';

class FKpagseguroct extends PaymentModule {

    private $_html = '';
    private $_postErrors = array();
    private $_charsetOptions = array('1' => 'ISO-8859-1', '2' =>'UTF-8');
    private $_httpVersions = array('1' => 'HTTP 1.0', '2' =>'HTTP 1.1');
    private $_tab_select = '';
    
    public function __construct() {

        $this->name = 'fkpagseguroct';
        $this->tab = 'payments_gateways';
        $this->version = '1.2.0';
        $this->author = 'módulosFK';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('FKpagseguro - Checkout Transparente');
        $this->description = $this->l('Aceita pagamentos através do PagSeguro - Checkout Transparente.');

        // URL que variam conforme endereco do dominio
        Configuration::updateValue('FKPAGSEGUROCT_URL_NOTIFICACAO', Tools::getShopDomain(true, true).__PS_BASE_URI__.'modules/fkpagseguroct/notificacao.php');
        Configuration::updateValue('FKPAGSEGUROCT_URL_IMG', Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/img/');

        // Array com nome das classes do menu
        $this->_tabClassName[] = array('className' => 'AdminFKpagseguroct', 'name' => 'FKpagseguro-CT');
        $this->_tabClassName[] = array('className' => 'AdminPagtoStatus', 'name' => $this->l('Status dos pagamentos'));
    }

    public function install() {

        // Mensagens padroes
        $msg_1  = htmlentities('<p style="font-weight: bold;">Sua compra está finalizada. Agradecemos por comprar conosco!</p><p>O processo de envio do seu pedido terá início logo após recebermos a confirmação do pagamento.</p>');
        $msg_2  = htmlentities('<p style="font-weight: bold;">O processo de pagamento não foi completado junto ao Pagseguro em razão dos problema(s) apontados abaixo.</p><p>Entre em contato com o Atendimento a Clientes ou clique no botão <span style="font-weight: bold;">Retornar ao Carrinho</span> para tentar novamente ou escolher uma nova forma de pagamento.</p>');
        $msg_3  = htmlentities('');

        if (!parent::install()
            Or !$this->criaTabelas()
            Or !$this->criaStatus()
            Or !$this->criaMenus()
            Or !$this->registerHook('displayHeader')
            Or !$this->registerHook('displayPayment')
            Or !$this->registerHook('displayPaymentReturn')
            Or !Configuration::updateValue('FKPAGSEGUROCT_MODO', '1')
            Or !Configuration::updateValue('FKPAGSEGUROCT_EMAIL', '')
            Or !Configuration::updateValue('FKPAGSEGUROCT_TOKEN', '')
            Or !Configuration::updateValue('FKPAGSEGUROCT_CHARSET', 'UTF-8')
            Or !Configuration::updateValue('FKPAGSEGUROCT_HTTP_VERSION', 'HTTP 1.0')
            Or !Configuration::updateValue('FKPAGSEGUROCT_CARTAO', 'on')
            Or !Configuration::updateValue('FKPAGSEGUROCT_BOLETO', 'on')
            Or !Configuration::updateValue('FKPAGSEGUROCT_TRANSF', 'on')
            Or !Configuration::updateValue('FKPAGSEGUROCT_BB', 'on')
            Or !Configuration::updateValue('FKPAGSEGUROCT_BANRISUL', 'on')
            Or !Configuration::updateValue('FKPAGSEGUROCT_BRADESCO', 'on')
            Or !Configuration::updateValue('FKPAGSEGUROCT_HSBC', 'on')
            Or !Configuration::updateValue('FKPAGSEGUROCT_ITAU', 'on')
            Or !Configuration::updateValue('FKPAGSEGUROCT_PARCELAS_SEM_JUROS', '0')
            Or !Configuration::updateValue('FKPAGSEGUROCT_STATUS_PAGO', 'on')
            Or !Configuration::updateValue('FKPAGSEGUROCT_STATUS_CANC', '')
            Or !Configuration::updateValue('FKPAGSEGUROCT_MSG_1', $msg_1)
            Or !Configuration::updateValue('FKPAGSEGUROCT_MSG_2', $msg_2)
            Or !Configuration::updateValue('FKPAGSEGUROCT_MSG_3', $msg_3)
            Or !Configuration::updateValue('FKPAGSEGUROCT_DDD', '|11|12|13|14|15|16|17|18|19|21|22|24|27|28|31|32|33|34|35|37|38|41|42|43|44|45|46|47|48|49|51|53|54|55|61|62|63|64|65|66|67|68|69|71|73|74|75|77|79|81|82|83|84|85|86|87|88|89|91|92|93|94|95|96|97|98|99|')
            Or !Configuration::updateValue('FKPAGSEGUROCT_BOOTSTRAP', 'on')
            Or !Configuration::updateValue('FKPAGSEGUROCT_URL_SESSION_PRODUCAO', 'https://ws.pagseguro.uol.com.br/v2/sessions')
            Or !Configuration::updateValue('FKPAGSEGUROCT_URL_SESSION_SANDBOX', 'https://ws.sandbox.pagseguro.uol.com.br/v2/sessions')
            Or !Configuration::updateValue('FKPAGSEGUROCT_URL_TRANSACTION_PRODUCAO', 'https://ws.pagseguro.uol.com.br/v2/transactions')
            Or !Configuration::updateValue('FKPAGSEGUROCT_URL_TRANSACTION_SANDBOX', 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions')
            Or !Configuration::updateValue('FKPAGSEGUROCT_URL_NOTIFICATIONS_PRODUCAO', 'https://ws.pagseguro.uol.com.br/v2/transactions/notifications')
            Or !Configuration::updateValue('FKPAGSEGUROCT_URL_NOTIFICATIONS_SANDBOX', 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions/notifications')
            Or !Configuration::updateValue('FKPAGSEGUROCT_URL_JS_PRODUCAO', 'https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js')
            Or !Configuration::updateValue('FKPAGSEGUROCT_URL_JS_SANDBOX', 'https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js')) {

            return false;
        }

        return true;
    }

    public function uninstall() {
        if (!parent::uninstall()
            Or !$this->excluiTabelas()
            Or !$this->excluiMenus()
            Or !$this->unregisterHook('displayHeader')
            OR !$this->unregisterHook('displayPayment')
            OR !$this->unregisterHook('displayPaymentReturn')
        ) {

            return false;
        }

        // Exclui dados de Configuração
        if (!Db::getInstance()->delete("configuration", "name LIKE 'FKPAGSEGUROCT_%'")) {
            return false;
        }

        return true;
    }

    public function hookdisplayHeader($params) {
        // CSS
        if (Configuration::get('FKPAGSEGUROCT_BOOTSTRAP') == 'on') {
            $this->context->controller->addCSS($this->_path.'css/fkpagseguroct_front_bootstrap.css');
        }else {
            $this->context->controller->addCSS($this->_path.'css/fkpagseguroct_front.css');
        }

    }
    
    public function hookdisplayPayment($params) {
        
        if (!$this->active) {
            return false;
        }

        if (!$this->checkCurrency($params['cart'])) {
            return false;
        }
        
        $this->smarty->assign(array(
            'ps_version'    => _PS_VERSION_,
            'bootstrap'     => Configuration::get('FKPAGSEGUROCT_BOOTSTRAP'),
            'urlImg'        => $this->_path.'img/pagseguro_pagto.png'
        ));

        return $this->display(__FILE__, 'payment.tpl');
    }
    
    public function hookdisplayPaymentReturn($params) {
        
        if (!$this->active) {
            return false;
        }
        
        // Recupera informacoes da transacao
        $id_cart = Tools::getValue('id_cart');
        $cod_status = Tools::getValue('cod_status');
        $cod_transacao = Tools::getValue('cod_transacao');
        $link_boleto = Tools::getValue('link_boleto');
        $link_transf = Tools::getValue('link_transf');
        
        // Verifica se é para alterar o status do pedido
        $checkout = new FKpagseguroctInterfaceClass();
        $checkout->atualizaStatusPedido($id_cart, $cod_status);
        
        // Dados para pagina de finalizacao
        $this->smarty->assign(array(
            'fkpagseguroct_msg_1'           => html_entity_decode(Configuration::get('FKPAGSEGUROCT_MSG_1')),
            'fkpagseguroct_link_boleto'     => $link_boleto,
            'fkpagseguroct_link_transf'     => $link_transf,
            'fkpagseguroct_cod_transacao'   => $cod_transacao,
            'fkpagseguroct_pedido'          => $params['objOrder']->id,
            'fkpagseguroct_referencia'      => $params['objOrder']->reference,
            'fkpagseguroct_valor'           => number_format($params['objOrder']->total_paid, 2, ',', '.')
        ));
        
        return $this->display(__FILE__, 'payment_return.tpl');
    }
    
    public function checkCurrency($cart) {

        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);

        if (is_array($currencies_module))
            foreach ($currencies_module as $currency_module)
                if ($currency_order->id == $currency_module['id_currency'])
                    return true;
        return false;
    }
        

    public function getContent() {

        if (Tools::isSubmit('btnSubmit')) {

            $this->postValidation();

            if (!sizeof($this ->_postErrors)) {
                $this->_html .= $this->displayConfirmation($this->l('Configuração alterada'));
            }else {
                foreach ($this->_postErrors AS $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        }

        $this->_html .= $this->renderForm();

        return $this->_html;

    }

    public function renderForm() {

        // CSS
        $this->context->controller->addCSS($this->_path.'css/fkpagseguroct_admin.css');

        $this->procConfigGeral();
        $this->infConfiguracao();

        $this->smarty->assign(array(
            'pathInclude'   => _PS_MODULE_DIR_.$this->name.'/views/config/',
            'tabSelect'     => $this->_tab_select,
        ));

        return $this->display(__FILE__, 'views/config/mainConfig.tpl');
    }

    private function procConfigGeral() {

        // TPL a ser utilizado
        $name_tpl ='configGeral.tpl';

        $this->smarty->assign(array(
            'tab_2' => array(
                'nameTpl'                           => $name_tpl,
                'formAction'                        => Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']),
                'fkpagseguroct_modo'                => Configuration::get('FKPAGSEGUROCT_MODO'),
                'fkpagseguroct_email'               => Configuration::get('FKPAGSEGUROCT_EMAIL'),
                'fkpagseguroct_token'               => Configuration::get('FKPAGSEGUROCT_TOKEN'),
                'fkpagseguroct_charset'             => Configuration::get('FKPAGSEGUROCT_CHARSET'),
                'charsetOptions'                    => $this->_charsetOptions,
                'fkpagseguroct_http_version'        => Configuration::get('FKPAGSEGUROCT_HTTP_VERSION'),
                'httpVersions'                      => $this->_httpVersions,
                'fkpagseguroct_cartao'              => Configuration::get('FKPAGSEGUROCT_CARTAO'),
                'fkpagseguroct_boleto'              => Configuration::get('FKPAGSEGUROCT_BOLETO'),
                'fkpagseguroct_transf'              => Configuration::get('FKPAGSEGUROCT_TRANSF'),
                'fkpagseguroct_bb'                  => Configuration::get('FKPAGSEGUROCT_BB'),
                'fkpagseguroct_banrisul'            => Configuration::get('FKPAGSEGUROCT_BANRISUL'),
                'fkpagseguroct_bradesco'            => Configuration::get('FKPAGSEGUROCT_BRADESCO'),
                'fkpagseguroct_hsbc'                => Configuration::get('FKPAGSEGUROCT_HSBC'),
                'fkpagseguroct_itau'                => Configuration::get('FKPAGSEGUROCT_ITAU'),
                'fkpagseguroct_parcelas_sem_juros'  => Configuration::get('FKPAGSEGUROCT_PARCELAS_SEM_JUROS'),
                'fkpagseguroct_msg_1'               => html_entity_decode(Configuration::get('FKPAGSEGUROCT_MSG_1')),
                'fkpagseguroct_msg_2'               => html_entity_decode(Configuration::get('FKPAGSEGUROCT_MSG_2')),
                'fkpagseguroct_msg_3'               => html_entity_decode(Configuration::get('FKPAGSEGUROCT_MSG_3')),
                'fkpagseguroct_ddd'                 => Configuration::get('FKPAGSEGUROCT_DDD'),
                'fkpagseguroct_status_pago'         => Configuration::get('FKPAGSEGUROCT_STATUS_PAGO'),
                'fkpagseguroct_status_canc'         => Configuration::get('FKPAGSEGUROCT_STATUS_CANC'),
                'fkpagseguroct_bootstrap'           => Configuration::get('FKPAGSEGUROCT_BOOTSTRAP'),
            )
        ));

    }

    private function infConfiguracao() {

        // TPL a ser utilizado
        $name_tpl ='infConfiguracao.tpl';

        // Verifica SOAP
        $soap = true;
        $msgSoap = 'Habilite o SOAP em seu PHP';

        if (!extension_loaded('soap')) {
            $soap = false;
        }

        // Verifica cURL
        $curl = true;
        $msgCurl = 'Habilite o cURL em seu PHP';

        if (!function_exists('curl_init')) {
            $curl = false;
        }

        // Verifica Modulos Nativos
        $modulosNativos = true;
        $msgModulosNativos = 'A execução de Módulos não Nativos está desabilitada. Habilite a execução de Módulos não Nativos.';

        if (Configuration::get('PS_DISABLE_NON_NATIVE_MODULE') == '1') {
            $modulosNativos = false;
        }

        // Verifica Overrides
        $overrides = true;
        $msgOverrides = 'A execução de Overrides está desabilitada. Habilite a execução de Overrides.';

        if (Configuration::get('PS_DISABLE_OVERRIDES') == '1') {
            $overrides = false;
        }

        $this->smarty->assign(array(
            'tab_3' => array(
                'nameTpl'                   => $name_tpl,
                'urlImg'                    => Configuration::get('FKPAGSEGUROCT_URL_IMG'),
                'soap'                      => $soap,
                'msgSoap'                   => $msgSoap,
                'curl'                      => $curl,
                'msgCurl'                   => $msgCurl,
                'modulosNativos'            => $modulosNativos,
                'msgModulosNativos'         => $msgModulosNativos,
                'overrides'                 => $overrides,
                'msgOverrides'              => $msgOverrides,
            )
        ));
    }

    private function postValidation() {

        $sessao = Tools::getValue('origem');

        switch($sessao) {

            case 'configGeral':

                // Posicionamento da tab
                $this->_tab_select = '2';
                
                // Email
                if (Trim(Tools::getValue('fkpagseguroct_email')) == '') {
                    $this->_postErrors[] = $this->l('O campo Email é obrigatório.');
                }
                
                // Token
                if (Trim(Tools::getValue('fkpagseguroct_token')) == '') {
                    $this->_postErrors[] = $this->l('O campo Token é obrigatório.');
                }
                
                // Formas de Pagamento
                if (Tools::getValue('fkpagseguroct_cartao') != 'on' and Tools::getValue('fkpagseguroct_boleto') != 'on' and Tools::getValue('fkpagseguroct_transf') != 'on') {
                    $this->_postErrors[] = $this->l('Obrigatório escolher uma Forma de Pagamento.');
                }

                // Transferencia online
                if (Tools::getValue('fkpagseguroct_transf') == 'on') {

                    if (Tools::getValue('fkpagseguroct_bb') != 'on' and
                        Tools::getValue('fkpagseguroct_banrisul') != 'on' and
                        Tools::getValue('fkpagseguroct_bradesco') != 'on' and
                        Tools::getValue('fkpagseguroct_hsbc') != 'on' and
                        Tools::getValue('fkpagseguroct_itau') != 'on') {
                        $this->_postErrors[] = $this->l('Obrigatório escolher no mínimo um banco quando ativo Transferência Online.');
                    }

                }

                // Parcelas sem juros
                if (Trim(Tools::getValue('fkpagseguroct_parcelas_sem_juros')) == '') {
                    $this->_postErrors[] = $this->l('Campo "Parcelas sem juros" não preenchido');
                }else {
                    $valor = str_replace(',', '.', Tools::getValue('fkpagseguroct_parcelas_sem_juros'));

                    if (!is_numeric($valor)) {
                        $this->_postErrors[] = $this->l('O campo "Parcelas sem juros" não é numérico');
                    }else {
                        if ($valor < 0) {
                            $this->_postErrors[] = $this->l('O campo "Parcelas sem juros" não pode ser menor que 0 (zero)');
                        }else {
                            if ($valor == 1) {
                                $this->_postErrors[] = $this->l('O campo "Parcelas sem juros" não pode ser igual a 1 (um)');
                            }
                        }
                    }
                }
                
                // Mensagem de Pagamento Concluido
                if (Trim(Tools::getValue('fkpagseguroct_msg_1')) == '') {
                    $this->_postErrors[] = $this->l('O campo de mensagem Pagamento concluído é obrigatório.');
                }
                
                // Mensagem de Pagamento Nao Concluido
                if (Trim(Tools::getValue('fkpagseguroct_msg_2')) == '') {
                    $this->_postErrors[] = $this->l('O campo de mensagem Pagamento não concluído é obrigatório.');
                }
                
                // DDD
                if (Trim(Tools::getValue('fkpagseguroct_ddd')) == '') {
                    $this->_postErrors[] = $this->l('O campo de DDD é obrigatório.');
                }

                if (!$this->_postErrors) {
                    $this->postProcess($sessao);
                }

                break;

        }
    }

    private function postProcess($sessao) {

        switch($sessao) {

            case 'configGeral':

                // Salva as configurações
                Configuration::updateValue('FKPAGSEGUROCT_MODO', Tools::getValue('fkpagseguroct_modo'));
                Configuration::updateValue('FKPAGSEGUROCT_EMAIL', Trim(Tools::getValue('fkpagseguroct_email')));
                Configuration::updateValue('FKPAGSEGUROCT_TOKEN', Trim(Tools::getValue('fkpagseguroct_token')));
                Configuration::updateValue('FKPAGSEGUROCT_CHARSET', $this->_charsetOptions[Tools::getValue('fkpagseguroct_charset')]);
                Configuration::updateValue('FKPAGSEGUROCT_HTTP_VERSION', $this->_httpVersions[Tools::getValue('fkpagseguroct_http_version')]);
                Configuration::updateValue('FKPAGSEGUROCT_CARTAO', Tools::getValue('fkpagseguroct_cartao'));
                Configuration::updateValue('FKPAGSEGUROCT_BOLETO', Tools::getValue('fkpagseguroct_boleto'));
                Configuration::updateValue('FKPAGSEGUROCT_TRANSF', Tools::getValue('fkpagseguroct_transf'));
                Configuration::updateValue('FKPAGSEGUROCT_BB', Tools::getValue('fkpagseguroct_bb'));
                Configuration::updateValue('FKPAGSEGUROCT_BANRISUL', Tools::getValue('fkpagseguroct_banrisul'));
                Configuration::updateValue('FKPAGSEGUROCT_BRADESCO', Tools::getValue('fkpagseguroct_bradesco'));
                Configuration::updateValue('FKPAGSEGUROCT_HSBC', Tools::getValue('fkpagseguroct_hsbc'));
                Configuration::updateValue('FKPAGSEGUROCT_ITAU', Tools::getValue('fkpagseguroct_itau'));
                Configuration::updateValue('FKPAGSEGUROCT_PARCELAS_SEM_JUROS', Tools::getValue('fkpagseguroct_parcelas_sem_juros'));
                Configuration::updateValue('FKPAGSEGUROCT_MSG_1', htmlentities(Tools::getValue('fkpagseguroct_msg_1')));
                Configuration::updateValue('FKPAGSEGUROCT_MSG_2', htmlentities(Tools::getValue('fkpagseguroct_msg_2')));
                Configuration::updateValue('FKPAGSEGUROCT_MSG_3', htmlentities(Tools::getValue('fkpagseguroct_msg_3')));
                Configuration::updateValue('FKPAGSEGUROCT_DDD', Tools::getValue('fkpagseguroct_ddd'));
                Configuration::updateValue('FKPAGSEGUROCT_STATUS_PAGO', Tools::getValue('fkpagseguroct_status_pago'));
                Configuration::updateValue('FKPAGSEGUROCT_STATUS_CANC', Tools::getValue('fkpagseguroct_status_canc'));
                Configuration::updateValue('FKPAGSEGUROCT_BOOTSTRAP', Tools::getValue('fkpagseguroct_bootstrap'));

                break;

        }
    }

    private function criaStatus() {

        // Verifica se o status ja existe
        $id_state = Db::getInstance()->getRow('SELECT `id_order_state` FROM `'._DB_PREFIX_.'order_state` WHERE `module_name` = "'.$this->name.'"');

        if ($id_state) {
            // Grava configuracao de id_state_order
            if (!Configuration::updateValue('FKPAGSEGUROCT_STATE_ORDER', $id_state['id_order_state'])) {
                return false;
            }

            return true;
        }

        // Cria status para o PagSeguro
        $dados = array(
            'invoice'       => 0,
            'send_email'    => 0,
            'module_name'   => $this->name,
            'color'         => 'RoyalBlue',
            'unremovable'   => 0,
            'hidden'        => 0,
            'logable'       => 0,
            'delivery'      => 0,
            'shipped'       => 0,
            'paid'          => 0,
            'deleted'       => 0
        );

        if (!Db::getInstance()->insert('order_state', $dados)) {
            return false;
        }

        // Recupera id_order_state criado
        $id_state = Db::getInstance()->getRow('SELECT `id_order_state` FROM `'._DB_PREFIX_.'order_state` WHERE `module_name` = "'.$this->name.'"');

        if (!$id_state) {
            return false;
        }

        // Cria status por idioma
        $idiomas = Db::getInstance()->ExecuteS('SELECT `id_lang` FROM `'._DB_PREFIX_.'lang`');

        if (!$idiomas) {
            return false;
        }

        foreach ($idiomas as $idioma) {

            $dados = array(
                'id_order_state'    => $id_state['id_order_state'],
                'id_lang'           => $idioma['id_lang'],
                'name'              => $this->l('PagSeguro: aguardando pagamento'),
                'template'          => '',
            );

            if (!Db::getInstance()->insert('order_state_lang', $dados)) {
                return false;
            }

        }

        // Copia icone
        $origem = (_PS_MODULE_DIR_.$this->name."/img/pagseguro_status.gif");
        $destino = (_PS_IMG_DIR_."os/".$id_state['id_order_state'].".gif");

        if (!copy($origem, $destino)) {
            return false;
        }

        // Grava configuracao de id_state_order
        if (!Configuration::updateValue('FKPAGSEGUROCT_STATE_ORDER', $id_state['id_order_state'])) {
            return false;
        }

        return true;
    }

    private function criaMenus() {

        // Cria tab principal
        $main_tab = new Tab();
        $main_tab->class_name = $this->_tabClassName[0]['className'];

        $languages = Language::getLanguages();
        foreach ($languages as $language) {
            $main_tab->name[$language['id_lang']] = $this->_tabClassName[0]['name'];
        }

        $main_tab->id_parent = 0;
        $main_tab->module = $this->name;
        $main_tab->add();

        // Cria sub tabs do menu
        for ($i = 1; $i < count($this->_tabClassName); $i++) {

            $tab = new Tab();
            $tab->class_name = $this->_tabClassName[$i]['className'];

            $languages = Language::getLanguages();
            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = $this->_tabClassName[$i]['name'];
            }

            $tab->id_parent = $main_tab->id;
            $tab->module = $this->name;
            $tab->add();
        }

        return true;
    }

    private function excluiMenus() {

        for ($i = 0; $i < count($this->_tabClassName); $i++) {

            $id_tab = Tab::getIdFromClassName($this->_tabClassName[$i]['className']);
            if ($id_tab) {
                $tab = new Tab($id_tab);
                $tab->delete();
            }
        }

        return true;
    }


    private function criaTabelas() {

        // Cria a tabela de servicos
        $sql = 'CREATE TABLE IF NOT EXISTS `' ._DB_PREFIX_. 'fkpagseguroct` (
                `id_pagseguro`      int(10) NOT NULL AUTO_INCREMENT,
                `id_shop`           int(10) NULL,
                `cod_cliente`       int(10) NULL,
                `id_cart`           int(10) NULL,
                `cod_transacao`     varchar(50) NULL,
                `status`            int(10) NULL,
                `desc_status`       varchar(40) NULL,
                `pagto`             int(10) NULL,
                `desc_pagto`        varchar(40) NULL,
                `data_status`       datetime NULL,
                `data_pedido`       datetime NULL,
                PRIMARY KEY(`id_pagseguro`),
                INDEX (id_cart)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        Db::getInstance() -> execute($sql);

        return true;
    }

    private function excluiTabelas() {

        // Exclui as tabelas
        $sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkpagseguroct`;";
        Db::getInstance()->execute($sql);

        return true;
    }

}