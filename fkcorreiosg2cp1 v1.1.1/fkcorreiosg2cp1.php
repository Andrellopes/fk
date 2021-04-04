<?php

include_once(_PS_MODULE_DIR_.'fkcorreiosg2/models/FKcorreiosg2Class.php');
include_once(dirname(__FILE__).'/models/FKcorreiosg2cp1FreteClass.php');

class fkcorreiosg2cp1 extends CarrierModule {

    // Contem o id do Carrier em execucao
    public $id_carrier;

    private $prazoEntrega = array();

    private $html = '';
    private $postErrors = array();
    private $tab_select = '';
    private $abrirTransp = '0';
    private $abrirRegra = '0';
    private $abrirRegiao = '0';

    public function __construct() {

        $this->name     = 'fkcorreiosg2cp1';
        $this->tab      = 'shipping_logistics';
        $this->version  = '1.1.1';
        $this->author   = 'módulosFK';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('FKcorreiosg2cp1 - Complemento Transportadoras');
        $this->description = $this->l('Envio de produtos através de transportadoras em geral.');

        // Registro do complemento no FKcorreiosg2
        $this->incluiRegistroModulo();

        // URL/URI que variam conforme endereco do dominio
        Configuration::updateValue('FKCORREIOSG2CP1_URL_IMG', Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/img/');

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

        // Atualiza configuracoes se nao existir
        if (!Configuration::hasKey('FKCORREIOSG2CP1_EXCLUIR_CONFIG')) {
            Configuration::updateValue('FKCORREIOSG2CP1_EXCLUIR_CONFIG', '');
        }

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

        if (Configuration::get('FKCORREIOSG2CP1_EXCLUIR_CONFIG') == 'on') {
            // Exclui tabelas
            $this->excluiTabelas();

            // Exclui dados de Configuração
            if (!Db::getInstance()->delete("configuration", "name LIKE 'FKCORREIOSG2CP1_%'")) {
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
                FROM '._DB_PREFIX_.'fkcorreiosg2cp1_transportadoras
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

            Db::getInstance()->update('fkcorreiosg2cp1_transportadoras', $dados, 'id_carrier = '.(int)$transp['id_carrier']);

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
        $this->context->controller->addCSS($this->_path.'css/fkcorreiosg2cp1_admin.css');

        // JS
        $this->context->controller->addJS(_PS_MODULE_DIR_.'fkcorreiosg2/js/jquery.maskedinput.js');
        $this->context->controller->addJS($this->_path.'js/fkcorreiosg2cp1_admin.js');

        $this->configGeral();
        $this->configTransp();

        $this->smarty->assign(array(
            'fkcorreiosg2cp1' => array(
                'pathInclude'   => _PS_MODULE_DIR_.$this->name.'/views/config/',
                'tabSelect'     => $this->tab_select,
                'abrirTransp'   => $this->abrirTransp,
                'abrirRegra'    => $this->abrirRegra,
                'abrirRegiao'   => $this->abrirRegiao,
            )

        ));

        return $this->display(__FILE__, 'views/config/mainConfig.tpl');
    }

    private function configGeral() {

        // TPL a ser utilizado
        $name_tpl ='configGeral.tpl';

        $this->smarty->assign(array(
            'tab_2' => array(
                'nameTpl'                                   => $name_tpl,
                'formAction'                                => Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']),
                'fkcorreiosg2cp1_excluir_config'           => Tools::getValue('fkcorreiosg2cp1_excluir_config', Configuration::get('FKCORREIOSG2CP1_EXCLUIR_CONFIG')),
            )
        ));

    }

    private function configTransp() {

        // TPL a ser utilizado
        $name_tpl ='cadTransp.tpl';

        // Recupera dados da tabela de Transportadoras
        $transportadoras = $this->recuperaTransportadoras();

        // Instancia FKcorreiosg2Class
        $fkClass = new FKcorreiosg2Class();

        // Verifica e recupera carrier excluidos manualmente via opcao do Prestashop
        if ($transportadoras) {
            $fkClass->recuperaCarrierExcluido($transportadoras);
        }

        // Recupera dados da tabela de Regras de Precos
        $regrasPrecos = $this->recuperaRegrasPrecos();

        // Recupera dados da tabela de Regioes
        $regioes = $this->recuperaRegioes();

        $this->smarty->assign(array(
            'tab_3' => array(
                'nameTpl'               => $name_tpl,
                'formAction'            => Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']),
                'transportadoras'       => $transportadoras,
                'regrasPrecos'          => $regrasPrecos,
                'regioes'               => $regioes,
                'arrayUF'               => $fkClass->criaArrayUF($regioes),
                'urlLogoPS'             => Configuration::get('FKCORREIOSG2_URL_LOGO_PS'),
                'uriLogoPS'             => Configuration::get('FKCORREIOSG2_URI_LOGO_PS'),
                'urlImg'                => Configuration::get('FKCORREIOSG2CP1_URL_IMG'),
            )

        ));

    }

    private function postValidation() {

        $origem = Tools::getValue('origem');

        switch($origem) {

            case 'configGeral':

                // Tab selecionada
                $this->tab_select = '2';

                $this->postProcess($origem);

                break;

            case 'cadTransp':

                // Tab selecionada
                $this->tab_select = '3';

                // Recupera id da transportadora
                $id = Tools::getValue('idTransp');

                // Controle de abertura do toogle
                $this->abrirTransp = $id;

                if (Tools::isSubmit('btnAddTransp')) {
                    $this->incluiTransportadora();
                    break;
                }

                if (Tools::isSubmit('btnDelTransp')) {
                    $this->excluiTransportadora($id);
                    break;
                }

                //Valida os campos
                if (Tools::getValue('fkcorreiosg2cp1_transp_ativo_'.$id)) {

                    // Verifica o campo Nome da Transportadora
                    if (trim(Tools::getValue('fkcorreiosg2cp1_transp_nome_'.$id)) == '') {
                        $this->postErrors[] = $this->l('O campo "Nome da Transportadora" não está preenchido');
                    }

                    // Verifica o campo Grade
                    if (trim(Tools::getValue('fkcorreiosg2cp1_transp_grade_'.$id)) == '') {
                        $this->postErrors[] = $this->l('O campo "Grade" não está preenchido');
                    }else {
                        if (!is_numeric(Tools::getValue('fkcorreiosg2cp1_transp_grade_'.$id))) {
                            $this->postErrors[] = $this->l('O campo "Grade" não é numérico');
                        }else {
                            if (Tools::getValue('fkcorreiosg2cp1_transp_grade_'.$id) < 0) {
                                $this->postErrors[] = $this->l('O campo "Grade" não pode ser menor que 0 (zero)');
                            }
                        }
                    }

                }

                if (!$this->postErrors) {
                    $this->postProcess($origem);
                }

                break;

            case 'cadRegras':

                // Tab selecionada
                $this->tab_select = '3';

                // Recupera id da transportadora
                $idTransp = Tools::getValue('idTransp');

                // Recupera id da regra de preco
                $idRegra = Tools::getValue('idRegra');

                // Controle de abertura do toogle
                $this->abrirTransp = $idTransp;
                $this->abrirRegra = $idTransp;

                if (Tools::isSubmit('btnAddRegra')) {
                    $this->incluiRegraPreco($idTransp);
                    break;
                }

                if (Tools::isSubmit('btnDelRegra')) {
                    $this->excluiRegraPreco($idRegra);
                    break;
                }

                //Valida os campos
                if (Tools::getValue('fkcorreiosg2cp1_regra_ativo_'.$idRegra)) {

                    // Verifica o campo Nome da Regra
                    if (trim(Tools::getValue('fkcorreiosg2cp1_regra_nome_'.$idRegra)) == '') {
                        $this->postErrors[] = $this->l('O campo "Nome da Regra" não está preenchido');
                    }

                    // Verifica o campo Valor/Percentual do Tipo de Regra
                    if (trim(Tools::getValue('fkcorreiosg2cp1_regra_tipo_valor_'.$idRegra)) == '') {
                        $this->postErrors[] = $this->l('O campo "Valor/Percentual" do item Tipo de Regra não está preenchido');
                    }else {
                        $valor = str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regra_tipo_valor_'.$idRegra));

                        if (!is_numeric($valor)) {
                            $this->postErrors[] = $this->l('O campo "Valor/Percentual" do item Tipo de Regra não é numérico');
                        }else {
                            if ($valor <= 0) {
                                $this->postErrors[] = $this->l('O campo "Valor/Percentual" do item Tipo de Regra não pode ser menor ou igual a 0 (zero)');
                            }
                        }
                    }

                    // Verifica valores do item Formula
                    if (trim(Tools::getValue('fkcorreiosg2cp1_regra_tipo_'.$idRegra)) != _TIPO_VALOR_FIXO_) {

                        // Verifica o campo Valor da Formula
                        if (trim(Tools::getValue('fkcorreiosg2cp1_regra_formula_valor_'.$idRegra)) == '') {
                            $this->postErrors[] = $this->l('O campo "Valor" do item Fórmula não está preenchido');
                        }else {
                            $valor = str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regra_formula_valor_'.$idRegra));

                            if (!is_numeric($valor)) {
                                $this->postErrors[] = $this->l('O campo "Valor" do item Fórmula não é numérico');
                            }else {
                                if (trim(Tools::getValue('fkcorreiosg2cp1_regra_formula_'.$idRegra)) == _FORMULA_POR_INTERVALO_) {
                                    if ($valor <= 0) {
                                        $this->postErrors[] = $this->l('O campo "Valor" do item Fórmula não pode ser menor ou igual a 0 (zero) quando selecionado "por Intervalo"');
                                    }
                                }else {
                                    if ($valor < 0) {
                                        $this->postErrors[] = $this->l('O campo "Valor" do item Fórmula não pode ser menor que 0 (zero)');
                                    }
                                }
                            }
                        }

                        // Verifica o campo Valor Minimo da Formula
                        if (trim(Tools::getValue('fkcorreiosg2cp1_regra_formula_valor_minimo_'.$idRegra)) == '') {
                            $this->postErrors[] = $this->l('O campo "Valor Mínimo" do item Fórmula não está preenchido');
                        }else {
                            $valor = str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regra_formula_valor_minimo_'.$idRegra));

                            if (!is_numeric($valor)) {
                                $this->postErrors[] = $this->l('O campo "Valor Mínimo" do item Fórmula não é numérico');
                            }else {
                                if ($valor < 0) {
                                    $this->postErrors[] = $this->l('O campo "Valor Mínimo" do item Fórmula não pode ser menor que 0 (zero)');
                                }
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
                $this->tab_select = '3';

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
                if (Tools::getValue('fkcorreiosg2cp1_regiao_ativo_'.$idRegiao)) {

                    // Nome da regiao
                    if (trim(Tools::getValue('fkcorreiosg2cp1_regiao_nome_'.$idRegiao)) == '') {
                        $this->postErrors[] = $this->l('O campo "Nome da Região" não está preenchido');
                    }

                    // Prazo de entrega
                    if (trim(Tools::getValue('fkcorreiosg2cp1_regiao_prazo_entrega_'.$idRegiao)) == '') {
                        $this->postErrors[] = $this->l('O campo "Prazo de Entrega" não está preenchido');
                    }

                    // Verifica o campo Peso Maximo por Produto
                    if (trim(Tools::getValue('fkcorreiosg2cp1_regiao_peso_maximo_produto_' . $idRegiao)) == '') {
                        $this->postErrors[] = $this->l('O campo "Peso Máximo por Produto" não está preenchido');
                    } else {
                        $valor = str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regiao_peso_maximo_produto_' . $idRegiao));

                        if (!is_numeric($valor)) {
                            $this->postErrors[] = $this->l('O campo "Peso Máximo por Produto" não é numérico');
                        } else {
                            if ($valor < 0) {
                                $this->postErrors[] = $this->l('O campo "Peso Máximo por Produto" não pode ser menor que 0 (zero)');
                            }
                        }
                    }

                    // Campo "Estados atendidos" e "Intervalo de CEPs atendidos"
                    if (!Tools::getValue('fkcorreiosg2cp1_regiao_uf_'.$idRegiao) and !Tools::getValue('fkcorreiosg2cp1_regiao_cep_'.$idRegiao)) {
                        $this->postErrors[] = $this->l('O campo "Estados Atendidos" ou "Intervalo CEPs Atendidos" devem ser preenchidos');
                    }

                    // Tabela de precos
                    if (trim(Tools::getValue('fkcorreiosg2cp1_regiao_tabela_preco_'.$idRegiao)) == '') {
                        $this->postErrors[] = $this->l('O campo "Tabela de Preços" não está preenchido');
                    }

                    // Verifica o campo Fator Cubagem
                    if (trim(Tools::getValue('fkcorreiosg2cp1_regiao_fator_cubagem_' . $idRegiao)) == '') {
                        $this->postErrors[] = $this->l('O campo "Fator Cubagem" não está preenchido');
                    } else {
                        $valor = str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regiao_fator_cubagem_' . $idRegiao));

                        if (!is_numeric($valor)) {
                            $this->postErrors[] = $this->l('O campo "Fator Cubagem" não é numérico');
                        } else {
                            if ($valor < 0) {
                                $this->postErrors[] = $this->l('O campo "Fator Cubagem" não pode ser menor que 0 (zero)');
                            }
                        }
                    }

                    // Verifica o campo Valor do Kilo excedente
                    if (trim(Tools::getValue('fkcorreiosg2cp1_regiao_kilo_adicional_' . $idRegiao)) == '') {
                        $this->postErrors[] = $this->l('O campo "Valor por Kilo Excedente" não está preenchido');
                    } else {
                        $valor = str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regiao_kilo_adicional_' . $idRegiao));

                        if (!is_numeric($valor)) {
                            $this->postErrors[] = $this->l('O campo "Valor por Kilo Excedente" não é numérico');
                        } else {
                            if ($valor < 0) {
                                $this->postErrors[] = $this->l('O campo "Valor por Kilo Excedente" não pode ser menor que 0 (zero)');
                            }
                        }
                    }

                    // Verifica o campo Valor Adicional Fixo
                    if (trim(Tools::getValue('fkcorreiosg2cp1_regiao_valor_adicional_' . $idRegiao)) == '') {
                        $this->postErrors[] = $this->l('O campo "Valor Fixo Adicional" não está preenchido');
                    } else {
                        $valor = str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regiao_valor_adicional_' . $idRegiao));

                        if (!is_numeric($valor)) {
                            $this->postErrors[] = $this->l('O campo "Valor Fixo Adicional" não é numérico');
                        } else {
                            if ($valor < 0) {
                                $this->postErrors[] = $this->l('O campo "Valor Fixo Adicional" não pode ser menor que 0 (zero)');
                            }
                        }
                    }

                    // Verifica o campo Valor do Frete Minimo
                    if (trim(Tools::getValue('fkcorreiosg2cp1_regiao_frete_minimo_' . $idRegiao)) == '') {
                        $this->postErrors[] = $this->l('O campo "Frete Mínimo" não está preenchido');
                    } else {
                        $valor = str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regiao_frete_minimo_' . $idRegiao));

                        if (!is_numeric($valor)) {
                            $this->postErrors[] = $this->l('O campo "Frete Mínimo" não é numérico');
                        } else {
                            if ($valor < 0) {
                                $this->postErrors[] = $this->l('O campo "Frete Mínimo" não pode ser menor que 0 (zero)');
                            }
                        }
                    }

                    // Verifica o campo Percentual de Desconto
                    if (trim(Tools::getValue('fkcorreiosg2cp1_regiao_percentual_desc_'.$idRegiao)) == '') {
                        $this->postErrors[] = $this->l('O campo "Percentual de Desconto" não está preenchido');
                    }else {
                        $valor = str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regiao_percentual_desc_'.$idRegiao));

                        if (!is_numeric($valor)) {
                            $this->postErrors[] = $this->l('O campo "Percentual de Desconto" não é numérico');
                        }else {
                            if ($valor < 0) {
                                $this->postErrors[] = $this->l('O campo "Percentual de Desconto" não pode ser menor que 0 (zero)');
                            }
                        }
                    }

                    // Verifica o campo Valor do Pedido
                    if (trim(Tools::getValue('fkcorreiosg2cp1_regiao_valor_pedido_desc_'.$idRegiao)) == '') {
                        $this->postErrors[] = $this->l('O campo "Valor do Pedido" não está preenchido');
                    }else {
                        $valor = str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regiao_valor_pedido_desc_'.$idRegiao));

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

        switch ($origem) {

            case 'configGeral':

                Configuration::updateValue('FKCORREIOSG2CP1_EXCLUIR_CONFIG', Trim(Tools::getValue('fkcorreiosg2cp1_excluir_config')));
                break;

            case 'cadTransp':

                // Recupera id da transportadora
                $id = Tools::getValue('idTransp');

                // Altera cadastro de transportadoras
                $dados = array(
                    'nome_transp'   => Tools::getValue('fkcorreiosg2cp1_transp_nome_'.$id),
                    'grade'         => Tools::getValue('fkcorreiosg2cp1_transp_grade_'.$id),
                    'ativo'         => (!Tools::getValue('fkcorreiosg2cp1_transp_ativo_'.$id) ? '0' : '1')
                );

                Db::getInstance()->update('fkcorreiosg2cp1_transportadoras', $dados, 'id = '.(int)$id);

                // Instancia FKcorreiosg2Class
                $fkClass = new FKcorreiosg2Class();

                // Recupera dados da Transportadora
                $sql = "SELECT nome_transp, id_carrier, ativo, grade
                        FROM "._DB_PREFIX_."fkcorreiosg2cp1_transportadoras
                        WHERE id = ".$id;

                $transp = Db::getInstance()->getRow($sql);

                // Altera o Carrier
                $parm = array(
                    'nomeCarrier'   => $transp['nome_transp'],
                    'idCarrier'     => $transp['id_carrier'],
                    'ativo'         => $transp['ativo'],
                    'grade'         => $transp['grade'],
                    'arrayLogo'     => $_FILES,
                    'campoLogo'     => 'fkcorreiosg2cp1_transp_logo_'.$id,
                );


                if (!$fkClass->alteraCarrier($parm)) {
                    $this->postErrors[] = $fkClass->getMsgErro();
                }

                break;

            case 'cadRegras':

                // Recupera id da regra de preco
                $id = Tools::getValue('idRegra');

                // Recupera valor conforme Incidencia
                if (trim(Tools::getValue('fkcorreiosg2cp1_regra_tipo_'.$id)) == _TIPO_VALOR_FIXO_) {
                    $formulaRegra = _FORMULA_POR_INTERVALO_;
                    $formulaRegraValor = 0;
                    $formulaRegraValorMinimo = 0;
                }else {
                    $formulaRegra = Tools::getValue('fkcorreiosg2cp1_regra_formula_'.$id);
                    $formulaRegraValor = str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regra_formula_valor_'.$id));
                    $formulaRegraValorMinimo = str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regra_formula_valor_minimo_'.$id));
                }

                // Altera cadastro de regras de precos
                $dados = array(
                    'nome_regra'                    => Tools::getValue('fkcorreiosg2cp1_regra_nome_'.$id),
                    'tipo_regra'                    => Tools::getValue('fkcorreiosg2cp1_regra_tipo_'.$id),
                    'tipo_regra_valor'              => str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regra_tipo_valor_'.$id)),
                    'formula_regra'                 => $formulaRegra,
                    'formula_regra_valor'           => $formulaRegraValor,
                    'formula_regra_valor_minimo'    => $formulaRegraValorMinimo,
                    'ativo'                         => (!Tools::getValue('fkcorreiosg2cp1_regra_ativo_'.$id) ? '0' : '1')
                );

                Db::getInstance()->update('fkcorreiosg2cp1_regras_precos', $dados, 'id = '.(int)$id);

                break;

            case 'cadRegioes':

                // Recupera id da regra de preco
                $id = Tools::getValue('idRegiao');

                // Instancia FKcorreiosg2Class
                $fkClass = new FKcorreiosg2Class();

                // Formata UFs
                $regiaoUF = $fkClass->formataGravacaoUF(Tools::getValue('fkcorreiosg2cp1_regiao_uf_'.$id));

                // Altera fkcorreiosg2_frete_gratis
                $dados = array(
                    'nome_regiao'           => Tools::getValue('fkcorreiosg2cp1_regiao_nome_'.$id),
                    'prazo_entrega'         => Tools::getValue('fkcorreiosg2cp1_regiao_prazo_entrega_'.$id),
                    'peso_maximo_produto'   => str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regiao_peso_maximo_produto_'.$id)),
                    'filtro_regiao_uf'      => Tools::getValue('fkcorreiosg2cp1_regiao_filtro_uf_'.$id),
                    'regiao_uf'             => $regiaoUF,
                    'regiao_cep'            => Tools::getValue('fkcorreiosg2cp1_regiao_cep_'.$id),
                    'regiao_cep_excluido'   => Tools::getValue('fkcorreiosg2cp1_regiao_cep_excluido_'.$id),
                    'tipo_tabela'           => Tools::getValue('fkcorreiosg2cp1_regiao_tipo_tabela_'.$id),
                    'tabela_preco'          => str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regiao_tabela_preco_'.$id)),
                    'fator_cubagem'         => str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regiao_fator_cubagem_'.$id)),
                    'valor_adicional_kilo'  => str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regiao_kilo_adicional_'.$id)),
                    'valor_adicional_fixo'  => str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regiao_valor_adicional_'.$id)),
                    'frete_minimo'          => str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regiao_frete_minimo_'.$id)),
                    'percentual_desconto'   => str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regiao_percentual_desc_'.$id)),
                    'valor_pedido_desconto' => str_replace(',', '.', Tools::getValue('fkcorreiosg2cp1_regiao_valor_pedido_desc_'.$id)),
                    'ativo'                 => (!Tools::getValue('fkcorreiosg2cp1_regiao_ativo_'.$id) ? '0' : '1'),
                );

                Db::getInstance()->update('fkcorreiosg2cp1_regioes', $dados, 'id = '.(int)$id);

                break;
        }
    }

    public function getOrderShippingCost($params, $shipping_cost) {

        // Instacia FKcorreiosg2cp1FreteClass
        $freteClass = new FKcorreiosg2cp1FreteClass();

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
                FROM "._DB_PREFIX_."fkcorreiosg2cp1_transportadoras
                WHERE id_shop = ".$this->context->shop->id."
                ORDER BY nome_transp";

        return Db::getInstance()->ExecuteS($sql);
    }

    private function recuperaRegrasPrecos() {

        $sql = "SELECT *
                FROM "._DB_PREFIX_."fkcorreiosg2cp1_regras_precos
                WHERE id_shop = ".$this->context->shop->id."
                ORDER BY nome_regra";

        return Db::getInstance()->ExecuteS($sql);
    }

    private function recuperaRegioes() {

        $sql = "SELECT *
                FROM "._DB_PREFIX_."fkcorreiosg2cp1_regioes
                WHERE id_shop = ".$this->context->shop->id."
                ORDER BY nome_regiao";

        return Db::getInstance()->ExecuteS($sql);
    }

    private function incluiTransportadora() {

        $nomeTransp = 'Nova Transportadora';

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
            'nome_transp'   => $nomeTransp,
            'grade'         => '0',
            'ativo'         => '0'
        );

        Db::getInstance()->insert('fkcorreiosg2cp1_transportadoras', $dados);
    }

    private function excluiTransportadora($id) {

        // Instacia FKcorreiosClass
        $fkclass = new FKcorreiosg2Class();

        // Recupera dados da Transportadora
        $sql = "SELECT id_carrier
                FROM "._DB_PREFIX_."fkcorreiosg2cp1_transportadoras
                WHERE id = ".$id;

        $transp = Db::getInstance()->getRow($sql);

        // Desinstala Carrier do Prestashop
        $fkclass->excluiCarrier($transp['id_carrier']);

        // Exclui o registro do cadastro de transportadoras
        Db::getInstance()->delete('fkcorreiosg2cp1_transportadoras', 'id = '.(int)$id);

        // Exclui os registro do cadastro de regras de precos
        Db::getInstance()->delete('fkcorreiosg2cp1_regras_precos', 'id_transp = '.(int)$id);

        // Exclui os registro do cadastro de regioes
        Db::getInstance()->delete('fkcorreiosg2cp1_regioes', 'id_transp = '.(int)$id);
    }

    private function incluiRegraPreco($idTransp) {

        // Insere registro na tabela de transportadoras
        $dados = array(
            'id_shop'                       => $this->context->shop->id,
            'id_transp'                     => $idTransp,
            'nome_regra'                    => 'Nova Regra',
            'tipo_regra'                    => _TIPO_VALOR_FIXO_,
            'tipo_regra_valor'              => '0',
            'formula_regra'                 => _FORMULA_POR_INTERVALO_,
            'formula_regra_valor'           => '0',
            'formula_regra_valor_minimo'    => '0',
            'ativo'                         => '0'
        );

        Db::getInstance()->insert('fkcorreiosg2cp1_regras_precos', $dados);

    }

    private function excluiRegraPreco($id) {
        // Exclui o registro do cadastro de regras de precos
        Db::getInstance()->delete('fkcorreiosg2cp1_regras_precos', 'id = '.(int)$id);
    }

    private function incluiRegiao($idTransp) {

        // Insere registro na tabela de transportadoras
        $dados = array(
            'id_shop'               => $this->context->shop->id,
            'id_transp'             => $idTransp,
            'nome_regiao'           => 'Nova Região',
            'peso_maximo_produto'   => '0',
            'filtro_regiao_uf'      => '1',
            'tipo_tabela'           => '1',
            'fator_cubagem'         => '0',
            'valor_adicional_kilo'  => '0',
            'valor_adicional_fixo'  => '0',
            'frete_minimo'          => '0',
            'percentual_desconto'   => '0',
            'valor_pedido_desconto' => '0',
            'ativo'                 => '0'
        );

        Db::getInstance()->insert('fkcorreiosg2cp1_regioes', $dados);

    }

    private function excluiRegiao($id) {
        // Exclui o registro do cadastro de regioes
        Db::getInstance()->delete('fkcorreiosg2cp1_regioes', 'id = '.(int)$id);
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

    private function atualizaVersaoModulo() {

        /* TODO: utilizar quando houver alterações de versao que necessitem alterar tabelas ou configuracoes
        if (version_compare($this->version, '160.0.0', '==')) {

        }
        */

        return true;
    }

    private function criaTabelas() {

        $db = Db::getInstance();

        // Cria tabela com o cadastro das transportadoras
        $sql = 'CREATE TABLE IF NOT EXISTS `' ._DB_PREFIX_. 'fkcorreiosg2cp1_transportadoras` (
            	`id` 				int(10) 	    NOT NULL AUTO_INCREMENT,
				`id_shop`			int(10),
            	`id_carrier` 		int(10),
				`nome_transp`       varchar(64),
            	`grade` 			int(10),
            	`ativo` 			tinyint(1),
            	INDEX (`id_carrier`),
            	PRIMARY KEY (`id`)
            	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);

        // Cria tabela com as regras de precos
        $sql = 'CREATE TABLE IF NOT EXISTS `' ._DB_PREFIX_. 'fkcorreiosg2cp1_regras_precos` (
            	`id` 				            int(10) 	    NOT NULL AUTO_INCREMENT,
            	`id_shop`			            int(10),
				`id_transp`			            int(10),
				`nome_regra`                    varchar(100),
				`tipo_regra`		            int(10),
				`tipo_regra_valor`	            decimal(20,2),
				`formula_regra`		            int(10),
				`formula_regra_valor`	        decimal(20,2),
				`formula_regra_valor_minimo`	decimal(20,2),
            	`ativo` 			            tinyint(1),
            	INDEX (`id_transp`),
            	PRIMARY KEY (`id`)
            	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);

        // Cria tabela com as regioes
        $sql = 'CREATE TABLE IF NOT EXISTS `' ._DB_PREFIX_. 'fkcorreiosg2cp1_regioes` (
            	`id` 					int(10) 		NOT NULL AUTO_INCREMENT,
            	`id_shop`			    int(10),
				`id_transp`	            int(10),
				`nome_regiao`  			varchar(100),
				`prazo_entrega`         varchar(250),
				`peso_maximo_produto`	decimal(20,4),
				`filtro_regiao_uf`	    int(10),
				`regiao_uf`				varchar(100),
				`regiao_cep`			text,
				`regiao_cep_excluido`	text,
				`tipo_tabela`	        int(10),
				`tabela_preco`			text,
				`fator_cubagem`		    decimal(20,4),
				`valor_adicional_kilo`	decimal(20,2),
				`valor_adicional_fixo`  decimal(20,2),
				`frete_minimo`          decimal(20,2),
				`percentual_desconto`   decimal(20,2),
            	`valor_pedido_desconto` decimal(20,2),
				`ativo` 			    tinyint(1),
				INDEX (`id_transp`),
            	PRIMARY KEY (`id`)
            	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);

        return true;
    }

    private function excluiTabelas() {

        // Exclui as tabelas
        $sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkcorreiosg2cp1_transportadoras`;";
        Db::getInstance()->execute($sql);

        $sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkcorreiosg2cp1_regras_precos`;";
        Db::getInstance()->execute($sql);

        $sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkcorreiosg2cp1_regioes`;";
        Db::getInstance()->execute($sql);

    }

}