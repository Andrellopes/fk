<?php

include_once 'models/MessengerClass.php';
include_once 'defines/defines.php';

class fkmessenger extends Module {
    
    private $_html = '';
    private $_postErrors = array();
    private $_tab_select = '';
    
    public function __construct() {
        
        $this->name = 'fkmessenger';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'módulosFK';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('FKmessenger');
        $this->description = $this->l('Permite o envio de mensagens via lightBox.');
        
    }
    
    public function install() {

        if (!parent::install()
            Or !$this->criaTabelas()
            Or !$this->registerHook('displayHeader')
            Or !$this->registerHook('displayHome')
            Or !$this->registerHook('displayFooterProduct')
            Or !$this->registerHook('displayShoppingCartFooter')) {
            return false;
        }

        return true;
    }

    public function uninstall() {

        if (!parent::uninstall()
            Or !$this->excluiTabelas()
            Or !$this->unregisterHook('displayHeader')
            Or !$this->unregisterHook('displayHome')
            Or !$this->unregisterHook('displayFooterProduct')
            Or !$this->unregisterHook('displayShoppingCartFooter')) {
            return false;
        }

        // Exclui dados de Configuração
        if (!Db::getInstance()->delete("configuration", "name LIKE 'FKMESSENGER_%'")) {
            return false;
        }

        return true;
    }
    
    public function getContent() {

        if (Tools::isSubmit('btnSubmit') or Tools::isSubmit('btnAdd') or Tools::isSubmit('btnDelete')) {

            $this->postValidation();

            if (!sizeof($this ->_postErrors)) {
                if (Tools::isSubmit('btnSubmit')) {
                    $this->_html .= $this->displayConfirmation($this->l('Configuração alterada'));
                }
            }else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        }

        $this->_html .= $this->renderForm();

        return $this->_html;
    }

    public function renderForm() {

        // CSS
        $this->context->controller->addCSS($this->_path.'css/fkmessenger_admin.css');

        // JS
        $this->context->controller->addJS($this->_path.'js/fkmessenger_admin.js');
        $this->context->controller->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');

        if (version_compare(_PS_VERSION_, '1.6.0.11', '<=')) {
            $this->context->controller->addJS(_PS_JS_DIR_.'tinymce.inc.js');
        }else {
            $this->context->controller->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
        }

        $this->context->controller->addJS($this->_path.'js/fkmessenger_tinymce.js');

        $this->configHome();
        $this->configProduto();
        $this->configCarrinho();

        $this->smarty->assign(array(
            'pathInclude'   => _PS_MODULE_DIR_.$this->name.'/views/config/',
            'path_adm'      => dirname($_SERVER['PHP_SELF']),
            'path_css'      => _THEME_CSS_DIR_,
            'language'      => 'pt',
            'tabSelect'     => $this->_tab_select,
        ));

        return $this->display(__FILE__, 'views/config/mainConfig.tpl');

    }

    private function configHome() {

        // TPL a ser utilizado
        $name_tpl ='configHome.tpl';

        // Recupera dados
        $messageClass = new MessengerClass();
        $mensagens = $messageClass->recuperaMensagem_tudo(_origemHome_, $this->context->shop->id);

        $this->smarty->assign(array(
            'tab_2' => array(
                'nameTpl'       => $name_tpl,
                'formAction'    => Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']),
                'mensagens'     => $mensagens,
            )

        ));
    }

    private function configProduto() {

        // TPL a ser utilizado
        $name_tpl ='configProduto.tpl';

        // Recupera dados
        $messageClass = new MessengerClass();
        $mensagens = $messageClass->recuperaMensagem_tudo(_origemProduto_, $this->context->shop->id);

        $this->smarty->assign(array(
            'tab_3' => array(
                'nameTpl'       => $name_tpl,
                'formAction'    => Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']),
                'mensagens'     => $mensagens,
            )

        ));

    }

    private function configCarrinho() {

        // TPL a ser utilizado
        $name_tpl ='configCarrinho.tpl';

        // Recupera dados
        $messageClass = new MessengerClass();
        $mensagens = $messageClass->recuperaMensagem_tudo(_origemCarrinho_, $this->context->shop->id);

        $this->smarty->assign(array(
            'tab_4' => array(
                'nameTpl'       => $name_tpl,
                'formAction'    => Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']),
                'mensagens'     => $mensagens,
            )

        ));

    }
    
