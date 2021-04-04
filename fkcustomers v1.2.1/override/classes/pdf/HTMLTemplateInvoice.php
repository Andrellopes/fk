<?php

class HTMLTemplateInvoice extends HTMLTemplateInvoiceCore {

    public function getContent() {

        // TODO: alterar quando mudar versão do Prestashop
        if (version_compare(_PS_VERSION_, '1.6.0.9', '<=')) {
            return $this->v1_6_0_5();
        }elseif (version_compare(_PS_VERSION_, '1.6.0.11', '>=') and version_compare(_PS_VERSION_, '1.6.0.14', '<=')) {
            return $this->v1_6_0_11();
        }elseif (version_compare(_PS_VERSION_, '1.6.1.0', '>=') and version_compare(_PS_VERSION_, '1.6.1.1', '<=')) {
            return $this->v1_6_1_0();
        }else {
            return $this->v1_6_1_2();
        }

    }

    private function v1_6_0_5() {

        $country = new Country((int)$this->order->id_address_invoice);
        $invoice_address = new Address((int)$this->order->id_address_invoice);

        $formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
        $formatted_delivery_address = '';

        // Cria string com informações adicionais de CPF/CNPJ e RG/IE
        $dados = Db::getInstance()->getRow('SELECT `tipo`, `cpf_cnpj`, `rg_ie` FROM '._DB_PREFIX_.'customer WHERE `id_customer` = '.(int)$this->order->id_customer);

        $inf_adicionais = '';
        if ($dados['tipo'] == 'pf') {
            $inf_adicionais = '<br>CPF: '.$dados['cpf_cnpj'].' RG: '.$dados['rg_ie'];
        }else {
            if ($dados['tipo'] == 'pj') {
                $inf_adicionais = '<br>CNPJ: '.$dados['cpf_cnpj'].' IE: '.$dados['rg_ie'];
            }else {
                $inf_adicionais = '<br>CPF/CNPJ: Não informado  RG/IE: Não informado';
            }
        }

        // Complementa a string com as informações adicionais
        $formatted_invoice_address .= $inf_adicionais;

        if ($this->order->id_address_delivery != $this->order->id_address_invoice) {
            $delivery_address = new Address((int)$this->order->id_address_delivery);
            $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
        }

        $customer = new Customer((int)$this->order->id_customer);

        if (version_compare(_PS_VERSION_, '1.6.0.13', '>=')) {

            $order_details = $this->order_invoice->getProducts();
            if (Configuration::get('PS_PDF_IMG_INVOICE')) {

                foreach ($order_details as &$order_detail) {

                    if ($order_detail['image'] != null) {
                        $name = 'product_mini_'.(int)$order_detail['product_id'].(isset($order_detail['product_attribute_id']) ? '_'.(int)$order_detail['product_attribute_id'] : '').'.jpg';
                        $order_detail['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_.'p/'.$order_detail['image']->getExistingImgPath().'.jpg', $name, 45, 'jpg', false);

                        if (file_exists(_PS_TMP_IMG_DIR_.$name))
                            $order_detail['image_size'] = getimagesize(_PS_TMP_IMG_DIR_.$name);
                        else
                            $order_detail['image_size'] = false;
                    }
                }
            }

            $data = array(
                'order' => $this->order,
                'order_details' => $order_details,
                'cart_rules' => $this->order->getCartRules($this->order_invoice->id),
                'delivery_address' => $formatted_delivery_address,
                'invoice_address' => $formatted_invoice_address,
                'tax_excluded_display' => Group::getPriceDisplayMethod($customer->id_default_group),
                'tax_tab' => $this->getTaxTabContent(),
                'customer' => $customer
            );
        }else {
            $data = array(
                'order' => $this->order,
                'order_details' => $this->order_invoice->getProducts(),
                'cart_rules' => $this->order->getCartRules($this->order_invoice->id),
                'delivery_address' => $formatted_delivery_address,
                'invoice_address' => $formatted_invoice_address,
                'tax_excluded_display' => Group::getPriceDisplayMethod($customer->id_default_group),
                'tax_tab' => $this->getTaxTabContent(),
                'customer' => $customer
            );
        }

        if (Tools::getValue('debug'))
            die(json_encode($data));

        $this->smarty->assign($data);

        return $this->smarty->fetch($this->getTemplateByCountry($country->iso_code));

    }

    private function v1_6_0_11() {

        $invoice_address = new Address((int)$this->order->id_address_invoice);
        $country = new Country((int)$invoice_address->id_country);

        $formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
        $formatted_delivery_address = '';

        // Cria string com informações adicionais de CPF/CNPJ e RG/IE
        $dados = Db::getInstance()->getRow('SELECT `tipo`, `cpf_cnpj`, `rg_ie` FROM '._DB_PREFIX_.'customer WHERE `id_customer` = '.(int)$this->order->id_customer);

        $inf_adicionais = '';
        if ($dados['tipo'] == 'pf') {
            $inf_adicionais = '<br>CPF: '.$dados['cpf_cnpj'].' RG: '.$dados['rg_ie'];
        }else {
            if ($dados['tipo'] == 'pj') {
                $inf_adicionais = '<br>CNPJ: '.$dados['cpf_cnpj'].' IE: '.$dados['rg_ie'];
            }else {
                $inf_adicionais = '<br>CPF/CNPJ: Não informado  RG/IE: Não informado';
            }
        }

        // Complementa a string com as informações adicionais
        $formatted_invoice_address .= $inf_adicionais;

        if ($this->order->id_address_delivery != $this->order->id_address_invoice) {
            $delivery_address = new Address((int)$this->order->id_address_delivery);
            $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
        }

        $customer = new Customer((int)$this->order->id_customer);

        if (version_compare(_PS_VERSION_, '1.6.0.13', '>=')) {

            $order_details = $this->order_invoice->getProducts();
            if (Configuration::get('PS_PDF_IMG_INVOICE')) {

                foreach ($order_details as &$order_detail) {

                    if ($order_detail['image'] != null) {
                        $name = 'product_mini_'.(int)$order_detail['product_id'].(isset($order_detail['product_attribute_id']) ? '_'.(int)$order_detail['product_attribute_id'] : '').'.jpg';
                        $order_detail['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_.'p/'.$order_detail['image']->getExistingImgPath().'.jpg', $name, 45, 'jpg', false);

                        if (file_exists(_PS_TMP_IMG_DIR_.$name))
                            $order_detail['image_size'] = getimagesize(_PS_TMP_IMG_DIR_.$name);
                        else
                            $order_detail['image_size'] = false;
                    }
                }
            }

            $data = array(
                'order' => $this->order,
                'order_details' => $order_details,
                'cart_rules' => $this->order->getCartRules($this->order_invoice->id),
                'delivery_address' => $formatted_delivery_address,
                'invoice_address' => $formatted_invoice_address,
                'tax_excluded_display' => Group::getPriceDisplayMethod($customer->id_default_group),
                'tax_tab' => $this->getTaxTabContent(),
                'customer' => $customer
            );
        }else {
            $data = array(
                'order' => $this->order,
                'order_details' => $this->order_invoice->getProducts(),
                'cart_rules' => $this->order->getCartRules($this->order_invoice->id),
                'delivery_address' => $formatted_delivery_address,
                'invoice_address' => $formatted_invoice_address,
                'tax_excluded_display' => Group::getPriceDisplayMethod($customer->id_default_group),
                'tax_tab' => $this->getTaxTabContent(),
                'customer' => $customer
            );
        }

        if (Tools::getValue('debug'))
            die(json_encode($data));

        $this->smarty->assign($data);

        return $this->smarty->fetch($this->getTemplateByCountry($country->iso_code));

    }

    private function v1_6_1_0() {

        $invoiceAddressPatternRules = Tools::jsonDecode(Configuration::get('PS_INVCE_INVOICE_ADDR_RULES'), true);
        $deliveryAddressPatternRules = Tools::jsonDecode(Configuration::get('PS_INVCE_DELIVERY_ADDR_RULES'), true);

        $invoice_address = new Address((int)$this->order->id_address_invoice);
        $country = new Country((int)$invoice_address->id_country);

        if ($this->order_invoice->invoice_address)
            $formatted_invoice_address = $this->order_invoice->invoice_address;
        else
            $formatted_invoice_address = AddressFormat::generateAddress($invoice_address, $invoiceAddressPatternRules, '<br />', ' ');

        // Cria string com informações adicionais de CPF/CNPJ e RG/IE
        $dados = Db::getInstance()->getRow('SELECT `tipo`, `cpf_cnpj`, `rg_ie` FROM '._DB_PREFIX_.'customer WHERE `id_customer` = '.(int)$this->order->id_customer);

        $inf_adicionais = '';
        if ($dados['tipo'] == 'pf') {
            $inf_adicionais = '<br>CPF: '.$dados['cpf_cnpj'].'<br>RG: '.$dados['rg_ie'];
        }else {
            if ($dados['tipo'] == 'pj') {
                $inf_adicionais = '<br>CNPJ: '.$dados['cpf_cnpj'].'<br>IE: '.$dados['rg_ie'];
            }else {
                $inf_adicionais = '<br>CPF/CNPJ: Não informado  <br>RG/IE: Não informado';
            }
        }

        // Complementa a string com as informações adicionais
        $formatted_invoice_address .= $inf_adicionais;

        $delivery_address = null;
        $formatted_delivery_address = '';
        if (isset($this->order->id_address_delivery) && $this->order->id_address_delivery)
        {
            if ($this->order_invoice->delivery_address)
                $formatted_delivery_address = $this->order_invoice->delivery_address;
            else
            {
                $delivery_address = new Address((int)$this->order->id_address_delivery);
                $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, $deliveryAddressPatternRules, '<br />', ' ');
            }
        }

        // Complementa a string com as informações adicionais
        $formatted_delivery_address .= $inf_adicionais;

        $customer = new Customer((int)$this->order->id_customer);

        $order_details = $this->order_invoice->getProducts();

        $has_discount = false;
        foreach ($order_details as $id => &$order_detail)
        {
            // Find out if column 'price before discount' is required
            if ($order_detail['reduction_amount_tax_excl'] > 0)
            {
                $has_discount = true;
                $order_detail['unit_price_tax_excl_before_specific_price'] = $order_detail['unit_price_tax_excl_including_ecotax'] + $order_detail['reduction_amount_tax_excl'];
            }
            elseif ($order_detail['reduction_percent'] > 0)
            {
                $has_discount = true;
                $order_detail['unit_price_tax_excl_before_specific_price'] = (100 * $order_detail['unit_price_tax_excl_including_ecotax']) / (100 - 15);
            }

            // Set tax_code
            $taxes = OrderDetail::getTaxListStatic($id);
            $tax_temp = array();
            foreach ($taxes as $tax)
            {
                $obj = new Tax($tax['id_tax']);
                $tax_temp[] = sprintf($this->l('%1$s%2$s%%'), ($obj->rate + 0), '&nbsp;');
            }

            $order_detail['order_detail_tax'] = $taxes;
            $order_detail['order_detail_tax_label'] = implode(', ', $tax_temp);
        }
        unset($tax_temp);
        unset($order_detail);

        if (Configuration::get('PS_PDF_IMG_INVOICE'))
        {
            foreach ($order_details as &$order_detail)
            {
                if ($order_detail['image'] != null)
                {
                    $name = 'product_mini_'.(int)$order_detail['product_id'].(isset($order_detail['product_attribute_id']) ? '_'.(int)$order_detail['product_attribute_id'] : '').'.jpg';
                    $path = _PS_PROD_IMG_DIR_.$order_detail['image']->getExistingImgPath().'.jpg';

                    $order_detail['image_tag'] = preg_replace(
                        '/\.*'.preg_quote(__PS_BASE_URI__, '/').'/',
                        _PS_ROOT_DIR_.DIRECTORY_SEPARATOR,
                        ImageManager::thumbnail($path, $name, 45, 'jpg', false),
                        1
                    );

                    if (file_exists(_PS_TMP_IMG_DIR_.$name))
                        $order_detail['image_size'] = getimagesize(_PS_TMP_IMG_DIR_.$name);
                    else
                        $order_detail['image_size'] = false;
                }
            }
            unset($order_detail); // don't overwrite the last order_detail later
        }

        $cart_rules = $this->order->getCartRules($this->order_invoice->id);
        $free_shipping = false;
        foreach ($cart_rules as $key => $cart_rule)
        {
            if ($cart_rule['free_shipping'])
            {
                $free_shipping = true;
                /**
                 * Adjust cart rule value to remove the amount of the shipping.
                 * We're not interested in displaying the shipping discount as it is already shown as "Free Shipping".
                 */
                $cart_rules[$key]['value_tax_excl'] -= $this->order_invoice->total_shipping_tax_excl;
                $cart_rules[$key]['value'] -= $this->order_invoice->total_shipping_tax_incl;

                /**
                 * Don't display cart rules that are only about free shipping and don't create
                 * a discount on products.
                 */
                if ($cart_rules[$key]['value'] == 0)
                    unset($cart_rules[$key]);
            }
        }

        $product_taxes = 0;
        foreach ($this->order_invoice->getProductTaxesBreakdown($this->order) as $details)
            $product_taxes += $details['total_amount'];

        $product_discounts_tax_excl = $this->order_invoice->total_discount_tax_excl;
        $product_discounts_tax_incl = $this->order_invoice->total_discount_tax_incl;
        if ($free_shipping)
        {
            $product_discounts_tax_excl -= $this->order_invoice->total_shipping_tax_excl;
            $product_discounts_tax_incl -= $this->order_invoice->total_shipping_tax_incl;
        }

        $products_after_discounts_tax_excl = $this->order_invoice->total_products - $product_discounts_tax_excl;
        $products_after_discounts_tax_incl = $this->order_invoice->total_products_wt - $product_discounts_tax_incl;

        $shipping_tax_excl = $free_shipping ? 0 : $this->order_invoice->total_shipping_tax_excl;
        $shipping_tax_incl = $free_shipping ? 0 : $this->order_invoice->total_shipping_tax_incl;
        $shipping_taxes = $shipping_tax_incl - $shipping_tax_excl;

        $wrapping_taxes = $this->order_invoice->total_wrapping_tax_incl - $this->order_invoice->total_wrapping_tax_excl;

        $total_taxes = $this->order_invoice->total_paid_tax_incl - $this->order_invoice->total_paid_tax_excl;

        $footer = array(
            'products_before_discounts_tax_excl' => $this->order_invoice->total_products,
            'product_discounts_tax_excl' => $product_discounts_tax_excl,
            'products_after_discounts_tax_excl' => $products_after_discounts_tax_excl,
            'products_before_discounts_tax_incl' => $this->order_invoice->total_products_wt,
            'product_discounts_tax_incl' => $product_discounts_tax_incl,
            'products_after_discounts_tax_incl' => $products_after_discounts_tax_incl,
            'product_taxes' => $product_taxes,
            'shipping_tax_excl' => $shipping_tax_excl,
            'shipping_taxes' => $shipping_taxes,
            'shipping_tax_incl' => $shipping_tax_incl,
            'wrapping_tax_excl' => $this->order_invoice->total_wrapping_tax_excl,
            'wrapping_taxes' => $wrapping_taxes,
            'wrapping_tax_incl' => $this->order_invoice->total_wrapping_tax_incl,
            'ecotax_taxes' => $total_taxes - $product_taxes - $wrapping_taxes - $shipping_taxes,
            'total_taxes' => $total_taxes,
            'total_paid_tax_excl' => $this->order_invoice->total_paid_tax_excl,
            'total_paid_tax_incl' => $this->order_invoice->total_paid_tax_incl
        );

        foreach ($footer as $key => $value)
            $footer[$key] = Tools::ps_round($value, _PS_PRICE_COMPUTE_PRECISION_, $this->order->round_mode);

        /**
         * Need the $round_mode for the tests.
         */
        $round_type = null;
        switch ($this->order->round_type)
        {
            case Order::ROUND_TOTAL:
                $round_type = 'total';
                break;
            case Order::ROUND_LINE;
                $round_type = 'line';
                break;
            case Order::ROUND_ITEM:
                $round_type = 'item';
                break;
            default:
                $round_type = 'line';
                break;
        }

        $display_product_images = Configuration::get('PS_PDF_IMG_INVOICE');
        $tax_excluded_display = Group::getPriceDisplayMethod($customer->id_default_group);

        $layout = $this->fk_computeLayout(array('has_discount' => $has_discount));

        $legal_free_text = Hook::exec('displayInvoiceLegalFreeText', array('order' => $this->order));
        if (!$legal_free_text)
            $legal_free_text = Configuration::get('PS_INVOICE_LEGAL_FREE_TEXT', (int)Context::getContext()->language->id, null, (int)$this->order->id_shop);

        $data = array(
            'order' => $this->order,
            'order_invoice' => $this->order_invoice,
            'order_details' => $order_details,
            'cart_rules' => $cart_rules,
            'delivery_address' => $formatted_delivery_address,
            'invoice_address' => $formatted_invoice_address,
            'addresses' => array('invoice' => $invoice_address, 'delivery' => $delivery_address),
            'tax_excluded_display' => $tax_excluded_display,
            'display_product_images' => $display_product_images,
            'layout' => $layout,
            'tax_tab' => $this->getTaxTabContent(),
            'customer' => $customer,
            'footer' => $footer,
            'ps_price_compute_precision' => _PS_PRICE_COMPUTE_PRECISION_,
            'round_type' => $round_type,
            'legal_free_text' => $legal_free_text,
        );

        if (Tools::getValue('debug'))
            die(json_encode($data));

        $this->smarty->assign($data);

        $tpls = array(
            'style_tab' => $this->smarty->fetch($this->getTemplate('invoice.style-tab')),
            'addresses_tab' => $this->smarty->fetch($this->getTemplate('invoice.addresses-tab')),
            'summary_tab' => $this->smarty->fetch($this->getTemplate('invoice.summary-tab')),
            'product_tab' => $this->smarty->fetch($this->getTemplate('invoice.product-tab')),
            'tax_tab' => $this->getTaxTabContent(),
            'payment_tab' => $this->smarty->fetch($this->getTemplate('invoice.payment-tab')),
            'total_tab' => $this->smarty->fetch($this->getTemplate('invoice.total-tab')),
        );
        $this->smarty->assign($tpls);


        return $this->smarty->fetch($this->getTemplateByCountry($country->iso_code));

    }

    private function v1_6_1_2() {

        $invoiceAddressPatternRules = Tools::jsonDecode(Configuration::get('PS_INVCE_INVOICE_ADDR_RULES'), true);
        $deliveryAddressPatternRules = Tools::jsonDecode(Configuration::get('PS_INVCE_DELIVERY_ADDR_RULES'), true);

        $invoice_address = new Address((int)$this->order->id_address_invoice);
        $country = new Country((int)$invoice_address->id_country);

        if ($this->order_invoice->invoice_address) {
            $formatted_invoice_address = $this->order_invoice->invoice_address;
        } else {
            $formatted_invoice_address = AddressFormat::generateAddress($invoice_address, $invoiceAddressPatternRules, '<br />', ' ');
        }

        //*** FKcustomers - Cria string com informações adicionais de CPF/CNPJ e RG/IE
        $dados = Db::getInstance()->getRow('SELECT `tipo`, `cpf_cnpj`, `rg_ie` FROM '._DB_PREFIX_.'customer WHERE `id_customer` = '.(int)$this->order->id_customer);

        $inf_adicionais = '';
        if ($dados['tipo'] == 'pf') {
            $inf_adicionais = '<br>CPF: '.$dados['cpf_cnpj'].'<br>RG: '.$dados['rg_ie'];
        }else {
            if ($dados['tipo'] == 'pj') {
                $inf_adicionais = '<br>CNPJ: '.$dados['cpf_cnpj'].'<br>IE: '.$dados['rg_ie'];
            }else {
                $inf_adicionais = '<br>CPF/CNPJ: Não informado  <br>RG/IE: Não informado';
            }
        }

        //*** FKcustomers - Complementa a string com as informações adicionais
        $formatted_invoice_address .= $inf_adicionais;

        $delivery_address = null;
        $formatted_delivery_address = '';
        if (isset($this->order->id_address_delivery) && $this->order->id_address_delivery) {
            if ($this->order_invoice->delivery_address) {
                $formatted_delivery_address = $this->order_invoice->delivery_address;
            } else {
                $delivery_address = new Address((int)$this->order->id_address_delivery);
                $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, $deliveryAddressPatternRules, '<br />', ' ');
            }
        }

        //*** FKcustomers - Complementa a string com as informações adicionais
        $formatted_delivery_address .= $inf_adicionais;

        $customer = new Customer((int)$this->order->id_customer);

        $order_details = $this->order_invoice->getProducts();

        $has_discount = false;
        foreach ($order_details as $id => &$order_detail) {
            // Find out if column 'price before discount' is required
            if ($order_detail['reduction_amount_tax_excl'] > 0) {
                $has_discount = true;
                $order_detail['unit_price_tax_excl_before_specific_price'] = $order_detail['unit_price_tax_excl_including_ecotax'] + $order_detail['reduction_amount_tax_excl'];
            } elseif ($order_detail['reduction_percent'] > 0) {
                $has_discount = true;
                $order_detail['unit_price_tax_excl_before_specific_price'] = (100 * $order_detail['unit_price_tax_excl_including_ecotax']) / (100 - $order_detail['reduction_percent']);
            }

            // Set tax_code
            $taxes = OrderDetail::getTaxListStatic($id);
            $tax_temp = array();
            foreach ($taxes as $tax) {
                $obj = new Tax($tax['id_tax']);
                $tax_temp[] = sprintf($this->l('%1$s%2$s%%'), ($obj->rate + 0), '&nbsp;');
            }

            $order_detail['order_detail_tax'] = $taxes;
            $order_detail['order_detail_tax_label'] = implode(', ', $tax_temp);
        }
        unset($tax_temp);
        unset($order_detail);

        if (Configuration::get('PS_PDF_IMG_INVOICE')) {
            foreach ($order_details as &$order_detail) {
                if ($order_detail['image'] != null) {
                    $name = 'product_mini_'.(int)$order_detail['product_id'].(isset($order_detail['product_attribute_id']) ? '_'.(int)$order_detail['product_attribute_id'] : '').'.jpg';
                    $path = _PS_PROD_IMG_DIR_.$order_detail['image']->getExistingImgPath().'.jpg';

                    $order_detail['image_tag'] = preg_replace(
                        '/\.*'.preg_quote(__PS_BASE_URI__, '/').'/',
                        _PS_ROOT_DIR_.DIRECTORY_SEPARATOR,
                        ImageManager::thumbnail($path, $name, 45, 'jpg', false),
                        1
                    );

                    if (file_exists(_PS_TMP_IMG_DIR_.$name)) {
                        $order_detail['image_size'] = getimagesize(_PS_TMP_IMG_DIR_.$name);
                    } else {
                        $order_detail['image_size'] = false;
                    }
                }
            }
            unset($order_detail); // don't overwrite the last order_detail later
        }

        $cart_rules = $this->order->getCartRules($this->order_invoice->id);
        $free_shipping = false;
        foreach ($cart_rules as $key => $cart_rule) {
            if ($cart_rule['free_shipping']) {
                $free_shipping = true;
                /**
                 * Adjust cart rule value to remove the amount of the shipping.
                 * We're not interested in displaying the shipping discount as it is already shown as "Free Shipping".
                 */
                $cart_rules[$key]['value_tax_excl'] -= $this->order_invoice->total_shipping_tax_excl;
                $cart_rules[$key]['value'] -= $this->order_invoice->total_shipping_tax_incl;

                /**
                 * Don't display cart rules that are only about free shipping and don't create
                 * a discount on products.
                 */
                if ($cart_rules[$key]['value'] == 0) {
                    unset($cart_rules[$key]);
                }
            }
        }

        $product_taxes = 0;
        foreach ($this->order_invoice->getProductTaxesBreakdown($this->order) as $details) {
            $product_taxes += $details['total_amount'];
        }

        $product_discounts_tax_excl = $this->order_invoice->total_discount_tax_excl;
        $product_discounts_tax_incl = $this->order_invoice->total_discount_tax_incl;
        if ($free_shipping) {
            $product_discounts_tax_excl -= $this->order_invoice->total_shipping_tax_excl;
            $product_discounts_tax_incl -= $this->order_invoice->total_shipping_tax_incl;
        }

        $products_after_discounts_tax_excl = $this->order_invoice->total_products - $product_discounts_tax_excl;
        $products_after_discounts_tax_incl = $this->order_invoice->total_products_wt - $product_discounts_tax_incl;

        $shipping_tax_excl = $free_shipping ? 0 : $this->order_invoice->total_shipping_tax_excl;
        $shipping_tax_incl = $free_shipping ? 0 : $this->order_invoice->total_shipping_tax_incl;
        $shipping_taxes = $shipping_tax_incl - $shipping_tax_excl;

        $wrapping_taxes = $this->order_invoice->total_wrapping_tax_incl - $this->order_invoice->total_wrapping_tax_excl;

        $total_taxes = $this->order_invoice->total_paid_tax_incl - $this->order_invoice->total_paid_tax_excl;

        $footer = array(
            'products_before_discounts_tax_excl' => $this->order_invoice->total_products,
            'product_discounts_tax_excl' => $product_discounts_tax_excl,
            'products_after_discounts_tax_excl' => $products_after_discounts_tax_excl,
            'products_before_discounts_tax_incl' => $this->order_invoice->total_products_wt,
            'product_discounts_tax_incl' => $product_discounts_tax_incl,
            'products_after_discounts_tax_incl' => $products_after_discounts_tax_incl,
            'product_taxes' => $product_taxes,
            'shipping_tax_excl' => $shipping_tax_excl,
            'shipping_taxes' => $shipping_taxes,
            'shipping_tax_incl' => $shipping_tax_incl,
            'wrapping_tax_excl' => $this->order_invoice->total_wrapping_tax_excl,
            'wrapping_taxes' => $wrapping_taxes,
            'wrapping_tax_incl' => $this->order_invoice->total_wrapping_tax_incl,
            'ecotax_taxes' => $total_taxes - $product_taxes - $wrapping_taxes - $shipping_taxes,
            'total_taxes' => $total_taxes,
            'total_paid_tax_excl' => $this->order_invoice->total_paid_tax_excl,
            'total_paid_tax_incl' => $this->order_invoice->total_paid_tax_incl
        );

        foreach ($footer as $key => $value) {
            $footer[$key] = Tools::ps_round($value, _PS_PRICE_COMPUTE_PRECISION_, $this->order->round_mode);
        }

        /**
         * Need the $round_mode for the tests.
         */
        $round_type = null;
        switch ($this->order->round_type) {
            case Order::ROUND_TOTAL:
                $round_type = 'total';
                break;
            case Order::ROUND_LINE:
                $round_type = 'line';
                break;
            case Order::ROUND_ITEM:
                $round_type = 'item';
                break;
            default:
                $round_type = 'line';
                break;
        }

        $display_product_images = Configuration::get('PS_PDF_IMG_INVOICE');
        $tax_excluded_display = Group::getPriceDisplayMethod($customer->id_default_group);

        $layout = $this->fk_computeLayout(array('has_discount' => $has_discount));

        $legal_free_text = Hook::exec('displayInvoiceLegalFreeText', array('order' => $this->order));
        if (!$legal_free_text) {
            $legal_free_text = Configuration::get('PS_INVOICE_LEGAL_FREE_TEXT', (int)Context::getContext()->language->id, null, (int)$this->order->id_shop);
        }

        $data = array(
            'order' => $this->order,
            'order_invoice' => $this->order_invoice,
            'order_details' => $order_details,
            'cart_rules' => $cart_rules,
            'delivery_address' => $formatted_delivery_address,
            'invoice_address' => $formatted_invoice_address,
            'addresses' => array('invoice' => $invoice_address, 'delivery' => $delivery_address),
            'tax_excluded_display' => $tax_excluded_display,
            'display_product_images' => $display_product_images,
            'layout' => $layout,
            'tax_tab' => $this->getTaxTabContent(),
            'customer' => $customer,
            'footer' => $footer,
            'ps_price_compute_precision' => _PS_PRICE_COMPUTE_PRECISION_,
            'round_type' => $round_type,
            'legal_free_text' => $legal_free_text,
        );

        if (Tools::getValue('debug')) {
            die(json_encode($data));
        }

        $this->smarty->assign($data);

        $tpls = array(
            'style_tab' => $this->smarty->fetch($this->getTemplate('invoice.style-tab')),
            'addresses_tab' => $this->smarty->fetch($this->getTemplate('invoice.addresses-tab')),
            'summary_tab' => $this->smarty->fetch($this->getTemplate('invoice.summary-tab')),
            'product_tab' => $this->smarty->fetch($this->getTemplate('invoice.product-tab')),
            'tax_tab' => $this->getTaxTabContent(),
            'payment_tab' => $this->smarty->fetch($this->getTemplate('invoice.payment-tab')),
            'total_tab' => $this->smarty->fetch($this->getTemplate('invoice.total-tab')),
        );
        $this->smarty->assign($tpls);

        return $this->smarty->fetch($this->getTemplateByCountry($country->iso_code));
    }

    // Alterada o nome da função devido na versão 1.6.1.2 ter sido alterada para protect e anteriormente ser private
    private function fk_computeLayout($params) {

        $layout = array(
            'reference' => array(
                'width' => 15,
            ),
            'product' => array(
                'width' => 40,
            ),
            'quantity' => array(
                'width' => 8,
            ),
            'tax_code' => array(
                'width' => 8,
            ),
            'unit_price_tax_excl' => array(
                'width' => 0,
            ),
            'total_tax_excl' => array(
                'width' => 0,
            )
        );

        if (isset($params['has_discount']) && $params['has_discount'])
        {
            $layout['before_discount'] = array('width' => 0);
            $layout['product']['width'] -= 7;
            $layout['reference']['width'] -= 3;
        }

        $total_width = 0;
        $free_columns_count = 0;
        foreach ($layout as $data)
        {
            if ($data['width'] === 0)
                ++$free_columns_count;

            $total_width += $data['width'];
        }

        $delta = 100 - $total_width;

        foreach ($layout as $row => $data)
            if ($data['width'] === 0)
                $layout[$row]['width'] = $delta / $free_columns_count;

        $layout['_colCount'] = count($layout);

        return $layout;
    }

}


