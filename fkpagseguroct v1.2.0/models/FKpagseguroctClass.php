<?php

class FKpagseguroctClass extends ObjectModel {

    public static $definition = array(
        'table' => 'fkpagseguroct',
        'primary' => 'id_pagseguro',
        'multilang' => false,
        'fields' => array(
            'id_shop'       =>	array('type' => self::TYPE_INT),
            'cod_cliente'   =>	array('type' => self::TYPE_INT),
            'id_cart'       =>	array('type' => self::TYPE_INT),
            'cod_transacao' => 	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 50),
            'status'        =>	array('type' => self::TYPE_INT),
            'desc_status'   => 	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 40),
            'pagto'         =>	array('type' => self::TYPE_INT),
            'desc_pagto'    => 	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 40),
            'data_status'   =>	array('type' => self::TYPE_DATE),
            'data_pedido'   =>	array('type' => self::TYPE_DATE),
        ),
    );
    
    public function recuperaControle($id_pagseguro) {
        
        $sql = 'SELECT '._DB_PREFIX_.'fkpagseguroct.*, 
                       '._DB_PREFIX_.'orders.id_order, 
                       '._DB_PREFIX_.'orders.reference 
                FROM '._DB_PREFIX_.'fkpagseguroct 
                    INNER JOIN '._DB_PREFIX_.'orders
                        ON '._DB_PREFIX_.'fkpagseguroct.id_cart = '._DB_PREFIX_.'orders.id_cart
                WHERE '._DB_PREFIX_.'fkpagseguroct.id_pagseguro = '.(int)$id_pagseguro; 
                
        return Db::getInstance()->getRow($sql);        
    }
    
    public function recuperaCadastroCliente($id_customer) {

        // Verifica se existe o campo cpf_cnpj
        $cpf_cnpj = false;

        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '"._DB_PREFIX_."customer' AND column_name = 'cpf_cnpj' AND table_schema = '"._DB_NAME_."'";
        $dados = Db::getInstance()->getRow($sql);

        if ($dados) {
            $cpf_cnpj = true;
        }

        // Verifica se existe o campo reg_ie
        $rg_ie = false;

        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '"._DB_PREFIX_."customer' AND column_name = 'rg_ie' AND table_schema = '"._DB_NAME_."'";
        $dados = Db::getInstance()->getRow($sql);

        if ($dados) {
            $rg_ie = true;
        }

        // Constroi select com cpf_cnpj e rg_ie
        $select = '';

        if (!$cpf_cnpj) {
            $select = ", '' As cpf_cnpj";
        }

        if (!$rg_ie) {
            $select .= ", '' As rg_ie";
        }

        $sql =  'SELECT '.
                    _DB_PREFIX_.'customer.*, '.
                    _DB_PREFIX_.'group_lang.name'.
                    $select.' '.
                'FROM '._DB_PREFIX_.'customer
                        INNER JOIN '._DB_PREFIX_.'group_lang
                            ON '._DB_PREFIX_.'customer.id_default_group = '._DB_PREFIX_.'group_lang.id_group AND '._DB_PREFIX_.'customer.id_lang = '._DB_PREFIX_.'group_lang.id_lang '.
                'WHERE '._DB_PREFIX_.'customer.id_customer = '.(int)$id_customer;

        $dados = Db::getInstance()->getRow($sql);

        return $dados;
    }

    public function recuperaEnderecoEntrega($id_address_delivery) {

        // Verifica se existe o campo numend
        $numend = false;

        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '"._DB_PREFIX_."address' AND column_name = 'numend' AND table_schema = '"._DB_NAME_."'";
        $dados = Db::getInstance()->getRow($sql);

        if ($dados) {
            $numend = true;
        }

        // Verifica se existe o campo compl
        $compl = false;

        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '"._DB_PREFIX_."address' AND column_name = 'compl' AND table_schema = '"._DB_NAME_."'";
        $dados = Db::getInstance()->getRow($sql);

        if ($dados) {
            $compl = true;
        }

        // Constroi select com numend e compl
        $select = '';

        if (!$numend) {
            $select = ", '' As numend";
        }

        if (!$compl) {
            $select .= ", '' As compl";
        }

        $sql =  'SELECT '.
                    _DB_PREFIX_.'address.*, '.
                    _DB_PREFIX_.'country_lang.name AS pais, '.
                    _DB_PREFIX_.'state.name AS estado'.
                    $select.' '.
                'FROM '._DB_PREFIX_.'address
                        INNER JOIN '._DB_PREFIX_.'customer
                            ON '._DB_PREFIX_.'address.id_customer = '._DB_PREFIX_.'customer.id_customer
                        INNER JOIN '._DB_PREFIX_.'country_lang
                            ON '._DB_PREFIX_.'customer.id_lang = '._DB_PREFIX_.'country_lang.id_lang AND '._DB_PREFIX_.'address.id_country = '._DB_PREFIX_.'country_lang.id_country
                        INNER JOIN '._DB_PREFIX_.'state
                            ON '._DB_PREFIX_.'address.id_state = '._DB_PREFIX_.'state.id_state '.
                'WHERE '._DB_PREFIX_.'address.id_address = '.(int)$id_address_delivery;

        $dados = Db::getInstance()->getRow($sql);

        return $dados;
    }
    
    public function recuperaPedido($id_order) {
        
        // Recupera o pedido do cliente
        $sql = 'SELECT
                    '._DB_PREFIX_.'orders.*,
                    '._DB_PREFIX_.'carrier.name AS carrier_name,
                    '._DB_PREFIX_.'order_state_lang.name AS state_name
                FROM '._DB_PREFIX_.'orders
                    LEFT OUTER JOIN '._DB_PREFIX_.'carrier
                        ON '._DB_PREFIX_.'orders.id_carrier = '._DB_PREFIX_.'carrier.id_carrier
                    INNER JOIN '._DB_PREFIX_.'order_state_lang
                        ON '._DB_PREFIX_.'orders.current_state = '._DB_PREFIX_.'order_state_lang.id_order_state
                        AND '._DB_PREFIX_.'orders.id_lang = '._DB_PREFIX_.'order_state_lang.id_lang
                WHERE '._DB_PREFIX_.'orders.id_order = '.(int)$id_order;
                
        $pedido = Db::getInstance()->getrow($sql);
            
        // Recupera produtos do pedido
        $sql = 'SELECT
              '._DB_PREFIX_.'order_detail.product_id,
              '._DB_PREFIX_.'order_detail.product_name,
              '._DB_PREFIX_.'order_detail.product_quantity,
              '._DB_PREFIX_.'order_detail.unit_price_tax_incl,
              '._DB_PREFIX_.'order_detail.total_price_tax_incl
            FROM '._DB_PREFIX_.'orders
                INNER JOIN '._DB_PREFIX_.'order_detail
                    ON '._DB_PREFIX_.'orders.id_order = '._DB_PREFIX_.'order_detail.id_order
            WHERE '._DB_PREFIX_.'orders.id_order = '.(int)$id_order;

        $produtos = Db::getInstance()->executeS($sql);
        
        // Array com o pedido e produtos
        $pedido_cliente = array(
            'id_order'          => $pedido['id_order'],
            'reference'         => $pedido['reference'],
            'date_add'          => $pedido['date_add'],
            'invoice_date'      => $pedido['invoice_date'],
            'payment'           => $pedido['payment'],
            'carrier_name'      => $pedido['carrier_name'],
            'state_name'        => $pedido['state_name'],
            'total_products_wt' => $pedido['total_products_wt'],
            'total_shipping'    => $pedido['total_shipping'],
            'total_discounts'   => $pedido['total_discounts'],
            'total_wrapping'    => $pedido['total_wrapping'],
            'total_paid'        => $pedido['total_paid'],
            'produtos'          => $produtos,
        );
        
        return $pedido_cliente;
    }

    public function excluiRegistro($id) {
        return Db::getInstance()->delete('fkpagseguroct', 'id_pagseguro = '.(int)$id);
    }

}