    private function postValidation() {

        $origem = Tools::getValue('origem');

        switch($origem) {

             case 'configHome':
             
                // Posicionamento da tab
                $this->_tab_select = '2';
             
                // Altura lightbox
                if (Tools::getValue('fkmessenger_home_altura') == NULL) {
                    $this->_postErrors[] = $this->l('Altura do LightBox não preenchido');
                }else {
                    $valor = str_replace(',', '.', Tools::getValue('fkmessenger_home_altura'));
            
                    if (!is_numeric($valor)) {
                        $this->_postErrors[] = $this->l('O campo "Altura do LightBox" não é numérico');
                    }else {
                        if ($valor < 0) {
                            $this->_postErrors[] = $this->l('O campo "Altura do LightBox" não pode ser menor que 0 (zero)');
                        }
                    }
                }
                
                // Largura lightbox
                if (Tools::getValue('fkmessenger_home_largura') == NULL) {
                    $this->_postErrors[] = $this->l('Largura do LightBox não preenchido');
                }else {
                    $valor = str_replace(',', '.', Tools::getValue('fkmessenger_home_largura'));
            
                    if (!is_numeric($valor)) {
                        $this->_postErrors[] = $this->l('O campo "Largura do LightBox" não é numérico');
                    }else {
                        if ($valor < 0) {
                            $this->_postErrors[] = $this->l('O campo "Largura do LightBox" não pode ser menor que 0 (zero)');
                        }
                    }
                }
                
                // Botao 1
                if (Tools::getValue('fkmessenger_home_link1') != NULL and Tools::getValue('fkmessenger_home_botao1') == NULL) {
                    $this->_postErrors[] = $this->l('Identificação do Botão 1 não preenchido');    
                }
                
                if (Tools::getValue('fkmessenger_home_link1') != NULL and Tools::getValue('fkmessenger_home_cor1') == NULL) {
                    $this->_postErrors[] = $this->l('Cor do Botão 1 não selecionada');    
                }
                
                // Botao 2
                if (Tools::getValue('fkmessenger_home_link2') != NULL and Tools::getValue('fkmessenger_home_botao2') == NULL) {
                    $this->_postErrors[] = $this->l('Identificação do Botão 2 não preenchido');    
                }
                
                if (Tools::getValue('fkmessenger_home_link2') != NULL and Tools::getValue('fkmessenger_home_cor2') == NULL) {
                    $this->_postErrors[] = $this->l('Cor do Botão 2 não selecionada');    
                }
                
                // Mensagem
                if (Tools::getValue('fkmessenger_home_mensagem') == NULL) {
                    $this->_postErrors[] = $this->l('Mensagem não preenchida');    
                }
                
                if (!$this->_postErrors) {
                    $this->postProcess($origem);
                }

                break;
                
            case 'configProduto':
            
                // Posicionamento da tab
                $this->_tab_select = '3';
                
                if (Tools::isSubmit('btnAdd') or Tools::isSubmit('btnDelete')) {
                    $this->postProcess($origem);
                    break;    
                }
                
                // Recupera ID da mensagem
                $id_msg = Tools::getValue('id');
                
                // Verifica se ja existem mensagens para "Mostrar sempre" e "Mostrar sempre na abertura da sessão" pois so pode existir uma
                $messageClass = new MessengerClass();
                $mensagens = $messageClass->recuperaMensagem_ativo(_origemProduto_, $this->context->shop->id);
                
                if (Tools::getValue('fkmessenger_produto_freq_'.$id_msg) == _prodSempre_ or Tools::getValue('fkmessenger_produto_freq_'.$id_msg) == _prodSempreAbertura_) {
                    foreach ($mensagens as $mensagem) {
                        if ($mensagem['id'] != $id_msg and ($mensagem['frequencia'] == _prodSempre_ or $mensagem['frequencia'] == _prodSempreAbertura_)) {
                            $this->_postErrors[] = $this->l('Já existe mensagem com a Frequência "Mostrar sempre" ou "Mostrar sempre na abertura da sessão" cadastrada e só pode existir uma mensagem desse tipo cadastrada');
                            break;
                        }
                    }
                }
                
                // Identificacao da Mensagem
                if (Tools::getValue('fkmessenger_produto_nome_msg_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Identificação da mensagem não preenchida');    
                }
                
                // Filtro
                if ((Tools::getValue('fkmessenger_produto_freq_'.$id_msg) == (int)_prodSempreFiltro_ or Tools::getValue('fkmessenger_produto_freq_'.$id_msg) == (int)_prodUnicoFiltro_) and  Tools::getValue('fkmessenger_produto_filtro_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Não preenchido os IDs das Categorias ou Produtos no campo Filtro');    
                }
                
                // Altura lightbox
                if (Tools::getValue('fkmessenger_produto_altura_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Altura do LightBox não preenchido');
                }else {
                    $valor = str_replace(',', '.', Tools::getValue('fkmessenger_produto_altura_'.$id_msg));
            
                    if (!is_numeric($valor)) {
                        $this->_postErrors[] = $this->l('O campo "Altura do LightBox" não é numérico');
                    }else {
                        if ($valor < 0) {
                            $this->_postErrors[] = $this->l('O campo "Altura do LightBox" não pode ser menor que 0 (zero)');
                        }
                    }
                }
                
                // Largura lightbox
                if (Tools::getValue('fkmessenger_produto_largura_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Largura do LightBox não preenchido');
                }else {
                    $valor = str_replace(',', '.', Tools::getValue('fkmessenger_produto_largura_'.$id_msg));
            
                    if (!is_numeric($valor)) {
                        $this->_postErrors[] = $this->l('O campo "Largura do LightBox" não é numérico');
                    }else {
                        if ($valor < 0) {
                            $this->_postErrors[] = $this->l('O campo "Largura do LightBox" não pode ser menor que 0 (zero)');
                        }
                    }
                }
                
                // Botao 1
                if (Tools::getValue('fkmessenger_produto_link1_'.$id_msg) != NULL and Tools::getValue('fkmessenger_produto_botao1_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Identificação do Botão 1 não preenchido');    
                }
                
                if (Tools::getValue('fkmessenger_produto_link1_'.$id_msg) != NULL and Tools::getValue('fkmessenger_produto_cor1_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Cor do Botão 1 não selecionada');    
                }
                
                // Botao 2
                if (Tools::getValue('fkmessenger_produto_link2_'.$id_msg) != NULL and Tools::getValue('fkmessenger_produto_botao2_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Identificação do Botão 2 não preenchido');    
                }
                
                if (Tools::getValue('fkmessenger_produto_link2_'.$id_msg) != NULL and Tools::getValue('fkmessenger_produto_cor2_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Cor do Botão 2 não selecionada');    
                }
                
                // Mensagem
                if (Tools::getValue('fkmessenger_produto_mensagem_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Mensagem não preenchida');    
                }
                
                if (!$this->_postErrors) {
                    $this->postProcess($origem);
                }
            
                break;

            case 'configCarrinho':

                // Posicionamento da tab
                $this->_tab_select = '4';

                if (Tools::isSubmit('btnAdd') or Tools::isSubmit('btnDelete')) {
                    $this->postProcess($origem);
                    break;
                }

                // Recupera ID da mensagem
                $id_msg = Tools::getValue('id');

                // Verifica se ja existem mensagens para "Mostrar sempre" e "Mostrar sempre na abertura da sessão" pois so pode existir uma
                $messageClass = new MessengerClass();
                $mensagens = $messageClass->recuperaMensagem_ativo(_origemCarrinho_, $this->context->shop->id);

                if (Tools::getValue('fkmessenger_carrinho_freq_'.$id_msg) == _cartSempre_ or Tools::getValue('fkmessenger_carrinho_freq_'.$id_msg) == _cartSempreAbertura_) {
                    foreach ($mensagens as $mensagem) {
                        if ($mensagem['id'] != $id_msg and ($mensagem['frequencia'] == _cartSempre_ or $mensagem['frequencia'] == _cartSempreAbertura_)) {
                            $this->_postErrors[] = $this->l('Já existe mensagem com a Frequência "Mostrar sempre" ou "Mostrar sempre na abertura da sessão" cadastrada e só pode existir uma mensagem desse tipo cadastrada');
                            break;
                        }
                    }
                }

                // Identificacao da Mensagem
                if (Tools::getValue('fkmessenger_carrinho_nome_msg_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Identificação da mensagem não preenchida');
                }

                // Filtro
                if (Tools::getValue('fkmessenger_carrinho_freq_'.$id_msg) == (int)_cartSempreValorMenor_ or
                    Tools::getValue('fkmessenger_carrinho_freq_'.$id_msg) == (int)_cartUnicoValorMenor_ or
                    Tools::getValue('fkmessenger_carrinho_freq_'.$id_msg) == (int)_cartSempreValorMaior_ or
                    Tools::getValue('fkmessenger_carrinho_freq_'.$id_msg) == (int)_cartUnicoValorMaior_) {

                    if (Tools::getValue('fkmessenger_carrinho_filtro_'.$id_msg) == NULL) {
                        $this->_postErrors[] = $this->l('Não preenchido o valor do pedido');
                    }else {
                        $valor = str_replace(',', '.', Tools::getValue('fkmessenger_carrinho_filtro_'.$id_msg));

                        if (!is_numeric($valor)) {
                            $this->_postErrors[] = $this->l('O campo "Valor do pedido" não é numérico');
                        }else {
                            if ($valor < 0) {
                                $this->_postErrors[] = $this->l('O campo "Valor do pedido" não pode ser menor que 0 (zero)');
                            }
                        }
                    }

                }

                // Altura lightbox
                if (Tools::getValue('fkmessenger_carrinho_altura_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Altura do LightBox não preenchido');
                }else {
                    $valor = str_replace(',', '.', Tools::getValue('fkmessenger_carrinho_altura_'.$id_msg));

                    if (!is_numeric($valor)) {
                        $this->_postErrors[] = $this->l('O campo "Altura do LightBox" não é numérico');
                    }else {
                        if ($valor < 0) {
                            $this->_postErrors[] = $this->l('O campo "Altura do LightBox" não pode ser menor que 0 (zero)');
                        }
                    }
                }

                // Largura lightbox
                if (Tools::getValue('fkmessenger_carrinho_largura_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Largura do LightBox não preenchido');
                }else {
                    $valor = str_replace(',', '.', Tools::getValue('fkmessenger_carrinho_largura_'.$id_msg));

                    if (!is_numeric($valor)) {
                        $this->_postErrors[] = $this->l('O campo "Largura do LightBox" não é numérico');
                    }else {
                        if ($valor < 0) {
                            $this->_postErrors[] = $this->l('O campo "Largura do LightBox" não pode ser menor que 0 (zero)');
                        }
                    }
                }

                // Botao 1
                if (Tools::getValue('fkmessenger_carrinho_link1_'.$id_msg) != NULL and Tools::getValue('fkmessenger_carrinho_botao1_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Identificação do Botão 1 não preenchido');
                }

                if (Tools::getValue('fkmessenger_carrinho_link1_'.$id_msg) != NULL and Tools::getValue('fkmessenger_carrinho_cor1_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Cor do Botão 1 não selecionada');
                }

                // Botao 2
                if (Tools::getValue('fkmessenger_carrinho_link2_'.$id_msg) != NULL and Tools::getValue('fkmessenger_carrinho_botao2_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Identificação do Botão 2 não preenchido');
                }

                if (Tools::getValue('fkmessenger_carrinho_link2_'.$id_msg) != NULL and Tools::getValue('fkmessenger_carrinho_cor2_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Cor do Botão 2 não selecionada');
                }

                // Mensagem
                if (Tools::getValue('fkmessenger_carrinho_mensagem_'.$id_msg) == NULL) {
                    $this->_postErrors[] = $this->l('Mensagem não preenchida');
                }

                if (!$this->_postErrors) {
                    $this->postProcess($origem);
                }

                break;
             
        }
    }

