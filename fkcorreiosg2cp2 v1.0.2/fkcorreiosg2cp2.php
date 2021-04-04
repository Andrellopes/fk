<?php

include_once _PS_MODULE_DIR_.'fkcorreiosg2cp2/defines/defines.php';

class fkcorreiosg2cp2 extends Module {

    private $html = '';
    private $postErrors = array();
    private $tab_select = '';
    
    public function __construct() {

        $this->name     = 'fkcorreiosg2cp2';
        $this->tab      = 'shipping_logistics';
        $this->version  = '1.0.2';
        $this->author   = 'módulosFK';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('FKcorreiosg2cp2 - Complemento Etiquetas de Endereçamento');
        $this->description = $this->l('Impressão de etiquetas para envio aos Correios.');

        // Registro do complemento no FKcorreiosg2
        $this->incluiRegistroModulo();

        // Array com nome dos Controllers do menu
        $this->_menuClassName[] = array('className' => 'AdminGerarEtiqEnder', 'name' => $this->l('Etiquetas de Endereçamento'));

        // Array com nome dos Controllers das opcoes
        $this->_opcoesClassName[] = array('className' => 'AdminImpEtiqEnder', 'name' => $this->l('Imprime Etiquetas de Endereçamento'));

    }

    public function install() {

        // Verifica se o FKcorreiosg2 esta instalado
        if (!module::isInstalled('fkcorreiosg2')) {
            $this->_errors[] = Tools::displayError('O módulo principal FKcorreiosg2 não está instalado.');
            return false;
        }

        if (!parent::install()
            Or !$this->criaMenus()
            Or !$this->criaTabelas()
            Or !$this->alteraTabela()) {

            return false;
        }

        // Atualiza configuracoes se nao existit
        if (!Configuration::hasKey('FKCORREIOSG2CP2_REMETENTE')) {
            Configuration::updateValue('FKCORREIOSG2CP2_REMETENTE', Configuration::get('PS_SHOP_NAME'));
        }

        if (!Configuration::hasKey('FKCORREIOSG2CP2_ENDERECO')) {
            Configuration::updateValue('FKCORREIOSG2CP2_ENDERECO', Configuration::get('PS_SHOP_ADDR1'));
        }

        if (!Configuration::hasKey('FKCORREIOSG2CP2_NUMERO')) {
            Configuration::updateValue('FKCORREIOSG2CP2_NUMERO', '');
        }

        if (!Configuration::hasKey('FKCORREIOSG2CP2_BAIRRO')) {
            Configuration::updateValue('FKCORREIOSG2CP2_BAIRRO', Configuration::get('PS_SHOP_ADDR2'));
        }

        if (!Configuration::hasKey('FKCORREIOSG2CP2_CIDADE')) {
            Configuration::updateValue('FKCORREIOSG2CP2_CIDADE', Configuration::get('PS_SHOP_CITY'));
        }

        if (!Configuration::hasKey('FKCORREIOSG2CP2_ESTADO')) {
            $state = new State(Configuration::get('PS_SHOP_STATE_ID'));
            $estado = $state->iso_code;
            Configuration::updateValue('FKCORREIOSG2CP2_ESTADO', $estado);
        }

        if (!Configuration::hasKey('FKCORREIOSG2CP2_CEP')) {
            Configuration::updateValue('FKCORREIOSG2CP2_CEP', Configuration::get('PS_SHOP_CODE'));
        }

        if (!Configuration::hasKey('FKCORREIOSG2CP2_ETIQ_PAGINA')) {
            Configuration::updateValue('FKCORREIOSG2CP2_ETIQ_PAGINA', '4');
        }

        if (!Configuration::hasKey('FKCORREIOSG2CP2_IMPRESSO')) {
            Configuration::updateValue('FKCORREIOSG2CP2_IMPRESSO', '0');
        }

        if (!Configuration::hasKey('FKCORREIOSG2CP2_NAO_PAGOS')) {
            Configuration::updateValue('FKCORREIOSG2CP2_NAO_PAGOS', '');
        }

        if (!Configuration::hasKey('FKCORREIOSG2CP2_EXCLUIR_CONFIG')) {
            Configuration::updateValue('FKCORREIOSG2CP2_EXCLUIR_CONFIG', '');
        }

        // Processa atualizacao de versao
        if (!$this->atualizaVersaoModulo()) {
            $this->_errors[] = Tools::displayError('Erro durante atualização da versão do complemento.');
            return false;
        }

        return true;

    }

