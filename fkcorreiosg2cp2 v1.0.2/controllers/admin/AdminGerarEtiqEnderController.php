<?php

include_once dirname(__FILE__) . '/../../models/GerarEtiqEnderClass.php';

class AdminGerarEtiqEnderController extends ModuleAdminController {

    public function __construct() {

        $this->bootstrap = true;
        $this->context = Context::getContext();

        // Define query e campos a serem mostrados
        $this->className = 'GerarEtiqEnderClass';
        $this->list_id = 'orders';   // nome do campo da funcao Bulk
        $this->lang = false;
        $this->list_no_link = true;
        $this->table = 'orders';
        $this->identifier = 'id_order';
        $this->addRowAction('gerarpdf');

        $this->_select = "b.firstname,
                          b.lastname,
                          b.email";

        $this->_join = "INNER JOIN "._DB_PREFIX_."customer b
                            ON a.id_customer = b.id_customer
                            AND a.id_lang = b.id_lang";

        if (Configuration::get('FKCORREIOSG2CP2_NAO_PAGOS') == 'on') {
            $this->_filter = "AND a.id_shop = ".(int)$this->context->shop->id;
        }else {
            $this->_filter = "AND a.invoice_date <> '0000-00-00 00:00:00' AND a.id_shop = ".(int)$this->context->shop->id;
        }

        $this->_defaultOrderBy = 'id_order';
        $this->_defaultOrderWay = 'DESC';

        $this->fields_list = array(
            'id_order' => array(
                'title' => $this->l('Pedido'),
                'align' => 'left',
                'width' => 80,
            ),
            'reference' => array(
                'title' => $this->l('Referência'),
                'align' => 'left'
            ),
            'firstname' => array(
                'title' => $this->l('Nome'),
                'type'  => 'text',
                'align' => 'left'
            ),
            'lastname' => array(
                'title' => $this->l('Sobrenome'),
                'type'  => 'text',
                'align' => 'left'
            ),
            'total_paid' => array(
                'title' => $this->l('Valor'),
                'type'  => 'price',
                'align' => 'left'
            ),
            'date_add' => array(
                'title' => $this->l('Data do pedido'),
                'type'  => 'datetime',
                'align' => 'left',
            ),
            'fk_etiq_ender' => array(
                'title' => $this->l('Impresso'),
                'type'  => 'bool',
                'align' => 'center',
                'callback' => 'jaImpresso'
            ),
        );

        $this->bulk_actions = array(
            'gerar' => array(
                'text' => $this->l('Gerar PDF'),
                'icon' => 'icon-file-o',
            )
        );

        parent::__construct();

    }

    public function initContent() {

        // Pedido unico
        if (Tools::isSubmit('gerarpdf')) {
            $etiquetas = new GerarEtiqEnderClass();

            if (Configuration::get('FKCORREIOSG2CP2_ETIQ_PAGINA') == '2') {
                $etiquetas->geraEtiquetas_2(Tools::getValue('id_order'));
            }else {
                $etiquetas->geraEtiquetas_4(Tools::getValue('id_order'));
            }

            Tools::redirectAdmin($this->context->link->getAdminLink('AdminImpEtiqEnder'));
        } else {
            // Varios pedidos selecionados
            if ($this->action == 'bulkgerar') {
                $etiquetas = new GerarEtiqEnderClass();

                if (Configuration::get('FKCORREIOSG2CP2_ETIQ_PAGINA') == '2') {
                    $etiquetas->geraEtiquetas_2(Tools::getValue('ordersBox'));
                }else {
                    $etiquetas->geraEtiquetas_4(Tools::getValue('ordersBox'));
                }

                Tools::redirectAdmin($this->context->link->getAdminLink('AdminImpEtiqEnder'));
            }
        }

        parent::initContent();
    }

    public function initPageHeaderToolbar() {

        $this->page_header_toolbar_btn['EtiqEnder'] = array(
            'href' => $this->context->link->getAdminLink('AdminImpEtiqEnder'),
            'desc' => $this->l('Ir para PDFs gerados', null, null, false),
            'icon' => 'process-icon-next'
        );

        parent::initPageHeaderToolbar();
    }

    public function initToolbar() {

        parent::initToolbar();

        // Desativa botoes
        unset($this->toolbar_btn['new']);

        $this->toolbar_title = $this->l('Gerar etiquetas de endereçamento');
    }

    // cria gerarpdf action list
    public function displayGerarPDFLink($token = null, $id) {

        if (!array_key_exists('gerarpdf', self::$cache_lang)) {
            self::$cache_lang['gerarpdf'] = $this->l('Gerar PDF');
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.'&'.$this->identifier.'='.$id.'&gerarpdf&token='.($token != null ? $token : $this->token),
            'action' => self::$cache_lang['gerarpdf'],
        ));

        return $this->context->smarty->fetch(_PS_MODULE_DIR_.FK_NOME_MODULO.'/views/templates/admin/list_action_gerarpdf.tpl');
    }

    // Funcao alterada para acerto de bug do Prestashop
    public function processResetFilters($list_id = null) {
        parent::processResetFilters();
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminGerarEtiqEnder'));
    }

    public function jaImpresso($id_order, $tr) {
        return ($tr['fk_etiq_ender'] ? $this->l('Sim') : $this->l('Não'));
    }

}