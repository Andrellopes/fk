<?php

include_once dirname(__FILE__) . '/../../models/ImpEtiqEnderClass.php';

class AdminImpEtiqEnderController extends ModuleAdminController {

    public function __construct() {

        $this->bootstrap = true;
        $this->context = Context::getContext();

        // Define query e campos a serem mostrados
        $this->className = 'ImpEtiqEnderClass';
        $this->list_id = 'fkcorreiosg2cp2_etiquetas_ender';   // nome do campo da funcao Bulk
        $this->lang = false;
        $this->list_no_link = true;
        $this->table = 'fkcorreiosg2cp2_etiquetas_ender';
        $this->identifier = 'id';
        $this->addRowAction('imprimirpdf');
        $this->addRowAction('delete');

        $this->_filter = "AND a.id_shop = ".(int)$this->context->shop->id;
        $this->_defaultOrderBy = 'id';
        $this->_defaultOrderWay = 'DESC';

        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'left',
                'width' => 80,
            ),
            'arquivo_pdf' => array(
                'title' => $this->l('Arquivo'),
                'align' => 'left'
            ),
            'data_criacao' => array(
                'title' => $this->l('Data de criação'),
                'type'  => 'datetime',
                'align' => 'left',
            ),
        );

        $this->bulk_actions = array(
            'excluir' => array(
                'text' => $this->l('Excluir PDF'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Excluir os itens selecionados?')
            )
        );

        parent::__construct();
    }

    public function initContent() {

        // Exclusao de varios arquivos pdf
        if ($this->action == 'bulkexcluir') {
            $imprClass = new ImpEtiqEnderClass();
            $imprClass->excluiRegistro(Tools::getValue('fkcorreiosg2cp2_etiquetas_enderBox'));
        }

        parent::initContent();
    }

    public function initPageHeaderToolbar() {

        $this->page_header_toolbar_btn['EtiqEnder'] = array(
            'href' => $this->context->link->getAdminLink('AdminGerarEtiqEnder'),
            'desc' => $this->l('Retornar', null, null, false),
            'icon' => 'process-icon-back'
        );

        parent::initPageHeaderToolbar();
    }

    public function initToolbar() {

        parent::initToolbar();

        // Desativa botoes
        unset($this->toolbar_btn['new']);

        $this->toolbar_title = $this->l('Imprimir etiquetas de endereçamento');
    }

    // cria imprimirpdf action list
    public function displayImprimirPDFLink($token = null, $id) {

        // Recupera nome do arquivo PDF
        $imprClass = new ImpEtiqEnderClass();
        $arquivo = $imprClass->recuperaNomeArquivo($id);

        if (!array_key_exists('imprimirpdf', self::$cache_lang)) {
            self::$cache_lang['imprimirpdf'] = $this->l('Imprimir PDF');
        }

        $this->context->smarty->assign(array(
            'linkPdf'   => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/fkcorreiosg2cp2/pdf/'.$arquivo['arquivo_pdf'],
            'action'    => self::$cache_lang['imprimirpdf'],
        ));

        return $this->context->smarty->fetch(_PS_MODULE_DIR_.FK_NOME_MODULO.'/views/templates/admin/list_action_imprimirpdf.tpl');
    }

    public function processDelete() {
        $imprClass = new ImpEtiqEnderClass();
        return $imprClass->excluiRegistro(Tools::getValue('id'));
    }

    // Funcao alterada para acerto de bug do Prestashop
    public function processResetFilters($list_id = null) {
        parent::processResetFilters();
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminImpEtiqEnder'));
    }

}