    public function uninstall() {

        if (!parent::uninstall()
            Or !$this->excluiMenus()
            Or !$this->excluiRegistroModulo()) {
            return false;
        }

        if (Configuration::get('FKCORREIOSG2CP2_EXCLUIR_CONFIG') == 'on') {
            // Exclui tabelas
            $this->excluiTabelas();

            // Exclui campo da tabela orders
            $sql = "ALTER TABLE "._DB_PREFIX_."orders DROP COLUMN fk_etiq_ender;";
            Db::getInstance()->Execute($sql);

            // Exclui configuracoes
            if (!Db::getInstance()->delete("configuration", "name LIKE 'FKCORREIOSG2CP2_%'")) {
                return false;
            }
        }

        return true;
    }

    public function getContent() {

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

    private function renderForm() {

        // CSS
        $this->context->controller->addCSS(_PS_MODULE_DIR_.'fkcorreiosg2/css/fkcorreiosg2_admin.css');

        // JS
        $this->context->controller->addJS(_PS_MODULE_DIR_.'fkcorreiosg2/js/jquery.maskedinput.js');
        $this->context->controller->addJS($this->_path.'js/fkcorreiosg2cp2_admin.js');

        $this->configGeral();

        $this->smarty->assign(array(
            'fkcorreiosg2cp2' => array(
                'pathInclude'   => _PS_MODULE_DIR_.$this->name.'/views/config/',
                'tabSelect'     => $this->tab_select,
            )

        ));

        return $this->display(__FILE__, 'views/config/mainConfig.tpl');
    }

    private function configGeral() {

        // TPL a ser utilizado
        $name_tpl ='configGeral.tpl';

        if (Configuration::get('FKCORREIOSG2CP2_ETIQ_PAGINA') == 2) {
            $urlLogo = FK_URL_IMG.'logo_2.jpg';
            $uriLogo = FK_URI_IMG.'logo_2.jpg';
        }else {
            $urlLogo = FK_URL_IMG.'logo_4.jpg';
            $uriLogo = FK_URI_IMG.'logo_4.jpg';
        }

        $this->smarty->assign(array(
            'tab_2' => array(
                'nameTpl'                           => $name_tpl,
                'formAction'                        => Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']),
                'urlLogo'                           => $urlLogo,
                'uriLogo'                           => $uriLogo,
                'urlNoImage'                        => FK_URL_IMG.'no_image.jpg',
                'fkcorreiosg2cp2_remetente'        => Tools::getValue('fkcorreiosg2cp2_remetente', Configuration::get('FKCORREIOSG2CP2_REMETENTE')),
                'fkcorreiosg2cp2_endereco'         => Tools::getValue('fkcorreiosg2cp2_endereco', Configuration::get('FKCORREIOSG2CP2_ENDERECO')),
                'fkcorreiosg2cp2_numero'           => Tools::getValue('fkcorreiosg2cp2_numero', Configuration::get('FKCORREIOSG2CP2_NUMERO')),
                'fkcorreiosg2cp2_bairro'           => Tools::getValue('fkcorreiosg2cp2_bairro', Configuration::get('FKCORREIOSG2CP2_BAIRRO')),
                'fkcorreiosg2cp2_cidade'           => Tools::getValue('fkcorreiosg2cp2_cidade', Configuration::get('FKCORREIOSG2CP2_CIDADE')),
                'fkcorreiosg2cp2_estado'           => Tools::getValue('fkcorreiosg2cp2_estado', Configuration::get('FKCORREIOSG2CP2_ESTADO')),
                'fkcorreiosg2cp2_cep'              => Tools::getValue('fkcorreiosg2cp2_cep', Configuration::get('FKCORREIOSG2CP2_CEP')),
                'fkcorreiosg2cp2_etiq_pagina'      => Tools::getValue('fkcorreiosg2cp2_etiq_pagina', Configuration::get('FKCORREIOSG2CP2_ETIQ_PAGINA')),
                'fkcorreiosg2cp2_impresso'         => Tools::getValue('fkcorreiosg2cp2_impresso', Configuration::get('FKCORREIOSG2CP2_IMPRESSO')),
                'fkcorreiosg2cp2_nao_pagos'        => Tools::getValue('fkcorreiosg2cp2_nao_pagos', Configuration::get('FKCORREIOSG2CP2_NAO_PAGOS')),
                'fkcorreiosg2cp2_excluir_config'   => Tools::getValue('fkcorreiosg2cp2_excluir_config', Configuration::get('FKCORREIOSG2CP2_EXCLUIR_CONFIG')),
            )
        ));

    }

    private function postValidation() {

        $origem = Tools::getValue('origem');

        switch($origem) {

            case 'configGeral':

                // Tab selecionada
                $this->tab_select = '2';

                if (Trim(Tools::getValue('fkcorreiosg2cp2_remetente')) == '') {
                    $this->postErrors[] = $this->l('Campo "Remetente" não preenchido.');
                }

                if (Trim(Tools::getValue('fkcorreiosg2cp2_endereco')) == '') {
                    $this->postErrors[] = $this->l('Campo "Endereço" não preenchido.');
                }

                if (Trim(Tools::getValue('fkcorreiosg2cp2_numero')) == '') {
                    $this->postErrors[] = $this->l('Campo "Número" não preenchido.');
                }

                if (Trim(Tools::getValue('fkcorreiosg2cp2_bairro')) == '') {
                    $this->postErrors[] = $this->l('Campo "Bairro" não preenchido.');
                }

                if (Trim(Tools::getValue('fkcorreiosg2cp2_cidade')) == '') {
                    $this->postErrors[] = $this->l('Campo "Cidade" não preenchido.');
                }

                if (Trim(Tools::getValue('fkcorreiosg2cp2_estado')) == '') {
                    $this->postErrors[] = $this->l('Campo "Estado" não preenchido.');
                }

                if (Trim(Tools::getValue('fkcorreiosg2cp2_cep')) == '') {
                    $this->postErrors[] = $this->l('Campo "CEP" não preenchido.');
                }

                if (Trim(Tools::getValue('fkcorreiosg2cp2_impresso')) == '') {
                    $this->postErrors[] = $this->l('Campo "Considerar Impresso até o Pedido" não preenchido');
                }else {
                    if (!is_numeric(Tools::getValue('fkcorreiosg2cp2_impresso'))) {
                        $this->postErrors[] = $this->l('O campo "Considerar Impresso até o Pedido" não é numérico');
                    }else {
                        if (Tools::getValue('fkcorreiosg2cp2_impresso') < 0) {
                            $this->postErrors[] = $this->l('O campo "Considerar Impresso até o Pedido" não pode ser menor que 0 (zero)');
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

                Configuration::updateValue('FKCORREIOSG2CP2_REMETENTE', Trim(Tools::getValue('fkcorreiosg2cp2_remetente')));
                Configuration::updateValue('FKCORREIOSG2CP2_ENDERECO', Trim(Tools::getValue('fkcorreiosg2cp2_endereco')));
                Configuration::updateValue('FKCORREIOSG2CP2_NUMERO', Trim(Tools::getValue('fkcorreiosg2cp2_numero')));
                Configuration::updateValue('FKCORREIOSG2CP2_BAIRRO', Trim(Tools::getValue('fkcorreiosg2cp2_bairro')));
                Configuration::updateValue('FKCORREIOSG2CP2_CIDADE', Trim(Tools::getValue('fkcorreiosg2cp2_cidade')));
                Configuration::updateValue('FKCORREIOSG2CP2_ESTADO', Trim(Tools::getValue('fkcorreiosg2cp2_estado')));
                Configuration::updateValue('FKCORREIOSG2CP2_CEP', Trim(Tools::getValue('fkcorreiosg2cp2_cep')));
                Configuration::updateValue('FKCORREIOSG2CP2_ETIQ_PAGINA', Trim(Tools::getValue('fkcorreiosg2cp2_etiq_pagina')));
                Configuration::updateValue('FKCORREIOSG2CP2_IMPRESSO', Trim(Tools::getValue('fkcorreiosg2cp2_impresso')));
                Configuration::updateValue('FKCORREIOSG2CP2_NAO_PAGOS', Trim(Tools::getValue('fkcorreiosg2cp2_nao_pagos')));
                Configuration::updateValue('FKCORREIOSG2CP2_EXCLUIR_CONFIG', Trim(Tools::getValue('fkcorreiosg2cp2_excluir_config')));

                // Marca pedidos impressos
                $dados = array(
                    'fk_etiq_ender'   => 1,
                );

                Db::getInstance()->update('orders', $dados, 'id_order <= '.(int)Trim(Tools::getValue('fkcorreiosg2cp2_impresso')));

                // Copia logo
                $extensoes_permitidas = array('0' => 'jpg');

                if(!empty($_FILES['fkcorreiosg2cp2_logo']['name'])) {

                    // Verifica se houve algum erro com o upload
                    if ($_FILES['fkcorreiosg2cp2_logo']['error'] != 0) {
                        $this->postErrors[] = $this->l('Erro durante upload da imagem.');
                        break;
                    }

                    // Verifica extensão do arquivo
                    $array = explode('.', $_FILES['fkcorreiosg2cp2_logo']['name']);
                    $extensao = end($array);
                    $extensao = strtolower($extensao);

                    if (array_search($extensao, $extensoes_permitidas) === false) {
                        $this->postErrors[] = $this->l('Permitido somente arquivos com extensões jpg.');
                        break;
                    }

                    // Move o logo para a pasta upload dando rename
                    if (Configuration::get('FKCORREIOSG2CP2_ETIQ_PAGINA') == 2) {
                        $logo = 'logo_2.jpg';
                    }else {
                        $logo = 'logo_4.jpg';
                    }

                    if (!move_uploaded_file($_FILES['fkcorreiosg2cp2_logo']['tmp_name'], FK_URI_IMG.$logo)) {
                        $this->postErrors[] = $this->l('Não foi possível gravar o Logo na pasta img.');
                        break;
                    }
                }

                break;

        }
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
                    'frete'     => false,
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

        // TODO: utilizar quando houver alterações de versao que necessitem alterar tabelas ou configuracoes

        return true;
    }

    private function criaMenus() {

        // Cria tab principal
        $mainTab = Tab::getInstanceFromClassName('AdminFKcorreiosg2');
        $mainTab->active = true;
        $mainTab->save();

        // Registra os Controllers do menu
        for ($i = 0; $i < count($this->_menuClassName); $i++) {

            $tab = new Tab();
            $tab->class_name = $this->_menuClassName[$i]['className'];

            $languages = Language::getLanguages();
            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = $this->_menuClassName[$i]['name'];
            }

            $tab->id_parent = $mainTab->id;
            $tab->module = $this->name;
            $tab->add();
        }

        // Registra os Controllers das opcoes
        for ($i = 0; $i < count($this->_opcoesClassName); $i++) {

            $tab = new Tab();
            $tab->class_name = $this->_opcoesClassName[$i]['className'];

            $languages = Language::getLanguages();
            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = $this->_opcoesClassName[$i]['name'];
            }

            $tab->id_parent = -1;
            $tab->module = $this->name;
            $tab->add();
        }

        return true;
    }

    private function excluiMenus() {

        // Exclui Controllers do menu
        for ($i = 0; $i < count($this->_menuClassName); $i++) {

            $idTab = Tab::getIdFromClassName($this->_menuClassName[$i]['className']);

            if ($idTab) {
                $tab = new Tab($idTab);
                $tab->delete();
            }
        }

        // Exclui Controllers das opcoes
        for ($i = 0; $i < count($this->_opcoesClassName); $i++) {

            $id_tab = Tab::getIdFromClassName($this->_opcoesClassName[$i]['className']);

            if ($id_tab) {
                $tab = new Tab($id_tab);
                $tab->delete();
            }
        }

        // Desabilita menu principal se nao tiver mais submenus
        $idMainTab = Tab::getIdFromClassName('AdminFKcorreiosg2');

        if (Tab::getNbTabs($idMainTab) == 0) {
            $mainTab = Tab::getInstanceFromClassName('AdminFKcorreiosg2');
            $mainTab->active = false;
            $mainTab->save();
        }

        return true;
    }

    private function alteraTabela() {

        $db = Db::getInstance();

        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '"._DB_PREFIX_."orders' AND column_name = 'fk_etiq_ender' AND table_schema = '"._DB_NAME_."'";
        $dados = $db->getRow($sql);

        if (!$dados) {
            $sql =   "ALTER TABLE "._DB_PREFIX_."orders ADD fk_etiq_ender tinyint(1) DEFAULT 0;";
            $db->Execute($sql);
        }

        return true;
    }

    private function criaTabelas() {

        // Cria a tabela contendo o controle dos pdf

        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fkcorreiosg2cp2_etiquetas_ender` (
                `id`            int(10)         NOT NULL AUTO_INCREMENT,
                `id_shop`       int(10)         NULL,
                `arquivo_pdf`   varchar(255)    NULL,
                `data_criacao`  datetime        NULL,
                PRIMARY KEY (`id`),
                KEY `arquivo_pdf` (`arquivo_pdf`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        Db::getInstance()-> Execute($sql);

        return true;
    }

    private function excluiTabelas() {

        $sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkcorreiosg2cp2_etiquetas_ender`;";
        Db::getInstance()-> Execute($sql);

        return true;
    }

}