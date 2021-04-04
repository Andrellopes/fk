<?php

include_once(dirname(__FILE__).'/models/FKparcg2Class.php');

class fkparcelamentog2 extends Module
{
    private $html = '';
    private $postErrors = array();
    private $tab_select = '';

    public function __construct()
    {
        $this->name = 'fkparcelamentog2';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->author = 'módulosFK';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Módulo FKparcelamento - Geração 2');
        $this->description = $this->l('Informa aos clientes as opções de parcelamento disponíveis.');

        // Grava cookie da url de funcoes
        setcookie('fkparcg2_url_funcoes', Tools::getShopDomain(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/funcoes.php', 0);
    }

    public function install()
    {
        if (!parent::install()
            Or !$this->registerHook('displayRightColumnProduct')
            Or !$this->registerHook('displayShoppingCartFooter')
            Or !Configuration::updateValue('FKPARCG2_BLOCO_PRODUTO', 'on')
            Or !Configuration::updateValue('FKPARCG2_BLOCO_CARRINHO', 'on')
            Or !Configuration::updateValue('FKPARCG2_COR_FUNDO_SEM_JUROS', '#43B754')
            Or !Configuration::updateValue('FKPARCG2_COR_FONTE_SEM_JUROS', '#FFF')
            Or !Configuration::updateValue('FKPARCG2_LARGURA_CARRINHO', '100%')
            Or !Configuration::updateValue('FKPARCG2_TITULO_1', '')
            Or !Configuration::updateValue('FKPARCG2_PARCELAS_1', '')
            Or !Configuration::updateValue('FKPARCG2_SEM_JUROS_1', '')
            Or !Configuration::updateValue('FKPARCG2_VALOR_MIN_1', '')
            Or !Configuration::updateValue('FKPARCG2_TEXTO_1', '')
            Or !Configuration::updateValue('FKPARCG2_FATORES_1', '')
            Or !Configuration::updateValue('FKPARCG2_JUROS_MES_1', '')
            Or !Configuration::updateValue('FKPARCG2_JUROS_ANO_1', '')
            Or !Configuration::updateValue('FKPARCG2_JUROS_CALCULO_1', 'on')
            Or !Configuration::updateValue('FKPARCG2_ATIVO_2', '')
            Or !Configuration::updateValue('FKPARCG2_TITULO_2', '')
            Or !Configuration::updateValue('FKPARCG2_PARCELAS_2', '')
            Or !Configuration::updateValue('FKPARCG2_SEM_JUROS_2', '')
            Or !Configuration::updateValue('FKPARCG2_VALOR_MIN_2', '')
            Or !Configuration::updateValue('FKPARCG2_TEXTO_2', '')
            Or !Configuration::updateValue('FKPARCG2_FATORES_2', '')
            Or !Configuration::updateValue('FKPARCG2_JUROS_MES_2', '')
            Or !Configuration::updateValue('FKPARCG2_JUROS_ANO_2', '')
            Or !Configuration::updateValue('FKPARCG2_JUROS_CALCULO_2', 'on')
        ) {

            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            Or !$this->unregisterHook('displayRightColumnProduct')
            Or !$this->unregisterHook('displayShoppingCartFooter')
        ) {

            return false;
        }

        // Exclui dados de Configuração
        if (!Db::getInstance()->delete("configuration", "name LIKE 'FKPARCG2_%'")) {
            return false;
        }

        return true;

    }

    public function hookdisplayRightColumnProduct($params)
    {
        // Retorna se nao for para mostrar em produtos
        if (Configuration::get('FKPARCG2_BLOCO_PRODUTO') != 'on') {
            return false;
        }

        // CSS
        $this->context->controller->addCSS($this->_path.'css/fkparcg2_tab.css');
        $this->context->controller->addCSS($this->_path.'css/fkparcg2_front.css');

        // JS
        $this->context->controller->addJS($this->_path.'js/fkparcg2_tab.js');
        $this->context->controller->addJS($this->_path.'js/fkparcg2_cookie.js');
        $this->context->controller->addJS($this->_path.'js/fkparcg2_produto.js');


        // smarty
        $this->smarty->assign(array(
            'fkparcg2_cor_fundo_sem_juros'  => Configuration::get('FKPARCG2_COR_FUNDO_SEM_JUROS'),
            'fkparcg2_cor_fonte_sem_juros'  => Configuration::get('FKPARCG2_COR_FONTE_SEM_JUROS'),
            'fkparcg2_titulo_1'             => Configuration::get('FKPARCG2_TITULO_1'),
            'fkparcg2_sem_juros_1'          => Configuration::get('FKPARCG2_SEM_JUROS_1'),
            'fkparcg2_texto_1'              => Configuration::get('FKPARCG2_TEXTO_1'),
            'fkparcg2_juros_mes_1'          => Configuration::get('FKPARCG2_JUROS_MES_1'),
            'fkparcg2_juros_ano_1'          => Configuration::get('FKPARCG2_JUROS_ANO_1'),
            'fkparcg2_ativo_2'              => Configuration::get('FKPARCG2_ATIVO_2'),
            'fkparcg2_titulo_2'             => Configuration::get('FKPARCG2_TITULO_2'),
            'fkparcg2_sem_juros_2'          => Configuration::get('FKPARCG2_SEM_JUROS_2'),
            'fkparcg2_texto_2'              => Configuration::get('FKPARCG2_TEXTO_2'),
            'fkparcg2_juros_mes_2'          => Configuration::get('FKPARCG2_JUROS_MES_2'),
            'fkparcg2_juros_ano_2'          => Configuration::get('FKPARCG2_JUROS_ANO_2'),
        ));

        return $this->display(__FILE__, 'views/front/simuladorProduto.tpl');
    }

    public function hookdisplayShoppingCartFooter($params)
    {
        // Retorna se nao for para mostrar em produtos
        if (Configuration::get('FKPARCG2_BLOCO_CARRINHO') != 'on') {
            return false;
        }

        // CSS
        $this->context->controller->addCSS($this->_path.'css/fkparcg2_tab.css');
        $this->context->controller->addCSS($this->_path.'css/fkparcg2_front.css');

        // JS
        $this->context->controller->addJS($this->_path.'js/fkparcg2_tab.js');

        // Recupera valor do pedido
        $valorPedido = $params['total_price'];

        // Instancia FKparcg2Class
        $fkparcg2Class = new FKparcg2Class();

        // Constroi array com as parcelas
        $parcelas_1 = $fkparcg2Class->processaParcelamentoCarrinho($valorPedido,'1');
        $totalParcelas_1 = count($parcelas_1);

        if ($totalParcelas_1 > 0) {
            $totalParcelas_1 -= 1;
        }

        $parcelas_2 = $fkparcg2Class->processaParcelamentoCarrinho($valorPedido,'2');
        $totalParcelas_2 = count($parcelas_2);

        if ($totalParcelas_2 > 0) {
            $totalParcelas_2 -= 1;
        }

        $this->smarty->assign(array(
            'fkparcg2_cor_fundo_sem_juros'  => Configuration::get('FKPARCG2_COR_FUNDO_SEM_JUROS'),
            'fkparcg2_cor_fonte_sem_juros'  => Configuration::get('FKPARCG2_COR_FONTE_SEM_JUROS'),
            'fkparcg2_largura_carrinho'     => Configuration::get('FKPARCG2_LARGURA_CARRINHO'),
            'fkparcg2_titulo_1'             => Configuration::get('FKPARCG2_TITULO_1'),
            'fkparcg2_sem_juros_1'          => Configuration::get('FKPARCG2_SEM_JUROS_1'),
            'fkparcg2_parcelas_1'           => $parcelas_1,
            'fkparcg2_total_parcelas_1'     => $totalParcelas_1,
            'fkparcg2_texto_1'              => Configuration::get('FKPARCG2_TEXTO_1'),
            'fkparcg2_juros_mes_1'          => Configuration::get('FKPARCG2_JUROS_MES_1'),
            'fkparcg2_juros_ano_1'          => Configuration::get('FKPARCG2_JUROS_ANO_1'),
            'fkparcg2_ativo_2'              => Configuration::get('FKPARCG2_ATIVO_2'),
            'fkparcg2_titulo_2'             => Configuration::get('FKPARCG2_TITULO_2'),
            'fkparcg2_parcelas_2'           => $parcelas_2,
            'fkparcg2_total_parcelas_2'     => $totalParcelas_2,
            'fkparcg2_sem_juros_2'          => Configuration::get('FKPARCG2_SEM_JUROS_2'),
            'fkparcg2_texto_2'              => Configuration::get('FKPARCG2_TEXTO_2'),
            'fkparcg2_juros_mes_2'          => Configuration::get('FKPARCG2_JUROS_MES_2'),
            'fkparcg2_juros_ano_2'          => Configuration::get('FKPARCG2_JUROS_ANO_2'),
        ));

        return $this->display(__FILE__, 'views/front/simuladorCarrinho.tpl');
    }

    public function getContent()
    {

        if (!empty($_POST)) {

            $this->postValidation();

            if (sizeof($this->postErrors)) {
                foreach ($this->postErrors AS $err) {
                    $this->html .= $this->displayError($err);
                }
            }
        }

        $this->html .= $this->renderForm();

        return $this->html;

    }

    private function renderForm()
    {
        // CSS
        $this->context->controller->addCSS($this->_path.'css/fkparcg2_admin.css');

        // JS
        $this->context->controller->addJS($this->_path.'js/fkparcg2_admin.js');

        // recupera dados da configuracao
        $this->configGeral();
        $this->configParc_1();
        $this->configParc_2();

        $this->smarty->assign(array(
            'pathInclude'   => _PS_MODULE_DIR_.$this->name.'/views/config/',
            'tabSelect'     => $this->tab_select,
            'formAction'    => Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']),
        ));

        return $this->display(__FILE__, 'views/config/mainConfig.tpl');
    }

    private function configGeral()
    {
        // TPL a ser utilizado
        $name_tpl ='configGeral.tpl';

        $this->smarty->assign(array(
            'tab_1' => array(
                'nameTpl'                       => $name_tpl,
                'fkparcg2_bloco_produto'        => Tools::getValue('fkparcg2_bloco_produto', Configuration::get('FKPARCG2_BLOCO_PRODUTO')),
                'fkparcg2_bloco_carrinho'       => Tools::getValue('fkparcg2_bloco_carrinho', Configuration::get('FKPARCG2_BLOCO_CARRINHO')),
                'fkparcg2_cor_fundo_sem_juros'  => Tools::getValue('fkparcg2_cor_fundo_sem_juros', Configuration::get('FKPARCG2_COR_FUNDO_SEM_JUROS')),
                'fkparcg2_cor_fonte_sem_juros'  => Tools::getValue('fkparcg2_cor_ffonte_sem_juros', Configuration::get('FKPARCG2_COR_FONTE_SEM_JUROS')),
                'fkparcg2_largura_carrinho'     => Tools::getValue('fkparcg2_largura_carrinho', Configuration::get('FKPARCG2_LARGURA_CARRINHO')),
            )
        ));
    }

    private function configParc_1()
    {
        // TPL a ser utilizado
        $name_tpl ='configParc_1.tpl';

        // Array dos fatores
        $fatores = explode('|', Configuration::get('FKPARCG2_FATORES_1'));

        $this->smarty->assign(array(
            'tab_2' => array(
                'nameTpl'                   => $name_tpl,
                'fkparcg2_titulo_1'         => Tools::getValue('fkparcg2_titulo_1', Configuration::get('FKPARCG2_TITULO_1')),
                'fkparcg2_parcelas_1'       => Tools::getValue('fkparcg2_parcelas_1', Configuration::get('FKPARCG2_PARCELAS_1')),
                'fkparcg2_sem_juros_1'      => Tools::getValue('fkparcg2_sem_juros_1', Configuration::get('FKPARCG2_SEM_JUROS_1')),
                'fkparcg2_valor_min_1'      => Tools::getValue('fkparcg2_valor_min_1', Configuration::get('FKPARCG2_VALOR_MIN_1')),
                'fkparcg2_texto_1'          => Tools::getValue('fkparcg2_texto_1', Configuration::get('FKPARCG2_TEXTO_1')),
                'fkparcg2_fatores_1'        => $fatores,
                'fkparcg2_juros_mes_1'      => Configuration::get('FKPARCG2_JUROS_MES_1'),
                'fkparcg2_juros_ano_1'      => Configuration::get('FKPARCG2_JUROS_ANO_1'),
                'fkparcg2_juros_calculo_1'  => Tools::getValue('fkparcg2_juros_calculo_1', Configuration::get('FKPARCG2_JUROS_CALCULO_1')),
            )
        ));
    }

    private function configParc_2()
    {
        // TPL a ser utilizado
        $name_tpl ='configParc_2.tpl';

        // Array dos fatores
        $fatores = explode('|', Configuration::get('FKPARCG2_FATORES_2'));

        $this->smarty->assign(array(
            'tab_3' => array(
                'nameTpl'                   => $name_tpl,
                'fkparcg2_ativo_2'          => Tools::getValue('fkparcg2_ativo_2', Configuration::get('FKPARCG2_ATIVO_2')),
                'fkparcg2_titulo_2'         => Tools::getValue('fkparcg2_titulo_2', Configuration::get('FKPARCG2_TITULO_2')),
                'fkparcg2_parcelas_2'       => Tools::getValue('fkparcg2_parcelas_2', Configuration::get('FKPARCG2_PARCELAS_2')),
                'fkparcg2_sem_juros_2'      => Tools::getValue('fkparcg2_sem_juros_2', Configuration::get('FKPARCG2_SEM_JUROS_2')),
                'fkparcg2_valor_min_2'      => Tools::getValue('fkparcg2_valor_min_2', Configuration::get('FKPARCG2_VALOR_MIN_2')),
                'fkparcg2_texto_2'          => Tools::getValue('fkparcg2_texto_2', Configuration::get('FKPARCG2_TEXTO_2')),
                'fkparcg2_fatores_2'        => $fatores,
                'fkparcg2_juros_mes_2'      => Configuration::get('FKPARCG2_JUROS_MES_2'),
                'fkparcg2_juros_ano_2'      => Configuration::get('FKPARCG2_JUROS_ANO_2'),
                'fkparcg2_juros_calculo_2'  => Tools::getValue('fkparcg2_juros_calculo_2', Configuration::get('FKPARCG2_JUROS_CALCULO_2')),
            )
        ));
    }

    private function postValidation()
    {
        $origem = Tools::getValue('origem');

        switch ($origem) {

            case 'configParc_1':

                // Posicionamento da tab
                $this->tab_select = '2';

                if (Trim(Tools::getValue('fkparcg2_titulo_1')) == '') {
                    $this->postErrors[] = $this->l('O campo "Título da aba" é obrigatório.');
                }

                if (Trim(Tools::getValue('fkparcg2_parcelas_1')) == '') {
                    $this->postErrors[] = $this->l('Campo "Total de parcelas" não preenchido');
                } else {
                    if (!is_numeric(Tools::getValue('fkparcg2_parcelas_1'))) {
                        $this->postErrors[] = $this->l('O campo "Total de parcelas" não é numérico');
                    } else {
                        if (Tools::getValue('fkparcg2_parcelas_1') < 0) {
                            $this->postErrors[] = $this->l('O campo "Total de parcelas" não pode ser menor que 0 (zero)');
                        }
                    }
                }

                if (Trim(Tools::getValue('fkparcg2_sem_juros_1')) == '') {
                    $this->postErrors[] = $this->l('Campo "Parcelas sem juros" não preenchido');
                } else {
                    if (!is_numeric(Tools::getValue('fkparcg2_sem_juros_1'))) {
                        $this->postErrors[] = $this->l('O campo "Parcelas sem juros" não é numérico');
                    } else {
                        if (Tools::getValue('fkparcg2_sem_juros_1') < 0) {
                            $this->postErrors[] = $this->l('O campo "Parcelas sem juros" não pode ser menor que 0 (zero)');
                        }
                    }
                }

                if (Trim(Tools::getValue('fkparcg2_valor_min_1')) == '') {
                    $this->postErrors[] = $this->l('Campo "Valor mínimo da parcela" não preenchido');
                } else {
                    if (!is_numeric(Tools::getValue('fkparcg2_valor_min_1'))) {
                        $this->postErrors[] = $this->l('O campo "Valor mínimo da parcela" não é numérico');
                    } else {
                        if (Tools::getValue('fkparcg2_valor_min_1') < 0) {
                            $this->postErrors[] = $this->l('O campo "Valor mínimo da parcela" não pode ser menor que 0 (zero)');
                        }
                    }
                }

                if (is_numeric(Tools::getValue('fkparcg2_parcelas_1'))) {
                    
                    $totalParcelas = Tools::getValue('fkparcg2_parcelas_1');

                    for ($i = 1; $i <= $totalParcelas; $i++) {

                        if (Trim(Tools::getValue('fkparcg2_fator_1_'.$i)) == '') {
                            $this->postErrors[] = 'Campo "Fator da parcela '.$i.'" não preenchido';
                        } else {
                            $valor = str_replace(',','.', Tools::getValue('fkparcg2_fator_1_'.$i));
                            if (!is_numeric($valor)) {
                                $this->postErrors[] = $this->l('O campo "Fator da parcela '.$i.'" não é numérico');
                            } else {
                                if ($valor <= 0) {
                                    $this->postErrors[] = $this->l('O campo "Fator da parcela '.$i.'" não pode ser menor ou igual a 0 (zero)');
                                }
                            }
                        }
                    }
                }

                if (Tools::getValue('fkparcg2_juros_calculo_1') != 'on') {

                    if (Trim(Tools::getValue('fkparcg2_juros_mes_1')) == '') {
                        $this->postErrors[] = $this->l('Campo "Juros mensais" não preenchido');
                    } else {
                        $valor = str_replace(',','.', Tools::getValue('fkparcg2_juros_mes_1'));
                        if (!is_numeric($valor)) {
                            $this->postErrors[] = $this->l('O campo "Juros mensais" não é numérico');
                        } else {
                            if ($valor < 0) {
                                $this->postErrors[] = $this->l('O campo "Juros mensais" não pode ser menor que 0 (zero)');
                            }
                        }
                    }

                    if (Trim(Tools::getValue('fkparcg2_juros_ano_1')) == '') {
                        $this->postErrors[] = $this->l('Campo "Juros anuais" não preenchido');
                    } else {
                        $valor = str_replace(',','.', Tools::getValue('fkparcg2_juros_ano_1'));
                        if (!is_numeric($valor)) {
                            $this->postErrors[] = $this->l('O campo "Juros anuais" não é numérico');
                        } else {
                            if ($valor < 0) {
                                $this->postErrors[] = $this->l('O campo "Juros anuais" não pode ser menor que 0 (zero)');
                            }
                        }
                    }
                }

                break;

            case 'configParc_2':

                // Posicionamento da tab
                $this->tab_select = '3';
                
                if (Trim(Tools::getValue('fkparcg2_ativo_2')) == '') {
                    break;
                }

                if (Trim(Tools::getValue('fkparcg2_titulo_2')) == '') {
                    $this->postErrors[] = $this->l('O campo "Título da aba" é obrigatório.');
                }

                if (Trim(Tools::getValue('fkparcg2_parcelas_2')) == '') {
                    $this->postErrors[] = $this->l('Campo "Total de parcelas" não preenchido');
                } else {
                    if (!is_numeric(Tools::getValue('fkparcg2_parcelas_2'))) {
                        $this->postErrors[] = $this->l('O campo "Total de parcelas" não é numérico');
                    } else {
                        if (Tools::getValue('fkparcg2_parcelas_2') < 0) {
                            $this->postErrors[] = $this->l('O campo "Total de parcelas" não pode ser menor que 0 (zero)');
                        }
                    }
                }

                if (Trim(Tools::getValue('fkparcg2_sem_juros_2')) == '') {
                    $this->postErrors[] = $this->l('Campo "Parcelas sem juros" não preenchido');
                } else {
                    if (!is_numeric(Tools::getValue('fkparcg2_sem_juros_2'))) {
                        $this->postErrors[] = $this->l('O campo "Parcelas sem juros" não é numérico');
                    } else {
                        if (Tools::getValue('fkparcg2_sem_juros_2') < 0) {
                            $this->postErrors[] = $this->l('O campo "Parcelas sem juros" não pode ser menor que 0 (zero)');
                        }
                    }
                }

                if (Trim(Tools::getValue('fkparcg2_valor_min_2')) == '') {
                    $this->postErrors[] = $this->l('Campo "Valor mínimo da parcela" não preenchido');
                } else {
                    if (!is_numeric(Tools::getValue('fkparcg2_valor_min_2'))) {
                        $this->postErrors[] = $this->l('O campo "Valor mínimo da parcela" não é numérico');
                    } else {
                        if (Tools::getValue('fkparcg2_valor_min_2') < 0) {
                            $this->postErrors[] = $this->l('O campo "Valor mínimo da parcela" não pode ser menor que 0 (zero)');
                        }
                    }
                }

                if (is_numeric(Tools::getValue('fkparcg2_parcelas_2'))) {

                    $totalParcelas = Tools::getValue('fkparcg2_parcelas_2');

                    for ($i = 1; $i <= $totalParcelas; $i++) {

                        if (Trim(Tools::getValue('fkparcg2_fator_2_'.$i)) == '') {
                            $this->postErrors[] = 'Campo "Fator da parcela '.$i.'" não preenchido';
                        } else {
                            $valor = str_replace(',','.', Tools::getValue('fkparcg2_fator_2_'.$i));
                            if (!is_numeric($valor)) {
                                $this->postErrors[] = $this->l('O campo "Fator da parcela '.$i.'" não é numérico');
                            } else {
                                if ($valor <= 0) {
                                    $this->postErrors[] = $this->l('O campo "Fator da parcela '.$i.'" não pode ser menor ou igual a 0 (zero)');
                                }
                            }
                        }
                    }
                }

                if (Tools::getValue('fkparcg2_juros_calculo_2') != 'on') {

                    if (Trim(Tools::getValue('fkparcg2_juros_mes_2')) == '') {
                        $this->postErrors[] = $this->l('Campo "Juros mensais" não preenchido');
                    } else {
                        $valor = str_replace(',','.', Tools::getValue('fkparcg2_juros_mes_2'));
                        if (!is_numeric($valor)) {
                            $this->postErrors[] = $this->l('O campo "Juros mensais" não é numérico');
                        } else {
                            if ($valor < 0) {
                                $this->postErrors[] = $this->l('O campo "Juros mensais" não pode ser menor que 0 (zero)');
                            }
                        }
                    }

                    if (Trim(Tools::getValue('fkparcg2_juros_ano_2')) == '') {
                        $this->postErrors[] = $this->l('Campo "Juros anuais" não preenchido');
                    } else {
                        $valor = str_replace(',','.', Tools::getValue('fkparcg2_juros_ano_2'));
                        if (!is_numeric($valor)) {
                            $this->postErrors[] = $this->l('O campo "Juros anuais" não é numérico');
                        } else {
                            if ($valor < 0) {
                                $this->postErrors[] = $this->l('O campo "Juros anuais" não pode ser menor que 0 (zero)');
                            }
                        }
                    }
                }

                break;

        }

        if (!$this->postErrors) {
            $this->postProcess($origem);
        }

    }

    private function postProcess($origem)
    {
        switch ($origem) {

            case 'configGeral':
                Configuration::updateValue('FKPARCG2_BLOCO_PRODUTO', Trim(Tools::getValue('fkparcg2_bloco_produto')));
                Configuration::updateValue('FKPARCG2_BLOCO_CARRINHO', Trim(Tools::getValue('fkparcg2_bloco_carrinho')));
                Configuration::updateValue('FKPARCG2_COR_FUNDO_SEM_JUROS', Trim(Tools::getValue('fkparcg2_cor_fundo_sem_juros')));
                Configuration::updateValue('FKPARCG2_COR_FONTE_SEM_JUROS', Trim(Tools::getValue('fkparcg2_cor_fonte_sem_juros')));
                Configuration::updateValue('FKPARCG2_LARGURA_CARRINHO', Trim(Tools::getValue('fkparcg2_largura_carrinho')));

                break;

            case 'configParc_1':
                Configuration::updateValue('FKPARCG2_TITULO_1', Trim(Tools::getValue('fkparcg2_titulo_1')));
                Configuration::updateValue('FKPARCG2_PARCELAS_1', Trim(Tools::getValue('fkparcg2_parcelas_1')));
                Configuration::updateValue('FKPARCG2_SEM_JUROS_1', Trim(Tools::getValue('fkparcg2_sem_juros_1')));
                Configuration::updateValue('FKPARCG2_VALOR_MIN_1', Trim(Tools::getValue('fkparcg2_valor_min_1')));
                Configuration::updateValue('FKPARCG2_TEXTO_1', Trim(Tools::getValue('fkparcg2_texto_1')));
                Configuration::updateValue('FKPARCG2_FATORES_1', $this->formataFatores('1'));

                if (Tools::getValue('fkparcg2_juros_calculo_1') == 'on') {
                    $jurosMes = $this->calcJurosMes('1');
                    $jurosAno = $this->calcJurosAno('1', $jurosMes);

                    Configuration::updateValue('FKPARCG2_JUROS_MES_1', $jurosMes);
                    Configuration::updateValue('FKPARCG2_JUROS_ANO_1', $jurosAno);
                } else {
                    Configuration::updateValue('FKPARCG2_JUROS_MES_1', Trim(Tools::getValue('fkparcg2_juros_mes_1')));
                    Configuration::updateValue('FKPARCG2_JUROS_ANO_1', Trim(Tools::getValue('fkparcg2_juros_ano_1')));
                }

                Configuration::updateValue('FKPARCG2_JUROS_CALCULO_1', Trim(Tools::getValue('fkparcg2_juros_calculo_1')));

                break;

            case 'configParc_2':
                Configuration::updateValue('FKPARCG2_ATIVO_2', Trim(Tools::getValue('fkparcg2_ativo_2')));
                Configuration::updateValue('FKPARCG2_TITULO_2', Trim(Tools::getValue('fkparcg2_titulo_2')));
                Configuration::updateValue('FKPARCG2_PARCELAS_2', Trim(Tools::getValue('fkparcg2_parcelas_2')));
                Configuration::updateValue('FKPARCG2_SEM_JUROS_2', Trim(Tools::getValue('fkparcg2_sem_juros_2')));
                Configuration::updateValue('FKPARCG2_VALOR_MIN_2', Trim(Tools::getValue('fkparcg2_valor_min_2')));
                Configuration::updateValue('FKPARCG2_TEXTO_2', Trim(Tools::getValue('fkparcg2_texto_2')));
                Configuration::updateValue('FKPARCG2_FATORES_2', $this->formataFatores('2'));

                if (Tools::getValue('fkparcg2_juros_calculo_2') == 'on') {
                    $jurosMes = $this->calcJurosMes('2');
                    $jurosAno = $this->calcJurosAno('2', $jurosMes);

                    Configuration::updateValue('FKPARCG2_JUROS_MES_2', $jurosMes);
                    Configuration::updateValue('FKPARCG2_JUROS_ANO_2', $jurosAno);
                } else {
                    Configuration::updateValue('FKPARCG2_JUROS_MES_2', Trim(Tools::getValue('fkparcg2_juros_mes_2')));
                    Configuration::updateValue('FKPARCG2_JUROS_ANO_2', Trim(Tools::getValue('fkparcg2_juros_ano_2')));
                }

                Configuration::updateValue('FKPARCG2_JUROS_CALCULO_2', Trim(Tools::getValue('fkparcg2_juros_calculo_2')));

                break;

        }
    }

    private function formataFatores($idFator)
    {
        $fatores = '';
        $totalParcelas = Configuration::get('FKPARCG2_PARCELAS_'.$idFator);

        for ($i = 1; $i <= $totalParcelas; $i++) {

            $fator = Trim(Tools::getValue('fkparcg2_fator_'.$idFator.'_'.$i));
            $fator = str_replace(',', '.', $fator);

            if ($i != 1) {
                $fatores .= '|';
            }
            $fatores .= $fator;
        }

        return $fatores;
    }

    private function calcJurosMes($idParcelamento) {

        if (Configuration::get('FKPARCG2_PARCELAS_'.$idParcelamento) == Configuration::get('FKPARCG2_SEM_JUROS_'.$idParcelamento)) {
            return 0;
        }

        // Recupera total de parcelas
        $totalParcelas = Configuration::get('FKPARCG2_PARCELAS_'.$idParcelamento);

        // Recupera o ultimo fator
        $fatores = Configuration::get('FKPARCG2_FATORES_'.$idParcelamento);
        $pos = strrpos($fatores, '|');

        if ($pos === false) {
            return 0;
        }

        $fator = substr($fatores, $pos+1);

        // Calcula
        $capital = 100;
        $valorParcela = $capital * $fator;

        $valorParcelaTmp = 0;
        $jurosTmp = 0;
        $jurosRetorno = 0;

        for ($i = 0.01; $valorParcelaTmp <= $valorParcela; $i += 0.01) {

            $jurosRetorno = $jurosTmp;
            $jurosTmp = $i / 100;

            $result = $jurosTmp * pow(1 + $jurosTmp, (int)$totalParcelas);
            $result_1 = pow(1 + $jurosTmp, (int)$totalParcelas) - 1;
            $result_2 = $result / $result_1;

            $valorParcelaTmp = $capital * $result_2;

        }

        return number_format($jurosRetorno * 100, 2, ',', '.');
    }

    private function calcJurosAno($idParcelamento, $jurosMes) {

        if (Configuration::get('FKPARCG2_PARCELAS_'.$idParcelamento) == Configuration::get('FKPARCG2_SEM_JUROS_'.$idParcelamento)) {
            return 0;
        }

        $result = 1 + ($jurosMes / 100);
        $result_1 = pow($result, 12);
        $jurosRetorno = ($result_1 - 1) * 100;

        return number_format($jurosRetorno, 2, ',', '.');
    }

}