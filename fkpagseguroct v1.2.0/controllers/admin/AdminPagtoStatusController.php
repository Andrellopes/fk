<?php

include_once _PS_MODULE_DIR_.'fkpagseguroct/models/FKpagseguroctClass.php';
  
class AdminPagtoStatusController extends ModuleAdminController {
    
    public function __construct() {
        
        $this->bootstrap = true;

        $context = Context::getContext();

        $this->table = 'fkpagseguroct';
        $this->className = 'FKpagseguroctClass';
        $this->identifier = 'id_pagseguro';
        $this->lang = false;
        $this->list_no_link = true;
        $this->addRowAction('view');
        $this->addRowAction('delete');

        $this->_select = 'b.firstname, b.lastname, c.id_order, c.reference, c.total_paid_tax_incl';
        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'customer b ON (b.id_customer = a.cod_cliente)
                        LEFT JOIN '._DB_PREFIX_.'orders c ON (c.id_cart = a.id_cart)';
        $this->_filter = 'AND a.id_shop = '.(int)$context->shop->id;
        $this->_orderBy = 'data_pedido';
        $this->_orderWay = 'DESC';


        $this->fields_list = array(
            'id_order' => array(
                'title' => $this->l('Pedido'),
                'align' => 'left',
                'width' => 70
            ),
            'reference' => array(
                'title' => $this->l('Referência Pedido'),
                'align' => 'left',
                'width' => 90
            ),
            'cod_cliente' => array(
                'title' => $this->l('Código Cliente'),
                'align' => 'left',
                'width' => 70
            ),
            'firstname' => array(
                'title' => $this->l('Nome'),
                'align' => 'left'
            ),
            'lastname' => array(
                'title' => $this->l('Sobrenome'),
                'align' => 'left'
            ),
            'data_pedido' => array(
                'title' => $this->l('Data Pedido'),
                'width' => 140,
                'align' => 'left',
                'type' => 'datetime'
            ),
            'total_paid_tax_incl' => array(
                'title' => $this->l('Total Pedido'),
                'width' => 80,
                'align' => 'left',
                'type' => 'price'
            ),
            'data_status' => array(
                'title' => $this->l('Data Status'),
                'width' => 140,
                'align' => 'left',
                'type' => 'datetime'
            ),
            'desc_status' => array(
                'title' => $this->l('Status'),
                'align' => 'left',
                'width' => 150,
                'prefix' => '<b>',
                'suffix' => '</b>'
            ),
            'desc_pagto' => array(
                'title' => $this->l('Forma de Pagamento'),
                'align' => 'left',
                'width' => 150,
                'prefix' => '<b>',
                'suffix' => '</b>'
            )

        );

        parent::__construct();
    }
    
    public function initContent() {
        
        if ($this->display == 'view') {
            
            $id_pagseguro = Tools::getValue('id_pagseguro');
        
            // Instancia pagSeguroClass
            $psClass = new FKpagseguroctClass();
            $controle = $psClass->recuperaControle($id_pagseguro);
            
            $id_customer = $controle['cod_cliente'];
            $id_order = $controle['id_order'];
            
            // Recupera cadastro do cliente
            $cadastro = $psClass->recuperaCadastroCliente($id_customer);
            
            // Recupera endereco de entrega do pedido
            $orders = new Order($id_order);
            $endereco = $psClass->recuperaEnderecoEntrega($orders->id_address_delivery);
            
            // Recupera pedido
            $pedido = $psClass->recuperaPedido($id_order);
            
            $this->context->smarty->assign(array(
                'cadastro'      => $cadastro,
                'endereco'      => $endereco,
                'pedido'        => $pedido
            ));
            
            // Renderiza
            $this->content .= $this->context->smarty->fetch(_PS_MODULE_DIR_.'fkpagseguroct/views/templates/admin/detalhes.tpl');
        }    
       
        parent::initContent();         
    }
    
    public function initPageHeaderToolbar() {
            
        if ($this->display == 'view') {
            $this->page_header_toolbar_btn['retornar'] = array(
                'href' => $this->context->link->getAdminLink('AdminPagtoStatus'),
                'desc' => $this->l('Retornar', null, null, false),
                'icon' => 'process-icon-back'
            );    
        }
    
        parent::initPageHeaderToolbar();
    }         
    
    public function initToolbar() {
        
        parent::initToolbar();
        
        // Desativa botoes
        unset($this->toolbar_btn['new']);
        
        // Altera titulo
        $this->toolbar_title = $this->l('Detalhes do Pedido');    
    
    }

    public function processDelete() {
        $pagSeguroClass = new FKpagseguroctClass();
        return $pagSeguroClass->excluiRegistro(Tools::getValue('id_pagseguro'));
    }
    
    // Funcao alterada para acerto de bug do Prestashop
    public function processResetFilters($list_id = null) {
        
        parent::processResetFilters();
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminPagtoStatus'));

    }

    public function setMedia()
    {
        parent::setMedia();

        // CSS
        $this->addCSS(_PS_MODULE_DIR_.'fkpagseguroct/css/fkpagseguroct_admin.css');
    }

}

?>