    private function postProcess($origem) {

        switch($origem) {

            case 'configHome':
            
                // Exclui dados anteriores
                Db::getInstance()->delete('fkmessenger', 'origem = '.(int)_origemHome_.' and id_shop = '.(int)$this->context->shop->id);

                // Retira espaco entre as tags html
                $mensagem = $this->retiraEspacoTag(Tools::getValue('fkmessenger_home_mensagem'));

                // Inclui dados
                $dados = array(
                    'id_shop'           => $this->context->shop->id,
                    'origem'            => _origemHome_,
                    'frequencia'        => (!Tools::getValue('fkmessenger_home_freq') ? 1 : Tools::getValue('fkmessenger_home_freq')),
                    'modal'             => Tools::getValue('fkmessenger_home_modal'),
                    'altura'            => Tools::getValue('fkmessenger_home_altura'),
                    'largura'           => Tools::getValue('fkmessenger_home_largura'),
                    'nome_botao_1'      => Tools::getValue('fkmessenger_home_botao1'),
                    'link_botao_1'      => Tools::getValue('fkmessenger_home_link1'),
                    'nova_pagina_1'     => Tools::getValue('fkmessenger_home_link1_np'),
                    'cor_1'             => Tools::getValue('fkmessenger_home_cor1'),
                    'nome_botao_2'      => Tools::getValue('fkmessenger_home_botao2'),
                    'link_botao_2'      => Tools::getValue('fkmessenger_home_link2'),
                    'nova_pagina_2'     => Tools::getValue('fkmessenger_home_link2_np'),
                    'cor_2'             => Tools::getValue('fkmessenger_home_cor2'),
                    'mensagem'          => $mensagem,
                    'ativo'             => Tools::getValue('fkmessenger_home_ativo'),
                );
                
                Db::getInstance()->insert('fkmessenger', $dados);

                break;
                
            case 'configProduto':
            
                if (Tools::isSubmit('btnAdd')) {
                    
                    // Inclui dados inicias
                    $dados = array(
                        'id_shop'           => $this->context->shop->id,
                        'nome_mensagem'     => 'Nova Mensagem',
                        'origem'            => _origemProduto_,
                        'frequencia'        => _prodSempre_,
                        'tipo_filtro'       => _prodFiltroCategoria_,
                        'altura'            => '0',
                        'largura'           => '0', 
                        'cor_1'             => _btnBranco_,
                        'cor_2'             => _btnBranco_,
                    );
                    
                    Db::getInstance()->insert('fkmessenger', $dados);
                    
                    break;
                }
                
                if (Tools::isSubmit('btnDelete')) {
                     // Exclui registro
                     Db::getInstance()->delete('fkmessenger', 'id = '.(int)Tools::getValue('id'));
                     
                     break;
                }
                
                // Recupera ID da mensagem
                $id_msg = Tools::getValue('id');

                // Retira espaco entre as tags html
                $mensagem = $this->retiraEspacoTag(Tools::getValue('fkmessenger_produto_mensagem_'.$id_msg));
                
                // Inclui dados alterados
                $dados = array(
                    'id_shop'           => $this->context->shop->id,
                    'nome_mensagem'     => Tools::getValue('fkmessenger_produto_nome_msg_'.$id_msg),
                    'origem'            => _origemProduto_,
                    'frequencia'        => Tools::getValue('fkmessenger_produto_freq_'.$id_msg),
                    'tipo_filtro'       => Tools::getValue('fkmessenger_produto_tipo_filtro_'.$id_msg),
                    'filtro'            => Tools::getValue('fkmessenger_produto_filtro_'.$id_msg),
                    'modal'             => Tools::getValue('fkmessenger_produto_modal_'.$id_msg),
                    'altura'            => Tools::getValue('fkmessenger_produto_altura_'.$id_msg),
                    'largura'           => Tools::getValue('fkmessenger_produto_largura_'.$id_msg),
                    'nome_botao_1'      => Tools::getValue('fkmessenger_produto_botao1_'.$id_msg),
                    'link_botao_1'      => Tools::getValue('fkmessenger_produto_link1_'.$id_msg),
                    'nova_pagina_1'     => Tools::getValue('fkmessenger_produto_link1_np_'.$id_msg),
                    'cor_1'             => Tools::getValue('fkmessenger_produto_cor1_'.$id_msg),
                    'nome_botao_2'      => Tools::getValue('fkmessenger_produto_botao2_'.$id_msg),
                    'link_botao_2'      => Tools::getValue('fkmessenger_produto_link2_'.$id_msg),
                    'nova_pagina_2'     => Tools::getValue('fkmessenger_produto_link2_np_'.$id_msg),
                    'cor_2'             => Tools::getValue('fkmessenger_produto_cor2_'.$id_msg),
                    'mensagem'          => $mensagem,
                    'ativo'             => Tools::getValue('fkmessenger_produto_ativo_'.$id_msg),
                );
                
                Db::getInstance()->update('fkmessenger', $dados, 'id = '.(int)Tools::getValue('id'));
            
                break;

            case 'configCarrinho':

                if (Tools::isSubmit('btnAdd')) {

                    // Inclui dados inicias
                    $dados = array(
                        'id_shop'           => $this->context->shop->id,
                        'nome_mensagem'     => 'Nova Mensagem',
                        'origem'            => _origemCarrinho_,
                        'frequencia'        => _cartSempre_,
                        'valor_pedido'      => '0',
                        'altura'            => '0',
                        'largura'           => '0',
                        'cor_1'             => _btnBranco_,
                        'cor_2'             => _btnBranco_,
                    );

                    Db::getInstance()->insert('fkmessenger', $dados);

                    break;
                }

                if (Tools::isSubmit('btnDelete')) {
                    // Exclui registro
                    Db::getInstance()->delete('fkmessenger', 'id = '.(int)Tools::getValue('id'));

                    break;
                }

                // Recupera ID da mensagem
                $id_msg = Tools::getValue('id');

                // Retira espaco entre as tags html
                $mensagem = $this->retiraEspacoTag(Tools::getValue('fkmessenger_carrinho_mensagem_'.$id_msg));

                // Inclui dados alterados
                $dados = array(
                    'id_shop'           => $this->context->shop->id,
                    'nome_mensagem'     => Tools::getValue('fkmessenger_carrinho_nome_msg_'.$id_msg),
                    'origem'            => _origemCarrinho_,
                    'frequencia'        => Tools::getValue('fkmessenger_carrinho_freq_'.$id_msg),
                    'valor_pedido'      => (Tools::getValue('fkmessenger_carrinho_filtro_'.$id_msg) ? str_replace(',', '.', Tools::getValue('fkmessenger_carrinho_filtro_'.$id_msg)) : 0),
                    'modal'             => Tools::getValue('fkmessenger_carrinho_modal_'.$id_msg),
                    'altura'            => Tools::getValue('fkmessenger_carrinho_altura_'.$id_msg),
                    'largura'           => Tools::getValue('fkmessenger_carrinho_largura_'.$id_msg),
                    'nome_botao_1'      => Tools::getValue('fkmessenger_carrinho_botao1_'.$id_msg),
                    'link_botao_1'      => Tools::getValue('fkmessenger_carrinho_link1_'.$id_msg),
                    'nova_pagina_1'     => Tools::getValue('fkmessenger_carrinho_link1_np_'.$id_msg),
                    'cor_1'             => Tools::getValue('fkmessenger_carrinho_cor1_'.$id_msg),
                    'nome_botao_2'      => Tools::getValue('fkmessenger_carrinho_botao2_'.$id_msg),
                    'link_botao_2'      => Tools::getValue('fkmessenger_carrinho_link2_'.$id_msg),
                    'nova_pagina_2'     => Tools::getValue('fkmessenger_carrinho_link2_np_'.$id_msg),
                    'cor_2'             => Tools::getValue('fkmessenger_carrinho_cor2_'.$id_msg),
                    'mensagem'          => $mensagem,
                    'ativo'             => Tools::getValue('fkmessenger_carrinho_ativo_'.$id_msg),
                );

                Db::getInstance()->update('fkmessenger', $dados, 'id = '.(int)Tools::getValue('id'));

                break;
        }
    }

