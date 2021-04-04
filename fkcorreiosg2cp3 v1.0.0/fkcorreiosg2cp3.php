<?php

include_once(_PS_MODULE_DIR_.'fkcorreiosg2/models/FKcorreiosg2Class.php');
include_once(dirname(__FILE__).'/models/FKcorreiosg2cp3FreteClass.php');

class fkcorreiosg2cp3 extends CarrierModule {

    // Contem o id do Carrier em execucao
    public $id_carrier;

    private $prazoEntrega = array();

    private $html = '';
    private $postErrors = array();
    private $tab_select = '';
    private $abrirTransp = '0';
    private $abrirRegiao = '0';

    // Array Modalidade do Frete
    private $modalidadeFrete = array(
        '0' => array('descricao'    => 'Expresso',      'fatorCubagem' => '6000', 'tipo' => 'Aéreo'),
        '3' => array('descricao'    => '.Package',      'fatorCubagem' => '3333', 'tipo' => 'Rodoviário'),
        '4' => array('descricao'    => 'Rodoviário',    'fatorCubagem' => '3333', 'tipo' => 'Rodoviário'),
        '5' => array('descricao'    => 'Econômico',     'fatorCubagem' => '3333', 'tipo' => 'Rodoviário'),
        '6' => array('descricao'    => 'Doc',           'fatorCubagem' => '3333', 'tipo' => 'Rodoviário'),
        '7' => array('descricao'    => 'Corporate',     'fatorCubagem' => '6000', 'tipo' => 'Aéreo'),
        '9' => array('descricao'    => '.Com',          'fatorCubagem' => '6000', 'tipo' => 'Aéreo'),
        '10' => array('descricao'   => 'Internacional', 'fatorCubagem' => '6000', 'tipo' => 'Aéreo'),
        '12' => array('descricao'   => 'Cargo',         'fatorCubagem' => '6000', 'tipo' => 'Aéreo'),
        '14' => array('descricao'   => 'Emergencial',   'fatorCubagem' => '3333', 'tipo' => 'Rodoviário'),
    );

    // Array Tipo do Seguro
    private $tipoSeguro = array(
        'N' => 'Normal',
        'A' => 'Apólice própria',
    );

    // Array Pagamento do Frete no destino
    private $pagamento = array(
        'S' => 'Pelo destinatário',
        'N' => 'Pelo contratante'
    );

    // Array Tipo de Entrega
    private $tipoEntrega = array(
        'R' => 'Retirar na unidade JADLOG',
        'D' => 'Entrega a domicílio'
    );