    public function hookdisplayHeader($params) {
        // CSS
        $this->context->controller->addCSS($this->_path.'css/fkmessenger_front.css');
        
        // JS
        $this->context->controller->addJS(_PS_JS_DIR_.'jquery/plugins/fancybox/jquery.fancybox.js');
    }

    public function hookDisplayHome($params) {
        
        // Recupera mensagem
        $messageClass = new MessengerClass();
        $mensagem = $messageClass->recuperaMensagem_ativo(_origemHome_, $this->context->shop->id);
        
        // Retorna se nao tem mensagem
        if (!$mensagem) {
            return false;
        }
        
        // Retorna se e para exibir somente na abertura e ja foi exibida
        if ($mensagem['frequencia'] == _homeSempreAbertura_ and isset($_COOKIE[_cookieHome_]) and $_COOKIE[_cookieHome_] == $mensagem['id']) {
            return false;
        }

        // Adiciona Fancybox caso QuickView esteja desativado
        if (!Configuration::get('PS_QUICK_VIEW')) {
            $this->context->controller->addjqueryPlugin('fancybox');
        }

        // Grava cookie
        setcookie(_cookieHome_, $mensagem['id'], 0);
        
        return $this->mostraMensagem($mensagem);
    }
    
    public function hookDisplayFooterProduct($params) {
        
        // Recupera mensagem
        $messageClass = new MessengerClass();
        $mensagens = $messageClass->recuperaMensagem_ativo(_origemProduto_, $this->context->shop->id);
        
        // Retorna se nao tem mensagens
        if (!$mensagens) {
            return false;
        }
        
        // Recupera ID da Categoria e ID do Produto
        $id_categoria = $params['category']->id;
        $id_produto = $params['product']->id; 
        
        // Verifica "Mostrar uma única vez para cada Produto definido no filtro"
        foreach ($mensagens as $mensagem) {
        
            if ($mensagem['tipo_filtro'] == _prodFiltroProduto_ and $mensagem['frequencia'] == _prodUnicoFiltro_) {
                
                $filtro_tmp = ','.$mensagem['filtro'].',';
                $produto_tmp = ','.$id_produto.','; 
                
                // Verifica se o produto esta no filtro
                if (strpos($filtro_tmp, $produto_tmp) === false) {
                }else {
                    // Verifica se ja foi mostrado
                    if (isset($_COOKIE[_cookieProd_])) {
                        $cookieArray = unserialize($_COOKIE[_cookieProd_]);
                        
                        if (isset($cookieArray[$mensagem['id']][_prodUnicoFiltro_][_prodFiltroProduto_][$id_produto])) {
                            return false;
                        }
                    }
                    
                    // Grava cookie e mostra mensagem
                    $this->gravaCookieProd($mensagem['id'], _prodUnicoFiltro_, _prodFiltroProduto_, $id_produto);
                    return $this->mostraMensagem($mensagem);
                }
            }    
        }

        // Verifica "Mostrar sempre que acessar qualquer Produto definido no filtro"
        foreach ($mensagens as $mensagem) {

            if ($mensagem['tipo_filtro'] == _prodFiltroProduto_ and $mensagem['frequencia'] == _prodSempreFiltro_) {

                $filtro_tmp = ','.$mensagem['filtro'].',';
                $produto_tmp = ','.$id_produto.',';

                // Verifica se o produto esta no filtro
                if (strpos($filtro_tmp, $produto_tmp) === false) {
                }else {
                    return $this->mostraMensagem($mensagem);
                }
            }
        }
        
        // Verifica "Mostrar uma única vez para cada Categoria definida no filtro"
        foreach ($mensagens as $mensagem) {
        
            if ($mensagem['tipo_filtro'] == _prodFiltroCategoria_ and $mensagem['frequencia'] == _prodUnicoFiltro_) {
                
                $filtro_tmp = ','.$mensagem['filtro'].',';
                $categoria_tmp = ','.$id_categoria.','; 
                
                // Verifica se a categoria esta no filtro
                if (strpos($filtro_tmp, $categoria_tmp) === false) {
                }else {
                    // Verifica se ja foi mostrado
                    if (isset($_COOKIE[_cookieProd_])) {
                        $cookieArray = unserialize($_COOKIE[_cookieProd_]);
                        
                        if (isset($cookieArray[$mensagem['id']][_prodUnicoFiltro_][_prodFiltroCategoria_][$id_categoria])) {
                            return false;
                        }
                    }
                    
                    // Grava cookie e mostra mensagem
                    $this->gravaCookieProd($mensagem['id'], _prodUnicoFiltro_, _prodFiltroCategoria_, $id_categoria);
                    return $this->mostraMensagem($mensagem);
                }
            }    
        }

        // Verifica "Mostrar sempre que acessar qualquer Categoria definida no filtro"
        foreach ($mensagens as $mensagem) {
        
            if ($mensagem['tipo_filtro'] == _prodFiltroCategoria_ and $mensagem['frequencia'] == _prodSempreFiltro_) {
                
                $filtro_tmp = ','.$mensagem['filtro'].',';
                $categoria_tmp = ','.$id_categoria.','; 
                
                // Verifica se a categoria esta no filtro
                if (strpos($filtro_tmp, $categoria_tmp) === false) {
                }else {
                    return $this->mostraMensagem($mensagem);
                }
            }    
        }
        
        // Verifica "Mostrar sempre na abertura da sessão"
        foreach ($mensagens as $mensagem) {
        
            if ($mensagem['frequencia'] == _prodSempreAbertura_) {
                
                // Verifica se ja foi mostrado
                if (isset($_COOKIE[_cookieProd_])) {
                    $cookieArray = unserialize($_COOKIE[_cookieProd_]);
                    
                    if (isset($cookieArray[$mensagem['id']][_prodSempreAbertura_])) {
                        return false;
                    }
                }
                
                // Grava cookie e mostra mensagem
                $this->gravaCookieProd($mensagem['id'], _prodSempreAbertura_, '0', '0');
                return $this->mostraMensagem($mensagem);
            }    
        }
        
        // Verifica "Mostrar sempre"
        foreach ($mensagens as $mensagem) {
        
            if ($mensagem['frequencia'] == _prodSempre_) {
                return $this->mostraMensagem($mensagem);
            }    
        }
        
    }

    public function hookDisplayShoppingCartFooter($params) {

        // Recupera mensagem
        $messageClass = new MessengerClass();
        $mensagens = $messageClass->recuperaMensagem_ativo(_origemCarrinho_, $this->context->shop->id);

        // Retorna se nao tem mensagens
        if (!$mensagens) {
            return false;
        }

        // Recupera valor do pedido
        $valor_pedido = $this->context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);

        // Verifica "Mostrar uma única vez quando o valor do pedido for menor que o definido no filtro"
        foreach ($mensagens as $mensagem) {

            if ($mensagem['frequencia'] == _cartUnicoValorMenor_ and $valor_pedido < $mensagem['valor_pedido']) {

                // Verifica se ja foi mostrado
                if (isset($_COOKIE[_cookieCart_])) {
                    $cookieArray = unserialize($_COOKIE[_cookieCart_]);

                    if (isset($cookieArray[$mensagem['id']][_cartUnicoValorMenor_][$valor_pedido])) {
                        return false;
                    }
                }

                // Grava cookie e mostra mensagem
                $this->gravaCookieCart($mensagem['id'], _cartUnicoValorMenor_, $valor_pedido);
                return $this->mostraMensagem($mensagem);
            }
        }

        // Verifica "Mostrar sempre que o valor do pedido for menor que o definido no filtro"
        foreach ($mensagens as $mensagem) {

            if ($mensagem['frequencia'] == _cartSempreValorMenor_ and $valor_pedido < $mensagem['valor_pedido']) {

                // Mostra mensagem
                return $this->mostraMensagem($mensagem);
            }
        }