    public function __construct() {

        $this->name     = 'fkcorreiosg2cp3';
        $this->tab      = 'shipping_logistics';
        $this->version  = '1.0.0';
        $this->author   = 'módulosFK';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('FKcorreiosg2cp3 - Complemento JADLOG');
        $this->description = $this->l('Envio de produtos através da transportadora JADLOG.');

        // Registro do complemento no FKcorreiosg2
        $this->incluiRegistroModulo();

        // URL/URI que variam conforme endereco do dominio
        Configuration::updateValue('FKCORREIOSG2CP3_URL_IMG', Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/img/');

    }

    public function install() {

        // Verifica se o FKcorreiosg2 esta instalado
        if (!module::isInstalled('fkcorreiosg2')) {
            $this->_errors[] = Tools::displayError('O módulo principal FKcorreiosg2 não está instalado.');
            return false;
        }

        if (!parent::install()
            Or !$this->criaTabelas()
            Or !$this->incluiRegistroModulo()
            Or !$this->registerHook('actionCarrierUpdate')
            Or !$this->registerHook('displayBeforeCarrier')) {

            return false;
        }

        if (!Configuration::hasKey('FKCORREIOSG2CP3_EXCLUIR_CONFIG')) {
            Configuration::updateValue('FKCORREIOSG2CP3_EXCLUIR_CONFIG', '');
        }

        Configuration::updateValue('FKCORREIOSG2CP3_URL_WS_JADLOG_FRETE', 'http://www.jadlog.com.br/JadlogEdiWs/services/ValorFreteBean?wsdl');

        // Processa atualizacao de versao
        if (!$this->atualizaVersaoModulo()) {
            $this->_errors[] = Tools::displayError('Erro durante atualização da versão do módulo.');
            return false;
        }

        return true;

    }

    public function uninstall() {

        // Recupera transportadoras
        $transp = $this->recuperaTransportadoras();

        // Instacia FKcorreiosClass
        $fkclass = new FKcorreiosg2Class();

        if (!parent::uninstall()
            Or !$fkclass->desinstalaCarrier($transp)
            Or !$this->excluiRegistroModulo()
            Or !$this->unregisterHook('actionCarrierUpdate')
            Or !$this->unregisterHook('displayBeforeCarrier')) {

            return false;
        }

        if (Configuration::get('FKCORREIOSG2CP3_EXCLUIR_CONFIG') == 'on') {
            // Exclui tabelas
            $this->excluiTabelas();

            // Exclui dados de Configuração
            if (!Db::getInstance()->delete("configuration", "name LIKE 'FKCORREIOSG2CP3_%'")) {
                return false;
            }
        }

        return true;
    }

    public function hookdisplayBeforeCarrier($params) {

        if (!isset($this->context->smarty->tpl_vars['delivery_option_list'])) {
            return;
        }

        $delivery_option_list = $this->context->smarty->tpl_vars['delivery_option_list'];

        foreach ($delivery_option_list->value as $id_address) {

            foreach ($id_address as $key) {

                foreach ($key['carrier_list'] as $id_carrier) {

                    if (isset($this->prazoEntrega[$id_carrier['instance']->id])) {

                        if (is_numeric($this->prazoEntrega[$id_carrier['instance']->id])) {

                            if ($this->prazoEntrega[$id_carrier['instance']->id] == 0) {
                                $msg = $this->l('entrega no mesmo dia');
                            }else {
                                if ($this->prazoEntrega[$id_carrier['instance']->id] > 1) {
                                    $msg = 'entrega em até '.$this->prazoEntrega[$id_carrier['instance']->id].$this->l(' dias úteis');
                                }else {
                                    $msg = 'entrega em '.$this->prazoEntrega[$id_carrier['instance']->id].$this->l(' dia útil');
                                }
                            }
                        }else {
                            $msg = $this->prazoEntrega[$id_carrier['instance']->id];
                        }

                        $id_carrier['instance']->delay[$this->context->cart->id_lang] = $msg;
                    }
                }
            }
        }

    }

    public function hookactionCarrierUpdate($params) {

        $atualizado = false;

        // Recupera dados da tabela
        $sql = 'SELECT *
                FROM '._DB_PREFIX_.'fkcorreiosg2cp3_transportadoras
                WHERE id_carrier = '.(int)$params['id_carrier'];

        $transp = Db::getInstance()->getRow($sql);

        // Verifica se houve alteracao no id
        if ((int)$transp['id_carrier'] != (int)$params['carrier']->id) {
            $novoId = $params['carrier']->id;
            $atualizado = true;
        }else {
            $novoId = $transp['id_carrier'];
        }

        // Verifica se houve alteracao na grade
        if ((int)$transp['grade'] != (int)$params['carrier']->grade) {
            $novaGrade = $params['carrier']->grade;
            $atualizado = true;
        }else {
            $novaGrade = $transp['grade'];
        }

        // Verifica se houve alteracao no campo ativo
        if ($transp['ativo'] != $params['carrier']->active) {
            $novoAtivo = $params['carrier']->active;
            $atualizado = true;
        }else {
            $novoAtivo = $transp['ativo'];
        }

        if ($atualizado == true) {

            // Atualiza dados da tabela de transportadoras
            $dados = array(
                'id_carrier'    => $novoId,
                'grade'         => $novaGrade,
                'ativo'         => $novoAtivo
            );

            Db::getInstance()->update('fkcorreiosg2cp3_transportadoras', $dados, 'id_carrier = '.(int)$transp['id_carrier']);

            // Atualiza dados da tabela de frete gratis
            $dados = array(
                'id_carrier'    => $novoId,
            );

            Db::getInstance()->update('fkcorreiosg2_frete_gratis', $dados, 'id_carrier = '.(int)$transp['id_carrier']);
        }

    }

    public function getContent() {

        if (!empty($_POST)) {

            $this->postValidation();

            if (sizeof($this->postErrors)) {
                foreach ($this->postErrors AS $erro) {
                    $this->html .= $this->displayError($erro);
                }
            }
        }

        $this->html .= $this->renderForm();

        return $this->html;

    }

    private function renderForm() {

        // CSS
        $this->context->controller->addCSS(_PS_MODULE_DIR_.'fkcorreiosg2/css/fkcorreiosg2_admin.css');
        $this->context->controller->addCSS($this->_path.'css/fkcorreiosg2cp3_admin.css');

        // JS
        $this->context->controller->addJS(_PS_MODULE_DIR_.'fkcorreiosg2/js/jquery.maskedinput.js');
        $this->context->controller->addJS($this->_path.'js/fkcorreiosg2cp3_admin.js');

        $this->configTransp();

        $this->smarty->assign(array(
            'fkcorreiosg2cp3' => array(
                'pathInclude'   => _PS_MODULE_DIR_.$this->name.'/views/config/',
                'tabSelect'     => $this->tab_select,
                'abrirTransp'   => $this->abrirTransp,
                'abrirRegiao'   => $this->abrirRegiao,
            )

        ));

        return $this->display(__FILE__, 'views/config/mainConfig.tpl');
    }

    private function configTransp() {

        // TPL a ser utilizado
        $name_tpl ='cadTransp.tpl';

        // Recupera dados da tabela de Transportadoras
        $transportadoras = $this->recuperaTransportadoras();

        // Instancia FKcorreiosg2Class
        $fkClass = new FKcorreiosg2Class();

        // Inclui os registros se não existir
        if (!$transportadoras) {
            $this->incluiTransportadora();

            // Recupera dados incluidos na tabela
            $transportadoras = $this->recuperaTransportadoras();
        }else {
            // Verifica e recupera carrier excluidos manualmente via opcao do Prestashop
            $fkClass->recuperaCarrierExcluido($transportadoras);
        }

        // Recupera dados da tabela de Regioes
        $regioes = $this->recuperaRegioes();

        // Instancia FKcorreiosg2Class
        $fkClass = new FKcorreiosg2Class();

        $this->smarty->assign(array(
            'tab_2' => array(
                'nameTpl'                           => $name_tpl,
                'formAction'                        => Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']),
                'tipoSeguro'                        => $this->tipoSeguro,
                'pagamento'                         => $this->pagamento,
                'entrega'                           => $this->tipoEntrega,
                'modalidadeFrete'                   => $this->modalidadeFrete,
                'transportadoras'                   => $transportadoras,
                'regioes'                           => $regioes,
                'arrayUF'                           => $fkClass->criaArrayUF($regioes),
                'urlLogoPS'                         => Configuration::get('FKCORREIOSG2_URL_LOGO_PS'),
                'uriLogoPS'                         => Configuration::get('FKCORREIOSG2_URI_LOGO_PS'),
                'urlImg'                            => Configuration::get('FKCORREIOSG2CP3_URL_IMG'),
                'fkcorreiosg2cp3_excluir_config'    => Tools::getValue('fkcorreiosg2cp3_excluir_config', Configuration::get('FKCORREIOSG2CP3_EXCLUIR_CONFIG')),
            )

        ));

    }

    private function postValidation() {

        $origem = Tools::getValue('origem');

        switch($origem) {

            case 'cadTransp':

                // Tab selecionada
                $this->tab_select = '2';

                // Recupera id da transportadora
                $id = Tools::getValue('idTransp');

                // Controle de abertura do toogle
                $this->abrirTransp = $id;

                //Valida os campos
                if (Tools::getValue('fkcorreiosg2cp3_transp_ativo_'.$id)) {

                    // Verifica o campo CNPJ
                    if (trim(Tools::getValue('fkcorreiosg2cp3_transp_cnpj_'.$id)) == '') {
                        $this->postErrors[] = $this->l('O campo "CNPJ do contratante" não está preenchido');
                    }

                    // Verifica o campo Senha
                    if (trim(Tools::getValue('fkcorreiosg2cp3_transp_senha_'.$id)) == '') {
                        $this->postErrors[] = $this->l('O campo "Senha" não está preenchido');
                    }

                    // Verifica o campo Valor da Coleta
                    if (trim(Tools::getValue('fkcorreiosg2cp3_transp_valor_coleta_'.$id)) == '') {
                        $this->postErrors[] = $this->l('O campo "Valor da coleta" não está preenchido');
                    } else {
                        $valor = str_replace(',', '.', Tools::getValue('fkcorreiosg2cp3_transp_valor_coleta_'.$id));

                        if (!is_numeric($valor)) {
                            $this->postErrors[] = $this->l('O campo "Valor da coleta" não é numérico');
                        } else {
                            if ($valor < 0) {
                                $this->postErrors[] = $this->l('O campo "Valor da coleta" não pode ser menor que 0 (zero)');
                            }
                        }
                    }

                    // Verifica o campo Nome da Transportadora
                    if (trim(Tools::getValue('fkcorreiosg2cp3_transp_nome_'.$id)) == '') {
                        $this->postErrors[] = $this->l('O campo "Nome da Transportadora" não está preenchido');
                    }

                    // Verifica o campo Grade
                    if (trim(Tools::getValue('fkcorreiosg2cp3_transp_grade_'.$id)) == '') {
                        $this->postErrors[] = $this->l('O campo "Grade" não está preenchido');
                    }else {
                        if (!is_numeric(Tools::getValue('fkcorreiosg2cp3_transp_grade_'.$id))) {
                            $this->postErrors[] = $this->l('O campo "Grade" não é numérico');
                        }else {
                            if (Tools::getValue('fkcorreiosg2cp3_transp_grade_'.$id) < 0) {
                                $this->postErrors[] = $this->l('O campo "Grade" não pode ser menor que 0 (zero)');
                            }
                        }
                    }

                }

                if (!$this->postErrors) {
                    $this->postProcess($origem);
                }

                break;

            case 'cadRegioes':

                // Tab selecionada
                $this->tab_select = '2';

                // Recupera id da transportadora
                $idTransp = Tools::getValue('idTransp');

                // Recupera id da regiao
                $idRegiao = Tools::getValue('idRegiao');

                // Controle de abertura do toogle
                $this->abrirTransp = $idTransp;
                $this->abrirRegiao = $idTransp;

                if (Tools::isSubmit('btnAddRegiao')) {
                    $this->incluiRegiao($idTransp);
                    break;
                }

                if (Tools::isSubmit('btnDelRegiao')) {
                    $this->excluiRegiao($idRegiao);
                    break;
                }

                //Valida os campos
                if (Tools::getValue('fkcorreiosg2cp3_regiao_ativo_'.$idRegiao)) {

                    // Nome da regiao
                    if (trim(Tools::getValue('fkcorreiosg2cp3_regiao_nome_'.$idRegiao)) == '') {
                        $this->postErrors[] = $this->l('O campo "Nome da Região" não está preenchido');
                    }

                    // Prazo de entrega
                    if (trim(Tools::getValue('fkcorreiosg2cp3_regiao_prazo_entrega_'.$idRegiao)) == '') {
                        $this->postErrors[] = $this->l('O campo "Prazo de Entrega" não está preenchido');
                    }

                    // Verifica o campo Peso Maximo por Produto
                    if (trim(Tools::getValue('fkcorreiosg2cp3_regiao_peso_maximo_produto_' . $idRegiao)) == '') {
                        $this->postErrors[] = $this->l('O campo "Peso Máximo por Produto" não está preenchido');
                    } else {
                        $valor = str_replace(',', '.', Tools::getValue('fkcorreiosg2cp3_regiao_peso_maximo_produto_' . $idRegiao));

                        if (!is_numeric($valor)) {
                            $this->postErrors[] = $this->l('O campo "Peso Máximo por Produto" não é numérico');
                        } else {
                            if ($valor < 0) {
                                $this->postErrors[] = $this->l('O campo "Peso Máximo por Produto" não pode ser menor que 0 (zero)');
                            }
                        }
                    }

                    // Campo "Estados atendidos" e "Intervalo de CEPs atendidos"
                    if (!Tools::getValue('fkcorreiosg2cp3_regiao_uf_'.$idRegiao) and !Tools::getValue('fkcorreiosg2cp3_regiao_cep_'.$idRegiao)) {
                        $this->postErrors[] = $this->l('O campo "Estados Atendidos" ou "Intervalo CEPs Atendidos" devem ser preenchidos');
                    }

                    // Verifica o campo Percentual de Desconto
                    if (trim(Tools::getValue('fkcorreiosg2cp3_regiao_percentual_desc_'.$idRegiao)) == '') {
                        $this->postErrors[] = $this->l('O campo "Percentual de Desconto" não está preenchido');
                    }else {
                        $valor = str_replace(',', '.', Tools::getValue('fkcorreiosg2cp3_regiao_percentual_desc_'.$idRegiao));

                        if (!is_numeric($valor)) {
                            $this->postErrors[] = $this->l('O campo "Percentual de Desconto" não é numérico');
                        }else {
                            if ($valor < 0) {
                                $this->postErrors[] = $this->l('O campo "Percentual de Desconto" não pode ser menor que 0 (zero)');
                            }
                        }
                    }

                    // Verifica o campo Valor do Pedido
                    if (trim(Tools::getValue('fkcorreiosg2cp3_regiao_valor_pedido_desc_'.$idRegiao)) == '') {
                        $this->postErrors[] = $this->l('O campo "Valor do Pedido" não está preenchido');
                    }else {
                        $valor = str_replace(',', '.', Tools::getValue('fkcorreiosg2cp3_regiao_valor_pedido_desc_'.$idRegiao));

                        if (!is_numeric($valor)) {
                            $this->postErrors[] = $this->l('O campo "Valor do Pedido" não é numérico');
                        }else {
                            if ($valor < 0) {
                                $this->postErrors[] = $this->l('O campo "Valor do Pedido" não pode ser menor que 0 (zero)');
                            }
                        }
                    }

                }

                if (!$this->postErrors) {
                    $this->postProcess($origem);
                }

                break;

        }
    }

    private function postProcess($origem) {

        // Exclui cache
        $this->excluiCache();

        switch ($origem) {

            case 'cadTransp':

                Configuration::updateValue('FKCORREIOSG2CP3_EXCLUIR_CONFIG', Trim(Tools::getValue('fkcorreiosg2cp3_excluir_config')));

                // Recupera id da transportadora
                $id = Tools::getValue('idTransp');

                // Altera cadastro de transportadoras
                $dados = array(
                    'cnpj'              => Tools::getValue('fkcorreiosg2cp3_transp_cnpj_'.$id),
                    'senha'             => Tools::getValue('fkcorreiosg2cp3_transp_senha_'.$id),
                    'valor_coleta'      => Tools::getValue('fkcorreiosg2cp3_transp_valor_coleta_'.$id),
                    'tipo_seguro'       => Tools::getValue('fkcorreiosg2cp3_transp_tipo_seguro_'.$id),
                    'pagamento'         => Tools::getValue('fkcorreiosg2cp3_transp_pagamento_'.$id),
                    'tipo_entrega'      => Tools::getValue('fkcorreiosg2cp3_transp_entrega_'.$id),
                    'nome_transp'       => Tools::getValue('fkcorreiosg2cp3_transp_nome_'.$id),
                    'grade'             => Tools::getValue('fkcorreiosg2cp3_transp_grade_'.$id),
                    'ativo'             => (!Tools::getValue('fkcorreiosg2cp3_transp_ativo_'.$id) ? '0' : '1')
                );

                Db::getInstance()->update('fkcorreiosg2cp3_transportadoras', $dados, 'id = '.(int)$id);

                // Instancia FKcorreiosg2Class
                $fkClass = new FKcorreiosg2Class();

                // Recupera dados da Transportadora
                $sql = "SELECT nome_transp, id_carrier, ativo, grade
                        FROM "._DB_PREFIX_."fkcorreiosg2cp3_transportadoras
                        WHERE id = ".$id;

                $transp = Db::getInstance()->getRow($sql);

                // Altera o Carrier
                $parm = array(
                    'nomeCarrier'   => $transp['nome_transp'],
                    'idCarrier'     => $transp['id_carrier'],
                    'ativo'         => $transp['ativo'],
                    'grade'         => $transp['grade'],
                    'arrayLogo'     => $_FILES,
                    'campoLogo'     => 'fkcorreiosg2cp3_transp_logo_'.$id,
                );


                if (!$fkClass->alteraCarrier($parm)) {
                    $this->postErrors[] = $fkClass->getMsgErro();
                }

                break;

            case 'cadRegioes':

                // Recupera id da regra de preco
                $id = Tools::getValue('idRegiao');

                // Recupera o Fator Cubagem
                $modalidadeFrete = Tools::getValue('fkcorreiosg2cp3_regiao_modalidade_frete_'.$id);
                $fatorCubagem = $this->modalidadeFrete[$modalidadeFrete]['fatorCubagem'];

                // Instancia FKcorreiosg2Class
                $fkClass = new FKcorreiosg2Class();

                // Formata UFs
                $regiaoUF = $fkClass->formataGravacaoUF(Tools::getValue('fkcorreiosg2cp3_regiao_uf_'.$id));

                // Altera fkcorreiosg2_frete_gratis
                $dados = array(
                    'modalidade_frete'      => $modalidadeFrete,
                    'fator_cubagem'         => $fatorCubagem,
                    'nome_regiao'           => Tools::getValue('fkcorreiosg2cp3_regiao_nome_'.$id),
                    'prazo_entrega'         => Tools::getValue('fkcorreiosg2cp3_regiao_prazo_entrega_'.$id),
                    'peso_maximo_produto'   => str_replace(',', '.', Tools::getValue('fkcorreiosg2cp3_regiao_peso_maximo_produto_'.$id)),
                    'filtro_regiao_uf'      => Tools::getValue('fkcorreiosg2cp3_regiao_filtro_uf_'.$id),
                    'regiao_uf'             => $regiaoUF,
                    'regiao_cep'            => Tools::getValue('fkcorreiosg2cp3_regiao_cep_'.$id),
                    'regiao_cep_excluido'   => Tools::getValue('fkcorreiosg2cp3_regiao_cep_excluido_'.$id),
                    'percentual_desconto'   => str_replace(',', '.', Tools::getValue('fkcorreiosg2cp3_regiao_percentual_desc_'.$id)),
                    'valor_pedido_desconto' => str_replace(',', '.', Tools::getValue('fkcorreiosg2cp3_regiao_valor_pedido_desc_'.$id)),
                    'ativo'                 => (!Tools::getValue('fkcorreiosg2cp3_regiao_ativo_'.$id) ? '0' : '1'),
                );

                Db::getInstance()->update('fkcorreiosg2cp3_regioes', $dados, 'id = '.(int)$id);

                break;
        }
    }

    public function getOrderShippingCost($params, $shipping_cost) {

        // Instacia FKcorreiosg2cp3FreteClass
        $freteClass = new FKcorreiosg2cp3FreteClass();

        // Ignora Carrier se frete nao calculado
        if (!$freteClass->calculaFretePS($params, $this->id_carrier)) {
            return false;
        }

        // Recupera dados do frete
        $frete = $freteClass->getFreteCarrier();

        // Grava array com o Prazo de entrega
        $this->prazoEntrega[$this->id_carrier] = $frete['prazoEntrega'];

        // Retorna Valor do Frete
        return (float)$frete['valorFrete'];
    }

    public function getOrderShippingCostExternal($params) {
        return $this->getOrderShippingCost($params, 0);
    }

    private function recuperaTransportadoras() {

        $sql = "SELECT *
                FROM "._DB_PREFIX_."fkcorreiosg2cp3_transportadoras
                WHERE id_shop = ".$this->context->shop->id."
                ORDER BY nome_transp";

        return Db::getInstance()->ExecuteS($sql);
    }

    private function recuperaRegioes() {

        $sql = "SELECT *
                FROM "._DB_PREFIX_."fkcorreiosg2cp3_regioes
                WHERE id_shop = ".$this->context->shop->id."
                ORDER BY nome_regiao";

        return Db::getInstance()->ExecuteS($sql);
    }

    private function incluiTransportadora() {

        $nomeTransp = 'JADLOG';

        // Instacia FKcorreiosClass
        $fkclass = new FKcorreiosg2Class();

        // Inclui Carrier no Prestashop
        $parm = array(
            'name' 					=> $nomeTransp,
            'id_tax_rules_group' 	=> 0,
            'active' 				=> false,
            'deleted' 				=> false,
            'shipping_handling' 	=> false,
            'range_behavior' 		=> true,
            'is_module' 			=> true,
            'shipping_external' 	=> true,
            'shipping_method' 		=> 0,
            'external_module_name' 	=> $this->name,
            'need_range' 			=> true,
            'url' 					=> '',
            'is_free' 				=> false,
            'grade' 				=> 0,
        );

        $idCarrier = $fkclass->instalaCarrier($parm);

        // Insere registro na tabela de transportadoras
        $dados = array(
            'id_shop'       => $this->context->shop->id,
            'id_carrier'    => $idCarrier,
            'tipo_seguro'   => 'N',
            'valor_coleta'  => '0',
            'pagamento'     => 'N',
            'tipo_entrega'  => 'D',
            'nome_transp'   => $nomeTransp,
            'grade'         => '0',
            'ativo'         => '0'
        );

        Db::getInstance()->insert('fkcorreiosg2cp3_transportadoras', $dados);
    }

    private function incluiRegiao($idTransp) {

        // Insere registro na tabela de transportadoras
        $dados = array(
            'id_shop'               => $this->context->shop->id,
            'id_transp'             => $idTransp,
            'modalidade_frete'      => '4',
            'nome_regiao'           => 'Nova Região',
            'peso_maximo_produto'   => '0',
            'filtro_regiao_uf'      => '1',
            'percentual_desconto'   => '0',
            'valor_pedido_desconto' => '0',
            'ativo'                 => '0'
        );

        Db::getInstance()->insert('fkcorreiosg2cp3_regioes', $dados);

    }

    private function excluiRegiao($id) {
        // Exclui o registro do cadastro de regioes
        Db::getInstance()->delete('fkcorreiosg2cp3_regioes', 'id = '.(int)$id);
    }

    private function incluiRegistroModulo(){

        if (module::isInstalled('fkcorreiosg2') and module::isInstalled($this->name)) {

            // Verifica se ja esta registrado
            $sql = "SELECT id
                FROM "._DB_PREFIX_."fkcorreiosg2_complementos
                WHERE id_shop = ".$this->context->shop->id." AND
                      modulo = '".$this->name."'";

            $complemento = Db::getInstance()->getRow($sql);

            if ($complemento) {
                // Atualiza descricao
                $dados = array(
                    'descricao' => $this->description,
                );

                if (!Db::getInstance()->update('fkcorreiosg2_complementos', $dados, 'id = '.$complemento['id'])) {
                    return false;
                }
            }else {
                // Insere registro
                $dados = array(
                    'id_shop'   => $this->context->shop->id,
                    'modulo'    => $this->name,
                    'descricao' => $this->description,
                    'frete'     => true,
                );

                if (!Db::getInstance()->insert('fkcorreiosg2_complementos', $dados)) {
                    return false;
                }
            }

        }

        return true;
    }

    private function excluiRegistroModulo() {

        if (!Db::getInstance()->delete("fkcorreiosg2_complementos", "modulo = '".$this->name."'")) {
            return false;
        }

        return true;
    }

    private function excluiCache() {
        Db::getInstance()->delete('fkcorreiosg2cp4_cache');
    }

    private function atualizaVersaoModulo() {

        return true;
    }

    private function criaTabelas() {

        $db = Db::getInstance();

        // Cria tabela com o cadastro das transportadoras
        $sql = 'CREATE TABLE IF NOT EXISTS `' ._DB_PREFIX_. 'fkcorreiosg2cp3_transportadoras` (
            	`id` 		    int(10) 	    NOT NULL AUTO_INCREMENT,
				`id_shop`       int(10),
            	`id_carrier`    int(10),
            	`cnpj`          varchar(20),
            	`senha`         varchar(8),
            	`valor_coleta`  decimal(20,2),
            	`tipo_seguro`   varchar(1),
            	`pagamento`     varchar(1),
            	`tipo_entrega`  varchar(1),
				`nome_transp`   varchar(64),
            	`grade`         int(10),
            	`ativo`         tinyint(1),
            	INDEX (`id_carrier`),
            	PRIMARY KEY (`id`)
            	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);

        // Cria tabela com as regioes
        $sql = 'CREATE TABLE IF NOT EXISTS `' ._DB_PREFIX_. 'fkcorreiosg2cp3_regioes` (
            	`id` 					    int(10) 		NOT NULL AUTO_INCREMENT,
            	`id_shop`			        int(10),
				`id_transp`	                int(10),
				`modalidade_frete`          int(10),
				`fator_cubagem`             decimal(20,4),
				`nome_regiao`  			    varchar(100),
				`prazo_entrega`             varchar(50),
				`peso_maximo_produto`	    decimal(20,4),
				`filtro_regiao_uf`	        int(10),
				`regiao_uf`				    varchar(100),
				`regiao_cep`			    text,
				`regiao_cep_excluido`	    text,
				`percentual_desconto`       decimal(20,2),
            	`valor_pedido_desconto`     decimal(20,2),
				`ativo` 			        tinyint(1),
				INDEX (`id_transp`),
            	PRIMARY KEY (`id`)
            	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);

        // Cria a tabela de cache
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fkcorreiosg2cp3_cache` (
                `id`            int(10)     NOT NULL AUTO_INCREMENT,
                `hash`          varchar(32),
                `valor_frete`   decimal(20,2),
                `prazo_entrega` varchar(50),
                INDEX (`hash`),
                PRIMARY KEY  (`id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);

        return true;
    }

    private function excluiTabelas() {

        // Exclui as tabelas
        $sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkcorreiosg2cp3_transportadoras`;";
        Db::getInstance()->execute($sql);

        $sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkcorreiosg2cp3_regioes`;";
        Db::getInstance()->execute($sql);

        $sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkcorreiosg2cp3_cache`;";
        Db::getInstance()->execute($sql);

    }

}