        // Verifica "Mostrar uma única vez quando o valor do pedido for maior que o definido no filtro"
        foreach ($mensagens as $mensagem) {

            if ($mensagem['frequencia'] == _cartUnicoValorMaior_ and $valor_pedido > $mensagem['valor_pedido']) {

                // Verifica se ja foi mostrado
                if (isset($_COOKIE[_cookieCart_])) {
                    $cookieArray = unserialize($_COOKIE[_cookieCart_]);

                    if (isset($cookieArray[$mensagem['id']][_cartUnicoValorMaior_][$valor_pedido])) {
                        return false;
                    }
                }

                // Grava cookie e mostra mensagem
                $this->gravaCookieCart($mensagem['id'], _cartUnicoValorMaior_, $valor_pedido);
                return $this->mostraMensagem($mensagem);
            }
        }

        // Verifica "Mostrar sempre que o valor do pedido for maior que o definido no filtro"
        foreach ($mensagens as $mensagem) {

            if ($mensagem['frequencia'] == _cartSempreValorMaior_ and $valor_pedido > $mensagem['valor_pedido']) {

                // Mostra mensagem
                return $this->mostraMensagem($mensagem);
            }
        }

        // Verifica "Mostrar sempre na abertura da sessão"
        foreach ($mensagens as $mensagem) {

            if ($mensagem['frequencia'] == _cartSempreAbertura_) {

                // Verifica se ja foi mostrado
                if (isset($_COOKIE[_cookieCart_])) {
                    $cookieArray = unserialize($_COOKIE[_cookieCart_]);

                    if (isset($cookieArray[$mensagem['id']][_prodSempreAbertura_])) {
                        return false;
                    }
                }

                // Grava cookie e mostra mensagem
                $this->gravaCookieCart($mensagem['id'], _cartSempreAbertura_, '0');
                return $this->mostraMensagem($mensagem);
            }
        }

        // Verifica "Mostrar sempre"
        foreach ($mensagens as $mensagem) {

            if ($mensagem['frequencia'] == _cartSempre_) {
                return $this->mostraMensagem($mensagem);
            }
        }
        
    }

    private function retiraEspacoTag($htmlFull) {

        $htmlArray = explode('>', $htmlFull);

        $htmlSemEspaco = '';

        foreach($htmlArray as $html) {
            $htmlSemEspaco .= trim($html);

            if ($html != ''){
                $htmlSemEspaco .= '>';
            }
        }

        return $htmlSemEspaco;
    }

    private function retornaClasseCor($cor) {
      
        switch ($cor) {
            
           case _btnBranco_:
             return ' btn-branco';
             break;
             
           case _btnPreto_:
             return ' btn-Preto';
             break;
             
           case _btnAzul_:
             return ' btn-Azul';
             break;  
             
           case _btnVerde_:
             return ' btn-Verde';
             break;  
           
           case _btnVermelho_:
             return ' btn-Vermelho';
             break;  
             
        }    
    }
    
    private function gravaCookieProd($id_msg, $freq, $tipo_filtro, $id_produto) {
    
        $cookieArray = array(
                            $id_msg => array(
                                $freq => array(
                                    $tipo_filtro => array($id_produto => $id_produto)
                        )));
                        
        $cookieArray = serialize($cookieArray);
        setcookie(_cookieProd_, $cookieArray, 0);
                            
    }

    private function gravaCookieCart($id_msg, $freq, $valor_pedido) {

        $cookieArray = array(
                            $id_msg => array(
                                $freq => array(
                                    $valor_pedido => $valor_pedido
                        )));

        $cookieArray = serialize($cookieArray);
        setcookie(_cookieCart_, $cookieArray, 0);

    }
    
    private function mostraMensagem($mensagem) {
    
        // Variaveis smarty
        $modal = 'nao';
        if ($mensagem['modal'] == '1') {
            $modal = 'sim';
        }

        $altura = $mensagem['altura']; 
        $largura = $mensagem['largura'];
        
        // Define se o botao deve ser mostrado, seu tamanho e cor
        if ($mensagem['link_botao_1'] == '') {
            $class_botao_1 = "fkmessenger-display-none";
        }else {
            if ($mensagem['link_botao_2'] == '') {
                $class_botao_1 = "fkmessenger-col-lg-100";
            }else {
                $class_botao_1 = "fkmessenger-col-lg-48";
            }
            
            $class_botao_1 .= $this->retornaClasseCor($mensagem['cor_1']);
        }
        
        if ($mensagem['link_botao_2'] == '') {
            $class_botao_2 = "fkmessenger-display-none";
        }else {
            if ($mensagem['link_botao_1'] == '') {
                $class_botao_2 = "fkmessenger-col-lg-100";
            }else {
                $class_botao_2 = "fkmessenger-col-lg-48";
            }
            
            $class_botao_2 .= $this->retornaClasseCor($mensagem['cor_2']);    
        }
        
        $this->smarty->assign(array(
            'modal'         => $modal,
            'altura'        => $altura,
            'largura'       => $largura,
            'mensagem'      => $mensagem['mensagem'],
            'class_botao_1' => $class_botao_1,
            'nome_botao_1'  => ($mensagem['nome_botao_1'] != '' ? $mensagem['nome_botao_1'] : ''),
            'link_botao_1'  => ($mensagem['link_botao_1'] != '' ? $mensagem['link_botao_1'] : ''),
            'nova_pagina_1' => ($mensagem['nova_pagina_1'] == true ? '_blank' : '_self'),
            'class_botao_2' => $class_botao_2,
            'nome_botao_2'  => ($mensagem['nome_botao_2'] != '' ? $mensagem['nome_botao_2'] : ''),
            'link_botao_2'  => ($mensagem['link_botao_2'] != '' ? $mensagem['link_botao_2'] : ''),
            'nova_pagina_2' => ($mensagem['nova_pagina_2'] == true ? '_blank' : '_self'),
        ));
        
        return $this->display(__FILE__, 'views/front/mensagem.tpl');    
    }
    
    private function criaTabelas() {

        $db = Db::getInstance();

        // Cria a tabela de mensagens
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fkmessenger` (
                `id`                int(10)         NOT NULL AUTO_INCREMENT,
                `id_shop`           int(10)         NULL,
                `nome_mensagem`     varchar(50)     NULL,
                `origem`            int(1)          NULL,
                `frequencia`        int(10)         NULL,
                `tipo_filtro`       int(10)         NULL,
                `filtro`            text            NULL,
                `valor_pedido`      decimal(20,2)   NULL,
                `modal`             int(1)          NULL,
                `altura`            int(10)         NULL,
                `largura`           int(10)         NULL,
                `nome_botao_1`      varchar(50)     NULL,
                `link_botao_1`      varchar(500)    NULL,
                `nova_pagina_1`     int(1)          NULL,
                `cor_1`             int(10)         NULL,
                `nome_botao_2`      varchar(50)     NULL,
                `link_botao_2`      varchar(500)    NULL,
                `nova_pagina_2`     int(1)          NULL,
                `cor_2`             int(10)         NULL,
                `mensagem`          text            NULL,
                `ativo`             int(1)          NULL,
                PRIMARY KEY (`id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);
        
        return true;
        
    }
    
    private function excluiTabelas() {
        
        $db = Db::getInstance();

        $sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkmessenger`;";
        $db-> Execute($sql);

        return true;
    }

}
  
?>